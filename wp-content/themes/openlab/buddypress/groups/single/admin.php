<?php do_action( 'template_notices' ) ?>

<?php global $bp;

$group_type=groups_get_groupmeta($bp->groups->current_group->id, 'wds_group_type' );

$group_label_uc = openlab_get_group_type_label( 'case=upper' );
?>

<?php //the following switches out the membership menu for the regular admin menu on membership-based admin pages ?>

<div class="row"><div class="col-md-24">
        <div class="submenu">

    <?php if ($bp->action_variables[0] == 'membership-requests' || $bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' ): ?>
    <?php do_action( 'bp_before_group_members_content' ) ?>
            
		<ul class="nav nav-inline">
			<?php openlab_group_membership_tabs(); ?>
		</ul>

    <?php else: ?>
            
            <div class="submenu-text pull-left bold"><?php echo $group_label_uc ?> Settings:</div>
            <ul class="nav nav-inline">
                <?php openlab_group_admin_tabs(); ?>
            </ul>
            
    <?php endif; ?>
            
        </div><!-- .item-list-tabs -->
    </div></div>

<form action="<?php bp_group_admin_form_action() ?>" name="group-settings-form" id="group-settings-form" class="standard-form form-panel" method="post" enctype="multipart/form-data">

<?php do_action( 'bp_before_group_admin_content' ) ?>

    <div class="item-body" id="group-create-body">

<?php /* Edit Group Details */ ?>
<?php if ( bp_is_group_admin_screen( 'edit-details' ) ) : ?>
        
        <div class="panel panel-default">
                    <div class="panel-heading"><?php echo $group_label_uc; ?> Details</div>
                    <div class="panel-body">

	<?php do_action( 'bp_before_group_details_admin' ); ?>

	<label for="group-name"><?php echo $group_label_uc . ' Name' ?> (required)</label>
	<input class="form-control" type="text" name="group-name" id="group-name" value="<?php bp_group_name() ?>" />

	<label for="group-desc"><?php echo $group_label_uc . ' Description' ?> (required)</label>
	<textarea class="form-control" name="group-desc" id="group-desc"><?php bp_group_description_editable() ?></textarea>

	<?php do_action( 'groups_custom_group_fields_editable' ) ?>

	<?php if ( !openlab_is_portfolio() ) : ?>
        <div class="notify-settings">
			<p class="ol-tooltip notify-members"><?php _e( 'Notify group members of changes via email', 'buddypress' ); ?></p>
                        <div class="radio">
                            <label><input type="radio" name="group-notify-members" value="1" /> <?php _e( 'Yes', 'buddypress' ); ?></label>
                            <label><input type="radio" name="group-notify-members" value="0" checked="checked" /> <?php _e( 'No', 'buddypress' ); ?></label>
                        </div>
        </div>

	<?php else : ?>

		<input type="hidden" name="group-notify-members" value="0" />

	<?php endif ?>
                </div>
        </div>


	<?php do_action( 'bp_after_group_details_admin' ); ?>

	<p><input class="btn btn-primary" type="submit" value="<?php _e( 'Save Changes', 'buddypress' ) ?> &rarr;" id="save" name="save" /></p>
	<?php wp_nonce_field( 'groups_edit_group_details' ) ?>
<?php endif; ?>

<?php /* Manage Group Settings */ ?>
<?php if ( bp_is_group_admin_screen( 'group-settings' ) ) : ?>

	<?php do_action( 'bp_before_group_settings_admin' ); ?>

	<?php if ( function_exists('bp_forums_is_installed_correctly') && !openlab_is_portfolio() ) : ?>

		<?php if ( bp_forums_is_installed_correctly() ) : ?>

			<div class="checkbox">
        <h4>Discussion Settings</h4>
        <p id="discussion-settings-tag">These settings enable or disable the discussion forum on your <?php echo $group_type_uc ?> profile.</p>
				<label><input type="checkbox" name="group-show-forum" id="group-show-forum" value="1"<?php bp_group_show_forum_setting() ?> /> <?php _e( 'Enable discussions forum', 'buddypress' ) ?></label>
			</div>

		<?php endif; ?>

		<hr />
	<?php endif; ?>

	<?php if ( ! openlab_is_portfolio() ) : ?>
		<div class="checkbox">
			<h4>Portfolio List Settings</h4>
			<p id="portfolio-list-settings-tag">These settings enable or disable the member portfolio list display on your Course profile.</p>

			<?php $portfolio_list_enabled = openlab_portfolio_list_enabled_for_group() ?>
			<?php $portfolio_list_heading = openlab_portfolio_list_group_heading() ?>
			<label><input type="checkbox" name="group-show-portfolio-list" id="group-show-portfolio-list" value="1" <?php checked( $portfolio_list_enabled ) ?> /> Enable portfolio list</label>

			<h5><label for="group-portfolio-list-heading">List Heading</label></h5>
			<input name="group-portfolio-list-heading" id="group-portfolio-list-heading" type="text" value="<?php echo esc_attr( $portfolio_list_heading ) ?>" />
		</div>

		<hr />
	<?php endif; ?>

	<?php openlab_group_privacy_settings($group_type); ?>

<?php endif; ?>

<?php /* Group Avatar Settings */ ?>
<?php if ( bp_is_group_admin_screen( 'group-avatar' ) ) : ?>

	<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>
      <p id="upload-group-avatar-title">Upload New Avatar</p>
			<p id="upload-group-avatar-text"><?php _e("Upload an image to use as an avatar for this group. The image will be shown on the main group page, and in search results.", 'buddypress') ?></p>

			<p>
				<input type="file" name="file" id="file" />
				<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
				<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
			</p>

			<?php if ( bp_get_group_has_avatar() ) : ?>

        <p id="delete-group-avatar-title">Delete Avatar</p>
				<p id="delete-group-avatar-text"><?php _e( "If you'd like to remove the existing avatar but not upload a new one, please use the delete avatar button.", 'buddypress' ) ?></p>

				<?php bp_button( array( 'id' => 'delete_group_avatar', 'component' => 'groups', 'wrapper_id' => 'delete-group-avatar-button', 'link_class' => 'edit', 'link_href' => bp_get_group_avatar_delete_link(), 'link_title' => __( 'Delete Avatar', 'buddypress' ), 'link_text' => __( 'Delete Avatar', 'buddypress' ) ) ); ?>

			<?php endif; ?>

			<?php wp_nonce_field( 'bp_avatar_upload' ) ?>

	<?php endif; ?>

	<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

		<h3><?php _e( 'Crop Avatar', 'buddypress' ) ?></h3>

		<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ) ?>" />

		<div id="avatar-crop-pane">
			<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ) ?>" />
		</div>

		<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

		<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
		<input type="hidden" id="x" name="x" />
		<input type="hidden" id="y" name="y" />
		<input type="hidden" id="w" name="w" />
		<input type="hidden" id="h" name="h" />

		<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

	<?php endif; ?>

<?php endif; ?>

<?php /* Manage Group Members */ ?>
<?php if ( bp_is_group_admin_screen( 'manage-members' ) ) : ?>

	<?php do_action( 'bp_before_group_manage_members_admin' ); ?>

	<div class="bp-widget">
		<h4><?php _e( 'Administrators', 'buddypress' ); ?></h4>

		<?php if ( bp_has_members( '&include='. bp_group_admin_ids() ) ) : ?>

		<div id="group-list" class="item-list inline-element-list row group-manage-members">

			<?php while ( bp_members() ) : bp_the_member(); ?>
			<div class="col-md-8 group-item">
                            <div class="group-item-wrapper admins <?php echo (count( bp_group_admin_ids( false, 'array' ) ) > 1 ? '' : 'no-btn'); ?>">
                                <div class="row info-row">
                                    <div class="col-sm-8">
                                     <a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
                                    </div>
                                    <div class="col-sm-16">
                                        <h4>
                                                <a href="<?php bp_member_permalink(); ?>"> <?php bp_member_name(); ?></a>
                                        </h4>
                                    </div>
                                </div>
                            <?php if ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
                                <div class="row">
                                    <div class="col-sm-24">
                                                        <a class="btn btn-primary btn-block link-btn confirm admin-demote-to-member admins" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
                                    </div>
                                </div>
					<?php endif; ?>
                            </div>
			</div>
			<?php endwhile; ?>

		</div>

		<?php endif; ?>

	</div>

	<?php if ( bp_group_has_moderators() ) : ?>
		<div class="bp-widget">
			<h4><?php _e( 'Moderators', 'buddypress' ); ?></h4>

			<?php if ( bp_has_members( '&include=' . bp_group_mod_ids() ) ) : ?>
				<div id="group-list" class="item-list single-line inline-element-list row group-manage-members">

					<?php while ( bp_members() ) : bp_the_member(); ?>
                                        <div class="col-md-8 group-item">
                                            <div class="group-item-wrapper moderators">
                                                <div class="row info-row">
                                                    <div class="col-sm-8">
                                                     <a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
                                                    </div>
                                                    <div class="col-sm-16">
                                                        <h4>
                                                                <a href="<?php bp_member_permalink(); ?>"> <?php bp_member_name(); ?></a>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-24">
                                                                        <a href="<?php bp_group_member_promote_admin_link( array( 'user_id' => bp_get_member_user_id() ) ); ?>" class="btn btn-primary btn-sm link-btn confirm mod-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a>
                                                                        <a class="btn btn-primary btn-sm link-btn confirm mod-demote-to-member" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
					<?php endwhile; ?>

				</div>

			<?php endif; ?>
		</div>
	<?php endif ?>


	<div class="bp-widget">
		<h4><?php _e("Members", "buddypress"); ?></h4>

		<?php if ( bp_group_has_members( 'per_page=15&exclude_banned=false' ) ) : ?>

			<?php if ( bp_group_member_needs_pagination() ) : ?>

				<div class="pagination no-ajax">

					<div id="member-count" class="pag-count">
						<?php bp_group_member_pagination_count(); ?>
					</div>

					<div id="member-admin-pagination" class="pagination-links">
						<?php bp_group_member_admin_pagination(); ?>
					</div>

				</div>

			<?php endif; ?>

			<div id="group-list" class="item-list inline-element-list row group-manage-members">
				<?php while ( bp_group_members() ) : bp_group_the_member(); ?>

                                        <div class="col-md-8 group-item">
                                            <div class="group-item-wrapper members">
                                                <div class="row info-row">
						<div class="col-sm-8">
                                                     <a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src ="<?php echo bp_core_fetch_avatar(array('item_id' => bp_get_member_user_id(), 'object' => 'group', 'type' => 'full', 'html' => false)) ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
                                                     <span class="italics"><?php if ( bp_get_group_member_is_banned() ) _e( '(banned)', 'buddypress' ); ?></span>
                                                    </div>
                                                    <div class="col-sm-16">
                                                        <h4>
                                                                <a href="<?php bp_member_permalink(); ?>"> <?php bp_member_name(); ?></a>
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-24">
                                                        <?php if ( bp_get_group_member_is_banned() ) : ?>

								<a href="<?php bp_group_member_unban_link(); ?>" class="btn btn-primary btn-xs link-btn confirm member-unban" title="<?php _e( 'Unban this member', 'buddypress' ); ?>"><?php _e( 'Remove Ban', 'buddypress' ); ?></a>

							<?php else : ?>

								<a href="<?php bp_group_member_ban_link(); ?>" class="btn btn-primary btn-xs link-btn confirm member-ban" title="<?php _e( 'Kick and ban this member', 'buddypress' ); ?>"><?php _e( 'Kick &amp; Ban', 'buddypress' ); ?></a>
								<a href="<?php bp_group_member_promote_mod_link(); ?>" class="btn btn-primary btn-xs link-btn confirm member-promote-to-mod" title="<?php _e( 'Promote to Mod', 'buddypress' ); ?>"><?php _e( 'Promote to Mod', 'buddypress' ); ?></a>
								<a href="<?php bp_group_member_promote_admin_link(); ?>" class="btn btn-primary btn-xs link-btn confirm member-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a>

							<?php endif; ?>

								<a href="<?php bp_group_member_remove_link(); ?>" class="btn btn-primary btn-xs link-btn confirm" title="<?php _e( 'Remove this member', 'buddypress' ); ?>"><?php _e( 'Remove from group', 'buddypress' ); ?></a>

								<?php do_action( 'bp_group_manage_members_admin_item' ); ?>
                                                    </div>
                                                </div>
				</div>
                                        </div>

				<?php endwhile; ?>
			</div>

		<?php else: ?>

			<div id="message" class="info">
				<p><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
			</div>

		<?php endif; ?>

	</div>

	<?php do_action( 'bp_after_group_manage_members_admin' ); ?>

<?php endif; ?>

<?php /* Manage Membership Requests */ ?>
<?php if ( bp_is_group_admin_screen( 'membership-requests' ) ) : ?>

	<?php do_action( 'bp_before_group_membership_requests_admin' ); ?>

	<?php if ( bp_group_has_membership_requests() ) : ?>

		<ul id="request-list" class="item-list">
			<?php while ( bp_group_membership_requests() ) : bp_group_the_membership_request(); ?>

				<li>
					<?php bp_group_request_user_avatar_thumb() ?>
					<h4><?php bp_group_request_user_link() ?> <span class="comments"><?php bp_group_request_comment() ?></span></h4>
					<span class="activity"><?php bp_group_request_time_since_requested() ?></span>

					<?php do_action( 'bp_group_membership_requests_admin_item' ); ?>

					<div class="action">

						<?php bp_button( array( 'id' => 'group_membership_accept', 'component' => 'groups', 'wrapper_class' => 'accept', 'link_href' => bp_get_group_request_accept_link(), 'link_title' => __( 'Accept', 'buddypress' ), 'link_text' => __( 'Accept', 'buddypress' ) ) ); ?>

						<?php bp_button( array( 'id' => 'group_membership_reject', 'component' => 'groups', 'wrapper_class' => 'reject', 'link_href' => bp_get_group_request_reject_link(), 'link_title' => __( 'Reject', 'buddypress' ), 'link_text' => __( 'Reject', 'buddypress' ) ) ); ?>

						<?php do_action( 'bp_group_membership_requests_admin_item_action' ); ?>

					</div>
				</li>

			<?php endwhile; ?>
		</ul>

	<?php else: ?>

		<div id="message" class="info">
			<p><?php _e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
		</div>

	<?php endif; ?>

	<?php do_action( 'bp_after_group_membership_requests_admin' ); ?>

<?php endif; ?>

<?php do_action( 'groups_custom_edit_steps' ) // Allow plugins to add custom group edit screens ?>

<?php /* Delete Group Option */ ?>
<?php if ( bp_is_group_admin_screen( 'delete-group' ) ) : ?>

	<?php do_action( 'bp_before_group_delete_admin' ); ?>

	<div id="message" class="info">
		<p><?php printf( 'WARNING: Deleting this %s will completely remove ALL content associated with it. There is no way back, please be careful with this option.', openlab_get_group_type() ); ?></p>
	</div>

	<input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" onclick="if(this.checked) { document.getElementById('delete-group-button').disabled = ''; } else { document.getElementById('delete-group-button').disabled = 'disabled'; }" /> <?php printf( 'I understand the consequences of deleting this %s.', openlab_get_group_type() ); ?>

	<?php do_action( 'bp_after_group_delete_admin' ); ?>

    <?php $account_type = xprofile_get_field_data( 'Account Type', $bp->loggedin_user->id);
		  if ($account_type == 'Student' && openlab_get_group_type() == 'portfolio' )
		  {
			  $group_type = 'ePortfolio';
		  } else {
			  $group_type = openlab_get_group_type();
		  }

	?>

	<div class="submit">
		<input type="submit" disabled="disabled" value="<?php _e( 'Delete '.$group_type, 'buddypress' ) ?> &rarr;" id="delete-group-button" name="delete-group-button" />
	</div>

	<input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id() ?>" />

	<?php wp_nonce_field( 'groups_delete_group' ) ?>

<?php endif; ?>

<?php /* This is important, don't forget it */ ?>
	<input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id() ?>" />
    </div><!--#group-create-body-->

<?php do_action( 'bp_after_group_admin_content' ) ?>

</form><!-- #group-settings-form -->

