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
<div class="nextgen-gallery-onboarding-form-step " id="features">
	<div class="nextgen-gallery-onboarding-wizard-body nextgen-gallery-wizard-features">
		<div class="steps"><?php esc_html_e( 'Step - 2 of 5', 'nextgen-gallery' ); ?></div>
		<div class="nextgen-gallery-onboarding-settings-row no-border no-margin">
			<div class="settings-name">
				<h2><?php esc_html_e( 'What Gallery features do you want to enable?', 'nextgen-gallery' ); ?></h2>
				<div class="name small-margin">
				</div>
				<div class="nextgen-gallery-onboarding-description">
					<?php esc_html_e( 'We have already selected our recommended features based on your site category, but you can use the following features to fine-tune your site.', 'nextgen-gallery' ); ?>
				</div>
			</div>
		</div>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<?php esc_html_e( 'Basic Slideshows', 'nextgen-gallery' ); ?>
							<!---->
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Pre-made gallery themes will enhance the look of your galleries.', 'nextgen-gallery' ); ?></div>
						<!---->
					</div>
				</div>
				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round no-clicks disabled">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" class="no-clicks" checked="checked"/>
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
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<?php esc_html_e( 'Albums', 'nextgen-gallery' ); ?>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Create albums, add galleries, manage cover photos, show tag clouds etc.', 'nextgen-gallery' ); ?></div>
					</div>
				</div>
				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round no-clicks disabled">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" class="no-clicks" checked="checked" />
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
		$is_installed = $onboarding->is_recommended_plugin_installed( 'ngg-gallery-themes' );
		$checked      = $is_installed ? 'checked="checked"' : '';
		$installs_text = ! $is_installed ? esc_html__( 'Installs NextGEN Gallery Pro', 'nextgen-gallery' ) : esc_html__( 'NextGEN Pro is already installed', 'nextgen-gallery' );
		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="ngg-gallery-themes"><?php esc_html_e( 'Gallery Themes', 'nextgen-gallery' ); ?><span class="nextgen-gallery-pro-badge">PRO</span></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Stylise your gallery with slideshows, multiple layouts, thumbnail', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="ngg-gallery-themes-desc" style="display: none"><?php echo esc_html( $installs_text ); ?></div>
					</div>
				</div>
				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>" >
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" name="ngg-gallery-themes"  value="ngg-gallery-themes" id="ngg-gallery-themes" data-name="<?php esc_attr_e( 'Gallery Themes', 'nextgen-gallery' ); ?>" class="feature <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
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
		$is_installed = $onboarding->is_recommended_plugin_installed( 'ngg-ecommerce' );
		$checked      = $is_installed ? 'checked="checked"' : '';
		$installs_text = ! $is_installed ? esc_html__( 'Installs NextGEN Gallery Pro', 'nextgen-gallery' ) : esc_html__( 'NextGEN Pro is already installed', 'nextgen-gallery' );
		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="ngg-ecommerce"><?php esc_html_e( 'Built-in Ecommerce', 'nextgen-gallery' ); ?><span class="nextgen-gallery-pro-badge">PRO</span></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Features transform your website into e-commerce to sell your photos.', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="ngg-ecommerce-desc" style="display: none"><?php echo esc_html( $installs_text ); ?></div>
					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" data-name="" name="ngg-ecommerce"  value="ngg-ecommerce" id="ngg-ecommerce" class="feature <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?> />
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
		$is_installed = $onboarding->is_recommended_plugin_installed( 'ngg-ecom-management' );
		$checked      = $is_installed ? 'checked="checked"' : '';
		$installs_text = ! $is_installed ? esc_html__( 'Installs NextGEN Gallery Pro', 'nextgen-gallery' ) : esc_html__( 'NextGEN Pro is already installed', 'nextgen-gallery' );
		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="ngg-ecom-management"><?php esc_html_e( 'E-commerce Management', 'nextgen-gallery' ); ?><span class="nextgen-gallery-pro-badge">PRO</span></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Manage your online store with automated tax calculations, coupons, print sell, price list', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="ngg-ecom-management-desc" style="display: none"><?php echo esc_html( $installs_text ); ?></div>
					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" data-name="<?php esc_attr_e( 'E-commerce Management', 'nextgen-gallery' ); ?>" name="ngg-ecom-management" value="ngg-ecom-management" id="ngg-ecom-management" class="feature" <?php echo esc_attr( $checked ); ?>/>
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
		$is_installed = $onboarding->is_recommended_plugin_installed( 'ngg-safe-checkout' );
		$checked      = $is_installed ? 'checked="checked"' : '';
		$installs_text = ! $is_installed ? esc_html__( 'Installs NextGEN Gallery Pro', 'nextgen-gallery' ) : esc_html__( 'NextGEN Pro is already installed', 'nextgen-gallery' );
		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="ngg-safe-checkout"><?php esc_html_e( 'Secure Checkout', 'nextgen-gallery' ); ?><span class="nextgen-gallery-pro-badge">PRO</span></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Grow your online business with multiple payment system provider and safe checkout', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="ngg-safe-checkout-desc" style="display: none"><?php echo esc_html( $installs_text ); ?></div>
					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" data-name="" name="ngg-safe-checkout" value="ngg-safe-checkout" id="ngg-safe-checkout" class="feature <?php echo esc_attr( $is_installed ); ?>" <?php echo esc_attr( $checked ); ?>/>
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
		$is_installed = $onboarding->is_recommended_plugin_installed( 'ngg-image-proofing' );
		$checked      = $is_installed ? 'checked="checked"' : '';
		$installs_text = ! $is_installed ? esc_html__( 'Installs NextGEN Gallery Pro', 'nextgen-gallery' ) : esc_html__( 'NextGEN Pro is already installed', 'nextgen-gallery' );
		?>
		<div class="feature-grid small-padding medium-margin">
			<div class="nextgen-gallery-row">
				<div class="nextgen-gallery-col col-xs-11 text-xs-left">
					<div class="settings-name">
						<div class="name small-margin">
							<label for="ngg-image-proofing"><?php esc_html_e( 'Image Proofing', 'nextgen-gallery' ); ?><span class="nextgen-gallery-pro-badge">PRO</span></label>
						</div>
						<div class="nextgen-gallery-description-text"><?php esc_html_e( 'Enhance the speed of image selection and streamline sharing with the client', 'nextgen-gallery' ); ?></div>
						<div class="nextgen-desc" id="ngg-image-proofing-desc" style="display: none"><?php echo esc_html( $installs_text ); ?></div>
					</div>
				</div>

				<div class="nextgen-gallery-col col-xs-1 text-xs-left">
					<label class="nextgen-gallery-checkbox round <?php echo esc_attr( $is_installed ); ?>">
				<span class="form-checkbox-wrapper">
					<span class="form-checkbox">
						<input type="checkbox" data-name="<?php esc_attr_e( 'Image Proofing', 'nextgen-gallery' ); ?>" name="ngg-image-proofing" value="ngg-image-proofing" id="ngg-image-proofing" class="feature" <?php echo esc_attr( $checked ); ?> />
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
		<div class="go-back"><a href="#general" data-prev="0" class="nextgen-gallery-onboarding-wizard-back-btn nextgen-gallery-onboarding-btn-prev" id="" >←&nbsp;<?php esc_html_e( 'Go back', 'nextgen-gallery' ); ?></a></div>
		<div class="spacer"></div><button type="submit" data-next="2" class="btn nextgen-gallery-onboarding-wizard-primary-btn nextgen-gallery-onboarding-btn-next "  id="nextgen-gallery-save-features" ><?php esc_html_e( 'Save and Continue', 'nextgen-gallery' ); ?>&nbsp; →</button>
	</div>
</div>
