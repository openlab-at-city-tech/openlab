<?php
/**
 * Class Logger
 *
 * @since   July 26, 2008
 * @author  Kenny Katzgrau <katzgrau@gmail.com> (originally) and Katz Web Services, Inc.
 * @package TEC\Common\TrustedLogin\Client
 *
 * @link    https://github.com/katzgrau/KLogger
 */

namespace TEC\Common\TrustedLogin;

use DateTime;
use RuntimeException;

/**
 * Originally copied from https://github.com/katzgrau/KLogger/blob/3c19e350232e5fee0c3e96e3eff1e7be5f37d617/src/Logger.php
 * See: https://github.com/trustedlogin/client/issues/105
 *
 * A light, permissions-checking logging class.
 *
 * Originally written for use with wpSearch.
 *
 * Usage:
 * $log = new Katzgrau\KLogger\Logger('/var/log/', TEC\Common\Psr\Log\LogLevel::INFO);
 * $log->info('Returned a million search results'); //Prints to the log file
 * $log->error('Oh dear.'); //Prints to the log file
 * $log->debug('x = 5'); //Prints nothing due to current severity threshhold
 *
 * We are disabling phpcs for this file because it was a copy of another library. We may refactor it in the future.
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
 * phpcs:disable WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
 * phpcs:disable WordPress.NamingConventions.ValidFunctionName.MethodNameInvalid
 *
 * @author  Kenny Katzgrau <katzgrau@gmail.com>
 * @since   July 26, 2008
 * @link    https://github.com/katzgrau/KLogger
 */
class Logger {

	const EMERGENCY = 'emergency';
	const ALERT     = 'alert';
	const CRITICAL  = 'critical';
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const NOTICE    = 'notice';
	const INFO      = 'info';
	const DEBUG     = 'debug';

	/**
	 * KLogger options
	 *  Anything options not considered 'core' to the logging library should be
	 *  settable view the third parameter in the constructor
	 *
	 *  Core options include the log file path and the log threshold
	 *
	 * @var array
	 */
	protected $options = array(
		'extension'      => 'txt',
		'dateFormat'     => 'Y-m-d G:i:s.u',
		'filename'       => false,
		'flushFrequency' => false,
		'prefix'         => 'log_',
		'logFormat'      => false,
		'appendContext'  => true,
	);

	/**
	 * Path to the log file
	 *
	 * @var string
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	private $logFilePath;

	/**
	 * Current minimum logging threshold
	 *
	 * @var string
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	protected $logLevelThreshold = self::DEBUG;

	/**
	 * The number of lines logged in this instance's lifetime
	 *
	 * @var int
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	private $logLineCount = 0;

	/**
	 * Log Levels
	 *
	 * @var array
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	protected $logLevels = array(
		self::EMERGENCY => 0,
		self::ALERT     => 1,
		self::CRITICAL  => 2,
		self::ERROR     => 3,
		self::WARNING   => 4,
		self::NOTICE    => 5,
		self::INFO      => 6,
		self::DEBUG     => 7,
	);

	/**
	 * This holds the file handle for this instance's log file
	 *
	 * @var resource|false
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	private $fileHandle;

	/**
	 * This holds the last line logged to the logger
	 *  Used for unit tests
	 *
	 * @var string
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	private $lastLine = '';

	/**
	 * Octal notation for default permissions of the log file.
	 *
	 * @var integer
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 */
	private $defaultPermissions = 0777;

	/**
	 * Class constructor
	 *
	 * @param string $logDirectory      File path to the logging directory.
	 * @param string $logLevelThreshold The LogLevel Threshold.
	 * @param array  $options        Associative array of Klogger options (see the $options class property).
	 *
	 * @internal param string $logFilePrefix The prefix for the log file name
	 * @internal param string $logFileExt The extension for the log file
	 * @phpcs suppress WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase
	 *
	 * @throws RuntimeException If the file cannot be written to.
	 * @return void
	 */
	public function __construct( $logDirectory, $logLevelThreshold = self::DEBUG, array $options = array() ) {
		$this->logLevelThreshold = $logLevelThreshold;
		$this->options           = array_merge( $this->options, $options );

		$logDirectory = rtrim( $logDirectory, DIRECTORY_SEPARATOR );
		if ( ! file_exists( $logDirectory ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
			mkdir( $logDirectory, $this->defaultPermissions, true );
		}

		if ( strpos( $logDirectory, 'php://' ) === 0 ) {
			$this->setLogToStdOut( $logDirectory );
			$this->setFileHandle( 'w+' );
		} else {
			$this->setLogFilePath( $logDirectory );
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_is_writable
			if ( file_exists( $this->logFilePath ) && ! is_writable( $this->logFilePath ) ) {
				throw new RuntimeException( 'The file could not be written to. Check that appropriate permissions have been set.' );
			}
			$this->setFileHandle( 'a' );
		}

		if ( ! $this->fileHandle ) {
			throw new RuntimeException( 'The file could not be opened. Check permissions.' );
		}
	}

	/**
	 * Directly sets the log file path.
	 *
	 * @param string $stdOutPath The path to the standard out.
	 */
	public function setLogToStdOut( $stdOutPath ) {
		$this->logFilePath = $stdOutPath;
	}

	/**
	 * Sets the log file path.
	 *
	 * @param string $logDirectory The log file path.
	 */
	public function setLogFilePath( $logDirectory ) {
		if ( $this->options['filename'] ) {
			if ( strpos( $this->options['filename'], '.log' ) !== false || strpos( $this->options['filename'], '.txt' ) !== false ) {
				$this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . $this->options['filename'];
			} else {
				$this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . $this->options['filename'] . '.' . $this->options['extension'];
			}
		} else {
			$this->logFilePath = $logDirectory . DIRECTORY_SEPARATOR . $this->options['prefix'] . gmdate( 'Y-m-d' ) . '.' . $this->options['extension'];
		}
	}

	/**
	 * Sets the file handle for the resource.
	 *
	 * @param string $writeMode The mode to use when opening the file handle using fopen().
	 *
	 * @internal param resource $fileHandle
	 */
	public function setFileHandle( $writeMode ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
		$this->fileHandle = fopen( $this->logFilePath, $writeMode );
	}


	/**
	 * Class destructor
	 */
	public function __destruct() {
		if ( $this->fileHandle ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
			fclose( $this->fileHandle );
		}
	}

	/**
	 * Sets the date format used by all instances of KLogger
	 *
	 * @param string $dateFormat Valid format string for date().
	 */
	public function setDateFormat( $dateFormat ) {
		$this->options['dateFormat'] = $dateFormat;
	}

	/**
	 * Sets the Log Level Threshold
	 *
	 * @param string $logLevelThreshold The log level threshold.
	 */
	public function setLogLevelThreshold( $logLevelThreshold ) {
		$this->logLevelThreshold = $logLevelThreshold;
	}

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level  PSR-3 log level.
	 * @param string $message The message to log.
	 * @param array  $context Additional information for the log.
	 * @return void
	 */
	public function log( $level, $message, array $context = array() ) {
		if ( $this->logLevels[ $this->logLevelThreshold ] < $this->logLevels[ $level ] ) {
			return;
		}
		$message = $this->formatMessage( $level, $message, $context );
		$this->write( $message );
	}

	/**
	 * Writes a line to the log without prepending a status or timestamp
	 *
	 * @param string $message Line to write to the log.
	 * @throws RuntimeException If the file cannot be written to.
	 *
	 * @return void
	 */
	public function write( $message ) {
		if ( null !== $this->fileHandle ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
			if ( fwrite( $this->fileHandle, $message ) === false ) {
				throw new RuntimeException( 'The file could not be written to. Check that appropriate permissions have been set.' );
			} else {
				$this->lastLine = trim( $message );
				++$this->logLineCount;

				if ( $this->options['flushFrequency'] && 0 === $this->logLineCount % $this->options['flushFrequency'] ) {
					fflush( $this->fileHandle );
				}
			}
		}
	}

	/**
	 * Get the file path that the log is currently writing to
	 *
	 * @return string
	 */
	public function getLogFilePath() {
		return $this->logFilePath;
	}

	/**
	 * Get the last line logged to the log file.
	 *
	 * @return string
	 */
	public function getLastLogLine() {
		return $this->lastLine;
	}

	/**
	 * Formats the message for logging.
	 *
	 * @param  string $level   The Log Level of the message.
	 * @param  string $message The message to log.
	 * @param  array  $context The context.
	 * @return string
	 */
	protected function formatMessage( $level, $message, $context ) {
		if ( $this->options['logFormat'] ) {
			$parts   = array(
				'date'          => $this->getTimestamp(),
				'level'         => strtoupper( $level ),
				'level-padding' => str_repeat( ' ', 9 - strlen( $level ) ),
				'priority'      => $this->logLevels[ $level ],
				'message'       => $message,
				'context'       => wp_json_encode( $context ),
			);
			$message = $this->options['logFormat'];
			foreach ( $parts as $part => $value ) {
				$message = str_replace( '{' . $part . '}', $value, $message );
			}
		} else {
			$message = "[{$this->getTimestamp()}] [{$level}] {$message}";
		}

		if ( $this->options['appendContext'] && ! empty( $context ) ) {
			$message .= PHP_EOL . $this->indent( $this->contextToString( $context ) );
		}

		return $message . PHP_EOL;
	}

	/**
	 * Gets the correctly formatted Date/Time for the log entry.
	 *
	 * PHP DateTime is dump, and you have to resort to trickery to get microseconds
	 * to work correctly, so here it is.
	 *
	 * @return string
	 */
	private function getTimestamp() {
		// phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$originalTime = microtime( true );
		$micro        = sprintf( '%06d', ( $originalTime - floor( $originalTime ) ) * 1000000 );
		$date         = new DateTime( gmdate( 'Y-m-d H:i:s.' . $micro, (int) $originalTime ) );

		return $date->format( $this->options['dateFormat'] );
	}

	/**
	 * Takes the given context and coverts it to a string.
	 *
	 * @param  array $context The Context.
	 * @return string
	 */
	protected function contextToString( $context ) {
		$export = '';
		foreach ( $context as $key => $value ) {
			$export .= "{$key}: ";
			$export .= preg_replace(
				array(
					'/=>\s+([a-zA-Z])/im',
					'/array\(\s+\)/im',
					'/^  |\G  /m',
				),
				array(
					'=> $1',
					'array()',
					'    ',
				),
				str_replace( 'array (', 'array(', var_export( $value, true ) ) // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_var_export
			);
			$export .= PHP_EOL;
		}
		return str_replace( array( '\\\\', '\\\'' ), array( '\\', '\'' ), rtrim( $export ) );
	}

	/**
	 * Indents the given string with the given indent.
	 *
	 * @param  string $content The string to indent.
	 * @param  string $indent What to use as the indent.
	 * @return string
	 */
	protected function indent( $content, $indent = '    ' ) {
		return $indent . str_replace( "\n", "\n" . $indent, $content );
	}

	/**
	 * System is unusable.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function emergency( $message, array $context = array() ) {
		$this->log( self::EMERGENCY, $message, $context );
	}

	/**
	 * Action must be taken immediately.
	 *
	 * Example: Entire website down, database unavailable, etc. This should
	 * trigger the SMS alerts and wake you up.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function alert( $message, array $context = array() ) {
		$this->log( self::ALERT, $message, $context );
	}

	/**
	 * Critical conditions.
	 *
	 * Example: Application component unavailable, unexpected exception.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function critical( $message, array $context = array() ) {
		$this->log( self::CRITICAL, $message, $context );
	}

	/**
	 * Runtime errors that do not require immediate action but should typically
	 * be logged and monitored.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function error( $message, array $context = array() ) {
		$this->log( self::ERROR, $message, $context );
	}

	/**
	 * Exceptional occurrences that are not errors.
	 *
	 * Example: Use of deprecated APIs, poor use of an API, undesirable things
	 * that are not necessarily wrong.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function warning( $message, array $context = array() ) {
		$this->log( self::WARNING, $message, $context );
	}

	/**
	 * Normal but significant events.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function notice( $message, array $context = array() ) {
		$this->log( self::NOTICE, $message, $context );
	}

	/**
	 * Interesting events.
	 *
	 * Example: User logs in, SQL logs.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function info( $message, array $context = array() ) {
		$this->log( self::INFO, $message, $context );
	}

	/**
	 * Detailed debug information.
	 *
	 * @param string  $message The message to log.
	 * @param mixed[] $context Additional information about the event.
	 *
	 * @return void
	 */
	public function debug( $message, array $context = array() ) {
		$this->log( self::DEBUG, $message, $context );
	}
}
