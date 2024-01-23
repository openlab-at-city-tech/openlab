<?php
/**
 * Block patterns component.
 *
 * For reference:
 *   Default pattern categories in WordPress:
 *   - buttons
 *   - columns
 *   - gallery
 *   - header
 *   - text
 *   Additional WordPress.com pattern categories:
 *   - featured
 *   - about
 *   - blog
 *   - call-to-action
 *   - coming-soon
 *   - contact
 *   - images
 *   - link-in-bio
 *   - list
 *   - media
 *   - podcast
 *   - portfolio
 *   - quotes
 *   - subscribe
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Content;

use WebManDesign\Michelle\Component_Interface;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Block_Patterns implements Component_Interface {

	/**
	 * Theme prefix for categories and patterns registration.
	 *
	 * @since   1.3.0
	 * @access  private
	 * @var     string
	 */
	private static $prefix = 'michelle/';

	/**
	 * Lists pattern setup arrays.
	 *
	 * @since   1.0.0
	 * @access  private
	 * @var     array
	 */
	private static $pattern_args = array();

	/**
	 * List of predefined pattern categories in WordPress.
	 *
	 * @since   1.3.0
	 * @access  private
	 * @var     array
	 */
	private static $default_cats = array( 'buttons', 'columns', 'gallery', 'header', 'text' );

	/**
	 * Fallback theme pattern category..
	 *
	 * @since   1.3.0
	 * @access  private
	 * @var     string
	 */
	private static $fallback_cat = 'michelle';

	/**
	 * Initialization.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function init() {

		// Requirements check

			if ( ! function_exists( 'register_block_pattern' ) ) {
				return;
			}


		// Processing

			// Actions

				add_action( 'after_setup_theme', __CLASS__ . '::remove_core_patterns' );
				add_action( 'after_setup_theme', __CLASS__ . '::register_categories' );
				add_action( 'after_setup_theme', __CLASS__ . '::register' );

	} // /init

	/**
	 * Remove core block patterns.
	 *
	 * @since  1.0.0
	 *
	 * @return  void
	 */
	public static function remove_core_patterns() {

		// Processing

			remove_theme_support( 'core-block-patterns' );

	} // /remove_core_patterns

	/**
	 * Register block patterns.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  void
	 */
	public static function register() {

		// Variables

			global $content_width;

			$patterns_hierarchy = self::get_pattern_ids();


		// Processing

			foreach ( $patterns_hierarchy as $category => $patterns ) {
				foreach ( $patterns as $id ) {

					// Fallback category files are not in a subfolder.
					if ( self::$fallback_cat !== $category ) {
						$id = $category . '/' . $id;
					}

					ob_start();
					get_template_part( 'templates/parts/block/pattern/' . $id );
					$content = ob_get_clean();

					// Why bother if we have no pattern setup args?
					if ( empty( self::$pattern_args[ $id ] ) ) {
						continue;
					}

					$args = wp_parse_args(
						self::$pattern_args[ $id ],
						array(
							'title'         => '',
							'content'       => trim( $content ),
							'categories'    => null,
							'blockTypes'    => array(),
							'viewportWidth' => ( stripos( $content, 'alignfull' ) ) ? ( 1920 ) : ( absint( $content_width * 1.25 ) ),
						)
					);

					// Why bother if we have no content or title?
					if (
						empty( $args['content'] )
						|| empty( $args['title'] )
					) {
						continue;
					}

					$args['title'] = esc_html( $args['title'] );

					// Automatic categories.
					if ( empty( $args['categories'] ) ) {
						if ( $category ) {
							$args['categories'] = array( $category );
						} else {
							$args['categories'] = array( self::$fallback_cat ); // Fallback category.
						}
					}
					$args['categories'] = array_map( function( $category ) {
						if (
							self::$fallback_cat === $category
							|| in_array( $category, self::$default_cats )
						) {
							return $category;
						} else {
							return self::$prefix . $category;
						}
					}, $args['categories'] );

					register_block_pattern( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern
						self::$prefix . $id,
						/**
						 * Filters array of block pattern registration arguments.
						 *
						 * @since  1.3.0
						 *
						 * @param  array  $args  Block pattern registration arguments.
						 * @param  string $id    Block pattern registration ID.
						 */
						(array) apply_filters( 'michelle/content/block_patterns/register/args', $args, $id )
					);
				}
			}

	} // /register

	/**
	 * Register custom block pattern categories.
	 *
	 * @since  1.3.0
	 *
	 * @return  void
	 */
	public static function register_categories() {

		// Requirements check

			if ( ! function_exists( 'register_block_pattern_category' ) ) {
				return;
			}


		// Variables

			$categories = array(
				'cta'        => _x( 'Call to Action', 'Block pattern category label.', 'michelle' ),
				'contact'    => _x( 'Contact', 'Block pattern category label.', 'michelle' ),
				'cover'      => _x( 'Cover', 'Block pattern category label.', 'michelle' ),
				'list'       => _x( 'Lists', 'Block pattern category label.', 'michelle' ),
				'media-text' => _x( 'Media and Text', 'Block pattern category label.', 'michelle' ),
				'number'     => _x( 'Numbers', 'Block pattern category label.', 'michelle' ),
				'post'       => _x( 'Posts', 'Block pattern category label.', 'michelle' ),
				'price'      => _x( 'Prices', 'Block pattern category label.', 'michelle' ),
				'faq'        => _x( 'Question and Answer', 'Block pattern category label.', 'michelle' ),
				'quote'      => _x( 'Quotes', 'Block pattern category label.', 'michelle' ),
				'site'       => _x( 'Site', 'Website. Block pattern category label.', 'michelle' ),
				'team'       => _x( 'Team', 'Block pattern category label.', 'michelle' ),
			);


		// Processing

			// Add new categories with appropriate prefix.
			foreach ( $categories as $id => $label ) {
				register_block_pattern_category( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern_category
					self::$prefix . $id,
					array( 'label' => esc_html( $label ) )
				);
			}

			// Fallback category. Without prefix.
			register_block_pattern_category( // phpcs:ignore WPThemeReview.PluginTerritory.ForbiddenFunctions.editor_blocks_register_block_pattern_category
				self::$fallback_cat,
				array( 'label' => esc_html_x( 'Michelle theme', 'Block pattern category label.', 'michelle' ) )
			);

	} // /register_categories

	/**
	 * Gets array of block pattern IDs/slugs within categories to load.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @return  array
	 */
	public static function get_pattern_ids(): array {

		// Variables

			$pattern_ids = array(

				'contact' => array(
					'bg-1',
					'bg-2',
					'description',
					'image-middle',
					'image-side',
					'quote',
					'shadow',
					'social',
				),

				'cover' => array(
					'2-images',
					'bg-image',
					'bg-links',
					'bg-text-left',
					'centered-background',
					'color-image',
					'color-image-large',
					'fullscreen-bottom-text',
					'images',
					'overflow',
					'overlap',
				),

				'cta' => array(
					'bg',
					'box',
					'centered',
					'cta',
					'large',
					'quote',
					'simple',
				),

				'faq' => array(
					'question-answer',
				),

				'gallery' => array(
					'gallery-captions',
					'gallery-no-gap',
					'gallery-variable-with-description',
					'image-padding-left',
					'image-padding-right',
					'image-parallax',
					'logos',
				),

				'header' => array(
					'hidden-accessibly',
				),

				'list' => array(
					'features-bg-image',
					'features-bg-shadow',
					'features-center-icon',
					'features-fullwidth-bg',
					'features-large-image',
					'features-parallax',
					'features-shadow',
					'features-simple',
					'features-single-large',
				),

				'media-text' => array(
					'parallax',
					'with-image',
				),

				'number' => array(
					'steps',
				),

				'post' => array(
					'blog',
					'project-gallery',
					'project-image',
				),

				'price' => array(
					'cards',
					'columns-with-icons',
					'food-menu',
					'with-contact',
				),

				'quote' => array(
					'testimonial',
					'testimonial-bg',
					'testimonials',
				),

				'site' => array(
					'footer',
					'footer-form',
				),

				'team' => array(
					'team',
					'team-2',
				),

				'text' => array(
					'2-columns-text',
					'2-columns-wider-heading',
					'extra-hierarchy',
					'heading-columns',
					'large-lead',
					'with-description',
				),

			);


		// Output

			/**
			 * Filters array of block pattern IDs.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $pattern_ids
			 */
			return (array) apply_filters( 'michelle/content/block_patterns/get_pattern_ids', $pattern_ids );

	} // /get_pattern_ids

	/**
	 * Adds a block pattern setup array to list.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  string $file  Pattern setup file name/path.
	 * @param  array  $args  Pattern setup arguments.
	 *
	 * @return  void
	 */
	public static function add_pattern_args( string $file, array $args ) {

		// Variables

			$dir = basename( dirname( $file ) ) . '/';
			$id  = str_replace( 'pattern/', '', $dir . basename( $file, '.php' ) );


		// Processing

			self::$pattern_args[ $id ] = (array) $args;

	} // /add_pattern_args

}
