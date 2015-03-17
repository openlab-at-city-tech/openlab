<?php

include_once( 'base_shortener.php' );

class FDX1IsgdShortener extends FDX1BaseShortener {

      function FDX1IsgdShortener() {}


	function shorten( $link ) {
		$request_uri = 'http://is.gd/create.php?format=simple&url=' . urlencode( $link );

		$request = new WP_Http;
		$result = $request->request( $request_uri );

		if ( $result ) {
			$decoded_result = $result['body'];
			if ( $result['response']['code'] == 200 && $decoded_result ) {
				return $decoded_result;
			}
		}

		return $link;
	}
}
