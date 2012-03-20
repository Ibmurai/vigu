<?php
/**
 * TODO_DOCUMENT_ME
 *
 * PHP version 5
 *
 * @category TODO_DOCUMENT_ME
 * @package  TODO_DOCUMENT_ME
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2012-TODO-
 */

/**
 * TODO_DOCUMENT_ME
 *
 * @category   TODO_DOCUMENT_ME
 * @package    TODO_DOCUMENT_ME
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class ApiPublicControllerLog extends ApiPublicController {
	/**
	 *
	 * @return void
	 */
	public function searchAction() {
		$lines = array(
			array(
				'message'    => 'FATAL LOLZ!',
				'level'      => 'Fatal',
				'file'       => 'lolz.php',
				'module'     => 'xphoto',
				'site'       => 'fyens.dk',
				'line'       => 42,
				'count'      => rand(42,4200),
				'stacktrace' => debug_backtrace(),
			),
			array(
				'message'    => 'Depped LOLZ!',
				'level'      => 'Deprecated',
				'file'       => 'rofl.php',
				'module'     => 'xphoto',
				'site'       => 'fyens.dk',
				'line'       => 11117,
				'count'      => rand(42,4200),
				'stacktrace' => debug_backtrace(),
			),
			array(
				'message'    => 'Need more beer.',
				'level'      => 'Notice',
				'file'       => 'beer.php',
				'module'     => 'xphoto',
				'site'       => 'fyens.dk',
				'line'       => 1,
				'count'      => rand(42,4200),
				'stacktrace' => debug_backtrace(),
			),
			array(
				'message'    => 'FATAL LOLZ!',
				'level'      => 'Fatal',
				'file'       => 'include/lolz.php',
				'module'     => 'xphoto',
				'site'       => 'fyens.dk',
				'line'       => 43,
				'count'      => rand(42,4200),
				'stacktrace' => debug_backtrace(),
			),
			array(
				'message'    => 'FATAL LOLZ!',
				'level'      => 'Fatal',
				'file'       => 'public/class/XphotoPronImage.php',
				'module'     => 'xphoto',
				'site'       => 'placeboobs.com',
				'line'       => 142,
				'count'      => rand(42,4200),
				'stacktrace' => debug_backtrace(),
			),
		);

		$newLines = array();
		for ($i = 0; $i < 60; $i++) {
			$newLines = array_merge($newLines, $lines);
		}

		$this->assign('lines', $newLines);
	}

}
