<?php
/**
 * WPML Module
 *
 * @version $Id: wpml_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_WPML extends DWModule {
		public static $icon;
		protected static $info = 'Using this option can override all other options.';
		protected static $except = 'Except the languages';
		public static $option = array( 'wpml' => 'Language' );
		protected static $overrule = TRUE;
		protected static $question = 'Show widget default on all languages?';
		public static $plugin = array( 'wpml' => FALSE );
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();

			if ( self::detect() ) {
				$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
				require_once($wpml_api);

				$list = array();
				$wpml_langs = wpml_get_active_languages();
				foreach ( $wpml_langs as $lang ) {
					$code = $lang['code'];
					$list[$code] = $lang['display_name'];
				}

				self::mkGUI(self::$type, self::$option[self::$name], self::$question, self::$info, self::$except, $list);
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->wpml = FALSE;

			if ( defined('ICL_PLUGIN_PATH') && file_exists(ICL_PLUGIN_PATH . DW_WPML_API) ) {
				self::checkOverrule('DW_WPML');
				if ( $update ) {
					$DW->wpml = TRUE;
				}
				self::$icon = '<img src="' . $DW->plugin_url . DW_WPML_ICON . '" alt="WMPL" title="Dynamic Widgets syncs with other languages of these pages via WPML" style="position:relative;top:2px;" />';
				return TRUE;
			}
			return FALSE;
		}

		public static function detectLanguage() {
			$DW = &$GLOBALS['DW'];

			if ( self::detect() ) {
				$wpml_api = ICL_PLUGIN_PATH . DW_WPML_API;
				if ( file_exists($wpml_api) ) {
					require_once($wpml_api);

					$wpmlang = wpml_get_default_language();
					$curlang = wpml_get_current_language();
					$DW->message('WPML language: ' . $curlang);

					if ( $wpmlang != $curlang ) {
						$DW->wpml = TRUE;
						$DW->message('WPML enabled');
					}

					return $curlang;
				}
			}
		}

		public static function getID($content_id, $content_type = 'post_page') {
			// WPML works with the taxonomy id, DW works with term_id
			if ( $content_type == 'tax_category' ) {
				$content_id = self::getTaxID($content_id);
			}

			$language_code = wpml_get_default_language();
			$lang = wpml_get_content_translation($content_type, $content_id, $language_code);

			if ( is_array($lang) ) {
				$id = $lang[$language_code];
			} else {
				$id = $content_id;
			}

			if ( $content_type == 'tax_category' ) {
				$id = self::getTermID($id);
			}

			return $id;
		}

		private static function getTaxID($term_id) {
			global $wpdb;

			$query = "SELECT term_taxonomy_id FROM " . $wpdb->term_taxonomy . " WHERE term_id = %s";
			$query = $wpdb->prepare($query, $term_id);
			$tax_id = $wpdb->get_var($query);

			return $tax_id;
		}

		private static function getTermID($tax_id) {
			global $wpdb;

			$query = "SELECT term_id FROM " . $wpdb->term_taxonomy . " WHERE term_taxonomy_id = %s";
			$query = $wpdb->prepare($query, $tax_id);
			$term_id = $wpdb->get_var($query);

			return $term_id;
		}
	}
?>