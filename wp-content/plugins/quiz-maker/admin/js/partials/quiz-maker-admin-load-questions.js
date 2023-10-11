(function($) {
    'use strict';

    function AysQuizMakerLoadQuestions(element, options){
        this.el = element;
        this.$el = $(element);
        this.htmlClassPrefix = 'ays-quiz-';
        this.dbOptions = undefined;

        this.question      = 'question_id';
        this.answer        = 'answer';

        this.ajaxAction    = 'get_quiz_question_html';

        this.init();

        return this;
    }

    AysQuizMakerLoadQuestions.prototype.init = function() {
        var _this = this;

        _this.setEvents();
    };

    AysQuizMakerLoadQuestions.prototype.setEvents = function(e){
        var _this = this;

        $(document).ready(function () {
            $(document).find('.ays-button').attr('disabled', 'disabled');
            $(document).find('.ays-button').prop('disabled', true);

            var progressBar = $('<div class="'+ _this.htmlClassPrefix +'questions-loading-progress-bar">' +
                '<div class="'+ _this.htmlClassPrefix +'questions-loading-progress-bar-fill" style="width: 0;">' +
                '   <span class="'+ _this.htmlClassPrefix +'questions-loading-progress-bar-percent"></span>' +
                '</div>' +
            '</div>');

            var questionIdsVal = $(document).find('#ays_already_added_questions').val();

            if( typeof questionIdsVal != "undefined" && questionIdsVal != "" ){
                // $(document.body).append( progressBar );
                // setTimeout(function () {
                //     $(document).find( '.' + _this.htmlClassPrefix +'questions-loading-progress-bar-fill' ).css('padding-right', '10px');
                // }, 1);

                _this.loadingfullPercent = 0;

                _this.loadingfullPercent = $(document).find('#ays_already_added_questions_count').val();


                _this.loadingfillPercent = 0;
                _this.loadingStartTime = Date.now();

                _this.loadSectionsRecursively( 0, false );
            } else {
                // $(document).find('.' + _this.htmlClassPrefix +'questions-loading-progress-bar').remove();
                // $(document).find('.' + _this.htmlClassPrefix +'questions-loading-progress-bar-overlay').remove();

                $(document).find('.' + _this.htmlClassPrefix +'loader-banner').removeAttr('disabled');
                $(document).find('.' + _this.htmlClassPrefix +'loader-banner').prop('disabled', false);
                $(document).find('.ays_quiz_loader_box').hide();
            }

        });
    }

    AysQuizMakerLoadQuestions.prototype.loadSectionsRecursively = async function( index, last ) {
        var _this = this;

        if( last ){
            _this.loadingfillPercent = 0;

            $(document).find('.ays-button').removeAttr('disabled');
            $(document).find('.ays-button').prop('disabled', false);
            $(document).find('.ays_quiz_loader_box').addClass('display_none');

            return;
        }

        var questionIdsVal = $(document).find('#ays_already_added_questions').val();
        if( typeof questionIdsVal != "undefined" && questionIdsVal != "" ){
            var questionIds = questionIdsVal.split(',');

            var questionsQueueLength = Math.ceil( questionIds.length / 50 );
            for( var i=0; i < questionsQueueLength; i++ ){
                var start = i * 50;
                var questionsPool = questionIds.slice( start, start + 50 );

                await _this.loadQuestion( questionsPool );
            }
        }

    }

    AysQuizMakerLoadQuestions.prototype.loadQuestion = async function( questions ) {
        var _this = this;

        var data = {
            action: _this.ajaxAction,
            ays_questions_ids: questions,
        };

        await $.ajax({
            url: functionsQuizLangObj.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            async: true,
            success: function(response){
                _this.loadingfillPercent += questions.length;
                if( _this.loadingfillPercent == _this.loadingfullPercent ){
                    setTimeout(function () {
                        // $(document).find('.' + _this.htmlClassPrefix +'questions-loading-progress-bar').remove();
                        // $(document).find('.' + _this.htmlClassPrefix +'questions-loading-progress-bar-overlay').remove();

                        $(document).find('.' + _this.htmlClassPrefix +'loader-banner').removeAttr('disabled');
                        $(document).find('.' + _this.htmlClassPrefix +'loader-banner').prop('disabled', false);
                        $(document).find('.ays_quiz_loader_box').hide();
                    }, 1000);

                }
                var percent = (_this.loadingfillPercent / _this.loadingfullPercent) * 100;
                percent = parseInt(percent);

                // $(document).find('.' + _this.htmlClassPrefix + 'questions-loading-progress-bar-fill').css('width', percent + '%');
                // $(document).find('.' + _this.htmlClassPrefix + 'questions-loading-progress-bar-percent').text(percent + '%');

                if( response.status === true ) {
                    $(document).find('div.ays-quiz-preloader').css('display', 'none');
                    var table = $(document).find('table#ays-questions-table tbody'),
                        id_container = $(document).find('input#ays_already_added_questions'),
                        existing_ids = ( id_container.val().split(',')[0] === "" ) ? [] : id_container.val().split(','),
                        new_ids = [];

                    for(var i = 0; i < response.ids.length; i++) {
                        new_ids.push(response.ids[i]);
                        table.append(response.rows[i]);
                        var table_rows = $('table#ays-questions-table tbody tr'),
                            table_rows_length = table_rows.length;
                        if( table_rows_length % 2 === 0 ) {
                            table_rows.eq( ( table_rows_length - 1 ) ).addClass('even');
                        }
                    }
                    var table_rows = $('table#ays-questions-table tbody tr');
                }
            },
            error: function() {
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                }).then(function(res){
                    return false;
                });
            }

        });
    }

    $.fn.AysQuizLoadQuestions = function(options) {
        return this.each(function() {
            if (!$.data(this, 'AysQuizLoadQuestions')) {
                $.data(this, 'AysQuizLoadQuestions', new AysQuizMakerLoadQuestions(this, options));
            } else {
                try {
                    $(this).data('AysQuizLoadQuestions').init();
                } catch (err) {
                    console.error('AysQuizLoadQuestions has not initiated properly');
                }
            }
        });
    };

    $(document).find('#ays-quiz-category-form.ays-quiz-form').AysQuizLoadQuestions();
})(jQuery);