<?php
class S2_Admin extends S2_Core {
	/* ===== WordPress menu registration and scripts ===== */
	/**
	 * Hook the menu
	 */
	public function admin_menu() {
		add_menu_page( __( 'Subscribe2', 'subscribe2' ), __( 'Subscribe2', 'subscribe2' ), apply_filters( 's2_capability', 'read', 'user' ), 's2', null, S2URL . 'include/email-edit.png' );

		$s2user = add_submenu_page( 's2', __( 'Your Subscriptions', 'subscribe2' ), __( 'Your Subscriptions', 'subscribe2' ), apply_filters( 's2_capability', 'read', 'user' ), 's2', array( &$this, 'user_menu' ) );
		add_action( "admin_print_scripts-$s2user", array( &$this, 'checkbox_form_js' ) );
		add_action( "admin_print_styles-$s2user", array( &$this, 'user_admin_css' ) );
		add_action( 'load-' . $s2user, array( &$this, 'user_help' ) );

		$s2subscribers = add_submenu_page( 's2', __( 'Subscribers', 'subscribe2' ), __( 'Subscribers', 'subscribe2' ), apply_filters( 's2_capability', 'manage_options', 'manage' ), 's2_tools', array( &$this, 'subscribers_menu' ) );
		add_action( "admin_print_scripts-$s2subscribers", array( &$this, 'checkbox_form_js' ) );
		add_action( "admin_print_scripts-$s2subscribers", array( &$this, 'subscribers_form_js' ) );
		add_action( "admin_print_scripts-$s2subscribers", array( &$this, 'subscribers_css' ) );
		add_action( 'load-' . $s2subscribers, array( &$this, 'subscribers_help' ) );
		add_action( 'load-' . $s2subscribers, array( &$this, 'subscribers_options' ) );

		$s2settings = add_submenu_page( 's2', __( 'Settings', 'subscribe2' ), __( 'Settings', 'subscribe2' ), apply_filters( 's2_capability', 'manage_options', 'settings' ), 's2_settings', array( &$this, 'settings_menu' ) );
		add_action( "admin_print_scripts-$s2settings", array( &$this, 'checkbox_form_js' ) );
		add_action( "admin_print_scripts-$s2settings", array( &$this, 'option_form_js' ) );
		add_action( "admin_print_scripts-$s2settings", array( &$this, 'dismiss_js' ) );
		add_filter( 'plugin_row_meta', array( &$this, 'plugin_links' ), 10, 2 );
		add_action( 'load-' . $s2settings, array( &$this, 'settings_help' ) );

		$s2mail = add_submenu_page( 's2', __( 'Send Email', 'subscribe2' ), __( 'Send Email', 'subscribe2' ), apply_filters( 's2_capability', 'publish_posts', 'send' ), 's2_posts', array( &$this, 'write_menu' ) );
		add_action( 'load-' . $s2mail, array( &$this, 'mail_help' ) );
	}

	/**
	 * Contextual Help
	 */
	public function user_help() {
		$screen = get_current_screen();
		if ( 'never' !== $this->subscribe2_options['email_freq'] ) {
			$screen->add_help_tab(
				array(
					'id'      => 's2-user-help1',
					'title'   => __( 'Overview', 'subscribe2' ),
					'content' => '<p>' . __( 'From this page you can opt in or out of receiving a periodical digest style email of blog posts.', 'subscribe2' ) . '</p>',
				)
			);
		} else {
			$screen->add_help_tab(
				array(
					'id'      => 's2-user-help1',
					'title'   => __( 'Overview', 'subscribe2' ),
					'content' => '<p>' . __( 'From this page you can control your subscription preferences. Choose the email format you wish to receive, which categories you would like to receive notification for and depending on the site settings which authors you would like to read.', 'subscribe2' ) . '</p>',
				)
			);
		}
	}

	public function subscribers_help() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'      => 's2-subscribers-help1',
				'title'   => __( 'Overview', 'subscribe2' ),
				'content' => '<p>' . __( 'From this page you can manage your subscribers.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-subscribers-help2',
				'title'   => __( 'Public Subscribers', 'subscribe2' ),
				'content' => '<p>' . __( 'Public Subscribers are subscribers who have used the plugin form and only provided their email address.', 'subscribe2' ) . '</p><p>' . __( 'On this page public subscribers can be viewed, searched, deleted and also toggled between Confirmed and Unconfirmed status.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-subscribers-help3',
				'title'   => __( 'Registered Subscribers', 'subscribe2' ),
				'content' => '<p>' . __( 'Registered Subscribers are subscribers who have registered in WordPress and have a username and password.', 'subscribe2' ) .
				'</p><p>' . __( 'Registered Subscribers have greater personal control over their subscription. They can change the format of the email and also select which categories and authors they want to receive notifications about.', 'subscribe2' ) .
				'</p><p>' . __( 'On this page registered subscribers can be viewed and searched. User accounts can be deleted from here with any posts created by those users being assigned to the currently logged in user. Bulk changes can be applied to all user settings changing their subscription email format and categories.', 'subscribe2' ) . '</p>',
			)
		);
	}

	public function subscribers_options() {
		$option = 'per_page';

		$args = array(
			'label'   => __( 'Number of subscribers per page: ', 'subscribe2' ),
			'default' => 25,
			'option'  => 'subscribers_per_page',
		);
		add_screen_option( $option, $args );
	}

	public function subscribers_set_screen_option( $status, $option, $value ) {
		if ( 'subscribers_per_page' === $option && false === $status ) {
			if ( $value < 1 || $value > 999 ) {
				return;
			}
			return $value;
		}
	}

	public function settings_help() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help1',
				'title'   => __( 'Overview', 'subscribe2' ),
				'content' => '<p>' . __( 'From this page you can adjust the Settings for Subscribe2.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help2',
				'title'   => __( 'Email Settings', 'subscribe2' ),
				'content' => '<p>' . __( 'This section allows you to specify settings that apply to the emails generated by the site.', 'subscribe2' ) .
				'</p><p>' . __( 'Emails can be sent to individual subscribers by setting the number of recipients per email to 1. A setting greater than one will group recipients together and make use of the BCC emails header. A setting of 0 sends a single email with all subscribers in one large BCC group. A setting of 1 looks less like spam email to filters but takes longer to process.', 'subscribe2' ) .
				'</p><p>' . __( 'This section is also where the sender of the email on this page is chosen. You can choose Post Author or your Blogname but it is recommended to create a user account with an email address that really exists and shares the same domain name as your site (the bit after the @ should be the same as your sites web address) and then use this account.', 'subscribe2' ) .
				'</p><p>' . __( 'This page also configures the frequency of emails. This can be at the time new posts are made (per post) or periodically with an excerpt of each post made (digest). Additionally the post types (pages, private, password protected) can also be configured here.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help3',
				'title'   => __( 'Templates', 'subscribe2' ),
				'content' => '<p>' . __( 'This section allows you to customise the content of your notification emails.', 'subscribe2' ) .
				'</p><p>' . __( 'There are special {KEYWORDS} that are used by Subscribe2 to place content into the final email. The template also accepts regular text and HTML as desired in the final emails.', 'subscribe2' ) .
				'</p><p>' . __( 'The {KEYWORDS} are listed on the right of the templates, note that some are for per post emails only and some are for digest emails only. Make sure the correct keywords are used based upon the Email Settings.', 'subscribe2' ) .
				'</p><p>' . __( 'The Notification Email template is used for sending notifications of new posts. The Subscribe / Unsubscribe confirmation template is sent when a new subscription or unsubscription request is made. The Reminder template is used to send reminder emails; this is done automatically or can be done manually.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help4',
				'title'   => __( 'Registered Users', 'subscribe2' ),
				'content' => '<p>' . __( 'This section allows settings that apply to Registered Subscribers to be configured.', 'subscribe2' ) .
				'</p><p>' . __( 'Categories can be made compulsory so emails are always sent to Public and Registered Subscribers for posts in these categories. They can also be excluded so that emails are not generated for Subscribers. Registered Subscribers can be allowed to bypass category exclusions. Excluded categories take precedence over Compulsory categories.', 'subscribe2' ) .
				'</p><p>' . __( 'A set of default settings for new users can also be specified using the Auto Subscribe section. Settings specified here will be applied to any newly created user accounts while Subscribe2 is activated.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help5',
				'title'   => __( 'Appearance', 'subscribe2' ),
				'content' => '<p>' . __( 'This section allows you to enable several aspect of the plugin such as Widgets and editor buttons.', 'subscribe2' ) .
				'</p><p>' . __( 'AJAX mode can be enabled that is intended to work with the shortcode link parameter so that a dialog opens in the centre of the browser rather then using the regular form.', 'subscribe2' ) .
				'</p><p>' . __( 'The email over ride check box can be set to be automatically checked for every new post and page from here to, this may be useful if you will only want to send very occasional notifications for specific posts. You can then uncheck this box just before you publish your content.', 'subscribe2' ) . '</p>',
			)
		);
		$screen->add_help_tab(
			array(
				'id'      => 's2-settings-help7',
				'title'   => __( 'Miscellaneous', 'subscribe2' ),
				'content' => '<p>' . __( 'This section contains a place to bar specified domains from becoming Public Subscribers and links to help and support pages.', 'subscribe2' ) .
				'</p>',
			)
		);
	}

	public function mail_help() {
		$screen = get_current_screen();
		$screen->add_help_tab(
			array(
				'id'      => 's2-send-mail-help1',
				'title'   => __( 'Overview', 'subscribe2' ),
				'content' => '<p>' . __( 'From this page you can send emails to the recipients in the group selected in the drop down.', 'subscribe2' ) .
				'</p><p>' . __( '<strong>Preview</strong> will send a preview of the email to the currently logged in user. <strong>Send</strong> will send the email to the recipient list.', 'subscribe2' ) . '</p>',
			)
		);
	}

	/**
	 * Hook for Admin Drop Down Icons
	 */
	public function ozh_s2_icon() {
		return S2URL . 'include/email-edit.png';
	}

	/**
	 * Insert Javascript and CSS into admin_headers
	 */
	public function checkbox_form_js() {
		wp_register_script( 's2_checkbox', S2URL . 'include/s2-checkbox' . $this->script_debug . '.js', array( 'jquery' ), '1.4', true );
		wp_enqueue_script( 's2_checkbox' );
	}

	public function user_admin_css() {
		wp_register_style( 's2_user_admin', S2URL . 'include/s2-user-admin' . $this->script_debug . '.css', array(), '1.0' );
		wp_enqueue_style( 's2_user_admin' );
	}

	public function option_form_js() {
		wp_register_script( 's2_edit', S2URL . 'include/s2-edit' . $this->script_debug . '.js', array( 'jquery' ), '1.3', true );
		wp_enqueue_script( 's2_edit' );
		if ( 'never' !== $this->subscribe2_options['email_freq'] ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'jquery-style', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css', array(), '1.12.1' );
			wp_register_script( 's2_date_time', S2URL . 'include/s2-date-time' . $this->script_debug . '.js', array( 'jquery-ui-datepicker' ), '1.1', true );
			wp_enqueue_script( 's2_date_time' );
		}
	}

	public function dismiss_js() {
		wp_register_script( 's2_dismiss', S2URL . 'include/s2-dismiss' . $this->script_debug . '.js', array( 'jquery' ), '1.1', true );
		$translation_array = array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 's2_dismiss_nonce' ),
		);
		wp_localize_script( 's2_dismiss', 's2DismissScriptStrings', $translation_array );
		wp_enqueue_script( 's2_dismiss' );
	}

	public function s2_dismiss_notice_handler() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 's2_dismiss_nonce' ) ) {
			return false;
		}
		$this->subscribe2_options['dismiss_sender_warning'] = '1';
		update_option( 'subscribe2_options', $this->subscribe2_options );
		wp_die();
	}

	public function subscribers_form_js() {
		wp_register_script( 's2_subscribers', S2URL . 'include/s2-subscribers' . $this->script_debug . '.js', array(), '1.5', true );
		$translation_array = array(
			'registered_confirm_single' => __( 'You are about to delete a registered user account, any posts made by this user will be assigned to you. Are you sure?', 'subscribe2' ),
			'registered_confirm_plural' => __( 'You are about to delete registered user accounts, any posts made by these users will be assigned to you. Are you sure?', 'subscribe2' ),
			'public_confirm_single'     => __( 'You are about to delete a public subscriber. Are you sure?', 'subscribe2' ),
			'public_confirm_plural'     => __( 'You are about to delete public subscribers. Are you sure?', 'subscribe2' ),
			'bulk_manage_all'           => __( 'You are about to make Bulk Management changes to all Registered Users. Are you sure?', 'subscribe2' ),
			'bulk_manage_single'        => __( 'You are about to make Bulk Management changes to the selected Registered User. Are you sure?', 'subscribe2' ),
			'bulk_manage_plural'        => __( 'You are about to make Bulk Management changes to the selected Registered Users. Are you sure?', 'subscribe2' ),
		);
		wp_localize_script( 's2_subscribers', 's2ScriptStrings', $translation_array );
		wp_enqueue_script( 's2_subscribers' );
	}

	public function subscribers_css() {
		echo '<style type="text/css">';
		echo '.wp-list-table .column-date { width: 15%; }';
		echo '</style>';
	}

	/**
	 * Adds a links directly to the settings page from the plugin page
	 */
	public function plugin_links( $links, $file ) {
		if ( S2DIR . 'subscribe2.php' === $file ) {
			$links[] = '<a href="admin.php?page=s2_settings">' . __( 'Settings', 'subscribe2' ) . '</a>';
		}
		return $links;
	}

	/* ===== Menus ===== */
	/**
	 * Our subscriber management page
	 */
	public function subscribers_menu() {
		require_once S2PATH . 'admin/subscribers.php';
	}

	/**
	 * Our settings page
	 */
	public function settings_menu() {
		require_once S2PATH . 'admin/settings.php';
	}

	/**
	 * Our profile menu
	 */
	public function user_menu() {
		require_once S2PATH . 'admin/your-subscriptions.php';
	}

	/**
	 * Display the Write sub-menu
	 */
	public function write_menu() {
		require_once S2PATH . 'admin/send-email.php';
	}

	/* ===== Write Toolbar Button Functions ===== */
	/**
	 * Register our button in the QuickTags bar
	 */
	public function button_init() {
		global $pagenow;
		if ( ! in_array( $pagenow, array( 'post-new.php', 'post.php', 'page-new.php', 'page.php' ), true ) && ! strpos( esc_url( $_SERVER['REQUEST_URI'] ), 'page=s2_posts' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
			return;
		}
		if ( 'true' === get_user_option( 'rich_editing' ) ) {
			// Hook into the rich text editor
			add_filter( 'mce_external_plugins', array( &$this, 'mce_plugin' ) );
			add_filter( 'mce_buttons', array( &$this, 'mce_button' ) );
		} else {
			wp_enqueue_script( 'subscribe2_button', S2URL . 'include/s2-button' . $this->script_debug . '.js', array( 'quicktags' ), '2.0', true );
		}
	}

	/**
	 * Add buttons for Rich Text Editor
	 */
	public function mce_plugin( $arr ) {
		$arr['subscribe2'] = S2URL . 'tinymce/editor-plugin4' . $this->script_debug . '.js';
		return $arr;
	}

	public function mce_button( $arr ) {
		$arr[] = 'subscribe2';
		return $arr;
	}

	/* ===== widget functions ===== */
	/**
	 * Function to add css and js files to admin header
	 */
	public function widget_s2counter_css_and_js() {
		// ensure we only add colorpicker js to widgets page
		if ( false !== stripos( esc_url( $_SERVER['REQUEST_URI'] ), 'widgets.php' ) ) {
			wp_enqueue_style( 'farbtastic' );
			wp_enqueue_script( 'farbtastic' );
			wp_register_script( 's2_colorpicker', S2URL . 'include/s2-colorpicker' . $this->script_debug . '.js', array( 'farbtastic' ), '1.3', true );
			wp_enqueue_script( 's2_colorpicker' );
		}
	}

	/* ===== meta box functions to allow per-post override ===== */
	/**
	 * Create meta box on write pages
	 */
	public function s2_meta_init( $post_type, $post ) {
		if ( true === $this->block_editor ) {
			return;
		}

		if ( 'yes' === $this->subscribe2_options['pages'] ) {
			$s2_post_types = array( 'page', 'post' );
		} else {
			$s2_post_types = array( 'post' );
		}

		$s2_post_types = apply_filters( 's2_post_types', $s2_post_types );

		if ( ! in_array( $post_type, $s2_post_types, true ) ) {
			return;
		}

		add_meta_box(
			'subscribe2',
			__( 'Subscribe2 Notification Override', 'subscribe2' ),
			array( &$this, 's2_override_meta' ),
			$post_type,
			'advanced',
			'default',
			array(
				'__block_editor_compatible_meta_box' => false,
				'__back_compat_meta_box'             => true,
			)
		);

		add_meta_box(
			'subscribe2-preview',
			__( 'Subscribe2 Preview', 'subscribe2' ),
			array( &$this, 's2_preview_meta' ),
			$post_type,
			'side',
			'default',
			array(
				'__block_editor_compatible_meta_box' => false,
				'__back_compat_meta_box'             => true,
			)
		);

		if ( 'publish' === $post->post_status || ( 'private' === $post->post_status && 'yes' === $this->subscribe2_options['private'] ) ) {
			add_meta_box(
				'subscribe2-resend',
				__( 'Subscribe2 Resend', 'subscribe2' ),
				array( &$this, 's2_resend_meta' ),
				$post_type,
				'side',
				'default',
				array(
					'__block_editor_compatible_meta_box' => false,
					'__back_compat_meta_box'             => true,
				)
			);
		}
	}

	/**
	 * Meta override box code
	 */
	public function s2_override_meta() {
		global $post_ID;
		$s2mail = get_post_meta( $post_ID, '_s2mail', true );
		echo '<input type="hidden" name="s2meta_nonce" id="s2meta_nonce" value="' . esc_attr( wp_create_nonce( wp_hash( plugin_basename( __FILE__ ) ) ) ) . '" />';
		echo esc_html__( 'Check here to disable sending of an email notification for this post/page', 'subscribe2' );
		echo '&nbsp;&nbsp;<input type="checkbox" name="s2_meta_field" value="no"';
		if ( 'no' === $s2mail || ( '1' === $this->subscribe2_options['s2meta_default'] && '' === $s2mail ) ) {
			echo ' checked="checked"';
		}
		echo ' />';
	}

	/**
	 * Meta override box form handler
	 */
	public function s2_meta_handler( $post_id ) {
		if ( ! isset( $_POST['s2meta_nonce'] ) || ! wp_verify_nonce( $_POST['s2meta_nonce'], wp_hash( plugin_basename( __FILE__ ) ) ) ) {
			return $post_id;
		}

		if ( 'page' === $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		if ( isset( $_POST['s2_meta_field'] ) && 'no' === $_POST['s2_meta_field'] ) {
			update_post_meta( $post_id, '_s2mail', $_POST['s2_meta_field'] );
		} else {
			update_post_meta( $post_id, '_s2mail', 'yes' );
		}
	}

	/**
	 * Meta preview box code
	 */
	public function s2_preview_meta() {
		echo '<p>' . esc_html__( 'Send preview email of this post to currently logged in user:', 'subscribe2' ) . '</p>' . "\r\n";
		echo '<input class="button" name="s2_preview" type="submit" value="' . esc_attr( __( 'Send Preview', 'subscribe2' ) ) . '" />' . "\r\n";
	}

	/**
	 * Meta preview box handler
	 */
	public function s2_preview_handler() {
		if ( isset( $_POST['s2_preview'] ) ) {
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				return;
			}
			global $post, $current_user;
			if ( 'never' !== $this->subscribe2_options['email_freq'] ) {
				$this->subscribe2_cron( $current_user->user_email );
			} else {
				$this->publish( $post, $current_user->user_email );
			}
		}
	}

	/**
	 * Meta resend box code
	 */
	public function s2_resend_meta() {
		echo '<p>' . esc_html__( 'Resend the notification email of this post to current subscribers:', 'subscribe2' ) . '</p>' . "\r\n";
		echo '<input class="button" name="s2_resend" type="submit" value="' . esc_attr( __( 'Resend Notification', 'subscribe2' ) ) . '" />' . "\r\n";
	}

	/**
	 * Meta resend box handler
	 */
	public function s2_resend_handler() {
		if ( isset( $_POST['s2_resend'] ) ) {
			if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
				return;
			}
			global $post;
			$this->publish( $post );
		}
	}

	/* ===== WordPress menu helper functions ===== */
	/**
	 * Collects the signup date for all public subscribers
	 */
	public function signup_date( $email = '' ) {
		if ( '' === $email ) {
			return false;
		}

		global $wpdb;
		if ( ! empty( $this->signup_dates ) ) {
			return $this->signup_dates[ $email ];
		} else {
			$results = $wpdb->get_results( "SELECT email, date FROM $wpdb->subscribe2", ARRAY_N );
			foreach ( $results as $result ) {
				$this->signup_dates[ $result[0] ] = $result[1];
			}
			return $this->signup_dates[ $email ];
		}
	}

	/**
	 * Collects the signup time for all public subscribers
	 */
	public function signup_time( $email = '' ) {
		if ( '' === $email ) {
			return false;
		}

		global $wpdb;
		if ( ! empty( $this->signup_times ) ) {
			return $this->signup_times[ $email ];
		} else {
			$results = $wpdb->get_results( "SELECT email, time FROM $wpdb->subscribe2", ARRAY_N );
			foreach ( $results as $result ) {
				$this->signup_times[ $result[0] ] = $result[1];
			}
			return $this->signup_times[ $email ];
		}
	}

	/**
	 * Collects the ip address for all public subscribers
	 */
	public function signup_ip( $email = '' ) {
		if ( '' === $email ) {
			return false;
		}

		global $wpdb;
		if ( ! empty( $this->signup_ips ) ) {
			return $this->signup_ips[ $email ];
		} else {
			$results = $wpdb->get_results( "SELECT email, ip FROM $wpdb->subscribe2", ARRAY_N );
			foreach ( $results as $result ) {
				$this->signup_ips[ $result[0] ] = $result[1];
			}
			return $this->signup_ips[ $email ];
		}
	}

	/**
	 * Export subscriber emails and other details to CSV
	 */
	public function prepare_export( $subscribers ) {
		if ( empty( $subscribers ) ) {
			return;
		}
		$subscribers = explode( ",\r\n", $subscribers );
		natcasesort( $subscribers );

		$exportcsv = _x( 'User Email,User Type,User Name,Confirm Date,IP', 'Comma Separated Column Header names for CSV Export', 'subscribe2' );
		$all_cats  = $this->all_cats( false, 'ID' );

		foreach ( $all_cats as $cat ) {
			$exportcsv .= ',' . html_entity_decode( $cat->cat_name, ENT_QUOTES );
			$cat_ids[]  = $cat->term_id;
		}
		$exportcsv .= "\r\n";

		foreach ( $subscribers as $subscriber ) {
			if ( $this->is_registered( $subscriber ) ) {
				$user_id   = $this->get_user_id( $subscriber );
				$user_info = get_userdata( $user_id );

				$cats            = explode( ',', get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true ) );
				$subscribed_cats = '';
				foreach ( $cat_ids as $cat ) {
					( in_array( (string) $cat, $cats, true ) ) ? $subscribed_cats .= ',Yes' : $subscribed_cats .= ',No';
				}

				$exportcsv .= $subscriber . ',';
				$exportcsv .= __( 'Registered User', 'subscribe2' );
				$exportcsv .= ',' . $user_info->display_name;
				$exportcsv .= ',,' . $subscribed_cats . "\r\n";
			} else {
				if ( '1' === $this->is_public( $subscriber ) ) {
					$exportcsv .= $subscriber . ',' . __( 'Confirmed Public Subscriber', 'subscribe2' ) . ',,' . $this->signup_date( $subscriber ) . ',' . $this->signup_ip( $subscriber ) . "\r\n";
				} elseif ( '0' === $this->is_public( $subscriber ) ) {
					$exportcsv .= $subscriber . ',' . __( 'Unconfirmed Public Subscriber', 'subscribe2' ) . ',,' . $this->signup_date( $subscriber ) . ',' . $this->signup_ip( $subscriber ) . "\r\n";
				}
			}
		}

		return $exportcsv;
	}

	/**
	 * Display a table of post formats supported by the currently active theme
	 */
	public function display_format_form( $formats, $selected = array() ) {
		$half = ( count( $formats[0] ) / 2 );
		$i    = 0;
		$j    = 0;
		echo '<table style="width: 100%; border-collapse: separate; border-spacing: 2px; *border-collapse: expression(\'separate\', cellSpacing = \'2px\');" class="editform">' . "\r\n";
		echo '<tr><td style="text-align: left;" colspan="2">' . "\r\n";
		echo '<label><input type="checkbox" name="checkall" value="checkall_format" /> ' . esc_html__( 'Select / Unselect All', 'subscribe2' ) . '</label>' . "\r\n";
		echo '</td></tr>' . "\r\n";
		echo '<tr style="vertical-align: top;"><td style="width: 50%; text-align: left">' . "\r\n";
		foreach ( $formats[0] as $format ) {
			if ( $i >= $half && 0 === $j ) {
				echo '</td><td style="width: 50%; text-align: left">' . "\r\n";
				$j++;
			}

			if ( 0 === $j ) {
				echo '<label><input class="checkall_format" type="checkbox" name="format[]" value="' . esc_attr( $format ) . '"';
				if ( in_array( $format, $selected, true ) ) {
						echo ' checked="checked"';
				}
				echo ' /> ' . esc_html( ucwords( $format ) ) . '</label><br>' . "\r\n";
			} else {
				echo '<label><input class="checkall_format" type="checkbox" name="format[]" value="' . esc_attr( $format ) . '"';
				if ( in_array( $format, $selected, true ) ) {
							echo ' checked="checked"';
				}
				echo ' /> ' . esc_html( ucwords( $format ) ) . '</label><br>' . "\r\n";
			}
			$i++;
		}
		echo '</td></tr>' . "\r\n";
		echo '</table>' . "\r\n";
	}

	/**
	 * Display a drop-down form to select subscribers
	 * $selected is the option to select
	 * $submit is the text to use on the Submit button
	 */
	public function display_subscriber_dropdown( $selected = 'registered', $submit = '', $exclude = array() ) {
		global $wpdb, $current_tab;

		$who = array(
			'all'         => __( 'All Users and Subscribers', 'subscribe2' ),
			'public'      => __( 'Public Subscribers', 'subscribe2' ),
			'confirmed'   => ' &nbsp;&nbsp;' . __( 'Confirmed', 'subscribe2' ),
			'unconfirmed' => ' &nbsp;&nbsp;' . __( 'Unconfirmed', 'subscribe2' ),
			'all_users'   => __( 'All Registered Users', 'subscribe2' ),
			'registered'  => __( 'Registered Subscribers', 'subscribe2' ),
		);

		$all_cats = $this->all_cats( false );

		// count the number of subscribers
		$count['confirmed']   = $wpdb->get_var( "SELECT COUNT(id) FROM $wpdb->subscribe2 WHERE active='1'" );
		$count['unconfirmed'] = $wpdb->get_var( "SELECT COUNT(id) FROM $wpdb->subscribe2 WHERE active='0'" );
		if ( in_array( 'unconfirmed', $exclude, true ) ) {
			$count['public'] = $count['confirmed'];
		} elseif ( in_array( 'confirmed', $exclude, true ) ) {
			$count['public'] = $count['unconfirmed'];
		} else {
			$count['public'] = ( $count['confirmed'] + $count['unconfirmed'] );
		}
		if ( $this->s2_mu ) {
			$count['all_users'] = $wpdb->get_var( "SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key='" . $wpdb->prefix . "capabilities'" );
		} else {
			$count['all_users'] = $wpdb->get_var( "SELECT COUNT(ID) FROM $wpdb->users" );
		}
		if ( $this->s2_mu ) {
			$count['registered'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(b.meta_key) FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND b.meta_key=%s AND b.meta_value <> ''", $this->get_usermeta_keyname( 's2_subscribed' ) ) );
		} else {
			$count['registered'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(meta_key) FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value <> ''", $this->get_usermeta_keyname( 's2_subscribed' ) ) );
		}
		$count['all'] = ( $count['confirmed'] + $count['unconfirmed'] + $count['all_users'] );
		// get subscribers to individual categories but only if we are using per-post notifications
		if ( 'never' === $this->subscribe2_options['email_freq'] ) {
			$compulsory = explode( ',', $this->subscribe2_options['compulsory'] );
			if ( $this->s2_mu ) {
				foreach ( $all_cats as $cat ) {
					if ( in_array( (string) $cat->term_id, $compulsory, true ) ) {
						$count[ $cat->name ] = $count['all_users'];
					} else {
						$count[ $cat->name ] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(a.meta_key) FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b ON a.user_id = b.user_id WHERE a.meta_key='" . $wpdb->prefix . "capabilities' AND b.meta_key=%s", $this->get_usermeta_keyname( 's2_cat' ) . $cat->term_id ) );
					}
				}
			} else {
				foreach ( $all_cats as $cat ) {
					if ( in_array( (string) $cat->term_id, $compulsory, true ) ) {
						$count[ $cat->name ] = $count['all_users'];
					} else {
						$count[ $cat->name ] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(meta_value) FROM $wpdb->usermeta WHERE meta_key=%s", $this->get_usermeta_keyname( 's2_cat' ) . $cat->term_id ) );
					}
				}
			}
		}

		echo '<select name="what">' . "\r\n";
		foreach ( $who as $whom => $display ) {
			if ( in_array( $whom, $exclude, true ) ) {
				continue;
			}

			echo '<option value="' . esc_attr( $whom ) . '"';
			if ( $whom === $selected ) {
				echo ' selected="selected" ';
			}
			echo '>' . esc_html( $display ) . ' (' . esc_html( $count[ $whom ] ) . ')</option>' . "\r\n";
		}

		if ( 'public' !== $current_tab && $count['registered'] > 0 && 'never' === $this->subscribe2_options['email_freq'] ) {
			foreach ( $all_cats as $cat ) {
				if ( in_array( (string) $cat->term_id, $exclude, true ) ) {
					continue;
				}
				echo '<option value="' . esc_attr( $cat->term_id ) . '"';
				if ( $cat->term_id === $selected ) {
					echo ' selected="selected" ';
				}
				echo '> &nbsp;&nbsp;' . esc_html( $cat->name ) . '&nbsp;(' . esc_html( $count[ $cat->name ] ) . ') </option>' . "\r\n";
			}
		}

		echo '</select>';
		if ( false !== $submit ) {
			echo '&nbsp;<input type="submit" class="button-secondary" value="' . esc_attr( $submit ) . '" />' . "\r\n";
		}
	}

	/**
	 * Display a drop down list of administrator level users and
	 * optionally include a choice for Post Author
	 */
	public function admin_dropdown( $inc_author = false ) {
		global $wpdb;

		$args = array(
			'fields' => array(
				'ID',
				'display_name',
			),
			'role'   => 'administrator',
		);

		$wp_user_query = get_users( $args );
		if ( ! empty( $wp_user_query ) ) {
			foreach ( $wp_user_query as $user ) {
				$admins[] = $user;
			}
		} else {
			$admins = array();
		}

		if ( $inc_author ) {
			$author[] = (object) array(
				'ID'           => 'author',
				'display_name' => __( 'Post Author', 'subscribe2' ),
			);
			$author[] = (object) array(
				'ID'           => 'blogname',
				'display_name' => html_entity_decode( get_option( 'blogname' ), ENT_QUOTES ),
			);
			$admins   = array_merge( $author, $admins );
		}

		$option = '<select name="sender">' . "\r\n";
		foreach ( $admins as $admin ) {
			$option .= '<option value="' . $admin->ID . '"';
			if ( $admin->ID === $this->subscribe2_options['sender'] ) {
				$option .= ' selected="selected"';
			}
			$option .= '>' . $admin->display_name . '</option>' . "\r\n";
		}
		$option .= '</select>' . "\r\n";

		$allowed_tags = array(
			'select' => array(
				'name' => true,
			),
			'option' => array(
				'value'    => true,
				'selected' => true,
			),
		);

		echo wp_kses( $option, $allowed_tags );
	}

	/**
	 * Display a dropdown of choices for digest email frequency
	 * and give user details of timings when event is scheduled
	 */
	public function display_digest_choices() {
		global $wpdb;
		$cron_file = ABSPATH . 'wp-cron.php';
		if ( ! is_readable( $cron_file ) ) {
			echo '<strong><em style="color: red">' . esc_html__( 'The WordPress cron functions may be disabled on this server. Digest notifications may not work.', 'subscribe2' ) . '</em></strong><br>' . "\r\n";
		}
		$scheduled_time = wp_next_scheduled( 's2_digest_cron' );
		$offset         = get_option( 'gmt_offset' ) * 60 * 60;
		$schedule       = (array) wp_get_schedules();
		$schedule       = array_merge(
			array(
				'never' => array(
					'interval' => 0,
					'display'  => __( 'For each Post', 'subscribe2' ),
				),
			),
			$schedule
		);

		$sort = array();
		foreach ( (array) $schedule as $key => $value ) {
			$sort[ $key ] = $value['interval'];
		}
		asort( $sort );
		$schedule_sorted = array();
		foreach ( $sort as $key => $value ) {
			$schedule_sorted[ $key ] = $schedule[ $key ];
		}
		foreach ( $schedule_sorted as $key => $value ) {
			echo '<label><input type="radio" name="email_freq" value="' . esc_attr( $key ) . '"' . checked( $this->subscribe2_options['email_freq'], $key, false ) . ' />';
			echo ' ' . esc_html( $value['display'] ) . '</label><br>' . "\r\n";
		}
		if ( $scheduled_time ) {
			$date_format = get_option( 'date_format' );
			$time_format = get_option( 'time_format' );
			echo '<p>' . esc_html__( 'Current UTC time is', 'subscribe2' ) . ': ' . "\r\n";
			echo '<strong>' . esc_html( date_i18n( $date_format . ' @ ' . $time_format, false, 'gmt' ) ) . '</strong></p>' . "\r\n";
			echo '<p>' . esc_html__( 'Current blog time is', 'subscribe2' ) . ': ' . "\r\n";
			echo '<strong>' . esc_html( date_i18n( $date_format . ' @ ' . $time_format ) ) . '</strong></p>' . "\r\n";
			echo '<p>' . esc_html__( 'Next email notification will be sent when your blog time is after', 'subscribe2' ) . ': ' . "\r\n";
			echo '<input type="hidden" id="jscrondate" value="' . esc_attr( date_i18n( $date_format, $scheduled_time + $offset ) ) . '" />';
			echo '<input type="hidden" id="jscrontime" value="' . esc_attr( date_i18n( $time_format, $scheduled_time + $offset ) ) . '" />';
			echo '<span id="s2cron_1"><span id="s2crondate" style="background-color: #FFFBCC">' . esc_html( date_i18n( $date_format, $scheduled_time + $offset ) ) . '</span>';
			echo ' @ <span id="s2crontime" style="background-color: #FFFBCC">' . esc_html( date_i18n( $time_format, $scheduled_time + $offset ) ) . '</span> ';
			echo '<a href="#" onclick="s2Show(\'cron\'); return false;">' . esc_html__( 'Edit', 'subscribe2' ) . '</a></span>' . "\r\n";
			echo '<span id="s2cron_2">' . "\r\n";
			echo '<input id="s2datepicker" name="crondate" value="' . esc_attr( date_i18n( $date_format, $scheduled_time + $offset ) ) . '">' . "\r\n";
			$hours        = array( '12:00 am', '1:00 am', '2:00 am', '3:00 am', '4:00 am', '5:00 am', '6:00 am', '7:00 am', '8:00 am', '9:00 am', '10:00 am', '11:00 am', '12:00 pm', '1:00 pm', '2:00 pm', '3:00 pm', '4:00 pm', '5:00 pm', '6:00 pm', '7:00 pm', '8:00 pm', '9:00 pm', '10:00 pm', '11:00 pm' );
			$current_hour = intval( date_i18n( 'G', $scheduled_time + $offset ) );
			echo '<select name="crontime">' . "\r\n";
			foreach ( $hours as $key => $value ) {
				echo '<option value="' . esc_attr( $key ) . '"';
				if ( ! empty( $scheduled_time ) && $key === $current_hour ) {
					echo ' selected="selected"';
				}
				echo '>' . esc_html( $value ) . '</option>' . "\r\n";
			}
			echo '</select>' . "\r\n";
			echo '<a href="#" onclick="s2CronUpdate(\'cron\'); return false;">' . esc_html__( 'Update', 'subscribe2' ) . '</a>' . "\r\n";
			echo '<a href="#" onclick="s2CronRevert(\'cron\'); return false;">' . esc_html__( 'Revert', 'subscribe2' ) . '</a></span>' . "\r\n";
			if ( ! empty( $this->subscribe2_options['last_s2cron'] ) ) {
				echo '<p>' . esc_html__( 'Attempt to resend the last Digest Notification email', 'subscribe2' ) . ': ';
				echo '<input type="submit" class="button-secondary" name="resend" value="' . esc_attr( __( 'Resend Digest', 'subscribe2' ) ) . '" /></p>' . "\r\n";
			}
		} else {
			echo '<br>';
		}
	}

	/**
	 * Create and display a dropdown list of pages
	 */
	public function pages_dropdown( $s2page ) {
		$pages = get_pages();
		if ( empty( $pages ) ) {
			return;
		}

		$option  = '<select name="s2page">' . "\r\n";
		$option .= '<option value="0">' . __( 'Select a page', 'subscribe2' ) . '</option>' . "\r\n";
		foreach ( $pages as $page ) {
			$option .= '<option value="' . $page->ID . '"';
			if ( $page->ID === $s2page ) {
				$option .= ' selected="selected"';
			}
			$option .= '>';
			$parents = array_reverse( get_ancestors( $page->ID, 'page' ) );
			if ( $parents ) {
				foreach ( $parents as $parent ) {
					$option .= get_the_title( $parent ) . ' &raquo; ';
				}
			}
			$option .= $page->post_title . '</option>' . "\r\n";
		}
		$option .= '</select>' . "\r\n";

		$allowed_tags = array(
			'select' => array(
				'name' => true,
			),
			'option' => array(
				'value'    => true,
				'selected' => true,
			),
		);

		echo wp_kses( $option, $allowed_tags );
	}

	/**
	 * Subscribe all registered users to category selected on Admin Manage Page
	 */
	public function subscribe_registered_users( $emails = '', $cats = array() ) {
		if ( '' === $emails || '' === $cats ) {
			return false;
		}
		global $wpdb;

		$useremails = explode( ",\r\n", $emails );
		$useremails = implode( ', ', array_map( array( $this, 'prepare_in_data' ), $useremails ) );

		$user_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)" ); // phpcs:ignore WordPress.DB.PreparedSQL

		foreach ( $user_ids as $user_id ) {
			$old_cats = get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true );
			if ( ! empty( $old_cats ) ) {
				$old_cats = explode( ',', $old_cats );
				$newcats  = array_unique( array_merge( $cats, $old_cats ) );
			} else {
				$newcats = $cats;
			}
			if ( ! empty( $newcats ) && $newcats !== $old_cats ) {
				// add subscription to these cat IDs
				foreach ( $newcats as $id ) {
					update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $id, $id );
				}
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $newcats ) );
			}
			unset( $newcats );
		}
	}

	/**
	 * Unsubscribe all registered users to category selected on Admin Manage Page
	 */
	public function unsubscribe_registered_users( $emails = '', $cats = array() ) {
		if ( '' === $emails || '' === $cats ) {
			return false;
		}
		global $wpdb;

		$useremails = explode( ",\r\n", $emails );
		$useremails = implode( ', ', array_map( array( $this, 'prepare_in_data' ), $useremails ) );

		$user_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)" ); // phpcs:ignore WordPress.DB.PreparedSQL

		foreach ( $user_ids as $user_id ) {
			$old_cats = explode( ',', get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true ) );
			$remain   = array_diff( $old_cats, $cats );
			if ( ! empty( $remain ) && $remain !== $old_cats ) {
				// remove subscription to these cat IDs and update s2_subscribed
				foreach ( $cats as $id ) {
					delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $id );
				}
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $remain ) );
			} else {
				// remove subscription to these cat IDs and update s2_subscribed to ''
				foreach ( $cats as $id ) {
					delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $id );
				}
				delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ) );
			}
			unset( $remain );
		}
	}

	/**
	 * Handles bulk changes to email format for Registered Subscribers
	 */
	public function format_change( $emails, $format ) {
		if ( empty( $format ) ) {
			return;
		}

		global $wpdb;
		$useremails = explode( ",\r\n", $emails );
		$useremails = implode( ', ', array_map( array( $this, 'prepare_in_data' ), $useremails ) );
		$ids        = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)" ); // phpcs:ignore WordPress.DB.PreparedSQL
		$ids        = implode( ',', array_map( array( $this, 'prepare_in_data' ), $ids ) );
		$sql        = "UPDATE $wpdb->usermeta SET meta_value='{$format}' WHERE meta_key='" . $this->get_usermeta_keyname( 's2_format' ) . "' AND user_id IN ($ids)";
		$wpdb->query( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL
	}

	/**
	 * Handles bulk update to digest preferences
	 */
	public function digest_change( $emails, $digest ) {
		if ( empty( $digest ) ) {
			return;
		}

		global $wpdb;
		$useremails = explode( ",\r\n", $emails );
		$useremails = implode( ', ', array_map( array( $this, 'prepare_in_data' ), $useremails ) );

		$user_ids = $wpdb->get_col( "SELECT ID FROM $wpdb->users WHERE user_email IN ($useremails)" ); // phpcs:ignore WordPress.DB.PreparedSQL

		if ( 'digest' === $digest ) {
			$exclude = explode( ',', $this->subscribe2_options['exclude'] );
			if ( ! empty( $exclude ) ) {
				$all_cats = $this->all_cats( true, 'ID' );
			} else {
				$all_cats = $this->all_cats( false, 'ID' );
			}

			$cats_string = '';
			foreach ( $all_cats as $cat ) {
				( '' === $cats_string ) ? $cats_string = "$cat->term_id" : $cats_string .= ",$cat->term_id";
			}

			foreach ( $user_ids as $user_id ) {
				foreach ( $all_cats as $cat ) {
					update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $cat->term_id, $cat->term_id );
				}
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), $cats_string );
			}
		} elseif ( '-1' === $digest ) {
			foreach ( $user_ids as $user_id ) {
				$cats = explode( ',', get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true ) );
				foreach ( $cats as $id ) {
					delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $id );
				}
				delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ) );
			}
		}
	}

	/* ===== functions to handle addition and removal of WordPress categories ===== */
	/**
	 * Autosubscribe registered users to newly created categories
	 * if registered user has selected this option
	 */
	public function new_category( $new_category = '' ) {
		if ( 'no' === $this->subscribe2_options['show_autosub'] ) {
			return;
		}

		global $wpdb;
		if ( 'never' !== $this->subscribe2_options['email_freq'] ) {
			// if we are doing digests add new categories to users who are currently opted in
			$user_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key=%s AND meta_value<>''",
					$this->get_usermeta_keyname( 's2_subscribed' )
				)
			);
			foreach ( $user_ids as $user_id ) {
				$old_cats = get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true );
				$old_cats = explode( ',', $old_cats );
				$newcats  = array_merge( $old_cats, (array) $new_category );
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $new_category, $new_category );
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $newcats ) );
			}
			return;
		}

		if ( 'yes' === $this->subscribe2_options['show_autosub'] ) {
			if ( $this->s2_mu ) {
				$user_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b WHERE a.user_id = b.user_id AND a.meta_key=%s AND a.meta_value='yes' AND b.meta_key=%s",
						$this->get_usermeta_keyname( 's2_autosub' ),
						$this->get_usermeta_keyname( 's2_subscribed' )
					)
				);
			} else {
				$user_ids = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE $wpdb->usermeta.meta_key=%s AND $wpdb->usermeta.meta_value='yes'",
						$this->get_usermeta_keyname( 's2_autosub' )
					)
				);
			}
			if ( '' === $user_ids ) {
				return;
			}

			foreach ( $user_ids as $user_id ) {
				$old_cats = get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true );
				if ( empty( $old_cats ) ) {
					$newcats = (array) $new_category;
				} else {
					$old_cats = explode( ',', $old_cats );
					$newcats  = array_merge( $old_cats, (array) $new_category );
				}
				// add subscription to these cat IDs
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $new_category, $new_category );
				update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $newcats ) );
			}
		} elseif ( 'exclude' === $this->subscribe2_options['show_autosub'] ) {
			$excluded_cats                       = explode( ',', $this->subscribe2_options['exclude'] );
			$excluded_cats[]                     = $new_category;
			$this->subscribe2_options['exclude'] = implode( ',', $excluded_cats );
			update_option( 'subscribe2_options', $this->subscribe2_options );
		}
	}

	/**
	 * Automatically delete subscriptions to a category when it is deleted
	 */
	public function delete_category( $deleted_category = '' ) {
		global $wpdb;

		if ( $this->s2_mu ) {
			$user_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT a.user_id FROM $wpdb->usermeta AS a INNER JOIN $wpdb->usermeta AS b WHERE a.user_id = b.user_id AND a.meta_key=%s AND b.meta_key=%s",
					$this->get_usermeta_keyname( 's2_cat' ) . $deleted_category,
					$this->get_usermeta_keyname( 's2_subscribed' )
				)
			);
		} else {
			$user_ids = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT DISTINCT user_id FROM $wpdb->usermeta WHERE meta_key=%s",
					$this->get_usermeta_keyname( 's2_cat' ) . $deleted_category
				)
			);
		}
		if ( '' === $user_ids ) {
			return;
		}

		foreach ( $user_ids as $user_id ) {
			$old_cats = explode( ',', get_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), true ) );
			if ( ! is_array( $old_cats ) ) {
				$old_cats = array( $old_cats );
			}
			// add subscription to these cat IDs
			delete_user_meta( $user_id, $this->get_usermeta_keyname( 's2_cat' ) . $deleted_category );
			$remain = array_diff( $old_cats, (array) $deleted_category );
			update_user_meta( $user_id, $this->get_usermeta_keyname( 's2_subscribed' ), implode( ',', $remain ) );
		}
	}

	/* ===== functions to show & handle one-click subscription ===== */
	/**
	 * Show form for one-click subscription on user profile page
	 */
	public function one_click_profile_form( $user ) {
		echo '<h3>' . esc_html__( 'Email subscription', 'subscribe2' ) . '</h3>' . "\r\n";
		echo '<table class="form-table">' . "\r\n";
		echo '<tr><th scope="row">' . esc_html__( 'Subscribe / Unsubscribe', 'subscribe2' ) . '</th>' . "\r\n";
		echo '<td><label><input type="checkbox" name="sub2-one-click-subscribe" value="1" ' . checked( ! get_user_meta( $user->ID, $this->get_usermeta_keyname( 's2_subscribed' ), true ), false, false ) . ' /> ' . esc_html__( 'Receive notifications', 'subscribe2' ) . '</label><br>' . "\r\n";
		echo '<span class="description">' . esc_html__( 'Check if you want to receive email notification when new posts are published', 'subscribe2' ) . '</span>' . "\r\n";
		echo '</td></tr></table>' . "\r\n";
	}

	/**
	 * Handle submission from profile one-click subscription
	 */
	public function one_click_profile_form_save( $user_id ) {
		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return false;
		}

		if ( isset( $_POST['sub2-one-click-subscribe'] ) && 1 === $_POST['sub2-one-click-subscribe'] ) {
			// Subscribe
			$this->one_click_handler( $user_id, 'subscribe' );
		} else {
			// Unsubscribe
			$this->one_click_handler( $user_id, 'unsubscribe' );
		}
	}

	/**
	 * Core function to hook the digest email preview to the action on the Settings page
	 */
	public function digest_preview( $user_email = '' ) {
		if ( false === $this->validate_email( $user_email ) ) {
			return;
		}
		$this->subscribe2_cron( $user_email );
	}

	/**
	 * Core function to hook the resent digest email to the action on the Settings page
	 */
	public function digest_resend( $resend ) {
		if ( 'resend' === $resend ) {
			$this->subscribe2_cron( '', 'resend' );
		}
	}

	/**
	 * Uninstall hook
	 */
	public function s2_uninstall() {
		require_once S2PATH . 'classes/class-s2-uninstall.php';
		$s2_uninstall = new S2_Uninstall();
		$s2_uninstall->uninstall();
	}
}
