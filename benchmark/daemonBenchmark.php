<?php

require_once dirname(__FILE__) . '/../handlers/RedisFunctions.php';

$redis = new RedisFunctions(100000);
$redis->flushAll();

echo "Generating notices...\n";
`php gearmanBenchmarkGenerateNotices.php 2>/dev/null`;

echo 'I will now start the daemon.';

chdir('../handlers');
system("nohup php daemon.php >/dev/null 2>&1 &");
$start = microtime(true);

usleep(250000);

echo "Monitoring incoming...\n";

while (($size = $redis->getIncomingSize()) > 0) {
	echo "Incoming size is $size...\n";
	usleep(100000);
}

printf("Incoming queue emptied in %.1f seconds.\n", microtime(true) - $start);

echo `kill \`cat /vigu/ViguDaemon.lock\``;
