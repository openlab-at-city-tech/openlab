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

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink\Unlink_Dom_Processor;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Unlink\Unlink_Regex_Processor;

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
	 * Modifies the content based on the action type (Replace/Unlink) and the target tags and attributes.
	 *
	 * @param string $content The content to process.
	 * @param string $link The link to be replaced/removed.
	 * @param string $new_link The new link to replace the old link with (Replace action only).
	 * @return array|string Array of replacements (Unlink) or modified content string (Replace).
	 */
	protected function process_content( string $content, string $link, string $new_link ) {
		if ( empty( $this->get_target_tags() ) || empty( str_replace( PHP_EOL, '', $content ) ) ) {
			return $content;
		}

		$link = untrailingslashit( trim( $link, '\'"' ) );

		$replacements = $this->extract_replacements( $content, $link, $new_link, $this->get_target_tags() );

		$replacements = apply_filters( 'wpmudev_blc_unlink_replacements', $replacements, $content, $link, $new_link );

		if ( empty( $replacements ) ) {
			return $content;
		}

		$content = str_replace( '/>', '>', $content );

		return str_replace( array_keys( $replacements ), array_values( $replacements ), $content );
	}

	public function get_block_att_value_replacement( string $search_term = null, string $new_term = null ) {
		// When unlinking we can set the block att that holds the search link to empty string,
		return '';
	}

	/**
	 * Returns the primary processor for the Unlink action.
	 *
	 * @return Abstract_Content_Processor
	 */
	protected function get_processor(): Abstract_Content_Processor {
		return new Unlink_Dom_Processor( $this );
	}

	/**
	 * Returns the fallback processor for the Unlink action.
	 *
	 * @return Abstract_Content_Processor
	 */
	protected function get_fallback_processor(): Abstract_Content_Processor {
		return new Unlink_Regex_Processor( $this );
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
