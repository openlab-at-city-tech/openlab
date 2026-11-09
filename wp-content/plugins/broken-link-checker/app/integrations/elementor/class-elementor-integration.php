<?php
/**
 * Elementor integration for Broken Link Checker.
 * Includes filters and functions to update links in Elementor content when editing/unlinking a link.
 *
 * @package WPMUDEV_BLC\App\Integrations\Elementor
 */

namespace WPMUDEV_BLC\App\Integrations\Elementor;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use WPMUDEV_BLC\App\Integrations\Elementor\Elementor_Link_Processor;
use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Link_Processor;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class Elementor_Integration
 *
 * @package WPMUDEV_BLC\App\Integrations\Elementor
 */
class Elementor_Integration extends Base {

	/**
	 * Flag to ensure hooks are only registered once per instance.
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Registers WordPress hooks. Runs only once per instance.
	 *
	 * @return void
	 */
	public function init() {
		if ( $this->initialized ) {
			return;
		}

		$this->initialized = true;

		// Elementor meta keys are added to this filter in order to be included in the meta keys that are updated when editing/unlinking a link.
		add_filter(
			'wpmudev_blc_edit_link_post_meta_keys',
			array( $this, 'add_meta_keys' ),
			10,
			1
		);

		add_filter(
			'wpmudev_blc_link_processor_content',
			array( $this, 'update_content_links' ),
			10,
			5
		);
	}

	/**
	 * Add Elementor meta keys to the list of meta keys that are processed when editing/unlinking a link.
	 *
	 * @param array $meta_keys The existing array of meta keys.
	 * @return array The modified array of meta keys with Elementor keys added.
	 */
	public function add_meta_keys( $meta_keys ) {
		$meta_keys[] = '_elementor_data';
		return $meta_keys;
	}

	/**
	 * Update links in Elementor content when editing/unlinking a link.
	 *
	 * @param string         $content The original content.
	 * @param string         $link The old link URL.
	 * @param string         $new_link The new link URL (empty for unlink).
	 * @param array          $tags The HTML tags and attributes to search within.
	 * @param Link_Processor $link_processor The parent Link_Processor instance.
	 * @return string The processed content.
	 */
	public function update_content_links(
		$content,
		$link,
		$new_link,
		$tags,
		$link_processor
	) {
		$processor   = new Elementor_Link_Processor( $link_processor );
		$new_content = $processor->process( $content, $link, $new_link, $tags );

		if ( $new_content !== $content ) {
			// If the content was modified, we need to save the updated Elementor data back to the post meta.
			$post_id = $link_processor->post_id;
			if ( $post_id ) {
				// Wipe the Elementor cache for this post to ensure the updated data is used when editing the post in Elementor.
				delete_post_meta( $post_id, '_elementor_element_cache' );
			}

			$content = wp_slash( $new_content );
		}

		return $content;
	}
}
