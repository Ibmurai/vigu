<?php

require_once '../lib/PHP-Daemon/Core/Daemon.php';
require_once '../lib/PHP-Daemon/Core/PluginInterface.php';
require_once '../lib/PHP-Daemon/Core/Lock/LockInterface.php';
require_once '../lib/PHP-Daemon/Core/Lock/Lock.php';
require_once '../lib/PHP-Daemon/Core/Lock/File.php';
require_once '../lib/PHP-Daemon/Core/Plugins/Ini.php';

class ViguDaemon extends Core_Daemon {
	/**
	 * @var string
	 */
	const COUNTS_PREFIX = '|counts|';

	/**
	 * @var string
	 */
	const TIMESTAMPS_PREFIX = '|timestamps|';

	/**
	 * @var string
	 */
	const SEARCH_PREFIX = '|search|';

	/**
	 * The Redis connection for incoming data.
	 *
	 * @var Redis
	 */
	private $_incRedis;

	/**
	 * The Redis connection for storage.
	 *
	 * @var Redis
	 */
	private $_stoRedis;

	/**
	 * The Redis connection for indexing.
	 *
	 * @var Redis
	 */
	private $_indRedis;

	/**
	 * Construct a new Daemon instance.
	 *
	 * @return null
	 */
	protected function __construct() {
		// We want to our daemon to tick once every 1 second.
		$this->loop_interval = 1.00;

		// Set our Lock Provider
		$this->lock = new Core_Lock_File;
		$this->lock->daemon_name = __CLASS__;
		$this->lock->ttl = $this->loop_interval;
		$this->lock->path = dirname(__FILE__);

		parent::__construct();
	}

	/**
	 * Load plugins.
	 *
	 * @return null
	 */
	protected function load_plugins() {
		// Use the INI plugin to provide an easy way to include config settings
		$this->load_plugin('Ini');
		$this->Ini->filename = 'vigu.ini';
	}

	/**
	 * This is where you implement any once-per-execution setup code.
	 *
	 * @return null
	 * @throws Exception
	 */
	protected function setup() {
		if (isset($this->Ini['redis'])) {
			$this->_incRedis = new Redis();
			$this->_incRedis->connect($this->Ini['redis']['host'], $this->Ini['redis']['port'], $this->Ini['redis']['timeout']);
			$this->_incRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
			$this->_incRedis->select(2);

			$this->_stoRedis = new Redis();
			$this->_stoRedis->connect($this->Ini['redis']['host'], $this->Ini['redis']['port'], $this->Ini['redis']['timeout']);
			$this->_stoRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
			$this->_stoRedis->select(0);

			$this->_indRedis = new Redis();
			$this->_indRedis->connect($this->Ini['redis']['host'], $this->Ini['redis']['port'], $this->Ini['redis']['timeout']);
			$this->_indRedis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_PHP);
			$this->_indRedis->select(1);
		} else {
			$this->fatal_error('The configuration does not define a redis section.');
		}

		if (!isset($this->Ini['log'])) {
			$this->fatal_error('shutdown.ini does not define the \'log\' setting.');
		}
		if ($this->is_parent) {
			$emails = array();
			if (isset($this->Ini['emails'])) foreach ($this->Ini['emails'] as $email) {
				$emails[] = $email;
				$this->log("Adding $email to notification list.");
			}
			$this->email_distribution_list = $emails;
		}
	}

	/**
	 * This is where you implement the tasks you want your daemon to perform.
	 * This method is called at the frequency defined by loop_interval.
	 * If this method takes longer than 90% of the loop_interval, a Warning will be raised.
	 *
	 * @return void
	 */
	protected function execute() {
		$this->log($this->_incRedis->lSize('incoming') . ' elements in queue.');
		$count = 0;
		while (($inc = $this->_incRedis->lPop('incoming')) && $count++ < 1000) {
			list($hash, $timestamp) = $inc;
			$this->_process($hash, $timestamp);
			$this->_incRedis->del($hash);
		}
	}

	protected function _process($hash, $timestamp) {
		if (($line = $this->_incRedis->get($hash)) === false) {
			$line = null;
		}

		$this->_store($hash, $timestamp, $line);
		$this->_index($hash, $timestamp, $line);
	}

	protected function _store($hash, $timestamp, $line = null) {
		if ($line === null) {
			$line = $this->_stoRedis->get($hash);
		}

		if ($oldLine = $this->_stoRedis->get($hash)) {
			$changed = false;

			if ($oldLine['last'] < $timestamp) {
				$line['last'] = $timestamp;
			}
			if ($oldLine['first'] > $timestamp) {
				$line['first'] = $timestamp;
				$changed = true;
			} else {
				$line['first'] = $oldLine['first'];
			}
			if ($oldLine['last'] < $timestamp) {
				$line['last'] = $timestamp;
				$changed = true;
			} else {
				$line['last'] = $oldLine['last'];
			}

			if ($changed) {
				$this->_stoRedis->set($hash, $line);
			}
		} else {
			$line['first'] = $timestamp;
			$line['last'] = $timestamp;
			$this->_stoRedis->set($hash, $line);
		}
	}

	protected function _index($hash, $timestamp, $line = null) {
		$count = $this->_indRedis->zIncrBy(self::COUNTS_PREFIX, 1, $hash);
		$oldLastTimestamp = $this->_indRedis->zScore(self::TIMESTAMPS_PREFIX, $hash);
		if ($timestamp > $oldLastTimestamp) {
			$this->_indRedis->zAdd(self::TIMESTAMPS_PREFIX, $timestamp, $hash);
		} else {
			$timestamp = $oldLastTimestamp;
		}
		if ($line !== null) {
			foreach (self::_splitPath($line['file']) as $word) {
				$this->_indRedis->zAdd(self::TIMESTAMPS_PREFIX . strtolower($word), $timestamp, $hash);
				$this->_indRedis->zAdd(self::COUNTS_PREFIX . strtolower($word), $count, $hash);
			}
		}
	}

	/**
	 * Split a path to an array of words.
	 *
	 * @param string $path
	 *
	 * @return array
	 */
	private static function _splitPath($path) {
		return array_filter(preg_split('#[/\\\\\\\.: -]#', $path));
	}

	/**
	 * Gets the log file name from configuration.
	 *
	 * @return string
	 */
	protected function log_file() {
		return $this->Ini['log'];
	}
}

// The daemon needs to know from which file it was executed.
ViguDaemon::setFilename(__file__);

// The run() method will start the daemon loop.
ViguDaemon::getInstance()->run();
