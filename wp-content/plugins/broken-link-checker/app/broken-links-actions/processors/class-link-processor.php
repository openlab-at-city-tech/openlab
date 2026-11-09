<?php
/**
 * The parent Processor class.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.3
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\Core\Utils\Abstracts\Base;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
abstract class Link_Processor extends Base {
	protected $is_block = false;

	protected $is_recurring_block = false;

	protected $is_nav = false;

	protected $current_block_name = null;
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
	protected $special_rules = array();

	public $post_id = null;

	/**
	 * Used to set the properties of the class and set them up.
	 *
	 * @return void
	 */
	public function load( array $props = array() ) {
		$this->clear();

		if ( ! empty( $props ) ) {
			$default_props = array(
				'is_block'           => false,
				'is_recurring_block' => false,
				'is_nav'             => false,
			);

			$props = apply_filters(
				'wpmudev_blc_link_actions_processor_props',
				wp_parse_args( $props, $default_props ),
				$props
			);

			if ( ! empty( $props ) ) {
				foreach ( $props as $property_name => $property_value ) {
					if ( property_exists( $this, $property_name ) ) {
						$this->__set( $property_name, $property_value );
					}
				}
			}
		}
	}

	/**
	 * Clears all optional class properties.
	 *
	 * @return void
	 */
	public function clear() {
		$this->special_rules      = array();
		$this->is_block           = false;
		$this->is_recurring_block = false;
		$this->current_block_name = null;
		$this->is_nav             = false;
		$this->post_id            = null;
	}

	public function parse_block( array $block = array(), string $link = null, string $new_link = null ) {
		$this->is_block = true;

		if ( ! empty( $block['blockName'] ) ) {
			$this->current_block_name = $block['blockName'];
		}
		
		if ( ! empty( $block['attrs'] ) && is_array( $block['attrs'] ) ) {
			$block['attrs'] = $this->parse_block_atts( $block['attrs'], $link, $new_link );
		}

		if ( ! empty( $block['innerHTML'] ) ) {
			$block['innerHTML'] = ! empty( $block['innerHTML'] ) ? $this->execute( $block['innerHTML'], $link, $new_link ) : $block['innerHTML'];
		}

		if ( ! empty( $block['innerContent'] ) && is_array( $block['innerContent'] ) ) {
			foreach ( $block['innerContent'] as $inner_content_key => $inner_content ) {
				$block['innerContent'][ $inner_content_key ] = ! empty( $block['innerContent'][ $inner_content_key ] ) ?
					$this->execute( $block['innerContent'][ $inner_content_key ], $link, $new_link ) :
					$block['innerContent'][ $inner_content_key ];
			}
		}

		if ( ! empty( $block['innerBlocks'] ) && is_array( $block['innerBlocks'] ) ) {
			foreach ( $block['innerBlocks'] as $inner_block_key => $inner_block ) {
				$block['innerBlocks'][ $inner_block_key ] = $this->parse_block( $inner_block, $link, $new_link );
			}
		}

		$this->is_block = false;

		return $block;
	}

	public function parse_block_atts( array $block_atts = array(), string $link = null, string $new_link = null ) {
		if ( ! empty( $block_atts ) ) {
			foreach ( $block_atts as $key => $value ) {
				/*
				 * Only rewrite a setting that both looks like a URL and actually holds the broken
				 * link being actioned. Without the link comparison every URL-like block setting
				 * (embedded video, navigation link, etc.) would be replaced or emptied too,
				 * silently breaking unrelated content.
				 */
				if (
					! is_string( $value ) ||
					! filter_var( $value, FILTER_VALIDATE_URL ) ||
					! $this->block_att_holds_link( $value, $link )
				) {
					continue;
				}

				$block_atts[ $key ] = $this->get_block_att_value_replacement( $value, $new_link );
			}
		}

		return $block_atts;
	}

	/**
	 * Checks whether a URL-like block attribute value holds the broken link being actioned.
	 *
	 * The comparison mirrors the one used for links found in the visible content: surrounding
	 * quotes and a trailing slash are ignored, and URLs are normalised through `links_match()`.
	 *
	 * @param string      $att_value The URL-like block attribute value.
	 * @param string|null $link      The link being replaced/unlinked.
	 * @return bool
	 */
	public function block_att_holds_link( string $att_value = '', ?string $link = null ) : bool {
		if ( empty( $att_value ) || empty( $link ) ) {
			return false;
		}

		return $this->links_match(
			untrailingslashit( trim( $link, '\'"' ) ),
			untrailingslashit( trim( $att_value, '\'"' ) )
		);
	}

	/**
	 * Checks if content requires special handling based on cases registered in `$this->special_rules`.
	 *
	 * @param string $content
	 * @return array
	 */
	public function content_special_actions( string $content = '' ) {
		$special_rules = $this->get_special_rules();

		if ( empty( $special_rules ) ) {
			return array();
		}

		// An array for storing the special actions for this content, if there are any.
		$special_actions = array();

		foreach ( $special_rules as $special_case_key => $special_case_data ) {
			if ( empty( $special_case_data['condition_callback'] ) || empty( $special_case_data['action'] ) ) {
				continue;
			}

			// First we need to make sure that the callback is valid and callable.
			if (
				is_callable( $special_case_data['condition_callback'] ) &&
				is_callable( $special_case_data['action'] )
			) {
				// Now we need to check if the content will fit the callback's condition(s).
				if ( call_user_func(
					$special_case_data['condition_callback'],
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
	 * Summary of links_match
	 * @param string $requested_link The link that we are looking for (to replace/delete).
	 * @param string $comparison_link The link found in post or block content.
	 * @return bool
	 */
	public function links_match( string $requested_link = '', string $comparison_link = '' ) : bool {
		return strcasecmp( $this->prepare_url( $comparison_link ), $this->prepare_url( $requested_link ) ) == 0;
	}

	/**
	 * Prepares/normalizes a url for string comparison. It turns relative urls into absolute urls and removes scheme (http|https).
	 * @param string $url
	 * @return string
	 */
	public function prepare_url( string $url = '' ) : string {
		$site_url = site_url();
		$scheme   = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':';

		if ( empty( wp_parse_url( $url, PHP_URL_HOST ) ) ) {
			// If the url is relative, we need to make it absolute. 
			// Makes sure that the relative url is subpath of the current post too by checking if url pah starts with `/` or not.
			// If it doesn't start with `/` then it is a relative url to the current post.
			$url_start = $this->str_starts_with( $url, '/' ) ? $site_url : get_permalink( $this->post_id );
			$url       = rtrim( $url_start, '/' ) . '/' . ltrim( $url, '/' );
		}

		return str_replace( $scheme, '', $url );
	}

	/**
	 * Normalizes urls similar to how engine does.
	 * Note: This does not normalize URI's based on the specification RFC 3986 https://tools.ietf.org/html/rfc3986. It sets uri to lowercase and removes trailing slashes, query vars and anchors.
	 *
	 * @param string $url
	 * @return string
	 */
	public function normalize_url( string $url = '' ) {
		// Instead of parsing url and re-building it we can simply split it (tokenize) using strtok.
		return strtok(
			strtok(
				untrailingslashit( strtolower( $url ) ),
				'#' 
			),
			'?'
		);
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

	public function is_block( string $content = null, $needle = null ) {
		if ( empty( $needle ) ) {
			return $this->is_block;
		}

		if ( ! $this->is_block || empty( $this->current_block_name ) ) {
			return false;
		}

		return is_array( $needle ) ? in_array( $this->current_block_name, $needle ) : $this->current_block_name === $needle;
	}

	/**
	 * * Provides a list of tags and attributes in which BLC will search for broken links.
	 *
	 * @return array
	 */
	public function get_target_tags() {

		return apply_filters(
			'wpmudev_blc_replace_target_tags',
			array(
				'a' => array( 'href' ),
				//'img'    => array( 'src', 'srcset ),
				//'iframe' => 'src'
			)
		);
	}

	/**
	 * Gives the special rules that the process has set.
	 * At any runtime we have only one of the link actions running. We can't have eg unlink and edit running on same instance, 
	 * so it is safe to be setting special rules without specifying action keys (eg each process would return an array with it's key and value will be the array of rules) 
	 * and we can keep it simple to set and get those rules.
	 *
	 * @return array
	 */
	protected function get_special_rules() {
		if ( empty( $this->special_rules ) || ! is_array( $this->special_rules ) ) {
			$this->set_special_rules();
		}

		return $this->special_rules;
	}

	abstract protected function set_special_rules();

	/**
	 * Executes the Processor's action.
	 *
	 * @param string $content The content to process.
	 * @param string $link The link to be replaced/removed.
	 * @param string $new_link The new link to replace the old link with (Replace action only).
	 * @return string The processed content.
	 */
	public function execute( string $content = '', string $link = '', string $new_link = '' ) {
		$content = $this->process_content( $content, $link, $new_link );

		return apply_filters(
			'wpmudev_blc_link_processor_content',
			$content,
			$link,
			$new_link,
			$this->get_target_tags(),
			$this
		);
	}

	/**
	 * Modifies the content based on the action type (Replace/Unlink) and the target tags and attributes.
	 *
	 * @param string $content The content to process.
	 * @param string $link The link to be replaced/removed.
	 * @param string $new_link The new link to replace the old link with (Replace action only).
	 * @return array|string Array of replacements (Unlink) or modified content string (Replace).
	 */
	abstract protected function process_content( string $content, string $link, string $new_link );

	abstract public function get_block_att_value_replacement( string $search_term = null, string $new_term = null );

	/**
	 * Returns the primary processor for this action.
	 *
	 * @return Abstract_Content_Processor
	 */
	abstract protected function get_processor(): Abstract_Content_Processor;

	/**
	 * Returns the fallback processor for this action.
	 *
	 * @return Abstract_Content_Processor
	 */
	abstract protected function get_fallback_processor(): Abstract_Content_Processor;

	/**
	 * Delegates content processing to the appropriate processor strategy.
	 * Uses the primary processor by default, but routes through the fallback processor when
	 * SiteOrigin Page Builder, Divi, or Elementor is active, as those builders
	 * may store content in formats that the primary processor does not handle reliably.
	 *
	 * @param string $content Source content to process.
	 * @param string $link The link to be replaced/removed.
	 * @param string $new_link The new link to replace the old link with (Replace action only).
	 * @param array  $tags An array of target tags and attributes to process.
	 * @return array|string Array of replacements (Unlink) or modified content string (Replace).
	 */
	protected function extract_replacements( $content, $link, $new_link, array $tags ) {
		if ( $this->is_pagebuilder_active() ) {
			$new_content = $this->get_fallback_processor()->process( $content, $link, $new_link, $tags );
		} else {
			$new_content = $this->get_processor()->process( $content, $link, $new_link, $tags );

			if ( $new_content === $content ) {
				// If the primary processor returns unchanged result, we should try the fallback processor before giving up.
				$new_content = $this->get_fallback_processor()->process( $content, $link, $new_link, $tags );
			}
		}

		return $new_content;
	}

	/**
	 * Checks whether a known page builder plugin (SiteOrigin Page Builder, Divi, or Elementor)
	 * is currently active.
	 *
	 * @return bool
	 */
	protected function is_pagebuilder_active() {
		// SiteOrigin Page Builder.
		if ( defined( 'SITEORIGIN_PANELS_VERSION' ) ) {
			return true;
		}

		// Divi (plugin or theme).
		if ( defined( 'ET_BUILDER_PLUGIN_ACTIVE' ) || function_exists( 'et_setup_theme' ) ) {
			return true;
		}

		// Elementor.
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			return true;
		}

		return apply_filters( 'wpmudev_blc_is_pagebuilder_active', false );
	}
}
