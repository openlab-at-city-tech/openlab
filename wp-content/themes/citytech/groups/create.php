<?php remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_create_group' );

function cuny_create_group(){

global $bp;

//this function doesn't work - explore for deprecation or fixing
/*$group_type = openlab_get_current_group_type();*/

// Set a group label. The (e)Portfolio logic means we have to do an extra step
if ( 'portfolio' == $group_type ) {
	$group_label = openlab_get_portfolio_label( 'case=upper&user_id=' . bp_loggedin_user_id() );
	$page_title  = 'Create ' . openlab_get_portfolio_label( 'case=upper&leading_a=1&user_id=' . bp_loggedin_user_id() );
} else {
	$group_label = $group_type;
	$page_title  = 'Create a ' . ucwords( $group_type );
}

//get group type
if ( !empty( $_GET['type'] ) ) {
		$group_type = $_GET['type'];
	}

?>
		<h1 class="entry-title mol-title"><?php bp_loggedin_user_fullname() ?>'s Profile</h1>

		<div class="submenu"><?php echo openlab_my_groups_submenu($group_type); ?></div>

		<div id="single-course-body">

			<form action="<?php bp_group_creation_form_action() ?>" method="post" id="create-group-form" class="standard-form" enctype="multipart/form-data">

			<?php do_action( 'bp_before_create_group' ) ?>

			<?php do_action( 'template_notices' ) ?>

			<div class="item-body" id="group-create-body">

				<?php /* Group creation step 1: Basic group details */ ?>
				<?php if ( bp_is_group_creation_step( 'group-details' ) ) : ?>

					<?php do_action( 'bp_before_group_details_creation_step' ); ?>

					<?php if ( 'course' == $group_type ) : ?>
						<p class="ol-tooltip">Please take a moment to consider the name of your Course. We recommend keeping your Course title name under 50 characters. You can always change the name of your course later.</p>
					<?php elseif ( 'portfolio' == $group_type ) : ?>
						<p class="ol-tooltip">We recommend that the name of your <?php echo $group_label ?> follow this format:</p>

						<ul class="ol-tooltip">
							<li>FirstName LastName's <?php echo $group_label ?> </li>
							<li>Jane Smith's <?php echo $group_label ?> (Example)</li>
						</ul>
					<?php else : ?>
						<p class="ol-tooltip">Please take a moment to consider the name of your <?php echo ucwords( $group_type ) ?>.  Choosing a name that clearly identifies your  <?php echo ucwords( $group_type ) ?> will make it easier for others to find your <?php echo ucwords( $group_type ) ?> profile. We recommend keeping your  <?php echo ucwords( $group_type ) ?> name under 50 characters.</p>
					<?php endif ?>

					<label for="group-name">* <?php echo ucfirst( $group_type ); ?> Name <?php _e( '(required)', 'buddypress' )?></label>
					<input size="80" type="text" name="group-name" id="group-name" value="<?php bp_new_group_name() ?>" />

					<label for="group-desc">* <?php echo ucfirst($group_type);?> Description <?php _e( '(required)', 'buddypress' )?></label>
					<textarea name="group-desc" id="group-desc"><?php bp_new_group_description() ?></textarea>

					<?php do_action( 'bp_after_group_details_creation_step' ) ?>

					<?php wp_nonce_field( 'groups_create_save_group-details' ) ?>

				<?php endif; ?>

				<?php /* Group creation step 2: Group settings */ ?>
				<?php if ( bp_is_group_creation_step( 'group-settings' ) ) : ?>

					<?php do_action( 'bp_before_group_settings_creation_step' ); ?>

					<?php /* Don't show Discussion toggle for portfolios */ 
						  /* Changed this to hidden in case this value is needed */ ?>
					<?php if ( !openlab_is_portfolio() && function_exists( 'bp_forums_is_installed_correctly' ) ) : ?>
						<?php if ( bp_forums_is_installed_correctly() ) : ?>
							<div class="checkbox">
								<label><input type="hidden" name="group-show-forum" id="group-show-forum" value="1"<?php if ( bp_get_new_group_enable_forum() ) { ?> checked="checked"<?php } ?> /></label>
							</div>
						<?php else : ?>
							<?php if ( is_super_admin() ) : ?>
								<div class="checkbox">
									<label><input type="hidden" disabled="disabled" name="disabled" id="disabled" value="0" /> <?php printf( __('<strong>Attention Site Admin:</strong> '.$group_type.' forums require the <a href="%s">correct setup and configuration</a> of a bbPress installation.', 'buddypress' ), bp_get_root_domain() . '/wp-admin/admin.php?page=bb-forums-setup' ) ?></label>
								</div>
							<?php endif; ?>
						<?php endif; ?>
					<?php endif; ?>

					<hr />

					<h4><?php _e( 'Privacy Settings', 'buddypress' ); ?></h4>

					<?php /* @todo This should probably be modded for all group types */ ?>
					<?php if ( openlab_is_portfolio() ) : ?>
						<h5>Portfolio Profile</h5>
					<?php endif ?>
                    
                    <p id="privacy-intro"><?php _e('To change these settings later, use the '.$group_type.' Profile Settings page.','buddypress'); ?></p>

					<div class="radio">
						<label><input type="radio" name="group-status" value="public"<?php if ( 'public' == bp_get_new_group_status() || !bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'Public ', 'buddypress' ) ?></strong>
							<ul>
								<li><?php _e( 'This '.ucfirst($group_type).' Profile and related content and activity will be visible to the public.', 'buddypress' ) ?></li>
								<li><?php _e( 'This '.ucfirst($group_type).' will be listed in the '.ucfirst($group_type).' directory, search results, and may be displayed on the OpenLab home page.', 'buddypress' ) ?></li>
								<li><?php _e( 'Any OpenLab member may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
							</ul>
						</label>

						<?php /* Portfolios don't have a Private setting */ ?>
						<?php if ( !openlab_is_portfolio() && ( !isset( $_GET['type'] ) || 'portfolio' != $_GET['type'] ) ): ?>
							<label><input type="radio" name="group-status" value="private"<?php if ( 'private' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e( 'Private', 'buddypress' ) ?></strong>
							<ul>
								<li><?php _e( 'This '.ucfirst($group_type).' Profile and related content and activity will only be visible to members of the group..', 'buddypress' ) ?></li>
								<li><?php _e( 'This '.ucfirst($group_type).' will be listed in the ' .ucfirst($group_type).' directory and in search results.', 'buddypress' ) ?></li>
								<li><?php _e( 'Only OpenLab members who request membership and are accepted may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
							</ul>
							</label>

						<?php endif ?>

						<label><input type="radio" name="group-status" value="hidden"<?php if ( 'hidden' == bp_get_new_group_status() ) { ?> checked="checked"<?php } ?> />
							<strong><?php _e('Hidden', 'buddypress') ?></strong>
							<ul>
                            <?php if ( !openlab_is_portfolio() ) : ?>
									<li><?php _e( 'This '.ucfirst($group_type).' Profile, related content and activity will only be visible only to members of the '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
									<li><?php _e( 'This '.ucfirst($group_type).' Profile will NOT be listed in the '.ucfirst($group_type).' directory, search results, or OpenLab home page.', 'buddypress' ) ?></li>
									<li><?php _e( 'Only OpenLab members who are invited may join this '.ucfirst($group_type).'.', 'buddypress' ) ?></li>
							<?php endif ?>
							</ul>
						</label>
					</div>

					<?php //do_action( 'bp_after_group_settings_creation_step' ); ?>

					<?php if ( $groupblog_id = openlab_get_site_id_by_group_id() ) : ?>
						<h5><?php echo ucfirst($group_type); ?> Site</h5>
						<p><?php _e('These settings affect how others view your '.ucfirst($group_type).' Site.') ?></p>
						<?php openlab_site_privacy_settings_markup( $groupblog_id ) ?>
					<?php endif ?>

					<?php wp_nonce_field( 'groups_create_save_group-settings' ) ?>

				<?php endif; ?>

				<?php /* Group creation step 3: Avatar Uploads */?>

				<?php if ( bp_is_group_creation_step( 'group-avatar' ) ) : ?>

                	<?php do_action( 'bp_before_group_avatar_creation_step' ); ?>

					<?php if ( !bp_get_avatar_admin_step() || 'upload-image' == bp_get_avatar_admin_step() ) : ?>

						<div class="left-menu">
							<?php bp_new_group_avatar() ?>
						</div><!-- .left-menu -->

						<div class="main-column">
							<p><?php _e("Upload an image to use as an avatar for this ".$group_type.". The image will be shown on the main ".$group_type." page, and in search results.", 'buddypress') ?></p>

							<p>
								<input type="file" name="file" id="file" />
								<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
								<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							</p>

							<p><?php _e( 'To skip the avatar upload process, hit the "Finish" button.', 'buddypress' ) ?></p>
						</div><!-- .main-column -->

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Group Avatar', 'buddypress' ) ?></h3>

						<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ) ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ) ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
						<input type="hidden" name="upload" id="upload" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

					<?php endif; ?>

					<?php do_action( 'bp_after_group_avatar_creation_step' ); ?>

					<?php wp_nonce_field( 'groups_create_save_group-avatar' ) ?>

				<?php endif; ?>

				<?php /* Group creation step 4: Invite friends to group */ ?>
				<?php if ( bp_is_group_creation_step( 'group-invites' ) ) : ?>

					<?php do_action( 'bp_before_group_invites_creation_step' ); ?>

					<?php if ( function_exists( 'bp_get_total_friend_count' ) && bp_get_total_friend_count( bp_loggedin_user_id() ) ) : ?>
						<div class="left-menu">

							<div id="invite-list">
								<ul>
									<?php bp_new_group_invite_friend_list() ?>
								</ul>

								<?php wp_nonce_field( 'groups_invite_uninvite_user', '_wpnonce_invite_uninvite_user' ) ?>
							</div>

						</div><!-- .left-menu -->

						<div class="main-column">

							<div id="message" class="info">
								<p><?php _e('Select people to invite from your friends list.', 'buddypress'); ?></p>
							</div>

							<?php /* The ID 'friend-list' is important for AJAX support. */ ?>
							<ul id="friend-list" class="item-list">
							<?php if ( bp_group_has_invites() ) : ?>

								<?php while ( bp_group_invites() ) : bp_group_the_invite(); ?>

									<li id="<?php bp_group_invite_item_id() ?>">
										<?php bp_group_invite_user_avatar() ?>

										<h4><?php bp_group_invite_user_link() ?></h4>
										<span class="activity"><?php bp_group_invite_user_last_active() ?></span>

										<div class="action">
											<a class="remove" href="<?php bp_group_invite_user_remove_invite_url() ?>" id="<?php bp_group_invite_item_id() ?>"><?php _e( 'Remove Invite', 'buddypress' ) ?></a>
										</div>
									</li>

								<?php endwhile; ?>

								<?php wp_nonce_field( 'groups_send_invites', '_wpnonce_send_invites' ) ?>
							<?php endif; ?>
							</ul>

						</div><!-- .main-column -->

					<?php else : ?>

						<div id="message" class="info">
							<p><?php _e( 'Once you have built up friend connections you will be able to invite others to your '.$group_type.'. You can send invites any time in the future by selecting the "Send Invites" option when viewing your new '.$group_type.'.', 'buddypress' ); ?></p>
						</div>

					<?php endif; ?>

					<?php wp_nonce_field( 'groups_create_save_group-invites' ) ?>
					<?php do_action( 'bp_after_group_invites_creation_step' ); ?>

				<?php endif; ?>

				<?php do_action( 'groups_custom_create_steps' ) // Allow plugins to add custom group creation steps ?>

				<?php do_action( 'bp_before_group_creation_step_buttons' ); ?>

				<?php if ( 'crop-image' != bp_get_avatar_admin_step() ) : ?>
					<div class="submit" id="previous-next">
						<?php /* Previous Button */ ?>
						<?php if ( !bp_is_first_group_creation_step() ) : ?>
							<input type="button" value="&larr; <?php _e('Previous Step', 'buddypress') ?>" id="group-creation-previous" name="previous" onclick="location.href='<?php bp_group_creation_previous_link() ?>'" />
						<?php endif; ?>

						<?php /* Next Button */ ?>
						<?php if ( !bp_is_last_group_creation_step() && !bp_is_first_group_creation_step() ) : ?>
							<input type="submit" value="<?php _e('Next Step', 'buddypress') ?> &rarr;" id="group-creation-next" name="save" />
						<?php endif;?>

						<?php /* Create Button */ ?>
						<?php if ( bp_is_first_group_creation_step() ) : ?>
							<input type="submit" value="<?php _e('Create '.ucfirst($group_type).' and Continue', 'buddypress') ?> &rarr;" id="group-creation-create" name="save" />
						<?php endif; ?>

						<?php /* Finish Button */ ?>
						<?php if ( bp_is_last_group_creation_step() ) : ?>
							<input type="submit" value="<?php _e('Finish', 'buddypress') ?> &rarr;" id="group-creation-finish" name="save" />
						<?php endif; ?>
					</div>
				<?php endif;?>

				<?php do_action( 'bp_after_group_creation_step_buttons' ); ?>

				<?php /* Don't leave out this hidden field */ ?>
				<input type="hidden" name="group_id" id="group_id" value="<?php bp_new_group_id() ?>" />

				<?php do_action( 'bp_directory_groups_content' ) ?>

			</div><!-- .item-body -->

			<?php do_action( 'bp_after_create_group' ) ?>

		</form>
		</div>
        <?php add_action( 'genesis_before_sidebar_widget_area', create_function( '', 'include( get_stylesheet_directory() . "/members/single/sidebar.php" );' ) ); ?>
<?php } ?>
<?php genesis() ?>
