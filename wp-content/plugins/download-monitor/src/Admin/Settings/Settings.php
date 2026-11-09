<?php

use WPChill\DownloadMonitor\Shop\Services\Services;

class DLM_Admin_Settings {

	/**
	 * Array used for preloading shortcodes to required pages
	 *
	 * @var array
	 *
	 * @since 4.9.6
	 */
	public $page_preloaders = array();

	/**
	 * Get settings URL
	 *
	 * @return string
	 */
	public static function get_url() {
		return admin_url( 'edit.php?post_type=dlm_download&page=download-monitor-settings' );
	}

	public function __construct() {
		// Add shortcodes to required pages
		$this->preload_shortcodes();
	}

	/**
	 * register_settings function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_settings() {
		$settings = $this->get_settings();

		// register our options and settings
		foreach ( $settings as $tab_key => $tab ) {
			foreach ( $tab['sections'] as $section_key => $section ) {
				$option_group = 'dlm_' . $tab_key . '_' . $section_key;

				// Check to see if $section['fields'] is set, we could be using it for upsells
				if ( isset( $section['fields'] ) ) {
					foreach ( $section['fields'] as $field ) {
						if ( $field['type'] == 'group' ) {
							foreach ( $field['options'] as $group_field ) {
								if ( ! empty( $group_field['name'] ) ) {
									if ( isset( $group_field['std'] ) ) {
										add_option( $group_field['name'], $group_field['std'] );
									}
									register_setting( $option_group, $group_field['name'] );
								}
							}
							continue;
						}

						if ( ! empty( $field['name'] ) && ! in_array( $field['type'], apply_filters( 'dlm_settings_display_only_fields', array( 'action_button' ) ) ) ) {
							if ( isset( $field['std'] ) ) {
								add_option( $field['name'], $field['std'] );
							}
							register_setting( $option_group, $field['name'] );
						}
					}
				}

				// on the overview page, we also register the enabled setting for every gateway. This makes the checkboxes to enable gateways work.
				if ( 'overview' == $section_key ) {
					$gateways = Services::get()->service( 'payment_gateway' )->get_all_gateways();
					if ( ! empty( $gateways ) ) {
						foreach ( $gateways as $gateway ) {
							register_setting( $option_group, 'dlm_gateway_' . esc_attr( $gateway->get_id() ) . '_enabled' );
						}
					}
				}
			}
		}
	}

	/**
	 * Method that return all Download Monitor Settings
	 *
	 * @access public
	 * @return array
	 */
	public function get_settings() {
		$settings = array(
			'general'            => array(
				'title'    => __( 'General', 'download-monitor' ),
				'sections' => array(
					'general' => array(
						'title'  => __( 'General settings', 'download-monitor' ),
						'fields' => array(
							array(
								'name'        => 'dlm_download_endpoint',
								'type'        => 'text',
								'std'         => 'download',
								'placeholder' => __( 'download', 'download-monitor' ),
								'label'       => __( 'Download Endpoint', 'download-monitor' ),
								'desc'        => sprintf( __( 'Define what endpoint should be used for download links. By default this will be %1$s( %2$s ).', 'download-monitor' ), '<strong>download</strong>', esc_url( home_url() ) . '<strong>/download/</strong>' ),
							),
							array(
								'name'    => 'dlm_download_endpoint_value',
								'std'     => 'ID',
								'label'   => __( 'Endpoint Value', 'download-monitor' ),
								'desc'    => sprintf( __( 'Define what unique value should be used on the end of your endpoint to identify the downloadable file. e.g. ID would give a link like <strong>10</strong> ( %1$s%2$s )', 'download-monitor' ), home_url( '/download/' ), '<strong>10/</strong>' ),
								'type'    => 'select',
								'options' => array(
									'ID'   => __( 'Download ID', 'download-monitor' ),
									'slug' => __( 'Download slug', 'download-monitor' ),
								),
							),
							array(
								'name'     => 'dlm_default_template',
								'std'      => '',
								'label'    => __( 'Default Template', 'download-monitor' ),
								'desc'     => __( 'Choose which template is used for <strong>[download]</strong> shortcodes by default (this can be overridden by the <strong>format</strong> argument).', 'download-monitor' ),
								'type'     => 'select',
								'options'  => download_monitor()->service( 'template_handler' )->get_available_templates(),
								'priority' => 10,
							),
							array(
								'name'     => 'dlm_custom_template',
								'type'     => 'text',
								'std'      => '',
								'label'    => __( 'Custom Template', 'download-monitor' ),
								'desc'     => __( 'Leaving this blank will use the default <strong>content-download.php</strong> template file. If you enter, for example, <strong>button</strong>, the <strong>content-download-button.php</strong> template will be used instead. You can add custom templates inside your theme folder.', 'download-monitor' ),
								'priority' => 10,
							),
							array(
								'name'     => 'dlm_wp_search_enabled',
								'std'      => '',
								'label'    => __( 'Include in Search', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => __( "If enabled, downloads will be included in the site's internal search results.", 'download-monitor' ),
								'type'     => 'checkbox',
							),
							array(
								'name'     => 'dlm_turn_off_file_browser',
								'std'      => '',
								'label'    => __( 'Disable file browser', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => __( 'Disables the directory file browser.', 'download-monitor' ),
								'type'     => 'checkbox',
								'priority' => 60,
							),
						),
					),
				),
				'priority' => 10,
			),
			'advanced'           => array(
				'title'    => __( 'Advanced', 'download-monitor' ),
				'sections' => array(
					'page_setup' => array(
						'title'  => __( 'Pages', 'download-monitor' ),
						'fields' => array(
							array(
								'name'    => 'dlm_no_access_page',
								'std'     => '',
								'label'   => __( 'No Access Page', 'download-monitor' ),
								'desc'    => __( "Choose what page is displayed when the user has no access to a file. Don't forget to add the <strong>[dlm_no_access]</strong> shortcode to the page.", 'download-monitor' ),
								'type'    => 'lazy_select',
								'options' => array(),
							),
							array(
								'name'     => 'dlm_no_access_modal',
								'std'      => '0',
								'label'    => __( 'No Access Modal', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => __( 'Open no access message in a modal (pop-up) window.', 'download-monitor' ),
								'type'     => 'checkbox',
							),
							array(
								'name'     => 'dlm_use_default_modal',
								'std'      => '1',
								'label'    => __( 'Use default modal', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => __( 'When enabled, the content of the "No Access page" option will be displayed in the no access modal. If disabled, the modal will show content specific to each extension.', 'download-monitor' ),
								'type'     => 'checkbox',
							),
							array(
								'name'    => 'dlm_dp_downloading_page',
								'std'     => '',
								'label'   => __( 'Downloading Page', 'download-monitor' ),
								'desc'    => __( "Select what page should be displayed as the 'downloading page'.", 'download-monitor' ),
								'type'    => 'lazy_select',
								'options' => array(),
							),
							array(
								'name'     => 'dlm_pa_search_results_page',
								'std'      => '',
								'label'    => __( 'Search -> Document Library Manager Page', 'download-monitor' ),
								'cb_label' => __( 'Enable', 'download-monitor' ),
								'desc'     => sprintf( __( 'Select a page to have downloads in <strong>WordPress</strong> search results link to your Document Library Manager page. Note that this page should have the %1$s shortcode. This is dependant on the %2$s setting.', 'download-monitor' ), '<code>[download_page]</code>', '<code>Include in Search</code>' ),
								'type'     => 'lazy_select',
								'options'  => array(),
							),
							array(
								'name'     => 'dlm_pa_persist_content',
								'std'      => '',
								'label'    => __( 'Hide Page Content', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => sprintf( __( 'Hides the content present on the page containing the %s shortcode on the download\'s info/category/tags/search pages.', 'download-monitor' ), '<code>[download_page]</code>' ),
								'type'     => 'checkbox',
							),
						),
					),
					'access'     => array(
						'title'  => __( 'Access', 'download-monitor' ),
						'fields' => array(
							array(
								'name'        => 'dlm_no_access_error',
								'std'         => sprintf( __( 'You do not have permission to access this download. %1$sGo to homepage%2$s', 'download-monitor' ), '<a href="' . home_url() . '">', '</a>' ),
								'placeholder' => '',
								'label'       => __( 'No access message', 'download-monitor' ),
								'desc'        => __( "The message that will be displayed to visitors when they don't have access to a file.", 'download-monitor' ),
								'type'        => 'editor',
							),
							array(
								'name'     => 'dlm_aam_shortcode_hide_no_access',
								'std'      => '',
								'label'    => __( 'Hide Downloads?', 'download-monitor' ),
								'cb_label' => sprintf( __( 'Hide downloads in %s overview that user has no access to.', 'download-monitor' ), '<code>[downloads]</code>' ),
								'desc'     => sprintf( __( 'Let Advanced Access Manager filter your downloads displayed by %s and remove downloads that the current user has no access to', 'download-monitor' ), '<code>[downloads]</code>' ),
								'type'     => 'checkbox',
							),
							array(
								'name'     => 'dlm_aam_pa_hide_no_access',
								'std'      => '',
								'label'    => __( 'Hide PA Downloads?', 'download-monitor' ),
								'cb_label' => __( 'Hide downloads in Page Addon overview that user has not access to.', 'download-monitor' ),
								'desc'     => __( "Note that this only works with the 'normal' Page Addon downloads and e.g. not with featured downloads or tags.", 'download-monitor' ),
								'type'     => 'checkbox',
							),
						),
					),
					'logging'    => array(
						'title'  => __( 'Reports', 'download-monitor' ),
						'fields' => array(
							array(
								'name'    => 'dlm_logging_ip_type',
								'std'     => '',
								'label'   => __( 'IP Address Logging', 'download-monitor' ),
								'desc'    => __( 'Define if and how you like to store IP addresses of users that download your files in your logs.', 'download-monitor' ),
								'type'    => 'select',
								'options' => array(
									'full'       => __( 'Store full IP address', 'download-monitor' ),
									'anonymized' => __( 'Store anonymized IP address (remove last 3 digits)', 'download-monitor' ),
									'none'       => __( 'Store no IP address', 'download-monitor' ),
								),
							),
							array(
								'name'     => 'dlm_count_unique_ips',
								'std'      => '',
								'label'    => __( 'Count unique IPs only', 'download-monitor' ),
								'cb_label' => '',
								'desc'     => sprintf( __( 'If enabled, the counter for each download will only increment and create a log entry once per IP address. Note that this option only works if %1$s is set to %2$s.', 'download-monitor' ), '<strong>' . __( 'IP Address Logging', 'download-monitor' ) . '</strong>', '<strong>' . __( 'Store full IP address', 'download-monitor' ) . '</strong>' ),
								'type'     => 'checkbox',
							),
							array(
								'name'    => 'dlm_remove_download_logs_after',
								'std'     => '365',
								'label'   => __( 'Max logs duration', 'download-monitor' ),
								'desc'    => __( 'Remove download log entries older than the selected value. The removal process is executed daily.', 'download-monitor' ),
								'type'    => 'select',
								'options' => apply_filters(
									'dlm_active_users_headers',
									array(
										'0'   => __( 'Unlimited', 'download-monitor' ),
										'7'   => __( '7 Days', 'download-monitor' ),
										'15'  => __( '15 Days', 'download-monitor' ),
										'30'  => __( '30 Days', 'download-monitor' ),
										'90'  => __( '3 Months', 'download-monitor' ),
										'180' => __( '6 Months', 'download-monitor' ),
										'365' => __( '1 Year', 'download-monitor' ),
									)
								),
							),
						),
					),
				),
				'priority' => 20,
			),
			'lead_generation'    => array(
				'title'    => esc_html__( 'Content Locking', 'download-monitor' ),
				'sections' => $this->get_content_locking_sections(),
				'priority' => 30,
			),
			'external_hosting'   => array(
				'title'    => esc_html__( 'External Hosting', 'download-monitor' ),
				'sections' => $this->get_external_hosting_sections(),
				'priority' => 40,
			),
			'integration'        => array(
				'title'    => esc_html__( 'Integration', 'download-monitor' ),
				'sections' => $this->get_integration_sections(),
				'priority' => 50,
			),
			'email_notification' => array(
				'title'    => esc_html__( 'Emails', 'download-monitor' ),
				'sections' => $this->get_email_notification_sections(),
				'priority' => 60,
			),
			'page_addon'         => array(
				'title'    => esc_html__( 'Document Library Manager', 'download-monitor' ),
				'sections' => $this->get_page_addon_sections(),
				'priority' => 45,
			),
		);

		$settings['shop'] = array(
			'title'    => __( 'Shop', 'download-monitor' ),
			'sections' => array(
				'general' => array(
					'title'  => __( 'General', 'download-monitor' ),
					'fields' => array(
						array(
							'name'     => 'dlm_shop_enabled',
							'std'      => '',
							'label'    => __( 'Shop', 'download-monitor' ),
							'cb_label' => '',
							'desc'     => __( 'If enabled, allows you to sell your downloads via Download Monitor.', 'download-monitor' ),
							'type'     => 'checkbox',
							'priority' => 20,
						),
					),
				),
			),
			'priority' => 15,
		);

		$settings['shop']['sections']['general']['fields'] = array_merge(
			$settings['shop']['sections']['general']['fields'],
			array(
				array(
					'name'  => 'dlm_invoice_prefix',
					'type'  => 'text',
					'std'   => '',
					'label' => __( 'Invoice Prefix', 'download-monitor' ),
					'desc'  => __( 'This prefix is added to the invoice ID. Enter an unique prefix here.', 'download-monitor' ),
					'child' => true,
				),
				array(
					'name'    => 'dlm_base_country',
					'std'     => 'US',
					'label'   => __( 'Base Country', 'download-monitor' ),
					'desc'    => __( 'Where is your store located?', 'download-monitor' ),
					'type'    => 'select',
					'options' => Services::get()->service( 'country' )->get_countries(),
					'child'   => true,
				),
				array(
					'name'    => 'dlm_currency',
					'std'     => 'USD',
					'label'   => __( 'Currency', 'download-monitor' ),
					'desc'    => __( 'In what currency are you selling?', 'download-monitor' ),
					'type'    => 'select',
					'options' => $this->get_currency_list_with_symbols(),
					'child'   => true,
				),
				array(
					'name'    => 'dlm_currency_pos',
					'std'     => 'left',
					'label'   => __( 'Currency Position', 'download-monitor' ),
					'desc'    => __( 'The position of the currency symbol.', 'download-monitor' ),
					'type'    => 'select',
					'options' => array(
						'left'        => sprintf( __( 'Left (%s)', 'download-monitor' ), Services::get()->service( 'format' )->money( 9.99, array( 'currency_position' => 'left' ) ) ),
						'right'       => sprintf( __( 'Right (%s)', 'download-monitor' ), Services::get()->service( 'format' )->money( 9.99, array( 'currency_position' => 'right' ) ) ),
						'left_space'  => sprintf( __( 'Left with space (%s)', 'download-monitor' ), Services::get()->service( 'format' )->money( 9.99, array( 'currency_position' => 'left_space' ) ) ),
						'right_space' => sprintf( __( 'Right with space (%s)', 'download-monitor' ), Services::get()->service( 'format' )->money( 9.99, array( 'currency_position' => 'right_space' ) ) ),
					),
					'child'   => true,
				),
				array(
					'name'  => 'dlm_decimal_separator',
					'type'  => 'text',
					'std'   => '.',
					'label' => __( 'Decimal Separator', 'download-monitor' ),
					'desc'  => __( 'The decimal separator of displayed prices.', 'download-monitor' ),
					'child' => true,
				),
				array(
					'name'  => 'dlm_thousand_separator',
					'type'  => 'text',
					'std'   => ',',
					'label' => __( 'Thousand Separator', 'download-monitor' ),
					'desc'  => __( 'The thousand separator of displayed prices.', 'download-monitor' ),
					'child' => true,
				),
				array(
					'name'     => 'dlm_disable_cart',
					'std'      => '',
					'label'    => __( 'Disable Cart', 'download-monitor' ),
					'cb_label' => '',
					'desc'     => __( 'If enabled, your customers will be sent to your checkout page directly.', 'download-monitor' ),
					'type'     => 'checkbox',
					'child'    => true,
				),
				array(
					'name'  => '',
					'type'  => 'title',
					'title' => __( 'Pages', 'download-monitor' ),
					'child' => true,
				),
				array(
					'name'    => 'dlm_page_cart',
					'std'     => '',
					'label'   => __( 'Cart page', 'download-monitor' ),
					'desc'    => __( 'Your cart page, make sure it has the <strong>[dlm_cart]</strong> shortcode.', 'download-monitor' ),
					'type'    => 'lazy_select',
					'options' => array(),
					'child'   => true,
				),
				array(
					'name'    => 'dlm_page_checkout',
					'std'     => '',
					'label'   => __( 'Checkout page', 'download-monitor' ),
					'desc'    => __( 'Your checkout page, make sure it has the <strong>[dlm_checkout]</strong> shortcode.', 'download-monitor' ),
					'type'    => 'lazy_select',
					'options' => array(),
					'child'   => true,
				),
			)
		);

		$settings['shop']['sections'] = array_merge( $settings['shop']['sections'], $this->get_payment_methods_sections() );

		if ( self::is_settings_context() ) {
			$settings = $this->access_files_checker_field( $settings );
			$settings = $this->robots_files_checker_field( $settings );
		}

		// this is here to maintain backwards compatibility, use 'dlm_settings' instead
		$old_settings = apply_filters( 'download_monitor_settings', array() );

		// This is the correct filter
		$settings = apply_filters( 'dlm_settings', $settings );

		// Backwards compatibility for 4.3 and 4.4.4
		$settings = $this->backwards_compatibility_settings( $old_settings, $settings );

		// Let's sort the fields by priority
		foreach ( $settings as $key => $setting ) {
			// Check if we have sections
			if ( ! empty( $setting['sections'] ) ) {
				foreach ( $setting['sections'] as $s_key => $section ) {
					// Check if we have fields
					if ( ! empty( $section['fields'] ) ) {
						// Sort the fields by priority
						uasort(
							$settings[ $key ]['sections'][ $s_key ]['fields'],
							array(
								'DLM_Admin_Helper',
								'sort_data_by_priority',
							)
						);
					}
				}
			}
		}

		return $settings;
	}

	/**
	 * Content Locking tab — full schema for every lock extension, always
	 * present regardless of license/enabled state (the REST settings layer
	 * decides locked/badge per section, this method only owns the fields).
	 *
	 * @return array
	 */
	private function get_content_locking_sections() {
		return array(
			'email_lock'           => array(
				'title'  => esc_html__( 'Email Lock', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_el_unlock_type',
						'std'     => 'per_download',
						'label'   => __( 'Submitting email actions', 'download-monitor' ),
						'desc'    => __( 'Select what should be unlocked upon email address submission. Only applies to Email Lock locked downloads.', 'download-monitor' ),
						'type'    => 'radio',
						'options' => array(
							'per_download' => __( 'Unlock requested download', 'download-monitor' ),
							'global'       => __( 'Unlock all locked downloads', 'download-monitor' ),
							'email_link'   => __( 'Email download link', 'download-monitor' ),
						),
					),
					array(
						'name'     => 'dlm_el_optin_double',
						'std'      => '1',
						'label'    => __( 'Double Opt-in', 'download-monitor' ),
						'cb_label' => '',
						'desc'     => __( 'Require user to confirm the entered email and only after that the user can download the files.', 'download-monitor' ),
						'type'     => 'checkbox',
					),
					array(
						'name'     => 'dlm_el_enable_name',
						'std'      => '0',
						'label'    => __( 'Name field', 'download-monitor' ),
						'cb_label' => '',
						'desc'     => __( 'When toggled to ON this will show another field asking users to input their name. Note: When toggled to ON, the Name field also becomes a required field.', 'download-monitor' ),
						'type'     => 'checkbox',
					),
					array(
						'name'    => 'dlm_el_optin_group',
						'label'   => __( 'Opt-in field', 'download-monitor' ),
						'type'    => 'group',
						'options' => array(
							array(
								'name'     => 'dlm_el_optin',
								'std'      => '',
								'label'    => '',
								'cb_label' => __( 'Add checkbox', 'download-monitor' ),
								'desc'     => __( 'Enter your opt-in text. If you enter any text here, a checkbox will be added to your Email Lock forms that your users are required to check. If this field is left empty, no opt-in checkbox will be added.', 'download-monitor' ),
								'type'     => 'editor',
							),
						),
					),
					array(
						'name'    => 'dlm_el_email_settings_group',
						'label'   => __( 'Email settings', 'download-monitor' ),
						'type'    => 'group',
						'options' => array(
							array(
								'name'  => 'dlm_el_optin_email_from_name',
								'std'   => '',
								'label' => __( '"From" name', 'download-monitor' ),
								'desc'  => sprintf( __( 'From whom should the email say it\'s from. If left empty %s will be used.', 'download-monitor' ), '<strong><i>' . esc_html( get_bloginfo( 'name' ) ) . '</i></strong>' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_from',
							),
							array(
								'name'  => 'dlm_el_optin_email_from_address',
								'std'   => '',
								'label' => __( '"From" address', 'download-monitor' ),
								'desc'  => sprintf( __( 'From what address should the email say it\'s from. If left empty %s will be used.', 'download-monitor' ), '<strong><i>' . esc_html( get_bloginfo( 'admin_email' ) ) . '</i></strong>' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_from',
							),
						),
					),
					array(
						'name'    => 'double_optin_accordion_confirmation',
						'label'   => __( 'Confirmation email', 'download-monitor' ),
						'type'    => 'group',
						'child'   => array(
							'field' => 'dlm_el_unlock_type',
							'value' => 'email_link',
						),
						'options' => array(
							array(
								'name'    => 'dlm_el_optin_double_lading_page',
								'std'     => '',
								'label'   => __( 'Landing page', 'download-monitor' ),
								'desc'    => __( 'The page where the user should land when clicking on the confirmation page. Default is our own dynamic template.', 'download-monitor' ),
								'type'    => 'lazy_select',
								'options' => array(),
								'width'   => 'half',
							),
							array(
								'name'  => 'dlm_el_optin_double_text',
								'std'   => '',
								'label' => __( 'Page content', 'download-monitor' ),
								'desc'  => __( 'The text to be shown after the user unlocked the Download and email confirmation is required. Placeholders like <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code> can be used.', 'download-monitor' ),
								'type'  => 'editor',
							),
							array(
								'name'  => 'dlm_el_optin_double_lading_page_title',
								'std'   => 'Thank you',
								'label' => __( 'Page title', 'download-monitor' ),
								'desc'  => __( 'The page\'s title where the user should land when clicking on the confirmation page.', 'download-monitor' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_confirmation_title_subject',
							),
							array(
								'name'  => 'dlm_el_optin_confirmation_subject',
								'std'   => '',
								'label' => __( 'Email subject', 'download-monitor' ),
								'desc'  => __( 'The confirmation email subject the user receives. Placeholders like <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code>, <code>%%site_url%%</code>, <code>%%site_title%%</code>, <code>%%date%%</code>, <code>%%client_name%%</code> can be used.', 'download-monitor' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_confirmation_title_subject',
							),
							array(
								'name'  => 'dlm_el_optin_confirmation_content',
								'std'   => '',
								'label' => __( 'Email body content', 'download-monitor' ),
								'desc'  => __( 'The confirmation email content the user receives. Placeholders like <code>%%confirmation_link%%</code>, <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code>, <code>%%site_url%%</code>, <code>%%site_title%%</code>, <code>%%date%%</code>, <code>%%client_name%%</code> can be used.', 'download-monitor' ),
								'type'  => 'editor',
							),
						),
					),
					array(
						'name'    => 'double_optin_accordion_file_delivery',
						'label'   => __( 'File delivery', 'download-monitor' ),
						'type'    => 'group',
						'child'   => array(
							'field' => 'dlm_el_unlock_type',
							'value' => 'email_link',
						),
						'options' => array(
							array(
								'name'    => 'dlm_el_optin_thankyou_landing_page',
								'std'     => '',
								'label'   => __( 'Landing Page', 'download-monitor' ),
								'desc'    => __( 'The page where the user should land after clicking the Unlock Download. Default is our own dynamic template.', 'download-monitor' ),
								'type'    => 'lazy_select',
								'options' => array(),
								'width'   => 'half',
							),
							array(
								'name'  => 'dlm_el_optin_text',
								'std'   => '',
								'label' => __( 'Page content', 'download-monitor' ),
								'desc'  => __( 'The text to be shown after the user unlocked the Download. Placeholders like <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code>, <code>%%resend_email_button%%</code> can be used.', 'download-monitor' ),
								'type'  => 'editor',
							),
							array(
								'name'  => 'dlm_el_optin_thankyou_page_title',
								'std'   => 'Thank you',
								'label' => __( 'Page title', 'download-monitor' ),
								'desc'  => __( 'The page\'s title where the user should land after clicking the Unlock Download.', 'download-monitor' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_file_delivery_title_subject',
							),
							array(
								'name'  => 'dlm_el_optin_email_subject',
								'std'   => '',
								'label' => __( 'Email subject', 'download-monitor' ),
								'desc'  => __( 'The email subject the user should receive when the download unlock happens. Placeholders like <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code>, <code>%%site_url%%</code>, <code>%%site_title%%</code>, <code>%%date%%</code>, <code>%%client_name%%</code> can be used.', 'download-monitor' ),
								'type'  => 'text',
								'width' => 'half',
								'row'   => 'el_file_delivery_title_subject',
							),
							array(
								'name'  => 'dlm_el_optin_email_content',
								'std'   => '',
								'label' => __( 'Email body content', 'download-monitor' ),
								'desc'  => __( 'The email content the user should receive when the download unlock happens. Placeholders like <code>%%email_link%%</code>, <code>%%download_title%%</code>, <code>%%download_ID%%</code>, <code>%%download_version%%</code>, <code>%%site_url%%</code>, <code>%%site_title%%</code>, <code>%%date%%</code>, <code>%%client_name%%</code> can be used.', 'download-monitor' ),
								'type'  => 'editor',
							),
							array(
								'name'    => 'dlm_el_email_link',
								'std'     => 'download_link',
								'label'   => __( 'Email link', 'download-monitor' ),
								'desc'    => __( 'Select whether to send the user a download link or a landing page link which includes the download.', 'download-monitor' ),
								'type'    => 'radio',
								'options' => array(
									'download_link' => __( 'Download Link', 'download-monitor' ),
									'landing_page'  => __( 'Landing page', 'download-monitor' ),
								),
							),
							array(
								'name'    => 'dlm_el_link_to_landing_page',
								'std'     => '',
								'label'   => __( 'Landing page', 'download-monitor' ),
								'desc'    => __( 'The page where the user should land after clicking the link in the email. It should contain the download link.', 'download-monitor' ),
								'type'    => 'lazy_select',
								'options' => array(),
								'width'   => 'half',
								'child'   => array(
									'field' => 'dlm_el_email_link',
									'value' => 'landing_page',
								),
							),
						),
					),
				),
			),
			'ninja_forms'          => array(
				'title'  => esc_html__( 'Ninja Forms', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_nf_unlock_type',
						'std'     => 'per_form',
						'label'   => __( 'Completing form will unlock', 'download-monitor' ),
						'desc'    => __( 'Select what should be unlocked upon completing a form. Only applies to Ninja Forms locked downloads.', 'download-monitor' ),
						'type'    => 'select',
						'options' => array(
							'per_form' => __( 'only form locked downloads', 'download-monitor' ),
							'global'   => __( 'all locked downloads', 'download-monitor' ),
						),
						'width'   => 'half',
					),
				),
			),
			'gravity_forms'        => array(
				'title'  => esc_html__( 'Gravity Forms', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_gf_unlock_type',
						'std'     => 'per_download',
						'label'   => __( 'Completing form will unlock', 'download-monitor' ),
						'desc'    => __( 'Select what should be unlocked upon completing a form. Only applies to Gravity Forms locked downloads.', 'download-monitor' ),
						'type'    => 'select',
						'options' => array(
							'per_download' => __( 'only requested download', 'download-monitor' ),
							'per_form'     => __( 'all downloads with the same form', 'download-monitor' ),
							'global'       => __( 'all locked downloads', 'download-monitor' ),
						),
						'width'   => 'half',
					),
				),
			),
			'contact_form_7'       => array(
				'title'  => esc_html__( 'Contact Form 7', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_cf7_unlock_type',
						'std'     => 'per_download',
						'label'   => __( 'Completing form will unlock', 'download-monitor' ),
						'desc'    => __( 'Select what should be unlocked upon completing a form. Only applies to Contact Form 7 locked downloads.', 'download-monitor' ),
						'type'    => 'select',
						'options' => array(
							'per_download' => __( 'only requested download', 'download-monitor' ),
							'per_form'     => __( 'all downloads with the same form', 'download-monitor' ),
							'global'       => __( 'all locked downloads', 'download-monitor' ),
						),
						'width'   => 'half',
					),
				),
			),
			'wpforms'              => array(
				'title'  => esc_html__( 'WPForms', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_wpforms_unlock_type',
						'std'     => 'per_download',
						'label'   => __( 'Completing form will unlock', 'download-monitor' ),
						'desc'    => __( 'Select what should be unlocked upon completing a form. Only applies to WPForms locked downloads.', 'download-monitor' ),
						'type'    => 'select',
						'options' => array(
							'per_download' => __( 'only requested download', 'download-monitor' ),
							'per_form'     => __( 'all downloads with the same form', 'download-monitor' ),
							'global'       => __( 'all locked downloads', 'download-monitor' ),
						),
						'width'   => 'half',
					),
				),
			),
			'password_lock'        => array(
				'title'  => esc_html__( 'Password Lock', 'download-monitor' ),
				'fields' => array(
					array(
						'name'     => 'dlm_pw_global',
						'std'      => '0',
						'label'    => __( 'Global password', 'download-monitor' ),
						'cb_label' => __( 'Enable for all downloads', 'download-monitor' ),
						'desc'     => __( 'If enabled, ALL your downloads are locked with the password below. Unlocking any download unlocks every download.', 'download-monitor' ),
						'type'     => 'checkbox',
					),
					array(
						'name'  => 'dlm_pw_global_password',
						'std'   => '',
						'label' => __( 'Password', 'download-monitor' ),
						'type'  => 'password',
						'child' => true,
					),
				),
			),
			'mailchimp_connection' => array(
				'title'        => esc_html__( 'Mailchimp', 'download-monitor' ),
				'show_upsells' => false,
				'fields'       => array(
					array(
						'name'  => '',
						'type'  => 'mailchimp_connection_app',
						'label' => '',
					),
				),
			),
			'expiring_links'       => array(
				'title'         => esc_html__( 'Expiring Links', 'download-monitor' ),
				'show_upsells'  => false,
				'contend_class' => 'dlm-content-tab-full',
				'fields'        => array(
					array(
						'name'  => '',
						'type'  => 'expiring_links_tokens_app',
						'label' => '',
					),
				),
			),
		);
	}

	/**
	 * @return array
	 */
	private function get_external_hosting_sections() {
		return array(
			'amazon_s3'    => array(
				'title'  => esc_html__( 'Amazon S3', 'download-monitor' ),
				'fields' => array(
					array(
						'name'  => 'dlm_amazon_s3_access_key',
						'std'   => '',
						'type'  => 'text',
						'label' => __( 'AWS Access Key ID', 'download-monitor' ),
						'desc'  => sprintf( __( 'Your public AWS Access Key ID. To find this, go to your <a href="%s" target="_blank">Security Credentials page</a>.', 'download-monitor' ), 'https://console.aws.amazon.com/iam/home?#security_credential' ),
						'width' => 'half',
						'row'   => 's3_access',
					),
					array(
						'name'  => 'dlm_amazon_s3_secret_access_key',
						'std'   => '',
						'type'  => 'password',
						'label' => __( 'AWS Secret Access Key', 'download-monitor' ),
						'desc'  => sprintf( __( 'Your secret AWS Access Key. To find this, go to your <a href="%s" target="_blank">Security Credentials page</a>.', 'download-monitor' ), 'https://console.aws.amazon.com/iam/home?#security_credential' ),
						'width' => 'half',
						'row'   => 's3_access',
					),
					array(
						'name'  => 'dlm_amazon_s3_bucket',
						'std'   => '',
						'type'  => 'text',
						'label' => __( 'AWS S3 Bucket Name', 'download-monitor' ),
						'desc'  => sprintf( __( 'Your AWS S3 bucket name. To find this, go to your <a href="%s" target="_blank">Buckets page</a>.', 'download-monitor' ), 'https://s3.console.aws.amazon.com/s3' ),
						'width' => 'half',
						'row'   => 's3_bucket_region',
					),
					array(
						'name'    => 'dlm_amazon_s3_region',
						'std'     => '',
						'type'    => 'select',
						'label'   => __( 'S3 Region', 'download-monitor' ),
						'desc'    => __( 'Your S3 bucket region. If a region is not selected the Amazon S3 file browser will not work but the direct url functionality will be available.', 'download-monitor' ),
						'width'   => 'half',
						'row'     => 's3_bucket_region',
						'options' => array(
							''               => __( 'Detect automatically', 'download-monitor' ),
							'us-east-1'      => 'US East (N. Virginia) - us-east-1',
							'us-east-2'      => 'US East (Ohio) - us-east-2',
							'us-west-1'      => 'US West (N. California) - us-west-1',
							'us-west-2'      => 'US West (Oregon) - us-west-2',
							'ca-central-1'   => 'Canada (Central) - ca-central-1',
							'ap-south-1'     => 'Asia Pacific (Mumbai) - ap-south-1',
							'ap-northeast-2' => 'Asia Pacific (Seoul) - ap-northeast-2',
							'ap-southeast-1' => 'Asia Pacific (Singapore) - ap-southeast-1',
							'ap-southeast-2' => 'Asia Pacific (Sydney) - ap-southeast-2',
							'ap-northeast-1' => 'Asia Pacific (Tokyo) - ap-northeast-1',
							'eu-central-1'   => 'EU (Frankfurt) - eu-central-1',
							'eu-west-1'      => 'EU (Ireland) - eu-west-1',
							'eu-west-2'      => 'EU (London) - eu-west-2',
							'sa-east-1'      => 'South America (Sao Paulo) - sa-east-1',
							'cn-north-1'     => 'China (Beijing) - cn-north-1',
						),
					),
				),
			),
			'google_drive' => array(
				'title'  => esc_html__( 'Google Drive', 'download-monitor' ),
				'fields' => array(
					array(
						'name'  => '',
						'type'  => 'drive_auth_button',
						'std'   => '',
						'label' => '',
						'desc'  => '',
					),
					array(
						'name'  => 'dlm_google_drive_client_id',
						'std'   => '',
						'type'  => 'text',
						'label' => __( 'Client id', 'download-monitor' ),
						'desc'  => __( 'Insert the google client id you get from Google Cloud Console.', 'download-monitor' ),
						'width' => 'half',
						'row'   => 'gd_client',
					),
					array(
						'name'  => 'dlm_google_drive_client_secret',
						'std'   => '',
						'type'  => 'password',
						'label' => __( 'Client Secret Key', 'download-monitor' ),
						'desc'  => __( 'Insert the google client secret key you get from Google Cloud Console.', 'download-monitor' ),
						'width' => 'half',
						'row'   => 'gd_client',
					),
					array(
						'name'  => 'dlm_authorized_javascript',
						'type'  => 'console_uri_field',
						'std'   => '',
						'label' => __( 'Authorized JavaScript origins', 'download-monitor' ),
						'desc'  => __( 'Copy and insert this in Google Cloud Console\'s Authorized JavaScript origins field.', 'download-monitor' ),
						'value' => home_url(),
						'width' => 'half',
						'row'   => 'gd_uris',
					),
					array(
						'name'  => 'dlm_redirect_uri',
						'type'  => 'console_uri_field',
						'std'   => '',
						'label' => __( 'Authorized redirect URIs', 'download-monitor' ),
						'desc'  => __( 'Copy and insert this in Google Cloud Console\'s Authorized redirect URIs field.', 'download-monitor' ),
						'value' => esc_url_raw( admin_url( 'edit.php?post_type=dlm_download&page=download-monitor-settings&tab=external_hosting&action=oauth_redirect' ) ),
						'width' => 'half',
						'row'   => 'gd_uris',
					),
				),
			),
		);
	}

	/**
	 * @return array
	 */
	private function get_integration_sections() {
		$doc_link      = 'https://www.download-monitor.com/kb/captcha/';
		$default_type  = 'turnstile';
		if ( empty( get_option( 'dlm_global_type' ) ) && get_option( 'dlm_ca_global', false ) ) {
			$default_type = 'recaptcha';
		}

		return array(
			'turnstile' => array(
				'title'  => __( 'Turnstile', 'download-monitor' ),
				'fields' => array(
					array(
						'name'  => 'dlm_turnstile_sitekey',
						'std'   => '',
						'label' => __( 'Turnstile Site Key', 'download-monitor' ),
						'desc'  => sprintf( __( 'The Turnstile Site Key can be found in your Cloudflare dashboard. %s for more information on how to set this up.', 'download-monitor' ), '<a href="' . $doc_link . '" target="_blank">' . __( 'Please read our documentation', 'download-monitor' ) . '</a>' ),
						'type'  => 'text',
						'width' => 'half',
						'row'   => 'turnstile_keys',
					),
					array(
						'name'  => 'dlm_turnstile_sitesecret',
						'std'   => '',
						'label' => __( 'Turnstile Secret Key', 'download-monitor' ),
						'desc'  => sprintf( __( 'The Turnstile Secret Key can be found in your Cloudflare dashboard. %s for more information on how to set this up.', 'download-monitor' ), '<a href="' . $doc_link . '" target="_blank">' . __( 'Please read our documentation', 'download-monitor' ) . '</a>' ),
						'type'  => 'text',
						'width' => 'half',
						'row'   => 'turnstile_keys',
					),
					array(
						'name'        => 'dlm_turnstile_unlock_text',
						'std'         => __( 'Please complete the captcha to download the file.', 'download-monitor' ),
						'placeholder' => __( 'Please complete the captcha to download the file.', 'download-monitor' ),
						'label'       => __( 'Turnstile Unlock Text', 'download-monitor' ),
						'desc'        => __( 'The text displayed above your captcha field.', 'download-monitor' ),
						'type'        => 'editor',
					),
				),
			),
			'captcha'   => array(
				'title'  => __( 'reCAPTCHA', 'download-monitor' ),
				'fields' => array(
					array(
						'name'    => 'dlm_ca_type',
						'std'     => '',
						'label'   => __( 'reCAPTCHA type', 'download-monitor' ),
						'desc'    => __( 'Select the version of the reCAPTCHA you are willing to use.', 'download-monitor' ),
						'type'    => 'select',
						'width'   => 'half',
						'options' => array(
							'v2' => __( 'reCAPTCHA v2', 'download-monitor' ),
							'v3' => __( 'reCAPTCHA v3', 'download-monitor' ),
						),
					),
					array(
						'name'  => 'dlm_ca_sitekey',
						'std'   => '',
						'label' => __( 'reCAPTCHA Site Key', 'download-monitor' ),
						'desc'  => sprintf( __( 'The Google reCAPTCHA Site Key can be found in your Google reCAPTCHA dashboard. %s for more information on how to set this up.', 'download-monitor' ), '<a href="' . $doc_link . '" target="_blank">' . __( 'Please read our documentation', 'download-monitor' ) . '</a>' ),
						'type'  => 'text',
						'width' => 'half',
						'row'   => 'recaptcha_keys',
					),
					array(
						'name'  => 'dlm_ca_sitesecret',
						'std'   => '',
						'label' => __( 'reCAPTCHA Secret Key', 'download-monitor' ),
						'desc'  => sprintf( __( 'The Google reCAPTCHA Secret Key can be found in your Google reCAPTCHA dashboard. %s for more information on how to set this up.', 'download-monitor' ), '<a href="' . $doc_link . '" target="_blank">' . __( 'Please read our documentation', 'download-monitor' ) . '</a>' ),
						'type'  => 'text',
						'width' => 'half',
						'row'   => 'recaptcha_keys',
					),
					array(
						'name'        => 'dlm_ca_unlock_text',
						'std'         => __( 'Please complete the captcha to download the file.', 'download-monitor' ),
						'placeholder' => __( 'Please complete the captcha to download the file.', 'download-monitor' ),
						'label'       => __( 'Captcha Unlock Text', 'download-monitor' ),
						'desc'        => __( 'The text displayed above your captcha field.', 'download-monitor' ),
						'type'        => 'editor',
					),
				),
			),
			'global'    => array(
				'title'  => __( 'Global Mode', 'download-monitor' ),
				'fields' => array(
					array(
						'name'     => 'dlm_ca_global',
						'std'      => '1',
						'label'    => __( 'Global mode', 'download-monitor' ),
						'cb_label' => __( 'Enable', 'download-monitor' ),
						'desc'     => sprintf( __( 'If enabled, %sALL%s your downloads will be captcha locked.', 'download-monitor' ), '<strong>', '</strong>' ),
						'type'     => 'checkbox',
					),
					array(
						'name'    => 'dlm_global_type',
						'std'     => $default_type,
						'label'   => __( 'Captcha type', 'download-monitor' ),
						'desc'    => __( 'Select the type of the captcha you are willing to use.', 'download-monitor' ),
						'type'    => 'select',
						'options' => array(
							'turnstile' => __( 'Turnstile', 'download-monitor' ),
							'recaptcha' => __( 'reCAPTCHA', 'download-monitor' ),
						),
						'child'   => true,
					),
				),
			),
		);
	}

	/**
	 * @return array
	 */
	private function get_email_notification_sections() {
		$available_fields = array( 'download_id', 'download_name', 'version', 'user', 'user_email', 'user_firstname', 'user_lastname', 'ip_address', 'timestamp' );
		$default_fields    = array( 'download_name', 'version', 'user', 'ip_address' );
		$admin_email       = get_option( 'admin_email', '' );

		$fields_description  = __( 'What fields should be included in the email. Separate multiple fields by comma(,).', 'download-monitor' ) . '<br/>';
		$fields_description .= __( 'Available fields', 'download-monitor' ) . ': ';
		$fields_description .= '<code>' . implode( '</code>, <code>', apply_filters( 'dlm_en_available_fields', $available_fields ) ) . '</code>';

		return array(
			'email_notification' => array(
				'title'  => esc_html__( 'Email Notification', 'download-monitor' ),
				'fields' => array(
					array(
						'name'        => 'dlm_en_email_addresses',
						'type'        => 'text',
						'std'         => $admin_email,
						'label'       => __( 'Email Addresses', 'download-monitor' ),
						'placeholder' => $admin_email,
						'desc'        => __( 'Define which email addresses will receive download notifications and reports. Separate multiple addresses by comma(,).', 'download-monitor' ),
						'width'       => 'half',
					),
					array(
						'name'  => '',
						'title' => __( 'Notification after a download', 'download-monitor' ),
						'desc'  => __( 'Receive download notifications per download.', 'download-monitor' ),
						'type'  => 'title',
					),
					array(
						'name'    => 'dlm_en_type',
						'std'     => 'all',
						'label'   => __( 'Send notifications for', 'download-monitor' ),
						'desc'    => __( 'You can send notifications for every or just selected downloads files. When "Selected Downloads" is selected, turn on notifications per download in the download edit screen.', 'download-monitor' ),
						'type'    => 'select',
						'width'   => 'half',
						'options' => array(
							'never'    => __( 'Never', 'download-monitor' ),
							'all'      => __( 'All Downloads', 'download-monitor' ),
							'selected' => __( 'Selected Downloads', 'download-monitor' ),
						),
					),
					array(
						'name'        => 'dlm_en_fields',
						'type'        => 'text',
						'std'         => implode( ',', apply_filters( 'dlm_en_default_fields', $default_fields ) ),
						'label'       => __( 'Email Fields', 'download-monitor' ),
						'placeholder' => $admin_email,
						'desc'        => $fields_description,
						'width'       => 'half',
					),
					array(
						'name'  => '',
						'title' => __( 'Reports', 'download-monitor' ),
						'desc'  => __( 'Receive a periodic downloads report notification.', 'download-monitor' ),
						'type'  => 'title',
					),
					array(
						'name'    => 'dlm_en_period',
						'std'     => 'never',
						'label'   => __( 'Report period', 'download-monitor' ),
						'desc'    => __( 'You can send email notification on each download or a periodical downloads report.', 'download-monitor' ),
						'type'    => 'select',
						'width'   => 'half',
						'options' => array(
							'never'   => __( 'Never', 'download-monitor' ),
							'daily'   => __( 'Daily', 'download-monitor' ),
							'weekly'  => __( 'Weekly', 'download-monitor' ),
							'monthly' => __( 'Monthly', 'download-monitor' ),
						),
					),
					array(
						'name'     => 'dlm_en_report_stats',
						'type'     => 'checkbox',
						'cb_label' => '',
						'label'    => __( 'Statistics', 'download-monitor' ),
						'desc'     => __( 'Sends a report containing the number of downloads per period, the most downloaded download and average downloads per day.', 'download-monitor' ),
					),
					array(
						'name'     => 'dlm_en_report_top_dls',
						'type'     => 'checkbox',
						'cb_label' => '',
						'label'    => __( 'Top 10 Downloads', 'download-monitor' ),
						'desc'     => __( 'Sends a report containing the top 10 downloads (title and number of downloads).', 'download-monitor' ),
					),
					array(
						'name'     => 'dlm_en_report_top_users',
						'type'     => 'checkbox',
						'cb_label' => '',
						'label'    => __( 'Top 10 Users', 'download-monitor' ),
						'desc'     => __( 'Sends a report containing the top 10 users (name and number of downloads).', 'download-monitor' ),
					),
				),
			),
		);
	}

	/**
	 * @return array
	 */
	private function get_page_addon_sections() {
		return array(
			'base_settings'    => array(
				'title'  => esc_html__( 'Base Settings', 'download-monitor' ),
				'fields' => array(
					array(
						'name'      => '',
						'type'      => 'page_addon_settings',
						'data_type' => 'base',
					),
				),
			),
			'table_settings'   => array(
				'title'  => esc_html__( 'Table Styling', 'download-monitor' ),
				'fields' => array(
					array(
						'name'      => '',
						'type'      => 'page_addon_settings',
						'data_type' => 'table',
					),
				),
			),
			'grid_settings'    => array(
				'title'  => esc_html__( 'Grid Styling', 'download-monitor' ),
				'fields' => array(
					array(
						'name'      => '',
						'type'      => 'page_addon_settings',
						'data_type' => 'grid',
					),
				),
			),
			'minimal_settings' => array(
				'title'  => esc_html__( 'Minimal Styling', 'download-monitor' ),
				'fields' => array(
					array(
						'name'      => '',
						'type'      => 'page_addon_settings',
						'data_type' => 'minimal',
					),
				),
			),
		);
	}

	/**
	 * Backwards compatibility for settings
	 *
	 * @param $old_settings
	 * @param $settings
	 *
	 * @return mixed
	 * @since 4.4.5
	 */
	public function backwards_compatibility_settings( $old_settings, $settings ) {
		// First we check if there is info in $old_settings
		if ( empty( $old_settings ) ) {
			return $settings;
		}

		$compatibility_tabs = array(
			'access',
			'amazon_s3',
			'captcha',
			'downloading_page',
			'email_lock',
			'email_notification',
			'gravity_forms',
			'ninja_forms',
			'page_addon',
		);

		foreach ( $old_settings as $tab_key => $tab ) {
			// $tab[1] contains the fields inside the setting, not being set means it doesn't have any fields
			if ( ! empty( $tab[1] ) ) {
				if ( in_array( $tab_key, $compatibility_tabs ) ) {
					$tab_title   = false;
					$tab_section = $tab_key;

					if ( 'access' == $tab_key ) {
						$tab_parent = 'advanced';
					}

					if ( 'amazon_s3' == $tab_key ) {
						$tab_parent = 'external_hosting';
					}

					if ( 'captcha' == $tab_key ) {
						$tab_parent = 'integration';
					}

					if ( 'downloading_page' == $tab_key || 'page_addon' == $tab_key ) {
						$tab_parent  = 'advanced';
						$tab_section = 'page_setup';
						$tab_title   = true;
					}

					if ( 'email_lock' == $tab_key || 'twitter_lock' == $tab_key || 'gravity_forms' == $tab_key || 'ninja_forms' == $tab_key ) {
						$tab_parent = 'lead_generation';
					}

					if ( 'email_notification' == $tab_key ) {
						$tab_parent = 'email_notification';
						$tab_title  = true;

						// Reassign the title because the extension overwrittens it
						$settings['email_notification'] = array(
							'title' => esc_html__( 'Emails', 'download-monitor' ),
						);
					}

					if ( isset( $tab[0] ) && ! $tab_title ) {
						$settings[ $tab_parent ]['sections'][ $tab_section ] = array(
							'title'    => $tab[0],
							'sections' => array(),
						);
					}

					// Let's check if there are sections or fields so we can add other fields and not overwrite them
					if ( isset( $settings[ $tab_parent ]['sections'] ) && isset( $settings[ $tab_parent ]['sections'][ $tab_section ]['fields'] ) ) {
						$settings[ $tab_parent ]['sections'][ $tab_section ]['fields'] = array_merge( $settings[ $tab_parent ]['sections'][ $tab_section ]['fields'], $tab[1] );
					} else {
						$settings[ $tab_parent ]['sections'][ $tab_section ]['fields'] = $tab[1];
					}

					// Unset the previous used tab - new tabs are already provided thanks to upsells
					if ( 'email_notification' != $tab_key && 'terns_and_conditions' != $tab_key ) {
						unset( $settings[ $tab_key ] );
					}
				} else {
					foreach ( $tab[1] as $other_tab_fields ) {
						$settings['other']['sections']['other']['fields'][] = $other_tab_fields;
					}
				}
			}
		}

		// Check to see if there is any info in Other tab
		if ( isset( $settings['other'] ) ) {
			$settings['other']['title'] = esc_html__( 'Other', 'download-monitor' );
		}

		return $settings;
	}


	/**
	 * Returns the list of all available currencies and add the symbol to the label
	 *
	 * @return array
	 */
	private function get_currency_list_with_symbols() {
		/** @var \WPChill\DownloadMonitor\Shop\Helper\Currency $currency_helper */
		$currency_helper = Services::get()->service( 'currency' );

		$currencies = $currency_helper->get_available_currencies();

		// get_currency_symbol

		if ( ! empty( $currencies ) ) {
			foreach ( $currencies as $k => $v ) {
				$currencies[ $k ] = $v . ' (' . $currency_helper->get_currency_symbol( $k ) . ')';
			}
		}

		return $currencies;
	}

	/**
	 * Generate payment method sections for settings
	 *
	 * @return array
	 */
	private function get_payment_methods_sections() {
		$gateways = Services::get()->service( 'payment_gateway' )->get_all_gateways();

		// formatted array of gateways with id=>title map (used in select fields)
		$gateways_formatted = array();
		if ( ! empty( $gateways ) ) {
			foreach ( $gateways as $gateway ) {
				$gateways_formatted[ $gateway->get_id() ] = $gateway->get_title();
			}
		}

		/** Generate the overview sections */
		$sections = array(
			'overview' => array(
				'title'  => __( 'Payment Gateways', 'download-monitor' ),
				'fields' => array(
					array(
						'name'     => '',
						'std'      => 'USD',
						'label'    => __( 'Enabled Gateways', 'download-monitor' ),
						'desc'     => __( 'Check all payment methods you want to enable on your webshop.', 'download-monitor' ),
						'type'     => 'gateway_overview',
						'gateways' => $gateways,
					),
					array(
						'name'    => 'dlm_default_gateway',
						'std'     => 'paypal',
						'label'   => __( 'Default Gateway', 'download-monitor' ),
						'desc'    => __( 'This payment method will be pre-selected on your checkout page.', 'download-monitor' ),
						'type'    => 'select',
						'options' => $gateways_formatted,
					),
				),
			),
		);

		/** Generate sections for all gateways */
		if ( ! empty( $gateways ) ) {
			/** @var \WPChill\DownloadMonitor\Shop\Checkout\PaymentGateway\PaymentGateway $gateway */
			foreach ( $gateways as $gateway ) {
				// Option to enable gateways already exists in the Payment Gateways. We should not add it again.
				$fields = array();

				$gateway_settings = $gateway->get_settings();
				if ( ! empty( $gateway_settings ) ) {
					$escaped_id = esc_attr( $gateway->get_id() );
					foreach ( $gateway_settings as $gw ) {
						$prefixed_field = $gw;

						$prefixed_field['name'] = 'dlm_gateway_' . $escaped_id . '_' . $prefixed_field['name'];

						$fields[] = $prefixed_field;
					}
				}

				// dlm_gateway_paypal_

				$sections[ $gateway->get_id() ] = array(
					'title'  => $gateway->get_title(),
					'fields' => $fields,
				);
			}
		}

		return $sections;
	}

	/**
	 * Preload shortcodes to required pages
	 *
	 * @return void
	 */
	private function preload_shortcodes() {
		/**
		 * Filter the shortcodes to preload to the required pages
		 *
		 * Array should consist of key => value pairs where the key is the option and the value is the shortcode to preload
		 *
		 * @hook  dlm_preload_shortcodes
		 *
		 * @param  array  $page_preloaders  The array of page preloaders.
		 *
		 * @return array
		 * @since 4.9.6
		 */
		$this->page_preloaders = apply_filters(
			'dlm_preload_shortcodes',
			array(
				'dlm_no_access_page' => '[dlm_no_access]',
				'dlm_page_cart'      => '[dlm_cart]',
				'dlm_page_checkout'  => '[dlm_checkout]',
			)
		);
		if ( ! empty( $this->page_preloaders ) ) {
			foreach ( $this->page_preloaders as $option => $shortcode ) {
				add_action( 'update_option_' . $option, array( $this, 'preload_shortcode_to_page' ), 15, 3 );
			}
		}
	}

	/**
	 * Add the required shortcode to the page content
	 *
	 * @param  mixed   $old     The old page ID.
	 * @param  mixed   $new     The new page ID.
	 * @param  string  $option  The option name.
	 *
	 * @return void
	 * @since 4.9.6
	 */
	public function preload_shortcode_to_page( $old, $new, $option ) {
		$page_id = absint( $new );
		if ( 0 === $page_id ) {
			return;
		}
		// 1. Get the unformatted post(page) content.
		$page = get_post( $page_id );

		// 2. Search the content for the existence of our shortcode.
		if ( false !== strpos( $page->post_content, $this->page_preloaders[ $option ] ) ) {
			// The page has the no access shortcode, return;
			return;
		}

		// 3. If we got here it means we need to add our shortcode to the page's content.
		$page->post_content .= $this->page_preloaders[ $option ];

		// 4. Finally, we update the post.
		wp_update_post( $page );
	}

	/**
	 * @return bool
	 */
	private static function is_settings_context() {
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return true;
		}

		return isset( $_GET['post_type'], $_GET['page'] ) && 'dlm_download' === $_GET['post_type'] && 'download-monitor-settings' === $_GET['page'];
	}

	/**
	 * @param  array  $settings
	 *
	 * @return array
	 */
	private function access_files_checker_field( $settings ) {
		$upload_dir      = wp_upload_dir();
		$server_software = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		$is_iis          = stristr( $server_software, 'Microsoft-IIS' ) !== false;
		$is_nginx        = stristr( $server_software, 'nginx' ) !== false;

		if ( $is_iis ) {
			$protection_path = $upload_dir['basedir'] . '/dlm_uploads/web.config';
			$icon            = 'dashicons-dismiss';
			$icon_color      = '#f00';
			$icon_text       = __( 'Web.config is missing.', 'download-monitor' );

			if ( file_exists( $protection_path ) ) {
				$icon       = 'dashicons-yes-alt';
				$icon_color = '#00A32A';
				$icon_text  = __( 'You are protected by web.config.', 'download-monitor' );
			}
		} else {
			$htaccess_path = $upload_dir['basedir'] . '/dlm_uploads/.htaccess';
			$icon          = 'dashicons-dismiss';
			$icon_color    = '#f00';
			$icon_text     = __( 'Htaccess is missing.', 'download-monitor' );

			if ( file_exists( $htaccess_path ) ) {
				$icon       = 'dashicons-yes-alt';
				$icon_color = '#00A32A';
				$icon_text  = __( 'You are protected by htaccess.', 'download-monitor' );
			}

			if ( $is_nginx ) {
				$upload_path = str_replace( sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ), '', $upload_dir['basedir'] );
				$nginx_rules = "<code class='dlm-code-nginx-rules'>location ^~ " . $upload_path . '/dlm_uploads {<br />internal;<br />}</code>';

				$nginx_text = sprintf( __( 'Please add the following rules to your nginx config to disable direct file access: %s', 'download-monitor' ), wp_kses_post( $nginx_rules ) );

				$icon       = 'dashicons-dismiss';
				$icon_color = '#f00';
				$icon_text  = sprintf( __( 'Because your server is running on nginx, our .htaccess file can\'t protect your downloads. %s', 'download-monitor' ), $nginx_text );
				$disabled   = true;
			}
		}

		if ( ! isset( $settings['general']['sections']['misc']['title'] ) ) {
			$settings['general']['sections']['misc']['title'] = __( 'Miscellaneous', 'download-monitor' );
		}

		$settings['general']['sections']['misc']['fields'][] = array(
			'name'       => 'dlm_regenerate_protection',
			'label'      => __( 'Regenerate protection for uploads folder', 'download-monitor' ),
			'desc'       => __( 'Regenerates the .htaccess file.', 'download-monitor' ),
			'icon'       => $icon,
			'icon-color' => $icon_color,
			'icon-text'  => $icon_text,
			'disabled'   => isset( $disabled ) ? 'true' : 'false',
			'type'       => 'htaccess_status',
			'priority'   => 30,
		);

		return $settings;
	}

	/**
	 * @param  array  $settings
	 *
	 * @return array
	 */
	private function robots_files_checker_field( $settings ) {
		$transient = get_transient( 'dlm_robots_txt' );

		if ( ! $transient ) {
			$robots_file = sanitize_text_field( wp_unslash( $_SERVER['DOCUMENT_ROOT'] ) ) . '/robots.txt';

			$transient = array(
				'icon'       => 'dashicons-dismiss',
				'icon_color' => '#f00',
				'text'       => __( 'Robots.txt is missing.', 'download-monitor' ),
			);

			if ( file_exists( $robots_file ) ) {
				if ( stristr( (string) file_get_contents( $robots_file ), 'dlm_uploads' ) ) {
					$transient['protected']  = true;
					$transient['icon']       = 'dashicons-yes-alt';
					$transient['icon_color'] = '#00A32A';
					$transient['text']       = __( 'You are protected by robots.txt.', 'download-monitor' );
				} else {
					$transient['protected'] = false;
					$transient['text']      = __( 'Robots.txt file exists but dlm_uploads folder is not protected.', 'download-monitor' );
				}
			} else {
				$response = wp_remote_get( get_home_url() . '/robots.txt' );

				if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
					$transient['virtual'] = 'maybe';
					$transient['text']    = __( 'Robots.txt file is missing but site may have virtual robots.txt file. If you regenerate this you will loose the restrictions set in the virtual one. Please either update the virtual with the corresponding rules for dlm_uploads or regenerate and update the newly created one with the contents from the virtual file.', 'download-monitor' );
				} else {
					$transient['virtual'] = 'maybe';
					$transient['text']    = __( 'Robots.txt file is missing but site has virtual robots.txt file. If you regenerate this you will loose the restrictions set in the virtual one. Please either update the virtual with the corresponding rules for dlm_uploads or regenerate and update the newly created one with the contents from the virtual file.', 'download-monitor' );
				}
			}

			set_transient( 'dlm_robots_txt', $transient, DAY_IN_SECONDS );
		}

		$transient = wp_parse_args(
			$transient,
			array(
				'icon'       => 'dashicons-dismiss',
				'icon_color' => '#f00',
				'text'       => __( 'Robots.txt is missing.', 'download-monitor' ),
			)
		);

		$settings['general']['sections']['misc']['fields'][] = array(
			'name'       => 'dlm_regenerate_robots',
			'label'      => __( 'Regenerate crawler protection for uploads folder', 'download-monitor' ),
			'desc'       => __( 'Regenerates the robots.txt file.', 'download-monitor' ),
			'icon'       => $transient['icon'],
			'icon-color' => $transient['icon_color'],
			'icon-text'  => $transient['text'],
			'type'       => 'htaccess_status',
			'priority'   => 40,
		);

		return $settings;
	}
}
