<?php
/**
 * Theme starter content.
 *
 * @link  https://make.wordpress.org/core/2016/11/30/starter-content-for-themes-in-4-7/
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.7
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Starter implements Component_Interface {

	/**
	 * Starter content array.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $content = array();

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Requirements check

			if ( ! is_customize_preview() ) {
				return;
			}


		// Processing

			// Loading

				self::attachments();
				self::pages();
				self::options();
				self::nav_menus();

			// Setup

				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme' );

	} // /init

	/**
	 * After setup theme.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			/**
			 * Filters theme starter content setup array.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $content  WordPress starter content setup array.
			 */
			self::$content = apply_filters( 'michelle/add_theme_support/starter-content', self::$content );

			if ( ! empty( self::$content ) ) {
				add_theme_support( 'starter-content', self::$content );
			}

	} // /after_setup_theme

	/**
	 * Get specific content.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $context
	 *
	 * @return  string
	 */
	public static function get_content( string $context ): string {

		// Processing

			ob_start();
			get_template_part( 'templates/parts/admin/content-starter', $context );


		// Output

			return trim( ob_get_clean() );

	} // /get_content

	/**
	 * Pages.
	 *
	 * @since    1.0.0
	 * @version  1.0.4
	 *
	 * @return  void
	 */
	public static function pages() {

		// Output

			self::$content['posts'] = array(

				'home' => array(
					'post_type'    => 'page',
					'post_title'   => esc_html_x( 'Home', 'Theme starter content', 'michelle' ),
					'post_content' => self::get_content( 'home' ),
					'post_excerpt' => self::get_content( 'excerpt' ),
					'template'     => 'templates/no-intro-header-overlaid-light.php',
				),

				'blog' => array(
					'post_type'    => 'page',
					'post_excerpt' => self::get_content( 'excerpt' ),
				),

				'about' => array(
					'post_type'    => 'page',
					'post_title'   => esc_html_x( 'About', 'Theme starter content', 'michelle' ),
					'post_content' => self::get_content( 'about' ),
					'post_excerpt' => self::get_content( 'excerpt' ),
					'template'     => 'templates/no-intro-header-overlaid-light.php',
				),

				'services' => array(
					'post_type'    => 'page',
					'post_title'   => esc_html_x( 'Services', 'Theme starter content', 'michelle' ),
					'post_content' => self::get_content( 'services' ),
					'post_excerpt' => self::get_content( 'excerpt' ),
					'template'     => 'templates/no-intro.php',
				),

				'faq' => array(
					'post_type'    => 'page',
					'post_title'   => esc_html_x( 'FAQ', 'Theme starter content', 'michelle' ),
					'post_content' => self::get_content( 'faq' ),
					'post_excerpt' => self::get_content( 'excerpt' ),
					'template'     => 'templates/no-intro-header-overlaid-dark.php',
				),

				'contact' => array(
					'post_type'    => 'page',
					'post_title'   => esc_html_x( 'Contact', 'Theme starter content', 'michelle' ),
					'post_content' => self::get_content( 'contact' ),
					'post_excerpt' => self::get_content( 'excerpt' ),
				),

			);

	} // /pages

	/**
	 * Navigational menus.
	 *
	 * @since    1.0.0
	 * @version  1.0.12
	 *
	 * @return  void
	 */
	public static function nav_menus() {

		// Output

			self::$content['nav_menus'] = array(

				'primary' => array(
					'name' => esc_html_x( 'Primary Menu', 'Theme starter content', 'michelle' ),
					'items' => array(

						'link_home',

						'link_about' => array(
							'title'     => esc_html_x( 'About', 'Theme starter content', 'michelle' ),
							'type'      => 'post_type',
							'object'    => 'page',
							'object_id' => '{{about}}',
						),

						'link_services' => array(
							'title'     => esc_html_x( 'Services', 'Theme starter content', 'michelle' ),
							'type'      => 'post_type',
							'object'    => 'page',
							'object_id' => '{{services}}',
						),

						'link_faq' => array(
							'title'     => esc_html_x( 'FAQ', 'Theme starter content', 'michelle' ),
							'type'      => 'post_type',
							'object'    => 'page',
							'object_id' => '{{faq}}',
						),

						'link_contact' => array(
							'title'     => esc_html_x( 'Contact', 'Theme starter content', 'michelle' ),
							'type'      => 'post_type',
							'object'    => 'page',
							'object_id' => '{{contact}}',
						),

						'link_blog' => array(
							'title'     => esc_html_x( 'Blog', 'Theme starter content', 'michelle' ),
							'type'      => 'post_type',
							'object'    => 'page',
							'object_id' => '{{blog}}',
						),

						'link_demo' => array(
							'title' => '<svg class="svg-icon" width="1.5em" height="1.5em" style="vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" viewBox="0 0 20 20"><path d="M10 4.4C3.439 4.4 0 9.232 0 10c0 .766 3.439 5.6 10 5.6c6.56 0 10-4.834 10-5.6c0-.768-3.44-5.6-10-5.6zm0 9.907c-2.455 0-4.445-1.928-4.445-4.307c0-2.379 1.99-4.309 4.445-4.309s4.444 1.93 4.444 4.309c0 2.379-1.989 4.307-4.444 4.307zM10 10c-.407-.447.663-2.154 0-2.154c-1.228 0-2.223.965-2.223 2.154s.995 2.154 2.223 2.154c1.227 0 2.223-.965 2.223-2.154c0-.547-1.877.379-2.223 0z"/></svg><span class="screen-reader-text">' . esc_html_x( 'Demo website', 'Theme starter content.', 'michelle' ) . '</span>',
							'url'   => 'https://themedemos.webmandesign.eu/michelle/',
						),

						'link_documentation' => array(
							'title' => '<svg class="svg-icon" width="1.5em" height="1.5em" style="vertical-align: middle;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" aria-hidden="true" viewBox="0 0 20 20"><path d="M10.595 5.196l.446 1.371a4.135 4.135 0 0 1 1.441-.795c.59-.192 1.111-.3 1.582-.362l-.43-1.323a9.465 9.465 0 0 0-1.58.368a5.25 5.25 0 0 0-1.459.741zm.927 2.855l.446 1.371a4.135 4.135 0 0 1 1.441-.795c.59-.192 1.111-.3 1.582-.362l-.43-1.323a9.465 9.465 0 0 0-1.58.368a5.21 5.21 0 0 0-1.459.741zm.928 2.854l.446 1.371a4.135 4.135 0 0 1 1.441-.795c.59-.192 1.111-.3 1.582-.362l-.43-1.323a9.465 9.465 0 0 0-1.58.368a5.21 5.21 0 0 0-1.459.741zm-7.062 2.172l.43 1.323a8.745 8.745 0 0 1 1.492-.636a4.141 4.141 0 0 1 1.633-.203l-.446-1.371a5.25 5.25 0 0 0-1.615.257a9.406 9.406 0 0 0-1.494.63zM3.533 7.368l.43 1.323a8.825 8.825 0 0 1 1.492-.636a4.141 4.141 0 0 1 1.633-.203L6.643 6.48a5.263 5.263 0 0 0-1.616.258a9.406 9.406 0 0 0-1.494.63zm.927 2.855l.43 1.323a8.745 8.745 0 0 1 1.492-.636a4.141 4.141 0 0 1 1.633-.203L7.57 9.335a5.25 5.25 0 0 0-1.615.257a9.417 9.417 0 0 0-1.495.631zm6.604-8.813a5.26 5.26 0 0 0-3.053 2.559a5.257 5.257 0 0 0-3.973-.275C1.515 4.514.069 6.321.069 6.321l4.095 12.587c.126.387.646.477.878.143c.499-.719 1.46-1.658 3.257-2.242c1.718-.558 2.969.054 3.655.578c.272.208.662.06.762-.268c.252-.827.907-2.04 2.61-2.593c1.799-.585 3.129-.389 3.956-.1c.385.134.75-.242.625-.629L15.819 1.203s-2.232-.612-4.755.207zm-.113 13.846a5.208 5.208 0 0 0-3.141.044c-1.251.406-2.127.949-2.699 1.404L1.866 6.722c.358-.358 1.187-1.042 2.662-1.521c1.389-.451 2.528-.065 3.279.378l3.144 9.677zm6.894-2.689c-.731-.032-1.759.044-3.01.451a5.205 5.205 0 0 0-2.567 1.81L9.124 5.151c.346-.8 1.04-1.782 2.43-2.233c1.474-.479 2.547-.413 3.047-.334l3.244 9.983z"/></svg><span class="screen-reader-text">' . esc_html_x( 'User manual', 'Theme starter content.', 'michelle' ) . '</span>',
							'url'   => 'https://webmandesign.github.io/docs/michelle',
						),

					),
				),

			);

	} // /nav_menus

	/**
	 * WordPress options.
	 *
	 * @since    1.0.0
	 * @version  1.0.5
	 *
	 * @return  void
	 */
	public static function options() {

		// Output

			self::$content['options'] = array(
				'show_on_front'       => 'page',
				'page_on_front'       => '{{home}}',
				'page_for_posts'      => '{{blog}}',
				'posts_per_page'      => 6,
				'custom_logo'         => '{{image-logo-dark}}',
				'custom_logo_light'   => '{{image-logo-light}}',
				'display_site_title'  => false,
			);

	} // /options

	/**
	 * Attachments.
	 *
	 * @since  1.0.4
	 *
	 * @return  void
	 */
	public static function attachments() {

		// Output

			self::$content['attachments'] = array(

				'image-logo-dark' => array(
					'file' => 'assets/images/logo-michelle-dark.png',
				),

				'image-logo-light' => array(
					'file' => 'assets/images/logo-michelle-light.png',
				),

			);

	} // /attachments

	/**
	 * Get starter content image URL.
	 *
	 * @since    1.0.0
	 * @version  1.2.0
	 *
	 * @param  string $filename
	 * @param  string $extension
	 *
	 * @return  string
	 */
	public static function get_image_url( string $filename = '', string $extension = 'png' ): string {

		// Output

			return add_query_arg(
				'ver',
				'v' . MICHELLE_THEME_VERSION,
				get_theme_file_uri( 'assets/images/starter/' . $filename . '.' . trim( $extension ) )
			);

	} // /get_image_url

	/**
	 * Get starter content texts.
	 *
	 * @since  1.3.0
	 *
	 * @param  string $scope
	 *
	 * @return  string
	 */
	public static function get_text( string $scope ): string {

		// Variables

			$output = '---';
			$scope  = explode( '/', $scope );

			/**
			 * Filters array of demo texts used in block patterns and starter content.
			 *
			 * @since  1.3.0
			 *
			 * @param  array $texts
			 */
			$texts = (array) apply_filters( 'michelle/demo_texts', array(

				// Basic texts:
					'xs' => _x( 'Some text', 'Demo text.', 'michelle' ),
					's'  => _x( 'Just a short sentence', 'Demo text.', 'michelle' ),
					'm'  => _x( 'Write your own copy text here', 'Demo text.', 'michelle' ),
					'l'  => _x( 'This is just a demo text you should overwrite', 'Demo text.', 'michelle' ),

				'title' => array(
					's' => _x( 'This is title', 'Demo text.', 'michelle' ),
					'm' => _x( 'Write some title text here', 'Demo text.', 'michelle' ),
					'l' => _x( 'The ideal length of the title text in here should be maybe a bit longer', 'Demo text.', 'michelle' ),
				),

				'contact' => array(
					'address' => _x( '123 Street Name<br>Cityname, 56789<br>COUNTRY', 'Demo text.', 'michelle' ),
					'email'   => _x( 'example@example.com', 'Demo text.', 'michelle' ),
					'phone'   => _x( '+1 (123) 456-7890', 'Demo text.', 'michelle' ),
				),

				'date' => array(
					'event'   => _x( '10:30 am on Monday, July 1, 2021', 'Demo text.', 'michelle' ),
					'weekday' => _x( 'Mon - Fri', 'Demo text. Week days.', 'michelle' ),
					'weekend' => _x( 'Sat - Sun', 'Demo text. Weekend days.', 'michelle' ),
					'mon'     => _x( 'Monday', 'Demo text.', 'michelle' ),
					'tue'     => _x( 'Tuesday', 'Demo text.', 'michelle' ),
					'wed'     => _x( 'Wednesday', 'Demo text.', 'michelle' ),
					'thu'     => _x( 'Thursday', 'Demo text.', 'michelle' ),
					'fri'     => _x( 'Friday', 'Demo text.', 'michelle' ),
					'sat'     => _x( 'Saturday', 'Demo text.', 'michelle' ),
					'sun'     => _x( 'Sunday', 'Demo text.', 'michelle' ),
				),

				'people' => array(
					'name' => _x( 'Name Surname', 'Demo text.', 'michelle' ),
					'job'  => _x( 'Designer', 'Demo text. A job title.', 'michelle' ),
				),

				// Others:
					'alt'    => _x( 'Image alternative description text', 'Demo text. Image alt text.', 'michelle' ),
					'button' => _x( 'Click here →', 'Demo text. Button label.', 'michelle' ),
					'price'  => _x( '$19', 'Demo text. Price.', 'michelle' ),

			) );


		// Processing

			foreach ( $scope as $category ) {
				if ( isset( $texts[ $category ] ) ) {
					$texts = $texts[ $category ];
				}
			}

			if ( is_string( $texts ) ) {
				$output = $texts;
			}


		// Output

			return $output;

	} // /get_text

	/**
	 * Echos starter content texts.
	 *
	 * @since    1.3.0
	 * @version  1.3.7
	 *
	 * @param  string/array $scope
	 * @param  string       $suffix
	 *
	 * @return  void
	 */
	public static function the_text( $scope, string $suffix = '' ) {

		// Variables

			$output = array();


		// Processing

			if ( is_string( $scope ) ) {

				// Get direct text defined with string scope.
				$output = array( self::get_text( $scope ) . $suffix );

			} elseif ( is_array( $scope ) ) {

				// Get all texts defined with multiple scopes in an array.
				foreach ( $scope as $text ) {
					$output[] = self::get_text( $text ) . $suffix;
				}

			} elseif ( is_numeric( $scope ) ) {

				// Get specific number of various length sentences.
				$sequence  = array( 's','l','m','l','m', 's','l','m','l','m', 's','l','m','l','m', 's','l','m','l','m', );
				$sentences = array_slice(
					$sequence,
					0,
					min( absint( $scope ), count( $sequence ) )
				);
				foreach ( $sentences as $text ) {
					$output[] = self::get_text( $text ) . $suffix;
				}

			}


		// Output

			echo wp_kses( trim( implode( ' ', $output ) ), 'inline' );

	} // /the_text

}
