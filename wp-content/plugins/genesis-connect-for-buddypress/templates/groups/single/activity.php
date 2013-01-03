<div class="item-list-tabs no-ajax" id="bpsubnav" role="navigation">
	<ul>
		<li class="feed"><a href="<?php bp_group_activity_feed_link(); ?>" title="<?php _e( 'RSS', 'buddypress' ); ?>"><?php _e( 'RSS', 'buddypress' ); ?></a></li>

		<?php do_action( 'bp_group_activity_syndication_options' ); ?>

		<li id="activity-filter-select" class="last">
			<label for="activity-filter-by"><?php _e( 'Show:', 'buddypress' ); ?></label> 
			<select id="activity-filter-by">
				<option value="-1"><?php _e( 'Everything', 'buddypress' ); ?></option>
				<option value="activity_update"><?php _e( 'Updates', 'buddypress' ); ?></option>

				<?php if ( bp_is_active( 'forums' ) ) : ?>
					<option value="new_forum_topic"><?php _e( 'Forum Topics', 'buddypress' ); ?></option>
					<option value="new_forum_post"><?php _e( 'Forum Replies', 'buddypress' ); ?></option>
				<?php endif; ?>

				<option value="joined_group"><?php _e( 'Group Memberships', 'buddypress' ); ?></option>

				<?php do_action( 'bp_group_activity_filter_options' ); ?>
			</select>
		</li>
	</ul>
	<div class="clear"></div>
</div><!-- .item-list-tabs -->
<?php 
do_action( 'bp_before_group_activity_post_form' );
if ( is_user_logged_in() && bp_group_is_member() ) {
	gconnect_locate_template( array( 'activity/post-form.php'), true );
}
do_action( 'bp_after_group_activity_post_form' );
do_action( 'bp_before_group_activity_content' );
?>
<div class="activity single-group">
	<?php gconnect_locate_template( array( 'activity/activity-loop.php' ), true ); ?>
</div><!-- .activity -->

<?php
do_action( 'bp_after_group_activity_content' );
