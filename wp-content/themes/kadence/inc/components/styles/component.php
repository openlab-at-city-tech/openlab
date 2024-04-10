<?php
/**
 * Kadence\Styles\Component class
 *
 * @package kadence
 */

namespace Kadence\Styles;

use Kadence_Blocks_Frontend;
use Kadence\Component_Interface;
use Kadence\Templating_Component_Interface;
use Kadence\Kadence_CSS;
use LearnDash_Settings_Section;
use function Kadence\kadence;
use function Kadence\get_webfont_url;
use function Kadence\print_webfont_preload;
use function add_action;
use function add_filter;
use function wp_enqueue_style;
use function wp_register_style;
use function wp_style_add_data;
use function get_theme_file_uri;
use function get_theme_file_path;
use function wp_styles;
use function esc_attr;
use function esc_url;
use function wp_style_is;
use function _doing_it_wrong;
use function wp_print_styles;
use function post_password_required;
use function is_singular;
use function comments_open;
use function get_comments_number;
use function apply_filters;
use function add_query_arg;
use function wp_add_inline_style;

/**
 * Class for managing stylesheets.
 *
 * Exposes template tags:
 * * `kadence()->print_styles()`
 */
class Component implements Component_Interface, Templating_Component_Interface {

	/**
	 * Associative array of CSS files, as $handle => $data pairs.
	 * $data must be an array with keys 'file' (file path relative to 'assets/css' directory), and optionally 'global'
	 * (whether the file should immediately be enqueued instead of just being registered) and 'preload_callback'
	 * (callback function determining whether the file should be preloaded for the current request).
	 *
	 * Do not access this property directly, instead use the `get_css_files()` method.
	 *
	 * @var array
	 */
	protected $css_files;

	/**
	 * Associative array of Google Fonts to load.
	 *
	 * Do not access this property directly, instead use the `get_google_fonts()` method.
	 *
	 * @var array
	 */
	protected static $google_fonts = array();

	/**
	 * String of css based on options.
	 *
	 * @var string
	 */
	protected $dynamic_css = null;

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'styles';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_styles' ) );
		//add_action( 'wp_enqueue_scripts', array( $this, 'action_enqueue_fonts' ), 90 );
		add_action( 'wp_head', array( $this, 'action_preload_styles' ) );
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_styles' ) );
		add_filter( 'wp_resource_hints', array( $this, 'filter_resource_hints' ), 10, 2 );
		add_filter( 'kadence_dynamic_css', array( $this, 'dynamic_css' ) );
		add_filter( 'tiny_mce_before_init', array( $this, 'filter_add_tinymce_editor_styles' ) );
		add_filter( 'kadence_editor_dynamic_css', array( $this, 'editor_dynamic_css' ) );
		add_action( 'wp_head', array( $this, 'frontend_gfonts' ), 89 );
		add_action( 'admin_init', array( $this, 'action_add_gutenberg_styles' ), 1 );
		add_action( 'admin_init', array( $this, 'update_block_style_dependencies' ), 2 );
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `kadence()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags() : array {
		return array(
			'print_styles' => array( $this, 'print_styles' ),
		);
	}
	/**
	 * Registers or enqueues stylesheets.
	 *
	 * Stylesheets that are global are enqueued. All other stylesheets are only registered, to be enqueued later.
	 */
	public function action_enqueue_styles() {

		$preloading_styles_enabled = $this->preloading_styles_enabled();

		$css_files = $this->get_css_files();
		foreach ( $css_files as $handle => $data ) {
			$src     = get_theme_file_uri( '/assets/css/' . $data['file'] );
			$version = kadence()->get_asset_version( get_theme_file_path( '/assets/css/' . $data['file'] ) );
			/*
			 * Enqueue global stylesheets immediately and register the other ones for later use
			 * (unless preloading stylesheets is disabled, in which case stylesheets should be immediately
			 * enqueued based on whether they are necessary for the page content).
			 */
			if ( $data['global'] || ! $preloading_styles_enabled && is_callable( $data['preload_callback'] ) && call_user_func( $data['preload_callback'] ) ) {
				wp_enqueue_style( $handle, $src, array(), $version, $data['media'] );
			} else {
				wp_register_style( $handle, $src, array(), $version, $data['media'] );
			}

			wp_style_add_data( $handle, 'precache', true );
		}

		// Inline Dynamic CSS.
		wp_add_inline_style( 'kadence-global', trim( apply_filters( 'kadence_dynamic_css', '' ) ) );

		// // Enqueue Google Fonts.
		// $google_fonts_url = $this->get_google_fonts_url();
		// if ( ! empty( $google_fonts_url ) ) {
		// 	wp_enqueue_style( 'kadence-fonts', $google_fonts_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
		// }
	}
	/**
	 * Enqueue Frontend Fonts
	 */
	public function frontend_gfonts() {
		if ( class_exists( 'Kadence_Blocks_Frontend' ) ) {
			$ktblocks_instance = Kadence_Blocks_Frontend::get_instance();
			foreach ( $ktblocks_instance::$gfonts as $key => $font ) {
				if ( ! array_key_exists( $key, self::$google_fonts ) ) {
					$add_font = array(
						'fontfamily'   => $font['fontfamily'],
						'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
						'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
					);
					self::$google_fonts[ $key ] = $add_font;
				} else {
					foreach ( $font['fontvariants'] as $variant ) {
						if ( ! in_array( $variant, self::$google_fonts[ $key ]['fontvariants'], true ) ) {
							array_push( self::$google_fonts[ $key ]['fontvariants'], $variant );
						}
					}
					foreach ( $font['fontsubsets'] as $variant ) {
						if ( ! in_array( $variant, self::$google_fonts[ $key ]['fontsubsets'], true ) ) {
							array_push( self::$google_fonts[ $key ]['fontsubsets'], $variant );
						}
					}
				}
			}
			add_filter( 'kadence_blocks_print_google_fonts', '__return_false' );
			// foreach ( self::$google_fonts as $key => $font ) {
			// 	if ( ! array_key_exists( $key, $ktblocks_instance::$gfonts ) ) {
			// 		$add_font = array(
			// 			'fontfamily'   => $font['fontfamily'],
			// 			'fontvariants' => ( isset( $font['fontvariants'] ) && ! empty( $font['fontvariants'] ) && is_array( $font['fontvariants'] ) ? $font['fontvariants'] : array() ),
			// 			'fontsubsets'  => ( isset( $font['fontsubsets'] ) && ! empty( $font['fontsubsets'] ) && is_array( $font['fontsubsets'] ) ? $font['fontsubsets'] : array() ),
			// 		);
			// 		$ktblocks_instance::$gfonts[ $key ] = $add_font;
			// 	} else {
			// 		foreach ( $font['fontvariants'] as $variant ) {
			// 			if ( ! in_array( $variant, $ktblocks_instance::$gfonts[ $key ]['fontvariants'], true ) ) {
			// 				array_push( $ktblocks_instance::$gfonts[ $key ]['fontvariants'], $variant );
			// 			}
			// 		}
			// 	}
			// }
			if ( empty( self::$google_fonts ) ) {
				return;
			}
			$this->action_enqueue_fonts();
		} else {
			if ( empty( self::$google_fonts ) ) {
				return;
			}
			$this->action_enqueue_fonts();
		}
	}

	/**
	 * Registers or enqueues google fonts.
	 */
	public function action_enqueue_fonts() {
		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if ( ! empty( $google_fonts_url ) ) {
			if ( kadence()->option( 'load_fonts_local' ) ) {
				if ( kadence()->option( 'preload_fonts_local' ) && apply_filters( 'kadence_local_fonts_preload', true ) ) {
					print_webfont_preload( $google_fonts_url );
				}
				wp_register_style(
					'kadence-fonts',
					get_webfont_url( $google_fonts_url ),
					array(),
					KADENCE_VERSION
				);
				wp_print_styles( 'kadence-fonts' );
			} else {
				wp_register_style( 'kadence-fonts', $google_fonts_url, array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion
				wp_print_styles( 'kadence-fonts' );
			}
		}
	}

	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param string $css any custom css.
	 * @return string
	 */
	public function dynamic_css( $css ) {
		$generated_css = $this->generate_base_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Kadence Base CSS */\n" . $generated_css;
		}
		if ( kadence()->has_header() ) {
			$generated_header_css = $this->generate_header_css();
			if ( ! empty( $generated_header_css ) ) {
				$css .= "\n/* Kadence Header CSS */\n" . $generated_header_css;
			}
		}
		if ( kadence()->has_footer() ) {
			$generated_footer_css = $this->generate_footer_css();
			if ( ! empty( $generated_footer_css ) ) {
				$css .= "\n/* Kadence Footer CSS */\n" . $generated_footer_css;
			}
		}
		$generated_dynamic_css = $this->generate_dynamic_css();
		if ( ! empty( $generated_dynamic_css ) ) {
			$css .= "\n/* Kadence Dynamic CSS */\n" . $generated_dynamic_css;
		}
		return $css;
	}
	/**
	 * Generates the dynamic css based on page options.
	 *
	 * @return string
	 */
	public function generate_dynamic_css() {
		$css                    = new Kadence_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		// Above Page Title Featured Image.
		if ( is_singular() && kadence()->show_hero_title() && has_post_thumbnail() ) {
			$post_type = get_post_type();
			if ( kadence()->option( $post_type . '_title_featured_image' ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				if ( $image ) {
					$css->set_selector( '.content-title-style-above .' . $post_type . '-hero-section .entry-hero-container-inner' );
					$css->add_property( 'background-image', $image[0] );
					$bg_settings = kadence()->sub_option( $post_type . '_title_background', 'desktop' );
					if ( $bg_settings && isset( $bg_settings['image'] ) ) {
						$repeat      = ( isset( $bg_settings['image']['repeat'] ) && ! empty( $bg_settings['image']['repeat'] ) ? $bg_settings['image']['repeat'] : 'no-repeat' );
						$size        = ( isset( $bg_settings['image']['size'] ) && ! empty( $bg_settings['image']['size'] ) ? $bg_settings['image']['size'] : 'cover' );
						$position    = ( isset( $bg_settings['image']['position'] ) && is_array( $bg_settings['image']['position'] ) && isset( $bg_settings['image']['position']['x'] ) && ! empty( $bg_settings['image']['position']['x'] ) && isset( $bg_settings['image']['position']['y'] ) && ! empty( $bg_settings['image']['position']['y'] ) ? ( $bg_settings['image']['position']['x'] * 100 ) . '% ' . ( $bg_settings['image']['position']['y'] * 100 ) . '%' : 'center' );
						$attachement = ( isset( $bg_settings['image']['attachment'] ) && ! empty( $bg_settings['image']['attachment'] ) ? $bg_settings['image']['attachment'] : 'scroll' );
						$css->add_property( 'background-repeat', $repeat );
						$css->add_property( 'background-position', $position );
						$css->add_property( 'background-size', $size );
						$css->add_property( 'background-attachment', $attachement );
					} else {
						$css->add_property( 'background-repeat', 'no-repeat' );
						$css->add_property( 'background-position', 'center center' );
						$css->add_property( 'background-size', 'cover' );
						$css->add_property( 'background-attachment', 'scroll' );
					}
				}
			}
		} elseif ( is_post_type_archive( 'product' ) && kadence()->show_hero_title() && class_exists( 'woocommerce' ) && kadence()->option( 'page_title_featured_image' ) ) {
			$post_id = wc_get_page_id( 'shop' );
			if ( has_post_thumbnail( $post_id ) ) {
				$post_type = get_post_type();
				$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
				$css->set_selector( '.' . $post_type . '-archive-hero-section .entry-hero-container-inner' );
				$css->add_property( 'background-image', $image[0] );
				$bg_settings = kadence()->sub_option( $post_type . '_title_background', 'desktop' );
				if ( $bg_settings && isset( $bg_settings['image'] ) ) {
					$repeat      = ( isset( $bg_settings['image']['repeat'] ) && ! empty( $bg_settings['image']['repeat'] ) ? $bg_settings['image']['repeat'] : 'no-repeat' );
					$size        = ( isset( $bg_settings['image']['size'] ) && ! empty( $bg_settings['image']['size'] ) ? $bg_settings['image']['size'] : 'cover' );
					$position    = ( isset( $bg_settings['image']['position'] ) && is_array( $bg_settings['image']['position'] ) && isset( $bg_settings['image']['position']['x'] ) && ! empty( $bg_settings['image']['position']['x'] ) && isset( $bg_settings['image']['position']['y'] ) && ! empty( $bg_settings['image']['position']['y'] ) ? ( $bg_settings['image']['position']['x'] * 100 ) . '% ' . ( $bg_settings['image']['position']['y'] * 100 ) . '%' : 'center' );
					$attachement = ( isset( $bg_settings['image']['attachment'] ) && ! empty( $bg_settings['image']['attachment'] ) ? $bg_settings['image']['attachment'] : 'scroll' );
					$css->add_property( 'background-repeat', $repeat );
					$css->add_property( 'background-position', $position );
					$css->add_property( 'background-size', $size );
					$css->add_property( 'background-attachment', $attachement );
				} else {
					$css->add_property( 'background-repeat', 'no-repeat' );
					$css->add_property( 'background-position', 'center' );
					$css->add_property( 'background-size', 'cover' );
					$css->add_property( 'background-attachment', 'scroll' );
				}
			}
		} elseif ( is_singular( 'product' ) && 'title' === kadence()->option( 'product_above_layout' ) && has_post_thumbnail() ) {
			$post_type = get_post_type();
			if ( kadence()->option( $post_type . '_title_featured_image' ) ) {
				$image = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
				$css->set_selector( '.' . $post_type . '-hero-section .entry-hero-container-inner' );
				$css->add_property( 'background-image', $image[0] );
				$bg_settings = kadence()->sub_option( $post_type . '_title_background', 'desktop' );
				if ( $bg_settings && isset( $bg_settings['image'] ) ) {
					$repeat      = ( isset( $bg_settings['image']['repeat'] ) && ! empty( $bg_settings['image']['repeat'] ) ? $bg_settings['image']['repeat'] : 'no-repeat' );
					$size        = ( isset( $bg_settings['image']['size'] ) && ! empty( $bg_settings['image']['size'] ) ? $bg_settings['image']['size'] : 'cover' );
					$position    = ( isset( $bg_settings['image']['position'] ) && is_array( $bg_settings['image']['position'] ) && isset( $bg_settings['image']['position']['x'] ) && ! empty( $bg_settings['image']['position']['x'] ) && isset( $bg_settings['image']['position']['y'] ) && ! empty( $bg_settings['image']['position']['y'] ) ? ( $bg_settings['image']['position']['x'] * 100 ) . '% ' . ( $bg_settings['image']['position']['y'] * 100 ) . '%' : 'center' );
					$attachement = ( isset( $bg_settings['image']['attachment'] ) && ! empty( $bg_settings['image']['attachment'] ) ? $bg_settings['image']['attachment'] : 'scroll' );
					$css->add_property( 'background-repeat', $repeat );
					$css->add_property( 'background-position', $position );
					$css->add_property( 'background-size', $size );
					$css->add_property( 'background-attachment', $attachement );
				} else {
					$css->add_property( 'background-repeat', 'no-repeat' );
					$css->add_property( 'background-position', 'center' );
					$css->add_property( 'background-size', 'cover' );
					$css->add_property( 'background-attachment', 'scroll' );
				}
			}
		}
		return $css->css_output();
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @return string
	 */
	public function generate_footer_css() {
		$css                    = new Kadence_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		$footer_elements = kadence()->option( 'footer_items' );

		$css->set_selector( '#colophon' );
		$css->render_background( kadence()->sub_option( 'footer_wrap_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '#colophon' );
		$css->render_background( kadence()->sub_option( 'footer_wrap_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '#colophon' );
		$css->render_background( kadence()->sub_option( 'footer_wrap_background', 'mobile' ), $css );
		$css->stop_media_query();

		// Footer Middle.
		if ( kadence()->display_footer_row( 'middle' ) ) {
			$css->set_selector( '.site-middle-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_middle_background', 'desktop' ), $css );
			$css->render_font( kadence()->option( 'footer_middle_widget_content' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_middle_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_middle_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-footer .site-middle-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_middle_link_colors', 'color' ) ) );
			$css->set_selector( '.site-footer .site-middle-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_middle_link_colors', 'hover' ) ) );
			$css->set_selector( '.site-middle-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_middle_height' ), 'desktop' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_middle_top_spacing' ), 'desktop' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_middle_bottom_spacing' ), 'desktop' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'desktop' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_middle_widget_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget-area .widget-title' );
			$css->render_font( kadence()->option( 'footer_middle_widget_title' ), $css );
			$css->set_selector( '.site-middle-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_middle_column_border', 'desktop' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'desktop' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'desktop' ) . ' / 2)' );
			}
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-middle-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_middle_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_middle_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_middle_bottom_border', 'tablet' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_middle_widget_content' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_middle_widget_content' ), 'tablet' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_middle_widget_title' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_middle_widget_title' ), 'tablet' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_middle_height' ), 'tablet' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_middle_top_spacing' ), 'tablet' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_middle_bottom_spacing' ), 'tablet' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'tablet' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_middle_widget_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_middle_column_border', 'tablet' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'tablet' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'tablet' ) . ' / 2)' );
			}
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-middle-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_middle_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_middle_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_middle_bottom_border', 'mobile' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_middle_widget_content' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_middle_widget_content' ), 'mobile' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_middle_widget_title' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_middle_widget_title' ), 'mobile' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_middle_height' ), 'mobile' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_middle_top_spacing' ), 'mobile' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_middle_bottom_spacing' ), 'mobile' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'mobile' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_middle_widget_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-middle-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_middle_column_border', 'mobile' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'mobile' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_middle_column_spacing' ), 'mobile' ) . ' / 2)' );
			}
			$css->stop_media_query();
		}

		// Footer top.
		if ( kadence()->display_footer_row( 'top' ) ) {
			$css->set_selector( '.site-top-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_top_background', 'desktop' ), $css );
			$css->render_font( kadence()->option( 'footer_top_widget_content' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_top_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_top_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-footer .site-top-footer-wrap a:not(.button):not(.wp-block-button__link):not(.wp-element-button)' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_top_link_colors', 'color' ) ) );
			$css->set_selector( '.site-footer .site-top-footer-wrap a:not(.button):not(.wp-block-button__link):not(.wp-element-button):hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_top_link_colors', 'hover' ) ) );
			$css->set_selector( '.site-top-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_top_height' ), 'desktop' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_top_top_spacing' ), 'desktop' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_top_bottom_spacing' ), 'desktop' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'desktop' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_top_widget_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget-area .widget-title' );
			$css->render_font( kadence()->option( 'footer_top_widget_title' ), $css );
			$css->set_selector( '.site-top-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_top_column_border', 'desktop' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'desktop' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'desktop' ) . ' / 2)' );
			}
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-top-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_top_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_top_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_top_bottom_border', 'tablet' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_top_widget_content' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_top_widget_content' ), 'tablet' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_top_widget_title' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_top_widget_title' ), 'tablet' ) );
			$css->set_selector( '.site-top-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_top_height' ), 'tablet' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_top_top_spacing' ), 'tablet' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_top_bottom_spacing' ), 'tablet' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'tablet' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_top_widget_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_top_column_border', 'tablet' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'tablet' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'tablet' ) . ' / 2)' );
			}
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-top-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_top_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_top_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_top_bottom_border', 'mobile' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_top_widget_content' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_top_widget_content' ), 'mobile' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_top_widget_title' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_top_widget_title' ), 'mobile' ) );
			$css->set_selector( '.site-top-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_top_height' ), 'mobile' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_top_top_spacing' ), 'mobile' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_top_bottom_spacing' ), 'mobile' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'mobile' ) );
			$css->add_property( 'grid-row-gap', $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_top_widget_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-top-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_top_column_border', 'mobile' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'mobile' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_top_column_spacing' ), 'mobile' ) . ' / 2)' );
			}
			$css->stop_media_query();
		}

		// Footer bottom.
		if ( kadence()->display_footer_row( 'bottom' ) ) {
			$css->set_selector( '.site-bottom-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_bottom_background', 'desktop' ), $css );
			$css->render_font( kadence()->option( 'footer_bottom_widget_content' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_bottom_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_bottom_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-footer .site-bottom-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_bottom_link_colors', 'color' ) ) );
			$css->set_selector( '.site-footer .site-bottom-footer-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_bottom_link_colors', 'hover' ) ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_bottom_height' ), 'desktop' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_bottom_top_spacing' ), 'desktop' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_bottom_bottom_spacing' ), 'desktop' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_bottom_widget_spacing' ), 'desktop' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget-area .widget-title' );
			$css->render_font( kadence()->option( 'footer_bottom_widget_title' ), $css );
			$css->set_selector( '.site-bottom-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_bottom_column_border', 'desktop' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'desktop' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'desktop' ) . ' / 2)' );
			}
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-bottom-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_bottom_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_bottom_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_bottom_bottom_border', 'tablet' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_bottom_widget_content' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_bottom_widget_content' ), 'tablet' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_bottom_widget_title' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_bottom_widget_title' ), 'tablet' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_bottom_height' ), 'tablet' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_bottom_top_spacing' ), 'tablet' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_bottom_bottom_spacing' ), 'tablet' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_bottom_widget_spacing' ), 'tablet' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_bottom_column_border', 'tablet' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'tablet' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'tablet' ) . ' / 2)' );
			}
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-bottom-footer-wrap .site-footer-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'footer_bottom_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'footer_bottom_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'footer_bottom_bottom_border', 'mobile' ) ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_bottom_widget_content' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_bottom_widget_content' ), 'mobile' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_bottom_widget_title' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_bottom_widget_title' ), 'mobile' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'footer_bottom_height' ), 'mobile' ) );
			$css->add_property( 'padding-top', $this->render_range( kadence()->option( 'footer_bottom_top_spacing' ), 'mobile' ) );
			$css->add_property( 'padding-bottom', $this->render_range( kadence()->option( 'footer_bottom_bottom_spacing' ), 'mobile' ) );
			$css->add_property( 'grid-column-gap', $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'footer_bottom_widget_spacing' ), 'mobile' ) );
			$css->set_selector( '.site-bottom-footer-inner-wrap .site-footer-section:not(:last-child):after' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'footer_bottom_column_border', 'mobile' ) ) );
			if ( $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'mobile' ) ) {
				$css->add_property( 'right', 'calc(-' . $this->render_range( kadence()->option( 'footer_bottom_column_spacing' ), 'mobile' ) . ' / 2)' );
			}
			$css->stop_media_query();
		}

		// Footer Social.
		if ( $this->search_for_value( 'footer-social', $footer_elements ) ) {
			$css->set_selector( '.footer-social-wrap' );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'footer_social_margin' ) ) );
			$css->set_selector( '.footer-social-wrap .footer-social-inner-wrap' );
			$css->add_property( 'font-size', $this->render_size( kadence()->option( 'footer_social_icon_size' ) ) );
			$css->add_property( 'gap', $this->render_size( kadence()->option( 'footer_social_item_spacing' ) ) );
			$css->set_selector( '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button' );
			if ( ! in_array( kadence()->option( 'footer_social_brand' ), array( 'always', 'untilhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_social_color', 'color' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'footer_social_background', 'color' ) ) );
			}
			$css->add_property( 'border', $css->render_border( kadence()->option( 'footer_social_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'footer_social_border_colors', 'color' ) ) );
			$css->add_property( 'border-radius', $this->render_size( kadence()->sub_option( 'footer_social_border_radius' ) ) );
			$css->set_selector( '.site-footer .site-footer-wrap .site-footer-section .footer-social-wrap .footer-social-inner-wrap .social-button:hover' );
			if ( ! in_array( kadence()->option( 'footer_social_brand' ), array( 'always', 'onhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_social_color', 'hover' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'footer_social_background', 'hover' ) ) );
			}
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'footer_social_border_colors', 'hover' ) ) );
			$css->set_selector( '.footer-social-wrap .social-button .social-label' );
			$css->render_font( kadence()->option( 'footer_social_typography' ), $css );
		}

		// Footer HTML.
		if ( $this->search_for_value( 'footer-html', $footer_elements ) ) {
			$css->set_selector( '#colophon .footer-html' );
			$css->render_font( kadence()->option( 'footer_html_typography' ), $css );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'footer_html_margin' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '#colophon .footer-html' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_html_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_html_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '#colophon .footer-html' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_html_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_html_typography' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '#colophon .site-footer-row-container .site-footer-row .footer-html a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_html_link_color', 'color' ) ) );
			$css->set_selector( '#colophon .site-footer-row-container .site-footer-row .footer-html a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_html_link_color', 'hover' ) ) );
		}
		// Footer Navigation.
		if ( $this->search_for_value( 'footer-navigation', $footer_elements ) ) {
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul > li > a' );
			$css->add_property( 'padding-left', $this->render_half_size( kadence()->option( 'footer_navigation_spacing' ) ) );
			$css->add_property( 'padding-right', $this->render_half_size( kadence()->option( 'footer_navigation_spacing' ) ) );
			$css->add_property( 'padding-top', $this->render_half_size( kadence()->option( 'footer_navigation_vertical_spacing' ) ) );
			$css->add_property( 'padding-bottom', $this->render_half_size( kadence()->option( 'footer_navigation_vertical_spacing' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_navigation_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'footer_navigation_background', 'color' ) ) );
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul li a' );
			$css->render_font( kadence()->option( 'footer_navigation_typography' ), $css );
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul li a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_navigation_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'footer_navigation_background', 'hover' ) ) );
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul li.current-menu-item > a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'footer_navigation_color', 'active' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'footer_navigation_background', 'active' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul li a' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_navigation_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_navigation_typography' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'footer_navigation_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '#colophon .footer-navigation .footer-menu-container > ul li a' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'footer_navigation_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'footer_navigation_typography' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'footer_navigation_typography' ), 'mobile' ) );
			$css->stop_media_query();
		}
		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Checks if a value is in a multidimensional array.
	 *
	 * @return Boolean
	 */
	public function search_for_value( $value, $array ) {
		if ( is_array( $array ) ) {
			foreach ( $array as $item ) {
				if ( is_array( $item ) ) {
					// Recursively search in the sub-array.
					if ( $this->search_for_value( $value, $item ) ) {
						return true;
					}
				} else {
					// Compare the current value with the target value.
					if ( $item === $value ) {
						return true;
					}
				}
			}
		}
		return false;
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @return string
	 */
	public function generate_header_css() {
		$css                    = new Kadence_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		if ( kadence()->sub_option( 'header_mobile_switch', 'size' ) ) {
			$tablet_down_media = kadence()->sub_option( 'header_mobile_switch', 'size' ) - 1;
			$desktop_up_media  = kadence()->sub_option( 'header_mobile_switch', 'size' );
			$media_query['tablet']  = '(max-width: ' . $tablet_down_media . 'px)';
			$media_query['desktop'] = '(min-width: ' . $desktop_up_media . 'px)';
		}
		$wide_width_add         = apply_filters(
			'kadence_align_wide_array',
			array(
				'px'  => '230',
				'em'  => '10',
				'rem' => '10',
				'vw'  => '10',
			)
		);
		$n_wide_width_add       = apply_filters(
			'kadence_narrow_width_align_wide_array',
			array(
				'px'  => '260',
				'em'  => '10',
				'rem' => '10',
				'vw'  => '10',
			)
		);

		$max_width_unit        = kadence()->sub_option( 'content_width', 'unit' );
		$max_width             = kadence()->sub_option( 'content_width', 'size' );
		$alignwide_media_query = $max_width + $wide_width_add[ $max_width_unit ];

		$n_max_width_unit        = kadence()->sub_option( 'content_narrow_width', 'unit' );
		$n_max_width             = kadence()->sub_option( 'content_narrow_width', 'size' );
		$n_alignwide_media_query = $n_max_width + $n_wide_width_add[ $n_max_width_unit ];

		$media_query['alignwide']        = '(min-width: ' . $alignwide_media_query . $max_width_unit . ')';
		$media_query['alignwide_narrow'] = '(min-width: ' . $n_alignwide_media_query . $n_max_width_unit . ')';
		$header_elements = kadence()->option( 'header_desktop_items' );
		$mobile_elements = kadence()->option( 'header_mobile_items' );
		// Header to Mobile Switch.
		if ( kadence()->sub_option( 'header_mobile_switch', 'size' ) ) {
			$css->set_selector( '.wp-site-blocks #mobile-header' );
			$css->add_property( 'display', 'block' );
			$css->set_selector( '.wp-site-blocks #main-header' );
			$css->add_property( 'display', 'none' );
			// Desktop Header.
			$css->start_media_query( $media_query['desktop'] );
			$css->set_selector( '.wp-site-blocks #mobile-header' );
			$css->add_property( 'display', 'none' );
			$css->set_selector( '.wp-site-blocks #main-header' );
			$css->add_property( 'display', 'block' );
			$css->stop_media_query();
		}
		$css->start_media_query( $media_query['tablet'] );
		// Mobile Transparent Header.
		$css->set_selector( '.mobile-transparent-header #masthead' );
		$css->add_property( 'position', 'absolute' );
		$css->add_property( 'left', '0px' );
		$css->add_property( 'right', '0px' );
		$css->add_property( 'z-index', '100' );
		$css->set_selector( '.kadence-scrollbar-fixer.mobile-transparent-header #masthead' );
		$css->add_property( 'right', 'var(--scrollbar-offset,0)' );
		$css->set_selector( '.mobile-transparent-header #masthead, .mobile-transparent-header .site-top-header-wrap .site-header-row-container-inner, .mobile-transparent-header .site-main-header-wrap .site-header-row-container-inner, .mobile-transparent-header .site-bottom-header-wrap .site-header-row-container-inner' );
		$css->add_property( 'background', 'transparent' );
		// Mobile Header row layouts.
		$css->set_selector( '.site-header-row-tablet-layout-fullwidth, .site-header-row-tablet-layout-standard' );
		$css->add_property( 'padding', '0px' );
		$css->stop_media_query();
		// Desktop Header.
		$css->start_media_query( $media_query['desktop'] );
		// Desktop Transparent Header.
		$css->set_selector( 'body.elementor-editor-active.transparent-header #masthead, body.fl-builder-edit.transparent-header #masthead, body.vc_editor.transparent-header #masthead, body.brz-ed.transparent-header #masthead' );
		$css->add_property( 'z-index', '0' );
		$css->set_selector( '.transparent-header #masthead' );
		$css->add_property( 'position', 'absolute' );
		$css->add_property( 'left', '0px' );
		$css->add_property( 'right', '0px' );
		$css->add_property( 'z-index', '100' );
		$css->set_selector( '.transparent-header.kadence-scrollbar-fixer #masthead' );
		$css->add_property( 'right', 'var(--scrollbar-offset,0)' );
		$css->set_selector( '.transparent-header #masthead, .transparent-header .site-top-header-wrap .site-header-row-container-inner, .transparent-header .site-main-header-wrap .site-header-row-container-inner, .transparent-header .site-bottom-header-wrap .site-header-row-container-inner' );
		$css->add_property( 'background', 'transparent' );
		$css->stop_media_query();
		// Logo area.
		if ( kadence()->option( 'custom_logo' ) || is_customize_preview() ) {
			$logo_width = kadence()->option( 'logo_width' );
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $logo_width['size'] ) && isset( $logo_width['size'][ $device ] ) && ! empty( $logo_width['size'][ $device ] ) ) {
					$unit = ( isset( $logo_width['unit'] ) && isset( $logo_width['unit'][ $device ] ) && ! empty( $logo_width['unit'][ $device ] ) ? $logo_width['unit'][ $device ] : 'px' );
					if ( 'desktop' !== $device ) {
						$css->start_media_query( $media_query[ $device ] );
					}
					$css->set_selector( '.site-branding a.brand img' );
					$css->add_property( 'max-width', $logo_width['size'][ $device ] . $unit );
					$css->set_selector( '.site-branding a.brand img.svg-logo-image' );
					$css->add_property( 'width', $logo_width['size'][ $device ] . $unit );
					if ( 'desktop' !== $device ) {
						$css->stop_media_query();
					}
				}
			}
		}
		// Icon Logo
		if ( ! kadence()->option( 'custom_logo' ) && kadence()->option( 'use_logo_icon' ) || is_customize_preview() ) {
			$logo_icon_width = kadence()->option( 'logo_icon_width' );
			foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
				if ( isset( $logo_icon_width['size'] ) && isset( $logo_icon_width['size'][ $device ] ) && ! empty( $logo_icon_width['size'][ $device ] ) ) {
					$unit = ( isset( $logo_icon_width['unit'] ) && isset( $logo_icon_width['unit'][ $device ] ) && ! empty( $logo_icon_width['unit'][ $device ] ) ? $logo_icon_width['unit'][ $device ] : 'px' );
					if ( 'desktop' !== $device ) {
						$css->start_media_query( $media_query[ $device ] );
					}
					$css->set_selector( '.site-branding a.brand .logo-icon' );
					$css->add_property( 'max-width', $logo_icon_width['size'][ $device ] . $unit );
					if ( 'desktop' !== $device ) {
						$css->stop_media_query();
					}
				}
			}
			$css->set_selector( '.site-branding a.brand .logo-icon' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'logo_icon_color', 'color' ) ) );
		}
		$css->set_selector( '.site-branding' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_logo_padding' ), 'desktop', true ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.site-branding' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_logo_padding' ), 'tablet', true ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.site-branding' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_logo_padding' ), 'mobile', true ) );
		$css->stop_media_query();
		$layout   = kadence()->option( 'logo_layout' );
		$includes = array();
		if ( is_array( $layout ) && isset( $layout['include'] ) ) {
			if ( isset( $layout['include']['desktop'] ) && ! empty( $layout['include']['desktop'] ) ) {
				if ( strpos( $layout['include']['desktop'], 'logo' ) !== false ) {
					if ( ! in_array( 'logo', $includes, true ) ) {
						$includes[] = 'logo';
					}
				}
				if ( strpos( $layout['include']['desktop'], 'title' ) !== false ) {
					if ( ! in_array( 'title', $includes, true ) ) {
						$includes[] = 'title';
					}
				}
				if ( strpos( $layout['include']['desktop'], 'tagline' ) !== false ) {
					if ( ! in_array( 'tagline', $includes, true ) ) {
						$includes[] = 'tagline';
					}
				}
			}
			if ( ! empty( $layout['include']['tablet'] ) ) {
				if ( strpos( $layout['include']['tablet'], 'logo' ) !== false ) {
					if ( ! in_array( 'logo', $includes, true ) ) {
						$includes[] = 'logo';
					}
				}
				if ( strpos( $layout['include']['tablet'], 'title' ) !== false ) {
					if ( ! in_array( 'title', $includes, true ) ) {
						$includes[] = 'title';
					}
				}
				if ( strpos( $layout['include']['tablet'], 'tagline' ) !== false ) {
					if ( ! in_array( 'tagline', $includes, true ) ) {
						$includes[] = 'tagline';
					}
				}
			}
			if ( ! empty( $layout['include']['mobile'] ) ) {
				if ( strpos( $layout['include']['mobile'], 'logo' ) !== false ) {
					if ( ! in_array( 'logo', $includes, true ) ) {
						$includes[] = 'logo';
					}
				}
				if ( strpos( $layout['include']['mobile'], 'title' ) !== false ) {
					if ( ! in_array( 'title', $includes, true ) ) {
						$includes[] = 'title';
					}
				}
				if ( strpos( $layout['include']['mobile'], 'tagline' ) !== false ) {
					if ( ! in_array( 'tagline', $includes, true ) ) {
						$includes[] = 'tagline';
					}
				}
			}
		}
		if ( in_array( 'title', $includes ) ) {
			$css->set_selector( '.site-branding .site-title' );
			$css->render_font( kadence()->option( 'brand_typography' ), $css );
			$css->set_selector( '.site-branding .site-title:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'brand_typography_color', 'hover' ) ) );
			$css->set_selector( 'body.home .site-branding .site-title' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'brand_typography_color', 'active' ) ) );
		}
		if ( in_array( 'tagline', $includes ) ) {
			$css->set_selector( '.site-branding .site-description' );
			$css->render_font( kadence()->option( 'brand_tag_typography' ), $css );
		}
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.site-branding .site-title' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'brand_typography' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'brand_typography' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'brand_typography' ), 'tablet' ) );
		$css->set_selector( '.site-branding .site-description' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'brand_tag_typography' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'brand_tag_typography' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'brand_tag_typography' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.site-branding .site-title' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'brand_typography' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'brand_typography' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'brand_typography' ), 'mobile' ) );
		$css->set_selector( '.site-branding .site-description' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'brand_tag_typography' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'brand_tag_typography' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'brand_tag_typography' ), 'mobile' ) );
		$css->stop_media_query();
		// Header.
		$css->set_selector( '#masthead, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) > .site-header-row-container-inner' );
		$css->render_background( kadence()->sub_option( 'header_wrap_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '#masthead, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) > .site-header-row-container-inner' );
		$css->render_background( kadence()->sub_option( 'header_wrap_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '#masthead, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) > .site-header-row-container-inner' );
		$css->render_background( kadence()->sub_option( 'header_wrap_background', 'mobile' ), $css );
		$css->stop_media_query();

		// Header Main.
		if ( kadence()->display_header_row( 'main' ) || kadence()->display_mobile_header_row( 'main' ) ) {
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'header_main_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_main_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-main-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_main_height' ), 'desktop' ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_main_top_border' ), 'tablet' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_main_bottom_border' ), 'tablet' ) );
			$css->set_selector( '.site-main-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_main_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_main_top_border' ), 'mobile' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_main_bottom_border' ), 'mobile' ) );
			$css->set_selector( '.site-main-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_main_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_main_padding' ), 'desktop', false ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_main_padding' ), 'tablet', false ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-main-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_main_padding' ), 'mobile', false ) );
			$css->stop_media_query();
			// Header Main Transparent.
			$css->set_selector( '.transparent-header #masthead .site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_trans_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.transparent-header #masthead .site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_trans_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.transparent-header #masthead .site-main-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_main_trans_background', 'mobile' ), $css );
			$css->stop_media_query();
		}
		// Header Top.
		if ( kadence()->display_header_row( 'top' ) || kadence()->display_mobile_header_row( 'top' ) ) {
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'header_top_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_top_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-top-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_top_height' ), 'desktop' ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_top_top_border' ), 'tablet' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_top_bottom_border' ), 'tablet' ) );
			$css->set_selector( '.site-top-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_top_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_top_top_border' ), 'mobile' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_top_bottom_border' ), 'mobile' ) );
			$css->set_selector( '.site-top-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_top_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_top_padding' ), 'desktop', false ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_top_padding' ), 'tablet', false ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-top-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_top_padding' ), 'mobile', false ) );
			$css->stop_media_query();
			// Header Top Transparent.
			$css->set_selector( '.transparent-header #masthead .site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_trans_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.transparent-header #masthead .site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_trans_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.transparent-header #masthead .site-top-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_top_trans_background', 'mobile' ), $css );
			$css->stop_media_query();
		}

		if ( kadence()->display_header_row( 'bottom' ) || kadence()->display_mobile_header_row( 'bottom' ) ) {
			// Header Bottom.
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'header_bottom_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_bottom_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.site-bottom-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_bottom_height' ), 'desktop' ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_bottom_top_border' ), 'tablet' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_bottom_bottom_border' ), 'tablet' ) );
			$css->set_selector( '.site-bottom-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_bottom_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_header_responsive_border( kadence()->option( 'header_bottom_top_border' ), 'mobile' ) );
			$css->add_property( 'border-bottom', $css->render_header_responsive_border( kadence()->option( 'header_bottom_bottom_border' ), 'mobile' ) );
			$css->set_selector( '.site-bottom-header-inner-wrap' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'header_bottom_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_bottom_padding' ), 'desktop', false ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_bottom_padding' ), 'tablet', false ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.site-bottom-header-wrap .site-header-row-container-inner>.site-container' );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'header_bottom_padding' ), 'mobile', false ) );
			$css->stop_media_query();
			// Header Bottom Transparent.
			$css->set_selector( '.transparent-header #masthead .site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_trans_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.transparent-header #masthead .site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_trans_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.transparent-header #masthead .site-bottom-header-wrap .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_bottom_trans_background', 'mobile' ), $css );
			$css->stop_media_query();
		}

		// Sticky Header.
		if ( 'no' !== kadence()->option( 'header_sticky' ) || 'no' !== kadence()->option( 'mobile_header_sticky' ) ) {
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding a.brand .logo-icon, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding a.brand .logo-icon' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_logo_icon_color', 'color' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.item-hidden-above):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.item-hidden-above) > .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_sticky_background', 'desktop' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_sticky_bottom_border', 'desktop' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.item-hidden-above):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.item-hidden-above) > .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_sticky_background', 'tablet' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_sticky_bottom_border', 'tablet' ) ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.site-header-row-container):not(.item-hidden-above):not(.site-main-header-wrap), #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start):not(.item-hidden-above) > .site-header-row-container-inner' );
			$css->render_background( kadence()->sub_option( 'header_sticky_background', 'mobile' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'header_sticky_bottom_border', 'mobile' ) ) );
			$css->stop_media_query();
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding .site-title, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding .site-description' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_site_title_color', 'color' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_background', 'color' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-toggle-open-container .menu-toggle-open, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .search-toggle-open-container .search-toggle-open' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_color', 'color' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li > a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_background', 'hover' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-toggle-open-container .menu-toggle-open:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-toggle-open-container .menu-toggle-open:focus, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .search-toggle-open-container .search-toggle-open:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .search-toggle-open-container .search-toggle-open:focus' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_color', 'hover' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current-menu-item > a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-menu-container > ul > li.current_page_item > a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_color', 'active' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_navigation_background', 'active' ) ) );
			// Sticky Button.
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'background' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'border' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-header-button-wrap .mobile-header-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'backgroundHover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_sticky_button_color', 'borderHover' ) ) );
			// Sticky Social.
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'background' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'border' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-social-wrap a.social-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-social-wrap a.social-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'backgroundHover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_sticky_social_color', 'borderHover' ) ) );
			// Sticky cart.
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_cart_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_cart_color', 'background' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button .header-cart-total:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_cart_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_cart_color', 'backgroundHover' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button .header-cart-total' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_cart_total_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_cart_total_color', 'background' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-cart-wrap .header-cart-button:hover .header-cart-total, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-mobile-cart-wrap .header-cart-button:hover .header-cart-total' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_cart_total_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_sticky_cart_total_color', 'backgroundHover' ) ) );
			// Sticky HTML.
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_html_color', 'color' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html a, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_html_color', 'link' ) ) );
			$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .header-html a:hover, #masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .mobile-html a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_sticky_html_color', 'hover' ) ) );
			// Sticky Header Logo.
			if ( kadence()->option( 'header_sticky_custom_logo' ) ) {
				$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'header_sticky_logo_width' ), 'desktop' ) );
				$css->start_media_query( $media_query['tablet'] );
				$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'header_sticky_logo_width' ), 'tablet' ) );
				$css->stop_media_query();
				$css->start_media_query( $media_query['mobile'] );
				$css->set_selector( '#masthead .kadence-sticky-header.item-is-fixed:not(.item-at-start) .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'header_sticky_logo_width' ), 'mobile' ) );
				$css->stop_media_query();
			}
		}
		if ( kadence()->desk_transparent_header() || kadence()->mobile_transparent_header() ) {
			// Transparent Header Logo.
			if ( kadence()->option( 'transparent_header_custom_logo' ) ) {
				$css->set_selector( '.transparent-header #main-header .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'transparent_header_logo_width' ), 'desktop' ) );
				$css->start_media_query( $media_query['tablet'] );
				$css->set_selector( '.transparent-header #main-header .site-branding img, .mobile-transparent-header #mobile-header .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'transparent_header_logo_width' ), 'tablet' ) );
				$css->stop_media_query();
				$css->start_media_query( $media_query['mobile'] );
				$css->set_selector( '.transparent-header #main-header .site-branding img, .mobile-transparent-header #mobile-header .site-branding img' );
				$css->add_property( 'max-width', $this->render_range( kadence()->option( 'transparent_header_logo_width' ), 'mobile' ) );
				$css->stop_media_query();
			}
			$css->set_selector( '.transparent-header .site-branding a.brand .logo-icon' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_logo_icon_color', 'color' ) ) );
			// Transparent Header.
			$css->set_selector( '.transparent-header #wrapper #masthead' );
			$css->render_background( kadence()->sub_option( 'transparent_header_background', 'desktop' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'transparent_header_bottom_border', 'desktop' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.transparent-header #wrapper #masthead' );
			$css->render_background( kadence()->sub_option( 'transparent_header_background', 'tablet' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'transparent_header_bottom_border', 'tablet' ) ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.transparent-header #wrapper #masthead' );
			$css->render_background( kadence()->sub_option( 'transparent_header_background', 'mobile' ), $css );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'transparent_header_bottom_border', 'mobile' ) ) );
			$css->stop_media_query();
			$css->set_selector( '.transparent-header #main-header .site-title, .transparent-header #main-header .site-branding .site-description, .mobile-transparent-header #mobile-header .site-branding .site-title, .mobile-transparent-header #mobile-header .site-branding .site-description' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_site_title_color', 'color' ) ) );
			$css->set_selector( '.transparent-header .header-navigation .header-menu-container > ul > li.menu-item > a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_background', 'color' ) ) );
			$css->set_selector( '.mobile-transparent-header .mobile-toggle-open-container .menu-toggle-open, .transparent-header .search-toggle-open-container .search-toggle-open' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_color', 'color' ) ) );
			$css->set_selector( '.transparent-header .header-navigation .header-menu-container > ul > li.menu-item > a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_background', 'hover' ) ) );
			$css->set_selector( '.mobile-transparent-header .mobile-toggle-open-container .menu-toggle-open:hover, .transparent-header .mobile-toggle-open-container .menu-toggle-open:focus, .transparent-header .search-toggle-open-container .search-toggle-open:hover, .transparent-header .search-toggle-open-container .search-toggle-open:focus' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_color', 'hover' ) ) );
			$css->set_selector( '.transparent-header .header-navigation .header-menu-container > ul > li.menu-item.current-menu-item > a, .transparent-header .header-menu-container > ul > li.menu-item.current_page_item > a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_color', 'active' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_navigation_background', 'active' ) ) );
			// Transparent Button.
			$css->set_selector( '.transparent-header #main-header .header-button, .mobile-transparent-header .mobile-header-button-wrap .mobile-header-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'background' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'border' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-button:hover, .mobile-transparent-header .mobile-header-button-wrap .mobile-header-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'backgroundHover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'transparent_header_button_color', 'borderHover' ) ) );
			// Transparent Social.
			$css->set_selector( '.transparent-header .header-social-wrap a.social-button, .mobile-transparent-header #mobile-header .header-mobile-social-wrap a.social-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'background' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'border' ) ) );
			$css->set_selector( '.transparent-header .header-social-wrap a.social-button:hover, .mobile-transparent-header #mobile-header .header-mobile-social-wrap a.social-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'backgroundHover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'transparent_header_social_color', 'borderHover' ) ) );
			// Transparent cart.
			$css->set_selector( '.transparent-header #main-header .header-cart-wrap .header-cart-button, .mobile-transparent-header #mobile-header .header-mobile-cart-wrap .header-cart-button' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_cart_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_cart_color', 'background' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-cart-wrap .header-cart-button:hover, .mobile-transparent-header #mobile-header .header-mobile-cart-wrap .header-cart-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_cart_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_cart_color', 'backgroundHover' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-cart-wrap .header-cart-button .header-cart-total, .mobile-transparent-header #mobile-header .header-mobile-cart-wrap .header-cart-button .header-cart-total' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_cart_total_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_cart_total_color', 'background' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-cart-wrap .header-cart-button:hover .header-cart-total, .mobile-transparent-header #mobile-header .header-mobile-cart-wrap .header-cart-button:hover .header-cart-total' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_cart_total_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'transparent_header_cart_total_color', 'backgroundHover' ) ) );
			// Transparent HTML.
			$css->set_selector( '.transparent-header #main-header .header-html, .mobile-transparent-header .mobile-html' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_html_color', 'color' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-html a, .mobile-transparent-header .mobile-html a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_html_color', 'link' ) ) );
			$css->set_selector( '.transparent-header #main-header .header-html a:hover, .mobile-transparent-header .mobile-html a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'transparent_header_html_color', 'hover' ) ) );
		}
		// Navigation.
		if ( $this->search_for_value( 'navigation', $header_elements ) ) {
			$css->set_selector( '.header-navigation[class*="header-navigation-style-underline"] .header-menu-container.primary-menu-container>ul>li>a:after' );
			$css->add_property( 'width', 'calc( 100% - ' . $this->render_size( kadence()->option( 'primary_navigation_spacing' ) ) . ')' );
			$css->set_selector( '.main-navigation .primary-menu-container > ul > li.menu-item > a' );
			$css->add_property( 'padding-left', $this->render_half_size( kadence()->option( 'primary_navigation_spacing' ) ) );
			$css->add_property( 'padding-right', $this->render_half_size( kadence()->option( 'primary_navigation_spacing' ) ) );
			if ( kadence()->option( 'primary_navigation_style' ) === 'standard' || kadence()->option( 'primary_navigation_style' ) === 'underline' ) {
				$css->add_property( 'padding-top', kadence()->sub_option( 'primary_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'primary_navigation_vertical_spacing', 'unit' ) );
				$css->add_property( 'padding-bottom', kadence()->sub_option( 'primary_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'primary_navigation_vertical_spacing', 'unit' ) );
			}
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'primary_navigation_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'primary_navigation_background', 'color' ) ) );
			$css->set_selector( '.main-navigation .primary-menu-container > ul > li.menu-item .dropdown-nav-special-toggle' );
			$css->add_property( 'right', $this->render_half_size( kadence()->option( 'primary_navigation_spacing' ) ) );
			$css->set_selector( '.main-navigation .primary-menu-container > ul li.menu-item > a' );
			$css->render_font( kadence()->option( 'primary_navigation_typography' ), $css, 'primary_nav' );
			$css->set_selector( '.main-navigation .primary-menu-container > ul > li.menu-item > a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'primary_navigation_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'primary_navigation_background', 'hover' ) ) );
			if ( kadence()->option( 'primary_navigation_parent_active' ) ) {
				$css->set_selector( '.header-navigation[class*="header-navigation-style-underline"] .header-menu-container.primary-menu-container>ul>li.current-menu-ancestor>a:after' );
				$css->add_property( 'transform', 'scale(1, 1) translate(50%, 0)' );
				$css->set_selector( '.main-navigation .primary-menu-container > ul > li.menu-item.current-menu-item > a, .main-navigation .primary-menu-container > ul > li.menu-item.current-menu-ancestor > a, .main-navigation .primary-menu-container > ul > li.menu-item.current-menu-ancestor > a' );
			} else {
				$css->set_selector( '.main-navigation .primary-menu-container > ul > li.menu-item.current-menu-item > a' );
			}
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'primary_navigation_color', 'active' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'primary_navigation_background', 'active' ) ) );
		}
		// Second Nav.
		if ( $this->search_for_value( 'navigation-2', $header_elements ) ) {
			$css->set_selector( '.header-navigation[class*="header-navigation-style-underline"] .header-menu-container.secondary-menu-container>ul>li>a:after' );
			$css->add_property( 'width', 'calc( 100% - ' . $this->render_size( kadence()->option( 'secondary_navigation_spacing' ) ) . ')' );
			$css->set_selector( '.secondary-navigation .secondary-menu-container > ul > li.menu-item > a' );
			$css->add_property( 'padding-left', $this->render_half_size( kadence()->option( 'secondary_navigation_spacing' ) ) );
			$css->add_property( 'padding-right', $this->render_half_size( kadence()->option( 'secondary_navigation_spacing' ) ) );
			if ( kadence()->option( 'secondary_navigation_style' ) === 'standard' || kadence()->option( 'secondary_navigation_style' ) === 'underline' ) {
				$css->add_property( 'padding-top', kadence()->sub_option( 'secondary_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'secondary_navigation_vertical_spacing', 'unit' ) );
				$css->add_property( 'padding-bottom', kadence()->sub_option( 'secondary_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'secondary_navigation_vertical_spacing', 'unit' ) );
			}
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'secondary_navigation_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'secondary_navigation_background', 'color' ) ) );
			$css->set_selector( '.secondary-navigation .primary-menu-container > ul > li.menu-item .dropdown-nav-special-toggle' );
			$css->add_property( 'right', $this->render_half_size( kadence()->option( 'secondary_navigation_spacing' ) ) );
			$css->set_selector( '.secondary-navigation .secondary-menu-container > ul li.menu-item > a' );
			$css->render_font( kadence()->option( 'secondary_navigation_typography' ), $css );
			$css->set_selector( '.secondary-navigation .secondary-menu-container > ul > li.menu-item > a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'secondary_navigation_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'secondary_navigation_background', 'hover' ) ) );
			if ( kadence()->option( 'secondary_navigation_parent_active' ) ) {
				$css->set_selector( '.header-navigation[class*="header-navigation-style-underline"] .header-menu-container.secondary-menu-container>ul>li.current-menu-ancestor>a:after' );
				$css->add_property( 'transform', 'scale(1, 1) translate(50%, 0)' );
				$css->set_selector( '.secondary-navigation .secondary-menu-container > ul > li.menu-item.current-menu-item > a, .secondary-navigation .secondary-menu-container > ul > li.menu-item.current-menu-ancestor > a' );
			} else {
				$css->set_selector( '.secondary-navigation .secondary-menu-container > ul > li.menu-item.current-menu-item > a' );
			}
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'secondary_navigation_color', 'active' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'secondary_navigation_background', 'active' ) ) );
		}
		// Dropdown.
		$css->set_selector( '.header-navigation .header-menu-container ul ul.sub-menu, .header-navigation .header-menu-container ul ul.submenu' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'dropdown_navigation_background', 'color' ) ) );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'dropdown_navigation_shadow' ), kadence()->default( 'dropdown_navigation_shadow' ) ) );
		$css->set_selector( '.header-navigation .header-menu-container ul ul li.menu-item, .header-menu-container ul.menu > li.kadence-menu-mega-enabled > ul > li.menu-item > a' );
		$css->add_property( 'border-bottom', $css->render_border( kadence()->option( 'dropdown_navigation_divider' ) ) );
		$css->set_selector( '.header-navigation .header-menu-container ul ul li.menu-item > a' );
		$css->add_property( 'width', kadence()->sub_option( 'dropdown_navigation_width', 'size' ) . kadence()->sub_option( 'dropdown_navigation_width', 'unit' ) );
		$css->add_property( 'padding-top', $css->render_size( kadence()->option( 'dropdown_navigation_vertical_spacing' ) ) );
		$css->add_property( 'padding-bottom', $css->render_size( kadence()->option( 'dropdown_navigation_vertical_spacing' ) ) );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'dropdown_navigation_color', 'color' ) ) );
		$css->render_font( kadence()->option( 'dropdown_navigation_typography' ), $css );
		$css->set_selector( '.header-navigation .header-menu-container ul ul li.menu-item > a:hover' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'dropdown_navigation_color', 'hover' ) ) );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'dropdown_navigation_background', 'hover' ) ) );
		$css->set_selector( '.header-navigation .header-menu-container ul ul li.menu-item.current-menu-item > a' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'dropdown_navigation_color', 'active' ) ) );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'dropdown_navigation_background', 'active' ) ) );
		// Mobile Toggle.
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open, .mobile-toggle-open-container .menu-toggle-open:focus' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_trigger_background', 'color' ) ) );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_trigger_color', 'color' ) ) );
		$css->add_property( 'padding', $this->render_measure( kadence()->option( 'mobile_trigger_padding' ) ) );
		$css->render_font( kadence()->option( 'mobile_trigger_typography' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_trigger_typography' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_trigger_typography' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_trigger_typography' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_trigger_typography' ), 'mobile' ) );
		$css->stop_media_query();
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open.menu-toggle-style-bordered' );
		$css->add_property( 'border', $css->render_border( kadence()->option( 'mobile_trigger_border' ) ) );
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open .menu-toggle-icon' );
		$css->add_property( 'font-size', kadence()->sub_option( 'mobile_trigger_icon_size', 'size' ) . kadence()->sub_option( 'mobile_trigger_icon_size', 'unit' ) );
		$css->set_selector( '.mobile-toggle-open-container .menu-toggle-open:hover, .mobile-toggle-open-container .menu-toggle-open:focus-visible' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_trigger_color', 'hover' ) ) );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_trigger_background', 'hover' ) ) );
		// Mobile Menu.
		$css->set_selector( '.mobile-navigation ul li' );
		$css->render_font( kadence()->option( 'mobile_navigation_typography' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.mobile-navigation ul li' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_navigation_typography' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_navigation_typography' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'mobile_navigation_typography' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.mobile-navigation ul li' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_navigation_typography' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_navigation_typography' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'mobile_navigation_typography' ), 'mobile' ) );
		$css->stop_media_query();
		$css->set_selector( '.mobile-navigation ul li a' );
		$css->add_property( 'padding-top', kadence()->sub_option( 'mobile_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'mobile_navigation_vertical_spacing', 'unit' ) );
		$css->add_property( 'padding-bottom', kadence()->sub_option( 'mobile_navigation_vertical_spacing', 'size' ) . kadence()->sub_option( 'mobile_navigation_vertical_spacing', 'unit' ) );
		$css->set_selector( '.mobile-navigation ul li > a, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_navigation_background', 'color' ) ) );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_navigation_color', 'color' ) ) );
		$css->set_selector( '.mobile-navigation ul li > a:hover, .mobile-navigation ul li.menu-item-has-children > .drawer-nav-drop-wrap:hover' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_navigation_background', 'hover' ) ) );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_navigation_color', 'hover' ) ) );
		$css->set_selector( '.mobile-navigation ul li.current-menu-item > a, .mobile-navigation ul li.current-menu-item.menu-item-has-children > .drawer-nav-drop-wrap' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_navigation_background', 'active' ) ) );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_navigation_color', 'active' ) ) );
		$css->set_selector( '.mobile-navigation ul li.menu-item-has-children .drawer-nav-drop-wrap, .mobile-navigation ul li:not(.menu-item-has-children) a' );
		$css->add_property( 'border-bottom', $css->render_border( kadence()->option( 'mobile_navigation_divider' ) ) );
		$css->set_selector( '.mobile-navigation:not(.drawer-navigation-parent-toggle-true) ul li.menu-item-has-children .drawer-nav-drop-wrap button' );
		$css->add_property( 'border-left', $css->render_border( kadence()->option( 'mobile_navigation_divider' ) ) );
		// Mobile Popout.
		$css->set_selector( '#mobile-drawer .drawer-inner, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice .pop-portion-bg, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice.pop-animated.show-drawer .drawer-inner' );
		$css->render_background( kadence()->sub_option( 'header_popup_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '#mobile-drawer .drawer-inner, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice .pop-portion-bg, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice.pop-animated.show-drawer .drawer-inner' );
		$css->render_background( kadence()->sub_option( 'header_popup_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '#mobile-drawer .drawer-inner, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice .pop-portion-bg, #mobile-drawer.popup-drawer-layout-fullwidth.popup-drawer-animation-slice.show-drawer.pop-animated .drawer-inner' );
		$css->render_background( kadence()->sub_option( 'header_popup_background', 'mobile' ), $css );
		$css->stop_media_query();
		$css->set_selector( '#mobile-drawer .drawer-header .drawer-toggle' );
		$css->add_property( 'padding', $this->render_measure( kadence()->option( 'header_popup_close_padding' ) ) );
		$css->add_property( 'font-size', kadence()->sub_option( 'header_popup_close_icon_size', 'size' ) . kadence()->sub_option( 'header_popup_close_icon_size', 'unit' ) );
		$css->set_selector( '#mobile-drawer .drawer-header .drawer-toggle, #mobile-drawer .drawer-header .drawer-toggle:focus' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_popup_close_color', 'color' ) ) );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_popup_close_background', 'color' ) ) );
		$css->set_selector( '#mobile-drawer .drawer-header .drawer-toggle:hover, #mobile-drawer .drawer-header .drawer-toggle:focus:hover' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_popup_close_color', 'hover' ) ) );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_popup_close_background', 'hover' ) ) );
		// Header Button.
		if ( $this->search_for_value( 'button', $header_elements ) ) {
			$css->set_selector( '#main-header .header-button' );
			$css->render_font( kadence()->option( 'header_button_typography' ), $css );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'header_button_margin' ) ) );
			$css->add_property( 'border-radius', $this->render_measure( kadence()->option( 'header_button_radius' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_button_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_button_background', 'color' ) ) );
			$css->add_property( 'border', $css->render_border( kadence()->option( 'header_button_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_button_border_colors', 'color' ) ) );
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'header_button_shadow' ), kadence()->default( 'header_button_shadow' ) ) );
			$css->set_selector( '#main-header .header-button.button-size-custom' );
			$css->add_property( 'padding', $this->render_measure( kadence()->option( 'header_button_padding' ) ) );
			$css->set_selector( '#main-header .header-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_button_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_button_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_button_border_colors', 'hover' ) ) );
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'header_button_shadow_hover' ), kadence()->default( 'header_button_shadow_hover' ) ) );
		}
		// Header HTML.
		if ( $this->search_for_value( 'html', $header_elements ) ) {
			$css->set_selector( '.header-html' );
			$css->render_font( kadence()->option( 'header_html_typography' ), $css );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'header_html_margin' ) ) );
			$css->set_selector( '.header-html a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_html_link_color', 'color' ) ) );
			$css->set_selector( '.header-html a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_html_link_color', 'hover' ) ) );
		}
		// Header Cart.
		if ( class_exists( 'woocommerce' ) && $this->search_for_value( 'cart', $header_elements ) ) {
			$css->set_selector( '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_cart_background', 'color' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_cart_color', 'color' ) ) );
			$css->add_property( 'padding', $this->render_measure( kadence()->option( 'header_cart_padding' ) ) );
			$css->set_selector( '.header-cart-wrap .header-cart-button .header-cart-total' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_cart_total_background', 'color' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_cart_total_color', 'color' ) ) );
			$css->set_selector( '.site-header-item .header-cart-wrap .header-cart-inner-wrap .header-cart-button:hover' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_cart_background', 'hover' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_cart_color', 'hover' ) ) );
			$css->set_selector( '.header-cart-wrap .header-cart-button:hover .header-cart-total' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_cart_total_background', 'hover' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_cart_total_color', 'hover' ) ) );
			$css->set_selector( '.header-cart-wrap .header-cart-button .header-cart-label' );
			$css->render_font( kadence()->option( 'header_cart_typography' ), $css );
			if ( ! empty( kadence()->sub_option( 'header_cart_icon_size', 'size' ) ) ) {
				$css->set_selector( '.header-cart-wrap .header-cart-button .kadence-svg-iconset' );
				$css->add_property( 'font-size', kadence()->sub_option( 'header_cart_icon_size', 'size' ) . kadence()->sub_option( 'header_cart_icon_size', 'unit' ) );
			}
		}
		// Mobile Cart.
		if ( class_exists( 'woocommerce' ) && $this->search_for_value( 'mobile-cart', $mobile_elements ) ) {
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_cart_background', 'color' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_cart_color', 'color' ) ) );
			$css->add_property( 'padding', $this->render_measure( kadence()->option( 'header_mobile_cart_padding' ) ) );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-button .header-cart-total' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_cart_total_background', 'color' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_cart_total_color', 'color' ) ) );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-inner-wrap .header-cart-button:hover' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_cart_background', 'hover' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_cart_color', 'hover' ) ) );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-button:hover .header-cart-total' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_cart_total_background', 'hover' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_cart_total_color', 'hover' ) ) );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-button .header-cart-label' );
			$css->render_font( kadence()->option( 'header_mobile_cart_typography' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-button .header-cart-label' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'header_mobile_cart_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'header_mobile_cart_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.header-mobile-cart-wrap .header-cart-button .header-cart-label' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'header_mobile_cart_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'header_mobile_cart_typography' ), 'mobile' ) );
			$css->stop_media_query();
			if ( ! empty( kadence()->sub_option( 'header_mobile_cart_icon_size', 'size' ) ) ) {
				$css->set_selector( '.header-mobile-cart-wrap .header-cart-button .kadence-svg-iconset' );
				$css->add_property( 'font-size', kadence()->sub_option( 'header_mobile_cart_icon_size', 'size' ) . kadence()->sub_option( 'header_mobile_cart_icon_size', 'unit' ) );
			}
		}
		// Header Social.
		if ( $this->search_for_value( 'social', $header_elements ) ) {
			$css->set_selector( '.header-social-wrap' );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'header_social_margin' ) ) );
			$css->set_selector( '.header-social-wrap .header-social-inner-wrap' );
			$css->add_property( 'font-size', $this->render_size( kadence()->option( 'header_social_icon_size' ) ) );
			$css->add_property( 'gap', $this->render_size( kadence()->option( 'header_social_item_spacing' ) ) );
			$css->set_selector( '.header-social-wrap .header-social-inner-wrap .social-button' );
			if ( ! in_array( kadence()->option( 'header_social_brand' ), array( 'always', 'untilhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_social_color', 'color' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_social_background', 'color' ) ) );
			}
			$css->add_property( 'border', $css->render_border( kadence()->option( 'header_social_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_social_border_colors', 'color' ) ) );
			$css->add_property( 'border-radius', $this->render_size( kadence()->sub_option( 'header_social_border_radius' ) ) );
			$css->set_selector( '.header-social-wrap .header-social-inner-wrap .social-button:hover' );
			if ( ! in_array( kadence()->option( 'header_social_brand' ), array( 'always', 'onhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_social_color', 'hover' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_social_background', 'hover' ) ) );
			}
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_social_border_colors', 'hover' ) ) );
			$css->set_selector( '.header-social-wrap .social-button .social-label' );
			$css->render_font( kadence()->option( 'header_social_typography' ), $css );
		}

		// Mobile Header Social.
		if ( $this->search_for_value( 'mobile-social', $mobile_elements ) ) {
			$css->set_selector( '.header-mobile-social-wrap' );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'header_mobile_social_margin' ) ) );
			$css->set_selector( '.header-mobile-social-wrap .header-mobile-social-inner-wrap' );
			$css->add_property( 'font-size', $this->render_size( kadence()->option( 'header_mobile_social_icon_size' ) ) );
			$css->add_property( 'gap', $this->render_size( kadence()->option( 'header_mobile_social_item_spacing' ) ) );
			$css->set_selector( '.header-mobile-social-wrap .header-mobile-social-inner-wrap .social-button' );
			if ( ! in_array( kadence()->option( 'header_mobile_social_brand' ), array( 'always', 'untilhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_social_color', 'color' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_social_background', 'color' ) ) );
			}
			$css->add_property( 'border', $css->render_border( kadence()->option( 'header_mobile_social_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_mobile_social_border_colors', 'color' ) ) );
			$css->add_property( 'border-radius', $this->render_size( kadence()->sub_option( 'header_mobile_social_border_radius' ) ) );
			$css->set_selector( '.header-mobile-social-wrap .header-mobile-social-inner-wrap .social-button:hover' );
			if ( ! in_array( kadence()->option( 'header_mobile_social_brand' ), array( 'always', 'onhover' ), true ) ) {
				$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_mobile_social_color', 'hover' ) ) );
				$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_mobile_social_background', 'hover' ) ) );
			}
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'header_mobile_social_border_colors', 'hover' ) ) );
			$css->set_selector( '.header-mobile-social-wrap .social-button .social-label' );
			$css->render_font( kadence()->option( 'header_mobile_social_typography' ), $css );
		}
		// Search Toggle.
		if ( $this->search_for_value( 'search', $header_elements ) || $this->search_for_value( 'search', $mobile_elements ) ) {
			$css->set_selector( '.search-toggle-open-container .search-toggle-open' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_search_background', 'color' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_search_color', 'color' ) ) );
			$css->add_property( 'padding', $this->render_measure( kadence()->option( 'header_search_padding' ) ) );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'header_search_margin' ) ) );
			$css->render_font( kadence()->option( 'header_search_typography' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.search-toggle-open-container .search-toggle-open' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'header_search_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'header_search_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.search-toggle-open-container .search-toggle-open' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'header_search_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'header_search_typography' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.search-toggle-open-container .search-toggle-open.search-toggle-style-bordered' );
			$css->add_property( 'border', $css->render_border( kadence()->option( 'header_search_border' ) ) );
			if ( kadence()->option( 'header_search_icon_size' ) ) {
				$search_size = kadence()->option( 'header_search_icon_size' );
				foreach ( array( 'desktop', 'tablet', 'mobile' ) as $device ) {
					if ( isset( $search_size['size'] ) && isset( $search_size['size'][ $device ] ) && ! empty( $search_size['size'][ $device ] ) ) {
						$unit = ( isset( $search_size['unit'] ) && isset( $search_size['unit'][ $device ] ) && ! empty( $search_size['unit'][ $device ] ) ? $search_size['unit'][ $device ] : 'px' );
						if ( 'desktop' !== $device ) {
							$css->start_media_query( $media_query[ $device ] );
						}
						$css->set_selector( '.search-toggle-open-container .search-toggle-open .search-toggle-icon' );
						$css->add_property( 'font-size', $search_size['size'][ $device ] . $unit );
						if ( 'desktop' !== $device ) {
							$css->stop_media_query();
						}
					}
				}
			}
			$css->set_selector( '.search-toggle-open-container .search-toggle-open:hover, .search-toggle-open-container .search-toggle-open:focus' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_search_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'header_search_background', 'hover' ) ) );
		}
		if ( $this->search_for_value( 'search', $header_elements ) || $this->search_for_value( 'search', $mobile_elements ) ) {
			// Search Modal.
			$css->set_selector( '#search-drawer .drawer-inner .drawer-content form input.search-field, #search-drawer .drawer-inner .drawer-content form .kadence-search-icon-wrap, #search-drawer .drawer-header' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_search_modal_color', 'color' ) ) );
			$css->set_selector( '#search-drawer .drawer-inner .drawer-content form input.search-field:focus, #search-drawer .drawer-inner .drawer-content form input.search-submit:hover ~ .kadence-search-icon-wrap, #search-drawer .drawer-inner .drawer-content form button[type="submit"]:hover ~ .kadence-search-icon-wrap' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'header_search_modal_color', 'hover' ) ) );
			$css->set_selector( '#search-drawer .drawer-inner' );
			$css->render_background( kadence()->sub_option( 'header_search_modal_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '#search-drawer .drawer-inner' );
			$css->render_background( kadence()->sub_option( 'header_search_modal_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '#search-drawer .drawer-inner' );
			$css->render_background( kadence()->sub_option( 'header_search_modal_background', 'mobile' ), $css );
			$css->stop_media_query();
		}
		// Header Mobile Button.
		if ( $this->search_for_value( 'mobile-button', $mobile_elements ) ) {
			$css->set_selector( '.mobile-header-button-wrap .mobile-header-button-inner-wrap .mobile-header-button' );
			$css->render_font( kadence()->option( 'mobile_button_typography' ), $css );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'mobile_button_margin' ) ) );
			$css->add_property( 'border-radius', $this->render_measure( kadence()->option( 'mobile_button_radius' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_button_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_button_background', 'color' ) ) );
			$css->add_property( 'border', $css->render_border( kadence()->option( 'mobile_button_border' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'mobile_button_border_colors', 'color' ) ) );
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'mobile_button_shadow' ), kadence()->default( 'mobile_button_shadow' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.mobile-header-button-wrap .mobile-header-button-inner-wrap .mobile-header-button' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_button_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_button_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.mobile-header-button-wrap .mobile-header-button-inner-wrap .mobile-header-button' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_button_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_button_typography' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.mobile-header-button-wrap .mobile-header-button-inner-wrap .mobile-header-button:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_button_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'mobile_button_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'mobile_button_border_colors', 'hover' ) ) );
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'mobile_button_shadow_hover' ), kadence()->default( 'mobile_button_shadow_hover' ) ) );
		}
		// Header HTML.
		if ( $this->search_for_value( 'mobile-html', $mobile_elements ) ) {
			$css->set_selector( '.mobile-html' );
			$css->render_font( kadence()->option( 'mobile_html_typography' ), $css );
			$css->add_property( 'margin', $this->render_measure( kadence()->option( 'mobile_html_margin' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.mobile-html' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_html_typography' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_html_typography' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.mobile-html' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'mobile_html_typography' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'mobile_html_typography' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.mobile-html a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_html_link_color', 'color' ) ) );
			$css->set_selector( '.mobile-html a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'mobile_html_link_color', 'hover' ) ) );
		}
		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @return string
	 */
	public function generate_base_css() {
		$css                    = new Kadence_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		$root_selector = apply_filters( 'kadence_root_css_selector', ':root' );
		$body_selector = apply_filters( 'kadence_body_css_selector', 'body' );
		$wide_width_add         = apply_filters(
			'kadence_align_wide_array',
			array(
				'px'  => '230',
				'em'  => '10',
				'rem' => '10',
				'vw'  => '10',
			)
		);
		$n_wide_width_add       = apply_filters(
			'kadence_narrow_width_align_wide_array',
			array(
				'px'  => '260',
				'em'  => '10',
				'rem' => '10',
				'vw'  => '10',
			)
		);

		$max_width_unit        = kadence()->sub_option( 'content_width', 'unit' );
		$max_width             = kadence()->sub_option( 'content_width', 'size' );
		$alignwide_media_query = $max_width + $wide_width_add[ $max_width_unit ];

		$n_max_width_unit        = kadence()->sub_option( 'content_narrow_width', 'unit' );
		$n_max_width             = kadence()->sub_option( 'content_narrow_width', 'size' );
		$n_alignwide_media_query = $n_max_width + $n_wide_width_add[ $n_max_width_unit ];

		$media_query['alignwide']        = '(min-width: ' . $alignwide_media_query . $max_width_unit . ')';
		$media_query['alignwide_narrow'] = '(min-width: ' . $n_alignwide_media_query . $n_max_width_unit . ')';
		// Globals.
		$css->set_selector( $root_selector );
		$css->add_property( '--global-palette1', kadence()->palette_option( 'palette1' ) );
		$css->add_property( '--global-palette2', kadence()->palette_option( 'palette2' ) );
		$css->add_property( '--global-palette3', kadence()->palette_option( 'palette3' ) );
		$css->add_property( '--global-palette4', kadence()->palette_option( 'palette4' ) );
		$css->add_property( '--global-palette5', kadence()->palette_option( 'palette5' ) );
		$css->add_property( '--global-palette6', kadence()->palette_option( 'palette6' ) );
		$css->add_property( '--global-palette7', kadence()->palette_option( 'palette7' ) );
		$css->add_property( '--global-palette8', kadence()->palette_option( 'palette8' ) );
		$css->add_property( '--global-palette9', kadence()->palette_option( 'palette9' ) );
		$css->add_property( '--global-palette9rgb', $css->hex2rgb( kadence()->palette_option( 'palette9' ) ) );
		$css->add_property( '--global-palette-highlight', $css->render_color( kadence()->sub_option( 'link_color', 'highlight' ) ) );
		$css->add_property( '--global-palette-highlight-alt', $css->render_color( kadence()->sub_option( 'link_color', 'highlight-alt' ) ) );
		$css->add_property( '--global-palette-highlight-alt2', $css->render_color( kadence()->sub_option( 'link_color', 'highlight-alt2' ) ) );

		$css->add_property( '--global-palette-btn-bg', $css->render_color_or_gradient( kadence()->sub_option( 'buttons_background', 'color' ) ) );
		$css->add_property( '--global-palette-btn-bg-hover', $css->render_color_or_gradient( kadence()->sub_option( 'buttons_background', 'hover' ) ) );

		$css->add_property( '--global-palette-btn', $css->render_color( kadence()->sub_option( 'buttons_color', 'color' ) ) );
		$css->add_property( '--global-palette-btn-hover', $css->render_color( kadence()->sub_option( 'buttons_color', 'hover' ) ) );

		$css->add_property( '--global-body-font-family', $css->render_font_family( kadence()->option( 'base_font' ), '' ) );
		$css->add_property( '--global-heading-font-family', $css->render_font_family( kadence()->option( 'heading_font' ) ) );
		//$css->add_property( '--global-h1-font-family', $css->render_font_family( kadence()->option( 'h1_font' ) ) );
		//$css->add_property( '--global-h2-font-family', $css->render_font_family( kadence()->option( 'h2_font' ) ) );
		//$css->add_property( '--global-h3-font-family', $css->render_font_family( kadence()->option( 'h3_font' ) ) );
		//$css->add_property( '--global-h4-font-family', $css->render_font_family( kadence()->option( 'h4_font' ) ) );
		//$css->add_property( '--global-h5-font-family', $css->render_font_family( kadence()->option( 'h5_font' ) ) );
		$css->add_property( '--global-primary-nav-font-family', $css->render_font_family( kadence()->option( 'primary_navigation_typography' ), '' ) );
		//$css->add_property( '--global-secondary-nav-font-family', $css->render_font_family( kadence()->option( 'secondary_navigation_typography' ) ) );
		//$css->add_property( '--global-site-title-font-family', $css->render_font_family( kadence()->option( 'brand_typography' ) ) );
		//$css->add_property( '--global-site-tag-font-family', $css->render_font_family( kadence()->option( 'brand_tag_typography' ) ) );
		//$css->add_property( '--global-button-font-family', $css->render_font_family( kadence()->option( 'buttons_typography' ) ) );
		$css->add_property( '--global-fallback-font', apply_filters( 'kadence_theme_global_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-display-fallback-font', apply_filters( 'kadence_theme_global_display_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-content-width', kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) );
		$css->add_property( '--global-content-narrow-width', kadence()->sub_option( 'content_narrow_width', 'size' ) . kadence()->sub_option( 'content_narrow_width', 'unit' ) );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'desktop' ) );
		$css->add_property( '--global-content-boxed-padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'desktop' ) );
		$css->add_property( '--global-calc-content-width', 'calc(' . kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) . ' - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );
		$css->add_property( '--wp--style--global--content-size', 'var(--global-calc-content-width)' );
		//$css->add_property( '--scrollbar-offset', '0px' );
		$css->set_selector( '.wp-site-blocks' );
		$css->add_property( '--global-vw', 'calc( 100vw - ( 0.5 * var(--scrollbar-offset)))' );
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$css->set_selector( ':root body.kadence-elementor-colors' );
			$css->add_property( '--e-global-color-kadence1', 'var(--global-palette1)' );
			$css->add_property( '--e-global-color-kadence2', 'var(--global-palette2)' );
			$css->add_property( '--e-global-color-kadence3', 'var(--global-palette3)' );
			$css->add_property( '--e-global-color-kadence4', 'var(--global-palette4)' );
			$css->add_property( '--e-global-color-kadence5', 'var(--global-palette5)' );
			$css->add_property( '--e-global-color-kadence6', 'var(--global-palette6)' );
			$css->add_property( '--e-global-color-kadence7', 'var(--global-palette7)' );
			$css->add_property( '--e-global-color-kadence8', 'var(--global-palette8)' );
			$css->add_property( '--e-global-color-kadence9', 'var(--global-palette9)' );
		}
		// Divi Editor.
		if ( class_exists( 'ET_Builder_Plugin' ) ) {
			$css->set_selector( 'body.et_divi_builder.et_fb_thin_admin_bar #wrapper' );
			$css->add_property( 'overflow', 'visible' );
			$css->set_selector( '#wrapper.et-fb-iframe-ancestor' );
			$css->add_property( 'overflow', 'visible' );
		}
		// if ( kadence()->option( 'enable_footer_on_bottom' ) ) {
		// 	$css->set_selector( 'html' );
		// 	$css->add_property( 'min-height', '100%' );
		// }
		$css->set_selector( $body_selector );
		$css->render_background( kadence()->sub_option( 'site_background', 'desktop' ), $css );
		if ( kadence()->option( 'font_rendering' ) ) {
			$css->add_property( '-webkit-font-smoothing', 'antialiased' );
			$css->add_property( '-moz-osx-font-smoothing', 'grayscale' );
		}
		$css->set_selector( $body_selector . ', input, select, optgroup, textarea' );
		$css->render_font( kadence()->option( 'base_font' ), $css, 'body' );
		$css->set_selector( '.content-bg, body.content-style-unboxed .site' );
		$css->render_background( kadence()->sub_option( 'content_background', 'desktop' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( $body_selector );
		$css->render_background( kadence()->sub_option( 'site_background', 'tablet' ), $css );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'base_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'base_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'base_font' ), 'tablet' ) );
		$css->set_selector( '.content-bg, body.content-style-unboxed .site' );
		$css->render_background( kadence()->sub_option( 'content_background', 'tablet' ), $css );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( $body_selector );
		$css->render_background( kadence()->sub_option( 'site_background', 'mobile' ), $css );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'base_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'base_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'base_font' ), 'mobile' ) );
		$css->set_selector( '.content-bg, body.content-style-unboxed .site' );
		$css->render_background( kadence()->sub_option( 'content_background', 'mobile' ), $css );
		$css->stop_media_query();
		// Heading Fonts.
		$css->set_selector( 'h1,h2,h3,h4,h5,h6' );
		$css->add_property( 'font-family', 'var(--global-heading-font-family)' );
		$css->set_selector( 'h1' );
		$css->render_font( kadence()->option( 'h1_font' ), $css );
		$css->set_selector( 'h2' );
		$css->render_font( kadence()->option( 'h2_font' ), $css );
		$css->set_selector( 'h3' );
		$css->render_font( kadence()->option( 'h3_font' ), $css );
		$css->set_selector( 'h4' );
		$css->render_font( kadence()->option( 'h4_font' ), $css );
		$css->set_selector( 'h5' );
		$css->render_font( kadence()->option( 'h5_font' ), $css );
		$css->set_selector( 'h6' );
		$css->render_font( kadence()->option( 'h6_font' ), $css );
		$css->set_selector( '.entry-hero h1' );
		$css->render_font( kadence()->option( 'title_above_font' ), $css, 'heading' );
		$css->set_selector( '.entry-hero .kadence-breadcrumbs, .entry-hero .search-form' );
		$css->render_font( kadence()->option( 'title_above_breadcrumb_font' ), $css );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( 'h1' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h1_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h1_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h1_font' ), 'tablet' ) );
		$css->set_selector( 'h2' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h2_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h2_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h2_font' ), 'tablet' ) );
		$css->set_selector( 'h3' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h3_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h3_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h3_font' ), 'tablet' ) );
		$css->set_selector( 'h4' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h4_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h4_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h4_font' ), 'tablet' ) );
		$css->set_selector( 'h5' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h5_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h5_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h5_font' ), 'tablet' ) );
		$css->set_selector( 'h6' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h6_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h6_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h6_font' ), 'tablet' ) );
		$css->set_selector( '.wp-site-blocks .entry-hero h1' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'title_above_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'title_above_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'title_above_font' ), 'tablet' ) );
		$css->set_selector( '.entry-hero .kadence-breadcrumbs' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'title_above_breadcrumb_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'title_above_breadcrumb_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'title_above_breadcrumb_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( 'h1' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h1_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h1_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h1_font' ), 'mobile' ) );
		$css->set_selector( 'h2' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h2_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h2_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h2_font' ), 'mobile' ) );
		$css->set_selector( 'h3' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h3_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h3_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h3_font' ), 'mobile' ) );
		$css->set_selector( 'h4' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h4_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h4_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h4_font' ), 'mobile' ) );
		$css->set_selector( 'h5' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h5_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h5_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h5_font' ), 'mobile' ) );
		$css->set_selector( 'h6' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'h6_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'h6_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'h6_font' ), 'mobile' ) );
		$css->set_selector( '.wp-site-blocks .entry-hero h1' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'title_above_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'title_above_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'title_above_font' ), 'mobile' ) );
		$css->set_selector( '.entry-hero .kadence-breadcrumbs' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'title_above_breadcrumb_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'title_above_breadcrumb_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'title_above_breadcrumb_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Layout.
		$css->add_property( 'max-width', kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) );
		$css->set_selector( '.site-container, .site-header-row-layout-contained, .site-footer-row-layout-contained, .entry-hero-layout-contained, .comments-area, .alignfull > .wp-block-cover__inner-container, .alignwide > .wp-block-cover__inner-container' );
		$css->add_property( 'max-width', 'var(--global-content-width)' );
		$css->set_selector( '.content-width-narrow .content-container.site-container, .content-width-narrow .hero-container.site-container' );
		$css->add_property( 'max-width', 'var(--global-content-narrow-width)' );
		$css->start_media_query( $media_query['alignwide'] );
		$css->set_selector( '.wp-site-blocks .content-container  .alignwide' );
		$css->add_property( 'margin-left', '-' . ( $wide_width_add[ $max_width_unit ] / 2 ) . $max_width_unit );
		$css->add_property( 'margin-right', '-' . ( $wide_width_add[ $max_width_unit ] / 2 ) . $max_width_unit );
		$css->add_property( 'width', 'unset' );
		$css->add_property( 'max-width', 'unset' );
		$css->stop_media_query();
		$css->start_media_query( $media_query['alignwide_narrow'] );
		$css->set_selector( '.content-width-narrow .wp-site-blocks .content-container .alignwide' );
		$css->add_property( 'margin-left', '-' . ( $n_wide_width_add[ $n_max_width_unit ] / 2 ) . $n_max_width_unit );
		$css->add_property( 'margin-right', '-' . ( $n_wide_width_add[ $n_max_width_unit ] / 2 ) . $n_max_width_unit );
		$css->add_property( 'width', 'unset' );
		$css->add_property( 'max-width', 'unset' );
		$css->stop_media_query();
		// Wide layout when boxed.
		$css->set_selector( '.content-style-boxed .wp-site-blocks .entry-content .alignwide' );
		$css->add_property( 'margin-left', 'calc( -1 * var( --global-content-boxed-padding ) )' );
		$css->add_property( 'margin-right', 'calc( -1 * var( --global-content-boxed-padding ) )' );
		// Content Spacing.
		$css->set_selector( '.content-area' );
		$css->add_property( 'margin-top', $css->render_range( kadence()->option( 'content_spacing' ), 'desktop' ) );
		$css->add_property( 'margin-bottom', $css->render_range( kadence()->option( 'content_spacing' ), 'desktop' ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.content-area' );
		$css->add_property( 'margin-top', $css->render_range( kadence()->option( 'content_spacing' ), 'tablet' ) );
		$css->add_property( 'margin-bottom', $css->render_range( kadence()->option( 'content_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.content-area' );
		$css->add_property( 'margin-top', $css->render_range( kadence()->option( 'content_spacing' ), 'mobile' ) );
		$css->add_property( 'margin-bottom', $css->render_range( kadence()->option( 'content_spacing' ), 'mobile' ) );
		$css->stop_media_query();
		// Content Edge Padding.
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( $root_selector );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'tablet' ) );
		$css->add_property( '--global-content-boxed-padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( $root_selector );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'mobile' ) );
		$css->add_property( '--global-content-boxed-padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'mobile' ) );
		$css->stop_media_query();
		// Boxed Spacing.
		$css->set_selector( '.entry-content-wrap' );
		$css->add_property( 'padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'desktop' ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.entry-content-wrap' );
		$css->add_property( 'padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.entry-content-wrap' );
		$css->add_property( 'padding', $this->render_range( kadence()->option( 'boxed_spacing' ), 'mobile' ) );
		$css->stop_media_query();
		// Single Boxed Shadow.
		$css->set_selector( '.entry.single-entry' );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'boxed_shadow' ), kadence()->default( 'boxed_shadow' ) ) );
		$css->add_property( 'border-radius', $css->render_measure( kadence()->option( 'boxed_border_radius' ) ) );
		// Loop Boxed Shadow.
		$css->set_selector( '.entry.loop-entry' );
		$css->add_property( 'border-radius', $css->render_measure( kadence()->option( 'boxed_grid_border_radius' ) ) );
		$grid_radius = kadence()->sub_option( 'boxed_grid_border_radius', 'size' );
		if ( is_array( $grid_radius ) && ( ! empty( $grid_radius[0] ) || ! empty( $grid_radius[1] ) || ! empty( $grid_radius[2] ) || ! empty( $grid_radius[3] ) ) ) {
			$css->add_property( 'overflow', 'hidden' );
		}
		if ( kadence()->sub_option( 'boxed_grid_shadow', 'inset' ) ) {
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'boxed_grid_shadow' ), kadence()->default( 'boxed_grid_shadow' ) ) );
			$css->set_selector( '.entry.loop-entry .post-thumbnail:after' );
			$css->add_property( 'content', ' ' );
			$css->add_property( 'position', 'absolute' );
			$css->add_property( 'top', '0px' );
			$css->add_property( 'left', '0px' );
			$css->add_property( 'right', '0px' );
			$css->add_property( 'bottom', '-60px' );
			$css->add_property( 'overflow', 'hidden' );
			$css->add_property( 'border-radius', '.25rem' );
			$css->add_property( 'border-radius', $css->render_measure( kadence()->option( 'boxed_grid_border_radius' ) ) );
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'boxed_grid_shadow' ), kadence()->default( 'boxed_grid_shadow' ) ) );
		} else {
			$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'boxed_grid_shadow' ), kadence()->default( 'boxed_grid_shadow' ) ) );
		}
		// Boxed Grid Spacing.
		$css->set_selector( '.loop-entry .entry-content-wrap' );
		$css->add_property( 'padding', $css->render_range( kadence()->option( 'boxed_grid_spacing' ), 'desktop' ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.loop-entry .entry-content-wrap' );
		$css->add_property( 'padding', $css->render_range( kadence()->option( 'boxed_grid_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.loop-entry .entry-content-wrap' );
		$css->add_property( 'padding', $css->render_range( kadence()->option( 'boxed_grid_spacing' ), 'mobile' ) );
		$css->stop_media_query();
		// Sidebar Width.
		if ( ! empty( kadence()->sub_option( 'sidebar_width', 'size' ) ) && is_numeric( kadence()->sub_option( 'sidebar_width', 'size' ) ) ) {
			$css->set_selector( '.has-sidebar:not(.has-left-sidebar) .content-container' );
			$css->add_property( 'grid-template-columns', '1fr ' . $this->render_size( kadence()->option( 'sidebar_width' ) ) );
			$css->set_selector( '.has-sidebar.has-left-sidebar .content-container' );
			$css->add_property( 'grid-template-columns', $this->render_size( kadence()->option( 'sidebar_width' ) ) . ' 1fr' );
		}
		// Sidebar.
		if ( kadence()->has_sidebar() ) {
			$css->set_selector( '.primary-sidebar.widget-area .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'sidebar_widget_spacing' ), 'desktop' ) );
			$css->render_font( kadence()->option( 'sidebar_widget_content' ), $css );
			$css->set_selector( '.primary-sidebar.widget-area .widget-title' );
			$css->render_font( kadence()->option( 'sidebar_widget_title' ), $css );
			$css->set_selector( '.primary-sidebar.widget-area .sidebar-inner-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button))' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'sidebar_link_colors', 'color' ) ) );
			$css->set_selector( '.primary-sidebar.widget-area .sidebar-inner-wrap a:where(:not(.button):not(.wp-block-button__link):not(.wp-element-button)):hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'sidebar_link_colors', 'hover' ) ) );
			$css->set_selector( '.primary-sidebar.widget-area' );
			$css->render_background( kadence()->sub_option( 'sidebar_background', 'desktop' ), $css );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'sidebar_padding' ), 'desktop' ) );
			$css->set_selector( '.has-sidebar.has-left-sidebar:not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar:not(.has-left-sidebar) .primary-sidebar.widget-area' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->set_selector( '.has-sidebar:not(.has-left-sidebar):not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar.has-left-sidebar .primary-sidebar.widget-area' );
			$css->add_property( 'border-left', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.primary-sidebar.widget-area .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'sidebar_widget_spacing' ), 'tablet' ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'sidebar_widget_content' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'sidebar_widget_content' ), 'tablet' ) );
			$css->set_selector( '.primary-sidebar.widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'sidebar_widget_title' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'sidebar_widget_title' ), 'tablet' ) );
			$css->set_selector( '.primary-sidebar.widget-area' );
			$css->render_background( kadence()->sub_option( 'sidebar_background', 'tablet' ), $css );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'sidebar_padding' ), 'tablet' ) );
			$css->set_selector( '.has-sidebar.has-left-sidebar:not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar:not(.has-left-sidebar) .primary-sidebar.widget-area' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'tablet' ), kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->set_selector( '.has-sidebar:not(.has-left-sidebar):not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar.has-left-sidebar .primary-sidebar.widget-area' );
			$css->add_property( 'border-left', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'tablet' ), kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.primary-sidebar.widget-area .widget' );
			$css->add_property( 'margin-bottom', $this->render_range( kadence()->option( 'sidebar_widget_spacing' ), 'mobile' ) );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'sidebar_widget_content' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'sidebar_widget_content' ), 'mobile' ) );
			$css->set_selector( '.primary-sidebar.widget-area .widget-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'sidebar_widget_title' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'sidebar_widget_title' ), 'mobile' ) );
			$css->set_selector( '.primary-sidebar.widget-area' );
			$css->render_background( kadence()->sub_option( 'sidebar_background', 'mobile' ), $css );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'sidebar_padding' ), 'mobile' ) );
			$css->set_selector( '.has-sidebar.has-left-sidebar:not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar:not(.has-left-sidebar) .primary-sidebar.widget-area' );
			$css->add_property( 'border-right', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'mobile' ), kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->set_selector( '.has-sidebar:not(.has-left-sidebar):not(.rtl) .primary-sidebar.widget-area, .rtl.has-sidebar.has-left-sidebar .primary-sidebar.widget-area' );
			$css->add_property( 'border-left', $css->render_border( kadence()->sub_option( 'sidebar_divider_border', 'mobile' ), kadence()->sub_option( 'sidebar_divider_border', 'desktop' ) ) );
			$css->stop_media_query();
		}

		// Button.
		if ( substr( kadence()->sub_option( 'buttons_background', 'color' ), 0, strlen( 'linear' ) ) === 'linear' || substr( kadence()->sub_option( 'buttons_background', 'color' ), 0, strlen( 'radial' ) ) === 'radial' ) {
			$css->set_selector( '.elementor-button-wrapper .elementor-button' );
			$css->add_property( 'background-image', 'var(--global-palette-btn-bg)' );
			$css->set_selector( '.elementor-button-wrapper .elementor-button:hover, .elementor-button-wrapper .elementor-button:focus' );
			if ( substr( kadence()->sub_option( 'buttons_background', 'hover' ), 0, strlen( 'linear' ) ) === 'linear' || substr( kadence()->sub_option( 'buttons_background', 'hover' ), 0, strlen( 'radial' ) ) === 'radial'  ) {
				$css->add_property( 'background-image', 'var(--global-palette-btn-bg-hover)' );
			} else {
				$css->add_property( 'background', 'var(--global-palette-btn-bg-hover)' );
			}
		}
		$css->set_selector( 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"], .fl-button, .elementor-button-wrapper .elementor-button' );
		$css->render_font( kadence()->option( 'buttons_typography' ), $css );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'buttons_border_radius' ), 'desktop' ) );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'desktop' ) );
		$css->add_property( 'border', $css->render_responsive_border( kadence()->option( 'buttons_border' ), 'desktop' ) );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'color' ) ) );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'buttons_shadow' ), kadence()->default( 'buttons_shadow' ) ) );
		$css->set_selector( '.wp-block-button.is-style-outline .wp-block-button__link' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'desktop' ) );
		$css->set_selector( 'button:hover, button:focus, button:active, .button:hover, .button:focus, .button:active, .wp-block-button__link:hover, .wp-block-button__link:focus, .wp-block-button__link:active, input[type="button"]:hover, input[type="button"]:focus, input[type="button"]:active, input[type="reset"]:hover, input[type="reset"]:focus, input[type="reset"]:active, input[type="submit"]:hover, input[type="submit"]:focus, input[type="submit"]:active, .elementor-button-wrapper .elementor-button:hover, .elementor-button-wrapper .elementor-button:focus, .elementor-button-wrapper .elementor-button:active' );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'hover' ) ) );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'buttons_shadow_hover' ), kadence()->default( 'buttons_shadow_hover' ) ) );
		$css->set_selector( '.kb-button.kb-btn-global-outline.kb-btn-global-inherit' );
		$btn_padding = kadence()->option( 'buttons_padding' );
		if ( isset( $btn_padding['size']['desktop'] ) ) {
			$btn_size_unit   = ( isset( $btn_padding['unit'] ) && isset( $btn_padding['unit']['desktop'] ) && ! empty( $btn_padding['unit']['desktop'] ) ? $btn_padding['unit']['desktop'] : 'px' );
			if ( isset( $btn_padding['size']['desktop'][0] ) ) {
				$css->add_property( 'padding-top', 'calc(' . $btn_padding['size']['desktop'][0] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][1] ) ) {
				$css->add_property( 'padding-right', 'calc(' . $btn_padding['size']['desktop'][1] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][2] ) ) {
				$css->add_property( 'padding-bottom', 'calc(' . $btn_padding['size']['desktop'][2] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][3] ) ) {
				$css->add_property( 'padding-left', 'calc(' . $btn_padding['size']['desktop'][3] . $btn_size_unit . ' - 2px)' );
			}
		}
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"], .fl-button, .elementor-button-wrapper .elementor-button' );
		$css->add_property( 'border', $css->render_responsive_border( kadence()->option( 'buttons_border' ), 'tablet' ) );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'color' ) ) );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'buttons_border_radius' ), 'tablet' ) );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'tablet' ) );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'buttons_typography' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'buttons_typography' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'buttons_typography' ), 'tablet' ) );
		$css->set_selector( '.wp-block-button.is-style-outline .wp-block-button__link' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'tablet' ) );
		$css->set_selector( '.kb-button.kb-btn-global-outline.kb-btn-global-inherit' );
		$btn_padding = kadence()->option( 'buttons_padding' );
		if ( isset( $btn_padding['size']['tablet'] ) ) {
			$btn_size_unit   = ( isset( $btn_padding['unit'] ) && isset( $btn_padding['unit']['tablet'] ) && ! empty( $btn_padding['unit']['tablet'] ) ? $btn_padding['unit']['tablet'] : 'px' );
			if ( isset( $btn_padding['size']['tablet'][0] ) ) {
				$css->add_property( 'padding-top', 'calc(' . $btn_padding['size']['tablet'][0] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['tablet'][1] ) ) {
				$css->add_property( 'padding-right', 'calc(' . $btn_padding['size']['tablet'][1] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['tablet'][2] ) ) {
				$css->add_property( 'padding-bottom', 'calc(' . $btn_padding['size']['tablet'][2] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['tablet'][3] ) ) {
				$css->add_property( 'padding-left', 'calc(' . $btn_padding['size']['tablet'][3] . $btn_size_unit . ' - 2px)' );
			}
		}
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( 'button, .button, .wp-block-button__link, input[type="button"], input[type="reset"], input[type="submit"], .fl-button, .elementor-button-wrapper .elementor-button' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'mobile' ) );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'buttons_border_radius' ), 'mobile' ) );
		$css->add_property( 'border', $css->render_responsive_border( kadence()->option( 'buttons_border' ), 'mobile' ) );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'color' ) ) );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'buttons_typography' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'buttons_typography' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'buttons_typography' ), 'mobile' ) );
		$css->set_selector( '.wp-block-button.is-style-outline .wp-block-button__link' );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'mobile' ) );
		$css->set_selector( '.kb-button.kb-btn-global-outline.kb-btn-global-inherit' );
		$btn_padding = kadence()->option( 'buttons_padding' );
		if ( isset( $btn_padding['size']['mobile'] ) ) {
			$btn_size_unit   = ( isset( $btn_padding['unit'] ) && isset( $btn_padding['unit']['mobile'] ) && ! empty( $btn_padding['unit']['mobile'] ) ? $btn_padding['unit']['mobile'] : 'px' );
			if ( isset( $btn_padding['size']['mobile'][0] ) ) {
				$css->add_property( 'padding-top', 'calc(' . $btn_padding['size']['mobile'][0] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['mobile'][1] ) ) {
				$css->add_property( 'padding-right', 'calc(' . $btn_padding['size']['mobile'][1] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['mobile'][2] ) ) {
				$css->add_property( 'padding-bottom', 'calc(' . $btn_padding['size']['mobile'][2] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['mobile'][3] ) ) {
				$css->add_property( 'padding-left', 'calc(' . $btn_padding['size']['mobile'][3] . $btn_size_unit . ' - 2px)' );
			}
		}
		$css->stop_media_query();
		// Image.
		$css->set_selector( '.entry-content :where(.wp-block-image) img, .entry-content :where(.wp-block-kadence-image) img' );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'image_border_radius' ), 'desktop' ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.entry-content :where(.wp-block-image) img, .entry-content :where(.wp-block-kadence-image) img' );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'image_border_radius' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.entry-content :where(.wp-block-image) img, .entry-content :where(.wp-block-kadence-image) img' );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'image_border_radius' ), 'mobile' ) );
		$css->stop_media_query();
		// Padding for transparent header.
		if ( kadence()->has_header() ) {
			$css->start_media_query( $media_query['desktop'] );
			$css->set_selector( '.transparent-header .entry-hero .entry-hero-container-inner' );
			$css->add_property( 'padding-top', $this->render_hero_padding( 'desktop' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.mobile-transparent-header .entry-hero .entry-hero-container-inner' );
			$css->add_property( 'padding-top', $this->render_hero_padding( 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.mobile-transparent-header .entry-hero .entry-hero-container-inner' );
			$css->add_property( 'padding-top', $this->render_hero_padding( 'mobile' ) );
			$css->stop_media_query();
		}
		// Above Title Area.
		if ( kadence()->show_hero_title() ) {
			$css->set_selector( '.wp-site-blocks .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'above_title_background', 'desktop' ), $css );
			$css->set_selector( '.wp-site-blocks .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( 'above_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'above_title_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'above_title_background', 'mobile' ), $css );
			$css->stop_media_query();
		}
		// Scroll To Top.
		if ( kadence()->option( 'scroll_up' ) ) {
			$css->set_selector( '#kt-scroll-up-reader, #kt-scroll-up' );
			$css->add_property( 'border', $css->render_border( kadence()->option( 'scroll_up_border' ) ) );
			$css->add_property( 'border-radius', $this->render_measure( kadence()->option( 'scroll_up_radius' ) ) );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'scroll_up_color', 'color' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'scroll_up_background', 'color' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'scroll_up_border_colors', 'color' ) ) );
			$css->add_property( 'bottom', $this->render_range( kadence()->option( 'scroll_up_bottom_offset' ), 'desktop' ) );
			$css->add_property( 'font-size', $this->render_range( kadence()->option( 'scroll_up_icon_size' ), 'desktop' ) );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'scroll_up_padding' ), 'desktop' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-right, #kt-scroll-up.scroll-up-side-right' );
			$css->add_property( 'right', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'desktop' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-left, #kt-scroll-up.scroll-up-side-left' );
			$css->add_property( 'left', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'desktop' ) );
			$css->set_selector( '#kt-scroll-up-reader:hover, #kt-scroll-up:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'scroll_up_color', 'hover' ) ) );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'scroll_up_background', 'hover' ) ) );
			$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'scroll_up_border_colors', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '#kt-scroll-up-reader, #kt-scroll-up' );
			$css->add_property( 'bottom', $this->render_range( kadence()->option( 'scroll_up_bottom_offset' ), 'tablet' ) );
			$css->add_property( 'font-size', $this->render_range( kadence()->option( 'scroll_up_icon_size' ), 'tablet' ) );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'scroll_up_padding' ), 'tablet' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-right, #kt-scroll-up.scroll-up-side-right' );
			$css->add_property( 'right', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'tablet' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-left, #kt-scroll-up.scroll-up-side-left' );
			$css->add_property( 'left', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '#kt-scroll-up-reader, #kt-scroll-up' );
			$css->add_property( 'bottom', $this->render_range( kadence()->option( 'scroll_up_bottom_offset' ), 'mobile' ) );
			$css->add_property( 'font-size', $this->render_range( kadence()->option( 'scroll_up_icon_size' ), 'mobile' ) );
			$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'scroll_up_padding' ), 'mobile' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-right, #kt-scroll-up.scroll-up-side-right' );
			$css->add_property( 'right', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'mobile' ) );
			$css->set_selector( '#kt-scroll-up-reader.scroll-up-side-left, #kt-scroll-up.scroll-up-side-left' );
			$css->add_property( 'left', $this->render_range( kadence()->option( 'scroll_up_side_offset' ), 'mobile' ) );
			$css->stop_media_query();
		}
		if ( is_page() ) {
			// Page Backgrounds.
			$css->set_selector( 'body.page' );
			$css->render_background( kadence()->sub_option( 'page_background', 'desktop' ), $css );
			$css->set_selector( 'body.page .content-bg, body.content-style-unboxed.page .site' );
			$css->render_background( kadence()->sub_option( 'page_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.page' );
			$css->render_background( kadence()->sub_option( 'page_background', 'tablet' ), $css );
			$css->set_selector( 'body.page .content-bg, body.content-style-unboxed.page .site' );
			$css->render_background( kadence()->sub_option( 'page_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.page' );
			$css->render_background( kadence()->sub_option( 'page_background', 'mobile' ), $css );
			$css->set_selector( 'body.page .content-bg, body.content-style-unboxed.page .site' );
			$css->render_background( kadence()->sub_option( 'page_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Page Title.
			$css->set_selector( '.wp-site-blocks .page-title h1' );
			$css->render_font( kadence()->option( 'page_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .page-title h1' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .page-title h1' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.page-title .entry-meta' );
			$css->render_font( kadence()->option( 'page_title_meta_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'page_title_meta_color', 'color' ) ) );
			$css->set_selector( '.page-title .entry-meta a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'page_title_meta_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.page-title .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_meta_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_meta_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_meta_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.page-title .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_meta_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_meta_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_meta_font' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.page-title .kadence-breadcrumbs' );
			$css->render_font( kadence()->option( 'page_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'page_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.page-title .kadence-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'page_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.page-title .kadence-breadcrumbs' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.page-title .kadence-breadcrumbs' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'page_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'page_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'page_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();

			// Above Page Title.
			$css->set_selector( '.page-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'page_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'page_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'page_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.page-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'page_title_height' ), 'desktop' ) );
			$css->set_selector( '.page-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( 'page_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.page-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'page_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'page_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'page_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.page-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'page_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.page-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'page_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'page_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'page_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.page-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'page_title_height' ), 'mobile' ) );
			$css->stop_media_query();
		}
		if ( ! get_option( 'show_avatars' ) ) {
			$css->set_selector( '.entry-author-style-center' );
			$css->add_property( 'padding-top', 'var(--global-md-spacing)' );
			$css->add_property( 'border-top', '1px solid var(--global-gray-500)' );
			$css->set_selector( '.entry-author-style-center .entry-author-avatar, .entry-meta .author-avatar' );
			$css->add_property( 'display', 'none' );
			$css->set_selector( '.entry-author-style-normal .entry-author-profile' );
			$css->add_property( 'padding-left', '0px' );
			$css->set_selector( '#comments .comment-meta' );
			$css->add_property( 'margin-left', '0px' );
		}
		if ( ! kadence()->option( 'post_comments_date' ) ) {
			$css->set_selector( '.comment-metadata a:not(.comment-edit-link), .comment-body .edit-link:before' );
			$css->add_property( 'display', 'none' );
		}
		// 404 Backgrounds.
		if ( is_404() ) {
			$css->set_selector( 'body.error404' );
			$css->render_background( kadence()->sub_option( '404_background', 'desktop' ), $css );
			$css->set_selector( 'body.error404 .content-bg, body.content-style-unboxed.error404 .site' );
			$css->render_background( kadence()->sub_option( '404_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.error404' );
			$css->render_background( kadence()->sub_option( '404_background', 'tablet' ), $css );
			$css->set_selector( 'body.error404 .content-bg, body.content-style-unboxed.error404 .site' );
			$css->render_background( kadence()->sub_option( '404_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.error404' );
			$css->render_background( kadence()->sub_option( '404_background', 'mobile' ), $css );
			$css->set_selector( 'body.error404 .content-bg, body.content-style-unboxed.error404 .site' );
			$css->render_background( kadence()->sub_option( '404_content_background', 'mobile' ), $css );
			$css->stop_media_query();
		}
		if ( is_singular( 'post' ) ) {
			// Post Backgrounds.
			$css->set_selector( 'body.single' );
			$css->render_background( kadence()->sub_option( 'post_background', 'desktop' ), $css );
			$css->set_selector( 'body.single .content-bg, body.content-style-unboxed.single .site' );
			$css->render_background( kadence()->sub_option( 'post_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single' );
			$css->render_background( kadence()->sub_option( 'post_background', 'tablet' ), $css );
			$css->set_selector( 'body.single .content-bg, body.content-style-unboxed.single .site' );
			$css->render_background( kadence()->sub_option( 'post_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single' );
			$css->render_background( kadence()->sub_option( 'post_background', 'mobile' ), $css );
			$css->set_selector( 'body.single .content-bg, body.content-style-unboxed.single .site' );
			$css->render_background( kadence()->sub_option( 'post_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Post Related Backgrounds.
			$css->set_selector( 'body.single .entry-related' );
			$css->render_background( kadence()->sub_option( 'post_related_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.single .entry-related' );
			$css->render_background( kadence()->sub_option( 'post_related_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.single .entry-related' );
			$css->render_background( kadence()->sub_option( 'post_related_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Post Related Title.
			$css->set_selector( '.wp-site-blocks .entry-related h2.entry-related-title' );
			$css->render_font( kadence()->option( 'post_related_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .entry-related h2.entry-related-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_related_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_related_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_related_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .entry-related h2.entry-related-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_related_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_related_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_related_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Post Title.
			$css->set_selector( '.wp-site-blocks .post-title h1' );
			$css->render_font( kadence()->option( 'post_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.wp-site-blocks .post-title h1' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.wp-site-blocks .post-title h1' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Post Title Categories.
			$css->set_selector( '.post-title .entry-taxonomies, .post-title .entry-taxonomies a' );
			$css->render_font( kadence()->option( 'post_title_category_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_category_color', 'color' ) ) );
			$css->set_selector( '.post-title .entry-taxonomies a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_category_color', 'hover' ) ) );
			$css->set_selector( '.post-title .entry-taxonomies .category-style-pill a' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'post_title_category_color', 'color' ) ) );
			$css->set_selector( '.post-title .entry-taxonomies .category-style-pill a:hover' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'post_title_category_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-title .entry-taxonomies' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_category_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_category_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_category_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-title .entry-taxonomies' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_category_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_category_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_category_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Post Title meta.
			$css->set_selector( '.post-title .entry-meta' );
			$css->render_font( kadence()->option( 'post_title_meta_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_meta_color', 'color' ) ) );
			$css->set_selector( '.post-title .entry-meta a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_meta_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-title .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_meta_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_meta_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_meta_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-title .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_meta_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_meta_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_meta_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Post Title Breadcrumbs.
			$css->set_selector( '.post-title .kadence-breadcrumbs' );
			$css->render_font( kadence()->option( 'post_title_breadcrumb_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.post-title .kadence-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_breadcrumb_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-title .kadence-breadcrumbs' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_breadcrumb_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_breadcrumb_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-title .kadence-breadcrumbs' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_breadcrumb_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_breadcrumb_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Post Title Excerpt.
			$css->set_selector( '.post-title .title-entry-excerpt' );
			$css->render_font( kadence()->option( 'post_title_excerpt_font' ), $css );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_excerpt_color', 'color' ) ) );
			$css->set_selector( '.post-title .title-entry-excerpt a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_title_excerpt_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-title .title-entry-excerpt' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_excerpt_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_excerpt_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_excerpt_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-title .title-entry-excerpt' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_title_excerpt_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_title_excerpt_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_title_excerpt_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Above Post Title.
			$css->set_selector( '.post-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.post-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_title_height' ), 'desktop' ) );
			$css->set_selector( '.post-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( 'post_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.post-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.post-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_title_height' ), 'mobile' ) );
			$css->stop_media_query();
		}
		if ( is_archive() || is_home() ) {
			// Above Archive Post Title.
			$css->set_selector( '.post-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_archive_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_archive_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_archive_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.post-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_archive_title_height' ), 'desktop' ) );
			$css->set_selector( '.post-archive-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( 'post_archive_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.post-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_archive_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_archive_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_archive_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.post-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_archive_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.post-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'post_archive_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'post_archive_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'post_archive_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.post-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'post_archive_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.wp-site-blocks .post-archive-title h1' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_title_color', 'color' ) ) );
			$css->set_selector( '.post-archive-title .kadence-breadcrumbs' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_title_breadcrumb_color', 'color' ) ) );
			$css->set_selector( '.post-archive-title .kadence-breadcrumbs a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_title_breadcrumb_color', 'hover' ) ) );
			$css->set_selector( '.post-archive-title .archive-description' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_title_description_color', 'color' ) ) );
			$css->set_selector( '.post-archive-title .archive-description a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_title_description_color', 'hover' ) ) );
			// Archive Backgrounds.
			$css->set_selector( 'body.archive, body.blog' );
			$css->render_background( kadence()->sub_option( 'post_archive_background', 'desktop' ), $css );
			$css->set_selector( 'body.archive .content-bg, body.content-style-unboxed.archive .site, body.blog .content-bg, body.content-style-unboxed.blog .site' );
			$css->render_background( kadence()->sub_option( 'post_archive_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.archive, body.blog' );
			$css->render_background( kadence()->sub_option( 'post_archive_background', 'tablet' ), $css );
			$css->set_selector( 'body.archive .content-bg, body.content-style-unboxed.archive .site, body.blog .content-bg, body.content-style-unboxed.blog .site' );
			$css->render_background( kadence()->sub_option( 'post_archive_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.archive, body.blog' );
			$css->render_background( kadence()->sub_option( 'post_archive_background', 'mobile' ), $css );
			$css->set_selector( 'body.archive .content-bg, body.content-style-unboxed.archive .site, body.blog .content-bg, body.content-style-unboxed.blog .site' );
			$css->render_background( kadence()->sub_option( 'post_archive_content_background', 'mobile' ), $css );
			$css->stop_media_query();
		}
		// Post archive item title.
		$css->set_selector( '.loop-entry.type-post h2.entry-title' );
		$css->render_font( kadence()->option( 'post_archive_item_title_font' ), $css, 'heading' );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.loop-entry.type-post h2.entry-title' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_title_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_title_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_title_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.loop-entry.type-post h2.entry-title' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_title_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_title_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_title_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Post archive item category.
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies' );
		$css->render_font( kadence()->option( 'post_archive_item_category_font' ), $css );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies, .loop-entry.type-post .entry-taxonomies a' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_item_category_color', 'color' ) ) );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies .category-style-pill a' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'post_archive_item_category_color', 'color' ) ) );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies a:hover' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_item_category_color', 'hover' ) ) );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies .category-style-pill a:hover' );
		$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'post_archive_item_category_color', 'hover' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_category_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_category_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_category_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.loop-entry.type-post .entry-taxonomies' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_category_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_category_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_category_font' ), 'mobile' ) );
		$css->stop_media_query();
		// Post archive item meta.
		$css->set_selector( '.loop-entry.type-post .entry-meta' );
		$css->render_font( kadence()->option( 'post_archive_item_meta_font' ), $css );
		$css->set_selector( '.loop-entry.type-post .entry-meta' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_item_meta_color', 'color' ) ) );
		$css->set_selector( '.loop-entry.type-post .entry-meta a:hover' );
		$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'post_archive_item_meta_color', 'hover' ) ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( '.loop-entry.type-post .entry-meta' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_meta_font' ), 'tablet' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_meta_font' ), 'tablet' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_meta_font' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( '.loop-entry.type-post .entry-meta' );
		$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'post_archive_item_meta_font' ), 'mobile' ) );
		$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'post_archive_item_meta_font' ), 'mobile' ) );
		$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'post_archive_item_meta_font' ), 'mobile' ) );
		$css->stop_media_query();
		if ( is_search() ) {
			// Search results Title.
			$css->set_selector( '.search-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'search_archive_title_background', 'desktop' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'search_archive_title_top_border', 'desktop' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'search_archive_title_bottom_border', 'desktop' ) ) );
			$css->set_selector( '.entry-hero.search-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'search_archive_title_height' ), 'desktop' ) );
			$css->set_selector( '.search-archive-hero-section .hero-section-overlay' );
			$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( 'search_archive_title_overlay_color', 'color' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.search-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'search_archive_title_background', 'tablet' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'search_archive_title_top_border', 'tablet' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'search_archive_title_bottom_border', 'tablet' ) ) );
			$css->set_selector( '.entry-hero.search-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'search_archive_title_height' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.search-archive-hero-section .entry-hero-container-inner' );
			$css->render_background( kadence()->sub_option( 'search_archive_title_background', 'mobile' ), $css );
			$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( 'search_archive_title_top_border', 'mobile' ) ) );
			$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( 'search_archive_title_bottom_border', 'mobile' ) ) );
			$css->set_selector( '.entry-hero.search-archive-hero-section .entry-header' );
			$css->add_property( 'min-height', $this->render_range( kadence()->option( 'search_archive_title_height' ), 'mobile' ) );
			$css->stop_media_query();
			$css->set_selector( '.search-archive-title h1' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'search_archive_title_color', 'color' ) ) );
			// Search Results Backgrounds.
			$css->set_selector( 'body.search-results' );
			$css->render_background( kadence()->sub_option( 'search_archive_background', 'desktop' ), $css );
			$css->set_selector( 'body.search-results .content-bg, body.content-style-unboxed.search-results .site' );
			$css->render_background( kadence()->sub_option( 'search_archive_content_background', 'desktop' ), $css );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( 'body.search-results' );
			$css->render_background( kadence()->sub_option( 'search_archive_background', 'tablet' ), $css );
			$css->set_selector( 'body.search-results .content-bg, body.content-style-unboxed.search-results .site' );
			$css->render_background( kadence()->sub_option( 'search_archive_content_background', 'tablet' ), $css );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( 'body.search-results' );
			$css->render_background( kadence()->sub_option( 'search_archive_background', 'mobile' ), $css );
			$css->set_selector( 'body.search-results .content-bg, body.content-style-unboxed.search-results .site' );
			$css->render_background( kadence()->sub_option( 'search_archive_content_background', 'mobile' ), $css );
			$css->stop_media_query();
			// Search Results item title.
			$css->set_selector( '.search-results .loop-entry h2.entry-title' );
			$css->render_font( kadence()->option( 'search_archive_item_title_font' ), $css, 'heading' );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.search-results .loop-entry h2.entry-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_title_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_title_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_title_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.search-results .loop-entry h2.entry-title' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_title_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_title_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_title_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Search Results item category.
			$css->set_selector( '.search-results .loop-entry .entry-taxonomies' );
			$css->render_font( kadence()->option( 'search_archive_item_category_font' ), $css );
			$css->set_selector( '.search-results .loop-entry .entry-taxonomies, .search-results .loop-entry .entry-taxonomies a' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'search_archive_item_category_color', 'color' ) ) );
			$css->set_selector( '.loop-entry .entry-taxonomies .category-style-pill a' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'search_archive_item_category_color', 'color' ) ) );
			$css->set_selector( '.search-results .loop-entry .entry-taxonomies a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'search_archive_item_category_color', 'hover' ) ) );
			$css->set_selector( '.loop-entry .entry-taxonomies .category-style-pill a:hover' );
			$css->add_property( 'background', $css->render_color( kadence()->sub_option( 'search_archive_item_category_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.search-results .loop-entry .entry-taxonomies' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_category_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_category_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_category_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.search-results .loop-entry .entry-taxonomies' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_category_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_category_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_category_font' ), 'mobile' ) );
			$css->stop_media_query();
			// Search Results item meta.
			$css->set_selector( '.search-results .loop-entry .entry-meta' );
			$css->render_font( kadence()->option( 'search_archive_item_meta_font' ), $css );
			$css->set_selector( '.search-results .loop-entry .entry-meta' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'search_archive_item_meta_color', 'color' ) ) );
			$css->set_selector( '.search-results .loop-entry .entry-meta a:hover' );
			$css->add_property( 'color', $css->render_color( kadence()->sub_option( 'search_archive_item_meta_color', 'hover' ) ) );
			$css->start_media_query( $media_query['tablet'] );
			$css->set_selector( '.search-results .loop-entry .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_meta_font' ), 'tablet' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_meta_font' ), 'tablet' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_meta_font' ), 'tablet' ) );
			$css->stop_media_query();
			$css->start_media_query( $media_query['mobile'] );
			$css->set_selector( '.search-results .loop-entry .entry-meta' );
			$css->add_property( 'font-size', $this->render_font_size( kadence()->option( 'search_archive_item_meta_font' ), 'mobile' ) );
			$css->add_property( 'line-height', $this->render_font_height( kadence()->option( 'search_archive_item_meta_font' ), 'mobile' ) );
			$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( 'search_archive_item_meta_font' ), 'mobile' ) );
			$css->stop_media_query();
		}
		if ( is_archive() || ( is_singular() && ! is_singular( 'post' ) && ! is_singular( 'page' ) && ! is_singular( 'product' ) ) ) {
			$all_post_types    = kadence()->get_post_types_objects();
			$extras_post_types = array();
			$add_extras        = false;
			foreach ( $all_post_types as $post_type_item ) {
				$post_type_name  = $post_type_item->name;
				$post_type_label = $post_type_item->label;
				$ignore_type     = kadence()->get_post_types_to_ignore();
				if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
					if ( is_singular( $post_type_name ) ) {
						// CPT Backgrounds.
						$css->set_selector( 'body.single-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_background', 'desktop' ), $css );
						$css->set_selector( 'body.single-' . $post_type_name . ' .content-bg, body.content-style-unboxed.single-' . $post_type_name . ' .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_content_background', 'desktop' ), $css );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( 'body.single-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_background', 'tablet' ), $css );
						$css->set_selector( 'body.single-' . $post_type_name . ' .content-bg, body.content-style-unboxed.single-' . $post_type_name . ' .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_content_background', 'tablet' ), $css );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( 'body.single-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_background', 'mobile' ), $css );
						$css->set_selector( 'body.single-' . $post_type_name . ' .content-bg, body.content-style-unboxed.single-' . $post_type_name . ' .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_content_background', 'mobile' ), $css );
						$css->stop_media_query();
						// CPT Title.
						$css->set_selector( '.wp-site-blocks .' . $post_type_name . '-title h1' );
						$css->render_font( kadence()->option( $post_type_name . '_title_font' ), $css, 'heading' );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.wp-site-blocks .' . $post_type_name . '-title h1' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.wp-site-blocks .' . $post_type_name . '-title h1' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT Title meta.
						$css->set_selector( '.' . $post_type_name . '-title .entry-meta' );
						$css->render_font( kadence()->option( $post_type_name . '_title_meta_font' ), $css );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_meta_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .entry-meta a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_meta_color', 'hover' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-title .entry-meta' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_meta_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_meta_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_meta_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-title .entry-meta' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_meta_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_meta_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_meta_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT Title Categories.
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies, .' . $post_type_name . '-title .entry-taxonomies a' );
						$css->render_font( kadence()->option( $post_type_name . '_title_category_font' ), $css );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_category_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_category_color', 'hover' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a' );
						$css->add_property( 'background', $css->render_color( kadence()->sub_option( $post_type_name . '_title_category_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies .category-style-pill a:hover' );
						$css->add_property( 'background', $css->render_color( kadence()->sub_option( $post_type_name . '_title_category_color', 'hover' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_category_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_category_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_category_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-title .entry-taxonomies' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_category_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_category_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_category_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT Title Breadcrumbs.
						$css->set_selector( '.' . $post_type_name . '-title .kadence-breadcrumbs' );
						$css->render_font( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), $css );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_breadcrumb_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .kadence-breadcrumbs a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_breadcrumb_color', 'hover' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-title .kadence-breadcrumbs' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-title .kadence-breadcrumbs' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_breadcrumb_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT Title Excerpt.
						$css->set_selector( '.' . $post_type_name . '-title .title-entry-excerpt' );
						$css->render_font( kadence()->option( $post_type_name . '_title_excerpt_font' ), $css );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_excerpt_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-title .title-entry-excerpt a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_title_excerpt_color', 'hover' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-title .title-entry-excerpt' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-title .title-entry-excerpt' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_title_excerpt_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT Post Title.
						$css->set_selector( '.' . $post_type_name . '-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_title_background', 'desktop' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_title_top_border', 'desktop' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_title_bottom_border', 'desktop' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_title_height' ), 'desktop' ) );
						$css->set_selector( '.' . $post_type_name . '-hero-section .hero-section-overlay' );
						$css->add_property( 'background', $css->render_color_or_gradient( kadence()->sub_option( $post_type_name . '_title_overlay_color', 'color' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_title_background', 'tablet' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_title_top_border', 'tablet' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_title_bottom_border', 'tablet' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_title_height' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_title_background', 'mobile' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_title_top_border', 'mobile' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_title_bottom_border', 'mobile' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_title_height' ), 'mobile' ) );
						$css->stop_media_query();
					}
					if ( is_archive() ) {
						// Above Archive CPT Title.
						$css->set_selector( '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_title_background', 'desktop' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_top_border', 'desktop' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_bottom_border', 'desktop' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-archive-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_archive_title_height' ), 'desktop' ) );
						$css->set_selector( '.' . $post_type_name . '-archive-hero-section .hero-section-overlay' );
						$css->add_property( 'background', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_overlay_color', 'color' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_title_background', 'tablet' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_top_border', 'tablet' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_bottom_border', 'tablet' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-archive-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_archive_title_height' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.' . $post_type_name . '-archive-hero-section .entry-hero-container-inner' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_title_background', 'mobile' ), $css );
						$css->add_property( 'border-top', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_top_border', 'mobile' ) ) );
						$css->add_property( 'border-bottom', $css->render_border( kadence()->sub_option( $post_type_name . '_archive_title_bottom_border', 'mobile' ) ) );
						$css->set_selector( '.entry-hero.' . $post_type_name . '-archive-hero-section .entry-header' );
						$css->add_property( 'min-height', $this->render_range( kadence()->option( $post_type_name . '_archive_title_height' ), 'mobile' ) );
						$css->stop_media_query();
						$css->set_selector( '.wp-site-blocks .' . $post_type_name . '-archive-title h1' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-archive-title .kadence-breadcrumbs' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_breadcrumb_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-archive-title .kadence-breadcrumbs a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_breadcrumb_color', 'hover' ) ) );
						$css->set_selector( '.' . $post_type_name . '-archive-title .archive-description' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_description_color', 'color' ) ) );
						$css->set_selector( '.' . $post_type_name . '-archive-title .archive-description a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_title_description_color', 'hover' ) ) );
						// CPT Archive Backgrounds.
						$css->set_selector( 'body.post-type-archive-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_background', 'desktop' ), $css );
						$css->set_selector( 'body.post-type-archive-' . $post_type_name . ' .content-bg, body.content-style-unboxed.post-type-archive-' . $post_type_name . ' .site, body.blog .content-bg, body.content-style-unboxed.blog .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_content_background', 'desktop' ), $css );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( 'body.post-type-archive-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_background', 'tablet' ), $css );
						$css->set_selector( 'body.post-type-archive-' . $post_type_name . ' .content-bg, body.content-style-unboxed.post-type-archive-' . $post_type_name . ' .site, body.blog .content-bg, body.content-style-unboxed.blog .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_content_background', 'tablet' ), $css );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( 'body.post-type-archive-' . $post_type_name );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_background', 'mobile' ), $css );
						$css->set_selector( 'body.post-type-archive-' . $post_type_name . ' .content-bg, body.content-style-unboxed.post-type-archive-' . $post_type_name . ' .site' );
						$css->render_background( kadence()->sub_option( $post_type_name . '_archive_content_background', 'mobile' ), $css );
						$css->stop_media_query();
						// CTP archive item title.
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' h2.entry-title' );
						$css->render_font( kadence()->option( $post_type_name . '_archive_item_title_font' ), $css, 'heading' );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' h2.entry-title' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' h2.entry-title' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_archive_item_title_font' ), 'mobile' ) );
						$css->stop_media_query();
						// CPT archive item meta.
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' .entry-meta' );
						$css->render_font( kadence()->option( $post_type_name . '_archive_item_meta_font' ), $css );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' .entry-meta' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_item_meta_color', 'color' ) ) );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' .entry-meta a:hover' );
						$css->add_property( 'color', $css->render_color( kadence()->sub_option( $post_type_name . '_archive_item_meta_color', 'hover' ) ) );
						$css->start_media_query( $media_query['tablet'] );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' .entry-meta' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'tablet' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'tablet' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'tablet' ) );
						$css->stop_media_query();
						$css->start_media_query( $media_query['mobile'] );
						$css->set_selector( '.loop-entry.type-' . $post_type_name . ' .entry-meta' );
						$css->add_property( 'font-size', $this->render_font_size( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'mobile' ) );
						$css->add_property( 'line-height', $this->render_font_height( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'mobile' ) );
						$css->add_property( 'letter-spacing', $this->render_font_spacing( kadence()->option( $post_type_name . '_archive_item_meta_font' ), 'mobile' ) );
						$css->stop_media_query();
					}
				}
			}
		}
		// Social brands.
		if ( '' !== kadence()->option( 'header_social_brand' ) || '' !== kadence()->option( 'header_mobile_social_brand' ) || '' !== kadence()->option( 'footer_social_brand' ) ) {
			$items = array();
			if ( '' !== kadence()->option( 'footer_social_brand' ) && is_array( kadence()->sub_option( 'footer_social_items', 'items' ) ) ) {
				$items = array_merge( $items, kadence()->sub_option( 'footer_social_items', 'items' ) );
			}
			if ( '' !== kadence()->option( 'header_social_brand' ) && is_array( kadence()->sub_option( 'header_social_items', 'items' ) ) ) {
				$items = array_merge( $items, kadence()->sub_option( 'header_social_items', 'items' ) );
			}
			if ( '' !== kadence()->option( 'header_mobile_social_brand' ) && is_array( kadence()->sub_option( 'header_mobile_social_items', 'items' ) ) ) {
				$items = array_merge( $items, kadence()->sub_option( 'header_mobile_social_items', 'items' ) );
			}
			$socials = array(
				'facebook'=> '#3b5998',
				'instagram'=> '#517fa4',
				'twitter'=> '#1DA1F2',
				'youtube'=> '#FF3333',
				'facebook_group'=> '#3b5998',
				'vimeo'=> '#4EBBFF',
				'pinterest'=> '#C92228',
				'linkedin'=> '#4875B4',
				'medium'=> '#181818',
				'wordpress'=> '#00749C',
				'reddit'=> '#ff4500',
				'patreon'=> '#052D49',
				'github'=> '#4078c0',
				'dribbble'=> '#EA4C89',
				'behance'=> '#1769ff',
				'vk'=> '#45668e',
				'xing'=> '#006567',
				'rss'=> '#FF6200',
				'email'=> '#181818',
				'phone'=> '#181818',
				'whatsapp'=> '#28cf54',
				'google_reviews'=> '#DB4437',
				'telegram'=> '#0088cc',
				'yelp'=> '#c41200',
				'trip_advisor'=> '#00af87',
				'imdb'=> '#F5C518',
				'soundcloud'=> '#ff7700',
				'tumblr'=> '#32506d',
				'tiktok'=> '#69C9D0',
				'discord'=> '#7289DA',
			);
			$socials_final = array();
			if ( is_array( $items ) && ! empty( $items ) ) {
				foreach ( $items as $item ) {
					if ( isset( $item['enabled'] ) && $item['enabled'] ) {
						if ( ! isset( $socials_final[ $item['id'] ] ) && isset( $socials[ $item['id'] ] ) ) {
							$socials_final[ $item['id' ] ] = $socials[ $item['id'] ];
						}
					}
				}
			}
			foreach ( $socials_final as $name => $color ) {
				$css->set_selector( 'body.social-brand-colors .social-show-brand-hover .social-link-' . $name . ':not(.ignore-brand):not(.skip):not(.ignore):hover, body.social-brand-colors .social-show-brand-until .social-link-' . $name . ':not(:hover):not(.skip):not(.ignore), body.social-brand-colors .social-show-brand-always .social-link-' . $name . ':not(.ignore-brand):not(.skip):not(.ignore)' );
				$css->add_property( 'background', $color );
				$css->set_selector( 'body.social-brand-colors .social-show-brand-hover.social-style-outline .social-link-' . $name . ':not(.ignore-brand):not(.skip):not(.ignore):hover, body.social-brand-colors .social-show-brand-until.social-style-outline .social-link-' . $name . ':not(:hover):not(.skip):not(.ignore), body.social-brand-colors .social-show-brand-always.social-style-outline .social-link-' . $name . ':not(.ignore-brand):not(.skip):not(.ignore)' );
				$css->add_property( 'color', $color );
			}
		}
		self::$google_fonts = $css->fonts_output();
		return $css->css_output();
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @return string
	 */
	public function generate_editor_css() {
		$css = new Kadence_CSS();
		$media_query            = array();
		$media_query['mobile']  = apply_filters( 'kadence_mobile_media_query', '(max-width: 767px)' );
		$media_query['tablet']  = apply_filters( 'kadence_tablet_media_query', '(max-width: 1024px)' );
		$media_query['desktop'] = apply_filters( 'kadence_desktop_media_query', '(min-width: 1025px)' );
		// Globals.
		$css->set_selector( ':root' );
		$css->add_property( '--global-palette1', kadence()->palette_option( 'palette1' ) );
		$css->add_property( '--global-palette2', kadence()->palette_option( 'palette2' ) );
		$css->add_property( '--global-palette3', kadence()->palette_option( 'palette3' ) );
		$css->add_property( '--global-palette4', kadence()->palette_option( 'palette4' ) );
		$css->add_property( '--global-palette5', kadence()->palette_option( 'palette5' ) );
		$css->add_property( '--global-palette6', kadence()->palette_option( 'palette6' ) );
		$css->add_property( '--global-palette7', kadence()->palette_option( 'palette7' ) );
		$css->add_property( '--global-palette8', kadence()->palette_option( 'palette8' ) );
		$css->add_property( '--global-palette9', kadence()->palette_option( 'palette9' ) );
		$css->add_property( '--global-palette-highlight', $css->render_color( kadence()->sub_option( 'link_color', 'highlight' ) ) );
		$css->add_property( '--global-palette-highlight-alt', $css->render_color( kadence()->sub_option( 'link_color', 'highlight-alt' ) ) );
		$css->add_property( '--global-palette-highlight-alt2', $css->render_color( kadence()->sub_option( 'link_color', 'highlight-alt2' ) ) );

		$css->add_property( '--global-palette-btn', $css->render_color( kadence()->sub_option( 'buttons_color', 'color' ) ) );
		$css->add_property( '--global-palette-btn-hover', $css->render_color( kadence()->sub_option( 'buttons_color', 'hover' ) ) );
		$css->add_property( '--global-palette-btn-bg', $css->render_color_or_gradient( kadence()->sub_option( 'buttons_background', 'color' ) ) );
		$css->add_property( '--global-palette-btn-bg-hover', $css->render_color_or_gradient( kadence()->sub_option( 'buttons_background', 'hover' ) ) );
		$css->add_property( '--global-fallback-font', apply_filters( 'kadence_theme_global_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-display-fallback-font', apply_filters( 'kadence_theme_global_display_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-body-font-family', $css->render_font_family( kadence()->option( 'base_font' ), '' ) );
		$css->add_property( '--global-heading-font-family', $css->render_font_family( kadence()->option( 'heading_font' ) ) );
		$css->add_property( '--global-content-width', kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) );
		$css->add_property( '--global-content-narrow-width', kadence()->sub_option( 'content_narrow_width', 'size' ) . kadence()->sub_option( 'content_narrow_width', 'unit' ) );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'desktop' ) );
		$css->add_property( '--global-content-wide-width', 'calc( var(--global-content-width) + 160px )' );
		$css->add_property( '--global-content-narrow-wide-width', 'calc( var(--global-content-narrow-width) + 260px )' );

		//$css->add_property( '--global-content-width', 'calc(' . kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) . ' - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );

		$css->add_property( '--global-calc-content-width', 'calc(var(--global-content-width) - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );
		$css->add_property( '--global-calc-wide-content-width', 'calc( var(--global-content-width) + 160px )' );
		$css->start_media_query( $media_query['tablet'] );
		$css->set_selector( ':root' );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->set_selector( ':root' );
		$css->add_property( '--global-content-edge-padding', $css->render_range( kadence()->option( 'content_edge_spacing' ), 'mobile' ) );
		$css->stop_media_query();

		// Fix editor:
		$css->set_selector( '.components-panel__header.edit-post-sidebar__panel-tabs ul' );
		$css->add_property( 'margin', '0px' );
		// Colors.
		$css->set_selector( ':root .has-theme-palette-1-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette1)' );
		$css->set_selector( ':root .has-theme-palette-1-color' );
		$css->add_property( 'color', 'var(--global-palette1)' );
		$css->set_selector( ':root .has-theme-palette-2-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette2)' );
		$css->set_selector( ':root .has-theme-palette-2-color' );
		$css->add_property( 'color', 'var(--global-palette2)' );

		$css->set_selector( ':root .has-theme-palette-3-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette3)' );
		$css->set_selector( ':root .has-theme-palette-3-color' );
		$css->add_property( 'color', 'var(--global-palette3)' );

		$css->set_selector( ':root .has-theme-palette-4-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette4)' );
		$css->set_selector( ':root .has-theme-palette-4-color' );
		$css->add_property( 'color', 'var(--global-palette4)' );

		$css->set_selector( ':root .has-theme-palette-5-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette5)' );
		$css->set_selector( ':root .has-theme-palette-5-color' );
		$css->add_property( 'color', 'var(--global-palette5)' );

		$css->set_selector( ':root .has-theme-palette-6-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette6)' );
		$css->set_selector( ':root .has-theme-palette-6-color' );
		$css->add_property( 'color', 'var(--global-palette6)' );

		$css->set_selector( ':root .has-theme-palette-7-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette7)' );
		$css->set_selector( ':root .has-theme-palette-7-color' );
		$css->add_property( 'color', 'var(--global-palette7)' );

		$css->set_selector( ':root .has-theme-palette-8-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette8)' );
		$css->set_selector( ':root .has-theme-palette-8-color' );
		$css->add_property( 'color', 'var(--global-palette8)' );

		$css->set_selector( ':root .has-theme-palette-9-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette9)' );
		$css->set_selector( ':root .has-theme-palette-9-color' );
		$css->add_property( 'color', 'var(--global-palette9)' );

		$css->set_selector( ':root .has-theme-palette1-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette1)' );
		$css->set_selector( ':root .has-theme-palette1-color' );
		$css->add_property( 'color', 'var(--global-palette1)' );
		$css->set_selector( ':root .has-theme-palette2-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette2)' );
		$css->set_selector( ':root .has-theme-palette2-color' );
		$css->add_property( 'color', 'var(--global-palette2)' );

		$css->set_selector( ':root .has-theme-palette3-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette3)' );
		$css->set_selector( ':root .has-theme-palette3-color' );
		$css->add_property( 'color', 'var(--global-palette3)' );

		$css->set_selector( ':root .has-theme-palette4-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette4)' );
		$css->set_selector( ':root .has-theme-palette4-color' );
		$css->add_property( 'color', 'var(--global-palette4)' );

		$css->set_selector( ':root .has-theme-palette5-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette5)' );
		$css->set_selector( ':root .has-theme-palette5-color' );
		$css->add_property( 'color', 'var(--global-palette5)' );

		$css->set_selector( ':root .has-theme-palette6-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette6)' );
		$css->set_selector( ':root .has-theme-palette6-color' );
		$css->add_property( 'color', 'var(--global-palette6)' );

		$css->set_selector( ':root .has-theme-palette7-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette7)' );
		$css->set_selector( ':root .has-theme-palette7-color' );
		$css->add_property( 'color', 'var(--global-palette7)' );

		$css->set_selector( ':root .has-theme-palette8-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette8)' );
		$css->set_selector( ':root .has-theme-palette8-color' );
		$css->add_property( 'color', 'var(--global-palette8)' );

		$css->set_selector( ':root .has-theme-palette9-background-color' );
		$css->add_property( 'background-color', 'var(--global-palette9)' );
		$css->set_selector( ':root .has-theme-palette9-color' );
		$css->add_property( 'color', 'var(--global-palette9)' );
		// Buttons.
		$css->set_selector( '.editor-styles-wrapper .wp-block-button .wp-block-button__link, .editor-styles-wrapper .kt-button.kb-btn-global-inherit' );
		$css->render_font( kadence()->option( 'buttons_typography' ), $css );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'buttons_border_radius' ), 'desktop' ) );
		$css->add_property( 'padding', $this->render_responsive_measure( kadence()->option( 'buttons_padding' ), 'desktop' ) );
		$css->set_selector( '.kt-button.kb-btn-global-outline.kb-btn-global-inherit' );
		$btn_padding = kadence()->option( 'buttons_padding' );
		if ( isset( $btn_padding['size']['desktop'] ) ) {
			$btn_size_unit   = ( isset( $btn_padding['unit'] ) && isset( $btn_padding['unit']['desktop'] ) && ! empty( $btn_padding['unit']['desktop'] ) ? $btn_padding['unit']['desktop'] : 'px' );
			if ( isset( $btn_padding['size']['desktop'][0] ) ) {
				$css->add_property( 'padding-top', 'calc(' . $btn_padding['size']['desktop'][0] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][1] ) ) {
				$css->add_property( 'padding-right', 'calc(' . $btn_padding['size']['desktop'][1] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][2] ) ) {
				$css->add_property( 'padding-bottom', 'calc(' . $btn_padding['size']['desktop'][2] . $btn_size_unit . ' - 2px)' );
			}
			if ( isset( $btn_padding['size']['desktop'][3] ) ) {
				$css->add_property( 'padding-left', 'calc(' . $btn_padding['size']['desktop'][3] . $btn_size_unit . ' - 2px)' );
			}
		}
		$css->set_selector( '.editor-styles-wrapper .wp-block-button .wp-block-button__link, .editor-styles-wrapper .kb-forms-submit, .editor-styles-wrapper .kt-button' );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'buttons_shadow' ), kadence()->default( 'buttons_shadow' ) ) );
		$css->set_selector( '.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link, .editor-styles-wrapper .kt-button.kb-btn-global-inherit' );
		$css->add_property( 'border', $css->render_responsive_border( kadence()->option( 'buttons_border' ), 'desktop' ) );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'color' ) ) );
		$css->set_selector( '.editor-styles-wrapper .wp-block-button:not(.is-style-outline) .wp-block-button__link:hover, .editor-styles-wrapper .kt-button.kb-btn-global-inherit:hover' );
		$css->add_property( 'border-color', $css->render_color( kadence()->sub_option( 'buttons_border_colors', 'hover' ) ) );
		$css->set_selector( '.editor-styles-wrapper .wp-block-button .wp-block-button__link:hover, .editor-styles-wrapper .kb-forms-submit:hover, .editor-styles-wrapper .kt-button:hover' );
		$css->add_property( 'box-shadow', $css->render_shadow( kadence()->option( 'buttons_shadow_hover' ), kadence()->default( 'buttons_shadow_hover' ) ) );
		$css->set_selector( '.editor-styles-wrapper :where(.wp-block-image) img, .editor-styles-wrapper :where(.wp-block-kadence-image) img' );
		$css->add_property( 'border-radius', $this->render_range( kadence()->option( 'image_border_radius' ), 'desktop' ) );

		$css->set_selector( '.editor-styles-wrapper .kt-button' );
		$css->render_font( kadence()->option( 'buttons_typography' ), $css );

		$css->set_selector( '.block-editor-page .editor-styles-wrapper, .editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'site_background', 'desktop' ), $css );
		$css->render_font( kadence()->option( 'base_font' ), $css );
		// FSE Specific.
		$css->set_selector( 'body.editor-styles-wrapper' );
		$css->render_font( kadence()->option( 'base_font' ), $css );
		$css->render_background( kadence()->sub_option( 'site_background', 'desktop' ), $css );
		$css->render_background( kadence()->sub_option( 'site_background', 'desktop' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'content_background', 'desktop' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'above_title_background', 'desktop' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-overlay-bg', $css->render_color_or_gradient( kadence()->sub_option( 'above_title_overlay_color', 'color' ) ) );
		$css->add_property( '--kad-editor-title-color', $css->render_color( kadence()->sub_option( 'title_above_font', 'color' ) ) );
		$css->add_property( '--kad-editor-vertical-top-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'desktop' ) );
		$css->add_property( '--kad-editor-vertical-bottom-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'desktop' ) );
		$css->start_media_query( $media_query['tablet'] );
		$css->render_background( kadence()->sub_option( 'site_background', 'tablet' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'content_background', 'tablet' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'above_title_background', 'tablet' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-vertical-top-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'tablet' ) );
		$css->add_property( '--kad-editor-vertical-bottom-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->render_background( kadence()->sub_option( 'site_background', 'mobile' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'content_background', 'mobile' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'above_title_background', 'mobile' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-vertical-top-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'mobile' ) );
		$css->add_property( '--kad-editor-vertical-bottom-padding', $css->render_range( kadence()->option( 'content_spacing' ), 'mobile' ) );
		$css->stop_media_query();
		// $css->set_selector( 'body.editor-styles-wrapper.admin-color-pcs-unboxed' );
		// $css->add_property( 'padding', '1em var(--global-content-edge-padding)' );
		// Unboxed.
		$css->set_selector( '.block-editor-page.post-content-style-unboxed .editor-styles-wrapper, .admin-color-pcs-unboxed.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'content_background', 'desktop' ), $css );
		// Page specific.
		$css->set_selector( '.block-editor-page.post-type-page .editor-styles-wrapper, .admin-color-post-type-page.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'page_site_background', 'desktop' ), $css );
		$css->render_background( kadence()->sub_option( 'page_site_background', 'desktop' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'page_content_background', 'desktop' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'page_title_background', 'desktop' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-overlay-bg', $css->render_color_or_gradient( kadence()->sub_option( 'page_title_overlay_color', 'color' ) ) );
		$css->add_property( '--kad-editor-title-color', $css->render_color( kadence()->sub_option( 'page_title_font', 'color' ) ) );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'page_title_height' ), 'desktop' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'page_title_align', 'desktop' ) );

		$css->start_media_query( $media_query['tablet'] );
		$css->render_background( kadence()->sub_option( 'page_site_background', 'tablet' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'page_content_background', 'tablet' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'page_title_background', 'tablet' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'page_title_height' ), 'tablet' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'page_title_align', 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->render_background( kadence()->sub_option( 'page_site_background', 'mobile' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'page_content_background', 'mobile' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'page_title_background', 'mobile' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'page_title_height' ), 'mobile' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'page_title_align', 'mobile' ) );
		$css->stop_media_query();

		$css->set_selector( '.block-editor-page.post-type-page.post-content-style-unboxed .editor-styles-wrapper, .admin-color-pcs-unboxed.admin-color-post-type-page.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'page_content_background', 'desktop' ), $css );
		$css->set_selector( '.block-editor-page.post-type-page.post-content-style-boxed .editor-styles-wrapper:before, .admin-color-pcs-boxed.admin-color-post-type-page.editor-styles-wrapper:before' );
		$css->render_background( kadence()->sub_option( 'page_content_background', 'desktop' ), $css );
		// Post specific.
		$css->set_selector( '.block-editor-page.post-type-post .editor-styles-wrapper, .admin-color-post-type-post.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'post_site_background', 'desktop' ), $css );
		$css->render_background( kadence()->sub_option( 'post_site_background', 'desktop' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'post_content_background', 'desktop' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'post_title_background', 'desktop' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-overlay-bg', $css->render_color_or_gradient( kadence()->sub_option( 'post_title_overlay_color', 'color' ) ) );
		$css->add_property( '--kad-editor-title-color', $css->render_color( kadence()->sub_option( 'post_title_font', 'color' ) ) );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'post_title_height' ), 'desktop' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'post_title_align', 'desktop' ) );

		$css->start_media_query( $media_query['tablet'] );
		$css->render_background( kadence()->sub_option( 'post_site_background', 'tablet' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'post_content_background', 'tablet' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'post_title_background', 'tablet' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'post_title_height' ), 'tablet' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'post_title_align', 'tablet' ) );
		$css->stop_media_query();
		$css->start_media_query( $media_query['mobile'] );
		$css->render_background( kadence()->sub_option( 'post_site_background', 'mobile' ), $css, '--kad-editor-body-bg' );
		$css->render_background( kadence()->sub_option( 'post_content_background', 'mobile' ), $css, '--kad-editor-content-bg' );
		$css->render_background( kadence()->sub_option( 'post_title_background', 'mobile' ), $css, '--kad-editor-title-bg' );
		$css->add_property( '--kad-editor-title-height', $this->render_range( kadence()->option( 'post_title_height' ), 'mobile' ) );
		$css->add_property( '--kad-editor-title-align', kadence()->sub_option( 'post_title_align', 'mobile' ) );
		$css->stop_media_query();

		$css->set_selector( '.block-editor-page.post-type-post.post-content-style-unboxed .editor-styles-wrapper, .admin-color-post-type-post.admin-color-pcs-unboxed.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'post_content_background', 'desktop' ), $css );
		$css->set_selector( '.block-editor-page.post-type-post.post-content-style-boxed .editor-styles-wrapper:before, .admin-color-post-type-post.admin-color-pcs-boxed.editor-styles-wrapper:before' );
		$css->render_background( kadence()->sub_option( 'post_content_background', 'desktop' ), $css );
		// Boxed Editor Width.
		$css->set_selector( '.block-editor-page.post-content-style-boxed .editor-styles-wrapper:before, .admin-color-pcs-boxed.editor-styles-wrapper:before' );
		$css->add_property( 'max-width', 'calc(' . kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) . ' - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );
		$css->render_background( kadence()->sub_option( 'content_background', 'desktop' ), $css );
		$css->set_selector( '.admin-color-pcs-boxed.editor-styles-wrapper:before, .admin-color-pcs-unboxed.editor-styles-wrapper' );
		$css->render_background( kadence()->sub_option( 'content_background', 'desktop' ), $css );
		// Narrow width.
		$css->set_selector( '.block-editor-page.post-content-style-boxed.post-content-width-narrow .editor-styles-wrapper:before, .admin-color-pcw-narrow.admin-color-pcs-boxed.editor-styles-wrapper:before' );
		$css->add_property( 'max-width', 'calc(var(--global-content-narrow-width) - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );
		// Sidebar Width.
		$sidebar_size = kadence()->sub_option( 'sidebar_width', 'size' );
		if ( empty( $sidebar_size ) ) {
			if ( 'px' !== kadence()->sub_option( 'content_width', 'unit' ) ) {
				$content_width = floor( kadence()->sub_option( 'content_width', 'size' ) * 17 ) - 48;
			} else {
				$content_width = kadence()->sub_option( 'content_width', 'size' ) - 48;
			}
			$sidebar_neg = floor( ( 27 / 100 ) * $content_width ) . 'px';
		} elseif ( '%' === kadence()->sub_option( 'sidebar_width', 'unit' ) ) {
			if ( 'px' !== kadence()->sub_option( 'content_width', 'unit' ) ) {
				$content_width = floor( kadence()->sub_option( 'content_width', 'size' ) * 17 ) - 48;
			} else {
				$content_width = kadence()->sub_option( 'content_width', 'size' ) - 48;
			}
			$sidebar_neg = floor( ( $sidebar_size / 100 ) * $content_width ) . 'px';
		} else {
			$sidebar_neg = $sidebar_size . kadence()->sub_option( 'sidebar_width', 'unit' );
		}
		$css->set_selector( '.block-editor-page.post-content-style-boxed.post-content-sidebar-right .editor-styles-wrapper:before, .block-editor-page.post-content-style-boxed.post-content-sidebar-left .editor-styles-wrapper:before, .admin-color-pc-sidebar-right.admin-color-pcs-boxed.editor-styles-wrapper:before, .admin-color-pc-sidebar-left.admin-color-pcs-boxed.editor-styles-wrapper:before' );
		$css->add_property( 'max-width', 'calc(' . kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) . ' - ' . $sidebar_neg . ' - 3.5rem - var(--global-content-edge-padding) - var(--global-content-edge-padding) )' );
		// Full Width.
		$css->set_selector( '.block-editor-page.post-content-style-boxed.post-content-width-fullwidth .editor-styles-wrapper:before, .admin-color-pcw-fullwidth.admin-color-pcs-boxed.editor-styles-wrapper:before' );
		$css->add_property( 'max-width', '100%' );
		// Content Editor Width.
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout.is-root-container > *, .editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper > *' );
		$css->add_property( 'max-width', 'var(--global-calc-content-width)' );
		$css->set_selector( '.editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper > [data-align="wide"], .editor-styles-wrapper .block-editor-block-list__layout.is-root-container > [data-align="wide"]' );
		$css->add_property( 'max-width', 'var(--global-content-wide-width)' );
		
		// Boxed Content Editor Width.
		$css->set_selector( '.post-content-style-boxed, .admin-color-pcs-boxed' );
		$css->add_property( '--global-calc-content-width', 'calc(var(--global-content-width) - var(--global-content-edge-padding) - var(--global-content-edge-padding) - 4rem )' );
		// Narrow Content Editor Width.
		$css->set_selector( '.post-content-width-narrow, .admin-color-pcw-narrow' );
		$css->add_property( '--global-content-width', 'var(--global-content-narrow-width)' );
		// Sidebar Content Editor Width.
		$css->set_selector( '.post-content-sidebar-right, .post-content-sidebar-left, .admin-color-pc-sidebar-right, .admin-color-pc-sidebar-left' );
		$css->add_property( '--global-content-width', 'calc(' . kadence()->sub_option( 'content_width', 'size' ) . kadence()->sub_option( 'content_width', 'unit' ) . ' - 3.5rem - ' . $sidebar_neg . ' )' );
		// Fullwidth Content Editor Width.
		$css->set_selector( '.post-content-width-fullwidth, .admin-color-pcw-fullwidth' );
		$css->add_property( '--global-calc-content-width', '100%' );
		$css->set_selector( '.post-content-width-fullwidth.post-content-style-boxed, .admin-color-pcw-fullwidth.admin-color-pcs-boxed' );
		$css->add_property( '--global-calc-content-width', 'calc( 100% - 4rem )' );
		// Kadence row theme Width.
		$css->set_selector( '.editor-styles-wrapper .wp-block-kadence-rowlayout > .innerblocks-wrap.kb-theme-content-width' );
		$css->add_property( 'padding-left', 'var(--global-content-edge-padding)' );
		$css->add_property( 'padding-right', 'var(--global-content-edge-padding)' );
		// $css->set_selector( '.post-content-style-boxed .editor-styles-wrapper .wp-block-kadence-rowlayout[data-align="full"] > .innerblocks-wrap.kb-theme-content-width' );
		// $css->add_property( 'padding-left', 'calc(var(--global-content-edge-padding) + 2rem)' );
		// $css->add_property( 'padding-right', 'calc(var(--global-content-edge-padding) + 2rem)' );
		// Align Wide Boxed.
		$css->set_selector( '.post-content-style-boxed, .admin-color-pcs-boxed' );
		$css->add_property( '--global-content-wide-width', 'calc(var(--global-content-width) + 4rem )' );
		$css->add_property( '--global-calc-wide-content-width', 'calc(var(--global-content-width) + 4rem )' );
		// Align Wide narrow boxed.
		// $css->set_selector( 'body.block-editor-page.post-content-style-boxed.post-content-width-narrow .block-editor-block-list__layout .wp-block[data-align=wide], body.block-editor-page.post-content-style-boxed.post-content-width-narrow .block-editor-block-list__layout .wp-block.alignwide' );
		// $css->add_property( 'max-width', 'calc(var(--global-content-narrow-width) - var(--global-content-edge-padding) )' );
		// Align Wide Sidebar boxed.
		$css->set_selector( 'body.block-editor-page.post-content-style-boxed.post-content-sidebar-right .block-editor-block-list__layout .wp-block[data-align=wide], body.block-editor-page.post-content-style-boxed.post-content-sidebar-right .block-editor-block-list__layout .wp-block.alignwide, body.block-editor-page.post-content-style-boxed.post-content-sidebar-left .block-editor-block-list__layout .wp-block[data-align=wide], body.block-editor-page.post-content-style-boxed.post-content-sidebar-left .block-editor-block-list__layout .wp-block.alignwide, .admin-color-pcs-boxed.admin-color-pc-sidebar-right .block-editor-block-list__layout .wp-block[data-align=wide], .admin-color-pcs-boxed.admin-color-pc-sidebar-right .block-editor-block-list__layout .wp-block.alignwide, .admin-color-pcs-boxed.admin-color-pc-sidebar-left .block-editor-block-list__layout .wp-block[data-align=wide], .admin-color-pcs-boxed.admin-color-pc-sidebar-left .block-editor-block-list__layout .wp-block.alignwide' );
		$css->add_property( 'max-width', 'calc(var(--global-content-width) - ' . $sidebar_neg . ' - 3.5em - var(--global-content-edge-padding) )' );
		// Full width.
		$css->set_selector( '.editor-styles-wrapper .wp-block[data-align="full"], .editor-styles-wrapper .wp-block.alignfull' );
		$css->add_property( 'max-width', 'none !important' );
		// $css->set_selector( 'body.block-editor-page .interface-interface-skeleton__editor' );
		// $css->add_property( 'max-width', '100%' );
		// Responsive mode
		$css->set_selector( '.block-editor-page.kadence-preview-width-tablet .editor-styles-wrapper:before' );
		$css->add_property( 'max-width', '810px !important' );
		$css->set_selector( '.block-editor-page.kadence-preview-width-mobile .editor-styles-wrapper:before' );
		$css->add_property( 'max-width', '390px !important' );
		// Heading Fonts.
		$css->set_selector( '.editor-styles-wrapper .editor-post-title .editor-post-title__input, .editor-post-title.wp-block .editor-post-title.wp-block__input, .editor-styles-wrapper .block-editor-block-list__layout h1, .editor-styles-wrapper .block-editor-block-list__layout h2, .editor-styles-wrapper .block-editor-block-list__layout h3, .editor-styles-wrapper .block-editor-block-list__layout h4, .editor-styles-wrapper .block-editor-block-list__layout h5, .editor-styles-wrapper .block-editor-block-list__layout h6' );
		$css->add_property( 'font-family', $css->render_font_family( kadence()->option( 'heading_font' ) ) );
		$css->set_selector( '.editor-styles-wrapper .editor-post-title .editor-post-title__input, .editor-styles-wrapper .block-editor-block-list__layout h1, .block-editor-page .editor-post-title.wp-block .editor-post-title.wp-block__input, .editor-styles-wrapper .edit-post-visual-editor__post-title-wrapper h1' );
		$css->render_font( kadence()->option( 'h1_font' ), $css );
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout h2' );
		$css->render_font( kadence()->option( 'h2_font' ), $css );
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout h3' );
		$css->render_font( kadence()->option( 'h3_font' ), $css );
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout h4' );
		$css->render_font( kadence()->option( 'h4_font' ), $css );
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout h5' );
		$css->render_font( kadence()->option( 'h5_font' ), $css );
		$css->set_selector( '.editor-styles-wrapper .block-editor-block-list__layout h6' );
		$css->render_font( kadence()->option( 'h6_font' ), $css );
		self::$google_fonts = $css->fonts_output();
		return $css->css_output();

	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param array $mce_init tinymce settings.
	 * @return string
	 */
	public function filter_add_tinymce_editor_styles( $mce_init ) {
		$css = new Kadence_CSS();
		$css->set_selector( ':root' );
		$css->add_property( '--global-palette1', kadence()->palette_option( 'palette1' ) );
		$css->add_property( '--global-palette2', kadence()->palette_option( 'palette2' ) );
		$css->add_property( '--global-palette3', kadence()->palette_option( 'palette3' ) );
		$css->add_property( '--global-palette4', kadence()->palette_option( 'palette4' ) );
		$css->add_property( '--global-palette5', kadence()->palette_option( 'palette5' ) );
		$css->add_property( '--global-palette6', kadence()->palette_option( 'palette6' ) );
		$css->add_property( '--global-palette7', kadence()->palette_option( 'palette7' ) );
		$css->add_property( '--global-palette8', kadence()->palette_option( 'palette8' ) );
		$css->add_property( '--global-palette9', kadence()->palette_option( 'palette9' ) );
		$css->add_property( '--global-fallback-font', apply_filters( 'kadence_theme_global_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-display-fallback-font', apply_filters( 'kadence_theme_global_display_typography_fallback', 'sans-serif' ) );
		$css->add_property( '--global-body-font-family', $css->render_font_family( kadence()->option( 'base_font' ), '' ) );
		$css->add_property( '--global-heading-font-family', $css->render_font_family( kadence()->option( 'heading_font' ) ) );
		// Body Fonts.
		$css->set_selector( 'body.mce-content-body' );
		$css->render_font_no_color( kadence()->option( 'base_font' ), $css, 'body' );
		$css->set_selector( 'body.mce-content-body h1,body.mce-content-body h2,body.mce-content-body h3,body.mce-content-body h4,body.mce-content-body h5,body.mce-content-body h6' );
		$css->add_property( 'font-family', $css->render_font_family( kadence()->option( 'heading_font' ) ) );
		$css->set_selector( 'body.mce-content-body h1' );
		$css->render_font_no_color( kadence()->option( 'h1_font' ), $css );
		$css->set_selector( 'body.mce-content-body h2' );
		$css->render_font_no_color( kadence()->option( 'h2_font' ), $css );
		$css->set_selector( 'body.mce-content-body h3' );
		$css->render_font_no_color( kadence()->option( 'h3_font' ), $css );
		$css->set_selector( 'body.mce-content-body h4' );
		$css->render_font_no_color( kadence()->option( 'h4_font' ), $css );
		$css->set_selector( 'body.mce-content-body h5' );
		$css->render_font_no_color( kadence()->option( 'h5_font' ), $css );
		$css->set_selector( 'body.mce-content-body h6' );
		$css->render_font_no_color( kadence()->option( 'h6_font' ), $css );

		if ( isset( $mce_init['content_style'] ) ) {
			$mce_init['content_style'] .= ' ' . str_replace( '"', "'", $css->css_output() ) . ' ';
		} else {
			$mce_init['content_style'] = str_replace( '"', "'", $css->css_output() ) . ' ';
		}
		return $mce_init;
	}
	/**
	 * Generates the Initial hero padding.
	 *
	 * @param string $device the target device.
	 * @return string
	 */
	public function render_hero_padding( $device ) {
		if ( 'desktop' === $device ) {
			$top_height = false;
			if ( kadence()->display_header_row( 'top' ) && $this->render_range( kadence()->sub_option( 'header_top_height' ), $device ) ) {
				$top_height = $this->render_range( kadence()->sub_option( 'header_top_height' ), $device );
			}
			$main_height = false;
			if ( kadence()->display_header_row( 'main' ) && $this->render_range( kadence()->sub_option( 'header_main_height' ), $device ) ) {
				$main_height = $this->render_range( kadence()->sub_option( 'header_main_height' ), $device );
			}
			$bottom_height = false;
			if ( kadence()->display_header_row( 'bottom' ) && $this->render_range( kadence()->sub_option( 'header_bottom_height' ), $device ) ) {
				$bottom_height = $this->render_range( kadence()->sub_option( 'header_bottom_height' ), $device );
			}
			$size_string = '';
			if ( ( $top_height && $main_height ) || ( $top_height && $bottom_height ) || ( $bottom_height && $main_height ) ) {
				$size_string = 'calc(';
			}
			if ( $top_height ) {
				$size_string .= $top_height;
				if ( $bottom_height || $main_height ) {
					$size_string .= ' + ';
				}
			}
			if ( $main_height ) {
				$size_string .= $main_height;
				if ( $bottom_height ) {
					$size_string .= ' + ';
				}
			}
			if ( $bottom_height ) {
				$size_string .= $bottom_height;
			}
			if ( ( $top_height && $main_height ) || ( $top_height && $bottom_height ) || ( $bottom_height && $main_height ) ) {
				$size_string .= ')';
			}
			return $size_string;
		} else {
			$top_height = false;
			if ( kadence()->display_mobile_header_row( 'top' ) ) {
				if ( $this->render_range( kadence()->sub_option( 'header_top_height' ), $device ) ) {
					$top_height = $this->render_range( kadence()->sub_option( 'header_top_height' ), $device );
				} elseif ( 'mobile' === $device && $this->render_range( kadence()->sub_option( 'header_top_height' ), 'tablet' ) ) {
					$top_height = $this->render_range( kadence()->sub_option( 'header_top_height' ), 'tablet' );
				} elseif ( $this->render_range( kadence()->sub_option( 'header_top_height' ), 'desktop' ) ) {
					$top_height = $this->render_range( kadence()->sub_option( 'header_top_height' ), 'desktop' );
				}
			}
			$main_height = false;
			if ( kadence()->display_mobile_header_row( 'main' ) ) {
				if ( $this->render_range( kadence()->sub_option( 'header_main_height' ), $device ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_main_height' ), $device );
				} elseif ( 'mobile' === $device && $this->render_range( kadence()->sub_option( 'header_main_height' ), 'tablet' ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_main_height' ), 'tablet' );
				} elseif ( $this->render_range( kadence()->sub_option( 'header_main_height' ), 'desktop' ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_main_height' ), 'desktop' );
				}
			}
			$bottom_height = false;
			if ( kadence()->display_mobile_header_row( 'bottom' ) ) {
				if ( $this->render_range( kadence()->sub_option( 'header_bottom_height' ), $device ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_bottom_height' ), $device );
				} elseif ( 'mobile' === $device && $this->render_range( kadence()->sub_option( 'header_bottom_height' ), 'tablet' ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_bottom_height' ), 'tablet' );
				} elseif ( $this->render_range( kadence()->sub_option( 'header_bottom_height' ), 'desktop' ) ) {
					$main_height = $this->render_range( kadence()->sub_option( 'header_bottom_height' ), 'desktop' );
				}
			}
			$size_string = '';
			if ( ( $top_height && $main_height ) || ( $top_height && $bottom_height ) || ( $bottom_height && $main_height ) ) {
				$size_string = 'calc(';
			}
			if ( $top_height ) {
				$size_string .= $top_height;
				if ( $bottom_height || $main_height ) {
					$size_string .= ' + ';
				}
			}
			if ( $main_height ) {
				$size_string .= $main_height;
				if ( $bottom_height ) {
					$size_string .= ' + ';
				}
			}
			if ( $bottom_height ) {
				$size_string .= $bottom_height;
			}
			if ( ( $top_height && $main_height ) || ( $top_height && $bottom_height ) || ( $bottom_height && $main_height ) ) {
				$size_string .= ')';
			}
			return $size_string;
		}
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_half_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = 'calc(' . $size_number . $size_unit . ' / 2)';
		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_negative_half_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = 'calc(-' . $size_number . $size_unit . ' / 2)';
		return $size_string;
	}

	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = $size_number . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array $size an array of size settings.
	 * @return string
	 */
	public function render_negative_size( $size ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		$size_number = ( isset( $size['size'] ) && ! empty( $size['size'] ) ? $size['size'] : '0' );
		$size_unit   = ( isset( $size['unit'] ) && ! empty( $size['unit'] ) ? $size['unit'] : 'em' );

		$size_string = '-' . $size_number . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the measure output.
	 *
	 * @param array $measure an array of font settings.
	 * @return string
	 */
	public function render_measure( $measure ) {
		if ( empty( $measure ) ) {
			return false;
		}
		if ( ! is_array( $measure ) ) {
			return false;
		}
		if ( ! isset( $measure['size'] ) ) {
			return false;
		}
		if ( ! is_array( $measure['size'] ) ) {
			return false;
		}
		if ( ! is_numeric( $measure['size'][0] ) && ! is_numeric( $measure['size'][0] ) && ! is_numeric( $measure['size'][0] ) && ! is_numeric( $measure['size'][0] ) ) {
			return false;
		}
		$size_unit   = ( isset( $measure['unit'] ) && ! empty( $measure['unit'] ) ? $measure['unit'] : 'px' );
		$size_string = ( is_numeric( $measure['size'][0] ) ? $measure['size'][0] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][1] ) ? $measure['size'][1] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][2] ) ? $measure['size'][2] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][3] ) ? $measure['size'][3] : '0' ) . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the measure output.
	 *
	 * @param array $measure an array of font settings.
	 * @return string
	 */
	public function render_responsive_measure( $measure, $device, $allow_part = false ) {
		if ( empty( $measure ) ) {
			return false;
		}
		if ( ! is_array( $measure ) ) {
			return false;
		}
		if ( ! isset( $measure['size'] ) ) {
			return false;
		}
		if ( ! is_array( $measure['size'] ) ) {
			return false;
		}
		if ( ! isset( $measure['size'][ $device ] ) ) {
			return false;
		}
		if ( ! is_array( $measure['size'][ $device ] ) ) {
			return false;
		}
		if ( ! $allow_part && ! is_numeric( $measure['size'][ $device ][0] ) && ! is_numeric( $measure['size'][ $device ][1] ) && ! is_numeric( $measure['size'][ $device ][2] ) && ! is_numeric( $measure['size'][ $device ][3] ) ) {
			return false;
		}
		$size_unit   = ( isset( $measure['unit'] ) && isset( $measure['unit'][ $device ] ) && ! empty( $measure['unit'][ $device ] ) ? $measure['unit'][ $device ] : 'px' );
		$size_string = ( is_numeric( $measure['size'][ $device ][0] ) ? $measure['size'][ $device ][0] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][ $device ][1] ) ? $measure['size'][ $device ][1] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][ $device ][2] ) ? $measure['size'][ $device ][2] : '0' ) . $size_unit . ' ' . ( is_numeric( $measure['size'][ $device ][3] ) ? $measure['size'][ $device ][3] : '0' ) . $size_unit;
		return $size_string;
	}
	/**
	 * Generates the font output.
	 *
	 * @param array  $font an array of font settings.
	 * @param object $css an object of css output.
	 * @param string $inherit an string to determine if the font should inherit.
	 * @return string
	 */
	public function render_font( $font, $css, $inherit = null ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( isset( $font['style'] ) && ! empty( $font['style'] ) ) {
			$css->add_property( 'font-style', $font['style'] );
		}
		if ( isset( $font['weight'] ) && ! empty( $font['weight'] ) ) {
			$css->add_property( 'font-weight', $font['weight'] );
		}
		$size_type = ( isset( $font['sizeType'] ) && ! empty( $font['sizeType'] ) ? $font['sizeType'] : 'px' );
		if ( isset( $font['size'] ) && isset( $font['size']['desktop'] ) && ! empty( $font['size']['desktop'] ) ) {
			$css->add_property( 'font-size', $font['size']['desktop'] . $size_type );
		}
		$line_type = ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) ? $font['lineType'] : '' );
		$line_type = ( '-' !== $line_type ? $line_type : '' );
		if ( isset( $font['lineHeight'] ) && isset( $font['lineHeight']['desktop'] ) && ! empty( $font['lineHeight']['desktop'] ) ) {
			$css->add_property( 'line-height', $font['lineHeight']['desktop'] . $line_type );
		}
		$letter_type = ( isset( $font['spacingType'] ) && ! empty( $font['spacingType'] ) ? $font['spacingType'] : 'em' );
		if ( isset( $font['letterSpacing'] ) && isset( $font['letterSpacing']['desktop'] ) && ! empty( $font['letterSpacing']['desktop'] ) ) {
			$css->add_property( 'letter-spacing', $font['letterSpacing']['desktop'] . $letter_type );
		}
		$family = ( isset( $font['family'] ) && ! empty( $font['family'] ) && 'inherit' !== $font['family'] ? $font['family'] : '' );
		if ( ! empty( $family ) ) {
			if ( isset( $font['google'] ) && true === $font['google'] ) {
				$fallback = ' var(--global-fallback-font)';
				if ( ! empty( $inherit ) && 'body' === $inherit ) {
					$fallback = ' var(--global-fallback-body-font)';
					$css->maybe_add_google_font( $font, $inherit );
				} else {
					$css->maybe_add_google_font( $font );
				}
				$css->add_property( 'font-family', $family + $fallback );
			} else {
				$css->add_property( 'font-family', $family );
			}
		}
		if ( isset( $font['transform'] ) && ! empty( $font['transform'] ) ) {
			$css->add_property( 'text-transform', $font['transform'] );
		}
		if ( isset( $font['color'] ) && ! empty( $font['color'] ) ) {
			$css->add_property( 'color', $css->render_color( $font['color'] ) );
		}
	}
	/**
	 * Generates the font family output.
	 *
	 * @param array $font an array of font settings.
	 * @return string
	 */
	public function render_font_family( $font, $area = 'headers' ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['family'] ) ) {
			return false;
		}
		if ( empty( $font['family'] ) ) {
			return false;
		}
		if ( 'inherit' === $font['family'] ) {
			$font_string = 'inherit';
		} else {
			$font_string = $font['family'];
		}
		if ( isset( $font['google'] ) && true === $font['google'] ) {
			$this->maybe_add_google_font( $font, $area );
		}

		return $font_string;
	}
	/**
	 * Generates the font size output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_size( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['size'] ) ) {
			return false;
		}
		if ( ! is_array( $font['size'] ) ) {
			return false;
		}
		if ( ! isset( $font['size'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['size'][ $device ] ) ) {
			return false;
		}
		$font_string = $font['size'][ $device ] . ( isset( $font['sizeType'] ) && ! empty( $font['sizeType'] ) ? $font['sizeType'] : 'px' );

		return $font_string;
	}
	/**
	 * Generates the font height output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_height( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['lineHeight'] ) ) {
			return false;
		}
		if ( ! is_array( $font['lineHeight'] ) ) {
			return false;
		}
		if ( ! isset( $font['lineHeight'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['lineHeight'][ $device ] ) ) {
			return false;
		}
		if ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) && '-' === $font['lineType'] ) {
			$font['lineType'] = '';
		}
		$font_string = $font['lineHeight'][ $device ] . ( isset( $font['lineType'] ) && ! empty( $font['lineType'] ) ? $font['lineType'] : '' );

		return $font_string;
	}
	/**
	 * Generates the font spacing output.
	 *
	 * @param array  $font an array of font settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_font_spacing( $font, $device ) {
		if ( empty( $font ) ) {
			return false;
		}
		if ( ! is_array( $font ) ) {
			return false;
		}
		if ( ! isset( $font['letterSpacing'] ) ) {
			return false;
		}
		if ( ! is_array( $font['letterSpacing'] ) ) {
			return false;
		}
		if ( ! isset( $font['letterSpacing'][ $device ] ) ) {
			return false;
		}
		if ( empty( $font['letterSpacing'][ $device ] ) ) {
			return false;
		}
		$font_string = $font['letterSpacing'][ $device ] . ( isset( $font['spacingType'] ) && ! empty( $font['spacingType'] ) ? $font['spacingType'] : 'em' );

		return $font_string;
	}

	/**
	 * Generates the color output.
	 *
	 * @param string $color any color attribute.
	 * @return string
	 */
	public function render_color( $color ) {
		if ( empty( $color ) ) {
			return false;
		}
		if ( ! is_array( $color ) && strpos( $color, 'palette' ) !== false ) {
			$color = 'var(--global-' . $color . ')';
		}
		return $color;
	}
	/**
	 * Generates the size output.
	 *
	 * @param array  $size an array of size settings.
	 * @param string $device the device this is showing on.
	 * @return string
	 */
	public function render_range( $size, $device ) {
		if ( empty( $size ) ) {
			return false;
		}
		if ( ! is_array( $size ) ) {
			return false;
		}
		if ( ! isset( $size['size'] ) ) {
			return false;
		}
		if ( ! is_array( $size['size'] ) ) {
			return false;
		}
		if ( ! isset( $size['size'][ $device ] ) ) {
			return false;
		}
		if ( ! is_numeric( $size['size'][ $device ] ) ) {
			return false;
		}
		$size_type   = ( isset( $size['unit'] ) && is_array( $size['unit'] ) && isset( $size['unit'][ $device ] ) && ! empty( $size['unit'][ $device ] ) ? $size['unit'][ $device ] : 'px' );
		$size_string = $size['size'][ $device ] . $size_type;

		return $size_string;
	}
	/**
	 * Preloads in-body stylesheets depending on what templates are being used.
	 *
	 * Only stylesheets that have a 'preload_callback' provided will be considered. If that callback evaluates to true
	 * for the current request, the stylesheet will be preloaded.
	 *
	 * Preloading is disabled when AMP is active, as AMP injects the stylesheets inline.
	 *
	 * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Preloading_content
	 */
	public function action_preload_styles() {

		// If preloading styles is disabled, return early.
		if ( ! $this->preloading_styles_enabled() ) {
			return;
		}

		$wp_styles = wp_styles();

		$css_files = $this->get_css_files();
		foreach ( $css_files as $handle => $data ) {

			// Skip if stylesheet not registered.
			if ( ! isset( $wp_styles->registered[ $handle ] ) ) {
				continue;
			}

			// Skip if no preload callback provided.
			if ( ! is_callable( $data['preload_callback'] ) ) {
				continue;
			}

			// Skip if preloading is not necessary for this request.
			if ( ! call_user_func( $data['preload_callback'] ) ) {
				continue;
			}

			$preload_uri = $wp_styles->registered[ $handle ]->src . '?ver=' . $wp_styles->registered[ $handle ]->ver;

			echo '<link rel="preload" id="' . esc_attr( $handle ) . '-preload" href="' . esc_url( $preload_uri ) . '" as="style">';
			echo "\n";
		}
	}

	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_editor_styles() {
		// Enqueue block editor stylesheet.
		add_editor_style( 'assets/css/editor/editor-styles.min.css' );
	}
	/**
	 * Generates the dynamic css based on customizer options.
	 *
	 * @param string $css any custom css.
	 * @return string
	 */
	public function editor_dynamic_css( $css ) {
		$generated_css = $this->generate_editor_css();
		if ( ! empty( $generated_css ) ) {
			$css .= "\n/* Kadence Dynamic CSS */\n" . $generated_css;
		}
		return $css;
	}

	/**
	 * Enqueues WordPress theme styles for the editor.
	 */
	public function action_add_gutenberg_styles() {
		wp_register_style( 'kadence-editor-global', false, array(), KADENCE_VERSION );
		//wp_enqueue_style( 'kadence-editor-global' );
		wp_add_inline_style( 'kadence-editor-global', trim( apply_filters( 'kadence_editor_dynamic_css', '' ) ) );
		// Enqueue Google Fonts.
		$google_fonts_url = $this->get_google_fonts_url();
		if ( ! empty( $google_fonts_url ) ) {
			if ( kadence()->option( 'load_fonts_local' ) || class_exists( 'Extendify' ) ) {
				wp_register_style(
					'kadence-google-fonts',
					get_webfont_url( $google_fonts_url ),
					array(),
					KADENCE_VERSION
				);
			} else {
				wp_register_style( 'kadence-google-fonts', $google_fonts_url, array(), KADENCE_VERSION  );
			}
		}
	}
	/**
	 * Connects theme styles to core block style so it loads in full size editing context.
	 * This is a workaround so dynamic css can be loaded in Iframe and FSE mode.
	 */
	public function update_block_style_dependencies() {
		$wp_styles = wp_styles();
		$style     = $wp_styles->query( 'wp-block-library', 'registered' );
		if ( ! $style ) {
			return;
		}
		if (
			wp_style_is( 'kadence-editor-global', 'registered' ) &&
			! in_array( 'kadence-editor-global', $style->deps, true )
		) {
			$style->deps[] = 'kadence-editor-global';
		}
		if (
			wp_style_is( 'kadence-google-fonts', 'registered' ) &&
			! in_array( 'kadence-google-fonts', $style->deps, true )
		) {
			$style->deps[] = 'kadence-google-fonts';
		}
	}
	/**
	 * Adds preconnect resource hint for Google Fonts.
	 *
	 * @param array  $urls          URLs to print for resource hints.
	 * @param string $relation_type The relation type the URLs are printed.
	 * @return array URLs to print for resource hints.
	 */
	public function filter_resource_hints( array $urls, string $relation_type ) : array {
		if ( 'preconnect' === $relation_type && wp_style_is( 'kadence-fonts', 'queue' ) ) {
			$urls[] = array(
				'href' => 'https://fonts.gstatic.com',
				'crossorigin',
			);
		}

		return $urls;
	}

	/**
	 * Prints stylesheet link tags directly.
	 *
	 * This should be used for stylesheets that aren't global and thus should only be loaded if the HTML markup
	 * they are responsible for is actually present. Template parts should use this method when the related markup
	 * requires a specific stylesheet to be loaded. If preloading stylesheets is disabled, this method will not do
	 * anything.
	 *
	 * If the `<link>` tag for a given stylesheet has already been printed, it will be skipped.
	 *
	 * @param string ...$handles One or more stylesheet handles.
	 */
	public function print_styles( string ...$handles ) {

		// If preloading styles is disabled (and thus they have already been enqueued), return early.
		if ( ! $this->preloading_styles_enabled() ) {
			return;
		}

		$css_files = $this->get_css_files();
		$handles   = array_filter(
			$handles,
			function( $handle ) use ( $css_files ) {
				$is_valid = isset( $css_files[ $handle ] ) && ! $css_files[ $handle ]['global'];
				if ( ! $is_valid ) {
					/* translators: %s: stylesheet handle */
					_doing_it_wrong( __CLASS__ . '::print_styles()', esc_html( sprintf( __( 'Invalid theme stylesheet handle: %s', 'kadence' ), $handle ) ), 'Kadence 1.0.0' );
				}
				return $is_valid;
			}
		);

		if ( empty( $handles ) ) {
			return;
		}

		wp_print_styles( $handles );
	}

	/**
	 * Determines whether to preload stylesheets and inject their link tags directly within the page content.
	 *
	 * Using this technique generally improves performance, however may not be preferred under certain circumstances.
	 * For example, since AMP will include all style rules directly in the head, it must not be used in that context.
	 * By default, this method returns true unless the page is being served in AMP. The
	 * {@see 'kadence_preloading_styles_enabled'} filter can be used to tweak the return value.
	 *
	 * @return bool True if preloading stylesheets and injecting them is enabled, false otherwise.
	 */
	protected function preloading_styles_enabled() {
		$preloading_styles_enabled = ! kadence()->is_amp();

		if ( $preloading_styles_enabled ) {
			$preloading_styles_enabled = kadence()->option( 'enable_preload' );
		}

		/**
		 * Filters whether to preload stylesheets and inject their link tags within the page content.
		 *
		 * @param bool $preloading_styles_enabled Whether preloading stylesheets and injecting them is enabled.
		 */
		return apply_filters( 'kadence_preloading_styles_enabled', $preloading_styles_enabled );
	}

	/**
	 * Gets all CSS files.
	 *
	 * @return array Associative array of $handle => $data pairs.
	 */
	protected function get_css_files() : array {
		if ( is_array( $this->css_files ) ) {
			return $this->css_files;
		}

		$css_files = array(
			'kadence-global'     => array(
				'file'   => 'global.min.css',
				'global' => true,
			),
			'kadence-rtl'   => array(
				'file'   => 'rtl.min.css',
				'global' => is_rtl(),
			),
			'kadence-simplelightbox-css' => array(
				'file'   => 'simplelightbox.min.css',
				'global' => kadence()->option( 'lightbox' ),
			),
			'kadence-header'    => array(
				'file'             => 'header.min.css',
				'preload_callback' => function() {
					return kadence()->has_header();
				},
			),
			'kadence-content'    => array(
				'file'             => 'content.min.css',
				'preload_callback' => function() {
					return kadence()->has_content();
				},
			),
			'kadence-comments'   => array(
				'file'             => 'comments.min.css',
				'preload_callback' => function() {
					return apply_filters( 'kadence_theme_enable_comment_css', kadence()->show_comments() );
				},
			),
			'kadence-sidebar'    => array(
				'file'             => 'sidebar.min.css',
				'preload_callback' => function() {
					return kadence()->has_sidebar();
				},
			),
			'kadence-author-box'   => array(
				'file'             => 'author-box.min.css',
				'preload_callback' => function() {
					return apply_filters( 'kadence_theme_enable_author_box_css', is_single() && kadence()->option( 'post_author_box' ) );
				},
			),
			'kadence-related-posts'   => array(
				'file'             => 'related-posts.min.css',
				'preload_callback' => function() {
					return is_single() && kadence()->option( 'post_related' );
				},
			),
			'kad-splide'   => array(
				'file'             => 'kadence-splide.min.css',
				'preload_callback' => function() {
					return is_single() && kadence()->option( 'post_related' );
				},
			),
			'kadence-woocommerce'    => array(
				'file'   => 'woocommerce.min.css',
				'global' => class_exists( 'woocommerce' ),
			),
			'kadence-account-woocommerce'    => array(
				'file'   => 'woocommerce-account.min.css',
				'global' => class_exists( 'woocommerce' ) && is_account_page(),
			),
			'kadence-heroic'    => array(
				'file'   => 'heroic-knowledge-base.min.css',
				'global' => class_exists( 'HT_Knowledge_Base' ),
			),
			'kadence-footer'    => array(
				'file'             => 'footer.min.css',
				'preload_callback' => function() {
					return kadence()->has_footer();
				},
			),
		);

		/**
		 * Filters default CSS files.
		 *
		 * @param array $css_files Associative array of CSS files, as $handle => $data pairs.
		 *                         $data must be an array with keys 'file' (file path relative to 'assets/css'
		 *                         directory), and optionally 'global' (whether the file should immediately be
		 *                         enqueued instead of just being registered) and 'preload_callback' (callback)
		 *                         function determining whether the file should be preloaded for the current request).
		 */
		$css_files = apply_filters( 'kadence_css_files', $css_files );

		$this->css_files = array();
		foreach ( $css_files as $handle => $data ) {
			if ( is_string( $data ) ) {
				$data = array( 'file' => $data );
			}

			if ( empty( $data['file'] ) ) {
				continue;
			}

			$this->css_files[ $handle ] = array_merge(
				array(
					'global'           => false,
					'preload_callback' => null,
					'media'            => 'all',
				),
				$data
			);
		}

		return $this->css_files;
	}

	/**
	 * Add google font to array.
	 *
	 * @param array  $font the font settings.
	 * @param string $full the font use case.
	 */
	public function maybe_add_google_font( $font, $full = null ) {
		if ( ! empty( $full ) && 'headers' === $full ) {
			$new_variant = array();
			if ( isset( $font['variant'] ) && ! empty( $font['variant'] ) && is_array( $font['variant'] ) ) {
				foreach ( array( 'h1_font', 'h2_font', 'h3_font', 'h4_font', 'h5_font', 'h6_font' ) as $option ) {
					$variant = kadence()->sub_option( $option, 'variant' );
					if ( in_array( $variant, $font['variant'], true ) && ! in_array( $variant, $new_variant, true ) ) {
						array_push( $new_variant, $variant );
					}
				}
			}
			if ( empty( $new_variant ) ) {
				$new_variant = $font['variant'];
			}
		}
		if ( ! empty( $full ) && 'body' === $full && 'inherit' === kadence()->sub_option( 'heading_font', 'family' ) ) {
			$new_variant = array( $font['variant'] );
			if ( isset( $font['variant'] ) && ! empty( $font['variant'] ) && ! is_array( $font['variant'] ) ) {
				$current_variant = array( $font['variant'] );
				foreach ( array( 'h1_font', 'h2_font', 'h3_font', 'h4_font', 'h5_font', 'h6_font' ) as $option ) {
					$variant = kadence()->sub_option( $option, 'variant' );
					if ( ! in_array( $variant, $current_variant, true ) && ! in_array( $variant, $new_variant, true ) ) {
						array_push( $new_variant, $variant );
					}
				}
			}
			if ( empty( $new_variant ) ) {
				$new_variant = array( $font['variant'] );
			}
		} else if ( ! empty( $full ) && 'body' === $full && 'inherit' !== kadence()->sub_option( 'heading_font', 'family' ) ) {
			$new_variant = array( $font['variant'], '700' );
		}
				// Check if the font has been added yet.
		if ( ! array_key_exists( $font['family'], self::$google_fonts ) ) {
			if ( ! empty( $full ) && 'headers' === $full ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else if ( ! empty( $full ) && 'body' === $full && 'inherit' === kadence()->sub_option( 'heading_font', 'family' ) ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else if ( ! empty( $full ) && 'body' === $full && 'inherit' !== kadence()->sub_option( 'heading_font', 'family' ) ) {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $new_variant ) && ! empty( $new_variant ) && is_array( $new_variant ) ? $new_variant : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			} else {
				$add_font = array(
					'fontfamily'   => $font['family'],
					'fontvariants' => ( isset( $font['variant'] ) && ! empty( $font['variant'] ) ? array( $font['variant'] ) : array() ),
					'fontsubsets'  => ( isset( $font['subset'] ) && ! empty( $font['subset'] ) ? array( $font['subset'] ) : array() ),
				);
			}
			self::$google_fonts[ $font['family'] ] = $add_font;
		} else {
			if ( ! empty( $full ) ) {
				foreach ( $new_variant as $variant ) {
					if ( ! in_array( $variant, self::$google_fonts[ $font['family'] ]['fontvariants'], true ) ) {
						array_push( self::$google_fonts[ $font['family'] ]['fontvariants'], $variant );
					}
				}
			} else {
				if ( ! in_array( $font['variant'], self::$google_fonts[ $font['family'] ]['fontvariants'], true ) ) {
					array_push( self::$google_fonts[ $font['family'] ]['fontvariants'], $font['variant'] );
				}
			}
		}
	}
	/**
	 * Load the front end Google Fonts
	 */
	public function get_google_fonts_url() {
		$google_fonts = apply_filters( 'kadence_theme_google_fonts_array', self::$google_fonts );
		if ( empty( $google_fonts ) ) {
			return '';
		}
		if ( ! apply_filters( 'kadence_print_google_fonts', true ) ) {
			return '';
		}
		$should_output = false;
		$link    = '';
		$sub_add = array();
		$subsets = kadence()->option( 'google_subsets' );
		foreach ( $google_fonts as $key => $gfont_values ) {
			if ( ! empty( $gfont_values['fontfamily'] ) ) {
				if ( ! empty( $link ) ) {
					$link .= '%7C'; // Append a new font to the string.
				}
				$should_output = true;
				$link .= $gfont_values['fontfamily'];
				if ( ! empty( $gfont_values['fontvariants'] ) ) {
					$link .= ':';
					$link .= implode( ',', $gfont_values['fontvariants'] );
				}
				if ( ! empty( $gfont_values['fontsubsets'] ) && is_array( $gfont_values['fontsubsets'] ) ) {
					foreach ( $gfont_values['fontsubsets'] as $subkey ) {
						if ( ! empty( $subkey ) && ! in_array( $subkey, $sub_add ) ) {
							$sub_add[] = $subkey;
						}
					}
				}
			}
		}
		if ( ! $should_output ) {
			return '';
		}
		$args = array(
			'family' => $link,
		);
		if ( ! empty( $subsets ) ) {
			$available = array( 'latin-ext', 'cyrillic', 'cyrillic-ext', 'greek', 'greek-ext', 'vietnamese', 'arabic', 'khmer', 'chinese', 'chinese-simplified', 'tamil', 'bengali', 'devanagari', 'hebrew', 'korean', 'thai', 'telugu' );
			foreach ( $subsets as $key => $enabled ) {
				if ( $enabled && in_array( $key, $available, true ) ) {
					if ( 'chinese' === $key ) {
						$key = 'chinese-traditional';
					}
					if ( ! in_array( $key, $sub_add ) ) {
						$sub_add[] = $key;
					}
				}
			}
			if ( $sub_add ) {
				$args['subset'] = implode( ',', $sub_add );
			}
		}
		if ( apply_filters( 'kadence_display_swap_google_fonts', true ) ) {
			$args['display'] = 'swap';
		}
		$font_url = add_query_arg( apply_filters( 'kadence_theme_google_fonts_query_args', $args ), 'https://fonts.googleapis.com/css' );
		return $font_url;
	}
}
