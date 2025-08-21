<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * Interface for the widget.
 *
 * @since  9.5.0
 *
 * @notice This interface should never change unless a major version is released.
 *
 * @phpstan-type WIDGET_ARGS array{
 *       name?:          string,
 *       id?:            string,
 *       id_increment?:  string,
 *       description?:   string,
 *       class?:         string,
 *       before_widget:  string,
 *       after_widget:   string,
 *       before_title:   string,
 *       after_title:    string,
 *       before_sidebar?:string,
 *       after_sidebar?: string,
 *       show_in_rest?:  boolean,
 *       widget_id?:     string,
 *       widget_name?:   string,
 *  }
 *
 * @template SETTINGS of array<string, string|int|bool|array<string, string>>
 * @template DEFAULTS of array<key-of<SETTINGS>, int|string|array<string, string>>
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
	 * Checks first for a value, then verifies the value = 'checked'.
	 *
	 * @param string $name - Name of checkbox.
	 *
	 * @return bool
	 */
	public function checked( $name ): bool;


	/**
	 * Hide an element_key if a controlling_checkbox is checked.
	 *
	 * @param string  $controlling_checkbox                       - Name of controlling_checkbox field which controls whether to hide this
	 *                                                            element or not.
	 * @param ?string $element_key                                - Match the `element_to_reveal` passed to $this->checkbox() for the
	 *                                                            checkbox which controls this.
	 * @param bool    $reverse                                    - hide on check instead of show on check.
	 *
	 * @return void
	 */
	public function hide_element( $controlling_checkbox, $element_key = null, $reverse = false ): void;


	/**
	 * Outputs a <input type="checkbox"> with the id and name filled.
	 *
	 * @param string  $name              - Name of field.
	 * @param ?string $element_to_reveal - Element to reveal/hide when a box is checked/unchecked.
	 *
	 * @return void
	 */
	public function checkbox( $name, $element_to_reveal = null ): void;


	/**
	 * Echoes the widget content.
	 *
	 * @phpstan-param WIDGET_ARGS $args
	 * @phpstan-param SETTINGS    $instance
	 *
	 * @param array               $args     Display arguments including 'before_title', 'after_title',
	 *                                      'before_widget', and 'after_widget'.
	 * @param array               $instance The settings for the particular instance of the widget.
	 *
	 * @return void
	 */
	public function widget( $args, $instance );
}
