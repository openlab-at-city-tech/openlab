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

// Module-level (not per-editor-instance) so the icon index is fetched once and
// shared across every slide's caption editor on the page, instead of once per slide.
const POPULAR_ICONS = [
	'star', 'heart', 'house', 'user', 'envelope', 'phone', 'check', 'xmark',
	'magnifying-glass', 'camera', 'image', 'video', 'film', 'music', 'location-dot',
	'calendar-days', 'clock', 'bell', 'gift', 'cart-shopping', 'thumbs-up', 'comment',
	'share-nodes', 'lock', 'key', 'gear', 'trash', 'pen', 'arrow-right', 'arrow-left',
	'chevron-down', 'chevron-up', 'plus', 'minus', 'circle-info', 'triangle-exclamation',
	'circle-question', 'download', 'upload', 'link', 'tag', 'flag', 'fire', 'bolt',
	'sun', 'moon', 'cloud', 'umbrella', 'leaf', 'paw', 'gem', 'crown', 'trophy',
	'medal', 'circle-play', 'circle-check', 'address-book', 'map', 'compass', 'globe'
];

let iconIndex;
let iconIndexPromise;
const fetchIconIndex = function() {
	if (iconIndex) {
		return Promise.resolve(iconIndex);
	}
	if (iconIndexPromise) {
		return iconIndexPromise;
	}
	iconIndexPromise = fetch(metaslider.fontawesome_icons_url)
		.then(function(response) { return response.json(); })
		.then(function(data) {
			iconIndex = Array.isArray(data) ? data : [];
			return iconIndex;
		})
		.catch(function() {
			iconIndex = [];
			return iconIndex;
		});
	return iconIndexPromise;
}

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
        // metaslider_add_sample_slides param is deprecated since 3.106 - We use the native import for the quickstart
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
						console.log('TinyMCE is not defined or disabled in MetaSlider Slideshow settings!');
						return;
					}

					const text = typeof metaslider !== 'undefined' ? metaslider : null;

					const id = `caption_override_${this.$parent.id}`;
					// Add Image data to metaslider.tinymce
					if (typeof metaslider.tinymce.find(obj => obj.type === 'image') === 'undefined') {
						metaslider.tinymce.push({
							type: 'image',
							configuration: {
								toolbar: 'undo redo bold italic underline strikethrough removeformat forecolor fontsizeinput lineheight styles link unlink alignleft aligncenter alignright add_image add_icon add_button device_options code',
								menubar: false,
								plugins: 'code link',
								line_height_formats: '0.8 0.9 1 1.1 1.2 1.3 1.4 1.5 1.6 1.7 1.8 1.9 2 2.1 2.2 2.3 2.4 2.5 2.6 2.7 2.8 2.9 3',
								branding: false,
								promotion: false,
								height: 240,
								preview_styles: false,
								forced_root_block: 'div',
								convert_urls: false,
								// Mirrors TinyMCE's own bundled default (see non_empty_elements in
								// admin/assets/vendor/tinymce/js/tinymce/tinymce.min.js) plus 'i', so the inserted
								// icon survives cleanup as an "empty" tag. Setting this option replaces rather than
								// merges with the default, so keep this list in sync if TinyMCE is ever upgraded.
								non_empty_elements: 'td th iframe video audio object script code pre svg i',
								extended_valid_elements: 'i[class|aria-hidden|style|title]',
								content_css: typeof metaslider !== 'undefined' && metaslider.fontawesome_css_urls
									? metaslider.fontawesome_css_urls
									: [],
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
									img {
										max-width: 100%;
										height: auto !important;
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

									let mediaFrame;
									editor.ui.registry.addButton('add_image', {
										icon: 'image',
										tooltip: text.add_image,
										onAction: function() {
											if (mediaFrame) {
												mediaFrame.open();
												return;
											}

											mediaFrame = wp.media({
												title: text.add_image,
												button: { text: text.insert },
												multiple: false,
												library: { type: 'image' }
											});

											mediaFrame.on('select', function() {
												const attachment = mediaFrame.state().get('selection').first().toJSON();

												const fallbackSanitize = (value) => {
													if (!value) return '';
													return value
														.replace(/</g, '&lt;')
														.replace(/>/g, '&gt;')
														.replace(/"/g, '&quot;')
														.replace(/'/g, '&#039;')
														.replace(/&/g, '&amp;');
												}

												const wpSanitizeAvailable = wp && wp.sanitize && wp.sanitize.stripTagsAndEncodeText;
												const sanitizedUrl = wpSanitizeAvailable ?
													wp.sanitize.stripTagsAndEncodeText(attachment.url || '') :
													fallbackSanitize(attachment.url || '');
												const sanitizedAlt = wpSanitizeAvailable ?
													wp.sanitize.stripTagsAndEncodeText(attachment.alt || '') :
													fallbackSanitize(attachment.alt || '');

												editor.insertContent(`<img src="${sanitizedUrl}" alt="${sanitizedAlt}" />`);
											});

											mediaFrame.open();
										}
									});

									editor.ui.registry.addIcon('font-awesome-logo', '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20"><path fill="currentColor" d="M91.7 96C106.3 86.8 116 70.5 116 52C116 23.3 92.7 0 64 0S12 23.3 12 52c0 16.7 7.8 31.5 20 41l0 3 0 352 0 64 64 0 0-64 373.6 0c14.6 0 26.4-11.8 26.4-26.4c0-3.7-.8-7.3-2.3-10.7L432 272l61.7-138.9c1.5-3.4 2.3-7 2.3-10.7c0-14.6-11.8-26.4-26.4-26.4L91.7 96z"/></svg>')

									editor.ui.registry.addButton('add_icon', {
										icon: 'font-awesome-logo',
										tooltip: text.add_icon,
										onAction: function() {
											const bookmark = editor.selection.getBookmark(2, true);

											fetchIconIndex().then(function(icons) {
												const gridId = 'ms-fa-icon-grid';

												const dialogApi = editor.windowManager.open({
													title: text.add_icon,
													size: 'medium',
													body: {
														type: 'panel',
														items: [
															{ type: 'input', name: 'search', placeholder: text.search_icons },
															{ type: 'htmlpanel', html: `
																<style>
																	.ms-fa-icon-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(44px, 1fr)); gap: 4px; max-height: 260px; overflow-y: auto; padding: 4px 0 !important; }
																	.ms-fa-icon-grid button { display: flex; align-items: center; justify-content: center; height: 40px; border: 1px solid #dcdcde; background: #fff; border-radius: 3px; cursor: pointer; font-size: 16px; padding: 0; }
																	.ms-fa-icon-grid button:hover, .ms-fa-icon-grid button:focus { border-color: #0073aa; background: #f0f6fc; }
																	.ms-fa-icon-grid button i, .ms-fa-search-clear i { font-family: "Font Awesome 6 Free" !important; font-weight: 900 !important; font-style: normal !important; }
																	.ms-fa-icon-empty { grid-column: 1 / -1; padding: 12px 4px !important; color: #646970 !important; }
																	.tox-dialog-wrap .tox-form__group:first-child { position: relative !important; }
																	.tox-dialog-wrap .tox-form__group:first-child .tox-textfield { padding-right: 28px; }
																	.ms-fa-search-clear { display: none; position: absolute !important; top: 50%; right: 6px; transform: translateY(-50%); padding: 4px; margin: 0; border: 0; background: transparent; cursor: pointer; color: #787c82; }
																	.ms-fa-search-clear:hover, .ms-fa-search-clear:focus { color: #2271b1; }
																	.ms-fa-search-clear.ms-is-visible { display: flex; align-items: center; justify-content: center; }
																</style>
																<div id="${gridId}" class="ms-fa-icon-grid"></div>
															` }
														]
													},
													initialData: {},
													buttons: [
														{ type: 'cancel', text: text.close }
													]
												});

												const insertIcon = function(unicode) {
													editor.focus();
													editor.selection.moveToBookmark(bookmark);

													const tempId = 'ms-fa-icon-' + unicode + '-' + Math.floor(Math.random() * 1e9);
													editor.insertContent(`<i id="${tempId}" class="fa-solid" aria-hidden="true">&#x${unicode};</i>`);

													// Move the caret after the inserted icon, otherwise TinyMCE leaves it
													// inside the tag and any text typed next ends up inside the <i>
													const insertedNode = editor.dom.get(tempId);
													if (insertedNode) {
														insertedNode.removeAttribute('id');
														editor.selection.select(insertedNode);
														editor.selection.collapse(false);
													}

													dialogApi.close();
												}

												const renderGrid = function(term) {
													const container = document.getElementById(gridId);
													if (!container) {
														return;
													}

													const filtered = term
														? icons.filter(function(icon) {
															return icon.n.indexOf(term) !== -1 ||
																icon.l.toLowerCase().indexOf(term) !== -1 ||
																icon.t.some(function(iconTerm) { return iconTerm.toLowerCase().indexOf(term) !== -1; });
														}).slice(0, 120)
														: POPULAR_ICONS.map(function(name) {
															return icons.find(function(icon) { return icon.n === name; });
														}).filter(Boolean);

													if (!filtered.length) {
														container.innerHTML = `<div class="ms-fa-icon-empty">${text.no_icons_found}</div>`;
														return;
													}

													container.innerHTML = filtered.map(function(icon) {
														return `<button type="button" title="${icon.l}" data-unicode="${icon.u}"><i class="fa-solid fa-${icon.n}" aria-hidden="true"></i></button>`;
													}).join('');

													container.querySelectorAll('button[data-unicode]').forEach(function(btn) {
														btn.addEventListener('click', function() {
															insertIcon(btn.getAttribute('data-unicode'));
														});
													});
												}

												setTimeout(function() {
													renderGrid('');

													// TinyMCE doesn't expose the dialog's own root element via the windowManager
													// API, so this reaches into its internal (undocumented) DOM/class structure.
													// Scope to the most recently opened dialog wrap rather than the first match
													// in the document, in case another TinyMCE dialog is already open elsewhere.
													const dialogWraps = document.querySelectorAll('.tox-dialog-wrap');
													const dialogWrap = dialogWraps[dialogWraps.length - 1];
													const searchInput = dialogWrap ? dialogWrap.querySelector('.tox-textfield') : null;
													if (searchInput) {
														const searchGroup = searchInput.closest('.tox-form__group');
														const clearButton = document.createElement('button');
														clearButton.type = 'button';
														clearButton.className = 'ms-fa-search-clear';
														clearButton.setAttribute('aria-label', text.clear_search);
														clearButton.innerHTML = '<i class="fa-solid" aria-hidden="true">&#xf00d;</i>';
														if (searchGroup) {
															searchGroup.appendChild(clearButton);
														}

														const toggleClearButton = function() {
															clearButton.classList.toggle('ms-is-visible', !!searchInput.value);
														}

														searchInput.addEventListener('input', function() {
															renderGrid(searchInput.value.trim().toLowerCase());
															toggleClearButton();
														});

														clearButton.addEventListener('click', function() {
															searchInput.value = '';
															searchInput.focus();
															renderGrid('');
															toggleClearButton();
														});
													}
												}, 50);
											});
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
				console.log('TinyMCE is not defined or disabled in MetaSlider Slideshow settings!');
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
