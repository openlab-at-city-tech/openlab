<?php
/**
 * Template part for displaying the Footer Social Module
 *
 * @package kadence
 */

namespace Kadence;

$align        = ( kadence()->sub_option( 'footer_social_align', 'desktop' ) ? kadence()->sub_option( 'footer_social_align', 'desktop' ) : 'default' );
$tablet_align = ( kadence()->sub_option( 'footer_social_align', 'tablet' ) ? kadence()->sub_option( 'footer_social_align', 'tablet' ) : 'default' );
$mobile_align = ( kadence()->sub_option( 'footer_social_align', 'mobile' ) ? kadence()->sub_option( 'footer_social_align', 'mobile' ) : 'default' );

$valign        = ( kadence()->sub_option( 'footer_social_vertical_align', 'desktop' ) ? kadence()->sub_option( 'footer_social_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( kadence()->sub_option( 'footer_social_vertical_align', 'tablet' ) ? kadence()->sub_option( 'footer_social_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( kadence()->sub_option( 'footer_social_vertical_align', 'mobile' ) ? kadence()->sub_option( 'footer_social_vertical_align', 'mobile' ) : 'default' );
if ( ! wp_style_is( 'kadence-header', 'enqueued' ) ) {
	wp_enqueue_style( 'kadence-header' );
}
?>
<div class="footer-widget-area widget-area site-footer-focus-item footer-social content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?>" data-section="kadence_customizer_footer_social">
	<div class="footer-widget-area-inner footer-social-inner">
		<?php
		/**
		 * Kadence Footer Social
		 *
		 * Hooked Kadence\footer_social
		 */
		do_action( 'kadence_footer_social' );
		?>
	</div>
</div><!-- data-section="footer_social" -->
