if (typeof gperk == 'undefined') {
	var gperk = {};
}

gperk.confirmActionUrl = function(event, message, url) {
	event.preventDefault();

	var elem = jQuery( event.target );

	if ( ! url) {
		url = elem.prop( 'href' );
	}

	if (confirm( message )) {
		location.href = url;
	}

}

/**
* Add a tab to the form editor
*
*/
gperk.addTab = function(elem, id, label) {

	var tabClass = id == '#gws_form_tab' ? 'gwp_form_tab' : 'gwp_field_tab';

	var tabClass    = id.replace( '#', '' ),
		altTabClass = tabClass.replace( 'gws', 'gwp' ),
		tabClass    = tabClass != altTabClass ? tabClass + ' ' + altTabClass : tabClass;

	// destory tabs already initialized
	elem.tabs( 'destroy' );

	// add new tab
	elem.find( 'ul' ).eq( 0 ).append( '<li style="width:100px; padding:0px;" class="' + tabClass + '"> \
        <a href="' + id + '">' + label + '</a> \
        </li>' )

	// add new tab content
	elem.append( jQuery( id ) );

	// re-init tabs
	elem.tabs({
		beforeActivate: function(event, ui) {
			switch ( jQuery( ui.newPanel ).prop( 'id' ) ) {
				case 'gws_form_tab':
					jQuery( document ).trigger( 'gwsFormTabSelected', [ form ] );
				break;
				case 'gws_field_tab':
					jQuery( document ).trigger( 'gwsFieldTabSelected', [ field ] );
				break;
			};
		}
	});

}

gperk.togglePerksTab = function() {

	var fieldTab = jQuery( '.ui-tabs-nav li.gwp_field_tab' );

	fieldTab.hide();

	if ( gperk.fieldHasSettings() ) {
		fieldTab.show();
	}

};

gperk.fieldHasSettings = function() {

	var hasSetting = false;

	jQuery( '#gws_field_tab' ).find( 'li.field_setting' ).each(function(){
		var patt = /(display[: ]+none)/;
		if ( ! patt.test( jQuery( this ).attr( 'style' ) )) {
			hasSetting = true;
		}
	});

	return hasSetting;
}

gperk.toggleSection = function(elem, selector) {
	var elem = jQuery( elem );

	if (elem.prop( 'checked' )) {
		elem.parents( '.gwp-field' ).addClass( 'open' );
		jQuery( selector ).gwpSlide( 'down', '.perk-settings' );
	} else {
		elem.parents( '.gwp-field' ).removeClass( 'open' );
		jQuery( selector ).gwpSlide( 'up', '.perk-settings' );
	}

}

gperk.isSingleProduct = function(field) {
	singleFieldTypes = gperk.applyFilter( 'gwSingleFieldTypes', ['singleproduct', 'hiddenproduct', 'calculation'] );
	return jQuery.inArray( field.inputType, singleFieldTypes ) != -1;
}

gperk.getFieldLabel = function(field, inputId) {

	if (gperk.isUndefined( inputId )) {
		inputId = false;
	}

	var label = field.label;
	var input = gperk.getInput( field, inputId );

	if (field.type == 'checkbox' && input != false) {
		return input.label;
	} else if (input != false) {
		return input.label;
	} else {
		return label;
	}

}

gperk.getInput = function(field, inputId) {

	if (gperk.isUndefined( field['inputs'] ) && jQuery.isArray( field['inputs'] )) {
		for (i in field['inputs']) {
			var input = field['inputs'][i];
			if (input.id == inputId) {
				return input;
			}
		}
	}

	return false;
}

gperk.toggleSettings = function(id, toggleSettingsId, isChecked, animate ) {

	var elem         = jQuery( '#' + id );
	var settingsElem = jQuery( '#' + toggleSettingsId );
	var animate      = typeof animate !== 'undefined' ? animate : true;

	// if "isChecked" is passed, check the checkbox
	if ( ! gperk.is( isChecked, 'undefined' )) {
		elem.prop( 'checked', isChecked );
	} else {
		isChecked = elem.is( ':checked' );
	}

	if ( isChecked ) {
		if ( animate ) {
			settingsElem.gfSlide( 'down' );
		} else {
			settingsElem.show();
		}
	} else {
		if ( animate ) {
			settingsElem.gfSlide( 'up' );
		} else {
			settingsElem.hide();
		}
	}

	SetFieldProperty( id, isChecked );

}

gperk.setInputProperty = function(inputId, property, value) {

	var field = GetSelectedField();

	for (i in field.inputs) {
		if (field.inputs[i].id == inputId) {
			field.inputs[i][property] = value;
		}
	}

}

/**
* Set a form property on current form.
*
* This function should only be used on the Gravity Forms form editor page where the "form" object is a global
* variable and available for modification. Changes made to the form object on this page will be saved
* when the user clicks "Update Form".
*
* @type Object
*/
gperk.setFormProperty = function(property, value) {
	form[property] = value;
}

/**
* GWPerks version of the gfSlide jQuery plugin, used to show/hide/slideup/slidedown depending on whether
* the settings are being init or already displayed
*
* @param isVisibleSelector Defaults to '#field_settings'; pass "false" to force "hide()"
*
*/

jQuery.fn.gwpSlide = function(direction, isVisibleSelector) {

	if (typeof isVisibleSelector == undefined) {
		isVisibleSelector = '#field_settings';
	}

	var isVisible = isVisibleSelector === false || isVisibleSelector === true ? isVisibleSelector : jQuery( isVisibleSelector ).is( ':visible' );

	if (direction == 'up') {
		if ( ! isVisible) {
			this.hide();
		} else {
			this.slideUp();
		}
	} else {
		if ( ! isVisible) {
			this.show();
		} else {
			this.slideDown();
		}
	}

	return this;
};

jQuery( document ).ready( function ( $ ) {

	// Setup initialization action when Gravity Perks field settings are opened in GF 2.5.
	$( '.panel-block-tabs' ).on( 'accordionactivate', function( event, ui ) {
		if ( ui.newPanel.is( '#gravity-perks_tab' ) ) {
			gform.doAction( 'gperks_field_settings_tab_loaded', event, GetSelectedField(), window.form, ui );
		}
	} );

	/**
	 * Hide custom field settings tab if no settings are displayed for the selected field type
	 */
	jQuery( document ).bind( 'gform_load_field_settings', function( event, field ) {
		gperk.togglePerksTab()
	});

	/**
	 * Add additional capabilities to Gravity Forms tooltips including a "gp-tooltip-right" class as well as being
	 * able to pass data-gp-tooltip-options as an attribute to modify the jQuery UI Tooltip options for the current
	 * tooltip.
	 */
	var perkifyTooltip = function(el) {
		var options = $( el ).data( 'gp-tooltip-options' );

		if ( ! options && $( el ).hasClass( 'gp-tooltip-right' ) ) {
			options = {
				classes: {
					'ui-tooltip': 'gp-tooltip-right'
				},
				position: {
					my: 'right bottom',
					at: 'right+10 top-11',
					collision: 'none'
				},
				// Fixes positional issues introduced by GF in [PR#1183](https://github.com/gravityforms/gravityforms/pull/1883/files).
				open: null
			};
		}

		if ( options ) {
			$( el ).tooltip( 'option', options );
		}
	};

	$( document ).on('gperks_tooltips_initialized', function() {
		$( '.gp-tooltip' ).each( function() {
			perkifyTooltip( this );
		} );
	});

	setTimeout( function() {
		$( document ).trigger( 'gperks_tooltips_initialized' );
	} );

} );
