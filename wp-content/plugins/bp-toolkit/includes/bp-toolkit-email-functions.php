<?php

/**
 * Email functionality for Block, Suspend, Report for BuddyPress
 *
 */
/**
 * Installs our emails.
 *
 * @since 3.0.0
 */
function bptk_install_emails()
{
    $defaults = array(
        'post_status' => 'publish',
        'post_type'   => bp_get_email_post_type(),
    );
    $emails = bptk_get_emails();
    $descriptions = array(
        'bptk-admin-new-report'       => __( 'Notify admin when a new item is reported.', 'bp-toolkit' ),
        'bptk-user-item-hidden'       => __( 'Notify user when their item is hidden.', 'bp-toolkit' ),
        'bptk-user-suspended'         => __( 'Notify user when they have been suspended.', 'bp-toolkit' ),
        'bptk-user-item-restored'     => __( 'Notify user when their item is restored.', 'bp-toolkit' ),
        'bptk-admin-item-automod'     => __( 'Notify admin when a new item is auto-moderated.', 'bp-toolkit' ),
        'bptk-reporter-item-hidden'   => __( 'Notify reporter when their reported item is hidden.', 'bp-toolkit' ),
        'bptk-reporter-item-restored' => __( 'Notify reporter when their reported item is restored.', 'bp-toolkit' ),
    );
    // Populate the database with our new BuddyPress emails.
    foreach ( $emails as $id => $email ) {
        $post_id = wp_insert_post( bp_parse_args( $email, $defaults, 'install_email_' . $id ) );
        if ( !$post_id ) {
            continue;
        }
        $tt_ids = wp_set_object_terms( $post_id, $id, bp_get_email_tax_type() );
        foreach ( $tt_ids as $tt_id ) {
            $term = get_term_by( 'term_taxonomy_id', (int) $tt_id, bp_get_email_tax_type() );
            wp_update_term( (int) $term->term_id, bp_get_email_tax_type(), array(
                'description' => $descriptions[$id],
            ) );
        }
    }
}

add_action( 'bp_core_install_emails', 'bptk_install_emails' );
/**
 * Get the content of the emails.
 *
 * @since 3.0.0
 */
function bptk_get_emails()
{
    return array(
        'bptk-admin-new-report'       => array(
        'post_title'   => __( '[{{{site.name}}}] New {{bptk_report.item_type}} reported', 'bp-toolkit' ),
        'post_content' => __( "A new {{bptk_report.item_type}} has been reported :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\n<a href=\"{{{bptk_report.reports_screen}}}\">View Reports Screen</a> to take the appropriate action.", 'bp-toolkit' ),
        'post_excerpt' => __( "A new {{bptk_report.item_type}} has been reported :\n\n\"{{bptk_report.item_content}}\"\n\nGo to the reports screen to view it and take any required action: {{{bptk_report.reports_screen}}}", 'bp-toolkit' ),
    ),
        'bptk-admin-item-automod'     => array(
        'post_title'   => __( '[{{{site.name}}}] New {{bptk_report.item_type}} hidden', 'bp-toolkit' ),
        'post_content' => __( "A new {{bptk_report.item_type}} has been subject to auto-moderation as it met the currently set threshold :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\n<a href=\"{{{bptk_report.reports_screen}}}\">View Reports Screen</a> to take the appropriate action.", 'bp-toolkit' ),
        'post_excerpt' => __( "A new {{bptk_report.item_type}} has been subject to auto-moderation :\n\n\"{{bptk_report.item_content}}\"\n\nGo to the reports screen to view it and take any required action: {{{bptk_report.reports_screen}}}", 'bp-toolkit' ),
    ),
        'bptk-user-item-hidden'       => array(
        'post_title'   => __( '[{{{site.name}}}] Your {{bptk_report.item_type}} has been moderated', 'bp-toolkit' ),
        'post_content' => __( "{{recipient.name}},\n\nYour {{bptk_report.item_type}} has been reported and has been hidden :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
        'post_excerpt' => __( "{{recipient.name}},\n\nYour {{bptk_report.item_type}} has been reported and has been hidden :\n\n\"{{bptk_report.item_content}}\"\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
    ),
        'bptk-user-suspended'         => array(
        'post_title'   => __( '[{{{site.name}}}] You have been suspended', 'bp-toolkit' ),
        'post_content' => __( "{{recipient.name}},\n\nYou have been suspended.\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nIf you are currently logged in, you will be logged out. You will not be able to log back in again, until the suspension is lifted.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
        'post_excerpt' => __( "{{recipient.name}},\n\nYou have been suspended.\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nIf you are currently logged in, you will be logged out. You will not be able to log back in again, until the suspension is lifted.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
    ),
        'bptk-user-item-restored'     => array(
        'post_title'   => __( '[{{{site.name}}}] Your {{bptk_report.item_type}} has been restored', 'bp-toolkit' ),
        'post_content' => __( "{{recipient.name}},\n\nYour {{bptk_report.item_type}} has been restored :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
        'post_excerpt' => __( "{{recipient.name}},\n\nYour {{bptk_report.item_type}} has been restored :\n\n\"{{bptk_report.item_content}}\"\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
    ),
        'bptk-reporter-item-hidden'   => array(
        'post_title'   => __( '[{{{site.name}}}] Your reported {{bptk_report.item_type}} has been moderated', 'bp-toolkit' ),
        'post_content' => __( "{{recipient.name}},\n\nThe {{bptk_report.item_type}} you reported has been hidden :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nThank you for helping our community to be safer.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
        'post_excerpt' => __( "{{recipient.name}},\n\nThe {{bptk_report.item_type}} you reported has been hidden :\n\n\"{{bptk_report.item_content}}\"\n\nThis action may have been automatic, where too many reports from other members were received, or manually by an administrator.\n\nThank you for helping our community to be safer.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
    ),
        'bptk-reporter-item-restored' => array(
        'post_title'   => __( '[{{{site.name}}}] Your reported {{bptk_report.item_type}} has been restored', 'bp-toolkit' ),
        'post_content' => __( "{{recipient.name}},\n\nThe {{bptk_report.item_type}} you reported has been restored :\n\n<blockquote>&quot;{{bptk_report.item_content}}&quot;</blockquote>\n\nThis normally happens when an administrator or moderator has reviewed your report, but has considered that it is no longer necessary to keep the content hidden.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
        'post_excerpt' => __( "{{recipient.name}},\n\nThe {{bptk_report.item_type}} you reported has been restored :\n\n\"{{bptk_report.item_content}}\"\n\nThis normally happens when an administrator or moderator has reviewed your report, but has considered that it is no longer necessary to keep the content hidden.\n\nIf you have any questions, please contact a site administrator.", 'bp-toolkit' ),
    ),
    );
}

/**
 * Get the recipient(s) for admin emails, either the default admin, or a list of emails.
 *
 * @since 3.0.0
 */
function bptk_get_admin_email()
{
    $options = get_option( 'report_emails_section' );
    
    if ( !empty($options['bptk_report_emails_admin_list']) ) {
        $array = explode( ',', $options['bptk_report_emails_admin_list'] );
        $trimmed = array_map( 'trim', $array );
        return reset( $trimmed );
    } else {
        return get_bloginfo( 'admin_email' );
    }

}

/**
 * Get email arguments.
 *
 * @since 3.0.0
 */
function bptk_get_args( $item_id, $activity_type )
{
    // Set the location of our reports table
    $args = array(
        'tokens' => array(
        'bptk_report.reports_screen' => bp_get_admin_url( 'edit.php?post_type=report' ),
    ),
    );
    
    if ( $activity_type == 'member' ) {
        $args['tokens']['bptk_report.item_type'] = __( 'user profile', 'bp-toolkit' );
        $args['tokens']['bptk_report.item_content'] = bp_core_get_user_domain( $item_id );
    }
    
    return $args;
}

/**
 * Sends emails based on events.
 *
 * @since 3.0.0
 */
function bptk_send_email(
    $type,
    $item_id,
    $activity_type,
    $post_id = null
)
{
    switch ( $type ) {
        case 'bptk-admin-new-report':
        case 'bptk-admin-item-automod':
            $recipients = bptk_get_admin_email();
            
            if ( is_array( $recipients ) ) {
                foreach ( $recipients as $recipient ) {
                    bp_send_email( $type, $recipient, bptk_get_args( $item_id, $activity_type ) );
                }
            } else {
                bp_send_email( $type, $recipients, bptk_get_args( $item_id, $activity_type ) );
            }
            
            break;
        case 'bptk-user-item-hidden':
        case 'bptk-user-item-restored':
            
            if ( $post_id == 'suspensions' ) {
                $recipient = $item_id;
            } else {
                $recipient = get_post_meta( $post_id, '_bptk_member_reported', true );
            }
            
            $user = get_user_by( 'id', $recipient );
            if ( $user ) {
                bp_send_email( $type, $user, bptk_get_args( $item_id, $activity_type ) );
            }
            break;
        case 'bptk-reporter-item-hidden':
        case 'bptk-reporter-item-restored':
            $recipient = get_post_meta( $post_id, '_bptk_reported_by', true );
            $user = get_user_by( 'id', $recipient );
            bp_send_email( $type, $user, bptk_get_args( $item_id, $activity_type ) );
            break;
        case 'bptk-user-suspended':
            $recipient = $item_id;
            $user = get_user_by( 'id', $recipient );
            bp_send_email( $type, $user, bptk_get_args( $item_id, $activity_type ) );
            break;
    }
}
