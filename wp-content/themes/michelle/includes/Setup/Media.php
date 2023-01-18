<?php
/**
 * Media setup component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since  1.0.0
 */

namespace WebManDesign\Michelle\Setup;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Media implements Component_Interface {

	/**
	 * Initialization.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Processing

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::after_setup_theme' );
				add_action( 'after_setup_theme', __CLASS__ . '::add_image_size' );

				add_action( 'admin_init', __CLASS__ . '::image_sizes_notice_display' );

			// Filters

				add_filter( 'image_size_names_choose', __CLASS__ . '::image_sizes_select' );

				add_filter( 'michelle/setup/media/get_image_sizes', __CLASS__ . '::set_image_sizes' );

	} // /init

	/**
	 * After setup theme.
	 *
	 * @link  https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function after_setup_theme() {

		// Processing

			add_theme_support( 'post-thumbnails' );
			add_theme_support( 'post-thumbnails', array(
				'attachment:audio',
				'attachment:video',
			) );

	} // /after_setup_theme

	/**
	 * Get theme image sizes setup array.
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get_image_sizes(): array {

		// Variables

			/**
			 * Filters image sizes setup array.
			 *
			 * Array key stands for image registration ID.
			 * Array values consist of single image size setup array.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $image_sizes
			 */
			$image_sizes = (array) apply_filters( 'michelle/setup/media/get_image_sizes', array() );


		// Processing

			$image_sizes = array_map(
				function( $args = array() ) {
					// Parsing image size setup args.
					$args = wp_parse_args( (array) $args, array(
						'name'        => '', // Human readable image size name.
						'description' => '', // Human readable image size description.
						'width'       => 100,
						'height'      => 100,
						'crop'        => false,
					) );

					if ( ! empty( $args['name'] ) ) {
						return $args;
					}
				},
				$image_sizes
			);

			$image_sizes = array_filter( $image_sizes );


		// Output

			return $image_sizes;

	} // /get_image_sizes

	/**
	 * Setting image sizes.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $image_sizes
	 *
	 * @return  array
	 */
	public static function set_image_sizes( array $image_sizes ): array {

		// Variables

			global $content_width;

			$typography_size_html   = get_theme_mod( 'typography_size_html', 20 );
			$entry_content_width    = get_theme_mod( 'layout_width_entry_content', 640 );
			$thumbnail_aspect_ratio = (string) get_theme_mod( 'thumbnail_aspect_ratio', '3:2' );

			$thumbnail_size = array(
				absint( ( $content_width - 2 * $typography_size_html ) / 2 ),
				0,
				false,
			);

			if ( stripos( $thumbnail_aspect_ratio, ':' ) ) {
				$ratio = explode( ':', $thumbnail_aspect_ratio );

				$width  = absint( ( $content_width - 2 * $typography_size_html ) / 2 );
				$height = absint( $width / absint( $ratio[0] ) * absint( $ratio[1] ) );

				$thumbnail_size = array( $width, $height, true );
			}


		// Processing

			$image_sizes = array(

				'thumbnail' => array(
					'name'        => esc_html_x( 'Thumbnail', 'WordPress predefined image size name.', 'michelle' ),
					'description' => esc_html__( 'Not used by the theme.', 'michelle' ),
					'width'       => 480,
					'height'      => 0,
					'crop'        => false,
				),

				'medium' => array(
					'name'        => esc_html_x( 'Medium', 'WordPress predefined image size name.', 'michelle' ),
					'description' => esc_html__( 'In image attachment page preview.', 'michelle' ),
					'width'       => absint( $entry_content_width ),
					'height'      => 0,
					'crop'        => false,
				),

				'large' => array(
					'name'        => esc_html_x( 'Large', 'WordPress predefined image size name.', 'michelle' ),
					'description' => esc_html__( 'In featured posts.', 'michelle' ),
					'width'       => absint( $content_width ),
					'height'      => 0,
					'crop'        => false,
				),

				'michelle-thumbnail' => array(
					'name'        => esc_html_x( 'Post thumbnail', 'Image size name', 'michelle' ),
					'description' => esc_html__( 'In posts list.', 'michelle' ) . ' <a href="' . esc_url( admin_url( 'customize.php?autofocus[control]=thumbnail_aspect_ratio' ) ) . '">' . esc_html__( 'Change this image &rarr;', 'michelle' ) . '</a>',
					'width'       => $thumbnail_size[0],
					'height'      => $thumbnail_size[1],
					'crop'        => $thumbnail_size[2],
				),

			);


		// Output

			return $image_sizes;

	} // /set_image_sizes

	/**
	 * What are default WordPress image sizes?
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get_default_image_sizes(): array {

		// Output

			return array( 'thumbnail', 'medium', 'medium_large', 'large' );

	} // /get_default_image_sizes

	/**
	 * Add custom image sizes.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function add_image_size() {

		// Variables

			$image_sizes = self::get_image_sizes();
			$predefined  = self::get_default_image_sizes();


		// Processing

			foreach ( $image_sizes as $size => $args ) {
				if ( in_array( $size, $predefined ) ) {
					continue;
				}

				add_image_size(
					$size,
					$args['width'],
					$args['height'],
					$args['crop']
				);
			}

	} // /add_image_size

	/**
	 * Adding custom image sizes to image size selector.
	 *
	 * @since  1.0.0
	 *
	 * @param  array $sizes
	 *
	 * @return  array
	 */
	public static function image_sizes_select( array $sizes ): array {

		// Variables

			$image_sizes = self::get_image_sizes();
			$predefined  = self::get_default_image_sizes();


		// Processing

			foreach ( $image_sizes as $size => $args ) {
				if ( in_array( $size, $predefined ) || ! isset( $args[3] ) ) {
					continue;
				}

				$sizes[ $size ] = esc_html( $args[3] );
			}


		// Output

			return $sizes;

	} // /image_sizes_select

	/**
	 * Register recommended image sizes notice.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function image_sizes_notice_display() {

		// Processing

			add_settings_field(
				'recommended-image-sizes',
				'',
				__CLASS__ . '::image_sizes_notice_content',
				'media',
				'default',
				array()
			);

			register_setting(
				'media',
				'recommended-image-sizes',
				'esc_attr'
			);

	} // /image_sizes_notice_display

	/**
	 * Display recommended image sizes notice.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function image_sizes_notice_content() {

		// Processing

			get_template_part( 'templates/parts/admin/media', 'image-sizes' );

	} // /image_sizes_notice_content

}
