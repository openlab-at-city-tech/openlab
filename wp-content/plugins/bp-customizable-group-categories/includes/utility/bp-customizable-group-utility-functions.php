<?php

/*
 * BP Customizable Group Categories utility functions
 * Fun for the outside world
 */

function bpcgc_get_terms_by_group_type($group_type) {
    global $wpdb;

    $terms_out = array();

    if (!$group_type) {
        return false;
    }

    $key = 'bpcgc_group_' . $group_type;

    $query = $wpdb->prepare("SELECT t.* FROM $wpdb->terms t INNER JOIN $wpdb->termmeta tm on tm.term_id = t.term_id WHERE tm.meta_key=%s", $key);
    $terms_out = $wpdb->get_results($query);

    return $terms_out;
}

function bpcgc_get_group_selected_terms($group_id = 0, $conditional = false) {
    if (empty($group_id)) {
        $group_id = bp_get_new_group_id() ? bp_get_new_group_id() : bp_get_current_group_id();
    }

    $group_terms = BPCGC_Groups_Terms::get_object_terms($group_id);
   
    if($conditional && empty($group_terms)){
        return false;
    }
    
    return $group_terms;
}
