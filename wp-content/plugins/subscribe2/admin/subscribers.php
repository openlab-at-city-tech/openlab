<?php
if ( ! function_exists( 'add_action' ) ) {
	exit();
}

global $subscribers, $what, $current_tab;

// detect or define which tab we are in
$current_tab = isset( $_GET['tab'] ) ? esc_attr( $_GET['tab'] ) : 'public';

// Access function to allow display for form elements
require_once S2PATH . 'classes/class-s2-forms.php';
$s2_forms = new s2_forms();

// Instantiate and prepare our table data - this also runs the bulk actions
if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}
if ( ! class_exists( 'Subscribe2_List_Table' ) ) {
	require_once S2PATH . 'classes/class-s2-list-table.php';
	$s2_list_table = new S2_List_Table();
}

// was anything POSTed ?
if ( isset( $_POST['s2_admin'] ) ) {
	$s2_request_category = '';
	if (isset($_REQUEST['category']) && $_REQUEST['category']) {
		$s2_request_category = $_REQUEST['category'];
	}
	if ( false === wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $s2_list_table->_args['plural'] ) ) {
		die( '<p>' . esc_html__( 'Security error! Your request cannot be completed.', 'subscribe2' ) . '</p>' );
	}

	if ( ! empty( $_POST['addresses'] ) ) {
		$reg_sub_error = '';
		$pub_sub_error = '';
		$unsub_error   = '';
		$email_error   = '';
		$message       = '';
		foreach ( preg_split( '/[\s,]+/', $_POST['addresses'] ) as $email ) {
			$clean_email = $this->sanitize_email( $email );
			if ( false === $this->validate_email( $clean_email ) ) {
				( '' === $email_error ) ? $email_error = "$email" : $email_error .= ", $email";
					continue;
			} else {
				if ( isset( $_POST['subscribe'] ) ) {
					if ( false !== $this->is_public( $clean_email ) ) {
						( '' === $pub_sub_error ) ? $pub_sub_error = "$clean_email" : $pub_sub_error .= ", $clean_email";
						continue;
					}
					if ( $this->is_registered( $clean_email ) ) {
						( '' === $reg_sub_error ) ? $reg_sub_error = "$clean_email" : $reg_sub_error .= ", $clean_email";
						continue;
					}
					$this->add( $clean_email, true );
					$message = __( 'Address(es) subscribed!', 'subscribe2' );
				} elseif ( isset( $_POST['unsubscribe'] ) ) {
					if ( false === $this->is_public( $clean_email ) || $this->is_registered( $clean_email ) ) {
						( '' === $unsub_error ) ? $unsub_error = "$clean_email" : $unsub_error .= ", $clean_email";
						continue;
					}
					$this->delete( $clean_email );
					$message = __( 'Address(es) unsubscribed!', 'subscribe2' );
				}
			}
		}
		if ( '' !== $reg_sub_error ) {
			echo '<div id="message" class="error"><p><strong>' . esc_html__( 'Some emails were not processed, the following are already Registered Subscribers', 'subscribe2' ) . ':<br>' . esc_html( $reg_sub_error ) . '</strong></p></div>';
		}
		if ( '' !== $pub_sub_error ) {
			echo '<div id="message" class="error"><p><strong>' . esc_html__( 'Some emails were not processed, the following are already Public Subscribers', 'subscribe2' ) . ':<br>' . esc_html( $pub_sub_error ) . '</strong></p></div>';
		}
		if ( '' !== $unsub_error ) {
			echo '<div id="message" class="error"><p><strong>' . esc_html__( 'Some emails were not processed, the following were not in the database', 'subscribe2' ) . ':<br> ' . esc_html( $unsub_error ) . '</strong></p></div>';
		}
		if ( '' !== $email_error ) {
			echo '<div id="message" class="error"><p><strong>' . esc_html__( 'Some emails were not processed, the following were invalid email addresses', 'subscribe2' ) . ':<br> ' . esc_html( $email_error ) . '</strong></p></div>';
		}
		if ( '' !== $message ) {
			echo '<div id="message" class="updated fade"><p><strong>' . esc_html( $message ) . '</strong></p></div>';
		}
		$_POST['what'] = 'confirmed';
	} elseif ( isset( $_POST['remind'] ) ) {
		$this->remind( $_POST['reminderemails'] );
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Reminder Email(s) Sent!', 'subscribe2' ) . '</strong></p></div>';
	} elseif ( isset( $_POST['sub_categories'] ) && 'subscribe' === (isset($_POST['manage']) ? $_POST['manage'] : '') ) {
		if ( isset( $_REQUEST['subscriber'] ) ) {
			$this->subscribe_registered_users( implode( ",\r\n", $_REQUEST['subscriber'] ), $s2_request_category );
		} else {
			$this->subscribe_registered_users( $_POST['exportcsv'], $s2_request_category );
		}
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Registered Users Subscribed!', 'subscribe2' ) . '</strong></p></div>';
	} elseif ( isset( $_POST['sub_categories'] ) && 'unsubscribe' === (isset($_POST['manage']) ? $_POST['manage'] : '') ) {
		if ( isset( $_REQUEST['subscriber'] ) ) {
			$this->unsubscribe_registered_users( implode( ",\r\n", $_REQUEST['subscriber'] ), $s2_request_category );
		} else {
			$this->unsubscribe_registered_users( $_POST['exportcsv'], $s2_request_category );
		}
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Registered Users Unsubscribed!', 'subscribe2' ) . '</strong></p></div>';
	} elseif ( isset( $_POST['sub_format'] ) ) {
		if ( isset( $_REQUEST['subscriber'] ) ) {
			$this->format_change( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['format'] );
		} else {
			$this->format_change( $_POST['exportcsv'], $_POST['format'] );
		}
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Format updated for Selected Registered Users!', 'subscribe2' ) . '</strong></p></div>';
	} elseif ( isset( $_POST['sub_digest'] ) ) {
		if ( isset( $_REQUEST['subscriber'] ) ) {
			$this->digest_change( implode( ",\r\n", $_REQUEST['subscriber'] ), $_POST['sub_category'] );
		} else {
			$this->digest_change( $_POST['exportcsv'], $_POST['sub_category'] );
		}
		echo '<div id="message" class="updated fade"><p><strong>' . esc_html__( 'Digest Subscription updated for Selected Registered Users!', 'subscribe2' ) . '</strong></p></div>';
	}
}

if ( 'registered' === $current_tab ) {
	// Get Registered Subscribers
	$registered = $this->get_registered( 'return=emailid' );
	$all_users  = $this->get_all_registered( 'emailid' );

	// safety check for our arrays
	if ( '' === $registered ) {
		$registered = array();
	}
	if ( '' === $all_users ) {
		$all_users = array();
	}
} else {
	//Get Public Subscribers
	$confirmed   = $this->get_public();
	$unconfirmed = $this->get_public( 0 );
	// safety check for our arrays
	if ( '' === $confirmed ) {
		$confirmed = array();
	}
	if ( '' === $unconfirmed ) {
		$unconfirmed = array();
	}
}

$reminderform = false;
if ( isset( $_REQUEST['what'] ) ) {
	if ( 'public' === $_REQUEST['what'] ) {
		$what        = 'public';
		$subscribers = array_merge( (array) $confirmed, (array) $unconfirmed );
	} elseif ( 'confirmed' === $_REQUEST['what'] ) {
		$what        = 'confirmed';
		$subscribers = $confirmed;
	} elseif ( 'unconfirmed' === $_REQUEST['what'] ) {
		$what        = 'unconfirmed';
		$subscribers = $unconfirmed;
		if ( ! empty( $subscribers ) ) {
			$reminderemails = implode( ',', $subscribers );
			$reminderform   = true;
		}
	} elseif ( is_numeric( $_REQUEST['what'] ) ) {
		$what        = intval( $_REQUEST['what'] );
		$subscribers = $this->get_registered( "cats=$what&return=emailid" );
	} elseif ( 'registered' === $_REQUEST['what'] ) {
		$what        = 'registered';
		$subscribers = $registered;
	} elseif ( 'all_users' === $_REQUEST['what'] ) {
		$what        = 'all_users';
		$subscribers = $all_users;
	}
} else {
	if ( 'public' === $current_tab ) {
		$what        = 'public';
		$subscribers = array_merge( (array) $confirmed, (array) $unconfirmed );
	} else {
		$what        = 'all_users';
		$subscribers = $all_users;
	}
}

if ( ! empty( $_POST['s'] ) ) {
	if ( 'registered' === $current_tab ) {
		foreach ( $subscribers as $subscriber ) {
			if ( is_numeric( stripos( $subscriber['user_email'], $_POST['s'] ) ) ) {
				$result[] = $subscriber;
			}
		}
	} else {
		foreach ( $subscribers as $subscriber ) {
			if ( is_numeric( stripos( $subscriber, $_POST['s'] ) ) ) {
				$result[] = $subscriber;
			}
		}
	}
	$subscribers = $result;
}

$s2_list_table->prepare_items();

// show our form
echo '<div class="wrap">';
echo '<h1>' . esc_html__( 'Subscribers', 'subscribe2' ) . '</h1>' . "\r\n";
$s2tabs = array(
	'public'     => __( 'Public Subscribers', 'subscribe2' ),
	'registered' => __( 'Registered Subscribers', 'subscribe2' ),
);
echo '<h2 class="nav-tab-wrapper">';
foreach ( $s2tabs as $tab_key => $tab_caption ) {
	$active = ( $current_tab === $tab_key ) ? 'nav-tab-active' : '';
	echo '<a class="nav-tab ' . esc_attr( $active ) . '" href="' . esc_url( '?page=s2_tools&amp;tab=' . $tab_key ) . '">' . esc_html( $tab_caption ) . '</a>';
}
echo '</h2>';
echo '<form method="post">' . "\r\n";

echo '<input type="hidden" name="s2_admin" />' . "\r\n";
switch ( $current_tab ) {
	case 'public':
		echo '<input type="hidden" id="s2_location" name="s2_location" value="public" />' . "\r\n";
		echo '<div class="s2_admin" id="s2_add_subscribers">' . "\r\n";
		echo '<h2>' . esc_html__( 'Add/Remove Subscribers', 'subscribe2' ) . '</h2>' . "\r\n";
		echo '<p>' . esc_html__( 'Enter addresses, one per line or comma-separated', 'subscribe2' ) . '<br>' . "\r\n";
		echo '<textarea rows="2" cols="80" name="addresses"></textarea></p>' . "\r\n";
		echo '<input type="hidden" name="s2_admin" />' . "\r\n";
		echo '<p class="submit" style="border-top: none;"><input type="submit" class="button-primary" name="subscribe" value="' . esc_attr( __( 'Subscribe', 'subscribe2' ) ) . '" />';
		echo '&nbsp;<input type="submit" class="button-primary" name="unsubscribe" value="' . esc_attr( __( 'Unsubscribe', 'subscribe2' ) ) . '" /></p>' . "\r\n";
		echo '</div>' . "\r\n";

		// subscriber lists
		echo '<div class="s2_admin" id="s2_current_subscribers">' . "\r\n";
		echo '<h2>' . esc_html__( 'Current Subscribers', 'subscribe2' ) . '</h2>' . "\r\n";
		echo '<br>';
		$cats    = $this->all_cats();
		$cat_ids = array();
		foreach ( $cats as $category ) {
			$cat_ids[] = $category->term_id;
		}
		$exclude = array_merge( array( 'all', 'all_users', 'registered' ), $cat_ids );
		break;

	case 'registered':
		echo '<input type="hidden" id="s2_location" name="s2_location" value="registered" />' . "\r\n";
		echo '<div class="s2_admin" id="s2_add_subscribers">' . "\r\n";
		echo '<h2>' . esc_html__( 'Add/Remove Subscribers', 'subscribe2' ) . '</h2>' . "\r\n";
		echo '<p class="submit" style="border-top: none;"><a class="button-primary" href="' . esc_url( admin_url( 'user-new.php' ) ) . '">' . esc_html__( 'Add Registered User', 'subscribe2' ) . '</a></p>' . "\r\n";

		echo "</div>\r\n";

		// subscriber lists
		echo '<div class="s2_admin" id="s2_current_subscribers">' . "\r\n";
		echo '<h2>' . esc_html__( 'Current Subscribers', 'subscribe2' ) . '</h2>' . "\r\n";
		echo '<br>';
		$exclude = array( 'all', 'public', 'confirmed', 'unconfirmed' );
		break;
}

// show the selected subscribers
echo '<table style="width: 100%; border-collapse: separate; border-spacing: 0px; *border-collapse: expression("separate", cellSpacing = "0px");"><tr>';
echo '<td style="width: 50%; text-align: left;">';
$this->display_subscriber_dropdown( $what, __( 'Filter', 'subscribe2' ), $exclude );
echo '</td>' . "\r\n";
if ( $reminderform ) {
	echo '<td style="width: 25%; text-align: right;"><input type="hidden" name="reminderemails" value="' . esc_attr( $reminderemails ) . '" />' . "\r\n";
	echo '<input type="submit" class="button-secondary" name="remind" value="' . esc_attr( __( 'Send Reminder Email', 'subscribe2' ) ) . '" /></td>' . "\r\n";
} else {
	echo '<td style="width: 25%;"></td>';
}
if ( ! empty( $subscribers ) ) {
	if ( 'public' === $current_tab ) {
		$exportcsv = implode( ",\r\n", $subscribers );
	} else {
		$exportcsv = '';
		foreach ( $subscribers as $subscriber ) {
			( '' === $exportcsv ) ? $exportcsv = $subscriber['user_email'] : $exportcsv .= ",\r\n" . $subscriber['user_email'];
		}
	}
	echo '<td style="width: 25%; text-align: right;"><input type="hidden" name="exportcsv" value="' . esc_attr( $exportcsv ) . '" />' . "\r\n";
	echo '<input type="submit" class="button-secondary" name="csv" value="' . esc_attr( __( 'Save Emails to CSV File', 'subscribe2' ) ) . '" /></td>' . "\r\n";
} else {
	echo '<td style="width: 25%;"></td>';
}
echo '</tr></table>';

// output our subscriber table
$s2_list_table->search_box( __( 'Search', 'subscribe2' ), 'search_id' );
$s2_list_table->display();
echo '</div>' . "\r\n";

// show bulk management form if filtered in some Registered Users
if ( 'registered' === $current_tab ) {
	echo '<div class="s2_admin" id="s2_bulk_manage">' . "\r\n";
	echo '<h2>' . esc_html__( 'Bulk Management', 'subscribe2' ) . '</h2>' . "\r\n";
	if ( 'never' === $this->subscribe2_options['email_freq'] ) {
		$categories = array();
		if ( isset( $_POST['category'] ) ) {
			$categories = $_POST['category'];
		}
		$manage = '';
		if ( isset( $_POST['manage'] ) ) {
			$manage = $_POST['manage'];
		}
		$format = '';
		if ( isset( $_POST['format'] ) ) {
			$format = $_POST['format'];
		}
		echo esc_html__( 'Preferences for Registered Users selected above can be changed using this section.', 'subscribe2' ) . '<br>' . "\r\n";
		echo '<strong><em style="color: red">' . esc_html__( 'Consider User Privacy as changes cannot be undone', 'subscribe2' ) . '</em></strong><br>' . "\r\n";
		echo '<br>' . esc_html__( 'Action to perform', 'subscribe2' ) . ':' . "\r\n";
		echo '<label><input type="radio" name="manage" value="subscribe"' . checked( $manage, 'subscribe', false ) . ' /> ' . esc_html__( 'Subscribe', 'subscribe2' ) . '</label>&nbsp;&nbsp;' . "\r\n";
		echo '<label><input type="radio" name="manage" value="unsubscribe"' . checked( $manage, 'unsubscribe', false ) . ' /> ' . esc_html__( 'Unsubscribe', 'subscribe2' ) . '</label><br><br>' . "\r\n";
		if ( '1' === $this->subscribe2_options['reg_override'] ) {
			$s2_forms->display_category_form( $categories, 1 );
		} else {
			$s2_forms->display_category_form( $categories, 0 );
		}
		echo '<p class="submit"><button class="button-primary" name="sub_categories" onclick="return bmCheck();">' . esc_html__( 'Bulk Update Categories', 'subscribe2' ) . '</button></p>';
		echo '<br>' . esc_html__( 'Send email as', 'subscribe2' ) . ':' . "\r\n";
		echo '<label><input type="radio" name="format" value="html"' . checked( $format, 'html', false ) . ' /> ' . esc_html__( 'HTML - Full', 'subscribe2' ) . '</label>&nbsp;&nbsp;' . "\r\n";
		echo '<label><input type="radio" name="format" value="html_excerpt"' . checked( $format, 'html_excerpt', false ) . ' /> ' . esc_html__( 'HTML - Excerpt', 'subscribe2' ) . '</label>&nbsp;&nbsp;' . "\r\n";
		echo '<label><input type="radio" name="format" value="post"' . checked( $format, 'post', false ) . ' /> ' . esc_html__( 'Plain Text - Full', 'subscribe2' ) . '</label>&nbsp;&nbsp;' . "\r\n";
		echo '<label><input type="radio" name="format" value="excerpt"' . checked( $format, 'excerpt', false ) . '/> ' . esc_html__( 'Plain Text - Excerpt', 'subscribe2' ) . '</label>' . "\r\n";
		echo '<p class="submit"><button class="button-primary" name="sub_format" onclick="return bmCheck();">' . esc_html__( 'Bulk Update Format', 'subscribe2' ) . '</button></p>';
	} else {
		$sub_cats = '';
		if ( isset( $_POST['sub_category'] ) ) {
			$sub_cats = $_POST['sub_category'];
		}
		echo esc_html__( 'Preferences for Registered Users selected above can be changed using this section.', 'subscribe2' ) . "<br>\r\n";
		echo '<strong><em style="color: red">' . esc_html__( 'Consider User Privacy as changes cannot be undone.', 'subscribe2' ) . '</em></strong><br>' . "\r\n";
		echo '<br>' . esc_html__( 'Subscribe Selected Users to receive a periodic digest notification', 'subscribe2' ) . ':' . "\r\n";
		echo '<label><input type="radio" name="sub_category" value="digest"' . checked( $sub_cats, 'digest', false ) . ' /> ';
		echo esc_html__( 'Yes', 'subscribe2' ) . '</label>&nbsp;&nbsp;' . "\r\n";
		echo '<label><input type="radio" name="sub_category" value="-1"' . checked( $sub_cats, '-1', false ) . ' /> ';
		echo esc_html__( 'No', 'subscribe2' ) . '</label>';
		echo '<p class="submit"><button class="button-primary" name="sub_digest" onclick="return bmCheck();">' . esc_html__( 'Bulk Update Digest Subscription', 'subscribe2' ) . '</button></p>';
	}
	echo '</div>' . "\r\n";
}
echo '</form></div>' . "\r\n";

require ABSPATH . 'wp-admin/admin-footer.php';
// just to be sure
die;
