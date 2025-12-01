<?php

/**
 * Various utility functions for editor 
 *
 * @copyright   Copyright (C) 2020, Echo Plugins
 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Editor_Utilities {

	/**
	 * Check if the current page is actively rendering the current page on the frontend.
	 *
	 * Supported builders:
	 *  - Elementor
	 *  - Divi Builder
	 *  - WPBakery Page Builder / Visual Composer
	 *  - Visual Composer Website Builder (vcv)
	 *  - Beaver Builder
	 *  - SiteOrigin Page Builder
	 *
	 * @return bool True when a supported builder is active for current page.
	 */
	public static function is_page_builder_enabled() {

		global $post;
		if ( empty( $post ) ) {
			return false;
		}

		$post_id = $post->ID;

		/* ---------- Elementor ---------- */
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			/*
			 * Elementor loads the current page inside an iframe when the visual builder
			 * is opened.  The iframe URL contains the "elementor-preview" query
			 * parameter.  We only want to hide our own Front-end Editor while the
			 * user is working with Elementor – not on normal page views.  Therefore
			 * we simply check for the presence of that parameter.
			 *
			 * Example editor URL:
			 *   https://example.com/my-page/?p=123&elementor-preview=1&ver=3.20.0
			 */
			if ( ! empty( $_GET['elementor-preview'] ) ) {
				return true;
			}

		}

		/* ---------- Divi ---------- */
		if ( class_exists( 'ET_Theme_Builder_Request' ) ) {
			if ( isset($_GET['et_fb']) && $_GET['et_fb'] !== '' ) {
				return true;
			}
		}

		/* ---------- WPBakery Page Builder ---------- */
		if ( defined( 'WPB_VC_VERSION' ) ) {
			/*
			 * WPBakery (formerly Visual Composer) adds a few query parameters to the
			 * front-end page when the live editor is active.  The most reliable one
			 * across versions is "vc_editable" which is set to "true".  We also look
			 * for the legacy "vc_edit" and "vc_action" parameters just in case the
			 * site is running an old version.
			 */
			if ( ! empty( $_GET['vc_editable'] ) || ! empty( $_GET['vc_edit'] ) || ! empty( $_GET['vc_action'] ) ) {
				return true;
			}

		}

		/* ---------- Visual Composer Website Builder ---------- */
		if ( defined( 'VCV_VERSION' ) ) {
			/*
			 * Visual Composer Website Builder (vcv) uses the query parameter
			 * "vcv-action" when the live editor loads the front-end preview.
			 * It can have values like "vcvFrontend" or "vcvPreview".
			 */
			if ( ! empty( $_GET['vcv-action'] ) ) {
				return true;
			}

		}

		/* ---------- Beaver Builder ---------- */
		if ( defined( 'FL_BUILDER_LITE' ) || class_exists( 'FLBuilder' ) ) {
			if ( ! empty($_GET['fl_builder']) ) {
				return true;
			}
		}

		/* ---------- SiteOrigin Page Builder ---------- */
		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			/*
			 * SiteOrigin Live Editor appends the "so_live_editor" query parameter
			 * (true/1) to the preview URL.  Another parameter sometimes used is
			 * "siteorigin_panels_live_editor".  Detect either.
			 */
			if ( ! empty( $_GET['so_live_editor'] ) || ! empty( $_GET['siteorigin_panels_live_editor'] ) ) {
				return true;
			}

		}

		return false;
	}

	public static function initialize_advanced_search_box( $use_main_page_settings = true ) {
		if ( EPKB_Utilities::is_advanced_search_enabled() && class_exists( 'ASEA_Search_Box_View' ) ) { /* @disregard PREFIX */
			global $asea_use_main_page_settings;
			$asea_use_main_page_settings = $use_main_page_settings;	// for AJAX request we need to hard-code the value here
			/**@disregard P1009 */
			new ASEA_Search_Box_View();	/* @disregard PREFIX */	// TODO: move to KB Utilities Constants
		}
	}
}