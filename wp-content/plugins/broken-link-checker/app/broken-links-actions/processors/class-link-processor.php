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
				if ( filter_var( $value, FILTER_VALIDATE_URL ) ) {
					$block_atts[ $key ] = $this->get_block_att_value_replacement( $value, $new_link );
				}
			}
		}

		return $block_atts;
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

	public function links_match( string $requested_link = '', string $comparison_link = '' ) {
		$requested_link  = $this->normalize_url( $requested_link );
		$comparison_link = $this->normalize_url( $comparison_link );
		$links_match     = false;

		/**
		 * If link doesn't have Host part, it is internal link. All internal links are stored as relative urls in Queue.
		 * If link does have the Host part, then it's an external link.
		 * For internal links we need to test 3 cases:
		 * 1. Relative urls (that is how it is stored in Queue)
		 * 2. Absolute url.
		 * 3. Absolute url without scheme ( //site.com )
		 * Engine treats all those types as full urls
		 */
		if ( empty( wp_parse_url( $requested_link, PHP_URL_HOST ) ) ) {
			// Check Relative, Absolute and Absolute without host urls for $old_link.
			$site_url           = site_url();
			$scheme             = wp_parse_url( $site_url, PHP_URL_SCHEME ) . ':';
			$absolute_link      = $site_url . $requested_link;
			$semi_absolute_link = str_replace( $scheme, '', $absolute_link );
			$links_match        = strcasecmp( $requested_link, $comparison_link ) == 0 ||
				strcasecmp( $absolute_link, $comparison_link ) == 0 ||
				strcasecmp( $semi_absolute_link, $comparison_link ) == 0;
		} else {
			// Check $old_link normally.
			$links_match = strcasecmp( $comparison_link, $requested_link ) == 0;
		}

		return $links_match;
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

		/*
		$parsed = array_intersect_key(
			wp_parse_url( $url ),
			array_flip( ['scheme', 'host', 'path'] )
		);*/
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

	abstract public function execute( string $content = null, string $link = null, string $new_link = null );

	abstract public function get_block_att_value_replacement( string $search_term = null, string $new_term = null );
}