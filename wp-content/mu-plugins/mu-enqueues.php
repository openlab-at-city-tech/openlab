<?php

/**
 * MU Plugins enqueues
 * Keeping this all in once place
 */
/* * minify library* */
require_once( WPMU_PLUGIN_DIR . '/minify/src/Converter.php' );
require_once( WPMU_PLUGIN_DIR . '/minify/src/Minify.php' );
require_once( WPMU_PLUGIN_DIR . '/minify/src/Exception.php' );
require_once( WPMU_PLUGIN_DIR . '/minify/src/JS.php' );
require_once( WPMU_PLUGIN_DIR . '/minify/src/CSS.php' );

function openlab_mu_enqueue() {

    //google plus one
    wp_register_script('google-plus-one', 'https://apis.google.com/js/plusone.js');
    wp_enqueue_script('google-plus-one');

    //adding smooth scroll
    /*wp_register_script('smoothscroll-js', plugins_url('js', __FILE__) . '/jquery-smooth-scroll/jquery.smooth-scroll.min.js', array('jquery'));
    wp_enqueue_script('smoothscroll-js');
    wp_register_script('select-js', plugins_url('js', __FILE__) . '/jquery-custom-select/jquery.customSelect.min.js', array('jquery'));
    wp_enqueue_script('select-js');
    wp_register_script('openlab-search-js', plugins_url('js', __FILE__) . '/openlab/openlab.search.js', array('jquery'));
    wp_enqueue_script('openlab-search-js');
    wp_register_script('openlab-nav-js', plugins_url('js', __FILE__) . '/openlab/openlab.nav.js', array('jquery'));
    wp_enqueue_script('openlab-nav-js');*/

    $result_path = WPMU_PLUGIN_DIR . '/js/openlab/openlab.nav.min.js';

    $smoothscroll = WPMU_PLUGIN_DIR . '/js/jquery-smooth-scroll/jquery.smooth-scroll.min.js';
    $select = WPMU_PLUGIN_DIR . '/js/jquery-custom-select/jquery.customSelect.min.js';
    $openlab_search = WPMU_PLUGIN_DIR . '/js/openlab/openlab.search.js';
    $openlab_nav = WPMU_PLUGIN_DIR . '/js/openlab/openlab.nav.js';

    $minifier = new MatthiasMullie\Minify\JS($smoothscroll, $select, $openlab_search, $openlab_nav);
    $minifier->minify($result_path);

    wp_register_script('openlab-nav-js', plugins_url('js', __FILE__) . '/openlab/openlab.nav.min.js', array('jquery'));
    wp_enqueue_script('openlab-nav-js');
}

add_action('wp_enqueue_scripts', 'openlab_mu_enqueue',9);
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
