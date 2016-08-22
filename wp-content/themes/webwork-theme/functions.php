<?php

add_action( 'wp_enqueue_scripts', 'twentysixteen_parent_theme_enqueue_styles' );

function twentysixteen_parent_theme_enqueue_styles() {
    wp_enqueue_style( 'twentysixteen-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'twentysixteen-webwork-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( 'twentysixteen-style' )
    );

}
