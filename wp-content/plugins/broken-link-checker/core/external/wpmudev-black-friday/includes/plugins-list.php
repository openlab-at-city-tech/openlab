<?php
/**
 * Plugin list of WPMU DEV for the black-friday sub-module.
 *
 * @link    https://wpmudev.com/
 * @since   2.0.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV\Plugin_Cross_Sell
 *
 * @copyright (c) 2025, WPMU DEV (http://wpmudev.com)
 */

return array(
	'forminator'          => array(
		'slug'              => 'forminator',
		'path'              => 'forminator/forminator.php',
		'utm_source'        => 'forminator',
		'utm_campaign'      => 'blackfriday_plugin_forminator',
		// Optional. The plugin's own campaign page, instead of the shared one.
		'campaign_url'      => 'https://wpmudev.com/project/forminator-pro/',
		'admin_url_page'    => 'forminator',
		'admin_parent_page' => 'forminator',
		'upsell_widget'     => array(
			// Screen ids where the upsell widget is displayed.
			'screens'   => array( 'toplevel_page_forminator', 'toplevel_page_forminator-network' ),
			// The widget's upgrade button. Used to find the widget, as it has no id or unique class.
			'button'    => 'a[href*="forminator_dashboard_upsellwidget_button"]',
			// The widget wrapper, the closest parent of the button.
			'container' => '.sui-box',
			// The badge image is added at the end of the title.
			'title'     => '.sui-box-title',
			// Elements to hide, eg the PRO tag.
			'hide'      => '.sui-tag-pro',
		),
	),
	'smush'               => array(
		'slug'              => 'wp-smushit',
		'path'              => 'wp-smushit/wp-smush.php',
		'utm_source'        => 'smush',
		'utm_campaign'      => 'blackfriday_plugin_smush',
		'campaign_url'      => 'https://wpmudev.com/project/wp-smush-pro/',
		'admin_url_page'    => 'smush',
		'admin_parent_page' => 'smush',
		// Optional. The plugin uses the new UI, so its menu gets the full Black Friday design.
		'new_ui'            => true,
		// The banner is moved to the top of this element, below the plugin's page header. Rendered with js by the plugin.
		'banner_container'  => '.smush-main-content-inner',
		// The banner is hidden while the body has this class. Smush adds it on all its pages while its onboarding wizard is shown.
		'banner_hidden_body_class' => 'smush-onboarding-wizard',
	),
	'hummingbird'         => array(
		'slug'              => 'hummingbird-performance',
		'path'              => 'hummingbird-performance/wp-hummingbird.php',
		'utm_source'        => 'hummingbird',
		'utm_campaign'      => 'blackfriday_plugin_hummingbird',
		'campaign_url'      => 'https://wpmudev.com/project/wp-hummingbird/',
		'admin_url_page'    => 'wphb',
		'admin_parent_page' => 'wphb',
		'new_ui'            => true,
		'banner_container'  => '.wpmudev-hb-main-content-inner',
	),
	'defender'            => array(
		'slug'              => 'defender-security',
		'path'              => 'defender-security/wp-defender.php',
		'utm_source'        => 'defender',
		'utm_campaign'      => 'blackfriday_plugin_defender',
		'campaign_url'      => 'https://wpmudev.com/project/wp-defender/',
		'admin_url_page'    => array( 'wp-defender', 'wdf-' ),
		'admin_parent_page' => 'wp-defender',
		'new_ui'            => true,
		'banner_container'  => '.defender-main-content-inner',
	),
	'smartcrawl'          => array(
		'slug'              => 'smartcrawl-seo',
		'path'              => 'smartcrawl-seo/wpmu-dev-seo.php',
		'utm_source'        => 'smartcrawl',
		'utm_campaign'      => 'blackfriday_plugin_smartcrawl',
		'campaign_url'      => 'https://wpmudev.com/project/smartcrawl-wordpress-seo/',
		'admin_url_page'    => 'wds',
		'admin_parent_page' => 'wds_wizard',
		'upsell_widget'     => array(
			'screens'   => array( 'toplevel_page_wds_wizard', 'toplevel_page_wds_wizard-network' ),
			'button'    => 'a[href*="smartcrawl_dashboard_upsellwidget_button"]',
			'container' => '#wds-upgrade',
			'title'     => '.sui-box-title',
			'hide'      => '.sui-tag-pro',
		),
	),
	'hustle'              => array(
		'slug'              => 'wordpress-popup',
		'path'              => 'wordpress-popup/popover.php',
		'utm_source'        => 'hustle',
		'utm_campaign'      => 'blackfriday_plugin_hustle',
		'campaign_url'      => 'https://wpmudev.com/project/hustle/',
		'admin_url_page'    => 'hustle',
		'admin_parent_page' => 'hustle',
		'upsell_widget'     => array(
			'screens'   => array( 'toplevel_page_hustle' ),
			'button'    => 'a[href*="hustle_dashboard_upsellwidget_button"]',
			'container' => '.hui-box-upsell',
			'title'     => '.sui-box-title',
			'hide'      => '.sui-tag-pro',
		),
	),
	'branda'              => array(
		'slug'              => 'branda-white-labeling',
		'path'              => 'branda-white-labeling/ultimate-branding.php',
		'utm_source'        => 'branda',
		'utm_campaign'      => 'blackfriday_plugin_branda',
		'campaign_url'      => 'https://wpmudev.com/project/ultimate-branding/',
		'admin_url_page'    => 'branding',
		'admin_parent_page' => 'branding',
		'upsell_widget'     => array(
			'screens'   => array( 'toplevel_page_branding', 'toplevel_page_branding-network' ),
			'button'    => 'a[href*="branda_dashboard_upsellwidget_button"]',
			'container' => '#branda-dashboard-widget-upsell',
			'title'     => '.sui-box-title',
			'hide'      => '.sui-tag-pro',
		),
	),
	'beehive'             => array(
		'slug'              => 'beehive-analytics',
		'path'              => 'beehive-analytics/beehive-analytics.php',
		'utm_source'        => 'beehive',
		'utm_campaign'      => 'blackfriday_plugin_beehive',
		'campaign_url'      => 'https://wpmudev.com/project/beehive-analytics-pro/',
		'admin_url_page'    => 'beehive',
		'admin_parent_page' => 'beehive',
	),
	'broken-link-checker' => array(
		'slug'              => 'broken-link-checker',
		'path'              => 'broken-link-checker/broken-link-checker.php',
		'utm_source'        => 'blc',
		'utm_campaign'      => 'blackfriday_plugin_blc',
		'campaign_url'      => 'https://wpmudev.com/black-friday/',
		'admin_url_page'    => 'blc',
		'admin_parent_page' => 'blc_dash',
	),
);
