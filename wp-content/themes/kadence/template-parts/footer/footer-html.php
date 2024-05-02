<?php
/**
 * Template part for displaying the footer info
 *
 * @package kadence
 */

namespace Kadence;

$align = ( kadence()->sub_option( 'footer_html_align', 'desktop' ) ? kadence()->sub_option( 'footer_html_align', 'desktop' ) : 'default' );
$tablet_align = ( kadence()->sub_option( 'footer_html_align', 'tablet' ) ? kadence()->sub_option( 'footer_html_align', 'tablet' ) : 'default' );
$mobile_align = ( kadence()->sub_option( 'footer_html_align', 'mobile' ) ? kadence()->sub_option( 'footer_html_align', 'mobile' ) : 'default' );

$valign = ( kadence()->sub_option( 'footer_html_vertical_align', 'desktop' ) ? kadence()->sub_option( 'footer_html_vertical_align', 'desktop' ) : 'default' );
$tablet_valign = ( kadence()->sub_option( 'footer_html_vertical_align', 'tablet' ) ? kadence()->sub_option( 'footer_html_vertical_align', 'tablet' ) : 'default' );
$mobile_valign = ( kadence()->sub_option( 'footer_html_vertical_align', 'mobile' ) ? kadence()->sub_option( 'footer_html_vertical_align', 'mobile' ) : 'default' );

?>

<div class="footer-widget-area site-info site-footer-focus-item content-align-<?php echo esc_attr( $align ); ?> content-tablet-align-<?php echo esc_attr( $tablet_align ); ?> content-mobile-align-<?php echo esc_attr( $mobile_align ); ?> content-valign-<?php echo esc_attr( $valign ); ?> content-tablet-valign-<?php echo esc_attr( $tablet_valign ); ?> content-mobile-valign-<?php echo esc_attr( $mobile_valign ); ?>" data-section="kadence_customizer_footer_html">
	<div class="footer-widget-area-inner site-info-inner">
		<?php
		/**
		 * Kadence Footer HTML
		 *
		 * Hooked Kadence\footer_html
		 */
		do_action( 'kadence_footer_html' );
		?>
	</div>
</div><!-- .site-info -->
