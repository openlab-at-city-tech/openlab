<?php
/**
 * Template part for displaying the header
 *
 * @package kadence
 */

namespace Kadence;

kadence()->print_styles( 'kadence-header' );
?>
<header id="masthead" class="site-header" role="banner" <?php kadence()->print_microdata( 'header' ); ?>>
	<div id="main-header" class="site-header-wrap">
		<div class="site-header-inner-wrap<?php echo esc_attr( 'top_main_bottom' === kadence()->option( 'header_sticky' ) ? ' kadence-sticky-header' : '' ); ?>"<?php
		if ( 'top_main_bottom' === kadence()->option( 'header_sticky' ) ) {
			echo ' data-reveal-scroll-up="' . ( kadence()->option( 'header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
			echo ' data-shrink="' . ( kadence()->option( 'header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
			if ( kadence()->option( 'header_sticky_shrink' ) ) {
				echo ' data-shrink-height="' . esc_attr( kadence()->sub_option( 'header_sticky_main_shrink', 'size' ) ) . '"';
			}
		}
		?>>
			<div class="site-header-upper-wrap">
				<div class="site-header-upper-inner-wrap<?php echo esc_attr( 'top_main' === kadence()->option( 'header_sticky' ) ? ' kadence-sticky-header' : '' ); ?>"<?php
				if ( 'top_main' === kadence()->option( 'header_sticky' ) ) {
					echo ' data-reveal-scroll-up="' . ( kadence()->option( 'header_reveal_scroll_up' ) ? 'true' : 'false' ) . '"';
					echo ' data-shrink="' . ( kadence()->option( 'header_sticky_shrink' ) ? 'true' : 'false' ) . '"';
					if ( kadence()->option( 'header_sticky_shrink' ) ) {
						echo ' data-shrink-height="' . esc_attr( kadence()->sub_option( 'header_sticky_main_shrink', 'size' ) ) . '"';
					}
				}
				?>>
					<?php
					/**
					 * Kadence Top Header
					 *
					 * Hooked Kadence\top_header
					 */
					do_action( 'kadence_top_header' );
					/**
					 * Kadence Main Header
					 *
					 * Hooked Kadence\main_header
					 */
					do_action( 'kadence_main_header' );
					?>
				</div>
			</div>
			<?php
			/**
			 * Kadence Bottom Header
			 *
			 * Hooked Kadence\bottom_header
			 */
			do_action( 'kadence_bottom_header' );
			?>
		</div>
	</div>
	<?php
	/**
	 * Kadence Mobile Header
	 *
	 * Hooked Kadence\mobile_header
	 */
	do_action( 'kadence_mobile_header' );
	?>
</header><!-- #masthead -->
