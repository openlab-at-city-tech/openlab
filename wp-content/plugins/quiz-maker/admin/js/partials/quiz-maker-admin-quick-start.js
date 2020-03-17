(function($){
    $(document).ready(function(){
        
        $(window).on('click', function(e){
            if(!$(e.target).hasClass('.ays_modal_question')){
                if($(e.target).parents('.ays_modal_question.active_question').length == 0){
                    deactivate_questions();
                }
            }
        });
        
        $(document).find('#ays_quick_start').on('click', function () {
            $('#ays-quick-modal').aysModal('show');
        });
        
        $(document).find('.ays_modal_question').live('click', function (e) {
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
            deactivate_questions();
        });

        $(document).find('.active_remove_answer').live('click', function () {
            if($(this).parents('.ays_answers_table').find('.ays_answer_td').length == 2){
                swal.fire({
                    type: 'warning',
                    text:'Sorry minimum count of answers should be 2'
                });
                return false;
            }
            var item = $(this).parents().eq(0);
            $(this).parents().eq(0).addClass('animated fadeOutLeft');
            setTimeout(function () {
                item.remove();
            }, 400);
        });

        $(document).find('.ays_trash_icon').live('click', function () {
            if ($(document).find('.ays_modal_question').length == 1) {
                swal.fire({
                    type: 'warning',
                    text:'Sorry minimum count of questions should be 1'
                });
                return false;
            }
            var item = $(this).parent('.ays-modal-flexbox.flex-end').parent('.ays_modal_element.ays_modal_question');
            item.addClass('animated fadeOutLeft');
            setTimeout(function () {
                item.remove();
            }, 400);

        });

        $(document).find('.ays_add_question').live('click', function () {
            var ays_answer_radio_id = ++$('.ays_modal_question').length;
            var appendAble = '<div class="ays_modal_element ays_modal_question">'+
                    '<div class="ays_question_overlay"></div>'+
                    '<p class="ays_question">Question Default Title</p>'+
                    '<div class="ays-modal-flexbox flex-end">'+
                        '<table class="ays_answers_table">'+
                            '<tr>'+
                                '<td>'+
                                    '<input type="radio" name="ays_answer_radio[${ays_answer_radio_id}]" checked>'+
                                '</td>'+
                                '<td class="ays_answer_td">'+
                                    '<p class="ays_answer"></p>'+
                                    '<p>Answer</p>'+
                                '</td>'+
                                '<td class="show_remove_answer">'+
                                    '<i class="ays_fa ays_fa_times" aria-hidden="true"></i>'+
                                '</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td>'+
                                    '<input type="radio" name="ays_answer_radio[${ays_answer_radio_id}]">'+
                                '</td>'+
                                '<td class="ays_answer_td">'+
                                    '<p class="ays_answer"></p>'+
                                    '<p>Answer</p>'+
                                '</td>'+
                                '<td class="show_remove_answer">'+
                                    '<i class="ays_fa ays_fa_times" aria-hidden="true"></i>'+
                                '</td>'+
                            '</tr>'+
                            '<tr>'+
                                '<td>'+
                                    '<input type="radio" name="ays_answer_radio[${ays_answer_radio_id}]">'+
                                '</td>'+
                                '<td class="ays_answer_td">'+
                                    '<p class="ays_answer"></p>'+
                                    '<p>Answer</p>'+
                                '</td>'+
                                '<td class="show_remove_answer">'+
                                    '<i class="ays_fa ays_fa_times" aria-hidden="true"></i>'+
                                '</td>'+
                            '</tr>'+
                            '<tr class="show_add_answer">'+
                                '<td colspan="3">'+
                                    '<a href="javascript:void(0)" class="ays_add_answer">'+
                                        '<i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                                    '</a>'+
                                '</td>'+
                            '</tr>'+
                        '</table>'+
                        '<a href="javascript:void(0)" class="ays_trash_icon">'+
                            '<i class="ays_fa ays_fa_trash_o" aria-hidden="true"></i>'+
                        '</a>'+
                    '</div>'+
                '</div>';
            $(document).find('.ays-quick-questions-container').append(appendAble);
        });

        $(document).find('.ays_add_answer').live('click', function () {
            var question_id = $(document).find('.ays_modal_question').index($(this).parents('.ays_modal_question'));
            $(this).parents().eq(1).before('<tr><td><input type="radio" name="ays_answer_radio[' + (++question_id) + ']"></td><td class="ays_answer_td"><input type="text" placeholder="Empty Answer" class="ays_answer"></td><td class="active_remove_answer"><i class="ays_fa ays_fa_times" aria-hidden="true"></i></td></tr>');
        });
        
        
        
    });
})(jQuery);