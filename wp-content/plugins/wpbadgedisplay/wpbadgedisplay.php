<?php
/**
 *
 * Plugin Name: WPBadgeDisplay
 * Version: 1.0.0
 * Description: Adds a widget for displaying Open Badges on your blog.
 * Author: Dave Lester
 * Author URI: http://www.davelester.org
 * Plugin URI: https://github.com/davelester/WPBadgeDisplay
 * Text Domain: wpbadgedisplay
 * Domain Path: /languages
 * @package wpbadgedisplay
*/

class WPBadgeDisplayWidget extends WP_Widget {
	public function __construct() {
		parent::__construct(
	 		'WPBadgeDisplayWidget',
			'WPBadgeDisplay Widget',
			array(
				'description' => __( 'Display Open Badges', 'wpbadgedisplay' ),
			)
		);
	}

	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array(
			'title' => '',
			'openbadges_email' => '',
			'openbadges_user_id' => '',
		) );
		$title = $instance['title'];
	?>
	<p><label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpbadgedisplay' ); ?> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>

	<p><label for="openbadges_user_id"><?php _e( 'Email Account:', 'wpbadgedisplay' ); ?> <input class="widefat" id="openbadges_email" name="<?php echo $this->get_field_name( 'openbadges_email' ); ?>" type="text" value="<?php echo esc_attr( $instance['openbadges_email'] ); ?>" /></label></p>
	<?php
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$args = wp_parse_args( (array) $new_instance, array(
			'title' => '',
			'openbadges_email' => '',
			'openbadges_user_id' => '',
		) );
		$instance['title'] = sanitize_text_field( $args['title'] );
		$instance['openbadges_email'] = $args['openbadges_email'];
		$instance['openbadges_user_id'] = wpbadgedisplay_convert_email_to_openbadges_id( $instance['openbadges_email'] );

		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$badgedata = wpbadgedisplay_get_public_backpack_contents( $instance['openbadges_user_id'], null );
		echo wpbadgedisplay_return_embed( $badgedata );
	}

}
add_action( 'widgets_init', create_function( '', 'return register_widget("WPBadgeDisplayWidget");' ) );

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
	$response = wp_remote_get( $groupsurl );
	if ( ! is_array( $response ) ) {
		return '';
	}
	$groupsdata = json_decode( $response['body'] );

	if ( ! empty( $groupsdata->groups ) ) {
		foreach ( $groupsdata->groups as $group ) {
			$badgesurl = "https://backpack.openbadges.org/displayer/{$openbadgesuserid}/group/{$group->groupId}.json";
			$response = wp_remote_get( $badgesurl );
			if ( ! is_array( $response ) ) {
				continue;
			}
			$badgesdata = json_decode( $response['body'] );

			$badgesingroup = array();

			foreach ( $badgesdata->badges as $badge ) {
				$badgedata = array(
					'title' => $badge->assertion->badge->name,
					'image' => $badge->imageUrl,
					'criteriaurl' => $badge->assertion->badge->criteria,
					'issuername' => $badge->assertion->badge->issuer->name,
					'issuerurl' => $badge->assertion->badge->issuer->origin,
				);
				array_push( $badgesingroup, $badgedata );
			}

			$groupdata = array(
				'groupname' => $group->name,
				'groupID' => $group->groupId,
				'numberofbadges' => $group->badges,
				'badges' => $badgesingroup,
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
	return $body->userId;
}

function wpbadgedisplay_read_shortcodes( $atts ) {
	extract( shortcode_atts( array(
		'email' => '',
		'username' => '',
		'badgename' => '',
	), $atts ) );

	// Create params array
	$params = array();

	// If both email and username specified, return an error message
	if ( $email && $username ) {
		return __( 'An email address and username cannot both be included as attributes of a single shortcode', 'wpbadgedisplay' );
	}

	// If a username for a WordPress install is given, retrieve its email address
	if ( $username ) {
		$email = get_the_author_meta( 'user_email', get_user_by( 'login', $username )->ID );
	}

	// If we still have no email value, fall back on the author of the current post
	if ( ! $email ) {
		$email = get_the_author_meta( 'user_email' );
	}

	/* 	With a user's email address, retrieve their Mozilla Persona ID
		Ideally, email->ID conversion will run only once since a persona ID will not change */
	if ( $email ) {
		$openbadgesuserid = wpbadgedisplay_convert_email_to_openbadges_id( $email );
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
			        $wpbadgedisplaywidget[ $id ]['openbadges_email'] = $openbadges_email;
			        $wpbadgedisplaywidget[ $id ]['openbadges_user_id'] = $openbadges_user_id;
			}
		}
		update_option( 'widget_wpbadgedisplaywidget', $wpbadgedisplaywidget );
	}
	delete_option( 'openbadges_email' );
	delete_option( 'openbadges_user_id' );
}

?>
