<?php
if ( !defined('P3_PATH') )
	die( 'Forbidden ');

/**
 * Performance Profile Reader
 *
 * @author GoDaddy.com
 * @version 1.0
 * @package P3_Profiler
 */
 class P3_Profiler_Reader {

	/**
	 * Total site load time (profile + theme + core + plugins)
	 * @var float
	 */
	public $total_time = 0;

	/**
	 * Total site load time (theme + core + plugins - profile)
	 * @var float
	 */
	public $site_time = 0;

	/**
	 * Time spent in themes
	 * Calls which spend time in multiple areas are prioritized prioritized
	 * first as plugins, second as themes, lastly as core.
	 * @var float
	 */
	public $theme_time = 0;

	/**
	 * Time spent in plugins.
	 * Calls which spend time in multiple areas are prioritized prioritized
	 * first as plugins, second as themes, lastly as core.
	 * @var float
	 */
	public $plugin_time = 0;

	/**
	 * Time spent in the profiler code.
	 * @var float
	 */
	public $profile_time = 0;

	/**
	 * Time spent in themes
	 * Calls which spend time in multiple areas are prioritized prioritized
	 * first as plugins, second as themes, lastly as core.
	 * @var float
	 */
	public $core_time = 0;

	/**
	 * Memory usage per visit as reported by memory_get_peak_usage(true)
	 * @var float
	 */
	public $memory = 0;

	/**
	 * Number of plugin related function calls (does not include php internal
	 * calls due to a limitation of how the tick handler works)
	 * @var int
	 */
	public $plugin_calls = 0;

	/**
	 * Extracted URL from the profile
	 * @var string
	 */
	public $report_url = '';

	/**
	 * Extracted date from the first visit in the profile
	 * @var int
	 */
	public $report_date = '';
	
	/**
	 * Total number of mysql queries as reported by get_num_queries()
	 * @var int
	 */
	public $queries = 0;

	/**
	 * Total number of visits recorded in the profile
	 * @var int
	 */
	public $visits = 0;

	/**
	 * List of detected plugins
	 * @var array
	 */
	public $detected_plugins = array();
	
	/**
	 * Array of total time spent in each plugin
	 * key = plugin name
	 * value = seconds (float)
	 * @var array
	 */
	public $plugin_times = array();

	/**
	 * Theme name, as determined from the file path
	 * @var string
	 */
	public $theme_name = '';

	/**
	 * Averaged values for the report
	 * @var array
	 */
	public $averages = array(
		'total' => 0,
		'site' => 0,
		'core' => 0,
		'plugins' => 0,
		'profile' => 0,
		'theme' => 0,
		'memory' => 0,
		'plugin_calls' => 0,
		'queries' => 0,
		'observed' => 0,
		'expected' => 0,
		'drift' => 0,
		'plugin_impact' => 0,
	);

	/**
	 * Internal profile data
	 * @var array
	 */
	private $_data = array();
	
	/**
	 * Scan name, correlates to a file name, with .json stripped off
	 * @var string
	 */
	public $profile_name = '';

	/**
	 * Constructor
	 * @param string $file Full path to the profile json file
	 * @return P3_Profiler_Reader
	 */
	public function __construct( $file ) {

		// Open the file
		$fp = fopen( $file, 'r' );
		if ( FALSE === $fp ) {
			throw new Exception( __( 'Cannot open file: ', 'p3-profiler' ) . $file );
		}
		
		// Decode each line.  Each line is a separate json object.  Whenever a
		// a visit is recorded, a new line is added to the file.		
		while ( !feof( $fp ) ) {
			$line = fgets( $fp );
			if ( empty( $line) ) {
				continue;
			}
			$tmp = json_decode( $line );
			if ( null === $tmp ) {
				throw new Exception( __( 'Cannot parse file: ', 'p3-profiler' ) . $file );
				fclose( $fp );
			}
			$this->_data[] = $tmp;
		}
		
		// Close the file
		fclose( $fp );
		
		// Set the profile name
		$this->profile_name = preg_replace( '/\.json$/', '', basename ( $file ) );
		
		// Parse the data
		$this->_parse();
	}

	/**
	 * Parse from $this->_data and fill in the rest of the member vars
	 * @return void
	 */
	private function _parse() {

		// Check for empty data
		if ( empty( $this->_data ) ) {
			throw new P3_Profiler_No_Data_Exception( __( 'No visits recorded during this profiling session.', 'p3-profiler' ) );
		}
		
		foreach ( $this->_data as $o ) {
			// Set report meta-data
			if ( empty( $this->report_date ) ) {
				$this->report_date = strtotime( $o->date );
				$scheme            = parse_url( $o->url, PHP_URL_SCHEME );
				$host              = parse_url( $o->url, PHP_URL_HOST );
				$path              = parse_url( $o->url, PHP_URL_PATH );
				$this->report_url  = sprintf( '%s://%s%s', $scheme, $host, $path );
				$this->visits      = count( $this->_data );
			}
			
			// Set total times / queries / function calls
			$this->total_time   += $o->runtime->total;
			$this->site_time    += ( $o->runtime->total - $o->runtime->profile );
			$this->theme_time   += $o->runtime->theme;
			$this->plugin_time  += $o->runtime->plugins;
			$this->profile_time += $o->runtime->profile;
			$this->core_time    += $o->runtime->wordpress;
			$this->memory       += $o->memory;
			$this->plugin_calls += $o->stacksize;
			$this->queries      += $o->queries;
			
			// Loop through the plugin data
			foreach ( $o->runtime->breakdown as $k => $v ) {
				if ( !array_key_exists( $k, $this->plugin_times ) ) {
					$this->plugin_times[$k] = 0;
				}
				$this->plugin_times[$k] += $v;
			}
		}

		// Fix plugin names and average out plugin times
		$tmp                = $this->plugin_times;
		$this->plugin_times = array();
		foreach ( $tmp as $k => $v ) {
			$k = $this->get_plugin_name( $k );
			$this->plugin_times[$k] = $v / $this->visits;
		}

		// Get a list of the plugins we detected
		$this->detected_plugins = array_keys( $this->plugin_times );
		sort( $this->detected_plugins );

		// Calculate the averages
		$this->_get_averages();
		
		// Get theme name
		if ( property_exists( $this->_data[0], 'theme_name') ) {
			$this->theme_name = str_replace( realpath( WP_CONTENT_DIR . '/themes/' ), '', realpath( $this->_data[0]->theme_name ) );
			$this->theme_name = preg_replace('|^[\\\/]+([^\\\/]+)[\\\/]+.*|', '$1', $this->theme_name);
			$this->theme_name = $this->_get_theme_name( $this->theme_name );
		} else {
			$this->theme_name = 'unknown';
		}
	}

	/**
	 * Calculate the average values
	 * @return void
	 */
	private function _get_averages() {
		if ( $this->visits <= 0 ) {
			return;
		}
		$this->averages = array(
			'total'         => $this->total_time / $this->visits,
			'site'          => ( $this->total_time - $this->profile_time ) / $this->visits,
			'core'          => $this->core_time / $this->visits,
			'plugins'       => $this->plugin_time / $this->visits,
			'profile'       => $this->profile_time / $this->visits,
			'theme'         => $this->theme_time / $this->visits,
			'memory'        => $this->memory / $this->visits,
			'plugin_calls'  => $this->plugin_calls / $this->visits,
			'queries'       => $this->queries / $this->visits,
			'observed'      => $this->total_time / $this->visits,
			'expected'      => ( $this->theme_time + $this->core_time + $this->profile_time + $this->plugin_time) / $this->visits,
		);
		$this->averages['drift']         = $this->averages['observed'] - $this->averages['expected'];
		$this->averages['plugin_impact'] = $this->averages['plugins'] / $this->averages['site'] * 100;
	}

	/**
	 * Return a list of runtimes times by url
	 * Where the key is the url and the value is an array of runtime values
	 * in seconds (float)
	 * @return array
	 */
	public function get_stats_by_url() {
		$ret = array();
		foreach ( $this->_data as $o ) {
			$tmp = array(
				'url'       => $o->url,
				'core'      => $o->runtime->wordpress,
				'plugins'   => $o->runtime->plugins,
				'profile'   => $o->runtime->profile,
				'theme'     => $o->runtime->theme,
				'queries'   => $o->queries,
				'breakdown' => array()
			);
			foreach ( $o->runtime->breakdown as $k => $v ) {
				$name = $this->get_plugin_name( $k );
				if ( !array_key_exists( $name, $tmp['breakdown'] ) ) {
					$tmp['breakdown'][$name] = 0;
				}
				$tmp['breakdown'][$name] += $v;
			}
			$ret[] = $tmp;
		}
		return $ret;
	}
	
	/**
	 * Get a raw list (slugs only) of the detected plugins
	 * @return array
	 */
	public function get_raw_plugin_list() {
		$tmp = array();
		foreach ( $this->_data as $o ) {
			foreach( $o->runtime->breakdown as $k => $v ) {
				$tmp[] = $k;
			}
		}
		return array_unique( $tmp );
	}

	/**
	 * Translate a plugin name
	 * Uses get_plugin_data if available.
	 * @param string $plugin Plugin name (possible paths will be guessed)
	 * @return string
	 */
	public function get_plugin_name( $plugin ) {
		if ( function_exists( 'get_plugin_data' ) ) {
			$plugin_info = array();
			$possible_paths = array(
				WP_PLUGIN_DIR . "/$plugin.php",
				WP_PLUGIN_DIR . "/$plugin/$plugin.php",
				WPMU_PLUGIN_DIR . "/$plugin.php"
			);
			foreach ( $possible_paths as $path ) {
				if ( file_exists( $path ) ) {
					$plugin_info = get_plugin_data( $path );
					if ( !empty( $plugin_info ) && !empty( $plugin_info['Name'] ) ) {
						return $plugin_info['Name'];
					}
				}
			}
		}
		return $this->_format_name( $plugin );
	}

	/**
	 * Translate a theme name
	 * Uses get_theme_data if available.
	 * @param string $plugin Theme name (possible path will be guessed)
	 * @return string
	 */
	private function _get_theme_name( $theme ) {
		if ( function_exists( 'wp_get_theme') ) {
			$theme_info = wp_get_theme( $theme );
			return $theme_info->get('Name');
		} elseif ( function_exists( 'get_theme_data' ) && file_exists( WP_CONTENT_DIR . '/themes/' . $theme . '/style.css' ) ) {
			$theme_info = get_theme_data( WP_CONTENT_DIR . '/themes/' . $theme . '/style.css' );
			if ( !empty( $theme_info ) && !empty( $theme_info['Name'] ) ) {
				return $theme_info['Name'];
			}
		}
		return $this->_format_name( $theme );
	}
	
	/**
	 * Format plugin / theme name.  This is only to be used if
	 * get_plugin_data() / get_theme_data() aren't available or if the
	 * original files are missing
	 * @param string $name
	 * @return string
	 */
	private function _format_name( $name ) {
		return ucwords( str_replace( array( '-', '_' ), ' ', $name ) );
	}
}
