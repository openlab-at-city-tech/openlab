/**
 * JS functionality for Academic Unit frontend selector.
 */
(function($){

	var $accountTypeSelector,
		$academicUnitCheckboxes,
		$academicUnits;

	$(document).ready(function() {
		$academicUnits = $('.academic-unit');
		$academicUnitCheckboxes = $('.academic-unit-checkbox');
		validateAcademicTypeSelector();
		$academicUnitCheckboxes.change( validateAcademicTypeSelector );

		$('.cboxol-academic-unit-selector').closest('form').on('submit', function(e) {
			return validateRequiredTypes();
		});
	});

	/**
	 * Hide/show units based on whether the parent is selected.
	 */
	function validateAcademicTypeSelector() {
		var $selectedUnits = $('.academic-unit-checkbox:checked');
		var selectedUnitSlugs = [];
		$selectedUnits.each( function( k, v	) {
			selectedUnitSlugs.push( v.value );
		} );
        console.log(selectedUnitSlugs);

		$academicUnits.removeClass( 'academic-unit-visible' ).addClass( 'academic-unit-hidden' );
		$academicUnitCheckboxes.each( function( k, v ) {
			// Items without parents or with unchecked parents should be shown.
			var hasParent = v.dataset.hasOwnProperty( 'parent' ) && v.dataset.parent.length > 0;
            console.log(v.dataset.parent);
			if ( ! hasParent || -1 !== selectedUnitSlugs.indexOf( v.dataset.parent ) ) {
				$( v ).closest( '.academic-unit' ).removeClass( 'academic-unit-hidden' ).addClass( 'academic-unit-visible' );
			} else {
				// Hidden fields can't be checked.
				$( v ).prop( 'checked', false );
			}
		} );
	}

	/**
	 * Validate form to ensure that required types are present.
	 */
	function validateRequiredTypes() {
		var entityType = CBOXOLAcademicTypes.entityType;
		var typeOfType, entityTypeUnitTypes;
		var validated = true;

		if ( 'group' === entityType ) {
			typeOfType = CBOXOLAcademicTypes.groupType;
			entityTypeUnitTypes = CBOXOLAcademicTypes.typesByGroupType[ typeOfType ];
		} else {
			typeOfType = $accountTypeSelector.val();
			entityTypeUnitTypes = CBOXOLAcademicTypes.typesByMemberType[ typeOfType ];
		}

		for ( var i in entityTypeUnitTypes ) {
			if ( 'required' !== entityTypeUnitTypes[ i ].status ) {
				continue;
			}

			if ( 0 === $('.cboxol-academic-unit-selector-for-type-' + entityTypeUnitTypes[ i ].slug).find(':checked').length ) {
				validated = false;
				break;
			}
		}

		if ( validated ) {
			$('.academic-unit-notice').remove();
		} else {
			$('.cboxol-academic-unit-selector').closest('.panel-body').prepend('<div id="message" class="bp-template-notice error academic-unit-notice"><p>' + CBOXOLAcademicTypes.requiredError + '</p></div>');

			var aurOffset = $( '#panel-academic-units' ).offset();
			if ( aurOffset ) {
				window.scrollTo( 0, aurOffset.top - 50 );
			}
		}

		return validated;
	}
}(jQuery))
