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
class ApiPublicControllerModules extends ApiPublicController {
	/**
	 * Get all registered modules, or all registered modules for a given site.
	 *
	 * @param string $site <null>
	 *
	 * @return void
	 */
	public function getAction($site) {
		$modules = array(
			'xphoto',
			'fsArticle',
			'assCandy',
		);

		$this->assign('modules', $modules);
	}

}
