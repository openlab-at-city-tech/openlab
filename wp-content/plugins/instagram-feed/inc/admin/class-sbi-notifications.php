<?php

/**
 * SBI_Notifications.
 *
 * @since 2.6/5.9
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class SBI_Notifications
{
	/**
	 * Source of notifications content.
	 *
	 * @var string
	 */
	const SOURCE_URL = 'https://plugin.smashballoon.com/notifications.json';

	/**
	 * @var string
	 */
	const OPTION_NAME = 'sbi_notifications';

	/**
	 * JSON data contains notices for all plugins. This is used
	 * to select messages only meant for this plugin
	 *
	 * @var string
	 */
	const PLUGIN = 'instagram';

	/**
	 * Option value.
	 *
	 * @since 2.6/5.9
	 *
	 * @var bool|array
	 */
	public $option = false;

	/**
	 * Initialize class.
	 *
	 * @since 2.6/5.9
	 */
	public function init()
	{
		$this->hooks();
	}

	/**
	 * Register hooks.
	 *
	 * @since 2.6/5.9
	 */
	public function hooks()
	{
		add_action('admin_enqueue_scripts', array($this, 'enqueues'));

		add_action('admin_init', array($this, 'output'));

		// on cron. Once a week?
		add_action('sbi_notification_update', array($this, 'update'));

		add_action('wp_ajax_sbi_dashboard_notification_dismiss', array($this, 'dismiss'));
	}

	/**
	 * Get notification count.
	 *
	 * @return int
	 * @since 2.6/5.9
	 */
	public function get_count()
	{
		return count($this->get());
	}

	/**
	 * Get notification data.
	 *
	 * @return array
	 * @since 2.6/5.9
	 */
	public function get()
	{
		if (!$this->has_access()) {
			return array();
		}

		$option = $this->get_option();

		// Update notifications using async task.
		if (empty($option['update']) || sbi_get_current_time() > $option['update'] + DAY_IN_SECONDS) {
			$this->update();
		}

		$events = !empty($option['events']) ? $this->verify_active($option['events']) : array();
		$feed = !empty($option['feed']) ? $this->verify_active($option['feed']) : array();

		// If there is a new user notification, add it to the beginning of the notification list
		$sbi_newuser = new SBI_New_User();
		$newuser_notifications = $sbi_newuser->get();

		if (!empty($newuser_notifications)) {
			$events = array_merge($newuser_notifications, $events);
		}

		return array_merge($events, $feed);
	}

	/**
	 * Check if user has access and is enabled.
	 *
	 * @return bool
	 * @since 2.6/5.9
	 */
	public function has_access()
	{
		$access = false;

		if (current_user_can('manage_instagram_feed_options')) {
			$access = true;
		}

		return apply_filters('sbi_admin_notifications_has_access', $access);
	}

	/**
	 * Update notification data from feed.
	 *
	 * @since 2.6/5.9
	 */
	public function update()
	{
		$feed = $this->fetch_feed();
		$option = $this->get_option();

		update_option(
			'sbi_notifications',
			array(
				'update' => sbi_get_current_time(),
				'feed' => $feed,
				'events' => $option['events'],
				'dismissed' => $option['dismissed'],
			)
		);
	}

	/**
	 * Fetch notifications from feed.
	 *
	 * @return array
	 * @since 2.6/5.9
	 */
	public function fetch_feed()
	{
		$res = wp_safe_remote_get($this->source_url());

		if (is_wp_error($res)) {
			return array();
		}

		$body = wp_remote_retrieve_body($res);

		if (empty($body)) {
			return array();
		}

		return $this->verify(json_decode($body, true));
	}

	/**
	 * Use this function to get the source URL to allow
	 * inheritance for the New_User class
	 *
	 * @return string
	 */
	public function source_url()
	{
		return self::SOURCE_URL;
	}

	/**
	 * Verify notification data before it is saved.
	 *
	 * @param array $notifications Array of notifications items to verify.
	 *
	 * @return array
	 * @since 2.6/5.9
	 */
	public function verify($notifications)  // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh
	{
		$data = array();

		if (!is_array($notifications) || empty($notifications)) {
			return $data;
		}

		$option = $this->get_option();

		foreach ($notifications as $notification) {
			// Ignore if not a targeted plugin
			if (!empty($notification['plugin']) && is_array($notification['plugin']) && !in_array(self::PLUGIN, $notification['plugin'], true)) {
				continue;
			}

			// Ignore if max wp version detected
			if (!empty($notification['maxwpver']) && version_compare(get_bloginfo('version'), $notification['maxwpver'], '>')) {
				continue;
			}

			// Ignore if max version has been reached
			if (!empty($notification['maxver']) && version_compare($notification['maxver'], SBIVER) < 0) {
				continue;
			}

			// Ignore if min version has not been reached
			if (!empty($notification['minver']) && version_compare($notification['minver'], SBIVER) > 0) {
				continue;
			}


			// Ignore if PHP version requirement not met
			if (!empty($notification['minphpver']) && version_compare(PHP_VERSION, $notification['minphpver'], '<')) {
				continue;
			}

			// Ignore if PHP version is too high
			if (!empty($notification['maxphpver']) && version_compare(PHP_VERSION, $notification['maxphpver'], '>')) {
				continue;
			}

			// Ignore if a specific sbi_status is empty or false
			if (!empty($notification['statuscheck'])) {
				$status_key = sanitize_key($notification['statuscheck']);
				$sbi_statuses_option = get_option('sbi_statuses', array());

				if (empty($sbi_statuses_option[$status_key])) {
					continue;
				}
			}

			// The message and license should never be empty, if they are, ignore.
			if (empty($notification['content']) || empty($notification['type'])) {
				continue;
			}

			// Ignore if license type does not match.
			$license = sbi_is_pro_version() ? 'pro' : 'free';

			if (!in_array($license, $notification['type'], true)) {
				continue;
			}

			// Ignore if expired.
			if (!empty($notification['end']) && sbi_get_current_time() > strtotime($notification['end'])) {
				continue;
			}

			// Ignore if notification has already been dismissed.
			if (!empty($option['dismissed']) && in_array($notification['id'], $option['dismissed'])) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				continue;
			}

			// TODO: Ignore if notification existed before installing SBI.
			// Prevents bombarding the user with notifications after activation.
			$activated = false;
			if (
				!empty($activated)
				&& !empty($notification['start'])
				&& $activated > strtotime($notification['start'])
			) {
				continue;
			}

			$data[] = $notification;
		}

		return $data;
	}

	/**
	 * Get option value.
	 *
	 * @param bool $cache Reference property cache if available.
	 *
	 * @return array
	 * @since 2.6/5.9
	 */
	public function get_option($cache = true)
	{
		if ($this->option && $cache) {
			return $this->option;
		}

		$option = get_option($this->option_name(), array());

		$this->option = array(
			'update' => !empty($option['update']) ? $option['update'] : 0,
			'events' => !empty($option['events']) ? $option['events'] : array(),
			'feed' => !empty($option['feed']) ? $option['feed'] : array(),
			'dismissed' => !empty($option['dismissed']) ? $option['dismissed'] : array(),
		);

		return $this->option;
	}

	/**
	 * Use this function to get the option name to allow
	 * inheritance for the New_User class
	 *
	 * @return string
	 */
	public function option_name()
	{
		return self::OPTION_NAME;
	}

	/**
	 * Verify saved notification data for active notifications.
	 *
	 * @param array $notifications Array of notifications items to verify.
	 *
	 * @return array
	 * @since 2.6/5.9
	 */
	public function verify_active($notifications)
	{
		if (!is_array($notifications) || empty($notifications)) {
			return array();
		}

		// Remove notfications that are not active.
		foreach ($notifications as $key => $notification) {
			if (
				(!empty($notification['start']) && sbi_get_current_time() < strtotime($notification['start']))
				|| (!empty($notification['end']) && sbi_get_current_time() > strtotime($notification['end']))
			) {
				unset($notifications[$key]);
			}

			if (empty($notification['recent_install_override']) && $this->recently_installed()) {
				unset($notifications[$key]);
			}

			// Ignore if max version has been reached
			if (!empty($notification['maxver']) && version_compare($notification['maxver'], SBIVER) < 0) {
				unset($notifications[$key]);
			}

			// Ignore if max wp version detected
			if (!empty($notification['maxwpver']) && version_compare(get_bloginfo('version'), $notification['maxwpver'], '>')) {
				unset($notifications[$key]);
			}

			// Ignore if min version has not been reached
			if (!empty($notification['minver']) && version_compare($notification['minver'], SBIVER) > 0) {
				unset($notifications[$key]);
			}

			// Ignore if a specific sbi_status is empty or false
			if (!empty($notification['statuscheck'])) {
				$status_key = sanitize_key($notification['statuscheck']);
				$sbi_statuses_option = get_option('sbi_statuses', array());

				if (empty($sbi_statuses_option[$status_key])) {
					unset($notifications[$key]);
				}
			}
		}

		return $notifications;
	}

	/**
	 * @return bool
	 *
	 * @since 1.4.5/1.4.2
	 */
	public function recently_installed()
	{
		$sbi_statuses_option = get_option('sbi_statuses', array());

		if (!isset($sbi_statuses_option['first_install'])) {
			return false;
		}

		// Plugin was installed less than a week ago
		if ((int)$sbi_statuses_option['first_install'] > time() - WEEK_IN_SECONDS) {
			return true;
		}

		return false;
	}

	/**
	 * Add a manual notification event.
	 *
	 * @param array $notification Notification data.
	 * @since 2.6/5.9
	 */
	public function add($notification)
	{
		if (empty($notification['id'])) {
			return;
		}

		$option = $this->get_option();

		if (in_array($notification['id'], $option['dismissed'])) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
			return;
		}

		foreach ($option['events'] as $item) {
			if ($item['id'] === $notification['id']) {
				return;
			}
		}

		$notification = $this->verify(array($notification));

		update_option(
			'sbi_notifications',
			array(
				'update' => $option['update'],
				'feed' => $option['feed'],
				'events' => array_merge($notification, $option['events']),
				'dismissed' => $option['dismissed'],
			)
		);
	}

	/**
	 * Admin area Form Overview enqueues.
	 *
	 * @since 2.6/5.9
	 */
	public function enqueues()
	{
		if (!$this->has_access()) {
			return;
		}

		$notifications = $this->get();

		if (empty($notifications)) {
			return;
		}

		$min = '';

		wp_enqueue_style(
			'sbi-admin-notifications',
			SBI_PLUGIN_URL . "css/admin-notifications{$min}.css",
			array(),
			SBIVER
		);

		wp_enqueue_script(
			'sbi-admin-notifications',
			SBI_PLUGIN_URL . "js/admin-notifications{$min}.js",
			array('jquery'),
			SBIVER,
			true
		);
	}

	/**
	 * Output notifications on Instagram Feed admin area.
	 *
	 * @since 2.6/5.9
	 */
	public function output()
	{
		if (isset($_GET['feed_id'])) {
			return;
		}

		$notifications = $this->get();

		if (empty($notifications)) {
			return;
		}

		$content_allowed_tags = array(
			'em' => array(),
			'strong' => array(),
			'span' => array(
				'style' => array(),
			),
			'a' => array(
				'href' => array(),
				'target' => array(),
				'rel' => array(),
			),
		);

		global $sbi_notices;
		$notice_args = array(
			'wrap_class' => 'sbi-notifications-wrap',
			'wrap_id' => 'sbi-notifications',
			'page' => array(
				'sbi-feed-builder',
				'sbi-settings',
				'sbi-oembeds-manager',
				'sbi-extensions-manager',
				'sbi-about-us',
				'sbi-support',
			),
			'capability' => array('manage_instagram_feed_options', 'manage_options'),
			'dismissible' => true,
			'dismiss' => array(
				'class' => 'dismiss',
				'title' => __('Dismiss this message', 'instagram-feed'),
				'icon' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-dismiss-icon.svg',
				'tag' => 'a',
				'href' => '',
			),
			'nav' => true,
			'navigation' => array(
				'class' => 'navigation',
				'tag' => 'div',
				'items' => array(
					'prev' => array(
						'class' => 'prev disabled',
						'title' => __('Previous message', 'instagram-feed'),
						'icon' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-carousel-prev.svg',
						'tag' => 'a',
						'attr' => '',
					),
					'next' => array(
						'class' => 'next disabled',
						'title' => __('Next message', 'instagram-feed'),
						'icon' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-carousel-next.svg',
						'tag' => 'a',
						'attr' => '',
					),
				),
			),
			'wrap_schema' => '<div {wrap_id} {wrap_class}>{dismiss}{navigation}<div class="messages"><div {class} {data}>{image}{title}<p class="content">{message}</p>{buttons}</div></div></div>',
		);

		foreach ($notifications as $notification) {
			$type = $notification['id'];
			$buttons = array();

			if (!empty($notification['btns']) && is_array($notification['btns'])) {
				foreach ($notification['btns'] as $btn_type => $btn) {
					if ($type == 'review' || $type == 'discount') {
						$class = $btn_type === 'primary' ? 'sbi-btn-blue' : 'sbi-btn-grey';
					} else {
						$class = $btn_type === 'primary' ? 'sbi-btn-green' : 'sbi-btn-grey';
					}
					$class .= isset($btn['class']) ? ' ' . $btn['class'] : '';
					if (is_array($btn['url'])) {
						$btn['url'] = array(
							'args' => $btn['url'],
							'action' => 'sbi-' . $type
						);
					} elseif (!is_array($btn['url'])) {
						$btn['url'] = $this->replace_merge_fields($btn['url'], $notification);
					}
					if (!empty($btn['attr'])) {
						$btn['target'] = '_blank';
					}
					$buttons[] = array(
						'class' => 'sbi-btn ' . $class,
						'url' => !empty($btn['url']) ? $btn['url'] : '',
						'target' => !empty($btn['target']) && $btn['target'] === '_blank' ? '_blank' : '',
						'rel' => !empty($btn['target']) && $btn['target'] === '_blank' ? 'noopener' : '',
						'text' => !empty($btn['text']) ? sanitize_text_field($btn['text']) : '',
						'tag' => !empty($btn['tag']) ? $btn['tag'] : 'a',
					);
				}
			}

			if (empty($notification['image'])) {
				$image = array(
					'src' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-bell.svg',
					'alt' => 'notice',
					'wrap' => '<div class="bell"><img {src} {alt}></div>',
				);
			} else {
				if ($notification['image'] === 'balloon') {
					$image = array(
						'src' => SBI_PLUGIN_URL . 'admin/assets/img/balloon.svg',
						'alt' => 'notice',
						'wrap' => '<div class="bell"><img {src} {alt}></div>',
					);
				} elseif ($notification['id'] === 'review' || $notification['id'] === 'discount') {
					$image = array(
						'src' => SBI_PLUGIN_URL . 'admin/assets/img/' . sanitize_text_field($notification['image']),
						'alt' => 'notice',
						'wrap' => '<div class="bell"><img {src} {alt}></div>',
					);
				} else {
					$image = array(
						'src' => SBI_PLUGIN_URL . 'admin/assets/img/' . sanitize_text_field($notification['image']),
						'alt' => 'notice',
						'overlay' => isset($notification['image_overlay']) ? str_replace('%', '%%', $notification['image_overlay']) : '',
						'overlay_wrap' => '<div class="overlay">{overlay}</div>',
						'wrap' => '<div class="thumb"><img {src} {alt}>{overlay}</div>',
					);
				}
			}

			switch ($type) {
				case 'review':
					$sbi_open_feedback_url = 'https://smashballoon.com/feedback/?plugin=instagram-lite';
					$review_consent = get_option('sbi_review_consent');
					if (!$review_consent) {
						$error_args = array(
							'wrap_class' => 'sbi-notifications-wrap sbi_review_notice',
							'class' => 'sbi_review_step1_notice message',
							'data' => array(
								'message-id' => !empty($notification['id']) ? esc_attr(sanitize_text_field($notification['id'])) : 0,
							),
							'title' => array(
								'text' => __('Are you enjoying the Instagram Feed Plugin?', 'instagram-feed'),
								'class' => 'title',
							),
							'image' => array(
								'src' => SBI_PLUGIN_URL . 'admin/assets/img/' . sanitize_text_field($notification['image']),
								'alt' => 'notice',
								'wrap' => '<div class="bell"><img {src} {alt}></div>',
							),
							'buttons' => array(
								array(
									'text' => __('Yes', 'instagram-feed'),
									'class' => 'sbi-btn-link',
									'id' => 'sbi_review_consent_yes',
									'tag' => 'button',
								),
								array(
									'text' => __('No', 'instagram-feed'),
									'class' => 'sbi-btn-link',
									'id' => 'sbi_review_consent_no',
									'target' => '_blank',
									'url' => $sbi_open_feedback_url,
									'tag' => 'a',
								),
							),
							'buttons_wrap_start' => '<div class="review-step-1-btns">',
							'buttons_wrap_end' => '</div>',
							'priority' => 50,
							'wrap_schema' => '<div {wrap_id} {wrap_class}>{dismiss}{navigation}<div class="messages"><div {class} {data}>{image}{title}{buttons}</div></div></div>',
						);
						$error_args = wp_parse_args($error_args, $notice_args);
						$error_args['dismiss']['href'] = array(
							'args' => array(
								'sbi_dismiss' => $type
							),
							'action' => 'sbi-' . $type
						);
						$sbi_notices->add_notice('review_step_1', 'information', $error_args, 'marketing');
					}
					$error_args = array(
						'wrap_class' => 'sbi-notifications-wrap sbi_review_notice',
						'class' => 'message rn_step_2',
						'data' => array(
							'message-id' => !empty($notification['id']) ? esc_attr(sanitize_text_field($notification['id'])) : 0,
						),
						'title' => array(
							'text' => __('Glad to hear you are enjoying it. Would you consider leaving a positive review?', 'instagram-feed'),
							'class' => 'title',
						),
						'message' => __('It really helps to support the plugin and help others to discover it too!', 'instagram-feed'),
						'image' => $image,
						'buttons' => $buttons,
						'buttons_wrap_start' => '<div class="buttons">',
						'buttons_wrap_end' => '</div>',
						'styles' => array(
							'display' => array(
								'condition' => array(
									'key' => 'option',
									'name' => 'sbi_review_consent',
									'compare' => '===',
									'value' => 'yes',
								),
								'true' => '',
								'false' => 'none',
							),
						),
						'priority' => 51,
						'wrap_schema' => '<div {wrap_id} {wrap_class}>{dismiss}{navigation}<div class="messages"><div {class} {data} {styles}>{image}{title}<p class="content">{message}</p>{buttons}</div></div></div>',
					);
					$error_args = wp_parse_args($error_args, $notice_args);
					$error_args['dismiss']['href'] = array(
						'args' => array(
							'sbi_dismiss' => $type
						),
						'action' => 'sbi-' . $type
					);
					$sbi_notices->add_notice('review_step_2', 'information', $error_args, 'marketing');
					break;

				case 'discount':
					$error_args = array(
						'wrap_class' => 'sbi-notifications-wrap sbi_discount_notice',
						'class' => 'message',
						'data' => array(
							'message-id' => !empty($notification['id']) ? esc_attr(sanitize_text_field($notification['id'])) : 0,
						),
						'title' => array(
							'text' => !empty($notification['title']) ? sanitize_text_field($this->replace_merge_fields($notification['title'], $notification)) : '',
							'class' => 'title',
						),
						'message' => !empty($notification['content']) ? wp_kses($this->replace_merge_fields($notification['content'], $notification), $content_allowed_tags) : '',
						'image' => $image,
						'buttons' => $buttons,
						'buttons_wrap_start' => '<div class="buttons">',
						'buttons_wrap_end' => '</div>',
						'start_date' => !empty($notification['start']) ? $notification['start'] : '',
						'end_date' => !empty($notification['end']) ? $notification['end'] : '',
					);
					$error_args = wp_parse_args($error_args, $notice_args);
					$error_args['dismiss']['href'] = array(
						'args' => array(
							'sbi_dismiss' => $type
						),
						'action' => 'sbi-' . $type
					);
					$sbi_notices->add_notice($notification['id'], 'information', $error_args, 'marketing');
					break;

				default:
					$error_args = array(
						'class' => 'message',
						'data' => array(
							'message-id' => !empty($notification['id']) ? esc_attr(sanitize_text_field($notification['id'])) : 0,
						),
						'title' => array(
							'text' => !empty($notification['title']) ? sanitize_text_field($this->replace_merge_fields($notification['title'], $notification)) : '',
							'class' => 'title',
						),
						'message' => !empty($notification['content']) ? wp_kses($this->replace_merge_fields($notification['content'], $notification), $content_allowed_tags) : '',
						'image' => $image,
						'priority' => 100,
						'buttons' => $buttons,
						'buttons_wrap_start' => '<div class="buttons">',
						'buttons_wrap_end' => '</div>',
						'start_date' => !empty($notification['start']) ? $notification['start'] : '',
						'end_date' => !empty($notification['end']) ? $notification['end'] : '',
					);
					$error_args = wp_parse_args($error_args, $notice_args);
					$sbi_notices->add_notice($notification['id'], 'information', $error_args, 'marketing');

					break;
			}
		}
	}

	/**
	 * Fields from the remote source contain placeholders to allow
	 * some messages to be used for multiple plugins.
	 *
	 * @param $content string
	 * @param $notification array
	 *
	 * @return string
	 *
	 * @since 2.6/5.9
	 */
	public function replace_merge_fields($content, $notification)
	{
		$merge_fields = array(
			'{plugin}' => 'Instagram Feed',
			'{amount}' => isset($notification['amount']) ? $notification['amount'] : '',
			'{platform}' => 'Instagram',
			'{lowerplatform}' => 'instagram',
			'{review-url}' => 'https://wordpress.org/support/plugin/instagram-feed/reviews/',
			'{slug}' => 'instagram-feed',
			'{campaign}' => 'instagram-free'
		);

		if (sbi_is_pro_version()) {
			$merge_fields['{campaign}'] = 'instagram-pro';
			$merge_fields['{plugin}'] = 'Instagram Feed Pro';
		}

		foreach ($merge_fields as $find => $replace) {
			$content = str_replace($find, $replace, $content);
		}

		return $content;
	}

	/**
	 * Dismiss notification via AJAX. If it's a new user message, also dismiss it
	 * on all admin pages.
	 *
	 * @since 2.6/5.9
	 */
	public function dismiss()
	{
		// Run a security check.
		check_ajax_referer('sbi-admin', 'nonce');

		// Check for access and required param.
		if (!$this->has_access() || empty($_POST['id'])) {
			wp_send_json_error();
		}

		$id = sanitize_text_field(wp_unslash($_POST['id']));

		if ($id === 'review') {
			$sbi_statuses_option = get_option('sbi_statuses', array());

			update_option('sbi_rating_notice', 'dismissed', false);
			$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
			update_option('sbi_statuses', $sbi_statuses_option, false);

			// remove the rating notice step 1 and step 2 from global notices
			global $sbi_notices;
			$sbi_notices->remove_notice('review_step_1');
			$sbi_notices->remove_notice('review_step_2');
			$sbi_notices->remove_notice('review_step_1_all_pages');
			$sbi_notices->remove_notice('review_step_2_all_pages');
		} elseif ($id === 'discount') {
			update_user_meta(get_current_user_id(), 'sbi_ignore_new_user_sale_notice', 'always');

			$current_month_number = (int)date('n', sbi_get_current_time());
			$not_early_in_the_year = ($current_month_number > 5);

			if ($not_early_in_the_year) {
				update_user_meta(get_current_user_id(), 'sbi_ignore_bfcm_sale_notice', date('Y', sbi_get_current_time()));
			}

			global $sbi_notices;
			$sbi_notices->remove_notice('discount');
		} else {
			global $sbi_notices;
			$sbi_notices->remove_notice($id);
		}

		$option = $this->get_option();
		$type = is_numeric($id) ? 'feed' : 'events';

		$option['dismissed'][] = $id;
		$option['dismissed'] = array_unique($option['dismissed']);

		// Remove notification.
		if (is_array($option[$type]) && !empty($option[$type])) {
			foreach ($option[$type] as $key => $notification) {
				if ($notification['id'] == $id) { // phpcs:ignore WordPress.PHP.StrictComparisons
					unset($option[$type][$key]);
					break;
				}
			}
		}

		update_option('sbi_notifications', $option);

		wp_send_json_success();
	}
}
