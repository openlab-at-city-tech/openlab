<?php
/**
 * Template part for displaying the Mobile Header
 *
 * @package kadence
 */

namespace Kadence;

?>

<div id="mobile-header" class="site-mobile-header-wrap">
	<div class="site-header-inner-wrap<?php echo esc_attr( 'top_main_bottom' === kadence()->option( 'mobile_header_sticky' ) ? ' kadence-sticky-header' : '' ); ?>"<?php
	if ( 'top_main_bottom' === kadence()->option( 'mobile_header_sticky' ) ) {
		echo ' data-shrink="' . ( kadence()->option( 'mobile_header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
		echo ' data-reveal-scroll-up="' . ( kadence()->option( 'mobile_header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
		if ( kadence()->option( 'mobile_header_sticky_shrink' ) ) {
			echo ' data-shrink-height="' . esc_attr( kadence()->sub_option( 'mobile_header_sticky_main_shrink', 'size' ) ) . '"';
		}
	}
	?>>
		<div class="site-header-upper-wrap">
			<div class="site-header-upper-inner-wrap<?php echo esc_attr( 'top_main' === kadence()->option( 'mobile_header_sticky' ) ? ' kadence-sticky-header' : '' ); ?>"<?php
			if ( 'top_main' === kadence()->option( 'mobile_header_sticky' ) ) {
				echo ' data-shrink="' . ( kadence()->option( 'mobile_header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
				echo ' data-reveal-scroll-up="' . ( kadence()->option( 'mobile_header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
				if ( kadence()->option( 'mobile_header_sticky_shrink' ) ) {
					echo ' data-shrink-height="' . esc_attr( kadence()->sub_option( 'mobile_header_sticky_main_shrink', 'size' ) ) . '"';
				}
			}
			?>>
			<?php
			/**
			 * Kadence Top Header
			 *
			 * Hooked kadence_mobile_top_header 10
			 */
			do_action( 'kadence_mobile_top_header' );
			/**
			 * Kadence Main Header
			 *
			 * Hooked kadence_mobile_main_header 10
			 */
			do_action( 'kadence_mobile_main_header' );
			?>
			</div>
		</div>
		<?php
		/**
		 * Kadence Mobile Bottom Header
		 *
		 * Hooked kadence_mobile_bottom_header 10
		 */
		do_action( 'kadence_mobile_bottom_header' );
		?>
	</div>
</div>
