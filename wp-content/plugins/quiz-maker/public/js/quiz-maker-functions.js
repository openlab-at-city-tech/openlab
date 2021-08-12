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


function ays_countdown_datetime(sec, showMessage) {
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

        document.getElementById("show_timer_countdown").innerHTML = text;

        // If the count down is over, write some text
        if (distance > 0) {
            distance -= 1000;
        }
        if (distance <= 0) {
            clearInterval(x_int);
            if(showMessage){
                document.getElementById("show_timer_countdown").innerHTML = quizLangObj.expiredMessage;
            }else{
                document.getElementById("show_timer_countdown").innerHTML = '';
            }
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
         var errorMessage;
         requiredQuestions.removeClass('ays-has-error');
         for (var i = 0; i < requiredQuestions.length; i++) {
             var item = requiredQuestions.eq(i);
             if( item.data('type') == 'text' || item.data('type') == 'short_text' || item.data('type') == 'number' ){
                 if( item.find( '.ays-text-field .ays-text-input' ).val() == '' ){
                     errorMessage = '<img src="' + quiz_maker_ajax_public.warningIcon + '" alt="error">';
                     errorMessage += '<span>' + quizLangObj.requiredError + '</span>';

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

function goToTop( el ) {
    el.get(0).scrollIntoView({
        block: "center",
        behavior: "smooth"
    });
}
