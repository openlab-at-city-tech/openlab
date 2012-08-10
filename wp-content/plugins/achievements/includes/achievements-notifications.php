<?php
/**
 * Contains functions to create notifications in the "BuddyBar" and send email notifications on specific user actions.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage notifications
 *
 * $Id: achievements-notifications.php 1018 2011-10-07 20:19:01Z DJPaul $
 */

/**
 * The format notification function takes DB entries for notifications and formats them so that they can be
 * displayed and read on the screen.
 *
 * @since 2.0
 * @see bp_core_add_notification()
 * @uses DPA_Achievement
 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param string $action Type of notification
 * @param int $item_id Achievement ID
 * @param int $secondary_item_id User ID
 * @param int $total_items Number of pending notifications of this type
 */
function dpa_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
	global $achievements_template, $bp, $wpdb;

	do_action( 'dpa_format_notifications', $action, $item_id, $secondary_item_id, $total_items, $format );

	switch ( $action ) {
		case 'new_achievement':
			if ( 1 == $total_items ) {
				$achievements_template->achievement = new DPA_Achievement( array( 'id' => $item_id, 'populate_extras' => false ) );
				$text = sprintf( __( 'Achievement unlocked: %s', 'dpa' ), dpa_get_achievement_name() );
				$link = dpa_get_achievement_slug_permalink();
				unset( $achievements_template->achievement );

			} else {
				$text = __( 'Achievements unlocked!', 'dpa' );
				$link = bp_core_get_user_domain( $secondary_item_id ) . DPA_SLUG . '/' . DPA_SLUG_MY_ACHIEVEMENTS;
			}
		break;
	}

	if ( 'string' == $format ) {
		return apply_filters( 'dpa_new_achievement_notification', '<a href="' . $link . '">' . $text . '</a>', $item_id, $secondary_item_id, $total_items );

	} else {
		$array = array(
			'text' => $text,
			'link' => $link
		);

		return apply_filters( 'dpa_new_achievement_notification', $array, $item_id, $secondary_item_id, $total_items );
	}
}

/**
 * Sends the email notification to the user when an Achievement is unlocked.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @param int $achievement_id
 * @param int $user_id
 * @since 2.0
 */
function dpa_achievement_unlocked_notification( $achievement_id, $user_id ) {
		global $achievements_template, $bp, $wpdb;

		if ( 'no' == get_user_meta( $user_id, 'notification_dpa_unlock_achievement', true ) )
			return;

		$recipient     = get_userdata( $user_id );
		$settings_link = bp_core_get_user_domain( $user_id ) . bp_get_settings_slug() . '/notifications/';
		$achievements_link = bp_core_get_user_domain( $user_id ) . DPA_SLUG . '/';

		$email_subject = sprintf( __( '[%1$s] Achievement unlocked: %2$s', 'dpa' ), wp_specialchars_decode( get_blog_option( BP_ROOT_BLOG, 'blogname' ), ENT_QUOTES ), dpa_get_achievement_name() );
		$email_content = sprintf( __(
'
You have unlocked an Achievement: %1$s

To review this and see all of your Achievements, go to %2$s

---------------------
To disable these notifications please log in and go to: %3$s',
		'dpa' ), dpa_get_achievement_name(), $achievements_link, $settings_link );

		// Send the message
		$email_to = apply_filters( 'dpa_unlock_achievement_notification_to', $recipient->user_email, $achievement_id );
		$email_subject = apply_filters( 'dpa_unlock_achievement_notification_subject', $email_subject, $achievement_id );
		$email_content = apply_filters( 'dpa_unlock_achievement_notification_message', $email_content, $achievement_id, $achievements_link, $settings_link );

		wp_mail( $email_to, $email_subject, $email_content );
}
add_action( 'dpa_achievement_unlocked', 'dpa_achievement_unlocked_notification', 10, 2 );
?>