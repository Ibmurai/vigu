<?php
require_once dirname(__FILE__) . '/../handlers/shutdown.php';
for ($i = 0; $i < 100000; $i++) {
	 trigger_error("Notice #$i");
}
echo "Generated $i notices.\n";
