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

		add_action( 'init', array( $this, 'init_bsf_analytics' ), 5 );
		add_filter( 'bsf_core_stats', array( $this, 'add_astra_analytics_data' ) );
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
		);

		// Add onboarding analytics data.
		self::add_astra_onboarding_analytics_data( $astra_stats );

		$stats_data['plugin_data']['astra'] = array_merge_recursive( $stats_data['plugin_data']['astra'], $astra_stats );

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
