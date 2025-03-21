/* global gsurveyLikertStrings, gsurveyVars */

var isLegacy = gsurveyLikertStrings.isLegacy === 'true';

/*----  Survey ----*/
function SetDefaultValues_survey(field) {
    field.label = gsurveyVars.strings.untitledSurveyField;
    SetDefaultValues_likert(field);
}

function gform_new_choice_survey(field, choice) {

    if (window['gform_new_choice_' + field.inputType ]) {
        choice = window['gform_new_choice_' + field.inputType ](field, choice);
    } else {
        choice = new Choice('', GenerateSurveyDefaultChoiceValue(field));
    }

    return choice;
}

function GenerateSurveyDefaultChoiceValue(field) {
    return 'gsurvey' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });
}

jQuery(document).bind('gform_load_field_settings', function (event, field, form) {
    if (field.type == 'survey') {
        jQuery('#gsurvey-field-type').val(field.inputType);
        jQuery('#gsurvey-question').val(field['label']);

        if (has_entry(field.id)) {
            jQuery('#gsurvey-field-type').attr('disabled', true);
        } else {
            jQuery('#gsurvey-field-type').removeAttr('disabled');
        }
    }
});

function StartChangeSurveyType(type) {

    var field = GetSelectedField();
    field.choices = null;

    if (window['SetDefaultValues_' + type ])
        window['SetDefaultValues_' + type ](field);
    jQuery('#gsurvey-field-type').val(type);
    if (type == 'checkbox' || type == 'radio' || type == 'select') {
        field.choices = new Array(
            new Choice(gsurveyRankStrings.firstChoice, GenerateSurveyDefaultChoiceValue(field), false),
            new Choice(gsurveyRankStrings.secondChoice, GenerateSurveyDefaultChoiceValue(field), false),
            new Choice(gsurveyRankStrings.thirdChoice, GenerateSurveyDefaultChoiceValue(field), false)
        );
    }

    if ( type !== 'text' ) {
        field.inputMask = false;
    }

    return StartChangeInputType(type, field);
}

/*----  Likert ----*/
function SetDefaultValues_likert(field) {

    field.enableChoiceValue = true;
    field.enablePrice = false;
    field.gsurveyLikertEnableMultipleRows = false;
    field.gsurveyLikertEnableScoring = false;

    field.choices = new Array(
        new gsurveyLikertChoice(gsurveyLikertStrings.columnLabel1, GenerateLikertChoiceValue(field), 1),
        new gsurveyLikertChoice(gsurveyLikertStrings.columnLabel2, GenerateLikertChoiceValue(field), 2),
        new gsurveyLikertChoice(gsurveyLikertStrings.columnLabel3, GenerateLikertChoiceValue(field), 3),
        new gsurveyLikertChoice(gsurveyLikertStrings.columnLabel4, GenerateLikertChoiceValue(field), 4),
        new gsurveyLikertChoice(gsurveyLikertStrings.columnLabel5, GenerateLikertChoiceValue(field), 5)
    );

    field.gsurveyLikertRows = new Array(
        new gsurveyLikertRow(gsurveyLikertStrings.firstChoice),
        new gsurveyLikertRow(gsurveyLikertStrings.secondChoice),
        new gsurveyLikertRow(gsurveyLikertStrings.thirdChoice),
        new gsurveyLikertRow(gsurveyLikertStrings.fourthChoice),
        new gsurveyLikertRow(gsurveyLikertStrings.fifthChoice)
    );

    var fieldNumber = field.id + '.1';
    var rowValue = field.gsurveyLikertRows[0].value;
    field.inputType = 'likert';

    //field.inputs = new Array(new gsurveyLikertInput(fieldNumber, "", rowValue));
    field.inputs = null;

    return field;
}

jQuery(document).bind('gform_load_field_settings', function (event, field, form) {

    if (field.inputType == 'likert') {

        if (typeof field.gsurveyLikertEnableMultipleRows == 'undefined')
            field.gsurveyLikertEnableMultipleRows = false;
        if (typeof field.gsurveyLikertEnableScoring == 'undefined')
            field.gsurveyLikertEnableScoring = false;

        jQuery('#gsurvey-likert-enable-multiple-rows').prop('checked', field.gsurveyLikertEnableMultipleRows);
        jQuery('#gsurvey-likert-enable-scoring').prop('checked', field.gsurveyLikertEnableScoring);

        jQuery("#gsurvey-field-type").val('likert');

        if (field.gsurveyLikertEnableMultipleRows)
            jQuery('.gsurvey-likert-setting-rows').show();

        if (field.gsurveyLikertEnableScoring)
            jQuery('#gsurvey-likert-columns-container').addClass('gsurvey-likert-scoring-enabled');
        else
            jQuery('#gsurvey-likert-columns-container').removeClass('gsurvey-likert-scoring-enabled');

        jQuery('#gsurvey-likert-columns-container ul#gsurvey-likert-columns').html(gsurveyLikertGetColumns(field));
        jQuery('#gsurvey-likert-rows-container ul#gsurvey-likert-rows').html(gsurveyLikertGetRows(field));

    }

});


function gsurveyLikertUpdatePreview(field) {
    if (field == undefined)
        field = GetSelectedField();
    var fieldPreviewMarkup = gsurveyLikertGetFieldPreviewMarkup(field);
    jQuery('.field_selected .gsurvey-likert').parent().html(fieldPreviewMarkup);
}

function gsurveyLikertUpdateInputs(field) {

    if (field.gsurveyLikertEnableMultipleRows) {
        var fieldNumber, rowValue, skip;
        field.inputs = new Array();
        skip = 0;
        for (var i = 0; i < field.gsurveyLikertRows.length; i++) {
            rowValue = field.gsurveyLikertRows[i].value;
            if ((i + 1 + skip) % 10 == 0) {
                skip++;
            }
            fieldNumber = field.id + '.' + (i + 1 + skip);
            field.inputs.push(new gsurveyLikertInput(fieldNumber, field.gsurveyLikertRows[i].text, rowValue));
        }

    } else {
        field.inputs = null;
    }

}

function gsurveyLikertInput(id, label, name) {
    this.id = id;
    this.label = label;
    this.name = name;
}

function gsurveyLikertGetFieldPreviewMarkup(field) {
    var m,
        numRows = field.gsurveyLikertEnableMultipleRows ? field.gsurveyLikertRows.length : 1,
        displayRows = numRows > 5 ? 5 : numRows;

    m = "<table class='gsurvey-likert'>";
    m += '<thead>';
    if (field.gsurveyLikertEnableMultipleRows)
        m += "<td class='gsurvey-likert-row-label'></td>";
    for (var i = 0; i < field.choices.length; i++) {
        var id = 'choice_' + field.id + '_' + i;
        m += "<th class='gsurvey-likert-choice-label'><label for='" + id + "'>" + field.choices[i].text + '</label></th>';
    }
    m += '</thead>';

    for (var r = 1; r <= displayRows; r++) {
        m += '<tr>';
        if (field.gsurveyLikertEnableMultipleRows)
            m += '<td class="gsurvey-likert-row-label">' + field.gsurveyLikertRows[r - 1].text + '</td>';
        for (var i = 0; i < field.choices.length; i++) {
            m += "<td class='gsurvey-likert-choice'><input type='radio' disabled='disabled'></td>";
        }
        m += '</tr>';
    }

    if (numRows > 5) {
        var colCount = field.choices.length + 1;
        m += "<tr><td colspan='" + colCount + "'>" + gf_vars['editToViewAll'].replace('%d', numRows) + '</td></tr>';
    }

    m += '</table>';
    return m;
}

// likert columns

function gsurveyLikertChoice(text, value, score){
    this.text=text;
    this.value = value ? value : text;
    this.isSelected = false;
    this.score = score;
}

function gsurveyLikertUpdateColumnsObject() {
    var field = GetSelectedField();
    jQuery('ul#gsurvey-likert-columns li').each(function (index) {
        $this = jQuery(this);
        var text = $this.children('input.gsurvey-likert-column-text').val();
        var val = $this.children('input.gsurvey-likert-column-value').val();
        var score = $this.children('input.gsurvey-likert-column-score').val();
        var i = $this.data('index');
        if(typeof score == 'undefined')
            score = i+1;
        var g = new gsurveyLikertChoice(text, val, score);
        field.choices[i] = g;
    });
    gsurveyLikertUpdateInputs(field);

}

function GenerateLikertChoiceValue(field) {
    return 'glikertcol' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });

}

function gsurveyLikertInsertColumn(index) {
    var field = GetSelectedField();
    gsurveyLikertUpdateColumnsObject();

    var g = new Choice('', GenerateLikertChoiceValue(field));
    field.choices.splice(index, 0, g);
    jQuery('#gsurvey-likert-columns-container ul#gsurvey-likert-columns').html(gsurveyLikertGetColumns(field));
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertDeleteColumn(index) {
    var field = GetSelectedField();
    gsurveyLikertUpdateColumnsObject();
    field.choices.splice(index, 1);
    jQuery('#gsurvey-likert-columns-container ul#gsurvey-likert-columns').html(gsurveyLikertGetColumns(field));
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertMoveColumn(fromIndex, toIndex) {
    var field = GetSelectedField();
    gsurveyLikertUpdateColumnsObject();
    var column = field.choices[fromIndex];

    //deleting from old position
    field.choices.splice(fromIndex, 1);

    //inserting into new position
    field.choices.splice(toIndex, 0, column);

    jQuery('#gsurvey-likert-columns-container ul#gsurvey-likert-columns').html(gsurveyLikertGetColumns(field));
    gsurveyLikertUpdateColumnsObject();
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertGetColumns( field ) {
	var imagesUrl = gsurveyVars.imagesUrl;
	var str = '';
	var score;
	var i;

	for ( i = 0; i < field.choices.length; i++ ) {
		var elements = {
			handle: '<i class="field-choice-handle field-choice-handle--column"></i>',
			add: '<button class="field-choice-button field-choice-button--insert gf_insert_field_choice" onclick="gsurveyLikertInsertColumn(' + ( i + 1 ) + ');"></button>',
			remove: '<button class="field-choice-button field-choice-button--delete gf_delete_field_choice" onclick="gsurveyLikertDeleteColumn(' + i + ');"></button>',
		};

		if ( isLegacy ) {
			elements = {
				handle: '<img src="' + imagesUrl + '/arrow-handle.svg" width="14" height="14" class="gsurvey-liket-column-handle gsurvey-likert-handle--legacy" alt="' + gsurveyLikertStrings.dragToReOrder + '" />',
				add: '<img src="' + imagesUrl + '/add.svg" width="16" height="16" class="add_field_choice" title="' + gsurveyLikertStrings.addAnotherColumn + '" alt="' + gsurveyLikertStrings.addAnotherColumn + '" style="cursor:pointer; margin:0 3px;" onclick="gsurveyLikertInsertColumn(' + ( i + 1 ) + ');" />',
				remove: '<img src="' + imagesUrl + '/remove.svg" width="16" height="16" title="' + gsurveyLikertStrings.removeThisColumn + '" alt="' + gsurveyLikertStrings.removeThisColumn + '" class="delete_field_choice" style="cursor:pointer;" onclick="gsurveyLikertDeleteColumn(' + i + ');" />',
			};
		}

		str += isLegacy ? '<li data-index="' + i + '">' : '<li class="field-choice-row" data-index="' + i + '">';
		str += elements.handle;

		str += "<input type='text' id='gsurvey-likert-column-text-" + i + "' value=\"" + field.choices[i].text.replace(/"/g, "&quot;") + "\"  class='gsurvey-column-input gsurvey-likert-column-text field-choice-text field-choice-text--likert' onkeyup='gsurveyLikertUpdateColumnsObject();gsurveyLikertUpdatePreview()'/>";

		if ( typeof field.choices[ i ].score == 'undefined' ) {
			field.choices[ i ].score = i + 1;
		}

		score = field.choices[ i ].score;

		str += "<input type='text' id='gsurvey-likert-column-score-" + i + "' value=\"" + score + "\"  class='gsurvey-column-input gsurvey-likert-column-score' onkeyup='gsurveyLikertUpdateColumnsObject();gsurveyLikertUpdatePreview()'/>";
		str += "<input type='hidden' id='gsurvey-likert-column-value-" + i + "' value=\"" + field.choices[i].value + "\" class='gsurvey-likert-column-value' >";
		str += elements.add;

		if ( field.choices.length > 1 ) {
			str += elements.remove;
		}

		str += '</li>';
	}

	return str;
}

// likert rows

function gsurveyLikertUpdateRowsObject() {
    var field = GetSelectedField();
    jQuery('#gsurvey-likert-rows li').each(function (index) {
        var gsurveyLikertRowText = jQuery(this).children('input.gsurvey-likert-row-text').val();
        var gsurveyLikertRowVal = jQuery(this).children('input.gsurvey-likert-row-id').val();
        var i = jQuery(this).data("index");
        var g = new gsurveyLikertRow(gsurveyLikertRowText, gsurveyLikertRowVal);
        field.gsurveyLikertRows[i] = g;
    });
    gsurveyLikertUpdateInputs(field);

}

function gsurveyLikertGenerateRowVal() {
    return 'glikertrowxxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });

}

function gsurveyLikertRow(text, value) {
    this.text = text;
    if (value == undefined)
        this.value = gsurveyLikertGenerateRowVal();
    else
        this.value = value;
}

function gsurveyLikertInsertRow(index) {
    var field = GetSelectedField();
    gsurveyLikertUpdateRowsObject();

    var g = new gsurveyLikertRow('');
    field.gsurveyLikertRows.splice(index, 0, g);
    jQuery('#gsurvey-likert-rows-container ul#gsurvey-likert-rows').html(gsurveyLikertGetRows(field));
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertDeleteRow(index) {
    var field = GetSelectedField();
    gsurveyLikertUpdateRowsObject();
    field.gsurveyLikertRows.splice(index, 1);
    jQuery('#gsurvey-likert-rows-container ul#gsurvey-likert-rows').html(gsurveyLikertGetRows(field));
    field.inputs.splice(index, 1);
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertMoveRow(fromIndex, toIndex) {
    var field = GetSelectedField();
    gsurveyLikertUpdateRowsObject();
    var row = field.gsurveyLikertRows[fromIndex];

    //deleting from old position
    field.gsurveyLikertRows.splice(fromIndex, 1);

    //inserting into new position
    field.gsurveyLikertRows.splice(toIndex, 0, row);

    jQuery('#gsurvey-likert-rows-container ul#gsurvey-likert-rows').html(gsurveyLikertGetRows(field));
    gsurveyLikertUpdateRowsObject();
    gsurveyLikertUpdatePreview(field);
}

function gsurveyLikertGetRows( field ) {
	var imagesUrl = gsurveyVars.imagesUrl;
	var str = '';

	for ( var i = 0; i < field.gsurveyLikertRows.length; i++ ) {
		var elements = {
			handle: '<i class="field-choice-handle field-choice-handle--row"></i>',
			add: '<button class="field-choice-button field-choice-button--insert gf_insert_field_choice" onclick="gsurveyLikertInsertRow(' + ( i + 1 ) + ');"></button>',
			remove: '<button class="field-choice-button field-choice-button--delete gf_delete_field_choice" onclick="gsurveyLikertDeleteRow(' + i + ');"></button>',
		};

		if ( isLegacy ) {
			elements = {
				handle: '<img src=\'' + imagesUrl + '/arrow-handle.svg\' width=\'14\' height=\'14\' class=\'gsurvey-liket-row-handle gsurvey-likert-handle--legacy\' alt=\'' + gsurveyLikertStrings.dragToReOrder + '\' /> ',
				add: '<img src=\'' + imagesUrl + '/add.svg\' width=\'16\' height=\'16\' class=\'add_field_choice\' title=\'' + gsurveyLikertStrings.addAnotherRow + '\' alt=\'' + gsurveyLikertStrings.addAnotherRow + '\' style=\'cursor:pointer; margin:0 3px;\' onclick="gsurveyLikertInsertRow(' + ( i + 1 ) + ');" />',
				remove: '<img src=\'' + imagesUrl + '/remove.svg\' width=\'16\' height=\'16\' title=\'' + gsurveyLikertStrings.removeThisRow + '\' alt=\'' + gsurveyLikertStrings.removeThisRow + '\' class=\'delete_field_choice\' style=\'cursor:pointer;\' onclick="gsurveyLikertDeleteRow(' + i + ');" />',
			};
		}

		str += isLegacy ? '<li data-index="' + i + '">' : '<li class="field-choice-row" data-index="' + i + '">';
		str += elements.handle;
		str += '<input type=\'text\' id=\'gsurvey-likert-row-text-' + i + '\' value="' + field.gsurveyLikertRows[ i ].text.replace( /"/g, '&quot;' ) + '"  class=\'gsurvey-row-input gsurvey-likert-row-text field-choice-text field-choice-text--likert\' onkeyup=\'gsurveyLikertUpdateRowsObject(); gsurveyLikertUpdatePreview();\' />';
		str += '<input type=\'hidden\' id=\'gsurvey-likert-row-id-' + i + '\' value="' + field.gsurveyLikertRows[ i ].value + '" class=\'gsurvey-likert-row-id\' >';
		str += elements.add;

		if ( field.gsurveyLikertRows.length > 1 ) {
			str += elements.remove;
		}

		str += '</li>';
	}

	return str;
}

/*----  Rank ----*/

function SetDefaultValues_rank(field) {

    field.inputType = 'rank';
    field.inputs = null;
    field.enableChoiceValue = true;
    field.enablePrice = false;

    if (!field.choices) {
        field.choices = new Array(
            new Choice(gsurveyRankStrings.firstChoice, GenerateRankChoiceValue(field), false),
            new Choice(gsurveyRankStrings.secondChoice, GenerateRankChoiceValue(field), false),
            new Choice(gsurveyRankStrings.thirdChoice, GenerateRankChoiceValue(field), false),
            new Choice(gsurveyRankStrings.fourthChoice, GenerateRankChoiceValue(field), false),
            new Choice(gsurveyRankStrings.fifthChoice, GenerateRankChoiceValue(field), false)
        );
    }

    return field;
}
function GenerateRankChoiceValue(field) {
    return 'grank' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });
}

function gform_new_choice_rank(field, choice) {
    if (field.inputType == 'rank')
        choice['value'] = GenerateRankChoiceValue(field);
    return choice;
}

jQuery(document).bind('gform_load_field_choices', function (event, field) {
    if (field.inputType == 'rank') {
        jQuery('.field_selected .gsurvey-rank').html(gsurveyRankGetFieldPreviewMarkup(field));
    }
});

function gsurveyRankGetFieldPreviewMarkup(field) {
    var m = '';
    for (var i = 0; i < field.choices.length; i++) {
        var id = 'choice_' + field.id + '_' + i;
        if (i < 5)
            m += "<li class='gform-field-label gform-field-label--type-inline gsurvey-rank-choice'>" + field.choices[i].text + "</li>";
    }
    if (field.choices.length > 5)
        m += "<li class='gchoice_total'>" + gf_vars['editToViewAll'].replace('%d', field.choices.length) + '</li>';

    return m;
}


/*----  Rating ----*/

function SetDefaultValues_rating(field) {

    field.inputType = 'rating';
    field.inputs = null;
    field.enableChoiceValue = true;
    field.enablePrice = false;
    field.reversed = true;

    if (!field.choices) {
        field.choices = new Array(
            new Choice(gsurveyRatingStrings.firstChoice, GenerateRatingChoiceValue(field), false),
            new Choice(gsurveyRatingStrings.secondChoice, GenerateRatingChoiceValue(field), false),
            new Choice(gsurveyRatingStrings.thirdChoice, GenerateRatingChoiceValue(field), false),
            new Choice(gsurveyRatingStrings.fourthChoice, GenerateRatingChoiceValue(field), false),
            new Choice(gsurveyRatingStrings.fifthChoice, GenerateRatingChoiceValue(field), false)
        );
    }

    return field;
}

function GenerateRatingChoiceValue(field) {
    return 'grating' + field.id + 'xxxxxxxx'.replace(/[xy]/g, function (c) {
        var r = Math.random() * 16 | 0, v = c == 'x' ? r : r & 0x3 | 0x8;
        return v.toString(16);
    });

}

function gform_new_choice_rating(field, choice) {

    if (field.inputType == 'rating')
        choice['value'] = GenerateRatingChoiceValue(field);

    return choice;
}

jQuery(document).bind('gform_load_field_choices', function (event, field) {

    if (field.inputType == 'rating') {
        if (typeof field.reversed == 'undefined') {
            var $choices = jQuery('#field_choices');
            $choices.children().each(function (i, li) {
                $choices.prepend(li)
            });
            field.choices = field.choices.reverse();
            field.reversed = true;
        }

        if( gsurveyVars.refreshPreview ) {
            var fieldPreviewMarkup = gsurveyRatingGetFieldPreviewMarkup(field);
            jQuery('.field_selected .gsurvey-rating').html(fieldPreviewMarkup);
        }

    }

});

function gsurveyRatingGetFieldPreviewMarkup(field) {
    var m = "";
    for (var i = 0; i < field.choices.length; i++) {
        var id = 'choice_' + field.id + '_' + i;
        var checked = field.choices[i].isSelected ? 'checked' : '';
        m += "<input name='input_" + field.id + "' type='radio' " + checked + " id='" + id + "' disabled='disabled'/><label for='" + id + "'><span>" + field.choices[i].text + '</span></label>';
    }

    return m;
}


jQuery(document).ready(function () {

    jQuery('#gsurvey-likert-columns').sortable({
        axis: 'y',
        handle: isLegacy ? '.gsurvey-liket-column-handle' : '.field-choice-handle--column',
        update: function (event, ui) {
            var fromIndex = ui.item.data('index');
            var toIndex = ui.item.index();
            gsurveyLikertMoveColumn(fromIndex, toIndex);
        }
    });

    jQuery('#gsurvey-likert-rows').sortable({
        axis: 'y',
        handle: isLegacy ? '.gsurvey-liket-row-handle' : '.field-choice-handle--row',
        update: function (event, ui) {
            var fromIndex = ui.item.data('index');
            var toIndex = ui.item.index();
            gsurveyLikertMoveRow(fromIndex, toIndex);
        }
    });

});

gform.addFilter( 'gform_conditional_logic_values_input', function( markup, objectType, ruleIndex, selectedFieldId, selectedValue ) {
    var field= GetFieldById( selectedFieldId );

    if( ! field ) {
        return markup;
    }

    if ( GetInputType( field ) === 'likert' ) {
        markup = filterLikertValues( markup, objectType, ruleIndex, selectedFieldId, selectedValue );
    }

    if ( GetInputType( field ) === 'rank' ) {
        markup = filterRankValues( markup, objectType, ruleIndex, selectedFieldId, selectedValue );
    }

    return markup;
} );

function filterLikertValues( markup, objectType, ruleIndex, selectedFieldId, selectedValue ) {
    var field = GetFieldById( selectedFieldId );

    if ( ! field.inputs ) {
        return markup;
    }

    // When default conditional logic is generated by the Settings API, it just gets the field ID, not the input ID, so we have to add the input ID here.
    if ( field.gsurveyLikertEnableMultipleRows === true && ! selectedFieldId.toString().match(/\.\d$/)  ) {
        let firstInputIdString = field.inputs[0].id.toString();
        let match = firstInputIdString.match(/\.\d+$/);
        selectedFieldId = selectedFieldId + match[0];
    }

    var thisInput = field.inputs.filter(function (input) {
        return input.id === selectedFieldId;
    });

    if ( thisInput.length === 0 ) {
        return markup;
    }

    var row = thisInput[0].name;

    // use regex to add the row name to the value
    markup = markup.replace(/glikertcol/g, row + ':glikertcol');

    // GetRuleValuesDropDown in form_admin.js gets confused, because it can't find the selected value, and it adds a new option, which we need to take out here
    var parser = new DOMParser();
    var doc    = parser.parseFromString(markup, 'text/html');

    var selectedOption = doc.querySelector('option[selected="selected"]');
    if (selectedOption) {
        // if selectedOption has a value that contains :glikertrow, remove it
        if( selectedOption.value.indexOf(':glikertrow') !== -1 ) {
            selectedOption.parentNode.removeChild( selectedOption );

            var markup = doc.documentElement.outerHTML;
        }
    }

    // parse the html to add the "selected" attribute to the correct option
    var index = markup.indexOf('value="' + selectedValue + '"');
    if (index !== -1) {
        var selectedOption = markup.slice(0, index) + 'selected="selected" ' + markup.slice(index);
        markup = selectedOption;
    }

    return markup;
}

function filterRankValues( markup, objectType, ruleIndex, selectedFieldId, selectedValue ) {
    var field = GetFieldById( selectedFieldId );

    // Remove the old options from the markup
    var tempDiv = document.createElement( 'div' );
    tempDiv.innerHTML = markup;

    var selectElement = tempDiv.querySelector( 'select' );
    if ( ! selectElement ) {
        return markup;
    }
    selectElement.innerHTML = '';

    // Add the new options
    field.choices.forEach(function(item, index) {
        var option = document.createElement('option');
        var number = index + 1;
        option.value = number;
        option.text = number;
        selectElement.appendChild(option);
    });

    var optionToSelect = selectElement.querySelector( 'option[value="' + selectedValue + '"]' );
    if ( optionToSelect ) {
        optionToSelect.setAttribute( 'selected', 'selected' );
    }

    markup = tempDiv.innerHTML;
    return markup;
}

gform.addFilter( 'gform_conditional_logic_operators', function ( operators, objectType, fieldId ) {
    if( fieldId == 0 )
        fieldId = GetFirstRuleField();

    var field = GetFieldById( fieldId );

    if ( ! field ) {
        return operators;
    }

    if ( GetInputType( field ) === 'rank' ) {
        operators = { "is": gsurveyRankConditionStrings.is, "isnot": gsurveyRankConditionStrings.isNot, ">": gsurveyRankConditionStrings.greaterThan, "<": gsurveyRankConditionStrings.lessThan };
    }

    if ( GetInputType( field ) === 'likert' ) {
        operators = { "is": gsurveyLikertConditionStrings.is, "isnot": gsurveyLikertConditionStrings.isNot };
    }

    return operators;
});

gform.addFilter( 'gform_conditional_logic_fields', function( options, form, selectedFieldId  ) {
    // Filter the options for the Rank field
    let newOptions = options.slice();

    form.fields.forEach(function (field) {
        if (GetInputType(field) == 'rank') {
            const choices = field.choices;
            const removedItem = newOptions.find(option => option.value === field.id);
            const removedIndex = newOptions.indexOf(removedItem);

            // Remove the specific 'rank' type item
            newOptions.splice(removedIndex, 1);

            choices.forEach(function (choice, choiceIndex) {
                const newIndex = removedIndex + choiceIndex;
                newOptions.splice(newIndex, 0, {
                    label: field.label + ' (' + choice.text + ')',
                    value: field.id + '.' + choices.indexOf(choice)
                });
            });
        }
    });

    options = newOptions.slice();

    return options;
} );
