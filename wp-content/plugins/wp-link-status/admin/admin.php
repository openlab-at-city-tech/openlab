<?php

// Load dependencies
require_once(dirname(dirname(__FILE__)).'/core/module.php');

/**
 * WP Link Status Admin class
 *
 * @package WP Link Status
 * @subpackage WP Link Status Admin
 */
class WPLNST_Admin extends WPLNST_Core_Module {



	// Properties
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Handle submitted scan object
	 */
	public $scan_submit;



	/**
	 * Version for external calls
	 */
	protected $script_version;



	// Initialization
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Creates a singleton object
	 */
	public static function instantiate($args = null) {
		return self::get_instance(get_class(), $args);
	}



	/**
	 * Custom constructor
	 */
	protected function on_construct($args = null) {
		
		// Dependencies
		wplnst_require('core', 'text');
		
		// Load translations
		add_action('plugins_loaded', array('WPLNST_Core_Plugin', 'load_plugin_textdomain'));
		
		// Check AJAX Mode
		if (defined('DOING_AJAX') && DOING_AJAX) {
		
			// Check this plugin action
			if (!empty($_POST['action']) && 0 === strpos($_POST['action'], 'wplnst_')) {
				
				// Check suffix
				$suffix = mb_substr($_POST['action'], 7);
				if (!empty($suffix) && method_exists($this, 'ajax_'.$suffix)) {
					
					// Set AJAX handler
					add_action('wp_ajax_'.$_POST['action'], array(&$this, 'ajax_'.$suffix));
				}
			}
		
		// Continue
		} else {
		
			// Check submit
			$this->scans_edit_submit_check();
			
			// Menu
			add_action('admin_menu', array(&$this, 'admin_menu'));
			
			// Enqueues
			add_action('admin_enqueue_scripts', array(&$this, 'admin_enqueue'));
			
			// Screen options
			add_filter('set-screen-option', array(&$this, 'options_screen_set'), 11, 3);
		}
	}



	/**
	 * Enqueue scripts and styles
	 */
	public function admin_enqueue() {
		
		// Check plugin context
		if (!self::is_plugin_page())
			return;
		
		// Compose script version
		$this->script_version = self::get_script_version();
		
		// Commmon admin styles
		wp_enqueue_style('wplnst-admin-css', plugins_url('assets/css/admin.css', WPLNST_FILE), array(), $this->script_version);
		
		// jQuery Lightboxed plugin
		wp_enqueue_script('wplnst-admin-lighboxed', plugins_url('assets/js/lightboxed/jquery.lightboxed.min.js', WPLNST_FILE), array('jquery'), $this->script_version, true);
		
		// Common admin script
		wp_enqueue_script('wplnst-admin-script', plugins_url('assets/js/admin.js', WPLNST_FILE), array('jquery'), $this->script_version, true);
		
		// Edit scan script
		if (WPLNST_Core_Plugin::slug.'-new-scan' == $_GET['page'] || (!empty($_GET['context']) && 'edit' == $_GET['context']))
			wp_enqueue_script('wplnst-admin-script-edit', plugins_url('assets/js/admin-edit.js', WPLNST_FILE), array('jquery', 'json2'), $this->script_version, true);
		
		// Enqueue version specific scripts
		$this->admin_enqueue_version();
		
		// Screen options
		$this->options_screen_add();
	}



	/**
	 * Enqueue specific versions scripts
	 */
	protected function admin_enqueue_version() {
		// Need to override, but do nothing
	}



	/**
	 * Admin menu hooks
	 */
	public function admin_menu() {
		
		// Base 64 encoded SVG image
		$icon_svg = 'data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiIHN0YW5kYWxvbmU9Im5vIj8+PHN2ZyAgIHhtbG5zOmRjPSJodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLyIgICB4bWxuczpjYz0iaHR0cDovL2NyZWF0aXZlY29tbW9ucy5vcmcvbnMjIiAgIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyIgICB4bWxuczpzdmc9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiAgIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgICB4bWxuczpzb2RpcG9kaT0iaHR0cDovL3NvZGlwb2RpLnNvdXJjZWZvcmdlLm5ldC9EVEQvc29kaXBvZGktMC5kdGQiICAgeG1sbnM6aW5rc2NhcGU9Imh0dHA6Ly93d3cuaW5rc2NhcGUub3JnL25hbWVzcGFjZXMvaW5rc2NhcGUiICAgdmVyc2lvbj0iMS4xIiAgIHg9IjBweCIgICB5PSIwcHgiICAgdmlld0JveD0iMCAwIDEwMCAxMDAiICAgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTAwIDEwMCIgICB4bWw6c3BhY2U9InByZXNlcnZlIiAgIGlkPSJzdmcyIiAgIGlua3NjYXBlOnZlcnNpb249IjAuNDguNCByOTkzOSIgICB3aWR0aD0iMTAwJSIgICBoZWlnaHQ9IjEwMCUiICAgc29kaXBvZGk6ZG9jbmFtZT0ibGluay1zdGF0dXMtNy5zdmciPjxtZXRhZGF0YSAgICAgaWQ9Im1ldGFkYXRhMTAiPjxyZGY6UkRGPjxjYzpXb3JrICAgICAgICAgcmRmOmFib3V0PSIiPjxkYzpmb3JtYXQ+aW1hZ2Uvc3ZnK3htbDwvZGM6Zm9ybWF0PjxkYzp0eXBlICAgICAgICAgICByZGY6cmVzb3VyY2U9Imh0dHA6Ly9wdXJsLm9yZy9kYy9kY21pdHlwZS9TdGlsbEltYWdlIiAvPjwvY2M6V29yaz48L3JkZjpSREY+PC9tZXRhZGF0YT48ZGVmcyAgICAgaWQ9ImRlZnM4IiAvPjxzb2RpcG9kaTpuYW1lZHZpZXcgICAgIHBhZ2Vjb2xvcj0iI2ZmZmZmZiIgICAgIGJvcmRlcmNvbG9yPSIjNjY2NjY2IiAgICAgYm9yZGVyb3BhY2l0eT0iMSIgICAgIG9iamVjdHRvbGVyYW5jZT0iMTAiICAgICBncmlkdG9sZXJhbmNlPSIxMCIgICAgIGd1aWRldG9sZXJhbmNlPSIxMCIgICAgIGlua3NjYXBlOnBhZ2VvcGFjaXR5PSIwIiAgICAgaW5rc2NhcGU6cGFnZXNoYWRvdz0iMiIgICAgIGlua3NjYXBlOndpbmRvdy13aWR0aD0iMTY4MCIgICAgIGlua3NjYXBlOndpbmRvdy1oZWlnaHQ9Ijk3NCIgICAgIGlkPSJuYW1lZHZpZXc2IiAgICAgc2hvd2dyaWQ9ImZhbHNlIiAgICAgaW5rc2NhcGU6em9vbT0iMi4zNiIgICAgIGlua3NjYXBlOmN4PSItMjguNjAxNjk1IiAgICAgaW5rc2NhcGU6Y3k9IjUwIiAgICAgaW5rc2NhcGU6d2luZG93LXg9IjAiICAgICBpbmtzY2FwZTp3aW5kb3cteT0iMjQiICAgICBpbmtzY2FwZTp3aW5kb3ctbWF4aW1pemVkPSIxIiAgICAgaW5rc2NhcGU6Y3VycmVudC1sYXllcj0ic3ZnMiIgLz48cGF0aCAgICAgZD0iTTUwLDBDMjIuNCwwLDAsMjIuNCwwLDUwczIyLjQsNTAsNTAsNTBzNTAtMjIuNCw1MC01MFM3Ny42LDAsNTAsMHogTTU1LjgsNjUuM0w0NCw3N2MtMy4yLDMuMi03LjUsNS0xMS44LDVoMCAgYy0zLjksMC03LjUtMS41LTEwLjItNC4xYy01LjgtNS44LTUuOC0xNS4zLDAtMjEuMWwxNC4xLTE0LjFMMzYsNDQuOWMtMC4yLDIuNiwwLjEsNS4xLDAuOSw3LjRsMC4yLDAuNWwtOS41LDkuNSAgYy0xLjQsMS40LTIuMSwzLjItMi4xLDUuMWMwLDEuOSwwLjcsMy43LDIuMSw1LjFjMS4yLDEuMiwyLjksMS45LDQuNywxLjljMi4zLDAsNC42LTEsNi4zLTIuN2wxMS43LTExLjdjMy4zLTMuMywzLjctOC4yLDAuOS0xMSAgYy0wLjctMC43LTEtMS45LTAuOC0zLjFjMC4yLTEuMiwwLjgtMi4zLDEuNy0zLjJsMS41LTEuNWwwLjUsMC4zYzAuOSwwLjYsMS44LDEuMywyLjUsMmMwLjgsMC44LDEuNCwxLjYsMiwyLjYgIEM2Mi4zLDUxLjksNjEuMSw2MCw1NS44LDY1LjN6IE03Ny45LDQzLjJMNjMuOCw1Ny4zbDAuMi0yLjFjMC4yLTIuNi0wLjEtNS4xLTAuOS03LjRMNjMsNDcuMmw5LjUtOS41YzEuNC0xLjQsMi4xLTMuMiwyLjEtNS4xICBjMC0xLjktMC43LTMuNy0yLjEtNS4xYy0xLjItMS4yLTIuOS0xLjktNC43LTEuOWMtMi4zLDAtNC42LDEtNi4zLDIuN0w0OS43LDQwLjFjLTMuMywzLjMtMy43LDguMi0wLjksMTFjMC43LDAuNywxLDEuOSwwLjgsMy4xICBjLTAuMiwxLjItMC44LDIuMy0xLjcsMy4yTDQ2LjQsNTlsLTAuNS0wLjNjLTAuOS0wLjYtMS44LTEuMy0yLjUtMmMtMC44LTAuOC0xLjQtMS42LTItMi42Yy0zLjYtNS45LTIuNC0xNC4xLDIuOS0xOS40TDU2LDIzICBjMy4yLTMuMiw3LjUtNSwxMS44LTVjMy45LDAsNy41LDEuNSwxMC4yLDQuMWMyLjgsMi44LDQuNCw2LjYsNC40LDEwLjVDODIuMywzNi42LDgwLjcsNDAuNCw3Ny45LDQzLjJ6IiAgICAgaWQ9InBhdGg0IiAgICAgc3R5bGU9ImZpbGw6I2ZmZmZmZjtmaWxsLW9wYWNpdHk6MSIgLz48L3N2Zz4=';
		
		// Main menu
		add_menu_page($this->get_plugin_title(), $this->get_menu_title(), WPLNST_Core_Plugin::capability, WPLNST_Core_Plugin::slug, array(&$this, 'admin_menu_scans'), $icon_svg, '99.20226');
		
		// Scans
		add_submenu_page(WPLNST_Core_Plugin::slug, self::get_text('scans'), self::get_text('scans'), WPLNST_Core_Plugin::capability, WPLNST_Core_Plugin::slug, array(&$this, 'admin_menu_scans'));
		add_submenu_page(WPLNST_Core_Plugin::slug, self::get_text('scan_new'), self::get_text('scan_new'), WPLNST_Core_Plugin::capability, WPLNST_Core_Plugin::slug.'-new-scan', array(&$this, 'admin_menu_scans_new'));
		
		// Utilities
		$this->admin_menu_utilities();
		
		// Settings
		add_submenu_page(WPLNST_Core_Plugin::slug, self::get_text('settings'), self::get_text('settings'), WPLNST_Core_Plugin::capability, WPLNST_Core_Plugin::slug.'-settings', array(&$this, 'admin_menu_settings'));
		
		// Addons
		$this->admin_menu_addons();
	}



	/**
	 * Admin menu utilities
	 */
	protected function admin_menu_utilities() {}



	/**
	 * Admin menu addons
	 */
	protected function admin_menu_addons() {
		do_action('wplnst_admin_menu_addons');
		add_submenu_page(WPLNST_Core_Plugin::slug, __('Extensions', 'wplnst'), '<span style="color:#f18500">'.__('Extensions', 'wplnst').'</span>', WPLNST_Core_Plugin::capability, WPLNST_Core_Plugin::slug.'-extensions', array(&$this, 'admin_menu_extensions'));
	}



	// Menu hooks
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Scans common page
	 */
	public function admin_menu_scans() {
		wplnst_require('admin', 'scans');
		new WPLNST_Admin_Scans($this, 'context');
	}



	/*
	 * New or edit scan page
	 */
	public function admin_menu_scans_new() {
		wplnst_require('admin', 'scans');
		new WPLNST_Admin_Scans($this, 'edit');
	}



	/**
	 * Settings page
	 */
	public function admin_menu_settings() {
		wplnst_require('admin', 'settings');
		new WPLNST_Admin_Settings($this);
	}



	/**
	 * Extensions page
	 */
	public function admin_menu_extensions() {
		wplnst_require('admin', 'extensions');
		new WPLNST_Admin_Extensions($this);
	}



	// New or edit scan submit handler
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Check a submit attempt
	 */
	private function scans_edit_submit_check() {
		
		// Check plugin context
		if (!self::is_plugin_page())
			return;
		
		// Edit scan form, early check because maybe do a redirection for new scans
		if ((WPLNST_Core_Plugin::slug == $_GET['page'] && !empty($_GET['context']) && 'edit' == $_GET['context']) || WPLNST_Core_Plugin::slug.'-new-scan' == $_GET['page']) {
			
			// Check id and nonce submit
			if (isset($_POST['scan_id']) && isset($_POST['scan_edit_nonce']))
				add_action('admin_init', array(&$this, 'scans_edit_submit'));
		}
	}



	/**
	 * Check scan edit submit and save data
	 */
	public function scans_edit_submit() {
		$this->load_scans_object();
		wplnst_require('admin', 'scans-submit');
		$this->scan_submit = new WPLNST_Admin_Scans_Submit($this->scans);
	}



	// Options screen
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Add options to the admin screen
	 */
	private function options_screen_add() {
		
		// Check scans context
		if (WPLNST_Core_Plugin::slug == $_GET['page']) {
			
			// Scans list
			if (!isset($_GET['scan_id']) && empty($_GET['context'])) {
				$option_default = WPLNST_Core_Types::scans_per_page;
				$option_per_page = 'wplnst_scans_per_page';
				$option_label = __('Scans per page', 'wplnst');
			
			// Results list
			} elseif (isset($_GET['context']) && 'results' == $_GET['context']) {
				$option_default = WPLNST_Core_Types::scans_results_per_page;
				$option_per_page = 'wplnst_scan_results_per_page';
				$option_label = __('Crawler results per page', 'wplnst');
			}
		}
			
		// Check option result
		if (isset($option_label)) {
			add_screen_option('per_page', array(
				'default' => $option_default,
				'label'   => $option_label,
				'option'  => $option_per_page,
			));
		}
	}



	/**
	 * Set the screen options
	 */
	public function options_screen_set($status, $option, $value) {
		
		// Options names
		$allowed = array(
			'scans_per_page',
			'scan_results_per_page',
		);

		// Enum allowed
		foreach ($allowed as $name) {
			if ('wplnst_'.$name == $option)
				return $value;
		}
	}



	// Default view and exceptions
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Show default admin view
	 */
	public function screen_view($args) {
		
		// Set plugin title
		$args['plugin_title'] = $this->get_plugin_title();
		
		// Before the screen output
		$this->screen_view_before();
		
		// And show it
		self::screen_view_output($args);
	}



	/**
	 * Before the screen view output
	 */
	protected function screen_view_before() {
		// Need to override, but do nothing
	}



	/**
	 * Output the screen view
	 */
	private static function screen_view_output($args) {
		
		// Vars
		extract($args);
		
		?><div class="wrap wplnst-wrap">
			
			<h2 id="wplnst-title"><?php echo empty($title)? '' : $title.' - '; echo $plugin_title; if (!empty($add_item_text) && !empty($add_item_url)) : ?> <a class="add-new-h2" href="<?php echo esc_url($add_item_url); ?>"><?php echo esc_html($add_item_text); ?></a><?php endif; ?></h2>
			
			<?php if (!wplnst_is_curl_enabled()) : ?><div class="error notice"><p><?php _e('Not detected the required cURL module enabled. Please contact with your hosting provider in order to install cURL for PHP in this server.', 'wplnst'); ?></p></div><?php endif; ?>
			
			<?php if (!empty($notice_error))   : ?><div class="error notice"><p><?php echo $notice_error; ?></p></div><?php endif; ?>
			
			<?php if (!empty($notice_success)) : ?><div class="updated notice is-dismissible"><p><?php echo $notice_success; ?></p></div><?php endif; ?>
			
			<?php if (!empty($notice_warning)) : ?><div class="notice notice-warning is-dismissible"><p><?php echo $notice_warning; ?></p></div><?php endif; ?>
			
			<?php if (!empty($notice_crawler)) : ?><div class="updated notice is-dismissible"><p><?php echo $notice_crawler; ?></p></div><?php endif; ?>
			
			<?php if (!empty($wp_action)) do_action($wp_action, $args); ?>
			
		</div><?php
	}



	/**
	 * Common scan not found
	 */
	public function screen_scan_not_found($title) {
		$this->screen_view(array(
			'title' => $title,
			'notice_error' => self::get_text('scan_not_found'),
		));
	}



	/**
	 * Invalid data screen
	 */
	public function screen_invalid_data($title) {
		$this->screen_view(array(
			'title' => $title,
			'notice_error' => self::get_text('invalid_data'),
		));
	}



	/**
	 * Invalid data screen
	 */
	public function screen_invalid_nonce($title) {
		$this->screen_view(array(
			'title' => $title,
			'notice_error' => self::get_text('invalid_nonce'),
		));
	}



	/**
	 * Common scan not found
	 */
	public function screen_unknown_error($title) {
		$this->screen_view(array(
			'title' => $title,
			'notice_error' => self::get_text('unknown_error'),
		));
	}



	// Utilities
	// ---------------------------------------------------------------------------------------------------



	/**
	 * Wrapper of WPLNST_Core_Text class get_text method
	 */
	public static function get_text($name) {
		return WPLNST_Core_Text::get_text($name);
	}



	/**
	 * Return suffixed version for external scripts
	 */
	protected function get_script_version() {
		global $wp_version;
		return (empty($wp_version)? '' : 'wp-'.str_replace('http://', '', esc_url($wp_version)).'-').'wplnst-'.WPLNST_VERSION;
	}



	/**
	 * Return plugin title in admin menu
	 */
	protected function get_menu_title() {
		return WPLNST_Core_Plugin::title;
	}



	/**
	 * Return plugin title for default view
	 */
	protected function get_plugin_title() {
		return WPLNST_Core_Plugin::title;
	}



	/**
	 * Check if we are on any page of this plugin
	 */
	private static function is_plugin_page() {
		global $pagenow;
		return !(empty($pagenow) || 'admin.php' != $pagenow || empty($_GET['page']) || 0 !== strpos($_GET['page'], WPLNST_Core_Plugin::slug));
	}



}