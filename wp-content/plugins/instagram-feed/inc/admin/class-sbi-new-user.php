<?php

/**
 * SBI_New_User.
 *
 * @since 2.6
 */

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

class SBI_New_User extends SBI_Notifications
{
	/**
	 * Source of notifications content.
	 *
	 * @since 2.6
	 *
	 * @var string
	 */
	const SOURCE_URL = 'https://plugin.smashballoon.com/newuser.json';

	/**
	 * @var string
	 */
	const OPTION_NAME = 'sbi_newuser_notifications';

	/**
	 * Register hooks.
	 *
	 * @since 2.6
	 */
	public function hooks()
	{
		add_action('admin_init', array($this, 'output'), 8);

		add_action('admin_init', array($this, 'dismiss'));
		add_action('wp_ajax_sbi_review_notice_consent_update', array($this, 'review_notice_consent'));
	}

	public function source_url()
	{
		return self::SOURCE_URL;
	}

	/**
	 * Add a manual notification event.
	 *
	 * @param array $notification Notification data.
	 * @since 2.6
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
			$this->option_name(),
			array(
				'update' => $option['update'],
				'feed' => $option['feed'],
				'events' => array_merge($notification, $option['events']),
				'dismissed' => $option['dismissed'],
			)
		);
	}

	/**
	 * Verify notification data before it is saved.
	 *
	 * @param array $notifications Array of notifications items to verify.
	 *
	 * @return array
	 *
	 * @since 2.6
	 */
	public function verify($notifications)
	{
		$data = array();

		if (!is_array($notifications) || empty($notifications)) {
			return $data;
		}

		$option = $this->get_option();

		foreach ($notifications as $key => $notification) {
			// The message should never be empty, if they are, ignore.
			if (empty($notification['content'])) {
				continue;
			}

			// Ignore if notification has already been dismissed.
			if (!empty($option['dismissed']) && in_array($notification['id'], $option['dismissed'])) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				continue;
			}

			$data[$key] = $notification;
		}

		return $data;
	}

	public function option_name()
	{
		return self::OPTION_NAME;
	}

	/**
	 * Do not enqueue anything extra.
	 *
	 * @since 2.6
	 */
	public function enqueues()
	{
	}

	public function review_notice_consent()
	{
		// Security Checks
		check_ajax_referer('sbi_nonce', 'sbi_nonce');
		$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';

		$cap = apply_filters('sbi_settings_pages_capability', $cap);
		if (!current_user_can($cap)) {
			wp_send_json_error(); // This auto-dies.
		}

		$consent = isset($_POST['consent']) ? sanitize_text_field($_POST['consent']) : '';

		update_option('sbi_review_consent', $consent);

		if ($consent == 'no') {
			$sbi_statuses_option = get_option('sbi_statuses', array());
			update_option('sbi_rating_notice', 'dismissed', false);
			$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
			update_option('sbi_statuses', $sbi_statuses_option, false);

			// remove the rating notice step 1 and step 2 from global notices.
			global $sbi_notices;
			$sbi_notices->remove_notice('review_step_1');
			$sbi_notices->remove_notice('review_step_2');
			$sbi_notices->remove_notice('review_step_1_all_pages');
			$sbi_notices->remove_notice('review_step_2_all_pages');
		} elseif ($consent == 'yes') {
			global $sbi_notices;
			$sbi_notices->remove_notice('review_step_1');
			$sbi_notices->remove_notice('review_step_1_all_pages');
		}
		wp_die();
	}

	/**
	 * Output notifications on Form Overview admin area.
	 *
	 * @since 2.6
	 */
	public function output()
	{
		$notifications = $this->get();

		if (empty($notifications)) {
			return;
		}

		// new user notices included in regular settings page notifications so this
		// checks to see if user is one of those pages
		if (
			!empty($_GET['page'])
			&& strpos($_GET['page'], 'sbi') !== false
		) {
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
		$review_consent = get_option('sbi_review_consent');

		global $sbi_notices;
		$notice_args = array(
			'page_exclude' => array(
				'sbi-feed-builder',
				'sbi-settings',
				'sbi-oembeds-manager',
				'sbi-extensions-manager',
				'sbi-about-us',
				'sbi-support',
			),
			'capability' => array('manage_instagram_feed_options', 'manage_options'),
		);

		foreach ($notifications as $notification) {
			$img_src = SBI_PLUGIN_URL . 'admin/assets/img/' . sanitize_text_field($notification['image']);
			$type = sanitize_text_field($notification['id']);
			$title = $this->get_notice_title($notification);
			$content = $this->get_notice_content($notification, $content_allowed_tags);
			$buttons = array();

			if (!empty($notification['btns']) && is_array($notification['btns'])) {
				foreach ($notification['btns'] as $btn_type => $btn) {
					$class = $btn_type === 'primary' ? 'sbi-btn-blue' : 'sbi-btn-grey';
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
						'text' => !empty($btn['text']) ? wp_kses($btn['text'], $content_allowed_tags) : '',
						'tag' => !empty($btn['tag']) ? $btn['tag'] : 'a',
					);
				}
			}

			switch ($type) {
				case 'review':
					$sbi_open_feedback_url = 'https://smashballoon.com/feedback/?plugin=instagram-free';
					// step 1 for the review notice.
					if (!$review_consent) {
						$error_args = array(
							'class' => 'sbi_notice sbi_review_notice_step_1',
							'image' => array(
								'src' => $img_src,
								'alt' => 'notice',
								'wrap' => '<div class="sbi_thumb"><img {src} {alt}></div>',
							),
							'message' => __('Are you enjoying the Instagram Feed Plugin?', 'instagram-feed'),
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
							'buttons_wrap_start' => '<div class="sbi-notice-consent-btns">',
							'buttons_wrap_end' => '</div>',
							'priority' => 50,
							'wrap_schema' => '<div {class}>{image}<div class="sbi-notice-text"><p class="sbi-notice-text-p">{message}</p></div>{buttons}</div>',
						);
						$error_args = wp_parse_args($error_args, $notice_args);
						$sbi_notices->add_notice('review_step_1_all_pages', 'information', $error_args);
					}
					$error_args = array(
						'class' => 'sbi_notice_op sbi_notice sbi_review_notice',
						'title' => array(
							'text' => $title,
							'class' => 'sbi-notice-text-header',
						),
						'message' => $content,
						'image' => array(
							'src' => $img_src,
							'alt' => 'notice',
							'wrap' => '<div class="sbi_thumb"><img {src} {alt}></div>',
						),
						'buttons' => $buttons,
						'buttons_wrap_start' => '<div class="sbi-notice-btns-wrap"><p class="sbi-notice-links">',
						'buttons_wrap_end' => '</p></div>',
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
						'dismissible' => true,
						'dismiss' => array(
							'class' => 'sbi-notice-dismiss',
							'icon' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-dismiss-icon.svg',
							'tag' => 'a',
							'href' => array(
								'args' => array(
									'sbi_dismiss' => $type
								),
								'action' => 'sbi-' . $type
							)
						),
						'wrap_schema' => '<div {class} {styles}>{image}<div class="sbi-notice-text">
						<div class="sbi-notice-text-inner">{title}<p class="sbi-notice-text-p">{message}</p></div>{buttons}</div>{dismiss}</div>',
					);
					$error_args = wp_parse_args($error_args, $notice_args);
					$sbi_notices->add_notice('review_step_2_all_pages', 'information', $error_args);
					break;
				default:
					$error_args = array(
						'class' => 'sbi_notice_op sbi_notice sbi_' . $type . '_notice',
						'title' => array(
							'text' => $title,
							'class' => 'sbi-notice-text-header',
						),
						'message' => $content,
						'image' => array(
							'src' => $img_src,
							'alt' => 'notice',
							'wrap' => '<div class="sbi_thumb"><img {src} {alt}></div>',
						),
						'buttons' => $buttons,
						'buttons_wrap_start' => '<div class="sbi-notice-btns-wrap"><p class="sbi-notice-links">',
						'buttons_wrap_end' => '</p></div>',
						'dismissible' => true,
						'dismiss' => array(
							'class' => 'sbi-notice-dismiss',
							'icon' => SBI_PLUGIN_URL . 'admin/assets/img/sbi-dismiss-icon.svg',
							'tag' => 'a',
							'href' => array(
								'args' => array(
									'sbi_dismiss' => $type
								),
								'action' => 'sbi-' . $type
							)
						),
						'wrap_schema' => '<div {class} {styles}>{image}<div class="sbi-notice-text">
						<div class="sbi-notice-text-inner">{title}<p class="sbi-notice-text-p">{message}</p></div>{buttons}</div>{dismiss}</div>',
					);
					$error_args = wp_parse_args($error_args, $notice_args);
					$sbi_notices->add_notice($notification['id'], 'information', $error_args);
					break;
			}
		}
	}

	/**
	 * Get notification data.
	 *
	 * @return array
	 * @since 2.6
	 */
	public function get()
	{
		if (!$this->has_access()) {
			return array();
		}

		$option = $this->get_option();

		// Only update if does not exist.
		if (empty($option['update'])) {
			$this->update();
		}

		$events = !empty($option['events']) ? $this->verify_active($option['events']) : array();
		$feed = !empty($option['feed']) ? $this->verify_active($option['feed']) : array();

		return array_merge($events, $feed);
	}

	/**
	 * Update notification data from feed.
	 *
	 * @since 2.6
	 */
	public function update()
	{
		$feed = $this->fetch_feed();
		$option = $this->get_option();

		update_option(
			$this->option_name(),
			array(
				'update' => time(),
				'feed' => $feed,
				'events' => $option['events'],
				'dismissed' => $option['dismissed'],
			)
		);
	}

	/**
	 * Verify saved notification data for active notifications.
	 *
	 * @param array $notifications Array of notifications items to verify.
	 *
	 * @return array
	 * @since 2.6
	 */
	public function verify_active($notifications)
	{
		if (!is_array($notifications) || empty($notifications)) {
			return array();
		}

		$sbi_statuses_option = get_option('sbi_statuses', array());
		$current_time = sbi_get_current_time();

		// rating notice logic
		$sbi_rating_notice_option = get_option('sbi_rating_notice', false);
		$sbi_rating_notice_waiting = get_transient('instagram_feed_rating_notice_waiting');
		$should_show_rating_notice = ($sbi_rating_notice_waiting !== 'waiting' && $sbi_rating_notice_option !== 'dismissed');

		// new user discount logic
		$in_new_user_month_range = true;
		$should_show_new_user_discount = false;
		$has_been_one_month_since_rating_dismissal = isset($sbi_statuses_option['rating_notice_dismissed']) ? ((int)$sbi_statuses_option['rating_notice_dismissed'] + ((int)$notifications['review']['wait'] * DAY_IN_SECONDS)) < $current_time + 1 : true;

		if (isset($sbi_statuses_option['first_install']) && $sbi_statuses_option['first_install'] === 'from_update') {
			global $current_user;
			$user_id = $current_user->ID;
			$ignore_new_user_sale_notice_meta = get_user_meta($user_id, 'sbi_ignore_new_user_sale_notice');
			$ignore_new_user_sale_notice_meta = isset($ignore_new_user_sale_notice_meta[0]) ? $ignore_new_user_sale_notice_meta[0] : '';
			if ($ignore_new_user_sale_notice_meta !== 'always') {
				$should_show_new_user_discount = true;
			}
		} elseif ($in_new_user_month_range && $has_been_one_month_since_rating_dismissal && $sbi_rating_notice_waiting !== 'waiting') {
			global $current_user;
			$user_id = $current_user->ID;
			$ignore_new_user_sale_notice_meta = get_user_meta($user_id, 'sbi_ignore_new_user_sale_notice');
			$ignore_new_user_sale_notice_meta = isset($ignore_new_user_sale_notice_meta[0]) ? $ignore_new_user_sale_notice_meta[0] : '';

			if (
				$ignore_new_user_sale_notice_meta !== 'always'
				&& isset($sbi_statuses_option['first_install'])
				&& $current_time > (int)$sbi_statuses_option['first_install'] + ((int)$notifications['discount']['wait'] * DAY_IN_SECONDS)
			) {
				$should_show_new_user_discount = true;
			}
		}

		if (sbi_is_pro_version()) {
			$should_show_new_user_discount = false;
		}

		if (isset($notifications['review']) && $should_show_rating_notice) {
			return array($notifications['review']);
		} elseif (isset($notifications['discount']) && $should_show_new_user_discount) {
			return array($notifications['discount']);
		}

		return array();
	}

	/**
	 * SBI Get Notice Title depending on the notice type
	 *
	 * @param array $notification
	 *
	 * @return string $title
	 * @since 6.0
	 */
	public function get_notice_title($notification)
	{
		$type = $notification['id'];
		$title = '';

		// Notice title depending on notice type
		if ($type == 'review') {
			$title = __('Glad to hear you are enjoying it. Would you consider leaving a positive review?', 'instagram-feed');
		} elseif ($type == 'discount') {
			$title = __('Exclusive offer - 60% off!', 'instagram-feed');
		} else {
			$title = $this->replace_merge_fields($notification['title'], $notification);
		}

		return $title;
	}

	/**
	 * SBI Get Notice Content depending on the notice type
	 *
	 * @param array $notification
	 * @param array $content_allowed_tags
	 *
	 * @return string $content
	 * @since 6.0
	 */
	public function get_notice_content($notification, $content_allowed_tags)
	{
		$type = $notification['id'];
		$content = '';

		// Notice content depending on notice type
		if ($type == 'review') {
			$content = __('It really helps to support the plugin and help others to discover it too!', 'instagram-feed');
		} elseif ($type == 'discount') {
			$content = __('We don’t run promotions very often, but for a limited time we’re offering 60% Off our Pro version to all users of our free Instagram Feed.', 'instagram-feed');
		} else {
			if (!empty($notification['content'])) {
				$content = wp_kses($this->replace_merge_fields($notification['content'], $notification), $content_allowed_tags);
			}
		}
		return $content;
	}

	/**
	 * SBI Get Notice Title depending on the notice type
	 *
	 * @param array $notification
	 *
	 * @return string $title
	 * @since 6.0
	 */
	public function dismiss()
	{
		global $current_user;
		$user_id = $current_user->ID;
		$sbi_statuses_option = get_option('sbi_statuses', array());

		// TODO: Remove at 7.0.0
		global $sbi_notices;
		$discount_notice = $sbi_notices->get_notice('discount');
		if ($discount_notice && !isset($sbi_statuses_option['preexisting_discount_notice_check'])) {
			update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');
			$sbi_notices->remove_notice('discount');
		}
		$sbi_statuses_option['preexisting_discount_notice_check'] = true;
		update_option('sbi_statuses', $sbi_statuses_option);

		// TODO:: Remove at 7.0.0
		$rating_notice_found = false;
		$rating_notices = array(
			'review_step_1',
			'review_step_1_all_pages',
			'review_step_2',
			'review_step_2_all_pages',
		);

		foreach ($rating_notices as $rating_notice) {
			$notice = $sbi_notices->get_notice($rating_notice);
			if ($notice) {
				$rating_notice_found = $notice;
				break;
			}
		}

		$sbi_rating_notice_option = get_option('sbi_rating_notice', false);
		$sbi_rating_notice_waiting = get_transient('instagram_feed_rating_notice_waiting');

		if (!empty($rating_notice_found) && $sbi_rating_notice_option === false && $sbi_rating_notice_waiting === false) {
			foreach ($rating_notices as $rating_notice) {
				$sbi_notices->remove_notice($rating_notice);
			}

			update_option('sbi_rating_notice', 'dismissed', false);
			$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
			update_option('sbi_statuses', $sbi_statuses_option, false);

			update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');
		}

		if (isset($_GET['sbi_ignore_rating_notice_nag'])) {
			$rating_ignore = false;
			if (isset($_GET['sbi_nonce']) && wp_verify_nonce($_GET['sbi_nonce'], 'sbi-review')) {
				$rating_ignore = isset($_GET['sbi_ignore_rating_notice_nag']) ? sanitize_text_field($_GET['sbi_ignore_rating_notice_nag']) : false;
			}
			if (1 === (int)$rating_ignore) {
				update_option('sbi_rating_notice', 'dismissed', false);
				$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
				update_option('sbi_statuses', $sbi_statuses_option, false);

				$sbi_notices->remove_notice('review_step_2');
				$sbi_notices->remove_notice('review_step_2_all_pages');
			} elseif ('later' === $rating_ignore) {
				set_transient('instagram_feed_rating_notice_waiting', 'waiting', 2 * WEEK_IN_SECONDS);
				delete_option('sbi_review_consent');
				update_option('sbi_rating_notice', 'pending', false);

				$sbi_notices->remove_notice('review_step_2');
				$sbi_notices->remove_notice('review_step_2_all_pages');
			}
		}

		if (isset($_GET['sbi_ignore_new_user_sale_notice'])) {
			$new_user_ignore = false;
			if (isset($_GET['sbi_nonce']) && wp_verify_nonce($_GET['sbi_nonce'], 'sbi-discount')) {
				$new_user_ignore = isset($_GET['sbi_ignore_new_user_sale_notice']) ? sanitize_text_field($_GET['sbi_ignore_new_user_sale_notice']) : false;
			}
			if ('always' === $new_user_ignore) {
				update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');

				$current_month_number = (int)date('n', sbi_get_current_time());
				$not_early_in_the_year = ($current_month_number > 5);

				if ($not_early_in_the_year) {
					update_user_meta($user_id, 'sbi_ignore_bfcm_sale_notice', date('Y', sbi_get_current_time()));
				}

				$sbi_notices->remove_notice('discount');
			}
		}

		if (isset($_GET['sbi_ignore_bfcm_sale_notice'])) {
			$bfcm_ignore = false;
			if (isset($_GET['sbi_nonce']) && wp_verify_nonce($_GET['sbi_nonce'], 'sbi-bfcm')) {
				$bfcm_ignore = isset($_GET['sbi_ignore_bfcm_sale_notice']) ? sanitize_text_field($_GET['sbi_ignore_bfcm_sale_notice']) : false;
			}
			if ('always' === $bfcm_ignore) {
				update_user_meta($user_id, 'sbi_ignore_bfcm_sale_notice', 'always');
			} elseif (date('Y', sbi_get_current_time()) === $bfcm_ignore) {
				update_user_meta($user_id, 'sbi_ignore_bfcm_sale_notice', date('Y', sbi_get_current_time()));
			}
			update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');

			$sbi_notices->remove_notice('discount');
		}

		if (isset($_GET['sbi_dismiss'])) {
			$review_nonce = isset($_GET['sbi_nonce']) ? wp_verify_nonce($_GET['sbi_nonce'], 'sbi-review') : false;
			$discount_nonce = isset($_GET['sbi_nonce']) ? wp_verify_nonce($_GET['sbi_nonce'], 'sbi-discount') : false;
			$notice_dismiss = ($review_nonce || $discount_nonce) ? sanitize_text_field($_GET['sbi_dismiss']) : false;

			if ('review' === $notice_dismiss) {
				update_option('sbi_rating_notice', 'dismissed', false);
				$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
				update_option('sbi_statuses', $sbi_statuses_option, false);

				update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');

				$sbi_notices->remove_notice('review_step_1');
				$sbi_notices->remove_notice('review_step_1_all_pages');
				$sbi_notices->remove_notice('review_step_2');
				$sbi_notices->remove_notice('review_step_2_all_pages');
			} elseif ('discount' === $notice_dismiss) {
				update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');

				$current_month_number = (int)date('n', sbi_get_current_time());
				$not_early_in_the_year = ($current_month_number > 5);

				if ($not_early_in_the_year) {
					update_user_meta($user_id, 'sbi_ignore_bfcm_sale_notice', date('Y', sbi_get_current_time()));
				}

				update_user_meta($user_id, 'sbi_ignore_new_user_sale_notice', 'always');

				$sbi_notices->remove_notice('discount');
			}
		}
	}
}
