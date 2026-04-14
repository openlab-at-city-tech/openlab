<?php

/**
 * Onbaording Wizard
 *
 * @since 6.3
 */

namespace InstagramFeed\admin;

use InstagramFeed\Builder\SBI_Feed_Builder;
use InstagramFeed\Builder\SBI_Feed_Saver_Manager;
use InstagramFeed\Builder\SBI_Source;
use SB_Instagram_GDPR_Integrations;
use Sbi\Helpers\PluginSilentUpgrader;
use Sbi_Install_Skin;

if (!defined('ABSPATH')) {
	exit;
}

class SBI_Onboarding_wizard extends SBI_Feed_Builder
{
	static $plugin_name = 'instagram';
	static $current_version = SBI_DBVERSION;
	static $target_version = '2.1';
	static $statues_name = 'sbi_statuses';

	public function __construct()
	{
		$this->init();
	}

	/**
	 * Init Setup Dashboard.
	 *
	 * @since 6.0
	 */
	public function init()
	{
		if (is_admin()) {
			add_action('admin_menu', array($this, 'register_menu'));
			// add ajax listeners
			SBI_Feed_Saver_Manager::hooks();
			SBI_Source::hooks();
			self::hooks();
			$this->ajax_hooks();
		}
	}

	public function ajax_hooks()
	{
		add_action('wp_ajax_sbi_feed_saver_manager_process_wizard', array($this, 'process_wizard_data'));
		add_action('wp_ajax_sbi_feed_saver_manager_dismiss_wizard', array($this, 'dismiss_wizard'));
	}

	/**
	 * Onboarding Wizard Content & Steps
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_onboarding_wizard_content()
	{
		$active_gdpr_plugin = SB_Instagram_GDPR_Integrations::gdpr_plugins_active();
		$show_wpconsent = !$active_gdpr_plugin;

		$data = [
			'heading' => __('Smash Balloon', 'instagram-feed'),
			'subheading' => __('Instagram Feed by', 'instagram-feed'),
			'logo' => SBI_BUILDER_URL . 'assets/img/instagram.png',
			'balloon' => SBI_BUILDER_URL . 'assets/img/balloon.png',
			'balloon1' => SBI_BUILDER_URL . 'assets/img/balloon-1.png',
			'userIcon' => SBI_BUILDER_URL . 'assets/img/user.png',
			'saveSettings' => ['featuresList', 'pluginsList'],
			'successMessages' => [
				'connectAccount' => __('Connected an Instagram account', 'instagram-feed'),
				'setupFeatures' => __('Features were set up', 'instagram-feed'),
				'feedPlugins' => __('Feed plugins for # installed', 'instagram-feed')
			],
			'steps' => [
				[
					'id' => 'welcome',
					'template' => SBI_BUILDER_DIR . 'templates/onboarding/welcome.php',
					'heading' => __('Let\'s set up your plugin!', 'instagram-feed'),
					'description' => __('Ready to add a dash of Instagram to your website? Setting up your first feed is quick and easy. We\'ll get you up and running in no time.', 'instagram-feed'),
					'button' => __('Launch the Setup Wizard', 'instagram-feed'),
					'img' => SBI_BUILDER_URL . 'assets/img/waving-hand.png',
					'banner' => SBI_BUILDER_URL . 'assets/img/onboarding-banner.jpg',

				],
				[
					'id' => 'add-source',
					'template' => SBI_BUILDER_DIR . 'templates/onboarding/add-source.php',
					'heading' => __('Connect your Instagram Account', 'instagram-feed'),
					'smallHeading' => __('STEP 1', 'instagram-feed'),
				],
				[
					'id' => 'configure-features',
					'template' => SBI_BUILDER_DIR . 'templates/onboarding/configure-features.php',
					'heading' => __('Configure features', 'instagram-feed'),
					'smallHeading' => __('STEP 2', 'instagram-feed'),
					'featuresList' => [
						[
							'heading' => __('Instagram User Feed', 'instagram-feed'),
							'description' => __('Create and display Instagram feeds from connected accounts', 'instagram-feed'),
							'color' => 'green',
							'active' => true,
							'uncheck' => true,
							'icon' => '<svg width="24" height="32" viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 10.6094C9 10.6094 6.60938 13.0469 6.60938 16C6.60938 19 9 21.3906 12 21.3906C14.9531 21.3906 17.3906 19 17.3906 16C17.3906 13.0469 14.9531 10.6094 12 10.6094ZM12 19.5156C10.0781 19.5156 8.48438 17.9688 8.48438 16C8.48438 14.0781 10.0312 12.5312 12 12.5312C13.9219 12.5312 15.4688 14.0781 15.4688 16C15.4688 17.9688 13.9219 19.5156 12 19.5156ZM18.8438 10.4219C18.8438 9.71875 18.2812 9.15625 17.5781 9.15625C16.875 9.15625 16.3125 9.71875 16.3125 10.4219C16.3125 11.125 16.875 11.6875 17.5781 11.6875C18.2812 11.6875 18.8438 11.125 18.8438 10.4219ZM22.4062 11.6875C22.3125 10 21.9375 8.5 20.7188 7.28125C19.5 6.0625 18 5.6875 16.3125 5.59375C14.5781 5.5 9.375 5.5 7.64062 5.59375C5.95312 5.6875 4.5 6.0625 3.23438 7.28125C2.01562 8.5 1.64062 10 1.54688 11.6875C1.45312 13.4219 1.45312 18.625 1.54688 20.3594C1.64062 22.0469 2.01562 23.5 3.23438 24.7656C4.5 25.9844 5.95312 26.3594 7.64062 26.4531C9.375 26.5469 14.5781 26.5469 16.3125 26.4531C18 26.3594 19.5 25.9844 20.7188 24.7656C21.9375 23.5 22.3125 22.0469 22.4062 20.3594C22.5 18.625 22.5 13.4219 22.4062 11.6875ZM20.1562 22.1875C19.8281 23.125 19.0781 23.8281 18.1875 24.2031C16.7812 24.7656 13.5 24.625 12 24.625C10.4531 24.625 7.17188 24.7656 5.8125 24.2031C4.875 23.8281 4.17188 23.125 3.79688 22.1875C3.23438 20.8281 3.375 17.5469 3.375 16C3.375 14.5 3.23438 11.2188 3.79688 9.8125C4.17188 8.92188 4.875 8.21875 5.8125 7.84375C7.17188 7.28125 10.4531 7.42188 12 7.42188C13.5 7.42188 16.7812 7.28125 18.1875 7.84375C19.0781 8.17188 19.7812 8.92188 20.1562 9.8125C20.7188 11.2188 20.5781 14.5 20.5781 16C20.5781 17.5469 20.7188 20.8281 20.1562 22.1875Z" fill="#696D80"/></svg>'
						],
						[
							'data' => [
								'id' => 'enable_email_report',
								'type' => 'settings'
							],
							'heading' => __('Downtime Prevention', 'instagram-feed'),
							'description' => __('Prevent downtime in the event your feed is unable to update', 'instagram-feed'),
							'color' => 'green',
							'active' => true,
							'uncheck' => true,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_4085_38049)"><path d="M18.9999 16.9003C20.2151 16.6536 21.2952 15.9641 22.0306 14.9658C22.766 13.9674 23.1043 12.7315 22.9796 11.4978C22.855 10.2641 22.2765 9.12077 21.3563 8.28967C20.4361 7.45858 19.2399 6.99905 17.9999 7.0003H16.7399C16.4086 5.71762 15.764 4.53729 14.8638 3.56529C13.9637 2.59328 12.8363 1.86003 11.5828 1.43136C10.3292 1.0027 8.98891 0.892032 7.68207 1.10931C6.37523 1.32658 5.1428 1.865 4.09544 2.67621C3.04808 3.48742 2.21856 4.54604 1.68137 5.75701C1.14418 6.96799 0.916124 8.29341 1.01769 9.61429C1.11925 10.9352 1.54725 12.2102 2.26326 13.3248C2.97926 14.4394 3.96087 15.3587 5.11993 16.0003" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 11L9 17H15L11 23" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_4085_38049"><rect width="24" height="24" fill="white"/></clipPath></defs></svg>'
						],
						[
							'data' => [
								'id' => 'sb_instagram_disable_resize',
								'type' => 'settings'
							],
							'heading' => __('Image Optimization', 'instagram-feed'),
							'description' => __('Optimize and locally store feed images to improve search rankings and page speed', 'instagram-feed'),
							'color' => 'green',
							'active' => true,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 3H5C3.89543 3 3 3.89543 3 5V19C3 20.1046 3.89543 21 5 21H19C20.1046 21 21 20.1046 21 19V5C21 3.89543 20.1046 3 19 3Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.5 10C9.32843 10 10 9.32843 10 8.5C10 7.67157 9.32843 7 8.5 7C7.67157 7 7 7.67157 7 8.5C7 9.32843 7.67157 10 8.5 10Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M21 15L16 10L5 21" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						]

					],
					'proFeaturesList' => [
						[
							'heading' => __('Hashtag Feeds', 'instagram-feed'),
							'description' => __('Display Instagram posts that have a particular hashtag', 'instagram-feed'),
							'uncheck' => true,
							'active' => false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M4 9H20" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 15H20" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M10 3L8 21" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 3L14 21" stroke="#8C8F9A" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
						[
							'heading' => __('Tagged Feeds', 'instagram-feed'),
							'description' => __('Show Instagram posts that you have been tagged in', 'instagram-feed'),
							'uncheck' => true,
							'active' => false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 16C14.2091 16 16 14.2091 16 12C16 9.79086 14.2091 8 12 8C9.79086 8 8 9.79086 8 12C8 14.2091 9.79086 16 12 16Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 8.00036V13.0004C16 13.796 16.3161 14.5591 16.8787 15.1217C17.4413 15.6843 18.2044 16.0004 19 16.0004C19.7957 16.0004 20.5587 15.6843 21.1213 15.1217C21.6839 14.5591 22 13.796 22 13.0004V12.0004C21.9999 9.74339 21.2362 7.55283 19.8333 5.78489C18.4303 4.01694 16.4706 2.77558 14.2726 2.26265C12.0747 1.74973 9.76794 1.9954 7.72736 2.95972C5.68677 3.92405 4.03241 5.55031 3.03327 7.57408C2.03413 9.59785 1.74898 11.9001 2.22418 14.1065C2.69938 16.3128 3.90699 18.2936 5.65064 19.7266C7.39429 21.1597 9.57144 21.9607 11.8281 21.9995C14.0847 22.0383 16.2881 21.3126 18.08 19.9404" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
						[
							'heading' => __('Lightbox', 'instagram-feed'),
							'description' => __('View photos and videos in a popup lightbox directly on your site', 'instagram-feed'),
							'uncheck' => true,
							'active' => false,
							'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 3H5C4.46957 3 3.96086 3.21071 3.58579 3.58579C3.21071 3.96086 3 4.46957 3 5V8M21 8V5C21 4.46957 20.7893 3.96086 20.4142 3.58579C20.0391 3.21071 19.5304 3 19 3H16M16 21H19C19.5304 21 20.0391 20.7893 20.4142 20.4142C20.7893 20.0391 21 19.5304 21 19V16M3 16V19C3 19.5304 3.21071 20.0391 3.58579 20.4142C3.96086 20.7893 4.46957 21 5 21H8" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
						],
					]
				],
				[
					'id' => 'install-plugins',
					'template' => SBI_BUILDER_DIR . 'templates/onboarding/install-plugins.php',
					'heading' => $show_wpconsent ? __('Install a GDPR plugin', 'instagram-feed') : __('You might also be interested in...', 'instagram-feed'),
					'description' => $show_wpconsent ? __('Ensure your social media feeds comply with privacy regulations by installing a plugin today.', 'instagram-feed') : __('Instagram Feed users also install these plugins', 'instagram-feed'),
					'showGDPRInfo' => $show_wpconsent,
					'gdprInfo' => [
						'heading' => __('Why should I install a GDPR plugin?', 'instagram-feed'),
						'columns' => [
							[
								'title' => __('Legal Compliance', 'instagram-feed'),
								'description' => __('Ensure your website complies with GDPR and other privacy regulations.', 'instagram-feed'),
								'icon' => SBI_BUILDER_URL . 'assets/img/svg/legal.svg',
							],
							[
								'title' => __('Build Trust', 'instagram-feed'),
								'description' => __('Show visitors you respect their privacy with transparent cookie consent.', 'instagram-feed'),
								'icon' => SBI_BUILDER_URL . 'assets/img/svg/trust.svg',
							],
							[
								'title' => __('Easy Management', 'instagram-feed'),
								'description' => __('Simplify cookie consent and privacy compliance with automated tools.', 'instagram-feed'),
								'icon' => SBI_BUILDER_URL . 'assets/img/svg/management.svg',
							]
						]
					],
					'pluginsList' => $show_wpconsent ? [
						[
							'plugin' => 'wpconsent',
							'data' => [
								'type' => 'install_plugins',
								'id' => 'wpconsent',
								'pluginName' => __('WPConsent', 'instagram-feed')
							],
							'heading' => __('WPConsent', 'instagram-feed'),
							'description' => __('Detect all the plugins that use cookies and sets a consent banner in just a few clicks. Works well with Smash Balloon plugins.', 'instagram-feed'),
							'icon' => SBI_BUILDER_URL . 'assets/img/wpconsent-icon.png',
							'color' => 'blue',
							'active' => true
						]
					] : self::get_awesomemotive_plugins(),
					'star_icons' => SBI_PLUGIN_URL . 'admin/assets/img/stars.svg'
				],
				[
					'id' => 'success-page',
					'template' => SBI_BUILDER_DIR . 'templates/onboarding/success-page.php',
					'heading' => __('Awesome. You are all set up!', 'instagram-feed'),
					'description' => __('Here\'s an overview of everything that is setup', 'instagram-feed'),
					'upgradeContent' => [
						'heading' => __('Upgrade to unlock hashtag feeds, tagged feeds, a popup lightbox and more', 'instagram-feed'),
						'description' => __('To unlock these features and much more, upgrade to Pro and enter your license key below.', 'instagram-feed'),
						'button' => [
							'text' => __('Upgrade to Instagram Feed Pro', 'instagram-feed'),
							'link' => 'https://smashballoon.com/pricing/instagram-feed/?license_key&upgrade=true&utm_campaign=instagram-free&utm_source=setup&utm_medium=upgrade-license'
						],
						'upgradeCouppon' => 'Upgrade today and save 50% on a Pro License! (auto-applied at checkout)',
						'banner' => SBI_BUILDER_URL . 'assets/img/success-banner.jpg',

						'upgradeFeaturesList' => [
							[
								'heading' => __('Hashtag Feeds', 'instagram-feed'),
								'icon' => '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.66797 6.5H13.3346" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M2.66797 10.5H13.3346" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6.66536 2.5L5.33203 14.5" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.6654 2.5L9.33203 14.5" stroke="#0068A0" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __('Tagged Feeds', 'instagram-feed'),
								'icon' => '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.9987 11.1663C9.47146 11.1663 10.6654 9.97243 10.6654 8.49967C10.6654 7.02692 9.47146 5.83301 7.9987 5.83301C6.52594 5.83301 5.33203 7.02692 5.33203 8.49967C5.33203 9.97243 6.52594 11.1663 7.9987 11.1663Z" stroke="#E34F0E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M10.6654 5.83357V9.1669C10.6654 9.69734 10.8761 10.206 11.2512 10.5811C11.6262 10.9562 12.1349 11.1669 12.6654 11.1669C13.1958 11.1669 13.7045 10.9562 14.0796 10.5811C14.4547 10.206 14.6654 9.69734 14.6654 9.1669V8.50024C14.6653 6.99559 14.1562 5.53522 13.2209 4.35659C12.2856 3.17796 10.9791 2.35039 9.5138 2.00844C8.04852 1.66648 6.51066 1.83027 5.15027 2.47315C3.78988 3.11603 2.68697 4.20021 2.02088 5.54939C1.35478 6.89856 1.16468 8.4334 1.48148 9.90431C1.79828 11.3752 2.60335 12.6957 3.76579 13.6511C4.92823 14.6064 6.37966 15.1405 7.88408 15.1663C9.38851 15.1922 10.8574 14.7084 12.052 13.7936" stroke="#E34F0E" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __('Lightbox', 'instagram-feed'),
								'icon' => '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M5.33333 2.5H3.33333C2.97971 2.5 2.64057 2.64048 2.39052 2.89052C2.14048 3.14057 2 3.47971 2 3.83333V5.83333M14 5.83333V3.83333C14 3.47971 13.8595 3.14057 13.6095 2.89052C13.3594 2.64048 13.0203 2.5 12.6667 2.5H10.6667M10.6667 14.5H12.6667C13.0203 14.5 13.3594 14.3595 13.6095 14.1095C13.8595 13.8594 14 13.5203 14 13.1667V11.1667M2 11.1667V13.1667C2 13.5203 2.14048 13.8594 2.39052 14.1095C2.64057 14.3595 2.97971 14.5 3.33333 14.5H5.33333" stroke="#CC7A00" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							],
							[
								'heading' => __('And many more', 'instagram-feed'),
								'icon' => '<svg width="16" height="17" viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M7.9987 9.16634C8.36689 9.16634 8.66536 8.86786 8.66536 8.49967C8.66536 8.13148 8.36689 7.83301 7.9987 7.83301C7.63051 7.83301 7.33203 8.13148 7.33203 8.49967C7.33203 8.86786 7.63051 9.16634 7.9987 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12.6667 9.16634C13.0349 9.16634 13.3333 8.86786 13.3333 8.49967C13.3333 8.13148 13.0349 7.83301 12.6667 7.83301C12.2985 7.83301 12 8.13148 12 8.49967C12 8.86786 12.2985 9.16634 12.6667 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.33464 9.16634C3.70283 9.16634 4.0013 8.86786 4.0013 8.49967C4.0013 8.13148 3.70283 7.83301 3.33464 7.83301C2.96645 7.83301 2.66797 8.13148 2.66797 8.49967C2.66797 8.86786 2.96645 9.16634 3.33464 9.16634Z" fill="#434960" stroke="#434960" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>'
							]
						]
					]
				],

			]
		];

		$dynamic_features_list = self::get_dynamic_features_list();

		if (isset($data['steps']) && sizeof($dynamic_features_list) > 0) {
			$key_cf_ft = array_search('configure-features', array_column($data['steps'], 'id'));
			if ($key_cf_ft !== false) {
				$new_features_lit = array_merge($data['steps'][$key_cf_ft]['featuresList'], $dynamic_features_list);
				$data['steps'][$key_cf_ft]['featuresList'] = $new_features_lit;
			}
		}

		return $data;
	}

	/**
	 * Return Awesome Motive Plugins
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_awesomemotive_plugins()
	{
		$installed_plugins = get_plugins();

		$awesomemotive_plugins_list = [
			[
				'plugin' => 'allinoneseo',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'allinoneseo',
					'pluginName' => __('All in One SEO', 'instagram-feed'),
				],
				'heading' => __('All in One SEO Toolkit', 'instagram-feed'),
				'description' => __('Out-of-the-box SEO for WordPress. Features like XML Sitemaps, SEO for custom post types, SEO for blogs, business sites, or ecommerce sites, and much more.', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/allinoneseo.png',
				'installs_number' => '3 Million+ Installs'
			],
			[
				'plugin' => 'monsterinsight',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'monsterinsight',
					'pluginName' => __('MonsterInsights', 'instagram-feed'),
				],
				'heading' => __('Analytics by MonsterInsights', 'instagram-feed'),
				'description' => __('Make it “effortless” to connect your WordPress site with Google Analytics, so you can start making data-driven decisions to grow your business.', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/monsterinsight.png',
				'installs_number' => '3 Million+ Installs'
			],
			[
				'plugin' => 'wpforms',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'wpforms',
					'pluginName' => __('WPForms', 'instagram-feed'),
				],
				'heading' => __('Forms by WPForms', 'instagram-feed'),
				'description' => __('Create contact, subscription or payment forms with the most beginner friendly drag & drop WordPress forms plugin', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/wpforms.png',
				'installs_number' => '5 Million+ Installs'
			],
			[
				'plugin' => 'seedprod',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'seedprod',
					'pluginName' => __('SeedProd', 'instagram-feed'),
				],
				'heading' => __('SeedProd Website Builder', 'instagram-feed'),
				'description' => __('A simple and powerful theme builder, landing page builder, "coming soon" page builder, and maintenance mode notice builder', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/seedprod.png',
				'installs_number' => '900 Thousand+ Installs'
			],
			[
				'plugin' => 'optinmonster',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'optinmonster',
					'pluginName' => __('OptinMonster', 'instagram-feed'),
				],
				'heading' => __('OptinMonster Popup Builder', 'instagram-feed'),
				'description' => __('Make popups & opt-in forms to build your email newsletter subscribers, generate leads, and close sales', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/optinmonster.png',
				'installs_number' => '1 Million+ Installs'
			],
			[
				'plugin' => 'pushengage',
				'data' => [
					'type' => 'install_plugins',
					'id' => 'pushengage',
					'pluginName' => __('PushEngage', 'instagram-feed'),
				],
				'heading' => __('PushEngage Notifications', 'instagram-feed'),
				'description' => __('Create and send high-converting web push notifications to your website visitors.', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => SBI_BUILDER_URL . 'assets/img/pushengage.svg',
				'installs_number' => '10 Thousand+ Installs'
			]
		];

		$available_plugins = [];
		foreach ($awesomemotive_plugins_list as $plugin) {
			if (!self::check_awesome_motive_plugin($plugin['plugin'], $installed_plugins)) {
				array_push($available_plugins, $plugin);
			}
		}
		return array_slice($available_plugins, 0, 3);
	}

	/**
	 * Check if AWESOME MOTIVE Plugin
	 *
	 * @return boolean
	 *
	 * @since 6.X
	 */
	public static function check_awesome_motive_plugin($plugin, $installed_plugins)
	{

		switch ($plugin) {
			case 'allinoneseo':
				if (
					isset($installed_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php'])
					|| isset($installed_plugins['all-in-one-seo-pack-pro/all_in_one_seo_pack.php'])
				) {
					return true;
				}
				return false;
			case 'monsterinsight':
				if (
					isset($installed_plugins['google-analytics-for-wordpress/googleanalytics.php'])
					|| isset($installed_plugins['google-analytics-premium/googleanalytics-premium.php'])
				) {
					return true;
				}
				return false;
			case 'wpforms':
				if (
					isset($installed_plugins['wpforms-lite/wpforms.php'])
					|| isset($installed_plugins['wpforms/wpforms.php'])
				) {
					return true;
				}
				return false;
			case 'seedprod':
				if (
					isset($installed_plugins['coming-soon/coming-soon.php'])
				) {
					return true;
				}
				return false;
			case 'optinmonster':
				if (
					isset($installed_plugins['optinmonster/optin-monster-wp-api.php'])
				) {
					return true;
				}
				return false;
			case 'pushengage':
				if (
					isset($installed_plugins['pushengage/main.php'])
				) {
					return true;
				}
				return false;
		}
	}

	/**
	 * Return Dynamic Features List depending on multiple criteria
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_dynamic_features_list()
	{
		$features_list = [];
		$smash_plugin_list = self::get_smash_plugins_list();
		if (isset($smash_plugin_list['plugins']) && sizeof($smash_plugin_list['plugins']) > 0) {
			$description_plugins = implode(', ', $smash_plugin_list['text']);
			$search = ',';
			$description_plugins_text = strrev(preg_replace(strrev("/$search/"), strrev(' and '), strrev($description_plugins), 1));

			$plugins_info = [];
			foreach ($smash_plugin_list['plugins'] as $p_item) {
				if ($p_item['is_istalled'] === false) {
					array_push(
						$plugins_info,
						$p_item
					);
				}
			}

			array_push(
				$features_list,
				[
					'data' => [
						'id' => $description_plugins,
						'type' => 'install_plugins',
						'plugins' => 'smash'
					],
					'heading' => __('Social Feed Collection', 'instagram-feed'),
					'description' => __('Install', 'instagram-feed') . ' ' . $description_plugins_text . ' ' . __('feed plugins for more fresh content', 'instagram-feed'),
					'color' => 'blue',
					'active' => true,
					'icon' => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M21 15.9999V7.9999C20.9996 7.64918 20.9071 7.30471 20.7315 7.00106C20.556 6.69742 20.3037 6.44526 20 6.2699L13 2.2699C12.696 2.09437 12.3511 2.00195 12 2.00195C11.6489 2.00195 11.304 2.09437 11 2.2699L4 6.2699C3.69626 6.44526 3.44398 6.69742 3.26846 7.00106C3.09294 7.30471 3.00036 7.64918 3 7.9999V15.9999C3.00036 16.3506 3.09294 16.6951 3.26846 16.9987C3.44398 17.3024 3.69626 17.5545 4 17.7299L11 21.7299C11.304 21.9054 11.6489 21.9979 12 21.9979C12.3511 21.9979 12.696 21.9054 13 21.7299L20 17.7299C20.3037 17.5545 20.556 17.3024 20.7315 16.9987C20.9071 16.6951 20.9996 16.3506 21 15.9999Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M3.26953 6.95996L11.9995 12.01L20.7295 6.95996" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M12 22.08V12" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
					'plugins' => $plugins_info,
					'tooltip' => __('Smash Balloon offers the best social media feed solutions for WordPress. Enabling this feature will install additional plugins to power all of the supported platforms', 'instagram-feed'),
				]
			);
		}


		// Reviews Plugin
		$reviews_plugin = self::get_smash_reviews_plugin();
		if ($reviews_plugin !== false) {
			array_push($features_list, $reviews_plugin);
		}

		return $features_list;
	}

	/**
	 * Return Uninstalled SmashBalloon Plugins
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_smash_plugins_list()
	{
		$installed_plugins = get_plugins();

		// check whether the pro or free plugins are installed
		$is_facebook_installed = false;
		$facebook_plugin = 'custom-facebook-feed/custom-facebook-feed.php';
		if (isset($installed_plugins['custom-facebook-feed-pro/custom-facebook-feed.php'])) {
			$is_facebook_installed = true;
			$facebook_plugin = 'custom-facebook-feed-pro/custom-facebook-feed.php';
		} elseif (isset($installed_plugins['custom-facebook-feed/custom-facebook-feed.php'])) {
			$is_facebook_installed = true;
		}

		$is_instagram_installed = false;
		$instagram_plugin = 'instagram-feed/instagram-feed.php';
		if (isset($installed_plugins['instagram-feed-pro/instagram-feed.php'])) {
			$is_instagram_installed = true;
			$instagram_plugin = 'instagram-feed-pro/instagram-feed.php';
		} elseif (isset($installed_plugins['instagram-feed/instagram-feed.php'])) {
			$is_instagram_installed = true;
		}

		$is_twitter_installed = false;
		$twitter_plugin = 'custom-twitter-feeds/custom-twitter-feed.php';
		if (isset($installed_plugins['custom-twitter-feeds-pro/custom-twitter-feed.php'])) {
			$is_twitter_installed = true;
			$twitter_plugin = 'custom-twitter-feeds-pro/custom-twitter-feed.php';
		} elseif (isset($installed_plugins['custom-twitter-feeds/custom-twitter-feed.php'])) {
			$is_twitter_installed = true;
		}

		$is_youtube_installed = false;
		$youtube_plugin = 'feeds-for-youtube/youtube-feed.php';
		if (isset($installed_plugins['youtube-feed-pro/youtube-feed.php'])) {
			$is_youtube_installed = true;
			$youtube_plugin = 'youtube-feed-pro/youtube-feed.php';
		} elseif (isset($installed_plugins['feeds-for-youtube/youtube-feed.php'])) {
			$is_youtube_installed = true;
		}

		$is_tiktok_installed = false;
		$tiktok_plugin = 'feeds-for-tiktok/feeds-for-tiktok.php';
		if (isset($installed_plugins['tiktok-feeds-pro/tiktok-feeds-pro.php'])) {
			$is_tiktok_installed = true;
			$tiktok_plugin = 'tiktok-feeds-pro/tiktok-feeds-pro.php';
		} elseif (isset($installed_plugins['feeds-for-tiktok/feeds-for-tiktok.php'])) {
			$is_tiktok_installed = true;
		}

		$smash_list = [
			'text' => [],
			'plugins' => [
				[
					'type' => 'instagram',
					'is_istalled' => $is_instagram_installed,
					'download_link' => $instagram_plugin,
					'min_php' => '5.6.0',
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/insta-icon.svg'
				],
				[
					'type' => 'facebook',
					'is_istalled' => $is_facebook_installed,
					'download_link' => $facebook_plugin,
					'min_php' => '5.6.0',
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/fb-icon.svg'
				],
				[
					'type' => 'twitter',
					'is_istalled' => $is_twitter_installed,
					'download_link' => $twitter_plugin,
					'min_php' => '5.6.0',
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/twitter-icon.svg'
				],
				[
					'type' => 'youtube',
					'is_istalled' => $is_youtube_installed,
					'download_link' => $youtube_plugin,
					'min_php' => '5.6.0',
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/youtube-icon.svg'
				],
				[
					'type' => 'tiktok',
					'is_istalled' => $is_tiktok_installed,
					'download_link' => $tiktok_plugin,
					'min_php' => '7.0',
					'icon' => SBI_PLUGIN_URL . 'admin/assets/img/tiktok-icon.svg'
				]
			]
		];
		foreach ($smash_list['plugins'] as $mash_plugin) {
			if (version_compare(PHP_VERSION, $mash_plugin['min_php'], '<')) {
				$mash_plugin['is_istalled'] = true;
			}
			if ($mash_plugin['type'] === self::$plugin_name || $mash_plugin['is_istalled'] === true) {
				unset($mash_plugin);
			} else {
				array_push($smash_list['text'], ucfirst($mash_plugin['type']));
			}
		}

		return $smash_list;
	}

	/**
	 * Return Reviews Plugin if not Installed
	 *
	 * @return array
	 *
	 * @since 6.X
	 */
	public static function get_smash_reviews_plugin()
	{
		$installed_plugins = get_plugins();
		$min_php = '7.1';

		$is_reviews_installed = false;
		$reviews_plugin = 'reviews-feed/sb-reviews.php';
		if (isset($installed_plugins['reviews-feed-pro/sb-reviews-pro.php'])) {
			$is_reviews_installed = true;
			$reviews_plugin = 'reviews-feed-pro/sb-reviews-pro.php';
		} elseif (isset($installed_plugins['reviews-feed/sb-reviews.php'])) {
			$is_reviews_installed = true;
		}

		if (version_compare(PHP_VERSION, $min_php, '<')) {
			$is_reviews_installed = true;
		}

		if ($is_reviews_installed === false) {
			return [
				'data' => [
					'id' => 'reviews',
					'type' => 'install_plugins'
				],
				'heading' => __('Customer Reviews Plugin', 'instagram-feed'),
				'description' => __('Install Reviews Feed to display customer reviews from Google or Yelp and build trust', 'instagram-feed'),
				'color' => 'blue',
				'active' => true,
				'icon' => '<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M19 9.50003C19.0034 10.8199 18.6951 12.1219 18.1 13.3C17.3944 14.7118 16.3098 15.8992 14.9674 16.7293C13.6251 17.5594 12.0782 17.9994 10.5 18C9.18013 18.0035 7.87812 17.6951 6.7 17.1L1 19L2.9 13.3C2.30493 12.1219 1.99656 10.8199 2 9.50003C2.00061 7.92179 2.44061 6.37488 3.27072 5.03258C4.10083 3.69028 5.28825 2.6056 6.7 1.90003C7.87812 1.30496 9.18013 0.996587 10.5 1.00003H11C13.0843 1.11502 15.053 1.99479 16.5291 3.47089C18.0052 4.94699 18.885 6.91568 19 9.00003V9.50003Z" stroke="#696D80" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>',
				'plugins' => [
					[
						'type' => 'reviews',
						'is_istalled' => $is_reviews_installed,
						'download_link' => $reviews_plugin,
						'min_php' => $min_php,
						'icon' => SBI_PLUGIN_URL . 'admin/assets/img/reviews-icon.svg'
					]
				],
				'tooltip' => __('Enabling this feature will install Reviews Feed plugin. Reviews Feed by Smash Balloon helps users to display reviews from Google, TripAdvisor, TrustPilot and more.', 'instagram-feed'),

			];
		}
		return false;
	}

	/**
	 * Wizard Wrapper.
	 *
	 * @since 6.0
	 */
	public function feed_builder()
	{
		include_once SBI_BUILDER_DIR . 'templates/wizard.php';
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
		$submenu = self::should_init_wizard() ? 'sb-instagram-feed' : 'sb';
		$feed_builder = add_submenu_page(
			$submenu,
			__('Setup', 'instagram-feed'),
			__('Setup', 'instagram-feed'),
			$cap,
			'sbi-setup',
			array($this, 'feed_builder'),
			0
		);
		add_action('load-' . $feed_builder, array($this, 'builder_enqueue_admin_scripts'));
	}

	/**
	 * Check if we need to Init the Onboarding wizard
	 *
	 * @since 6.0
	 */
	public static function should_init_wizard()
	{
		$statues = get_option(self::$statues_name, array());
		if (!isset($statues['wizard_dismissed']) || $statues['wizard_dismissed'] === false) {
			return true;
		}
		return false;
	}

	/**
	 * Process Wizard Data
	 *    Save Settings, Install Plugins and more
	 *
	 * @since 6.0.8
	 */
	public function process_wizard_data()
	{
		if (!isset($_POST['data'])) {
			wp_send_json_error();
		}

		check_ajax_referer('sbi-admin', 'nonce');
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		$sbi_settings = get_option('sb_instagram_settings', array());

		$onboarding_data = sanitize_text_field(stripslashes($_POST['data']));
		$onboarding_data = json_decode($onboarding_data, true);
		foreach ($onboarding_data as $single_data) {
			if ($single_data['type'] === 'settings') {
				$sbi_settings[$single_data['id']] = $single_data['id'] === 'sb_instagram_disable_resize' ? false : true;
			}
			if ($single_data['type'] === 'install_plugins' && current_user_can('install_plugins')) {
				$plugins = explode(',', $single_data['id']);
				// Deleting Redirect Data for 3rd plugins
				// $this->disable_installed_plugins_redirect();
				foreach ($plugins as $plugin_name) {
					@SBI_Onboarding_wizard::install_single_plugin($plugin_name);
					$this->disable_installed_plugins_redirect();
				}
			}
		}
		update_option('sb_instagram_settings', $sbi_settings);


		wp_die();
	}

	/**
	 * Install Plugin
	 *
	 * @since 6.X
	 */
	public static function install_single_plugin($plugin_name)
	{
		$plugin_download = self::get_plugin_download_link(strtolower(str_replace(' ', '', $plugin_name)));
		if ($plugin_download === false || !current_user_can('install_plugins')) {
			return false;
		}


		if (strpos($plugin_download, 'https://downloads.wordpress.org/plugin/') !== 0) {
			return false;
		}

		set_current_screen('sbi-feed-builder');
		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'sbi-feed-builder',
				),
				admin_url('admin.php')
			)
		);

		$creds = request_filesystem_credentials($url, '', false, false, null);
		// Check for file system permissions.
		if (false === $creds || !WP_Filesystem($creds)) {
			return false;
		}
		require_once SBI_PLUGIN_DIR . 'inc/admin/class-install-skin.php';
		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

		// Create the plugin upgrader with our custom skin.
		$installer = new PluginSilentUpgrader(new Sbi_Install_Skin());

		// Error check.
		if (!method_exists($installer, 'install') || empty($plugin_download)) {
			wp_send_json_error($error);
		}

		$installer->install(esc_url_raw(wp_unslash($plugin_download)));

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin_basename = $installer->plugin_info();

		if ($plugin_basename) {
			activate_plugin($plugin_basename);
		}
	}

	/**
	 * Get Plugin Download
	 *
	 * @since 6.X
	 */
	public static function get_plugin_download_link($plugin_name)
	{
		$plugin_download = false;
		switch (strtolower($plugin_name)) {
			case 'wpconsent':
				$plugin_download = 'https://downloads.wordpress.org/plugin/wpconsent-cookies-banner-privacy-suite.zip';
				break;
			case 'facebook':
				$plugin_download = 'https://downloads.wordpress.org/plugin/custom-facebook-feed.zip';
				break;
			case 'instagram':
				$plugin_download = 'https://downloads.wordpress.org/plugin/instagram-feed.zip';
				break;
			case 'twitter':
				$plugin_download = 'https://downloads.wordpress.org/plugin/custom-twitter-feeds.zip';
				break;
			case 'youtube':
				$plugin_download = 'https://downloads.wordpress.org/plugin/feeds-for-youtube.zip';
				break;
			case 'tiktok':
				$plugin_download = 'https://downloads.wordpress.org/plugin/feeds-for-tiktok.zip';
				break;
			case 'reviews':
				$plugin_download = 'https://downloads.wordpress.org/plugin/reviews-feed.zip';
				break;
			case 'allinoneseo':
				$plugin_download = 'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip';
				break;
			case 'monsterinsight':
				$plugin_download = 'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip';
				break;
			case 'wpforms':
				$plugin_download = 'https://downloads.wordpress.org/plugin/wpforms-lite.zip';
				break;
			case 'seedprod':
				$plugin_download = 'https://downloads.wordpress.org/plugin/coming-soon.zip';
				break;
			case 'optinmonster':
				$plugin_download = 'https://downloads.wordpress.org/plugin/optinmonster.zip';
				break;
			case 'pushengage':
				$plugin_download = 'https://downloads.wordpress.org/plugin/pushengage.zip';
				break;
		}
		return $plugin_download;
	}

	/**
	 * Disable Installed Plugins Redirect
	 *
	 * @since 6.0.8
	 */
	public function disable_installed_plugins_redirect()
	{
		// Monster Insight
		delete_transient('_monsterinsights_activation_redirect');

		// All in one SEO
		update_option('aioseo_activation_redirect', true);

		// WPForms
		update_option('wpforms_activation_redirect', true);

		// Optin Monster
		delete_transient('optin_monster_api_activation_redirect');
		update_option('optin_monster_api_activation_redirect_disabled', true);

		// Seed PROD
		update_option('seedprod_dismiss_setup_wizard', true);

		// PushEngage
		delete_transient('pushengage_activation_redirect');

		// Smash Plugin redirect remove
		$this->disable_smash_installed_plugins_redirect();
	}

	/**
	 * Disable Smash Balloon Plugins Redirect
	 *
	 * @since 6.0.8
	 */
	public function disable_smash_installed_plugins_redirect()
	{
		$smash_list = [
			'facebook' => 'cff_plugin_do_activation_redirect',
			'instagram' => 'sbi_plugin_do_activation_redirect',
			'youtube' => 'sby_plugin_do_activation_redirect',
			'twitter' => 'ctf_plugin_do_activation_redirect',
			'reviews' => 'sbr_plugin_do_activation_redirect',
		];

		if (isset($smash_list[self::$plugin_name])) {
			unset($smash_list[self::$plugin_name]);
		}

		foreach ($smash_list as $key => $opt) {
			delete_option($opt);
		}
	}

	/**
	 * Dismiss Onboarding Wizard
	 *
	 * @since 6.0.8
	 */
	public function dismiss_wizard()
	{
		check_ajax_referer('sbi-admin', 'nonce');
		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}
		$sbi_statuses_option = get_option('sbi_statuses', array());
		$sbi_statuses_option['wizard_dismissed'] = true;
		update_option('sbi_statuses', $sbi_statuses_option);
		wp_send_json_error();
	}
}
