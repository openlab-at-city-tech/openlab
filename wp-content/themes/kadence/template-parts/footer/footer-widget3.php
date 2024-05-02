<?php
/**
 * Template part for displaying the footer info
 *
 * @package kadence
 */

namespace Kadence;

$align = ( kadence()->sub_option( 'footer_widget3_align', 'desktop' ) ? kadence()->sub_option( 'footer_widget3_align', 'desktop' ) : 'default' );
$tablet_align = ( kadence()->sub_option( 'footer_widget3_align', 'tablet' ) ? kadence()->sub_option( 'footer_widget3_align', 'tablet' ) : 'default' );
$mobile_align = ( kadence()->sub_option( 'footer_widget3_align', 'mobile' ) ? kadence()->sub_option( 'footer_widget3_align', 'mobile' ) : 'default' );

$valign = ( kadence()->sub_option( 'footer_widget3_vertical_align', 'desktop' ) ? kadence()->sub_option( 'footer_widget3_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( kadence()->sub_option( 'footer_widget3_vertical_align', 'tablet' ) ? kadence()->sub_option( 'footer_widget3_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( kadence()->sub_option( 'footer_widget3_vertical_align', 'mobile' ) ? kadence()->sub_option( 'footer_widget3_vertical_align', 'mobile' ) : 'default' );

?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-widget3 content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?>" data-section="sidebar-widgets-footer3">
	<div class="footer-widget-area-inner site-info-inner">
		<?php
		dynamic_sidebar( 'footer3' );
		?>
	</div>
</div><!-- .footer-widget3 -->
