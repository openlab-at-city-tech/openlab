<?php
/*
Plugin Name: Gravity Forms Change Entry Creator Add-on
Plugin URI: http://katz.co/gravity-forms-addons/
Description: This simple addon allows users with Entry-editing capabilities to change who a <a href="http://katz.si/gravityforms" rel="nofollow">Gravity Forms</a> lead is assigned to.
Author: Katz Web Services, Inc.
Version: 3.5.4
Author URI: http://www.katzwebservices.com

Copyright 2014 Katz Web Services, Inc. (email: info@katzwebservices.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/


add_action("gform_entry_info", 'kws_gf_change_entry_creator_form', 10, 2);

// If this is already custom-added from katz.co
if(!function_exists('kws_gf_change_entry_creator_form')) {
function kws_gf_change_entry_creator_form($form_id, $lead) {
    if(GFCommon::current_user_can_any("gravityforms_edit_entries")) {
    
        //@since 3.5.3 - filter possible creators
        $users = apply_filters( 'kws_gf_entry_creator_users', '', $form_id );
        
        if( empty( $users ) ) {
	        $users = get_users();
		}
		
        $output = '<label for="change_created_by">';
        $output .= __('Change Entry Creator:', 'gravity-forms-addons');
        $output .= '</label>
        <select name="created_by" id="change_created_by" class="widefat">';
        foreach($users as $user) {
            $output .= '<option value="'. $user->ID .'"'. selected( $lead['created_by'], $user->ID, false ).'>'.$user->display_name.' ('.$user->user_nicename.')</option>';
        }
        $output .= '</select>';
        $output .= '<input name="originally_created_by" value="'.$lead['created_by'].'" type="hidden" />';
        echo $output;
    }
}
} 

add_action("gform_after_update_entry", 'kws_gf_update_entry_creator', 10, 2);
if(!function_exists('kws_gf_update_entry_creator')) {
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
}
