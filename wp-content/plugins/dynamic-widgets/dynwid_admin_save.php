<?php
	/**
	 * dynwid_admin_save.php - Saving options to the database
	 *
	 * @version $Id: dynwid_admin_save.php 2624427 2021-11-04 10:33:06Z qurl $
	 * @copyright 2011 Jacco Drabbe
	 */

	defined('ABSPATH') or die("No script kiddies please!");

	// Security - nonce, etc.
	$widget_id = ( isset($_POST['widget_id']) && ! empty($_POST['widget_id']) ) ? esc_attr($_POST['widget_id']) : '';
	$returnurl = ( isset($_POST['returnurl']) && ! empty($_POST['returnurl']) ) ? esc_url($_POST['returnurl']) : '';

	// In some cases $widget_id appears not to be global (anymore)
	$GLOBALS['widget_id'] = $widget_id;

	check_admin_referer('plugin-name-action_edit_' . $widget_id);
	if ( !array_key_exists($widget_id, $DW->registered_widgets) ) {
		wp_die('WidgetID is not valid');
	}

	/* Checking basic stuff */
	$DW->registerOverrulers();
	foreach ( $DW->overrule_maintype as $o ) {
		if ( $o != 'date' && $o != 'url' ) {
			$act_field = $o . '_act';
			if ( isset($_POST[$act_field]) ) {
				if ( $_POST[$o] == 'no' && is_array($_POST[$act_field]) && count($_POST[$act_field]) == 0 ) {
					wp_redirect($_SERVER['REQUEST_URI'] . '&work=none');
					die();
				}
			}
		}
	}

	// Date check
	if ( $_POST['date'] == 'no' ) {
		$date_start = trim(esc_attr($_POST['date_start']));
		$date_end = trim(esc_attr($_POST['date_end']));

		if ( ! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_start) && ! preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_end) ) {
			wp_redirect($_SERVER['REQUEST_URI'] . '&work=none');
			die();
		}

		if ( ! empty($date_start) ) {
			@list($date_start_year, $date_start_month, $date_start_day) = explode('-', $date_start);
			if ( !checkdate($date_start_month, $date_start_day, $date_start_year) ) {
				unset($date_start);
			}
		}
		if ( ! empty($date_end) ) {
			@list($date_end_year, $date_end_month, $date_end_day) = explode('-', $date_end);
			if ( !checkdate($date_end_month, $date_end_day, $date_end_year) ) {
				unset($date_end);
			}
		}

		if ( ! empty($date_start) && ! empty($date_end) ) {
			if ( mktime(0, 0, 0, $date_start_month, $date_start_day, $date_start_year) > mktime(0, 0, 0, $date_end_month, $date_end_day, $date_end_year) ) {
				wp_redirect($_SERVER['REQUEST_URI'] . '&work=nonedate');
				die();
			}
		}
	}

	// URL
	if ( $_POST['url'] == 'no' && empty($_POST['url_value']) ) {
		wp_redirect($_SERVER['REQUEST_URI'] . '&work=none');
		die();
	}

	// Domain
	if ( $_POST['domain'] == 'no' && empty($_POST['domain_value']) ) {
		wp_redirect($_SERVER['REQUEST_URI'] . '&work=none');
		die();
	}

	// IP
	if ( $_POST['ip'] == 'no' && empty($_POST['ip_value']) ) {
		wp_redirect($_SERVER['REQUEST_URI'] . '&work=none');
		die();
	}

	// Removing already set options, but keeping individual rules
	$dbtable = $GLOBALS['wpdb']->prefix . DW_DB_TABLE;
	$query = "SELECT COUNT(1) AS total FROM " . $dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = 'individual'";
	$count = $GLOBALS['wpdb']->get_var($query);

	if ( $count > 0 && isset($_POST['individual']) && $_POST['individual'] == '1' ) {
		$post_types = ( is_array($_POST['post_types']) ) ? $_POST['post_types'] : array();
		$post_types = array_merge(array( 'single_post', 'single_tag' ), $post_types);

		foreach ( $post_types as $t ) {
			$maintype = ( !preg_match('/^single/', $t) ) ? $t . '-post' : $t;

			$query = "SELECT name FROM " . $dbtable . " WHERE widget_id = '" . $widget_id . "' AND maintype = '" . $maintype . "'";
			$results = $GLOBALS['wpdb']->get_results($query);

			foreach ( $results as $row ) {
				if ( is_numeric($row->name) ) {
					$a = $maintype . '_act';

					if ( !is_array($_POST[$a]) ) {
						$_POST[$a] = array();
					}

					$_POST[$a][] = $row->name;
				}
			}

		}

	}

	$DW->resetOptions($widget_id);

	// Role
	DWModule::save('role', 'complex');

	// Date
	if ( $_POST['date'] == 'no' ) {
		$dates = array();
		if ( preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_start) ) {
			$dates['date_start'] = $date_start;
		}
		if ( preg_match('/^[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}$/', $date_end) ) {
			$dates['date_end'] = $date_end;
		}

		if ( count($dates) > 0 ) {
			$DW->addDate($widget_id, $dates);
		}
	}

	// Day
	DWModule::save('day', 'complex');

	// Week
	DWModule::save('week', 'complex');

	// Browser
	DWModule::save('browser', 'complex');

	// Device
	DWModule::save('device', 'complex');

	// Template
	DWModule::save('tpl', 'complex');

	// URL
	if ( ! empty($_POST['url_value']) ) {
		$urls = array();

		$url_values = trim($_POST['url_value']);
		$url_values = str_replace("\r", "", $url_values);
		$url_values = explode("\n", $url_values);

		foreach ( $url_values as $url ) {
			$url = trim($url);
			if ( ! empty($url) ) {
				$urls[] = $url;
			}
		}

		if ( count($urls) > 0 ) {
			$DW->addUrls($widget_id, $_POST['url'], $urls);
		}
	}

	// URL
	if ( ! empty($_POST['domain_value']) ) {
		$domains = array();

		$domain_values = trim($_POST['domain_value']);
		$domain_values = str_replace("\r", "", $domain_values);
		$domain_values = explode("\n", $domain_values);

		foreach ( $domain_values as $domain ) {
			$url = trim($domain);
			if ( ! empty($domain) ) {
				$domains[] = $domain;
			}
		}

		if ( count($domains) > 0 ) {
			$DW->addDomains($widget_id, $_POST['domain'], $domains);
		}
	}

	// IP
	if ( ! empty($_POST['ip_value']) ) {
		$ips = array();

		$ip_values = trim($_POST['ip_value']);
		$ip_values = str_replace("\r", "", $ip_values);
		$ip_values = explode("\n", $ip_values);

		foreach ( $ip_values as $ip ) {
			$ip = trim($ip);
			if ( ! empty($ip) ) {
				$ips[] = $ip;
			}
		}

		if ( count($ips) > 0 ) {
			$DW->addIPs($widget_id, $_POST['ip'], $ips);
		}
	}

	// Featured Image
	DWModule::save('fimage');

	// Shortcode
	if ( ! empty($_POST['shortcode_value']) ) {
		$value = sanitize_text_field($_POST['shortcode_value']);
		if ( ! empty($value) && substr($value, 0, 1) !== '[' && substr($value, strlen($value) - 1) !== ']' ) {
			$value = '[' . $value . ']';
		}

		$match = sanitize_text_field($_POST['shortcode_match']);
		$operator = $_POST['shortcode_operator'];
		if ( !in_array($operator, array( '=', '!=' )) ) {
			$operator = '=';
		}

		if ( ! empty($value) ) {
			$DW->addShortcode($widget_id, $_POST['shortcode'], $value, $match, $operator);
		}
	}

	// Front Page
	DWModule::save('front-page', 'complex');

	// Single Post
	DWModule::save('single');

	// -- Post Taxonomies
	if ( isset($_POST['single_tax_list']) && is_array($_POST['single_tax_list']) && count($_POST['single_tax_list']) > 0 ) {
		foreach ( $_POST['single_tax_list'] as $tax ) {
			$tax = sanitize_text_field($tax);
			$act_tax_field = $tax . '_act';
			// if ( isset($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 ) {
			if ( isset($_POST[$act_tax_field]) && !empty($_POST[$act_tax_field]) ) {
				if ( substr($_POST[$act_tax_field], 0, 1) == ',' ) {
					$_POST[$act_tax_field] = substr($_POST[$act_tax_field], 1);
				}
				$t = explode(',', $_POST[$act_tax_field]);
				$t = array_unique($t);

				$DW->addMultiOption($widget_id, $tax, $_POST['single'], $t);
			}

			// ---- Childs >> Can't use DWModule::childSave() cause of $name != $tax, but $name == 'post'
			$act_tax_childs_field = $tax . '_childs_act';
			// if ( isset($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 && isset($_POST[$act_tax_childs_field]) && count($_POST[$act_tax_childs_field]) > 0 ) {
			if ( isset($_POST[$act_tax_field]) && ! empty($_POST[$act_tax_field]) && isset($_POST[$act_tax_childs_field]) && ! empty($_POST[$act_tax_childs_field]) ) {
				if ( substr($_POST[$act_tax_childs_field], 0, 1) == ',' ) {
					$_POST[$act_tax_childs_field] = substr($_POST[$act_tax_childs_field], 1);
				}
				$t_childs = explode(',', $_POST[$act_tax_childs_field]);
				$t_childs = array_unique($t_childs);

				$DW->addChilds($widget_id, $tax . '-childs', $_POST['single'], $t, $t_childs);
			}
		}
	}

	// -- Author
	if ( isset($_POST['single_author_act']) && is_array($_POST['single_author_act']) && count($_POST['single_author_act']) > 0 ) {
		if ( $_POST['single'] == 'yes' ) {
			$DW->addSingleOption($widget_id, 'single', '1');
		}
		$DW->addMultiOption($widget_id, 'single-author', $_POST['single'], $_POST['single_author_act']);
	}

	// -- Category
	if ( isset($_POST['single_category_act']) && is_array($_POST['single_category_act']) && count($_POST['single_category_act']) > 0 ) {
		if ( $_POST['single'] == 'yes' && isset($_POST['single_author_act']) && count($_POST['single_author_act']) == 0 ) {
			$DW->addSingleOption($widget_id, 'single', '1');
		}
		$DW->addMultiOption($widget_id, 'single-category', $_POST['single'], $_POST['single_category_act']);
	}

	// ---- Childs
	if ( isset($_POST['single_category_act']) && is_array($_POST['single_category_act']) && count($_POST['single_category_act']) > 0 && isset($_POST['single_category_childs_act']) && is_array($_POST['single_category_childs_act']) && count($_POST['single_category_childs_act']) > 0 ) {
		$DW->addChilds($widget_id, 'single-category-childs', $_POST['single'], $_POST['single_category_act'], $_POST['single_category_childs_act']);
	}

	// -- Individual / Posts / Tag
	if ( isset($_POST['individual']) && $_POST['individual'] == '1' ) {
		$DW->addSingleOption($widget_id, 'individual', '1');
		if ( isset($_POST['single_post_act']) && is_array($_POST['single_post_act']) && count($_POST['single_post_act']) > 0 ) {
			$DW->addMultiOption($widget_id, 'single-post', $_POST['single'], $_POST['single_post_act']);
		}
		if ( isset($_POST['single_tag_act']) && is_array($_POST['single_tag_act']) && count($_POST['single_tag_act']) > 0 ) {
			$DW->addMultiOption($widget_id, 'single-tag', $_POST['single'], $_POST['single_tag_act']);
		}
	}

	// Attachment
	DWModule::save('attachment');

	// Pages
	// Go through the page_tax_list - Workaround as for some reason get_object_taxonomies() is not always filled
	$page_taxonomy = FALSE;
	$page_tax_list = array();
	if ( isset($_POST['page_tax_list']) && is_array($_POST['page_tax_list']) && count($_POST['page_tax_list']) > 0 ) {
		foreach ( $_POST['page_tax_list'] as $tax ) {
			$tax = sanitize_text_field($tax);
			$act_tax_field = $tax . '_act';
			if ( isset($_POST[$act_tax_field]) && is_array($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 ) {
				$page_taxonomy = TRUE;
				break;
			}
		}
	}

	if ( ( isset($_POST['page_act']) && is_array($_POST['page_act']) && count($_POST['page_act']) > 0 ) || $page_taxonomy ) {
		// When $page_taxonomy = TRUE
		if ( !isset($_POST['page_act']) ) {
			$_POST['page_act'] = array();
		}

		$DW->addMultiOption($widget_id, 'page', $_POST['pg'], $_POST['page_act']);
	} else if ( $_POST['pg'] == 'no' ) {
		$DW->addSingleOption($widget_id, 'page');
	}

	// -- Childs
	DWModule::childSave('page');

	// -- Page Taxonomies
	if ( isset($_POST['page_tax_list']) && is_array($_POST['page_tax_list']) && count($_POST['page_tax_list']) > 0 ) {
		foreach ( $_POST['page_tax_list'] as $tax ) {
			$tax = sanitize_text_field($tax);

			$act_tax_field = $tax . '_act';
			if ( isset($_POST[$act_tax_field]) && ! empty($_POST[$act_tax_field]) ) {

				if ( substr($_POST[$act_tax_field], 0, 1) == ',' ) {
					$_POST[$act_tax_field] = substr($_POST[$act_tax_field], 1);
				}
				$t = explode(',', $_POST[$act_tax_field]);
				$t = array_unique($t);

				$DW->addMultiOption($widget_id, $tax, $_POST['page'], $t);
			}

			// ---- Childs >> Can't use DWModule::childSave() cause of $name != $tax, but $name == 'page'
			$act_tax_childs_field = $tax . '_childs_act';
			if ( isset($_POST[$act_tax_field]) && ! empty($_POST[$act_tax_field]) && isset($_POST[$act_tax_childs_field]) && !empty($_POST[$act_tax_childs_field]) ) {

				if ( substr($_POST[$act_tax_childs_field], 0, 1) == ',' ) {
					$_POST[$act_tax_childs_field] = substr($_POST[$act_tax_childs_field], 1);
				}
				$t_childs = explode(',', $_POST[$act_tax_childs_field]);
				$t_childs = array_unique($t_childs);

				$DW->addChilds($widget_id, $tax . '-childs', $_POST['page'], $t, $t_childs);
			}
		}
	}

	// Author
	DWModule::save('author', 'complex');

	// Categories
	DWModule::save('category', 'complex');
	DWModule::childSave('category');      // -- Childs

	// Tags
	DWModule::save('tag', 'complex');

	// Archive
	DWModule::save('archive');

	// Error 404
	DWModule::save('e404');

	// Search
	DWModule::save('search');

	// Custom Types
	if ( isset($_POST['post_types']) ) {
		foreach ( $_POST['post_types'] as $type ) {
			$type = sanitize_text_field($type);

			if ( isset($_POST['individual']) && sanitize_text_field($_POST['individual']) == '1' ) {
				if ( isset($_POST[$type . '-post_act']) && is_array($_POST[$type . '-post_act']) && count($_POST[$type . '-post_act']) > 0 ) {
					$DW->addMultiOption($widget_id, $type . '-post', $_POST[$type], $_POST[$type . '-post_act']);
				}
			}

			// Check taxonomies
			$taxonomy = FALSE;

			// Go through the tax_list - Workaround as for some reason get_object_taxonomies() is not always filled
			$tax_list = array();
			$len = strlen($type);
			if ( isset($_POST['tax_list']) && is_array($_POST['tax_list']) && count($_POST['tax_list']) > 0 ) {
				foreach ( $_POST['tax_list'] as $tl ) {
					$tl = sanitize_text_field($tl);

					if ( substr($tl, 0, $len) == $type ) {
						$tax_list[] = $tl;
					}
				}
			}

			foreach ( $tax_list as $tax ) {
				$act_tax_field = $tax . '_act';
				if ( isset($_POST[$act_tax_field]) && is_array($_POST[$act_tax_field]) && count($_POST[$act_tax_field]) > 0 ) {
					$taxonomy = TRUE;
					break;
				}
			}

			$act_field = $type . '_act';
			if ( ( isset($_POST[$act_field]) && is_array($_POST[$act_tax_field]) && count($_POST[$act_field]) > 0 ) || $taxonomy ) {
				// When taxonomy = TRUE
				if ( !isset($_POST[$act_field]) ) {
					$_POST[$act_field] = array();
				}

				$DW->addMultiOption($widget_id, $type, $_POST[$type], $_POST[$act_field]);
			} else if ( $_POST[$type] == 'no' ) {
				$DW->addSingleOption($widget_id, $type);
			}

			// -- Childs
			DWModule::childSave($type);

			// -- Taxonomies
			foreach ( $tax_list as $tax ) {
				$act_tax_field = $tax . '_act';
				if ( isset($_POST[$act_tax_field]) && ! empty($_POST[$act_tax_field]) ) {

					if ( substr($_POST[$act_tax_field], 0, 1) == ',' ) {
						$_POST[$act_tax_field] = substr($_POST[$act_tax_field], 1);
					}
					$t = explode(',', $_POST[$act_tax_field]);
					$t = array_unique($t);

					$DW->addMultiOption($widget_id, $tax, $_POST[$type], $t);
				}

				// ---- Childs >> Can't use DWModule::childSave() cause of $name != $tax, but $name == $type
				$act_tax_childs_field = $tax . '_childs_act';
				if ( isset($_POST[$act_tax_field]) && ! empty($_POST[$act_tax_field]) && isset($_POST[$act_tax_childs_field]) && !empty($_POST[$act_tax_childs_field]) ) {

					if ( substr($_POST[$act_tax_childs_field], 0, 1) == ',' ) {
						$_POST[$act_tax_childs_field] = substr($_POST[$act_tax_childs_field], 1);
					}
					$t_childs = explode(',', $_POST[$act_tax_childs_field]);
					$t_childs = array_unique($t_childs);

					$DW->addChilds($widget_id, $tax . '-childs', $_POST[$type], $t, $t_childs);
				}
			}
		}

		DWModule::save('cp_archive', 'complex');
	}

	// Custom Taxonomies
	if ( isset($_POST['dw_taxonomy']) ) {
		foreach ( $_POST['dw_taxonomy'] as $tax ) {
			$type = 'tax_' . $tax;
			$act_field = $type . '_act';
			if ( isset($_POST[$act_field]) && ! empty($_POST[$act_field]) ) {
				/*				if (! is_array($_POST[$act_field]) ) {
									$_POST[$act_field] = array();
								} */

				if ( substr($_POST[$act_field], 0, 1) == ',' ) {
					$_POST[$act_field] = substr($_POST[$act_field], 1);
				}
				$t = explode(',', $_POST[$act_field]);
				$t = array_unique($t);

				$DW->addMultiOption($widget_id, $type, $_POST[$type], $t);
			} else if ( $_POST[$type] == 'no' ) {
				$DW->addSingleOption($widget_id, $type);
			}

			DWModule::childSave($type);
		}
	}

	// WPML PLugin support
	DWModule::save('wpml', 'complex');

	// QTranslate Plugin support
	// DWModule::save('qt', 'complex');

	// WPSC/WPEC Plugin support
	DWModule::save('wpsc', 'complex');

	// bbPress Plugin support
	DWModule::save('bbp_profile', 'simple');

	// BP Plugin support
	DWModule::save('bp', 'complex');

	// BP Plugin support (Groups)
	DWModule::save('bp-group', 'complex');

	// Pods Plugin support
	DWModule::save('pods', 'complex');

	// Redirect to ReturnURL
	if ( ! empty($returnurl) ) {
		$q = array();

		// Checking if there are arguments set
		$pos = strpos($returnurl, '?');
		if ( $pos !== FALSE ) {
			// evaluate the args
			$query_string = substr($returnurl, ( $pos + 1 ));
			$args = explode('&', $query_string);
			foreach ( $args as $arg ) {
				@list($name, $value) = explode('=', $arg);
				if ( $name != 'dynwid_save' && $name != 'widget_id' ) {
					$q[] = $name . '=' . $value;
				}
			}
			$script_url = substr($returnurl, 0, $pos);
		} else {
			$script_url = $returnurl;
		}
		$q[] = 'dynwid_save=yes';
		$q[] = 'widget_id=' . $widget_id;

		wp_redirect($script_url . '?' . implode('&', $q));
		die();
	}
?>
