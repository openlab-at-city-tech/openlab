<?php
/**
 * Init
 *
 * @since 1.0.0
 * @package NPS Survey
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Nps_Notice' ) ) :

	/**
	 * Admin
	 */
	class Astra_Nps_Notice {
		/**
		 * Instance
		 *
		 * @since 1.0.0
		 * @var (Object) Astra_Nps_Notice
		 */
		private static $instance = null;

		/**
		 * Get Instance
		 *
		 * @since 1.0.0
		 *
		 * @return object Class object.
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0.0
		 */
		private function __construct() {

			// Return if white labelled is enabled.
			if ( astra_is_white_labelled() ) {
				return;
			}

			add_action( 'admin_footer', array( $this, 'render_astra_nps_survey' ), 999 );

			// Added filter to allow overriding the URL externally.
			add_filter( 'nps_survey_build_url', function( $url ) {
				return get_template_directory_uri() . '/inc/lib/nps-survey/dist/';
			});

			add_filter( 'nps_survey_allowed_screens', function( $screens ) {
				// Restrict other NPS popups on Astra specific pages.
				if ( ! self::is_nps_showing() ) {
					return $screens;
				}

				// Add new screen IDs to the array.
				$screens[] = 'toplevel_page_astra';
				$screens[] = 'astra_page_theme-builder-free';
				$screens[] = 'astra_page_theme-builder';
			
				return $screens;
			});

			add_action( 'admin_enqueue_scripts', array( $this, 'register_assets' ) );
		}

		/**
		 * Check if NPS is showing.
		 *
		 * @since 4.8.7
		 * @return bool
		 */
		public static function is_nps_showing() {
			$astra_nps_options = get_option( Nps_Survey::get_nps_id( 'astra' ), array() );
			$display_after     = isset( $astra_nps_options['display_after'] ) && is_int( $astra_nps_options['display_after'] ) ? $astra_nps_options['display_after'] : 0;

			return Nps_Survey::is_show_nps_survey_form( 'astra', $display_after );
		}

		/**
		 * Register admin scripts for NPS visibility condition.
		 *
		 * @param String $hook Screen name where the hook is fired.
		 * @since 4.8.7
		 * @return void
		 */
		public static function register_assets( ) {
			if ( self::is_nps_showing() ) {
				// Intentionally hiding the other NPS popups when visible along with the Astra NPS.
				$css_file = is_rtl() ? 'nps-visibility-rtl.css' : 'nps-visibility.css';
				wp_enqueue_style( 'astra-nps-visibility', ASTRA_THEME_URI . 'inc/assets/css/' . $css_file, array(), ASTRA_THEME_VERSION );
			}
		}

		/** 
		 * Render NPS Survey
		 *
		 * @return void
		 */
		public function render_astra_nps_survey() {
			Nps_Survey::show_nps_notice(
				'nps-survey-astra',
				array(
					'show_if' => defined( 'ASTRA_THEME_VERSION' ),
					'dismiss_timespan' => 2 * WEEK_IN_SECONDS,
					'display_after' => get_option('astra_nps_show') ? 0 : 2 * WEEK_IN_SECONDS,
					'plugin_slug' => 'astra',
					'message' => array(
						// Step 1 i.e rating input.
						'logo' => esc_url( ASTRA_THEME_URI . 'inc/assets/images/astra-logo.svg'),
						'plugin_name' => __( 'Astra', 'astra' ),
						'nps_rating_message' => __( 'How likely are you to recommend #pluginname to your friends or colleagues?', 'astra' ),

						// Step 2A i.e. positive.
						'feedback_title' => __( 'Thanks a lot for your feedback! 😍', 'astra' ),
						'feedback_content' => __( 'Could you please do us a favor and give us a 5-star rating on WordPress? It would help others choose Astra with confidence. Thank you!', 'astra' ),
						'plugin_rating_link' => esc_url( 'https://wordpress.org/support/theme/astra/reviews/#new-post' ),

						// Step 2B i.e. negative.
						'plugin_rating_title' => __( 'Thank you for your feedback', 'astra' ),
						'plugin_rating_content' => __( 'We value your input. How can we improve your experience?', 'astra' ),
					),
				)
			);
		}

	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Nps_Notice::get_instance();

endif;
