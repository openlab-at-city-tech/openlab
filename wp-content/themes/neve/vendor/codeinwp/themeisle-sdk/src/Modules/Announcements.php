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

use ThemeisleSDK\Common\Abstract_Module;
use ThemeisleSDK\Loader;
use ThemeisleSDK\Product;

/**
 * Announcement module for the ThemeIsle SDK.
 */
class Announcements extends Abstract_Module {

	/**
	 * Holds the timeline for the announcements.
	 *
	 * @var array
	 */
	private static $timeline = array(
		'black_friday' => array(
			'start'    => '2024-11-25 00:00:00',
			'end'      => '2024-12-03 23:59:59',
			'rendered' => false,
		),
	);

	/**
	 * Holds the option prefix for the announcements.
	 *
	 * This is used to store the dismiss date for each announcement.
	 *
	 * @var string
	 */
	public $option_prefix = 'themeisle_sdk_announcement_';

	/**
	 * Holds the time for the current request.
	 *
	 * @var string
	 */
	public $time = '';

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
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$this->product = $product;

		add_action( 'admin_init', array( $this, 'load_announcements' ) );
		add_filter( 'themeisle_sdk_active_announcements', array( $this, 'get_active_announcements' ) );
		add_filter( 'themeisle_sdk_announcements', array( $this, 'get_announcements_for_plugins' ) );
	}

	/**
	 * Load all valid announcements.
	 *
	 * @return void
	 */
	public function load_announcements() {
		$active = $this->get_active_announcements();

		if ( empty( $active ) ) {
			return;
		}

		foreach ( $active as $announcement ) {

			$method = $announcement . '_notice_render';

			if ( method_exists( $this, $method ) ) {
				add_action( 'admin_notices', array( $this, $method ) );
			}
		}

		// Load the ajax handler.
		add_action( 'wp_ajax_themeisle_sdk_dismiss_announcement', array( $this, 'disable_notification_ajax' ) );
	}

	/**
	 * Get all active announcements.
	 *
	 * @return array List of active announcements.
	 */
	public function get_active_announcements() {
		$active = array();

		foreach ( self::$timeline as $announcement_slug => $dates ) {
			if ( $this->is_active( $dates ) && $this->can_show( $announcement_slug, $dates ) ) {
				$active[] = $announcement_slug;
			}
		}

		return $active;
	}

	/**
	 * Get all announcements along with plugin specific data.
	 *
	 * @return array List of announcements.
	 */
	public function get_announcements_for_plugins() {

		$announcements = array();

		foreach ( self::$timeline as $announcement => $dates ) {
			$announcements[ $announcement ] = $dates;

			if ( false !== strpos( $announcement, 'black_friday' ) ) {
				$announcements[ $announcement ]['active'] = $this->is_active( $dates );

				// Dashboard banners URLs.
				$announcements[ $announcement ]['feedzy_dashboard_url'] = tsdk_utmify( 'https://themeisle.com/plugins/feedzy-rss-feeds/blackfriday/', 'bfcm24', 'dashboard' );
				$announcements[ $announcement ]['neve_dashboard_url']   = tsdk_utmify( 'https://themeisle.com/themes/neve/blackfriday/', 'bfcm24', 'dashboard' );
				$announcements[ $announcement ]['otter_dashboard_url']  = tsdk_utmify( 'https://themeisle.com/plugins/otter-blocks/blackfriday/', 'bfcm24', 'dashboard' );

				// Customizer banners URLs.
				$announcements[ $announcement ]['hestia_customizer_url'] = tsdk_utmify( 'https://themeisle.com/black-friday/', 'bfcm24', 'hestiacustomizer' );
				$announcements[ $announcement ]['neve_customizer_url']   = tsdk_utmify( 'https://themeisle.com/black-friday/', 'bfcm24', 'nevecustomizer' );

				// Banners urgency text.
				$remaining_time                                   = $this->get_remaining_time_for_event( $dates['end'] );
				$announcements[ $announcement ]['remaining_time'] = $remaining_time;
				$announcements[ $announcement ]['urgency_text']   = ! empty( $remaining_time ) ? 'Hurry up! Only ' . $remaining_time . ' left.' : '';
			}
		}

		return apply_filters( 'themeisle_sdk_announcements_data', $announcements );
	}

	/**
	 * Get the announcement data.
	 *
	 * @param string $announcement The announcement to get the data for.
	 *
	 * @return array
	 */
	public function get_announcement_data( $announcement ) {
		return ! empty( $announcement ) && is_string( $announcement ) && isset( self::$timeline[ $announcement ] ) ? self::$timeline[ $announcement ] : array();
	}

	/**
	 * Check if the announcement has an active timeline.
	 *
	 * @param array $dates The announcement to check.
	 *
	 * @return bool
	 */
	public function is_active( $dates ) {

		if ( empty( $this->time ) ) {
			$this->time = current_time( 'Y-m-d' );
		}

		$start = isset( $dates['start'] ) ? $dates['start'] : null;
		$end   = isset( $dates['end'] ) ? $dates['end'] : null;

		if ( $start && $end ) {
			return $start <= $this->time && $this->time <= $end;
		} elseif ( $start ) {
			return $this->time >= $start;
		} elseif ( $end ) {
			return $this->time <= $end;
		}

		return false;
	}

	/**
	 * Get the remaining time for the event in a human readable format.
	 *
	 * @param string $end_date The end date for event.
	 * @return string Remaining time for the event.
	 */
	public function get_remaining_time_for_event( $end_date ) {
		if ( empty( $end_date ) || ! is_string( $end_date ) ) {
			return '';
		}

		try {
			$end_date     = new \DateTime( $end_date, new \DateTimeZone( 'GMT' ) );
			$current_date = new \DateTime( 'now', new \DateTimeZone( 'GMT' ) );
			$diff         = $end_date->diff( $current_date );

			if ( $diff->days > 0 ) {
				return $diff->days === 1 ? $diff->format( '%a day' ) : $diff->format( '%a days' );
			}

			if ( $diff->h > 0 ) {
				return $diff->h === 1 ? $diff->format( '%h hour' ) : $diff->format( '%h hours' );
			}

			if ( $diff->i > 0 ) {
				return $diff->i === 1 ? $diff->format( '%i minute' ) : $diff->format( '%i minutes' );
			}

			return $diff->s === 1 ? $diff->format( '%s second' ) : $diff->format( '%s seconds' );
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				error_log( $e->getMessage() ); // phpcs:ignore
			}
		}

		return '';
	}

	/**
	 * Check if the announcement can be shown.
	 *
	 * @param string $announcement_slug The announcement to check.
	 * @param array  $dates The announcement to check.
	 *
	 * @return bool
	 */
	public function can_show( $announcement_slug, $dates ) {
		$dismiss_date = get_option( $this->option_prefix . $announcement_slug, false );

		if ( false === $dismiss_date ) {
			return true;
		}

		// If the start date is after the dismiss date, show the notice.
		$start = isset( $dates['start'] ) ? $dates['start'] : null;
		if ( $start && $dismiss_date < $start ) {
			return true;
		}

		return false;
	}

	/**
	 * Disable the notification via ajax.
	 *
	 * @return void
	 */
	public function disable_notification_ajax() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'dismiss_themeisle_event_notice' ) ) {
			wp_die( 'Invalid nonce! Refresh the page and try again.' );
		}

		if ( ! isset( $_POST['announcement'] ) || ! is_string( $_POST['announcement'] ) ) {
			wp_die( 'Invalid announcement! Refresh the page and try again.' );
		}

		$announcement = sanitize_key( $_POST['announcement'] );

		update_option( $this->option_prefix . $announcement, current_time( 'Y-m-d' ) );
		wp_die( 'success' );
	}

	/**
	 * Render the Black Friday notice.
	 *
	 * @return void
	 */
	public function black_friday_notice_render() {

		// Prevent the notice from being rendered twice.
		if ( self::$timeline['black_friday']['rendered'] ) {
			return;
		}
		self::$timeline['black_friday']['rendered'] = true;

		$product_names = array();

		foreach ( Loader::get_products() as $product ) {
			$slug = $product->get_slug();

			// Do not add if the contains the string 'pro'.
			if ( strpos( $slug, 'pro' ) !== false ) {
				continue;
			}

			$product_names[] = $product->get_name();
		}

		// Randomize the products and get only 4.
		shuffle( $product_names );
		$product_names = array_slice( $product_names, 0, 4 );

		?>
		<style>
			.themeisle-sale {
				display: flex;
			}
		</style>
		<div class="themeisle-sale notice notice-info is-dismissible" data-announcement="black_friday">
			<img src="<?php echo esc_url_raw( $this->get_sdk_uri() . 'assets/images/themeisle-logo.svg' ); ?>" />
			<p>
				<strong>Themeisle Black Friday Sale is Live!</strong> - Enjoy Maximum Savings on <?php echo esc_html( implode( ', ', $product_names ) ); ?>.
				<a href="<?php echo esc_url_raw( tsdk_utmify( 'https://themeisle.com/blackfriday/', 'bfcm24', 'globalnotice' ) ); ?>" target="_blank">Learn more</a>
				<span class="themeisle-sale-error"></span>
			</p>
		</div>
		<script type="text/javascript" data-origin="themeisle-sdk">
			window.document.addEventListener('DOMContentLoaded', () => {
				const observer = new MutationObserver((mutationsList, observer) => {
					for(let mutation of mutationsList) {
						if (mutation.type === 'childList') {
							const container = document.querySelector('.themeisle-sale.notice');
							const button = container?.querySelector('button');
							if ( button ) {
								button.addEventListener('click', e => {
									e.preventDefault();
									fetch('<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>', {
										method: 'POST',
										headers: {
											'Content-Type': 'application/x-www-form-urlencoded'
										},
										body: new URLSearchParams({
											action: 'themeisle_sdk_dismiss_announcement',
											nonce: '<?php echo esc_attr( wp_create_nonce( 'dismiss_themeisle_event_notice' ) ); ?>',
											announcement: container.dataset.announcement
										})
									})
									.then(response => response.text())
									.then(response => {
										if (!response?.includes('success')) {
											document.querySelector('.themeisle-sale-error').innerHTML = response;
											return;
										}

										document.querySelectorAll('.themeisle-sale.notice').forEach(el => {
											el.classList.add('hidden');
											setTimeout(() => {
												el.remove();
											}, 800);
										});
									})
									.catch(error => {
										console.error('Error:', error);
										document.querySelector('.themeisle-sale-error').innerHTML = error;
									});
								});
								observer.disconnect();
								break;
							}
						}
					}
				});

				observer.observe(document.body, { childList: true, subtree: true });
			});
		</script>
		<?php
	}
}
