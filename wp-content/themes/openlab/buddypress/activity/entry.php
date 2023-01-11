<?php
// Activity ID
$activity_id = bp_get_activity_id();

// Activity component (groups/blogs)
$activity_component = bp_get_activity_object_name();

switch( $activity_component ) {
	case 'blogs':
		// Get site/blog by activity item id
		$blog = get_blog_details( array( 'blog_id' => bp_get_activity_item_id() ) );
			
		// Get site/blog name
		$item_name = $blog->blogname;

		// Get site/blog url
		$item_url = $blog->siteurl;

		// Get default avatar uri to be used for this type of activity
		$item_avatar_url = openlab_get_default_avatar_uri();
		break;
	case 'members':
	case 'xprofile':
		$item_name = bp_core_get_user_displayname( bp_get_activity_user_id() );
		$item_url = bp_core_get_userlink( bp_get_activity_user_id(), false, true );
		$item_avatar_url = bp_core_fetch_avatar( array(
			'item_id' => bp_get_activity_user_id(),
			'html'	=> false
		 ) );
		break;
	case 'friends':
		$item_name = '';
		$item_url = '';
		$item_avatar_url = openlab_get_default_avatar_uri();
		break;
	case 'groups':
	default:
		// Group ID
		$group_id = openlab_get_group_id_by_activity_id( $activity_id );

		// Get group by ID
		$group = groups_get_group( $group_id );

		// Get group data
		$item_name = bp_get_group_name( $group );
		$item_url = bp_get_group_permalink( $group );
		$item_avatar_url = bp_get_group_avatar_url( $group, 'full' );
}
?>
<div class="group-item">
	<div class="group-item-wrapper">
		<div class="activity-entry-row">
			<div class="activity-entry-avatar">
				<div class="activity-avatar">
					<a href="<?php echo $item_url; ?>" title="<?php echo $item_name; ?>">
						<img src="<?php echo $item_avatar_url; ?>" class="img-responsive" alt="<?php echo $item_name; ?>" />
					</a>
				</div>
			</div>
			<div class="activity-entry-data">
				<div class="activity-header">
					<div class="activity-header-title">
						<p class="item-title h2">
							<a class="no-deco" href="<?php echo $item_url; ?>" title="<?php echo $item_name; ?>"><?php echo $item_name; ?></a>
						</p>
					</div>
					<?php if ( is_user_logged_in() ) : ?>
					<div class="activity-header-meta">
						<?php if ( bp_activity_can_favorite() ) : ?>
							<?php if ( !bp_get_activity_is_favorite() ) : ?>
								<a href="<?php bp_activity_favorite_link(); ?>" title="Pin activity" class="button fav bp-secondary-action" data-activity_id="<?php echo $activity_id; ?>">
									<span class="fa fa-star-o"></span>
								</a>
							<?php else : ?>
								<a href="<?php bp_activity_unfavorite_link(); ?>" title="Unpin activity" class="button unfav bp-secondary-action" data-activity_id="<?php echo $activity_id; ?>">
									<span class="fa fa-star"></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<div class="activity-body">
					<?php echo openlab_get_user_activity_action(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
