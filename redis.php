<?php

require_once 'data.php';

function o($str) {
	print($str . "\n");
}

try {
	$redis = new Redis();
	if (!$redis->connect('localhost', 6379)) {
		die("Could not connect to Redis server.");
	} else {
		o('Connected.');
	}
	$redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
	$redis->select(0);
	$redis->flushAll();

	$countRedis = new Redis();
	if (!$countRedis->connect('localhost', 6379)) {
		die("Could not connect to Redis server.");
	} else {
		o('Connected Count.');
	}
	$countRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
	$countRedis->select(1);
	$countRedis->flushAll();

	for ($i = 0; $i < 100; $i++) {
		foreach (getLog(1000) as $line) {
			$key = getKey($line);
			if ($oldLine = $redis->hGetAll($key)) {
				$changed = false;
				if ($oldLine['first'] > $line['timestamp']) {
					$line['first'] = $line['timestamp'];
					$changed = true;
				} else {
					$line['first'] = $oldLine['first'];
				}
				if ($oldLine['last'] < $line['timestamp']) {
					$line['last'] = $line['timestamp'];
					$changed = true;
				} else {
					$line['last'] = $oldLine['last'];
				}
				if ($changed) {
					unset($line['timestamp']);
					$redis->hMset($key, $line);
				}
			} else {
				$line['first'] = $line['timestamp'];
				$line['last'] = $line['timestamp'];
				unset($line['timestamp']);
				$redis->hMset($key, $line);
			}
			updateCount($line, $key);
		}
	}

	o('Top 10 by count:');
	showTop10();

	o('Most recent 10:');
	mostRecent10();

	o('Search for "yet" ordered by timestamp:');
	searchFileRecent10('yet');

	o('Search for "yet another file" ordered by count:');
	searchFileTop10('yet another file');

} catch (RedisException $redEx) {
	die ('RedisException: ' . $redEx->getMessage());
}

function updateCount($line, $key) {
	global $countRedis;

	$count = $countRedis->zIncrBy('|counts|', 1, $key);
	if ($line['last'] > $countRedis->zScore('|timestamps|', $key)) {
		$countRedis->zAdd('|timestamps|', $line['last'], $key);
	}
	foreach (array_filter(preg_split('#[/\\\\\\\.:]#', $line['file'])) as $word) {
		$countRedis->zAdd('|timestamps|' . strtolower($word), $line['last'], $key);
		$countRedis->zAdd('|counts|' . strtolower($word), $count, $key);
	}
}

function showTop10() {
	global $countRedis;
	global $redis;

	foreach ($countRedis->zRevRange('|counts|', 0, 9, true) as $key => $count) {
		$line = $redis->hGetAll($key);
		ol($line, $count);
	}
}

function mostRecent10() {
	global $countRedis;
	global $redis;

	foreach ($countRedis->zRevRange('|timestamps|', 0, 9, true) as $key => $timestamp) {
		ol($redis->hGetAll($key), $countRedis->zScore('|counts|', $key));
	}
}

function searchFileRecent10($search) {
	global $countRedis;
	global $redis;

	$search = array_filter(preg_split('#[/\\\\\\\.: ]#', $search));
	foreach ($search as &$val) {
		$val = '|timestamps|' . strtolower($val);
	}

	$id = '|search|';
	$total = $countRedis->zInter($id, $search);
	foreach ($countRedis->zRevRange($id, 0, 9, true) as $key => $timestamp) {
		ol($redis->hGetAll($key), $countRedis->zScore('|counts|', $key));
	}
}

function searchFileTop10($search) {
	global $countRedis;
	global $redis;

	$search = array_filter(preg_split('#[/\\\\\\\.: ]#', $search));
	foreach ($search as &$val) {
		$val = '|counts|' . strtolower($val);
	}

	$id = '|search|' . uniqid(true);
	$total = $countRedis->zInter($id, $search);
	foreach ($countRedis->zRevRange($id, 0, 9, true) as $key => $timestamp) {
		ol($redis->hGetAll($key), $countRedis->zScore('|counts|', $key));
	}
}

function ol($line, $count) {
	printf('%10.10s|%10.10s|%10.10s|%10.10s|%10.10s|%10.10s|%10.10s|%10.10s|%10.10s|%10.10s' . "\n",
		$line['level'],
		$line['message'],
		$line['host'],
		$line['file'],
		$line['line'],
		date("H:i:s", (integer)$line['first']),
		date("H:i:s", (integer)$line['last']),
		json_encode($line['context']),
		json_encode($line['stacktrace']),
		$count
	);
}

function getKey($line) {
	return $line['file'] . '|' . $line['line'] . '|' . $line['host'] . '|' . $line['level'];
}
