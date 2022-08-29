<?php
namespace ElementsKit_Lite\Modules\Widget_Builder;

use ElementsKit_Lite\Modules\Widget_Builder\Controls\Widget_Writer;

defined( 'ABSPATH' ) || exit;

class Init {
	private $dir;
	private $url;

	public function __construct() {

		// get current directory path
		$this->dir = dirname( __FILE__ ) . '/';

		// get current module's url
		$this->url = \ElementsKit_Lite::plugin_url() . 'modules/widget-builder/';

		// include all necessary files
		$this->include_files();

		//hooks
		add_action( 'admin_enqueue_scripts', array( $this, 'load_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'register_meta_boxes' ) );
		// add_action('elementor/init', [$this, 'elementor_widget_category']);
		add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
		add_action( 'elementor/editor/before_enqueue_scripts', array( $this, 'editor_css' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'frontend_css' ) );

		add_action( 'admin_init', array( $this, 'on_empty_trash_delete_files' ) );
		//add_action('wp_trash_post', [$this, 'on_trash_delete_files']);

		// calling necessary classess
		new Api\Common();
		new Cpt();
		new Live_Action();
	}
	
	public function include_files() {
		// include $this->dir . 'extend-controls.php';
	}

	public function register_widgets( $widgets_manager ) {
		$widgets = get_posts(
			array(
				'post_type'   => 'elementskit_widget',
				'post_status' => 'publish',
				'numberposts' => -1,
			)
		);

		$upload = wp_upload_dir();
		foreach ( $widgets as $widget ) {
			$slug       = 'ekit_wb_' . $widget->ID;
			$dir        = $upload['basedir'] . '/elementskit/custom_widgets/' . $slug . '/';
			$file       = $dir . 'widget.php';
			$class_name = '\Elementor\Ekit_Wb_' . $widget->ID;

			if ( file_exists( $file ) ) {
				include $file;
				$widgets_manager->register( new $class_name() );
			}
		}
	}

	public function elementor_widget_category( $widgets_manager ) {
		$widgets_manager->register(
			'elementskit-lite',
			array(
				'title' => esc_html__( 'ElementsKit Custom', 'elementskit-lite' ),
				'icon'  => 'fa fa-plug',
			),
			1
		);
	}

	public function frontend_css() {
		if ( ! is_singular( 'elementskit_widget' ) ) {
			return;
		}
		
		wp_enqueue_style( 'elementskit-widget-builder-common-css', $this->url . 'assets/css/ekit-widget-builder-common.css', array(), \ElementsKit_Lite::version() );
	}

	public function editor_css() {
		$screen = get_current_screen();
		
		if ( $screen->id != 'elementskit_widget' ) {
			return;
		}

		wp_enqueue_style( 'elementskit-widget-builder-common-css', $this->url . 'assets/css/ekit-widget-builder-common.css', array(), \ElementsKit_Lite::version() );
	}

	public function load_scripts() {
		$screen = get_current_screen();
		
		if ( $screen->id != 'elementskit_widget' ) {
			return;
		}

		wp_enqueue_style( 'google-fonts-roboto', 'https://fonts.googleapis.com/css?family=Roboto:300,400,500,700', array(), null );
		wp_enqueue_style( 'font-awesome', ELEMENTOR_ASSETS_URL . 'lib/font-awesome/css/all.min.css', array(), null );
		wp_enqueue_style( 'elementskit-widget-builder-editor-css', $this->url . 'assets/css/ekit-widget-builder-editor.css', array(), \ElementsKit_Lite::version() );
		wp_enqueue_style( 'elementskit-widget-builder-common-css', $this->url . 'assets/css/ekit-widget-builder-common.css', array(), \ElementsKit_Lite::version() );

		wp_enqueue_script( 'elementskit-widget-builder-editor-js', $this->url . 'assets/js/ekit-widget-builder-editor.js', array(), \ElementsKit_Lite::version(), true );
	}

	public function register_meta_boxes() {
		add_meta_box( 'elementskit-widget-builder-markup', __( 'Builder', 'elementskit-lite' ), array( $this, 'metabox_display_callback' ), 'elementskit_widget' );
	}

	public function metabox_display_callback( $post ) {
		include $this->dir . 'views/builder.php';
	}

	public function on_empty_trash_delete_files() {

		if ( !empty( $_GET['post'] ) && !empty($_GET['_wpnonce']) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'delete-post_' . intval( $_GET['post'] ) ) ) {

			$post_id   = intval( $_GET['post'] );
			$post_type = get_post_type( $post_id );

			if ( $post_type == 'elementskit_widget' ) {

				Widget_Writer::delete_widget( $post_id );
			}       
		} elseif (
			isset( $_GET['post_type'] ) && $_GET['post_type'] == 'elementskit_widget' &&
			isset( $_GET['action'] ) && $_GET['action'] == -1 &&
			isset( $_GET['post_status'] ) && $_GET['post_status'] == 'trash' ) {
		}
	}
}
