/*! © JetSloth — SPDX-License-Identifier: GPL-2.0-or-later */

var imageChoices = imageChoices || {};
(function($){

	imageChoices.cssClasses = {
		"selected": "image-choices-choice-selected",
		"hover": "image-choices-choice-hover",
		"focus": "image-choices-choice-focus"
	}

	imageChoices.getFormMarkupVersion = function( form_or_id ){
		var formElementID;
		var formID = '';
		var $form;

		if ( typeof form_or_id === 'undefined' || ( typeof form_or_id === 'string' && form_or_id === '' ) || ( typeof form_or_id === 'number' && form_or_id <= 0 ) ) {
			formElementID = $('form[id^="gform_"]:first').attr('id');
			formID = ( window.hasOwnProperty('gf_get_form_id_by_html_id') ) ? window.gf_get_form_id_by_html_id( formElementID ) : formElementID.replace('gform_', '');
			$form = $('#' + formElementID);
		}
		else if ( form_or_id instanceof jQuery ) {
			formElementID = form_or_id.attr('id');
			formID = ( window.hasOwnProperty('gf_get_form_id_by_html_id') ) ? window.gf_get_form_id_by_html_id( formElementID ) : formElementID.replace('gform_', '');
			$form = form_or_id;
		}
		else if ( typeof form_or_id === 'string' && form_or_id.indexOf("gform_") !== -1 ) {
			formID = (window.hasOwnProperty('gf_get_form_id_by_html_id')) ? window.gf_get_form_id_by_html_id( form_or_id ) : form_or_id.replace('gform_', '');
			formElementID = "gform_" + formID;
			$form = $('#' + formElementID);
		}
		else {
			formID = (window.hasOwnProperty('gf_get_form_id_by_html_id')) ? window.gf_get_form_id_by_html_id( "gform_" + form_or_id.toString() ) : form_or_id.toString();
			formElementID = "gform_" + formID;
			$form = $('#' + formElementID);
		}

		if ( !$form.length ) {
			return $('.gform_body .gfield:first').is('li') ? 1 : 2;
		}

		return $form.find('.gform_body .gfield:first').is('li') ? 1 : 2;
	};

	imageChoices.isLegacyMarkup = function( form_or_id ) {
		return ( imageChoices.getFormMarkupVersion( form_or_id ) === 1 );
	}

	imageChoices.isLegacyMode = function() {
		var useNewFeatures = ( imageChoicesVars.hasOwnProperty('useNewFeatures') && imageChoicesVars.useNewFeatures.toString() === 'true');
		return !useNewFeatures;
	};

	imageChoices.$fieldChoices = function( $field ) {
		if ( typeof $field === 'undefined' || $field instanceof jQuery === false) {
			return [];
		}

		var choicesSelector = '.ginput_container .gfield_radio div[class*="gchoice"], .ginput_container .gfield_checkbox div[class*="gchoice"]:not(.gchoice_select_all)';// GF 2.5+
		if ( imageChoices.isLegacyMarkup( $field.closest('[id^="gform_wrapper_"]') ) ) {
			choicesSelector = '.ginput_container .gfield_radio li, .ginput_container .gfield_checkbox li:not(.gchoice_select_all)';
		}

		return $field.find(choicesSelector);
	};

	imageChoices.SelectedFields = function( form_id ) {
		var $form = ( typeof form_id !== 'undefined' && form_id !== '' ) ? $('#gform_'+form_id) : $('.gform_wrapper form');
		imageChoices.$fieldChoices( $form.find('.image-choices-field') ).find('input:checked').each(function() {
			var $input = $(this);
			var $choice = $input.closest('[class*="gchoice"]');// TODO: Update to just .gchoice ?
			$choice.addClass(imageChoices.cssClasses.selected);
			/*
			if ( $input.val() === "gf_other_choice" ) {
				$input.trigger('click')
			}
			*/
		});
	};

	imageChoices.InitLightbox = function( form_id ){
		var $form = ( typeof form_id !== 'undefined' && form_id !== '' ) ? $('#gform_'+form_id) : $('.gform_wrapper form');
		var form_id_num = parseInt(form_id, 10);
		$form.find('.image-choices-field.image-choices-use-lightbox').each(function(){
			var $field = $(this);
			var field_id = $field.attr('id');
			var field_id_num = parseInt( field_id.substring( field_id.lastIndexOf('_') + 1 ), 10);
			if ( !imageChoicesVars.hasOwnProperty('elementorCompat') || imageChoicesVars.elementorCompat !== 'elementor' ) {
				var opts = gform.applyFilters('gfic_lightbox_options', {
					captions: imageChoices.isLegacyMode() || $field.hasClass('ic-lightbox-captions'),
					captionType: 'data',
					captionsData: 'caption'
				}, form_id_num, field_id_num);
				opts.captions = gform.applyFilters('gfic_lightbox_captions', opts.captions, form_id_num, field_id_num);// for backward compat
				opts = gform.applyFilters(`gfic_lightbox_options_${form_id_num}`, opts, form_id_num, field_id_num);
				opts = gform.applyFilters(`gfic_lightbox_options_${form_id_num}_${field_id_num}`, opts, form_id_num, field_id_num);
				$field.find('.image-choices-lightbox-btn').jetslothLightbox(opts);
			}
		});
		$form.find('.product-image-field.product-image-use-lightbox').each(function(){
			var $field = $(this);
			var field_id = $field.attr('id');
			var field_id_num = parseInt( field_id.substring( field_id.lastIndexOf('_') + 1 ), 10);

			var $lightboxBtn = $field.find('.ic-product-image-lightbox-btn');

			if ( !imageChoicesVars.hasOwnProperty('elementorCompat') || imageChoicesVars.elementorCompat !== 'elementor' ) {
				var title = '';
				if ( $field.find('.gfield_label_product').length ) {
					title = $field.find('.gfield_label_product').text();
				}
				else {
					title = ( $field.find('.gftt-label').length ) ? $field.find('.gftt-label').text() : $field.find('.gfield_label').text();
				}
				var escapedTitle = (title + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
				$lightboxBtn.data('caption', escapedTitle);
				$lightboxBtn.attr('data-elementor-open-lightbox', 'no');
				var opts = gform.applyFilters('gfic_lightbox_options', {
					captions: $field.hasClass('product-image-lightbox-captions'),
					captionType: 'data',
					captionsData: 'caption'
				}, form_id_num, field_id_num);
				opts.captions = gform.applyFilters('gfic_lightbox_captions', opts.captions, form_id_num, field_id_num);// for backward compat
				opts = gform.applyFilters(`gfic_lightbox_options_${form_id_num}`, opts, form_id_num, field_id_num);
				opts = gform.applyFilters(`gfic_lightbox_options_${form_id_num}_${field_id_num}`, opts, form_id_num, field_id_num);
				$lightboxBtn.jetslothLightbox(opts);
			}
		});
	};

	imageChoices.onChoiceKeyPress = function(e) {
		var TABKEY = 9;
		var SPACEKEY = 32;
		var $input = $(e.target);
		var $field = $input.closest('.gfield');

		if (e.keyCode === SPACEKEY && ( $input.is(':checkbox') || $input.is(':radio') ) ) {
			if ($input.is(':radio') && !$input.is(':checked')) {
				e.preventDefault();
				//e.stopImmediatePropagation();
				//$input.next('label').click();
			}
			else if ($input.is(':checkbox')) {
				e.preventDefault();
				e.stopImmediatePropagation();
				//$input.next('label').click();
			}
		}
		else if (e.keyCode === TABKEY && $field.hasClass('image-choices-field')) {
			var direction = (e.shiftKey) ? 'previous' : 'next';
			var $inputWrap = $input.closest('.image-choices-choice');

			if (direction === 'next') {
				if (!$inputWrap.is(':last-child')) {
					e.preventDefault();
					//e.stopImmediatePropagation();
					$inputWrap.next('.image-choices-choice').find('> input').focus();
				}
			}
			else if (direction === 'previous') {
				if (!$inputWrap.is(':first-child')) {
					e.preventDefault();
					//e.stopImmediatePropagation();
					$inputWrap.prev('.image-choices-choice').find('> input').focus();
				}
			}

		}
	};

	imageChoices.onChoicesSelectAllClick = function(e) {
		var $toggle = $(this);
		var $field = $toggle.closest('.gfield');
		var toggledOn = $toggle.is('input') ? $toggle.is(':checked') : $toggle.data('checked');
		var $choices = toggledOn ? $field.find('.image-choices-choice input:checked') : $field.find('.image-choices-choice input:not(:checked)');

		if ($choices.length) {
			$choices.each(function(n, choiceEl){
				var $choice = $(choiceEl);
				imageChoices.onChoiceLabelClick( $choice.next('label') );
			});
		}
	};

	imageChoices.onChoiceLabelClick = function( $label ) {
		var $choice = $label.closest('[class*="gchoice"]');
		var $wrap = $choice.closest('.gfield_checkbox, .gfield_radio').first();

		var $input = $choice.find('input');

		if ($choice.hasClass('image-choices-choice-other')) {
			setTimeout(function(){
				$choice.find('.gf_other_wrap').addClass('active');
				$choice.find('input:text').focus();
			}, 250)
		}

		if ($wrap.hasClass('gfield_radio')) {
			// radio
			$wrap.find('.'+imageChoices.cssClasses.selected).not($choice).removeClass(imageChoices.cssClasses.selected).find('input').prop('checked', false);
			$choice.addClass(imageChoices.cssClasses.selected);
		}
		else if ($wrap.hasClass('gfield_checkbox')) {
			// checkbox
			if ( $input.is( ':checked' ) ) {
				$choice.addClass(imageChoices.cssClasses.selected);
			}
			else {
				$choice.removeClass(imageChoices.cssClasses.selected);
			}
		}
	};


	imageChoices.clearProductImageChoice = function($field) {
		let $input = $field.querySelector('.ginput_container_singleproduct .ginput_quantity');
		$field.classList.remove('ic-product-selected');
		$input.value = '';
		jQuery($input).trigger('input');
	};

	imageChoices.selectProductImageChoice = function($field) {
		$field.classList.add('ic-product-selected');
		let group = Array.from($field.classList).filter(cls => ( cls.indexOf('ic-product-group-') !== -1 ));
		let groupSelector = ( group.length ) ? '.' + group[0] : '';
		document.querySelectorAll(`.product-image-field.ic-product-single-choice${groupSelector}:not([id="${$field.id}"])`).forEach($choice => {
			imageChoices.clearProductImageChoice($choice);
		});
	};

	imageChoices.onProductImageChoiceClick = function($field){
		let $input = $field.querySelector('.ginput_container_singleproduct .ginput_quantity');
		if ( !$field.classList.contains('ic-product-single-choice') ) {
			$field.classList.toggle('ic-product-selected');
			if ( !$field.classList.contains('ic-product-selected') ) {
				imageChoices.clearProductImageChoice($field);
			}
		}
		else if ( !$field.classList.contains('ic-product-selected') ) {
			imageChoices.selectProductImageChoice($field);
		}
	};

	imageChoices.setupProductImageChoices = function($form){
		$form.querySelectorAll('.product-image-field.ic-product-image-choice .ic-product-image-wrap').forEach($wrap => {
			$wrap.addEventListener('click', e => {
				e.preventDefault();
				imageChoices.onProductImageChoiceClick( e.currentTarget.closest('.gfield') );
			});
		});
	};



	imageChoices.SetUpFields = function( form_id, current_page ) {

		// Entry detail view, doesn't get the custom class on the "field" wrap (it's .detail-view not .gfield)
		// If we add the classes here, the rest of the script (and styles) will work
		$('.entry-details .detail-view .image-choices-choice-image-wrap').each(function(i){
			var $imgWrap = $(this);
			var $field = $imgWrap.closest('.detail-view');
			$field.addClass('image-choices-field image-choices-show-labels');
		});


		var $form = ( typeof form_id !== 'undefined' && form_id !== '' ) ? $('#gform_'+form_id) : $('.gform_wrapper form');

		// WooCommerce Gravity Forms Product Add-Ons compatibility
		if ( !$form.length && $('.gform_variation_wrapper').length ) {
			$form = $('.gform_variation_wrapper');
		}

		var $fields = $form.find('.image-choices-field');
		if ( $fields.length ) {
			$fields.find('.gfield_radio, .gfield_checkbox').addClass('gform-theme__no-reset--children');
		}

		var $choices = imageChoices.$fieldChoices( $fields );
		var $productImages = $form.find('.product-image-field');

		$form.find('.gchoice_select_all input, button[id$="select_all"]').each(function(t, toggle){
			$(toggle).on('click', imageChoices.onChoicesSelectAllClick);
		});

		if ( $choices.length > 0 || $productImages.length > 0 ) {

			$choices.each(function(){
				var $choice = $(this);

				var choiceInit = $choice.data('init');
				if (choiceInit !== true) {

					$choice.data('init', true);

					$choice.find('label').addClass('gform-field-label');// for non gf theme installs

					// add a hover state
					$choice.find('label').hover(function(e){
						var $this = $(this);
						if ( $this.find('input').is(':disabled') ) {
							return false;
						}
						else {
							var $choice = $this.closest('[class*="gchoice"]');// TODO: Update to just .gchoice ?
							$choice.addClass(imageChoices.cssClasses.hover);
						}

					}, function(e){
						var $this = $(this);
						if ( $this.find('input').is(':disabled') ){
							return false;
						}
						else {
							var $choice = $this.closest('[class*="gchoice"]');// TODO: Update to just .gchoice ?
							$choice.removeClass(imageChoices.cssClasses.hover);
						}

					});

					$choice.find('input').focus(function() {
						var $this = $(this);
						var $choice = $this.closest('[class*="gchoice"]');// TODO: Update to just .gchoice ?
						$choice.addClass(imageChoices.cssClasses.focus);
					}).blur( function() {
						var $this = $(this);
						var $choice = $this.closest('[class*="gchoice"]');// TODO: Update to just .gchoice ?
						$choice.removeClass(imageChoices.cssClasses.focus);
					});


					if ($choice.find('input[value="gf_other_choice"]').length) {

						$choice.addClass('image-choices-choice-other');

						$choice.find('input[type="text"]').wrap('<div class="gf_other_wrap"></div>');
						$('.gf_other_wrap').append('<a href="#" class="gf_other_close"></a>');
						$(document).on('click', '.gf_other_close', function(e){
							e.preventDefault();
							$(this).parent().removeClass('active')
						});

						$choice.find( 'input:not([type="text"])' ).on('click', function() {
							var $this = $( this );
							var $label = $this.closest('.image-choices-choice').find('label')
							imageChoices.onChoiceLabelClick( $label );
						} );

					}
					else {

						$choice.find( 'input' ).on('click', function() {
							var $this = $( this );
							var $label = $this.closest('.image-choices-choice').find('label')
							imageChoices.onChoiceLabelClick( $label );
						} );

					}

					var $choicesField = $choice.closest('.image-choices-field');
					if ($choicesField.hasClass('image-choices-use-lightbox') && !$choice.find('.image-choices-lightbox-btn').length) {
						var id = $choicesField.attr('id');
						var title = $choice.find('.image-choices-choice-text').html();
						var escapedTitle = (title + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
						var thumbUrl = $choice.find('.image-choices-choice-image').attr('src');
						var imgUrl = $choice.find('.image-choices-choice-image').data('lightbox-src');
						if (typeof imgUrl === 'undefined' || imgUrl === '') {
							imgUrl = thumbUrl;
						}

						if (imgUrl !== '') {

							var elementorAtts = ( imageChoicesVars.hasOwnProperty('elementorCompat') && imageChoicesVars.elementorCompat !== 'elementor' ) ? 'data-elementor-open-lightbox="no"' : '';

							var $lightboxBtn = $('<a href="'+imgUrl+'" class="image-choices-lightbox-btn gform-theme__disable-reset" ' + elementorAtts + ' rel="'+id+'"><i></i></a>');
							$lightboxBtn.data('caption', escapedTitle);
							$choice.prepend($lightboxBtn);
						}
					}

				}
			});

			imageChoices.setupProductImageChoices( $form.get(0) );

			setTimeout(function(){
				if ( $choices.length ) {
					imageChoices.SelectedFields( form_id );
				}
				imageChoices.InitLightbox( form_id );
				imageChoices.InitLazyLoad( form_id, current_page );
				if ( typeof jetslothMatchHeights !== 'undefined' ) {
					jetslothMatchHeights();
				}
				gform.doAction('gfic_setup', form_id);
			}, 100);

		}

	};
	window.imageChoices_SetUpFields = imageChoices.SetUpFields;// legacy support added in 1.3.0

	imageChoices.ConditionalLogic = function( form_id ) {
		// TODO: specific form by id
		var $form = ( typeof form_id !== 'undefined' && form_id !== '' ) ? $('#gform_'+form_id) : $('.gform_wrapper form');
		// WooCommerce Gravity Forms Product Add-Ons compatibility
		if ( !$form.length && $('.gform_variation_wrapper').length ) {
			$form = $('.gform_variation_wrapper');
		}

		var $choicesField = $form.find('.image-choices-field');
		if ($choicesField.length) {
			$choicesField.each(function(){
				var $field = $(this);
				if ($field.is(':hidden')) {
					$field.find('.image-choices-choice').removeClass(imageChoices.cssClasses.selected + ' ' + imageChoices.cssClasses.hover + ' ' + imageChoices.cssClasses.focus);
				}
			});
			setTimeout(function(){
				if ( typeof jetslothMatchHeights !== 'undefined' ) {
					jetslothMatchHeights();
				}
			}, 10);
		}
	};

	$(document).bind('gform_post_render', function(event, form_id, current_page){
		window.gformGetOptionLabel = imageChoices.gformGetOptionLabel;
		imageChoices.SetUpFields( form_id, current_page );
	});


	$(document).bind('gform_post_conditional_logic', function(event, form_id, fields, isInit){
		imageChoices.ConditionalLogic( form_id );
		imageChoices.SelectedFields( form_id );
	});

	$(document).on('gfcf/conversational/navigate/start gfcf/conversational/navigate/next gfcf/conversational/navigate/prev gfcf/conversational/navigate/error', function(){
		if ( jetslothMatchHeights ) {
			jetslothMatchHeights();
		}
	});

	imageChoices.gformGetOptionLabel = function(element, selected_value, current_price, form_id, field_id) {
		element = $(element);

		// Added for cross compat with Color Picker
		if (element.closest('.gfield').hasClass('color-picker-field') && typeof window.colorPicker_gformGetOptionLabel === 'function') {
			return window.colorPicker_gformGetOptionLabel(element, selected_value, current_price, form_id, field_id);
		}

		var wrap = element.closest('[class*="gchoice"]');// TODO: Update to just .gchoice
		var index = wrap.index();

		var price = gformGetPrice(selected_value);
		var current_diff = element.attr('price');
		var original_label = element.html().replace(/<span(.*)<\/span>/i, "").replace(current_diff, "");

		var diff = gformGetPriceDifference(current_price, price);
		diff = gformToNumber(diff) == 0 ? "" : " " + diff;
		element.attr('price', diff);

		//don't add <span> for drop down items (not supported)
		var price_label = element[0].tagName.toLowerCase() == "option" ? " " + diff : "<span class='ginput_price'>" + diff + "</span>";
		var label = original_label + price_label;

		//calling hook to allow for custom option formatting
		if(window["gform_format_option_label"])
			label = gform_format_option_label(label, original_label, price_label, current_price, price, form_id, field_id, index);

		return label;
	};
	window.imageChoices_gformGetOptionLabel = imageChoices.gformGetOptionLabel;// legacy support added in 1.3.0
	window.gformGetOptionLabel = imageChoices.gformGetOptionLabel;



	imageChoices.gform_format_option_label = function(fullLabel, fieldLabel, priceLabel, selectedPrice, price, formId, fieldId, index) {
		var markup = [fullLabel];
		var choiceText = `${fieldLabel}`;
		var choicePrice = `${priceLabel}`;

		var $field = $('#gform_'+formId+' .gfield#field_'+formId+'_'+fieldId);

		if ($field.length && $field.hasClass('image-choices-field')) {

			var $allOptions = $field.find('[class*="gchoice"]');// TODO: Update to just .gchoice ?
			var $thisOption = (typeof index !== 'undefined' && index >= 0) ? $allOptions.eq(index) : $allOptions;// when index is not passed in or not valid, get all options
			var loadedChoices = ( typeof $field.data('jetsloth-lazy-loaded') !== 'undefined' ) ? $field.data('jetsloth-lazy-loaded') : [];

			$thisOption.each(function(){

				var $option = $(this);

				var $thisOptionLabel = $option.find('label');
				var $thisOptionInput = $option.find('input');

				var thisOptionImage = $thisOptionLabel.data('img');
				var thisOptionLightboxSrc = $thisOptionLabel.data('lightbox-src');

				// Get the label content from the data attribute
				/*
				var fallbackFieldLabel = $thisOptionLabel.attr('data-text');
				if ( (typeof fieldLabel === 'undefined' || fieldLabel === '') && fallbackFieldLabel !== '') {
					fieldLabel = fallbackFieldLabel;
				}
				*/
				fieldLabel = $thisOptionLabel.attr('data-text');

				if ( priceLabel === fieldLabel ) {
					priceLabel = "";
				}

				if ( priceLabel === "<span class='ginput_price'></span>" ) {
					priceLabel = "<span class='ginput_price'>&nbsp;</span>";
				}

				var jmhId = formId.toString() + '_' + fieldId.toString();
				//var jmhAttr = ( !imageChoices.isLegacyMode() && ( $field.hasClass('ic-theme--cover-tile') || $field.hasClass('ic-image--natural') ) ) ? ' data-jmh="' + jmhId + '_' + index + '"' : '';
				var jmhAttr = ( !imageChoices.isLegacyMode() && ( $field.hasClass('ic-theme--cover-tile') || $field.hasClass('ic-image--natural') ) ) ? ' data-jmh="' + jmhId + '_image' + '"' : '';

				var hasLazyLoad = $field.hasClass('has-jetsloth-lazy');
				var shouldLazyLoad = ( hasLazyLoad && $.inArray( index, loadedChoices ) === -1 );
				var imageMarkup = [
					'<span class="image-choices-choice-image-wrap"' + jmhAttr + ' style="background-image:url('+thisOptionImage+');">',
						'<img src="'+thisOptionImage+'" class="image-choices-choice-image" alt="" data-lightbox-src="'+thisOptionLightboxSrc+'" />',
					'</span>',
				].join('');
				if ( shouldLazyLoad ) {
					imageMarkup = [
						'<span class="image-choices-choice-image-wrap jetsloth-lazy"' + jmhAttr + ' data-lazy-bg="'+thisOptionImage+'">',
							'<img src="" data-lazy-src="'+thisOptionImage+'" class="image-choices-choice-image jetsloth-lazy" alt="" data-lightbox-src="'+thisOptionLightboxSrc+'" />',
						'</span>',
					].join('');
				}

				//var jmhID = formId + '_' + fieldId;
				choiceText = gform.applyFilters( 'gfic_choice_text', fieldLabel, selectedPrice, price, formId, fieldId, index );
				choiceText = gform.applyFilters( `gfic_choice_text_${formId}`, choiceText, selectedPrice, price, formId, fieldId, index );
				choiceText = gform.applyFilters( `gfic_choice_text_${formId}_${fieldId}`, choiceText, selectedPrice, price, formId, fieldId, index );
				choiceText = gform.applyFilters( `gfic_choice_text_${formId}_${fieldId}_${index}`, choiceText, selectedPrice, price, formId, fieldId, index );

				choicePrice = gform.applyFilters( 'gfic_choice_price', priceLabel, selectedPrice, price, formId, fieldId, index );
				choicePrice = gform.applyFilters( `gfic_choice_price_${formId}`, choicePrice, selectedPrice, price, formId, fieldId, index );
				choicePrice = gform.applyFilters( `gfic_choice_price_${formId}_${fieldId}`, choicePrice, selectedPrice, price, formId, fieldId, index );
				choicePrice = gform.applyFilters( `gfic_choice_price_${formId}_${fieldId}_${index}`, choicePrice, selectedPrice, price, formId, fieldId, index );
				markup = [
					imageMarkup,
					'<span class="image-choices-choice-text">' + choiceText + '</span>',
					'<span class="image-choices-choice-price">' + choicePrice + '</span>'
				];

				if ($field.hasClass('image-choices-use-lightbox') && $option.find('.image-choices-lightbox-btn').length) {
					var escapedTitle = (fieldLabel + '').replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');
					var thumbUrl = thisOptionImage;
					var imgUrl = thisOptionLightboxSrc;
					if (typeof imgUrl === 'undefined' || imgUrl === '') {
						imgUrl = thumbUrl;
					}

					if (imgUrl !== '') {
						$option.find('.image-choices-lightbox-btn').attr('href', imgUrl).data('caption', escapedTitle).attr('rel', fieldId);
					}
				}

				if ( typeof jetslothMatchHeights !== 'undefined' ) {
					setTimeout(() => jetslothMatchHeights, 100);
				}

				if ( shouldLazyLoad ) {
					setTimeout(function(){
						$option.find('.jetsloth-lazy').each(function(){
							imageChoices.observer.observe( this );
						});
					}, 100);
				}
			});

		}

		var choiceHtml = gform.applyFilters( 'gfic_choice_html', markup.join(''), choiceText, choicePrice, selectedPrice, price, formId, fieldId, index );
		choiceHtml = gform.applyFilters( `gfic_choice_html_${formId}`, choiceHtml, choiceText, choicePrice, selectedPrice, price, formId, fieldId, index );
		choiceHtml = gform.applyFilters( `gfic_choice_html_${formId}_${fieldId}`, choiceHtml, choiceText, choicePrice, selectedPrice, price, formId, fieldId, index );
		choiceHtml = gform.applyFilters( `gfic_choice_html_${formId}_${fieldId}_${index}`, choiceHtml, choiceText, choicePrice, selectedPrice, price, formId, fieldId, index );
		return choiceHtml;
	};
	window.imageChoices_gform_format_option_label = imageChoices.gform_format_option_label;// legacy support added in 1.3.0


	window.gform_format_option_label = function(fullLabel, fieldLabel, priceLabel, selectedPrice, price, formId, fieldId, index) {

		if ( typeof gftt !== 'undefined' && typeof gftt.option_label_reinit !== 'undefined' ) {
			gftt.option_label_reinit( formId, fieldId );
		}

		// Added for cross compat with Color Picker
		var $field = $('#field_'+formId+'_'+fieldId);
		if ($field.length && $field.hasClass('color-picker-field') && typeof window.colorPicker_gform_format_option_label === 'function') {
			return window.colorPicker_gform_format_option_label(fullLabel, fieldLabel, priceLabel, selectedPrice, price, formId, fieldId, index);
		}

		return imageChoices.gform_format_option_label(fullLabel, fieldLabel, priceLabel, selectedPrice, price, formId, fieldId, index);
	}


	imageChoices.onImageLoaded = function( element ) {

		var $el = $(element);
		$el.addClass('jetsloth-lazy-loaded');

		var $field = $el.closest('.gfield');
		if ( !$field.length ) {
			return;
		}

		var index = $el.closest('li').index();
		var loadedChoices = ( typeof $field.data('jetsloth-lazy-loaded') !== 'undefined' ) ? $field.data('jetsloth-lazy-loaded') : [];

		loadedChoices.push( index );
		$field.data('jetsloth-lazy-loaded', loadedChoices);

		if ( typeof jetslothMatchHeights !== 'undefined' ) {
			setTimeout(() => jetslothMatchHeights, 100);
		}
	};

	imageChoices.loadImage = function( element ) {

		var $el = $(element);

		if ( typeof $el.data('lazy-bg') !== 'undefined' && $el.data('lazy-bg') !== '' ) {
			var img = new Image();
			img.onload = function() {
				$el.css('background-image', 'url(' + $el.data('lazy-bg') + ')');
				imageChoices.onImageLoaded( $el );
			};
			img.src = $el.data('lazy-bg');
		}
		else if ( typeof $el.data('lazy-src') !== 'undefined' && $el.data('lazy-src') !== '' ) {
			$el.on('load', function(){
				imageChoices.onImageLoaded( $el );
			});
			$el.attr('src', $el.data('lazy-src'));
		}

	};


	imageChoices.lazyLoad = function( elements ) {

		$.each(elements, function(i, item){
			if (item.intersectionRatio > 0) {
				imageChoices.observer.unobserve(item.target);
				imageChoices.loadImage(item.target);
			};
		});

	};

	var lazyLoadInit = false;
	imageChoices.InitLazyLoad = function( form_id, current_page ) {

		if ( !lazyLoadInit ) {
			// Set up the intersection observer to detect when to define
			// and load the real image source
			var lazyRootMargin = gform.applyFilters( 'gfic_lazy_root_margin', "100px", form_id, current_page );
			lazyRootMargin = gform.applyFilters( `gfic_lazy_root_margin_${form_id}`, lazyRootMargin, form_id, current_page );
			lazyRootMargin = gform.applyFilters( `gfic_lazy_root_margin_${form_id}_${current_page}`, lazyRootMargin, form_id, current_page );

			var lazyThreshold = gform.applyFilters( 'gfic_lazy_threshold', 1.0, form_id, current_page );
			lazyThreshold = gform.applyFilters( `gfic_lazy_threshold_${form_id}`, lazyThreshold, form_id, current_page );
			lazyThreshold = gform.applyFilters( `gfic_lazy_threshold_${form_id}_${current_page}`, lazyThreshold, form_id, current_page );
			var lazyLoadOptions = {
				rootMargin: lazyRootMargin,
				threshold: lazyThreshold
			};
			imageChoices.observer = new IntersectionObserver(imageChoices.lazyLoad, lazyLoadOptions);
			lazyLoadInit = true;
		}

		var $form = $('#gform_' + form_id);
		var $page = ( typeof current_page !== 'undefined' ) ? $form.find('#gform_page_' + form_id + '_' + current_page) : [];
		var $elements = ( $page.length ) ? $page.find('.image-choices-field .jetsloth-lazy, .product-image-field .jetsloth-lazy') : $form.find('.image-choices-field .jetsloth-lazy, .product-image-field .jetsloth-lazy');
		$elements.each(function(){
			imageChoices.observer.observe( this );
		});

	}

})(jQuery);
