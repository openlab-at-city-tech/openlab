<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * @author OnPoint Plugins
 * @since  9.6.0
 *
 * @phpstan-template SETTINGS of array<string, mixed>
 */
interface WidgetId {
	/**
	 * Get the base id from the `WP_Widget` class.
	 *
	 * Replacement for `$widget->id_base`.
	 *
	 * @return string
	 */
	public function get_id_base(): string;


	/**
	 * Get the id from the `WP_Widget` class.
	 *
	 * Replacement for `$widget->id`.
	 *
	 * @return string
	 */
	public function get_id(): string;


	/**
	 * Get the widget number.
	 *
	 * Replacement for `$widget->number`.
	 *
	 * @return string
	 */
	public function get_widget_number(): string;


	/**
	 * Get the field id from the \WP_Widget class.
	 *
	 * @notice Definition must be compatible with \WP_Widget::get_field_id().
	 *
	 * @phpstan-param key-of<SETTINGS> $field_name
	 *
	 * @param string                   $field_name - Name of field.
	 *
	 * @return string
	 */
	public function get_field_id( $field_name );


	/**
	 * Get the field name from the \WP_Widget class.
	 *
	 * @notice Definition must be compatible with \WP_Widget::get_field_name().
	 *
	 * @phpstan-param key-of<SETTINGS> $field_name
	 *
	 * @param string                   $field_name - Name of field.
	 *
	 * @return string
	 */
	public function get_field_name( $field_name );
}
