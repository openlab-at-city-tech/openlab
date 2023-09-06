(function($){
    $(document).ready(function(){
        
        $(document).find('#ays-type').select2({
            placeholder: 'Select question type',
            templateResult: ays_formatState,
            templateSelection: ays_formatState
        }).on('change', function (e) {
            var _this = $(this);

            var questionType = _this.val();
            var defaultCount = $(document).find('input[name="ays_answer_default_count"]');

            if ( questionType == 'true_or_false' ) {
                if ( defaultCount.length > 0 ) {
                    defaultCount.val( 2 );
                }
            }
        });

        function ays_formatState (ays_state) {
            if(!ays_state.id) {
                return aysEscapeHtml(ays_state.text);
            }
            var baseUrl = $(ays_state.element).data('nkar');
            if(baseUrl != ''){
                var ays_state = $(
                    '<span><img src=' + baseUrl + ' class="ays_answer_select_image" /> ' + aysEscapeHtml(ays_state.text) + '</span>'
                );
            }else{
                var ays_state = $('<span>' + aysEscapeHtml(ays_state.text) + '</span>');
            }
            return ays_state;
        }

        $(document).find('#ays-cat').select2({
            placeholder: 'Select question category'
        });

        $(document).on('change', '#ays-type', function () {
            switch ($(this).val()) {
                case 'checkbox':
                    $(document).find('input.ays-correct-answer').attr('type', 'checkbox');
                    break;
                default:
                    $(document).find('input.ays-correct-answer').attr('type', 'radio');
                    break;
            }
        });

        $(document).find('#ays-type').on('change', function(e){
            var answer_row = $('.ays-answer-row'),
                row_count = answer_row.length,
                row_id = row_count + 1,
                customTypes = [ 'custom' ],
                isCustomType = false,
                textTypes = [ 'text', 'short_text' ],
                isTextType = false,
                numberTypes = [ 'number' ],
                isNumberType = false,
                checkboxTypes = [ 'checkbox' ],
                isCheckboxType = false,
                mixedTypes = [ 'matching' ],
                isMixedType = false,
                matchingTypes = [ 'matching' ],
                isMatchingType = false,
                cloned;
            cloned = ((row_count % 2) === 0) ? answer_row.eq(0).clone() : answer_row.eq(1).clone();
            $(document).find('.ays-answers-toolbar-bottom').hide();
                $(document).find('.ays-text-answers-desc').hide();

            if($(this).val() != 'radio' && $(this).val() != 'checkbox'){
                $(document).find('.ays-answers-toolbar-bottom input[name="ays-use-html"]').removeAttr('checked');
            }

            for (var i = 0; i < customTypes.length; i++) {
                if($(this).val() == customTypes[i]){
                    isCustomType = true;
                    break;
                }
            }

            for (var i = 0; i < numberTypes.length; i++) {
                if($(this).val() == numberTypes[i]){
                    isNumberType = true;
                    break;
                }
            }

            if(isCustomType){
                $(document).find('.hide_for_custom_type').addClass('display_none');
                switch($(this).val()){
                    case 'custom':
                        $(document).find('.show_for_custom_type').removeClass('display_none');
                    break;
                }
            }else{
                $(document).find('.hide_for_custom_type').removeClass('display_none');
                $(document).find('.show_for_custom_type').addClass('display_none');
            }

            for (var i = 0; i < textTypes.length; i++) {
                if($(this).val() == textTypes[i]){
                    isTextType = true;
                    break;
                }
            }

            for (var i = 0; i < checkboxTypes.length; i++) {
                if($(this).val() == checkboxTypes[i]){
                    isCheckboxType = true;
                    break;
                }
            }

            for (var i = 0; i < mixedTypes.length; i++) {
                if($(this).val() == mixedTypes[i]){
                    isMixedType = true;
                    break;
                }
            }

            for (var i = 0; i < matchingTypes.length; i++) {
                if($(this).val() == matchingTypes[i]){
                    isMatchingType = true;
                    break;
                }
            }

            var showForTextType = $(document).find('.show_for_text_type');
            if(isTextType){
                if(showForTextType.hasClass('display_none')){
                    showForTextType.removeClass('display_none');
                }
            }else{
                if(! showForTextType.hasClass('display_none')){
                    showForTextType.addClass('display_none');
                }
            }

            var showForNumberType = $(document).find('.show_for_number_type');
            if(isNumberType){
                if(showForNumberType.hasClass('display_none')){
                    showForNumberType.removeClass('display_none');
                }
            }else{
                if(! showForNumberType.hasClass('display_none')){
                    showForNumberType.addClass('display_none');
                }
            }

            var showForCheckboxType = $(document).find('.show_for_checkbox_type');
            if( isCheckboxType ){
                if(showForCheckboxType.hasClass('display_none')){
                    showForCheckboxType.removeClass('display_none');
                }
            }else{
                if(! showForCheckboxType.hasClass('display_none')){
                    showForCheckboxType.addClass('display_none');
                }
            }

            var showForMixedTypes = $(document).find('.show_only_for_mixed_types');
            if( isMixedType ){
                if(showForMixedTypes.hasClass('display_none')){
                    showForMixedTypes.removeClass('display_none');
                }
            }else{
                if( ! showForMixedTypes.hasClass('display_none') ){
                    showForMixedTypes.addClass('display_none');
                }
            }

            var showForMatchingTypes = $(document).find('.show_only_for_matching_type');
            if( isMatchingType ){
                if(showForMatchingTypes.hasClass('display_none')){
                    showForMatchingTypes.removeClass('display_none');
                }
            }else{
                if( ! showForMatchingTypes.hasClass('display_none') ){
                    showForMatchingTypes.addClass('display_none');
                }
            }

            $(document).find('table#ays-answers-table thead .ays-weight-row').removeClass('display_none');
            $('table#ays-answers-table thead tr th.ays-quiz-question-answer-answer-row').text( quizLangObj.answer );

            if($(this).val() == 'date'){
                Date.prototype.yyyymmdd = function() {
                    var mm = this.getMonth() + 1; // getMonth() is zero-based
                    var dd = this.getDate();

                    return [this.getFullYear(),
                        (mm>9 ? '' : '0') + mm,
                        (dd>9 ? '' : '0') + dd
                    ].join('-');
                };

                var date = new Date();
                var d = date.yyyymmdd();
                var answerRow = $('<tr class="ays-answer-row ui-state-default">'+
                    '<td>'+
                        '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                    '</td>'+
                    '<td>'+
                        '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                        '<input type="date" name="ays-correct-answer-value[]" class="ays-date-input ays-correct-answer-value" value="" placeholder="e. g. '+d+'"/>'+
                    '</td>'+
                '</tr>');
                $(document).find('table#ays-answers-table tbody').addClass('text_answer');
                $(document).find('label[for="ays-answers-table"]').html('Answer');
                $('table#ays-answers-table tbody').html('');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $('table#ays-answers-table thead tr th:first-child').addClass('th-650');
                $(document).find('table#ays-answers-table tbody').append(answerRow);
            }else if($(this).val() == 'number'){
                var answerRow = $('<tr class="ays-answer-row ui-state-default">'+
                    '<td>'+
                        '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                    '</td>'+
                    '<td>'+
                        '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                        '<input type="number" step="any" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                    '<td>'+
                        '<input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                 '</tr>');
                $(document).find('table#ays-answers-table tbody').addClass('text_answer');
                $(document).find('label[for="ays-answers-table"]').html('Answer');
                $('table#ays-answers-table tbody').html('');
                var answerHeadRowLast = $('<th class="th-350 reremoveable">Placeholder</th>');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $(document).find('table#ays-answers-table thead tr').append(answerHeadRowLast);
                $('table#ays-answers-table thead tr th:first-child').addClass('th-650');
                $(document).find('table#ays-answers-table tbody').append(answerRow);
            }else if($(this).val() == 'short_text'){
                $(document).find('.ays-text-answers-desc').show();
                var answerRow = $('<tr class="ays-answer-row ui-state-default">'+
                    '<td>'+
                        '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                    '</td>'+
                    '<td>'+
                        '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                        '<input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                    '<td>'+
                        '<input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                 '</tr>');
                $(document).find('table#ays-answers-table tbody').addClass('text_answer');
                $(document).find('label[for="ays-answers-table"]').html('Answer');
                $('table#ays-answers-table tbody').html('');
                var answerHeadRowLast = $('<th class="th-350 reremoveable">Placeholder</th>');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $(document).find('table#ays-answers-table thead tr').append(answerHeadRowLast);
                $('table#ays-answers-table thead tr th:first-child').addClass('th-650');
                $(document).find('table#ays-answers-table tbody').append(answerRow);
            }else if($(this).val() == 'fill_in_blank'){
                var slugID = 1;
                var slugID_Value = "{blank_"+ slugID +"}";

                var answerRow = '<tr class="ays-answer-row ui-state-default">'+
                    '<td class="ays-quiz-question-answer-weight-point-row">'+
                        '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                    '</td>'+
                    '<td class="ays-quiz-question-answer-answer-row">'+
                        '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                        '<input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                    '<td class="ays-quiz-question-answer-placeholder-row">'+
                        '<input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                    '<td class="ays-quiz-question-answer-slug-row">'+
                        '<input type="text" name="ays_answer_slug[]" class="ays-answer-slug-value" value="'+ slugID_Value +'" readonly title="'+ quizLangObj.clickForCopy +'" />'+
                    '</td>'+
                    '<td class="ays-quiz-question-answer-delete-row">'+
                        '<a href="javascript:void(0)" class="ays-delete-fill-in-blank-answer">'+
                           ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                        '</a>'+
                    '</td>'+
                '</tr>';


                var addAnswerHTML = '<a href="javascript:void(0)" class="ays-add-fill-in-blank-answer">'+
                    '<i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                '</a>';

                var answerHeadRowListHTML = '<th class="th-350 reremoveable">'+ quizLangObj.placeholder +'</th>'+
                    '<th class="th-150 reremoveable">'+
                        '<span>'+
                           quizLangObj.slug +
                        '</span>'+
                        '<input type="hidden" name="ays_answer_slug_max_id" class="ays-answer-slug-max-id" value="1"/>'+
                    '</th>'+
                    '<th class="th-150 removable ays-quiz-question-answer-delete-row">'+ quizLangObj.delete +'</th>';

                $(document).find('.ays-text-answers-desc').show();
                $(document).find('table#ays-answers-table tbody').addClass('text_answer');
                $(document).find('label[for="ays-answers-table"]').html(addAnswerHTML);
                $('table#ays-answers-table tbody').html('');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $(document).find('table#ays-answers-table thead tr').append(answerHeadRowListHTML);
                $('table#ays-answers-table thead tr th:first-child').addClass('th-650');
                $(document).find('table#ays-answers-table tbody').append(answerRow);
            }else if( $(this).val() == 'matching' ){
                $(document).find('table#ays-answers-table tbody').removeClass('text_answer');
                $(document).find('table#ays-answers-table tbody').addClass('matching_answer')
                row_id = 1;
                var addAnswer = $('<a href="javascript:void(0)" class="ays-add-answer">'+
                        '<i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                    '</a>'),
                    answerHeadRow = $('<th class="th-150 removable">Ordering</th>'),
                    answerHeadRowLast = $('<th class="th-350 removable">Match</th>'+
                        '<th class="th-150 removable">Delete</th>');
                $(document).find('label[for="ays-answers-table"]').html('Choices');
                $('table#ays-answers-table thead tr th.ays-quiz-question-answer-answer-row').text('Choice');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $(document).find('label[for="ays-answers-table"]').append(addAnswer);
                $(document).find('table#ays-answers-table thead tr').prepend(answerHeadRow);
                $(document).find('table#ays-answers-table thead .ays-weight-row').addClass('display_none');
                $(document).find('table#ays-answers-table thead tr').append(answerHeadRowLast);
                $(document).find('table#ays-answers-table tbody').html('');
                $(document).find('.ays-answers-toolbar-bottom').show();

                for(row_id = 1; row_id <= 3; row_id++){
                    var answerRow = '<tr class="ays-answer-row ui-state-default">'+
                        '<td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>'+
                        '<td class="ays-quiz-question-answer-answer-row">'+
                            '<input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="Choice '+ row_id +'"/>'+
                        '</td>'+
                        '<td class="ays-quiz-question-answer-answer-row">'+
                            '<input type="text" name="ays-correct-answer-value-match[]" class="ays-correct-answer-value" value="Match '+ row_id +'"/>'+
                        '</td>'+
                        '<td class="ays-quiz-question-answer-delete-row">'+
                            '<a href="javascript:void(0)" class="ays-delete-answer">'+
                               ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                            '</a>'+
                        '</td>'+
                    '</tr>';
                    $(document).find('table#ays-answers-table tbody').append(answerRow);
                }

                $(document).find('table#ays-answers-matches-table .ays-incorrect-match-row-empty').remove();
                for(row_id = 1; row_id <= 3; row_id++) {
                    var answerRow = $('<tr class="ays-incorrect-match-row">' +
                        '<td class="ays-quiz-question-answer-incorrect-match-row">' +
                            '<input type="text" name="ays-answer-incorrect-matches[]" class="ays-incorrect-answer-match-value" value="Incorrect match ' + row_id + '"/>' +
                        '</td>' +
                        '<td class="ays-quiz-question-answer-incorrect-match-delete-row">'+
                            '<a href="javascript:void(0)" class="ays-delete-answer-incorrect-match">'+
                                ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                            '</a>'+
                        '</td>'+
                    '</tr>');
                    $(document).find('table#ays-answers-matches-table tbody').append(answerRow);
                }

            }else if($(this).val() == 'text'){
                $(document).find('.ays-text-answers-desc').show();
                let answerRow = $('<tr class="ays-answer-row ui-state-default">'+
                    '<td>'+
                        '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                    '</td>'+
                    '<td>'+
                        '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                        '<textarea type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value"></textarea>'+
                    '</td>'+
                    '<td>'+
                        '<input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>'+
                    '</td>'+
                '</tr>');
                $(document).find('table#ays-answers-table tbody').addClass('text_answer');
                $(document).find('label[for="ays-answers-table"]').html('Answer');
                $('table#ays-answers-table tbody').html('');
                let answerHeadRowLast = $('<th class="th-350 reremoveable">Placeholder</th>');
                $('table#ays-answers-table thead tr th.removable').remove();
                $('table#ays-answers-table thead tr th.reremoveable').remove();
                $(document).find('table#ays-answers-table thead tr').append(answerHeadRowLast);
                $('table#ays-answers-table thead tr th:first-child').addClass('th-650');
                $(document).find('table#ays-answers-table tbody').append(answerRow);
            }else{
                $(document).find('.ays-answers-toolbar-bottom').show();
                if($(this).val() == 'select'){
                    $(document).find('.ays-answers-toolbar-bottom').find('.use_html').hide();
                }else{
                    $(document).find('.ays-answers-toolbar-bottom').find('.use_html').show();
                }

                var true_or_false_flag = false;
                if ( $(this).val() == 'true_or_false' ) {
                    true_or_false_flag = true;
                }

                if(
                    $(document).find('table#ays-answers-table tbody').hasClass('text_answer')
                    ||
                    $(document).find('table#ays-answers-table tbody').hasClass('matching_answer')
                    ||
                    true_or_false_flag
                ){
                    $(document).find('table#ays-answers-table tbody').removeClass('text_answer');
                    row_id = 1;
                    var addAnswer = $('<a href="javascript:void(0)" class="ays-add-answer">'+
                            '<i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                        '</a>'),
                        answerHeadRow = $('<th class="th-150 removable">Ordering</th>'+
                                '<th class="th-150 removable">Correct</th>'),
                        answerHeadKeywordRow = $('<th class="th-150 removable">Keyword</th>'),
                        answerHeadRowLast = $('<th class="th-150 removable">Image</th>'+
                                '<th class="th-150 removable">Delete</th>');
                    $(document).find('label[for="ays-answers-table"]').html('Answers');
                    $('table#ays-answers-table thead tr th.removable').remove();
                    $('table#ays-answers-table thead tr th.reremoveable').remove();
                    $(document).find('label[for="ays-answers-table"]').append(addAnswer);
                    $(document).find('table#ays-answers-table thead tr').prepend(answerHeadRow);
                    answerHeadKeywordRow.insertAfter('table#ays-answers-table thead .ays-weight-row');
                    $(document).find('table#ays-answers-table thead tr').append(answerHeadRowLast);
                    $(document).find('table#ays-answers-table tbody').html('');
                    var default_answer_count = $(document).find('table#ays-answers-table').attr('ays_default_count');
                    default_answer_count = parseInt(default_answer_count);
                    var simbolsArr = aysGenCharArray( "A", "F" );
                    var intervalsOptionHTML = '';
                    for (var i = 0; i < simbolsArr.length; i++) {
                        intervalsOptionHTML += '<option value="'+ simbolsArr[i] +'">'+ simbolsArr[i] +'</option>';
                    }

                    var true_or_false_arr = new Array(
                        quizLangObj.true,
                        quizLangObj.false
                    );

                    if ( true_or_false_flag ) {
                        default_answer_count = 2;
                    }

                    var loop_index = 0;

                    for(row_id = 1; row_id <= default_answer_count; row_id++){
                        var true_or_false_val = '';
                        if ( true_or_false_flag ) {
                            if (typeof true_or_false_arr[loop_index] !== 'undefined') {
                                true_or_false_val = true_or_false_arr[loop_index];
                            }
                            loop_index++;
                        }

                        var answerRow = '<tr class="ays-answer-row ui-state-default">'+
                            '<td class="ays-quiz-question-answer-ordering-row"><i class="ays_fa ays_fa_arrows" aria-hidden="true"></i></td>'+
                            '<td class="ays-quiz-question-answer-correct-row">'+
                                '<span>'+
                                    '<input type="radio" id="ays-correct-answer-'+row_id+'" class="ays-correct-answer" name="ays-correct-answer[]" value="'+row_id+'"/>'+
                                    '<label for="ays-correct-answer-'+row_id+'"></label>'+
                                '</span>'+
                            '</td>'+
                            '<td class="ays-quiz-question-answer-weight-point-row">'+
                                '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight" value="0"/>'+
                            '</td>'+
                            '<td class="ays-quiz-question-answer-keyword-row">'+
                                '<select name="ays_quiz_keywords[]" class="ays_quiz_keywords">'+
                                    intervalsOptionHTML +
                                '</select>'+
                            '</td>'+
                            '<td class="ays-quiz-question-answer-answer-row">'+
                                '<input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value="'+ true_or_false_val +'"/>'+
                            '</td>'+
                            '<td class="ays-quiz-question-answer-image-row">'+
                                '<label class="ays-label" for="ays-answer"><a href="javascript:void(0)" class="add-answer-image" style="display:block;">'+ quizLangObj.add +'</a></label>'+
                                '<div class="ays-answer-image-container ays-answer-image-container-div" style="display:none;">'+
                                    '<span class="ays-remove-answer-img"></span>'+
                                    '<img src="" class="ays-answer-img"/>'+
                                    '<input type="hidden" name="ays_answer_image[]" class="ays-answer-image-path" value=""/>'+
                                '</div>'+
                            '</td>'+
                            '<td class="ays-quiz-question-answer-delete-row">'+
                                '<a href="javascript:void(0)" class="ays-delete-answer">'+
                                   ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                                '</a>'+
                            '</td>'+
                        '</tr>';
                        $(document).find('table#ays-answers-table tbody').append(answerRow);
                    }
                }
            }
        });
        
        $(document).on('click', '.ays-add-answer', function () {
            var answer_row = $('.ays-answer-row'),
                row_count = answer_row.length,
                row_id = row_count + 1,
                cloned;

            var parent = $(document).find('table#ays-answers-table');
            // cloned = ((row_count % 2) === 0) ? answer_row.eq(0).clone() : answer_row.eq(1).clone();
            cloned = answer_row.eq(row_count-1).clone();

            cloned.find('input.ays-correct-answer').attr('id', 'ays-correct-answer-' + row_id);
            cloned.find('input.ays-correct-answer').val(row_id);
            cloned.find('input.ays-correct-answer').prop('checked', false);
            cloned.find('input.ays-correct-answer-value').val('');
            cloned.find('input.ays-answer-weight').val('0');
            cloned.find('.ays-answer-image-container').parent().html(' <label class=\'ays-label\' for=\'ays-answer\'><a href="javascript:void(0)" class="add-answer-image" style=display:block;>'+ quizLangObj.add +'</a></label>\n' +
                '<div class="ays-answer-image-container ays-answer-image-container-div" style=display:none; >\n' +
                '<span class="ays-remove-answer-img"></span>\n' +
                '<img src="" class="ays-answer-img" />\n' +
                '<input type="hidden" name="ays_answer_image[]" class="ays-answer-image-path" value=""/>\n' +
                '                                    </div>');
            cloned.find('label').attr('for', 'ays-correct-answer-' + row_id);

            cloned.appendTo('table#ays-answers-table tbody');

            $(document).find('table#ays-answers-table tr.ays-answer-row').each(function (index) {
                if ($(this).hasClass('even')) {
                    $(this).removeClass('even');
                }
                var className = ((index % 2) === 0) ? 'even' : '';
                $(this).addClass(className);
            });

            var lastAnswer = parent.find("input[name='ays-correct-answer-value[]']").last();
            lastAnswer.focus();
        });

        $(document).on('click', '.ays-add-fill-in-blank-answer', function () {
            var answer_row = $('.ays-answer-row'),
                row_count = answer_row.length,
                row_id = row_count + 1,
                cloned;

            var parent = $(document).find('table#ays-answers-table');
            var slugID = parent.find(".ays-answer-slug-max-id").val();

            if( slugID == "" ){
                slugID = 1;
            } else {
                slugID = parseInt(slugID) + 1;
            }

            var slugID_Value = "{blank_"+ slugID +"}";

            var answerRow = '<tr class="ays-answer-row ui-state-default">'+
                '<td class="ays-quiz-question-answer-weight-point-row">'+
                    '<input type="text" name="ays-answer-weight[]" class="ays-answer-weight w-100" value="0"/>'+
                '</td>'+
                '<td class="ays-quiz-question-answer-answer-row">'+
                    '<input style="display:none;" class="ays-correct-answer" type="checkbox" name="ays-correct-answer[]" value="1" checked/>'+
                    '<input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" value=""/>'+
                '</td>'+
                '<td class="ays-quiz-question-answer-placeholder-row">'+
                    '<input type="text" name="ays-answer-placeholder[]" class="ays-correct-answer-value" value=""/>'+
                '</td>'+
                '<td class="ays-quiz-question-answer-slug-row">'+
                    '<input type="text" name="ays_answer_slug[]" class="ays-answer-slug-value" value="'+ slugID_Value +'" readonly title="'+ quizLangObj.clickForCopy +'" />'+
                '</td>'+
                '<td class="ays-quiz-question-answer-delete-row">'+
                    '<a href="javascript:void(0)" class="ays-delete-fill-in-blank-answer">'+
                       ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                    '</a>'+
                '</td>'+
            '</tr>';

            $(document).find('table#ays-answers-table tbody').append(answerRow);

            $(document).find('table#ays-answers-table tr.ays-answer-row').each(function (index) {
                if ($(this).hasClass('even')) {
                    $(this).removeClass('even');
                }
                var className = ((index % 2) === 0) ? 'even' : '';
                $(this).addClass(className);
            });

            var lastAnswer = parent.find("input[name='ays-correct-answer-value[]']").last();
            lastAnswer.focus();

            parent.find(".ays-answer-slug-max-id").val(slugID);
        });

        $(document).on('click', '.ays-answer-slug-value', function () {
            var _this = $(this);

            var text = _this.val();
            _this.attr('title', quizLangObj.copied);

            // setTimeout(function(){
            //     _this.attr('title', 'Click for copy');
            // },1000);

            _this.select();
            document.execCommand('copy');
        });
        
        $(document).on('click', '.ays-delete-answer', function () {
            var index = 1;
            var rowCount = $('tr.ays-answer-row').length;
            if (rowCount > 2) {
                $(this).parent('td').parent('tr.ays-answer-row').remove();
                $(document).find('tr.ays-answer-row').each(function () {
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    var className = ((index % 2) === 0) ? 'even' : '';
                    $(this).addClass(className);
                    $(this).find('span.ays-radio').find('input').attr('id', 'ays-correct-answer-' + index);
                    $(this).find('span.ays-radio').find('input').val(index);
                    $(this).find('span.ays-radio').find('label').attr('for', 'ays-correct-answer-' + index);
                    index++;
                });
            } else {
                alert( quizLangObj.minimumCountAnswerShouldBe + " 2");
            }
        });

        $(document).on('click', '.ays-delete-fill-in-blank-answer', function () {
            var index = 1;
            var rowCount = $('tr.ays-answer-row').length;
            if (rowCount > 1) {
                $(this).parent('td').parent('tr.ays-answer-row').remove();
                $(document).find('tr.ays-answer-row').each(function () {
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    var className = ((index % 2) === 0) ? 'even' : '';
                    $(this).addClass(className);
                    $(this).find('span.ays-radio').find('input').attr('id', 'ays-correct-answer-' + index);
                    $(this).find('span.ays-radio').find('input').val(index);
                    $(this).find('span.ays-radio').find('label').attr('for', 'ays-correct-answer-' + index);
                    index++;
                });
            } else {
                alert( quizLangObj.minimumCountAnswerShouldBe + " 1");
            }
        });

        
        // Questions form submit
        // Checking the issues
//        $(document).find('#ays-question-form').on('submit', function(e){
//            let questionType = $(document).find('select[name="ays_question_type"]').val();
//            let answersTable = $(document).find('#ays-answers-table');
//            let status = true;
//            switch(questionType){
//                case "radio":
//                case "checkbox":
//                case "select":
//                    if(answersTable.find('tbody tr').length < 2){
//                        swal.fire({
//                            type: 'warning',
//                            text:'Sorry minimum count of answers should be 2'
//                        });
//                        status = false;
//                    }
//                break;
//                case "text":
//                    
//                break;
//            }
//            let correctAnswers = $(document).find('.ays-correct-answer:checked').length;
//            if(correctAnswers == 0){
//                swal.fire({
//                    type: 'warning',
//                    text: 'You must select at least one correct answer'
//                });
//                status = false;
//            }
//            if(status){
//                $(this)[0].submit();
//            }else{                
//                e.preventDefault();
//            }
//        });
        
        // Questions form submit
        // Checking the issues
        $(document).find('#ays-question-form').on('submit', function(e){
            var emptyQuestion = null;
            if ($("#wp-ays-question-wrap").hasClass("tmce-active")){
                emptyQuestion = tinyMCE.get('ays-question').getContent();
            }else{
                emptyQuestion = $('#ays-question').val();
            }
            var questionType = $(document).find('select[name="ays_question_type"]').val();
            var questionImage = $(document).find('#ays-question-image').val();
            var answersTable = $(document).find('#ays-answers-table');
            var status = true;
            switch(questionType){
                case "custom":
                case "radio":
                case "checkbox":
                case "select":
                    if(answersTable.find('tbody tr').length < 2){
                        swal.fire({
                            type: 'warning',
                            text: quizLangObj.minimumCountAnswerShouldBe + " 2"
                        });
                        status = false;
                    }
                    var answersValues = $(document).find('input.ays-correct-answer-value');
                    if(questionType != 'text' || questionType){
                        var countEmptyVals = 0;
                        answersValues.each(function(){
                            if($(this).val() == ''){
                                countEmptyVals++;
                            }
                        });
                        if (questionType != 'custom') {
                            //if((answersValues.length - countEmptyVals) <= 1){
                            if(countEmptyVals > 0){
                                swal.fire({
                                    type: 'warning',
                                    text: quizLangObj.sorryYouMustFillout
                                });
                                status = false;
                            }
                        }
                    }
                break;
                case "text":
                    // if(answersTable.find('textarea.ays-correct-answer-value').val().trim() == ''){
                    //     swal.fire({
                    //         type: 'warning',
                    //         text:'You must enter the answer'
                    //     });
                    //     status = false;
                    // }
                break;
            }
            if (questionType != 'custom') {
                if(emptyQuestion == null || emptyQuestion == ''){
                    if( questionImage == '' ){
                        swal.fire({
                            type: 'warning',
                            text: 'The question can\'t be empty.'
                        });
                        status = false;
                    }
                }
            }
            var correctAnswers = $(document).find('.ays-correct-answer:checked').length;
            var notInfluence = $(document).find('#ays_not_influence_to_score').prop('checked');
            
            if (questionType != 'custom' && questionType != 'matching' && !notInfluence) {
                if(correctAnswers == 0){
                    swal.fire({
                        type: 'warning',
                        text: quizLangObj.youMustSelectAtLeast
                    });
                    status = false;
                }
            }
            if(status){
                aysQuizFormSubmitted = true;
                $(this)[0].submit();
            }else{
                aysQuizFormSubmitted = false;
                e.preventDefault();
            }
        });

        $(document).on('click', '.ays-add-answer-incorrect-match', function (e){
            $(document).find('table#ays-answers-matches-table .ays-incorrect-match-row-empty').remove();
            var row_id = $(document).find('table#ays-answers-matches-table .ays-incorrect-match-row').length + 1;
            var answerRow = $('<tr class="ays-incorrect-match-row">' +
                '<td class="ays-quiz-question-answer-incorrect-match-row">' +
                    '<input type="text" name="ays-answer-incorrect-matches[]" class="ays-incorrect-answer-match-value" value="Incorrect match ' + row_id + '"/>' +
                '</td>' +
                '<td class="ays-quiz-question-answer-incorrect-match-delete-row">'+
                    '<a href="javascript:void(0)" class="ays-delete-answer-incorrect-match">'+
                        ' <i class="ays_fa ays_fa_minus_square" aria-hidden="true"></i>'+
                    '</a>'+
                '</td>'+
            '</tr>');
            $(document).find('table#ays-answers-matches-table tbody').append(answerRow);
        });
        $(document).on('click', '.ays-delete-answer-incorrect-match', function (e){
            var index = 1;
            $(this).parents('tr.ays-incorrect-match-row').remove();
            var rowCount = $('tr.ays-incorrect-match-row').length;
            if (rowCount > 0) {
                $(document).find('tr.ays-incorrect-match-row').each(function () {
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    var className = ((index % 2) === 0) ? 'even' : '';
                    $(this).addClass(className);
                    index++;
                });
            } else {
                var answerRow = $('<tr class="ays-incorrect-match-row ays-incorrect-match-row-empty">' +
                    '<td class="ays-quiz-question-answer-incorrect-match-row" colspan="2">' +
                        quizLangObj.noIncorrectMatches +
                    '</td>' +
                '</tr>');
                $(document).find('table#ays-answers-matches-table tbody').append(answerRow);
            }
        });

        $(document).on('click', 'a.add-question-image', function (e) {
            openMediaUploader(e, $(this));
        });
        $(document).on('click', 'a.add-question-bg-image', function (e) {
            openMediaUploaderQuestionBg(e, $(this));
        });
        $(document).on('click', '.ays-remove-question-img', function () {
            $(this).parent().find('img#ays-question-img').attr('src', '');
            $(this).parent().find('input#ays-question-image').val('');
            $(this).parent().fadeOut();
            $(document).find('.ays-field a.add-question-image').text(quizLangObj.addImage);
        });
        $(document).on('click', '.ays-remove-question-bg-img', function () {
            $(this).parent().fadeOut();
            $(this).parent().find('img#ays-question-bg-img').attr('src', '');
            $(this).parent().find('input#ays-question-bg-image').val('');
            $(document).find('a.add-question-bg-image').text(quizLangObj.addImage);
        });
        $(document).on('click', 'label.ays-label a.add-answer-image', function (e) {
            openAnswerMediaUploader(e, $(this));
        });
        $(document).on('click', '.ays-remove-answer-img', function () {
            $(this).parent().fadeOut();
            var ays_remove_answer_img = $(this);
            setTimeout(function(){
                ays_remove_answer_img.parents().eq(1).find('.add-answer-image').fadeIn();
                ays_remove_answer_img.parent().find('img.ays-answer-img').attr('src', '');
                ays_remove_answer_img.parent().find('input.ays-answer-image-path').eq(0).val('');
            },300);
        });
    });
})(jQuery);
