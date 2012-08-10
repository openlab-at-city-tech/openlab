<?php
/**
 * Creates and applies filters for component output functions.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements 
 * @subpackage filters
 *
 * $Id: achievements-filters.php 972 2011-04-03 10:09:47Z DJPaul $
 */

// Display filters
add_filter( 'dpa_get_achievement_name', 'wptexturize'   );
add_filter( 'dpa_get_achievement_name', 'convert_chars' );

add_filter( 'dpa_get_achievement_description', 'wptexturize'       );
add_filter( 'dpa_get_achievement_description', 'convert_smilies'   );
add_filter( 'dpa_get_achievement_description', 'convert_chars'     );
add_filter( 'dpa_get_achievement_description', 'wpautop'           );
add_filter( 'dpa_get_achievement_description', 'shortcode_unautop' );
add_filter( 'dpa_get_achievement_description', 'make_clickable'    );
add_filter( 'dpa_get_achievement_description', 'do_shortcode', 11  );  // AFTER wpautop()

add_filter( 'dpa_get_member_achievements_score',        'bp_core_number_format' );
add_filter( 'dpa_get_total_achievement_count',          'bp_core_number_format' );
add_filter( 'dpa_get_total_achievement_count_for_user', 'bp_core_number_format' );

add_filter( 'dpa_get_addedit_action_description', 'wptexturize'   );
add_filter( 'dpa_get_addedit_action_description', 'convert_chars' );

// More display filters (search query, RSS feeds, widgets, admin settings)
add_filter( 'dpa_get_achievements_search_query', 'wp_kses_data', 1 );  // From an external source
add_filter( 'dpa_get_achievements_search_query', 'wptexturize'       );
add_filter( 'dpa_get_achievements_search_query', 'convert_chars'     );

add_filter( 'dpa_admin_get_rss_feed', 'wp_kses_data', 1 );  // From an external source
add_filter( 'dpa_admin_get_rss_feed', 'wptexturize'       );
add_filter( 'dpa_admin_get_rss_feed', 'convert_chars'     );

add_filter( 'dpa_widget_title', 'wptexturize'   );
add_filter( 'dpa_widget_title', 'convert_chars' );

add_filter( 'dpa_admin_settings_mediakeywords', 'wptexturize'   );
add_filter( 'dpa_admin_settings_mediakeywords', 'convert_chars' );

// Create/edit screen display filter
add_filter( 'dpa_get_addedit_value', 'esc_attr' );


// CRUD filters
add_filter( 'dpa_achievement_name_before_save', 'wp_filter_kses', 1   );
add_filter( 'dpa_achievement_name_before_save', 'sanitize_text_field' );

add_filter( 'dpa_achievement_slug_before_save', 'wp_filter_kses', 1 );
add_filter( 'dpa_achievement_slug_before_save', 'sanitize_title'    );

if ( bp_is_active( 'activity' ) )
	add_filter( 'dpa_achievement_description_before_save', 'bp_activity_filter_kses', 1 );
else
	add_filter( 'dpa_achievement_description_before_save', 'wp_filter_kses', 1          );

// More CRUD filters (widgets + admin settings)
add_filter( 'dpa_admin_settings_mediakeywords_before_save', 'wp_kses_data', 1     );
add_filter( 'dpa_admin_settings_mediakeywords_before_save', 'sanitize_text_field' );

add_filter( 'dpa_widget_title_before_save', 'wp_filter_kses', 1   );
add_filter( 'dpa_widget_title_before_save', 'sanitize_text_field' );


// Custom filters

/**
 * Filters the bp_has_members() query to return only users who have unlocked the specific Achievement.
 *
 * @global object $bp BuddyPress global settings
 * @global wpdb $wpdb WordPress database object
 * @since 2.0
 */
function dpa_filter_users_by_achievement( $query, $sql_parts ) {
	global $bp, $wpdb;

	// Hacktastic. To override filter text on pagination. See bp_members_pagination_count().
	$find    = __( 'Viewing member %1$s to %2$s (of %3$s active members)', 'buddypress' );  // Intentionally uses BuddyPress' text domain.
	$replace = __( 'Viewing member %1$s to %2$s (of %3$s members) who have unlocked this Achievement', 'dpa' );
	dpa_override_i18n( $find, $replace );

	if ( $bp->achievements->current_achievement->id ) {
		// This function is hooked into both bp_core_get_total_users_sql and bp_core_get_paged_users_sql, modify the query appropiately.
		if ( isset( $sql_parts['select_main'] ) )
	    $sql_parts['select_main'] .= ', unlocked.achieved_at';

   $sql_parts['where'] = ", {$bp->achievements->table_unlocked} as unlocked {$sql_parts['where']}" . $wpdb->prepare( ' AND unlocked.achievement_id = %d AND unlocked.user_id = u.ID', $bp->achievements->current_achievement->id );

		if ( "ORDER BY um.meta_value DESC" == $sql_parts[0] || "ORDER BY u.user_registered DESC" == $sql_parts[0] || "ORDER BY pd.value ASC" == $sql_parts[0] || "ORDER BY rand()" == $sql_parts[0] || "ORDER BY CONVERT(um.meta_value, SIGNED) DESC" == $sql_parts[0] )
			$sql_parts[0] = "ORDER BY unlocked.achieved_at DESC";
	}

	// Remove the filters that invoke this function so it doesn't affect other usages of the members loop on the same page
	if ( isset( $sql_parts['select_main'] ) )
		remove_filter( 'bp_core_get_paged_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );
	else
		remove_filter( 'bp_core_get_total_users_sql', 'dpa_filter_users_by_achievement', 10, 2 );

	// Switch back the translation
	$find    = __( 'Viewing member %1$s to %2$s (of %3$s members) who have unlocked this Achievement', 'dpa' );
	$replace = __( 'Viewing member %1$s to %2$s (of %3$s active members)', 'buddypress' );  // Intentionally uses BuddyPress' text domain.
	dpa_override_i18n( $find, $replace );

	return apply_filters( 'dpa_filter_users_by_achievement', join( ' ', (array)$sql_parts ), $sql_parts );
}

/**
 * Changes the "last activity" timestamp in the members' template loop to when the Achievement was unlocked.
 * Use only in the members' template loop.
 *
 * @global BP_Core_Members_Template $members_template
 * @see dpa_filter_users_by_achievement()
 * @since 2.0
 */
function dpa_filter_unlockedby_activity_timestamp() {
	global $members_template;

	return sprintf( __( 'Unlocked %s ago', 'dpa' ), bp_core_time_since( $members_template->member->achieved_at ) );
}

/**
 * Removes the filter added to modify the timestamp on the members-loop.php template to prevent it avoiding with
 * multiple use of the members loop on the same page (e.g. a widget).
 *
 * @see dpa_filter_achievement_unlockedby_template_loader()
 * @see dpa_screen_achievement_unlocked_by()
 * @since 2.0.3
 */
function dpa_remove_filters_after_members_loop() {
	remove_filter( 'bp_member_last_active', 'dpa_filter_unlockedby_activity_timestamp' );
}
?>