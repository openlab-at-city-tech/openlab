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
			default: 'override'
		}
	},
	data() {
		return {
			sources: {
				'override': this.override,
				'image-caption': this.cleanupQuotes(this.imageCaption),
				'image-description': this.cleanupQuotes(this.imageDescription)
			},
			language: {},
			selectedSource: '',
			editorInstance: false,
			editorContent: null,
			textareaContent: ''
		}
	},
	created() {
        this.selectedSource = this.captionSource ? this.captionSource : 'override'
        // Check if URL contains metaslider_add_sample_slides=withcaption
        const urlParams = new URLSearchParams(window.location.search);
        const sampleSlides = urlParams.get('metaslider_add_sample_slides');
		
        if (sampleSlides === 'withcaption') {
            // Set default to media caption for carousel with captions
            this.selectedSource = 'image-caption';
        } else {
            this.selectedSource = this.captionSource ? this.captionSource : 'override';
        }
    },
	mounted() {
		// When an image is updated, check that the data is fresh (via Vue or jQuery)
		EventManager.$on('metaslider/image-meta-updated', (slides, metadata) => this.updateMetadata(slides, metadata))
		window.jQuery(window).on('metaslider/image-meta-updated', (event, slides, metadata) => this.updateMetadata(slides, metadata))

		// Set specific wording for the options
		this.language = {
			'image-caption': this.__('Media caption', 'ml-slider'),
			'image-description': this.__('Media description', 'ml-slider'),
			'override': this.__('Manual entry', 'ml-slider'),
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

					const text = typeof metaslider !== 'undefined' ? metaslider : null;

					const id = `caption_override_${this.$parent.id}`;
					// Add Image data to metaslider.tinymce
					if (typeof metaslider.tinymce.find(obj => obj.type === 'image') === 'undefined') {
						metaslider.tinymce.push({
							type: 'image',
							configuration: {
								toolbar: [
									'undo redo bold italic forecolor fontsizeinput link unlink alignleft aligncenter alignright styles code device_options add_button'
								],
								menubar: false,
								plugins: 'code link',
								branding: false,
								promotion: false,
								height: 240,
								preview_styles: false,
								forced_root_block: 'div',
								convert_urls: false,
								content_style: `
									.ms-custom-button {
										display: inline-block;
										background-color: #0073aa;
										color: #fff;
										cursor: pointer;
										padding: 8px 14px;
										border-radius: 4px;
										text-decoration: none;
										transition: background-color 0.2s ease;
									}
									.ms-custom-button:hover {
										opacity: 0.8;
									}
								`,
								setup: (editor) => {
									editor.on('init', function() {
										const text = this.__('This will override the Caption Link Color option in the "Theme" area of the right sidebar.', 'ml-slider');
										setTimeout(function() {
											const forecolorButton = editor.editorContainer.querySelector('[aria-label*="Text color"]');
											if (forecolorButton) {
												forecolorButton.setAttribute('title', text);
											}
										}, 100);
									}.bind(this));

									if (typeof metaslider !== 'undefined' && metaslider.mobile_settings) {
										editor.on('BeforeSetContent', function (event) {
											event.content = event.content
												.replace(/\n/g, ' ')
												.replace(/<div>\s*(\[metaslider_hide[^\]]*\])\s*<\/div>/g, '$1')
												.replace(/<div>\s*(\[\/metaslider_hide\])\s*<\/div>/g, '$1'); 
										});

										editor.on('PostProcess', function (event) {
											event.content = event.content
												.replace(/\n/g, ' ')
												.replace(/<div>\s*(\[metaslider_hide[^\]]*\])\s*<\/div>/g, '$1')
												.replace(/<div>\s*(\[\/metaslider_hide\])\s*<\/div>/g, '$1');
										});
										let selectedOptions = [];
										editor.ui.registry.addMenuButton('device_options', {
											text: metaslider.device_options_dropdown,
											fetch: function(callback) {
												callback([
													{
														type: 'togglemenuitem',
														text: metaslider.hide_on_mobile,
														onAction: function(api) {
															toggleSelection(editor, api, 'smartphone', selectedOptions);
														}
													},
													{
														type: 'togglemenuitem',
														text: metaslider.hide_on_tablet,
														onAction: function(api) {
															toggleSelection(editor, api, 'tablet', selectedOptions);
														}
													},
													{
														type: 'togglemenuitem',
														text: metaslider.hide_on_laptop,
														onAction: function(api) {
															toggleSelection(editor, api, 'laptop', selectedOptions);
														}
													},
													{
														type: 'togglemenuitem',
														text: metaslider.hide_on_desktop,
														onAction: function(api) {
															toggleSelection(editor, api, 'desktop', selectedOptions);
														}
													}
												]);
											}
										});
									}
									
									editor.ui.registry.addButton('add_button', {
										text: text.add_button,
										onAction: function() {
											editor.windowManager.open({
												title: text.add_button,
												body: {
													type: 'panel',
														items: [
														{ type: 'input', name: 'url', label: text.url },
														{ type: 'htmlpanel', html: `<div id="url-error" style="color: red; margin-bottom: 5px; display: none;">${text.enter_url}</div>` },
														{ type: 'input', name: 'text', label: text.link_text },
														{ type: 'htmlpanel', html: `<div id="text-error" style="color: red; margin-bottom: 5px; display: none;">${text.enter_text}</div>` },
														{ type: 'checkbox', name: 'newtab', label: text.open_new_window },
														{ type: 'htmlpanel', html: `<label class="tox-label">${text.button_color}</label><div class="ms-color-tooltip-wrapper"><input type="text" id="bgColor" class="colorpicker" value="rgb(0, 115, 170)" data-alpha-enabled="true" /></div>` },
														{ type: 'htmlpanel', html: `<label class="tox-label">${text.text_color}</label><div class="ms-color-tooltip-wrapper"><input type="text" id="txtColor" class="colorpicker" value="rgb(255, 255, 255)" data-alpha-enabled="true" /></div>` },											   
														]
												},
												initialData: {},
												buttons: [
													{ type: 'cancel', text: text.close },
													{ type: 'submit', name: 'insert', text: text.insert, primary: true }
												],
												onChange: (api, details) => {},
												onSubmit: function(api) {
													const data = api.getData();
													const url = data.url?.trim() || '';
													const text = data.text?.trim() || '';
													const newtab = data.newtab || false;
													const bgColor = document.getElementById('bgColor').value || 'rgb(0, 115, 170)';
													const txtColor = document.getElementById('txtColor').value || 'rgb(255, 255, 255)';
													const urlError = document.getElementById('url-error');
													const textError = document.getElementById('text-error');
													
													if (url.trim() == '') {
														urlError.style.display = '';
														return;
													} else {
														urlError.style.display = 'none';
													}

													if (text.trim() == '') {
														textError.style.display = '';
														return;
													} else {
														textError.style.display = 'none';
													}

													const fallbackSanitize = (text) => {
														if (!text) return '';
														return text
															.replace(/</g, '&lt;')
															.replace(/>/g, '&gt;')
															.replace(/"/g, '&quot;')
															.replace(/'/g, '&#039;')
															.replace(/&/g, '&amp;');
													}

													const wpSanitizeAvailable = wp && wp.sanitize && wp.sanitize.stripTagsAndEncodeText;
													const sanitizedUrl = wpSanitizeAvailable ? 
														wp.sanitize.stripTagsAndEncodeText(url.trim()) : 
														fallbackSanitize(url.trim());
													const sanitizedText = wpSanitizeAvailable ? 
														wp.sanitize.stripTagsAndEncodeText(text.trim()) : 
														fallbackSanitize(text.trim());
													const targetAttr = newtab ? ' target="_blank" rel="noopener"' : '';
													const bgColorStyle = `background-color: ${bgColor};`;
													const txtColorStyle = `color: ${txtColor};`;
													
													const buttonHtml = `<a href="${sanitizedUrl}" class="ms-custom-button" ${targetAttr} style="${bgColorStyle}${txtColorStyle}">${sanitizedText}</a>`;

													editor.insertContent(buttonHtml);
													api.close();
												}
											});

											let $ = window.jQuery
											setTimeout(() => {
												$('.tox-dialog-wrap .colorpicker').each(function() {
													$(this).wpColorPicker({
														change: function(event, ui) {
															var input = $(this).parents('.wp-picker-container').find('input.colorpicker');
															var btn = $(this).parents('.wp-picker-container').find('button.wp-color-result');

															btn.css('background-color',ui.color.toCSS('rgba'));

															input.data('new-color',ui.color.toCSS('rgba'));
															input.attr('value',ui.color.toCSS('rgba'));
															input.val(ui.color.toCSS('rgba'));
														}
													}).promise().done(function() {
														if (text) {
															$(this).parents('.wp-picker-container').find('.iris-strip').eq(0).prepend(`<span class="ms-color-tooltip">${text.tone}</span>`);
															$(this).parents('.wp-picker-container').find('.iris-strip').eq(1).prepend(`<span class="ms-color-tooltip">${text.opacity}</span>`);
														}
													});
												});
											}, 100);
										}
									});

									editor.on('input', function () {
										updateContent(editor);
									});

									editor.on('ExecCommand', function () {
										updateContent(editor);
									});

									var updateContent = function (editor) {
										var el = document.getElementById(editor.id);
										if (el) {
											el.value = editor.getContent();
										}
									}
								}
							}
						});

						function toggleSelection(editor, api, option, selectedOptions) {
							let selectedText = editor.selection.getContent()

							// Check if text is already wrapped in [metaslider-hide] shortcode
							let hideRegex = /\[metaslider_hide devices="(.*?)"\]([\s\S]*?)\[\/metaslider_hide\]/;
							let match = selectedText.match(hideRegex);
							let currentOptions = [];

							if (match) {
								currentOptions = match[1].split(", ").map(opt => opt.trim());
								selectedText = match[2].trim();
							}

							if (currentOptions.includes(option)) {
								currentOptions = currentOptions.filter(item => item !== option);
								api.setActive(false);
							} else {
								currentOptions.push(option);
								api.setActive(true);
							}

							let newTag = currentOptions.length > 0
        						? `[metaslider_hide devices="${currentOptions.join(", ")}"]${selectedText}[/metaslider_hide]`
        						: selectedText;

							editor.selection.setContent(newTag);
							editor.execCommand('mceUpdateContent');
						}

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
		}
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
