<?php
/*
 * move the slider to occur after the nav hooks
 */
function gconnect_serenity_head() {
	if( function_exists( 'serenity_include_slider' ) ) {
		remove_action('genesis_after_header', 'serenity_include_slider');
		add_action('genesis_after_header', 'serenity_include_slider', 21);
	}
}
add_action( 'genesis_meta', 'gconnect_serenity_head', 91);
