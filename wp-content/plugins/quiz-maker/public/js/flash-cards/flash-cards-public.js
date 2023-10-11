(function ($) {
    'use strict';
    $(document).ready(function () {
        $(document).find('.ays_quiz_back').hide();

        if($(document).find('.ays_quiz_flash_card_introduction').length) {
            var startBtn = $(document).find('.ays_quiz_fc_start_btn_content');
            var nextPrevBtns = $(document).find('.ays_quiz_fc_next_btn_content');
            var introduction = $(document).find('.ays_quiz_flash_card_introduction');

            var firstFlashContent = $(document).find(".ays_quiz_flash_card_content");
            firstFlashContent.each( function(){
                var _this = $(this);
                var firstFlashCard = _this.find('.ays_quiz_flash_card').eq(0);
                firstFlashCard.addClass("display_none");
            });

            if(nextPrevBtns.length) {
                nextPrevBtns.addClass("display_none");
            }

            startBtn.on('click', function() {
                var _this = $(this);
                var parent = _this.parents(".ays_quiz_flash_card_content");

                var nextPrevBtns   = parent.find('.ays_quiz_fc_next_btn_content');
                var firstFlashCard = parent.find('.ays_quiz_flash_card').eq(0);
                var introduction   = parent.find('.ays_quiz_flash_card_introduction');

                introduction.slideDown("1000");
                introduction.addClass("display_none");
                firstFlashCard.removeClass("display_none")

                _this.addClass("display_none");
                if(nextPrevBtns.length) {
                    nextPrevBtns.removeClass("display_none");
                }
            })
        }

        $(document).find('.ays_quiz_flash_card').on('click', function() {
            var _this = $(this);

            var aysQuizQuestion = _this.find('.ays_quiz_front');
            var aysQuizAnswer   = _this.find('.ays_quiz_back');
            var aysQuizPage     = _this.find('.ays_quiz_current_page');
            
            if( aysQuizQuestion.hasClass('active')){
                _this.css('transform','rotateY(179deg)');
                _this.css('justify-content','flex-start');

                aysQuizQuestion.removeClass('active');
                aysQuizQuestion.hide();
                aysQuizAnswer.addClass('active');
                aysQuizPage.addClass('rotated');
                setTimeout( function(){
                    aysQuizAnswer.show();
                }, 250);

            }else if(aysQuizAnswer.hasClass('active')){
                _this.css('transform','rotateY(0deg)');
                _this.css('justify-content','flex-end');

                aysQuizAnswer.removeClass('active');
                aysQuizAnswer.hide();
                aysQuizQuestion.addClass('active');
                aysQuizPage.removeClass('rotated');
                setTimeout( function(){
                    aysQuizQuestion.show(); 
                }, 250);
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
