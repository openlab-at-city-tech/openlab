<?php

include_once( 'base_shortener.php' );

class FDX1YourlsShortener extends FDX1BaseShortener {
	var $path;
	var $signature;
	
	function FDX1YourlsShortener( $path, $signature ) {

		$this->path = $path;
		$this->signature = $signature;
	}
	
	function shorten( $link ) {
		$request_uri = $this->path . '?signature=' . $this->signature . '&action=shorturl&format=simple&url=' . urlencode( $link );

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
