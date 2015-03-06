<?php

if ( !class_exists( 'WP_Http' ) ) {
	include_once( ABSPATH . WPINC. '/class-http.php' );
}
define( 'fdx1_OAUTH_CONSUMER_KEY', 'ywPMWiTkSJ3HOEINuttyDQ' );
define( 'fdx1_OAUTH_REQUEST_URL', 'https://api.twitter.com/oauth/request_token' );
define( 'fdx1_OAUTH_ACCESS_URL', 'https://api.twitter.com/oauth/access_token' );
define( 'fdx1_OAUTH_AUTHORIZE_URL', 'https://api.twitter.com/oauth/authorize' );
define( 'fdx1_OAUTH_REALM', 'https://twitter.com/' );

class FDX1OAuth {

	function FDX1OAuth() {
		$this->duplicate_tweet = false;
		$this->response_code = false;
		$this->error_message = false;
		
		$this->setup();
		
		$this->oauth_time_offset = 0; // offset 10 20...
		$this->set_defeault_oauth_tokens();
	}
	
	function set_defeault_oauth_tokens() {	
		$this->oauth_consumer_key = fdx1_OAUTH_CONSUMER_KEY;
		$this->oauth_consumer_secret = fdx1_OAUTH_CONSUMER_SECRET;
	}
	
	function set_oauth_tokens( $key, $secret ) {
		$this->oauth_consumer_key = $key;
		$this->oauth_consumer_secret = $secret;
	}

	function get_response_code() {
		return $this->response_code;
	}

	function get_error_message() {
		return $this->error_message;
	}
	
	function encode( $string ) {
   		return str_replace( '+', ' ', str_replace( '%7E', '~', rawurlencode( $string ) ) );
	}
	
	function create_signature_base_string( $get_method, $base_url, $params ) {
		if ( $get_method ) {
			$base_string = "GET&";
		} else {
			$base_string = "POST&";	
		}

		$base_string .= $this->encode( $base_url ) . "&";
		
		// Sort the parameters
		ksort( $params );
		
		$encoded_params = array();
		foreach( $params as $key => $value ) {
			$encoded_params[] = $this->encode( $key ) . '=' . $this->encode( $value );
		}
		
		$base_string = $base_string . $this->encode( implode( $encoded_params, "&" ) );
		
		return $base_string;
	}
	
	function params_to_query_string( $params ) {
		$query_string = array();
		foreach( $params as $key => $value ) {
			$query_string[ $key ] = $key . '=' . $value;	
		}
		
		ksort( $query_string );
		
		return implode( '&', $query_string );
	}
	
	function do_get_request( $url ) {

		$request = new WP_Http;	
		$result = $request->request( $url );
			
		$this->response_code = $result[ 'response' ][ 'code' ];
		if ( $result['response']['code'] == '200' ) {
			return $result['body'];
		} else {
			return false;		
		}	
	}
	
	function do_request( $url, $oauth_header, $body_params = '' ) {		

			$request = new WP_Http;
			
			$params = array();
			if ( $body_params ) {
				foreach( $body_params as $key => $value ) {
					$body_params[ $key ] = ( $value );
				}
				
				$params['body'] = $body_params;	
			} 
			
			$params['method'] = 'POST';
			$params['headers'] = array( 'Authorization' => $oauth_header );
					
			$result = $request->request( $url, $params );

			if ( !is_wp_error( $result ) ) {

				$this->response_code = $result[ 'response' ][ 'code' ];
				
				if ( $result['response']['code'] == '200' ) {
					return $result['body'];
				} else {

	  				switch( $result['response']['code'] ) {
	  					case 403:
	  						$this->duplicate_tweet = true;
	  						break;
	  				}

					$error_message_found = preg_match( '#<error>(.*)</error>#i', $result[ 'body' ], $matches );
                                        if ( $error_message_found ) {
                                                $this->error_message = $matches[1];
                                        }

				}

			}

		
		return false;
	}
	
	function get_nonce() {
		return md5( mt_rand() + mt_rand() );	
	}
	
	function parse_params( $string_params ) {
		$good_params = array();
		
		$params = explode( '&', $string_params );
		foreach( $params as $param ) {
			$keyvalue = explode( '=', $param );
			$good_params[ $keyvalue[0] ] = $keyvalue[1];
		}
		
		return $good_params;
	}
	
	function set_oauth_time_offset( $offset ) {
		$this->oauth_time_offset = $offset;
	}
	
	function hmac_sha1( $key, $data ) {
		if ( function_exists( 'hash_hmac' ) ) {
			$hash = hash_hmac( 'sha1', $data, $key, true );	
			
			return $hash;
		} else {
			$blocksize = 64;
			$hashfunc = 'sha1';
			if ( strlen( $key ) >$blocksize ) {
				$key = pack( 'H*', $hashfunc( $key ) );
			}
			
			$key = str_pad( $key, $blocksize, chr(0x00) );
			$ipad = str_repeat( chr( 0x36 ), $blocksize );
			$opad = str_repeat( chr( 0x5c ), $blocksize );
			$hash = pack( 'H*', $hashfunc( ( $key^$opad ).pack( 'H*',$hashfunc( ($key^$ipad).$data ) ) ) );
			
			return $hash;
		}
	}
	
	function do_oauth( $url, $params, $token_secret = '' ) {
		$sig_string = $this->create_signature_base_string( false, $url, $params );
		
		//$hash = hash_hmac( 'sha1', $sig_string, fdx1_OAUTH_CONSUMER_SECRET . '&' . $token_secret, true );
		$hash = $this->hmac_sha1( $this->oauth_consumer_secret . '&' . $token_secret, $sig_string );
		$sig = base64_encode( $hash );
		
		$params['oauth_signature'] = $sig;
		
		$header = "OAuth ";
		$all_params = array();
		$other_params = array();
		foreach( $params as $key => $value ) {
			if ( strpos( $key, 'oauth_' ) !== false ) {
				$all_params[] = $key . '="' . $this->encode( $value ) . '"';
			} else {
				$other_params[ $key ] = $value;	
			}
		}
		
		$header .= implode( $all_params, ", " );
		
		return $this->do_request( $url, $header, $other_params );		
	}
//******************************************************
   	function get_oauth_params() {
		$params = array();
		$params['oauth_consumer_key'] = $this->oauth_consumer_key;;
		$params['oauth_signature_method'] = 'HMAC-SHA1';
		$params['oauth_timestamp'] = time() + $this->oauth_time_offset;
		$params['oauth_nonce'] = $this->get_nonce();
		$params['oauth_version'] = '1.0';
		return $params;
	}
//******************************************************

	function get_request_token() {
        $params = $this->get_oauth_params();
		$params['oauth_callback'] = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '&fdx1_oauth=1';

		$result = $this->do_oauth( fdx1_OAUTH_REQUEST_URL, $params );
		if ( $result ) {
			$new_params = $this->parse_params( $result );
			return $new_params;
		}
	}
	
	function get_access_token( $token, $token_secret, $verifier ) {
        $params = $this->get_oauth_params();
		$params['oauth_token'] = $token;
		$params['oauth_verifier'] = $verifier;
		
		$result = $this->do_oauth( fdx1_OAUTH_ACCESS_URL, $params, $token_secret );
		if ( $result ) {
			$new_params = $this->parse_params( $result );
			return $new_params;
		}
	}
	
	function update_status( $token, $token_secret, $status ) {
    	$params = $this->get_oauth_params();
    	$params['oauth_token'] = $token;
		$params['status'] = $status;

		$url = 'https://api.twitter.com/1.1/statuses/update.json';

		$result = $this->do_oauth( $url, $params, $token_secret );
		if ( $result ) {
		 //	$new_params = fdx1_parsexml( $result );
            $new_params = json_decode( $result );
			return true;
		} else {
			return false;	
		}	
	}	
	
	function was_duplicate_tweet() {
		return $this->duplicate_tweet;	
	}
	
	function get_auth_url( $token ) {
		return fdx1_OAUTH_AUTHORIZE_URL . '?oauth_token=' . $token;
	}
	

function setup() {
		define( 'fdx1_OAUTH_CONSUMER_SECRET', '8wBlmw81giyrDIVjobkPc1g4aJu5tMnsyGKVulE9k' );
	}
}

?>
