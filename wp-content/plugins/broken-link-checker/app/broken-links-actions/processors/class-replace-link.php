<?php
/**
 * Executes the `Replace` action on Broken Links.
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
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace\Replace_Regex_Processor;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Replace\Replace_Wp_Native_Processor;

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Replace_Link extends Link_Processor {
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

		$link = trim( $link, '\'"' );

		return $this->extract_replacements( $content, $link, $new_link, $this->get_target_tags() );
	}

	/**
	 * Returns the replacement value for a block attribute.
	 *
	 * @param string|null $search_term The term to search for.
	 * @param string|null $new_term The term to replace with.
	 * @return string|null The replacement value.
	 */
	public function get_block_att_value_replacement( ?string $search_term = null, ?string $new_term = null ) {
		return $new_term;
	}

	/**
	 * Returns the fallback processor for the Replace action.
	 *
	 * @return Abstract_Content_Processor
	 */
	protected function get_fallback_processor(): Abstract_Content_Processor {
		return new Replace_Regex_Processor( $this );
	}

	/**
	 * Returns the WP native (WP_HTML_Tag_Processor) processor for the Replace action.
	 * process() returns null when WP_HTML_Tag_Processor is unavailable (WP < 6.2).
	 *
	 * @return Replace_Wp_Native_Processor
	 */
	protected function get_processor(): Replace_Wp_Native_Processor {
		return new Replace_Wp_Native_Processor( $this );
	}

	protected function set_special_rules() {

	}

}
