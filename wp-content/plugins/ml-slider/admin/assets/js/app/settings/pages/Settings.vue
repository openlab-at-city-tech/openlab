<template>
<div>
	<split-layout :loading="loading">
		<template slot="header">{{ __('Slideshow Defaults', 'ml-slider') }}</template>
		<template slot="description">{{ __('Update default settings used when creating a new slideshow.', 'ml-slider') }}</template>
		<template slot="fields">
			<text-single-input v-model="slideshowDefaults.title" name="default-slideshow-title" @click="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('Default Slideshow Title', 'ml-slider') }}</template>
				<template slot="description"><span v-html="defaultTitleDescription"/></template>
				<template slot="input-label">
					{{ __('Change the default title', 'ml-slider') }}
				</template>
			</text-single-input>
			<text-single-input 
				v-model="slideshowDefaults.width" 
				name="default-slideshow-width" 
				wrapper-class="w-24" 
				@click="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('Base Image Width', 'ml-slider') }}</template>
				<template slot="description">{{ __('Update the default width for the base image. This will be used for the slideshow dimensions and base image cropping.', 'ml-slider') }}</template>
				<template slot="input-label">
					{{ __('Change the default width', 'ml-slider') }}
				</template>
			</text-single-input>
			<text-single-input 
				v-model="slideshowDefaults.height" 
				name="default-slideshow-height" 
				wrapper-class="w-24" 
				@click="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('Base Image Height', 'ml-slider') }}</template>
				<template slot="description">{{ __('Update the default height for the base image. This will be used for the base image cropping and slideshow dimensions. If set to 100% width, the height will scale accordingly.', 'ml-slider') }}</template>
				<template slot="input-label">
					{{ __('Change the default width', 'ml-slider') }}
				</template>
			</text-single-input>
			<switch-single-input v-model="slideshowDefaults.fullWidth" @change="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('100% Width', 'ml-slider') }}</template>
				<template slot="description">{{ __('While the width and height defined above will be used for cropping (if enabled) and the base slideshow dimensions, you may also set the slideshow to stretch to its container.', 'ml-slider') }}</template>
			</switch-single-input>
		</template>
	</split-layout>
    <!-- Hey, for now this is hiden for pro users because we haven't integrated a license system yet! -->
    <split-layout :loading="loading" class="lg:mt-6">
		<template slot="header">{{ __('Global Settings', 'ml-slider') }}</template>
		<template slot="description">{{ __('Here you will find general account settings and options related to your account', 'ml-slider') }}</template>
		<template slot="fields">
			<text-single-input v-model="globalSettings.license" name="ms-license" class="hidden" @click="saveGlobalSettings()">
				<template slot="header">{{ __('License Key', 'ml-slider') }}</template>
				<template slot="description"><span v-html="licenseDescription"/></template>
				<template slot="input-label">
					{{ __('Update license key', 'ml-slider') }}
				</template>
			</text-single-input>
			<switch-single-input v-model="globalSettings.optIn" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Help Improve MetaSlider', 'ml-slider') }}</template>
				<template slot="description">
                    <span v-html="optInDescription"/>
                    <small v-if="Object.prototype.hasOwnProperty.call(optinInfo, 'id')" class="italic">Activated by user id #{{ optinInfo.id }} ({{ optinInfo.email }}) on {{ new Date(optinInfo.time * 1000).toLocaleDateString() }}</small>
                </template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.gallery" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable Gallery (Beta)', 'ml-slider') }}</template>
				<template slot="description">{{ __('Fast, SEO-focused, fully WCAG accessible and easy to use galleries.', 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.mobileSettings" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable Mobile Settings (Beta)', 'ml-slider') }}</template>
				<template slot="description">{{ __('Add option to hide slides and captions per screen size.', 'ml-slider') }}</template>
			</switch-single-input>
			<template v-if="globalSettings.mobileSettings">
				<div id="mobile-settings" class="bg-white shadow mb-4 relative px-4 py-5 md:p-6">
					<h3 class="text-lg leading-6 m-0 font-medium text-gray-darkest">
						{{ __('Mobile Settings', 'ml-slider') }}
					</h3>
					<div class="mt-2 max-w-xl text-sm leading-5 text-gray-dark">
						<div class="m-0 pt-0">
							{{ __('Set breakpoints for different screen sizes', 'ml-slider') }}
						</div>
					</div>
					<div class="row">
						<div class="col">
							<span class="dashicons dashicons-smartphone"></span>
							<text-multiple-input v-model="slideshowDefaults.smartphone" name="default-slideshow-smartphone" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<span class="dashicons dashicons-tablet"></span>
							<text-multiple-input v-model="slideshowDefaults.tablet" name="default-slideshow-tablet" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<span class="dashicons dashicons-laptop"></span>
							<text-multiple-input v-model="slideshowDefaults.laptop" name="default-slideshow-laptop" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<span class="dashicons dashicons-desktop"></span>
							<text-multiple-input v-model="slideshowDefaults.desktop" name="default-slideshow-desktop" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
					</div>
				</div>
			</template>
			<switch-single-input v-model="globalSettings.legacy" @change="saveGlobalSettings()" v-bind:class="{ 'disableSwitch': legacySlideshows !== 0}">
				<template slot="header">{{ __('Disable Legacy Libraries (Beta)', 'ml-slider') }}</template>
				<template slot="description">{{ __('This setting allows you to disable the legacy slideshow libraries: Nivo Slider, Coin Slider, and Responsive Slides', 'ml-slider') }}</template>
				<template slot="legacy-notices" v-if="legacySlideshows === 0">
					<div class="notice notice-success ml-legacy-notice">
                    	<p>{{ __('You can safely enable this setting. None of your slideshows use the legacy libraries.', 'ml-slider') }}</p>
					</div>
                </template>
				<template slot="legacy-notices" v-if="legacySlideshows !== 0">
					<div class="notice notice-warning  ml-legacy-notice">
                    	<p>{{ legacySlideshows > 1 
                    		? sprintf(__('You currently have %s slideshows that use legacy libraries.', 'ml-slider'), legacySlideshows)
                    		: sprintf(__('You currently have %s slideshow that uses legacy libraries.', 'ml-slider'), legacySlideshows) }}</p>
					</div>
                </template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.adminBar" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable MetaSlider on Admin Bar', 'ml-slider') }}</template>
				<template slot="description">{{ __('Add and edit slideshows easier by showing MetaSlider on your admin bar.', 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.editLink" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable Frontend Edit Links', 'ml-slider') }}</template>
				<template slot="description">{{ __('Edit slideshows easily by showing MetaSlider link under each slideshow.', 'ml-slider') }}</template>
			</switch-single-input>
			<select-field-input 
				v-model="globalSettings.newSlideOrder" 
				:options="[
					{ value: 'last', label: __('Last', 'ml-slider') },
					{ value: 'first', label: __('First', 'ml-slider') }
				]" 
				@click="saveGlobalSettings()">
				<template slot="header">{{ __('New slides order', 'ml-slider') }}</template>
				<template slot="description">{{ __('Select the position for new added slides.', 'ml-slider') }}</template>
			</select-field-input>
		</template>
	</split-layout>
</div>
</template>

<script>
import { default as SplitLayout } from '../layouts/_split'
import { default as TextSingle } from '../inputs/_textSingle'
import { default as TextMultiple } from '../inputs/_textMultiple'
import { default as SwitchSingle } from '../inputs/_switchSingle'
import { default as SelectField } from '../inputs/_selectField'
import { default as WarningAlert } from '../inputs/alerts/_warningSmall'
import { Settings } from '../../api'
import { Slideshow } from '../../api'
export default {
	components: {
		'split-layout' : SplitLayout,
		'text-single-input' : TextSingle,
		'text-multiple-input' : TextMultiple,
		'switch-single-input' : SwitchSingle,
		'select-field-input' : SelectField,
		'alert-warning-small': WarningAlert
	},
	props: {},
	data() {
		return {
            loading: true,
            optinInfo: {},
			slideshowDefaults: {
				title: '',
				fullWidth: false,
				width: 0,
				height: 0,
				smartphone: 480,
				tablet: 768,
				laptop: 1024,
				desktop: 1440,
            },
            globalSettings: {
				license: '',
				optIn: false,
				gallery: false,
				adminBar: true,
				editLink: false,
				legacy: true,
				newSlideOrder: 'last',
				mobileSettings: false,
			},
			legacySlideshows: {}

		}
	},
	computed: {
		defaultTitleDescription() {
			return this.sprintf(this.__('Change the default title that will be used when creating a new slideshow. Use %s and it will be replaced by the current slideshow ID.', 'ml-slider'), '<code class="bg-transparent p-0 font-bold">{id}</code>')
        },
        licenseDescription() {
			return this.sprintf(
                this.__('If you are a pro member, enter your license key here to receive updates. %s', 'ml-slider'),
                `<a target="_blank" href="${this.hoplink}">${this.__('Upgrade here', 'ml-slider')}</a>`
            )
		},
		optInDescription() {
			return this.sprintf(
                this.__('Opt-in to let MetaSlider responsibly collect information about how you use our plugin. This is disabled by default, but may have been enabled by via a notification. %s', 'ml-slider'),
                `<a target="_blank" href="${this.privacyLink}">${this.__('View our detailed privacy policy', 'ml-slider')}</a>`
            )
		},
		isDisabled() {
			if(legacySlideshows == 0){

			}
			return this.form.validated;
		}
	},
	created() {
		Settings.getSlideshowDefaults().then(({data}) => {
			Object.keys(data.data).forEach(key => {
				if (this.slideshowDefaults.hasOwnProperty(key)) {
					this.slideshowDefaults[key] = data.data[key]
				}
			})
			this.loading = false
		}).catch(error => {
			this.notifyError('metaslider/settings-load-error', error.response, true)
		})
		Settings.getGlobalSettings().then(({data}) => {
			Object.keys(data.data).forEach(key => {
				if (this.globalSettings.hasOwnProperty(key)) {
					this.globalSettings[key] = data.data[key]
				}
			})
			this.loading = false
		}).catch(error => {
			this.notifyError('metaslider/settings-load-error', error.response, true)
        })
		Settings.get('optin_user_extras').then(({data}) => {
			this.optinInfo = data.data
		})
		Slideshow.legacy().then(({data}) => {
			this.legacySlideshows = data.data
		})
	},
	mounted() {},
	methods: {
		saveSlideshowDefaultSettings() {
			const settings = JSON.stringify(this.slideshowDefaults)
			Settings.saveSlideshowDefaults(settings).then(({data}) => {
				this.notifyInfo(
					'metaslider/settings-page-slideshow-settings-saved',
					this.__('Slideshow settings saved', 'ml-slider'),
					true
				)
			}).catch(error => {
				this.notifyError('metaslider/settings-save-error', error.response, true)
			})
		},
		async saveGlobalSettings() {
            this.optinInfo = {}
            if (this.globalSettings.optIn) {
                await Settings.saveGlobalSettingsSingle('optin_via', 'manual')
            }
			const settings = JSON.stringify(this.globalSettings)
			Settings.saveGlobalSettings(settings).then(({data}) => {
				this.notifyInfo(
					'metaslider/settings-page-global-settings-saved',
					this.__('Global settings saved', 'ml-slider'),
					true
				)
			}).catch(error => {
				this.notifyError('metaslider/settings-save-error', error.response, true)
			})
		}
	}
}
</script>
