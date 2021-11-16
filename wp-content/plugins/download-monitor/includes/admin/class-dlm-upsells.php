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

	public $extensions = array();

	private $upsell_tabs = array();

	/**
	 * DLM_Upsells constructor.
	 *
	 * @since 4.4.5
	 */
	public function __construct() {

		$this->set_hooks();

		$this->set_tabs();

		$this->set_upsell_actions();

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

	/**
	 * Set our hooks
	 *
	 * @since 4.4.5
	 */
	public function set_hooks(){

		add_action( 'dlm_tab_content_general', array( $this, 'general_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_access', array( $this, 'access_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_logging', array( $this, 'logging_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_terns_and_conditions', array( $this, 'terms_and_conditions_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_email_notification', array( $this, 'emails_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_pages', array( $this, 'pages_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_misc', array( $this, 'misc_tab_upsell' ), 15 );

		add_action( 'dlm_tab_content_endpoints', array( $this, 'endpoint_tab_upsell' ), 15 );

		add_filter( 'dlm_download_metaboxes', array( $this, 'add_meta_boxes' ), 30 );

		add_action( 'dlm_download_monitor_files_writepanel_start', array( $this, 'files_metabox_upsells' ), 30, 1 );

		add_filter( 'dlm_settings', array( $this, 'pro_tab_upsells' ), 99, 1 );

		add_action( 'admin_init', array( $this, 'set_extensions' ), 99 );

	}


	/**
	 * Generate the all-purpose upsell box
	 *
	 * @param       $title
	 * @param       $description
	 * @param       $tab
	 * @param       $extension
	 * @param null  $utm_source
	 * @param array $features
	 *
	 * @return string
	 *
	 * @since 4.4.5
	 */
	public function generate_upsell_box( $title, $description, $tab, $extension, $features = array(), $utm_source = null ) {

		echo '<div class="wpchill-upsell">';

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
					echo "<p>" . esc_html( $feature['feature'] ) . "</p>";
				} else {
					echo '<span class="wpchill-check dashicons dashicons-yes"></span>' . esc_html( $feature['feature'] );
				}

				echo '</li>';

			}
			echo '</ul>';
		}

		if ( ! $utm_source ) {
			$utm_source = 'settings_panel';
		}

		echo '<p class="wpchill-upsell-description">' . esc_html( $description ) . '</p>';
		echo '<p>';

		$buttons = '<a target="_blank" href="https://download-monitor.com/extensions/' . esc_attr( $extension ) . '/?utm_source=' . esc_attr( $utm_source ) . '&utm_medium=upsell&utm_campaign=w.org&utm_content=' . esc_attr( $tab ) . '" class="button-primary button">' . esc_html__( 'Get Extension!', 'download-monitor' ) . '</a>';

		echo wp_kses_post( apply_filters( 'dlm_upsell_buttons', $buttons, $tab ) );

		echo '</p>';
		echo '</div>';

	}

	/**
	 * Add upsell metaboxes
	 *
	 * @since 4.4.5
	 */
	public function add_meta_boxes( $meta_boxes ) {

		if ( ! $this->check_extension( 'dlm-download-page' ) ) {

			$meta_boxes[] = array(
				'id'       => 'dlm-download-page-upsell',
				'title'    => esc_html__( 'Downloading page', 'download-monitor' ),
				'callback' => array( $this, 'output_download_page_upsell' ),
				'screen'   => 'dlm_download',
				'context'  => 'side',
				'priority' => 30
			);
		}

		if ( ! $this->check_extension( 'dlm-buttons' ) ) {

			$meta_boxes[] = array(
				'id'       => 'dlm-buttons-upsell',
				'title'    => esc_html__( 'Buttons', 'download-monitor' ),
				'callback' => array( $this, 'output_buttons_upsell' ),
				'screen'   => 'dlm_download',
				'context'  => 'side',
				'priority' => 40
			);
		}

		return $meta_boxes;
	}

	/**
	 * Set the existing extensions
	 *
	 * @since 4.4.5
	 */
	public function set_extensions() {

		$dlm_Extensions = DLM_Admin_Extensions::get_instance();

		$extensions = $dlm_Extensions->get_extensions();

		foreach ( $extensions as $extension ) {
			$this->extensions[] = $extension->product_id;
		}

	}

	/**
	 * Check if extension exists
	 *
	 * @param $extension
	 *
	 * @return bool
	 *
	 * @since 4.4.5
	 */
	public function check_extension( $extension ) {

		if ( empty( $this->extensions ) || ! in_array( $extension, $this->extensions ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Set DLM's upsell tabs
	 *
	 * @since 4.4.5
	 */
	public function set_tabs() {
		// Define our upsell tabs
		// First is the tab and then are the sections
		$this->upsell_tabs = apply_filters( 'dlm_upsell_tabs', array(
				'lead_generation'  => array(
					'title'    => esc_html__( 'Lead Generation', 'download-monitor' ),
					'upsell'   => true,
					'sections' => array(
						'ninja_forms'   => array(
							'title'    => __( 'Ninja Forms', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
						'gravity_forms' => array(
							'title'    => __( 'Gravity Forms', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
							'upsell'   => true,
							'badge'    => true,
						),
						'email_lock'    => array(
							'title'    => __( 'Email lock', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
					),
				),
				'external_hosting' => array(
					'title'    => esc_html__( 'External hosting', 'download-monitor' ),
					'sections' => array(
						'amazon_s3'    => array(
							'title'    => __( 'Amazon S3', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
						'google_drive' => array(
							'title'    => __( 'Google Drive', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
					),
				),
				'advanced'         => array(
					'title'    => esc_html__( 'Advanced', 'download-monitor' ),
					'sections' => array(
						'page_addon'       => array(
							'title'    => __( 'Page Addon', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
						'downloading_page' => array(
							'title'    => __( 'Downloading Page', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						),
						'captcha'          => array(
							'title'    => __( 'Captcha', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						)
					),
				),
				'integration'      => array(
					'title'    => esc_html__( 'Integration', 'download-monitor' ),
					'sections' => array(
						'captcha' => array(
							'title'    => __( 'Captcha', 'download-monitor' ),
							'sections' => array(), // Need to put sections here for backwards compatibility
						)
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
	 * Add Upsell tabs content
	 *
	 * @since 4.4.5
	 */
	public function set_upsell_actions() {

		foreach ( $this->upsell_tabs as $key => $tab ) {

			if ( method_exists( 'DLM_Upsells', 'upsell_tab_content_' . $key ) ) {
				add_action( 'dlm_tab_content_' . $key, array( $this, 'upsell_tab_content_' . $key ), 30, 1 );
			}

		}
	}

	/**
	 * Settings General tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function general_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-download-duplicator' ) ) {

			$this->generate_upsell_box( 
				__( 'Duplicate your downloads', 'download-monitor' ),
				__( 'You’re one click away from duplicating downloads, including their data, versions, and files.', 'download-monitor' ),
				'general',
				'download-duplicator'
			);
		}

		if ( ! $this->check_extension( 'dlm-email-notification' ) ) {

			$this->generate_upsell_box(
				__( 'Email notifications', 'download-monitor' ),
				__( 'Create an email alert to be notified each time one of your files has been downloaded.', 'download-monitor' ),
				'general',
				'email-notification'
			);
		}

	}

	/**
	 * Settings Access tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function access_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-advanced-access-manager' ) ) {

			$this->generate_upsell_box(
				__( 'Advanced access manager', 'download-monitor' ),
				__( 'Limit access to your downloads by setting advanced access rules and restrictions with this extension.', 'download-monitor' ),
				'access',
				'advanced-access-manager'
			);
		}

		if ( ! $this->check_extension( 'dlm-twitter-lock' ) ) {

			$this->generate_upsell_box(
				__( 'Twitter lock', 'download-monitor' ),
				__( 'Allow your users to tweet a pre-defined text before accessing a download.', 'download-monitor' ),
				'access',
				'twitter-lock'
			);
		}

		if ( ! $this->check_extension( 'dlm-email-lock' ) ) {

			$this->generate_upsell_box(
				__( 'Email lock', 'download-monitor' ),
				__( 'Require your users’ email addresses to send newsletters and create a list of your customers.', 'download-monitor' ),
				'access',
				'email-lock'
			);
		}

		if ( ! $this->check_extension( 'dlm-gravity-forms' ) ) {

			$this->generate_upsell_box(
				__( 'Gravity forms extension', 'download-monitor' ),
				__( 'Ask users to fill in a form created on Gravity Forms before they start downloading your files.', 'download-monitor' ),
				'access',
				'gravity-forms'
			);
		}

		if ( ! $this->check_extension( 'dlm-ninja-forms' ) ) {

			$this->generate_upsell_box(
				__( 'Ninja forms extension', 'download-monitor' ),
				__( 'Use the Ninja Forms extension to add forms easily to your download files.', 'download-monitor' ),
				'access',
				'ninja-forms'
			);
		}

		if ( ! $this->check_extension( 'dlm-mailchimp-lock' ) ) {

			$this->generate_upsell_box(
				__( 'Mailchimp extension', 'download-monitor' ),
				__( 'Create a MailChimp list and ask users to subscribe to it before accessing a downloadable file.', 'download-monitor' ),
				'access',
				'mailchimp-lock'
			);
		}

	}

	/**
	 * Settings Logging tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function logging_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-captcha' ) ) {

			$this->generate_upsell_box(
				__( 'Captcha', 'download-monitor' ),
				__( 'Stop bots from spamming your downloads and ask users to complete Google reCAPTCHA.', 'download-monitor' ),
				'logging',
				'captcha'
			);
		}

	}

	/**
	 * Settings Terms and conditions tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function terms_and_conditions_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-terms-and-conditions' ) ) {

			$this->generate_upsell_box(
				__( 'Terms and conditions', 'download-monitor' ),
				__( 'Require your users to accept your terms and conditions before they can download your files.', 'download-monitor' ),
				'terns_and_conditions',
				'terms-and-conditions'
			);
		}

	}

	/**
	 * Settings Emails tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function emails_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-email-notification' ) ) {

			$this->generate_upsell_box(
				__( 'Email notifications', 'download-monitor' ),
				__( 'The Email Notification extension for Download Monitor sends you an email whenever one of your files is downloaded', 'download-monitor' ),
				'email_notifications',
				'email-notifications'
			);
		}

	}

	/**
	 * Settings Logging tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function pages_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-terms-conditions' ) ) {

			$this->generate_upsell_box(
				__( 'Terms & Conditions', 'download-monitor' ),
				__( 'Easily require your visitors to agree to your terms and conditions before downloading files.', 'download-monitor' ),
				'pages',
				'terms-conditions'
			);
		}

		if ( ! $this->check_extension( 'dlm-page-addon' ) ) {

			$this->generate_upsell_box(
				__( 'Page Addon', 'download-monitor' ),
				__( 'List all downloads, categories, tags, and showcase info pages of each resource with a self-contained [download_page] shortcode!', 'download-monitor' ),
				'pages',
				'page-addon'
			);
		}

	}

	/**
	 * Settings Misc tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function output_buttons_upsell() {

		if ( ! $this->check_extension( 'dlm-buttons' ) ) {

			$this->generate_upsell_box(
				__( 'Buttons', 'download-monitor' ),
				__( 'The Buttons extension allows you to customize your download buttons as you please in order to improve the user experience. Create stunning buttons without needing any coding skills!', 'download-monitor' ),
				'cpt',
				'buttons'
			);
		}

	}

	/**
	 * Settings Misc tab upsell
	 *
	 *
	 * @since 4.4.5
	 */
	public function endpoint_tab_upsell() {

		if ( ! $this->check_extension( 'dlm-csv-impoter' ) ) {

			$this->generate_upsell_box(
				__( 'Importer', 'download-monitor' ),
				__( 'Easily import your downloads, including their categories, tags, and files.', 'download-monitor' ),
				'endpoint',
				'csv-impoter'
			);
		}

		if ( ! $this->check_extension( 'dlm-csv-exporter' ) ) {

			$this->generate_upsell_box(
				__( 'Exporter', 'download-monitor' ),
				__( 'With a single click, you can quickly export your downloads and their tags, categories, and file versions to a CSV file.', 'download-monitor' ),
				'endpoint',
				'csv-exporter'
			);
		}

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
	 * Upsell for Locking tab
	 *
	 * @since 4.4.5
	 */
	public function upsell_tab_content_lead_generation() {

		if ( ! $this->check_extension( 'dlm-ninja-forms' ) ) {

			$this->generate_upsell_box(
				__( 'Ninja Forms extension', 'download-monitor' ),
				__( 'The Ninja Forms extension for Download Monitor allows you to require users to fill in a Ninja Forms form before they gain access to a download.','download-monitor' ),
				'ninja_forms',
				'ninja-forms'
			);
		}

		if ( ! $this->check_extension( 'dlm-email-lock' ) ) {

			$this->generate_upsell_box(
				__( 'Email lock extension', 'download-monitor' ),
				__( 'The Email Lock extension for Download Monitor allows you to require users to fill in their email address before they gain access to a download.', 'download-monitor' ),
				'email_lock',
				'email-lock'
			);
		}

		if ( ! $this->check_extension( 'dlm-gravity-forms' ) ) {

			$this->generate_upsell_box(
				__( 'Gravity Forms extension', 'download-monitor' ),
				__( 'The Gravity Forms extension for Download Monitor allows you to require users to fill out a Gravity Forms form before they gain access to a download.', 'download-monitor' ),
				'gravity_forms',
				'gravity-forms'
			);
		}

	}

	/**
	 * Upsell for Amazon S3 setting tab
	 *
	 * @since 4.4.5
	 */
	public function upsell_tab_content_external_hosting() {

		if ( ! $this->check_extension( 'dlm-amazon-s3' ) ) {

			$this->generate_upsell_box(
				__( 'Amazon S3', 'download-monitor' ),
				__( 'Link to files hosted on Amazon s3 so that you can serve secure, expiring download links.', 'download-monitor' ),
				'amazon_s3',
				'amazon-s3'
			);
		}

		if ( ! $this->check_extension( 'dlm-google-drive' ) ) {

			$this->generate_upsell_box(
				__( 'Google Drive', 'download-monitor' ),
				__( 'With this extension, you can integrate your files from Google Drive into Download Monitor.', 'download-monitor' ),
				'google_drive',
				'google-drive'
			);
		}

	}


	/**
	 * Upsell for Page Addon setting tab
	 *
	 * @since 4.4.5
	 */
	public function upsell_tab_content_advanced() {

		if ( ! $this->check_extension( 'dlm-page-addon' ) ) {

			$this->generate_upsell_box(
				__( 'Page addon extension', 'download-monitor' ),
				__( 'Add a self contained [download_page] shortcode to your site to list downloads, categories, tags, and show info pages about each of your resources.', 'download-monitor' ),
				'page_addon',
				'page-addon'
			);
		}

		if ( ! $this->check_extension( 'dlm-downloading-page' ) ) {

			$this->generate_upsell_box(
				__( 'Downloading page extension', 'download-monitor' ),
				__( 'The Downloading Page extension for Download Monitor forces your downloads to be served from a separate page.', 'download-monitor' ),
				'downloading_page',
				'downloading-page'
			);
		}


	}

	/**
	 * Upsell for Captcha setting tab
	 *
	 * @since 4.4.5
	 */
	public function upsell_tab_content_captcha() {

		if ( ! $this->check_extension( 'dlm=captcha' ) ) {

			$this->generate_upsell_box(
				__( 'Captcha extension', 'download-monitor' ),
				__( 'The Captcha extension for Download Monitor allows you to require users to complete a Google reCAPTCHA before they gain access to a download.', 'download-monitor' ),
				'captcha',
				'captcha'
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
	public function files_metabox_upsells( $download ) {

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
	 * Upsell for Integration tab
	 *
	 * @since 4.4.5
	 */
	public function upsell_tab_content_integration() {

		if ( ! $this->check_extension( 'dlm-captcha' ) ) {

			$this->generate_upsell_box(
				__( 'Captcha', 'download-monitor' ),
				__( 'Stop bots from spamming your downloads and ask users to complete Google reCAPTCHA.', 'download-monitor' ),
				'logging',
				'captcha'
			);
		}

	}
}

DLM_Upsells::get_instance();