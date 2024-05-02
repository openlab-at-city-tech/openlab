<?php
/**
 * Kadence\Editor\Component class
 *
 * @package kadence
 */

namespace Kadence\Editor;

use Kadence\Component_Interface;
use function Kadence\kadence;
use function add_action;
use function add_filter;
use function add_theme_support;

/**
 * Class for integrating with the block editor.
 *
 * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'editor';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'after_setup_theme', array( $this, 'action_add_editor_support' ) );
		add_filter( 'admin_body_class', array( $this, 'filter_admin_body_class' ) );
		remove_action( 'admin_head-post.php', 'kadence_blocks_admin_editor_width', 100 );
		remove_action( 'admin_head-post-new.php', 'kadence_blocks_admin_editor_width', 100 );
		add_filter( 'kadence_blocks_editor_width', array( $this, 'kadence_blocks_admin_editor_width_filter' ), 100 );
		add_action( 'after_setup_theme', array( $this, 'google_font_add_editor_styles' ) );
		add_filter( 'theme_file_path', array( $this, 'disable_json_optionally' ), 10, 2 );
		add_filter( 'wp_theme_json_data_theme', array( $this, 'edit_theme_json' ), 10 );
	}
	/**
	 * Edit the theme.json file optionally.
	 *
	 * @param object $theme_json the path to the theme.json file.
	 */
	public function edit_theme_json( $theme_json ) {
		$theme_mode = get_theme_mod( 'theme_json_mode', false );
		if ( ! $theme_mode ) {
			$new_data = array(
				'version'  => 2,
				"settings" => array(
					"appearanceTools" => true,
					"border" => [
						"color" => true, 
						"radius" => true, 
						"style" => true, 
						"width" => true 
					], 
					"color" => [
						"custom" => true, 
						"defaultPalette" => false, 
						"link" => true, 
						"palette" => [
							[
							"name" => "Accent", 
							"slug" => "theme-palette1", 
							"color" => "var(--global-palette1)" 
							], 
							[
								"name" => "Accent - alt", 
								"slug" => "theme-palette2", 
								"color" => "var(--global-palette2)" 
							], 
							[
								"name" => "Strongest text", 
								"slug" => "theme-palette3", 
								"color" => "var(--global-palette3)" 
							], 
							[
								"name" => "Strong Text", 
								"slug" => "theme-palette4", 
								"color" => "var(--global-palette4)" 
							], 
							[
								"name" => "Medium text", 
								"slug" => "theme-palette5", 
								"color" => "var(--global-palette5)" 
							], 
							[
								"name" => "Subtle Text", 
								"slug" => "theme-palette6", 
								"color" => "var(--global-palette6)" 
							], 
							[
								"name" => "Subtle Background", 
								"slug" => "theme-palette7", 
								"color" => "var(--global-palette7)" 
							], 
							[
								"name" => "Lighter Background", 
								"slug" => "theme-palette8", 
								"color" => "var(--global-palette8)" 
							], 
							[
								"name" => "White or offwhite", 
								"slug" => "theme-palette9", 
								"color" => "var(--global-palette9)" 
								] 
							] 
					], 
					"layout" => [
						"contentSize" => "var(--global-calc-content-width)", 
						"wideSize" => "var(--global-calc-wide-content-width)", 
						"fullSize" => "none" 
					], 
					"spacing" => [
						"blockGap" => null, 
						"margin" => true, 
						"padding" => true, 
						"units" => [
							"px", 
							"em", 
							"rem", 
							"vh", 
							"vw", 
							"%" 
						] 
					], 
					"typography" => [
					"customFontSize" => true, 
					"fontSizes" => [
						[
							"name" => "Small", 
							"slug" => "small", 
							"size" => "var(--global-font-size-small)" 
						], 
						[
							"name" => "Medium", 
							"slug" => "medium", 
							"size" => "var(--global-font-size-medium)" 
						], 
						[
						"name" => "Large", 
						"slug" => "large", 
						"size" => "var(--global-font-size-large)" 
						], 
						[
							"name" => "Larger", 
							"slug" => "larger", 
							"size" => "var(--global-font-size-larger)" 
						], 
						[
							"name" => "XX-Large", 
							"slug" => "xxlarge", 
							"size" => "var(--global-font-size-xxlarge)" 
						] 
					], 
					"lineHeight" => true 
					] 
				),
			  	"styles" => [
					"elements" => [
						"button" => [
							"spacing" => [
								"padding" => false 
							], 
							"border" => [
								"width" => false 
							],
							"color" => [
								"background" => false, 
								"text" => false 
							],
							"typography" => [
								"fontFamily" => false, 
								"fontSize" => false, 
								"lineHeight" => false, 
								"textDecoration" => false 
							]
						]
					]
				]
			);

			return $theme_json->update_with( $new_data );
		}
		return $theme_json;
	}
	/**
	 * Disables the theme.json file optionally.
	 *
	 * @param string $path the path to the theme.json file.
	 */
	public function disable_json_optionally( $path, $file ) {
		$length = strlen( 'kadence/theme.json' );
		if ( ! empty( $file ) && 'theme.json' === $file && substr( $path, -$length ) === 'kadence/theme.json' ) {
			$theme_mode = get_theme_mod( 'theme_json_mode', false );
			if ( ! $theme_mode ) {
				// Set the path to a file that doesn't exist.
				$new_path = str_replace( 'kadence/theme.json', 'kadence/missing-theme.json', $path );
				return $new_path;
			} else {
				return $path;
			}
		}
		return $path;
	}
	/**
	 * Registers an editor stylesheet for the current theme.
	 */
	public function google_font_add_editor_styles() {
		if ( kadence()->sub_option( 'base_font', 'google' ) ) {
			$font        = kadence()->option( 'base_font' );
			if ( $font['family'] ) {
				$font_string = $font['family'] . ':' . ( $font['variant'] ? $font['variant'] : '400' ) . ',700';
				if ( kadence()->sub_option( 'heading_font', 'google' ) ) {
					$heading_font = kadence()->option( 'heading_font' );
					if ( $heading_font['family'] ) {
						$font_string .= '%7C' . $heading_font['family'] . ':400,700';
					}
				}
				$font_url    = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=' . $font_string );
				add_editor_style( $font_url );
			}
		} elseif ( kadence()->sub_option( 'heading_font', 'google' ) ) {
			$font        = kadence()->option( 'heading_font' );
			if ( $font['family'] ) {
				$font_string = $font['family'] . ':400,700';
				$font_url    = str_replace( ',', '%2C', '//fonts.googleapis.com/css?family=' . $font_string );
				add_editor_style( $font_url );
			}
		}
	}
	/**
	 * Adds filter for Kadence Editor Width.
	 *
	 * @param bool $enable the editor width enable.
	 */
	public function kadence_blocks_admin_editor_width_filter( $enable ) {
		return false;
	}
	/**
	 * Adds filter for admin body class to add in layout information.
	 *
	 * @param string $classes the admin classes.
	 */
	public function filter_admin_body_class( $classes ) {
		$screen = get_current_screen();
		if ( 'post' == $screen->base ) {
			global $post;
			$post_type  = get_post_type();
			$post_layout = array(
				'layout'           => 'normal',
				'boxed'            => 'boxed',
				'feature'          => 'hide',
				'comments'         => 'hide',
				'navigation'       => 'hide',
				'title'            => 'normal',
				'sidebar'          => 'none',
				'padding'         => 'show',
			);
			$post_layout = apply_filters( 'kadence_post_layout', $post_layout );

			$postlayout   = get_post_meta( $post->ID, '_kad_post_layout', true );
			$postboxed    = get_post_meta( $post->ID, '_kad_post_content_style', true );
			$postfeature  = get_post_meta( $post->ID, '_kad_post_feature', true );
			$vpadding     = get_post_meta( $post->ID, '_kad_post_vertical_padding', true );
			$postnav      = get_post_meta( $post->ID, '_kad_post_navigation', true );
			$posttitle    = get_post_meta( $post->ID, '_kad_post_title', true );
			$postsidebar  = get_post_meta( $post->ID, '_kad_post_layout', true );
			if ( isset( $postlayout ) && ( 'left' === $postsidebar || 'right' === $postsidebar ) ) {
				$post_layout['sidebar'] = $postlayout;
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = kadence()->option( $post_type . '_layout' );
				if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$post_layout['sidebar'] = $option_layout;
				}
			}
			if ( isset( $posttitle ) && ( 'above' === $posttitle || 'normal' === $posttitle || 'hide' === $posttitle ) ) {
				$post_layout['title'] = $posttitle;
			} else {
				$option_title = kadence()->option( $post_type . '_title' );
				if ( false === $option_title ) {
					$post_layout['title'] = 'hide';
				} else {
					$option_title_layout = kadence()->option( $post_type . '_title_layout' );
					if ( 'above' === $option_title_layout || 'normal' === $option_title_layout ) {
						$post_layout['title'] = $option_title_layout;
					}
				}
			}
			if ( isset( $postnav ) && ( 'show' === $postnav || 'hide' === $postnav ) ) {
				$post_layout['navigation'] = $postnav;
			} else {
				$option_nav = kadence()->option( $post_type . '_navigation' );
				if ( $option_nav ) {
					$post_layout['navigation'] = 'show';
				}
			}
			if ( isset( $vpadding ) && ( 'show' === $vpadding || 'hide' === $vpadding ) ) {
				$post_layout['padding'] = $vpadding;
			} else {
				$option_padding = kadence()->option( $post_type . '_vertical_padding' );
				if ( $option_padding ) {
					$post_layout['padding'] = 'show';
				}
			}
			if ( isset( $postboxed ) && ( 'unboxed' === $postboxed || 'boxed' === $postboxed ) ) {
				$post_layout['boxed'] = $postboxed;
			} else {
				$option_boxed = kadence()->option( $post_type . '_content_style' );
				if ( 'unboxed' === $option_boxed || 'boxed' === $option_boxed ) {
					$post_layout['boxed'] = $option_boxed;
				}
			}
			if ( isset( $postfeature ) && ( 'show' === $postfeature || 'hide' === $postfeature ) ) {
				$post_layout['feature'] = $postfeature;
			} else {
				$option_feature = kadence()->option( $post_type . '_feature' );
				if ( $option_feature ) {
					$post_layout['feature'] = 'show';
				}
			}
			if ( isset( $postlayout ) && ( 'narrow' === $postlayout || 'fullwidth' === $postlayout ) ) {
				$post_layout['layout'] = $postlayout;
			} else if ( isset( $postlayout ) && ( 'left' === $postlayout || 'right' === $postlayout ) ) {
				$post_layout['layout'] = 'narrow';
			} elseif ( ( isset( $postlayout ) && 'default' === $postlayout ) || empty( $postlayout ) ) {
				$option_layout = kadence()->option( $post_type . '_layout' );
				if ( 'narrow' === $option_layout || 'fullwidth' === $option_layout ) {
					$post_layout['layout'] = $option_layout;
				} else if ( 'left' === $option_layout || 'right' === $option_layout ) {
					$post_layout['layout'] = 'narrow';
				}
			}
			$classes .= ' post-content-width-' . esc_attr( $post_layout['layout'] ) . ' admin-color-pcw-' . esc_attr( $post_layout['layout'] ) . ' post-content-style-' . esc_attr( $post_layout['boxed'] ) . ' admin-color-pcs-' . esc_attr( $post_layout['boxed'] ) . ' admin-color-post-type-' . esc_attr( $post_type ) . ' post-content-vertical-padding-' . esc_attr( $post_layout['padding'] ) . ' admin-color-pcvp-' . esc_attr( $post_layout['padding'] ) . ' post-content-title-' . esc_attr( $post_layout['title'] ) . ' admin-color-pct-' . esc_attr( $post_layout['title'] ) . '  post-content-sidebar-' . esc_attr( $post_layout['sidebar'] ) . ' admin-color-pc-sidebar-' . esc_attr( $post_layout['sidebar'] ) . ' ';
		}
		return $classes;
	}
	/**
	 * Adds support for various editor features.
	 */
	public function action_add_editor_support() {
		// Add support for post thumbnails.
		add_theme_support( 'post-thumbnails' );

		// Add support for editor styles.
		add_theme_support( 'editor-styles' );

		// Add support for default block styles.
		// Don't add this "add_theme_support( 'wp-block-styles' );" because theme provides it own styling.
		// add_theme_support( 'wp-block-styles' );
		// Add support for wide-aligned images.
		add_theme_support( 'align-wide' );
		add_theme_support( 'custom-spacing' );
		if ( apply_filters( 'kadence-theme-block-templates-support', true ) ) {
			add_theme_support( 'block-templates' );
		}
	}
}
