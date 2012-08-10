<?php
if ( !function_exists('add_action') ) {
	exit();
}

global $s2nonce, $wpdb, $wp_version;

// send error message if no WordPress page exists
$sql = "SELECT ID FROM $wpdb->posts WHERE post_type='page' AND post_status='publish' LIMIT 1";
$id = $wpdb->get_var($sql);
if ( empty($id) ) {
	echo "<div id=\"message\" class=\"error\"><p><strong>$this->no_page</strong></p></div>";
}

$sender = $this->get_userdata($this->subscribe2_options['sender']);
list($user, $domain) = explode('@', $sender->user_email, 2);
if ( !strstr($_SERVER['SERVER_NAME'], $domain) ) {
	echo "<div id=\"message\" class=\"error\"><p><strong>" . __('You appear to be sending notifications from an email address from a different domain name to your blog, this may result in failed emails', 'subscribe2') . "</strong></p></div>";
}

// was anything POSTed?
if ( isset( $_POST['s2_admin']) ) {
	check_admin_referer('subscribe2-options_subscribers' . $s2nonce);
	if ( isset($_POST['reset']) ) {
		$this->reset();
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_reset</strong></p></div>";
	} elseif ( isset($_POST['preview']) ) {
		global $user_email;
		$this->preview_email = true;
		if ( 'never' == $this->subscribe2_options['email_freq'] ) {
			$post = get_posts('numberposts=1');
			$this->publish($post[0], $user_email);
		} else {
			$this->subscribe2_cron($user_email);
		}
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Preview message(s) sent to logged in user', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['resend']) ) {
		$status = $this->subscribe2_cron('', 'resend');
		if ( $status === false ) {
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('The Digest Notification email contained no post information. No email was sent', 'subscribe2') . "</strong></p></div>";
		} else {
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Attempt made to resend the Digest Notification email', 'subscribe2') . "</strong></p></div>";
		}
	} elseif ( isset($_POST['submit']) ) {
		// BCClimit
		if ( is_numeric($_POST['bcc']) && $_POST['bcc'] >= 0 ) {
			$this->subscribe2_options['bcclimit'] = $_POST['bcc'];
		}
		// admin_email
		$this->subscribe2_options['admin_email'] = $_POST['admin_email'];

		// send as blogname, author or admin?
		if ( is_numeric($_POST['sender']) ) {
			$sender = $_POST['sender'];
		} elseif ($_POST['sender'] == 'author') {
			$sender = 'author';
		} else {
			$sender = 'blogname';
		}
		$this->subscribe2_options['sender'] = $sender;

		// send email for pages, private and password protected posts
		$this->subscribe2_options['stylesheet'] = $_POST['stylesheet'];
		$this->subscribe2_options['pages'] = $_POST['pages'];
		$this->subscribe2_options['password'] = $_POST['password'];
		$this->subscribe2_options['private'] = $_POST['private'];
		$this->subscribe2_options['cron_order'] = $_POST['cron_order'];
		$this->subscribe2_options['tracking'] = $_POST['tracking'];

		// send per-post or digest emails
		$email_freq = $_POST['email_freq'];
		$scheduled_time = wp_next_scheduled('s2_digest_cron');
		if ( $email_freq != $this->subscribe2_options['email_freq'] || $_POST['hour'] != date('H', $scheduled_time) ) {
			// make sure the timezone strings are right
			if ( function_exists('date_default_timezone_get') && date_default_timezone_get() != get_option('timezone_string') ) {
				date_default_timezone_set(get_option('timezone_string'));
			}
			$this->subscribe2_options['email_freq'] = $email_freq;
			wp_clear_scheduled_hook('s2_digest_cron');
			$scheds = (array)wp_get_schedules();
			$interval = ( isset($scheds[$email_freq]['interval']) ) ? (int) $scheds[$email_freq]['interval'] : 0;
			if ( $interval == 0 ) {
				// if we are on per-post emails remove last_cron entry
				unset($this->subscribe2_options['last_s2cron']);
				unset($this->subscribe2_options['previous_s2cron']);
			} else {
				// if we are using digest schedule the event and prime last_cron as now
				$time = time() + $interval;
				if ( $interval < 86400 ) {
					// Schedule CRON events occurring less than daily starting now and periodically thereafter
					$maybe_time = mktime($_POST['hour'], 0, 0, date('m', time()), date('d', time()), date('Y', time()));
					// is maybe_time in the future
					$offset = $maybe_time - time();
					// is maybe_time + $interval in the future
					$offset2 = ($maybe_time + $interval) - time();
					if ( $offset < 0 ) {
						if ( $offset2 < 0 ) {
							$timestamp = &$time;
						} else {
							$timestamp = $maybe_time + $interval;
						}
					} else {
						$timestamp = &$maybe_time;
					}
				} else {
					// Schedule other CRON events starting at user defined hour and periodically thereafter
					$timestamp = mktime($_POST['hour'], 0, 0, date('m', $time), date('d', $time), date('Y', $time));
				}
				wp_schedule_event($timestamp, $email_freq, 's2_digest_cron');
				if ( !isset($this->subscribe2_options['last_s2cron']) ) {
					$this->subscribe2_options['last_s2cron'] = current_time('mysql');
				}
			}
		}

		// email subject and body templates
		// ensure that are not empty before updating
		if ( !empty($_POST['notification_subject']) ) {
			$this->subscribe2_options['notification_subject'] = $_POST['notification_subject'];
		}
		if ( !empty($_POST['mailtext']) ) {
			$this->subscribe2_options['mailtext'] = $_POST['mailtext'];
		}
		if ( !empty($_POST['confirm_subject']) ) {
			$this->subscribe2_options['confirm_subject'] = $_POST['confirm_subject'];
		}
		if ( !empty($_POST['confirm_email']) ) {
			$this->subscribe2_options['confirm_email'] = $_POST['confirm_email'];
		}
		if ( !empty($_POST['remind_subject']) ) {
			$this->subscribe2_options['remind_subject'] = $_POST['remind_subject'];
		}
		if ( !empty($_POST['remind_email']) ) {
			$this->subscribe2_options['remind_email'] = $_POST['remind_email'];
		}

		// excluded categories
		if ( !empty($_POST['category']) ) {
			sort($_POST['category']);
			$exclude_cats = implode(',', $_POST['category']);
		} else {
			$exclude_cats = '';
		}
		$this->subscribe2_options['exclude'] = $exclude_cats;
		// allow override?
		( isset($_POST['reg_override']) ) ? $override = '1' : $override = '0';
		$this->subscribe2_options['reg_override'] = $override;

		// excluded formats
		if ( !empty($_POST['format']) ) {
			$exclude_formats = implode(',', $_POST['format']);
		} else {
			$exclude_formats = '';
		}
		$this->subscribe2_options['exclude_formats'] = $exclude_formats;

		// default WordPress page where Subscribe2 token is placed
		if ( is_numeric($_POST['page']) && $_POST['page'] >= 0 ) {
			$this->subscribe2_options['s2page'] = $_POST['page'];
		}

		// Number of subscriber per page
		if ( is_numeric($_POST['entries']) && $_POST['entries'] > 0 ) {
			$this->subscribe2_options['entries'] = (int)$_POST['entries'];
		}

		// show meta link?
		( isset($_POST['show_meta']) && $_POST['show_meta'] == '1' ) ? $showmeta = '1' : $showmeta = '0';
		$this->subscribe2_options['show_meta'] = $showmeta;

		// show button?
		( isset($_POST['show_button']) && $_POST['show_button'] == '1' ) ? $showbutton = '1' : $showbutton = '0';
		$this->subscribe2_options['show_button'] = $showbutton;

		// enable AJAX style form
		( isset($_POST['ajax']) && $_POST['ajax'] == '1' ) ? $ajax = '1' : $ajax = '0';
		$this->subscribe2_options['ajax'] = $ajax;

		// show widget in Presentation->Widgets
		( isset($_POST['widget']) && $_POST['widget'] == '1' ) ? $showwidget = '1' : $showwidget = '0';
		$this->subscribe2_options['widget'] = $showwidget;

		// show counterwidget in Presentation->Widgets
		( isset($_POST['counterwidget']) && $_POST['counterwidget'] == '1' ) ? $showcounterwidget = '1' : $showcounterwidget = '0';
		$this->subscribe2_options['counterwidget'] = $showcounterwidget;

		// Subscribe2 over ride postmeta checked by default
		( isset($_POST['s2meta_default']) && $_POST['s2meta_default'] == '1' ) ? $s2meta_default = '1' : $s2meta_default = '0';
		$this->subscribe2_options['s2meta_default'] = $s2meta_default;

		//automatic subscription
		$this->subscribe2_options['autosub'] = $_POST['autosub'];
		$this->subscribe2_options['newreg_override'] = $_POST['newreg_override'];
		$this->subscribe2_options['wpregdef'] = $_POST['wpregdef'];
		$this->subscribe2_options['autoformat'] = $_POST['autoformat'];
		$this->subscribe2_options['show_autosub'] = $_POST['show_autosub'];
		$this->subscribe2_options['autosub_def'] = $_POST['autosub_def'];
		$this->subscribe2_options['comment_subs'] = $_POST['comment_subs'];
		$this->subscribe2_options['one_click_profile'] = $_POST['one_click_profile'];

		//barred domains
		$this->subscribe2_options['barred'] = $_POST['barred'];

		echo "<div id=\"message\" class=\"updated fade\"><p><strong>$this->options_saved</strong></p></div>";
		update_option('subscribe2_options', $this->subscribe2_options);
	}
}
// show our form
echo "<div class=\"wrap\">";
echo "<div id=\"icon-options-general\" class=\"icon32\"></div>";
echo "<h2>" . __('Subscribe2 Settings', 'subscribe2') . "</h2>\r\n";
echo "<a href=\"http://subscribe2.wordpress.com/\">" . __('Plugin Blog', 'subscribe2') . "</a> | ";
echo "<a href=\"https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&amp;hosted_button_id=2387904\">" . __('Make a donation via PayPal', 'subscribe2') . "</a>";
echo "<form method=\"post\" action=\"\">\r\n";
if ( function_exists('wp_nonce_field') ) {
	wp_nonce_field('subscribe2-options_subscribers' . $s2nonce);
}
echo "<input type=\"hidden\" name=\"s2_admin\" value=\"options\" />\r\n";
echo "<input type=\"hidden\" id=\"jsbcc\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" />";
echo "<input type=\"hidden\" id=\"jspage\" value=\"" . $this->subscribe2_options['s2page'] . "\" />";
echo "<input type=\"hidden\" id=\"jsentries\" value=\"" . $this->subscribe2_options['entries'] . "\" />";

// settings for outgoing emails
echo "<div class=\"s2_admin\" id=\"s2_notification_settings\">\r\n";
echo "<h2>" . __('Notification Settings', 'subscribe2') . "</h2>\r\n";
echo __('Restrict the number of recipients per email to (0 for unlimited)', 'subscribe2') . ': ';
echo "<span id=\"s2bcc_1\"><span id=\"s2bcc\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['bcclimit'] . "</span> ";
echo "<a href=\"#\" onclick=\"s2_show('bcc'); return false;\">" . __('Edit', 'subscribe2') . "</a></span>\n";
echo "<span id=\"s2bcc_2\">\r\n";
echo "<input type=\"text\" name=\"bcc\" value=\"" . $this->subscribe2_options['bcclimit'] . "\" size=\"3\" />\r\n";
echo "<a href=\"#\" onclick=\"s2_update('bcc'); return false;\">". __('Update', 'subscribe2') . "</a>\n";
echo "<a href=\"#\" onclick=\"s2_revert('bcc'); return false;\">". __('Revert', 'subscribe2') . "</a></span>\n";

echo "<br /><br />" . __('Send Admins notifications for new', 'subscribe2') . ': ';
echo "<label><input type=\"radio\" name=\"admin_email\" value=\"subs\"" . checked($this->subscribe2_options['admin_email'], 'subs', false) . " />\r\n";
echo __('Subscriptions', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"admin_email\" value=\"unsubs\"" . checked($this->subscribe2_options['admin_email'], 'unsubs', false) . " />\r\n";
echo __('Unsubscriptions', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"admin_email\" value=\"both\"" . checked($this->subscribe2_options['admin_email'], 'both', false) . " />\r\n";
echo __('Both', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"admin_email\" value=\"none\"" . checked($this->subscribe2_options['admin_email'], 'none', false) . " />\r\n";
echo __('Neither', 'subscribe2') . "</label><br /><br />\r\n";

echo __('Include theme CSS stylesheet in HTML notifications', 'subscribe2') . ': ';
echo "<label><input type=\"radio\" name=\"stylesheet\" value=\"yes\"" . checked($this->subscribe2_options['stylesheet'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"stylesheet\" value=\"no\"" . checked($this->subscribe2_options['stylesheet'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";

echo __('Send Emails for Pages', 'subscribe2') . ': ';
echo "<label><input type=\"radio\" name=\"pages\" value=\"yes\"" . checked($this->subscribe2_options['pages'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"pages\" value=\"no\"" . checked($this->subscribe2_options['pages'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
$s2_post_types = apply_filters('s2_post_types', NULL);
if ( !empty($s2_post_types) ) {
	$types = '';
	echo __('Subscribe2 will send email notifications for the following custom post types', 'subscribe2') . ': <strong>';
	foreach ($s2_post_types as $type) {
		('' == $types) ? $types = ucwords($type) : $types .= ", " . ucwords($type);
	}
	echo $types . "</strong><br /><br />\r\n";
}
echo __('Send Emails for Password Protected Posts', 'subscribe2') . ': ';
echo "<label><input type=\"radio\" name=\"password\" value=\"yes\"" . checked($this->subscribe2_options['password'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"password\" value=\"no\"" . checked($this->subscribe2_options['password'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
echo __('Send Emails for Private Posts', 'subscribe2') . ': ';
echo "<label><input type=\"radio\" name=\"private\" value=\"yes\"" . checked($this->subscribe2_options['private'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"private\" value=\"no\"" . checked($this->subscribe2_options['private'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
echo __('Send Email From', 'subscribe2') . ': ';
echo "<label>\r\n";
$this->admin_dropdown(true);
echo "</label><br /><br />\r\n";
if ( function_exists('wp_schedule_event') ) {
	echo __('Send Emails', 'subscribe2') . ": <br /><br />\r\n";
	$this->display_digest_choices();
	echo __('For digest notifications, date order for posts is', 'subscribe2') . ": \r\n";
	echo "<label><input type=\"radio\" name=\"cron_order\" value=\"desc\"" . checked($this->subscribe2_options['cron_order'], 'desc', false) . " /> ";
	echo __('Descending', 'subscribe2') . "</label>&nbsp;&nbsp;";
	echo "<label><input type=\"radio\" name=\"cron_order\" value=\"asc\"" . checked($this->subscribe2_options['cron_order'], 'asc', false) . " /> ";
	echo __('Ascending', 'subscribe2') . "</label><br /><br />\r\n";
}
echo __('Add Tracking Parameters to the Permalink', 'subscribe2') . ": ";
echo "<input type=\"text\" name=\"tracking\" value=\"" . stripslashes($this->subscribe2_options['tracking']) . "\" size=\"50\" /> ";
echo "<br />" . __('eg. utm_source=subscribe2&utm_medium=email&utm_campaign=postnotify', 'subscribe2') . "<br /><br />\r\n";
echo "</div>\r\n";

// email templates
echo "<div class=\"s2_admin\" id=\"s2_templates\">\r\n";
echo "<h2>" . __('Email Templates', 'subscribe2') . "</h2>\r\n";
echo "<br />";
echo "<table width=\"100%\" cellspacing=\"2\" cellpadding=\"1\" class=\"editform\">\r\n";
echo "<tr><td>";
echo __('New Post email (must not be empty)', 'subscribe2') . ":<br />\r\n";
echo __('Subject Line', 'subscribe2') . ": ";
echo "<input type=\"text\" name=\"notification_subject\" value=\"" . stripslashes($this->subscribe2_options['notification_subject']) . "\" size=\"30\" />";
echo "<br />\r\n";
echo "<textarea rows=\"9\" cols=\"60\" name=\"mailtext\">" . stripslashes($this->subscribe2_options['mailtext']) . "</textarea><br /><br />\r\n";
echo "</td><td valign=\"top\" rowspan=\"3\">";
echo "<p class=\"submit\"><input type=\"submit\" class=\"button-secondary\" name=\"preview\" value=\"" . __('Send Email Preview', 'subscribe2') . "\" /></p>\r\n";
echo "<h3>" . __('Message substitutions', 'subscribe2') . "</h3>\r\n";
echo "<dl>";
echo "<dt><b><em style=\"color: red\">" . __('IF THE FOLLOWING KEYWORDS ARE ALSO IN YOUR POST THEY WILL BE SUBSTITUTED' ,'subscribe2') . "</em></b></dt><dd></dd>\r\n";
echo "<dt><b>{BLOGNAME}</b></dt><dd>" . get_option('blogname') . "</dd>\r\n";
echo "<dt><b>{BLOGLINK}</b></dt><dd>" . get_option('home') . "</dd>\r\n";
echo "<dt><b>{TITLE}</b></dt><dd>" . __("the post's title<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{POST}</b></dt><dd>" . __("the excerpt or the entire post<br />(<i>based on the subscriber's preferences</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{POSTTIME}</b></dt><dd>" . __("the excerpt of the post and the time it was posted<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{TABLE}</b></dt><dd>" . __("a list of post titles<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{TABLELINKS}</b></dt><dd>" . __("a list of post titles followed by links to the atricles<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{PERMALINK}</b></dt><dd>" . __("the post's permalink<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{TINYLINK}</b></dt><dd>" . __("the post's permalink after conversion by TinyURL<br />(<i>for per-post emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{DATE}</b></dt><dd>" . __("the date the post was made<br />(<i>for per-post emails only</i>)", "subscribe2") . "</dd>\r\n";
echo "<dt><b>{TIME}</b></dt><dd>" . __("the time the post was made<br />(<i>for per-post emails only</i>)", "subscribe2") . "</dd>\r\n";
echo "<dt><b>{MYNAME}</b></dt><dd>" . __("the admin or post author's name", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{EMAIL}</b></dt><dd>" . __("the admin or post author's email", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{AUTHORNAME}</b></dt><dd>" . __("the post author's name", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{LINK}</b></dt><dd>" . __("the generated link to confirm a request<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{ACTION}</b></dt><dd>" . __("Action performed by LINK in confirmation email<br />(<i>only used in the confirmation email template</i>)", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{CATS}</b></dt><dd>" . __("the post's assigned categories", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{TAGS}</b></dt><dd>" . __("the post's assigned Tags", 'subscribe2') . "</dd>\r\n";
echo "<dt><b>{COUNT}</b></dt><dd>" . __("the number of posts included in the digest email<br />(<i>for digest emails only</i>)", 'subscribe2') . "</dd>\r\n";
echo "</dl></td></tr><tr><td>";
echo __('Subscribe / Unsubscribe confirmation email', 'subscribe2') . ":<br />\r\n";
echo __('Subject Line', 'subscribe2') . ": ";
echo "<input type=\"text\" name=\"confirm_subject\" value=\"" . stripslashes($this->subscribe2_options['confirm_subject']) . "\" size=\"30\" /><br />\r\n";
echo "<textarea rows=\"9\" cols=\"60\" name=\"confirm_email\">" . stripslashes($this->subscribe2_options['confirm_email']) . "</textarea><br /><br />\r\n";
echo "</td></tr><tr valign=\"top\"><td>";
echo __('Reminder email to Unconfirmed Subscribers', 'subscribe2') . ":<br />\r\n";
echo __('Subject Line', 'subscribe2') . ": ";
echo "<input type=\"text\" name=\"remind_subject\" value=\"" . stripslashes($this->subscribe2_options['remind_subject']) . "\" size=\"30\" /><br />\r\n";
echo "<textarea rows=\"9\" cols=\"60\" name=\"remind_email\">" . stripslashes($this->subscribe2_options['remind_email']) . "</textarea><br /><br />\r\n";
echo "</td></tr></table><br />\r\n";
echo "</div>\r\n";

// excluded categories
echo "<div class=\"s2_admin\" id=\"s2_excluded_categories\">\r\n";
echo "<h2>" . __('Excluded Categories', 'subscribe2') . "</h2>\r\n";
echo "<p>";
echo "<strong><em style=\"color: red\">" . __('Posts assigned to any Excluded Category do not generate notifications and are not included in digest notifications', 'subscribe2') . "</em></strong><br />\r\n";
echo "</p>";
$this->display_category_form(explode(',', $this->subscribe2_options['exclude']));
echo "<center><label><input type=\"checkbox\" name=\"reg_override\" value=\"1\"" . checked($this->subscribe2_options['reg_override'], '1', false) . " /> ";
echo __('Allow registered users to subscribe to excluded categories?', 'subscribe2') . "</label></center><br />\r\n";

// excluded post formats
$formats = get_theme_support('post-formats');
if ( $formats !== false ) {
	// excluded formats
	echo "<h2>" . __('Excluded Formats', 'subscribe2') . "</h2>\r\n";
	echo "<p>";
	echo "<strong><em style=\"color: red\">" . __('Posts assigned to any Excluded Format do not generate notifications and are not included in digest notifications', 'subscribe2') . "</em></strong><br />\r\n";
	echo "</p>";
	$this->display_format_form($formats, explode(',', $this->subscribe2_options['exclude_formats']));
}
echo "</div>\r\n";

// Appearance options
echo "<div class=\"s2_admin\" id=\"s2_appearance_settings\">\r\n";
echo "<h2>" . __('Appearance', 'subscribe2') . "</h2>\r\n";
echo "<p>";

// WordPress page ID where subscribe2 token is used
echo __('Set default Subscribe2 page as ID', 'subscribe2') . ': ';
echo "<select name=\"page\">\r\n";
$this->pages_dropdown($this->subscribe2_options['s2page']);
echo "</select>\r\n";

// Number of subscribers per page
echo "<br /><br />" . __('Set the number of Subscribers displayed per page', 'subscribe2') . ': ';
echo "<span id=\"s2entries_1\"><span id=\"s2entries\" style=\"background-color: #FFFBCC\">" . $this->subscribe2_options['entries'] . "</span> ";
echo "<a href=\"#\" onclick=\"s2_show('entries'); return false;\">" . __('Edit', 'subscribe2') . "</a></span>\n";
echo "<span id=\"s2entries_2\">\r\n";
echo "<input type=\"text\" name=\"entries\" value=\"" . $this->subscribe2_options['entries'] . "\" size=\"3\" />\r\n";
echo "<a href=\"#\" onclick=\"s2_update('entries'); return false;\">". __('Update', 'subscribe2') . "</a>\n";
echo "<a href=\"#\" onclick=\"s2_revert('entries'); return false;\">". __('Revert', 'subscribe2') . "</a></span>\n";

// show link to WordPress page in meta
echo "<br /><br /><label><input type=\"checkbox\" name=\"show_meta\" value=\"1\"" . checked($this->subscribe2_options['show_meta'], '1', false) . " /> ";
echo __('Show a link to your subscription page in "meta"?', 'subscribe2') . "</label><br /><br />\r\n";

// show QuickTag button
echo "<label><input type=\"checkbox\" name=\"show_button\" value=\"1\"" . checked($this->subscribe2_options['show_button'], '1', false) . " /> ";
echo __('Show the Subscribe2 button on the Write toolbar?', 'subscribe2') . "</label><br /><br />\r\n";

// enable AJAX style form
echo "<label><input type=\"checkbox\" name=\"ajax\" value=\"1\"" . checked($this->subscribe2_options['ajax'], '1', false) . " />";
echo __('Enable AJAX style subscription form?', 'subscribe2') . "</label><br /><br />\r\n";

// show Widget
echo "<label><input type=\"checkbox\" name=\"widget\" value=\"1\"" . checked($this->subscribe2_options['widget'], '1', false) . " /> ";
echo __('Enable Subscribe2 Widget?', 'subscribe2') . "</label><br /><br />\r\n";

// show Counter Widget
echo "<label><input type=\"checkbox\" name=\"counterwidget\" value=\"1\"" . checked($this->subscribe2_options['counterwidget'], '1', false) . " /> ";
echo __('Enable Subscribe2 Counter Widget?', 'subscribe2') . "</label><br /><br />\r\n";

// s2_meta checked by default
echo "<label><input type =\"checkbox\" name=\"s2meta_default\" value=\"1\"" . checked($this->subscribe2_options['s2meta_default'], '1', false) . " /> ";
echo __('Disable email notifications is checked by default on authoring pages?', 'subscribe2') . "</label>\r\n";
echo "</p>";
echo "</div>\r\n";

//Auto Subscription for new registrations
echo "<div class=\"s2_admin\" id=\"s2_autosubscribe_settings\">\r\n";
echo "<h2>" . __('Auto Subscribe', 'subscribe2') . "</h2>\r\n";
echo "<p>";
echo __('Subscribe new users registering with your blog', 'subscribe2') . ":<br />\r\n";
echo "<label><input type=\"radio\" name=\"autosub\" value=\"yes\"" . checked($this->subscribe2_options['autosub'], 'yes', false) . " /> ";
echo __('Automatically', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autosub\" value=\"wpreg\"" . checked($this->subscribe2_options['autosub'], 'wpreg', false) . " /> ";
echo __('Display option on Registration Form', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autosub\" value=\"no\"" . checked($this->subscribe2_options['autosub'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
echo __('Auto-subscribe includes any excluded categories', 'subscribe2') . ":<br />\r\n";
echo "<label><input type=\"radio\" name=\"newreg_override\" value=\"yes\"" . checked($this->subscribe2_options['newreg_override'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"newreg_override\" value=\"no\"" . checked($this->subscribe2_options['newreg_override'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
echo __('Registration Form option is checked by default', 'subscribe2') . ":<br />\r\n";
echo "<label><input type=\"radio\" name=\"wpregdef\" value=\"yes\"" . checked($this->subscribe2_options['wpregdef'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"wpregdef\" value=\"no\"" . checked($this->subscribe2_options['wpregdef'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />\r\n";
echo __('Auto-subscribe users to receive email as', 'subscribe2') . ": <br />\r\n";
echo "<label><input type=\"radio\" name=\"autoformat\" value=\"html\"" . checked($this->subscribe2_options['autoformat'], 'html', false) . " /> ";
echo __('HTML - Full', 'subscribe2') ."</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autoformat\" value=\"html_excerpt\"" . checked($this->subscribe2_options['autoformat'], 'html_excerpt', false) . " /> ";
echo __('HTML - Excerpt', 'subscribe2') ."</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autoformat\" value=\"post\"" . checked($this->subscribe2_options['autoformat'], 'post', false) . " /> ";
echo __('Plain Text - Full', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autoformat\" value=\"excerpt\"" . checked($this->subscribe2_options['autoformat'], 'excerpt', false) . " /> ";
echo __('Plain Text - Excerpt', 'subscribe2') . "</label><br /><br />";
echo __('Registered Users have the option to auto-subscribe to new categories', 'subscribe2') . ": <br />\r\n";
echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"yes\"" . checked($this->subscribe2_options['show_autosub'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"no\"" . checked($this->subscribe2_options['show_autosub'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"show_autosub\" value=\"exclude\"" . checked($this->subscribe2_options['show_autosub'], 'exclude', false) . " /> ";
echo __('New categories are immediately excluded', 'subscribe2') . "</label><br /><br />";
echo __('Option for Registered Users to auto-subscribe to new categories is checked by default', 'subscribe2') . ": <br />\r\n";
echo "<label><input type=\"radio\" name=\"autosub_def\" value=\"yes\"" . checked($this->subscribe2_options['autosub_def'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"autosub_def\" value=\"no\"" . checked($this->subscribe2_options['autosub_def'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />";
echo __('Display checkbox to allow subscriptions from the comment form', 'subscribe2') . ": <br />\r\n";
echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"before\"" . checked($this->subscribe2_options['comment_subs'], 'before', false) . " /> ";
echo __('Before the Comment Submit button', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"after\"" . checked($this->subscribe2_options['comment_subs'], 'after', false) . " /> ";
echo __('After the Comment Submit button', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"comment_subs\" value=\"no\"" . checked($this->subscribe2_options['comment_subs'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label><br /><br />";
echo __('Show one-click subscription on profile page', 'subscribe2') . ":<br />\r\n";
echo "<label><input type=\"radio\" name=\"one_click_profile\" value=\"yes\"" . checked($this->subscribe2_options['one_click_profile'], 'yes', false) . " /> ";
echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;";
echo "<label><input type=\"radio\" name=\"one_click_profile\" value=\"no\"" . checked($this->subscribe2_options['one_click_profile'], 'no', false) . " /> ";
echo __('No', 'subscribe2') . "</label>\r\n";
echo "</p></div>\r\n";

//barred domains
echo "<div class=\"s2_admin\" id=\"s2_barred_domains\">\r\n";
echo "<h2>" . __('Barred Domains', 'subscribe2') . "</h2>\r\n";
echo "<p>";
echo __('Enter domains to bar from public subscriptions: <br /> (Use a new line for each entry and omit the "@" symbol, for example email.com)', 'subscribe2');
echo "<br />\r\n<textarea style=\"width: 98%;\" rows=\"4\" cols=\"60\" name=\"barred\">" . esc_textarea($this->subscribe2_options['barred']) . "</textarea>";
echo "</p>";
echo "</div>\r\n";

// submit
echo "<p class=\"submit\" align=\"center\"><input type=\"submit\" class=\"button-primary\" name=\"submit\" value=\"" . __('Submit', 'subscribe2') . "\" /></p>";

// reset
echo "<h2>" . __('Reset Default', 'subscribe2') . "</h2>\r\n";
echo "<p>" . __('Use this to reset all options to their defaults. This <strong><em>will not</em></strong> modify your list of subscribers.', 'subscribe2') . "</p>\r\n";
echo "<p class=\"submit\" align=\"center\">";
echo "<input type=\"submit\" id=\"deletepost\" name=\"reset\" value=\"" . __('RESET', 'subscribe2') .
"\" />";
echo "</p></form></div>\r\n";

include(ABSPATH . 'wp-admin/admin-footer.php');
// just to be sure
die;
?>