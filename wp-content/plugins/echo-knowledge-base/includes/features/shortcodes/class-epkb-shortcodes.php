<?php

/**
 * Setup shortcodes
 *
 * @copyright   Copyright (c) 2018, Echo Plugins
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */
class EPKB_Shortcodes {

	const SHORTCODES = array(
		'epkb-knowledge-base',
		'epkb-articles-index-directory',
		'eckb-advanced-search',
		'widg-recent-articles',
		'widg-search-articles',
		'widg-categories-list',
		'widg-category-articles',
		'widg-tags-list',
		'widg-tag-articles',
		'epkb-faqs',
		'widg-popular-articles'
	);

	public function __construct() {
		new EPKB_Articles_Index_Shortcode();
		new EPKB_Faqs_Shortcode();
    }

	/**
	 * Check if shortcode exists and active
	 *
	 * @param $shortcode_name
	 *
	 * @return bool
	 */
	public static function is_shortcode_exists( $shortcode_name ) {

		// is shortcode defined
		if ( ! in_array( $shortcode_name, self::SHORTCODES ) ) {
			return false;
		}

		// is echo-widgets plugin active
		if ( ! empty( preg_match( '/^widg/', $shortcode_name ) ) && empty( EPKB_Utilities::is_plugin_enabled( 'widg' ) ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Get copy shortcode to clipboard box
	 *
	 * @param $shortcode_name
	 * @param $kb_id
	 * @param $box_label
	 *
	 * @return string
	 */
	public static function get_copy_box( $shortcode_name, $kb_id=1, $box_label='' ) {

		$embed_code = self::get_embed_code( $shortcode_name, $kb_id );
		if ( empty( $embed_code ) ) {
			return '';
		}

		return EPKB_HTML_Elements::get_copy_to_clipboard_box( $embed_code, $box_label );
	}

	/**
	 * Get copy shortcode with custom parameters to clipboard box
	 *
	 * @param $shortcode_name
	 * @param array $parameters
	 * @param string $box_label
	 *
	 * @return string
	 */
	public static function get_copy_custom_box( $shortcode_name, $parameters = [], $box_label='', $quotes=true ) {

		if ( empty( self::is_shortcode_exists( $shortcode_name ) ) ) {
			return '';
		}

		$embed_code = '[' . $shortcode_name;
		foreach ( $parameters as $key => $val ) {
			$embed_code .= ' ' . $key . '=' . ( $quotes ? '"' : '' ) . $val . ( $quotes ? '"' : '' );
		}

		$embed_code .= ']';

		return EPKB_HTML_Elements::get_copy_to_clipboard_box( $embed_code, $box_label );
	}

	/**
	 * Get shortcode embed code
	 *
	 * @param $shortcode_name
	 * @param $kb_id
	 *
	 * @return string
	 */
	private static function get_embed_code( $shortcode_name, $kb_id=1 ) {

		if ( empty( self::is_shortcode_exists( $shortcode_name ) ) ) {
			return '';
		}

		$shortcode_param = $kb_id == EPKB_KB_Config_DB::DEFAULT_KB_ID ? '' : ' kb_id=' . $kb_id;
		return '[' . $shortcode_name . $shortcode_param . ']';
	}
}
