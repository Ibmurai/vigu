<?php
/**
 * TODO_DOCUMENT_ME
 *
 * PHP version 5.3+
 *
 * @category TODO_DOCUMENT_ME
 * @package  TODO_DOCUMENT_ME
 * @author   Jens Riisom Schultz <jers@fynskemedier.dk>
 * @since    2012-TODO-
 */
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
require_once 'Doctrine/Common/ClassLoader.php';
/**
 * TODO_DOCUMENT_ME
 *
 * @category   TODO_DOCUMENT_ME
 * @package    TODO_DOCUMENT_ME
 * @subpackage Class
 * @author     Jens Riisom Schultz <jers@fynskemedier.dk>
 */
class ApiPublicController extends FroodController {
	/** @var EntityManager The Doctrine2 entity manager */
	protected $_em;

	/**
	 * Initialization.
	 *
	 * @return void
	 */
	protected function initialize() {
		parent::initialize();

		$this->doOutputJson();
		$this->setupDoctrine();
	}

	/**
	 * Setup Doctrine2.
	 *
	 * @return void
	 */
	private function setupDoctrine() {
		$loader = new \Doctrine\Common\ClassLoader('Doctrine');
		$loader->register();

		$dbParams = array(
			'driver'   => 'pdo_mysql',
			'user'     => 'vigu',
			'password' => 'vigu',
			'dbname'   => 'vigu',
		);
		$path = array(__DIR__ . '/../Model');
		$config = Setup::createAnnotationMetadataConfiguration($path, true);
		$this->_em = EntityManager::create($dbParams, $config);
	}
}
