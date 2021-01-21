<?php
/**
 * Plugin Name: Text Hover
 * Version:     4.0
 * Plugin URI:  https://coffee2code.com/wp-plugins/text-hover/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: text-hover
 * Description: Add hover text to regular text in posts. Handy for providing explanations of names, terms, phrases, abbreviations, and acronyms mentioned in your blog.
 *
 * Compatible with WordPress 4.9+ through 5.4+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/text-hover/
 *
 * @package Text_Hover
 * @author  Scott Reilly
 * @version 4.0
 */

/*
	Copyright (c) 2007-2020 by Scott Reilly (aka coffee2code)

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) or die();

if ( ! class_exists( 'c2c_TextHover' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_TextHover extends c2c_TextHover_Plugin_050 {

	/**
	 * Name of plugin's setting.
	 *
	 * @since 3.8
	 * @var string
	 */
	const SETTING_NAME = 'c2c_text_hover';

	/**
	 * The one true instance.
	 *
	 * @var c2c_TextHover
	 */
	private static $instance;

	/**
	 * Get singleton instance.
	 *
	 * @since 3.5
	 */
	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	protected function __construct() {
		parent::__construct( '4.0', 'text-hover', 'c2c', __FILE__, array() );
		register_activation_hook( __FILE__, array( __CLASS__, 'activation' ) );

		return self::$instance = $this;
	}

	/**
	 * Handles activation tasks, such as registering the uninstall hook.
	 *
	 * @since 3.1
	 */
	public static function activation() {
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall' ) );
	}

	/**
	 * Handles uninstallation tasks, such as deleting plugin options.
	 */
	public static function uninstall() {
		delete_option( self::SETTING_NAME );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	protected function load_config() {
		$this->name      = __( 'Text Hover', 'text-hover' );
		$this->menu_name = __( 'Text Hover', 'text-hover' );

		$this->config = array(
			'text_to_hover' => array(
				'input'            => 'textarea',
				'datatype'         => 'hash',
				'default'          => array(
					"WP" => "WordPress"
				),
				'allow_html'       => true,
				'no_wrap'          => true,
				'input_attributes' => 'rows="15" cols="40"',
				'label'            => '',
				'help'             => '',
			),
			'text_hover_comments' => array(
				'input'            => 'checkbox',
				'default'          => false,
				'label'            => __( 'Enable text hover in comments?', 'text-hover' ),
				'help'             => '',
			),
			'replace_once' => array(
				'input'            => 'checkbox',
				'default'          => false,
				'label'            => __( 'Only text hover once per term per post?', 'text-hover' ),
				'help'             => __( 'If checked, then each term will only have a text hover occur for the first instance it appears in a post.', 'text-hover' ) .
					'<br />' .
					__( 'Note: this setting currently does not apply if the term contains a multibyte character.', 'text-hover' ),
			),
			'case_sensitive' => array(
				'input'            => 'checkbox',
				'default'          => true,
				'label'            => __( 'Should the matching of terms/abbreviations be case sensitive?', 'text-hover' ),
				'help'             => __( 'If checked, then hover text defined for \'WP\' would not apply to \'wp\'. This setting applies to all terms. If you want to selectively have case insensitive terms, then leave this option checked and create separate entries for each variation.', 'text-hover' ),
			),
			'use_pretty_tooltips' => array(
				'input'            => 'checkbox',
				'default'          => true,
				'label'            => __( 'Should better looking hover tooltips be shown?', 'text-hover' ),
				'help'             => __( 'If unchecked, the default browser rendering of tooltips will be used.', 'text-hover' ),
			),
			'when' => array(
				'input'            => 'select',
				'default'          => 'early',
				'options'          => array( 'early', 'late' ),
				'label'            => __( 'When to process text?', 'text-replace' ),
				'help'             => sprintf( __( "Text hover replacements can happen 'early' (before most other text processing for posts) or 'late' (after most other text processing for posts). By default the plugin handles text early, but depending on the replacements you've defined and the plugins you're using, you can eliminate certain conflicts by switching to 'late'. Finer-grained control can be achieved via the <code>%s</code> filter.", 'text-hover' ), 'c2c_text_hover_filter_priority' ),
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to actually actions against filters.
	 */
	public function register_filters() {
		$options = $this->get_options();

		/**
		 * Filters third party plugin/theme hooks that get processed for hover text.
		 *
		 * Use this to amend or remove support for hooks present in third party
		 * plugins and themes.
		 *
		 * Currently supported plugins:
		 * - Advanced Custom Fields
		 *    'acf/format_value/type=text',
		 *    'acf/format_value/type=textarea',
		 *    'acf/format_value/type=url',
		 *    'acf_the_content',
		 * - Elementor
		 *    'elementor/frontend/the_content',
		 *    'elementor/widget/render_content',
		 *
		 * @since 3.9
		 *
		 * @param array $filters The third party filters that get processed for
		 *                       hover text. See filter inline docs for defaults.
		 */
		$filters = (array) apply_filters( 'c2c_text_hover_third_party_filters', array(
			// Support for Advanced Custom Fields plugin.
			'acf/format_value/type=text',
			'acf/format_value/type=textarea',
			'acf/format_value/type=url',
			'acf_the_content',
			// Support for Elementor plugin.
			'elementor/frontend/the_content',
			'elementor/widget/render_content',
		) );

		// Add in relevant stock WP filters.
		$filters = array_merge( $filters, array( 'the_content', 'the_excerpt', 'widget_text' ) );

		/**
		 * Filters the hooks that get processed for hover text.
		 *
		 * @since 3.0
		 *
		 * @param array $filters The filters that get processed for hover text.
		 *                       Default ['the_content', 'the_excerpt',
		 *                       'widget_text'].
		 */
		$filters = (array) apply_filters( 'c2c_text_hover_filters', $filters );

		$default_priority = ( 'late' === $options[ 'when'] ) ? 1000 : 3;

		foreach ( $filters as $filter ) {
			/**
			 * Filters the priority for attaching the text hover handler to a
			 * hook.
			 *
			 * @since 4.0
			 *
			 * @param int    $priority The priority for the 'c2c_text_hover'
			 *                         filter. Default 3 if 'when' setting
			 *                         value is 'early', else 1000.
			 * @param string $filter   The filter name.
			 */
			$priority = (int) apply_filters( 'c2c_text_hover_filter_priority', $default_priority, $filter );

			add_filter( $filter, array( $this, 'text_hover' ), $priority );
		}

		add_action( 'wp_enqueue_scripts',  array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_print_scripts', array( $this, 'admin_print_scripts' ) );

		add_filter( 'get_comment_text',    array( $this, 'text_hover_comment_text' ), 11 );
		add_filter( 'get_comment_excerpt', array( $this, 'text_hover_comment_text' ), 11 );
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text Optional. Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Text Hover Settings', 'text-hover' ) );

		echo '<p>' . __( 'Text Hover is a plugin that allows you to add hover text for text in posts. Very handy to create hover explanations of people mentioned in your blog, and/or definitions of unique abbreviations and terms you use.', 'text-hover' ) . '</p>';
		echo '<div class="c2c-hr">&nbsp;</div>';
		echo '<h3>' . __( 'Abbreviations and hover text', 'text-hover' ) . '</h3>';
		echo '<p>' . __( 'Define terms/abbreviations and hovertext explanations here. The format should be like this:', 'text-hover' ) . '</p>';
		echo "<blockquote><code>WP => WordPress</code></blockquote>";
		echo '<p>' . __( 'Where <code>WP</code> is the term, acronym, or phrase you intend to use in your posts, and the <code>WordPress</code> would be what you want to appear in a hover tooltip when a visitor hovers their mouse over the term.', 'text-hover' );
		echo ' ' . __( 'See how things look: <abbr title="WordPress" class="c2c-text-hover">WP</abbr> (better-looking) or <abbr title="WordPress">WP</abbr> (basic).', 'text-hover' ) . '</p>';
		echo '<p>' . __( 'Other considerations:', 'text-hover' ) . '</p>';
		echo '<ul class="c2c-plugin-list"><li>';
		echo __( 'Terms and abbreviations are assumed to be whole words within your posts (i.e. they are immediately prepended by some sort of space character (space, tab, etc) and are immediately appended by a space character or punctuation (which can include any of: ?!.,-+)]})', 'text-hover' );
		echo '</li><li>';
		echo __( 'Only use quotes if they are actually part of the original or hovertext strings.', 'text-hover' );
		echo '</li><li><strong><em>';
		echo __( 'Define only one hovertext per line.', 'text-hover' );
		echo '</em></strong></li><li><strong><em>';
		echo __( 'Hovertexts must not span multiple lines.', 'text-hover' );
		echo '</em></strong></li><li><strong><em>';
		echo __( 'The use of HTML in hovertext should be limited to basic formatting tags, and only if you have better looking tooltips enabled.', 'text-hover' );
		echo '</em></strong></li></ul>';
	}

	/**
	 * Enqueues scripts and styles for plugin's settings page.
	 *
	 * @since 3.5
	 */
	public function admin_print_scripts() {
		if ( $this->options_page === get_current_screen()->id ) {
			$this->enqueue_scripts();
		}
	}

	/**
	 * Enqueues scripts and styles.
	 *
	 * @since 3.5
	 */
	public function enqueue_scripts() {
		$options = $this->get_options();

		/**
		 * Filters if pretty tooltips should be used.
		 *
		 * Pretty tooltips are rendered using the qTip2 JS library.
		 *
		 * @since 3.5
		 *
		 * @param bool $use Should pretty tooltips be used? Default is value set
		 *                  in plugin settings, which is initially true.
		 */
		if ( ! (bool) apply_filters( 'c2c_text_hover_use_pretty_tooltips', $options['use_pretty_tooltips'] ) ) {
			return;
		}

		$qtip2_version = '3.0.3';

		wp_enqueue_style( 'qtip2', plugins_url( 'assets/jquery.qtip.min.css', __FILE__ ), array(), $qtip2_version );
		wp_enqueue_style( 'text-hover', plugins_url( 'assets/text-hover.css', __FILE__ ), array(), $this->version );

		wp_enqueue_script( 'qtip2', plugins_url( 'assets/jquery.qtip.min.js', __FILE__ ), array( 'jquery' ), $qtip2_version, true );
		wp_enqueue_script( 'text-hover', plugins_url( 'assets/text-hover.js', __FILE__ ), array( 'jquery', 'qtip2' ), $this->version, true );
	}

	/**
	 * Text hovers comment text if enabled.
	 *
	 * @since 3.5
	 *
	 * @param string $text The comment text.
	 * @return string
	 */
	public function text_hover_comment_text( $text ) {
		$options = $this->get_options();

		/**
		 * Filters if comments should be processed for text hovers.
		 *
		 * @since 3.5
		 *
		 * @param bool $include_comments Should comments be processed for text
		 *                               hovers? Default is value set in plugin
		 *                               settings, which is initially false.
		 */
		if ( (bool) apply_filters( 'c2c_text_hover_comments', $options['text_hover_comments'] ) ) {
			$text = $this->text_hover( $text );
		}

		return $text;
	}

	/**
	 * Perform text hover replacements.
	 *
	 * @param string  $text Text to be processed for text hovers.
	 * @return string Text with hovertexts already processed.
	 */
	public function text_hover( $text ) {
		$options = $this->get_options();

		/**
		 * Filters the list of text that have associated hover text.
		 *
		 * @since 3.0
		 *
		 * @param array $text_to_hover Associative array of text to hover and
		 *                             respective hover text. Default is value
		 *                             set in plugin settings.
		 */
		$text_to_hover = (array) apply_filters( 'c2c_text_hover', $options['text_to_hover'] );

		/**
		 * Filters if text matching for text hover should be case sensitive.
		 *
		 * @since 3.0
		 *
		 * @param bool $case_sensitive Should text matching for text hover be
		 *                             case sensitive? Default is value set in
		 *                             plugin settings, which is initially true.
		 */
		$case_sensitive = (bool) apply_filters( 'c2c_text_hover_case_sensitive', $options['case_sensitive'] );

		/**
		 * Filters if text hovering should be limited to once per term per piece
		 * of text being processed regardless of how many times the term appears.
		 *
		 * @since 3.5
		 *
		 * @param bool $replace_once Should text hovering be limited to once
		 *                           per term per post? Default is value set in
		 *                           plugin settings, which is initially false.
		 */
		$limit = (bool) apply_filters( 'c2c_text_hover_once', $options['replace_once'] );

		$preg_flags = $case_sensitive ? 'ms' : 'msi';
		$mb_regex_encoding = null;

		$text = ' ' . $text . ' ';

		$can_do_mb = function_exists( 'mb_regex_encoding' ) && function_exists( 'mb_ereg_replace' ) && function_exists( 'mb_strlen' );

		// Store original mb_regex_encoding and then set it to UTF-8.
		if ( $can_do_mb ) {
			$mb_regex_encoding = mb_regex_encoding();
			mb_regex_encoding( 'UTF-8' );
		}

		if ( $text_to_hover ) {
			// Sort array descending by key length. This way longer, more precise
			// strings take precedence over shorter strings, preventing premature
			// partial replacements.
			// E.g. if "abc" and "abc def" are both defined  and in that order, the
			// string "abc def ghi" would match on "abc def", the longer string rather
			// than the shorter, less precise "abc".
			$keys = array_map( $can_do_mb ? 'mb_strlen' : 'strlen', array_keys( $text_to_hover ) );
			array_multisort( $keys, SORT_DESC, $text_to_hover );
		}

		foreach ( $text_to_hover as $old_text => $hover_text ) {

			if ( ! $hover_text ) {
				continue;
			}

			$use_mb = $can_do_mb && ( strlen( $old_text ) != mb_strlen( $old_text ) );

			//$new_text = "\\1<abbr class='c2c-text-hover' title='" . esc_attr( addcslashes( $hover_text, '\\$' ) ) . "'>\\2</abbr>\\3";
			$new_text = "<abbr class='c2c-text-hover' title='" . esc_attr( addcslashes( $hover_text, '\\$' ) ) . "'>\\1</abbr>";

			// If the string to be hovered looks like it could be HTML, then just do true
			// str_replace() and trust the user knows what they're doing.
			if ( false !== strpos( $old_text, '<' ) || false !== strpos( $old_text, '>' ) ) {
				$old_text = stripslashes( $old_text );

				// Sanity check that it's an HTML tag.
				if ( $use_mb ) {
					$is_tag = mb_ereg_match( '/<.+>/', $old_text );
				} else {
					$is_tag = preg_match( '/<.+>/', $old_text );
				}

				if ( $is_tag ) {
					$text = str_replace(
						$old_text,
						sprintf( str_replace( "\\1", '%s', $new_text ), $old_text ),
						$text
					);
					continue;
				}
			}

			// Save an unaltered version of the search text.
			$orig_old_text = $old_text;

			// Ensure the text doesn't make use of the regex delimiter.
			$old_text = preg_quote( $old_text, '~' );

			// If the string to be linked includes '&', consider '&amp;' and '&#038;' equivalents.
			// Visual editor will convert the former, but users aren't aware of the conversion.
			if ( false !== strpos( $old_text, '&' ) ) {
				$old_text = str_replace( '&', '&(?:amp;|#038;)?', $old_text );
			}

			// Allow spaces in hoverable text to represent any number of whitespace chars.
			$old_text = preg_replace( '/\s+/', '\s+', $old_text );

			// WILL match string within string, but WON'T match within tags
			/*
			$regex = "(?!<.*?)([\s\'\"\.\x98\x99\x9c\x9d\xCB\x9C\xE2\x84\xA2\xC5\x93\xEF\xBF\xBD\(\[\{\)\>])($old_text)([\s\'\"\x98\x99\x9c\x9d\xCB\x9C\xE2\x84\xA2\xC5\x93\xEF\xBF\xBD\?\!\.\,\-\+\]\)\}\(\<])(?![^<>]*?>)";
			*/

			/* Could really just use this instead of next three lines of code. Though '<' and '>' are intentionally excluded here. */
			//$regex_start_barrier = ctype_punct( $orig_old_text[0] )  ? '(?!\s)?' : '\b';
			//$regex_end_barrier   = ctype_punct( $orig_old_text[-1] ) ? '(?!\s)?' : '\b';

			$punctuation = array( '&', '#', '!', '?', '.', '-', '/', '\\', '%', '@', '$', '^', '*', '(', ')', '+', '=', '~', '`', '[', ']', '|', ':', ';', '"', "'" );
			$regex_start_barrier = in_array( $orig_old_text[0], $punctuation )  ? '(?!\s)?' : '\b';
			$regex_end_barrier   = in_array( $orig_old_text[-1], $punctuation ) ? '(?!\s)?' : '\b';
			$regex = "$regex_start_barrier($old_text)$regex_end_barrier(?!([^<]+)?>)";

			// If the text to be replaced has multibyte character(s), use
			// mb_ereg_replace() if possible.
			if ( $use_mb ) {
				// NOTE: mb_ereg_replace() does not support limiting the number of
				// replacements, hence the different handling if replacing once.
				if ( $limit ) {
					// Find first occurrence of the search string.
					$pos = mb_strpos( $text, $orig_old_text );
					// Only do the replacement if the search string was found.
					if ( false !== $pos ) {
						$text  = mb_substr( $text, 0, $pos )
							. sprintf( str_replace( "\\1", '%s', $new_text ), $orig_old_text )
							. mb_substr( $text, $pos + mb_strlen( $orig_old_text ) );
					}
				} else {
					$text = mb_ereg_replace( $regex, $new_text, $text, $preg_flags );
				}
			} else {
				$text = preg_replace( "~{$regex}~{$preg_flags}", $new_text, $text, ( $limit ? 1 : -1 ) );
			}

		}

		// Restore original mb_regexp_encoding, if changed.
		if ( $mb_regex_encoding ) {
			mb_regex_encoding( $mb_regex_encoding );
		}

		// Remove abbreviations within abbreviations.
		$text = preg_replace( "#(<abbr\s+[^>]+>)(.*)<abbr\s+[^>]+>([^<]*)</abbr>([^>]*)</abbr>#iU", "$1$2$3$4</abbr>" , $text );

		return trim( $text );
	}

} // end c2c_TextHover

add_action( 'plugins_loaded', array( 'c2c_TextHover', 'get_instance' ) );

endif; // end if !class_exists()
