<?php

namespace ElementsKit_Lite\Libs\Framework\Classes;

use ElementsKit_Lite\Traits\Singleton;

defined( 'ABSPATH' ) || exit;

class Plugin_Data_Sender {
	use Singleton;

	public function __construct() {}

	private function getUrl( $route ) {
		return trailingslashit(\ElementsKit_Lite::api_url() . $route);
	}

	public function sendEmailSubscribeData( $route, $data ) {
		return wp_remote_post(
			$this->getUrl( $route ),
			array(
				'method'      => 'POST',
				'headers'     => array(
					'Accept'       => '*/*',
					'Content-Type' => 'application/json'
				),
				'body'        => wp_json_encode( $data ),
			)
		);
	}
}
