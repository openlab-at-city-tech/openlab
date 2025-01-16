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
$checked    = 'checked';
?>
<div class="nextgen-gallery-onboarding-form-step " id="recommended">
	<div class="nextgen-gallery-onboarding-wizard-body nextgen-gallery-wizard-features">
		<div class="steps"><?php esc_html_e( 'Step - 3 of 5', 'nextgen-gallery' ); ?></div>
		<div class="nextgen-gallery-onboarding-settings-row no-border no-margin">
			<div class="settings-name">
				<h2><?php esc_html_e( 'Add Recommended Features to Grow Your Business', 'nextgen-gallery' ); ?></h2>
				<div class="name small-margin">
				</div>
				<div class="nextgen-gallery-onboarding-description">
					<?php esc_html_e( 'We\'ve preselected recommended features tailored to your site category, you have the option to further customise your site using the following features.', 'nextgen-gallery' ); ?>
				</div>
			</div>
		</div>
		<?php
		$is_installed = $onboarding->is_recommended_plugin_installed( 'wpforms-lite' );
		$installs_text = ! $is_installed ? esc_html__( 'Installs WP Forms', 'nextgen-gallery' ) : esc_html__( 'WP Forms is already installed', 'nextgen-gallery' );

		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="wpforms-lite"><?php esc_html_e( 'Form Builder', 'nextgen-gallery' ); ?></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Build forms for your photography business using the fastest form builder ever WPForms', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="wpforms-lite-desc"><?php echo esc_html( $installs_text ); ?></div>

					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" id="wpforms-lite" value="wpforms-lite" data-name="<?php esc_html_e( 'WP Forms', 'nextgen-gallery' ); ?>" class="recommended <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
						<span class="fancy-checkbox blue">
							<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
								<path
									fill-rule="evenodd"
									clip-rule="evenodd"
									d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
									fill="currentColor"
								></path>
							</svg>
						</span>
					</span>
				</span>
					</label>
				</div>
			</div>
		</div>
		<?php
		$is_installed = $onboarding->is_recommended_plugin_installed( 'google-analytics-for-wordpress' );
		$installs_text = ! $is_installed ? esc_html__( 'Installs Google Analytics For WordPress', 'nextgen-gallery' ) : esc_html__( 'Google Analytics For WordPress is already installed', 'nextgen-gallery' );

		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="google-analytics-for-wordpress"><?php esc_html_e( 'Website Analytics', 'nextgen-gallery' ); ?></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Understand your customer’s interactions across your Galleries with MonsterInsight.', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="google-analytics-for-wordpress-desc"><?php echo esc_html( $installs_text ); ?></div>

					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" id="google-analytics-for-wordpress" data-name="<?php esc_html_e( 'Google Analytics For WordPress', 'nextgen-gallery' ); ?>" value="google-analytics-for-wordpress" class="recommended <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
						<span class="fancy-checkbox blue">
							<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
								<path
									fill-rule="evenodd"
									clip-rule="evenodd"
									d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
									fill="currentColor"
								></path>
							</svg>
						</span>
					</span>
				</span>
					</label>
				</div>
			</div>
		</div>
		<?php
		$is_installed = $onboarding->is_recommended_plugin_installed( 'duplicator' );
		$installs_text = ! $is_installed ? esc_html__( 'Installs Duplicator', 'nextgen-gallery' ) : esc_html__( 'Duplicator is already installed', 'nextgen-gallery' );

		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="duplicator"><?php esc_html_e( 'Website Backups', 'nextgen-gallery' ); ?></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Backup, Migrate, Secure your gallery images from getting lost with Duplicator.', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="duplicator-desc"><?php echo esc_html( $installs_text ); ?></div>

					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" id="duplicator" value="duplicator" data-name="<?php esc_html_e( 'Duplicator', 'nextgen-gallery' ); ?>" class="recommended <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
						<span class="fancy-checkbox blue">
							<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
								<path
									fill-rule="evenodd"
									clip-rule="evenodd"
									d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
									fill="currentColor"
								></path>
							</svg>
						</span>
					</span>
				</span>
					</label>
				</div>
			</div>
		</div>
		<?php
		$is_installed = $onboarding->is_recommended_plugin_installed( 'all-in-one-seo-pack' );
		$installs_text = ! $is_installed ? esc_html__( 'Installs All In One SEO Pack', 'nextgen-gallery' ) : esc_html__( 'All In One SEO Pack is already installed', 'nextgen-gallery' );

		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="all-in-one-seo-pack"><?php esc_html_e( 'SEO Toolkit', 'nextgen-gallery' ); ?></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Improve your website search rankings for your gallery with AIOSEO', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="all-in-one-seo-pack-desc"><?php echo esc_html( $installs_text ); ?></div>

					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" id="all-in-one-seo-pack" value="all-in-one-seo-pack" data-name="<?php esc_html_e( 'All In One SEO Pack', 'nextgen-gallery' ); ?>" class="recommended <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
						<span class="fancy-checkbox blue">
							<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
								<path
									fill-rule="evenodd"
									clip-rule="evenodd"
									d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
									fill="currentColor"
								></path>
							</svg>
						</span>
					</span>
				</span>
					</label>
				</div>
			</div>
		</div>
		<?php
		$is_installed = $onboarding->is_recommended_plugin_installed( 'wp-mail-smtp' );
		$installs_text = ! $is_installed ? esc_html__( 'Installs WP Mail SMTP', 'nextgen-gallery' ) : esc_html__( 'WP Mail SMTP is already installed', 'nextgen-gallery' );

		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="wp-mail-smtp"><?php esc_html_e( 'Email Deliverability', 'nextgen-gallery' ); ?></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Set up your WordPress to use a trusted email provider with WP Mail SMTP', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="wp-mail-smtp-desc"><?php echo esc_html( $installs_text ); ?></div>

					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" id="wp-mail-smtp" data-name="<?php esc_html_e( 'WP Mail SMTP', 'nextgen-gallery' ); ?>" value="wp-mail-smtp" class="recommended <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
						<span class="fancy-checkbox blue">
							<svg viewBox="0 0 14 11" fill="none" xmlns="http://www.w3.org/2000/svg" class="nextgen-gallery-checkmark">
								<path
									fill-rule="evenodd"
									clip-rule="evenodd"
									d="M10.8542 1.37147C11.44 0.785682 12.3897 0.785682 12.9755 1.37147C13.5613 1.95726 13.5613 2.907 12.9755 3.49279L6.04448 10.4238C5.74864 10.7196 5.35996 10.8661 4.97222 10.8631C4.58548 10.8653 4.19805 10.7189 3.90298 10.4238L1.0243 7.5451C0.438514 6.95931 0.438514 6.00956 1.0243 5.42378C1.61009 4.83799 2.55983 4.83799 3.14562 5.42378L4.97374 7.2519L10.8542 1.37147Z"
									fill="currentColor"
								></path>
							</svg>
						</span>
					</span>
				</span>
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="nextgen-gallery-onboarding-wizard-footer">
		<div class="go-back"><a href="#features" data-prev="1" class="nextgen-gallery-onboarding-wizard-back-btn nextgen-gallery-onboarding-btn-prev" id="" >←&nbsp;<?php esc_html_e( 'Go back', 'nextgen-gallery' ); ?></a></div>
		<div class="spacer"></div><button type="button" data-next="3" class="btn nextgen-gallery-onboarding-wizard-primary-btn nextgen-gallery-onboarding-btn-next " id="nextgen-gallery-install-recommended"><?php esc_html_e( 'Save and Continue', 'nextgen-gallery' ); ?>&nbsp; →</button>
	</div>
</div>
<div class="selected-plugins-names" style="display: none;"></div>
<br>
