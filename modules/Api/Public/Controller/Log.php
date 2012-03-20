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
 * @author     Johannes Skov Frandsen <jsf@fynskemedier.dk>
 */
class ApiPublicControllerLog extends ApiPublicController {
	/**
	 * Grid search
	 *
	 * @param integer $rows   <100> Number of rows to return
	 * @param integer $page   <1> Result offset
	 * @param string  $sidx   <level> Field to sort by
	 * @param string  $sord   <desc> Sort direction (asc or desc)
	 * @param string  $module <null> Limit search to one module (default is null = all modules)
	 * @param string  $site   <null> Limit search to one site (default is null = all sites)
	 * @param string  $level  <null> Limit search to one error level (default is null = all levels)
	 * @param string  $path   <null> Limit search to match a specific path (default is null = any file path)
	 * @param string  $search <null> Limit search to match string in error message (default is null = any error message)
	 * 
	 * @return void
	 */
	public function gridAction($rows, $page, $sidx, $sord, $module, $site, $level, $path, $search) {
		//Do your magic here
		
		//This is a dummy implementaion which generate dummy data for testing 
		$dummyMessage = array(1 => 'Its wrong I\'m telling you', 2 => 'Arhhhhh it hurts!', 3 => 'You can\'t be serious', 3 => 'Oh pleace don\'t do that');
		$dummyLevel = array(2 => 'Error', 3 => 'Warning', 4 => 'Notice', 5 => 'Deprecated');

		$rows = array();
		for ($i = 0; $i < 300; $i++) {
			$message = $dummyMessage[array_rand($dummyMessage)];
			$level = $dummyLevel[array_rand($dummyLevel)];
			$errors  = rand(42, 4200);

			$rows[$i]['id'] = $i + 1; //the unique id of the row
			$rows[$i]['cell'] = array($level, $message, $errors); //an array that contains the data for a row
		}
		$this->assign('total', 100);  // total pages for the query
		$this->assign('page', 1);     // current page of the query
		$this->assign('records', 300);// total number of records for the query
		$this->assign('rows', $rows); // an array that contains the actual data
	}
}
