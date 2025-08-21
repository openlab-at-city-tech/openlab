/**
 * Common scripts for admin pages functionality which can be re-used on public frontend for admin form controls
 */
jQuery(document).ready(function($) {

	var epkb = $( '#ekb-admin-page-wrap' );

	// New ToolTip
	$( document ).on( 'click', '#ekb-admin-page-wrap .epkb__option-tooltip__button', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		let tooltip_on = tooltip_contents.css('display') === 'block';

		tooltip_contents.fadeOut();

		if ( ! tooltip_on ) {
			clearTimeout(timeoutOptionTooltip);
			tooltip_contents.fadeIn();
		}
	});
	let timeoutOptionTooltip;
	$( document ).on( 'mouseenter', '#ekb-admin-page-wrap .epkb__option-tooltip__button, #ekb-admin-page-wrap .epkb__option-tooltip__contents', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		clearTimeout(timeoutOptionTooltip);
		tooltip_contents.fadeIn();
	});

	$( document ).on( 'mouseleave', '#ekb-admin-page-wrap .epkb__option-tooltip__button, #ekb-admin-page-wrap .epkb__option-tooltip__contents', function(){
		const tooltip_contents = $( this ).parent().find( '.epkb__option-tooltip__contents' );
		timeoutOptionTooltip = setTimeout( function() {
			tooltip_contents.fadeOut();
		}, 1000);
	});

	// ToolTip
	$( document ).on( 'click', '#ekb-admin-page-wrap .eckb-tooltip-button', function(){
		$( this ).parent().find( '.eckb-tooltip-contents' ).fadeToggle();
	});

	// Toggle the PRO Setting Tooltip
	$( document ).on( 'click', '#ekb-admin-page-wrap .epkb-admin__input-disabled, #ekb-admin-page-wrap .epkb__option-pro-tag', function (){
		let $tooltip = $( this ).closest( '.epkb-input-group' ).find( '.epkb__option-pro-tooltip' );
		let is_visible = $tooltip.is(':visible');

		// hide all pro tooltip
		$( '.epkb__option-pro-tooltip' ).hide();

		// toggle current pro tooltip
		if ( is_visible ) {
			$tooltip.hide();
		} else {
			$tooltip.show();
		}
	});

	// Hide PRO Setting Tooltip if click outside the tooltip
	$( document ).on( 'click', function (e){
		let target = $( e.target );
		if ( ! target.closest( '.epkb__option-pro-tooltip' ).length && ! target.closest( '.epkb-admin__input-disabled' ).length && ! target.closest( '.epkb__option-pro-tag' ).length  ) {
			$( '.epkb__option-pro-tooltip' ).hide();
		}
	});


	function update_custom_selection_green_mark() {

		// Through each custom selection group
		let custom_selection_groups = [];
		$( '[data-custom-selection-group]' ).each( function() {

			// Execute once for each unique group
			let current_selection_group = $( this ).data( 'custom-selection-group' );
			if ( custom_selection_groups.includes( current_selection_group ) ) {
				return;
			}
			custom_selection_groups.push( current_selection_group );

			// Reset all green marks in the current selection group
			$( '[data-custom-selection-group="' + current_selection_group + '"] .epkb-input-custom-dropdown__option-mark' ).html( '' );

			// Through each element of the current selection group
			$( '[data-custom-selection-group="' + current_selection_group + '"]' ).each( function() {

				// The 'none' value does not need green mark
				let selected_value = $( this ).find( 'select' ).val();
				if ( selected_value === 'none' ) {
					return;
				}

				// Set green mark for the current selected value inside other dropdowns of the current selection group
				let selection_label = $( this ).data( 'custom-selection-label' );
				if ( selection_label ) {
					$( '[data-custom-selection-group="' + current_selection_group + '"] [data-value="' + selected_value + '"]:not(.epkb-input-custom-dropdown__option--selected)' ).find( '.epkb-input-custom-dropdown__option-mark' ).html( '(' + selection_label + ')' );
				}
			} );
		} );
	}

	// Custom Dropdown
	$( document ).on( 'click', '.epkb-input-custom-dropdown__input', function( e ) {

		// Avoid trigger of 'click' event for any parent element when clicked on the input
		e.stopPropagation();

		let current_list = $( this ).closest( '.input_container' ).find( '.epkb-input-custom-dropdown__options-list' );

		// Close all option lists
		$( '.epkb-input-custom-dropdown__options-list' ).not( current_list ).hide();

		// Show current option list
		$( current_list ).toggle();
	} );

	// Handle selection for Custom Dropdown
	$( document ).on( 'click', '.epkb-input-custom-dropdown__option', function() {

		// Handle change of value
		let new_value = $( this ).data( 'value' );
		let input_container = $( this ).closest( '.input_container' );
		input_container.find( '.epkb-input-custom-dropdown__option' ).removeClass( 'epkb-input-custom-dropdown__option--selected' );
		$( this ).addClass( 'epkb-input-custom-dropdown__option--selected' );
		let prev_value = input_container.find( 'select' ).val();

		// Hide list of options
		input_container.find( '.epkb-input-custom-dropdown__options-list' ).hide();

		// Change value for the hidden select (to have it filled on form submission)
		input_container.find( 'select' ).val( new_value ).trigger( 'change' );

		// Update label text of the custom dropdown
		let value_label = input_container.find( 'select option[value="' + new_value + '"]' ).html();
		input_container.find( '.epkb-input-custom-dropdown__input span' ).html( value_label );

		let current_input_name = input_container.find( 'select' ).attr( 'name' );

		// Unset current value in other dropdowns of the current unselection group
		let current_unselection_group = $( this ).closest( '[data-custom-unselection-group]' ).data( 'custom-unselection-group' );
		$( '[data-custom-unselection-group="' + current_unselection_group + '"] select' ).each( function() {
			if ( current_input_name !== $( this ).attr( 'name' ) && $( this ).val() === new_value ) {
				$( this ).val( 'none' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
				$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
			}
		} );

		// Unset other dropdowns of the current unselection group if any of them has non-zero value
		let current_nonzero_unselection_group = $( this ).closest( '[data-custom-nonzero-unselection-group]' ).data( 'custom-nonzero-unselection-group' );
		$( '[data-custom-nonzero-unselection-group="' + current_nonzero_unselection_group + '"] select' ).each( function() {
			if ( parseInt( new_value ) > 0 && current_input_name !== $( this ).attr( 'name' ) && parseInt( $( this ).val() ) > 0 ) {
				$( this ).val( '0' ).trigger( 'change', true ); // trigger 'change' to have the updated appearance of select element in browser
				$( this ).closest( '.eckb-conditional-setting-input' ).trigger( 'click' ); // trigger dependent fields
			}
		} );

		// When user adds a new row with a Module, use the width from the row above if any
		if ( current_unselection_group === 'ml-row' && prev_value === 'none' && new_value !== 'none' ) {

			// If row above has no module, then use prev ++, until either row above with module found or all rows above checked
			let current_row = $( this ).closest( '.epkb-admin__form-sub-tab-wrap' );
			let rows_above = current_row.prevAll( '.epkb-admin__form-sub-tab-wrap' );
			rows_above.each( function() {

				// Continue only if row above has module
				let source_row_module_selector = $( this ).find( '[data-custom-selection-group="ml-row"] select' );
				if ( source_row_module_selector.length && source_row_module_selector.val() !== 'none' ) {

					let source_row_module_selector_name = source_row_module_selector.attr( 'name' );
					let source_row_width_name = source_row_module_selector_name.replace( '_module', '_desktop_width' );
					let source_row_width_units_name = source_row_module_selector_name.replace( '_module', '_desktop_width_units' );

					let current_row_width_name = current_input_name.replace( '_module', '_desktop_width' );
					let current_row_width_units_name = current_input_name.replace( '_module', '_desktop_width_units' );

					// Set width value from row above
					let row_width_value = $( '[name="' + source_row_width_name + '"]' ).val();
					let row_width_units = $( '[name="' + source_row_width_units_name + '"]:checked' ).val();
					$( '[name="' + current_row_width_name + '"]' ).val( row_width_value ).trigger( 'change' );
					$( '[name="' + current_row_width_units_name + '"][value="' + row_width_units + '"]' ).trigger( 'click' );

					// Row above with module found, then stop loop
					return false;
				}
			} );
		}
	} );

	// Update Custom Dropdown when value of its select element was programmatically changed
	$( document ).on( 'change', '.epkb-input-custom-dropdown select', function( e ) {

		let new_value = $( this ).val();
		let input_container = $( this ).closest( '.input_container' );
		$( input_container ).find( '.epkb-input-custom-dropdown__option' ).removeClass( 'epkb-input-custom-dropdown__option--selected' );
		$( input_container ).find( '.epkb-input-custom-dropdown__option[data-value="' + new_value + '"]' ).addClass( 'epkb-input-custom-dropdown__option--selected' );

		// Update label text of the custom dropdown
		let value_label = $( input_container ).find( 'select option[value="' + new_value + '"]' ).html();
		$( input_container ).find( '.epkb-input-custom-dropdown__input span' ).html( value_label );

		// Update green marks
		update_custom_selection_green_mark();

		// Update icons
		$( this ).closest( '.epkb-input-custom-dropdown' ).find( '.epkb-input-custom-dropdown__option-icon' ).removeClass( 'epkb-input-custom-dropdown__option-icon--active' );
		$( this ).closest( '.epkb-input-custom-dropdown' ).find( '.epkb-input-custom-dropdown__option-icon[data-option-value="' + new_value + '"]' ).addClass( 'epkb-input-custom-dropdown__option-icon--active' );
	} );

	// Hide options list of the Custom Dropdown when clicked outside and the list is opened
	$( document ).on( 'click', function() {
		$( '.epkb-input-custom-dropdown__options-list' ).hide();
	} );

	// Initialize Custom Dropdowns
	$( '.epkb-input-custom-dropdown select' ).trigger( 'change' );

	// Learn More links
	$( document ).on( 'click', '.epkb-admin__form-tab-content-lm__toggler', function(e){

		e.stopPropagation();

		if ( $(this).closest('.epkb-admin__form-tab-content-learn-more').hasClass('epkb-admin__form-tab-content-learn-more--active') ) {
			$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');
		} else {

			$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');

			$(this).closest('.epkb-admin__form-tab-content-learn-more').addClass('epkb-admin__form-tab-content-learn-more--active');
		}
	});
	$('body').on('click', function(){
		$('.epkb-admin__form-tab-content-learn-more').removeClass('epkb-admin__form-tab-content-learn-more--active');
	});

	// Copy to clipboard button
	$( document ).on( 'click', '.epkb-copy-to-clipboard-box-container .epkb-ctc__copy-button', function( e ){
		e.preventDefault();
		let textarea = document.createElement( 'textarea' );
		let $container = $( this ).closest( '.epkb-copy-to-clipboard-box-container' );
		textarea.value = $container.find( '.epkb-ctc__embed-code' ).text();
		textarea.style.position = 'fixed';
		document.body.appendChild( textarea );
		textarea.focus();
		textarea.select();
		document.execCommand( 'copy' );
		textarea.remove();

		$container.find( '.epkb-ctc__embed-code' ).css( { 'opacity': 0 } );
		$container.find( '.epkb-ctc__embed-notification' ).fadeIn( 200 );

		setTimeout( function() {
			$container.find( '.epkb-ctc__embed-code' ).css( { 'opacity': 1 } );
			$container.find( '.epkb-ctc__embed-notification' ).hide();
		}, 1500 );
	});

	let isColorInputSync = false;
	$('.epkb-admin__color-field input').wpColorPicker({
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

	// Conditional setting input
	$( document ).on( 'click', '.eckb-conditional-setting-input', function() {

		// Find current input
		let current_input = $( this ).find( 'input' );
		if ( $( current_input[0] ).attr( 'type' ) === 'radio' ) {
			current_input = $( this ).find( 'input:checked' );
		}
		if ( ! current_input.length ) {
			current_input = $( this ).find( 'select' );
		}

		// OR LOGIC: Find content that is dependent to the current input
		let or_dependent_targets = $( '.eckb-condition-depend__' + current_input.attr( 'name' ) );

		// AND LOGIC: Find content that is dependent to the current input
		let and_dependent_targets = $( '.eckb-condition-depend-and__' + current_input.attr( 'name' ) );

		// Hide all dependent fields if the current input is not visible - only for AND logic, because OR logic can be satisfied with any of dependency
		if ( $( this ).css( 'display' ) === 'none' ) {
			$( and_dependent_targets ).hide();
			return;
		}

		// OR LOGIC: Show fields if condition matched
		or_dependent_targets.each( function() {

			// Find all dependencies
			let all_dependency_fields = $( this ).data( 'dependency-ids' );
			if ( typeof all_dependency_fields === 'undefined' ) {
				return;
			}
			all_dependency_fields = all_dependency_fields.split( ' ' );

			// Find all values for which show the dependent content
			let all_dependency_values = $( this ).data( 'enable-on-values' );
			if ( typeof all_dependency_values === 'undefined' ) {
				return;
			}
			all_dependency_values = all_dependency_values.split( ' ' );

			// First hide the dependent content, and then show it if any of its currently visible dependencies has corresponding value
			$( this ).hide();
			$( this ).closest( '.epkb-input-group-combined-units').find( '.epkb-input-desc' ).hide();

			for ( let i = 0; i < all_dependency_fields.length; i++ ) {

				// Find dependency field
				let dependency_field = $( '#' + all_dependency_fields[i] );
				if ( typeof dependency_field === 'undefined' || ! dependency_field.length ) {
					continue;
				}

				// Ignore currently hidden fields
				if ( $( dependency_field ).closest( '.epkb-admin__input-field' ).css( 'display' ) === 'none' ) {
					continue;
				}

				// Find dependency input
				let dependency_input = $( dependency_field ).is( 'select' ) ? ( dependency_field ) : dependency_field.find( 'input' );
				if ( typeof dependency_input === 'undefined' || ! dependency_input.length ) {
					continue;
				}

				// Find dependency input value
				let dependency_input_value = false;
				if ( dependency_input.attr( 'type' ) === 'radio' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
					if ( ! dependency_input.length ) {
						continue;
					}
				} else if ( dependency_input.attr( 'type' ) === 'checkbox' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
					dependency_input_value = dependency_input.length ? 'on' : 'off';
				}
				if ( dependency_input_value === false ) {
					dependency_input_value = dependency_input.val();
				}

				let current_field_id = $( this ).attr( 'id' );

				// Show dependent content if value of the dependency input in dependency values list
				if ( all_dependency_values.indexOf( dependency_input_value ) >= 0 ) {
					$( this ).show();
					$( this ).closest( '.epkb-input-group-combined-units').find( '.epkb-input-desc' ).show();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				trigger_conditional_field_click( current_field_id );
			}
		} );

		// AND LOGIC: Show fields if condition matched
		and_dependent_targets.each( function() {

			let current_dependent_target = this;

			// Find all dependencies
			let all_dependency_fields = $( current_dependent_target ).data( 'dependency-and' );
			if ( typeof all_dependency_fields === 'undefined' ) {
				return;
			}
			all_dependency_fields = all_dependency_fields.trim().split( ' ' );

			// First show the dependent content, and then hide it if any of its dependencies does not have corresponding value or is currently hidden
			$( current_dependent_target ).show();
			for ( let i = 0; i < all_dependency_fields.length; i++ ) {

				let current_field_id = $( this ).attr( 'id' );
				let dependency_id = all_dependency_fields[i].split( '--' )[0];
				let dependency_value = all_dependency_fields[i].split( '--' )[1];

				// Find dependency field - hide if is not found
				let dependency_field = $( '#' + dependency_id );
				if ( typeof dependency_field === 'undefined' || ! dependency_field.length ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Hide for currently hidden fields
				if ( $( dependency_field ).closest( '.epkb-admin__input-field' ).css( 'display' ) === 'none' ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Find dependency input
				let dependency_input = $( dependency_field ).is( 'select' ) ? ( dependency_field ) : dependency_field.find( 'input' );
				if ( typeof dependency_input === 'undefined' || ! dependency_input.length ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				// Find dependency input value
				let dependency_input_value = false;
				if ( dependency_input.attr( 'type' ) === 'radio' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
					if ( ! dependency_input.length ) {
						$( current_dependent_target ).hide();
						trigger_conditional_field_click( current_field_id );
						return;
					}
				} else if ( dependency_input.attr( 'type' ) === 'checkbox' ) {
					dependency_input = $( dependency_field ).find( 'input:checked' );
					dependency_input_value = dependency_input.length ? 'on' : 'off';
				}
				if ( dependency_input_value === false ) {
					dependency_input_value = dependency_input.val();
				}

				// Hide dependent content if value of the dependency input does not match dependency value
				if ( dependency_input_value !== dependency_value ) {
					$( current_dependent_target ).hide();
					trigger_conditional_field_click( current_field_id );
					return;
				}

				trigger_conditional_field_click( current_field_id );
			}
		} );

		// Hide settings box if there are no visible settings left or show it if there is any visible setting
		$( '.epkb-admin__form-sub-tab-wrap--active .epkb-admin__form-tab-content, .epkb-fe__feature-settings--active .epkb-fe__settings-section' ).show().each( function () {

			// Skip settings boxes which does not contain any setting
			if ( ! $( this ).find( '.epkb-admin__kb__form > div' ).length ) {
				return;
			}

			let has_visible_settings = false;

			$( this ).find( '.epkb-admin__kb__form > div' ).each( function () {
				if ( ! $( this ).is( ':hidden' ) ) {
					has_visible_settings = true;
					return false;
				}
			} );

			if ( ! has_visible_settings ) {
				$( this ).hide();
			}
		} );
	} );

	// Initialize conditional fields
	$( '.eckb-conditional-setting-input' ).trigger( 'click' );

	// Trigger click event of the current dependent field to check its own dependent fields
	function trigger_conditional_field_click( field_id ) {

		let current_field = $( '#' + field_id );
		if ( ! current_field.length ) {
			return;
		}

		if ( current_field.hasClass( 'eckb-conditional-setting-input' ) ) {
			setTimeout( function() {
				current_field.trigger( 'click' );
			}, 1 );
		}
	}
});