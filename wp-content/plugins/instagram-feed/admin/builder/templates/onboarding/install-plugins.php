<div class="sb-onboarding-wizard-step-wrapper sb-onboarding-wizard-step-installp sb-fs">

	<div class="sb-onboarding-wizard-step-top sb-fs" data-large="true">
		<h4 v-html="onboardingWizardStepContent['install-plugins'].heading"></h4>
		<span v-html="onboardingWizardStepContent['install-plugins'].description"></span>
	</div>

	<div class="sb-onboarding-wizard-elements-list sb-fs">

		<div class="sb-onboarding-wizard-elem sb-fs"
			 v-for="plugin in onboardingWizardStepContent['install-plugins']?.pluginsList">
			<div class="sb-onboarding-wizard-elem-info">
				<div class="sb-onboarding-wizard-elem-icon" v-if="plugin?.icon !== undefined">
					<img :src="plugin?.icon" :alt="plugin?.heading"/>
				</div>
				<div class="sb-onboarding-wizard-elem-text">
					<strong v-if="plugin?.heading !== undefined">
						<span v-html="plugin?.heading"></span>
						<span class="sb-onboarding-wizard-elem-text-installs">
							<img :src="onboardingWizardStepContent['install-plugins']?.star_icons">
							<e v-html="plugin?.installs_number"></e>
						</span>
					</strong>
					<span v-if="plugin?.description !== undefined" v-html="plugin?.description"></span>
				</div>

			</div>
			<div class="sb-onboarding-wizard-elem-toggle">
				<div :data-color="plugin?.color" :data-active="switcherOnboardingWizardCheckActive(plugin)"
					 :data-uncheck="plugin?.uncheck"
					 @click.prevent.default="switcherOnboardingWizardClick(plugin)"></div>
			</div>
			<div class="sb-onboarding-wizard-gdpr-info sb-fs" v-if="onboardingWizardStepContent['install-plugins'].showGDPRInfo">
				<h4>{{onboardingWizardStepContent['install-plugins'].gdprInfo.heading}}</h4>
				<div class="sb-onboarding-wizard-gdpr-columns">
					<div class="sb-gdpr-box" v-for="column in onboardingWizardStepContent['install-plugins'].gdprInfo.columns">
						<div class="sb-gdpr-box-icon"><img :src="column.icon"></div>
						<div class="sb-gdpr-box-text">
							<h5>{{column.title}}</h5>
							<p>{{column.description}}</p>
						</div>

					</div>
				</div>
			</div>
		</div>

	</div>



	<div class="sb-onboarding-wizard-clicking">
		<span v-html="svgIcons['info']"></span>
		<span>
			<?php echo __('Clicking Next will install ', 'instagram-feed') ?>
			<span v-for="(plugin, ind) in onboardingWizardStepContent['install-plugins']?.pluginsList"
				  v-html="plugin?.data?.pluginName + (ind !== onboardingWizardStepContent['install-plugins']?.pluginsList.length - 1 ? ', ' : '.')"></span>
		</span>
	</div>

</div>

<div class="sb-onboarding-wizard-step-pag-btns sb-fs">
	<button class="sb-btn sbi-btn-grey sb-btn-wizard-back" v-html="'Back'"
			@click.prevent.default="previousWizardStep"></button>
	<button class="sb-btn sbi-btn-blue sb-btn-wizard-next sb-btn-wizard-install" v-html="'Install Selected Plugins'"
			@click.prevent.default="nextWizardStep('submit')"></button>
</div>