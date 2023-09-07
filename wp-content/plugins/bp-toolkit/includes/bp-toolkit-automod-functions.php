<?php

/**
 * Automod functionality for Block, Suspend, Report for BuddyPress
 *
 */
/**
 * Return true if automod is enabled for the activity type.
 *
 * @since 3.0.0
 *
 */
function bptk_is_automod_enabled( $activity_type )
{
    
    if ( $activity_type == 'member' ) {
        $options = get_option( 'report_user_section' );
        $enabled = ( isset( $options['bptk_report_user_toggle_automod'] ) && $options['bptk_report_user_toggle_automod'] == "on" ? true : false );
    } else {
        $enabled = false;
    }
    
    return $enabled;
}

/**
 * Return true if automod is enabled for the activity type.
 *
 * @since 3.0.0
 *
 */
function bptk_get_automod_threshold( $activity_type )
{
    
    if ( $activity_type == 'member' ) {
        $options = get_option( 'report_user_section' );
        $enabled = ( isset( $options['bptk_report_user_automod_threshold'] ) ? $options['bptk_report_user_automod_threshold'] : false );
    } else {
        $enabled = false;
    }
    
    return $enabled;
}

/**
 * Handles automod behaviour for new or updated reports.
 *
 * @since 3.0.0
 *
 */
function bptk_do_automod( $post_id )
{
    $activity_type = get_post_meta( $post_id, '_bptk_activity_type', true );
    $automod_enabled = bptk_is_automod_enabled( $activity_type );
    // Bail if automod was never enabled
    
    if ( $automod_enabled == false ) {
        update_post_meta( $post_id, 'automod_status', 'Automod disabled for this item type' );
        return;
    }
    
    $item_id = get_post_meta( $post_id, '_bptk_item_id', true );
    $total_reports = bptk_substantiated_reports_per_item( $item_id );
    $threshold = bptk_get_automod_threshold( $activity_type );
    if ( $threshold == false ) {
        return;
    }
    
    if ( $total_reports >= $threshold ) {
        bptk_moderate_activity( $item_id, $activity_type, $post_id );
        update_post_meta( $post_id, 'automod_status', 'Automod triggered' );
    } else {
        update_post_meta( $post_id, 'automod_status', 'Automod enabled, but below threshold' );
    }

}

/**
 * Moderate users, activities or anything else.
 *
 * @since 3.0.0
 *
 */
function bptk_moderate_activity( $item_id, $activity_type, $post_id = null )
{
    $options = get_option( 'report_emails_section' );
    // Check new auto-mod admin emails are enabled. This will also stop admins receiving emails when they have done the moderation
    if ( $post_id != null && (isset( $options['bptk_report_emails_automod_admin'] ) && $options['bptk_report_emails_automod_admin'] == "on") ) {
        bptk_send_email(
            'bptk-admin-item-automod',
            $item_id,
            $activity_type,
            $post_id
        );
    }
    // If user moderation notifications are enabled, send mail
    if ( $post_id != null && (isset( $options['bptk_report_emails_automod_user'] ) && $options['bptk_report_emails_automod_user'] == "on") ) {
        if ( $activity_type != 'member' ) {
            bptk_send_email(
                'bptk-user-item-hidden',
                $item_id,
                $activity_type,
                $post_id
            );
        }
    }
    // If reporter moderation notifications are enabled, send mail
    if ( $post_id != null && (isset( $options['bptk_report_emails_automod_reporter'] ) && $options['bptk_report_emails_automod_reporter'] == "on") ) {
        bptk_send_email(
            'bptk-reporter-item-hidden',
            $item_id,
            $activity_type,
            $post_id
        );
    }
    if ( $activity_type == 'member' ) {
        bptk_suspend_member( $item_id );
    }
}

/**
 * Unmoderate users, activities or anything else.
 *
 * @since 3.0.0
 *
 */
function bptk_unmoderate_activity( $item_id, $activity_type, $post_id = null )
{
    $options = get_option( 'report_emails_section' );
    // If user restoration notifications are enabled, send mail
    if ( $post_id != null && (isset( $options['bptk_report_emails_restored_user'] ) && $options['bptk_report_emails_restored_user'] == "on") ) {
        bptk_send_email(
            'bptk-user-item-restored',
            $item_id,
            $activity_type,
            $post_id
        );
    }
    // If reporter restoration notifications are enabled, send mail
    if ( $post_id != null && (isset( $options['bptk_report_emails_restored_reporter'] ) && $options['bptk_report_emails_restored_reporter'] == "on") ) {
        bptk_send_email(
            'bptk-reporter-item-restored',
            $item_id,
            $activity_type,
            $post_id
        );
    }
    if ( $activity_type == 'member' ) {
        // Remove the item from the moderation list
        bptk_unsuspend_member( $item_id );
    }
}

/**
 * Handler to add to list of moderated activities.
 *
 * @since 3.0.0
 *
 */
function bptk_add_to_moderated_list( $item_id, $activity_type )
{
    $option = 'bptk_moderated_' . $activity_type . '_list';
    $exists = get_option( $option );
    
    if ( $exists ) {
        $key = array_search( $item_id, $exists );
        
        if ( !$key ) {
            $exists[] = $item_id;
            update_option( $option, $exists );
        }
    
    } else {
        update_option( $option, array( $item_id ) );
    }

}

/**
 * Handler to remove from list of moderated activities.
 *
 * @since 3.0.0
 *
 */
function bptk_remove_from_moderated_list( $item_id, $activity_type )
{
    bp_activity_update_meta( $item_id, 'last_unmoderated', current_time( 'mysql' ) );
    bp_activity_update_meta( $item_id, 'last_unmoderated_by', get_current_user_id() );
    $option = 'bptk_moderated_' . $activity_type . '_list';
    $exists = get_option( $option );
    
    if ( $exists ) {
        $key = array_search( $item_id, $exists );
        unset( $exists[$key] );
        update_option( $option, $exists );
    } else {
        return;
    }

}

/**
 * Get moderated list.
 */
function bptk_get_moderated_list( $activity_type )
{
    $option = 'bptk_moderated_' . $activity_type . '_list';
    $exists = ( get_option( $option ) ? get_option( $option ) : array() );
    return $exists;
}

/**
 * Check if a particular item is moderated.
 *
 * @param $item_id
 * @param $activity_type
 *
 * @return bool
 */
function bptk_is_item_moderated( $item_id, $activity_type )
{
    $list = bptk_get_moderated_list( $activity_type );
    
    if ( $list && in_array( $item_id, $list ) ) {
        return true;
    } else {
        return false;
    }

}
