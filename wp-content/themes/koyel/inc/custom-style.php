<?php 
function koyel_custom_css() {
    wp_enqueue_style('koyel-custom', get_template_directory_uri() . '/assets/css/custom-style.css' );
    $header_text_color = get_header_textcolor();
    $koyel_custom_css = '';
    $koyel_custom_css .= '
        .site-title a,
        .site-description,
        .site-title a:hover {
            color: #'.esc_attr( $header_text_color ).' ;
        }
    ';
    wp_add_inline_style( 'koyel-custom', $koyel_custom_css );
}
add_action( 'wp_enqueue_scripts', 'koyel_custom_css' );