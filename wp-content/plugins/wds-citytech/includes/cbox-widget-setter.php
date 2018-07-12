<?php

/**
 * Utility class for dealing with sidebar widgets
 */
class CBox_Widget_Setter {
	public static function set_widget( $args ) {
		$r = wp_parse_args( $args, array(
			'id_base' => '',
			'sidebar_id' => '',
			'settings' => array(),
		) );

		$id_base    = $r['id_base'];
		$sidebar_id = $r['sidebar_id'];
		$settings   = (array) $r['settings'];

		$widget_options = $option_keys = get_option( 'widget_' . $id_base, array() );
		if ( ! isset( $widget_options['_multiwidget'] ) ) {
			$widget_options['_multiwidget'] = 1;
		}
		unset( $option_keys['_multiwidget'] );
		$option_keys = array_keys( $option_keys );
		$last_key = array_pop( $option_keys );
		$option_index = $last_key + 1;
		$widget_options[ $option_index ] = self::sanitize_widget_options( $id_base, $settings, array() );
		update_option( 'widget_' . $id_base, $widget_options );

		$widget_id = $id_base . '-' . $option_index;
		self::move_sidebar_widget( $widget_id, null, $sidebar_id, null, $option_index );
		return;

		// Don't try to set a widget if it hasn't been registered
		if ( ! self::widget_exists( $id_base ) ) {
			return new WP_Error( 'widget_does_not_exist', 'Widget does not exist' );
		}

		global $wp_registered_sidebars;
		if ( ! isset( $wp_registered_sidebars[ $sidebar_id ] ) ) {
			return new WP_Error( 'sidebar_does_not_exist', 'Sidebar does not exist' );
		}

		$sidebars = wp_get_sidebars_widgets();

		$sidebar = isset( $sidebar[ $sidebar_id ] ) ? $sidebars[ $sidebar_id ] : array();

		// Reset multiwidgets to updated=false, or `update_callback()` will fail.
		global $wp_registered_widgets;
		foreach ( $wp_registered_widgets as $primary_widget_id => $primary_widget ) {
			if ( 0 !== strpos( $primary_widget_id, $id_base ) ) {
				continue;
			}

			if ( ! isset( $primary_widget['callback'][0] ) || ! ( $primary_widget['callback'][0] instanceof WP_Widget ) ) {
				continue;
			}

			$primary_widget['callback'][0]->updated = false;
		}

		// Multi-widgets can only be detected by looking at their settings
		$option_name  = 'widget_' . $id_base;

		// Don't let it get pulled from the cache
		wp_cache_delete( $option_name, 'options' );
		$all_settings = get_option( $option_name );

		if ( is_array( $all_settings ) ) {
			$skeys = array_keys( $all_settings );

			// Find the highest numeric key
			rsort( $skeys );

			foreach ( $skeys as $k ) {
				if ( is_numeric( $k ) ) {
					$multi_number = $k + 1;
					break;
				}
			}

			if ( ! isset( $multi_number ) ) {
				$multi_number = 1;
			}

			$all_settings[ $multi_number ] = $settings;
			//$all_settings = array( $multi_number => $settings );
		} else {
			$multi_number = 1;
			$all_settings = array( $multi_number => $settings );
		}


		$widget_id = $id_base . '-' . $multi_number;
		$sidebar[] = $widget_id;

		$all_settings = array_filter( $all_settings );

		// Because of the way WP_Widget::update_callback() works, gotta fake the $_POST
		$_POST['widget-' . $id_base] = $all_settings;

		global $wp_registered_widget_updates, $wp_registered_widget_controls;
		foreach ( (array) $wp_registered_widget_updates as $name => $control ) {

			if ( $name == $id_base ) {
				if ( !is_callable( $control['callback'] ) )
					continue;

				if ( isset( $control['callback'][0] ) && ( $control['callback'][0] instanceof WP_Widget ) ) {
					$control['callback'][0]->updated = false;
				}

				ob_start();
					call_user_func_array( $control['callback'], $control['params'] );
				ob_end_clean();
				break;
			}
		}

		$sidebars[ $sidebar_id ] = $sidebar;
		wp_set_sidebars_widgets( $sidebars );

		return update_option( $option_name, $all_settings );
	}

	/**
	 * Checks to see whether a sidebar is already populated
	 */
	public static function is_sidebar_populated( $sidebar_id ) {
		$sidebars = wp_get_sidebars_widgets();
		return ! empty( $sidebars[ $sidebar_id ] );
	}

	/**
	 * Moves all active widgets from a given sidebar into the inactive array
	 */
	public static function clear_sidebar( $sidebar_id, $delete_to = 'inactive' ) {
		global $_wp_sidebars_widgets, $sidebars_widgets;

		$sidebars = wp_get_sidebars_widgets();
		if ( ! isset( $sidebars[ $sidebar_id ] ) ) {
			return new WP_Error( 'sidebar_does_not_exist', 'Sidebar does not exist' );
		}

		if ( 'inactive' == $delete_to ) {
			$sidebars['wp_inactive_widgets'] = array_unique( array_merge( $sidebars['wp_inactive_widgets'], $sidebars[ $sidebar_id ] ) );
		}

		$sidebars[ $sidebar_id ] = array();
		$_wp_sidebars_widgets[ $sidebar_id ] = array();
		$sidebars_widgets[ $sidebar_id ] = array();
		wp_set_sidebars_widgets( $sidebars );
	}

	/**
	 * Check to see whether a widget has been registered
	 *
	 * @param string $id_base
	 * @return bool
	 */
	public static function widget_exists( $id_base ) {
		global $wp_widget_factory;

		foreach ( $wp_widget_factory->widgets as $w ) {
			if ( $id_base == $w->id_base ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get a widget's instantiated object based on its name
	 *
	 * @param string $id_base Name of the widget
	 * @return WP_Widget|false
	 */
	protected static function get_widget_obj( $id_base ) {
		global $wp_widget_factory;

		$widget = wp_filter_object_list( $wp_widget_factory->widgets, array( 'id_base' => $id_base ) );
		if ( empty( $widget ) ) {
			false;
		}

		return array_pop( $widget );
	}

	/**
	 * Clean up a widget's options based on its update callback
	 *
	 * @param string $id_base Name of the widget
	 * @param mixed $dirty_options
	 * @param mixed $old_options
	 * @return mixed
	 */
	protected static function sanitize_widget_options( $id_base, $dirty_options, $old_options ) {

		$widget = self::get_widget_obj( $id_base );
		if ( empty( $widget ) ) {
			return array();
		}

		// No easy way to determine expected array keys for $dirty_options
		// because Widget API dependent on the form fields
		return @$widget->update( $dirty_options, $old_options );

	}

	/**
	 * Reposition a widget within a sidebar or move to another sidebar.
	 *
	 * @param string $widget_id
	 * @param string|null $current_sidebar_id
	 * @param string $new_sidebar_id
	 * @param int|null $current_index
	 * @param int $new_index
	 */
	protected static function move_sidebar_widget( $widget_id, $current_sidebar_id, $new_sidebar_id, $current_index, $new_index ) {

		$all_widgets = self::wp_get_sidebars_widgets();
		$needs_placement = true;
		// Existing widget
		if ( $current_sidebar_id && ! is_null( $current_index ) ) {

			$widgets = $all_widgets[ $current_sidebar_id ];
			if ( $current_sidebar_id !== $new_sidebar_id ) {

				unset( $widgets[ $current_index ] );

			} else {

				$part = array_splice( $widgets, $current_index, 1 );
				array_splice( $widgets, $new_index, 0, $part );

				$needs_placement = false;

			}

			$all_widgets[ $current_sidebar_id ] = array_values( $widgets );

		}

		if ( $needs_placement ) {
			$widgets = ! empty( $all_widgets[ $new_sidebar_id ] ) ? $all_widgets[ $new_sidebar_id ] : array();
			$before = array_slice( $widgets, 0, $new_index, true );
			$after = array_slice( $widgets, $new_index, count( $widgets ), true );
			$widgets = array_merge( $before, array( $widget_id ), $after );
			$all_widgets[ $new_sidebar_id ] = array_values( $widgets );
		}

		update_option( 'sidebars_widgets', $all_widgets );

	}

	/**
	 * Re-implementation of wp_get_sidebars_widgets()
	 * because the original has a nasty global component
	 */
	protected static function wp_get_sidebars_widgets() {
		$sidebars_widgets = get_option( 'sidebars_widgets', array() );

		if ( is_array( $sidebars_widgets ) && isset( $sidebars_widgets['array_version'] ) ) {
			unset( $sidebars_widgets['array_version'] );
		}

		return $sidebars_widgets;
	}
}
