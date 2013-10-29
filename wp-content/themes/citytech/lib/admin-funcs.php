<?php
/**
 * For functionality that hooks into the admin
 * 
 */

//this functionality is currently tabled, but will return in 1.3.3, leaving this code in place for now and commenting out filter actions

/**
 * Help Sidebar has to sets of icons: "Our Support Team" and "ePortfolio Support Team"
 * Add Meta Field checkboxes to Help->Contact-Us page to delinieate icons
 *
 * @param $form_fields array, fields to include in attachment form
 * @param $post object, attachment record in database
 * @return $form_fields, modified form fields
 */
function openlab_attachment_field_image_meta($form_fields, $post) {

    $help_contact_page_id = get_page_by_path('contact-us', OBJECT, 'help')->ID;

    if ($post->post_parent == $help_contact_page_id) {
        
        $support_team_value = get_post_meta($post->ID,'openlab_support_team');
        
        $form_fields['openlab_support_team'] = array(
            'label' => 'Support Team',
            'input' => 'html',
            'html' => ' '.$support_team_value.'<input type="checkbox" name="attachments[' . $post->ID . '][openlab_support_team]" id="openlab_support_team-' . $post->ID . '" value="1" '.($support_team_value == 1 ? 'checked' : '' ).' />',
        );
    }

    return $form_fields;
}

//add_filter('attachment_fields_to_edit', 'openlab_attachment_field_image_meta', 10, 2);

/**
 * Save values of Photographer Name and URL in media uploader
 *
 * @param $post array, the post data for database
 * @param $attachment array, attachment fields from $_POST form
 * @return $post array, modified post data
 */
function openlab_attachment_field_image_meta_save($post, $attachment_data) {

    if (!empty($attachment_data['openlab_support_team']) && $attachment_data['openlab_support_team'] == '1'):
        update_post_meta($post['ID'], 'openlab_support_team', true);
    else:
        delete_post_meta($post['ID'], 'openlab_support_team');
    endif;

    return $post;
}

//add_filter('attachment_fields_to_save', 'openlab_attachment_field_image_meta_save', 10, 2);


