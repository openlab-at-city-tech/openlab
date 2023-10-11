// (function($){
    var aysQuizFormSubmitted = true;
    String.prototype.hexToRgbA = function(a) {
        
        if (typeof a === 'undefined'){
            a = 1;
        }
        var ays_rgb;
        var result1 = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})/i.exec(this);
        var result2 = /^#?([a-f\d]{1})([a-f\d]{1})([a-f\d]{1})/i.exec(this);
        if(result1){
            ays_rgb = {
                r: parseInt(result1[1], 16),
                g: parseInt(result1[2], 16),
                b: parseInt(result1[3], 16)
            };
            return 'rgba('+ays_rgb.r+','+ays_rgb.g+','+ays_rgb.b+','+a+')';
        }else if(result2){
            ays_rgb = {
                r: parseInt(result2[1]+''+result2[1], 16),
                g: parseInt(result2[2]+''+result2[2], 16),
                b: parseInt(result2[3]+''+result2[3], 16)
            };
            return 'rgba('+ays_rgb.r+','+ays_rgb.g+','+ays_rgb.b+','+a+')';
        }else{
            return null;
        }
    }
    
    jQuery.fn.aysModal = function(action){
        var jQuerythis = jQuery(this);
        switch(action){
            case 'hide':
                jQuery(this).find('.ays-modal-content').css('animation-name', 'zoomOut');
                setTimeout(function(){
                    jQuery(document.body).removeClass('modal-open');
                    jQuery(document).find('.ays-modal-backdrop').remove();
                    jQuerythis.hide();
                }, 250);
            break;
            case 'show': 
            default:
                jQuerythis.show();
                jQuery(this).find('.ays-modal-content').css('animation-name', 'zoomIn');
                jQuery(document).find('.modal-backdrop').remove();
                jQuery(document.body).append('<div class="ays-modal-backdrop"></div>');
                jQuery(document.body).addClass('modal-open');
            break;
        }
    }
    
    function quiz_themes_live_preview(quiz_color, quiz_background_color, text_color, buttons_text_color) {
        jQuery(document).find('#ays-quiz-color').wpColorPicker('color', quiz_color);
        jQuery(document).find('#ays-quiz-bg-color').wpColorPicker('color', quiz_background_color);
        jQuery(document).find('#ays-quiz-text-color').wpColorPicker('color', text_color);
        jQuery(document).find('#ays-quiz-buttons-text-color').wpColorPicker('color', buttons_text_color);

        jQuery(document).find('#ays-quiz-live-button').css({
            'background-color': quiz_color,
            'color': buttons_text_color
        });
        jQuery(document).find('.ays-quiz-live-container').css({
            'background-color': quiz_background_color
        });
    }

    function checkTrue(flag) {
        return flag === true;
    }

    function openMediaUploaderForImage(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            frame:    'post',    // <-- this is the important part
            state:    'insert',
            library: {
                type: 'image'
            },
            multiple: false
        }).on('insert', function () {
            // var attachment = aysUploader.state().get('selection').first().toJSON();
            var wrap = element.parents('.ays-image-wrap');

            var state = aysUploader.state();
            var selection = selection || state.get('selection');
            if (! selection) return;
            // We set multiple to false so only get one image from the uploader
            var attachment = selection.first();
            var display = state.display(attachment).toJSON();  // <-- additional properties
            attachment = attachment.toJSON();
            // Do something with attachment.id and/or attachment.url here
            var imgurl = attachment.sizes[display.size].url;

            wrap.find('.ays-image-container img').attr('src', imgurl);
            wrap.find('input.ays-image-path').val(imgurl);
            wrap.find('.ays-image-container').fadeIn();
            wrap.find('a.ays-add-image').hide();
        }).open();
        return false;
    }

    function openMediaUploader(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            frame:    'post',    // <-- this is the important part
            state:    'insert',
            library: {
                type: 'image'
            },
            multiple: false
        }).on('insert', function () {
            // var attachment = aysUploader.state().get('selection').first().toJSON();

            var state = aysUploader.state();
            var selection = selection || state.get('selection');
            if (! selection) return;
            // We set multiple to false so only get one image from the uploader
            var attachment = selection.first();
            var display = state.display(attachment).toJSON();  // <-- additional properties
            attachment = attachment.toJSON();
            // Do something with attachment.id and/or attachment.url here
            var imgurl = attachment.sizes[display.size].url;

            element.text('Edit Image');
            element.parent().parent().find('.ays-question-image-container').fadeIn();
            element.parent().parent().find('img#ays-question-img').attr('src', imgurl);
            element.parent().parent().find('input#ays-question-image').val(imgurl);
        }).open();
        return false;
    }

    function openMediaUploaderQuestionBg(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload Question Background',
            button: {
                text: 'Upload'
            },
            frame:    'post',    // <-- this is the important part
            state:    'insert',
            library: {
                type: 'image'
            },
            multiple: false
        }).on('insert', function () {
            // var attachment = aysUploader.state().get('selection').first().toJSON();

            var state = aysUploader.state();
            var selection = selection || state.get('selection');
            if (! selection) return;
            // We set multiple to false so only get one image from the uploader
            var attachment = selection.first();
            var display = state.display(attachment).toJSON();  // <-- additional properties
            attachment = attachment.toJSON();
            // Do something with attachment.id and/or attachment.url here
            var imgurl = attachment.sizes[display.size].url;

            element.text('Edit Image');
            element.parent().parent().find('.ays-question-bg-image-container').fadeIn();
            element.parent().parent().find('img#ays-question-bg-img').attr('src', imgurl);
            element.parent().parent().find('input#ays-question-bg-image').val(imgurl);
        }).open();
        return false;
    }
    
    function openMusicMediaUploader(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload music',
            button: {
                text: 'Upload'
            },
            // frame:    'post',    // <-- this is the important part
            // state:    'insert',
            library: {
                type: 'audio'
            },
            multiple: false
        }).on('select', function () {
            var attachment = aysUploader.state().get('selection').first().toJSON();
            element.next().attr('src', attachment.url);
            element.parent().find('input.ays_quiz_bg_music').val(attachment.url);
        }).open();
        return false;
    }
    
    function openQuizMediaUploader(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            frame:    'post',    // <-- this is the important part
            state:    'insert',
            library: {
                type: 'image'
            },
            multiple: false
        }).on('insert', function () {
            // var attachment = aysUploader.state().get('selection').first().toJSON();

            var state = aysUploader.state();
            var selection = selection || state.get('selection');
            if (! selection) return;
            // We set multiple to false so only get one image from the uploader
            var attachment = selection.first();
            var display = state.display(attachment).toJSON();  // <-- additional properties
            attachment = attachment.toJSON();
            // Do something with attachment.id and/or attachment.url here
            var imgurl = attachment.sizes[display.size].url;

            if(element.hasClass('add-quiz-bg-image')){
                element.parent().find('img#ays-quiz-bg-img').attr('src', imgurl);
                element.parent().find('.ays-quiz-bg-image-container').fadeIn();
                element.next().val(attachment.url);
                jQuery(document).find('.ays-quiz-live-container').css({'background-image': 'url("'+imgurl+'")'});
                element.hide();
            }else if(element.hasClass('ays-edit-quiz-bg-img')){
                element.parent().find('.ays-quiz-bg-image-container').fadeIn();
                element.parent().find('img#ays-quiz-bg-img').attr('src', imgurl);
                jQuery(document).find('#ays_quiz_bg_image').val(imgurl);
                jQuery(document).find('.ays-quiz-live-container').css({'background-image': 'url("'+imgurl+'")'});
            }else{
                element.text('Edit Image');
                element.parent().parent().find('.ays-quiz-image-container').fadeIn();
                element.parent().parent().find('img#ays-quiz-img').attr('src', imgurl);
                jQuery('input#ays-quiz-image').val(imgurl);
                var ays_quiz_theme = jQuery(document).find('input[name="ays_quiz_theme"]:checked').val();
                switch (ays_quiz_theme) {
                    case 'elegant_dark':
                    case 'elegant_light':
                    case 'rect_light':
                    case 'rect_dark':
                    case 'classic_dark':
                    case 'classic_light':
                        jQuery(document).find('.ays-quiz-live-image').attr('src', imgurl);
                        jQuery(document).find('.ays-quiz-live-image').css({'display': 'block'});
                        break;
                    case 'modern_light':
                    case 'modern_dark':
                        // jQuery(document).find('.ays-quiz-live-container').css({'background-image':'url('+attachment.url+')'});
                        // jQuery(document).find('.ays-quiz-live-image').css({'display': 'none'});
                        jQuery(document).find('.ays-quiz-live-image').attr('src', imgurl);
                        jQuery(document).find('.ays-quiz-live-image').css({'display': 'block'});
                        jQuery(document).find('.ays-quiz-live-button').css('border','1px solid');
                        break;
                    default:
                        jQuery(document).find('.ays-quiz-live-image').attr('src', imgurl);
                        jQuery(document).find('.ays-quiz-live-image').css({'display': 'block'});
                        break;

                }
            }
        }).open();

        return false;
    }

    function openAnswerMediaUploader(e, element) {
        e.preventDefault();
        var aysUploader = wp.media({
            title: 'Upload',
            button: {
                text: 'Upload'
            },
            frame:    'post',    // <-- this is the important part
            state:    'insert',
            library: {
                type: 'image'
            },
            multiple: false
        }).on('insert', function () {
            // var attachment = aysUploader.state().get('selection').first().toJSON();

            var state = aysUploader.state();
            var selection = selection || state.get('selection');
            if (! selection) return;
            // We set multiple to false so only get one image from the uploader
            var attachment = selection.first();
            var display = state.display(attachment).toJSON();  // <-- additional properties
            attachment = attachment.toJSON();
            // Do something with attachment.id and/or attachment.url here
            var imgurl = attachment.sizes[display.size].url;
            
            element.parents().eq(1).find('.add-answer-image').css({'display': 'none'})
            element.parent().parent().find('.ays-answer-image-container').fadeIn();
            element.parent().parent().find('img.ays-answer-img').attr('src', imgurl);
            element.parents('tr').find('input.ays-answer-image-path').val(imgurl);
            if(element.hasClass('add-interval-image')){
                element.parent().parent().find('img').attr('src', imgurl);
                element.parents('tr').find('input.ays-answer-image').val(imgurl);
            }
        }).open();
        return false;
    }

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

    function createPagination(pagination, pagesCount, pageShow) {
        (function (baseElement, pages, pageShow) {
            var pageNum = 0, pageOffset = 0;

            function _initNav() {
                var appendAble = '';
                for (var i = 0; i < pagesCount; i++) {
                    var activeClass = (i === 0) ? 'active' : '';
                    appendAble += '<li class="' + activeClass + ' ays-question-page" data-page="' + (i + 1) + '">' + (i + 1) + '</li>';
                }
                jQuery('ul.ays-question-nav-pages').html(appendAble);
                var pagePos = (jQuery('div.ays-question-pagination').width()/2) - (parseInt(jQuery('ul.ays-question-nav-pages>li:first-child').css('width'))/2);
                jQuery('ul.ays-question-nav-pages').css({
                    'margin-left': pagePos,
                });
                //init events
                var toPage;
                baseElement.on('click', '.ays-question-nav-pages li, .ays-question-nav-btn', function (e) {
                    if (jQuery(e.target).is('.ays-question-nav-btn')) {
                        toPage = jQuery(this).hasClass('ays-question-prev') ? pageNum - 1 : pageNum + 1;
                    } else {
                        toPage = jQuery(this).index();
                    }
                    var page = Number(toPage) + 1;
                    show_hide_rows(page);
                    _navPage(toPage);
                });
            }

            function _navPage(toPage) {
                var sel = jQuery('.ays-question-nav-pages li', baseElement), w = sel.first().outerWidth(),
                    diff = toPage - pageNum;

                if (toPage >= 0 && toPage <= pages - 1) {
                    sel.removeClass('active').eq(toPage).addClass('active');
                    pageNum = toPage;
                } else {
                    return false;
                }

                if (toPage <= (pages - (pageShow + (diff > 0 ? 0 : 1))) && toPage >= 0) {
                    pageOffset = pageOffset + -w * diff;
                } else {
                    pageOffset = (toPage > 0) ? -w * (pages - pageShow) : 0;
                }

                sel.parent().css('left', pageOffset + 'px');
            }

            _initNav();

        })(pagination, pagesCount, pageShow);
    }

//    window.onload = function () {
//        if (document.getElementById('import_button')) {
//            document.getElementById('import_button').addEventListener('click', function () {
//                document.getElementById('import_file').click();
//            });
////            document.getElementById('example_button').addEventListener('click', function () {
////                document.getElementById('example_file').click();
////            });
//        }
//    }
    
    jQuery('.tablenav.top').find('.clear').before(jQuery('#category-filter-div'));
    
    function activate_question(element){
//        element.find('.ays_question_overlay').addClass('display_none');
        element.find('.ays_fa.ays_fa_times').parent()
            .removeClass('show_remove_answer')
            .addClass('active_remove_answer');
        element.find('.ays_add_answer').parents().eq(1).removeClass('show_add_answer');
//        element.addClass('active_question');
//        var this_question = element.find('.ays_question').text();
//        element.find('.ays_question').remove();
//        element.prepend('<input type="text" value="' + this_question + '" class="ays_question_input">');
        var answers_tr = element.find('.ays_answers_table tr');
        for (var i = 0; i < answers_tr.length; i++) {
            var answer_text = (jQuery(answers_tr.eq(i)).find('.ays_answer').text() && jQuery(answers_tr.eq(i)).find('.ays_answer').text() !== "Answer") ? "value='" + jQuery(answers_tr.eq(i)).find('.ays_answer').text() + "'" : "placeholder='"+ functionsQuizLangObj.answerText +"'";
            jQuery(answers_tr.eq(i)).find('.ays_answer_td').empty();
            jQuery(answers_tr.eq(i)).find('.ays_answer_td').append('<input type="text"  ' + answer_text + '  class="ays_answer">');
        }

        jQuery(document).find('#ays-quick-modal-content .ays_modal_element').removeClass('active_question_border');

        element.find('.ays_question_input').select();
        element.addClass('active_question_border');

    }
    
    function deactivate_questions() {
        if (jQuery('.active_question').length !== 0) {
            var question = jQuery('.active_question').eq(0);
            if(!jQuery(question).find('input[name^="ays_answer_radio"]:checked').length){
                jQuery(question).find('input[name^="ays_answer_radio"]').eq(0).attr('checked',true)
            }
            jQuery(question).find('.ays_add_answer').parents().eq(1).addClass('show_add_answer');
            jQuery(question).find('.fa.fa-times').parent().removeClass('active_remove_answer').addClass('show_remove_answer');

            var question_text = jQuery(question).find('.ays_question_input').val();
            jQuery(question).find('.ays_question_input').remove();
            jQuery(question).prepend('<p class="ays_question">' + question_text + '</p>');
            var answers_tr = jQuery(question).find('.ays_answers_table tr');
            for (var i = 0; i < answers_tr.length; i++) {
                var answer_text = (jQuery(answers_tr.eq(i)).find('.ays_answer').val()) ? jQuery(answers_tr.eq(i)).find('.ays_answer').val() : '';
                jQuery(answers_tr.eq(i)).find('.ays_answer_td').empty();
                var answer_html = '<p class="ays_answer">' + answer_text + '</p>'+((answer_text == '')?'<p>Answer</p>':'');
                jQuery(answers_tr.eq(i)).find('.ays_answer_td').append(answer_html)
            }
            jQuery('.active_question').find('.ays_question_overlay').removeClass('display_none');
            jQuery('.active_question').removeClass('active_question');
        }
    }
    
    function searchForPage(params, data) {
        // If there are no search terms, return all of the data
        if (jQuery.trim(params.term) === '') {
          return data;
        }

        // Do not display the item if there is no 'text' property
        if (typeof data.text === 'undefined') {
          return null;
        }
        var searchText = data.text.toLowerCase();
        // `params.term` should be the term that is used for searching
        // `data.text` is the text that is displayed for the data object
        if (searchText.indexOf(params.term) > -1) {
          var modifiedData = jQuery.extend({}, data, true);
          modifiedData.text += ' (matched)';

          // You can return modified objects from here
          // This includes matching the `children` how you want in nested data sets
          return modifiedData;
        }

        // Return `null` if the term should not be displayed
        return null;
    }

    function selectElementContents(el) {
        if (window.getSelection && document.createRange) {
            var _this = jQuery(document).find('strong.ays-quiz-shortcode-box');

            var text      = el.textContent;
            var textField = document.createElement('textarea');

            textField.innerText = text;
            document.body.appendChild(textField);
            textField.select();
            document.execCommand('copy');
            textField.remove();

            var selection = window.getSelection();
            selection.setBaseAndExtent(el,0,el,1);

            jQuery(el).attr( "data-original-title", quizLangObj.copied );
            jQuery(el).attr( "title", quizLangObj.copied );

            jQuery(el).tooltip("show");

        } else if (document.selection && document.body.createTextRange) {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.select();
        }
    }

    function aysGenCharArray(charA, charZ) {
        var a = [], i = charA.charCodeAt(0), j = charZ.charCodeAt(0);
        for (; i <= j; ++i) {
            a.push(String.fromCharCode(i));
        }
        return a;
    }

    function aysGetJsonFromUrl( url ) {
        if (!url) url = location.href;
        var question = url.indexOf("?");
        var hash = url.indexOf("#");
        if (hash == -1 && question == -1) return {};
        if (hash == -1) hash = url.length;
        var query = question == -1 || hash == question + 1 ? url.substring(hash) :
            url.substring(question + 1, hash);
        var result = {};
        var queryArray = query.split("&");
        for(var i=0; i < queryArray.length; i++){
            var part = queryArray[i];
            if (!part) return;
            part = part.split("+").join(" "); // replace every + with space, regexp-free version
            var eq = part.indexOf("=");
            var key = eq > -1 ? part.substr(0, eq) : part;
            var val = eq > -1 ? decodeURIComponent(part.substr(eq + 1)) : "";
            var from = key.indexOf("[");
            if (from == -1) result[decodeURIComponent(key)] = val;
            else {
                var to = key.indexOf("]", from);
                var index = decodeURIComponent(key.substring(from + 1, to));
                key = decodeURIComponent(key.substring(0, from));
                if (!result[key]) result[key] = [];
                if (!index) result[key].push(val);
                else result[key][index] = val;
            }
        }
        return result;
    }

    function aysQuizstripHTML( dirtyString ) {
        var container = document.createElement('div');
        var text = document.createTextNode(dirtyString);
        container.appendChild(text);

        return container.innerHTML; // innerHTML will be a xss safe string
    }

    function submitOnce(subButton){
        var subLoader = subButton.parents('div').find('.ays_quiz_loader_box');
        if ( subLoader.hasClass("display_none") ) {
            subLoader.removeClass("display_none");
        }
        subLoader.css("padding-left" , "8px");
        subLoader.css("display", "inline-flex");
        setTimeout(function() {
            if( aysQuizFormSubmitted ){
                jQuery(document).find('.ays-quiz-loader-banner, .ays-quiz-category-next-button-class, .ays-quiz-next-button-class').attr('disabled', true);
            }else{
                subLoader.removeAttr("style");
                subLoader.addClass("display_none");
            }
        }, 50);

        setTimeout( function(){
            jQuery(document).find('.ays-quiz-loader-banner, .ays-quiz-category-next-button-class, .ays-quiz-next-button-class').attr('disabled', false);
            subLoader.removeAttr("style");
            subLoader.addClass("display_none");
        }, 5000 );
    }

    function copyEmbedCodeContents(el) {
        if (window.getSelection && document.createRange) {
            var _this = jQuery(document).find('.ays-quiz-embed-code-textarea');
            var btn   = jQuery(document).find('.ays-quiz-copy-embed-code');

            var text      = _this.val();
            var textField = document.createElement('textarea');

            textField.innerText = text;
            document.body.appendChild(textField);
            textField.select();
            document.execCommand('copy');
            textField.remove();

            btn.attr( "data-original-title", quizLangObj.copied );
            btn.attr( "title", quizLangObj.copied );

            btn.tooltip("show");
        } else if (document.selection && document.body.createTextRange) {
            var textRange = document.body.createTextRange();
            textRange.moveToElementText(el);
            textRange.select();
        }
    }

    function ays_quiz_search_box_pagination(listTableClass, searchBox) {
        if(jQuery(document).find( "." + listTableClass ).length) {
            if(jQuery(document).find( "#" + searchBox ).length) {
                var search_string = jQuery(document).find("#" + searchBox).val();
                if(search_string != "") {
                    jQuery(document).find("."+ listTableClass +" .pagination-links a").each(function() {
                        if ( typeof this.href != "undefined" && this.href != "" ) {
                            if ( this.href.indexOf("&s=") < 0 ) {
                                this.href = this.href + "&s=" + search_string;
                            }
                        }
                    });
                }
            }
        }
    }


//})(jQuery);
