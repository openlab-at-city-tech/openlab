<?php
/**
 * Kadence\Elementor_Pro\Component class
 *
 * @package kadence
 */

namespace Kadence\Elementor_Pro;

use Kadence\Component_Interface;
use Kadence\Theme;
use Elementor;
use \Elementor\Plugin;
use ElementorPro\Modules\ThemeBuilder\ThemeSupport;
use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager;
use ElementorPro\Modules\ThemeBuilder\Module;
use function Kadence\kadence;
use function add_action;
use function have_posts;
use function the_post;
use function apply_filters;
use function get_template_part;
use function get_post_type;
use function is_account_page;
use function is_checkout;
use function is_cart;


/**
 * Class for adding Elementor plugin support.
 */
class Component implements Component_Interface {

	/**
	 * Gets the unique identifier for the theme component.
	 *
	 * @return string Component slug.
	 */
	public function get_slug() : string {
		return 'elementor_pro';
	}

	/**
	 * Adds the action and filter hooks to integrate with WordPress.
	 */
	public function initialize() {
		add_action( 'elementor/theme/register_locations', array( $this, 'register_elementor_locations' ) );
		add_action( 'elementor/dynamic_tags/register_tags', array( $this, 'add_palette_colors' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'disable_theme_account_css' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'disable_theme_checkout_changes' ), 20 );
		add_action( 'wp_enqueue_scripts', array( $this, 'disable_theme_cart_changes' ), 20 );
	}
	/**
	 * Disable theme account css.
	 */
	public function disable_theme_account_css() {
		if ( class_exists( 'woocommerce' ) && is_account_page() ) {
			if ( function_exists( 'elementor_location_exits' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
				wp_dequeue_style( 'kadence-account-woocommerce' );
				$kadence_theme_class = \Kadence\Theme::instance();
				remove_action( 'woocommerce_before_account_navigation', array( $kadence_theme_class->components['woocommerce'], 'myaccount_nav_wrap_start' ), 2 );
				remove_action( 'woocommerce_before_account_navigation', array( $kadence_theme_class->components['woocommerce'], 'myaccount_nav_avatar' ), 20 );
				remove_action( 'woocommerce_after_account_navigation', array( $kadence_theme_class->components['woocommerce'], 'myaccount_nav_wrap_end' ), 50 );
			}
		}
	}
	/**
	 * Disable theme checkout css.
	 */
	public function disable_theme_checkout_changes() {
		if ( class_exists( 'woocommerce' ) && is_checkout() ) {
			if ( function_exists( 'elementor_location_exits' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
				wp_enqueue_style( 'kadence-elementor-checkout', get_theme_file_uri( '/assets/css/elementor-checkout.min.css' ), array(), KADENCE_VERSION );
			}
		}
	}
	/**
	 * Disable theme cart changes.
	 */
	public function disable_theme_cart_changes() {
		if ( class_exists( 'woocommerce' ) && is_cart() ) {
			if ( function_exists( 'elementor_location_exits' ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( get_the_ID() ) ) {
				wp_enqueue_style( 'kadence-elementor-cart', get_theme_file_uri( '/assets/css/elementor-cart.min.css' ), array(), KADENCE_VERSION );
				$kadence_theme_class = \Kadence\Theme::instance();
				// Remove Cart Changes.
				remove_action( 'woocommerce_before_cart', array( $kadence_theme_class->components['woocommerce'], 'cart_form_wrap_before' ) );
				remove_action( 'woocommerce_after_cart', array( $kadence_theme_class->components['woocommerce'], 'cart_form_wrap_after' ) );
				remove_action( 'woocommerce_before_cart_table', array( $kadence_theme_class->components['woocommerce'], 'cart_summary_title' ) );
			}
		}
	}
	/**
	 * Elementor dynamic tag support.
	 *
	 * @param object $dynamic_tags the dynamic tags modal.
	 */
	public function add_palette_colors( $dynamic_tags ) {
		if ( apply_filters( 'kadence_theme_add_palette_to_elementor_tags', false ) ) {
			// In our Dynamic Tag we use a group named request-variables so we need.
			// To register that group as well before the tag.
			\Elementor\Plugin::$instance->dynamic_tags->register_group(
				'kadence-palette',
				array(
					'title' => __( 'Kadence Theme', 'kadence' ),
				)
			);

			require_once get_template_directory() . '/inc/components/elementor_pro/class-elementor-dynamic-colors.php'; // phpcs:ignore WPThemeReview.CoreFunctionality.FileInclude.FileIncludeFound

			// Finally register the tag.
			$dynamic_tags->register_tag( 'Kadence\Elementor_Pro\Elementor_Dynamic_Colors' );
		}
	}
	/**
	 * Elementor Location support.
	 *
	 * @param object $elementor_theme_manager the theme manager.
	 */
	public function register_elementor_locations( $elementor_theme_manager ) {
		$elementor_theme_manager->register_all_core_location();
		$elementor_theme_manager->register_location(
			'header',
			array(
				'hook'         => 'kadence_header',
				'remove_hooks' => array( 'Kadence\header_markup' ),
			)
		);
		$elementor_theme_manager->register_location(
			'footer',
			array(
				'hook'         => 'kadence_footer',
				'remove_hooks' => array( 'Kadence\footer_markup' ),
			)
		);
		$elementor_theme_manager->register_location(
			'single',
			array(
				'hook'         => 'kadence_single',
				'remove_hooks' => array( 'Kadence\single_markup' ),
			)
		);
	}
}
