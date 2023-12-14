<?php

namespace Imagely\NGG\DisplayedGallery;

/**
 * The Trigger Manager displays "trigger buttons" for a displayed gallery.
 *
 * Each display type can register a "handler", which is a class with a render method, which is used
 * to render the display of the trigger buttons.
 *
 * Each trigger button is registered with a handler, which is also a class with a render() method.
 */
class TriggerManager {

	static $_instance                      = null;
	private $_triggers                     = [];
	private $_trigger_order                = [];
	private $_display_type_handlers        = [];
	private $_default_display_type_handler = null;
	private $css_class                     = 'ngg-trigger-buttons';

	public $view;

	private $_default_image_types = [
		'photocrati-nextgen_basic_thumbnails',
		'photocrati-nextgen_basic_singlepic',
	];

	/**
	 * @return TriggerManager
	 */
	static function get_instance() {
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
		if ( ( $current_index = array_search( $name, $this->_trigger_order ) ) !== false ) {
			$next_index = $current_index += 1;

			// 1,2,3,4,5 => 1,2,4,3,5
			if ( isset( $this->_trigger_order[ $next_index ] ) ) {
				$next                                   = $this->_trigger_order[ $next_index ];
				$this->_trigger_order[ $next_index ]    = $name;
				$this->_trigger_order[ $current_index ] = $next;
			}
		}

		return $this->position_of( $name );
	}

	public function decrement_position( $name ) {
		if ( ( $current_index = array_search( $name, $this->_trigger_order ) ) !== false ) {
			$previous_index = $current_index -= 1;
			if ( isset( $this->_trigger_order[ $previous_index ] ) ) {
				$previous                                = $this->_trigger_order[ $previous_index ];
				$this->_trigger_order[ $previous_index ] = $name;
				$this->_trigger_order[ $current_index ]  = $previous;
			}
		}

		return $this->position_of( $name );
	}

	public function position_of( $name ) {
		return array_search( $name, $this->_trigger_order );
	}

	public function move_to_position( $name, $position_index ) {
		if ( ( $current_index = $this->position_of( $name ) ) !== false ) {
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
		if ( ( $index = $this->position_of( $name ) ) ) {
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
		if ( $index !== false or $index != $this->count() - 1 ) {
			unset( $this->_trigger_order[ $index ] );
			$this->_trigger_order[] = $name;
			$this->_rebuild_index();
		}

		return $this->position_of( $name );
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

		return $klass;
	}

	public function render( $view, $displayed_gallery ) {
		if ( ( $klass = $this->get_handler_for_displayed_gallery( $displayed_gallery ) ) ) {
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
				$handler->view              = $this->view = $view;
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
			if ( ( $markup = $this->render_trigger( $name, $view, $displayed_gallery ) ) ) {
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

	public function enqueue_resources( $displayed_gallery ) {
		if ( ( $handler = $this->get_handler_for_displayed_gallery( $displayed_gallery ) ) ) {
			wp_enqueue_style( 'fontawesome' );
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

class ImageTriggerHandler {

	public $displayed_gallery;
	public $manager;
	public $view;

	public function render() {
		foreach ( $this->view->find( 'nextgen_gallery.image', true ) as $image_element ) {
			$image_element->append( $this->manager->render_triggers( $image_element, $this->displayed_gallery ) );
		}
	}
}

class TriggerHandler {

	public $displayed_gallery;
	public $manager;
	public $view;

	public function render() {
		$this->view->append( $this->manager->render_triggers( $this->view, $this->displayed_gallery ) );
	}
}
