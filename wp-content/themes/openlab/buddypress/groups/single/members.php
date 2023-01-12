<?php 
// Get private users of the group
$private_users = openlab_get_group_private_users( bp_get_group_id() );

// If user is not mod and there are private users, exclude them from the list
if( ! current_user_can( 'bp_moderate' ) && ! empty( $private_users ) ) {
	$members_args['exclude'] = $private_users;
}

// Don't exclude admins from the list
$members_args['exclude_admins_mods'] = 0;

if ( bp_group_has_members( $members_args ) ) : ?>

	<?php do_action( 'bp_before_group_members_content' ) ?>
    <div class="row">
        <div class="submenu col-sm-16">
		<ul class="nav nav-inline">
			<?php openlab_group_membership_tabs(); ?>
		</ul>
	</div><!-- .item-list-tabs --> 
        <div id="member-count" class="pag-count col-sm-8 align-right">
			<?php bp_group_member_pagination_count() ?>
		</div>

        </div>

	<?php do_action( 'bp_before_group_members_list' ) ?>

	<div id="group-members-list" class="item-list group-members group-list clearfix">
		<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

			<div class="group-item col-md-8 col-xs-12">
                            <div class="group-item-wrapper">
                                <div class="row">
                                <div class="item-avatar col-md-9 col-xs-7">
				<a href="<?php bp_member_permalink() ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'member', 'type' => 'full', 'html' => false)) ?>" alt="<?php bp_member_name(); ?>"/></a>
                                </div>
                                <div class="item col-md-15 col-xs-17">
				<p class="h5">
                                                <a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_member_permalink() ?>" data-basevalue="28" data-minvalue="20" data-basewidth="152"><?php bp_member_name(); ?></a><span class="original-copy hidden"><?php bp_member_name(); ?></span>
                                            </p>
				<span class="activity"><?php openlab_member_joined_since() ?></span>

				<?php /*
				<?php
				// Show "Hide my membership" checkbox for the logged in user and non-mods only
				if( ( bp_get_member_user_id() === bp_loggedin_user_id() ) && ! current_user_can( 'bp_moderate' ) ) {

					// Check if current user's membership is private for this group.
					$isPrivate = openlab_is_my_membership_private( bp_get_current_group_id() );
				?>
				<div class="group-item-membership-privacy">
					<label>
						<input type="checkbox" name="membership_privacy" id="membership_privacy" data-group_id="<?php echo bp_get_current_group_id(); ?>" value="<?php echo bp_loggedin_user_id(); ?>" <?php echo ( $isPrivate ) ? 'checked' : ''; ?> /> Hide my membership
					</label>
				</div>
				<?php } ?>
				<?php 
				// Show hidden membership label for the mods
				if( current_user_can( 'bp_moderate' ) && in_array( bp_get_member_user_id(), $private_users, true ) ) { ?>
				<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> Membership hidden</p>
				<?php } ?>
				*/ ?>

				<?php do_action( 'bp_group_members_list_item' ) ?>

				<?php if ( function_exists( 'friends_install' ) ) : ?>

					<div class="action">
						<?php bp_add_friend_button( bp_get_group_member_id(), bp_get_group_member_is_friend() ) ?>

						<?php do_action( 'bp_group_members_list_item_action' ) ?>
					</div>

				<?php endif; ?>
                                </div>
                            </div>
			</div>
                        </div>

		<?php endwhile; ?>

	</div>
        <div id="pag-top" class="pagination clearfix">

            <div class="pagination-links" id="member-dir-pag-top">
                <?php echo openlab_members_pagination_links('mlpage') ?>
            </div>

        </div>

	<?php do_action( 'bp_after_group_members_content' ) ?>

<?php else: ?>

	<div id="message" class="info">
		<p class="bold"><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
	</div>

<?php endif; ?>
