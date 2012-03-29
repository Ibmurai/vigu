<?php
	$words1 = array("You ", "Michael Jackson ", "Steve Jobs ", "Bill Gates ", "Stevie Wonder ", "50 Cent ", "Simon Cowell ", "Tinky-Winky ", "Jeremy Clarkson ", "Richard Hammond ", "James May ", "Tony Blair ", "George Bush ", "Sadam Hussein ", "Eric Cartman ", "Bin-Laden ", "Your mother-in-law ", "Your grandma ", "The Asian community ", "This fat chick ", "Johannes Skov Frandsen ", "Jens Riisom Schultz ");
	$words2 = array("has to ", "wants to ", "should not ", "got bribed by a black man to ", "thought they would ", "had an accident and therefore had to ", "more than likely wants to ", "probably should ", "can't exactly ", "hopes to God that their mum wants to ", "told me to ");
	$words3 = array("rape ", "have sex with ", "set on fire ", "pinch ", "murder ", "poo on ", "smile at ", "play with ", "finger ", "toss off ", "wank off ", "discombobulate ", "make babies with ", "make a baby with ", "smell ", "fart on ", "grate the end off of ", "tea-bag ", "deprecate ");
	$words4 = array("your ", "their ");
	$words5 = array("smelly ", "sexy ", "stupid ", "butt-ugly ", "lovely ", "pretty ", "stinky ", "shit-stained ", "scammy ", "strawberry-flavoured ", "fat ", "skinny ", "trampy ", "drunken ", "cheesey ", "mother's ", "father's ", "friends's ", "hair-dresser's ", "manager's ", "wife's ", "husband's ", "children's ", "sister's ", "well polished ");
	$words6 = array("vagina.", "penis.", "pet dog.", "hamster.", "doctor.", "mother.", "father.", "nose.", "belly button.", "man-hole.", "Mac.", "cup of coffee.", "bong.", "glass dildo.");

function _randomMessage() {
	global $words1;
	global $words2;
	global $words3;
	global $words4;
	global $words5;
	global $words6;

	return $words1[array_rand($words1)]
		 . $words2[array_rand($words2)]
		 . $words3[array_rand($words3)]
		 . $words4[array_rand($words4)]
		 . $words5[array_rand($words5)]
		 . $words6[array_rand($words6)]
	;
}

	$hosts = array(
		'fmweb1',
		'fmweb2',
		'fmweb3',
		'fmweb4',
		'fmweb5',
		'fmweb6',
		'fmweb7',
		'fmweb8',
		'fmtest1',
		'php53test',
		'fmbjarne1',
		'develop',
		'placeboobs.com',
	);
function _randomHost() {
	global $hosts;
	return $hosts[array_rand($hosts)];
}

	$levels = array(
		'ERROR',
		'WARNING',
		'PARSE',
		'NOTICE',
		'CORE ERROR',
		'CORE WARNING',
		'COMPILE ERROR',
		'COMPILE WARNING',
		'USER ERROR',
		'USER WARNING',
		'USER NOTICE',
		'USER DEPRECATED',
		'STRICT',
		'RECOVERABLE ERROR',
	);
function _randomLevel() {
	global $levels;
	return $levels[array_rand($levels)];
}

	$files = array(
		'autoexec.bat',
		'C:\WINDOWS\WIN.COM',
		'/home/web/web-fyens/modules/userbase/header.php',
		'lol.txt',
		'/yet/another/file',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/class/XphotoAdminController/Folder.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/class/XphotoAdminController/Image.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/class/XphotoAdminController/Index.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/class/XphotoAdminController/Slideshow.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/class/XphotoAdminController.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/header.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/admin/menu.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/browse_slideshows.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/Ui.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/XphotoConsole.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/XphotoConsoleImportTask.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/XphotoConsoleMiscTasks.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/XphotoConsoleSolrIndexTask.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/console/XphotoConsoleTask.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoAddWatermark.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoAutoRotate.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoBlackAndWhite.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoCenterOnBg.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoConvertToSRGB.php',
		'/Users/ibber_of_crew42/vhosts/fyens/modules/xphoto/class/filters/XphotoCrop.php',
	);
function _randomFile() {
	global $files;
	return $files[array_rand($files)];
}

function getLog($amount = 100) {
	$log = array();

	for ($i = 0; $i < $amount; $i++) {
		$log[] = array(
			'host'       => _randomHost(),
			'timestamp'  => rand(strtotime('yesterday'), time()),
			'level'      => _randomLevel(),
			'message'    => _randomMessage(),
			'file'       => _randomFile(),
			'line'       => rand(0, 43),
			'context'    => array(),
			'stacktrace' => array(),
		);
	}

	return $log;
}
