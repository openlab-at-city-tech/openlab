<?php
/**
 * Common ancestor for all content-level processors.
 *
 * @link    https://wordpress.org/plugins/broken-link-checker/
 * @since   2.2.4
 *
 * @author  WPMUDEV (https://wpmudev.com)
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors
 *
 * @copyright (c) 2023, Incsub (http://incsub.com)
 */

namespace WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors;

// Abort if called directly.
defined( 'WPINC' ) || die;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Link_Processor;

/**
 * Class Abstract_Content_Processor
 *
 * All concrete content processors must extend this class.
 * Unlink processors return an array of (search => replacement) pairs.
 * Replace processors return the modified content string.
 * The Replace_Wp_Native_Processor returns null when the WP native class is unavailable.
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors
 */
abstract class Abstract_Content_Processor {

	/**
	 * The parent Link_Processor instance (e.g. Unlink_Link or Replace_Link).
	 * Used to access shared utilities such as links_match() and content_special_actions().
	 *
	 * @var Link_Processor
	 */
	protected $link_processor;

	/**
	 * Constructor.
	 *
	 * @param Link_Processor $link_processor The parent Link_Processor instance.
	 */
	public function __construct( Link_Processor $link_processor ) {
		$this->link_processor = $link_processor;
	}

	/**
	 * Execute the content processing logic for the given tag/attribute pair.
	 *
	 * Unlink implementations return an array: [ $search_markup => $replacement_str, ... ]
	 * Replace implementations return the modified content string.
	 * Replace_Wp_Native_Processor returns null when the WP native processor is unavailable.
	 *
	 * @param string $content  Source content to process.
	 * @param string $link     The broken link URL to find.
	 * @param string $new_link The replacement URL (empty for unlink).
	 * @param array  $tags An array of target tags and attributes to process, in the format.
	 *                     [
	 *                       'tag_name' => ['attr1', 'attr2', ...],
	 *                       ...
	 *                     ]
	 * @return array|string|null
	 */
	abstract public function process( string $content, string $link, string $new_link, array $tags );
}
