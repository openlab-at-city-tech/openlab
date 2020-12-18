<?php

class MeowCommon_Classes_Rest_License
{
	private $licenser = null;
	private $namespace = null;

	public function __construct( &$licenser ) {
    $this->licenser = $licenser;
		$this->namespace = "meow-licenser/{$licenser->prefix}/v1";
		if ( !current_user_can( 'administrator' ) ) {
			return;
		} 
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	function rest_api_init() {
		register_rest_route( $this->namespace, '/get_license/', [
			'methods' => 'POST',
			'callback' => [ $this, 'get_license' ]
    ]);
    register_rest_route( $this->namespace, '/set_license/', [
			'methods' => 'POST',
			'callback' => [ $this, 'set_license' ]
		]);
	}

	function get_license() {
    return new WP_REST_Response( [ 'success' => true, 'data' => $this->licenser->license ], 200 );
  }
  
  function set_license( $request ) {
		$params = $request->get_json_params();
    $serialKey = $params['serialKey'];
    $this->licenser->validate_pro( $serialKey );
    return new WP_REST_Response( [ 'success' => true, 'data' => $this->licenser->license ], 200 );
	}
}

?>