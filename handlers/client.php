<?php

require dirname(__FILE__) . '/RedisFunctions.php';
require dirname(__FILE__) . '/../lib/php-gearman-admin/GearmanAdmin.php';

// Parse config
msg('Reading config. ' . getcwd() . '/vigu.ini');
$config = parse_ini_file(getcwd() . '/vigu.ini', true);

// Connect to Redis
try {
	if (isset($config['ttl']) && isset($config['redis']) && isset($config['redis']['host']) && isset($config['redis']['port']) && isset($config['redis']['timeout'])) {
		/* @var RedisFunctions */
		$redis = new RedisFunctions($config['ttl'], $config['redis']['host'], $config['redis']['port'], $config['redis']['timeout']);
	} else {
		msg('Configuration error.');
		unset($redis);
		die;
	}
} catch (RedisException $e) {
	msg('Redis connection error: ' . $e->getMessage());
	die;
}

// Connect to gearman server
$client = new GearmanClient();
$client->addServer();

$admin = new GearmanAdmin();

$iterations = 0;
// Main loop
while (true) {
	// Dump status approx. every 5 minutes.
	if ($iterations % 600 == 0) {
		msg('Gearman server status:');
		msg($admin->refreshStatus());
		msg('Gearman server worker information:');
		msg($admin->refreshWorkers());
		msg('Cleaning up...');
		$redis->cleanIndexes();
	}
	$iterations++;

	usleep(50000);

	if ($redis->getIncomingSize() > 0) {
		$status = $admin->refreshStatus();
		if ($status->getTotal('incoming') < $status->getAvailable('incoming') * 3) {
			if (count($data = $redis->getIncoming())) {
				order($data);
			}
		}
	}

	if (!$client->runTasks()) {
		msg('ERROR: ' . $client->error());
		die;
	}
}

function msg($message) {
	print "$message\n";
}

function order($data) {
	global $client;

	msg('Ordering peon to work. (Chop down ' . count($data) .' trees)');

	$client->addTaskBackground('incoming', json_encode($data));
}
