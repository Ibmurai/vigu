<?php

require_once '../lib/PHP-Daemon/Core/Daemon.php';
require_once '../lib/PHP-Daemon/Core/PluginInterface.php';
require_once '../lib/PHP-Daemon/Core/Lock/LockInterface.php';
require_once '../lib/PHP-Daemon/Core/Lock/Lock.php';
require_once '../lib/PHP-Daemon/Core/Lock/File.php';
require_once '../lib/PHP-Daemon/Core/Plugins/Ini.php';

class ViguDaemon extends Core_Daemon {
	/**
	 * Maintains a count of the posting threads.
	 *
	 * @var integer
	 */
	private $_threads = 0;

	/**
	 * We keep the constructor as simple as possible because exceptions thrown from a
	 * constructor are a PITA and difficult to recover from.
	 *
	 * Use the constructor only to set runtime settings, anything else you need to prepare your
	 * daemon should should go in the setup() method.
	 *
	 * Any Plugins should be loaded in the setup() method.
	 *
	 * IMPORTANT to remember to always invoke the parent constructor.
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

	protected function load_plugins() {
		// Use the INI plugin to provide an easy way to include config settings
		$this->load_plugin('Ini');
		$this->Ini->filename = 'shutdown.ini';
	}

	/**
	 * This is where you implement any once-per-execution setup code.
	 *
	 * @return null
	 * @throws Exception
	 */
	protected function setup() {
		if (!isset($this->Ini['log'])) {
			$this->fatal_error('shutdown.ini does not define the \'log\' setting.');
		}
		if (!isset($this->Ini['site'])) {
			$this->fatal_error('shutdown.ini does not define the \'site\' setting.');
		}
		if (!isset($this->Ini['dir'])) {
			$this->fatal_error('shutdown.ini does not define the \'dir\' setting.');
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
		$files = glob($this->Ini['dir'] . '/vigu-*');

		if (count($files) > 0) {
			$newFiles = array();
			foreach ($files as $file) {
				if (@rename($file, $newFile = dirname($file) . '/proc-' . basename($file))) {
					$newFiles[] = $newFile;
				}
			}
			$this->fork(array($this, 'postFilesToServer'), array($newFiles));
		}
	}

	protected function postFilesToServer($files) {
		$this->log('Processing ' . count($files) . ' files...');
		foreach ($files as $file) {
			$this->postFileToServer($file);
		}
	}


	/**
	 * Post the contents of a given file to a Vigu server.
	 *
	 * @param string $file
	 *
	 * @return void
	 */
	protected function postFileToServer($file) {
		$timeStart = microtime(true);

		$lines = @unserialize(file_get_contents($file));

		if ($lines === false) {
			$this->log("Could not unserialize $file.");
		} else {
			$url = 'http://' . $this->Ini['site'] . '/api';
			$this->log("Posting lines from file, $file, to url, $url.");

			foreach (array_chunk($lines, 25) as $chunk) {
				$httpRequest = new HttpRequest($url, HttpRequest::METH_POST);
				$httpRequest->addPostFields(array('lines' => $chunk));

				try {
					$httpRequest->setOptions(array('timeout' => 1));
					$httpRequest->send();
				} catch (HttpException $e) {
					$this->log('Caught exception during posting - Some errors may have been lost: ' . get_class($e) . ': ' . $e->getMessage());

					unlink($file);
					return;
				}
			}
			$this->log('Posted ' . count($lines) . ' lines in ' . sprintf('%.3f', microtime(true) - $timeStart) . ' seconds.');
		}

		unlink($file);
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
