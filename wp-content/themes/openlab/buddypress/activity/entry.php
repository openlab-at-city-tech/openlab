<?php 
// Group ID
$group_id = openlab_get_group_id_by_activity_id( bp_get_activity_id() );

// Get group by ID
$group = groups_get_group( $group_id );

// Get group data
$group_name = bp_get_group_name( $group );
$group_url = bp_get_group_permalink( $group );
$group_avatar_url = bp_get_group_avatar_url( $group, 'medium' );
?>
<div class="group-item">
	<div class="group-item-wrapper">
		<div class="row">
			<div class="item-avatar alignleft col-xs-3">
				<div class="activity-avatar">
					<a href="<?php echo $group_url; ?>" title="<?php echo $group_name; ?>">
						<img src="<?php echo $group_avatar_url; ?>" class="img-responsive" alt="<?php echo $group_name; ?>" />
					</a>
				</div>
			</div>
			<div class="item col-xs-21">
				<div class="activity-header">
					<div class="activity-header-title">
						<p class="item-title h2">
							<a class="no-deco" href="<?php echo $group_url; ?>" title="<?php echo $group_name; ?>"><?php echo $group_name; ?></a>
						</p>
					</div>
					<?php if ( is_user_logged_in() ) : ?>
					<div class="activity-header-meta">
						<?php if ( bp_activity_can_favorite() ) : ?>
							<?php if ( !bp_get_activity_is_favorite() ) : ?>
								<a href="<?php bp_activity_favorite_link(); ?>" title="Pin activity" class="button fav bp-secondary-action">
									<span class="fa fa-thumb-tack"></span>
								</a>
							<?php else : ?>
								<a href="<?php bp_activity_unfavorite_link(); ?>" title="Unpin activity" class="button unfav bp-secondary-action">
									<span class="fa fa-thumb-tack"></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<?php endif; ?>
				</div>
				<div class="activity-body">
					<?php bp_activity_action(); ?>
				</div>
			</div>
		</div>
	</div>
</div>
