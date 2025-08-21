jQuery( document ).ready( function( $ ) {

	let frontendEditor = $( '#epkb-fe__editor' );

	// show the frontend editor if one of KB pages is found in the page
	if ( frontendEditor.length > 0 ) {
		if ( frontendEditor.data( 'display-frontend-editor-closed' ) ) {
			$('#epkb-fe__toggle').show();
		} else {
			open_frontend_editor(null );
		}
	} else {
		$('#wp-admin-bar-epkb-edit-mode-button').hide();
	}

	let admin_report_error_form = $( '#epkb-fe__error-form-wrap .epkb-admin__error-form__container' );

	// Handle FE Open Button and Admin bar FE edit link
	$( '.epkb-fe__toggle, #wp-admin-bar-epkb-edit-mode-button' ).on( 'click', function( e ) {
		open_frontend_editor( e );
	} );

	function open_frontend_editor( e ) {
		if ( e ) {	
			e.preventDefault(); // Prevent the default link behavior
		}
		frontendEditor.css( 'right', '0' );
		frontendEditor.show();
		$( '.epkb-fe__toggle' ).hide();
		$( '#epkb-fe__editor .epkb-fe__feature-settings' ).each(function () {
			const $featureSettings = $( this );
			const rowNumber = $featureSettings.attr( 'data-row-number' );
			if ( rowNumber === 'none' ) {
				$featureSettings.find( '.epkb-fe__settings-section:not(.epkb-fe__settings-section--module-position)' ).addClass( 'epkb-fe__settings-section--hide' );
			} else {
				$featureSettings.find( '.epkb-fe__settings-section:not(.epkb-fe__settings-section--module-position)' ).removeClass( 'epkb-fe__settings-section--hide' );
			}
		} );
	}

	// Close FE
	$( document ).on( 'click', '.epkb-fe__header-close-button', function() {
		frontendEditor.hide();
		$( '.epkb-fe__toggle' ).show();
		$( '#epkb-fe__action-back' ).trigger( 'click' );

		// first time user closed the FE, do not save the state
		if ( frontendEditor.data( 'display-frontend-editor-closed' ) ) {
			return;
		}

		$.ajax( {
			url: epkb_vars.ajaxurl,
			method: 'POST',
			data: {
				action: 'eckb_closed_fe_editor',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
			}
		} );
	} );

	// Open Help tab
	$( document ).on( 'click', '#epkb-fe__help-tab', function() {

		// Add Help class and remove Main class
		frontendEditor.addClass( 'epkb-fe__editor--help' ).removeClass( 'epkb-fe__editor--home' );

		// Show action buttons
		$( '#epkb-fe__editor .epkb-fe__actions' ).show();
	} );

	// Show settings for a feature
	$( document ).on( 'click', '.epkb-fe__feature-select-button', function() {
		let $button = $( this );
		let feature_name = $button.data( 'feature' );
		let $tab = $( '.epkb-fe__feature-settings[data-feature="' + feature_name + '"]' );

		$( '.epkb-fe__header-title' ).hide();
		$( '.epkb-fe__header-title[data-title="' + feature_name + '"]' ).addClass( 'epkb-fe__header-title--active' );

		// set epkb-fe__editor--settings and remove rest of classes
		/*frontendEditor.removeClass(function(index, className) {
			return (className.match(/\bepkb-fe__editor--\S+/g) || []).join(' ');
		});*/
		frontendEditor.addClass( 'epkb-fe__editor--settings' ).removeClass( 'epkb-fe__editor--home epkb-fe__editor--help' );

		$( '.epkb-fe__feature-select-button' ).hide();
		$( '.epkb-fe__feature-settings' ).removeClass( 'epkb-fe__feature-settings--active' );
		$tab.addClass( 'epkb-fe__feature-settings--active' );

		// Refresh conditional settings
		$tab.find( '.eckb-conditional-setting-input' ).trigger( 'click' );

		// Ensure custom dropdowns in the newly shown tab reflect the current select values
		$tab.find('.epkb-input-custom-dropdown select').each(function() {
			update_custom_dropdown_display($(this));
		});

		$( '#epkb-fe__editor .epkb-fe__actions' ).show();
		$( '.epkb-fe__top-actions' ).hide();
	} );

	// Back button to hide settings for the feature and show features list
	$( document ).on( 'click', '#epkb-fe__action-back', function() {

		// set epkb-fe__editor--home and remove rest of classes
		frontendEditor.removeClass(function(index, className) {
			return (className.match(/\bepkb-fe__editor--\S+/g) || []).join(' ');
		  });
		$( '.epkb-fe__header-title' ).removeClass( 'epkb-fe__header-title--active' );
		frontendEditor.addClass( 'epkb-fe__editor--home' );

		// Show module icons
		$( '.epkb-fe__feature-select-button' ).show();

		// Hide all module settings
		$( '.epkb-fe__feature-settings' ).removeClass( 'epkb-fe__feature-settings--active' );

		// Hide action buttons
		$( '#epkb-fe__editor .epkb-fe__actions' ).hide();

		// Show 'close' button
		$( '.epkb-fe__top-actions' ).show();
	} );

	// Expand / Collapse settings section
	$( document ).on( 'click', '.epkb-fe__settings-section-header', function() {
		let $section = $( this ).parent();
		let $sectionBody = $section.find( '.epkb-fe__settings-section-body' );
		if ( $section.hasClass( 'epkb-fe__is_opened' ) ) {
			$sectionBody.stop().slideUp();
			$section.removeClass( 'epkb-fe__is_opened' );
		} else {
			$sectionBody.stop().slideDown();
			$section.addClass( 'epkb-fe__is_opened' );
		}
	} );

	// Switch Settings boxes which belong to certain feature
	// Add the following CSS classes in PHP config to necessary Settings boxes:
	// - epkb-fe__settings-section--module-box
	// - epkb-fe__settings-section--{module name}-box
	// - epkb-fe__settings-section--hide
	// Add 'data' => [ 'insert-box-after' => {selector} ] in PHP config to insert the box after certain Settings box
	// Adapted from admin-plugin-pages.js - has similar functionality
	function switch_module_boxes( module_selector ) {
		let current_module_name = $( module_selector ).val();

		// Hide other modules Settings boxes in the current tab
		let other_modules_boxes = $( module_selector ).closest( '.epkb-fe__feature-settings' ).find( '.epkb-fe__settings-section--module-box:not(.epkb-fe__settings-section--' + current_module_name + '-box)' );
		other_modules_boxes.addClass( 'epkb-fe__settings-section--hide' );

		// Find all Settings boxes which belong to the currently selected module
		let module_boxes = $( module_selector ).closest( '.epkb-fe__features-list' ).find( '.epkb-fe__settings-section--' + current_module_name + '-box' );
		if ( ! module_boxes.length ) {
			return;
		}

		$( module_boxes.get().reverse() ).each( function () {
			$( this ).removeClass( 'epkb-fe__settings-section--hide' );

			// Show Settings boxes which belong to the currently selected module
			let insert_box_after = $( this ).data( 'insert-box-after' );
			$( module_selector ).closest( '.epkb-fe__feature-settings .epkb-fe__settings-list' ).find( insert_box_after ).after( this );
		} );

		// Insure the selected Layout is shown as active - fix for Grid or Sidebar Layout selection with Elegant Layouts disabled
		if ( current_module_name === 'categories_articles' ) {
			$( '[name="kb_main_page_layout"]:checked' ).trigger( 'click' );
		}
	}

	// Initialize Layout box settings
	// Adapted from admin-plugin-pages.js - has similar functionality
	$( '[data-settings-group="ml-row"].epkb-row-module-setting select' ).each( function() {
		switch_module_boxes( this );
	} );

	// Helper function to update the custom dropdown's display
	function update_custom_dropdown_display($select) {
		const $inputGroup = $select.closest('.epkb-input-custom-dropdown');
		if (!$inputGroup.length) return;

		const $optionsList = $inputGroup.find('.epkb-input-custom-dropdown__options-list');
		const newValue = $select.val();
		const newText = $select.find('option:selected').text();

		$inputGroup.find('.epkb-input-custom-dropdown__input span').text(newText);
		$optionsList.find('.epkb-input-custom-dropdown__option').removeClass('epkb-input-custom-dropdown__option--selected');
		$optionsList.find('.epkb-input-custom-dropdown__option[data-value="' + newValue + '"]').addClass('epkb-input-custom-dropdown__option--selected');
	}


	/*************************************************************************************************
	 *
	 *          FE Settings - Preview Changes / Save Changes
	 *
	 ************************************************************************************************/

	let ignore_setting_update_flag = false;
	let current_layout_name = frontendEditor.find( '[name="kb_main_page_layout"]:checked' ).val();

	// Design preset may change settings which are not present in the FE UI - apply full design settings + FE UI settings until user saved settings
	let selected_search_preset = 'current';

	// Update page with a new preview - call backend
	function updatePreview( event, ui ) {

		if ( ignore_setting_update_flag ) {
			return;
		}

		const $feature_settings_container = $( event.target ).closest( '.epkb-fe__feature-settings' );
		const feature_name = $feature_settings_container.data( 'feature' );
		const kb_page_type = $feature_settings_container.data( 'kb-page-type' );
		const post_id = frontendEditor.data( 'post-id' );

		// Get the actual current position from the DOM for this feature
		const $activeRow = $('.epkb-ml__row[data-feature="' + feature_name + '"]');
		const actualPosition = $activeRow.length > 0 ? $activeRow.attr('data-position') : 'none';

		if ( $( event.target ).hasClass( 'wp-color-picker' ) && ui && ui.color ) {
			$( event.target ).closest( '.wp-picker-container' ).find( '.wp-color-result' ).css( 'background-color', ui.color.toString() );
			$( event.target ).val( ui.color.toString() );
		}

		let kb_config = collectConfig();

		kb_config[feature_name + '_module_position'] = actualPosition;

		// Get taxonomy and term information for archive pages
		let taxonomy = '';
		let term_id = 0;
		if ( kb_page_type === 'archive-page' ) {
			// Try to get taxonomy from body class or data attributes
			const $body = $('body');
			const bodyClasses = $body.attr('class');
			
			// Look for taxonomy in body classes (e.g., 'tax-epkb_post_type_1_category')
			const taxMatch = bodyClasses ? bodyClasses.match(/tax-(epkb_post_type_\d+_[\w]+)/) : null;
			if (taxMatch) {
				taxonomy = taxMatch[1];
			}
			
			// Try to get term ID from body classes (e.g., 'term-123')
			const termMatch = bodyClasses ? bodyClasses.match(/term-(\d+)/) : null;
			if (termMatch) {
				term_id = parseInt(termMatch[1]);
			}
		}

		// Show loading dialog based on page type
		if ( kb_page_type === 'main-page' ) {
			epkb_loading_Dialog( 'show', '', $( '#epkb-modular-main-page-container, #eckb-kb-template' ) );
			if ( $feature_settings_container.find( '[name="advanced_search_mp_presets"]:checked' ).val() !== 'current' ) {
				selected_search_preset = $feature_settings_container.find( '[name="advanced_search_mp_presets"]:checked' ).val();
			}
		} else if ( kb_page_type === 'article-page' ) {
			// For article page, always show loading on the main article container
			epkb_loading_Dialog( 'show', '', $( '#eckb-article-page-container-v2' ) );
			if ( $feature_settings_container.find( '[name="advanced_search_ap_presets"]:checked' ).val() !== 'current' ) {
				selected_search_preset = $feature_settings_container.find( '[name="advanced_search_ap_presets"]:checked' ).val();
			}
		} else if ( kb_page_type === 'archive-page' ) {
			epkb_loading_Dialog( 'show', '', $( '#eckb-archive-page-container' ) );
		}

		// Apply changes without saving (for preview purpose)
		$.ajax( {
			url: epkb_vars.ajaxurl,
			method: 'POST',
			data: {
				action: 'eckb_apply_fe_settings',
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				kb_id: frontendEditor.data( 'kbid' ),
				new_kb_config: kb_config,
				kb_page_type: kb_page_type,
				feature_name: feature_name,
				setting_name: $(event.target).attr('name'),
				prev_link_css_id: $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-css"]' ).attr( 'id' ),
				settings_row_number: actualPosition ? actualPosition : 'none',
				taxonomy: taxonomy,
				term_id: term_id,
				kb_post_id: post_id,
				layout_name: current_layout_name,
				selected_search_preset: selected_search_preset
			},
			success: function( response ) {

				// Handle generic KB error (caught by KB)
				if ( ! response.success ) {
					try {
						let responseJson = JSON.parse( response );
						if ( responseJson.message ) {
							$( 'body' ).append( $( responseJson.message ) );
						}
					} catch ( e ) {}
					return;
				}

				// Main Page: Handle successful AJAX response

				// Ensure we do not trigger extra updates during control re-initialization
				ignore_setting_update_flag = true;

				if ( kb_page_type === 'main-page' ) {

					update_main_page_css( response );

					// Update layout module settings if required (on layout change)
					if ( response.data.layout_settings_html && response.data.layout_settings_html.length > 0 ) {

						// Update current layout value fter layout switch (is needed to properly adjust settings on server side for layout change)
						current_layout_name = frontendEditor.find( '[name="kb_main_page_layout"]:checked' ).val();

						// Update module settings HTML
						let $settings_list = $( '.epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list' );
						$settings_list.html( response.data.layout_settings_html );

						// If the feature is located in the first row, then it already contains all settings boxes (the first row is used as a storage for all optional settings boxes - Settings UI inherited functionality)
						if ( parseInt( actualPosition ) === 1 ) {

							// Find all Settings boxes which belong to the currently selected module
							let module_boxes = $settings_list.find( '.epkb-fe__settings-section--' + feature_name + '-box' );
							if ( module_boxes.length > 0 ) {
								$( module_boxes.get() ).each( function () {
									$( this ).removeClass( 'epkb-fe__settings-section--hide' );
								} );
							}
						}

						// If the feature is located in non-first row, then retrieve its optional settings boxes from temporary storage
						else {

							// Create temporary container for optional settings to initialize (required by inherited logic from Settings UI)
							let $temporary_layout_change_settings = $( '<div id="epkb-fe__layout-change-settings" style="display: none !important;">' + response.data.layout_settings_html_temp + '</div>' );

							// Find all Settings boxes which belong to the currently selected module
							let module_boxes = $temporary_layout_change_settings.find( '.epkb-fe__settings-section--' + feature_name + '-box' );
							if ( module_boxes.length > 0 ) {
								$( module_boxes.get().reverse() ).each( function () {
									$( this ).removeClass( 'epkb-fe__settings-section--hide' );

									// Show Settings boxes which belong to the currently selected module
									let insert_box_after = $( this ).data( 'insert-box-after' );
									$settings_list.find( insert_box_after ).after( this );
								} );
							}
						}

						// Re-apply current settings for the dropdown controls of the module
						$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list .epkb-input-custom-dropdown select' ).trigger( 'change' );

						// Re-init radio-buttons
						$( '#epkb-fe__editor .epkb-fe__feature-settings .epkb-fe__settings-list input[type="radio"][checked]' ).prop( 'checked', true );

						prepare_color_picker( feature_name );

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );

						// Enable the feature in the refreshed settings
						$( '#epkb-fe__editor  .epkb-settings-control__input__toggle[name="' + feature_name + '"]' ).trigger( 'click', true );
					}

					// Update FAQs module settings (after applying design preset)
					if ( response.data.faqs_design_settings ) {

						// Unselect preset name to prevent continuing preset applying on further settings changes
						$feature_settings_container.find( '[name="faq_preset_name"]' ).prop( 'checked', false );

						// Apply preset settings for UI controls
						for ( const [ key, value ] of Object.entries( response.data.faqs_design_settings ) ) {
							const $target_field = $feature_settings_container.find( '[name="' + key + '"]' );
							apply_preset_setting( $feature_settings_container, $target_field, key, value );
						}

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );
					}

					// Update Categories & Articles module settings (after applying design preset)
					if ( response.data.categories_articles_design_settings ) {

						// Unselect preset name to prevent continuing preset applying on further settings changes
						$feature_settings_container.find( '[name="categories_articles_preset"]' ).val( 'current' ).trigger( 'change' );

						// Apply preset settings for UI controls
						for ( const [ key, value ] of Object.entries( response.data.categories_articles_design_settings ) ) {
							const $target_field = frontendEditor.find( '[name="' + key + '"]' );
							apply_preset_setting( frontendEditor, $target_field, key, value );
						}

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );
					}

					// Update search settings (after applying design preset)
					if ( response.data.search_design_settings ) {

						// Apply preset settings for UI controls
						for ( const [ key, value ] of Object.entries( response.data.search_design_settings ) ) {
							const $target_field = frontendEditor.find( '[name="' + key + '"]' );
							apply_preset_setting( frontendEditor, $target_field, key, value );
						}

						// Unselect preset name to prevent continuing preset applying on further settings changes
						$feature_settings_container.find( '[name="advanced_search_mp_presets"][value="current"]' ).prop( 'checked', true );

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );
					}

					// Inline styles - changed every time a module setting was changed
					if ( response.data.inline_styles ) {
						$( '[id^="epkb-mp-frontend-modular-"][id$="-layout-inline-css"]' ).html( response.data.inline_styles );
					}

					// Update HTML of the target module - changed every time a module setting was changed
					if ( response.data.preview_html ) {
						// Find the row by feature name, not by row number
						let $destination_row = $( '.epkb-ml__row[data-feature="' + feature_name + '"]' );
						
						// If the row exists (module is enabled), update its content
						if ( $destination_row.length > 0 ) {
							$destination_row.html( response.data.preview_html );
						}
						// Note: If the row doesn't exist, we don't create it here because 
						// the module is disabled and shouldn't have content displayed
					}

					// Ensure public JS which is dependent on HTML initialization is re-initialized
					setTimeout( function() { 
						$( window ).trigger( 'resize' );
					}, 100 );
				}

				// Article Page
				if ( kb_page_type === 'article-page' ) {

					// Update search settings (after applying design preset or sync with Main Page search)
					if ( response.data.search_design_settings ) {

						// Apply preset settings for UI controls
						for ( const [ key, value ] of Object.entries( response.data.search_design_settings ) ) {
							const $target_field = frontendEditor.find( '[name="' + key + '"]' );
							apply_preset_setting( frontendEditor, $target_field, key, value );
						}

						// Unselect preset name to prevent continuing preset applying on further settings changes
						$feature_settings_container.find( '[name="advanced_search_ap_presets"][value="current"]' ).prop( 'checked', true );

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );
					}

					// Update HTML of the entire Article content or specific sections
					if ( response.data.preview_html ) {

						const $newContent = $( response.data.preview_html );
						const $templateData = $newContent.filter('#eckb-template-update-data');

						// Update template wrapper if data is present
						if ( $templateData.length ) {
							const data = JSON.parse( $templateData.text() );
							const $kbTemplate = $( '.eckb-kb-template' );

							if ( data.classes ) {
								$kbTemplate.removeClass( 'eckb-article-resets eckb-article-defaults' );
								$kbTemplate.addClass( data.classes );
							}

							if ( data.style ) {
								$kbTemplate.attr( 'style', data.style );
							}
						}

						// Update article content
						$( '[id^="eckb-article-page-container"]' ).parent().html( response.data.preview_html );
					}

					// Inline styles - changed every time a module setting was changed
					if ( response.data.inline_styles ) {
						let $inlineStyles = $( '#epkb-ap-frontend-layout-inline-css' );
						if ( $inlineStyles.length ) {
							$inlineStyles.html( response.data.inline_styles );
						} else {
							// Create the inline styles element if it doesn't exist
							$( '<style id="epkb-ap-frontend-layout-inline-css">' + response.data.inline_styles + '</style>' ).appendTo( 'head' );
						}
					}

					// Ensure public JS which is dependent on HTML initialization is re-initialized
					setTimeout( function() {
						$( window ).trigger( 'resize' );
						// Re-initialize any article-specific JavaScript functionality
						if ( typeof epkb_init_article_toc === 'function' ) {
							epkb_init_article_toc();
						}
						// Re-initialize advanced search if it was updated
						if ( feature_name === 'article-page-search-box' && typeof epkb_init_advanced_search === 'function' ) {
							epkb_init_advanced_search();
						}
					}, 100 );
				}

				// Archive Page
				if ( kb_page_type === 'archive-page' ) {

					// Update design settings (after applying design preset)
					if ( response.data.archive_design_settings ) {

						// Unselect preset name to prevent continuing preset applying on further settings changes
						$feature_settings_container.find( '[name="archive_content_sub_categories_display_mode"]' ).prop( 'checked', false );

						// Apply preset settings for UI controls
						for ( const [ key, value ] of Object.entries( response.data.archive_design_settings ) ) {
							const $target_field = $feature_settings_container.find( '[name="' + key + '"]' );
							apply_preset_setting( $feature_settings_container, $target_field, key, value );
						}

						// Re-apply current settings for the buttons, radio buttons, and other controls of the module
						$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list .eckb-conditional-setting-input' ).trigger( 'click' );
					}

					// Update HTML of the entire Archive content
					if ( response.data.preview_html ) {
						$( '#eckb-archive-page-container' ).replaceWith( response.data.preview_html );
					}

					// Inline styles - changed every time a module setting was changed
					if ( response.data.inline_styles ) {
						let $inlineStyles = $( '#epkb-cp-frontend-layout-inline-css' );
						if ( $inlineStyles.length ) {
							$inlineStyles.html( response.data.inline_styles );
						} else {
							// Create the inline styles element if it doesn't exist
							$( '<style id="epkb-cp-frontend-layout-inline-css">' + response.data.inline_styles + '</style>' ).appendTo( 'head' );
						}
					}

					// Ensure public JS which is dependent on HTML initialization is re-initialized
					setTimeout( function() {
						$( window ).trigger( 'resize' );
					}, 100 );
				}

				// Allow to handle user changes for settings
				ignore_setting_update_flag = false;

			},
			complete: function() {
				// Remove loading dialog based on page type
				if ( kb_page_type === 'main-page' ) {
					epkb_loading_Dialog( 'remove', '', $( '#epkb-modular-main-page-container, #eckb-kb-template' ) );
				} else if ( kb_page_type === 'article-page' ) {
					// Remove loading from the main article container
					epkb_loading_Dialog( 'remove', '', $( '#eckb-article-page-container-v2' ) );
				} else if ( kb_page_type === 'archive-page' ) {
					epkb_loading_Dialog( 'remove', '', $( '#eckb-archive-page-container' ) );
				}
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				// Handle AJAX request errors with detailed information
				let errorMessage = buildDetailedErrorMessage( jqXHR, textStatus, errorThrown, epkb_vars.fe_update_preview_error );

				show_report_error_form( errorMessage );

				// Remove loading dialog based on page type
				if ( kb_page_type === 'main-page' ) {
					epkb_loading_Dialog( 'remove', '', $( '#epkb-modular-main-page-container, #eckb-kb-template' ) );
				} else if ( kb_page_type === 'article-page' ) {
					// Remove loading from the main article container
					epkb_loading_Dialog( 'remove', '', $( '#eckb-article-page-container-v2' ) );
				} else if ( kb_page_type === 'archive-page' ) {
					epkb_loading_Dialog( 'remove', '', $( '#eckb-archive-page-container' ) );
				}
			},
		} );
	}

	function apply_preset_setting( $feature_settings_container, $target_setting_field, key, value ) {

		if ( $target_setting_field.length === 0 ) {
			return;
		}

		// Radio buttons
		if ( $target_setting_field.attr( 'type' ) === 'radio' ) {
			$feature_settings_container.find( '[name="' + key + '"][value="' + value + '"]' ).prop( 'checked', true );

		// Checkbox
		} else if ( $target_setting_field.attr( 'type' ) === 'checkbox' ) {
			$feature_settings_container.find( '[name="' + key + '"]' ).prop( 'checked', value === 'on' );

		// Color-picker
		} else if ( $target_setting_field.hasClass( 'wp-color-picker' ) ) {
			$target_setting_field.closest( '.wp-picker-container' ).find( '.wp-color-result' ).css( 'background-color', value );
			$target_setting_field.val( value );

		// Other field types
		} else {
			$target_setting_field.val( value );
		}
	}

	// Update page with reload while keep unsaved changes in FE settings
	function update_preview_via_page_reload( event, ui ) {

		if ( ignore_setting_update_flag ) {
			return;
		}

		const $feature_settings_container = $( event.target ).closest( '.epkb-fe__feature-settings' );
		const kb_page_type = $feature_settings_container.data( 'kb-page-type' );

		if ( kb_page_type === 'main-page' ) {
			epkb_loading_Dialog( 'show', '', $( '#epkb-modular-main-page-container, #eckb-kb-template' ) );
		} else if ( kb_page_type === 'article-page' ) {
			epkb_loading_Dialog( 'show', '', $( '#eckb-article-page-container-v2' ) );
		} else if ( kb_page_type === 'archive-page' ) {
			epkb_loading_Dialog( 'show', '', $( '#eckb-archive-page-container' ) );
		}

		if ( $( event.target ).hasClass( 'wp-color-picker' ) && ui && ui.color ) {
			$( event.target ).closest( '.wp-picker-container' ).find( '.wp-color-result' ).css( 'background-color', ui.color.toString() );
			$( event.target ).val( ui.color.toString() );
		}

		const kb_config = collectConfig();
		const config_json = JSON.stringify( kb_config );
		const feature_name = $( '#epkb-fe__editor .epkb-fe__feature-settings--active' ).attr( 'data-feature' );

		// Set parameter to re-open currently active feature in the editor
		const action_url = new URL( window.location.href );
		action_url.searchParams.set( 'epkb_fe_reopen_feature', feature_name );

		let $preview_form = $( '<form method="post" action="' + action_url + '" style="display: none !important;">' +
			'<input type="hidden" name="epkb_fe_reload_mode" value="on">' +
			'<input type="hidden" name="kb_id" value="' + frontendEditor.data( 'kbid' ) + '">' +
			'<input type="text" name="new_kb_config" value="">' +
			'</form>' );

		$preview_form.find( '[name="new_kb_config"]' ).val( config_json );

		$( 'body' ).append( $preview_form );

		$preview_form.trigger( 'submit' );
	}

	function update_main_page_css( response ) {
		// Main CSS file - changed only on switching layout
		if ( response.data.link_css && response.data.link_css.length > 0 ) {
			let new_link_css_id = $( response.data.link_css ).attr( 'id' );
			let $current_link_css = $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-css"]' );

			// Load the file only once
			if ( new_link_css_id !== $current_link_css.attr( 'id' ) ) {
				$current_link_css.replaceWith( response.data.link_css );
			}
		}

		// RTL Main CSS file - changed only on switching layout
		if ( response.data.link_css_rtl && response.data.link_css_rtl.length > 0 ) {
			let new_link_css_rtl_id = $( response.data.link_css_rtl ).attr( 'id' );
			let $current_link_css_rtl = $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-rtl-css"]' );

			// Load the file only once
			if ( new_link_css_rtl_id !== $current_link_css_rtl.attr( 'id' ) ) {
				$current_link_css_rtl.replaceWith( response.data.link_css_rtl );
			}
		}

		// EL.AY Main CSS file - changed only on switching layout
		if ( response.data.elay_link_css && response.data.elay_link_css.length > 0 ) {
			let new_elay_link_css_id = $( response.data.elay_link_css ).attr( 'id' );
			let $current_elay_link_css = $( '[id^="elay-mp-frontend-modular-"][id$="-layout-css"]' );

			// EL.AY layout-specific CSS file is not present if the layout was not active during the page load
			if ( $current_elay_link_css.length > 0 ) {

				// Load the file only once
				if ( new_elay_link_css_id !== $current_elay_link_css.attr( 'id' ) ) {
					$current_elay_link_css.replaceWith( response.data.elay_link_css );
				}
			} else {
				$current_elay_link_css.insertAfter( '#elay-public-modular-styles-css' );
			}

			if ( $current_elay_link_css.length ) {
				$current_elay_link_css.replaceWith( response.data.elay_link_css );
			} else {
				let $current_link_css = $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-css"]' );
				$( response.data.elay_link_css ).insertAfter( $current_link_css );
			}
		}

		// RTL Main CSS file
		if ( response.data.elay_link_css_rtl && response.data.elay_link_css_rtl.length > 0 ) {
			let new_elay_link_css_rtl_id = $( response.data.elay_link_css ).attr( 'id' );
			let $current_elay_link_css_rtl = $( '[id^="elay-mp-frontend-modular-"][id$="-layout-rtl-css"]' );

			// EL.AY layout-specific CSS file is not present if the layout was not active during the page load
			if ( $current_elay_link_css_rtl.length > 0 ) {

				// Load the file only once
				if ( new_elay_link_css_rtl_id !== $current_elay_link_css_rtl.attr( 'id' ) ) {
					$current_elay_link_css_rtl.replaceWith( response.data.elay_link_css_rtl );
				}
			} else {
				$current_elay_link_css_rtl.insertAfter( '#elay-public-modular-styles-rtl-css' );
			}

			if ( $current_elay_link_css_rtl.length ) {
				$current_elay_link_css_rtl.replaceWith( response.data.elay_link_css_rtl );
			} else {
				let $current_link_css = $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-rtl-css"]' );
				$( response.data.elay_link_css_rtl ).insertAfter( $current_link_css );
			}
		}
	}

	// Preview Update: on single setting change except colors
	$( document ).on( 'change', '#epkb-fe__editor input, #epkb-fe__editor select, #epkb-fe__editor textarea', function( event, is_triggered_programmatically ) {

		// Programmatically triggered 'change' event from Settings UI inherited functionality passes the additional argument to let other handlers know it was triggered by script
		if ( is_triggered_programmatically ) {
			return;
		}

		// do not update preview if we are updating settings based on previous user selection
		if ( ignore_setting_update_flag ) {
			return;
		}

		let $field = $( this );

		// some settings do not need to trigger AJAX update for preview
		const noPreviewUpdateSettings = [ 'search_result_mode', 'search_box_results_style', 'article_search_box_results_style', 'article_search_result_mode', 'advanced_search_mp_show_top_category',
			'advanced_search_ap_show_top_category', 'advanced_search_mp_results_list_size', 'advanced_search_ap_results_list_size', 'advanced_search_text_highlight_enabled',
			'advanced_search_mp_results_page_size', 'advanced_search_ap_results_page_size', 'advanced_search_mp_box_results_style', 'advanced_search_ap_box_results_style', 'article_views_counter_method', 'back_navigation_mode'];
		if ( noPreviewUpdateSettings.includes( $field[0].name ) ) {
			return;
		}

		// Map of toggle settings to their corresponding selectors for instant show/hide (sidebars cannot use this feature since the page layout needs to be adjusted for enabled/disabled sidebar)
		const instantToggleSettings = {
			// TODO FUTURE: does not give any advantage since it can have different selectors while all the selectors should be present to skip the AJAX update correctly
			// 'article_search_toggle': ['#eckb-article-header .epkb-doc-search-container', '#eckb-article-header #asea-doc-search-container'],

			// TODO FUTURE: does not give any advantage since is designed for handle new and old article at the same time, while all the selectors should be present to skip the AJAX update correctly
			// 'print_button_enable': ['.eckb-article-content-toolbar-button-container', '.eckb-print-button-meta-container'],

			// TODO FUTURE: it looks like the 'eckb-ach__article-meta__views_counter' is never printed on the page
			// 'article_views_counter_enable': ['.eckb-article-content-article-views-counter-container', '.eckb-ach__article-meta__views_counter'],
			// 'article_content_enable_views_counter': ['.eckb-article-content-article-views-counter-container', '.eckb-ach__article-meta__views_counter'],

			// TODO FUTURE: has effect only when both top and bottom features enabled, otherwise reloads the preview via AJAX when toggle turned 'ON'
			'article_content_enable_author': ['.eckb-article-content-author-container', '.eckb-ach__article-meta__author'],
			'article_content_enable_created_date': ['.eckb-article-content-created-date-container', '.eckb-ach__article-meta__date-created'],
			'article_content_enable_last_updated_date': ['.eckb-article-content-last-updated-date-container', '.eckb-ach__article-meta__date-updated'],

			'article_content_enable_article_title': ['#eckb-article-content-title-container'],
			'article_content_enable_back_navigation': ['#eckb-article-back-navigation-container'],
			'breadcrumb_enable': ['#eckb-article-content-breadcrumb-container'],
			'prev_next_navigation_enable': ['.epkb-article-navigation-container'],
			'meta-data-footer-toggle': ['.eckb-article-content-footer__article-meta'],
		};

		// Special handling for instant toggle settings
		if ( instantToggleSettings[$field[0].name] ) {
			const isToggleOn = $field.attr('type') === 'checkbox' ? $field.prop('checked') : $field.val() === 'on';
			const selectors = instantToggleSettings[$field[0].name];
			let foundExisting = false;

			// If toggling OFF, hide all matching elements and skip backend call
			if ( ! isToggleOn ) {
				selectors.forEach(selector => {
					$(selector).hide();
				} );
				return;
			}

			// Check if any of the elements exist
			for ( const one_selector_index in selectors ) {
				const $element = $( selectors[one_selector_index] );
				if ( $element.length > 0 ) {
					foundExisting = true;
				}
				// If any of the selectors is missing, then consider all are missing
				else {
					foundExisting = false;
					break;
				}
			}

			// If toggling ON and elements exist, show them and skip backend call
			if ( isToggleOn && foundExisting ) {
				selectors.forEach( selector => {
					$( selector ).show();
				} );
				return;
			}
			// If toggling OFF and elements do not exist, skip backend call
			else if ( ! isToggleOn && ! foundExisting ) {
				return;
			}
			
			// If toggling ON but elements don't exist, continue to backend call
			// Don't return here - let it fall through to updatePreview
		}

		// Special handling for TOC fields synchronization
		if ( $field.attr( 'name' ) === 'toc_toggler' || 
			 $field.attr( 'name' ) === 'toc_left' || 
			 $field.attr( 'name' ) === 'toc_right' ) {
			
			ignore_setting_update_flag = true;
			
			// Handle TOC toggler change
			if ( $field.attr( 'name' ) === 'toc_toggler' ) {
				const isChecked = $field.prop( 'checked' );
				
				if ( isChecked ) {
					// Case 1: Try to set to first available position on Left Sidebar
					if ( set_toc_to_article_sidebar_position( 'left' ) ) {
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_left"]' ).prop( 'checked', true );
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_content"], #epkb-fe__editor input[name="toc_locations"][value="toc_right"]' ).prop( 'checked', false );
					}
					// Case 2: If all positions of Left Sidebar have components, try Right Sidebar
					else if ( set_toc_to_article_sidebar_position( 'right' ) ) {
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_right"]' ).prop( 'checked', true );
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_left"], #epkb-fe__editor input[name="toc_locations"][value="toc_content"]' ).prop( 'checked', false );
					}
					// Case 3: If all positions of both Sidebars have components, set to Content
					else {
						$( '#epkb-fe__editor #toc_content' ).val( '1' ).trigger( 'change' );
						update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_content"]' ).prop( 'checked', true );
						$( '#epkb-fe__editor input[name="toc_locations"][value="toc_left"], #epkb-fe__editor input[name="toc_locations"][value="toc_right"]' ).prop( 'checked', false );
					}
				} else {
					// Unselect all locations
					$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
					update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
					$( '#epkb-fe__editor input[name="toc_locations"]' ).prop( 'checked', false );
					unselect_toc_in_article_sidebar_positions();
				}
			}
			
			// Handle TOC location change (radio buttons)
			else if ( $field.attr( 'name' ) === 'toc_locations' && $field.prop( 'checked' ) ) {
				const location = $field.val();
				let is_toc_set = false;
				
				switch ( location ) {
					case 'toc_left':
						unselect_toc_in_article_sidebar_positions();
						$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
						update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
						is_toc_set = set_toc_to_article_sidebar_position( 'left' );
						break;
						
					case 'toc_content':
						unselect_toc_in_article_sidebar_positions();
						$( '#epkb-fe__editor #toc_content' ).val( '1' ).trigger( 'change' );
						update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
						is_toc_set = true;
						break;
						
					case 'toc_right':
						unselect_toc_in_article_sidebar_positions();
						$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
						update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
						is_toc_set = set_toc_to_article_sidebar_position( 'right' );
						break;
				}
				
				// If failed to set TOC position, uncheck the location
				if ( !is_toc_set ) {
					$field.prop( 'checked', false );
				}
				
				// Uncheck other TOC location checkboxes (except the current one if successfully set)
				$( '#epkb-fe__editor #toc_locations input' ).each( function() {
					// Skip current Location input if it was successfully set
					if ( $( this ).val() === location && is_toc_set ) {
						return true;
					}
					
					// Unselect Location input
					$( this ).prop( 'checked', false );
				});
				
				check_toc_toggler_state();
			}
			
			// Handle Article Sidebar Position dropdowns
			else if ( $field.attr( 'name' ) === 'toc_left' || $field.attr( 'name' ) === 'toc_right' ) {
				const sidebarSuffix = $field.attr( 'name' ) === 'toc_left' ? 'left' : 'right';
				const currentValue = parseInt( $field.val() );
				
				if ( currentValue > 0 ) {
					// TOC position selected
					const locationValue = sidebarSuffix === 'left' ? 'toc_left' : 'toc_right';
					$( '#epkb-fe__editor input[name="toc_locations"][value="' + locationValue + '"]' ).prop( 'checked', true );
					$( '#epkb-fe__editor input[name="toc_locations"]' ).not( '[value="' + locationValue + '"]' ).prop( 'checked', false );
					$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
					update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
					
					// Unselect the other sidebar
					const otherSidebar = sidebarSuffix === 'left' ? 'right' : 'left';
					const $otherToc = $( '#epkb-fe__editor #toc_' + otherSidebar );
					if ( parseInt( $otherToc.val() ) > 0 ) {
						$otherToc.val( '0' ).trigger( 'change' );
						update_custom_dropdown_display( $otherToc );
					}
				}
				
				check_toc_toggler_state();
			}
			
			ignore_setting_update_flag = false;
			
			// Continue to updatePreview
		}

		// Settings UI inherited logic - need to unselect conflicting dropdowns here before the config values are collected and passed to AJAX update
		let current_unselection_group = $field.closest( '[data-custom-unselection-group]' ).data( 'custom-unselection-group' );
		let current_nonzero_unselection_group = $field.closest( '[data-custom-nonzero-unselection-group]' ).data( 'custom-nonzero-unselection-group' );
		if ( current_unselection_group || current_nonzero_unselection_group ) {

			ignore_setting_update_flag = true;

			// Handle change of value
			const current_input_name = $field.attr( 'name' );

			// Unset current value in other dropdowns of the current unselection group
			$( '[data-custom-unselection-group="' + current_unselection_group + '"] select' ).each( function() {
				if ( current_input_name !== $( this ).attr( 'name' ) && $( this ).val() === $field.val() ) {
					$( this ).val( 'none' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
					$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
				}
			} );

			// Unset other dropdowns of the current unselection group if any of them has non-zero value
			$( '[data-custom-nonzero-unselection-group="' + current_nonzero_unselection_group + '"] select' ).each( function() {
				if ( parseInt( $field.val() ) > 0 && current_input_name !== $( this ).attr( 'name' ) && parseInt( $( this ).val() ) > 0 ) {
					$( this ).val( '0' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
					$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
				}
			} );

			ignore_setting_update_flag = false;
		}

		// Article Right Sidebar (the UI has hidden fields which need to update on visual UI change - required to have the correct values in KB config when call AJAX update)
		if ( $field.attr( 'name' ) === 'nav_sidebar_right' ) {

			// When unselected, then set current sidebar navigation type to none
			const current_value = $( this ).val();
			if ( current_value === '0' || current_value === 0 ) {
				$( '[name="article_nav_sidebar_type_right"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
				return;
			}

			// When sidebar switched by 'Categories and Articles Navigation' dropdown, then keep its type in the selected sidebar (e.g. copy navigation type value from opposite sidebar to the current sidebar)
			const current_left_sidebar_type = $( '[name="article_nav_sidebar_type_left"]:checked' ).val();
			if ( current_left_sidebar_type !== 'eckb-nav-sidebar-none' ) {
				$( '[name="article_nav_sidebar_type_left"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
				$( '[name="article_nav_sidebar_type_right"][value="' + current_left_sidebar_type + '"]' ).parent().find( '.epkb-label' ).click();
			}
		}

		// Article Left Sidebar (the UI has hidden fields which need to update on visual UI change - required to have the correct values in KB config when call AJAX update)
		if ( $field.attr( 'name' ) === 'nav_sidebar_left' ) {

			// When unselected, then set current sidebar navigation type to none
			const current_value = $( this ).val();
			if ( current_value === '0' || current_value === 0 ) {
				$( '[name="article_nav_sidebar_type_left"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
				return;
			}

			// When sidebar switched by 'Categories and Articles Navigation' dropdown, then keep its type in the selected sidebar (e.g. copy navigation type value from opposite sidebar to the current sidebar)
			const current_right_sidebar_type = $( '[name="article_nav_sidebar_type_right"]:checked' ).val();
			if ( current_right_sidebar_type !== 'eckb-nav-sidebar-none' ) {
				$( '[name="article_nav_sidebar_type_right"][value="eckb-nav-sidebar-none"]' ).parent().find( '.epkb-label' ).click();
				$( '[name="article_nav_sidebar_type_left"][value="' + current_right_sidebar_type + '"]' ).parent().find( '.epkb-label' ).click();
			}
		}

		// For radio buttons, only proceed if the changed element is the selected one.
		if ( $field.attr( 'type' ) === 'radio' && ! $field.is( ':checked' ) ) {
			return;
		}

		// Color-picker handles its update through 'iris' library
		if ( $field.hasClass( 'wp-color-picker' ) ) {
			return;
		}

		// Module position handles its change itself
		if ( $field.closest( '.epkb-row-module-position' ).length ) {
			return;
		}

		// Module selector is excluded from the Editor UI
		if ( $field.closest( '.epkb-fe__settings-section--module-selection' ).length > 0 ) {
			return;
		}

		// Unselected module does not need to trigger AJAX update for preview
		if ( $field.closest( '.epkb-fe__feature-settings' ).attr( 'data-row-number' ) === 'none' ) {
			return;
		}

		// For some settings need to reload the entire page
		if ( $field.attr( 'name' ) === 'templates_for_kb' ) {
			update_preview_via_page_reload( event );
			return;
		}

		updatePreview( event );
	});

	function prepare_color_picker( feature_name ) {
		let isColorInputSync = false;
		$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list .epkb-admin__color-field input' ).wpColorPicker({
			change: function( colorEvent, ui) {

				// Do nothing for programmatically changed value (for sync purpose)
				if ( isColorInputSync ) {
					return;
				}

				isColorInputSync = true;

				// Get current color value
				let color_value = $( colorEvent.target ).wpColorPicker( 'color' );
				let setting_name = $( colorEvent.target ).attr( 'name' );

				// Sync other color pickers that have the same name
				$( '.epkb-admin__color-field input[name="' + setting_name + '"]' ).not( colorEvent.target ).each( function () {
					$( this ).wpColorPicker( 'color', color_value );
				} );

				isColorInputSync = false;
			},
		});

		// Ensure the WordPress color-picker is ready before 'iris' library options are available
		setTimeout( function() {
			$( '#epkb-fe__editor .epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-list input.wp-color-picker' ).iris( 'option', 'change', colorpicker_update );
		}, 100 );
	}

	// Preview Update: on color change
	setTimeout( function() {
		$( '#epkb-fe__editor input.wp-color-picker' ).iris( 'option', 'change', colorpicker_update );
	}, 100 );

	// Before send AJAX request for the preview update, the color-picker should wait until the user stopped to change the color
	let colorpicker_update_timeout = false;
	function colorpicker_update( event, ui ) {

		// Remove previous timeout handler
		if ( colorpicker_update_timeout ) {
			clearTimeout( colorpicker_update_timeout );
		}

		// Set current timeout handler
		colorpicker_update_timeout = setTimeout( function () {
			updatePreview( event, ui );
			colorpicker_update_timeout
		}, 200 );
	}

	// Save settings
	$( document ).on( 'click', '#epkb-fe__action-save', function( event ) {

		let kb_config = collectConfig();
		
		// Determine which save action to use based on the active feature's page type
		const $activeFeatureSettings = $( '#epkb-fe__editor .epkb-fe__feature-settings--active' );
		const kb_page_type = $activeFeatureSettings.data( 'kb-page-type' );
		let save_action = 'eckb_save_fe_settings'; // default for main page
		
		if ( kb_page_type === 'article-page' ) {
			save_action = 'eckb_save_fe_article_settings';
		} else if ( kb_page_type === 'archive-page' ) {
			save_action = 'eckb_save_fe_archive_settings'; // for future implementation
		}

		// Show loading dialog based on page type
		let loadingContainer;
		if ( kb_page_type === 'article-page' ) {
			loadingContainer = $( '#eckb-article-page-container-v2' );
		} else if ( kb_page_type === 'archive-page' ) {
			loadingContainer = $( '#eckb-archive-page-container' );
		} else {
			loadingContainer = $( '#epkb-modular-main-page-container, #eckb-kb-template' );
		}
		epkb_loading_Dialog( 'show', '', loadingContainer );

		$.ajax( {
			url: epkb_vars.ajaxurl,
			method: 'POST',
			data: {
				action: save_action,
				_wpnonce_epkb_ajax_action: epkb_vars.nonce,
				kb_id: frontendEditor.data( 'kbid' ),
				new_kb_config: kb_config,
				selected_search_preset: selected_search_preset
			},
			success: function( response ) {

				if ( response.data && response.data.message ) {
					epkb_show_success_notification( response.data.message );
				}

				// Handle successful AJAX response.
				if ( response.success ) {

				} else {
					try {
						let responseJson = JSON.parse( response );
						if ( responseJson.message ) {
							$( 'body' ).append( $( responseJson.message ) );
						}
					} catch ( e ) {}
				}

				// Remove loading dialog
				epkb_loading_Dialog( 'remove', '', loadingContainer );

				// Reset stored search design preset
				selected_search_preset = 'current';
			},
			error: function( jqXHR, textStatus, errorThrown ) {
				// Handle AJAX request errors with detailed information
				let errorMessage = buildDetailedErrorMessage( jqXHR, textStatus, errorThrown, epkb_vars.fe_save_settings_error );
				
				show_report_error_form( errorMessage );
				epkb_loading_Dialog( 'remove', '', loadingContainer );
			}
		} );
	} );

	function collectConfig() {
		// collect settings
		let kb_config = {};

		frontendEditor.find( 'input, select, textarea' ).each( function(){

			// ignore inputs with empty name and pro feature fields (an ad field)
			if ( ! $( this ).attr( 'name' ) || ! $( this ).attr( 'name' ).length
				|| $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tag' ).length
				|| $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tag-container' ).length ) {
				return true;
			}

			if ( $( this ).attr( 'type' ) === 'checkbox' ) {

				// checkboxes multiselect
				if ( $( this ).closest( '.epkb-admin__checkboxes-multiselect' ).length ) {
					if ( $( this ).prop( 'checked' ) ) {
						if ( ! kb_config[ $(this).attr( 'name' ) ] ) {
							kb_config[ $( this ).attr( 'name' ) ] = [];
						}
						kb_config[ $( this ).attr( 'name' ) ].push( $( this ).val() );
					}

					// single checkbox
				} else {
					kb_config[ $( this ).attr( 'name' ) ] = $( this ).prop( 'checked' ) ? 'on' : 'off';
				}
				return true;
			}

			if ( $( this ).attr('type') === 'radio' ) {
				if ( $( this ).prop( 'checked' ) ) {
					kb_config[ $( this ).attr( 'name' ) ] = $( this ).val();
				}
				return true;
			}

			if ( typeof $( this ).attr( 'name' ) == 'undefined' ) {
				return true;
			}
			kb_config[ $( this ).attr( 'name' ) ] = $( this ).val();
		});

		// Ensure 'faq_group_ids' is set even if no FAQ Groups are selected
		if ( $( '[name="faq_group_ids"]' ).length && typeof kb_config.faq_group_ids == 'undefined' ) {
			kb_config.faq_group_ids = 0;
		}

		// Add current module positions for all enabled modules
		$('.epkb-ml__row').each(function() {
			const $row = $(this);
			const feature = $row.attr('data-feature');
			const position = $row.attr('data-position');
			
			if (feature && position) {
				kb_config[feature + '_module_position'] = position;
			}
		});
		
		// Set 'none' position for disabled modules
		$('.epkb-fe__feature-settings').each(function() {
			const $featureSettings = $(this);
			const feature = $featureSettings.attr('data-feature');
			const rowNumber = $featureSettings.attr('data-row-number');
			
			if (feature && rowNumber === 'none') {
				kb_config[feature + '_module_position'] = 'none';
			}
		});

		return kb_config;
	}
	

	/*************************************************************************************************
	 *
	 *          TOC Position Synchronization
	 *
	 ************************************************************************************************/

	// Helper function to unselect TOC in article sidebar positions
	function unselect_toc_in_article_sidebar_positions() {
		$( '#epkb-fe__editor #toc_left, #epkb-fe__editor #toc_right' ).each( function() {
			if ( parseInt( $( this ).val() ) > 0 ) {
				$( this ).val( '0' ).trigger( 'change' );
				update_custom_dropdown_display( $( this ) );
			}
		} );
	}

	// Helper function to set TOC to article sidebar position
	function set_toc_to_article_sidebar_position( sidebar_suffix ) {
		let is_toc_set = false;
		const $sidebar_toc = $( '#epkb-fe__editor #toc_' + sidebar_suffix );
		if ( $sidebar_toc.length && parseInt( $sidebar_toc.val() ) === 0 ) {
			ignore_setting_update_flag = true;
			$sidebar_toc.val( '3' ).trigger( 'change' );
			update_custom_dropdown_display( $sidebar_toc );
			ignore_setting_update_flag = false;
			is_toc_set = true;
		}
		return is_toc_set;
	}

	// Check and update TOC toggler state based on locations
	function check_toc_toggler_state() {
		let state = false;
		
		// Check if any location is selected
		$( '#epkb-fe__editor input[name="toc_locations"]' ).each( function() {
			if ( $( this ).prop('checked' ) ) {
				state = true;
			}
		} );
		
		// Check if any sidebar position is selected
		if ( parseInt( $( '#epkb-fe__editor #toc_left' ).val() ) > 0 || 
			 parseInt( $( '#epkb-fe__editor #toc_right' ).val() ) > 0 ||
			 parseInt( $( '#epkb-fe__editor #toc_content' ).val() ) > 0 ) {
			state = true;
		}
		
		ignore_setting_update_flag = true;
		$( '#epkb-fe__editor #toc_toggler input' ).prop( 'checked', state );
		ignore_setting_update_flag = false;
	}

	// TOC Location Click Handler - matches backend behavior
	$( document ).on( 'click', '#epkb-fe__editor input[name="toc_locations"]', function() {
		let current_location = $( this ).prop( 'value' );
		let input_checked = $( this ).prop( 'checked' );
		let is_toc_set = false;

		ignore_setting_update_flag = true;

		switch ( current_location ) {

			case 'toc_left':
				unselect_toc_in_article_sidebar_positions();
				$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
				update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
				if ( input_checked ) {
					is_toc_set = set_toc_to_article_sidebar_position( 'left' );
				}
				break;

			case 'toc_content':
				unselect_toc_in_article_sidebar_positions();
				$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
				update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
				if ( input_checked ) {
					$( '#epkb-fe__editor input[name="toc_locations"][value="toc_content"]' ).prop( 'checked', true );
					$( '#epkb-fe__editor input[name="toc_locations"][value="toc_left"], #epkb-fe__editor input[name="toc_locations"][value="toc_right"]' ).prop( 'checked', false );
					$( '#epkb-fe__editor #toc_content' ).val( '1' ).trigger( 'change' );
					update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
					is_toc_set = true;
				}
				break;

			case 'toc_right':
				unselect_toc_in_article_sidebar_positions();
				$( '#epkb-fe__editor #toc_content' ).val( '0' ).trigger( 'change' );
				update_custom_dropdown_display( $( '#epkb-fe__editor #toc_content' ) );
				if ( input_checked ) {
					is_toc_set = set_toc_to_article_sidebar_position( 'right' );
				}
				break;

			default:
				break;
		}

		$( '#epkb-fe__editor input[name="toc_locations"]' ).each( function() {
			// Skip current Location input (unset current location if failed to set toc position to the current location)
			if ( $( this ).prop( 'value' ) === current_location && is_toc_set ) {
				return true;
			}

			// Unselect Location input
			$( this ).prop( 'checked', false );
		} );

		// Refresh toggler
		check_toc_toggler_state();

		ignore_setting_update_flag = false;

		// Trigger preview update
		updatePreview( { target: this } );
	} );


	/*************************************************************************************************
	 *
	 *          Module Position Change
	 *
	 ************************************************************************************************/

	/* ------------------------------------------------------------------
   Modular-page position helper (v2 – toggle + radio buttons)
   – Keeps every module's toggle / "Move Up" / "Move Down" controls
     in sync with the real order of .epkb-ml__row elements.
   – Runs once on DOM-ready and after every control interaction.
   ------------------------------------------------------------------ */

	const MAX_POS = 5;                                              // hard limit
	const $container = $('#epkb-modular-main-page-container');

	/* ─── utilities ──────────────────────────────────────────────── */
	const rows            = ()       => $('.epkb-ml__row');         // live collection
	const rowByFeature    = slug     => $(`.epkb-ml__row[data-feature="${slug}"]`);
	const enabledCount    = ()       => rows().length;

	/* (re)build the on-page rows list according to their data-position      */
	function sortRows() {
		rows()
		.sort((a, b) => +$(a).attr('data-position') - +$(b).attr('data-position'))
		.appendTo($container);
	}

	/* renumber 1…N after any removal or swap (keeps gaps out)               */
	function renumberRows() {
		rows().each((idx, el) => {
			const pos = idx + 1;
			const $row = $(el);
			const feature = $row.attr('data-feature');

			$row.attr('data-position', pos);

			// Update the corresponding feature settings' data-row-number attribute
			if (feature) {
				$('.epkb-fe__feature-settings[data-feature="' + feature + '"]').attr('data-row-number', pos);
			}
		});
		normalizeSettingsAfterRowsRenumbering();
	}

	function normalizeSettingsAfterRowsRenumbering() {

		// Normalize row numbers
		rows().each( ( idx, el ) => {
			const $row = $( el );
			const position = parseInt( $row.attr( 'data-position' ) );
			let prev_position = $row.attr( 'id' );
			prev_position = prev_position ? parseInt( prev_position.replaceAll( 'epkb-ml__row-', '' ) ) : position;
			const $other_row = $( '#epkb-ml__row-' + position );

			if ( position === prev_position ) {
				return;
			}

			let $row_module_setting = $( '[name="ml_row_' + prev_position + '_module"]' );
			let $row_width_setting = $( '[name="ml_row_' + prev_position + '_desktop_width"]' );
			let $row_width_units_setting = $( '[name="ml_row_' + prev_position + '_desktop_width_units"]' );
			const width_unit_value = $( '[name="ml_row_' + prev_position + '_desktop_width_units"]:checked' ).val();

			let $other_row_module_setting = $( '[name="ml_row_' + position + '_module"]' );
			let $other_row_width_setting = $( '[name="ml_row_' + position + '_desktop_width"]' );
			let $other_row_width_units_setting = $( '[name="ml_row_' + position + '_desktop_width_units"]' );
			const other_width_unit_value = $( '[name="ml_row_' + position + '_desktop_width_units"]:checked' ).val();

			const feature = $row_width_setting.closest( '.epkb-fe__feature-settings' ).find( '.epkb-row-module-position.epkb-settings-control-type-toggle .epkb-settings-control__input__toggle' ).prop( 'checked' )
				? $row_width_setting.closest( '.epkb-fe__feature-settings' ).attr( 'data-feature' )
				: 'none';
			const other_feature = $other_row_width_setting.closest( '.epkb-fe__feature-settings' ).find( '.epkb-row-module-position.epkb-settings-control-type-toggle .epkb-settings-control__input__toggle' ).prop( 'checked' )
				? $other_row_width_setting.closest( '.epkb-fe__feature-settings' ).attr( 'data-feature' )
				: 'none';

			$row.attr( 'id', 'epkb-ml__row-' + position );
			$other_row.attr( 'id', 'epkb-ml__row-' + prev_position );

			$row_module_setting
				.attr( 'id', 'ml_row_' + position + '_module' )
				.attr( 'name', 'ml_row_' + position + '_module' );
				//.val( feature );
			$row_width_setting
				.attr( 'id', 'ml_row_' + position + '_desktop_width' )
				.attr( 'name', 'ml_row_' + position + '_desktop_width' )
				.parent().find( 'label' ).attr( 'for', 'ml_row_' + position + '_desktop_width' )
				.closest( '#ml_row_' + prev_position + '_desktop_width' ).attr( 'id', 'ml_row_' + position + '_desktop_width' );
			$row_width_units_setting.closest( '#ml_row_' + prev_position + '_desktop_width_units' ).attr( 'id', 'ml_row_' + position + '_desktop_width_units' );
			$row_width_units_setting.each( function () {
				let current_id = $( this ).attr( 'id' );
				current_id = current_id.replaceAll( 'ml_row_' + prev_position, 'ml_row_' + position );
				$( this ).attr( 'id', current_id ).attr( 'name', 'ml_row_' + position + '_desktop_width_units' ).parent().find( 'label' ).attr( 'for', current_id );
			} );

			$other_row_module_setting
				.attr( 'id', 'ml_row_' + prev_position + '_module' )
				.attr( 'name', 'ml_row_' + prev_position + '_module' );
				// .val( other_feature );
			$other_row_width_setting
				.attr( 'id', 'ml_row_' + prev_position + '_desktop_width' )
				.attr( 'name', 'ml_row_' + prev_position + '_desktop_width' )
				.parent().find( 'label' ).attr( 'for', 'ml_row_' + prev_position + '_desktop_width' )
				.closest( '#ml_row_' + position + '_desktop_width' ).attr( 'id', 'ml_row_' + prev_position + '_desktop_width' );
			$other_row_width_units_setting.closest( '#ml_row_' + position + '_desktop_width_units' ).attr( 'id', 'ml_row_' + prev_position + '_desktop_width_units' );
			$other_row_width_units_setting.each( function () {
				let current_id = $( this ).attr( 'id' );
				current_id = current_id.replaceAll( 'ml_row_' + position, 'ml_row_' + prev_position );
				$( this ).attr( 'id', current_id ).attr( 'name', 'ml_row_' + prev_position + '_desktop_width_units' ).parent().find( 'label' ).attr( 'for', current_id );
			} );

			$row_width_units_setting.each( function () {
				if ( $( this ).val() === width_unit_value ) {
					$( this ).prop( 'checked', true );
				}
			} );

			$other_row_width_units_setting.each( function () {
				if ( $( this ).val() === other_width_unit_value ) {
					$( this ).prop( 'checked', true );
				}
			} );

			let inline_styles_container = $( '[id^="epkb-mp-frontend-modular-"][id$="-layout-inline-css"]' )
			let inline_styles = inline_styles_container.html();
			inline_styles = inline_styles
				.replaceAll( '#epkb-ml__row-' + position, '#epkb-ml__row-temp' )
				.replaceAll( '#epkb-ml__row-' + prev_position, '#epkb-ml__row-' + position )
				.replaceAll( '#epkb-ml__row-temp', '#epkb-ml__row-' + prev_position );
			inline_styles_container.html( inline_styles );
		});
	}

	/* create a minimal placeholder row when a module is (re)enabled         */
	function addRow(toggle, slug) {
		if (enabledCount() >= MAX_POS || rowByFeature(slug).length) return;

		const new_row_id = 'epkb-ml__row-' + $( toggle ).closest( '#epkb-fe__editor .epkb-fe__feature-settings--active' ).find( '[id^="ml_row_"][id$="_desktop_width"]' ).attr( 'id' ).replaceAll( 'ml_row_', '' ).replaceAll( '_desktop_width', '' );

		// Create new row with temporary position 0 (will be fixed by renumberRows)
		$('<div>', {
			id:            new_row_id,
			class:         'epkb-ml__row',
			'data-position': 0,
			'data-feature':  slug
		}).prependTo($container);

		// Renumber all rows starting from 1
		renumberRows();
		sortRows();

		// Update the feature settings' data-row-number attribute to match the new position
		const newPosition = rowByFeature(slug).attr('data-position');
		$('.epkb-fe__feature-settings[data-feature="' + slug + '"]').attr('data-row-number', newPosition);

		// Trigger updatePreview with the correct context
		const $featureSettings = $('.epkb-fe__feature-settings[data-feature="' + slug + '"]');
		const $dummyEvent = {
			target: $featureSettings.find('input, select').first()[0],
			preventDefault: function() {}
		};

		if ($dummyEvent.target) {
			updatePreview($dummyEvent);
		}

		// Update all position controls to reflect the new state
		refreshPositionControls();
	}

	/* remove a row when a module is disabled                                */
	function removeRow(slug) {
		rowByFeature(slug).remove();
		renumberRows();
		sortRows();
		refreshPositionControls();
	}

	/* refresh Module Position settings to reflect the current state         */
	function refreshPositionControls() {
		const total = enabledCount();

		// Only target module position toggles within the frontend editor
		// More specific selector to avoid affecting other controls
		$('#epkb-fe__editor .epkb-row-module-position .epkb-settings-control__input__toggle').each(function () {
			const $toggle = $(this);
			const slug    = $toggle.attr('name');
			const $wrap   = $toggle.closest('.epkb-fe__settings-section-body');
			const $radios = $wrap.find('.epkb-radio-buttons-container');
			const $up     = $radios.find('input[value="move-up"]');
			const $down   = $radios.find('input[value="move-down"]');
			const $row    = rowByFeature(slug);
			const enabled = $row.length > 0;
			const $featureSettings = $('.epkb-fe__feature-settings[data-feature="' + slug + '"]');

			/* toggle state & label */
			$toggle.prop('checked', enabled);

			/* Update the feature settings' data-row-number attribute */
			if (enabled) {
				const position = $row.attr('data-position');
				$featureSettings.attr('data-row-number', position);
			} else {
				$featureSettings.attr('data-row-number', 'none');
			}

			/* show/hide radio buttons */
			if (!enabled || total <= 1) {
				$radios.hide();
			} else {
				$radios.show();
				const pos = +$row.attr('data-position');
				$up.prop('disabled',   pos === 1);
				$down.prop('disabled', pos === total);
			}

			/* show/hide other settings sections based on module state */
			if (enabled) {
				// Show all settings sections when module is enabled
				$featureSettings.find('.epkb-fe__settings-section:not(.epkb-fe__settings-section--module-position)').removeClass('epkb-fe__settings-section--hide');
			} else {
				// Hide all settings sections except module position when module is disabled
				$featureSettings.find('.epkb-fe__settings-section:not(.epkb-fe__settings-section--module-position)').addClass('epkb-fe__settings-section--hide');
			}

			/* clear the momentary radio selection */
			$radios.find('input[type="radio"]').prop('checked', false);
		});
	}

	/* ─── event handlers ────────────────────────────────────────────────── */
	// 1. toggle enable / disable ------------------------------------------
	$( document ).on( 'change', '#epkb-fe__editor .epkb-row-module-position .epkb-settings-control__input__toggle', function () {
		const slug = this.name;

		if ( this.checked ) {
			addRow( this, slug );
		} else {
			removeRow( slug );
		}
	} );

	// 2. move-up / move-down ----------------------------------------------
	$( document ).on('change', '.epkb-input[value="move-up"], .epkb-input[value="move-down"]', function () {
		const $btn      = $(this);
		const dir       = $btn.val() === 'move-up' ? -1 : +1;
		const slug      = $btn.closest('.epkb-row-module-position').data('module');
		const $row      = rowByFeature(slug);
		const oldPos    = +$row.attr('data-position');
		const newPos    = oldPos + dir;
		const $otherRow = rows().filter((_, el) => +$(el).attr('data-position') === newPos);

		if (!$row.length || !$otherRow.length) return;    // already at edge

		// swap the two rows' data-position values
		$row.attr('data-position', newPos);
		$otherRow.attr('data-position', oldPos);

		normalizeSettingsAfterRowsRenumbering();

		sortRows();
		refreshPositionControls();  // Triggered by move up/down as required
	});

	/* ─── initial run ───────────────────────────────────────────────────── */
	renumberRows();        // make sure positions start at 1‥N, no gaps
	sortRows();
	refreshPositionControls();  // Keep for initial setup


	/*************************************************************************************************
	 *
	 *          Various
	 *
	 ************************************************************************************************/

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

	function epkb_get_page_params() {
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
	epkb_get_page_params();

	//Resize Visual Helper Collect Page Parameters
	$( window ).on('resize', function() {
		epkb_get_page_params();
	} );

	function epkb_loading_Dialog( displayType, message, parent_container ){

		if ( displayType === 'show' ) {

			let output =
				'<div class="epkb-fe-dialog-box-loading">' +

				//<-- Header -->
				'<div class="epkb-admin-dbl__header">' +
				'<div class="epkb-admin-dbl-icon epkbfa epkbfa-hourglass-half"></div>'+
				(message ? '<div class="epkb-admin-text">' + message + '</div>' : '' ) +
				'</div>'+

				'</div>';

			//Add message output at the end of Body Tag
			parent_container.append( output );

		} else if( displayType === 'remove' ) {

			// Remove loading dialogs.
			parent_container.find( '.epkb-fe-dialog-box-loading' ).remove();
		}
	}

	// Close Button Message if Close Icon clicked
	$( document ).on( 'click', '.epkb-close-notice', function() {
		let bottom_message = $( this ).closest( '.eckb-bottom-notice-message' );
		bottom_message.addClass( 'fadeOutDown' );
		setTimeout( function() {
			bottom_message.html( '' );
		}, 10000 );
	} );

	// SHOW INFO MESSAGES
	function epkb_admin_notification( $title, $message , $type ) {
		return '<div class="eckb-bottom-notice-message">' +
			'<div class="contents">' +
			'<span class="' + $type + '">' +
			($title ? '<h4>' + $title + '</h4>' : '' ) +
			($message ? '<p>' + $message + '</p>': '') +
			'</span>' +
			'</div>' +
			'<div class="epkb-close-notice epkbfa epkbfa-window-close"></div>' +
			'</div>';
	}

	function epkb_show_success_notification( $message, $title = '' ) {
		$('.eckb-bottom-notice-message').remove();
		$('body').append( epkb_admin_notification( $title, $message, 'success' ) );
	}

	// Re-open editor if the page was reloaded programmatically on setting change
	( function() {
		const current_url = new URL( window.location.href );
		const feature_name = current_url.searchParams.get( 'epkb_fe_reopen_feature' )
		if ( feature_name && feature_name.length > 0 ) {

			// Re-open editor feature
			$( '.epkb-fe__toggle' ).trigger( 'click' );
			$( '#epkb-fe__editor .epkb-fe__feature-select-button[data-feature="' + feature_name + '"]' ).trigger( 'click' );

			// Remove temporary parameter to avoid re-opening editor on manual page reloading
			current_url.searchParams.delete( 'epkb_fe_reopen_feature' );

			// Clear history to avoid resending the form on manual page reloading
			history.replaceState( null, '', current_url.pathname + current_url.search + current_url.hash );
		}
	} )();

	// Open editor if it is opened by 'epkb_load_editor' action (when FE is hidden by settings it is still possible to open the FE by admin link or direct link in Settings UI)
	( function() {
		const current_url = new URL( window.location.href );
		const action = current_url.searchParams.get( 'action' )
		if ( action && action.length > 0 && action === 'epkb_load_editor' ) {

			open_frontend_editor( null );

			// Prevent re-opening FE on page reload
			current_url.searchParams.delete( 'action' );

			// Clear history to avoid resending reopening the FE on manual page reloading
			history.replaceState( null, '', current_url.pathname + current_url.search + current_url.hash );
		}
	} )();

	// Link to open certain setting of certain feature
	$( document ).on( 'click', '#epkb-fe__editor .epkb-fe__open-feature-setting-link', function ( event ) {

		// Disable the default <a> tag behavior
		event.preventDefault();

		const feature_name = $( this ).attr( 'data-feature' );
		const setting_name = $( this ).attr( 'data-setting' );
		const settings_section = $( this ).attr( 'data-section' );

		// Remove any previous highlighting
		frontendEditor.find( '.epkb-highlighted_setting' ).removeClass( 'epkb-highlighted_setting' );

		// Open the target feature settings
		frontendEditor.find( '.epkb-fe__feature-select-button[data-feature="' + feature_name + '"]' ).trigger( 'click' );

		let setting_offset = 0;

		let $target_container = false;

		// CASE: Link to single setting
		if ( setting_name ) {
			$target_container = frontendEditor.find( '.epkb-fe__feature-settings[data-feature="' + feature_name + '"] [name="' + setting_name + '"]' ).closest( '.epkb-input-group' );
		}

		// CASE: Link to settings section
		if ( settings_section ) {
			$target_container = frontendEditor.find( '.epkb-fe__feature-settings[data-feature="' + feature_name + '"] .epkb-fe__settings-section--' + settings_section );
		}

		if ( $target_container.length > 0 ) {
			setting_offset = $target_container.offset().top - frontendEditor.offset().top - 100;
			setting_offset = setting_offset > 0 ? setting_offset : 0;

			// Highlight the target setting
			$target_container.addClass( 'epkb-highlighted_setting' );
		}

		// Scroll to the target setting
		frontendEditor.animate( {
			scrollTop: setting_offset
		}, 300 );

		// Disable the default <a> tag behavior
		return false;
	} );

	// use to remove link to FE
	function isDiviPresent() {
		// Multiple checks for maximum reliability
		const checks = [
			// Check for Divi CSS classes
			document.querySelector('.et_pb_section, .et_pb_row, .et_pb_column') !== null,
			
			// Check for Divi JavaScript
			typeof window.et_pb_custom !== 'undefined',
			
			// Check for body classes
			document.body.classList.contains('et_divi_theme') ||
			document.body.classList.contains('et_pb_theme'),
			
			// Check for Divi stylesheets
			Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
				 .some(link => link.href.includes('divi') || link.href.includes('et-core')),
				 
			// Check meta generator tag
			document.querySelector('meta[name="generator"][content*="Divi"]') !== null
		];
		
		// Return true if any check passes
		return checks.some(check => check === true);
	}

	/**
	 * Report the Report Error Form
	 */
	// Close Error Submit Form if Close Icon or Close Button clicked
	$( admin_report_error_form ).on( 'click', '.epkb-close-notice, .epkb-admin__error-form__btn-cancel', function(){
		$( admin_report_error_form ).css( 'display', 'none' ).parent().css( 'display', 'none' );
	});

	// Submit the Report Error Form
	$( admin_report_error_form ).find( '#epkb-admin__error-form' ).on( 'submit', function ( event ) {
		event.preventDefault();

		let $form = $(this);

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: epkb_vars.ajaxurl,
			data: $form.serialize(),
			beforeSend: function (xhr) {
				// block the form and add loader
				$form.find( '.epkb-admin__error-form__btn-wrap, input, label, textarea' ).slideUp( 'fast' );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).addClass( 'epkb-admin__error-form__response--active' );
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.fe_sending_error_report );
			}
		}).done(function (response) {
			// success message
			if ( typeof response.success !== 'undefined' && response.success === false ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else if ( typeof response.success !== 'undefined' && response.success === true ) {
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( response.data );
			} else {
				// something went wrong
				$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( epkb_vars.fe_send_report_error );
			}
		}).fail(function (jqXHR, textStatus, errorThrown) {
			// Build detailed error message
			let errorMessage = buildDetailedErrorMessage( jqXHR, textStatus, errorThrown, epkb_vars.fe_send_report_error );
			
			$( admin_report_error_form ).find( '.epkb-admin__error-form__response' ).html( errorMessage );
		}).always(function () {
			// remove form loader
			$( admin_report_error_form ).find( 'input, textarea' ).prop( 'disabled', false );
		});
	});

	/**
	 * Build detailed error message from AJAX error response
	 *
	 * @param {Object} jqXHR - jQuery XMLHttpRequest object
	 * @param {string} textStatus - Text status of the error
	 * @param {string} errorThrown - Exception object if available
	 * @param {string} baseMessage - Base error message to display
	 * @returns {string} Formatted error message with details
	 */
	function buildDetailedErrorMessage( jqXHR, textStatus, errorThrown, baseMessage ) {
		let errorMessage = baseMessage || 'An error occurred';

		// Add specific error details
		if ( textStatus ) {
			errorMessage += '\n\n' + ( epkb_vars.error_details || 'Error Details' ) + ': ' + textStatus;
		}

		if ( errorThrown ) {
			errorMessage += '\n' + errorThrown;
		}

		// If server returned an error message, try to extract it
		if ( jqXHR.responseJSON && jqXHR.responseJSON.message ) {
			errorMessage += '\n\n' + ( epkb_vars.server_response || 'Server Response' ) + ': ' + jqXHR.responseJSON.message;
		} else if ( jqXHR.responseText ) {
			// Try to parse response text for error details
			try {
				const response = JSON.parse( jqXHR.responseText );
				if ( response.message ) {
					errorMessage += '\n\n' + ( epkb_vars.server_response || 'Server Response' ) + ': ' + response.message;
				}
			} catch ( e ) {
				// If not JSON, show first 200 characters of response
				if ( jqXHR.responseText.length > 0 ) {
					errorMessage += '\n\n' + ( epkb_vars.server_response || 'Server Response' ) + ': ' + jqXHR.responseText.substring( 0, 200 );
				}
			}
		}

		return errorMessage;
	}

	function show_report_error_form( error_message ) {
		let error_message_text = error_message ? error_message : '';
		$( admin_report_error_form ).find( '.epkb-admin__error-form__title' ).text( epkb_vars.fe_report_error_title );
		$( admin_report_error_form ).find( '.epkb-admin__error-form__desc' ).text( epkb_vars.fe_report_error_desc );
		$( admin_report_error_form ).find( '#epkb-admin__error-form__message' ).val( error_message_text );
		$( admin_report_error_form ).css( 'display', 'block' ).parent().css( 'display', 'block' );
	}
});