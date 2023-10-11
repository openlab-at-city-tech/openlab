(function ($) {
    'use strict';
    $(document).ready(function () {
        $.fn.goTo = function(myOptions) {
            var QuizAnimationTop = (myOptions.quiz_animation_top && myOptions.quiz_animation_top != 0) ? parseInt(myOptions.quiz_animation_top) : 100;

            myOptions.quiz_enable_animation_top = myOptions.quiz_enable_animation_top ? myOptions.quiz_enable_animation_top : 'on';
            var EnableQuizAnimationTop = ( myOptions.quiz_enable_animation_top && myOptions.quiz_enable_animation_top == 'on' ) ? true : false;
            if( EnableQuizAnimationTop ){
                $('html, body').animate({
                    scrollTop: $(this).offset().top - QuizAnimationTop + 'px'
                }, 'slow');
            }
            return this; // for chaining...
        }
        if (!String.prototype.trim) {
            (function() {
                String.prototype.trim = function() {
                    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                };
            })();
        }

        $(document).find('.for_quiz_rate_avg.ui.rating').rating('disable');

        var ays_quiz_container, ays_quiz_container_id, quizId; //flag to prevent quick multi-click glitches
        var myOptions, myQuizOptions, explanationTimeout;
        var emailValivatePattern = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.\w{2,}$/;
        var aysThisQuizBullets = null;
        var aysThisQuizQuestionsFirstPass = true;
        var AYS_MS_PER_SECONDS = 1000;
        var aysDurationInSeconds = 2;
        if( typeof window.aysQuizBullets == 'undefined' ){
            window.aysQuizBullets = [];
        }

        if( typeof window.aysQuizQuestionTimers == 'undefined' ){
            window.aysQuizQuestionTimers = [];
        }

        if( typeof window.aysSeeResultConfirmBox == 'undefined' ){
            window.aysSeeResultConfirmBox = [];
        }

        if( typeof window.aysEarlyFinishConfirmBox == 'undefined' ){
            window.aysEarlyFinishConfirmBox = [];
        }


        window.aysTimerInterval = null;
        window.countdownTimeForShowInterval = null;
        window.aysQuizStartDate = null;
        window.voiceAction = false;

        function aysQuizResizeIframe(iframe) {
            iframe.height = iframe.contentWindow.document.body.scrollHeight + 40;
            window.requestAnimationFrame(function(){ aysQuizResizeIframe(iframe) });
        }

        $(document).find('.ays-quiz-container[data-is-embed="1"]').each(function () {
            if (Number(this.dataset.isAmp) === 0 && Number(this.dataset.isEmbed) === 1) {
                // var iframes = window.parent.document.querySelectorAll('iframe[id^="aysQuizIframe"]');
                // if( iframes ) {
                //     for (var i = 0; i < iframes.length; i++) {
                //         aysQuizResizeIframe(iframes[i]);
                //     }
                // }
            }
        });

        if( $(document).find('.ays-quiz-container[data-is-amp="1"]').length > 0 ) {
            aysResizeiFrame();
        }

        function time_limit(e) {
            quizId = $(e.target).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            myOptions = JSON.parse(atob(window.aysQuizOptions[quizId]));

            if(checkQuizPassword(e, myOptions, false) === false){
                return false;
            }

            if(typeof myOptions.answers_rw_texts == 'undefined'){
                myOptions.answers_rw_texts = 'on_passing';
            }
            if(typeof myOptions.make_questions_required == 'undefined'){
                myOptions.make_questions_required = 'off';
            }
            var quizOptionsName = 'quizOptions_'+quizId;
            myQuizOptions = [];
            if(typeof window[quizOptionsName] !== 'undefined'){
                for(var i in window[quizOptionsName]){
                    if(window[quizOptionsName].hasOwnProperty(i)){
                        myQuizOptions[i] = (JSON.parse(window.atob(window[quizOptionsName][i])));
                    }
                }
            }
            
            if(typeof window.aysSeeResultConfirmBox !== 'undefined'){
                window.aysSeeResultConfirmBox[ quizId ] = false;
            }

            if(typeof window.aysEarlyFinishConfirmBox !== 'undefined'){
                window.aysEarlyFinishConfirmBox[ quizId ] = false;
            }

            var container = $(e.target).parents('.ays-quiz-container');
            if ($(this).parents('.step').next().find('.information_form').length === 0 ){
                var quizMusic = container.find('audio.ays_quiz_music');
                if(quizMusic.length !== 0){                
                    var soundEls = $(document).find('.ays_music_sound');
                    container.find('.ays_music_sound').removeClass('ays_display_none');
                    if(!isPlaying(quizMusic.get(0))){
                        container.find('audio.ays_quiz_music')[0].play();
                        audioVolumeIn($(e.target).parents('.ays-quiz-container').find('audio.ays_quiz_music')[0]);
                    }
                }
                container.find('.ays-live-bar-wrap').css({'display': 'block'});
                container.find('.ays-live-bar-percent').css({'display': 'inline-block'});
                container.find('input.ays-start-date').val(GetFullDateTime());
                window.aysQuizStartDate = GetFullDateTime();
            }
            if ($(this).parents('.step').next().find('.information_form').length === 0 && myOptions.enable_timer == 'on') {
                var questionCountPerPage = myOptions.question_count_per_page && myOptions.question_count_per_page === 'on';
                var displayALlQuestion = myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions === 'on';
                if( displayALlQuestion ){
                    myOptions.quiz_timer_type = 'quiz_timer';
                } else if ( questionCountPerPage ) {
                    myOptions.quiz_timer_type = 'quiz_timer';
                }

                var timerType = myOptions.quiz_timer_type ? myOptions.quiz_timer_type : 'quiz_timer';
                if( timerType === 'quiz_timer' ){
                    quizTimer( container );
                }else if( timerType === 'question_timer' ){
                    if( myOptions.quiz_waiting_time && myOptions.quiz_waiting_time === 'on' ){
                        myOptions.quiz_waiting_time = 'off';
                    }
                    questionTimerInit( container );
                }
            }else{
                $(this).parents('.step').next().find('.information_form').find('.ays_next.action-button').on('click', function () {
                    if($(this).parents('.step').find('.information_form').find('.ays_next.action-button').hasClass('ays_start_allow')){
                        time_limit(e);                        
                    }
                });
            }
        }
            
        $(document).on('click', '.ays-quiz-question-title-text-to-speech-icon', function(){
            window.voiceAction = !window.voiceAction;
            var questionText = atob($(this).attr('data-question'));
            if ('speechSynthesis' in window) {
                
                var voices = quizGetVoices();
                var rate = 1, pitch = 1, volume = 1;
                if(window.voiceAction){
                    listenQuestionText(questionText, voices[0], rate, pitch, volume, 'play' );
                }
                else{
                    listenQuestionText(questionText, voices[0], rate, pitch, volume, 'cancel' );                        
                }
            }else{
                console.log('Speech Synthesis Not Supported');
            }
        });
        
        $(document).find('.ays_next.start_button').on('click', time_limit);
        
        $(document).find('.ays_next.start_button').on('click', function(e){

            if (typeof $(this).attr("data-enable-leave-page") !== 'undefined') {
                $(this).attr("data-enable-leave-page",true);
            }

            if(checkQuizPassword(e, myOptions, false) === false){
                return false;
            }

            ays_quiz_container_id = $(this).parents(".ays-quiz-container").attr("id");
            ays_quiz_container = $('#'+ays_quiz_container_id);
            
            aysResetQuiz( ays_quiz_container );

            ays_quiz_container.css('padding-bottom', '0px');
            var ancnoxneriQanak = $(this).parents('.ays-questions-container').find('.ays_quizn_ancnoxneri_qanak');
            var aysQuizReteAvg = $(this).parents('.ays-questions-container').find('.ays_quiz_rete_avg');
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();

            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            if(isRequiredQuestion === true && !( parseInt( next_sibilings_count ) > 0 ) ){
                ays_quiz_container.find('div[data-question-id]').each(function(){
                    var thisStep = $(this);
                    if(!thisStep.find('input.ays_next').hasClass('ays_display_none')){
                        thisStep.find('input.ays_next').attr('disabled', 'disabled');
                    }else if(!thisStep.find('i.ays_next_arrow').hasClass('ays_display_none')){
                        thisStep.find('i.ays_next_arrow').attr('disabled', 'disabled');
                    }

                    if(!thisStep.find('input.ays_early_finish').hasClass('ays_display_none')){
                        thisStep.find('input.ays_early_finish').attr('disabled', 'disabled');
                    }else if(!thisStep.find('i.ays_early_finish').hasClass('ays_display_none')){
                        thisStep.find('i.ays_early_finish').attr('disabled', 'disabled');
                    }
                });
            }

            if( ays_quiz_container.find('.enable_min_selection_number').length > 0 ){
                ays_quiz_container.find('.enable_min_selection_number').each(function(){
                    var thisStep = $(this).parents('.step');
                    thisStep.find('input.ays_next').attr('disabled', 'disabled');
                    thisStep.find('i.ays_next_arrow').attr('disabled', 'disabled');

                    thisStep.find('input.ays_early_finish').attr('disabled', 'disabled');
                    thisStep.find('i.ays_early_finish').attr('disabled', 'disabled');
                });
            }

            ays_quiz_container.find('div[data-question-id][data-type="custom"]').each(function(){
                $(this).find('i.ays_clear_answer').addClass('ays_display_none');
                $(this).find('input.ays_clear_answer').addClass('ays_display_none');
            });

            setTimeout(function(){
                ays_quiz_container.css('border-radius', myOptions.quiz_border_radius + 'px');
                ays_quiz_container.find('.step').css('border-radius', myOptions.quiz_border_radius + 'px');
            }, 400);
            
            aysAnimateStep(ays_quiz_container.data('questEffect'), aysQuizReteAvg);
            aysAnimateStep(ays_quiz_container.data('questEffect'), ancnoxneriQanak);
            
            if ($(this).parents('.step').next().find('.information_form').length === 0 ){

                $(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideDown(500);
                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
                if(questions_count <= parseInt(next_sibilings_count)){
                    next_sibilings_count = questions_count;
                }
                if(parseInt(next_sibilings_count) > 0 &&
                   ($(this).parents('.step').attr('data-question-id') || 
                    $(this).parents('.step').next().attr('data-question-id'))){
                    var final_width = ((parseInt(next_sibilings_count)) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(parseInt(next_sibilings_count)));
                    }else{                
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }else{
                    var final_width = ((curent_number+1) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                    }else{                
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }
            }

            if ( ays_quiz_container.hasClass('ays_quiz_hide_bg_on_start_page') ) {
                ays_quiz_container.removeClass('ays_quiz_hide_bg_on_start_page');
            }

            aysQuizBullets[quizId] = createPagination($(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap'), 1);
            aysThisQuizBullets = aysQuizBullets[quizId];
            aysThisQuizBullets._navPage(0);

        });

        $(document).on('focus', '.ays-quiz-container input', function () {
            $(window).on('keydown', function (event) {
                if (event.keyCode === 13) {
                    return false;
                }
            });
        });

        $(document).on('blur', '.ays-quiz-container input', function () {
            $(window).off('keydown');
        });
        
        $.each($(document).find('.ays_block_content'), function () {
            if ($(document).find('.ays_block_content').length != 0) {
                var ays_block_element = $(this).parents().eq(2);
                ays_block_element.find('input.ays-start-date').val(GetFullDateTime());
                ays_block_element.find('div.ays-quiz-timer').slideUp(500);
                var timer = parseInt(ays_block_element.find('div.ays-quiz-timer').attr('data-timer'));
                var timerInTitle = ays_block_element.find('div.ays-quiz-timer').data('showInTitle');
                var tabTitle = document.title;
                setTimeout(function(){
                if (timer !== NaN) {
                    timer += 2;
                    if (timer !== undefined) {
                        var countDownDate = new Date().getTime() + (timer * 1000);
                        var x = setInterval(function () {
                            var now = new Date().getTime();
                            var distance = countDownDate - Math.ceil(now/1000)*1000;
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            var timeForShow = "";
                            if(hours <= 0){
                                hours = null;
                            }else if (hours < 10) {
                                hours = '0' + hours;
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            if (seconds < 10) {
                                seconds = '0' + seconds;
                            }
                            timeForShow =  ((hours==null)? "" : (hours + ":")) + minutes + ":" + seconds;
                            if(distance <=1000){
                                timeForShow = ((hours==null) ? "" : "00:") + "00:00";
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                if(timerInTitle){
                                    document.title = timeForShow + " - " + tabTitle ;
                                }
                            }else{
                                ays_block_element.find('div.ays-quiz-timer').html(timeForShow);
                                if(timerInTitle){
                                    document.title = timeForShow + " - " + tabTitle ;
                                }
                            }
                            ays_block_element.find('div.ays-quiz-timer').slideDown(500);
                            var ays_block_element_redirect_url = ays_block_element.find('.ays_redirect_url').text();
                            if (distance <= 1) {
                                clearInterval(x);
                                var totalSteps = ays_block_element.find('div.step').length;
                                var currentStep = ays_block_element.eq(2).find('div.step.active-step');
                                var currentStepIndex = ays_block_element.eq(2).find('div.step.active-step').index();
                                if (currentStep.hasClass('ays_thank_you_fs') === false) {
                                    var steps = totalSteps - 3;
                                    ays_block_element.find('div.step').each(function (index) {
                                        if (index >= (currentStepIndex - 1) && index <= steps) {
                                            $(this).remove();
                                        }
                                    });
                                    // window.location = ays_block_element_redirect_url;
                                    if (window.location != window.parent.location) {
                                        parent.location = ays_block_element_redirect_url;
                                    } else {
                                        window.location = ays_block_element_redirect_url;
                                    }
                                }
                            }
                        }, 1000);
                    }
                }
                }, 1000);
            }
        });
        
        $(document).find('button.ays_check_answer').on('click', function (e) {
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            var thisAnswerOptions;
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
            var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
            var questionId = $(this).parents('.step').data('questionId');
            var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
            var showOnlyWrongAnswer = (myOptions.show_only_wrong_answer && myOptions.show_only_wrong_answer == "on") ? true : false;
            var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
            var next_sibilings_count = quizContainer.find('.ays_question_count_per_page').val();
            var enableNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == 'on') ? true : false;
            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            var disableQuestions = false;
            if( parseInt( next_sibilings_count ) > 0 || quiz_display_all_questions == true ){
                disableQuestions = true;
            }

            var ans_right_wrong_icon = (myOptions.ans_right_wrong_icon && myOptions.ans_right_wrong_icon != "") ? myOptions.ans_right_wrong_icon : "default";

            var correct_img_URL = "";
            var wrong_img_URL   = "";
            if( ans_right_wrong_icon == "default"){
                correct_img_URL = quizLangObj.AYS_QUIZ_PUBLIC_URL + "/images/correct.png";
                wrong_img_URL   = quizLangObj.AYS_QUIZ_PUBLIC_URL + "/images/wrong.png";
            } else if( ans_right_wrong_icon == "none" ){
                correct_img_URL = "";
                wrong_img_URL   = "";
            } else {
                correct_img_URL = quizLangObj.AYS_QUIZ_PUBLIC_URL + "/images/correct-"+ ans_right_wrong_icon +".png";
                wrong_img_URL   = quizLangObj.AYS_QUIZ_PUBLIC_URL + "/images/wrong-"+ ans_right_wrong_icon +".png";
            }

            var correct_img_URL_HTML = "";
            var wrong_img_URL_HTML   = "";
            if (correct_img_URL != "") {
                correct_img_URL_HTML = "<img class='ays-quiz-check-button-right-wrong-icon' data-type='"+ans_right_wrong_icon+"' src='"+ correct_img_URL +"'>"
            }

            if (wrong_img_URL != "") {
                wrong_img_URL_HTML = "<img class='ays-quiz-check-button-right-wrong-icon' data-type='"+ ans_right_wrong_icon +"' src='"+ wrong_img_URL +"'>"
            }

            thisAnswerOptions = myQuizOptions[questionId];
            if($(this).parent().find('.ays-text-input').val() !== ""){
                if ($(e.target).parents('form[id^="ays_finish_quiz"]').hasClass('enable_correction')) {
                    if($(e.target).parents('.step').hasClass('not_influence_to_score')){
                        return false;
                    }
                    $(this).css({
                        animation: "bounceOut .5s",
                    });
                    setTimeout(function(){
                        $(e.target).parent().find('.ays-text-input').css('width', '100%');
                        $(e.target).css("display", "none");
                    },480);
                    $(e.target).parent().find('.ays-text-input').css('background-color', '#eee');
                    $(this).parent().find('.ays-text-input').attr('disabled', 'disabled');
                    $(this).attr('disabled', 'disabled');
                    $(this).off('change');
                    $(this).off('click');
                    $(this).parents('.ays-field').addClass('ays-answered-text-input');

                    var input = $(this).parent().find('.ays-text-input');
                    var type = input.attr('type');
                    var userAnsweredText = input.val().trim();

                    // Enable case sensitive text
                    var enable_case_sensitive_text = (thisAnswerOptions.enable_case_sensitive_text && thisAnswerOptions.enable_case_sensitive_text != "") ? thisAnswerOptions.enable_case_sensitive_text : false;

                    var trueAnswered = false;
                    var thisQuestionAnswer = aysEscapeHtmlDecode( thisAnswerOptions.question_answer ).aysStripSlashes().toLowerCase();
                    var displayingQuestionAnswer = thisAnswerOptions.question_answer;

                    if( type == 'text' || type == 'short_text' ){
                        if ( enable_case_sensitive_text ) {
                            thisQuestionAnswer = aysEscapeHtmlDecode( thisAnswerOptions.question_answer ).aysStripSlashes();
                        }
                    }

                    if(type == 'date'){
                        var correctDate = new Date(thisAnswerOptions.question_answer),
                            correctDateYear = correctDate.getFullYear(),
                            correctDateMonth = correctDate.getMonth(),
                            correctDateDay = correctDate.getDate();
                        var userDate = new Date(userAnsweredText),
                            userDateYear = userDate.getFullYear(),
                            userDateMonth = userDate.getMonth(),
                            userDateDay = userDate.getDate();
                        if(correctDateYear == userDateYear && correctDateMonth == userDateMonth && correctDateDay == userDateDay){
                            trueAnswered = true;
                        }
                    }else if(type != 'number'){
                        thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        displayingQuestionAnswer = displayingQuestionAnswer.split('%%%');
                        for(var i = 0; i < thisQuestionAnswer.length; i++){
                            if ( enable_case_sensitive_text ) {
                                if(userAnsweredText == thisQuestionAnswer[i].trim()){
                                    trueAnswered = true;
                                    break;
                                }
                            } else {
                                if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i].trim()){
                                    trueAnswered = true;
                                    break;
                                }
                            }
                        }
                    }else{
                        if(type == 'number'){
                            if(userAnsweredText.toLowerCase().replace(/\.([^0]+)0+$/,".$1") == thisQuestionAnswer.trim().replace(/\.([^0]+)0+$/,".$1")){
                                trueAnswered = true;
                            }
                        } else {
                            if(userAnsweredText.toLowerCase() == thisQuestionAnswer.trim()){
                                trueAnswered = true;
                            }
                        }
                    }

                    if(trueAnswered){
                        if((right_answer_sound)){
                            resetPlaying([right_answer_sound, wrong_answer_sound]);
                            setTimeout(function(){
                                right_answer_sound.play();
                            }, 10);
                        }
                        $(this).parent().find('.ays-text-input').css('background-color', 'rgba(39,174,96,0.5)');
                        $(this).parent().find('input[name="ays_answer_correct[]"]').val(1);

                        if (correct_img_URL_HTML != "") {
                            $(this).parent().append(correct_img_URL_HTML);
                        }

                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.right_answer_text').slideDown(250);
                        }

                        if( aysThisQuizBullets !== null ){
                            var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                            var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                            thisBullet.attr('disabled', 'disabled');
                            thisBullet.addClass('ays_quiz_correct_answer');
                            if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                            }
                        }
                    }else{
                        if((wrong_answer_sound)){
                            resetPlaying([right_answer_sound, wrong_answer_sound]);
                            setTimeout(function(){
                                wrong_answer_sound.play();
                            }, 10);
                        }
                        $(this).parent().find('.ays-text-input').css('background-color', 'rgba(243,134,129,0.8)');
                        $(this).parent().find('input[name="ays_answer_correct[]"]').val(0);

                        if (wrong_img_URL_HTML != "") {
                            $(this).parent().append(wrong_img_URL_HTML);
                        }

                        if( aysThisQuizBullets !== null ){
                            var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                            var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                            thisBullet.attr('disabled', 'disabled');
                            thisBullet.addClass('ays_quiz_wrong_answer');
                            if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                            }
                        }
                        if(showOnlyWrongAnswer === false){
                            var rightAnswerText = '<div class="ays-text-right-answer">';

                            if(type == 'date'){
                                var correctDate = new Date(thisAnswerOptions.question_answer),
                                    correctDateYear = correctDate.getUTCFullYear(),
                                    correctDateMonth = (correctDate.getUTCMonth() + 1) < 10 ? "0"+(correctDate.getUTCMonth() + 1) : (correctDate.getUTCMonth() + 1),
                                    correctDateDay = (correctDate.getUTCDate() < 10) ? "0"+correctDate.getUTCDate() : correctDate.getUTCDate();
                                rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');
                            }else if(type != 'number'){
                                // rightAnswerText += thisQuestionAnswer[0];
                                rightAnswerText += displayingQuestionAnswer[0];
                            }else{
                                // rightAnswerText += thisQuestionAnswer;
                                rightAnswerText += displayingQuestionAnswer;
                            }

                            rightAnswerText += '</div>';
                            $(this).parents('.ays-quiz-answers').append(rightAnswerText);
                            $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                        }

                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.wrong_answer_text').slideDown(250);
                        }

                        if(finishAfterWrongAnswer){
                            $(this).parents('div[data-question-id]').find('.ays_next').attr('disabled', 'disabled');
                            $(this).parents('div[data-question-id]').find('.ays_early_finish').attr('disabled', 'disabled');
                            $(this).parents('div[data-question-id]').find('.ays_previous').attr('disabled', 'disabled');
                            if( disableQuestions ){
                                quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                            }
                            explanationTimeout = setTimeout(function(){
                                window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                goToLastPage(e);
                            }, explanationTime * 1000);
                        }
                        if(finishAfterWrongAnswer){
                            if( aysThisQuizBullets !== null ){
                                aysThisQuizBullets.baseElement.find('.ays_questions_nav_question').each(function(){
                                    var thisBullet = $(this);
                                    thisBullet.attr('disabled', 'disabled');
                                });
                            }
                        }
                    }
                    var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                    if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                        if(! $(this).parents('.step').hasClass('not_influence_to_score')){
                            $(this).parents('.step').find('.ays_questtion_explanation').slideDown(250);
                        }
                    }

                    explanationTimeout = setTimeout(function(){
                        if ( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                            if ( existsEmtpyQuestions ) {
                                quizContainer.find('input.ays_finish').trigger('click');
                            }
                        }
                    }, explanationTime * 1000);

                    stopQuestionTimer(quizContainer, questionId, quizId);
                }
            }
        });

        $(document).find('textarea.ays_question_limit_length, input.ays_question_limit_length').on('keyup keypress', function(e) {
            var $this = $(this);
            var questionId = $this.attr('data-question-id');
            var container = $this.parents('.ays-field').next('.ays_quiz_question_text_conteiner');
            var box = container.find('.ays_quiz_question_text_message');
            var questionTextMessage = container.find('.ays_quiz_question_text_message_span');

            if (questionId !== null && questionId != '') {

                // Maximum length of a text field
                var enable_question_text_max_length = (myQuizOptions[questionId].enable_question_text_max_length && myQuizOptions[questionId].enable_question_text_max_length != "") ? myQuizOptions[questionId].enable_question_text_max_length : false;

                // Length
                var question_text_max_length = (myQuizOptions[questionId].question_text_max_length && myQuizOptions[questionId].question_text_max_length != "") ? parseInt(myQuizOptions[questionId].question_text_max_length) : '';

                // Limit by
                var question_limit_text_type = (myQuizOptions[questionId].question_limit_text_type && myQuizOptions[questionId].question_limit_text_type != "") ? myQuizOptions[questionId].question_limit_text_type : 'characters';

                // Show word/character counter
                var question_enable_text_message = (myQuizOptions[questionId].question_enable_text_message && myQuizOptions[questionId].question_enable_text_message != '') ? myQuizOptions[questionId].question_enable_text_message : false;

                var remainder = '';
                if(question_text_max_length != '' && question_text_max_length != 0){
                    switch ( question_limit_text_type ) {
                        case 'characters':
                            var tval = $this.val();
                            var tlength = tval.length;
                            var set = question_text_max_length;
                            var remain = parseInt(set - tlength);
                            if (remain <= 0 && e.which !== 0 && e.charCode !== 0) {
                                $this.val((tval).substring(0, tlength - 1));
                            }
                            if (e.type=="keyup") {
                                var tval = $this.val().trim();
                                if(tval.length > 0 && tval != null){
                                    var wordsLength = this.value.split('').length;
                                    if (wordsLength > question_text_max_length) {
                                        var trimmed = tval.split('', question_text_max_length).join("");
                                        $this.val(trimmed);
                                    }
                                }
                            }
                            remainder = remain;
                            break;
                        case 'words':
                            if (e.type=="keyup") {
                                var tval = $this.val().trim();
                                if(tval.length > 0 && tval != null){
                                    var wordsLength = this.value.match(/\S+/g).length;
                                    if (wordsLength > question_text_max_length) {
                                        var trimmed = tval.split(/\s+/, question_text_max_length).join(" ");
                                        $this.val(trimmed + " ");
                                    }
                                    remainder = question_text_max_length - wordsLength;
                                }
                            }
                            break;
                        default:
                            break;
                    }
                    if (e.type=="keyup") {
                        if ( question_enable_text_message ) {
                            if(question_text_max_length != '' && question_text_max_length != 0){
                                if (remainder <= 0) {
                                    remainder = 0;
                                    if (! box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.addClass('ays_quiz_question_text_error_message')
                                    }
                                }else{
                                    if ( box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.removeClass('ays_quiz_question_text_error_message')
                                    }
                                }
                                if (tval.length == 0 || tval == null) {
                                    if ( box.hasClass('ays_quiz_question_text_error_message') ) {
                                        box.removeClass('ays_quiz_question_text_error_message')
                                    }
                                    remainder = question_text_max_length;
                                }

                                questionTextMessage.html( remainder );
                            }
                        }
                    }
                }
            }
        });

        $(document).on('click', '.enable_max_selection_number input[type="checkbox"]', function(e) {
            var $this = $(this);

            var parent = $this.parents('.step');
            var questionId = parent.attr('data-question-id');
            questionId = parseInt( questionId );

            var checkedCount = parent.find('.ays-field input[type="checkbox"]:checked').length;

            if (questionId !== null && questionId != '' && typeof myQuizOptions[questionId] != 'undefined') {

                // Maximum length of a text field
                var enable_max_selection_number = (myQuizOptions[questionId].enable_max_selection_number && myQuizOptions[questionId].enable_max_selection_number != "") ? myQuizOptions[questionId].enable_max_selection_number : false;

                // Length
                var max_selection_number = (myQuizOptions[questionId].max_selection_number && myQuizOptions[questionId].max_selection_number != "") ? parseInt(myQuizOptions[questionId].max_selection_number) : '';

                if( enable_max_selection_number === true && max_selection_number != '' ){

                    if( max_selection_number < checkedCount ){
                        return false;
                    }
                }
            }

        });

        $(document).on('click', '.enable_min_selection_number input[type="checkbox"]', function(e) {
            var $this = $(this);
            var this_current_fs = $this.parents('.step[data-question-id]');
            var enableArrows = $this.parents(".ays-questions-container").find(".ays_qm_enable_arrows").val();
            var questions = $(this).parents('form').find('.step[data-question-id]');
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            var current_fs_indexes = [];

            var thisIndex = 0;
            for( var j = 0; j < Math.floor( questions.length / parseInt( next_sibilings_count ) ); j++ ){
                thisIndex += parseInt( next_sibilings_count );
                current_fs_indexes.push( thisIndex - 1 );
            }

            var thisStepIndex = 0;
            var thisQuestionStepIndex = questions.index( $this.parents('.step[data-question-id]') );
            for( var k = 0; k < current_fs_indexes.length; k++ ){
                if( thisQuestionStepIndex <= current_fs_indexes[k] ){
                    thisStepIndex = current_fs_indexes[k];
                    break;
                }
            }

            if( thisStepIndex == 0 ){
                thisStepIndex = questions.length - 1;
            }

            var current_fs_index = questions.index( questions.eq( thisStepIndex ) );

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            if ( quiz_display_all_questions ) {
                next_sibilings_count = questions.length;
            }


            var buttonsDiv = $(this).parents('form').find('.step[data-question-id]').eq( thisStepIndex ).find('.ays_buttons_div');
            if($(this).parents('.step').attr('data-question-id')){
                if( parseInt( next_sibilings_count ) > 0 && ( $(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id') ) ){
                    var sliceStart = current_fs_index - parseInt( next_sibilings_count ) < 0 ? 0 : current_fs_index - parseInt( next_sibilings_count ) + 1;
                    this_current_fs = questions.slice( sliceStart, current_fs_index + 1 );
                }else{
                    this_current_fs = $(this).parents('.step[data-question-id]');
                    buttonsDiv = this_current_fs.find('.ays_buttons_div');
                }
            }else{
                this_current_fs = $(this).parents('.step[data-question-id]');
                buttonsDiv = this_current_fs.find('.ays_buttons_div');
            }

            this_current_fs.each(function(){
                var checkedMinSelCount = aysCheckMinimumCountCheckbox( $(this), myQuizOptions );

                // if( ays_quiz_is_question_min_count( $(this), !checkedMinSelCount ) === true ){
                    if( checkedMinSelCount == true ){
                        if(enableArrows){
                            buttonsDiv.find('i.ays_next_arrow').removeAttr('disabled');
                            buttonsDiv.find('i.ays_next_arrow').prop('disabled', false);
                            buttonsDiv.find('i.ays_early_finish').prop('disabled', false);
                        }else{
                            buttonsDiv.find('input.ays_next').removeAttr('disabled');
                            buttonsDiv.find('input.ays_next').prop('disabled', false);
                            buttonsDiv.find('input.ays_early_finish').prop('disabled', false);
                        }
                    }else{
                        if(enableArrows){
                            buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                            buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                            buttonsDiv.find('i.ays_early_finish').prop('disabled', true);
                        }else{
                            buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                            buttonsDiv.find('input.ays_next').prop('disabled', true);
                            buttonsDiv.find('input.ays_early_finish').prop('disabled', true);
                        }
                    }
                // }else{
                //     if(enableArrows){
                //         buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                //         buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                //         buttonsDiv.find('i.ays_early_finish').prop('disabled', true);
                //     }else{
                //         buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                //         buttonsDiv.find('input.ays_next').prop('disabled', true);
                //         buttonsDiv.find('input.ays_early_finish').prop('disabled', true);
                //         return false;
                //     }
                // }
            });
        });

        $(document).find('input.ays_question_number_limit_length').on('keyup keypress', function(e) {
            var $this = $(this);
            var questionId = $this.parents('.step[data-question-id]').attr('data-question-id');
            var parent = $this.parents('.ays-abs-fs');

            if (questionId !== null && questionId != '') {
                var questionOptions = myQuizOptions[questionId];

                // Maximum length of a number field
                var enable_question_number_max_length = (questionOptions.enable_question_number_max_length && questionOptions.enable_question_number_max_length != "") ? questionOptions.enable_question_number_max_length : false;

                // Length
                var question_number_max_length = (typeof questionOptions.question_number_max_length != 'undefined' && questionOptions.question_number_max_length !== "") ? parseInt(questionOptions.question_number_max_length) : '';

                // Minimum length of a number field
                var enable_question_number_min_length = (questionOptions.enable_question_number_min_length && questionOptions.enable_question_number_min_length != "") ? questionOptions.enable_question_number_min_length : false;

                // Length
                var question_number_min_length = (typeof questionOptions.question_number_min_length != 'undefined' && questionOptions.question_number_min_length !== "") ? parseInt(questionOptions.question_number_min_length) : '';

                // Show error message
                var enable_question_number_error_message = (questionOptions.enable_question_number_error_message && questionOptions.enable_question_number_error_message != "") ? questionOptions.enable_question_number_error_message : false;

                // Message
                var question_number_error_message = (questionOptions.question_number_error_message && questionOptions.question_number_error_message != "") ? questionOptions.question_number_error_message : '';

                if ( enable_question_number_max_length ) {
                    if(question_number_max_length !== ''){
                        var tval = $this.val().trim();
                        var inputVal = parseInt( tval );

                        if( ! isNaN(inputVal) ){
                            if ( inputVal > question_number_max_length ) {
                                $this.val(question_number_max_length);
                            }

                            if (e.type=="keyup") {
                                if ( inputVal > question_number_max_length ) {
                                    $this.val(question_number_max_length);
                                }
                            }
                        }
                    }
                }

                if ( enable_question_number_min_length ) {
                    if(question_number_min_length !== ''){
                        var tval = $this.val().trim();
                        var inputVal = parseInt( tval );
                        if( ! isNaN(inputVal) ){
                            if ( inputVal < question_number_min_length ) {
                                $this.val(question_number_min_length);
                            }

                            if (e.type=="keyup") {
                                if ( inputVal < question_number_min_length ) {
                                    $this.val(question_number_min_length);
                                }
                            }
                        }
                    }
                }

                if ( enable_question_number_error_message ) {
                    if ( question_number_error_message != "" ) {
                        var tval = $this.val().trim();
                        var inputVal = tval;

                        var errorMessageBox = parent.find('.ays-quiz-number-error-message');

                        if ( tval != "" ) {

                            if ( isNaN( +inputVal ) ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else if ( tval.indexOf("e") > -1 ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else if ( tval.slice(-1) == "e" ) {
                                if ( errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.removeClass('ays_display_none');
                                }
                            } else {
                                if ( ! errorMessageBox.hasClass('ays_display_none') ) {
                                    errorMessageBox.addClass('ays_display_none');
                                }
                            }
                        } else {
                            if ( ! errorMessageBox.hasClass('ays_display_none') ) {
                                errorMessageBox.addClass('ays_display_none');
                            }
                        }
                    }
                }
            }
        });

        $(document).on('change', 'input[name^="ays_questions"]', function (e) {
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            var _this = $(this);
            var parentStep = _this.parents('.step');
            var questionID = parentStep.data('questionId');
            var questionType = parentStep.attr('data-type');
            var answerId = _this.val();

            if( typeof questionID != "undefined" && questionID !== null ){

                var thisQuestionCorrectAnswer = myQuizOptions[questionID].question_answer.length <= 0 ? array() : myQuizOptions[questionID].question_answer;
                var ifCorrectAnswer = thisQuestionCorrectAnswer[answerId] == '' ? '' : thisQuestionCorrectAnswer[answerId];
                if( typeof ifCorrectAnswer != "undefined" ){
                    _this.parents('.ays-field').find('input[name="ays_answer_correct[]"]').val(ifCorrectAnswer);

                    if( ifCorrectAnswer == '0' && questionType === 'radio' && $(e.target).parents('form.ays-quiz-form').hasClass('enable_correction') ){

                        for (var question_answer_ID in thisQuestionCorrectAnswer) {
                            var UserAnswered_true_or_false = thisQuestionCorrectAnswer[question_answer_ID];
                            parentStep.find('.ays-quiz-answers .ays-field input[value="'+ question_answer_ID +'"]').prev().val(UserAnswered_true_or_false);
                        }
                    }
                }
            }

            if(typeof myOptions != 'undefined'){
                var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
                if(isRequiredQuestion === true){
                    if($(e.target).attr('type') === 'radio' || $(e.target).attr('type') === 'checkbox'){
                        if($(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]:checked').length != 0){
                            if ( $(e.target).attr('type') === 'checkbox' ) {
                                var checkedMinSelCount = aysCheckMinimumCountCheckbox( $(e.target).parents('div[data-question-id]'), myQuizOptions );

                                if( checkedMinSelCount == true ){
                                    if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                        $(e.target).parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                                        $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');

                                    }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                                        $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                                    }
                                }else{
                                    if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){

                                        $(e.target).parents('div[data-question-id]').find('input.ays_next').attr('disabled', 'disabled');
                                        $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', 'disabled');

                                    }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', 'disabled');
                                        $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', 'disabled');
                                    }
                                }
                            } else {
                                if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                    $(e.target).parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                                    $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');

                                }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                                    $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                                }
                            }
                        }else{
                            if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){

                                $(e.target).parents('div[data-question-id]').find('input.ays_next').attr('disabled', 'disabled');
                                $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', 'disabled');

                            }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', 'disabled');
                                $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', 'disabled');
                            }
                        }
                    }
                }
            }

            var isRequiredQuestion = myOptions.make_questions_required && myOptions.make_questions_required === "on";

            if($(e.target).parents('.step').hasClass('not_influence_to_score')){
                if($(e.target).attr('type') === 'radio') {
                    $(e.target).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                    $(e.target).parents('.ays-field').addClass('checked_answer_div');
                }
                if($(e.target).attr('type') === 'checkbox') {
                    if(!$(e.target).parents('.ays-field').hasClass('checked_answer_div')){
                        $(e.target).parents('.ays-field').addClass('checked_answer_div');
                    }else{
                        $(e.target).parents('.ays-field').removeClass('checked_answer_div');
                    }
                } 
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') && checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if (checked_inputs.attr('type') === 'radio') {
                        checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');                    
                    }
                }
                if ($(e.target).parents().eq(4).hasClass('enable_correction')) {                    
                    if (checked_inputs.attr('type') === "radio") {
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                    } else if (checked_inputs.attr('type') === "checkbox") {
                        $(e.target).attr('disabled', true);
                        $(e.target).off('change');
                    }
                }
                return false;
            }
            if ($(e.target).parents().eq(4).hasClass('enable_correction')) {
                var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
                var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
                var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
                var showOnlyWrongAnswer = (myOptions.show_only_wrong_answer && myOptions.show_only_wrong_answer == "on") ? true : false;
                var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                var next_sibilings_count = quizContainer.find('.ays_question_count_per_page').val();
                var enableNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == 'on') ? true : false;
                var thankYouStep = quizContainer.find('div.step.ays_thank_you_fs');
                var infoFormLast = thankYouStep.prev().find('div.information_form');
                // Display all questions on one page
                myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
                var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

                var disableQuestions = false;
                if( parseInt( next_sibilings_count ) > 0 || quiz_display_all_questions == true ){
                    disableQuestions = true;
                }

                myOptions.quiz_waiting_time = ( myOptions.quiz_waiting_time ) ? myOptions.quiz_waiting_time : "off";
                var quizWaitingTime = (myOptions.quiz_waiting_time && myOptions.quiz_waiting_time == "on") ? true : false;

                myOptions.enable_next_button = ( myOptions.enable_next_button ) ? myOptions.enable_next_button : "off";
                var quizNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == "on") ? true : false;

                if ( quizWaitingTime && !quizNextButton ) {
                    explanationTime += 2;
                }

                var quizWaitingCountDownDate = new Date().getTime() + (explanationTime * 1000);

                if ($(e.target).parents().eq(1).find('input[name="ays_answer_correct[]"]').length !== 0) {
                    var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                    if (checked_inputs.attr('type') === "radio") {
                        checked_inputs.nextAll().addClass('answered');
                        checked_inputs.parent().addClass('checked_answer_div');
                        if (checked_inputs.prev().val() == 1){
                            checked_inputs.nextAll().addClass('correct')
                            checked_inputs.parent().addClass('correct_div');

                            if( aysThisQuizBullets !== null ){
                                var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                                var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                                thisBullet.attr('disabled', 'disabled');
                                thisBullet.addClass('ays_quiz_correct_answer');
                                if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                    thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                                }
                            }
                        }else{
                            checked_inputs.nextAll().addClass('wrong');
                            checked_inputs.parent().addClass('wrong_div');

                            if( aysThisQuizBullets !== null ){
                                var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                                var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                                thisBullet.attr('disabled', 'disabled');
                                thisBullet.addClass('ays_quiz_wrong_answer');
                                if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                    thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                                }
                            }
                        }

                        if (checked_inputs.prev().val() == 1) {
                            $(e.target).parents('.ays-field').addClass('correct_div checked_answer_div');
                            $(e.target).next('label').addClass('correct answered');

                            if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')){
                                if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                    $(e.target).parents().eq(3).find('.right_answer_text').slideDown(250);
                                }
                                explanationTimeout = setTimeout(function(){
                                    if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                        checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                            if ( existsEmtpyQuestions ) {
                                                if(infoFormLast.length == 0){
                                                    quizContainer.find('input.ays_finish').trigger('click');
                                                } else {
                                                    quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                }
                                            }
                                        } else {
                                            checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                        }
                                    } else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                        var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                        if ( existsEmtpyQuestions ) {
                                            if(infoFormLast.length == 0){
                                                quizContainer.find('input.ays_finish').trigger('click');
                                            } else {
                                                quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                            }
                                        }
                                    }

                                }, explanationTime * 1000);
                                if (quizWaitingTime && !quizNextButton) {
                                    window.countdownTimeForShowInterval = setInterval(function () {
                                        countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                                    }, 1000);
                                }
                            }else{
                                explanationTimeout = setTimeout(function(){
                                    if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                        checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                            if ( existsEmtpyQuestions ) {
                                                if(infoFormLast.length == 0){
                                                    quizContainer.find('input.ays_finish').trigger('click');
                                                } else {
                                                    quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                }
                                            }
                                        } else {
                                            checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                        }
                                    } else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                        var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                        if ( existsEmtpyQuestions ) {
                                            if(infoFormLast.length == 0){
                                                quizContainer.find('input.ays_finish').trigger('click');
                                            } else {
                                                quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                            }
                                        }
                                    }
                                }, explanationTime * 1000);
                                if (quizWaitingTime && !quizNextButton) {
                                    window.countdownTimeForShowInterval = setInterval(function () {
                                        countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                                    }, 1000);
                                }
                            }
                            if((right_answer_sound)){
                                resetPlaying([right_answer_sound, wrong_answer_sound]);
                                setTimeout(function(){
                                    right_answer_sound.play();
                                }, 10);
                            }
                        }
                        else {
                            if(showOnlyWrongAnswer === false){
                                $(e.target).parents('.ays-quiz-answers').find('input[name="ays_answer_correct[]"][value="1"]').parent().addClass('correct_div checked_answer_div');
                                $(e.target).parents('.ays-quiz-answers').find('input[name="ays_answer_correct[]"][value="1"]').nextAll().addClass('correct answered');
                                $(e.target).parents('.ays-field').addClass('wrong_div');
                            }

                            if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both' || myOptions.answers_rw_texts == 'disable')){
                                if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                    $(e.target).parents().eq(3).find('.wrong_answer_text').slideDown(250);
                                }
                                if(finishAfterWrongAnswer){
                                    checked_inputs.parents('div[data-question-id]').find('.ays_next').attr('disabled', 'disabled');
                                    checked_inputs.parents('div[data-question-id]').find('.ays_early_finish').attr('disabled', 'disabled');
                                    checked_inputs.parents('div[data-question-id]').find('.ays_previous').attr('disabled', 'disabled');
                                    if( disableQuestions ){
                                        quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                                    }
                                }
                                explanationTimeout = setTimeout(function(){
                                    if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                        checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                        if(finishAfterWrongAnswer){
                                            window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                            goToLastPage(e);
                                        } else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                                var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                                if ( existsEmtpyQuestions ) {
                                                    if(infoFormLast.length == 0){
                                                        quizContainer.find('input.ays_finish').trigger('click');
                                                    } else {
                                                        quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                    }
                                                }
                                        } else{
                                            if ( isRequiredQuestion ) {
                                                var existsEmtpyQuestions = ays_quiz_is_question_empty( checked_inputs.parents('div[data-question-id]') );
                                                if ( existsEmtpyQuestions ) {
                                                    checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                                }
                                            } else {
                                                checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                            }
                                        }
                                    }else{
                                        if(finishAfterWrongAnswer){
                                            window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                            goToLastPage(e);
                                        } 
                                        else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                            if ( existsEmtpyQuestions ) {
                                                if(infoFormLast.length == 0){
                                                    quizContainer.find('input.ays_finish').trigger('click');
                                                } else {
                                                    quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                }
                                            }
                                        }
                                    }
                                }, explanationTime * 1000);
                                if (quizWaitingTime && !quizNextButton) {
                                    window.countdownTimeForShowInterval = setInterval(function () {
                                        countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                                    }, 1000);
                                }
                            }else{
                                if(finishAfterWrongAnswer){
                                    if( disableQuestions ){
                                        quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                                    }
                                }

                                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                    checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    if(finishAfterWrongAnswer){
                                        window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                        goToLastPage(e);
                                    } else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                        explanationTimeout = setTimeout(function(){
                                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                            if ( existsEmtpyQuestions ) {
                                                if(infoFormLast.length == 0){
                                                    quizContainer.find('input.ays_finish').trigger('click');
                                                } else {
                                                    quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                }
                                            }
                                        }, explanationTime * 1000);
                                        if (quizWaitingTime && !quizNextButton) {
                                            window.countdownTimeForShowInterval = setInterval(function () {
                                                countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                                            }, 1000);
                                        }
                                    } else{
                                        explanationTimeout = setTimeout(function(){
                                            if ( isRequiredQuestion ) {
                                                var existsEmtpyQuestions = ays_quiz_is_question_empty( checked_inputs.parents('div[data-question-id]') );
                                                if ( existsEmtpyQuestions ) {
                                                    checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                                }
                                            } else {
                                                checked_inputs.parents('div[data-question-id]').find('.ays_next').trigger('click');
                                            }
                                        }, explanationTime * 1000);
                                    }
                                }else{
                                    if(finishAfterWrongAnswer){
                                        window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                        goToLastPage(e);
                                    } 
                                    else if( quiz_display_all_questions && isRequiredQuestion && !enableNextButton ) {
                                        explanationTimeout = setTimeout(function(){
                                            var existsEmtpyQuestions = ays_quiz_is_question_empty( quizContainer.find('div[data-question-id]') );
                                            if ( existsEmtpyQuestions ) {
                                                if(infoFormLast.length == 0){
                                                    quizContainer.find('input.ays_finish').trigger('click');
                                                } else {
                                                    quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                                }
                                            }
                                        }, explanationTime * 1000);
                                        if (quizWaitingTime && !quizNextButton) {
                                            window.countdownTimeForShowInterval = setInterval(function () {
                                                countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                                            }, 1000);
                                        }
                                    }
                                }
                            }
                            if((wrong_answer_sound)){
                                resetPlaying([right_answer_sound, wrong_answer_sound]);
                                setTimeout(function(){
                                    wrong_answer_sound.play();
                                }, 10);
                            }
                            if(finishAfterWrongAnswer){
                                if( aysThisQuizBullets !== null ){
                                    aysThisQuizBullets.baseElement.find('.ays_questions_nav_question').each(function(){
                                        var thisBullet = $(this);
                                        thisBullet.attr('disabled', 'disabled');
                                    });
                                }
                            }
                        }

                        if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                            if(! $(e.target).parents('.step').hasClass('not_influence_to_score')){
                                $(e.target).parents().eq(3).find('.ays_questtion_explanation').slideDown(250);
                            }
                        }
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input[name^="ays_questions"]').off('change');
                        $(e.target).parents('div[data-question-id]').find('.ays-field').css({
                            'pointer-events': 'none'
                        });

                    }else if(checked_inputs.attr('type') === "checkbox"){
                        checked_inputs = $(e.target);
                        if( $(e.target).parents().eq(1).find('input:checked').length > 0 ){
                            if( aysThisQuizBullets !== null ){
                                var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                                var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                                thisBullet.parent().addClass('ays-quiz-questions-nav-item-answered');
                            }
                        }

                        if (checked_inputs.length === 1) {
                            checked_inputs.parent().addClass('checked_answer_div');
                            if(checked_inputs.prev().val() == 1){
                                if((right_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        right_answer_sound.play();
                                    }, 10);
                                }
                                checked_inputs.parents('.ays-field').addClass('correct_div checked_answer_div');
                                checked_inputs.nextAll().addClass('correct answered');
                            }else{
                                if((wrong_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        wrong_answer_sound.play();
                                    }, 10);
                                }
                                if(finishAfterWrongAnswer){
                                    window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                    goToLastPage(e);
                                    if( disableQuestions ){
                                        quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                                    }
                                    if( aysThisQuizBullets !== null ){
                                        aysThisQuizBullets.baseElement.find('.ays_questions_nav_question').each(function(){
                                            var thisBullet = $(this);
                                            thisBullet.attr('disabled', 'disabled');
                                        });
                                    }
                                }
                                checked_inputs.parent().addClass('wrong_div');
                                checked_inputs.nextAll().addClass('wrong answered');
                            }
                        }else{
                            for (var i = 0; i < checked_inputs.length; i++) {
                                if(checked_inputs.eq(i).prev().val() == 1){
                                    if((right_answer_sound)){
                                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                                        setTimeout(function(){
                                            right_answer_sound.play();
                                        }, 10);
                                    }
                                    checked_inputs.eq(i).nextAll().addClass('correct answered');
                                    checked_inputs.eq(i).parent().addClass('correct_div');
                                    checked_inputs.eq(i).parent().addClass('checked_answer_div');
                                }else{
                                    if((wrong_answer_sound)){
                                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                                        setTimeout(function(){
                                            wrong_answer_sound.play();
                                        }, 10);
                                    }
                                    if(finishAfterWrongAnswer){
                                        window.aysEarlyFinishConfirmBox[ quizId ] = true;
                                        goToLastPage(e);
                                        if( disableQuestions ){
                                            quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                                        }
                                    }
                                    checked_inputs.eq(i).parent().addClass('checked_answer_div');
                                    checked_inputs.eq(i).nextAll().addClass('wrong answered');
                                    checked_inputs.eq(i).parent().addClass('wrong_div');
                                }
                            }
                            if(checked_inputs.eq(i).prev().val() == 1){
                                checked_inputs.eq(i).next().addClass('correct answered');
                                if((right_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        right_answer_sound.play();
                                    }, 10);
                                }
                            }else{
                                checked_inputs.eq(i).next().addClass('wrong answered');
                                if((wrong_answer_sound)){
                                    resetPlaying([right_answer_sound, wrong_answer_sound]);
                                    setTimeout(function(){
                                        wrong_answer_sound.play();
                                    }, 10);
                                }
                                if(finishAfterWrongAnswer){
                                    if( aysThisQuizBullets !== null ){
                                        aysThisQuizBullets.baseElement.find('.ays_questions_nav_question').each(function(){
                                            var thisBullet = $(this);
                                            thisBullet.attr('disabled', 'disabled');
                                        });
                                    }
                                }
                            }
                        }

                        $(e.target).attr('disabled', true);
                        $(e.target).off('change');
                    }
                }

                if( parentStep.data('type') === 'radio' ) {
                    stopQuestionTimer(quizContainer, questionID, quizId);
                }

                if( aysThisQuizBullets !== null ) {
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionID + '"]');
                    thisBullet.removeClass('ays-has-error');
                }
                parentStep.removeClass('ays-has-error');
            }else{
                if($(e.target).attr('type') === 'radio') {
                    $(e.target).parents('.ays-quiz-answers').find('.checked_answer_div').removeClass('checked_answer_div');
                    $(e.target).parents('.ays-field').addClass('checked_answer_div');
                }
                if($(e.target).attr('type') === 'checkbox') {
                    if(!$(e.target).parents('.ays-field').hasClass('checked_answer_div')){
                        $(e.target).parents('.ays-field').addClass('checked_answer_div');
                    }else{
                        $(e.target).parents('.ays-field').removeClass('checked_answer_div');
                    }
                } 
                var checked_inputs = $(e.target).parents().eq(1).find('input:checked');
                if( checked_inputs.length > 0 ){
                    if( aysThisQuizBullets !== null ){
                        var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                        var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                        thisBullet.parent().addClass('ays-quiz-questions-nav-item-answered');
                    }
                }else{
                    if( aysThisQuizBullets !== null ){
                        var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                        var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                        thisBullet.parent().removeClass('ays-quiz-questions-nav-item-answered');
                    }
                }

                if (checked_inputs.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                    checked_inputs.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if (checked_inputs.attr('type') === 'radio') {
                        checked_inputs.parents('div[data-question-id]').find('input.ays_next').trigger('click');
                    }
                }

                if ( myOptions.enable_timer && myOptions.enable_timer === 'on' ) {
                    if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
                        if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
                            if( isRequiredQuestion ) {
                                if (ays_quiz_is_question_empty(quizContainer.find('div[data-question-id]')) === true) {
                                    for (var questionId in window.aysQuizQuestionTimers[quizId]) {
                                        stopQuestionTimer(quizContainer, questionId, quizId);
                                    }
                                }
                            }else{
                                // for (var questionId in window.aysQuizQuestionTimers[quizId]) {
                                //     stopQuestionTimer(quizContainer, questionId);
                                // }
                            }
                        }
                    }
                }

                if( aysThisQuizBullets !== null ) {
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionID + '"]');
                    thisBullet.removeClass('ays-has-error');
                }
            }
        });

        $(document).on('change', 'textarea[name^="ays_questions"], input[type="text"][name^="ays_questions"], input[type="number"][name^="ays_questions"], input[type="date"][name^="ays_questions"]', function (e) {
            if( $(e.target).val().length > 0 ){
                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                    thisBullet.parent().addClass('ays-quiz-questions-nav-item-answered');
                    $(e.target).parents('div[data-question-id]').removeClass('ays-has-error');
                    thisBullet.removeClass('ays-has-error');
                }
            }else{
                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                    thisBullet.parent().removeClass('ays-quiz-questions-nav-item-answered');
                }
            }
        });

        $(document).on('input', 'textarea[name^="ays_questions"], input[name^="ays_questions"]', function (e) {

            var _this = $(this);
            var parentStep = _this.parents('.step');
            var questionType = parentStep.attr('data-type');

            if( $(e.target).val().length > 0 ){
                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                    thisBullet.parent().addClass('ays-quiz-questions-nav-item-answered');
                    $(e.target).parents('div[data-question-id]').removeClass('ays-has-error');
                    thisBullet.removeClass('ays-has-error');
                }
            }else{
                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = $(e.target).parents('div[data-question-id]').data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                    thisBullet.parent().removeClass('ays-quiz-questions-nav-item-answered');
                }
            }

            if(typeof myOptions != 'undefined'){
                var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
                if(isRequiredQuestion === true){
                    if( questionType === 'fill_in_blank' ){
                        var fill_in_blank_answers = parentStep.find('.ays-quiz-fill-in-blank-input');

                        var fill_in_blank_flag = true;
                        if(fill_in_blank_answers.length > 0){
                            for (var i = 0; i < fill_in_blank_answers.length; i++) {
                                var current_input_val = $(fill_in_blank_answers[i]).val();
                                if( current_input_val == '' ){
                                    fill_in_blank_flag = false;
                                    break;
                                }
                            }
                        }

                        if( fill_in_blank_flag ){
                            if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                $(e.target).parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                                $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');
                            }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                                $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                            }
                        }else{
                            if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                $(e.target).parents('div[data-question-id]').find('input.ays_next').attr('disabled', true);
                                $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', true);
                            }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', true);
                                $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', true);
                            }
                        }
                    } else {
                        if($(e.target).attr('type') === 'text' ||
                           $(e.target).attr('type') === 'number' ||
                           $(e.target).attr('type') === 'date'){
                            if($(e.target).val() != ''){
                                if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                    $(e.target).parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                                    $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');
                                }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                                    $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                                }
                            }else{
                                if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                                    $(e.target).parents('div[data-question-id]').find('input.ays_next').attr('disabled', true);
                                    $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', true);
                                }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', true);
                                    $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', true);
                                }
                            }
                        }
                    }
                }
            }
        });

        $(document).on('input', '.information_form input[name="ays_user_phone"]', function(){
            if ($(this).attr('type') !== 'hidden') {
                $(this).removeClass('ays_red_border');
                $(this).removeClass('ays_green_border');
                if($(this).val() != ''){
                    if (!validatePhoneNumber($(this).get(0))) {
                        $(this).addClass('ays_red_border');
                    }else{
                        $(this).addClass('ays_green_border');
                    }
                }
            }
        });
        
        $(document).on('input', 'input.ays_quiz_password', function(e){
            var $this = $(this);
            var startButton = $this.parents('.ays-quiz-container').find('input.start_button');
            if($this.val() != ''){
                startButton.removeAttr('disabled');
            }else{
                startButton.attr('disabled', 'disabled');
            }
        });

        $(document).on('click', '.ays-quiz-password-toggle', function(e){
            var $this  = $(this);

            var parent = $this.parents('.ays-quiz-password-toggle-visibility-box');
            var passwordInput = parent.find('.ays_quiz_password');

            var visibilityOn  = parent.find('.ays-quiz-password-toggle-visibility');
            var visibilityOff = parent.find('.ays-quiz-password-toggle-visibility-off');

            if( $this.hasClass('ays-quiz-password-toggle-visibility-off') ) {
                passwordInput.attr('type', 'text');

                if ( visibilityOn.hasClass('ays_display_none') ) {
                    visibilityOn.removeClass('ays_display_none');
                }

                if ( ! visibilityOff.hasClass('ays_display_none') ) {
                    visibilityOff.addClass('ays_display_none');
                }

            } else if( $this.hasClass('ays-quiz-password-toggle-visibility') ) {
                passwordInput.attr('type', 'password');

                if ( ! visibilityOn.hasClass('ays_display_none') ) {
                    visibilityOn.addClass('ays_display_none');
                }

                if ( visibilityOff.hasClass('ays_display_none') ) {
                    visibilityOff.removeClass('ays_display_none');
                }
            }
        });

        setTimeout(function(){
            $(document).find('input.ays_quiz_password').val('');
        }, 500);

        $(document).on('input', '.information_form input[name="ays_user_email"], .information_form input[type="email"]', function(){
            if ($(this).attr('type') !== 'hidden') {
                $(this).removeClass('ays_red_border');
                $(this).removeClass('ays_green_border');
                if($(this).val() != ''){
                    if (!(emailValivatePattern.test($(this).val()))) {
                        $(this).addClass('ays_red_border');
                    }else{
                        $(this).addClass('ays_green_border');
                    }
                }
            }
        });

        $(document).find('.ays-text-field .ays-text-input').each(function(ev){
            $(this).on('keydown', function(e){
                myOptions.enable_enter_key = !( myOptions.enable_enter_key ) ? "on" : myOptions.enable_enter_key;
                var enableEnterKey = (myOptions.enable_enter_key && myOptions.enable_enter_key == "on") ? true : false;
                var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
                var isKeyboardNavigation = (myOptions.quiz_enable_keyboard_navigation && myOptions.quiz_enable_keyboard_navigation == "on") ? true : false;
                if(enableEnterKey || isKeyboardNavigation){
                    if (e.keyCode === 13 && !e.shiftKey) {
                        if(animating){
                            return false;
                        }
                        if(isRequiredQuestion === true){
                            if($(this).val() == ''){
                                return false;
                            }
                        }
                        if($(this).parents('.step').find('input.ays_finish.action-button').length > 0){
                            $(this).parents('.step').find('input.ays_finish.action-button').trigger('click');
                        }else{
                            $(this).parents('.step').find('input.ays_next.action-button').trigger('click');
                        }
                        $(this).trigger('blur');
                        return false;
                    }
                }
            });
        });

        $(document).find('.ays_next').on('click', function(e){
            e.preventDefault();
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            if(checkQuizPassword(e, myOptions, true) === false){
                return false;
            }

            if ( typeof window.aysSeeResultConfirmBox[ quizId ] != 'undefined' && window.aysSeeResultConfirmBox[ quizId ] ) {
                window.aysSeeResultConfirmBox[ quizId ] = false;
                return false;
            }

            if(typeof explanationTimeout != 'undefined'){
                clearTimeout(explanationTimeout);
            }

            ays_quiz_container = $(this).parents(".ays-quiz-container");
            if (!($(this).hasClass('start_button'))) {
                if ($(this).parents('.step').find('input[required]').length !== 0 || 
                    $(this).parents('.step').find('select.ays_quiz_form_input[required]').length !== 0) {
                    var empty_inputs = 0;
                    var required_inputs = $(this).parents('.step').find('input[required]');
                    $(this).parents('.step').find('.ays_red_border').removeClass('ays_red_border');
                    $(this).parents('.step').find('.ays_green_border').removeClass('ays_green_border');
                    for (var i = 0; i < required_inputs.length; i++) {
                        switch(required_inputs.eq(i).attr('type')){
                            case "checkbox": {
                                if(required_inputs.eq(i).prop('checked') === false){
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }else{
                                    required_inputs.eq(i).addClass('ays_green_border');
                                }
                                break;
                            }
                            case "email": {
                                if (!(emailValivatePattern.test(required_inputs.eq(i).val()))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }else{
                                    required_inputs.eq(i).addClass('ays_green_border');
                                }
                                break;
                            }
                            case "tel": {
                                if (!validatePhoneNumber(required_inputs.eq(i).get(0))) {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }else{
                                    required_inputs.eq(i).addClass('ays_green_border');
                                }
                                break;
                            }
                            default:{
                                if (required_inputs.eq(i).val().trim() === '' &&
                                    required_inputs.eq(i).attr('type') !== 'hidden') {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }else{
                                    required_inputs.eq(i).addClass('ays_green_border');
                                }
                                break;
                            }
                        }
                    }
                    var empty_inputs2 = 0;
                    var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                    var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                    var selectAttr = $(this).parents('.step').find('select.ays_quiz_form_input[required]');
                    if(phoneInput.val() != ''){
                        phoneInput.removeClass('ays_red_border');
                        phoneInput.removeClass('ays_green_border');
                        if (!validatePhoneNumber(phoneInput.get(0))) {
                            if (phoneInput.attr('type') !== 'hidden') {
                                phoneInput.addClass('ays_red_border');
                                phoneInput.addClass('shake');
                                empty_inputs2++;
                            }
                        }else{
                            phoneInput.addClass('ays_green_border');
                        }
                    }
                    if(emailInput.val() != ''){
                        emailInput.removeClass('ays_red_border');
                        emailInput.removeClass('ays_green_border');
                        if (!(emailValivatePattern.test(emailInput.val()))) {
                            if (emailInput.attr('type') !== 'hidden') {
                                emailInput.addClass('ays_red_border');
                                emailInput.addClass('shake');
                                empty_inputs++;
                            }
                        }else{
                            emailInput.addClass('ays_green_border');
                        }
                    }

                    for (var i = 0; i < selectAttr.length; i++) {
                        if(selectAttr.eq(i).val() == ''){
                            selectAttr.eq(i).removeClass('ays_red_border');
                            selectAttr.eq(i).removeClass('ays_green_border');

                            selectAttr.eq(i).addClass('ays_red_border');
                            selectAttr.eq(i).addClass('shake');
                            empty_inputs++;
                        }else{
                            selectAttr.eq(i).removeClass('ays_red_border');
                        }
                    }
                    var errorFields = $(this).parents('.step').find('.ays_red_border');
                    if (empty_inputs2 !== 0 || empty_inputs !== 0) {
                        setTimeout(function(){
                            errorFields.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        setTimeout(function(){
                            required_inputs.each(function(){
                                $(this).removeClass('shake');
                            });
                        }, 500);
                        return false;
                    }else{
                        $(this).addClass('ays_start_allow');
                    }
                }else{
                    if ($(this).parents('.step').find('.information_form').length !== 0 ){
                        var empty_inputs = 0;
                        var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                        var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                        var emailInputs = $(this).parents('.step').find('input[type="email"]');
                        if(phoneInput.val() != ''){
                            phoneInput.removeClass('ays_red_border');
                            phoneInput.removeClass('ays_green_border');
                            if (!validatePhoneNumber(phoneInput.get(0))) {
                                if (phoneInput.attr('type') !== 'hidden') {
                                    phoneInput.addClass('ays_red_border');
                                    phoneInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                phoneInput.addClass('ays_green_border');
                            }
                        }
                        if(emailInput.val() != ''){
                            emailInput.removeClass('ays_red_border');
                            emailInput.removeClass('ays_green_border');
                            if (!(emailValivatePattern.test(emailInput.val()))) {
                                if (emailInput.attr('type') !== 'hidden') {
                                    emailInput.addClass('ays_red_border');
                                    emailInput.addClass('shake');
                                    empty_inputs++;
                                }
                            }else{
                                emailInput.addClass('ays_green_border');
                            }
                        }
                        emailInputs.each(function(){
                            var thisEmailInput = $(this);
                            if(thisEmailInput.val() != ''){
                                thisEmailInput.removeClass('ays_red_border');
                                thisEmailInput.removeClass('ays_green_border');
                                if (!(emailValivatePattern.test(thisEmailInput.val()))) {
                                    thisEmailInput.addClass('ays_red_border');
                                    thisEmailInput.addClass('shake');
                                    empty_inputs++;
                                }else{
                                    thisEmailInput.addClass('ays_green_border');
                                }
                            }
                        });
                        var errorFields = $(this).parents('.step').find('.ays_red_border');
                        if (empty_inputs !== 0) {
                            setTimeout(function(){
                                errorFields.each(function(){
                                    $(this).removeClass('shake');
                                });
                            }, 500);
                            return false;
                        }
                        $(this).addClass('ays_start_allow');
                    }
                }
            }
            
            if (animating) return false;
            animating = true;
            current_fs = $(this).parents('.step');
            next_fs = $(this).parents('.step').next();

            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            var enableNavigationBar = (myOptions.enable_navigation_bar && myOptions.enable_navigation_bar == "on") ? true : false;
            var quizDisableInputFocusing = (myOptions.quiz_disable_input_focusing && myOptions.quiz_disable_input_focusing == "on") ? true : false;
            var timerMainContainer = ays_quiz_container.find('.ays_quiz_timer_container');
            var timerType = myOptions.quiz_timer_type ? myOptions.quiz_timer_type : 'quiz_timer';

            if( current_fs.find('.information_form').length === 0 && !($(this).hasClass('ays_finish')) && !($(this).hasClass('start_button')) ) {
                if (myOptions.enable_timer && myOptions.enable_timer === 'on') {
                    if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
                        if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
                            if (myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on') {
                                if (myOptions.make_questions_required && myOptions.make_questions_required === "on") {
                                    var qid = checkQuestionTimer(ays_quiz_container, quizId);
                                    if (qid === false) {
                                        for (var questionID in window.aysQuizQuestionTimers[quizId]) {
                                            stopQuestionTimer(ays_quiz_container, questionID, quizId);
                                        }
                                        timerMainContainer.addClass('ays-quiz-timer-end-for-required')
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ( typeof window.aysEarlyFinishConfirmBox[ quizId ] != 'undefined' && ! window.aysEarlyFinishConfirmBox[ quizId ] ) {
                if( ! timerMainContainer.hasClass('ays-quiz-timer-end-for-required') ){
                    if(isRequiredQuestion === true){
                        if(next_fs.find('.information_form').length === 0){
                            if ( $(this).hasClass("ays_finish") ) {
                                if ( enableNavigationBar ) {
                                    ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                                        return false;
                                    }
                                }
                            }
                        } else {
                            var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                            var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]'))+1;
                            var questionCountPerPage = myOptions.question_count_per_page && myOptions.question_count_per_page === 'on' ? true : false;
                            if(questions_count === curent_number){
                                if ( enableNavigationBar ) {

                                    ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                                        return false;
                                    }
                                } else if( questionCountPerPage ){
                                    ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                                        return false;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if( current_fs.find('.information_form').length != 0 && !($(this).hasClass('ays_finish'))){
                $(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideDown(500);
            }

            if( next_fs.find('.information_form').length != 0 ){
                stopQuestionTimer( ays_quiz_container, current_fs.data('questionId'), quizId );
                $(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideUp(500);
                timerMainContainer.slideUp(500);
            }

            var questions_count = $(this).parents('form').find('div[data-question-id]').length;
            var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();

            var isQuestionPerPageCustom = (myOptions.question_count_per_page_type && myOptions.question_count_per_page_type == "custom") ? true : false;
            var customPerPageValue = (myOptions.question_count_per_page_custom_order && myOptions.question_count_per_page_custom_order != "") ? myOptions.question_count_per_page_custom_order : "";
            var customPerPageRequiredFlag = true;
            if(next_fs.find('.information_form').length === 0){
                if(isRequiredQuestion === true && !$(this).hasClass('ays-quiz-after-timer-end')){
                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                        var customPerPageRequiredFlag = false;
                    }
                }
                if( isQuestionPerPageCustom && customPerPageValue != "" && customPerPageRequiredFlag ){
                    var custom_next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page_custom_next').val();
                    var custom_prev_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val();
                    var questions_count_new = $(this).parents('form').find('div[data-question-id]').length;
                    if( typeof custom_next_sibilings_count != "undefined" && custom_next_sibilings_count != "" ){
                        var custom_next_sibilings_count_arr = custom_next_sibilings_count.split(',');
                        if (custom_next_sibilings_count_arr !== undefined && custom_next_sibilings_count_arr.length > 0) {
                            next_sibilings_count = parseInt( custom_next_sibilings_count_arr[0] );
                            $(this).parents('form').find('.ays_question_count_per_page').val(next_sibilings_count);

                            var shift_number = custom_next_sibilings_count_arr.shift();

                            if( typeof custom_prev_sibilings_count == 'undefined' || custom_prev_sibilings_count == "" ){
                                $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val(next_sibilings_count);
                            } else {
                                var custom_prev_sibilings_count_arr = custom_prev_sibilings_count.split(",");
                                custom_prev_sibilings_count_arr.push(shift_number);

                                var custom_prev_sibilings_count_arr_new = custom_prev_sibilings_count_arr.join(",");
                                $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val(custom_prev_sibilings_count_arr_new);
                            }

                            if( custom_next_sibilings_count_arr.length > 0 ){
                                var custom_next_sibilings_count_new_val = custom_next_sibilings_count_arr.join(',');
                                $(this).parents('form').find('.ays_question_count_per_page_custom_next').val(custom_next_sibilings_count_new_val);
                            } else {

                                var sum_prev_quesiton = 0;
                                for (var ii = 0; ii < custom_prev_sibilings_count_arr.length; ii++ ) {
                                    sum_prev_quesiton += parseInt( custom_prev_sibilings_count_arr[ii] );
                                }
                                var last_page_questions_count = parseInt(questions_count_new) - parseInt(sum_prev_quesiton);
                                if( last_page_questions_count != 0 ) {
                                    $(this).parents('form').find('.ays_question_count_per_page_custom_next').val(last_page_questions_count);
                                } else {
                                    $(this).parents('form').find('.ays_question_count_per_page_custom_next').val("");
                                }
                            }
                            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
                        }
                    }
                }

            }

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            // Enable finish button
            myOptions.enable_early_finish = ( myOptions.enable_early_finish ) ? myOptions.enable_early_finish : 'off';
            var enable_early_finish = (myOptions.enable_early_finish && myOptions.enable_early_finish == "on") ? true : false;

            var enableNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == 'on') ? true : false;

            if ( quiz_display_all_questions ) {
                next_sibilings_count = questions_count;
            }

            if(parseInt(next_sibilings_count)>0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))){
                if(parseInt(next_sibilings_count) >= questions_count){
                    next_sibilings_count = questions_count;
                }

                var current_fs_index = $(this).parents('form').find('.step').index($(this).parents('.step'));
                var new_current_fs_index = $(this).parents('form').find('.step').index($(this).parents('form').find('.step.active-step'));
                var next_fs_index = $(this).parents('form').find('.step').index(next_fs);

                if($(this).parents('.step').attr('data-question-id')){
                    if( isQuestionPerPageCustom && customPerPageValue != ""){
                        current_fs = $(this).parents('form').find('.step').slice(parseInt(new_current_fs_index) - 1,next_fs_index);
                    } else {
                        current_fs = $(this).parents('form').find('.step').slice(current_fs_index-parseInt(next_sibilings_count),current_fs_index+1);
                    }
                }else{
                    current_fs = $(this).parents('.step');
                }
                if(questions_count === curent_number){
                    if(current_fs.hasClass('.information_form').length !== 0){
                        current_fs.find('.ays_next').eq(current_fs.find('.ays_next').length-1).addClass('ays_timer_end');
                        current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp(500);
                        $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                    }

                    var timer2 = parseInt(ays_quiz_container.find('div.ays-quiz-timer').attr('data-timer'));
                    if (!isNaN(timer2) && myOptions.timer !== undefined) {
                        if (myOptions.timer === timer2 && timer2 !== 0) {
                            var myStartDate = new Date(Date.now() - aysDurationInSeconds * AYS_MS_PER_SECONDS);
                            var countdownEndTime = myStartDate.aysCustomFormat( "#YYYY#-#MM#-#DD# #hhhh#:#mm#:#ss#" );
                            $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(countdownEndTime);
                        } else {
                            $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                        }
                    } else {
                        $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                    }
                }
                

                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var count_per_page = Math.floor(questions_count/parseInt(next_sibilings_count));
                            var nextCountQuestionsPerPage = questions_count-curent_number;
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+parseInt(next_sibilings_count)) / questions_count * 100) + "%";
                            if(nextCountQuestionsPerPage < parseInt(next_sibilings_count)){
                                final_width = ((curent_number+nextCountQuestionsPerPage) / questions_count * 100) + "%";
                            }
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                if(nextCountQuestionsPerPage < parseInt(next_sibilings_count)){
                                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+parseInt(nextCountQuestionsPerPage)));
                                }else{
                                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+parseInt(next_sibilings_count)));
                                }
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                var next_siblings = $(this).parents('.step').nextAll('.step').slice(0,parseInt(next_sibilings_count));

                if($(this).parents('form').find('div[data-question-id]').index($(this).parents('.step'))+1 !== $(this).parents('form').find('div[data-question-id]').length) {
                    for (var z = 0; z < next_siblings.length; z++) {
                        if (next_siblings.eq(z).attr('data-question-id') === undefined) {
                            next_siblings.splice(z);
                        }
                    }
                }else{
                    if(next_siblings.length !== 1) {
                        next_siblings.splice(next_siblings.length - 1);
                    }
                }

                if ($(this).parents('form').hasClass('enable_correction')) {
                    next_siblings.find('input[name^="ays_questions"]').each(function(){
                        if ($(this).parents('.ays-quiz-answers').find('.correct').length === 0 &&
                            $(this).parents('.ays-quiz-answers').find('.wrong').length === 0 &&
                            $(this).parents('.ays-quiz-answers').find('.ays-answered-text-input').length === 0) {
                            $(this).attr('disabled', false);
                        }
                    });
                }

                for(var i=0 ;i<next_siblings.length-1;i++){
                    var nextQuestionType = next_siblings.eq(i).find('input[name^="ays_questions"]').attr('type');
                    var buttonsDiv = next_siblings.eq(i).find('.ays_buttons_div');
                    next_siblings.eq(i).find('.ays_previous').remove();
                    if(i === next_siblings.length-1 && next_siblings.eq(i).find('textarea[name^="ays_questions"]').attr('type')==='text'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'checkbox'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'number'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'text'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    if(i === next_siblings.length-1 && nextQuestionType === 'date'){
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        continue;
                    }
                    next_siblings.eq(i).find('.ays_next').remove();
                    next_siblings.eq(i).find('.ays_early_finish').remove();
                }

                for(var i=0 ;i<next_siblings.length;i++){
                    if ( i !== 0 ) {
                        next_siblings.eq(i).find('.ays-export-quiz-button-container').remove();
                    }
                }

                if ( quiz_display_all_questions ) {
                    next_siblings.find('.ays_previous').remove();
                }

                if(current_fs.hasClass('ays-abs-fs')){
                    current_fs = $(this).parents('.step');
                    next_fs = $(this).parents('.step').next();
                    current_fs.removeClass('active-step');
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);
                }

                var nextQuestionType = next_siblings.eq(next_siblings.length-1).find('input[name^="ays_questions"]').attr('type');
                var buttonsDiv = next_siblings.eq(next_siblings.length-1).find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }

                if ( ! enable_early_finish ) {
                    buttonsDiv.find('i.ays_early_finish').addClass('ays_display_none');
                    buttonsDiv.find('input.ays_early_finish').addClass('ays_display_none');
                }
                
                var nextArrowIsDisabled = buttonsDiv.find('.ays_next_arrow').hasClass('ays_display_none');
                var nextButtonIsDisabled = buttonsDiv.find('.ays_next').hasClass('ays_display_none');

                if(next_siblings.eq(next_siblings.length-1).find('textarea[name^="ays_questions"]').attr('type')==='text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(next_siblings.eq(next_siblings.length-1).find('textarea[name^="ays_questions"]').attr('type') === 'text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }


                if(nextQuestionType === 'checkbox' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'checkbox' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'matching' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'matching' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                
                var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;

                if(isRequiredQuestion === true && !$(this).hasClass('ays-quiz-after-timer-end')){
                    if(next_siblings.find('.information_form').length === 0){
                        var existsEmtpyQuestions = ays_quiz_is_question_required( current_fs );

                        if( ! next_siblings.hasClass('ays-filled-required-qestions') ){
                            if(enableArrows){
                                buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                            }else{
                                buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                            }
                        }

                        if ( next_siblings.eq( next_siblings.length - 1 ).hasClass('ays-custom-step') ) {
                            var customQuestionStep = next_siblings.eq( next_siblings.length - 1 );
                            if(enableArrows){
                                customQuestionStep.find('i.ays_next_arrow').removeAttr('disabled');
                                buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                            }else{
                                customQuestionStep.find('input.ays_next').removeAttr('disabled');
                                buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                            }
                        }

                        if( existsEmtpyQuestions === false ){
                            return false;
                        }else{
                            current_fs.addClass('ays-filled-required-qestions');
                        }
                    } else if ( next_siblings.find('.information_form').length > 0 ) {
                        if ( !($(this).hasClass('start_button')) && quiz_display_all_questions && !enableNextButton ) {
                            var quizContainer = $(e.target).parents('.ays-quiz-container');
                            var existsEmtpyQuestions = ays_quiz_is_question_required( quizContainer.find('div[data-question-id]') );
                            
                            if( ! next_siblings.hasClass('ays-filled-required-qestions') ){
                                if(enableArrows){
                                    buttonsDiv.find('i.ays_next_arrow.ays_finish').prop('disabled', false);
                                }else{
                                    buttonsDiv.find('input.ays_next.ays_finish').prop('disabled', false);
                                }
                            }

                            if( existsEmtpyQuestions === false ){
                                return false;
                            }else{
                                current_fs.addClass('ays-filled-required-qestions');
                            }
                        }
                    }
                }

                if (!($(this).hasClass('start_button'))) {
                    var minSelHasError = 0;
                    var minSelQuestions = next_siblings;
                   // if(($(this).hasClass('ays_finish')) == false){
                   //     minSelQuestions = next_siblings;
                   // }else{
                        minSelQuestions = current_fs;
                   // }

                    for( var k = 0; k < minSelQuestions.length; k++ ){
                        if( $( minSelQuestions[k] ).find('.enable_min_selection_number').length > 0 ){
                            var checkedMinSelCount = aysCheckMinimumCountCheckbox( $( minSelQuestions[k] ), myQuizOptions );
                            if( ays_quiz_is_question_min_count( $( minSelQuestions[k] ), !checkedMinSelCount ) === true ){
                                if( checkedMinSelCount == true ){
                                    if(enableArrows){
                                        buttonsDiv.find('i.ays_next_arrow').removeAttr('disabled');
                                        buttonsDiv.find('i.ays_next_arrow').prop('disabled', false);
                                    }else{
                                        buttonsDiv.find('input.ays_next').removeAttr('disabled');
                                        buttonsDiv.find('input.ays_next').prop('disabled', false);
                                    }
                                }else{
                                    if(enableArrows){
                                        buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                                        buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                                    }else{
                                        buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                                        buttonsDiv.find('input.ays_next').prop('disabled', true);
                                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                                    }
                                    minSelHasError++;
                                }
                            }else{
                                if(enableArrows){
                                    buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                                    buttonsDiv.find('i.ays_next_arrow').prop('disabled', true);
                                    buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                                }else{
                                    buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                                    buttonsDiv.find('input.ays_next').prop('disabled', true);
                                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                                }
                                minSelHasError++;
                            }
                        }
                    }

                    if( minSelHasError > 0 ){
                        return false;
                    }
                }

                ays_quiz_container.find('.active-step').removeClass('active-step');
                next_siblings.eq(0).addClass('active-step');
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_siblings);

                if( !quizDisableInputFocusing ){
                    next_siblings.eq(0).find('.ays-text-input:first-child').trigger( "focus" );

                    next_siblings.eq(0).find('.ays-text-input:first-child').trigger( "focus" );
                    if ( ! next_siblings.eq(0).find('.ays-text-input:first-child').is(":focus") ) {
                        setTimeout(function(e){
                            next_siblings.eq(0).find('.ays-text-input:first-child').trigger( "focus" );
                        },1001);
                    }
                }

                setTimeout(function(){
                    if(next_siblings.find('.ays-text-field').length > 0){
                        if(next_siblings.find('.ays-text-field').width() < 250){
                            next_siblings.find('.ays-text-field').css({
                                'flex-wrap': 'wrap',
                                'justify-content': 'center',
                                'padding': '5px'
                            });
                            next_siblings.find('.ays-text-field').find('input.ays-text-input').css('margin-bottom', '5px');
                        }
                    }
                },2000);
            }else{
                current_fs = $(this).parents('.step');
                next_fs = $(this).parents('.step').next();

                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = next_fs.data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]').parent();
                    aysThisQuizBullets._navPage(thisBullet.index());
                }

                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]'))+1;
                if(questions_count === curent_number){
                    if(current_fs.hasClass('.information_form').length !== 0){
                        current_fs.find('.ays_next').addClass('ays_timer_end');
                        current_fs.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp(500);
                        $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                    }

                    var timer2 = parseInt(ays_quiz_container.find('div.ays-quiz-timer').attr('data-timer'));
                    if (!isNaN(timer2) && myOptions.timer !== undefined) {
                        if (myOptions.timer === timer2 && timer2 !== 0) {
                            var myStartDate = new Date(Date.now() - aysDurationInSeconds * AYS_MS_PER_SECONDS);
                            var countdownEndTime = myStartDate.aysCustomFormat( "#YYYY#-#MM#-#DD# #hhhh#:#mm#:#ss#" );
                            $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(countdownEndTime);
                        } else {
                            $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                        }
                    } else {
                        $(this).parents('.ays-quiz-container').find('input.ays-quiz-end-date').val(GetFullDateTime());
                    }
                }
                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+1) / questions_count * 100) + "%";
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                if (current_fs.hasClass('ays-abs-fs')) {
                    current_fs = $(this).parents('.step');
                    next_fs = $(this).parents('.step').next();
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);
                }
                current_fs.removeClass('active-step');
                next_fs.addClass('active-step');
                var nextQuestionType = next_fs.find('input[name^="ays_questions"]').attr('type');
                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();

                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }
                var nextArrowIsDisabled = buttonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none');
                var nextButtonIsDisabled = buttonsDiv.find('input.ays_next').hasClass('ays_display_none');

                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                }


                if(nextQuestionType === 'checkbox' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'checkbox' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'number' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   next_fs.find('.ays_buttons_div').find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && enableArrows){
                   next_fs.find('.ays_buttons_div').find('input.ays_next').addClass('ays_display_none');
                   next_fs.find('.ays_buttons_div').find('i.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'date' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }
                if(next_fs.data('type') === 'matching' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(next_fs.data('type') === 'matching' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
                var checkUserAnswered = false;
                if (next_fs.find('.ays-quiz-answers .ays-field textarea').length) {
                    nextQuestionType = 'text';
                }

                if (next_fs.data('type') === 'matching') {
                    nextQuestionType = 'matching';
                } else if (next_fs.find('.ays-quiz-answers .ays-field select.ays-select').length) {
                    nextQuestionType = 'select';
                }
                
                if ( next_fs.hasClass('ays-custom-step') ) {
                    nextQuestionType = 'custom';
                }

                switch (nextQuestionType){
                    case "radio":
                        checkUserAnswered = next_fs.find('.ays-quiz-answers .ays-field input[type="'+ nextQuestionType +'"]').is(':checked');
                    break;
                    case "checkbox":
                        checkUserAnswered = next_fs.find('.ays-quiz-answers .ays-field input[type="'+ nextQuestionType +'"]').is(':checked');
                    break;
                    case "number":
                        var questionTypeVal = next_fs.find('.ays-quiz-answers .ays-field input[type="'+ nextQuestionType +'"]').val().length;
                        if (questionTypeVal !== 0) {
                            checkUserAnswered = true;
                        }
                    break;
                    case "text":
                        var questionTypeVal = next_fs.find('.ays-quiz-answers .ays-field textarea, .ays-quiz-answers .ays-field input[type="text"]').val();
                        if (questionTypeVal !== '') {
                            checkUserAnswered = true;
                        }
                    break;
                    case "date":
                        var questionTypeVal = next_fs.find('.ays-quiz-answers .ays-field input[type="'+ nextQuestionType +'"]').val().length;
                        if (questionTypeVal !== 0) {
                            checkUserAnswered = true;
                        }
                    break;
                    case "select":
                        var questionTypeEl = next_fs.find('.ays-quiz-answers .ays-field select.ays-select option[data-chisht]');
                        questionTypeEl.each(function(){
                            var questionTypeVal = $(this).attr('selected');
                            if (questionTypeVal) {
                                checkUserAnswered = true;
                            }
                        });
                    break;
                    case "matching":
                        var questionTypeEl = next_fs.find('.ays-quiz-answers .ays-field select.ays-select option');
                        var checkedCount = 0;
                        var choicesCount = next_fs.find('.ays-quiz-answers .ays-field.ays-matching-field-option').length;
                        questionTypeEl.each(function(){
                            var questionTypeVal = $(this).attr('selected');
                            if (questionTypeVal) {
                                checkedCount++;
                            }
                        });
                        if( checkedCount === choicesCount ) {
                            checkUserAnswered = true;
                        }
                    break;
                    case "custom":
                        checkUserAnswered = true;
                    break;
                    default:
                        checkUserAnswered = next_fs.find('.ays-quiz-answers .ays-field input[type="'+ nextQuestionType +'"]').prop('ckecked');
                    break;
                }
                if(isRequiredQuestion === true){
                    if(next_fs.find('.information_form').length === 0){
                        var newNextQuestionType = next_fs.attr('data-type');

                        if(enableArrows){
                            if (! checkUserAnswered) {
                                buttonsDiv.find('i.ays_next_arrow').attr('disabled', 'disabled');
                            }else{
                                if( nextQuestionType == 'custom' ){
                                    buttonsDiv.find('i.ays_next').removeAttr('disabled');
                                    buttonsDiv.find('i.ays_finish').removeAttr('disabled');
                                }
                                if( newNextQuestionType !== "fill_in_blank" ){
                                    buttonsDiv.find('i.ays_early_finish').removeAttr('disabled');
                                }
                            }
                        }else{
                            if (! checkUserAnswered) {
                                buttonsDiv.find('input.ays_next').attr('disabled', 'disabled');
                            }else{
                                if( nextQuestionType == 'custom' ){
                                    buttonsDiv.find('input.ays_next').removeAttr('disabled');
                                    buttonsDiv.find('input.ays_finish').removeAttr('disabled');
                                }
                                if( newNextQuestionType !== "fill_in_blank" ){
                                    buttonsDiv.find('input.ays_early_finish').removeAttr('disabled');
                                }
                            }
                        }
                    }
                }

                var isAnimate = true;
                var timerType = myOptions.quiz_timer_type ? myOptions.quiz_timer_type : 'quiz_timer';
                if( isRequiredQuestion && myOptions.enable_timer && myOptions.enable_timer === 'on' && timerType === 'question_timer' ) {
                    var qid = checkQuestionTimer(ays_quiz_container, quizId);
                    if ($(this).hasClass('ays_finish') && qid !== false) {
                        isAnimate = false;
                    }
                }

                if ( isAnimate ) {
                    aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
                }

                if( !quizDisableInputFocusing  ){
                    next_fs.find('.ays-text-input:first-child').trigger( "focus" );
                    if ( ! next_fs.find('.ays-text-input:first-child').is(":focus") ) {
                        setTimeout(function(e){
                            next_fs.find('.ays-text-input:first-child').trigger( "focus" );
                        },1001);
                    }
                }

                setTimeout(function(){
                    if(next_fs.find('.ays-text-field').length > 0){
                        if(next_fs.find('.ays-text-field').width() < 250){
                            next_fs.find('.ays-text-field').css({
                                'flex-wrap': 'wrap',
                                'justify-content': 'center',
                                'padding': '5px'
                            });
                            next_fs.find('.ays-text-field').find('input.ays-text-input').css('margin-bottom', '5px');
                        }
                    }
                },2000);
            }
            
            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top || $(this).parents('.ays-questions-container').offset().top > 500){
                ays_quiz_container.goTo( myOptions );
            }
            if(current_fs.find('audio').length > 0){
                current_fs.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(current_fs.find('video').length > 0){
                current_fs.find('video').each(function(e, el){
                    el.pause();
                });
            }

            //Current
            if(current_fs.find('audio').length > 0){
                var sound_src = next_fs.find('audio').attr('src');
                if (typeof sound_src !== 'undefined'){
                    var audio = next_fs.find('audio').get(0);
                    audio.pause();
                    audio.currentTime = 0;
                }
            }

            //Next
            var enableAudioAutoplay = (myOptions.enable_audio_autoplay && myOptions.enable_audio_autoplay == 'on') ? true : false;
            if(next_fs.find('audio').length > 0){
                if(enableAudioAutoplay){
                    var sound_src = next_fs.find('audio').attr('src');
                    if (typeof sound_src !== 'undefined'){
                        var audio = next_fs.find('audio').get(0);
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            }

            //Custom type
            //Checking Next button and Arrows filelds
            var enableNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == 'off') ? true : false;
            var enableArrows = (myOptions.enable_arrows && myOptions.enable_arrows == 'on') ? true : false;
            if(enableNextButton){
                if(next_fs.hasClass('ays-custom-step')){
                    if (enableArrows) {
                        next_fs.find('.ays_buttons_div i.ays_next.action-button.ays_arrow').removeClass('ays_display_none');
                    }else{
                        next_fs.find('.ays_buttons_div input.ays_next.action-button').removeClass('ays_display_none');
                    }
                }
            }

            setTimeout(function (){
                questionTimer( next_fs, next_fs.data('questionId') );
            }, 1);
            aysResizeiFrame();
        });


        $(document).find('.ays_questions_nav_question').on('click', function(e){
            e.preventDefault();
            var questionId = $(this).data('id');
            ays_quiz_container = $(this).parents(".ays-quiz-wrap").find(".ays-quiz-container");

            if (animating) return false;
            animating = true;
            current_fs = ays_quiz_container.find('div.active-step[data-question-id]');
            next_fs = ays_quiz_container.find('div[data-question-id="'+questionId+'"]');

            var quizDisableInputFocusing = (myOptions.quiz_disable_input_focusing && myOptions.quiz_disable_input_focusing == "on") ? true : false;

                current_fs.removeClass('active-step');
                next_fs.addClass('active-step');
                var nextQuestionType = next_fs.find('input[name^="ays_questions"]').attr('type');
                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();

                var questions_count = $(this).parents('.ays-quiz-wrap').find('div[data-question-id]').length;
                var curent_number = $(this).parents('.ays-quiz-wrap').find('div[data-question-id]').index( ays_quiz_container.find('div[data-question-id="'+questionId+'"]') );

                var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                var final_width = ((curent_number+1) / questions_count * 100) + "%";

                if( ays_quiz_container.find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                    ays_quiz_container.find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                }else{
                    ays_quiz_container.find('.ays-live-bar-percent').text(parseInt(final_width));
                }
                ays_quiz_container.find('.ays-live-bar-fill').animate({'width': final_width}, 1000);

                if ( next_fs.hasClass('ays-custom-step') ) {
                    nextQuestionType = 'custom';
                }

                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }
                var nextArrowIsDisabled = buttonsDiv.find('.ays_next_arrow').hasClass('ays_display_none');
                var nextButtonIsDisabled = buttonsDiv.find('.ays_next').hasClass('ays_display_none');

                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(next_fs.find('textarea[name^="ays_questions"]').attr('type')==='text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }


                if(nextQuestionType === 'checkbox' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'checkbox' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'number' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'number' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'short_text' && nextArrowIsDisabled && nextButtonIsDisabled){
                   buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'short_text' && enableArrows){
                   buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                   buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(nextQuestionType === 'date' && nextArrowIsDisabled && nextButtonIsDisabled){
                    buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                }
                if(nextQuestionType === 'date' && enableArrows){
                    buttonsDiv.find('input.ays_next').addClass('ays_display_none');
                    buttonsDiv.find('.ays_next_arrow').removeClass('ays_display_none');
                }

                if(enableArrows){
                    if( nextQuestionType == 'custom' ){
                        buttonsDiv.find('i.ays_next').removeAttr('disabled');
                        buttonsDiv.find('i.ays_finish').removeAttr('disabled');
                        buttonsDiv.find('i.ays_early_finish').removeAttr('disabled');
                    }
                }else{
                    if( nextQuestionType == 'custom' ){
                        buttonsDiv.find('input.ays_next').removeAttr('disabled');
                        buttonsDiv.find('input.ays_finish').removeAttr('disabled');
                        buttonsDiv.find('input.ays_early_finish').removeAttr('disabled');
                    }
                }

                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);

                if( !quizDisableInputFocusing ){
                    next_fs.find('.ays-text-input:first-child').trigger( "focus" );
                }

                setTimeout(function(){
                    if(next_fs.find('.ays-text-field').length > 0){
                        if(next_fs.find('.ays-text-field').width() < 250){
                            next_fs.find('.ays-text-field').css({
                                'flex-wrap': 'wrap',
                                'justify-content': 'center',
                                'padding': '5px'
                            });
                            next_fs.find('.ays-text-field').find('input.ays-text-input').css('margin-bottom', '5px');
                        }
                    }
                },2000);
            // }

            // if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top || $(this).parents('.ays-questions-container').offset().top > 500){
            //     ays_quiz_container.goTo();
            // }
            if(current_fs.find('audio').length > 0){
                current_fs.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(current_fs.find('video').length > 0){
                current_fs.find('video').each(function(e, el){
                    el.pause();
                });
            }

            questionTimer( next_fs, next_fs.data('questionId') );
        });

        $(document).find('.ays_previous').on("click", function(e){
            ays_quiz_container = $(this).parents(".ays-quiz-container");

            var enableArrows = ays_quiz_container.find(".ays-questions-container .ays_qm_enable_arrows").val();
            if(myOptions.enable_arrows){
                enableArrows = myOptions.enable_arrows == 'on' ? true : false;
            }else{
                enableArrows = parseInt(enableArrows) == 1 ? true : false;
            }

            if(typeof explanationTimeout != 'undefined'){
                clearTimeout(explanationTimeout);

                var thisButtonsDiv = $(this).parents(".ays_buttons_div");
                setTimeout(function(){
                    if (thisButtonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                        thisButtonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        if(enableArrows){
                            thisButtonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                        }else{
                            thisButtonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        }
                    }
                }, 1000);
            }

            if(animating) return false;
            animating = true;

            var next_sibilings_count = ays_quiz_container.find('.ays_question_count_per_page').val();

            var isQuestionPerPageCustom = (myOptions.question_count_per_page_type && myOptions.question_count_per_page_type == "custom") ? true : false;
            var customPerPageValue = (myOptions.question_count_per_page_custom_order && myOptions.question_count_per_page_custom_order != "") ? myOptions.question_count_per_page_custom_order : "";
            var quizDisableInputFocusing = (myOptions.quiz_disable_input_focusing && myOptions.quiz_disable_input_focusing == "on") ? true : false;

            if( isQuestionPerPageCustom && customPerPageValue != ""){
                var custom_next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page_custom_next').val();
                var custom_prev_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val();
                var questions_count_new = $(this).parents('form').find('div[data-question-id]').length;
                if( typeof custom_prev_sibilings_count != "undefined" && custom_prev_sibilings_count != "" ){
                    var custom_prev_sibilings_count_arr = custom_prev_sibilings_count.split(',');
                    var custom_next_sibilings_count_arr = custom_next_sibilings_count.split(',');
                    if (custom_prev_sibilings_count_arr !== undefined && custom_prev_sibilings_count_arr.length > 0) {
                        var pop_number = custom_prev_sibilings_count_arr.pop();
                        next_sibilings_count = parseInt( custom_prev_sibilings_count_arr[ custom_prev_sibilings_count_arr.length - 1 ] );

                        $(this).parents('form').find('.ays_question_count_per_page').val(next_sibilings_count)

                        if( typeof custom_prev_sibilings_count == 'undefined' || custom_prev_sibilings_count == "" ){
                            $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val(next_sibilings_count);
                        } else {
                            var custom_prev_sibilings_count_arr = custom_prev_sibilings_count.split(",");
                            custom_prev_sibilings_count_arr.pop();

                            var custom_prev_sibilings_count_arr_new = custom_prev_sibilings_count_arr.join(",");
                            $(this).parents('form').find('.ays_question_count_per_page_custom_prev').val(custom_prev_sibilings_count_arr_new);
                        }

                        custom_next_sibilings_count_arr = custom_next_sibilings_count_arr.filter(word => word != "");

                        if( custom_next_sibilings_count_arr.length > 0 ){
                            custom_next_sibilings_count_arr.unshift(pop_number);
                            var custom_next_sibilings_count_new_val = custom_next_sibilings_count_arr.join(',');
                            $(this).parents('form').find('.ays_question_count_per_page_custom_next').val(custom_next_sibilings_count_new_val);
                        } else {

                            var sum_prev_quesiton = 0;
                            for (var ii = 0; ii < custom_prev_sibilings_count_arr.length; ii++ ) {
                                sum_prev_quesiton += parseInt( custom_prev_sibilings_count_arr[ii] );
                            }
                            var last_page_questions_count = parseInt(questions_count_new) - parseInt(sum_prev_quesiton);
                            if( last_page_questions_count != 0 ) {
                                $(this).parents('form').find('.ays_question_count_per_page_custom_next').val(last_page_questions_count);
                            } else {
                                $(this).parents('form').find('.ays_question_count_per_page_custom_next').val("");
                            }
                        }
                    }
                }
            }

            if(parseInt(next_sibilings_count)>0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))){
                var questions_count = ays_quiz_container.find('div[data-question-id]').length;
                var curent_number_of_this = ays_quiz_container.find('div[data-question-id]').index($(this).parents('div[data-question-id]')) + 1;
                var curent_number = ays_quiz_container.find('div[data-question-id]').index($(this).parents('div[data-question-id]')) - parseInt(next_sibilings_count) + 1;
                var count_per_page = questions_count%parseInt(next_sibilings_count);
                var nextCountQuestionsPerPage = questions_count-curent_number;
                if(count_per_page > 0 && curent_number_of_this == questions_count){
                    curent_number = ays_quiz_container.find('div[data-question-id]').index($(this).parents('div[data-question-id]')) - count_per_page + 1;
                }

                var current_first_fs_index = ays_quiz_container.find('div[data-question-id]').index(ays_quiz_container.find('.active-step').eq(0));
                var next_fs = ays_quiz_container.find('div[data-question-id]').slice((current_first_fs_index - parseInt(next_sibilings_count)), current_first_fs_index);

                var new_current_fs_index = $(this).parents('form').find('.step').index($(this).parents('form').find('.step.active-step'));
                var next_fs_index = $(this).parents('form').find('.step').index(next_fs);

                if (!($(this).hasClass('start_button'))) {
                    var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                    var final_width = ((curent_number) / questions_count * 100) + "%";
                    if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number));
                    }else{
                        $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                    }
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                }
                var current_fs_index = ays_quiz_container.find('div[data-question-id]').index(ays_quiz_container.find('.active-step').eq(0));

                if($(this).parents('.step').attr('data-question-id')){
                    if( isQuestionPerPageCustom && customPerPageValue != ""){
                        if( custom_next_sibilings_count_arr.length == 0 ){
                            current_fs = ays_quiz_container.find('div[data-question-id]').slice(current_fs_index,questions_count);
                        } else {
                            current_fs = ays_quiz_container.find('div[data-question-id]').slice(current_fs_index,current_fs_index+parseInt(pop_number)+1);
                        }
                    } else {
                        current_fs = ays_quiz_container.find('div[data-question-id]').slice(current_fs_index,current_fs_index+parseInt(next_sibilings_count));
                    }

                    // current_fs = ays_quiz_container.find('div[data-question-id]').slice(current_fs_index,current_fs_index+parseInt(next_sibilings_count));
                }else{
                    current_fs = $(this).parents('.step');
                }

                var buttonsDiv = next_fs.find('.ays_buttons_div');
                var enableArrows = $(document).find(".ays-questions-container .ays_qm_enable_arrows").val();
                if(myOptions.enable_arrows){
                    enableArrows = myOptions.enable_arrows == 'on' ? true : false;
                }else{
                    enableArrows = parseInt(enableArrows) == 1 ? true : false;
                }

                if (buttonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                    buttonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if(enableArrows){
                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                    }else{
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                    }
                }

                ays_quiz_container.find('div[data-question-id]').eq(current_fs_index).removeClass('active-step');
                next_fs.eq(0).addClass('active-step')
                if ($(this).parents('form').hasClass('enable_correction')) {
                    if (next_fs.find('.correct').length !== 0 || $(this).parents('div[data-question-id]').prev().find('.wrong').length !== 0) {
                        next_fs.find('input[name^="ays_questions"]').on('click',function () {
                            var is_user_answer_correct = $(this).parent().find('.correct').length;
                            var is_user_answer_wrong = $(this).parent().find('.wrong').length;
                            if (is_user_answer_correct !== 0 || is_user_answer_wrong !== 0) {
                                return false;
                            }
                        });
                    }
                }

                if ($(this).parents('form').hasClass('enable_correction')) {
                    next_fs.find('input[name^="ays_questions"]').each(function(){
                        if ($(this).parents('.ays-quiz-answers').find('.correct').length === 0 &&
                            $(this).parents('.ays-quiz-answers').find('.wrong').length === 0 &&
                            $(this).parents('.ays-quiz-answers').find('.ays-answered-text-input').length === 0) {
                            $(this).attr('disabled', false);
                        }
                    });
                }
                
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
                if( !quizDisableInputFocusing ){
                    next_fs.eq(0).find('.ays-text-input:first-child').trigger( "focus" );
                    
                    next_fs.eq(0).find('.ays-text-input:first-child').trigger( "focus" );
                    if ( ! next_fs.eq(0).find('.ays-text-input:first-child').is(":focus") ) {
                        setTimeout(function(e){
                            next_fs.eq(0).find('.ays-text-input:first-child').trigger( "focus" );
                        },1001);
                    }
                }
            }else{
                if ($(this).parents('div[data-question-id]').prev().find('.correct').length === 0 &&
                    $(this).parents('div[data-question-id]').prev().find('.wrong').length === 0 &&
                    $(this).parents('div[data-question-id]').prev().find('.ays-answered-text-input').length === 0) {
                    $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', false);
                }else{
                    $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', true);
                    if( $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('type') == 'checkbox' ){
                        $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"]').attr('disabled', false);
                        $(this).parents('div[data-question-id]').prev().find('input[name^="ays_questions"][type="radio"]').on('click',function () {
                            return false;
                        });
                    }
                }

                current_fs = $(this).parents('.step');
                next_fs = $(this).parents('.step').prev();


                if( aysThisQuizBullets !== null ){
                    var thisQuestionID = next_fs.data('questionId');
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]').parent();
                    aysThisQuizBullets._navPage(thisBullet.index());
                }

                var buttonsDiv = next_fs.find('.ays_buttons_div');

                if (buttonsDiv.find('input.ays_next').hasClass('ays_display_none') &&
                    buttonsDiv.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    if(enableArrows){
                        buttonsDiv.find('i.ays_next_arrow').removeClass('ays_display_none');
                        buttonsDiv.find('i.ays_next_arrow').removeAttr('disabled');
                        buttonsDiv.find('i.ays_early_finish').removeAttr('disabled');
                        buttonsDiv.find('i.ays_next_arrow').removeProp('disabled');
                        buttonsDiv.find('i.ays_early_finish').removeProp('disabled');
                    }else{
                        buttonsDiv.find('input.ays_next').removeClass('ays_display_none');
                        buttonsDiv.find('input.ays_next').removeAttr('disabled');
                        buttonsDiv.find('input.ays_early_finish').removeAttr('disabled');
                        buttonsDiv.find('input.ays_next').removeProp('disabled');
                        buttonsDiv.find('input.ays_early_finish').removeProp('disabled');
                    }
                }

                if (current_fs.hasClass('ays-abs-fs')) {
                    current_fs = $(this).parents('.step');
                    next_fs = $(this).parents('.step').prev();
                    var counterClass = "";
                    switch(ays_quiz_container.data('questEffect')){
                        case "shake":
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                        case "fade":
                            counterClass = "fadeIn";
                        break;
                        case "none":
                            counterClass = "";
                        break;
                        default:
                            counterClass = ays_quiz_container.data('questEffect');
                        break;
                    }
                    next_fs.find('.ays-question-counter').addClass(counterClass);
                }
                current_fs.removeClass('active-step');
                next_fs.addClass('active-step');

                var questions_count = $(this).parents('form').find('div[data-question-id]').length;
                var curent_number = $(this).parents('form').find('div[data-question-id]').index($(this).parents('div[data-question-id]'))-1;
                if(curent_number != questions_count){
                    if(($(this).hasClass('ays_finish')) == false){
                        if (!($(this).hasClass('start_button'))) {
                            var current_width = $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').width();
                            var final_width = ((curent_number+1) / questions_count * 100) + "%";
                            if($(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(curent_number+1));
                            }else{
                                $(this).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(parseInt(final_width));
                            }
                            $(this).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({'width': final_width}, 1000);
                        }
                    }
                }else{
                    $(this).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300)
                }
                aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);

                if( !quizDisableInputFocusing ){
                    next_fs.find('.ays-text-input:first-child').trigger( "focus" );

                    next_fs.find('.ays-text-input:first-child').trigger( "focus" );
                    if ( ! next_fs.find('.ays-text-input:first-child').is(":focus") ) {
                        setTimeout(function(e){
                            next_fs.find('.ays-text-input:first-child').trigger( "focus" );
                        },1001);
                    }
                }
            }
            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top || $(this).parents('.ays-questions-container').offset().top > 500){
                ays_quiz_container.goTo( myOptions );
            }
            if(current_fs.find('audio').length > 0){
                current_fs.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(current_fs.find('video').length > 0){
                current_fs.find('video').each(function(e, el){
                    el.pause();
                });
            }

            //Current
            if(current_fs.find('audio').length > 0){
                var sound_src = next_fs.find('audio').attr('src');
                if (typeof sound_src !== 'undefined'){
                    var audio = next_fs.find('audio').get(0);
                    audio.pause();
                    audio.currentTime = 0;
                }
            }

            //Previous
            var enableAudioAutoplay = (myOptions.enable_audio_autoplay && myOptions.enable_audio_autoplay == 'on') ? true : false;
            if(next_fs.find('audio').length > 0){
                if(enableAudioAutoplay){
                    var sound_src = next_fs.find('audio').attr('src');
                    if (typeof sound_src !== 'undefined'){
                        var audio = next_fs.find('audio').get(0);
                        audio.currentTime = 0;
                        audio.play();
                    }
                }
            }
            questionTimer( next_fs, next_fs.data('questionId') );
            aysResizeiFrame();
        });

        $(document).find('.ays-quiz-container input').on('focus',function () {
            $(window).on('keydown',function (event) {
                if(event.keyCode === 13){
                    return false;
                }
            });
        });

        $(document).find('.ays-quiz-container input').on('blur',function () {
            $(window).off('keydown');
        });
        
        $(document).on('click', '.ays-quiz-container .ays_question_hint', function (e) {
            e.preventDefault();
            $(e.target).parents('.ays-quiz-container').find('.ays_music_sound').toggleClass('z_index_0');
            $(e.target).parent().find('.ays_question_hint_text').toggleClass('show_hint');
            if($(e.target).parent().find('.ays_question_hint_text').hasClass('show_hint')){
                $(window).on('click', function(ev){
                    if( ! ( $(ev.target).hasClass('ays_question_hint_text') || $(ev.target).hasClass('ays_question_hint') ) ){
                        $(e.target).parent().find('.ays_question_hint_text').removeClass('show_hint')
                        $(e.target).parents('.ays-quiz-container').find('.ays_music_sound').removeClass('z_index_0');
                    }
                });
            }
        });

        $(document).on('click', '.ays-field', function() {
            if ($(this).find(".select2").hasClass('select2-container--open')) {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_down');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_up');
            } else {
                $(this).find('b[role="presentation"]').removeClass('ays_fa ays_fa_chevron_up');
                $(this).find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
            }
        });

        $(document).find('select.ays-select').on("select2:selecting", function(e){
            $(this).parents('.ays-quiz-container').find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
        });
        
        $(document).find('select.ays-select').on("select2:opening", function(e){
            var _this = $(this)
            setTimeout(function (){
                _this.parents('.ays-quiz-container').css('z-index', 1);
                _this.parents('.step').css('z-index', 1);
            }, 1);
        });

        $(document).find('select.ays-select').on("select2:closing", function(e){
            $(this).parents('.ays-quiz-container').find('b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');
            $(this).parents('.ays-quiz-container').css('z-index', 'initial');
            $(this).parents('.step').css('z-index', 'initial');
        });
        
        $(document).find('select.ays-select').on("select2:select", function(e){
            var quizContainer = $(e.target).parents('.ays-quiz-container');
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            var _this = $(this);
            var parentStep = _this.parents('.step');
            var questionId = parentStep.data('questionId');
            var questionType = parentStep.data('type');

            var right_answer_sound = quizContainer.find('.ays_quiz_right_ans_sound').get(0);
            var wrong_answer_sound = quizContainer.find('.ays_quiz_wrong_ans_sound').get(0);
            var finishAfterWrongAnswer = (myOptions.finish_after_wrong_answer && myOptions.finish_after_wrong_answer == "on") ? true : false;
            var showOnlyWrongAnswer = (myOptions.show_only_wrong_answer && myOptions.show_only_wrong_answer == "on") ? true : false;
            var explanationTime = myOptions.explanation_time && myOptions.explanation_time != "" ? parseInt(myOptions.explanation_time) : 4;
            var next_sibilings_count = quizContainer.find('.ays_question_count_per_page').val();
            var enableNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == 'on') ? true : false;
            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            var thankYouStep = quizContainer.find('div.step.ays_thank_you_fs');
            var infoFormLast = thankYouStep.prev().find('div.information_form');

            // Display all questions on one page
            myOptions.quiz_display_all_questions = ( myOptions.quiz_display_all_questions ) ? myOptions.quiz_display_all_questions : 'off';
            var quiz_display_all_questions = (myOptions.quiz_display_all_questions && myOptions.quiz_display_all_questions == "on") ? true : false;

            var disableQuestions = false;
            if( parseInt( next_sibilings_count ) > 0 || quiz_display_all_questions == true ){
                disableQuestions = true;
            }

            myOptions.quiz_waiting_time = ( myOptions.quiz_waiting_time ) ? myOptions.quiz_waiting_time : "off";
            var quizWaitingTime = (myOptions.quiz_waiting_time && myOptions.quiz_waiting_time == "on") ? true : false;

            myOptions.enable_next_button = ( myOptions.enable_next_button ) ? myOptions.enable_next_button : "off";
            var quizNextButton = (myOptions.enable_next_button && myOptions.enable_next_button == "on") ? true : false;

            if ( quizWaitingTime && !quizNextButton) {
                explanationTime += 2;
            }
            var data = e.params.data;
            var this_select_value = data.id;
            $(this).find("option").removeAttr("selected");
            $(this).find("option[value='"+this_select_value+"']").attr("selected", true);

            var quizWaitingCountDownDate = new Date().getTime() + (explanationTime * 1000);
            var thisAnswerOptions = myQuizOptions[questionId];

            if( questionType === 'matching' ){
                var choice = thisAnswerOptions.question_answer[ this_select_value ];
                var choicesCount = $(e.target).parents('div[data-question-id]').find('.ays-field.ays-matching-field-option').length;
                var answeredChoicesCount = 0; //$(e.target).parents('div[data-question-id]').find('.ays-field.ays-matching-field-option select option:selected').length;
                $(e.target).parents('div[data-question-id]').find('.ays-field.ays-matching-field-option').each( function ( index, item ) {
                    if( $(item).find( 'select.ays-select option[selected]' ).length > 0 ) {
                        answeredChoicesCount++;
                    }
                } );
            }

            $(this).parent().find('.ays-select-field-value').attr("value", questionType === 'matching' ? choice : this_select_value);
            if($(this).parents(".ays-questions-container").find('form[id^="ays_finish_quiz"]').hasClass('enable_correction')) {
                var chishtPatasxan = $(this).find('option[selected="selected"]').data("chisht");
                var answerIsCorrect = chishtPatasxan == 1;

                if( questionType === 'matching' ){
                    var answerId = $(this).parents('.ays-matching-field-match').data('answerId');
                    answerIsCorrect = Number( answerId ) === Number( choice );
                }

                if (answerIsCorrect) {
                    if((right_answer_sound)){
                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                        setTimeout(function(){
                            right_answer_sound.play();
                        }, 10);
                    }
                    $(this).parents('.ays-field').addClass('correct_div checked_answer_div');
                    $(this).parents('.ays-field').find('.select2-selection__rendered').css("background-color", "rgba(0, 128, 0, 0.6)");
                } else {
                    if((wrong_answer_sound)){
                        resetPlaying([right_answer_sound, wrong_answer_sound]);
                        setTimeout(function(){
                            wrong_answer_sound.play();
                        }, 10);
                    }
                    $(this).parents('.ays-field').addClass('wrong_div checked_answer_div');
                    $(this).parents('.ays-field').find('.select2-selection__rendered').css("background-color", "rgba(255, 2, 2, 0.6)");
                    if(showOnlyWrongAnswer === false){
                        var rightAnswerText = '<div class="ays-text-right-answer">';

                        if ( questionType === 'matching' ) {
                            rightAnswerText += $(this).parents('.ays-matching-field-option').find('.ays-matching-field-choice').text();
                            rightAnswerText += ' -> ' + getObjectKey( thisAnswerOptions.question_answer, answerId, 'number' );
                        } else {
                            rightAnswerText += $(this).find('option[data-chisht="1"]').html();
                        }

                        rightAnswerText += '</div>';
                        $(this).parents('.ays-quiz-answers').append(rightAnswerText);
                        $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').css("text-align", "left");
                        $(this).parents('.ays-quiz-answers').find('.ays-text-right-answer').slideDown(500);
                    }
                }
                var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionId + '"]');

                var isProcessToShowRWTexts = false;
                var isProcessToShowColors = false;
                var correctChoicesCount = $(e.target).parents('div[data-question-id]').find('.correct_div').length;
                var wrongChoicesCount = $(e.target).parents('div[data-question-id]').find('.wrong_div').length;
                var ifStatement = answerIsCorrect;
                if( questionType === 'matching' ) {
                    ifStatement = correctChoicesCount >= wrongChoicesCount;
                    answeredChoicesCount = $(e.target).parents('div[data-question-id]').find('.checked_answer_div').length;
                    if( choicesCount === answeredChoicesCount ) {
                        isProcessToShowRWTexts = true;
                        isProcessToShowColors = true;
                    }
                } else {
                    isProcessToShowColors = true;
                }

                if (isProcessToShowColors){
                    if (ifStatement){
                        if( aysThisQuizBullets !== null ){
                            thisBullet.attr('disabled', 'disabled');
                            thisBullet.addClass('ays_quiz_correct_answer');
                            if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                            }
                        }
                    }else{
                        if( aysThisQuizBullets !== null ){
                            thisBullet.attr('disabled', 'disabled');
                            thisBullet.addClass('ays_quiz_wrong_answer');
                            if (!thisBullet.parent().hasClass('ays-quiz-questions-nav-item-last-question')) {
                                thisBullet.parent().addClass('ays_quiz_checked_answer_div');
                            }
                        }
                    }
                }

                parentStep.removeClass('ays-has-error');
                thisBullet.removeClass('ays-has-error');

                if ( questionType === 'matching' ) {
                    if ( isProcessToShowRWTexts === true ) {
                        if (myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')) {
                            if ( correctChoicesCount >= wrongChoicesCount ) {
                                $(e.target).parents('div[data-question-id]').find('.right_answer_text').slideDown(500);
                            } else {
                                $(e.target).parents('div[data-question-id]').find('.wrong_answer_text').slideDown(500);
                            }
                        }
                    }
                } else {
                    if (myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_passing' || myOptions.answers_rw_texts == 'on_both')) {
                        if (answerIsCorrect) {
                            $(e.target).parents('div[data-question-id]').find('.right_answer_text').slideDown(500);
                        } else {
                            $(e.target).parents('div[data-question-id]').find('.wrong_answer_text').slideDown(500);
                        }
                    }
                }

                if(finishAfterWrongAnswer && ! answerIsCorrect ){
                    $(e.target).parents('div[data-question-id]').find('.ays_next').attr('disabled', 'disabled');
                    $(e.target).parents('div[data-question-id]').find('.ays_early_finish').attr('disabled', 'disabled');
                    $(e.target).parents('div[data-question-id]').find('.ays_previous').attr('disabled', 'disabled');
                    if( disableQuestions ){
                        quizContainer.find('div[data-question-id]').css('pointer-events', 'none');
                    }
                    if( aysThisQuizBullets !== null ){
                        aysThisQuizBullets.baseElement.find('.ays_questions_nav_question').each(function(){
                            var thisBullet = $(this);
                            thisBullet.attr('disabled', 'disabled');
                        });
                    }
                }
                if ( questionType !== 'matching' ) {
                    explanationTimeout = setTimeout(function () {
                        if (ifStatement) {
                            if ($(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                $(e.target).parents('div[data-question-id]').find('.ays_next').trigger('click');
                            } else if (quiz_display_all_questions && isRequiredQuestion && !enableNextButton) {
                                var existsEmtpyQuestions = ays_quiz_is_question_empty(quizContainer.find('div[data-question-id]'));
                                if (existsEmtpyQuestions) {
                                    if (infoFormLast.length == 0) {
                                        quizContainer.find('input.ays_finish').trigger('click');
                                    } else {
                                        quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                    }
                                }
                            }
                        } else {
                            if (finishAfterWrongAnswer) {
                                window.aysEarlyFinishConfirmBox[quizId] = true;
                                goToLastPage(e);
                            } else {
                                if ($(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                                    $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                    $(e.target).parents('div[data-question-id]').find('.ays_next').trigger('click');
                                } else if (quiz_display_all_questions && isRequiredQuestion && !enableNextButton) {
                                    var existsEmtpyQuestions = ays_quiz_is_question_empty(quizContainer.find('div[data-question-id]'));
                                    if (existsEmtpyQuestions) {
                                        if (infoFormLast.length == 0) {
                                            quizContainer.find('input.ays_finish').trigger('click');
                                        } else {
                                            quizContainer.find('div[data-question-id] input.ays_next.action-button').trigger('click');
                                        }
                                    }
                                }
                            }
                        }
                    }, explanationTime * 1000);

                    if (quizWaitingTime && !quizNextButton) {
                        window.countdownTimeForShowInterval = setInterval(function () {
                            countdownTimeForShow( parentStep, quizWaitingCountDownDate );
                        }, 1000);
                    }
                }

                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if(showExplanationOn == 'on_passing' || showExplanationOn == 'on_both'){
                    if ( questionType === 'matching' ) {
                        if (isProcessToShowRWTexts === true) {
                            if (!$(this).parents('.step').hasClass('not_influence_to_score')) {
                                $(this).parents('.step').find('.ays_questtion_explanation').slideDown(250);
                            }
                        }
                    }else {
                        if (!$(this).parents('.step').hasClass('not_influence_to_score')) {
                            $(this).parents('.step').find('.ays_questtion_explanation').slideDown(250);
                        }
                    }
                }
                $(this).attr("disabled", true);

                if ( isProcessToShowColors ) {
                    stopQuestionTimer(quizContainer, questionId, quizId);
                }
            }else{
                if ($(this).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none') &&
                    $(this).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                    $(this).parents('div[data-question-id]').find('.ays_next').trigger('click');
                }
                var bulletsStatement = $(this).find('option:selected').length !== 0;
                if ( questionType === 'matching' ) {
                    bulletsStatement = answeredChoicesCount === choicesCount;
                }

                if( bulletsStatement ){
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionId + '"]');
                    thisBullet.parent().addClass('ays-quiz-questions-nav-item-answered');
                }else{
                    var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionId + '"]');
                    thisBullet.parent().removeClass('ays-quiz-questions-nav-item-answered');
                }

            }

            if(isRequiredQuestion === true){
                var buttonsStatement = $(this).find('option:selected').length !== 0;
                if ( questionType === 'matching' ) {
                    buttonsStatement = answeredChoicesCount === choicesCount;
                }

                if( buttonsStatement ){
                    if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                        $(e.target).parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                        $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');
                    }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                        $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                    }
                }else{
                    if (!$(e.target).parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                        $(e.target).parents('div[data-question-id]').find('input.ays_next').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', true);
                    }else if(!$(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        $(e.target).parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', true);
                        $(e.target).parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', true);
                    }
                }
            }

            // $(this).find("option").removeAttr("selected");
            // $(this).find("option[value='"+this_select_value+"']").attr("selected", true);
        });

        function aysSelectDropdownCheck(select){
            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            if(isRequiredQuestion === true){
                if(select.val() != ''){
                    if (!select.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                        select.parents('div[data-question-id]').find('input.ays_next').removeAttr('disabled');
                        select.parents('div[data-question-id]').find('input.ays_early_finish').removeAttr('disabled');
                    }else if(!select.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        select.parents('div[data-question-id]').find('i.ays_next_arrow').removeAttr('disabled');
                        select.parents('div[data-question-id]').find('i.ays_early_finish').removeAttr('disabled');
                    }
                }else{
                    if (!select.parents('div[data-question-id]').find('input.ays_next').hasClass('ays_display_none')){
                        select.parents('div[data-question-id]').find('input.ays_next').attr('disabled', true);
                        select.parents('div[data-question-id]').find('input.ays_early_finish').attr('disabled', true);
                    }else if(!select.parents('div[data-question-id]').find('i.ays_next_arrow').hasClass('ays_display_none')) {
                        select.parents('div[data-question-id]').find('i.ays_next_arrow').attr('disabled', true);
                        select.parents('div[data-question-id]').find('i.ays_early_finish').attr('disabled', true);
                    }
                }
            }
        }

        // var shareButtons = document.querySelectorAll(".ays-share-btn.ays-to-share");
        $(document).on('click', ".ays-share-btn.ays-to-share", function (event){
            var width = 650,
                height = 450;

            event.preventDefault();

            window.open(this.href, quizLangObj.shareDialog, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width='+width+',height='+height+',top='+(screen.height/2-height/2)+',left='+(screen.width/2-width/2));
        });

        // if (shareButtons) {
        //     [].forEach.call(shareButtons, function(button) {
        //         button.addEventListener("click", function(event) {
        //             var width = 650,
        //                 height = 450;

        //             event.preventDefault();

        //             window.open(this.href, quizLangObj.shareDialog, 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width='+width+',height='+height+',top='+(screen.height/2-height/2)+',left='+(screen.width/2-width/2));
        //         });
        //     });
        // }

        $(document).find('.ays-quiz-container').map(function () {
            $(this).find('div[data-question-id]').eq(0).find('.ays_previous').css({'display':'none'});
            var next_sibilings_count = parseInt($(this).find('.ays_question_count_per_page').val());
            if(next_sibilings_count>0){
                $(this).find('div[data-question-id]').eq(next_sibilings_count-1).find('.ays_previous').css({'display':'none'});
            }
        });

        $(document).on('click', '.ays_finish.action-button.ays_arrow', function () {
            $(this).parents('.ays_buttons_div').find('input.ays_next.action-button').trigger('click');
        });

        $(document).find('div[data-question-id]').map(function () {
            $(this).find('.ays-quiz-answers .ays-field').map(function () {
                if($(this).find('label[for^="ays-answer"]').eq(1).find('img').length !== 0){
                    $(this).find('label[for^="ays-answer"]').eq(0).addClass('ays_empty_before_content');
                    if($(this).find('label[for^="ays-answer"]').eq(0).text().length === 0){
                        $(this).find('label[for^="ays-answer"]').eq(0).css('background','transparent');
                    }
                }
            });
        });
        
        $(document).on('click', '.ays_finish.action-button', function () {
            var quizId = $(this).parents(".ays-quiz-container").find('input[name="ays_quiz_id"]').val();
            if ( typeof window.aysEarlyFinishConfirmBox != 'undefined') {
                if ( typeof window.aysEarlyFinishConfirmBox[ quizId ] != 'undefined' ) {
                    if( window.aysSeeResultConfirmBox[ quizId ] == true ){
                        $(this).addClass("ays_timer_end");
                    }
                }
            }
            if (typeof $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page") !== 'undefined') {
                if(! $(this).parents('.ays-quiz-container').find('.step.active-step .ays-abs-fs.ays-end-page').hasClass('information_form')){
                    $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page",false);
                }
            }
        });
        
        $(document).on('click', '.ays_early_finish.action-button', function (e) {
            e.preventDefault();
            var quizId = $(this).parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();

            if (typeof $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page") !== 'undefined') {
                if(! $(this).parents('.ays-quiz-container').find('.step .ays-abs-fs.ays-end-page').hasClass('information_form')){
                    $(this).parents('.ays-quiz-container').find('.ays_next.start_button').attr("data-enable-leave-page",false);
                }
            }
            var confirm;
            if (myOptions.enable_early_finsh_comfirm_box && myOptions.enable_early_finsh_comfirm_box == "off") {
                confirm = true;
            }else{
                confirm = window.confirm(quizLangObj.areYouSure);
            }
            if(confirm){
                clearTimeout(explanationTimeout);
                window.aysEarlyFinishConfirmBox[ quizId ] = true;
                var totalSteps = $(e.target).parents().eq(3).find('div.step').length;
                var currentStep = $(e.target).parents().eq(3).find('div.step.active-step');
                var thankYouStep = $(e.target).parents().eq(3).find('div.step.ays_thank_you_fs');
                var infoFormLast = thankYouStep.prev().find('div.information_form');
                var questions_count = $(e.target).parents('form').find('div[data-question-id]').length;
                $(this).parents('.ays-quiz-container').find('.ays_finish.action-button').addClass("ays_timer_end");
                if($(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(questions_count);
                }else{
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-fill').animate({
                        width: '100%'
                    });
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-percent').text(100);
                }
                currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').slideUp();
                setTimeout(function () {
                    currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                },300);
                if(infoFormLast.length == 0){
                    if (currentStep.hasClass('ays_thank_you_fs') === false) {
                        var steps = totalSteps - 3;
                        $(e.target).parents().eq(3).find('div.step').each(function (index) {
                            if ($(this).hasClass('ays_thank_you_fs')) {
                                $(this).addClass('active-step')
                            }else{
                                $(this).css('display', 'none');
                            }
                        });
                        $(e.target).parents().eq(3).find('input.ays_finish').trigger('click');
                    }
                }else{
                    currentStep.parents('.ays-quiz-container').find('.ays-quiz-timer').parent().hide();
                    $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                    setTimeout(function () {
                        $(e.target).parents('.ays-quiz-container').find('.ays-live-bar-wrap').css('display','none');
                    },300);
                    aysAnimateStep($(e.target).parents('.ays-quiz-container').data('quest-effect'), currentStep, infoFormLast.parent());
                    $(e.target).parents().eq(3).find('div.step').each(function (index) {
                        $(this).css('display', 'none');
                        $(this).removeClass('active-step')
                    });
                    infoFormLast.parent().css('display', 'flex');
                    infoFormLast.parent().addClass('active-step');
                    
                    $(this).parents(".ays-quiz-container").find('.ays_finish.action-button').addClass("ays_timer_end");
                    $(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideUp(500);
                }

                if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top || $(this).parents('.ays-questions-container').offset().top > 500){
                    $(this).parents(".ays-quiz-container").goTo( myOptions );
                }
            }
        });

        $(document).on('click', '.action-button.ays_restart_training_button', function () {
            window.location.href = window.location.href + ( window.location.search ? '&' : '?' ) + 'reset_quiz=1';
        });

        $(document).on('click', '.action-button.ays_restart_button', function () {
            if (window.location != window.parent.location) {
                parent.location.reload();
            } else {
                window.location.reload();
            }
        });

        $(document).on('click', '.action-button.ays-quiz-exit-button', function (e) {
            if (window.location != window.parent.location) {
                var ays_quiz_exit_url = $(this).attr('href');
                if ( typeof ays_quiz_exit_url != "undefined" && ays_quiz_exit_url != "" ) {
                    e.preventDefault();
                    parent.location = ays_quiz_exit_url;
                }
            }
        });
        
        $(document).on('click', '.action-button.ays_clear_answer', function () {
            var $this = $(this);
            var activeStep = $this.parents('.step');
            var inputs = activeStep.find('input[name^="ays_questions[ays-question-"]:checked');
            var checked_answer_divs = activeStep.find('div.ays-field.checked_answer_div');
            var ays_text_field = activeStep.find('div.ays-field.ays-text-field');
            var ays_select_field = activeStep.find('div.ays-field.ays-select-field, div.ays-matching-field');
            inputs.removeAttr('checked');
            inputs.trigger('change');
            checked_answer_divs.removeClass('checked_answer_div');
            ays_text_field.find('.ays-text-input').val('');
            ays_text_field.find('.ays-text-input').trigger('input');
            if(ays_select_field.find('select.ays-select').length > 0){
                ays_select_field.find('select.ays-select').val(null).trigger('change');
                ays_select_field.find('select.ays-select option').removeAttr('selected');
                var thisQuestionID = ays_select_field.parents('div[data-question-id]').data('questionId');
                var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + thisQuestionID + '"]');
                thisBullet.parent().removeClass('ays-quiz-questions-nav-item-answered');
                aysSelectDropdownCheck(ays_select_field.find('select.ays-select'));
            }
        });
        
        $(document).on('click', '.ays_music_sound', function() {
            var $this = $(this);
            var quizCoutainer = $this.parents('.ays-quiz-container');
            var audioEls = $(document).find('audio.ays_quiz_music');
            var soundEls = $(document).find('.ays_music_sound');
            var audioEl = quizCoutainer.find('audio.ays_quiz_music').get(0);
            if($this.hasClass('ays_sound_active')){
                audioEl.volume = 0;
                $this.find('.ays_fa').addClass('ays_fa_volume_off').removeClass('ays_fa_volume_up');
                $this.removeClass('ays_sound_active');
            } else {
                audioEl.volume = 1;
                $this.find('.ays_fa').addClass('ays_fa_volume_up').removeClass('ays_fa_volume_off');
                $this.addClass('ays_sound_active');
            }
        });

        function goToLastPage(e){
            clearTimeout(explanationTimeout);
            clearInterval(window.aysTimerInterval);
            var container = $(e.target).parents('.ays-quiz-container');
            var totalSteps = container.find('div.step').length;
            var currentStep = container.find('div.step.active-step');
            var thankYouStep = container.find('div.step.ays_thank_you_fs');
            var infoFormLast = thankYouStep.prev().find('div.information_form');
            var questions_count = $(e.target).parents('form').find('div[data-question-id]').length;
            if(container.find('.ays-live-bar-percent').hasClass('ays-live-bar-count')){
                container.find('.ays-live-bar-percent').text(questions_count);
            }else{
                container.find('.ays-live-bar-fill').animate({
                    width: '100%'
                });
                container.find('.ays-live-bar-percent').text(100);
            }
            container.find('.ays-quiz-timer').slideUp();
            setTimeout(function () {
                container.find('.ays-quiz-timer').parent().hide();
            },300);

            if(infoFormLast.length == 0){
                if (currentStep.hasClass('ays_thank_you_fs') === false) {
                    var steps = totalSteps - 3;
                    container.find('div.step').each(function (index) {
                        if ($(this).hasClass('ays_thank_you_fs')) {
                            $(this).addClass('active-step')
                        }else{
                            $(this).css('display', 'none');
                        }
                    });
                    container.find('input.ays_finish').trigger('click');
                }
            }else{
                container.find('.ays-quiz-timer').parent().hide();
                container.find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                setTimeout(function () {
                    container.find('.ays-live-bar-wrap').css('display','none');
                },300);
                aysAnimateStep(container.data('quest-effect'), currentStep, infoFormLast.parent());
                container.find('div.step').each(function (index) {
                    $(this).css('display', 'none');
                    $(this).removeClass('active-step')
                });
                infoFormLast.parent().css('display', 'flex');
                infoFormLast.parent().addClass('active-step');

                $(e.target).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideUp(500);
            }
        }

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

        $(document).find('.ays-quiz-container').each(function(){
            var $this = $(this);
            var selectEl = $this.find('select.ays-select');
            selectEl.each(function(){
                $(this).select2({
                    placeholder: quizLangObj.selectPlaceholder,
                    dropdownParent: $(this).parents('.ays-abs-fs'),
                    templateResult: ays_formatState
                });
            });
        });

        $(document).find('.ays-quiz-container b[role="presentation"]').addClass('ays_fa ays_fa_chevron_down');

        function aysResetQuiz ($quizContainer){
            var cont = $quizContainer.find('div[data-question-id]');
            cont.find('input[type="text"], textarea, input[type="number"], input[type="url"], input[type="email"]').each(function(){
                $(this).val('');
            });
            cont.find('select').each(function(){
                $(this).val('');
            });
            cont.find('select.ays-select').each(function(){
                $(this).val(null).trigger('change');
            });
            cont.find('select option').each(function(){
                $(this).removeAttr('selected');
            });
            cont.find('input[type="radio"], input[type="checkbox"]').each(function(){
                $(this).removeAttr('checked');
            });
        }

        window.addEventListener("beforeunload", function (e) {
            var startButton = $(document).find('.ays-quiz-container .ays_next.start_button');
            var flag = false;
            for (var i = 0; i < startButton.length; i++) {
                var startBtn = startButton.eq(i).attr('data-enable-leave-page');
                if(typeof startBtn != undefined && startBtn === 'true'){
                    flag = true;
                    break;
                }
            }

            if(flag){
                event.preventDefault();
                speechSynthesis.cancel();
                return true;
            }else{
                return null;
            }
        });

        window.onbeforeunload =  function (e) {
            var startButton = $(document).find('.ays-quiz-container .ays_next.start_button');
            var flag = false;
            for (var i = 0; i < startButton.length; i++) {
                var startBtn = startButton.eq(i).attr('data-enable-leave-page');
                if(typeof startBtn != undefined && startBtn === 'true'){
                    flag = true;
                    break;
                }
            }
            if(flag){
                event.preventDefault();
                speechSynthesis.cancel();
                return true;
            }else{
                return null;
            }
        }

        if( $(document).find('.ays-quiz-container').length > 0 ){
            var ays_quiz_autostart_interval = setInterval( function() {
                if (document.readyState === 'complete') {
                    var firstQuiz = $(document).find('.ays-quiz-container').first();
                    if( firstQuiz.find('input[name="ays_quiz_id"]').length > 0 ){
                        var fisrtQuizId = firstQuiz.find('input[name="ays_quiz_id"]').val();
                        var fisrtQuizOptions = JSON.parse(atob(window.aysQuizOptions[fisrtQuizId]));

                        var questions_count = firstQuiz.find('div[data-question-id]').length;

                        if(typeof fisrtQuizOptions.enable_autostart == 'undefined'){
                            fisrtQuizOptions.enable_autostart = 'off';
                        }

                        if(typeof fisrtQuizOptions.enable_password == 'undefined'){
                            fisrtQuizOptions.enable_password = 'off';
                        }

                        if( fisrtQuizOptions.enable_autostart == 'on' && fisrtQuizOptions.enable_password == 'off' ){
                            if ( questions_count > 0 ) {
                                setTimeout(function(){
                                    firstQuiz.find('.ays_next.start_button').trigger('click');
                                }, 500);
                            }

                        }
                    }
                    clearInterval(ays_quiz_autostart_interval);
                }
            }, 500);
        }

        $(document).find('.ays_next.start_button.ays_quiz_enable_loader').each(function(e){
            var $this = $(this);
            var container = $(this).parents('.ays-quiz-container');

            var ays_quiz = setInterval( function() {
                if (document.readyState === 'complete') {
                    var startButtonText = quizLangObj.startButtonText;
                    if (startButtonText == null || startButtonText == '' ) {
                        startButtonText = quizLangObj.defaultStartButtonText;
                    }

                    container.find('.ays_quiz_start_button_loader_container').addClass('ays_display_none');
                    if ( $this.hasClass('ays_quiz_enable_loader') ) {
                        $this.removeClass('ays_quiz_enable_loader');
                    }
                    $this.prop('disabled', false);
                    $this.val( startButtonText );
                    clearInterval(ays_quiz);
                }
            }, 500);
        });

        $(document).find('.show_timer_countdown').each(function(e){
            // Countdown date
            var countDownEndDate = $(this).data('timer_countdown');
            var quiz_id = $(this).parents(".ays-quiz-container").attr("id");
            if (countDownEndDate != '' && countDownEndDate != undefined) {
                var showM = $(this).parents('.step').data('messageExist');
                ays_countdown_datetime(countDownEndDate, !showM, quiz_id);
            }
        });

        document.addEventListener('fullscreenchange', function(event) {
            if (!document.fullscreenElement) {
                var eventTarget = event.target
                if( $( eventTarget ).hasClass('ays-quiz-container') ){
                    aysQuizFullScreenDeactivate( eventTarget );
                }
            }
        }, false);

        $(document).on('click', '.ays-quiz-open-full-screen, .ays-quiz-close-full-screen', function() {
            var quiz_container = $(this).parents('.ays-quiz-container').get(0);
            toggleFullscreen(quiz_container);
        });

        $(document).on('click', '.action-button.ays_chain_next_quiz_button', function () {
            if (window.location != window.parent.location) {
                parent.location.reload();
            } else {
                window.location.reload();
            }
        });

        $(document).on('click', '.action-button.ays_chain_see_result_button', function () {
            if (window.location != window.parent.location) {
                parent.location.reload();
            } else {
                window.location.reload();
            }
        });

        $(document).on('change', '.ays-quiz-res-toggle-checkbox', function(){
            var _this  = $(this);
            var parent = _this.parents('.ays_quiz_results');
            var elements = parent.find('.step.ays_question_result');

            if (_this.prop('checked')) {
                if (  elements.hasClass('ays_display_none') ) {
                    elements.removeClass('ays_display_none');
                }
            }else{
                elements.addClass('ays_display_none');             
            }
        });

        $(document).on('keydown', (e) => {
            var keyboardBox = $(document).find(".ays-quiz-container.ays-quiz-keyboard-active");

            if(e.keyCode === 9 && keyboardBox.length > 0){
                $(document).find(".ays-select-field *:not(.dropdown-wrapper,.select2-selection__arrow, .select2-selection__placeholder, .ays_fa.ays_fa_chevron_down)").attr("tabindex" , "0")
            }

            if(e.keyCode === 27){
                var zoomPopup = $(document).find('.ays-quiz-question-image-lightbox-container');
                if( zoomPopup.length > 0 ){
                    zoomPopup.hide();
                }
            }

            if($(e.target).hasClass("ays-quiz-keyboard-active")){
                if (e.keyCode === 32) {
                    e.preventDefault();
                    $(e.target).find('label.ays-quiz-keyboard-label').trigger("click");
                }


                if (e.keyCode === 13) {
                    e.preventDefault();
                    var checked_inputs = $(e.target).parents(".ays-quiz-answers").find('input:checked');
                    if(checked_inputs.length > 0){
                        $(e.target).parents('.step.active-step').find(".ays_next").trigger('click');
                    }
                    else{
                        $(e.target).trigger('click');
                    }

                }
            }
        });

        $(document).on('click', '.ays-image-question-img .ays-quiz-question-image-zoom', function(e) {
            var _this = $(this);

            var dataSrc = _this.attr('data-ays-src');
            var keyboardBox = _this.parents(".ays-quiz-container.ays-quiz-keyboard-active");
            var keyboardClass = "";
            var keyboardTabindex = "";

            if(keyboardBox.length > 0){
                keyboardClass = "ays-quiz-keyboard-active";
                keyboardTabindex = "tabindex='0'";
            }

            if (dataSrc != null && dataSrc != "") {
                var aysImagesOverlayBox = $(document).find('.ays-quiz-question-image-lightbox-container');
                var lightboxContainer = "";
                if (aysImagesOverlayBox.length > 0 )  {
                    var mainDiv = document.querySelector(".ays-quiz-question-image-lightbox-container");
                    var createdImgTag = document.querySelector(".ays-quiz-question-image-lightbox-img");

                    createdImgTag.src = dataSrc;
                    mainDiv.style.display = "flex";
                } else {
                    var bodyTag = document.getElementsByTagName("body")[0];

                    lightboxContainer += '<div class="ays-quiz-question-image-lightbox-container" style="display: flex;">';

                        lightboxContainer += '<div class="ays-quiz-question-image-lightbox-img-box">';
                            lightboxContainer += '<img class="ays-quiz-question-image-lightbox-img" src="'+ dataSrc +'" style="z-index: 102;">';
                        lightboxContainer += '</div>';
                        lightboxContainer += '<span class="ays-quiz-question-image-lightbox-close-button '+ keyboardClass +'" '+ keyboardTabindex +'>×</span>';

                    lightboxContainer += '</div>';

                    $(document).find('html > body').append(lightboxContainer);

                    var mainDiv = $(document).find(".ays-quiz-question-image-lightbox-container");
                    mainDiv.css({
                        'display': 'flex'
                    });
                }
            }
        });


        $(document).on('click', '.ays-quiz-question-image-lightbox-close-button', function() {
            var _this = $(this);
            var parent = _this.parents(".ays-quiz-question-image-lightbox-container");

            parent.css({
                'display': 'none'
            });
        });

        $(document).on('click', '.ays-quiz-question-image-lightbox-container', function(e){
            var modalBox = $(e.target).attr('class');
            var _this = $(this);

            if (typeof modalBox != 'undefined' &&  modalBox == 'ays-quiz-question-image-lightbox-container') {
                _this.css({
                    'display': 'none'
                });
            }
        });

        $(document).on('click', '.ays-navbar-bookmark', function(e) {
            var highlighted = ($(this).attr('highlighted') === 'true');
            var navbarItem = $(this).parents('.ays-quiz-questions-nav-bookmark-box').prev('.ays-quiz-questions-nav-content').find('.ays-quiz-questions-nav-item-active');
            var src = $(this).attr('src');

            if (!highlighted) {
                $(this).attr('highlighted', true).attr('src', src.replace('empty', 'filled'));
                navbarItem.find('.ays-quiz-navbar-highlighted-notice').show();
            } else {
                $(this).attr('highlighted', false).attr('src', src.replace('filled', 'empty'));
                navbarItem.find('.ays-quiz-navbar-highlighted-notice').hide();
            }
        });

        $(document).on('click', '.ays-quiz-open-report-window', function() {
            
            if ($(this).parents('div.step.active-step').length > 0) {
                var question = $(this).parents('div.step.active-step');
            } else {
                var question = $(this).parents('div.step.ays_question_result')

            }

            var questionId = question.attr('data-question-id');

            var reportsModal = $('#ays-quiz-question-report-modal');

            reportsModal.fadeIn(200, function() {
                reportsModal.find('textarea#ays-quiz-question-report-textarea').val('');
                reportsModal.find('input.ays-quiz-report-question-id').val(questionId);
            });
        });
        
        $(document).find('.ays-close-reports-window').on('click', function() {
            $('#ays-quiz-question-report-modal').fadeOut(200);
        });

        function quizTimer( container ){
            container.find('div.ays-quiz-timer').hide(800);
            var timer = parseInt(container.find('div.ays-quiz-timer').attr('data-timer'));
            var pageTitle = $(document).find('title');
            var pageTitleText = $(document).find('title').html();
            var timeForShow = "";

            // Display all questions on one page
            myOptions.quiz_timer_red_warning = ( myOptions.quiz_timer_red_warning ) ? myOptions.quiz_timer_red_warning : 'off';
            var quiz_timer_red_warning = (myOptions.quiz_timer_red_warning && myOptions.quiz_timer_red_warning == "on") ? true : false;

            if (!isNaN(timer) && myOptions.timer !== undefined) {
                if (myOptions.timer === timer && timer !== 0) {
                    timer += 2;
                    if (timer !== undefined) {
                        var countDownDate = new Date().getTime() + (timer * 1000);
                        var timerFlag = false;

                        // Message before timer
                        var quiz_message_before_timer = (myOptions.quiz_message_before_timer && myOptions.quiz_message_before_timer != "") ? ( myOptions.quiz_message_before_timer ) : '';

                        if ( quiz_message_before_timer != '' ) {
                            quiz_message_before_timer = quiz_message_before_timer.replace(/(["'])/g, "\\$1") + " ";
                            $(document).find('html > head').append('<style> #ays-quiz-container-'+ quizId +' div.ays-quiz-timer.ays-quiz-message-before-timer:before{content: "'+ quiz_message_before_timer +'"; }</style>');
                        }

                        window.aysTimerInterval = setInterval(function () {
                            var now = new Date().getTime();
                            var distance = countDownDate - Math.ceil(now/1000)*1000;
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            var sec = seconds;
                            var min = minutes;
                            var hour = hours;
                            if(hours <= 0){
                                hours = null;
                            }else if (hours < 10) {
                                hours = '0' + hours;
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            if (seconds < 10) {
                                seconds = '0' + seconds;
                            }
                            timeForShow =  ((hours==null)? "" : (hours + ":")) + minutes + ":" + seconds;
                            if(distance <=1000){
                                timeForShow =  ((hours==null) ? "" : "00:") + "00:00";
                                container.find('div.ays-quiz-timer').html(timeForShow);
                                if(myOptions.quiz_timer_in_title == 'on'){
                                    pageTitle.html( timeForShow + " - " + pageTitleText );
                                }
                            }else{
                                container.find('div.ays-quiz-timer').html(timeForShow);
                                if(myOptions.quiz_timer_in_title == 'on'){
                                    pageTitle.html( timeForShow + " - " + pageTitleText );
                                }
                            }

                            if ( quiz_timer_red_warning ) {
                                var distanceSec = Math.floor(distance / 1000);
                                var timerPercentage = Math.floor(( timer - distanceSec ) * 100);
                                var percentage = Math.floor( timerPercentage / timer );

                                if ( percentage >= 90 && ! timerFlag ) {
                                    var timerContainer = container.find('section.ays_quiz_timer_container');
                                    timerFlag = true;

                                    if ( ! timerContainer.hasClass( 'ays_quiz_timer_red_warning' ) ) {
                                        timerContainer.addClass( 'ays_quiz_timer_red_warning' );
                                    }
                                }
                            }

                            container.find('.ays_quiz_timer_container').show();
                            container.find('div.ays-quiz-timer').show(500);
                            if(container.find('.ays-quiz-timer').length === 0){
                                clearInterval(window.aysTimerInterval);
                                if(myOptions.quiz_timer_in_title == 'on'){
                                    pageTitle.html( pageTitleText );
                                }
                                container.find('.ays_quiz_timer_container').slideUp(500);
                            }
                            if(container.find('.ays_finish.action-button').hasClass("ays_timer_end") ||
                                container.find('.ays_next.action-button').hasClass("ays_timer_end")){
                                clearInterval(window.aysTimerInterval);
                                if(myOptions.quiz_timer_in_title == 'on'){
                                    pageTitle.html( pageTitleText );
                                }
                                container.find('.ays_quiz_timer_container').slideUp(500);
                            }

                            if(hour == 0 && min == 0 && sec < 1){
                                container.find('.ays_buttons_div > *:not(input.ays_finish)').off('click');
                            }

                            if (distance <= 1) {
                                clearInterval(window.aysTimerInterval);
                                if(! container.find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                                    container.find('.ays_quiz_timer_container').css({
                                        'position': 'static',
                                        'height': '100%',
                                    });
                                    container.find('.ays_quiz_timer_container').show();
                                    container.find('div.ays-quiz-timer').slideUp();
                                    container.find('div.ays-quiz-after-timer').addClass("ays-quiz-timer-end");
                                    container.find('.ays_quiz_timer_container').addClass("ays-quiz-timer-end-for-required");
                                    container.find('div.ays-quiz-after-timer').slideDown(500);
                                }else{
                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                    container.find('.ays_quiz_timer_container').addClass("ays-quiz-timer-end-for-required");
                                }

                                if(myOptions.quiz_timer_in_title == 'on'){
                                    pageTitle.html( pageTitleText );
                                }
                                var totalSteps = container.find('div.step').length;
                                var currentStep = container.find('div.step.active-step');
                                var thankYouStep = container.find('div.step.ays_thank_you_fs');
                                var infoFormLast = thankYouStep.prev().find('div.information_form');

                                var myStartDate = new Date(Date.now() - aysDurationInSeconds * AYS_MS_PER_SECONDS);
                                var countdownEndTime = myStartDate.aysCustomFormat( "#YYYY#-#MM#-#DD# #hhhh#:#mm#:#ss#" );

                                container.find('input.ays-quiz-end-date').val(countdownEndTime);
                                if(infoFormLast.length == 0){
                                    if (currentStep.hasClass('ays_thank_you_fs') === false) {
                                        var steps = totalSteps - 3;
                                        container.find('div.step').each(function (index) {
                                            if ($(this).hasClass('ays_thank_you_fs')) {
                                                $(this).addClass('active-step')
                                            }else{
                                                $(this).css('display', 'none');
                                            }
                                        });
                                        // aysAnimateStep(ays_quiz_container.data('questEffect'), currentStep, ays_quiz_container.find('.step.ays_thank_you_fs'));
                                        var ays_finish_button = container.find('input.ays_finish');
                                        if(ays_finish_button.prop('disabled')){
                                            ays_finish_button.prop('disabled', false);
                                        }
                                        ays_finish_button.addClass('ays-quiz-after-timer-end');
                                        ays_finish_button.trigger('click');
                                    }
                                }else{
                                    if(container.find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                                        container.find('.ays-quiz-timer').parent().slideUp(500);
                                    }
                                    container.find('.ays-live-bar-wrap').removeClass('rubberBand').addClass('bounceOut');
                                    container.find('.ays-live-bar-percent').removeClass('rubberBand').addClass('bounceOut');
                                    setTimeout(function () {
                                        container.find('.ays-live-bar-wrap').css('display','none');
                                        container.find('.ays-live-bar-percent').css('display','none');
                                    },300);
                                    aysAnimateStep(ays_quiz_container.data('questEffect'), currentStep, infoFormLast.parent());
                                    container.find('div.step').each(function (index) {
                                        $(this).css('display', 'none');
                                        $(this).removeClass('active-step')
                                    });
                                    infoFormLast.parent().css('display', 'flex');
                                    infoFormLast.parent().addClass('active-step');
                                    var ays_finish_button = container.find('input.ays_finish');
                                    ays_finish_button.addClass('ays-quiz-after-timer-end');
                                }
                                container.parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').slideUp(500);
                            }
                        }, 1000);
                    }
                } else {
                    alert('Wanna cheat??');
                    if (window.location != window.parent.location) {
                        parent.location.reload();
                    } else {
                        window.location.reload();
                    }
                }
            }
        }

        function questionTimerInit( container ){
            var questions = container.find('div[data-question-id]:not([data-type="custom"])');
            window.aysQuizQuestionTimers[quizId] = {};
            for( var i=0; i < questions.length; i++ ) {
                var questionID = questions[i].dataset.questionId;
                window.aysQuizQuestionTimers[quizId][questionID] = {
                    active: false,
                    timeout: null,
                    timer: null,
                    ended: false,
                    stopped: false,
                    switchTimeout: null
                };
            }
        }

        function checkQuestionTimer( container ){
            if( window.aysQuizQuestionTimers[quizId] ) {
                var questions = container.find('div[data-question-id]');
                var remainingQuestionsEmpty = [];
                for (var i = 0; i < questions.length; i++) {
                    var questionID = questions[i].dataset.questionId;

                    if(
                        window.aysQuizQuestionTimers[quizId][questionID]
                        &&
                        (
                            window.aysQuizQuestionTimers[quizId][questionID].ended === true
                            ||
                            window.aysQuizQuestionTimers[quizId][questionID].stopped === true
                        )
                    ) {
                        continue;
                    } else {
                        if ( ays_quiz_is_question_empty( $(questions[i]) ) === false ) {
                            remainingQuestionsEmpty.push({
                                item: $(questions[i]),
                                id: questionID
                            });
                        }
                    }
                }

                if( remainingQuestionsEmpty.length > 0 ){
                    return remainingQuestionsEmpty[0].id;
                }else{
                    return false;
                }
            }
            return false;
        }

        function stopQuestionTimer( container, questionID, quizId ){
            if( myOptions.enable_timer && myOptions.enable_timer === 'on' ) {
                if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
                    if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
                        if ( window.aysQuizQuestionTimers[quizId][questionID] ) {
                            // clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
                            window.aysQuizQuestionTimers[quizId][questionID].stopped = true;
                            var qid = checkQuestionTimer( container, quizId );
                            if (qid === false) {
                                container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                            }
                        }
                    }
                }
            }
        }

        function questionTimer( questionContainer, questionID ){
            var container = questionContainer.parents('.ays-quiz-container');
            if( ! questionID ){
                return;
            }

            if( myOptions.enable_timer && myOptions.enable_timer !== 'on' ){
                return;
            }

            if (myOptions.quiz_timer_type && myOptions.quiz_timer_type !== 'question_timer') {
                return;
            }

            if ( ! window.aysQuizQuestionTimers || ! window.aysQuizQuestionTimers[quizId] ) {
                return;
            }

            if ( questionContainer.data('type') === 'custom' ) {
                for (var i in window.aysQuizQuestionTimers[quizId]) {
                    clearInterval(window.aysQuizQuestionTimers[quizId][i].timeout);
                    clearInterval(window.aysQuizQuestionTimers[quizId][i].switchTimeout);
                    window.aysQuizQuestionTimers[quizId][i].active = false;
                }

                container.find('div.ays-quiz-after-timer').slideDown(500);
                container.find('div.ays-quiz-timer').slideUp(500);
                container.find('.ays_quiz_timer_container').removeClass("ays-quiz-timer-end-for-required");
                questionContainer.find('.ays-quiz-answers').css({
                    'pointer-events': 'none'
                });
                container.find('.ays_quiz_timer_container').slideUp(500);

                var qid = checkQuestionTimer( container );
                if (qid === false) {
                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                }
            }

            if ( ! window.aysQuizQuestionTimers[quizId][questionID] ) {
                return;
            }

            for ( var i in window.aysQuizQuestionTimers[quizId] ){
                if( window.aysQuizQuestionTimers[quizId][i].active ){
                    clearInterval( window.aysQuizQuestionTimers[quizId][i].timeout );
                    clearInterval( window.aysQuizQuestionTimers[quizId][i].switchTimeout );
                    window.aysQuizQuestionTimers[quizId][i].active = false;
                }
            }

            container.find('div.ays-quiz-timer').html("--:--");
            var timeForShow = "";

            var isRequiredQuestion = myOptions.make_questions_required && myOptions.make_questions_required === "on";
            var enablePrevButton = myOptions.enable_previous_button && myOptions.enable_previous_button === 'on';
            var enableNextButton = myOptions.enable_next_button && myOptions.enable_next_button === 'on';
            var enableArrows = myOptions.enable_arrows && myOptions.enable_arrows === 'on';

            clearInterval(explanationTimeout);

            var qid = checkQuestionTimer( container );
            if (qid === false) {
                container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                container.find('input.ays_finish').addClass('ays_timer_end');
            }

            if ( window.aysQuizQuestionTimers[quizId][questionID].ended === true ) {
                if (enableNextButton) {
                    if (enableArrows) {
                        questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                    } else {
                        questionContainer.find('input.ays_next').removeAttr('disabled');
                    }
                }

                if( isRequiredQuestion ){
                    if (enableArrows) {
                        if (!questionContainer.find('i.ays_next_arrow').hasClass('ays_display_none')){
                            questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                        }
                    } else {
                        if (!questionContainer.find('input.ays_next').hasClass('ays_display_none')){
                            questionContainer.find('input.ays_next').removeAttr('disabled');
                        }
                    }
                }
            }

            if ( myOptions.timer !== undefined ) {
                var timer = Number( myOptions.timer );
                if(
                    window.aysQuizQuestionTimers[quizId][questionID].timeout !== null
                    &&
                    window.aysQuizQuestionTimers[quizId][questionID].timer !== null
                ) {
                    timer = window.aysQuizQuestionTimers[quizId][questionID].timer;
                }

                if( window.aysQuizQuestionTimers[quizId][questionID].ended === false ) {
                    if ( timer <= 0 ){
                        window.aysQuizQuestionTimers[quizId][questionID].ended = true;
                        if (enableNextButton) {
                            if (enableArrows) {
                                questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                            } else {
                                questionContainer.find('input.ays_next').removeAttr('disabled');
                            }
                        }
                    }
                }

                if (
                    window.aysQuizQuestionTimers[quizId][questionID].ended === true
                    &&
                    window.aysQuizQuestionTimers[quizId][questionID].stopped === false
                ) {
                    questionContainer.find('.ays-quiz-answers').css({
                        'pointer-events': 'none'
                    });
                    questionContainer.find('[name^="ays_questions"]').attr('disabled', 'disabled');
                    questionContainer.find('select.ays-select').select2('close');
                    container.find('.ays_quiz_timer_container').removeClass("ays-quiz-timer-end-for-required");
                    container.find('div.ays-quiz-timer').slideUp();

                    if ( ! container.find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text') ) {
                        container.find('.ays_quiz_timer_container').css({
                            'position': 'static',
                            'height': '100%',
                        });

                        container.find('.ays_quiz_timer_container').slideDown(500);
                        container.find('div.ays-quiz-after-timer').slideDown(500);
                    } else {
                        container.find('.ays_quiz_timer_container').slideUp(500);
                        container.find('div.ays-quiz-after-timer').slideUp(500);
                    }
                } else if (
                    window.aysQuizQuestionTimers[quizId][questionID].ended === true
                    &&
                    window.aysQuizQuestionTimers[quizId][questionID].stopped === true
                ) {
                    container.find('.ays_quiz_timer_container').slideUp();
                } else {
                    container.find('div.ays-quiz-after-timer').slideUp();
                    container.find('div[data-question-id="'+ questionID +'"]').find('.ays-quiz-answers').removeAttr('style');
                }

                if ( !isNaN(timer) && timer !== 0 ) {
                    timer += 2;
                    var countDownDate = new Date().getTime() + (timer * 1000);

                    // Message before timer
                    var quiz_message_before_timer = (myOptions.quiz_message_before_timer && myOptions.quiz_message_before_timer !== "") ? (myOptions.quiz_message_before_timer) : '';
                    if (quiz_message_before_timer !== '') {
                        quiz_message_before_timer = quiz_message_before_timer.replace(/(["'])/g, "\\$1") + " ";
                        $(document).find('html > head').append('<style> #ays-quiz-container-' + quizId + ' div.ays-quiz-timer.ays-quiz-message-before-timer:before{content: "' + quiz_message_before_timer + '"; }</style>');
                    }

                    if ( ! window.aysQuizQuestionTimers[quizId][questionID].ended ) {
                        window.aysQuizQuestionTimers[quizId][questionID].active = true;
                        window.aysQuizQuestionTimers[quizId][questionID].timeout = setInterval(function () {

                            if( window.aysQuizQuestionTimers[quizId][questionID].stopped === true ){
                                container.find('div.ays-quiz-after-timer').slideDown(500);
                                container.find('div.ays-quiz-timer').slideUp(500);
                                container.find('.ays_quiz_timer_container').removeClass("ays-quiz-timer-end-for-required");
                                questionContainer.find('.ays-quiz-answers').css({
                                    'pointer-events': 'none'
                                });
                                clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
                                window.aysQuizQuestionTimers[quizId][questionID].active = false;
                                window.aysQuizQuestionTimers[quizId][questionID].ended = true;
                                window.aysQuizQuestionTimers[quizId][questionID].timer = -1;
                                container.find('.ays_quiz_timer_container').slideUp(500);

                                var qid = checkQuestionTimer( container );
                                if (qid === false) {
                                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                }
                                return;
                            }

                            var now = new Date().getTime();
                            var distance = countDownDate - Math.ceil(now / 1000) * 1000;
                            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            window.aysQuizQuestionTimers[quizId][questionID].timer = Math.floor(distance / 1000);

                            if (hours <= 0) {
                                hours = null;
                            } else if (hours < 10) {
                                hours = '0' + hours;
                            }
                            if (minutes < 10) {
                                minutes = '0' + minutes;
                            }
                            if (seconds < 10) {
                                seconds = '0' + seconds;
                            }

                            timeForShow = ((hours == null) ? "" : (hours + ":")) + minutes + ":" + seconds;
                            if (distance <= 1000) {
                                timeForShow = ((hours == null) ? "" : "00:") + "00:00";
                            }

                            container.find('div.ays-quiz-timer').html(timeForShow);
                            container.find('.ays_quiz_timer_container').show(500);

                            container.find('div.ays-quiz-timer').show(500);

                            if (container.find('.ays-quiz-timer').length === 0) {
                                clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
                                window.aysQuizQuestionTimers[quizId][questionID].active = false;
                                container.find('.ays_quiz_timer_container').slideUp(500);
                            }

                            if (distance <= 1) {
                                clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
                                window.aysQuizQuestionTimers[quizId][questionID].active = false;
                                window.aysQuizQuestionTimers[quizId][questionID].ended = true;

                                questionContainer.find('.ays-quiz-answers').css({
                                    'pointer-events': 'none'
                                })

                                if (!container.find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')) {
                                    container.find('.ays_quiz_timer_container').css({
                                        'position': 'static',
                                        'height': '100%',
                                    });

                                    container.find('.ays_quiz_timer_container').show();
                                    container.find('div.ays-quiz-timer').slideUp();
                                    container.find('div.ays-quiz-after-timer').addClass("ays-quiz-timer-end");
                                    container.find('.ays_quiz_timer_container').addClass("ays-quiz-timer-end-for-required");
                                    container.find('div.ays-quiz-after-timer').slideDown(500);
                                } else {
                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                    container.find('.ays_quiz_timer_container').addClass("ays-quiz-timer-end-for-required");
                                }

                                questionContainer.find('[name^="ays_questions"]').attr('disabled', 'disabled');
                                questionContainer.find('[name^="ays_questions"]').trigger('blur');
                                questionContainer.find('select.ays-select').trigger('blur');
                                questionContainer.find('select.ays-select').select2('close');

                                if( isRequiredQuestion === true ){
                                    if( aysThisQuizBullets !== null ) {
                                        var thisBullet = aysThisQuizBullets.baseElement.find('.ays_questions_nav_question[data-id="' + questionID + '"]');
                                        thisBullet.removeClass('ays-has-error');
                                    }
                                    questionContainer.removeClass('ays-has-error');

                                    if( enableNextButton ){
                                        if( enableArrows ){
                                            questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                                        }else{
                                            questionContainer.find('input.ays_next').removeAttr('disabled' );
                                        }

                                        if( myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on' ) {
                                            if ( questionContainer.find('input.ays_next').hasClass('ays_finish') ) {
                                                aysThisQuizQuestionsFirstPass = false;
                                            }

                                            if( aysThisQuizQuestionsFirstPass === false ) {
                                                var qid = checkQuestionTimer(container);
                                                if (qid === false) {
                                                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                                }
                                            }
                                        }

                                    } else {
                                        var isNeedToSwitch = true;
                                        if( enableArrows ){
                                            if (!questionContainer.find('i.ays_next_arrow').hasClass('ays_display_none')){
                                                isNeedToSwitch = false;
                                            }
                                        }else{
                                            if (!questionContainer.find('input.ays_next').hasClass('ays_display_none')){
                                                isNeedToSwitch = false;
                                            }
                                        }

                                        if( myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on' ) {
                                            if ( questionContainer.find('input.ays_next').hasClass('ays_finish') ) {
                                                aysThisQuizQuestionsFirstPass = false;
                                            }

                                            if( aysThisQuizQuestionsFirstPass === false ) {
                                                isNeedToSwitch = false;

                                                var qid = checkQuestionTimer(container);
                                                if (qid !== false) {
                                                    container.find('.ays_questions_nav_question[data-id="' + qid + '"]').trigger('click');
                                                } else {
                                                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                                    questionContainer.css('display', 'none');
                                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                                    container.find('input.ays_finish').trigger('click');
                                                }
                                            }
                                        }
                                        if( isNeedToSwitch ) {
                                            window.aysQuizQuestionTimers[quizId][questionID].switchTimeout = setTimeout(function () {
                                                questionContainer.find('input.ays_next').trigger('click');
                                            }, 1500 );
                                        }
                                    }

                                    if (enableArrows) {
                                        if (!questionContainer.find('i.ays_next_arrow').hasClass('ays_display_none')){
                                            questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                                        }
                                    } else {
                                        if (!questionContainer.find('input.ays_next').hasClass('ays_display_none')){
                                            questionContainer.find('input.ays_next').removeAttr('disabled');
                                        }
                                    }
                                }else{
                                    if( enableNextButton ){
                                        if( enableArrows ){
                                            questionContainer.find('i.ays_next_arrow').removeAttr('disabled');
                                        }else{
                                            questionContainer.find('input.ays_next').removeAttr('disabled' );
                                        }

                                        if( myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on' ) {
                                            if ( questionContainer.find('input.ays_next').hasClass('ays_finish') ) {
                                                aysThisQuizQuestionsFirstPass = false;
                                            }

                                            if( aysThisQuizQuestionsFirstPass === false ) {
                                                var qid = checkQuestionTimer(container);
                                                if (qid === false) {
                                                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                                }
                                            }
                                        }

                                    } else {
                                        var isNeedToSwitch = true;
                                        if (enableArrows) {
                                            if (!questionContainer.find('i.ays_next_arrow').hasClass('ays_display_none')) {
                                                isNeedToSwitch = false;
                                            }
                                        } else {
                                            if (!questionContainer.find('input.ays_next').hasClass('ays_display_none')) {
                                                isNeedToSwitch = false;
                                            }
                                        }

                                        if (myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on') {
                                            if (questionContainer.find('input.ays_next').hasClass('ays_finish')) {
                                                aysThisQuizQuestionsFirstPass = false;
                                            }

                                            if (aysThisQuizQuestionsFirstPass === false) {
                                                isNeedToSwitch = false;

                                                var qid = checkQuestionTimer(container);
                                                if (qid !== false) {
                                                    container.find('.ays_questions_nav_question[data-id="' + qid + '"]').trigger('click');
                                                } else {
                                                    container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                                    questionContainer.css('display', 'none');
                                                    container.find('.ays_quiz_timer_container').slideUp(500);
                                                    container.find('input.ays_finish').trigger('click');
                                                }
                                            }
                                        }

                                        if (isNeedToSwitch) {
                                            window.aysQuizQuestionTimers[quizId][questionID].switchTimeout = setTimeout(function () {
                                                questionContainer.find('input.ays_next').trigger('click');
                                            }, 1500);
                                        }
                                    }

                                    if( myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on' ) {
                                        if ( questionContainer.find('input.ays_next').hasClass('ays_finish') ) {
                                            aysThisQuizQuestionsFirstPass = false;
                                        }

                                        if( aysThisQuizQuestionsFirstPass === false ) {
                                            var qid = checkQuestionTimer(container);
                                            if (qid === false) {
                                                container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
                                            }
                                        }
                                    }
                                }
                            }
                        }, 1000);
                    }
                }
            }
        }
    });

    function show_hide_rows(page) {

        var rows = jQuery('table.ays-add-questions-table tbody tr');
        rows.each(function (index) {
            jQuery(this).css('display', 'none');
        });
        var counter = page * 5 - 4;
        for (var i = counter; i < (counter + 5); i++) {
            rows.eq(i - 1).css('display', 'table-row');
        }
    }

    function createPagination(pagination, pageShow) {
        // (pagination, pageShow)
        function aysPagination(baseElement, pageShow) {
            this.baseElement = baseElement;
            this.pageShow = pageShow
            this.pageNow = 1
            var pages = this.baseElement.parents('.ays-quiz-wrap').find('div[data-question-id]').length;
            var pageNum = 0;
            var pageOffset = 0;

            this._initNav = function() {
                var _this = this;
                if( _this.baseElement.hasClass('ays-quiz-questions-nav-with-controls') ){
                    var pagePos = (this.baseElement.width()/2) - (parseInt(this.baseElement.find('.ays-quiz-questions-nav-item:first-child').css('width'))/2);
                    _this.baseElement.find('div.ays-quiz-questions-nav-content').css({
                        'margin-left': pagePos,
                    });
                }

                //init events
                var toPage;
                _this.baseElement.on('click', '.ays-quiz-questions-nav-item a.ays_questions_nav_question', function (e) {
                    toPage = jQuery(this).parent().index();
                    _this._navPage(toPage);
                });

                if( _this.baseElement.hasClass('ays-quiz-questions-nav-with-controls') ){
                    _this.baseElement.on('click', '.ays-quiz-questions-nav-go-left', function (e) {
                        _this._navScroll(_this.pageNow, 'left');
                    });

                    _this.baseElement.on('click', '.ays-quiz-questions-nav-go-right', function (e) {
                        _this._navScroll(_this.pageNow, 'right');
                    });
                }
            }

            this._navPage = function(toPage) {
                this.pageNow = toPage;
                var sel = jQuery('.ays-quiz-questions-nav-item', this.baseElement), w = sel.first().outerWidth()+10,
                    diff = toPage - pageNum;
                var src = $(document).find('.ays-navbar-bookmark').attr('src');

                if (toPage >= 0 && toPage <= pages - 1) {
                    sel.removeClass('ays-quiz-questions-nav-item-active').eq(toPage).addClass('ays-quiz-questions-nav-item-active');
                    if (sel.eq(toPage).find('.ays-quiz-navbar-highlighted-notice').length > 0) {
                        if (sel.eq(toPage).find('.ays-quiz-navbar-highlighted-notice').css('display') == 'none') {
                            $(document).find('.ays-navbar-bookmark').attr('highlighted', false).attr('src', src.replace('filled', 'empty'));
                        } else {
                            $(document).find('.ays-navbar-bookmark').attr('highlighted', true).attr('src', src.replace('empty', 'filled'));
                        }
                    }

                    this.baseElement.find('[disabled]').removeAttr('disabled');
                    sel.eq(toPage).attr('disabled', 'disabled');
                    sel.eq(toPage).find('a.ays_questions_nav_question').attr('disabled', 'disabled');
                    pageNum = toPage;
                } else {
                    return false;
                }

                if (toPage <= (pages - (this.pageShow + (diff > 0 ? 0 : 1))) && toPage >= 0) {
                    pageOffset = pageOffset + -w * diff;
                } else {
                    pageOffset = (toPage > 0) ? -w * (pages - this.pageShow) : 0;
                }

                if( this.baseElement.hasClass('ays-quiz-questions-nav-with-controls') ){
                    sel.parent().css('left', pageOffset + 'px');
                }
            }

            this._navScroll = function(toPage, dir) {

                if(dir === 'left'){
                    toPage -= 3;
                }

                if(dir == 'right'){
                    toPage += 3;
                }

                if(toPage > pages - 1){
                    toPage = pages - 1;
                }

                if(toPage < 0){
                    toPage = 0;
                }

                this.pageNow = toPage;

                var sel = jQuery('.ays-quiz-questions-nav-item', this.baseElement),
                    w = sel.first().outerWidth()+10,
                    diff = toPage - pageNum;

                if (toPage >= 0 && toPage <= pages - 1) {
                    pageNum = toPage;
                } else {
                    return false;
                }

                if (toPage <= (pages - (this.pageShow + (diff > 0 ? 0 : 1))) && toPage >= 0) {
                    pageOffset = pageOffset + -w * diff;
                } else {
                    pageOffset = (toPage > 0) ? -w * (pages - this.pageShow) : 0;
                }

                sel.parent().css('left', pageOffset + 'px');
            }

            this._initNav();

            return this;
        }
        var bullets = new aysPagination(pagination, pageShow);
        
        return bullets;
    }

})( jQuery );
