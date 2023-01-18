<?php
/**
 * Uninstall file, which would delete all user metadata and configuration settings
 *
 * @since 1.0
 * @package badgeos
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

global $wpdb;

require_once plugin_dir_path( __FILE__ ) . 'includes/utilities.php';

// Loop through all active sites.
$sites = array();
if ( is_multisite() ) {
	$blog_ids = $wpdb->get_results( 'SELECT blog_id FROM ' . $wpdb->base_prefix . 'blogs' );
	foreach ( $blog_ids as $key => $value ) {
		$sites[] = $value->blog_id;
	}
} else {
	$sites[] = get_current_blog_id();
}

foreach ( $sites as $site_blog_id ) {
	if ( is_multisite() ) {
		switch_to_blog( $site_blog_id );
	}
	$badgeos_settings         = badgeos_utilities::get_option( 'badgeos_settings' );
	$remove_data_on_uninstall = ( isset( $badgeos_settings['remove_data_on_uninstall'] ) ) ? $badgeos_settings['remove_data_on_uninstall'] : '';

	/**
	 * Return - if delete option is not enabled.
	 */
	if ( 'on' !== $remove_data_on_uninstall ) {
		return;
	}

	global $wpdb;

	/**
	 * Delete Achievements post types data
	 */
	$achievement_types = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT ID, post_title 
            FROM $wpdb->posts 
            WHERE post_type = %s",
			$badgeos_settings['achievement_main_post_type']
		)
	);

	if ( is_array( $achievement_types ) && ! empty( $achievement_types ) && ! is_null( $achievement_types ) ) {
		$to_delete        = array();
		$child_post_types = array();
		foreach ( $achievement_types as $achievement_type ) {
			$to_delete[]        = $achievement_type->ID;
			$child_post_types[] = sanitize_title( substr( strtolower( $achievement_type->post_title ), 0, 20 ) );
		}

		if ( ! empty( $child_post_types ) ) {
			foreach ( $child_post_types as $child_post_type ) {
				$child_posts = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", $child_post_type ) );
				if ( is_array( $child_posts ) && ! empty( $child_posts ) && ! is_null( $child_posts ) ) {
					foreach ( $child_posts as $child_post ) {
						$to_delete[] = $child_post->ID;
					}
				}
			}
		}

		foreach ( $to_delete as $del ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $del ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $del ) );
		}
	}

	if ( ! empty( $badgeos_settings ) ) {
		if ( is_array( $badgeos_settings ) && count( $badgeos_settings ) > 0 ) {
			$rank_types = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_name FROM $wpdb->posts WHERE post_type = %s", $badgeos_settings['ranks_main_post_type'] ) );
			if ( is_array( $rank_types ) && ! empty( $rank_types ) && ! is_null( $rank_types ) ) {
				foreach ( $rank_types as $rank_type ) {
					$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $rank_type->post_name ) );
				}
			}
			$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $badgeos_settings['ranks_main_post_type'] ) );
			$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $badgeos_settings['ranks_step_post_type'] ) );
			$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $badgeos_settings['points_main_post_type'] ) );
			$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $badgeos_settings['points_award_post_type'] ) );
			$wpdb->get_results( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE post_type=%s", $badgeos_settings['points_deduct_post_type'] ) );
		}
	}

	/**
	 * Delete Step post type data
	 */
	$steps_ids = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type = %s", $badgeos_settings['achievement_step_post_type'] ) );
	if ( is_array( $steps_ids ) && ! empty( $steps_ids ) && ! is_null( $steps_ids ) ) {
		foreach ( $steps_ids as $steps_id ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID = %d", $steps_id->ID ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $steps_id->ID ) );
		}
	}

	/**
	 * Delete badgeos-log-entry post type data
	 */
	$badgeos_log_entry = $wpdb->get_results( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE post_type=%s", 'badgeos-log-entry' ) );

	if ( is_array( $badgeos_log_entry ) && ! empty( $badgeos_log_entry ) && ! is_null( $badgeos_log_entry ) ) {
		foreach ( $badgeos_log_entry as $log_entry ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->posts WHERE ID         = %d", $log_entry->ID ) );
			$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->postmeta WHERE post_id = %d", $log_entry->ID ) );
		}
	}

	/**
	 * Delete user BadgeOS meta
	 */

	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_badgeos_triggered_triggers';" );
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_badgeos_can_notify_user';" );
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_badgeos_achievements';" );
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_badgeos_active_achievements';" );
	$wpdb->query( "DELETE FROM $wpdb->usermeta WHERE meta_key = '_badgeos_points';" );

	/**
	 * Delete BadgeOS options
	 */

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_settings' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'widget_p2p' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'widget_earned_user_achievements_widget' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'p2p_storage' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_admin_tools' ) );

	/**
	 * Delete BadgeOS tables
	 */

	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'p2p' );
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'p2pmeta' );
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'badgeos_points' );
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'badgeos_ranks' );
	$wpdb->query( 'DROP TABLE IF EXISTS ' . $wpdb->prefix . 'badgeos_achievements' );

	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_assertion_url' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_issuer_url' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_json_url' ) );
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name = %s", 'badgeos_evidence_url' ) );

}
