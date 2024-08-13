<?php


class TRP_Gettext_Scan {

    protected $settings;

	public function __construct( $settings ) {
		$this->settings = $settings;
	}

	public function scan_gettext() {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && current_user_can( apply_filters( 'trp_translating_capability', 'manage_options' ) ) ) {
			if ( isset( $_POST['action'] ) && $_POST['action'] === 'trp_scan_gettext' ) {
				check_ajax_referer( 'scangettextnonce', 'security' );
				$status = $this->scan();
				echo trp_safe_json_encode( $status ); //phpcs:ignore

			}
		}
		wp_die();
	}

	public function scan() {
		global $trp_gettext_strings_discovered;
		require_once TRP_PLUGIN_DIR . 'assets/lib/potx/potx.php';
		$start_time      = microtime( true );
		$scan_paths_completed = get_option( 'trp_gettext_scan_paths_completed', array( 'paths_completed' => 0, 'current_filename' => null ) );
		$paths_to_scan   = apply_filters( 'trp_paths_to_scan_for_gettext', array_merge( $this->get_active_plugins_paths(), $this->get_active_theme_paths() ) );
		$filename = '';

		$trp_gettext_strings_discovered = array();
		$path_key                       = 0;

		foreach ( $paths_to_scan as $path_key => $path ) {
			if ( $path_key < $scan_paths_completed['paths_completed'] ) {
				continue;
			}
			$interrupted_in_the_recursive_scan = false;
            if ( is_file( $path ) ) {
                trp_potx_process_file( realpath( $path ), 0, 'trp_save_gettext_string' );
            } elseif (is_dir($path)) {
                $iterator = new RecursiveDirectoryIterator( $path );

				// loop through directory and get _e(), __() etc. function calls
				foreach ( new RecursiveIteratorIterator( $iterator ) as $filename => $current_file ) {

					if( $scan_paths_completed['current_filename'] ){
						if( $filename == $scan_paths_completed['current_filename'] ) {
							$scan_paths_completed['current_filename'] = null;
						}
						continue;
					}

					if ( isset( $current_file ) ) {

						$current_file_pathinfo = pathinfo( $current_file );

						if ( ! empty( $current_file_pathinfo['extension'] ) && $current_file_pathinfo['extension'] == "php" ) {

							if ( file_exists( $current_file ) ) {
								trp_potx_process_file( realpath( $current_file ), 0, 'trp_save_gettext_string' );

								if ( ( microtime( true ) - $start_time ) > 2 ) {
									$path_key--;
									$interrupted_in_the_recursive_scan = true;
									break;
								}
							}
						}
					}
				}
			}
			if ( ( microtime( true ) - $start_time ) > 2 ) {
				$filename = ($interrupted_in_the_recursive_scan) ? $filename : '';
				break;
			}
		}

		$this->insert_gettext_in_db();

		$paths_completed     = $path_key + 1;
		$total_paths_to_scan = count( $paths_to_scan );
		$return_array        = array( 'completed'        => false,
		                              'progress_message' => sprintf( esc_html__( 'Scanning item %1$d of %2$d...', 'translatepress-multilingual' ), $paths_completed, $total_paths_to_scan )
		);
		if ( $paths_completed >= $total_paths_to_scan ) {
			delete_option( 'trp_gettext_scan_paths_completed' );
			$return_array['completed'] = true;
		} else {
			update_option( 'trp_gettext_scan_paths_completed', array( 'paths_completed' => $paths_completed, 'current_filename' => $filename ) )  ;
		}

		return $return_array;
	}

	public function get_active_plugins_paths() {
		$the_plugins = get_option( 'active_plugins' );
		$folders     = array();
		foreach ( $the_plugins as $value ) {
			$string = explode( '/', $value );
			if ( isset( $string[0] ) ) {
				$folders[] = trailingslashit( WP_PLUGIN_DIR ) . $string[0];
			}
		}

		return $folders;
	}

	public function get_active_theme_paths() {
		$folders = array();
		// current theme. child theme if present
		$child_theme_dir = get_stylesheet_directory();
		$folders[]       = $child_theme_dir;

		// parent theme
		$parent_theme_dir = get_template_directory();
		if ( $parent_theme_dir !== $child_theme_dir ) {
			$folders[] = $parent_theme_dir;
		}

		return $folders;
	}

	public function insert_gettext_in_db() {
		global $trp_gettext_strings_discovered;
		$trp                   = TRP_Translate_Press::get_trp_instance();
		$trp_query             = $trp->get_component( 'query' );
		$gettext_insert_update = $trp_query->get_query_component( 'gettext_insert_update' );

		$inserted_original_ids = $gettext_insert_update->gettext_original_strings_sync( $trp_gettext_strings_discovered );

		$email_paths       = apply_filters( 'trp_email_paths_', array(
			'templates/emails/',
			'includes/emails/',
			'woocommerce/emails/'
		) );

		// Windows servers have paths with \ instead of /
		$reverse_paths = array();
		foreach($email_paths as $path ){
			$reverse_paths[] = str_replace('/','\\', $path );
		}
		$email_paths = array_merge($email_paths, $reverse_paths );

		$strings_in_emails = array();
		foreach ( $trp_gettext_strings_discovered as $key => $string ) {
			foreach ( $email_paths as $email_path ) {
				if ( strpos( $string['file'], $email_path ) !== false ) {
					$strings_in_emails[] = $inserted_original_ids[$key];
					break;
				}
			}
		}

		$gettext_insert_update->bulk_insert_original_id_meta( $strings_in_emails, 'in_email', 'yes' );
	}
}

function trp_save_gettext_string( $original, $domain, $context, $file, $line, $string_mode, $text_plural = false ) {
	global $trp_gettext_strings_discovered;
	if ( !empty( $original ) ) {
		$domain      = ( empty( $domain ) ) ? 'default' : $domain;
		$context     = ( empty( $context ) ) ? 'trp_context' : $context;
		$text_plural = ( empty( $text_plural ) ) ? '' : $text_plural;

		if ( ! isset( $trp_gettext_strings_discovered[ $context . '::' . $domain . '::' . $original ] ) ) {
			$trp_gettext_strings_discovered[ $context . '::' . $domain . '::' . $original ] = array(
				'original'        => $original,
				'domain'          => $domain,
				'context'         => $context,
				'original_plural' => $text_plural,
				'file'            => $file
			);
		}
	}
}
