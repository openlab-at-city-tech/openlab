<?php
/**
 * Widgets.
 *
 * @author Paul Gibbs <paul@byotos.com>
 * @package Achievements
 * @subpackage widgets
 *
 * $Id: achievements-widgets.php 1003 2011-10-04 20:15:19Z DJPaul $
 */

/**
 * Sitewide Leaderboard widget.
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Sitewide_Leaderboard extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function DPA_Sitewide_Leaderboard() {
		$widget_ops = array( 'classname' => 'achievements-sitewide-leaderboard', 'description' => __( 'Sitewide leaderboard', 'dpa' ) );
		$this->WP_Widget( 'achievements-sitewide', __( 'Achievements Leaderboard', 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-sitewide' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 * @todo Figure out a way to unify the output. Remove table?
	 */
	function widget( $args, $instance ) {
		global $bp;

		extract( $args, EXTR_SKIP );
		if ( !$high_scorers = dpa_points_get_high_scorers( $instance['limit'] ) )
			return;

		$high_scorers_count = count( $high_scorers );

		$appearance = apply_filters( 'dpa_widget_title', $instance['style'] );
		if ( empty( $appearance ) )  // Handle widget changes from v2.0.3 to v2.0.4.
			$appearance = 'leaguetable';

		// Figure out the upper range for the percentage bars
		if ( 'modern' == $appearance ) {
			$high_scorers_max_count = 0;

			foreach ( $high_scorers as $high_scorer )
				if ( $high_scorer->points > $high_scorers_max_count )
					$high_scorers_max_count = $high_scorer->points;
		}

		echo $before_widget;
		echo $before_title;
		echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
		echo $after_title;

		if ( 'leaguetable' == $appearance ) :
		?>

			<table class="achievements-leaderboard-leaguetable">
				<thead>
					<tr>
						<th><abbr title="<?php _e( 'Position', 'dpa' ) ?>"><?php /* translators: column heading, indicating numerical position in a list */ _e( '#', 'dpa' ) ?></th>
						<th><?php _e( 'Member', 'dpa' ) ?></th>
						<th><?php _e( 'Score', 'dpa' ) ?></th>
					</tr>
				</thead>
				<tbody>
					<?php for ( $i=0; $i<$high_scorers_count; $i++ ) : ?>
						<tr class="<?php if ( !empty( $bp->loggedin_user->id ) && $bp->loggedin_user->id == $high_scorers[$i]->id ) echo 'logged-in' ?>">
							<td><?php echo $i+1 ?>.</td>
							<td><?php echo bp_core_fetch_avatar( array( 'height' => 20, 'item_id' => $high_scorers[$i]->id, 'type' => 'thumb', 'width' => 20 ) ) ?>&nbsp;<?php echo bp_core_get_userlink( $high_scorers[$i]->id ) ?></td>
							<td><?php echo bp_core_number_format( $high_scorers[$i]->points ) ?></td>
						</tr>
					<?php endfor; ?>
				</tbody>
			</table>

		<?php elseif ( 'modern' == $appearance ) : ?>

			<div class="achievements-leaderboard-modern">
				<?php for ( $i=0; $i<$high_scorers_count; $i++ ) : ?>
					<?php $percentage = ( $high_scorers[$i]->points / $high_scorers_max_count ) * 100; ?>
					<div class="<?php if ( !empty( $bp->loggedin_user->id ) && $bp->loggedin_user->id == $high_scorers[$i]->id ) echo 'logged-in' ?>" style="width: <?php echo $percentage ?>%">
						<a href="<?php echo bp_core_get_userlink( $high_scorers[$i]->id, false, true ); echo DPA_SLUG . '/' . DPA_SLUG_MY_ACHIEVEMENTS ?>"><?php echo bp_core_get_user_displayname( $high_scorers[$i]->id ) ?></a><span class="score"><?php echo bp_core_number_format( $high_scorers[$i]->points ) ?></span>
					</div>
				<?php endfor; ?>
			</div>

		<?php
		endif;

		do_action( 'dpa_sitewide_leaderboard_widget', $appearance, $args, $instance );
		echo $after_widget;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance  An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['limit'] = apply_filters( 'dpa_widget_limit_before_save', (int)$new_instance['limit'] );
		$instance['style'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['style'] ) );
		$instance['title'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'Achievement Leaderboard', 'dpa' ), 'limit' => 12, 'style' => 'leaguetable' ) );
		$limit = apply_filters( 'dpa_widget_limit', $instance['limit'] );
		$style = apply_filters( 'dpa_widget_title', $instance['style'] );
		$title = apply_filters( 'dpa_widget_title', $instance['title'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit to this many results', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="1" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'style' ); ?>"><?php _e( 'Appearance', 'dpa' ); ?></label><br />
			<select class="widefat" id="<?php echo $this->get_field_id( 'style' ); ?>" name="<?php echo $this->get_field_name( 'style' ); ?>">
			<?php
				$style_options = $this->dpa_get_sitewide_leaderboard_style_options();
				foreach ( $style_options as $name => $description ) {
					$selected = '';
					if ( $style == $name )
						$selected = 'selected="selected"';

					printf( '<option value="%1$s"%2$s>%3$s</option>', esc_attr( $name ), $selected, $description );
				}
			?>
			</select>
		</p>
	<?php
	}

	/**
	 * Get appearance styles for the site-wide leaderboard widget.
	 *
	 * @return array of key/value pairs
	 * @since 2.0.4
	 */
	function dpa_get_sitewide_leaderboard_style_options() {
		return array_merge( array( 'leaguetable' => __( 'League table', 'dpa' ), 'modern' => __( 'Modern', 'dpa' ) ), apply_filters( 'dpa_sitewide_leaderboard_style_options', array() ) );
	}
}

/**
 * Member Achievement widget (displays Achievement's pictures, that the user has unlocked).
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Member_Achievements extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function DPA_Member_Achievements() {
		$widget_ops = array( 'classname' => 'achievements-member-achievements', 'description' => __( "A photo grid of a member's unlocked Achievements", 'dpa' ) );
		$this->WP_Widget( 'achievements-member-achievements', __( 'Achievements, Unlocked', 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-member-achievements' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 */
	function widget( $args, $instance ) {
		global $achievements_template, $bp;

		if ( ( $instance['loggedin_user'] && empty( $bp->loggedin_user->id ) ) || ( !$instance['loggedin_user'] && empty( $bp->displayed_user->id ) ) )
			return;

		if ( $instance['loggedin_user'] )
			$user_id = $bp->loggedin_user->id;
		else
			$user_id = $bp->displayed_user->id;

		extract( $args, EXTR_SKIP );

		if ( dpa_has_achievements( array( 'user_id' => $user_id, 'max' => $instance['limit'], 'populate_extras' => false, 'type' => 'unlocked' ) ) ) :
			echo $before_widget;

			if ( $instance['title'] ) {
				echo $before_title;
				echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
				echo $after_title;
			}
			?>

			<div class="avatar-block">
				<?php while ( dpa_achievements() ) : dpa_the_achievement(); ?>
					<div class="item-avatar">
						<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
					</div>
				<?php endwhile; ?>
			</div>
			<p class="achievements-widget-showall"><a href="<?php echo esc_attr( bp_loggedin_user_domain() . DPA_SLUG . '/' . DPA_SLUG_MY_ACHIEVEMENTS ) ?>"><?php _e( 'Show All', 'dpa' ) ?></a></p>
			<div style="clear: right;"></div>

		<?php
		echo $after_widget;
		endif;

		$achievements_template = null;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );
		$instance['limit'] = apply_filters( 'dpa_widget_limit_before_save', (int)$new_instance['limit'] );
		$instance['loggedin_user'] = apply_filters( 'dpa_widget_bool_before_save', (bool)$new_instance['loggedin_user'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( "Your Recent Achievements", 'dpa' ), 'limit' => 12 ) );
		$title = apply_filters( 'dpa_widget_title', $instance['title'] );
		$limit = apply_filters( 'dpa_widget_limit', $instance['limit'] );
		$loggedin_user = apply_filters( 'dpa_widget_loggedin_user', !empty( $instance['loggedin_user'] ) ? $instance['loggedin_user'] : false );

		$checked = '';
		if ( $loggedin_user )
			$checked = 'checked="checked"';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit to this many Achievements', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="1" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'loggedin_user' ); ?>"><?php _e( "Always show as logged in member", 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'loggedin_user' ); ?>" name="<?php echo $this->get_field_name( 'loggedin_user' ); ?>" value="1" type="checkbox" <?php echo $checked; ?> />
		</p>

	<?php
	}
}

/**
 * Available Achievements widget; displays pictures of Achievements that are available on this site. Intended for WordPress multisite.
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Available_Achievements_Widget extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function dpa_available_achievements_widget() {
		$widget_ops = array( 'classname' => 'achievements-available-achievements', 'description' => __( "A photo grid of all the Achievements available on this site; intended for multisite", 'dpa' ) );
		$this->WP_Widget( 'achievements-available-achievements', __( 'Achievements, All', 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-available-achievements' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @global int $blog_id Site ID
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 */
	function widget( $args, $instance ) {
		global $achievements_template, $blog_id, $bp;

		extract( $args, EXTR_SKIP );

		if ( dpa_has_achievements( 'type=active&per_page=' . $instance['limit'] . '&max=' . $instance['limit'] . '&populate_extras=0' ) ) :
			echo $before_widget;

			if ( $instance['title'] ) {
				echo $before_title;
				echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
				echo $after_title;
			}

			$show_sitewide_achievements = false;
			if ( !$instance['showsitewide'] )
				$show_sitewide_achievements = true;
			?>

			<div class="avatar-block">
				<?php
				while ( dpa_achievements() ) :
					dpa_the_achievement();

					if ( !$show_sitewide_achievements && dpa_get_achievement_site_id() == -1 )
						continue;

					if ( is_multisite() && ( dpa_get_achievement_site_id() != $blog_id && dpa_get_achievement_site_id() != -1 ) )
						continue;
				?>
					<div class="item-avatar">
						<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
					</div>
				<?php endwhile; ?>
			</div>
			<p class="achievements-widget-showall"><a href="<?php dpa_achievements_permalink() ?>"><?php _e( 'Show All', 'dpa' ) ?></a></p>
			<div style="clear: right;"></div>

		<?php
		echo $after_widget;
		endif;

		$achievements_template = null;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );
		$instance['limit'] = apply_filters( 'dpa_widget_limit_before_save', (int)$new_instance['limit'] );
		$instance['showsitewide'] = apply_filters( 'dpa_widget_showsitewide_before_save', (bool)$new_instance['showsitewide'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( 'All Available Achievements', 'dpa' ), 'limit' => 12 ) );
		$title = apply_filters( 'dpa_widget_title', $instance['title'] );
		$limit = apply_filters( 'dpa_widget_limit', $instance['limit'] );
		$showsitewide = apply_filters( 'dpa_widget_showsitewide', !empty( $instance['showsitewide'] ) ? $instance['showsitewide'] : false );

		$checked = '';
		if ( $showsitewide )
			$checked = 'checked="checked"';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Show this many Achievements', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="1" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'showsitewide' ); ?>"><?php _e( "Always show non site-specific Achievements", 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'showsitewide' ); ?>" name="<?php echo $this->get_field_name( 'showsitewide' ); ?>" value="1" type="checkbox" <?php echo $checked; ?> />
		</p>
	<?php
	}
}

/**
 * Featured Achievement widget
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Featured_Achievement extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function DPA_Featured_Achievement() {
		$widget_ops = array( 'classname' => 'achievements-featured-achievement', 'description' => __( "Displays a single Achievement in detail", 'dpa' ) );
		$this->WP_Widget( 'achievements-featured-achievement', __( 'Achievements, Featured', 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-featured-achievement' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 */
	function widget( $args, $instance ) {
		global $achievements_template, $bp;

		extract( $args, EXTR_SKIP );

		if ( dpa_has_achievements( array( 'slug' => $instance['achievement_slug'], 'populate_extras' => false, 'type' => 'single' ) ) ) :
			echo $before_widget;

			if ( $instance['title'] ) {
				echo $before_title;
				echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
				echo $after_title;
			}
			?>

			<?php while ( dpa_achievements() ) : dpa_the_achievement(); ?>

				<div class="item-avatar">
					<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
					<div class="item-title"><a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_name() ?></a></div>
					<div class="item-meta"><span class="activity"><?php printf( __( "%s points", 'dpa' ), bp_core_number_format( dpa_get_achievement_points() ) ) ?></span></div>

					<div class="item-desc"><?php dpa_achievement_description_excerpt() ?></div>
				</div>

			<?php endwhile; ?>

			<p class="achievements-widget-showall"><a href="<?php dpa_achievement_slug_permalink() ?>"><?php _e( 'Show More', 'dpa' ) ?></a></p>
			<div style="clear: right;"></div>

		<?php
		echo $after_widget;
		endif;

		$achievements_template = null;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']            = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );
		$instance['achievement_slug'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['achievement_slug'] ) );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( "Featured Achievement", 'dpa' ), 'achievement_slug' => '' ) );
		$title = apply_filters( 'dpa_widget_title', $instance['title'] );
		$achievement_slug = apply_filters( 'dpa_widget_achievement_slug', $instance['achievement_slug'] );
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'achievement_slug' ); ?>"><?php _e( 'Achievement', 'dpa' ); ?></label><br />
			<select class="widefat" id="<?php echo $this->get_field_id( 'achievement_slug' ); ?>" name="<?php echo $this->get_field_name( 'achievement_slug' ); ?>"><?php $this->get_achievements_list( $achievement_slug ) ?></select>
		</p>

	<?php
	}

	/**
	 * Convenience function to return list of Achievement name and ID pairs
	 *
	 * @param string $achievement_slug Current selection's slug
	 * @since 2.0
	 */
	function get_achievements_list( $achievement_slug ) {
		if ( dpa_has_achievements( 'type=alphabetical&page=1&per_page=0' ) ) :
			while ( dpa_achievements() ) : dpa_the_achievement(); $slug = dpa_get_achievement_slug(); ?>

				<option value="<?php echo esc_attr( $slug ) ?>" <?php if ( $achievement_slug == $slug ) { echo 'selected="selected"'; } ?>><?php dpa_achievement_name() ?></option>

			<?php endwhile;
		endif;
	}
}

/**
 * Member Achievement widget (displays Achievement's pictures which the user is yet to unlocked).
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Member_Achievements_Available extends WP_Widget {
	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function DPA_Member_Achievements_Available() {
		$widget_ops = array( 'classname' => 'achievements-member-achievements-available', 'description' => __( "A photo grid of Achievements which the member is yet to unlock", 'dpa' ) );
		$this->WP_Widget( 'achievements-member-achievements-available', __( 'Achievements, Locked', 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-member-achievements-available' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global DPA_Achievement_Template $achievements_template Achievements template tag object
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 */
	function widget( $args, $instance ) {
		global $achievements_template, $bp;

		if ( ( $instance['loggedin_user'] && empty( $bp->loggedin_user->id ) ) || ( !$instance['loggedin_user'] && empty( $bp->displayed_user->id ) ) )
			return;

		if ( $instance['loggedin_user'] )
			$user_id = $bp->loggedin_user->id;
		else
			$user_id = $bp->displayed_user->id;

		extract( $args, EXTR_SKIP );

		if ( dpa_has_achievements( array( 'user_id' => $user_id, 'max' => $instance['limit'], 'populate_extras' => false, 'type' => 'locked' ) ) ) :
			echo $before_widget;

			if ( $instance['title'] ) {
				echo $before_title;
				echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
				echo $after_title;
			}
			?>

			<div class="avatar-block">
				<?php while ( dpa_achievements() ) : dpa_the_achievement(); ?>
					<div class="item-avatar">
						<a href="<?php dpa_achievement_slug_permalink() ?>"><?php dpa_achievement_picture() ?></a>
					</div>
				<?php endwhile; ?>
			</div>

		<?php
		echo $after_widget;
		endif;

		$achievements_template = null;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );
		$instance['limit'] = apply_filters( 'dpa_widget_limit_before_save', (int)$new_instance['limit'] );
		$instance['loggedin_user'] = apply_filters( 'dpa_widget_bool_before_save', (bool)$new_instance['loggedin_user'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( "Unlock These Achievements", 'dpa' ), 'limit' => 12 ) );
		$title = apply_filters( 'dpa_widget_title', $instance['title'] );
		$limit = apply_filters( 'dpa_widget_limit', $instance['limit'] );
		$loggedin_user = apply_filters( 'dpa_widget_loggedin_user', !empty( $instance['loggedin_user'] ) ? $instance['loggedin_user'] : false );

		$checked = '';
		if ( $loggedin_user )
			$checked = 'checked="checked"';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'limit' ); ?>"><?php _e( 'Limit to this many Achievements', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'limit' ); ?>" name="<?php echo $this->get_field_name( 'limit' ); ?>" type="number" min="1" value="<?php echo esc_attr( $limit ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'loggedin_user' ); ?>"><?php _e( "Always show as logged in member", 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'loggedin_user' ); ?>" name="<?php echo $this->get_field_name( 'loggedin_user' ); ?>" value="1" type="checkbox" <?php echo $checked; ?> />
		</p>

	<?php
	}
}

/**
 * Member Achievement score widget
 *
 * @package Achievements
 * @since 2.0
 * @subpackage widgets
 * @uses WP_Widget
 */
class DPA_Member_Points extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @since 2.0
	 */
	function DPA_Member_Points() {
		$widget_ops = array( 'classname' => 'achievements-member-points', 'description' => __( "Displays a member's Achievement score", 'dpa' ) );
		$this->WP_Widget( 'achievements-member-points', __( "Achievements, Member's Score", 'dpa' ), $widget_ops, array( 'id_base' => 'achievements-member-points' ) );
	}

	/**
	 * Outputs the HTML for this widget.
	 *
	 * @global object $bp BuddyPress global settings
	 * @param array $args An array of standard parameters for widgets
	 * @param array $instance An array of settings for this widget instance
	 * @since 2.0
	 */
	function widget( $args, $instance ) {
		global $bp;

		extract( $args, EXTR_SKIP );

		if ( ( $instance['loggedin_user'] && empty( $bp->loggedin_user->id ) ) || ( !$instance['loggedin_user'] && empty( $bp->displayed_user->id ) ) )
			return;

		if ( $instance['loggedin_user'] )
			$user_id = $bp->loggedin_user->id;
		else
			$user_id = $bp->displayed_user->id;

		echo $before_widget;

		if ( $instance['title'] ) {
			echo $before_title;
			echo esc_html( apply_filters( 'dpa_widget_title', $instance['title'] ) );
			echo $after_title;
		}
		?>

		<h4><?php dpa_member_achievements_score( $user_id ) ?></h4>

		<?php
		echo $after_widget;
	}

	/**
	 * Deals with the settings when they are saved by the admin.
	 *
	 * @param array $new_instance An array of new settings as submitted by the admin
	 * @param array $old_instance An array of the previous settings 
	 * @return array The validated and (if necessary) amended settings
	 * @since 2.0
	 */
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title']         = apply_filters( 'dpa_widget_title_before_save', stripslashes( $new_instance['title'] ) );
		$instance['loggedin_user'] = apply_filters( 'dpa_widget_bool_before_save', (bool)$new_instance['loggedin_user'] );

		return $instance;
	}

	/**
	 * Displays the form for this widget on the Widgets page of the WP Admin area.
	 *
	 * @param array $instance An array of the current settings for this widget
	 * @since 2.0
	 */
	function form( $instance ) {
		$instance = wp_parse_args( (array) $instance, array( 'title' => __( "Achievement Score", 'dpa' ), 'loggedin_user' => 0 ) );
		$title         = apply_filters( 'dpa_widget_title', $instance['title'] );
		$loggedin_user = apply_filters( 'dpa_widget_loggedin_user', $instance['loggedin_user'] );

		$checked = '';
		if ( $loggedin_user )
			$checked = 'checked="checked"';
		?>

		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'loggedin_user' ); ?>"><?php _e( "Always show as logged in member", 'dpa' ); ?></label><br />
			<input class="widefat" id="<?php echo $this->get_field_id( 'loggedin_user' ); ?>" name="<?php echo $this->get_field_name( 'loggedin_user' ); ?>" value="1" type="checkbox" <?php echo $checked; ?> />
		</p>

	<?php
	}
}

/**
 * Registers custom widgets.
 *
 * @since 2.0
 * @see dpa_member_profile_achievement_pictures()
 * @uses DPA_Member_Achievements
 * @uses DPA_Sitewide_Leaderboard
 * @uses DPA_Available_Achievements_Widget
 * @uses DPA_Featured_Achievement
 * @uses DPA_Member_Achievements_Available
 * @uses DPA_Member_Points
 */
function dpa_register_widgets() {
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Member_Achievements');" ) );
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Sitewide_Leaderboard');" ) );
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Available_Achievements_Widget');" ) );
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Featured_Achievement');" ) );
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Member_Achievements_Available');" ) );
	add_action( 'widgets_init', create_function( '', "register_widget('DPA_Member_Points');" ) );
}
add_action( 'bp_register_widgets', 'dpa_register_widgets' );
?>