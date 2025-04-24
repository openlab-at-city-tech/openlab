<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Widget\Widget;

/**
 * Rules for a Menu class.
 *
 * Previously managed by the being phased out Menu_Abstract.
 *
 * @author OnPoint Plugins
 * @since  9.5.0
 *
 * @phpstan-import-type WIDGET_ARGS from Widget
 * @phpstan-template SETTINGS of array<string, string|int|array<string|int, string>>
 * @phpstan-template INTERFACED_CLASS
 */
interface Menu {
	/**
	 * Is this item excluded from this menu?
	 *
	 * @param int|string $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ): bool;


	/**
	 * Get id of the highest level parent item.
	 *
	 * @return ?int
	 */
	public function get_top_parent_id();


	/**
	 * Get key to order the menu items by.
	 *
	 * @return string
	 */
	public function get_order_by();


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	public function get_order();


	/**
	 * Should this widget be displayed.
	 *
	 * @return bool
	 */
	public function is_displayed();


	/**
	 * How many levels should be displayed.
	 *
	 * @return int
	 */
	public function get_levels_to_display();


	/**
	 * Render the widget
	 *
	 * @return void
	 */
	public function render();


	/**
	 * Get current menu instance.
	 *
	 * @phpstan-return INTERFACED_CLASS|null
	 */
	public static function get_current();


	/**
	 * Constructs a new instance of this class.
	 *
	 * @phpstan-param WIDGET_ARGS $widget_args
	 * @phpstan-param SETTINGS    $widget_instance
	 *
	 * @param array               $widget_instance - Widget settings.
	 * @param array               $widget_args     - Widget registration args.
	 *
	 * @phpstan-return INTERFACED_CLASS
	 */
	public static function factory( array $widget_instance, array $widget_args );
}
