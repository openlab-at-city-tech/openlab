<?php
namespace ElementsKit_Lite\Core;

use ElementsKit_Lite\Libs\Framework\Attr;

defined( 'ABSPATH' ) || exit;

class Build_Widgets {

	/**
	 * Collection of default widgets.
	 *
	 * @since 1.0.0
	 * @access private
	 */
	private $widgets;

	use \ElementsKit_Lite\Traits\Singleton;

	public function __construct() {

		new \ElementsKit_Lite\Widgets\Init\Enqueue_Scripts();
		$this->widgets = \ElementsKit_Lite\Config\Widget_List::instance()->get_list( 'active' );

		// check if the widget is exists
		foreach ( $this->widgets as $widget ) {
			$this->add_widget( $widget );
		}

		add_action( 'elementor/widgets/register', array( $this, 'register_widget' ) );
	}


	public function add_widget( $widget_config ) {

		$widget_dir = (
			isset( $widget_config['path'] ) 
			? $widget_config['path'] 
			: \ElementsKit_Lite::widget_dir() . $widget_config['slug'] . '/'
		);

		include $widget_dir . $widget_config['slug'] . '.php';
		include $widget_dir . $widget_config['slug'] . '-handler.php';

		$base_class_name = (
			( isset( $widget_config['base_class_name'] ) )
			? $widget_config['base_class_name']
			: '\Elementor\ElementsKit_Widget_' . \ElementsKit_Lite\Utils::make_classname( $widget_config['slug'] )
		);

		$handler       = $base_class_name . '_Handler';
		$handler_class = new $handler();

		if ( $handler_class->scripts() != false ) {
			add_action( 'wp_enqueue_scripts', array( $handler_class, 'scripts' ) );
		}

		if ( $handler_class->styles() != false ) {
			add_action( 'wp_enqueue_scripts', array( $handler_class, 'styles' ) );
		}

		if ( $handler_class->inline_css() != false ) {
			wp_add_inline_style( 'elementskit-init-css', $handler_class->inline_css() );
		}

		if ( $handler_class->inline_js() != false ) {
			wp_add_inline_script( 'elementskit-init-js', $handler_class->inline_js() );
		}

		if ( $handler_class->register_api() != false ) {
			if ( \file_exists( $handler_class->register_api() ) ) {
				include_once $handler_class->register_api();
				$api = $base_class_name . '_Api';
				new $api();
			}
		}

		if ( $handler_class->wp_init() != false ) {
			add_action( 'init', array( $handler_class, 'wp_init' ) );
		}
	}


	public function register_widget( $widgets_manager ) {
		foreach ( $this->widgets as $widget_slug => $widget ) {
			$class_name = '\Elementor\ElementsKit_Widget_' . \ElementsKit_Lite\Utils::make_classname( $widget_slug );
			if ( class_exists( $class_name ) ) {
				$widgets_manager->register( new $class_name() );
			}
		}
	}
}
