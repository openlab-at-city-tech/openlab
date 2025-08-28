/* global document, Backbone */
/* jshint unused:false, newcap: false */
/**
* Slide Model
*/
var SoliloquySlide = Backbone.Model.extend({

	/**
	* Defaults
	* As we always populate this model with existing data, we
	* leave these blank to just show how this model is structured.
	*/
	defaults: {
		'id': '',
		'title': '',
		'caption': '',
		'alt': '',
		'link': '',
		'type': '',

	},

});

/**
* Images Collection
* - Comprises of all slides in an Soliloquy Slider
* - Each image is represented by an SoliloquySlides Model
*/
var SoliloquySlides = new Backbone.Collection();

/**
* Modal Window
*/
var SoliloquyModalWindow = new wp.media.view.Modal({
	controller: {
		trigger: function () {

		}
	}
});

/**
* View
*/
var SoliloquyView = wp.Backbone.View.extend({

	/**
	* The Tag Name and Tag's Class(es)
	*/
	id: 'soliloquy-meta-edit',
	tagName: 'div',
	className: 'edit-attachment-frame mode-select hide-menu hide-router',

	/**
	* Template
	* - The template to load inside the above tagName element
	*/
	template: wp.template('soliloquy-meta-editor'),

	/**
	* Events
	* - Functions to call when specific events occur
	*/
	events: {
		'click .edit-media-header .left': 'loadPreviousItem',
		'click .edit-media-header .right': 'loadNextItem',

		'keyup input': 'updateItem',
		'keyup textarea': 'updateItem',
		'change input': 'updateItem',
		'change textarea': 'updateItem',
		'keyup .CodeMirror': 'updateCode',
		'blur textarea': 'updateItem',

		'change select': 'updateItem',

		'click a.soliloquy-meta-submit': 'saveItem',

		'keyup input#link-search': 'searchLinks',
		'click div.query-results li': 'insertLink',

		'click a.soliloquy-thumbnail': 'insertThumb',
		'click a.soliloquy-thumbnail-delete': 'removeThumb',

		'click button.media-file': 'insertMediaFileLink',
		'click button.attachment-page': 'insertAttachmentPageLink',
	},

	/**
	* Initialize
	*
	* @param object model   SoliloquyImage Backbone Model
	*/
	initialize: function (args) {

		// Set some flags
		this.is_loading = false;
		this.collection = args.collection;
		this.child_views = args.child_views;
		this.attachment_id = args.attachment_id;
		this.attachment_index = 0;
		this.search_timer = '';

		// Get the model from the collection
		var count = 0;
		this.collection.each(function (model) {

			// If this model's id matches the attachment id, this is the model we want, also make sure both are int
			if (String(model.get('id')) == String(this.attachment_id)) {
				this.model = model;
				this.attachment_index = count;

				return false;
			}

			// Increment the index count
			count++;
		}, this);

	},
	updateCode: function (e) {

		$model = this.model;

		$textarea = this.$el.find('.soliloquy-html-slide-code');

		$model.set('code', this.editor.getValue(), { silent: true });

		$textarea.text();

	},
	insertThumb: function (e) {

		$model = this.model;

		e.preventDefault();

		// Get input field class name
		var fieldClassName = this.$el.data('field');

		var soliloquy_media_frame = wp.media.frames.soliloquy_media_frame = wp.media({
			className: 'media-frame soliloquy-media-frame',
			frame: 'select',
			multiple: false,
			title: soliloquy_metabox_local.videoframe,
			library: {
				type: 'image'
			},
			button: {
				text: soliloquy_metabox_local.videouse
			}
		});

		soliloquy_media_frame.on('select', function () {

			// Grab our attachment selection and construct a JSON representation of the model.
			var thumbnail = soliloquy_media_frame.state().get('selection').first().toJSON();

			$model.set('src', thumbnail.url, { silent: true });
			jQuery('div.thumbnail > img', $parent.find('.media-frame-content')).attr('src', thumbnail.url);

		});

		// Now that everything has been set, let's open up the frame.
		soliloquy_media_frame.open();
	},

	removeThumb: function (e) {

		e.preventDefault();

		$model = this.model;
		$parent = this.$el.parent();

		jQuery('div.thumbnail > img', $parent.find('.media-frame-content')).attr('src', '');

		$model.set('src', '', { silent: true });

	},
	/**
	* Render
	* - Binds the model to the view, so we populate the view's fields and data
	*/
	render: function () {

		// Get HTML
		this.$el.html(this.template(this.model.attributes));
		// If any child views exist, render them now
		if (this.child_views.length > 0) {
			this.child_views.forEach(function (view) {
				// Init with model
				var child_view = new view({
					model: this.model
				});

				// Render view within our main view
				this.$el.find('div.addons').append(child_view.render().el);

			}, this);
		}

		// Set caption
		this.$el.find('textarea[name=caption]').val(this.model.get('caption'));

		// Init QuickTags on the caption editor
		// Delay is required for the first load for some reason
		setTimeout(function () {
			quicktags({
				id: 'caption',
				buttons: 'strong,em,link,ul,ol,li,close'
			});
			QTags._buttonsInit();
		}, 500);

		// Init Link Searching
		wpLink.init;

		// Enable / disable the buttons depending on the index
		if (this.attachment_index === 0) {
			// Disable left button
			this.$el.find('button.left').addClass('disabled');
		}
		if (this.attachment_index == (this.collection.length - 1)) {
			// Disable right button
			this.$el.find('button.right').addClass('disabled');
		}

		textarea = this.$el.find('.soliloquy-html-slide-code');

		if (textarea.length) {

			this.editor = CodeMirror.fromTextArea(textarea[0], {
				enterMode: 'keep',
				indentUnit: 4,
				electricChars: false,
				lineNumbers: true,
				lineWrapping: true,
				matchBrackets: true,
				mode: 'php',
				smartIndent: false,
				tabMode: 'shift',
				theme: 'ttcn'
			});

		}

		this.$el.trigger('soliloquyRenderMeta');

		// Return
		return this;

	},

	/**
	* Tells the view we're loading by displaying a spinner
	*/
	loading: function () {

		// Set a flag so we know we're loading data
		this.is_loading = true;

		// Show the spinner
		this.$el.find('.spinner').css('visibility', 'visible');

	},

	/**
	* Hides the loading spinner
	*/
	loaded: function (response) {

		// Set a flag so we know we're not loading anything now
		this.is_loading = false;

		// Hide the spinner
		this.$el.find('.spinner').css('visibility', 'hidden');

		// Display the error message, if it's provided
		if (typeof response !== 'undefined') {
			alert(response);
		}

	},

	/**
	* Load the previous model in the collection
	*/
	loadPreviousItem: function () {

		// Decrement the index
		this.attachment_index--;

		// Get the model at the new index from the collection
		this.model = this.collection.at(this.attachment_index);

		// Update the attachment_id
		this.attachment_id = this.model.get('id');

		// Re-render the view
		this.render();

	},

	/**
	* Load the next model in the collection
	*/
	loadNextItem: function () {

		// Increment the index
		this.attachment_index++;

		// Get the model at the new index from the collection
		this.model = this.collection.at(this.attachment_index);

		// Update the attachment_id
		this.attachment_id = this.model.get('id');

		// Re-render the view
		this.render();


	},

	/**
	* Updates the model based on the changed view data
	*/
	updateItem: function (event) {

		// Check if the target has a name. If not, it's not a model value we want to store
		if (event.target.name == '') {
			return;
		}

		// Update the model's value, depending on the input type
		if (event.target.type == 'checkbox') {
			value = (event.target.checked ? 1 : 0);
		} else {
			value = event.target.value;
		}

		// Update the model
		this.model.set(event.target.name, value);

	},

	/**
	* Saves the image metadata
	*/
	saveItem: function (event) {

		event.preventDefault();

		// Tell the View we're loading
		this.trigger('loading');

		// Make an AJAX request to save the image metadata
		wp.media.ajax('soliloquy_save_meta', {
			context: this,
			data: {
				nonce: soliloquy_metabox_local.save_nonce,
				post_id: soliloquy_metabox_local.id,
				attach_id: this.model.get('id'),
				meta: this.model.attributes,
			},
			success: function (response) {

				// Tell the view we've finished successfully
				this.trigger('loaded loaded:success');

				// Assign the model's JSON string back to the underlying item
				var item = JSON.stringify(this.model.attributes);
				jQuery('ul#soliloquy-output li#' + this.model.get('id')).attr('data-soliloquy-image-model', item);
				// Show the user the 'saved' notice for 1.5 seconds
				var saved = this.$el.find('.saved');
				saved.fadeIn();
				setTimeout(function () {
					saved.fadeOut();
				}, 1500);

			},
			error: function (error_message) {

				// Tell wp.media we've finished, but there was an error
				this.trigger('loaded loaded:error', error_message);

			}
		});

	},

	/**
	* Searches Links
	*/
	searchLinks: function (event) {


	},

	/**
	* Inserts the clicked link into the URL field
	*/
	insertLink: function (event) {



	},

	/**
	* Inserts the direct media link for the Media Library item
	*
	* The button triggering this event is only displayed if we are editing a
	* Media Library item, so there's no need to perform further checks
	*/
	insertMediaFileLink: function (event) {

		// Tell the View we're loading
		this.trigger('loading');

		// Make an AJAX request to get the media link
		wp.media.ajax('soliloquy_get_attachment_links', {
			context: this,
			data: {
				nonce: soliloquy_metabox_local.save_nonce,
				attachment_id: this.model.get('id'),
			},
			success: function (response) {

				// Update model
				this.model.set('link', response.media_link);

				// Tell the view we've finished successfully
				this.trigger('loaded loaded:success');

				// Re-render the view
				this.render();

			},
			error: function (error_message) {

				// Tell wp.media we've finished, but there was an error
				this.trigger('loaded loaded:error', error_message);

			}
		});

	},

	/**
	* Inserts the attachment page link for the Media Library item
	*
	* The button triggering this event is only displayed if we are editing a
	* Media Library item, so there's no need to perform further checks
	*/
	insertAttachmentPageLink: function (event) {

		// Tell the View we're loading
		this.trigger('loading');

		// Make an AJAX request to get the media link
		wp.media.ajax('soliloquy_get_attachment_links', {
			context: this,
			data: {
				nonce: soliloquy_metabox_local.save_nonce,
				attachment_id: this.model.get('id'),
			},
			success: function (response) {

				// Update model
				this.model.set('link', response.attachment_page);

				// Tell the view we've finished successfully
				this.trigger('loaded loaded:success');

				// Re-render the view
				this.render();

			},
			error: function (error_message) {

				// Tell wp.media we've finished, but there was an error
				this.trigger('loaded loaded:error', error_message);

			}
		});

	}

});

/**
* Sub Views
* - Addons must populate this array with their own Backbone Views, which will be appended
* to the settings region
*/
var SoliloquyChildViews = [];
var SoliloquyContentViews = [];

/**
* DOM
*/
; (function ($) {

	$(document).ready(function () {

		soliloquy_edit = {

			init: function () {

				// Populate the collection
				SoliloquySlidesUpdate();

				// Edit Image
				$('#soliloquy-settings-content').on('click.soliloquyModify', '.soliloquy-modify-slide', function (e) {

					// Prevent default action
					e.preventDefault();
					// Get the selected attachment
					var attachment_id = $(this).parent().data('soliloquy-slide');

					// Pass the collection of images for this gallery to the modal view, as well
					// as the selected attachment
					SoliloquyModalWindow.content(new SoliloquyView({
						collection: SoliloquySlides,
						child_views: SoliloquyChildViews,
						attachment_id: attachment_id,
					}));

					// Open the modal window
					SoliloquyModalWindow.open();

					$(document).trigger('soliloquyEditOpen');

					$('.CodeMirror').each(function (i, el) {
						el.CodeMirror.refresh();
					});



				});

			}
		};

		soliloquy_edit.init();

	});

	$(document).on('soliloquyUploaded', function () {

		soliloquy_edit.init();

	});

})(jQuery);

/**
* Populates the SoliloquySlides Backbone collection
*
* Called when images are added, deleted or reordered
* Doesn't need to be called when an image is edited, as the model will be updated automatically in the collection
*/
function SoliloquySlidesUpdate(selected) {

	// Clear the collection
	SoliloquySlides.reset();

	var $items = 'ul#soliloquy-output li.soliloquy-slide' + (selected ? '.selected' : '');

	// Iterate through the gallery images in the DOM, adding them to the collection
	jQuery($items).each(function () {
		// Build an SoliloquyImage Backbone Model from the JSON supplied in the element
		var soliloquy_slide = jQuery.parseJSON(jQuery(this).attr('data-soliloquy-image-model'));

		// Add the model to the collection
		SoliloquySlides.add(new SoliloquySlide(soliloquy_slide));

	});

}
