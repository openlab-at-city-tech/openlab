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

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Unlink_Link extends Link_Processor {
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
	/*protected $special_strings = array(
		'reusable_button_block' => array(
			'description'        => 'In WP Button reusable block, removing the <a> tag will make button show as a simple string. Instead we can remove only the href att',
			'condition_callback' => array( $this, 'str_starts_with' ),
			'needle'             => '<!-- wp:buttons -->',
			'action'             => array( $this, 'rm_href_attribute' ),
		),
		'button_blocks' => array(
			'description'        => 'In WP Button block, removing the <a> tag will make button show as a simple string. Instead we can remove only the href att',
			'condition_callback' => array( $this, 'is_specific_block' ),
			'needle'             => '<!-- wp:buttons -->',
			'action'             => array( $this, 'rm_href_attribute' ),
		),
	);*/

	/**
	 * Executes the Processor's action.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @return string
	 */
	public function execute( string $content = null, string $link = null, string $new_link = null ) {
		if ( empty( $this->get_target_tags() ) || empty( str_replace( PHP_EOL, '', $content ) ) ) {
			return $content;
		}

		$link         = untrailingslashit( trim( $link, '\'"' ) );
		$replacements = array();

		foreach ( $this->get_target_tags() as $tag_name => $tag_atts ) {
			if ( ! empty( $tag_atts ) ) {
				foreach ( $tag_atts as $tag_att ) {
					// First try with `DOMDocument` by default.
					if ( apply_filters( 'wpmudev_blc_link_action_use_dom', true, 'unlink', $link ) ) {
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

	public function get_block_att_value_replacement( string $search_term = null, string $new_term = null ) {
		// When unlinking we can set the block att that holds the search link to empty string,
		return '';
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

			if ( $this->links_match( $link, $old_link ) ) {	
				$search_markup   = $dom->saveHTML( $dom_link );
				$replacement_str = $dom_link->nodeValue;
				
				if ( ! empty( $replacements[ $search_markup ] ) ) {
					continue;
				}

				$special_actions = $this->content_special_actions( $content );

				if ( ! empty( $special_actions ) ) {
					$replacement_str = $search_markup;

					foreach ( $special_actions as $special_case_key => $callback ) {
						if ( is_callable( $callback ) ) {
							$replacement_str = call_user_func(
								$callback,
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
					$old_link = untrailingslashit( trim( $matches[2][ $key ], '\'"' ) );

					if ( $this->links_match( $link, $old_link ) ) {
						$replacements[ $markup ] = $matches[3][ $key ];
					}
				}
			}
		}

		return $replacements;
	}

	/**
	 * This is the callback function that is set in `$this->set_special_rules` method.
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

	protected function set_special_rules() {
		$special_strings = array(
			'reusable_button_block' => array(
				'description'        => 'In WP Button reusable block, removing the <a> tag will make button show as a simple string. Instead we can remove only the href att',
				'condition_callback' => array( $this, 'str_starts_with' ),
				'needle'             => '<!-- wp:buttons -->',
				'action'             => array( $this, 'rm_href_attribute' ),
			),
			/*'button_blocks' => array(
				'description'        => 'In WP Button block, removing the <a> tag will make button show as a simple string. Instead we can remove only the href att',
				'condition_callback' => array( $this, 'is_block' ),
				'needle'             => null,
				'action'             => array( $this, 'rm_href_attribute' ),
			),*/
		);

		$this->special_rules = $special_strings;
	}
}
