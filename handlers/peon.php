<?php

const COUNTS_PREFIX = '|counts|';
const TIMESTAMPS_PREFIX = '|timestamps|';
const SEARCH_PREFIX = '|search|';

// Parse config
msg('Reading config. ' . getcwd() . '/vigu.ini');
$config = parse_ini_file(getcwd() . '/vigu.ini', true);

// Connect to Redis
try {
	/** @var Redis */
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
	/** @var Redis */
	global $redis;
	$redis->select(3);

	$start = microtime(true);

	$data = json_decode($job->workload());

	msg('Yes, master.');
	msg('Processing ' . count($data) . ' items.');

	foreach ($data as $inc) {
		list($hash, $timestamp) = $inc;
		process($hash, $timestamp);
	}

	msg(sprintf('Work complete! (%.3f s)', microtime(true) - $start));
}

/**
 * Process an incoming error.
 *
 * @param string  $hash
 * @param integer $timestamp
 *
 * @return null
 */
function process($hash, $timestamp) {
	global $redis;
	$redis->select(3);

	if (($line = $redis->get($hash)) === false) {
		$line = null;
	}
	$redis->expire($hash, 60);

	$line = store($hash, $timestamp, $line);
	index($hash, $timestamp, $line);
}

/**
 * Store an incoming error.
 *
 * @param string     $hash
 * @param integer    $timestamp
 * @param array|null $line
 *
 * @return array The stored error.
 */
function store($hash, $timestamp, array $line = null) {
	global $redis;
	global $config;
	$redis->select(1);

	if ($line === null) {
		$line = $redis->get($hash);
	}

	if ($oldLine = $redis->get($hash)) {
		if ($oldLine['last'] < $timestamp) {
			$line['last'] = $timestamp;
		}
		if ($oldLine['first'] > $timestamp) {
			$line['first'] = $timestamp;
		} else {
			$line['first'] = $oldLine['first'];
		}
		if ($oldLine['last'] < $timestamp) {
			$line['last'] = $timestamp;
		} else {
			$line['last'] = $oldLine['last'];
		}

		$redis->setex($hash, $config['ttl'] + 360, $line);
	} else {
		$line['first'] = $timestamp;
		$line['last'] = $timestamp;
		$redis->setex($hash, $config['ttl'] + 360, $line);
	}

	return $line;
}

/**
 * Index an incoming error.
 *
 * @param string  $hash
 * @param integer $timestamp
 * @param array   $line
 *
 * @return null
 */
function index($hash, $timestamp, array $line) {
	global $redis;
	$redis->select(2);

	$count = $redis->zIncrBy(COUNTS_PREFIX, 1, $hash);
	$oldLastTimestamp = $redis->zScore(TIMESTAMPS_PREFIX, $hash);

	$redis->multi(Redis::PIPELINE);

	if ($timestamp > $oldLastTimestamp) {
		$redis->zAdd(TIMESTAMPS_PREFIX, $timestamp, $hash);
	} else {
		$timestamp = $oldLastTimestamp;
	}
	foreach (splitPath($line['file']) as $word) {
		$redis->zAdd(TIMESTAMPS_PREFIX . strtolower($word), $timestamp, $hash);
		$redis->zAdd(COUNTS_PREFIX . strtolower($word), $count, $hash);
	}
	$redis->exec();
}

/**
 * Split a path to an array of words.
 *
 * @param string $path
 *
 * @return array
 */
function splitPath($path) {
	return array_filter(preg_split('#[/\\\\\\\.: -]#', $path));
}
