<?php

// Parse config
msg('Reading config. ' . getcwd() . '/vigu.ini');
$config = parse_ini_file(getcwd() . '/vigu.ini', true);

// Connect to incoming Redis
try {
	$redis = new Redis();
	if (isset($config['redis']) && isset($config['redis']['host']) && isset($config['redis']['port']) && isset($config['redis']['timeout'])) {
		msg("Connecting to Redis: {$config['redis']['host']}:{$config['redis']['port']} with timeout {$config['redis']['timeout']}.");
		if (!$redis->connect($config['redis']['host'], $config['redis']['port'], $config['redis']['timeout'])) {
			unset($redis);
		}
	} else {
		msg('Configuration error.');
		unset($redis);
		die;
	}
	$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
} catch (RedisException $e) {
	msg('Redis connection error: ' . $e->getMessage());
	die;
}

// Connect to gearman server
$client = new GearmanClient();
$client->addServer();

// Main loop
while (true) {
	usleep(100000);

	if (count($data = getIncoming())) {
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

function getIncoming() {
	global $redis;

	$redis->select(3);

	$inc = array();
	$count = 0;
	while (($pair = $redis->lPop('incoming')) && $count++ < 1000) {
		$inc[] = $pair;
	}

	return $inc;
}