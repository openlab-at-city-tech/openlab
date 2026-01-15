<template>
<div>
	<split-layout :loading="loading">
		<template slot="header">{{ __('Slideshow Defaults', 'ml-slider') }}</template>
		<template slot="description">{{ __('Update the default settings used when creating new slideshows.', 'ml-slider') }}</template>
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
				<template slot="header">{{ __('Default Base Image Width', 'ml-slider') }}</template>
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
				<template slot="header">{{ __('Default Base Image Height', 'ml-slider') }}</template>
				<template slot="description">{{ __('Update the default height for the base image. This will be used for the base image cropping and slideshow dimensions. If set to 100% width, the height will scale accordingly.', 'ml-slider') }}</template>
				<template slot="input-label">
					{{ __('Change the default width', 'ml-slider') }}
				</template>
			</text-single-input>
			<switch-single-input v-model="slideshowDefaults.fullWidth" @change="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('100% Width', 'ml-slider') }}</template>
				<template slot="description">{{ __('While the width and height defined above will be used for cropping (if enabled) and the base slideshow dimensions, you may also set the slideshow to stretch to its container.', 'ml-slider') }}</template>
			</switch-single-input>
			<select-field-input 
				v-model="slideshowDefaults.navigation" 
				:options="navigationOptions" 
				@click="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('Default Navigation', 'ml-slider') }}</template>
				<template slot="description">{{ __('Change the default navigation when creating a new slideshow.', 'ml-slider') }}</template>
			</select-field-input>
			<switch-single-input v-model="slideshowDefaults.autoPlay" @change="saveSlideshowDefaultSettings()">
				<template slot="header">{{ __('Auto Play', 'ml-slider') }}</template>
				<template slot="description">{{ __('Change the default transition between slides.', 'ml-slider') }}</template>
			</switch-single-input>
		</template>
	</split-layout>
    <split-layout :loading="loading" class="lg:mt-6">
		<template slot="header">{{ __('Global Settings', 'ml-slider') }}</template>
		<template slot="description">{{ __('Update the settings used for all the slideshows on your site.', 'ml-slider') }}</template>
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
			<switch-single-input v-model="globalSettings.mobileSettings" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable Device Settings', 'ml-slider') }}</template>
				<template slot="description">{{ __('Add option to hide slides and captions per screen size.', 'ml-slider') }}</template>
			</switch-single-input>
			<template v-if="globalSettings.mobileSettings">
				<div id="mobile-settings" class="bg-white shadow mb-4 relative px-4 py-5 md:p-6">
					<h3 class="text-lg leading-6 m-0 font-medium text-gray-darkest">
						{{ __('Device Settings', 'ml-slider') }}
					</h3>
					<div class="mt-2 max-w-xl text-sm leading-5 text-gray-dark">
						<div class="m-0 pt-0">
							{{ __('Set breakpoints for different screen sizes', 'ml-slider') }}
						</div>
					</div>
					<div class="row">
						<div class="col">
							<div class="mobile-icon-wrap text-gray-dark">
								<span class="dashicons dashicons-smartphone"></span>
								<span class="ms-icon-name">{{ __('Smartphone', 'ml-slider') }}</span>
							</div>
							<text-multiple-input v-model="slideshowDefaults.smartphone" name="default-slideshow-smartphone" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<div class="mobile-icon-wrap text-gray-dark">
								<span class="dashicons dashicons-tablet"></span>
								<span class="ms-icon-name">{{ __('Tablet', 'ml-slider') }}</span>
							</div>
							<text-multiple-input v-model="slideshowDefaults.tablet" name="default-slideshow-tablet" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<div class="mobile-icon-wrap text-gray-dark">
								<span class="dashicons dashicons-laptop"></span>
								<span class="ms-icon-name">{{ __('Laptop', 'ml-slider') }}</span>
							</div>
							<text-multiple-input v-model="slideshowDefaults.laptop" name="default-slideshow-laptop" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
						<div class="col">
							<div class="mobile-icon-wrap text-gray-dark">
								<span class="dashicons dashicons-desktop"></span>
								<span class="ms-icon-name">{{ __('Desktop', 'ml-slider') }}</span>
							</div>
							<text-multiple-input v-model="slideshowDefaults.desktop" name="default-slideshow-desktop" @click="saveSlideshowDefaultSettings()">
							</text-multiple-input>
						</div>
					</div>
				</div>
			</template>
			<switch-single-input v-model="globalSettings.legacy" @change="saveGlobalSettings()" v-bind:class="{ 'disableSwitch': legacySlideshows !== 0}">
				<template slot="header">{{ __('Disable Legacy Libraries', 'ml-slider') }}</template>
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
			<switch-single-input v-model="globalSettings.legacyWidget" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Disable Legacy Widget', 'ml-slider') }}</template>
				<template slot="description">{{ __('This setting allows you to disable the legacy MetaSlider widget.', 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.tinyMce" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Enable TinyMCE', 'ml-slider') }}</template>
				<template slot="description">{{ __('TinyMCE is a WYSIWYG editor you can use in slide captions.', 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.fixTouchSwipe" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Fix Touch Swipe', 'ml-slider') }}</template>
				<template slot="description">{{ __("If Touch Swipe doesn't work as expected, enable this setting.", 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="globalSettings.autoThemeConfig" @change="saveGlobalSettings()">
				<template slot="header">{{ __('Recommended Theme Options', 'ml-slider') }}</template>
				<template slot="description">{{ __('Automatically apply recommended slideshow options when selecting a theme. This will replace some of the previous options.', 'ml-slider') }}</template>
			</switch-single-input>
			<template>
				<div id="dashboard-settings" class="bg-white shadow mb-4 relative px-4 py-5 md:p-6">
					<h3 class="text-lg leading-6 m-0 font-medium text-gray-darkest">
						{{ __('Dashboard Settings', 'ml-slider') }}
					</h3>
					<div class="mt-2 max-w-xl text-sm leading-5 text-gray-dark">
						<div class="m-0 pt-0">
							{{ __('Set default sorting options and items per page for your Dashboard.', 'ml-slider') }}
						</div>
					</div>
					<div class="row">
						<div class="col">
							<select-field-input 
								v-model="globalSettings.dashboardSort" 
								:options="[
									{ value: 'ID', label: __('Slideshow ID', 'ml-slider') },
									{ value: 'post_title', label: __('Title', 'ml-slider') },
									{ value: 'post_date', label: __('Date Created', 'ml-slider') }
								]" 
								@click="saveGlobalSettings()">
								<template slot="header">{{ __('Sort Slideshows By', 'ml-slider') }}</template>
							</select-field-input>
						</div>
						<div class="col">
							<select-field-input 
								v-model="globalSettings.dashboardOrder" 
								:options="[
									{ value: 'asc', label: __('Ascending', 'ml-slider') },
									{ value: 'desc', label: __('Descending', 'ml-slider') }
								]" 
								@click="saveGlobalSettings()">
								<template slot="header">{{ __('Order By', 'ml-slider') }}</template>
							</select-field-input>
						</div>
						<div class="col">
							<text-single-input 
								v-model="globalSettings.dashboardItems" 
								name="dashboard-items" 
								wrapper-class="w-24" 
								@click="saveGlobalSettings()">
								<template slot="header">{{ __('Items Per Page', 'ml-slider') }}</template>
							</text-single-input>
						</div>
					</div>
				</div>
			</template>
		</template>
	</split-layout>
	<!-- Pro Ads -->
	<split-layout :loading="loading" class="lg:mt-6" v-if="!isPro()">
		<template slot="header">{{ __('Pro Settings', 'ml-slider') }}</template>
		<template slot="description">{{ __('Update the MetaSlider Pro settings.', 'ml-slider') }}</template>
		<template slot="fields">
			<switch-single-input-ad :value="false">
				<template slot="header">{{ __('Flush Cache when Saving Changes', 'ml-slider') }}</template>
				<template slot="description">{{ __('This setting allows you to automatically flush cache when saving slideshow changes. Support WP Rocket, WP Super Cache, W3 Total Cache, WP-Optimize and WP Fastest Cache plugins.', 'ml-slider') }}</template>
				<template slot="proText">{{ __('This feature is available in MetaSlider Pro', 'ml-slider') }}</template>
			</switch-single-input-ad>
		</template>
	</split-layout>
	<!-- Pro settings -->
	<split-layout :loading="loading" class="lg:mt-6" v-if="isPro()">
		<template slot="header">{{ __('Pro Settings', 'ml-slider') }}</template>
		<template slot="description">{{ __('Update the MetaSlider Pro settings.', 'ml-slider') }}</template>
		<template slot="fields">
			<text-single-input 
				v-model="proSettings.postFeedFields" 
				name="default-slideshow-width" 
				wrapper-class="w-24" 
				@click="saveProSettings()">
				<template slot="header">{{ __('Maximum Number of Custom Fields in Post Feed and WooCommerce Slides', 'ml-slider') }}</template>
				<template slot="description">{{ __('Select how many custom fields will display in the dropdown menu when you are inserting tags.', 'ml-slider') }}</template>
				<template slot="input-label">
					{{ __('Change the maximum custom fields for Post Feed and WooCommerce', 'ml-slider') }}
				</template>
			</text-single-input>
			<text-single-input 
				v-model="proSettings.postFeedTitleLength" 
				name="default-slideshow-width" 
				wrapper-class="w-24" 
				@click="saveProSettings()">
				<template slot="header">{{ __('Maximum Title Length in Post Feed and WooCommerce Slides', 'ml-slider') }}</template>
				<template slot="description">{{ __('Select the maximum title length for post titles.', 'ml-slider') }}</template>
				<template slot="input-label">
					{{ __('Change the maximum title length for Post Feed and WooCommerce post titles', 'ml-slider') }}
				</template>
			</text-single-input>
			<switch-single-input v-model="proSettings.legacyThemeEditor" @change="saveProSettings()">
				<template slot="header">{{ __('Enable Legacy Theme Editor', 'ml-slider') }}</template>
				<template slot="description">{{ __('This setting allows you to enable the legacy Theme Editor.', 'ml-slider') }}</template>
			</switch-single-input>
			<switch-single-input v-model="proSettings.flushCache" @change="saveProSettings()">
				<template slot="header">{{ __('Flush Cache when Saving Changes', 'ml-slider') }}</template>
				<template slot="description">{{ __('This setting allows you to automatically flush cache when saving slideshow changes. Support WP Rocket, WP Super Cache, W3 Total Cache, WP-Optimize and WP Fastest Cache plugins.', 'ml-slider') }}</template>
			</switch-single-input>
		</template>
	</split-layout>
</div>
</template>

<script>
import { default as SplitLayout } from '../layouts/_split'
import { default as TextSingle } from '../inputs/_textSingle'
import { default as TextMultiple } from '../inputs/_textMultiple'
import { default as SwitchSingle } from '../inputs/_switchSingle'
import { default as SwitchSingleAd } from '../inputs/_switchSingleAd'
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
		'switch-single-input-ad' : SwitchSingleAd,
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
				navigation: true,
				autoPlay: true,
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
				adminBar: true,
				editLink: false,
				legacy: true,
				newSlideOrder: 'last',
				mobileSettings: true,
				legacyWidget: true,
				tinyMce: true,
				fixTouchSwipe: false,
				autoThemeConfig: true,
				dashboardSort: 'ID',
				dashboardOrder: 'asc',
				dashboardItems: 10

			},
			proSettings: {
				postFeedFields: 100,
				legacyThemeEditor: false, // false means legacy Theme editor is disabled
				postFeedTitleLength: 100,
				flushCache: false
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
		},
		navigationOptions() {
			const baseOptions = [
				{ value: false, label: this.__('Hidden', 'ml-slider') },
				{ value: true, label: this.__('Dots', 'ml-slider') },
				{ value: 'dots_onhover', label: this.__('Dots - Visible On Hover', 'ml-slider') }
			];

			return [
				...baseOptions,
				{ 
					value: 'thumbs', 
					label: this.isPro() ? this.__('Thumbnail', 'ml-slider') : this.__('Thumbnail (Pro)', 'ml-slider'), 
					disabled: !this.isPro() 
				},
				{ 
					value: 'thumbs_onhover', 
					label: this.isPro() ? this.__('Thumbnails - Visible On Hover', 'ml-slider') : this.__('Thumbnails - Visible On Hover (Pro)', 'ml-slider'), 
					disabled: !this.isPro() 
				},
				{ 
					value: 'filmstrip', 
					label: this.isPro() ? this.__('Filmstrip', 'ml-slider') : this.__('Filmstrip (Pro)', 'ml-slider'),  
					disabled: !this.isPro() 
				},
				{ 
					value: 'filmstrip_onhover', 
					label: this.isPro() ? this.__('Filmstrip - Visible On Hover', 'ml-slider') : this.__('Filmstrip - Visible On Hover (Pro)', 'ml-slider'),  
					disabled: !this.isPro() 
				}
			];
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
		Settings.getProSettings().then(({data}) => {
			Object.keys(data.data).forEach(key => {
				if (this.proSettings.hasOwnProperty(key)) {
					this.proSettings[key] = data.data[key]
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
		},
		// @TODO - Maybe move this to metaslider-pro/v1 ?
		saveProSettings() {
			const settings = JSON.stringify(this.proSettings)
			Settings.saveProSettings(settings).then(({data}) => {
				this.notifyInfo(
					'metaslider/settings-page-slideshow-settings-saved',
					this.__('Pro settings saved', 'ml-slider'),
					true
				)
			}).catch(error => {
				this.notifyError('metaslider/settings-save-error', error.response, true)
			})
		},
		isPro() {
			return metaslider_api.proUser !== 'undefined' && Number(metaslider_api.proUser) === 1;
		},
	},
	watch: {
		legacySlideshows(newVal) {
			if (newVal !== 0) {
				this.globalSettings.legacy = false;
			}
		}
	}
}
</script>
