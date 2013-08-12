<?php
/*
Change Entry Creator Gravity Forms Add-on
This simple addon allows users with Entry-editing capabilities to change who a lead is assigned to.
Version: 1.0
Author URI: http://www.katzwebservices.com
*/

if(function_exists('kws_gf_change_entry_creator_form')) { return; }

add_action("gform_entry_info", 'kws_gf_change_entry_creator_form', 10, 2);
function kws_gf_change_entry_creator_form($form_id, $lead) {
    if(GFCommon::current_user_can_any("gravityforms_edit_entries")) {
        $users = get_users();
        $output = '<label for="change_created_by">';
        $output .= __('Change Entry Creator:', 'gravity-forms-addons');
        $output .= '</label>
        <select name="created_by" id="change_created_by" class="widefat">';
        foreach($users as $user) {
            $output .= '<option value="'.$user->ID.'"'.selected((int)$lead["created_by"] === (int)$user->ID, true, false).'>'.$user->display_name.' ('.$user->user_nicename.')</option>';
        }
        $output .= '</select>';
        $output .= '<input name="originally_created_by" value="'.$lead['created_by'].'" type="hidden" />';
        echo $output;
    }
}

add_action("gform_after_update_entry", 'kws_gf_update_entry_creator', 10, 2);
function kws_gf_update_entry_creator($form, $leadid) {
        global $current_user;

    if(GFCommon::current_user_can_any("gravityforms_edit_entries")) {
        // Update the entry
        $created_by = rgpost('created_by');
        RGFormsModel::update_lead_property($leadid, 'created_by', $created_by);

        // If the creator has changed, let's add a note about who it used to be.
        $originally_created_by = rgpost('originally_created_by');
        if($originally_created_by !== $created_by) {
            $originally_created_by_user_data = get_userdata($originally_created_by);
            $created_by_user_data =  get_userdata($created_by);
            $user_data = get_userdata($current_user->ID);
            RGFormsModel::add_note($leadid, $current_user->ID, $user_data->display_name, sprintf(__('Changed lead creator from %s to %s', 'gravity-forms-addons'), $originally_created_by_user_data->display_name.' (ID #'.$originally_created_by_user_data->ID.')', $created_by_user_data->display_name.' (ID #'.$created_by_user_data->ID.')'));
        }
    }
}
