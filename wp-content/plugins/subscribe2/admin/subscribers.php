<?php
if ( !function_exists('add_action') ) {
	exit();
}

global $wpdb, $s2nonce;

//Get Registered Subscribers for bulk management
$registered = $this->get_registered();
$all_users = $this->get_all_registered();

// was anything POSTed ?
if ( isset($_POST['s2_admin']) ) {
	check_admin_referer('subscribe2-manage_subscribers' . $s2nonce);
	if ( !empty($_POST['addresses']) ) {
		$sub_error = '';
		$unsub_error = '';
		foreach ( preg_split ("|[\s,]+|", $_POST['addresses']) as $email ) {
			$email = $this->sanitize_email($email);
			if ( is_email($email) && $_POST['subscribe'] ) {
				if ( $this->is_public($email) !== false ) {
					('' == $sub_error) ? $sub_error = "$email" : $sub_error .= ", $email";
					continue;
				}
				$this->add($email, true);
				$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) subscribed!', 'subscribe2') . "</strong></p></div>";
			} elseif ( is_email($email) && $_POST['unsubscribe'] ) {
				if ( $this->is_public($email) === false ) {
					('' == $unsub_error) ? $unsub_error = "$email" : $unsub_error .= ", $email";
					continue;
				}
				$this->delete($email);
				$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) unsubscribed!', 'subscribe2') . "</strong></p></div>";
			}
		}
		if ( $sub_error != '' ) {
			echo "<div id=\"message\" class=\"error\"><p><strong>" . __('Some emails were not processed, the following were already subscribed' , 'subscribe2') . ":<br />$sub_error</strong></p></div>";
		}
		if ( $unsub_error != '' ) {
			echo "<div id=\"message\" class=\"error\"><p><strong>" . __('Some emails were not processed, the following were not in the database' , 'subscribe2') . ":<br />$unsub_error</strong></p></div>";
		}
		echo $message;
		$_POST['what'] = 'confirmed';
	} elseif ( isset($_POST['process']) ) {
		if ( isset($_POST['delete']) ) {
			foreach ( $_POST['delete'] as $address ) {
				$this->delete($address);
			}
			echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Address(es) deleted!', 'subscribe2') . "</strong></p></div>";
		}
		if ( isset($_POST['confirm']) ) {
			foreach ( $_POST['confirm'] as $address ) {
				$this->toggle($this->sanitize_email($address));
			}
			$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Status changed!', 'subscribe2') . "</strong></p></div>";
		}
		if ( isset($_POST['unconfirm']) ) {
			foreach ( $_POST['unconfirm'] as $address ) {
				$this->toggle($this->sanitize_email($address));
			}
			$message = "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Status changed!', 'subscribe2') . "</strong></p></div>";
		}
		echo $message;
	} elseif ( !empty($_POST['searchterm']) ) {
		$confirmed = $this->get_public();
		$unconfirmed = $this->get_public(0);
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$all_users);
		foreach ( $subscribers as $subscriber ) {
			if ( is_numeric(stripos($subscriber, $_POST['searchterm'])) ) {
				$result[] = $subscriber;
			}
		}
	} elseif ( isset($_POST['remind']) ) {
		$this->remind($_POST['reminderemails']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Reminder Email(s) Sent!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_categories']) && 'subscribe' == $_POST['manage'] ) {
		$this->subscribe_registered_users($_POST['exportcsv'], $_POST['category']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Subscribed!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_categories']) && 'unsubscribe' == $_POST['manage'] ) {
		$this->unsubscribe_registered_users($_POST['exportcsv'], $_POST['category']);
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Registered Users Unsubscribed!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_format']) ) {
		$this->format_change( $_POST['format'], $_POST['exportcsv'] );
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Format updated for Selected Registered Users!', 'subscribe2') . "</strong></p></div>";
	} elseif ( isset($_POST['sub_digest']) ) {
		$this->digest_change( $_POST['sub_category'], $_POST['exportcsv'] );
		echo "<div id=\"message\" class=\"updated fade\"><p><strong>" . __('Digest Subscription updated for Selected Registered Users!', 'subscribe2') . "</strong></p></div>";
	}
}

//Get Public Subscribers once for filter
$confirmed = $this->get_public();
$unconfirmed = $this->get_public(0);
// safety check for our arrays
if ( '' == $confirmed ) { $confirmed = array(); }
if ( '' == $unconfirmed ) { $unconfirmed = array(); }
if ( '' == $registered ) { $registered = array(); }
if ( '' == $all_users ) { $all_users = array(); }

$reminderform = false;
$urlpath = str_replace("\\", "/", S2PATH);
$urlpath = trailingslashit(get_option('siteurl')) . substr($urlpath,strpos($urlpath, "wp-content/"));
if ( isset($_GET['s2page']) ) {
	$page = (int) $_GET['s2page'];
} else {
	$page = 1;
}

if ( isset($_POST['what']) ) {
	$page = 1;
	if ( 'all' == $_POST['what'] ) {
		$what = 'all';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$all_users);
	} elseif ( 'public' == $_POST['what'] ) {
		$what = 'public';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
	} elseif ( 'confirmed' == $_POST['what'] ) {
		$what = 'confirmed';
		$subscribers = $confirmed;
	} elseif ( 'unconfirmed' == $_POST['what'] ) {
		$what = 'unconfirmed';
		$subscribers = $unconfirmed;
		if ( !empty($subscribers) ) {
			$reminderemails = implode(",", $subscribers);
			$reminderform = true;
		}
	} elseif ( is_numeric($_POST['what']) ) {
		$what = intval($_POST['what']);
		$subscribers = $this->get_registered("cats=$what");
	} elseif ( 'registered' == $_POST['what'] ) {
		$what = 'registered';
		$subscribers = $registered;
	} elseif ( 'all_users' == $_POST['what'] ) {
		$what = 'all_users';
		$subscribers = $all_users;
	}
} elseif ( isset($_GET['what']) ) {
	if ( 'all' == $_GET['what'] ) {
		$what = 'all';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$all_users);
	} elseif ( 'public' == $_GET['what'] ) {
		$what = 'public';
		$subscribers = array_merge((array)$confirmed, (array)$unconfirmed);
	} elseif ( 'confirmed' == $_GET['what'] ) {
		$what = 'confirmed';
		$subscribers = $confirmed;
	} elseif ( 'unconfirmed' == $_GET['what'] ) {
		$what = 'unconfirmed';
		$subscribers = $unconfirmed;
		if ( !empty($subscribers) ) {
			$reminderemails = implode(",", $subscribers);
			$reminderform = true;
		}
	} elseif ( is_numeric($_GET['what']) ) {
		$what = intval($_GET['what']);
		$subscribers = $this->get_registered("cats=$what");
	} elseif ( 'registered' == $_GET['what'] ) {
		$what = 'registered';
		$subscribers = $registered;
	} elseif ( 'all_users' == $_GET['what'] ) {
		$what = 'all_users';
		$subscribers = $all_users;
	}
} else {
	$what = 'all';
	$subscribers = array_merge((array)$confirmed, (array)$unconfirmed, (array)$all_users);
}
if ( !empty($_POST['searchterm']) ) {
	$subscribers = &$result;
	$what = 'public';
}

if ( !empty($subscribers) ) {
	natcasesort($subscribers);
	// Displays a page number strip - adapted from code in Akismet
	$args['what'] = $what;
	$total_subscribers = count($subscribers);
	$total_pages = ceil($total_subscribers / $this->subscribe2_options['entries']);
	$strip = '';
	if ( $page > 1 ) {
		$args['s2page'] = $page - 1;
		$strip .= '<a class="prev" href="' . esc_url(add_query_arg($args)) . '">&laquo; '. __('Previous Page', 'subscribe2') .'</a>' . "\n";
	}
	if ( $total_pages > 1 ) {
		for ( $page_num = 1; $page_num <= $total_pages; $page_num++ ) {
			if ( $page == $page_num ) {
				$strip .= "<strong>Page " . $page_num . "</strong>\n";
			} else {
				if ( $page_num < 3 || ( $page_num >= $page - 2 && $page_num <= $page + 2 ) || $page_num > $total_pages - 2 ) {
					$args['s2page'] = $page_num;
					$strip .= "<a class=\"page-numbers\" href=\"" . esc_url(add_query_arg($args)) . "\">" . $page_num . "</a>\n";
					$trunc = true;
				} elseif ( $trunc == true ) {
					$strip .= "...\n";
					$trunc = false;
				}
			}
		}
	}
	if ( ( $page ) * $this->subscribe2_options['entries'] < $total_subscribers ) {
		$args['s2page'] = $page + 1;
		$strip .= "<a class=\"next\" href=\"" . esc_url(add_query_arg($args)) . "\">". __('Next Page', 'subscribe2') . " &raquo;</a>\n";
	}
}

// show our form
echo "<div class=\"wrap\">";
echo "<div id=\"icon-tools\" class=\"icon32\"></div>";
echo "<h2>" . __('Manage Subscribers', 'subscribe2') . "</h2>\r\n";
echo "<form method=\"post\" action=\"\">\r\n";
if ( function_exists('wp_nonce_field') ) {
	wp_nonce_field('subscribe2-manage_subscribers' . $s2nonce);
}
echo "<div class=\"s2_admin\" id=\"s2_add_subscribers\">\r\n";
echo "<h2>" . __('Add/Remove Subscribers', 'subscribe2') . "</h2>\r\n";
echo "<p>" . __('Enter addresses, one per line or comma-separated', 'subscribe2') . "<br />\r\n";
echo "<textarea rows=\"2\" cols=\"80\" name=\"addresses\"></textarea></p>\r\n";
echo "<input type=\"hidden\" name=\"s2_admin\" />\r\n";
echo "<p class=\"submit\" style=\"border-top: none;\"><input type=\"submit\" class=\"button-primary\" name=\"subscribe\" value=\"" . __('Subscribe', 'subscribe2') . "\" />";
echo "&nbsp;<input type=\"submit\" class=\"button-primary\" name=\"unsubscribe\" value=\"" . __('Unsubscribe', 'subscribe2') . "\" /></p>\r\n";
echo "</div>\r\n";

// subscriber lists
echo "<div class=\"s2_admin\" id=\"s2_current_subscribers\">\r\n";
echo "<h2>" . __('Current Subscribers', 'subscribe2') . "</h2>\r\n";
echo "<br />";
$this->display_subscriber_dropdown($what, __('Filter', 'subscribe2'));
echo "<br /><br />";
// show the selected subscribers
$alternate = 'alternate';
echo "<table class=\"widefat\" cellpadding=\"2\" cellspacing=\"2\" width=\"100%\">";
$searchterm = ( isset($_POST['searchterm']) ) ? stripslashes(esc_html($_POST['searchterm'])) : '';
echo "<tr class=\"alternate\"><td colspan=\"3\"><input type=\"text\" name=\"searchterm\" value=\"" . $searchterm . "\" /></td>\r\n";
echo "<td><input type=\"submit\" class=\"button-secondary\" name=\"search\" value=\"" . __('Search Subscribers', 'subscribe2') . "\" /></td>\r\n";
if ( $reminderform ) {
	echo "<td width=\"25%\" align=\"right\"><input type=\"hidden\" name=\"reminderemails\" value=\"" . $reminderemails . "\" />\r\n";
	echo "<input type=\"submit\" class=\"button-secondary\" name=\"remind\" value=\"" . __('Send Reminder Email', 'subscribe2') . "\" /></td>\r\n";
} else {
	echo "<td width=\"25%\"></td>";
}
if ( !empty($subscribers) ) {
	$exportcsv = implode(",\r\n", $subscribers);
	echo "<td width=\"25%\" align=\"right\"><input type=\"hidden\" name=\"exportcsv\" value=\"" . $exportcsv . "\" />\r\n";
	echo "<input type=\"submit\" class=\"button-secondary\" name=\"csv\" value=\"" . __('Save Emails to CSV File', 'subscribe2') . "\" /></td>\r\n";
} else {
	echo "<td width=\"25%\"></td>";
}
echo "</tr>";

if ( !empty($subscribers) ) {
	echo "<tr><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"button-secondary\" name=\"process\" value=\"" . __('Process', 'subscribe2') . "\" /></td>\r\n";
	echo "<td colspan=\"3\" align=\"right\">" . $strip . "</td></tr>\r\n";
}
if ( !empty($subscribers) ) {
	if ( is_int($this->subscribe2_options['entries']) ) {
		$subscriber_chunks = array_chunk($subscribers, $this->subscribe2_options['entries']);
	} else {
		$subscriber_chunks = array_chunk($subscribers, 25);
	}
	$chunk = $page - 1;
	$subscribers = $subscriber_chunks[$chunk];
	echo "<tr class=\"alternate\" style=\"height:1.5em;\">\r\n";
	echo "<td width=\"4%\" align=\"center\">";
	echo "<img src=\"" . $urlpath . "include/accept.png\" alt=\"&lt;\" title=\"" . __('Confirm this email address', 'subscribe2') . "\" /></td>\r\n";
	echo "<td width=\"4%\" align=\"center\">";
	echo "<img src=\"" . $urlpath . "include/exclamation.png\" alt=\"&gt;\" title=\"" . __('Unconfirm this email address', 'subscribe2') . "\" /></td>\r\n";
	echo "<td width=\"4%\" align=\"center\">";
	echo "<img src=\"" . $urlpath . "include/cross.png\" alt=\"X\" title=\"" . __('Delete this email address', 'subscribe2') . "\" /></td><td colspan=\"3\"></td></tr>\r\n";
	echo "<tr class=\"\"><td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"confirm_checkall\" /></td>\r\n";
	echo "<td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"unconfirm_checkall\" /></td>\r\n";
	echo "<td align=\"center\"><input type=\"checkbox\" name=\"checkall\" value=\"delete_checkall\" /></td>\r\n";
	echo "<td colspan =\"3\" align=\"left\"><strong>" . __('Select / Unselect All', 'subscribe2') . "</strong></td></tr>\r\n";

	foreach ( $subscribers as $subscriber ) {
		echo "<tr class=\"$alternate\" style=\"height:1.5em;\">";
		echo "<td align=\"center\">\r\n";
		if ( in_array($subscriber, $confirmed) ) {
			echo "</td><td align=\"center\">\r\n";
			echo "<input class=\"unconfirm_checkall\" title=\"" . __('Unconfirm this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"unconfirm[]\" value=\"" . $subscriber . "\" /></td>\r\n";
			echo "<td align=\"center\">\r\n";
			echo "<input class=\"delete_checkall\" title=\"" . __('Delete this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"delete[]\" value=\"" . $subscriber . "\" />\r\n";
			echo "</td>\r\n";
			echo "<td colspan=\"3\"><span style=\"color:#006600\">&#x221A;&nbsp;&nbsp;</span><abbr title=\"" . $this->signup_ip($subscriber) . "\"><a href=\"mailto:" . $subscriber . "\">" . $subscriber . "</a></abbr>\r\n";
			echo "(<span style=\"color:#006600\">" . $this->signup_date($subscriber) . "</span>)\r\n";
		} elseif ( in_array($subscriber, $unconfirmed) ) {
			echo "<input class=\"confirm_checkall\" title=\"" . __('Confirm this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"confirm[]\" value=\"" . $subscriber . "\" /></td>\r\n";
			echo "<td align=\"center\"></td>\r\n";
			echo "<td align=\"center\">\r\n";
			echo "<input class=\"delete_checkall\" title=\"" . __('Delete this email address', 'subscribe2') . "\" type=\"checkbox\" name=\"delete[]\" value=\"" . $subscriber . "\" />\r\n";
			echo "</td>\r\n";
			echo "<td colspan=\"3\"><span style=\"color:#FF0000\">&nbsp;!&nbsp;&nbsp;&nbsp;</span><abbr title=\"" . $this->signup_ip($subscriber) . "\"><a href=\"mailto:" . $subscriber . "\">" . $subscriber . "</a></abbr>\r\n";
			echo "(<span style=\"color:#FF0000\">" . $this->signup_date($subscriber) . "</span>)\r\n";
		} elseif ( in_array($subscriber, $all_users) ) {
			$user_info = get_user_by('email', $subscriber);
			echo "</td><td align=\"center\"></td><td align=\"center\"></td>\r\n";
			echo "<td colspan=\"3\"><span style=\"color:#006600\">&reg;&nbsp;&nbsp;</span><abbr title=\"" . $user_info->user_login . "\"><a href=\"mailto:" . $subscriber . "\">" . $subscriber . "</a></abbr>\r\n";
			echo "(<a href=\"" . get_option('siteurl') . "/wp-admin/admin.php?page=s2&amp;email=" . urlencode($subscriber) . "\">" . __('edit', 'subscribe2') . "</a>)\r\n";
		}
		echo "</td></tr>\r\n";
		('alternate' == $alternate) ? $alternate = '' : $alternate = 'alternate';
	}
} else {
	if ( $_POST['searchterm'] ) {
		echo "<tr><td colspan=\"6\" align=\"center\"><b>" . __('No matching subscribers found', 'subscribe2') . "</b></td></tr>\r\n";
	} else {
		echo "<tr><td colspan=\"6\" align=\"center\"><b>" . __('NONE', 'subscribe2') . "</b></td></tr>\r\n";
	}
}
if ( !empty($subscribers) ) {
	echo "<tr class=\"$alternate\"><td colspan=\"3\" align=\"center\"><input type=\"submit\" class=\"button-secondary\" name=\"process\" value=\"" . __('Process', 'subscribe2') . "\" /></td>\r\n";
	echo "<td colspan=\"3\" align=\"right\">" . $strip . "</td></tr>\r\n";
}
echo "</table>\r\n";
echo "</div>\r\n";

// show bulk managment form if filtered in some Registered Users
if ( in_array($what, array('registered', 'all_users')) || is_numeric($what) ) {
	echo "<div class=\"s2_admin\" id=\"s2_bulk_manage\">\r\n";
	echo "<h2>" . __('Bulk Management', 'subscribe2') . "</h2>\r\n";
	if ( $this->subscribe2_options['email_freq'] == 'never' ) {
		echo __('Preferences for Registered Users selected in the filter above can be changed using this section.', 'subscribe2') . "<br />\r\n";
		echo "<strong><em style=\"color: red\">" . __('Consider User Privacy as changes cannot be undone', 'subscribe2') . "</em></strong><br />\r\n";
		echo "<br />" . __('Action to perform', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"manage\" value=\"subscribe\" checked=\"checked\" /> " . __('Subscribe', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"manage\" value=\"unsubscribe\" /> " . __('Unsubscribe', 'subscribe2') . "</label><br /><br />\r\n";
		$this->display_category_form();
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_categories\" value=\"" . __('Bulk Update Categories', 'subscribe2') . "\" /></p>";
		echo "<br />" . __('Send email as', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"html\" /> " . __('HTML - Full', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"html_excerpt\" /> " . __('HTML - Excerpt', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"post\" /> " . __('Plain Text - Full', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"format\" value=\"excerpt\" checked=\"checked\" /> " . __('Plain Text - Excerpt', 'subscribe2') . "</label>\r\n";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_format\" value=\"" . __('Bulk Update Format', 'subscribe2') . "\" /></p>";
	} else {
		echo __('Preferences for Registered Users selected in the filter above can be changed using this section.', 'subscribe2') . "<br />\r\n";
		echo "<strong><em style=\"color: red\">" . __('Consider User Privacy as changes cannot be undone', 'subscribe2') . "</em></strong><br />\r\n";
		echo "<br />" . __('Subscribe Selected Users to recieve a periodic digest notification', 'subscribe2') . ":\r\n";
		echo "<label><input type=\"radio\" name=\"sub_category\" value=\"digest\" checked=\"checked\" /> ";
		echo __('Yes', 'subscribe2') . "</label>&nbsp;&nbsp;\r\n";
		echo "<label><input type=\"radio\" name=\"sub_category\" value=\"-1\" /> ";
		echo __('No', 'subscribe2') . "</label>";
		echo "<p class=\"submit\"><input type=\"submit\" class=\"button-primary\" name=\"sub_digest\" value=\"" . __('Bulk Update Digest Subscription', 'subscribe2') . "\" /></p>";
	}
	echo "</div>\r\n";
}
echo "</form></div>\r\n";

include(ABSPATH . 'wp-admin/admin-footer.php');
// just to be sure
die;
?>