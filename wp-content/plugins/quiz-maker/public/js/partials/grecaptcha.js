(function($) {
    'use strict';

    function AysQuizRecaptchaPlugin(element, options){
        this.el = element;
        this.$el = $(element);
        this.htmlClassPrefix = 'ays-quiz-';
        this.current_fs;
        this.next_fs;
        this.quizObject = undefined;
        this.finishButton = undefined;
        this.dbOptions = undefined;

        this.init();

        return this;
    }

    AysQuizRecaptchaPlugin.prototype.init = function() {
        var _this = this;

        var uniqueKey = _this.$el.find('.'+ _this.htmlClassPrefix + 'g-recaptcha').data('uniqueKey');

        if( typeof window.aysQuizRecaptchaObj != 'undefined' ){
            if(typeof window.aysQuizRecaptchaObj[ uniqueKey ] != 'undefined' ){
                _this.dbOptions = JSON.parse( window.atob( window.aysQuizRecaptchaObj[ uniqueKey ] ) );
            }
        }

        _this.setEvents();
    };

    AysQuizRecaptchaPlugin.prototype.setEvents = function(e){
        var _this = this;


        _this.$el.find('form.' + _this.htmlClassPrefix + 'form').on('afterQuizSubmission', function(e){
            _this.quizObject = e.detail._this;
            _this.finishButton = e.detail.thisButton;
            _this.current_fs = $(e.target).parents('.step');
            _this.next_fs = $(e.target).parents('.step').next();

            var form = _this.$el.find('form');

            var quizRecaptcha = _this.dbOptions.eable_recaptcha && _this.dbOptions.enable_recaptcha == "on" ? true : false;

            var formCaptchaValidation = false;
            if( quizRecaptcha ){
                formCaptchaValidation = form.attr('data-recaptcha-validate') && form.attr('data-recaptcha-validate') == 'true' ? true : false;
            }

            if( formCaptchaValidation === false ) {
                _this.initRecaptcha($(this));
            }else{
                e.preventDefault();
            }

            _this.activeStep(form.find('.' + _this.htmlClassPrefix + 'step.active-step .ays_finish'), 'next', null);

            _this.aysAnimateStep('fade', _this.current_fs, _this.next_fs);

            _this.goTo();
        });
    }

    AysQuizRecaptchaPlugin.prototype.initRecaptcha = function( step ) {
        var _this = this;

        var captchaContainer = step.find( '.' + _this.htmlClassPrefix + 'recaptcha-wrap' ),
            captcha          = step.find( '.' + _this.htmlClassPrefix + 'g-recaptcha' );
            var captchaSiteKey = _this.dbOptions.siteKey === '' ? null : _this.dbOptions.siteKey,
            captchaID      = _this.htmlClassPrefix + 'recaptcha-' + Date.now(),
            apiVar         = grecaptcha,
            theme          = _this.dbOptions.theme === '' ? null : _this.dbOptions.theme;
            
        captcha.attr('id', captchaID);
        if( captchaSiteKey ) {
            try {
                var options = {
                    'sitekey': captchaSiteKey,
                    'expired-callback': function () {
                        _this.setRecaptchaChecked(false);
                        apiVar.reset(opt_widget_id);
                    },
                    'callback': function (response) {
                        if (!response) {
                            _this.recaptchaErrorCallback($('#' + captchaID));
                        } else {
                            _this.recaptchaSuccessCallback($('#' + captchaID));
                        }
                    },
                    // 'error-callback': function () {
                    //     _this.setRecaptchaChecked(false);
                    //     apiVar.reset(opt_widget_id);
                    // }
                };

                if( theme ) {
                    options.theme = theme;
                }

                var opt_widget_id = apiVar.render( captchaID, options );
                captcha.attr('data-widget-id', opt_widget_id);
            } catch (error) {}
            _this.quizDispatchEvent(document, "AysQuizRecaptchaLoaded", true);
        }
    }

    AysQuizRecaptchaPlugin.prototype.recaptchaErrorCallback = function (el) {
        var _this = this;
        _this.setRecaptchaChecked( false );
        _this.quizDispatchEvent(document, "AysQuizRecaptchaError", true);
        var err = el.parents('.step').find('.' + _this.htmlClassPrefix + 'g-recaptcha-hidden-error');
        err.show();
        return false;
    };

    AysQuizRecaptchaPlugin.prototype.recaptchaSuccessCallback = function (el) {
        var _this = this;
        _this.setRecaptchaChecked( true );
        _this.quizDispatchEvent(document, "AysQuizRecaptchaSuccess", true);
        var err = el.parents('.step').find('.' + _this.htmlClassPrefix + 'g-recaptcha-hidden-error');
        err.hide();
        setTimeout(function (){
            // el.parents('.step').find('.' + _this.htmlClassPrefix + 'recaptcha-section').remove();
            _this.$el.find('.' + _this.htmlClassPrefix + 'recaptcha-section').remove();
            _this.finishButton.trigger('click');
        }, 500);
    };

    AysQuizRecaptchaPlugin.prototype.setRecaptchaChecked = function ( isChceked ) {
        var _this = this;
        var form = _this.$el.find('form');
        form.attr('data-recaptcha-validate', isChceked ? 'true' : 'false');
    }

    AysQuizRecaptchaPlugin.prototype.quizDispatchEvent = function (el, ev, custom) {
        var e = document.createEvent(custom ? "CustomEvent" : "HTMLEvents");
        custom ? e.initCustomEvent(ev, true, true, false) : e.initEvent(ev, true, true);
        el.dispatchEvent(e);
    };

    AysQuizRecaptchaPlugin.prototype.activeStep = function(button, action, where) {
        var _this = this;
        _this.current_fs = button.parents('.step');
        if(action == 'next'){
            if( where !== null ){
                _this.next_fs = _this.$el.find( '.step[data-id="'+ where +'"]' );
            }else{
                _this.next_fs = button.parents('.step').next();
            }
        }
        if(action == 'prev'){
            if( where !== null ){
                _this.next_fs = _this.$el.find( '.step[data-id="'+ where +'"]' );
            }else{
                _this.next_fs = button.parents('.step').prev();
            }
        }
        _this.current_fs.removeClass('active-step');
        _this.next_fs.addClass('active-step');
    }

    AysQuizRecaptchaPlugin.prototype.aysAnimateStep = function(animation, current_fs, next_fs, duration){
        var _this = this;
        if(typeof duration == "undefined"){
            duration = 500;
        }

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
                        _this.animating = false;
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
                        _this.animating = false;
                    },1000);
                break;
                case "shake":
                    current_fs.animate({opacity: 0}, {
                        step: function (now, mx) {
                            _this.scale = 1 - (1 - now) * 0.2;
                            _this.left = (now * 50) + "%";
                            _this.opacity = 1 - now;
                            current_fs.css({
                                'transform': 'scale(' + _this.scale + ')',
                                'position': 'absolute',
                                'top':0,
                                'opacity': 1,
                                'pointer-events': 'none'
                            });
                            next_fs.css({
                                'left': _this.left,
                                'opacity': _this.opacity,
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
                            _this.animating = false;
                        },
                        easing: 'easeInOutBack'
                    });
                break;
                case "fade":
                    current_fs.animate({opacity: 0}, {
                        step: function (now, mx) {
                            _this.opacity = 1 - now;
                            current_fs.css({
                                'position': 'absolute',
                                'width': '100%',
                                'pointer-events': 'none'
                            });
                            next_fs.css({
                                'opacity': _this.opacity,
                                'position':'relative',
                                'display':'block',
                                'pointer-events': 'none'
                            });
                        },
                        duration: duration,
                        complete: function () {
                            current_fs.hide();
                            current_fs.css({
                                'pointer-events': 'auto',
                                'position': 'static'
                            });
                            next_fs.css({
                                'display':'block',
                                'position':'relative',
                                'opacity': 1,
                                'pointer-events': 'auto'
                            });
                            _this.animating = false;
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
                                'display':'block',
                                'position':'relative',
                                'transform':'scale(1)',
                                'pointer-events': 'auto'
                            });
                            _this.animating = false;
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
                        _this.animating = false;
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
                        _this.animating = false;
                    },1000);
                case "shake":
                    current_fs.animate({opacity: 0}, {
                        step: function (now, mx) {
                            _this.scale = 1 - (1 - now) * 0.2;
                            _this.left = (now * 50) + "%";
                            _this.opacity = 1 - now;
                            current_fs.css({
                                'transform': 'scale(' + _this.scale + ')',
                            });
                        },
                        duration: 800,
                        complete: function () {
                            current_fs.hide();
                            _this.animating = false;
                        },
                        easing: 'easeInOutBack'
                    });
                break;
                case "fade":
                    current_fs.animate({opacity: 0}, {
                        step: function (now, mx) {
                            _this.opacity = 1 - now;
                        },
                        duration: 500,
                        complete: function () {
                            current_fs.hide();
                            _this.animating = false;
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
                            _this.animating = false;
                        }
                    });
                break;
            }
        }
    }

    AysQuizRecaptchaPlugin.prototype.goTo = function() {
        var quizAnimationTop;
        
        if(this.dbOptions != 'undefined'){
            quizAnimationTop = (this.dbOptions.quiz_animation_top && this.dbOptions.quiz_animation_top != 0) ? parseInt(this.dbOptions.quiz_animation_top) : 200;
        }else{
            quizAnimationTop = 200;
        }

        if(this.dbOptions.quiz_enable_animation_top){
            $('html, body').animate({
                scrollTop: this.$el.offset().top - quizAnimationTop + 'px'
            }, 'fast');
        }

        return this; // for chaining...
    }

    $.fn.AysQuizRecaptcha = function(options) {
        return this.each(function() {
            if (!$.data(this, 'AysQuizRecaptcha')) {
                $.data(this, 'AysQuizRecaptcha', new AysQuizRecaptchaPlugin(this, options));
            } else {
                try {
                    $(this).data('AysQuizRecaptcha').init();
                } catch (err) {
                    console.error('AysQuizRecaptcha has not initiated properly');
                }
            }
        });
    };

    $(document).find('.ays-quiz-container').AysQuizRecaptcha();
})(jQuery);
