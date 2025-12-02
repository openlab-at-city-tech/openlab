/*! © JetSloth — SPDX-License-Identifier: GPL-2.0-or-later */

var imageChoicesAdmin = imageChoicesAdmin || {};

(function($){

	var GF_MARKUP_VERSION = 2;

	imageChoicesAdmin.extend = (...arguments) => {

		// Variables
		let extended = {};
		let deep = false;
		let i = 0;

		// Check if a deep merge
		if (typeof (arguments[0]) === 'boolean') {
			deep = arguments[0];
			i++;
		}

		// Merge the object into the extended object
		let merge = function (obj) {
			for (let prop in obj) {
				if (obj.hasOwnProperty(prop)) {
					if (deep && Object.prototype.toString.call(obj[prop]) === '[object Object]') {
						// If we're doing a deep merge and the property is an object
						extended[prop] = imageChoicesAdmin.extend(true, extended[prop], obj[prop]);
					} else {
						// Otherwise, do a regular merge
						extended[prop] = obj[prop];
					}
				}
			}
		};

		// Loop through each object and conduct a merge
		for (; i < arguments.length; i++) {
			merge(arguments[i]);
		}

		return extended;

	};

	imageChoicesAdmin.__dialogDefaults = {
		dialogType: 'alert',
		cancelText: 'Cancel',
		confirmText: 'Ok',
		allowCancel: true,
		promptLabel: '',
		promptPlaceholder: '',
		promptValue: '',
		title: '',
		content: '',
		image: false,
		onConfirm: null,
		onCancel: null,
	};

	imageChoicesAdmin.closeDialog = (dialogId) => {
		let dialog = document.getElementById(dialogId);
		if ( dialog ) {
			dialog.classList.remove('ic-active');
			setTimeout(() => {
				dialog.remove();
			}, 300);
		}
		let dialogBg = document.querySelector(`.ic-dialog-bg[data-for="${dialogId}"]`);
		if ( dialogBg ) {
			dialogBg.classList.remove('ic-active');
			setTimeout(() => {
				dialogBg.remove();
			}, 300);
		}
	};

	imageChoicesAdmin.dialog = (options) => {
		let _options = imageChoicesAdmin.__dialogDefaults;
		let allOptions = (typeof options === "object") ? imageChoicesAdmin.extend(true, _options, options) : _options;
		if ( allOptions.dialogType !== 'alert' && allOptions.dialogType !== 'confirm' && allOptions.dialogType !== 'prompt' ) {
			allOptions.dialogType = imageChoicesAdmin.__dialogDefaults.dialogType;
		}
		if ( allOptions.dialogType === 'alert' ) {
			allOptions.allowCancel = false;
		}

		let dialogCount = document.querySelectorAll('.ic-dialog').length;
		let dialogId = `ic_dialog_${dialogCount}`;

		let baseZindex = 999900;

		let dialogBg = document.createElement('div');
		dialogBg.classList.add('ic-dialog-bg');
		dialogBg.dataset.for = dialogId;
		dialogBg.style.zIndex = `${baseZindex + dialogCount}`;

		let cancelButton = ( allOptions.allowCancel ) ? `<button type="button" class="ic-btn ic-btn-secondary ic-dialog-cancel">${allOptions.cancelText}</button>` : ``;

		let promptLabel = ( allOptions.promptLabel ) ? `<label for="${dialogId}_input" class="ic-prompt-input-label">${allOptions.promptLabel}</label>` : ``;
		let promptInput = ( allOptions.dialogType === 'prompt' ) ? `<div class="ic-prompt-input-wrap">${promptLabel}<input type="text" id="${dialogId}_input" class=ic-prompt-input" value="${allOptions.promptValue}" placeholder="${allOptions.promptPlaceholder}"></div>` : ``;

		let dialog = document.createElement('div');
		dialog.id = dialogId;
		dialog.classList.add('ic-dialog', `ic-dialog-${allOptions.dialogType}`);
		dialog.innerHTML = `<div class="ic-dialog-header">${allOptions.title}</div><div class="ic-dialog-body">${allOptions.content}${promptInput}</div><div class="ic-dialog-footer">${cancelButton}<button type="button" class="ic-btn ic-dialog-confirm">${allOptions.confirmText}</button></div>`;
		dialog.style.zIndex = `${baseZindex + dialogCount + 1}`;

		document.body.append(dialogBg);
		document.body.append(dialog);
		setTimeout(() => {
			dialogBg.classList.add('ic-active');
			dialog.classList.add('ic-active');
		}, 10);

		dialog.querySelector('.ic-dialog-confirm').addEventListener('click', (e) => {
			e.preventDefault();
			let thisId =  e.currentTarget.closest('.ic-dialog').id;
			if ( typeof allOptions.onConfirm === 'function' ) {
				let val = ( allOptions.dialogType === 'prompt' ) ? document.getElementById(`${thisId}_input`).value : undefined;
				allOptions.onConfirm(val);
			}
			imageChoicesAdmin.closeDialog( thisId );
		});

		if ( allOptions.allowCancel ) {
			dialog.querySelector('.ic-dialog-cancel').addEventListener('click', (e) => {
				e.preventDefault();
				if ( typeof allOptions.onCancel === 'function' ) {
					allOptions.onCancel();
				}
				imageChoicesAdmin.closeDialog( e.currentTarget.closest('.ic-dialog').id );
			});
		}
	};

	imageChoicesAdmin.prompt = (options) => {
		let _options = imageChoicesAdmin.__dialogDefaults;
		if (typeof options !== "object") {
			options = {};
		}
		options.dialogType = 'prompt';
		let allOptions = (typeof options === "object") ? imageChoicesAdmin.extend(true, _options, options) : _options;
		imageChoicesAdmin.dialog(allOptions);
	};

	imageChoicesAdmin.confirm = (options) => {
		let _options = imageChoicesAdmin.__dialogDefaults;
		if (typeof options !== "object") {
			options = {};
		}
		options.dialogType = 'confirm';
		let allOptions = (typeof options === "object") ? imageChoicesAdmin.extend(true, _options, options) : _options;
		imageChoicesAdmin.dialog(allOptions);
	};

	imageChoicesAdmin.alert = (options) => {
		let _options = imageChoicesAdmin.__dialogDefaults;
		if (typeof options !== "object") {
			options = {};
		}
		options.dialogType = 'alert';
		let allOptions = (typeof options === "object") ? imageChoicesAdmin.extend(true, _options, options) : _options;
		imageChoicesAdmin.dialog(allOptions);
	};


	imageChoicesAdmin.markupDetect = function() {
		if ( typeof window.form !== 'undefined' && window.form.hasOwnProperty('markupVersion') ) {
			GF_MARKUP_VERSION = ( window.form.markupVersion !== '' ) ? parseInt( window.form.markupVersion.toString(), 10 ) : 1;
		}
		return GF_MARKUP_VERSION;
	};

	imageChoicesAdmin.isLegacyMode = function() {
		var useNewFeatures = ( imageChoicesVars.hasOwnProperty('useNewFeatures') && imageChoicesVars.useNewFeatures.toString() === 'true');
		return !useNewFeatures;
	};

	imageChoicesAdmin.formHasImageChoicesFields = function() {
		var hasImageChoicesFields = false;
		$.each(window.form.fields, function(i, field){
			if ( field.hasOwnProperty('imageChoices_enableImages') && field.imageChoices_enableImages ) {
				hasImageChoicesFields = true;
				return false;
			}
		});
		return hasImageChoicesFields;
	};

	imageChoicesAdmin.markup = {
		removeChoicesButton: function(){
			return '<button class="button image-choices-remove-choices-btn" type="button" onclick="imageChoicesAdmin.removeAllChoices();">'+imageChoicesFieldStrings.removeAllChoices+'</button>';
		},
		choicesAdminRow: function(index, image, id){
			var i = (index !== undefined) ? index : 0;
			var img = (image !== undefined) ? image : '';
			var id = (id !== undefined) ? id : '';
			var buttonLabel = '<i class="ic-select-icon"></i>';// GF 2.5+
			return [
				'<button type="button" id="image-choices-option-upload-button-'+i+'" class="button image-choices-option-upload-button" onclick="imageChoicesAdmin.OpenMediaLibrary(this);" title="'+imageChoicesFieldStrings.uploadImage+'">'+buttonLabel+'</button>',
				'<span id="image-choices-option-preview-'+i+'" class="image-choices-option-preview-wrap">',
					'<span class="image-choices-option-preview" style="background-image:url('+img+');"></span>',
					'<a href="javascript:void(0);" class="image-choices-option-image-remove" onclick="imageChoicesAdmin.RemovePreview(this);" title="'+imageChoicesFieldStrings.removeImage+'"><i class="dashicons dashicons-no"></i></a>',
				'</span>',
				'<input type="hidden" id="image-choices-option-image-'+i+'" class="image-choices-option-image" value="'+img+'" />',
				'<input type="hidden" id="image-choices-option-image-id-'+i+'" class="image-choices-option-image-id" value="'+id+'" />'
			].join('');
		},
		choicesPreviewRow: function(index, labelText, image) {
			var i = (index !== undefined) ? index : 0;
			var label = (labelText !== undefined) ? labelText : '';
			var img = (image !== undefined) ? image : '';
			return [
				'<span class="image-choices-choice-image-wrap" style="background-image:url('+img+')">',
					'<img src="'+img+'" alt="" class="image-choices-choice-image" />',
				'</span>',
				'<span class="image-choices-choice-text">'+label+'</span>'
			].join('');
		}
	};

	imageChoicesAdmin.getChoices = function( field ) {
		if ( typeof field === 'undefined' ) {
			field = GetSelectedField();
		}

		var choicesSelector = '[class*="-choice-row"]';

		imageChoicesAdmin.getSettingsElement().find(choicesSelector).each(function(){
			var $row = $(this);
			var i = $row.data('index');
			var img = (field.choices.length && field.choices[i].imageChoices_image !== undefined) ? field.choices[i].imageChoices_image : '';
			var imgID = (field.choices.length && field.choices[i].imageChoices_imageID !== undefined) ? field.choices[i].imageChoices_imageID : '';

			if ( !$row.find('.image-choices-option-image').length ) {
				var $firstInput = $row.find('input[id*="_choice_text"]:first');
				if ( !$firstInput.length ) {
					$firstInput = $row.find('input[id*="-choice-text"]:first');
				}
				if ( $firstInput.length ) {
					$firstInput.before(imageChoicesAdmin.markup.choicesAdminRow(i, img, imgID));
				}
			}

			var $imageInput = $row.find('.image-choices-option-image');
			if ($imageInput.val() !== '') {
				$row.addClass('image-choices-has-image');
			}
		});

		imageChoicesAdmin.updateFieldPreview(field);
	};

	imageChoicesAdmin.RemovePreview = function( btnEl ) {
		if (typeof btnEl === 'undefined') {
			return;
		}

		var $choice = $(btnEl).closest('[class*="-choice-row"]');
		$choice.find('.image-choices-option-image').val('');
		$choice.find('.image-choices-option-image-id').val('');
		$choice.find('.image-choices-option-preview').css('background-image', '');
		$choice.removeClass('image-choices-has-image');

		imageChoicesAdmin.UpdateFieldChoicesObject();

		var field = GetSelectedField();
		imageChoicesAdmin.updateFieldPreview(field);
	};

	imageChoicesAdmin.UpdateFieldChoicesObject = function() {
		var field = GetSelectedField();

		var $fieldSettings = imageChoicesAdmin.getSettingsElement();

		var selector = '[class*="-choice-row"]';

		$fieldSettings.find(selector).each(function(index) {
			var $choice = $(this);
			var image = $choice.find('.image-choices-option-image').val();
			var imageID = $choice.find('.image-choices-option-image-id').val();
			var i = $choice.data("index");
			if (image !== '') {
				$choice.addClass('image-choices-has-image');
				$choice.find('.gf-image-choices-option-preview').css('background-image', 'url('+image+')');
			}
			else {
				$choice.removeClass('image-choices-has-image');
			}
			field.choices[i].imageChoices_image = image;
			field.choices[i].imageChoices_imageID = imageID;
		});
	};

	imageChoicesAdmin.onProductPriceChange = function() {
		setTimeout(function(){
			imageChoicesAdmin.updateFieldPreview( GetSelectedField() );
		}, 10);
	};

	var ttUpdateDelay;

	imageChoicesAdmin.updateFieldPreview = function( field ) {

		var $field = $('#field_'+field.id);
		var productImageField = ( field.type === 'product' && field.inputType === 'singleproduct' );

		var $fieldLabel = $field.find('.gfield_label.gform-field-label');
		var $productImageWrap = $field.find('.ic-product-image-wrap');

		var prop = productImageField ? 'productImage_enabled' : 'imageChoices_enableImages';
		var imagesEnabled = (field.hasOwnProperty(prop) && field[prop] === true);
		var hasProductImage = imageChoicesAdmin.hasProductImage( field );


		if ( !imagesEnabled || !productImageField ) {
			if ( $productImageWrap.length ) {
				$fieldLabel.detach();
				$field.prepend($fieldLabel);
				$productImageWrap.remove();
			}
		}

		if ( productImageField ) {

			if ( !$productImageWrap.length && hasProductImage ) {
				$fieldLabel.detach();
				$productImageWrap = $(`<div class="ic-product-image-wrap"></div>`);
				$productImageWrap.append(`<div class="ic-product-image" style="background-image:url(${field.productImage_image});"><img src="${field.productImage_image}" alt="" class="ic-product-image-element" /></div>`);
				$productImageWrap.append($fieldLabel);
				$productImageWrap.append(`<div class="ic-product-image-price">${field.basePrice}</div>`);
				$field.prepend($productImageWrap);
			}
			else if ( !hasProductImage && $productImageWrap.length ) {
				$fieldLabel.detach();
				$field.prepend($fieldLabel);
				$productImageWrap.remove();
			}

			if ( hasProductImage ) {
				$field.find('.ic-product-image-price').html(field.basePrice);
			}

		}
		else {

			if ( imagesEnabled && field.imageChoices_showLabels === undefined ) {
				imageChoicesAdmin.toggleShowLabels(true);
			}

			imageChoicesAdmin.$fieldChoices( $field ).each(function(i){
				var $choice = $(this);

				if ( !$choice.hasClass('gchoice_total') ) {

					$choice.toggleClass('image-choices-choice', imagesEnabled);

					var $choiceLabel = $choice.find('label');
					var labelText = ($choiceLabel.find('.image-choices-choice-text').length) ? $choiceLabel.find('.image-choices-choice-text').html() : $choiceLabel.html();

					if ( imagesEnabled && field.choices.length > i) {
						var img = (field.choices[i].imageChoices_image !== undefined) ? field.choices[i].imageChoices_image : '';
						$choiceLabel.html(imageChoicesAdmin.markup.choicesPreviewRow(i, labelText, img));
					}
					else if ( !imagesEnabled ) {
						$choiceLabel.html(labelText);
					}

				}
			});

			if ( typeof gfttAdmin !== 'undefined' ) {
				clearTimeout(ttUpdateDelay);
				ttUpdateDelay = setTimeout(function(){
					gfttAdmin.updateChoicesPreview();
				}, 100);
			}

		}


	};

	imageChoicesAdmin.getField = function() {
		var field = GetSelectedField();
		return $('#field_'+field.id);
	};

	imageChoicesAdmin.removeAllChoices = function() {
		imageChoicesAdmin.getSettingsElement().find('.image-choices-option-image-remove').each(function(){
			$(this).trigger('click');
		});

		$settingsWrap.find('.gf_delete_field_choice').each(function(){
			$(this).trigger('click');
		});

		$settingsWrap.find('.field-choice-row input[type="text"], .gquiz-choice-row input[type="text"]').val('');
	};

	imageChoicesAdmin.isColorPickerPluginActive = function() {
		return (window.hasOwnProperty('colorPickerFieldVars') && typeof window.colorPickerFieldVars !== 'undefined');
	};

	imageChoicesAdmin.isImageChoicesEnabled = function( field ) {
		field = field || GetSelectedField();
		return ( imageChoicesAdmin.fieldCanHaveImages(field) && field.imageChoices_enableImages === true );
	};

	imageChoicesAdmin.hasImageChoicesImage = function( field ) {
		field = field || GetSelectedField();
		return ( imageChoicesAdmin.isImageChoicesEnabled(field) && field.hasOwnProperty('imageChoices_image') && field.imageChoices_image !== '' );
	};

	imageChoicesAdmin.isProductImageField = function( field ) {
		field = field || GetSelectedField();
		return ( field.type === "product" && field.inputType === "singleproduct" );
	};

	imageChoicesAdmin.isProductImageEnabled = function( field ) {
		field = field || GetSelectedField();
		return ( imageChoicesAdmin.isProductImageField(field) && field.productImage_enabled === true );
	};

	imageChoicesAdmin.hasProductImage = function( field ) {
		field = field || GetSelectedField();
		return ( imageChoicesAdmin.isProductImageField(field) && field.hasOwnProperty('productImage_image') && field.productImage_image !== '' );
	};

	imageChoicesAdmin.hasProductImageId = function( field ) {
		field = field || GetSelectedField();
		return ( imageChoicesAdmin.isProductImageField(field) && field.hasOwnProperty('productImage_imageId') && field.productImage_imageId );
	};

	imageChoicesAdmin.onProductImageToggleClick = function(toggle) {
		imageChoicesAdmin.toggleEnableImages(toggle.checked, true);
		if ( toggle.checked ) {
			if ( !imageChoicesAdmin.hasProductImage() ) {
				$('#product_image_tab_toggle').click();
				setTimeout(function(){
					$('#product-image-button').click();
				}, 500);
			}
		}
	}

	imageChoicesAdmin.toggleEnableImages = function( enable, forProductImage ) {

		var isForProductImage = ( forProductImage === true );
		var toggleSelector = isForProductImage ? 'input.field_product_image_enabled' : 'input.field_choice_images_enabled';

		var $settings = imageChoicesAdmin.getSettingsElement();
		var $toggle = $settings.find(toggleSelector);

		if ( enable === undefined ) {
			enable = $toggle.is(':checked');
		}

		if ( isForProductImage ) {
			imageChoicesAdmin.onToggleEnableImages(enable, isForProductImage);
			return;
		}

		var $coloPickerToggle = $settings.find('input.field_color_picker_enabled');

		// if user is enabling image choices, and color picker is currently in use on this field, confirm the switch
		if (enable && imageChoicesAdmin.isColorPickerPluginActive() && $coloPickerToggle.length && $coloPickerToggle.is(':checked')) {
			if (window.confirm(imageChoicesFieldStrings.confirmImagesToggle)) {
				if (window.hasOwnProperty('colorPicker_toggleEnableColors') && typeof window.colorPicker_toggleEnableColors === 'function') {
					window.colorPicker_toggleEnableColors(false);
				}
				imageChoicesAdmin.onToggleEnableImages(enable, isForProductImage);
			}
			else {
				$toggle.prop('checked', false);
			}
		}
		else {
			imageChoicesAdmin.onToggleEnableImages(enable, isForProductImage);
		}
	};
	window.imageChoices_toggleEnableImages = imageChoicesAdmin.toggleEnableImages;// legacy support

	imageChoicesAdmin.onToggleEnableImages = function( enable, forProductImage ) {

		var field = GetSelectedField();
		if ( !field ) {
			return;
		}

		var isForProductImage = ( forProductImage === true );
		var toggleSelector = isForProductImage ? 'input.field_product_image_enabled' : 'input.field_choice_images_enabled';

		var $field = imageChoicesAdmin.getField();
		var $settings = imageChoicesAdmin.getSettingsElement();
		var $toggle = $settings.find(toggleSelector);

		if (enable === undefined) {
			enable = $toggle.is(':checked');
		}

		$field.toggleClass('image-choices-admin-field', enable);
		//$fieldSettings.toggleClass('image-choices-product-field', field.type === "product");

		if ( isForProductImage ) {

			SetFieldProperty('productImage_enabled', enable);
			$toggle.prop('checked', enable);
			$('#product_image_tab_toggle').toggle( enable );

			var $imageWrap = $field.find('.ic-product-image-wrap');

			if ( !enable && $imageWrap.length ) {
				var $label = $field.find('.gfield_label.gform-field-label');
				$label.detach();
				$field.prepend($label);
				$imageWrap.remove();
			}

			$field.toggleClass('product-image-admin-field', enable);
			$field.toggleClass('product-image-enabled', enable);
			$settings.toggleClass('product-image-enabled', enable);

		}
		else {

			SetFieldProperty('imageChoices_enableImages', enable);
			$toggle.prop('checked', enable);
			$('#image_choices_tab_toggle').toggle( enable );

			var choicesContainerSelector = ( field.type.toLowerCase() === "quiz" ) ? '#gquiz_gfield_settings_choices_container' : '#gfield_settings_choices_container';
			var $choicesContainer = $(choicesContainerSelector);
			$choicesContainer.toggleClass('image-choices-enabled', enable);

			$field.toggleClass('image-choices-use-images', enable);
			$settings.toggleClass('image-choices-use-images', enable);

			var $removeChoicesBtn = $settings.find('.image-choices-remove-choices-btn');

			if (!enable) {
				if ($removeChoicesBtn.length) {
					$removeChoicesBtn.remove();
				}
			}

			var $enableOtherChoicesSetting = $('.field_setting.other_choice_setting');
			var isFieldWithOtherChoiceOption = (field.type === 'radio' || (field.type === 'poll' && field.inputType === 'radio'));

			if (isFieldWithOtherChoiceOption) {
				$enableOtherChoicesSetting.show();
			}

		}

		imageChoicesAdmin.updateFieldPreview(field);
	};

	imageChoicesAdmin.toggleShowLabels = function( enable ) {
		var $field = imageChoicesAdmin.getField();
		var $toggle = imageChoicesAdmin.getSettingsElement().find('input.image_choices_show_labels');

		if (enable === undefined) {
			enable = $toggle.is(':checked');
		}

		SetFieldProperty('imageChoices_showLabels', enable);

		$toggle.prop('checked', enable);

		$field.toggleClass('image-choices-show-labels', enable);
	};

	imageChoicesAdmin.toggleShowPrices = function( enable ) {
		var $field = imageChoicesAdmin.getField();
		var $toggle = imageChoicesAdmin.getSettingsElement().find('input.image_choices_show_prices');

		if (enable === undefined) {
			enable = $toggle.is(':checked');
		}

		SetFieldProperty('imageChoices_showPrices', enable);

		$toggle.prop('checked', enable);

		$field.toggleClass('image-choices-show-prices', enable);
	};

	imageChoicesAdmin.toggleUseLightboxCaption = function( enable, forProductImage ) {

		var $field = imageChoicesAdmin.getField();
		var isForProductImage = ( forProductImage === true );
		var toggleSelector = isForProductImage ? 'input.product_image_use_lightbox_caption' : 'input.image_choices_use_lightbox_caption';
		var $toggle = imageChoicesAdmin.getSettingsElement().find(toggleSelector);

		if (enable === undefined) {
			enable = $toggle.is(':checked');
		}

		$toggle.prop('checked', enable);

		if ( isForProductImage ) {
			SetFieldProperty('productImage_useLightboxCaption', enable);
		}
		else {
			SetFieldProperty('imageChoices_useLightboxCaption', enable);
		}

	};

	imageChoicesAdmin.toggleUseLightbox = function( enable, forProductImage ) {

		var field = GetSelectedField();
		var $field = imageChoicesAdmin.getField();
		var isForProductImage = ( forProductImage === true );
		var toggleSelector = isForProductImage ? 'input.product_image_use_lightbox' : 'input.image_choices_use_lightbox';
		var $toggle = imageChoicesAdmin.getSettingsElement().find(toggleSelector);
		var lightboxCaptionSelector = isForProductImage ? '.product-image-setting-use-lightbox-caption' : '.image-choices-setting-use-lightbox-caption';

		if (enable === undefined) {
			enable = $toggle.is(':checked');
		}

		$toggle.prop('checked', enable);

		if ( isForProductImage ) {

			SetFieldProperty('productImage_useLightbox', enable);
			$field.toggleClass('product-image-use-lightbox', enable);

			if ( imageChoicesAdmin.hasProductImage(field) && !imageChoicesAdmin.hasProductImageId(field) ) {
				window.alert(imageChoicesFieldStrings.useLightboxWarning);
				return false;
			}

		}
		else {

			SetFieldProperty('imageChoices_useLightbox', enable);
			$field.toggleClass('image-choices-use-lightbox', enable);

			if (enable) {
				$.each(field.choices, function(i, fieldChoice){
					var hasImage = ( fieldChoice.hasOwnProperty('imageChoices_image') && fieldChoice.imageChoices_image !== '' );
					var hasImageID = ( fieldChoice.hasOwnProperty('imageChoices_imageID') && fieldChoice.imageChoices_imageID !== '' );

					if ( hasImage && !hasImageID ) {
						window.alert(imageChoicesFieldStrings.useLightboxWarning);
						return false;
					}
				});
			}

		}

		document.querySelector(lightboxCaptionSelector).style.setProperty('display', enable ? 'block' : 'none', 'important');

	};

	imageChoicesAdmin.updateEntrySetting = function( value, forProductImage ) {
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';

		SetFieldProperty(`${propPrefix}_entrySetting`, value);
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-entry-value`).val( value );
	};

	imageChoicesAdmin.OpenMediaLibrary = function( btnEl ) {
		if (typeof btnEl === 'undefined') {
			return;
		}

		var field = GetSelectedField();
		var isForProductImage = imageChoicesAdmin.isProductImageField(field);

		var image_fileFrame;

		if ( isForProductImage ) {
			var $field = imageChoicesAdmin.getField();
			image_fileFrame = $field.data('file-frame');
		}
		else {
			var $choice = $(btnEl).closest('[class*="-choice-row"]');
			image_fileFrame = $choice.data('file-frame');
		}

		// If the media frame already exists, reopen it.
		if ( image_fileFrame ) {
			// Open frame
			image_fileFrame.open();
			return;
		}

		// Create the media frame.
		image_fileFrame = wp.media({
			title: 'Select an image to upload',
			button: {
				text: 'Use this image'
			},
			frame: 'post',
			state: 'insert',
			multiple: false	// Set to true to allow multiple files to be selected
		});

		setTimeout(function(){
			$('input[value="Insert into Post"]').val('Use this Image');
			$('button.media-button-insert').text('Use this Image');
		}, 100);

		jQuery('body').on('click', 'li.attachment', function(){
			$('input[value="Insert into Post"]').val('Use this Image');
			$('button.media-button-insert').text('Use this Image');
		});

		// When an image is selected, run a callback.
		image_fileFrame.on( 'insert', function(selection) {
			var state = image_fileFrame.state();
			selection = selection || state.get('selection');
			if (! selection) {
				return;
			}

			var attachment = selection.first();
			var selectedSize = "";

			var display = state.display(attachment).toJSON();
			attachment = attachment.toJSON();

			// start with the selected size, but fallback to one of the defaults if there's no image
			// this can happen eg when there are existing images in the media library when a new size is created, and thumbnails haven't been generated for existing
			// the size will appear as an option in the media library when selecting the image, but then fails as there's no image in that size
			var sizes = ["medium", "thumbnail", "full"];
			if ( display && display.hasOwnProperty('size') && display.size ) {
				sizes.unshift( display.size );
			}

			var img;
			if ( !attachment.hasOwnProperty('sizes') ) {
				img = attachment.url;
				selectedSize = "full";
			}
			else {
				var i = 0;
				var imageSize = sizes[i].toString();
				while ( !attachment.sizes.hasOwnProperty(imageSize) || !attachment.sizes[imageSize].hasOwnProperty('url') || attachment.sizes[imageSize].url === "" ) {
					i++;
					imageSize = sizes[i].toString();
				}
				img = attachment.sizes[imageSize].url;
				selectedSize = imageSize;
				// TODO: size override? But can use the IC image size update tool
				/*
				if ( imageChoicesVars.hasOwnProperty('defaults') && imageChoicesVars.defaults.hasOwnProperty('imageSize') && sizes.indexOf(imageChoicesVars.defaults.imageSize) !== -1 ) {
					img = attachment.sizes[imageChoicesVars.defaults.imageSize].url;
				}
				*/
			}

			if ( isForProductImage ) {
				imageChoicesAdmin.updateProductImage(img, attachment.id);
			}
			else {
				$choice.find('.image-choices-option-image').val(img);
				$choice.find('.image-choices-option-image-id').val(attachment.id);
				$choice.find('.image-choices-option-preview').css('background-image', 'url('+img+')');
				$choice.addClass('image-choices-has-image');

				imageChoicesAdmin.UpdateFieldChoicesObject();
				imageChoicesAdmin.updateFieldPreview(field);

				// GF 2.6+
				var $choicesFlyout = $('#choices-ui-flyout');
				if ( $choicesFlyout.length ) {
					$choicesFlyout.addClass('gform-flyout--anim-in-ready gform-flyout--anim-in-active');
				}
			}

		});

		// Finally, open the modal
		image_fileFrame.open();

		if ( isForProductImage ) {
			$field.data('file-frame', image_fileFrame);
		}
		else {
			$choice.data('file-frame', image_fileFrame);
		}
	};

	imageChoicesAdmin.fieldCanHaveImages = function( field ) {

		if ( typeof field === 'undefined' || !field.hasOwnProperty('type') ) {
			return false;
		}

		var canHave = (
			field.type === 'radio'
			|| field.type === 'checkbox'
			|| ( field.type === 'quiz' && field.inputType == 'radio' )
			|| ( field.type === 'quiz' && field.inputType == 'checkbox' )
			|| ( field.type === 'poll' && field.inputType == 'radio' )
			|| ( field.type === 'poll' && field.inputType == 'checkbox' )
			|| ( field.type === 'post_custom_field' && field.inputType == 'radio' )
			|| ( field.type === 'post_custom_field' && field.inputType == 'checkbox' )
			|| ( field.type === 'survey' && field.inputType == 'radio' )
			|| ( field.type === 'survey' && field.inputType == 'checkbox' )
			|| ( field.type === 'product' && field.inputType == 'radio' )
			|| ( field.type === 'shipping' && field.inputType == 'radio' )
			|| ( field.type === 'option' && field.inputType == 'radio' )
			|| ( field.type === 'option' && field.inputType == 'checkbox' )
			|| ( field.type === 'multi_choice' && field.inputType == 'radio' )
			|| ( field.type === 'multi_choice' && field.inputType == 'checkbox' )
		);

		return gform.applyFilters('gfic_field_can_have_images', canHave, field);

	};

	imageChoicesAdmin.$fieldChoices = function( $field ) {
		if ( typeof $field === 'undefined' || $field instanceof jQuery === false) {
			return [];
		}
		var choicesSelector = '.ginput_container .gfield_radio div[class*="gchoice"], .ginput_container .gfield_checkbox .gchoice:not(.gchoice_select_all)';// GF 2.5+

		return $field.find(choicesSelector);
	};

	imageChoicesAdmin.getSettingsElement = function() {
		var fieldSettingsSelector = '#field_settings_container';// GF 2.5+

		var field = GetSelectedField();
		fieldSettingsSelector += ( field.type === "quiz" ) ? ', #gquiz_gfield_settings_choices_container' : ', #gfield_settings_choices_container'

		return $(fieldSettingsSelector);
	};

	imageChoicesAdmin.previewHeight = function( $el ) {

		if ( typeof $el !== 'object' ) {
			var $field = imageChoicesAdmin.getField();
			$el = $field.find('.ginput_container');
		}

		if ( !$el.length || !$el.closest('.gfield').hasClass('image-choices-admin-field') ) {
			return;
		}

		setTimeout(function(){
			$el.height( $el.find('> ul:first').outerHeight() );
		}, 20);

	};

	imageChoicesAdmin.updateProductImage = function( url, id ) {

		var $settings = imageChoicesAdmin.getSettingsElement();
		var $field = imageChoicesAdmin.getField();

		if ( typeof url === 'undefined' ) {
			url = '';
		}
		if ( typeof id === 'undefined' ) {
			id = '';
		}

		SetFieldProperty('productImage_image', url);
		SetFieldProperty('productImage_imageId', id);

		$settings.find('.product-image-url').val(url);
		$settings.find('.product-image-id').val(id);
		$settings.find('#product-image-preview').css('background-image', 'url('+url+')');

		$settings.toggleClass('has-product-image', url !== '');// todo: class on settings?
		$field.toggleClass('has-product-image', url !== '');// todo: or class on field?
		imageChoicesAdmin.updateFieldPreview( GetSelectedField() );

	};

	imageChoicesAdmin.updateThemeSetting = function( value, forProductImage ) {

		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';

		SetFieldProperty( `${propPrefix}_theme`, value );
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-theme`).val( value );

		var $previewWrap = $(`#${selectorPrefix}-theme-preview`);
		var $previewImg;
		$previewWrap.toggle(value !== 'none');
		if (value === 'none') {
			$previewWrap.hide().find('img').hide();
		}
		else if (value === 'form_setting') {
			value = (window.form.hasOwnProperty('gf-image-choices') && window.form['gf-image-choices'].hasOwnProperty('gf_image_choices_theme')) ? window.form['gf-image-choices'].gf_image_choices_theme : '';
			if ( !value || value === "global_setting" ) {
				value = ( imageChoicesVars.hasOwnProperty('globals') && imageChoicesVars.globals.hasOwnProperty('theme') ) ? imageChoicesVars.globals.theme : ( imageChoicesVars.hasOwnProperty('defaults') && imageChoicesVars.defaults.hasOwnProperty('theme') ) ? imageChoicesVars.defaults.theme : 'simple';
			}
			$previewWrap.find('img').hide();
			$previewImg = $previewWrap.find(`img.${selectorPrefix}-theme-preview-${value}`);
			if ( $previewImg.length ) {
				$previewImg.show();
			}
		}
		else {
			$previewWrap.find('img').hide();
			$previewImg = $previewWrap.find(`img.${selectorPrefix}-theme-preview-${value}`);
			if ( $previewImg.length ) {
				$previewImg.show();
			}
		}

	};

	imageChoicesAdmin.updateFeatureColorSetting = function( value ) {

		SetFieldProperty( `imageChoices_featureColor`, value );

		var $customColorLabel = imageChoicesAdmin.getSettingsElement().find(`[for="image-choices-feature-color-custom"]`);
		var $customColorInput = imageChoicesAdmin.getSettingsElement().find(`#image-choices-feature-color-custom`);
		var $customColorPicker = imageChoicesAdmin.getSettingsElement().find(`.image-choices-setting-feature-color .wp-picker-container`);
		var customColor = $customColorInput.val();

		imageChoicesAdmin.getSettingsElement().find(`#image-choices-feature-color`).val( value );

		if ( value === "custom" ) {
			$customColorLabel.show();
			$customColorInput.show();
			if ( $customColorPicker.length ) {
				$customColorPicker.show();
				setTimeout(function(){
					if ( customColor !== "" ) {
						$customColorInput.wpColorPicker('color', customColor);
					}
					else {
						$customColorPicker.find('.wp-picker-clear').trigger('click');
					}
				}, 100);
			}
			else {
				$customColorInput.wpColorPicker({
					change: function( e, ui ) {
						imageChoicesAdmin.updateCustomFeatureColor( ui.color.toString() );
					}
				});
				setTimeout(function(){
					if ( customColor !== "" ) {
						$customColorInput.wpColorPicker('color', customColor);
					}
					else {
						$customColorPicker.find('.wp-picker-clear').trigger('click');
					}
				}, 100);
			}
		}
		else {
			$customColorLabel.hide();
			$customColorInput.hide();
			if ( $customColorPicker.length ) {
				$customColorPicker.find('.wp-picker-clear').trigger('click');
				$customColorPicker.hide();
			}
		}

	};

	imageChoicesAdmin.updateCustomFeatureColor = function( value, forProductImage ) {
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-feature-color-custom`).val( value );
		SetFieldProperty( `${propPrefix}_featureColorCustom`, value );
	};

	imageChoicesAdmin.updateAlignSetting = function( value ) {
		SetFieldProperty( 'imageChoices_align', value );
		imageChoicesAdmin.getSettingsElement().find('#image-choices-align').val( value );
	};

	imageChoicesAdmin.updateImageStyleSetting = function( value, forProductImage ) {
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';
		SetFieldProperty( `${propPrefix}_imageStyle`, value );

		var $select = imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-image-style`);
		if ( !$select.length ) {
			return;
		}

		$select.val( value );
		if ( value === 'form_setting' ) {
			var optionText = $select.find('option[value="form_setting"]').html();
			value = optionText.substring( optionText.indexOf('(') + 1, optionText.indexOf(')') ).toLowerCase();
			if ( value.indexOf('global:') !== -1 ) {
				value = value.substring( value.indexOf('global:') + 8 );
			}
		}

		imageChoicesAdmin.getSettingsElement().find('.image-choices-setting-height, .image-choices-setting-height-medium, .image-choices-setting-height-small').toggleClass('hide-setting', value === 'square' || value === 'natural' );
	};

	imageChoicesAdmin.validateNumericValue = function( val ) {
		var numVal = '';
		val = val.toString();
		if ( val.trim() !== '' ) {
			var num = parseInt(val, 10);
			if ( isNaN(num) || num <= 0 ) {
				numVal = '';
			}
			else {
				numVal = num.toString();
			}
		}
		return ( numVal !== val ) ? numVal : val;
	};

	imageChoicesAdmin.updateColumnsSetting = function( value ) {
		SetFieldProperty( 'imageChoices_columns', value );
		var $settings = imageChoicesAdmin.getSettingsElement();
		$settings.find('#image-choices-columns').val( value );
		$settings.find('.image-choices-setting-columns-width').toggle( value === 'fixed' );
	};

	imageChoicesAdmin.updateColumnsWidthSetting = function( value ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		SetFieldProperty( 'imageChoices_columnsWidth', numVal );
		imageChoicesAdmin.getSettingsElement().find('#image-choices-columns-width').val( numVal );
	};

	imageChoicesAdmin.updateColumnsMediumSetting = function( value ) {
		SetFieldProperty( 'imageChoices_columnsMedium', value );
		var $settings = imageChoicesAdmin.getSettingsElement();
		$settings.find('#image-choices-columns-medium').val( value );
		$settings.find('.image-choices-setting-columns-width-medium').toggle( value === 'fixed' );
	};

	imageChoicesAdmin.updateColumnsMediumWidthSetting = function( value ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		SetFieldProperty( 'imageChoices_columnsWidthMedium', numVal );
		imageChoicesAdmin.getSettingsElement().find('#image-choices-columns-width-medium').val( numVal );
	};

	imageChoicesAdmin.updateColumnsSmallSetting = function( value ) {
		SetFieldProperty( 'imageChoices_columnsSmall', value );
		var $settings = imageChoicesAdmin.getSettingsElement();
		$settings.find('#image-choices-columns-small').val( value );
		$settings.find('.image-choices-setting-columns-width-small').toggle( value === 'fixed' );
	};

	imageChoicesAdmin.updateColumnsSmallWidthSetting = function( value ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		SetFieldProperty( 'imageChoices_columnsWidthSmall', numVal );
		imageChoicesAdmin.getSettingsElement().find('#image-choices-columns-width-small').val( numVal );
	};

	imageChoicesAdmin.updateHeightSetting = function( value, forProductImage ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';

		SetFieldProperty( `${propPrefix}_height`, numVal );
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-height`).val( numVal );
	};

	imageChoicesAdmin.updateMediumHeightSetting = function( value, forProductImage ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';

		SetFieldProperty( `${propPrefix}_heightMedium`, numVal );
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-height-medium`).val( numVal );
	};

	imageChoicesAdmin.updateSmallHeightSetting = function( value, forProductImage ) {
		var numVal = imageChoicesAdmin.validateNumericValue( value );
		var isForProductImage = ( forProductImage === true );
		var propPrefix = isForProductImage ? 'productImage' : 'imageChoices';
		var selectorPrefix = isForProductImage ? 'product-image' : 'image-choices';

		SetFieldProperty( `${propPrefix}_heightSmall`, numVal );
		imageChoicesAdmin.getSettingsElement().find(`#${selectorPrefix}-height-small`).val( numVal );
	};

	imageChoicesAdmin.initProductImageField = function(field) {
		var $field = $('#field_'+field.id);
		$field.addClass('product-image-admin-field');
		$field.addClass('product-image-enabled');
		if ( imageChoicesAdmin.hasProductImage(field) ) {
			$field.addClass('has-product-image');
		}
		imageChoicesAdmin.updateFieldPreview(field);
	};

	imageChoicesAdmin.initImageChoicesField = function(field) {
		var $field = $('#field_'+field.id);
		$field.addClass('image-choices-admin-field');
		$field.addClass('image-choices-enabled');
		if ( imageChoicesAdmin.hasImageChoicesImage(field) ) {
			$field.addClass('image-choices-has-image');
		}
		imageChoicesAdmin.updateFieldPreview(field);
	};

	imageChoicesAdmin.formEditorInit = function() {

		gform.addAction( 'gform_after_refresh_field_preview', function(fieldId){
			var field = GetSelectedField();
			if ( imageChoicesAdmin.isImageChoicesEnabled(field) ) {
				imageChoicesAdmin.updateFieldPreview(field);
			}
		});

		// setup previews on load
		form.fields.forEach(function(f){
			if ( imageChoicesAdmin.isProductImageEnabled(f) ) {
				imageChoicesAdmin.initProductImageField(f);
			}
			else if ( imageChoicesAdmin.isImageChoicesEnabled(f) ) {
				imageChoicesAdmin.initImageChoicesField(f);
			}
		});

		$('.gfield.image-choices-admin-field').each(function(){
			imageChoicesAdmin.$fieldChoices( $(this) ).each(function(){
				let $choice = $(this);
				if ( !$choice.hasClass('gchoice_total') ) {
					$choice.addClass('image-choices-choice');
				}
			});
		});

		$('#gform_fields .gfield .ginput_container').each(function(){
			imageChoicesAdmin.previewHeight( $(this) );
		});


		$(window).on('gf_update_field_choices', function() {
			setTimeout(function() {
				imageChoicesAdmin.getChoices();
			}, 10);

			imageChoicesAdmin.previewHeight();
		});

		$(document).on('click', '.gf_insert_field_choice, .gf_delete_field_choice', function(e){
			imageChoicesAdmin.previewHeight();
		});

		gform.addAction('gform_load_field_choices', function( fields ){

			setTimeout(function(){

				var field = ( typeof fields !== 'undefined' && fields.length ) ? fields[0] : GetSelectedField();
				var $field = $('#field_'+field.id);
				var $fieldSettings = imageChoicesAdmin.getSettingsElement();

				if ( imageChoicesAdmin.isProductImageField(field) ) {

					$('#field_base_price').on('change', imageChoicesAdmin.onProductPriceChange);

					var productImageEnabled = imageChoicesAdmin.isProductImageEnabled(field);

					var productImageUrl = (field.productImage_image !== undefined) ? field.productImage_image : '';
					var productImageId = (field.productImage_imageId !== undefined) ? field.productImage_imageId : '';
					var useProductImageLightbox = (field.productImage_useLightbox !== undefined) ? field.productImage_useLightbox : false;
					var selectedProductImageTheme = (field.productImage_theme !== undefined) ? field.productImage_theme : 'form_setting';
					var productImageStyle = (field.productImage_imageStyle !== undefined) ? field.productImage_imageStyle : 'form_setting';
					var productImageUseLightboxCaption = (field.productImage_useLightboxCaption !== undefined) ? field.productImage_useLightboxCaption : true;
					if ( productImageUseLightboxCaption === "form_setting" || productImageUseLightboxCaption === "" ) {
						productImageUseLightboxCaption = true;
					}
					var productImageHeightSetting = (field.productImage_height !== undefined) ? field.productImage_height : '';
					var productImageMediumHeightSetting = (field.productImage_heightMedium !== undefined) ? field.productImage_heightMedium : '';
					var productImageSmallHeightSetting = (field.productImage_heightSmall !== undefined) ? field.productImage_heightSmall : '';

					var productImageEntryValue = (field.productImage_entrySetting !== undefined) ? field.productImage_entrySetting : 'form_setting';

					$fieldSettings.addClass('product-image-field-settings');

					imageChoicesAdmin.toggleEnableImages(productImageEnabled, true);

					imageChoicesAdmin.updateProductImage(productImageUrl, productImageId);

					imageChoicesAdmin.toggleUseLightbox(useProductImageLightbox, true);
					imageChoicesAdmin.toggleUseLightboxCaption(productImageUseLightboxCaption, true);

					imageChoicesAdmin.updateThemeSetting(selectedProductImageTheme, true);
					imageChoicesAdmin.updateImageStyleSetting(productImageStyle, true);

					imageChoicesAdmin.updateHeightSetting(productImageHeightSetting, true);
					imageChoicesAdmin.updateMediumHeightSetting(productImageMediumHeightSetting, true);
					imageChoicesAdmin.updateSmallHeightSetting(productImageSmallHeightSetting, true);

					imageChoicesAdmin.updateEntrySetting(productImageEntryValue, true);

				}
				else {
					imageChoicesAdmin.toggleEnableImages(false, true);
					imageChoicesAdmin.updateProductImage('');
					imageChoicesAdmin.toggleUseLightboxCaption(true, true);
					imageChoicesAdmin.updateThemeSetting('form_setting', true);
					imageChoicesAdmin.updateImageStyleSetting('form_setting', true);

					imageChoicesAdmin.updateHeightSetting('', true);
					imageChoicesAdmin.updateMediumHeightSetting('', true);
					imageChoicesAdmin.updateSmallHeightSetting('', true);

					imageChoicesAdmin.updateEntrySetting('', true);

					$field.removeClass('product-image-admin-field');
					$fieldSettings.removeClass('product-image-field-settings');
				}

				if ( imageChoicesAdmin.fieldCanHaveImages(field) ) {

					var imagesEnabled = (field.imageChoices_enableImages === true);
					var useLightbox = (field.imageChoices_useLightbox !== undefined) ? field.imageChoices_useLightbox : false;
					var displayLabels = (field.imageChoices_showLabels !== undefined) ? field.imageChoices_showLabels : true;
					var showPrices = (field.type === 'product' && field.imageChoices_showPrices !== undefined) ? field.imageChoices_showPrices : false;
					var entryValue = (field.imageChoices_entrySetting !== undefined) ? field.imageChoices_entrySetting : 'form_setting';
					var selectedTheme = (field.imageChoices_theme !== undefined) ? field.imageChoices_theme : 'form_setting';
					var featureColor = (field.imageChoices_featureColor !== undefined) ? field.imageChoices_featureColor : 'form_setting';
					var featureColorCustom = (field.imageChoices_featureColorCustom !== undefined) ? field.imageChoices_featureColorCustom : '';
					var alignSetting = (field.imageChoices_align !== undefined) ? field.imageChoices_align : 'form_setting';
					var imageStyle = (field.imageChoices_imageStyle !== undefined) ? field.imageChoices_imageStyle : 'form_setting';
					var columnsSetting = (field.imageChoices_columns !== undefined) ? field.imageChoices_columns : 'form_setting';
					var columnsWidthSetting = (field.imageChoices_columnsWidth !== undefined) ? field.imageChoices_columnsWidth : '';
					var columnsMediumSetting = (field.imageChoices_columnsMedium !== undefined) ? field.imageChoices_columnsMedium : 'form_setting';
					var columnsMediumWidthSetting = (field.imageChoices_columnsWidthMedium !== undefined) ? field.imageChoices_columnsWidthMedium : '';
					var columnsSmallSetting = (field.imageChoices_columnsSmall !== undefined) ? field.imageChoices_columnsSmall : 'form_setting';
					var columnsSmallWidthSetting = (field.imageChoices_columnsWidthSmall !== undefined) ? field.imageChoices_columnsWidthSmall : '';
					var useLightboxCaption = (field.imageChoices_useLightboxCaption !== undefined) ? field.imageChoices_useLightboxCaption : true;
					if ( useLightboxCaption === "form_setting" || useLightboxCaption === "" ) {
						useLightboxCaption = true;
					}
					var heightSetting = (field.imageChoices_height !== undefined) ? field.imageChoices_height : '';
					var mediumHeightSetting = (field.imageChoices_heightMedium !== undefined) ? field.imageChoices_heightMedium : '';
					var smallHeightSetting = (field.imageChoices_heightSmall !== undefined) ? field.imageChoices_heightSmall : '';

					$fieldSettings.addClass('image-choices-field-settings');

					if ( field.type === 'product' ) {
						$fieldSettings.addClass('image-choices-product-field');
					}

					imageChoicesAdmin.toggleEnableImages(imagesEnabled);
					imageChoicesAdmin.toggleUseLightbox(useLightbox);
					imageChoicesAdmin.toggleShowLabels(displayLabels);
					imageChoicesAdmin.toggleShowPrices(showPrices);
					imageChoicesAdmin.updateEntrySetting(entryValue);
					imageChoicesAdmin.toggleUseLightboxCaption(useLightboxCaption);

					if ( !imageChoicesAdmin.isLegacyMode() ) {
						imageChoicesAdmin.updateThemeSetting(selectedTheme);
						imageChoicesAdmin.updateCustomFeatureColor(featureColorCustom);
						imageChoicesAdmin.updateFeatureColorSetting(featureColor);
						imageChoicesAdmin.updateAlignSetting(alignSetting);
						imageChoicesAdmin.updateImageStyleSetting(imageStyle);

						imageChoicesAdmin.updateHeightSetting(heightSetting);
						imageChoicesAdmin.updateMediumHeightSetting(mediumHeightSetting);
						imageChoicesAdmin.updateSmallHeightSetting(smallHeightSetting);

						imageChoicesAdmin.updateColumnsWidthSetting(columnsWidthSetting);
						imageChoicesAdmin.updateColumnsMediumWidthSetting(columnsMediumWidthSetting);
						imageChoicesAdmin.updateColumnsSmallWidthSetting(columnsSmallWidthSetting);

						imageChoicesAdmin.updateColumnsSetting(columnsSetting);
						imageChoicesAdmin.updateColumnsMediumSetting(columnsMediumSetting);
						imageChoicesAdmin.updateColumnsSmallSetting(columnsSmallSetting);
					}

					imageChoicesAdmin.getChoices( field );

				}
				else {

					imageChoicesAdmin.toggleEnableImages(false);
					imageChoicesAdmin.toggleUseLightboxCaption(true);
					//imageChoicesAdmin.toggleShowLabels(false);

					if ( !imageChoicesAdmin.isLegacyMode() ) {
						imageChoicesAdmin.updateThemeSetting('form_setting');
						imageChoicesAdmin.updateCustomFeatureColor('');
						imageChoicesAdmin.updateFeatureColorSetting('form_setting');
						imageChoicesAdmin.updateAlignSetting('form_setting');
						imageChoicesAdmin.updateImageStyleSetting('form_setting');

						imageChoicesAdmin.updateHeightSetting('');
						imageChoicesAdmin.updateMediumHeightSetting('');
						imageChoicesAdmin.updateSmallHeightSetting('');

						imageChoicesAdmin.updateColumnsWidthSetting('');
						imageChoicesAdmin.updateColumnsMediumWidthSetting('');
						imageChoicesAdmin.updateColumnsSmallWidthSetting('');

						imageChoicesAdmin.updateColumnsSetting('form_setting');
						imageChoicesAdmin.updateColumnsMediumSetting('form_setting');
						imageChoicesAdmin.updateColumnsSmallSetting('form_setting');
					}

					$field.removeClass('image-choices-show-labels');
					$fieldSettings.removeClass('image-choices-field-settings');
					$fieldSettings.remove('image-choices-product-field');
				}

			}, 100);

		});

		$(document).bind('gform_load_field_settings', function (event, field, form) {

			// There's currently no action or event fired by GF when the 'show values' or 'show prices' option is toggled, but the preview updates
			// So we override the global UpdateFieldChoices that is called at this point (GF form_editor.js) in order to fire our own gf_update_field_choices event so we can update preview
			if (typeof window.imageChoices_GF_UpdateFieldChoices !== 'function' && typeof UpdateFieldChoices === 'function') {
				window.imageChoices_GF_UpdateFieldChoices = UpdateFieldChoices;
				window.UpdateFieldChoices = function() {
					window.imageChoices_GF_UpdateFieldChoices.apply(this, arguments);
					$(window).trigger('gf_update_field_choices');
				};
			}

			imageChoicesAdmin.previewHeight();
		});

	};

	imageChoicesAdmin.formEditorLegacyWarning = function() {

		return;

		var alertMessage = [
			'<div class="gform-alert" data-js="gform-alert" role="status" data-gform-alert-instance="gfic-legacy-alert-' + window.form.id.toString() + '">',
				'<span class="gform-alert__icon gform-icon gform-icon--warning" aria-hidden="true"></span>',
				'<div class="gform-alert__message-wrap">',
					'<p class="gform-alert__message">JetSloth will stop supporting legacy markup at the end of 2023. We encourage you to test out your form with legacy markup turned off, to ensure continued compatibility with Image Choices.</p>',
					'<a class="gform-alert__cta gform-button gform-button--white gform-button--size-xs" href="' + window.imageChoicesVars.form_settings + window.form.id.toString() + '#gform_setting_markupVersion" aria-label="">Form settings</a>',
				'</div>',
			'</div>',
		].join('');

		var $gfLegacyAlert = $('.simplebar-content > .gform-alert[data-gform-alert-instance^="gform-alert-"]:first');
		if ( $gfLegacyAlert.length ) {
			$gfLegacyAlert.after(alertMessage);
		}
		else {
			$('.simplebar-content').prepend(alertMessage);
		}
		gform.tools.trigger( 'gform_init_alerts' );

	};

	imageChoicesAdmin.adminInit = () => {

		// Custom CSS editors
		var $customCssBoxes = $('#gf_image_choices_custom_css_global, #gf_image_choices_custom_css, #gf_image_choices_user_css_global, #gf_image_choices_user_css_form');
		if ( $customCssBoxes.length ) {
			var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
			editorSettings.codemirror = _.extend(
				{},
				editorSettings.codemirror,
				{
					indentUnit: 4,
					tabSize: 4,
					mode: 'css',
					lineNumbers: true,
					autoCloseBrackets: true,
					continueComments: true,
					indentWithTabs: true,
					inputStyle: "contenteditable",
					lineWrapping: true,
					lint: false,
					matchBrackets: true,
					styleActiveLine: true,
					gutters: [],
					extraKeys: {
						"Alt-F": "findPersistent",
						"Cmd-F": "findPersistent",
						"Ctrl-F": "findPersistent",
						"Cmd-/": "toggleComment",
						"Ctrl-/": "toggleComment",
						"Ctrl-Space": "autocomplete",
					}
				}
			);
			$customCssBoxes.each(function(){
				window[this.id + '_editor'] = wp.codeEditor.initialize( $(this), editorSettings );
			});
		}

		var $legacyCssSectionToggle = $('#gform_settings_section_collapsed_gf_image_choices_legacy_custom_css_section, #gform_settings_section_collapsed_gf_image_choices_legacy_custom_form_css_section');
		if ( $legacyCssSectionToggle.length ) {
			$legacyCssSectionToggle.on('click', function(){
				$(this).closest('.gform-settings-panel').find('textarea').each(function(){
					var ref = $(this).attr('id') + '_editor';
					try {
						window[ref].codemirror.refresh();
					}
					catch(e) {
						// do nothing
					}
				});
			});
		}

		var $globalStyleSelect = $('#gf_image_choices_global_image_style');
		if ( $globalStyleSelect.length ) {
			$globalStyleSelect.on('change', function(e){
				var style = e.currentTarget.value;
				$('#gform_setting_gf_image_choices_global_height, #gform_setting_gf_image_choices_global_height_medium, #gform_setting_gf_image_choices_global_height_small').toggle( style !== 'natural' && style !== 'square' );
			});
			$globalStyleSelect.trigger('change');
		}

		var $formStyleSelect = $('#gf_image_choices_image_style');
		if ( $formStyleSelect.length ) {
			$formStyleSelect.on('change', function(e){
				var style = e.currentTarget.value;
				if ( style === 'global_setting' ) {
					var optionText = $(e.currentTarget).find(`option[value="${style}"]`).html();
					style = optionText.substring( optionText.indexOf('(') + 1, optionText.indexOf(')') ).toLowerCase();
				}
				$('#gform_setting_gf_image_choices_height, #gform_setting_gf_image_choices_height_medium, #gform_setting_gf_image_choices_height_small').toggle( style !== 'natural' && style !== 'square' );
			});
			$formStyleSelect.trigger('change');
		}

		// show/hide new settings based on toggle
		var $legacyModeToggle = $('#_gform_setting_gf_image_choices_use_legacy_styles');
		var $newFeaturesElements = $('#gform-settings-section-theme, #gform-settings-section-feature-color, #gform-settings-section-choices-layout, #gform-settings-section-image-options, #gform-settings-section-custom-css, #gform-settings-section-gf_image_choices_legacy_custom_css_section .gform-settings-label');

		if ( $legacyModeToggle.length ) {
			$legacyModeToggle.on('change', function(e){
				var useLegacyMode = $legacyModeToggle.is(':checked');
				if ( $newFeaturesElements.length ) {
					$newFeaturesElements.toggle( !useLegacyMode );
				}
				if ( $legacyCssSectionToggle.length ) {
					if ( ( useLegacyMode && $legacyCssSectionToggle.is(":checked") ) || ( !useLegacyMode && !$legacyCssSectionToggle.is(":checked") ) ) {
						$legacyCssSectionToggle.click();
					}
				}
			});
			$legacyModeToggle.trigger('change');
		}



		// settings page tab switch (hide submit if not settings tab)
		var $settingsTabNavLinks = $('#tab_gf-image-choices .gform-settings-tabs__navigation a');
		if ( $settingsTabNavLinks.length ) {
			$settingsTabNavLinks.each(function(){
				$(this).on('click', function(e){
					window.location.hash = e.currentTarget.id.replace('gform-settings-tab-gf_image_choices_', '');
					$('#tab_gf-image-choices #gform-settings-save').toggle( $(this).data('tab') === "gf_image_choices_settings_tab" );
				});
			});

			setTimeout(() => {
				const hash = window.location.hash.substring(1);
				const hashTab = ( hash !== "" ) ? document.querySelector(`#tab_gf-image-choices #gform-settings-tab-gf_image_choices_${hash}`) : null;
				const tabLink = hashTab ? hashTab : $settingsTabNavLinks[0];
				tabLink.dispatchEvent( new Event('click') );
			}, 100);
		}


		// numeric settings live validation to number (plugin and form settings)
		var $numericSettingsInputs = $('input[id^="gf_image_choices"][id*="columns_width"], input[id^="image-choices"][id*="columns-width"], input[id^="gf_image_choices"][id*="height"], input[id^="image-choices"][id*="height"], input[id^="product-image"][id*="height"]');
		if ( $numericSettingsInputs.length ) {
			$numericSettingsInputs.each(function(){
				var $input = $(this);
				$input.on('keyup', function(e){
					var val = e.currentTarget.value.toString();
					var numVal = imageChoicesAdmin.validateNumericValue( val );
					if ( numVal !== val ) {
						e.currentTarget.value = numVal;
					}
				});
			});
		}


		// form settings theme preview
		var $settingsThemeSelect = $('#gf_image_choices_theme, #gf_image_choices_global_theme').first();
		var $settingsThemePreview = $('#gform_setting_gf_image_choices_theme_preview .image-choices-theme-preview, #gform_setting_gf_image_choices_global_theme_preview .image-choices-theme-preview').first();
		if ( $settingsThemeSelect.length && $settingsThemePreview.length ) {
			$settingsThemeSelect.on('change', function(e){
				var value = $(this).val();
				if ( value === "" || value == "global_setting" ) {
					value = ( imageChoicesVars.hasOwnProperty('globals') && imageChoicesVars.globals.hasOwnProperty('theme') ) ? imageChoicesVars.globals.theme : ( imageChoicesVars.hasOwnProperty('defaults') && imageChoicesVars.defaults.hasOwnProperty('theme') ) ? imageChoicesVars.defaults.theme : 'simple';
				}

				$settingsThemePreview.toggle(value !== 'none');
				if (value === 'none') {
					$settingsThemePreview.hide().find('img').hide();
				}
				else {
					$settingsThemePreview.find('img').hide();
					$settingsThemePreview.find('img.image-choices-theme-preview-'+value).show();
				}
			});
			$settingsThemeSelect.trigger('change');
		}

		// form settings feature color
		var $settingsFeatureColorSelect = $('#gf_image_choices_feature_color, #gf_image_choices_global_feature_color').first();
		var $settingsFeatureColorCustom = $('#gform_setting_gf_image_choices_feature_color_custom, #gform_setting_gf_image_choices_global_feature_color_custom').first();
		var $settingsFeatureColorInput = $('#gf_image_choices_feature_color_custom, #gf_image_choices_global_feature_color_custom').first();
		var settingsCustomFeatureColor;
		var $settingsFeatureColorPicker;

		if ( $settingsFeatureColorSelect.length && $settingsFeatureColorCustom.length ) {

			$settingsFeatureColorPicker = $settingsFeatureColorCustom.find('.wp-picker-container');
			settingsCustomFeatureColor = $settingsFeatureColorInput.val();

			$settingsFeatureColorSelect.on('change', function(e){
				var value = $(this).val();

				if ( value === "custom" ) {
					$settingsFeatureColorCustom.show();
					if ( $settingsFeatureColorPicker.length ) {
						setTimeout(function(){
							if ( customColor !== "" ) {
								$settingsFeatureColorInput.wpColorPicker('color', settingsCustomFeatureColor);
							}
							else {
								$settingsFeatureColorPicker.find('.wp-picker-clear').trigger('click');
							}
						}, 100);
					}
					else {
						$settingsFeatureColorInput.wpColorPicker();
						setTimeout(function(){
							if ( settingsCustomFeatureColor !== "" ) {
								$settingsFeatureColorInput.wpColorPicker('color', settingsCustomFeatureColor);
							}
							else {
								$settingsFeatureColorPicker.find('.wp-picker-clear').trigger('click');
							}
						}, 100);
					}
				}
				else {
					$settingsFeatureColorCustom.hide();
					if ( $settingsFeatureColorPicker.length ) {
						$settingsFeatureColorPicker.find('.wp-picker-clear').trigger('click');
					}
				}

			});
			$settingsFeatureColorSelect.trigger('change');
		}



		var $urlReplacementTool = $('#image_choices_url_replacement');
		var $urlReplacementSubmit = ($urlReplacementTool.length) ? $('#image_choices_url_replacement_submit') : null;
		var $urlReplacementProgress = ($urlReplacementTool.length) ? $urlReplacementTool.find('.jetbase-tool__progress') : null;
		var $urlReplacementProgressBar = ($urlReplacementTool.length) ? $urlReplacementTool.find('.jetbase-tool__progress-percent') : null;
		var $urlReplacementStatus = ($urlReplacementTool.length) ? $urlReplacementTool.find('.jetbase-tool__progress-status') : null;

		var urlReplacements = {
			old: "",
			new: "",
			forms: [],
			index: 0,
			replacements: []
		}

		imageChoicesAdmin.processUrlReplacement = function() {
			if ( !urlReplacements.forms.length || urlReplacements.replacements.length === urlReplacements.forms.length || urlReplacements.index >= urlReplacements.forms.length ) {
				// done
				$urlReplacementProgressBar.css('width', `100%`);
				$urlReplacementStatus.html(`Done! ${urlReplacements.forms.length} form(s) processed`);
				$urlReplacementTool.removeClass('busy');
				if ( $sizeReplacementTool.length ) {
					$sizeReplacementTool.removeClass('disabled');
				}
				return
			}

			$.ajax({
				url: window.ajaxurl,
				method: 'POST',
				data: {
					action: 'gf_image_choices_url_replacement',
					id: urlReplacements.forms[urlReplacements.index],
					old: urlReplacements.old,
					new: urlReplacements.new
				},
				beforeSend: function(jqXHR, settings){
					$urlReplacementStatus.html(`Processing form ${urlReplacements.index + 1}/${urlReplacements.forms.length}`);
				},
				success: function(response) {
					if ( response && response.success ) {
						urlReplacements.replacements[urlReplacements.index] = response.total;
					}
					else if ( response && response.error ) {
						urlReplacements.replacements[urlReplacements.index] = false;
						$urlReplacementStatus.html('Error: ' + response.error );
					}
					else {
						urlReplacements.replacements[urlReplacements.index] = false;
						$urlReplacementStatus.html('Error');
					}
					urlReplacements.index++;
					$urlReplacementProgressBar.css('width', `${ Math.ceil( (urlReplacements.index / urlReplacements.forms.length) * 100 ) }%`);
					imageChoicesAdmin.processUrlReplacement();
				}
			});

		}

		if ( $urlReplacementSubmit && $urlReplacementSubmit.length ) {

			$urlReplacementSubmit.on('click', function(e){
				e.preventDefault();

				var formId = $urlReplacementTool.find('#image_choices_url_replacement_form_select').val().trim();
				var oldUrl = $urlReplacementTool.find('#image_choices_url_replacement_from_input').val().trim();
				var newUrl = $urlReplacementTool.find('#image_choices_url_replacement_to_input').val().trim();
				if ( !oldUrl || !newUrl || oldUrl === newUrl ) {
					return;
				}

				urlReplacements.old = oldUrl;
				urlReplacements.new = newUrl;
				urlReplacements.forms = [];
				urlReplacements.index = 0;
				urlReplacements.replacements = [];

				if ( formId === "all" ) {

					$urlReplacementStatus.html('Scanning for forms with old URL in image choices...');
					$urlReplacementProgressBar.css('width', '0%');
					$urlReplacementTool.addClass('busy');
					$urlReplacementProgress.addClass('active');
					if ( $sizeReplacementTool.length ) {
						$sizeReplacementTool.addClass('disabled');
					}

					$.ajax({
						url: window.ajaxurl,
						method: 'POST',
						data: {
							action: 'gf_image_choices_get_url_replacement_form_ids',
							form_id: urlReplacements.formId,
							old: urlReplacements.old,
							new: urlReplacements.new
						},
						success: function(response) {
							if ( response && response.success ) {
								if ( response.forms && response.forms.length ) {
									urlReplacements.forms = response.forms
									imageChoicesAdmin.processUrlReplacement();
								}
								else {
									$urlReplacementProgressBar.css('width', `100%`);
									$urlReplacementStatus.html(`Done! No image choices found containing that old URL`);
									$urlReplacementTool.removeClass('busy');
									if ( $sizeReplacementTool.length ) {
										$sizeReplacementTool.removeClass('disabled');
									}
								}
							}
							else if ( response && response.error ) {
								$urlReplacementStatus.html('Error: ' + response.error );
							}
							else {
								$urlReplacementStatus.html('Error');
							}
						}
					});

				}
				else {

					$urlReplacementStatus.html('...');
					$urlReplacementProgressBar.css('width', '0%');
					$urlReplacementTool.addClass('busy');
					$urlReplacementProgress.addClass('active');
					if ( $sizeReplacementTool.length ) {
						$sizeReplacementTool.addClass('disabled');
					}

					urlReplacements.forms = [ formId ]
					imageChoicesAdmin.processUrlReplacement();

				}

			});

		}


		var $sizeReplacementTool = $('#image_choices_image_replacement');
		var $sizeReplacementSubmit = ($sizeReplacementTool.length) ? $('#image_choices_image_replacement_submit') : null;
		var $sizeReplacementProgress = ($sizeReplacementTool.length) ? $sizeReplacementTool.find('.jetbase-tool__progress') : null;
		var $sizeReplacementProgressBar = ($sizeReplacementTool.length) ? $sizeReplacementTool.find('.jetbase-tool__progress-percent') : null;
		var $sizeReplacementStatus = ($sizeReplacementTool.length) ? $sizeReplacementTool.find('.jetbase-tool__progress-status') : null;

		var sizeReplacements = {
			new: "",
			forms: [],
			index: 0,
			replacements: []
		}

		imageChoicesAdmin.processSizeReplacement = function() {
			if ( !sizeReplacements.forms.length || sizeReplacements.replacements.length === sizeReplacements.forms.length || sizeReplacements.index >= sizeReplacements.forms.length ) {
				// done
				$sizeReplacementProgressBar.css('width', `100%`);
				$sizeReplacementStatus.html(`Done! ${sizeReplacements.forms.length} form(s) processed`);
				$sizeReplacementTool.removeClass('busy');
				if ( $urlReplacementTool.length ) {
					$urlReplacementTool.removeClass('disabled');
				}
				return
			}

			$.ajax({
				url: window.ajaxurl,
				method: 'POST',
				data: {
					action: 'gf_image_choices_image_size_replacement',
					id: sizeReplacements.forms[sizeReplacements.index],
					new: sizeReplacements.new
				},
				beforeSend: function(jqXHR, settings) {
					$sizeReplacementStatus.html(`Processing form ${sizeReplacements.index + 1}/${sizeReplacements.forms.length}`);
				},
				success: function(response) {
					if ( response && response.success ) {
						sizeReplacements.replacements[sizeReplacements.index] = response.total;
					}
					else if ( response && response.error ) {
						sizeReplacements.replacements[sizeReplacements.index] = false;
						$sizeReplacementStatus.html('Error: ' + response.error );
					}
					else {
						sizeReplacements.replacements[sizeReplacements.index] = false;
						$sizeReplacementStatus.html('Error');
					}
					sizeReplacements.index++;
					$sizeReplacementProgressBar.css('width', `${ Math.ceil( (sizeReplacements.index / sizeReplacements.forms.length) * 100 ) }%`);
					imageChoicesAdmin.processSizeReplacement();
				}
			});

		}

		if ( $sizeReplacementSubmit && $sizeReplacementSubmit.length ) {

			$sizeReplacementSubmit.on('click', function(e){
				e.preventDefault();

				var newSize = $sizeReplacementTool.find('#image_choices_image_replacement_size_select').val().trim();
				var formId = $sizeReplacementTool.find('#image_choices_image_replacement_form_select').val().trim();
				if ( !newSize ) {
					return;
				}

				sizeReplacements.new = newSize;
				sizeReplacements.forms = [];
				sizeReplacements.index = 0;
				sizeReplacements.replacements = [];


				if ( formId === "all" ) {

					$sizeReplacementStatus.html('Scanning for forms with image choices enabled...');
					$sizeReplacementProgressBar.css('width', '0%');
					$sizeReplacementTool.addClass('busy');
					$sizeReplacementProgress.addClass('active');

					if ( $urlReplacementTool.length ) {
						$urlReplacementTool.addClass('disabled');
					}

					$.ajax({
						url: window.ajaxurl,
						method: 'POST',
						data: {
							action: 'gf_image_choices_get_image_size_replacement_form_ids',
						},
						success: function(response) {
							if ( response && response.success ) {
								if ( response.forms && response.forms.length ) {
									sizeReplacements.forms = response.forms
									imageChoicesAdmin.processSizeReplacement();
								}
								else {
									$sizeReplacementProgressBar.css('width', `100%`);
									$sizeReplacementStatus.html(`Done! No forms found with image choices enabled`);
									$sizeReplacementTool.removeClass('busy');
									if ( $urlReplacementTool.length ) {
										$urlReplacementTool.removeClass('disabled');
									}
								}
							}
							else if ( response && response.error ) {
								$sizeReplacementStatus.html('Error: ' + response.error );
							}
							else {
								$sizeReplacementStatus.html('Error');
							}
						}
					});

				}
				else {

					$sizeReplacementStatus.html('...');
					$sizeReplacementProgressBar.css('width', '0%');
					$sizeReplacementTool.addClass('busy');
					$sizeReplacementProgress.addClass('active');

					if ( $urlReplacementTool.length ) {
						$urlReplacementTool.addClass('disabled');
					}

					sizeReplacements.forms = [ formId ];
					imageChoicesAdmin.processSizeReplacement();

				}

			});

		}

	};

	var gformAdminReady = false;
	var gformAdminReadyCheck;

	$(document).ready(function() {
		if ( !gformAdminReady ) {
			gformAdminReadyCheck = setInterval(function(){
				if ( typeof gform !== 'undefined' ) {
					gformAdminReady = true;
					clearInterval(gformAdminReadyCheck);
					imageChoicesAdmin.adminInit();
				}
			}, 100);
		}
	});

	$(document).bind( 'gform_main_scripts_loaded', function() {

		if ( typeof window.form !== 'undefined' ) {
			var markupVersion = imageChoicesAdmin.markupDetect();
			if ( markupVersion === 1 && imageChoicesAdmin.formHasImageChoicesFields() ) {
				imageChoicesAdmin.formEditorLegacyWarning();
			}
			imageChoicesAdmin.formEditorInit();
		}

	});

})(jQuery);
