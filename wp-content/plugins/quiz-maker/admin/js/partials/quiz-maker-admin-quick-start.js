(function($){
    $(document).ready(function(){
        
//        $(window).on('click', function(e){
//            if(!$(e.target).hasClass('.ays_modal_question')){
//                if($(e.target).parents('.ays_modal_question.active_question').length == 0){
//                    deactivate_questions();
//                }
//            }
//        });
        
        $(document).find('#ays_quick_start').on('click', function () {
            $('#ays-quick-modal').aysModal('show');
            var activate_first_question = $(document).find('#ays_question_id_1');
            if (! activate_first_question.hasClass('active_question')) {
                activate_first_question.addClass('active_question');
                activate_question(activate_first_question);
            }
        });
        
        $(document).on('click', '.ays_modal_question', function (e) {
            if (!$(this).hasClass('active_question')) {
                deactivate_questions();
                activate_question($(this));
            }
        });        
        
        $(document).find('#ays-quick-modal-content .ays-close').on('click', function () {
//            $(document).find('#ays-quick-modal-content').css('animation-name', 'zoomOut');
//            setTimeout(function(){
//                $(document).find('#ays-quick-modal').modal('hide');
//            }, 250);
//            deactivate_questions();
        });

        $(document).on('click', '.active_remove_answer', function () {
            var rowCount = $(this).parents('.ays_answers_table').find('.ays_answer_td').length;

            if (rowCount > 2) {
                var confirm = window.confirm( quizLangObj.deleteAnswer);
                if(confirm){
                    var item = $(this).parents().eq(0);
                    $(this).parents().eq(0).addClass('animated fadeOutLeft');
                    item.remove();
                }
            } else {
                swal.fire({
                    type: 'warning',
                    text: quizLangObj.minimumCountAnswerShouldBe + " 2"
                });
            }
//            var item = $(this).parents().eq(0);
//            $(this).parents().eq(0).addClass('animated fadeOutLeft');
//            setTimeout(function () {
//                item.remove();
//            }, 400);
        });

        $(document).on('click', '.ays_trash_icon', function () {
            if ($(document).find('.ays_modal_question').length == 1) {
                swal.fire({
                    type: 'warning',
                    text: quizLangObj.minimumCountQuestionShouldBe + " 1"
                });
                return false;
            }

            var confirm = window.confirm(quizLangObj.deleteQuestion);
            if (confirm) {
                var question_max_inp_id = $(document).find('#ays_quick_question_max_id');
                // var item = $(this).parent('.ays-modal-flexbox.flex-end').parent('.ays_modal_element.ays_modal_question');
                var items = $(this).parents('.ays-quick-questions-container').find('.ays_modal_element.ays_modal_question');
                var item = $(this).parents('.ays_modal_element.ays_modal_question');

                question_max_inp_id.val( items.length - 1 );
                item.addClass('animated fadeOutLeft');
                setTimeout(function () {
                    item.remove();
                }, 400);
            }

        });

        $(document).on('click', '.ays_modal_element.ays_modal_question', function() {
            if (! $(this).hasClass('active_question_border')) {
                $(document).find('#ays-quick-modal-content .ays_modal_element.ays_modal_question').removeClass('active_question_border');
                $(this).addClass('active_question_border');
            }
        });

        // Dublicate Question
        $(document).on('click','.ays_question_clone_icon', function (e) {
            var question_max_inp_id = $(document).find('#ays_quick_question_max_id');
            var question_max_id = parseInt(question_max_inp_id.val());
            if (isNaN(question_max_id)) {
                question_max_id = 1;
            }
            var ays_answer_radio_id = ( question_max_id + 1 );
            question_max_inp_id.val(ays_answer_radio_id);

            var cloningElement = $(this).parents('.ays_modal_element.ays_modal_question');
            var questionType = cloningElement.find('.ays_quick_question_type').val();
            var questionCat = cloningElement.find('.ays_quick_question_cat').val();
            var parentId = cloningElement.attr('id');

            $(document).find('#'+parentId+' .ays_answer_unique_id:checked').addClass('checkedElement');

            var cloneElem = cloningElement.clone( true, false );
            cloneElem.attr('id','ays_question_id_'+ays_answer_radio_id);


            cloneElem.find('.ays_question_input').select();

            cloneElem.find('.ays_quick_question_type option:selected').removeAttr('selected');
            cloneElem.find('.ays_quick_question_cat option:selected').removeAttr('selected');
            cloneElem.find('.ays_quick_question_type option:selected').prop('selected', false);
            cloneElem.find('.ays_quick_question_cat option:selected').prop('selected', false);

            cloneElem.find('.ays_quick_question_type option[value='+ questionType +']').attr('selected','selected');
            cloneElem.find('.ays_quick_question_cat option[value='+ questionCat +']').attr('selected','selected');
            cloneElem.find('.ays_quick_question_type option[value='+ questionType +']').prop('selected', true);
            cloneElem.find('.ays_quick_question_cat option[value='+ questionCat +']').prop('selected', true);

            cloneElem.find('.ays_answer_unique_id').attr('name', 'ays_answer_radio['+ays_answer_radio_id+']');

            var checkedRadio = cloneElem.find('.checkedElement:first-of-type');
            checkedRadio.attr('checked', 'checked');
            cloneElem.insertAfter('#'+parentId);
            setTimeout(function () {
                $(document).find('#ays-quick-modal-content .ays_modal_element.ays_modal_question').removeClass('active_question_border');
                var clonedElement = $(document).find('#ays_question_id_'+ays_answer_radio_id);
                clonedElement.addClass('active_question_border');
            },100);

        });

        // Change Question Type
        $(document).on('change', '.ays_quick_question_type', function (e) {
            var $this = $(this);
            var parent = $this.parents('.ays_modal_question');
            var parentID = parent.attr('id');
            var questionID = parent.attr('data-id');

            var questionType = $this.val();

            var answersTable    = parent.find('.ays_answers_table');
            var answerUniqueID  = answersTable.find('.ays_answer_unique_id');
            var textTypeTable   = parent.find('table.ays_quick_quiz_text_type_table');

            var question_max_inp_id = $(document).find('#ays_quick_question_max_id');
            var question_max_id = parseInt(question_max_inp_id.val());
            if (isNaN(question_max_id)) {
                question_max_id = 1;
            }
            var ays_answer_radio_id = question_max_id;

            switch (questionType) {
                case 'radio':
                    answerUniqueID.attr('type','radio');
                    break;
                case 'checkbox':
                    answerUniqueID.attr('type','checkbox');
                    break;
                case 'select':
                    answerUniqueID.attr('type','radio');
                    break;
                case 'text':
                    var textHTML = '<tr><td><input style="display:none;" class="ays-correct-answer ays_answer_unique_id" type="checkbox" name="ays_answer_radio['+ ays_answer_radio_id +']" value="1" checked/><textarea type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value" placeholder="'+ quizLangObj.answerText +'"></textarea></td></tr>';

                    var textTypeElementTd    = textTypeTable.find( 'tbody' );
                    var shortTextTypeElement = textTypeElementTd.find( 'input.ays-correct-answer-value.ays-text-question-type-value' );

                    if ( shortTextTypeElement.length > 0 ) {
                        shortTextTypeElement.remove();
                    }

                    textTypeElementTd.html( textHTML );

                    if ( textTypeTable.hasClass('display_none') ) {

                        textTypeTable.removeClass('display_none');
                    }

                    if ( ! answersTable.hasClass('display_none') ) {
                        answersTable.addClass('display_none')
                    }
                    break;
                case 'short_text':
                    var shortTextHTML = '<tr><td><input style="display:none;" class="ays-correct-answer ays_answer_unique_id" type="checkbox" name="ays_answer_radio['+ ays_answer_radio_id +']" value="1" checked/><input type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value ays-text-question-type-value" placeholder="'+ quizLangObj.answerText +'" value=""/></td></tr>';

                    var textTypeElementTd    = textTypeTable.find( 'tr td' );
                    var textTypeElement      = textTypeElementTd.find( 'textarea.ays-correct-answer-value.ays-text-question-type-value' );

                    if ( textTypeElement.length > 0 ) {
                        textTypeElement.remove();
                    }

                    textTypeElementTd.html( shortTextHTML );

                    if ( textTypeTable.hasClass('display_none') ) {
                        textTypeTable.removeClass('display_none');
                    }

                    if ( ! answersTable.hasClass('display_none') ) {
                        answersTable.addClass('display_none')
                    }
                    break;
                case 'number':
                    var numberHTML = '<tr><td><input style="display:none;" class="ays-correct-answer ays_answer_unique_id" type="checkbox" name="ays_answer_radio['+ ays_answer_radio_id +']" value="1" checked/><input type="number" name="ays-correct-answer-value[]" class="ays-correct-answer-value ays-text-question-type-value" placeholder="'+ quizLangObj.answerText +'" value=""/></td></tr>';

                    var textTypeElementTd    = textTypeTable.find( 'tr td' );
                    var textTypeElement      = textTypeElementTd.find( 'textarea.ays-correct-answer-value.ays-text-question-type-value' );

                    if ( textTypeElement.length > 0 ) {
                        textTypeElement.remove();
                    }

                    textTypeElementTd.html( numberHTML );

                    if ( textTypeTable.hasClass('display_none') ) {
                        textTypeTable.removeClass('display_none');
                    }

                    if ( ! answersTable.hasClass('display_none') ) {
                        answersTable.addClass('display_none')
                    }
                    break;
                case 'date':

                    var dateHTML = '<tr><td><input style="display:none;" class="ays-correct-answer ays_answer_unique_id" type="checkbox" name="ays_answer_radio['+ ays_answer_radio_id +']" value="1" checked/><input type="date" name="ays-correct-answer-value[]" class="ays-correct-answer-value ays-text-question-type-value" placeholder="'+ quizLangObj.currentTime +'" value=""/></td></tr>';

                    var textTypeElementTd    = textTypeTable.find( 'tbody' );
                    var textTypeElement      = textTypeElementTd.find( 'textarea.ays-correct-answer-value.ays-text-question-type-value' );

                    if ( textTypeElement.length > 0 ) {
                        textTypeElement.remove();
                    }

                    textTypeElementTd.html( dateHTML );

                    if ( textTypeTable.hasClass('display_none') ) {
                        textTypeTable.removeClass('display_none');
                    }

                    if ( ! answersTable.hasClass('display_none') ) {
                        answersTable.addClass('display_none')
                    }
                    break;
                case 'true_or_false':
                    var trueOrFalseHTML =
                    '<tr>'+
                    '    <td>'+
                    '        <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio['+ ays_answer_radio_id +']" checked>'+
                    '    </td>'+
                    '    <td class="ays_answer_td">'+
                    '        <p class="ays_answer">'+ quizLangObj.true +'</p>'+
                    '        <p>Answer</p>'+
                    '    </td>'+
                    '    <td class="show_remove_answer">'+
                    '        <i class="ays_fa ays_fa_times" aria-hidden="true"></i>'+
                    '    </td>'+
                    '</tr>'+
                    '<tr>'+
                    '    <td>'+
                    '        <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio['+ ays_answer_radio_id +']">'+
                    '    </td>'+
                    '    <td class="ays_answer_td">'+
                    '        <p class="ays_answer">'+ quizLangObj.false +'</p>'+
                    '        <p>Answer</p>'+
                    '    </td>'+
                    '    <td class="show_remove_answer">'+
                    '        <i class="ays_fa ays_fa_times" aria-hidden="true"></i>'+
                    '    </td>'+
                    '</tr>'+
                    '<tr class="ays_quiz_add_answer_box show_add_answer">'+
                    '    <td colspan="3">'+
                    '        <a href="javascript:void(0)" class="ays_add_answer">'+
                    '            <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                    '        </a>'+
                    '    </td>'+
                    '</tr>';

                    answersTable.find('tbody').html( trueOrFalseHTML );
                    activate_question(parent);

                    break;
                default:
                    answerUniqueID.attr('type','radio');
                    break;
            }

            if ( questionType != 'text' && questionType != 'short_text' && questionType != 'number' && questionType != 'date' ) {
                if ( answersTable.hasClass('display_none') ) {
                    answersTable.removeClass('display_none')
                }

                if ( ! textTypeTable.hasClass('display_none') ) {
                    textTypeTable.addClass('display_none');
                }
            }
        });

        $(document).on('click', '.ays_add_question', function () {
            var question_max_inp_id = $(document).find('#ays_quick_question_max_id');
            var question_max_id = parseInt(question_max_inp_id.val());
            if (isNaN(question_max_id)) {
                question_max_id = 1;
            }
            var ays_answer_radio_id = ( question_max_id + 1 );
            question_max_inp_id.val(ays_answer_radio_id);

            var ays_quiz_catObj = aysQuizCatObj.category;
            var appendAble =
                '<div class="ays_modal_element ays_modal_question active_question active_question_border" data-id="'+ays_answer_radio_id+'" id="ays_question_id_'+ays_answer_radio_id+'">'+
                '    <div class="form-group row">' +
                '        <div class="col-sm-8">' +
                '            <input type="text" value="'+quizLangObj.questionTitle+'" class="ays_question_input">' +
                '        </div>' +
                '        <div class="col-sm-4" style="text-align: right;">' +
                '            <select class="ays_quick_question_type" name="ays_quick_question_type[]" style="width: 200px;">' +
                '                <option value="radio">'+quizLangObj.radio+'</option>' +
                '                <option value="checkbox">'+quizLangObj.checkbox+'</option>' +
                '                <option value="select">'+quizLangObj.dropdawn+'</option>' +
                '                <option value="text">'+ quizLangObj.textType +'</option>'+
                '                <option value="short_text">'+ quizLangObj.shortTextType +'</option>'+
                '                <option value="number">'+ quizLangObj.number +'</option>'+
                '                <option value="true_or_false">'+ quizLangObj.trueOrFalse +'</option>'+
                '                <option value="date">'+ quizLangObj.date +'</option>'+
                '            </select>' +
                '        </div>' +
                '    </div>' +
                '    <div class="form-group row">' +
                '        <div class="col-sm-8"></div>' +
                '        <div class="col-sm-4" style="text-align: right;">' +
                '            <select class="ays_quick_question_cat" name="ays_quick_question_cat[]" style="width: 200px;">';
                            for(var k in ays_quiz_catObj ){
                                appendAble += '<option value="'+ays_quiz_catObj[k]['id']+'">'+ays_quiz_catObj[k]['title']+'</option>';
                            }
                appendAble += '</select>' +
                '        </div>' +
                '    </div>' +

                '    <div class="ays-modal-flexbox flex-end">' +
                '        <table class="ays_answers_table">' +
                '            <tr>' +
                '                <td>' +
                '                    <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio['+ays_answer_radio_id+']" checked>' +
                '                </td>' +
                '                <td class="ays_answer_td">' +
                '                    <p class="ays_answer"></p>' +
                '                    <p>Answer</p>' + +
                '                </td>' +
                '                <td class="show_remove_answer">' +
                '                    <i class="ays_fa ays_fa_times" aria-hidden="true"></i>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>' +
                '                    <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio['+ays_answer_radio_id+']">' +
                '                </td>' +
                '                <td class="ays_answer_td">' +
                '                    <p class="ays_answer"></p>' +
                '                    <p>Answer</p>' +
                '                </td>' +
                '                <td class="show_remove_answer">' +
                '                    <i class="ays_fa ays_fa_times" aria-hidden="true"></i>' +
                '                </td>' +
                '            </tr>' +
                '            <tr>' +
                '                <td>' +
                '                    <input class="ays_answer_unique_id" type="radio" name="ays_answer_radio['+ays_answer_radio_id+']">' +
                '                </td>' +
                '                <td class="ays_answer_td">' +
                '                    <p class="ays_answer"></p>' +
                '                    <p>Answer</p>' +
                '                </td>' +
                '                <td class="show_remove_answer">' +
                '                    <i class="ays_fa ays_fa_times" aria-hidden="true"></i>' +
                '                </td>' +
                '            </tr>' +
                '            <tr class="ays_quiz_add_answer_box show_add_answer">' +
                '                <td colspan="3">' +
                '                    <a href="javascript:void(0)" class="ays_add_answer">' +
                '                        <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>' +
                '                    </a>' +
                '                </td>' +
                '            </tr>' +
                '        </table>' +
                '        <table class="ays_quick_quiz_text_type_table display_none">'+
                '            <tr>'+
                '                <td>'+
                '                    <input style="display:none;" class="ays-correct-answer ays_answer_unique_id" type="checkbox" name="ays_answer_radio['+ ays_answer_radio_id +']" value="1" checked/>'+
                '                    <textarea type="text" name="ays-correct-answer-value[]" class="ays-correct-answer-value ays-text-question-type-value" placeholder="'+ quizLangObj.answerText +'"></textarea>'+
                '                </td>'+
                '            </tr>'+
                '        </table>'+
                '        <div class="ays-quick-quiz-icons-box">' +
                '            <a href="javascript:void(0)" class="ays_question_clone_icon">' +
                '                <i class="ays_fa ays_fa_clone" aria-hidden="true"></i>' +
                '            </a>' +
                '            <a href="javascript:void(0)" class="ays_trash_icon">' +
                '                <i class="ays_fa ays_fa_trash_o" aria-hidden="true"></i>' +
                '            </a>' +
                '        </div>' +
                '    </div>' +
                '</div>';
            $(document).find('.ays-quick-questions-container').append(appendAble);
            var question_conteiner = $(document).find('#ays_question_id_'+ ays_answer_radio_id);
            activate_question(question_conteiner);
        });


        $(document).on('click', '.ays_add_answer', function () {
            var question_id = $(document).find('.ays_modal_question').index($(this).parents('.ays_modal_question'));
            var parent = $(this).parents('.ays_modal_question');
            var questionType = parent.find('.ays_quick_question_type').val();
            var questType;
            switch (questionType) {
                case 'radio':
                    questType = 'radio';
                    break;
                case 'checkbox':
                    questType = 'checkbox';
                    break;
                case 'select':
                    questType = 'radio';
                    break;
                default:
                    questType = 'radio';
                    break;
            }

            $(this).parents().eq(1).before('<tr><td><input class="ays_answer_unique_id" type="'+ questType +'" name="ays_answer_radio[' + (++question_id) + ']"></td><td class="ays_answer_td"><input type="text" placeholder="'+ quizLangObj.emptyAnswer +'" class="ays_answer"></td><td class="active_remove_answer"><i class="ays_fa ays_fa_times" aria-hidden="true"></i></td></tr>');

            var tableTr = $(this).parents('.ays_answers_table').find('tr');
            var childLength = tableTr.length;
            var postPreviousChild = childLength - 2;
            tableTr.eq(postPreviousChild).find('.ays_answer').select();

//            $(this).parents().eq(1).before('<tr><td><input type="radio" name="ays_answer_radio[' + (++question_id) + ']"></td><td class="ays_answer_td"><input type="text" placeholder="Empty Answer" class="ays_answer"></td><td class="active_remove_answer"><i class="ays_fa ays_fa_times" aria-hidden="true"></i></td></tr>');
        });
        
        $(document).on("keydown" , "#ays_quick_popup .ays_answer" , function(e) {
            var $this = $(this);
            var $thisValue = $this.val();
            var parent = $this.parents('table.ays_answers_table');

            var lastAnswer = parent.find(".ays_answer").last();

            if ( lastAnswer.is(":focus") ) {
                if (e.keyCode === 13) {
                    e.preventDefault();

                    var addButton = parent.find(".ays_add_answer");
                    addButton.trigger("click");

                    var addedLastAnswer = parent.find(".ays_answer").last();
                    addedLastAnswer.focus();
                }
            } else {
                if (e.keyCode === 13) {
                    e.preventDefault();

                    var parentTr = $this.parents('tr');
                    var nextElement = parentTr.next().find(".ays_answer");
                    if (nextElement.length > 0) {
                        var nextElementVal = nextElement.val();
                        nextElement.val('');
                        nextElement.val( nextElementVal );

                        nextElement.focus();
                    }
                }
            }

            if(e.keyCode == 38 && !e.ctrlKey && !e.shiftKey ){
                var parentTr = $this.parents('tr');
                if( parentTr.prev().length > 0 ){
                    parentTr.prev().find(".ays_answer").trigger('focus');
                }else{
                    return false;
                }
            }

            if(e.keyCode === 40 && !e.ctrlKey && !e.shiftKey ){
                var parentTr = $this.parents('tr');
                var next_element = parentTr.next();

                if( ! next_element.hasClass('ays_quiz_add_answer_box') ){
                    parentTr.next().find(".ays_answer").trigger('focus');
                }else{

                    var addButton = parent.find(".ays_add_answer");
                    addButton.trigger("click");

                    var addedLastAnswer = parent.find(".ays_answer").last();
                    addedLastAnswer.focus();
                }
            }

            if(e.keyCode === 8  && $thisValue == ""){
                e.preventDefault();

                var deleteButton = $this.parents('tr').find(".active_remove_answer");
                var prevParentTr = $this.parents('tr').prev();

                deleteButton.trigger("click");

                var addedLastAnswer = prevParentTr.find(".ays_answer");
                var lastAnswerVal = addedLastAnswer.val();
                addedLastAnswer.val('');
                addedLastAnswer.val( lastAnswerVal );

                addedLastAnswer.focus();
            }
        });
        
    });
})(jQuery);
