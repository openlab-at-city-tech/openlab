<?php
/**
 * Template part for displaying the header branding/logo
 *
 * @package kadence
 */

namespace Kadence;

?>
<div class="site-header-item site-header-focus-item" data-section="title_tagline">
	<?php
	/**
	 * Kadence Site Branding
	 *
	 * Hooked Kadence\site_branding
	 */
	do_action( 'kadence_site_branding' );
	?>
</div><!-- data-section="title_tagline" -->
