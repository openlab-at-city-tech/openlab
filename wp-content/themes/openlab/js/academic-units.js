/**
 * JS functionality for Academic Unit frontend selector.
 */
var openlab = window.openlab || {}

openlab.academicUnits = (function($){
    return {
        init: function() {
            this.academicUnits = $('.academic-unit');
            this.academicUnitCheckboxes = $('.academic-unit-checkbox');
            this.validateAcademicTypeSelector();
            this.academicUnitCheckboxes.on( 'change', this.validateAcademicTypeSelector );

            var au = this
            $('.cboxol-academic-unit-selector').closest('form').on('submit', function(e) {
                return au.validateRequiredTypes();
            });
        },

        /**
         * Hide/show units based on whether the parent is selected.
         */
        validateAcademicTypeSelector: function() {
            var $selectedUnits = $('.academic-unit-checkbox:checked');
            var selectedUnitSlugs = [];
            var au = window.openlab.academicUnits;

            $selectedUnits.each( function( k, v	) {
                selectedUnitSlugs.push( v.value );
            } );

            au.academicUnits.removeClass( 'academic-unit-visible' ).addClass( 'academic-unit-hidden' );
            au.academicUnitCheckboxes.each( function( k, v ) {
                // Items without parents or with unchecked parents should be shown.
                var hasParent = v.dataset.hasOwnProperty( 'parent' ) && v.dataset.parent.length > 0;
                if ( ! hasParent || -1 !== selectedUnitSlugs.indexOf( v.dataset.parent ) ) {
                    $( v ).closest( '.academic-unit' ).removeClass( 'academic-unit-hidden' ).addClass( 'academic-unit-visible' );
                } else {
                    // Hidden fields can't be checked.
                    $( v ).prop( 'checked', false );
                }
            } );

						// Items appearing under more than one parent should have dupes removed.
						var uniqueSlugs = [];
						au.academicUnitCheckboxes.map( function(item, index){
							var $parentEl = $(index).closest('.academic-unit');
							if ( ! $parentEl.hasClass( 'academic-unit-visible' ) ) {
								return;
							}

							var theSlug = index.value;
							if ( -1 === uniqueSlugs.indexOf( theSlug ) ) {
								uniqueSlugs.push( theSlug );
							} else {
								$parentEl.addClass( 'academic-unit-hidden' );
							}
						});

        },

        /**
         * Validate form to ensure that required types are present.
         */
        validateRequiredTypes: function() {
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
    }
}(jQuery))

jQuery(document).ready(function(){
    openlab.academicUnits.init();
});
