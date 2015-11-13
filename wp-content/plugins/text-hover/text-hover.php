<?php
/**
 * Plugin Name: Text Hover
 * Version:     3.6
 * Plugin URI:  http://coffee2code.com/wp-plugins/text-hover/
 * Author:      Scott Reilly
 * Author URI:  http://coffee2code.com/
 * License:     GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: text-hover
 * Domain Path: /lang/
 * Description: Add hover text to regular text in posts. Handy for providing explanations of names, terms, phrases, and acronyms mentioned in your blog.
 *
 * Compatible with WordPress 3.6+ through 4.1+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/text-hover/
 *
 * @package Text_Hover
 * @author Scott Reilly
 * @version 3.6
 */

/*
 * TODO:
 * - Shortcode and template tag to display listing of all supported text hovers (filterable)
*/

/*
	Copyright (c) 2007-2015 by Scott Reilly (aka coffee2code)

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

final class c2c_TextHover extends C2C_Plugin_039 {

	/**
	 * The one true instance.
	 *
	 * @var c2c_TextReplace
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
		parent::__construct( '3.6', 'text-hover', 'c2c', __FILE__, array() );
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
	public function uninstall() {
		delete_option( 'c2c_text_hover' );
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	protected function load_config() {
		$this->name      = __( 'Text Hover', $this->textdomain );
		$this->menu_name = __( 'Text Hover', $this->textdomain );

		$this->config = array(
			'text_to_hover' => array( 'input' => 'textarea', 'datatype' => 'hash', 'default' => array(
					"WP" => "WordPress"
				), 'allow_html' => true, 'no_wrap' => true, 'input_attributes' => 'rows="15" cols="40"',
				'label' => '', 'help' => ''
			),
			'text_hover_comments' => array( 'input' => 'checkbox', 'default' => false,
				'label' => __( 'Enable text hover in comments?', $this->textdomain ),
				'help'  => ''
			),
			'replace_once' => array( 'input' => 'checkbox', 'default' => false,
				'label' => __( 'Only text hover once per term per post?', $this->textdomain ),
				'help'  => __( 'If checked, then each term will only have a text hover occur for the first instance it appears in a post.', $this->textdomain ) .
					'<br />' .
					__( 'Note: this setting currently does not apply if the term contains a multibyte character.', $this->textdomain )
			),
			'case_sensitive' => array( 'input' => 'checkbox', 'default' => true,
				'label' => __( 'Should the matching of terms/acronyms be case sensitive?', $this->textdomain ),
				'help'  => __( 'If checked, then hover text defined for \'WP\' would not apply to \'wp\'. This setting applies to all terms. If you want to selectively have case insensitive terms, then leave this option checked and create separate entries for each variation.', $this->textdomain )
			),
			'use_pretty_tooltips' => array( 'input' => 'checkbox', 'default' => true,
				'label' => __( 'Should better looking hover tooltips be shown?', $this->textdomain ),
				'help'  => __( 'If unchecked, the default browser rendering of tooltips will be used.', $this->textdomain )
			),
		);
	}

	/**
	 * Override the plugin framework's register_filters() to actually actions against filters.
	 */
	public function register_filters() {
		$filters = apply_filters( 'c2c_text_hover_filters', array( 'the_content', 'get_the_excerpt', 'widget_text' ) );
		foreach ( (array) $filters as $filter ) {
			add_filter( $filter, array( $this, 'text_hover' ), 3 );
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
		parent::options_page_description( __( 'Text Hover Settings', $this->textdomain ) );

		echo '<p>' . __( 'Text Hover is a plugin that allows you to add hover text for text in posts. Very handy to create hover explanations of people mentioned in your blog, and/or definitions of unique acronyms and terms you use.', $this->textdomain ) . '</p>';
		echo '<div class="c2c-hr">&nbsp;</div>';
		echo '<h3>' . __( 'Acronyms and hover text', $this->textdomain ) . '</h3>';
		echo '<p>' . __( 'Define terms/acronyms and hovertext explanations here.  The format should be like this:', $this->textdomain ) . '</p>';
		echo "<blockquote><code>WP => WordPress</code></blockquote>";
		echo '<p>' . __( 'Where <code>WP</code> is the term, acronym, or phrase you intend to use in your posts, and the <code>WordPress</code> would be what you want to appear in a hover tooltip when a visitor hovers their mouse over the term.', $this->textdomain );
		echo ' ' . __( 'See how things look: <acronym title="WordPress" style="border-bottom:1px dashed #000;">WP</acronym>.', $this->textdomain ) . '</p>';
		echo '<p>' . __( 'Other considerations:', $this->textdomain ) . '</p>';
		echo '<ul class="c2c-plugin-list"><li>';
		echo __( 'Terms and acronyms are assumed to be whole words within your posts (i.e. they are immediately prepended by some sort of space character (space, tab, etc) and are immediately appended by a space character or punctuation (which can include any of: ?!.,-+)]})', $this->textdomain );
		echo '</li><li>';
		echo __( 'Only use quotes it they are actual part of the original or hovertext strings.', $this->textdomain );
		echo '</li><li><strong><em>';
		echo __( 'Define only one hovertext per line.', $this->textdomain );
		echo '</em></strong></li><li><strong><em>';
		echo __( 'Hovertexts must not span multiple lines.', $this->textdomain );
		echo '</em></strong></li><li><strong><em>';
		echo __( 'Don\'t use HTML in the hovertext.', $this->textdomain );
		echo '</em></strong></li></ul>';
	}

	/**
	 * Enqueues scripts and styles for plugin's settings page.
	 *
	 * @since 3.5
	 */
	public function admin_print_scripts() {
		if ( $this->options_page == get_current_screen()->id ) {
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

		if ( ! apply_filters( 'c2c_text_hover_use_pretty_tooltips', $options['use_pretty_tooltips'] == '1' ) ) {
			return;
		}

		wp_enqueue_style( 'qtip2', plugins_url( 'assets/jquery.qtip.min.css', __FILE__ ) );
		wp_enqueue_style( 'text-hover', plugins_url( 'assets/text-hover.css', __FILE__ ) );

		wp_enqueue_script( 'qtip2', plugins_url( 'assets/jquery.qtip.min.js', __FILE__ ), array( 'jquery' ), '2.2.0', true );
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

		if ( apply_filters( 'c2c_text_hover_comments', $options['text_hover_comments'] ) ) {
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
		$options        = $this->get_options();
		$text_to_hover  = apply_filters( 'c2c_text_hover',                $options['text_to_hover'] );
		$case_sensitive = apply_filters( 'c2c_text_hover_case_sensitive', $options['case_sensitive'] );
		$limit          = apply_filters( 'c2c_text_hover_once',           $options['replace_once'] ) ? 1 : -1;
		$preg_flags     = $case_sensitive ? 's' : 'si';
		$mb_regex_encoding = null;

		$text = ' ' . $text . ' ';

		$can_do_mb = function_exists( 'mb_regex_encoding' ) && function_exists( 'mb_ereg_replace' ) && function_exists( 'mb_strlen' );

		// Store original mb_regex_encoding and then set it to UTF-8.
		if ( $can_do_mb ) {
			$mb_regex_encoding = mb_regex_encoding();
			mb_regex_encoding( 'UTF-8' );
		}

		foreach ( (array) $text_to_hover as $old_text => $hover_text ) {

			if ( empty( $hover_text ) ) {
				continue;
			}

			$new_text = "\\1<acronym class='c2c-text-hover' title='" . esc_attr( addcslashes( $hover_text, '\\$' ) ) . "'>\\2</acronym>\\3";
			$old_text = preg_quote( $old_text, '~' );

			// If the string to be linked includes '&', consider '&amp;' and '&#038;' equivalents.
			// Visual editor will convert the former, but users aren't aware of the conversion.
			if ( false !== strpos( $old_text, '&' ) ) {
				$old_text = str_replace( '&', '&(?:amp;|#038;)?', $old_text );
			}

			// WILL match string within string, but WON'T match within tags
			$regex = "(?!<.*?)([\s\'\"\.\x98\x99\x9c\x9d\xCB\x9C\xE2\x84\xA2\xC5\x93\xEF\xBF\xBD\(\[\{])($old_text)([\s\'\"\x98\x99\x9c\x9d\xCB\x9C\xE2\x84\xA2\xC5\x93\xEF\xBF\xBD\?\!\.\,\-\+\]\)\}])(?![^<>]*?>)";

			// If the text to be replaced has multibyte character(s), use
			// mb_ereg_replace() if possible.
			if ( $can_do_mb && ( strlen( $old_text ) != mb_strlen( $old_text ) ) ) {
				// NOTE: mb_ereg_replace() does not support limiting the number of replacements.
				$text = mb_ereg_replace( $regex, $new_text, $text, $preg_flags );
			} else {
				$text = preg_replace( "~{$regex}~{$preg_flags}", $new_text, $text, $limit );
			}

		}

		// Restore original mb_regexp_encoding, if changed.
		if ( $mb_regex_encoding ) {
			mb_regex_encoding( $mb_regex_encoding );
		}

		return trim( $text );
	}

} // end c2c_TextHover

c2c_TextHover::get_instance();

endif; // end if !class_exists()
