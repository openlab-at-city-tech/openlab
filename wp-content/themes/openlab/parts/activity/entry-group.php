<div class="group-item">
	<div class="group-item-wrapper">
		<div class="activity-entry-row">
			<div class="activity-entry-avatar">
				<div class="activity-avatar">
                    <a href="<?php bp_activity_user_link(); ?>">
                        <?php bp_activity_avatar( array(
                            'type'  => 'full',
                            'class' => 'img-responsive'
                        )); ?>
                    </a>
				</div>
			</div>
			<div class="activity-entry-data">
                <div class="group-activity-content">
                    <div class="activity-body">
                        <?php echo openlab_get_user_activity_action(); ?>
                    </div>
                    <?php if( is_user_logged_in() ) : ?>
                    <div class="activity-action">
                        <?php if ( bp_activity_can_favorite() ) : ?>
							<?php if ( !bp_get_activity_is_favorite() ) : ?>
								<a href="<?php bp_activity_favorite_link(); ?>" title="Star activity" class="button fav bp-secondary-action" data-activity_id="<?php echo bp_get_activity_id(); ?>">
									<span class="sr-only">Star activity</span>
                                    <span class="fa fa-star-o"></span>
								</a>
							<?php else : ?>
								<a href="<?php bp_activity_unfavorite_link(); ?>" title="Unstar activity" class="button unfav bp-secondary-action" data-activity_id="<?php echo bp_get_activity_id(); ?>">
									<span class="sr-only">Unstar activity</span>
                                    <span class="fa fa-star"></span>
								</a>
							<?php endif; ?>
						<?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
			</div>
		</div>
	</div>
</div>
