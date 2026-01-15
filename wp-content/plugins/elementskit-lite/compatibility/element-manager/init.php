<?php
namespace ElementsKit_Lite\Compatibility\Element_Manager;

defined('ABSPATH') || exit;

class Init {

	public function __construct() {
		add_action('update_option_elementor_disabled_elements', [$this, 'sync_with_elementor'], 10, 2);
		add_action('elementskit/widgets/status/update', [$this, 'sync_with_elementskit']);
		add_action('upgrader_process_complete', [$this, 'reset_widget_statuses'], 10, 2);
	}

	public function sync_with_elementor($old_value, $new_value) {
		$disabled_elements = (array) get_option( 'elementor_disabled_elements', [] );

		if($new_value) {
			$widgets_to_disable = array_filter(
				$disabled_elements,
				function( $element ) {
					return preg_match( '/^(elementskit-|ekit-)/', $element );
				}
			);

			if($widgets_to_disable) {
				$this->disable_widgets($widgets_to_disable);
			}
		} else {
			// Re-enable all ElementsKit widgets if none are disabled
			$this->disable_widgets();
		}
	}

	public function disable_widgets($widgets_to_disable = []) {
		$elementskit_options = get_option('elementskit_options');
		if (!$elementskit_options) {
			return;
		}

		// Normalize widget keys by removing "elementskit-" or "ekit-" prefix
		$widgets_to_disable = array_map(function ($widget) {
			return preg_replace('/^(elementskit-|ekit-)/', '', $widget);
		}, $widgets_to_disable);

		// Correct widget names if needed
		$widgets_to_disable = array_map(function ($widget) {
			return $this->correct_widget_name($widget);
		}, $widgets_to_disable);

		$widgets_list = isset($elementskit_options['widget_list']) ? $elementskit_options['widget_list'] : [];

		if (is_array($widgets_list)) {
			$changed = false;

			foreach ($widgets_list as $widget_key => &$widget_data) {
				$new_status = in_array($widget_key, $widgets_to_disable, true) ? 'inactive' : 'active';

				// Only update if the status is different
				if (!isset($widget_data['status']) || $widget_data['status'] !== $new_status) {
					$widget_data['status'] = $new_status;
					$changed = true;
				}
			}

			unset($widget_data); // break reference

			// Save updated options only if changes were made
			if ($changed) {
				$elementskit_options['widget_list'] = $widgets_list;
				update_option('elementskit_options', $elementskit_options);
			}
		}
	}

	public function sync_with_elementskit($widgets_list) {
		if (empty($widgets_list) || !is_array($widgets_list)) {
			return;
		}

		// Current disabled elements (Elementor expects prefixed keys, e.g. elementskit-foo / ekit-foo)
		$disabled_widgets = (array) get_option('elementor_disabled_elements', []);
		$original = $disabled_widgets;

		foreach ($widgets_list as $key => $widget) {
			// Prefer $widget['slug'] if present; fallback to array key
			$raw_slug = isset($widget['slug']) && $widget['slug'] ? $widget['slug'] : $key;

			// Resolve to the canonical/expected slug before prefixing
			$slug = $this->correct_widget_name($raw_slug, true);

			$key_one = 'elementskit-' . $slug;
			$key_two = 'ekit-' . $slug;

			$status = isset($widget['status']) ? $widget['status'] : null;
			if ($status === null) {
				continue;
			}

			if ($status === 'inactive') {
				// Ensure both prefixed keys exist
				if ($this->is_registered_widget($key_one) && !isset($disabled_widgets[$key_one])) {
					$disabled_widgets[] = $key_one;
				}
				if ($this->is_registered_widget($key_two) && !isset($disabled_widgets[$key_two])) {
					$disabled_widgets[] = $key_two;
				}
			} else { // active -> ensure both are removed
				if (in_array($key_one, $disabled_widgets)) {
					$disabled_widgets = array_values(array_diff($disabled_widgets, [$key_one]));
				}
				if (in_array($key_two, $disabled_widgets)) {
					$disabled_widgets = array_values(array_diff($disabled_widgets, [$key_two]));
				}
			}
		}

		// Only write if something actually changed
		if ($disabled_widgets !== $original) {
			update_option('elementor_disabled_elements', $disabled_widgets);
		}
	}

	public function reset_widget_statuses($upgrader_object, $options) {
		$should_run = true;
		$our_plugin = 'elementskit-lite/elementskit-lite.php';
		if (!empty($options['plugins']) && $options['action'] == 'update' && $options['type'] == 'plugin' ) {
			foreach($options['plugins'] as $plugin) {
				if ($plugin != $our_plugin) {
					$should_run = false;
					break;
				}
			}
		}

		// Check if should run
		if(!$should_run) {
			return;
		}

		// Check if already updated
		$is_already_update = get_transient('ekit_element_manager_compatibity');
		if($is_already_update) {
			return;
		}

		// Run the update
		$elementskit_options = get_option('elementskit_options');
		if (!$elementskit_options) {
			return;
		}

		// Get all widgets
		$widgets_list = $elementskit_options['widget_list'] ?? [];
		$widgets_to_disable = array_filter($widgets_list, function ($widget) {
			return isset($widget['status']) && $widget['status'] === 'inactive';
		});

		if($widgets_to_disable) {
			$this->sync_with_elementskit($widgets_to_disable);
		}

		// Set transient to avoid running again
		set_transient('ekit_element_manager_compatibity', true);
	}

	public function correct_widget_name($slug = '', $reverse = false) {
		if($reverse) {
			if($slug === 'advanced-accordion') {
				return 'advance-accordion';
			}

			if($slug === 'advanced-tab') {
				return 'tab';
			}

			if($slug === 'social') {
				return 'social-media';
			}

			if($slug === 'tab') {
				return 'simple-tab';
			}
		} else {
			if($slug === 'advance-accordion') {
				return 'advanced-accordion';
			}

			if($slug === 'tab') {
				return 'advanced-tab';
			}

			if($slug === 'social-media') {
				return 'social';
			}

			if($slug === 'simple-tab') {
				return 'tab';
			}
		}

		return $slug;
	}

	public function is_registered_widget($id = '') {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		$widgets = $widgets_manager->get_widget_types();

		$widget_list = [];
		foreach ( $widgets as $widget ) {
			$widget_list[$widget->get_name()] = [
				'slug'   => $widget->get_name(),  // unique ID (e.g., 'heading', 'button', 'image-box')
				'title'=> $widget->get_title(), // display title
			];
		}

		if($id) {
			return isset($widget_list[$id]) ? $widget_list[$id] : null;
		}

		return $widget_list;
	}
}
