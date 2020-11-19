<?php

namespace Advanced_Sidebar_Menu\Menus;

/**
 * Base for Menu classes.
 *
 * @author OnPoint Plugins
 * @since  7.0.0
 */
abstract class Menu_Abstract {
	const DISPLAY_ALL              = 'display_all';
	const EXCLUDE                  = 'exclude';
	const INCLUDE_CHILDLESS_PARENT = 'include_childless_parent';
	const INCLUDE_PARENT           = 'include_parent';
	const LEVELS                   = 'levels';
	const LEVEL_CHILD              = 'child';
	const LEVEL_DISPLAY_ALL        = 'display-all';
	const LEVEL_GRANDCHILD         = 'grandchild';
	const LEVEL_PARENT             = 'parent';
	const ORDER                    = 'order';
	const ORDER_BY                 = 'order_by';
	const TITLE                    = 'title';

	/**
	 * Widget Args
	 *
	 * @var array
	 */
	public $args = [];

	/**
	 * Widget instance
	 *
	 * @var array
	 */
	public $instance;

	/**
	 * Track the ids which have been used in case of
	 * plugins like Elementor that we need to manually increment.
	 *
	 * @since 7.6.0
	 * @ticket #4775
	 *
	 * @var string[]
	 */
	protected static $unique_widget_ids = [];


	/**
	 * Construct a new instance of this widget.
	 *
	 * @param array $instance - Widget settings.
	 * @param array $args     - Widget registration arguments.
	 */
	public function __construct( array $instance, array $args ) {
		$this->instance = apply_filters( 'advanced-sidebar-menu/menus/widget-instance', $instance, $args, $this );
		$this->args = $args;

		$this->increment_widget_id();
	}


	/**
	 * Get id of the highest level parent item.
	 *
	 * @return int
	 */
	abstract public function get_top_parent_id();


	/**
	 * Get key to order the menu items by.
	 *
	 * @return string
	 */
	abstract public function get_order_by();


	/**
	 * Get order of the menu (ASC|DESC).
	 *
	 * @return string
	 */
	abstract public function get_order();


	/**
	 * Should this widget be displayed.
	 *
	 * @return bool
	 */
	abstract public function is_displayed();


	/**
	 * How many levels should be displayed.
	 *
	 * @return int
	 */
	abstract public function get_levels_to_display();


	/**
	 * Render the widget
	 *
	 * @return void
	 */
	abstract public function render();


	/**
	 * Increment the widget id until it is unique to all widgets being displayed
	 * in the current context.
	 *
	 * Required because plugins like Elementor will reuse the same generic id for
	 * widgets within page content, and we need a unique id to properly target with
	 * styles, accordions, etc.
	 *
	 * @since 7.6.0
	 * @ticket #4775
	 *
	 * @return void
	 */
	protected function increment_widget_id() {
		if ( ! isset( $this->args['widget_id'] ) ) {
			return;
		}
		if ( in_array( $this->args['widget_id'], self::$unique_widget_ids, true ) ) {
			$suffix = 2;
			do {
				$alt_widget_id = $this->args['widget_id'] . "-$suffix";
				$suffix ++;
			} while ( in_array( $alt_widget_id, self::$unique_widget_ids, true ) );
			$this->args['widget_id'] = $alt_widget_id;
			self::$unique_widget_ids[] = $alt_widget_id;
		} else {
			self::$unique_widget_ids[] = $this->args['widget_id'];
		}
	}


	/**
	 * The instance arguments from the current widget.
	 *
	 * @return array
	 */
	public function get_widget_instance() {
		return $this->instance;
	}


	/**
	 * The widget arguments from the current widget.
	 *
	 * @return array
	 */
	public function get_widget_args() {
		return $this->args;
	}


	/**
	 * Checks if a widget's checkbox is checked.
	 *
	 * Checks first for a value then verifies the value = checked
	 *
	 * @param string $name - Name of checkbox.
	 *
	 * @return bool
	 */
	public function checked( $name ) {
		return isset( $this->instance[ $name ] ) && 'checked' === $this->instance[ $name ];
	}


	/**
	 * Determines if all the children should be included.
	 *
	 * @return bool
	 */
	public function display_all() {
		return $this->checked( self::DISPLAY_ALL );
	}


	/**
	 * Determines if the parent page or cat should be included.
	 *
	 * @return bool
	 */
	public function include_parent() {
		return $this->checked( self::INCLUDE_PARENT ) && ! $this->is_excluded( $this->get_top_parent_id() );
	}


	/**
	 * Is this id excluded from this menu?
	 *
	 * @param int $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ) {
		$exclude = $this->get_excluded_ids();

		return in_array( (int) $id, $exclude, true );
	}


	/**
	 * Retrieve the excluded items' ids.
	 *
	 * @return array
	 */
	public function get_excluded_ids() {
		return array_map( 'intval', array_filter( explode( ',', $this->instance[ self::EXCLUDE ] ), 'is_numeric' ) );
	}


	/**
	 * Echos the title of the widget to the page
	 *
	 * @todo find somewhere more appropriate for this?
	 */
	public function title() {
		if ( ! empty( $this->instance[ self::TITLE ] ) ) {
			$title = apply_filters( 'widget_title', $this->instance[ self::TITLE ], $this->args, $this->instance );
			$title = apply_filters( 'advanced-sidebar-menu/menus/widget-title', esc_html( $title ), $this->args, $this->instance, $this );

			// phpcs:disable
			echo $this->args['before_title'] . $title . $this->args['after_title'];
			// phpcs:enable
		}
	}


	/**
	 * Store current menu instance.
	 *
	 * @static
	 *
	 * @var Page|Category
	 */
	protected static $current;


	/**
	 * Get current menu instance.
	 *
	 * @static
	 *
	 * @return Page|Category
	 */
	public static function get_current() {
		return self::$current;
	}


	/**
	 * Construct a new instance of this class.
	 *
	 * @param array $widget_instance - Widget settings.
	 * @param array $widget_args     - Widget registration args.
	 *
	 * @static
	 *
	 * @return static
	 */
	public static function factory( array $widget_instance, array $widget_args ) {
		$menu = new static( $widget_instance, $widget_args );
		static::$current = $menu;

		return $menu;
	}
}
