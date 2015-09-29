<?php
/**
 * Pods Module
 *
 * @version $Id$
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Pods extends DWModule {
		protected static $except = 'Except the Pods pages';
		public static $option = array( 'pods' => 'Pods pages' );
		public static $plugin = array( 'pods' => FALSE );
		protected static $question = 'Show widget default on Pods pages?';
		protected static $type = 'complex';

		public static function admin() {
			$wpdb = &$GLOBALS['wpdb'];

			parent::admin();

			if ( self::detect() ) {
				if ( function_exists('pods_api') ) {
					$results = pods_api()->load_pages();
				} else {
					$query = "SELECT id, uri AS name FROM " . $wpdb->prefix . "pod_pages ORDER BY uri";
					$results = $wpdb->get_results($query, ARRAY_A);
				}

				$list = array();
				foreach ( $results as $row ) {
					$list[ $row['id'] ] = $row['name'];
				}

				self::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
			}
		}

		public static function detect($update = TRUE) {
			$DW = &$GLOBALS['DW'];
			$DW->pods = FALSE;

			if ( defined('PODS_DIR') ) {
				if ( $update ) {
					$DW->pods = TRUE;
				}
				return TRUE;
			}
			return FALSE;
		}

		public static function is_dw_pods_page($id) {
			if ( function_exists('pods_api') ) {
				$pod_page = pods_api()->load_page( array( 'id' => $id ) );
				$pod_page_name = (! empty($pod_page) ) ? $pod_page['name'] : '';

				if (! empty($pod_page_name) && is_pod_page($pod_page_name) ) {
					return TRUE;
				}
			} else {
				global $pod_page_exists;

				if ( is_int($id) ) {
					$id = array($id);
				}

				if ( in_array($pod_page_exists['id'], $id) ) {
					return TRUE;
				}

			}

			return FALSE;
		}
	}
?>