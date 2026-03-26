<div class="sb-onboarding-wizard-step-wrapper sb-onboarding-wizard-step-success sb-fs">
	<div class="sb-onboarding-wizard-step-top sb-fs" data-large="true">
		<h4 v-html="onboardingWizardStepContent['success-page'].heading"></h4>
		<span v-html="onboardingWizardStepContent['success-page'].description"></span>
	</div>

	<div class="sb-onboarding-wizard-success-list sb-fs" :data-done="onboardingWizardDone">

		<div class="sb-onboarding-wizard-succes-elem sb-fs"
			 v-for="(singleMessage , sId) in onboardingSuccessMessagesDisplay"
			 :style="'transition-delay:' + (parseInt(sId) * .5) + 's;'">
			<span class="sb-onboarding-wizard-succes-icon">
				<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
					<path d="M18.3346 9.23355V10.0002C18.3336 11.7972 17.7517 13.5458 16.6757 14.9851C15.5998 16.4244 14.0874 17.4773 12.3641 17.9868C10.6408 18.4963 8.79902 18.4351 7.11336 17.8124C5.4277 17.1896 3.98851 16.0386 3.01044 14.5311C2.03236 13.0236 1.56779 11.2403 1.68603 9.44714C1.80427 7.65402 2.49897 5.94715 3.66654 4.58111C4.8341 3.21506 6.41196 2.26303 8.16479 1.867C9.91763 1.47097 11.7515 1.65216 13.393 2.38355"
						  stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
					<path d="M18.3333 3.33301L10 11.6747L7.5 9.17467" stroke-width="2" stroke-linecap="round"
						  stroke-linejoin="round"/>
				</svg>

			</span>
			<span class="sb-onboarding-wizard-succes-text sb-fs" v-html="singleMessage"></span>
		</div>

	</div>
</div>

<div class="sb-onboarding-wizard-upgrade-ctn sb-fs">
	<div class="sb-onboarding-wizard-upgrade-text sb-fs">
		<h3 v-html="onboardingWizardStepContent['success-page'].upgradeContent.heading"></h3>
		<p v-html="onboardingWizardStepContent['success-page'].upgradeContent.description"></p>
		<div class="sb-onboarding-wizard-upgrade-btns">
			<a class="sb-btn sbi-btn-white sb-btn-wizard-next"
			   v-html="onboardingWizardStepContent['success-page'].upgradeContent.button.text"
			   :href="onboardingWizardStepContent['success-page'].upgradeContent.button.link" target="_blank"
			   rel="noopener"></a>
			<div class="sb-onboarding-wizard-upgrade-off">
				<div>
					<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<mask id="mask0_4219_56670" style="mask-type:alpha" maskUnits="userSpaceOnUse" x="0" y="0"
							  width="24" height="24">
							<rect width="24" height="24" fill="#D9D9D9"/>
						</mask>
						<g mask="url(#mask0_4219_56670)">
							<path d="M14.25 21.4C13.8667 21.7833 13.3917 21.975 12.825 21.975C12.2583 21.975 11.7833 21.7833 11.4 21.4L2.6 12.6C2.41667 12.4167 2.27083 12.2 2.1625 11.95C2.05417 11.7 2 11.4333 2 11.15V4C2 3.45 2.19583 2.97917 2.5875 2.5875C2.97917 2.19583 3.45 2 4 2H11.15C11.4333 2 11.7 2.05417 11.95 2.1625C12.2 2.27083 12.4167 2.41667 12.6 2.6L21.4 11.425C21.7833 11.8083 21.975 12.2792 21.975 12.8375C21.975 13.3958 21.7833 13.8667 21.4 14.25L14.25 21.4ZM12.825 20L19.975 12.85L11.15 4H4V11.15L12.825 20ZM6.5 8C6.91667 8 7.27083 7.85417 7.5625 7.5625C7.85417 7.27083 8 6.91667 8 6.5C8 6.08333 7.85417 5.72917 7.5625 5.4375C7.27083 5.14583 6.91667 5 6.5 5C6.08333 5 5.72917 5.14583 5.4375 5.4375C5.14583 5.72917 5 6.08333 5 6.5C5 6.91667 5.14583 7.27083 5.4375 7.5625C5.72917 7.85417 6.08333 8 6.5 8Z"
								  fill="#1C1B1F"/>
						</g>
					</svg>
				</div>
				<span v-html="onboardingWizardStepContent['success-page'].upgradeContent.upgradeCouppon"></span>
			</div>
		</div>
	</div>
	<div class="sb-onboarding-wizard-upgrade-banner sb-fs">
		<img :src="onboardingWizardStepContent['success-page'].upgradeContent.banner" alt="Upgrade Content Banner"/>
	</div>
</div>

<div class="sb-onboarding-wizard-license-ctn sb-fs">
	<strong><?php echo __('Already have a license key?') ?></strong>
	<p><?php echo __('Upgrade in a single click by adding your license key below') ?></p>
	<div class="sb-onboarding-wizard-license-inp-ctn">
		<input type="text" placeholder="<?php echo __('Paste license key here') ?>" v-model="setupLicencekey"/>
		<button class="sb-btn sb-btn-grey" @click.prevent.default="runOneClickUpgrade">
			<svg v-if="licenseLoading" version="1.1" id="loader-1" xmlns="http://www.w3.org/2000/svg"
				 x="0px" y="0px" width="20px" height="20px"
				 viewBox="0 0 50 50" style="enable-background:new 0 0 50 50;" xml:space="preserve"><path fill="#fff"
																										 d="M43.935,25.145c0-10.318-8.364-18.683-18.683-18.683c-10.318,0-18.683,8.365-18.683,18.683h4.068c0-8.071,6.543-14.615,14.615-14.615c8.072,0,14.615,6.543,14.615,14.615H43.935z">
					<animateTransform attributeType="xml" attributeName="transform" type="rotate" from="0 25 25"
									  to="360 25 25" dur="0.6s" repeatCount="indefinite"/>
				</path></svg>
			<svg v-if="!licenseLoading" width="16" height="16" viewBox="0 0 16 16" fill="none"
				 xmlns="http://www.w3.org/2000/svg">
				<path fill-rule="evenodd" clip-rule="evenodd"
					  d="M6.46447 9.88868L12.8284 3.52472L14.2426 4.93893L6.46447 12.7171L2.22183 8.47446L3.63604 7.06025L6.46447 9.88868Z"
					  fill="#9295A6"/>
			</svg>
			<?php echo __('Activate') ?>
		</button>
	</div>
	<span class="sb-onboarding-wizard-license-error" v-if="setupLicencekeyError !== null"
		  v-html="setupLicencekeyError"></span>
</div>

<div class="sb-onboarding-wizard-finish-ctn sb-fs">
	<button class="sb-btn sb-btn-grey" @click.prevent.default="dismissOnboardingWizard">
		<?php echo __('Complete Setup Without Upgrading') ?>
		<svg width="6" height="9" viewBox="0 0 6 9" fill="none" xmlns="http://www.w3.org/2000/svg">
			<path d="M1.66656 0.5L0.726562 1.44L3.7799 4.5L0.726562 7.56L1.66656 8.5L5.66656 4.5L1.66656 0.5Z"
				  fill="#141B38"/>
		</svg>
	</button>
</div>



