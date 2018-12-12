<?php
/**
 * Post footer template.
 *
 * This only shows up by default if:
 *   1. The Creative Commons License widget is inactive; or if:
 *   2. The Creative Commons License widget is active and the post license is
 *      different than the default site license.
 *
 * @since 0.1.0
 */
?>

<div class="entry-meta entry-meta-creative-commons">

	<?php cac_cc_license_link( array( 'use_logo' => true ) ); ?>

	<p><?php printf( __( 'This entry is licensed under a Creative Commons %s license.', 'cac-creative-commons' ), cac_cc_get_license_link() ); ?></p>

</div>