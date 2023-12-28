<?php
/**
 * Featured posts component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Loop;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Customize\Mod;
use WP_Customize_Manager;
use WP_Query;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Featured_Posts implements Component_Interface {

	/**
	 * Soft cache for queried featured posts.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $posts = array();

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'customize_register', __CLASS__ . '::option_pointers' );

				add_filter( 'body_class', __CLASS__ . '::body_class', 99 );

				add_action( 'pre_get_posts', __CLASS__ . '::pre_get_posts' );

				add_action( 'tha_content_while_before', __CLASS__ . '::set_image' );
				add_action( 'tha_content_while_after',  __CLASS__ . '::set_image' );

				add_action( 'michelle/postslist/before', __CLASS__ . '::display' );

			// Filters

				add_filter( 'michelle/customize/options/get', __CLASS__ . '::options' );

	} // /init

	/**
	 * Display posts.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function display() {

		// Requirements

			if (
				! is_home()
				|| is_paged()
			) {
				return;
			}


		// Output

			get_template_part( 'templates/parts/loop/loop', 'featured-posts' );

	} // /display

	/**
	 * Gets featured posts.
	 *
	 * @since  1.0.0
	 *
	 * @return  array|WP_Query
	 */
	public static function get_posts() {

		// Variables

			$tag = Mod::get( 'featured_posts_tag' );


		// Processing

			if (
				empty( self::$posts )
				&& ! empty( $tag )
			) {

				/**
				 * Filters featured posts query arguments.
				 *
				 * @since  1.0.0
				 *
				 * @param  array  $args
				 * @param  string $tag   Featured posts tag slug.
				 */
				$args = (array) apply_filters( 'michelle/loop/featured_posts/get_posts', array(
					'post_type'           => 'post',
					'tag'                 => $tag,
					'posts_per_page'      => 3,
					'no_found_rows'       => true,
					'ignore_sticky_posts' => true,
					'post_status'         => 'publish',
				), $tag );

				self::$posts = new WP_Query( $args );
			}


		// Output

			return self::$posts;

	} // /get_posts

	/**
	 * Sets featured post fallback media.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $context
	 *
	 * @return  void
	 */
	public static function set_image( $context = '' ) {

		// Requirements check

			if ( 'featured' !== $context ) {
				return;
			}


		// Processing

			if ( doing_action( 'tha_content_while_before' ) ) {
				add_filter( 'michelle/entry/media/get_image_size', __CLASS__ . '::get_image_size' );
				add_filter( 'pre/michelle/entry/media/display', __CLASS__ . '::get_media_fallback' );
			} else {
				remove_filter( 'pre/michelle/entry/media/display', __CLASS__ . '::get_media_fallback' );
				remove_filter( 'michelle/entry/media/get_image_size', __CLASS__ . '::get_image_size' );
			}

	} // /set_image

	/**
	 * Gets featured post fallback media image size.
	 *
	 * @since  1.0.0
	 *
	 * @return  string
	 */
	public static function get_image_size(): string {

		// Output

			/**
			 * Filters featured posts image size.
			 *
			 * @since  1.0.0
			 *
			 * @param  string $image_size
			 */
			return (string) apply_filters( 'michelle/loop/featured_posts/get_image_size', 'large' );

	} // /get_image_size

	/**
	 * Gets featured post fallback media.
	 *
	 * @since  1.0.0
	 *
	 * @param  mixed $pre
	 *
	 * @return  mixed
	 */
	public static function get_media_fallback( $pre ) {

		// Requirements check

			if ( has_post_thumbnail() ) {
				return $pre;
			}


		// Variables

			$image_id = absint( Mod::get( 'featured_posts_image' ) );


		// Processing

			if ( ! empty( $image_id ) ) {
				return
					'<div class="entry-media">'
					. '<a href="' . esc_url( get_permalink() ) . '">'
					. wp_get_attachment_image( $image_id, self::get_image_size() )
					. '</a>'
					. '</div>';
			}


		// Output

			return $pre;

	} // /get_media_fallback

	/**
	 * Theme options.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $options
	 *
	 * @return  array
	 */
	public static function options( array $options ): array {

		// Variables

			$tags = get_tags( array(
				'hide_empty' => false,
				'fields'     => 'slugs',
			) );
			if ( is_wp_error( $tags ) ) {
				$tags = array();
			}


		// Processing

			if ( ! isset( $options[ 400 . 'posts' ] ) ) {
				$options[ 400 . 'posts' ] = array(
					'id'             => 'posts',
					'type'           => 'section',
					'create_section' => esc_html_x( 'Blog', 'Customizer section title.', 'michelle' ),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				);
			}

			$options[ 400 . 'posts' . 200 ] = array(
				'type'    => 'html',
				'content' =>
					'<h3>'
					. esc_html__( 'Featured posts', 'michelle' )
					. '</h3>',
			);

			$options[ 400 . 'posts' . 210 ] = array(
				'type'              => 'text',
				'id'                => 'featured_posts_tag',
				'label'             => esc_html__( 'Featured posts tag slug', 'michelle' ),
				'description'       => esc_html__( '3 latest posts assigned to this tag will be displayed as featured posts on blog page.', 'michelle' ) . ' (<a href="' . esc_url_raw( admin_url( 'edit-tags.php?taxonomy=post_tag' ) ) . '" target="_blank"  rel="noopener noreferrer">' . esc_html__( 'Open tags manager in a new window now &rarr;', 'michelle' ) . '</a>)',
				'default'           => '',
				'datalist'          => $tags,
				'sanitize_callback' => 'esc_attr',
				'input_attrs'       => array(
					'placeholder' => esc_attr_x( 'featured', 'Form field placeholder. URL slug, no diacritic.', 'michelle' ),
				),
			);

			$options[ 400 . 'posts' . 220 ] = array(
				'type'        => 'checkbox',
				'id'          => 'featured_posts_remove_from_blog',
				'label'       => esc_html__( 'Remove from blog posts', 'michelle' ),
				'description' => esc_html__( 'Makes sure the featured posts will be removed from posts list on blog page.', 'michelle' ),
				'default'     => false,
			);

			$options[ 400 . 'posts' . 230 ] = array(
				'type'        => 'image',
				'id'          => 'featured_posts_image',
				'label'       => esc_html__( 'Fallback image', 'michelle' ),
				'description' => esc_html__( 'This image will be displayed when post has no featured image set.', 'michelle' ),
				'return'      => 'id',
				'default'     => 0,
			);


		// Output

			return $options;

	} // /options

	/**
	 * Setup customizer partial refresh pointers.
	 *
	 * @since  1.0.0
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function option_pointers( WP_Customize_Manager $wp_customize ) {

		// Processing

			$wp_customize->selective_refresh->add_partial( 'featured_posts_tag', array(
				'selector' => '.featured-posts-section',
			) );

	} // /option_pointers

	/**
	 * Remove featured posts from blog posts list.
	 *
	 * This has to be enabled in theme options.
	 *
	 * @since  1.0.0
	 *
	 * @param  WP_Query $query
	 *
	 * @return  void
	 */
	public static function pre_get_posts( WP_Query $query ) {

		// Requirements check

			if (
				! $query->is_home()
				|| ! $query->is_main_query()
				|| empty( Mod::get( 'featured_posts_remove_from_blog' ) )
			) {
				return;
			}


		// Variables

			$featured = self::get_posts();


		// Processing

			if ( ! empty( $featured ) ) {
				$query->set( 'post__not_in', wp_list_pluck( $featured->posts, 'ID' ) );
			}

	} // /pre_get_posts

	/**
	 * HTML body classes.
	 *
	 * @since  1.3.0
	 *
	 * @param  array $classes
	 *
	 * @return  array
	 */
	public static function body_class( array $classes ): array {

		// Processing

			if (
				is_home()
				&& ! is_paged()
				&& ! empty( self::get_posts() )
			) {
				$classes[] = 'has-featured-posts';
			}


		// Output

			return $classes;

	} // /body_class

}
