<?php
/**
 * Astra Updates
 *
 * Functions for updating data, used by the background updater.
 *
 * @package Astra
 * @version 2.1.3
 */

defined( 'ABSPATH' ) || exit;

/**
 * Check if we need to load icons as font or SVG.
 *
 * @since 3.3.0
 * @return void
 */
function astra_icons_svg_compatibility() {

	$theme_options = get_option( 'astra-settings' );

	if ( ! isset( $theme_options['can-update-astra-icons-svg'] ) ) {
		// Set a flag to check if we need to add icons as SVG.
		$theme_options['can-update-astra-icons-svg'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Header Footer builder - Migration compatibility.
 *
 * @since 3.0.0
 *
 * @return void
 */
function astra_header_builder_compatibility() {
	$theme_options = get_option( 'astra-settings', array() );

	// Set flag to not load button specific CSS.
	if ( ! isset( $theme_options['is-header-footer-builder'] ) ) {
		$theme_options['is-header-footer-builder'] = false;
		update_option( 'astra-settings', $theme_options );
	}
	if ( ! isset( $theme_options['header-footer-builder-notice'] ) ) {
		$theme_options['header-footer-builder-notice'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Clears assets cache and regenerates new assets files.
 *
 * @since 3.0.1
 *
 * @return void
 */
function astra_clear_assets_cache() {
	if ( is_callable( 'Astra_Minify::refresh_assets' ) ) {
		Astra_Minify::refresh_assets();
	}
}

/**
 * Gutenberg pattern compatibility changes.
 *
 * @since 3.3.0
 *
 * @return void
 */
function astra_gutenberg_pattern_compatibility() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['guntenberg-button-pattern-compat-css'] ) ) {
		$theme_options['guntenberg-button-pattern-compat-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to provide backward compatibility of float based CSS for existing users.
 *
 * @since 3.3.0
 * @return void.
 */
function astra_check_flex_based_css() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['is-flex-based-css'] ) ) {
		$theme_options['is-flex-based-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Update the Cart Style, Icon color & Border radius if None style is selected.
 *
 * @since 3.4.0
 * @return void.
 */
function astra_update_cart_style() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( isset( $theme_options['woo-header-cart-icon-style'] ) && 'none' === $theme_options['woo-header-cart-icon-style'] ) {
		$theme_options['woo-header-cart-icon-style']  = 'outline';
		$theme_options['header-woo-cart-icon-color']  = '';
		$theme_options['woo-header-cart-icon-color']  = '';
		$theme_options['woo-header-cart-icon-radius'] = '';
	}

	if ( isset( $theme_options['edd-header-cart-icon-style'] ) && 'none' === $theme_options['edd-header-cart-icon-style'] ) {
		$theme_options['edd-header-cart-icon-style']  = 'outline';
		$theme_options['edd-header-cart-icon-color']  = '';
		$theme_options['edd-header-cart-icon-radius'] = '';
	}

	update_option( 'astra-settings', $theme_options );
}

/**
 * Update existing 'Grid Column Layout' option in responsive way in Related Posts.
 * Till this update 3.5.0 we have 'Grid Column Layout' only for singular option, but now we are improving it as responsive.
 *
 * @since 3.5.0
 * @return void.
 */
function astra_update_related_posts_grid_layout() {

	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['related-posts-grid-responsive'] ) && isset( $theme_options['related-posts-grid'] ) ) {

		/**
		 * Managed here switch case to reduce further conditions in dynamic-css to get CSS value based on grid-template-columns. Because there are following CSS props used.
		 *
		 * '1' = grid-template-columns: 1fr;
		 * '2' = grid-template-columns: repeat(2,1fr);
		 * '3' = grid-template-columns: repeat(3,1fr);
		 * '4' = grid-template-columns: repeat(4,1fr);
		 *
		 * And we already have Astra_Builder_Helper::$grid_size_mapping (used for footer layouts) for getting CSS values based on grid layouts. So migrating old value of grid here to new grid value.
		 */
		switch ( $theme_options['related-posts-grid'] ) {
			case '1':
				$grid_layout = 'full';
				break;

			case '2':
				$grid_layout = '2-equal';
				break;

			case '3':
				$grid_layout = '3-equal';
				break;

			case '4':
				$grid_layout = '4-equal';
				break;
		}

		$theme_options['related-posts-grid-responsive'] = array(
			'desktop' => $grid_layout,
			'tablet'  => $grid_layout,
			'mobile'  => 'full',
		);

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Migrate Site Title & Site Tagline options to new responsive array.
 *
 * @since 3.5.0
 *
 * @return void
 */
function astra_site_title_tagline_responsive_control_migration() {

	$theme_options = get_option( 'astra-settings', array() );

	if ( false === get_option( 'display-site-title-responsive', false ) && isset( $theme_options['display-site-title'] ) ) {
		$theme_options['display-site-title-responsive']['desktop'] = $theme_options['display-site-title'];
		$theme_options['display-site-title-responsive']['tablet']  = $theme_options['display-site-title'];
		$theme_options['display-site-title-responsive']['mobile']  = $theme_options['display-site-title'];
	}

	if ( false === get_option( 'display-site-tagline-responsive', false ) && isset( $theme_options['display-site-tagline'] ) ) {
		$theme_options['display-site-tagline-responsive']['desktop'] = $theme_options['display-site-tagline'];
		$theme_options['display-site-tagline-responsive']['tablet']  = $theme_options['display-site-tagline'];
		$theme_options['display-site-tagline-responsive']['mobile']  = $theme_options['display-site-tagline'];
	}

	update_option( 'astra-settings', $theme_options );
}

/**
 * Do not apply new font-weight heading support CSS in editor/frontend directly.
 *
 * 1. Adding Font-weight support to widget titles.
 * 2. Customizer font CSS not supporting in editor.
 *
 * @since 3.6.0
 *
 * @return void
 */
function astra_headings_font_support() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['can-support-widget-and-editor-fonts'] ) ) {
		$theme_options['can-support-widget-and-editor-fonts'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 3.6.0
 * @return void.
 */
function astra_remove_logo_max_width() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['can-remove-logo-max-width-css'] ) ) {
		$theme_options['can-remove-logo-max-width-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to maintain backward compatibility for existing users for Transparent Header border bottom default value i.e from '' to 0.
 *
 * @since 3.6.0
 * @return void.
 */
function astra_transparent_header_default_value() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['transparent-header-default-border'] ) ) {
		$theme_options['transparent-header-default-border'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Clear Astra + Astra Pro assets cache.
 *
 * @since 3.6.1
 * @return void.
 */
function astra_clear_all_assets_cache() {
	if ( ! class_exists( 'Astra_Cache_Base' ) ) {
		return;
	}
	// Clear Astra theme asset cache.
	$astra_cache_base_instance = new Astra_Cache_Base( 'astra' );
	$astra_cache_base_instance->refresh_assets( 'astra' );

	// Clear Astra Addon's static and dynamic CSS asset cache.
	astra_clear_assets_cache();
	$astra_addon_cache_base_instance = new Astra_Cache_Base( 'astra-addon' );
	$astra_addon_cache_base_instance->refresh_assets( 'astra-addon' );
}

/**
 * Set flag for updated default values for buttons & add GB Buttons padding support.
 *
 * @since 3.6.3
 * @return void
 */
function astra_button_default_values_updated() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['btn-default-padding-updated'] ) ) {
		$theme_options['btn-default-padding-updated'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag for old users, to not directly apply underline to content links.
 *
 * @since 3.6.4
 * @return void
 */
function astra_update_underline_link_setting() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['underline-content-links'] ) ) {
		$theme_options['underline-content-links'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Add compatibility support for WP-5.8. as some of settings & blocks already their in WP-5.7 versions, that's why added backward here.
 *
 * @since 3.6.5
 * @return void
 */
function astra_support_block_editor() {
	$theme_options = get_option( 'astra-settings' );

	// Set flag on existing user's site to not reflect changes directly.
	if ( ! isset( $theme_options['support-block-editor'] ) ) {
		$theme_options['support-block-editor'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to maintain backward compatibility for existing users.
 * Fixing the case where footer widget's right margin space not working.
 *
 * @since 3.6.7
 * @return void
 */
function astra_fix_footer_widget_right_margin_case() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['support-footer-widget-right-margin'] ) ) {
		$theme_options['support-footer-widget-right-margin'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 3.6.7
 * @return void
 */
function astra_remove_elementor_toc_margin() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['remove-elementor-toc-margin-css'] ) ) {
		$theme_options['remove-elementor-toc-margin-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 * Use: Setting flag for removing widget specific design options when WordPress 5.8 & above activated on site.
 *
 * @since 3.6.8
 * @return void
 */
function astra_set_removal_widget_design_options_flag() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['remove-widget-design-options'] ) ) {
		$theme_options['remove-widget-design-options'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Apply zero font size for new users.
 *
 * @since 3.6.9
 * @return void
 */
function astra_zero_font_size_comp() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['astra-zero-font-size-case-css'] ) ) {
		$theme_options['astra-zero-font-size-case-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/** Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 3.6.9
 * @return void
 */
function astra_unset_builder_elements_underline() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['unset-builder-elements-underline'] ) ) {
		$theme_options['unset-builder-elements-underline'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Migrating Builder > Account > transparent resonsive menu color options to single color options.
 * Because we do not show menu on resonsive devices, whereas we trigger login link on responsive devices instead of showing menu.
 *
 * @since 3.6.9
 *
 * @return void
 */
function astra_remove_responsive_account_menu_colors_support() {

	$theme_options = get_option( 'astra-settings', array() );

	$account_menu_colors = array(
		'transparent-account-menu-color',                // Menu color.
		'transparent-account-menu-bg-obj',               // Menu background color.
		'transparent-account-menu-h-color',              // Menu hover color.
		'transparent-account-menu-h-bg-color',           // Menu background hover color.
		'transparent-account-menu-a-color',              // Menu active color.
		'transparent-account-menu-a-bg-color',           // Menu background active color.
	);

	foreach ( $account_menu_colors as $color_option ) {
		if ( ! isset( $theme_options[ $color_option ] ) && isset( $theme_options[ $color_option . '-responsive' ]['desktop'] ) ) {
			$theme_options[ $color_option ] = $theme_options[ $color_option . '-responsive' ]['desktop'];
		}
	}

	update_option( 'astra-settings', $theme_options );
}

/**
 * Link default color compatibility.
 *
 * @since 3.7.0
 * @return void
 */
function astra_global_color_compatibility() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['support-global-color-format'] ) ) {
		$theme_options['support-global-color-format'] = false;
	}

	// Set Footer copyright text color for existing users to #3a3a3a.
	if ( ! isset( $theme_options['footer-copyright-color'] ) ) {
		$theme_options['footer-copyright-color'] = '#3a3a3a';
	}

	update_option( 'astra-settings', $theme_options );
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 3.7.4
 * @return void
 */
function astra_improve_gutenberg_editor_ui() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['improve-gb-editor-ui'] ) ) {
		$theme_options['improve-gb-editor-ui'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * Starting supporting content-background color for Full Width Contained & Full Width Stretched layouts.
 *
 * @since 3.7.8
 * @return void
 */
function astra_fullwidth_layouts_apply_content_background() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['apply-content-background-fullwidth-layouts'] ) ) {
		$theme_options['apply-content-background-fullwidth-layouts'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Sets the default breadcrumb separator selector value if the current user is an exsisting user
 *
 * @since 3.7.8
 * @return void
 */
function astra_set_default_breadcrumb_separator_option() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['breadcrumb-separator-selector'] ) ) {
		$theme_options['breadcrumb-separator-selector'] = 'unicode';
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * Backward flag purpose - To initiate modern & updated UI of block editor & frontend.
 *
 * @since 3.8.0
 * @return void
 */
function astra_apply_modern_block_editor_ui() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['wp-blocks-ui'] ) && ! version_compare( $theme_options['theme-auto-version'], '3.8.0', '==' ) ) {
		$theme_options['blocks-legacy-setup'] = true;
		$theme_options['wp-blocks-ui']        = 'legacy';
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * Backward flag purpose - To keep structure defaults updation by filter.
 *
 * @since 3.8.3
 * @return void
 */
function astra_update_customizer_layout_defaults() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['customizer-default-layout-update'] ) ) {
		$theme_options['customizer-default-layout-update'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * Backward flag purpose - To initiate maintain modern, updated v2 experience of block editor & frontend.
 *
 * @since 3.8.3
 * @return void
 */
function astra_apply_modern_block_editor_v2_ui() {
	$theme_options  = get_option( 'astra-settings', array() );
	$option_updated = false;
	if ( ! isset( $theme_options['wp-blocks-v2-ui'] ) ) {
		$theme_options['wp-blocks-v2-ui'] = false;
		$option_updated                   = true;
	}
	if ( ! isset( $theme_options['wp-blocks-ui'] ) ) {
		$theme_options['wp-blocks-ui'] = 'custom';
		$option_updated                = true;
	}
	if ( $option_updated ) {
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Display Cart Total and Title compatibility.
 *
 * @since 3.9.0
 * @return void
 */
function astra_display_cart_total_title_compatibility() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['woo-header-cart-label-display'] ) ) {
		// Set the Display Cart Label toggle values with shortcodes.
		$cart_total_status = isset( $theme_options['woo-header-cart-total-display'] ) ? $theme_options['woo-header-cart-total-display'] : true;
		$cart_label_status = isset( $theme_options['woo-header-cart-title-display'] ) ? $theme_options['woo-header-cart-title-display'] : true;

		if ( $cart_total_status && $cart_label_status ) {
			$theme_options['woo-header-cart-label-display'] = __( 'Cart', 'astra' ) . '/{cart_total_currency_symbol}';
		} elseif ( $cart_total_status ) {
			$theme_options['woo-header-cart-label-display'] = '{cart_total_currency_symbol}';
		} elseif ( $cart_label_status ) {
			$theme_options['woo-header-cart-label-display'] = __( 'Cart', 'astra' );
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * If old user then it keeps then default cart icon.
 *
 * @since 3.9.0
 * @return void
 */
function astra_update_woocommerce_cart_icons() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['astra-woocommerce-cart-icons-flag'] ) ) {
		$theme_options['astra-woocommerce-cart-icons-flag'] = false;
	}
}

/**
 * Set brder color to blank for old users for new users 'default' will take over.
 *
 * @since 3.9.0
 * @return void
 */
function astra_legacy_customizer_maintenance() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['border-color'] ) ) {
		$theme_options['border-color'] = '#dddddd';
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Enable single product breadcrumb to maintain backward compatibility for existing users.
 *
 * @since 3.9.0
 * @return void
 */
function astra_update_single_product_breadcrumb() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( isset( $theme_options['single-product-breadcrumb-disable'] ) ) {
		$theme_options['single-product-breadcrumb-disable'] = ( true === $theme_options['single-product-breadcrumb-disable'] ) ? false : true;
	} else {
		$theme_options['single-product-breadcrumb-disable'] = true;
	}
	update_option( 'astra-settings', $theme_options );
}

/**
 * Restrict direct changes on users end so make it filterable.
 *
 * @since 3.9.0
 * @return void
 */
function astra_apply_modern_ecommerce_setup() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['modern-ecommerce-setup'] ) ) {
		$theme_options['modern-ecommerce-setup'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Migrate old user data to new responsive format layout for shop's summary box content alignment.
 *
 * @since 3.9.0
 * @return void
 */
function astra_responsive_shop_content_alignment() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['shop-product-align-responsive'] ) && isset( $theme_options['shop-product-align'] ) ) {
		$theme_options['shop-product-align-responsive'] = array(
			'desktop' => $theme_options['shop-product-align'],
			'tablet'  => $theme_options['shop-product-align'],
			'mobile'  => $theme_options['shop-product-align'],
		);
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Change default layout to standard for old users.
 *
 * @since 3.9.2
 * @return void
 */
function astra_shop_style_design_layout() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['woo-shop-style-flag'] ) ) {
		$theme_options['woo-shop-style-flag'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Apply css for show password icon on woocommerce account page.
 *
 * @since 3.9.2
 * @return void
 */
function astra_apply_woocommerce_show_password_icon_css() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['woo-show-password-icon'] ) ) {
		$theme_options['woo-show-password-icon'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 3.9.4
 *
 * @since 3.9.4
 * @return void
 */
function astra_theme_background_updater_3_9_4() {
	$theme_options = get_option( 'astra-settings', array() );

	// Check if user is a old global sidebar user.
	if ( ! isset( $theme_options['astra-old-global-sidebar-default'] ) ) {
		$theme_options['astra-old-global-sidebar-default'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	// Slide in cart width responsive control backwards compatibility.
	if ( isset( $theme_options['woo-desktop-cart-flyout-width'] ) && ! isset( $theme_options['woo-slide-in-cart-width'] ) ) {
		$theme_options['woo-slide-in-cart-width'] = array(
			'desktop'      => $theme_options['woo-desktop-cart-flyout-width'],
			'tablet'       => '',
			'mobile'       => '',
			'desktop-unit' => 'px',
			'tablet-unit'  => 'px',
			'mobile-unit'  => 'px',
		);
		update_option( 'astra-settings', $theme_options );
	}

	// Astra Spectra Gutenberg Compatibility CSS.
	if ( ! isset( $theme_options['spectra-gutenberg-compat-css'] ) ) {
		$theme_options['spectra-gutenberg-compat-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * 4.0.0 backward handling part.
 *
 * 1. Migrate existing setting & do required onboarding for new admin dashboard v4.0.0 app.
 * 2. Migrating Post Structure & Meta options in title area meta parts.
 *
 * @since 4.0.0
 * @return void
 */
function astra_theme_background_updater_4_0_0() {
	// Dynamic customizer migration starts here.
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['dynamic-blog-layouts'] ) && ! isset( $theme_options['theme-dynamic-customizer-support'] ) ) {
		$theme_options['dynamic-blog-layouts']             = false;
		$theme_options['theme-dynamic-customizer-support'] = true;

		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();

		// Archive summary box compatibility.
		$archive_title_font_size = array(
			'desktop'      => isset( $theme_options['font-size-archive-summary-title']['desktop'] ) ? $theme_options['font-size-archive-summary-title']['desktop'] : 40,
			'tablet'       => isset( $theme_options['font-size-archive-summary-title']['tablet'] ) ? $theme_options['font-size-archive-summary-title']['tablet'] : '',
			'mobile'       => isset( $theme_options['font-size-archive-summary-title']['mobile'] ) ? $theme_options['font-size-archive-summary-title']['mobile'] : '',
			'desktop-unit' => isset( $theme_options['font-size-archive-summary-title']['desktop-unit'] ) ? $theme_options['font-size-archive-summary-title']['desktop-unit'] : 'px',
			'tablet-unit'  => isset( $theme_options['font-size-archive-summary-title']['tablet-unit'] ) ? $theme_options['font-size-archive-summary-title']['tablet-unit'] : 'px',
			'mobile-unit'  => isset( $theme_options['font-size-archive-summary-title']['mobile-unit'] ) ? $theme_options['font-size-archive-summary-title']['mobile-unit'] : 'px',
		);
		$single_title_font_size  = array(
			'desktop'      => isset( $theme_options['font-size-entry-title']['desktop'] ) ? $theme_options['font-size-entry-title']['desktop'] : '',
			'tablet'       => isset( $theme_options['font-size-entry-title']['tablet'] ) ? $theme_options['font-size-entry-title']['tablet'] : '',
			'mobile'       => isset( $theme_options['font-size-entry-title']['mobile'] ) ? $theme_options['font-size-entry-title']['mobile'] : '',
			'desktop-unit' => isset( $theme_options['font-size-entry-title']['desktop-unit'] ) ? $theme_options['font-size-entry-title']['desktop-unit'] : 'px',
			'tablet-unit'  => isset( $theme_options['font-size-entry-title']['tablet-unit'] ) ? $theme_options['font-size-entry-title']['tablet-unit'] : 'px',
			'mobile-unit'  => isset( $theme_options['font-size-entry-title']['mobile-unit'] ) ? $theme_options['font-size-entry-title']['mobile-unit'] : 'px',
		);
		$archive_summary_box_bg  = array(
			'desktop' => array(
				'background-color'      => ! empty( $theme_options['archive-summary-box-bg-color'] ) ? $theme_options['archive-summary-box-bg-color'] : '',
				'background-image'      => '',
				'background-repeat'     => 'repeat',
				'background-position'   => 'center center',
				'background-size'       => 'auto',
				'background-attachment' => 'scroll',
				'background-type'       => '',
				'background-media'      => '',
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
			),
		);
		// Single post structure.
		foreach ( $post_types as $index => $post_type ) {
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_post_structure = isset( $theme_options['blog-single-post-structure'] ) ? $theme_options['blog-single-post-structure'] : array( 'single-image', 'single-title-meta' );
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$migrated_post_structure = array();

			if ( ! empty( $single_post_structure ) ) {
				/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				foreach ( $single_post_structure as $key ) {
					/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					if ( 'single-title-meta' === $key ) {
						$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title';
						if ( 'post' === $post_type ) {
							$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-meta';
						}
					}
					if ( 'single-image' === $key ) {
						$migrated_post_structure[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-image';
					}
				}

				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-structure' ] = $migrated_post_structure;
			}

			// Single post meta.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_post_meta = isset( $theme_options['blog-single-meta'] ) ? $theme_options['blog-single-meta'] : array( 'comments', 'category', 'author' );
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$migrated_post_metadata = array();

			if ( ! empty( $single_post_meta ) ) {
				$tax_counter = 0;
				$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy';
				/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
				foreach ( $single_post_meta as $key ) {
					/** @psalm-suppress PossiblyInvalidIterator */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
					switch ( $key ) {
						case 'author':
							$migrated_post_metadata[] = 'author';
							break;
						case 'date':
							$migrated_post_metadata[] = 'date';
							break;
						case 'comments':
							$migrated_post_metadata[] = 'comments';
							break;
						case 'category':
							if ( 'post' === $post_type ) {
								$migrated_post_metadata[]   = $tax_slug;
								$theme_options[ $tax_slug ] = 'category';

								$tax_counter = ++$tax_counter;
								$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy-' . $tax_counter;
							}
							break;
						case 'tag':
							if ( 'post' === $post_type ) {
								$migrated_post_metadata[]   = $tax_slug;
								$theme_options[ $tax_slug ] = 'post_tag';

								$tax_counter = ++$tax_counter;
								$tax_slug    = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-taxonomy-' . $tax_counter;
							}
							break;
						default:
							break;
					}
				}

				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-metadata' ] = $migrated_post_metadata;
			}

			// Archive layout compatibilities.
			$archive_banner_layout = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true; // Setting WooCommerce archive option disabled as WC already added their header content on archive.
			$theme_options[ 'ast-archive-' . esc_attr( $post_type ) . '-title' ] = $archive_banner_layout;

			// Single layout compatibilities.
			$single_banner_layout = ( class_exists( 'WooCommerce' ) && 'product' === $post_type ) ? false : true; // Setting WC single option disabled as there is no any header set from default WooCommerce.
			$theme_options[ 'ast-single-' . esc_attr( $post_type ) . '-title' ] = $single_banner_layout;

			// BG color support.
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-image-type' ] = ! empty( $theme_options['archive-summary-box-bg-color'] ) ? 'custom' : 'none';
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-custom-bg' ]  = $archive_summary_box_bg;

			// Archive title font support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-family' ] = ! empty( $theme_options['font-family-archive-summary-title'] ) ? $theme_options['font-family-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-size' ] = $archive_title_font_size;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-weight' ] = ! empty( $theme_options['font-weight-archive-summary-title'] ) ? $theme_options['font-weight-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$archive_dynamic_line_height = ! empty( $theme_options['line-height-archive-summary-title'] ) ? $theme_options['line-height-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$archive_dynamic_text_transform = ! empty( $theme_options['text-transform-archive-summary-title'] ) ? $theme_options['text-transform-archive-summary-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-title-font-extras' ] = array(
				'line-height'         => $archive_dynamic_line_height,
				'line-height-unit'    => 'em',
				'letter-spacing'      => '',
				'letter-spacing-unit' => 'px',
				'text-transform'      => $archive_dynamic_text_transform,
				'text-decoration'     => '',
			);

			// Archive title colors support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-title-color' ] = ! empty( $theme_options['archive-summary-box-title-color'] ) ? $theme_options['archive-summary-box-title-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-text-color' ] = ! empty( $theme_options['archive-summary-box-text-color'] ) ? $theme_options['archive-summary-box-text-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// Single title colors support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-banner-title-color' ] = ! empty( $theme_options['entry-title-color'] ) ? $theme_options['entry-title-color'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			// Single title font support.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-family' ] = ! empty( $theme_options['font-family-entry-title'] ) ? $theme_options['font-family-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-size' ] = $single_title_font_size;
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-weight' ] = ! empty( $theme_options['font-weight-entry-title'] ) ? $theme_options['font-weight-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_dynamic_line_height = ! empty( $theme_options['line-height-entry-title'] ) ? $theme_options['line-height-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			$single_dynamic_text_transform = ! empty( $theme_options['text-transform-entry-title'] ) ? $theme_options['text-transform-entry-title'] : '';
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort

			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-extras' ] = array(
				'line-height'         => $single_dynamic_line_height,
				'line-height-unit'    => 'em',
				'letter-spacing'      => '',
				'letter-spacing-unit' => 'px',
				'text-transform'      => $single_dynamic_text_transform,
				'text-decoration'     => '',
			);
		}

		// Set page specific structure, as page only has featured image at top & title beneath to it, hardcoded writing it here.
		$theme_options['ast-dynamic-single-page-structure'] = array( 'ast-dynamic-single-page-image', 'ast-dynamic-single-page-title' );

		// EDD content layout & sidebar layout migration in new dynamic option.
		$theme_options['archive-download-content-layout'] = isset( $theme_options['edd-archive-product-layout'] ) ? $theme_options['edd-archive-product-layout'] : 'default';
		$theme_options['archive-download-sidebar-layout'] = isset( $theme_options['edd-sidebar-layout'] ) ? $theme_options['edd-sidebar-layout'] : 'no-sidebar';
		$theme_options['single-download-content-layout']  = isset( $theme_options['edd-single-product-layout'] ) ? $theme_options['edd-single-product-layout'] : 'default';
		$theme_options['single-download-sidebar-layout']  = isset( $theme_options['edd-single-product-sidebar-layout'] ) ? $theme_options['edd-single-product-sidebar-layout'] : 'default';

		update_option( 'astra-settings', $theme_options );
	}

	// Admin backward handling starts here.
	$admin_dashboard_settings = get_option( 'astra_admin_settings', array() );
	if ( ! isset( $admin_dashboard_settings['theme-setup-admin-migrated'] ) ) {

		if ( ! isset( $admin_dashboard_settings['self_hosted_gfonts'] ) ) {
			$admin_dashboard_settings['self_hosted_gfonts'] = isset( $theme_options['load-google-fonts-locally'] ) ? $theme_options['load-google-fonts-locally'] : false;
		}
		if ( ! isset( $admin_dashboard_settings['preload_local_fonts'] ) ) {
			$admin_dashboard_settings['preload_local_fonts'] = isset( $theme_options['preload-local-fonts'] ) ? $theme_options['preload-local-fonts'] : false;
		}

		// Consider admin part from theme side migrated.
		$admin_dashboard_settings['theme-setup-admin-migrated'] = true;
		update_option( 'astra_admin_settings', $admin_dashboard_settings );
	}

	// Check if existing user and disable smooth scroll-to-id.
	if ( ! isset( $theme_options['enable-scroll-to-id'] ) ) {
		$theme_options['enable-scroll-to-id'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	// Check if existing user and disable scroll to top if disabled from pro addons list.
	$scroll_to_top_visibility = false;
	/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
	if ( defined( 'ASTRA_EXT_VER' ) && Astra_Ext_Extension::is_active( 'scroll-to-top' ) ) {
		/** @psalm-suppress UndefinedClass */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
		$scroll_to_top_visibility = true;
	}
	if ( ! isset( $theme_options['scroll-to-top-enable'] ) ) {
		$theme_options['scroll-to-top-enable'] = $scroll_to_top_visibility;
		update_option( 'astra-settings', $theme_options );
	}

	// Default colors & typography flag.
	if ( ! isset( $theme_options['update-default-color-typo'] ) ) {
		$theme_options['update-default-color-typo'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	// Block editor experience improvements compatibility flag.
	if ( ! isset( $theme_options['v4-block-editor-compat'] ) ) {
		$theme_options['v4-block-editor-compat'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * 4.0.2 backward handling part.
 *
 * 1. Read Time option backwards handling for old users.
 *
 * @since 4.0.2
 * @return void
 */
function astra_theme_background_updater_4_0_2() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-0-2-update-migration'] ) && isset( $theme_options['blog-single-meta'] ) && in_array( 'read-time', $theme_options['blog-single-meta'] ) ) {
		if ( isset( $theme_options['ast-dynamic-single-post-metadata'] ) && ! in_array( 'read-time', $theme_options['ast-dynamic-single-post-metadata'] ) ) {
			$theme_options['ast-dynamic-single-post-metadata'][] = 'read-time';
			$theme_options['v4-0-2-update-migration']            = true;
			update_option( 'astra-settings', $theme_options );
		}
	}
}

/**
 * Handle backward compatibility on version 4.1.0
 *
 * @since 4.1.0
 * @return void
 */
function astra_theme_background_updater_4_1_0() {

	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['v4-1-0-update-migration'] ) ) {
		$theme_options['v4-1-0-update-migration'] = true;
		$current_payment_list                     = array();
		$old_payment_list                         = isset( $theme_options['single-product-payment-list']['items'] ) ? $theme_options['single-product-payment-list']['items'] : array();

		$visa_payment       = isset( $theme_options['single-product-payment-visa'] ) ? $theme_options['single-product-payment-visa'] : '';
		$mastercard_payment = isset( $theme_options['single-product-payment-mastercard'] ) ? $theme_options['single-product-payment-mastercard'] : '';
		$discover_payment   = isset( $theme_options['single-product-payment-discover'] ) ? $theme_options['single-product-payment-discover'] : '';
		$paypal_payment     = isset( $theme_options['single-product-payment-paypal'] ) ? $theme_options['single-product-payment-paypal'] : '';
		$apple_pay_payment  = isset( $theme_options['single-product-payment-apple-pay'] ) ? $theme_options['single-product-payment-apple-pay'] : '';

		false !== $visa_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-100',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-visa',
				'image'   => '',
				'label'   => __( 'Visa', 'astra' ),
			)
		) : '';

		false !== $mastercard_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-101',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-mastercard',
				'image'   => '',
				'label'   => __( 'Mastercard', 'astra' ),
			)
		) : '';

		false !== $mastercard_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-102',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-amex',
				'image'   => '',
				'label'   => __( 'Amex', 'astra' ),
			)
		) : '';

		false !== $discover_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-103',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-discover',
				'image'   => '',
				'label'   => __( 'Discover', 'astra' ),
			)
		) : '';

		$paypal_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-104',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-paypal',
				'image'   => '',
				'label'   => __( 'Paypal', 'astra' ),
			)
		) : '';

		$apple_pay_payment ? array_push(
			$current_payment_list,
			array(
				'id'      => 'item-105',
				'enabled' => true,
				'source'  => 'icon',
				'icon'    => 'cc-apple-pay',
				'image'   => '',
				'label'   => __( 'Apple Pay', 'astra' ),
			)
		) : '';

		if ( $current_payment_list ) {
			$theme_options['single-product-payment-list'] =
			array(
				'items' =>
					array_merge(
						$current_payment_list,
						$old_payment_list
					),
			);

			update_option( 'astra-settings', $theme_options );
		}

		if ( ! isset( $theme_options['woo_support_global_settings'] ) ) {
			$theme_options['woo_support_global_settings'] = true;
			update_option( 'astra-settings', $theme_options );
		}

		if ( isset( $theme_options['theme-dynamic-customizer-support'] ) ) {
			$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
			foreach ( $post_types as $index => $post_type ) {
				$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-title-font-extras' ]['text-transform'] = '';
			}
			update_option( 'astra-settings', $theme_options );
		}
	}
}

/**
 * 4.1.4 backward handling cases.
 *
 * 1. Migrating users to combined color overlay option to new dedicated overlay options.
 *
 * @since 4.1.4
 * @return void
 */
function astra_theme_background_updater_4_1_4() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-1-4-update-migration'] ) ) {
		$ast_bg_control_options = array(
			'off-canvas-background',
			'footer-adv-bg-obj',
			'footer-bg-obj',
		);

		foreach ( $ast_bg_control_options as $key => $bg_option ) {
			if ( isset( $theme_options[ $bg_option ] ) && ! isset( $theme_options[ $bg_option ]['overlay-type'] ) ) {
				$bg_type = isset( $theme_options[ $bg_option ]['background-type'] ) ? $theme_options[ $bg_option ]['background-type'] : '';

				$theme_options[ $bg_option ]['overlay-type']     = 'none';
				$theme_options[ $bg_option ]['overlay-color']    = '';
				$theme_options[ $bg_option ]['overlay-gradient'] = '';

				if ( 'image' === $bg_type ) {
					$bg_img   = isset( $theme_options[ $bg_option ]['background-image'] ) ? $theme_options[ $bg_option ]['background-image'] : '';
					$bg_color = isset( $theme_options[ $bg_option ]['background-color'] ) ? $theme_options[ $bg_option ]['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $bg_option ]['overlay-type']     = 'classic';
						$theme_options[ $bg_option ]['overlay-color']    = $bg_color;
						$theme_options[ $bg_option ]['overlay-gradient'] = '';
					}
				}
			}
		}

		$ast_resp_bg_control_options = array(
			'hba-footer-bg-obj-responsive',
			'hbb-footer-bg-obj-responsive',
			'footer-bg-obj-responsive',
			'footer-menu-bg-obj-responsive',
			'hb-footer-bg-obj-responsive',
			'hba-header-bg-obj-responsive',
			'hbb-header-bg-obj-responsive',
			'hb-header-bg-obj-responsive',
			'header-mobile-menu-bg-obj-responsive',
			'site-layout-outside-bg-obj-responsive',
			'content-bg-obj-responsive',
		);

		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
		foreach ( $post_types as $index => $post_type ) {
			$ast_resp_bg_control_options[] = 'ast-dynamic-archive-' . esc_attr( $post_type ) . '-banner-custom-bg';
			$ast_resp_bg_control_options[] = 'ast-dynamic-single-' . esc_attr( $post_type ) . '-banner-background';
		}

		$component_limit = defined( 'ASTRA_EXT_VER' ) ? Astra_Builder_Helper::$component_limit : Astra_Builder_Helper::$num_of_header_menu;
		for ( $index = 1; $index <= $component_limit; $index++ ) {
			$_prefix                       = 'menu' . $index;
			$ast_resp_bg_control_options[] = 'header-' . $_prefix . '-bg-obj-responsive';
		}

		foreach ( $ast_resp_bg_control_options as $key => $resp_bg_option ) {
			// Desktop version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['desktop'] ) && is_array( $theme_options[ $resp_bg_option ]['desktop'] ) && ! isset( $theme_options[ $resp_bg_option ]['desktop']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$desk_bg_type = isset( $theme_options[ $resp_bg_option ]['desktop']['background-type'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-type'] : '';
				// @codingStandardsIgnoreEnd

				$theme_options[ $resp_bg_option ]['desktop']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['desktop']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['desktop']['overlay-gradient'] = '';

				if ( 'image' === $desk_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['desktop']['background-image'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['desktop']['background-color'] ) ? $theme_options[ $resp_bg_option ]['desktop']['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['desktop']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['desktop']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['desktop']['overlay-gradient'] = '';
					}
				}
			}

			// Tablet version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['tablet'] ) && is_array( $theme_options[ $resp_bg_option ]['tablet'] ) && ! isset( $theme_options[ $resp_bg_option ]['tablet']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$tablet_bg_type = isset( $theme_options[ $resp_bg_option ]['tablet']['background-type'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-type'] : '';
				// @codingStandardsIgnoreEnd
				$theme_options[ $resp_bg_option ]['tablet']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['tablet']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['tablet']['overlay-gradient'] = '';
				if ( 'image' === $tablet_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['tablet']['background-image'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['tablet']['background-color'] ) ? $theme_options[ $resp_bg_option ]['tablet']['background-color'] : '';
					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['tablet']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['tablet']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['tablet']['overlay-gradient'] = '';
					}
				}
			}


			// Mobile version.
			/** @psalm-suppress PossiblyUndefinedStringArrayOffset */ // phpcs:ignore Generic.Commenting.DocComment.MissingShort
			if ( isset( $theme_options[ $resp_bg_option ]['mobile'] ) && is_array( $theme_options[ $resp_bg_option ]['mobile'] ) && ! isset( $theme_options[ $resp_bg_option ]['mobile']['overlay-type'] ) ) {
				// @codingStandardsIgnoreStart
				$mobile_bg_type = isset( $theme_options[ $resp_bg_option ]['mobile']['background-type'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-type'] : '';
				// @codingStandardsIgnoreEnd
				$theme_options[ $resp_bg_option ]['mobile']['overlay-type']     = '';
				$theme_options[ $resp_bg_option ]['mobile']['overlay-color']    = '';
				$theme_options[ $resp_bg_option ]['mobile']['overlay-gradient'] = '';

				if ( 'image' === $mobile_bg_type ) {
					$bg_img   = isset( $theme_options[ $resp_bg_option ]['mobile']['background-image'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-image'] : '';
					$bg_color = isset( $theme_options[ $resp_bg_option ]['mobile']['background-color'] ) ? $theme_options[ $resp_bg_option ]['mobile']['background-color'] : '';

					if ( '' !== $bg_img && '' !== $bg_color && ( ! is_numeric( strpos( $bg_color, 'linear-gradient' ) ) && ! is_numeric( strpos( $bg_color, 'radial-gradient' ) ) ) ) {
						$theme_options[ $resp_bg_option ]['mobile']['overlay-type']     = 'classic';
						$theme_options[ $resp_bg_option ]['mobile']['overlay-color']    = $bg_color;
						$theme_options[ $resp_bg_option ]['mobile']['overlay-gradient'] = '';
					}
				}
			}
		}

		$theme_options['v4-1-4-update-migration'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.1.6
 *
 * @since 4.1.6
 * @return void
 */
function astra_theme_background_updater_4_1_6() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['list-block-vertical-spacing'] ) ) {
		$theme_options['list-block-vertical-spacing'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Set flag to avoid direct reflections on live site & to maintain backward compatibility for existing users.
 *
 * @since 4.1.7
 * @return void
 */
function astra_theme_background_updater_4_1_7() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['add-hr-styling-css'] ) ) {
		$theme_options['add-hr-styling-css'] = false;
		update_option( 'astra-settings', $theme_options );
	}

	if ( ! isset( $theme_options['astra-site-svg-logo-equal-height'] ) ) {
		$theme_options['astra-site-svg-logo-equal-height'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Migrating users to new container layout options
 *
 * @since 4.2.0
 * @return void
 */
function astra_theme_background_updater_4_2_0() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-2-0-update-migration'] ) ) {

		$post_types          = Astra_Posts_Structure_Loader::get_supported_post_types();
		$theme_options       = get_option( 'astra-settings' );
		$blog_types          = array( 'single', 'archive' );
		$third_party_layouts = array( 'woocommerce', 'edd', 'lifterlms', 'lifterlms-course-lesson', 'learndash' );

		// Global.
		if ( isset( $theme_options['site-content-layout'] ) ) {
			$theme_options = astra_apply_layout_migration( 'site-content-layout', 'ast-site-content-layout', 'site-content-style', 'site-sidebar-style', $theme_options );
		}

		// Single, archive.
		foreach ( $blog_types as $index => $blog_type ) {
			foreach ( $post_types as $index => $post_type ) {
				$old_layout    = $blog_type . '-' . esc_attr( $post_type ) . '-content-layout';
				$new_layout    = $blog_type . '-' . esc_attr( $post_type ) . '-ast-content-layout';
				$content_style = $blog_type . '-' . esc_attr( $post_type ) . '-content-style';
				$sidebar_style = $blog_type . '-' . esc_attr( $post_type ) . '-sidebar-style';

				if ( isset( $theme_options[ $old_layout ] ) ) {
					$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options );
				}
			}
		}

		// Third party existing layout migrations to new layout options.
		foreach ( $third_party_layouts as $index => $layout ) {
			$old_layout    = $layout . '-content-layout';
			$new_layout    = $layout . '-ast-content-layout';
			$content_style = $layout . '-content-style';
			$sidebar_style = $layout . '-sidebar-style';
			if ( isset( $theme_options[ $old_layout ] ) ) {
				if ( 'lifterlms' === $layout ) {

					// Lifterlms course/lesson sidebar style migration case.
					$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, 'lifterlms-course-lesson-sidebar-style', $theme_options );
				}
				$theme_options = astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options );
			}
		}

		if ( ! isset( $theme_options['fullwidth_sidebar_support'] ) ) {
			$theme_options['fullwidth_sidebar_support'] = false;
		}

		$theme_options['v4-2-0-update-migration'] = true;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle migration from old to new layouts.
 *
 * Migration cases for old users, old layouts -> new layouts.
 *
 * @since 4.2.0
 * @param mixed $old_layout
 * @param mixed $new_layout
 * @param mixed $content_style
 * @param mixed $sidebar_style
 * @param array $theme_options
 * @return array $theme_options The updated theme options.
 */
function astra_apply_layout_migration( $old_layout, $new_layout, $content_style, $sidebar_style, $theme_options ) {
	switch ( astra_get_option( $old_layout ) ) {
		case 'boxed-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'boxed';
			$theme_options[ $sidebar_style ] = 'boxed';
			break;
		case 'content-boxed-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'boxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'plain-container':
			$theme_options[ $new_layout ]    = 'normal-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'page-builder':
			$theme_options[ $new_layout ]    = 'full-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		case 'narrow-container':
			$theme_options[ $new_layout ]    = 'narrow-width-container';
			$theme_options[ $content_style ] = 'unboxed';
			$theme_options[ $sidebar_style ] = 'unboxed';
			break;
		default:
			$theme_options[ $new_layout ]    = 'default';
			$theme_options[ $content_style ] = 'default';
			$theme_options[ $sidebar_style ] = 'default';
			break;
	}
	return $theme_options;
}

/**
 * Handle backward compatibility on version 4.2.2
 *
 * @since 4.2.2
 * @return void
 */
function astra_theme_background_updater_4_2_2() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-2-2-core-form-btns-styling'] ) ) {
		$theme_options['v4-2-2-core-form-btns-styling'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.4.0
 *
 * @since 4.4.0
 * @return void
 */
function astra_theme_background_updater_4_4_0() {
	$theme_options = get_option( 'astra-settings', array() );
	if ( ! isset( $theme_options['v4-4-0-backward-option'] ) ) {
		$theme_options['v4-4-0-backward-option'] = false;

		// Migrate primary button outline styles to secondary buttons.
		if ( isset( $theme_options['font-family-button'] ) ) {
			$theme_options['secondary-font-family-button'] = $theme_options['font-family-button'];
		}
		if ( isset( $theme_options['font-size-button'] ) ) {
			$theme_options['secondary-font-size-button'] = $theme_options['font-size-button'];
		}
		if ( isset( $theme_options['font-weight-button'] ) ) {
			$theme_options['secondary-font-weight-button'] = $theme_options['font-weight-button'];
		}
		if ( isset( $theme_options['font-extras-button'] ) ) {
			$theme_options['secondary-font-extras-button'] = $theme_options['font-extras-button'];
		}
		if ( isset( $theme_options['button-bg-color'] ) ) {
			$theme_options['secondary-button-bg-color'] = $theme_options['button-bg-color'];
		}
		if ( isset( $theme_options['button-bg-h-color'] ) ) {
			$theme_options['secondary-button-bg-h-color'] = $theme_options['button-bg-h-color'];
		}
		if ( isset( $theme_options['theme-button-border-group-border-color'] ) ) {
			$theme_options['secondary-theme-button-border-group-border-color'] = $theme_options['theme-button-border-group-border-color'];
		}
		if ( isset( $theme_options['theme-button-border-group-border-h-color'] ) ) {
			$theme_options['secondary-theme-button-border-group-border-h-color'] = $theme_options['theme-button-border-group-border-h-color'];
		}
		if ( isset( $theme_options['button-radius-fields'] ) ) {
			$theme_options['secondary-button-radius-fields'] = $theme_options['button-radius-fields'];
		}

		// Single - Article Featured Image visibility migration.
		$post_types = Astra_Posts_Structure_Loader::get_supported_post_types();
		foreach ( $post_types as $index => $post_type ) {
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-position-layout-1' ] = 'none';
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-position-layout-2' ] = 'none';
			$theme_options[ 'ast-dynamic-single-' . esc_attr( $post_type ) . '-article-featured-image-ratio-type' ]        = 'default';
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.5.0.
 *
 * @since 4.5.0
 * @return void
 */
function astra_theme_background_updater_4_5_0() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['v4-5-0-backward-option'] ) ) {
		$theme_options['v4-5-0-backward-option'] = false;

		$palette_options = get_option( 'astra-color-palettes', Astra_Global_Palette::get_default_color_palette() );
		if ( ! isset( $palette_options['presets'] ) ) {
			$palette_options['presets'] = astra_get_palette_presets();
			update_option( 'astra-color-palettes', $palette_options );
		}

		update_option( 'astra-settings', $theme_options );
	}
}

/**
 * Handle backward compatibility on version 4.5.2.
 *
 * @since 4.5.2
 * @return void
 */
function astra_theme_background_updater_4_5_2() {
	$theme_options = get_option( 'astra-settings', array() );

	if ( ! isset( $theme_options['scndry-btn-default-padding'] ) ) {
		$theme_options['scndry-btn-default-padding'] = false;
		update_option( 'astra-settings', $theme_options );
	}
}
