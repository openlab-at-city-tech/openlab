<?php

/**
 * MU Plugins enqueues
 * Keeping this all in once place
 */
function openlab_mu_enqueue() {
    //adding smooth scroll
    wp_register_script('smoothscroll-js', plugins_url('js', __FILE__) . '/jquery-smooth-scroll/jquery.smooth-scroll.min.js', array('jquery'));
    wp_enqueue_script('smoothscroll-js');
    wp_register_script('select-js', plugins_url('js', __FILE__) . '/jquery-custom-select/jquery.customSelect.min.js', array('jquery'));
    wp_enqueue_script('select-js');
    wp_register_script('openlab-search-js', plugins_url('js', __FILE__) . '/openlab/openlab.search.js', array('jquery'));
    wp_enqueue_script('openlab-search-js');
}

add_action('wp_enqueue_scripts', 'openlab_mu_enqueue');
add_action('admin_enqueue_scripts', 'openlab_mu_enqueue');
