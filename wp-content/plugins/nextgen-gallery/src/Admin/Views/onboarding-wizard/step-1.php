<?php
/**
 * Outputs the first step of the Onboarding Wizard.
 *
 * @since   3.59.4
 *
 * @package NextGEN Gallery
 */

use Imagely\NGG\Admin\Onboarding_Wizard;
$onboarding = new Onboarding_Wizard();
?>
	<div class="nextgen-gallery-onboarding-form-step nextgen-gallery-onboarding-form-step-active" id="general">
		<form id="nextgen-gallery-general" method="post" >
		<div class="nextgen-gallery-onboarding-wizard-body">
			<div class="steps"><?php esc_html_e( 'Step - 1 of 5', 'nextgen-gallery' ); ?></div>
			<div class="nextgen-gallery-onboarding-settings-row no-padding no-border">
				<div class="settings-name">
					<h2><?php esc_html_e( 'What best describes you?', 'nextgen-gallery' ); ?></h2>
					<div class="name small-margin">
					</div>
					<div class="nextgen-gallery-onboarding-description"><?php esc_html_e( 'Tell us a bit about this website to help us craft the perfect experience for you.', 'nextgen-gallery' ); ?></div>
				</div>
				<div class="nextgen-gallery-onboarding-input-container">
					<div class="nextgen-gallery-onboarding-input">
						<div class="nextgen-gallery-options">
							<div class="nextgen-gallery-option">
								<input type="radio" required id="photographer" name="eow[_user_type]" value="photographer">
								<label for="photographer"><?php esc_html_e( 'Photographer', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option">
								<input type="radio" required id="online-store" name="eow[_user_type]" value="online-store">
								<label for="online-store"><?php esc_html_e( 'Online Store', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option">
								<input type="radio" required id="business_store_owner" name="eow[_user_type]" value="corporate-website">
								<label for="business_store_owner"><?php esc_html_e( 'Business Owner', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option">
								<input type="radio" required id="designer-artist" name="eow[_user_type]" value="designer-artist">
								<label for="designer-artist"><?php esc_html_e( 'Designer / Artist', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option">
								<input type="radio" required id="blogger" name="eow[_user_type]" value="blogger">
								<label for="blogger"><?php esc_html_e( 'Blogger', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option">
								<input type="radio" required id="something_else" name="eow[_user_type]" value="other">
								<label for="something_else"><?php esc_html_e( 'Other', 'nextgen-gallery' ); ?></label>
							</div>
						</div>
						<div class="nextgen-gallery-options" id="others_div" style="display: none;">
							<input type="text" id="others" name="eow[_others]" value="<?php echo esc_attr( $onboarding->get_onboarding_data( '_others' ) ); ?>" placeholder="<?php esc_attr_e( 'What best describes you?', 'nextgen-gallery' ); ?>">
					</div>
					</div>
				</div>
				<div class="settings-name" id="ngg-pro-upsell" style="display: none;">
 					<div class="nextgen-gallery-onboarding-description small-margin">
 						<?php esc_html_e( 'Unlock Built-In E-commerce to sell your photos online.', 'nextgen-gallery' ); ?>
					  <a target="_blank" href="<?php echo esc_url('http://www.imagely.com/lite/?utm_source=WordPress&utm_campaign=nextgen-galleryliteplugin&utm_medium=nextgen-gallery-wizard-welcome') ?>" ><?php esc_html_e('Upgrade Now', 'nextgen-gallery' ); ?>
						  <svg width="13" height="9" viewBox="0 0 13 9" fill="none" xmlns="http://www.w3.org/2000/svg">
							  <path d="M7.73438 9L6.65156 7.9125L9.39727 5.25H0V3.75H9.39727L6.65156 1.0875L7.73438 0L12.375 4.5L7.73438 9Z" fill="#618E00"/>
						  </svg>
					  </a>
					</div>
 				</div>
				<div class="settings-name">
					<div class="name small-margin">
						<?php esc_html_e( 'Join NextGEN Community', 'nextgen-gallery' ); ?>
					</div>
					<div class="nextgen-gallery-onboarding-description"></div>
				</div>
				<div class="nextgen-gallery-onboarding-input-container">
					<div class="nextgen-gallery-onboarding-input">
						<div class="nextgen-gallery-options">
							<div class="nextgen-gallery-option width-20">
								<label for="email_address"><?php esc_html_e( 'Email Address', 'nextgen-gallery' ); ?></label>
							</div>
							<div class="nextgen-gallery-option width-80">
								<?php $email_address = ( $onboarding->get_onboarding_data( '_email_address' ) ) ? $onboarding->get_onboarding_data( '_email_address' ) : get_bloginfo( 'admin_email' ); ?>
								<input type="email" id="email_address" name="eow[_email_address]" value="<?php echo esc_attr( $email_address ); ?>" placeholder=""/>
								<div class="nextgen-gallery-email-error"></div>
							</div>
						</div>
					</div>
				</div>
					<div class="nextgen-gallery-onboarding-input-container" style="margin-top: -20px;">
						<div class="nextgen-gallery-onboarding-input">
							<div class="nextgen-gallery-options email_opt_in">
								<input type="checkbox" id="email_opt_in" name="eow[_email_opt_in]" value="yes" />
								<label for="email_opt_in"><?php esc_html_e( 'I agree to receive important communications from NextGEN Gallery.', 'nextgen-gallery' ); ?></label>
							</div>
						</div>
					</div>
				</div>
			<div class="nextgen-gallery-onboarding-settings-row no-border">
				<div class="nextgen-gallery-onboarding-input-container">
					<div class="nextgen-gallery-onboarding-input">
						<div class="nextgen-gallery-options nextgen-gallery-checkbox">
							<label class="nextgen-gallery-toggle">
								<input id="nextgen-gallery-tracking" type="checkbox"  class="" name="eow[_usage_tracking]" value="yes" >
								<span class="nextgen-gallery-switch"></span>
								<span class="description " >
									<?php esc_html_e( 'Help us better understand our users and their website needs.', 'nextgen-gallery' ); ?>
								</span>
							</label>
								<div class="tooltip-container" >
									<svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-circle-question-mark"><path fill-rule="evenodd" clip-rule="evenodd" d="M1.6665 10.0001C1.6665 5.40008 5.39984 1.66675 9.99984 1.66675C14.5998 1.66675 18.3332 5.40008 18.3332 10.0001C18.3332 14.6001 14.5998 18.3334 9.99984 18.3334C5.39984 18.3334 1.6665 14.6001 1.6665 10.0001ZM10.8332 13.3334V15.0001H9.1665V13.3334H10.8332ZM9.99984 16.6667C6.32484 16.6667 3.33317 13.6751 3.33317 10.0001C3.33317 6.32508 6.32484 3.33341 9.99984 3.33341C13.6748 3.33341 16.6665 6.32508 16.6665 10.0001C16.6665 13.6751 13.6748 16.6667 9.99984 16.6667ZM6.6665 8.33341C6.6665 6.49175 8.15817 5.00008 9.99984 5.00008C11.8415 5.00008 13.3332 6.49175 13.3332 8.33341C13.3332 9.40251 12.6748 9.97785 12.0338 10.538C11.4257 11.0695 10.8332 11.5873 10.8332 12.5001H9.1665C9.1665 10.9824 9.9516 10.3806 10.6419 9.85148C11.1834 9.43642 11.6665 9.06609 11.6665 8.33341C11.6665 7.41675 10.9165 6.66675 9.99984 6.66675C9.08317 6.66675 8.33317 7.41675 8.33317 8.33341H6.6665Z" fill="currentColor"></path></svg>
									<span class="tooltip-text"><?php esc_html_e( 'This will let us monitor the usage of your galleries and insights which will be used to improve the NextGEN Gallery', 'nextgen-gallery' ); ?></span>
								</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="nextgen-gallery-onboarding-wizard-footer">
				<div class="go-back"><a href="#welcome" class="nextgen-gallery-onboarding-wizard-back-btn" id="nextgen-gallery-onboarding-back-to-welcome">←&nbsp;<?php esc_html_e( 'Go back', 'nextgen-gallery' ); ?></a></div>
				<div class="spacer"></div><input type="submit" data-next="1" class="btn nextgen-gallery-onboarding-wizard-primary-btn nextgen-gallery-onboarding-btn-next " id="save-opt-in" value="<?php esc_attr_e( 'Save and Continue', 'nextgen-gallery' ); ?>&nbsp; →"/>
		</div>
		</form>
	</div>
<script>
	let user_type = '<?php echo esc_js( $onboarding->get_onboarding_data( '_user_type' ) ); ?>';
	if ( user_type ) {
		document.querySelector('input[name="eow[_user_type]"][value="' + user_type + '"]').checked = true;
		( user_type === 'other' ) ? document.getElementById('others_div').style.display = 'block' : document.getElementById('others_div').style.display = 'none';
		( user_type === 'online-store' ) ? document.getElementById('ngg-pro-upsell').style.display = 'block' : document.getElementById('ngg-pro-upsell').style.display = 'none';
	}

	// if email_opt_in is checked show checked.
	let email_optIn = '<?php echo esc_js( $onboarding->get_onboarding_data( '_email_opt_in' ) ); ?>';
	if ( email_optIn ) {
		document.querySelector('input[name="eow[_email_opt_in]"]').checked = true;
	}

	// if nextgen-gallery-tracking is checked show checked.
	let usage_tracking = '<?php echo esc_js( $onboarding->get_onboarding_data( '_usage_tracking' ) ); ?>';
	if ( usage_tracking ) {
		document.querySelector('input[name="eow[_usage_tracking]"]').checked = true;
	}

	if(!user_type || user_type === 'undefined') {
		// Set true by default.
		document.querySelector('input[name="eow[_email_opt_in]"]').checked = true;
	}



</script>
