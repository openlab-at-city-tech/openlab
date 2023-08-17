<?php
namespace ElementsKit_Lite\Compatibility\Data_Migration;

defined( 'ABSPATH' ) || exit;

class Settings_Db {
	public function __construct() {

		$widget_list = \ElementsKit_Lite\Config\Widget_List::instance()->get_list();
		$this->migrate( $widget_list, 'widget' );
		
		$module_list = \ElementsKit_Lite\Config\Module_List::instance()->get_list();
		$this->migrate( $module_list, 'module' );

		// fix slick to swiper migration
		add_action('upgrader_process_complete', [$this, 'slick_to_swiper_migrate'], 10, 2);
	}

	private function migrate( $list, $type ) {
		$list_db       = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( $type . '_list', array() );
		$list_prepared = array();
		
		if ( empty( $list_db[0] ) || is_array( $list_db[0] ) ) {
			return;
		}

		foreach ( $list as $slug => $info ) {
			if ( isset( $info['package'] ) && $info['package'] == 'pro-disabled' ) {
				continue;
			}

			if ( isset( $info['attributes'] ) && in_array( 'new', $info['attributes'] ) ) {
				continue;
			}

			$info['status'] = ( in_array( $slug, $list_db ) ? 'active' : 'inactive' );

			$list_prepared[ $slug ] = $info;
		}

		\ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->save_option( $type . '_list', $list_prepared );
	}

	public function slick_to_swiper_migrate($upgrader_object, $options) {
		// Plugins to check for updates
		$plugins_to_check = [
			'elementskit-lite/elementskit-lite.php',
			'elementskit/elementskit.php',
		];

		// Check if any of the specified plugins are updated
		if (!empty($options['action']) && $options['action'] === 'update') {
			$updated_plugins = [];
			if(!empty($options['plugins']) && is_array($options['plugins'])) {
				$updated_plugins = $options['plugins'];
			}

			if(!empty($options['plugin']) && !is_array($options['plugin'])) {
				$updated_plugins[] = $options['plugin'];
			}

			foreach ($plugins_to_check as $plugin_slug) {
				if (in_array($plugin_slug, $updated_plugins)) {
					// upgrade for lite version
					if (!get_transient('ekit_lite_slick_to_swiper_migrate')) {
						set_transient('ekit_lite_slick_to_swiper_migrate', \ElementsKit_Lite::version());
						\Elementor\Plugin::$instance->files_manager->clear_cache();
					}
	
					// upgrade for pro version
					if (
						method_exists('ElementsKit', 'version') 
						&& !get_transient('ekit_slick_to_swiper_migrate') 
						&& version_compare(\ElementsKit::version(), '3.2.1', '>')
						&& version_compare(\ElementsKit_Lite::version(), '2.8.8', '>')
						) {
							set_transient('ekit_slick_to_swiper_migrate', \ElementsKit::version());
							\Elementor\Plugin::$instance->files_manager->clear_cache();
					}
				}
			}
		}
	}
}