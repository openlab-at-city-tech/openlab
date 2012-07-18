<?php

add_action('wp_print_styles', 'digressit_default_stylesheets', 100);
add_action('init', 'digressit_default_lightboxes', 100);


function digressit_default_stylesheets(){
	wp_register_style('digressit.default', get_template_directory_uri()."/style.css");
	wp_enqueue_style('digressit.default');
}

function digressit_default_lightboxes(){
	add_action('add_lightbox', 'lightbox_login');
	add_action('add_lightbox', 'lightbox_register');
	add_action('add_lightbox', 'lightbox_site_register');
	add_action('add_lightbox', 'lightbox_registering');
	add_action('add_lightbox', 'lightbox_generic_response');
}
?>