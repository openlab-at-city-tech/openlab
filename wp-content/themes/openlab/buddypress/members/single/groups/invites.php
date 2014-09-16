
<?php do_action( 'bp_before_group_invites_content' ) ?>

<?php if ( bp_has_groups( 'type=invites&user_id=' . bp_loggedin_user_id() ) ) : ?>

            <div id="group-list" class="invites item-list row">

		<?php while ( bp_groups() ) : bp_the_group(); ?>
			
                <div class="group-item col-md-12">
                            <div class="group-item-wrapper">
                                <div class="row info-row">
                                    <div class="item-avatar alignleft col-sm-9">
                                        <a href="<?php bp_group_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_group_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="<?php echo $group->name; ?>"/></a>
                                    </div>
                                    <div class="item col-sm-15">
                                        <h4><a href="<?php bp_group_permalink() ?>"><?php openlab_shortened_text(bp_get_group_name(), 15); ?></a></h4>
                                        <p class="members">
                                            <span class="small"> ( <?php printf(__('%s members', 'buddypress'), bp_group_total_members(false)) ?> )</span>
                                        </p>
                                        <p class="desc">
                                            <?php bp_group_description_excerpt() ?>
                                        </p>

                                        <?php do_action('bp_group_invites_item') ?>

                                        <div class="action invite-member-actions">
                                            <a class="button accept btn btn-primary link-btn" href="<?php bp_group_accept_invite_link() ?>"><?php _e('Accept', 'buddypress') ?></a> &nbsp;
                                            <a class="button reject confirm btn btn-primary link-btn" href="<?php bp_group_reject_invite_link() ?>"><?php _e('Reject', 'buddypress') ?></a>

                                            <?php do_action('bp_group_invites_item_action') ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

		<?php endwhile; ?>
	</div>

<?php else: ?>

	<div id="message" class="info row">
            <div class="col-md-24">
		<p><?php _e( 'You have no outstanding group invites.', 'buddypress' ) ?></p>
            </div>
	</div>

<?php endif;?>

<?php do_action( 'bp_after_group_invites_content' ) ?>