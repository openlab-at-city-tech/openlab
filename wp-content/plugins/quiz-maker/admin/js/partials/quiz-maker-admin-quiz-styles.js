(function($){
    'use strict';
    $(document).ready(function(){
        
        $(document).find('.nav-answers-tab-wrapper a.nav-tab').on('click', function (e) {
            let elemenetID = $(this).attr('href');
            $(document).find('.nav-answers-tab-wrapper a.nav-tab').each(function () {
                if ($(this).hasClass('nav-tab-active')) {
                    $(this).removeClass('nav-tab-active');
                }
            });
            $(this).addClass('nav-tab-active');
            $(document).find('.ays-quiz-live-container-answers .step').each(function () {
                if ($(this).hasClass('active-step'))
                    $(this).removeClass('active-step');
            });
            $('.ays-quiz-live-container-answers .step' + elemenetID).addClass('active-step');
            e.preventDefault();
        });

        $(document).on('change', '.ays_toggle', function (e) {
            let state = $(this).prop('checked');
            if($(this).hasClass('ays_toggle_slide')){
                switch (state) {
                    case true:
                        $(this).parents().eq(1).find('.ays_toggle_target').slideDown(250);
                        break;
                    case false:
                        $(this).parents().eq(1).find('.ays_toggle_target').slideUp(250);
                        break;
                }
            }else{
                switch (state) {
                    case true:
                        $(this).parents().eq(1).find('.ays_toggle_target').show(250);
                        break;
                    case false:
                        $(this).parents().eq(1).find('.ays_toggle_target').hide(250);
                        break;
                }
            }
        });
        
        $(document).find('#ays_quiz_gradient_direction').on('change', function () {
            toggleBackgrounGradient();
        });
        
        let defaultColors = {
            classicLight: {
                quizColor: "#27ae60",
                bgColor: "#ffffff",
                textColor: "#515151",
            },
            classicDark: {
                quizColor: "#0d62bc",
                bgColor: "#000000",
                textColor: "#e25600",
            },
            elegantLight: {
                quizColor: "#ffffff",
                bgColor: "#ffffff",
                textColor: "#2c2c2c",
            },
            elegantDark: {
                quizColor: "#2c2c2c",
                bgColor: "#2c2c2c",
                textColor: "#ffffff",
            },
            rectLight: {
                quizColor: "#fff195",
                bgColor: "#ffffff",
                textColor: "#515151",
            },
            rectDark: {
                quizColor: "#1e73be",
                bgColor: "#2c2c2c",
                textColor: "#ffffff",
            },
            modernLight: {
                quizColor: "#e74c3c",
                bgColor: "#ffffff",
                textColor: "#000000",
            },
            modernDark: {
                quizColor: "#33c465",
                bgColor: "#ffffff",
                textColor: "#000000",
            }
        };
        
        
        let defaultTextColor, defaultBgColor, defaultQuizColor;
        switch ($(document).find('input[name="ays_quiz_theme"]:checked').val()) {
            case 'elegant_dark':
                defaultQuizColor = defaultColors.elegantDark.quizColor;
                defaultBgColor = defaultColors.elegantDark.bgColor;
                defaultTextColor = defaultColors.elegantDark.textColor;
                break;
            case 'elegant_light':
                defaultQuizColor = defaultColors.elegantLight.quizColor;
                defaultBgColor = defaultColors.elegantLight.bgColor;
                defaultTextColor = defaultColors.elegantLight.textColor;
                break;
            case 'rect_light':
                defaultQuizColor = defaultColors.rectLight.quizColor;
                defaultBgColor = defaultColors.rectLight.bgColor;
                defaultTextColor = defaultColors.rectLight.textColor;
                break;
            case 'rect_dark':
                defaultQuizColor = defaultColors.rectDark.quizColor;
                defaultBgColor = defaultColors.rectDark.bgColor;
                defaultTextColor = defaultColors.rectDark.textColor;
                break;
            case 'classic_dark':
                defaultQuizColor = defaultColors.classicDark.quizColor;
                defaultBgColor = defaultColors.classicDark.bgColor;
                defaultTextColor = defaultColors.classicDark.textColor;
                break;
            case 'classic_light':
                defaultQuizColor = defaultColors.classicLight.quizColor;
                defaultBgColor = defaultColors.classicLight.bgColor;
                defaultTextColor = defaultColors.classicLight.textColor;
                break;
            case 'modern_dark':
                defaultQuizColor = defaultColors.modernDark.quizColor;
                defaultBgColor = defaultColors.modernDark.bgColor;
                defaultTextColor = defaultColors.modernDark.textColor;
                break;
            case 'modern_light':
                defaultQuizColor = defaultColors.modernLight.quizColor;
                defaultBgColor = defaultColors.modernLight.bgColor;
                defaultTextColor = defaultColors.modernLight.textColor;
                break;
            default:
                defaultQuizColor = defaultColors.classicLight.quizColor;
                defaultBgColor = defaultColors.classicLight.bgColor;
                defaultTextColor = defaultColors.classicLight.textColor;
                break;
        }
        

        let ays_quiz_bg_color_picker = {
            defaultColor: defaultBgColor,
            change: function (e) {
                setTimeout(function () {
                    $(document).find('.ays-quiz-live-container').css({'background-color': e.target.value});
                    $(document).find('.ays-progress-value.fourth').css({
                        'color': e.target.value,
                    });
                }, 1);
            }
        };
        let ays_quiz_text_color_picker = {
            defaultColor: defaultTextColor,
            change: function (e) {
                setTimeout(function () {
                    $(document).find('.ays-quiz-live-title').css({'color': e.target.value});
                    $(document).find('.ays-quiz-live-subtitle').css({'color': e.target.value});
                    $(document).find('.ays-quiz-live-button').css({'color': e.target.value});
                    $(document).find('.ays-progress-value.first, .ays-progress-value.second').css({
                        'color': e.target.value,
                    });
                    $(document).find('.ays-progress-value.third').css({
                        'color': e.target.value,
                    });
                    $(document).find('.ays-progress.first, .ays-progress.second').css({
                        'background': e.target.value,
                    });
                    $(document).find('.ays-progress-bg.third, .ays-progress-bg.fourth').css({
                        'background': e.target.value,
                    });
                }, 1);
            }
        };
        let ays_quiz_color_picker = {
            defaultColor: defaultQuizColor,
            change: function (e) {
                setTimeout(function () {
                    $(document).find('.ays-quiz-live-button').css({'background': e.target.value});
                    $(document).find('.ays-progress-bar.first, .ays-progress-bar.second').css({
                        'background-color': e.target.value,
                    });
                    $(document).find('.ays-progress-bar.third, .ays-progress-bar.fourth').css({
                        'background-color': e.target.value,
                    });
                }, 1);
            }
        };
        let ays_quiz_box_shadow_color_picker = {
            change: function (e) {
                setTimeout(function () {
                    $(document).find('.ays-quiz-live-container').css({'box-shadow': '0 0 15px 1px ' + e.target.value});
                }, 1);
            }
        };
        let ays_quiz_border_color_picker = {
            change: function (e) {
                setTimeout(function () {
                    $(document).find('.ays-quiz-live-container').css({'border-color': e.target.value});
                }, 1);
            }
        };        
        let ays_quiz_box_gradient_color1_picker = {
            change: function (e) {
                setTimeout(function () {
                    toggleBackgrounGradient();
                }, 1);
            }
        };
        let ays_quiz_box_gradient_color2_picker = {
            change: function (e) {
                setTimeout(function () {
                    toggleBackgrounGradient();
                }, 1);
            }
        };
        
        let ays_ind_leaderboard_color_picker = {
            defaultColor: '#99BB5A',
            change: function (e) {
            }
        };
        let ays_glob_leaderboard_color_picker = {
            defaultColor: '#99BB5A',
            change: function (e) {
                
            }
        };
        let ays_answers_border_color = {
            change: function (e) {
                refreshLivePreview();
            }
        };
        let ays_answers_box_shadow_color = {
            change: function (e) {
                refreshLivePreview();
            }
        };

        
        // Initialize color pickers
        $(document).find('#ays-quiz-bg-color').wpColorPicker(ays_quiz_bg_color_picker);
        $(document).find('#ays-quiz-text-color').wpColorPicker(ays_quiz_text_color_picker);
        $(document).find('#ays-quiz-color').wpColorPicker(ays_quiz_color_picker);
        $(document).find('#ays-quiz-box-shadow-color').wpColorPicker(ays_quiz_box_shadow_color_picker);
        $(document).find('#ays_quiz_border_color').wpColorPicker(ays_quiz_border_color_picker);
        $(document).find('#ays-background-gradient-color-1').wpColorPicker(ays_quiz_box_gradient_color1_picker);
        $(document).find('#ays-background-gradient-color-2').wpColorPicker(ays_quiz_box_gradient_color2_picker);
        
        $(document).find('#ays_leadboard_color').wpColorPicker(ays_ind_leaderboard_color_picker);
        $(document).find('#ays_gleadboard_color').wpColorPicker(ays_glob_leaderboard_color_picker);
        
        $(document).find('#ays_answers_border_color').wpColorPicker(ays_answers_border_color);
        $(document).find('#ays_answers_box_shadow_color').wpColorPicker(ays_answers_box_shadow_color);

        toggleBackgrounGradient();
        $(document).find('input#ays-enable-background-gradient').on('change', function () {
            toggleBackgrounGradient();
        });
        
        // Quiz live preview
        // Theme select
        $(document).find('input[name="ays_quiz_theme"]').on('change', function () {
            let theme_value = $(this).val();
            let bg_image_url = '';
            let defaultTextColor, defaultBgColor, defaultQuizColor;
            switch (theme_value) {
                case 'elegant_dark':
                    quiz_themes_live_preview('#2C2C2C', '#2C2C2C', '#ffffff');
                    $(document).find('#ays-quiz-live-button').css({'border': '1px solid'});
                    $(document).find('#answers_view_select').css('display','');
                    defaultQuizColor = defaultColors.elegantDark.quizColor;
                    defaultBgColor = defaultColors.elegantDark.bgColor;
                    defaultTextColor = defaultColors.elegantDark.textColor;
                    break;
                case 'elegant_light':
                    quiz_themes_live_preview('#ffffff', '#ffffff', '#2C2C2C');
                    $(document).find('#ays-quiz-live-button').css({'border': '1px solid'});
                    $(document).find('#answers_view_select').css('display','');
                    defaultQuizColor = defaultColors.elegantLight.quizColor;
                    defaultBgColor = defaultColors.elegantLight.bgColor;
                    defaultTextColor = defaultColors.elegantLight.textColor;
                    break;
                case 'rect_light':
                    quiz_themes_live_preview('#fff195', '#fff', '#515151');
                    $(document).find('#ays-quiz-live-button').css({'border': '1px solid'});
                    $(document).find('#answers_view_select').css('display','');
                    defaultQuizColor = defaultColors.rectLight.quizColor;
                    defaultBgColor = defaultColors.rectLight.bgColor;
                    defaultTextColor = defaultColors.rectLight.textColor;
                    break;
                case 'rect_dark':
                    quiz_themes_live_preview('#1e73be', '#2c2c2c', '#ffffff');
                    $(document).find('#ays-quiz-live-button').css({'border': '1px solid'});
                    $(document).find('#answers_view_select').css('display','');
                    defaultQuizColor = defaultColors.rectDark.quizColor;
                    defaultBgColor = defaultColors.rectDark.bgColor;
                    defaultTextColor = defaultColors.rectDark.textColor;
                    break;
                case 'classic_dark':
                    quiz_themes_live_preview('#0d62bc', '#000', '#e25600');
                    $(document).find('#ays-quiz-live-button').css({'border': 'none'});
                    $(document).find('#answers_view_select').css('display','none');
                    defaultQuizColor = defaultColors.classicDark.quizColor;
                    defaultBgColor = defaultColors.classicDark.bgColor;
                    defaultTextColor = defaultColors.classicDark.textColor;
                    break;
                case 'classic_light':
                    quiz_themes_live_preview('#27AE60', '#fff', '#515151');
                    $(document).find('#ays-quiz-live-button').css({'border': 'none'});
                    $(document).find('#answers_view_select').css('display','none');
                    defaultQuizColor = defaultColors.classicLight.quizColor;
                    defaultBgColor = defaultColors.classicLight.bgColor;
                    defaultTextColor = defaultColors.classicLight.textColor;
                    break;
                case 'modern_light':
                    if($(document).find('#ays-quiz-image').val() !== ''){
                        bg_image_url = $(document).find('#ays-quiz-image').val();
                    }
                    $(document).find('.ays-quiz-live-image').css('display','none');
                    quiz_themes_live_preview('#e74c3c', '#fff', '#000');
                    $(document).find('#ays-quiz-live-button').css({'border': 'none'});
                    $(document).find('#answers_view_select').css('display','');
                    $(document).find('.ays-quiz-live-container').css({'background-image':'url('+bg_image_url+')'});
                    defaultQuizColor = defaultColors.modernLight.quizColor;
                    defaultBgColor = defaultColors.modernLight.bgColor;
                    defaultTextColor = defaultColors.modernLight.textColor;
                    break;
                case 'modern_dark':
                    if($(document).find('#ays-quiz-image').val() !== ''){
                        bg_image_url = $(document).find('#ays-quiz-image').val();
                    }
                    $(document).find('.ays-quiz-live-image').css('display','none');
                    quiz_themes_live_preview('#33c465', '#fff', '#000');
                    $(document).find('#ays-quiz-live-button').css({'border': 'none'});
                    $(document).find('#answers_view_select').css('display','');
                    $(document).find('.ays-quiz-live-container').css({'background-image':'url('+bg_image_url+')'});

                    defaultQuizColor = defaultColors.modernDark.quizColor;
                    defaultBgColor = defaultColors.modernDark.bgColor;
                    defaultTextColor = defaultColors.modernDark.textColor;
                    break;
                default:
                    quiz_themes_live_preview('#27AE60', '#fff', '#515151');
                    $(document).find('#ays-quiz-live-button').css({'border': 'none'});
                    $(document).find('#answers_view_select').css('display','none');
                    defaultQuizColor = defaultColors.classicLight.quizColor;
                    defaultBgColor = defaultColors.classicLight.bgColor;
                    defaultTextColor = defaultColors.classicLight.textColor;
                    break;
            }

            
            let ays_quiz_bg_color_picker = {
                defaultColor: defaultBgColor,
                change: function (e) {
                    setTimeout(function () {
                        $(document).find('.ays-quiz-live-container').css({'background-color': e.target.value});
                        $(document).find('.ays-progress-value.fourth').css({
                            'color': e.target.value,
                        });
                    }, 1);
                }
            };
            let ays_quiz_text_color_picker = {
                defaultColor: defaultTextColor,
                change: function (e) {
                    setTimeout(function () {
                        $(document).find('.ays-quiz-live-title').css({'color': e.target.value});
                        $(document).find('.ays-quiz-live-subtitle').css({'color': e.target.value});
                        $(document).find('.ays-quiz-live-button').css({'color': e.target.value});
                        $(document).find('.ays-progress-value.first, .ays-progress-value.second').css({
                            'color': e.target.value,
                        });
                        $(document).find('.ays-progress-value.third').css({
                            'color': e.target.value,
                        });
                        $(document).find('.ays-progress.first, .ays-progress.second').css({
                            'background': e.target.value,
                        });
                        $(document).find('.ays-progress-bg.third, .ays-progress-bg.fourth').css({
                            'background': e.target.value,
                        });
                    }, 1);
                }
            };
            let ays_quiz_color_picker = {
                defaultColor: defaultQuizColor,
                change: function (e) {
                    setTimeout(function () {
                        $(document).find('.ays-quiz-live-button').css({'background': e.target.value});
                        $(document).find('.ays-progress-bar.first, .ays-progress-bar.second').css({
                            'background-color': e.target.value,
                        });
                        $(document).find('.ays-progress-bar.third, .ays-progress-bar.fourth').css({
                            'background-color': e.target.value,
                        });
                    }, 1);
                }
            };
            $(document).find('#ays-quiz-bg-color').wpColorPicker(ays_quiz_bg_color_picker);
            $(document).find('#ays-quiz-text-color').wpColorPicker(ays_quiz_text_color_picker);
            $(document).find('#ays-quiz-color').wpColorPicker(ays_quiz_color_picker);
        });
        setTimeout(function(){
            $(document).find('.ays-quiz-live-title').text($(document).find('#ays-quiz-title').val());
            let emptySubtitle;
            if ($(document).find("#wp-ays-quiz-description-wrap").hasClass("tmce-active")){
                $(document).find("#wp-ays-quiz-description-wrap").addClass("html-active").removeClass("tmce-active");
                emptySubtitle = $(document).find('#ays-quiz-description').val();
                emptySubtitle = window.tinyMCE.get('ays-quiz-description').getContent();
                $(document).find("#wp-ays-quiz-description-wrap").addClass("tmce-active").removeClass("html-active");
            }else if($(document).find("#wp-ays-quiz-description-wrap").hasClass("html-active")){
                emptySubtitle = $(document).find('#ays-quiz-description').val();
            }else{
                emptySubtitle = $(document).find('#ays-quiz-description').val();
            }
            $(document).find('.ays-quiz-live-subtitle').html(emptySubtitle);
        },1000);
        
        $(document).find('.ays-quiz-live-button').css({
            'background': $(document).find('#ays-quiz-color').val(),
            'color': $(document).find('#ays-quiz-text-color').val()
        });
        $(document).find('.ays-quiz-live-title, .ays-quiz-live-subtitle').css({
            'color': $(document).find('#ays-quiz-text-color').val()
        });
        $(document).find('.ays-progress-value.first, .ays-progress-value.second').css({
            'color': $(document).find('#ays-quiz-text-color').val(),
        });
        $(document).find('.ays-progress-value.third').css({
            'color': $(document).find('#ays-quiz-text-color').val(),
        });
        $(document).find('.ays-progress-value.fourth').css({
            'color': $(document).find('#ays-quiz-bg-color').val(),
        });
        $(document).find('.ays-progress.first, .ays-progress.second').css({
            'background': $(document).find('#ays-quiz-text-color').val(),
        });
        $(document).find('.ays-progress-bg.third, .ays-progress-bg.fourth').css({
            'background': $(document).find('#ays-quiz-text-color').val(),
        });
        
        $(document).find('.ays-progress-bar.first, .ays-progress-bar.second').css({
            'background': $(document).find('#ays-quiz-color').val(),
        });
        $(document).find('.ays-progress-bar.third, .ays-progress-bar.fourth').css({
            'background': $(document).find('#ays-quiz-color').val(),
        });


        let live_width = ($(document).find('#ays-quiz-width').val() !== 0) ? $(document).find('#ays-quiz-width').val() + 'px' : '100%';
        $(document).find('.ays-quiz-live-container').css({
            'min-height': $(document).find('#ays-quiz-height').val() + 'px',
            'width': live_width,
            'background-color': $(document).find('#ays-quiz-bg-color').val(),
            'border-radius': $(document).find('#ays_quiz_border_radius').val() + 'px'
        });
        
//        $(document).find('.ays-quiz-live-image').attr('src', $(document).find('#ays-quiz-img').attr('src'));

        $(document).find('#ays-quiz-title').on('change', function () {
            $(document).find('.ays-quiz-live-title').text($(document).find('#ays-quiz-title').val());
        });
        $(document).find('a[href="#tab2"]').on('click', function () {
            let emptySubtitle;
            if ($(document).find("#wp-ays-quiz-description-wrap").hasClass("tmce-active")){
                $(document).find("#wp-ays-quiz-description-wrap").addClass("html-active").removeClass("tmce-active");
                emptySubtitle = $(document).find('#ays-quiz-description').val();
                emptySubtitle = window.tinyMCE.get('ays-quiz-description').getContent();
                $(document).find("#wp-ays-quiz-description-wrap").addClass("tmce-active").removeClass("html-active");
            }else{
                emptySubtitle = $(document).find('#ays-quiz-description').val();
            }
            $(document).find('.ays-quiz-live-subtitle').html(emptySubtitle);
        });
        $(document).find('#ays-quiz-height').on('change', function (e) {
            $(document).find('.ays-quiz-live-container').css({'min-height': e.target.value + 'px'});
        });
        $(document).find('#ays-quiz-width').on('change', function (e) {
            $(document).find('.ays-quiz-live-container').css({'width': e.target.value + 'px'});
        });
        
        $(document).find('.ays-quiz-live-title, .ays-quiz-live-subtitle').css({'color': $(document).find('#ays-quiz-text-color').val()});
        
        if ($(document).find('#ays-quiz-img').attr('src')) {
            let ays_quiz_theme = $(document).find('input[name="ays_quiz_theme"]:checked').val();
            let quiz_image_src = $(document).find('#ays-quiz-img').attr('src');
            switch (ays_quiz_theme) {
                case 'elegant_dark':
                case 'elegant_light':
                case 'rect_light':
                case 'rect_dark':
                case 'classic_dark':
                case 'classic_light':
                    $(document).find('.ays-quiz-live-image').attr('src', quiz_image_src);
                    $(document).find('.ays-quiz-live-image').css({'display': 'block'});
                    break;
                case 'modern_light':
                case 'modern_dark':
                    let bg_image_url = '';
                    if($(document).find('#ays-quiz-img').length !== 0){
                        bg_image_url = $(document).find('#ays-quiz-img').attr('src');
                    }
                    $(document).find('.ays-quiz-live-container').css({'background-image':'url('+bg_image_url+')'});
                    $(document).find('.ays-quiz-live-image').css({'display': 'none'});
                    $(document).find('#ays-quiz-live-button').css('border','1px solid');
                    break;
            }
        } else {
            $(document).find('.ays-quiz-live-image').css({'display': 'none'});
        }
        
        
        if($(document).find('input#ays_quiz_bg_image').val() != ''){
            $(document).find('.ays-quiz-live-container').css({'background-image': 'url("'+$(document).find('input#ays_quiz_bg_image').val()+'")'});
        }
        
        if($(document).find('#ays_enable_box_shadow').attr('checked') == 'checked'){
            $(document).find('.ays-quiz-live-container').css({'box-shadow': '0 0 15px 1px ' + $(document).find('#ays-quiz-box-shadow-color').val()});
        }else{
            $(document).find('.ays-quiz-live-container').css({'box-shadow': 'none'});
        }
        $(document).find('#ays_enable_box_shadow').on('change', function () {
            if($(this).attr('checked') == 'checked'){
                $(document).find('.ays-quiz-live-container').css({'box-shadow': '0 0 15px 1px' + $(document).find('#ays-quiz-box-shadow-color').val()});
            }else{
                $(document).find('.ays-quiz-live-container').css({'box-shadow': 'none'});
            }
        });
        
        if($(document).find('#ays_enable_border').attr('checked') == 'checked'){
            $(document).find('.ays-quiz-live-container').css({
                'border': $(document).find('#ays_quiz_border_width').val() + 'px ' + $(document).find('#ays_quiz_border_style').val() + ' ' + $(document).find('#ays_quiz_border_color').val()
            });
        }else{
            $(document).find('.ays-quiz-live-container').css({'border': 'none'});
        }
        $(document).find('#ays_enable_border').on('change', function () {
            if($(this).attr('checked') == 'checked'){
                $(document).find('.ays-quiz-live-container').css({
                    'border': $(document).find('#ays_quiz_border_width').val() + 'px ' + $(document).find('#ays_quiz_border_style').val() + ' ' + $(document).find('#ays_quiz_border_color').val()
                });
            }else{
                $(document).find('.ays-quiz-live-container').css({'border': 'none'});
            }
        });
        
        // Border options
        // Boeder width
        $(document).find('#ays_quiz_border_width').on('change', function () {
            $(document).find('.ays-quiz-live-container').css({
                'border-width': $(document).find('#ays_quiz_border_width').val() + 'px'
            });
        });
        // Boeder style
        $(document).find('#ays_quiz_border_style').on('change', function () {
            $(document).find('.ays-quiz-live-container').css({
                'border-style': $(document).find('#ays_quiz_border_style').val()
            });
        });
        // Boeder radius
        $(document).find('#ays_quiz_border_radius').on('change', function () {
            $(document).find('.ays-quiz-live-container').css({'border-radius': $(this).val() + 'px'});
        });
        
        
        $(document).find('.ays_theme_image_div').on('click',function () {
            var radio_id = $(this).parent().attr('for');
            if($(this).hasClass('ays_active_theme_image')){
                $(this).removeClass('ays_active_theme_image');
                $(document).find('#'+radio_id+'').prop('checked',false);
            }else{
                $(document).find('.ays_active_theme_image').removeClass('ays_active_theme_image');
                $(document).find('input[name="ays_quiz_theme"]').prop('checked',false);
                $(this).addClass('ays_active_theme_image');
                $(document).find('#'+radio_id+'').prop('checked',true);
            }
        });
        
        $(document).find('#ays_progress_bar_style').on('change', function () {
            $(document).find('.ays-progress').removeClass('display_block');
            $(document).find('.ays-progress.' + $(this).val() + '').addClass('display_block');
        });

        $(document).find('#ays_quiz_bg_image_position').on('change', function () {
            var quizContainer = $(document).find('.ays-quiz-live-container');
            quizContainer.css({
                'background-position': $(this).val()
            });
        });
        $(document).on('click', '.ays-remove-quiz-bg-img', function () {
            $(this).parent().find('img#ays-quiz-bg-img').attr('src', '');
            $(this).parent().parent().find('input#ays_quiz_bg_image').val('');
            $(this).parent().fadeOut();
            $(this).parent().parent().find('a.add-quiz-bg-image').show();
            $(document).find('.ays-quiz-live-container').css({'background-image': 'none'});
            toggleBackgrounGradient();
        });
        
        
        $(document).find('#ays_quest_animation').on('change', function () {
            var animation = $(this).val();
            aysAnimationLivePreview(animation);
        });
        
        $(document).find('.ays_animate_animation').on('click', function () {
            var animation = $(document).find('#ays_quest_animation').val();
            aysAnimationLivePreview(animation);
        });
        
        
        /* 
        ========================================== 
            Quiz animation effect live preview 
        ========================================== 
        */
        function aysAnimationLivePreview(animation){
            var quizContainer = $(document).find('.ays-quiz-live-container-1');
            var quizContainer2 = $(document).find('.ays-quiz-live-container-2');
            quizContainer.css({display:'flex'});
            quizContainer2.css({display:'none'});

            switch(animation){
                case 'none':
                    quizContainer.css({display:'none'});
                    setTimeout(function(){
                        quizContainer.css({display:'flex'});
                    }, 50);
                break;
                case 'fade':
                    quizContainer.css({
                        opacity: 0,                        
                        transition: '.5s ease-in-out'
                    });
                    setTimeout(function(){
                        quizContainer.css({
                            opacity: 1,
                            transition: 'none'
                        });
                    }, 500);
                break;
                case 'shake':
                    var scale, left, opacity;
                    quizContainer.animate({opacity: 0}, {
                        step: function (now, mx) {
                            scale = 1 - (1 - now) * 0.2;
                            left = (now * 50) + "%";
                            opacity = 1 - now;
                            quizContainer.css({
                                'transform': 'scale(' + scale + ')',
                                'position': 'absolute',
                                'top':0,
                                'opacity': 1
                            });
                            quizContainer2.css({
                                'left': left, 
                                'opacity': opacity,
                                'display':'flex',
                            });
                        },
                        duration: 800,
                        complete: function () {
                            quizContainer.hide();
                            quizContainer.css({
                                'transform':'scale(1)',
                                'opacity': 1,
                                'position': 'relative'
                            });
                            quizContainer2.css({
                                'display':'flex',
                                'transform':'scale(1)',
                                'opacity': 1
                            });                            
                            setTimeout(function(){
                                quizContainer.css({display:'flex'});
                                quizContainer2.css({display:'none'});
                            }, 100);
                        },
                        easing: 'easeInOutBack'
                    });
                break;                    
                case "lswing":
                    quizContainer.parent().css({
                        perspective: '800px',
                    });

                    quizContainer.addClass('swing-out-right-bck');
                    quizContainer.css({
                        'pointer-events': 'none'
                    });
                    setTimeout(function(){
                        quizContainer.css({
                            'position': 'absolute',
                        });
                        quizContainer2.css('display', 'flex');
                        quizContainer2.addClass('swing-in-left-fwd');
                    },400);
                    setTimeout(function(){
                        quizContainer.hide();
                        quizContainer.css({
                            'pointer-events': 'auto',
                            'position': 'static'
                        });
                        quizContainer2.css({
                            'position':'static',
                            'pointer-events': 'auto'
                        });
                        quizContainer.removeClass('swing-out-right-bck');                    
                        quizContainer2.removeClass('swing-in-left-fwd');
                        animating = false;
                    },1000);
                break;
                case "rswing":
                    quizContainer.parent().css({
                        perspective: '800px',
                    });

                    quizContainer.addClass('swing-out-left-bck');
                    quizContainer.css({
                        'pointer-events': 'none'
                    });
                    setTimeout(function(){
                        quizContainer.css({
                            'position': 'absolute',
                        });
                        quizContainer2.css('display', 'flex');
                        quizContainer2.addClass('swing-in-right-fwd');
                    },400);
                    setTimeout(function(){
                        quizContainer.hide();
                        quizContainer.css({
                            'pointer-events': 'auto',
                            'position': 'static'
                        });
                        quizContainer2.css({
                            'position':'static',
                            'pointer-events': 'auto'
                        });
                        quizContainer.removeClass('swing-out-left-bck');                    
                        quizContainer2.removeClass('swing-in-right-fwd');
                    },1000);
                break;
            }
            
        }
        
        /* 
        ========================================== 
            Background Gradient 
        ========================================== 
        */
        function toggleBackgrounGradient() {
            if($(document).find('input#ays_quiz_bg_image').val() == '') {
                let quiz_gradient_direction = $(document).find('#ays_quiz_gradient_direction').val();
                switch(quiz_gradient_direction) {
                    case "horizontal":
                        quiz_gradient_direction = "to right";
                        break;
                    case "diagonal_left_to_right":
                        quiz_gradient_direction = "to bottom right";
                        break;
                    case "diagonal_right_to_left":
                        quiz_gradient_direction = "to bottom left";
                        break;
                    default:
                        quiz_gradient_direction = "to bottom";
                }
                if($(document).find('input#ays-enable-background-gradient').attr('checked') == 'checked'){
                    $(document).find('.ays-quiz-live-container').css({
                        'background-image': "linear-gradient(" + quiz_gradient_direction + ", " + $(document).find('input#ays-background-gradient-color-1').val() + ", " + $(document).find('input#ays-background-gradient-color-2').val()+")"
                    });
                }else{
                     $(document).find('.ays-quiz-live-container').css({
                         'background-image': "none"
                     });
                }
            }
        }


        $(document).find('input[name="ays_quiz_theme"],#ays-quiz-color,#ays-quiz-text-color, #ays_answers_view, #ays_answers_padding, #ays_answers_font_size,'+
            '#ays_answers_border, #ays_answers_border_width, #ays_answers_border_style, #ays_answers_box_shadow,'+
            '#ays_show_answers_caption, #ays_answers_margin, #ays_ans_rw_icon_preview, #ays_wrong_icon_preview, input[name="ays_ans_right_wrong_icon"], '+
            '#ays_ans_img_height, #ays_ans_img_caption_position, #ays_ans_img_caption_style').on('change', function(e){
            refreshLivePreview();
        });
        refreshLivePreview();

        function refreshLivePreview(){
            let liveCSS = $(document).find('#ays_live_custom_css');
            let answersCSS = '';
            let answersCont = $(document).find('.ays-quiz-answers');
            let answersImagesCont = $(document).find('.answers-image-container .ays-quiz-answers');
            let answersField = $(document).find('.ays-quiz-answers .ays-field');

            let quizTheme = $(document).find('input[name="ays_quiz_theme"]:checked').val();
            $(document).find('.ays-quiz-live-container-answers').removeClass('ays_quiz_elegant_dark');
            $(document).find('.ays-quiz-live-container-answers').removeClass('ays_quiz_elegant_light');
            $(document).find('.ays-quiz-live-container-answers').removeClass('ays_quiz_rect_dark');
            $(document).find('.ays-quiz-live-container-answers').removeClass('ays_quiz_rect_light');
            switch(quizTheme){
                case "elegant_dark":
                    quizTheme = 'ays_quiz_elegant_dark';
                break;
                case "elegant_light":
                    quizTheme = 'ays_quiz_elegant_light';
                break;
                case "rect_dark":
                    quizTheme = 'ays_quiz_rect_dark';
                break;
                case "rect_light":
                    quizTheme = 'ays_quiz_rect_light';
                break;
                default:
                    quizTheme = '';
            }
            if(quizTheme != ''){
                $(document).find('.ays-quiz-live-container-answers').addClass(quizTheme);
            }

            let viewType = $(document).find('#ays_answers_view').val();
            let showCaption = $(document).find('#ays_show_answers_caption').prop('checked');
            let captionPosition = $(document).find('#ays_ans_img_caption_position').val();
            let captionStyle = $(document).find('#ays_ans_img_caption_style').val();
            let imageHeight = $(document).find('#ays_ans_img_height').val();

            let answersBorder = $(document).find('#ays_answers_border').prop('checked');
            let answersBoxShadow = $(document).find('#ays_answers_box_shadow').prop('checked');
            let answersBorderWidth = $(document).find('#ays_answers_border_width').val();
            let answersBorderStyle = $(document).find('#ays_answers_border_style').val();
            let answersBorderColor = $(document).find('#ays_answers_border_color').val();
            let answersBoxShadowColor = $(document).find('#ays_answers_box_shadow_color').val();

            let answersPadding = $(document).find('#ays_answers_padding').val();
            let answersMargin = $(document).find('#ays_answers_margin').val();
            let answersFontSize = $(document).find('#ays_answers_font_size').val();
            let AnswersRWIcon = $(document).find('input[name="ays_ans_right_wrong_icon"]:checked').val();
            let AnswersRIcon = $(document).find('input[name="ays_ans_right_wrong_icon"]:checked').next('.right_icon').attr('src');
            let AnswersWIcon = $(document).find('input[name="ays_ans_right_wrong_icon"]:checked').nextAll('.wrong_icon').attr('src');
            let showAnswersRWIcons = $(document).find('#ays_ans_rw_icon_preview').prop('checked');
            let showWrongIcons = $(document).find('#ays_wrong_icon_preview').prop('checked');

            let quizColor = $(document).find('#ays-quiz-color').val();
            let quizBgColor = $(document).find('#ays-quiz-bg-color').val();
            let quizTextColor = $(document).find('#ays-quiz-text-color').val();

            if(showAnswersRWIcons){
                $(document).find('.ays-quiz-answers .ays-field label').addClass('answered');
            }else{
                $(document).find('.ays-quiz-answers .ays-field label').removeClass('answered');
            }

            if(! showCaption){
                answersImagesCont.find('.ays-field input+label').addClass('display_none_imp');
            }else{
                answersImagesCont.find('.ays-field input+label').removeClass('display_none_imp');
            }

            if(viewType == 'list'){

                if(captionStyle == 'inside'){
                    if(captionPosition == 'top'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'initial',
                            'top': '0',
                            'bottom': 'unset',
                            'opacity': '1',
                        });
                    }else if(captionPosition == 'bottom'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'initial',
                            'top': 'unset',
                            'bottom': '0',
                            'opacity': '1',
                        });
                    }
                }

                answersCont.removeClass('ays_grid_view_container');
                answersCont.addClass('ays_list_view_container');
                answersField.removeClass('ays_grid_view_item');
                answersField.addClass('ays_list_view_item');

                if(captionPosition == 'top'){
                    $(document).find('.ays-field.ays_list_view_item').css({
                        'flex-direction': 'row'
                    });
                }else if(captionPosition == 'bottom'){
                    $(document).find('.ays-field.ays_list_view_item').css({
                        'flex-direction': 'row-reverse'
                    });
                }

            }else if(viewType == 'grid'){

                answersCont.removeClass('ays_list_view_container');
                answersCont.addClass('ays_grid_view_container');
                answersField.removeClass('ays_list_view_item');
                answersField.addClass('ays_grid_view_item');

                if(captionStyle == 'outside'){
                    if(captionPosition == 'top'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'initial',
                            'top': '0',
                            'bottom': 'unset',
                            'opacity': '1',
                        });
                    }else if(captionPosition == 'bottom'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'initial',
                            'top': 'unset',
                            'bottom': '0',
                            'opacity': '1',
                        });
                    }
                }else if(captionStyle == 'inside'){
                    if(captionPosition == 'top'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'absolute',
                            'top': '0',
                            'bottom': 'unset',
                            'opacity': '.5',
                        });
                    }else if(captionPosition == 'bottom'){
                        answersImagesCont.find('.ays-field input+label[for^="ays-answer-"]').css({
                            'position': 'absolute',
                            'top': 'unset',
                            'bottom': '0',
                            'opacity': '.5',
                        });
                    }
                }

                switch(captionPosition){
                    case "bottom":
                        answersImagesCont.find('.ays-field.ays_grid_view_item').css({
                            'flex-direction': 'column-reverse'
                        });
                    break;
                    case "top":
                        answersImagesCont.find('.ays-field.ays_grid_view_item').css({
                            'flex-direction': 'column'
                        });
                    break;
                }
            }

            if(answersBorder){
                $(document).find('.ays-quiz-answers .ays-field').css({
                    'border-width': answersBorderWidth+'px',
                    'border-style': answersBorderStyle,
                    'border-color': answersBorderColor,
                });

            }else{
                $(document).find('.ays-quiz-answers .ays-field').css({
                    'border-width': '0px',
                    'border-style': 'none',
                    'border-color': 'none',
                });
            }

            if(answersBoxShadow){
                $(document).find('.ays-quiz-answers .ays-field').css({
                    'box-shadow': '0px 0px 10px ' + answersBoxShadowColor,
                });
            }else{
                $(document).find('.ays-quiz-answers .ays-field').css({
                    'box-shadow': 'none',
                });
            }

            $(document).find('.ays-quiz-answers .ays-field .ays-answer-image').css({
                'height': imageHeight+'px',
            });
            $(document).find('.ays-quiz-answers .ays-field label').css({
                'font-size': answersFontSize + 'px'
            });
            $(document).find('.ays-quiz-answers .ays-field').css({
                'margin-bottom': answersMargin + 'px'
            });
            $(document).find('.ays-quiz-answers .ays-field.ays_grid_view_item').css({
                'width': 'calc(50% - ' + (answersMargin / 2) + 'px)',
            });
            $(document).find('.ays-quiz-answers .ays-field.ays_list_view_item').css({
                'width': '100%',
            });
            $(document).find('.ays-quiz-answers .ays-field.ays_grid_view_item:nth-child(odd)').css({
                'margin-right': (answersMargin / 2) + 'px',
            });

            answersCSS = '.ays-quiz-answers .ays-field input~label[for^="ays-answer-"] {'+
                    'padding: ' + answersPadding + 'px !important;'+
                    'color: '+ quizTextColor +';'+
                '}'+
                '.ays-quiz-live-container .ays-field input:checked~label:hover {'+
                    'background-color: '+ quizColor +';'+
                '}'+
                '.ays-quiz-live-container .ays-field input:checked~label {'+
                    'background-color: '+ quizColor +';'+
                '}';

            if(quizTheme == '' || quizTheme == 'elegant_dark' || quizTheme == 'elegant_light'){
                answersCSS += '.ays-quiz-live-container .ays-field {'+
                    'background-color: transparent;'+
                '}';
            }else{
                answersCSS += '.ays-quiz-live-container .ays-field {'+
                    'background-color: '+ quizColor +';'+
                '}';
            }
            answersCSS += '.ays-quiz-live-container .ays-field:hover,'+
                '.ays-quiz-live-container .ays-field:hover label {'+
                    'background-color: '+ quizColor +';'+
                    'color: '+ quizTextColor +';'+
                '}';
            let answerIcon = AnswersRIcon;
            if(showWrongIcons){
                answerIcon = AnswersWIcon;
            }
            if(showAnswersRWIcons){
                if(AnswersRWIcon == 'style-9'){

                    answersCSS += '#step1 .ays-quiz-answers .ays-field label[for^="ays-answer-"]:first-of-type::after {'+
                        'content: url("' + answerIcon + '") !important;'+
                        'position: relative;'+
                        'top: -12px;'+
                    '}'+
                    '#step2 .ays-quiz-answers .ays-field label[for^="ays-answer-"]:last-of-type::after {'+
                        'content: url("' + answerIcon + '") !important;'+
                        'height: auto;'+
                        'position: absolute;';
                        if(captionPosition == 'top'){
                            answersCSS += 'bottom: ' + (parseInt(answersPadding)+5) + 'px;';
                        }else{
                            answersCSS += 'top: ' + (parseInt(answersPadding)+5) + 'px;';
                        }
                        answersCSS += 'left: '+ (parseInt(answersPadding)+5) +'px;'+
                    '}';
                }else{

                    answersCSS += '#step1 .ays-quiz-answers .ays-field label[for^="ays-answer-"]:first-of-type::after {'+
                        'content: url("' + answerIcon + '") !important;'+
                    '}'+
                    '#step2 .ays-quiz-answers .ays-field label[for^="ays-answer-"]:last-of-type::after {'+
                        'content: url("' + answerIcon + '") !important;'+
                        'height: auto;'+
                        'position: absolute;';
                        if(captionPosition == 'top'){
                            answersCSS += 'bottom: ' + (parseInt(answersPadding)+5) + 'px;';
                        }else{
                            answersCSS += 'top: ' + (parseInt(answersPadding)+5) + 'px;';
                        }
                        answersCSS += 'left: '+ (parseInt(answersPadding)+5) +'px;'+
                    '}';
                }
            }else{
                answersCSS += '#step1 .ays-quiz-answers .ays-field label.answered[for^="ays-answer-"]::after,'+
                    '#step2 .ays-quiz-answers .ays-field label.answered[for^="ays-answer-"]::after {'+
                    'content: none!important;'+
                '}';
            }

            liveCSS.html('');
            liveCSS.html(liveCSS.html() + answersCSS);
        }

    });
})(jQuery);
