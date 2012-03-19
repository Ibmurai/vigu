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
class ApiPublicControllerSites extends ApiPublicController {
	/**
	 * Get all registered sites.
	 *
	 * @return void
	 */
	public function getAction() {
		$sites = array(
			'fyens.dk',
			'placeboobs.com',
		);

		$this->assign('sites', $sites);
	}
}
