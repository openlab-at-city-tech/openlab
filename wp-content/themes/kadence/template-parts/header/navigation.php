<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item site-header-item-main-navigation header-navigation-layout-stretch-<?php echo ( kadence()->option( 'primary_navigation_stretch' ) ? 'true' : 'false' ); ?> header-navigation-layout-fill-stretch-<?php echo ( kadence()->option( 'primary_navigation_fill_stretch' ) ? 'true' : 'false' ); ?>" data-section="kadence_customizer_primary_navigation">
	<?php
	/**
	 * Kadence Primary Navigation
	 *
	 * Hooked Kadence\primary_navigation
	 */
	do_action( 'kadence_primary_navigation' );
	?>
</div><!-- data-section="primary_navigation" -->
