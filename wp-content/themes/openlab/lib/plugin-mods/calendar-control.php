<?php

/**
 * Calendar control
 * Hooks into Events Organiser and BuddyPress Event Organiser 
 */
/* * *
 * Preventing the creation of dedicated venue pages
 */
function openlab_control_venue_taxonomy($event_category_args) {

    $event_category_args['rewrite'] = false;

    return $event_category_args;
}

add_filter('eventorganiser_register_taxonomy_event-venue', 'openlab_control_venue_taxonomy');

/**
 * Pointing to custom templates in OpenLab theme folder
 * @param type $stack
 * @return type
 */
function openlab_add_eventorganiser_custom_template_folder($stack) {

    $custom_loc = get_stylesheet_directory() . '/event-organiser';

    array_unshift($stack, $custom_loc);

    return $stack;
}

add_filter('eventorganiser_template_stack', 'openlab_add_eventorganiser_custom_template_folder');

function openlab_eventorganiser_no_venue_link($html, $event_id) {

    echo '<pre>' . print_r($html, true) . '</pre>';

    return $html;
}

add_filter('eventorganiser_event_meta_list', 'openlab_eventorganiser_no_venue_link');
