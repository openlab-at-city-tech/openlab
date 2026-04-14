<?php

namespace InstagramFeed\Integrations\Divi;

use InstagramFeed\Helpers\Util;
use InstagramFeed\Integrations\SBI_Integration;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBI_Divi_Handler
 *
 * @since 6.2.9
 */
class SBI_Divi_Handler
{
	/**
	 * SBI_Divi_Handler constructor.
	 *
	 * @since 6.2.9
	 */
	public function __construct()
	{
		$this->load();
	}

	/**
	 * Load an integration.
	 *
	 * @since 6.2.9
	 */
	public function load()
	{
		if ($this->allow_load()) {
			$this->hooks();
		}
	}

	/**
	 * Indicate if current integration is allowed to load.
	 *
	 * @return bool
	 * @since 6.2.9
	 */
	public function allow_load()
	{
		if (function_exists('et_divi_builder_init_plugin')) {
			return true;
		}

		$allow_themes = ['Divi'];
		$theme_name = get_template();

		return in_array($theme_name, $allow_themes, true);
	}

	/**
	 * Hooks.
	 *
	 * @since 6.2.9
	 */
	public function hooks()
	{
		add_action('et_builder_ready', [$this, 'register_module']);

		if (wp_doing_ajax()) {
			add_action('wp_ajax_sb_instagramfeed_divi_preview', [$this, 'preview']);
		}

		if ($this->is_divi_builder()) {
			add_action('wp_enqueue_scripts', [$this, 'builder_scripts']);
		}
	}

	/**
	 * Determine if a current page is opened in the Divi Builder.
	 *
	 * @return bool
	 * @since 6.2.9
	 */
	private function is_divi_builder()
	{
		return !empty($_GET['et_fb']); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * Load scripts.
	 *
	 * @since 6.2.9
	 */
	public function builder_scripts()
	{
		wp_enqueue_script(
			'sbinstagram-divi',
			// The unminified version is not supported by the browser.
			SBI_PLUGIN_URL . 'admin/assets/js/divi-handler.min.js',
			['react', 'react-dom', 'jquery'],
			SBIVER,
			true
		);

		wp_localize_script(
			'sbinstagram-divi',
			'sb_divi_builder',
			[
				'ajax_handler' => admin_url('admin-ajax.php'),
				'nonce' => wp_create_nonce('sbi-admin'),
				'feed_splash' => htmlspecialchars(SBI_Integration::get_widget_cta('button'))
			]
		);

		$upload = wp_upload_dir();
		$resized_url = trailingslashit($upload['baseurl']) . trailingslashit(SBI_UPLOADS_NAME);

		$js_options = array(
			'font_method' => 'svg',
			'placeholder' => trailingslashit(SBI_PLUGIN_URL) . 'img/placeholder.png',
			'resized_url' => $resized_url,
			'ajax_url' => admin_url('admin-ajax.php'),
		);

		// legacy settings
		$path = Util::sbi_legacy_css_enabled() ? 'js/legacy/' : 'js/';

		wp_enqueue_script(
			'sbiscripts',
			SBI_PLUGIN_URL . $path . 'sbi-scripts.min.js',
			array('jquery'),
			SBIVER,
			true
		);
		wp_localize_script('sbiscripts', 'sb_instagram_js_options', $js_options);
	}

	/**
	 * Register Divi module.
	 *
	 * @since 6.2.9
	 */
	public function register_module()
	{
		if (!class_exists('ET_Builder_Module')) {
			return;
		}

		new SBInstagramFeed();
	}

	/**
	 * Ajax handler for the Feed preview.
	 *
	 * @since 6.2.9
	 */
	public function preview()
	{
		check_ajax_referer('sbi-admin', 'nonce');

		if (!sbi_current_user_can('manage_instagram_feed_options')) {
			wp_send_json_error();
		}

		$feed_id = absint(filter_input(INPUT_POST, 'feed_id', FILTER_SANITIZE_NUMBER_INT));

		wp_send_json_success(
			do_shortcode(
				sprintf(
					'[instagram-feed feed="%1$s"]',
					absint($feed_id)
				)
			)
		);
	}
}
