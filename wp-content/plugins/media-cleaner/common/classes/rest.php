<?php

class MeowCommon_Classes_Rest
{
	private $namespace = "meow-common/v1";
	static public $instance = null;

	static public function init_once() {
		if ( !function_exists( 'wp_get_current_user' ) ) {
			return;
		}
		if ( !current_user_can( 'administrator' ) ) {
			return;
		}
		if ( !MeowCommon_Classes_Rest::$instance ) {
			MeowCommon_Classes_Rest::$instance = new self();
		}
	}

	private function __construct() {
		add_action( 'rest_api_init', array( $this, 'rest_api_init' ) );
	}

	function rest_api_init() {
		register_rest_route( $this->namespace, '/empty_request/', [
			'methods' => 'POST',
			'callback' => [ $this, 'empty_request' ]
		]);
		register_rest_route( $this->namespace, '/file_operation/', [
			'methods' => 'POST',
			'callback' => [ $this, 'file_operation' ]
		]);
		register_rest_route( $this->namespace, '/sql_request/', [
			'methods' => 'POST',
			'callback' => [ $this, 'sql_request' ]
		]);
		register_rest_route( $this->namespace, '/error_logs/', [
			'methods' => 'POST',
			'callback' => [ $this, 'rest_error_logs' ]
		]);
		register_rest_route( $this->namespace, '/all_settings/', [
			'methods' => 'GET',
			'callback' => [ $this, 'rest_all_settings' ]
		]);
		register_rest_route( $this->namespace, '/update_option/', [
			'methods' => 'POST',
			'callback' => [ $this, 'rest_update_option' ]
		]);
	}

	function file_rand( $filesize ) {
		$tmp_file = tmpfile();
		fseek( $tmp_file, $filesize - 1, SEEK_CUR );
		fwrite( $tmp_file, 'a');
		fclose( $tmp_file );
	}

	function empty_request() {
    return new WP_REST_Response( [ 'success' => true ], 200 );
	}
	
	function file_operation() {
		$this->file_rand( 1024 * 10 );
    return new WP_REST_Response( [ 'success' => true ], 200 );
	}
	
	function sql_request() {
		global $wpdb;
		$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts}" );
    return new WP_REST_Response( [ 'success' => true, 'data' => $count ], 200 );
	}

	function rest_all_settings() {
		$settings = array(
			'meowapps_hide_meowapps' => get_option( 'meowapps_hide_meowapps', false ),
			'force_sslverify' => get_option( 'force_sslverify', false )
		);
		return new WP_REST_Response([ 'success' => true, 'data' => $settings ], 200 );
	}

	function rest_update_option( $request ) {
		$params = $request->get_json_params();
		try {
			$result = update_option( $params['name'], $params['value'] );
			return new WP_REST_Response([ 'success' => $result ], 200 );
		}
		catch (Exception $e) {
			return new WP_REST_Response([ 'success' => false, 'message' => $e->getMessage() ], 500 );
		}
	}

	function rest_error_logs( $request ) {
		return new WP_REST_Response( [ 'success' => true, 'data' => MeowCommon_Helpers::php_error_logs() ], 200 );
	}

}

?>