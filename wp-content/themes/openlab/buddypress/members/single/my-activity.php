<?php
$type = ( isset( $_GET['type'] ) ) ? $_GET['type'] : '';
$filter = ( isset( $_GET['filter'] ) ) ? $_GET['filter'] : '';
$args = openlab_activities_loop_args( $type, $filter );

$filter_options = [
	'' 						=> __( 'All Activity', 'openlab' ),
	'new_blog_post' 		=> __( 'Posts', 'openlab' ),
	'new_blog_comment' 		=> __( 'Comments', 'openlab' ),
	'created_group'			=> __( 'New Groups', 'openlab' ),
	'joined_group'			=> __( 'Group Memberships', 'buddypress' ),
	'added_group_document'	=> __( 'New File', 'openlab' ),
	'bp_doc_created'		=> __( 'New Docs', 'buddypress' ),
	'bp_doc_edited'			=> __( 'Doc Edits', 'buddypress' ),
	'bp_doc_comment'		=> __( 'Doc Comments', 'buddypress' ),
	'bbp_topic_creat'		=> __( 'New Discussion Topics', 'openlab' ),
	'bbp_reply_create'		=> __( 'Discussion Replies', 'openlab' )
];

add_filter( 'bp_activity_time_since', 'openlab_change_activity_date_format' );
?>

<?php echo openlab_submenu_markup( 'my-activity' ); ?>
<div id="item-body" role="main">
	<?php do_action( 'bp_before_activity_loop' ); ?>

	<?php if ( bp_has_activities( $args ) ) : ?>
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
		
		<?php if ( empty( $_POST['page'] ) ) : ?>
			<div id="activity-stream" class="activity-list item-list group-list">
		<?php endif; ?>
	
		<?php while ( bp_activities() ) : bp_the_activity(); ?>
			<?php bp_get_template_part( 'activity/entry' ); ?>
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
<?php remove_filter( 'bp_activity_time_since', 'openlab_change_activity_date_format' ); ?>
