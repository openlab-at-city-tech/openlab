<?php
/**
 * Template part for displaying the header navigation menu
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item site-header-item-navgation-popup-toggle" data-section="kadence_customizer_mobile_trigger">
	<?php
	/**
	 * Kadence Mobile Navigation Popup Toggle
	 *
	 * Hooked Kadence\navigation_popup_toggle
	 */
	do_action( 'kadence_navigation_popup_toggle' );
	?>
</div><!-- data-section="mobile_trigger" -->
