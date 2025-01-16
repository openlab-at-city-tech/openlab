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

// Check if license key is already verified.
$ngg_pro_key = get_option('photocrati_license_default', null);
?>
<div class="nextgen-gallery-onboarding-form-step nextgen-gallery-wizard-license-key" id="summary">
	<div class="nextgen-gallery-onboarding-wizard-body">
		<div class="steps"><?php esc_html_e( 'Step - 4 of 5', 'nextgen-gallery' ); ?></div>
		<div class="nextgen-gallery-onboarding-settings-row no-border no-margin">
			<div class="settings-name">
				<h2><?php esc_html_e( 'Upgrade to Pro', 'nextgen-gallery' ); ?></h2>
				<div class="name small-margin">
				</div>
				<?php
				// get license data.
				$license_type = $onboarding->get_license_type();
				?>
				<div class="nextgen-gallery-onboarding-description">

					<?php
					// Translators: %s is the license type.
					printf( __( 'You are currently using NextGEN <strong>%s</strong>.', 'nextgen-gallery' ), esc_html( ucfirst( $license_type ) ) ); // // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					?>
				</div>
			</div>
		</div>
		<div class="license-cta-box">
			<div class="">
				<?php
				printf(
				// Translators: %s is the link to upgrade to PRO.
					__( 'To Unlock below features, <strong><a target="_blank" href="%s">Upgrade to PRO</a></strong> and Enter your license key below', 'nextgen-gallery' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					esc_url( 'https://imagely.com/lite/?utm_source=liteplugin&utm_medium=wizard&utm_campaign=wizard' )
				);
				?>
			</div>
			<div class="nextgen-gallery-row" id="selected-add-ons">
				<div class="nextgen-gallery-col col-xs-12 col-sm-6 text-xs-left">
					<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
						<path
							fill-rule="evenodd"
							clip-rule="evenodd"
							d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
							fill="currentColor"
						></path>
					</svg>
					<?php esc_html_e( 'Built-in Ecommerce', 'nextgen-gallery' ); ?>
				</div>
				<div class="nextgen-gallery-col col-xs-12 col-sm-6 text-xs-left">
					<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
						<path
							fill-rule="evenodd"
							clip-rule="evenodd"
							d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
							fill="currentColor"
						></path>
					</svg>
					<?php esc_html_e( 'Accept PayPal, Stripe and Check', 'nextgen-gallery' ); ?>
				</div>
				<div class="nextgen-gallery-col col-xs-12 col-sm-6 text-xs-left">
					<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
						<path
							fill-rule="evenodd"
							clip-rule="evenodd"
							d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
							fill="currentColor"
						></path>
					</svg>
					<?php esc_html_e( 'Advance Selling Features', 'nextgen-gallery' ); ?>
				</div>
				<div class="nextgen-gallery-col col-xs-12 col-sm-6 text-xs-left">
					<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
						<path
							fill-rule="evenodd"
							clip-rule="evenodd"
							d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
							fill="currentColor"
						></path>
					</svg>
					<?php esc_html_e( 'Secure Checkout', 'nextgen-gallery' ); ?>
				</div>
			</div>
		</div>
		<div class="nextgen-gallery-onboarding-settings-row no-border ">
			<div class=" ">
				<p>
					<?php esc_html_e( 'Already purchased? Simply enter your license key below to connect with NextGEN Pro!', 'nextgen-gallery' ); ?>
				</p>
				<form id="nextgen-gallery-settings-verify-key" method="post">
					<div class="nextgen-gallery-row ">
						<div class="nextgen-gallery-col col-xs-12 col-sm-8 text-xs-left nextgen-gallery-onboarding-input">
							<input type="password" required name="nextgen-gallery-license-key" id="nextgen-gallery-settings-key" value="<?php echo esc_html( $ngg_pro_key ) ?>" placeholder="<?php esc_attr_e( 'Enter your license key', 'nextgen-gallery' ); ?>"/>
						</div>
						<div class="nextgen-gallery-col col-xs-12 col-sm-2 text-xs-left">
							<input type="submit" name="nextgen-gallery-verify-submit" value="<?php esc_attr_e( 'Connect', 'nextgen-gallery' ); ?>" class=" btn nextgen-gallery-onboarding-wizard-primary-btn nextgen-gallery-verify-submit" id="nextgen-gallery-settings-connect-btn"/>
						</div>
						<div class="nextgen-gallery-col col-xs-12 col-sm-1 text-xs-right">
							<span class="spinner nextgen-gallery-onboarding-spinner"></span>
						</div>
					</div>
				</form>
				<div class="nextgen-gallery-row ">
					<div class="nextgen-gallery-col col-xs-12 col-sm-12 text-xs-left">
						<div id="license-key-message" class=""></div>
					</div>
				</div>
			</div>
	</div>
	</div>
	<div class="nextgen-gallery-onboarding-wizard-footer">
		<div class="go-back"><a href="#recommended" data-prev="2" class="nextgen-gallery-onboarding-wizard-back-btn nextgen-gallery-onboarding-btn-prev" id="" >←&nbsp;<?php esc_html_e( 'Go back', 'nextgen-gallery' ); ?></a></div>
		<div class="spacer"></div><button type="button" data-next="4" class="btn nextgen-gallery-onboarding-wizard-primary-btn nextgen-gallery-onboarding-btn-next " id="install-nextgen-gallery-addons-btn"><?php esc_html_e( 'Save and Continue', 'nextgen-gallery' ); ?>&nbsp; →</button>
	</div>
</div>
