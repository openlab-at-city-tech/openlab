<?php

use InstagramFeed\Admin\SBI_Callout;
use InstagramFeed\Builder\SBI_Feed_Builder;

/**
 * Includes functions related to actions while in the admin area.
 *
 * - All AJAX related features
 * - Enqueueing of JS and CSS files
 * - Settings link on "Plugins" page
 * - Creation of local avatar image files
 * - Connecting accounts on the "Configure" tab
 * - Displaying admin notices
 * - Clearing caches
 * - License renewal
 */

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly.
}

function sb_instagram_menu()
{
	$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';
	$cap = apply_filters('sbi_settings_pages_capability', $cap);

	$notice_bubble = sb_menu_notice_bubble();
	add_menu_page(
		__('Instagram Feed', 'instagram-feed'),
		__('Instagram Feed', 'instagram-feed') . $notice_bubble,
		$cap,
		'sb-instagram-feed',
		'sb_instagram_settings_page'
	);

	add_submenu_page(
		'sb-instagram-feed',
		__('Upgrade to Pro', 'instagram-feed'),
		'<span class="sbi_get_pro">' . __('Upgrade to Pro', 'instagram-feed') . '</span>',
		$cap,
		'https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=menu-link&utm_medium=upgrade-link&utm_content=UpgradeToPro',
		''
	);

	if (version_compare(PHP_VERSION, '7.4.0') >= 0 && !is_plugin_active('feeds-for-tiktok/feeds-for-tiktok.php') && !is_plugin_active('tiktok-feeds-pro/tiktok-feeds-pro.php')) {
		add_submenu_page(
			'sb-instagram-feed',
			__('TikTok Feeds', 'instagram-feed'),
			'<span class="sbi_get_tiktok">' . __('TikTok Feeds', 'instagram-feed') . '</span>' . '<span class="sbi-notice-alert sbi-new-indicator"><span>New!</span></span>',
			$cap,
			'admin.php?page=sbtt',
			''
		);
	}

	if (version_compare(PHP_VERSION, '7.1.0') >= 0 && !is_plugin_active('reviews-feed/sb-reviews.php') && !is_plugin_active('reviews-feed-pro/sb-reviews-pro.php')) {
		add_submenu_page(
			'sb-instagram-feed',
			__('Reviews Feed', 'instagram-feed'),
			'<span class="sbi_get_sbr">' . __('Reviews Feed', 'instagram-feed') . '</span>',
			$cap,
			'admin.php?page=sbr',
			''
		);
	}

	// Show a Instagram plugin menu item if it isn't already installed
	if (!is_plugin_active('custom-facebook-feed/custom-facebook-feed.php') && !is_plugin_active('custom-facebook-feed-pro/custom-facebook-feed.php') && current_user_can('activate_plugins') && current_user_can('install_plugins')) {
		add_submenu_page(
			'sb-instagram-feed',
			__('Facebook Feed', 'instagram-feed'),
			'<span class="sbi_get_cff">' . __('Facebook Feed', 'instagram-feed') . '</span>',
			$cap,
			'admin.php?page=cff-builder',
			''
		);
	}

	// Show a Twitter plugin menu item if it isn't already installed
	if (!is_plugin_active('custom-twitter-feeds/custom-twitter-feed.php') && !is_plugin_active('custom-twitter-feeds-pro/custom-twitter-feed.php') && current_user_can('activate_plugins') && current_user_can('install_plugins')) {
		add_submenu_page(
			'sb-instagram-feed',
			__('Twitter Feed', 'instagram-feed'),
			'<span class="sbi_get_ctf">' . __('Twitter Feed', 'instagram-feed') . '</span>',
			$cap,
			'admin.php?page=sb-instagram-feed&tab=more',
			''
		);
	}

	// Show a YouTube plugin menu item if it isn't already installed
	if (!is_plugin_active('feeds-for-youtube/youtube-feed.php') && !is_plugin_active('youtube-feed-pro/youtube-feed.php') && current_user_can('activate_plugins') && current_user_can('install_plugins')) {
		add_submenu_page(
			'sb-instagram-feed',
			__('YouTube Feed', 'instagram-feed'),
			'<span class="sbi_get_yt">' . __('YouTube Feed', 'instagram-feed') . '</span>',
			$cap,
			'admin.php?page=sb-instagram-feed&tab=more',
			''
		);
	}
}

add_action('admin_menu', 'sb_instagram_menu');

function sb_menu_notice_bubble()
{
	global $sb_instagram_posts_manager;
	if ($sb_instagram_posts_manager->are_critical_errors()) {
		return ' <span class="update-plugins sbi-error-alert sbi-notice-alert"><span>!</span></span>';
	}

	$notice = '';
	$notifications = false;
	if (class_exists('\SBI_Notifications')) {
		$sbi_notifications = new SBI_Notifications();
		$notifications = $sbi_notifications->get();

		if (!empty($notifications) && is_array($notifications)) {
			$notifications = count($notifications);
		}
	}

	global $sbi_notices;
	$api_notice = $sbi_notices->get_notice('personal_api_deprecation');
	if (!empty($api_notice)) {
		$notifications = $notifications && $notifications > 0 ? $notifications + 1 : 1;
	}

	$callout = SBI_Callout::print_callout_ob_html('side-menu');
	$print_callout = $callout !== false ? $callout : '';

	if ($notifications) {
		$notice = ' <span class="sbi-notice-alert"><span>' . absint($notifications) . '</span></span>';
	}

	return $notice . $print_callout;
}

function sbi_add_settings_link($links)
{
	$pro_link = '<a href="https://smashballoon.com/instagram-feed/?utm_campaign=instagram-free&utm_source=plugins-page&utm_medium=upgrade-link&utm_content=UpgradeToPro" target="_blank" style="font-weight: bold; color: #1da867;">' . __('Upgrade to Pro', 'instagram-feed') . '</a>';

	$sbi_settings_link = '<a href="' . esc_url(admin_url('admin.php?page=sbi-settings')) . '">' . esc_html__('Settings', 'instagram-feed') . '</a>';
	array_unshift($links, $pro_link, $sbi_settings_link);

	return $links;
}

add_filter("plugin_action_links_instagram-feed/instagram-feed.php", 'sbi_add_settings_link', 10, 2);

function sb_instagram_admin_style()
{
	wp_register_style('sb_instagram_admin_css', SBI_PLUGIN_URL . 'css/sb-instagram-admin.css', array(), SBIVER);
	wp_enqueue_style('sb_instagram_admin_css');
	wp_enqueue_style('wp-color-picker');
}

add_action('admin_enqueue_scripts', 'sb_instagram_admin_style');

function sb_instagram_admin_scripts()
{
	wp_enqueue_script('sb_instagram_admin_js', SBI_PLUGIN_URL . 'js/sb-instagram-admin-6.js', array(), SBIVER, true);
	wp_localize_script(
		'sb_instagram_admin_js',
		'sbiA',
		array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'sbi_nonce' => wp_create_nonce('sbi_nonce'),
		)
	);
	$strings = array(
		'addon_activate' => esc_html__('Activate', 'instagram-feed'),
		'addon_activated' => esc_html__('Activated', 'instagram-feed'),
		'addon_active' => esc_html__('Active', 'instagram-feed'),
		'addon_deactivate' => esc_html__('Deactivate', 'instagram-feed'),
		'addon_inactive' => esc_html__('Inactive', 'instagram-feed'),
		'addon_install' => esc_html__('Install Addon', 'instagram-feed'),
		'addon_error' => esc_html__('Could not install addon. Please download from wpforms.com and install manually.', 'instagram-feed'),
		'plugin_error' => esc_html__('Could not install a plugin. Please download from WordPress.org and install manually.', 'instagram-feed'),
		'addon_search' => esc_html__('Searching Addons', 'instagram-feed'),
		'ajax_url' => admin_url('admin-ajax.php'),
		'cancel' => esc_html__('Cancel', 'instagram-feed'),
		'close' => esc_html__('Close', 'instagram-feed'),
		'nonce' => wp_create_nonce('sbi-admin'),
		'almost_done' => esc_html__('Almost Done', 'instagram-feed'),
		'oops' => esc_html__('Oops!', 'instagram-feed'),
		'ok' => esc_html__('OK', 'instagram-feed'),
		'plugin_install_activate_btn' => esc_html__('Install and Activate', 'instagram-feed'),
		'plugin_install_activate_confirm' => esc_html__('needs to be installed and activated to import its forms. Would you like us to install and activate it for you?', 'instagram-feed'),
		'plugin_activate_btn' => esc_html__('Activate', 'instagram-feed'),
		'oembed_connectionURL' => sbi_get_oembed_connection_url(),
		'smashPlugins'	=> SBI_Feed_Builder::get_smashballoon_plugins_info()
	);
	$strings = apply_filters('sbi_admin_strings', $strings);
	wp_localize_script(
		'sb_instagram_admin_js',
		'sbi_admin',
		$strings
	);
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-draggable');
	wp_enqueue_script('wp-color-picker');
}

add_action('admin_enqueue_scripts', 'sb_instagram_admin_scripts');

function sbi_get_oembed_connection_url()
{
	$admin_url_state = admin_url('admin.php?page=sbi-oembeds-manager');
	$nonce = wp_create_nonce('sbi_con');
	// If the admin_url isn't returned correctly then use a fallback
	if ($admin_url_state == '/wp-admin/admin.php?page=sbi-oembeds-manager') {
		$admin_url_state = "https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	}

	return array(
		'connect' => SBI_OEMBED_CONNECT_URL,
		'sbi_con' => $nonce,
		'stateURL' => $admin_url_state
	);
}

function sbi_formatted_error($response)
{
	if (isset($response['error'])) {
		$response['error']['message'] = str_replace('Please read the Graph API documentation at https://developers.facebook.com/docs/graph-api', '', $response['error']['message']);
		$error = '<span>' . sprintf(__('API error %s:', 'instagram-feed'), esc_html($response['error']['code'])) . ' ' . esc_html($response['error']['message']) . '</span>';
		$error .= '<div class="license-action-btns"><p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __('Directions on how to resolve this issue', 'instagram-feed') . '</a></p></div>';

		return $error;
	} else {
		$message = '<span>' . sprintf(__('Error connecting to %s.', 'instagram-feed'), $response['url']) . '</span>';
		if (isset($response['response']) && isset($response['response']->errors)) {
			foreach ($response['response']->errors as $key => $item) {
				$message .= '<span>' . esc_html($key) . ' - ' . esc_html($item[0]) . '</span>';
			}
		}
		$message .= '<div class="license-action-btns"><p class="sbi-error-directions"><a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __('Directions on how to resolve this issue', 'instagram-feed') . '</a></p></div>';

		return $message;
	}
}

function sbi_connect_new_account($access_token, $account_id)
{
	$split_id = explode(' ', trim($account_id));
	$account_id = preg_replace('/[^A-Za-z0-9 ]/', '', $split_id[0]);
	if (!empty($account_id)) {
		$split_token = explode(' ', trim($access_token));
		$access_token = preg_replace('/[^A-Za-z0-9 ]/', '', $split_token[0]);
	}

	$account = array(
		'access_token' => $access_token,
		'user_id' => $account_id,
		'type' => 'business',
	);

	if (sbi_code_check($access_token)) {
		$account['type'] = 'basic';
	}

	$connector = new SBI_Account_Connector();

	$response = $connector->fetch($account);

	if (isset($response['access_token'])) {
		$connector->add_account_data($response);
		$connector->update_stored_account();
		$connector->after_update();
		return $connector->get_account_data();
	} else {
		return $response;
	}
}

add_action('admin_init', 'sbi_admin_error_notices');

function sbi_admin_error_notices()
{
	global $sb_instagram_posts_manager;

	if (isset($_GET['page']) && in_array($_GET['page'], array('sbi-settings'), true)) {
		$errors = $sb_instagram_posts_manager->get_errors();

		if (!empty($errors)) {
			if (!empty($errors['database_create']) || !empty($errors['upload_dir'])) {
				$type = !empty($errors['database_create']) ? 'database_create' : 'upload_dir';
				$title = !empty($errors['database_create']) ? __('Instagram Feed was unable to create new database tables.', 'instagram-feed') : '';
				$message = !empty($errors['database_create']) ? $errors['database_create'] : $errors['upload_dir'];

				$buttons = array(
					array(
						'text' => __('Visit our FAQ page for help', 'instagram-feed'),
						'url' => 'https://smashballoon.com/docs/instagram/',
						'class' => 'sbi-license-btn sbi-btn-blue sbi-notice-btn',
						'target' => '_blank',
						'tag' => 'a',
					)
				);

				if (!empty($errors['database_create'])) {
					$buttons[] = array(
						'text' => __('Try creating database tables again', 'instagram-feed'),
						'class' => 'sbi-retry-db sbi-space-left sbi-btn sbi-notice-btn sbi-btn-grey',
						'tag' => 'button',
					);
				}

				addErrorNotice(
					$type,
					$title,
					$message,
					$buttons
				);
			}

			if (!empty($errors['unused_feed'])) {
				addErrorNotice(
					'unused_feed',
					__('Action Required Within 7 Days:', 'instagram-feed'),
					$errors['unused_feed'] . '<br>' . __('Or you can simply press the "Fix Usage" button to fix this issue.', 'instagram-feed'),
					array(
						array(
							'text' => __('Fix Usage', 'instagram-feed'),
							'class' => 'sbi-reset-unused-feed-usage sbi-space-left sbi-btn sbi-notice-btn sbi-btn-blue',
							'tag' => 'button',
						),
					)
				);
			}

			if (!empty($errors['platform_data_deleted'])) {
				addErrorNotice(
					'platform_data_deleted',
					__('All Instagram Data has Been Removed:', 'instagram-feed'),
					$errors['platform_data_deleted'] . '<br>' . __('To fix your feeds, reconnect all accounts that were in use on the Settings page.', 'instagram-feed')
				);
			}

			if (!empty($errors['database_error'])) {
				addErrorNotice(
					'database_error',
					__('Action Required: Unable to save or update feed sources', 'instagram-feed'),
					$errors['database_error'] . '<br>' . __('Please ensure that all database tables are created and the user has the following permissions: SELECT, INSERT, UPDATE, DELETE, ALTER (for updates), CREATE TABLE, DROP TABLE, and INDEX.', 'instagram-feed'),
					array(
						array(
							'text' => __('Visit our FAQ page for help', 'instagram-feed'),
							'url' => 'https://smashballoon.com/doc/instagram-api-error-message-reference/',
							'class' => 'sbi-license-btn sbi-btn-blue sbi-notice-btn',
							'target' => '_blank',
							'tag' => 'a',
						),
						array(
							'text' => __('Try creating database tables again', 'instagram-feed'),
							'class' => 'sbi-retry-db sbi-space-left sbi-btn sbi-notice-btn sbi-btn-grey',
							'tag' => 'button',
						)
					)
				);
			}
		}

		$critical_errors = $sb_instagram_posts_manager->get_critical_errors();
		if ($sb_instagram_posts_manager->are_critical_errors()) {
			addErrorNotice(
				'critical_error',
				__('Instagram Feed is encountering an error and your feeds may not be updating due to the following reasons:', 'instagram-feed'),
				$critical_errors
			);
		}
	}
}

/**
 * Adds an error notice to the admin panel.
 *
 * @param string $id The unique identifier for the error notice.
 * @param string $title The title of the error notice.
 * @param string $message The message content of the error notice.
 * @param array  $buttons Optional. An array of buttons to display with the error notice. Default is an empty array.
 */
function addErrorNotice($id, $title, $message, $buttons = array())
{
	global $sbi_notices;

	$error_args = array(
		'class' => 'sbi-admin-notices sbi-critical-error-notice',
		'title' => array(
			'text' => $title,
			'class' => 'sb-notice-title',
			'tag' => 'h3',
		),
		'message' => '<p>' . $message . '</p><br>',
		'dismissible' => false,
		'priority' => 1,
		'page' => 'sbi-settings',
		'buttons' => $buttons,
		'buttons_wrap_start' => '<p class="sbi-error-directions">',
		'buttons_wrap_end' => '</p>',
		'icon' => array(
			'src' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-error.svg',
			'wrap' => '<span class="sb-notice-icon sb-error-icon"><img {src}></span>',
		),
		'wrap_schema' => '<div {id} {class}>{icon}<div class="sbi-notice-body">{title}{message}{buttons}</div></div>',
	);

	$sbi_notices->add_notice($id, 'error', $error_args);
}

function sbi_reset_log()
{
	check_ajax_referer('sbi_nonce', 'sbi_nonce');

	if (!sbi_current_user_can('manage_instagram_feed_options')) {
		wp_send_json_error();
	}

	global $sb_instagram_posts_manager;

	$sb_instagram_posts_manager->remove_all_errors();

	global $sbi_notices;
	$sbi_notices->remove_notice('critical_error');

	sbi_clear_caches();
	wp_send_json_success('1');
}

add_action('wp_ajax_sbi_reset_log', 'sbi_reset_log');

function sb_instagram_settings_page()
{
	$link = admin_url('admin.php?page=sbi-settings');
	?>
	<div id="sbi_admin">
		<div class="sbi_notice">
			<strong><?php esc_html_e('The Instagram Feed Settings page has moved!', 'instagram-feed'); ?></strong>
			<a href="<?php echo esc_url($link); ?>"><?php esc_html_e('Click here to go to the new page.', 'instagram-feed'); ?></a>
		</div>
	</div>
	<?php
}
