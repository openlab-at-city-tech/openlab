<?php
/**
 * Koyel Theme Customizer
 *
 * @package Koyel
 */

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function koyel_blog_customize_register( $wp_customize ) {
	// Add koyel options section
	$wp_customize->add_section('koyel_blog_options', array(
		'title'          => __('Social Options', 'koyel-blog'),
		'capability'     => 'edit_theme_options',
		'description'    => __('Add social section options', 'koyel-blog'),
		'priority'       => 20,

	));

	// Facebook Url
    $wp_customize->add_setting('fb_url', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('fa_url_control', array(
        'label'      => __('Facebook Url', 'koyel-blog'),
        'description'=> __('Type your facebook profile link.', 'koyel-blog'),
        'section'    => 'koyel_blog_options',
        'settings'   => 'fb_url',
        'type'       => 'url',
    ));

	// Twitter Url
    $wp_customize->add_setting('tw_url', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('tw_url_control', array(
        'label'      => __('Twitter Url', 'koyel-blog'),
        'description'=> __('Type your twitter profile link.', 'koyel-blog'),
        'section'    => 'koyel_blog_options',
        'settings'   => 'tw_url',
        'type'       => 'url',
    ));

	// Linkedin Url
    $wp_customize->add_setting('link_url', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('link_url_control', array(
        'label'      => __('Linkedin Url', 'koyel-blog'),
        'description'=> __('Type your linkedin profile link.', 'koyel-blog'),
        'section'    => 'koyel_blog_options',
        'settings'   => 'link_url',
        'type'       => 'url',
    ));

	// instagram Url
    $wp_customize->add_setting('instagram_url', array(
        'capability'     => 'edit_theme_options',
        'type'           => 'theme_mod',
        'sanitize_callback' => 'esc_url_raw',
        'transport' => 'refresh',
    ));
    $wp_customize->add_control('instagram_url_control', array(
        'label'      => __('Instagram Url', 'koyel-blog'),
        'description'=> __('Type your instagram profile link.', 'koyel-blog'),
        'section'    => 'koyel_blog_options',
        'settings'   => 'instagram_url',
        'type'       => 'url',
    ));
}
add_action( 'customize_register', 'koyel_blog_customize_register' );