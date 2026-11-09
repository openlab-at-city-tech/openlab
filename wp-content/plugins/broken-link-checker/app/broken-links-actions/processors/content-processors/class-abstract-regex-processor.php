<?php
/**
 * Abstract regex-based content processor.
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

/**
 * Class Abstract_Regex_Processor
 *
 * Provides the shared regex pattern-building utility.
 * Concrete subclasses implement `process()` using their preferred matching
 * strategy (preg_match_all for Unlink, preg_replace_callback for Replace).
 *
 * @package WPMUDEV_BLC\App\Broken_Links_Actions\Processors\Content_Processors
 */
abstract class Abstract_Regex_Processor extends Abstract_Content_Processor {

	/**
	 * Builds the regex pattern used to locate HTML tags with the given attribute.
	 *
	 * Subclasses may override this method to use a stricter pattern (e.g. with
	 * a backreference to enforce matching quote characters).
	 *
	 * @param string $tag_name The name of the HTML tag to process.
	 * @param string $tag_att The attribute of the HTML tag to process.
	 * @return string The regex pattern.
	 */
	protected function get_pattern( string $tag_name, string $tag_att ): string {
		return "<{$tag_name}\s[^>]*{$tag_att}=(\"??)([^\" >]*?)[^>]*>(.*)<\/{$tag_name}>";
	}
}
