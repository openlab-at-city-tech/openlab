<?php 
namespace ElementsKit_Lite\Core;

class Handler_Api {

	public $prefix  = '';
	public $param   = '';
	public $request = null;

	public function __construct() {
		$this->config();
		$this->init();
	}

	public function config() {
	}

	public function init() {
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					untrailingslashit( 'elementskit/v1/' . $this->prefix ),
					'/(?P<action>\w+)/' . ltrim( $this->param, '/' ),
					array(
						'methods'             => \WP_REST_Server::ALLMETHODS,
						'callback'            => array( $this, 'callback' ),
						'permission_callback' => '__return_true', 
					// all permissions are implimented inside the callback action
					)
				);
			}
		);
	}

	public function callback( $request ) {
		$this->request = $request;
		$action_class  = strtolower( $this->request->get_method() ) . '_' . $this->request['action'];

		if ( method_exists( $this, $action_class ) ) {
			return $this->{$action_class}();
		}
	}

}
