<?php
/**
 *
 * Plugin Name: WPBadgeDisplay
 * Version: 1.1.0
 * Description: Adds a widget for displaying Open Badges on your blog.
 * Author: Dave Lester
 * Author URI: http://www.davelester.org
 * Plugin URI: https://github.com/davelester/WPBadgeDisplay
 * Text Domain: wpbadgedisplay
 * Domain Path: /languages
 * @package wpbadgedisplay
*/

// Add widget.
require( 'includes/class-wpbadgedisplaywidget.php' );
add_action( 'widgets_init', function() {
	register_widget( 'WPBadgeDisplayWidget' );
});

if ( ! defined( 'WPBADGEDISPLAY_VERSION' ) ) {
	define( 'WPBADGEDISPLAY_VERSION', '1.0.0' );
}

// Verify what major version we're at.
function wpbadgedisplay_check_version() {
	if ( WPBADGEDISPLAY_VERSION !== get_option( 'wpbadgedisplay_version' ) ) {
		wpbadgedisplay_activation();
	}
}
add_action( 'plugins_loaded', 'wpbadgedisplay_check_version' );

// Run on activation.
function wpbadgedisplay_activation() {
	update_option( 'wpbadgedisplay_version', WPBADGEDISPLAY_VERSION );

	$openbadges_email = get_option( 'openbadges_email' );
	if ( $openbadges_email ) {
		wpbadgedisplay_migrate_settings( $openbadges_email );
	}
}
register_activation_hook( __FILE__, 'wpbadgedisplay_activation' );

// Run on deacativation.
function wpbadgedisplay_deactivation() {
	delete_option( 'wpbadgedisplay_version' );
}
register_deactivation_hook( __FILE__, 'wpbadgedisplay_deactivation' );

// Using OpenBadges User ID, retrieve array of public groups and badges from backpack displayer api
function wpbadgedisplay_get_public_backpack_contents( $openbadgesuserid ) {
	$backpackdata = array();

	$groupsurl = "https://backpack.openbadges.org/displayer/{$openbadgesuserid}/groups.json";
	$response  = wp_remote_get( $groupsurl );
	if ( ! is_array( $response ) ) {
		return '';
	}
	$groupsdata = json_decode( $response['body'] );

	if ( ! empty( $groupsdata->groups ) ) {
		foreach ( $groupsdata->groups as $group ) {
			$badgesurl = "https://backpack.openbadges.org/displayer/{$openbadgesuserid}/group/{$group->groupId}.json";
			$response  = wp_remote_get( $badgesurl );
			if ( ! is_array( $response ) ) {
				continue;
			}
			$badgesdata = json_decode( $response['body'] );

			$badgesingroup = array();

			foreach ( $badgesdata->badges as $badge ) {
				$badgedata = array(
					'title'       => $badge->assertion->badge->name,
					'image'       => $badge->imageUrl,
					'criteriaurl' => $badge->assertion->badge->criteria,
					'issuername'  => $badge->assertion->badge->issuer->name,
					'issuerurl'   => $badge->assertion->badge->issuer->origin,
				);
				array_push( $badgesingroup, $badgedata );
			}

			$groupdata = array(
				'groupname'      => $group->name,
				'groupID'        => $group->groupId,
				'numberofbadges' => $group->badges,
				'badges'         => $badgesingroup,
			);
			array_push( $backpackdata, $groupdata );
		}
	}

	return $backpackdata;
}

/* Generate HTML returned to display badges. Used by both widgets and shortcodes */
function wpbadgedisplay_return_embed( $badgedata, $options = null ) {
	echo "<div id='wpbadgedisplay_widget'>";

	foreach ( $badgedata as $group ) {
		echo '<h1>' . $group['groupname'] . '</h1>';
		echo '<ul class="badge-list">';

		foreach ( $group['badges'] as $badge ) {
			echo '<li>';
			echo "<a href='" . $badge['criteriaurl'] . "'>";
			echo "<img src='" . $badge['image'] . "' />";
			echo '<h2>' . $badge['title'] . '</h2>';
			echo '</a>';
			echo '</li>';
		}
		echo '</ul>';

		if ( ! $group['badges'] ) {
			echo __( 'No badges have been added to this group', 'wpbadgedisplay' );
		}
	}

	if ( ! $badgedata ) {
		echo __( 'No public groups exist for this user', 'wpbadgedisplay' );
	}
	echo '</div>';
}

function wpbadgedisplay_convert_email_to_openbadges_id( $email ) {
	$emailjson = wp_remote_post( 'https://backpack.openbadges.org/displayer/convert/email', array(
		'body' => array(
			'email' => $email,
		),
	) );

	// @todo The user id should probably be cached locally since it's persistent anyway
	if ( is_wp_error( $emailjson ) || 200 != $emailjson['response']['code'] ) {
		return '';
	}

	$body = json_decode( $emailjson['body'] );
	return $body->userId; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.NotSnakeCaseMemberVar.
}

function wpbadgedisplay_read_shortcodes( $atts ) {
	$atts = ( shortcode_atts( array(
		'email'     => '',
		'username'  => '',
		'badgename' => '',
	), $atts ) );

	// Create params array
	$params = array();

	// If both email and username specified, return an error message
	if ( $atts['email'] && $atts['username'] ) {
		return __( 'An email address and username cannot both be included as attributes of a single shortcode', 'wpbadgedisplay' );
	}

	// If a username for a WordPress install is given, retrieve its email address
	if ( $atts['username'] ) {
		$atts['email'] = get_the_author_meta( 'user_email', get_user_by( 'login', $atts['username'] )->ID );
	}

	// If we still have no email value, fall back on the author of the current post
	if ( ! $atts['email'] ) {
		$atts['email'] = get_the_author_meta( 'user_email' );
	}

	/* 	With a user's email address, retrieve their Mozilla Persona ID
		Ideally, email->ID conversion will run only once since a persona ID will not change */
	if ( $atts['email'] ) {
		$openbadgesuserid = wpbadgedisplay_convert_email_to_openbadges_id( $atts['email'] );
	}

	/*  Adds a hook for other plugins (like WPBadger) to add more shortcodes
		that can optionally be added to the params array */
	do_action( 'openbadges_shortcode' );

	$badgedata = wpbadgedisplay_get_public_backpack_contents( $openbadgesuserid );
	return wpbadgedisplay_return_embed( $badgedata );

	// @todo: github ticket #3, if email or username not specified and shortcode is called
	// on an author page, automatically retrieve the author email from the plugin
}
add_shortcode( 'openbadges', 'wpbadgedisplay_read_shortcodes' );

function wpbadgedisplay_scripts() {
	wp_enqueue_style( 'wpbadgedisplay-style', plugins_url( 'style.css', __FILE__ ) );
}
add_action( 'wp_enqueue_scripts', 'wpbadgedisplay_scripts' );

// Beginning with 1.0.0 options are stored per-widget.
function wpbadgedisplay_migrate_settings( $openbadges_email ) {
	$wpbadgedisplaywidget = get_option( 'widget_wpbadgedisplaywidget' );
	if ( $wpbadgedisplaywidget ) {
		// We force this in case there were prior API failures.
		$openbadges_user_id = wpbadgedisplay_convert_email_to_openbadges_id( $openbadges_email );
		foreach ( $wpbadgedisplaywidget as $id => $widget ) {
			if ( is_int( $id ) ) {
				$wpbadgedisplaywidget[ $id ]['openbadges_email']   = $openbadges_email;
				$wpbadgedisplaywidget[ $id ]['openbadges_user_id'] = $openbadges_user_id;
			}
		}
		update_option( 'widget_wpbadgedisplaywidget', $wpbadgedisplaywidget );
	}
	delete_option( 'openbadges_email' );
	delete_option( 'openbadges_user_id' );
}

function wpbadgedisplay_eraser( $email_address, $page = 1 ) {
	$limit  = 100; // Limit us to avoid timing out
	$page   = (int) $page;
	$offset = $limit * ( $page - 1 );

	$items_left    = false;
	$items_removed = false;

	$widgets         = get_option( 'widget_wpbadgedisplaywidget' );
	$widgets_updated = array();

	if ( count( $widgets ) > $limit ) {
		$widgets    = array_slice( $widgets, $offset, $limit, true );
		$items_left = true;
	}

	foreach ( $widgets as $key => $instance ) {
		$new_instance = null;
		if ( is_array( $instance ) && array_key_exists( 'title', $instance ) ) {
			if ( $instance['openbadges_email'] === $email_address ) {
				$new_instance = $instance;

				$new_instance['openbadges_email']   = '';
				$new_instance['openbadges_user_id'] = '';

				$items_removed = true;
			}
		}
		$widgets_updated[ $key ] = $new_instance ? $new_instance : $instance;
	}

	update_option( 'widget_wpbadgedisplaywidget', $widgets_updated );

	// Tell core if we have more comments to work on still
	return array(
		'items_removed'  => $items_removed,
		'items_retained' => false,
		'messages'       => array(),
		'done'           => (bool) ! $items_left,
	);
}

function wpbadgedisplay_exporter( $email_address, $page = 1 ) {
	$limit  = 100; // Limit us to avoid timing out
	$page   = (int) $page;
	$offset = $limit * ( $page - 1 );

	$items_left = false;

	$widgets = get_option( 'widget_wpbadgedisplaywidget' );

	if ( count( $widgets ) > $limit ) {
		$widgets    = array_slice( $widgets, $offset, $limit, true );
		$items_left = true;
	}

	$widget_ids = array();
	foreach ( $widgets as $key => $instance ) {
		if ( is_array( $instance ) && array_key_exists( 'title', $instance ) ) {
			if ( $instance['openbadges_email'] === $email_address ) {
				$widget_ids[] = $key;
			}
		}
	}

	$items = array();

	$group_id    = 'widgets';
	$group_label = __( 'Widgets', 'wpbadgedisplay' );

	if ( ! empty( $widget_ids ) ) {
		$default = array(
			'name'  => __( 'WPBadgeDisplay Widgets Using Your Email/ID', 'wpbadgedisplay' ),
			'value' => implode( ', ', $widget_ids ),
		);
		$data    = array( $default );

		$items[] = array(
			'group_id'    => $group_id,
			'group_label' => $group_label,
			'item_id'     => 'wpbadgedisplay-widget-ids',
			'data'        => $data,
		);
	}

	$badge_id = wpbadgedisplay_convert_email_to_openbadges_id( $email_address );
	if ( ! empty( $badge_id ) ) {
		$default = array(
			'name'  => __( 'Your Open Badges Backpack User ID According to WPBadgeDisplay', 'wpbadgedisplay' ),
			'value' => wpbadgedisplay_convert_email_to_openbadges_id( $email_address ),
		);
		$data    = array( $default );

		$items[] = array(
			'group_id'    => $group_id,
			'group_label' => $group_label,
			'item_id'     => 'wpbadgedisplay-widget-user-id',
			'data'        => $data,
		);
	}

	$badge_data = wpbadgedisplay_get_public_backpack_contents( wpbadgedisplay_convert_email_to_openbadges_id( $email_address ) );
	if ( ! empty( $badge_data ) ) {
		$default = array(
			'name'  => __( 'Badge Data Imported by WPBadgeDisplay', 'wpbadgedisplay' ),
			'value' => print_r( $badge_data, true ),
		);
		$data    = array( $default );

		$items[] = array(
			'group_id'    => $group_id,
			'group_label' => $group_label,
			'item_id'     => 'wpbadgedisplay-badge-data',
			'data'        => $data,
		);
	}

	// Tell core if we have more comments to work on still.
	return array(
		'data' => $items,
		'done' => (bool) ! $items_left,
	);
}

function register_wpbadgedisplay_eraser() {
	$erasers['wpbadgedisplay'] = array(
		'eraser_friendly_name' => __( 'WPBadgeDisplay Plugin', 'wpbadgedisplay' ),
		'callback'             => 'wpbadgedisplay_eraser',
	);
	return $erasers;
}

function register_wpbadgedisplay_exporter( $exporters ) {
	$exporters['wpbadgedisplay'] = array(
		'exporter_friendly_name' => __( 'WPBadgeDisplay Plugin', 'wpbadgedisplay' ),
		'callback'               => 'wpbadgedisplay_exporter',
	);
	return $exporters;
}

add_filter(
	'wp_privacy_personal_data_erasers',
	'register_wpbadgedisplay_eraser',
	10
);

add_filter(
	'wp_privacy_personal_data_exporters',
	'register_wpbadgedisplay_exporter',
	10
);
