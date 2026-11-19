<template>
	<div class="rounded-lg">
		<div class="p-4 text-white rounded-t-lg flex items-center justify-center gap-2" style="background-color:#29375b;">
			<svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M14 8h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
			</svg>
			<h3 class="m-0 text-lg leading-6 font-medium text-white">{{ __('MetaSlider Gallery', 'ml-slider') }}</h3>
		</div>
		<div class="p-5 text-center" v-html="adHtml"></div>
	</div>
</template>

<script>
export default {
	filename: 'GalleryConvertNotice',
	created() {
		this.$parent.classes = 'w-full max-w-lg rounded-lg'
	},
	mounted() {
		this.notifyInfo('metaslider/gallery-convert-notice-opened', this.__('Gallery convert notice opened', 'ml-slider'))
	},
	beforeDestroy() {
		this.notifyInfo('metaslider/gallery-convert-notice-closed', this.__('Gallery convert notice closed', 'ml-slider'))
	},
	computed: {
		adHtml() {
			return window.metaslider_api.gallery_convert_ad || ''
		}
	}
}
</script>
