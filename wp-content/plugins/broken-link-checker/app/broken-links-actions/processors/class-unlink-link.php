<?php
/**
 * Executes the `Unlink` action on Broken Links.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.1
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2022, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Unlink_Link extends Base {
	/**
	 * Using a dynamic way to handle special cases like the Button Block of WP.
	 * This way it is easier to accept further special cases in future.
	 * Each case can have a 
	 * description : This is for developers to understand the reason of each case. It is not displayed anywhere.
	 * condition_callback: When content is considered special for each case. Accepts/requires input content and `needle`
	 * needle: The needle to be used in the `condition_callback`.
	 * action: A callback function that will be replace the traditional unlink. Accepts/requires input content, `tag_name` and `tag_att`.
	 *
	 * @var array
	 */
	protected $special_strings = array(
		'button_block' => array(
			'description'        => 'In WP Button reusable block, removing the <a> tag will give an error notice when editing a page with that reusable block. Instead we can remove the href att',
			'condition_callback' => 'str_starts_with',
			'needle'             => '<!-- wp:buttons -->',
			'action'             => 'rm_href_attribute',
		),
	);

	/**
	 * Executes the Processor's action.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @return bool
	 */
	public function execute( string $content = '', string $link = '', string $new_link = '' ) {
		if ( empty( $this->get_target_tags() ) ) {
			return $content;
		}

		$link         = untrailingslashit( trim( $link, '\'"' ) );
		$replacements = array();

		foreach ( $this->get_target_tags() as $tag_name => $tag_atts ) {
			if ( ! empty( $tag_atts ) ) {
				foreach ( $tag_atts as $tag_att ) {
					// First try with `DOMDocument` by default.
					if ( apply_filters( 'wpmudev_blc_link_action_use_dom', true, 'unink', $link ) ) {
						$replacements = $this->use_domdocument_processor( $content, $link, $new_link, $tag_name, $tag_att );
					} else {
						// Optionally in case Regex is preferred, the filter `wpmudev_blc_link_action_use_dom` can be set to return false.
						$replacements = $this->use_regex_processor( $content, $link, $new_link, $tag_name, $tag_att );
					}
				}
			}
		}

		return empty( $replacements ) ? $content : str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
	}

	/**
	 * Checks if content requires special handling based on cases registered in `$this->special_strings`.
	 *
	 * @param string $content
	 * @return array
	 */
	public function content_special_actions( string $content = '' ) {
		if ( empty( $this->special_strings ) ) {
			return array();
		}

		// An array for storing the special actions for this content, if there are any.
		$special_actions = array();

		foreach ( $this->special_strings as $special_case_key => $special_case_data ) {
			if ( empty( $special_case_data['condition_callback'] ) || empty( $special_case_data['action'] ) ) {
				continue;
			}

			// First we need to make sure that the callback is valid and callable.
			if (
				is_callable( array( $this, $special_case_data['condition_callback'] ) ) &&
				is_callable( array( $this, $special_case_data['action'] ) )
			) {
				// Now we need to check if the content will fit the callback's condition(s).
				if ( call_user_func(
					array( $this, $special_case_data['condition_callback'] ),
					trim( $content ),
					! empty( $special_case_data['needle'] ) ? $special_case_data['needle'] : ''
				)
				) {
					$special_actions[ $special_case_key ] = $special_case_data['action'];
				}
			}

		}

		return $special_actions;
	}

	/**
	 * Checks if a string starts with given needle.
	 *
	 * @param string $haystack
	 * @param string $needle
	 * @return bool
	 */
	public function str_starts_with( string $haystack = '', string $needle = '' ) {
		if ( function_exists( 'str_starts_with' ) ) {
			return str_starts_with( $haystack, $needle );
		}

		if ( '' === $needle ) {
			return true;
		}

		return 0 === strpos( $haystack, $needle );
	}

	/**
	 * Processes content using `DOMDocument`.
	 * So far haven't found a way to utilize `WP_HTML_Tag_Processor` to unlink because the WP_HTML_Tag_Processor class does not return the element's markup, nor it's position in content, in order to replace with new markup.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return array
	 */
	public function use_domdocument_processor( string $content = '', string $link = '', string $new_link = '', string $tag_name = '', string $tag_att = '' ) {
		$dom          = new \DOMDocument();
		$replacements = array();

		libxml_use_internal_errors( true );

		$dom->loadHTML( $content );

		foreach ( $dom->getElementsByTagName( $tag_name ) as $dom_link ) {
			$search_markup      = '';
			$replacement_str    = '';
			$old_link           = $dom_link->getAttribute( $tag_att );
			$site_url           = site_url();
			$scheme             = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':';
			$absolute_link      = $site_url . $link;
			$semi_absolute_link = str_replace( $scheme, '', $absolute_link );
			$links_match        = strcasecmp( trailingslashit( $link ), trailingslashit( $old_link ) ) == 0 ||
				strcasecmp( trailingslashit( $absolute_link ), trailingslashit( $old_link ) ) == 0 ||
				strcasecmp( trailingslashit( $semi_absolute_link ), trailingslashit( $old_link ) ) == 0;

			if ( $links_match ) {
				$search_markup   = $dom->saveHTML( $dom_link );
				$replacement_str = $dom_link->nodeValue;

				if ( ! empty( $replacements[ $search_markup ] ) ) {
					continue;
				}

				$special_actions = $this->content_special_actions( $content );

				if ( ! empty( $special_actions ) ) {
					$replacement_str = $search_markup;

					foreach ( $special_actions as $special_case_key => $callback ) {
						if ( is_callable( array( $this, $callback ) ) ) {
							$replacement_str = call_user_func(
								array( $this, $callback ),
								$replacement_str,
							);
						}
					}

					$replacements[ $search_markup ] = $replacement_str;

					continue;
				}

				if ( apply_filters( 'wpmudev_blc_link_action_unlink_wrap', false, $link, $new_link ) ) {
					$replacement_el = $dom->createElement( 'span', $replacement_str );
					$replacement_el->setAttribute( 'class', apply_filters( 'wpmudev_blc_link_action_unlink_wrap_class', 'blc_unlinked', $link, $new_link ) );
					$replacement_el->setAttribute( 'data-blc-orig-url', $link );
					$replacement_str = $dom->saveHTML( $replacement_el );
				}

				$replacements[ $search_markup ] = $replacement_str;
			}
		}

		return $replacements;
	}

	/**
	 * Processes content using regex.
	 * Regex is default way for wp version < 6.2, even if not preferred. 
	 * The reason we use regex by default is because:
	 * 1. WP_HTML_Tag_Processor isn't introduced until v 6.2.
	 * 2. DOM will fix any invalid markup which might not be desired.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return string
	 */
	public function use_regex_processor( string $content = '', string $link = '', string $new_link = '', string $tag_name = '', string $tag_att = '' ) {
		$regexp       = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";
		$replacements = array();

		if ( preg_match_all( "/$regexp/siU", $content, $matches ) ) {

			if ( ! empty( $matches[0] ) ) {
				foreach ( $matches[0] as $key => $markup ) {
					$old_link    = untrailingslashit( trim( $matches[2][ $key ], '\'"' ) );
					$links_match = false;

					/**
					 * If link doesn't have Host part, it is internal link. All internal links are stored as relative urls in Queue.
					 * If link does have the Host part, then it's an external link.
					 * For internal links we need to test 3 cases:
					 * 1. Relative urls (that is how it is stored in Queue)
					 * 2. Absolute url.
					 * 3. Absolute url without scheme ( //site.com )
					 * Engine treats all those types as full urls
					 */
					if ( empty( wp_parse_url( $link, PHP_URL_HOST ) ) ) {
						// Check Relative, Absolute and Absolute without host urls for $old_link.
						$site_url           = site_url();
						$scheme             = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':';
						$absolute_link      = $site_url . $link;
						$semi_absolute_link = str_replace( $scheme, '', $absolute_link );
						$links_match        = strcasecmp( trailingslashit( $link ), trailingslashit( $old_link ) ) == 0 ||
							strcasecmp( trailingslashit( $absolute_link ), trailingslashit( $old_link ) ) == 0 ||
							strcasecmp( trailingslashit( $semi_absolute_link ), trailingslashit( $old_link ) ) == 0;
					} else {
						// Check $old_link normally.
						$links_match = strcasecmp( trailingslashit( $old_link ), trailingslashit( $link ) ) == 0;
					}

					if ( $links_match ) {
						$replacements[ $markup ] = $matches[3][ $key ];
					}
				}
			}
		}

		return $replacements;
	}

	/**
	 * This is the callback function that is set in `$this->special_strings` var.
	 * It removes the `$tag_att` (default `href`) args from the `$tag_name` (default `<a>` tag) of the input var ($content).
	 *
	 * @param string $content
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return string
	 */
	public function rm_href_attribute( string $content = '', string $tag_name = 'a', string $tag_att = 'href' ) {
		$dom = new \DOMDocument();

		libxml_use_internal_errors( true );

		$dom->loadHTML( $content );

		foreach ( $dom->getElementsByTagName( $tag_name ) as $dom_link ) {
			$dom_link->removeAttribute( $tag_att );

			//$style = $dom_link->getAttribute( 'style' );
			//$dom_link->setAttribute( 'style', "{$style} color: inherit; text-decoration: inherit;" );

			$content = $dom->saveHTML( $dom_link );
		}

		return $content;
	}

	/**
	 * Provides a list of tags and attributes in which BLC will search for broken links.
	 *
	 * @return array
	 */
	public function get_target_tags() {

		return apply_filters(
			'wpmudev_blc_replace_target_tags',
			array(
				'a' => array( 'href' ),
			)
		);
	}
}
