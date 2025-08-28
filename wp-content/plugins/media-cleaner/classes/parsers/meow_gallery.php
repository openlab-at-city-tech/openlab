<?php

add_action('wpmc_scan_post', 'wpmc_scan_html_meow_gallery', 10, 2);


function wpmc_scan_html_meow_gallery($html, $id)
{
    global $wpmc;

    $posts_images_urls = array();
    $posts_images_ids = array();

    $matches = array();
    $pattern = get_shortcode_regex( ['gallery', 'meow-gallery'] );
    preg_match_all( '/'. $pattern .'/s', $html, $matches );

    global $wpmgl;
    if ( !isset( $wpmgl ) ) {
       return; // Meow Gallery is not active
    }

    foreach( $matches[3] as $index => $attrs_string ) {
        
        $attributes = shortcode_parse_atts( $attrs_string );
        $inner_html = $wpmgl->gallery( $attributes );

        $urls = $wpmc->get_urls_from_html( $inner_html );

        $posts_images_urls = array_merge( $posts_images_urls, $urls );
    }

    $wpmc->add_reference_url($posts_images_urls, 'Meow Gallery (URL)', $id);
}