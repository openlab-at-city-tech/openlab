<?php

/**
 * P3 Plugin Performance Profiler Plugin Controller
 *
 * @author GoDaddy.com
 * @package P3_Profiler
 */
class P3_Profiler_Plugin_Admin {

	/**
	 * List table of the profile scans
	 * @var P3_Profiler_Table
	 */
	public static $scan_table = null;

	/**
	 * Name of the current scan being viewed
	 * @var string
	 */
	public static $scan = '';

	/**
	 * Current action
	 * @var string
	 */
	public static $action = '';

	/**
	 * Profile reader object
	 * @var P3_Profiler_Reader
	 */
	public static $profile = '';

	/**
	 * Remove the admin bar from the customer site when profiling is enabled
	 * to prevent skewing the numbers, as much as possible.  Also prevent ssl
	 * warnings by forcing content into ssl mode if the admin is in ssl mode
	 */
	public static function remove_admin_bar() {
		if ( !is_admin() && is_user_logged_in() ) {
			remove_action( 'init', '_wp_admin_bar_init' );
			if ( true === force_ssl_admin() ) {
				add_filter( 'site_url', array( __CLASS__, '_fix_url' ) );
				add_filter( 'admin_url', array( __CLASS__, '_fix_url' ) );
				add_filter( 'post_link', array( __CLASS__, '_fix_url' ) );
				add_filter( 'category_link', array( __CLASS__, '_fix_url' ) );
				add_filter( 'get_archives_link', array( __CLASS__, '_fix_url' ) );
				add_filter( 'tag_link', array( __CLASS__, '_fix_url' ) );
				add_filter( 'home_url', array( __CLASS__, '_fix_url' ) );
			}
		}
	}

	/**
	 * Replace http with https to avoid SSL warnings in the preview iframe if the admin is in SSL
	 * This will strip off any port numbers and will not replace URLs in off-site links
	 * @param string $url
	 * @return string
	 */
	public static function _fix_url( $url ) {
		static $host = '';
		if ( empty( $host ) ) {
			$host = preg_replace( '/[:\d+$]/', '', $_SERVER['HTTP_HOST'] );
		}
		return str_ireplace( 'http://' . $host, 'https://' . $host, $url );
	}

	/**
	 * Load javascripts
	 */
	public static function load_scripts() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-dialog' );
		wp_enqueue_script( 'jquery-ui-tabs' );
		wp_enqueue_script( 'jquery-ui-progressbar' );
		wp_enqueue_script( 'flot', plugins_url() . '/p3-profiler/js/jquery.flot.min.js', array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'flot.pie', plugins_url() . '/p3-profiler/js/jquery.flot.pie.min.js', array( 'flot' ) );
		wp_enqueue_script( 'flot.navigate', plugins_url() . '/p3-profiler/js/jquery.flot.navigate.js', array( 'flot' ) );
		wp_enqueue_script( 'p3_corners', plugins_url() . '/p3-profiler/js/jquery.corner.js', array( 'jquery-ui-core' ) );
		wp_enqueue_script( 'p3_qtip', plugins_url() . '/p3-profiler/js/jquery.qtip.min.js', array( 'jquery-ui-core' ) );
	}

	/**
	 * Load styles
	 */
	public static function load_styles() {
		if ( 'classic' == get_user_option( 'admin_color' ) ) {
			wp_enqueue_style ( 'jquery-ui-css', plugins_url() . '/p3-profiler/css/jquery-ui-classic.css', array(), P3_VERSION );
		} else {
			wp_enqueue_style ( 'jquery-ui-css', plugins_url() . '/p3-profiler/css/jquery-ui-fresh.css', array(), P3_VERSION );
		}
		wp_enqueue_style( 'p3_qtip_css', plugins_url() . '/p3-profiler/css/jquery.qtip.min.css', array(), P3_VERSION );
		wp_enqueue_style( 'p3_css', plugins_url() . '/p3-profiler/css/p3.css', array(), P3_VERSION );
	}

	/**
	 * Determine the profiles path
	 */
	public static function set_path() {
		$uploads_dir = wp_upload_dir();
		define( 'P3_PROFILES_PATH', $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'profiles' );
	}

	/**
	 * Initialize, upgrade, etc.
	 * Determine the action from the query string that guides the exection path
	 * Catch any special actions here (e.g. download a file)
	 */
	public static function init() {

		// Upgrade
		self::upgrade();

		// Set up the request based on p3_action
		if ( !empty( $_REQUEST['p3_action'] ) ) {
			self::$action = $_REQUEST['p3_action'];
		}
		if ( empty( self::$action ) || 'current-scan' == self::$action ) {
			self::$scan = self::get_latest_profile();
			self::$action = 'current-scan';
		} elseif ( 'view-scan' == self::$action ) {
			self::$scan = '';
			if ( !empty( $_REQUEST['name'] ) ) {
				self::$scan = sanitize_file_name( basename( $_REQUEST['name'] ) );
			} else {
				self::$scan = basename( self::get_latest_profile() );
			}
			if ( empty( self::$scan ) || !file_exists( P3_PROFILES_PATH . DIRECTORY_SEPARATOR . self::$scan ) ) {
				self::add_notice( __( 'Scan does not exist', 'p3-profiler' ), true );
			}
			self::$scan = P3_PROFILES_PATH . DIRECTORY_SEPARATOR . self::$scan;
		}

		// Download the debug logs before output is sent
		if ( 'download-debug-log' == self::$action ) {
			self::download_debug_log();
		} elseif ( 'clear-debug-log' == self::$action ) {
			self::clear_debug_log();
		}
	}

	/**
	 * Dispatcher function.  All requests enter through here
	 * and are routed based upon the p3_action request variable
	 * @return void
	 */
	public static function dispatcher() {

		// If there's a scan, create a viewer object
		if ( !empty( self::$scan ) ) {
			try {
				self::$profile = new P3_Profiler_Reader( self::$scan );
			} catch ( P3_Profiler_No_Data_Exception $e ) {
				echo '<div class="error"><p>' .
						sprintf( __( 'No visits recorded during this profiling session.  Check the <a href="%s">help</a> page for more information', 'p3-profiler' ),
							esc_url( add_query_arg( array( 'p3_action' => 'help', 'current_scan' => null ) ) ) . '#q-circumvent-cache"'
						) .
					 '</p></div>';
				self::$scan = null;
				self::$profile = null;
				self::$action = 'list-scans';
			} catch ( Exception $e ) {
				echo '<div class="error"><p>' . __( 'Error reading scan', 'p3-profiler' ) . '</p></div>';
			}
		} else {
			self::$profile = null;
		}

		// Usability message
		if ( !defined( 'WPP_PROFILING_STARTED' ) ) {
			echo '<div class="updated usability-msg"><p>' . __( 'Click "Start Scan" to run a performance scan of your website.', 'p3-profiler' ) . '</p></div>';
		}

		// Load the list table, let it handle any bulk actions
		if ( empty( self::$profile ) && in_array( self::$action, array( 'list-scans', 'current-scan' ) ) ) {
			self::$scan_table = new P3_Profiler_Table();
			self::$scan_table->prepare_items();
		}

		// Load scripts & styles
		self::load_scripts();
		self::load_styles();

		// Show the page
		require_once P3_PATH . '/templates/template.php';
	}

	/**
	 * Get a list of pages for the auto-scanner
	 * @return array
	 */
	public static function list_of_pages() {

		// Start off the scan with the home page
		$pages = array( get_home_url() ); // Home page

		// Search for a word from the blog description
		$words = array_merge( explode( ' ', get_bloginfo( 'name' ) ), explode( ' ', get_bloginfo( 'description' ) ) );
		$pages[] = home_url( '?s=' . $words[ mt_rand( 0, count( $words ) - 1 ) ] );

		// Get 4 random tags
		$func = create_function('', "return 'rand()';");
		add_filter( 'get_terms_orderby', $func );
		$terms = get_terms( 'post_tag', 'number=4' );
		foreach ( (array) $terms as $term ) {
			$pages[] = get_term_link( $term );
		}

		// Get 4 random categories
		$cats = get_terms( 'category', 'number=4');
		foreach ( (array) $cats as $cat ) {
			$pages[] = get_term_link( $cat );
		}
		remove_filter( 'get_terms_orderby', $func );

		// Get the latest 4 posts
		$tmp = preg_split( '/\s+/', wp_get_archives( 'type=postbypost&limit=4&echo=0' ) );
		if ( !empty( $tmp ) ) {
			foreach ( $tmp as $page ) {
				if ( preg_match( "/href='([^']+)'/", $page, $matches ) ) {
					$pages[] = $matches[1];
				}
			}
		}

		// Scan some admin pages, too
		$pages[] = admin_url();
		$pages[] = admin_url('edit.php');
		$pages[] = admin_url('plugins.php');

		// Fix SSL
		if ( true === force_ssl_admin() ) {
			foreach ( $pages as $k => $v ) {
				$pages[$k] = str_replace( 'http://', 'https://', $v );
			}
		}

		// Done
		return apply_filters( 'p3_automatic_scan_urls', $pages );
	}

	/**************************************************************/
	/** AJAX FUNCTIONS                                           **/
	/**************************************************************/

	/**
	 * Ajax die
	 * @param string $message
	 */
	public static function ajax_die( $message ) {
		global $wp_version;
		if ( version_compare( $wp_version, '3.4-dev' ) >= 0 ) {
			wp_die( $message );
		} else {
			die( $message );
		}
	}

	/**
	 * Start scan
	 */
	public static function ajax_start_scan() {

		// Check nonce
		if ( !check_admin_referer( 'p3_ajax_start_scan', 'p3_nonce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Sanitize the file name
		$filename = sanitize_file_name( basename( $_POST['p3_scan_name'] ) );

		// Add the entry ( multisite installs can run more than one concurrent profile )
		delete_option( 'p3_profiler-error_detection' );
		$opts = get_option( 'p3-profiler_options' );
		if( empty( $opts ) || !is_array( $opts ) ) {
			$opts = array();
		}
		$opts['profiling_enabled'] = array(
			'ip'                   => stripslashes( $_POST['p3_ip'] ),
			'disable_opcode_cache' => ( 'true' == $_POST['p3_disable_opcode_cache'] ),
			'name'                 => $filename,
		);
		update_option( 'p3-profiler_options', $opts );

		// Kick start the profile file
		if ( !file_exists( P3_PROFILES_PATH . "/$filename.json" ) ) {
			$flag = file_put_contents( P3_PROFILES_PATH . "/$filename.json", '' );
		} else {
			$flag = true;
		}

		// Check if either operation failed
		if ( false === $flag ) {
			self::ajax_die( 0 );
		} else {
			self::ajax_die( 1 );
		}
	}

	/**
	 * Stop scan
	 */
	public static function ajax_stop_scan() {

		// Check nonce
		if ( !check_admin_referer( 'p3_ajax_stop_scan', 'p3_nonce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Get current options
		$opts = get_option( 'p3-profiler_options' );
		$opts = $opts['profiling_enabled'];

		// Turn off scanning
		p3_profiler_disable();

		// Tell the user what happened
		self::add_notice( __( 'Turned off performance scanning.', 'p3-profiler' ) );

		// Return the last filename
		if ( !empty( $opts ) && is_array( $opts ) && array_key_exists( 'name', $opts ) ) {
			echo $opts['name'] . '.json';
			self::ajax_die( '' );
		} else {
			self::ajax_die( 0 );
		}
	}

	/**
	 * Save advanced settings
	 */
	public static function ajax_save_settings() {

		// Check nonce
		if ( !check_admin_referer( 'p3_save_settings', 'p3_nonce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Save the new options
		$opts = get_option( 'p3-profiler_options' );
		$opts['disable_opcode_cache'] = ( 'true' == $_POST['p3_disable_opcode_cache'] );
		$opts['cache_buster']         = ( 'true' == $_POST['p3_cache_buster'] );
		$opts['use_current_ip']       = ( 'true' == $_POST['p3_use_current_ip'] );
		$opts['ip_address']           = ( $_POST['p3_ip_address'] );
		$opts['debug']                = ('true' == $_POST['p3_debug'] );
		update_option( 'p3-profiler_options', $opts );

		// Clear the debug log if it's full
		if ( 'true' === $_POST['p3_debug'] ) {
			$log = get_option( 'p3-profiler_debug_log' );
			if ( is_array( $log ) && count( $log ) >= 100  ) {
				update_option( 'p3-profiler_debug_log', array() );
			}
		}

		self::ajax_die( 1 );
	}


	/**************************************************************/
	/** EMAIL RESULTS                                            **/
	/**************************************************************/

	/**
	 * Send results ( presumably to admin or support )
	 */
	public static function ajax_send_results() {

		// Check nonce
		if ( !check_admin_referer( 'p3_ajax_send_results', 'p3_nonce' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}

		// Check fields
		$to      = sanitize_email( $_POST['p3_to'] );
		$from    = sanitize_email( $_POST['p3_from'] );
		$subject = trim( $_POST['p3_subject'] );
		$message = strip_tags( $_POST['p3_message'] );
		$results = strip_tags( $_POST['p3_results'] );

		// Append the results to the message ( if a messge was specified )
		if ( empty( $message ) ) {
			$message = stripslashes( $results );
		} else {
			$message = stripslashes( $message . "\n\n" .$results );
		}

		// Check for errors and send message
		if ( !is_email( $to ) || !is_email( $from ) ) {
			echo '0|';
			_e( 'Invalid subject', 'p3-profiler' );
		} elseif ( empty( $subject ) ) {
			echo '0|';
			_e( 'Invalid subject', 'p3-profiler' );
		} elseif ( false === wp_mail( $to, $subject, $message, "From: $from" ) ) {
			echo '0|';
			printf(
				__( '<a href="%s" target="_blank">wp_mail()</a> function returned false', 'p3-profiler' ),
				'http://codex.wordpress.org/Function_Reference/wp_mail'
			);
		} else {
			echo '1';
		}
		self::ajax_die( '' );
	}

	/**************************************************************/
	/** DEBUG LOG FUNCTIONS                                      **/
	/**************************************************************/

	/**
	 * Clear the debug log
	 */
	public static function clear_debug_log() {
		if ( !check_admin_referer( 'p3-clear-debug-log' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		update_option( 'p3-profiler_debug_log', array() );
		wp_redirect( esc_url( add_query_arg( array( 'p3_action' => 'help' ) ) ) );
	}

	/**
	 * Download the debug log
	 */
	public static function download_debug_log() {
		if ( !check_admin_referer( 'p3-download-debug-log' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$log = get_option( 'p3-profiler_debug_log' );
		if ( empty( $log ) ) {
			$log = array();
		}
		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Type: application/force-download');
		header('Content-Type: application/octet-stream');
		header('Content-Type: application/download');
		header('Content-Disposition: attachment; filename="p3debug.csv";');
		header('Content-Transfer-Encoding: binary');

		// File header
		printf('"%s","%s","%s","%s","%s","%s","%s","%s","%s"' . "\n",
			__( 'Profiling Enabled',  'p3-profiler' ),
			__( 'Recording IP',       'p3-profiler' ),
			__( 'Scan Name',          'p3-profiler' ),
			__( 'Recording',          'p3-profiler' ),
			__( 'Disable Optimizers', 'p3-profiler' ),
			__( 'URL',                'p3-profiler' ),
			__( 'Visitor IP',         'p3-profiler' ),
			__( 'Time',               'p3-profiler' ),
			_x( 'PID',   'Abbreviation for process id', 'p3-profiler' )
		);

		foreach ( (array) $log as $entry ) {
			printf('"%s","%s","%s","%s","%s","%s","%s","%s","%d"' . "\n",
				is_array( $entry['profiling_enabled']  ) ? 'true' : 'false',
				$entry['recording_ip'],
				$entry['scan_name'],
				$entry['recording'] ? 'true' : 'false',
				$entry['disable_optimizers'] ? 'true' : 'false',
				$entry['url'],
				$entry['visitor_ip'],
				date( 'Y-m-d H:i:s', $entry['time'] ),
				$entry['pid']
			);
		}

		// Done
		die();
	}


	/**************************************************************/
	/**  HISTORY PAGE                                            **/
	/**************************************************************/

	/**
	 * Get the latest performance scan
	 * @return string|false
	 */
	public static function get_latest_profile() {

		// Open the directory
		$dir = opendir( P3_PROFILES_PATH );
		if ( false === $dir ) {
			wp_die( __( 'Cannot read profiles directory', 'p3-profiler' ) );
		}

		// Loop through the files, get the path and the last modified time
		$files = array();
		while ( false !== ( $file = readdir( $dir ) ) ) {
			if ( '.json' == substr( $file, -5 ) && filesize( P3_PROFILES_PATH . '/' . $file ) > 0 ) {
				$files[filemtime( P3_PROFILES_PATH . "/$file" )] = P3_PROFILES_PATH . "/$file";
			}
		}
		closedir( $dir );

		// If there are no files, return false
		if ( empty( $files ) ) {
			return false;
		}

		// Sort the files by the last modified time, return the latest
		ksort( $files );
		return array_pop( $files );
	}

	/**
	 * Add a notices
	 * @param string $notice
	 * @param bool $error Default false.  If true, this is a red error.  If false, this is a yellow notice.
	 * @return void
	 */
	public static function add_notice( $notice, $error = false ) {

		// Get any notices on the stack
		$notices = get_option( 'p3_notices' );
		if ( empty( $notices ) ) {
			$notices = array();
		}

		// Add the notice to the stack
		$notices[] = array(
			'msg'   => $notice,
			'error' => $error,
		);

		// Save the stack
		update_option( 'p3_notices', $notices );
	}

	/**
	 * Display notices
	 * @return voide
	 */
	public static function show_notices() {
		$notices = get_option( 'p3_notices' );
		if ( !empty( $notices ) ) {
			$notices = array_unique( $notices );
			foreach ( $notices as $notice ) {
				echo '<div class="' . ( ( $notice['error'] ) ? 'error' : 'updated' ) . '"><p>' . htmlentities( $notice['msg'] ) . '</p></div>';
			}
		}
		update_option( 'p3_notices', array() );
		if ( false !== self::scan_enabled() ) {
			echo '<div class="updated"><p>' . __( 'Performance scanning is enabled.', 'p3-profiler' ) . '</p></div>';
		}
	}

	/**
	 * Make the profiles folder
	 * @param string $path
	 */
	public static function make_profiles_folder( $path ) {
		wp_mkdir_p( $path );
		if ( !file_exists( "$path/.htaccess" ) ) {
			file_put_contents( $path . DIRECTORY_SEPARATOR . '.htaccess', "Deny from all\n" );
		}
		if ( !file_exists( "$path/index.php" ) ) {
			file_put_contents( $path. DIRECTORY_SEPARATOR . 'index.php', '<' . "?php header( 'Status: 404 Not found' ); ?" . ">\nNot found" );
		}
	}

	/**
	 * Delete the profiles folder
	 * @param string $path
	 */
	public static function delete_profiles_folder( $path ) {
		if ( !file_exists( $path ) )
			return;
		$dir = opendir( $path );
		while ( ( $file = readdir( $dir ) ) !== false ) {
			if ( $file != '.' && $file != '..' ) {
				unlink( $path . DIRECTORY_SEPARATOR . $file );
			}
		}
		closedir( $dir );
		rmdir( $path );
	}

	/**
	 * Check to see if a scan is enabled
	 * @return array|false
	 */
	public static function scan_enabled() {
		$opts = get_option( 'p3-profiler_options' );
		if ( !empty( $opts['profiling_enabled'] ) ) {
			return isset( $opts['profiling_enabled'] );
		}
		return false;
	}

	/**
	 * Convert a filesize ( in bytes ) to a human readable filesize
	 * @param int $size
	 * @return string
	 */
	public static function readable_size( $size ) {
		$units = array(
			_x( 'B',  'Abbreviation for bytes',     'p3-profiler' ),
			_x( 'KB', 'Abbreviation for kilobytes', 'p3-profiler' ),
			_x( 'MB', 'Abbreviation for megabytes', 'p3-profiler' ),
			_x( 'GB', 'Abbreviation for gigabytes', 'p3-profiler' ),
			_x( 'TB', 'Abbreviation for terabytes', 'p3-profiler' )
		);
		$size  = max( $size, 0 );
		$pow   = floor( ( $size ? log( $size ) : 0 ) / log( 1024 ) );
		$pow   = min( $pow, count( $units ) - 1 );
		$size /= pow( 1024, $pow );
		return round( $size, 0 ) . ' ' . $units[$pow];
	}

	/**
	 * Actions to take when a multisite blog is removed
	 */
	public static function delete_blog() {
		$uploads_dir = wp_upload_dir();
		$folder      = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'profiles' . DIRECTORY_SEPARATOR;
		self::delete_profiles_folder( $folder );
		delete_option( 'p3-profiler_version' );
		delete_option( 'p3-profiler_options' );
		delete_option( 'p3-profiler_debug_log' );
	}

	/**
	 * Upgrade
	 * Check options, perform any necessary data conversions
	 */
	public static function upgrade() {

		// Get the current version
		$version = get_option( 'p3-profiler_version' );

		// Upgrading from < 1.1.0
		if ( empty( $version ) || version_compare( $version, '1.1.0' ) < 0 ) {
			update_option( 'p3-profiler_disable_opcode_cache', true );
			update_option( 'p3-profiler_use_current_ip', true );
			update_option( 'p3-profiler_ip_address', '' );
			update_option( 'p3-profiler_version', '1.1.0' );
		}

		// Upgrading from < 1.1.2
		if ( empty( $version) || version_compare( $version, '1.1.2' ) < 0 ) {
			update_option( 'p3-profiler_cache_buster', true );
			update_option( 'p3-profiler_version', '1.1.2' );
		}

		// Upgrading from < 1.2.0
		if ( empty( $version) || version_compare( $version, '1.2.0' ) < 0 ) {

			// Set profiling option
			update_option( 'p3-profiler_profiling_enabled', false );
			update_option( 'p3-profiler_version', '1.2.0' );
			update_option( 'p3-profiler_debug', false );
			update_option( 'p3-profiler_debug_log', array() );

			// Remove any .htaccess modifications
			$file = ABSPATH . '/.htaccess';
			if ( file_exists( $file ) && array() !== extract_from_markers( $file, 'p3-profiler' ) ) {
				insert_with_markers( $file, 'p3-profiler', array( '# removed during 1.2.0 upgrade' ) );
			}

			// Remove .profiling_enabled if it's still present
			if ( file_exists( P3_PATH . '/.profiling_enabled' ) ) {
				@unlink( P3_PATH . '/.profiling_enabled' );
			}
		}

		// Upgrading from < 1.3.0
		if ( empty( $version) || version_compare( $version, '1.3.0' ) < 0 ) {
			update_option( 'p3-profiler_version', '1.3.0' );

			// Move to a serialized single option
			$opts = array(
				'profiling_enabled'    => get_option( 'p3-profiler_profiling_enabled' ),
				'disable_opcode_cache' => get_option( 'p3-profiler_disable_opcode_cache' ),
				'use_current_ip'       => get_option( 'p3-profiler_use_current_ip' ),
				'ip_address'           => get_option( 'p3-profiler_ip_address' ),
				'cache_buster'         => get_option( 'p3-profiler_cache_buster' ),
				'debug'                => get_option( 'p3-profiler_debug' )
			);
			update_option( 'p3-profiler_options', $opts );

			// Delete the extra options
			delete_option( 'p3-profiler_disable_opcode_cache' );
			delete_option( 'p3-profiler_use_current_ip' );
			delete_option( 'p3-profiler_ip_address' );
			delete_option( 'p3-profiler_cache_buster' );
			delete_option( 'p3-profiler_debug' );
			delete_option( 'p3-profiler_profiling_enabled' );
		}

		// Upgrading from < 1.5.0
		if ( empty( $version) || version_compare( $version, '1.5.0' ) < 0 ) {
			update_option( 'p3-profiler_version', '1.5.0' );
		}

		// Ensure the profiles folder is there
		$uploads_dir = wp_upload_dir();
		$folder      = $uploads_dir['basedir'] . DIRECTORY_SEPARATOR . 'profiles';
		self::make_profiles_folder( $folder );
	}
}
