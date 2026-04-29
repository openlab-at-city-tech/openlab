<?php
/**
 * Astra BSF Analytics class helps to connect BSF Analytics.
 *
 * @package astra.
 */

defined( 'ABSPATH' ) or exit;

/**
 * Astra BSF Analytics class.
 *
 * @since 4.10.0
 */
class Astra_BSF_Analytics {
	/**
	 * Instance object.
	 *
	 * @var self|null Class Instance.
	 */
	private static $instance = null;

	/**
	 * Events tracker instance.
	 *
	 * @var \BSF_Analytics_Events
	 * @since 4.12.7
	 */
	private static $events;

	/**
	 * Class constructor.
	 *
	 * @return void
	 * @since 4.10.0
	 */
	public function __construct() {
		/*
		* BSF Analytics.
		*/
		if ( ! class_exists( 'BSF_Analytics_Loader' ) ) {
			require_once ASTRA_THEME_DIR . 'inc/lib/bsf-analytics/class-bsf-analytics-loader.php';
		}
		if ( ! class_exists( 'BSF_Analytics_Events' ) ) {
			require_once ASTRA_THEME_DIR . 'inc/lib/bsf-analytics/class-bsf-analytics-events.php';
		}
		self::$events = new \BSF_Analytics_Events( 'astra' );

		add_action( 'init', array( $this, 'init_bsf_analytics' ), 5 );
		add_filter( 'bsf_core_stats', array( $this, 'add_astra_analytics_data' ) );

		// Track Astra customizer publish events for kpi and one-time event tracking.
		add_action( 'astra_customizer_save', array( $this, 'maybe_save_customizer_published_timestamp' ) );

		// Track theme version updates.
		add_action( 'astra_theme_update_after', array( $this, 'track_theme_updated' ) );

		// Track onboarding completion/skip in real-time via One Onboarding hooks.
		add_action( 'one_onboarding_completion_astra', array( $this, 'track_onboarding_completed' ) );
		add_action( 'one_onboarding_state_saved_astra', array( $this, 'track_onboarding_skipped' ) );

		// Track admin settings toggles.
		add_action( 'update_option_astra_admin_settings', array( $this, 'track_admin_settings_changes' ), 10, 2 );

		// Track learn chapter progress.
		add_action( 'astra_learn_progress_saved', array( $this, 'track_learn_chapter_progress' ) );
	}

	/**
	 * Initializes BSF Analytics.
	 *
	 * @since 4.10.0
	 * @return void
	 */
	public function init_bsf_analytics() {
		// Bail early if BSF_Analytics_Loader::get_instance is not callable and if Astra white labelling is enabled.
		if ( ! is_callable( '\BSF_Analytics_Loader::get_instance' ) || astra_is_white_labelled() ) {
			return;
		}

		// Kept it for future reference.
		// add_filter(
		// 'uds_survey_allowed_screens',
		// static function ( $screens ) {
		// $screens[] = 'themes';
		// return $screens;
		// }
		// );

		$bsf_analytics = \BSF_Analytics_Loader::get_instance();
		$bsf_analytics->set_entity(
			array(
				'astra' => array(
					'product_name'        => 'Astra',
					'path'                => ASTRA_THEME_DIR . 'inc/lib/bsf-analytics',
					'author'              => 'brainstormforce',
					'time_to_display'     => '+24 hours',
					'hide_optin_checkbox' => true,

					/* Deactivation Survey */
					'deactivation_survey' => apply_filters(
						'astra_deactivation_survey_data',
						array(
							// Kept it for future reference.
							// array(
							// 'id'                => 'deactivation-survey-astra',
							// 'popup_logo'        => ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg',
							// 'plugin_slug'       => 'astra',
							// 'popup_title'       => __( 'Quick Feedback', 'astra' ),
							// 'support_url'       => 'https://wpastra.com/contact/',
							// 'popup_description' => __( 'If you have a moment, please share why you are deactivating Astra:', 'astra' ),
							// 'show_on_screens'   => array( 'themes' ),
							// 'plugin_version'    => ASTRA_THEME_VERSION,
							// 'popup_reasons'     => self::get_default_reasons(),
							// ),
						)
					),
				),
			)
		);
	}

	/**
	 * Get the array of default reasons.
	 *
	 * @since 4.10.0
	 * @return array Default reasons.
	 */
	public static function get_default_reasons() {
		return array(
			'temporary_deactivation' => array(
				'label'           => esc_html__( 'This is a temporary deactivation for testing.', 'astra' ),
				'placeholder'     => esc_html__( 'How can we assist you?', 'astra' ),
				'show_cta'        => 'false',
				'accept_feedback' => 'false',
			),
			'theme_not_working'      => array(
				'label'           => esc_html__( 'The theme isn\'t working properly.', 'astra' ),
				'placeholder'     => esc_html__( 'Please tell us more about what went wrong?', 'astra' ),
				'show_cta'        => 'true',
				'accept_feedback' => 'true',
			),
			'found_better_theme'     => array(
				'label'           => esc_html__( 'I found a better alternative theme.', 'astra' ),
				'placeholder'     => esc_html__( 'Could you please specify which theme?', 'astra' ),
				'show_cta'        => 'false',
				'accept_feedback' => 'true',
			),
			'missing_a_feature'      => array(
				'label'           => esc_html__( 'It\'s missing a specific feature.', 'astra' ),
				'placeholder'     => esc_html__( 'Please tell us more about the feature.', 'astra' ),
				'show_cta'        => 'false',
				'accept_feedback' => 'true',
			),
			'other'                  => array(
				'label'           => esc_html__( 'Other', 'astra' ),
				'placeholder'     => esc_html__( 'Please tell us more details.', 'astra' ),
				'show_cta'        => 'false',
				'accept_feedback' => 'true',
			),
		);
	}

	/**
	 * Callback function to add Astra specific analytics data.
	 *
	 * @param array $stats_data existing stats_data.
	 *
	 * @since 4.10.0
	 * @return array
	 */
	public function add_astra_analytics_data( $stats_data ) {
		if ( ! isset( $stats_data['plugin_data']['astra'] ) ) {
			$stats_data['plugin_data']['astra'] = array();
		}

		$bsf_internal_referrer    = get_option( 'bsf_product_referers', array() );
		$admin_dashboard_settings = get_option( 'astra_admin_settings', array() );
		$is_hf_builder_active     = class_exists( 'Astra_Builder_Helper' ) ? Astra_Builder_Helper::$is_header_footer_builder_active : true;

		$astra_stats = array(
			'free_version'                 => ASTRA_THEME_VERSION,
			'site_language'                => get_locale(),
			'numeric_values'               => array(),
			'boolean_values'               => array(
				'pro_active'             => defined( 'ASTRA_EXT_VER' ),
				'astra_sites_active'     => is_plugin_active( 'astra-sites/astra-sites.php' ),
				'astra_pro_sites_active' => is_plugin_active( 'astra-pro-sites/astra-pro-sites.php' ),
				'is_using_dark_palette'  => Astra_Global_Palette::is_dark_palette(),
			),
			'internal_referrer'            => empty( $bsf_internal_referrer['astra'] ) ? '' : $bsf_internal_referrer['astra'],
			'using_old_header_footer'      => $is_hf_builder_active ? 'no' : 'yes',
			'loading_google_fonts_locally' => isset( $admin_dashboard_settings['self_hosted_gfonts'] ) && $admin_dashboard_settings['self_hosted_gfonts'] ? 'yes' : 'no',
			'preloading_local_fonts'       => isset( $admin_dashboard_settings['preload_local_fonts'] ) && $admin_dashboard_settings['preload_local_fonts'] ? 'yes' : 'no',
			'hosting_provider'             => self::get_hosting_provider(),
		);

		// Add onboarding analytics data.
		self::add_astra_onboarding_analytics_data( $astra_stats );

		// Add learn progress analytics data.
		self::add_learn_progress_analytics_data( $astra_stats );

		// Add KPI tracking data.
		self::add_kpi_tracking_data( $astra_stats );

		$stats_data['plugin_data']['astra'] = array_merge_recursive( $stats_data['plugin_data']['astra'], $astra_stats );

		// Merge events after array_merge_recursive — recursive merge corrupts
		// numeric-indexed event arrays by merging inner keys at the same index.
		self::add_events_tracking_data( $stats_data['plugin_data']['astra'] );

		return $stats_data;
	}

	/**
	 * Add Astra onboarding analytics data.
	 *
	 * @param array $astra_stats Reference to the astra stats data.
	 *
	 * @since 4.11.12
	 * @return array
	 */
	public static function add_astra_onboarding_analytics_data( &$astra_stats ) {
		// Get onboarding analytics data from option.
		/** @psalm-suppress UndefinedClass */
		$option_name     = is_callable( '\One_Onboarding\Core\Register::get_option_name' )
			? \One_Onboarding\Core\Register::get_option_name( 'astra' )
			: 'astra_onboarding';
		$onboarding_data = get_option( $option_name, array() );

		// Return if no onboarding data.
		if ( empty( $onboarding_data ) || ! is_array( $onboarding_data ) ) {
			return;
		}

		// Process skipped screens.
		if ( isset( $onboarding_data['screens'] ) && is_array( $onboarding_data['screens'] ) ) {
			// Determine the last screen.
			$last_screen = isset( $onboarding_data['completion_screen'] ) ? $onboarding_data['completion_screen'] : 'done';

			// Transform the screen keys to their descriptive names.
			$skipped_screens = array();
			foreach ( $onboarding_data['screens'] as $screen ) {
				if ( ! isset( $screen['id'] ) ) {
					continue;
				}

				$screen_id = $screen['id'];

				// Break if we've reached the last screen.
				if ( $screen_id === $last_screen ) {
					break;
				}

				$skipped = isset( $screen['skipped'] ) ? $screen['skipped'] : $screen_id !== $last_screen;
				if ( $skipped ) {
					$skipped_screens[] = $screen_id;
				}
			}

			// Add the skipped screens as an array.
			$astra_stats['onboarding_skipped_screens'] = $skipped_screens;
		}

		// Process pro features.
		if ( isset( $onboarding_data['pro_features'] ) && is_array( $onboarding_data['pro_features'] ) ) {
			$astra_stats['onboarding_selected_pro_features'] = $onboarding_data['pro_features'];
		}

		// Process selected starter templates builder.
		$astra_stats['onboarding_selected_st_builder'] = isset( $onboarding_data['starter_templates_builder'] ) ? $onboarding_data['starter_templates_builder'] : '';

		// Process selected addons
		if ( isset( $onboarding_data['selected_addons'] ) && is_array( $onboarding_data['selected_addons'] ) ) {
			$astra_stats['onboarding_selected_addons'] = $onboarding_data['selected_addons'];
		}

		// Onboarding Completion Status.
		$astra_stats['boolean_values']['onboarding_completed'] = isset( $onboarding_data['completion_screen'] ) && ! empty( $onboarding_data['completion_screen'] );
		if ( $astra_stats['boolean_values']['onboarding_completed'] ) {
			$astra_stats['onboarding_completion_screen'] = isset( $onboarding_data['completion_screen'] ) ? $onboarding_data['completion_screen'] : '';
		}

		// Onboarding Exit Status.
		if ( isset( $onboarding_data['exited_early'] ) ) {
			$astra_stats['boolean_values']['onboarding_exited_early'] = (bool) $onboarding_data['exited_early'];
		}
	}

	/**
	 * Add Astra learn progress analytics data.
	 *
	 * This function retrieves the astra_learn_progress user meta from ALL users
	 * and returns an array of chapter IDs that have been completed by at least one user.
	 * A chapter is considered complete only when ALL its steps are marked as true in the database.
	 *
	 * @param array $astra_stats Reference to the astra stats data.
	 *
	 * @since 4.12.0
	 * @return void
	 */
	public static function add_learn_progress_analytics_data( &$astra_stats ) {
		global $wpdb;

		// Get all users who have learn progress data.
		/** @psalm-suppress UndefinedConstant */
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT user_id, meta_value FROM {$wpdb->usermeta} WHERE meta_key = %s",
				'astra_learn_progress'
			),
			ARRAY_A
		);

		// Return if no data found.
		if ( empty( $results ) ) {
			return;
		}

		// Get the actual chapters structure to validate against.
		$chapters = Astra_Learn::get_chapters_structure();

		// Initialize array to store unique completed chapter IDs across all users.
		$completed_chapters = array();

		// Process each user's progress data.
		foreach ( $results as $row ) {
			$progress_data = maybe_unserialize( $row['meta_value'] );

			// Skip if data is not an array.
			if ( ! is_array( $progress_data ) ) {
				continue;
			}

			// Check each chapter from the actual structure.
			foreach ( $chapters as $chapter ) {
				$chapter_id = $chapter['id'];

				// Skip if already recorded as completed.
				if ( in_array( $chapter_id, $completed_chapters, true ) ) {
					continue;
				}

				// Skip if this chapter has no steps defined.
				if ( ! isset( $chapter['steps'] ) || ! is_array( $chapter['steps'] ) || empty( $chapter['steps'] ) ) {
					continue;
				}

				// Skip if this chapter doesn't exist in this user's progress data.
				if ( ! isset( $progress_data[ $chapter_id ] ) || ! is_array( $progress_data[ $chapter_id ] ) ) {
					continue;
				}

				// Check if ALL steps from the chapter definition are completed for this user.
				$all_steps_completed = true;
				foreach ( $chapter['steps'] as $step ) {
					$step_id = $step['id'];

					// If step is not in progress data or not completed, chapter is incomplete.
					if ( ! isset( $progress_data[ $chapter_id ][ $step_id ] ) || ! $progress_data[ $chapter_id ][ $step_id ] ) {
						$all_steps_completed = false;
						break;
					}
				}

				// If all steps are completed for this user, add chapter ID to the array.
				if ( $all_steps_completed ) {
					$completed_chapters[] = $chapter_id;
				}
			}
		}

		// Add to astra stats if we have completed chapters.
		if ( ! empty( $completed_chapters ) ) {
			$astra_stats['learn_chapters_completed'] = array_values( array_unique( $completed_chapters ) );
		}
	}

	/**
	 * Get the hosting provider (ASN Organization) for the current site.
	 *
	 * @param string      $ip    Optional. IP address to look up. Defaults to server IP.
	 * @param string|null $token Optional. ipinfo.io API token for higher rate limits.
	 *
	 * @return string|null Hosting provider name (ASN org), or null if not detected.
	 */
	public static function get_hosting_provider( $ip = '', $token = null ) {
		if ( 'local' === wp_get_environment_type() ) {
			return null; // Skip on local environments.
		}

		$transient_key = 'ast' . md5( 'hosting_provider' );
		// If no IP provided, try to get the current server IP.
		$is_current_server = false;
		if ( ! $ip ) {
			// Fetch from transient only for current server IP.
			$cached = get_transient( $transient_key );
			if ( $cached ) {
				return $cached;
			}

			$is_current_server = true;
			$ip                = isset( $_SERVER['SERVER_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_ADDR'] ) ) : null;
		}

		// Fallback: resolve server name.
		if ( ! $ip || $ip === '127.0.0.1' || $ip === '::1' ) {
			$hostname = isset( $_SERVER['SERVER_NAME'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_NAME'] ) ) : 'localhost';
			$ip       = gethostbyname( $hostname );
		}

		// Optional: fallback to external service for public IP.
		if ( ! $ip || $ip === '127.0.0.1' || $ip === '::1' ) {
			$response = wp_remote_get( 'https://api.ipify.org' );
			if ( ! is_wp_error( $response ) ) {
				$ip = trim( wp_remote_retrieve_body( $response ) );
			}
		}

		// Validate final IP before using in outbound request.
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		if ( ! $ip ) {
			return null; // Could not detect IP.
		}

		// Query ipinfo.io.
		$url      = 'https://ipinfo.io/' . rawurlencode( $ip ) . '/json' . ( $token ? '?token=' . rawurlencode( $token ) : '' );
		$response = wp_remote_get( $url, array( 'timeout' => 5 ) );
		if ( is_wp_error( $response ) ) {
			return null;
		}

		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		if ( ! empty( $data['org'] ) ) {
			// Example: "AS13335 Cloudflare, Inc."
			$parts            = explode( ' ', $data['org'], 2 );
			$hosting_provider = isset( $parts[1] ) ? $parts[1] : $data['org'];

			// Cache the result for current server IP only.
			if ( $is_current_server ) {
				set_transient( $transient_key, $hosting_provider, defined( 'MONTH_IN_SECONDS' ) ? MONTH_IN_SECONDS : 30 * DAY_IN_SECONDS );
			}
			return $hosting_provider;
		}

		return null;
	}

	/**
	 * Maybe save customizer published timestamp and track first publish event.
	 *
	 * This function checks if Astra customizer settings were modified during the customizer save event.
	 * If so, it records the current timestamp in the '_astra_customizer_published_timestamps' option for KPI tracking
	 * and tracks the first customizer publish as a one-time event.
	 *
	 * @since 4.12.2
	 * @return void
	 */
	public function maybe_save_customizer_published_timestamp() {
		global $wp_customize;

		// Bail if customizer manager not available or no Astra customizer settings modified.
		if ( ! $wp_customize || ! self::has_astra_customizer_settings_modified( $wp_customize ) ) {
			return;
		}

		$timestamps = get_option( '_astra_customizer_published_timestamps', array() );
		if ( ! is_array( $timestamps ) ) {
			$timestamps = array();
		}

		$timestamps[] = time();
		update_option( '_astra_customizer_published_timestamps', $timestamps, false );

		// Track first customizer publish as a one-time event.
		self::$events->track( 'first_customizer_published', ASTRA_THEME_VERSION );
	}

	/**
	 * Check if any Astra-specific settings were modified in the customizer.
	 *
	 * @param WP_Customize_Manager $wp_customize The customizer manager instance.
	 *
	 * @since 4.12.2
	 * @return bool True if Astra customizer settings were modified, false otherwise.
	 */
	public static function has_astra_customizer_settings_modified( $wp_customize ) {
		$posted_values = $wp_customize->unsanitized_post_values();

		// Check if any setting key starts with 'astra-' to identify Astra customizer settings.
		foreach ( $posted_values as $setting_id => $setting_value ) {
			if ( strpos( $setting_id, 'astra-' ) === 0 ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Add KPI tracking data.
	 *
	 * @param array $astra_stats Reference to the astra stats data.
	 * @since 4.12.2
	 * @return void
	 */
	public static function add_kpi_tracking_data( &$astra_stats ) {
		$timestamps = get_option( '_astra_customizer_published_timestamps', array() );
		if ( empty( $timestamps ) || ! is_array( $timestamps ) ) {
			return;
		}

		// Get today's date for comparison.
		$today = wp_date( 'Y-m-d' );

		// Group timestamps by date and count occurrences, excluding today's data.
		$kpi_data              = array();
		$timestamps_to_cleanup = array();

		foreach ( $timestamps as $timestamp ) {
			// Skip invalid timestamps.
			if ( ! is_numeric( $timestamp ) ) {
				continue;
			}

			$date = wp_date( 'Y-m-d', (int) $timestamp );

			// Skip today's data as we may have incomplete data for the current day.
			if ( $date === $today ) {
				continue;
			}

			// Count occurrences by date.
			if ( ! isset( $kpi_data[ $date ] ) ) {
				$kpi_data[ $date ] = array(
					'numeric_values' => array(
						'customizer_published' => 0,
					),
				);
			}
			$kpi_data[ $date ]['numeric_values']['customizer_published']++;

			// Mark this timestamp for cleanup (all timestamps except today's).
			$timestamps_to_cleanup[] = $timestamp;
		}

		// Only add to stats if we have data to report.
		if ( ! empty( $kpi_data ) ) {
			$astra_stats['kpi_records'] = $kpi_data;
		}

		// Cleanup old timestamps that are being sent to analytics.
		// Keep only today's timestamps in the option.
		if ( ! empty( $timestamps_to_cleanup ) ) {
			$remaining_timestamps = array_diff( $timestamps, $timestamps_to_cleanup );
			update_option( '_astra_customizer_published_timestamps', array_values( $remaining_timestamps ), false );
		}
	}

	// ============================================
	// Event Tracking Methods
	// ============================================

	/**
	 * Add one-time event tracking data to analytics payload.
	 *
	 * Detects state-based milestone events (dedup in track() ensures each fires only once),
	 * then flushes pending events into the stats array.
	 *
	 * @param array $astra_stats Reference to the astra stats data.
	 * @since 4.12.7
	 * @return void
	 */
	private static function add_events_tracking_data( &$astra_stats ) {
		$days_since_install = self::get_days_since_install();

		// theme_activated: track once with install source and time-to-value.
		$referer_key   = defined( 'BSF_UTM_ANALYTICS_REFERER' ) ? BSF_UTM_ANALYTICS_REFERER : 'bsf_product_referers';
		$bsf_referrers = get_option( $referer_key, array() );
		$source        = ! empty( $bsf_referrers['astra'] ) ? $bsf_referrers['astra'] : 'self';

		self::$events->track(
			'theme_activated',
			ASTRA_THEME_VERSION,
			array(
				'source'             => $source,
				'days_since_install' => (string) $days_since_install,
				'site_language'      => get_locale(),
				'hosting_provider'   => self::get_hosting_provider(),
			)
		);

		// Ensure events_record always exists in payload.
		if ( ! isset( $astra_stats['events_record'] ) ) {
			$astra_stats['events_record'] = array();
		}

		// Flush pending events into the payload.
		$existing = isset( $astra_stats['events_record'] ) ? $astra_stats['events_record'] : array();
		$flushed  = self::$events->flush_pending();

		if ( ! empty( $existing ) || ! empty( $flushed ) ) {
			$astra_stats['events_record'] = array_merge( $existing, $flushed );
		}
	}

	/**
	 * Track onboarding completion event.
	 *
	 * Fired by `one_onboarding_completion_astra` hook when user finishes onboarding.
	 *
	 * @param array $completion_data Complete data including screens, user info, and product details.
	 * @since 4.12.7
	 * @return void
	 */
	public function track_onboarding_completed( $completion_data ) {
		$properties = self::get_onboarding_properties( $completion_data );

		$completion_screen              = isset( $completion_data['completion_screen'] ) ? sanitize_text_field( $completion_data['completion_screen'] ) : '';
		$properties['completion_screen'] = $completion_screen;

		// Starter Templates builder selection — only relevant if user reached that screen.
		if ( 'starter-templates' === $completion_screen && ! empty( $completion_data['starter_templates_builder'] ) ) {
			$properties['st_builder'] = sanitize_text_field( $completion_data['starter_templates_builder'] );
		}

		// Pro features selected during onboarding.
		$pro_features               = isset( $completion_data['pro_features'] ) && is_array( $completion_data['pro_features'] ) ? $completion_data['pro_features'] : array();
		$properties['pro_features'] = array_map( 'sanitize_text_field', $pro_features );

		// Addons selected during onboarding.
		$selected_addons               = isset( $completion_data['selected_addons'] ) && is_array( $completion_data['selected_addons'] ) ? $completion_data['selected_addons'] : array();
		$properties['selected_addons'] = array_map( 'sanitize_text_field', $selected_addons );

		self::$events->track( 'onboarding_completed', ASTRA_THEME_VERSION, $properties, true );
	}

	/**
	 * Track onboarding skipped event.
	 *
	 * Fired by `one_onboarding_state_saved_astra` hook when user exits onboarding early.
	 * Re-trackable: each exit replaces the previous skip data so the funnel
	 * reflects the user's latest progress.
	 *
	 * @param array $state_data Complete state data including screens and exit info.
	 * @since 4.12.7
	 * @return void
	 */
	public function track_onboarding_skipped( $state_data ) {
		if ( empty( $state_data['exited_early'] ) ) {
			return;
		}

		$properties                = self::get_onboarding_properties( $state_data );
		$properties['exit_screen'] = isset( $state_data['exit_screen'] ) ? sanitize_text_field( $state_data['exit_screen'] ) : '';

		// Allow re-tracking so the funnel reflects the user's latest exit point.
		self::$events->track( 'onboarding_skipped', ASTRA_THEME_VERSION, $properties, true );
	}

	/**
	 * Track admin settings changes for learn tab, local fonts, and abilities toggles.
	 *
	 * Fired by `update_option_astra_admin_settings` hook. Re-trackable since
	 * users can toggle these settings multiple times.
	 *
	 * @param array $old_value Previous settings array.
	 * @param array $new_value Updated settings array.
	 * @since 4.12.7
	 * @return void
	 */
	public function track_admin_settings_changes( $old_value, $new_value ) {
		$was_learn_tab_enabled = ! empty( $old_value['show_learn_tab'] );
		$is_learn_tab_enabled  = ! empty( $new_value['show_learn_tab'] );

		if ( $was_learn_tab_enabled !== $is_learn_tab_enabled ) {
			self::$events->track(
				'learn_tab_toggled',
				ASTRA_THEME_VERSION,
				array( 'enabled' => $is_learn_tab_enabled ? 'yes' : 'no' ),
				true
			);
		}

		$was_local_fonts_enabled  = ! empty( $old_value['self_hosted_gfonts'] );
		$is_local_fonts_enabled   = ! empty( $new_value['self_hosted_gfonts'] );
		$was_preload_enabled      = ! empty( $old_value['preload_local_fonts'] );
		$is_preload_enabled       = ! empty( $new_value['preload_local_fonts'] );

		if ( $was_local_fonts_enabled !== $is_local_fonts_enabled || $was_preload_enabled !== $is_preload_enabled ) {
			self::$events->track(
				'local_fonts_toggled',
				ASTRA_THEME_VERSION,
				array(
					'enabled'          => $is_local_fonts_enabled ? 'yes' : 'no',
					'preload_enabled'  => $is_preload_enabled ? 'yes' : 'no',
				),
				true
			);
		}

		// Abilities API master switch.
		$was_abilities_enabled = ! empty( $old_value['enable_abilities'] );
		$is_abilities_enabled  = ! empty( $new_value['enable_abilities'] );

		if ( $was_abilities_enabled !== $is_abilities_enabled ) {
			self::$events->track(
				'abilities_toggled',
				ASTRA_THEME_VERSION,
				array( 'enabled' => $is_abilities_enabled ? 'yes' : 'no' ),
				true
			);
		}

		// Edit (write) abilities toggle.
		$was_edit_abilities_enabled = ! empty( $old_value['enable_edit_abilities'] );
		$is_edit_abilities_enabled  = ! empty( $new_value['enable_edit_abilities'] );

		if ( $was_edit_abilities_enabled !== $is_edit_abilities_enabled ) {
			self::$events->track(
				'edit_abilities_toggled',
				ASTRA_THEME_VERSION,
				array( 'enabled' => $is_edit_abilities_enabled ? 'yes' : 'no' ),
				true
			);
		}

		// MCP server toggle.
		$was_mcp_server_enabled = ! empty( $old_value['enable_mcp_server'] );
		$is_mcp_server_enabled  = ! empty( $new_value['enable_mcp_server'] );

		if ( $was_mcp_server_enabled !== $is_mcp_server_enabled ) {
			self::$events->track(
				'mcp_server_toggled',
				ASTRA_THEME_VERSION,
				array( 'enabled' => $is_mcp_server_enabled ? 'yes' : 'no' ),
				true
			);
		}
	}

	/**
	 * Track cumulative learn chapter progress.
	 *
	 * Fires on `astra_learn_progress_saved`. Compares chapter completion state
	 * against the full chapter structure and retracks with a cumulative snapshot
	 * so the server always has the latest state.
	 *
	 * @param array $saved_progress Full progress data for the current user.
	 * @since 4.12.7
	 * @return void
	 */
	public function track_learn_chapter_progress( $saved_progress ) {
		if ( empty( $saved_progress ) || ! class_exists( 'Astra_Learn' ) ) {
			return;
		}

		$chapters = Astra_Learn::get_chapters_structure();
		if ( empty( $chapters ) ) {
			return;
		}

		$properties   = array();
		$all_complete = true;

		foreach ( $chapters as $chapter ) {
			$chapter_id = isset( $chapter['id'] ) ? $chapter['id'] : '';
			if ( empty( $chapter_id ) || ! isset( $chapter['steps'] ) || ! is_array( $chapter['steps'] ) || empty( $chapter['steps'] ) ) {
				continue;
			}

			$is_done = true;
			foreach ( $chapter['steps'] as $step ) {
				$step_id = isset( $step['id'] ) ? $step['id'] : '';
				if ( empty( $step_id ) ) {
					continue;
				}
				if ( empty( $saved_progress[ $chapter_id ][ $step_id ] ) ) {
					$is_done = false;
					break;
				}
			}

			$properties[ sanitize_key( $chapter_id ) ] = $is_done ? 'completed' : 'pending';
			if ( ! $is_done ) {
				$all_complete = false;
			}
		}

		$event_value = $all_complete ? 'completed' : 'in_progress';

		self::$events->track( 'learn_chapter_progress', $event_value, $properties, true );
	}

	/**
	 * Extract common onboarding properties from completion/state data.
	 *
	 * @param array $data Onboarding data (completion_data or state_data).
	 * @since 4.12.7
	 * @return array Properties array with skipped/completed screen info.
	 */
	private static function get_onboarding_properties( $data ) {
		$screens           = isset( $data['screens'] ) && is_array( $data['screens'] ) ? $data['screens'] : array();
		$skipped_screens   = array();
		$completed_screens = array();

		foreach ( $screens as $screen ) {
			if ( empty( $screen['id'] ) ) {
				continue;
			}
			$screen_id = sanitize_text_field( $screen['id'] );
			if ( ! empty( $screen['skipped'] ) ) {
				$skipped_screens[] = $screen_id;
			} else {
				$completed_screens[] = $screen_id;
			}
		}

		return array(
			'screens_completed' => ! empty( $completed_screens ) ? $completed_screens : '',
			'screens_skipped'   => ! empty( $skipped_screens ) ? $skipped_screens : '',
			'screens_total'     => (string) count( $screens ),
		);
	}

	/**
	 * Track theme version update as a re-trackable event.
	 *
	 * @param string $previous_version The theme version before the update.
	 * @since 4.12.7
	 * @return void
	 */
	public function track_theme_updated( $previous_version ) {
		if ( empty( $previous_version ) ) {
			return;
		}

		self::$events->track(
			'theme_updated',
			ASTRA_THEME_VERSION,
			array( 'from_version' => $previous_version ),
			true
		);
	}

	// ============================================
	// Helper Methods
	// ============================================

	/**
	 * Get days since Astra was installed.
	 *
	 * @since 4.12.7
	 * @return int Number of days since install.
	 */
	private static function get_days_since_install() {
		$install_time = get_site_option( 'astra_usage_installed_time', 0 );
		if ( $install_time > 0 ) {
			return (int) floor( ( time() - $install_time ) / DAY_IN_SECONDS );
		}
		return 0;
	}

	/**
	 * Initiator.
	 *
	 * @since 4.10.0
	 * @return self initialized object of class.
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
}

/**
 * Initiates the Astra_BSF_Analytics class instance.
 */
Astra_BSF_Analytics::get_instance();
