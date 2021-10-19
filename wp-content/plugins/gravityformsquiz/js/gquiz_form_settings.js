(function (gquiz, $) {
    var grades = {};

    gquiz.init = function (){

        var gradesJSON = $("#grades").val();
        grades = $.parseJSON(gradesJSON);

        $(".gquiz-grading").click(function(){
            toggleGradingOptions(this.value);
        });

        toggleGradingOptions( $("input[name='_gform_setting_grading']:checked").val() );

		initializeQuizConfirmations();

        $('#gquiz-grades').html(getGrades());

        $(document).on("blur", 'input.gquiz-grade-value', (function () {
            var $this = $(this);
            var percent = $this.val();
            if (percent < 0 || isNaN(percent)) {
                $this.val(0);
            } else if (percent > 100) {
                $this.val(100);
            }
        }));

        $(document).on("keypress", 'input.gquiz-grade-value', (function (event) {
            if (event.which == 27) {
                this.blur();
                return false;
            }
            if (event.which === 0 || event.which === 8)
                return true;
            if (event.which < 48 || event.which > 57) {
                event.preventDefault();
            }

        }));

        //enble sorting on the grades table
        $('#gquiz-grades').sortable({
            axis  : 'y',
            handle: '.gquiz-grade-handle',
            update: function (event, ui) {
                var fromIndex = ui.item.data("index");
                var toIndex = ui.item.index();
                moveGrade(fromIndex, toIndex);
            }
        });


        $("#gform-settings").submit(function () {
            updateGradesObject();
            $("#grades").val($.toJSON(grades));
        })
    }

    function toggleGradingOptions(gradeOption) {
        switch (gradeOption) {
            case "none" :
                $('#passfail_grading_options').fadeOut('fast');
                $('#letter_options_section').fadeOut('fast');
                break;
            case "passfail" :
                $('#letter_options_section').fadeOut('fast');
                $('#passfail_grading_options').fadeIn('fast');
                break;
            case "letter" :
                $('#passfail_grading_options').fadeOut('fast');
                $('#letter_options_section').fadeIn('fast');
                break;
        }
    }

	/**
	 * Initializes the state and event handlers for the Quiz Confirmation sections.
	 */
	function initializeQuizConfirmations() {
		var passFailConfirmationFields = [
			$( '#gform_setting_passConfirmationMessage' ),
			$( '#gform_setting_passConfirmationDisableAutoformat' ),
			$( '#gform_setting_failConfirmationMessage' ),
			$( '#gform_setting_failConfirmationDisableAutoformat' ),
		];

		var letterGradeConfirmationFields = [
			$( '#gform_setting_letterConfirmationMessage' ),
			$( '#gform_setting_letterConfirmationDisableAutoformat' ),
		];

		toggleQuizConfirmations( $( '#passfaildisplayconfirmation' ), passFailConfirmationFields );
		toggleQuizConfirmations( $( '#letterdisplayconfirmation' ), letterGradeConfirmationFields );
	}

	/**
	 * Loops through Quiz Confirmation fields and shows or hides depending on the value of the checkbox.
	 *
	 * @param {Object} $gradingOption The selected Grading Setting radio input.
	 * @param {Array} $quizConfirmationFields Array of quiz confirmation inputs for a given Grading Setting.
	 */
	function toggleQuizConfirmations( $gradingOption, $quizConfirmationFields ) {
		for ( var i = 0, length = $quizConfirmationFields.length; i < length; i++ ) {
			$gradingOption.prop( 'checked' ) ? $quizConfirmationFields[ i ].show() : $quizConfirmationFields[ i ].hide();
		}
	}

    function Grade(text, value) {
        this.text = text;
        this.value = value;
    }

    gquiz.insertGrade = function(index) {
        updateGradesObject();
        var gradeBelowVal;
        var gradeAbove = grades[index - 1];
        var gradeBelow = grades[index];
        if (typeof gradeBelow == 'undefined')
            gradeBelowVal = 0;
        else
            gradeBelowVal = gradeBelow.value;
        var newValue = parseInt(gradeBelowVal) + parseInt(( gradeAbove.value - gradeBelowVal ) / 2);
        var g = new Grade("", newValue);
        grades.splice(index, 0, g);
        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    gquiz.deleteGrade = function (index) {
        updateGradesObject();
        grades.splice(index, 1);
        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    function moveGrade(fromIndex, toIndex) {
        updateGradesObject();
        var grade = grades[fromIndex];

        //deleting from old position
        grades.splice(fromIndex, 1);

        //inserting into new position
        grades.splice(toIndex, 0, grade);

        $('div#gquiz-settings-grades-container ul#gquiz-grades').html(getGrades());
    }

    function getGrades() {

        var imagesUrl = gquizVars.imagesUrl;
        var str = "";
        for (var i = 0; i < grades.length; i++) {

            str += "<li data-index='" + i + "'>";
            str += "<i class='field-choice-handle gquiz-grade-handle' title='" + gquiz_strings.dragToReOrder + "'></i>";
            //str += "<img src='" + imagesUrl + "/arrow-handle.png' class='gquiz-grade-handle' alt='" + gquiz_strings.dragToReOrder + "' /> ";
            str += "<input type='text' id='gquiz-grade-text-" + i + "' value=\"" + grades[i].text.replace(/"/g, "&quot;") + "\"  class='gquiz-grade-input gquiz-grade-text' />";
            str += " <span class='gquiz-greater-than-or-equal'>&gt;=</span> ";

            str += '<div class="gquiz-percentage-container">';
            str += "<input type='text' id='gquiz-grade-value-" + i + "' value=\"" + grades[i].value + "\" class='gquiz-grade-input gquiz-grade-value percentage-input' >";
            str += "<span class='gform-settings-field__text-append'>&#37;</span> ";
            str += '</div><!-- .percentage-container -->';

            str += '<div class="gquiz-grade-buttons">';
            str += "<button class='gquiz-grade-row-button gquiz-add-grade' title='" + gquiz_strings.addAnotherGrade + "' alt='" + gquiz_strings.addAnotherGrade + "' onclick=\"gquiz.insertGrade(" + (i + 1) + ");\" />";

            if (grades.length > 1) {
                str += "<button class='gquiz-grade-row-button gquiz-delete-grade' title='" + gquiz_strings.removeThisGrade + "' alt='" + gquiz_strings.removeThisGrade + "' onclick=\"gquiz.deleteGrade(" + i + ");\" />";
			}

            str += '</div><!-- .gquiz-grade-buttons -->';

            str += "</li>";

        }

        return str;
    }

    function updateGradesObject() {

        $('ul#gquiz-grades li').each(function (index) {
            var $this = $(this);
            var gquizText = $this.find('input.gquiz-grade-text').val();
            var gquizValue = $this.find('input.gquiz-grade-value').val();
            var i = $this.data("index");
            var g = new Grade(gquizText, parseInt(gquizValue));
            grades[parseInt(i)] = g;
        });

    }


    String.prototype.format = function () {
        var args = arguments;
        return this.replace(/{(\d+)}/g, function (match, number) {
            return typeof args[number] != 'undefined' ? args[number] : match;
        });
    };

}(window.gquiz = window.gquiz || {}, jQuery));

jQuery(document).ready(function () {

    gquiz.init();
});

//------------------ Grades -----------------



jQuery(document).ready(function () {
    if (typeof form == 'undefined')
        return;

    //defaults added in php
    /*
    if (typeof form.gquizGrading == 'undefined')
        form.gquizGrading = 'none';
    if (typeof form.gquizConfirmationFail == 'undefined')
        form.gquizConfirmationFail = gquiz_strings.gquizConfirmationFail;
    if (typeof form.gquizConfirmationPass == 'undefined')
        form.gquizConfirmationPass = gquiz_strings.gquizConfirmationPass;
    if (typeof form.gquizConfirmationLetter == 'undefined')
        form.gquizConfirmationLetter = gquiz_strings.gquizConfirmationLetter;
    if (typeof form.gquizConfirmationPassAutoformatDisabled == 'undefined')
        form.gquizConfirmationPassAutoformatDisabled = false;
    if (typeof form.gquizConfirmationFailAutoformatDisabled == 'undefined')
        form.gquizConfirmationFailAutoformatDisabled = false;
    if (typeof form.gquizConfirmationLetterAutoformatDisabled == 'undefined')
        form.gquizConfirmationLetterAutoformatDisabled = false;

    if (typeof form.gquizGrades == 'undefined' || form.gquizGrades.length == 0)
        form.gquizGrades = new Array(
            new gquiz_Grade(gquiz_strings.gradeA, 90),
            new gquiz_Grade(gquiz_strings.gradeB, 80),
            new gquiz_Grade(gquiz_strings.gradeC, 70),
            new gquiz_Grade(gquiz_strings.gradeD, 60),
            new gquiz_Grade(gquiz_strings.gradeE, 0)
        );


    if (typeof form.gquizPassMark == 'undefined')
        form.gquizPassMark = "50";

    if (typeof form.gquizShuffleFields == 'undefined')
        form.gquizShuffleFields = false;
    if (typeof form.gquizInstantFeedback == 'undefined')
        form.gquizInstantFeedback = false;

    if (typeof form.gquizConfirmationTypePassFail == 'undefined')
        form.gquizConfirmationTypePassFail = "quiz";

    if (typeof form.gquizConfirmationTypeLetter == 'undefined')
        form.gquizConfirmationTypeLetter = "quiz";


    if(typeof form.gquizDisplayConfirmationPassFail == 'undefined')
        form.gquizDisplayConfirmationPassFail = true;
    if(typeof form.gquizDisplayConfirmationLetter== 'undefined')
        form.gquizDisplayConfirmationLetter = true;
        */



});

