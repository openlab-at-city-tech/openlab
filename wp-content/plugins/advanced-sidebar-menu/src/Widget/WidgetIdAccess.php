<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * Trait for the `Widget\Id` interface.
 *
 * Allows access to the properties of \WP_Widget.
 *
 * @author OnPoint Plugins
 * @since  9.6.0
 *
 * Properties come from the \WP_Widget class.
 *
 * @property-read mixed|string $id_base
 * @property-read bool|string  $id
 * @property-read bool|int     $number
 *
 */
trait WidgetIdAccess {
	/**
	 * Get the base id from the `WP_Widget` class.
	 *
	 * Replacement for `$widget->id_base`.
	 *
	 * @return string
	 */
	public function get_id_base(): string {
		return $this->id_base;
	}


	/**
	 * Get the id from the `WP_Widget` class.
	 *
	 * Replacement for `$widget->id`.
	 *
	 * @return string
	 */
	public function get_id(): string {
		return (string) $this->id;
	}


	/**
	 * Get the widget number.
	 *
	 * Replacement for `$widget->number`.
	 *
	 * @return string
	 */
	public function get_widget_number(): string {
		return (string) $this->number;
	}
}
