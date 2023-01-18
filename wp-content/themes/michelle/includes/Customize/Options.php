<?php
/**
 * Theme options component.
 *
 * @package    Michelle
 * @copyright  WebMan Design, Oliver Juhas
 *
 * @since    1.0.0
 * @version  1.3.0
 */

namespace WebManDesign\Michelle\Customize;

use WebManDesign\Michelle\Component_Interface;
use WebManDesign\Michelle\Content\Block_Area;
use WebManDesign\Michelle\Setup\Media;
use WebManDesign\Michelle\Tool\Google_Fonts;
use WP_Customize_Manager;

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

class Options implements Component_Interface {

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

				add_action( 'customize_register', __CLASS__ . '::modify', 100 );

			// Filters

				add_filter( 'michelle/customize/options/get', __CLASS__ . '::set', 5 );

	} // /init

	/**
	 * Get theme options setup array.
	 *
	 * @since  1.0.0
	 *
	 * @return  array
	 */
	public static function get(): array {

		// Output

			/**
			 * Filters customizer theme options setup array.
			 *
			 * @since  1.0.0
			 *
			 * @param  array $options
			 */
			return (array) apply_filters( 'michelle/customize/options/get', array() );

	} // /get

	/**
	 * Modify native WordPress options and setup partial refresh pointers.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  WP_Customize_Manager $wp_customize
	 *
	 * @return  void
	 */
	public static function modify( WP_Customize_Manager $wp_customize ) {

		// Processing

			// Change options.
			$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';
			$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';

			// Custom background options.
			$cb_options = array(
				'color',
				'image',
				'preset',
				'position',
				'size',
				'repeat',
				'attachment',
			);
			foreach ( $cb_options as $prop ) {
				$wp_customize->get_control( 'background_' . $prop )->section = 'colors_general';
			}
			$wp_customize->get_control( 'background_color' )->priority = 10;

			// Option pointers only:

				// Post thumbnail.
				$wp_customize->selective_refresh->add_partial( 'thumbnail_aspect_ratio', array(
					'selector' => '#posts',
				) );

				// Error 404 content.
				$wp_customize->selective_refresh->add_partial( 'block_area_error_404', array(
					'selector' => '.error404 .site-content',
				) );

				// Footer content.
				$wp_customize->selective_refresh->add_partial( 'block_area_site_footer', array(
					'selector' => '.site-footer-section:first-child .site-footer-content',
				) );

	} // /modify

	/**
	 * Sets theme options array.
	 *
	 * @since    1.0.0
	 * @version  1.3.0
	 *
	 * @param  array $options
	 *
	 * @return  array
	 */
	public static function set( array $options = array() ): array {

		// Variables

			// Predefined font families.
			$font_families = array_filter( array_merge(
				Google_Fonts::get_suggested_fonts(),
				array(
					'system',
					'sans-serif',
					'serif',
				)
			) );

			// Image sizes.
			$image_sizes = array_map(
				function( $args ) {
					$output  = $args['name'] . ': ';
					$output .= $args['width'] . 'px &times; ';
					$output .= ( $args['height'] ) ? ( $args['height'] . 'px' ) : ( esc_html_x( 'variable height', 'Variable image height.', 'michelle' ) );
					$output .= ', ';
					$output .= ( $args['crop'] ) ? ( esc_html__( 'cropped', 'michelle' ) ) : ( esc_html__( 'scaled', 'michelle' ) );
					return $output;
				},
				Media::get_image_sizes()
			);

			// Reusable blocks.
			$blocks_reusable = get_posts( array(
				'post_type'   => Block_Area::get_post_type(),
				'numberposts' => 100,
			) );
			$blocks = array(
				0 => esc_html__( '&mdash; no block', 'michelle' ),
			);
			foreach ( $blocks_reusable as $block ) {
				$blocks[ $block->ID ] = $block->post_title;
			}


		// Processing

			$options = array(

				/**
				 * Site identity: Logo.
				 */

					'0' . 10 . 'logo' . 10 => array(
						'section'           => 'title_tagline',
						'priority'          => 9,
						'type'              => 'number',
						'id'                => 'custom_logo_height',
						'label'             => esc_html__( 'Max logo image height (px)', 'michelle' ),
						'description'       => esc_html__( 'Do not forget to set the logo max height.', 'michelle' ) . ' ' . esc_html__( 'Upload twice as big image to make your logo ready for high DPI screens.', 'michelle' ),
						'default'           => 100,
						'sanitize_callback' => 'absint',
						'input_attrs'       => array(
							'size'     => 5,
							'maxwidth' => 3,
							'min'      => 20,
							'max'      => 500,
						),
						'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
						'preview_js'        => array(
							'css' => array(
								':root' => array(
									array(
										'property' => '--[[id]]',
										'suffix'   => 'px',
									),
								),
							),
						),
					),

					'0' . 10 . 'logo' . 20 => array(
						'section'     => 'title_tagline',
						'priority'    => 10,
						'type'        => 'checkbox',
						'id'          => 'display_site_title',
						'label'       => esc_html__( 'Display site title & tagline', 'michelle' ),
						'description' => esc_html__( 'Tagline longer than 40 characters gets accessibly hidden automatically on your live website.', 'michelle' ),
						'default'     => true,
						'preview_js'  => array(
							'custom' => "$( 'body' ).toggleClass( 'is-hidden-site-title' );",
						),
					),

					'0' . 10 . 'logo' . 30 => array(
						'section'    => 'title_tagline',
						'priority'   => 11,
						'type'       => 'radio',
						'id'         => 'site_title_position',
						'label'      => esc_html__( 'Site title position', 'michelle' ),
						'default'    => 'last',
						'choices'    => array(
							'first' => esc_html__( 'First item in header', 'michelle' ),
							'last'  => esc_html__( 'Last item in header', 'michelle' ),
						),
						'preview_js' => array(
							'custom' => "$( 'body' ).toggleClass( 'has-site-title-first has-site-title-last' );",
						),
					),

				/**
				 * Theme credits.
				 */
				'0' . 90 . 'placeholder' => array(
					'id'                   => 'placeholder',
					'type'                 => 'section',
					'create_section'       => '',
					'in_panel'             => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
					'in_panel-description' => '<h3>' . esc_html__( 'Theme Credits', 'michelle' ) . '</h3>'
						. '<p class="description">'
						. sprintf(
							/* translators: 1: linked theme name, 2: theme author name. */
							esc_html__( '%1$s is a WordPress theme developed by %2$s.', 'michelle' ),
							'<a href="' . esc_url( wp_get_theme( 'michelle' )->get( 'ThemeURI' ) ) . '"><strong>' . esc_html( wp_get_theme( 'michelle' )->get( 'Name' ) ) . '</strong></a>',
							'<strong>' . esc_html( wp_get_theme( 'michelle' )->get( 'Author' ) ) . '</strong>'
						)
						. '</p>'
						. '<p class="description">'
						. sprintf(
							/* translators: %s: theme author link. */
							esc_html__( 'You can obtain other professional WordPress themes at %s.', 'michelle' ),
							'<strong><a href="' . esc_url( wp_get_theme( 'michelle' )->get( 'AuthorURI' ) ) . '">' . esc_html( str_replace( 'http://', '', untrailingslashit( wp_get_theme( 'michelle' )->get( 'AuthorURI' ) ) ) ) . '</a></strong>'
						)
						. '</p>'
						. '<p class="description">'
						. esc_html__( 'Thank you for using a theme by WebMan Design!', 'michelle' )
						. '</p>',
				),

				/**
				 * Colors: General colors.
				 */
				100 . 'colors' => array(
					'id'             => 'colors_general',
					'type'           => 'section',
					'create_section' => sprintf(
						/* translators: Customizer section title. %s = section name. */
						esc_html__( 'Colors: %s', 'michelle' ),
						esc_html_x( 'General', 'Customizer color section title', 'michelle' )
					),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

						100 . 'colors' . 100 => array(
							'type'       => 'color',
							'id'         => 'color_body_text',
							'label'      => esc_html__( 'Text color', 'michelle' ),
							'default'    => '#434547',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),
						100 . 'colors' . 110 => array(
							'type'       => 'color',
							'id'         => 'color_body_headings',
							'label'      => esc_html__( 'Headings color', 'michelle' ),
							'default'    => '#030507',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),

						100 . 'colors' . 200 => array(
							'type'       => 'color',
							'id'         => 'color_border_decorative',
							'label'      => esc_html__( 'Decorative border color', 'michelle' ),
							'default'    => '#f5df4d',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),

				/**
				 * Colors: Accent colors.
				 */
				110 . 'colors' => array(
					'id'             => 'colors_accents',
					'type'           => 'section',
					'create_section' => sprintf(
						/* translators: Customizer section title. %s = section name. */
						esc_html__( 'Colors: %s', 'michelle' ),
						esc_html_x( 'Accents', 'Customizer color section title', 'michelle' )
					),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					110 . 'colors' . 100 => array(
						'type'       => 'color',
						'id'         => 'color_accent',
						'label'      => esc_html__( 'Accent color', 'michelle' ),
						'default'    => '#6340a7',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
						'palette' => array(
							'name' => esc_html__( 'Accent color', 'michelle' ),
							'slug' => 'accent',
						),
					),

					/**
					 * Button colors.
					 */
					110 . 'colors' . 200 => array(
						'type'    => 'html',
						'content' => '<h3>' . esc_html__( 'Button', 'michelle' ) . '</h3>',
					),

						110 . 'colors' . 210 => array(
							'type'       => 'color',
							'id'         => 'color_button_background',
							'label'      => esc_html__( 'Background color', 'michelle' ),
							'default'    => '#f5df4d',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),
						110 . 'colors' . 220 => array(
							'type'        => 'color',
							'id'          => 'color_button_text',
							'label'       => esc_html__( 'Text color', 'michelle' ),
							'default'     => '#030507',
							'css_var'     => 'maybe_hash_hex_color',
							'preview_js'  => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),

				/**
				 * Colors: Header.
				 */
				120 . 'colors' => array(
					'id'             => 'colors_header',
					'type'           => 'section',
					'create_section' => sprintf(
						/* translators: Customizer section title. %s = section name. */
						esc_html__( 'Colors: %s', 'michelle' ),
						esc_html_x( 'Header', 'Customizer color section title', 'michelle' )
					),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					/**
					 * Navigation colors.
					 */
					120 . 'colors' . 100 => array(
						'type'        => 'html',
						'content'     => '<h3>' . esc_html__( 'Site navigation', 'michelle' ) . '</h3>',
						'description' => esc_html__( 'Primary menu location sub-menu colors.', 'michelle' ),
					),

						120 . 'colors' . 110 => array(
							'type'       => 'color',
							'id'         => 'color_navigation_background',
							'label'      => esc_html__( 'Background color', 'michelle' ),
							'default'    => '#f5df4d',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),
						120 . 'colors' . 120 => array(
							'type'       => 'color',
							'id'         => 'color_navigation_text',
							'label'      => esc_html__( 'Text color', 'michelle' ),
							'default'    => '#030507',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),

					/**
					 * Search form colors.
					 */
					120 . 'colors' . 200 => array(
						'type'        => 'html',
						'content'     => '<h3>' . esc_html__( 'Search form', 'michelle' ) . '</h3>',
						'description' => esc_html__( 'Pop-up search form colors.', 'michelle' ),
					),

						120 . 'colors' . 210 => array(
							'type'       => 'color',
							'id'         => 'color_search_background',
							'label'      => esc_html__( 'Background color', 'michelle' ),
							'default'    => '#f5df4d',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),
						120 . 'colors' . 220 => array(
							'type'       => 'color',
							'id'         => 'color_search_text',
							'label'      => esc_html__( 'Text color', 'michelle' ),
							'default'    => '#030507',
							'css_var'    => 'maybe_hash_hex_color',
							'preview_js' => array(
								'css' => array(
									':root' => array(
										'--[[id]]',
									),
								),
							),
						),

				/**
				 * Colors: Footer.
				 */
				130 . 'colors' => array(
					'id'             => 'colors_footer',
					'type'           => 'section',
					'create_section' => sprintf(
						/* translators: Customizer section title. %s = section name. */
						esc_html__( 'Colors: %s', 'michelle' ),
						esc_html_x( 'Footer', 'Customizer color section title', 'michelle' )
					),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					130 . 'colors' . 100 => array(
						'type'       => 'color',
						'id'         => 'color_footer_background',
						'label'      => esc_html__( 'Background color', 'michelle' ),
						'default'    => '#030507',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
					),
					130 . 'colors' . 110 => array(
						'type'       => 'color',
						'id'         => 'color_footer_text',
						'label'      => esc_html__( 'Text color', 'michelle' ),
						'default'    => '#ffffff',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
					),
					130 . 'colors' . 120 => array(
						'type'       => 'color',
						'id'         => 'color_footer_link',
						'label'      => esc_html__( 'Link color', 'michelle' ),
						'default'    => '#ffffff',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
					),

				/**
				 * Colors: Editor palette.
				 */
				199 . 'colors' => array(
					'id'             => 'colors_editor',
					'type'           => 'section',
					'create_section' => sprintf(
						/* translators: Customizer section title. %s = section name. */
						esc_html__( 'Colors: %s', 'michelle' ),
						esc_html_x( 'Editor palette', 'Customizer color section title', 'michelle' )
					),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					199 . 'colors' . 100 => array(
						'type'    => 'html',
						'content' => '<p>' . esc_html__( 'Accent color is also being conveniently added into editor color palette for you, so you don\'t need to duplicate it here.', 'michelle' ) . '</p>',
					),

					199 . 'colors' . 200 => array(
						'type'       => 'color',
						'id'         => 'color_palette_' . 1,
						'label'      => sprintf(
							/* translators: Editor palette color label. %d: color number. */
							esc_html__( 'Palette color %d', 'michelle' ),
							1
						),
						'default'    => '#f5df4d',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
						'palette' => array(
							'slug' => 'palette-' . 1,
						),
					),
					199 . 'colors' . 210 => array(
						'type'       => 'color',
						'id'         => 'color_palette_' . 2,
						'label'      => sprintf(
							/* translators: Editor palette color label. %d: color number. */
							esc_html__( 'Palette color %d', 'michelle' ),
							2
						),
						'default'    => '#030507',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
						'palette' => array(
							'slug' => 'palette-' . 2,
						),
					),
					199 . 'colors' . 220 => array(
						'type'       => 'color',
						'id'         => 'color_palette_' . 3,
						'label'      => sprintf(
							/* translators: Editor palette color label. %d: color number. */
							esc_html__( 'Palette color %d', 'michelle' ),
							3
						),
						'default'    => '#ffffff',
						'css_var'    => 'maybe_hash_hex_color',
						'preview_js' => array(
							'css' => array(
								':root' => array(
									'--[[id]]',
								),
							),
						),
						'palette' => array(
							'slug' => 'palette-' . 3,
						),
					),


				/**
				 * Layout.
				 */
				200 . 'layout' => array(
					'id'             => 'layout',
					'type'           => 'section',
					'create_section' => esc_html_x( 'Layout', 'Customizer section title.', 'michelle' ),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					/**
					 * Site layout.
					 */
					200 . 'layout' . 100 => array(
						'type'    => 'html',
						'content' => '<h3>' . esc_html_x( 'Site Container', 'A website container.', 'michelle' ) . '</h3>',
					),

						200 . 'layout' . 110 => array(
							'type'              => 'range',
							'id'                => 'layout_width_content',
							'label'             => esc_html__( 'Content width', 'michelle' ),
							'description'       =>
								esc_html__( 'Default value:', 'michelle' ) . ' ' . 1400
								. '<br>'
								. esc_html__( 'This width is applied on archive pages, wide-aligned blocks&hellip;', 'michelle' ),
							'default'           => 1400,
							'min'               => 880,
							'max'               => 1920,
							'step'              => 1,
							'suffix'            => 'px',
							'sanitize_callback' => 'absint',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
							'preview_js'        => array(
								'css' => array(
									':root' => array(
										array(
											'property' => '--[[id]]',
											'suffix'   => 'px',
										),
									),
								),
							),
						),
						200 . 'layout' . 120 => array(
							'type'              => 'range',
							'id'                => 'layout_width_entry_content',
							'label'             => esc_html__( 'Entry content width', 'michelle' ),
							'description'       =>
								esc_html__( 'Default value:', 'michelle' ) . ' ' . 640
								. '<br>'
								. esc_html__( 'This width is applied on post and page actual content elements.', 'michelle' )
								. ' '
								. esc_html__( 'Set this cautiously for the best readability.', 'michelle' ),
							'default'           => 640,
							'min'               => 400,
							'max'               => 1000,
							'step'              => 1,
							'suffix'            => 'px',
							'sanitize_callback' => 'absint',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
							'preview_js'        => array(
								'css' => array(
									':root' => array(
										array(
											'property' => '--[[id]]',
											'suffix'   => 'px',
										),
									),
								),
							),
						),

				/**
				 * Typography.
				 */
				300 . 'typography' => array(
					'id'             => 'typography',
					'type'           => 'section',
					'create_section' => esc_html_x( 'Typography', 'Customizer section title.', 'michelle' ),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					300 . 'typography' . 100 => array(
						'type'              => 'range',
						'id'                => 'typography_size_html',
						'label'             => esc_html__( 'Basic font size in px', 'michelle' ),
						'description'       => esc_html__( 'All other font sizes are calculated automatically from this basic font size.', 'michelle' ),
						'default'           => 19,
						'min'               => 13,
						'max'               => 28,
						'step'              => 1,
						'suffix'            => 'px',
						'sanitize_callback' => 'absint',
						'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
						'preview_js'        => array(
							'css' => array(
								':root' => array(
									array(
										'property' => '--[[id]]',
										'suffix'   => 'px',
									),
								),
							),
						),
					),
					300 . 'typography' . 110 => array(
						'type'              => 'range',
						'id'                => 'typography_size_header',
						'label'             => esc_html__( 'Header font size in px', 'michelle' ),
						'default'           => 16,
						'min'               => 13,
						'max'               => 28,
						'step'              => 1,
						'suffix'            => 'px',
						'sanitize_callback' => 'absint',
						'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
						'preview_js'        => array(
							'css' => array(
								':root' => array(
									array(
										'property' => '--[[id]]',
										'suffix'   => 'px',
									),
								),
							),
						),
					),
					300 . 'typography' . 120 => array(
						'type'              => 'range',
						'id'                => 'typography_size_footer',
						'label'             => esc_html__( 'Footer font size in px', 'michelle' ),
						'default'           => 16,
						'min'               => 13,
						'max'               => 28,
						'step'              => 1,
						'suffix'            => 'px',
						'sanitize_callback' => 'absint',
						'css_var'           => __NAMESPACE__ . '\Sanitize::css_px',
						'preview_js'        => array(
							'css' => array(
								':root' => array(
									array(
										'property' => '--[[id]]',
										'suffix'   => 'px',
									),
								),
							),
						),
					),

					300 . 'typography' . 200 => array(
						'type'    => 'html',
						'content' =>
							'<h3>'
							. esc_html__( 'Font families setup', 'michelle' )
							. '</h3>'
							. '<p class="description">'
							. sprintf(
								/* translators: %s: customizer option values. */
								esc_html__( 'Values of %s set web safe system font families.', 'michelle' ),
								'<code>system</code>, <code>serif</code>, <code>sans-serif</code>'
							)
							. '</p>'
							. '<p class="description">'
							. esc_html__( 'You can use any Google Fonts with this theme.', 'michelle' )
							. ' '
							. esc_html__( 'Just input the Google Fonts font family name into the fields below, choose language, and you are done!', 'michelle' )
							. '</p>',
					),

						300 . 'typography' . 210 => array(
							'type'              => 'text',
							'id'                => 'typography_font_global',
							'label'             => esc_html__( 'Global font', 'michelle' ),
							'description'       => esc_html__( 'Default value:', 'michelle' ) . ' <code>Inter, sans-serif</code>',
							'default'           => 'Inter, sans-serif',
							'datalist'          => $font_families,
							'sanitize_callback' => __NAMESPACE__ . '\Sanitize::fonts',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_fonts',
							'input_attrs'       => array(
								'placeholder' => 'sans-serif',
							),
						),
						300 . 'typography' . 220 => array(
							'type'              => 'text',
							'id'                => 'typography_font_headings',
							'label'             => esc_html__( 'Headings font', 'michelle' ),
							'description'       => esc_html__( 'Default value:', 'michelle' ) . ' <code>Inter, sans-serif</code>',
							'default'           => 'Inter, sans-serif',
							'datalist'          => $font_families,
							'sanitize_callback' => __NAMESPACE__ . '\Sanitize::fonts',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_fonts',
							'input_attrs'       => array(
								'placeholder' => 'sans-serif',
							),
						),
						300 . 'typography' . 230 => array(
							'type'              => 'text',
							'id'                => 'typography_font_site_title',
							'label'             => esc_html__( 'Site title font', 'michelle' ),
							'description'       => esc_html__( 'Default value:', 'michelle' ) . ' <code>Inter, sans-serif</code>',
							'default'           => 'Inter, sans-serif',
							'datalist'          => $font_families,
							'sanitize_callback' => __NAMESPACE__ . '\Sanitize::fonts',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_fonts',
							'input_attrs'       => array(
								'placeholder' => 'serif',
							),
						),
						300 . 'typography' . 240 => array(
							'type'              => 'text',
							'id'                => 'typography_font_alt',
							'label'             => esc_html__( 'Alternative font', 'michelle' ),
							'description'       => esc_html__( 'Used for quotes, for example.', 'michelle' ) . ' ' . esc_html__( 'Default value:', 'michelle' ) . ' <code>Inter, sans-serif</code>',
							'default'           => 'Inter, sans-serif',
							'datalist'          => $font_families,
							'sanitize_callback' => __NAMESPACE__ . '\Sanitize::fonts',
							'css_var'           => __NAMESPACE__ . '\Sanitize::css_fonts',
							'input_attrs'       => array(
								'placeholder' => 'serif',
							),
						),

						300 . 'typography' . 250 => array(
							'type'        => 'checkbox',
							'id'          => 'typography_google_fonts',
							'label'       => esc_html__( 'Enable theme Google Fonts loading', 'michelle' ),
							'description' => esc_html__( 'In case you are loading fonts via plugin, disable this option.', 'michelle' ),
							'default'     => true,
						),
						300 . 'typography' . 260 => array(
							'type'        => 'multicheckbox',
							'id'          => 'typography_font_language',
							'label'       => esc_html__( 'Languages', 'michelle' ),
							'description' =>
								esc_html__( 'Not all Google Fonts support all languages.', 'michelle' )
								. ' '
								. esc_html__( 'Please check on Google Fonts website to make sure.', 'michelle' ),
							'default'     => array( 'latin' ),
							'choices' 		=> array(
								'latin'        => esc_html__( 'Latin', 'michelle' ),
								'latin-ext'    => esc_html__( 'Latin Extended', 'michelle' ),
								'cyrillic'     => esc_html__( 'Cyrillic', 'michelle' ),
								'cyrillic-ext' => esc_html__( 'Cyrillic Extended', 'michelle' ),
								'greek'        => esc_html__( 'Greek', 'michelle' ),
								'greek-ext'    => esc_html__( 'Greek Extended', 'michelle' ),
								'vietnamese'   => esc_html__( 'Vietnamese', 'michelle' ),
							),
							'active_callback' => __NAMESPACE__ . '\Options_Conditional::is_typography_google_fonts',
						),

				/**
				 * Posts.
				 */
				400 . 'posts' => array(
					'id'             => 'posts',
					'type'           => 'section',
					'create_section' => esc_html_x( 'Posts', 'Customizer section title.', 'michelle' ),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					400 . 'posts' . 100 => array(
						'type'        => 'select',
						'id'          => 'thumbnail_aspect_ratio',
						'label'       => esc_html__( 'Post thumbnail aspect ratio', 'michelle' ),
						'description' => esc_html__( 'Note that if you already have images uploaded to your website, you need to regenerate their sizes to apply this change.', 'michelle' ) . ' <a href="' . esc_url( ( class_exists( 'RegenerateThumbnails' ) ) ? ( admin_url( 'tools.php?page=regenerate-thumbnails' ) ) : ( 'https://wordpress.org/plugins/regenerate-thumbnails/' ) ) . '">' . esc_html__( 'You can use a plugin for that &rarr;', 'michelle' ) . '</a><br>' . esc_html__( '(This option can not be previewed here, only on your live website.)', 'michelle' ),
						'default'     => '3:2',
						'choices'     => array(
							''     => esc_html__( 'Keep original image aspect ratio', 'michelle' ),
							'1:1'  => '1:1',
							'4:3'  => '4:3',
							'3:2'  => '3:2',
							'2:1'  => '2:1',
							'16:9' => '16:9',
							'21:9' => '21:9',
						),
						'preview_js' => false, // This is to prevent customizer preview reload.
					),

				/**
				 * Others.
				 */
				950 . 'others' => array(
					'id'             => 'others',
					'type'           => 'section',
					'create_section' => esc_html_x( 'Others', 'Customizer section title.', 'michelle' ),
					'in_panel'       => esc_html_x( 'Theme Options', 'Customizer panel title.', 'michelle' ),
				),

					950 . 'others' . 100 => array(
						'type'        => 'checkbox',
						'id'          => 'admin_welcome_page',
						'label'       => esc_html__( 'Show "Welcome" page', 'michelle' ),
						'description' => esc_html__( 'Under "Appearance" WordPress dashboard menu.', 'michelle' ),
						'default'     => true,
						'preview_js'  => false, // This is to prevent customizer preview reload.
					),

					950 . 'others' . 110 => array(
						'type'        => 'checkbox',
						'id'          => 'navigation_mobile',
						'label'       => esc_html__( 'Enable mobile navigation', 'michelle' ),
						'description' => esc_html__( 'If your website navigation is very simple and you do not want to use the mobile navigation functionality, you can disable it here.', 'michelle' ),
						'default'     => true,
					),

					950 . 'others' . 120 => array(
						'type'        => 'checkbox',
						'id'          => 'header_mobile_sticky',
						'label'       => esc_html__( 'Enable sticky header on small screens', 'michelle' ),
						'description' => esc_html__( 'When visitor scrolls up on small screens site header appears immediately.', 'michelle' ),
						'default'     => true,
					),

					950 . 'others' . 200 => array(
						'type'        => 'html',
						'content'     => '<h3>' . esc_html__( 'Content', 'michelle' ) . '</h3>',
						'description' =>
							esc_html__( 'Create and edit your content with block editor in Reusable Blocks manager.', 'michelle' )
							. ' '
							. '(<a href="' . esc_url( admin_url( 'edit.php?post_type=wp_block' ) ) . '" target="_blank"  rel="noopener noreferrer">' . esc_html__( 'Open Reusable Blocks manager in a new window now &rarr;', 'michelle' ) . '</a>)'
							. '<br><br>'
							. esc_html__( 'Then assign created reusable blocks for display below.', 'michelle' ),
					),

					950 . 'others' . 210 => array(
						'type'              => 'select',
						'id'                => 'block_area_site_footer',
						'label'             => esc_html__( 'Footer content', 'michelle' ),
						'default'           => 0,
						'choices'           => $blocks,
						'sanitize_callback' => 'absint',
					),

					950 . 'others' . 220 => array(
						'type'              => 'select',
						'id'                => 'block_area_error_404',
						'label'             => esc_html__( 'Error 404 content', 'michelle' ),
						'default'           => 0,
						'choices'           => $blocks,
						'sanitize_callback' => 'absint',
					),

			);


		// Output

			return (array) $options;

	} // /set

}
