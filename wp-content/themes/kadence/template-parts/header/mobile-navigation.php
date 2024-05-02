<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item site-header-item-mobile-navigation mobile-navigation-layout-stretch-<?php echo ( kadence()->option( 'mobile_navigation_stretch' ) ? 'true' : 'false' ); ?>" data-section="kadence_customizer_mobile_navigation">
	<?php
	/**
	 * Kadence Mobile Navigation
	 *
	 * Hooked Kadence\mobile_navigation
	 */
	do_action( 'kadence_mobile_navigation' );
	?>
</div><!-- data-section="mobile_navigation" -->
