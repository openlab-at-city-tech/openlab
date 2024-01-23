<?php
namespace ElementsKit_Lite\Compatibility\Data_Migration;

defined('ABSPATH') || exit;

class Settings_Db {
	public function __construct() {

		$widget_list = \ElementsKit_Lite\Config\Widget_List::instance()->get_list();
		$this->migrate($widget_list, 'widget');

		$module_list = \ElementsKit_Lite\Config\Module_List::instance()->get_list();
		$this->migrate($module_list, 'module');

		// fix slick to swiper migration
		add_action('upgrader_process_complete', [$this, 'slick_to_swiper_migrate'], 10, 2);

		// migrate nav menu predefined icon to fontawesome icon
		add_action('upgrader_process_complete', [$this, 'nav_menu_icon_upgrader'], 10, 2);
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

	public function slick_to_swiper_migrate($upgrader_object, $options) {
		// Plugins to check for updates
		$plugins_to_check = [
			'elementskit-lite/elementskit-lite.php',
			'elementskit/elementskit.php',
		];

		// Check if any of the specified plugins are updated
		if (!empty($options['action']) && $options['action'] === 'update') {
			$updated_plugins = [];
			if (!empty($options['plugins']) && is_array($options['plugins'])) {
				$updated_plugins = $options['plugins'];
			}

			if (!empty($options['plugin']) && !is_array($options['plugin'])) {
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

	public function nav_menu_icon_upgrader($upgrader_object, $options) {
		// Plugins to check for updates
		$plugins_to_check = [
			'elementskit-lite/elementskit-lite.php',
		];

		// return if pro not active
		if (\ElementsKit_Lite::license_status() !== 'valid') return;

		// Check if any of the specified plugins are updated
		if (!empty($options['action']) && $options['action'] === 'update') {
			$updated_plugins = [];
			if (!empty($options['plugins']) && is_array($options['plugins'])) {
				$updated_plugins = $options['plugins'];
			}

			if (!empty($options['plugin']) && !is_array($options['plugin'])) {
				$updated_plugins[] = $options['plugin'];
			}

			foreach ($plugins_to_check as $plugin_slug) {
				if (in_array($plugin_slug, $updated_plugins)) {
					// upgrade for lite version
					if (!get_transient('ekit_lite_nav_menu_icon_migrate')) {
						set_transient('ekit_lite_nav_menu_icon_migrate', \ElementsKit_Lite::version());
						$this->nav_menu_icon_migrate();
					}
				}
			}
		}
	}

	public function nav_menu_icon_migrate() {
		global $wpdb;

		// upgrade `video` widget settings (merge providers).
		$post_ids = $wpdb->get_col(
			'SELECT `post_id` FROM `' . $wpdb->postmeta . '` WHERE `meta_key` = "_elementor_data" AND `meta_value` LIKE \'%"widgetType":"ekit-nav-menu"%\';'
		);

		if (empty($post_ids)) {
			return;
		};

		foreach ($post_ids as $post_id) {
			$do_update = false;
			$document  = \Elementor\Plugin::$instance->documents->get($post_id);

			if ($document) {
				$data = $document->get_elements_data();
			}

			if (empty($data)) {
				continue;
			}

			$data = \Elementor\Plugin::$instance->db->iterate_data($data, function ($element) use (&$do_update) {
				if (empty($element['widgetType']) || 'ekit-nav-menu' !== $element['widgetType'] || !empty($element['settings']['elementskit_submenu_indicator_icon'])) {
					return $element;
				}

				$replacements = [];

				if (!empty($element['settings']['elementskit_style_tab_submenu_item_arrow'])) {
					$replacements = [
						'elementskit_style_tab_submenu_item_arrow' => 'elementskit_submenu_indicator_icon',
					];
				}

				foreach ($replacements as $old => $new) {
					if (!empty($element['settings'][$old])) {
						switch ($element['settings'][$old]) {
						case 'elementskit_line_arrow':
							$element['settings'][$new] = [
								'value'   => 'icon icon-down-arrow1',
								'library' => 'ekiticons',
							];
							break;
						case 'elementskit_plus_icon':
							$element['settings'][$new] = [
								'value'   => 'icon icon-plus',
								'library' => 'ekiticons',
							];
							break;
						case 'elementskit_fill_arrow':
							$element['settings'][$new] = [
								'value'   => 'icon icon-arrow-point-to-down',
								'library' => 'ekiticons',
							];
							break;
						default:
							$element['settings'][$new] = [
								'value'   => '',
								'library' => '',
							];
						}

						// $element['settings'][$new] = $element['settings'][$old];
						$do_update = true;
					}
				}

				// cleanup old unused settings.
				if (!empty($element['settings']['elementskit_style_tab_submenu_item_arrow'])) {
					// unset($element['settings']['elementskit_style_tab_submenu_item_arrow']);
				}

				return $element;
			});

			// Only update if needed.
			if (!$do_update) {
				continue;
			}

			// We need the `wp_slash` in order to avoid the unslashing during the `update_post_meta`
			$json_value = wp_slash(wp_json_encode($data));

			update_metadata('post', $post_id, '_elementor_data', $json_value);

			// Clear WP cache for next step.
			wp_cache_flush();
		} // End foreach().
	}
}