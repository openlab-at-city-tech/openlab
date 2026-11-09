<?php

/*
 * Log file location.
 *
 * The plugin writes its log into one server generated directory. The path is never
 * taken from the setting as given, because the settings page needs only
 * manage_options - which on multisite every subsite administrator holds, without
 * any filesystem access of their own.
 */

if ( ! function_exists( 'blc_get_log_directory' ) ) {
	/**
	 * The single directory the plugin writes its log into.
	 *
	 * @return string Absolute path, with no trailing slash.
	 */
	function blc_get_log_directory() {
		$uploads = wp_upload_dir();

		return untrailingslashit( wp_normalize_path( $uploads['basedir'] ) ) . '/broken-link-checker';
	}
}

if ( ! function_exists( 'blc_get_default_log_basename' ) ) {
	/**
	 * The default log file name.
	 *
	 * @return string
	 */
	function blc_get_default_log_basename() {
		return 'blc-log.txt';
	}
}

if ( ! function_exists( 'blc_sanitize_log_path' ) ) {
	/**
	 * Reduce a configured log path to one inside the plugin's own log directory.
	 *
	 * Only the file name is taken from the configured value; the directory is fixed. A
	 * configured path can therefore no longer aim the log - or the truncation the
	 * clear-log cron performs - at an arbitrary writable file, nor cause an unrelated
	 * directory's .htaccess to be replaced.
	 *
	 * The name must end in .txt, and any inner extension is flattened: "shell.php.txt"
	 * would still run as PHP on an Apache server that maps handlers by any extension
	 * in the name, so it becomes "shell-php.txt".
	 *
	 * @param string $requested The configured path.
	 * @return string An absolute path inside the plugin log directory.
	 */
	function blc_sanitize_log_path( $requested ) {
		$directory = blc_get_log_directory();
		$default   = $directory . '/' . blc_get_default_log_basename();

		$name = sanitize_file_name( wp_basename( str_replace( '\\', '/', strval( $requested ) ) ) );

		if ( 'txt' !== strtolower( pathinfo( $name, PATHINFO_EXTENSION ) ) ) {
			return $default;
		}

		$base = trim( str_replace( '.', '-', pathinfo( $name, PATHINFO_FILENAME ) ), '.-_' );

		if ( '' === $base ) {
			return $default;
		}

		return $directory . '/' . $base . '.txt';
	}
}

if ( ! function_exists( 'blc_get_log_path_error_messages' ) ) {
	/**
	 * The messages shown to the administrator when a log path is refused, by error code.
	 *
	 * @return string[]
	 */
	function blc_get_log_path_error_messages() {
		return array(
			'blc_log_path_traversal' => __( 'The log file path was not accepted because it contains "..". The default log file location is used instead.', 'broken-link-checker' ),
			'blc_log_path_outside'   => __( 'The log file path was not accepted because the log file has to be inside the plugin log directory. The default log file location is used instead.', 'broken-link-checker' ),
			'blc_log_path_name'      => __( 'The log file name was not accepted. Use a file name ending in .txt. The default log file location is used instead.', 'broken-link-checker' ),
			'blc_log_dir_unusable'   => __( 'The log file could not be used because the plugin log directory is a symbolic link, or it or the log file is not writable. Logging has been turned off.', 'broken-link-checker' ),
		);
	}
}

if ( ! function_exists( 'blc_resolve_requested_log_path' ) ) {
	/**
	 * Validate a log path submitted on the settings page.
	 *
	 * Unlike blc_sanitize_log_path(), which quietly reduces whatever it is given, this
	 * refuses a value that tries to leave the log directory so that the administrator
	 * can be told. A bare file name, or a full path that is already inside the log
	 * directory (which is what the settings page displays), is accepted.
	 *
	 * @param string $requested The submitted path. Empty means the default log file.
	 * @return string|WP_Error An absolute path inside the plugin log directory, or an
	 *                         error whose code is a key of blc_get_log_path_error_messages().
	 */
	function blc_resolve_requested_log_path( $requested ) {
		$requested = trim( strval( $requested ) );
		$messages  = blc_get_log_path_error_messages();

		if ( '' === $requested ) {
			return blc_sanitize_log_path( '' );
		}

		$normalized = str_replace( '\\', '/', $requested );

		foreach ( explode( '/', $normalized ) as $segment ) {
			if ( '..' === $segment ) {
				return new WP_Error( 'blc_log_path_traversal', $messages['blc_log_path_traversal'] );
			}
		}

		// Stream wrappers such as phar:// are never a place to put the log.
		if ( false !== strpos( $normalized, '://' ) || false !== strpos( $normalized, "\0" ) ) {
			return new WP_Error( 'blc_log_path_outside', $messages['blc_log_path_outside'] );
		}

		if ( false !== strpos( $normalized, '/' ) ) {
			$directory = untrailingslashit( wp_normalize_path( dirname( $normalized ) ) );

			if ( $directory !== untrailingslashit( wp_normalize_path( blc_get_log_directory() ) ) ) {
				return new WP_Error( 'blc_log_path_outside', $messages['blc_log_path_outside'] );
			}
		}

		$resolved = blc_sanitize_log_path( $normalized );

		// blc_sanitize_log_path() falls back to the default for a name it cannot accept.
		if ( basename( $resolved ) === blc_get_default_log_basename() && basename( $normalized ) !== blc_get_default_log_basename() ) {
			return new WP_Error( 'blc_log_path_name', $messages['blc_log_path_name'] );
		}

		return $resolved;
	}
}

if ( ! function_exists( 'blc_log_directory_is_safe' ) ) {
	/**
	 * Whether the log directory can be trusted to be a real directory inside uploads.
	 *
	 * A directory that is missing is fine: it has not been created yet, and creating it
	 * is done by blc_prepare_log_directory().
	 *
	 * @return bool
	 */
	function blc_log_directory_is_safe() {
		$directory = blc_get_log_directory();

		if ( is_link( $directory ) ) {
			return false;
		}

		$real_directory = realpath( $directory );

		if ( false === $real_directory ) {
			return true;
		}

		/*
		 * Not a symlink, but it still has to resolve to somewhere beneath uploads. This
		 * catches what is_link() does not, such as a junction point on Windows.
		 */
		$uploads      = wp_upload_dir();
		$real_uploads = realpath( $uploads['basedir'] );

		if ( false === $real_uploads ) {
			return false;
		}

		return 0 === strpos(
			trailingslashit( wp_normalize_path( $real_directory ) ),
			trailingslashit( wp_normalize_path( $real_uploads ) )
		);
	}
}

if ( ! function_exists( 'blc_log_path_is_safe' ) ) {
	/**
	 * Whether the plugin may write to a log path.
	 *
	 * Catches the case where the log file, or the directory holding it, has been
	 * replaced by a symlink pointing somewhere else on the filesystem.
	 *
	 * @param string $path Absolute path, as returned by blc_sanitize_log_path().
	 * @return bool
	 */
	function blc_log_path_is_safe( $path ) {
		if ( ! blc_log_directory_is_safe() || is_link( $path ) ) {
			return false;
		}

		$real_directory = realpath( blc_get_log_directory() );

		if ( false === $real_directory ) {
			// Nothing created yet; blc_sanitize_log_path() has already fixed the location.
			return true;
		}

		$real_parent = realpath( dirname( $path ) );

		return false !== $real_parent && $real_parent === $real_directory;
	}
}

if ( ! function_exists( 'blc_prepare_log_directory' ) ) {
	/**
	 * Create the log directory, and an .htaccess and index.php for it only when none
	 * is present.
	 *
	 * A directory that turns out to be a symlink is refused before anything is written
	 * to it, so that the .htaccess cannot end up in the symlink's target.
	 *
	 * @return bool Whether the directory exists and is usable.
	 */
	function blc_prepare_log_directory() {
		$directory = blc_get_log_directory();

		if ( ! blc_log_directory_is_safe() ) {
			return false;
		}

		if ( ! is_dir( $directory ) && ! mkdir( $directory, 0750, true ) ) {
			return false;
		}

		/*
		 * Exclusive creation. A file already in place belongs to whoever put it there
		 * and is never overwritten. The index.php covers servers that ignore .htaccess
		 * but would otherwise list the directory.
		 */
		$protection_files = array(
			'.htaccess' => 'Deny from all',
			'index.php' => '<?php // Silence is golden.',
		);

		foreach ( $protection_files as $file_name => $contents ) {
			$file = $directory . '/' . $file_name;

			if ( file_exists( $file ) ) {
				continue;
			}

			$handle = @fopen( $file, 'x' ); //phpcs:ignore WordPress.WP.AlternativeFunctions

			if ( false !== $handle ) {
				fwrite( $handle, $contents ); //phpcs:ignore WordPress.WP.AlternativeFunctions
				fclose( $handle ); //phpcs:ignore WordPress.WP.AlternativeFunctions
			}
		}

		return true;
	}
}

if ( ! class_exists( 'blcLogger' ) ) :

	define( 'BLC_LEVEL_DEBUG', 0 );
	define( 'BLC_LEVEL_INFO', 1 );
	define( 'BLC_LEVEL_WARNING', 2 );
	define( 'BLC_LEVEL_ERROR', 3 );

	/**
	 * Base class for loggers. Doesn't actually log anything anywhere.
	 *
	 * @package Broken Link Checker
	 * @author Janis Elsts
	 */
	class blcLogger {
		protected $log_level = BLC_LEVEL_DEBUG;

		function __construct( $param = '' ) {

		}

		function blcLogger( $param = '' ) {
			$this->__construct( $param );
		}

		function log( $message, $object = null, $level = BLC_LEVEL_INFO ) {

		}

		function debug( $message, $object = null ) {
			$this->log( $message, $object, BLC_LEVEL_DEBUG );
		}

		function info( $message, $object = null ) {
			$this->log( $message, $object, BLC_LEVEL_INFO );
		}

		function warn( $message, $object = null ) {
			$this->log( $message, $object, BLC_LEVEL_WARNING );
		}

		function error( $message, $object = null ) {
			$this->log( $message, $object, BLC_LEVEL_ERROR );
		}

		function get_messages( $min_level = BLC_LEVEL_DEBUG ) {
			return array();
		}

		function get_log( $min_level = BLC_LEVEL_DEBUG ) {
			return array();
		}

		function clear() {

		}

		public function set_log_level( $level ) {
			$this->log_level = $level;
		}
	}

	/**
	 * A basic logger that uses WP options for permanent storage.
	 *
	 * Log entries are initially stored in memory and need to explicitly
	 * flushed to the database by calling blcCachedOptionLogger::save().
	 *
	 * @package Broken Link Checker
	 * @author Janis Elsts
	 */
	class blcCachedOptionLogger extends blcLogger {
		var $option_name = '';
		var $log;
		var $filter_level = BLC_LEVEL_DEBUG;

		function __construct( $option_name = '' ) {
			$this->option_name = $option_name;
			$oldLog            = get_option( $this->option_name );
			if ( is_array( $oldLog ) && ! empty( $oldLog ) ) {
				$this->log = $oldLog;
			} else {
				$this->log = array();
			}
		}

		function log( $message, $object = null, $level = BLC_LEVEL_DEBUG ) {
			$new_entry = array( $level, $message, $object );
			array_push( $this->log, $new_entry );
		}

		function get_log( $min_level = BLC_LEVEL_DEBUG ) {
			$this->filter_level = $min_level;
			return array_filter( $this->log, array( $this, '_filter_log' ) );
		}

		function _filter_log( $entry ) {
			return ( $entry[0] >= $this->filter_level );
		}

		function get_messages( $min_level = BLC_LEVEL_DEBUG ) {
			$messages = $this->get_log( $min_level );
			return array_map( array( $this, '_get_log_message' ), $messages );
		}

		function _get_log_message( $entry ) {
			return $entry[1];
		}

		function clear() {
			$this->log = array();
			delete_option( $this->option_name );
		}

		function save() {
			update_option( $this->option_name, $this->log );
		}
	}

	/**
	 * A dummy logger that doesn't log anything.
	 */
	class blcDummyLogger extends blcLogger { }

	/**
	 * A basic logger that logs messages to a file.
	 */
	class blcFileLogger extends blcLogger {
		protected $fileName; //phpcs:ignore WordPress.NamingConventions.ValidVariableName.PropertyNotSnakeCase

		public function __construct( $fileName = '' ) {
			$this->fileName = $fileName;
		}

		function log( $message, $object = null, $level = BLC_LEVEL_INFO ) {
			if ( $level < $this->log_level ) {
				return;
			}

			$line = sprintf(
				'[%1$s] %2$s %3$s',
				date( 'Y-m-d H:i:s P' ), //phpcs:ignore WordPress.DateTime.RestrictedFunctions.date_date
				$this->get_level_string( $level ),
				$message
			);

			if ( isset( $object ) ) {
				$line .= ' ' . var_export( $object, true );
			}

			$line .= "\n";

			error_log( $line, 3, $this->fileName );
		}

		function get_messages( $min_level = BLC_LEVEL_DEBUG ) {
			return array( __CLASS__ . ':get_messages() is not implemented' );
		}

		function get_log( $min_level = BLC_LEVEL_DEBUG ) {
			return array( __CLASS__ . ':get_log() is not implemented' );
		}

		public function clear() {
			if ( is_file( $this->fileName ) && is_writable( $this->fileName ) ) {
				$handle = fopen( $this->fileName, 'w' );
				fclose( $handle );
			}
		}

		protected function get_level_string( $level ) {
			switch ( $level ) {
				case BLC_LEVEL_DEBUG:
					return 'DEBUG:';
				case BLC_LEVEL_ERROR:
					return 'ERROR:';
				case BLC_LEVEL_WARNING:
					return 'WARN:';
				case BLC_LEVEL_INFO:
					return 'INFO:';
			}
			return 'LOG:';
		}
	}

endif;

