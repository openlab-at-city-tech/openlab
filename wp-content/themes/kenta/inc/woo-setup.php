<?php
/**
 * Kenta Theme WooCommerce Setup
 *
 * @package Kenta
 */

use LottaFramework\Facades\Css;
use LottaFramework\Facades\CZ;
use LottaFramework\Utils;

if ( ! function_exists( 'kenta_woo_setup' ) ) {
	/**
	 * WooCommerce setup.
	 */
	function kenta_woo_setup() {
		add_theme_support( 'woocommerce' );
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
}
add_action( 'after_setup_theme', 'kenta_woo_setup' );

if ( ! function_exists( 'kenta_remove_woo_css' ) ) {
	/**
	 * Disable original WooCommerce CSS.
	 *
	 * @param array $styles
	 *
	 * @return array
	 */
	function kenta_remove_woo_css( $styles ) {
		$styles['woocommerce-layout']['src']      = false;
		$styles['woocommerce-smallscreen']['src'] = false;
		$styles['woocommerce-general']['src']     = false;

		return $styles;
	}
}
add_filter( 'woocommerce_enqueue_styles', 'kenta_remove_woo_css' );

if ( ! function_exists( 'kenta_enqueue_woo_scripts' ) ) {
	function kenta_enqueue_woo_scripts() {
		$suffix = defined( 'WP_DEBUG' ) && WP_DEBUG ? '' : '.min';

		// Theme admin scripts
		wp_register_style(
			'kenta-woo-style',
			get_template_directory_uri() . '/dist/css/woo' . $suffix . '.css',
			[],
			KENTA_VERSION
		);

		wp_enqueue_style( 'kenta-woo-style' );
	}
}
add_action( 'wp_enqueue_scripts', 'kenta_enqueue_woo_scripts', 30 );

if ( ! function_exists( 'kenta_woo_dynamic_css' ) ) {
	/**
	 * WooCommerce dynamic css
	 *
	 * @param array $css
	 *
	 * @return mixed
	 */
	function kenta_woo_dynamic_css( array $css = [] ) {
		if ( is_store_notice_showing() ) {
			$css['.woocommerce-store-notice, p.demo_store'] = array_merge(
				Css::colors( CZ::get( 'kenta_store_notice_colors' ), [
					'text'            => 'color',
					'dismiss-initial' => '--kenta-link-initial-color',
					'dismiss-hover'   => '--kenta-link-hover-color',
				] ),
				Css::background( CZ::get( 'kenta_store_notice_background' ) )
			);
		}

		if ( kenta_is_woo_shop() ) {
			// product wrapper
			$card_width = [];
			foreach ( CZ::get( 'kenta_store_catalog_columns' ) as $device => $columns ) {
				$card_width[ $device ] = sprintf( "%.2f", substr( sprintf( "%.3f", ( 100 / (int) $columns ) ), 0, - 1 ) ) . '%';
			}
			$css['.kenta-products > .product'] = [
				'width' => $card_width,
			];

			// products list
			$css['.kenta-products'] = [
				'--kenta-link-initial-color' => 'var(--kenta-primary-color)',
				'--kenta-link-hover-color'   => 'var(--kenta-primary-active)',
				'--card-gap'                 => CZ::get( 'kenta_store_catalog_gap' ),
			];
			// product title
			$css['.kenta-products .woocommerce-loop-product__title'] = [
				'font-size'   => '1rem',
				'font-weight' => 600
			];

			// product wrapper
			$css['.woocommerce .kenta-products li.product .kenta-product-wrapper'] = array_merge(
				$css['.woocommerce .kenta-products li.product .kenta-product-wrapper'] ?? [],
				[
					'text-align'               => CZ::get( 'kenta_store_card_content_alignment' ),
					'--card-thumbnail-spacing' => CZ::get( 'kenta_store_card_thumbnail_spacing' ),
					'--card-content-spacing'   => CZ::get( 'kenta_store_card_content_spacing' )
				],
				kenta_card_preset_style( CZ::get( 'kenta_store_card_style_preset' ) )
			);

			// product button
			$preset = kenta_button_preset( 'kenta_entry_read_more_', CZ::get( 'kenta_entry_read_more_preset' ) );

			$css[".kenta-products .kenta-button"] = array_merge(
				[
					'--kenta-button-height' => CZ::get( 'kenta_entry_read_more_min_height' )
				],
				Css::shadow( CZ::get( 'kenta_entry_read_more_shadow', $preset ) ),
				Css::typography( CZ::get( 'kenta_entry_read_more_typography', $preset ) ),
				Css::dimensions( CZ::get( 'kenta_entry_read_more_padding', $preset ), '--kenta-button-padding' ),
				Css::dimensions( CZ::get( 'kenta_entry_read_more_radius', $preset ), '--kenta-button-radius' ),
				Css::colors( CZ::get( 'kenta_entry_read_more_text_color', $preset ), [
					'initial' => '--kenta-button-text-initial-color',
					'hover'   => '--kenta-button-text-hover-color',
				] ),
				Css::colors( CZ::get( 'kenta_entry_read_more_button_color', $preset ), [
					'initial' => '--kenta-button-initial-color',
					'hover'   => '--kenta-button-hover-color',
				] ),
				Css::border( CZ::get( 'kenta_entry_read_more_border', $preset ), '--kenta-button-border' )
			);

			$css[".kenta-products .kenta-button:hover"] = Css::shadow( CZ::get( 'kenta_entry_read_more_shadow_active', $preset ) );

			// pagination
			$css['.woocommerce-pagination'] = array_merge(
				Css::border( CZ::get( 'kenta_pagination_button_border' ), '--kenta-pagination-button-border' ),
				Css::colors( CZ::get( 'kenta_pagination_button_color' ), [
					'initial' => '--kenta-pagination-initial-color',
					'active'  => '--kenta-pagination-active-color',
					'accent'  => '--kenta-pagination-accent-color',
				] ),
				Css::typography( CZ::get( 'kenta_pagination_typography' ) ),
				[
					'--kenta-pagination-button-radius' => CZ::get( 'kenta_pagination_button_radius' ),
					'justify-content'                  => CZ::get( 'kenta_pagination_alignment' )
				]
			);
		}

		return $css;
	}
}
add_filter( 'kenta_filter_no_cache_dynamic_css', 'kenta_woo_dynamic_css' );

if ( ! function_exists( 'kenta_woo_before_content' ) ) {
	/**
	 * Wrap woocommerce content - start
	 */
	function kenta_woo_before_content() {
		$sidebar = kenta_get_sidebar_layout( 'store' );

		?>
        <main class="<?php Utils::the_clsx( kenta_container_css( array( 'sidebar' => $sidebar ) ) ) ?>">
        <div id="content" class="flex-grow max-w-full text-accent">
		<?php if ( ! is_shop() ): ?>
            <div class="kenta-article-content kenta-entry-content entry-content clearfix mx-auto">
		<?php endif; ?>
		<?php
	}
}

if ( ! function_exists( 'kenta_woo_after_content' ) ) {
	/**
	 * Wrap woocommerce content - end
	 */
	function kenta_woo_after_content() {
		$layout = 'no-sidebar';

		if ( ! is_product() ) {
			$layout = kenta_get_sidebar_layout( 'store' );
		}

		?>
		<?php if ( ! is_shop() ): ?>
            </div>
		<?php endif; ?>
        </div>
		<?php
		/**
		 * Hook - kenta_action_sidebar.
		 */
		do_action( 'kenta_action_sidebar', $layout );
		?>
        </main>
		<?php
	}
}

/**
 * WooCommerce's products loop hooks
 */

if ( ! function_exists( 'kenta_woo_loop_filters_wrapper' ) ) {
	/**
	 * Wrap WooCommerce filters start
	 */
	function kenta_woo_loop_filters_wrapper() {
		?><div class="kenta-products-filters"><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_filters_wrapper_end' ) ) {
	/**
	 * Wrap WooCommerce filters end
	 */
	function kenta_woo_loop_filters_wrapper_end() {
		?></div><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_item_wrapper' ) ) {
	/**
	 * Wrap WooCommerce loop product item start
	 */
	function kenta_woo_loop_item_wrapper() {
		$classnames = Utils::clsx( [
			'kenta-product-wrapper' => true,
			'kenta-scroll-reveal'   => CZ::checked( 'kenta_store_card_scroll_reveal' )
		] )

		?><div class="<?php echo esc_attr( $classnames ) ?>"><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_item_wrapper_end' ) ) {
	/**
	 * Wrap WooCommerce loop product item end
	 */
	function kenta_woo_loop_item_wrapper_end() {
		?></div><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_product_thumbnail_wrapper' ) ) {
	/**
	 * Wrap WooCommerce loop product thumbnail start
	 */
	function kenta_woo_loop_product_thumbnail_wrapper() {
		?><div class="kenta-product-thumbnail"><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_product_thumbnail_wrapper_end' ) ) {
	/**
	 * Wrap WooCommerce loop product thumbnail end
	 */
	function kenta_woo_loop_product_thumbnail_wrapper_end() {
		?></div><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_product_content_wrapper' ) ) {
	/**
	 * Wrap WooCommerce loop product content start
	 */
	function kenta_woo_loop_product_content_wrapper() {
		?><div class="kenta-product-content"><?php
	}
}

if ( ! function_exists( 'kenta_woo_loop_product_content_wrapper_end' ) ) {
	/**
	 * Wrap WooCommerce loop product content end
	 */
	function kenta_woo_loop_product_content_wrapper_end() {
		?></div><?php
	}
}

/**
 * Single product
 */

if ( ! function_exists( 'kenta_woo_product_gallery_wrapper' ) ) {
	function kenta_woo_product_gallery_wrapper() {
		?><div class="kenta-woo-single-gallery"><?php
	}
}

if ( ! function_exists( 'kenta_woo_product_gallery_wrapper_end' ) ) {
	function kenta_woo_product_gallery_wrapper_end() {
		?></div><?php
	}
}

/**
 *  Checkout page hooks
 */

if ( ! function_exists( 'kenta_woo_checkout_wrapper' ) ) {
	function kenta_woo_checkout_wrapper() {
		?><div class="kenta-woo-checkout-wrapper"><?php
	}
}

if ( ! function_exists( 'kenta_woo_checkout_wrapper_end' ) ) {
	function kenta_woo_checkout_wrapper_end() {
		?></div><?php
	}
}

if ( ! function_exists( 'kenta_woo_checkout_left_wrapper' ) ) {
	function kenta_woo_checkout_left_wrapper() {
		?><div class="kenta-woo-checkout-left-columns"><?php
	}
}

if ( ! function_exists( 'kenta_woo_checkout_left_wrapper_end' ) ) {
	function kenta_woo_checkout_left_wrapper_end() {
		?></div><?php
	}
}

if ( ! function_exists( 'kenta_woo_checkout_right_wrapper' ) ) {
	function kenta_woo_checkout_right_wrapper() {
		?><div class="kenta-woo-checkout-right-columns"><?php
	}
}

if ( ! function_exists( 'kenta_woo_checkout_right_wrapper_end' ) ) {
	function kenta_woo_checkout_right_wrapper_end() {
		?></div><?php
	}
}

/**
 *  Products loop hooks
 */

if ( ! function_exists( 'kenta_woo_set_loop_posts_per_page' ) ) {
	/**
	 * Products pre page count
	 *
	 * @return int
	 */
	function kenta_woo_set_loop_posts_per_page() {
		return intval( CZ::get( 'kenta_store_catalog_per_page' ) );
	}
}

if ( ! function_exists( 'kenta_woo_content_header' ) ) {
	/**
	 * Show shop header
	 */
	function kenta_woo_content_header() {
		if ( is_product() ) {
			// Don't show header on single product page
			return;
		}

		kenta_show_archive_header();
	}
}

if ( ! function_exists( 'kenta_woo_modify_loop_add_to_card_args' ) ) {
	/**
	 * Modify add to card button classes
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	function kenta_woo_modify_loop_add_to_card_args( $args ) {
		$args['class'] = $args['class'] . ' kenta-button';

		return $args;
	}
}

/**
 * Modify hooks entry
 */

if ( ! function_exists( 'kenta_modify_woo_template_hooks' ) ) {
	/**
	 * Modify woo template hooks
	 */
	function kenta_modify_woo_template_hooks() {
		// Change mobile devices breakpoint.
		add_filter( 'woocommerce_style_smallscreen_breakpoint', function ( $px ) {
			return \LottaFramework\Facades\Css::mobile();
		} );

		// Add custom filter to allow further class modification.
		add_filter( 'woocommerce_product_loop_start', function ( $html ) {
			return preg_replace( '/(class=".*?)"/', '$1 ' . implode( ' ', apply_filters( 'kenta_woo_loop_classes', array( 'kenta-products' ) ) ) . '"', $html );
		} );

		// Remove breadcrumbs for WooCommerce page.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
		// Remove Default WooCommerce Sidebar
		remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

		// Change main content (primary) wrapper.
		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
		add_action( 'woocommerce_before_main_content', 'kenta_woo_content_header', 0 );
		add_action( 'woocommerce_before_main_content', 'kenta_woo_before_content', 5 );
		add_action( 'woocommerce_after_main_content', 'kenta_woo_after_content', 50 );

		// Remove title from its original position.
		add_filter( 'woocommerce_show_page_title', '__return_false' );
		// Remove archive description from its original position.
		remove_action( 'woocommerce_archive_description', 'woocommerce_taxonomy_archive_description', 10 );

		// Remove the original link wrapper.
		remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
		remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );

		// Add wrapper to products grid filters.
		add_action( 'woocommerce_before_shop_loop', 'kenta_woo_loop_filters_wrapper', 11 );
		add_action( 'woocommerce_before_shop_loop', 'kenta_woo_loop_filters_wrapper_end', 999 );

		// Add wrapper to products grid item.
		add_action( 'woocommerce_before_shop_loop_item', 'kenta_woo_loop_item_wrapper', 1 );
		add_action( 'woocommerce_after_shop_loop_item', 'kenta_woo_loop_item_wrapper_end', 999 );

		// Add product thumbnail wrapper.
		add_action( 'woocommerce_before_shop_loop_item_title', 'kenta_woo_loop_product_thumbnail_wrapper', 1 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'kenta_woo_loop_product_thumbnail_wrapper_end', 999 );
		// Add a link wrapper to the product thumbnail.
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 9 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 19 );

		// Change the order of the on sale button.
		remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
		add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 2 );

		// Add a link wrapper to the product title.
		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_open', 1 );
		add_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 999 );

		// Add product content wrapper
		add_action( 'woocommerce_shop_loop_item_title', 'kenta_woo_loop_product_content_wrapper', 0 );
		add_action( 'woocommerce_after_shop_loop_item', 'kenta_woo_loop_product_content_wrapper_end', 999 );

		add_filter( 'woocommerce_loop_add_to_cart_args', 'kenta_woo_modify_loop_add_to_card_args' );

		// Products loop
		add_filter( 'loop_shop_per_page', 'kenta_woo_set_loop_posts_per_page' );

		/**
		 * Single product page
		 */
		// Add wrapper to products grid filters.
		add_action( 'woocommerce_before_single_product_summary', 'kenta_woo_product_gallery_wrapper', 19 );
		add_action( 'woocommerce_before_single_product_summary', 'kenta_woo_product_gallery_wrapper_end', 29 );
	}
}
add_action( 'init', 'kenta_modify_woo_template_hooks' );

if ( ! function_exists( 'kenta_modify_template_hooks_after_init' ) ) {
	/**
	 * Modify filters for WooCommerce template rendering
	 */
	function kenta_modify_template_hooks_after_init() {
		/**
		 * Checkout page hooks
		 */
		if ( is_checkout() ) {
			add_action( 'woocommerce_checkout_before_customer_details', 'kenta_woo_checkout_wrapper', 1 );

			add_action( 'woocommerce_checkout_before_customer_details', 'kenta_woo_checkout_left_wrapper', 1 );
			add_action( 'woocommerce_checkout_after_customer_details', 'kenta_woo_checkout_left_wrapper_end', 999 );

			add_action( 'woocommerce_checkout_before_order_review_heading', 'kenta_woo_checkout_right_wrapper', 1 );
			add_action( 'woocommerce_checkout_after_order_review', 'kenta_woo_checkout_right_wrapper_end', 999 );

			add_action( 'woocommerce_checkout_after_order_review', 'kenta_woo_checkout_wrapper_end', 999 );
		}
	}
}
add_action( 'wp', 'kenta_modify_template_hooks_after_init' );
