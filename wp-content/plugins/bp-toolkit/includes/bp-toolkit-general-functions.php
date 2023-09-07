<?php

/**
 * General functionality for Block, Suspend, Report for BuddyPress
 *
 */
/**
 * Gets number of reports made by user.
 *
 * @since 3.0.0
 *
 */
function bptk_reports_made_by_user( $user_id )
{
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_key'    => '_bptk_reported_by',
        'meta_value'  => $user_id,
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;
    return $count;
}

/**
 * Gets number of reports made about a user's activities.
 *
 * @since 3.0.0
 *
 */
function bptk_reports_about_user( $user_id )
{
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_query'  => array( array(
        'key'   => '_bptk_member_reported',
        'value' => $user_id,
    ) ),
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;
    return $count;
}

/**
 * Gets number of substantiated reports made about a user's activities.
 *
 * @since 3.0.0
 *
 */
function bptk_substantiated_reports_about_user( $user_id )
{
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_query'  => array( array(
        'key'   => '_bptk_member_reported',
        'value' => $user_id,
    ), array(
        'key'   => 'is_upheld',
        'value' => 1,
    ) ),
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;
    return $count;
}

/**
 * Gets number of reports made about an item.
 *
 * @since 3.0.0
 *
 */
function bptk_reports_per_item( $item_id )
{
    if ( !isset( $item_id ) || $item_id == '' ) {
        return;
    }
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_query'  => array( array(
        'key'   => '_bptk_item_id',
        'value' => $item_id,
    ) ),
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;
    return $count;
}

/**
 * Gets number of substantiated reports made about an item.
 *
 * @since 3.0.0
 *
 */
function bptk_substantiated_reports_per_item( $item_id )
{
    if ( !isset( $item_id ) || $item_id == '' ) {
        return;
    }
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_query'  => array( array(
        'key'   => '_bptk_item_id',
        'value' => $item_id,
    ), array(
        'key'   => 'is_upheld',
        'value' => 1,
    ) ),
    );
    $query = new WP_Query( $args );
    $count = $query->found_posts;
    return $count;
}

/**
 * Do ordinals.
 *
 * @since 3.0.0
 *
 */
function bptk_ordinal( $number )
{
    $ends = array(
        'th',
        'st',
        'nd',
        'rd',
        'th',
        'th',
        'th',
        'th',
        'th',
        'th'
    );
    
    if ( $number % 100 >= 11 && $number % 100 <= 13 ) {
        return $number . 'th';
    } else {
        return $number . $ends[$number % 10];
    }

}

/**
 * Suspend member.
 *
 * @param $member_id The User ID
 *
 * @since 3.0.0
 *
 */
function bptk_suspend_member( $member_id )
{
    // Bail if no member id.
    if ( empty($member_id) ) {
        return;
    }
    // Check if already suspended
    $status = get_user_meta( $member_id, 'bptk_suspend', true );
    if ( $status == 1 ) {
        return;
    }
    // Update meta
    update_user_meta( $member_id, 'bptk_suspend', 1 );
    // Update moderated list
    bptk_add_to_moderated_list( $member_id, 'member' );
    // If on the front-end, display a message
    bp_core_add_message( __( 'User successfully suspended', 'bp-toolkit' ) );
    $options = get_option( 'report_emails_section' );
    // If user suspension notifications are enabled, send mail
    if ( isset( $options['bptk_report_emails_automod_user'] ) && $options['bptk_report_emails_automod_user'] == "on" ) {
        bptk_send_email(
            'bptk-user-suspended',
            $member_id,
            'member',
            'suspensions'
        );
    }
    if ( bptk_prevent_login_enabled() ) {
        // Get the user's sessions object and destroy all sessions.
        WP_Session_Tokens::get_instance( $member_id )->destroy_all();
    }
}

/**
 * Unsuspend member.
 *
 * @param $member_id The User ID
 *
 * @since 3.0.0
 *
 */
function bptk_unsuspend_member( $member_id )
{
    // Bail if no member id.
    if ( empty($member_id) ) {
        return;
    }
    // Check if already suspended
    $status = get_user_meta( $member_id, 'bptk_suspend', true );
    if ( $status == 0 || empty($status) ) {
        return;
    }
    // Update meta
    update_user_meta( $member_id, 'bptk_suspend', 0 );
    // Update moderated list
    bptk_remove_from_moderated_list( $member_id, 'member' );
    // If on the front-end, display a message
    // if ( !is_admin() ) {
    bp_core_add_message( __( 'User successfully unsuspended', 'bp-toolkit' ) );
    // }
    $options = get_option( 'report_emails_section' );
    // If user unsuspension notifications are enabled, send mail
    if ( isset( $options['bptk_report_emails_restored_user'] ) && $options['bptk_report_emails_restored_user'] == "on" ) {
        bptk_send_email(
            'bptk-user-item-restored',
            $member_id,
            'member',
            'suspensions'
        );
    }
}

/**
 * Get a list of users a given user_id has blocked
 */
function bptk_get_blocked_users( $user_id = null )
{
    // If no user ID provided, use logged in user
    if ( $user_id === null ) {
        $user_id = bp_current_user_id();
    }
    $list = get_user_meta( $user_id, 'bptk_block', true );
    if ( empty($list) ) {
        $list = array();
    }
    $_list = apply_filters( 'get_blocked_users', $list, $user_id );
    return array_filter( $_list );
}

/**
 * Block user
 */
function bptk_block_user( int $blocked_user, $blocking_user = null )
{
    // If no blocking user provided, use logged in user
    if ( $blocking_user == null ) {
        $blocking_user = bp_current_user_id();
    }
    // You can't block admins or mods, so bail...
    
    if ( user_can( $blocked_user, BPTK_ADMIN_CAP ) ) {
        bp_core_add_message( __( 'You cannot block administrators or moderators', 'bp-toolkit' ), 'error' );
        return;
    }
    
    // If not already in block list, add to the blocker's list
    $block_list = get_user_meta( $blocking_user, 'bptk_block', true );
    
    if ( $block_list ) {
        $key = array_search( $blocked_user, $block_list );
        
        if ( $key === false ) {
            $block_list[] = $blocked_user;
            $added_to_block_list = update_user_meta( $blocking_user, 'bptk_block', $block_list );
        }
    
    } else {
        $list = array();
        $list[] = $blocked_user;
        $added_to_block_list = update_user_meta( $blocking_user, 'bptk_block', $list );
    }
    
    // Add to user's blocked by list
    $blocked_by = get_user_meta( $blocked_user, 'bptk_blocked_by', true );
    
    if ( $blocked_by ) {
        $key = array_search( $blocking_user, $blocked_by );
        
        if ( $key === false ) {
            $blocked_by[] = $blocking_user;
            $added_to_blocked_by_list = update_user_meta( $blocked_user, 'bptk_blocked_by', $blocked_by );
        }
    
    } else {
        $list = array();
        $list[] = $blocking_user;
        $added_to_blocked_by_list = update_user_meta( $blocked_user, 'bptk_blocked_by', $list );
    }
    
    
    if ( $added_to_block_list == true && $added_to_blocked_by_list == true ) {
        do_action( 'bptk_user_blocked', $blocked_user, $blocking_user );
        bp_core_add_message( __( 'User successfully blocked.', 'bp-toolkit' ) );
    }

}

/**
 * Unblock User
 */
function bptk_unblock_user( int $blocked_user, $unblocking_user = null )
{
    // If no unblocking user provided, use logged in user
    if ( $unblocking_user == null ) {
        $unblocking_user = bp_current_user_id();
    }
    // Remove from user's block list
    $block_list = get_user_meta( $unblocking_user, 'bptk_block', true );
    
    if ( $block_list ) {
        $key = array_search( $blocked_user, $block_list );
        
        if ( $key === false ) {
            // User was never in the block list. This shouldn't happen in the wild.
            return;
        } else {
            unset( $block_list[$key] );
            $removed_from_block_list = update_user_meta( $unblocking_user, 'bptk_block', $block_list );
        }
        
        if ( empty($block_list) ) {
            delete_user_meta( $unblocking_user, 'bptk_block' );
        }
    }
    
    // Remove from user's blocked by list
    $blocked_by = get_user_meta( $blocked_user, 'bptk_blocked_by', true );
    
    if ( $blocked_by ) {
        $key = array_search( $unblocking_user, $blocked_by );
        
        if ( $key === false ) {
            return;
        } else {
            unset( $blocked_by[$key] );
            $removed_from_blocked_by_list = update_user_meta( $blocked_user, 'bptk_blocked_by', $blocked_by );
        }
        
        if ( empty($blocked_by) ) {
            delete_user_meta( $blocked_user, 'bptk_blocked_by' );
        }
    }
    
    
    if ( $removed_from_block_list == true && $removed_from_blocked_by_list == true ) {
        do_action( 'bptk_user_unblocked', $blocked_user, $unblocking_user );
        bp_core_add_message( __( 'User successfully unblocked.', 'bp-toolkit' ) );
    }

}

/**
 * Get any integrations.
 *
 * @since 3.0.0
 *
 */
function get_integrations()
{
    return false;
}

/**
 * Returns all unread reports.
 *
 * @since 3.0.0
 */
function bptk_get_unread_reports()
{
    $args = array(
        'post_type'   => 'report',
        'numberposts' => -1,
        'meta_query'  => array( array(
        'key'     => 'is_read',
        'value'   => '0',
        'compare' => '=',
    ) ),
    );
    $unread_reports = get_posts( $args );
    return $unread_reports;
}

/**
 * Display a bptk help tip.
 *
 * @param string $tip Help tip text.
 * @param bool $allow_html Allow sanitized HTML if true or escape.
 *
 * @return string
 *
 * @since  3.1.0
 *
 */
function bptk_help_tip( $tip, $allow_html = false )
{
    
    if ( $allow_html ) {
        $tip = bptk_sanitize_tooltip( $tip );
    } else {
        $tip = esc_attr( $tip );
    }
    
    return '<span class="bptk-help-tip" data-tip="' . $tip . '"></span>';
}

/**
 * Sanitize a string destined to be a tooltip.
 *
 * Tooltips are encoded with htmlspecialchars to prevent XSS. Should not be used in conjunction with esc_attr()
 *
 * @param string $var Data to sanitize.
 *
 * @return string
 *
 * @since  3.1.0
 *
 */
function bptk_sanitize_tooltip( $var )
{
    return htmlspecialchars( wp_kses( html_entity_decode( $var ), array(
        'br'     => array(),
        'em'     => array(),
        'strong' => array(),
        'small'  => array(),
        'span'   => array(),
        'ul'     => array(),
        'li'     => array(),
        'ol'     => array(),
        'p'      => array(),
    ) ) );
}

/**
 * Checks to see if user is suspended.
 *
 * @param int $user_id User ID.
 *
 * @return boolean
 *
 * @since  3.1.0
 *
 */
function is_suspended( $user_id )
{
    $status = get_user_meta( $user_id, 'bptk_suspend', true );
    
    if ( $status == 0 || empty($status) ) {
        return false;
    } else {
        return true;
    }

}

/**
 * Checks to see if user has been blacklisted.
 *
 * @param int $user_id User ID.
 *
 * @return boolean
 *
 * @since  3.1.0
 *
 */
function is_blacklisted( $user_id )
{
    $options = get_option( 'report_section' );
    
    if ( isset( $options['bptk_report_blacklist'] ) ) {
        // Convert string to array
        $blacklist = explode( ',', $options['bptk_report_blacklist'] );
        // Search the array for current user and return result
        
        if ( array_search( $user_id, $blacklist ) !== false ) {
            return true;
        } else {
            return false;
        }
    
    }

}

/**
 * Add member to blacklist.
 *
 * @param $member_id The User ID
 *
 * @since 3.1.0
 *
 */
function bptk_blacklist_member( $user_id )
{
    // Bail if no user id.
    if ( empty($user_id) ) {
        return;
    }
    // Check if already blacklisted
    
    if ( is_blacklisted( $user_id ) ) {
        return;
    } else {
        // Clear
        wp_cache_delete( 'alloptions', 'options' );
        $options = get_option( 'report_section' );
        
        if ( isset( $options['bptk_report_blacklist'] ) ) {
            // Convert string to array
            $blacklist = explode( ',', $options['bptk_report_blacklist'] );
            // Add user to array
            $blacklist[] = $user_id;
            // Convert back to string
            $comma_separated = implode( ",", $blacklist );
            $options['bptk_report_blacklist'] = $comma_separated;
            // Save
            update_option( 'report_section', $options );
        }
    
    }

}

/**
 * Remove member from blacklist.
 *
 * @param $member_id The User ID
 *
 * @since 3.1.0
 *
 */
function bptk_unblacklist_member( $user_id )
{
    // Bail if no user id.
    if ( empty($user_id) ) {
        return;
    }
    // Clear
    wp_cache_delete( 'alloptions', 'options' );
    $options = get_option( 'report_section' );
    
    if ( isset( $options['bptk_report_blacklist'] ) ) {
        // Convert string to array
        $blacklist = explode( ',', $options['bptk_report_blacklist'] );
        // Search the array for current user and return result
        $key = array_search( $user_id, $blacklist );
        // If present, remove from array
        
        if ( $key !== false ) {
            unset( $blacklist[$key] );
        } else {
            return;
        }
        
        // Convert back to string
        $comma_separated = implode( ",", $blacklist );
        $options['bptk_report_blacklist'] = $comma_separated;
        // Save
        update_option( 'report_section', $options );
    }

}

/**
 * Checks to see if report has been upheld.
 *
 * @param int $post_id Post ID.
 *
 * @return boolean
 *
 * @since  3.1.0
 *
 */
function is_upheld( $post_id )
{
    $status = get_post_meta( $post_id, 'is_upheld', true );
    
    if ( $status == 0 || empty($status) ) {
        return false;
    } else {
        return true;
    }

}

/**
 * Checks to see if report item has been moderated.
 *
 * @param int $post_id Post ID.
 *
 * @return boolean
 *
 * @since  3.1.0
 *
 */
function is_moderated( $post_id )
{
    global  $post ;
    $option = 'bptk_moderated_' . $post->_bptk_activity_type . '_list';
    $exists = get_option( $option );
    
    if ( $exists && in_array( $post->_bptk_item_id, $exists ) ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Checks to see if BuddyBoss is the theme.
 *
 * @return boolean
 *
 * @since  3.1.0
 *
 */
function bptk_is_buddyboss()
{
    $theme = get_stylesheet();
    
    if ( $theme == 'buddyboss-theme' || $theme == 'buddyboss-theme-child' ) {
        return true;
    } elseif ( in_array( 'buddyboss-platform/bp-loader.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        return true;
    } else {
        return false;
    }

}

/**
 * Marks a report as upheld.
 *
 * @param $post_id The ID of the Post.
 *
 * @since  3.1.0
 *
 */
function bptk_set_upheld( $post_id )
{
    if ( !$post_id ) {
        return;
    }
    update_post_meta( $post_id, 'is_upheld', 1 );
}

/**
 * Marks a report as not upheld.
 *
 * @param $post_id The ID of the Post.
 *
 * @since  3.1.0
 *
 */
function bptk_remove_upheld( $post_id )
{
    if ( !$post_id ) {
        return;
    }
    update_post_meta( $post_id, 'is_upheld', 0 );
}

function bptk_prevent_login_enabled()
{
    return true;
}
