<?php
/**
 * FSE Freelancer Portfolio functions and definitions
 *
 * @package fse_freelancer_portfolio
 * @since 1.0
 */

if ( ! function_exists( 'fse_freelancer_portfolio_support' ) ) :
	function fse_freelancer_portfolio_support() {

		// Add support for block styles.
		add_theme_support( 'wp-block-styles' );

		// Enqueue editor styles.
		add_editor_style( 'style.css' );

	}
endif;

add_action( 'after_setup_theme', 'fse_freelancer_portfolio_support' );

if ( ! function_exists( 'fse_freelancer_portfolio_styles' ) ) :
	function fse_freelancer_portfolio_styles() {
		// Register theme stylesheet.
		$fse_freelancer_portfolio_theme_version = wp_get_theme()->get( 'Version' );

		$fse_freelancer_portfolio_version_string = is_string( $fse_freelancer_portfolio_theme_version ) ? $fse_freelancer_portfolio_theme_version : false;
		wp_enqueue_style(
			'fse-freelancer-portfolio-style',
			get_template_directory_uri() . '/style.css',
			array(),
			$fse_freelancer_portfolio_version_string
		);

		// Enqueue the 'animate-style' stylesheet
		wp_enqueue_style( 'animate-style', 
			get_template_directory_uri() . '/assets/css/animate.min.css', 
			array(), 
			$fse_freelancer_portfolio_theme_version, 
			'all' 
		);
		
		// Enqueue the 'themeanimate-js' script, dependent on jQuery
		wp_enqueue_script( 'themeanimate-js', 
			get_template_directory_uri() . '/assets/js/themeanimate.js', 
			array( 'jquery' ), 
			$fse_freelancer_portfolio_theme_version, true 
		);
	}
endif;

add_action( 'wp_enqueue_scripts', 'fse_freelancer_portfolio_styles' );

/* Theme Credit link */
define('FSE_FREELANCER_PORTFOLIO_BUY_NOW',__('https://www.cretathemes.com/products/portfolio-wordpress-theme/','fse-freelancer-portfolio'));
define('FSE_FREELANCER_PORTFOLIO_PRO_DEMO',__('https://pattern.cretathemes.com/fse-freelancer-portfolio/','fse-freelancer-portfolio'));
define('FSE_FREELANCER_PORTFOLIO_THEME_DOC',__('https://pattern.cretathemes.com/pro-guide/fse-freelancer-portfolio/','fse-freelancer-portfolio'));
define('FSE_FREELANCER_PORTFOLIO_SUPPORT',__('https://wordpress.org/support/theme/fse-freelancer-portfolio/','fse-freelancer-portfolio'));
define('FSE_FREELANCER_PORTFOLIO_REVIEW',__('https://wordpress.org/support/theme/fse-freelancer-portfolio/reviews/#new-post','fse-freelancer-portfolio'));

// Add block patterns
require get_template_directory() . '/inc/block-patterns.php';

// Add block styles
require get_template_directory() . '/inc/block-styles.php';

// Block Filters
require get_template_directory() . '/inc/block-filters.php';

// Svg icons
require get_template_directory() . '/inc/icon-function.php';

// Customizer
require get_template_directory() . '/inc/customizer.php';

// Get Started.
require get_template_directory() . '/inc/get-started/get-started.php';

// Add Getstart admin notice
function fse_freelancer_portfolio_admin_notice() { 
    global $pagenow;
    $theme_args      = wp_get_theme();
    $meta            = get_option( 'fse_freelancer_portfolio_admin_notice' );
    $name            = $theme_args->__get( 'Name' );
    $current_screen  = get_current_screen();

    if( !$meta ){
	    if( is_network_admin() ){
	        return;
	    }

	    if( ! current_user_can( 'manage_options' ) ){
	        return;
	    } if($current_screen->base != 'appearance_page_fse-freelancer-portfolio-guide-page' ) { ?>

	    <div class="notice notice-success">
	        <h1><?php esc_html_e('Hey, Thank you for installing Apparel Ecommerce Store Theme!', 'fse-freelancer-portfolio'); ?></h1>
	        <p><a class="button button-primary customize load-customize hide-if-no-customize" href="<?php echo esc_url( admin_url( 'themes.php?page=fse-freelancer-portfolio-guide-page' ) ); ?>"><?php esc_html_e('Navigate Getstart', 'fse-freelancer-portfolio'); ?></a></p>
	        <p class="dismiss-link"><strong><a href="?fse_freelancer_portfolio_admin_notice=1"><?php esc_html_e( 'Dismiss', 'fse-freelancer-portfolio' ); ?></a></strong></p>
	    </div>
	    <?php

	}?>
	    <?php

	}
}

add_action( 'admin_notices', 'fse_freelancer_portfolio_admin_notice' );

if( ! function_exists( 'fse_freelancer_portfolio_update_admin_notice' ) ) :
/**
 * Updating admin notice on dismiss
*/
function fse_freelancer_portfolio_update_admin_notice(){
    if ( isset( $_GET['fse_freelancer_portfolio_admin_notice'] ) && $_GET['fse_freelancer_portfolio_admin_notice'] = '1' ) {
        update_option( 'fse_freelancer_portfolio_admin_notice', true );
    }
}
endif;
add_action( 'admin_init', 'fse_freelancer_portfolio_update_admin_notice' );

//After Switch theme function
add_action('after_switch_theme', 'fse_freelancer_portfolio_getstart_setup_options');
function fse_freelancer_portfolio_getstart_setup_options () {
    update_option('fse_freelancer_portfolio_admin_notice', FALSE );
}