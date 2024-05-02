<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item site-header-item-main-navigation header-navigation-layout-stretch-<?php echo ( kadence()->option( 'secondary_navigation_stretch' ) ? 'true' : 'false' ); ?> header-navigation-layout-fill-stretch-<?php echo ( kadence()->option( 'secondary_navigation_fill_stretch' ) ? 'true' : 'false' ); ?>" data-section="kadence_customizer_secondary_navigation">
	<?php
	/**
	 * Kadence Secondary Navigation
	 *
	 * Hooked Kadence\secondary_navigation
	 */
	do_action( 'kadence_secondary_navigation' );
	?>
</div><!-- data-section="secondary_navigation" -->
