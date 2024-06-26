<?php
$type = ( isset( $_GET['type'] ) ) ? $_GET['type'] : '';
$filter = ( isset( $_GET['filter'] ) ) ? $_GET['filter'] : '';
$args = openlab_group_activities_loop_args( $type, $filter );

$filter_options = [
	'' 						=> __( 'All Activity', 'openlab' ),
	'created_announcement,created_announcement_reply' => __( 'Announcements', 'openlab' ),
	'new_blog_post' 		=> __( 'Posts', 'openlab' ),
	'new_blog_comment' 		=> __( 'Comments', 'openlab' ),
	'joined_group'			=> __( 'Group Memberships', 'buddypress' ),
	'added_group_document'	=> __( 'New Files', 'openlab' ),
	'bp_doc_created'		=> __( 'New Docs', 'buddypress' ),
	'bp_doc_edited'			=> __( 'Doc Edits', 'buddypress' ),
	'bp_doc_comment'		=> __( 'Doc Comments', 'buddypress' ),
	'bbp_topic_create'		=> __( 'New Discussion Topics', 'openlab' ),
	'bbp_reply_create'		=> __( 'Discussion Replies', 'openlab' )
];
?>
<?php echo openlab_submenu_markup( 'group-activity' ); ?>
<div id="item-body" role="main">
    <?php do_action( 'bp_before_activity_loop' ); ?>

	<div class="activity-filter">
		<form action="" id="activity-filter-form" class="activity-filter-form" method="GET">
			<?php if( ! empty( $type ) ) { ?>
			<input type="hidden" name="type" value="<?php echo $type; ?>" />
			<?php } ?>
			<select id="activity-filter-by" name="filter" class="form-control">
				<?php foreach( $filter_options as $key => $label ) { ?>
					<option value="<?php echo $key; ?>" <?php echo ( $filter === $key ) ? 'selected' : ''; ?>><?php echo $label; ?></option>
				<?php } ?>
			</select>
		</form>
	</div>

    <?php if ( bp_has_activities( $args ) ) : ?>
        <?php if ( empty( $_POST['page'] ) ) : ?>
			<div id="activity-stream" class="activity-list item-list group-list">
		<?php endif; ?>

		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php
			if ( 'connections' === $type ) {
				$template = 'activity/entry';
			} else {
				$template = 'parts/activity/entry-group';
			}
			?>
			<?php bp_get_template_part( $template ); ?>
		<?php endwhile; ?>

		<?php echo openlab_activities_pagination_links(); ?>

		<?php if ( empty( $_POST['page'] ) ) : ?>
			</div>
		<?php endif; ?>

    <?php else : ?>
        <div id="message" class="info">
			<p><?php _e( 'Sorry, there was no activity found. Please try a different filter.', 'buddypress' ); ?></p>
		</div>
    <?php endif; ?>

    <?php do_action( 'bp_after_activity_loop' ); ?>

    <?php if ( empty( $_POST['page'] ) ) : ?>
        <form action="" name="activity-loop-form" id="activity-loop-form" method="post">
            <?php wp_nonce_field( 'activity_filter', '_wpnonce_activity_filter' ); ?>
        </form>
    <?php endif; ?>
</div>
