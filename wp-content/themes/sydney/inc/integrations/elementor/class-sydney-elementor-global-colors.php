<?php
/**
 * Add global colors to Elementor
 *
 * @package Sydney
 */


if ( !class_exists( 'Sydney_Elementor_Global_Colors' ) ) :

	Class Sydney_Elementor_Global_Colors {

		/**
		 * Instance
		 */		
		private static $instance;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self;
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {	

			if ( !class_exists( 'Elementor\Plugin' ) ) {
				return;
			}

			add_filter( 'rest_request_after_callbacks', array( $this, 'add_global_colors_to_frontend' ), 999, 3 );
			add_filter( 'rest_request_after_callbacks', array( $this, 'add_global_colors_to_picker' ), 999, 3 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ), 999 );
		}

		/**
		 * Enqueue Global Colors
		 */
		public function enqueue() {
			$global_colors = sydney_get_global_colors();

			$css    = ':root{';
			foreach ( $global_colors as $slug => $color ) {
				$css .= '--e-global-color-' . str_replace( '-', '', $slug ) . ':' . $color . ';';
			}
			$css .= '}';

			wp_add_inline_style( 'sydney-style-min', $css );
		}

		/**
		 * Add to the frontend
		 */
		public function add_global_colors_to_frontend( $response, $handler, \WP_REST_Request $request ) {
			$route         = $request->get_route();

			$global_colors = sydney_get_global_colors();

			$color_slugs = array_combine( array_keys( $global_colors ), array_keys( $global_colors ) );
	
			foreach ( array_keys( $global_colors ) as $slug ) {
				$color_slugs[$slug] = $slug;
			}
	
			$rest_id = substr( $route, strrpos( $route, '/' ) + 1 );
	
			if ( ! in_array( $rest_id, array_keys( $color_slugs ), true ) ) {
				return $response;
			}
	
			$response = new \WP_REST_Response(
				[
					'id'    => esc_attr( $rest_id ),
					'title' => esc_html( $color_slugs[ $rest_id ] ),
					'value' => $global_colors[ $color_slugs[ $rest_id ] ],
				]
			);
			return $response;
		}
	
		/**
		 * Add to the color picker
		 */
		public function add_global_colors_to_picker( $response, $handler, \WP_REST_Request $request ) {
			$route = $request->get_route();

	
			if ( $route !== '/elementor/v1/globals' ) {
				return $response;
			}
	
			$label_map = [
				'global_color_1'   	=> __( 'Global Color 1', 'sydney' ),
				'global_color_2' 	=> __( 'Global Color 2', 'sydney' ),
				'global_color_3'    => __( 'Global Color 3', 'sydney' ),
				'global_color_4'    => __( 'Global Color 4', 'sydney' ),
				'global_color_5'    => __( 'Global Color 5', 'sydney' ),
				'global_color_6'    => __( 'Global Color 6', 'sydney' ),
				'global_color_7'    => __( 'Global Color 7', 'sydney' ),
				'global_color_8'    => __( 'Global Color 8', 'sydney' ),
				'global_color_9'    => __( 'Global Color 9', 'sydney' ),
			];

			$global_colors = sydney_get_global_colors();
	
			$data   = $response->get_data();
	
			foreach ( $global_colors as $slug => $color_value ) {
				$data['colors'][ $slug ] = [
					'id'    => esc_attr( $slug ),
					'title' => 'Sydney' . ' ' . esc_html( $label_map[ $slug ] ),
					'value' => $color_value,
				];
			}
	
			$response->set_data( $data );
	
			return $response;
		}


	}

	/**
	 * Initialize class
	 */
	Sydney_Elementor_Global_Colors::get_instance();

endif;