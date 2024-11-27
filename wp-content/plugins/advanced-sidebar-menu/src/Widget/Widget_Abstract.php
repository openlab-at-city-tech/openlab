<?php

namespace Advanced_Sidebar_Menu\Widget;

/**
 * Placeholder for older version of PRO which expect all widgets to extend this class.
 *
 * @todo Remove this class once required PRO version is 9.6.0+.
 *
 * @template SETTINGS of array<string, mixed>
 * @template DEFAULTS of array<key-of<SETTINGS>, mixed>
 *
 * @extends \WP_Widget<SETTINGS>
 * @implements Widget<SETTINGS, DEFAULTS>
 */
abstract class Widget_Abstract extends \WP_Widget implements Widget {
	/**
	 * @use Checkbox<SETTINGS>
	 */
	use Checkbox;

	/**
	 * @use Instance<SETTINGS, DEFAULTS>
	 */
	use Instance;
}
