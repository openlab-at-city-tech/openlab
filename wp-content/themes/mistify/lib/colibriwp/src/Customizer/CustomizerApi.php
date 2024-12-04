<?php


namespace ColibriWP\Theme\Customizer;

use ColibriWP\Theme\Core\Hooks;
use WP_REST_Request;
use WP_REST_Response;

class CustomizerApi {

	const REST_NAMESPACE = 'colibri_theme/v1';

	public function __construct() {
		$that = $this;
		add_action(
			'rest_api_init',
			function () use ( $that ) {
				foreach ( $that->getRoutes() as $route => $data ) {
					$data['callback']            = array( $that, $data['callback'] );
					$data['permission_callback'] = function () {
						return current_user_can( 'edit_theme_options' );
					};
					register_rest_route( static::REST_NAMESPACE, $route, $data );
				}
			}
		);

		Hooks::prefixed_add_filter(
			'customizer_additional_js_data',
			function ( $data ) {
				$data['api_url'] = site_url( '?rest_route=/' . static::REST_NAMESPACE );

				return $data;
			}
		);
	}

	protected function getRoutes() {

		return array(
			'/attachment-data/(?P<id>\d+)' => array(
				'method'   => 'GET',
				'callback' => 'getAttachmentData',
			),
		);

	}

	public function send( $data, $status = '200' ) {
		$reponse = new WP_REST_Response( $data );
		$reponse->set_status( $status );

		return $reponse;
	}

	public function getAttachmentData( WP_REST_Request $request ) {

		$id = $request->get_param( 'id' );

		$url       = wp_get_attachment_url( $id );
		$type      = wp_check_filetype( $url, wp_get_mime_types() );
		$mime_type = $type['type'];

		return $this->send(
			array(
				'url'       => $url,
				'mime_type' => $mime_type,
			)
		);
	}
}
