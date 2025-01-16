<?php

namespace Advanced_Sidebar_Menu\Menus;

use Advanced_Sidebar_Menu\Widget\Widget;

/**
 * Base for Menu classes.
 *
 * @author OnPoint Plugins
 *
 * @phpstan-import-type PAGE_SETTINGS from Page
 * @phpstan-import-type WIDGET_ARGS from Widget
 *
 * @phpstan-template SETTINGS of array<string, string|int|array<string, string>>
 * @implements Menu<SETTINGS, self<SETTINGS>>
 */
abstract class Menu_Abstract implements Menu {
	public const WIDGET = 'menu-abstract';

	// Options shared between menus.
	public const DISPLAY_ALL              = 'display_all';
	public const EXCLUDE                  = 'exclude';
	public const INCLUDE_CHILDLESS_PARENT = 'include_childless_parent';
	public const INCLUDE_PARENT           = 'include_parent';
	public const LEVELS                   = 'levels';
	public const ORDER                    = 'order';
	public const ORDER_BY                 = 'order_by';
	public const TITLE                    = 'title';

	// Possible level values.
	public const LEVEL_CHILD       = 'child';
	public const LEVEL_DISPLAY_ALL = 'display-all';
	public const LEVEL_GRANDCHILD  = 'grandchild';
	public const LEVEL_PARENT      = 'parent';

	/**
	 * Track the ids, which have been used in case of
	 * plugins like Elementor that we need to manually increment.
	 *
	 * @since 7.6.0
	 * @ticket #4775
	 *
	 * @var string[]
	 */
	protected static array $unique_widget_ids = [];

	/**
	 * The current menu instance.
	 *
	 * @deprecated In favor of using factory on the specific class.
	 *
	 * @var Menu_Abstract
	 */
	protected static Menu_Abstract $current;

	/**
	 * Widget Args
	 *
	 * @phpstan-var WIDGET_ARGS
	 *
	 * @var array
	 */
	public $args;

	/**
	 * Widget instance
	 *
	 * @phpstan-var SETTINGS
	 *
	 * @var array
	 */
	public $instance;


	/**
	 * Constructs a new instance of this widget.
	 *
	 * @phpstan-param SETTINGS    $instance
	 * @phpstan-param WIDGET_ARGS $args
	 *
	 * @param array               $instance - Widget settings.
	 * @param array               $args     - Widget registration arguments.
	 */
	final public function __construct( array $instance, array $args ) {
		$this->instance = apply_filters( 'advanced-sidebar-menu/menus/widget-instance', $instance, $args, $this );
		$this->args = $args;

		$this->increment_widget_id();
	}


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
			$this->args['widget_id'] = (string) \preg_replace( '/^([\d-])/', '_$1', wp_hash( (string) wp_json_encode( $this->instance ) ) );
			//phpcs:ignore WordPress.Security.NonceVerification -- Not actually using the value of $_POST.
		} elseif ( isset( $_POST['action'] ) && 'elementor_ajax' === $_POST['action'] ) {
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
				++ $suffix;
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
	 * @phpstan-return SETTINGS
	 * @return array
	 */
	public function get_widget_instance(): array {
		return $this->instance;
	}


	/**
	 * The widget arguments from the current widget.
	 *
	 * @phpstan-return WIDGET_ARGS
	 * @return array
	 */
	public function get_widget_args(): array {
		return $this->args;
	}


	/**
	 * Is a widget's checkbox is checked?
	 *
	 * Checks first for a value then verifies the value = 'checked'.
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
	 * Retrieve the excluded items' ids.
	 *
	 * @return array<int>
	 */
	public function get_excluded_ids(): array {
		if ( ! \array_key_exists( static::EXCLUDE, $this->instance ) || ! \is_string( $this->instance[ static::EXCLUDE ] ) ) {
			return [];
		}
		return \array_map( '\intval', \array_filter( \explode( ',', $this->instance[ static::EXCLUDE ] ), 'is_numeric' ) );
	}


	/**
	 * Echos the title of the widget to the page.
	 *
	 * @return void
	 */
	public function title(): void {
		if ( ! \array_key_exists( static::TITLE, $this->instance ) || '' === $this->instance[ static::TITLE ] ) {
			return;
		}
		$title = apply_filters( 'widget_title', $this->instance[ static::TITLE ], $this->args, $this->instance );
		$title = apply_filters( 'advanced-sidebar-menu/menus/widget-title', $title, $this->args, $this->instance, $this );

		echo $this->args['before_title'] . esc_html( $title ) . $this->args['after_title']; //phpcs:ignore -- Args are HTML.
	}


	//phpcs:disable


	/**
	 * @deprecated In favor of using factory on the specific class.
	 */
	public static function get_current() {
		return static::$current;
	}


	/**
	 * @deprecated In favor of using factory on the specific class.
	 */
	public static function factory( array $widget_instance, array $widget_args ) {
		$menu = new static( $widget_instance, $widget_args );
		static::$current = $menu;
		return $menu;
	}
	//phpcs:enable
}
