<?php
/**
 * Plugin Name: Text Replace
 * Version:     4.0
 * Plugin URI:  https://coffee2code.com/wp-plugins/text-replace/
 * Author:      Scott Reilly
 * Author URI:  https://coffee2code.com/
 * License:     GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: text-replace
 * Description: Replace text with other text. Handy for creating shortcuts to common, lengthy, or frequently changing text/HTML, or for smilies.
 *
 * Compatible with WordPress 4.9+ through 5.7+.
 *
 * =>> Read the accompanying readme.txt file for instructions and documentation.
 * =>> Also, visit the plugin's homepage for additional information and updates.
 * =>> Or visit: https://wordpress.org/plugins/text-replace/
 *
 * @package Text_Replace
 * @author  Scott Reilly
 * @version 4.0
 */

/*
	Copyright (c) 2004-2021 by Scott Reilly (aka coffee2code)

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

if ( ! class_exists( 'c2c_TextReplace' ) ) :

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'c2c-plugin.php' );

final class c2c_TextReplace extends c2c_Plugin_064 {

	/**
	 * Name of plugin's setting.
	 *
	 * @since 3.8
	 * @var string
	 */
	const SETTING_NAME = 'c2c_text_replace';

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
		parent::__construct( '4.0', 'text-replace', 'c2c', __FILE__, array() );
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
	 * Handle plugin updates.
	 *
	 * @since 3.2.1
	 *
	 * @param string $old_version The version number of the old version of
	 *                            the plugin. '0.0' indicates no version
	 *                            previously stored
	 * @param array  $options     Array of all plugin options
	 */
	protected function handle_plugin_upgrade( $old_version, $options ) {
		if ( version_compare( $old_version, '3.2.1', '<' ) ) {
			// Plugin got upgraded from a version earlier than 3.2.1
			// Logic was inverted for case_sensitive.
			$options['case_sensitive'] = ! $options['case_sensitive'];
		}
		return $options; // Important!
	}

	/**
	 * Initializes the plugin's configuration and localizable text variables.
	 */
	protected function load_config() {
		$this->name      = __( 'Text Replace', 'text-replace' );
		$this->menu_name = __( 'Text Replace', 'text-replace' );

		$this->config = array(
			'text_to_replace' => array(
				'input'            => 'inline_textarea',
				'datatype'         => 'hash',
				'default'          => array(
					":wp:"          => "<a href='https://wordpress.org'>WordPress</a>",
					":codex:"       => "<a href='https://codex.wordpress.org'>WordPress Codex</a>",
					":coffee2code:" => "<a href='https://coffee2code.com' title='coffee2code'>coffee2code</a>"
				),
				'allow_html'       => true,
				'no_wrap'          => true,
				'input_attributes' => 'rows="15"',
				'label'            => __( 'Text to replace', 'text-replace' ),
				'help'             => __( 'One per line. A shortcut definition must not span multiple lines. HTML is allowed for shortcuts and their replacement strings. Only use quotes if they are an actual part of the shortcut or replacement strings.', 'text-replace' ),
			),
			'text_replace_comments' => array(
				'input'            => 'checkbox',
				'default'          => false,
				'label'            => __( 'Enable text replacement in comments?', 'text-replace' ),
				'help'             => __( 'If checked, then all comments, including those from visitors, will be processed for text replacements.', 'text-replace' ),
			),
			'replace_once' => array(
				'input'            => 'checkbox',
				'default'          => false,
				'label'            => __( 'Only text replace once per term per post?', 'text-replace' ),
				'help'             => __( 'If checked, then each term will only be replaced the first time it appears in a post.', 'text-replace' ),
			),
			'case_sensitive' => array(
				'input'            => 'checkbox',
				'default'          => true,
				'label'            => __( 'Case sensitive text replacement?', 'text-replace' ),
				'help'             => __( 'If checked, then a replacement for :wp: would not replace :WP:.', 'text-replace' ),
				'more_help'        => __( 'This setting applies to all shortcuts. If you want to selectively have case insensitive shortcuts, then leave this option checked and create separate entries for each variation.', 'text-replace' ),
			),
			'when' => array(
				'input'            => 'select',
				'datatype'         => 'hash',
				'default'          => 'early',
				'options'          => array(
					'early' => __( 'early', 'text-replace' ),
					'late'  => __( 'late', 'text-replace' )
				),
				'label'            => __( 'When to process text?', 'text-replace' ),
				/* translators: %s: The name of a filter provided by the plugin. */
				'help'             => sprintf(
					__( "Text replacements can happen 'early' (before most other text processing for posts) or 'late' (after most other text processing for posts). By default the plugin handles text early, but depending on the replacements you've defined and the plugins you're using, you can eliminate certain conflicts by switching to 'late'. Finer-grained control can be achieved via the %s filter.", 'text-replace' ),
					'<code>c2c_text_replace_filter_priority</code>'
				),
			),
			'more_filters' => array(
				'input'            => 'inline_textarea',
				'datatype'         => 'array',
				'no_wrap'          => true,
				'input_attributes' => 'rows="6"',
				'label'            => __( 'More filters', 'text-replace' ),
				'help'             => sprintf(
					/* translators: %s: List of default filters. */
					__( 'List more filters that should get text replacements. One filter per line. These supplement the default filters: %s (and any others added via filters).', 'text-replace' ),
					'<code>' . implode( '</code>, <code>', $this->get_default_filters() ) . '</code>'
				),
			),
		);
	}

	/**
	 * Returns the default filters processed by the plugin.
	 *
	 * The values do not take into account any user-specified filters from the
	 * more_filters setting nor any filtering. A value returned here does not
	 * necessarily mean it'll get text replaced.
	 *
	 * Currently supported third-party plugins:
	 *
	 * - Advanced Custom Fields
	 *    'acf/format_value/type=text',
	 *    'acf/format_value/type=textarea',
	 *    'acf/format_value/type=url',
	 *    'acf_the_content',
	 * - Elementor
	 *    'elementor/frontend/the_content',
	 *    'elementor/widget/render_content',
	 *
	 * @since 4.0
	 *
	 * @param string $type The type of filters. One of 'core', 'third_party', 'both'.
	 *                     Default 'core'.
	 * @return array The filters associated with the specified type. Returns an
	 *               empty array for an invalid type.
	 */
	public function get_default_filters( $type = 'core' ) {
		$core        = array( 'the_content', 'the_excerpt', 'widget_text' );
		$third_party = array(
			// Support for Advanced Custom Fields plugin.
			'acf/format_value/type=text',
			'acf/format_value/type=textarea',
			'acf/format_value/type=url',
			'acf_the_content',
			// Support for Elementor plugin.
			'elementor/frontend/the_content',
			'elementor/widget/render_content',
		);

		switch ( $type ) {
			case 'both':
				$filters = array_merge( $core, $third_party );
				break;
			case 'core':
				$filters = $core;
				break;
			case 'third_party':
				$filters = $third_party;
				break;
			default:
				$filters = array();
		}

		return $filters;
	}

	/**
	 * Override the plugin framework's register_filters() to actually register actions against filters.
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
		$filters = (array) apply_filters( 'c2c_text_replace_third_party_filters', $this->get_default_filters( 'third_party' ) );

		// Add in relevant stock WP filters and additional filters.
		$filters = array_unique( array_merge( $filters, $this->get_default_filters(), $options['more_filters'] ) );

		/**
		 * Filters the hooks that get processed for hover text.
		 *
		 * @since 3.0
		 *
		 * @param array $filters The filters that get processed for text.
		 *                       replacement Default ['the_content',
		 *                       'the_excerpt', 'widget_text'] plus third-party
		 *                       filters defined via the
		 *                       `c2c_text_replace_third_party_filters` filter.
		 */
		$filters = (array) apply_filters( 'c2c_text_replace_filters', $filters );

		$default_priority = ( 'late' === $options[ 'when'] ) ? 1000 : 2;

		foreach ( $filters as $filter ) {
			/**
			 * Filters the priority for attaching the text replacement handler to
			 * a hook.
			 *
			 * @since 3.9
			 *
			 * @param int    $priority The priority for the 'c2c_text_replace'
			 *                         filter. Default 2 if 'when' setting
			 *                         value is 'early', else 1000.
			 * @param string $filter   The filter name.
			 */
			$priority = (int) apply_filters( 'c2c_text_replace_filter_priority', $default_priority, $filter );

			add_filter( $filter, array( $this, 'text_replace' ), $priority );
		}

		// Note that the priority must be set high enough to avoid <img> tags inserted by the text replace process from
		// getting omitted as a result of the comment text sanitation process, if you use this plugin for smilies, for instance.
		add_filter( 'get_comment_text',    array( $this, 'text_replace_comment_text' ), 11 );
		add_filter( 'get_comment_excerpt', array( $this, 'text_replace_comment_text' ), 11 );
	}

	/**
	 * Returns translated strings used by c2c_Plugin parent class.
	 *
	 * @since 4.0
	 *
	 * @param string $string Optional. The string whose translation should be
	 *                       returned, or an empty string to return all strings.
	 *                       Default ''.
	 * @return string|string[] The translated string, or if a string was provided
	 *                         but a translation was not found then the original
	 *                         string, or an array of all strings if $string is ''.
	 */
	public function get_c2c_string( $string = '' ) {
		$strings = array(
			'%s cannot be cloned.'
				/* translators: %s: Name of plugin class. */
				=> __( '%s cannot be cloned.', 'text-replace' ),
			'%s cannot be unserialized.'
				/* translators: %s: Name of plugin class. */
				=> __( '%s cannot be unserialized.', 'text-replace' ),
			'A value is required for: "%s"'
				/* translators: %s: Label for setting. */
				=> __( 'A value is required for: "%s"', 'text-replace' ),
			'Click for more help on this plugin'
				=> __( 'Click for more help on this plugin', 'text-replace' ),
			' (especially check out the "Other Notes" tab, if present)'
				=> __( ' (especially check out the "Other Notes" tab, if present)', 'text-replace' ),
			'Coffee fuels my coding.'
				=> __( 'Coffee fuels my coding.', 'text-replace' ),
			'Donate'
				=> __( 'Donate', 'text-replace' ),
			'Expected integer value for: %s'
				=> __( 'Expected integer value for: %s', 'text-replace' ),
			'If this plugin has been useful to you, please consider a donation'
				=> __( 'If this plugin has been useful to you, please consider a donation', 'text-replace' ),
			'Invalid file specified for C2C_Plugin: %s'
				/* translators: %s: Path to the plugin file. */
				=> __( 'Invalid file specified for C2C_Plugin: %s', 'text-replace' ),
			'More information about %1$s %2$s'
				/* translators: 1: plugin name 2: plugin version */
				=> __( 'More information about %1$s %2$s', 'text-replace' ),
			'More Help'
				=> __( 'More Help', 'text-replace' ),
			'More Plugin Help'
				=> __( 'More Plugin Help', 'text-replace' ),
			'Reset Settings'
				=> __( 'Reset Settings', 'text-replace' ),
			'Save Changes'
				=> __( 'Save Changes', 'text-replace' ),
			'See the "Help" link to the top-right of the page for more help.'
				=> __( 'See the "Help" link to the top-right of the page for more help.', 'text-replace' ),
			'Settings'
				=> __( 'Settings', 'text-replace' ),
			'Settings reset.'
				=> __( 'Settings reset.', 'text-replace' ),
			'Something went wrong.'
				=> __( 'Something went wrong.', 'text-replace' ),
			"Thanks for the consideration; it's much appreciated."
				=> __( "Thanks for the consideration; it's much appreciated.", 'text-replace' ),
			'The method %1$s should not be called until after the %2$s action.'
				/* translators: 1: The name of a code function, 2: The name of a WordPress action. */
				=> __( 'The method %1$s should not be called until after the %2$s action.', 'text-replace' ),
			'The plugin author homepage.'
				=> __( 'The plugin author homepage.', 'text-replace' ),
			"The plugin configuration option '%s' must be supplied."
				/* translators: %s: The setting configuration key name. */
				=>__( "The plugin configuration option '%s' must be supplied.", 'text-replace' ),
			'This plugin brought to you by %s.'
				/* translators: %s: Link to plugin author's homepage. */
				=> __( 'This plugin brought to you by %s.', 'text-replace' ),
		);

		if ( ! $string ) {
			return array_values( $strings );
		}

		return ! empty( $strings[ $string ] ) ? $strings[ $string ] : $string;
	}

	/**
	 * Outputs the text above the setting form.
	 *
	 * @param string $localized_heading_text Optional. Localized page heading text.
	 */
	public function options_page_description( $localized_heading_text = '' ) {
		parent::options_page_description( __( 'Text Replace Settings', 'text-replace' ) );

		echo '<p>' . __( 'Text Replace is a plugin that allows you to replace text with other text in posts, etc. Very handy to create shortcuts to commonly-typed and/or lengthy text/HTML, or for smilies.', 'text-replace' ) . "</p>\n";
		echo '<div class="c2c-hr">&nbsp;</div>' . "\n";
		echo '<h3>' . __( 'Shortcuts and text replacements', 'text-replace' ) . "</h3>\n";
		echo '<p>' . __( 'Shortcuts and text replacement expansions defined below should be formatted like this:', 'text-replace' ) . "</p>\n";
		echo "<blockquote><code>:wp: => &lt;a href='https://wordpress.org'>WordPress&lt;/a></code></blockquote>\n";
		echo '<ul class="c2c-plugin-list">' . "\n";
		echo "\t<li>" . sprintf( __( "The %s represents the text in your existing posts that you want to get replaced. (The colons aren't necessary, but is a good technique to use to reduce unexpected replacements.)", 'text-replace' ), '<code>:wp:</code>' ) . "</li>\n";
		echo "\t<li>" . sprintf( __( 'The %s is the separator between the text to replace and the text replacement.', 'text-replace' ), '<code> => </code>' ) . "</li>\n";
		echo "\t<li>" . sprintf( __( 'The %s represents the replacement text.', 'text-replace' ), '<code>&lt;a href=\'https://wordpress.org\'&gt;WordPress&lt;/a&gt;</code>' ) . "</li>\n";
		echo "</ul>\n";
		printf( '<p>' . __( 'If you are solely interested in replacing words or phrases with links to the URLs of your choosing, then check out my <a href="%s">Linkify Text</a> plugin, which better facilitates that variety of replacements.', 'text-replace' ) . "</p>\n", 'https://wordpress.org/plugins/linkify-text/' );
		printf( '<p>' . __( 'If you are solely interested in adding help text as tooltips that appear when a visitor hovers over a word or phrase, then check out my <a href="%s">Text Hover</a> plugin, which better facilitates that variety of replacements.', 'text-replace' ) . "</p>\n", 'https://wordpress.org/plugins/text-hover/' );
		echo '<p>' . __( 'Other considerations:', 'text-replace' ) . "</p>\n";
		echo '<ul class="c2c-plugin-list">' . "\n\t" . '<li>';
		_e( 'Be careful not to define text that could match partially when you don\'t want it to:<br />i.e.  <code>Me => Scott</code> would also inadvertently change "Men" to be "Scottn"', 'text-replace' );
		echo "</li>\n\t<li>";
		printf( __( 'If you intend to use this plugin to handle smilies, you should probably disable WordPress\'s default smilie/emoticon handler on the <a href="%s">Writing Settings</a> page.', 'text-replace' ), admin_url( 'options-writing.php' ) );
		echo "</li>\n\t<li>";
		_e( 'Text inside of HTML tags (such as tag names and attributes) will not be matched. So, for example, you can\'t expect a <code>:mycss:</code> shortcut to work in: <code>&lt;a href="" :mycss:&gt;text&lt;/a&gt;.</code>', 'text-replace' );
		echo "</li>\n</ul>\n";
	}

	/**
	 * Text replaces comment text if enabled.
	 *
	 * @since 3.5
	 *
	 * @param string $text The comment text
	 * @return string
	 */
	public function text_replace_comment_text( $text ) {
		$options = $this->get_options();

		/**
		 * Filters if comments should be processed for text replacement.
		 *
		 * @since 3.0
		 *
		 * @param bool $include_comments Should comments be processed for text
		 *                               replacement? Default is value set in
		 *                               plugin settings, which is initially
		 *                               false.
		 */
		if ( (bool) apply_filters( 'c2c_text_replace_comments', $options['text_replace_comments'] ) ) {
			$text = $this->text_replace( $text );
		}

		return $text;
	}

	/**
	 * Perform text replacements.
	 *
	 * @param string  $text Text to be processed for text replacements
	 * @return string Text with replacements already processed
	 */
	public function text_replace( $text ) {
		$options         = $this->get_options();

		/**
		 * Filters the list of text that will get replaced.
		 *
		 * @since 3.0
		 *
		 * @param array $text_to_replace Associative array of text to replace
		 *                               and respective replacement text. Default
		 *                               is value set in plugin settings.
		 */
		$text_to_replace = (array) apply_filters( 'c2c_text_replace',                $options['text_to_replace'] );

		/**
		 * Filters if text matching for text replacement should be case sensitive.
		 *
		 * @since 3.0
		 *
		 * @param bool $case_sensitive Should text matching for text replacement
		 *                             be case sensitive? Default is value set in
		 *                             plugin settings, which is initially true.
		 */
		$case_sensitive  = (bool)  apply_filters( 'c2c_text_replace_case_sensitive', $options['case_sensitive'] );

		/**
		 * Filters if text replacement should be limited to once per phrase per
		 * piece of text being processed regardless of how many times the phrase
		 * appears.
		 *
		 * @since 3.5
		 *
		 * @param bool $replace_once Should text hovering be limited to once
		 *                           per term per post? Default is value set in
		 *                           plugin settings, which is initially false.
		 */
		$limit           = (bool)  apply_filters( 'c2c_text_replace_once',           $options['replace_once'] );

		$preg_flags      = $case_sensitive ? 'ms' : 'msi';
		$mb_regex_encoding = null;

		// Bail early if there are no replacements defined.
		if ( ! $text_to_replace || ( isset( $text_to_replace[0] ) && ! $text_to_replace[0] ) ) {
			return $text;
		}

		$text = ' ' . $text . ' ';

		$can_do_mb = function_exists( 'mb_regex_encoding' ) && function_exists( 'mb_ereg_replace' ) && function_exists( 'mb_strlen' );

		// Store original mb_regex_encoding and then set it to UTF-8.
		if ( $can_do_mb ) {
			$mb_regex_encoding = mb_regex_encoding();
			mb_regex_encoding( 'UTF-8' );
		}

		if ( $text_to_replace ) {
			// Sort array descending by key length. This way longer, more precise
			// strings take precedence over shorter strings, preventing premature
			// partial replacements.
			// E.g. if "abc" and "abc def" are both defined for linking and in that
			// order, the string "abc def ghi" would match on "abc def", the longer
			// string rather than the shorter, less precise "abc".
			$keys = array_map( $can_do_mb ? 'mb_strlen' : 'strlen', array_keys( $text_to_replace ) );
			array_multisort( $keys, SORT_DESC, $text_to_replace );
		}

		foreach ( $text_to_replace as $old_text => $new_text ) {

			// If the text to be replaced includes a '<' or '>', do direct string replacement.
			if ( strpos( $old_text, '<' ) !== false || strpos( $old_text, '>' ) !== false ) {
				// If only doing one replacement, need to handle specially since there is
				// no built-in, non-preg_replace method to do a single replacement.
				if ( $limit ) {
					$pos = $case_sensitive ? strpos( $text, $old_text ) : stripos( $text, $old_text );
					if ( $pos !== false ) {
						$text = substr_replace( $text, $new_text, $pos, strlen( $old_text ) );
					}
				} else {
					if ( $case_sensitive ) {
						$text = str_replace( $old_text, $new_text, $text );
					} else {
						$text = str_ireplace( $old_text, $new_text, $text );
					}
				}
			}
			// Otherwise use preg_replace() to avoid replacing text inside HTML tags.
			else {
				$old_text = preg_quote( $old_text, '~' );
				$new_text = addcslashes( $new_text, '\\$' );

				// If the string to be linked includes '&', consider '&amp;' and '&#038;' equivalents.
				// Visual editor will convert the former, but users aren't aware of the conversion.
				if ( false !== strpos( $old_text, '&' ) ) {
					$old_text = str_replace( '&', '&(amp;|#038;)?', $old_text );
				}

				// Allow spaces in linkable text to represent any number of whitespace chars.
				$old_text = preg_replace( '/\s+/', '\s+', $old_text );

				// WILL match string within string, but WON'T match within tags.
				$regex = "(?!<.*?){$old_text}(?![^<>]*?>)";

				// If the text to be replaced has multibyte character(s), use
				// mb_ereg_replace() if possible.
				if ( $can_do_mb && ( strlen( $old_text ) != mb_strlen( $old_text ) ) ) {
					// NOTE: mb_ereg_replace() does not support limiting the number of
					// replacements, hence the different handling if replacing once.
					if ( $limit ) {
						// Find first occurrence of the search string.
						mb_ereg_search_init( $text, $old_text, $preg_flags );
						$pos = mb_ereg_search_pos();

						// Only do the replacement if the search string was found.
						if ( false !== $pos ) {
							$match = mb_ereg_search_getregs();
							$text  = mb_substr( $text, 0, $pos[0] )
								. $new_text
								. mb_substr( $text, $pos[0] + mb_strlen( $match[0] ) );
						}
					} else {
						$text = mb_ereg_replace( $regex, $new_text, $text, $preg_flags );
					}
				} else {
					$text = preg_replace( "~{$regex}~{$preg_flags}", $new_text, $text, ( $limit ? 1 : -1 ) );
				}
			}

		}

		// Restore original mb_regexp_encoding, if changed.
		if ( $mb_regex_encoding ) {
			mb_regex_encoding( $mb_regex_encoding );
		}

		return trim( $text );
	} //end text_replace()

} // end c2c_TextReplace

add_action( 'plugins_loaded', array( 'c2c_TextReplace', 'get_instance' ) );

endif; // end if !class_exists()
