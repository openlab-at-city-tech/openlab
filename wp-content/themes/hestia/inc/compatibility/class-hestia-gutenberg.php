<?php
/**
 * Hestia Gutenberg integration handler class.
 *
 * Author:          Andrei Baicus <andrei@themeisle.com>
 * Created on:      2018-12-06
 *
 * @package hestia
 */

/**
 * Class Hestia_Gutenberg
 *
 * @since 2.0.18
 */
class Hestia_Gutenberg extends Hestia_Abstract_Main {
	/**
	 * The post ID.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var null
	 */
	private $post_id = null;

	/**
	 * Css style added inline for mobile.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $mobile_style = '';

	/**
	 * Css style added inline for tablet.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $tablet_style = '';

	/**
	 * Css style added inline for desktop.
	 *
	 * @since  2.0.18
	 * @access private
	 * @var string
	 */
	private $desktop_style = '';

	/**
	 * Initialize the compatibility module.
	 *
	 * @since  2.0.18
	 * @access public
	 * @return void
	 */
	public function init() {
		if ( apply_filters( 'hestia_filter_gutenberg_integration', true ) !== true ) {
			return;
		}
		$this->set_post_id();
		$this->run_styles();
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue' ) );
	}

	/**
	 * Set the post ID.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return int|null
	 */
	private function set_post_id() {
		if ( ! isset( $_GET['post'] ) ) {
			return null;
		}
		$this->post_id = $_GET['post'];
	}

	/**
	 * Enqueue scripts and styles.
	 *
	 * @since  2.0.18
	 * @access public
	 * @return void
	 */
	public function enqueue() {
		wp_enqueue_style( 'hestia-gutenberg-css', get_template_directory_uri() . '/assets/css/gutenberg-editor-style' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.css', array(), HESTIA_VERSION );
		wp_add_inline_style( 'hestia-gutenberg-css', $this->get_inline_style() );

		if ( $this->is_front_page() ) {
			return;
		}

		wp_register_script( 'hestia-gutenberg-js', get_template_directory_uri() . '/assets/js/admin/gutenberg-preview' . ( ( HESTIA_DEBUG ) ? '' : '.min' ) . '.js', array( 'jquery' ), HESTIA_VERSION, true );

		wp_localize_script( 'hestia-gutenberg-js', 'hestiaGtb', $this->get_localization() );

		wp_enqueue_script( 'hestia-gutenberg-js' );

	}

	/**
	 * Get the inline style string.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return string
	 */
	private function get_inline_style() {
		$css = '';

		$css .= $this->mobile_style;

		$css .= '@media( min-width: 480px ) {' . $this->tablet_style . '}';
		$css .= '@media( min-width: 768px ) {' . $this->desktop_style . '}';

		return $css;
	}

	/**
	 * Actually go through all the inline styles.
	 */
	private function run_styles() {

		// Sidebar width
		$this->set_style( 'hestia_sidebar_width', 25, '.hestia-gtb .hestia-sidebar-dummy', 'min-width', '%', 'desktop' );

		// Font families
		$this->set_style(
			'hestia_headings_font',
			false,
			'
		.editor-styles-wrapper .editor-writing-flow h1, 
		.editor-styles-wrapper .editor-writing-flow h2, 
		.editor-styles-wrapper .editor-writing-flow h3, 
		.editor-styles-wrapper .editor-writing-flow h4, 
		.editor-styles-wrapper .editor-writing-flow h5,
		.editor-styles-wrapper .editor-writing-flow h6,
		.editor-styles-wrapper .editor-post-title__block .editor-post-title__input,
		.editor-styles-wrapper.header-default .editor-post-title__block .editor-post-title__input',
			'font-family'
		);
		$this->set_style( 'hestia_body_font', false, '.editor-styles-wrapper .editor-writing-flow, .editor-default-block-appender textarea.editor-default-block-appender__content', 'font-family' );

		// Container width
		$container_selector = '
			.sidebar-full-width .wp-block:not([data-align=wide]):not([data-align=full]),
			.header-default:not(.sidebar-full-width) .hestia-block-list-wrap,
			.header-no-content:not(.sidebar-full-width) .hestia-writing-flow-inside,
			.header-classic-blog:not(.sidebar-full-width) .hestia-writing-flow-inside';
		$this->set_responsive_style( 'hestia_container_width', false, $container_selector, 'max-width', 'px' );

		// Header gradient
		$header_gradient = get_theme_mod( 'hestia_header_gradient_color', apply_filters( 'hestia_header_gradient_default', '#a81d84' ) );

		// Add gradient color
		$this->add_css( '.editor-styles-wrapper.header-default .editor-writing-flow .hestia-featured-background.title-container,.editor-styles-wrapper .no-content', 'linear-gradient(45deg, ' . hestia_hex_rgba( $header_gradient ) . ' 0%, ' . hestia_generate_gradient_color( $header_gradient ) . ' 100%)', 'background', 'no-repeat center/cover' );

		// Accent color
		$this->set_style( 'accent_color', apply_filters( 'hestia_accent_color_default', '#e91e63' ), '.editor-styles-wrapper .editor-writing-flow a', 'color' );
		// Header text color
		$this->set_style( 'header_text_color', '#fff', '.editor-styles-wrapper.header-default .editor-post-title__block .editor-post-title__input', 'color' );
		// Header overlay color
		$this->set_style( 'header_overlay_color', 'rgba(0, 0, 0, 0.5)', '.editor-styles-wrapper.has-featured-image .hestia-featured-background:before', 'background-color' );

		// Font sizes
		$headings_map = $this->get_headings_font_size_defaults_map();

		// Header font size.
		$this->set_font_size_style( 'hestia_header_titles_fs', $headings_map['h1'], '.editor-styles-wrapper .editor-post-title__block .editor-post-title__input' );

		// Headings font size.
		foreach ( $headings_map as $tag => $args ) {
			$this->set_font_size_style( 'hestia_post_page_headings_fs', $args, '.editor-styles-wrapper .editor-writing-flow ' . esc_attr( $tag ) );
		}

		// Content font size.
		$default_values = array(
			'desktop' => 18,
			'tablet'  => 16,
			'mobile'  => 16,
		);

		$this->set_font_size_style( 'hestia_post_page_content_fs', $default_values, '.editor-styles-wrapper .editor-writing-flow, .editor-styles-wrapper .editor-writing-flow p, .editor-default-block-appender textarea.editor-default-block-appender__content' );
	}

	/**
	 * Set the font size style.
	 *
	 * This is made to work with our system of font sizes.
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string $theme_mod the theme mod key.
	 * @param array  $base_values the font base values ['mobile','tablet','desktop'].
	 * @param string $selector css selector.
	 */
	private function set_font_size_style( $theme_mod, $base_values, $selector ) {
		if ( empty( $theme_mod ) || ! is_array( $base_values ) || empty( $selector ) ) {
			return;
		}

		$value = get_theme_mod( $theme_mod );

		if ( empty( $value ) ) {
			return;
		}

		$value = json_decode( $value, true );

		$value = wp_parse_args(
			$value,
			array(
				'desktop' => 0,
				'tablet'  => 0,
				'mobile'  => 0,
			)
		);

		$values_to_set = array(
			'desktop' => $base_values['desktop'] + $value['desktop'],
			'tablet'  => $base_values['tablet'] + $value['tablet'],
			'mobile'  => $base_values['mobile'] + $value['mobile'],
		);

		foreach ( $values_to_set as $query => $value ) {
			$this->add_css( $selector, $values_to_set[ $query ], 'font-size', 'px', $query );
		}
	}

	/**
	 * Set style per media query
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string     $theme_mod theme mod key.
	 * @param string|int $default_value default value.
	 * @param string     $selector css selector.
	 * @param string     $property css property.
	 * @param string     $suffix suffix for the css value.
	 * @param string     $media_query media query.
	 */
	private function set_style( $theme_mod, $default_value, $selector, $property, $suffix = '', $media_query = 'mobile' ) {
		if ( empty( $selector ) || empty( $theme_mod ) || empty( $property ) ) {
			return;
		}

		$value = get_theme_mod( $theme_mod, $default_value );
		if ( empty( $value ) ) {
			return;
		}

		$this->add_css( $selector, $value, $property, $suffix, $media_query );
	}

	/**
	 * Add CSS.
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string     $selector css selector.
	 * @param string|int $value value to set.
	 * @param string     $property css property.
	 * @param string     $suffix suffix for the css value.
	 * @param string     $media_query media query.
	 */
	private function add_css( $selector, $value, $property, $suffix, $media_query = 'mobile' ) {
		if ( empty( $value ) ) {
			return;
		}
		$css = esc_attr( $selector ) . '{' . esc_attr( $property ) . ': ' . esc_attr( $value ) . esc_attr( $suffix ) . ';}';
		switch ( $media_query ) {
			default:
			case 'mobile':
				$this->mobile_style .= $css;
				break;
			case 'tablet':
				$this->tablet_style .= $css;
				break;
			case 'desktop':
				$this->desktop_style .= $css;
				break;
		}
	}

	/**
	 * Set responsive style.
	 *
	 * @since  2.0.18
	 * @access private
	 *
	 * @param string     $theme_mod theme mod key.
	 * @param string|int $default_value default value.
	 * @param string     $selector css selector.
	 * @param string     $property css property.
	 * @param string     $suffix suffix for the css value.
	 */
	private function set_responsive_style( $theme_mod, $default_value, $selector, $property, $suffix = '' ) {
		$value = get_theme_mod( $theme_mod, $default_value );
		if ( empty( $value ) ) {
			return;
		}
		$value = json_decode( $value );

		foreach ( $value as $media_query => $media_query_value ) {
			$this->add_css( $selector, $media_query_value, $property, $suffix, $media_query );
		}
	}

	/**
	 * Get the script localization.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return array
	 */
	private function get_localization() {
		return array(
			'featuredImage'  => get_the_post_thumbnail_url( $this->post_id ),
			'headerLayout'   => $this->get_header_layout(),
			'sidebarLayout'  => $this->get_sidebar_layout(),
			'headerSitewide' => $this->get_header_sitewide(),
			'headerImage'    => get_theme_mod( 'header_image', false ),
			'strings'        => $this->get_strings(),
		);
	}

	/**
	 * Check if we're editing front page.
	 *
	 * @return bool
	 */
	private function is_front_page() {

		if ( get_option( 'show_on_front' ) !== 'page' ) {
			return false;
		}

		if ( $this->post_id !== get_option( 'page_on_front' ) ) {
			return false;
		}

		$page_template = get_post_meta( $this->post_id, '_wp_page_template', true );
		if ( ! empty( $page_template ) && 'default' !== $page_template ) {
			return false;
		}

		$disabled_frontpage = get_theme_mod( 'disable_frontpage_sections', false );
		if ( true === (bool) $disabled_frontpage ) {
			return false;
		}

		return true;
	}

	/**
	 * Get the header site-wide value.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return string
	 */
	private function get_header_sitewide() {
		$header_sitewide = get_theme_mod( 'hestia_header_image_sitewide', false );

		if ( $header_sitewide === false ) {
			return 'no';
		}

		return 'yes';
	}

	/**
	 * Get the strings passed to JS.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return array
	 */
	private function get_strings() {
		return array(
			'sidebar' => esc_html__( 'Sidebar', 'hestia' ),
		);
	}

	/**
	 * Get the header layout.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return string
	 */
	private function get_header_layout() {
		$header_layout = get_post_meta( $this->post_id, 'hestia_header_layout', true );
		if ( ! empty( $header_layout ) ) {
			return $header_layout;
		}

		if ( class_exists( 'WooCommerce' ) ) {
			$cart_id     = get_option( 'woocommerce_cart_page_id' );
			$checkout_id = get_option( 'woocommerce_checkout_page_id' );
			if ( $this->post_id === $cart_id ) {
				return 'no-content';
			}

			if ( $this->post_id === $checkout_id ) {
				return 'no-content';
			}
		}
		$header_layout = get_theme_mod( 'hestia_header_layout', 'default' );

		return $header_layout;
	}

	/**
	 * Get the sidebar layout.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return string
	 */
	private function get_sidebar_layout() {
		if ( class_exists( 'WooCommerce' ) && $this->post_id !== null ) {
			$woocommerce_pages = array(
				'woocommerce_cart_page_id',
				'woocommerce_checkout_page_id',
				'woocommerce_myaccount_page_id',
			);
			foreach ( $woocommerce_pages as $option_id ) {
				if ( get_option( $option_id ) !== $this->post_id ) {
					continue;
				}

				return 'full-width';
			}
		}
		$sidebar_layout = get_post_meta( $this->post_id, 'hestia_layout_select', true );
		if ( ! empty( $sidebar_layout ) ) {

			return $sidebar_layout;
		}

		if ( get_post_type( $this->post_id ) === 'page' || ( isset( $_GET['post_type'] ) && $_GET['post_type'] === 'page' ) ) {
			return get_theme_mod( 'hestia_page_sidebar_layout', 'full-width' );
		}

		return get_theme_mod( 'hestia_blog_sidebar_layout', 'sidebar-right' );
	}

	/**
	 * Get the default values for header font sizes.
	 *
	 * @since  2.0.18
	 * @access private
	 * @return array
	 */
	private function get_headings_font_size_defaults_map() {
		return array(
			'h1' => array(
				'desktop' => 42,
				'tablet'  => 36,
				'mobile'  => 36,
			),
			'h2' => array(
				'desktop' => 37,
				'tablet'  => 32,
				'mobile'  => 32,
			),
			'h3' => array(
				'desktop' => 32,
				'tablet'  => 28,
				'mobile'  => 28,
			),
			'h4' => array(
				'desktop' => 27,
				'tablet'  => 24,
				'mobile'  => 24,
			),
			'h5' => array(
				'desktop' => 23,
				'tablet'  => 21,
				'mobile'  => 21,
			),
			'h6' => array(
				'desktop' => 18,
				'tablet'  => 18,
				'mobile'  => 18,
			),
		);
	}
}
