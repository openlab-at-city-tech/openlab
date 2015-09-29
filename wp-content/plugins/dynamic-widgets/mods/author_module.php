<?php
/**
 * Author Module
 *
 * @version $Id: author_module.php 1095126 2015-02-20 12:59:35Z qurl $
 * @copyright 2011 Jacco Drabbe
 */

	defined('ABSPATH') or die("No script kiddies please!");

	class DW_Author extends DWModule {
		protected static $except = 'Except the author(s)';
		public static $option = array( 'author' => 'Author Pages' );
		protected static $question = 'Show widget default on author pages?';
		protected static $type = 'complex';

		public static function admin() {
			parent::admin();
			self::mkGUI(self::$type, self::$option[self::$name], self::$question);
		}

		public static function getAuthors() {
			global $wpdb;

			if ( function_exists('get_users') ) {
				$authors = get_users( array('who' => 'authors') );
			} else {
				$query = "SELECT " . $wpdb->prefix . "users.ID, " . $wpdb->prefix . "users.display_name
							 FROM " . $wpdb->prefix . "users
							 JOIN " . $wpdb->prefix . "usermeta ON " . $wpdb->prefix . "users.ID = " . $wpdb->prefix . "usermeta.user_id
							 WHERE 1 AND " . $wpdb->prefix . "usermeta.meta_key = '" . $wpdb->prefix . "user_level'
							 	AND " . $wpdb->prefix . "usermeta.meta_value > '0'";
				$authors = $wpdb->get_results($query);
			}

			$list = array();
			foreach ( $authors as $author ) {
				$list[$author->ID] = $author->display_name;
			}

			return $list;
		}

		public static function mkGUI($type, $title, $question, $info = FALSE, $except = FALSE, $list = FALSE, $name = NULL) {
			$DW = &$GLOBALS['DW'];
			$list = self::getAuthors();

			if ( $info ) {
				self::$opt = $DW->getDWOpt($GLOBALS['widget_id'], 'single-author');

				if ( count($list) > DW_LIST_LIMIT ) {
					$select_style = DW_LIST_STYLE;
				}

				if ( count($list) > 0 ) {
					$DW->dumpOpt(self::$opt);
					echo '<br />';
					_e(self::$except, DW_L10N_DOMAIN);
					echo '<br />';
					echo '<div id="single-author-select" class="condition-select" ' . ( (isset($select_style)) ? $select_style : '' ) . ' />';
					foreach ( $list as $key => $value ) {
						$extra = 'onclick="ci(\'single_author_act_' . $key . '\')"';
						echo '<input type="checkbox" id="single_author_act_' . $key . '" name="single_author_act[]" value="' . $key . '" ' . ( (self::$opt->count > 0 && in_array($key, self::$opt->act)) ? 'checked="checked"' : '' ) . $extra  . ' /> <label for="single_author_act_' . $key . '">' . $value . '</label><br />' . "\n";
					}
					echo '</div>' . "\n";
				}
			} else {
				parent::mkGUI(self::$type, self::$option[self::$name], self::$question, FALSE, self::$except, $list);
			}
		}
	}
?>