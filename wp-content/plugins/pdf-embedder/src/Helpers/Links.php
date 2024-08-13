<?php

namespace PDFEmbedder\Helpers;

use PDFEmbedder\Admin\License;

/**
 * Links class.
 *
 * @since 4.7.0
 */
class Links {

	/**
	 * Upgrade link used within the various admin pages.
	 *
	 * @since 4.7.0
	 *
	 * @param string $medium  URL parameter: utm_medium.
	 * @param string $content URL parameter: utm_content.
	 */
	public static function get_upgrade_link( string $medium = 'link', string $content = '' ): string {

		$url = 'https://wp-pdf.com/premium/';

		if ( pdf_embedder()->is_premium() ) {
			$url = add_query_arg(
				'license_key',
				sanitize_text_field( License::get_key() ),
				$url
			);
		}

		// phpcs:ignore WPForms.Comments.PHPDocHooks.RequiredHookDocumentation
		$upgrade = self::get_utm_link( $url, apply_filters( 'pdfemb_upgrade_link_medium', $medium ), $content );

		/**
		 * Modify upgrade link.
		 *
		 * @since 4.7.0
		 *
		 * @param string $upgrade Upgrade links.
		 */
		return apply_filters( 'pdfemb_upgrade_link', $upgrade );
	}

	/**
	 * Add UTM tags to a link that allows detecting traffic sources for our or partners' websites.
	 *
	 * @since 4.7.0
	 *
	 * @param string $link    Link to which you need to add UTM tags.
	 * @param string $medium  The page or location description. Check your current page and try to find
	 *                        and use an already existing medium for links otherwise, use a page name.
	 * @param string $content The feature's name, the button's content, the link's text, or something
	 *                        else that describes the element that contains the link.
	 * @param string $term    Additional information for the content that makes the link more unique.
	 */
	public static function get_utm_link( string $link, string $medium, string $content = '', string $term = '' ): string {

		$sanitize_key = static function ( $key = '' ): string {
			return preg_replace( '/[^a-zA-Z0-9_\-\.\:\/]/', '', $key );
		};

		/**
		 * Modify the source of the UTM link.
		 *
		 * @since 4.6.0
		 *
		 * @param string $source The source of the UTM link.
		 */
		$source = apply_filters( 'pdfemb_tracking_src', strpos( $link, 'https://wp-pdf.com' ) === 0 ? 'WordPress' : 'pdfplugin' );

		if ( defined( 'PDFEMB_TRACKING_SRC' ) && is_string( PDFEMB_TRACKING_SRC ) ) {
			$source = PDFEMB_TRACKING_SRC;
		}

		return add_query_arg(
			array_filter(
				[
					'utm_campaign' => pdf_embedder()->is_premium() ? 'plugin' : 'liteplugin',
					'utm_source'   => $source,
					'utm_medium'   => rawurlencode( $medium ),
					'utm_content'  => rawurlencode( $content ),
					'utm_term'     => rawurlencode( $term ),
					'utm_locale'   => $sanitize_key( get_locale() ),
				]
			),
			$link
		);
	}

	/**
	 * Generate a human-readable title from the URL.
	 *
	 * @since 4.7.0
	 *
	 * @param string $url Any URL.
	 */
	public static function make_title_from_url( string $url ): string {

		if ( preg_match( '|/([^/]+?)(\.pdf(\?[^/]*)?)?$|i', $url, $matches ) ) {
			return $matches[1];
		}

		return $url;
	}
}
