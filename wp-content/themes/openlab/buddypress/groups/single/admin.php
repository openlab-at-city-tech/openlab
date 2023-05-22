<?php
global $bp;

$group_type = groups_get_groupmeta( bp_get_current_group_id(), 'wds_group_type' );

$group_label_uc = openlab_get_group_type_label( 'case=upper' );
$portfolio_sharing = groups_get_groupmeta( bp_get_current_group_id(), 'enable_portfolio_sharing' );
?>

<?php //the following switches out the membership menu for the regular admin menu on membership-based admin pages ?>

<div class="row">
	<div class="col-md-24">
		<div class="submenu">

			<?php if ( $bp->action_variables[0] == 'membership-requests' || $bp->action_variables[0] == 'manage-members' || $bp->action_variables[0] == 'notifications' ) : ?>
				<?php do_action( 'bp_before_group_members_content' ); ?>

				<ul class="nav nav-inline">
					<?php openlab_group_membership_tabs(); ?>
				</ul>

			<?php else : ?>

				<div class="submenu-text pull-left bold"><?php echo $group_label_uc; ?> Settings:</div>
				<ul class="nav nav-inline">
					<?php openlab_group_admin_tabs(); ?>
				</ul>
			<?php endif; ?>

		</div><!-- .item-list-tabs -->
	</div>
</div>

<form action="<?php bp_group_admin_form_action(); ?>" name="group-settings-form" id="group-settings-form" class="standard-form form-panel" method="post" enctype="multipart/form-data">

	<?php do_action( 'bp_before_group_admin_content' ); ?>

	<div class="item-body" id="group-create-body">

		<?php /* Edit Group Details */ ?>
		<?php if ( bp_is_group_admin_screen( 'edit-details' ) ) : ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="panel panel-default">
				<div class="panel-heading"><?php echo $group_label_uc; ?> Details</div>
				<div class="panel-body">

					<?php do_action( 'bp_before_group_details_admin' ); ?>

					<label for="group-name"><?php echo $group_label_uc . ' Name'; ?> (required)</label>
					<input class="form-control" type="text" name="group-name" id="group-name" value="<?php bp_group_name(); ?>" />

					<label for="group-desc"><?php echo $group_label_uc . ' Description'; ?> (required)</label>
					<textarea class="form-control" name="group-desc" id="group-desc"><?php bp_group_description_editable(); ?></textarea>

					<?php do_action( 'groups_custom_group_fields_editable' ); ?>

					<?php if ( ! openlab_is_portfolio() ) : ?>
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

			<p><input class="btn btn-primary" type="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?> &#xf138;" id="save" name="save" /></p>
			<?php wp_nonce_field( 'groups_edit_group_details' ); ?>
		<?php endif; ?>

		<?php /* Manage Group Settings */ ?>
		<?php if ( bp_is_group_admin_screen( 'group-settings' ) ) : ?>

			<?php do_action( 'bp_before_group_settings_admin' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<?php if ( current_user_can( 'grant_badges' ) && class_exists( '\OpenLab\Badges\Template' ) ) : ?>
				<div id="panel-badges" class="panel panel-default">
					<div class="panel-heading">Badges</div>
					<div class="panel-body">
						<?php \OpenLab\Badges\Template::group_admin_markup(); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php openlab_group_privacy_settings( $group_type ); ?>

			<?php openlab_group_privacy_membership_settings(); ?>

			<?php if ( 'portfolio' !== $group_type ) : ?>
				<?php openlab_group_member_role_settings( $group_type ); ?>
				<?php openlab_group_sharing_settings_markup( $group_type ); ?>
			<?php endif; ?>

			<?php openlab_group_collaboration_tools_settings( $group_type ); ?>

			<?php if ( function_exists( 'eo_get_event_fullcalendar' ) && ! openlab_is_portfolio() ) : ?>
				<?php
				$calendar_enabled    = openlab_is_calendar_enabled_for_group();
				$event_create_access = openlab_get_group_event_create_access_setting( bp_get_current_group_id() );
				?>
				<div class="panel panel-default">
					<div class="panel-heading">Calendar Settings</div>
					<div class="panel-body">
						<p id="discussion-settings-tag">These settings enable or disable your calendar and determine who can create events.</p>

						<div class="row calendar-settings-toggle">
							<div class="col-sm-24">
								<div class="checkbox checkbox-float">
									<label><input type="checkbox" name="openlab-edit-group-calendar" id="group-show-calendar" value="1"<?php checked( $calendar_enabled ); ?> /> Enable Calendar</label>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-sm-24">
								<div class="radio no-margin no-margin-all spaced-list">
									<label class="regular"><input type="radio" name="openlab-bpeo-event-create-access" value="members" <?php checked( 'members', $event_create_access ); ?> /> <?php _e( 'Any group member may connect events to this group', 'buddypress' ); ?></label>
									<label class="regular"><input type="radio" name="openlab-bpeo-event-create-access" value="admin" <?php checked( 'admin', $event_create_access ); ?> /> <?php _e( 'Only administrators and moderators may connect events to this group', 'buddypress' ); ?></label>
								</div>
							</div>
						</div>
					</div>
				</div>

			<?php endif; ?>

			<?php /* Portfolio List Settings */ ?>
			<?php if ( ! openlab_is_portfolio() ) : ?>
				<div class="panel panel-default">
					<div class="panel-heading">Portfolio List Settings</div>
					<div class="panel-body">
						<p id="portfolio-list-settings-tag">These settings enable or disable the member portfolio list display on your Course profile.</p>

						<?php $portfolio_list_enabled = openlab_portfolio_list_enabled_for_group(); ?>
						<?php $portfolio_list_heading = openlab_portfolio_list_group_heading(); ?>

						<div class="checkbox">
							<label><input type="checkbox" name="group-show-portfolio-list" id="group-show-portfolio-list" value="1" <?php checked( $portfolio_list_enabled ); ?> /> Enable portfolio list</label>
						</div>

						<label for="group-portfolio-list-heading">List Heading</label>
						<input name="group-portfolio-list-heading" id="group-portfolio-list-heading" class="form-control" type="text" value="<?php echo esc_attr( $portfolio_list_heading ); ?>" />
					</div>
				</div>
			<?php endif; ?>

			<?php /* Library Settings */ ?>
			<?php if ( ! openlab_is_portfolio() ) : ?>
				<?php openlab_group_library_settings(); ?>
			<?php endif; ?>

			<?php /* "Related Links List Settings" */ ?>
			<div class="panel panel-default">
				<div class="panel-heading">Related Links List Settings</div>
				<div class="panel-body">
					<p>These settings enable or disable the related links list display on your <?php echo $group_label_uc; ?> profile.</p>
					<?php $related_links_list_enable = groups_get_groupmeta( bp_get_current_group_id(), 'openlab_related_links_list_enable' ); ?>
					<?php $related_links_list_heading = groups_get_groupmeta( bp_get_current_group_id(), 'openlab_related_links_list_heading' ); ?>
					<?php $related_links_list = openlab_get_group_related_links( bp_get_current_group_id(), 'edit' ); ?>
					<div class="checkbox">
						<label><input type="checkbox" name="related-links-list-enable" id="related-links-list-enable" value="1" <?php checked( $related_links_list_enable ); ?> /> Enable related links list</label>
					</div>
					<label for="related-links-list-heading">List Heading</label>
					<input name="related-links-list-heading" id="related-links-list-heading" class="form-control" type="text" value="<?php echo esc_attr( $related_links_list_heading ); ?>" />
					<ul class="related-links-edit-items inline-element-list">
						<?php $rli = 1; ?>
						<?php foreach ( (array) $related_links_list as $rl ) : ?>
							<li class="form-inline label-combo row">
								<div class="form-group col-sm-9">
									<label for="related-links-<?php echo $rli; ?>-name">Name</label> <input name="related-links[<?php echo $rli; ?>][name]" id="related-links-<?php echo $rli; ?>-name" class="form-control" value="<?php echo esc_attr( $rl['name'] ); ?>" />
								</div>
								<div class="form-group col-sm-15">
									<label for="related-links-<?php echo $rli; ?>-url">URL</label> <input name="related-links[<?php echo $rli; ?>][url]" id="related-links-<?php echo $rli; ?>-url" class="form-control related-links-url" value="<?php echo esc_attr( $rl['url'] ); ?>" />

									<div class="related-link-actions">
										<button type="button" class="related-link-remove related-link-action"><span class="sr-only">Remove this link</span><i class="fa fa-minus-circle" role="presentation"></i></button>
									</div>
								</div>
							</li>
							<?php $rli++; ?>
						<?php endforeach; ?>
					</ul>

					<button type="button" class="related-link-add related-link-action" id="add-new-related-link"><span class="sr-only">Add new link</span><i class="fa fa-plus-circle" role="presentation"></i></button>
				</div>
			</div>

			<?php if ( openlab_is_portfolio() ) : ?>
				<div class="panel panel-default">
					<div class="panel-heading">Add to My Portfolio</div>
					<div class="panel-body">
						<div class="editfield">
							<p class="description">The Add to Portfolio feature saves selected posts, pages, and comments that you have authored on Course, Project, and Club sites directly to your Portfolio site. For more information visit <a href="https://openlab.citytech.cuny.edu/blog/help/openlab-help/">OpenLab Help</a>.</p>

							<div class="checkbox">
								<label for="portfolio-sharing">
									<input name="portfolio-sharing" type="checkbox" id="portfolio-sharing" value="1" <?php checked( 'yes', $portfolio_sharing ); ?> />
									Enable "Add to My Portfolio"
								</label>
							</div>

							<?php wp_nonce_field( 'add_to_portfolio_toggle', 'add-to-portfolio-toggle-nonce', false ); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php do_action( 'bp_after_group_settings_admin' ); ?>
			<p><input class="btn btn-primary" type="submit" value="<?php _e( 'Save Changes', 'buddypress' ); ?> &#xf138;" id="save" name="save" /></p>
			<?php wp_nonce_field( 'groups_edit_group_settings' ); ?>

		<?php endif; ?>

		<?php /* Group Avatar Settings */ ?>
		<?php if ( bp_is_group_admin_screen( 'group-avatar' ) ) : ?>

			<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading">Upload Avatar</div>
					<div class="panel-body">
						<?php do_action( 'template_notices' ); ?>
						<div class="row">
							<div class="col-sm-8">
								<div id="avatar-wrapper">
									<div class="padded-img">

										<?php if ( bp_get_group_avatar() ) : ?>
											<?php
											$group_avatar = bp_core_fetch_avatar(
												array(
													'item_id' => bp_get_group_id(),
													'object' => 'group',
													'type' => 'full',
													'html' => false,
												)
											);
											?>
											<img class="img-responsive padded" src="<?php echo esc_attr( $group_avatar ); ?>" alt="<?php echo bp_get_group_name(); ?>"/>
										<?php else : ?>
											<img class="img-responsive padded" src ="<?php echo get_stylesheet_directory_uri(); ?>/images/avatar_blank.png" alt="avatar-blank"/>
										<?php endif; ?>

									</div>
								</div>
							</div>
							<div class="col-sm-16">

								<p class="italics"><?php _e( 'Upload an image to use as an avatar for this ' . bp_get_group_type() . '. The image will be shown on the main ' . bp_get_group_type() . ' page, and in search results.', 'buddypress' ); ?></p>

								<p id="avatar-upload">
								<div class="form-group form-inline">
									<div class="form-control type-file-wrapper">
										<input type="file" name="file" id="file" />
									</div>
									<input class="btn btn-primary top-align" type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ); ?>" />
									<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
								</div>
								</p>

								<?php if ( bp_get_user_has_avatar() ) : ?>
									<p class="italics"><?php _e( "If you'd like to remove the existing avatar but not upload a new one, please use the delete avatar button.", 'buddypress' ); ?></p>
									<a class="btn btn-primary no-deco" href="<?php echo bp_get_group_avatar_delete_link(); ?>" title="<?php _e( 'Delete Avatar', 'buddypress' ); ?>"><?php _e( 'Delete Avatar', 'buddypress' ); ?></a>
								<?php endif; ?>

								<?php wp_nonce_field( 'bp_avatar_upload' ); ?>
							</div>
						</div>
					</div>
				</div>

			<?php endif; ?>

			<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading">Crop Avatar</div>
					<div class="panel-body">

						<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ); ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop(); ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ); ?>" />
						</div>

						<input class="btn btn-primary" type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ); ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src(); ?>" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

						<?php wp_nonce_field( 'bp_avatar_cropstore' ); ?>
					</div>
				</div>

			<?php endif; ?>

		<?php endif; ?>

		<?php /* Manage Group Members */ ?>
		<?php if ( bp_is_group_admin_screen( 'manage-members' ) ) : ?>

			<?php do_action( 'bp_before_group_manage_members_admin' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<div class="bp-widget">
				<h4><?php _e( 'Administrators', 'buddypress' ); ?></h4>

				<?php if ( bp_has_members( '&include=' . bp_group_admin_ids() ) ) : ?>

					<div id="group-manage-admins-members" class="group-list item-list inline-element-list row group-manage-members">

					<?php
					while ( bp_members() ) :
						bp_the_member();
						?>
							<div class="col-md-8 col-xs-12 group-item">
								<div class="group-item-wrapper admins <?php echo ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ? '' : 'no-btn' ); ?>">
									<div class="row info-row">
										<div class="col-md-9 col-xs-7">
											<?php
											$group_member_avatar = bp_core_fetch_avatar(
												array(
													'item_id' => bp_get_member_user_id(),
													'object'  => 'member',
													'type'    => 'full',
													'html'    => false,
												)
											);

											?>
											<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $group_member_avatar ); ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
										</div>
										<div class="col-md-15 col-xs-17">
											<p class="h5">
												<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_member_permalink(); ?>" data-basevalue="28" data-minvalue="20" data-basewidth="152"><?php bp_member_name(); ?></a><span class="original-copy hidden"><?php bp_member_name(); ?></span>
											</p>
											<?php if ( count( bp_group_admin_ids( false, 'array' ) ) > 1 ) : ?>
												<ul class="group-member-actions">
													<li><a class="confirm admin-demote-to-member admins" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a></li>
												</ul>
											<?php endif; ?>
										</div>
									</div>
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
						<div id="group-manage-moderators-members" class="item-list single-line inline-element-list row group-manage-members group-list">

							<?php
							while ( bp_members() ) :
								bp_the_member();
								?>
								<div class="col-md-8 col-xs-12 group-item">
									<div class="group-item-wrapper moderators">
										<div class="row info-row">
											<div class="col-md-9 col-xs-7">
												<?php
												$group_member_avatar = bp_core_fetch_avatar(
													array(
														'item_id' => bp_get_member_user_id(),
														'object' => 'member',
														'type' => 'full',
														'html' => false,
													)
												);
												?>
												<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $group_member_avatar ); ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
											</div>
											<div class="col-md-15 col-xs-17">
												<p class="h5">
													<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_member_permalink(); ?>" data-basevalue="28" data-minvalue="20" data-basewidth="152"><?php bp_member_name(); ?></a><span class="original-copy hidden"><?php bp_member_name(); ?></span>
												</p>

												<ul class="group-member-actions">
													<li><a href="<?php bp_group_member_promote_admin_link( array( 'user_id' => bp_get_member_user_id() ) ); ?>" class="confirm mod-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a></li>
													<li><a class="confirm mod-demote-to-member" href="<?php bp_group_member_demote_link( bp_get_member_user_id() ); ?>"><?php _e( 'Demote to Member', 'buddypress' ); ?></a></li>
												</ul>
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
				<h4><?php _e( 'Members', 'buddypress' ); ?></h4>

				<?php if ( bp_group_has_members( 'per_page=15&exclude_banned=0' ) ) :
					// Get private users of the group
					$private_users = openlab_get_group_private_users( bp_get_group_id() );
				?>

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

					<div id="group-manage-members" class="item-list inline-element-list row group-manage-members group-list">
					<?php
					while ( bp_group_members() ) :
						bp_group_the_member();
						?>

							<div class="col-md-8 col-xs-12 group-item">
								<div class="group-item-wrapper members">
									<div class="row info-row">
										<div class="col-md-9 col-xs-7">
											<?php
											$group_member_avatar = bp_core_fetch_avatar(
												array(
													'item_id' => bp_get_member_user_id(),
													'object'  => 'member',
													'type'    => 'full',
													'html'    => false,
												)
											);
											?>
											<a href="<?php bp_member_permalink(); ?>"><img class="img-responsive" src="<?php echo esc_attr( $group_member_avatar ); ?>" alt="Profile picture of <?php echo bp_get_member_name(); ?>"/></a>
											<span class="italics">
											<?php
											if ( bp_get_group_member_is_banned() ) {
												_e( '(banned)', 'buddypress' );}
											?>
											</span>
										</div>
										<div class="col-md-15 col-xs-17">
											<p class="h5">
												<a class="no-deco truncate-on-the-fly hyphenate" href="<?php bp_member_permalink(); ?>" data-basevalue="28" data-minvalue="20" data-basewidth="152"><?php bp_member_name(); ?></a><span class="original-copy hidden"><?php bp_member_name(); ?></span>
											</p>

											<ul class="group-member-actions">
												<?php if ( bp_get_group_member_is_banned() ) : ?>
													<li><a href="<?php bp_group_member_unban_link(); ?>" class="confirm member-unban" title="<?php _e( 'Unban this member', 'buddypress' ); ?>"><?php _e( 'Remove Ban', 'buddypress' ); ?></a></li>
												<?php else : ?>
													<li><a href="<?php bp_group_member_promote_mod_link(); ?>" class="confirm member-promote-to-mod" title="<?php _e( 'Promote to Mod', 'buddypress' ); ?>"><?php _e( 'Promote to Mod', 'buddypress' ); ?></a></li>
													<li><a href="<?php bp_group_member_promote_admin_link(); ?>" class="confirm member-promote-to-admin" title="<?php _e( 'Promote to Admin', 'buddypress' ); ?>"><?php _e( 'Promote to Admin', 'buddypress' ); ?></a></li>
												<?php endif; ?>

												<li><a href="<?php bp_group_member_remove_link(); ?>" class="confirm" title="<?php _e( 'Remove this member', 'buddypress' ); ?>"><?php _e( 'Remove from group', 'buddypress' ); ?></a></li>

												<?php if ( ! bp_get_group_member_is_banned() ) : ?>
													<li><a href="<?php bp_group_member_ban_link(); ?>" class="confirm member-ban" title="<?php esc_html_e( 'Kick and ban this member', 'buddypress' ); ?>">Ban from group</a></li>
												<?php endif; ?>

											</ul>

											<?php do_action( 'bp_group_manage_members_admin_item' ); ?>

											<?php if( in_array( bp_get_member_user_id(), $private_users, true ) ) { ?>
											<p class="private-membership-indicator"><span class="fa fa-eye-slash"></span> Membership hidden</p>
											<?php } ?>
										</div>
									</div>
								</div>
							</div>
						<?php endwhile; ?>
					</div>

				<?php else : ?>

					<div id="message" class="info">
						<p class="bold"><?php _e( 'This group has no members.', 'buddypress' ); ?></p>
					</div>

				<?php endif; ?>

			</div>

			<?php do_action( 'bp_after_group_manage_members_admin' ); ?>

		<?php endif; ?>

		<?php /* Manage Membership Requests */ ?>
		<?php if ( bp_is_group_admin_screen( 'membership-requests' ) ) : ?>

			<?php do_action( 'bp_before_group_membership_requests_admin' ); ?>

			<?php do_action( 'template_notices' ); ?>

				<?php if ( bp_group_has_membership_requests() ) : ?>

				<div id="group-manage-request-list" class="group-list item-list inline-element-list row group-manage-requests group-manage-members">
					<?php
					while ( bp_group_membership_requests() ) :
						bp_group_the_membership_request();
						?>
						<div class="col-md-8 col-xs-12 group-item">
							<div class="group-item-wrapper">
								<div class="row info-row">
									<div class="col-md-9 col-xs-7">
										<?php
										$group_member_avatar = bp_core_fetch_avatar(
											array(
												'item_id' => $GLOBALS['requests_template']->request->user_id,
												'object'  => 'member',
												'type'    => 'full',
												'html'    => false,
											)
										);
										?>
										<img class="img-responsive" src="<?php echo esc_attr( $group_member_avatar ); ?>" />
									</div>

									<div class="col-md-15 col-xs-17">
										<h4>
											<?php echo openlab_group_request_user_link(); ?>
										</h4>

										<ul class="group-member-actions">
											<li><a href="<?php bp_group_request_accept_link(); ?>"><?php _e( 'Accept', 'buddypress' ); ?></a></li>
											<li><a href="<?php bp_group_request_reject_link(); ?>"><?php _e( 'Reject', 'buddypress' ); ?></a></li>
										</ul>
									</div>
								</div>
							</div>
						</div>
					<?php endwhile; ?>
				</div>

			<?php else : ?>

				<div id="message" class="info">
					<p><?php _e( 'There are no pending membership requests.', 'buddypress' ); ?></p>
				</div>

			<?php endif; ?>

			<?php do_action( 'bp_after_group_membership_requests_admin' ); ?>

		<?php endif; ?>

		<?php /* Delete Group Option */ ?>
		<?php if ( bp_is_group_admin_screen( 'delete-group' ) ) : ?>

			<?php do_action( 'bp_before_group_delete_admin' ); ?>

			<?php do_action( 'template_notices' ); ?>

			<div id="message" class="bp-template-notice error margin-bottom">
				<p><?php printf( 'WARNING: Deleting this %s will completely remove ALL content associated with it. There is no way back, please be careful with this option.', openlab_get_group_type() ); ?></p>
			</div>

			<div class="checkbox no-margin no-margin-bottom">
				<label>
					<input type="checkbox" name="delete-group-understand" id="delete-group-understand" value="1" onclick="if (this.checked) {
									document.getElementById('delete-group-button').disabled = '';
								} else {
									document.getElementById('delete-group-button').disabled = 'disabled';
								}" />
			<?php printf( 'I understand the consequences of deleting this %s.', openlab_get_group_type() ); ?>
				</label>
			</div>

			<?php do_action( 'bp_after_group_delete_admin' ); ?>

			<?php
			$account_type = openlab_get_user_member_type( bp_loggedin_user_id() );
			if ( $account_type == 'student' && openlab_get_group_type() === 'portfolio' ) {
				$group_type = 'ePortfolio';
			} else {
				$group_type = openlab_get_group_type();
			}
			?>

			<div class="submit">
				<input class="btn btn-primary btn-margin btn-margin-top" type="submit" disabled="disabled" value="<?php _e( 'Delete ' . $group_type, 'buddypress' ); ?> &#xf138;" id="delete-group-button" name="delete-group-button" />
			</div>

			<input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />

			<?php wp_nonce_field( 'groups_delete_group' ); ?>

		<?php endif; ?>

		<?php
		/**
		 * This is a quick and dirty solution for injecting Bootstrap markup into the bp group email subscription edit screens
		 * Basically it opts out of the action call to groups_custom_edit_steps and instead uses custom functions pulled from the bp group email subscription core
		 * This functionality is definitely a candidate for a better solution
		 */
		if ( bp_is_group_admin_screen( 'notifications' ) ) {
			openlab_ass_admin_notice_form();
		} else {
			// Allow plugins to add custom group edit screens
			do_action( 'groups_custom_edit_steps' );
		}
		?>

		<?php /* This is important, don't forget it */ ?>
		<input type="hidden" name="group-id" id="group-id" value="<?php bp_group_id(); ?>" />
	</div><!--#group-create-body-->

<?php do_action( 'bp_after_group_admin_content' ); ?>

</form><!-- #group-settings-form -->

