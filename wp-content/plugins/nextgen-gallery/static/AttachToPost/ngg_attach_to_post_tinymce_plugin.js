// Self-executing function to create and register the TinyMCE plugin
(function (siteurl, $) {
	window.id = 'wordpress-post-page';

	// Shows why the picker's result was refused, above the picker iframe and
	// inside the dialog that hosts it, so a refused insert is not a dead button.
	function show_dialog_notice(frame, message) {
		var host = frame && frame.parentNode;
		if (!host) {
			return;
		}
		var notice = host.querySelector('.ngg-atp-refused-notice');
		if (!notice) {
			notice = document.createElement('div');
			notice.className = 'notice notice-error ngg-atp-refused-notice';
			notice.setAttribute('role', 'alert');
			// Not positioned, so the host's own close button stays on top of it.
			notice.style.cssText =
				'margin:0;padding:8px 48px 8px 12px;background:#fff;border-left:4px solid #d63638;';
			notice.appendChild(document.createElement('p'));
			host.insertBefore(notice, frame);
		}
		notice.firstChild.textContent = message;
	}

	tinyMCE.addI18n('en.ngg_attach_to_post', {
		title: 'Attach NextGEN Gallery to Post'
	});

	// Create the plugin. We'll register it afterwards
	tinymce.create('tinymce.plugins.NextGEN_AttachToPost', {
		/**
		 * The WordPress Site URL
		 */
		siteurl: siteurl,

		/**
		 * Returns metadata about this plugin
		 */
		getInfo: function () {
			return {
				longname: 'NextGEN Gallery',
				author: 'Imagely',
				authorurl: 'https://www.imagely.com',
				infourl: 'https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/',
				version: '0.1'
			};
		},

		/**
		 * Initializes the plugin, this will be executed after the plugin has been created.
		 */
		init: function (editor, plugin_url) {
			var self = this;

			// Cross-origin fallback: accept the dialog's postMessage result, but only
			// from our own dialog iframe.
			if (!window.ngg_atp_message_bridge) {
				window.ngg_atp_message_bridge = true;
				// The picker posts `insert` and then `close` unconditionally
				// (display_tab.js clicked()), so refusing the insert is not enough on
				// its own: the paired `close` would still shut the dialog, take the
				// notice with it, and the failure would read as success. Remember the
				// refusal briefly and let that one `close` through to nothing, as the
				// block receiver (LegacyModal.tsx) does. The dialog's own close button
				// does not go through postMessage, so it always works.
				var DROPPED_INSERT_WINDOW_MS = 2000;
				var dropped_insert_at = 0;
				window.addEventListener('message', function (ev) {
					var data = ev && ev.data;
					if (!data || typeof data.ngg_attach_to_post === 'undefined') {
						return;
					}
					// The dialog can be hosted by the TinyMCE window manager
					// (#ngg_attach_to_post_dialog_ifr) or by Thickbox (#TB_iframeContent).
					var frame =
						document.getElementById('ngg_attach_to_post_dialog_ifr') ||
						document.getElementById('TB_iframeContent');
					if (!frame || ev.source !== frame.contentWindow) {
						console.error(
							'NextGEN: attach-to-post message rejected (' +
								(frame ? 'source mismatch' : 'dialog iframe not found') +
								').'
						);
						return;
					}

					var settings = window.ngg_tinymce_plugin || {};
					var i18n = settings.i18n || {};

					// `#TB_iframeContent` is Thickbox's global id, so any plugin's Thickbox
					// dialog owns it. Only treat it as ours when it points at the picker.
					var atp_slug = settings.atp_slug || 'nextgen-attach_to_post';
					if (String(frame.getAttribute('src') || '').indexOf(atp_slug) < 0) {
						console.error(
							'NextGEN: attach-to-post message rejected (dialog iframe is not the gallery picker).'
						);
						return;
					}

					// `ev.source` alone is not an origin check: `contentWindow` identifies
					// the browsing context and keeps that identity across navigations, so
					// any document that ends up in this frame would satisfy it. Pin the
					// origin to the picker's own URL (`admin_url()`, localized by
					// ATPManager), not to whatever the frame's `src` says. An opaque origin
					// ("null", from a CSP-sandboxed dialog) can never match and is rejected
					// rather than trusted -- it is indistinguishable from a sandboxed
					// attacker frame -- but the user is told, instead of the dialog just
					// doing nothing.
					var expected_origin = window.location.origin;
					try {
						if (settings.igw_url) {
							expected_origin = new URL(settings.igw_url, document.baseURI).origin;
						}
					} catch (e) {
						// Keep this document's own origin as the expected one.
					}

					if (ev.origin !== expected_origin) {
						console.error('NextGEN: attach-to-post message rejected (origin mismatch).');
						show_dialog_notice(
							frame,
							i18n.unverified ||
								'The gallery picker sent a response that could not be verified, so nothing was inserted. Reload the editor and try again, or insert the gallery with a shortcode.'
						);
						return;
					}

					var inserted = true;
					if (data.ngg_attach_to_post === 'insert') {
						// Same shape check the block receiver applies. This path also feeds
						// the shortcode through `_.unescape()` in the PostProcess handler
						// below, so reject anything that would decode into markup there too.
						var shortcode = typeof data.shortcode === 'string' ? data.shortcode.trim() : '';
						if (
							!/^\[ngg[\w-]*(\s[^<>]*)?\]$/.test(shortcode) ||
							/[<>]/.test(_.unescape(shortcode))
						) {
							dropped_insert_at = Date.now();
							console.error(
								'NextGEN: attach-to-post insert dropped, unexpected shortcode payload.'
							);
							show_dialog_notice(
								frame,
								i18n.unexpected ||
									'The gallery picker returned an unexpected result, so nothing was inserted. Select the gallery again, or insert it with a shortcode.'
							);
							return;
						}
						data.shortcode = shortcode;

						var ed = tinymce.activeEditor;
						var textarea = document.getElementById('content');
						if (ed && !ed.isHidden()) {
							var node = ed.selection.getNode();
							if (data.ref && node && node.outerHTML.indexOf(data.ref) >= 0) {
								$(node).attr(
									'data-shortcode',
									data.shortcode.substring(1, data.shortcode.length - 1)
								);
							} else {
								ed.execCommand('mceInsertContent', false, data.shortcode);
							}
							ed.selection.collapse(false);
						} else if (textarea) {
							var start = textarea.selectionStart || 0;
							var end = textarea.selectionEnd || 0;
							textarea.value =
								textarea.value.substring(0, start) + data.shortcode + textarea.value.substring(end);
						} else {
							inserted = false;
							dropped_insert_at = Date.now();
							console.error('NextGEN: attach-to-post insert dropped, no editor target.');
							show_dialog_notice(
								frame,
								i18n.no_target ||
									'There is no editor to insert the gallery into, so nothing was inserted.'
							);
						}
					}

					if (data.ngg_attach_to_post === 'insert' && inserted) {
						dropped_insert_at = 0;
					} else if (
						data.ngg_attach_to_post === 'close' &&
						dropped_insert_at &&
						Date.now() - dropped_insert_at < DROPPED_INSERT_WINDOW_MS
					) {
						dropped_insert_at = 0;
						inserted = false;
					}

					// Leave the dialog open when an insert could not be performed, so the
					// selection is not silently lost.
					if (inserted) {
						// Close via the host that actually opened the dialog: Thickbox sets
						// #TB_iframeContent, everything else is the TinyMCE window manager.
						if (frame.id === 'TB_iframeContent') {
							if (typeof tb_remove === 'function') {
								tb_remove();
							}
						} else if (
							window.tinymce &&
							tinymce.activeEditor &&
							tinymce.activeEditor.windowManager
						) {
							tinymce.activeEditor.windowManager.close();
						}
						$('html,body').css('overflow', 'auto');
					}
				});
			}

			// TinyMCE 4s events are a bit weird, but this lets us listen to the window-manager close event
			editor.windowManager.nggOldOpen = editor.windowManager.open;
			editor.windowManager.open = function (one, two) {
				var modal = editor.windowManager.nggOldOpen(one, two);
				modal.on('close', self.wm_close_event);
				// TinyMCE 4's window manager does not give its iframe an id, so give our
				// dialog's iframe the id the message bridge looks for.
				if (one && one.id === 'ngg_attach_to_post_dialog') {
					var body = document.getElementById('ngg_attach_to_post_dialog-body');
					var dialog_frame = body ? body.querySelector('iframe') : null;
					if (dialog_frame && !dialog_frame.id) {
						dialog_frame.id = 'ngg_attach_to_post_dialog_ifr';
					}
				}
				return modal;
			};

			// Register a new TinyMCE command
			editor.addCommand('ngg_attach_to_post', this.render_attach_to_post_interface, {
				editor: editor,
				plugin: editor.plugins.NextGEN_AttachToPost
			});

			// Add a button to trigger the above command
			editor.addButton('NextGEN_AttachToPost', {
				title: 'ngg_attach_to_post.title',
				cmd: 'ngg_attach_to_post',
				image: plugin_url + '/igw_button.png'
			});

			/**
			 * Listen for click events to our placeholder
			 */
			editor.on('mouseup touchend', function (e) {
				tinymce.extend(self, {
					editor: editor,
					plugin: editor.plugins.NextGEN_AttachToPost
				});

				// Support for IGW placeholder images. NGG <= 2.1.50
				if (e.target.tagName === 'IMG') {
					if (self.get_class_name(e.target).indexOf('ngg_displayed_gallery') >= 0) {
						editor.dom.events.cancel(e);
						var id = e.target.src.match(/\d+$/);
						if (id) id = id.pop();
						self.render_attach_to_post_interface({
							key: 'id',
							val: id
						});
					}
				}

				// Support for IGW Visual Shortcodes. NGG >= 2.1.50.1
				else {
					var $target = $(e.target);

					if ($target.hasClass('nggPlaceholderButton')) {
						// Remove button
						if ($target.hasClass('nggIgwRemove')) {
							var $placeholder = $target.parents('.nggPlaceholder');
							var shortcode = $placeholder[0].getAttribute('data-shortcode');
							editor.fire('ngg-removed', {shortcode: shortcode});
							$placeholder.remove();
						}

						// Edit button
						else {
							// Do not use jQuery's .data() here: it will use cached data
							window.igw_shortcode = $(e.target)
								.parents('.nggPlaceholder')[0]
								.getAttribute('data-shortcode');

							self.render_attach_to_post_interface({
								key: 'shortcode',
								val: Base64.encode(window.igw_shortcode),
								ref: $(e.target).parents('.nggPlaceholder').attr('id')
							});
						}
					}
				}
			});

			/**
			 * Find each shortcode and replace it with the placeholder, rendered using an underscore template
			 * in templates/tinymce_placeholder.php
			 */
			editor.on('BeforeSetContent', function (event) {
				handle_shortcode(event, '[ngg_images ');
				handle_shortcode(event, '[ngg ');
				handle_shortcode(event, '[imagely ');
			});

			/**
			 * Substitutes the IGW placeholders with the corresponding shortcode
			 */
			editor.on('PostProcess', function (event) {
				var $content = $('<div/>').append(event.content);
				$content
					.find('.nggPlaceholder')
					.toArray()
					.forEach(function (placeholder) {
						var $placeholder = $(placeholder);
						var shortcode = $placeholder.data('shortcode');
						shortcode = '[' + _.unescape(shortcode) + ']';
						$placeholder.replaceWith(shortcode);
					});
				event.content = $content[0].innerHTML;
			});

			function handle_shortcode(event, shortcode_opening_tag) {
				while (event.content.indexOf(shortcode_opening_tag) >= 0) {
					var start_of_shortcode = event.content.indexOf(shortcode_opening_tag);
					var index = start_of_shortcode + shortcode_opening_tag.length;
					var found_attribute_assignment = false;
					var current_attribute_enclosure = null;
					var last_found_char = false;
					var found_closing_bracket = false;
					var content_length = event.content.length;
					while (index < content_length) {
						var char = event.content[index];
						if (found_attribute_assignment) {
							// Inside an attribute value, so only its own matching quote ends it.
							// Testing the opening rule again here would miss a closing single
							// quote, whose preceding character is the value rather than "=".
							if (char == current_attribute_enclosure) {
								found_attribute_assignment = false;
								current_attribute_enclosure = null;
							}
						} else if (char == '"' || (char == "'" && last_found_char == '=')) {
							found_attribute_assignment = true;
							current_attribute_enclosure = char;
						} else if (char == ']') {
							// A ] outside of an attribute value closes the shortcode.
							found_closing_bracket = true;
							break;
						}

						last_found_char = char;
						index++;
					}

					// An unterminated shortcode has no end to wrap up to. Leave the content
					// alone rather than absorbing everything that follows into a placeholder.
					if (!found_closing_bracket) {
						break;
					}

					// Replace the shortcode with a placeholder
					var match = event.content.substring(start_of_shortcode, ++index);
					var shortcode = match.substring(1, match.length - 1);
					shortcode = shortcode.replace('[', '&#91;');
					shortcode = shortcode.replace(']', '&#93;');

					var template = _.template($('#ngg-igw-placeholder').html());
					event.content = event.content.replace(
						match,
						template(
							$.extend(ngg_igw_i18n, {
								shortcode: shortcode,
								ref: _.now()
							})
						)
					);
				}
			}
		},

		get_class_name: function (node) {
			var class_name = node.getAttribute('class') ? node.getAttribute('class') : node.className;
			if (class_name) {
				return class_name;
			} else {
				return '';
			}
		},

		wm_close_event: function (e) {
			if (e && e.target && e.target._id && e.target._id == 'ngg_attach_to_post_dialog') {
				// Restore scrolling for the main content window when the attach to post interface is closed
				$('html,body').css('overflow', 'auto');
				tinyMCE.activeEditor.selection.select(tinyMCE.activeEditor.dom.select('p')[0]);
				tinyMCE.activeEditor.selection.collapse(0);
			}
		},

		/**
		 * Renders the attach to post interface
		 */
		render_attach_to_post_interface: function (params) {
			// Determine the attach to post url
			var attach_to_post_url = window.igw.url;
			if (typeof params != 'undefined') {
				attach_to_post_url += '&' + params.key + '=' + encodeURIComponent(params.val);
				if (typeof params['ref'] != 'undefined') {
					attach_to_post_url += '&ref=' + encodeURIComponent(params.ref);
				}
			}
			attach_to_post_url += '&editor=' + this.editor.id;
			attach_to_post_url += '&nonce=' + window.ngg_tinymce_plugin.nonce;

			var win = window;
			while (win.parent != null && win.parent != win) {
				win = win.parent;
			}

			win = $(win);
			var winWidth = win.width();
			var winHeight = win.height();
			var popupWidth = 1600;
			var popupHeight = 1200;
			var minWidth = 800;
			var minHeight = 600;
			var maxWidth = winWidth - winWidth * 0.05;
			var maxHeight = winHeight - winHeight * 0.1;

			if (maxWidth < minWidth) {
				maxWidth = winWidth - 20;
			}
			if (maxHeight < minHeight) {
				maxHeight = winHeight - 40;
			}
			if (popupWidth > maxWidth) {
				popupWidth = maxWidth;
			}
			if (popupHeight > maxHeight) {
				popupHeight = maxHeight;
			}

			// for mobile devices: dismiss the keyboard by blurring any input with focus
			document.activeElement.blur();
			Array.prototype.forEach.call(document.querySelectorAll('input, textarea'), function (it) {
				it.blur();
			});

			// Open a window, occupying 90% of the screen real estate
			this.editor.windowManager.open({
				url: attach_to_post_url,
				id: 'ngg_attach_to_post_dialog',
				width: popupWidth,
				height: popupHeight,
				title: 'NextGEN Gallery - Attach To Post'
			});

			// Ensure that the window cannot be scrolled - XXX actually allow scrolling in the main window and disable it for the inner-windows/frames/elements as to create a single scrollbar
			$('html,body').css('overflow', 'hidden');
			$('#ngg_attach_to_post_dialog_ifr').css('overflow-y', 'auto');
			$('#ngg_attach_to_post_dialog_ifr').css('overflow-x', 'hidden');
		}
	});

	// Register plugin
	tinymce.PluginManager.add('NextGEN_AttachToPost', tinymce.plugins.NextGEN_AttachToPost);
})(photocrati_ajax.wp_site_url, jQuery);
