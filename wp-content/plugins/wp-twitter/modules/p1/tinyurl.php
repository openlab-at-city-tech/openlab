<?php

include_once( 'base_shortener.php' );

class FDX1TinyUrlShortener extends FDX1BaseShortener {

      function FDX1TinyUrlShortener() {}

	function shorten( $link ) {
		$request_uri = 'http://tinyurl.com/api-create.php?url=' . urlencode( $link );
				
		$request = new WP_Http;
		$result = $request->request( $request_uri );

		if ( $result ) {
			if ( isset( $result['response'] ) && isset( $result['response']['code'] ) && $result['response']['code'] == 200 ) {
				$link = $result['body'];
			}
		}

		return $link;
	}
}	
