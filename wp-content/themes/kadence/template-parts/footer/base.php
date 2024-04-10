<?php
/**
 * Template part for displaying the footer info
 *
 * @package kadence
 */

namespace Kadence;

if ( kadence()->has_content() ) {
	kadence()->print_styles( 'kadence-content' );
}
kadence()->print_styles( 'kadence-footer' );

?>
<footer id="colophon" class="site-footer" role="contentinfo">
	<div class="site-footer-wrap">
		<?php
		/**
		 * Kadence Top footer
		 *
		 * Hooked Kadence\top_footer
		 */
		do_action( 'kadence_top_footer' );
		/**
		 * Kadence Middle footer
		 *
		 * Hooked Kadence\middle_footer
		 */
		do_action( 'kadence_middle_footer' );
		/**
		 * Kadence Bottom footer
		 *
		 * Hooked Kadence\bottom_footer
		 */
		do_action( 'kadence_bottom_footer' );
		?>
	</div>
</footer><!-- #colophon -->

