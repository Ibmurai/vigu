<?php

class ViguErrorHandler {
	/**
	 * Contains all logged errors.
	 *
	 * @var array[]
	 */
	private static $_log = array();

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
	 * @param Exception $exception
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
	 * @param integer $errno
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
			'context'    => $context,
			'stacktrace' => $stacktrace,
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
	 * Send the errors to the Vigu server.
	 *
	 * @return void
	 */
	private static function _send() {
		if (!empty(self::$_log)) {
			$url = 'http://vigu.localhost/api';

			$httpRequest = new HttpRequest($url, HttpRequest::METH_POST);
			$httpRequest->addPostFields(array('lines' => self::$_log));

			try {
				$httpRequest->send();
			} catch (HttpException $e) {
				// Ignore all errors
			}
		}
	}
}

register_shutdown_function('ViguErrorHandler::shutdown');
set_error_handler('ViguErrorHandler::error');
set_exception_handler('ViguErrorHandler::exception');
