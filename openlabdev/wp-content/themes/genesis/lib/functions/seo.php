<?php
/**
 * This file essentially checks for the existence of 3rd party
 * SEO plugins, and disables the Genesis SEO features if they
 * are present.
 *
 * @package Genesis
 * @author Nathan Rice
 */

/**
 * This function disables the Genesis SEO features
 */
function genesis_disable_seo() {
	remove_filter('wp_title', 'genesis_default_title', 10, 3);
	remove_action('get_header', 'genesis_doc_head_control');
	remove_action('genesis_meta','genesis_seo_meta_description');
	remove_action('genesis_meta','genesis_seo_meta_keywords');
	remove_action('genesis_meta','genesis_robots_meta');
	remove_action('wp_head','genesis_canonical');
	add_action('wp_head', 'rel_canonical');

	remove_action('admin_menu', 'genesis_add_inpost_seo_box');
	remove_action('save_post', 'genesis_inpost_seo_save', 1, 2);

	remove_action('admin_init', 'genesis_add_taxonomy_seo_options');

	remove_action('show_user_profile', 'genesis_user_seo_fields');
	remove_action('edit_user_profile', 'genesis_user_seo_fields');

	remove_theme_support('genesis-seo-settings-menu');
	add_filter('pre_option_' . GENESIS_SEO_SETTINGS_FIELD, '__return_empty_array');
}

add_action('init', 'genesis_seo_compatibility_check', 15);
/**
 * This function checks for the existence of popular SEO plugins and disables
 * the Genesis SEO features if one or more of the plugins is active.
 *
 */
function genesis_seo_compatibility_check() {

	if ( genesis_detect_seo_plugins() )
		genesis_disable_seo();
		
	/** Disable Taxonomy Title/Description text if WordPress SEO is active */
	if ( defined( 'WPSEO_VERSION' ) ) {
		remove_action( 'admin_init', 'genesis_add_taxonomy_archive_options' );
		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
	}

	/** Disable Genesis <title> generation if SEO Title Tag is active */
	if (function_exists('seo_title_tag')) {
		remove_filter('wp_title', 'genesis_default_title', 10, 3);
		remove_action('genesis_title', 'wp_title');
		add_action('genesis_title', 'seo_title_tag');
	}

}

add_action('admin_notices', 'genesis_scribe_nag');
/**
 * Display nag for Scribe SEO Copywriting tool.
 */
function genesis_scribe_nag() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'seo-settings' )
		return;

	if ( class_exists('Ecordia') || get_option('genesis-scribe-nag-disabled') )
		return;

	printf( '<div class="updated" style="overflow: hidden;"><p class="alignleft">Have you tried our Scribe SEO software? Do keyword research, content optimization, and link building without leaving WordPress. <b>Genesis owners save over 50&#37; using the promo code FIRST when you sign up</b>. <a href="%s" target="_blank">Click here for more info</a>.</p> <p class="alignright"><a href="%s">Dismiss</a></p></div>', 'http://scribeseo.com/genesis-owners-only', add_query_arg( 'dismiss-scribe', 'true', menu_page_url( 'seo-settings', false ) ) );

}

add_action('admin_init', 'genesis_disable_scribe_nag');
/**
 * This function detects a query flag, and disables the Scribe nag,
 * then redirects the user back to the SEO settings page.
 */
function genesis_disable_scribe_nag() {

	if ( !isset($_REQUEST['page']) || $_REQUEST['page'] != 'seo-settings' )
		return;

	if ( !isset($_REQUEST['dismiss-scribe']) || $_REQUEST['dismiss-scribe'] !== 'true' )
		return;

	update_option( 'genesis-scribe-nag-disabled', 1 );

	genesis_admin_redirect( 'seo-settings' );
	exit;
}

/**
 * Detect some SEO Plugin that add constants, classes or functions.
 *
 * Introduces Genesis filter: 'genesis_detect_seo_plugins'.
 *
 * @uses 'genesis_detect_seo_plugin' filter to allow third party manpulation of
 *  SEO Plugin list.
 * @uses genesis_detect_plugin()
 * @since 1.6
 * @author Charles Clarkson
 */
function genesis_detect_seo_plugins() {

	return genesis_detect_plugin(

		// Use this filter to adjust plugin tests.
		apply_filters( 'genesis_detect_seo_plugins',

			// Add to this array to add new plugin checks.
			array(

				// Classes to detect.
				'classes' => array(
					'wpSEO',
					'All_in_One_SEO_Pack',
					'HeadSpace_Plugin',
					'Platinum_SEO_Pack',
				),

				// Functions to detect.
				'functions' => array(),

				// Constants to detect.
				'constants' => array(
					'WPSEO_VERSION'
				),
			)
		)
	);
}
