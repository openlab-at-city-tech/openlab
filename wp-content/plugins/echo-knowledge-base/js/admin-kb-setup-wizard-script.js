jQuery(document).ready(function($) {

	let wizard = $( '#epkb-config-wizard-content' );

	// If the Wizard is not detected don't run scripts.
	if ( wizard.length <= 0 ) {
		return;
	}

	let admin_report_error_form = $( '.epkb-admin__error-form__container' );

	/**
	 * Handle Setup Wizard Apply Button
	 */
	wizard.find( '.epkb-setup-wizard-button-apply' ).on( 'click' , function(e){

		let menu_ids = [];

		let postData = {
			action: 'epkb_apply_setup_wizard_changes',
			_wpnonce_epkb_ajax_action: $('#_wpnonce_epkb_ajax_action').val(),
			epkb_wizard_kb_id: $('#epkb_wizard_kb_id').val(),
			sidebar_selection: '0'
		};

		let layout_name = $('input[name="epkb-layout"]:checked').val();
		if ( typeof layout_name == 'undefined' ) {
			layout_name = '';
		}

		if ( $('.epkb-menu-checkbox input[type=checkbox]:checked').length ) {
			$('.epkb-menu-checkbox input[type=checkbox]:checked').each(function(){
				menu_ids.push($(this).prop('name').split('epkb_menu_')[1]);
			});
		}

		if ( $('.epkb-setup-wizard-sidebar input[type=radio]:checked').length ) {
			postData.sidebar_selection = $('.epkb-setup-wizard-sidebar .epkb-setup-wizard__featured-img-container--active').data('value');
		}

		let categories_articles_sidebar_location = $( 'input[name=categories_articles_sidebar_location]:checked' ).val();
		if ( typeof categories_articles_sidebar_location == 'undefined' ) {
			categories_articles_sidebar_location = '';
		}

		let module_row_toggles = $( '.epkb-setup-wizard-step-container--modules .epkb-setup-wizard-module-row .epkb-setup-wizard-module-row-toggle' );
		let row_1_module = typeof module_row_toggles[0] == 'undefined' ? '' : $( module_row_toggles[0] ).find( 'input:checked' ).val();
		let row_2_module = typeof module_row_toggles[1] == 'undefined' ? '' : $( module_row_toggles[1] ).find( 'input:checked' ).val();
		let row_3_module = typeof module_row_toggles[2] == 'undefined' ? '' : $( module_row_toggles[2] ).find( 'input:checked' ).val();
		let row_4_module = typeof module_row_toggles[3] == 'undefined' ? '' : $( module_row_toggles[3] ).find( 'input:checked' ).val();
		let row_5_module = typeof module_row_toggles[4] == 'undefined' ? '' : $( module_row_toggles[4] ).find( 'input:checked' ).val();

		let categories_articles_preset_name = $( '.epkb-setup-wizard-step-container--presets .epkb-setup-wizard-module-settings-row--active input:checked' ).val();
		if ( typeof categories_articles_preset_name == 'undefined' ) {
			categories_articles_preset_name = '';
		}

		// Set preset value to 'current' only if user did not select any preset
		if ( categories_articles_preset_name.length && ! wizard.hasClass( 'epkb-config-setup-wizard-modular--first-setup' ) ) {
			let apply_preset_toggle = $( '[name="epkb-setup-wizard-theme-content-show-option__toggle"]' );
			let current_layout = apply_preset_toggle.closest( '.epkb-setup-wizard-theme-content-show-option' ).data( 'current-layout' );
			if ( ! apply_preset_toggle.prop( 'checked' ) && current_layout === layout_name ) {
				categories_articles_preset_name = 'current';
			}
		}

		postData.layout_name = layout_name;
		postData.kb_name = $('.epkb-wizard-name input').val();
		postData.kb_slug = $('.epkb-wizard-slug input').val();
		postData.menu_ids = menu_ids;

		postData.categories_articles_sidebar_location = categories_articles_sidebar_location;
		postData.row_1_module = row_1_module;
		postData.row_2_module = row_2_module;
		postData.row_3_module = row_3_module;
		postData.row_4_module = row_4_module;
		postData.row_5_module = row_5_module;
		postData.categories_articles_preset_name = categories_articles_preset_name;
		postData.kb_main_page_type = $( '[name="epkb-main-page-type"]' ).length ? $( '[name="epkb-main-page-type"]:checked' ).val() : '';

		epkb_send_ajax ( postData );
	});

	/**
	 * Button JS for next and prev Step.
	 */
	wizard.find( '.epkb-setup-wizard-button-next, .epkb-setup-wizard-button-prev' ).on( 'click' , function(e){
		e.preventDefault();
		let nextStep = Number( $(this).val() );
		setup_wizard_switch_step( nextStep );
	} );

	/**
	 * Select Steps by Steps Bar
	 */
	$( document ).on( 'click', '.epkb-setup-wizard-step-tab', function() {
		let nextStep = Number( $( this ).data( 'step' ) );
		setup_wizard_switch_step( nextStep );
	} );

	/**
	 * Switch steps for Setup Wizard
	 */
	function setup_wizard_switch_step( nextStep ) {

		// Steps Bar
		setup_wizard_highlight_step_tabs( nextStep );

		// Header
		$( '.epkb-wc-step-header' ).removeClass( 'epkb-wc-step-header--active' );
		const next_step_header = $( '.epkb-wc-step-header--' + nextStep );
		next_step_header.addClass( 'epkb-wc-step-header--active' );

		modular_setup_wizard_switch_step( nextStep );

		// Presets Content Show Option
		const content_show_option = $('.epkb-config-setup-wizard-modular .epkb-setup-wizard-theme-content-show-option');
		if ( content_show_option.length > 0 && next_step_header.hasClass( 'epkb-wc-step-header--design' ) ) {

			let selected_layout = $('.epkb-config-setup-wizard-modular input[name="epkb-layout"]:checked').val();
			if ( typeof selected_layout == 'undefined' ) {
				selected_layout = '';
			}
			const wizard_content = $('.epkb-config-setup-wizard-modular .epkb-wizard-content .eckb-wizard-step-design');
			content_show_option.show();
			if ( $( '.epkb-config-setup-wizard-modular input[name=epkb-setup-wizard-theme-content-show-option__toggle]' ).prop('checked') ) {
				wizard_content.addClass('epkb-wc-step-panel--active');
			} else {
				wizard_content.removeClass('epkb-wc-step-panel--active');
			}
		}

		// Scroll page to top
		wizard_scroll_to_top();
	}

	$( document ).on( 'change', '.epkb-config-setup-wizard-modular input[name=epkb-setup-wizard-theme-content-show-option__toggle]', function() {
		const wizard_content = $('.epkb-config-setup-wizard-modular .epkb-wizard-content .eckb-wizard-step-design');
		if ( $( this ).prop('checked') ) {
			wizard_content.addClass('epkb-wc-step-panel--active');
		} else {
			wizard_content.removeClass('epkb-wc-step-panel--active');
		}
	});

	function updateWizardSidebar() {
		const navigation = $('.epkb-setup-wizard-sidebar input[name=article_navigation]:checked').val();
		const location = $('.epkb-setup-wizard-sidebar input[name=article_location]:checked').val();
		let value = 7;
		if (navigation === 'categories_articles') {
			value = location === 'left' ? 1 : 2;
		} else if (navigation === 'top_categories') {
			value = location === 'left' ? 3 : 4;
		} else if (navigation === 'current_category_articles') {
			value = location === 'left' ? 5 : 6;
		}
		$('.epkb-setup-wizard-sidebar .epkb-setup-wizard__featured-img-container').removeClass('epkb-setup-wizard__featured-img-container--active');
		$(`.epkb-setup-wizard-sidebar .epkb-setup-wizard__featured-img-container[data-value=${value}]`).addClass('epkb-setup-wizard__featured-img-container--active');
	}

	$(document).on('change', '.epkb-setup-wizard-sidebar input[name=article_navigation]', updateWizardSidebar);
	$(document).on('change', '.epkb-setup-wizard-sidebar input[name=article_location]', updateWizardSidebar);

	/**
	 * Switch steps for Modular version of Setup Wizard
	 */
	function modular_setup_wizard_switch_step( nextStep ) {

		// Content
		$( '.epkb-wc-step-panel' ).removeClass( 'epkb-wc-step-panel--active' );
		$( '.eckb-wizard-step-' + nextStep ).addClass( 'epkb-wc-step-panel--active' );

		// Hide all buttons
		$( '.epkb-wc-step-panel-button' ).removeClass( 'epkb-wc-step-panel-button--active' );

		let total_steps_number = $( '.epkb-wizard-content .epkb-wc-step-panel' ).length;

		// Show First Step button only for first step
		if ( nextStep === 1 ) {
			$( '.epkb-wsb-step-1-panel-button' ).addClass( 'epkb-wc-step-panel-button--active' );
		}

		// Show Middle Step button for all steps except of first and last steps
		if ( nextStep !== 1 && nextStep < total_steps_number ) {
			let middle_steps_buttons_panel = $( '.epkb-wsb-step-2-panel-button' );
			middle_steps_buttons_panel.addClass( 'epkb-wc-step-panel-button--active' );

			// Update prev/next buttons value
			middle_steps_buttons_panel.find( '.epkb-setup-wizard-button-prev' ).val( nextStep - 1 );
			middle_steps_buttons_panel.find( '.epkb-setup-wizard-button-next' ).val( nextStep + 1 );
		}

		// Show Last Step button only for last step
		if ( nextStep === total_steps_number ) {
			$( '.epkb-wsb-step-3-panel-button' ).addClass( 'epkb-wc-step-panel-button--active' );
		}
	}

	/**
	 * Highlight all completed steps in status bar.
	 */
	function setup_wizard_status_bar_highlight_completed_steps( nextStep ){

		// Clear Completed Classes
		wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).removeClass( 'epkb-wsb-step--completed' );

		wizard.find( '.epkb-wizard-status-bar .epkb-wsb-step' ).each( function(){

			// Get each Step ID
			let id = $( this ).attr( 'id' );

			// Get last character the number of each ID
			let lastChar = id[id.length -1];

			// If the ID is less than the current step then add completed class.
			if( lastChar < nextStep ){
				$( this ).addClass( 'epkb-wsb-step--completed' );
			}
		});
	}

	/**
	 * Highlight step tabs
	 */
	function setup_wizard_highlight_step_tabs( nextStep ) {

		// Unselect all tabs
		wizard.find( '.epkb-setup-wizard-step-tab' ).removeClass( 'epkb-setup-wizard-step-tab--active epkb-setup-wizard-step-tab--completed' );

		let current_tab = wizard.find( '.epkb-setup-wizard-step-tab--' + nextStep );

		// Highlight current tab as active
		current_tab.addClass( 'epkb-setup-wizard-step-tab--active' );

		// Highlight left tabs as completed
		current_tab.prevAll( '.epkb-setup-wizard-step-tab' ).addClass( 'epkb-setup-wizard-step-tab--completed' );
	}

	/**
	 * Quickly scroll the user back to the top.
	 */
	function wizard_scroll_to_top(){
		$("html, body").animate({ scrollTop: 0 }, 0);
	}

	/**
	 * Highlight selected theme
	 */
	$( '.epkb-setup-wizard-step__item-description__option__label' ).on( 'click', function () {
		$( this ).closest( '.epkb-wc-step-panel' ).find( '.epkb-setup-option-container' ).removeClass( 'epkb-setup-option-container--active' );
		$( this ).closest( '.epkb-setup-wizard-step__item-content' ).find('.epkb-setup-option-container').addClass( 'epkb-setup-option-container--active' );
	});

	/**
	 * Highlight selected main page type
	 */
	$( '.epkb-setup-wizard-features-choice__option__label' ).on( 'click', function () {
		$( this ).closest( '.epkb-wc-step-panel' ).find( '.epkb-setup-wizard-features-choice' ).removeClass( 'epkb-setup-wizard-features-choice--active' );
		$( this ).closest( '.epkb-setup-wizard-features-choice' ).addClass( 'epkb-setup-wizard-features-choice--active' );
	});

	// For wizard step layout: click on image will popup
	$('.eckb-wizard-step-layout .epkb-setup-option__featured-img').on( 'click', function(){
		let imageUrl = $( this ).attr( 'src' );

		let popupHtml = `
			<div class="epkb-config-wizard-image-popup-overlay">
				<div class="epkb-config-wizard-image-popup-container">
					<img src="${ imageUrl }" alt="">
					<span class="epkb-config-wizard-image-close-popup">&times;</span>
				</div>
			</div>`;

		$( 'body' ).append( popupHtml );

		$( '.epkb-config-wizard-image-popup-container' ).click( function() {
			$( this ).toggleClass( 'epkb-zoomed' );
		});

		$( '.epkb-config-wizard-image-close-popup' ).click( function() {
			$( '.epkb-config-wizard-image-popup-overlay' ).remove();
			removeClickListener();
		});

		const outsideClickListener = ( event ) => {
			$target = $( event.target );
			if ( !$target.closest( '.epkb-config-wizard-image-popup-container' ).length && $( '.epkb-config-wizard-image-popup-container' ).is(':visible') ) {
				$( '.epkb-config-wizard-image-popup-overlay' ).remove();
				removeClickListener();
			}
		};
		const removeClickListener = () => {
			document.removeEventListener( 'click', outsideClickListener );
		};
		setTimeout( function () {
			document.addEventListener( 'click', outsideClickListener );
		}, 10);
	});
	/**
	 * Live change of KB Main Page slug
	 */
	$( '.epkb-wizard-slug input[type="text"]' ).on( 'input', function () {
		$( '#epkb-wizard-slug-target' ).text( $( this ).val() );
	});
	/**
	 * If Elegant Layouts is disabled, the Resource Links row will be left in Inactive Rows
	 */
	( function () {
		let resource_module_row = $( '.epkb-setup-wizard-module-row--resource-link--disabled' );
		if ( resource_module_row.length ) {
			$( '.epkb-setup-wizard-hidden-rows-title' ).addClass( 'epkb-setup-wizard-hidden-rows-title--active' );
			let rows_list_container = resource_module_row.closest( '.epkb-setup-wizard-module-rows-list' );
			rows_list_container.append( resource_module_row );
		}
	})();
	/**
	 * Switch visibility of Module Row in Modules step
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-module-row-toggle input', function() {
		let row_toggle_value = $( this ).val();
		let module_row = $( this ).closest( '.epkb-setup-wizard-module-row' );
		let module_name = module_row.data( 'row-module');
		let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
		let layout_step = wizard.find( '.epkb-setup-wizard-step-container--layout' );
		let rows_list_container = $( this ).closest( '.epkb-setup-wizard-module-rows-list' );

		// Disable Module for current Row
		if ( row_toggle_value === 'none' ) {
			module_row.removeClass( 'epkb-setup-wizard-module-row--active' );

			// Move Row to the end of Rows list
			rows_list_container.append( module_row );

			// keep the row background for 30 seconds when the row moved to inactive rows.
			module_row.removeClass( 'epkb-setup-wizard-module-row--activated' );
			module_row.addClass( 'epkb-setup-wizard-module-row--inactivated' );
			setTimeout( () => {
				module_row.removeClass( 'epkb-setup-wizard-module-row--inactivated' );
			}, 30000);

		// Enable Module for current Row
		} else {
			module_row.addClass( 'epkb-setup-wizard-module-row--active' );

			// Move Row with Module after nearest active Row or to the beginning of Rows list if now active Rows found
			let insert_after_row = rows_list_container.find( '.epkb-setup-wizard-module-row--active:not([data-row-module="' + module_name + '"])' ).last();
			if ( insert_after_row.length ) {
				module_row.insertAfter( insert_after_row );
			} else {
				rows_list_container.prepend( module_row );
			}

			// keep the row background for 30 seconds when the row removed from inactive rows.
			module_row.removeClass( 'epkb-setup-wizard-module-row--inactivated' );
			module_row.addClass( 'epkb-setup-wizard-module-row--activated' );
			setTimeout( () => {
				module_row.removeClass( 'epkb-setup-wizard-module-row--activated' );
			}, 30000);

		}

		// Show title above hidden Rows only if any of the Rows is hidden
		if ( $( '.epkb-setup-wizard-step-container--modules .epkb-setup-wizard-module-row' ).length - $( '.epkb-setup-wizard-step-container--modules .epkb-setup-wizard-module-row--active' ).length ) {
			$( '.epkb-setup-wizard-hidden-rows-title' ).addClass( 'epkb-setup-wizard-hidden-rows-title--active' );
		} else {
			$( '.epkb-setup-wizard-hidden-rows-title' ).removeClass( 'epkb-setup-wizard-hidden-rows-title--active' );
		}

		// Show/Hide message that Categories & Articles module was not selected
		if ( module_name === 'categories_articles' ) {
			if ( row_toggle_value === 'none' ) {
				layout_step.addClass( 'epkb-setup-wizard-step-container--hide' );
				presets_step.addClass( 'epkb-setup-wizard-step-container--hide' );
				wizard.find( '.epkb-setup-wizard-no-categories-articles-message' ).addClass( 'epkb-setup-wizard-no-categories-articles-message--active' );
			} else {
				layout_step.removeClass( 'epkb-setup-wizard-step-container--hide' );
				presets_step.removeClass( 'epkb-setup-wizard-step-container--hide' );
				wizard.find( '.epkb-setup-wizard-no-categories-articles-message' ).removeClass( 'epkb-setup-wizard-no-categories-articles-message--active' );
			}
		}
	} );

	/**
	 * Change sequence for Module Row
	 */
	$( document ).on( 'click', '.epkb-setup-wizard-module-row-sequence', function() {
		let current_row = $( this ).closest( '.epkb-setup-wizard-module-row' );

		// Do nothing for disabled rows
		if ( ! current_row.hasClass( 'epkb-setup-wizard-module-row--active' ) ) {
			return;
		}

		// keep the row selected with a purple border and bold heading for 2 seconds once the row moves
		current_row.addClass( 'epkb-setup-wizard-module-row--selected' );
		setTimeout( () => {
			current_row.removeClass( 'epkb-setup-wizard-module-row--selected' );
		}, 30000);

		if ( $( this ).hasClass( 'epkb-setup-wizard-module-row-sequence--up' ) ) {
			let insert_before_target = current_row.prev();
			if ( insert_before_target.length ) {
				current_row.slideUp( 500, function() {
					current_row.insertBefore( insert_before_target );
					current_row.slideDown( 500 );
				});
			}
		} else {
			let insert_after_target = current_row.next();
			if ( insert_after_target.length && insert_after_target.hasClass( 'epkb-setup-wizard-module-row--active' )  ) {
				current_row.slideUp( 500, function() {
					current_row.insertAfter( insert_after_target );
					current_row.slideDown( 500 );
				});
			}
		}
	} );

	/**
	 * Switch Sidebar visibility in Modules step
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-module-sidebar-selector input', function() {
		let current_step = $( this ).closest( '.epkb-setup-wizard-step-container--modules' );

		// Hide Left and Right Sidebars
		current_step.find( '.epkb-setup-wizard-module-sidebar' ).removeClass( 'epkb-setup-wizard-module-sidebar--active' );
		current_step.find( '.epkb-setup-wizard-module--categories_articles' ).removeClass( 'epkb-setup-wizard-module-sidebar--active' );

		// Show Left Sidebar if selected
		if ( $( this ).val() === 'left' ) {
			current_step.find( '.epkb-setup-wizard-module-sidebar--left' ).addClass( 'epkb-setup-wizard-module-sidebar--active' );
			current_step.find( '.epkb-setup-wizard-module--categories_articles' ).addClass( 'epkb-setup-wizard-module-sidebar--active' );
		}

		// Show Right Sidebar if selected
		if ( $( this ).val() === 'right' ) {
			current_step.find( '.epkb-setup-wizard-module-sidebar--right' ).addClass( 'epkb-setup-wizard-module-sidebar--active' );
			current_step.find( '.epkb-setup-wizard-module--categories_articles' ).addClass( 'epkb-setup-wizard-module-sidebar--active' );
		}
	} );

	/**
	 * Switch Presets Preview when user changes Layout
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-step-container--layout input[name="epkb-layout"]', function() {
		let layout_name = $( this ).val();

		// Preview for Modules step
		let modules_step = wizard.find( '.epkb-setup-wizard-step-container--modules' );
		modules_step.find( '.epkb-setup-wizard-module--categories_articles' ).find( '.epkb-setup-wizard-module-layout' ).removeClass( 'epkb-setup-wizard-module-layout--active' );
		modules_step.find( '.epkb-setup-wizard-module--categories_articles' ).find( '.epkb-setup-wizard-module-layout--' + layout_name ).addClass( 'epkb-setup-wizard-module-layout--active' );

		// Preview for Presets step
		let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
		presets_step.find( '.epkb-setup-wizard-module-layout' ).removeClass( 'epkb-setup-wizard-module-layout--active' );
		presets_step.find( '.epkb-setup-wizard-module-settings-row' ).removeClass( 'epkb-setup-wizard-module-settings-row--active' );
		presets_step.find( '.epkb-setup-wizard-module-layout--' + layout_name ).addClass( 'epkb-setup-wizard-module-layout--active' );
		presets_step.find( '.epkb-setup-wizard-module-settings-row--' + layout_name ).addClass( 'epkb-setup-wizard-module-settings-row--active' );

		update_article_navigation( layout_name );
	} );

	function update_article_navigation( layout_name ) {

		// Hide 'none' choice of Article Navigation if Sidebar layout is active
		let article_navigation_input = wizard.find( '.epkb-setup-wizard-option__navigation-selector' );
		let hide_none_on_layout = article_navigation_input.data( 'hide-none-on-layout' );
		let current_article_navigation = article_navigation_input.data( 'current-value' );
		if ( typeof hide_none_on_layout !== 'undefined' && layout_name === hide_none_on_layout ) {
			article_navigation_input.find( '[name="article_navigation"][value="none"]' ).parent( '.epkb-input-container' ).hide();

			// Select value which user has in configuration or first value if 'none' is current configuration value
			if ( current_article_navigation === 'none' ) {
				$( article_navigation_input.find( '[name="article_navigation"]' )[0] ).prop( 'checked', true ).trigger( 'change' );
			} else {
				article_navigation_input.find( '[name="article_navigation"][value="' + current_article_navigation + '"]' ).prop( 'checked', true ).trigger( 'change' );
			}

		// Show 'none' choice
		} else {
			article_navigation_input.find( '[name="article_navigation"][value="none"]' ).parent( '.epkb-input-container' ).show().trigger( 'change' );

			// Always select value which user has in configuration when user changes Layout (Sidebar layout does not have 'none' option for Article Navigation)
			article_navigation_input.find( '[name="article_navigation"][value="' + current_article_navigation + '"]' ).prop( 'checked', true ).trigger( 'change' );
		}
	}
	update_article_navigation( wizard.find( '.epkb-setup-wizard-step-container--layout input[name="epkb-layout"]:checked' ).val() );

	/**
	 * Switch Presets Preview when user changes Preset
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-module-preset-selector input', function() {
		let preset_name = $( this ).val();
		let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
		let active_layout = presets_step.find( '.epkb-setup-wizard-module-layout--active' );
		active_layout.find( '.epkb-setup-wizard-module-preset' ).removeClass( 'epkb-setup-wizard-module-preset--active' );
		active_layout.find( '.epkb-setup-wizard-module-preset--' + preset_name ).addClass( 'epkb-setup-wizard-module-preset--active' );
	} );


	/*************************************************************************************************
	 *
	 *          Utilities
	 *
	 ***********************************************************************************************/

	/**
	 * Displays a Center Dialog box with a loading icon and text.
	 *
	 * This should only be used for indicating users that loading or saving or processing is in progress, nothing else.
	 * This code is used in these files, any changes here must be done to the following files.
	 *   - admin-plugin-pages.js
	 *   - admin-kb-config-scripts.js
	 *   - admin-kb-wizard-script.js
	 *	 - admin-kb-setup-wizard-script.js
	 * @param  {string}    displayType     Show or hide Dialog initially. ( show, remove )
	 * @param  {string}    message         Optional    Message output from database or settings.
	 *
	 * @return {html}                      Removes old dialogs and adds the HTML to the end body tag with optional message.
	 */
	function epkb_loading_Dialog( displayType, message ){

		if ( displayType === 'show' ){

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
			$( 'body' ).append( output );

		} else if( displayType === 'remove' ){
			// Remove loading dialogs.
			$( '.epkb-admin-dialog-box-loading' ).remove();
			$( '.epkb-admin-dialog-box-overlay' ).remove();
		}
	}

	/**
	 * SHOW INFO MESSAGES
	 */
	function epkb_admin_notification( $title, $message , $type ) {

		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? $message : '') +
			'</span>' +
			'</div>' +
			'</div>';
	}

	// generic AJAX call handler
	function epkb_send_ajax( postData ) {

		let errorMsg;
		let theResponse;

		// Show message about creating demo KB if Setup Wizard run first time for default KB
		let loading_dialog_message = wizard.hasClass( 'epkb-config-setup-wizard-modular--first-setup' ) && parseInt( $( '#epkb_wizard_kb_id' ).val() ) === 1
			? epkb_vars.creating_demo_data
			: epkb_vars.saving_changes;

		$.ajax({
			type: 'POST',
			dataType: 'json',
			data: postData,
			url: ajaxurl,
			beforeSend: function (xhr)
			{
				epkb_loading_Dialog( 'show', loading_dialog_message );
			}
		}).done( function( response ) {
			theResponse = ( response ? response : '' );

			epkb_loading_Dialog( 'remove', '' );

			// Error in response
			if ( theResponse.error || typeof theResponse.message === 'undefined' ) {
				//noinspection JSUnresolvedVariable,JSUnusedAssignment
				errorMsg = theResponse.message ? theResponse.message : epkb_admin_notification('', epkb_vars.reload_try_again, 'error');
				$(admin_report_error_form).find('.epkb-admin__error-form__title').text(epkb_vars.setup_wizard_error_title);
				$(admin_report_error_form).find('.epkb-admin__error-form__desc').text(epkb_vars.setup_wizard_error_desc);
				$(admin_report_error_form).find('#epkb-admin__error-form__message').val('Setup Wizard: ' + $(errorMsg).text().trim());
				$(admin_report_error_form).css('display', 'block', 'important');
				return;
			}

			// Success in response - redirect to 'Need Help?' page
			if ( theResponse.redirect_to_url && theResponse.redirect_to_url.length > 0 ) {
				$('#epkb-wizard-success-message').addClass('epkb-dialog-box-form--active');
				$('#epkb-wizard-success-message .epkb-accept-button').on('click', function () {
					window.location = theResponse.redirect_to_url;
				});
			}

		} ).fail( function() {
			epkb_loading_Dialog( 'remove', '' );

			// On internal server error assume the error is outside Setup Wizard - force finish the Setup Wizard like on success
			let current_url = window.location.href;
			let success_url = current_url.replace( '&page=epkb-kb-configuration&setup-wizard-on', '&page=epkb-kb-need-help&epkb_after_kb_setup' );
			$('#epkb-wizard-success-message').addClass('epkb-dialog-box-form--active');
			$('#epkb-wizard-success-message .epkb-accept-button').on('click', function () {
				window.location = success_url;
			});
		} );
	}

	// PREVIEW POPUP
	(function(){
		// New ToolTip
		wizard.on( 'click', '.epkb__option-tooltip__button', function(){
			const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
			let tooltip_on = tooltip_contents.css('display') == 'block';

			tooltip_contents.fadeOut();

			if ( ! tooltip_on ) {
				clearTimeout(timeoutOptionTooltip);
				tooltip_contents.fadeIn();
			}
		});
		let timeoutOptionTooltip;
		wizard.on( 'mouseenter', '.epkb__option-tooltip__button, .epkb__option-tooltip__contents', function(){
			const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
			clearTimeout(timeoutOptionTooltip);
			tooltip_contents.fadeIn();
		});

		wizard.on( 'mouseleave', '.epkb__option-tooltip__button, .epkb__option-tooltip__contents', function(){
			const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
			timeoutOptionTooltip = setTimeout( function() {
				tooltip_contents.fadeOut();
			}, 1000);
		});

		// Add "Hide Row" / "Show Row" tooltip
		wizard.find('#epkb-wsb-step-2-panel .epkb-setup-wizard-module-row .epkb-setup-wizard-module-row-toggle .epkb-radio-buttons-container .epkb-input-container').each( function ( ind, elem ) {
			const radio_button_value = $( elem ).find( 'input.epkb-input' ).val();
			if ( radio_button_value === 'none' ) {
				$( elem ).find( 'label.epkb-label' ).attr('data-tooltip', 'Hide Row' );
			} else {
				$( elem ).find( 'label.epkb-label' ).attr('data-tooltip', 'Show Row' );
			}
		});

		// Add "Resource Links" Activate button click event
		wizard.find( '#epkb-wsb-step-2-panel .epkb-setup-wizard-module-row .epkb-setup-wizard-module-row-right-settings .epkb-setup-wizard-module-row--resource-links-activate' ).on( 'click', function( e ){
			$('#epkb-dialog-pro-feature-ad-resource-links').addClass( 'epkb-dialog-pro-feature-ad--active' );
		});

		// Add "Layout Setup" Choose button click event
		wizard.find( '#epkb-wsb-step-3-panel .epkb-setup-wizard-step__item .epkb-setup-wizard-step__item-description .epkb-setup-wizard-step__item-description__button-pro' ).on( 'click', function( e ){
			const popup_id = $( this ).data( "target" );
			$( '#' + popup_id ).addClass( 'epkb-dialog-pro-feature-ad--active' );
		});

		wizard.find( '.epkb-dialog-pro-feature-ad .epkb-dbf__close' ).on( 'click',function(){
			$( this ).closest( '.epkb-dialog-pro-feature-ad' ).removeClass( 'epkb-dialog-pro-feature-ad--active' );
		});

		$( document ).on( 'click', function (e){
			let target = $( e.target );
			if ( ! target.closest( '.epkb-setup-wizard-module-row--resource-links-activate' ).length && ! target.closest( '.epkb-dialog-pro-feature-ad' ).length && ! target.closest( '.epkb-setup-wizard-step__item-description__button-pro' ).length ) {
				$( '.epkb-dialog-pro-feature-ad' ).removeClass( 'epkb-dialog-pro-feature-ad--active' );
			}
		});

		//Open Popup larger Image
		wizard.find( '.eckb-wizard-step-2 .epkb-setup-option__featured-img-container' ).on( 'click', function( e ){

			e.preventDefault();
			e.stopPropagation();

			wizard.find( '.image_zoom' ).remove();

			var img_src;
			var img_tag = $( this ).find( 'img' );
			if ( img_tag.length > 1 ) {
				img_src = $(img_tag[0]).is(':visible') ? $(img_tag[0]).attr('src') :
					( $(img_tag[1]).is(':visible') ? $(img_tag[1]).attr('src') : $(img_tag[2]).attr('src') );

			} else {
				img_src = $( this ).find( 'img' ).attr( 'src' );
			}

			$( this ).after('' +
				'<div id="epkb_image_zoom" class="epkb-setup-wizard-image_zoom">' +
					'<div class="epkb-setup-wizard-image_zoom__content">' +
						'<img src="' + img_src + '" class="epkb-setup-wizard-image_zoom__image">' +
						'<span class="epkb-setup-wizard-image_zoom__close epkbfa epkbfa-close"></span>'+
					'</div>' +
				'</div>' + '');

			// remember page scroll position and disable page scrolling to enable modal scrolling
			let page_scroll_position = $( document ).scrollTop();
			$( 'html, body' ).css( { 'overflow': 'hidden' } );

			//Close Plugin Preview Popup
			$( 'html, body' ).on('click', function(){
				$( '#epkb_image_zoom' ).remove();

				// enable page scrolling and set it to the previous position
				$( this ).css( { 'overflow': 'initial' } ).off( 'click' ).animate( { scrollTop: page_scroll_position }, 0 );
			});
		});
	})();

	// Close Button Message if Close Icon clicked
	$(document).on( 'click', '.epkb-close-notice', function(){
		$( this ).parent().addClass( 'fadeOutDown' );
	});


	/**
	 * Report the Report Error Form
	 */
	// Close Error Submit Form if Close Icon or Close Button clicked
	$( admin_report_error_form ).on( 'click', '.epkb-close-notice, .epkb-admin__error-form__btn-cancel', function(){
		window.location = epkb_vars.need_help_url;
	});

	// Submit the Report Error Form
	$( admin_report_error_form ).find( '#epkb-admin__error-form' ).on( 'submit', function ( event ) {
		event.preventDefault();

		let $form = $(this);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $form.serialize(),
			beforeSend: function (xhr) {
				// block the form and add loader
				$form.find( '.epkb-admin__error-form__btn-wrap, input, label, textarea' ).slideUp( 'fast' );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).addClass( 'epkb-admin__error-form__response--active' );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.sending_error_report );
			}
		}).done(function (response) {
			// success message
			if ( typeof response.success !== 'undefined' && response.success == false ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else if ( typeof response.success !== 'undefined' && response.success == true ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else {
				// something went wrong
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.send_report_error );
			}
		}).fail(function (response, textStatus, error) {
			// something went wrong
			$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.send_report_error );
		}).always(function () {
			// remove form loader
			$( admin_report_error_form ).find( 'input, textarea' ).prop( 'disabled', false );
			setTimeout( function() {
				window.location = epkb_vars.need_help_url;
			}, 1000 );
		});
	});

	//Dismiss ongoing notice
	$(document).on( 'click', '.epkb-notice-dismiss', function( event ) {
		event.preventDefault();

		$('#'+$(this).data('notice-id')).slideUp();

		var postData = {
			action: 'epkb_dismiss_ongoing_notice',
			epkb_dismiss_id: $(this).data('notice-id')
		};
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: postData
		});
	} );
});