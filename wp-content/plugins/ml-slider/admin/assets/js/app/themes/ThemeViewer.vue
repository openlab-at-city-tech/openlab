<template>

	<!-- This component will work similar to the featured image component -->
	<section
		v-if="current.id"
		:class="{'unsupported': unsupportedSliderType}"
		class="theme-select-module">
		<p v-if="hasThemeSet">
			{{ __('Slideshow Theme', 'ml-slider') }}: 
				<template v-if="'custom' == current.theme.type">
					<span v-if="current.theme.version === 'v2'">
						{{ current.theme.base_title }}
					</span>
					<span v-else>
						{{ current.theme.title }}
					</span>
				</template>
				<template v-else>
					<span>{{ current.theme.title }}</span>
				</template>
		</p>
		<div
			:class="{'ms-modal-open': is_open}"
			class="inside wp-clearfix metaslider-theme-viewer">

			<!-- If the theme is not supported we should show an error -->
			<p
				v-if="(hasThemeSet && unsupportedSliderType)"
				class="slider-not-supported-warning">
				{{ __('This theme was designed for FlexSlider. Please choose the FlexSlider option for the best display.', 'ml-slider') }}
			</p>
			<!-- Notice when "Recommended Theme Options" is disabled -->
			<p v-if="!Number(autoThemeConfig)" class="slider-not-supported-warning" v-html="recommendedThemeOptionsNotice"></p>

			<!-- If there's a theme already set -->
			<div
				v-if="hasThemeSet"
				class="ms-current-theme">
				<button
					style="width:100%;text-decoration:none"
					type="button"
					class="button-link change-theme-img-button"
					@click="openModal">
					<div
						v-if="'custom' == current.theme.type"
						:class="[
							'custom-theme-single p-0',
							{ 'custom-theme-single--legacy': current.theme?.version !== 'v2' }
						]">
						<template v-if="current.theme.version === 'v2'">
							<div class="theme-label-info-v2">
								<div class="custom-subtitle">
									{{ current.theme.base_title + ' ' + __('theme', 'ml-slider') }}
								</div>
								{{ current.theme.title }}
							</div>
							<div class="theme-image-wrapper">
								<img 
									:src="themeDirectoryUrl + current.theme.base + '/screenshot.png'"
									:alt="current.theme.title"
									class="theme-image-v2"> 
							</div>
						</template>
						<template v-else>
							<div class="theme-label-info-legacy">
								{{ __('Legacy', 'ml-slider') }}
							</div>
							<div class="custom-theme-single">
								<div class="custom-subtitle">
									{{ __('Custom theme', 'ml-slider') }}
								</div>
								{{ current.theme.title }}
							</div>
						</template>
					</div>
					<div v-else>
						<img
							v-if="current.theme.screenshot_dir"
							:src="current.theme.screenshot_dir + '/screenshot.png'"
							:alt="current.theme.title">
						<img
							v-else
							:src="themeDirectoryUrl + current.theme.folder + '/screenshot.png'"
							:alt="current.theme.title">
					</div>
				</button>
				<button
					type="button"
					class="button-link remove-theme"
					@click="removeTheme">{{ __('Remove', 'ml-slider') }}
				</button>
				<button
					type="button"
					class="button-link change-theme"
					@click="openModal">{{ __('Change', 'ml-slider') }}
				</button>
				<!-- Customize theme design (optional) -->
				<theme-customize :manifest="theme_customize"></theme-customize>
			</div>

			<!-- If no theme then we render the theme select button -->
			<div v-else>
				<p>
					{{ __('Change the design of your slideshow with a stylish MetaSlider theme!', 'ml-slider') }}
				</p>
				<button
					v-if="Object.keys(themes).length || Object.keys(customThemes).length"
					type="button"
					class="button"
					@click="openModal">{{ __('Select a custom theme', 'ml-slider') }}
				</button>
			</div>

			<!-- This will be a modal for showing the themes -->
			<sweet-modal
				ref="themesModal"
				:hide-close-button="true"
				:blocking="true"
				:pulse-on-block="false"
				overlay-theme="dark"
				@close="is_open = false">
				<button
					slot="box-action"
					@click.prevent="$refs.themesModal.close()">
                    <svg class="w-6 -mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
				</button>
				<sweet-modal-tab
					id="all"
					:title="__('All themes', 'ml-slider')">
					<div v-if="loading || loadingCustom">
						{{ __('Loading...', 'ml-slider') }}
					</div>
					<template v-if="(themes && Object.keys(themes).length) || (Object.keys(customThemes).length && proUser)">
						<div class="columns">
							<div class="theme-list-column">
								<!-- Notice when "Recommended Theme Options" is disabled -->
								<div v-if="!Number(autoThemeConfig)" class="slider-not-supported-warning" style="margin: 0 !important" v-html="recommendedThemeOptionsNotice"></div>
								<ul class="ms-image-selector regular-themes">
									<li
										v-if="themes && Object.keys(themes).length"
										v-for="theme in themes"
										:key="theme.folder"
										:class="{ 
											'a-theme': true, selected: (selectedTheme.folder === theme.folder), 
											'unlock-pro-theme-ad': nonSelectablePremiumTheme(theme.type)
										}"
										role="checkbox"
										@mouseover="hoveredTheme = theme; showPremiumThemeAd(theme.type, theme.folder)"
										@mouseout="hoveredTheme = selectedTheme"
										@mouseleave="hidePremiumThemeAd(theme.type)"
										@click="nonSelectablePremiumTheme(theme.type) ? null : selectTheme(theme)">
										<span>
											<div 
												v-if="revealThemeAd === theme.folder"
												class="custom-theme-single upgrade-pro-theme-ad">
												<h3 class="text-white mb-3">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h3>
												<p class="text-white font-normal text-sm mb-3">
													{{ __('Upgrade now to unlock this theme!', 'ml-slider') }}
												</p>
												<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="hoplink" target="_blank">{{ __('Upgrade now', 'ml-slider') }} <span class="dashicons dashicons-external border-0"></span></a>
											</div>
											<img
												v-if="theme.screenshot_dir"
												:src="theme.screenshot_dir + '/screenshot.png'"
												:alt="theme.title">
											<img
												v-else
												:src="themeDirectoryUrl + theme.folder + '/screenshot.png'"
												:alt="theme.title">
										</span>
									</li>

									<template v-if="Object.keys(customThemes).length && proUser">
										<li
											v-for="theme in customThemes"
											:key="theme.folder"
											:class="{ 'a-theme': true, selected: (selectedTheme.folder == theme.folder) }"
											role="checkbox"
											@mouseover="hoveredTheme = theme"
											@mouseout="hoveredTheme = selectedTheme"
											@click="selectTheme(theme)">
											<span>
												<template v-if="theme.version === 'v2'">
													<div class="theme-label-info-v2">
														<div class="custom-subtitle">
															{{ theme.base_title + ' ' + __('theme', 'ml-slider') }}
														</div>
														{{ theme.title }}
													</div>
													<div class="theme-image-wrapper">
														<img 
															:src="themeDirectoryUrl + theme.base + '/screenshot.png'"
															:alt="theme.title"
															class="theme-image-v2"> 
													</div>
												</template>
												<template v-else>
													<div class="theme-label-info-legacy">
														{{ __('Legacy', 'ml-slider') }}
													</div>
													<div class="custom-theme-single">
														{{ theme.title }}
													</div>
												</template>
											</span>
										</li>
									</template>
									<template v-else-if="!Object.keys(customThemes).length && proUser && !loadingCustom">
										<li class="a-theme">
											<span>
												<div class="custom-theme-single upgrade-pro-theme-ad">
													<h3 class="text-white mb-3">{{ __('MetaSlider Pro is installed!', 'ml-slider') }}</h3>
													<p class="text-white font-normal text-sm mb-3">
														{{ __('You can create your own themes with our theme editor', 'ml-slider') }}
													</p>
													<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="themeEditorLink">{{ __('Get started', 'ml-slider') }}</a>
												</div>
											</span>
										</li>
									</template>
									<template v-else>
										<li class="a-theme unlock-pro-custom-themes-ad">
											<span>
												<div class="custom-theme-single upgrade-pro-theme-ad custom-theme-editor">
													<h3 class="text-white mb-3">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h3>
													<p class="text-white font-normal text-sm mb-3">
														{{ __('Upgrade now to build your own custom themes!', 'ml-slider') }}
													</p>
													<a class="w-full inline-flex items-center justify-center px-5 py-2 border border-transparent rounded-md text-white bg-orange hover:bg-orange-darker active:bg-orange-darkest transition ease-in-out duration-150 md:w-auto text-base" :href="hoplink" target="_blank">{{ __('Upgrade now', 'ml-slider') }} <span class="dashicons dashicons-external border-0"></span></a>
												</div>
											</span>
										</li>
									</template>
								</ul>
							</div>
							<div class="theme-details-column">
								<template v-if="showThemeDetails && hoveredTheme.type !== 'custom'">
									<div>
										<h1
											slot="button"
											class="metaslider-theme-title"
											v-text="hoveredTheme.title"/>
										<template v-if="hoveredTheme.description">
											<div class="ms-theme-description">
												<h2>{{ __('Theme Details', 'ml-slider') }}</h2>
												<p v-html="hoveredTheme.description"/>
											</div>
										</template>
										<template v-if="hoveredTheme.instructions">
											<div class="ms-theme-instructions">
												<h2>{{ __('Theme Instructions', 'ml-slider') }}</h2>
												<p v-html="hoveredTheme.instructions"/>
											</div>
										</template>
									</div>
									<div v-if="hoveredTheme && hoveredTheme.tags && hoveredTheme.tags.length">
										<h3>{{ __('Tags', 'ml-slider') }}</h3>
										<ul class="ms-theme-tags">
											<li
												v-for="(tag, i) in hoveredTheme.tags"
												:key="i"
												v-text="tag"/>
										</ul>
									</div>
								</template>
								<template v-else-if="hoveredTheme.type === 'custom'">
									<div>
										<h1 class="metaslider-theme-title">
											 <template v-if="hoveredTheme.version === 'v2'">
												{{ hoveredTheme.base_title }}
											 </template>
											<template v-else>
												{{ hoveredTheme.title }}
											</template>
										</h1>
										<div class="ms-theme-description">
											<template v-if="hoveredTheme.version === 'v2'">
												<h2>{{ __('Style Details', 'ml-slider') }}</h2>
												<p>{{ __('This style was created with the Theme Editor.', 'ml-slider') }}</p>
											</template>
											<template v-else>
												<h2>{{ __('Theme Details', 'ml-slider') }}</h2>
												<p>{{ __('This theme was created through the theme editor.', 'ml-slider') }}</p>
											</template>
										</div>
									</div>
								</template>
								<template v-else>
									<template v-if="proUser">
										<div>
											<div>
												<h1 class="metaslider-theme-title">{{ __('How To Use', 'ml-slider') }}</h1>
												<p>{{ __('Select a theme on the left to use on this slideshow. Click the theme for more details.', 'ml-slider') }}</p>
											</div>
										</div>
									</template>
									<template v-else>
										<div>
											<h1 class="metaslider-theme-title">{{ __('Get MetaSlider Pro!', 'ml-slider') }}</h1>
											<p>{{ __('MetaSlider Pro gives you access to extra themes. You can also create completely new themes that can easily be added to new slideshows.', 'ml-slider') }}</p>
										</div>
									</template>
								</template>
							</div>
						</div>
					</template>
					<template v-else>
						<div class="free-themes-not-found">
							<h1>{{ __('Error: No themes were found.', 'ml-slider') }}</h1>
						</div>
					</template>
				</sweet-modal-tab>
				<template
					slot="button">
					<div>
						<span
							v-if="sliderTypeNotSupported"
							class="slider-not-supported-warning">
							{{ __('This theme was designed for FlexSlider. Please choose the FlexSlider option for the best display.', 'ml-slider') }}</span>
					</div>
					<div class="flex items-center">
						<button
							:title="__('Preview slideshow', 'ml-slider')"
							class="flex items-center m-0 mr-1 text-gray-darker"
							@click.prevent="openPreview">
                            <svg class="w-6 inline mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            {{ __('Preview', 'ml-slider') }}
						</button>
						<button
							:disabled="!selectedTheme.folder"
							class="button button-primary"
							@click.stop.prevent="setTheme">{{ __('Select', 'ml-slider') }}
						</button>
					</div>
				</template>
			</sweet-modal>
		</div>
	</section>
</template>

<script>
import { EventManager } from '../utils'
import { Axios } from '../api'
import './components'
import { mapGetters } from 'vuex'
import QS from 'qs'
import { default as ThemeCustomize } from './includes/ThemeCustomize'

export default {
	components: {
		'theme-customize' : ThemeCustomize
	},
	props: {
		themeDirectoryUrl: {
			type: [String],
			default: ''
		}
	},
	data() {
		return {
			loading: true,
			loadingCustom: true,
			unsupportedSliderType: false,
			themes: {},
			customThemes: {},
			selectedTheme: {},
			hoveredTheme: {},
			is_open: false,
			revealThemeAd: null,
			theme_customize: [], // @TODO Maybe declare as {} ?
			theme_edit_settings: {}
		}
	},
	watch: {
		currentThemeSupports() {
			// TODO: Settings - once reactive, refactor this
			this.updateSupportedStatus()
		},
		current: {
			immediate: true,
			handler: function(current) {
				// hoveredTheme controls what shows in the sidebar
				if (!this.current || !this.current.theme || 'none' === this.current.theme) {
					this.selectedTheme = this.hoveredTheme = this.themeStub()
					return
				}
				this.selectedTheme = this.current.theme
				this.hoveredTheme = this.current.theme

				// Get the theme customizations if available from db to avoid sync issues
				Axios.get('theme/customization', {
					params: {
						action: 'ms_get_theme_customization',
						slideshow_id: this.current.id,
						theme: this.selectedTheme['folder'], // Just the folder!
						type: this.selectedTheme['type'] // free, premium, etc.
					}
				}).then(response => {
					var customize_data = response.data.data;
					
					// Assign defaults from manifest, including default values
					this.theme_customize = customize_data.manifest || [];

					// Iterate each section that has 'status' key
					this.theme_customize.forEach((section_item, section_index) => {

						// Loop each section 'settings' key
						section_item.settings.forEach((row_item, row_index) => {
							if (row_item.type === 'fields' && typeof row_item.fields !== 'undefined') {
								// Replace default values with the saved ones
								for (let i = 0; i < row_item.fields.length; i++) {
									// The 'name' in theme_customize should match keys in saved_settings
									const name = row_item.fields[i].name; 
									const manifest_fields = this.theme_customize[section_index].settings[row_index].fields[i];

									// Check if saved_settings contains the key matching 'name'
									if (customize_data.saved_settings 
										&& customize_data.saved_settings.hasOwnProperty(name)
										&& typeof customize_data.saved_settings[name] !== 'undefined') {
											manifest_fields.value = customize_data.saved_settings[name];
									} else {
										// Use default value if saved setting for this name doesn't exist
										manifest_fields.value = manifest_fields.default;
									}
								}
							} else {
								// The 'name' in theme_customize should match keys in saved_settings
								const name = row_item.name;
								const manifest_data = this.theme_customize[section_index].settings[row_index];

								// Check if saved_settings contains the key matching 'name'
								if (customize_data.saved_settings 
									&& customize_data.saved_settings.hasOwnProperty(name)
									&& typeof customize_data.saved_settings[name] !== 'undefined') {
									manifest_data.value = customize_data.saved_settings[name];
								} else {
									// Use default value if saved setting for this name doesn't exist
									manifest_data.value = manifest_data.default;
								}
							}
						});
					});

					this.updateColorPicker();
				}).catch(error => {
					this.notifyError('metaslider/theme-error', error, true)
				})
			}
		}
	},
	computed: {
		showThemeDetails() {
			return this.hoveredTheme.description || (this.selectedTheme.description && !this.isCustomTheme)
		},
		isCustomTheme() {
			if (!this.selectedTheme) return false
			return this.selectedTheme && this.selectedTheme.folder ? this.selectedTheme.folder.startsWith('_theme') : false
		},
		sliderTypeNotSupported() {
			if (!this.hovererdTheme || !this.hoveredTheme.tags) {
				return false
			}

			// TODO: Settings - once reactive, refactor this
			let currentType = document.querySelector('input[name="settings[type]"]:checked')
			if (!currentType) return false
			return parseInt(this.hoveredTheme.supports.indexOf(currentType.value), 10) === -1
		},
		supportLink() {
			return this.proUser ? 'https://www.metaslider.com/support/' : 'https://wordpress.org/support/plugin/ml-slider'
		},
		currentThemeSupports() {
			if (!this.current.id) return undefined
			return this.current.theme ? this.current.theme.supports : undefined
		},
		hasThemeSet() {
			if (!this.current.id || !this.current.hasOwnProperty('theme')) return false
			return this.current.theme.hasOwnProperty('folder') && this.current.theme.folder.length
		},
		recommendedThemeOptionsNotice() {
			const linkStart = `<a href="${this.metaslider_settings_page}" target="_blank" style="color:#135e96">`;
			const linkEnd = `</a>`;
			return this.sprintf(
				this.__('We recommend you enable %1$s"Recommended Theme Options"%2$s to automatically adjust slideshow settings when selecting a new theme.', 'ml-slider'),
				linkStart,
				linkEnd
			);
		},
		...mapGetters({
			current: 'slideshows/getCurrent'
		})
	},
	created() {},
	mounted() {
		this.fetchThemes()

		// TODO: when converting settings to vue, this could be removed
		document.querySelectorAll('input[name="settings[type]"]').forEach(sliderType => {
			sliderType.addEventListener('click', event => {

				// TODO: Settings - once reactive, refactor this
				this.updateSupportedStatus()

				// hack to work with non-vue (refreshes computed properties)
				this.hoveredTheme = {}
				this.hoveredTheme = this.selectedTheme || this.current.theme
			})
		})

		this.updateSupportedStatus()
		this.setColorPicker();
	},
	methods: {
		fetchThemes() {

			// Pre-built themes
			Axios.get('themes/all', {
				params: {
					action: 'ms_get_all_free_themes'
				}
			}).then(response => {
				this.themes = response.data.data
				this.loading = false
			}).catch(error => {
				this.loading = false
				this.notifyError('metaslider/theme-error', error, true)
			})

            // Custom themes
            this.loadingCustom = this.proUser
			this.proUser && Axios.get('themes/custom', {
				params: {
					action: 'ms_get_custom_themes'
				}
			}).then(response => {
				this.customThemes = (typeof response.data.data === 'object') ? response.data.data : {}
				this.loadingCustom = false
			}).catch(error => {
				this.loadingCustom = false
				this.notifyError('metaslider/theme-error', error, true)
			})
		},
		selectTheme(theme) {
			this.selectedTheme = (this.selectedTheme === theme) ? {} : theme
		},
		removeTheme() {
			this.selectedTheme = {}
			this.setTheme()
		},
		setTheme() {
			this.notifyInfo('metaslider/theme-updating', this.__('Saving theme...', 'ml-slider'))
			this.$refs.themesModal.close()

			// If the selected theme is set and already the current theme, do nothing
			if (Object.keys(this.selectedTheme).length && Object.is(this.selectedTheme.folder, this.current.theme.folder)) {
				this.notifySuccess('metaslider/theme-updated', this.__('Theme saved', 'ml-slider'), true)
			} else {
				this.$store.commit('slideshows/updateTheme', this.selectedTheme)

				Axios.post('themes/set', QS.stringify({
					action: 'ms_set_theme',
					slideshow_id: this.current.id,
					theme: this.selectedTheme
				})).then(response => {
					this.theme_customize = this.selectedTheme.customize || [];

					// Iterate each section that has 'status' key
					this.theme_customize.forEach((section_item, section_index) => {
						
						// Loop each section 'settings' key
						section_item.settings.forEach((row_item, row_index) => {

							if (row_item.type === 'fields' && typeof row_item.fields !== 'undefined') {
								// Add value property by copying default property value
								for (let i = 0; i < row_item.fields.length; i++) {
									const manifest_fields = this.theme_customize[section_index].settings[row_index].fields[i];

									manifest_fields.value = manifest_fields.default;
								}
							} else {
								const manifest_data = this.theme_customize[section_index].settings[row_index];

								manifest_data.value = manifest_data.default;
							}
						});
					});

					this.updateColorPicker();

					setTimeout(() => {
						// @TODO - Maybe move to admin.js under window.metaslider.app.EventManager.$on("metaslider/theme-updated", function () {});
						this.showHideColorPicker();  //delay to load all picker first
					}, 1000);

					this.notifySuccess('metaslider/theme-updated', this.__('Theme saved', 'ml-slider'), true)

					if (Number(this.autoThemeConfig)) {
						this.theme_edit_settings = this.selectedTheme.edit_settings ?? {};
						this.updateEditSettings();
					}
				}).catch(error => {
					this.notifyError('metaslider/theme-error', error, true)
				})
			}
		},
		setColorPicker() {
			var $ = window.jQuery;
			$('.static-theme-customize .colorpicker').each(function () {
				$(this).wpColorPicker({
					change: function(event, ui) {
						var input = $(this).parents('.wp-picker-container').find('input.colorpicker');
						var btn = $(this).parents('.wp-picker-container').find('button.wp-color-result');
			
						btn.css('background-color',ui.color.toCSS('rgba'));
			
						input.data('new-color',ui.color.toCSS('rgba'));
						input.attr('value',ui.color.toCSS('rgba'));
			
						btn.trigger('change');
					}
				}).promise().done(function() {
					var text = typeof metaslider !== 'undefined' ? metaslider : null;
					if (text) {
						$(this).parents('.wp-picker-container').find('.iris-strip').eq(0).prepend(`<span class="ms-color-tooltip">${text.tone}</span>`);
						$(this).parents('.wp-picker-container').find('.iris-strip').eq(1).prepend(`<span class="ms-color-tooltip">${text.opacity}</span>`);
					}
				});
			});
		},
		updateColorPicker() {
			this.$nextTick( function () {
				this.setColorPicker();

				var $ = window.jQuery;
				$('.static-theme-customize .colorpicker').each(function () {
					const newColor = $(this).val();
					if (newColor.length) {
						$(this).wpColorPicker('color', newColor);
					}
				});
			});
		},
		updateEditSettings() {
			this.$nextTick( function () {

				if (Object.keys(this.theme_edit_settings).length > 0) {
					var $ = window.jQuery;

					for (const [key,value] of Object.entries(this.theme_edit_settings)) {
						const field = $(`#metaslider_configuration [name="settings[${key}]"]`);

						if (field.length == 1) {
							if (field.is('select')) {
								// select
								if (field.find(`option[value="${value}"]`).length) {
									field.val(value).trigger('change');
								}
							} else if (field.is(':checkbox')) {
								// checkbox
								field.prop('checked', value).trigger('change');
							} else if (field.is('input')) {
								// input
								const fieldType = field.attr('type');
								if (fieldType === 'text' || fieldType === 'number') {
									field.val(value).trigger('change');
								}
							}
							field.attr('data-edit-setting', true); // Not requied. We add it just for reference
						}
					}

					setTimeout(function () {
						EventManager.$emit('metaslider/save');
					}, 1000);
				}
			});
		},
		openModal() {
			// TODO: when converting settings to vue, this could be removed.
			// It's used to force re-render of the UI
			this.hoveredTheme = this.selectedTheme || this.current.theme

			// If a current theme is selected, show that tab
			let tab = 'all'
			this.is_open = true
			this.$refs.themesModal.open(tab)
		},
		openPreview() {
			EventManager.$emit('metaslider/preview', {
				slideshowId: this.current.id,
				themeId: this.selectedTheme ? this.selectedTheme.folder : ''
			})
		},
		updateSupportedStatus() {
			if (!this.current.id || 'undefined' === typeof this.currentThemeSupports) return true
			let currentType = document.querySelector('input[name="settings[type]"]:checked')
			this.unsupportedSliderType = this.currentThemeSupports ? this.currentThemeSupports.indexOf(currentType.value) === -1 : false
		},
		themeStub() {
			return {
				description: null,
				folder: null,
				images: [],
				supports: [],
				tags: [],
				title: null,
				type: null
			}
		},
		showPremiumThemeAd(type, id) {
			if (this.nonSelectablePremiumTheme(type)) {
				this.revealThemeAd = id;
			}
		},
		hidePremiumThemeAd(type) {
			if (this.nonSelectablePremiumTheme(type)) {
				this.revealThemeAd = null;
			}
		},
		nonSelectablePremiumTheme(type) {
			return !this.proUser && type === 'premium';
		},
		showHideColorPicker() {
			this.$nextTick( function () {
				var $ = window.jQuery;
				$('.static-theme-customize tr').show();
				if ($('.ms-settings-table input[name="settings[pausePlay]"]').is(':checked')) {
					$('tr.customizer-pausePlay').show();
				} else {
					$('tr.customizer-pausePlay').hide();
				}
				if ($('.ms-settings-table select[name="settings[links]"]').val() === 'false') {
					$('tr.customizer-links').hide();
				} else {
					$('tr.customizer-links').show();
				}

				if ($('.ms-settings-table select[name="settings[navigation]"]').val() === 'true') {
					$('tr.customizer-navigation').show();
				} else {
					$('tr.customizer-navigation').hide();
				}
			});
		}
	}
}
</script>

<style lang="scss">
	@import '../assets/styles/globals.scss';
	@import '../assets/styles/mixins.scss';

	@mixin custom-theme-box() {
		.theme-image-wrapper {
			background: #2271b1;
			width: 100%;
			height: 100%;
			display: block;
		}
		.theme-label-info-v2 {
			position: absolute;
			top: 50%;
			left: 0;
			color: #fff;
			font-size: 1.3rem;
			font-weight: bold;
			z-index: 1;
			text-shadow: 0 0px 10px #000;
			width: 100%;
			transform: translateY(-50%);
			text-align: center;
			padding-left: 1rem;
			padding-right: 1rem;

			.custom-subtitle {
				color: #fff;
				font-size: 12px;
				font-weight: 300;
				margin-bottom: .1em;
				text-transform: uppercase;
			}
		}
		.theme-image-v2 {
			opacity: 0.6;
		}
		.theme-label-info-legacy {
			position: absolute;
			top: 10px;
			right: 10px;
			background: rgba(255,255,255,1);
			color: #2271b1;
			font-size: 0.7em;
			font-weight: normal;
			padding: 3px 7px;
			border-radius: 4px;
			z-index: 1;
			opacity: 0.5;
		}
	}
	#metaslider-ui .metaslider-theme-viewer {
		p {
			margin-top: 0;
			color: #444;
		}
	}
	#metaslider-ui .metaslider-theme-viewer > .sweet-modal-overlay > .sweet-modal {
		position: absolute;
		display: flex;
		flex-direction: column;
		width: 100%;
		height: 100%;
		max-width: 90%;
		max-height: 90%;
		left: 5%;
		top: 5%;
		right: 0;
		bottom: 0;
		overflow: visible;
		> .sweet-buttons {
			display: flex;
			align-items: center;
			justify-content: space-between;
			button {
				margin-left: 0.5rem;
			}
			.metaslider-theme-title {
				font-size: 1.3em;
				margin-top: 0.3em;
			}
		}
	}
	#metaslider-ui .sweet-modal .columns {
		display: flex;
		flex-direction: row;
		.theme-list-column {
			width: 75%;
			position: absolute;
			left: 0;
			top: 0;
			bottom: 0;
			right: 0;
			overflow: auto;
		}
		.theme-details-column {
			display: flex;
			flex-direction: column;
			justify-content: space-between;
			width: 25%;
			background: #f3f3f3;
			border-left: 1px solid #dddddd;
			position: absolute;
			bottom: 0;
			top: 0;
			right: 0;
			height: 100%;
			text-align: left;
			padding: 0 1rem 1rem;
			color: #666;
			[dir='rtl'] & {
				right: auto;
    			left: 0;
			}
			.metaslider-theme-title {
				background-color: #e8e8e8;
				color: #4a4a4a;
				font-size: 1.5em;
				font-weight: 500;
				margin: -1.5rem -1rem 1.5rem;
				padding: 0.5rem 1rem 0.4rem;
			}
			h2, h3 {
				margin: 0;
				margin-top: 1.5rem;
				margin-bottom: .6em;
				color: #666;
				padding: 0;
				font-weight: 600;
				text-transform: uppercase;
				font-size: 1em;
			}
			h2:first-of-type {
				margin-top: 0;
			}
			h3 {
				font-size: 0.9em;
				text-transform: none;
			}
			p {
				line-height: 1.4;
				font-size: 0.9em;
			}
			.ms-theme-description {
				margin-bottom: 2rem;
			}
			ul.ms-theme-tags {
				margin: 0;
				li {
					border-radius: 0.2em;
					display: inline-block;
					margin-right: 0.4em;
					line-height: 1;
					white-space: nowrap;
					font-size: 13px;
					line-height: 1;
					margin-right: 0.4em;
					white-space: nowrap;
					background: lightgray;
					padding: 5px;
					color: #555;
					// border-bottom: 1px solid #b4b6b7;
					// &:hover {
					// 	border-bottom: 1px solid #747b7d;
					// }
				}
			}
		}
	}
	#metaslider-ui .free-themes-not-found {
		max-width: 455px;
		h1 {
			color: $brand;
		}
	}
	#metaslider-ui .ms-image-selector {
		display: flex;
		flex-wrap: wrap;
		margin: 0;
		padding: 0.5rem;
		li {
			background: #fafafa;
			cursor: pointer;
			margin: 0;
			padding: 2px;
			width: 33.3%;
			@include from(1850px) {
				width: 25%;
			}
			@include until(1100px) {
				width: 50%;
			}
			@include until(900px) {
				width: 100%;
			}
			img {
				max-width: 100%;
				display: block;
				width: 100%;
			}
			span {
				border: 4px solid #fafafa;
				height: 100%;
				display: block;
				padding: 2px;
				position: relative;
			}
			@include custom-theme-box();
			&:hover span {
				border-color: #ccc;
			}
			&.selected span {
				border-color: $blue-dark;
			}
		}
	}
	#metaslider-ui .ms-image-selector li.ms-theme-more {
		cursor: default;
		span {
			font-size: 1.5em;
			text-transform: uppercase;
			line-height: 1.3;
			background: #efefef;
			border-color: #FFFFFF !important;
			height: 100%;
			> div {
				padding: 2rem;
				display: flex;
				flex-direction: column;
				align-items: center;
				justify-content: space-around;
				height: 100%;
				border: 4px solid #eaeaea;
			}
			small {
				font-size: 15px;
				text-transform: initial;
			}
		}
	}

	// Styles for the smaller box
	#metaslider-ui .theme-select-module {
		min-height: 70px;
		.button-info {
			margin-top: 0;
		}
	}
	#metaslider-ui .metaslider-theme-viewer {
		z-index: 3;
		position: relative;
		&.ms-modal-open {
			z-index: 999999;
		}
	}
	#metaslider-ui .theme-select-module .hndle {
		padding-bottom: 0;
	}
	#metaslider-ui .theme-select-module .hndle span {
		color: $brand;
	}
	#metaslider-ui .theme-select-module .slider-not-supported-warning {
		background-color: #f9edc9;
		border: 1px solid #f2a561;
		margin-bottom: 1em;
		padding: 10px 15px;
		svg {
			color: $red !important;
		}
	}
	#metaslider-ui .theme-select-module .sweet-buttons .slider-not-supported-warning {
		margin-bottom: 0;
	}
	#metaslider-ui .theme-select-module .change-theme-img-button {
		img {
			display: block;
			max-width: 100%;
			width: 100%;
		}
	}
	#metaslider-ui .ms-current-theme {
		@include custom-theme-box();

		.custom-theme-single {
			height: 100%;
			
			&--legacy {
				min-height: 200px;
			}
		}
	}
	#metaslider-ui .ms-current-theme .custom-theme-single .custom-subtitle {
		font-size: 12px;
		font-weight: 300;
		text-transform: uppercase;
		color: #fff;
		margin-bottom: 0.1em;
	}
	#metaslider-ui .custom-theme-single {
		width: 100%;
		height: 100%;
		line-height: normal;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		font-size: 1.3rem;
		font-weight: bold;
		background-color: #2271b1;
		color: white;
		padding: 1rem;
		box-sizing: border-box;
	}
	.regular-themes {
		.a-theme {
			//min-height: 216px;
		}
		.unlock-pro-theme-ad {
			span {
				position: relative
			}
			.custom-theme-single {
				position: absolute;
				z-index: 2;
			}
			img {
				//position: absolute;
				z-index: 1;
				width: calc( 100% - 4px ) !important;
				height: auto !important;
			}
			.upgrade-pro-theme-ad {
				width: calc( 100% - 4px ) !important;
				height: calc( 100% - 4px ) !important;

				@media (max-width: 1199px) and (min-width: 1100px) {
					h3 {
						display: none;
					}
				}
			}
		}
		.unlock-pro-custom-themes-ad {
			.upgrade-pro-theme-ad {

				&.custom-theme-editor {
					background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAyAAAAIVCAMAAAAebmvwAAACXlBMVEUAAAD///////9lZWVVVVVdXV2Dg4N0dHR7e3tHR0fNzc2Li4usrKy1tbW8vLzFxcXa2trIyMiYmJhnZ2fi4uK9vb2jo6PT09Ovr6/q6uru7u7x8fGUlJT7+/vm5uY7Ozufn5/c3NxNTU02NjZ5eXnf39/////09PShoaFXV1eampr///+oqKj+/v7////R0dHCwsJAQEC0tLRHR0cqKirKysr///94eHjs7Ozd3d2hoaG5ubn///+Dg4P+/v7///////9qamrX19dtbW1lZWViYmL////n5+f+/v739/eNjY3////+/v7w8PAzMzORkZFiYmJvb2+MjIz///////90dHSxsbHLy8txcXH///////////////+ZmZnq6ur////FxcXy8vJiYmL////k5OT///+urq6SkpK0tLSpqan///9eXl7///////////+5ubn////////////////////////////////////MzMz///////////////////////////////////////////+FhYX///////////////+ZmZn////////R0dH///9+fn7///////////////9iYmL///8uLi6lpaX///////++vr5jY2PNzc3///+urq6QkJD///////+JiYmWlpadnZ3///////////////9eXl7///////////////+8vLz///////+ysrJiYmJgYGBxcXHKysr////n5+fBwcHDw8Nubm7JycnCwsLFxcW6urro6Oi6urre3t6oqKiPj4/T09OgoKC9vb3FxcWurq6FhYUUfroNAAAAynRSTlMAzn8wJyw/NzwfZkRTWVtgamJKNG9dUGhWcnV3R3txGU1sIhU6bgV5TihLfVEHC2deHFcGDGPLYHRtgFrHaHzEyVJpNUwuDnJ7ekXBCXYTRURWcBJJXFdkNr51biV6c3KWeDi7cCqIdYyFXjxBPhyOLxo7VTZmFlhbnJtGTSKsGJYejSCga1JwpmMKFGtms2SjiTORRi0PgrWokklnggZzd2Bud31QMSg5PWiwEEOQhrmKQkBZmbdzk5QiMxQNE2FBUDkzRihzgmdGP5ba/QAAdIdJREFUeNrsnM9LG0EUgJeX3SSLN3MVFpIgbJcGFj3n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);
					background-size: cover;
				}

				@media (max-width: 1199px) and (min-width: 1100px) {
					h3 {
						display: none;
					}
				}
			}
		}
	}
	#metaslider-ui .sweet-modal-tabs li.sweet-modal-tab {
		display: none !important;
	}
	@include until(700px) {
		#metaslider-ui .sweet-modal {
			.sweet-content {
				display: block;
			}
			.columns {
				flex-direction: column;
				& > div {
					position: static!important;
					width: 100% !important;
				}
			}
		}
	}
	// Fade the custom theme backgrounds for variety
	// $step:1;
	// $color: $brand;
	// @while $step <=5  {
	// 	#metaslider-ui .custom-themes li:nth-child(10n+#{$step}) > div {
	// 		background: linear-gradient($color, darken($color, (5%)));
	// 	}
	// 	$color: darken($color, (5%));
	// 	$step: $step + 1;
	// }
	// $step:6;
	// @while $step <=10  {
	// 	#metaslider-ui .custom-themes li:nth-child(10n+#{$step}) > div {
	// 		background: linear-gradient($color, lighten($color, (5%)));
	// 	}
	// 	$color: lighten($color, (5%));
	// 	$step: $step + 1;
	// }
</style>
