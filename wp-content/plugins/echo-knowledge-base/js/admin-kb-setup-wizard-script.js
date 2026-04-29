jQuery(document).ready(function($) {

	let wizard = $( '#epkb-config-wizard-content' );

	// If the Wizard is not detected don't run scripts.
	if ( wizard.length <= 0 ) {
		return;
	}


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

		// Check if clicking Next on Layout step with PRO layout selected
		if ( $( this ).hasClass( 'epkb-setup-wizard-button-next' ) ) {
			let current_header = $( '.epkb-wc-step-header--active' );
			if ( current_header.hasClass( 'epkb-wc-step-header--layout' ) ) {
				let selected_layout = wizard.find( '.epkb-setup-wizard-layout-option--active' );
				if ( selected_layout.hasClass( 'epkb-setup-wizard-layout-option--pro' ) ) {
					// Show PRO layout dialog instead of proceeding
					// Use stopPropagation to prevent document click handler from closing the dialog immediately
					e.stopPropagation();
					$( '#epkb-wizard-pro-layout-dialog' ).addClass( 'epkb-dialog-pro-feature-ad--active' );
					return;
				}
			}
		}

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

		// Layout step handling - load live preview
		if ( next_step_header.hasClass( 'epkb-wc-step-header--layout' ) ) {
			setTimeout( load_layout_preview_on_step_enter, 100 );
		}

		// Design step handling
		if ( next_step_header.hasClass( 'epkb-wc-step-header--design' ) ) {

			let selected_layout = $('.epkb-config-setup-wizard-modular input[name="epkb-layout"]:checked').val();
			if ( typeof selected_layout == 'undefined' ) {
				selected_layout = '';
			}

			// Ensure the selected layout is marked as active in the presets step
			if ( selected_layout ) {
				let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
				presets_step.find( '.epkb-setup-wizard-module-layout' ).removeClass( 'epkb-setup-wizard-module-layout--active' );
				presets_step.find( '.epkb-setup-wizard-module-settings-row' ).removeClass( 'epkb-setup-wizard-module-settings-row--active' );
				presets_step.find( '.epkb-setup-wizard-module-layout--' + selected_layout ).addClass( 'epkb-setup-wizard-module-layout--active' );
				presets_step.find( '.epkb-setup-wizard-module-settings-row--' + selected_layout ).addClass( 'epkb-setup-wizard-module-settings-row--active' );
			}

			// Presets Content Show Option (only exists when NOT first setup)
			const content_show_option = $('.epkb-config-setup-wizard-modular .epkb-setup-wizard-theme-content-show-option');
			if ( content_show_option.length > 0 ) {
				const wizard_content = $('.epkb-config-setup-wizard-modular .epkb-wizard-content .eckb-wizard-step-design');
				content_show_option.show();
				if ( $( '.epkb-config-setup-wizard-modular input[name=epkb-setup-wizard-theme-content-show-option__toggle]' ).prop('checked') ) {
					wizard_content.addClass('epkb-wc-step-panel--active');
				} else {
					wizard_content.removeClass('epkb-wc-step-panel--active');
				}
			}

			// Load live previews when entering the design step
			setTimeout(load_visible_preset_previews, 100);
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
	 * Highlight step tabs and banner highlights
	 */
	function setup_wizard_highlight_step_tabs( nextStep ) {

		// Legacy tabs (for backwards compatibility)
		wizard.find( '.epkb-setup-wizard-step-tab' ).removeClass( 'epkb-setup-wizard-step-tab--active epkb-setup-wizard-step-tab--completed' );
		let current_tab = wizard.find( '.epkb-setup-wizard-step-tab--' + nextStep );
		current_tab.addClass( 'epkb-setup-wizard-step-tab--active' );
		current_tab.prevAll( '.epkb-setup-wizard-step-tab' ).addClass( 'epkb-setup-wizard-step-tab--completed' );

		// New banner highlights
		wizard.find( '.epkb-setup-wizard-step-highlight' ).removeClass( 'epkb-setup-wizard-step-highlight--active epkb-setup-wizard-step-highlight--completed' );
		let current_highlight = wizard.find( '.epkb-setup-wizard-step-highlight--' + nextStep );
		current_highlight.addClass( 'epkb-setup-wizard-step-highlight--active' );
		current_highlight.prevAll( '.epkb-setup-wizard-step-highlight' ).addClass( 'epkb-setup-wizard-step-highlight--completed' );
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

	// Click on image will popup (zoom functionality)
	$('.eckb-wizard-step-layout .epkb-setup-option__featured-img, .epkb-setup-wizard-features-choice img').on( 'click', function(){
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
	 * Layout Step - Live Preview
	 */
	let layoutHtmlCache = {};
	let currentLayoutRequest = null; // Track which layout is currently being requested

	function load_layout_live_preview( layout_name ) {
		let preview_container = wizard.find( '.epkb-setup-wizard-layout-preview' );
		let preview_content = preview_container.find( '.epkb-setup-wizard-layout-preview__content' );
		let loading_spinner = preview_container.find( '.epkb-setup-wizard-layout-preview__loading' );
		const preset = 'current';
		const cache_key = preset + '::' + layout_name;

		// Track this request so we can ignore stale responses
		currentLayoutRequest = layout_name;

		// Show loading spinner
		loading_spinner.show();

		// Check cache first
		if ( layoutHtmlCache[cache_key] ) {
			// Verify this layout is still active before applying
			let active_layout = wizard.find( '.epkb-setup-wizard-layout-option--active' ).data( 'layout' );
			if ( active_layout !== layout_name ) {
				return; // User switched to different layout, ignore this
			}
			loading_spinner.hide();
			preview_content.html( layoutHtmlCache[cache_key].html );
			replaceWizardCss( layoutHtmlCache[cache_key], 'layout-' + layout_name );
			return;
		}

		let postData = {
			action: 'epkb_get_wizard_preset_preview',
			_wpnonce_epkb_ajax_action: $( '#_wpnonce_epkb_ajax_action' ).val(),
			epkb_wizard_kb_id: $( '#epkb_wizard_kb_id' ).val(),
			layout: layout_name,
			preset: preset,
			preview_type: 'layout'
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			cache: false,
			url: ajaxurl,
			data: postData
		}).done( function( response ) {
			// Check if user switched to a different layout while waiting for response
			let active_layout = wizard.find( '.epkb-setup-wizard-layout-option--active' ).data( 'layout' );
			if ( active_layout !== layout_name ) {
				return; // User switched to different layout, ignore this stale response
			}

			loading_spinner.hide();

			if ( response.success && response.data ) {
				layoutHtmlCache[cache_key] = {
					html: response.data.html,
					css_file_slug: response.data.css_file_slug,
					css_file_url: response.data.css_file_url,
					css_file_rtl_url: response.data.css_file_rtl_url,
					css: response.data.css
				};

				preview_content.html( response.data.html );
				replaceWizardCss( layoutHtmlCache[cache_key], 'layout-' + layout_name );
			} else {
				preview_content.html( '<div class="epkb-setup-wizard-layout-preview__error">' + ( response?.message ?? epkb_vars.unknown_error ) + '</div>' );
			}
		}).fail( function( response, textStatus, error ) {
			// Check if user switched to a different layout while waiting for response
			let active_layout = wizard.find( '.epkb-setup-wizard-layout-option--active' ).data( 'layout' );
			if ( active_layout !== layout_name ) {
				return; // User switched to different layout, ignore this stale response
			}

			loading_spinner.hide();
			preview_content.html( '<div class="epkb-setup-wizard-layout-preview__error">' + ( error ?? epkb_vars.unknown_error ) + '</div>' );
		});
	}

	// Layout option click handler
	$( document ).on( 'click', '.epkb-setup-wizard-layout-option', function() {
		let layout_option = $( this );
		let preview_container = wizard.find( '.epkb-setup-wizard-layout-preview' );
		let preview_content = preview_container.find( '.epkb-setup-wizard-layout-preview__content' );
		let loading_spinner = preview_container.find( '.epkb-setup-wizard-layout-preview__loading' );
		let is_pro = layout_option.hasClass( 'epkb-setup-wizard-layout-option--pro' );

		// Update active state
		wizard.find( '.epkb-setup-wizard-layout-option' ).removeClass( 'epkb-setup-wizard-layout-option--active' );
		layout_option.addClass( 'epkb-setup-wizard-layout-option--active' );

		// Check the radio button for all layouts
		layout_option.find( 'input[name="epkb-layout"]' ).prop( 'checked', true );

		// Handle pro layout - show screenshot preview (no dialog, allow Next button)
		if ( is_pro ) {
			let pro_screenshot_url = layout_option.data( 'pro-screenshot' );
			if ( pro_screenshot_url ) {
				loading_spinner.hide();
				preview_content.html( '<img src="' + pro_screenshot_url + '" alt="Layout Preview" class="epkb-setup-wizard-layout-preview__screenshot">' );
			}
			preview_container.addClass( 'epkb-setup-wizard-layout-preview--pro' );
		} else {
			preview_container.removeClass( 'epkb-setup-wizard-layout-preview--pro' );

			// Load preview for selected layout
			let layout_name = layout_option.data( 'layout' );
			load_layout_live_preview( layout_name );
		}
	});

	// Later/Cancel button in PRO layout dialog - closes the dialog
	$( document ).on( 'click', '.epkb-dialog-pro-feature-ad__cancel-btn', function(e) {
		e.preventDefault();
		e.stopPropagation();
		$( this ).closest( '.epkb-dialog-pro-feature-ad, .epkb-dialog-pro-feature-ad2' ).removeClass( 'epkb-dialog-pro-feature-ad--active' );
	});

	// Prevent article link clicks in PRO layout previews (Grid/Sidebar without ELAY)
	$( document ).on( 'click', '.epkb-setup-wizard-layout-preview--pro a', function( e ) {
		e.preventDefault();
		e.stopPropagation();
	});

	// Load layout preview when entering layout step
	function load_layout_preview_on_step_enter() {
		let layout_step = wizard.find( '.epkb-setup-wizard-step-container--layout-preview' );
		if ( layout_step.length === 0 || ! layout_step.closest( '.epkb-wc-step-panel' ).hasClass( 'epkb-wc-step-panel--active' ) ) {
			return;
		}

		// Get currently active layout option (handles both regular and PRO layouts)
		let active_option = layout_step.find( '.epkb-setup-wizard-layout-option--active' );
		if ( active_option.length === 0 ) {
			return;
		}

		let preview_container = wizard.find( '.epkb-setup-wizard-layout-preview' );
		let preview_content = preview_container.find( '.epkb-setup-wizard-layout-preview__content' );
		let loading_spinner = preview_container.find( '.epkb-setup-wizard-layout-preview__loading' );

		// For PRO layouts, show screenshot
		if ( active_option.hasClass( 'epkb-setup-wizard-layout-option--pro' ) ) {
			let pro_screenshot_url = active_option.data( 'pro-screenshot' );
			if ( pro_screenshot_url ) {
				loading_spinner.hide();
				preview_content.html( '<img src="' + pro_screenshot_url + '" alt="Layout Preview" class="epkb-setup-wizard-layout-preview__screenshot">' );
			}
			preview_container.addClass( 'epkb-setup-wizard-layout-preview--pro' );
		} else {
			// For regular layouts, load live preview
			let selected_layout = active_option.data( 'layout' );
			if ( selected_layout ) {
				preview_container.removeClass( 'epkb-setup-wizard-layout-preview--pro' );
				load_layout_live_preview( selected_layout );
			}
		}
	}

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

		// Load live previews for the new layout
		setTimeout(load_visible_preset_previews, 100);
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
	 * Switch Presets Preview when user changes Preset (from regular preset buttons)
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-module-preset-selector input', function() {
		let preset_name = $( this ).val();
		let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
		let active_layout = presets_step.find( '.epkb-setup-wizard-module-layout--active' );
		let active_settings_row = presets_step.find( '.epkb-setup-wizard-module-settings-row--active' );

		// Update preset cards in the preview area
		active_layout.find( '.epkb-setup-wizard-module-preset' ).removeClass( 'epkb-setup-wizard-module-preset--active' );
		active_layout.find( '.epkb-setup-wizard-module-preset--' + preset_name ).addClass( 'epkb-setup-wizard-module-preset--active' );

		// Update the Keep Current Style button state (deselect it when other preset selected)
		active_settings_row.find( '.epkb-setup-wizard-keep-current-button' ).removeClass( 'epkb-setup-wizard-keep-current-button--active' );
		active_settings_row.find( '.epkb-setup-wizard-keep-current-button input[value="current"]' ).prop( 'checked', false );
	} );

	/**
	 * Switch Presets Preview when user clicks Keep Current Style button
	 */
	$( document ).on( 'change', '.epkb-setup-wizard-keep-current-button input', function() {
		let presets_step = wizard.find( '.epkb-setup-wizard-step-container--presets' );
		let active_layout = presets_step.find( '.epkb-setup-wizard-module-layout--active' );
		let active_settings_row = presets_step.find( '.epkb-setup-wizard-module-settings-row--active' );

		// Update preset cards in the preview area (select "current", deselect others)
		active_layout.find( '.epkb-setup-wizard-module-preset' ).removeClass( 'epkb-setup-wizard-module-preset--active' );
		active_layout.find( '.epkb-setup-wizard-module-preset--current' ).addClass( 'epkb-setup-wizard-module-preset--active' );

		// Update button state
		active_settings_row.find( '.epkb-setup-wizard-keep-current-button' ).addClass( 'epkb-setup-wizard-keep-current-button--active' );

		// Deselect other preset radio buttons
		active_settings_row.find( '.epkb-setup-wizard-module-preset-selector input' ).prop( 'checked', false );
	} );



	/**
	 * Load live preview for a preset
	 */

	let presetHtmlCache = {};

	function replaceWizardCss(data, preset) {
		if (!data || !data.css_file_slug) return;

		// Add the main CSS file ONLY ONCE (shared across all presets of same layout)
		if ($('#epkb-wizard-' + data.css_file_slug + '-css').length === 0) {
			$('head').append('<link rel="stylesheet" id="epkb-wizard-' + data.css_file_slug + '-css" href="' + data.css_file_url + '" media="all">');
		}

		// Add RTL CSS if needed
		if (data.css_file_rtl_url && $('#epkb-wizard-' + data.css_file_slug + '-rtl-css').length === 0) {
			$('head').append('<link rel="stylesheet" id="epkb-wizard-' + data.css_file_slug + '-rtl-css" href="' + data.css_file_rtl_url + '" media="all">');
		}

		// Add inline CSS (theme-specific styles) with UNIQUE ID per preset so they don't conflict
		if (data.css && preset) {
			let styleId = 'epkb-wizard-preview-styles-' + preset;
			// Remove old version of THIS preset's styles
			$('#' + styleId).remove();
			// Add new version
			$('head').append('<style id="' + styleId + '">' + data.css + '</style>');
		}
	}

	function load_preset_live_preview( preset_container ) {
		let layout = preset_container.data('layout');
		let preset = preset_container.data('preset');
		let preview_container = preset_container.find('.epkb-setup-wizard-module-preset__preview');
		let loading_spinner = preview_container.find('.epkb-setup-wizard-module-preset__preview-loading');

		// Check if already loaded (has live preview content)
		if (preset_container.hasClass('epkb-setup-wizard-module-preset--loaded')) {
			return;
		}

		// Show loading spinner
		loading_spinner.show();

		if (presetHtmlCache[preset]) {
			// Hide spinner
			loading_spinner.hide();

			// Clear any existing content and set cached HTML
			preview_container.html(presetHtmlCache[preset].html);

			// Replace CSS (pass preset name for unique styling)
			replaceWizardCss(presetHtmlCache[preset], preset);

			// Mark as loaded
			preset_container.addClass('epkb-setup-wizard-module-preset--loaded');
			return;
		}

		let postData = {
			action: 'epkb_get_wizard_preset_preview',
			_wpnonce_epkb_ajax_action: $('#_wpnonce_epkb_ajax_action').val(),
			epkb_wizard_kb_id: $('#epkb_wizard_kb_id').val(),
			layout: layout,
			preset: preset,
			preview_type: 'preset'
		};

		$.ajax({
			type: 'POST',
			dataType: 'json',
			cache: false,
			url: ajaxurl,
			data: postData
		}).done(function (response) {
			// Hide loading spinner
			loading_spinner.hide();

			if ( response.success && response.data ) {

				// Cache the HTML + CSS info
				presetHtmlCache[preset] = {
					html: response.data.html,
					css_file_slug: response.data.css_file_slug,
					css_file_url: response.data.css_file_url,
					css_file_rtl_url: response.data.css_file_rtl_url,
					css: response.data.css
				};

			// Clear any existing content and set live HTML
			preview_container.html(response.data.html);

			// Replace CSS (pass preset name for unique styling)
			replaceWizardCss(presetHtmlCache[preset], preset);

			// Mark as loaded
			preset_container.addClass('epkb-setup-wizard-module-preset--loaded');
			} else {
				// Show error (clear existing content first)
				preview_container.html('<div class="epkb-setup-wizard-module-preset__preview-error">' + ( response?.message ?? epkb_vars.unknown_error ) + '</div>');
			}
		}).fail(function (response, textStatus, error) {
			// Hide loading spinner and show error (clear existing content first)
			loading_spinner.hide();
			preview_container.html('<div class="epkb-setup-wizard-module-preset__preview-error">' + (error ?? epkb_vars.unknown_error) + '</div>');
		});
	}

	/**
	 * Load live previews for all visible presets in the active layout
	 */
	function load_visible_preset_previews() {
		let active_layout = $('.epkb-setup-wizard-step-container--presets .epkb-setup-wizard-module-layout--active');
		if (active_layout.length === 0) {
			return;
		}

		// Load all presets in the active layout
		active_layout.find('.epkb-setup-wizard-module-preset').each(function() {
			load_preset_live_preview($(this));
		});
	}


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
				console.error( 'Setup Wizard Error:', theResponse );
				return;
			}

			// Success in response - show success dialog
			if ( theResponse.redirect_to_url && theResponse.redirect_to_url.length > 0 ) {
				let $successDialog = $('#epkb-wizard-success-message');
				$successDialog.addClass('epkb-dialog-box-form--active');

				// Set the KB main page URL for the "Open Knowledge Base" link
				if ( theResponse.kb_main_page_url ) {
					$successDialog.find('.epkb-wizard-success-content__open-kb').attr('href', theResponse.kb_main_page_url);
				}

				$successDialog.find('.epkb-accept-button').on('click', function () {
					window.location = theResponse.redirect_to_url;
				});
			}

		} ).fail( function() {
			epkb_loading_Dialog( 'remove', '' );

			// On internal server error assume the error is outside Setup Wizard - force finish the Setup Wizard like on success
			let current_url = window.location.href;
			let success_url = current_url.replace( '&page=epkb-kb-configuration&setup-wizard-on', '&page=epkb-dashboard&epkb_after_kb_setup' );
			let $successDialog = $('#epkb-wizard-success-message');
			$successDialog.addClass('epkb-dialog-box-form--active');

			// Hide the "Open KB" link on failure since we don't have the URL
			$successDialog.find('.epkb-wizard-success-content__open-kb').hide();

			$successDialog.find('.epkb-accept-button').on('click', function () {
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
		wizard.find( '.epkb-setup-wizard-step__item .epkb-setup-wizard-step__item-description .epkb-setup-wizard-step__item-description__button-pro' ).on( 'click', function( e ){
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

	/*************************************************************************************************
	 *
	 *          Layout Preview - Show More functionality
	 *
	 ***********************************************************************************************/

	// Classic Layout: Show main content of Category
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-articles-show-more', function() {
		$( this ).parent().parent().toggleClass( 'epkb-category-section--active');
		$( this ).parent().parent().find( '.epkb-category-section__body' ).slideToggle();
		$( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		const isExpanded = $( this ).find( '.epkb-ml-articles-show-more__show-more__icon' ).hasClass( 'epkbfa-minus' );
		if ( isExpanded ) {
			$( this ).parent().find( '.epkb-ml-article-count' ).hide();
		} else {
			$( this ).parent().find( '.epkb-ml-article-count' ).show();
		}
	} );

	// Classic Layout: Toggle Level 2 Category Articles and Level 3 Categories
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-2-lvl-category-container', function( e ) {
		if ( $( this ).hasClass( 'epkb-ml-2-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-2-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-2-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-3-lvl-categories' ).slideToggle();
		$( this ).find( '.epkb-ml-2-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-2-lvl-category--active' );
	} );

	// Classic Layout: Toggle Level 3 Category Articles and Level 4 Categories
	$( document ).on( 'click', '#epkb-ml-classic-layout .epkb-ml-3-lvl-category-container', function( e ) {
		if ( $( this ).hasClass( 'epkb-ml-3-lvl-category--active' ) && ! $( e.target ).hasClass( 'epkb-ml-3-lvl-category__show-more__icon' ) ) return;
		$( this ).find( '.epkb-ml-3-lvl-article-list' ).slideToggle();
		$( this ).find( '.epkb-ml-4-lvl-categories' ).slideToggle();
		$( this ).find( '.epkb-ml-3-lvl-category__show-more__icon' ).toggleClass( 'epkbfa-plus epkbfa-minus' );
		$( this ).toggleClass( 'epkb-ml-3-lvl-category--active' );
	} );

	// Tabs Layout: Level 1 Show more if articles are assigned to top categories
	$( document ).on( 'click', '#epkb-ml-tabs-layout .epkb-ml-articles-show-more', function( e ) {
		e.preventDefault();
		$( this ).hide();
		$( this ).parent().find( '.epkb-list-column li' ).removeClass( 'epkb-ml-article-hide' );
	});

	// Tabs Layout: Tab click handler
	$( document ).on( 'click', '#epkb-content-container .epkb-nav-tabs li', function() {
		let tabContainerLocal = $( this ).closest( '#epkb-content-container' );
		tabContainerLocal.find( '.epkb-nav-tabs li' ).removeClass( 'active' );
		$( this ).addClass( 'active' );
		tabContainerLocal.find( '.epkb-tab-panel' ).removeClass( 'active' );
		tabContainerLocal.find( '.epkb-panel-container .' + $( this ).attr( 'id' ) ).addClass( 'active' );
	});

	// Drill Down Layout: Move content box under the category row
	function moveCategoriesBoxUnderTopCategoryButton( $layout, currentTopCat ) {
		const $allCatContent = $layout.find( '.epkb-ml-all-categories-content-container' );
		const $buttonContainer = $layout.find( '.epkb-ml-top-categories-button-container' );
		$allCatContent.hide();

		let currentTopCatOffset = currentTopCat.offset().top;
		let inserted = false;

		$layout.find( '.epkb-ml-top__cat-container' ).each( function() {
			let catOffset = $( this ).offset().top;
			if ( catOffset - currentTopCatOffset > 0 && ! inserted ) {
				$allCatContent.insertAfter( $( this ).prevAll( '.epkb-ml-top__cat-container' ).first() );
				inserted = true;
				return false;
			}
		} );

		if ( ! inserted ) {
			$allCatContent.insertAfter( $buttonContainer );
		}
	}

	// Drill Down Layout: Top Category Button click
	$( document ).on( 'click', '.epkb-ml-top__cat-container', function() {
		const $layout = $( this ).closest( '#epkb-ml-drill-down-layout' );
		const $catContent_ShowClass = 'epkb-ml__cat-content--show';
		const $topCatButton_ActiveClass = 'epkb-ml-top__cat-container--active';
		const $catButton_ActiveClass = 'epkb-ml__cat-container--active';
		const $catButtonContainers_ActiveClass = 'epkb-ml-categories-button-container--active';
		const $catButtonContainers_ShowClass = 'epkb-ml-categories-button-container--show';
		const $backButton_ActiveClass = 'epkb-back-button--active';
		const $allCatContent = $layout.find( '.epkb-ml-all-categories-content-container' );

		$layout.find( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );

		if ( $( this ).hasClass( $topCatButton_ActiveClass ) ) {
			$( this ).removeClass( $topCatButton_ActiveClass );
			$allCatContent.hide();
			return;
		}

		$allCatContent.find( '.epkb-back-button' ).removeClass( $backButton_ActiveClass );
		$( this ).addClass( $topCatButton_ActiveClass );

		$layout.find( '.epkb-ml-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		$layout.find( '.epkb-ml__cat-content' ).removeClass( $catContent_ShowClass );
		$layout.find( '.epkb-ml__cat-container' ).removeClass( $catButton_ActiveClass );

		moveCategoriesBoxUnderTopCategoryButton( $layout, $( this ) );

		$allCatContent.show();

		const catId = $( this ).data( 'cat-id' );
		$layout.find( '.epkb-ml-1-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );
		$layout.find( '.epkb-ml-2-lvl-categories-button-container[data-cat-level="1"][data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );
	});

	// Drill Down Layout: Category Button click
	$( document ).on( 'click', '.epkb-ml__cat-container', function() {
		const $layout = $( this ).closest( '#epkb-ml-drill-down-layout' );
		const $catContent_ShowClass = 'epkb-ml__cat-content--show';
		const $catButton_ActiveClass = 'epkb-ml__cat-container--active';
		const $catButtonContainers_ActiveClass = 'epkb-ml-categories-button-container--active';
		const $catButtonContainers_ShowClass = 'epkb-ml-categories-button-container--show';
		const $backButton_ActiveClass = 'epkb-back-button--active';

		if ( $( this ).hasClass( $catButton_ActiveClass ) ) {
			return;
		}

		$layout.find( '.epkb-ml-all-categories-content-container .epkb-back-button' ).addClass( $backButton_ActiveClass );

		const catLevel = parseInt( $( this ).data( 'cat-level' ) );
		const catId = $( this ).data( 'cat-id' );
		const parentCatLevel = catLevel - 1;
		const parentCatId = $( this ).data( 'parent-cat-id' );
		const childCatLevel = catLevel + 1;

		$( this ).addClass( $catButton_ActiveClass );
		$layout.find( '.epkb-ml-' + catLevel + '-lvl-categories-button-container[data-cat-id="' + parentCatId + '"]' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

		$layout.find( '.epkb-ml-' + parentCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		$layout.find( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
		$layout.find( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );

		$layout.find( '.epkb-ml-' + catLevel + '-lvl__cat-content[data-cat-id="' + catId + '"]' ).addClass( $catContent_ShowClass );
		$layout.find( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container[data-cat-id="' + catId + '"]' ).addClass( $catButtonContainers_ShowClass );
	});

	// Drill Down Layout: Back Button click
	$( document ).on( 'click', '.epkb-back-button', function() {
		const $layout = $( this ).closest( '#epkb-ml-drill-down-layout' );
		const $catContent_ShowClass = 'epkb-ml__cat-content--show';
		const $topCatButton_ActiveClass = 'epkb-ml-top__cat-container--active';
		const $catButton_ActiveClass = 'epkb-ml__cat-container--active';
		const $catButtonContainers_ActiveClass = 'epkb-ml-categories-button-container--active';
		const $catButtonContainers_ShowClass = 'epkb-ml-categories-button-container--show';
		const $backButton_ActiveClass = 'epkb-back-button--active';

		let currentCatContent = $layout.find( '.epkb-ml__cat-content.' + $catContent_ShowClass );
		let catLevel = parseInt( currentCatContent.data( 'cat-level' ) );
		let childCatLevel = catLevel + 1;

		if ( catLevel === 1 ) {
			$layout.find( '.epkb-ml-top__cat-container' ).removeClass( $topCatButton_ActiveClass );
			$layout.find( '.epkb-ml-all-categories-content-container' ).hide();
			return;
		}

		let parentCatId = currentCatContent.data( 'parent-cat-id' );
		let parentCatLevel = catLevel - 1;

		if ( parentCatLevel === 1 ) {
			$layout.find( '.epkb-ml-all-categories-content-container .epkb-back-button' ).removeClass( $backButton_ActiveClass );
		}

		$layout.find( '.epkb-ml-' + catLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass );
		$layout.find( '.epkb-ml-' + catLevel + '-lvl__cat-container' ).removeClass( $catButton_ActiveClass );
		$layout.find( '.epkb-ml-' + catLevel + '-lvl__cat-content' ).removeClass( $catContent_ShowClass );
		$layout.find( '.epkb-ml-' + childCatLevel + '-lvl-categories-button-container' ).removeClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );

		let parentCatButton = $layout.find( '.epkb-ml-' + parentCatLevel + '-lvl__cat-container[data-cat-id="' + parentCatId + '"]' );
		parentCatButton.closest( '.epkb-ml-categories-button-container' ).addClass( $catButtonContainers_ActiveClass + ' ' + $catButtonContainers_ShowClass );
		parentCatButton.addClass( $catButton_ActiveClass );
		$layout.find( '.epkb-ml-' + parentCatLevel + '-lvl__cat-content[data-cat-id="' + parentCatId + '"]' ).addClass( $catContent_ShowClass );
	});

	// Basic Layout: Toggle sub-categories (expand/collapse articles and sub-sub-categories)
	$( document ).on( 'click', '.epkb-section-body .epkb-category-level-2-3:not(.epkb-category-focused)', function( e ) {
		e.preventDefault();
		$( this ).parent().children( 'ul' ).toggleClass( 'active' );

		// Toggle aria-expanded attribute
		let ariaExpandedVal = $( this ).attr( 'aria-expanded' );
		$( this ).attr( 'aria-expanded', ariaExpandedVal === 'true' ? 'false' : 'true' );

		// Toggle expand/collapse icon
		let $icon = $( this ).find( '.epkb-category-level-2-3__cat-icon' );
		let iconPairs = [
			[ 'ep_font_icon_plus', 'ep_font_icon_minus' ],
			[ 'ep_font_icon_plus_box', 'ep_font_icon_minus_box' ],
			[ 'ep_font_icon_right_arrow', 'ep_font_icon_down_arrow' ],
			[ 'ep_font_icon_arrow_carrot_right', 'ep_font_icon_arrow_carrot_down' ],
			[ 'ep_font_icon_arrow_carrot_right_circle', 'ep_font_icon_arrow_carrot_down_circle' ],
			[ 'ep_font_icon_folder_add', 'ep_font_icon_folder_open' ]
		];
		iconPairs.forEach( function( pair ) {
			if ( $icon.hasClass( pair[0] ) ) {
				$icon.removeClass( pair[0] ).addClass( pair[1] );
			} else if ( $icon.hasClass( pair[1] ) ) {
				$icon.removeClass( pair[1] ).addClass( pair[0] );
			}
		});
	});

	// Category Focused Layout: Prevent sub-category clicks (they are links but should not navigate in preview)
	$( document ).on( 'click', '.epkb-section-body .epkb-category-level-2-3.epkb-category-focused', function( e ) {
		e.preventDefault();
		e.stopPropagation();
	});
});
