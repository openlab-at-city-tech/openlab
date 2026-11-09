<?php
/**
 * Elementor link processor for Broken Link Checker.
 * Contains the Elementor_Link_Processor class which processes links in Elementor content when editing/unlinking
 *
 * @package WPMUDEV_BLC\App\Integrations\Elementor
 */

namespace WPMUDEV_BLC\App\Integrations\Elementor;

use WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors\Abstract_Content_Processor;
/**
 * Class Elementor_Link_Processor
 *
 * @package WPMUDEV_BLC\App\Integrations\Elementor
 */
class Elementor_Link_Processor extends Abstract_Content_Processor {

	/**
	 * The target tags and attributes to process.
	 *
	 * @var array
	 */
	private $tags = array();

	/**
	 * The Elementor data to process.
	 *
	 * @var array
	 */
	private $data = array();

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
	 * @param array  $tags     An array of target tags and attributes to process.
	 * @return array|string|null
	 */
	public function process( string $content, string $link, string $new_link, array $tags ) {
		if ( ! $this->set_content( $content ) ) {
			return $content;
		}
		$this->set_tags( $tags );

		$data = $this->get_data();
		foreach ( $data as $idx => $row ) {
			$update_row = $this->update_links( $row, $link, $new_link );
			$this->update_row( $idx, $update_row );
		}

		return $this->get_content();
	}

	/**
	 * Sets the content to be processed.
	 *
	 * @param string $content The content to set, expected to be a JSON string.
	 */
	public function set_content( string $content ) {
		$this->data = json_decode( $content, true );

		if ( json_last_error() !== JSON_ERROR_NONE ) {
			// If the content is not a valid JSON string, we set data to an empty array to prevent processing errors.
			$this->data = array();

			return false;
		}

		return true;
	}

	/**
	 * Returns the processed content as a JSON string.
	 *
	 * @return string
	 */
	public function get_content() {
		return wp_json_encode( $this->data );
	}

	/**
	 * Returns the processed Elementor data.
	 *
	 * @return array
	 */
	public function get_data() {
		return $this->data;
	}

	/**
	 * Sets the processed Elementor data.
	 *
	 * @param array $data The Elementor data to set.
	 */
	public function set_data( array $data ) {
		$this->data = $data;
	}

	/**
	 * Updates a specific row of the Elementor data.
	 *
	 * @param int   $index The index of the row to update.
	 * @param array $row   The new data for the specified row.
	 */
	public function update_row( $index, $row ) {
		$this->data[ $index ] = $row;
	}

	/**
	 * Sets the target tags and attributes for processing.
	 *
	 * @param array $tags An array of target tags and attributes to process.
	 */
	public function set_tags( array $tags ) {
		$this->tags = $tags;
	}

	/**
	 * Recursively walks through Elementor data and updates links in link elements.
	 *
	 * @param array  $data The Elementor data to update.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array|string|null
	 */
	protected function update_links( $data, $link, $new_link ) {
		if ( empty( $data ) || ! is_array( $data ) ) {
			// If data is empty or not an array, there's nothing to update, so we return early.
			return $data;
		}

		if ( isset( $data['settings'] ) && is_array( $data['settings'] ) ) {
			// Update links in nested settings.
			$data['settings'] = $this->update_settings( $data['settings'], $link, $new_link );
		}

		if ( isset( $data['elements'] ) && is_array( $data['elements'] ) ) {
			foreach ( $data['elements'] as $key => $element ) {
				// Recursively update links in nested data.
				$data['elements'][ $key ] = $this->update_links(
					$element,
					$link,
					$new_link
				);
			}
		}
		return $data;
	}

	/**
	 * Updates links in Elementor settings, specifically in link elements and image widget elements.
	 *
	 * @param array  $data The Elementor setting data to update.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array|string|null
	 */
	protected function update_settings( $data, $link, $new_link ) {
		if ( isset( $this->tags['a'] ) ) {
			// If the target tags include 'a', we check for and update any links in link elements.
			$data = $this->update_links_in_link_elements( $data, $link, $new_link );
			$data = $this->update_links_in_paragraph( $data, $link, $new_link );
		}

		return $data;
	}

	/**
	 * Updates links in Elementor link elements and image widget link settings.
	 *
	 * @param array  $data The Elementor setting data to update.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array|string|null
	 */
	private function update_links_in_link_elements( $data, $link, $new_link ) {
		// Check if this is an image widget with a link.
		if ( isset( $data['link'] ) && isset( $data['link']['url'] ) ) {
			$data = $this->update_elementor_widget_link( $data, $link, $new_link );
		}

			// Check if this is a link element.
		if (
			isset( $data['link'] ) &&
			isset( $data['link']['value']['destination'] )
		) {
			$data = $this->update_elementor_link_url( $data, $link, $new_link );
		}

		return $data;
	}

	/**
	 * Updates the link URL in an Elementor image widget link.
	 *
	 * @param array  $data The image widget data.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array|string|null
	 */
	private function update_elementor_widget_link( $data, $link, $new_link ) {

		if ( strpos( $data['link']['url'], $link ) === false ) {
			// If the image widget link does not contain the old link URL, we don't need to update it, so we return early.
			return $data;
		}

		/*
		 * A stored Elementor setting, not markup: sanitize_url() normalises the value
		 * without HTML-encoding it. Elementor escapes it again when it renders.
		 */
		$data['link']['url'] = str_replace(
			$link,
			sanitize_url( $new_link ),
			$data['link']['url']
		);

		return $data;
	}

	/**
	 * Updates the link URL in an Elementor link element.
	 *
	 * @param array  $data The link element data.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array|string|null
	 */
	private function update_elementor_link_url( $data, $link, $new_link ) {

		$destination = $data['link']['value']['destination'];
		if ( ! is_array( $destination ) ) {
			// If the link element destination is not an array, we cannot process it, so we return early.
			return $data;
		}

		if ( 'url' !== $destination['$$type'] ) {
			// If the link element destination is not a URL, we don't need to update it, so we return early.
			return $data;
		}

		if ( strpos( $destination['value'], $link ) !== false ) {
			// A stored setting as above, so the same normalisation applies.
			$data['link']['value']['destination']['value'] = str_replace(
				$link,
				sanitize_url( $new_link ),
				$destination['value']
			);
		}

		return $data;
	}

	/**
	 * Updates links in Elementor paragraph elements and image widget link settings.
	 *
	 * @param array  $data The Elementor setting data to update.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL.
	 *
	 * @return array Elementor widget settings data.
	 */
	private function update_links_in_paragraph( $data, $link, $new_link ) {
		if ( isset( $data['paragraph'] ) ) {
			if (
				isset( $data['paragraph']['value']['content']['value'] )
			) {
				$data['paragraph']['value']['content']['value'] = $this->replace_content_links(
					$data['paragraph']['value']['content']['value'],
					$link,
					$new_link
				);
			}
		}

		return $data;
	}

	/**
	 * Replaces occurrences of the old link URL with the new link URL in a given content string.
	 *
	 * @param string $content The content string to process.
	 * @param string $link The old link URL to find.
	 * @param string $new_link The new link URL to replace with.
	 *
	 * @return string The content string with links replaced.
	 */
	private function replace_content_links( $content, $link, $new_link ) {
		if ( empty( $content ) ) {
			return '';
		}

		$content = wp_unslash( $content );

		$matches = array();

		// Find all links.
		$found = preg_match_all(
			'/<a\s[^>]*href="([^" >]*?)"[^>]*>(.*)<\/a>/',
			$content,
			$matches
		);

		if ( ! $found ) {
			// No links found in the content.
			return $content;
		}

		if ( empty( $new_link ) ) {
			// Loop through all found links and replace with a link text.
			foreach ( $matches[1] as  $idx => $match ) {

				// Check if the link matches the old link URL.
				if ( $match === $link ) {

					// Replace the entire anchor tag with just the link text (removing the link).
					$content = str_replace(
						$matches[0][ $idx ],
						$matches[2][ $idx ],
						$content
					);
				}
			}

			return $content;
		}

		/*
		 * This lands in markup, where the URL sits inside an href. esc_url() encodes the
		 * quote characters so the value cannot close the attribute holding it.
		 */
		$content = str_replace(
			$link,
			esc_url( $new_link ),
			$content
		);

		return $content;
	}
}
