<template>
	<div
		id="preview-component"
		:class="{ 'ms-has-error': errorMessage.length }"
		class="h-full z-max relative">
		<sweet-modal
			:ref="'preview'"
			:class="{'control-light': lightsOn}"
			:overlay-theme="overlayTheme"
			:modal-theme="overlayTheme"
			:blocking="true"
			:pulse-on-block="false"
			hide-close-button>
			<div
				slot="box-action"
				class="flex w-full bg-gray-light fixed top-0 left-0 right-0 h-12 items-center justify-between">
				<div class="flex w-full justify-center items-center">
                    <button
                        :title="__('Dark / Light Mode (Use L key)', 'ml-slider') "
                        class="lightbulb w-14 p-2 hover:bg-black hover:text-white transition duration-200"
                        :class="{
                            'bg-black text-white': overlayTheme !== 'dark',
                            'bg-transparent text-black': overlayTheme === 'dark'
                        }"
                        @click="toggleLights">
                        <svg style="width:32px;height:32px;margin: 0 auto;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </button>
                    <button
                        :title="__('Toggle full width (Use F key)', 'ml-slider')"
                        :disabled="showLaptopWidth || showTabletWidth || showSmartphoneWidth"
                        :class="{
                            'bg-black text-white': showFullwidth && !(showLaptopWidth || showTabletWidth || showSmartphoneWidth),
                            'bg-transparent text-black': !showFullwidth && !(showLaptopWidth || showTabletWidth || showSmartphoneWidth),
                            'opacity-50 cursor-not-allowed': showLaptopWidth || showTabletWidth || showSmartphoneWidth
                        }"
                        class="w-14 p-2 hover:bg-black hover:text-white transition duration-200"
                        @click="toggleFullwidth">
						<span v-if="showFullwidth" class="dashicons dashicons-editor-contract w-full text-xl"></span>
						<span v-else class="dashicons dashicons-editor-expand text-xl"></span>
                    </button>
					<button v-if="mobileSettingsEnabled"
						:title="__('Laptop preview', 'ml-slider')"
						:class="{
							'bg-black text-white': showLaptopWidth,
							'bg-transparent text-black': !showLaptopWidth
						}"
						class="w-14 p-2 hover:bg-black hover:text-white transition duration-200"
						@click="toggleDevice('laptop')">
						<span class="dashicons dashicons-laptop text-xl"></span>
					</button>
					<button v-if="mobileSettingsEnabled"
						:title="__('Tablet preview', 'ml-slider')"
						:class="{
							'bg-black text-white': showTabletWidth,
							'bg-transparent text-black': !showTabletWidth
						}"
						class="w-14 p-2 hover:bg-black hover:text-white transition duration-200"
						@click="toggleDevice('tablet')">
						<span class="dashicons dashicons-tablet text-xl"></span>
					</button>
					<button v-if="mobileSettingsEnabled"
						:title="__('Smartphone preview', 'ml-slider')"
						:class="{
							'bg-black text-white': showSmartphoneWidth,
							'bg-transparent text-black': !showSmartphoneWidth
						}"
						class="w-14 p-2 hover:bg-black hover:text-white transition duration-200"
						@click="toggleDevice('smartphone')">
						<span class="dashicons dashicons-smartphone text-xl"></span>
					</button>
				</div>
				<button
					:title="__('Exit preview', 'ml-slider') + ' (ESC)'"
					class="mr-2 rtl:ml-2 rtl:mr-0 w-6 text-black"
					@click="closePreview">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
				</button>
			</div>
            <iframe
				v-if="'' !== html"
				:class="{'invisible':!iframeLoaded}"
				:id="'iframe-' + _uid"
				:srcdoc="html"
				frameborder="0"
				@load="setupIframe"
			/>
            <div v-else>
                <span v-if="!iframeLoaded && !errorMessage.length">
                    {{ __('Loading...', 'ml-slider') }}
                </span>
                <p
                    v-if="errorMessage.length"
                    class="ms-error"
                    v-text="errorMessage"/>
                <p
                    v-if="notFullySupported"
                    class="ms-feature-not-supported">
                    {{ __('This feature is not fully supported in this browser.', 'ml-slider') }}
                </p>
            </div>
		</sweet-modal>
	</div>
</template>

<script>
// TODO Maybe we dont need to save first if on a theme view
// green checkmark

import { EventManager } from '../utils'
import { Axios } from '../api'
import './components'
import srcDoc from 'srcdoc-polyfill'
import hotkeys from 'hotkeys-js';
import { mapGetters } from 'vuex'

export default {
	props: {},
	data() {
		return {
			html: '',
			slideshowId: '',
			theme_id: '',
			iframeLoaded: false,
			errorMessage: '',
			previewIframe: {},
			overlayTheme: 'dark',
			showFullwidth: false,
			showLaptopWidth: false,
			showTabletWidth: false,
			showSmartphoneWidth: false,
			notFullySupported: !('srcdoc' in document.createElement('iframe')),
			resizeEvent: {},
			mobileSettingsEnabled: typeof metaslider.mobile_settings !== 'undefined' && metaslider.mobile_settings === '1' ? true : false
		}
	},
	computed: {
		lightsOn() {
			// TODO: save the state in the settings behind the scenes
			return 'dark' !== this.overlayTheme
		},
		maxWidth() {
			// TODO: refactor when settings object is implimented in vue store
			let width = parseInt(document.getElementsByName('settings[width]')[0].value, 10)

			// This accounts the 40px padding on each side.
			return (!this.showFullwidth && width) ? width + 'px' : '100%'
		},
		...mapGetters({
			current: 'slideshows/getCurrent'
		})
	},
	mounted() {
		// Note, the slideshow should be saved BEFORE this event is fired
		EventManager.$on('metaslider/open-preview', ({ slideshowId, themeId }) => {
			this.openPreview(slideshowId, themeId)
		})

		hotkeys('alt+p', () => this.handleOpeningPreviewByKeyboard())
	},
	methods: {
		hasSlides() {
			return document.querySelector('tr.slide:not(.ms-deleted)')
		},
		openPreview(slideshowId = null, themeId = null) {
			// If no images are found, offer to import some.
			if (!this.hasSlides()) {
				EventManager.$emit('import-notice', themeId || this.current.theme.folder)
				return false
			}

			// Add events for keyboard controls
			document.addEventListener('keyup', this.handleKeyups)

			// Reset to not show fullwidth whenever loaded.
			this.showFullwidth = false

			// Open the specific preview
			this.$refs['preview'].open()

			// Fetch the iframe
			this.fetchIframe(slideshowId, themeId)

		},
		closePreview() {
			this.$refs['preview'].close()
			this.html = ''
			this.iframeLoaded = false
			this.errorMessage = ''
			document.removeEventListener('keyup', this.handleKeyups)
		},
		fetchIframe(slideshowId = null, themeId = null) {
			this.errorMessage = ''
			Axios.get('slideshow/preview', {
				params: {
					action: 'ms_get_preview',
					theme_slug: themeId || this.current.theme.folder, // Used for pro themes
					slideshow_id: slideshowId || this.current.id,
					theme_id: themeId || this.current.theme.folder
				}
			}).then(response => {
				this.html = response.data.data

				// polyfill for ie11
				this.$nextTick(() => {
					srcDoc.set(document.getElementById('iframe-' + this._uid))

					// ! Somehow this is an IE11 fix. I'm guessing it forces Vuejs to compare
					// ! the dom to the virtual dom and force the update. Oh well, it works
					if (this.notFullySupported) console.log(document.getElementById('iframe-' + this._uid))
				})
				this.notifySuccess('metaslider/preview-loaded', 'Preview loaded')
			}).catch(error => {
				this.iframeLoaded = true
				this.errorMessage = this.getErrorMessage(error.response)
				this.notifyError('metaslider/preview-error', error)
			})
		},
		setupIframe(event) {
			this.previewIframe = {
				iframe: event.target,
				window: event.target.contentWindow,
				document: event.target.contentDocument,
				container: event.target.contentDocument.getElementById('preview-container'),
				slideshow: event.target.contentDocument.querySelector('.metaslider')
			}

			// Add events for keyboard controls for when focus is inside the iframe
			this.previewIframe.document.addEventListener('keyup', this.handleKeyups)

			// Set the slideshow to 100% width
			this.previewIframe.slideshow.style.width = '100%'

			// Add a way to fake a resize event inside the iframe, and trigger it
			if ('function' !== typeof window.Event) { // IE 11 polyfill
				this.resizeEvent = this.previewIframe.window.document.createEvent('UIEvents')
				this.resizeEvent.initUIEvent('resize', true, false, window, 0)
			} else {
				this.resizeEvent = new Event('resize')
			}

			// If the slideshow is a carousel make full width
			if (document.getElementsByName('settings[carouselMode]')[0].checked) {
				this.toggleFullwidth()
			}

			this.previewIframe.window.dispatchEvent(this.resizeEvent)
			this.iframeLoaded = true
		},
		toggleFullwidth() {
			this.showTabletWidth = false;
			this.showSmartphoneWidth = false;
			this.showLaptopWidth = false;

			this.showFullwidth = !this.showFullwidth

			// Set the container and slideshow to full width
			this.previewIframe.container.style.maxWidth = this.maxWidth
			this.previewIframe.slideshow.style.maxWidth = this.maxWidth

			// trigger a resize in the iframe to let the slider recalculate itself
			this.previewIframe.window.dispatchEvent(this.resizeEvent)
		},
		toggleDevice(device) {
			this.showFullwidth = false;
			
			const breakpoints = typeof metaslider.breakpoints === 'object' ? metaslider.breakpoints : {
				laptop: 1024,
				tablet: 768,
				smartphone: 480
			};

			// Set the container and slideshow to full width
			switch (device) {
				default:
				case 'laptop':
					this.showTabletWidth = false;
					this.showSmartphoneWidth = false;
					
					this.showLaptopWidth = !this.showLaptopWidth


					this.previewIframe.iframe.style.width = this.showLaptopWidth ? breakpoints.laptop + 'px' : '100%'
					this.previewIframe.container.style.maxWidth = this.showLaptopWidth ? breakpoints.laptop + 'px' : this.maxWidth
					this.previewIframe.slideshow.style.maxWidth = this.showLaptopWidth ? breakpoints.laptop + 'px' : this.maxWidth
					break
				case 'tablet':
					this.showLaptopWidth = false;
					this.showSmartphoneWidth = false;

					this.showTabletWidth = !this.showTabletWidth

					this.previewIframe.iframe.style.width = this.showTabletWidth ? breakpoints.tablet + 'px' : '100%'
					this.previewIframe.container.style.maxWidth = this.showTabletWidth ? breakpoints.tablet + 'px' : this.maxWidth
					this.previewIframe.slideshow.style.maxWidth = this.showTabletWidth ? breakpoints.tablet + 'px' : this.maxWidth
					break
				case 'smartphone':
					this.showLaptopWidth = false;
					this.showTabletWidth = false;

					this.showSmartphoneWidth = !this.showSmartphoneWidth

					this.previewIframe.iframe.style.width = this.showSmartphoneWidth ? breakpoints.smartphone + 'px' : '100%'
					this.previewIframe.container.style.maxWidth = this.showSmartphoneWidth ? breakpoints.smartphone + 'px' : this.maxWidth
					this.previewIframe.slideshow.style.maxWidth = this.showSmartphoneWidth ? breakpoints.smartphone + 'px' : this.maxWidth
					break
			}
			
			// hide content while reloading to avoid glitch
			this.iframeLoaded = false
			
			// reload iframe content to ensure layout recalculates for new screen size
			this.previewIframe.iframe.contentWindow.location.reload()
		},
		toggleLights() {
			this.overlayTheme = 'dark' === this.overlayTheme ? 'light' : 'dark'
		},
		handleKeyups(event) {
			70 === event.keyCode && this.toggleFullwidth() // F
			76 === event.keyCode && this.toggleLights() // L
			27 === event.keyCode && this.closePreview() // ESC
		},
		handleOpeningPreviewByKeyboard() {

			if (this.$parent.saving) return false

			if (document.getElementById('preview-component').length) {
				return false
			}

			// This will also offer to import slides if none exist
			EventManager.$emit('metaslider/preview')
		}
	}
}
</script>

<style lang="scss">
	@import '../assets/styles/globals.scss';
	#preview-component .dashicons,
	#preview-component .dashicons:before {
		font-size: 30px;
		width: 32px;
  		height: 32px;
	}
	div#preview-component {
		float: left;
		> .sweet-modal-overlay {
			background: #FFF;
			&.theme-dark {
				background: $wp-black;
			}
			.sweet-modal {
				background: transparent;
				box-shadow: none;
				min-width: 100%;
				padding: 0;
				.sweet-content,
				.sweet-content-content,
				iframe {
                    display: flex;
                    align-items: center;
                    justify-content: center;
					width: 100%;
					height:100%;
				}
			}
		}
	}
</style>
