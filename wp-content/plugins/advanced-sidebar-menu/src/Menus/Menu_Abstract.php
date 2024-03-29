<?php

namespace Advanced_Sidebar_Menu\Menus;

/**
 * Base for Menu classes.
 *
 * @author OnPoint Plugins
 */
abstract class Menu_Abstract {
	const WIDGET = 'menu-abstract';

	// Options shared between menus.
	const DISPLAY_ALL              = 'display_all';
	const EXCLUDE                  = 'exclude';
	const INCLUDE_CHILDLESS_PARENT = 'include_childless_parent';
	const INCLUDE_PARENT           = 'include_parent';
	const LEVELS                   = 'levels';
	const ORDER                    = 'order';
	const ORDER_BY                 = 'order_by';
	const TITLE                    = 'title';

	// Possible level values.
	const LEVEL_CHILD       = 'child';
	const LEVEL_DISPLAY_ALL = 'display-all';
	const LEVEL_GRANDCHILD  = 'grandchild';
	const LEVEL_PARENT      = 'parent';

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
	 * Track the ids, which have been used in case of
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
	 * @return ?int
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
		// Block widgets loaded via the REST API don't have full widget args.
		if ( ! isset( $this->args['widget_id'] ) ) {
			// Prefix any leading digits or hyphens with '_'.
			$this->args['widget_id'] = \preg_replace( '/^([\d-])/', '_$1', wp_hash( (string) wp_json_encode( $this->instance ) ) );
			//phpcs:ignore -- Not actually using the value of $_POST.
		} elseif ( ! empty( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) {
			/**
			 * Elementor sends widgets one at a time with the same id during the preview.
			 * Since we can't increment nor is there a unique id, we always
			 * use the instance for Elementor previews.
			 */
			$this->args['widget_id'] .= wp_hash( (string) wp_json_encode( $this->instance ) );
		}

		if ( \in_array( $this->args['widget_id'], static::$unique_widget_ids, true ) ) {
			$suffix = 2;
			do {
				$alt_widget_id = $this->args['widget_id'] . "-$suffix";
				++$suffix;
			} while ( \in_array( $alt_widget_id, static::$unique_widget_ids, true ) );
			$this->args['widget_id'] = $alt_widget_id;
			static::$unique_widget_ids[] = $alt_widget_id;
		} else {
			static::$unique_widget_ids[] = $this->args['widget_id'];
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
		return $this->checked( static::DISPLAY_ALL );
	}


	/**
	 * Determines if the parent page or cat should be included.
	 *
	 * @return bool
	 */
	public function include_parent() {
		return $this->checked( static::INCLUDE_PARENT ) && ! $this->is_excluded( $this->get_top_parent_id() ?? - 1 );
	}


	/**
	 * Is this id excluded from this menu?
	 *
	 * @param int|string $id ID of the object.
	 *
	 * @return bool
	 */
	public function is_excluded( $id ) {
		$excluded = \in_array( (int) $id, $this->get_excluded_ids(), true );
		return apply_filters( 'advanced-sidebar-menu/menus/' . static::WIDGET . '/is-excluded', $excluded, $id, $this->get_widget_args(), $this->get_widget_instance(), $this );
	}


	/**
	 * Retrieve the excluded items' ids.
	 *
	 * @return array<int>
	 */
	public function get_excluded_ids() {
		if ( ! \array_key_exists( static::EXCLUDE, $this->instance ) ) {
			return [];
		}
		return \array_map( '\intval', \array_filter( \explode( ',', $this->instance[ static::EXCLUDE ] ), 'is_numeric' ) );
	}


	/**
	 * Echos the title of the widget to the page.
	 *
	 * @return void
	 */
	public function title() {
		if ( ! \array_key_exists( static::TITLE, $this->instance ) || '' === (string) $this->instance[ static::TITLE ] ) {
			return;
		}
		$title = apply_filters( 'widget_title', $this->instance[ static::TITLE ], $this->args, $this->instance );
		$title = apply_filters( 'advanced-sidebar-menu/menus/widget-title', $title, $this->args, $this->instance, $this );

		echo $this->args['before_title'] . esc_html( $title ) . $this->args['after_title']; //phpcs:ignore -- Args are HTML.
	}


	/**
	 * Store current menu instance.
	 *
	 * @static
	 *
	 * @var Page|Category|null
	 */
	protected static $current;


	/**
	 * Get current menu instance.
	 *
	 * @static
	 *
	 * @return Page|Category|null
	 */
	public static function get_current() {
		return static::$current;
	}


	/**
	 * Constructs a new instance of this class.
	 *
	 * @param array $widget_instance - Widget settings.
	 * @param array $widget_args     - Widget registration args.
	 *
	 * @static
	 *
	 * @return static
	 */
	public static function factory( array $widget_instance, array $widget_args ) {
		/* @phpstan-ignore-next-line */
		$menu = new static( $widget_instance, $widget_args );
		/* @phpstan-ignore-next-line */
		static::$current = $menu;

		return $menu;
	}
}
