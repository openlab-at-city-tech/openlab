<?php

/**
 * wp-admin custom styles.
 *
 * If we get more of these, they should probably be moved to a real stylesheet.
 */

function openlab_admin_styles() {
	?>
<style type="text/css">
@media screen and ( max-width: 782px ) {
	/* My Sites */
	body.my-sites-php table.widefat td {
		width: 94%;
		float: left;
		padding: 8px 3%;
	}

	body.my-sites-php table > tbody > :nth-child(odd),
	.alternate {
		background-color: inherit;
	}

	body.my-sites-php table > tbody > tr > :nth-child(odd) {
		background-color: #f9f9f9;
	}
}
</style>
	<?php
}
add_action( 'admin_head', 'openlab_admin_styles', 100 );
