(function ($) {
    'use strict';
    $.fn.serializeFormJSON = function () {
        var o = {},
            a = this.serializeArray();
        $.each(a, function () {
            if (o[this.name]) {
                if (!o[this.name].push) {
                    o[this.name] = [o[this.name]];
                }
                o[this.name].push(this.value || '');
            } else {
                o[this.name] = this.value || '';
            }
        });
        return o;
    };
    $(document).ready(function () {
        var current_fs, next_fs, previous_fs; //fieldsets
        var left, opacity, scale; //fieldset properties which we will animate
        var animating; //flag to prevent quick multi-click glitches
        var form, ays_quiz_container, ays_quiz_container_id;

        window.aysQuizParentWindowLink = null;
        if (window.location != window.parent.location) {
            window.parent.postMessage("getParentUrl", "*");
            window.addEventListener("message", receiveMessage, false);
        }

        function receiveMessage(event) {
            if (typeof event.data === "string") {
                window.aysQuizParentWindowLink = event.data;
            }
        }

        if(!$.fn.goTo){
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
        }
        // for details
        $.fn.aysModal = function(action){
            var $this = $(this);
            switch(action){
                case 'hide':
                    $(this).find('.ays-modal-content').css('animation-name', 'zoomOut');
                    setTimeout(function(){
                        $(document.body).removeClass('modal-open');
                        $(document).find('.ays-modal-backdrop').remove();
                        $this.hide();
                    }, 250);
                    break;
                case 'show':
                default:
                    $this.show();
                    $(this).find('.ays-modal-content').css('animation-name', 'zoomIn');
                    $(document).find('.modal-backdrop').remove();
                    $(document.body).append('<div class="ays-modal-backdrop"></div>');
                    $(document.body).addClass('modal-open');
                    break;
            }
        }

        if (!String.prototype.trim) {
            (function() {
                String.prototype.trim = function() {
                    return this.replace(/^[\s\uFEFF\xA0]+|[\s\uFEFF\xA0]+$/g, '');
                };
            })();
        }
        $(document).find(".ays-quiz-container .information_form").each(function(e){
            var $this = $(this);
            var cont = $(document).find(".ays-quiz-container");
            var thisCont = $this.parents('.ays-quiz-container');
            var quizId = thisCont.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            if(myOptions.autofill_user_data && myOptions.autofill_user_data == "on"){
                var userData = {};
                userData.action = 'ays_get_user_information';
                $.ajax({
                    url: quiz_maker_ajax_public.ajax_url,
                    method: 'post',
                    dataType: 'json',
                    crossDomain: true,
                    data: userData,
                    success: function (response) {
                        if(response !== null){
                            $this.find("input[name='ays_user_name']").val(response.data.display_name);
                            $this.find("input[name='ays_user_email']").val(response.data.user_email);
                        }
                    }
                });
            }
        });

        $(document).on('click', '.ays-quiz-rate-link-box .ays-quiz-rate-link', function (e) {
            e.preventDefault();
            var _this  = $(this);
            var parent = _this.parents('.ays-quiz-container');
            var quizId = parent.find('input[name="ays_quiz_id"]').val();
            var form   = parent.find('form');

            var action = 'ays_get_rate_last_reviews';
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                crossDomain: true,
                data: {
                    quiz_id: quizId,
                    action: action
                },
                success: function(response){
                    if(response.status === true){
                        form.find('.quiz_rate_reasons_body').html(response.quiz_rate_html);
                        form.find('.lds-spinner2').addClass('lds-spinner2-none').removeClass('lds-spinner2');
                        form.find('.quiz_rate_reasons_container').slideDown(500);

                        _this.slideUp(500);

                        form.on('click', 'button.ays_load_more_review', function(e){
                            form.find('.quiz_rate_load_more [data-role="loader"]').addClass(form.find('.quiz_rate_load_more .ays-loader').data('class')).removeClass('ays-loader');
                            var startFrom = parseInt($(e.target).attr('startfrom'));
                            var zuyga = parseInt($(e.target).attr('zuyga'));
                            $.ajax({
                                url: quiz_maker_ajax_public.ajax_url,
                                method: 'post',
                                crossDomain: true,
                                data:{
                                    action: 'ays_load_more_reviews',
                                    quiz_id: quizId,
                                    start_from: startFrom,
                                    zuyga: zuyga
                                },
                                success: function(resp){
                                    if(zuyga == 0){
                                        zuyga = 1;
                                    }else{
                                        zuyga = 0;
                                    }

                                    form.find('.quiz_rate_load_more [data-role="loader"]').addClass('ays-loader').removeClass(form.find('.quiz_rate_load_more .ays-loader').data('class'));
                                    form.find('.quiz_rate_reasons_container').append(resp);
                                    form.find('.quiz_rate_more_review:last-of-type').slideDown(500);
                                    $(e.target).attr('startfrom', startFrom + 5 );
                                    $(e.target).attr('zuyga', zuyga);
                                    if(form.find('.quiz_rate_reasons_container p.ays_no_more').length > 0){
                                        $(e.target).remove();
                                    }
                                }
                            });
                        });
                    } else {
                        swal.fire({
                            type: 'info',
                            html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                        });
                    }
                },
                error: function(){
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                    });
                }
            });
        });

        $(document).find('input.ays_finish').on('click', function (e) {
            e.preventDefault();

            var _this = $(this);
            ays_quiz_container_id = $(this).parents(".ays-quiz-container").attr("id");
            ays_quiz_container = $('#'+ays_quiz_container_id);

            var quizId = ays_quiz_container.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            var quizOptionsName = 'quizOptions_'+quizId;
            var myQuizOptions = [];
            
            if(typeof window[quizOptionsName] !== 'undefined'){
                for(var i in window[quizOptionsName]){
                    if(window[quizOptionsName].hasOwnProperty(i)){
                        myQuizOptions[i] = (JSON.parse(window.atob(window[quizOptionsName][i])));
                    }
                }
            }

            var form = ays_quiz_container.find('form');

            myOptions.enable_recaptcha = ( myOptions.enable_recaptcha ) ? myOptions.enable_recaptcha : 'off';
            var quizRecaptcha = myOptions.enable_recaptcha && myOptions.enable_recaptcha == "on" ? true : false;

            var formCaptchaValidation = null;
            if( quizRecaptcha ){
                if( form.attr('data-recaptcha-validate') ){
                    formCaptchaValidation = form.attr('data-recaptcha-validate') == 'true' ? true : false;
                }
            }

            if( quizRecaptcha && formCaptchaValidation === null){
                var cEvent = new CustomEvent('afterQuizSubmission', {
                    detail: {
                        _this: _this,
                        thisButton: $( e.target )
                    }
                });
                form.get(0).dispatchEvent(cEvent);
            }

            if( quizRecaptcha === false ){
                formCaptchaValidation = true;
            }

            if ( formCaptchaValidation !== true ) {
                return;
            }

            if( myOptions.enable_timer && myOptions.enable_timer === 'on' ) {
                if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
                    if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
                        if( myOptions.enable_navigation_bar && myOptions.enable_navigation_bar === 'on' ) {
                            if (myOptions.make_questions_required && myOptions.make_questions_required === "on") {
                                var qid = checkQuestionTimer(ays_quiz_container, quizId);
                                if (qid !== false) {
                                    ays_quiz_container.find('.ays_questions_nav_question[data-id="' + qid + '"]').trigger('click');
                                    ays_quiz_is_question_required( ays_quiz_container.find('.step[data-question-id="' + qid + '"]') );
                                    return;
                                } else {
                                    for ( var questionID in window.aysQuizQuestionTimers[quizId] ) {
                                        stopQuestionTimer( questionID, quizId, myOptions );
                                    }
                                }
                            }else{
                                for ( var questionID in window.aysQuizQuestionTimers[quizId] ) {
                                    stopQuestionTimer( questionID, quizId, myOptions );
                                }
                            }
                        }else{
                            for ( var questionID in window.aysQuizQuestionTimers[quizId] ) {
                                stopQuestionTimer( questionID, quizId, myOptions );
                            }
                        }
                    }
                }
            }

            if( ! $(this).hasClass('ays-quiz-after-timer-end') ){
                var confirm = true;
                myOptions.enable_see_result_confirm_box = ! myOptions.enable_see_result_confirm_box ? 'off' : myOptions.enable_see_result_confirm_box;
                var enable_see_result_confirm_box = (myOptions.enable_see_result_confirm_box && myOptions.enable_see_result_confirm_box == 'on') ? true : false;
                if (enable_see_result_confirm_box) {
                    if ( ! window.aysEarlyFinishConfirmBox[ quizId ] ) {
                        confirm = window.confirm(quizLangObj.areYouSure);
                        window.aysSeeResultConfirmBox[ quizId ] = false;
                    }
                }

                if ( ! confirm ) {
                    window.aysSeeResultConfirmBox[ quizId ] = true;
                    return false;
                }
            }

            if($(document).scrollTop() >= $(this).parents('.ays-questions-container').offset().top){
                ays_quiz_container.goTo(myOptions);
            }

            if(ays_quiz_container.find('.ays_music_sound').length !== 0){
                ays_quiz_container.find('.ays_music_sound').fadeOut();
                setTimeout(function() {
                    audioVolumeOut(ays_quiz_container.find('audio.ays_quiz_music').get(0));
                },4000);
                setTimeout(function() {
                    ays_quiz_container.find('audio.ays_quiz_music').get(0).pause();
                },6000);
            }
            if(ays_quiz_container.find('audio').length > 0){
                ays_quiz_container.find('audio').each(function(e, el){
                    el.pause();
                });
            }
            if(ays_quiz_container.find('video').length > 0){
                ays_quiz_container.find('video').each(function(e, el){
                    el.pause();
                });
            }

            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            var enableNavigationBar = (myOptions.enable_navigation_bar && myOptions.enable_navigation_bar == "on") ? true : false;
            var aysQuestionCountPerPage = $(this).parents('form').find('.ays_question_count_per_page');
            var next_sibilings_count = aysQuestionCountPerPage.length > 0 ? parseInt( aysQuestionCountPerPage.val() ) : null;

            if( ! $(this).hasClass('ays-quiz-after-timer-end') && ! window.aysEarlyFinishConfirmBox[ quizId ] ){
                if(isRequiredQuestion === true && next_sibilings_count !== null ){
                    ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                        return false;
                    }
                } else if (isRequiredQuestion === true && next_sibilings_count === null) {
                    if ( enableNavigationBar ) {
                        ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                        if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                            return false;
                        }
                    }
                }
            }

            ays_quiz_container.find('.ays-live-bar-wrap').addClass('bounceOut');
            setTimeout(function () {
                ays_quiz_container.find('.ays-live-bar-wrap').css('display','none');
            },300);

            var isRequiredQuestion = (myOptions.make_questions_required && myOptions.make_questions_required == "on") ? true : false;
            var aysQuestionCountPerPage = $(this).parents('form').find('.ays_question_count_per_page');
            var next_sibilings_count = aysQuestionCountPerPage.length > 0 ? parseInt( aysQuestionCountPerPage.val() ) : null;

            if( ! $(this).hasClass('ays-quiz-after-timer-end') && ! window.aysEarlyFinishConfirmBox[ quizId ] ){
                if(isRequiredQuestion === true && next_sibilings_count !== null ){
                    ays_quiz_is_question_required( ays_quiz_container.find('.step') );
                    if( ays_quiz_container.hasClass('ays-quiz-has-error') ){
                        return false;
                    }
                }
            }

            var emailValivatePattern = /^[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+\.\w{2,}$/;

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
                                if (required_inputs.eq(i).val() === '' &&
                                    required_inputs.eq(i).attr('type') !== 'hidden') {
                                    required_inputs.eq(i).addClass('ays_red_border');
                                    required_inputs.eq(i).addClass('shake');
                                    empty_inputs++;
                                }else{
                                    required_inputs.eq(i).addClass('ays_green_border');
                                }
                                if(required_inputs.eq(i).attr('name') == "ays_user_email" && !(emailValivatePattern.test(required_inputs.eq(i).val()))){
                                    empty_inputs++;
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
                    if(selectAttr.val() == ''){
                        selectAttr.removeClass('ays_red_border');
                        selectAttr.removeClass('ays_green_border');

                        selectAttr.addClass('ays_red_border');
                        selectAttr.addClass('shake');
                        empty_inputs++;
                    }else{
                        selectAttr.removeClass('ays_red_border');
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
                    }
                }else{
                    if($(this).parents('.step').find('.information_form').length !== 0 ){
                        var empty_inputs = 0;
                        var phoneInput = $(this).parents('.step').find('input[name="ays_user_phone"]');
                        var emailInput = $(this).parents('.step').find('input[name="ays_user_email"]');
                        var emailInputs = $(this).parents('.step').find('input[type="email"]');
                        if(phoneInput.val() != ''){
                            phoneInput.removeClass('ays_red_border');
                            phoneInput.removeClass('ays_green_border');
                            if (!validatePhoneNumber(phoneInput.get(0))){
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
                    }
                }
            }

            var next_sibilings_count = $(this).parents('form').find('.ays_question_count_per_page').val();
            $(e.target).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', false);
            $(e.target).parents().eq(3).find('div.ays-quiz-timer').slideUp(500);
            if($(e.target).parents().eq(3).find('div.ays-quiz-after-timer').hasClass('empty_after_timer_text')){
                $(e.target).parents().eq(3).find('div.ays-quiz-timer').parent().slideUp(500);
            }

            next_fs = $(this).parents('.step').next();
            current_fs = $(this).parents('.step');
            next_fs.addClass('active-step');
            current_fs.removeClass('active-step');
            form = ays_quiz_container.find('form');

            if (!($(this).hasClass('start_button')) && ! window.aysEarlyFinishConfirmBox[ quizId ]) {
                var minSelHasError = 0;
                var buttonsDiv = current_fs.find('.ays_buttons_div');
                var enableArrows = $(this).parents(".ays-questions-container").find(".ays_qm_enable_arrows").val();
                var timerBox = $(this).parents(".ays-quiz-container").find(".ays-quiz-timer div.ays-quiz-after-timer");
                if( ays_quiz_container.find('.step[data-question-id] .enable_min_selection_number').length > 0 ){
                    ays_quiz_container.find('.step[data-question-id] .enable_min_selection_number').each(function(){
                        var MinSelQuestion = $(this).parents('.step[data-question-id]');
                        var checkedMinSelCount = aysCheckMinimumCountCheckbox( MinSelQuestion, myQuizOptions );
                        if( ays_quiz_is_question_min_count( MinSelQuestion, !checkedMinSelCount ) === true ){
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
                    });
                }

                var is_timerEnd = false;
                if(timerBox.hasClass("ays_timer_end")){
                    is_timerEnd = true;
                }

                if( minSelHasError > 0 && is_timerEnd){
                    return false;
                }
            }

            var textAnswers = form.find('div.ays-text-field textarea.ays-text-input');
            for(var i=0; i < textAnswers.length; i++){
                var userAnsweredText = textAnswers.eq(i).val().trim();
                var questionId = textAnswers.eq(i).parents('.step').data('questionId');

                var trueAnswered = false;

                // Enable case sensitive text
                var enable_case_sensitive_text = (myQuizOptions[questionId].enable_case_sensitive_text && myQuizOptions[questionId].enable_case_sensitive_text != "") ? myQuizOptions[questionId].enable_case_sensitive_text : false;

                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();

                if ( enable_case_sensitive_text ) {
                    thisQuestionAnswer = thisQuestionCorrectAnswer;
                }

                thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes().split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if ( enable_case_sensitive_text ) {
                        if(userAnsweredText == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    } else {
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    }
                }

                if(trueAnswered){
                    textAnswers.eq(i).next().val(1);
                }else{
                    textAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        textAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        textAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }
                textAnswers.eq(i).removeAttr('disabled');
            }
            
            var numberAnswers = form.find('div.ays-text-field input[type="number"].ays-text-input');
            for(var i=0; i < numberAnswers.length; i++){
                var userAnsweredText = numberAnswers.eq(i).val().trim();
                var questionId = numberAnswers.eq(i).parents('.step').data('questionId');
                if(userAnsweredText.toLowerCase().replace(/\.([^0]+)0+$/,".$1") === myQuizOptions[questionId].question_answer.toLowerCase().replace(/\.([^0]+)0+$/,".$1")){
                    numberAnswers.eq(i).next().val(1);
                }else{
                    numberAnswers.eq(i).next().val(0);
                    numberAnswers.eq(i).attr('chishtpatasxan', myQuizOptions[questionId].question_answer);
                }
                numberAnswers.eq(i).removeAttr('disabled')
            }
            
            var shortTextAnswers = form.find('div.ays-text-field input[type="text"].ays-text-input');
            for(var i=0; i < shortTextAnswers.length; i++){
                var userAnsweredText = shortTextAnswers.eq(i).val().trim();
                var questionId = shortTextAnswers.eq(i).parents('.step').data('questionId');

                var trueAnswered = false;

                // Enable case sensitive text
                var enable_case_sensitive_text = (myQuizOptions[questionId].enable_case_sensitive_text && myQuizOptions[questionId].enable_case_sensitive_text != "") ? myQuizOptions[questionId].enable_case_sensitive_text : false;

                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();

                if ( enable_case_sensitive_text ) {
                    thisQuestionAnswer = thisQuestionCorrectAnswer;
                }

                thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes().split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if ( enable_case_sensitive_text ) {
                        if(userAnsweredText == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    } else {
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    }
                }

                if(trueAnswered){
                    shortTextAnswers.eq(i).next().val(1);
                }else{
                    shortTextAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        shortTextAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        shortTextAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }

                shortTextAnswers.eq(i).removeAttr('disabled')
            }

            var fillInBlankAnswers = form.find('div.ays_quiz_question input[type="text"].ays-text-input.ays-quiz-fill-in-blank-input');
            for(var i=0; i < fillInBlankAnswers.length; i++){
                var userAnsweredText = fillInBlankAnswers.eq(i).val().trim();
                var answerID = fillInBlankAnswers.eq(i).attr('data-answer-id');
                var questionId = fillInBlankAnswers.eq(i).parents('.step').data('questionId');

                var trueAnswered = false;

                // Enable case sensitive text
                var enable_case_sensitive_text = (myQuizOptions[questionId].enable_case_sensitive_text && myQuizOptions[questionId].enable_case_sensitive_text != "") ? myQuizOptions[questionId].enable_case_sensitive_text : false;

                var thisQuestionCorrectAnswer_Arr = myQuizOptions[questionId].question_answer.length > 0 ? "" : myQuizOptions[questionId].question_answer;

                var thisQuestionCorrectAnswer = thisQuestionCorrectAnswer_Arr[answerID];
                var thisQuestionAnswer = thisQuestionCorrectAnswer.toLowerCase();

                if ( enable_case_sensitive_text ) {
                    thisQuestionAnswer = thisQuestionCorrectAnswer;
                }

                thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes().split('%%%');
                for(var i_answer = 0; i_answer < thisQuestionAnswer.length; i_answer++){
                    if ( enable_case_sensitive_text ) {
                        if(userAnsweredText == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    } else {
                        if(userAnsweredText.toLowerCase() == thisQuestionAnswer[i_answer].trim()){
                            trueAnswered = true;
                            break;
                        }
                    }
                }

                if(trueAnswered){
                    fillInBlankAnswers.eq(i).next().val(1);
                }else{
                    fillInBlankAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        fillInBlankAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        fillInBlankAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }

                fillInBlankAnswers.eq(i).removeAttr('disabled');

            }

            var dateAnswers = form.find('div.ays-text-field input[type="date"].ays-text-input');
            for(var i=0; i < dateAnswers.length; i++){
                var userAnsweredText = dateAnswers.eq(i).val();
                var questionId = dateAnswers.eq(i).parents('.step').data('questionId');
                var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer == '' ? "" : myQuizOptions[questionId].question_answer;

                var trueAnswered = false;
                var correctDate = new Date(thisQuestionCorrectAnswer),
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

                if(trueAnswered){
                    dateAnswers.eq(i).next().val(1);
                }else{
                    dateAnswers.eq(i).next().val(0);
                    if(thisQuestionCorrectAnswer == ''){
                        dateAnswers.eq(i).attr('chishtpatasxan', '-');
                    }else{
                        dateAnswers.eq(i).attr('chishtpatasxan', thisQuestionCorrectAnswer);
                    }
                }

                dateAnswers.eq(i).removeAttr('disabled')
            }


            var data = form.serializeFormJSON();
            var questionsIds = data.ays_quiz_questions.split(',');
            
            var questionsObjs = {};
            form.find('div[data-question-id]').each(function (){
                questionsObjs[$(this).data('questionId')] = $(this).data('type');
            });

            for(var i = 0; i < questionsIds.length; i++){
                if( questionsObjs[ questionsIds[i] ] && questionsObjs[ questionsIds[i] ] !== 'matching' && questionsObjs[ questionsIds[i] ] !== 'fill_in_blank' ) {
                    if (!data['ays_questions[ays-question-' + questionsIds[i] + ']']) {
                        data['ays_questions[ays-question-' + questionsIds[i] + ']'] = "";
                    }
                }
            }

            var checked_inputs_arr = ays_quiz_container.find(".step .ays-field input[id*='ays-answer-']:checked");
            if ( checked_inputs_arr.length > 0 ) {
                checked_inputs_arr.each(function () {
                    var checked_input = $(this);
                    var parent = checked_input.parents('.step');
                    var checked_input_name  = checked_input.attr('name');
                    var checked_input_value = checked_input.attr('value');

                    var questionId = parent.attr('data-question-id');
                    var answerId = checked_input.val();

                    if( typeof questionId != "undefined" && questionId !== null ){

                        var thisQuestionCorrectAnswer = myQuizOptions[questionId].question_answer.length <= 0 ? array() : myQuizOptions[questionId].question_answer;
                        var ifCorrectAnswer = thisQuestionCorrectAnswer[answerId] == '' ? '' : thisQuestionCorrectAnswer[answerId];
                        if( typeof ifCorrectAnswer != "undefined" ){
                            checked_input.parents('.ays-field').find('input[name="ays_answer_correct[]"]').val(ifCorrectAnswer);
                        }

                        if (checked_input_name != "" && checked_input_value != "") {
                            if ( data[checked_input_name] !== undefined && data[checked_input_name] == "") {
                                data[checked_input_name] = checked_input_value;
                            }
                        }
                    }
                });

                var newData = form.serializeFormJSON();
                var ays_answer_correct_data = typeof newData["ays_answer_correct[]"] != "undefined" ? newData["ays_answer_correct[]"] : new Array();
                if(typeof ays_answer_correct_data != "undefined" && ays_answer_correct_data.length > 0){
                    data['ays_answer_correct[]'] = ays_answer_correct_data;
                }
            }
            
            $(this).parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').css("display", "none");

            data.action = 'ays_finish_quiz';
            data.end_date = GetFullDateTime();
            var ays_quiz_finish_time = ays_quiz_container.find('.ays-quiz-end-date');
            if ( ays_quiz_finish_time.length !== 0 && ays_quiz_finish_time.val() != "" ) {
                data.end_date = ays_quiz_finish_time.val();
            } else {
                data.end_date = GetFullDateTime();
            }

            if ( typeof data.start_date == "undefined" || data.start_date == "" ) {
                data.start_date = window.aysQuizStartDate;
            }

            if ( typeof data.end_date == "undefined" || data.end_date == "" ) {
                data.end_date = GetFullDateTime();
            }

            if (window.location != window.parent.location) {
                if ( typeof data.ays_quiz_curent_page_link != "undefined" && data.ays_quiz_curent_page_link != "" ) {
                    if ( data.ays_quiz_curent_page_link.indexOf("action=ays_quiz_iframe_shortcode") > 0 ) {
                        if( window.aysQuizParentWindowLink && window.aysQuizParentWindowLink != ""){
                            data.ays_quiz_curent_page_link = window.aysQuizParentWindowLink;
                        }
                    }
                }
            }
            
            var aysQuizLoader = form.find('div[data-role="loader"]');
            aysQuizLoader.addClass(aysQuizLoader.data('class'));
            aysQuizLoader.removeClass('ays-loader');

            var animationOptions = {
                scale: scale,
                left: left,
                opacity: opacity,
                animating: animating
            }
            
            setTimeout(function () {
                sendQuizData(data, form, myOptions, myQuizOptions, animationOptions, $(e.target));
            },2000);
            
            if (parseInt(next_sibilings_count) > 0 && ($(this).parents('.step').attr('data-question-id') || $(this).parents('.step').next().attr('data-question-id'))) {
                current_fs = $(this).parents('form').find('div[data-question-id]');
            }
            
            if( isRequiredQuestion ) {
                var qid = checkQuestionTimer(ays_quiz_container, quizId);
                if ( qid === false ){
                    aysAnimateStep(ays_quiz_container.data('questEffect'), current_fs, next_fs);
                }
            }
        });

        $(document).find('.ays_next.start_button').on('click',function(e){
            var $this = $(this);
            var thisCont = $this.parents('.ays-quiz-container');
            var quizId = thisCont.find('input[name="ays_quiz_id"]').val();
            var myOptions = JSON.parse(window.atob(window.aysQuizOptions[quizId]));
            var checkQuizGeneratedPassword = checkQuizPassword(e, myOptions, false);
            if(checkQuizGeneratedPassword){
                if(myOptions.enable_password && myOptions.enable_password == 'on'){
                    if(myOptions.generate_password && myOptions.generate_password == 'generated_password'){
                        var userGeneratedPasswordVal = $this.parents('.ays-quiz-container').find('.ays_quiz_password').val();
                        var userData = {};
                        userData.action = 'ays_generated_used_passwords';
                        userData.userGeneratedPassword = userGeneratedPasswordVal;
                        userData.quizId = quizId;
                        $.ajax({
                            url: quiz_maker_ajax_public.ajax_url,
                            method: 'post',
                            dataType: 'json',
                            crossDomain: true,
                            data: userData,
                            success: function (response) {
                                if(response.status){
                                }
                            }
                        });
                    }
                }

                if(myOptions.limit_users && myOptions.limit_users == 'on'){
                    var limit_users_by = (myOptions.limit_users_by && myOptions.limit_users_by != '') ? myOptions.limit_users_by : 'ip';
                    var isUserLoggedIn = (myOptions.is_user_logged_in && myOptions.is_user_logged_in != null) ? myOptions.is_user_logged_in : null;

                    myOptions.enable_restriction_pass = myOptions.enable_restriction_pass ? myOptions.enable_restriction_pass : 'off';
                    var onlyForSelectedUserRole = (myOptions.enable_restriction_pass && myOptions.enable_restriction_pass == 'on' ) ? true : false;

                    myOptions.turn_on_extra_security_check = myOptions.turn_on_extra_security_check ? myOptions.turn_on_extra_security_check : 'on';
                    var turnOnSecurityCheck = (myOptions.turn_on_extra_security_check && myOptions.turn_on_extra_security_check == 'on' ) ? true : false;

                    myOptions.limit_attempts_count_by_user_role = myOptions.limit_attempts_count_by_user_role ? myOptions.limit_attempts_count_by_user_role : '';
                    var limitAttemptsCountByUserRole = (myOptions.limit_attempts_count_by_user_role !=='' ) ? parseInt( myOptions.limit_attempts_count_by_user_role ) : '';

                    if( isUserLoggedIn === null ){
                        isUserLoggedIn = false;
                    }

                    var checkLimit = false;
                    if( limit_users_by != 'user_id' ){
                        checkLimit = true;
                    }

                    if( isUserLoggedIn ){
                        checkLimit = true;
                    }

                    if( turnOnSecurityCheck === false ){
                        checkLimit = false;
                    }

                    if( checkLimit ){
                        var quiz_max_pass_count = (myOptions.quiz_max_pass_count && myOptions.quiz_max_pass_count != '') ? parseInt(myOptions.quiz_max_pass_count) : 1;

                        if( limitAttemptsCountByUserRole !== '' && onlyForSelectedUserRole === true ){
                            quiz_max_pass_count = limitAttemptsCountByUserRole;
                        }

                        var limitation_message = (myOptions.limitation_message && myOptions.limitation_message != '') ? myOptions.limitation_message : quizLangObj.alreadyPassedQuiz;
                        var quiz_pass_score = (myOptions.quiz_pass_score && myOptions.quiz_pass_score != '') ? myOptions.quiz_pass_score : 0;
                        var text_color = (myOptions.text_color && myOptions.text_color != '') ? myOptions.text_color : "#333";

                        var html = '<div style="color:'+ text_color +';min-height:200px;" class="ays_block_content">'+ limitation_message +'</div>';

                        var userData = {};

                        userData.action = 'ays_quiz_check_user_started';
                        userData.quiz_id = quizId;
                        userData.quiz_max_pass_count = quiz_max_pass_count;
                        userData.quiz_pass_score = quiz_pass_score;
                        userData.start_date = GetFullDateTime();

                        $.ajax({
                            url: quiz_maker_ajax_public.ajax_url,
                            method: 'post',
                            dataType: 'json',
                            crossDomain: true,
                            data: userData,
                            success: function (response) {
                                if(response.status){
                                    thisCont.find('.ays_quiz_result_row_id').val(response.result_id);
                                }else{
                                    $this.parents('.ays-quiz-wrap').find('.ays-quiz-questions-nav-wrap').hide();
                                    thisCont.find('.ays_quiz_timer_container').slideUp(500);
                                    thisCont.find('form').append(html);
                                    thisCont.find('div.step').remove();
                                    clearInterval(window.aysTimerInterval);
                                }
                            }
                        });
                    }
                }
                else if( myOptions.store_all_not_finished_results && myOptions.store_all_not_finished_results === true ){
                    var quiz_max_pass_count = (myOptions.quiz_max_pass_count && myOptions.quiz_max_pass_count != '') ? parseInt(myOptions.quiz_max_pass_count) : 1;
                    var limitation_message = (myOptions.limitation_message && myOptions.limitation_message != '') ? myOptions.limitation_message : quizLangObj.alreadyPassedQuiz;
                    var quiz_pass_score = (myOptions.quiz_pass_score && myOptions.quiz_pass_score != '') ? myOptions.quiz_pass_score : 0;
                    var text_color = (myOptions.text_color && myOptions.text_color != '') ? myOptions.text_color : "#333";

                    var html = '<div style="color:'+ text_color +';min-height:200px;" class="ays_block_content">'+ limitation_message +'</div>';

                    var userData = {};

                    userData.action = 'ays_quiz_check_user_started';
                    userData.quiz_id = quizId;
                    userData.quiz_max_pass_count = quiz_max_pass_count;
                    userData.quiz_pass_score = quiz_pass_score;
                    userData.start_date = GetFullDateTime();

                    $.ajax({
                        url: quiz_maker_ajax_public.ajax_url,
                        method: 'post',
                        dataType: 'json',
                        crossDomain: true,
                        data: userData,
                        success: function (response) {
                            if(response.status){
                                thisCont.find('.ays_quiz_result_row_id').val(response.result_id);
                            }else{
                                clearInterval(window.aysTimerInterval);
                            }
                        }
                    });
                }
            }

            if( myOptions.paypalStatus ){
                if( myOptions.paypalStatus.extraCheck === true ){
                    var userData = {};

                    userData.action = 'ays_quiz_check_user_started_for_paypal';
                    userData.quiz_id = quizId;
                    userData.order_id = myOptions.paypalStatus.orderId;
                    userData.start_date = GetFullDateTime();

                    $.ajax({
                        url: quiz_maker_ajax_public.ajax_url,
                        method: 'post',
                        dataType: 'json',
                        crossDomain: true,
                        data: userData
                    });
                }
            }
        });

        $(document).on('click', '.ays-export-quiz-button', function (e) {
            var $this = $(this);
            var parent = $this.parents('.ays-quiz-container');
            var quizId = parent.find('input[name="ays_quiz_id"]').val();
            var form   = parent.find('form');

            var action = 'user_export_quiz_questions_pdf';
            var checkExportQuizAnswers = $(document).find('.ays-export-quiz-answers:checked').length;
            var exportQuizAnswers = checkExportQuizAnswers > 0 ? true : false;

            var dataForExport = JSON.parse(window.atob(window.aysQuizUserExportDataArray[quizId]));

            $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'flex');

            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    action            : action,
                    dataForExport     : dataForExport,
                    exportQuizAnswers : exportQuizAnswers
                },
                success: function (response) {
                    if (response.status) {
                        $this.parent().find('#downloadFileU').attr({
                            'href': response.fileUrl,
                            'download': response.fileName,
                        })[0].click();

                        window.URL.revokeObjectURL(response.fileUrl);
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h2>Can't load resource.</h2><br><h4>Maybe the data has been deleted.</h4>",
                        })
                    }
                    $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'none');
                    $this.removeClass('disabled');
                }
            });
            e.preventDefault();
        });

        $(document).find(".ays-quiz-submit-question-report").on('click', function(e) {
            e.preventDefault();
            var reportForm = $(this).parents('form#ays-quiz-question-report-form');

            var questionId = reportForm.find('input.ays-quiz-report-question-id').val();
            var quizId = reportForm.find('input.ays-quiz-report-quiz-id').val();
            var reportText = reportForm.find('textarea#ays-quiz-question-report-textarea').val();
            var sendEmail = reportForm.find('input.ays-quiz-report-question-send-email').val();

            if (reportText === '') {
                var errorMessageDiv = reportForm.find('div.ays-quiz-question-report-error');
                errorMessageDiv.show();

                setTimeout(function() {
                    errorMessageDiv.fadeOut(400, function() {
                        $(this).hide();
                    });
                }, 3000);
                return false;
            }

            $(document).find('div.ays-quiz-preloader').css('display', 'flex');
            $(document).find('div.ays-quiz-preloader').addClass('ays_quiz_modal_overlay');

            var data = {};
            data.action = 'ays_quiz_send_question_report';
            data.question_id = questionId;
            data.quiz_id = quizId;
            data.report_text = reportText;
            data.create_date = GetFullDateTime();
            data.send_email = sendEmail;
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                crossDomain: true,
                data: data,
                success: function (response) {
                    $(document).find('div.ays-quiz-preloader').css('display', 'none');
                    $(document).find('div.ays-quiz-preloader').removeClass('ays_quiz_modal_overlay');

                    if(response.status) {
                        $('#ays-quiz-question-report-modal').hide();

                        swal.fire({
                            type: 'success',
                            html: quizLangObj.reportSentMessage
                        });
                    }
                }
            });
        });

    });
    
    function sendQuizData(data, form, myOptions, myQuizOptions, options, element){
        if(typeof sendQuizData.counter == 'undefined'){
            sendQuizData.counter = 0;
        }
        if(window.navigator.onLine){
            sendQuizData.counter++;
            $.ajax({
                url: window.quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                crossDomain: true,
                data: data,
                success: function(response){
                    if(response.status === true){
                        doQuizResult(response, form, myOptions, myQuizOptions);
                    }else if( response.status === false && typeof response.flag !== 'undefined' && response.flag === false ){
                        var aysQuizContainer = element.parents('.ays-quiz-container');
                        var lastPageContent = '';

                        lastPageContent += '<p>';
                            lastPageContent += response.text;
                        lastPageContent += '</p>';

                        aysQuizContainer.find('.ays_thank_you_fs').html( lastPageContent );
                    }else{
                        if(sendQuizData.counter >= 5){
                            swal.fire({
                                type: 'error',
                            html: quizLangObj.sorry + ".<br>" + quizLangObj.unableStoreData + "."
                            });
                            goQuizFinishPage(form, options, element, myOptions);
                        }else{
                            if(window.navigator.onLine){
                                setTimeout(function(){
                                    sendQuizData(data, form, myOptions, myQuizOptions, options, element);
                                },3000);
                            }else{
                                sendQuizData(data, form, myOptions, myQuizOptions, options, element);
                            }
                        }
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if(sendQuizData.counter >= 5){
                        swal.fire({
                            type: 'error',
                            html: quizLangObj.sorry + ".<br>" + quizLangObj.unableStoreData + "."
                        });
                        goQuizFinishPage(form, options, element, myOptions);
                    }else{
                        setTimeout(function(){
                            sendQuizData(data, form, myOptions, myQuizOptions, options, element);
                        },3000);
                    }
                }
            });
        }else{
            swal.fire({
                type: 'warning',
                html: quizLangObj.connectionLost + ".<br>" + quizLangObj.checkConnection + "."
            });
            sendQuizData.counter = 0;
            goQuizFinishPage(form, options, element, myOptions);
            var aysQuizContainer = element.parents('.ays-quiz-container');
            aysQuizContainer.find('.step').hide();
            aysQuizContainer.find('.ays_thank_you_fs').prev().removeAttr('style').css({
                'display':'flex',
                'position':'static',
                'transform':'scale(1)',
                'opacity': 1,
                'pointer-events': 'auto'
            });
            var show_result_button = element.parents('form').find('div[data-question-id] input[name="ays_finish_quiz"]');
            if (show_result_button.hasClass('ays_display_none')) {
                show_result_button.removeClass('ays_display_none');
            }
        }
    }
    
    function goQuizFinishPage(form, options, element, myOptions){        
        var currentFS = form.find('.step.active-step');
        var next_sibilings_count = form.find('.ays_question_count_per_page').val();
        if (parseInt(next_sibilings_count) > 0 &&
            (element.parents('.step').attr('data-question-id') ||
             element.parents('.step').next().attr('data-question-id'))) {
            currentFS = form.find('div[data-question-id]');
        }
        currentFS.prev().css('display', 'flex');
        currentFS.animate({opacity: 0}, {
            step: function(now, mx) {
                options.scale = 1 - (1 - now) * 0.2;
                options.left = (now * 50)+"%";
                options.opacity = 1 - now;
                currentFS.css({
                    'transform': 'scale('+options.scale+')',
                    'position': '',
                    'pointer-events': 'none'
                });
                currentFS.prev().css({
                    'left': options.left,
                    'opacity': options.opacity,
                    'pointer-events': 'none'
                });
            },
            duration: 800,
            complete: function(){
                currentFS.hide();
                currentFS.css({
                    'opacity': '1',
                    'pointer-events': 'auto',
                });
                currentFS.prev().css({
                    'transform': 'scale(1)',
                    'position': 'relative',
                    'opacity': '1',
                    'pointer-events': 'auto'
                });
                options.animating = false;
            },
            easing: 'easeInOutBack'
        });
        if(myOptions.enable_correction == 'on'){
            if(currentFS.prev().find('input:checked').length > 0){
                currentFS.prev().find('.ays-field input').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field input').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field input').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('option:checked').length > 0){
                currentFS.prev().find('.ays-field select').attr('disabled', 'disabled');
                currentFS.prev().find('.ays-field select').on('click', function(){
                    return false;
                });
                currentFS.prev().find('.ays-field select').on('change', function(){
                    return false;
                });
            }
            if(currentFS.prev().find('textarea').length > 0){
                if(currentFS.prev().find('textarea').val() !== ''){
                    currentFS.prev().find('.ays-field textarea').attr('disabled', 'disabled');
                    currentFS.prev().find('.ays-field textarea').on('click', function(){
                        return false;
                    });
                    currentFS.prev().find('.ays-field textarea').on('change', function(){
                        return false;
                    });
                }
            }
        }
    }
    
    function doQuizResult(response, form, myOptions, myQuizOptions){
        var hideQuizBGImage = form.parents('.ays-quiz-container').data('hideBgImage');
		var QuizBGGragient = form.parents('.ays-quiz-container').data('bgGradient');
        var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
        var uniqueCode = response.unique_code;
		if(hideQuizBGImage){
			form.parents('.ays-quiz-container').css('background-image', 'none');
			if(typeof QuizBGGragient != 'undefined'){
				form.parents('.ays-quiz-container').css('background-image', QuizBGGragient);
			}
		}

        if(typeof response.chain_quiz_button_text != 'undefined' && response.chain_quiz_button_text === 'seeResult'){
            form.find('.ays_chain_see_result_button').removeClass('ays_display_none');
        }else if(typeof response.chain_quiz_button_text != 'undefined' && response.chain_quiz_button_text === 'nextQuiz'){
            form.find('.ays_chain_next_quiz_button').removeClass('ays_display_none');
        }

        var conditionsData = response.conditionData;
        var conditionsExict = false;
        var conditionPageMessage = "";
        var conditionEmailMessage = "";
        var conditionRedirectDelay = "";
        var conditionRedirectUrl = "";
        var conditionRedirectCountDown = "";
        var trueConditionsCount = "";
        if(typeof conditionsData != "undefined"){
            if(conditionsData.hasAction){
                if(conditionsData.pageMessage){
                    conditionPageMessage = conditionsData.pageMessage;
                }
                if(conditionsData.emailMessage){
                    conditionEmailMessage = conditionsData.emailMessage;
                }
                if(conditionsData.redirectDelay){
                    conditionRedirectDelay = conditionsData.redirectDelay;
                }
                if(conditionsData.redirectUrl){
                    conditionRedirectUrl = conditionsData.redirectUrl;
                }
                if(conditionsData.redirectCountDown){
                    conditionRedirectCountDown = conditionsData.redirectCountDown;
                }
                if(conditionsData.trueConditionsCount){
                    trueConditionsCount = conditionsData.trueConditionsCount;
                }
                conditionsExict = true;
            }
        }

        var ays_block_element = form.parents('.ays-quiz-container');
        var redirectActionResponse = true;
        if(conditionsExict){
            if(conditionRedirectUrl){
                redirectActionResponse = redirectAction(conditionRedirectUrl, conditionRedirectDelay, ays_block_element, conditionRedirectCountDown, form, myOptions, false, uniqueCode);
            }else{
                if(response.showIntervalMessage && ( response.interval_redirect_url !== null && response.interval_redirect_url != '' )){
                    redirectActionResponse = redirectAction(response.interval_redirect_url, response.interval_redirect_delay, ays_block_element, response.interval_redirect_after, form, myOptions, false, uniqueCode);
                }else{
                    if (myOptions.redirect_after_submit && myOptions.redirect_after_submit == 'on') {
                        redirectActionResponse = redirectAction(myOptions.submit_redirect_url, myOptions.submit_redirect_delay, ays_block_element, myOptions.submit_redirect_after, form, myOptions, true, uniqueCode);
                    }
                }
            }
        }else{
            if(response.showIntervalMessage && ( response.interval_redirect_url !== null && response.interval_redirect_url != '' )){
                redirectActionResponse = redirectAction(response.interval_redirect_url, response.interval_redirect_delay, ays_block_element, response.interval_redirect_after, form, myOptions, false, uniqueCode);
            }else{
                if (myOptions.redirect_after_submit && myOptions.redirect_after_submit == 'on') {
                    redirectActionResponse = redirectAction(myOptions.submit_redirect_url, myOptions.submit_redirect_delay, ays_block_element, myOptions.submit_redirect_after, form, myOptions, true, uniqueCode);
                }
            }
        }

        if( redirectActionResponse === false ){
            return false;
        }

        form.find('div.ays_message').css('display', 'none');
        form.find('.ays_average').css({'display': 'block'});
        var quizScore = '';
        switch(response.displayScore){
            case 'by_percentage':
                quizScore = parseInt(response.score);
            break;
            case 'by_correctness':
                quizScore = response.score.split('/');
            break;
            case 'by_points':
                quizScore = response.score.split('/');
            break;
            default:
                quizScore = parseInt(response.score);
        }

        form.find('.ays_quiz_timer_container').slideUp(500);

        if (response.hide_result) {
            form.find('div.ays_message').html(response.text);
        } else {
            if(response.showIntervalMessage){
                form.find('div.ays_message').html(response.intervalMessage + response.finishText);
                if (response.product) {
//                    var $wooBlock = $("<div class='ays-woo-block'></div>");
//                    var $wooInBlock = $("<div class='ays-woo-product-block'></div>");
//                    var $wooImage = $('<div class="product-image"><img src="' + response.product.image + '" alt="WooCommerce Product"></div>');
//                    var $wooName = $('<h4 class="ays-woo-product-title"><a href="'+response.product.prodUrl+'" target="_blank">'+response.product.name+'</a></h4>');
//                    var $wooCartLink = $(response.product.link);
//                    if(response.product.image){
//                        $wooBlock.append($wooImage);
//                    }
//                    $wooInBlock.append($wooName);
//                    $wooInBlock.append($wooCartLink);
//                    $wooBlock.append($wooInBlock);
//                    form.find('div.ays_message').after($wooBlock);
//                    if(form.parents('.ays-quiz-container').width() < 420){
//                        form.find('.ays-woo-block').css('flex-wrap', 'wrap');
//                        if(response.product.image){
//                            form.find('.ays-woo-product-block').css('padding-top', '20px');
//                        }
//                    }
                    var $wooBlock = '';
                    var $wooInBlock = '';
                    var $wooImage = '';
                    var $wooName = '';
                    var $wooCartLink = '';
                    var $wooBloks = '';
                    $wooBloks = $("<div class='ays-woo-block-main'></div>")
                    for(var products in response.product){
                        $wooBlock    = $("<div class='ays-woo-block'></div>");
                        $wooInBlock  = $("<div class='ays-woo-product-block'></div>");
                        $wooImage    = $('<div class="product-image"><img src="' + response.product[products].image + '" alt="WooCommerce Product"></div>');
                        $wooName     = $('<h4 class="ays-woo-product-title"><a href="'+response.product[products].prodUrl+'" target="_blank">'+response.product[products].name+'</a></h4>');
                        $wooCartLink = $(response.product[products].link);
                        if(response.product[products].image){
                            $wooBlock.append($wooImage);
                        }
                        $wooBloks.append($wooBlock);
                        $wooInBlock.append($wooName);
                        $wooInBlock.append($wooCartLink);
                        $wooBlock.append($wooInBlock);
                        form.find('div.ays_message').after($wooBloks);
                        if(form.parents('.ays-quiz-container').width() < 420){
                            form.find('.ays-woo-block').css('flex-wrap', 'wrap');
                            if(response.product[products].image){
                                form.find('.ays-woo-product-block').css('padding-top', '20px');
                            }
                        }
                    }
                }
            }else{
                form.find('div.ays_message').html(response.finishText);
            }

            if(conditionsExict){
                form.find('div.ays_message').prepend(conditionPageMessage);
            }

            form.find('p.ays_score').removeClass('ays_score_display_none');
            form.find('p.ays_score').html(form.find('p.ays_score').text()+'<span class="ays_score_percent animated"> ' + response.score + '</span>');
        }
        
        if( response.socialHeading ){
            form.find(".ays-quiz-social-shares-heading").html(response.socialHeading);
        }

        if( response.socialLinksHeading && response.socialLinksHeading != "" ){
            form.find(".ays-quiz-social-links-heading").html(response.socialLinksHeading);
        }

        form.find('div.ays_message').fadeIn(500);
        setTimeout(function () {
            form.find('p.ays_score').addClass('tada');
        }, 500);
        var numberOfPercent = 0;
        var percentAnimate = setInterval(function(){
            if(typeof quizScore == 'number'){
                form.find('.ays-progress-value').text(numberOfPercent + "%");
                if(numberOfPercent == quizScore){
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }else{
                var total = quizScore[1];
                var count = quizScore[0];
                total = parseFloat(total.trim());
                count = parseFloat(count.trim());
                form.find('.ays-progress-value').text(numberOfPercent + " / " + total);
                if(numberOfPercent >= count){
                    form.find('.ays-progress-value').text(count + " / " + total);
                    clearInterval(percentAnimate);
                }
                numberOfPercent++;
            }
        },20);
        
        var score = quizScore;
        if(response.displayScore == 'by_correctness' || response.displayScore == 'by_points'){
            var total = parseInt(quizScore[1].trim());
            var count = parseInt(quizScore[0].trim());
            score = (count / total) * 100;
        }

        if(response.scoreMessage){
            form.find('div.ays_score_message').html(response.scoreMessage);
        }

        var last_result_id = null;
        if(response.result_id && response.result_id != ''){
            last_result_id = parseInt( response.result_id );
        }

        aysQuizSetCustomEvent();
        var trackUsersEvent = new CustomEvent('getResultId', {
            detail: {
              resultId: last_result_id
            }
        });
        form.get(0).dispatchEvent(trackUsersEvent);

        // Make responses anonymous
        myOptions.quiz_make_responses_anonymous = ( myOptions.quiz_make_responses_anonymous ) ? myOptions.quiz_make_responses_anonymous : 'off';
        var quiz_make_responses_anonymous = (myOptions.quiz_make_responses_anonymous && myOptions.quiz_make_responses_anonymous == "on") ? true : false;

        // Enable Keyboard Navigation
        myOptions.quiz_enable_keyboard_navigation = ! myOptions.quiz_enable_keyboard_navigation ? 'off' : myOptions.quiz_enable_keyboard_navigation;
        var quiz_enable_keyboard_navigation = (myOptions.quiz_enable_keyboard_navigation && myOptions.quiz_enable_keyboard_navigation == 'on') ? true : false;

        // Make responses anonymous
        myOptions.quiz_enable_user_cհoosing_anonymous_assessment = ( myOptions.quiz_enable_user_cհoosing_anonymous_assessment ) ? myOptions.quiz_enable_user_cհoosing_anonymous_assessment : 'off';
        var quiz_enable_user_cհoosing_anonymous_assessment = (myOptions.quiz_enable_user_cհoosing_anonymous_assessment && myOptions.quiz_enable_user_cհoosing_anonymous_assessment == "on") ? true : false;

        var class_for_keyboard = '';
        var attributes_for_keyboard = '';

        if( quiz_enable_keyboard_navigation ){
            class_for_keyboard = "ays-quiz-keyboard-active";
            attributes_for_keyboard = "tabindex='0'";
        }

        if(score > 0){
            form.find('.ays-progress-bar').css('padding-right', '7px');
            var progressBarStyle = myOptions.progress_bar_style ? myOptions.progress_bar_style : 'first';
            if(progressBarStyle == 'first' || progressBarStyle == 'second'){
                form.find('.ays-progress-value').css('width', 0);
                form.find('.ays-progress-value').css('transition', 'width ' + score*25 + 'ms linear');
                setTimeout(function(){
                    form.find('.ays-progress-value').css('width', score+'%');
                }, 1);
            }
            form.find('.ays-progress-bar').css('transition', 'width ' + score*25 + 'ms linear');
            setTimeout(function(){
                form.find('.ays-progress-bar').css('width', score+'%');
            }, 1);
        }

        if ( score == 0 ) {
            // Quiz background Color
            var quiz_make_bg_color = (myOptions.bg_color && myOptions.bg_color != "") ? myOptions.bg_color : '#fff';

            form.find('.ays-progress-value').css('color', quiz_make_bg_color);
        }

        form.append($("<div class='ays_quiz_results'></div>"));
        var formResults = form.find('.ays_quiz_results');
        if (form.hasClass('enable_questions_result')) {

            // Enable the Show/Hide toggle
            myOptions.quiz_enable_results_toggle = ! myOptions.quiz_enable_results_toggle ? 'off' : myOptions.quiz_enable_results_toggle;
            var quiz_enable_results_toggle = (myOptions.quiz_enable_results_toggle && myOptions.quiz_enable_results_toggle == 'on') ? true : false;

            var resultToggleHTML = "";

            resultToggleHTML += '<div class="ays-quiz-results-toggle-block">';
                resultToggleHTML += '<span class="ays-show-res-toggle ays-res-toggle-show">'+ quizLangObj.show +'</span>';
                resultToggleHTML += '<input type="checkbox" class="ays_toggle ays_toggle_slide ays-quiz-res-toggle-checkbox" id="ays-quiz-show-results-toggle-'+ quizId +'" checked>';
                resultToggleHTML += '<label for="ays-quiz-show-results-toggle-'+ quizId +'" class="ays_switch_toggle '+ class_for_keyboard +'" '+ attributes_for_keyboard +'>Toggle</label>';
                resultToggleHTML += '<span class="ays-show-res-toggle ays-res-toggle-hide quest-toggle-failed">'+ quizLangObj.hide +'</span>';
            resultToggleHTML += '</div>';

            if ( quiz_enable_results_toggle ) {
                formResults.append(resultToggleHTML);
            }

            var questions = form.find('div[data-question-id]');
            var showOnlyWrongAnswer = (myOptions.show_only_wrong_answer && myOptions.show_only_wrong_answer == "on") ? true : false;
            if(myOptions.enable_correction && myOptions.enable_correction != 'on'){
                showOnlyWrongAnswer = false;
            }

            var ans_right_wrong_icon = (myOptions.ans_right_wrong_icon && myOptions.ans_right_wrong_icon != "") ? myOptions.ans_right_wrong_icon : "default";

            var correct_img_URL = "";
            var wrong_img_URL   = "";
            if( ans_right_wrong_icon == "default"){
                correct_img_URL = quiz_maker_ajax_public.AYS_QUIZ_PUBLIC_URL + "/images/correct.png";
                wrong_img_URL   = quiz_maker_ajax_public.AYS_QUIZ_PUBLIC_URL + "/images/wrong.png";
            } else if( ans_right_wrong_icon == "none" ){
                correct_img_URL = "";
                wrong_img_URL   = "";
            } else {
                correct_img_URL = quiz_maker_ajax_public.AYS_QUIZ_PUBLIC_URL + "/images/correct-"+ ans_right_wrong_icon +".png";
                wrong_img_URL   = quiz_maker_ajax_public.AYS_QUIZ_PUBLIC_URL + "/images/wrong-"+ ans_right_wrong_icon +".png";
            }

            var correct_img_URL_HTML = "";
            var wrong_img_URL_HTML   = "";
            if (correct_img_URL != "") {
                correct_img_URL_HTML = "<img class='ays-quiz-check-button-right-wrong-icon' data-type='"+ans_right_wrong_icon+"' src='"+ correct_img_URL +"'>"
            }

            if (wrong_img_URL != "") {
                wrong_img_URL_HTML = "<img class='ays-quiz-check-button-right-wrong-icon' data-type='"+ ans_right_wrong_icon +"' src='"+ wrong_img_URL +"'>"
            }

            var answerIsRightArr = new Array();
            for (var z = 0; z < questions.length; z++) {                
                if(questions.eq(z).hasClass('not_influence_to_score')){
                    continue;
                }
                var question = questions.eq(z).clone(true, true);
                var questionId = question.attr('data-question-id');
                var questionType = question.attr('data-type');
                var question_original_html = questions.eq(z).find('.ays_quiz_question');

                var ays_quiz_question_html      = questions.eq(z).find('.ays_quiz_question');
                var ays_quiz_question_img_html  = questions.eq(z).find('.ays-image-question-img');
                var question_explanation_html   = questions.eq(z).find('.ays_questtion_explanation');
                var wrong_answer_text_html      = questions.eq(z).find('.wrong_answer_text');
                var right_answer_text_html      = questions.eq(z).find('.right_answer_text');
                var question_report_html        = questions.eq(z).find('.ays_question_report');
                var note_message_box_html       = questions.eq(z).find('.ays-quiz-question-note-message-box');

                var question_parts_arr = new Array(
                    note_message_box_html,
                    question_explanation_html,
                    wrong_answer_text_html,
                    right_answer_text_html,
                    question_report_html,
                );

                question.find('.ays_quiz_question').remove();
                question.find('.ays-abs-fs').prepend( questions.eq(z).find('.ays_quiz_question') );

                question.find('.ays_questtion_explanation').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.ays_questtion_explanation') );

                question.find('.wrong_answer_text').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.wrong_answer_text') );

                question.find('.right_answer_text').remove();
                question.find('.ays-abs-fs').append( questions.eq(z).find('.right_answer_text') );

                question.find('input[type="button"]').remove();
                question.find('input[type="submit"]').remove();
                question.find('.ays_arrow').remove();
                question.find('.ays-export-quiz-button-container').remove();

                question.find('.ays-quiz-category-description-box').addClass('ays_display_none');

                question.addClass('ays_question_result');
                var checked_inputs = question.find('input:checked');
                var text_answer = question.find('textarea.ays-text-input');
                var number_answer = question.find('input[type="number"].ays-text-input');
                var short_text_answer = question.find('input[type="text"].ays-text-input:not(.ays-quiz-fill-in-blank-input)');
                var fillInBlankAnswers = question.find('div.ays_quiz_question input[type="text"].ays-text-input.ays-quiz-fill-in-blank-input');
                var date_answer = question.find('input[type="date"].ays-text-input');
                var selected_options = question.find('select');
                var answerIsRight = false;

                var fieldset_html = "<fieldset class='ays_fieldset'>" + "<legend>" + quizLangObj.notAnsweredText + "</legend>" + "</fieldset>";
                var question_html = question.find('.ays-abs-fs > *:not(.ays_quiz_question)').clone(true, true);

                myOptions.hide_correct_answers = (myOptions.hide_correct_answers) ? myOptions.hide_correct_answers : 'off';
                var hideRightAnswers =(myOptions.hide_correct_answers && myOptions.hide_correct_answers == 'on') ? true : false;

                if( questionType == "radio" || questionType == "checkbox"){
                    var parentStep = question;
                    var questionID = questionId;
                    var radioInputData = question.find('.ays-quiz-answers .ays-field input[name*="ays_questions"]');

                    for (var i = 0; i < radioInputData.length; i++) {
                        var currentAnswer = $( radioInputData[i] );
                        var currentAnswerID = currentAnswer.val();
                        
                        if( typeof questionID != "undefined" && questionID !== null ){

                            var thisQuestionCorrectAnswer = myQuizOptions[questionID].question_answer.length <= 0 ? array() : myQuizOptions[questionID].question_answer;
                            var ifCorrectAnswer = thisQuestionCorrectAnswer[currentAnswerID] == '' ? '' : thisQuestionCorrectAnswer[currentAnswerID];
                            if( typeof ifCorrectAnswer != "undefined" ){
                                question.find('input[name="ays_answer_correct[]"]').val(ifCorrectAnswer);

                                for (var question_answer_ID in thisQuestionCorrectAnswer) {
                                    var UserAnswered_true_or_false = thisQuestionCorrectAnswer[question_answer_ID];
                                    question.find('.ays-quiz-answers .ays-field input[value="'+ question_answer_ID +'"]').prev().val(UserAnswered_true_or_false);
                                }
                            }
                        }
                    }
                }
                
                if(showOnlyWrongAnswer === false){
                    question.find('input[name="ays_answer_correct[]"][value="1"]').parent().find('label').addClass('correct answered');
                    question.find('input[name="ays_answer_correct[]"][value="1"]').parents('div.ays-field').addClass('correct_div');
                }

                if(checked_inputs.length === 0){
                    var emptyAnswer = false;
                    if(question.find('input[type="radio"]').length !== 0){
                        emptyAnswer = true;
                    }
                    if(question.find('input[type="checkbox"]').length !== 0){
                        emptyAnswer = true;
                    }
                    if(emptyAnswer){

                        var q_answer_text_html = question.find('.ays-abs-fs .ays-quiz-answers');
                        question.find('.ays-abs-fs').html(fieldset_html);
                        question.find('.ays-abs-fs .ays_fieldset').append(ays_quiz_question_html);
                        question.find('.ays-abs-fs .ays_fieldset').append(ays_quiz_question_img_html);
                        question.find('.ays-abs-fs .ays_fieldset').append(q_answer_text_html );

                        for (var i = 0; i < question_parts_arr.length; i++) {
                            question.find('.ays-abs-fs .ays_fieldset').append( question_parts_arr[i] );
                        }

                        question.find('.ays-abs-fs').css({
                            'padding': '7px',
                            // 'width': '100%'
                        });
                    }
                }

                var aysAudio = $(document).find('.ays_question_result audio');
                if(aysAudio.length > 0){
                    aysAudio.each(function(e, el){
                        el.pause();
                    });
                }

                selected_options.each(function(element, item){
                    var selectOptions = $(item).children("option[data-chisht]");
                    var selectedOption = $(item).children("option[data-chisht]:selected");
                    var answerClass, answerDivClass, attrChecked, answerClassForSelected, answerClass_tpel, answerViewClass, attrCheckedStyle = "", attrCheckedStyle2;
                    var prefixIcon = '', attrCheckedStyle3 = '', attrCheckedStyle4;
                    var correctAnswersDiv = '', rectAnswerBefore = "";

                    if ( $(item).parents('.step').data('type') === 'matching' ) {
                        selectOptions = $(item).children("option");
                        selectedOption = $(item).children("option[selected]:selected");
                    }

                    answerViewClass = form.parents('.ays-quiz-container').find('.answer_view_class').val();
                    answerViewClass = "ays_"+form.find('.answer_view_class').val()+"_view_item";
                    var isAnswerCorrectFlag = true;
                    for(var j = 0; j < selectOptions.length; j++){
                        if($(selectOptions[j]).attr("value") == '' || $(selectOptions[j]).attr("value") == undefined || $(selectOptions[j]).attr("value") == null){
                            continue;
                        }

                        if($(selectOptions[j]).attr("data-nkar") == '' || $(selectOptions[j]).attr("data-nkar") == undefined || $(selectOptions[j]).attr("data-nkar") == null){
                            var selectedOptionImageHTML = "";
                        } else {
                            var selectedOptionImageURL = $(selectOptions[j]).attr("data-nkar");
                            var selectedOptionImageHTML = '<img src="'+ selectedOptionImageURL +'" alt="" class="ays-answer-image" />';
                        }

                        var isAnswerCorrect = parseInt($(selectOptions[j]).data("chisht")) === 1;
                        if ( $(item).parents('.step').data('type') === 'matching' ) {
                            var answerId = $(item).parents('.ays-matching-field-match').data('answerId');
                            var thisAnswerOptions = myQuizOptions[questionId];
                            var choice = thisAnswerOptions.question_answer[ $(selectOptions[j]).attr("value") ];
                            isAnswerCorrect = Number( answerId ) === Number( choice );
                            
                            if( hideRightAnswers && selectedOption.length == 0 && isAnswerCorrectFlag ) {
                                correctAnswersDiv += '<div class="ays-field-matching-type-empty-answer">' +
                                '<span>'+ quizLangObj.unansweredQuestion +'</span> ' +
                            '</div>';
                                isAnswerCorrectFlag = false;
                                continue;
                            }else if( isAnswerCorrect && hideRightAnswers && $(selectOptions[j]).prop('selected') == false ) {
                                continue;
                            }
                        }

                        if($(selectOptions[j]).prop('selected') == true){
                            if(isAnswerCorrect){
                                answerClassForSelected = " correct answered ";
                                answerDivClass = "correct_div checked_answer_div";
                                attrChecked = "checked='checked'";
                                answerIsRight = true;
                            }else{
                                answerClassForSelected = " wrong answered ";
                                attrChecked = "checked='checked'";
                                answerDivClass = "wrong_div checked_answer_div ";
                            }
                        }else{
                            if(showOnlyWrongAnswer === false){
                                if(isAnswerCorrect){
                                    answerClassForSelected = " correct answered ";
                                    answerDivClass = "correct_div checked_answer_div";
                                    attrChecked = "";
                                }else{
                                    answerClassForSelected = "";
                                    attrChecked = "";
                                    answerDivClass = "";
                                }
                            }else{
                                answerClassForSelected = "";
                                attrChecked = "";
                                answerDivClass = "";
                            }
                        }
                        attrCheckedStyle2 = "style='padding:0 !important;'";
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(isAnswerCorrect){
                                    prefixIcon = '<i class="ays_fa answer-icon ays_fa_check_square_o"></i>';
                                    attrCheckedStyle3 = "";
                                }else{                                                        
                                    prefixIcon = '<i class="ays_fa answer-icon ays_fa_check_square_o"></i>';
                                    attrCheckedStyle3 = "background-color: rgba(243,134,129,0.8);";
                                }
                            }else{
                                if(showOnlyWrongAnswer === false){
                                    if(isAnswerCorrect){
                                        prefixIcon = '<i class="ays_fa answer-icon ays_fa_square_o"></i>';
                                        attrCheckedStyle3 = "";
                                    }else{
                                        prefixIcon = '<i class="ays_fa answer-icon ays_fa_square_o"></i>';
                                        attrCheckedStyle3 = "";
                                    }
                                }else{
                                    prefixIcon = '<i class="ays_fa answer-icon ays_fa_square_o"></i>';
                                    attrCheckedStyle3 = "";
                                }
                            }
                                attrCheckedStyle = "style='display:block!important;"+attrCheckedStyle3+"'";
                                attrCheckedStyle2 = "";
                                answerViewClass = "";
                           }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(isAnswerCorrect){
                                    answerDivClass = "correct_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                    attrCheckedStyle = "style='padding: 0!important;'";
                                }
                            }else{
                                if(showOnlyWrongAnswer === false){
                                    if(isAnswerCorrect){
                                        answerDivClass = "correct_div checked_answer_div";
                                    }else{
                                        answerDivClass = "";
                                    }
                                }else{
                                    answerDivClass = "";
                                }
                                attrCheckedStyle = "";
                            }
                        }
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_rect_light')){
                            if($(selectOptions[j]).prop('selected') == true){
                                if(isAnswerCorrect){
                                    answerDivClass = "correct_div checked_answer_div";
                                }else{
                                    answerDivClass = "wrong_div checked_answer_div";
                                }
                                rectAnswerBefore = "rect_answer_correct_before";
                            }else{
                                if(showOnlyWrongAnswer === false){
                                    if(isAnswerCorrect){
                                        answerDivClass = "correct_div checked_answer_div";
                                    }else{
                                        answerDivClass = "";
                                    }
                                }else{
                                    answerDivClass = "";
                                }
                                rectAnswerBefore = "rect_answer_wrong_before";
                            }
                        }

                        if( answerDivClass == ""  ){
                            continue;
                        } 
                        
                        correctAnswersDiv += '<div class="ays-field '+answerViewClass+' '+answerDivClass+'" '+attrCheckedStyle+'>' +
                                '<input type="radio" value="'+$(selectOptions[j]).attr("value")+'" name="'+$(item).parent().find('.ays-select-field-value').attr('name')+'" disabled="disabled" '+attrChecked+'>' +
                                '<label class="'+answerClassForSelected+'" for="ays-answer-'+$(selectOptions[j]).attr("value")+'">'+prefixIcon+aysEscapeHtml($(selectOptions[j]).text())+'</label> ' +
                                '<label for="ays-answer-'+$(selectOptions[j]).attr("value")+'" class="ays_answer_image ays_empty_before_content '+answerClassForSelected+'">'+ selectedOptionImageHTML +'</label>' +
                            '</div>';
                    }

                    if ( $(item).parents('.step').data('type') === 'matching' ) {
                        $(item).parents('.ays-abs-fs').parent().find('.ays-text-right-answer').remove();
                        $(item).parents('.ays-matching-field-match').append(correctAnswersDiv);
                    } else {
                        $(item).parent().parent().find('.ays-text-right-answer').remove();
                        $(item).parent().parent().append(correctAnswersDiv);
                        $(item).parent().hide();
                    }

                    if(selectedOption.length === 0 && questionType != 'matching' ){
                        var _parent_item = $(item).parents('.ays-abs-fs');

                        _parent_item.html(fieldset_html);
                        _parent_item.find('.ays_fieldset').append(ays_quiz_question_html);
                        _parent_item.find('.ays_fieldset').append(ays_quiz_question_img_html);
                        _parent_item.find('.ays_fieldset').append($(item).parents('.ays-quiz-answers'));

                        for (var i = 0; i < question_parts_arr.length; i++) {
                            _parent_item.find('.ays_fieldset').append( question_parts_arr[i] );
                        }

                        $(item).parents('.ays-abs-fs').css({
                            'padding': '7px'
                        });
                    }
                    $(item).parents('.ays-abs-fs').find('.ays_buttons_div').remove();
                    if ( $(item).parents('.step').data('type') === 'matching' ) {
                        $(item).parent().find('.select2.select2-container').remove();
                        $(item).remove();
                    } else {
                        $(item).parent().remove();
                    }
                });
                
                text_answer.next().next().remove();
                text_answer.css('width', '100%');
                text_answer.attr('disabled', 'disabled');
                number_answer.next().next().remove();
                number_answer.css('width', '100%');
                number_answer.attr('disabled', 'disabled');
                short_text_answer.next().next().remove();
                short_text_answer.css('width', '100%');
                short_text_answer.attr('disabled', 'disabled');
                // fillInBlankAnswers.next().next().remove();
                // fillInBlankAnswers.attr('disabled', 'disabled');
                date_answer.next().next().remove();
                date_answer.css('width', '100%');
                date_answer.attr('disabled', 'disabled');
                if(text_answer.val() == ''){
                    if(showOnlyWrongAnswer === false){
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                            thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();

                        rightAnswerText += '</div>';
                        if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                    }
                    text_answer.css('background-color', 'rgba(243,134,129,0.4)');
                    text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');

                    if (wrong_img_URL_HTML != "") {
                        var if_img_exists = text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                        if ( if_img_exists.length <= 0 ) {
                            text_answer.parent().append(wrong_img_URL_HTML);
                        }
                    }

                    var _text_answer_parent = text_answer.parents('.ays-abs-fs');

                    _text_answer_parent.html(fieldset_html);
                    _text_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _text_answer_parent.find('.ays_fieldset').append(ays_quiz_question_img_html);
                    _text_answer_parent.find('.ays_fieldset').append(text_answer.parents('.ays-quiz-answers'));

                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _text_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(text_answer.next().val()) == 1){
                        text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;

                        if (correct_img_URL_HTML != "") {
                            var if_img_exists = text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                text_answer.parent().append(correct_img_URL_HTML);
                            }
                        }

                    }else{
                        text_answer.css('background-color', 'rgba(243,134,129,0.4)');

                        if (wrong_img_URL_HTML != "") {
                            var if_img_exists = text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                text_answer.parent().append(wrong_img_URL_HTML);
                            }
                        }

                        if(showOnlyWrongAnswer === false){
                            var rightAnswerText = '<div class="ays-text-right-answer">';
                            var thisQuestionAnswer = text_answer.attr('chishtpatasxan');
                            if(typeof thisQuestionAnswer != 'undefined'){
                                thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                            }else{
                                thisQuestionAnswer = [''];
                            }
                            rightAnswerText += thisQuestionAnswer[0].trim();

                            rightAnswerText += '</div>';
                            if(text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                            }
                            text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                'display': 'block'
                            });
                            text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');
                        }
                    }
                }
                if(number_answer.val() == ''){
                    if(showOnlyWrongAnswer === false){
                        var rightAnswerText = '<div class="ays-text-right-answer">'+
                            number_answer.attr('chishtpatasxan')+
                        '</div>';
                        if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                    }
                    number_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    number_answer.parents('.ays-abs-fs').find('.ays-quiz-number-error-message').addClass('ays_display_none');

                    if (wrong_img_URL_HTML != "") {
                        var if_img_exists = number_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                        if ( if_img_exists.length <= 0 ) {
                            number_answer.parent().append(wrong_img_URL_HTML);
                        }
                    }

                    var _number_answer_parent = number_answer.parents('.ays-abs-fs');

                    _number_answer_parent.html(fieldset_html);
                    _number_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _number_answer_parent.find('.ays_fieldset').append(ays_quiz_question_img_html);
                    _number_answer_parent.find('.ays_fieldset').append(number_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _number_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    number_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(number_answer.next().val()) == 1){
                        number_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;

                        if (correct_img_URL_HTML != "") {
                            var if_img_exists = number_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                number_answer.parent().append(correct_img_URL_HTML);
                            }
                        }

                    }else{
                        number_answer.css('background-color', 'rgba(243,134,129,0.4)');

                        if (wrong_img_URL_HTML != "") {
                            var if_img_exists = number_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                number_answer.parent().append(wrong_img_URL_HTML);
                            }
                        }

                        if(showOnlyWrongAnswer === false){
                            var rightAnswerText = '<div class="ays-text-right-answer">'+
                                number_answer.attr('chishtpatasxan')+
                                '</div>';
                            if(number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                number_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                            }
                            number_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                'display': 'block'
                            });
                            number_answer.parents('.ays-abs-fs').find('.ays-quiz-number-error-message').addClass('ays_display_none');
                        }
                    }
                }
                if(short_text_answer.val() == ''){
                    if(showOnlyWrongAnswer === false){
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                        if(typeof thisQuestionAnswer != 'undefined'){
                            thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                            thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                        }else{
                            thisQuestionAnswer = [''];
                        }
                        rightAnswerText += thisQuestionAnswer[0].trim();

                        rightAnswerText += '</div>';
                        if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                    }
                    short_text_answer.css('background-color', 'rgba(243,134,129,0.8)');
                    short_text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');

                    if (wrong_img_URL_HTML != "") {
                        var if_img_exists = short_text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                        if ( if_img_exists.length <= 0 ) {
                            short_text_answer.parent().append(wrong_img_URL_HTML);
                        }
                    }

                    var _short_text_parent = short_text_answer.parents('.ays-abs-fs');

                    _short_text_parent.html(fieldset_html);
                    _short_text_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _short_text_parent.find('.ays_fieldset').append(ays_quiz_question_img_html);
                    _short_text_parent.find('.ays_fieldset').append(short_text_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _short_text_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    short_text_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(short_text_answer.next().val()) == 1){
                        short_text_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;

                        if (correct_img_URL_HTML != "") {
                            var if_img_exists = short_text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                short_text_answer.parent().append(correct_img_URL_HTML);
                            }
                        }

                    }else{
                        short_text_answer.css('background-color', 'rgba(243,134,129,0.4)');

                        if (wrong_img_URL_HTML != "") {
                            var if_img_exists = short_text_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                short_text_answer.parent().append(wrong_img_URL_HTML);
                            }
                        }

                        if(showOnlyWrongAnswer === false){
                            var rightAnswerText = '<div class="ays-text-right-answer">';
                            var thisQuestionAnswer = short_text_answer.attr('chishtpatasxan');
                            if(typeof thisQuestionAnswer != 'undefined'){
                                thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                                thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                            }else{
                                thisQuestionAnswer = [''];
                            }
                            rightAnswerText += thisQuestionAnswer[0].trim();

                            rightAnswerText += '</div>';
                            if(short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                short_text_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                            }
                            short_text_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                'display': 'block'
                            });
                            short_text_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');
                        }
                    }
                }
                if( fillInBlankAnswers.length > 0 ){
                    var if_fill_in_blank_empty = false;
                    var fill_in_blank_empty_arr = new Array();
                    var is_user_answer_correct = true;
                    var fillInBlankAnswers_last = false;
                    for (var i = 0; i < fillInBlankAnswers.length; i++) {
                        var fill_in_blank_answer = $( fillInBlankAnswers[i] );
                        // fill_in_blank_answer.next().remove();
                        // fill_in_blank_answer.next().next().remove();
                        fill_in_blank_answer.attr('disabled', 'disabled');
                        // console.log(fill_in_blank_answer);
                        var fillInBlankAnswers_last = false;
                        if( (fillInBlankAnswers.length - 1) == i ){
                            var fillInBlankAnswers_last = true;
                        }

                        if( fill_in_blank_answer.val() == '' ){
                            fill_in_blank_empty_arr.push('empty');
                        }

                        if( fill_in_blank_empty_arr.length == fillInBlankAnswers.length ){
                            if_fill_in_blank_empty = true;
                        }

                        if(if_fill_in_blank_empty && fillInBlankAnswers_last){
                            if(showOnlyWrongAnswer === false){
                                // var rightAnswerText = '<div class="ays-text-right-answer">';
                                // var thisQuestionAnswer = fill_in_blank_answer.attr('chishtpatasxan');
                                // if(typeof thisQuestionAnswer != 'undefined'){
                                //     thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                                //     thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                                // }else{
                                //     thisQuestionAnswer = [''];
                                // }
                                // rightAnswerText += thisQuestionAnswer[0].trim();

                                // rightAnswerText += '</div>';
                                // if(fill_in_blank_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                //     fill_in_blank_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                                // }
                                // fill_in_blank_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                //     'display': 'block'
                                // });
                            }
                            fill_in_blank_answer.css('background-color', 'rgba(243,134,129,0.4)');
                            fill_in_blank_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');

                            if (wrong_img_URL_HTML != "") {
                                var if_img_exists = fill_in_blank_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                                if ( if_img_exists.length <= 0 ) {
                                    fill_in_blank_answer.parent().append(wrong_img_URL_HTML);
                                }
                            }

                            var _fill_in_blank_answer_parent = fill_in_blank_answer.parents('.ays-abs-fs');

                            _fill_in_blank_answer_parent.html(fieldset_html);
                            _fill_in_blank_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                            // _fill_in_blank_answer_parent.find('.ays_fieldset').append(fill_in_blank_answer);
                            _fill_in_blank_answer_parent.find('.ays_fieldset').append(ays_quiz_question_img_html);
                            // _fill_in_blank_answer_parent.find('.ays_fieldset').append(fill_in_blank_answer.parents('.ays-quiz-answers'));
                            
                            for (var i = 0; i < question_parts_arr.length; i++) {
                                _fill_in_blank_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                            }

                            _fill_in_blank_answer_parent.parents('.ays-abs-fs').css({
                                'padding': '7px'
                            });
                        }else{
                            if(parseInt(fill_in_blank_answer.next().val()) == 1){
                                fill_in_blank_answer.css('background-color', 'rgba(39,174,96,0.5)');
                                fill_in_blank_answer.css('width', 'auto');

                            }else{
                                fill_in_blank_answer.css('background-color', 'rgba(243,134,129,0.4)');
                                fill_in_blank_answer.css('width', 'auto');

                                if( is_user_answer_correct ){
                                    is_user_answer_correct = false;
                                }

                                if(showOnlyWrongAnswer === false){
                                    var rightAnswerText = '<div class="ays-text-right-answer">';
                                    var thisQuestionAnswer = fill_in_blank_answer.attr('chishtpatasxan');
                                    if(typeof thisQuestionAnswer != 'undefined'){
                                        thisQuestionAnswer = aysEscapeHtmlDecode( thisQuestionAnswer ).aysStripSlashes();
                                        thisQuestionAnswer = thisQuestionAnswer.split('%%%');
                                    }else{
                                        thisQuestionAnswer = [''];
                                    }
                                    rightAnswerText += thisQuestionAnswer[0].trim();

                                    rightAnswerText += '</div>';
                                    if(fill_in_blank_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                        fill_in_blank_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                                    }
                                    // fill_in_blank_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                    //     'display': 'block'
                                    // });
                                    fill_in_blank_answer.parents('.ays-abs-fs').find('.ays_quiz_question_text_conteiner').addClass('ays_display_none');
                                }
                            }

                            
                        }
                    }

                    if( fillInBlankAnswers_last ){
                        if( is_user_answer_correct === true ){
                            answerIsRight = true;
                            if (correct_img_URL_HTML != "") {
                                var if_img_exists = $( fillInBlankAnswers[0] ).parents('.ays_quiz_question').find(".ays-quiz-check-button-right-wrong-icon");
                                if ( if_img_exists.length <= 0 ) {
                                    $( fillInBlankAnswers[0] ).parent().append(correct_img_URL_HTML);
                                }
                            }
                        } else {
                            if (wrong_img_URL_HTML != "") {
                                var if_img_exists = $( fillInBlankAnswers[0] ).parents('.ays_quiz_question').find(".ays-quiz-check-button-right-wrong-icon");
                                if ( if_img_exists.length <= 0 ) {
                                    $( fillInBlankAnswers[0] ).parent().append(wrong_img_URL_HTML);
                                }
                            }
                        }
                    }
                }

                if(date_answer.val() == ''){
                    if(showOnlyWrongAnswer === false){
                        var rightAnswerText = '<div class="ays-text-right-answer">';
                        var thisQuestionAnswer = date_answer.attr('chishtpatasxan');

                        var correctDate = new Date(thisQuestionAnswer),
                            correctDateYear = correctDate.getUTCFullYear(),
                            correctDateMonth = (correctDate.getUTCMonth() + 1) < 10 ? "0"+(correctDate.getUTCMonth() + 1) : (correctDate.getUTCMonth() + 1),
                            correctDateDay = (correctDate.getUTCDate() < 10) ? "0"+correctDate.getUTCDate() : correctDate.getUTCDate();
                        rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');

                        rightAnswerText += '</div>';
                        if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                            date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                        }
                        date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                            'display': 'block'
                        });
                    }
                    date_answer.css('background-color', 'rgba(243,134,129,0.8)');

                    if (wrong_img_URL_HTML != "") {
                        var if_img_exists = date_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                        if ( if_img_exists.length <= 0 ) {
                            date_answer.parent().append(wrong_img_URL_HTML);
                        }
                    }

                    var _date_answer_parent = date_answer.parents('.ays-abs-fs');

                    _date_answer_parent.html(fieldset_html);
                    _date_answer_parent.find('.ays_fieldset').append(ays_quiz_question_html);
                    _date_answer_parent.find('.ays_fieldset').append(ays_quiz_question_img_html);
                    _date_answer_parent.find('.ays_fieldset').append(date_answer.parents('.ays-quiz-answers'));
                    
                    for (var i = 0; i < question_parts_arr.length; i++) {
                        _date_answer_parent.find('.ays_fieldset').append( question_parts_arr[i] );
                    }

                    date_answer.parents('.ays-abs-fs').css({
                        'padding': '7px'
                    });
                }else{
                    if(parseInt(date_answer.next().val()) == 1){
                        date_answer.css('background-color', 'rgba(39,174,96,0.5)');
                        answerIsRight = true;

                        if (correct_img_URL_HTML != "") {
                            var if_img_exists = date_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                date_answer.parent().append(correct_img_URL_HTML);
                            }
                        }

                    }else{
                        date_answer.css('background-color', 'rgba(243,134,129,0.4)');

                        if (wrong_img_URL_HTML != "") {
                            var if_img_exists = date_answer.parent().find(".ays-quiz-check-button-right-wrong-icon");
                            if ( if_img_exists.length <= 0 ) {
                                date_answer.parent().append(wrong_img_URL_HTML);
                            }
                        }

                        if(showOnlyWrongAnswer === false){
                            var rightAnswerText = '<div class="ays-text-right-answer">';
                            var thisQuestionAnswer = date_answer.attr('chishtpatasxan');
                            var correctDate = new Date(thisQuestionAnswer),
                                correctDateYear = correctDate.getFullYear(),
                                correctDateMonth = (correctDate.getMonth() + 1) < 10 ? "0"+(correctDate.getMonth() + 1) : (correctDate.getMonth() + 1),
                                correctDateDay = (correctDate.getDate() < 10) ? "0"+correctDate.getDate() : correctDate.getDate();
                            rightAnswerText += [correctDateMonth, correctDateDay, correctDateYear].join('/');

                            rightAnswerText += '</div>';
                            if(date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').length == 0){
                                date_answer.parents('.ays-quiz-answers').append(rightAnswerText);
                            }
                            date_answer.parents('.ays-quiz-answers').find('.ays-text-right-answer').css({
                                'display': 'block'
                            });
                        }
                    }
                }

                if (checked_inputs.length === 1) {
                    if(parseInt(checked_inputs.prev().val()) === 1){
                        checked_inputs.parent().addClass('checked_answer_div').addClass('correct_div');
                        checked_inputs.parent().find('label').addClass('correct answered');
                        answerIsRight = true;
                        if(form.parents('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                           form.parents('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                            $(checked_inputs).next().css('background-color', "transparent");
                            $(checked_inputs).parent().css('background-color', ' rgba(158,208,100,0.8)');
                        }
                    }else{
                        if($(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                           $(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                            $(checked_inputs).next().css('background-color', "transparent");
                            $(checked_inputs).parent().css('background-color', ' rgba(243,134,129,0.8)');
                        }
                        checked_inputs.parent().addClass('checked_answer_div').addClass('wrong_div');
                        checked_inputs.parent().find('label').addClass('wrong wrong_div answered');
                    }

                    if($(document).find('.ays-quiz-container').hasClass('ays_quiz_elegant_dark') ||
                       $(document).find('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
                        $(this).next().css('padding', "0 10px 0 10px");
                    }
                    $(checked_inputs).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);
                }else if(checked_inputs.length > 1){
                    var checked_right = 0;
                    checked_inputs.map(function() {
                        if(parseInt($(this).prev().val()) === 1){
                            $(this).parent().addClass('checked_answer_div').addClass('correct_div');
                            $(this).parent().find('label').addClass('correct answered');
                            if($(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                               $(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                                $(this).next().css('background-color', "transparent");
                                $(this).parent().css('background-color', ' rgba(158,208,100,0.8)');
                            }
                        }else{ 
                            $(this).parent().addClass('checked_answer_div').addClass('wrong_div');
                            $(this).parent().find('label').addClass('wrong wrong_div answered');
                            checked_right++;
                            if($(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                               $(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                                $(this).next().css('background-color', "transparent");
                                $(this).parent().css('background-color', ' rgba(243,134,129,0.8)');
                            }
                        }
                        if($(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_dark') ||
                           $(document).find('.ays-quiz-container').hasClass('ays_quiz_modern_light')){
                        }

                        if($(document).find('.ays-quiz-container').hasClass('ays_quiz_elegant_dark') ||
                           $(document).find('.ays-quiz-container').hasClass('ays_quiz_elegant_light')){
                        }
                        $(this).parents().eq(3).find('input[name^="ays_questions"]').attr('disabled', true);
                    });
                    if(checked_right == 0){
                        answerIsRight = true;
                    }
                }

                if (hideRightAnswers) {
                    question.find('.ays-text-right-answer').addClass("ays_quiz_display_none_important");
                    var aysFieldsets = question.find('fieldset.ays_fieldset');
                    if (aysFieldsets.length > 0) {
                        var aysFieldsetsField = aysFieldsets.find('.ays-field');
                        if (aysFieldsetsField.hasClass('correct_div')) {
                            aysFieldsetsField.removeClass('correct_div');
                        }
                        if (aysFieldsetsField.hasClass('checked_answer_div')) {
                            aysFieldsetsField.removeClass('checked_answer_div');
                        }
                        if (aysFieldsetsField.find('label').hasClass('correct')) {
                            aysFieldsetsField.find('label').removeClass('correct');
                        }
                    }
                    var answers_box = question.find('.ays-quiz-answers');
                    if (answers_box.length > 0) {
                        answers_box.each(function () {
                           var userWrongAnswered = $(this).find('.ays-field');
                           var userWrongAnsweredLabel = userWrongAnswered.find('label');
                           var questionTypeCheckbox = userWrongAnswered.find('input[type="checkbox"]');
                           if (userWrongAnsweredLabel.hasClass('wrong') || questionTypeCheckbox.length > 0) {
                                if (userWrongAnsweredLabel.hasClass('correct') && userWrongAnsweredLabel.hasClass('answered')) {
                                    var eachAnswers = userWrongAnswered.find('input[name^="ays_questions"]');
                                    if (eachAnswers.length > 0) {
                                        eachAnswers.each(function () {
                                            var parentBox = $(this).parents('.ays-field');
                                            if (! $(this).prop("checked")) {
                                                $(this).next().removeClass('correct');
                                                if (parentBox.hasClass('correct_div')) {
                                                    parentBox.removeClass('correct_div');
                                                }
                                                if (parentBox.hasClass('checked_answer_div')) {
                                                    parentBox.removeClass('checked_answer_div');
                                                }
                                                if (! parentBox.hasClass('.checked_answer_div') ) {
                                                    parentBox.find('input~label.answered.correct').addClass('ays_quiz_hide_correct_answer');
                                                }
                                            }
                                        });
                                    }
                                }
                           }
                        });
                    }
                }

                answerIsRightArr[ 'questionId_' + questionId ] = answerIsRight;

                if(myOptions.answers_rw_texts && (myOptions.answers_rw_texts == 'on_results_page' || myOptions.answers_rw_texts == 'on_both')){
                    if(answerIsRight){
                        question.find('.right_answer_text').css("display", "block");
                    }else{
                        question.find('.wrong_answer_text').css("display", "block");
                    }
                }else{
                    question.find('.right_answer_text').css("display", "none");
                    question.find('.wrong_answer_text').css("display", "none");
                }
                // question.find('.ays_questtion_explanation').css("display", "block");
                var showExplanationOn = (myOptions.show_questions_explanation && myOptions.show_questions_explanation != "") ? myOptions.show_questions_explanation : "on_results_page";
                if(showExplanationOn == 'on_results_page' || showExplanationOn == 'on_both'){
                    if(! question.hasClass('not_influence_to_score')){
                        question.find('.ays_questtion_explanation').css("display", "block");
                    }
                }else{
                    question.find('.ays_questtion_explanation').css("display", "none");
                }
                question.find('.ays_user_explanation').css("display", "none");
                question.css("pointer-events", "auto");
                question.find('.ays-quiz-answers').css("pointer-events", "none");

                question.find('.ays-quiz-answers .ays-field input').removeAttr("name").removeAttr("id");

                formResults.append(question);
            }

            myOptions.quiz_show_wrong_answers_first = ! myOptions.quiz_show_wrong_answers_first ? 'off' : myOptions.quiz_show_wrong_answers_first;
            var quiz_show_wrong_answers_first = (myOptions.quiz_show_wrong_answers_first && myOptions.quiz_show_wrong_answers_first == 'on') ? true : false;

            myOptions.quiz_show_only_wrong_answers = ! myOptions.quiz_show_only_wrong_answers ? 'off' : myOptions.quiz_show_only_wrong_answers;
            var quiz_show_only_wrong_answers = (myOptions.quiz_show_only_wrong_answers && myOptions.quiz_show_only_wrong_answers == 'on') ? true : false;

            if ( quiz_show_wrong_answers_first || quiz_show_only_wrong_answers) {
                var UserAnswered_true_arr  = new Array();
                var UserAnswered_false_arr = new Array();
                for (var question_ID in answerIsRightArr) {

                    var question_ID_arr = question_ID.split("_");
                    var questionDataID  = question_ID_arr[1];

                    var UserAnswered_true_or_false = answerIsRightArr[question_ID];
                    var questionHTML = form.find('.ays_quiz_results div.ays_question_result[data-question-id="'+ questionDataID +'"]').clone();

                    if ( UserAnswered_true_or_false ) {
                        UserAnswered_true_arr.push( questionHTML );
                    } else {
                        UserAnswered_false_arr.push( questionHTML );
                    }
                }

                if ( quiz_show_only_wrong_answers ) {
                    UserAnswered_true_arr = new Array();
                }

                var allQuestionHTML = UserAnswered_false_arr.concat( UserAnswered_true_arr );

                formResults.html('');

                if ( quiz_enable_results_toggle ) {
                    formResults.append(resultToggleHTML);
                }

                for (var ii = 0; ii < allQuestionHTML.length; ii++) {
                    formResults.append( allQuestionHTML[ii] );
                }
            }
        }
        
        var showResults = true;
        if ( (myOptions.enable_paypal && myOptions.enable_paypal === 'on') || 
            ( myOptions.enable_stripe && myOptions.enable_stripe === 'on' ) ) {
            if (myOptions.payment_type && myOptions.payment_type === 'postpay') {
                showResults = false;
            }else{
                showResults = true;
            }
        }

        if( showResults === true ){
            form.find('.ays_quiz_results').slideDown(1000);
        }

        form.find('.ays_quiz_rete').fadeIn(250);
        form.find('.for_quiz_rate').rating({
            onRate: function(res){
                // $(this).rating('disable');
                $(this).parent().find('.for_quiz_rate_reason').slideDown(500);
                $(this).parents('.ays_quiz_rete').attr('data-rate_score', res);
            }
        });

        if( quiz_enable_keyboard_navigation ){
            form.find('.for_quiz_rate > i').addClass('ays-quiz-keyboard-active');
            form.find('.for_quiz_rate > i').attr('tabindex', '0');
        }

        var aysQuizLoader = form.find('div[data-role="loader"]');
        aysQuizLoader.addClass('ays-loader');
        aysQuizLoader.removeClass(aysQuizLoader.data('class'));
        aysQuizLoader.find('.ays-loader-content').css('display','none');

        var openResultsPage = true;
        window.aysResultsForQuizStored = false;
        if ( (myOptions.enable_paypal && myOptions.enable_paypal === 'on') || 
            (myOptions.enable_stripe && myOptions.enable_stripe === 'on') ) {
            if (myOptions.payment_type && myOptions.payment_type === 'prepay') {
                openResultsPage = true;
            }else{
                openResultsPage = false;
                if( !window.aysResultsForQuizStored ) {
                    window.aysResultsForQuiz = form.find('.ays_quiz_results_page').html();
                    window.aysResultsForQuizStored = true;
                    form.find('.ays_quiz_results_page').html('');
                }
            }
        }

        if( openResultsPage === true ){
            form.find('.ays_quiz_results_page').css({'display':'block'});
        }

        form.find('.ays_paypal_wrap_div, .ays_stripe_wrap_div').css({'display':'block'});
        form.find('.ays_paypal_wrap_div, .ays_stripe_wrap_div').attr('data-result', response.result_id);

        form.css({'display':'block'});

        aysResizeiFrame();

        form.on('click', '.ays_quiz_rete .for_quiz_rate_reason .action-button', function(){

            var _this = $(this);
            var _parent = _this.parents('.for_quiz_rate_reason');
            
            if(myOptions.quiz_make_review_required == 'on' && myOptions.quiz_make_review_required == 'on'){

                var _el = _parent.find('.quiz_rate_reason[data-required="true"]');

                if ( ! _this.hasClass('start_button') ) {
                    if ( _el.length !== 0 ) {
                        var empty_inputs = 0;

                        if (_el.val().trim() === '' &&
                            _el.attr('type') !== 'hidden') {
                            _el.addClass('ays_red_border');
                            _el.addClass('ays_animated_x5ms');
                            _el.addClass('shake');
                            empty_inputs++;
                        }

                        var errorFields = _parent.find('.ays_red_border');
                        if ( empty_inputs !== 0 ) {
                            setTimeout(function(){
                                errorFields.each(function(){
                                    $(this).removeClass('shake');
                                });
                            }, 500);
                            return false;
                        }
                    }
                }
            }

            $(this).parents('.for_quiz_rate_reason').find('.quiz_rate_reason').attr('disabled', 'disabled');
            var data = {};
            var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            var quizCusrrentPageLink = form.parents('.ays-quiz-container').find('input[name="ays_quiz_curent_page_link"]').val();
            var enableUserCհoosingCheckbox = form.parents('.ays-quiz-container').find('.ays-quiz-user-cհoosing-anonymous-assessment .ays-quiz-user-cհoosing-anonymous-assessment:checked');
            
            var enableUserCհoosingCheckboxFlag = false;
            if( enableUserCհoosingCheckbox.length > 0 ){
            var enableUserCհoosingCheckboxFlag = true;
            }

            data.action = 'ays_rate_the_quiz';
            data.rate_reason = $(this).parents('.for_quiz_rate_reason').find('.quiz_rate_reason').val();
            data.rate_score = $(this).parents('.ays_quiz_rete').data('rate_score');
            data.rate_date = GetFullDateTime();
            data.quiz_id = quizId;
            data.last_result_id = last_result_id;
            data.quiz_make_responses_anonymous = quiz_make_responses_anonymous;
            data.quiz_current_page_link = quizCusrrentPageLink;
            data.quiz_enable_user_cհoosing_anonymous_assessment = quiz_enable_user_cհoosing_anonymous_assessment;
            data.quiz_enable_user_cհoosing_anonymous_assessment_checkbox_flag = enableUserCհoosingCheckboxFlag;
            form.find('.for_quiz_rate_reason').slideUp(800);
            var showAvgRate = false;
            myOptions.show_rate_after_rate = (myOptions.show_rate_after_rate) ? myOptions.show_rate_after_rate : 'on';
            if(myOptions.show_rate_after_rate && myOptions.show_rate_after_rate == 'on'){
                showAvgRate = true;
            }
            if(showAvgRate){
                $(this).parents('.ays_quiz_rete').find('.lds-spinner-none').addClass('lds-spinner').removeClass('lds-spinner-none');
            }
            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                $(this).parents('.ays_quiz_rete').find('.lds-spinner2-none').addClass('lds-spinner2').removeClass('lds-spinner2-none');
            }
            $.ajax({
                url: quiz_maker_ajax_public.ajax_url,
                method: 'post',
                dataType: 'json',
                crossDomain: true,
                data: data,
                success: function(response){
                    if(response.status === true){
                        setTimeout(function(){
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').attr('data-rating', response.score);
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating({
                                initialRating: response.score
                            });
                            form.find('.ays_quiz_rete').find('.for_quiz_rate').rating('disable');
                            form.find('.ays_quiz_rete').find('.ays-quiz-user-cհoosing-anonymous-assessment').hide();
                            if(showAvgRate){
                                form.find('.lds-spinner').addClass('lds-spinner-none').removeClass('lds-spinner');
                                form.find('.for_quiz_rate_reason').html('<p>'+response.rates_count + ' ' + quizLangObj.votes + ', ' + response.avg_score + ' ' + quizLangObj.avg + ' </p>');
                                form.find('.for_quiz_rate_reason').fadeIn(250);
                            }

                            var review_ty_message = form.find('.ays-quiz-review-thank-you-message');
                            if ( review_ty_message.length > 0 ) {
                                if ( review_ty_message.hasClass('ays_display_none') ) {
                                    review_ty_message.removeClass('ays_display_none');
                                }
                            }

                            if(myOptions.enable_quiz_rate == 'on' && myOptions.enable_rate_comments == 'on'){
                                var data = {};
                                data.action = 'ays_get_rate_last_reviews';
                                data.quiz_id = response.quiz_id;
                                $.ajax({
                                    url: quiz_maker_ajax_public.ajax_url,
                                    method: 'post',
                                    crossDomain: true,
                                    data: data,
                                    success: function(response){
                                        var response_arr = JSON.parse(response);
                                        var responseHTML = (response_arr.quiz_rate_html && response_arr.quiz_rate_html != '') ? response_arr.quiz_rate_html : '';

                                        form.find('.quiz_rate_reasons_body').html(responseHTML);
                                        form.find('.lds-spinner2').addClass('lds-spinner2-none').removeClass('lds-spinner2');
                                        form.find('.quiz_rate_reasons_container').slideDown(500);
                                        form.find('.ays-quiz-rate-link-box .ays-quiz-rate-link').slideUp(500);
                                        form.on('click', 'button.ays_load_more_review', function(e){
                                            form.find('.quiz_rate_load_more [data-role="loader"]').addClass(form.find('.quiz_rate_load_more .ays-loader').data('class')).removeClass('ays-loader');
                                            var startFrom = parseInt($(e.target).attr('startfrom'));
                                            var zuyga = parseInt($(e.target).attr('zuyga'));
                                            $.ajax({
                                                url: quiz_maker_ajax_public.ajax_url,
                                                method: 'post',
                                                crossDomain: true,
                                                data:{
                                                    action: 'ays_load_more_reviews',
                                                    quiz_id: quizId,
                                                    start_from: startFrom,
                                                    zuyga: zuyga
                                                },
                                                success: function(resp){
                                                    if(zuyga == 0){
                                                        zuyga = 1;
                                                    }else{
                                                        zuyga = 0;
                                                    }
                                                    
                                                    form.find('.quiz_rate_load_more [data-role="loader"]').addClass('ays-loader').removeClass(form.find('.quiz_rate_load_more .ays-loader').data('class'));
                                                    form.find('.quiz_rate_reasons_container').append(resp);
                                                    form.find('.quiz_rate_more_review:last-of-type').slideDown(500);
                                                    $(e.target).attr('startfrom', startFrom + 5 );
                                                    $(e.target).attr('zuyga', zuyga);
                                                    if(form.find('.quiz_rate_reasons_container p.ays_no_more').length > 0){
                                                        $(e.target).remove();
                                                    }
                                                }
                                            });
                                        });
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) {}
                                });
                            }
                        },1000);
                    }
                }
            });
        });
    }

    function redirect_timer(timer, args) {
        if( typeof args == 'undefined' ){
           args = {};
        }

        var tabTitle = document.title;

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
                $(document).find('.ays-quiz-container').find('div.ays-quiz-redirection-timer').html(timeForShow);
                document.title = timeForShow + " - " + tabTitle;
            }else{
                 $(document).find('.ays-quiz-container').find('div.ays-quiz-redirection-timer').html(timeForShow);
                document.title = timeForShow + " - " + tabTitle;
            }
            $(document).find('.ays-quiz-container').find('div.ays-quiz-redirection-timer').slideDown(500);
            var ays_interval_element_redirect_url = args.redirectUrl;

            if (distance <= 1000) {
                clearInterval(x);
                if (window.location != window.parent.location) {
                    window.parent.location = ays_interval_element_redirect_url;
                } else {
                    window.location = ays_interval_element_redirect_url;
                }
            }
        }, 1000);
    }
    
    function redirectAction(redirectUrl, redirectDelay, blockElement, redirectAfter, form, myOptions, redirectMessageFlag, uniqueCode){
        redirectUrl = redirectUrl.includes('?') ? redirectUrl.replace('[uniquecode]', "&uniquecode=" + uniqueCode ) : redirectUrl.replace('[uniquecode]', "?uniquecode=" + uniqueCode);

        var timer = parseInt(redirectDelay);
        if(timer === NaN){
            timer = 0;
        }

        if( timer == 0 ){
            if (window.location != window.parent.location) {
                window.parent.location = redirectUrl;
            } else {
                window.location = redirectUrl;
            }
            return false;
        }

        if ( redirectMessageFlag ) {
            var quizId = form.parents('.ays-quiz-container').find('input[name="ays_quiz_id"]').val();
            // Message before redirect timer
            var quiz_message_before_redirect_timer = (myOptions.quiz_message_before_redirect_timer && myOptions.quiz_message_before_redirect_timer != "") ? ( myOptions.quiz_message_before_redirect_timer ) : '';

            if ( quiz_message_before_redirect_timer != '' ) {
                quiz_message_before_redirect_timer = quiz_message_before_redirect_timer.replace(/(["'])/g, "\\$1") + " ";

                $(document).find('html > head').append('<style> #ays-quiz-container-'+ quizId +' div.ays-quiz-redirection-timer:before{content: "'+ quiz_message_before_redirect_timer +'"; }</style>');
            }
        }

        var tabTitle = document.title;
        var timerText = $('<section class="ays_quiz_redirection_timer_container">'+
            '<div class="ays-quiz-redirection-timer">'+
            quizLangObj.redirectAfter + ' ' + redirectAfter +
            '</div><hr></section>');
        form.parents('.ays-quiz-container').prepend(timerText);
        blockElement.find('.ays_quiz_redirection_timer_container').css({
            height: 'auto'
        });
        setTimeout(function(){
            if (timer !== NaN) {
                timer += 2;
                if (timer !== undefined) {
                    blockElement.find('div.ays-quiz-redirection-timer').slideUp(500);
                    redirect_timer( timer,  {
                        redirectUrl: redirectUrl
                    });
                }
            }
        }, 2000);

        return true;
    }

    function aysQuizSetCustomEvent() {
        if ( typeof window.CustomEvent === "function" ) return false; //If not IE
    
        function CustomEvent ( event, params ) {
            params = params || { bubbles: false, cancelable: false, detail: undefined };
            var evt = document.createEvent( 'CustomEvent' );
            evt.initCustomEvent( event, params.bubbles, params.cancelable, params.detail );
            return evt;
        }
    
        CustomEvent.prototype = window.Event.prototype;
    
        window.CustomEvent = CustomEvent;
    }

    function checkQuestionTimer( container, quizId ){
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
                ){
                    continue;
                }else{
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

    function stopQuestionTimer( questionID, quizId, myOptions ){
        if( myOptions.enable_timer && myOptions.enable_timer === 'on' ) {
            if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
                if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
                    if (window.aysQuizQuestionTimers[quizId][questionID] !== null) {
                        clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
                        window.aysQuizQuestionTimers[quizId][questionID].stopped = true;
                        window.aysQuizQuestionTimers[quizId][questionID].ended = true;
                    }
                }
            }
        }
    }

    // function stopQuestionTimer( container, questionID, myOptions ){
    //     if( myOptions.enable_timer && myOptions.enable_timer === 'on' ) {
    //         if (myOptions.quiz_timer_type && myOptions.quiz_timer_type === 'question_timer') {
    //             if (window.aysQuizQuestionTimers && window.aysQuizQuestionTimers[quizId]) {
    //                 if (window.aysQuizQuestionTimers[quizId][questionID] !== null) {
    //                     // clearInterval(window.aysQuizQuestionTimers[quizId][questionID].timeout);
    //                     window.aysQuizQuestionTimers[quizId][questionID].stopped = true;
    //                     var qid = checkQuestionTimer( container, quizId );
    //                     if (qid === false) {
    //                         container.find('input.ays_finish').addClass('ays-quiz-after-timer-end');
    //                     }
    //                 }
    //             }
    //         }
    //     }
    // }


})(jQuery);
