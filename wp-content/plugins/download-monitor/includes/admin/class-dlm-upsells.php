<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

/**
 * Class DLM_Upsells
 *
 * @since 4.4.5
 */
class DLM_Upsells {

	/**
	 * Holds the class object.
	 *
	 * @since 4.4.5
	 *
	 * @var object
	 */
	public static $instance;

	private $upsell_tabs = array();

	/**
	 * DLM_Upsells constructor.
	 *
	 * @since 4.4.5
	 */
	public function __construct() {

		// Add modal upsells through sub menu. Place here to run everywhere, not just on DLM pages.
		add_action( 'admin_menu', array( $this, 'add_upsell_modals' ), 13 );

		// Add Lite VS Pro page
		add_filter( 'dlm_admin_menu_links', array( $this, 'add_lite_vs_pro_page' ), 120 );
		add_action( 'admin_print_footer_scripts', array( $this, 'inline_script_for_redirection' ) );

		if ( ! DLM_Admin_Helper::is_dlm_admin_page() ) {
			return;
		}

		add_action( 'init', array( $this, 'upsells_init' ) );

		// Upgrade to PRO plugin action link
		add_filter( 'plugin_action_links_' . DLM_FILE, array( $this, 'filter_action_links' ), 60 );

		add_action( 'admin_enqueue_scripts', array( $this, 'enhanced_metrics_upsells_script' ) );
	}

	/**
	 * Returns the singleton instance of the class.
	 *
	 * @return object The DLM_Upsells object.
	 *
	 * @since 4.4.5
	 */
	public static function get_instance() {

		if ( ! isset( self::$instance ) && ! ( self::$instance instanceof DLM_Upsells ) ) {
			self::$instance = new DLM_Upsells();
		}

		return self::$instance;
	}

	public function upsells_init() {
		$this->set_hooks();

		$this->set_tabs();
	}

	/**
	 * Set our hooks
	 *
	 * @since 4.4.5
	 */
	public function set_hooks() {

		add_filter( 'dlm_download_metaboxes', array( $this, 'add_meta_boxes' ), 30 );

		add_filter( 'dlm_settings', array( $this, 'pro_tab_upsells' ), 99, 1 );

		add_action( 'dlm_reports_page_end', array( $this, 'insights_upsell' ), 99 );
	}


	/**
	 * Generate the all-purpose upsell box
	 *
	 * @param        $title
	 * @param        $description
	 * @param        $tab
	 * @param        $extension
	 * @param null   $utm_source
	 * @param array  $features
	 * @param string $utm_source
	 * @param string $icon
	 *
	 * @return string
	 *
	 * @since 4.4.5
	 */
	public function generate_upsell_box( $title, $description, $tab, $extension, $features = array(), $utm_source = null, $icon = false ) {

		echo '<div class="wpchill-upsell">';
		if ( $icon ) {
			echo '<img src="' . esc_url( DLM_URL . 'assets/images/upsells/' . $icon ) . '">';
		}

		if ( ! empty( $title ) ) {
			echo '<h2>' . esc_html( $title ) . '</h2>';
		}

		if ( ! empty( $features ) ) {
			echo '<ul class="wpchill-upsell-features">';

			foreach ( $features as $feature ) {
				echo '<li>';
				if ( isset( $feature['tooltip'] ) && '' != $feature['tooltip'] ) {
					echo '<div class="wpchill-tooltip"><span>[?]</span>';
					echo '<div class="wpchill-tooltip-content">' . esc_html( $feature['tooltip'] ) . '</div>';
					echo '</div>';
					echo '<p>' . esc_html( $feature['feature'] ) . '</p>';
				} else {
					echo esc_html( $feature['feature'] );
				}

				echo '</li>';
			}
			echo '</ul>';
		}
		if ( ! empty( $description ) ) {
			echo '<p class="wpchill-upsell-description">' . esc_html( $description ) . '</p>';
		}

		echo '<a target="_blank" href="https://www.download-monitor.com/pricing/?utm_source=' . ( ! empty( $extension ) ? esc_html( $extension ) . '_metabox' : '' ) . '&utm_medium=lite-vs-pro&utm_campaign=' . ( ! empty( $extension ) ? esc_html( str_replace( ' ', '_', $extension ) ) : '' ) . '"><div class="dlm-available-with-pro"><span class="dashicons dashicons-lock"></span><span>' . esc_html__( 'AVAILABLE WITH PREMIUM', 'download-monitor' ) . '</span></div></a>';
		$buttons  = '<a target="_blank" href="https://download-monitor.com/free-vs-pro/?utm_source=dlm-lite&utm_medium=link&utm_campaign=upsell&utm_term=lite-vs-pro" class="button">' . esc_html__( 'Free vs Premium', 'download-monitor' ) . '</a>';
		$buttons .= '<a target="_blank" href="https://www.download-monitor.com/pricing/?utm_source=' . ( ! empty( $extension ) ? esc_html( $extension ) . '_metabox' : '' ) . '&utm_medium=lite-vs-pro&utm_campaign=' . ( ! empty( $extension ) ? esc_html( str_replace( ' ', '_', $extension ) ) : '' ) . '" class="button-primary button">' . esc_html__( 'Get Premium', 'download-monitor' ) . '</a>';

		$buttons = apply_filters( 'dlm_upsell_buttons', $buttons, $extension );

		echo '<div class="wpchill-upsell-buttons-wrap">';
		echo $buttons; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Filtered HTML, escaped in the default output above.
		echo '</div>';
		echo '</div>';
	}

	/**
	 * Add upsell metaboxes
	 *
	 * @since 4.4.5
	 */
	public function add_meta_boxes( $meta_boxes ) {

		if ( ! $this->check_extension( 'dlm-downloading-page' ) ) {
			$meta_boxes[] = array(
				'id'       => 'dlm-download-page-upsell',
				'title'    => esc_html__( 'Downloading page', 'download-monitor' ),
				'callback' => array( $this, 'output_download_page_upsell' ),
				'screen'   => 'dlm_download',
				'context'  => 'side',
				'priority' => 30,
			);
		}

		if ( ! $this->check_extension( 'dlm-amazon-s3' ) || ! $this->check_extension( 'dlm-google-drive' ) ) {
			$meta_boxes[] = array(
				'id'       => 'dlm-external-hosting',
				'title'    => esc_html__( 'External Hosting', 'download-monitor' ),
				'callback' => array( $this, 'output_external_hosting_upsell' ),
				'screen'   => 'dlm_download',
				'context'  => 'normal',
				'priority' => 10,
			);
		}

		return $meta_boxes;
	}

	/**
	 * @return DLM_Pro\Extensions\Extensions|DLM_Extensions_Base
	 */
	private static function get_extensions_provider() {
		return class_exists( 'DLM_Pro\Extensions\Extensions' )
			? DLM_Pro\Extensions\Extensions::get_instance()
			: DLM_Extensions_Base::get_instance();
	}

	/**
	 * Whether the given addon is already owned by the current plan — used to
	 * decide whether to show its upsell.
	 *
	 * @param string $extension
	 *
	 * @return bool
	 *
	 * @since 4.4.5
	 */
	public function check_extension( $extension ) {
		return ! self::get_extensions_provider()->is_upgradable_addon( $extension );
	}

	/**
	 * Set DLM's upsell tabs
	 *
	 * @since 4.4.5
	 */
	public function set_tabs() {
		// Define our upsell tabs
		// First is the tab and then are the sections
		$this->upsell_tabs = apply_filters(
			'dlm_upsell_tabs',
			array(
				'lead_generation'  => array(
					'title'    => esc_html__( 'Content Locking', 'download-monitor' ),
					'upsell'   => true,
					'sections' => array(
						'ninja_forms'   => array(
							'title'    => __( 'Ninja Forms', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'gravity_forms' => array(
							'title'    => __( 'Gravity Forms', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
							'upsell'   => true,
							'badge'    => true,
						),
						'email_lock'    => array(
							'title'    => __( 'Email lock', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'cf7_lock'      => array(
							'title'    => __( 'Contact Form 7', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'wpforms_lock'  => array(
							'title'    => __( 'WP Forms', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
					),
				),
				'external_hosting' => array(
					'title'    => esc_html__( 'External hosting', 'download-monitor' ),
					'sections' => array(
						'amazon_s3'    => array(
							'title'    => __( 'Amazon S3', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'google_drive' => array(
							'title'    => __( 'Google Drive', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
					),
				),
				'advanced'         => array(
					'title'    => esc_html__( 'Advanced', 'download-monitor' ),
					'sections' => array(
						'page_addon'       => array(
							'title'    => __( 'Document Library Manager (Page Addon)', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'downloading_page' => array(
							'title'    => __( 'Downloading Page', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
						'captcha'          => array(
							'title'    => __( 'Captcha', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
					),
				),
				'integration'      => array(
					'title'    => esc_html__( 'Integration', 'download-monitor' ),
					'sections' => array(
						'captcha' => array(
							'title'    => __( 'Captcha', 'download-monitor' ),
							'sections' => array(),
							// Need to put sections here for backwards compatibility
						),
					),
				),
			)
		);
	}

	/**
	 * Add PRO Tabs upsells
	 *
	 * @param $settings
	 *
	 * @return mixed
	 *
	 * @since 4.4.5
	 */
	public function pro_tab_upsells( $settings ) {

		foreach ( $this->upsell_tabs as $key => $tab ) {
			if ( ! isset( $settings[ $key ] ) ) {
				if ( ! isset( $settings[ $key ]['title'] ) ) {
					$settings[ $key ]['title'] = $tab['title'];
				}

				foreach ( $tab['sections'] as $section_key => $section ) {
					if ( ! isset( $settings[ $key ]['sections'][ $section_key ] ) ) {
						$settings[ $key ]['sections'][ $section_key ]           = $section;
						$settings[ $key ]['sections'][ $section_key ]['upsell'] = true;
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Output the DLM Downloading Page extension upsell
	 *
	 * @since 4.4.5
	 */
	public function output_download_page_upsell() {

		if ( ! $this->check_extension( 'dlm-downloading-page' ) ) {
			$this->generate_upsell_box(
				'',
				__( 'Customize the downloading page by adding banners, ads, and anything you like.', 'download-monitor' ),
				'downloading_page',
				'downloading-page'
			);
		}
	}

	/**
	 * Output the Downloadable Files locations in the Downloadable files metabox
	 *
	 * @param $download
	 *
	 * @since 4.4.5
	 */
	public function output_external_hosting_upsell() {
		echo '<div class="upsells-columns">';

		if ( ! $this->check_extension( 'dlm-amazon-s3' ) ) {
			echo '<div class="upsells-column"><span class="dashicons dashicons-amazon"></span>';
			echo '<h3>' . esc_html__( 'Amazon S3', 'download-monitor' ) . '</h3>';
			$this->generate_upsell_box(
				'',
				__( 'Use Amazon S3 links for Download Monitor files to run secure, expiring download links.', 'download-monitor' ),
				'amazon_s3',
				'amazon-s3'
			);
			echo '</div>';
		}

		if ( ! $this->check_extension( 'dlm-google-drive' ) ) {
			echo '<div class="upsells-column"><span class="dashicons dashicons-google"></span>';
			echo '<h3>' . esc_html__( 'Google Drive', 'download-monitor' ) . '</h3>';
			$this->generate_upsell_box(
				'',
				__( 'With this extension, you can integrate your files from Google Drive into Download Monitor.', 'download-monitor' ),
				'google_drive',
				'google-drive'
			);
			echo '</div>';
		}

		echo '</div>';
	}

	/**
	 * Add lite vs pro page in menu
	 *
	 * @param [type] $links
	 *
	 * @return void
	 */
	public function add_lite_vs_pro_page( $links ) {

		$provider       = self::get_extensions_provider();
		$has_upgradable = false;

		foreach ( array_keys( $provider->extensions ) as $slug ) {
			if ( $provider->is_upgradable_addon( $slug ) ) {
				$has_upgradable = true;
				break;
			}
		}

		if ( ! $has_upgradable ) {
			return $links;
		}

		// Settings page.
		$links[] = array(
			'page_title' => __( 'LITE vs Premium', 'download-monitor' ),
			'menu_title' => __( 'LITE vs Premium', 'download-monitor' ),
			'capability' => 'manage_options',
			'menu_slug'  => '#dlm-lite-vs-pro',
			'function'   => array( $this, 'lits_vs_pro_page' ),
			'priority'   => 160,
		);

		return $links;
	}

	/**
	 * The LITE vs PRO page
	 *
	 * @return void
	 */
	public function lits_vs_pro_page() {
		return;
	}

	/**
	 * Add the Upgrade to PRO plugin action link
	 *
	 * @param array $links Plugin action links.
	 *
	 * @return array
	 *
	 * @since 4.5.7
	 */
	public function filter_action_links( $links ) {

		$provider       = self::get_extensions_provider();
		$has_upgradable = false;

		foreach ( array_keys( $provider->extensions ) as $slug ) {
			if ( $provider->is_upgradable_addon( $slug ) ) {
				$has_upgradable = true;
				break;
			}
		}

		if ( ! $has_upgradable ) {
			return $links;
		}

		if ( DLM_Pro_Compat::is_pro_outdated() ) {
			$label = esc_html__( 'Update DLM Pro!', 'download-monitor' );
			$url   = admin_url( 'plugins.php' );
		} else {
			$label = defined( 'DLM_PRO_VERSION' ) ? esc_html__( 'Upgrade!', 'download-monitor' ) : esc_html__( 'Upgrade to Premium!', 'download-monitor' );
			$url   = 'https://www.download-monitor.com/pricing/?utm_source=download-monitor&utm_medium=plugins-page&utm_campaign=upsell';
		}

		$upgrade = array( '<a target="_blank" style="color: orange;font-weight: bold;" href="' . esc_url( $url ) . '">' . $label . '</a>' );

		return array_merge( $upgrade, $links );
	}

	/**
	 * Reports upsells
	 *
	 * @param $tab
	 * @param $key
	 *
	 * @return void
	 * @since 4.8.6
	 */
	public function insights_upsell() {

		if ( $this->check_extension( 'dlm-enhanced-metrics' ) ) {
			return;
		}

		$list = array(
			array(
				'tooltip' => '',
				'feature' => __( 'Compare dates and view chart to see how you’ve done', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show number of completed downloads per download', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show number of redirected downloads per download', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show number of failed downloads per download', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show % of downloads from the total downloads number', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show number of completed downloads by logged in users', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show number of completed downloads by logged out users', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'See active users and their download information', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show the location from where in the site the user downloaded', 'download-monitor' ),
			),
			array(
				'tooltip' => '',
				'feature' => __( 'Show the download\'s category', 'download-monitor' ),
			),
		);

		echo '<div class="wpchill-upsells-wrapper">';

		$this->generate_upsell_box(
			__( 'Enhanced Metrics', 'download-monitor' ),
			'',
			'enhanced-metrics',
			'enhanced-metrics',
			$list
		);

		echo '</div>';
	}

	/**
	 * Upsell page/modals
	 *
	 * @since 5.0.13
	 */
	public function add_upsell_modals() {
		$upsells = DLM_Upsells::get_modal_upsells();
		if ( ! empty( $upsells ) ) {
			// Cycle through the upsells and add them as submenus
			foreach ( $upsells as $key => $upsell ) {
				add_submenu_page( 'edit.php?post_type=dlm_download', $upsell, $upsell, 'manage_options', $key . '_upsell_modal', '' );
			}
		}
	}

	/**
	 * Modal upsells — only for addons not already owned by the current plan.
	 *
	 * @since 5.0.13
	 */
	public static function get_modal_upsells() {
		$upsells = array(
			'dlm_aam' => array( __( 'Global Rules', 'download-monitor' ), 'dlm-advanced-access-manager' ),
			'dlm_lm'  => array( __( 'Library Manager', 'download-monitor' ), 'dlm-page-addon' ),
		);

		$provider = self::get_extensions_provider();
		$result   = array();

		foreach ( $upsells as $key => $upsell ) {
			list( $label, $slug ) = $upsell;
			if ( $provider->is_upgradable_addon( $slug ) ) {
				$result[ $key ] = $label;
			}
		}

		return $result;
	}

	public function inline_script_for_redirection() {
		?>
		<script type="text/javascript">
			document.addEventListener('DOMContentLoaded', function() {
				const link = document.querySelector('a[href*="edit.php?post_type=dlm_download&page=#dlm-lite-vs-pro"]');
				if (link) {
					link.addEventListener('click', function(event) {
						event.preventDefault();
						
						window.open(
						'https://download-monitor.com/free-vs-pro/?utm_source=dlm-lite&utm_medium=link&utm_campaign=upsell&utm_term=lite-vs-pro',
						'_blank'
					);
					});
				}
			});
		</script>
		<?php
	}

	public function enhanced_metrics_upsells_script() {
		if ( ! isset( $_GET['page'] ) || 'download-monitor-reports' !== $_GET['page'] ) {
			return;
		}

		if ( $this->check_extension( 'dlm-enhanced-metrics' ) ) {
			return;
		}

		$upsells_asset_file = require plugin_dir_path( DLM_PLUGIN_FILE ) . 'assets/js/upsells/upsells.asset.php';
		$upsells_enqueue    = array(
			'handle'       => 'dlm-reports-upsells',
			'dependencies' => $upsells_asset_file['dependencies'],
			'version'      => $upsells_asset_file['version'],
			'script'       => DLM_URL . 'assets/js/upsells/upsells.js',
			'style'        => DLM_URL . 'assets/js/upsells/upsells.css',
		);

		// Must be enqueued before so we can hook to it.
		$upsells_enqueue['dependencies'][] = 'dlm-reports-app';

		wp_enqueue_script(
			$upsells_enqueue['handle'],
			$upsells_enqueue['script'],
			$upsells_enqueue['dependencies'],
			$upsells_enqueue['version'],
			true
		);
		wp_enqueue_style(
			$upsells_enqueue['handle'],
			$upsells_enqueue['style'],
			array(),
			$upsells_enqueue['version']
		);
	}
}
