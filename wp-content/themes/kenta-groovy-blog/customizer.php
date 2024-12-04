<?php
/**
 * Customizer settings default value
 *
 * @package Kenta Groovy Blog
 */

if ( ! function_exists( 'kenta_groovy_blog_return_yes' ) ) {
	function kenta_groovy_blog_return_yes() {
		return 'yes';
	}
}

if ( ! function_exists( 'kenta_groovy_blog_return_no' ) ) {
	function kenta_groovy_blog_return_no() {
		return 'no';
	}
}

// Enable site wrap by default
add_filter( 'kenta_enable_site_wrap_default_value', 'kenta_groovy_blog_return_yes' );
// Disable transparent header by default
add_filter( 'kenta_enable_transparent_header_default_value', 'kenta_groovy_blog_return_no' );

//
//  Card style
//
if ( ! function_exists( 'kenta_groovy_blog_card_preset' ) ) {
	function kenta_groovy_blog_card_preset() {
		return 'solid-shadow';
	}
}
add_filter( 'kenta_card_style_preset_default_value', 'kenta_groovy_blog_card_preset' );
add_filter( 'kenta_store_card_style_preset_default_value', 'kenta_groovy_blog_card_preset' );
add_filter( 'kenta_global_sidebar_widgets-style_default_value', 'kenta_groovy_blog_card_preset' );
//add_filter( 'kenta_post_content_style_preset_default_value', 'kenta_groovy_blog_card_preset' );

//
// Form style
//
if ( ! function_exists( 'kenta_groovy_blog_form_style' ) ) {
	function kenta_groovy_blog_form_style() {
		return 'modern';
	}
}
add_filter( 'kenta_content_form_style_default_value', 'kenta_groovy_blog_form_style' );

//
// Sidebar
//
add_filter( 'kenta_post_sidebar_section_default_value', 'kenta_groovy_blog_return_no' );
add_filter( 'kenta_archive_sidebar_section_default_value', 'kenta_groovy_blog_return_yes' );

if ( ! function_exists( 'kenta_groovy_blog_widget_radius' ) ) {
	function kenta_groovy_blog_widget_radius() {
		return [
			'linked' => false,
			'left'   => '18px',
			'right'  => '18px',
			'top'    => '18px',
			'bottom' => '18px',
		];
	}
}
add_filter( 'kenta_global_sidebar_widgets-radius_default_value', 'kenta_groovy_blog_widget_radius' );

//
// Archive
//
if ( ! function_exists( 'kenta_groovy_blog_archive_columns' ) ) {
	function kenta_groovy_blog_archive_columns() {
		return [
			'desktop' => 2,
			'tablet'  => 2,
			'mobile'  => 1,
		];
	}
}
add_filter( 'kenta_archive_columns_default_value', 'kenta_groovy_blog_archive_columns' );

//
// Default color preset
//

if ( ! function_exists( 'kenta_groovy_blog_default_color_presets' ) ) {
	function kenta_groovy_blog_default_color_presets() {
		return 'groovy-1';
	}
}
add_filter( 'kenta_color_palettes_default_value', 'kenta_groovy_blog_default_color_presets' );

if ( ! function_exists( 'kenta_groovy_blog_color_presets' ) ) {
	function kenta_groovy_blog_color_presets( $presets ) {
		return [
			'groovy-1' => [
				'kenta-primary-color'  => '#f32d71',
				'kenta-primary-active' => '#f32d71',
				'kenta-accent-color'   => '#261e40',
				'kenta-accent-active'  => '#261e40',
				'kenta-base-300'       => '#261e40',
				'kenta-base-200'       => '#242835',
				'kenta-base-100'       => '#e9ecf7',
				'kenta-base-color'     => '#ffffff',
			],
			'groovy-2' => [
				'kenta-primary-color'  => '#ffe33c',
				'kenta-primary-active' => '#063aaf',
				'kenta-accent-color'   => '#000000',
				'kenta-accent-active'  => '#261e40',
				'kenta-base-300'       => '#261e40',
				'kenta-base-200'       => '#242835',
				'kenta-base-100'       => '#f0faff',
				'kenta-base-color'     => '#ffffff',
			],
			'groovy-3' => [
				'kenta-primary-color'  => '#0db2ac',
				'kenta-primary-active' => '#f42c70',
				'kenta-accent-color'   => '#261f41',
				'kenta-accent-active'  => '#261f41',
				'kenta-base-300'       => '#261f41',
				'kenta-base-200'       => '#261f41',
				'kenta-base-100'       => '#fbf4f4',
				'kenta-base-color'     => '#ffffff',
			],
			'groovy-4' => [
				'kenta-primary-color'  => '#d75151',
				'kenta-primary-active' => '#e5989b',
				'kenta-accent-color'   => '#261f41',
				'kenta-accent-active'  => '#261f41',
				'kenta-base-300'       => '#261f41',
				'kenta-base-200'       => '#261f41',
				'kenta-base-100'       => '#e8e9ff',
				'kenta-base-color'     => '#fffdc9',
			],
			'groovy-5' => [
				'kenta-primary-color'  => '#d75151',
				'kenta-primary-active' => '#e5989b',
				'kenta-accent-color'   => '#261f41',
				'kenta-accent-active'  => '#261f41',
				'kenta-base-300'       => '#261f41',
				'kenta-base-200'       => '#261f41',
				'kenta-base-100'       => '#e9f4e7',
				'kenta-base-color'     => '#fffde1',
			],
			'high-contrast' => [
				'kenta-primary-color'  => '#000000',
				'kenta-primary-active' => '#000000',
				'kenta-accent-color'   => '#000000',
				'kenta-accent-active'  => '#000000',
				'kenta-base-300'       => '#000000',
				'kenta-base-200'       => '#000000',
				'kenta-base-100'       => '#ffffff',
				'kenta-base-color'     => '#ffffff',
			],
		];
	}
}
add_filter( 'kenta_filter_color_presets', 'kenta_groovy_blog_color_presets' );

//
// Dark color preset
//
if ( ! function_exists( 'kenta_groovy_blog_dark_base_color' ) ) {
	function kenta_groovy_blog_dark_base_color() {
		return [
			'300'     => '#ffffff',
			'200'     => '#ffffff',
			'100'     => '#191929',
			'default' => '#000000',
		];
	}
}
add_filter( 'kenta_dark_base_color_default_value', 'kenta_groovy_blog_dark_base_color' );

if ( ! function_exists( 'kenta_groovy_blog_dark_accent_color' ) ) {
	function kenta_groovy_blog_dark_accent_color() {
		return [
			'default' => '#ffffff',
			'active'  => '#ffffff',
		];
	}
}
add_filter( 'kenta_dark_accent_color_default_value', 'kenta_groovy_blog_dark_accent_color' );

//
// Global typography
//
if ( ! function_exists( 'kenta_groovy_blog_global_typography' ) ) {
	function kenta_groovy_blog_global_typography() {
		return [
			'family'   => 'inter',
			'fontSize' => '18px',
			'variant'  => '400',
		];
	}
}
add_filter( 'kenta_site_global_typography_default_value', 'kenta_groovy_blog_global_typography' );

//
// Social Networks
//
if ( ! function_exists( 'kenta_groovy_blog_social_networks' ) ) {
	function kenta_groovy_blog_social_networks() {
		return [
			[
				'visible'  => true,
				'settings' => [
					'color' => [ 'official' => '#557dbc' ],
					'label' => 'Facebook',
					'url'   => '',
					'share' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
					'icon'  => [ 'value' => 'fab fa-facebook', 'library' => 'fa-brands' ]
				],
			],
			[
				'visible'  => true,
				'settings' => [
					'color' => [ 'official' => '#000000' ],
					'label' => 'Twitter',
					'url'   => '',
					'share' => 'https://twitter.com/share?url={url}&text={text}',
					'icon'  => [ 'value' => 'fab fa-x-twitter', 'library' => 'fa-brands' ]
				],
			],
			[
				'visible'  => true,
				'settings' => [
					'color' => [ 'official' => '#ed1376' ],
					'label' => 'Instagram',
					'url'   => '',
					'icon'  => [ 'value' => 'fab fa-instagram', 'library' => 'fa-brands' ]
				],
			],
			[
				'visible'  => true,
				'settings' => [
					'color' => [ 'official' => '#f42e53' ],
					'label' => 'Tiktok',
					'url'   => '',
					'icon'  => [ 'value' => 'fab fa-tiktok', 'library' => 'fa-brands' ]
				],
			],
		];
	}
}
add_filter( 'kenta_social_networks_default_value', 'kenta_groovy_blog_social_networks' );

//
// Header elements
//

if ( ! function_exists( 'kenta_groovy_blog_header_primary_row_elements' ) ) {
	function kenta_groovy_blog_header_primary_row_elements() {
		return [
			'desktop' => [
				[
					'elements' => [ 'socials' ],
					'settings' => [ 'width' => '30%' ]
				],
				[
					'elements' => [ 'logo' ],
					'settings' => [ 'width' => '40%', 'justify-content' => 'center' ]
				],
				[
					'elements' => [ 'theme-switch', 'search', 'button-1' ],
					'settings' => [ 'width' => '30%', 'justify-content' => 'flex-end', 'elements-gap' => '16px' ]
				],
			],
			'mobile'  => [
				[
					'elements' => [ 'logo' ],
					'settings' => [ 'width' => '100%', 'justify-content' => 'center' ]
				],
			],
		];
	}
}
add_filter( 'kenta_header_primary_row_default_value', 'kenta_groovy_blog_header_primary_row_elements' );

if ( ! function_exists( 'kenta_groovy_blog_header_primary_row_height' ) ) {
	function kenta_groovy_blog_header_primary_row_height() {
		return '125px';
	}
}
add_filter( 'kenta_header_primary_navbar_row_min_height_default_value', 'kenta_groovy_blog_header_primary_row_height' );

if ( ! function_exists( 'kenta_groovy_blog_header_primary_navbar_row_background' ) ) {
	function kenta_groovy_blog_header_primary_navbar_row_background() {
		return [
			'type'  => 'color',
			'color' => 'rgba(255,255,255,0)',
		];
	}
}
add_filter( 'kenta_header_primary_navbar_row_background_default_value', 'kenta_groovy_blog_header_primary_navbar_row_background' );

if ( ! function_exists( 'kenta_groovy_blog_header_bottom_row_elements' ) ) {
	function kenta_groovy_blog_header_bottom_row_elements() {
		return [
			'desktop' => [
				[
					'elements' => [ 'menu-1' ],
					'settings' => [ 'width' => '100%', 'justify-content' => 'center' ]
				],
			],
			'mobile'  => [
				[
					'elements' => [ 'trigger' ],
					'settings' => [ 'width' => '20%', 'justify-content' => 'flex-start', 'elements-gap' => '16px' ]
				],
				[
					'elements' => [ 'socials' ],
					'settings' => [ 'width' => '60%', 'justify-content' => 'center', 'elements-gap' => '16px' ]
				],
				[
					'elements' => [ 'theme-switch', 'search' ],
					'settings' => [ 'width' => '20%', 'justify-content' => 'flex-end', 'elements-gap' => '16px' ]
				],
			],
		];
	}
}
add_filter( 'kenta_header_bottom_row_default_value', 'kenta_groovy_blog_header_bottom_row_elements' );

if ( ! function_exists( 'kenta_groovy_blog_header_bottom_row_border_top' ) ) {
	function kenta_groovy_blog_header_bottom_row_border_top() {
		return [
			'width' => 1,
			'style' => 'solid',
			'color' => 'var(--kenta-base-300)',
		];
	}
}
add_filter( 'kenta_header_bottom_row_row_border_top_default_value', 'kenta_groovy_blog_header_bottom_row_border_top' );

if ( ! function_exists( 'kenta_groovy_blog_header_bottom_row_shadow' ) ) {
	function kenta_groovy_blog_header_bottom_row_shadow() {
		return [
			'enable'     => 'yes',
			'horizontal' => '0px',
			'vertical'   => '1px',
			'blur'       => '0px',
			'spread'     => '0px',
			'color'      => 'var(--kenta-base-300)',
		];
	}
}
add_filter( 'kenta_header_bottom_row_row_shadow_default_value', 'kenta_groovy_blog_header_bottom_row_shadow' );

// socials element
if ( ! function_exists( 'kenta_groovy_blog_header_socials_icons_color_type' ) ) {
	function kenta_groovy_blog_header_socials_icons_color_type() {
		return 'custom';
	}
}
add_filter( 'kenta_header_el_socials_icons_color_type_default_value', 'kenta_groovy_blog_header_socials_icons_color_type' );

// icon size
if ( ! function_exists( 'kenta_groovy_blog_icon_size' ) ) {
	function kenta_groovy_blog_icon_size() {
		return '18px';
	}
}
add_filter( 'kenta_header_el_socials_icons_size_default_value', 'kenta_groovy_blog_icon_size' );
add_filter( 'kenta_header_el_search_icon_button_size_default_value', 'kenta_groovy_blog_icon_size' );
add_filter( 'kenta_header_el_trigger_icon_button_size_default_value', 'kenta_groovy_blog_icon_size' );
add_filter( 'kenta_header_el_theme_switch_icon_button_size_default_value', 'kenta_groovy_blog_icon_size' );
add_filter( 'kenta_header_el_cart_icon_button_size_default_value', 'kenta_groovy_blog_icon_size' );

// search element
if ( ! function_exists( 'kenta_groovy_blog_search_icon' ) ) {
	function kenta_groovy_blog_search_icon() {
		return [
			'value'   => 'fas fa-q',
			'library' => 'fa-solid'
		];
	}
}
add_filter( 'kenta_header_el_search_icon_button_icon_default_value', 'kenta_groovy_blog_search_icon' );

// button element
if ( ! function_exists( 'kenta_groovy_blog_header_button_icon' ) ) {
	function kenta_groovy_blog_header_button_icon() {
		return [
			'library' => 'fa-solid',
			'value'   => 'fas fa-paper-plane',
		];
	}
}
add_filter( 'kenta_header_el_button_1_arrow_default_value', 'kenta_groovy_blog_header_button_icon' );

if ( ! function_exists( 'kenta_groovy_blog_header_button_text' ) ) {
	function kenta_groovy_blog_header_button_text() {
		return __( 'Subscribe', 'kenta-groovy-blog' );
	}
}
add_filter( 'kenta_header_el_button_1_text_default_value', 'kenta_groovy_blog_header_button_text' );

if ( ! function_exists( 'kenta_groovy_blog_header_button_preset' ) ) {
	function kenta_groovy_blog_header_button_preset() {
		return 'outline';
	}
}
add_filter( 'kenta_header_el_button_1_preset_default_value', 'kenta_groovy_blog_header_button_preset' );

if ( ! function_exists( 'kenta_groovy_blog_global_button_preset' ) ) {
	function kenta_groovy_blog_global_button_preset() {
		return 'accent';
	}
}
add_filter( 'kenta_header_el_button_1_preset_default_value', 'kenta_groovy_blog_global_button_preset' );
add_filter( 'kenta_header_el_button_1_preset_default_value', 'kenta_groovy_blog_global_button_preset' );
add_filter( 'kenta_header_el_button_2_preset_default_value', 'kenta_groovy_blog_global_button_preset' );
add_filter( 'kenta_entry_read_more_preset_default_value', 'kenta_groovy_blog_global_button_preset' );
add_filter( 'kenta_content_buttons_preset_default_value', 'kenta_groovy_blog_global_button_preset' );
add_filter( 'kenta_related_posts_read_more_preset_default_value', 'kenta_groovy_blog_global_button_preset' );

if ( ! function_exists( 'kenta_groovy_blog_button_radius' ) ) {
	function kenta_groovy_blog_button_radius() {
		return [
			'linked' => true,
			'left'   => '999px',
			'right'  => '999px',
			'top'    => '999px',
			'bottom' => '999px',
		];
	}
}
add_filter( 'kenta_header_el_button_1_radius_default_value', 'kenta_groovy_blog_button_radius' );
add_filter( 'kenta_header_el_button_2_radius_default_value', 'kenta_groovy_blog_button_radius' );
add_filter( 'kenta_entry_read_more_radius_default_value', 'kenta_groovy_blog_button_radius' );
add_filter( 'kenta_content_buttons_radius_default_value', 'kenta_groovy_blog_button_radius' );
add_filter( 'kenta_related_posts_read_more_radius_default_value', 'kenta_groovy_blog_button_radius' );

if ( ! function_exists( 'kenta_groovy_blog_button_shadow' ) ) {
	function kenta_groovy_blog_button_shadow() {
		return [
			'enable'     => 'yes',
			'horizontal' => '4px',
			'vertical'   => '4px',
			'blur'       => '0px',
			'spread'     => '0px',
			'color'      => 'var(--kenta-primary-color)',
		];
	}
}
add_filter( 'kenta_header_el_button_1_shadow_active_default_value', 'kenta_groovy_blog_button_shadow' );
add_filter( 'kenta_header_el_button_2_shadow_active_default_value', 'kenta_groovy_blog_button_shadow' );
add_filter( 'kenta_entry_read_more_shadow_active_default_value', 'kenta_groovy_blog_button_shadow' );
add_filter( 'kenta_content_buttons_shadow_active_default_value', 'kenta_groovy_blog_button_shadow' );
add_filter( 'kenta_related_posts_read_more_shadow_active_default_value', 'kenta_groovy_blog_button_shadow' );

if ( ! function_exists( 'kenta_groovy_blog_button_preset_args' ) ) {
	function kenta_groovy_blog_button_preset_args( $args, $id, $preset ) {
		if ( $preset !== 'ghost' ) {
			$args[ $id . 'shadow_active' ] = [
				'enable'     => 'yes',
				'horizontal' => '4px',
				'vertical'   => '4px',
				'blur'       => '0px',
				'spread'     => '0px',
				'color'      => 'var(--kenta-primary-color)',
			];
		}

		return $args;
	}
}
add_filter( 'kenta_header_el_button_1_preset_args', 'kenta_groovy_blog_button_preset_args', 10, 3 );
add_filter( 'kenta_header_el_button_2_preset_args', 'kenta_groovy_blog_button_preset_args', 10, 3 );
add_filter( 'kenta_entry_read_more_preset_args', 'kenta_groovy_blog_button_preset_args', 10, 3 );
add_filter( 'kenta_content_buttons_preset_args', 'kenta_groovy_blog_button_preset_args', 10, 3 );
add_filter( 'kenta_related_posts_read_more_preset_args', 'kenta_groovy_blog_button_preset_args', 10, 3 );

// logo element
if ( ! function_exists( 'kenta_groovy_blog_header_logo_title_typography' ) ) {
	function kenta_groovy_blog_header_logo_title_typography() {
		return [
			'family'        => 'inherit',
			'fontSize'      => '28px',
			'variant'       => '700',
			'lineHeight'    => '1.5',
			'textTransform' => 'uppercase',
		];
	}
}
add_filter( 'kenta_header_el_logo_site_title_typography_default_value', 'kenta_groovy_blog_header_logo_title_typography' );

if ( ! function_exists( 'kenta_groovy_blog_header_logo_tagline_typography' ) ) {
	function kenta_groovy_blog_header_logo_tagline_typography() {
		return [
			'family'        => 'lato',
			'fontSize'      => '16px',
			'variant'       => '400i',
			'lineHeight'    => '1.5',
			'textTransform' => 'capitalize',
		];
	}
}
add_filter( 'kenta_header_el_logo_site_tagline_typography_default_value', 'kenta_groovy_blog_header_logo_tagline_typography' );

if ( ! function_exists( 'kenta_groovy_blog_header_logo_content_alignment' ) ) {
	function kenta_groovy_blog_header_logo_content_alignment() {
		return 'center';
	}
}
add_filter( 'kenta_header_el_logo_content_alignment_default_value', 'kenta_groovy_blog_header_logo_content_alignment' );

add_filter( 'kenta_header_el_logo_enable_site_tagline_default_value', 'kenta_groovy_blog_return_yes' );

//menu element
if ( ! function_exists( 'kenta_groovy_blog_menu_preset' ) ) {
	function kenta_groovy_blog_menu_preset() {
		return 'pill';
	}
}
add_filter( 'kenta_header_el_menu_1_top_level_preset_default_value', 'kenta_groovy_blog_menu_preset' );
if ( ! function_exists( 'kenta_groovy_blog_menu_preset_args' ) ) {
	function kenta_groovy_blog_menu_preset_args( $args, $id, $preset ) {
		if ( $preset === 'pill' ) {
			$args[ $id . '_top_level_background_color' ] = [
				'initial' => 'var(--kenta-transparent)',
				'hover'   => 'var(--kenta-accent-color)',
				'active'  => 'var(--kenta-accent-color)',
			];
		}

		return $args;
	}
}
add_filter( 'kenta_header_el_menu_1_top_level_preset_args', 'kenta_groovy_blog_menu_preset_args', 10, 3 );

if ( ! function_exists( 'kenta_groovy_blog_menu_radius' ) ) {
	function kenta_groovy_blog_menu_radius() {
		return [
			'linked' => true,
			'left'   => '999px',
			'right'  => '999px',
			'top'    => '999px',
			'bottom' => '999px',
		];
	}
}
add_filter( 'kenta_header_el_menu_1_top_level_radius_default_value', 'kenta_groovy_blog_menu_radius' );

if ( ! function_exists( 'kenta_groovy_blog_menu_margin' ) ) {
	function kenta_groovy_blog_menu_margin() {
		return [
			'linked' => false,
			'top'    => '0px',
			'right'  => '6px',
			'bottom' => '0px',
			'left'   => '6px',
		];
	}
}
add_filter( 'kenta_header_el_menu_1_top_level_margin_default_value', 'kenta_groovy_blog_menu_margin' );

//
// Footer bottom row elements
//
if ( ! function_exists( 'kenta_groovy_blog_footer_bottom_row_elements' ) ) {
	function kenta_groovy_blog_footer_bottom_row_elements() {
		return [
			[
				'elements' => [ 'footer-socials', 'copyright' ],
				'settings' => [
					'width'           => [ 'desktop' => '100%', 'tablet' => '100%', 'mobile' => '100%' ],
					'direction'       => 'column',
					'align-items'     => 'center',
					'elements-gap'    => '24px',
					'justify-content' => [
						'desktop' => 'center',
						'tablet'  => 'center',
						'mobile'  => 'center'
					],
				],
			]
		];
	}
}
add_filter( 'kenta_footer_bottom_row_default_value', 'kenta_groovy_blog_footer_bottom_row_elements' );

if ( ! function_exists( 'kenta_groovy_blog_footer_bottom_row_background' ) ) {
	function kenta_groovy_blog_footer_bottom_row_background() {
		return [
			'type'  => 'color',
			'color' => 'var(--kenta-base-color)',
		];
	}
}
add_filter( 'kenta_footer_bottom_row_background_default_value', 'kenta_groovy_blog_footer_bottom_row_background' );

if ( ! function_exists( 'kenta_groovy_blog_footer_bottom_row_border_top' ) ) {
	function kenta_groovy_blog_footer_bottom_row_border_top() {
		return [
			'width' => 1,
			'style' => 'solid',
			'color' => 'var(--kenta-base-300)',
		];
	}
}
add_filter( 'kenta_footer_bottom_row_border_top_default_value', 'kenta_groovy_blog_footer_bottom_row_border_top' );

// footer logo element
if ( ! function_exists( 'kenta_groovy_blog_footer_logo_title_typography' ) ) {
	function kenta_groovy_blog_footer_logo_title_typography() {
		return [
			'family'        => 'inherit',
			'fontSize'      => '22px',
			'variant'       => '700',
			'lineHeight'    => '1.5',
			'textTransform' => 'uppercase',
		];
	}
}
add_filter( 'kenta_footer_el_logo_site_title_typography_default_value', 'kenta_groovy_blog_footer_logo_title_typography' );

if ( ! function_exists( 'kenta_groovy_blog_footer_logo_tagline_typography' ) ) {
	function kenta_groovy_blog_footer_logo_tagline_typography() {
		return [
			'family'        => 'lato',
			'fontSize'      => '16px',
			'variant'       => '400i',
			'lineHeight'    => '1.5',
			'textTransform' => 'capitalize',
		];
	}
}
add_filter( 'kenta_footer_el_logo_site_tagline_typography_default_value', 'kenta_groovy_blog_footer_logo_tagline_typography' );

if ( ! function_exists( 'kenta_groovy_blog_footer_logo_content_alignment' ) ) {
	function kenta_groovy_blog_footer_logo_content_alignment() {
		return 'center';
	}
}
add_filter( 'kenta_footer_el_logo_content_alignment_default_value', 'kenta_groovy_blog_footer_logo_content_alignment' );

//
// Preloader
//
if ( ! function_exists( 'kenta_groovy_blog_preloader_preset' ) ) {
	function kenta_groovy_blog_preloader_preset() {
		return 'preset-4';
	}
}
add_filter( 'kenta_preloader_preset_default_value', 'kenta_groovy_blog_preloader_preset' );

if ( ! function_exists( 'kenta_groovy_blog_preloader_colors' ) ) {
	function kenta_groovy_blog_preloader_colors() {
		return [
			'background' => '#000000',
			'accent'     => '#ffffff',
			'primary'    => 'var(--kenta-primary-accent)',
		];
	}
}
add_filter( 'kenta_preloader_colors_default_value', 'kenta_groovy_blog_preloader_colors' );
// disable preloader by default
add_filter( 'kenta_global_preloader_default_value', 'kenta_groovy_blog_return_no' );

//
// Author bio
//
if ( ! function_exists( 'kenta_groovy_blog_author_bio_background' ) ) {
	function kenta_groovy_blog_author_bio_background() {
		return [
			'type'  => 'color',
			'color' => 'var(--kenta-base-color)'
		];
	}
}
add_filter( 'kenta_post_author_bio_background_default_value', 'kenta_groovy_blog_author_bio_background' );

//
// Background
//
if ( ! function_exists( 'kenta_groovy_blog_body_background' ) ) {
	function kenta_groovy_blog_body_background() {
		return [
			'type'  => 'color',
			'color' => 'var(--kenta-base-color)'
		];
	}
}
add_filter( 'kenta_site_body_background_default_value', 'kenta_groovy_blog_body_background' );

if ( ! function_exists( 'kenta_groovy_blog_site_wrap_shadow' ) ) {
	function kenta_groovy_blog_site_wrap_shadow() {
		return [
			'enable'     => 'yes',
			'horizontal' => '0px',
			'vertical'   => '0px',
			'blur'       => '0px',
			'spread'     => '1px',
			'color'      => 'var(--kenta-base-300)',
		];
	}
}
add_filter( 'kenta_site_wrap_shadow_default_value', 'kenta_groovy_blog_site_wrap_shadow' );

add_filter( 'kenta_site_background_enable_particles_default_value', 'kenta_groovy_blog_return_yes' );

if ( ! function_exists( 'kenta_groovy_blog_background_particles_scope' ) ) {
	function kenta_groovy_blog_background_particles_scope() {
		return [
			'site-body'    => 'no',
			'site-content' => 'yes',
		];
	}
}
add_filter( 'kenta_site_background_particles_scope_default_value', 'kenta_groovy_blog_background_particles_scope' );

if ( ! function_exists( 'kenta_groovy_blog_background_particle_color' ) ) {
	function kenta_groovy_blog_background_particle_color() {
		return [
			'particle' => 'var(--kenta-accent-color)',
			'line'     => 'var(--kenta-accent-color)',
		];
	}
}
add_filter( 'kenta_site_background_particle_color_default_value', 'kenta_groovy_blog_background_particle_color' );

//
// Dynamic css
//
if ( ! function_exists( 'kenta_groovy_blog_dynamic_css' ) ) {
	function kenta_groovy_blog_dynamic_css( $css ) {
		$css[':root'] = array_merge(
			$css[':root'] ?? [],
			[
				'--kenta-form-control-radius' => '18px'
			]
		);
		$css['.card'] = array_merge(
			$css['.card'] ?? [],
			[ '--kenta-card-radius' => '18px' ]
		);

		return $css;
	}
}
add_filter( 'kenta_filter_dynamic_css', 'kenta_groovy_blog_dynamic_css' );
