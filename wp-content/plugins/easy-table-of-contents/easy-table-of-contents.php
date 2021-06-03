<?php
/**
 * Plugin Name: Easy Table of Contents
 * Plugin URI: http://connections-pro.com/
 * Description: Adds a user friendly and fully automatic way to create and display a table of contents generated from the page content.
 * Version: 2.0.17
 * Author: Steven A. Zahm
 * Author URI: http://connections-pro.com/
 * Text Domain: easy-table-of-contents
 * Domain Path: /languages
 *
 * Copyright 2021  Steven A. Zahm  ( email : helpdesk@connections-pro.com )
 *
 * Easy Table of Contents is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Easy Table of Contents; if not, see <http://www.gnu.org/licenses/>.
 *
 * @package  Easy Table of Contents
 * @category Plugin
 * @author   Steven A. Zahm
 * @version  2.0.17
 */

use Easy_Plugins\Table_Of_Contents\Debug;
use function Easy_Plugins\Table_Of_Contents\String\mb_find_replace;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'ezTOC' ) ) {

	/**
	 * Class ezTOC
	 */
	final class ezTOC {

		/**
		 * Current version.
		 *
		 * @since 1.0
		 * @var string
		 */
		const VERSION = '2.0.17';

		/**
		 * Stores the instance of this class.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @var ezTOC
		 */
		private static $instance;

		/**
		 * @since 2.0
		 * @var array
		 */
		private static $store = array();

		/**
		 * A dummy constructor to prevent the class from being loaded more than once.
		 *
		 * @access public
		 * @since  1.0
		 */
		public function __construct() { /* Do nothing here */ }

		/**
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @return ezTOC
		 */
		public static function instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof self ) ) {

				self::$instance = new self;

				self::defineConstants();
				self::includes();
				self::hooks();

				self::loadTextdomain();
			}

			return self::$instance;
		}

		/**
		 * Define the plugin constants.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function defineConstants() {

			define( 'EZ_TOC_DIR_NAME', plugin_basename( dirname( __FILE__ ) ) );
			define( 'EZ_TOC_BASE_NAME', plugin_basename( __FILE__ ) );
			define( 'EZ_TOC_PATH', dirname( __FILE__ ) );
			define( 'EZ_TOC_URL', plugin_dir_url( __FILE__ ) );
		}

		/**
		 * Includes the plugin dependency files.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function includes() {

			require_once( EZ_TOC_PATH . '/includes/class.options.php' );

			if ( is_admin() ) {

				// This must be included after `class.options.php` because it depends on it methods.
				require_once( EZ_TOC_PATH . '/includes/class.admin.php' );
			}

			require_once( EZ_TOC_PATH . '/includes/class.post.php' );
			require_once( EZ_TOC_PATH . '/includes/class.widget-toc.php' );
			require_once( EZ_TOC_PATH . '/includes/Debug.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.functions.php' );
			require_once( EZ_TOC_PATH . '/includes/inc.string-functions.php' );

			require_once( EZ_TOC_PATH . '/includes/inc.plugin-compatibility.php' );
		}

		/**
		 * Add the core action filter hook.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		private static function hooks() {

			//add_action( 'plugins_loaded', array( __CLASS__, 'loadTextdomain' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'enqueueScripts' ) );

			// Run after shortcodes are interpreted (priority 10).
			add_filter( 'the_content', array( __CLASS__, 'the_content' ), 100 );
			add_shortcode( 'ez-toc', array( __CLASS__, 'shortcode' ) );
			add_shortcode( apply_filters( 'ez_toc_shortcode', 'toc' ), array( __CLASS__, 'shortcode' ) );
		}

		/**
		 * Load the plugin translation.
		 *
		 * Credit: Adapted from Ninja Forms / Easy Digital Downloads.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 *
		 * @uses   apply_filters()
		 * @uses   get_locale()
		 * @uses   load_textdomain()
		 * @uses   load_plugin_textdomain()
		 *
		 * @return void
		 */
		public static function loadTextdomain() {

			// Plugin textdomain. This should match the one set in the plugin header.
			$domain = 'easy-table-of-contents';

			// Set filter for plugin's languages directory
			$languagesDirectory = apply_filters( "ez_{$domain}_languages_directory", EZ_TOC_DIR_NAME . '/languages/' );

			// Traditional WordPress plugin locale filter
			$locale   = apply_filters( 'plugin_locale', get_locale(), $domain );
			$fileName = sprintf( '%1$s-%2$s.mo', $domain, $locale );

			// Setup paths to current locale file
			$local  = $languagesDirectory . $fileName;
			$global = WP_LANG_DIR . "/{$domain}/" . $fileName;

			if ( file_exists( $global ) ) {

				// Look in global `../wp-content/languages/{$domain}/` folder.
				load_textdomain( $domain, $global );

			} elseif ( file_exists( $local ) ) {

				// Look in local `../wp-content/plugins/{plugin-directory}/languages/` folder.
				load_textdomain( $domain, $local );

			} else {

				// Load the default language files
				load_plugin_textdomain( $domain, false, $languagesDirectory );
			}
		}

		/**
		 * Call back for the `wp_enqueue_scripts` action.
		 *
		 * Register and enqueue CSS and javascript files for frontend.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function enqueueScripts() {

			// If SCRIPT_DEBUG is set and TRUE load the non-minified JS files, otherwise, load the minified files.
			$min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			$js_vars = array();

			wp_register_style( 'ez-icomoon', EZ_TOC_URL . "vendor/icomoon/style$min.css", array(), ezTOC::VERSION );
			wp_register_style( 'ez-toc', EZ_TOC_URL . "assets/css/screen$min.css", array( 'ez-icomoon' ), ezTOC::VERSION );

			wp_register_script( 'js-cookie', EZ_TOC_URL . "vendor/js-cookie/js.cookie$min.js", array(), '2.2.1', TRUE );
			wp_register_script( 'jquery-smooth-scroll', EZ_TOC_URL . "vendor/smooth-scroll/jquery.smooth-scroll$min.js", array( 'jquery' ), '2.2.0', TRUE );
			wp_register_script( 'jquery-sticky-kit', EZ_TOC_URL . "vendor/sticky-kit/jquery.sticky-kit$min.js", array( 'jquery' ), '1.9.2', TRUE );

			wp_register_script(
				'ez-toc-js',
				EZ_TOC_URL . "assets/js/front{$min}.js",
				array( 'jquery-smooth-scroll', 'js-cookie', 'jquery-sticky-kit' ),
				ezTOC::VERSION . '-' . filemtime( EZ_TOC_PATH . "/assets/js/front{$min}.js" ),
				true
			);

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				wp_enqueue_style( 'ez-toc' );
				self::inlineCSS();
			}

			if ( ezTOC_Option::get( 'smooth_scroll' ) ) {

				$js_vars['smooth_scroll'] = true;
			}

			//wp_enqueue_script( 'ez-toc-js' );

			if ( ezTOC_Option::get( 'show_heading_text' ) && ezTOC_Option::get( 'visibility' ) ) {

				$width = ezTOC_Option::get( 'width' ) !== 'custom' ? ezTOC_Option::get( 'width' ) : ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );

				$js_vars['visibility_hide_by_default'] = ezTOC_Option::get( 'visibility_hide_by_default' ) ? true : false;

				$js_vars['width'] = esc_js( $width );
			}

			$offset = wp_is_mobile() ? ezTOC_Option::get( 'mobile_smooth_scroll_offset', 0 ) : ezTOC_Option::get( 'smooth_scroll_offset', 30 );

			$js_vars['scroll_offset'] = esc_js( $offset );

			if ( ezTOC_Option::get( 'widget_affix_selector' ) ) {

				$js_vars['affixSelector'] = ezTOC_Option::get( 'widget_affix_selector' );
			}

			if ( 0 < count( $js_vars ) ) {

				wp_localize_script( 'ez-toc-js', 'ezTOC', $js_vars );
			}
		}

		/**
		 * Prints out inline CSS after the core CSS file to allow overriding core styles via options.
		 *
		 * @access private
		 * @since  1.0
		 * @static
		 */
		public static function inlineCSS() {

			$css = '';

			if ( ! ezTOC_Option::get( 'exclude_css' ) ) {

				$css .= 'div#ez-toc-container p.ez-toc-title {font-size: ' . ezTOC_Option::get( 'title_font_size', 120 ) . ezTOC_Option::get( 'title_font_size_units', '%' ) . ';}';
				$css .= 'div#ez-toc-container p.ez-toc-title {font-weight: ' . ezTOC_Option::get( 'title_font_weight', 500 ) . ';}';
				$css .= 'div#ez-toc-container ul li {font-size: ' . ezTOC_Option::get( 'font_size' ) . ezTOC_Option::get( 'font_size_units' ) . ';}';

				if ( ezTOC_Option::get( 'theme' ) === 'custom' || ezTOC_Option::get( 'width' ) != 'auto' ) {

					$css .= 'div#ez-toc-container {';

					if ( ezTOC_Option::get( 'theme' ) === 'custom' ) {

						$css .= 'background: ' . ezTOC_Option::get( 'custom_background_colour' ) . ';border: 1px solid ' . ezTOC_Option::get( 'custom_border_colour' ) . ';';
					}

					if ( 'auto' !== ezTOC_Option::get( 'width' ) ) {

						$css .= 'width: ';

						if ( 'custom' !== ezTOC_Option::get( 'width' ) ) {

							$css .= ezTOC_Option::get( 'width' );

						} else {

							$css .= ezTOC_Option::get( 'width_custom' ) . ezTOC_Option::get( 'width_custom_units' );
						}

						$css .= ';';
					}

					$css .= '}';
				}

				if ( 'custom' === ezTOC_Option::get( 'theme' ) ) {

					$css .= 'div#ez-toc-container p.ez-toc-title {color: ' . ezTOC_Option::get( 'custom_title_colour' ) . ';}';
					//$css .= 'div#ez-toc-container p.ez-toc-title a,div#ez-toc-container ul.ez-toc-list a {color: ' . ezTOC_Option::get( 'custom_link_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a {color: ' . ezTOC_Option::get( 'custom_link_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:hover {color: ' . ezTOC_Option::get( 'custom_link_hover_colour' ) . ';}';
					$css .= 'div#ez-toc-container ul.ez-toc-list a:visited {color: ' . ezTOC_Option::get( 'custom_link_visited_colour' ) . ';}';
				}
			}

			if ( $css ) {

				wp_add_inline_style( 'ez-toc', $css );
			}
		}

		/**
		 * Array search deep.
		 *
		 * Search an array recursively for a value.
		 *
		 * @link https://stackoverflow.com/a/5427665/5351316
		 *
		 * @param        $search
		 * @param array  $array
		 * @param string $mode
		 *
		 * @return bool
		 */
		public static function array_search_deep( $search, array $array, $mode = 'value' ) {

			foreach ( new RecursiveIteratorIterator( new RecursiveArrayIterator( $array ) ) as $key => $value ) {

				if ( $search === ${${"mode"}} ) {
					return true;
				}
			}

			return false;
		}

		/**
		 * Returns true if the table of contents is eligible to be printed, false otherwise.
		 *
		 * NOTE: Must bve use only within the loop.
		 *
		 * @access public
		 * @since  1.0
		 * @static
		 *
		 * @param WP_Post $post
		 *
		 * @return bool
		 */
		public static function is_eligible( $post ) {

			//global $wp_current_filter;

			if ( empty( $post ) || ! $post instanceof WP_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', $post );
				return false;
			}

			// This can likely be removed since it is checked in maybeApplyTheContentFilter().
			// Do not execute if root filter is one of those in the array.
			//if ( in_array( $wp_current_filter[0], array( 'get_the_excerpt', 'wp_head' ), true ) ) {
			//
			//	return false;
			//}

			if ( has_shortcode( $post->post_content, apply_filters( 'ez_toc_shortcode', 'toc' ) ) ||
			     has_shortcode( $post->post_content, 'ez-toc' ) ) {

				Debug::log( 'has_ez_toc_shortcode', 'Has instance of shortcode.', true );
				return true;
			}

			if ( is_front_page() && ! ezTOC_Option::get( 'include_homepage' ) ) {

				Debug::log( 'is_front_page', 'Is frontpage, TOC is not enabled.', false );
				return false;
			}

			$type = get_post_type( $post->ID );

			Debug::log( 'current_post_type', 'Post type is.', $type );

			$enabled = in_array( $type, ezTOC_Option::get( 'enabled_post_types', array() ), true );
			$insert  = in_array( $type, ezTOC_Option::get( 'auto_insert_post_types', array() ), true );

			Debug::log( 'is_supported_post_type', 'Is supported post type?', $enabled );
			Debug::log( 'is_auto_insert_post_type', 'Is auto insert for post types?', $insert );

			if ( $insert || $enabled ) {

				if ( ezTOC_Option::get( 'restrict_path' ) ) {

					/**
					 * @link https://wordpress.org/support/topic/restrict-path-logic-does-not-work-correctly?
					 */
					if ( false !== strpos( ezTOC_Option::get( 'restrict_path' ), $_SERVER['REQUEST_URI'] ) ) {

						Debug::log( 'is_restricted_path', 'In restricted path, post not eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return false;

					} else {

						Debug::log( 'is_not_restricted_path', 'Not in restricted path, post is eligible.', ezTOC_Option::get( 'restrict_path' ) );
						return true;
					}

				} else {

					if ( $insert && 1 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_disable_post_meta', 'Auto insert enabled and disable TOC is enabled in post meta.', false );
						return false;

					} elseif ( $insert && 0 === (int) get_post_meta( $post->ID, '_ez-toc-disabled', true ) ) {

						Debug::log( 'is_auto_insert_enabled_post_meta', 'Auto insert enabled and disable TOC is not enabled in post meta.', true );
						return true;

					} elseif ( $enabled && 1 === (int) get_post_meta( $post->ID, '_ez-toc-insert', true ) ) {

						Debug::log( 'is_supported_post_type_disable_insert_post_meta', 'Supported post type and insert TOC is enabled in post meta.', true );
						return true;

					} elseif ( $enabled && $insert ) {

						Debug::log( 'supported_post_type_and_auto_insert', 'Supported post type and auto insert TOC is enabled.', true );
						return true;
					}

					Debug::log( 'not_auto_insert_or_not_supported_post_type', 'Not supported post type or insert TOC is disabled.', false );
					return false;
				}

			} else {

				Debug::log( 'not_auto_insert_and_not_supported post_type', 'Not supported post type and do not auto insert TOC.', false );
				return false;
			}
		}

		/**
		 * Get TOC from store and if not in store process post and add it to the store.
		 *
		 * @since 2.0
		 *
		 * @param int $id
		 *
		 * @return ezTOC_Post|null
		 */
		public static function get( $id ) {

			$post = null;

			if ( isset( self::$store[ $id ] ) && self::$store[ $id ] instanceof ezTOC_Post ) {

				$post = self::$store[ $id ];

			} else {

				$post = ezTOC_Post::get( get_the_ID() );

				if ( $post instanceof ezTOC_Post ) {

					self::$store[ $id ] = $post;
				}
			}

			return $post;
		}

		/**
		 * Callback for the registered shortcode `[ez-toc]`
		 *
		 * NOTE: Shortcode is run before the callback @see ezTOC::the_content() for the `the_content` filter
		 *
		 * @access private
		 * @since  1.3
		 *
		 * @param array|string $atts    Shortcode attributes array or empty string.
		 * @param string       $content The enclosed content (if the shortcode is used in its enclosing form)
		 * @param string       $tag     Shortcode name.
		 *
		 * @return string
		 */
		public static function shortcode( $atts, $content, $tag ) {

			static $run = true;
			$html = '';

			if ( $run ) {

				$post = self::get( get_the_ID() );

				if ( ! $post instanceof ezTOC_Post ) {

					Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );

					return Debug::log()->appendTo( $content );
				}

				$html = $post->getTOC();
				$run  = false;
			}

			return $html;
		}

		/**
		 * Whether or not apply `the_content` filter.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		private static function maybeApplyTheContentFilter() {

			$apply = true;

			global $wp_current_filter;

			// Do not execute if root current filter is one of those in the array.
			if ( in_array( $wp_current_filter[0], array( 'get_the_excerpt', 'init', 'wp_head' ), true ) ) {

				$apply = false;
			}

			// bail if feed, search or archive
			if ( is_feed() || is_search() || is_archive() ) {

				$apply = false;
			}

			/**
			 * Whether or not to apply `the_content` filter callback.
			 *
			 * @see ezTOC::the_content()
			 *
			 * @since 2.0
			 *
			 * @param bool $apply
			 */
			return apply_filters( 'ez_toc_maybe_apply_the_content_filter', $apply );
		}

		/**
		 * Callback for the `the_content` filter.
		 *
		 * This will add the inline table of contents page anchors to the post content. It will also insert the
		 * table of contents inline with the post content as defined by the user defined preference.
		 *
		 * @since 1.0
		 *
		 * @param string $content
		 *
		 * @return string
		 */
		public static function the_content( $content ) {

			$maybeApplyFilter = self::maybeApplyTheContentFilter();

			Debug::log( 'the_content_filter', 'The `the_content` filter applied.', $maybeApplyFilter );

			if ( ! $maybeApplyFilter ) {

				return Debug::log()->appendTo( $content );
			}

			// Bail if post not eligible and widget is not active.
			$isEligible = self::is_eligible( get_post() );

			Debug::log( 'post_eligible', 'Post eligible.', $isEligible );

			if ( ! $isEligible && ! is_active_widget( false, false, 'ezw_tco' ) ) {

				return Debug::log()->appendTo( $content );
			}

			$post = self::get( get_the_ID() );

			if ( ! $post instanceof ezTOC_Post ) {

				Debug::log( 'not_instance_of_post', 'Not an instance if `WP_Post`.', get_the_ID() );

				return Debug::log()->appendTo( $content );
			}

			// Bail if no headings found.
			if ( ! $post->hasTOCItems() ) {

				return Debug::log()->appendTo( $content );
			}

			$find    = $post->getHeadings();
			$replace = $post->getHeadingsWithAnchors();
			$toc     = $post->getTOC();

			$headings = implode( PHP_EOL, $find );
			$anchors  = implode( PHP_EOL, $replace );

			$headingRows = count( $find ) + 1;
			$anchorRows  = count( $replace ) + 1;

			$style = "background-image: linear-gradient(#F1F1F1 50%, #F9F9F9 50%); background-size: 100% 4em; border: 1px solid #CCC; font-family: monospace; font-size: 1em; line-height: 2em; margin: 0 auto; overflow: auto; padding: 0 8px 4px; white-space: nowrap; width: 100%;";

			Debug::log(
				'found_post_headings',
				'Found headings:',
				"<textarea rows='{$headingRows}' style='{$style}' wrap='soft'>{$headings}</textarea>"
			);

			Debug::log(
				'replace_post_headings',
				'Replace found headings with:',
				"<textarea rows='{$anchorRows}' style='{$style}' wrap='soft'>{$anchors}</textarea>"
			);

			// If shortcode used or post not eligible, return content with anchored headings.
			if ( strpos( $content, 'ez-toc-container' ) || ! $isEligible ) {

				Debug::log( 'shortcode_found', 'Shortcode found, add links to content.', true );

				return mb_find_replace( $find, $replace, $content );
			}

			$position = ezTOC_Option::get( 'position' );

			Debug::log( 'toc_insert_position', 'Insert TOC at position', $position );

			// else also add toc to content
			switch ( $position ) {

				case 'top':
					$content = $toc . mb_find_replace( $find, $replace, $content );
					break;

				case 'bottom':
					$content = mb_find_replace( $find, $replace, $content ) . $toc;
					break;

				case 'after':
					$replace[0] = $replace[0] . $toc;
					$content    = mb_find_replace( $find, $replace, $content );
					break;

				case 'before':
				default:
					//$replace[0] = $html . $replace[0];
					$content    = mb_find_replace( $find, $replace, $content );

					/**
					 * @link https://wordpress.org/support/topic/php-notice-undefined-offset-8/
					 */
					if ( ! array_key_exists( 0, $replace ) ) {
						break;
					}

					$pattern = '`<h[1-6]{1}[^>]*' . preg_quote( $replace[0], '`' ) . '`msuU';
					$result  = preg_match( $pattern, $content, $matches );

					/*
					 * Try to place TOC before the first heading found in eligible heading, failing that,
					 * insert TOC at top of content.
					 */
					if ( 1 === $result ) {

						Debug::log( 'toc_insert_position_found', 'Insert TOC before first eligible heading.', $result );

						$start   = strpos( $content, $matches[0] );
						$content = substr_replace( $content, $toc, $start, 0 );

					} else {

						Debug::log( 'toc_insert_position_not_found', 'Insert TOC before first eligible heading not found.', $result );

						// Somehow, there are scenarios where the processing get this far and
						// the TOC is being added to pages where it should not. Disable for now.
						//$content = $html . $content;
					}
			}

			return Debug::log()->appendTo( $content );
		}

	}

	/**
	 * The main function responsible for returning the Easy Table of Contents instance to functions everywhere.
	 *
	 * Use this function like you would a global variable, except without needing to declare the global.
	 *
	 * Example: <?php $instance = ezTOC(); ?>
	 *
	 * @access public
	 * @since  1.0
	 *
	 * @return ezTOC
	 */
	function ezTOC() {

		return ezTOC::instance();
	}

	// Start Easy Table of Contents.
	add_action( 'plugins_loaded', 'ezTOC' );
}
