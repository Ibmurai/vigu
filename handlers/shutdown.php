<?php
/**
 * Include this file through php.ini to gather errors from a server.
 */
class ViguErrorHandler {
	/**
	 * Contains all logged errors.
	 *
	 * @var array[]
	 */
	private static $_log = array();

	/**
	 * The directory to store error logs in, temporarily.
	 *
	 * @var string
	 */
	private static $_dir;

	/**
	 * These super globals get stripped from contexts before storing them.
	 *
	 * @var array
	 */
	private static $_superGlobals = array(
		'GLOBALS',
		'_SERVER',
		'_GET',
		'_POST',
		'_FILES',
		'_COOKIE',
		'_SESSION',
		'_REQUEST',
		'_ENV',
	);

	/**
	 * Read and parse shutdown.ini.
	 *
	 * @return boolean True on success, false on failure.
	 */
	public static function readConfig() {
		if (file_exists($iniFile = dirname(__FILE__) . '/shutdown.ini')) {
			$config = parse_ini_file($iniFile);
			if (isset($config['dir'])) {
				self::$_dir = $config['dir'];
			} else {
				trigger_error('Vigu shutdown handler could not determine the directory to store temporary files, "dir", from shutdown.ini.', E_USER_NOTICE);
				return false;
			}
			return true;
		} else {
			trigger_error('Vigu shutdown handler could not locate shutdown.ini.', E_USER_NOTICE);
			return false;
		}
	}

	/**
	 * Handle any fatal errors.
	 *
	 * @return void
	 */
	public static function shutdown() {
		$lastError = error_get_last();
		$lastLoggedError = self::_getLastLoggedError();

		if ($lastError) {
			// Make sure that the last error has not already been logged
			if ($lastLoggedError) {
				if ($lastLoggedError
					&& $lastError['file'] == $lastLoggedError['file']
					&& $lastError['line'] == $lastLoggedError['line']
					&& $lastError['message'] == $lastLoggedError['message']
					&& self::_errnoToString($lastError['type']) == $lastLoggedError['level']) {
					self::_send();
					return;
				}
			}

			self::_logError($lastError['type'], $lastError['message'], $lastError['file'], $lastError['line']);
		}

		self::_send();
	}

	/**
	 * Handle any soft errors.
	 *
	 * @param integer $errno      Error number.
	 * @param string  $errstr     Message.
	 * @param string  $errfile    File.
	 * @param integer $errline    Line number.
	 * @param array   $errcontext Ignored.
	 *
	 * @return boolean Returns false to continue error handling by other error handlers.
	 */
	public static function error($errno = 0, $errstr = '', $errfile = '', $errline = 0, $errcontext = null) {
		self::_logError($errno, $errstr, $errfile, $errline, $errcontext, debug_backtrace());

		return false;
	}

	/**
	 * Handle any uncaught exceptions.
	 *
	 * @param Exception $exception Exception
	 *
	 * @return void
	 */
	public static function exception(Exception $exception) {
		self::_logError(
			E_ERROR,
			'Uncaught ' . get_class($exception) . ': ' . $exception->getMessage(),
			$exception->getFile(),
			$exception->getLine(),
			array(),
			$exception->getTrace()
		);

		throw $exception;
	}

	/**
	 * Convert an error number to a string.
	 *
	 * @param integer $errno Error number
	 *
	 * @return string
	 */
	private static function _errnoToString($errno) {
		switch($errno) {
			// Default
			default:
				return 'UNKNOWN';

			// PHP 5.2+ error types
			case E_ERROR :
				return 'ERROR';
			case E_WARNING:
				return 'WARNING';
			case E_PARSE:
				return 'PARSE';
			case E_NOTICE:
				return 'NOTICE';
			case E_CORE_ERROR:
				return 'CORE ERROR';
			case E_CORE_WARNING:
				return 'CORE WARNING';
			case E_CORE_ERROR:
				return 'COMPILE ERROR';
			case E_CORE_WARNING:
				return 'COMPILE WARNING';
			case E_USER_ERROR:
				return 'USER ERROR';
			case E_USER_WARNING:
				return 'USER WARNING';
			case E_USER_NOTICE:
				return 'USER NOTICE';
			case E_STRICT:
				return 'STRICT';
			case E_RECOVERABLE_ERROR:
				return 'RECOVERABLE ERROR';

			// PHP 5.3+ only
			case defined('E_DEPRECATED') ? E_DEPRECATED : 10000000 :
				return 'DEPRECATED';
			case defined('E_USER_DEPRECATED') ? E_USER_DEPRECATED : 10000000 :
				return 'USER DEPRECATED';
		}
	}

	/**
	 * Log an error.
	 *
	 * @param integer $errno      The error number.
	 * @param string  $message    The error message.
	 * @param string  $file       The file.
	 * @param integer $line       The line number.
	 * @param array   $context    The error context (variables available).
	 * @param array[] $stacktrace The stacktrace, as produced by debug_backtrace().
	 *
	 * @return void
	 */
	private static function _logError($errno, $message, $file, $line, $context = array(), $stacktrace = array()) {
		array_shift($stacktrace);

		self::$_log[] = array(
			'host'       => isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'Unknown',
			'timestamp'  => time(),
			'level'      => self::_errnoToString($errno),
			'message'    => $message,
			'file'       => $file,
			'line'       => $line,
			'context'    => self::_cleanContext($context),
			'stacktrace' => self::_cleanStacktrace($stacktrace),
		);
	}

	/**
	 * Get the last logged error.
	 *
	 * @return array|null
	 */
	private static function _getLastLoggedError() {
		if (!empty(self::$_log)) {
			return self::$_log[count(self::$_log) - 1];
		} else {
			return null;
		}
	}

	/**
	 * Clean a stacktrace, stripping class instances and array contents.
	 *
	 * @param array &$stacktrace
	 *
	 * @return array The cleaned stacktrace.
	 */
	private static function _cleanStacktrace(&$stacktrace) {
		foreach ($stacktrace as &$line) {
			if (isset($line['object'])) {
				unset($line['object']);
			}
			if (isset($line['args'])) foreach ($line['args'] as &$arg) {
				switch (true) {
					case is_object($arg):
						$arg = 'instance of ' . get_class($arg);
						break;
					case is_array($arg):
						$arg = 'array[' . count($arg) . ']';
						break;
				}
			}
		}

		return $stacktrace;
	}

	/**
	 * Clean a context array of superglobals, class instances and arrays.
	 *
	 * @param array $context
	 *
	 * @return array The cleaned context.
	 */
	private static function _cleanContext($context) {
		$newContext = array();

		foreach ($context as $key => $var) {
			if (array_search($key, self::$_superGlobals) === false) {
				switch (true) {
					case is_object($var):
						$var = 'instance of ' . get_class($var);
						break;
					case is_array($var):
						$var = 'array[' . count($var) . ']';
						break;
				}
				$newContext[$key] = $var;
			}
		}

		return $newContext;
	}

	/**
	 * Store the errors in a file, to be consumed by the Vigu daemon.
	 *
	 * @return void
	 */
	private static function _send() {


		if (!empty(self::$_log)) {
			/*
			$url = 'http://' . self::$_site . '/api';

			$timeStart = microtime(true);
			foreach (array_chunk(self::$_log, 25) as $chunk) {
				if (false && microtime(true) - $timeStart > 0.1) {
					// This is likely 500+ errors posted
					return;
				}
				$httpRequest = new HttpRequest($url, HttpRequest::METH_POST);
				$httpRequest->addPostFields(array('lines' => $chunk));

				try {
					$httpRequest->setOptions(array('timeout' => 1));
					$httpRequest->send();
				} catch (HttpException $e) {
					// Ignored
				}
			}
			*/
			$timeStart = microtime(true);

			file_put_contents(tempnam(self::$_dir . '/', 'vigu-'), serialize(self::$_log), LOCK_EX);

			trigger_error('Wrote ' . count(self::$_log) . ' errors in ' . sprintf('%.5f', microtime(true) - $timeStart) . ' seconds.', E_USER_NOTICE);
		}
	}
}

if (ViguErrorHandler::readConfig()) {
	register_shutdown_function('ViguErrorHandler::shutdown');
	set_error_handler('ViguErrorHandler::error');
	set_exception_handler('ViguErrorHandler::exception');
} else {
	trigger_error('Vigu could not be configured. Data will not be gathered.', E_USER_WARNING);
}
