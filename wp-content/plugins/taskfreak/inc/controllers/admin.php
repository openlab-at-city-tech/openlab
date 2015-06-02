<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

*/

class tfk_admin extends tzn_controller {
	
	public function __constuct() {
		include TFK_ROOT_PATH.'inc/controllers/admin/project_ajax.php';
	}
	
	public static function init() {
		$c = __CLASS__;
		$obj = new $c();
		add_action('admin_menu', array($obj, 'init_menu'));
		add_filter('plugin_action_links', array($obj,'init_plugin_links'), 10, 2);
	}

	/**
	 * Create admin menu
	 */
	public function init_menu() {
		// menu section
		add_menu_page('TaskFreak!', 'TaskFreak!', 'read', 'taskfreak', array($this,'page_dashboard'), plugins_url('/img/logo.16.png', TFK_ROOT_FILE), 55);
		// submenu
		add_submenu_page('taskfreak', __('Administrator Dashboard', 'taskfreak'), __('Dashboard', 'taskfreak'), 'read', 'taskfreak', array($this, 'page_dashboard')); // all WP users
		add_submenu_page('taskfreak', __('Manage projects', 'taskfreak'), __('Projects', 'taskfreak'), 'publish_posts', 'taskfreak_projects', array($this, 'page_projects')); // author
		add_submenu_page('taskfreak', __('Settings & Options', 'taskfreak'), __('Settings', 'taskfreak'), 'manage_options', 'taskfreak_settings', array($this, 'page_settings')); // administrator
	}
	
	/**
	 * Dashboard page
	 */
	public function page_dashboard() {
		$this->call('admin/dashboard.php');
	}
	
	/**
	 * Projects page
	 * list, create, edit or delete project
	 */
	public function page_projects() {
		$this->options = get_option('tfk_options');
		$this->linkadmin = '?page=taskfreak_projects';
		$this->linkfront = add_query_arg('mode', 'projects', $this->options['page_url']);
		$this->is_manager = tfk_user::check_role('editor');
		if (empty($_REQUEST['id'])) {
			// list projects by default
			// author, editor and admin
			$this->call('admin/project_list.php');
		} else if ($this->is_manager) {
			// only editor and admin can add/edit projects (any projects)
			$this->call('admin/project_edit.php');
		}
	}
	
	/**
	 * Global settings page
	 */
	public function page_settings() {
		$this->baselink = '?page=taskfreak_settings';
		$this->call('admin/settings.php');
	}
	
	/**
	 * Add "settings" link to plugin
	 */
	public function init_plugin_links($links, $file) {
		static $this_plugin;
	    if (!$this_plugin) {
	        $this_plugin = plugin_basename(TFK_ROOT_PATH);
	    }
	    if (preg_match('/^'.$this_plugin.'/', $file)) {
	        $settings_link = '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=taskfreak_settings">Settings</a>';
	        array_unshift($links, $settings_link);
	    }	
	    return $links;
	}
	
}

tfk_admin::init();