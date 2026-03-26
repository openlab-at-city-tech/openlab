<?php

namespace InstagramFeed\Integrations\Elementor;

use InstagramFeed\Builder\SBI_Feed_Builder;
use InstagramFeed\Helpers\Util;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
}

/**
 * Class SBI_Elementor_Base
 *
 * @since 6.2.9
 */
class SBI_Elementor_Base
{
	/**
	 * Plugin version.
	 *
	 * @since 6.2.9
	 * @access public
	 *
	 * @var string The plugin version.
	 */
	const VERSION = SBIVER;

	/**
	 * Minimum Elementor version.
	 *
	 * @since 6.2.9
	 * @access public
	 *
	 * @var string Minimum Elementor version required to run the plugin.
	 */
	const MINIMUM_ELEMENTOR_VERSION = '3.6.0';

	/**
	 * Minimum PHP version.
	 *
	 * @since 6.2.9
	 * @access public
	 *
	 * @var string Minimum PHP version required to run the plugin.
	 */
	const MINIMUM_PHP_VERSION = '5.6';
	/**
	 * Name Space.
	 *
	 * @since 6.2.9
	 * @access public
	 *
	 * @var string The name space of the plugin.
	 */
	const NAME_SPACE = 'InstagramFeed.Integrations.Elementor.';
	/**
	 * Instance.
	 *
	 * @since 6.2.9
	 * @access private
	 *
	 * @var SBI_Elementor_Base The single instance of the class.
	 */
	private static $instance = null;

	/**
	 * Instance.
	 *
	 * Ensures only one instance of the class is loaded or can be loaded.
	 *
	 * @return SBI_Elementor_Base An instance of the class.
	 * @since 6.2.9
	 * @access public
	 */
	public static function instance()
	{
		if (!self::is_compatible()) {
			return;
		}

		if (!isset(self::$instance) && !self::$instance instanceof SBI_Elementor_Base) {
			self::$instance = new SBI_Elementor_Base();
			self::$instance->apply_hooks();
		}
		return self::$instance;
	}

	/**
	 * Compatibility check.
	 *
	 * Check if the current environment is compatible with the plugin.
	 *
	 * @return bool True if the plugin can run, false otherwise.
	 * @since 6.2.9
	 */
	public static function is_compatible()
	{
		// Check if Elementor is installed and activated.
		if (!did_action('elementor/loaded')) {
			return false;
		}

		// Check for required Elementor version.
		if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
			return false;
		}

		// Check for required PHP version.
		if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
			return false;
		}

		return true;
	}

	/**
	 * Apply hooks.
	 *
	 * @since 6.2.9
	 * @access private
	 */
	private function apply_hooks()
	{
		add_action('elementor/frontend/after_register_scripts', array($this, 'register_frontend_scripts'));
		add_action('elementor/frontend/after_register_styles', array($this, 'register_frontend_styles'), 10);
		add_action('elementor/frontend/after_enqueue_styles', array($this, 'enqueue_frontend_styles'), 10);

		add_action('elementor/controls/register', array($this, 'register_controls'));
		add_action('elementor/widgets/register', array($this, 'register_widgets'));
		add_action('elementor/elements/categories_registered', array($this, 'add_smashballon_categories'));
	}

	/**
	 * Add Smash Balloon categories.
	 *
	 * Add the Smash Balloon category to the Elementor widget categories.
	 *
	 * @param object $elements_manager The Elementor elements manager.
	 * @since 6.2.9
	 * @access public
	 */
	public function add_smashballon_categories($elements_manager)
	{
		$elements_manager->add_category(
			'smash-balloon',
			[
				'title' => esc_html__('Smash Balloon', 'instagram-feed'),
				'icon' => 'fa fa-plug',
			]
		);
	}

	/**
	 * Register widgets.
	 *
	 * Register the Elementor widgets.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function register_widgets($widgets_manager)
	{
		$widgets_manager->register(new SBI_Elementor_Widget());

		$installed_plugins = SBI_Feed_Builder::get_smashballoon_plugins_info();
		unset($installed_plugins['instagram']);

		foreach ($installed_plugins as $plugin) {
			if (!$plugin['installed']) {
				$plugin_class = str_replace('.', '\\', self::NAME_SPACE) . $plugin['class'];
				$widgets_manager->register(new $plugin_class());
			}
		}

		do_action('sbi_elementor_widgets_registered');
	}

	/**
	 * Register controls.
	 *
	 * Register the Elementor controls.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function register_controls($controls_manager)
	{
		$controls_manager->register(new SBI_Feed_Elementor_Control());
	}

	/**
	 * Register frontend scripts.
	 *
	 * Register the frontend scripts.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function register_frontend_scripts()
	{
		$upload = wp_upload_dir();
		$resized_url = trailingslashit($upload['baseurl']) . trailingslashit(SBI_UPLOADS_NAME);

		$js_options = array(
			'font_method' => 'svg',
			'placeholder' => trailingslashit(SBI_PLUGIN_URL) . 'img/placeholder.png',
			'resized_url' => $resized_url,
			'ajax_url' => admin_url('admin-ajax.php'),
		);

		// legacy settings.
		$path = Util::sbi_legacy_css_enabled() ? 'js/legacy/' : 'js/';

		wp_register_script(
			'sbiscripts',
			SBI_PLUGIN_URL . $path . 'sbi-scripts.min.js',
			array('jquery'),
			SBIVER,
			true
		);
		wp_localize_script('sbiscripts', 'sb_instagram_js_options', $js_options);

		$data_handler = array(
			'smashPlugins' => SBI_Feed_Builder::get_smashballoon_plugins_info(),
			'nonce' => wp_create_nonce('sbi-admin'),
			'ajax_handler' => admin_url('admin-ajax.php'),
		);

		wp_register_script(
			'elementor-handler',
			SBI_PLUGIN_URL . 'admin/assets/js/elementor-handler.js',
			array('jquery'),
			SBIVER,
			true
		);

		wp_localize_script('elementor-handler', 'sbHandler', $data_handler);


		wp_register_script(
			'elementor-preview',
			SBI_PLUGIN_URL . 'admin/assets/js/elementor-preview.js',
			array('jquery'),
			SBIVER,
			true
		);
	}

	/**
	 * Register frontend styles.
	 *
	 * Register the frontend styles.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function register_frontend_styles()
	{
		// legacy settings
		$path = Util::sbi_legacy_css_enabled() ? 'css/legacy/' : 'css/';

		wp_register_style(
			'sbistyles',
			SBI_PLUGIN_URL . $path . 'sbi-styles.min.css',
			array(),
			SBIVER
		);
	}

	/**
	 * Enqueue frontend styles.
	 *
	 * Enqueue the frontend styles.
	 *
	 * @since 6.2.9
	 * @access public
	 */
	public function enqueue_frontend_styles()
	{
		wp_enqueue_style('sbistyles');
	}
}
