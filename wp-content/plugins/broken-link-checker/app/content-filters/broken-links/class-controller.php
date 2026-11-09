<?php
/**
 * Core content filter that highlights broken links in post content on the front end.
 * Runs automatically as part of the plugin's core functionality by hooking into
 * `the_content` — no manual action (replace/unlink/nofollow) is required.
 *
 * Broken link URLs are sourced from the scan results already stored in the plugin's
 * Settings model, so no additional HTTP requests are made at render time.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.5.0
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Content_Filters\Broken_Links
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Content_Filters\Broken_Links;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Options\Settings\Model as Settings;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Controller
 *
 * @package WPMUDEV_BLC\App\Content_Filters\Broken_Links
 */
class Controller extends Base {

	/**
	 * Cached list of broken link URLs for the current request.
	 *
	 * @var string[]|null
	 */
	private $broken_urls = null;

	/**
	 * Registers WordPress hooks.
	 *
	 * @return void
	 */
	public function init() {
		// Only run when the Cloud Engine is active (use_legacy_blc_version === false).
		if ( ! empty( Settings::instance()->get( 'use_legacy_blc_version' ) ) ) {
			return;
		}

		/**
		 * Allow the entire highlight feature to be disabled via filter.
		 * Return false to prevent broken links from being highlighted in content.
		 *
		 * @since 2.5.0
		 * @param bool $enabled Whether highlighting is enabled. Default true.
		 */
		if ( ! apply_filters( 'wpmudev_blc_highlight_broken_links', true ) ) {
			return;
		}

		// Only run on the front end, not in the admin.
		if ( ! is_admin() ) {
			add_filter( 'the_content', array( $this, 'highlight_broken_links' ), 20 );
			add_action( 'wp_head', array( $this, 'output_highlight_styles' ) );
		}
	}

	/**
	 * Filters post content and adds a CSS class plus data attribute to every
	 * anchor whose href matches a known broken link URL.
	 *
	 * @param string $content The post content.
	 * @return string Modified content.
	 */
	public function highlight_broken_links( string $content ) {
		if ( empty( $content ) ) {
			return $content;
		}

		$broken_urls = $this->get_broken_urls();

		if ( empty( $broken_urls ) ) {
			return $content;
		}

		// Fast bail: none of the broken URLs appear anywhere in the content.
		// Check both the raw URL and its HTML-encoded form (&amp;) because WordPress
		// serialises ampersands in href attributes as &amp;.
		$found = false;
		foreach ( $broken_urls as $url ) {
			if (
				strpos( $content, $url ) !== false ||
				strpos( $content, htmlspecialchars( $url, ENT_QUOTES ) ) !== false
			) {
				$found = true;
				break;
			}
		}

		if ( ! $found ) {
			return $content;
		}

		$css_class = apply_filters( 'wpmudev_blc_highlight_css_class', 'blc-broken-link' );
		$data_attr = apply_filters( 'wpmudev_blc_highlight_data_attr', 'data-blc-broken' );

		/*
		 * Both values arrive from filters and are written straight into a tag, so
		 * neither can be trusted. Reduce the class to what sanitize_html_class()
		 * allows, and hold the attribute name to a data-* name.
		 */
		$css_class = implode(
			' ',
			array_filter(
				array_map(
					'sanitize_html_class',
					preg_split( '/\s+/', (string) $css_class, -1, PREG_SPLIT_NO_EMPTY )
				)
			)
		);

		if ( '' === $css_class ) {
			$css_class = 'blc-broken-link';
		}

		if ( ! preg_match( '/^data-[A-Za-z0-9_-]+$/', (string) $data_attr ) ) {
			$data_attr = 'data-blc-broken';
		}

		// Replace & with (?:&amp;|&) so the pattern matches both the raw form
		// and the HTML-entity form that WordPress writes into href attributes.
		$broken_urls_escaped = array_map(
			static function ( $url ) {
				$url = preg_quote( $url, '/' );
				return str_replace( '&', '(?:&amp;|&)', $url );
			},
			$broken_urls
		);
		$urls_pattern        = implode( '|', $broken_urls_escaped );

		$modified = false;

		$pattern     = '/(<a\s[^>]*href=["\'])(' . $urls_pattern . ')(\/?)(["\'][^>]*>)/i';
		$new_content = preg_replace_callback(
			// Match anchor tags with href attributes containing any of the broken URLs.
			$pattern,
			function ( $matches ) use ( $css_class, $data_attr, &$modified ) {
				$tag = $matches[0];

				// Nothing to do if this anchor already carries the marker.
				if ( false !== stripos( $tag, $data_attr . '=' ) ) {
					return $tag;
				}

				$modified = true;

				/*
				 * The opening tag is kept exactly as it was written and only added to.
				 * Rebuilding it around a fixed pair of double quotes used to let a quote
				 * inside an originally single-quoted href close the attribute and open an
				 * event handler, and it also dropped every other attribute on the anchor.
				 */
				$class_merges = 0;
				$merged       = preg_replace(
					'/(\sclass\s*=\s*)(["\'])(.*?)\2/i',
					'${1}${2}${3} ' . $css_class . '${2}',
					$tag,
					1,
					$class_merges
				);

				if ( $class_merges > 0 && null !== $merged ) {
					// Appended to the class already on the tag; a second class attribute would be ignored.
					$tag = $merged;
				} else {
					$tag = preg_replace( '/\s*>$/', ' class="' . $css_class . '">', $tag, 1 );
				}

				return preg_replace( '/\s*>$/', ' ' . $data_attr . '="1">', $tag, 1 );
			},
			$content
		);

		if ( ! $modified || null === $new_content ) {
			return $content;
		}

		return $new_content;
	}

	/**
	 * Outputs minimal inline CSS for broken link highlighting when the page has
	 * any broken links in its content. Falls back gracefully if no links exist.
	 *
	 * Override the appearance via CSS targeting `.blc-broken-link` (default class)
	 * or by filtering `wpmudev_blc_highlight_css_class`.
	 *
	 * @return void
	 */
	public function output_highlight_styles() {
		if ( empty( $this->get_broken_urls() ) ) {
			return;
		}

		// Skip for logged-out users or users whose only capability is 'read' (i.e. subscribers).
		if (
			! is_user_logged_in() ||
			! current_user_can( 'edit_posts' )
		) {
			return;
		}

		$css_class = apply_filters( 'wpmudev_blc_highlight_css_class', 'blc-broken-link' );

		/**
		 * Filter the inline CSS output for highlighted broken links.
		 * Return an empty string to suppress default styles entirely.
		 *
		 * @since 2.5.0
		 * @param string $css     The default CSS rules.
		 * @param string $css_class The CSS class applied to broken link anchors.
		 */
		$css = apply_filters(
			'wpmudev_blc_highlight_inline_css',
			sprintf(
				'.%s { outline: 2px solid #e53e3e; outline-offset: 1px; }',
				esc_attr( $css_class )
			),
			$css_class
		);

		if ( ! empty( $css ) ) {
			printf( '<style id="blc-broken-link-highlight">%s</style>' . "\n", wp_strip_all_tags( $css ) );
		}
	}

	/**
	 * Returns a deduplicated, normalised list of broken link URLs from the last
	 * completed scan. Results are cached for the lifetime of the request.
	 *
	 * @return string[]
	 */
	private function get_broken_urls(): array {
		if ( ! is_null( $this->broken_urls ) ) {
			return $this->broken_urls;
		}

		$this->broken_urls = array();

		$scan_results = Settings::instance()->get( 'scan_results' );
		if ( empty( $scan_results ) || ! is_array( $scan_results ) ) {
			return $this->broken_urls;
		}

		$broken_links_list = $scan_results['broken_links_list'] ?? null;

		if ( empty( $broken_links_list ) || ! is_array( $broken_links_list ) ) {
			return $this->broken_urls;
		}

		foreach ( $broken_links_list as $broken_link ) {
			if ( is_object( $broken_link ) ) {
				$broken_link = (array) $broken_link;
			}

			if ( ! empty( $broken_link['is_ignored'] ) ) {
				continue;
			}

			$url = $broken_link['url'] ?? '';

			if ( ! empty( $url ) ) {
				$this->broken_urls[] = untrailingslashit( trim( $url, '\'"' ) );
			}
		}

		$this->broken_urls = array_unique( $this->broken_urls );

		return $this->broken_urls;
	}
}
