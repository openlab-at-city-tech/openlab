<?php
/**
 * Plugin Name: Dynamic Widgets
 * Description: Dynamic Widgets gives you full control on which pages your widgets will appear. It lets you dynamicly show or hide widgets on WordPress pages.
 * Version: 1.6.1
 * Requires at least: 3.0.0
 * Requires PHP: 5.2.7
 * Author: vivwebs
 * Author URI: https://profiles.wordpress.org/vivwebs/
 * Tags: widget, widgets, dynamic, sidebar, custom, rules, logic, admin, condition, conditional tags, conditional content, hide, show, wpml, qtranslate, wpec, buddypress, pods
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * Released under the GPL v.2, http://www.gnu.org/copyleft/gpl.html
 *
 * @version $Id: dynamic-widgets.php 2971998 2023-09-26 21:24:42Z vivalex $
 * @copyright 2017 Jacco Drabbe
 *
 * Thanks to Alexis Nomine for the contribution of the French (fr_FR) language files, several L10N fixes and change of the edit options UI.
 * Thanks to Daniel Bihler for the contribution of the German (de_DE) language files.
 * Thanks to Eduardo Larequi for the contribution of the Spanish (es_ES) language files and several L10N fixes.
 * Thanks to Hanolex for the contribution of the Chinese (Simplified) (zh_CN) language files.
 * Thanks to Liudas Ališauskas for the contribution of the Lithuanian (lt_LT) language files.
 * Thanks to Pedro Nave for the contribution of the Portuguese (pt_PT) language files.
 * Thanks to Renato Tavares for the contribution of the Brazil Portuguese (pt_BR) language files.
 * Thanks to Pavel Bilek for the contribution of the Chech (cs_CZ) language files.
 * Thanks to Morten Nalholm for the contribution of the Danish (da_DK) language files.
 * Thanks to Scott Kingsley Clark for the help to get the Pods module upgraded to support Pods v2.
 * Thanks to Sébastien Christy for the help finding a PHP bug preventing the exposure of class properties while in the right scope.
 * Thanks to Rick Anderson from Build Your Own Business Website (http://www.byobwebsite.com/) for the financial contribution to implement the AJAX lazy load taxonomy tree and the modules filter
 * Thanks to Advancis (http://advancis.net/) for the help and financial contribution to find and fix a WPML category bug.
 * Thanks to Borisa Djuraskovic for the contribution of the Serbo-Croatian (sr_RS) languages files.
 * Thanks to Leon Juranic from DefenseCode to run it's scanner over the source code and finding a few vulnerabilities.
 * Thanks to Nathan Wright of NW Consulting for the financial contribution to implement the shortcode filter feature.
 * Thanks to Mike Epstein to find a vulnerability in the DW settings.
 * Thanks to HANNA instruments for the financial contribution to implement the domain name / server name filter feature.
 * Thanks to WordPress formm user @fjaeker for finding and debugging a problem in the Pages module since WordPress 5.
 * Thanks to WordPress forum user @sovabarmak for finding and fixing a bug in Pages childs as a result of the previous Pages fix
 * Thanks to Erwan from WPScan to find a vulnerability in processing the Custom Posts Taxonomy tree.
 *
 *
 * WPML Plugin support via API
 * Using constants	ICL_PLUGIN_PATH > mods/wpml_module.php
 * Using functions  wpml_get_default_language() > mods/wpml_module.php
 *                  wpml_get_current_language() > mods/wpml_module.php
 *                  wpml_get_content_translation() > mods/wpml_module.php
 * 									wpml_get_active_languages() > mods/wpml_module.php
 *
 * QTranslate Plugin support via API
 * Using constants 	QTRANS_INIT > mods/qt_module.php
 * Using functions	qtrans_getLanguage() > mods/qt_module.php
 * Using WPOptions	qtranslate_default_language > mods/qt_module.php
 * 									qtranslate_enabled_languages > mods/qt_module.php
 *
 * WPSC/WPEC Plugin support
 * Using constants	WPSC_TABLE_PRODUCT_CATEGORIES	> dynwid_admin_overview.php
 * 									WPSC_VERSION > mods/wpsc_module.php
 * Using vars 			$wpsc_query > mods/wpsc_module.php
 *
 * BP Plugin support
 * Using constants	BP_VERSION > mods/bp_module.php
 * Using vars				$bp > mods/bp_module.php
 *
 * Pods Plugin support
 * Using constants 	PODS_VERSION_FULL > mods/pods_module.php
 * Using vars				$pod_page_exists > mods/pods_module.php, dynwid_worker.php
**/

	defined('ABSPATH') or die("No script kiddies please!");

	// Constants
	define('DW_CLASSES', dirname(__FILE__) . '/' . 'classes/');
	define('DW_DEBUG', FALSE);
	define('DW_DB_TABLE', 'dynamic_widgets');
	define('DW_L10N_DOMAIN', 'dynamic-widgets');
	define('DW_LIST_LIMIT', 20);
	define('DW_LIST_STYLE', 'style="overflow:auto;height:240px;"');
	define('DW_OLD_METHOD', get_option('dynwid_old_method'));
	define('DW_PAGE_LIMIT', get_option('dynwid_page_limit', 500));
	define('DW_MINIMUM_PHP', '5.2.7');
	define('DW_MINIMUM_WP', '3.0');
	define('DW_MODULES', dirname(__FILE__) . '/' . 'mods/');
	define('DW_PLUGIN', dirname(__FILE__) . '/' . 'plugin/');
	define('DW_TIME_LIMIT', 86400);				// 1 day
	define('DW_URL_AUTHOR', 'https://profiles.wordpress.org/vivwebs/');
	define('DW_VERSION', '1.6.1');
	define('DW_WPML_API', '/inc/wpml-api.php');			// WPML Plugin support - API file relative to ICL_PLUGIN_PATH
	define('DW_WPML_ICON', 'img/wpml_icon.png');	// WPML Plugin support - WPML icon

	// Classes - only PHP5
	if ( version_compare(PHP_VERSION, DW_MINIMUM_PHP, '>=') ) {
		require_once(dirname(__FILE__) . '/dynwid_class.php');
	}

	// Functions
	/**
	 * dynwid_activate() Activate the plugin
	 * @since 1.3.3
	 */
	function dynwid_activate() {
		global $wpdb;

		$dbtable = $wpdb->prefix . DW_DB_TABLE;

		$query = "CREATE TABLE IF NOT EXISTS " . $dbtable . " (
                id int(11) NOT NULL auto_increment,
                widget_id varchar(100) NOT NULL,
                maintype varchar(100) NOT NULL,
                `name` varchar(100) NOT NULL,
                `value` longtext NOT NULL,
              PRIMARY KEY  (id),
              KEY widget_id (widget_id,maintype)
            );";
		$wpdb->query($query);

		// Version check
		$version = get_option('dynwid_version');
		if ( $version !== FALSE ) {
/*    1.2 > Added support for widget display setting options for Author Pages.
   		Need to apply archive rule to author also to keep same behavior. */
			if ( version_compare($version, '1.2', '<') ) {
				$query = "SELECT widget_id FROM " . $dbtable . " WHERE maintype = 'archive'";
				$results = $wpdb->get_results($query);
				foreach ( $results as $myrow ) {
					$query = "INSERT INTO " . $dbtable . "(widget_id, maintype, value) VALUES ('" . $myrow->widget_id . "', 'author', '0')";
					$wpdb->query($query);
				}
			}

/*    1.3 > Added Date (range) support.
   		Need to change DB `value` to a LONGTEXT type
   		(not for the date of course, but for supporting next features which might need a lot of space) */
			if ( version_compare($version, '1.3', '<') ) {
				$query = "ALTER TABLE " . $dbtable . " CHANGE `value` `value` LONGTEXT NOT NULL";
				$wpdb->query($query);
			}

/*		1.4.0.5 > Enlarged the maintype field because of addition of CTs 	*/
			if ( version_compare($version, '1.4.0.5', '<') ) {
				$query = "ALTER TABLE " . $dbtable . " CHANGE `maintype` `maintype` VARCHAR(50) NOT NULL";
				$wpdb->query($query);
			}

/*		1.4.0.12 > Added MSIE 6 support in browser module
			Need to apply MSIE rule to MSIE6 to keep same behavior. */
			if ( version_compare($version, '1.4.0.12', '<') ) {
				$query = "SELECT widget_id, value FROM " . $dbtable . " WHERE maintype = 'browser' AND name = 'msie'";
				$results = $wpdb->get_results($query);
				foreach ( $results as $myrow ) {
					$query = "INSERT INTO " . $dbtable . "(widget_id, maintype, name, value) VALUES ('" . $myrow->widget_id . "', 'browser', 'msie6', '" . $myrow->value . "')";
					$wpdb->query($query);
				}
			}

/*    1.5b3 > Added support for widget display setting options for Tag Pages.
   		Need to apply archive rule to tag also to keep same behavior. */
			if ( version_compare($version, '1.5b3', '<') ) {
				$query = "SELECT widget_id FROM " . $dbtable . " WHERE maintype = 'archive'";
				$results = $wpdb->get_results($query);
				foreach ( $results as $myrow ) {
					$query = "INSERT INTO " . $dbtable . "(widget_id, maintype, value) VALUES ('" . $myrow->widget_id . "', 'tag', '0')";
					$wpdb->query($query);
				}
			}

			/*
			1.5.3.1 > Widgets seems to be started using longer classnames to avoid clashing.
			Widend up the width for widget_id from 40 to 60.
			*/
			if ( version_compare($version, '1.5.3.1', '<') ) {
				$query = "ALTER TABLE " . $dbtable . " CHANGE `widget_id` `widget_id` VARCHAR(60) NOT NULL";
				$wpdb->query($query);
			}

			/*
			 * 1.5.12.1 > All needs to widen up again. Moved it all to 100
			 */
			if ( version_compare($version, '1.5.12.1', '<') ) {
				$query = "ALTER TABLE " . $dbtable . " CHANGE `widget_id` `widget_id` VARCHAR(100) NOT NULL";
				$wpdb->query($query);

				$query = "ALTER TABLE " . $dbtable . " CHANGE `maintype` `maintype` VARCHAR(100)";
				$wpdb->query($query);

				$query = "ALTER TABLE " . $dbtable . " CHANGE `name` `name` VARCHAR(100)";
				$wpdb->query($query);
			}

		}
		update_option('dynwid_version', DW_VERSION);
	}

	/**
   * dynwid_add_admin_custom_box Adds meta boxes to Custom Post Types
   * @since 1.5.2.5
   */
	function dynwid_add_admin_custom_box() {
		$args = array(
			'public'   => TRUE,
			'_builtin' => FALSE
		);

		$post_types = get_post_types($args, 'objects', 'and');
		foreach ( array_keys($post_types) as $type ) {
			add_meta_box('dynwid', __('Dynamic Widgets', DW_L10N_DOMAIN), 'dynwid_add_post_control', $type, 'side', 'low');
		}
  }

	/**
   * dynwid_add_admin_help_tab() Add help tab for WP >= 3.3
   * @since 1.5.0
   */
	function dynwid_add_admin_help_tab() {
		$dw_admin_screen = $GLOBALS['dw_admin_screen'];
		$screen = get_current_screen();

		if ( $screen->id == $dw_admin_screen ) {
			// Contextual help
  		if ( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
  			$dw_help = dynwid_contextual_help_text('edit');
  		} else {
			$dw_help = dynwid_contextual_help_text('overview');
  		}

			$args = array(
				'id'	=> 'dw_help_tab',
				'title'	=> 'Dynamic Widgets',
				'content'	=> $dw_help
			);
			$screen->add_help_tab($args);
		}
	}

	/**
	* dynwid_add_admin_menu() Add plugin link to admin menu
	* @since 1.0
	*/
	function dynwid_add_admin_menu() {
		/** @var $DW DynWid */
		global $DW, $dw_admin_screen;

		$dw_admin_screen = add_submenu_page('themes.php', __('Dynamic Widgets', DW_L10N_DOMAIN), __('Dynamic Widgets', DW_L10N_DOMAIN), 'edit_theme_options', 'dynwid-config', 'dynwid_admin_page');

		if ( $DW->enabled ) {
			add_action('admin_print_styles-' . $dw_admin_screen, 'dynwid_add_admin_styles');
			add_action('admin_print_scripts-' . $dw_admin_screen, 'dynwid_add_admin_scripts');

			// Contextual help
			if ( isset($_GET['action']) && $_GET['action'] == 'edit' ) {
			   $dw_help = dynwid_contextual_help_text('edit');
			} else {
				$dw_help = dynwid_contextual_help_text('overview');
			}

			// Since WP 3.3 contextual help is handled different
			if ( version_compare($GLOBALS['wp_version'], '3.3', '>=') ) {
			   add_action('load-' . $dw_admin_screen, 'dynwid_add_admin_help_tab');
			} else {
			   add_contextual_help($dw_admin_screen, $dw_help);
			}

			// Only show meta box in posts panel when there are widgets enabled.
			$opt = $DW->getOpt('%','individual');
			if ( count($opt) > 0 ) {
			   add_meta_box('dynwid', __('Dynamic Widgets', DW_L10N_DOMAIN), 'dynwid_add_post_control', 'post', 'side', 'low');
			}
		}
	}

	/**
	* dynwid_add_admin_scripts() Enqueue jQuery UI scripts to admin page
	* @since 1.3
	*/
	function dynwid_add_admin_scripts() {
		/** @var $DW DynWid */
		global $DW;

		/*
		BuddyPress doing an overall JS enqueue (BAD!)
		Workaround fixing a js error with ui.accordion freezing the screen
		   - dtheme-ajax-js is used in BP default theme
		   - bp-js is used in BP Compatibility Plugin
		*/
		if ( wp_script_is('dtheme-ajax-js') ) {
			wp_deregister_script('dtheme-ajax-js');
		}

		if ( wp_script_is('bp-js') ) {
			wp_deregister_script('bp-js');
		}

		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-core');

		if ( version_compare(substr($GLOBALS['wp_version'], 0, 3), '3.1', '>=') ) {
			wp_enqueue_script('jquery-ui-widget');
			// wp_enqueue_script('jquery-ui-accordion', $DW->plugin_url . 'ui.accordion.1.8.7.js', array('jquery-ui-widget'));
			wp_enqueue_script('jquery-ui-datepicker', $DW->plugin_url . 'ui.datepicker.1.8.7.js', array('jquery-ui-widget'));
		} else {
			//  wp_enqueue_script('jquery-ui-accordion', $DW->plugin_url . 'ui.accordion.1.7.3.js', array('jquery-ui-core'));
			wp_enqueue_script('jquery-ui-datepicker', $DW->plugin_url . 'ui.datepicker.1.7.3.js', array('jquery-ui-core'));
		}
	}

	/**
	* dynwid_add_admin_styles() Enqueue CSS to admin page
	* @since 1.3
	*/
	function dynwid_add_admin_styles() {
		/** @var $DW DynWid */
		global $DW;

		if ( version_compare(substr($GLOBALS['wp_version'], 0, 3), '3.1', '>=') ) {
		   wp_enqueue_style('jquery-ui-custom', $DW->plugin_url . 'jquery-ui-1.8.7.custom.css');
		} else {
		   wp_enqueue_style('jquery-ui-custom', $DW->plugin_url . 'jquery-ui-1.7.3.custom.css');
		}
	}

	/**
	* dynwid_add_plugin_actions() Add settings link in WP plugin overview
	* @param array $all
	* @return array
	* @since 1.0
	*/
	function dynwid_add_plugin_actions($all) {
		$links = array();
		$links[ ] = '<a href="themes.php?page=dynwid-config">' . __('Settings') . '</a>';

		return array_merge($links, $all);
	}

	/**
	* dynwid_add_post_control() Add control widget to post screen
	* @since 1.2
	*/
	function dynwid_add_post_control() {
		/** @var $DW DynWid */
		global $DW, $post;

		$post_type = get_post_type($post->ID);
		if ( $post_type == 'post') {
			$post_type = 'single';
			$maintype = 'single-post';
		} else {
			$maintype = $post_type . '-post';
		}

		$opt = $DW->getOpt('%','individual');
		echo '<strong>' . __('Apply exception rule to widgets:', DW_L10N_DOMAIN) . '</strong><br /><br />';

		foreach ( $opt as $widget ) {
			$single_condition = '1';
			$checked = '';
			$opt_single = $DW->getOpt($widget->widget_id, $post_type);

			// loop through the opts to see if we have a match
			foreach ( $opt_single as $widget_opt ) {
				if ( $widget_opt->maintype == 'single' ) {
					$single_condition = $widget_opt->value;
				}

				if ( $widget_opt->maintype == $maintype && $widget_opt->name == $post->ID ) {
					$checked = ' checked="checked"';
				}
			}

			$default = ( $single_condition == '0' ) ? __('Off', DW_L10N_DOMAIN) : __('On', DW_L10N_DOMAIN);
			// echo '<input type="checkbox" id="dw_' . $widget->widget_id . '" name="dw-single-post[]" value="' . $widget->widget_id . '"' . $checked . ' /> <label for="dw_' . $widget->widget_id . '">' . $DW->getName($widget->widget_id) . __(' (Default: ', DW_L10N_DOMAIN) . $default . ')</label><br />';
			echo '<input type="checkbox" id="dw_' . $widget->widget_id . '" name="dw-single-post[]" value="' . $widget->widget_id . '"' . $checked . ' /> <label for="dw_' . $widget->widget_id . '">' . apply_filters( 'dynwid_post_control_label', $DW->getName($widget->widget_id) . __(' (Default: ', DW_L10N_DOMAIN) . $default . ')', $widget, $default ) . '</label><br />';
		}
	}

	/**
	* dynwid_add_tag_page() Add row to WP tags admin
	* @since 1.2
	*/
	function dynwid_add_tag_page() {
		/** @var $DW DynWid */
		global $DW;

		// Only show dynwid row when there are widgets enabled
		$opt = $DW->getOpt('%','individual');
		if ( count($opt) > 0 ) {

			echo '<tr class="form-field">';
			echo '<th scope="row" valign="top"><label for="dynamic-widgets">' . __('Dynamic Widgets', DW_L10N_DOMAIN) . '</label></th>';
			echo '<td>';

			foreach ( $opt as $widget ) {
				$single_condition = '1';
				$checked = '';
				$opt_single = $DW->getOpt($widget->widget_id, 'single');

				// loop through the opts to see if we have a match
				foreach ( $opt_single as $widget_opt ) {
					if ( $widget_opt->maintype == 'single' ) {
						$single_condition = $widget_opt->value;
					}

					if ( $widget_opt->maintype == 'single-tag' && $widget_opt->name == $_GET['tag_ID'] ) {
						$checked = ' checked="checked"';
					}
				}

				$default = ( $single_condition == '0' ) ? __('Off', DW_L10N_DOMAIN) : __('On', DW_L10N_DOMAIN);
				echo '<input type="checkbox" style="width:10pt;border:none;" id="dw_' . $widget->widget_id . '" name="dw-single-tag[]" value="' . $widget->widget_id . '"' . $checked . ' /> <label for="dw_' . $widget->widget_id . '">' . $DW->getName($widget->widget_id) . ' (' . __('Default', DW_L10N_DOMAIN) . ': ' . $default . ')</label><br />';

			} // END foreach opt

			echo '</td>';
			echo '</tr>';
		}
	}

	/**
	* dynwid_add_widget_control() Preparation for callback hook into WP widgets admin
	* @since 1.2
	*/
	function dynwid_add_widget_control() {
		/** @var $DW DynWid */
		global $DW;;

		/*
		Hooking into the callback of the widgets by moving the existing callback to wp_callback
		and setting callback with own callback function.
		We need the widget_id registered in params also for calling own callback.
		*/
		foreach ( $DW->registered_widgets as $widget_id => $widget ) {
			if ( array_key_exists($widget_id, $DW->registered_widget_controls) ) {
				$DW->registered_widget_controls[$widget_id]['wp_callback'] = $DW->registered_widget_controls[$widget_id]['callback'];
				$DW->registered_widget_controls[$widget_id]['callback'] = 'dynwid_widget_callback';

				/*
				 In odd cases params and/or params[0] seems not to be an array. Bugfix for:
				 Warning: Cannot use a scalar value as an array in ./wp-content/plugins/dynamic-widgets/dynamic-widgets.php
				*/

				/* Fixing params */
				if (! is_array($DW->registered_widget_controls[$widget_id]['params']) ) {
					$DW->registered_widget_controls[$widget_id]['params'] = array();
				}

				if ( count($DW->registered_widget_controls[$widget_id]['params']) == 0 ) {
					$DW->registered_widget_controls[$widget_id]['params'][ ] = array('widget_id' => $widget_id);
					// Fixing params[0]
				} else if (! is_array($DW->registered_widget_controls[$widget_id]['params'][0]) ) {
					$DW->registered_widget_controls[$widget_id]['params'][0] = array('widget_id' => $widget_id);
				} else {
					$DW->registered_widget_controls[$widget_id]['params'][0]['widget_id'] = $widget_id;
				}
			}
		}

		// Notifying user when options are saved and returned to ./wp-admin/widgets.php
		if ( isset($_GET['dynwid_save']) && $_GET['dynwid_save'] == 'yes' ) {
			add_action('sidebar_admin_page', 'dynwid_add_widget_page');
		}
	}

	/**
	* dynwid_add_widget_page() Save success message for WP widgets admin
	 * @since 1.2
	*/
	function dynwid_add_widget_page() {
		/** @var $DW DynWid */
		global $DW;;

		$name = strip_tags($DW->getName($_GET['widget_id']));
		$lead = __('Dynamic Widgets Options saved', DW_L10N_DOMAIN);
		$msg = __('for', DW_L10N_DOMAIN) . ' ' .  $name;

		DWMessageBox::create($lead, $msg);
	}

	/**
	* dynwid_admin_dump() Dump function
	* @since 1.0
	*/
	function dynwid_admin_dump() {
		/** @var $DW DynWid */
		global $DW;

		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=dynwid_dump_' . date('Ymd') . '.txt' );
		header('Content-Type: text/plain');

		$DW->dump();
		die();
	}

	/**
	* dynwid_admin_page() Admin pages
	* @since 1.0
	*/
	function dynwid_admin_page() {
		/** @var $DW DynWid */
		global $DW;;

		require_once( dirname(__FILE__) . '/dynwid_admin.php' );
	}

	/**
	 * dynwid_admin_wpec_dump() Dump WPEC rules function for upgrade to 3.8
	 * @since 1.4.0
	 */
	function dynwid_admin_wpec_dump() {
		$DW = &$GLOBALS['DW'];
		$wpdb = &$GLOBALS['wpdb'];
		$dump = array();

		$opt = $DW->getOpt('%', 'wpsc');

		$categories = array();
		$table = WPSC_TABLE_PRODUCT_CATEGORIES;
		$fields = array('id', 'name');
		$query = "SELECT " . implode(', ', $fields) . " FROM " . $table . " WHERE active = '1' ORDER BY name";
		$results = $wpdb->get_results($query);

		foreach ( $results as $myrow ) {
		   $categories[$myrow->id] = $myrow->name;
		}

		foreach ( $opt as $widget ) {
		   $id = $widget->widget_id;

		   if (! array_key_exists($id, $dump) ) {
		      $dump[$id] = array( 'name' => strip_tags($DW->getName($widget->widget_id)) );
		   }

		   if ( $widget->name == 'default' ) {
		      $dump[$id]['default'] = ( $widget->value == '0' ? 'No' : 'Yes' );
		   } else {
		      $v = $widget->name;
		      $dump[$id][ ] = $categories[$v];
		   }
		}

		header('Content-Description: File Transfer');
		header('Content-Disposition: attachment; filename=dynwid_wpec_dump_' . date('Ymd') . '.txt' );
		header('Content-Type: text/plain');

		foreach ( $dump as $widget ) {
			echo 'Widget: ' . $widget['name'] . "\r\n";
			echo 'Default set to ' . $widget['default'] . "\r\n";

			if ( count($widget) > 2 ) {
			   echo 'Categories ticked: ' . "\r\n";
			   foreach ( $widget as $k => $v ) {
			      if ( is_int($k) ) {
			         echo "\t" . $v . "\r\n";
			      }
			   }
			}

			echo "\r\n";
		}

		die();
	}

	/**
	* dynwid_contextual_help_text() Actual text to place into the contextual help screen
	* @param string $screen
	* @return string
	* @since 1.5.0
	*
	*/
	function dynwid_contextual_help_text($screen) {
		$DW = &$GLOBALS['DW'];

		// Contextual help
		if ( $screen == 'edit' ) {
			$dw_help  = __('Widgets are always displayed by default', DW_L10N_DOMAIN) . ' (' . __('The \'<em>Yes</em>\' selection', DW_L10N_DOMAIN) . ')'  . '<br />';
			$dw_help .= __('Click on the', DW_L10N_DOMAIN) . ' <img src="' . $DW->plugin_url . 'img/info.gif" alt="info" /> ' . __('next to the options for more info', DW_L10N_DOMAIN) . '.<br />';
			$dw_help .= __('The') . ' <img src="' . $DW->plugin_url . 'img/checkmark.gif" alt="Checkmark" /> ' . __('next to a section means it has options set.', DW_L10N_DOMAIN);
		} else {
			$dw_help  = '<p><strong>' . __('Static', DW_L10N_DOMAIN) . ' / ' . __('Dynamic', DW_L10N_DOMAIN) . '</strong><br />';
			$dw_help .= __('When a widget is', DW_L10N_DOMAIN) . ' <em>' . __('Static', DW_L10N_DOMAIN) . '</em>, ' . __('the widget uses the WordPress default. In other words, it\'s shown everywhere', DW_L10N_DOMAIN) . '.<br />';
			$dw_help .=  __('A widget is', DW_L10N_DOMAIN) . ' <em>' . __('Dynamic', DW_L10N_DOMAIN) . '</em> ' . __('when there are options set, i.e. not showing on the front page.', DW_L10N_DOMAIN) . '</p>';
			$dw_help .= '<p><strong>' . __('Reset', DW_L10N_DOMAIN) . '</strong><br />';
			$dw_help .= __('Reset makes the widget return to', DW_L10N_DOMAIN) . ' <em>' . __('Static', DW_L10N_DOMAIN) . '</em>.</p>';
		}

		return $dw_help;
	}

	/**
	* dynwid_disabled_add_admin_menu() Menu entry for disabled page.
	* @since 1.5.6.1
	*
	*/
	function dynwid_disabled_add_admin_menu() {
		add_submenu_page('themes.php', __('Dynamic Widgets', DW_L10N_DOMAIN), __('Dynamic Widgets', DW_L10N_DOMAIN), 'edit_theme_options', 'dynwid-config', 'dynwid_disabled_page');
	}

	/**
	* dynwid_disabled_page() Error boxes to show in admin when DW can not be initialised due to not meeting sysreq.
	* @since 1.5b1
	*
	*/
	function dynwid_disabled_page() {
		// As the DWMessagebox class is not loaded, we can not use it
		$php = version_compare(PHP_VERSION, DW_MINIMUM_PHP, '>=');
		$wp = version_compare($GLOBALS['wp_version'], DW_MINIMUM_WP, '>=');

		if (! $php ) {
		   echo '<div class="error" id="message"><p>';
		   _e('<b>ERROR</b> Your host is running a too low version of PHP. Dynamic Widgets needs at least version', DW_L10N_DOMAIN);
		   echo ' ' . DW_MINIMUM_PHP . '.';
		   echo '</p></div>';
		}

		if (! $wp ) {
		   echo '<div class="error" id="message"><p>';
		   _e('<b>ERROR</b> Your host is running a too low version of WordPress. Dynamic Widgets needs at least version', DW_L10N_DOMAIN);
		   echo ' ' . DW_MINIMUM_WP . '.';
		   echo '</p></div>';
		}
	}

	/**
	* dynwid_filter_init() Init of the worker
	* @since 1.3.5
	*/
	function dynwid_filter_init() {
		$DW = &$GLOBALS['DW'];
		require(dirname(__FILE__) . '/dynwid_init_worker.php');
	}

	/**
	* dynwid_filter_widgets() Worker
	* @since 1.3.5
	*/
	function dynwid_filter_widgets() {
		$DW = &$GLOBALS['DW'];

		dynwid_filter_init();
		if ( DW_OLD_METHOD ) {
		   dynwid_worker($DW->sidebars);
		} else {
		   add_filter('sidebars_widgets', 'dynwid_worker');
		}
	}

	/**
	* dynwid_init() Init of the plugin
	* @since 1.0
	*/
	function dynwid_init() {
		$php = version_compare(PHP_VERSION, DW_MINIMUM_PHP, '>=');
		$wp = version_compare($GLOBALS['wp_version'], DW_MINIMUM_WP, '>=');

		if ( $php && $wp ) {
		   $GLOBALS['DW'] = new dynWid();
		   $DW = &$GLOBALS['DW'];
		   $DW->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename(__FILE__), '', plugin_basename(__FILE__) );

		   if ( is_admin() ) {
		      if ( isset($_POST['dynwid_save']) && $_POST['dynwid_save'] == 'yes' ) {
		         require_once(dirname(__FILE__) . '/dynwid_admin_save.php');
		      }

		      load_plugin_textdomain(DW_L10N_DOMAIN, FALSE, dirname(plugin_basename(__FILE__)) . '/locale');
		      add_action('admin_menu', 'dynwid_add_admin_menu');

		      if ( $DW->enabled ) {
		         add_action('add_meta_boxes', 'dynwid_add_admin_custom_box');
		         add_action('edit_tag_form_fields', 'dynwid_add_tag_page');
		         add_action('edited_term', 'dynwid_save_tagdata');
		         add_action('plugin_action_links_' . plugin_basename(__FILE__), 'dynwid_add_plugin_actions');
		         add_action('save_post', 'dynwid_save_postdata');
		         add_action('sidebar_admin_setup', 'dynwid_add_widget_control');
			      // add_action('widgets_admin_page', 'dynwid_widgets_admin_page');

		         // AJAX calls
		         add_action('wp_ajax_term_tree', 'dynwid_term_tree');
		      }
		   } else {
		      if ( $DW->enabled ) {
		         add_action('wp_head', 'dynwid_filter_widgets');
		      }
		   }
		} else {
		   if ( is_admin() ) {
		      // Show errors in the admin page
		      add_action('admin_menu', 'dynwid_disabled_add_admin_menu');
		   }
		}
	}

	/**
	 * dynwid_install() Installation
	 * @since 1.3.1
	 */
	function dynwid_install() {
		if ( function_exists('is_multisite') ) {
			if ( is_multisite() && isset($_GET['networkwide']) && $_GET['networkwide'] == '1' ) {
				$plugin = plugin_basename(__FILE__);
				deactivate_plugins($plugin);
			} else {
				dynwid_activate();
			}
		} else {
			dynwid_activate();
		}
	}

	/**
	 * dynwid_save_postdata() Save of options via post screen
	 * @param int $post_id
	 * @since 1.2
	 */
	function dynwid_save_postdata($post_id) {
		global $DW;

	  if ( $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] != 'autosave' ) {
	  	$post_id = ( isset($_POST['post_ID']) && ! empty($_POST['post_ID']) ) ? intval($_POST['post_ID']) : 0;

	  	if ( $parent_id = wp_is_post_revision($post_id) ) {
				$post_id = $parent_id;
			}

			if ( $post_id > 0 ) {
				$post_type = get_post_type($post_id);
				if ( $post_type == 'post') {
					$post_type = 'single';
					$maintype = 'single-post';
				} else {
					$maintype = $post_type . '-post';
				}

			  // Housekeeping
			  $opt = $DW->getOpt('%','individual');
			  foreach ( $opt as $widget ) {
			    $DW->deleteOption($widget->widget_id, $maintype, $post_id);
			  }

			  if ( array_key_exists('dw-single-post', $_POST) ) {
			    $opt = $_POST['dw-single-post'];
			    $default = 'yes';
			    $default_single = '1';

			    foreach ( $opt as $widget_id ) {
			      $opt_single = $DW->getOpt($widget_id, $post_type);
			      if ( count($opt_single) > 0 ) {
			        foreach ( $opt_single as $widget ) {
			          if ( $widget->maintype == $post_type ) {
			            $default_single = $widget->value;
			          }
			        }

			        if ( $default_single == '0' ) {
			          $default = 'no';
			        }
			      }

			      $DW->addMultiOption($widget_id, $maintype, $default, array($post_id));
			    }
			  } // END if array_key_exists
			} // END if $post_id > 0
		} // END if ! autosave AND ! quick edit
	}

	/**
	 * dynwid_save_tagdata() Save of tagdata
	 * @param int $term_id
	 * @since 1.2
	 */
	function dynwid_save_tagdata($term_id) {
	  // Only act when tag is updated via 'edit', NOT via 'quick edit'
	  if ( $_POST['action'] == 'editedtag' ) {
	    $DW = &$GLOBALS['DW'];

	    if ( array_key_exists('tag_ID', $_POST) ) {
	      $term_id = $_POST['tag_ID'];
	    }

	    // Housekeeping
	    $opt = $DW->getOpt('%', 'individual');
	    foreach ( $opt as $widget ) {
	      $DW->deleteOption($widget->widget_id, 'single-tag', $term_id);
	    }

	    if ( array_key_exists('dw-single-tag', $_POST) ) {
	      $opt = $_POST['dw-single-tag'];
	      $default = 'yes';
	      $default_single = '1';

	      foreach ( $opt as $widget_id ) {
	        $opt_single = $DW->getOpt($widget_id, 'single');
	        if ( count($opt_single) > 0 ) {
	          foreach ( $opt_single as $widget ) {
	            if ( $widget->maintype == 'single' ) {
	              $default_single = $widget->value;
	            }
	          }
	        }
	        $DW->addMultiOption($widget_id, 'single-tag', $default, array($term_id));
	      }
	    } // END if array_key_exists
	  } // END if action
	}

	/**
	 * dynwid_sql_mode() Internal check for STRICT sql mode
	 * @since 1.3.6
	 */
	function dynwid_sql_mode() {
		$wpdb = $GLOBALS['wpdb'];
		$strict_mode = array('STRICT_TRANS_TABLES', 'STRICT_ALL_TABLES');

		$query = "SELECT @@GLOBAL.sql_mode";
		$result = $wpdb->get_var($query);
		$sql_global = explode(',', $result);

		$query = "SELECT @@SESSION.sql_mode";
		$result =  $wpdb->get_var($query);
		$sql_session = explode(',', $result);

		$sqlmode = array_merge($sql_global, $sql_session);
		if ( (bool) array_intersect($sql_session, $strict_mode) ) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * dynwid_term_tree() AJAX lazy loader for Taxonomy terms tree
	 * @since 1.5.4.2
	 *
	 * @return void
	 */
	function dynwid_term_tree() {
		global $DW;
		if ( empty($_POST['nonce']) || ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
			die ( 'Busted!');
		}
		include_once(DW_MODULES . 'custompost_module.php');
		
		$id = ( isset($_POST['id']) && ! empty($_POST['id']) ) ? sanitize_text_field( $_POST['id'] ) : 0;
		$name = ( isset($_POST['name']) && ! empty($_POST['name']) ) ? sanitize_text_field( $_POST['name'] ) : '';
		$prefix = ( isset($_POST['prefix']) && ! empty($_POST['prefix']) ) ? sanitize_text_field( $_POST['prefix'] ) : '';
		$widget_id = ( isset($_POST['widget_id']) && ! empty($_POST['widget_id']) ) ? sanitize_text_field( $_POST['widget_id'] ) : '';

		if ( intval($id) > 0 && ! empty($name) && ! empty($widget_id) ) {
			$opt_tax = $DW->getDWOpt($widget_id, $prefix);
			$opt_tax_childs = $DW->getDWOpt($widget_id, $prefix . '-childs');

			$tree = DW_CustomPost::getTaxChilds($name, array(), $id, array());
			if ( count($tree) > 0 ) {
				DW_CustomPost::prtTax($widget_id, $name, $tree, $opt_tax->act, $opt_tax_childs->act, $prefix);
			}
		}

		die();
	}

	/**
	 * dynwid_uninstall() Uninstall
	 * @since 1.0
	 */
	function dynwid_uninstall() {
		global $wpdb;

		$dbtable = $wpdb->prefix . DW_DB_TABLE;

		// Housekeeping
		delete_option('dynwid_housekeeping_lastrun');
		delete_option('dynwid_old_method');
		delete_option('dynwid_version');

		$query = "DROP TABLE IF EXISTS " . $dbtable;
		$wpdb->query($query);

		$plugin = plugin_basename(__FILE__);

		/* Shamelessly ripped from /wp-admin/plugins.php */
		deactivate_plugins($plugin);
		update_option('recently_activated', array($plugin => time()) + (array) get_option('recently_activated'));
		wp_redirect('plugins.php?deactivate=true&plugin_status=' . $status . '&paged=' . $page);

      die();
	}

	function dynwid_widgets_admin_page() {
		add_thickbox();
	}

	/**
	 * dynwid_widget_callback() Callback function for hooking into WP widgets admin
	 * @since 1.2
	 */
	function dynwid_widget_callback() {
		global $DW;

		$DW->loadModules();
		$DW->getModuleName();

		$args = func_get_args();
		$widget_id = $args[0]['widget_id'];
		$wp_callback = $DW->registered_widget_controls[$widget_id]['wp_callback'];

		// Calling original callback first
		call_user_func_array($wp_callback, $args);

		// Now adding the dynwid text & link
		echo '<p>' . __('Dynamic Widgets', DW_L10N_DOMAIN) . ': ';

		if ( array_key_exists($widget_id, $DW->registered_widgets) ) {
			echo '<a style="text-decoration:none;" title="' . __('Edit Dynamic Widgets Options', DW_L10N_DOMAIN) . '" href="themes.php?page=dynwid-config&action=edit&id=' . $widget_id . '&returnurl=widgets.php' . '">';
			// echo '<a style="text-decoration:none;" title="' . __('Edit Dynamic Widgets Options', DW_L10N_DOMAIN) . '" href="' . admin_url( 'themes.php?page=dynwid-config&action=edit&id=' . $widget_id . '&TB_iframe=true&width=&height=' ) . '" class="thickbox">';
			echo ( $DW->hasOptions($widget_id) ) ? __('Dynamic', DW_L10N_DOMAIN) : __('Static', DW_L10N_DOMAIN);
			echo '</a>';

			if ( $DW->hasOptions($widget_id) ) {
				$s = array();
				$opt = $DW->getOpt($widget_id, NULL);

				foreach ( $opt as $widget ) {
					$type = $widget->maintype;

					if ( $type != 'individual' && substr($type, -6) != 'childs' && ! preg_match('/.*-tax_.*/', $type) ) {
						$single = array('single-author', 'single-category', 'single-tag', 'single-post');

						if ( in_array($type, $single) ) {
							$type = 'single';
						}

						if (! in_array($type, $s) ) {
						   $s[ ] = $type;
						}
					}
				}

				$last = count($s) - 1;
				$string = '';

				for ( $i = 0; $i < $last; $i++ ) {
					$type = $s[$i];
					if (! empty($DW->dwoptions[$type]) ) {
						$string .= $DW->dwoptions[$type];
					}

					$string .= ( ($last - 1) == $i ) ? ' ' . __('and', DW_L10N_DOMAIN) . ' ' : ', ';
				}

				$type = $s[$last];
				if ( isset($DW->dwoptions[$type]) ) {
					$string .= $DW->dwoptions[$type];
				}

				$output  = '<br /><small>';
				$output .= ( count($opt) > 1 ) ? __('Options set for', DW_L10N_DOMAIN) : __('Option set for', DW_L10N_DOMAIN);
				$output .= ' ' . $string . '.</small>';
				echo $output;
			}
		} else {
		  echo '<em>' . __('Save the widget first', DW_L10N_DOMAIN) . '...</em>';
		}

		echo '</p>';
	}

	/**
	 * dynwid_worker() Worker process
	 *
	 * @param array $sidebars
	 * @return array
	 * @since 1.0
	 */
	function dynwid_worker($sidebars) {
	  $DW = &$GLOBALS['DW'];

		if ( $DW->listmade ) {
			$DW->message('Dynamic Widgets removelist already created');
			if ( count($DW->removelist) > 0 ) {
				foreach ( $DW->removelist as $sidebar_id => $widgets ) {
					foreach ( $widgets as $widget_key ){
						unset($sidebars[$sidebar_id][$widget_key]);
					}
				}
			}
		} else {
			require(dirname(__FILE__) . '/dynwid_worker.php');
		}

		return $sidebars;
	}

	// Hooks
	add_action('admin_action_dynwid_dump', 'dynwid_admin_dump');
	add_action('admin_action_wpec_dump', 'dynwid_admin_wpec_dump');
	add_action('admin_action_dynwid_uninstall', 'dynwid_uninstall');
	add_action('init', 'dynwid_init');
	register_activation_hook(__FILE__, 'dynwid_install');
?>
