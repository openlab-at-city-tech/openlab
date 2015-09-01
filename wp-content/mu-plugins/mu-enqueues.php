<?php

/**
 * MU Plugins enqueues
 * Keeping this all in once place
 */

function openlab_mu_enqueue() {

    //google plus one
    wp_register_script('google-plus-one', 'https://apis.google.com/js/plusone.js');
    wp_enqueue_script('google-plus-one');

    //adding smooth scroll
    wp_register_script('smoothscroll-js', plugins_url('js', __FILE__) . '/jquery-smooth-scroll/jquery.smooth-scroll.min.js', array('jquery'), '', true);
    wp_enqueue_script('smoothscroll-js');
    wp_register_script('select-js', plugins_url('js', __FILE__) . '/jquery-custom-select/jquery.customSelect.min.js', array('jquery'), '', true);
    wp_enqueue_script('select-js');
    wp_register_script('hyphenator-js', plugins_url('js', __FILE__) . '/hyphenator/hyphenator.js', array('jquery'), '', true);
    wp_enqueue_script('hyphenator-js');
    wp_register_script('openlab-search-js', plugins_url('js', __FILE__) . '/openlab/openlab.search.js', array('jquery'), '', true);
    wp_enqueue_script('openlab-search-js');
    wp_register_script('openlab-nav-js', plugins_url('js', __FILE__) . '/openlab/openlab.nav.js', array('jquery'), '', true);
    wp_enqueue_script('openlab-nav-js');
    wp_register_script('openlab-theme-fixes-js', plugins_url('js', __FILE__) . '/openlab/openlab.theme.fixes.js', array('jquery','twentyfourteen-script'), '', true);
    wp_enqueue_script('openlab-theme-fixes-js');
}

add_action('wp_enqueue_scripts', 'openlab_mu_enqueue', 9);
add_action('admin_enqueue_scripts', 'openlab_mu_enqueue');

function openlab_script_additional_attributes($good_protocol_url, $original_url, $_context) {

    if (false !== strpos($original_url, 'plusone.js')) {
        remove_filter('clean_url', 'openlab_script_additional_attributes', 10, 3);
        $url_parts = parse_url($good_protocol_url);
        return $url_parts['scheme'] . '://' . $url_parts['host'] . $url_parts['path'] . "' async defer='defer";
    }
    return $good_protocol_url;
}

add_filter('clean_url', 'openlab_script_additional_attributes', 10, 3);
