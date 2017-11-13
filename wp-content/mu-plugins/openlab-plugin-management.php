<?php

/**
 * This file is responsible for determining which plugins are shown to users
 * on the Plugins page of their individual sites
 */

function openlab_hide_plugins( $plugins ) {
	$network_admin_only = array();

	$super_admin_only = array(
		'1-jquery-photo-gallery-slideshow-flash/wp-1pluginjquery.php',
		'ajax-thumbnail-rebuild/ajax-thumbnail-rebuild.php',
		'ambrosite-nextprevious-post-link-plus/ambrosite-post-link-plus.php',
		'an-gradebook/GradeBook.php',
		'bbpress/bbpress.php',
		'bp-customizable-group-categories/bp-customizable-group-categories.php',
		'bp-event-organiser/bp-event-organiser.php',
		'bp-reply-by-email/loader.php',
		'cac-non-cuny-signup/loader.php',
		'ewww-image-optimizer/ewww-image-optimizer.php',
		'featured-content-gallery/content-gallery.php',
		'google-maps-embed/cets_EmbedGmaps.php',
		'grader/grader.php',
		'kb-gradebook/kb-gradebook.php',
		'p3-profiler/p3-profiler.php',
		'query-monitor/query-monitor.php',
		'slideshare/slideshare.php',
		'social/social.php',
		'static-html-output-plugin/wp-static-html-output.php',
		'stout-google-calendar/stout-google-calendar.php',
		'titan-framework/titan-framework.php',
		'ufhealth-require-image-alt-tags/ufhealth-require-image-alt-tags.php',
		'webwork/webwork.php',
		'wp-accessibility/wp-accessibility',
	);

	if ( ! is_super_admin() ) {
		foreach ( $plugins as $pkey => $plugin ) {
			if ( in_array( $pkey, $super_admin_only ) ) {
				unset( $plugins[ $pkey ] );
			}
		}
	}

	if ( ! is_network_admin() ) {
		$network_admin_only = array(
			'achievements/loader.php',
			'bp-groupblog/loader.php',
			'buddypress-group-documents/index.php',
			'bp-include-non-member-comments/bp-include-non-member-comments.php',
			'bp-mpo-activity-filter/bp-mpo-activity-filter.php',
			'bp-system-report/bp-system-report.php',
			'buddypress/bp-loader.php',
			'buddypress-docs/loader.php',
			'buddypress-group-email-subscription/bp-activity-subscription.php',
			'bp-template-pack/loader.php',
			'staticpress/plugin.php',
			'cac-group-announcements/cac-group-announcements.php',
			'cubepoints-buddypress-integration/loader.php',
			'digressit/digressit.php',
			'forum-attachments-for-buddypress/forum-attachments-for-buddypress.php',
			'genesis-connect/genesis-connect.php',
			'genesis-connect-for-buddypress/genesis-connect.php',
			'invite-anyone/invite-anyone.php',
			'more-privacy-options/ds_wp3_private_blog.php',
			'openlab-grade-comments/openlab-grade-comments.php',
			'u-buddypress-forum-attachment/u-bp-forum-attachment.php',
			'wds-buddypress-docs/loader.php',
			'wds-citytech/wds-citytech.php',
		);

		foreach ( $plugins as $pkey => $plugin ) {
			if ( in_array( $pkey, $network_admin_only ) ) {
				unset( $plugins[ $pkey ] );
			}
		}
	}

	$blog_specific_whitelist = array(
		'h5p/h5p.php' => array(
			11188, // bio-oer - https://redmine.citytech.cuny.edu/issues/2088
		),
	);

	foreach ( $blog_specific_whitelist as $plugin_file => $whitelisted_blogs ) {
		if ( ! in_array( get_current_blog_id(), $whitelisted_blogs ) ) {
			unset( $plugins[ $plugin_file ] );
		}
	}

	return $plugins;
}
add_filter( 'all_plugins', 'openlab_hide_plugins' );
