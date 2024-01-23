<?php
/**
 * Astra Theme Options
 *
 * @package     Astra
 * @author      Astra
 * @copyright   Copyright (c) 2020, Astra
 * @link        https://wpastra.com/
 * @since       Astra 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Theme Options
 */
if ( ! class_exists( 'Astra_Theme_Options' ) ) {
	/**
	 * Theme Options
	 */
	class Astra_Theme_Options {

		/**
		 * Class instance.
		 *
		 * @access private
		 * @var $instance Class instance.
		 */
		private static $instance;

		/**
		 * Customizer defaults.
		 *
		 * @access Private
		 * @since 1.4.3
		 * @var Array
		 */
		private static $defaults;

		/**
		 * Post id.
		 *
		 * @var $instance Post id.
		 */
		public static $post_id = null;

		/**
		 * A static option variable.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var mixed $db_options
		 */
		private static $db_options;

		/**
		 * A static option variable.
		 *
		 * @since 1.0.0
		 * @access private
		 * @var mixed $db_options
		 */
		private static $db_options_no_defaults;

		/**
		 * A static theme astra-options variable.
		 *
		 * @since 4.0.2
		 * @access public
		 * @var mixed $astra_options
		 */
		public static $astra_options = null;

		/**
		 * Initiator
		 */
		public static function get_instance() {
			if ( ! isset( self::$instance ) ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * Constructor
		 */
		public function __construct() {

			// Refresh options variables after customizer save.
			add_action( 'after_setup_theme', array( $this, 'refresh' ) );

		}

		/**
		 * Set default theme option values
		 *
		 * @since 1.0.0
		 * @return default values of the theme.
		 */
		public static function defaults() {

			if ( ! is_null( self::$defaults ) ) {
				return self::$defaults;
			}

			$palette_css_var_prefix = Astra_Global_Palette::get_css_variable_prefix();
			/**
			 * Update Astra customizer default values. To not update directly on existing users site, added backwards.
			 *
			 * @since 3.6.3
			 */
			$apply_new_default_values = astra_button_default_padding_updated();

			/**
			 * Update Astra customizer default values. To not update directly on existing users site, added backwards.
			 *
			 * @since 4.5.2
			 */
			$apply_scndry_default_padding_values = astra_scndry_btn_default_padding();

			/**
			 * Update Astra default color and typography values. To not update directly on existing users site, added backwards.
			 *
			 * @since 4.0.0
			 */
			$apply_new_default_color_typo_values = Astra_Dynamic_CSS::astra_check_default_color_typo();

			$astra_options = self::get_astra_options();

			// Defaults list of options.
			self::$defaults = apply_filters(
				'astra_theme_defaults',
				array(
					// Blog Single.
					'blog-single-width'                    => 'default',
					'blog-single-max-width'                => 1200,

					// Blog.
					'blog-post-structure'                  => array(
						'image',
						'title-meta',
					),
					'blog-width'                           => 'default',
					'blog-meta-date-type'                  => 'published',
					'blog-meta-date-format'                => '',
					'blog-max-width'                       => 1200,
					'blog-post-content'                    => 'excerpt',
					'blog-meta'                            => array(
						'comments',
						'category',
						'author',
					),
					// Colors.
					'text-color'                           => 'var(' . $palette_css_var_prefix . '3)',
					'link-color'                           => 'var(' . $palette_css_var_prefix . '0)',
					'theme-color'                          => 'var(' . $palette_css_var_prefix . '0)',
					'link-h-color'                         => 'var(' . $palette_css_var_prefix . '1)',
					'heading-base-color'                   => 'var(' . $palette_css_var_prefix . '2)',
					'border-color'                         => 'var(' . $palette_css_var_prefix . '6)',

					// Footer Bar Background.
					'footer-bg-obj'                        => array(
						'background-color'      => '',
						'background-image'      => '',
						'background-repeat'     => 'repeat',
						'background-position'   => 'center center',
						'background-size'       => 'auto',
						'background-attachment' => 'scroll',
						'background-type'       => '',
						'background-media'      => '',
						'overlay-type'          => '',
						'overlay-color'         => '',
						'overlay-gradient'      => '',
					),
					'footer-color'                         => '',
					'footer-link-color'                    => '',
					'footer-link-h-color'                  => '',

					// Footer Widgets Background.
					'footer-adv-bg-obj'                    => array(
						'background-color'      => '',
						'background-image'      => '',
						'background-repeat'     => 'repeat',
						'background-position'   => 'center center',
						'background-size'       => 'auto',
						'background-attachment' => 'scroll',
						'background-type'       => '',
						'background-media'      => '',
						'overlay-type'          => '',
						'overlay-color'         => '',
						'overlay-gradient'      => '',
					),
					'footer-adv-text-color'                => '',
					'footer-adv-link-color'                => '',
					'footer-adv-link-h-color'              => '',
					'footer-adv-wgt-title-color'           => '',

					// Buttons.
					'button-color'                         => '',
					'button-h-color'                       => '',
					'button-bg-color'                      => '',
					'button-bg-h-color'                    => '',
					'secondary-button-bg-h-color'          => '',
					'secondary-button-bg-color'            => '',
					'secondary-button-color'               => '',
					'secondary-button-h-color'             => '',
					'theme-button-padding'                 => array(
						'desktop'      => array(
							'top'    => $apply_new_default_values ? 15 : 10,
							'right'  => $apply_new_default_values ? 30 : 40,
							'bottom' => $apply_new_default_values ? 15 : 10,
							'left'   => $apply_new_default_values ? 30 : 40,
						),
						'tablet'       => array(
							'top'    => $apply_new_default_values ? 14 : '',
							'right'  => $apply_new_default_values ? 28 : '',
							'bottom' => $apply_new_default_values ? 14 : '',
							'left'   => $apply_new_default_values ? 28 : '',
						),
						'mobile'       => array(
							'top'    => $apply_new_default_values ? 12 : '',
							'right'  => $apply_new_default_values ? 24 : '',
							'bottom' => $apply_new_default_values ? 12 : '',
							'left'   => $apply_new_default_values ? 24 : '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'secondary-theme-button-padding'       => array(
						'desktop'      => array(
							'top'    => $apply_scndry_default_padding_values ? 15 : '',
							'right'  => $apply_scndry_default_padding_values ? 30 : '',
							'bottom' => $apply_scndry_default_padding_values ? 15 : '',
							'left'   => $apply_scndry_default_padding_values ? 30 : '',
						),
						'tablet'       => array(
							'top'    => $apply_scndry_default_padding_values ? 14 : '',
							'right'  => $apply_scndry_default_padding_values ? 28 : '',
							'bottom' => $apply_scndry_default_padding_values ? 14 : '',
							'left'   => $apply_scndry_default_padding_values ? 28 : '',
						),
						'mobile'       => array(
							'top'    => $apply_scndry_default_padding_values ? 12 : '',
							'right'  => $apply_scndry_default_padding_values ? 24 : '',
							'bottom' => $apply_scndry_default_padding_values ? 12 : '',
							'left'   => $apply_scndry_default_padding_values ? 24 : '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'button-radius-fields'                 => array(
						'desktop'      => array(
							'top'    => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'right'  => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'bottom' => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'left'   => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'secondary-button-radius-fields'       => array(
						'desktop'      => array(
							'top'    => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'right'  => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'bottom' => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
							'left'   => ! isset( $astra_options['button-radius'] ) ? '' : $astra_options['button-radius'],
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'theme-button-border-group-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'secondary-theme-button-border-group-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),

					// Footer - Small.
					'footer-sml-layout'                    => 'footer-sml-layout-1',
					'footer-sml-section-1'                 => 'custom',
					'footer-sml-section-1-credit'          => __( 'Copyright &copy; [current_year] [site_title] | Powered by [theme_author]', 'astra' ),
					'footer-sml-section-2'                 => '',
					'footer-sml-section-2-credit'          => __( 'Copyright &copy; [current_year] [site_title] | Powered by [theme_author]', 'astra' ),
					'footer-sml-dist-equal-align'          => true,
					'footer-sml-divider'                   => 1,
					'footer-sml-divider-color'             => '#7a7a7a',
					'footer-layout-width'                  => 'content',
					// General.
					'ast-header-retina-logo'               => '',
					'ast-header-logo-width'                => '',
					'ast-header-responsive-logo-width'     => array(
						'desktop' => '',
						'tablet'  => '',
						'mobile'  => '',
					),
					'header-color-site-title'              => '',
					'header-color-h-site-title'            => '',
					'header-color-site-tagline'            => '',
					'display-site-title-responsive'        => array(
						'desktop' => 1,
						'tablet'  => 1,
						'mobile'  => 1,
					),
					'display-site-tagline-responsive'      => array(
						'desktop' => 0,
						'tablet'  => 0,
						'mobile'  => 0,
					),
					'logo-title-inline'                    => 1,
					// Header - Primary.
					'disable-primary-nav'                  => false,
					'header-layouts'                       => 'header-main-layout-1',
					'header-main-rt-section'               => 'none',
					'header-display-outside-menu'          => false,
					'header-main-rt-section-html'          => '<button>' . __( 'Contact Us', 'astra' ) . '</button>',
					'header-main-rt-section-button-text'   => __( 'Button', 'astra' ),
					'header-main-rt-section-button-link'   => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
					'header-main-rt-section-button-link-option' => array(
						'url'      => apply_filters( 'astra_site_url', 'https://www.wpastra.com' ),
						'new_tab'  => false,
						'link_rel' => '',
					),
					'header-main-rt-section-button-style'  => 'theme-button',
					'header-main-rt-section-button-text-color' => '',
					'header-main-rt-section-button-back-color' => '',
					'header-main-rt-section-button-text-h-color' => '',
					'header-main-rt-section-button-back-h-color' => '',
					'header-main-rt-section-button-padding' => array(
						'desktop' => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'  => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'  => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
					),
					'header-main-rt-section-button-border-size' => array(
						'top'    => '',
						'right'  => '',
						'bottom' => '',
						'left'   => '',
					),
					'header-main-sep'                      => 1,
					'header-main-sep-color'                => '',
					'header-main-layout-width'             => 'content',
					// Header - Sub menu Border.
					'primary-submenu-border'               => array(
						'top'    => '2',
						'right'  => '0',
						'bottom' => '0',
						'left'   => '0',
					),
					'primary-submenu-item-border'          => false,
					'primary-submenu-b-color'              => '',
					'primary-submenu-item-b-color'         => '',

					// Primary header button typo options.
					'primary-header-button-font-family'    => 'inherit',
					'primary-header-button-font-weight'    => 'inherit',
					'primary-header-button-font-size'      => array(
						'desktop'      => '',
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'primary-header-button-text-transform' => '',
					'primary-header-button-line-height'    => 1,
					'primary-header-button-letter-spacing' => '',

					'header-main-menu-label'               => '',
					'header-main-menu-align'               => 'inline',
					'header-main-submenu-container-animation' => '',
					'mobile-header-breakpoint'             => '',
					'mobile-header-logo'                   => '',
					'mobile-header-logo-width'             => '',
					// Site Layout.
					'site-layout'                          => 'ast-full-width-layout',
					'site-content-width'                   => 1200,
					'narrow-container-max-width'           => 750,
					'site-layout-outside-bg-obj-responsive' => array(
						'desktop' => array(
							'background-color'      => $apply_new_default_values ? 'var(--ast-global-color-4)' : '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => '',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
					),
					'content-bg-obj-responsive'            => array(
						'desktop' => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
						'tablet'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
						'mobile'  => array(
							'background-color'      => 'var(' . $palette_css_var_prefix . '5)',
							'background-image'      => '',
							'background-repeat'     => 'repeat',
							'background-position'   => 'center center',
							'background-size'       => 'auto',
							'background-attachment' => 'scroll',
							'background-type'       => '',
							'background-media'      => '',
							'overlay-type'          => '',
							'overlay-color'         => '',
							'overlay-gradient'      => '',
						),
					),
					// Entry Content.
					'wp-blocks-ui'                         => false === astra_check_is_structural_setup() ? 'custom' : 'comfort',
					'wp-blocks-global-padding'             => array(
						'desktop'      => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'em',
						'tablet-unit'  => 'em',
						'mobile-unit'  => 'em',
					),

					// Container.
					'single-page-ast-content-layout'       => false === astra_check_is_structural_setup() ? 'default' : 'normal-width-container',
					'single-page-content-style'            => false === astra_check_is_structural_setup() ? 'default' : 'unboxed',
					'single-post-ast-content-layout'       => 'default',
					'single-post-content-style'            => 'default',
					'archive-post-ast-content-layout'      => 'default',
					'ast-site-content-layout'              => 'normal-width-container',
					'site-content-style'                   => 'boxed',

					// Typography.
					'body-font-family'                     => 'inherit',
					'body-font-variant'                    => '',
					'body-font-weight'                     => $apply_new_default_color_typo_values ? '400' : 'inherit',
					'font-size-body'                       => array(
						'desktop'      => $apply_new_default_color_typo_values ? 16 : 15,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'body-font-extras'                     => array(
						'line-height'         => ! isset( $astra_options['body-font-extras'] ) && isset( $astra_options['body-line-height'] ) ? $astra_options['body-line-height'] : '1.6',
						'line-height-unit'    => 'em',
						'letter-spacing'      => '',
						'letter-spacing-unit' => 'px',
						'text-transform'      => ! isset( $astra_options['body-font-extras'] ) && isset( $astra_options['body-text-transform'] ) ? $astra_options['body-text-transform'] : '',
						'text-decoration'     => '',
					),
					'headings-font-height-settings'        => array(
						'line-height'         => ! isset( $astra_options['headings-font-extras'] ) && isset( $astra_options['headings-line-height'] ) ? $astra_options['headings-line-height'] : '',
						'line-height-unit'    => 'em',
						'letter-spacing'      => '',
						'letter-spacing-unit' => 'px',
						'text-transform'      => ! isset( $astra_options['headings-font-extras'] ) && isset( $astra_options['headings-text-transform'] ) ? $astra_options['headings-text-transform'] : '',
						'text-decoration'     => '',
					),
					'para-margin-bottom'                   => '',
					'underline-content-links'              => true,
					'site-accessibility-toggle'            => true,
					'site-accessibility-highlight-type'    => 'dotted',
					'site-accessibility-highlight-input-type' => 'disable',
					'body-text-transform'                  => '',
					'headings-font-family'                 => 'inherit',
					'headings-font-weight'                 => $apply_new_default_values ? '600' : 'inherit',
					'font-size-site-title'                 => array(
						'desktop'      => $apply_new_default_color_typo_values ? 26 : 35,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-site-tagline'               => array(
						'desktop'      => 15,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'single-post-outside-spacing'          => array(
						'desktop'      => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'tablet'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'mobile'       => array(
							'top'    => '',
							'right'  => '',
							'bottom' => '',
							'left'   => '',
						),
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-page-title'                 => array(
						'desktop'      => $apply_new_default_color_typo_values ? 26 : 30,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h1'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 40 : 40,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h2'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 32 : 30,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h3'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 26 : 25,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h4'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 24 : 20,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h5'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 20 : 18,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),
					'font-size-h6'                         => array(
						'desktop'      => $apply_new_default_color_typo_values ? 16 : 15,
						'tablet'       => '',
						'mobile'       => '',
						'desktop-unit' => 'px',
						'tablet-unit'  => 'px',
						'mobile-unit'  => 'px',
					),

					// Sidebar.
					'site-sidebar-layout'                  => false === astra_check_old_sidebar_user() ? 'right-sidebar' : 'no-sidebar',
					'site-sidebar-width'                   => 30,
					'single-page-sidebar-layout'           => false === astra_check_is_structural_setup() ? 'default' : 'no-sidebar',
					'single-post-sidebar-layout'           => 'default',
					'archive-post-sidebar-layout'          => 'default',
					'site-sticky-sidebar'                  => false,
					'site-sidebar-style'                   => 'unboxed',
					'single-page-sidebar-style'            => 'unboxed',
					'single-post-sidebar-style'            => 'default',
					'archive-post-sidebar-style'           => 'default',

					// Sidebar.
					'footer-adv'                           => 'disabled',
					'footer-adv-border-width'              => '',
					'footer-adv-border-color'              => '#7a7a7a',

					// toogle menu style.
					'mobile-header-toggle-btn-style'       => 'minimal',
					'hide-custom-menu-mobile'              => 1,

					// toogle menu target.
					'mobile-header-toggle-target'          => 'icon',

					// Misc.
					'enable-scroll-to-id'                  => true,
				)
			);

			return self::$defaults;
		}

		/**
		 * Get astra-options DB values.
		 *
		 * @return array Return array of theme options from database.
		 *
		 * @since 4.0.0
		 */
		public static function get_astra_options() {
			if ( is_null( self::$astra_options ) || is_customize_preview() ) {
				self::$astra_options = get_option( ASTRA_THEME_SETTINGS );
			}
			return self::$astra_options;
		}

		/**
		 * Get theme options from static array()
		 *
		 * @return array    Return array of theme options.
		 */
		public static function get_options() {
			return self::$db_options;
		}

		/**
		 * Update theme static option array.
		 */
		public static function refresh() {
			self::$db_options = wp_parse_args(
				self::get_db_options(),
				self::defaults()
			);
		}

		/**
		 * Get theme options from static array() from database
		 *
		 * @return array    Return array of theme options from database.
		 */
		public static function get_db_options() {
			self::$db_options_no_defaults = self::get_astra_options();
			return self::$db_options_no_defaults;
		}
	}
}
/**
 * Kicking this off by calling 'get_instance()' method
 */
Astra_Theme_Options::get_instance();
