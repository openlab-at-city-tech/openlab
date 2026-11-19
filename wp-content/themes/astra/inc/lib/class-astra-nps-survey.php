<?php
/**
 * Init
 *
 * @since 4.8.7
 * @package NPS Survey
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Astra_Nps_Survey' ) ) {

	/**
	 * Admin
	 */
	class Astra_Nps_Survey {
		/**
		 * Instance
		 *
		 * @since 4.8.7
		 * @var (Object) Astra_Nps_Survey
		 */
		private static $instance = null;

		/**
		 * Get Instance
		 *
		 * @since 4.8.7
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
		 * @since 4.8.7
		 */
		private function __construct() {

			// Allow users to disable NPS survey via a filter.
			if ( apply_filters( 'astra_nps_survey_disable', false ) ) {
				return;
			}

			$this->version_check();
			add_action( 'init', array( $this, 'load' ), 999 );
			add_filter( 'nps_survey_post_data', array( $this, 'add_versions' ), 10, 2 );
		}

		/**
		 * Version Check
		 *
		 * @return void
		 */
		public function version_check() {

			$file = realpath( dirname( __FILE__ ) . '/nps-survey/version.json' );

			// Is file exist?
			if ( is_file( $file ) ) {
				// @codingStandardsIgnoreStart
				$file_data = json_decode( file_get_contents( $file ), true );
				// @codingStandardsIgnoreEnd
				global $nps_survey_version, $nps_survey_init;
				$path = realpath( dirname( __FILE__ ) . '/nps-survey/nps-survey.php' );
				$version = isset( $file_data['nps-survey'] ) ? $file_data['nps-survey'] : 0;

				if ( null === $nps_survey_version ) {
					$nps_survey_version = '1.0.0';
				}

				// Compare versions.
				if ( version_compare( $version, $nps_survey_version, '>=' ) ) {
					$nps_survey_version = $version;
					$nps_survey_init    = $path;
				}
			}
		}

		/**
		 * Load latest plugin
		 *
		 * @return void
		 */
		public function load() {

			global $nps_survey_version, $nps_survey_init;
			if ( is_file( realpath( $nps_survey_init ) ) ) {
				include_once realpath( $nps_survey_init );
			}
		}

		/**
		 * Add the Astra theme and Astra Pro versions to the NPS survey data.
		 *
		 * Only appended for Astra's own survey submission, not for other
		 * BSF products that share the NPS survey library. The Pro version is
		 * added only when Astra Pro (Addon) is active.
		 *
		 * @param array<mixed> $post_data NPS survey post data.
		 * @param string       $nps_id    NPS ID of the survey being submitted.
		 * @since 4.13.7
		 * @return array<mixed>
		 */
		public function add_versions( $post_data, $nps_id = '' ) {
			if (
				is_array( $post_data ) &&
				defined( 'ASTRA_THEME_VERSION' ) &&
				isset( $post_data['plugin_slug'] ) &&
				'astra' === $post_data['plugin_slug']
			) {
				$post_data['theme_version'] = ASTRA_THEME_VERSION;

				if ( defined( 'ASTRA_EXT_VER' ) ) {
					$post_data['pro_version'] = ASTRA_EXT_VER;
				}
			}

			return $post_data;
		}
	}

	/**
	 * Kicking this off by calling 'get_instance()' method
	 */
	Astra_Nps_Survey::get_instance();

}
