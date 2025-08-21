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
		add_action( 'upgrader_process_complete', [$this, 'clear_cache'], 10, 2 );
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

	public function clear_cache($upgrader_object, $options){
		$our_plugin = 'elementskit-lite/elementskit-lite.php';
		if (!empty($options['plugins']) && $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach($options['plugins'] as $plugin) {
				if ($plugin == $our_plugin) {
					$this->regenerate_widget_builder_widgets();
				}
			}
		}
	}

	public function regenerate_widget_builder_widgets() {
		$args = array(
			'post_type'      => 'elementskit_widget',
			'post_status'    => 'publish', // Only get published posts
			'posts_per_page' => -1,
		);

		$posts = get_posts($args);
	
		if ($posts) {
			foreach ($posts as $post) {
				$id = $post->ID;
				$widget_data = get_post_meta($id, 'elementskit_custom_widget_data', true);
				if(!empty($widget_data) && is_object( $widget_data )) {
					\ElementsKit_Lite\Modules\Widget_Builder\Widget_File::instance()->create( $widget_data, $id );
				}
			}
		}
	}
}