<?php
add_filter('genesis_pre_get_option_site_layout', 'cuny_home_layout');
function cuny_home_layout($opt) {
    $opt = 'content-sidebar';
    return $opt;
}

remove_action('genesis_loop', 'genesis_do_loop');
add_action('genesis_loop', 'cuny_registration_page' );

function cuny_registration_page() {
		do_action( 'bp_before_register_page' ) ?>

		<div class="page" id="register-page">

			<form action="" name="signup_form" id="signup_form" class="standard-form" method="post" enctype="multipart/form-data">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>

				<h1 class="entry-title"><?php _e( 'Create an Account', 'buddypress' ) ?></h1>

				<?php do_action( 'template_notices' ) ?>

				<p><?php _e( 'Registering for the City Tech OpenLab is easy. Just fill in the fields below and we\'ll get a new account set up for you in no time.', 'buddypress' ) ?></p>
				<p>Because the OpenLab is a space for collaboration between members of the City Tech community, a City Tech email address is required to use the site.</p> 
				<?php do_action( 'bp_before_account_details_fields' ) ?>

				<div class="register-section" id="basic-details-section">

					<?php /***** Basic Account Details ******/ ?>

					<h4><?php _e( 'Account Details', 'buddypress' ) ?></h4>

					<label for="signup_username"><?php _e( 'Username', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?> (lowercase & no special characters)</label>
					<?php do_action( 'bp_signup_username_errors' ) ?>
					<input type="text" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />

					<label for="signup_email"><?php _e( 'Email Address (required) <div class="email-requirements">Please use your City Tech email address to register</div>', 'buddypress' ) ?> </label>
					<?php do_action( 'bp_signup_email_errors' ) ?>
					<input type="text" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />

					<label for="signup_email_confirm">Confirm Email Address (required)</label>
					<input type="text" name="signup_email_confirm" id="signup_email_confirm" value="" />

					<label for="signup_password"><?php _e( 'Choose a Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label>
					<?php do_action( 'bp_signup_password_errors' ) ?>
					<input type="password" name="signup_password" id="signup_password" value="" />

					<label for="signup_password_confirm"><?php _e( 'Confirm Password', 'buddypress' ) ?> <?php _e( '(required)', 'buddypress' ) ?></label>
					<?php do_action( 'bp_signup_password_confirm_errors' ) ?>
					<input type="password" name="signup_password_confirm" id="signup_password_confirm" value="" />

				</div><!-- #basic-details-section -->

				<?php do_action( 'bp_after_account_details_fields' ) ?>

				<?php /***** Extra Profile Details ******/ ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<?php do_action( 'bp_before_signup_profile_fields' ) ?>

					<div class="register-section" id="profile-details-section">

						<h4><?php _e( 'Public Profile Details', 'buddypress' ) ?></h4>

						<p>Your responses in the form fields below will be displayed on your profile page, which is open to the public. You can always add, edit, or remove information at a later date.</p>

						<?php echo wds_get_register_fields();?>

                        <?php do_action( 'bp_after_signup_profile_fields' ) ?>

					</div><!-- #profile-details-section -->



				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ) ?>

				<div class="submit">
					<input style="display:none;" type="submit" name="signup_submit" id="signup_submit" value="<?php _e( 'Complete Sign Up', 'buddypress' ) ?> &rarr;" />
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ) ?>

				<?php wp_nonce_field( 'bp_new_signup' ) ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<h2><?php _e( 'Sign Up Complete!', 'buddypress' ) ?></h2>

				<?php do_action( 'template_notices' ) ?>

				<?php if ( bp_registration_needs_activation() ) : ?>
					<p><?php _e( 'You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.', 'buddypress' ) ?></p>
				<?php else : ?>
					<p><?php _e( 'You have successfully created your account! Please log in using the username and password you have just created.', 'buddypress' ) ?></p>
				<?php endif; ?>

				<!--<?php if ( bp_is_active( 'xprofile' ) && !(int)bp_get_option( 'bp-disable-avatar-uploads' ) ) : ?>

					<?php if ( 'upload-image' == bp_get_avatar_admin_step() ) : ?>

						<h4><?php _e( 'Your Current Avatar', 'buddypress' ) ?></h4>
						<p><?php _e( "We've fetched an avatar for your new account. If you'd like to change this, why not upload a new one?", 'buddypress' ) ?></p>

						<div id="signup-avatar">
							<?php bp_signup_avatar() ?>
						</div>

						<p>
							<input type="file" name="file" id="file" />
							<input type="submit" name="upload" id="upload" value="<?php _e( 'Upload Image', 'buddypress' ) ?>" />
							<input type="hidden" name="action" id="action" value="bp_avatar_upload" />
							<input type="hidden" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />
							<input type="hidden" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />
						</p>

						<?php wp_nonce_field( 'bp_avatar_upload' ) ?>

					<?php endif; ?>

					<?php if ( 'crop-image' == bp_get_avatar_admin_step() ) : ?>

						<h3><?php _e( 'Crop Your New Avatar', 'buddypress' ) ?></h3>

						<img src="<?php bp_avatar_to_crop() ?>" id="avatar-to-crop" class="avatar" alt="<?php _e( 'Avatar to crop', 'buddypress' ) ?>" />

						<div id="avatar-crop-pane">
							<img src="<?php bp_avatar_to_crop() ?>" id="avatar-crop-preview" class="avatar" alt="<?php _e( 'Avatar preview', 'buddypress' ) ?>" />
						</div>

						<input type="submit" name="avatar-crop-submit" id="avatar-crop-submit" value="<?php _e( 'Crop Image', 'buddypress' ) ?>" />

						<input type="hidden" name="signup_email" id="signup_email" value="<?php bp_signup_email_value() ?>" />
						<input type="hidden" name="signup_username" id="signup_username" value="<?php bp_signup_username_value() ?>" />
						<input type="hidden" name="signup_avatar_dir" id="signup_avatar_dir" value="<?php bp_signup_avatar_dir_value() ?>" />

						<input type="hidden" name="image_src" id="image_src" value="<?php bp_avatar_to_crop_src() ?>" />
						<input type="hidden" id="x" name="x" />
						<input type="hidden" id="y" name="y" />
						<input type="hidden" id="w" name="w" />
						<input type="hidden" id="h" name="h" />

						<?php wp_nonce_field( 'bp_avatar_cropstore' ) ?>

					<?php endif; ?>

				<?php endif; ?> -->

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ) ?>

			</form>

		</div>

		<?php do_action( 'bp_after_register_page' ) ?>

	<?php do_action( 'bp_after_directory_activity_content' ) ?>
	
	<!-- add blank sidebar -->
	<?php
		add_action('genesis_before_sidebar_widget_area', 'cuny_buddypress_register_actions');
	
	function cuny_buddypress_register_actions() {
		global $bp;?>
		<h2 class="sidebar-title">&nbsp;</h2>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
		<p>&nbsp;</p>
	<?php
	}
	?>
	<script type="text/javascript">
		jQuery(document).ready( function() {
			if ( jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show') )
				jQuery('div#blog-details').toggle();

			jQuery( 'input#signup_with_blog' ).click( function() {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
<?php }

genesis();