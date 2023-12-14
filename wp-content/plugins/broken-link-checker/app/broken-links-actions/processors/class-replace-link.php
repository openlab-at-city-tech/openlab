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

/**
 * Class Scan_Data
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors
 */
class Replace_Link extends Link_Processor {
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

		$link              = trim( $link, '\'"' );
		$processed_content = $content;

		foreach ( $this->get_target_tags() as $tag_name => $tag_atts ) {
			if ( ! empty( $tag_atts ) ) {
				foreach ( $tag_atts as $tag_att ) {
					// First try with `WP_HTML_Tag_Processor` for WP versions > v6.2.
					$processed_content = $this->use_wp_native_processor( $content, $link, $new_link, $tag_name, $tag_att );
					if ( ! $processed_content ) {
						if ( apply_filters( 'wpmudev_blc_link_action_use_dom', true, 'replace', $link ) ) {
							// Dom is second preferred method for wp version < v6.2.
							$processed_content = $this->use_domdocument_processor( $content, $link, $new_link, $tag_name, $tag_att );
						} else {
							// Keeping regex as an optional alternative.
							$processed_content = $this->use_regex_processor( $content, $link, $new_link, $tag_name, $tag_att );
						}
					}
				}
			}

			$content = $processed_content;
		}

		return $content;
	}

	public function get_block_att_value_replacement( string $search_term = null, string $new_term = null ) {
		return $new_term;
	}

	/**
	 * Processes content using wp native class `\WP_HTML_Tag_Processor`.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return string|false
	 */
	public function use_wp_native_processor( string $content = '', string $link = '', string $new_link = '', string $tag_name = '', string $tag_att = '' ) {
		if ( class_exists( '\WP_HTML_Tag_Processor' ) ) {
			$processor = new \WP_HTML_Tag_Processor( $content );

			while ( $processor->next_tag( array( 'tag_name' => $tag_name ) ) ) {
				$old_link = untrailingslashit( trim( $processor->get_attribute( $tag_att ), '\'"' ) );

				if ( $this->links_match( $link , $old_link ) ) {
					$processor->set_attribute( $tag_att, $new_link );

					if ( apply_filters( 'wpmudev_blc_link_action_edit_wrap', false, $link, $new_link ) ) {
						$existing_class = $processor->get_attribute( "class" );
						$processor->set_attribute( 'class',
							apply_filters(
								'wpmudev_blc_link_action_unlink_wrap_class',
								empty( $existing_class ) ? 'blc_edited' : "{$existing_class} blc_edited",
								$link,
								$new_link
							)
						);
						$processor->set_attribute( "data-blc-orig-url", $link );
					}

					$content = $processor->get_updated_html();
				}
			}

			return $content;
		}

		return false;
	}

	/**
	 * Processes content using `DOMDocument`.
	 * We keep this as default/recommended option for wp version < 6.2.
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return string
	 */
	public function use_domdocument_processor( string $content = '', string $link = '', string $new_link = '', string $tag_name = '', string $tag_att = '' ) {
		$dom = new \DOMDocument();

		libxml_use_internal_errors( true );

		$dom->loadHTML( $content );

		foreach ( $dom->getElementsByTagName( 'a' ) as $dom_link ) {
			$old_link = $dom_link->getAttribute( "href" );

			// 1. Check if found link matches the searched link.
			if ( $this->links_match( $link, $old_link ) ) {
				// 2. Prepare the old link markup to be searched and replaced.
				//2.1 Let's prepare the markup that needs to be replaced.
				$old_link_markup_search = $dom->saveHTML( $dom_link );

				// 2.2 Optionally, add a class and an attribute `data-blc-orig-url` in link so that it can be specified which url was the original.
				// To do that easily, we can use the `old` link's dom element (instead of creating a new one and place it again into DOM)
				if ( apply_filters( 'wpmudev_blc_link_action_edit_wrap', true, $link, $new_link ) ) {
					$existing_class = $dom_link->getAttribute( "class" );
					$dom_link->setAttribute( 'class',
						apply_filters(
							'wpmudev_blc_link_action_unlink_wrap_class',
							empty( $existing_class ) ? 'blc_edited' : "{$existing_class} blc_edited",
							$link,
							$new_link
						)
					);
					$dom_link->setAttribute( "data-blc-orig-url", '[edited-url-macro]' );
				}

				// 2.3 Prepare the new link markup.
				// We are using the $dom_link's html again, in case it has been altered with class and att.
				// We can use this new html as base to replace the url and generate the new markup.
				// We can not use this to search in `$content` though. The `$content` search needs to be done with `$old_link_markup_search`.
				$old_link_markup = $dom->saveHTML( $dom_link );
				$new_link_markup = str_replace(
					[ $link, '[edited-url-macro]' ],
					[ $new_link, $link ],
					$old_link_markup );

				// 3. Replace the old link's markup with the new markup.
				$content = str_replace( $old_link_markup_search, $new_link_markup, $content );
			}
		}

		return $content;
	}

	/**
	 * Processes content using regex. Not recommended, it's better to use DOMDocument.
	 * Optional for wp versions older than v6.2. To use this the filter `wpmudev_blc_link_action_use_dom` needs to be set to false (so that DOM is skipped in favor of regex)
	 *
	 * @param string $content
	 * @param string $link
	 * @param string $new_link
	 * @param string $tag_name
	 * @param string $tag_att
	 * @return string
	 */
	public function use_regex_processor( string $content = '', string $link = '', string $new_link = '', string $tag_name = '', string $tag_att = '' ) {
		//  /<a\s[^>]*href=["'](.*?)["']>(.*)/gm      : 231 steps, 0,1 ms
		//  /<a\s[^>]*href=["'](.*?)["']>(.*)<\/a>/gm : 255 steps, 0,1 ms
		// $regexp = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/{$tag_name}>";
		$regexp  = "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)[^>]*>(.*)<\/{$tag_name}>";
		$content = preg_replace_callback(
			"/$regexp/siU",
			function ($match) use ($link, $new_link, $tag_att) {
				$old_link = untrailingslashit( trim( $match[2], '\'"' ) );

				if ( $this->links_match( $link, $old_link ) ) {
					// Let's not use str_replace, so we avoid replacing url in tag content or other tag atts.
					// We have stripped trailing slashes, but we use `/?` in regex as link might have or might not have trailing slash.
					return preg_replace( "#({$tag_att}=[\"|\'])" . $old_link . '/?(["|\'])#i', '\1' . $new_link . '\2', $match[0], 1 );
				} else {
					return $match[0];
				}
			},
			$content
		);

		return $content;
	}

	protected function set_special_rules() {

	}

}
