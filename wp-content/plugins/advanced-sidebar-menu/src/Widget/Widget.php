<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * Interface for the widget.
 *
 * @since  9.5.0
 *
 * @todo  Once the required basic version is 9.5.0+, remove all PRO uses of Widget_Abstract in
 *         favor of this interface, then add a todo to remove the
 *        Widget_Abstract from the basic version once the required PRO version is of the new PRO version of that time.
 *
 *
 * @notice This interface should never change unless a major version is released.
 *
 * @phpstan-template SETTINGS of array
 * @phpstan-template DEFAULTS of array
 */
interface Widget {
	/**
	 * Store the instance to this class.
	 * We do this manually because filters hit the instance before we
	 * get to self::form() and self::widget()
	 *
	 * @see   \WP_Widget::form_callback()
	 *
	 * @phpstan-param SETTINGS $instance
	 * @phpstan-param DEFAULTS $defaults
	 *
	 * @param array            $instance - widget settings.
	 * @param array            $defaults - defaults for all widgets.
	 *
	 * @phpstan-return SETTINGS
	 * @return array
	 */
	public function set_instance( array $instance, array $defaults ): array;


	/**
	 * Is this checkbox checked?
	 *
	 * Checks first for a value then verifies the value = 'checked'.
	 *
	 * @param string $name - Name of checkbox.
	 *
	 * @return bool
	 */
	public function checked( $name ): bool;


	/**
	 * Hide an element_key if a controlling_checkbox is checked.
	 *
	 * @param string $controlling_checkbox - Name of controlling_checkbox field which controls whether to hide this
	 *                                     element or not.
	 * @param string $element_key          - Match the `element_to_reveal` passed to $this->checkbox() for the checkbox
	 *                                     which controls this.
	 * @param bool   $reverse              - hide on check instead of show on check.
	 *
	 * @return void
	 */
	public function hide_element( $controlling_checkbox, $element_key = null, $reverse = false ): void;


	/**
	 * Outputs a <input type="checkbox"> with id and name filled.
	 *
	 * @param string      $name              - Name of field.
	 * @param string|null $element_to_reveal - Element to reveal/hide when box is checked/unchecked.
	 *
	 * @return void
	 */
	public function checkbox( $name, $element_to_reveal = null ): void;
}
