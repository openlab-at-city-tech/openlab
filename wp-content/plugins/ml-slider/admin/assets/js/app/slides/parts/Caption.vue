<template>
	<div class="row caption mb-0">
		<div class="flex justify-between">
			<label class="mr-4 caption-label">
				{{ __("Caption", "ml-slider") }}
				<span class="dashicons dashicons-info tipsy-tooltip-top" :title="__('Enter text that will appear with your image slide.', 'ml-slider')" style="line-height: 1.2em;"></span>
			</label>
			<div
				:aria-labelledby="'caption_source_' + $parent.id"
				role="radiogroup"
				class="mb-1 mr-1">
				<div
					v-for="(caption, source) in sources"
					:key="source"
					class="whitespace-no-wrap inline-block mb-1 px-1">
					<input
						:id="source + '-' + $parent.id"
						:value="source"
						:name="'attachment[' + $parent.id + '][caption_source]'"
						v-model="selectedSource"
						class="m-0"
						type="radio"
						@click="maybeFocusTextarea">
					<label
						:for="source + '-' + $parent.id"
						:title="language[source]"
						class="m-0 truncate">
						{{ language[source] }}
					</label>
				</div>
			</div>
		</div>
		<textarea
			v-if="selectedSource !== 'override'"
			:value="!sources[selectedSource].length ? __('No default was found', 'ml-slider') : sources[selectedSource]"
			:title="__('Automatically updates directly from the WP Media Library', 'ml-slider')"
			class="tipsy-tooltip-top"
			readonly/>
		<textarea
			v-if="selectedSource === 'override'"
			v-model="textareaContent"
			:title="__('You may use HTML here', 'ml-slider')"
			:id="'caption_override_' + $parent.id"
			:name="'attachment[' + $parent.id + '][post_excerpt]'"
			class="tipsy-tooltip-top wysiwyg"
			data-type="image"/>
	</div>
</template>

<script>
import { EventManager } from '../../utils'
export default {
	props: {
		imageCaption: {
			type: [String],
			default: ''
		},
		imageDescription: {
			type: [String],
			default: ''
		},
		override: {
			type: [String],
			default: ''
		},
		captionSource: {
			type: [String],
			default: 'image-caption'
		}
	},
	data() {
		return {
			sources: {
				'image-caption': this.cleanupQuotes(this.imageCaption),
				'image-description': this.cleanupQuotes(this.imageDescription),
				'override': this.override
			},
			language: {},
			selectedSource: '',
			editorInstance: false,
			editorContent: null,
			textareaContent: ''
		}
	},
	created() {
		this.selectedSource = this.captionSource ? this.captionSource : 'image-caption'
	},
	mounted() {
		// When an image is updated, check that the data is fresh (via Vue or jQuery)
		EventManager.$on('metaslider/image-meta-updated', (slides, metadata) => this.updateMetadata(slides, metadata))
		window.jQuery(window).on('metaslider/image-meta-updated', (event, slides, metadata) => this.updateMetadata(slides, metadata))

		// Set specific wording for the options
		this.language = {
			'image-caption': this.__('Media caption', 'ml-slider'),
			'image-description': this.__('Media description', 'ml-slider'),
			'override': this.__('Manual entry', 'ml-slider')
		}

		this.textareaContent = this.convertStyleAttributes(this.sources['override']);
	},
	methods: {
		maybeFocusTextarea(event) {
			// Happens on click only
			'override' === event.target.defaultValue &&
				setTimeout(() => document.getElementById('caption_override_' + this.$parent.id).focus(), 300)
		},
		updateMetadata(slides, metadata) {
			console.log(slides)
			if (slides.includes(this.$parent.id)) {
				this.sources['image-caption'] = metadata.caption
				this.sources['image-description'] = metadata.description
			}
		},
		initializeTinyMCE() {
			this.$nextTick( function () {
				if (!this.editorInstance) {

					if (typeof tinymce === 'undefined') {
						console.log('TinyMCE is not defined or disabled in MetaSlider settings!');
						return;
					}

					const id = `caption_override_${this.$parent.id}`;
					
					// Add Image data to metaslider.tinymce
					if (typeof metaslider.tinymce.find(obj => obj.type === 'image') === 'undefined') {
						metaslider.tinymce.push({
							type: 'image',
							configuration: {
								toolbar: [
									'undo redo bold italic forecolor link unlink alignleft aligncenter alignright styles code'
								],
								menubar: false,
								plugins: 'code link',
								branding: false,
								promotion: false,
								height: 240,
								preview_styles: false,
								forced_root_block: 'div',
								convert_urls: false,
								setup: function (editor) {
									editor.on('input', function () {
										updateContent(editor);
									});

									editor.on('ExecCommand', function () {
										updateContent(editor);
									});

									const updateContent = function (editor) {
										const el = document.getElementById(editor.id);
										if (el) {
											el.value = editor.getContent();
										}
									}
								}
							}
						});

					}

					tinymce.init({
						...{ 
							selector: `#${id}`,
							init_instance_callback: (editor) => {
								if (this.editorContent) {
									const updateContent = function (editor) {
										const el = document.getElementById(editor.id);
										if (el) {
											el.value = editor.getContent();
										}
									}

									// Update editor content
									editor.setContent(this.editorContent);
									// Update textarea
									updateContent(editor);
								}
							}
						},
						...metaslider.tinymce.find(obj => obj.type === 'image').configuration
					});
					
					this.editorInstance = true;
				}
			});
		},
		destroyTinyMCE() {
			if (this.editorInstance) {
				const id = `caption_override_${this.$parent.id}`;

				// Save current content to use later if switch back to caption override
				this.editorContent = tinymce.get(id).getContent();

				tinymce.get(id).destroy();
				this.editorInstance = false;
			}
		},
		// Avoid Vue stripping style attribute
		// e.g. style=\"color: rgb(0, 0, 0);\" => style="color: rgb(0, 0, 0);" 
		convertStyleAttributes(html) {
			const regex = /style=\\(".*?"|'.*?')/g;
			return html.replace(regex, match => match.replace(/\\(?="|')/g, ''));
		},
		/**
		 * Avoid Vue converting single quotes into &#039; 
		 * and adding inverted slash for sinle and double quotes
		 * 
		 * @since 3.80
		 * 
		 * Replace: \&#039; with single quote, \' with single quote, and \" with double quote
		 */
		cleanupQuotes(html) {
			const regex = /\\&#039;|\\'|\\\"/g;
			return html.replace(regex, match => {
				// 
				if (match === '\\&#039;' || match === "\\'") {
					return "'";
				} else if (match === '\\"') {
					return '"';
				}
			});
		},
	},
	watch: {
		selectedSource(newSource, oldSource) {

			if (typeof tinymce === 'undefined') {
				console.log('TinyMCE is not defined or disabled in MetaSlider settings!');
				return;
			}

			if (newSource === 'override' && oldSource !== 'override') {
				this.initializeTinyMCE();
			} else if (newSource !== 'override' && oldSource === 'override') {
				this.destroyTinyMCE();
			}
		}
	}
}
</script>
