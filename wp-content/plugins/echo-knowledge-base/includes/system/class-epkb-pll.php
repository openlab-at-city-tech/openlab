<?php  if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * A class that makes KB compatible with Polylang
 *
 * @copyright   Copyright (C) 2018, Echo Plugins
 */
class EPKB_PLL {

	/**
	 * Return true if:
	 * - WPML option is disabled
	 * - OR Polylang is not enabled
	 * - OR the current page/article has default language
	 *
	 * @param $kb_config
	 * @return bool
	 */
	public static function is_original_language_page( $kb_config ) {

		if ( ! EPKB_Utilities::is_wpml_enabled( $kb_config ) || ! function_exists( 'pll_get_post_language' ) || ! function_exists( 'pll_default_language' ) ) {
			return true;
		}

		$current_page_id = (int)get_the_ID();
		$current_page_lang = pll_get_post_language( $current_page_id );
		$main_lang = pll_default_language();

		return $current_page_lang == $main_lang;
	}
}
