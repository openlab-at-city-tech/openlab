'use strict';
jQuery(document).ready(function($) {
	//open visual helper switcher (Switch Template) if storage is set
	if ( localStorage.getItem('epkb_template_changed') ) {
		setTimeout(function() {
			$('.js-epkb-accordion-toggle#epkb-vshelp-switch-template').trigger('click');
		}, 100);

		//remove storage
		localStorage.removeItem('epkb_template_changed');
	}

	//Event for toggle visual helper switcher. Show/Hide the side menu
	$('.js-epkb-accordion-toggle').on('click', function (e) {
		e.preventDefault();

		// Close all other accordion bodies
		$('.epkb-vshelp-accordion-body').not($(this).parents('.epkb-vshelp-accordion-wrapper').find('.epkb-vshelp-accordion-body')).slideUp();

		// Toggle the current accordion body
		$(this).parents('.epkb-vshelp-accordion-wrapper').find('.epkb-vshelp-accordion-body').slideToggle();
	});

	//Event for toggle accordion inside the side menu
	$('.js-epkb-side-menu-toggle').on('change', function (e) {
		let checkboxInput = $(this),
			sideMenu = $('.epkb-vshelp-side-menu-wrapper'),
			infoIcons = $('.epkb-vshelp-info-icon'),
			infoModal = $('.epkb-vshelp-info-modal'),
			closeSwitcher = $('.epkb-vshelp-hide-switcher');

		if ( checkboxInput.prop('checked') ) {
			sideMenu.slideDown();
			infoIcons.show();
			closeSwitcher.addClass('epkb-vshelp-hide-switcher--hidden');
		} else {
			sideMenu.slideUp();
			infoIcons.hide();
			infoModal.hide();
			closeSwitcher.removeClass('epkb-vshelp-hide-switcher--hidden');
		}

		let postData = {
			action: 'epkb_visual_helper_update_switch_settings',
			setting_name: checkboxInput.attr('name'),
			kb_id: $(this).data('kbid'),
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
		}).success(function (response) {});
	})

	//Event for hide switcher
	$('.epkb-vshelp-hide-switcher').on('click', function () {

		// First, hide the toggle on the front-end
		$(this).parents('.epkb-vshelp-toggle-wrapper').hide();

		let postData = {
			action: 'epkb_visual_helper_update_switch_settings',
			kb_id: $(this).data('kbid'),
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
		}).success(function (response) {});
	})

	//Event for hide switcher template
	$('.epkb-settings-control-circle-radio input').on('change', function () {
		let postData = {
			action: 'epkb_visual_helper_switch_template',
			kb_id: $(this).data('kbid'),
			current_template: $(this).val(),
			prop_name: $(this).attr('name'),
		};

		if ( ! $('.eckb-kb-no-content').length ) {
			$('body').append('<div class="eckb-kb-no-content"></div>');
		}

		epkb_loading_Dialog( 'show', '', $('body .eckb-kb-no-content') );

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: epkb_vars.ajaxurl,
		}).success(function (response) {
			if ( response.success ) {
				//create local storage
				localStorage.setItem('epkb_template_changed', true);

				window.location.reload(true);
			} else {
				epkb_loading_Dialog( 'remove', '', $('#epkb-modular-main-page-container .eckb-kb-no-content') );
			}
		});
	})

	//Collect Page Parameters
	const pageConfigs = [
		{
			checkSelector: '#epkb-modular-main-page-container',
			prefix: 'mp',
			containerSelector: '#epkb-ml__module-categories-articles',
			searchSelector: '#epkb-ml__row-1',
		},
		{
			checkSelector: '#eckb-article-page-container-v2',
			prefix: 'ap',
			containerSelector: '#eckb-article-body',
			searchSelector: '#eckb-article-header',
		},
		{
			checkSelector: '#eckb-archive-body',
			prefix: 'cp',
			containerSelector: '#eckb-archive-body',
			searchSelector: '#eckb-archive-header',
		},
	];

	function epkbVisualHelperGetParams() {
		const windowWidth = $( 'body' ).width();
		let prefix = '';
		let containerWidth = 0;
		let searchWidth = 0;

		for ( const config of pageConfigs ) {
			if ( $( config.checkSelector ).length ) {
				prefix = config.prefix;
				containerWidth = Math.round( $( config.containerSelector ).width() );
				searchWidth = Math.round( $( config.searchSelector ).width() );
				break;
			}
		}

		if ( prefix ) {
			if ( searchWidth ) {
				$( `.js-epkb-${prefix}-search-width` ).text(`${searchWidth}px` );
			}

			if ( containerWidth ) {
				$( `.js-epkb-${prefix}-width-container`).text(`${containerWidth}px` );
			}

			if ( windowWidth ) {
				$( `.js-epkb-${prefix}-width` ).text(`${windowWidth}px` );
			}
		}
	}


	//Initialize Visual Helper Collect Page Parameters
	epkbVisualHelperGetParams();

	//Resize Visual Helper Collect Page Parameters
	$(window).on('resize', function() {
		epkbVisualHelperGetParams();
	})

	//Create Visual Helper Info Icons
	function createInfoIcons() {
		//Hide all modal winows
		let infoModals = $('.epkb-vshelp-info-modal');

		// If Visual Helper Toggle is 'off', then still render the info icons, but make them invisible; else render initially visible
		const infoIconVisibilityStyle = $('.js-epkb-side-menu-toggle').prop('checked') ? '' : ' ' + 'style="display: none;"';

		infoModals.each(function (index, element) {
			// Collect icon position parameters
			let currentElement = $(element),
				selectors = currentElement.data('selectors'),
				selectorsList = selectors.split(',');

			let i = 0;
			for (let selector of selectorsList) {
				let selectorElement = $(selector).eq(0);

				if (selectorElement.length) {
					let modalId = currentElement.data('section-id');

					selectorElement.addClass('epkb-vshelp--has-info-icon');
					selectorElement.append('<span class="ep_font_icon_info epkb-vshelp-info-icon epkb-vshelp-info-icon--'+ modalId +'-'+ i +'" data-section-id="'+ modalId +'"' + infoIconVisibilityStyle + '></span>');
					i++;
				}
			}
		});
	}

	//Initialize Visual Helper Info Icons
	createInfoIcons();

	//Event for toggle info modal
	$(document).on('click', '.epkb-vshelp-info-icon', function (e) {
		e.stopPropagation();
		e.preventDefault();
		$('.epkb-vshelp-background').remove();
		
		let dataSectionId = $(this).data('section-id'),
				elementPosition = $(this).offset(),
				elementPosY = elementPosition.top,
				elementPosX = elementPosition.left,
				modal = $('.epkb-vshelp-info-modal[data-section-id="' + dataSectionId + '"]'),
				triangleCssClass = 'epkb-vshelp-info-modal--triangle-right';

		if (modal.length) {
			if ($(this).hasClass('epkb-vshelp-info-modal--active')) {
				$(this).removeClass('epkb-vshelp-info-modal--active');
				modal.hide();
			} else {

				$('#epkb-modular-main-page-container, #eckb-article-page-container-v2, #eckb-archive-page-container').append('<div class="epkb-vshelp-background"></div>')
				let positionCase = '';

				if (elementPosY > (modal.find('.epkb-vshelp-info-modal__title').outerHeight() + 35)) {
					elementPosY = elementPosY - (modal.find('.epkb-vshelp-info-modal__title').outerHeight() / 2) - $(this).outerHeight();
				}

				if (elementPosX > (modal.outerWidth() + 35)) {
					positionCase = 'rightSpace';
				} else if ($(window).outerWidth() - elementPosX > (modal.outerWidth() + 35)) {
					positionCase = 'leftSpace';
				} else {
					positionCase = 'center';
				}

				switch (positionCase) {
					case 'rightSpace':
						// if right space is enough for modal window
						elementPosX = elementPosX - modal.outerWidth() - 35;
						break;

					case 'leftSpace':
						// if Left space is enough for modal window
						elementPosX = elementPosX + $(this).outerHeight() + 35;
						triangleCssClass = 'epkb-vshelp-info-modal--triangle-left';
						break;

					case 'center':
						// if right and left space is not enough for modal window
						elementPosX = ($(window).outerWidth() / 2) - (modal.outerWidth() / 2);
						elementPosY = elementPosY + $(this).outerHeight() + 70;
						triangleCssClass = '';
						break;
				}

				modal.css({
					'top': elementPosY + 'px',
					'left': elementPosX + 'px'
				});

				$('.epkb-vshelp-info-icon').removeClass('epkb-vshelp-info-modal--active');
				$('.epkb-vshelp-info-modal').hide().removeClass('epkb-vshelp-info-modal--triangle-right epkb-vshelp-info-modal--triangle-left');

				$(this).addClass('epkb-vshelp-info-modal--active');
				modal.addClass(triangleCssClass).show();
			}
		}
	});

	// Hide modal if the click is outside of the icon or modal
	$(document).on('click', function (e) {
		if (!$(e.target).closest('.epkb-vshelp-info-icon, .epkb-vshelp-info-modal').length) {
			$('.epkb-vshelp-info-icon').removeClass('epkb-vshelp-info-modal--active');
			$('.epkb-vshelp-info-modal').hide();
			$('.epkb-vshelp-background').remove();
		}
	});

	function epkb_loading_Dialog( displayType, message, parent_container ){

		if ( displayType === 'show' ) {

			let output =
				'<div class="epkb-admin-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>' +
				'<div class="epkb-admin-dialog-box-overlay"></div>';

			//Add message output at the end of Body Tag
			parent_container.append( output );

		} else if( displayType === 'remove' ) {

			// Remove loading dialogs.
			parent_container.find( '.epkb-admin-dialog-box-loading' ).remove();
			parent_container.find( '.epkb-admin-dialog-box-overlay' ).remove();
		}
	}
});