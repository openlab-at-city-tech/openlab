<?php

class M_Ajax extends C_Base_Module {

	public $object;

	public function define(
		$id = 'pope-module',
		$name = 'Pope Module',
		$description = '',
		$version = '',
		$uri = '',
		$author = '',
		$author_uri = '',
		$context = false
	) {
		parent::define(
			'photocrati-ajax',
			'AJAX',
			'Provides AJAX functionality',
			'3.3.21',
			'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
			'Imagely',
			'https://www.imagely.com'
		);
	}

	public function _register_utilities() {
		$this->get_registry()->add_utility( 'I_Ajax_Controller', 'C_Ajax_Controller' );
	}

	public function _register_hooks() {
		add_action( 'ngg_routes', [ $this, 'define_routes' ] );
		add_action( 'init', [ $this, 'serve_ajax_request' ] );
	}

	public function serve_ajax_request() {
		// This method only begins NextGEN's AJAX endpoint handler, individual endpoints are responsible for
		// their own nonce and other security checks.
		//
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_REQUEST[ NGG_AJAX_SLUG ] ) ) {
			$controller = C_Ajax_Controller::get_instance();
			$controller->index_action();
			exit();
		}
	}

	public function define_routes( $router ) {
		$app = $router->create_app( '/photocrati_ajax' );
		$app->route( '/', 'I_Ajax_Controller#index' );
	}

	/**
	 * Pass PHP object or array to JS, preserving numeric and boolean value
	 *
	 * @param string       $handle
	 * @param string       $var_name
	 * @param object|array $data
	 */
	static function pass_data_to_js( $handle, $var_name, $data ) {
		$var_name = esc_js( $var_name );
		return wp_add_inline_script(
			$handle,
			"let {$var_name} = " . json_encode( $data, JSON_NUMERIC_CHECK )
		);
	}

	public function get_type_list() {
		return [ 'C_Ajax_Controller' => 'class.ajax_controller.php' ];
	}
}

new M_Ajax();
