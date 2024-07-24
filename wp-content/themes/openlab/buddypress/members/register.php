<?php /**
 *  sign up form template
 *
 */

wp_enqueue_script( 'openlab-academic-units' );

$ajaxurl = bp_core_ajax_url();

$first_name_field_id   = openlab_get_xprofile_field_id( 'First Name' );
$last_name_field_id    = openlab_get_xprofile_field_id( 'Last Name' );

$first_name_submitted   = isset( $_POST[ 'field_' . $first_name_field_id ] ) ? $_POST[ 'field_' . $first_name_field_id ] : '';
$last_name_submitted    = isset( $_POST[ 'field_' . $last_name_field_id ] ) ? $_POST[ 'field_' . $last_name_field_id ] : '';
$account_type_submitted = isset( $_POST['openlab-account-type'] ) ? $_POST['openlab-account-type'] : '';
$account_description_approval_submitted = isset( $_POST['account-description-approval'] ) ? $_POST['account-description-approval'] : '';

$account_type_options = array_map(
	function( $type ) {
		return [
			'label' => $type->name,
			'slug'  => $type->slug,
			'id'    => $type->term_id
		];
	},
	openlab_get_member_types()
);

?>

<div class="col-sm-18">
	<?php do_action( 'bp_before_register_page' ); ?>

	<div class="page" id="register-page">

		<div id="openlab-main-content"></div>

		<h1 class="entry-title"><?php esc_html_e( 'Create an Account', 'buddypress' ); ?></h1>

		<form action="" name="signup_form" id="signup_form" class="standard-form form-panel" method="post" enctype="multipart/form-data" data-parsley-trigger="blur">

			<?php if ( 'request-details' == bp_get_current_signup_step() ) : ?>
				<input type="hidden" id="has-signup-errors" value="<?php echo ! empty( buddypress()->signup->errors ) ? 'true' : 'false'; ?>" />

				<div class="panel panel-default" id="panel-welcome">
					<div class="panel-heading semibold">Welcome to the OpenLab!</div>

					<div class="panel-body">
						<div class="form-group">
							<p>To get started, please select your <strong>Account Type</strong>: (required)</p>
							<label class="screen-reader-text control-label" for="openlab-account-type">Account Type <span class="label-gloss">(required)</span></label>
							<div id="openlab-account-type-error" class="error-container"></div>
							<?php do_action( 'bp_field_account_type_errors' ); ?>
							<select
								class="form-control"
								type="text"
								name="openlab-account-type"
								id="openlab-account-type"
								data-parsley-required
								data-parsley-required-message="Account type is required."
								data-parsley-errors-container="#openlab-account-type-error"
							/>
								<option value="">----</option>
								<?php foreach ( $account_type_options as $account_type_option ) : ?>
									<option value="<?php echo esc_attr( $account_type_option['slug'] ); ?>" <?php selected( $account_type_submitted, $account_type_option['slug'] ); ?>><?php echo esc_html( $account_type_option['label'] ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div id="account-type-description-and-approval">
							<div class="account-type-description" data-account-type="faculty">
								<h2>About the OpenLab (Faculty)</h2>

								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin est est, tincidunt id dignissim sed, luctus in metus. Sed suscipit pellentesque lorem, at vehicula risus rhoncus in. Mauris non porttitor dolor. Praesent facilisis eu arcu vel posuere. Ut viverra felis mi. Duis mollis sapien a tortor bibendum, a ultricies nunc aliquam. Cras purus augue, tincidunt at enim non, mollis congue diam. Cras quis nibh velit. Nullam bibendum nisi et libero efficitur pretium.
								Proin nec nunc et lorem feugiat mattis porta id libero.
								Mauris turpis est, porta at odio at, fringilla fermentum risus. Sed in urna non est tempus egestas eu nec leo. In et tortor elit.
								Donec sodales feugiat faucibus. Nullam nec magna vel neque commodo egestas vitae at dolor.
								Fusce ullamcorper felis felis, at ultrices neque scelerisque nec.
							</div>

							<div class="account-type-description" data-account-type="staff">
								<h2>About the OpenLab (Staff)</h2>

								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin est est, tincidunt id dignissim sed, luctus in metus. Sed suscipit pellentesque lorem, at vehicula risus rhoncus in. Mauris non porttitor dolor. Praesent facilisis eu arcu vel posuere. Ut viverra felis mi. Duis mollis sapien a tortor bibendum, a ultricies nunc aliquam. Cras purus augue, tincidunt at enim non, mollis congue diam. Cras quis nibh velit. Nullam bibendum nisi et libero efficitur pretium.
								Proin nec nunc et lorem feugiat mattis porta id libero.
								Mauris turpis est, porta at odio at, fringilla fermentum risus. Sed in urna non est tempus egestas eu nec leo. In et tortor elit.
								Donec sodales feugiat faucibus. Nullam nec magna vel neque commodo egestas vitae at dolor.
								Fusce ullamcorper felis felis, at ultrices neque scelerisque nec.
							</div>

							<div class="account-type-description" data-account-type="student">
								<h2>About the OpenLab (Student)</h2>

								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin est est, tincidunt id dignissim sed, luctus in metus. Sed suscipit pellentesque lorem, at vehicula risus rhoncus in. Mauris non porttitor dolor. Praesent facilisis eu arcu vel posuere. Ut viverra felis mi. Duis mollis sapien a tortor bibendum, a ultricies nunc aliquam. Cras purus augue, tincidunt at enim non, mollis congue diam. Cras quis nibh velit. Nullam bibendum nisi et libero efficitur pretium.
								Proin nec nunc et lorem feugiat mattis porta id libero.
								Mauris turpis est, porta at odio at, fringilla fermentum risus. Sed in urna non est tempus egestas eu nec leo. In et tortor elit.
								Donec sodales feugiat faucibus. Nullam nec magna vel neque commodo egestas vitae at dolor.
								Fusce ullamcorper felis felis, at ultrices neque scelerisque nec.
							</div>

							<div class="account-type-description" data-account-type="alumni">
								<h2>About the OpenLab (Alumni)</h2>

								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin est est, tincidunt id dignissim sed, luctus in metus. Sed suscipit pellentesque lorem, at vehicula risus rhoncus in. Mauris non porttitor dolor. Praesent facilisis eu arcu vel posuere. Ut viverra felis mi. Duis mollis sapien a tortor bibendum, a ultricies nunc aliquam. Cras purus augue, tincidunt at enim non, mollis congue diam. Cras quis nibh velit. Nullam bibendum nisi et libero efficitur pretium.
								Proin nec nunc et lorem feugiat mattis porta id libero.
								Mauris turpis est, porta at odio at, fringilla fermentum risus. Sed in urna non est tempus egestas eu nec leo. In et tortor elit.
								Donec sodales feugiat faucibus. Nullam nec magna vel neque commodo egestas vitae at dolor.
								Fusce ullamcorper felis felis, at ultrices neque scelerisque nec.
							</div>

							<div class="account-type-description" data-account-type="non-city-tech">
								<h2>About the OpenLab (non-city-tech)</h2>

								Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin est est, tincidunt id dignissim sed, luctus in metus. Sed suscipit pellentesque lorem, at vehicula risus rhoncus in. Mauris non porttitor dolor. Praesent facilisis eu arcu vel posuere. Ut viverra felis mi. Duis mollis sapien a tortor bibendum, a ultricies nunc aliquam. Cras purus augue, tincidunt at enim non, mollis congue diam. Cras quis nibh velit. Nullam bibendum nisi et libero efficitur pretium.
								Proin nec nunc et lorem feugiat mattis porta id libero.
								Mauris turpis est, porta at odio at, fringilla fermentum risus. Sed in urna non est tempus egestas eu nec leo. In et tortor elit.
								Donec sodales feugiat faucibus. Nullam nec magna vel neque commodo egestas vitae at dolor.
								Fusce ullamcorper felis felis, at ultrices neque scelerisque nec.
							</div>
						</div>

						<div class="form-group">
							<fieldset class="account-description-approval-fieldset">
								<input type="radio" name="account-description-approval" id="account-description-approval-yes" value="yes" data-parsley-required data-parsley-required-message="Please confirm that you have read the description of the account type." <?php checked( $account_description_approval_submitted, 'yes' ); ?> /> <label for="account-description-approval-yes">Yes, I understand</label><br />
								<input type="radio" name="account-description-approval" id="account-description-approval-no" value="no" data-parsley-required data-parsley-required-message="Please confirm that you have read the description of the account type." <?php checked( $account_description_approval_submitted, 'no' ); ?> /> <label for="account-description-approval-no">No, I have questions</label>
							</fieldset>

							<div class="registration-continue-button" id="registration-continue-button-yes">
								<button class="btn btn-primary" href="#">Continue with Sign-up</button>
							</div>

							<div class="registration-continue-button" id="registration-continue-button-no">
								<a class="btn btn-primary" target="_blank" href="https://openlab.citytech.cuny.edu/blog/help/contact-us/">Get Help</a>
							</div>
						</div>
					</div><!-- .panel-body -->
				</div>

				<div class="panel panel-default">
					<div class="panel-heading semibold">Account Details</div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<p>For verification that you are a member of the City Tech community, please provide your City Tech email and full name. These are not displayed anywhere publicly on the site and are only visible to faculty and staff site administrators.</p>

						<?php do_action( 'bp_before_account_details_fields' ); ?>

						<div class="register-section" id="basic-details-section">

							<?php /* Basic Account Details */ ?>

							<div class="form-group">
								<label class="control-label" id="signup-email-label" for="signup_email"><span class="label-text">City Tech Email Address</span> <span class="label-gloss">(required, but not displayed on Public Profile) <div class="email-requirements"></div></span></label>
								<div id="signup_email_error" class="error-container"></div>
								<?php do_action( 'bp_signup_email_errors' ); ?>
								<input
									class="form-control email-autocomplete"
									type="text"
									name="signup_email"
									id="signup_email"
									value="<?php echo esc_attr( openlab_post_value( 'signup_email' ) ); ?>"
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Email is required."
									data-parsley-type="email"
									data-parsley-group="email"
									data-parsley-iff="#signup_email_confirm"
									data-parsley-iff-message=""
									data-parsley-errors-container="#signup_email_error"
									/>

								<label class="control-label" for="signup_email_confirm"><span class="label-text">Confirm Email Address</span> <span class="label-gloss">(required)</span></label>
								<div id="signup_email_confirm_error" class="error-container"></div>
								<input
									class="form-control email-autocomplete"
									type="text"
									name="signup_email_confirm"
									id="signup_email_confirm"
									value="<?php echo esc_attr( openlab_post_value( 'signup_email_confirm' ) ); ?>"
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Confirming your email is required."
									data-parsley-type="email"
									data-parsley-iff="#signup_email"
									data-parsley-iff-message="Email addresses must match."
									data-parsley-group="email"
									data-parsley-errors-container="#signup_email_confirm_error"
									/>
							</div>

							<div class="form-group">
								<label class="control-label" for="field_<?php echo intval( $first_name_field_id ); ?>"><span class="label-text">First Name</span> <span class="label-gloss">(required, but not displayed on Public Profile)</span></label>
								<div id="field_<?php echo esc_attr( $first_name_field_id ); ?>_error" class="error-container"></div>
								<?php do_action( 'bp_field_' . $first_name_field_id . '_errors' ); ?>
								<input
									class="form-control"
									type="text"
									name="field_<?php echo esc_attr( $first_name_field_id ); ?>"
									id="field_<?php echo esc_attr( $first_name_field_id ); ?>"
									data-parsley-required
									data-parsley-required-message="First Name is required."
									data-parsley-errors-container="#field_<?php echo esc_attr( $first_name_field_id ); ?>_error"
									value="<?php echo esc_attr( $first_name_submitted ); ?>"
								/>
							</div>

							<div class="form-group">
								<label class="control-label" for="field_<?php echo intval( $last_name_field_id ); ?>"><span class="label-text">Last Name</span> <span class="label-gloss">(required, but not displayed on Public Profile)</span></label>
								<div id="field_<?php echo esc_attr( $last_name_field_id ); ?>_error" class="error-container"></div>
								<?php do_action( 'bp_field_' . $last_name_field_id . '_errors' ); ?>
								<input
									class="form-control last-name-field"
									type="text"
									name="field_<?php echo esc_attr( $last_name_field_id ); ?>"
									id="field_<?php echo esc_attr( $last_name_field_id ); ?>"
									data-parsley-required
									data-parsley-required-message="Last Name is required."
									data-parsley-errors-container="#field_<?php echo esc_attr( $last_name_field_id ); ?>_error"
									value="<?php echo esc_attr( $last_name_submitted ); ?>"
								/>
							</div>

							<p>Please choose your username. <strong>You don't need to use your real name. We recommend that students do not use their full name.</strong> You will use your username to sign in, and it will also be visible in the URL of your member profile. You cannot change it after you sign up.</p>

							<div class="form-group">
								<label class="control-label" for="signup_username"><span class="label-text">Username</span> <span class="label-gloss">(required) (lowercase & no special characters)</span></label>
								<div id="signup_username_error" class="error-container"></div>
								<?php do_action( 'bp_signup_username_errors' ); ?>
								<?php
								$remote_attr = add_query_arg(
									array(
										'action' => 'openlab_unique_login_check',
										'login'  => '{value}',
									),
									$ajaxurl
								);
								?>
								<input
									class="form-control"
									type="text"
									name="signup_username"
									id="signup_username"
									value="<?php bp_signup_username_value(); ?>"
									data-parsley-lowercase
									data-parsley-nospecialchars
									data-parsley-required
									data-parsley-required-message="Username is required."
									data-parsley-minlength="4"
									data-parsley-remote="<?php echo esc_attr( $remote_attr ); ?>"
									data-parsley-remote-message="That username is already taken."
									data-parsley-errors-container="#signup_username_error"
									/>

							</div>

							<div data-parsley-children-should-match class="form-group">
								<label class="control-label" for="signup_password"><span class="label-text">Choose a Password</span> <span class="label-gloss">(required)</span></label>
								<div id="signup_password_error" class="error-container"></div>
								<?php do_action( 'bp_signup_password_errors' ); ?>
								<div class="password-field">
									<input
										class="form-control"
										type="password"
										name="signup_password"
										id="signup_password"
										value=""
										data-parsley-trigger="blur"
										data-parsley-required
										data-parsley-required-message="Password is required."
										data-parsley-group="password"
										data-parsley-iff="#signup_password_confirm"
										data-parsley-iff-message=""
										data-parsley-errors-container="#signup_password_error"
										/>

									<div id="password-strength-notice" class="password-strength-notice"></div>
								</div>

								<label class="control-label" for="signup_password_confirm"><span class="label-text">Confirm Password</span> <span class="label-gloss">(required)</span></label>
								<div id="signup_password_confirm_error" class="error-container"></div>
								<?php do_action( 'bp_signup_password_confirm_errors' ); ?>
								<input
									class="form-control password-field"
									type="password"
									name="signup_password_confirm"
									id="signup_password_confirm"
									value=""
									data-parsley-trigger="blur"
									data-parsley-required
									data-parsley-required-message="Confirming your password is required."
									data-parsley-group="password"
									data-parsley-iff="#signup_password"
									data-parsley-iff-message="Passwords must match."
									data-parsley-errors-container="#signup_password_confirm_error"
									/>
							</div>

						</div><!-- #basic-details-section -->
					</div>
				</div><!--.panel-->

				<?php do_action( 'bp_after_account_details_fields' ); ?>

				<?php /* Extra Profile Details */ ?>

				<?php if ( bp_is_active( 'xprofile' ) ) : ?>

					<div class="panel panel-default">
						<div class="panel-heading semibold">Public Profile Details</div>
						<div class="panel-body">

							<?php do_action( 'bp_before_signup_profile_fields' ); ?>

							<div class="register-section" id="profile-details-section">

								<p>Your profile page is open to the public. You can edit or change who can see this information here and in your profile settings.</p>

								<?php echo wds_get_register_fields( 'Base' ); // WPCS: XSS ok ?>

								<?php do_action( 'bp_after_signup_profile_fields' ); ?>

							</div><!-- #profile-details-section -->
						</div>
					</div><!--.panel-->

				<?php endif; ?>

				<?php do_action( 'bp_before_registration_submit_buttons' ); ?>

				<div id="sign-up-actions">
					<p class="sign-up-terms">
						By clicking "Complete Sign Up", I agree to the <a class="underline" href="<?php echo esc_attr( home_url( 'about/terms-of-service' ) ); ?>" target="_blank">OpenLab Terms of Use</a> and <a class="underline" href="http://cuny.edu/website/privacy.html" target="_blank">Privacy Policy</a>.
					</p>

					<p id="submitSrMessage" class="sr-only submit-alert" aria-live="polite"></p>

					<div class="submit">
						<input type="submit" name="signup_submit" id="signup_submit" class="btn btn-primary btn-disabled" value="<?php esc_html_e( 'Please Complete Required Fields', 'buddypress' ); ?>" />
					</div>
				</div>

				<?php do_action( 'bp_after_registration_submit_buttons' ); ?>

				<?php wp_nonce_field( 'bp_new_signup' ); ?>

			<?php endif; // request-details signup step ?>

			<?php if ( 'completed-confirmation' == bp_get_current_signup_step() ) : ?>

				<div class="panel panel-default">
					<div class="panel-heading semibold">Sign Up Complete!</div>
					<div class="panel-body">

						<?php do_action( 'template_notices' ); ?>

						<?php if ( bp_registration_needs_activation() ) : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom">You have successfully created your account! To begin using this site you will need to activate your account via the email we have just sent to your address.</p>
						<?php else : ?>
							<p class="bp-template-notice updated no-margin no-margin-bottom">You have successfully created your account! Please log in using the username and password you have just created</p>
						<?php endif; ?>

					</div>
				</div><!--.panel-->

			<?php endif; // completed-confirmation signup step ?>

			<?php do_action( 'bp_custom_signup_steps' ); ?>

		</form>

	</div>

	<?php do_action( 'bp_after_register_page' ); ?>

	<?php do_action( 'bp_after_directory_activity_content' ); ?>

	<script type="text/javascript">
		jQuery(document).ready(function () {
			if (jQuery('div#blog-details').length && !jQuery('div#blog-details').hasClass('show'))
				jQuery('div#blog-details').toggle();

			jQuery('input#signup_with_blog').click(function () {
				jQuery('div#blog-details').fadeOut().toggle();
			});
		});
	</script>
</div><!--content-->
