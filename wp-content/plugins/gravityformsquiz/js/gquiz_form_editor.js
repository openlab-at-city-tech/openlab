/* global jQuery, gquiz_strings, gform */

//------------------ Choices -----------------

function QuizChoice(text, value, isCorrect, weight) {
    this.text = text;
    this.value = value ? value : text;
    this.isSelected = false;
    this.price = '';
    this.gquizIsCorrect = isCorrect;
    this.gquizWeight = weight;

}

function StartChangeQuizType(type) {

    var field = GetSelectedField();
    field['gquizFieldType'] = type;

    //reset answers
    jQuery.each(field.choices, function (index) {
        field.choices[index].gquizIsCorrect = false;
        field.choices[index].gquizWeight = 0;
    });

    return StartChangeInputType(type, field);
}

function GenerateQuizChoiceValue(field) {
    return 'gquiz' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
            return v.toString(16);
        });
}

function gquiz_toggle_correct_choice( button, choiceIndex ) {
	var field = GetSelectedField();
	if ( field.inputType == 'radio' || field.inputType == 'select' ) {
		for ( var i = 0; i < field.choices.length; i++ ) {
			if ( field.choices[ i ].gquizIsCorrect ) {
				field.choices[ i ].gquizIsCorrect = false;
			}
		}

		jQuery( 'button.gquiz-button-correct-choice' ).each( function( index ) {
			jQuery( this ).removeClass( 'chosen-as-correct' );
		} );
		jQuery( button ).addClass( 'chosen-as-correct' );
		field.choices[ choiceIndex ].gquizIsCorrect = true;
	} else if ( field.inputType == 'checkbox' ) {
		var is_checked = jQuery( button ).hasClass( 'chosen-as-correct' );
		if ( is_checked ) {
			jQuery( button ).removeClass( 'chosen-as-correct' );
			jQuery( button ).attr( 'title', gquiz_strings.defineAsCorrect ).attr( 'alt', 'Not correct' );
		} else {
			jQuery( button ).attr( 'title', gquiz_strings.defineAsIncorrect ).attr( 'alt', 'Correct' );
			jQuery( button ).addClass( 'chosen-as-correct' );
		}

		var isCorrect = field.choices[ choiceIndex ].gquizIsCorrect;
		field.choices[ choiceIndex ].gquizIsCorrect = ! isCorrect;
	}

	gquiz_maybe_display_choices_help( field );
	RefreshSelectedFieldPreview();
}

function gquizToggleValues() {
    jQuery('#gquiz_gfield_settings_choices_container').toggleClass('gquiz-choice-values-visible');
}

function gquiz_maybe_display_choices_help(field) {

    var display_help = true;
    for (var i = 0; i < field.choices.length; i++) {
        if (field.choices[i].gquizIsCorrect) {
            display_help = false;
        }
    }
    if (display_help) {
        jQuery('.gquiz-choices-help').show();
    } else {
        jQuery('.gquiz-choices-help').hide();
    }

}

jQuery(document).bind('gform_load_field_choices', function (event, field) {

    if (field.type == 'quiz') {
        jQuery('#gquiz-field-choices').html(gquizGetChoices(field));
        gquiz_maybe_display_choices_help(field);
    }

});

function gquizGetChoices(field) {

    var imagesUrl = gquizVars.imagesUrl;
    var buttonClass;
    var str = '';
    var weight;
    for (var i = 0; i < field.choices.length; i++) {
        buttonClass = 'gquiz-button-correct-choice';

        if (field.choices[i].gquizIsCorrect === true) {
        	buttonClass += ' chosen-as-correct';
		}

        str += "<li class='gquiz-choice-row' data-index='" + i + "'>";
        str += '<i class="gquiz-choice-handle"></i>';

		str += "<button class='" + buttonClass + "' title='" + gquiz_strings.toggleCorrectIncorrect + "' onclick=\"gquiz_toggle_correct_choice(this, '" + i + "');\"></button>";

		str += "<input type='text' id='gquiz-choice-text-" + i + "' value=\"" + field.choices[i].text.replace(/"/g, "&quot;") + "\"  class='field-choice-input field-choice-text' />";
        str += "<input type='text' id='gquiz-choice-value-" + i + "' value=\"" + field.choices[i].value + "\" class='field-choice-input field-choice-value' >";

        if (typeof field.choices[i].gquizWeight == 'undefined') {
            field.choices[i].gquizWeight = field.choices[i].gquizIsCorrect == true ? 1 : 0;
        }
        weight = field.choices[i].gquizWeight;

        str += "<input id='gquiz-choice-weight-" + i + "' type='text' class='gquiz-choice-weight' onkeyup='gquizSetFieldChoice(" + i + ");' value='" + weight + "' /> ";

        str += "<button class='gf_insert_field_choice gquiz-insert-choice' aria-label='Add Answer'></button>";

        if (field.choices.length > 1) {
            str += "<button class='gf_delete_field_choice gquiz-delete-choice' aria-label='Remove Answer'></button>";
        }

        str += '</li>';

    }

    str += '<div class="gquiz-choices-help" style="display:none">' + gquiz_strings.markAnAnswerAsCorrect + '</div>'

    return str;
}

function gquizSetFieldChoice(index) {

    var text = jQuery('#gquiz-choice-text-' + index).val();
    var value = jQuery('#gquiz-choice-value-' + index).val();
    var weight = jQuery('#gquiz-choice-weight-' + index).val();
    var field = GetSelectedField();

    field.choices[index].text = text;
    field.choices[index].value = field.enableChoiceValue ? value : text;
    field.choices[index].gquizWeight = weight;

    //set field selections
    jQuery('#field_choices :radio, #field_choices :checkbox').each(function (index) {
        field.choices[index].isSelected = this.checked;
    });

    LoadBulkChoices(field);

    UpdateFieldChoices(GetInputType(field));
}

function gquizInsertChoice(index) {
    var field = GetSelectedField();

    var new_choice = new QuizChoice('', GenerateQuizChoiceValue(field), false, 0);

    field.choices.splice(index, 0, new_choice);
    gquizLoadChoices();
    UpdateFieldChoices(GetInputType(field));
}

function gquizDeleteChoice(index) {

    var field = GetSelectedField();
    var value = jQuery('#gquiz-choice-value-' + index).val();

    if (HasConditionalLogicDependency(field.id, value)) {
        if (!confirm(gf_vars.conditionalLogicDependencyChoice)) {
            return;
        }
    }

    field.choices.splice(index, 1);
    gquizLoadChoices();
    UpdateFieldChoices(GetInputType(field));
}

function gquizLoadChoices() {
    jQuery('#gquiz-field-choices').html(gquizGetChoices(field));
    gquiz_maybe_display_choices_help(field);
}

/**
 * Toggle weighted score option.
 *
 * @since 3.7
 *
 * @param el Checkbox element.
 */
function gquizToggleWeightedScore( el ) {
	SetFieldProperty( 'gquizWeightedScoreEnabled', el.checked );
	jQuery( '#gquiz_gfield_settings_choices_container' ).toggleClass( 'gquiz-weighted-score' );
}

/**
 * Field Type change event.
 *
 * @since 3.7
 *
 * @param el Select element.
 */
function gquizFieldTypeChange( el ) {
	if ( jQuery( el ).val() == '' ) {
		return;
	}
	jQuery( '#field_settings' ).slideUp(
		function() {
			StartChangeQuizType( jQuery( '#gquiz-field-type' ).val() );
		}
	);
}

/**
 * Open Bulk Add Thickbox window.
 *
 * @since 3.7
 *
 * @param window_title Window title.
 */
function gquizOpenBulkAdd( window_title ) {
	tb_show( window_title, '#TB_inline?height=500&amp;width=600&amp;inlineId=gfield_bulk_add', '' );
}

//------------------ Field settings init -----------------
jQuery( document ).on(
	'gform_load_field_settings',
	function ( event, field, form ) {

		if ( field.type != 'quiz' ) {
			return;
		}

		if ( typeof field.gquizEnableRandomizeQuizChoices == 'undefined' ) {
			field.gquizEnableRandomizeQuizChoices = false;
		}
		jQuery( '#gquiz-randomize-quiz-choices' ).prop( 'checked', field.gquizEnableRandomizeQuizChoices );
		jQuery( '#gquiz-field-type' ).val( field.gquizFieldType );
		jQuery( '#gquiz-question' ).val( field.label );

		jQuery( '#gquiz-answer-explanation' ).val( field.gquizAnswerExplanation );
		if ( field.gquizShowAnswerExplanation == undefined ) {
			field.gquizShowAnswerExplanation = false;
		}
		var isShowExplanation = field.gquizShowAnswerExplanation;
		if ( typeof field.gquizWeightedScoreEnabled == 'undefined' ) {
			field.gquizWeightedScoreEnabled = false;
		}
		jQuery( '#gquiz-weighted-score-enabled' ).prop( 'checked', field.gquizWeightedScoreEnabled );
		if ( field.gquizWeightedScoreEnabled) {
			jQuery( '#gquiz_gfield_settings_choices_container' ).addClass( 'gquiz-weighted-score' );
		} else {
			jQuery( '#gquiz_gfield_settings_choices_container' ).removeClass( 'gquiz-weighted-score' );
		}

		jQuery( '#gquiz-show-answer-explanation' ).prop( 'checked', isShowExplanation );
		gquiz_toggle_answer_explanation( isShowExplanation );

		jQuery( 'li.label_setting' ).hide();

		jQuery( '#gquiz-field-type' ).prop( 'disabled', has_entry( field.id ) );
	}
);

function gform_new_choice_quiz(field, choice) {
    if (field.type == 'quiz') {
        choice['value'] = GenerateQuizChoiceValue(field);
        choice['gquizIsCorrect'] = false;
        choice['gquizWeight'] = 0;
    }

    return choice;
}


function gquiz_toggle_answer_explanation(isShowExplanation) {

    if (isShowExplanation) {
        jQuery('.gquiz-setting-answer-explanation').show();
    }
    else {
        jQuery('.gquiz-setting-answer-explanation').hide();
    }
}

function gquizMoveFieldChoice(fromIndex, toIndex) {
    var field = GetSelectedField();
    var choice = field.choices[fromIndex];

    //deleting from old position
    field.choices.splice(fromIndex, 1);

    //inserting into new position
    field.choices.splice(toIndex, 0, choice);
    gquizLoadChoices();
    UpdateFieldChoices(GetInputType(field));
}

function SetDefaultValues_quiz(field) {

    field.gquizFieldType = 'radio';
    field.label = 'Untitled Quiz Field';
    field.inputType = 'radio';
    field.inputs = null;
    field.enableChoiceValue = true;
    field.enablePrice = false;
    field.gquizEnableRandomizeQuizChoices = false;
    field.gquizShowAnswerExplanation = false;
    field.gquizAnswerExplanation = '';
    field.gquizWeightedScoreEnabled = false;
    if (!field.choices) {
        field.choices = new Array(new QuizChoice(gquiz_strings.firstChoice, GenerateQuizChoiceValue(field), false, 0), new QuizChoice(gquiz_strings.secondChoice, GenerateQuizChoiceValue(field), false, 0), new QuizChoice(gquiz_strings.thirdChoice, GenerateQuizChoiceValue(field), false, 0));
    }

    return field;
}

// Remove specific field settings from output in settings panel for Quiz's various input types.
gform.addFilter( 'gform_editor_field_settings', function( settings, field ) {
	if ( field.type !== 'quiz' ) {
		return settings;
	}

	return settings.filter( function( setting ) {
		return [ '.choices_setting', '.duplicate_setting' ].indexOf( setting ) === -1;
	} );
} );

jQuery(document).ready(function () {

    jQuery('#gquiz-field-choices').sortable({
        axis: 'y',
        handle: '.gquiz-choice-handle',
        update: function (event, ui) {
            var fromIndex = ui.item.data("index");
            var toIndex = ui.item.index();
            gquizMoveFieldChoice(fromIndex, toIndex);
        }
    });

    jQuery('.gquiz-setting-choices')
        .on('input propertychange', '.field-choice-input', function (e) {
            var $this = jQuery(this);
            var li = $this.closest('li.gquiz-choice-row');
            var i = li.data('index');
            gquizSetFieldChoice(i);
            if ($this.hasClass('field-choice-text') || $this.hasClass('field-choice-value')) {
                CheckChoiceConditionalLogicDependency(this);
                e.stopPropagation();
            }
        })
        .on('click', '.gquiz-insert-choice', function () {
            var i = jQuery(this).closest('li.gquiz-choice-row').data('index');
            gquizInsertChoice(i + 1);
        })
        .on('click', '.gquiz-delete-choice', function () {
            var i = jQuery(this).closest('li.gquiz-choice-row').data('index');
            gquizDeleteChoice(i);
        });

});
