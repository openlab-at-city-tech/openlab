(function ($) {
    'use strict';
    $(document).ready(function () {
        $(document).find('.ays_quiz_back').hide();

        $(document).find('.ays_quiz_flash_card').on('click', function() {
            var aysQuizQuestion = $(this).find('.ays_quiz_front');
            var aysQuizAnswer   = $(this).find('.ays_quiz_back');

            if( aysQuizQuestion.hasClass('active')){
                $(this).css('transform','rotateY(180deg)');

                aysQuizQuestion.removeClass('active');
                aysQuizQuestion.hide();
                aysQuizAnswer.addClass('active');
                aysQuizAnswer.show();

            }else if(aysQuizAnswer.hasClass('active')){
                $(this).css('transform','rotateY(0deg)');

                aysQuizAnswer.removeClass('active');
                aysQuizAnswer.hide();
                aysQuizQuestion.addClass('active');
                aysQuizQuestion.show();
            }

        });

        $(document).find('.ays_quiz_fc_next_prev_btn.next').on('click',function(){
            var index = parseInt( $(this).parents('.ays_quiz_flash_card_content').attr('data-index') );
            index++;
            var id = $(this).parents('.ays_quiz_fc_next_btn_content').find('.quiz_id').val();
            var prevBtn = $(this).parents('.ays_quiz_fc_next_btn_content').find('.ays_quiz_fc_next_prev_btn.prev');

            prevBtn.css('display','block');

            var aysQuizFCElement = $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card');

            if(index > aysQuizFCElement.length-1){
                index = aysQuizFCElement.length-1;
            }

            if(index == aysQuizFCElement.length-1){
                $(this).parents('.ays_quiz_flash_card_content').find('a.ays_quiz_fc_next_prev_btn.next').css('display', 'none');
            }else{
                $(this).parents('.ays_quiz_flash_card_content').find('a.ays_quiz_fc_next_prev_btn.next').css('display', 'block');
            }

            $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card:visible').next('.ays_quiz_flash_card:hidden').css('display','block');
            $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card:visible').prev().css('display','none');

            $(this).parents('.ays_quiz_flash_card_content').attr('data-index', index);
        });

        $(document).find('.ays_quiz_fc_next_prev_btn.prev').on('click',function(){
            var index = parseInt( $(this).parents('.ays_quiz_flash_card_content').attr('data-index') );
            index--;
            var id = $(this).parent().find('.quiz_id').val();
            var prevBtn = $(this).find('.ays_quiz_flash_card_main_container a.prev');
            var nextBtn = $(this).next();

            prevBtn.css('display','block');
            var aysQuizFCElement = $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card');


            $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card:visible').prev('.ays_quiz_flash_card:hidden').css('display','block');
            $(this).parents('.ays_quiz_flash_card_main_container').find('.ays_quiz_flash_card_container_'+id+' > .ays_quiz_flash_card:visible').next().css('display','none');

            if(index <= 0){
                index = 0;
                $(this).css('display','none');
            }
            nextBtn.css('display','block');

            $(this).parents('.ays_quiz_flash_card_content').attr('data-index', index);

        });
    });

})(jQuery);
