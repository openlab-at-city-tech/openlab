<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * Shared widget instance logic.
 *
 * Done with a Trait
 * - Working to move from inheritance to composition.
 * - Supports passing in the settings and defaults.
 *
 * @since                 9.5.0
 *
 * @template SETTINGS of array<string, mixed>
 * @template DEFAULTS of array<key-of<SETTINGS>, mixed>
 */
trait Instance {
	/**
	 * The current widget instance
	 *
	 * @phpstan-var \Required<SETTINGS, key-of<DEFAULTS>>
	 *
	 * @var array
	 */
	protected array $widget_settings;


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
	 * @phpstan-return \Required<SETTINGS, key-of<DEFAULTS>>
	 * @return array
	 */
	public function set_instance( array $instance, array $defaults ): array {
		$this->widget_settings = \array_merge( $defaults, $instance );

		return $this->widget_settings;
	}
}
