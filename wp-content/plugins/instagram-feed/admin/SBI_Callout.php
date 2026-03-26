<?php

/**
 * Callouts
 *
 * @since X.X
 */

namespace InstagramFeed\Admin;

if (!defined('ABSPATH')) {
	exit;
}

class SBI_Callout
{
	const PLUGIN_NAME = 'instagram';
	const ASSETS_CSS = SBI_PLUGIN_URL . 'admin/assets/css/';
	const ASSETS_JS = SBI_PLUGIN_URL . 'admin/assets/js/';
	const TWO_WEEKS_WAIT = 1209600;

	public $plugins_list;
	public $should_show_callout;

	public function __construct()
	{
		$this->dismiss_notice();
		$this->plugins_list = self::get_callout_plugins_list();
		$this->should_show_callout = sizeof($this->plugins_list) !== 0 && self::should_show_callout();

		add_action('wp_enqueue_scripts', [$this, 'register_assets']);
		add_action('admin_enqueue_scripts', [$this, 'register_assets']);
		add_action('wp_dashboard_setup', [$this, 'dashboard_widget']);
	}

	/**
	 * Dimiss the Callout Notice
	 *
	 * @since X.X
	 */
	public function dismiss_notice()
	{
		if (
			!empty($_GET['sb_dismiss']) && $_GET['sb_dismiss'] === 'callout' &&
			!empty($_GET['sb_nonce']) && wp_verify_nonce($_GET['sb_nonce'], 'sb-callout')
		) {
			$callout_opt = get_option('sb_callout', []);
			$callout_opt['dismissed'] = true;
			/**
			 * If users dismisses the callout
			 * for the first time -> store the current time (So it will be shown after 2 weeks)
			 * 2 second time (& more than 2 weeks) -> store permanent, this way the callout will no longer be shown
			 */
			$callout_opt['dismissed_date'] =
				isset($callout_opt['dismissed_date']) &&
				(
					($callout_opt['dismissed_date'] === 'permanent') ||
					($callout_opt['dismissed_date'] !== 'permanent' && intval($callout_opt['dismissed_date']) + self::TWO_WEEKS_WAIT < time())
				)
					? 'permanent' : time();
			update_option('sb_callout', $callout_opt);
		}
	}

	/**
	 * Get Plugins List
	 * Check for SmashBalloon installed plugins (with no feeds)
	 *
	 * @return array
	 *
	 * @since X.X
	 */
	public static function get_callout_plugins_list()
	{
		if (!function_exists('get_plugins')) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$installed_plugins = get_plugins();
		$plugins_list = self::get_plugins_list();

		foreach ($plugins_list as $key => $plugin) {
			if (isset($installed_plugins[$plugin['plugin']]) && is_plugin_active($plugin['plugin'])) {
				$feeds_number = self::feeds_number($plugin['table']);
				// If feeds_number returns null, the table doesn't exist yet (e.g., during activation)
				// In this case, skip this plugin from the callout list
				if ($feeds_number === null) {
					unset($plugins_list[$key]);
					continue;
				}
				if ($feeds_number === 0) {
					$plugins_list[$key]['is_done'] = false;
				} else {
					$plugins_list[$key]['is_done'] = true;
				}
			} else {
				unset($plugins_list[$key]);
			}
		}

		uasort($plugins_list, function ($a, $b) {
			if ($a['is_done'] === true && $b['is_done'] === false) {
				return -1;
			}
			if ($a['is_done'] === false && $b['is_done'] === true) {
				return 1;
			}
			return 0;
		});
		return $plugins_list;
	}

	/**
	 * Get Smashballoon Plugins List
	 *
	 * @return array
	 *
	 * @since X.X
	 */
	public static function get_plugins_list()
	{
		return [
			'instagram' => [
				'plugin' => 'instagram-feed/instagram-feed.php',
				'statuses' => 'sbi_statuses',
				'table' => 'sbi_feeds',
				'page' => 'sbi-feed-builder'
			],
			'facebook' => [
				'plugin' => 'custom-facebook-feed/custom-facebook-feed.php',
				'statuses' => 'cff_statuses',
				'table' => 'cff_feeds',
				'page' => 'cff-feed-builder'
			],
			'twitter' => [
				'plugin' => 'custom-twitter-feeds/custom-twitter-feed.php',
				'statuses' => 'ctf_statuses',
				'table' => 'ctf_feeds',
				'page' => 'ctf-feed-builder'
			],
			'youtube' => [
				'plugin' => 'feeds-for-youtube/youtube-feed.php',
				'statuses' => 'sby_statuses',
				'table' => 'sby_feeds',
				'page' => 'sby-feed-builder'
			],
			'reviews' => [
				'plugin' => 'reviews-feed/sb-reviews.php',
				'statuses' => 'sbr_statuses',
				'table' => 'sbr_feeds',
				'page' => 'sbr'
			],
			'tiktok' => [
				'plugin' => 'feeds-for-tiktok/feeds-for-tiktok.php',
				'statuses' => 'sbtt_statuses',
				'table' => 'sbtt_feeds',
				'page' => 'sbtt'
			],
		];
	}

	/**
	 * SQL Query to get the number of created feeds
	 *
	 * @param string $table_name The table name without prefix
	 *
	 * @return int|null Number of feeds, or null if table doesn't exist yet
	 *
	 * @since X.X
	 */
	public static function feeds_number($table_name)
	{
		global $wpdb;
		$feeds_table_name = $wpdb->prefix . $table_name;

		// Check if table exists before querying to prevent errors during activation/migration
		// This prevents database errors in the log when plugin is first activated
		$table_exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $feeds_table_name));

		if ($table_exists !== $feeds_table_name) {
			return null;
		}

		return (int) $wpdb->get_var("SELECT COUNT(*) FROM $feeds_table_name");
	}

	/**
	 * Check if Whether to Show The callout or nor
	 *
	 * @since X.X
	 */
	public static function should_show_callout()
	{
		$plugins_list = self::get_callout_plugins_list();
		if (!isset($plugins_list[self::PLUGIN_NAME]) || !isset($plugins_list[self::PLUGIN_NAME]['statuses']) || !self::check_done_plugins($plugins_list)) {
			return false;
		}

		// Thrive Architect Compatibility.
		if (isset($_GET['tve']) && $_GET['tve'] == 'true') {
			return false;
		}

		// Current Plugin Options.
		$plugin = $plugins_list[self::PLUGIN_NAME];
		$plugin_statuses = get_option($plugin['statuses'], []);

		if (!isset($plugin_statuses['first_install'])) {
			return false;
		}
		// If the plugin has no feed, then we don't display the callout unless it's been 2 weeks after installation.
		$been_2_weeks = intval($plugin_statuses['first_install']) + self::TWO_WEEKS_WAIT > time();
		if ($been_2_weeks && !$plugins_list[self::PLUGIN_NAME]['is_done']) {
			return false;
		}

		// It's been more than 3 months of the first install
		$more_3_months = intval($plugin_statuses['first_install']) + 7889229 < time();
		if ($more_3_months) {
			return false;
		}

		/**
		 * These Options will be shared across all the plugins
		 * This way we can control the Callout dismissal once!
		 */
		$callout_opt = get_option('sb_callout', []);
		if (
			isset($callout_opt['dismissed'], $callout_opt['dismissed_date']) &&
			(
				$callout_opt['dismissed_date'] === 'permanent' ||
				(
					$callout_opt['dismissed_date'] !== 'permanent' && intval($callout_opt['dismissed_date']) + self::TWO_WEEKS_WAIT > time()
				)
			)
		) {
			return false;
		}

		return true;
	}

	/**
	 * Check if all the plugins are done
	 *
	 * @since X.X
	 */
	public static function check_done_plugins($plugins_list)
	{
		$should_show = false;
		foreach ($plugins_list as $s_plugin) {
			if ($s_plugin['is_done'] === false) {
				$should_show = true;
				break;
			}
		}
		return $should_show;
	}

	/**
	 * Register Callout Assets
	 *
	 * @since X.X
	 */
	public function register_assets()
	{
		$should_show_dashboard = sizeof($this->plugins_list) !== 0 && $this->is_dashboard_screen();
		if ($this->should_show_callout || $should_show_dashboard) {
			if (is_admin()) {
				wp_enqueue_script(
					'callout-js',
					self::ASSETS_JS . 'callout.js',
					null,
					null,
					true
				);
			}

			wp_enqueue_style(
				'callout-style',
				self::ASSETS_CSS . 'callout.css',
				false,
				null
			);
		}
	}

	public function is_dashboard_screen()
	{
		// Check if it's an AJAX request first.
		if (wp_doing_ajax()) {
			return false;
		}

		if (is_admin()) {
			if (!function_exists('get_current_screen')) {
				require_once ABSPATH . '/wp-admin/includes/screen.php';
			}

			$screen = get_current_screen();

			// Check if $screen exists before accessing its properties.
			if ($screen && $screen->id === "dashboard") {
				return true;
			}
		}

		return false;
	}

	/**
	 * Display Dashboard Widget
	 *
	 * @since X.X
	 */
	public function dashboard_widget()
	{
		$should_show_dashboard = sizeof($this->plugins_list) !== 0 && $this->is_dashboard_screen();
		if ($should_show_dashboard) {
			wp_add_dashboard_widget(
				'sb_dashboard_widget',
				__('Smash Balloon Feeds', 'instagram-feeds'),
				function () {
					echo self::print_callout_ob_html('dashboard', false);
				}
			);
		}
	}

	/**
	 * Print The Callout in the Sidebar Menu
	 *
	 * @since X.X
	 */
	public static function print_callout_ob_html($type)
	{
		ob_start();
		SBI_Callout::print_callout($type);
		return ob_get_clean();
	}

	/**
	 * Print the Callout
	 *
	 * @since X.X
	 */
	public static function print_callout($type = 'frontend')
	{
		$plugins_list = self::get_callout_plugins_list();
		if ((sizeof($plugins_list) === 0 || !self::should_show_callout()) && $type !== 'dashboard') {
			return false;
		}
		$process = 0;
		foreach ($plugins_list as $sg_plugin) {
			if (isset($sg_plugin['is_done']) && $sg_plugin['is_done'] === true) {
				$process += 1;
			}
		}
		$process_percent = $process === 0 ? 0 : intval(100 / (sizeof($plugins_list) / $process));

		$dismiss_callout = wp_nonce_url(
			add_query_arg(['sb_dismiss' => 'callout']),
			'sb-callout',
			'sb_nonce'
		);

		?>
		<div class="sb-callout-ctn" data-type="<?php echo $type ?>">
			<div class="sb-callout-top sb-fs">
				<div class="sb-callout-top-heading">
					<svg width="16" height="17" viewBox="0 0 16 17" fill="none">
						<path fill-rule="evenodd" clip-rule="evenodd"
							  d="M7.908 0.5C11.2231 0.5 13.9095 3.82243 13.9095 7.92084C13.9095 11.7725 11.5374 14.9377 8.50226 15.3061L9.23157 16.1975L7.1529 16.3743L7.48441 15.3245C4.36702 15.0556 1.90527 11.8498 1.90527 7.92084C1.90527 3.82243 4.59293 0.5 7.908 0.5ZM9.62864 6.08918L9.3398 3.10897L7.49633 5.42022L4.85195 3.91264L5.62324 6.66616L2.82813 7.56509L5.43923 8.88209L4.40933 11.6475L7.08659 10.4207L8.41253 13.0003L9.2858 10.1197L12.1663 10.6611L10.4565 8.18805L12.6214 6.17515L9.62864 6.08918Z"
							  fill="#FE544F"/>
					</svg>
					<strong>
						<span class="sb-callout-only-visible">
							<svg width="13" height="14" viewBox="0 0 13 14" fill="none">
								<path d="M6.49984 5.375C6.06886 5.375 5.65553 5.5462 5.35079 5.85095C5.04604 6.1557 4.87484 6.56902 4.87484 7C4.87484 7.43098 5.04604 7.8443 5.35079 8.14905C5.65553 8.4538 6.06886 8.625 6.49984 8.625C6.93081 8.625 7.34414 8.4538 7.64889 8.14905C7.95363 7.8443 8.12484 7.43098 8.12484 7C8.12484 6.56902 7.95363 6.1557 7.64889 5.85095C7.34414 5.5462 6.93081 5.375 6.49984 5.375ZM6.49984 9.70833C5.78154 9.70833 5.09267 9.42299 4.58476 8.91508C4.07685 8.40717 3.7915 7.71829 3.7915 7C3.7915 6.28171 4.07685 5.59283 4.58476 5.08492C5.09267 4.57701 5.78154 4.29167 6.49984 4.29167C7.21813 4.29167 7.90701 4.57701 8.41492 5.08492C8.92283 5.59283 9.20817 6.28171 9.20817 7C9.20817 7.71829 8.92283 8.40717 8.41492 8.91508C7.90701 9.42299 7.21813 9.70833 6.49984 9.70833ZM6.49984 2.9375C3.7915 2.9375 1.47859 4.62208 0.541504 7C1.47859 9.37792 3.7915 11.0625 6.49984 11.0625C9.20817 11.0625 11.5211 9.37792 12.4582 7C11.5211 4.62208 9.20817 2.9375 6.49984 2.9375Z"
									  fill="#0068A0"/>
							</svg>
							<?php echo __('Only Visible to you', 'instagram-feed') ?>
						</span>
						<span class="sb-fs"><?php echo __('Smash Balloon Feeds', 'instagram-feed') ?></span>
					</strong>
				</div>
				<span <?php echo self::js_open_link(esc_url($dismiss_callout), '_self') ?> class="sb-callout-top-dismiss"></span>
			</div>
			<div class="sb-callout-progress sb-fs">
				<div
						class="sb-callout-progress-radial"
						style="--percent: <?php echo 100 - $process_percent; ?>"
				>
					<span>
						<?php echo esc_attr($process_percent); ?>%
					</span>
					<svg width="71" height="71" viewBox="0 0 71 71" class="sb-progress-svg">
						<circle class="sb-progress-svg-bg"></circle>
						<circle class="sb-progress-svg-fg"></circle>
					</svg>
				</div>
				<div class="sb-callout-progress-text">
					<strong><?php echo __('Setup is almost complete', 'instagram-feed') ?></strong>
					<span><?php echo __('Complete your Smash Balloon feed setup and increase engagment', 'instagram-feed') ?></span>
				</div>
			</div>
			<div class="sb-callout-plugins sb-fs">
				<?php foreach ($plugins_list as $key => $plugin) { ?>
					<div class="sb-callout-plugin-item sb-fs"
						 data-done="<?php echo $plugin['is_done'] ? 'true' : 'false' ?>">
						<div class="sb-callout-item-checkbox"></div>
						<span>
							<?php echo ($key === 'instagram' ? __('Create an', 'instagram-feed') : __('Create a', 'instagram-feed')) . ' ' . ucfirst($key) . ' ' . __('Feed', 'instagram-feed') ?>
						</span>
						<?php if (sizeof($plugins_list) === 1) { ?>
							<span <?php echo self::js_open_link(esc_url(admin_url('admin.php?page=' . $plugin['page']))) ?> class="sb-callout-item-btn">
								<?php echo __('Go to Plugin', 'instagram-feed') ?>
							</span>
						<?php } ?>
					</div>
				<?php } ?>
				<?php
				if (sizeof($plugins_list) > 1 && $process !== sizeof($plugins_list)) {
					$next_p = self::next_plugin($plugins_list);
					?>
					<div class="sb-callout-bottom-btns sb-fs">
						<span  <?php echo self::js_open_link(esc_url(admin_url('admin.php?page=' . $plugins_list[$next_p]['page']))) ?> class="sb-callout-item-btn">
						<?php echo __('Go to', 'instagram-feed') . ' ' . ucfirst($next_p) . ' ' . __('Plugin', 'instagram-feed') ?>
						</span>
						<span <?php echo self::js_open_link(esc_url($dismiss_callout), '_self') ?> class="sb-callout-item-btn sb-callout-item-btn-grey">
						<?php echo __('Skip this step', 'instagram-feed') ?>
						</span>
					</div>
				<?php } ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Open Link
	 *
	 * @return string
	 *
	 * @since X.X
	 */
	public static function js_open_link($url, $target = "_blank")
	{
		return 'onclick="window.open(\'' . $url . '\', \'' . $target . '\')" ';
	}

	/**
	 * Get Next Plugin for Setup
	 *
	 * @return int
	 *
	 * @since X.X
	 */
	public static function next_plugin($plugins_list)
	{
		$pkey = '';
		foreach ($plugins_list as $key => $plugin) {
			if ($plugin['is_done'] === false) {
				$pkey = $key;
				break;
			}
		}
		return $pkey;
	}
}