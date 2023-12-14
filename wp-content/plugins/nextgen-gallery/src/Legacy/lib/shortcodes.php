<?php
/**
 * @author Alex Rabe, Vincent Prat
 *
 * @since 1.0.0
 * @description Use WordPress Shortcode API for more features
 * @Docs http://codex.wordpress.org/Shortcode_API
 * @todo This file should be merged into another file
 */

class NextGEN_shortcodes {

	// register the new shortcodes
	function __construct() {
		// Long posts should require a higher limit, see http://core.trac.wordpress.org/ticket/8553
		$pcre_limit = 500000;
		if ((int) ini_get( 'pcre.backtrack_limit' ) < $pcre_limit) {
			@ini_set( 'pcre.backtrack_limit', $pcre_limit );
		}

		// convert the old shortcode
		add_filter( 'the_content', array( 'NextGEN_shortcodes', 'convert_shortcode' ) );

		// ngglegacy display types use globals. These globals need to be reset at the start of every loop
		add_filter( 'loop_start', array( &$this, 'reset_globals' ) );
	}

	function reset_globals() {
		unset( $GLOBALS['subalbum'] );
		unset( $GLOBALS['nggShowGallery'] );
	}

	/**
	 * NextGEN_shortcodes::convert_shortcode()
	 * convert old shortcodes to the new WordPress core style
	 * [gallery=1]  ->> [nggallery id=1]
	 *
	 * @param string $content Content to search for shortcodes
	 * @return string Content with new shortcodes.
	 */
	static function convert_shortcode( $content ) {

		$ngg_options = nggGallery::get_option( 'ngg_options' );

		if ( stristr( $content, '[singlepic' )) {
			$search = "@\[singlepic=(\d+)(|,\d+|,)(|,\d+|,)(|,watermark|,web20|,)(|,right|,center|,left|,)\]@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					// remove the comma
					$match[2] = ltrim( $match[2], ',' );
					$match[3] = ltrim( $match[3], ',' );
					$match[4] = ltrim( $match[4], ',' );
					$match[5] = ltrim( $match[5], ',' );
					$replace  = "[singlepic id=\"{$match[1]}\" w=\"{$match[2]}\" h=\"{$match[3]}\" mode=\"{$match[4]}\" float=\"{$match[5]}\" ]";
					$content  = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[album' )) {
			$search = "@(?:<p>)*\s*\[album\s*=\s*(\w+|^\+)(|,extend|,compact)\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					// remove the comma
					$match[2] = ltrim( $match[2], ',' );
					$replace  = "[album id=\"{$match[1]}\" template=\"{$match[2]}\"]";
					$content  = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[gallery' )) {
			$search = "@(?:<p>)*\s*\[gallery\s*=\s*(\w+|^\+)\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					$replace = "[nggallery id=\"{$match[1]}\"]";
					$content = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[imagebrowser' )) {
			$search = "@(?:<p>)*\s*\[imagebrowser\s*=\s*(\w+|^\+)\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					$replace = "[imagebrowser id=\"{$match[1]}\"]";
					$content = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[slideshow' )) {
			$search = "@(?:<p>)*\s*\[slideshow\s*=\s*(\w+|^\+)(|,(\d+)|,)(|,(\d+))\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					// remove the comma
					$match[3] = ltrim( $match[3], ',' );
					$match[5] = ltrim( $match[5], ',' );
					$replace  = "[slideshow id=\"{$match[1]}\" w=\"{$match[3]}\" h=\"{$match[5]}\"]";
					$content  = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[tags' )) {
			$search = "@(?:<p>)*\s*\[tags\s*=\s*(.*?)\s*\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					$replace = "[nggtags gallery=\"{$match[1]}\"]";
					$content = str_replace( $match[0], $replace, $content );
				}
			}
		}

		if ( stristr( $content, '[albumtags' )) {
			$search = "@(?:<p>)*\s*\[albumtags\s*=\s*(.*?)\s*\]\s*(?:</p>)*@i";
			if (preg_match_all( $search, $content, $matches, PREG_SET_ORDER )) {

				foreach ($matches as $match) {
					$replace = "[nggtags album=\"{$match[1]}\"]";
					$content = str_replace( $match[0], $replace, $content );
				}
			}
		}

		return $content;
	}
}

// let's use it
$nggShortcodes = new NextGEN_shortcodes;
