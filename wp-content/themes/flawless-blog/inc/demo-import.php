<?php

function flawless_blog_intro_text( $default_text ) {
	$default_text .= sprintf(
		'<p class="demo-data-download-link">%1$s <a href="%2$s" target="_blank">%3$s</a></p>',
		esc_html__( 'Demo content files for Flawless Blog Theme.', 'flawless-blog' ),
		esc_url( 'https://demo.adorethemes.com/documentations/docs/flawless-blog/demo-data/' ),
		esc_html__( 'Click here for Demo File download', 'flawless-blog' )
	);

	return $default_text;
}
add_filter( 'pt-ocdi/plugin_intro_text', 'flawless_blog_intro_text' );

/**
 * OCDI after import.
 */
function flawless_blog_after_import_setup() {
	// Assign menus to their locations.
	$primary_menu = get_term_by( 'name', 'Primary Menu', 'nav_menu' );

	set_theme_mod(
		'nav_menu_locations',
		array(
			'primary' => $primary_menu->term_id,
		)
	);

}
add_action( 'ocdi/after_import', 'flawless_blog_after_import_setup' );
