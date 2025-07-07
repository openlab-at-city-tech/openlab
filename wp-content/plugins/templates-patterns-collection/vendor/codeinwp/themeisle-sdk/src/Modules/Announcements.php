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

	/**
	 * Mark if the notice was already loaded.
	 *
	 * @var boolean
	 */
	private static $notice_loaded = false;

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
		
		add_action( 'admin_init', array( $this, 'load_announcements' ) );
	}

	/**
	 * Load all valid announcements.
	 *
	 * @return void
	 */
	public function load_announcements() {
		if ( ! $this->is_black_friday_sale( $this->get_current_date() ) ) {
			return;
		}
		
		add_action( 'admin_notices', array( $this, 'black_friday_notice_render' ) );
		add_action( 'wp_ajax_themeisle_sdk_dismiss_black_friday_notice', array( $this, 'disable_notification_ajax' ) );
		add_action(
			'themeisle_internal_page',
			function( $plugin, $page_slug ) {
				self::$current_product = $plugin;
			},
			10,
			2 
		);
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
		
		$sale_title   = Loader::$labels['announcements']['black_friday'];
		$sale_url     = tsdk_translate_link( tsdk_utmify( 'https://themeisle.com/blackfriday/', 'bfcm25', $utm_location ) );
		$sale_message = sprintf( Loader::$labels['announcements']['max_savings'], '50%' );

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
		
		$all_configs = apply_filters( 'themeisle_sdk_blackfriday_data', array( 'default' => $this->get_notice_data() ) );

		if ( empty( $all_configs ) ) {
			return;
		}

		$data = end( $all_configs );
		
		if ( ! empty( self::$current_product ) && isset( $all_configs[ self::$current_product ] ) ) {
			$data = $all_configs[ self::$current_product ];
		}

		if ( empty( $data ) || ! is_array( $data ) ) {
			return;
		}
		
		$current_user_id = get_current_user_id();
		$can_dismiss     = true;

		if ( ! empty( $data['dismiss'] ) ) {
			$can_dismiss = $data['dismiss'];
		} else {
			// Disable by default if we are on a product page.
			if ( 0 < did_action( 'themeisle_internal_page' ) ) {
				$can_dismiss = false;
			}
		}

		if ( $can_dismiss && ! $this->can_show_notice( $this->get_current_date(), $current_user_id ) ) {
			return;
		}

		$sale_url           = ! empty( $data['sale_url'] ) ? $data['sale_url'] : '';
		$hide_other_notices = ! empty( $data['hide_other_notices'] ) ? $data['hide_other_notices'] : ! $can_dismiss;
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
						src="<?php echo esc_url( $this->get_sdk_uri() . 'assets/images/themeisle-logo.png' ); ?>"
					/>
				</div>
				<div class="themeisle-sale-content">
					<h4 class="themeisle-sale-title">
						<?php echo esc_html( $data['title'] ); ?>
						<span class="themeisle-sale-time-left">
							<?php echo esc_html( $data['time_left'] ); ?>
						</span>
					</h4>
					<p>
						<?php echo wp_kses_post( $data['message'] ); ?>
					</p>
				</div>
				<div class="themeisle-sale-action">
					<a
						href="<?php echo esc_url( $sale_url ); ?>"
						target="_blank"
						class="button button-primary themeisle-sale-button"
					>
					<?php echo esc_html( Loader::$labels['announcements']['notice_link_label'] ); ?>
					</a>
				</div>
				<?php if ( $can_dismiss ) : ?>
				<a href="<?php echo esc_url( $dismiss_notice_url ); ?>" class="themeisle-sale-dismiss">
					<span class="dashicons dashicons-dismiss"></span>
				</a>
				<?php endif; ?>
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
}
