<?php

namespace InstagramFeed\Builder;

use InstagramFeed\admin\SBI_Onboarding_wizard;
use InstagramFeed\Builder\Tabs\SBI_Builder_Customizer_Tab;
use InstagramFeed\Helpers\Util;
use SB_Instagram_Connected_Account;
use SB_Instagram_Data_Encryption;
use SB_Instagram_Feed_Locator;
use SB_Instagram_Parse;
use SB_Instagram_Settings;

/**
 * Custom Facebook Feed Builder
 *
 * @since 6.0
 */
class SBI_Feed_Builder
{
	private static $instance;

	/**
	 * Constructor.
	 *
	 * @since 6.0
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Init the Builder.
	 *
	 * @since 6.0
	 */
	function init()
	{
		if (is_admin()) {
			add_action('admin_menu', array($this, 'register_menu'));
			// add ajax listeners.
			SBI_Feed_Saver_Manager::hooks();
			SBI_Source::hooks();
			self::hooks();
		}
	}

	/**
	 * Mostly AJAX related hooks
	 *
	 * @since 6.0
	 */
	public static function hooks()
	{
		add_action('wp_ajax_sbi_dismiss_onboarding', array('InstagramFeed\Builder\SBI_Feed_Builder', 'after_dismiss_onboarding'));

		add_action('wp_ajax_sbi_other_plugins_modal', array('InstagramFeed\Builder\SBI_Feed_Builder', 'sb_other_plugins_modal'));
	}

	public static function instance()
	{
		if (null === self::$instance) {
			self::$instance = new self();
			return self::$instance;
		}
	}

	public static function sb_other_plugins_modal()
	{
		check_ajax_referer('sbi_nonce', 'sbi_nonce');

		if (!current_user_can('activate_plugins') || !current_user_can('install_plugins')) {
			wp_send_json_error();
		}

		$plugin = isset($_POST['plugin']) ? sanitize_key($_POST['plugin']) : '';
		$sb_other_plugins = self::install_plugins_popup();
		$plugin = isset($sb_other_plugins[$plugin]) ? $sb_other_plugins[$plugin] : false;
		if (!$plugin) {
			wp_send_json_error();
		}

		// Build the content for modals
		$output = '<div class="sbi-fb-source-popup sbi-fb-popup-inside sbi-install-plugin-modal">
		<div class="sbi-fb-popup-cls">' . self::builder_svg_icons('close') . '</div>
		<div class="sbi-install-plugin-body sbi-fb-fs">
		<div class="sbi-install-plugin-header">
		<div class="sb-plugin-image">' . $plugin['svgIcon'] . '</div>
		<div class="sb-plugin-name">
		<h3>' . $plugin['name'] . '<span>Free</span></h3>
		<p><span class="sb-author-logo">
		' . self::builder_svg_icons('smash-logo') . '
		</span>
		<span class="sb-author-name">' . $plugin['author'] . '</span>
		</p></div></div>
		<div class="sbi-install-plugin-content">
		<p>' . $plugin['description'] . '</p>';

		$plugin_install_data = array(
			'step' => 'install',
			'action' => 'sbi_install_addon',
			'nonce' => wp_create_nonce('sbi-admin'),
			'plugin' => $plugin['plugin'],
			'download_plugin' => $plugin['download_plugin'],
		);

		if (!$plugin['installed']) {
			$output .= sprintf(
				"<button class='sbi-install-plugin-btn sbi-btn-orange' id='sbi_install_op_btn' data-plugin-atts='%s'>%s</button></div></div></div>",
				sbi_json_encode($plugin_install_data),
				__('Install', 'instagram-feed')
			);
		}
		if ($plugin['installed'] && !$plugin['activated']) {
			$plugin_install_data['step'] = 'activate';
			$plugin_install_data['action'] = 'sbi_activate_addon';
			$output .= sprintf(
				"<button class='sbi-install-plugin-btn sbi-btn-orange' id='sbi_install_op_btn' data-plugin-atts='%s'>%s</button></div></div></div>",
				sbi_json_encode($plugin_install_data),
				__('Activate', 'instagram-feed')
			);
		}
		if ($plugin['installed'] && $plugin['activated']) {
			$output .= sprintf(
				"<button class='sbi-install-plugin-btn sbi-btn-orange' id='sbi_install_op_btn' disabled='disabled'>%s</button></div></div></div>",
				__('Plugin installed & activated', 'instagram-feed')
			);
		}

		wp_send_json_success(['output' => $output]);
		wp_die();
	}

	/**
	 * Used to dismiss onboarding using AJAX
	 *
	 * @since 6.0
	 */
	public static function after_dismiss_onboarding()
	{
		check_ajax_referer('sbi-admin', 'nonce');

		$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';
		$cap = apply_filters('sbi_settings_pages_capability', $cap);

		if (current_user_can($cap)) {
			$type = 'newuser';
			if (isset($_POST['was_active'])) {
				$type = sanitize_key($_POST['was_active']);
			}
			self::update_onboarding_meta('dismissed', $type);
		}
		wp_die();
	}

	/**
	 * Update status of onboarding sequence for specific user
	 *
	 * @return string|boolean
	 *
	 * @since 6.0
	 */
	public static function update_onboarding_meta($value, $type = 'newuser')
	{
		$onboarding_statuses = get_user_meta(get_current_user_id(), 'sbi_onboarding', true);
		if (!empty($onboarding_statuses)) {
			$statuses = Util::safe_unserialize($onboarding_statuses);
			$statuses[$type] = $value;
		} else {
			$statuses = array(
				$type => $value
			);
		}

		$statuses = maybe_serialize($statuses);

		update_user_meta(get_current_user_id(), 'sbi_onboarding', $statuses);
	}

	/**
	 * Get Smahballoon Plugins Info
	 *
	 * @since 6.2.9
	 */
	public static function get_smashballoon_plugins_info()
	{
		$active_sb_plugins = Util::get_sb_active_plugins_info();

		return [
			'facebook' => [
				'installed' => $active_sb_plugins['is_facebook_installed'],
				'class' => 'CFF_Elementor_Widget',
				'link' => 'https://smashballoon.com/custom-facebook-feed/',
				'icon' => self::builder_svg_icons('install-plugins-popup.facebook'),
				'description' => __('Custom Facebook Feeds is a highly customizable way to display tweets from your Facebook account. Promote your latest content and update your site content automatically.', 'instagram-feed'),
				'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
				'dashboard_link' => admin_url('admin.php?page=cff-feed-builder'),
				'active' => is_plugin_active($active_sb_plugins['facebook_plugin'])
			],
			'instagram' => [
				'installed' => $active_sb_plugins['is_instagram_installed'],
				'class' => 'SBI_Elementor_Widget',
				'link' => 'https://smashballoon.com/instagram-feed/',
				'icon' => self::builder_svg_icons('install-plugins-popup.instagram'),
				'description' => __('Instagram Feeds is a highly customizable way to display tweets from your Instagram account. Promote your latest content and update your site content automatically.', 'instagram-feed'),
				'download_plugin' => 'https://downloads.wordpress.org/plugin/instagram-feed.zip',
				'dashboard_link' => admin_url('admin.php?page=sbi-feed-builder'),
				'active' => is_plugin_active($active_sb_plugins['instagram_plugin'])
			],
			'twitter' => [
				'installed' => $active_sb_plugins['is_twitter_installed'],
				'class' => 'CTF_Elementor_Widget',
				'link' => 'https://smashballoon.com/custom-twitter-feeds/',
				'icon' => self::builder_svg_icons('install-plugins-popup.twitter'),
				'description' => __('Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'instagram-feed'),
				'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
				'dashboard_link' => admin_url('admin.php?page=ctf-feed-builder'),
				'active' => is_plugin_active($active_sb_plugins['twitter_plugin'])
			],
			'youtube' => [
				'installed' => $active_sb_plugins['is_youtube_installed'],
				'class' => 'SBY_Elementor_Widget',
				'link' => 'https://smashballoon.com/youtube-feed/',
				'icon' => self::builder_svg_icons('install-plugins-popup.youtube'),
				'description' => __('YouTube Feeds is a highly customizable way to display tweets from your YouTube account. Promote your latest content and update your site content automatically.', 'instagram-feed'),
				'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
				'dashboard_link' => admin_url('admin.php?page=sby-feed-builder'),
				'active' => is_plugin_active($active_sb_plugins['youtube_plugin'])
			]
		];
	}

	/**
	 * Register Menu.
	 *
	 * @since 6.0
	 */
	public function register_menu()
	{
		$cap = current_user_can('manage_instagram_feed_options') ? 'manage_instagram_feed_options' : 'manage_options';
		$cap = apply_filters('sbi_settings_pages_capability', $cap);

		$feed_builder = add_submenu_page(
			'sb-instagram-feed',
			__('All Feeds', 'instagram-feed'),
			__('All Feeds', 'instagram-feed'),
			$cap,
			'sbi-feed-builder',
			array($this, 'feed_builder'),
			0
		);
		add_action('load-' . $feed_builder, array($this, 'builder_enqueue_admin_scripts'));
	}

	/**
	 * Enqueue Builder CSS & Script.
	 *
	 * Loads only for builder pages
	 *
	 * @since 6.0
	 */
	public function builder_enqueue_admin_scripts()
	{
		if (get_current_screen()) :
			$screen = get_current_screen();
			if (strpos($screen->id, 'sbi-feed-builder') !== false || strpos($screen->id, 'sbi-setup') !== false) :
				$installed_plugins = get_plugins();

				$newly_retrieved_source_connection_data = SBI_Source::maybe_source_connection_data();
				$license_key = get_option('sbi_license_key', '');
				$upgrade_url = 'https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=lite-upgrade-bar';

				$active_extensions = array(
					// Fake.
					'feedLayout' => false,
					'headerLayout' => false,
					'postStyling' => false,
					'lightbox' => false,
					'filtermoderation' => false,
					'shoppablefeed' => false,
				);

				$sbi_builder = array(
					'ajax_handler' => admin_url('admin-ajax.php'),
					'pluginType' => 'free',
					'licenseType' => sbi_is_pro_version() ? 'pro' : 'free',
					'isSetupPage' => strpos($screen->id, 'sbi-setup') !== false ? 'true' : 'false',
					'builderUrl' => admin_url('admin.php?page=sbi-feed-builder'),
					'setUpUrl' => admin_url('admin.php?page=sbi-setup'),
					'upgradeUrl' => $upgrade_url,
					'activeExtensions' => $active_extensions,
					'pluginUrl' => trailingslashit(SBI_PLUGIN_URL),

					'nonce' => wp_create_nonce('sbi-admin'),
					'admin_nonce' => wp_create_nonce('sbi_admin_nonce'),

					'adminPostURL' => admin_url('post.php'),
					'widgetsPageURL' => admin_url('widgets.php'),
					'themeSupportsWidgets' => current_theme_supports('widgets'),
					'supportPageUrl' => admin_url('admin.php?page=sbi-support'),
					'genericText' => self::get_generic_text(),
					'welcomeScreen' => array(
						'mainHeading' => __('All Feeds', 'instagram-feed'),
						'createFeed' => __('Create your Feed', 'instagram-feed'),
						'createFeedDescription' => __('Connect your Instagram account and choose a feed type', 'instagram-feed'),
						'customizeFeed' => __('Customize your feed type', 'instagram-feed'),
						'customizeFeedDescription' => __('Choose layouts, color schemes, styles and more', 'instagram-feed'),
						'embedFeed' => __('Embed your feed', 'instagram-feed'),
						'embedFeedDescription' => __('Easily add the feed anywhere on your website', 'instagram-feed'),
						'customizeImgPath' => SBI_BUILDER_URL . 'assets/img/welcome-1.png',
						'embedImgPath' => SBI_BUILDER_URL . 'assets/img/welcome-2.png',
					),
					'pluginsInfo' => array(
						'social_wall' => array(
							'installed' => isset($installed_plugins['social-wall/social-wall.php']),
							'activated' => is_plugin_active('social-wall/social-wall.php'),
							'settingsPage' => admin_url('admin.php?page=sbsw'),
						)
					),
					'allFeedsScreen' => array(
						'mainHeading' => __('All Feeds', 'instagram-feed'),
						'columns' => array(
							'nameText' => __('Name', 'instagram-feed'),
							'shortcodeText' => __('Shortcode', 'instagram-feed'),
							'instancesText' => __('Instances', 'instagram-feed'),
							'actionsText' => __('Actions', 'instagram-feed'),
						),
						'bulkActions' => __('Bulk Actions', 'instagram-feed'),
						'legacyFeeds' => array(
							'heading' => __('Legacy Feeds', 'instagram-feed'),
							'toolTip' => __('What are Legacy Feeds?', 'instagram-feed'),
							'toolTipExpanded' => array(
								__('Legacy feeds are older feeds from before the version 6 update. You can edit settings for these feeds by using the "Settings" button to the right. These settings will apply to all legacy feeds, just like the settings before version 6, and work in the same way that they used to.', 'instagram-feed'),
								__('You can also create a new feed, which will now have it\'s own individual settings. Modifying settings for new feeds will not affect other feeds.', 'instagram-feed'),
							),
							'toolTipExpandedAction' => array(
								__('Legacy feeds represent shortcodes of old feeds found on your website before <br/>the version 6 update.', 'instagram-feed'),
								__('To edit Legacy feed settings, you will need to use the "Settings" button above <br/>or edit their shortcode settings directly. To delete a legacy feed, simply remove the <br/>shortcode wherever it is being used on your site.', 'instagram-feed'),
							),
							'show' => __('Show Legacy Feeds', 'instagram-feed'),
							'hide' => __('Hide Legacy Feeds', 'instagram-feed'),
						),
						'socialWallLinks' => self::get_social_wall_links(),
						'onboarding' => $this->get_onboarding_text()
					),
					'dialogBoxPopupScreen' => array(
						'deleteSource' => array(
							'heading' => __('Delete "#"?', 'instagram-feed'),
							'description' => __('This source is being used in a feed on your site. If you delete this source then new posts can no longer be retrieved for these feeds.', 'instagram-feed'),
						),
						'deleteSourceCustomizer' => array(
							'heading' => __('Delete "#"?', 'instagram-feed'),
							'description' => __('You are going to delete this source. To retrieve it, you will need to add it again. Are you sure you want to continue?', 'instagram-feed'),
						),
						'deleteSingleFeed' => array(
							'heading' => __('Delete "#"?', 'instagram-feed'),
							'description' => __('You are going to delete this feed. You will lose all the settings. Are you sure you want to continue?', 'instagram-feed'),
						),
						'deleteMultipleFeeds' => array(
							'heading' => __('Delete Feeds?', 'instagram-feed'),
							'description' => __('You are going to delete these feeds. You will lose all the settings. Are you sure you want to continue?', 'instagram-feed'),
						),
						'backAllToFeed' => array(
							'heading' => __('Are you Sure?', 'instagram-feed'),
							'description' => __('Are you sure you want to leave this page, all unsaved settings will be lost, please make sure to save before leaving.', 'instagram-feed'),
							'customButtons' => array(
								'confirm' => array(
									'text' => __('Save and Exit', 'instagram-feed'),
									'color' => 'blue',
								),
								'cancel' => array(
									'text' => __('Exit without Saving', 'instagram-feed'),
									'color' => 'red',
								),
							),
						),
						'unsavedFeedSources' => array(
							'heading' => __('You have unsaved changes', 'instagram-feed'),
							'description' => __('If you exit without saving, all the changes you made will be reverted.', 'instagram-feed'),
							'customButtons' => array(
								'confirm' => array(
									'text' => __('Save and Exit', 'instagram-feed'),
									'color' => 'blue'
								),
								'cancel' => array(
									'text' => __('Exit without Saving', 'instagram-feed'),
									'color' => 'red'
								)
							)
						)
					),
					'selectFeedTypeScreen' => array(
						'mainHeading' => __('Create an Instagram Feed', 'instagram-feed'),
						'feedTypeHeading' => __('Select Feed Type', 'instagram-feed'),
						'mainDescription' => __('Select one or more feed types. You can add or remove them later.', 'instagram-feed'),
						'updateHeading' => __('Update Feed Type', 'instagram-feed'),
						'advancedHeading' => __('Advanced Feeds', 'instagram-feed'),
						'anotherFeedTypeHeading' => __('Add Another Source Type', 'instagram-feed'),
					),
					'mainFooterScreen' => array(
						'heading' => sprintf(__('Upgrade to the %1$sAll Access Bundle%2$s to get all of our Pro Plugins', 'instagram-feed'), '<strong>', '</strong>'),
						'description' => __('Includes all Smash Balloon plugins for one low price: Instagram, Facebook, Twitter, YouTube, and Social Wall', 'instagram-feed'),
						'promo' => sprintf(__('%1$sBonus%2$s Lite users get %3$s50&#37; Off%4$s automatically applied at checkout', 'instagram-feed'), '<span class="sbi-bld-ft-bns">', '</span>', '<strong>', '</strong>'),
					),
					'embedPopupScreen' => array(
						'heading' => __('Embed Feed', 'instagram-feed'),
						'description' => __('Add the unique shortcode to any page, post, or widget:', 'instagram-feed'),
						'description_2' => current_theme_supports('widgets') ? __('Or use the built in WordPress block or widget', 'instagram-feed') : __('Or use the built in WordPress block', 'instagram-feed'),
						'addPage' => __('Add to a Page', 'instagram-feed'),
						'addWidget' => __('Add to a Widget', 'instagram-feed'),
						'selectPage' => __('Select Page', 'instagram-feed'),
					),
					'links' => self::get_links_with_utm(),
					'pluginsInfo' => array(
						'social_wall' => array(
							'installed' => isset($installed_plugins['social-wall/social-wall.php']) ? true : false,
							'activated' => is_plugin_active('social-wall/social-wall.php'),
							'settingsPage' => admin_url('admin.php?page=sbsw'),
						)
					),
					'selectSourceScreen' => self::select_source_screen_text(),
					'feedTypes' => $this->get_feed_types(),
					'advancedFeedTypes' => $this->get_advanced_feed_types(),
					'socialInfo' => $this->get_smashballoon_info(),
					'installPluginsPopup' => $this->install_plugins_popup(),
					'feeds' => self::get_feed_list(),
					'itemsPerPage' => SBI_Db::RESULTS_PER_PAGE,
					'feedsCount' => SBI_Db::feeds_count(),
					'sources' => self::get_source_list(),
					'sourceConnectionURLs' => SBI_Source::get_connection_urls(),

					'legacyFeeds' => $this->get_legacy_feed_list(),
					'extensionsPopup' => array(
						'hashtag' => array(
							'heading' => __('Upgrade to Pro to get Hashtag Feeds', 'instagram-feed'),
							'description' => __('Display posts from any public hashtag with an Instagram hashtag feed. Great for pulling in user-generated content associated with your brand, running promotional hashtag campaigns, engaging audiences at events, and more.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/hashtag?utm_campaign=instagram-free&utm_source=feed-type&utm_medium=hashtag')
						),
						'tagged' => array(
							'heading' => __('Upgrade to Pro to get Tagged Posts Feed', 'instagram-feed'),
							'description' => __('Display posts that you\'ve been tagged in by other users allowing you to increase your audience\'s engagement with your Instagram account.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=feed-type&utm_medium=tagged')
						),
						'socialwall' => array(
							// Combine all your social media channels into one Social Wall
							'heading' => '<span class="sb-social-wall">' . __('Combine all your social media channels into one', 'instagram-feed') . ' <span>' . __('Social Wall', 'instagram-feed') . '</span></span>',
							'description' => '<span class="sb-social-wall">' . __('A dash of Instagram, a sprinkle of Facebook, a spoonful of Twitter, and a dollop of YouTube, all in the same feed.', 'instagram-feed') . '</span>',
							'demoUrl' => 'https://smashballoon.com/social-wall/demo/?utm_campaign=instagram-free&utm_source=feed-type&utm_medium=social-wall&utm_content=learn-more',
							'buyUrl' => sprintf('https://smashballoon.com/social-wall/demo/?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=feed-type&utm_medium=social-wall&utm_content=Try Demo', $license_key),
							'bullets' => array(
								'heading' => __('Upgrade to the All Access Bundle and get:', 'instagram-feed'),
								'content' => array(
									__('Instagram Feed Pro', 'instagram-feed'),
									__('Custom Twitter Feeds Pro', 'instagram-feed'),
									__('YouTube Feeds Pro', 'instagram-feed'),
									__('Custom Facebook Feed Pro', 'instagram-feed'),
									__('All Pro Facebook Extensions', 'instagram-feed'),
									__('Social Wall Pro', 'instagram-feed'),
								)
							),
						),

						// Other Types
						'feedLayout' => array(
							'heading' => __('Upgrade to Pro to get Feed Layouts', 'instagram-feed'),
							'description' => __('Choose from one of our built-in layout options; grid, carousel, masonry, and highlight to allow you to showcase your content in any way you want.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=feed-layouts')
						),
						'headerLayout' => array(
							'heading' => __('Get Stories, Followers and Advanced Header Options', 'instagram-feed'),
							'description' => __('Got stories to tell? We want to help you share them. Display Instagram stories right on your website in a pop-up lightbox to keep your users engaged and on your website for longer.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=header')
						),

						'postStyling' => array(
							'heading' => __('Display Captions, Likes, and Comments', 'instagram-feed'),
							'description' => __('Upgrade to Pro to display post captions below each post and in the lightbox, which can be crawled by search engines to help boost SEO.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=posts')
						),

						'lightbox' => array(
							'heading' => __('Upgrade to Pro to enable the popup Lightbox', 'instagram-feed'),
							'description' => __('Allow visitors to view your photos and videos in a beautiful full size lightbox, keeping them on your site for longer to discover more of your content.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=lightbox')
						),

						'filtermoderation' => array(
							'heading' => __('Get Advanced Moderation and Filters with Pro', 'instagram-feed'),
							'description' => __('Use powerful moderation tools to create feeds of only specific chosen posts, or exclude specific chosen posts. You can also automatically include or exclude posts based on a word or hashtag found in the caption.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=filters')
						),

						'shoppablefeed' => array(
							'heading' => __('Upgrade to Pro to Get Shoppable Feeds', 'instagram-feed'),
							'description' => __('Automatically link Instagram posts to custom URLs of your choosing by adding the URL in the caption, or manually add links to specific pages or products on your site (or other sites) in a quick and easy way.', 'instagram-feed'),
							'bullets' => array(
								'heading' => __('And get much more!', 'instagram-feed'),
								'content' => array(
									__('Display Hashtag & Tagged feeds', 'instagram-feed'),
									__('Powerful visual moderation', 'instagram-feed'),
									__('Comments and Likes', 'instagram-feed'),
									__('Highlight specific posts', 'instagram-feed'),
									__('Multiple layout options', 'instagram-feed'),
									__('Popup photo/video lightbox', 'instagram-feed'),
									__('Instagram Stories', 'instagram-feed'),
									__('Shoppable feeds', 'instagram-feed'),
									__('Pro support', 'instagram-feed'),
									__('Post captions', 'instagram-feed'),
									__('Combine multiple feed types', 'instagram-feed'),
									__('30 day money back guarantee', 'instagram-feed'),
								)
							),
							'buyUrl' => sprintf('https://smashballoon.com/instagram-feed/demo/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=shoppable')
						),
					),
					'personalAccountScreen' => self::personal_account_screen_text(),
					'onboardingWizardContent' => SBI_Onboarding_wizard::get_onboarding_wizard_content()

				);

				if ($newly_retrieved_source_connection_data) {
					$sbi_builder['newSourceData'] = $newly_retrieved_source_connection_data;
				}

				if (isset($_GET['manualsource']) && $_GET['manualsource']) {
					$sbi_builder['manualSourcePopupInit'] = true;
				}

				$maybe_feed_customizer_data = SBI_Feed_Saver_Manager::maybe_feed_customizer_data();

				if ($maybe_feed_customizer_data) {
					sb_instagram_scripts_enqueue(true);
					$sbi_builder['customizerFeedData'] = $maybe_feed_customizer_data;
					$sbi_builder['customizerSidebarBuilder'] = SBI_Builder_Customizer_Tab::get_customizer_tabs();
					$sbi_builder['wordpressPageLists'] = $this->get_wp_pages();
					$sbi_builder['instagram_feed_dismiss_lite'] = get_transient('instagram_feed_dismiss_lite');

					if (!isset($_GET['feed_id']) || $_GET['feed_id'] === 'legacy') {
						$feed_id = 'legacy';
						$customizer_atts = array(
							'feed' => 'legacy',
							'customizer' => true
						);
					} elseif (intval($_GET['feed_id']) > 0) {
						$feed_id = intval($_GET['feed_id']);
						$customizer_atts = array(
							'feed' => $feed_id,
							'customizer' => true
						);
					}

					if (!empty($feed_id)) {
						$settings_preview = self::add_customizer_att($customizer_atts);
						if ($feed_id === 'legacy') {
							$preview_settings = SB_Instagram_Settings::get_legacy_feed_settings();
							$preview_settings['customizer'] = true;
							$sbi_builder['feedInitOutput'] = htmlspecialchars(display_instagram($customizer_atts, $preview_settings));
						} else {
							$sbi_builder['feedInitOutput'] = htmlspecialchars(display_instagram($settings_preview, true));
						}
					}

					// Date
					global $wp_locale;
					wp_enqueue_script(
						'sbi-date_i18n',
						SBI_PLUGIN_URL . 'admin/builder/assets/js/date_i18n.js',
						null,
						SBIVER,
						true
					);

					$monthNames = array_map(
						array(&$wp_locale, 'get_month'),
						range(1, 12)
					);
					$monthNamesShort = array_map(
						array(&$wp_locale, 'get_month_abbrev'),
						$monthNames
					);
					$dayNames = array_map(
						array(&$wp_locale, 'get_weekday'),
						range(0, 6)
					);
					$dayNamesShort = array_map(
						array(&$wp_locale, 'get_weekday_abbrev'),
						$dayNames
					);
					wp_localize_script(
						'sbi-date_i18n',
						'DATE_I18N',
						array(
							'month_names' => $monthNames,
							'month_names_short' => $monthNamesShort,
							'day_names' => $dayNames,
							'day_names_short' => $dayNamesShort
						)
					);
				}

				wp_enqueue_style(
					'sbi-builder-style',
					SBI_PLUGIN_URL . 'admin/builder/assets/css/builder.css',
					false,
					SBIVER
				);

				self::global_enqueue_ressources_scripts();

				wp_register_script('feed-builder-svgs', SBI_PLUGIN_URL . 'assets/svgs/svgs.js');

				wp_enqueue_script(
					'sbi-builder-app',
					SBI_PLUGIN_URL . 'admin/builder/assets/js/builder.js',
					array('feed-builder-svgs'),
					SBIVER,
					true
				);
				// Customize screens
				$sbi_builder['customizeScreens'] = $this->get_customize_screens_text();
				wp_localize_script(
					'sbi-builder-app',
					'sbi_builder',
					$sbi_builder
				);
				wp_enqueue_media();
			endif;
		endif;
	}

	/**
	 * Get Generic text
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_generic_text()
	{
		return array(
			'done' => __('Done', 'instagram-feed'),
			'title' => __('Settings', 'instagram-feed'),
			'dashboard' => __('Dashboard', 'instagram-feed'),
			'setup' => __('Setup', 'instagram-feed'),
			'addNew' => __('Add New', 'instagram-feed'),
			'addSource' => __('Add Source', 'instagram-feed'),
			'addAnotherSource' => __('Add another Source', 'instagram-feed'),
			'addSourceType' => __('Add Another Source Type', 'instagram-feed'),
			'previous' => __('Previous', 'instagram-feed'),
			'next' => __('Next', 'instagram-feed'),
			'finish' => __('Finish', 'instagram-feed'),
			'new' => __('New', 'instagram-feed'),
			'update' => __('Update', 'instagram-feed'),
			'upgrade' => __('Try the Pro Demo', 'instagram-feed'),
			'settings' => __('Settings', 'instagram-feed'),
			'back' => __('Back', 'instagram-feed'),
			'backAllFeeds' => __('Back to all feeds', 'instagram-feed'),
			'createFeed' => __('Create Feed', 'instagram-feed'),
			'add' => __('Add', 'instagram-feed'),
			'change' => __('Change', 'instagram-feed'),
			'getExtention' => __('Get Extension', 'instagram-feed'),
			'viewDemo' => __('View Demo', 'instagram-feed'),
			'includes' => __('Includes', 'instagram-feed'),
			'photos' => __('Photos', 'instagram-feed'),
			'photo' => __('Photo', 'instagram-feed'),
			'apply' => __('Apply', 'instagram-feed'),
			'copy' => __('Copy', 'instagram-feed'),
			'edit' => __('Edit', 'instagram-feed'),
			'duplicate' => __('Duplicate', 'instagram-feed'),
			'delete' => __('Delete', 'instagram-feed'),
			'remove' => __('Remove', 'instagram-feed'),
			'removeSource' => __('Remove Source', 'instagram-feed'),
			'shortcode' => __('Shortcode', 'instagram-feed'),
			'clickViewInstances' => __('Click to view Instances', 'instagram-feed'),
			'usedIn' => __('Used in', 'instagram-feed'),
			'place' => __('place', 'instagram-feed'),
			'places' => __('places', 'instagram-feed'),
			'item' => __('Item', 'instagram-feed'),
			'items' => __('Items', 'instagram-feed'),
			'learnMore' => __('Learn More', 'instagram-feed'),
			'location' => __('Location', 'instagram-feed'),
			'page' => __('Page', 'instagram-feed'),
			'copiedClipboard' => __('Copied to Clipboard', 'instagram-feed'),
			'feedImported' => __('Feed imported successfully', 'instagram-feed'),
			'failedToImportFeed' => __('Failed to import feed', 'instagram-feed'),
			'timeline' => __('Timeline', 'instagram-feed'),
			'help' => __('Help', 'instagram-feed'),
			'admin' => __('Admin', 'instagram-feed'),
			'member' => __('Member', 'instagram-feed'),
			'reset' => __('Reset', 'instagram-feed'),
			'preview' => __('Preview', 'instagram-feed'),
			'name' => __('Name', 'instagram-feed'),
			'id' => __('ID', 'instagram-feed'),
			'token' => __('Token', 'instagram-feed'),
			'confirm' => __('Confirm', 'instagram-feed'),
			'cancel' => __('Cancel', 'instagram-feed'),
			'clear' => __('Clear', 'instagram-feed'),
			'clearFeedCache' => __('Clear Feed Cache', 'instagram-feed'),
			'saveSettings' => __('Save Changes', 'instagram-feed'),
			'feedName' => __('Feed Name', 'instagram-feed'),
			'shortcodeText' => __('Shortcode', 'instagram-feed'),
			'general' => __('General', 'instagram-feed'),
			'feeds' => __('Feeds', 'instagram-feed'),
			'translation' => __('Translation', 'instagram-feed'),
			'advanced' => __('Advanced', 'instagram-feed'),
			'error' => __('Error:', 'instagram-feed'),
			'errorNotice' => __('There was an error when trying to connect to Instagram.', 'instagram-feed'),
			'errorDirections' => '<a href="https://smashballoon.com/instagram-feed/docs/errors/" target="_blank" rel="noopener">' . __('Directions on How to Resolve This Issue', 'instagram-feed') . '</a>',
			'dbErrorNotice' => __('There was an error when trying to update the database.', 'instagram-feed'),
			'errorSource' => __('Source Invalid', 'instagram-feed'),
			'errorEncryption' => __('Encryption Error', 'instagram-feed'),
			'invalid' => __('Invalid', 'instagram-feed'),
			'reconnect' => __('Reconnect', 'instagram-feed'),
			'feed' => __('feed', 'instagram-feed'),
			'sourceNotUsedYet' => __('Source is not used yet', 'instagram-feed'),
			'addImage' => __('Add Image', 'instagram-feed'),
			'businessRequired' => __('Business Account required', 'instagram-feed'),
			'selectedPost' => __('Selected Post', 'instagram-feed'),
			'productLink' => __('Product Link', 'instagram-feed'),
			'enterProductLink' => __('Add your product URL here', 'instagram-feed'),
			'editSources' => __('Edit Sources', 'instagram-feed'),
			'moderateFeed' => __('Moderate your feed', 'instagram-feed'),
			'moderateFeedSaveExit' => __('Save and Exit', 'instagram-feed'),
			'moderationMode' => __('Moderation Mode', 'instagram-feed'),
			'moderationModeEnterPostId' => __('Or Enter Post IDs to hide manually', 'instagram-feed'),
			'moderationModeTextareaPlaceholder' => __('Add words here to hide any posts containing these words', 'instagram-feed'),
			'filtersAndModeration' => __('Filters & Moderation', 'instagram-feed'),
			'topRated' => __('Top Rated', 'instagram-feed'),
			'mostRecent' => __('Most recent', 'instagram-feed'),
			'moderationModePreview' => __('Moderation Mode Preview', 'instagram-feed'),
			'exitSetup' => __('Exit Setup', 'instagram-feed'),
			'notification' => array(
				'feedSaved' => array(
					'type' => 'success',
					'text' => __('Feed saved successfully', 'instagram-feed')
				),
				'feedSavedError' => array(
					'type' => 'error',
					'text' => __('Error saving Feed', 'instagram-feed')
				),
				'previewUpdated' => array(
					'type' => 'success',
					'text' => __('Preview updated successfully', 'instagram-feed')
				),
				'carouselLayoutUpdated' => array(
					'type' => 'success',
					'text' => __('Carousel updated successfully', 'instagram-feed')
				),
				'unkownError' => array(
					'type' => 'error',
					'text' => __('Unknown error occurred', 'instagram-feed')
				),
				'cacheCleared' => array(
					'type' => 'success',
					'text' => __('Feed cache cleared', 'instagram-feed')
				),
				'selectSourceError' => array(
					'type' => 'error',
					'text' => __('Please select a source for your feed', 'instagram-feed')
				),
				'commentCacheCleared' => array(
					'type' => 'success',
					'text' => __('Comment cache cleared', 'instagram-feed')
				),
				'personalAccountUpdated' => array(
					'type' => 'success',
					'text' => __('Personal account updated', 'instagram-feed')
				)
			),
			'install' => __('Install', 'instagram-feed'),
			'installed' => __('Installed', 'instagram-feed'),
			'activate' => __('Activate', 'instagram-feed'),
			'installedAndActivated' => __('Installed & Activated', 'instagram-feed'),
			'free' => __('Free', 'instagram-feed'),
			'invalidLicenseKey' => __('Invalid license key', 'instagram-feed'),
			'licenseActivated' => __('License activated', 'instagram-feed'),
			'licenseDeactivated' => __('License Deactivated', 'instagram-feed'),
			'carouselLayoutUpdated' => array(
				'type' => 'success',
				'text' => __('Carousel Layout updated', 'instagram-feed')
			),
			'getMoreFeatures' => __('Get more features with Instagram Feed Pro', 'instagram-feed'),
			'liteFeedUsers' => __('Lite users get 50% OFF', 'instagram-feed'),
			'liteFeedUsersAutoApply' => __('Lite Feed Users get a 50% OFF (auto-applied at checkout)', 'instagram-feed'),
			'liteFeedUsersSimpleText' => __('Lite Feed Users get a 50% OFF', 'instagram-feed'),
			'liteFeedUsersAutoCheckout' => __('auto-applied at checkout', 'instagram-feed'),
			'tryDemo' => __('Try Demo', 'instagram-feed'),

			'displayImagesVideos' => __('Display images and videos in posts', 'instagram-feed'),
			'viewLikesShares' => __('View likes, shares and comments', 'instagram-feed'),
			'allFeedTypes' => __('All Feed Types: Photos, Albums, Events and more', 'instagram-feed'),
			'abilityToLoad' => __('Ability to “Load More” posts', 'instagram-feed'),

			'ctaHashtag' => __('Display Hashtag Feeds', 'instagram-feed'),
			'ctaLayout' => __('Carousel, Masonry, & Highlight layouts', 'instagram-feed'),
			'ctaPopups' => __('View posts in a pop-up lightbox', 'instagram-feed'),
			'ctaFilter' => __('Powerful post filtering and moderation', 'instagram-feed'),

			'andMuchMore' => __('And Much More!', 'instagram-feed'),
			'sbiFreeCTAFeatures' => array(
				__('Create shoppable feeds', 'instagram-feed'),
				__('Combine multiple feed types', 'instagram-feed'),
				__('Display likes, captions & comments', 'instagram-feed'),
				__('Instagram Stories', 'instagram-feed'),
				__('Play videos in your feed', 'instagram-feed'),
				__('Highlight specific posts', 'instagram-feed'),
				__('Display tagged posts', 'instagram-feed'),
				__('30 day money back guarantee', 'instagram-feed'),
				__('Fast, friendly, and effective support', 'instagram-feed'),
			),
			'ctaShowFeatures' => __('Show Features', 'instagram-feed'),
			'ctaHideFeatures' => __('Hide Features', 'instagram-feed'),
			'upgradeToPro' => __('Upgrade to Pro', 'instagram-feed'),
			'redirectLoading' => array(
				'heading' => __('Redirecting to connect.smashballoon.com', 'instagram-feed'),
				'description' => __('You will be redirected to our app so you can connect your account in 5 seconds', 'instagram-feed'),
			),
			'addAccountInfo' => __('Add Avatar and Bio', 'instagram-feed'),
			'updateAccountInfo' => __('Update Avatar and Bio', 'instagram-feed'),
			'personalAccountUpdated' => __('Personal account updated', 'instagram-feed'),
			'accountTypeInfo' => __('Action required. Reconnect as a business account. <strong><u>Why?</u></strong>', 'instagram-feed'),
			'accountTypeNotice' => __('As of December 2024, the personal account connection type will no longer work. Reconnect as a business account to continue using the plugin.', 'instagram-feed'),
		);
	}

	public static function get_social_wall_links()
	{
		return array(
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-feed-builder')) . '">' . __('All Feeds', 'instagram-feed') . '</a>',
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-settings')) . '">' . __('Settings', 'instagram-feed') . '</a>',
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-oembeds-manager')) . '">' . __('oEmbeds', 'instagram-feed') . '</a>',
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-extensions-manager')) . '">' . __('Extensions', 'instagram-feed') . '</a>',
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-about-us')) . '">' . __('About Us', 'instagram-feed') . '</a>',
			'<a href="' . esc_url(admin_url('admin.php?page=sbi-support')) . '">' . __('Support', 'instagram-feed') . '</a>',
		);
	}

	/**
	 * Text specific to onboarding. Will return an associative array 'active' => false
	 * if onboarding has been dismissed for the user or there aren't any legacy feeds.
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_onboarding_text()
	{
		// TODO: return if no legacy feeds
		$sbi_statuses_option = get_option('sbi_statuses', array());

		if (!isset($sbi_statuses_option['legacy_onboarding'])) {
			return array('active' => false);
		}

		if (
			$sbi_statuses_option['legacy_onboarding']['active'] === false
			|| self::onboarding_status() === 'dismissed'
		) {
			return array('active' => false);
		}

		$type = $sbi_statuses_option['legacy_onboarding']['type'];

		$text = array(
			'active' => true,
			'type' => $type,
			'legacyFeeds' => array(
				'heading' => __('Legacy Feed Settings', 'instagram-feed'),
				'description' => sprintf(__('These settings will impact %1$s legacy feeds on your site. You can learn more about what legacy feeds are and how they differ from new feeds %2$shere%3$s.', 'instagram-feed'), '<span class="cff-fb-count-placeholder"></span>', '<a href="https://smashballoon.com/doc/instagram-legacy-feeds/" target="_blank" rel="noopener">', '</a>'),
			),
			'getStarted' => __('You can now create and customize feeds individually. Click "Add New" to get started.', 'instagram-feed'),
		);

		if ($type === 'single') {
			$text['tooltips'] = array(
				array(
					'step' => 1,
					'heading' => __('How you create a feed has changed', 'instagram-feed'),
					'p' => __('You can now create and customize feeds individually without using shortcode options.', 'instagram-feed') . ' ' . __('Click "Add New" to get started.', 'instagram-feed'),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __('Your existing feed is here', 'instagram-feed'),
					'p' => __('You can edit your existing feed from here, and all changes will only apply to this feed.', 'instagram-feed'),
					'pointer' => 'top'
				)
			);
		} else {
			$text['tooltips'] = array(
				array(
					'step' => 1,
					'heading' => __('How you create a feed has changed', 'instagram-feed'),
					'p' => __('You can now create and customize feeds individually without using shortcode options.', 'instagram-feed') . ' ' . __('Click "Add New" to get started.', 'instagram-feed'),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __('Your existing feeds are under "Legacy" feeds', 'instagram-feed'),
					'p' => __('You can edit the settings for any existing "legacy" feed (i.e. any feed created prior to this update) here.', 'instagram-feed') . ' ' . __('This works just like the old settings page and affects all legacy feeds on your site.', 'instagram-feed')
				),
				array(
					'step' => 3,
					'heading' => __('Existing feeds work as normal', 'instagram-feed'),
					'p' => __('You don\'t need to update or change any of your existing feeds. They will continue to work as usual.', 'instagram-feed') . ' ' . __('This update only affects how new feeds are created and customized.', 'instagram-feed')
				)
			);
		}

		return $text;
	}

	/**
	 * Status of the onboarding sequence for specific user
	 *
	 * @return string|boolean
	 *
	 * @since 6.0
	 */
	public static function onboarding_status($type = 'newuser')
	{
		$onboarding_statuses = get_user_meta(get_current_user_id(), 'sbi_onboarding', true);
		$status = false;
		if (!empty($onboarding_statuses)) {
			$statuses = Util::safe_unserialize($onboarding_statuses);
			$status = isset($statuses[$type]) ? $statuses[$type] : false;
		}

		return $status;
	}

	/**
	 * Get Links with UTM
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function get_links_with_utm()
	{
		$license_key = null;
		if (get_option('sbi_license_key')) {
			$license_key = get_option('sbi_license_key');
		}
		$all_access_bundle = sprintf('https://smashballoon.com/all-access/?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=all-feeds&utm_medium=footer-banner&utm_content=learn-more', $license_key);
		$all_access_bundle_popup = sprintf('https://smashballoon.com/all-access/?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=balloon&utm_medium=all-access', $license_key);
		$sourceCombineCTA = sprintf('https://smashballoon.com/social-wall/?license_key=%s&upgrade=true&utm_campaign=instagram-free&utm_source=customizer&utm_medium=sources&utm_content=social-wall', $license_key);

		return array(
			'allAccessBundle' => $all_access_bundle,
			'popup' => array(
				'allAccessBundle' => $all_access_bundle_popup,
				'fbProfile' => 'https://www.facebook.com/SmashBalloon/',
				'twitterProfile' => 'https://twitter.com/smashballoon',
			),
			'sourceCombineCTA' => $sourceCombineCTA,
			'multifeedCTA' => 'https://smashballoon.com/extensions/multifeed/?utm_campaign=instagram-free&utm_source=customizer&utm_medium=sources&utm_content=multifeed',
			'doc' => 'https://smashballoon.com/docs/instagram/?utm_campaign=instagram-free&utm_source=support&utm_medium=view-documentation-button&utm_content=view-documentation',
			'blog' => 'https://smashballoon.com/blog/?utm_campaign=instagram-free&utm_source=support&utm_medium=view-blog-button&utm_content=view-blog',
			'gettingStarted' => 'https://smashballoon.com/docs/getting-started/?instagram&utm_campaign=instagram-free&utm_source=support&utm_medium=getting-started-button&utm_content=getting-started',
		);
	}

	/**
	 * Select Source Screen Text
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public static function select_source_screen_text()
	{
		return array(
			'mainHeading' => __('Select one or more sources', 'instagram-feed'),
			'description' => __('Sources are Instagram accounts your feed will display content from', 'instagram-feed'),
			'emptySourceDescription' => __('Looks like you have not added any source.<br/>Use “Add Source” to add a new one.', 'instagram-feed'),
			'mainHashtagHeading' => __('Enter Public Hashtags', 'instagram-feed'),
			'hashtagDescription' => __('Add one or more hashtag separated by comma', 'instagram-feed'),
			'hashtagGetBy' => __('Fetch posts that are', 'instagram-feed'),

			'sourcesListPopup' => array(
				'user' => array(
					'mainHeading' => __('Add a source for Timeline', 'instagram-feed'),
					'description' => __('Select or add an account you want to display the timeline for', 'instagram-feed'),
				),
				'tagged' => array(
					'mainHeading' => __('Add a source for Mentions', 'instagram-feed'),
					'description' => __('Select or add an account you want to display the mentions for', 'instagram-feed'),
				)
			),

			'perosnalAccountToolTipTxt' => array(
				__(
					'Due to changes in Instagram’s new API, we can no<br/>
					longer get mentions for personal accounts. To<br/>
					enable this for your account, you will need to convert it to<br/>
					a Business account. Learn More',
					'instagram-feed'
				),
			),
			'groupsToolTip' => array(
				__('Due to Facebook limitations, it\'s not possible to display photo feeds from a Group, only a Page.', 'instagram-feed')
			),
			'updateHeading' => __('Update Source', 'instagram-feed'),
			'updateDescription' => __('Select a source from your connected Facebook Pages and Groups. Or, use "Add New" to connect a new one.', 'instagram-feed'),
			'updateFooter' => __('Add multiple Facebook Pages or Groups to a feed with our Multifeed extension', 'instagram-feed'),
			'noSources' => __('Please add a source in order to display a feed. Go to the "Settings" tab -> "Sources" section -> Click "Add New" to connect a source.', 'instagram-feed'),

			'multipleTypes' => array(
				'user' => array(
					'heading' => __('User Timeline', 'instagram-feed'),
					'icon' => 'user',
					'description' => __('Connect an account to show posts for it.', 'instagram-feed'),
					'actionType' => 'addSource'
				),
				'hashtag' => array(
					'heading' => __('Hashtag', 'instagram-feed'),
					'icon' => 'hashtag',
					'description' => __('Add one or more hashtag separated by comma.', 'instagram-feed'),
					'businessRequired' => true,
					'actionType' => 'inputHashtags'
				),
				'tagged' => array(
					'heading' => __('Tagged', 'instagram-feed'),
					'icon' => 'mention',
					'description' => __('Connect an account to show tagged posts. This does not give us any permission to manage your Instagram account.', 'instagram-feed'),
					'businessRequired' => true,
					'actionType' => 'addSource'
				)
			),

			'modal' => array(
				'addNew' => __('Connect your Instagram Account', 'instagram-feed'),
				'selectSourceType' => __('Select Account Type', 'instagram-feed'),
				'connectAccount' => __('Connect an Instagram Account', 'instagram-feed'),
				'connectAccountDescription' => __('This does not give us permission to manage your Instagram account, it simply allows the plugin to see a list of them and retrieve their public content from the API.', 'instagram-feed'),
				'connect' => __('Connect', 'instagram-feed'),
				'alreadyHave' => __('Already have a API Token and Access Key for your account?', 'instagram-feed'),
				'addManuallyLink' => __('Add Account Manually', 'instagram-feed'),
				'selectAccount' => __('Select an Instagram Account', 'instagram-feed'),
				'showing' => __('Showing', 'instagram-feed'),
				'facebook' => __('Facebook', 'instagram-feed'),
				'businesses' => __('Businesses', 'instagram-feed'),
				'groups' => __('Groups', 'instagram-feed'),
				'connectedTo' => __('connected to', 'instagram-feed'),
				'addManually' => __('Add a Source Manually', 'instagram-feed'),
				'addSource' => __('Add Source', 'instagram-feed'),
				'sourceType' => __('Source Type', 'instagram-feed'),
				'accountID' => __('Instagram Account ID', 'instagram-feed'),
				'fAccountID' => __('Instagram Account ID', 'instagram-feed'),
				'eventAccessToken' => __('Event Access Token', 'instagram-feed'),
				'enterID' => __('Enter ID', 'instagram-feed'),
				'accessToken' => __('Instagram Access Token', 'instagram-feed'),
				'enterToken' => __('Enter Token', 'instagram-feed'),
				'addApp' => __('Add Instagram App to your group', 'instagram-feed'),
				'addAppDetails' => __('To get posts from your group, Instagram requires the "Smash Balloon Plugin" app to be added in your group settings. Just follow the directions here:', 'instagram-feed'),
				'addAppSteps' => array(
					__('Go to your group settings page by ', 'instagram-feed'),
					sprintf(__('Search for "Smash Balloon" and select our app %1$s(see screenshot)%2$s', 'instagram-feed'), '<a href="JavaScript:void(0);" id="sbi-group-app-tooltip">', '<img class="sbi-group-app-screenshot sb-tr-1" src="' . trailingslashit(SBI_PLUGIN_URL) . 'admin/assets/img/group-app.png" alt="Thumbnail Layout"></a>'),
					__('Click "Add" and you are done.', 'instagram-feed')
				),
				'alreadyExists' => __('Account already exists', 'instagram-feed'),
				'alreadyExistsExplanation' => __('The Instagram account you added is already connected as a “Business” account. Would you like to replace it with a “Personal“ account? (Note: Personal accounts cannot be used to display Tagged or Hashtag feeds.)', 'instagram-feed'),
				'replaceWithPersonal' => __('Replace with Personal', 'instagram-feed'),
				'notAdmin' => __('For groups you are not an administrator of', 'instagram-feed'),
				'disclaimerMentions' => __('Due to Instagram’s limitations, you need to connect a business account to display a Mentions timeline', 'instagram-feed'),
				'disclaimerHashtag' => __('Due to Instagram’s limitations, you need to connect a business account to display a Hashtag feed', 'instagram-feed'),
				'notSureToolTip' => __('Select "Personal" if displaying a regular feed of posts, as this can display feeds from either a Personal or Business account. For displaying a Hashtag or Tagged feed, you must have an Instagram Business account. If needed, you can convert a Personal account into a Business account by following the directions {link}here{link}.', 'instagram-feed')
			),
			'footer' => array(
				'heading' => __('Add feeds for popular social platforms with <span>our other plugins</span>', 'instagram-feed'),
			),
			'personal' => __('Personal', 'instagram-feed'),
			'business' => __('Business', 'instagram-feed'),
			'notSure' => __("I'm not sure", 'instagram-feed'),
		);
	}

	/**
	 * For types listed on the top of the select feed type screen
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_feed_types()
	{
		return array(
			array(
				'type' => 'user',
				'title' => __('User Timeline', 'instagram-feed'),
				'description' => __('Fetch posts from your Instagram profile', 'instagram-feed'),
				'icon' => 'usertimelineIcon'
			),
			array(
				'type' => 'hashtag',
				'title' => __('Public Hashtag', 'instagram-feed'),
				'description' => __('Fetch posts from a public Instagram hashtag', 'instagram-feed'),
				'tooltip' => __('Hashtag feeds require a connected Instagram business account', 'instagram-feed'),
				'businessRequired' => true,
				'icon' => 'publichashtagIcon'
			),
			array(
				'type' => 'tagged',
				'title' => __('Tagged Posts', 'instagram-feed'),
				'description' => __('Display posts your Instagram account has been tagged in', 'instagram-feed'),
				'tooltip' => __('Tagged posts feeds require a connected Instagram business account', 'instagram-feed'),
				'businessRequired' => true,
				'icon' => 'taggedpostsIcon'
			),
			array(
				'type' => 'socialwall',
				'title' => __('Social Wall', 'instagram-feed'),
				'description' => __('Create a feed with sources from different social platforms', 'instagram-feed'),
				'icon' => 'socialwall1Icon'
			)
		);
	}

	/**
	 * For types listed on the bottom of the select feed type screen
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_advanced_feed_types()
	{
		return array(
			array(
				'type' => 'hashtag',
				'title' => __('Public Hashtag', 'instagram-feed'),
				'description' => __('Fetch posts from a public Instagram hashtag', 'instagram-feed'),
				'tooltip' => __('Hashtag feeds require a connected Instagram business account', 'instagram-feed'),
				'businessRequired' => true,
				'icon' => 'publichashtagIcon'
			),
			array(
				'type' => 'tagged',
				'title' => __('Tagged Posts', 'instagram-feed'),
				'description' => __('Display posts your Instagram account has been tagged in', 'instagram-feed'),
				'tooltip' => __('Tagged posts feeds require a connected Instagram business account', 'instagram-feed'),
				'businessRequired' => true,
				'icon' => 'taggedpostsIcon'
			),
			array(
				'type' => 'socialwall',
				'title' => __('Social Wall', 'instagram-feed'),
				'description' => __('Create a feed with sources from different social platforms', 'instagram-feed'),
				'icon' => 'socialwall1Icon'
			),
		);
	}

	/**
	 * Gets a list of info
	 * Used in multiple places in the feed creator
	 * Other Platforms + Social Links
	 * Upgrade links
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_smashballoon_info()
	{
		return array(
			'colorSchemes' => array(
				'facebook' => '#006BFA',
				'twitter' => '#1B90EF',
				'instagram' => '#BA03A7',
				'youtube' => '#EB2121',
				'linkedin' => '#007bb6',
				'mail' => '#666',
				'smash' => '#EB2121'
			),
			'upgrade' => array(
				'name' => __('Upgrade to Pro', 'instagram-feed'),
				'icon' => 'instagram',
				'link' => 'https://smashballoon.com/instagram-feed/'
			),
			'platforms' => array(
				array(
					'name' => __('Facebook Feed', 'instagram-feed'),
					'icon' => 'facebook',
					'link' => 'https://smashballoon.com/custom-facebook-feed/?utm_campaign=instagram-free&utm_source=balloon&utm_medium=facebook'
				),
				array(
					'name' => __('Twitter Feed', 'instagram-feed'),
					'icon' => 'twitter',
					'link' => 'https://smashballoon.com/custom-twitter-feeds/?utm_campaign=instagram-free&utm_source=balloon&utm_medium=twitter'
				),
				array(
					'name' => __('YouTube Feed', 'instagram-feed'),
					'icon' => 'youtube',
					'link' => 'https://smashballoon.com/youtube-feed/?utm_campaign=instagram-free&utm_source=balloon&utm_medium=youtube'
				),
				array(
					'name' => __('Social Wall Plugin', 'instagram-feed'),
					'icon' => 'smash',
					'link' => 'https://smashballoon.com/social-wall/?utm_campaign=instagram-free&utm_source=balloon&utm_medium=social-wall ',
				)
			),
			'socialProfiles' => array(
				'facebook' => 'https://www.facebook.com/SmashBalloon/',
				'twitter' => 'https://twitter.com/smashballoon',
			),
			'morePlatforms' => array('instagram', 'youtube', 'twitter')
		);
	}

	/**
	 * Plugins information for plugin install modal in all feeds page on select source flow
	 *
	 * @return array
	 * @since 6.0
	 */
	public static function install_plugins_popup()
	{
		$active_sb_plugins = Util::get_sb_active_plugins_info();

		$return = array(
			'reviews' => array(
				'displayName' => __('Reviews', 'instagram-feed'),
				'name' => __('Reviews Feed', 'instagram-feed'),
				'author' => __('By Smash Balloon', 'instagram-feed'),
				'description' => __('To display a Reviews feed, our Reviews plugin is required. </br> Increase conversions and build positive brand trust through Google and Yelp reviews from your customers. Provide social proof needed to turn visitors into customers.', 'instagram-feed'),
				'dashboard_permalink' => admin_url('admin.php?page=sbr'),
				'svgIcon' => self::builder_svg_icons('install-plugins-popup.reviews'),
				'installed' => $active_sb_plugins['is_reviews_installed'],
				'activated' => is_plugin_active($active_sb_plugins['reviews_plugin']),
				'plugin' => $active_sb_plugins['reviews_plugin'],
				'download_plugin' => 'https://downloads.wordpress.org/plugin/reviews-feed.zip',
			),
			'facebook' => array(
				'displayName' => __('Facebook', 'instagram-feed'),
				'name' => __('Facebook Feed', 'instagram-feed'),
				'author' => __('By Smash Balloon', 'instagram-feed'),
				'description' => __('To display a Facebook feed, our Facebook plugin is required. </br> It provides a clean and beautiful way to add your Facebook posts to your website. Grab your visitors attention and keep them engaged with your site longer.', 'instagram-feed'),
				'dashboard_permalink' => admin_url('admin.php?page=cff-feed-builder'),
				'svgIcon' => self::builder_svg_icons('install-plugins-popup.facebook'),
				'installed' => $active_sb_plugins['is_facebook_installed'],
				'activated' => is_plugin_active($active_sb_plugins['facebook_plugin']),
				'plugin' => $active_sb_plugins['facebook_plugin'],
				'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip',
			),
			'twitter' => array(
				'displayName' => __('Twitter', 'instagram-feed'),
				'name' => __('Twitter Feed', 'instagram-feed'),
				'author' => __('By Smash Balloon', 'instagram-feed'),
				'description' => __('Custom Twitter Feeds is a highly customizable way to display tweets from your Twitter account. Promote your latest content and update your site content automatically.', 'instagram-feed'),
				'dashboard_permalink' => admin_url('admin.php?page=custom-twitter-feeds'),
				'svgIcon' => self::builder_svg_icons('install-plugins-popup.twitter'),
				'installed' => $active_sb_plugins['is_twitter_installed'],
				'activated' => is_plugin_active($active_sb_plugins['twitter_plugin']),
				'plugin' => $active_sb_plugins['twitter_plugin'],
				'download_plugin' => 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip',
			),
			'youtube' => array(
				'displayName' => __('YouTube', 'instagram-feed'),
				'name' => __('Feeds for YouTube', 'instagram-feed'),
				'author' => __('By Smash Balloon', 'instagram-feed'),
				'description' => __('To display a YouTube feed, our YouTube plugin is required. It provides a simple yet powerful way to display videos from YouTube on your website, Increasing engagement with your channel while keeping visitors on your website.', 'instagram-feed'),
				'dashboard_permalink' => admin_url('admin.php?page=youtube-feed'),
				'svgIcon' => self::builder_svg_icons('install-plugins-popup.youtube'),
				'installed' => $active_sb_plugins['is_youtube_installed'],
				'activated' => is_plugin_active($active_sb_plugins['youtube_plugin']),
				'plugin' => $active_sb_plugins['youtube_plugin'],
				'download_plugin' => 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip',
			),
			'tiktok' => array(
				'displayName' => __('TikTok', 'instagram-feed'),
				'name' => __('Feeds for TikTok', 'instagram-feed'),
				'author' => __('By Smash Balloon', 'instagram-feed'),
				'description' => __("To display a TikTok feed, our TikTok plugin is required. It allows you to seamlessly integrate your TikTok account’s videos into your WordPress website.", 'instagram-feed'),
				'dashboard_permalink' => admin_url('admin.php?page=sbtt'),
				'svgIcon' => self::builder_svg_icons('install-plugins-popup.tiktok'),
				'installed' => $active_sb_plugins['is_tiktok_installed'],
				'activated' => is_plugin_active($active_sb_plugins['tiktok_plugin']),
				'plugin' => $active_sb_plugins['tiktok_plugin'],
				'download_plugin' => "https://downloads.wordpress.org/plugin/feeds-for-tiktok.zip",
			)
		);

		if (version_compare(PHP_VERSION, '7.1.0') < 0) {
			$incompatible_plugins = array('reviews', 'tiktok');
			foreach ($incompatible_plugins as $plugin) {
				if (isset($return[$plugin])) {
					unset($return[$plugin]);
				}
			}
		}
		return $return;
	}

	/**
	 * For Other Platforms listed on the footer widget
	 *
	 * @return string
	 *
	 * @since 6.0
	 */
	public static function builder_svg_icons($icon = null)
	{
		// If the icon is set, load the SVG file and return it.
		if (!empty($icon)) {
			$icon_folder = explode('.', $icon);
			if (count($icon_folder) > 1) {
				$folder = $icon_folder[0];
				$icon = $icon_folder[1];
				$svg_path = SBI_PLUGIN_DIR . 'assets/svgs/' . $folder . '/' . $icon . '.svg';
			} else {
				$svg_path = SBI_PLUGIN_DIR . 'assets/svgs/' . $icon . '.svg';
			}
			if (is_file($svg_path)) {
				return file_get_contents($svg_path);
			}
		}

		return '';
	}

	/**
	 * Returns an associate array of all existing feeds along with their data
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_feed_list($feeds_args = array())
	{
		if (!empty($_GET['feed_id'])) {
			return array();
		}
		$feeds_data = SBI_Db::feeds_query($feeds_args);

		$i = 0;
		foreach ($feeds_data as $single_feed) {
			$args = array(
				'feed_id' => '*' . $single_feed['id'],
				'html_location' => array('content'),
			);
			$count = SB_Instagram_Feed_Locator::count($args);

			$content_locations = SB_Instagram_Feed_Locator::instagram_feed_locator_query($args);

			// if this is the last page, add in the header footer and sidebar locations
			if (count($content_locations) < SBI_Db::RESULTS_PER_PAGE) {
				$args = array(
					'feed_id' => '*' . $single_feed['id'],
					'html_location' => array('header', 'footer', 'sidebar'),
					'group_by' => 'html_location',
				);
				$other_locations = SB_Instagram_Feed_Locator::instagram_feed_locator_query($args);

				$locations = array();

				$combined_locations = array_merge($other_locations, $content_locations);
			} else {
				$combined_locations = $content_locations;
			}

			foreach ($combined_locations as $location) {
				$page_text = get_the_title($location['post_id']);
				if ($location['html_location'] === 'header') {
					$html_location = __('Header', 'instagram-feed');
				} elseif ($location['html_location'] === 'footer') {
					$html_location = __('Footer', 'instagram-feed');
				} elseif ($location['html_location'] === 'sidebar') {
					$html_location = __('Sidebar', 'instagram-feed');
				} else {
					$html_location = __('Content', 'instagram-feed');
				}
				$shortcode_atts = json_decode($location['shortcode_atts'], true);
				$shortcode_atts = is_array($shortcode_atts) ? $shortcode_atts : array();

				$full_shortcode_string = '[instagram-feed';
				foreach ($shortcode_atts as $key => $value) {
					if (!empty($value)) {
						$full_shortcode_string .= ' ' . esc_html($key) . '="' . esc_html($value) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = array(
					'link' => esc_url(get_the_permalink($location['post_id'])),
					'page_text' => $page_text,
					'html_location' => $html_location,
					'shortcode' => $full_shortcode_string
				);
			}
			$feeds_data[$i]['instance_count'] = $count;
			$feeds_data[$i]['location_summary'] = $locations;
			$settings = json_decode($feeds_data[$i]['settings'], true);

			$settings['feed'] = $single_feed['id'];

			$instagram_feed_settings = new SB_Instagram_Settings($settings, sbi_defaults());

			$feeds_data[$i]['settings'] = $instagram_feed_settings->get_settings();

			$i++;
		}
		return $feeds_data;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @param int $page
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public static function get_source_list($page = 1)
	{
		$args['page'] = $page;
		$source_data = SBI_Db::source_query($args);
		$encryption = new SB_Instagram_Data_Encryption();

		$return = array();
		foreach ($source_data as $source) {
			$info = !empty($source['info']) ? json_decode($encryption->decrypt($source['info']), true) : array();
			$source['header_data'] = $info;

			$settings = array('gdpr' => 'no');

			$avatar = SB_Instagram_Parse::get_avatar($info, $settings);

			if (SB_Instagram_Connected_Account::local_avatar_exists($source['username'])) {
				$source['local_avatar_url'] = SB_Instagram_Connected_Account::get_local_avatar_url($source['username']);
				$source['local_avatar'] = SB_Instagram_Connected_Account::get_local_avatar_url($source['username']);
			} else {
				$source['local_avatar'] = false;
			}

			$source['avatar_url'] = $avatar;
			$source['just_added'] = (!empty($_GET['sbi_username']) && isset($info['username']) && $info['username'] === $_GET['sbi_username']);
			$source['error_encryption'] = self::has_encryption_error($source, $encryption);
			$source['account_type_info'] = self::has_account_type_update_notice($source);

			$return[] = $source;
		}

		return $return;
	}

	private static function has_encryption_error($source, $encryption)
	{
		if (isset($source['access_token'])) {
			$token = $source['access_token'];
			return strpos($token, 'IG') === false && strpos($token, 'EA') === false && !$encryption->decrypt($token);
		}
		return false;
	}

	private static function has_account_type_update_notice($source)
	{
		if (isset($source['account_type']) && isset($source['header_data']['account_type'])) {
			$accountType = strtolower(trim($source['account_type']));
			$headerAccountType = strtolower(trim($source['header_data']['account_type']));

			$isPersonal = in_array($accountType, ['basic', 'personal']) && $headerAccountType === 'personal';
			$isProfessional = in_array($headerAccountType, ['business', 'media_creator']);
			$hasProfilePicture = isset($source['header_data']['profile_picture_url']) && !empty($source['header_data']['profile_picture_url']);

			return $isPersonal || ($isProfessional && !$hasProfilePicture);
		}
		return false;
	}

	/**
	 * Returns an associate array of all existing sources along with their data
	 *
	 * @return array
	 *
	 * @since 4.0
	 */
	public function get_legacy_feed_list()
	{
		if (!empty($_GET['feed_id'])) {
			return array();
		}
		$sbi_statuses = get_option('sbi_statuses', array());
		$sources_list = self::get_source_list();
		if (empty($sbi_statuses['support_legacy_shortcode'])) {
			return array();
		}

		$args = array(
			'html_location' => array('header', 'footer', 'sidebar', 'content'),
			'group_by' => 'shortcode_atts',
			'page' => 1
		);
		$feeds_data = SB_Instagram_Feed_Locator::legacy_instagram_feed_locator_query($args);
		if (empty($feeds_data)) {
			$args = array(
				'html_location' => array('header', 'footer', 'sidebar', 'content'),
				'group_by' => 'shortcode_atts',
				'page' => 1
			);
			$feeds_data = SB_Instagram_Feed_Locator::legacy_instagram_feed_locator_query($args);
		}

		$feed_saver = new SBI_Feed_Saver('legacy');
		$settings = $feed_saver->get_feed_settings();

		$default_type = 'timeline';

		if (isset($settings['feedtype'])) {
			$default_type = $settings['feedtype'];
		} elseif (isset($settings['type'])) {
			if (strpos($settings['type'], ',') === false) {
				$default_type = $settings['type'];
			}
		}
		$i = 0;
		$reindex = false;
		foreach ($feeds_data as $single_feed) {
			$args = array(
				'shortcode_atts' => $single_feed['shortcode_atts'],
				'html_location' => array('content'),
			);
			$content_locations = SB_Instagram_Feed_Locator::instagram_feed_locator_query($args);

			$count = SB_Instagram_Feed_Locator::count($args);
			if (count($content_locations) < SBI_Db::RESULTS_PER_PAGE) {
				$args = array(
					'feed_id' => $single_feed['feed_id'],
					'html_location' => array('header', 'footer', 'sidebar'),
					'group_by' => 'html_location'
				);
				$other_locations = SB_Instagram_Feed_Locator::instagram_feed_locator_query($args);

				$combined_locations = array_merge($other_locations, $content_locations);
			} else {
				$combined_locations = $content_locations;
			}

			$locations = array();
			foreach ($combined_locations as $location) {
				$page_text = get_the_title($location['post_id']);
				if ($location['html_location'] === 'header') {
					$html_location = __('Header', 'instagram-feed');
				} elseif ($location['html_location'] === 'footer') {
					$html_location = __('Footer', 'instagram-feed');
				} elseif ($location['html_location'] === 'sidebar') {
					$html_location = __('Sidebar', 'instagram-feed');
				} else {
					$html_location = __('Content', 'instagram-feed');
				}
				$shortcode_atts = json_decode($location['shortcode_atts'], true);
				$shortcode_atts = is_array($shortcode_atts) ? $shortcode_atts : array();

				$full_shortcode_string = '[instagram-feed';
				foreach ($shortcode_atts as $key => $value) {
					if (!empty($value)) {
						if (is_array($value)) {
							$value = implode(',', $value);
						}
						$full_shortcode_string .= ' ' . esc_html($key) . '="' . esc_html($value) . '"';
					}
				}
				$full_shortcode_string .= ']';

				$locations[] = array(
					'link' => esc_url(get_the_permalink($location['post_id'])),
					'page_text' => $page_text,
					'html_location' => $html_location,
					'shortcode' => $full_shortcode_string
				);
			}
			$shortcode_atts = json_decode($feeds_data[$i]['shortcode_atts'], true);
			$shortcode_atts = is_array($shortcode_atts) ? $shortcode_atts : array();

			$full_shortcode_string = '[instagram-feed';
			foreach ($shortcode_atts as $key => $value) {
				if (!empty($value)) {
					if (is_array($value)) {
						$value = implode(',', $value);
					}
					$full_shortcode_string .= ' ' . esc_html($key) . '="' . esc_html($value) . '"';
				}
			}
			$full_shortcode_string .= ']';

			$feeds_data[$i]['shortcode'] = $full_shortcode_string;
			$feeds_data[$i]['instance_count'] = $count;
			$feeds_data[$i]['location_summary'] = $locations;
			$feeds_data[$i]['feed_name'] = self::get_legacy_feed_name($sources_list, $feeds_data[$i]['feed_id']);
			$feeds_data[$i]['feed_type'] = $default_type;

			if (isset($shortcode_atts['feedtype'])) {
				$feeds_data[$i]['feed_type'] = $shortcode_atts['feedtype'];
			} elseif (isset($shortcode_atts['type'])) {
				if (strpos($shortcode_atts['type'], ',') === false) {
					$feeds_data[$i]['feed_type'] = $shortcode_atts['type'];
				}
			}

			if (isset($feeds_data[$i]['id'])) {
				unset($feeds_data[$i]['id']);
			}

			if (isset($feeds_data[$i]['html_location'])) {
				unset($feeds_data[$i]['html_location']);
			}

			if (isset($feeds_data[$i]['last_update'])) {
				unset($feeds_data[$i]['last_update']);
			}

			if (isset($feeds_data[$i]['post_id'])) {
				unset($feeds_data[$i]['post_id']);
			}

			if (!empty($shortcode_atts['feed'])) {
				$reindex = true;
				unset($feeds_data[$i]);
			}

			if (isset($feeds_data[$i]['shortcode_atts'])) {
				unset($feeds_data[$i]['shortcode_atts']);
			}

			$i++;
		}

		if ($reindex) {
			$feeds_data = array_values($feeds_data);
		}

		// if there were no feeds found in the locator table we still want the legacy settings to be available
		// if it appears as though they had used version 3.x or under at some point.
		if (
			empty($feeds_data)
			&& !is_array($sbi_statuses['support_legacy_shortcode'])
			&& ($sbi_statuses['support_legacy_shortcode'])
		) {
			$feeds_data = array(
				array(
					'feed_id' => __('Legacy Feed', 'instagram-feed') . ' ' . __('(unknown location)', 'instagram-feed'),
					'feed_name' => __('Legacy Feed', 'instagram-feed') . ' ' . __('(unknown location)', 'instagram-feed'),
					'shortcode' => '[instagram-feed]',
					'feed_type' => '',
					'instance_count' => false,
					'location_summary' => array()
				)
			);
		}

		return $feeds_data;
	}

	public static function get_legacy_feed_name($sources_list, $source_id)
	{
		foreach ($sources_list as $source) {
			if ($source['account_id'] == $source_id) {
				return $source['username'];
			}
		}
		return $source_id;
	}

	/**
	 * Personal Account
	 *
	 * @return array
	 *
	 * @since 6.0.8
	 */
	public static function personal_account_screen_text()
	{
		return array(
			'mainHeading1' => __('We’re almost there...', 'instagram-feed'),
			'mainHeading2' => __('Update Personal Account', 'instagram-feed'),
			'mainHeading3' => __('Add Instagram Profile Picture and Bio', 'instagram-feed'),
			'mainDescription' => __('Instagram does not provide us access to your profile picture or bio for personal accounts. Would you like to set up a custom profile photo and bio?.', 'instagram-feed'),
			'bioLabel' => __('Bio (140 Characters)', 'instagram-feed'),
			'bioPlaceholder' => __('Add your profile bio here', 'instagram-feed'),
			'confirmBtn' => __('Yes, let\'s do it', 'instagram-feed'),
			'cancelBtn' => __('No, maybe later', 'instagram-feed'),
			'uploadBtn' => __('Upload Profile Picture', 'instagram-feed')
		);
	}

	/**
	 * Get WP Pages List
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_wp_pages()
	{
		$pagesList = get_pages();
		$pagesResult = array();
		if (is_array($pagesList)) {
			foreach ($pagesList as $page) {
				array_push(
					$pagesResult,
					array(
						'id' => $page->ID,
						'title' => $page->post_title
					)
				);
			}
		}
		return $pagesResult;
	}

	public static function add_customizer_att($atts)
	{
		if (!is_array($atts)) {
			$atts = array();
		}
		$atts['feedtype'] = 'customizer';
		return $atts;
	}

	/**
	 * Global JS + CSS Files
	 *
	 * Shared JS + CSS ressources for the admin panel
	 *
	 * @since 6.0
	 */
	public static function global_enqueue_ressources_scripts($is_settings = false)
	{
		wp_enqueue_style(
			'feed-global-style',
			SBI_PLUGIN_URL . 'admin/builder/assets/css/global.css',
			false,
			SBIVER
		);

		wp_enqueue_script(
			'sb-vue',
			SBI_PLUGIN_URL . 'js/vue.min.js',
			null,
			'2.6.12',
			true
		);

		wp_enqueue_script(
			'feed-colorpicker-vue',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/vue-color.min.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'feed-builder-ressources',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/ressources.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'sb-dialog-box',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/confirm-dialog.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'install-plugin-popup',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/install-plugin-popup.js',
			null,
			SBIVER,
			true
		);

		wp_enqueue_script(
			'sb-add-source',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/add-source.js',
			null,
			SBIVER,
			true
		);

		$newly_retrieved_source_connection_data = SBI_Source::maybe_source_connection_data();
		$sbi_source = array(
			'sources' => self::get_source_list(),
			'sourceConnectionURLs' => SBI_Source::get_connection_urls($is_settings),
			'nonce' => wp_create_nonce('sbi-admin'),
		);
		if ($newly_retrieved_source_connection_data) {
			$sbi_source['newSourceData'] = $newly_retrieved_source_connection_data;
		}

		if (isset($_GET['manualsource']) && $_GET['manualsource']) {
			$sbi_source['manualSourcePopupInit'] = true;
		}

		wp_localize_script(
			'sb-add-source',
			'sbi_source',
			$sbi_source
		);

		wp_enqueue_script(
			'sb-personal-account',
			SBI_PLUGIN_URL . 'admin/builder/assets/js/personal-account.js',
			null,
			SBIVER,
			true
		);

		$sbi_personal_account = array(
			'personalAccountScreen' => self::personal_account_screen_text(),
			'nonce' => wp_create_nonce('sbi-admin'),
			'ajaxHandler' => admin_url('admin-ajax.php'),
		);

		wp_localize_script(
			'sb-personal-account',
			'sbi_personal_account',
			$sbi_personal_account
		);
	}

	/**
	 * Text related to the feed customizer
	 *
	 * @return array
	 *
	 * @since 6.0
	 */
	public function get_customize_screens_text()
	{
		$text = array(
			'common' => array(
				'preview' => __('Preview', 'instagram-feed'),
				'help' => __('Help', 'instagram-feed'),
				'embed' => __('Embed', 'instagram-feed'),
				'save' => __('Save', 'instagram-feed'),
				'sections' => __('Sections', 'instagram-feed'),
				'enable' => __('Enable', 'instagram-feed'),
				'background' => __('Background', 'instagram-feed'),
				'text' => __('Text', 'instagram-feed'),
				'inherit' => __('Inherit from Theme', 'instagram-feed'),
				'size' => __('Size', 'instagram-feed'),
				'color' => __('Color', 'instagram-feed'),
				'height' => __('Height', 'instagram-feed'),
				'placeholder' => __('Placeholder', 'instagram-feed'),
				'select' => __('Select', 'instagram-feed'),
				'enterText' => __('Enter Text', 'instagram-feed'),
				'hoverState' => __('Hover State', 'instagram-feed'),
				'sourceCombine' => __('Combine sources from multiple platforms using our Social Wall plugin', 'instagram-feed'),
			),

			'tabs' => array(
				'customize' => __('Customize', 'instagram-feed'),
				'settings' => __('Settings', 'instagram-feed'),
			),
			'overview' => array(
				'feedLayout' => __('Feed Layout', 'instagram-feed'),
				'colorScheme' => __('Color Scheme', 'instagram-feed'),
				'header' => __('Header', 'instagram-feed'),
				'posts' => __('Posts', 'instagram-feed'),
				'likeBox' => __('Like Box', 'instagram-feed'),
				'loadMore' => __('Load More Button', 'instagram-feed'),
			),
			'feedLayoutScreen' => array(
				'layout' => __('Layout', 'instagram-feed'),
				'list' => __('List', 'instagram-feed'),
				'grid' => __('Grid', 'instagram-feed'),
				'masonry' => __('Masonry', 'instagram-feed'),
				'carousel' => __('Carousel', 'instagram-feed'),
				'feedHeight' => __('Feed Height', 'instagram-feed'),
				'number' => __('Number of Posts', 'instagram-feed'),
				'columns' => __('Columns', 'instagram-feed'),
				'desktop' => __('Desktop', 'instagram-feed'),
				'tablet' => __('Tablet', 'instagram-feed'),
				'mobile' => __('Mobile', 'instagram-feed'),
				'bottomArea' => array(
					'heading' => __('Tweak Post Styles', 'instagram-feed'),
					'description' => __('Change post background, border radius, shadow etc.', 'instagram-feed'),
				)
			),
			'colorSchemeScreen' => array(
				'scheme' => __('Scheme', 'instagram-feed'),
				'light' => __('Light', 'instagram-feed'),
				'dark' => __('Dark', 'instagram-feed'),
				'custom' => __('Custom', 'instagram-feed'),
				'customPalette' => __('Custom Palette', 'instagram-feed'),
				'background2' => __('Background 2', 'instagram-feed'),
				'text2' => __('Text 2', 'instagram-feed'),
				'link' => __('Link', 'instagram-feed'),
				'bottomArea' => array(
					'heading' => __('Overrides', 'instagram-feed'),
					'description' => __('Colors that have been overridden from individual post element settings will not change. To change them, you will have to reset overrides.', 'instagram-feed'),
					'ctaButton' => __('Reset Overrides.', 'instagram-feed'),
				)
			),
			'shoppableFeedScreen' => array(
				'heading1' => __('Upgrade to Pro and make your Instagram Feed Shoppable', 'instagram-feed'),
				'description1' => __('This feature links the post to the one specified in your caption.<br/><br/>Don’t want to add links to the caption? You can add links manually to each post.<br/><br><br>', 'instagram-feed'),
				'heading2' => __('Tap “Add” or “Update” on an<br/>image to add/update it’s URL', 'instagram-feed'),

			)
		);

		$text['onboarding'] = $this->get_customizer_onboarding_text();

		return $text;
	}

	public function get_customizer_onboarding_text()
	{

		if (self::onboarding_status('customizer') === 'dismissed') {
			return array('active' => false);
		}

		return array(
			'active' => true,
			'type' => 'customizer',
			'tooltips' => array(
				array(
					'step' => 1,
					'heading' => __('Embedding a Feed', 'instagram-feed'),
					'p' => __('After you are done customizing the feed, click here to add it to a page or a widget.', 'instagram-feed'),
					'pointer' => 'top'
				),
				array(
					'step' => 2,
					'heading' => __('Customize', 'instagram-feed'),
					'p' => __('Change your feed layout, color scheme, or customize individual feed sections here.', 'instagram-feed'),
					'pointer' => 'top'
				),
				array(
					'step' => 3,
					'heading' => __('Settings', 'instagram-feed'),
					'p' => __('Update your feed source, filter your posts, or change advanced settings here.', 'instagram-feed'),
					'pointer' => 'top'
				)
			)
		);
	}

	/**
	 * Feed Builder Wrapper.
	 *
	 * @since 6.0
	 */
	public function feed_builder()
	{
		include_once SBI_BUILDER_DIR . 'templates/builder.php';
	}
}
