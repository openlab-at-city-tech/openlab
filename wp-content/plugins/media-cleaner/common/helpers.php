<?php

if ( !class_exists( 'MeowCommon_Helpers' ) ) {

	class MeowCommon_Helpers {
	
		static function is_divi_builder() {
			return isset( $_GET['et_fb'] ) && $_GET['et_fb'] === '1';
		}

		static function is_asynchronous_request() {
			return self::is_ajax_request() || self::is_woocommerce_ajax_request() || self::is_rest();
		}

		static function is_ajax_request() {
			return wp_doing_ajax();
		}

		static function is_woocommerce_ajax_request() {
			return !empty( $_GET['wc-ajax'] );
		}

		// Originally created by matzeeable, modified by jordymeow
		static function is_rest() {

			// WP_REST_Request init.
			$is_rest_request = defined('REST_REQUEST') && REST_REQUEST;
			if ( $is_rest_request ) {
				MeowCommon_Classes_Rest::init_once();
				return true;
			}

			// Plain permalinks.
			$prefix = rest_get_url_prefix();
			$request_contains_rest = isset( $_GET['rest_route'] ) && strpos( trim( $_GET['rest_route'], '\\/' ), $prefix , 0 ) === 0;
			if ( $request_contains_rest) {
				MeowCommon_Classes_Rest::init_once();
				return true;
			}		

			// It can happen that WP_Rewrite is not yet initialized, so better to do it.
			global $wp_rewrite;
			if ( $wp_rewrite === null ) { 
				$wp_rewrite = new WP_Rewrite();
			}
			$rest_url = wp_parse_url( trailingslashit( rest_url() ) );
			$current_url = wp_parse_url( add_query_arg( array() ) );
			if ( !$rest_url || !$current_url )
				return false;

			// URL Path begins with wp-json.
			if ( !empty( $current_url['path'] ) && !empty( $rest_url['path'] ) ) {
				$request_contains_rest = strpos( $current_url['path'], $rest_url['path'], 0 ) === 0;
				if ( $request_contains_rest) {
					MeowCommon_Classes_Rest::init_once();
					return true;
				}
			}

			return false;
		}

		static function test_error( $error = 'timeout', $diceSides = 1 ) {
			if ( rand( 1, $diceSides ) === 1 ) {
				if ( $error === 'timeout' ) {
					header("HTTP/1.0 408 Request Timeout");
					die();
				}
				else {
					trigger_error( "Error", E_USER_ERROR);
				}
			}
		}
	}

	if ( MeowCommon_Helpers::is_rest() ) {
		ini_set( 'display_errors', 0 );
	}
}

?>