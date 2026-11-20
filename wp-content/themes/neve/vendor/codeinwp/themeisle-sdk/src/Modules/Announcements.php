<?php
/**
 * File responsible for announcements.
 *
 * This is used to display information about limited events, such as Black Friday.
 *
 * @package     ThemeIsleSDK
 * @subpackage  Modules
 * @copyright   Copyright (c) 2017, Marius Cristea
 * @license     http://opensource.org/licenses/gpl-3.0.php GNU Public License
 * @since       3.3.0
 */

namespace ThemeisleSDK\Modules;

use DateTime;
use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Loader;
use ThemeisleSDK\Product;

/**
 * Announcement module for the ThemeIsle SDK.
 */
class Announcements extends Abstract_Module {

	const SALE_DURATION_BLACK_FRIDAY = '+7 days'; // DateTime modifier. (Include Cyber Monday)
	const MINIMUM_INSTALL_AGE        = 3 * DAY_IN_SECONDS;

	/**
	 * Mark if the notice was already loaded.
	 *
	 * @var boolean
	 */
	private static $notice_loaded = false;

	/**
	 * Mark if the plugin meta link was already loaded.
	 *
	 * @var boolean
	 */
	private static $meta_link_loaded = false;

	/**
	 * The product to be used.
	 *
	 * @var string
	 */
	private static $current_product = '';

	/**
	 * Check if the module can be loaded.
	 *
	 * @param Product $product Product data.
	 *
	 * @return bool
	 */
	public function can_load( $product ) {
		if ( $this->is_from_partner( $product ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Load the module for the selected product.
	 *
	 * @param Product $product Product data.
	 *
	 * @return void
	 */
	public function load( $product ) {
		$this->product = $product;

		add_filter(
			'themeisle_sdk_is_black_friday_sale',
			function( $is_black_friday ) {
				return $this->is_black_friday_sale( $this->get_current_date() );
			}
		);

		add_action( 'admin_menu', array( $this, 'load_announcements' ), 9 );
		add_action( 'wp_ajax_themeisle_sdk_dismiss_black_friday_notice', array( $this, 'disable_notification_ajax' ) );
	}

	/**
	 * Load all valid announcements.
	 *
	 * @return void
	 */
	public function load_announcements() {
		$current_date = $this->get_current_date();
		if ( ! $this->is_black_friday_sale( $current_date ) ) {
			return;
		}

		if ( self::MINIMUM_INSTALL_AGE > ( $current_date->getTimestamp() - $this->product->get_install_time() ) ) {
			return;
		}

		add_action( 'admin_notices', array( $this, 'black_friday_notice_render' ) );

		add_action(
			'themeisle_internal_page',
			function( $plugin, $page_slug ) {
				self::$current_product = $plugin;
			},
			10,
			2
		);

		add_filter( 'plugin_row_meta', array( $this, 'add_plugin_meta_links' ), 10, 2 );
		add_filter( $this->product->get_key() . '_about_us_metadata', array( $this, 'override_about_us_metadata' ), 100 );
	}

	/**
	 * Get the remaining time for the event in a human-readable format.
	 *
	 * @param DateTime $end_date The end date for event.
	 *
	 * @return string Remaining time for the event.
	 */
	public function get_remaining_time_for_event( $end_date ) {
		return human_time_diff( $this->get_current_date()->getTimestamp(), $end_date->getTimestamp() );
	}

	/**
	 * Check if the announcement can be shown.
	 *
	 * @param DateTime $current_date The announcement to check.
	 * @param int      $user_id The user id to show the notice.
	 *
	 * @return bool
	 */
	public function can_show_notice( $current_date, $user_id ) {
		$current_year                  = $current_date->format( 'Y' );
		$user_notice_dismiss_timestamp = get_user_meta( $user_id, 'themeisle_sdk_dismissed_notice_black_friday', true );

		if ( empty( $user_notice_dismiss_timestamp ) ) {
			return true;
		}

		$dismissed_year = wp_date( 'Y', $user_notice_dismiss_timestamp );

		return $current_year !== $dismissed_year;
	}

	/**
	 * Calculate the start date for Black Friday based on the year of the given date.
	 *
	 * Black Friday is the day after the Thanksgiving and the sale starts on the Monday of that week.
	 *
	 * @param DateTime $date The current date object, used to determine the year.
	 * @return DateTime The start date of Black Friday for the given year.
	 */
	public function get_start_date( $date ) {
		$year         = $date->format( 'Y' );
		$black_friday = new DateTime( "last friday of november {$year}" );

		$sale_start = clone $black_friday;
		$sale_start->modify( 'monday this week' );
		$sale_start->setTime( 0, 0 );

		return $sale_start;
	}

	/**
	 * Calculate the event end date.
	 *
	 * @param DateTime $start_date The start date.
	 * @return DateTime The end date.
	 */
	public function get_end_date( $start_date ) {
		$black_friday_end = clone $start_date;
		$black_friday_end->modify( self::SALE_DURATION_BLACK_FRIDAY );
		$black_friday_end->setTime( 23, 59, 59 );
		return $black_friday_end;
	}

	/**
	 * Check if the current date falls within the Black Friday sale period.
	 *
	 * @param DateTime $current_date The date to check.
	 * @return bool True if the date is within the Black Friday sale period, false otherwise.
	 */
	public function is_black_friday_sale( $current_date ) {
		$black_friday_start_date = $this->get_start_date( $current_date );
		$black_friday_end        = $this->get_end_date( $black_friday_start_date );
		return $black_friday_start_date <= $current_date && $current_date <= $black_friday_end;
	}

	/**
	 * Get the notice data.
	 *
	 * @return array The notice data.
	 */
	public function get_notice_data() {
		$time_left_label = $this->get_remaining_time_for_event( $this->get_end_date( $this->get_start_date( $this->get_current_date() ) ) );
		$time_left_label = sprintf( Loader::$labels['announcements']['time_left'], $time_left_label );

		$utm_location = 'globalnotice';
		if ( ! empty( $this->product ) ) {
			$utm_location = $this->product->get_friendly_name();
		}

		$sale_title = Loader::$labels['announcements']['black_friday'];
		$sale_url   = tsdk_translate_link( tsdk_utmify( 'https://themeisle.com/blackfriday/', 'bfcm26', $utm_location ) );

		$current_year = $this->get_current_date()->format( 'Y' );
		$sale_message = sprintf( Loader::$labels['announcements']['max_savings'], $current_year );

		return array(
			'title'     => $sale_title,
			'sale_url'  => $sale_url,
			'message'   => $sale_message,
			'time_left' => $time_left_label,
		);
	}

	/**
	 * Render the Black Friday notice.
	 *
	 * @return void
	 */
	public function black_friday_notice_render() {

		// Prevent the notice from being rendered twice.
		if ( self::$notice_loaded ) {
			return;
		}
		self::$notice_loaded = true;

		$current_user_id = get_current_user_id();

		if ( ! $this->can_show_notice( $this->get_current_date(), $current_user_id ) ) {
			return;
		}

		$all_configs = apply_filters( 'themeisle_sdk_blackfriday_data', array( 'default' => $this->get_notice_data() ) );

		if ( empty( $all_configs ) || ! is_array( $all_configs ) ) {
			return;
		}

		$data         = isset( $all_configs['default'] ) ? $all_configs['default'] : $this->get_notice_data();
		$products     = Loader::get_products();
		$current_time = $this->get_current_date()->getTimestamp();
		$can_show     = false;

		// Check if we have products that are eligible to show the notice with the default data. If the product provide its own config, use it.
		foreach ( $products as $product ) {
			$slug = $product->get_slug();

			if ( self::MINIMUM_INSTALL_AGE < ( $current_time - $product->get_install_time() ) ) {
				$can_show = true;

				if ( isset( $all_configs[ $slug ] ) && ! empty( $all_configs[ $slug ] ) && is_array( $all_configs[ $slug ] ) ) {
					$data = $all_configs[ $slug ];

					if ( self::$current_product === $slug ) {
						$data = $all_configs[ $slug ];
						break;
					}
				}
			}
		}

		if ( ! $can_show ) {
			return;
		}

		$displayed_on_internal_page = 0 < did_action( 'themeisle_internal_page' );

		$title              = ! empty( $data['title'] ) ? $data['title'] : Loader::$labels['announcements']['black_friday'];
		$time_left_label    = ! empty( $data['time_left'] ) ? $data['time_left'] : '';
		$message            = ! empty( $data['message'] ) ? $data['message'] : '';
		$logo_url           = ! empty( $data['logo_url'] ) ? $data['logo_url'] : $this->get_sdk_uri() . 'assets/images/themeisle-logo.png';
		$cta_label          = ! empty( $data['cta_label'] ) ? $data['cta_label'] : Loader::$labels['announcements']['notice_link_label'];
		$sale_url           = ! empty( $data['sale_url'] ) ? $data['sale_url'] : '';
		$hide_other_notices = ! empty( $data['hide_other_notices'] ) ? $data['hide_other_notices'] : $displayed_on_internal_page;
		$dismiss_notice_url = wp_nonce_url(
			add_query_arg(
				array( 'action' => 'themeisle_sdk_dismiss_black_friday_notice' ),
				admin_url( 'admin-ajax.php' )
			),
			'dismiss_themeisle_event_notice'
		);

		if ( empty( $sale_url ) ) {
			return;
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			$sale_url = remove_query_arg( 'lkey', $sale_url );
		}

		?>
		<style>
			.themeisle-sale {
				border-left-color: #0466CB;
			}
			.themeisle-sale :is(.themeisle-sale-title, p) {
				margin: 0;
			}
			.themeisle-sale-container {
				display: flex;
				align-items: center;
				padding: 0.5rem 0;
				gap: 0.5rem;
				padding-right: 10px;
			}
			.themeisle-sale-content {
				display: flex;
				flex-direction: column;
				gap: 0.2rem;
			}
			.themeisle-sale a {
				text-decoration: none;
			}
			.themeisle-sale p a {
				margin-left: 1rem;
				padding: 7px 12px;
				border-radius: 4px;
				background: #0466CB;
				color: white;
				font-weight: 700;
			}
			.themeisle-sale-dismiss {
				padding-top: 5px;
			}
			.themeisle-sale-dismiss span {
				color: #787c82;
				font-size: 16px;
			}
			.notice.themeisle-sale {
				padding: 0;
			}
			.themeisle-sale-logo {
				display: flex;
				justify-content: center;
				align-items: center;
				margin-left: 5px;
			}
			.themeisle-sale-time-left {
				margin-left: 5px;
				padding: 3px 5px;
				border-radius: 4px;
				background-color: #dfdfdf;
				font-weight: 600;
				font-size: x-small;
				line-height: 1;
			}
			.themeisle-sale-title {
				font-size: 14px;
				display: flex;
				align-items: center;
			}
			.themeisle-sale-action {
				flex-grow: 1;
				display: flex;
				justify-content: flex-end;
			}
			<?php if ( $hide_other_notices ) : ?>
				.notice:not(.themeisle-sale) {
					display: none;
				}
			<?php endif; ?>
		</style>
		<div class="themeisle-sale notice notice-info" data-event-slug="black_friday">
			<div class="themeisle-sale-container">
				<div class="themeisle-sale-logo">
					<img
						width="45"
						src="<?php echo esc_url( $logo_url ); ?>"
					/>
				</div>
				<div class="themeisle-sale-content">
					<h4 class="themeisle-sale-title">
						<?php echo esc_html( $title ); ?>
						<span class="themeisle-sale-time-left">
							<?php echo esc_html( $time_left_label ); ?>
						</span>
					</h4>
					<p>
						<?php echo wp_kses_post( $message ); ?>
					</p>
				</div>
				<div class="themeisle-sale-action">
					<a
						href="<?php echo esc_url( $sale_url ); ?>"
						target="_blank"
						class="button button-primary themeisle-sale-button"
					>
					<?php echo esc_html( $cta_label ); ?>
					</a>
				</div>
				<a href="<?php echo esc_url( $dismiss_notice_url ); ?>" class="themeisle-sale-dismiss">
					<span class="dashicons dashicons-dismiss"></span>
				</a>
			</div>
		</div>
		<script>
			// Note: Some plugins use React and the content is ready after the `DOMContentLoaded` event. Use this function to reposition the notice after components have rendered.
			window.tsdk_reposition_notice = function() {
				const bannerRoot = document.getElementById('tsdk_banner');
				const saleNotice = document.querySelector('.themeisle-sale');
				if ( ! bannerRoot || ! saleNotice ) {
					return;
				}

				bannerRoot.appendChild(saleNotice);
			};

			document.addEventListener( 'DOMContentLoaded', function() {
				window.tsdk_reposition_notice();
			} );
		</script>
		<?php
	}

	/**
	 * Disable the notification via ajax.
	 *
	 * @return void
	 */
	public function disable_notification_ajax() {
		check_ajax_referer( 'dismiss_themeisle_event_notice' );

		update_user_meta( get_current_user_id(), 'themeisle_sdk_dismissed_notice_black_friday', $this->get_current_date()->getTimestamp() );

		$return_page_url = wp_get_referer();
		if ( empty( $return_page_url ) ) {
			$return_page_url = admin_url();
		}

		wp_safe_redirect( $return_page_url );
		exit;
	}

	/**
	 * Add the plugin meta links.
	 *
	 * @param array<string, string> $links The plugin meta links.
	 * @param string                $plugin_file The plugin file.
	 * @return array<string, string> The plugin meta links.
	 */
	public function add_plugin_meta_links( $links, $plugin_file ) {
		if ( self::$meta_link_loaded ) {
			return $links;
		}

		if ( $plugin_file !== plugin_basename( $this->product->get_basefile() ) ) {
			return $links;
		}

		$configs = apply_filters( 'themeisle_sdk_blackfriday_data', array( 'default' => $this->get_notice_data() ) );

		if ( empty( $configs ) || ! is_array( $configs ) ) {
			return $links;
		}

		$current_slug = $this->product->get_slug();
		$data         = isset( $configs[ $current_slug ] ) && ! empty( $configs[ $current_slug ] ) && is_array( $configs[ $current_slug ] ) ? $configs[ $current_slug ] : array();

		$plugin_meta_message = '';
		$plugin_meta_url     = '';

		if ( isset( $data['plugin_meta_targets'] ) && ! empty( $data['plugin_meta_targets'] ) && ! in_array( $current_slug, $data['plugin_meta_targets'] ) ) {
			return $links; // The current configuration is for another plugins.
		}

		$plugin_meta_message = ! empty( $data['plugin_meta_message'] ) ? $data['plugin_meta_message'] : '';
		$plugin_meta_url     = ! empty( $data['sale_url'] ) ? $data['sale_url'] : '';

		if ( empty( $plugin_meta_url ) || empty( $plugin_meta_message ) ) {

			// Check if a configuration is in another plugin.
			$products = Loader::get_products();
			foreach ( $products as $product ) {
				$slug = $product->get_slug();

				if ( $slug === $current_slug || ! isset( $configs[ $slug ] ) || empty( $configs[ $slug ] ) || ! is_array( $configs[ $slug ] ) ) {
					continue;
				}

				if ( ! empty( $configs[ $slug ]['plugin_meta_targets'] ) && in_array( $current_slug, $configs[ $slug ]['plugin_meta_targets'] ) ) {
					$plugin_meta_message = ! empty( $configs[ $slug ]['plugin_meta_message'] ) ? $configs[ $slug ]['plugin_meta_message'] : '';
					$plugin_meta_url     = ! empty( $configs[ $slug ]['sale_url'] ) ? $configs[ $slug ]['sale_url'] : '';
					break;
				}
			}
		}

		if ( empty( $plugin_meta_url ) || empty( $plugin_meta_message ) ) {
			return $links;
		}

		$links[] = sprintf( '<a class="themeisle-sale-plugin-meta-link" style="color: red;" href="%s" target="_blank">%s</a>', esc_url( $plugin_meta_url ), esc_html( $plugin_meta_message ) );

		self::$meta_link_loaded = true;

		return $links;
	}

	/**
	 * Override the About Us upgrade menu during Black Friday.
	 *
	 * Registered dynamically during admin_menu when sale is active.
	 * Only applies if About_Us module is loaded for the product.
	 *
	 * @param array<string, mixed> $about_data About Us metadata.
	 *
	 * @return array<string, mixed>
	 */
	public function override_about_us_metadata( $about_data ) {
		if ( ! $this->is_black_friday_sale( $this->get_current_date() ) ) {
			return $about_data;
		}

		if ( empty( $about_data ) || ! is_array( $about_data ) ) {
			return $about_data;
		}

		if ( empty( $about_data['has_upgrade_menu'] ) || true !== $about_data['has_upgrade_menu'] ) {
			return $about_data;
		}

		$configs = apply_filters( 'themeisle_sdk_blackfriday_data', array( 'default' => $this->get_notice_data() ) );

		$current_slug = $this->product->get_slug();
		if ( ! isset( $configs[ $current_slug ] ) || empty( $configs[ $current_slug ] ) || ! is_array( $configs[ $current_slug ] ) ) {
			return $about_data;
		}

		$config = $configs[ $current_slug ];

		if ( empty( $config['upgrade_menu_text'] ) || empty( $config['sale_url'] ) ) {
			return $about_data;
		}

		$about_data['upgrade_text'] = $config['upgrade_menu_text'];
		$about_data['upgrade_link'] = $config['sale_url'];

		return $about_data;
	}
}
