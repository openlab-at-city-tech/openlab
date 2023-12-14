<?php

namespace Imagely\NGG\DataStorage;

class Sanitizer {

	public static function strip_html( $data, $just_scripts = false ) {
		// NGG 3.3.11 fix. Some of the data persisted with 3.3.11 didn't strip out all HTML.
		if ( strpos( $data, 'ngg_data_strip_html_placeholder' ) !== false ) {
			if ( class_exists( 'DomDocument' ) ) {
				$dom = new \DOMDocument( '1.0', 'UTF-8' );
				$dom->loadHTML( $data );
				$el    = $dom->getElementById( 'ngg_data_strip_html_placeholder' );
				$parts = array_map(
					function ( $el ) use ( $dom ) {
						$part = $dom->saveHTML( $el );
						return $part instanceof \DOMText ? $part->data : (string) $part;
					},
					$el->childNodes ? iterator_to_array( $el->childNodes ) : []
				);
				return self::strip_html( implode( ' ', $parts ), $just_scripts );
			} else {
				return \wp_strip_all_tags( $data );
			}
		}

		// Remove all HTML elements.
		if ( ! $just_scripts ) {
			return \wp_strip_all_tags( $data );
		} elseif ( class_exists( 'DOMDocument' ) ) {
			// Remove unsafe HTML. This can generate a *lot* of warnings when given improper texts.
			libxml_use_internal_errors( true );
			libxml_clear_errors();

			$config = \HTMLPurifier_Config::createDefault();
			$config->set( 'Cache.DefinitionImpl', null );
			$purifier       = new \HTMLPurifier( $config );
			$default_return = $purifier->purify( $data );
			return \apply_filters( 'ngg_html_sanitization', $default_return, $data );
		} else {
			// wp_strip_all_tags() is misleading in a way - it only removes <script> and <style> tags.
			return \wp_strip_all_tags( $data, true );
		}
	}
}
