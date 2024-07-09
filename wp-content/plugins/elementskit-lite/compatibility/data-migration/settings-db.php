<?php
namespace ElementsKit_Lite\Compatibility\Data_Migration;

defined('ABSPATH') || exit;

class Settings_Db {
	public function __construct() {

		$widget_list = \ElementsKit_Lite\Config\Widget_List::instance()->get_list();
		$this->migrate($widget_list, 'widget');

		$module_list = \ElementsKit_Lite\Config\Module_List::instance()->get_list();
		$this->migrate($module_list, 'module');

		// TODO - remove this after 3.10.0 release
		add_action( 'upgrader_process_complete', [$this, 'social_share_css_was_updated'], 10, 2 );
		add_action( 'upgrader_process_complete', [$this, 'team_widget_css_was_updated'], 10, 2 );
	}

	private function migrate($list, $type) {
		$list_db       = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option($type . '_list', array());
		$list_prepared = array();

		if (empty($list_db[0]) || is_array($list_db[0])) {
			return;
		}

		foreach ($list as $slug => $info) {
			if (isset($info['package']) && $info['package'] == 'pro-disabled') {
				continue;
			}

			if (isset($info['attributes']) && in_array('new', $info['attributes'])) {
				continue;
			}

			$info['status'] = (in_array($slug, $list_db) ? 'active' : 'inactive');

			$list_prepared[$slug] = $info;
		}

		\ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->save_option($type . '_list', $list_prepared);
	}

	public function social_share_css_was_updated($upgrader_object, $options){
		$our_plugin = 'elementskit-lite/elementskit-lite.php';
		if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach($options['plugins'] as $plugin) {
				if ($plugin == $our_plugin) {
					if ( !get_transient('social_share_css_was_updated')) {
						set_transient('social_share_css_was_updated', \ElementsKit_Lite::version());
						\Elementor\Plugin::$instance->files_manager->clear_cache();
					}
				}
			}
		}
	}
	public function team_widget_css_was_updated($upgrader_object, $options){
		$our_plugin = 'elementskit-lite/elementskit-lite.php';
		if ($options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach($options['plugins'] as $plugin) {
				if ($plugin == $our_plugin) {
					if ( !get_transient('team_widget_css_was_updated')) {
						set_transient('team_widget_css_was_updated', \ElementsKit_Lite::version());
						\Elementor\Plugin::$instance->files_manager->clear_cache();
					}
				}
			}
		}
	}
}