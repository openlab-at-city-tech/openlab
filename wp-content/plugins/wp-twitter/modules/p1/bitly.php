<?php

include_once( 'base_shortener.php' );

class FDX1BitlyShortener extends FDX1BaseShortener {
	var $api_key;
	var $login;
	
	function FDX1BitlyShortener( $login, $api_key ) {

		$this->login = $login;
		$this->api_key = $api_key;
	}
	
	function shorten( $link ) {
		$request_uri = 'http://api.bitly.com/v3/shorten?format=json' . 
			'&longUrl=' . urlencode( $link ) .
			'&apiKey=' . $this->api_key . 
			'&login=' . $this->login;
		
		$request = new WP_Http;
		$result = $request->request( $request_uri );

		if ( $result ) {
			$decoded_result = json_decode( $result['body'] );
			if ( $decoded_result && $decoded_result->status_code == 200 && isset( $decoded_result->data ) && isset( $decoded_result->data->url ) ) {
				return $decoded_result->data->url;
			}
		}
		
		return $link;
	}
}	
