<?php
/**
 * File that includes the SiteOrigin Builder link processor class.
 *
 * @package WPMUDEV_BLC\App\Integrations\SiteOrigin
 */

namespace WPMUDEV_BLC\App\Integrations\SiteOrigin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class SiteOrigin_Link_Processor
 *
 * @package WPMUDEV_BLC\App\Integrations\SiteOrigin
 */
class SiteOrigin_Link_Processor {
	const BUTTON_CLASS = 'SiteOrigin_Widget_Button_Widget';
	const IMAGE_CLASS  = 'WP_Widget_Media_Image';

	/**
	 * URL keys to search for inside each widget's instance, in priority order.
	 * Button widgets use 'url'; Media Image widgets use 'link_url'.
	 *
	 * @var string[]
	 */
	const URL_KEYS = array( 'url', 'link_url' );

	/**
	 * Execute the content processing logic for the given tag/attribute pair.
	 *
	 * Unlink implementations return an array: [ $search_markup => $replacement_str, ... ]
	 * Replace implementations return the modified content string.
	 * Replace_Wp_Native_Processor returns null when the WP native processor is unavailable.
	 *
	 * @param string $content The original content.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL (empty for unlink).
	 * @param array  $tags The HTML tags and attributes to search within.
	 * @return string The processed content.
	 */
	public function process(
		string $content,
		string $link,
		string $new_link,
		array $tags
	) {

		if ( empty( $content ) ) {
			// No content to process, return as is.
			return $content;
		}

		if ( empty( $tags['a'] ) || ! in_array( 'href', $tags['a'], true ) ) {
			// No 'a' tags with 'href' attributes to process, return as is.
			return $content;
		}

		// Work on the original (entity-encoded) content so attribute delimiters are
		// never ambiguous. Real SiteOrigin output stores JSON with &quot;-encoded
		// double-quotes, so [^"]* / [^>]* patterns are safe here.
		$offset          = 0;
		$closing_tag_len = strlen( '[/siteorigin_widget]' );
		$widget_classes  = array( self::BUTTON_CLASS, self::IMAGE_CLASS );

		// Find the next occurrence of any supported widget class.
		while ( false !== ( $button_class_pos = $this->find_next_class( $content, $widget_classes, $offset ) ) ) { // phpcs:ignore

			$widget_start = $this->find_widget_start( $content, $button_class_pos );
			if ( false === $widget_start ) {
				// No widget start found, move offset past the current button class position to avoid infinite loop and continue searching.
				$offset = $button_class_pos + strlen( self::BUTTON_CLASS );
				continue;
			}

			$widget_end = $this->find_widget_end( $content, $widget_start );
			if ( false === $widget_end ) {
				// No closing tag exists anywhere after this point, so no further
				// widget can be valid either. Stop searching.
				break;
			}

			// Extract the widget content and check for the presence of a hidden input field.
			$widget_content   = substr( $content, $widget_start, $widget_end - $widget_start );
			// Look for a hidden input field within the widget content.
			$has_hidden_input = preg_match( '/<input[^>]+type=["\']hidden["\'][^>]*>/', $widget_content, $matches );
			if ( ! $has_hidden_input || empty( $matches[0] ) ) {
				// No hidden input found, move offset past the current widget to avoid infinite loop and continue searching.
				$offset = $widget_end + $closing_tag_len;
				continue;
			}

			// In the original content, inner double-quotes in the value are &quot;,
			// so [^"]* safely captures the entire JSON blob without ambiguity.
			$has_value = preg_match( '/value="([^"]*)"/', $matches[0], $value_matches );
			if ( ! $has_value || empty( $value_matches[1] ) ) {
				// No value attribute found, move offset past the current widget to avoid infinite loop and continue searching.
				$offset = $widget_end + $closing_tag_len;
				continue;
			}

			// Replace URL keys directly in the entity-encoded value string.
			// json_decode/encode cannot be used here: args values such as
			// before_widget contain raw HTML with unescaped double-quotes that
			// make the decoded string invalid JSON. The entity-encoded form is
			// safe because every inner " is still stored as &quot;.
			$encoded_link     = htmlspecialchars( $link, ENT_QUOTES | ENT_HTML5 );
			$encoded_new_link = htmlspecialchars( $new_link, ENT_QUOTES | ENT_HTML5 );

			// Match &quot;KEY&quot;:&quot;LINK&quot; for each supported URL key.
			$url_keys_pattern = implode( '|', array_map( 'preg_quote', self::URL_KEYS ) );
			$url_pattern      = '/(&quot;(?:' . $url_keys_pattern . ')&quot;:&quot;)' . preg_quote( $encoded_link, '/' ) . '(&quot;)/';
			$new_value        = preg_replace( $url_pattern, '${1}' . $encoded_new_link . '${2}', $value_matches[1] );

			if ( null === $new_value || $new_value === $value_matches[1] ) {
				// URL not matched or regex error, move offset past the current widget.
				$offset = $widget_end + $closing_tag_len;
				continue;
			}

			$updated_hidden_input = str_replace( $value_matches[1], $new_value, $matches[0] );
			$content              = str_replace( $matches[0], $updated_hidden_input, $content );

			// Recalculate offset past the current widget's closing tag in the updated content.
			$closing_tag_pos = strpos( $content, '[/siteorigin_widget]', $widget_start );
			$offset          = false !== $closing_tag_pos ? $closing_tag_pos + $closing_tag_len : $widget_end + $closing_tag_len;
		}

		return $content;
	}

	/**
	 * Find the position of the next occurrence of any widget class in $classes,
	 * starting from $offset. Returns the earliest match position, or false.
	 *
	 * @param string   $content  The content to search.
	 * @param string[] $classes  List of widget class names to look for.
	 * @param int      $offset   Starting position for the search.
	 * @return int|false
	 */
	private function find_next_class( string $content, array $classes, int $offset ) {
		$earliest = false;
		foreach ( $classes as $class ) {
			$pos = strpos( $content, $class, $offset );
			if ( false !== $pos && ( false === $earliest || $pos < $earliest ) ) {
				$earliest = $pos;
			}
		}
		return $earliest;
	}

	/**
	 * Find the start position of the SiteOrigin widget containing the button.
	 *
	 * @param string $content The decoded content string.
	 * @param int    $button_class_pos The position of the button class in the content.
	 * @return int|false The position of the widget start or false if not found.
	 */
	private function find_widget_start( string $content, int $button_class_pos ) {
		return strrpos( $content, '[siteorigin_widget', -( strlen( $content ) - $button_class_pos ) );
	}

	/**
	 * Find the end position of the SiteOrigin widget containing the button.
	 *
	 * @param string $content The decoded content string.
	 * @param int    $widget_start The position of the widget start in the content.
	 * @return int|false The position of the widget end or false if not found.
	 */
	private function find_widget_end( string $content, int $widget_start ) {
		return strpos( $content, '[/siteorigin_widget]', $widget_start );
	}
}
