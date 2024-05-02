<?php
/**
 * Template part for displaying the a button in the mobile header.
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item" data-section="kadence_customizer_mobile_button">
	<?php
	/**
	 * Kadence Mobile Header Button
	 *
	 * Hooked Kadence\mobile_button
	 */
	do_action( 'kadence_mobile_button' );
	?>
</div><!-- data-section="mobile_button" -->
