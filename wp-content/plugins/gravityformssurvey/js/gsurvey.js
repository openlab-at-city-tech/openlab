function gsurveySetUpLikertFields(){
    if (jQuery('table.gsurvey-likert').length > 0) {
        jQuery( 'table.gsurvey-likert' ).find( 'td.gsurvey-likert-choice, input[type="radio"]' ).click(function(e) {

            var elem = jQuery( this ),
                input = elem.is( 'td.gsurvey-likert-choice' ) ? elem.find( 'input' ) : elem;

            if( input.is( ':disabled' ) )
                return false;

            input.prop( 'checked', true );
            input.closest( 'tr' ).find( '.gsurvey-likert-selected' ).removeClass( 'gsurvey-likert-selected' );
            input.parent().addClass( 'gsurvey-likert-selected' );
            input.focus().change();

        });

        // add a hover state
        jQuery('table.gsurvey-likert td').hover(function(e){
            if (jQuery(e.target).is('td.gsurvey-likert-choice-label') || jQuery(this).find('input').is(':disabled')) {
                return false;
            } else {
                jQuery(this).addClass('gsurvey-likert-hover');
            }

        }, function(e){
            if (jQuery(e.target).is('td.gsurvey-likert-choice-label') || jQuery(this).find('input').is(':disabled')){
                return false;
            } else {
                jQuery(this).removeClass('gsurvey-likert-hover');
            }

        });

        jQuery( 'table.gsurvey-likert input[type="radio"]' ).focus( function() {
            jQuery( this ).parent().addClass( 'gsurvey-likert-focus' );
        } ).blur( function() {
            jQuery( this ).parent().removeClass( 'gsurvey-likert-focus' );
        } );

    }
}

 /*--------- Rank  ---------*/
function gsurveyRankUpdateRank(ulElement){
    var IDs = [];
    jQuery(ulElement).find('li').each(function(){ IDs.push(this.id); });
    gsurveyRankings[ulElement.id] = IDs;
    jQuery(ulElement).parent().find('#' + ulElement.id + '-hidden').val(gsurveyRankings[ulElement.id]);

    // Trigger conditional logic when the field is updated.
    if( ! window.gf_form_conditional_logic ) {
        return;
    }

    var splitString = ulElement.id.split('-');
    var formId = parseInt(splitString[splitString.length - 2]); // Second to last element
    var fieldId = parseInt(splitString[splitString.length - 1]); // Last element

    var dependentFieldIds = rgars( gf_form_conditional_logic, [ formId, 'fields', fieldId ].join( '/' ) );
    if( dependentFieldIds ) {
        gf_apply_rules( formId, dependentFieldIds );
    }
}

function gsurveyRankMoveChoice(ulNode, fromIndex, toIndex){
    var ulNodeId = jQuery(ulNode).attr('id');
    var value = gsurveyRankings[ulNodeId][fromIndex];

    //deleting from old position
    gsurveyRankings[ulNodeId].splice(fromIndex, 1);

    //inserting into new position
    gsurveyRankings[ulNodeId].splice(toIndex, 0, value);
    gsurveyRankUpdateRank(ulNode);
}

function gsurveySetUpRankSortable(){
    var $rankField= jQuery('.gsurvey-rank');
    if ($rankField.length > 0) {
        $rankField.sortable({
            axis: 'y',

            cursor: 'move',
            update: function(event, ui){
                var fromIndex = ui.item.data('index');
                var toIndex = ui.item.index();
                gsurveyRankMoveChoice(this, fromIndex, toIndex);
            }
        });

        gsurveyRankings = {};

        jQuery('.gsurvey-rank').each(function(){
            gsurveyRankUpdateRank(this);

        });
    }
}

function init_fields() {
    gsurveySetUpRankSortable();
    gsurveySetUpLikertFields();
}

// In frontend we need to initialize the fields every time after gform_post_render for ajax forms.
jQuery(document).on( 'gform_post_render', function() {
    init_fields();
} );

// In backend edit entry page, init fields once document is ready.
jQuery( function( $ ) {
	if ( $( '#gform_update_button' ).length ) {
		init_fields();
	}
} );

// Get the correct field ID for evaluating conditional logic on multi-row likert fields.
gform.addFilter( 'gform_field_meta_raw_input_change', function( fieldMeta, $input, event ) {

    if ( ! $input[0].attributes.value ) {
        return fieldMeta;
    }

    // Only run this filter for likert fields.
    const likertRegex = /^glikert/;
    if ( ! likertRegex.test( $input[0].attributes.value.value ) ) {
        return fieldMeta;
    }

    const likertId = extractFieldId( $input[0].attributes.id.value );

    if ( likertId ) {
        fieldMeta.fieldId = likertId;
    }

    return fieldMeta;
} );

// Given the choice ID, extract the field ID.
function extractFieldId( inputString ) {
    const regex = /^choice_(\d+)_(\d+)_(\d+)_(\d+)$/;

    if (regex.test(inputString)) {
        const match = inputString.match(regex);

        const secondNumber = match[2];
        return secondNumber;
    } else {
        return null;
    }
}

// Filter to make conditional logic work on multi-row likert fields and rank fields.
gform.addFilter( 'gform_is_value_match', function ( isMatch, formId, rule ) {
    var $ = jQuery,
        inputId         = rule['fieldId'],
        fieldId         = gformExtractFieldId( inputId ),
        inputIndex      = gformExtractInputIndex( inputId );

    // if gformFormat function is not available, bail
    if ( typeof String.prototype.gformFormat !== 'function' ) {
        return isMatch;
    }

    $inputs = $( 'input[id^="choice_{0}_{1}_{2}"]'.gformFormat( formId, fieldId, inputIndex ) );
    if ( $inputs.length ) {
        // If the rule got saved without a value, it's because we want the default value.
        if ( '' == rule.value ) {
            rule.value = $inputs[0].defaultValue;
        }

        return gf_is_match_checkable( $inputs, rule, formId, fieldId );
    }

    $inputs = $( 'ul[id^="gsurvey-rank-{0}-{1}"]'.gformFormat( formId, fieldId, inputIndex ) );
    if ( $inputs.length ) {
       return gf_is_match_rank( $inputs, rule, formId, fieldId );
    }

    return isMatch;
});

// Evaluate conditional logic on a rank field
function gf_is_match_rank( $input, rule, formId, fieldId ) {
    var isMatch = false;
    var $ = jQuery;

    var [field, input] = rule.fieldId.split('.');
    if ( !input ) { // The rule doesn't get an input ID if the user doesn't change the rule from the default.
        input = 0;
    }
    const className = `choice_${formId}_${field}_${input}`;
    const listItem = $input[0].querySelector(`li.${className}`);

    if ( !listItem ) {
        return false;
    }

    var itemIndex = Array.from( listItem.parentElement.children ).indexOf( listItem ) + 1;
    var value = parseInt( rule.value );

    // If the rule does not have a value, it's because we want to test that the item is in the first position
    if ( ! rule.value ) {
        value = 1;
    }

    switch ( rule.operator ) {
        case 'is':
            return itemIndex === value;
        case 'isnot':
            return itemIndex !== value;
        case '>':
            return itemIndex > value;
        case '<':
            return itemIndex < value;
        default:
            console.error( 'Invalid operator' );
            return false;
    }

    return isMatch;
}
