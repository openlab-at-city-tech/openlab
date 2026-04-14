<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WPChill_Remote_Upsells' ) ) {
	/**
	 * Class WPChill_Remote_Upsells
	 *
	 * Handles remote upsell promotions fetched from external REST API.
	 * Checks daily via WP Cron if promotions are still valid.
	 *
	 */
	class WPChill_Remote_Upsells {

		/**
		 * Singleton instance
		 *
		 * @var WPChill_Remote_Upsells
		 */
		private static $instance;

		/**
		 * Hook name for the cron event
		 *
		 * @var string
		 */
		private $cron_hook = 'wpchill_upsells_check';

		/**
		 * Option name for storing upsell data
		 *
		 * @var string
		 */
		private $option_name = 'wpchill_upsells_data';

		/**
		 * Transient name for caching API requests
		 *
		 * @var string
		 */
		private $cache_transient = 'wpchill_upsells_cache';

		/**
		 * Remote API URL
		 *
		 * @var string
		 */
		private $api_url = '';

		/**
		 * Current active promotions data (array of promotions)
		 *
		 * @var array
		 */
		private $active_promotions = array();

		/**
		 * Constructor
		 *
		 * @param array $args Optional arguments (api_url).
		 */
		public function __construct( $args = array() ) {
			// Set API URL from arguments
			if ( ! empty( $args['api_url'] ) ) {
				$this->api_url = $args['api_url'];
			}

			// Schedule daily cron if not already scheduled
			if ( ! wp_next_scheduled( $this->cron_hook ) ) {
				wp_schedule_event( time(), 'daily', $this->cron_hook );
			}

			// Hook the cron action
			add_action( $this->cron_hook, array( $this, 'fetch_remote_upsells' ) );

			// Load and apply active promotions
			$this->load_active_promotions();

			// Register activation and deactivation hooks
			$plugin_slug    = explode( '/', plugin_basename( __FILE__ ) )[0];
			$active_plugins = (array) get_option( 'active_plugins', array() );
			foreach ( $active_plugins as $active_plugin ) {
				if ( 0 === strpos( $active_plugin, $plugin_slug . '/' ) ) {
					$plugin_file = WP_PLUGIN_DIR . '/' . $active_plugin;
					register_activation_hook( $plugin_file, array( $this, 'activate' ) );
					register_deactivation_hook( $plugin_file, array( $this, 'deactivate' ) );
					break;
				}
			}
		}

		/**
		 * Get singleton instance
		 *
		 * @param array $args Optional arguments (api_url).
		 * @return WPChill_Remote_Upsells
		 */
		public static function get_instance( $args = array() ) {
			if ( ! isset( self::$instance ) || ! ( self::$instance instanceof WPChill_Remote_Upsells ) ) {
				self::$instance = new WPChill_Remote_Upsells( $args );
			}

			return self::$instance;
		}

		/**
		 * Load active promotions from options and apply filters for each valid one
		 */
		private function load_active_promotions() {
			$data = get_option( $this->option_name, array() );

			if ( empty( $data ) || ! is_array( $data ) ) {
				return;
			}

			$has_css = false;

			foreach ( $data as $key => $promotion ) {
				// Skip invalid promotions
				if ( ! $this->validate_single_promotion( $promotion ) ) {
					continue;
				}

				// Skip expired promotions
				if ( ! $this->is_promotion_valid( $promotion ) ) {
					continue;
				}

				// Store active promotion
				$filter_hook = sanitize_text_field( $promotion['filter'] );
				$this->active_promotions[ $filter_hook ] = $promotion;

				// Apply the upsell button filter for this promotion
				add_filter( $filter_hook, array( $this, 'override_upsell_buttons' ), 15, 2 );

				// Check if any promotion has CSS
				if ( ! empty( $promotion['css'] ) ) {
					$has_css = true;
				}
			}

			// Add CSS output only once if any promotion has CSS
			if ( $has_css ) {
				add_action( 'admin_print_styles', array( $this, 'output_promotion_styles' ), 999 );
			}
		}

		/**
		 * Check if promotion is still valid based on start/end dates
		 *
		 * @param array $data Promotion data.
		 * @return bool
		 */
		private function is_promotion_valid( $data ) {
			if ( empty( $data ) || ! is_array( $data ) ) {
				return false;
			}

			$now = time();

			// Check start date if provided
			if ( ! empty( $data['start_date'] ) ) {
				$start = strtotime( $data['start_date'] );
				if ( $now < $start ) {
					return false;
				}
			}

			// Check end date if provided
			if ( ! empty( $data['end_date'] ) ) {
				$end = strtotime( $data['end_date'] );
				if ( $now > $end ) {
					return false;
				}
			}

			// Check active flag if provided
			if ( isset( $data['active'] ) && ! $data['active'] ) {
				return false;
			}

			return true;
		}

		/**
		 * Fetch upsell data from remote API
		 */
		public function fetch_remote_upsells() {
			// Return cached data if available
			$cached = get_transient( $this->cache_transient );
			if ( false !== $cached ) {
				return $cached;
			}

			$api_url = apply_filters( 'wpchill_upsells_api_url', $this->api_url );

			if ( empty( $api_url ) ) {
				return array();
			}

			$response = wp_remote_get(
				$api_url,
				array(
					'timeout' => 15,
				)
			);

			if ( is_wp_error( $response ) ) {
				return array();
			}

			$status_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $status_code ) {
				return array();
			}

			$body = wp_remote_retrieve_body( $response );
			$data = json_decode( $body, true );

			if ( json_last_error() !== JSON_ERROR_NONE ) {
				return array();
			}

			// Validate - must be an array of promotions
			if ( ! $this->validate_promotions_data( $data ) ) {
				$this->clear_promotions();
				return array();
			}

			// Store the promotions data
			update_option( $this->option_name, $data );
			set_transient( $this->cache_transient, $data, DAY_IN_SECONDS );

			return $data;
		}

		/**
		 * Validate promotions data structure (array of promotions)
		 *
		 * Expected structure:
		 * [
		 *   {
		 *     "active": true,
		 *     "start_date": "2024-11-25",
		 *     "end_date": "2024-12-02",
		 *     "filter": "modula_upsell_buttons",
		 *     "buttons": [...],
		 *     "css": "..."
		 *   },
		 *   {
		 *     "active": true,
		 *     "start_date": "2024-11-25",
		 *     "end_date": "2024-12-02",
		 *     "filter": "dlm_upsell_buttons",
		 *     "buttons": [...],
		 *     "css": "..."
		 *   }
		 * ]
		 *
		 * @param array $data Promotions data.
		 * @return bool
		 */
		private function validate_promotions_data( $data ) {
			if ( empty( $data ) || ! is_array( $data ) ) {
				return false;
			}

			// Check if it's an array of promotions (not a single promotion)
			// If first key is numeric, it's an array of promotions
			if ( ! isset( $data[0] ) ) {
				return false;
			}

			// Validate at least one promotion
			foreach ( $data as $promotion ) {
				if ( $this->validate_single_promotion( $promotion ) ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Validate a single promotion data structure
		 *
		 * @param array $promotion Single promotion data.
		 * @return bool
		 */
		private function validate_single_promotion( $promotion ) {
			if ( empty( $promotion ) || ! is_array( $promotion ) ) {
				return false;
			}

			// We need filter and buttons data
			if ( empty( $promotion['filter'] ) ) {
				return false;
			}

			if ( empty( $promotion['buttons'] ) || ! is_array( $promotion['buttons'] ) ) {
				return false;
			}

			return true;
		}

		/**
		 * Override upsell buttons with promotion data
		 *
		 * @param string $buttons Original buttons HTML.
		 * @param string $context The upsell context/location.
		 * @return string Modified buttons HTML.
		 */
		public function override_upsell_buttons( $buttons, $context = '' ) {
			// Get current filter being executed
			$current_filter = current_filter();

			// Find the promotion for this filter
			if ( ! isset( $this->active_promotions[ $current_filter ] ) ) {
				return $buttons;
			}

			$promotion = $this->active_promotions[ $current_filter ];

			if ( empty( $promotion['buttons'] ) ) {
				return $buttons;
			}

			// Extract original URLs from the buttons
			preg_match_all( '~<a(.*?)href="([^"]+)"(.*?)>~', $buttons, $matches );
			$original_urls = isset( $matches[2] ) ? $matches[2] : array();

			$new_buttons  = '';
			$button_index = 0;

			foreach ( $promotion['buttons'] as $button ) {
				if ( empty( $button['text'] ) ) {
					continue;
				}

				// Determine the URL
				$url = '';
				if ( isset( $button['url'] ) ) {
					if ( 'use_original' === $button['url'] && isset( $original_urls[ $button_index ] ) ) {
						$url = $original_urls[ $button_index ];
					} else {
						$url = $button['url'];
					}
				} elseif ( isset( $original_urls[ $button_index ] ) ) {
					$url = $original_urls[ $button_index ];
				}

				// Build button attributes
				$target = isset( $button['target'] ) ? $button['target'] : '_blank';
				$class  = isset( $button['class'] ) ? $button['class'] : 'button';
				$style  = isset( $button['style'] ) ? ' style="' . esc_attr( $button['style'] ) . '"' : '';

				$new_buttons .= sprintf(
					'<a target="%s" href="%s" class="%s"%s>%s</a>',
					esc_attr( $target ),
					esc_url( $url ),
					esc_attr( $class ),
					$style,
					esc_html( $button['text'] )
				);

				++$button_index;
			}

			return $new_buttons;
		}

		/**
		 * Output promotion CSS styles from all active promotions
		 */
		public function output_promotion_styles() {
			if ( empty( $this->active_promotions ) ) {
				return;
			}

			$css = '';

			foreach ( $this->active_promotions as $promotion ) {
				if ( ! empty( $promotion['css'] ) ) {
					$css .= $promotion['css'] . "\n";
				}
			}

			if ( ! empty( $css ) ) {
				echo '<style>' . wp_strip_all_tags( $css ) . '</style>';
			}
		}

		/**
		 * Clear stored promotions data
		 */
		public function clear_promotions() {
			delete_option( $this->option_name );
			delete_transient( $this->cache_transient );
			$this->active_promotions = array();
		}

		/**
		 * Get current active promotions data
		 *
		 * @return array
		 */
		public function get_active_promotions() {
			return $this->active_promotions;
		}

		/**
		 * Get promotion for a specific filter
		 *
		 * @param string $filter Filter hook name.
		 * @return array|false
		 */
		public function get_promotion_for_filter( $filter ) {
			return isset( $this->active_promotions[ $filter ] ) ? $this->active_promotions[ $filter ] : false;
		}

		/**
		 * Check if there are any active promotions
		 *
		 * @return bool
		 */
		public function has_active_promotions() {
			return ! empty( $this->active_promotions );
		}

		/**
		 * Manually set API URL
		 *
		 * @param string $url API URL.
		 */
		public function set_api_url( $url ) {
			$this->api_url = $url;
		}

		/**
		 * Run initial promotions check on plugin activation
		 */
		public function activate() {
			$this->fetch_remote_upsells();
		}

		/**
		 * Clean up on plugin deactivation
		 */
		public function deactivate() {
			wp_clear_scheduled_hook( $this->cron_hook );
		}

		/**
		 * Get transients to be cleared on uninstall
		 *
		 * @param array $transients Existing transients array.
		 * @return array
		 */
		public function get_transients_to_clear( $transients ) {
			$transients[] = $this->cache_transient;
			return $transients;
		}
	}
	// Initiate WPChill Upsells (remote promotions)
	WPChill_Remote_Upsells::get_instance(
		array(
			'api_url' => 'https://wp-modula.com/wp-json/upsells/v1/get',
		)
	);
}
