<?php
namespace AIOSEO\TableOfContents\Traits\Helpers;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Contains string specific helper methods.
 *
 * @since 1.0.0
 */
trait Strings {
	/**
	 * Escapes special regex characters.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string    The string.
	 * @param  string $delimiter The delimiter character.
	 * @return string            The escaped string.
	 */
	public function escapeRegex( $string, $delimiter = '/' ) {
		static $escapeRegex = [];
		if ( isset( $escapeRegex[ $string ] ) ) {
			return $escapeRegex[ $string ];
		}
		$escapeRegex[ $string ] = preg_quote( (string) $string, $delimiter );

		return $escapeRegex[ $string ];
	}

	/**
	 * Escapes special regex characters inside the replacement string.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The escaped string.
	 */
	public function escapeRegexReplacement( $string ) {
		static $escapeRegexReplacement = [];
		if ( isset( $escapeRegexReplacement[ $string ] ) ) {
			return $escapeRegexReplacement[ $string ];
		}

		$escapeRegexReplacement[ $string ] = str_replace( '$', '\$', $string );

		return $escapeRegexReplacement[ $string ];
	}

	/**
	 * preg_replace but with the replacement escaped.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $pattern     The pattern to search for.
	 * @param  string $replacement The replacement string.
	 * @param  string $subject     The subject to search in.
	 * @return string              The subject with matches replaced.
	 */
	public function pregReplace( $pattern, $replacement, $subject ) {
		$key = $pattern . $replacement . $subject;

		static $pregReplace = [];
		if ( isset( $pregReplace[ $key ] ) ) {
			return $pregReplace[ $key ];
		}

		// We can use the following pattern for this - (?<!\\)([\/.^$*+?|()[{}\]]{1})
		// The pattern above will only escape special characters if they're not escaped yet, which makes it compatible with all our patterns that are already escaped.
		// The caveat is that we'd need to first trim off slash delimiters and add them back later - otherwise they'd be escaped as well.

		$replacement         = $this->escapeRegexReplacement( $replacement );
		$pregReplace[ $key ] = preg_replace( $pattern, $replacement, (string) $subject );

		return $pregReplace[ $key ];
	}

	/**
	 * Returns the index of a substring in a string.
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $stack  The stack.
	 * @param  string   $needle The needle.
	 * @param  int      $offset The offset.
	 * @return int|bool         The index where the string starts or false if it does not exist.
	 */
	public function stringIndex( $stack, $needle, $offset = 0 ) {
		$key = $stack . $needle . $offset;

		static $stringIndex = [];
		if ( isset( $stringIndex[ $key ] ) ) {
			return $stringIndex[ $key ];
		}

		$stringIndex[ $key ] = function_exists( 'mb_strpos' ) ? mb_strpos( $stack, $needle, $offset, get_option( 'blog_charset' ) ) : strpos( $stack, $needle, $offset );

		return $stringIndex[ $key ];
	}

	/**
	 * Checks if the given string contains the given substring.
	 *
	 * @since 1.0.0
	 *
	 * @param  string   $stack  The stack.
	 * @param  string   $needle The needle.
	 * @param  int      $offset The offset.
	 * @return bool             Whether the substring occurs in the main string.
	 */
	public function stringContains( $stack, $needle, $offset = 0 ) {
		$key = $stack . $needle . $offset;

		static $stringContains = [];
		if ( isset( $stringContains[ $key ] ) ) {
			return $stringContains[ $key ];
		}

		$stringContains[ $key ] = false !== $this->stringIndex( $stack, $needle, $offset );

		return $stringContains[ $key ];
	}

	/**
	 * Check if a string is JSON encoded or not.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string to check.
	 * @return bool           True if it is JSON or false if not.
	 */
	public function isJsonString( $string ) {
		if ( ! is_string( $string ) ) {
			return false;
		}

		json_decode( $string );

		// Return a boolean whether or not the last error matches.
		return json_last_error() === JSON_ERROR_NONE;
	}

	/**
	 * Returns the string after all HTML entities have been decoded.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string to decode.
	 * @return string         The decoded string.
	 */
	public function decodeHtmlEntities( $string ) {
		static $decodeHtmlEntities = [];
		if ( isset( $decodeHtmlEntities[ $string ] ) ) {
			return $decodeHtmlEntities[ $string ];
		}

		// We must manually decode non-breaking spaces since html_entity_decode doesn't do this.
		$string                        = $this->pregReplace( '/&nbsp;/', ' ', $string );
		$decodeHtmlEntities[ $string ] = html_entity_decode( (string) $string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401 );

		return $decodeHtmlEntities[ $string ];
	}

	/**
	 * Returns the string with script tags stripped.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The modified string.
	 */
	public function stripScriptTags( $string ) {
		static $stripScriptTags = [];
		if ( isset( $stripScriptTags[ $string ] ) ) {
			return $stripScriptTags[ $string ];
		}

		$stripScriptTags[ $string ] = $this->pregReplace( '/<script(.*?)>(.*?)<\/script>/is', '', $string );

		return $stripScriptTags[ $string ];
	}

	/**
	 * Returns the string with incomplete HTML tags stripped.
	 * Incomplete tags are not unopened/unclosed pairs but rather single tags that aren't properly formed.
	 * e.g. <a href='something'
	 * e.g. href='something' >
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The modified string.
	 */
	public function stripIncompleteHtmlTags( $string ) {
		static $stripIncompleteHtmlTags = [];
		if ( isset( $stripIncompleteHtmlTags[ $string ] ) ) {
			return $stripIncompleteHtmlTags[ $string ];
		}

		$stripIncompleteHtmlTags[ $string ] = $this->pregReplace( '/(^(?!<).*?(\/>)|<[^>]*?(?!\/>)$)/is', '', $string );

		return $stripIncompleteHtmlTags[ $string ];
	}

	/**
	 * Trims HTML paragraph from the start/end of the given string.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The string.
	 * @return string         The modified string.
	 */
	public function trimParagraphTags( $string ) {
		$string = preg_replace( '/^<p[^>]*>/', '', (string) $string );
		$string = preg_replace( '/<\/p>/', '', (string) $string );

		return trim( $string );
	}

	/**
	 * Implodes an array into a WHEREIN clause useable string.
	 *
	 * @since 1.0.0
	 *
	 * @param  array  $array       The array.
	 * @param  bool   $outerQuotes Whether outer quotes should be added.
	 * @return string              The imploded array.
	 */
	public function implodeWhereIn( $array, $outerQuotes = false ) {
		// Reset the keys first in case there is no 0 index.
		$array = array_values( $array );

		if ( ! isset( $array[0] ) ) {
			return '';
		}

		if ( is_numeric( $array[0] ) ) {
			return implode( ', ', $array );
		}

		return $outerQuotes ? "'" . implode( "', '", $array ) . "'" : implode( "', '", $array );
	}

	/**
	 * Returns string after converting it to lowercase.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $string The original string.
	 * @return string         The string converted to lowercase.
	 */
	public function toLowerCase( $string ) {
		static $lowerCased = [];
		if ( isset( $lowerCased[ $string ] ) ) {
			return $lowerCased[ $string ];
		}
		$lowerCased[ $string ] = function_exists( 'mb_strtolower' ) ? mb_strtolower( $string, $this->getCharset() ) : strtolower( $string );

		return $lowerCased[ $string ];
	}

	/**
	 * Convert to camelCase.
	 *
	 * @since 1.1.0
	 *
	 * @param  string $string     The string to convert.
	 * @param  bool   $capitalize Whether to capitalize the first letter.
	 * @return string             The converted string.
	 */
	public function toCamelCase( $string, $capitalize = false ) {
		$string[0] = strtolower( $string[0] );
		if ( $capitalize ) {
			$string[0] = strtoupper( $string[0] );
		}

		return preg_replace_callback( '/_([a-z0-9])/', function ( $value ) {
			return strtoupper( $value[1] );
		}, $string );
	}
}