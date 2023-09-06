var current_fs, next_fs, previous_fs; //fieldsets
var left, opacity, scale; //fieldset properties which we will animate
var animating, percentAnimate; //flag to prevent quick multi-click glitches

function aysAnimateStep(animation, current_fs, next_fs){

    if(typeof next_fs !== "undefined"){
        switch(animation){
            case "lswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });

                current_fs.addClass('swing-out-right-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                next_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                    next_fs.css('display', 'flex');
                    next_fs.addClass('swing-in-left-fwd');
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    next_fs.css({
                        'position':'relative',
                        'pointer-events': 'auto'
                    });
                    current_fs.removeClass('swing-out-right-bck');
                    next_fs.removeClass('swing-in-left-fwd');
                    animating = false;
                },1000);
            break;
            case "rswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });

                current_fs.addClass('swing-out-left-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                next_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                    next_fs.css('display', 'flex');
                    next_fs.addClass('swing-in-right-fwd');
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    next_fs.css({
                        'position':'relative',
                        'pointer-events': 'auto'
                    });
                    current_fs.removeClass('swing-out-left-bck');
                    next_fs.removeClass('swing-in-right-fwd');
                    animating = false;
                },1000);
            break;
            case "shake":
                next_fs.css('transform', 'scale(1)');
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        scale = 1 - (1 - now) * 0.2;
                        left = (now * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')',
                            'position': 'absolute',
                            'top':0,
                            'opacity': 1,
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'left': left,
                            'opacity': opacity,
                            'display':'flex',
                            'position':'relative',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 800,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({
                            'pointer-events': 'auto',
                            'opacity': 1,
                            'position': 'static'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            case "fade":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        opacity = 1 - now;
                        current_fs.css({
                            'position': 'absolute',
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'opacity': opacity,
                            'position':'relative',
                            'display':'flex',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 500,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({
                            'pointer-events': 'auto',
                            'position': 'static'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'opacity': 1,
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    }
                });
            break;
            default:
                current_fs.animate({}, {
                    step: function (now, mx) {
                        current_fs.css({
                            'pointer-events': 'none'
                        });
                        next_fs.css({
                            'position':'relative',
                            'pointer-events': 'none'
                        });
                    },
                    duration: 0,
                    complete: function () {
                        current_fs.hide();
                        current_fs.css({
                            'pointer-events': 'auto'
                        });
                        next_fs.css({
                            'display':'flex',
                            'position':'relative',
                            'transform':'scale(1)',
                            'pointer-events': 'auto'
                        });
                        animating = false;
                    }
                });
            break;
        }
    }else{
        switch(animation){
            case "lswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });
                current_fs.addClass('swing-out-right-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    current_fs.removeClass('swing-out-right-bck');
                    animating = false;
                },1000);
            break;
            case "rswing":
                current_fs.parents('.ays-questions-container').css({
                    perspective: '800px',
                });
                current_fs.addClass('swing-out-left-bck');
                current_fs.css({
                    'pointer-events': 'none'
                });
                setTimeout(function(){
                    current_fs.css({
                        'position': 'absolute',
                    });
                },400);
                setTimeout(function(){
                    current_fs.hide();
                    current_fs.css({
                        'pointer-events': 'auto',
                        'position': 'static'
                    });
                    current_fs.removeClass('swing-out-left-bck');
                    animating = false;
                },1000);
            case "shake":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        scale = 1 - (1 - now) * 0.2;
                        left = (now * 50) + "%";
                        opacity = 1 - now;
                        current_fs.css({
                            'transform': 'scale(' + scale + ')',
                        });
                    },
                    duration: 800,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            case "fade":
                current_fs.animate({opacity: 0}, {
                    step: function (now, mx) {
                        opacity = 1 - now;
                    },
                    duration: 500,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    },
                    easing: 'easeInOutBack'
                });
            break;
            default:
                current_fs.animate({}, {
                    step: function (now, mx) {

                    },
                    duration: 0,
                    complete: function () {
                        current_fs.hide();
                        animating = false;
                    }
                });
            break;
        }
    }
}

/**
 * @return {string}
 */
function GetFullDateTime(){
    var now = new Date();
    return [[now.getFullYear(), AddZero(now.getMonth() + 1), AddZero(now.getDate())].join("-"), [AddZero(now.getHours()), AddZero(now.getMinutes()), AddZero(now.getSeconds())].join(":")].join(" ");
}

/**
 * @return {string}
 */
function AddZero(num) {
    return (num >= 0 && num < 10) ? "0" + num : num + "";
}

/**
 * @return {string}
 */
function aysEscapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
}

/**
 * @return {string}
 */
var aysEscapeHtmlDecode = (function() {
    // this prevents any overhead from creating the object each time
    var element = document.createElement('div');

    function decodeHTMLEntities (str) {
        if(str && typeof str === 'string') {
            // strip script/html tags
            str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
            str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
            element.innerHTML = str;
            str = element.textContent;
            element.textContent = '';
        }

        return str;
    }

    return decodeHTMLEntities;
})();

String.prototype.aysStripSlashes = function(){
    return this.replace(/\\(.)/mg, "$1");
}

function getObjectKey(object, value, type) {
    if ( ! type ) {
        type = 'string';
    }

    for ( var key in object ) {
        if( type === 'number' ) {
            if ( Number( object[key] ) === Number( value ) ) return key;
        } else if ( type === 'bool' ) {
            if ( Boolean( object[key] ) === Boolean( value ) ) return key;
        } else {
            if ( object[key] === value ) return key;
        }
    }
}

function audioVolumeIn(q){
    if(q.volume){
        var InT = 0;
        var setVolume = 1; // Target volume level for new song
        var speed = 0.05; // Rate of increase
        q.volume = InT;
        var eAudio = setInterval(function(){
            InT += speed;
            q.volume = InT.toFixed(1);
            if(InT.toFixed(1) >= setVolume){
                q.volume = 1;
                clearInterval(eAudio);
                //alert('clearInterval eAudio'+ InT.toFixed(1));
            };
        },50);
    };
};

function audioVolumeOut(q){
    if(q.volume){
        var InT = 1;
        var setVolume = 0;  // Target volume level for old song
        var speed = 0.05;  // Rate of volume decrease
        q.volume = InT;
        var fAudio = setInterval(function(){
            InT -= speed;
            q.volume = InT.toFixed(1);
            if(InT.toFixed(1) <= setVolume){
                clearInterval(fAudio);
                //alert('clearInterval fAudio'+ InT.toFixed(1));
            };
        },50);
    };
};

function isPlaying(audelem) {
    return !audelem.paused;
}

function resetPlaying(audelems) {
    for(var i = 0; i < audelems.length; i++){
        audelems[i].pause();
        audelems[i].currentTime = 0;
    }
    // return !audelem.paused;
}

function validatePhoneNumber(input) {
	var phoneno = /^[+ 0-9-]+$/;
	if (input.value.match(phoneno)) {
		return true;
	} else {
		return false;
	}
}

function checkQuizPassword(e, myOptions, isAlert){
    var passwordQuizInput = jQuery(e.target).parents('.step').find("input.ays_quiz_password");
    if(passwordQuizInput.length > 0){
        var passwordQuiz = passwordQuizInput.val();
        if(myOptions.enable_password && myOptions.enable_password == 'on'){
            if(myOptions.generate_password && myOptions.generate_password == 'generated_password'){
                var generated_passwords = myOptions.generated_passwords;
                var active_passwords = generated_passwords.active_passwords;
                var flag = false;
                for (var index in active_passwords) {
                    if(active_passwords[index] == passwordQuiz){
                        flag = true;
                        break;
                    }
                }
                if( flag === false ){
                    if(isAlert){
                        alert( quizLangObj.passwordIsWrong );
                    }
                    return false;
                }
            }else{
                if(myOptions.password_quiz && myOptions.password_quiz !== passwordQuiz){
                    if(isAlert){
                        alert( quizLangObj.passwordIsWrong );
                    }
                    return false;
                }
            }
        }
    }
    return true;
}


function ays_countdown_datetime(sec, showMessage, quizId) {
    var distance = sec*1000;
    var x_int;

    // Update the count down every 1 second
    x_int = setInterval(function() {
        // Time calculations for days, hours, minutes and seconds
        var days = Math.floor(distance / (1000 * 60 * 60 * 24));
        var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Output the result in an element with id="demo"
        var text = "";

        if(days > 0){
            text += days + " ";
            if(days == 1){
                text += quizLangObj.day + " ";
            }else{
                text += quizLangObj.days + " ";
            }
        }

        if(hours > 0){
            text += hours + " ";
            if(hours == 1){
                text += quizLangObj.hour + " ";
            }else{
                text += quizLangObj.hours + " ";
            }
        }

        if(minutes > 0){
            text += minutes + " ";
            if(minutes == 1){
                text += quizLangObj.minute + " ";
            }else{
                text += quizLangObj.minutes + " ";
            }
        }

        text += seconds + " " + quizLangObj.seconds;

        jQuery(document).find("#"+ quizId +" .show_timer_countdown").html(text);

        // If the count down is over, write some text
        if (distance > 0) {
            distance -= 1000;
        }
        if (distance <= 0) {
            clearInterval(x_int);
            jQuery(document).find("#"+ quizId +" .show_timer_countdown").html('');
        }
        if(distance == 0){
            location.reload();
        }
    }, 1000);
}

function ays_quiz_is_question_required( requiredQuestions ){
     animating = false;

     if ( requiredQuestions.length !== 0) {
         var empty_inputs = 0;
         var errorMessage, ays_quiz_container;
         requiredQuestions.removeClass('ays-has-error');

         for (var i = 0; i < requiredQuestions.length; i++) {
            var item = requiredQuestions.eq(i);
            ays_quiz_container = item.parents(".ays-quiz-container");
            break;
         }

         ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question").removeClass('ays-has-error');

         for (var i = 0; i < requiredQuestions.length; i++) {
             var item = requiredQuestions.eq(i);
             var questionId = item.attr('data-question-id');

             if( item.data('type') == 'text' || item.data('type') == 'short_text' || item.data('type') == 'number' ){
                 if( item.find( '.ays-text-field .ays-text-input' ).val() == '' ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     item.find( '.ays-text-field .ays-text-input' ).focus();
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }

             var errorFlag = false;
             if ( item.data('type') == 'radio' || item.data('type') == 'checkbox' ) {

                 if( item.find('input[name^="ays_questions"]:checked').length == 0 ){
                     errorFlag = true;
                 }

                 if( errorFlag ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }

             if ( item.data('type') == 'select' ) {
                 if( item.find('.ays-select-field .ays-select-field-value').val() == '' ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }

             if ( item.data('type') == 'matching' ) {
                 var filledCount = 0;
                 item.find('.ays-field .ays-select-field-value').each( function ( i, item ) {
                    var __this = jQuery(item)
                     if( __this.val() == '' ) {
                         filledCount++;
                     }
                 });

                 if( filledCount !== item.find('.ays-select-field .ays-select-field-value').length ) {
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }

             if ( item.data('type') == 'fill_in_blank' ) {
                 var fillInBlankCount = 0;
                 item.find('.ays_quiz_question .ays-quiz-fill-in-blank-input').each( function ( i, item ) {
                    var __this = jQuery(item);

                    if( __this.val() == '' ) {
                        fillInBlankCount++;
                    }
                 });

                 if( fillInBlankCount > 0 ) {
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }

             if ( item.data('type') == 'date' ) {
                 if( item.find('input[type="date"]').val() == '' ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

                     ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays_questions_nav_question[data-id='"+ questionId +"']").addClass('ays-has-error shake ays_animated_x5ms');

                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }
         }

         if (empty_inputs !== 0) {
             var errorFields = ays_quiz_container.find(".ays-quiz-questions-nav-wrap .ays-has-error");
             requiredQuestions.parents('.ays-quiz-container').addClass('ays-quiz-has-error');
             if ( errorFields.length !== 0 ) {
                 setTimeout(function(){
                    errorFields.each(function(){
                        jQuery(this).removeClass('shake ays_animated_x5ms');
                    });
                 }, 500);
             }
             return false;
         }else{
             requiredQuestions.parents('.ays-quiz-container').removeClass('ays-quiz-has-error');
             return true;
         }
     }
     return true;
}

function ays_quiz_is_question_empty( questions ){
    animating = false;

    if ( questions.length !== 0) {
        var empty_inputs = 0;
        var errorMessage;
        for (var i = 0; i < questions.length; i++) {
            var item = questions.eq(i);
            if( item.data('type') == 'text' || item.data('type') == 'short_text' || item.data('type') == 'number' ){
                if( item.find( '.ays-text-field .ays-text-input' ).val() == '' ){
                    empty_inputs++;
                    break;
                }else{
                    continue;
                }
            }

            var errorFlag = false;
            if ( item.data('type') == 'radio' || item.data('type') == 'checkbox' ) {

                if( item.find('input[name^="ays_questions"]:checked').length == 0 ){
                    errorFlag = true;
                }

                if( errorFlag ){
                    empty_inputs++;
                    break;
                }else{
                    continue;
                }
            }

            if ( item.data('type') == 'select' ) {
                if( item.find('.ays-select-field .ays-select-field-value').val() == '' ){
                    empty_inputs++;
                    break;
                }else{
                    continue;
                }
            }

            if ( item.data('type') == 'matching' ) {
                var filledCount = 0;
                item.find('.ays-select-field .ays-select-field-value').each( function ( i, item ) {
                    if( item.find('.ays-select-field .ays-select-field-value').val() == '' ) {
                        filledCount++;
                    }
                });

                if( filledCount !== item.find('.ays-select-field .ays-select-field-value').length ) {
                    empty_inputs++;
                    break;
                }else{
                    continue;
                }
            }

            if ( item.data('type') == 'date' ) {
                if( item.find('input[type="date"]').val() == '' ){
                    empty_inputs++;
                    break;
                }else{
                    continue;
                }
            }
        }

        if (empty_inputs !== 0) {
            return false;
        }else{
            return true;
        }
    }
    return true;
}

function ays_quiz_is_question_min_count( requiredQuestions, isAllow ){
     animating = false;

     if ( requiredQuestions.length !== 0) {
         var empty_inputs = 0;
         var errorMessage;
         requiredQuestions.removeClass('ays-has-error');
         for (var i = 0; i < requiredQuestions.length; i++) {
             var item = requiredQuestions.eq(i);

             var errorFlag = false;
             if ( item.data('type') == 'checkbox' ) {

                 if( isAllow ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';
                     item.addClass('ays-has-error');
                     item.find('.ays-quiz-question-validation-error').html(errorMessage);
                     goToTop( item );
                     empty_inputs++;
                     break;
                 }else{
                     continue;
                 }
             }
         }

         if (empty_inputs !== 0) {
             requiredQuestions.parents('.ays-quiz-container').addClass('ays-quiz-has-error');
             return false;
         }else{
             requiredQuestions.parents('.ays-quiz-container').removeClass('ays-quiz-has-error');
             return true;
         }
     }
     return true;
}

function aysCheckMinimumCountCheckbox( question, myQuizOptions ){

    var questionId = question.attr('data-question-id');
    questionId = parseInt( questionId );
    if( question.find('.ays-quiz-answers').hasClass('enable_min_selection_number') ){
        var checkedCount = question.find('.ays-field input[type="checkbox"]:checked').length;

        if (questionId !== null && questionId != '' && typeof myQuizOptions[questionId] != 'undefined') {

            // Minimum length of a text field
            var enable_min_selection_number = (myQuizOptions[questionId].enable_min_selection_number && myQuizOptions[questionId].enable_min_selection_number != "") ? myQuizOptions[questionId].enable_min_selection_number : false;

            // Length
            var min_selection_number = (myQuizOptions[questionId].min_selection_number && myQuizOptions[questionId].min_selection_number != "") ? parseInt(myQuizOptions[questionId].min_selection_number) : '';

            if( enable_min_selection_number === true && min_selection_number != '' ){

                if( min_selection_number <= checkedCount ){
                    return true;
                }
            }
        }
    } else {
        return true;
    }
    return false;
}

function goToTop( el ) {
    el.get(0).scrollIntoView({
        block: "center",
        behavior: "smooth"
    });
}

function countdownTimeForShow( parentStep, countDownDate ) {
    var timeForShow = "";
    var waitingTimeBox = parentStep.find('.ays-quiz-question-waiting-time-box');

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

    if (distance <= 1) {
        clearInterval(window.countdownTimeForShowInterval);
        waitingTimeBox.html("");
    } else {
        waitingTimeBox.html(timeForShow);
    }

    return timeForShow;
}

Date.prototype.aysCustomFormat = function(formatString){
    var YYYY,YY,MMMM,MMM,MM,M,DDDD,DDD,DD,D,hhhh,hhh,hh,h,mm,m,ss,s,ampm,AMPM,dMod,th;
    YY = ((YYYY=this.getFullYear())+"").slice(-2);
    MM = (M=this.getMonth()+1)<10?('0'+M):M;
    MMM = (MMMM=["January","February","March","April","May","June","July","August","September","October","November","December"][M-1]).substring(0,3);
    DD = (D=this.getDate())<10?('0'+D):D;
    DDD = (DDDD=["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"][this.getDay()]).substring(0,3);
    th=(D>=10&&D<=20)?'th':((dMod=D%10)==1)?'st':(dMod==2)?'nd':(dMod==3)?'rd':'th';
    formatString = formatString.replace("#YYYY#",YYYY).replace("#YY#",YY).replace("#MMMM#",MMMM).replace("#MMM#",MMM).replace("#MM#",MM).replace("#M#",M).replace("#DDDD#",DDDD).replace("#DDD#",DDD).replace("#DD#",DD).replace("#D#",D).replace("#th#",th);
    h=(hhh=this.getHours());
    if (h==0) h=24;
    if (h>12) h-=12;
    hh = h<10?('0'+h):h;
    hhhh = hhh<10?('0'+hhh):hhh;
    AMPM=(ampm=hhh<12?'am':'pm').toUpperCase();
    mm=(m=this.getMinutes())<10?('0'+m):m;
    ss=(s=this.getSeconds())<10?('0'+s):s;

    return formatString.replace("#hhhh#",hhhh).replace("#hhh#",hhh).replace("#hh#",hh).replace("#h#",h).replace("#mm#",mm).replace("#m#",m).replace("#ss#",ss).replace("#s#",s).replace("#ampm#",ampm).replace("#AMPM#",AMPM);
    // token:     description:             example:
    // #YYYY#     4-digit year             1999
    // #YY#       2-digit year             99
    // #MMMM#     full month name          February
    // #MMM#      3-letter month name      Feb
    // #MM#       2-digit month number     02
    // #M#        month number             2
    // #DDDD#     full weekday name        Wednesday
    // #DDD#      3-letter weekday name    Wed
    // #DD#       2-digit day number       09
    // #D#        day number               9
    // #th#       day ordinal suffix       nd
    // #hhhh#     2-digit 24-based hour    17
    // #hhh#      military/24-based hour   17
    // #hh#       2-digit hour             05
    // #h#        hour                     5
    // #mm#       2-digit minute           07
    // #m#        minute                   7
    // #ss#       2-digit second           09
    // #s#        second                   9
    // #ampm#     "am" or "pm"             pm
    // #AMPM#     "AM" or "PM"             PM
};

function aysResizeiFrame(){
    window.parent.postMessage({
        sentinel: "amp",
        type: "embed-size",
        height: document.body.scrollHeight + 40,
    }, "*");

    if( jQuery(window.parent.document.body).hasClass('amp-mode-mouse') ){
        jQuery(document.body).css('background-color', '#fff');
    }
}


function toggleFullscreen(elem) {
    elem = elem || document.documentElement;
    if (!document.fullscreenElement && !document.mozFullScreenElement &&
        !document.webkitFullscreenElement && !document.msFullscreenElement) {
        aysQuizFullScreenActivate( elem );
        if (elem.requestFullscreen) {
            elem.requestFullscreen();
        }else if (elem.msRequestFullscreen) {
            elem.msRequestFullscreen();
        }else if (elem.mozRequestFullScreen) {
            elem.mozRequestFullScreen();
        }else if (elem.webkitRequestFullscreen) {
            elem.webkitRequestFullscreen(Element.ALLOW_KEYBOARD_INPUT);
        }
    }else{
        aysQuizFullScreenDeactivate( elem );
        if(document.exitFullscreen) {
            document.exitFullscreen();
        }else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        }
    }
}

function aysQuizFullScreenActivate( elem ){
    jQuery(elem).find('.ays-quiz-full-screen-container > .ays-quiz-close-full-screen').css({'display':'block'});
    jQuery(elem).find('.ays-quiz-full-screen-container > .ays-quiz-open-full-screen').css('display','none');
    //jQuery(elem).find('.step:not(:first-of-type,.ays_thank_you_fs)').css({'height':'100vh'});
    jQuery(elem).css({'overflow':'auto'});

    if( jQuery(elem).find('.ays_quiz_reports').length > 0 ){
        jQuery(elem).find('.ays_quiz_reports').css({
            'position': 'fixed',
            'z-index': '1',
        });
    }else{
        if( jQuery(elem).find('.ays_quiz_rete_avg').length > 0 ){
            jQuery(elem).find('.ays_quiz_rete_avg').css({
                'position': 'fixed',
                'z-index': '1',
            });
        }

        if( jQuery(elem).find('.ays_quizn_ancnoxneri_qanak').length > 0 ){
            jQuery(elem).find('.ays_quizn_ancnoxneri_qanak').css({
                'position': 'fixed',
                'z-index': '1',
            });
        }
    }
}

function aysQuizFullScreenDeactivate( elem ){
    jQuery(elem).find('.ays-quiz-full-screen-container > svg.ays-quiz-open-full-screen').css({'display':'block'});
    jQuery(elem).find('.ays-quiz-full-screen-container > svg.ays-quiz-close-full-screen').css('display','none');
    //jQuery(elem).find('.step:not(:first-of-type)').css({'height':'auto'});
    jQuery(elem).css({'overflow':'initial'});

    if( jQuery(elem).find('.ays_quiz_reports').length > 0 ){
        jQuery(elem).find('.ays_quiz_reports').css({
            'position': 'absolute',
            'z-index': '1',
        });
    }else{
        if( jQuery(elem).find('.ays_quiz_rete_avg').length > 0 ){
            jQuery(elem).find('.ays_quiz_rete_avg').css({
                'position': 'absolute',
                'z-index': '1',
            });
        }

        if( jQuery(elem).find('.ays_quizn_ancnoxneri_qanak').length > 0 ){
            jQuery(elem).find('.ays_quizn_ancnoxneri_qanak').css({
                'position': 'absolute',
                'z-index': '1',
            });
        }
    }
}

function quizGetVoices() {
    var voices = speechSynthesis.getVoices();
    if(!voices.length){
      var utterance = new SpeechSynthesisUtterance("");
      speechSynthesis.speak(utterance);
      voices = speechSynthesis.getVoices();          
    }
    return voices;
}

function listenQuestionText( text, voice, rate, pitch, volume, action ){
    if(action == 'play'){
        let speakData = new SpeechSynthesisUtterance();
        speakData.volume = volume; // From 0 to 1
        speakData.rate = rate; // From 0.1 to 10
        speakData.pitch = pitch; // From 0 to 2
        speakData.text = text;
        speakData.lang = 'en';
        speakData.voice = voice;
        speechSynthesis.speak(speakData);
    }
    else if (action == 'cancel'){
        speechSynthesis.cancel();
    }
}