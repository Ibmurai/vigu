<?php

require_once dirname(__FILE__) . '/../lib/php-gearman-admin/GearmanAdmin.php';
require_once dirname(__FILE__) . '/../handlers/RedisFunctions.php';

$redis = new RedisFunctions(100000);
$redis->flushAll();

echo "Generating notices...\n";
`php gearmanBenchmarkGenerateNotices.php 2>/dev/null`;

echo 'I will now start the GearmanDaemon.';

chdir('../handlers');
system("nohup php GearmanDaemon.php >/dev/null 2>&1 &");
$start = microtime(true);

usleep(250000);

echo "Connecting gearman admin...\n";
$admin = new GearmanAdmin();

while (($size = $admin->refreshStatus()->getTotal('incoming')) > 0) {
	echo "Queue size is $size...\n";
	usleep(100000);
}

printf("Gearman job queue emptied in %.1f seconds.\n", microtime(true) - $start);

echo `kill \`cat /vigu/ViguGearmanDaemon.lock\``;
