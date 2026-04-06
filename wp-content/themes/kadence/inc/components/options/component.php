<?php
/**
 * Kadence\Options\Component class
 *
 * @package kadence
 */

namespace Kadence\Options;

use Kadence\Component_Interface;
use Kadence\Templating_Component_Interface;
use function Kadence\kadence;
use function apply_filters;
use function get_option;
use function is_null;
use function wp_parse_args;
use function add_action;

/**
 * Class for managing stylesheets.
 *
 * Exposes template tags:
 * * `kadence()->option()`
 * * `kadence()->default()`
 * * `kadence()->get_option_type()`
 * * `kadence()->get_option_name()`
 * * `kadence()->sub_option()`
 * * `kadence()->sidebar_options()`
 * * `kadence()->palette_option()`
 * * `kadence()->get_palette()`
 * * `kadence()->get_pro_url()`
 */
class Component implements Component_Interface, Templating_Component_Interface {


	/**
	 * Holds default theme option values
	 *
	 * @var default values of the theme.
	 */
	protected static $default_options = null;

	/**
	 * Settings database Name.
	 *
	 * @var array
	 */
	private static $opt_name = null;

	/**
	 * Settings database Name.
	 *
	 * @var array
	 */
	private static $opt_type = null;

	/**
	 * Holds theme option values
	 *
	 * @var values of the theme settings.
	 */
	protected static $options = null;

	/**
	 * Holds palette values
	 *
	 * @var values of the theme settings.
	 */
	protected static $palette = null;

	/**
	 * Holds sidebar options values
	 *
	 * @var values of the theme settings.
	 */
	protected static $sidebars = null;

	/**
	 * Holds header options values
	 *
	 * @var values of the theme settings.
	 */
	protected static $headers = null;

	/**
	 * Holds default palette values
	 *
	 * @var values of the theme settings.
	 */
	protected static $default_palette = null;

	/**
	 * Holds default theme option values for cpt
	 *
	 * @var default values of the theme.
	 */
	protected static $cpt_options = null;
	/**
	 * Holds theme option values
	 *
	 * @var values of the theme settings.
	 */
	protected static $custom_options = [];

	/**
	 * Holds allowed alt url values
	 *
	 * @var values of the allowed alt urls.
	 */
	protected static $allowed_urls = [ 'https://www.kadencewp.com/kadence-theme/hostinger/', 'https://www.kadencewp.com/kadence-theme/instawp/' ];

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug(): string {
		return 'options';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'wp_loaded', [ $this, 'add_default_options' ] );
		add_action( 'customize_register', [ $this, 'add_default_options' ], 5 );
		add_filter( 'option_kadence_global_palette', [ $this, 'normalize_palette_option' ], 10, 1 );
		add_filter( 'pre_update_option_kadence_global_palette', [ $this, 'normalize_palette_option' ], 10, 1 );
	}

	/**
	 * Normalize palette option.
	 *
	 * @param string $value the value of the palette option.
	 * @param string $option the option of the palette option.
	 * @return string the normalized value of the palette option.
	 */
	public function normalize_palette_option( $value ) {
		if ( $value && ! empty( $value ) ) {
			$palette = json_decode( $value, true );
			$extended_palette = [ 
				[
					'color' => '#FfFfFf',
					'slug' => 'palette10',
					'name' => 'Palette Color Complement',
				], 
				[
					'color' => '#13612e',
					'slug' => 'palette11',
					'name' => 'Palette Color Success',
				], 
				[
					'color' => '#1159af',
					'slug' => 'palette12',
					'name' => 'Palette Color Info',
				], 
				[
					'color' => '#b82105',
					'slug' => 'palette13',
					'name' => 'Palette Color Alert',
				], 
				[
					'color' => '#f7630c',
					'slug' => 'palette14',
					'name' => 'Palette Color Warning',
				], 
				[
					'color' => '#f5a524',
					'slug' => 'palette15',
					'name' => 'Palette Color Rating',
				]
			];
			if( isset( $palette['palette'] ) && is_array( $palette['palette'] ) && sizeof( $palette['palette'] ) == 9 ) {
				$palette['palette'] = array_merge( $palette['palette'], $extended_palette );
			}
			if( isset( $palette['second-palette'] ) && is_array( $palette['second-palette'] ) && sizeof( $palette['second-palette'] ) == 9 ) {
				$palette['second-palette'] = array_merge( $palette['second-palette'], $extended_palette );
			}
			if( isset( $palette['third-palette'] ) && is_array( $palette['third-palette'] ) && sizeof( $palette['third-palette'] ) == 9 ) {
				$palette['third-palette'] = array_merge( $palette['third-palette'], $extended_palette );
			}

			if ( ! empty( $palette ) ) {
				return json_encode( $palette );
			}
		}
		return $value;
	}

	/**
	 * Gets template tags to expose as methods on the Template_Tags class instance, accessible through `kadence()`.
	 *
	 * @return array Associative array of $method_name => $callback_info pairs. Each $callback_info must either be
	 *               a callable or an array with key 'callable'. This approach is used to reserve the possibility of
	 *               adding support for further arguments in the future.
	 */
	public function template_tags(): array {
		return [
			'option'                     => [ $this, 'option' ],
			'default'                    => [ $this, 'default' ],
			'get_option_type'            => [ $this, 'get_option_type' ],
			'get_option_name'            => [ $this, 'get_option_name' ],
			'sub_option'                 => [ $this, 'sub_option' ],
			'sidebar_options'            => [ $this, 'sidebar_options' ],
			'palette_option'             => [ $this, 'palette_option' ],
			'get_palette'                => [ $this, 'get_palette' ],
			'get_default_palette'        => [ $this, 'get_default_palette' ],
			'get_palette_for_customizer' => [ $this, 'get_palette_for_customizer' ],
			'get_pro_url'                => [ $this, 'get_pro_url' ],
			'block_header_options'       => [ $this, 'block_header_options' ],
		];
	}

	/**
	 * Get Option Type
	 *
	 * @access public
	 * @return string
	 */
	public function get_option_type() {
		// Define sections.
		if ( is_null( self::$opt_type ) ) {
			self::$opt_type = apply_filters( 'kadence_theme_option_type', 'theme_mod' );
		}
		// Return option_type.
		return self::$opt_type;
	}

	/**
	 * Get Option Name
	 *
	 * @access public
	 * @return string
	 */
	public function get_option_name() {
		// Define sections.
		if ( is_null( self::$opt_name ) ) {
			self::$opt_name = apply_filters( 'kadence_theme_option_name', 'kadence_settings' );
		}
		// Return option_name.
		return self::$opt_name;
	}
	/**
	 * Set default theme option values
	 *
	 * @return default values of the theme.
	 */
	public static function defaults() {
		// Don't store defaults until after init.
		if ( is_null( self::$default_options ) ) {
			$initial_version       = get_theme_mod( 'initial_version', false );
			$version_1_3_0_changes = true;
			// Check if the initial version is prior to 1.3.0
			if ( ! empty( $initial_version ) && version_compare( $initial_version, '1.3.0', '<' ) ) {
				$version_1_3_0_changes = false;
			}
			self::$default_options = apply_filters(
				'kadence_theme_options_defaults',
				[
					'content_width'                        => [
						'size' => 1290,
						'unit' => 'px',
					],
					'content_narrow_width'                 => [
						'size' => 842,
						'unit' => 'px',
					],
					'content_edge_spacing'                 => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 1.5,
						],
						'unit' => [
							'mobile'  => 'rem',
							'tablet'  => 'rem',
							'desktop' => 'rem',
						],
					],
					'content_spacing'                      => [
						'size' => [
							'mobile'  => 2,
							'tablet'  => 3,
							'desktop' => 5,
						],
						'unit' => [
							'mobile'  => 'rem',
							'tablet'  => 'rem',
							'desktop' => 'rem',
						],
					],
					'boxed_spacing'                        => [
						'size' => [
							'mobile'  => 1.5,
							'tablet'  => 2,
							'desktop' => 2,
						],
						'unit' => [
							'mobile'  => 'rem',
							'tablet'  => 'rem',
							'desktop' => 'rem',
						],
					],
					'boxed_shadow'                         => [
						'color'   => 'rgba(0,0,0,0.05)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 15,
						'spread'  => -10,
						'inset'   => false,
						'disabled' => false,
					],
					'boxed_border_radius'                  => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => true,
					],
					'boxed_grid_spacing'                   => [
						'size' => [
							'mobile'  => 1.5,
							'tablet'  => 2,
							'desktop' => 2,
						],
						'unit' => [
							'mobile'  => 'rem',
							'tablet'  => 'rem',
							'desktop' => 'rem',
						],
					],
					'boxed_grid_shadow'                    => [
						'color'   => 'rgba(0,0,0,0.05)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 15,
						'spread'  => -10,
						'inset'   => false,
						'disabled' => false,
					],
					'boxed_grid_border_radius'             => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => true,
					],
					'site_background'                      => [
						'desktop' => [
							'color' => 'palette8',
						],
					],
					'content_background'                   => [
						'desktop' => [
							'color' => 'palette9',
						],
					],
					// Sidebar.
					'sidebar_width'                        => [
						'size' => '',
						'unit' => '%',
					],
					'sidebar_widget_spacing'               => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 1.5,
						],
						'unit' => [
							'mobile'  => 'em',
							'tablet'  => 'em',
							'desktop' => 'em',
						],
					],
					'sidebar_widget_title'                 => [
						'size'       => [
							'desktop' => 20,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					'sidebar_widget_content'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => 'palette4',
					],
					'sidebar_link_style'                   => 'normal',
					'sidebar_link_colors'                  => [
						'color' => '',
						'hover' => '',
					],
					'sidebar_background'                   => [
						'desktop' => [
							'color' => '',
						],
					],
					'sidebar_divider_border'               => [],
					'sidebar_padding'                      => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					'sidebar_sticky'                       => false,
					'sidebar_sticky_last_widget'           => false,
					// Links.
					'link_color'                           => [
						'highlight'      => 'palette1',
						'highlight-alt'  => 'palette2',
						'highlight-alt2' => 'palette9',
						'style'          => 'standard',
					],
					// Scroll To Top.
					'scroll_up'                            => false,
					'scroll_up_side'                       => 'right',
					'scroll_up_icon'                       => 'arrow-up',
					'scroll_up_icon_size'                  => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 1.2,
						],
						'unit' => [
							'mobile'  => 'em',
							'tablet'  => 'em',
							'desktop' => 'em',
						],
					],
					'scroll_up_side_offset'                => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 30,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'scroll_up_bottom_offset'              => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 30,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'scroll_up_visiblity'                  => [
						'desktop' => true,
						'tablet'  => true,
						'mobile'  => false,
					],
					'scroll_up_style'                      => 'outline',
					'scroll_up_padding'                    => [
						'size'   => [ 
							'desktop' => [ 0.4, 0.4, 0.4, 0.4 ],
						],
						'unit'   => [
							'desktop' => 'em',
						],
						'locked' => [
							'desktop' => true,
						],
					],
					'scroll_up_color'                      => [
						'color' => '',
						'hover' => '',
					],
					'scroll_up_background'                 => [
						'color' => '',
						'hover' => '',
					],
					'scroll_up_border_colors'              => [
						'color' => '',
						'hover' => '',
					],
					'scroll_up_border'                     => [],
					'scroll_up_radius'                     => [
						'size'   => [ 0, 0, 0, 0 ],
						'unit'   => 'px',
						'locked' => true,
					],
					'comment_form_remove_web'              => false,
					'comment_form_before_list'             => false,
					// Buttons.
					'buttons_color'                        => [
						'color' => 'palette9',
						'hover' => 'palette9',
					],
					'buttons_background'                   => [
						'color' => 'palette1',
						'hover' => 'palette2',
					],
					'buttons_border_colors'                => [
						'color' => '',
						'hover' => '',
					],
					'buttons_border'                       => [],
					'buttons_border_radius'                => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'buttons_typography'                   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'buttons_padding'                      => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					'buttons_shadow'                       => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					'buttons_shadow_hover'                 => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 25,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					// Buttons Secondary.
					'buttons_secondary_color'                        => [
						'color' => 'palette3',
						'hover' => 'palette9',
					],
					'buttons_secondary_background'                   => [
						'color' => 'palette7',
						'hover' => 'palette2',
					],
					'buttons_secondary_border_colors'                => [
						'color' => '',
						'hover' => '',
					],
					'buttons_secondary_border'                       => [],
					'buttons_secondary_border_radius'                => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'buttons_secondary_typography'                   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'buttons_secondary_padding'                      => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					'buttons_secondary_shadow'                       => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => true,
					],
					'buttons_secondary_shadow_hover'                 => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 25,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => true,
					],
					// Buttons Outline.
					'buttons_outline_color'                        => [
						'color' => '',
						'hover' => '',
					],
					'buttons_outline_border_colors'                => [
						'color' => '',
						'hover' => '',
					],
					'buttons_outline_border'                       => [],
					'buttons_outline_border_radius'                => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'buttons_outline_typography'                   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'buttons_outline_padding'                      => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					'buttons_outline_shadow'                       => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => true,
					],
					'buttons_outline_shadow_hover'                 => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 25,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => true,
					],
					//image
					'image_border_radius'                  => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					//footer/header
					'enable_footer_on_bottom'              => true,
					'enable_scroll_to_id'                  => true,
					'blocks_header'                        => false,
					'blocks_header_id'                     => '',
					'lightbox'                             => false,
					'header_popup_width'                   => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'header_popup_content_max_width'       => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'enable_popup_body_animate'            => false,
					// Typography.
					'font_rendering'                       => false,
					'base_font'                            => [
						'size'       => [
							'desktop' => 17,
						],
						'lineHeight' => [
							'desktop' => 1.6,
						],
						'family'     => '-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol"',
						'google'     => false,
						'weight'     => '400',
						'variant'    => 'regular',
						'color'      => 'palette4',
					],
					'load_base_italic'                     => false,
					'google_subsets'                       => [],
					'load_fonts_local'                     => false,
					'preload_fonts_local'                  => true,
					'heading_font'                         => [
						'family' => 'inherit',
					],
					'h1_font'                              => [
						'size'       => [
							'desktop' => 32,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					'h2_font'                              => [
						'size'       => [
							'desktop' => 28,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					'h3_font'                              => [
						'size'       => [
							'desktop' => 24,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					'h4_font'                              => [
						'size'       => [
							'desktop' => 22,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette4',
					],
					'h5_font'                              => [
						'size'       => [
							'desktop' => 20,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette4',
					],
					'h6_font'                              => [
						'size'       => [
							'desktop' => 18,
						],
						'lineHeight' => [
							'desktop' => 1.5,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette5',
					],
					'title_above_font'                     => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'title_above_breadcrumb_font'          => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'google_subsets'                       => [
						'latin-ext'          => false,
						'cyrillic'           => false,
						'cyrillic-ext'       => false,
						'greek'              => false,
						'greek-ext'          => false,
						'vietnamese'         => false,
						'arabic'             => false,
						'khmer'              => false,
						'chinese'            => false,
						'chinese-simplified' => false,
						'tamil'              => false,
						'bengali'            => false,
						'devanagari'         => false,
						'hebrew'             => false,
						'korean'             => false,
						'thai'               => false,
						'telugu'             => false,
					],
					'header_mobile_switch'                 => [
						'size' => '',
						'unit' => 'px',
					],
					// Header.
					'header_desktop_items'                 => [
						'top'    => [
							'top_left'         => [],
							'top_left_center'  => [],
							'top_center'       => [],
							'top_right_center' => [],
							'top_right'        => [],
						],
						'main'   => [
							'main_left'         => [ 'logo' ],
							'main_left_center'  => [],
							'main_center'       => [],
							'main_right_center' => [],
							'main_right'        => [ 'navigation' ],
						],
						'bottom' => [
							'bottom_left'         => [],
							'bottom_left_center'  => [],
							'bottom_center'       => [],
							'bottom_right_center' => [],
							'bottom_right'        => [],
						],
					],
					'header_wrap_background'               => [
						'desktop' => [
							'color' => '#ffffff',
						],
					],
					// Header Main.
					'header_main_height'                   => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 80,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'header_main_layout'                   => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'header_main_background'               => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_main_trans_background'         => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_main_top_border'               => [],
					'header_main_bottom_border'            => [],
					'header_main_padding'                  => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					// Header Top.
					'header_top_height'                    => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 0,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'header_top_layout'                    => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'header_top_background'                => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_top_trans_background'          => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_top_top_border'                => [],
					'header_top_bottom_border'             => [],
					'header_top_padding'                   => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					// Header Bottom.
					'header_bottom_height'                 => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 0,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'header_bottom_layout'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'header_bottom_background'             => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_bottom_trans_background'       => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_bottom_top_border'             => [],
					'header_bottom_bottom_border'          => [],
					'header_bottom_padding'                => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					// Mobile Header.
					'header_mobile_items'                  => [
						'popup'  => [
							'popup_content' => [ 'mobile-navigation' ],
						],
						'top'    => [
							'top_left'   => [],
							'top_center' => [],
							'top_right'  => [],
						],
						'main'   => [
							'main_left'   => [ 'mobile-logo' ],
							'main_center' => [],
							'main_right'  => [ 'popup-toggle' ],
						],
						'bottom' => [
							'bottom_left'   => [],
							'bottom_center' => [],
							'bottom_right'  => [],
						],
					],
					// Logo.
					'logo_width'                           => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 200,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'use_mobile_logo'                      => false,
					'logo_layout'                          => [
						'include' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 'logo_title',
						],
						'layout'  => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 'standard',
						],
					],
					// Logo Icon.
					'use_logo_icon'                        => false,
					'logo_icon'                            => 'logoArrow',
					'logo_icon_width'                      => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 60,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'logo_icon_color'                      => [
						'color' => 'palette3',
					],
					'transparent_logo_icon_color'          => [
						'color' => '',
					],
					'header_sticky_logo_icon_color'        => [
						'color' => '',
					],
					'brand_typography'                     => [
						'size'       => [
							'desktop' => 26,
						],
						'lineHeight' => [
							'desktop' => 1.2,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					'brand_typography_color'               => [
						'hover'  => '',
						'active' => '',
					],
					'brand_tag_typography'                 => [
						'size'       => [
							'desktop' => 16,
						],
						'lineHeight' => [
							'desktop' => 1.4,
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette5',
					],
					'header_logo_padding'                  => [
						'size'   => [ 
							'desktop' => [ '', '', '', '' ],
						],
						'unit'   => [
							'desktop' => 'px',
						],
						'locked' => [
							'desktop' => false,
						],
					],
					// Navigation.
					'primary_navigation_typography'        => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'primary_navigation_spacing'           => [
						'size' => 1.2,
						'unit' => 'em',
					],
					'primary_navigation_vertical_spacing'  => [
						'size' => 0.6,
						'unit' => 'em',
					],
					'primary_navigation_stretch'           => false,
					'primary_navigation_open_type'         => 'hover',
					'primary_navigation_fill_stretch'      => false,
					'primary_navigation_style'             => 'standard',
					'primary_navigation_color'             => [
						'color'  => 'palette5',
						'hover'  => 'palette-highlight',
						'active' => 'palette3',
					],
					'primary_navigation_background'        => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'primary_navigation_parent_active'     => false,
					// Secondary Navigation.
					'secondary_navigation_typography'      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'secondary_navigation_spacing'         => [
						'size' => 1.2,
						'unit' => 'em',
					],
					'secondary_navigation_vertical_spacing' => [
						'size' => 0.6,
						'unit' => 'em',
					],
					'secondary_navigation_stretch'         => false,
					'secondary_navigation_open_type'       => 'hover',
					'secondary_navigation_fill_stretch'    => false,
					'secondary_navigation_style'           => 'standard',
					'secondary_navigation_color'           => [
						'color'  => 'palette5',
						'hover'  => 'palette-highlight',
						'active' => 'palette3',
					],
					'secondary_navigation_background'      => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'secondary_navigation_parent_active'   => false,
					// Dropdown.
					'dropdown_navigation_reveal'           => 'none',
					'dropdown_navigation_width'            => [
						'size' => 200,
						'unit' => 'px',
					],
					'dropdown_navigation_vertical_spacing' => [
						'size' => 1,
						'unit' => 'em',
					],
					'dropdown_navigation_color'            => [
						'color'  => 'palette8',
						'hover'  => 'palette9',
						'active' => 'palette9',
					],
					'dropdown_navigation_background'       => [
						'color'  => 'palette3',
						'hover'  => 'palette4',
						'active' => 'palette4',
					],
					'dropdown_navigation_divider'          => [
						'width' => 1,
						'unit'  => 'px',
						'style' => 'solid',
						'color' => 'rgba(255,255,255,0.1)',
					],
					'dropdown_navigation_border_radius'             => [
						'size'   => [ 0, 0, 0, 0 ],
						'unit'   => 'px',
						'locked' => true,
					],
					'dropdown_navigation_shadow'           => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 2,
						'blur'    => 13,
						'spread'  => 0,
						'inset'   => false,
						'disabled' => false,
					],
					'dropdown_navigation_typography'       => [
						'size'       => [
							'desktop' => 12,
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					// Mobile Trigger.
					'mobile_trigger_label'                 => '',
					'mobile_trigger_icon'                  => 'menu',
					'mobile_trigger_style'                 => 'default',
					'mobile_trigger_border'                => [
						'width' => 1,
						'unit'  => 'px',
						'style' => 'solid',
						'color' => 'currentColor',
					],
					'mobile_trigger_icon_size'             => [
						'size' => 20,
						'unit' => 'px',
					],
					'mobile_trigger_color'                 => [
						'color' => 'palette5',
						'hover' => 'palette-highlight',
					],
					'mobile_trigger_background'            => [
						'color' => '',
						'hover' => '',
					],
					'mobile_trigger_typography'            => [
						'size'       => [
							'desktop' => 14,
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'mobile_trigger_padding'               => [
						'size'   => [ 0.4, 0.6, 0.4, 0.6 ],
						'unit'   => 'em',
						'locked' => false,
					],
					// Mobile Navigation.
					'mobile_navigation_reveal'             => 'none',
					'mobile_navigation_collapse'           => true,
					'mobile_navigation_parent_toggle'      => false,
					'mobile_navigation_width'              => [
						'size' => 200,
						'unit' => 'px',
					],
					'mobile_navigation_vertical_spacing'   => [
						'size' => 1,
						'unit' => 'em',
					],
					'mobile_navigation_color'              => [
						'color'  => 'palette8',
						'hover'  => '',
						'active' => 'palette-highlight',
					],
					'mobile_navigation_background'         => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'mobile_navigation_divider'            => [
						'width' => 1,
						'unit'  => 'px',
						'style' => 'solid',
						'color' => 'rgba(255,255,255,0.1)',
					],
					'mobile_navigation_typography'         => [
						'size'       => [
							'desktop' => 14,
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					// Header Popup.
					'header_popup_side'                    => 'right',
					'header_popup_layout'                  => 'sidepanel',
					'header_popup_animation'               => 'fade',
					'header_popup_vertical_align'          => 'top',
					'header_popup_content_align'           => 'left',
					'header_popup_background'              => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_popup_close_color'             => [
						'color' => '',
						'hover' => '',
					],
					'header_popup_close_background'        => [
						'color' => '',
						'hover' => '',
					],
					'header_popup_close_icon_size'         => [
						'size' => '24',
						'unit' => 'px',
					],
					'header_popup_close_padding'           => [
						'size'   => [ 0.6, 0.15, 0.6, 0.15 ],
						'unit'   => 'em',
						'locked' => false,
					],
					// Header HTML.
					'header_html_content'                  => __( 'Insert HTML here', 'kadence' ),
					'header_html_typography'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'header_html_link_style'               => 'normal',
					'header_html_link_color'               => [
						'color' => '',
						'hover' => '',
					],
					'header_html_margin'                   => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'header_html_wpautop'                  => true,
					// Header Button.
					'header_button_label'                  => __( 'Button', 'kadence' ),
					'header_button_link'                   => '',
					'header_button_style'                  => 'filled',
					'header_button_size'                   => 'medium',
					'header_button_visibility'             => 'all',
					'header_button_padding'                => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'header_button_typography'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_button_color'                  => [
						'color' => '',
						'hover' => '',
					],
					'header_button_background'             => [
						'color' => '',
						'hover' => '',
					],
					'header_button_border_colors'          => [
						'color' => '',
						'hover' => '',
					],
					'header_button_border'                 => [
						'width' => 2,
						'unit'  => 'px',
					],
					'header_button_shadow'                 => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					'header_button_shadow_hover'           => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 25,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					'header_button_margin'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'header_button_radius'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => true,
					],
					// Header Social.
					'header_social_items'                  => [
						'items' => [
							[
								'id'      => 'facebook',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'facebook',
								'label'   => 'Facebook',
							],
							[
								'id'      => 'twitter',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'twitterAlt2',
								'label'   => 'X',
							],
							[
								'id'      => 'instagram',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'instagramAlt',
								'label'   => 'Instagram',
							],
						],
					],
					'header_social_style'                  => 'filled',
					'header_social_show_label'             => false,
					'header_social_item_spacing'           => [
						'size' => 0.3,
						'unit' => 'em',
					],
					'header_social_icon_size'              => [
						'size' => 1,
						'unit' => 'em',
					],
					'header_social_brand'                  => '',
					'header_social_color'                  => [
						'color' => '',
						'hover' => '',
					],
					'header_social_background'             => [
						'color' => '',
						'hover' => '',
					],
					'header_social_border_colors'          => [
						'color' => '',
						'hover' => '',
					],
					'header_social_border'                 => [
						'width' => 2,
						'unit'  => 'px',
						'style' => 'none',
					],
					'header_social_border_radius'          => [
						'size' => 3,
						'unit' => 'px',
					],
					'header_social_typography'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_social_margin'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					// Mobile Header Social.
					'header_mobile_social_items'           => [
						'items' => [
							[
								'id'      => 'facebook',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'facebook',
								'label'   => 'Facebook',
							],
							[
								'id'      => 'twitter',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'twitterAlt2',
								'label'   => 'X',
							],
							[
								'id'      => 'instagram',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'instagramAlt',
								'label'   => 'Instagram',
							],
						],
					],
					'header_mobile_social_style'           => 'filled',
					'header_mobile_social_show_label'      => false,
					'header_mobile_social_item_spacing'    => [
						'size' => 0.3,
						'unit' => 'em',
					],
					'header_mobile_social_icon_size'       => [
						'size' => 1,
						'unit' => 'em',
					],
					'header_mobile_social_brand'           => '',
					'header_mobile_social_color'           => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_social_background'      => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_social_border_colors'   => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_social_border'          => [
						'width' => 2,
						'unit'  => 'px',
						'style' => 'none',
					],
					'header_mobile_social_border_radius'   => [
						'size' => 3,
						'unit' => 'px',
					],
					'header_mobile_social_typography'      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_mobile_social_margin'          => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					// Header Search.
					'header_search_label'                  => '',
					'header_search_label_visiblity'        => [
						'desktop' => true,
						'tablet'  => true,
						'mobile'  => false,
					],
					'header_search_icon'                   => 'search',
					'header_search_style'                  => 'default',
					'header_search_woo'                    => false,
					'header_search_border'                 => [
						'width' => 1,
						'unit'  => 'px',
						'style' => 'solid',
						'color' => 'currentColor',
					],
					'header_search_icon_size'              => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 1,
						],
						'unit' => [
							'mobile'  => 'em',
							'tablet'  => 'em',
							'desktop' => 'em',
						],
					],
					'header_search_color'                  => [
						'color' => 'palette5',
						'hover' => 'palette-highlight',
					],
					'header_search_background'             => [
						'color' => '',
						'hover' => '',
					],
					'header_search_typography'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_search_padding'                => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'em',
						'locked' => false,
					],
					'header_search_margin'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'header_search_modal_color'            => [
						'color' => '',
						'hover' => '',
					],
					'header_search_modal_background'       => [
						'desktop' => '',
					],
					'header_search_modal_background'       => [
						'desktop' => [
							'color' => 'rgba(9, 12, 16, 0.97)',
						],
					],
					// Mobile Header Button.
					'mobile_button_label'                  => __( 'Button', 'kadence' ),
					'mobile_button_style'                  => 'filled',
					'mobile_button_size'                   => 'medium',
					'mobile_button_visibility'             => 'all',
					'mobile_button_typography'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'mobile_button_color'                  => [
						'color' => '',
						'hover' => '',
					],
					'mobile_button_background'             => [
						'color' => '',
						'hover' => '',
					],
					'mobile_button_border_colors'          => [
						'color' => '',
						'hover' => '',
					],
					'mobile_button_border'                 => [
						'width' => 2,
						'unit'  => 'px',
						'style' => 'none',
					],
					'mobile_button_margin'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'mobile_button_shadow'                 => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					'mobile_button_shadow_hover'           => [
						'color'   => 'rgba(0,0,0,0.1)',
						'hOffset' => 0,
						'vOffset' => 15,
						'blur'    => 25,
						'spread'  => -7,
						'inset'   => false,
						'disabled' => false,
					],
					'mobile_button_radius'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => true,
					],
					// Mobile Header HTML.
					'mobile_html_content'                  => __( 'Insert HTML here', 'kadence' ),
					'mobile_html_typography'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'mobile_html_link_color'               => [
						'color' => '',
						'hover' => '',
					],
					'mobile_html_margin'                   => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'mobile_html_link_style'               => 'normal',
					'mobile_html_wpautop'                  => true,
					// Transparent Header.
					'transparent_header_enable'            => false,
					'transparent_header_device'            => [
						'desktop' => true,
						'mobile'  => true,
					],
					'transparent_header_archive'           => true,
					'transparent_header_page'              => false,
					'transparent_header_post'              => false,
					'transparent_header_product'           => true,
					'transparent_header_logo_width'        => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'transparent_header_logo'              => '',
					'transparent_header_custom_logo'       => false,
					'transparent_header_mobile_logo'       => '',
					'transparent_header_custom_mobile_logo' => false,
					'transparent_header_site_title_color'  => [
						'color' => '',
					],
					'transparent_header_navigation_color'  => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'transparent_header_navigation_background' => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'transparent_header_button_color'      => [
						'color'           => '',
						'hover'           => '',
						'background'      => '',
						'backgroundHover' => '',
						'border'          => '',
						'borderHover'     => '',
					],
					'transparent_header_social_color'      => [
						'color'           => '',
						'hover'           => '',
						'background'      => '',
						'backgroundHover' => '',
						'border'          => '',
						'borderHover'     => '',
					],
					'transparent_header_html_color'        => [
						'color' => '',
						'link'  => '',
						'hover' => '',
					],
					'transparent_header_background'        => [
						'desktop' => [
							'color' => '',
						],
					],
					'transparent_header_bottom_border'     => [],
					// Sticky Header.
					'header_sticky'                        => 'no',
					'header_reveal_scroll_up'              => false,
					'header_sticky_shrink'                 => false,
					'header_sticky_main_shrink'            => [
						'size' => 60,
						'unit' => 'px',
					],
					'mobile_header_sticky'                 => 'no',
					'mobile_header_sticky_shrink'          => false,
					'mobile_header_reveal_scroll_up'       => false,
					'mobile_header_sticky_main_shrink'     => [
						'size' => 60,
						'unit' => 'px',
					],
					'header_sticky_logo'                   => '',
					'header_sticky_custom_logo'            => false,
					'header_sticky_mobile_logo'            => '',
					'header_sticky_custom_mobile_logo'     => false,
					'header_sticky_logo_width'             => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'header_sticky_site_title_color'       => [
						'color' => '',
					],
					'header_sticky_navigation_color'       => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'header_sticky_navigation_background'  => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					'header_sticky_button_color'           => [
						'color'           => '',
						'hover'           => '',
						'background'      => '',
						'backgroundHover' => '',
						'border'          => '',
						'borderHover'     => '',
					],
					'header_sticky_social_color'           => [
						'color'           => '',
						'hover'           => '',
						'background'      => '',
						'backgroundHover' => '',
						'border'          => '',
						'borderHover'     => '',
					],
					'header_sticky_html_color'             => [
						'color' => '',
						'link'  => '',
						'hover' => '',
					],
					'header_sticky_background'             => [
						'desktop' => [
							'color' => '',
						],
					],
					'header_sticky_bottom_border'          => [],
					'header_sticky_box_shadow'                 => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => 0,
						'inset'   => false,
					],
					// Footer.
					'footer_items'                         => [
						'top'    => [
							'top_1' => [],
							'top_2' => [],
							'top_3' => [],
							'top_4' => [],
							'top_5' => [],
						],
						'middle' => [
							'middle_1' => [],
							'middle_2' => [],
							'middle_3' => [],
							'middle_4' => [],
							'middle_5' => [],
						],
						'bottom' => [
							'bottom_1' => [ 'footer-html' ],
							'bottom_2' => [],
							'bottom_3' => [],
							'bottom_4' => [],
							'bottom_5' => [],
						],
					],
					'footer_wrap_background'               => [
						'desktop' => [
							'color' => '',
						],
					],
					// Footer Top.
					'footer_top_height'                    => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_top_column_spacing'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_top_widget_spacing'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_top_top_spacing'               => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_top_bottom_spacing'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_top_contain'                   => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'footer_top_columns'                   => '3',
					'footer_top_collapse'                  => 'normal',
					'footer_top_layout'                    => [
						'mobile'  => 'row',
						'tablet'  => '',
						'desktop' => 'equal',
					],
					'footer_top_direction'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'row',
					],
					'footer_top_background'                => [
						'desktop' => [
							'color' => '',
						],
					],
					'footer_top_top_border'                => [],
					'footer_top_bottom_border'             => [],
					'footer_top_column_border'             => [],
					'footer_top_widget_title'              => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_top_widget_content'            => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_top_widget_content_color'      => [
						'color' => '',
						'hover' => '',
					],
					'footer_top_link_style'                => 'plain',
					// Footer Middle.
					'footer_middle_height'                 => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_middle_column_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_middle_widget_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_middle_top_spacing'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_middle_bottom_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_middle_contain'                => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'footer_middle_collapse'               => 'normal',
					'footer_middle_columns'                => '3',
					'footer_middle_layout'                 => [
						'mobile'  => 'row',
						'tablet'  => '',
						'desktop' => 'equal',
					],
					'footer_middle_direction'              => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'row',
					],
					'footer_middle_background'             => [
						'desktop' => [
							'color' => '',
						],
					],
					'footer_middle_top_border'             => [],
					'footer_middle_bottom_border'          => [],
					'footer_middle_column_border'          => [],
					'footer_middle_widget_title'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_middle_widget_content'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_middle_widget_content_color'   => [
						'color' => '',
						'hover' => '',
					],
					'footer_middle_link_style'             => 'plain',
					// Footer Bottom.
					'footer_bottom_height'                 => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_bottom_column_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_bottom_widget_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_bottom_top_spacing'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_bottom_bottom_spacing'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '30',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'footer_bottom_contain'                => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'standard',
					],
					'footer_bottom_columns'                => '1',
					'footer_bottom_collapse'               => 'normal',
					'footer_bottom_layout'                 => [
						'mobile'  => 'row',
						'tablet'  => '',
						'desktop' => 'row',
					],
					'footer_bottom_direction'              => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => 'row',
					],
					'footer_bottom_background'             => [
						'desktop' => [
							'color' => '',
						],
					],
					'footer_bottom_top_border'             => [],
					'footer_bottom_bottom_border'          => [],
					'footer_bottom_column_border'          => [],
					'footer_bottom_widget_title'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_bottom_widget_content'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_bottom_widget_content_color'   => [
						'color' => '',
						'hover' => '',
					],
					'footer_bottom_link_style'             => 'plain',
					// Footer Navigation.
					'footer_navigation_typography'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'footer_navigation_spacing'            => [
						'size' => 1.2,
						'unit' => 'em',
					],
					'footer_navigation_vertical_spacing'   => [
						'size' => 0.6,
						'unit' => 'em',
					],
					'footer_navigation_stretch'            => false,
					'footer_navigation_style'              => 'standard',
					'footer_navigation_color'              => [
						'color'  => 'palette5',
						'hover'  => 'palette-highlight',
						'active' => 'palette3',
					],
					'footer_navigation_background'         => [
						'color'  => '',
						'hover'  => '',
						'active' => '',
					],
					// Footer Social.
					'footer_social_items'                  => [
						'items' => [
							[
								'id'      => 'facebook',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'facebook',
								'label'   => 'Facebook',
							],
							[
								'id'      => 'twitter',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'twitterAlt2',
								'label'   => 'X',
							],
							[
								'id'      => 'instagram',
								'enabled' => true,
								'source'  => 'icon',
								'url'     => '',
								'imageid' => '',
								'width'   => 24,
								'icon'    => 'instagramAlt',
								'label'   => 'Instagram',
							],
						],
					],
					'footer_social_style'                  => 'filled',
					'footer_social_show_label'             => false,
					'footer_social_item_spacing'           => [
						'size' => 0.3,
						'unit' => 'em',
					],
					'footer_social_icon_size'              => [
						'size' => 1,
						'unit' => 'em',
					],
					'footer_social_brand'                  => '',
					'footer_social_color'                  => [
						'color' => '',
						'hover' => '',
					],
					'footer_social_background'             => [
						'color' => '',
						'hover' => '',
					],
					'footer_social_border_colors'          => [
						'color' => '',
						'hover' => '',
					],
					'footer_social_border'                 => [
						'width' => 2,
						'unit'  => 'px',
						'style' => 'none',
					],
					'footer_social_border_radius'          => [
						'size' => 3,
						'unit' => 'px',
					],
					'footer_social_typography'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'footer_social_margin'                 => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					'footer_social_align'                  => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_social_vertical_align'         => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 1.
					'footer_widget1_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget1_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 2.
					'footer_widget2_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget2_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 3.
					'footer_widget3_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget3_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 4.
					'footer_widget4_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget4_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 5.
					'footer_widget5_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget5_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer Widget 6.
					'footer_widget6_align'                 => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'footer_widget6_vertical_align'        => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// Footer HTML.
					'footer_html_content'                  => '{copyright} {year} {site-title} {theme-credit}',
					'footer_html_typography'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'footer_html_link_color'               => [
						'color' => '',
						'hover' => '',
					],
					'footer_html_link_style'               => 'normal',
					'footer_html_margin'                   => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => false,
					],
					// Comments.
					'comment_form_before_list'             => false,
					'comment_form_remove_website'          => false,
					// 404.
					'404_layout'                           => 'normal',
					'404_content_style'                    => 'boxed',
					'404_vertical_padding'                 => 'show',
					'404_background'                       => '',
					'404_content_background'               => '',
					'404_sidebar_id'                       => 'sidebar-primary',
					// Page Layout.
					'page_layout'                          => 'normal',
					'page_content_style'                   => 'boxed',
					'page_vertical_padding'                => 'show',
					'page_comments'                        => false,
					'page_feature'                         => false,
					'page_feature_position'                => 'above',
					'page_feature_ratio'                   => '2-3',
					'page_background'                      => '',
					'page_content_background'              => '',
					'page_sidebar_id'                      => 'sidebar-primary',
					'page_title'                           => true,
					'page_title_layout'                    => 'above',
					'page_title_height'                    => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 200,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'page_title_inner_layout'              => 'standard',
					'page_title_background'                => [
						'desktop' => [
							'color' => '',
						],
					],
					'page_title_featured_image'            => false,
					'page_title_top_border'                => [],
					'page_title_bottom_border'             => [],
					'page_title_align'                     => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'page_title_font'                      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'page_title_breadcrumb_color'          => [
						'color' => '',
						'hover' => '',
					],
					'page_title_breadcrumb_font'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'page_title_meta_color'                => [
						'color' => '',
						'hover' => '',
					],
					'page_title_meta_font'                 => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'page_title_elements'                  => [ 'title', 'breadcrumb', 'meta' ],
					'page_title_element_title'             => [
						'enabled' => true,
					],
					'page_title_element_breadcrumb'        => [
						'enabled'    => false,
						'show_title' => true,
					],
					'page_title_element_meta'              => [
						'id'                     => 'meta',
						'enabled'                => false,
						'divider'                => 'dot',
						'author'                 => true,
						'authorImage'            => false,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'comments'               => false,
						'commentsCondition'      => false,
					],
					// Post Layout.
					'post_layout'                          => 'narrow',
					'post_content_style'                   => 'boxed',
					'post_vertical_padding'                => 'show',
					'post_sidebar_id'                      => 'sidebar-primary',
					'post_comments'                        => true,
					'post_comments_date'                   => true,
					'post_footer_area_boxed'               => false,
					'post_navigation'                      => true,
					'post_related'                         => true,
					'post_related_style'                   => 'wide',
					'post_related_carousel_loop'           => true,
					'post_related_carousel_dots'           => true,
					'post_related_columns'                 => '',
					'post_related_title'                   => '',
					'post_related_orderby'                 => '',
					'post_related_order'                   => '',
					'post_related_title_font'              => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'post_related_background'              => '',
					'post_tags'                            => true,
					'post_author_box'                      => false,
					'post_author_box_style'                => 'normal',
					'post_author_box_link'                 => true,
					'post_feature'                         => true,
					'post_feature_position'                => 'behind',
					'post_feature_caption'                 => false,
					'post_feature_ratio'                   => '2-3',
					'post_feature_width'                   => 'wide',
					'post_background'                      => '',
					'post_content_background'              => '',
					'post_title'                           => true,
					'post_title_layout'                    => 'normal',
					'post_title_height'                    => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 200,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'post_title_inner_layout'              => 'standard',
					'post_title_background'                => [
						'desktop' => [
							'color' => '',
						],
					],
					'post_title_featured_image'            => false,
					'post_title_overlay_color'             => [
						'color' => '',
					],
					'post_title_top_border'                => [],
					'post_title_bottom_border'             => [],
					'post_title_align'                     => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'post_title_font'                      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'post_title_breadcrumb_color'          => [
						'color' => '',
						'hover' => '',
					],
					'post_title_breadcrumb_font'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_title_meta_color'                => [
						'color' => '',
						'hover' => '',
					],
					'post_title_meta_font'                 => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_title_category_color'            => [
						'color' => '',
						'hover' => '',
					],
					'post_title_category_font'             => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_title_excerpt_color'             => [
						'color' => '',
						'hover' => '',
					],
					'post_title_excerpt_font'              => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_title_elements'                  => [ 'breadcrumb', 'categories', 'title', 'meta', 'excerpt' ],
					'post_title_element_categories'        => [
						'enabled' => true,
						'style'   => 'normal',
						'divider' => 'vline',
					],
					'post_title_element_title'             => [
						'enabled' => true,
					],
					'post_title_element_breadcrumb'        => [
						'enabled'    => false,
						'show_title' => true,
					],
					'post_title_element_excerpt'           => [
						'enabled' => false,
					],
					'post_title_element_meta'              => [
						'id'                     => 'meta',
						'enabled'                => true,
						'divider'                => 'dot',
						'author'                 => true,
						'authorLink'             => true,
						'authorImage'            => false,
						'authorImageSize'        => 25,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'categories'             => false,
						'categoriesEnableLabel'  => false,
						'categoriesLabel'        => '',
						'comments'               => false,
						'commentsCondition'      => false,
					],

					// enable_preload css style sheets.
					'enable_preload'                       => false,
					'disable_sitemap'                      => false,
					'breadcrumb_engine'                    => '',
					'breadcrumb_home_icon'                 => false,
					// Post Archive.
					'post_archive_title'                   => true,
					'post_archive_home_title'              => false,
					'post_archive_title_layout'            => 'above',
					'post_archive_title_inner_layout'      => 'standard',
					'post_archive_title_height'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'post_archive_title_elements'          => [ 'breadcrumb', 'title', 'description' ],
					'post_archive_title_element_title'     => [
						'enabled' => true,
					],
					'post_archive_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					'post_archive_title_element_description' => [
						'enabled' => true,
					],
					'post_archive_title_background'        => [
						'desktop' => [
							'color' => '',
						],
					],
					'post_archive_title_align'             => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'post_archive_title_overlay_color'     => [
						'color' => '',
					],
					'post_archive_title_breadcrumb_color'  => [
						'color' => '',
						'hover' => '',
					],
					'post_archive_title_color'             => [
						'color' => '',
					],
					'post_archive_description_color'       => [
						'color' => '',
						'hover' => '',
					],
					'post_archive_layout'                  => 'normal',
					'post_archive_content_style'           => 'boxed',
					'post_archive_columns'                 => '3',
					'post_archive_item_image_placement'    => 'above',
					'post_archive_item_vertical_alignment' => 'top',
					'post_archive_sidebar_id'              => 'sidebar-primary',
					'post_archive_elements'                => [ 'feature', 'categories', 'title', 'meta', 'excerpt', 'readmore' ],
					'post_archive_element_categories'      => [
						'enabled' => true,
						'style'   => 'normal',
						'divider' => 'vline',
					],
					'post_archive_element_title'           => [
						'enabled' => true,
					],
					'post_archive_element_meta'            => [
						'id'                     => 'meta',
						'enabled'                => true,
						'divider'                => 'dot',
						'author'                 => true,
						'authorLink'             => true,
						'authorImage'            => false,
						'authorImageSize'        => 25,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'categories'             => false,
						'categoriesEnableLabel'  => false,
						'categoriesLabel'        => '',
						'comments'               => false,
						'commentsCondition'      => false,
					],
					'post_archive_element_feature'         => [
						'enabled'   => true,
						'ratio'     => '2-3',
						'size'      => 'medium_large',
						'imageLink' => true,
					],
					'post_archive_element_excerpt'         => [
						'enabled'     => true,
						'words'       => 55,
						'fullContent' => false,
					],
					'post_archive_element_readmore'        => [
						'enabled' => true,
						'label'   => '',
					],
					'post_archive_item_title_font'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_archive_item_category_color'     => [
						'color' => '',
						'hover' => '',
					],
					'post_archive_item_category_font'      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_archive_item_meta_color'         => [
						'color' => '',
						'hover' => '',
					],
					'post_archive_item_meta_font'          => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'post_archive_background'              => '',
					'post_archive_content_background'      => '',
					'post_archive_column_layout'           => 'grid',
					// Search Results.
					'search_archive_title'                 => true,
					'search_archive_title_layout'          => 'normal',
					'search_archive_title_inner_layout'    => 'standard',
					'search_archive_title_height'          => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'search_archive_title_background'      => [
						'desktop' => [
							'color' => '',
						],
					],
					'search_archive_title_align'           => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'search_archive_title_overlay_color'   => [
						'color' => '',
					],
					'search_archive_title_breadcrumb_color' => [
						'color' => '',
						'hover' => '',
					],
					'search_archive_title_color'           => [
						'color' => '',
					],
					'search_archive_description_color'     => [
						'color' => '',
						'hover' => '',
					],
					'search_archive_layout'                => 'normal',
					'search_archive_content_style'         => 'boxed',
					'search_archive_columns'               => '3',
					'search_archive_item_image_placement'  => 'above',
					'search_archive_sidebar_id'            => 'sidebar-primary',
					'search_archive_elements'              => [ 'feature', 'categories', 'title', 'meta', 'excerpt', 'readmore' ],
					'search_archive_element_categories'    => [
						'enabled' => true,
						'style'   => 'normal',
						'divider' => 'vline',
					],
					'search_archive_element_title'         => [
						'enabled' => true,
					],
					'search_archive_element_meta'          => [
						'id'                     => 'meta',
						'enabled'                => true,
						'divider'                => 'dot',
						'author'                 => true,
						'authorLink'             => true,
						'authorImage'            => false,
						'authorImageSize'        => 25,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'categories'             => false,
						'categoriesEnableLabel'  => false,
						'categoriesLabel'        => '',
						'comments'               => false,
						'commentsCondition'      => false,
					],
					'search_archive_element_feature'       => [
						'enabled' => true,
						'ratio'   => '2-3',
						'size'    => 'medium_large',
					],
					'search_archive_element_excerpt'       => [
						'enabled'     => true,
						'words'       => 55,
						'fullContent' => false,
					],
					'search_archive_element_readmore'      => [
						'enabled' => true,
						'label'   => '',
					],
					'search_archive_item_title_font'       => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'search_archive_item_category_color'   => [
						'color' => '',
						'hover' => '',
					],
					'search_archive_item_category_font'    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'search_archive_item_meta_color'       => [
						'color' => '',
						'hover' => '',
					],
					'search_archive_item_meta_font'        => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'search_archive_background'            => '',
					'search_archive_content_background'    => '',
					'search_archive_column_layout'         => 'grid',
					// Product Archive Controls.
					'product_archive_toggle'               => true,
					'product_archive_default_view'         => 'grid',
					'product_archive_show_order'           => true,
					'product_archive_show_results_count'   => true,
					'product_archive_style'                => 'action-on-hover',
					'product_archive_image_hover_switch'   => 'none',
					'product_archive_button_style'         => 'text',
					'product_archive_button_align'         => false,
					'product_archive_title'                => true,
					'product_archive_title_layout'         => 'above',
					'product_archive_title_inner_layout'   => 'standard',
					'product_archive_title_height'         => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'product_archive_title_elements'       => [ 'breadcrumb', 'title', 'description' ],
					'product_archive_title_element_title'  => [
						'enabled' => true,
					],
					'product_archive_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					'product_archive_title_element_description' => [
						'enabled' => true,
					],
					'product_archive_title_background'     => [
						'desktop' => [
							'color' => '',
						],
					],
					'product_archive_title_align'          => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'product_archive_title_overlay_color'  => [
						'color' => '',
					],
					'product_archive_title_breadcrumb_color' => [
						'color' => '',
						'hover' => '',
					],
					'product_archive_title_heading_font'   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'product_archive_title_color'          => [
						'color' => '',
					],
					'product_archive_description_color'    => [
						'color' => '',
						'hover' => '',
					],
					'product_archive_layout'               => 'normal',
					'product_archive_content_style'        => 'boxed',
					'product_archive_sidebar_id'           => 'sidebar-primary',
					'product_archive_title_font'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'product_archive_price_font'           => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					// Archive Product Button.
					'product_archive_button_typography'    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'product_archive_button_color'         => [
						'color' => '',
						'hover' => '',
					],
					'product_archive_button_background'    => [
						'color' => '',
						'hover' => '',
					],
					'product_archive_button_border_colors' => [
						'color' => '',
						'hover' => '',
					],
					'product_archive_button_border'        => [
						'width' => 2,
						'unit'  => 'px',
						'style' => 'none',
					],
					'product_archive_button_shadow'        => [
						'color'   => 'rgba(0,0,0,0.0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => 0,
						'inset'   => false,
						'disabled' => false,
					],
					'product_archive_button_shadow_hover'  => [
						'color'   => 'rgba(0,0,0,0)',
						'hOffset' => 0,
						'vOffset' => 0,
						'blur'    => 0,
						'spread'  => 0,
						'inset'   => false,
						'disabled' => false,
					],
					'product_archive_button_radius'        => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'px',
						'locked' => true,
					],
					// Product Controls.
					'custom_quantity'                      => false,
					'product_archive_mobile_columns'       => 'default',
					'product_layout'                       => 'normal',
					'product_content_style'                => 'unboxed',
					'product_vertical_padding'             => 'show',
					'product_above_layout'                 => 'breadcrumbs',
					'product_sidebar_id'                   => 'sidebar-primary',
					'product_navigation'                   => false,
					'product_related'                      => true,
					'product_large_cart_button'            => false,
					'product_additional_weight_dimensions' => true,
					'product_related_style'                => 'standard',
					'product_related_columns'              => '4',
					'product_content_elements'             => [ 'category', 'title', 'rating', 'price', 'excerpt', 'add_to_cart', 'extras', 'payments', 'product_meta', 'share' ],
					'product_content_element_category'     => [
						'enabled' => false,
					],
					'product_content_element_title'        => [
						'enabled' => true,
					],
					'product_content_element_rating'       => [
						'enabled' => true,
					],
					'product_content_element_price'        => [
						'enabled'            => true,
						'show_shipping'      => false,
						'shipping_statement' => __( '& Free Shipping', 'kadence' ),
					],
					'product_content_element_excerpt'      => [
						'enabled' => true,
					],
					'product_content_element_add_to_cart'  => [
						'enabled'     => true,
						'button_size' => '',
					],
					'product_content_element_extras'       => [
						'enabled'        => false,
						'title'          => __( 'Free shipping on orders over $50!', 'kadence' ),
						'feature_1'      => __( 'Satisfaction Guaranteed', 'kadence' ),
						'feature_2'      => __( 'No Hassle Refunds', 'kadence' ),
						'feature_3'      => __( 'Secure Payments', 'kadence' ),
						'feature_4'      => '',
						'feature_5'      => '',
						'feature_1_icon' => 'shield_check',
						'feature_2_icon' => 'shield_check',
						'feature_3_icon' => 'shield_check',
						'feature_4_icon' => 'shield_check',
						'feature_5_icon' => 'shield_check',
					],
					'product_content_element_payments'     => [
						'enabled'          => false,
						'title'            => __( 'GUARANTEED SAFE CHECKOUT', 'kadence' ),
						'visa'             => true,
						'mastercard'       => true,
						'amex'             => true,
						'discover'         => true,
						'paypal'           => true,
						'applepay'         => false,
						'stripe'           => false,
						'link'             => false,
						'googlepay'        => false,
						'card_color'       => 'inherit',
						'custom_enable_01' => false,
						'custom_img_01'    => '',
						'custom_id_01'     => '',
						'custom_enable_02' => false,
						'custom_img_02'    => '',
						'custom_id_02'     => '',
						'custom_enable_03' => false,
						'custom_img_03'    => '',
						'custom_id_03'     => '',
						'custom_enable_04' => false,
						'custom_img_04'    => '',
						'custom_id_04'     => '',
						'custom_enable_05' => false,
						'custom_img_05'    => '',
						'custom_id_05'     => '',
					],
					'product_tab_style'                    => 'normal',
					'variation_direction'                  => 'horizontal',
					'product_tab_title'                    => true,
					'product_content_element_product_meta' => [
						'enabled' => true,
					],
					'product_content_element_share'        => [
						'enabled' => true,
					],
					'product_background'                   => '',
					'product_content_background'           => '',
					'product_title_elements'               => [ 'breadcrumb', 'category', 'above_title' ],
					'product_title_element_category'       => [
						'enabled' => true,
					],
					'product_title_element_above_title'    => [
						'enabled' => false,
					],
					'product_title_element_breadcrumb'     => [
						'enabled'    => false,
						'show_title' => true,
					],
					'product_title_height'                 => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => 200,
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'product_title_inner_layout'           => 'standard',
					'product_title_background'             => [
						'desktop' => [
							'color' => '',
						],
					],
					'product_title_overlay_color'          => [
						'color' => '',
					],
					'product_title_top_border'             => [],
					'product_title_bottom_border'          => [],
					'product_title_align'                  => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'product_title_font'                   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'product_single_category_font'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'product_above_title_font'             => [
						'size'       => [
							'desktop' => '32',
						],
						'lineHeight' => [
							'desktop' => '1.5',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => '',
					],
					'product_title_breadcrumb_color'       => [
						'color' => '',
						'hover' => '',
					],
					'product_above_category_font'          => [
						'size'       => [
							'desktop' => '32',
						],
						'lineHeight' => [
							'desktop' => '1.5',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '700',
						'variant'    => '700',
						'color'      => 'palette3',
					],
					// Store Notice:
					'woo_store_notice_placement'           => 'above',
					'woo_store_notice_hide_dismiss'        => false,
					'woo_store_notice_font'                => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'woo_store_notice_background'          => [
						'color' => '',
					],
					// Woo Account
					'woo_account_navigation_layout'        => 'right',
					'woo_account_navigation_avatar'        => true,
					// Heroic Knowledge Base.
					'ht_kb_header_search'                  => true,
					'ht_kb_archive_title_layout'           => 'above',
					'ht_kb_archive_layout'                 => 'normal',
					'ht_kb_archive_content_style'          => 'boxed',
					'ht_kb_title_elements'                 => [ 'breadcrumb', 'title' ],
					'ht_kb_title_element_title'            => [
						'enabled' => true,
					],
					'ht_kb_title_element_breadcrumb'       => [
						'enabled'    => true,
						'show_title' => true,
					],
					// Header Cart.
					'header_cart_label'                    => '',
					'header_cart_show_total'               => true,
					'header_cart_style'                    => 'link',
					'header_cart_popup_side'               => 'right',
					'header_cart_icon'                     => 'shopping-bag',
					'header_cart_icon_size'                => [
						'size' => '',
						'unit' => 'em',
					],
					'header_cart_color'                    => [
						'color' => '',
						'hover' => '',
					],
					'header_cart_background'               => [
						'color' => '',
						'hover' => '',
					],
					'header_cart_total_color'              => [
						'color' => '',
						'hover' => '',
					],
					'header_cart_total_background'         => [
						'color' => '',
						'hover' => '',
					],
					'header_cart_typography'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_cart_padding'                  => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'em',
						'locked' => false,
					],
					// Mobile Header Cart.
					'header_mobile_cart_label'             => '',
					'header_mobile_cart_show_total'        => true,
					'header_mobile_cart_style'             => 'link',
					'header_mobile_cart_popup_side'        => 'right',
					'header_mobile_cart_icon'              => 'shopping-bag',
					'header_mobile_cart_icon_size'         => [
						'size' => '',
						'unit' => 'em',
					],
					'header_mobile_cart_color'             => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_cart_background'        => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_cart_total_color'       => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_cart_total_background'  => [
						'color' => '',
						'hover' => '',
					],
					'header_mobile_cart_typography'        => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'header_mobile_cart_padding'           => [
						'size'   => [ '', '', '', '' ],
						'unit'   => 'em',
						'locked' => false,
					],
					// LifterLMS Course
					'course_syllabus_thumbs'               => false,
					'course_syllabus_thumbs_ratio'         => '2-3',
					'course_syllabus_columns'              => '1',
					'course_syllabus_lesson_style'         => 'standard',
					'course_layout'                        => 'normal',
					'course_content_style'                 => 'boxed',
					'course_vertical_padding'              => 'show',
					'course_sidebar_id'                    => 'llms_course_widgets_side',
					'course_feature'                       => false,
					'course_feature_position'              => 'behind',
					'course_feature_ratio'                 => '2-3',
					'course_comments'                      => false,
					'course_background'                    => '',
					'course_content_background'            => '',
					'course_title'                         => true,
					'course_title_layout'                  => 'normal',
					'course_title_height'                  => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'course_title_inner_layout'            => 'standard',
					'course_title_background'              => [
						'desktop' => [
							'color' => '',
						],
					],
					'course_title_featured_image'          => false,
					'course_title_overlay_color'           => [
						'color' => '',
					],
					'course_title_top_border'              => [],
					'course_title_bottom_border'           => [],
					'course_title_align'                   => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'course_title_font'                    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'course_title_breadcrumb_color'        => [
						'color' => '',
						'hover' => '',
					],
					'course_title_breadcrumb_font'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'course_title_elements'                => [ 'breadcrumb', 'title' ],
					'course_title_element_title'           => [
						'enabled' => true,
					],
					'course_title_element_breadcrumb'      => [
						'enabled'    => false,
						'show_title' => true,
					],
					// LifterLMS Lesson
					'lesson_layout'                        => 'right',
					'lesson_content_style'                 => 'boxed',
					'lesson_vertical_padding'              => 'show',
					'lesson_sidebar_id'                    => 'llms_lesson_widgets_side',
					'lesson_feature'                       => false,
					'lesson_comments'                      => false,
					'lesson_feature_position'              => 'behind',
					'lesson_feature_ratio'                 => '2-3',
					'lesson_background'                    => '',
					'lesson_content_background'            => '',
					'lesson_title'                         => true,
					'lesson_title_layout'                  => 'normal',
					'lesson_title_height'                  => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'lesson_title_inner_layout'            => 'standard',
					'lesson_title_background'              => [
						'desktop' => [
							'color' => '',
						],
					],
					'lesson_title_featured_image'          => false,
					'lesson_title_overlay_color'           => [
						'color' => '',
					],
					'lesson_title_top_border'              => [],
					'lesson_title_bottom_border'           => [],
					'lesson_title_align'                   => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'lesson_title_font'                    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'lesson_title_breadcrumb_color'        => [
						'color' => '',
						'hover' => '',
					],
					'lesson_title_breadcrumb_font'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'lesson_title_elements'                => [ 'breadcrumb', 'title' ],
					'lesson_title_element_title'           => [
						'enabled' => true,
					],
					'lesson_title_element_breadcrumb'      => [
						'enabled'    => false,
						'show_title' => true,
					],
					// LifterLMS Quiz
					'llms_quiz_layout'                     => 'right',
					'llms_quiz_content_style'              => 'boxed',
					'llms_quiz_vertical_padding'           => 'show',
					'llms_quiz_sidebar_id'                 => 'llms_lesson_widgets_side',
					'llms_quiz_title'                      => true,
					'llms_quiz_title_layout'               => 'normal',
					'llms_quiz_title_inner_layout'         => 'standard',
					'llms_quiz_title_align'                => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// LifterLMS Quiz
					'llms_membership_layout'               => 'normal',
					'llms_membership_content_style'        => 'boxed',
					'llms_membership_vertical_padding'     => 'show',
					'llms_membership_sidebar_id'           => 'sidebar-primary',
					'llms_membership_title'                => true,
					'llms_membership_title_layout'         => 'normal',
					'llms_membership_title_inner_layout'   => 'standard',
					'llms_membership_title_align'          => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					// LifterLMS Archive
					'course_archive_columns'               => '3',
					'course_archive_title'                 => true,
					'course_archive_title_layout'          => 'above',
					'course_archive_title_inner_layout'    => 'standard',
					'course_archive_title_height'          => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'course_archive_title_elements'        => [ 'breadcrumb', 'title', 'description' ],
					'course_archive_title_element_title'   => [
						'enabled' => true,
					],
					'course_archive_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					'course_archive_title_element_description' => [
						'enabled' => true,
					],
					'course_archive_title_background'      => [
						'desktop' => [
							'color' => '',
						],
					],
					'course_archive_title_align'           => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'course_archive_title_overlay_color'   => [
						'color' => '',
					],
					'course_archive_title_breadcrumb_color' => [
						'color' => '',
						'hover' => '',
					],
					'course_archive_title_color'           => [
						'color' => '',
					],
					'course_archive_description_color'     => [
						'color' => '',
						'hover' => '',
					],
					'course_archive_layout'                => 'normal',
					'course_archive_content_style'         => 'boxed',
					'course_archive_sidebar_id'            => 'sidebar-primary',
					// LifterLMS Member Archive
					'llms_membership_archive_columns'      => '3',
					'llms_membership_archive_title'        => true,
					'llms_membership_archive_title_layout' => 'above',
					'llms_membership_archive_title_inner_layout' => 'standard',
					'llms_membership_archive_title_height' => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'llms_membership_archive_title_elements' => [ 'breadcrumb', 'title', 'description' ],
					'llms_membership_archive_title_element_title' => [
						'enabled' => true,
					],
					'llms_membership_archive_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					'llms_membership_archive_title_element_description' => [
						'enabled' => true,
					],
					'llms_membership_archive_title_background' => [
						'desktop' => [
							'color' => '',
						],
					],
					'llms_membership_archive_title_align'  => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'llms_membership_archive_title_overlay_color' => [
						'color' => '',
					],
					'llms_membership_archive_title_breadcrumb_color' => [
						'color' => '',
						'hover' => '',
					],
					'llms_membership_archive_title_color'  => [
						'color' => '',
					],
					'llms_membership_archive_description_color' => [
						'color' => '',
						'hover' => '',
					],
					'llms_membership_archive_layout'       => 'normal',
					'llms_membership_archive_content_style' => 'boxed',
					'llms_membership_archive_sidebar_id'   => 'sidebar-primary',
					// Dashboard Layout
					'llms_dashboard_navigation_layout'     => 'right',
					'llms_dashboard_archive_columns'       => '3',
					// Learn Dash Course Grid.
					'learndash_course_grid'                => false,
					'learndash_course_grid_style'          => 'boxed',
					'sfwd-grid_title_font'                 => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					// Learn Dash Course Archive.
					'sfwd-courses_archive_columns'         => '3',
					'sfwd-courses_archive_title'           => true,
					'sfwd-courses_archive_title_layout'    => 'above',
					'sfwd-courses_archive_title_inner_layout' => 'standard',
					'sfwd-courses_archive_title_height'    => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-courses_archive_title_elements'  => [ 'breadcrumb', 'title', 'description' ],
					'sfwd-courses_archive_title_element_title' => [
						'enabled' => true,
					],
					'sfwd-courses_archive_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					'sfwd-courses_archive_title_element_description' => [
						'enabled' => true,
					],
					'sfwd-courses_archive_title_background' => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-courses_archive_title_align'     => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-courses_archive_title_overlay_color' => [
						'color' => '',
					],
					'sfwd-courses_archive_title_breadcrumb_color' => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-courses_archive_title_color'     => [
						'color' => '',
					],
					'sfwd-courses_archive_description_color' => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-courses_archive_layout'          => 'normal',
					'sfwd-courses_archive_content_style'   => 'boxed',
					'sfwd-courses_archive_sidebar_id'      => 'sidebar-primary',
					// Learn Dash Course
					'sfwd-courses_layout'                  => 'normal',
					'sfwd-courses_content_style'           => 'boxed',
					'sfwd-courses_comments'                => true,
					'sfwd-courses_vertical_padding'        => 'show',
					'sfwd-courses_sidebar_id'              => 'sidebar-primary',
					'sfwd-courses_feature'                 => false,
					'sfwd-courses_feature_position'        => 'behind',
					'sfwd-courses_feature_ratio'           => '2-3',
					'sfwd-courses_background'              => '',
					'sfwd-courses_content_background'      => '',
					'sfwd-courses_title'                   => true,
					'sfwd-courses_title_layout'            => 'normal',
					'sfwd-courses_title_height'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-courses_title_inner_layout'      => 'standard',
					'sfwd-courses_title_background'        => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-courses_title_featured_image'    => false,
					'sfwd-courses_title_overlay_color'     => [
						'color' => '',
					],
					'sfwd-courses_title_top_border'        => [],
					'sfwd-courses_title_bottom_border'     => [],
					'sfwd-courses_title_align'             => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-courses_title_font'              => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'sfwd-courses_title_breadcrumb_color'  => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-courses_title_breadcrumb_font'   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'sfwd-courses_title_elements'          => [ 'breadcrumb', 'title' ],
					'sfwd-courses_title_element_title'     => [
						'enabled' => true,
					],
					'sfwd-courses_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learndash Lessons.
					'sfwd-lessons_layout'                  => 'normal',
					'sfwd-lessons_comments'                => true,
					'sfwd-lessons_content_style'           => 'boxed',
					'sfwd-lessons_vertical_padding'        => 'show',
					'sfwd-lessons_sidebar_id'              => 'sidebar-primary',
					'sfwd-lessons_feature'                 => false,
					'sfwd-lessons_feature_position'        => 'behind',
					'sfwd-lessons_feature_ratio'           => '2-3',
					'sfwd-lessons_background'              => '',
					'sfwd-lessons_content_background'      => '',
					'sfwd-lessons_title'                   => true,
					'sfwd-lessons_title_layout'            => 'normal',
					'sfwd-lessons_title_height'            => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-lessons_title_inner_layout'      => 'standard',
					'sfwd-lessons_title_background'        => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-lessons_title_featured_image'    => false,
					'sfwd-lessons_title_overlay_color'     => [
						'color' => '',
					],
					'sfwd-lessons_title_top_border'        => [],
					'sfwd-lessons_title_bottom_border'     => [],
					'sfwd-lessons_title_align'             => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-lessons_title_font'              => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'sfwd-lessons_title_breadcrumb_color'  => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-lessons_title_breadcrumb_font'   => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'sfwd-lessons_title_elements'          => [ 'breadcrumb', 'title' ],
					'sfwd-lessons_title_element_title'     => [
						'enabled' => true,
					],
					'sfwd-lessons_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learndash Quiz.
					'sfwd-quiz_layout'                     => 'normal',
					'sfwd-quiz_comments'                   => true,
					'sfwd-quiz_content_style'              => 'boxed',
					'sfwd-quiz_vertical_padding'           => 'show',
					'sfwd-quiz_sidebar_id'                 => 'sidebar-primary',
					'sfwd-quiz_feature'                    => false,
					'sfwd-quiz_feature_position'           => 'behind',
					'sfwd-quiz_feature_ratio'              => '2-3',
					'sfwd-quiz_background'                 => '',
					'sfwd-quiz_content_background'         => '',
					'sfwd-quiz_title'                      => true,
					'sfwd-quiz_title_layout'               => 'normal',
					'sfwd-quiz_title_height'               => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-quiz_title_inner_layout'         => 'standard',
					'sfwd-quiz_title_background'           => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-quiz_title_featured_image'       => false,
					'sfwd-quiz_title_overlay_color'        => [
						'color' => '',
					],
					'sfwd-quiz_title_top_border'           => [],
					'sfwd-quiz_title_bottom_border'        => [],
					'sfwd-quiz_title_align'                => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-quiz_title_font'                 => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'sfwd-quiz_title_breadcrumb_color'     => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-quiz_title_breadcrumb_font'      => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'sfwd-quiz_title_elements'             => [ 'breadcrumb', 'title' ],
					'sfwd-quiz_title_element_title'        => [
						'enabled' => true,
					],
					'sfwd-quiz_title_element_breadcrumb'   => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learndash Topics.
					'sfwd-topic_layout'                    => 'normal',
					'sfwd-topic_content_style'             => 'boxed',
					'sfwd-topic_vertical_padding'          => 'show',
					'sfwd-topic_comments'                  => true,
					'sfwd-topic_sidebar_id'                => 'sidebar-primary',
					'sfwd-topic_feature'                   => false,
					'sfwd-topic_feature_position'          => 'behind',
					'sfwd-topic_feature_ratio'             => '2-3',
					'sfwd-topic_background'                => '',
					'sfwd-topic_content_background'        => '',
					'sfwd-topic_title'                     => true,
					'sfwd-topic_title_layout'              => 'normal',
					'sfwd-topic_title_height'              => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-topic_title_inner_layout'        => 'standard',
					'sfwd-topic_title_background'          => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-topic_title_featured_image'      => false,
					'sfwd-topic_title_overlay_color'       => [
						'color' => '',
					],
					'sfwd-topic_title_top_border'          => [],
					'sfwd-topic_title_bottom_border'       => [],
					'sfwd-topic_title_align'               => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-topic_title_font'                => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'sfwd-topic_title_breadcrumb_color'    => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-topic_title_breadcrumb_font'     => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'sfwd-topic_title_elements'            => [ 'breadcrumb', 'title' ],
					'sfwd-topic_title_element_title'       => [
						'enabled' => true,
					],
					'sfwd-topic_title_element_breadcrumb'  => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learn Dash Groups
					'groups_layout'                        => 'normal',
					'groups_content_style'                 => 'boxed',
					'groups_vertical_padding'              => 'show',
					'groups_sidebar_id'                    => 'sidebar-primary',
					'groups_feature'                       => false,
					'groups_feature_position'              => 'behind',
					'groups_feature_ratio'                 => '2-3',
					'groups_background'                    => '',
					'groups_content_background'            => '',
					'groups_title'                         => true,
					'groups_title_layout'                  => 'normal',
					'groups_title_height'                  => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'groups_title_inner_layout'            => 'standard',
					'groups_title_background'              => [
						'desktop' => [
							'color' => '',
						],
					],
					'groups_title_featured_image'          => false,
					'groups_title_overlay_color'           => [
						'color' => '',
					],
					'groups_title_top_border'              => [],
					'groups_title_bottom_border'           => [],
					'groups_title_align'                   => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'groups_title_font'                    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'groups_title_breadcrumb_color'        => [
						'color' => '',
						'hover' => '',
					],
					'groups_title_breadcrumb_font'         => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'groups_title_elements'                => [ 'breadcrumb', 'title' ],
					'groups_title_element_title'           => [
						'enabled' => true,
					],
					'groups_title_element_breadcrumb'      => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learn Dash essays
					'sfwd-essays_layout'                   => 'normal',
					'sfwd-essays_content_style'            => 'boxed',
					'sfwd-essays_vertical_padding'         => 'show',
					'sfwd-essays_sidebar_id'               => 'sidebar-primary',
					'sfwd-essays_comments'                 => true,
					'sfwd-essays_feature'                  => false,
					'sfwd-essays_feature_position'         => 'behind',
					'sfwd-essays_feature_ratio'            => '2-3',
					'sfwd-essays_background'               => '',
					'sfwd-essays_content_background'       => '',
					'sfwd-essays_title'                    => true,
					'sfwd-essays_title_layout'             => 'normal',
					'sfwd-essays_title_height'             => [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					],
					'sfwd-essays_title_inner_layout'       => 'standard',
					'sfwd-essays_title_background'         => [
						'desktop' => [
							'color' => '',
						],
					],
					'sfwd-essays_title_featured_image'     => false,
					'sfwd-essays_title_overlay_color'      => [
						'color' => '',
					],
					'sfwd-essays_title_top_border'         => [],
					'sfwd-essays_title_bottom_border'      => [],
					'sfwd-essays_title_align'              => [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					],
					'sfwd-essays_title_font'               => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					],
					'sfwd-essays_title_breadcrumb_color'   => [
						'color' => '',
						'hover' => '',
					],
					'sfwd-essays_title_breadcrumb_font'    => [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					],
					'sfwd-essays_title_elements'           => [ 'breadcrumb', 'title' ],
					'sfwd-essays_title_element_title'      => [
						'enabled' => true,
					],
					'sfwd-essays_title_element_breadcrumb' => [
						'enabled'    => false,
						'show_title' => true,
					],
					// Learndash Assignment.
					'sfwd-assignment_comments'             => true,
					// MISC
					'ie11_basic_support'                   => false,
					'theme_json_mode'                      => $version_1_3_0_changes ? true : false,
					'microdata'                            => true,
					'social_links_open_new_tab'            => $version_1_3_0_changes ? false : true,
				]
			);
		}
		return self::$default_options;
	}
	/**
	 * Get options from database
	 */
	public function get_options() {
		if ( is_null( self::$options ) ) {
			$options       = get_option( $this->get_option_name() );
			self::$options = wp_parse_args( $options, self::defaults() );
		}
		return self::$options;
	}
	/**
	 * Get options from database
	 */
	public function get_custom_options( $key ) {
		if ( is_customize_preview() ) {
			$options = get_option( $key );
			return wp_parse_args( $options, self::defaults() );
		}
		if ( ! isset( self::$custom_options[ $key ] ) ) {
			$options                      = get_option( $key );
			self::$custom_options[ $key ] = wp_parse_args( $options, self::defaults() );
		}
		return self::$custom_options[ $key ];
	}

	/**
	 * Add Custom Post type Defaults later in WordPress Load.
	 */
	public function add_default_options() {
		if ( is_null( self::$cpt_options ) ) {
			$add_options       = [];
			$all_post_types    = kadence()->get_post_types_objects();
			$extras_post_types = [];
			$add_extras        = false;
			foreach ( $all_post_types as $post_type_item ) {
				$post_type_name  = $post_type_item->name;
				$post_type_label = $post_type_item->label;
				$ignore_type     = kadence()->get_post_types_to_ignore();
				if ( ! in_array( $post_type_name, $ignore_type, true ) ) {
					// Single Items.
					$add_options[ $post_type_name . '_feature' ]                           = false;
					$add_options[ $post_type_name . '_feature_position' ]                  = 'behind';
					$add_options[ $post_type_name . '_feature_ratio' ]                     = '2-3';
					$add_options[ $post_type_name . '_background' ]                        = '';
					$add_options[ $post_type_name . '_content_background' ]                = '';
					$add_options[ $post_type_name . '_title' ]                             = true;
					$add_options[ $post_type_name . '_title_layout' ]                      = 'normal';
					$add_options[ $post_type_name . '_title_height' ]                      = [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					];
					$add_options[ $post_type_name . '_title_inner_layout' ]                = 'standard';
					$add_options[ $post_type_name . '_title_background' ]                  = [
						'desktop' => [
							'color' => '',
						],
					];
					$add_options[ $post_type_name . '_title_featured_image' ]              = false;
					$add_options[ $post_type_name . '_title_overlay_color' ]               = [
						'color' => '',
					];
					$add_options[ $post_type_name . '_title_top_border' ]                  = [];
					$add_options[ $post_type_name . '_title_bottom_border' ]               = [];
					$add_options[ $post_type_name . '_title_align' ]                       = [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					];
					$add_options[ $post_type_name . '_title_font' ]                        = [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
						'color'      => '',
					];
					$add_options[ $post_type_name . '_title_breadcrumb_color' ]            = [
						'color' => '',
						'hover' => '',
					];
					$add_options[ $post_type_name . '_title_breadcrumb_font' ]             = [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					];
					$add_options[ $post_type_name . '_title_meta_color' ]                  = [
						'color' => '',
						'hover' => '',
					];
					$add_options[ $post_type_name . '_title_meta_font' ]                   = [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					];
					$add_options[ $post_type_name . '_title_elements' ]                    = [ 'categories', 'title', 'breadcrumb', 'meta' ];
					$add_options[ $post_type_name . '_title_element_categories' ]          = [
						'enabled'    => false,
						'style'      => 'normal',
						'divider'    => 'vline',
						'taxonomies' => '',
					];
					$add_options[ $post_type_name . '_title_element_title' ]               = [
						'enabled' => true,
					];
					$add_options[ $post_type_name . '_title_element_breadcrumb' ]          = [
						'enabled'    => false,
						'show_title' => true,
					];
					$add_options[ $post_type_name . '_title_element_meta' ]                = [
						'id'                     => 'meta',
						'enabled'                => false,
						'divider'                => 'dot',
						'author'                 => true,
						'authorImage'            => false,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'comments'               => false,
						'commentsCondition'      => false,
					];
					$add_options[ $post_type_name . '_archive_title_height' ]              = [
						'size' => [
							'mobile'  => '',
							'tablet'  => '',
							'desktop' => '',
						],
						'unit' => [
							'mobile'  => 'px',
							'tablet'  => 'px',
							'desktop' => 'px',
						],
					];
					$add_options[ $post_type_name . '_archive_title_elements' ]            = [ 'breadcrumb', 'title', 'description' ];
					$add_options[ $post_type_name . '_archive_title_element_title' ]       = [
						'enabled' => true,
					];
					$add_options[ $post_type_name . '_archive_title_element_breadcrumb' ]  = [
						'enabled'    => false,
						'show_title' => true,
					];
					$add_options[ $post_type_name . '_archive_title_element_description' ] = [
						'enabled' => true,
					];
					$add_options[ $post_type_name . '_archive_title_background' ]          = [
						'desktop' => [
							'color' => '',
						],
					];
					$add_options[ $post_type_name . '_archive_title_align' ]               = [
						'mobile'  => '',
						'tablet'  => '',
						'desktop' => '',
					];
					$add_options[ $post_type_name . '_archive_title_overlay_color' ]       = [
						'color' => '',
					];
					$add_options[ $post_type_name . '_archive_title_breadcrumb_color' ]    = [
						'color' => '',
						'hover' => '',
					];
					$add_options[ $post_type_name . '_archive_title_color' ]               = [
						'color' => '',
					];
					$add_options[ $post_type_name . '_archive_description_color' ]         = [
						'color' => '',
						'hover' => '',
					];
					$add_options[ $post_type_name . '_archive_layout' ]                    = 'normal';
					$add_options[ $post_type_name . '_archive_content_style' ]             = 'boxed';
					$add_options[ $post_type_name . '_archive_columns' ]                   = '3';
					$add_options[ $post_type_name . '_archive_item_image_placement' ]      = 'above';
					$add_options[ $post_type_name . '_archive_sidebar_id' ]                = 'sidebar-primary';
					$add_options[ $post_type_name . '_archive_elements' ]                  = [ 'feature', 'categories', 'title', 'meta', 'excerpt', 'readmore' ];
					$add_options[ $post_type_name . '_archive_element_categories' ]        = [
						'enabled'  => false,
						'style'    => 'normal',
						'divider'  => 'vline',
						'taxonomy' => '',
					];
					$add_options[ $post_type_name . '_archive_element_title' ]             = [
						'enabled' => true,
					];
					$add_options[ $post_type_name . '_archive_element_meta' ]              = [
						'id'                     => 'meta',
						'enabled'                => false,
						'divider'                => 'dot',
						'author'                 => true,
						'authorLink'             => true,
						'authorImage'            => false,
						'authorImageSize'        => 25,
						'authorEnableLabel'      => true,
						'authorLabel'            => '',
						'date'                   => true,
						'dateTime'               => false,
						'dateEnableLabel'        => false,
						'dateLabel'              => '',
						'dateUpdated'            => false,
						'dateUpdatedTime'        => false,
						'dateUpdatedDifferent'   => false,
						'dateUpdatedEnableLabel' => false,
						'dateUpdatedLabel'       => '',
						'categories'             => false,
						'categoriesEnableLabel'  => false,
						'categoriesLabel'        => '',
						'comments'               => false,
						'commentsCondition'      => false,
					];
					$add_options[ $post_type_name . '_archive_element_feature' ]           = [
						'enabled' => true,
						'ratio'   => '2-3',
						'size'    => 'medium_large',
					];
					$add_options[ $post_type_name . '_archive_element_excerpt' ]           = [
						'enabled'     => true,
						'words'       => 55,
						'fullContent' => false,
					];
					$add_options[ $post_type_name . '_archive_element_readmore' ]          = [
						'enabled' => true,
						'label'   => '',
					];
					$add_options[ $post_type_name . '_archive_item_title_font' ]           = [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => '',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					];
					$add_options[ $post_type_name . '_archive_item_meta_color' ]           = [
						'color' => '',
						'hover' => '',
					];
					$add_options[ $post_type_name . '_archive_item_meta_font' ]            = [
						'size'       => [
							'desktop' => '',
						],
						'lineHeight' => [
							'desktop' => '',
						],
						'family'     => 'inherit',
						'google'     => false,
						'weight'     => '',
						'variant'    => '',
					];
					$add_options[ $post_type_name . '_archive_background' ]                = '';
					$add_options[ $post_type_name . '_archive_content_background' ]        = '';
				}
			}
			self::$cpt_options     = $add_options;
			self::$options         = wp_parse_args( self::get_options(), self::$cpt_options );
			self::$default_options = wp_parse_args( self::defaults(), self::$cpt_options );
		}
	}

	/**
	 * Get Default for option.
	 *
	 * @param string $key option key.
	 */
	public function default( $key, $backup = null ) {

		$defaults = self::defaults();
		$value    = ( isset( $defaults[ $key ] ) && '' !== $defaults[ $key ] ) ? $defaults[ $key ] : null;
		if ( is_null( $value ) ) {
			$value = $backup;
		}

		return $value;
	}

	/**
	 * Get Option
	 *
	 * @param string $key     option key.
	 * @param mix    $default option default.
	 */
	public function option( $key, $default = '' ) {
		$defaults = self::defaults();
		if ( ! empty( $opt_name_key = apply_filters( 'kadence_settings_key_custom_mapping', '', $key ) ) ) {
			$options = self::get_custom_options( $opt_name_key );
			$value   = ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) ? $options[ $key ] : null;
		} elseif ( 'option' === $this->get_option_type() ) {
				$options = self::get_options();
				$value   = ( isset( $options[ $key ] ) && '' !== $options[ $key ] ) ? $options[ $key ] : null;
		} else {
			$value = get_theme_mod( $key, null );
		}
		// Fallback to defaults array.
		if ( is_null( $value ) || ( isset( $value ) && '' === $value ) ) {
			$value = ( isset( $defaults[ $key ] ) && '' !== $defaults[ $key ] ) ? $defaults[ $key ] : null;
		}
		// Fallback to default.
		if ( is_null( $value ) || ( isset( $value ) && '' === $value ) ) {
			$value = $default;
		}

		return $value;
	}

	/**
	 * Get setting of option array.
	 *
	 * @param string $key        option key.
	 * @param string $first_key  option array first key.
	 * @param string $second_key option array second key.
	 * @param string $third_key  option array third key.
	 */
	public function sub_option( $key, $first_key = '', $second_key = '', $third_key = '' ) {
		$value = $this->option( $key );
		if ( ! empty( $first_key ) ) {
			if ( isset( $value[ $first_key ] ) && ( ! empty( $value[ $first_key ] ) || 0 === $value[ $first_key ] ) ) {
				$value = $value[ $first_key ];
			} else {
				$value = null;
			}
			if ( ! empty( $second_key ) ) {
				if ( isset( $value[ $second_key ] ) && ( ! empty( $value[ $second_key ] ) || 0 === $value[ $second_key ] ) ) {
					$value = $value[ $second_key ];
				} else {
					$value = null;
				}
				if ( ! empty( $third_key ) ) {
					if ( isset( $value[ $third_key ] ) && ( ! empty( $value[ $third_key ] ) || 0 === $value[ $third_key ] ) ) {
						$value = $value[ $third_key ];
					} else {
						$value = null;
					}
				}
			}
		}

		return $value;
	}
	/**
	 * Get Pro Url
	 * 
	 * @param string $url         url.
	 * @param string $initial_url initial url.
	 * @param string $source      source.
	 * @param string $medium      medium.
	 * @param string $campaign    campaign.
	 */
	public function get_pro_url( $url, $initial_url = '', $source = '', $medium = '', $campaign = '' ) {
		if ( empty( $initial_url ) ) {
			$initial_url = $url;
		}
		$partner_url = get_option( 'kadence_partner_pro_url', '' );
		if ( ! empty( $partner_url ) && in_array( $partner_url, self::$allowed_urls, true ) ) {
			$url = $partner_url;
		}
		$url = apply_filters( 'kadence_get_pro_url', $url, $initial_url );
		if ( in_array( $url, self::$allowed_urls, true ) ) {
			$url = $url;
		} else {
			$url = $initial_url;
		}
		// Add utm source.
		if ( ! empty( $source ) ) {
			$url = add_query_arg( 'utm_source', sanitize_text_field( $source ), $url );
		}
		// Add UTM medium.
		if ( ! empty( $medium ) ) {
			$url = add_query_arg( 'utm_medium', sanitize_text_field( $medium ), $url );
		}
		// Add UTM campaign.
		if ( ! empty( $campaign ) ) {
			$url = add_query_arg( 'utm_campaign', sanitize_text_field( $campaign ), $url );
		}
		return $url;
	}
	/**
	 * Get Palette
	 */
	public function get_palette_for_customizer() {
		$palette = get_option( 'kadence_global_palette' );
		if ( $palette && ! empty( $palette ) ) {
			$palette = json_decode( $palette, true );
			if ( isset( $palette['palette'] ) && is_array( $palette['palette'] ) ) {
				if ( isset( $palette['active'] ) && ! empty( $palette['active'] ) ) {
					$palette = json_encode( $palette );
				} else {
					$palette = self::palette_defaults();
				}
			} else {
				$palette = self::palette_defaults();
			}
		} else {
			$palette = self::palette_defaults();
		}
		return $palette;
	}
	/**
	 * Get Palette
	 */
	public function get_palette() {
		$palette = get_option( 'kadence_global_palette' );
		if ( ! $palette || empty( $palette ) ) {
			$palette = self::palette_defaults();
		}
		return $palette;
	}
	/**
	 * Get Palette
	 */
	public function get_default_palette() {
		return self::palette_defaults();
	}
	/**
	 * Set default theme option values
	 *
	 * @return default values of the theme.
	 */
	public static function palette_defaults() {
		// Don't store defaults until after init.
		if ( is_null( self::$default_palette ) ) {
			self::$default_palette = apply_filters( 
				'kadence_global_palette_defaults', 
				'{"palette":[{"color":"#2B6CB0","slug":"palette1","name":"Palette Color 1"},
				{"color":"#215387","slug":"palette2","name":"Palette Color 2"},
				{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},
				{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},
				{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},
				{"color":"#718096","slug":"palette6","name":"Palette Color 6"},
				{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},
				{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},
				{"color":"#ffffff","slug":"palette9","name":"Palette Color 9"},
				{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},
				{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},
				{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},
				{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},
				{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},
				{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],
				"second-palette":[{"color":"#2B6CB0","slug":"palette1","name":"Palette Color 1"},
				{"color":"#215387","slug":"palette2","name":"Palette Color 2"},
				{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},
				{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},
				{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},
				{"color":"#718096","slug":"palette6","name":"Palette Color 6"},
				{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},
				{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},
				{"color":"#ffffff","slug":"palette9","name":"Palette Color 9"},
				{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},
				{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},
				{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},
				{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},
				{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},
				{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],
				"third-palette":[{"color":"#2B6CB0","slug":"palette1","name":"Palette Color 1"},
				{"color":"#215387","slug":"palette2","name":"Palette Color 2"},
				{"color":"#1A202C","slug":"palette3","name":"Palette Color 3"},
				{"color":"#2D3748","slug":"palette4","name":"Palette Color 4"},
				{"color":"#4A5568","slug":"palette5","name":"Palette Color 5"},
				{"color":"#718096","slug":"palette6","name":"Palette Color 6"},
				{"color":"#EDF2F7","slug":"palette7","name":"Palette Color 7"},
				{"color":"#F7FAFC","slug":"palette8","name":"Palette Color 8"},
				{"color":"#ffffff","slug":"palette9","name":"Palette Color 9"},
				{"color":"#FfFfFf","slug":"palette10","name":"Palette Color Complement"},
				{"color":"#13612e","slug":"palette11","name":"Palette Color Success"},
				{"color":"#1159af","slug":"palette12","name":"Palette Color Info"},
				{"color":"#b82105","slug":"palette13","name":"Palette Color Alert"},
				{"color":"#f7630c","slug":"palette14","name":"Palette Color Warning"},
				{"color":"#f5a524","slug":"palette15","name":"Palette Color Rating"}],
				"active":"palette"}' );
		}
		return self::$default_palette;
	}
	/**
	 * Get Palette Option.
	 *
	 * @param string $subkey         option subkey.
	 * @param string $active_palette the active palette.
	 */
	public function palette_option( $subkey, $active_palette = null ) {
		if ( is_null( self::$palette ) ) {
			$palette = get_option( 'kadence_global_palette' );
			if ( $palette && ! empty( $palette ) ) {
				self::$palette = json_decode( $palette, true );
			} else {
				self::$palette = json_decode( self::palette_defaults(), true );
			}
		}
		$active = ! empty( $active_palette ) ? $active_palette : apply_filters( 'kadence_active_palette', ( self::$palette && is_array( self::$palette ) && isset( self::$palette['active'] ) && ! empty( self::$palette['active'] ) ? self::$palette['active'] : 'palette' ) );
		$value  = '';
		if ( self::$palette && is_array( self::$palette ) && isset( self::$palette[ $active ] ) && is_array( self::$palette[ $active ] ) ) {
			$palette_number = (int) preg_replace('/[^0-9]/', '', $subkey) - 1;
			$palette_item   = ( isset( self::$palette[ $active ][ $palette_number ] ) && is_array( self::$palette[ $active ][ $palette_number ] ) ? self::$palette[ $active ][ $palette_number ] : [] );
			if ( isset( $palette_item['slug'] ) && $palette_item['slug'] === $subkey ) {
				// #FfFfFf is the key to sync palette 10 with the complement color.
				if ( 'palette10' == $subkey && '#FfFfFf' == $palette_item['color'] ) {
					$value = apply_filters( 'kadence_palette_complement_color', 'oklch(from var(--global-palette1) calc(l + 0.10 * (1 - l)) calc(c * 1.00) calc(h + 180) / 100%)' );
				} else {
					$value = ( isset( $palette_item['color'] ) && ! empty( $palette_item['color'] ) ? $palette_item['color'] : '' );
				}
			}
		}

		return apply_filters( 'kadence_palette_option', $value, $subkey );
	}
	/**
	 * Get all the kadence_header posts to show in the customizer.
	 *
	 * @access public
	 * @return array
	 */
	public function block_header_options() {
		if ( is_null( self::$headers ) ) {
			$headers = [ '' => [ 'name' => esc_html__( 'Select', 'kadence' ) ] ];
			if ( defined( 'KADENCE_BLOCKS_VERSION' ) ) {
				$args  = [
					'post_type'      => 'kadence_header',
					'posts_per_page' => 100,
					'post_status'    => 'publish',
					'order'          => 'ASC',
					'orderby'        => 'menu_order',
				];
				$posts = get_posts( $args );
				foreach ( $posts as $post ) {
					$headers[ $post->ID ] = [
						'name' => $post->post_title,
					];
				}
			}
			self::$headers = $headers;
		}
		return self::$headers;
	}
	/**
	 * Get Customizer Sidebar Options
	 *
	 * @access public
	 * @return array
	 */
	public function sidebar_options() {
		// Don't store defaults until after init.
		if ( is_null( self::$sidebars ) ) {
			$sidebars    = [];
			$nonsidebars = [
				'header1',
				'header2',
				'footer1',
				'footer2',
				'footer3',
				'footer4',
				'footer5',
				'footer6',
			];
			foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
				if ( ! in_array( $sidebar['id'], $nonsidebars, true ) ) {
					$sidebars[ $sidebar['id'] ] = [ 'name' => $sidebar['name'] ];
				}
			}
			self::$sidebars = $sidebars;
		}
		return self::$sidebars;
	}
}
