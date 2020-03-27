jQuery(document).ready(function ($) {
    $(document).find('.ays_next.start_button').on('click', function(){
        setTimeout(function(){
            $(document).find('.ays_quiz_modern_dark section.ays_quiz_timer_container').find('hr').remove();

            $(document).find('.ays_quiz_modern_dark section.ays_quiz_timer_container').css({
                transition: '.5s ease-in-out',
            });
            if($(document).find('.ays_quiz_modern_dark .ays-quiz-timer').length > 0){
                $(document).find('.ays_quiz_modern_dark .ays_music_sound').css({
                    margin: "32px auto",
                });
                $(document).find('.ays_quiz_modern_dark .ays_question_hint').css({
                    'margin-top': '17px',
                });
                $(document).find('.ays_quiz_modern_dark .ays-question-counter').css({
                    'margin-top': '40px',
                });
            }
        }, 850);
    });
    $(document).find('.ays_quiz_modern_dark').find('input[name^="ays_questions"]').on('change', function (e) {
        if($(e.target).parents('.ays-quiz-container').hasClass('ays_quiz_modern_dark')){      
        if ($(e.target).parents().eq(4).hasClass('enable_correction')) {
            if ($(e.target).parents().eq(1).find('input[name="ays_answer_correct[]"]').length !== 0) {
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.length === 1) {
                    if (checked_inputs.prev().val() == 1) {
                        checked_inputs.next().addClass('correct');
                        checked_inputs.parent().addClass('correct_div');
                    } else {
                        checked_inputs.next().addClass('wrong');
                        checked_inputs.parent().addClass('wrong_div');
                    }
                    if (checked_inputs.attr('type') === "radio") {
                        if ($(this).prop('checked')) {
                            $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                            $(this).parents('.ays-field').addClass('checked_answer_div');
                        }
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                    }else if(checked_inputs.attr('type') === "checkbox"){
                        if ($(this).prop('checked')) {
                            $(this).parents('.ays-field').addClass('checked_answer_div');
                        }else{
                            $(this).parents('.ays-field').removeClass('checked_answer_div');
                        }
                        $(e.target).attr('disabled', true);
                        $(e.target).off('change');
                    }
                }else if (checked_inputs.length > 1){
                    for(var i = 0; i < checked_inputs.length; i++){
                        if ($(checked_inputs[i]).prev().val() == 1) {
                            $(checked_inputs[i]).next().addClass('correct');
                            $(checked_inputs[i]).next().css('background-color', "transparent");
                            $(checked_inputs[i]).parent().addClass('correct_div');
                        } else {
                            $(checked_inputs[i]).next().addClass('wrong');
                            $(checked_inputs[i]).next().css('background-color', "transparent");
                            $(checked_inputs[i]).parent().addClass('wrong_div');
                        }
                        if ($(checked_inputs[i]).attr('type') === "radio") {
                            if ($(this).prop('checked')) {
                                $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                                $(this).parent().addClass('checked_answer_div');
                            }
                            $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                            $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                        }else if($(checked_inputs[i]).attr('type') === "checkbox"){
                            if ($(this).prop('checked')) {
                                $(this).parents('.ays-field').addClass('checked_answer_div');
                            }else{
                                $(this).parents('.ays-field').removeClass('checked_answer_div');
                            }
                            $(e.target).attr('disabled', true);
                            $(e.target).off('change');
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                }

            }
        }else{
            if ($(e.target).parents().eq(1).find('input[name="ays_answer_correct[]"]').length !== 0) {
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.length === 1) {
                    if (checked_inputs.attr('type') === "radio") {
                        if ($(this).prop('checked')) {
                            $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                            $(this).parent().addClass('checked_answer_div');
                        }
                    }else if(checked_inputs.attr('type') === "checkbox"){
                        if ($(this).prop('checked')) {
                            $(this).parents('.ays-field').addClass('checked_answer_div');
                        }else{
                            $(this).parents('.ays-field').removeClass('checked_answer_div');
                        }
                    }
                }else if (checked_inputs.length > 1){
                    for(let i = 0; i < checked_inputs.length; i++){
                        if ($(checked_inputs[i]).attr('type') === "radio") {
                            if ($(this).prop('checked')) {
                                $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                                $(this).parent().addClass('checked_answer_div');
                            }
                        }else if($(checked_inputs[i]).attr('type') === "checkbox"){
                            if ($(this).prop('checked')) {                            
                                $(this).parents('.ays-field').addClass('checked_answer_div');
                            }else{
                                $(this).parents('.ays-field').removeClass('checked_answer_div');
                            }
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                }
            }
        }
        }
    });


});
