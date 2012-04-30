<?php

require dirname(__FILE__) . '/RedisFunctions.php';

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

// Main loop
while (true) {
	usleep(50000);

	if (count($data = $redis->getIncoming())) {
		order($data);
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
