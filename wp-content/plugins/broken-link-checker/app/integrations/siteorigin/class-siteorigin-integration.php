<?php
/**
 * File that includes the SiteOrigin Builder integration class.
 *
 * @package WPMUDEV_BLC\App\Integrations
 */

namespace WPMUDEV_BLC\App\Integrations\SiteOrigin;

if ( ! defined( 'WPINC' ) ) {
	die;
}

use WPMUDEV_BLC\App\Broken_Links_Actions\Link;
use WPMUDEV_BLC\App\Integrations\SiteOrigin\SiteOrigin_Link_Processor;
use WPMUDEV_BLC\Core\Utils\Abstracts\Base;

/**
 * Class SiteOrigin_Controller
 *
 * @package WPMUDEV_BLC\App\Integrations\SiteOrigin
 */
class SiteOrigin_Integration extends Base {
	/**
	 * Flag to ensure hooks are only registered once per instance.
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * The SiteOrigin Link Processor instance.
	 *
	 * @var SiteOrigin_Link_Processor
	 */
	private $processor;

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
		$this->processor   = new SiteOrigin_Link_Processor();

		// PageBuilder meta keys are added to this filter in order to be included in the meta keys that are updated when editing/unlinking a link.
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
			4
		);

		add_filter(
			'wpmudev_blc_link_action_post_meta_content',
			array( $this, 'update_meta_links' ),
			10,
			4
		);
	}

	/**
	 * Add meta keys for SiteOrigin Builder content.
	 *
	 * @param array $meta_keys The existing meta keys.
	 * @return array The updated meta keys.
	 */
	public function add_meta_keys( $meta_keys ) {
		$meta_keys[] = 'panels_data';

		return $meta_keys;
	}

	/**
	 * Update content links for SiteOrigin Builder content.
	 *
	 * @param string $content The original content.
	 * @param string $link The old link URL.
	 * @param string $new_link The new link URL (empty for unlink).
	 * @param array  $tags The HTML tags and attributes to search within.
	 * @return string The processed content.
	 */
	public function update_content_links( $content, $link, $new_link, $tags ) {
		return $this->processor->process(
			$content,
			$link,
			$new_link,
			$tags
		);
	}

	/**
	 * Update meta links for SiteOrigin Builder content.
	 *
	 * @param string|array $content The original meta value content.
	 * @param int          $post_id The ID of the post being processed.
	 * @param string       $meta_key The meta key of the content being processed.
	 * @param Link         $link The Link object representing the link being edited/unlinked.
	 * @return string|array The processed content.
	 */
	public function update_meta_links( $content, $post_id, $meta_key, $link ) {
		if ( empty( $content ) || is_null( $link ) ) {
			return $content;
		}

		if ( 'panels_data' !== $meta_key ) {
			// Not SiteOrigin Builder content, return as is.
			return $content;
		}

		if ( is_string( $content ) ) {
			if ( ! is_serialized( $content ) ) {
				// Not serialized data, so it cannot be a SiteOrigin layout. Leave it untouched.
				return $content;
			}

			// Never build objects from editor-controlled metadata. A SiteOrigin layout only
			// ever holds arrays, strings and scalar values, so object creation is switched
			// off entirely. Any object left in the decoded value is handled below.
			// phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged,WordPress.PHP.DiscouragedPHPFunctions.serialize_unserialize -- Decoding here is deliberate and bounded: a malformed value must not break the link action, and `allowed_classes` is false so no object is ever created.
			$panels = @unserialize( trim( $content ), array( 'allowed_classes' => false ) );
		} else {
			$panels = $content;
		}

		// Reject anything that is not a plain array tree. A value that still holds an
		// object (for example an incomplete class created above) was not written by
		// SiteOrigin and must not be used.
		if ( ! is_array( $panels ) || $this->contains_object( $panels ) ) {
			return $content;
		}

		if (
			isset( $panels['widgets'] ) &&
			is_array( $panels['widgets'] )
		) {
			$changed = false;

			// Recursively search through the panels data for widget instances and update links as needed.
			foreach ( $panels['widgets'] as &$widget ) {
				if ( isset( $widget['panels_info']['class'] ) ) {
					if ( SiteOrigin_Link_Processor::BUTTON_CLASS === $widget['panels_info']['class'] ) {
						// This is a Button widget, so we need to process its content for link updates.
						if ( ! empty( $widget['url'] ) ) {
							$url = str_replace(
								$link->get_link(),
								$link->get_new_link(),
								$widget['url']
							);

							if ( $url !== $widget['url'] ) {
								$widget['url'] = $url;
								$changed       = true;
							}
						}
					}

					if ( SiteOrigin_Link_Processor::IMAGE_CLASS === $widget['panels_info']['class'] ) {
						// This is a Media Image widget, so we need to process its content for link updates.
						if ( ! empty( $widget['link_url'] ) ) {
							$link_url = str_replace(
								$link->get_link(),
								$link->get_new_link(),
								$widget['link_url']
							);

							if ( $link_url !== $widget['link_url'] ) {
								$widget['link_url'] = $link_url;
								$changed            = true;
							}
						}
					}
				}
			}
			unset( $widget );

			if ( ! $changed ) {
				// Nothing matched, so return the original value untouched.
				return $content;
			}

			// Hand back the decoded array rather than a serialized string. The extensive
			// meta path passes the raw stored value and writes this return value straight
			// back with update_post_meta(), which serializes arrays itself. Returning a
			// serialized string here is what stored panels_data double-serialized.
			return $panels;
		}

		return $content;
	}

	/**
	 * Recursively checks whether a decoded value contains any PHP object.
	 *
	 * SiteOrigin stores layouts as nested arrays of scalars, so an object anywhere
	 * in the tree means the value did not come from the builder. Incomplete classes,
	 * created when unserialize() runs with `allowed_classes => false`, are objects
	 * too and are rejected here.
	 *
	 * @param mixed $value The decoded value to inspect.
	 * @return bool True when an object is found at any depth.
	 */
	private function contains_object( $value ) {
		if ( is_object( $value ) ) {
			return true;
		}

		if ( ! is_array( $value ) ) {
			return false;
		}

		foreach ( $value as $item ) {
			if ( $this->contains_object( $item ) ) {
				return true;
			}
		}

		return false;
	}
}
