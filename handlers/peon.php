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

$worker = new GearmanWorker();
$worker->addServer();
$worker->addFunction('incoming', 'incoming');

msg('Ready to work!');

do  {
	usleep(10000);
} while ($worker->work() && (($returnCode = $worker->returnCode()) == GEARMAN_SUCCESS));

msg("Bad return code[$returnCode]. Exiting.");

function msg($message) {
	print "$message\n";
}

/**
 * Handle incoming job.
 *
 * @param GearmanJob $job The job to handle.
 *
 * @return null
 */
function incoming(GearmanJob $job) {
	/* @var RedisFunctions */
	global $redis;

	$start = microtime(true);

	$data = json_decode($job->workload());

	msg('Yes, master.');
	msg('Processing ' . count($data) . ' items.');

	foreach ($data as $inc) {
		list($hash, $timestamp, $count) = $inc;
		$redis->process($hash, $timestamp, $count);
	}

	msg(sprintf('Work complete! (%.3f s)', microtime(true) - $start));
}
