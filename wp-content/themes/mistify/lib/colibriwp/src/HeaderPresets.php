<?php


namespace ColibriWP\Theme;

use ColibriWP\Theme\Core\Hooks;
use ColibriWP\Theme\Core\Utils;

class HeaderPresets {

	private $headers_data = array();

	public function __construct() {
		$this->loadHeadersData();
		Hooks::prefixed_add_filter(
			'customizer_js_data',
			array( $this, 'addHeadersToJSData' )
		);
	}

	public function loadHeadersData() {

		if ( ! file_exists( Theme::rootDirectory() . '/inc/customizer-headers.php' ) ) {
			return;
		}

		$assets_base_url = Theme::rootDirectoryUri() . '/resources/header-presets';

		$headers = require_once Theme::rootDirectory() . '/inc/customizer-headers.php';
		foreach ( $headers as $index => $header ) {
			$image = Utils::pathGet( $header, 'image', '' );
			$data  = Utils::pathGet( $header, 'data', array() );

			foreach ( $data as $data_index => $value ) {

				$decoded_value = $this->maybeJSONDecode( $value );

				if ( ( is_array( $value ) || $decoded_value !== $value ) && is_array( $decoded_value ) ) {
					$decoded_value       = $this->sprintfRecursive( $decoded_value, $assets_base_url );
					$data[ $data_index ] = $decoded_value;
				} else {
					if ( is_string( $value ) ) {
						$data[ $data_index ] = sprintf( $value, $assets_base_url );
					}
				}
			}

			$fallback_keys = array(
				'header_front_page.icon_list.localProps.iconList',
				'header_front_page.social_icons.localProps.iconList',
			);

			foreach ( $fallback_keys as $fallback_key ) {
				$data[ $fallback_key ] = Defaults::get( $fallback_key );
			}

			$headers[ $index ] = array(
				'image' => sprintf( $image, "{$assets_base_url}/previews" ),
				'data'  => $data,
			);

			$this->headers_data = $headers;
		}
	}

	private function maybeJSONDecode( $value ) {
		if ( is_string( $value ) && strlen( trim( $value ) ) ) {
			// try to decode an url encoded value
			$maybe_value = json_decode( urldecode( $value ), true );

			if ( json_last_error() === JSON_ERROR_NONE ) {
				return $maybe_value;
			} else {
				// try to decode the value directly
				if ( is_string( $value ) ) {
					$maybe_value = json_decode( $value, true );
					if ( json_last_error() === JSON_ERROR_NONE ) {
						return $maybe_value;
					}
				}
			}
		}

		return $value;
	}

	public function sprintfRecursive( $array, $arg ) {

		if ( ! is_array( $array ) ) {
			if ( is_string( $array ) ) {
				return sprintf( $array, $arg );
			}

			return $array;
		}

		foreach ( $array as $index => $value ) {
			$array[ $index ] = $this->sprintfRecursive( $value, $arg );
		}

		return $array;
	}

	public function addHeadersToJSData( $data ) {
		$data['headers'] = $this->headers_data;

		return $data;
	}
}
