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
		'admin_url_page'    => 'forminator',
		'admin_parent_page' => 'forminator',
	),
	'smush'               => array(
		'slug'              => 'wp-smushit',
		'path'              => 'wp-smushit/wp-smush.php',
		'utm_source'        => 'smush',
		'utm_campaign'      => 'blackfriday_plugin_smush',
		'admin_url_page'    => 'smush',
		'admin_parent_page' => 'smush',
	),
	'hummingbird'         => array(
		'slug'              => 'hummingbird-performance',
		'path'              => 'hummingbird-performance/wp-hummingbird.php',
		'utm_source'        => 'hummingbird',
		'utm_campaign'      => 'blackfriday_plugin_hummingbird',
		'admin_url_page'    => 'wphb',
		'admin_parent_page' => 'wphb',
	),
	'defender'            => array(
		'slug'              => 'defender-security',
		'path'              => 'defender-security/wp-defender.php',
		'utm_source'        => 'defender',
		'utm_campaign'      => 'blackfriday_plugin_defender',
		'admin_url_page'    => array( 'wp-defender', 'wdf-' ),
		'admin_parent_page' => 'wp-defender',
	),
	'smartcrawl'          => array(
		'slug'              => 'smartcrawl-seo',
		'path'              => 'smartcrawl-seo/wpmu-dev-seo.php',
		'utm_source'        => 'smartcrawl',
		'utm_campaign'      => 'blackfriday_plugin_smartcrawl',
		'admin_url_page'    => 'wds',
		'admin_parent_page' => 'wds_wizard',
	),
	'hustle'              => array(
		'slug'              => 'wordpress-popup',
		'path'              => 'wordpress-popup/popover.php',
		'utm_source'        => 'hustle',
		'utm_campaign'      => 'blackfriday_plugin_hustle',
		'admin_url_page'    => 'hustle',
		'admin_parent_page' => 'hustle',
	),
	'branda'              => array(
		'slug'              => 'branda-white-labeling',
		'path'              => 'branda-white-labeling/ultimate-branding.php',
		'utm_source'        => 'branda',
		'utm_campaign'      => 'blackfriday_plugin_branda',
		'admin_url_page'    => 'branding',
		'admin_parent_page' => 'branding',
	),
	'beehive'             => array(
		'slug'              => 'beehive-analytics',
		'path'              => 'beehive-analytics/beehive-analytics.php',
		'utm_source'        => 'beehive',
		'utm_campaign'      => 'blackfriday_plugin_beehive',
		'admin_url_page'    => 'beehive',
		'admin_parent_page' => 'beehive',
	),
	'broken-link-checker' => array(
		'slug'              => 'broken-link-checker',
		'path'              => 'broken-link-checker/broken-link-checker.php',
		'utm_source'        => 'blc',
		'utm_campaign'      => 'blackfriday_plugin_blc',
		'admin_url_page'    => 'blc',
		'admin_parent_page' => 'blc_dash',
	),
);
