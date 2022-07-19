<?php
/**
 * Plugin Name: BadgeOS BadgeStack Add-On
 * Plugin URI: https://badgeos.org/downloads/badgestack/
 * Description: This BadgeOS add-on automatically creates achievement types, pages and sample content to jumpstart your own badging system.
 * Author: BadgeOS
 * Version: 1.0.3
 * Author URI: https://badgeos.org/
 * License: GNU AGPL
 */

/*
 * Copyright © 2012-2013 LearningTimes, LLC
 *
 * This program is free software: you can redistribute it and/or modify it
 * under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General
 * Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>;.
*/

final class BadgeOS_BadgeStack {

	function __construct() {
		// Define plugin constants
		$this->basename       = plugin_basename( __FILE__ );
		$this->directory_path = plugin_dir_path( __FILE__ );
		$this->directory_url  = plugins_url( 'badgeos-badgestack/' );

		// Load translations
		load_plugin_textdomain( 'badgeos-badgestack', false, 'badge-plugin/languages' );

		// Run our activation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );

		// If BadgeOS is unavailable, deactivate our plugin
		add_action( 'admin_notices', array( $this, 'maybe_disable_plugin' ) );
		add_action( 'init', array( $this, 'after_activate' ), 999 );

	}

	/**
	 * Activation hook for the plugin.
	 *
	 * @since 1.0.0
	 */
	public function activate() {

		// If BadgeOS is available, run our activation functions
		if ( $this->meets_requirements() ) {

			// Setup an empty array to use for storing our achievement type post_types
			$this->type = array();

			// Setup our new Achievement Types
			$achievement_types = array (
				'community-badge' => array(
						'singular' => __( 'Community Badge', 'badgeos-badgestack' ),
						'plural'   => __( 'Community Badges', 'badgeos-badgestack' ),
					),
				'quest-badge' => array(
						'singular' =>  __( 'Quest Badge', 'badgeos-badgestack' ),
						'plural'   =>  __( 'Quest Badges', 'badgeos-badgestack' ),
					),
				'quest' => array(
						'singular' => __( 'Quest', 'badgeos-badgestack' ),
						'plural'   => __( 'Quests', 'badgeos-badgestack' ),
					),
				'level' => array(
						'singular' => __( 'Badge Level', 'badgeos-badgestack' ),
						'plural'   => __( 'Badge Levels', 'badgeos-badgestack' ),
					),
			);
			foreach ( $achievement_types as $type => $name ) {

				// Register our achievement type
				$this->register_achievement_type( $name['singular'], $name['plural'] );

				// Store it's sanitized singular title so we know what to use for post_type registering posts
				// Note: we do this because the singular title is translatable, and can therefore change
				$this->type[$type] = sanitize_title( $name['singular'] );
			}

			// Loop through all of our sample data and create each achievement
			foreach ( $this->get_achievement_posts() as $achievement ) {
				$this->create_achievement_post( $achievement );
			}

			// Update any steps awaiting final connections
			$this->update_step_connections();

			// Finally, setup option for secondary activation functions
			add_option( 'BadgeOS_BadgeStack_Activated', true );

		}

	}

	/**
	 * Secondary Activation Functions
	 *
	 * This is for things that have to run just AFTER we've activated,
	 * And we only want them to ever run the one time.
	 *
	 * @since 1.0.0
	 */
	function after_activate() {

		// If we've just completed activation, flush rewrite rules
		if ( get_option( 'BadgeOS_BadgeStack_Activated' ) ) {
			flush_rewrite_rules();
			delete_option( 'BadgeOS_BadgeStack_Activated' );
		}
	}

	/**
	 * Check if BadgeOS is available
	 *
	 * @since  1.0.0
	 * @return bool True if BadgeOS is available, false otherwise
	 */
	public static function meets_requirements() {

		if ( class_exists('BadgeOS') )
			return true;
		else
			return false;

	}

	/**
	 * Generate a custom error message and deactivates the plugin if we don't meet requirements
	 *
	 * @since 1.0.0
	 */
	public function maybe_disable_plugin() {

		if ( ! $this->meets_requirements() ) {
		// Display our error
	    echo '<div id="message" class="error">';
	    echo '<p>' . sprintf( __( 'BadgeOS BadgeStack Add-On requires BadgeOS and has been <a href="%s">deactivated</a>. Please install and activate BadgeOS and then reactivate this plugin.', 'badgeos-badgestack' ), admin_url( 'plugins.php' ) ) . '</p>';
	    echo '</div>';

	    // Deactivate our plugin
	    deactivate_plugins( $this->basename );
		}
	}

	/**
	 * Helper function for registering our different achievement types
	 *
	 * @since  1.0.0
	 * @param  string  $singular_name    The singular name for the achiement
	 * @param  string  $plural_name      The plural name for the achievement
	 */
	private function register_achievement_type( $singular_name = '', $plural_name = '' ) {

		// Only create the achievement type post if it doesn't already exist
		$badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array(); 
		if ( ! get_page_by_title( $singular_name, 'OBJECT', trim( $badgeos_settings['achievement_main_post_type'] ) ) ) {
			$achievement_post_id = wp_insert_post( array(
				'post_title'  => $singular_name,
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type'   => trim( $badgeos_settings['achievement_main_post_type'] ),
			) );
			update_post_meta( $achievement_post_id, '_badgeos_singular_name', $singular_name );
			update_post_meta( $achievement_post_id, '_badgeos_plural_name', $plural_name );
			update_post_meta( $achievement_post_id, '_badgeos_show_in_menu', true );
		}

		// Only create the achievement type page if it doesn't already exist
		if ( ! get_page_by_title( $plural_name, 'OBJECT', 'page' ) ) {
			$achievement_page_id = wp_insert_post( array(
				'post_title'   => $plural_name,
				'post_content' => '[badgeos_achievements_list type="' . sanitize_title( $singular_name ) . '" limit=20]',
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_type'    => 'page',
			) );
		}

	}

	/**
	 * Helper function for creating a new post for a given achievement type
	 *
	 * @since  1.0.0
	 * @param  array  $args All of our relevant args
	 * @return bool         True on success, false on failure
	 */
	private function create_achievement_post( $args = array() ) {

		// Setup our defaults
		$defaults = array(
			'post_type'    => 'badge',
			'post_status'  => 'publish',
			'post_title'   => '',
			'post_author'  => 1,
			'post_content' => '',
			'post_excerpt' => '',
			'thumbnail'    => '',
			'menu_order'   => 0,
			'post_meta'    => array(
				'_badgeos_points'               => 0,
				'_badgeos_earned_by'            => 'triggers',
				'_badgeos_sequential'           => false,
				'_badgeos_points_required'      => '',
				'_badgeos_congratulations_text' => '',
				'_badgeos_maximum_earnings'     => '',
				'_badgeos_hidden'               => false
			),
			'steps' => array()
		);
		$args = wp_parse_args( $args, $defaults );

		// If our post doesn't already exist, let's make it
		if ( ! get_page_by_title( $args['post_title'], 'OBJECT', $args['post_type'] ) ) {

			// Create our post
			$achievement_post_id = wp_insert_post( array(
				'post_type'    => $args['post_type'],
				'post_title'   => $args['post_title'],
				'post_author'  => $args['post_author'],
				'post_content' => $args['post_content'],
				'post_excerpt' => $args['post_excerpt'],
				'post_status'  => $args['post_status'],
				'menu_order'   => $args['menu_order']
			), true );

			// If we successfully made a new post
			if ( ! is_wp_error( $achievement_post_id ) ) {

				// Setup our thumbnail
				if ( $args['thumbnail'] ) {
					$tmp = download_url( trailingslashit( $this->directory_url ) . 'images/' . $args['thumbnail'] );
					if ( ! is_wp_error( $tmp ) ) {
						$thumbnail_id = media_handle_sideload( array( 'name' => $args['thumbnail'], 'tmp_name' => $tmp ), $achievement_post_id );
						if ( ! is_wp_error( $thumbnail_id ) )
							set_post_thumbnail( $achievement_post_id, $thumbnail_id );
					}
					// @unlink($tmp);
				}

				// Insert our meta
				foreach ( $args['post_meta'] as $key => $value ) {
					update_post_meta( $achievement_post_id, $key, $value );
				}

				// Create our steps
				if ( !empty( $args['steps'] ) ) {
					foreach ( $args['steps'] as $step_args ) {
						$this->create_achievement_step( $achievement_post_id, $step_args );
					}
				}

				// Let it be known that we successfully created a post
				return true;
			}
		}

		// If we made it here, no new post was created
		return false;
	}

	/**
	 * Helper function for creating a new step for a given achievement post
	 *
	 * @since  1.0.0
	 * @param  integer $achievement_id The post ID of our parent achievement
	 * @param  array   $args           All of our relevant args
	 * @return bool                    True on success, false on failure
	 */
	private function create_achievement_step( $achievement_id = 0, $args = array() ) {

		// Setup our defaults
		$defaults = array(
			'post_title'       => '',
			'count'            => 1,
			'trigger_type'     => '',
			'achievement_type' => '',
			'achievement_post' => '',
			'order'            => 0
		);
		$args = wp_parse_args( $args, $defaults );

		// Create our step
		$badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
		$step_id = wp_insert_post( array(
			'post_type'   => trim( $badgeos_settings['achievement_step_post_type'] ),
			'post_status' => 'publish',
			'post_author' => 1,
			'post_title'  => $args['post_title'],
		), true );

		// If we made a step
		if ( ! is_wp_error( $step_id ) ) {

			// Create the P2P connection from the step to the achievement
			$badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array(); 
			$p2p_id = p2p_create_connection(
				trim( $badgeos_settings['achievement_step_post_type'] ).'-to-' . get_post_type( $achievement_id ),
				array(
					'from' => $step_id,
					'to'   => $achievement_id,
					'meta' => array(
						'date'  => current_time( 'mysql' ),
						'order' => $args['order']
					)
				)
			);

			// Update our relevant meta
			update_post_meta( $step_id, '_badgeos_count', $args['count'] );
			update_post_meta( $step_id, '_badgeos_trigger_type', $args['trigger_type'] );
			update_post_meta( $step_id, '_badgeos_achievement_type', $args['achievement_type'] );

			// If the step is triggered by a specific achievement, setup that connection
			if ( 'specific-achievement' == $args['trigger_type'] ) {

				// Setup our steps awaiting connections array
				if ( !isset( $this->steps_awaiting_connection ) )
					$this->steps_awaiting_connection = array();

				// Update our awaiting connection array to include this step
				$this->steps_awaiting_connection[$step_id] = array(
					'post_title' => $args['achievement_post'],
					'post_type'  => $args['achievement_type']
				);
			}

			// Let it be known that we successfully created a step
			return true;
		}


		// If we made it here, no new step was created
		return false;
	}

	/**
	 * Helper function to create connections for steps that need them
	 *
	 * @since 1.0.0
	 */
	private function update_step_connections() {

		// If we have any steps awaiting a connection...
		if ( isset( $this->steps_awaiting_connection ) && !empty( $this->steps_awaiting_connection ) ) {

			// Loop through our steps awaiting connection and setup each connection
			$badgeos_settings = ( $exists = get_option( 'badgeos_settings' ) ) ? $exists : array();
			foreach ( $this->steps_awaiting_connection as $step_id => $args ) {
				$achievement = get_page_by_title( $args['post_title'], 'OBJECT', $args['post_type'] );
				p2p_create_connection(
					$args['post_type'] . '-to-'.trim( $badgeos_settings['achievement_step_post_type'] ),
					array(
						'from' => $achievement->ID,
						'to'   => $step_id,
						'meta' => array(
							'date' => current_time('mysql')
						)
					)
				);
			}

			// Empty out our steps awaiting connection array
			$this->steps_awaiting_connection = array();

		}
	}

	/**
	 * Return an array of all sample achievement posts
	 *
	 * @since 1.0.0
	 */
	private function get_achievement_posts() {
		return array(
			// Community Badges
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Award author when user visits post', 'badgeos-badgestack' ),
				'post_content' => __( "The administrator can award achievements, points or ranks and Award author when user visits post. Points will be awarded/deducted and Award author when user visits post.", 'badgeos-badgestack' ),
				'post_excerpt' => __( "The administrator can award achievements, points or ranks and Award author when user visits post. Points will be awarded/deducted and Award author when user visits post.", 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 75,
					'_badgeos_earned_by'            => 'points',
					'_badgeos_points_required'      => 500,
					'_badgeos_congratulations_text' => __( "Congratulations!! you have earned a badge level Award author when user visits post.", 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
			),
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Register to the website', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user registers to the website. This achievement will be awarded one time only.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user registers to the website. This achievement will be awarded one time only.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 75,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Thank you for taking time to Register to the website. Your contributions make our learning experiences more vibrant and meaningful.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Visit this site at least 5 times', 'badgeos-badgestack' ),
						'count'            => 5,
						'trigger_type'     => 'wp_login',
						'order'            => 0,
					),
					array(
						'post_title'       => __( "Comment at least 10 times on others' posts", 'badgeos-badgestack' ),
						'count'            => 10,
						'trigger_type'     => 'badgeos_new_comment',
						'order'            => 1,
					),
				),
			),
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Daily visit website', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user daily visit website. Points will be awarded/deducted when user daily visit website.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user daily visit website. Points will be awarded/deducted when user daily visit website.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations!! you have earned a badge level Daily visit website.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Do any 3 Quests', 'badgeos-badgestack' ),
						'count'            => 3,
						'trigger_type'     => 'any-achievement',
						'achievement_type' => $this->type['quest'],
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Earn any 2 Community Badges', 'badgeos-badgestack' ),
						'count'            => 2,
						'trigger_type'     => 'any-achievement',
						'achievement_type' => $this->type['community-badge'],
						'order'            => 1,
					),
					array(
						'post_title'       => __( 'Post at least 20 comments', 'badgeos-badgestack' ),
						'count'            => 20,
						'trigger_type'     => 'badgeos_new_comment',
						'order'            => 2,
					),
					array(
						'post_title'       => __( 'Complete All Levels', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'all-achievements',
						'achievement_type' => $this->type['level'],
						'order'            => 3,
					),
				),
			),
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'After completing the number of years', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user completes several number of years on the website. Points will be awarded/deducted on the user completes several number of years.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user completes several number of years on the website. Points will be awarded/deducted on the user completes several number of years.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 100,
					'_badgeos_earned_by'            => 'nomination',
					'_badgeos_congratulations_text' => __( 'Congratulations!! you have successfully spent X number of years.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'hidden',
				),
				'steps' => array(),
			),

			// Levels
			array(
				'post_type'    => $this->type['level'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Visit a Post', 'badgeos-badgestack' ),
				'post_content' => __( "The administrator can award achievements, points or ranks if the user visit a post. Points will be awarded/deducted on the user visits any post.", 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user visit a post. Points will be awarded/deducted on the user visits any post.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations! You have completed the Level "Visit a Post" and are on your way to becoming a go-to resource on alternative forms of recognizing achievement.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn the Badge: “Visit a Post”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest-badge'],
						'achievement_post' => __( 'Visit a Post', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['level'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Log in for X days', 'badgeos-badgestack' ),
				'post_content' => __( "The administrator can award achievements, points or ranks if the user log in for x days. With this trigger, you can set an achievement or even deduct the user’s BadgeOS points, if the user logged in for “X” number of days.", 'badgeos-badgestack' ),
				'post_excerpt' => __( "The administrator can award achievements, points or ranks if the user log in for x days. With this trigger, you can set an achievement or even deduct the user’s BadgeOS points, if the user logged in for “X” number of days.", 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations! You have successfully earned the log in for x days Badge Level.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn the Badge: “Publish a new Page”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest-badge'],
						'achievement_post' => __( 'Publish a new Page', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Earn the Badge: “Comment on a specific post”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Comment on a specific post', 'badgeos-badgestack' ),
						'order'            => 1,
					),
				),
			),

			// Quests
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Not Login for X days', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user not login for x days. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user has not logged in for X number of days.', 'badgeos-badgestack' ),
				'post_excerpt' => __( "The administrator can award achievements, points or ranks if the user not login for x days. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user has not logged in for X number of days.", 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'submission_auto',
					'_badgeos_congratulations_text' => __( 'Congratulations! You have earned the not login for x days Badge Level.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(),
			),
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Comment on a Post', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user comments on a post. Points will be awarded/deducted when a user comments on any post.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user comments on a post. Points will be awarded/deducted when a user comments on any post.', 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'submission_auto',
					'_badgeos_congratulations_text' => __( 'Congratulations!! You have earned the badge level comment on a post. ', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(),
			),				
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Comment on a specific post', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user comments on a specific post. Points will be awarded/deducted when a user comments on a specific post.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user comments on a specific post. Points will be awarded/deducted when a user comments on a specific post.', 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations!! You have earned the badge level comment on a specific post.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn “Not Login for X days” 30 times.', 'badgeos-badgestack' ),
						'count'            => 30,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Not Login for X days', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Earn “Comment on a Post” 1 time.', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Comment on a Post', 'badgeos-badgestack' ),
						'order'            => 2,
					),
				),
			),

			// Quest Badges
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Visit a Post', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user visit a post. Points will be awarded/deducted on the user visits any post.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user visit a post. Points will be awarded/deducted on the user visits any post.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations! You have completed the Level "Visit a Post" and are on your way to becoming a go-to resource on alternative forms of recognizing achievement.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Comment on a specific post', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Comment on a specific post', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Publish a new Post', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user publish a new post. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user publish a new post.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'The administrator can award achievements, points or ranks if the user publish a new post. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user publish a new post.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'                     => 0,
					'_badgeos_earned_by'                  => 'triggers',
					'_badgeos_sequential'                 => false,
					'_badgeos_congratulations_text'       => __( 'Congratulations!! you have earned the badge level publish a new page.', 'badgeos-badgestack' ),
					'_badgeos_hidden'                     => 'show',
					// '_badgeos_send_to_credly'             => 1,
					// '_badgeos_credly_include_testimonial' => 1,
					// '_badgeos_credly_categories'          => array(
					// 	'e-learning'            => 2985,
					// 	'e-learning (pedagogy)' => 3109,
					// 	'web based training'    => 2987
					// )
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Comment on a Post', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Comment on a Post', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Publish a new Page', 'badgeos-badgestack' ),
				'post_content' => __( 'The administrator can award achievements, points or ranks if the user publish a new page. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user publish a new page.', 'badgeos-badgestack' ),
				'post_excerpt' => __( "The administrator can award achievements, points or ranks if the user publish a new page. With this trigger, you can set an achievement or even deduct the users BadgeOS points, if the user publish a new page.", 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'                     => 500,
					'_badgeos_earned_by'                  => 'triggers',
					'_badgeos_sequential'                 => false,
					'_badgeos_congratulations_text'       => __( 'Congratulations!! you have earned the badge level publish a new page.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'           => 1,
					'_badgeos_hidden'                     => 'show',
					// '_badgeos_send_to_credly'             => 1,
					// '_badgeos_credly_include_testimonial' => 1,
					// '_badgeos_credly_categories'          => array(
					// 	'e-learning'            => 2985,
					// 	'e-learning (pedagogy)' => 3109,
					// 	'adult education'       => 3095,
					// 	'development of educational opportunities' => 3059
					// )
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Comment on a Post', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Comment on a Post', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Quest: Not Login for X Days', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Not Login for X Days', 'badgeos-badgestack' ),
						'order'            => 1,
					),
				),
			),
		);
	}

}
$GLOBALS['badgeos_badgestack'] = new BadgeOS_BadgeStack();
