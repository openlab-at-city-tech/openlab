<?php

namespace Imagely\NGG\DisplayedGallery;

use Imagely\NGG\DataStorage\Manager as StorageManager;

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * The Trigger Manager displays "trigger buttons" for a displayed gallery.
 *
 * Each display type can register a "handler", which is a class with a render method, which is used
 * to render the display of the trigger buttons.
 *
 * Each trigger button is registered with a handler, which is also a class with a render() method.
 */
class TriggerManager {

	/**
	 * Instance cache.
	 *
	 * @var TriggerManager|null
	 */
	public static $_instance = null;

	/**
	 * Triggers array.
	 *
	 * @var array
	 */
	private $_triggers = [];

	/**
	 * Trigger order array.
	 *
	 * @var array
	 */
	private $_trigger_order = [];

	/**
	 * Display type handlers.
	 *
	 * @var array
	 */
	private $_display_type_handlers = [];

	/**
	 * Default display type handler.
	 *
	 * @var object|null
	 */
	private $_default_display_type_handler = null;

	/**
	 * CSS class name.
	 *
	 * @var string
	 */
	private $css_class = 'ngg-trigger-buttons';

	/**
	 * View instance.
	 *
	 * @var object
	 */
	public $view;

	/**
	 * Default image types array.
	 *
	 * @var array
	 */
	private $_default_image_types = [
		'photocrati-nextgen_basic_thumbnails',
		'photocrati-nextgen_basic_singlepic',
	];

	/**
	 * Gets an instance of the trigger manager.
	 *
	 * @return TriggerManager
	 */
	public static function get_instance() {
		if ( ! self::$_instance ) {
			self::$_instance = new TriggerManager();
		}
		return self::$_instance;
	}

	public function __construct() {
		if ( \C_NextGEN_Bootstrap::get_pro_api_version() < 4.0 ) {
			$this->_default_image_types = array_merge(
				$this->_default_image_types,
				[
					'photocrati-nextgen_pro_thumbnail_grid',
					'photocrati-nextgen_pro_blog_gallery',
					'photocrati-nextgen_pro_film',
				]
			);
		}

		$this->_default_display_type_handler = '\Imagely\NGG\DisplayedGallery\TriggerHandler';
		foreach ( $this->_default_image_types as $display_type ) {
			$this->register_display_type_handler( $display_type, '\Imagely\NGG\DisplayedGallery\ImageTriggerHandler' );
		}
	}

	public function register_display_type_handler( $display_type, $klass = null ) {
		if ( ! $klass ) {
			$klass = $this->_default_display_type_handler;
		}
		$this->_display_type_handlers[ $display_type ] = $klass;
	}

	public function deregister_display_type_handler( $display_type ) {
		unset( $this->_display_type_handlers[ $display_type ] );
	}

	public function add( $name, $handler ) {
		$this->_triggers[ $name ] = $handler;
		$this->_trigger_order[]   = $name;

		return $this;
	}

	public function remove( $name ) {
		$order = [];
		unset( $this->_triggers[ $name ] );
		foreach ( $this->_trigger_order as $trigger ) {
			if ( $trigger != $name ) {
				$order[] = $trigger;
			}
		}
		$this->_trigger_order = $order;

		return $this;
	}

	public function _rebuild_index() {
		$order = [];
		foreach ( $this->_trigger_order as $name ) {
			$order[] = $name;
		}
		$this->_trigger_order = $order;

		return $this;
	}

	public function increment_position( $name ) {
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		$current_index = array_search( $name, $this->_trigger_order );
		if ( $current_index !== false ) {
			++$current_index;
			$next_index = $current_index;

			if ( isset( $this->_trigger_order[ $next_index ] ) ) {
				$next                                   = $this->_trigger_order[ $next_index ];
				$this->_trigger_order[ $next_index ]    = $name;
				$this->_trigger_order[ $current_index ] = $next;
			}
		}

		return $this->position_of( $name );
	}

	public function decrement_position( $name ) {
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		$current_index = array_search( $name, $this->_trigger_order );
		if ( $current_index !== false ) {
			--$current_index;
			$previous_index = $current_index;
			if ( isset( $this->_trigger_order[ $previous_index ] ) ) {
				$previous                                = $this->_trigger_order[ $previous_index ];
				$this->_trigger_order[ $previous_index ] = $name;
				$this->_trigger_order[ $current_index ]  = $previous;
			}
		}

		return $this->position_of( $name );
	}

	public function position_of( $name ) {
		// phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
		return array_search( $name, $this->_trigger_order );
	}

	public function move_to_position( $name, $position_index ) {
		$current_index = $this->position_of( $name );
		if ( $current_index !== false ) {
			$func = 'increment_position';
			if ( $current_index < $position_index ) {
				$func = 'decrement_position';
			}
			while ( $this->position_of( $name ) != $position_index ) {
				$this->$func( $name );
			}
		}

		return $this->position_of( $name );
	}

	public function move_to_start( $name ) {
		$index = $this->position_of( $name );
		if ( $index ) {
			unset( $this->_trigger_order[ $index ] );
			array_unshift( $this->_trigger_order, $name );
			$this->_rebuild_index();
		}

		return $this->position_of( $name );
	}

	public function count() {
		return count( $this->_trigger_order );
	}

	public function move_to_end( $name ) {
		$index = $this->position_of( $name );
		if ( $index !== false || $index != $this->count() - 1 ) {
			unset( $this->_trigger_order[ $index ] );
			$this->_trigger_order[] = $name;
			$this->_rebuild_index();
		}

		return $this->position_of( $name );
	}

	/**
	 * Whether a class name may be instantiated as a trigger handler.
	 *
	 * Accepted: the default handler, a subclass of it, or a class registered in code through
	 * register_display_type_handler(). A handler that is neither can be allowed explicitly through
	 * the `ngg_allowed_trigger_handlers` filter - that is a code-side decision, which a stored
	 * column value is not.
	 *
	 * @param string $klass Class name.
	 * @return bool
	 */
	protected function is_trigger_handler_class( $klass ) {
		if ( ! is_string( $klass ) || '' === $klass ) {
			return false;
		}

		// Shape-checked before anything that can autoload. class_exists() and is_subclass_of()
		// both run the autoloader, and NGG's autoloader maps the class name onto a path under
		// src/ - so calling either on the raw stored value would let "Imagely\NGG\..\..\uploads\x"
		// require() an uploaded file before this method ever returned false. A value that is not
		// shaped like a PHP class name never reaches those calls.
		if ( ! preg_match( '/^\\\\?[A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*(\\\\[A-Za-z_\x80-\xff][A-Za-z0-9_\x80-\xff]*)*$/', $klass ) ) {
			return false;
		}

		// Compared on a normalized form: PHP class names are case-insensitive and a leading "\\" is
		// optional, so a registered handler written as "\\Foo\\Bar" must still match a stored
		// "Foo\\Bar" - refusing that would drop a legitimate third-party handler.
		$normalized = self::normalize_class_name( $klass );
		$default    = $this->_default_display_type_handler;

		if ( $default && $normalized === self::normalize_class_name( $default ) ) {
			return true;
		}

		// Registered handlers are trusted: they were declared in code, not read from a column.
		foreach ( (array) $this->_display_type_handlers as $registered ) {
			if ( $normalized === self::normalize_class_name( $registered ) ) {
				return true;
			}
		}

		/**
		 * Filters the extra classes that may be instantiated as a trigger handler.
		 *
		 * @param string[] $handlers Fully-qualified class names.
		 */
		$allowed = (array) apply_filters( 'ngg_allowed_trigger_handlers', [] );

		foreach ( $allowed as $allowed_class ) {
			if ( $normalized === self::normalize_class_name( $allowed_class ) ) {
				return true;
			}
		}

		// Last: a subclass of the default handler. This is the only branch that has to load the
		// class, so it runs after every name-only comparison has failed, and only for a value
		// already shaped like a class name.
		if ( $default && is_subclass_of( $klass, ltrim( (string) $default, '\\' ) ) ) {
			return true;
		}

		return false;
	}

	/**
	 * A class name in the form used for comparison: no leading separator, lowercased.
	 *
	 * @param mixed $klass Class name.
	 * @return string
	 */
	protected static function normalize_class_name( $klass ) {
		return is_string( $klass ) ? strtolower( ltrim( $klass, '\\' ) ) : '';
	}

	public function get_handler_for_displayed_gallery( $displayed_gallery ) {
		// Find the trigger handler for the current display type.

		// First, check the display settings for the displayed gallery. Some third-party display types might specify their own handler.
		$klass = null;
		if ( isset( $displayed_gallery->display_settings['trigger_handler'] ) ) {
			$klass = $displayed_gallery->display_settings['trigger_handler'];
		} else {
			// Check if a handler has been registered.
			$klass = $this->_default_display_type_handler;
			if ( isset( $this->_display_type_handlers[ $displayed_gallery->display_type ] ) ) {
				$klass = $this->_display_type_handlers[ $displayed_gallery->display_type ];
			}
		}

		// Validated here, not at the sinks: display_settings['trigger_handler'] originates in the
		// gallery's stored display_type_settings and is therefore writable through the editing
		// endpoints, while both consumers reach code with it on an unauthenticated front-end
		// render - render() with `new $klass()`, enqueue_resources() with method_exists() and
		// call_user_func(). Gating the getter means a third consumer cannot be added without it.
		if ( $klass && ! $this->is_trigger_handler_class( $klass ) ) {
			// Logged, not silently swapped: this substitution makes a gallery's trigger buttons
			// (lightbox, add-to-cart, proofing) render from the default handler instead of the
			// configured one, or not render at all when there is no default - a front-end change
			// with no admin signal, so support needs a line to search for.
			StorageManager::get_instance()->log_path_refusal(
				sprintf(
					'NextGEN Gallery: refused trigger handler "%s" for display type "%s" - not the default handler, a subclass of it, or a handler registered in code; using the default instead',
					is_string( $klass ) ? $klass : gettype( $klass ),
					isset( $displayed_gallery->display_type ) ? $displayed_gallery->display_type : '?'
				),
				StorageManager::REFUSAL_TRIGGER_HANDLER
			);

			$klass = $this->_default_display_type_handler;
		}

		return $klass;
	}

	public function render( $view, $displayed_gallery ) {
		// Already validated by get_handler_for_displayed_gallery().
		$klass = $this->get_handler_for_displayed_gallery( $displayed_gallery );

		if ( $klass ) {
			$handler                    = new $klass();
			$handler->view              = $view;
			$handler->displayed_gallery = $displayed_gallery;
			$handler->manager           = $this;
			if ( method_exists( $handler, 'render' ) ) {
				$handler->render();
			}
		}

		return $view;
	}

	public function render_trigger( $name, $view, $displayed_gallery ) {
		$retval = '';

		if ( isset( $this->_triggers[ $name ] ) ) {
			$klass = $this->_triggers[ $name ];
			if ( call_user_func( [ $klass, 'is_renderable' ], $name, $displayed_gallery ) ) {
				$handler                    = new $klass();
				$handler->name              = $name;
				$this->view                 = $view;
				$handler->view              = $view;
				$handler->displayed_gallery = $displayed_gallery;
				$retval                     = $handler->render();
			}
		}

		return $retval;
	}

	public function render_triggers( $view, $displayed_gallery ) {
		$output    = false;
		$css_class = esc_attr( $this->css_class );
		$retval    = [ "<div class='{$css_class}'>" ];

		foreach ( $this->_trigger_order as $name ) {
			$markup = $this->render_trigger( $name, $view, $displayed_gallery );
			if ( $markup ) {
				$output   = true;
				$retval[] = $markup;
			}
		}

		if ( $output ) {
			$retval[] = '</div>';
			$retval   = implode( "\n", $retval );
		} else {
			$retval = '';
		}

		return $retval;
	}

	/**
	 * Runs each registered trigger's is_renderable() for its enqueue side effect,
	 * so trigger assets survive a rendering-cache hit that skips the render pass.
	 *
	 * @param object $displayed_gallery The displayed gallery.
	 * @return void
	 */
	public function enqueue_trigger_assets( $displayed_gallery ) {
		foreach ( $this->_trigger_order as $name ) {
			if ( ! isset( $this->_triggers[ $name ] ) ) {
				continue;
			}

			$klass = $this->_triggers[ $name ];
			if ( method_exists( $klass, 'is_renderable' ) ) {
				call_user_func( [ $klass, 'is_renderable' ], $name, $displayed_gallery );
			}
		}
	}

	public function enqueue_resources( $displayed_gallery ) {
		$handler = $this->get_handler_for_displayed_gallery( $displayed_gallery );
		if ( $handler ) {
			wp_enqueue_style( 'nextgen_gallery_icons' );
			wp_enqueue_style( 'ngg_trigger_buttons' );

			if ( method_exists( $handler, 'enqueue_resources' ) ) {
				call_user_func( [ $handler, 'enqueue_resources' ], $displayed_gallery );
				foreach ( $this->_trigger_order as $name ) {
					$handler    = $this->_triggers[ $name ];
					$renderable = true;
					if ( method_exists( $handler, 'is_renderable' ) ) {
						$renderable = call_user_func( $handler, 'is_renderable', $name, $displayed_gallery );
					}

					if ( $renderable && method_exists( $handler, 'enqueue_resources' ) ) {
						call_user_func( [ $handler, 'enqueue_resources', $name, $displayed_gallery ] );
					}
				}
			}
		}
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * Image trigger handler.
 */
class ImageTriggerHandler {

	/**
	 * Displayed gallery instance.
	 *
	 * @var object
	 */
	public $displayed_gallery;

	/**
	 * Manager instance.
	 *
	 * @var object
	 */
	public $manager;

	/**
	 * View instance.
	 *
	 * @var object
	 */
	public $view;

	public function render() {
		foreach ( $this->view->find( 'nextgen_gallery.image', true ) as $image_element ) {
			$image_element->append( $this->manager->render_triggers( $image_element, $this->displayed_gallery ) );
		}
	}
}

// phpcs:ignore Generic.Files.OneObjectStructurePerFile.MultipleFound
/**
 * Trigger handler.
 */
class TriggerHandler {

	/**
	 * Displayed gallery instance.
	 *
	 * @var object
	 */
	public $displayed_gallery;

	/**
	 * Manager instance.
	 *
	 * @var object
	 */
	public $manager;

	/**
	 * View instance.
	 *
	 * @var object
	 */
	public $view;

	public function render() {
		$this->view->append( $this->manager->render_triggers( $this->view, $this->displayed_gallery ) );
	}
}
