<?php
/**
 * Template part for displaying the header Social Modual
 *
 * @package kadence
 */

namespace Kadence;

$align        = ( kadence()->sub_option( 'footer_navigation_align', 'desktop' ) ? kadence()->sub_option( 'footer_navigation_align', 'desktop' ) : 'default' );
$tablet_align = ( kadence()->sub_option( 'footer_navigation_align', 'tablet' ) ? kadence()->sub_option( 'footer_navigation_align', 'tablet' ) : 'default' );
$mobile_align = ( kadence()->sub_option( 'footer_navigation_align', 'mobile' ) ? kadence()->sub_option( 'footer_navigation_align', 'mobile' ) : 'default' );

$valign        = ( kadence()->sub_option( 'footer_navigation_vertical_align', 'desktop' ) ? kadence()->sub_option( 'footer_navigation_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( kadence()->sub_option( 'footer_navigation_vertical_align', 'tablet' ) ? kadence()->sub_option( 'footer_navigation_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( kadence()->sub_option( 'footer_navigation_vertical_align', 'mobile' ) ? kadence()->sub_option( 'footer_navigation_vertical_align', 'mobile' ) : 'default' );

?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-navigation-wrap content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?> footer-navigation-layout-stretch-<?php echo ( kadence()->option( 'footer_navigation_stretch' ) ? 'true' : 'false' ); ?>" data-section="kadence_customizer_footer_navigation">
	<div class="footer-widget-area-inner footer-navigation-inner">
		<?php
		/**
		 * Kadence Footer Navigation
		 *
		 * Hooked Kadence\footer_navigation
		 */
		do_action( 'kadence_footer_navigation' );
		?>
	</div>
</div><!-- data-section="footer_navigation" -->
