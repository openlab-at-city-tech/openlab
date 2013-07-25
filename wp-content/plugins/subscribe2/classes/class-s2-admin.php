<?php
class s2_admin extends s2class {
/* ===== WordPress menu registration and scripts ===== */
	/**
	Hook the menu
	*/
	function admin_menu() {
		add_menu_page(__('Subscribe2', 'subscribe2'), __('Subscribe2', 'subscribe2'), apply_filters('s2_capability', "read", 'user'), 's2', NULL, S2URL . 'include/email_edit.png');

		$s2user = add_submenu_page('s2', __('Your Subscriptions', 'subscribe2'), __('Your Subscriptions', 'subscribe2'), apply_filters('s2_capability', "read", 'user'), 's2', array(&$this, 'user_menu'), S2URL . 'include/email_edit.png');
		add_action("admin_print_scripts-$s2user", array(&$this, 'checkbox_form_js'));
		add_action("admin_print_styles-$s2user", array(&$this, 'user_admin_css'));

		$s2subscribers = add_submenu_page('s2', __('Subscribers', 'subscribe2'), __('Subscribers', 'subscribe2'), apply_filters('s2_capability', "manage_options", 'manage'), 's2_tools', array(&$this, 'subscribers_menu'));
		add_action("admin_print_scripts-$s2subscribers", array(&$this, 'checkbox_form_js'));

		$s2settings = add_submenu_page('s2', __('Settings', 'subscribe2'), __('Settings', 'subscribe2'), apply_filters('s2_capability', "manage_options", 'settings'), 's2_settings', array(&$this, 'settings_menu'));
		add_action("admin_print_scripts-$s2settings", array(&$this, 'checkbox_form_js'));
		add_action("admin_print_scripts-$s2settings", array(&$this, 'option_form_js'));
		add_filter('plugin_row_meta', array(&$this, 'plugin_links'), 10, 2);

		add_submenu_page('s2', __('Send Email', 'subscribe2'), __('Send Email', 'subscribe2'), apply_filters('s2_capability', "publish_posts", 'send'), 's2_posts', array(&$this, 'write_menu'));

		$s2nonce = wp_hash('subscribe2');
	} // end admin_menu()

	/**
	Hook for Admin Drop Down Icons
	*/
	function ozh_s2_icon() {
		return S2URL . 'include/email_edit.png';
	} // end ozh_s2_icon()

	/**
	Insert Javascript and CSS into admin_header
	*/
	function checkbox_form_js() {
		wp_register_script('s2_checkbox', S2URL . 'include/s2_checkbox' . $this->script_debug . '.js', array('jquery'), '1.2');
		wp_enqueue_script('s2_checkbox');
	} //end checkbox_form_js()

	function user_admin_css() {
		wp_register_style('s2_user_admin', S2URL . 'include/s2_user_admin.css', array(), '1.0');
		wp_enqueue_style('s2_user_admin');
	} // end user_admin_css()

	function option_form_js() {
		wp_register_script('s2_edit', S2URL . 'include/s2_edit' . $this->script_debug . '.js', array('jquery'), '1.1');
		wp_enqueue_script('s2_edit');
	} // end option_form_js()

	/**
	Adds a links directly to the settings page from the plugin page
	*/
	function plugin_links($links, $file) {
		if ( $file == S2DIR.'subscribe2.php' ) {
			$links[] = "<a href='admin.php?page=s2_settings'>" . __('Settings', 'subscribe2') . "</a>";
			$links[] = "<a href='https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904'><b>" . __('Donate', 'subscribe2') . "</b></a>";
		}
		return $links;
	} // end plugin_links()

	/* ===== Menus ===== */
	/**
	Our subscriber management page
	*/
	function subscribers_menu() {
		require_once(S2PATH . 'admin/subscribers.php');
	} // end subscribers_menu()

	/**
	Our settings page
	*/
	function settings_menu() {
		require_once(S2PATH . 'admin/settings.php');
	} // end settings_menu()

	/**
	Our profile menu
	*/
	function user_menu() {
		require_once(S2PATH . 'admin/your_subscriptions.php');
	} // end user_menu()

	/**
	Display the Write sub-menu
	*/
	function write_menu() {
		require_once(S2PATH . 'admin/send_email.php');
	} // end write_menu()

/* ===== Write Toolbar Button Functions ===== */
	/**
	Register our button in the QuickTags bar
	*/
	function button_init() {
		global $pagenow;
		if ( !in_array($pagenow, array('post-new.php', 'post.php', 'page-new.php', 'page.php')) ) { return; }
		if ( !current_user_can('edit_posts') && !current_user_can('edit_pages') ) { return; }
		if ( 'true' == get_user_option('rich_editing') ) {
			// Hook into the rich text editor
			add_filter('mce_external_plugins', array(&$this, 'mce3_plugin'));
			add_filter('mce_buttons', array(&$this, 'mce3_button'));
		} else {
			if ( version_compare($this->wp_release, '3.3', '<') ) {
				wp_enqueue_script('subscribe2_button', S2URL . 'include/s2_button' . $this->script_debug . '.js', array('quicktags'), '1.0' );
			} else {
				// use QTags.addButton for WordPress 3.3 and greater
				wp_enqueue_script('subscribe2_button', S2URL . 'include/s2_button2' . $this->script_debug . '.js', array('quicktags'), '2.0' );
			}
		}
	} // end button_init()

	/**
	Add buttons for Rich Text Editor
	*/
	function mce3_plugin($arr) {
		$path = S2URL . 'tinymce3/editor_plugin' . $this->script_debug . '.js';
		$arr['subscribe2'] = $path;
		return $arr;
	} // end mce3_plugin()

	function mce3_button($arr) {
		$arr[] = 'subscribe2';
		return $arr;
	} // end mce3_button()

/* ===== widget functions ===== */
	/**
	Function to add css and js files to admin header
	*/
	function widget_s2counter_css_and_js() {
		// ensure we only add colorpicker js to widgets page
		if ( stripos($_SERVER['REQUEST_URI'], 'widgets.php' ) !== false ) {
			wp_enqueue_style('farbtastic');
			wp_enqueue_script('farbtastic');
			wp_register_script('s2_colorpicker', S2URL . 'include/s2_colorpicker' . $this->script_debug . '.js', array('farbtastic'), '1.0'); //my js
			wp_enqueue_script('s2_colorpicker');
		}
	} // end widget_s2_counter_css_and_js()

/* ===== meta box functions to allow per-post override ===== */
	/**
	Create meta box on write pages
	*/
	function s2_meta_init() {
		add_meta_box('subscribe2', __('Subscribe2 Notification Override', 'subscribe2' ), array(&$this, 's2_meta_box'), 'post', 'advanced');
		add_meta_box('subscribe2', __('Subscribe2 Notification Override', 'subscribe2' ), array(&$this, 's2_meta_box'), 'page', 'advanced');
	} // end s2_meta_init()

	/**
	Meta box code
	*/
	function s2_meta_box() {
		global $post_ID;
		$s2mail = get_post_meta($post_ID, '_s2mail', true);
		echo "<input type=\"hidden\" name=\"s2meta_nonce\" id=\"s2meta_nonce\" value=\"" . wp_create_nonce(wp_hash(plugin_basename(__FILE__))) . "\" />";
		echo __("Check here to disable sending of an email notification for this post/page", 'subscribe2');
		echo "&nbsp;&nbsp;<input type=\"checkbox\" name=\"s2_meta_field\" value=\"no\"";
		if ( $s2mail == 'no' || ($this->subscribe2_options['s2meta_default'] == "1" && $s2mail == "") ) {
			echo " checked=\"checked\"";
		}
		echo " />";
	} // end s2_meta_box()

	/**
	Meta box form handler
	*/
	function s2_meta_handler($post_id) {
		if ( !isset($_POST['s2meta_nonce']) || !wp_verify_nonce($_POST['s2meta_nonce'], wp_hash(plugin_basename(__FILE__))) ) { return $post_id; }

		if ( 'page' == $_POST['post_type'] ) {
			if ( !current_user_can('edit_page', $post_id) ) { return $post_id; }
		} else {
			if ( !current_user_can('edit_post', $post_id) ) { return $post_id; }
		}

		if ( isset($_POST['s2_meta_field']) && $_POST['s2_meta_field'] == 'no' ) {
			update_post_meta($post_id, '_s2mail', $_POST['s2_meta_field']);
		} else {
			update_post_meta($post_id, '_s2mail', 'yes');
		}
	} // end s2_meta_box_handler()

/* ===== WordPress menu helper functions ===== */
	/**
	Display a table of categories with checkboxes
	Optionally pre-select those categories specified
	*/
	function display_category_form($selected = array(), $override = 1, $compulsory = array(), $name = 'category') {
		global $wpdb;

		if ( $override == 0 ) {
			$all_cats = $this->all_cats(true);
		} else {
			$all_cats = $this->all_cats(false);
		}

		$half = (count($all_cats) / 2);
		$i = 0;
		$j = 0;
		echo "<table style=\"width: 100%; border-collapse: separate; border-spacing: 2px; *border-collapse: expression('separate', cellSpacing = '2px');\" class=\"editform\">\r\n";
		echo "<tr><td style=\"text-align: left;\" colspan=\"2\">\r\n";
		echo "<label><input type=\"checkbox\" name=\"checkall\" value=\"checkall_" . $name . "\" /> " . __('Select / Unselect All', 'subscribe2') . "</label>\r\n";
		echo "</td></tr>\r\n";
		echo "<tr style=\"vertical-align: top;\"><td style=\"width: 50%; text-align: left;\">\r\n";
		foreach ( $all_cats as $cat ) {
			if ( $i >= $half && 0 == $j ) {
				echo "</td><td style=\"width: 50%; text-align: left;\">\r\n";
				$j++;
			}
			$catName = '';
			$parents = array_reverse( get_ancestors($cat->term_id, $cat->taxonomy) );
			if ( $parents ) {
				foreach ( $parents as $parent ) {
					$parent = get_term($parent, $cat->taxonomy);
					$catName .= $parent->name . ' &raquo; ';
				}
			}
			$catName .= $cat->name;

			if ( 0 == $j ) {
				echo "<label><input class=\"checkall_" . $name . "\" type=\"checkbox\" name=\"" . $name . "[]\" value=\"" . $cat->term_id . "\"";
				if ( in_array($cat->term_id, $selected) || in_array($cat->term_id, $compulsory) ) {
					echo " checked=\"checked\"";
				}
				if ( in_array($cat->term_id, $compulsory) && $name === 'category' ) {
					echo " DISABLED";
				}
				echo " /> <abbr title=\"" . $cat->slug . "\">" . $catName . "</abbr></label><br />\r\n";
			} else {
				echo "<label><input class=\"checkall_" . $name . "\" type=\"checkbox\" name=\"" . $name . "[]\" value=\"" . $cat->term_id . "\"";
				if ( in_array($cat->term_id, $selected) || in_array($cat->term_id, $compulsory) ) {
					echo " checked=\"checked\"";
				}
				if ( in_array($cat->term_id, $compulsory) && $name === 'category' ) {
					echo " DISABLED";
				}
				echo " /> <abbr title=\"" . $cat->slug . "\">" . $catName . "</abbr></label><br />\r\n";
			}
			$i++;
		}
		if ( !empty($compulsory) ) {
			foreach ($compulsory as $cat) {
				echo "<input type=\"hidden\" name=\"" . $name . "[]\" value=\"" . $cat . "\">\r\n";
			}
		}
		echo "</td></tr>\r\n";
		echo "</table>\r\n";
	} // end display_category_form()

	/**
	Display a table of post formats supported by the currently active theme
	*/
	function display_format_form($formats, $selected = array()) {
		$half = (count($formats[0]) / 2);
		$i = 0;
		$j = 0;
		echo "<table style=\"width: 100%; border-collapse: separate; border-spacing: 2px; *border-collapse: expression('separate', cellSpacing = '2px');\" class=\"editform\">\r\n";
		echo "<tr><td style=\"text-align: left;\" colspan=\"2\">\r\n";
		echo "<label><input type=\"checkbox\" name=\"checkall\" value=\"checkall_format\" /> " . __('Select / Unselect All', 'subscribe2') . "</label>\r\n";
		echo "</td></tr>\r\n";
		echo "<tr style=\"vertical-align: top;\"><td style=\"width: 50%; text-align: left\">\r\n";
		foreach ( $formats[0] as $format ) {
			if ( $i >= $half && 0 == $j ) {
				echo "</td><td style=\"width: 50%; text-align: left\">\r\n";
				$j++;
			}

			if ( 0 == $j ) {
				echo "<label><input class=\"checkall_format\" type=\"checkbox\" name=\"format[]\" value=\"" . $format . "\"";
				if ( in_array($format, $selected) ) {
						echo " checked=\"checked\"";
				}
				echo " /> " . ucwords($format) . "</label><br />\r\n";
			} else {
				echo "<label><input class=\"checkall_format\" type=\"checkbox\" name=\"format[]\" value=\"" . $format . "\"";
				if ( in_array($format, $selected) ) {
							echo " checked=\"checked\"";
				}
				echo " /> " . ucwords($format) . "</label><br />\r\n";
			}
			$i++;
		}
		echo "</td></tr>\r\n";
		echo "</table>\r\n";
	} // end display_format_form()

	/**
	Display a table of authors with checkboxes
	Optionally pre-select those authors specified
	*/
	function display_author_form($selected = array()) {
		$all_authors = $this->get_authors();

		$half = (count($all_authors) / 2);
		$i = 0;
		$j = 0;
		echo "<table style=\"width: 100%; border-collapse: separate; border-spacing: 2px; *border-collapse: expression('separate', cellSpacing = '2px');\" class=\"editform\">\r\n";
		echo "<tr><td style=\"text-align: left;\" colspan=\"2\">\r\n";
		echo "<label><input type=\"checkbox\" name=\"checkall\" value=\"checkall_author\" /> " . __('Select / Unselect All', 'subscribe2') . "</label>\r\n";
		echo "</td></tr>\r\n";
		echo "<tr style=\"vertical-align: top;\"><td style=\"width: 50%; test-align: left;\">\r\n";
		foreach ( $all_authors as $author ) {
			if ( $i >= $half && 0 == $j ) {
				echo "</td><td style=\"width: 50%; text-align: left;\">\r\n";
				$j++;
			}
			if ( 0 == $j ) {
				echo "<label><input class=\"checkall_author\" type=\"checkbox\" name=\"author[]\" value=\"" . $author->ID . "\"";
				if ( in_array($author->ID, $selected) ) {
						echo " checked=\"checked\"";
				}
				echo " /> " . $author->display_name . "</label><br />\r\n";
			} else {
				echo "<label><input class=\"checkall_author\" type=\"checkbox\" name=\"author[]\" value=\"" . $author->ID . "\"";
				if ( in_array($author->ID, $selected) ) {
					echo " checked=\"checked\"";
				}
				echo " /> " . $author->display_name . "</label><br />\r\n";
				$i++;
			}
		}
		echo "</td></tr>\r\n";
		echo "</table>\r\n";
	} // end display_author_form()

	/**
	Collect an array of all author level users and above
	*/
	function get_authors() {
		if ( '' == $this->all_authors ) {
			$role = array('fields' => array('ID', 'display_name'), 'role' => 'administrator');
			$administrators = get_users( $role );
			$role = array('fields' => array('ID', 'display_name'), 'role' => 'editor');
			$editors = get_users( $role );
			$role = array('fields' => array('ID', 'display_name'), 'role' => 'author');
			$authors = get_users( $role );

			$this->all_authors = array_merge($administrators, $editors, $authors);
		}
		return apply_filters('s2_authors', $this->all_authors);
	} // end get_authors()

	/**
	Display a drop-down form to select subscribers
	$selected is the option to select
	$submit is the text to use on the Submit button
	*/
	function display_subscriber_dropdown($selected = 'registered', $submit = '', $exclude = array()) {
		global $wpdb;

		$who = array('all' => __('All Users and Subscribers', 'subscribe2'),
			'public' => __('Public Subscribers', 'subscribe2'),
			'confirmed' => ' &nbsp;&nbsp;' . __('Confirmed', 'subscribe2'),
			'unconfirmed' => ' &nbsp;&nbsp;' . __('Unconfirmed', 'subscribe2'),
			'all_users' => __('All Registered Users', 'subscribe2'),
			'registered' => __('Registered Subscribers', 'subscribe2'));

		$all_cats = $this->all_cats(false);

		// count the number of subscribers
		$count['confirmed'] = $wpdb->get_var("SELECT COUNT(id) FROM $this->public WHERE active='1'");
		$count['unconfirmed'] = $wpdb->get_var("SELECT COUNT(id) FROM $this->public WHERE active='0'");
		if ( in_array('unconfirmed', $exclude) ) {
			$count['public'] = $count['confirmed'];
		} elseif ( in_array('confirmed', $exclude) ) {
			$count['public'] = $count['unconfirmed'];
		} else {
			$count['public'] = ($count['confirmed'] + $count['unconfirmed']);
		}
		if ( $this->s2_mu ) {
			$count['all_users'] = $wpdb->get_var("SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key='" . $wpdb->prefix . "capabilities'");
		} else {
			$count['all_users'] = $wpdb->get_var("SELECT COUNT(ID) FROM $wpdb->users");
		}
		if ( $this->s2_mu ) {
			$count['registered'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key='" . $wpdb->prefix . "capabilities' AND meta_key=%s", $this->get_usermeta_keyname('s2_subscribed')));
		} else {
			$count['registered'] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key=%s", $this->get_usermeta_keyname('s2_subscribed')));
		}
		$count['all'] = ($count['confirmed'] + $count['unconfirmed'] + $count['all_users']);
		// get subscribers to individual categories but only if we are using per-post notifications
		if ( $this->subscribe2_options['email_freq'] == 'never' ) {
			if ( $this->s2_mu ) {
				foreach ( $all_cats as $cat ) {
					$count[$cat->name] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(a.meta_key) FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND b.meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $cat->term_id));
				}
			} else {
				foreach ( $all_cats as $cat ) {
					$count[$cat->name] = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_value) FROM $wpdb->usermeta WHERE meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $cat->term_id));
				}
			}
		}

		// do have actually have some subscribers?
		if ( 0 == $count['confirmed'] && 0 == $count['unconfirmed'] && 0 == $count['all_users'] ) {
			// no? bail out
			return;
		}

		echo "<select name=\"what\">\r\n";
		foreach ( $who as $whom => $display ) {
			if ( in_array($whom, $exclude) ) { continue; }
			if ( 0 == $count[$whom] ) { continue; }

			echo "<option value=\"" . $whom . "\"";
			if ( $whom == $selected ) { echo " selected=\"selected\" "; }
			echo ">$display (" . ($count[$whom]) . ")</option>\r\n";
		}

		if ( $count['registered'] > 0 && $this->subscribe2_options['email_freq'] == 'never' ) {
			foreach ( $all_cats as $cat ) {
				if ( in_array($cat->term_id, $exclude) ) { continue; }
				echo "<option value=\"" . $cat->term_id . "\"";
				if ( $cat->term_id == $selected ) { echo " selected=\"selected\" "; }
				echo "> &nbsp;&nbsp;" . $cat->name . "&nbsp;(" . $count[$cat->name] . ") </option>\r\n";
			}
		}
		echo "</select>";
		if ( false !== $submit ) {
			echo "&nbsp;<input type=\"submit\" class=\"button-secondary\" value=\"$submit\" />\r\n";
		}
	} // end display_subscriber_dropdown()

	/**
	Display a drop down list of administrator level users and
	optionally include a choice for Post Author
	*/
	function admin_dropdown($inc_author = false) {
		global $wpdb;

		$args = array('fields' => array('ID', 'display_name'), 'role' => 'administrator');
		$wp_user_query = get_users( $args );
		if ( !empty($wp_user_query) ) {
			foreach ($wp_user_query as $user) {
				$admins[] = $user;
			}
		} else {
			$admins = array();
		}

		if ( $inc_author ) {
			$author[] = (object)array('ID' => 'author', 'display_name' => __('Post Author', 'subscribe2'));
			$author[] = (object)array('ID' => 'blogname', 'display_name' => html_entity_decode(get_option('blogname'), ENT_QUOTES));
			$admins = array_merge($author, $admins);
		}

		echo "<select name=\"sender\">\r\n";
		foreach ( $admins as $admin ) {
			echo "<option value=\"" . $admin->ID . "\"";
			if ( $admin->ID == $this->subscribe2_options['sender'] ) {
				echo " selected=\"selected\"";
			}
			echo ">" . $admin->display_name . "</option>\r\n";
		}
		echo "</select>\r\n";
	} // end admin_dropdown()

	/**
	Display a dropdown of choices for digest email frequency
	and give user details of timings when event is scheduled
	*/
	function display_digest_choices() {
		global $wpdb;
		$cron_file = ABSPATH . 'wp-cron.php';
		if ( !is_readable($cron_file) ) {
			echo "<strong><em style=\"color: red\">" . __('The WordPress cron functions may be disabled on this server. Digest notifications may not work.', 'subscribe2') . "</em></strong><br />\r\n";
		}
		$scheduled_time = wp_next_scheduled('s2_digest_cron');
		$offset = get_option('gmt_offset') * 60 * 60;
		$schedule = (array)wp_get_schedules();
		$schedule = array_merge(array('never' => array('interval' => 0, 'display' => __('For each Post', 'subscribe2'))), $schedule);
		$sort = array();
		foreach ( (array)$schedule as $key => $value ) {
			$sort[$key] = $value['interval'];
		}
		asort($sort);
		$schedule_sorted = array();
		foreach ( $sort as $key => $value ) {
			$schedule_sorted[$key] = $schedule[$key];
		}
		foreach ( $schedule_sorted as $key => $value ) {
			echo "<label><input type=\"radio\" name=\"email_freq\" value=\"" . $key . "\"" . checked($this->subscribe2_options['email_freq'], $key, false) . " />";
			echo " " . $value['display'] . "</label><br />\r\n";
		}
		echo "<br />" . __('Send Digest Notification at UTC', 'subscribe2') . ": \r\n";
		$hours = array('12am', '1am', '2am', '3am', '4am', '5am', '6am', '7am', '8am', '9am', '10am', '11am', '12pm', '1pm', '2pm', '3pm', '4pm', '5pm', '6pm', '7pm', '8pm', '9pm', '10pm', '11pm');
		echo "<select name=\"hour\">\r\n";
		foreach ( $hours as $key => $value ) {
			echo "<option value=\"" . $key . "\"";
			if ( !empty($scheduled_time) && $key == date('H', $scheduled_time) ) {
				echo " selected=\"selected\"";
			}
			echo ">" . $value . "</option>\r\n";
		}
		echo "</select>\r\n";
		echo "<strong><em style=\"color: red\">" . __('Chosen time will be scheduled to a future date in relation to the current UTC time', 'subscribe2') . "</em></strong>\r\n";
		if ( $scheduled_time ) {
			$datetime = get_option('date_format') . ' @ ' . get_option('time_format');
			echo "<p>" . __('Current UTC time is', 'subscribe2') . ": \r\n";
			echo "<strong>" . date_i18n($datetime, false, 'gmt') . "</strong></p>\r\n";
			echo "<p>" . __('Current blog time is', 'subscribe2') . ": \r\n";
			echo "<strong>" . date_i18n($datetime) . "</strong></p>\r\n";
			echo "<p>" . __('Next email notification will be sent when your blog time is after', 'subscribe2') . ": \r\n";
			echo "<strong>" . date_i18n($datetime, $scheduled_time + $offset) . "</strong></p>\r\n";
			if ( !empty($this->subscribe2_options['previous_s2cron']) ) {
				echo "<p>" . __('Attempt to resend the last Digest Notification email', 'subscribe2') . ": ";
				echo "<input type=\"submit\" class=\"button-secondary\" name=\"resend\" value=\"" . __('Resend Digest', 'subscribe2') . "\" /></p>\r\n";
			}
		} else {
			echo "<br />";
		}
	} // end display_digest_choices()

	/**
	Create and display a dropdown list of pages
	*/
	function pages_dropdown($s2page) {
		global $wpdb;
		$sql = "SELECT ID, post_title FROM $wpdb->posts WHERE post_type='page' AND post_status='publish'";
		$pages = $wpdb->get_results($sql);

		if ( empty($pages) ) { return; }

		$option = '';
		foreach ( $pages as $page ) {
			$option .= "<option value=\"" . $page->ID . "\"";
			if ( $page->ID == $s2page ) {
				$option .= " selected=\"selected\"";
			}
			$option .= ">" . $page->post_title . "</option>\r\n";
		}

		echo $option;
	} // end pages_dropdown()

	/**
	Subscribe all registered users to category selected on Admin Manage Page
	*/
	function subscribe_registered_users($emails = '', $cats = array()) {
		if ( '' == $emails || '' == $cats ) { return false; }
		global $wpdb;

		$useremails = explode(",\r\n", $emails);
		$useremails = implode(", ", array_map(array($this, 'prepare_in_data'), $useremails));

		$sql = "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)";
		$user_IDs = $wpdb->get_col($sql);

		foreach ( $user_IDs as $user_ID ) {
			$old_cats = get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true);
			if ( !empty($old_cats) ) {
				$old_cats = explode(',', $old_cats);
				$newcats = array_unique(array_merge($cats, $old_cats));
			} else {
				$newcats = $cats;
			}
			if ( !empty($newcats) && $newcats !== $old_cats) {
				// add subscription to these cat IDs
				foreach ( $newcats as $id ) {
					update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id, $id);
				}
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $newcats));
			}
			unset($newcats);
		}
	} // end subscribe_registered_users()

	/**
	Unsubscribe all registered users to category selected on Admin Manage Page
	*/
	function unsubscribe_registered_users($emails = '', $cats = array()) {
		if ( '' == $emails || '' == $cats ) { return false; }
		global $wpdb;

		$useremails = explode(",\r\n", $emails);
		$useremails = implode(", ", array_map(array($this, 'prepare_in_data'), $useremails));

		$sql = "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)";
		$user_IDs = $wpdb->get_col($sql);

		foreach ( $user_IDs as $user_ID ) {
			$old_cats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
			$remain = array_diff($old_cats, $cats);
			if ( !empty($remain) && $remain !== $old_cats) {
				// remove subscription to these cat IDs and update s2_subscribed
				foreach ( $cats as $id ) {
					delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id);
				}
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $remain));
			} else {
				// remove subscription to these cat IDs and update s2_subscribed to ''
				foreach ( $cats as $id ) {
					delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id);
				}
				delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'));
			}
			unset($remain);
		}
	} // end unsubscribe_registered_users()

	/**
	Handles bulk changes to email format for Registered Subscribers
	*/
	function format_change($emails, $format) {
		if ( empty($format) ) { return; }

		global $wpdb;
		$useremails = explode(",\r\n", $emails);
		$useremails = implode(", ", array_map(array($this,'prepare_in_data'), $useremails));
		$ids = $wpdb->get_col("SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)");
		$ids = implode(',', array_map(array($this, 'prepare_in_data'), $ids));
		$sql = "UPDATE $wpdb->usermeta SET meta_value='{$format}' WHERE meta_key='" . $this->get_usermeta_keyname('s2_format') . "' AND user_id IN ($ids)";
		$wpdb->query($sql);
	} // end format_change()

	/**
	Handles bulk update to digest preferences
	*/
	function digest_change($emails, $digest) {
		if ( empty($digest) ) { return; }

		global $wpdb;
		$useremails = explode(",\r\n", $emails);
		$useremails = implode(", ", array_map(array($this, 'prepare_in_data'), $useremails));

		$sql = "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)";
		$user_IDs = $wpdb->get_col($sql);

		if ( $digest == 'digest' ) {
			$exclude = explode(',', $this->subscribe2_options['exclude']);
			if ( !empty($exclude) ) {
				$all_cats = $this->all_cats(true, 'ID');
			} else {
				$all_cats = $this->all_cats(false, 'ID');
			}

			$cats_string = '';
			foreach ( $all_cats as $cat ) {
				('' == $cats_string) ? $cats_string = "$cat->term_id" : $cats_string .= ",$cat->term_id";
			}

			foreach ( $user_IDs as $user_ID ) {
				foreach ( $all_cats as $cat ) {
					update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $cat->term_id, $cat->term_id);
				}
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), $cats_string);
			}
		} elseif ( $digest == '-1' ) {
			foreach ( $user_IDs as $user_ID ) {
				$cats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
				foreach ( $cats as $id ) {
					delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $id);
				}
				delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'));
			}
		}
	} // end digest_change()

/* ===== functions to handle addition and removal of WordPress categories ===== */
	/**
	Autosubscribe registered users to newly created categories
	if registered user has selected this option
	*/
	function new_category($new_category='') {
		if ( 'no' == $this->subscribe2_options['show_autosub'] ) { return; }
		// don't subscribe to individual new categories if we are doing digest emails
		if ( $this->subscribe2_options['email_freq'] != 'never' ) { return; }
		global $wpdb;

		if ( 'yes' == $this->subscribe2_options['show_autosub'] ) {
			if ( $this->s2_mu ) {
				$sql = $wpdb->prepare("SELECT DISTINCT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b WHERE a.user_id = b.user_id AND a.meta_key=%s AND a.meta_value='yes' AND b.meta_key=%s", $this->get_usermeta_keyname('s2_autosub'), $this->get_usermeta_keyname('s2_subscribed'));
			} else {
				$sql = $wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key=%s AND $wpdb->usermeta.meta_value='yes'", $this->get_usermeta_keyname('s2_autosub'));
			}
			$user_IDs = $wpdb->get_col($sql);
			if ( '' == $user_IDs ) { return; }

			foreach ( $user_IDs as $user_ID ) {
				$old_cats = get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true);
				if ( empty($old_cats) ) {
					$newcats = (array)$new_category;
				} else {
					$old_cats = explode(',', $old_cats);
					$newcats = array_merge($old_cats, (array)$new_category);
				}
				// add subscription to these cat IDs
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $new_category, $new_category);
				update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $newcats));
			}
		} elseif ( 'exclude' == $this->subscribe2_options['show_autosub'] ) {
			$excluded_cats = explode(',', $this->subscribe2_options['exclude']);
			$excluded_cats[] = $new_category;
			$this->subscribe2_options['exclude'] = implode(',', $excluded_cats);
			update_option('subscribe2_options', $this->subscribe2_options);
		}
	} // end new_category()

	/**
	Automatically delete subscriptions to a category when it is deleted
	*/
	function delete_category($deleted_category='') {
		global $wpdb;

		if ( $this->s2_mu ) {
			$sql = $wpdb->prepare("SELECT DISTINCT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b WHERE a.user_id = b.user_id AND a.meta_key=%s AND b.meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $deleted_category, $this->get_usermeta_keyname('s2_subscribed'));
		} else {
			$sql = $wpdb->prepare("SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key=%s", $this->get_usermeta_keyname('s2_cat') . $deleted_category);
		}
		$user_IDs = $wpdb->get_col($sql);
		if ( '' == $user_IDs ) { return; }

		foreach ( $user_IDs as $user_ID ) {
			$old_cats = explode(',', get_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), true));
			if ( !is_array($old_cats) ) {
				$old_cats = array($old_cats);
			}
			// add subscription to these cat IDs
			delete_user_meta($user_ID, $this->get_usermeta_keyname('s2_cat') . $deleted_category);
			$remain = array_diff($old_cats, (array)$deleted_category);
			update_user_meta($user_ID, $this->get_usermeta_keyname('s2_subscribed'), implode(',', $remain));
		}
	} // end delete_category()

/* ===== functions to show & handle one-click subscription ===== */
	/**
	Show form for one-click subscription on user profile page
	*/
	function one_click_profile_form($user) {
		echo "<h3>" . __('Email subscription', 'subscribe2') . "</h3>\r\n";
		echo "<table class=\"form-table\">\r\n";
		echo "<tr><th scope=\"row\">" . __('Subscribe / Unsubscribe', 'subscribe2') . "</th>\r\n";
		echo "<td><label><input type=\"checkbox\" name=\"sub2-one-click-subscribe\" value=\"1\" " . checked( ! get_user_meta($user->ID, $this->get_usermeta_keyname('s2_subscribed'), true), false, false ) . " /> " . __('Receive notifications', 'subscribe2') . "</label><br />\r\n";
		echo "<span class=\"description\">" . __('Check if you want to receive email notification when new posts are published', 'subscribe2') . "</span>\r\n";
		echo "</td></tr></table>\r\n";
	} // end one_click_profile_form()

	/**
	Handle submission from profile one-click subscription
	*/
	function one_click_profile_form_save($user_ID) {
		if ( !current_user_can( 'edit_user', $user_ID ) ) {
			return false;
		}

		if ( isset( $_POST['sub2-one-click-subscribe'] ) && 1 == $_POST['sub2-one-click-subscribe'] ) {
			// Subscribe
			$this->one_click_handler($user_ID, 'subscribe');
		} else {
			// Unsubscribe
			$this->one_click_handler($user_ID, 'unsubscribe');
		}
	} // end one_click_profile_form_save()
}
?>