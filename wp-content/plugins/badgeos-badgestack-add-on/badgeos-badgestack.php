<?php
/**
 * Plugin Name: BadgeOS BadgeStack Add-On
 * Plugin URI: http://www.learningtimes.com/
 * Description: This BadgeOS add-on automatically creates achievement types, pages and sample content to jumpstart your own badging system.
 * Author: Credly
 * Version: 1.0.1
 * Author URI: https://credly.com/
 * License: GNU AGPL
 */

/*
 * Copyright © 2012-2013 Credly, LLC
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

class BadgeOS_BadgeStack {

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
						'singular' => __( 'Level', 'badgeos-badgestack' ),
						'plural'   => __( 'Levels', 'badgeos-badgestack' ),
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
		if ( ! get_page_by_title( $singular_name, 'OBJECT', 'achievement-type' ) ) {
			$achievement_post_id = wp_insert_post( array(
				'post_title'  => $singular_name,
				'post_status' => 'publish',
				'post_author' => 1,
				'post_type'   => 'achievement-type',
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
					@unlink($tmp);
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
		$step_id = wp_insert_post( array(
			'post_type'   => 'step',
			'post_status' => 'publish',
			'post_author' => 1,
			'post_title'  => $args['post_title'],
		), true );

		// If we made a step
		if ( ! is_wp_error( $step_id ) ) {

			// Create the P2P connection from the step to the achievement
			$p2p_id = p2p_create_connection(
				'step-to-' . get_post_type( $achievement_id ),
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
			foreach ( $this->steps_awaiting_connection as $step_id => $args ) {
				$achievement = get_page_by_title( $args['post_title'], 'OBJECT', $args['post_type'] );
				p2p_create_connection(
					$args['post_type'] . '-to-step',
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
				'post_title'   => __( 'Active Learner', 'badgeos-badgestack' ),
				'post_content' => __( "This badge rewards active participation on our site. Complete Quests, earn other badges, comment meaningfully on others' contributions and posts: all will contribute to your achievement of this internal community badge. (Earn 500 points in our community to earn this badge.)", 'badgeos-badgestack' ),
				'post_excerpt' => __( "Rewards active participation right here in our learning community. Complete quests, earn other badges, comment meaningfully on others' contributions and posts: all will contribute to your achievement of this internal community badge.", 'badgeos-badgestack' ),
				'thumbnail'    => 'community-badge-activelearner.png',
				'post_meta'    => array(
					'_badgeos_points'               => 75,
					'_badgeos_earned_by'            => 'points',
					'_badgeos_points_required'      => 500,
					'_badgeos_congratulations_text' => __( "You earned 500 points by interacting with this online community. This makes you a very active learner, and we're rewarding you with a community badge to show for it.", 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
			),
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Commentator', 'badgeos-badgestack' ),
				'post_content' => __( 'This badge recognizes someone who engages deeply with our online learning community, taking the time to share feedback and insights based on the contributions of others.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'This badge recognizes someone who engages deeply with our online learning community, taking the time to share feedback and insights based on the contributions of others.', 'badgeos-badgestack' ),
				'thumbnail'    => 'community-badge-commentator.png',
				'post_meta'    => array(
					'_badgeos_points'               => 75,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Thank you for taking time to consistently share feedback and insights with others in our online community. Your contributions make our learning experiences more vibrant and meaningful.', 'badgeos-badgestack' ),
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
						'trigger_type'     => 'comment_post',
						'order'            => 1,
					),
				),
			),
			array(
				'post_type'    => $this->type['community-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Community Activist', 'badgeos-badgestack' ),
				'post_content' => __( 'The earner of this badge has participated in every core aspect of this online community, including completing the both levels, multiple quests and badges, and has engaged with peers in active dialogue and the exchange of ideas.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'Rewarded to those who take full advantage of available opportunities to explore new information and exchange ideas with peers.', 'badgeos-badgestack' ),
				'thumbnail'    => 'community-badge-activist.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'You are an exceedingly active learner who takes full advantage of available opportunities to explore new information and exchange ideas with peers.', 'badgeos-badgestack' ),
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
						'trigger_type'     => 'comment_post',
						'order'            => 2,
					),
					array(
						'post_title'       => __( 'Complete Both Levels', 'badgeos-badgestack' ),
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
				'post_title'   => __( 'Innovator', 'badgeos-badgestack' ),
				'post_content' => __( 'This badge recognizes someone who develops innovative strategies and shares new perspectives. Nominate a member of this community who you think epitomizes the role of "innovator".', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'This nomination-based badge recognizes someone who develops innovative strategies and shares new perspectives.', 'badgeos-badgestack' ),
				'thumbnail'    => 'community-badge-innovator',
				'post_meta'    => array(
					'_badgeos_points'               => 100,
					'_badgeos_earned_by'            => 'nomination',
					'_badgeos_congratulations_text' => __( 'You have been recognized by your peers as an innovative thinker and problem solver.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'hidden',
				),
				'steps' => array(),
			),

			// Levels
			array(
				'post_type'    => $this->type['level'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Adventures in Badging', 'badgeos-badgestack' ),
				'post_content' => __( "In this Level you'll learn about the potential for digital badges, and think about skills, behaviors, roles or milestones that often go unrecognized -- but deserve to be acknowledged.", 'badgeos-badgestack' ),
				'post_excerpt' => __( 'Learn about the potential for digital badges and think about skills, behaviors, roles or milestones that often go unrecognized -- but that deserve to be acknowledged.', 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations! You have completed the Level "Adventures in Badging" and are on your way to becoming a go-to resource on alternative forms of recognizing achievement.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn the Badge: “Badge Explorer”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest-badge'],
						'achievement_post' => __( 'Badge Explorer', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['level'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Begin Earning & Issuing Badges', 'badgeos-badgestack' ),
				'post_content' => __( "In this Level you’ll set-up a profile on Credly so you can earn, store, and share badges. You'll also issue a badge to one or more people who demonstrate skills, values or behaviors you have witnessed first-hand.", 'badgeos-badgestack' ),
				'post_excerpt' => __( "In this Level you’ll set-up a profile on Credly so you can earn, store, and share badges. You'll also issue a badge to one or more people who demonstrate skills, values or behaviors you have witnesses first-hand.", 'badgeos-badgestack' ),
				'thumbnail'    => 'level-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'Congratulations! You have completed the Level "Start Your Badge Profile" and now have a way to curate all of your achievements, and to recognize the skills and behaviors you value in the world! Add your Credly profile link to your resume or email footer and share your achievements with the world.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn the Badge: “Credly Member”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest-badge'],
						'achievement_post' => __( 'Credly Member', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Earn the Badge: “Badge Issuer”', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest-badge'],
						'achievement_post' => __( 'Badge Issuer', 'badgeos-badgestack' ),
						'order'            => 1,
					),
				),
			),

			// Quests
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Customize Your Profile', 'badgeos-badgestack' ),
				'post_content' => __( 'Your Credly profile is like a dynamic resume that helps others appreciate your skills and accomplishments. Each badge is validated by a person or organization that has observed your achievements first hand.

					In this Quest, you\'ll complete your Credly profile and explore the various options for sharing your badges so that you can curate how you showcase your achievements to others.

					Now that you have a <a href="https://credly.com" target="_blank">Credly</a> account, login and visit the "Account Settings" area from the top right menu:
					<ol>
						<li>Hover your mouse over the "About Me" section and click the pencil to add some about information about yourself.</span></li>
						<li>Click the pencil next to "Email" and add any email addresses you use. This will allow you to automatically consolidate achievements you receive from any person or organization in your Credly account, regardless of what email address they use when they issue you badges.</li>
						<li>Use the "Social Settings and Auto Share" area to link your social networks. This makes sharing your badges wherever you want them super easy.</li>
						<li>Hover over the avatar area and add an image of yourself.</li>
						<li>Click "<strong>Save Changes</strong>".</li>
						<li>Then visit the "My Credit" area of Credly to explore how to manage your badges, share them on social networks and create Categories.</li>
					</ol>
					When you are done, use the submission area to list at least one group or person who might issue you badges to recognize your achievements.', 'badgeos-badgestack' ),
				'post_excerpt' => __( "In this Quest, you'll complete your Credly profile and explore the various options for sharing your badges so that you can curate how you showcase your achievements to others.", 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'submission_auto',
					'_badgeos_congratulations_text' => __( 'You have set-up and customized your Credly.com profile, so you are ready to begin earning and sharing credit you receive for your achievements.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(),
			),
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Give Someone Credit', 'badgeos-badgestack' ),
				'post_content' => __( 'We know that a badge says something about a person who earns it, but what does a badge say about the issuer?

					When an observer of an earned badge looks at its criteria and the evidence that someone achieved something, what will they learn about the organization or individual who decided to bestow the badge? What does a badge say about what you value in the world?

					In this Quest, you will define a badge that represents the achievement of something you value. For example, it could be the demonstration of a skill, an important behavior or attitude, or the reaching of a milestone that matters. You will create a badge for this achievement, and then you will issue it someone who deserves it. Here\'s how:
					<ol>
						<li><span style="line-height: 15px;">Sign into your <a href="https://credly.com" target="_blank">Credly</a> account.</span></li>
						<li>Click "Give" at the top of the page.</li>
						<li>Design and define a badge that acknowledges something important to you or your work. Follow the steps to save and then give the badge to one or more people who deserves it.</li>
					</ol>

					After you give a badge, describe in the submission here the name of the badge and the criteria, or link to the badge on Credly.
					', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'In this Quest you will define a badge that represents the achievement of something you value. You will create a badge for this achievement, and then you will issue it someone who deserves it.', 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'submission_auto',
					'_badgeos_congratulations_text' => __( 'You have considered what you value and have defined, designed and issued a badge on Credly.com that acknowledges these values in practice in the world. ', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(),
			),
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Join Credly', 'badgeos-badgestack' ),
				'post_content' => __( '<a href="https://credly.com">Credly</a> is universal way for you to earn, store and showcase achievements throughout your life.  Achievements are represented by badges, which you can earn in virtually every aspect of your life, from school to the workplace to museums and fitness activities.

					Badges you earn for the learning you do right here on this site will be sent to Credly, where you can make them visible on social networks you use, on your blog, or your own web site. Every person on Credly also has a public "profile." You can curate what achievements you\'d like to showcase at any time on your Credly Profile.

					In this Quest, you\'ll visit <strong><a href="https://credly.com" target="_blank">Credly.com</a></strong> and join, creating a free account.

					Once you have created your account, visit the "View Profile" page from the user menu at the top right of the page and then copy the web address for your Profile page into the submission box here. (It will look something like "https://credly.com/u/jonathan".)', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'Visit Credly.com and create a free account where you can receive, showcase and curate your achievements -- including those you earn while on this site.', 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'submission_auto',
					'_badgeos_congratulations_text' => __( 'Credly empowers people to earn and give credit wherever it is due -- empowering everyone to acknowledge, share and reward achievement. Today, we give you credit for being a pioneer and a part of this growing movement by joining the Credly community.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(),
			),
			array(
				'post_type'    => $this->type['quest'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Watch a Video: Learn about Badges', 'badgeos-badgestack' ),
				'post_content' => __( 'In this Quest, you\'ll watch a video presentation entitled, "Giving Credit Where Credit is Due: Adventures in Digital Credentials and Badges". The talk was led by Jonathan Finkelstein, Founder of <a href="https://credly.com" target="_blank">Credly</a> and director of the BadgeStack Project, at an annual meeting about transforming education through technology.

					Watch the video below, and then in the submission box, share a brief comment or reflection about one action, milestone, behavior, or skill that people should get credit for achieving. Focus on something that people don\'t currently get properly acknowledged for having done.

					<iframe src="http://educause.mediasite.com/mediasite/Play/2a2738935a3649b7a97c32449a017a641d" height="560" width="100%" frameborder="0" scrolling="no"></iframe>

					Source: Original video available on the <a href="http://bit.ly/educause-finkelstein" target="_blank">EDUCAUSE</a> web site.

					<strong>Quest Question:</strong> Describe at least one action, milestone, behavior, or skill that people should get credit for achieving, but which usually go unrecognized or unnoticed.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'Watch a video about the potential for digital badges and share ideas about actions, milestones, behaviors, or skills that people should get credit for achieving, but which usually go unrecognized or unnoticed.', 'badgeos-badgestack' ),
				'thumbnail'    => 'quest-default.png',
				'post_meta'    => array(
					'_badgeos_points'               => 150,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'You are continuing your exploration about the power of digital badges and thinking about how they can be used to acknowledge valued -- but often unrecognized -- achievements.  You can re-visit or share the video you watched at any time: http://bit.ly/educause-finkelstein.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'     => 1,
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Earn “Customize Your Profile” 30 times.', 'badgeos-badgestack' ),
						'count'            => 30,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Customize Your Profile', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Earn “Give Someone Credit” 1 time.', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Give Someone Credit', 'badgeos-badgestack' ),
						'order'            => 2,
					),
				),
			),

			// Quest Badges
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Badge Explorer', 'badgeos-badgestack' ),
				'post_content' => __( 'A badge is a validated display of accomplishment, skill, quality or interest that can be earned in any environment. Badges can represent traditional academic achievement or the development of skills or knowledge outside traditional education. The earner of this badge has taken steps to learn about this new form of achievement recognition and to consider its potential.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'A badge is a validated display of accomplishment, skill, quality or interest that can be earned in any environment. The earner of this badge has taken steps to learn about this unique form of achievement recognition and to consider its potential.', 'badgeos-badgestack' ),
				'thumbnail'    => 'badge-badgeexplorer-pink.png',
				'post_meta'    => array(
					'_badgeos_points'               => 0,
					'_badgeos_earned_by'            => 'triggers',
					'_badgeos_sequential'           => false,
					'_badgeos_congratulations_text' => __( 'You are taking steps to explore the potential for badges and the unique possibilities this form of achievement recognition affords to learners throughout their lives.', 'badgeos-badgestack' ),
					'_badgeos_hidden'               => 'show',
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Watch a Video: "Learn about Badges"', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Watch a Video: Learn about Badges', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Badge Issuer', 'badgeos-badgestack' ),
				'post_content' => __( 'A badge says as much about the person or group who issued it as it does about the recipient.  To earn this "Issuer" badge, you will think about something you value in the world, define the criteria by which you and others will know that value has been exhibited, and then identify someone who deserves to be recognized.', 'badgeos-badgestack' ),
				'post_excerpt' => __( 'To earn this "Issuer" badge, you will think about something you value in the world, define the criteria by which you and others will know that value has been exhibited, and then identify someone who deserves to be recognized.', 'badgeos-badgestack' ),
				'thumbnail'    => 'badge-badge-issuer.png',
				'post_meta'    => array(
					'_badgeos_points'                     => 0,
					'_badgeos_earned_by'                  => 'triggers',
					'_badgeos_sequential'                 => false,
					'_badgeos_congratulations_text'       => __( 'You have taken the time to consider what you value in the world, you have defined a badge that represents those values, and you have identified and issued the badge to someone deserving of recognition.', 'badgeos-badgestack' ),
					'_badgeos_hidden'                     => 'show',
					'_badgeos_send_to_credly'             => 1,
					'_badgeos_credly_include_testimonial' => 1,
					'_badgeos_credly_categories'          => array(
						'e-learning'            => 2985,
						'e-learning (pedagogy)' => 3109,
						'web based training'    => 2987
					)

				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Give Someone Credit', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Give Someone Credit', 'badgeos-badgestack' ),
						'order'            => 0,
					),
				),
			),
			array(
				'post_type'    => $this->type['quest-badge'],
				'post_status'  => 'publish',
				'post_author'  => 1,
				'post_title'   => __( 'Credly Member', 'badgeos-badgestack' ),
				'post_content' => __( 'To receive this badge, the earner needs to create a free profile on Credly to receive, manage and share their lifelong achievements, and they need to issue a badge to someone else for a valued accomplishment or contribution that they have observed first-hand.', 'badgeos-badgestack' ),
				'post_excerpt' => __( "This badge is earned by creating a profile on Credly to receive, manage and share one's lifelong credentials and achievements, and by issuing a badge to someone else for a valued accomplishment or contribution.", 'badgeos-badgestack' ),
				'thumbnail'    => 'badge-credly-member-grey.png',
				'post_meta'    => array(
					'_badgeos_points'                     => 500,
					'_badgeos_earned_by'                  => 'triggers',
					'_badgeos_sequential'                 => false,
					'_badgeos_congratulations_text'       => __( 'Credly empowers people to earn and give credit wherever it is due -- empowering everyone to acknowledge, share and reward achievement. Today, we give you credit for being a pioneer and a part of this growing movement by joining the Credly community.', 'badgeos-badgestack' ),
					'_badgeos_maximum_earnings'           => 1,
					'_badgeos_hidden'                     => 'show',
					'_badgeos_send_to_credly'             => 1,
					'_badgeos_credly_include_testimonial' => 1,
					'_badgeos_credly_categories'          => array(
						'e-learning'            => 2985,
						'e-learning (pedagogy)' => 3109,
						'adult education'       => 3095,
						'development of educational opportunities' => 3059
					)
				),
				'steps' => array(
					array(
						'post_title'       => __( 'Quest: Create a free account on Credly.com', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Join Credly', 'badgeos-badgestack' ),
						'order'            => 0,
					),
					array(
						'post_title'       => __( 'Quest: Customize Your Credly Profile', 'badgeos-badgestack' ),
						'count'            => 1,
						'trigger_type'     => 'specific-achievement',
						'achievement_type' => $this->type['quest'],
						'achievement_post' => __( 'Customize Your Profile', 'badgeos-badgestack' ),
						'order'            => 1,
					),
				),
			),
		);
	}

}
$GLOBALS['badgeos_badgestack'] = new BadgeOS_BadgeStack();
