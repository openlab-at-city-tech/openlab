<?php
/**
 * WP-CLI tool for collecting networkwide data about 'More visibility options' usage.
 * 
 * Usage: wp eval-file scripts/collect-visibility-data.php
 * 
 * This script collects data about the usage of the 'openlab_post_visibility' postmeta option
 * across the network and provides statistics by group type and member type.
 */

// Exit if not run via WP-CLI
if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	die( 'This script must be run via WP-CLI.' );
}

/**
 * Collect visibility data across the network.
 */
function openlab_collect_visibility_data() {
	global $wpdb;

	// Initialize data structures
	$group_type_totals = [
		'course'    => 0,
		'project'   => 0,
		'club'      => 0,
		'portfolio' => 0,
		'unknown'   => 0, // Sites without associated groups
	];

	$member_type_totals = [
		'student' => 0,
		'faculty' => 0,
		'staff'   => 0,
		'alumni'  => 0,
		'unknown' => 0, // Users without member type
	];

	// Track visibility option usage
	$visibility_option_totals = [
		'group-members-only' => 0,
		'members-only'       => 0,
		'default'            => 0,
	];

	// Additional detailed breakdown
	$detailed_stats = [
		'total_sites_checked'           => 0,
		'sites_with_visibility_options' => 0,
		'total_posts_with_visibility'   => 0,
	];

	// Group type and visibility breakdown
	$group_type_visibility_breakdown = [];
	foreach ( array_keys( $group_type_totals ) as $type ) {
		$group_type_visibility_breakdown[ $type ] = [
			'group-members-only' => 0,
			'members-only'       => 0,
			'default'            => 0,
		];
	}

	// Get all sites in the network
	$sites = get_sites(
		[
			'number'  => 99999,
			'fields'  => 'ids',
			'public'  => null,
			'deleted' => 0,
			'spam'    => 0,
		]
	);

	$total_sites = count( $sites );
	WP_CLI::log( "Found {$total_sites} sites to analyze." );
	WP_CLI::log( '' );

	// Create progress bar
	$progress = WP_CLI\Utils\make_progress_bar( 'Processing sites', $total_sites );

	foreach ( $sites as $site_id ) {
		$detailed_stats['total_sites_checked']++;

		switch_to_blog( $site_id );

		// Get the group ID for this site
		$group_id = openlab_get_group_id_by_blog_id( $site_id );

		// Determine group type
		$group_type = 'unknown';
		if ( $group_id ) {
			$group_type = openlab_get_group_type( $group_id );
			if ( ! $group_type || ! in_array( $group_type, [ 'course', 'project', 'club', 'portfolio' ], true ) ) {
				$group_type = 'unknown';
			}
		}

		// Query for posts with openlab_post_visibility meta
		$posts_with_visibility = $wpdb->get_results(
			"SELECT pm.post_id, pm.meta_value as visibility_option, p.post_author
			FROM {$wpdb->postmeta} pm
			INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
			WHERE pm.meta_key = 'openlab_post_visibility'
			AND pm.meta_value IN ('group-members-only', 'members-only', 'default')
			AND p.post_status = 'publish'
			AND p.post_type IN ('post', 'page')"
		);

		// Check if this site has at least one post with visibility options
		if ( ! empty( $posts_with_visibility ) ) {
			$group_type_totals[ $group_type ]++;
			$detailed_stats['sites_with_visibility_options']++;

			// Process each post with visibility options
			foreach ( $posts_with_visibility as $post_data ) {
				$detailed_stats['total_posts_with_visibility']++;

				// Track visibility option usage
				$visibility_option = $post_data->visibility_option;
				if ( isset( $visibility_option_totals[ $visibility_option ] ) ) {
					$visibility_option_totals[ $visibility_option ]++;
				}

				// Track by group type and visibility option
				if ( isset( $group_type_visibility_breakdown[ $group_type ][ $visibility_option ] ) ) {
					$group_type_visibility_breakdown[ $group_type ][ $visibility_option ]++;
				}

				// Get member type of post author
				$author_id = (int) $post_data->post_author;
				if ( $author_id ) {
					$member_type = openlab_get_user_member_type( $author_id );

					if ( ! $member_type || ! in_array( $member_type, [ 'student', 'faculty', 'staff', 'alumni' ], true ) ) {
						$member_type = 'unknown';
					}

					$member_type_totals[ $member_type ]++;
				} else {
					$member_type_totals['unknown']++;
				}
			}
		}

		restore_current_blog();
		$progress->tick();
	}

	$progress->finish();

	// Display results
	WP_CLI::log( '' );
	WP_CLI::log( '============================================' );
	WP_CLI::log( 'VISIBILITY OPTIONS USAGE REPORT' );
	WP_CLI::log( '============================================' );
	WP_CLI::log( '' );

	// Overall statistics
	WP_CLI::log( '--- OVERALL STATISTICS ---' );
	WP_CLI::log( sprintf( 'Total sites checked: %d', $detailed_stats['total_sites_checked'] ) );
	WP_CLI::log( sprintf( 'Sites with visibility-restricted posts: %d', $detailed_stats['sites_with_visibility_options'] ) );
	WP_CLI::log( sprintf( 'Total posts/pages with visibility options: %d', $detailed_stats['total_posts_with_visibility'] ) );
	WP_CLI::log( '' );

	// Group type totals
	WP_CLI::log( '--- BY GROUP TYPE ---' );
	WP_CLI::log( 'Number of groups (by type) where the associated site has posts/pages with visibility options:' );
	foreach ( $group_type_totals as $type => $count ) {
		WP_CLI::log( sprintf( '  %s: %d', ucfirst( $type ), $count ) );
	}
	WP_CLI::log( '' );

	// Member type totals
	WP_CLI::log( '--- BY MEMBER TYPE ---' );
	WP_CLI::log( 'Number of posts/pages with visibility options by author member type:' );
	foreach ( $member_type_totals as $type => $count ) {
		WP_CLI::log( sprintf( '  %s: %d', ucfirst( $type ), $count ) );
	}
	WP_CLI::log( '' );

	// Visibility option breakdown
	WP_CLI::log( '--- BY VISIBILITY OPTION ---' );
	WP_CLI::log( 'Total posts/pages by visibility setting:' );
	foreach ( $visibility_option_totals as $option => $count ) {
		WP_CLI::log( sprintf( '  %s: %d', $option, $count ) );
	}
	WP_CLI::log( '' );

	// Detailed breakdown: Group Type + Visibility Option
	WP_CLI::log( '--- DETAILED BREAKDOWN: GROUP TYPE + VISIBILITY OPTION ---' );
	foreach ( $group_type_visibility_breakdown as $type => $options ) {
		if ( array_sum( $options ) > 0 ) {
			WP_CLI::log( sprintf( '%s:', ucfirst( $type ) ) );
			foreach ( $options as $option => $count ) {
				if ( $count > 0 ) {
					WP_CLI::log( sprintf( '  %s: %d', $option, $count ) );
				}
			}
		}
	}
	WP_CLI::log( '' );
	WP_CLI::log( '============================================' );

	WP_CLI::success( 'Data collection complete!' );
}

// Run the collection
openlab_collect_visibility_data();
