(function( $ ) {
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
    
    // Add question into quiz from popup window
    $(document).find('form#ays_add_question_rows').on( 'submit', function(e) {
        $(document).find('div.ays-quiz-preloader').css('display', 'flex');
        var questionsModalBox = $(document).find('div.ays-questions-modal');
        
        if(window.aysQuestNewSelected.length > 0){
            $(document).find('td.empty_quiz_td').parent().remove();
            var data = $(this).serializeFormJSON();
            data.action = 'add_question_rows';
            data['ays_questions_ids[]'] = window.aysQuestNewSelected;

            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response){
                    if( response.status === true ) {
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        var table = $('table#ays-questions-table tbody'),
                            id_container = $(document).find('input#ays_already_added_questions'),
                            existing_ids = ( id_container.val().split(',')[0] === "" ) ? [] : id_container.val().split(','),
                            new_ids = [];
                        for(var i = 0; i < response.ids.length; i++) {
                            if( $.inArray( response.ids[i], existing_ids ) === -1 ) {
                                new_ids.push(response.ids[i]);
                                table.append(response.rows[i]);
                                var table_rows = $('table#ays-questions-table tbody tr'),
                                    table_rows_length = table_rows.length;
                                if( table_rows_length % 2 === 0 ) {
                                    table_rows.eq( ( table_rows_length - 1 ) ).addClass('even');
                                }
                            }else{
                                var position = $.inArray( response.ids[i], existing_ids );
                            }
                        }
                        var table_rows = $('table#ays-questions-table tbody tr');

                        for(var i = 0; i < new_ids.length; i++){
                            existing_ids.push(new_ids[i]);
                        }

                        table_rows.each(function(){
                            var id = $(this).data('id');
                            if($.inArray( id.toString(), existing_ids ) === -1){
                                $(this).remove();
                            }
                        });
                        id_container.val( existing_ids );
                    }
                    $(document).find('.ays-modal').aysModal('hide');
                    var questions_count = response.ids.length;

                    var table_rows = $('table#ays-questions-table tbody tr');

                    var questions_count_val = questions_count;
                    if ( table_rows.length > 0 && table_rows.length > questions_count ) {
                        questions_count_val = table_rows.length;
                    }
                    
                    $(document).find('.questions_count_number').html(table_rows.length);

                    $(document).find('.ays_refresh_qbank_categories').removeClass('display_none');
                    window.aysQuestNewSelected = [];
                },
                error: function() {
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                    }).then(function(res){
                        $(document).find('.ays-modal').aysModal('hide');
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                    });
                }
            });
        }else{
            alert('You must select new questions to add to the quiz.');
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
        }
        e.preventDefault();
    } );

    // Export Results filters
    $(document).find('#quiz_stat_select').on('change',function(e){
        var quiz_id = $(document).find('#quiz_stat_select').val();
        var action = 'get_current_quiz_statistic';
        var $this = $(this);
        $this.parent().find('img.loader').removeClass('display_none');
        $.ajax({
            type: "POST",
            url: quiz_maker_ajax.ajax_url,
            data: {
                quiz_id: quiz_id,
                action: action
            },
            dataType: "json",
            success: function (response) {
                $("#ays_quiz_stat").remove();
                $("#chart-container").append('<canvas id="ays_quiz_stat" width="400" height="400"></canvas>');
                var dates = response.dates;
                var dates_values = response.dates_values;
                var ctx = $("#ays_quiz_stat");
                dates_values.push(Math.max.apply(Math,dates_values)+Math.round(Math.max.apply(Math,dates_values)/5));
                aysQuizzesChart({dates: dates, values: dates_values});
                $('.ays-collection').children().not(':first').remove();
                $('.ays-collection').append(response.charts);
                
                $this.parent().find('img.loader').addClass('display_none');
            }
        });
    });
    
    $(document).find('#start-date-filter').on('change', function(e) {
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#end-date-filter').on('change', function(e) {
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#export_results_guests').on('change', function(e) {
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#export_results_only_guests').on('change', function(e) {
        if( $(this).prop('checked') === true ){
            $(this).parents('#ays_export_filter').find('.filter-row-overlay').removeClass('display_none');
        }else{
            $(this).parents('#ays_export_filter').find('.filter-row-overlay').addClass('display_none');
        }
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#user_id-filter', function(e) {
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#quiz_id-filter', function(e) {
        $('#ays_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#ays_export_filter').on('submit', function(e) {
        e.preventDefault();
        var $this = $('#export-filters');
        var action = 'ays_results_export_filter';
        var user_id = $('#user_id-filter').val();
        var quiz_id = $('#quiz_id-filter').val();
        var date_from = $('#start-date-filter').val() || $('#start-date-filter').attr('min');
        var date_to = $('#end-date-filter').val() || $('#end-date-filter').attr('max');
        var export_guests = $('#export_results_guests').prop('checked');
        var export_answers_only_guests = $('#export_results_only_guests').prop('checked');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                user_id: user_id,
                quiz_id: quiz_id,
                date_from: date_from,
                date_to: date_to,
                with_guests: ! export_guests,
                only_guests: export_answers_only_guests
            },
            success: function(response) {
                $this.find('div.ays-quiz-preloader').css('display', 'none');
                if( response && response.qanak ){
                    $this.find(".export_results_count span").text( response.qanak );
                }else{
                    $this.find(".export_results_count span").text( 0 );
                }
            }
        });
    });
        
    var userSel2, quizSel2;
    
    $(document).find('.ays-export-filters').on('click', function(e) {
        var $this = $('#export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $this.aysModal('show');
        e.preventDefault();
        var action = 'ays_show_filters';
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: {
                action: action,
                with_guests: true
            },
            success: function(res) {
                $this.find('div.ays-quiz-preloader').css('display', '');
                var newUserSelect = "";
                var newQuizSelect = "";
                for (var u in res.users) {
                    newUserSelect += '<option value="'+ res.users[u].user_id +'">'+ res.users[u].display_name +'</option>';
                }
                for (var q in res.quizzes) {
                    newQuizSelect += '<option value="'+ res.quizzes[q].quiz_id +'">'+ res.quizzes[q].title +'</option>';
                }
                
                $this.find('#user_id-filter').html(newUserSelect);
                var userSel = $this.find('#user_id-filter');
                userSel2 = userSel.select2({
                    dropdownParent: userSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });

                $this.find('#quiz_id-filter').html(newQuizSelect);
                var quizSel = $this.find('#quiz_id-filter');
                quizSel2 = quizSel.select2({
                    dropdownParent: quizSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });
                // $this.find('input[type="date"]').attr({
                //     min: res.date_min,
                //     max: res.date_max
                // });
                
                $(document).on('click', '.select2-selection__choice__remove', function(){
                    userSel2.select2("close");
                    quizSel2.select2("close");
                });
                
                $this.find(".export_results_count span").text(res.count);
                $this.find('.ays-modal-body').show();
            },
            error: function() {
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                }).then(function(res){
                    $(document).find('#export-filters div.ays-quiz-preloader').css('display', 'none');                    
                    $this.aysModal('hide');
                });
            }
        });
    });
    
    $(document).find('#start-date-answers-filter').on('change', function(e) {
        $('#ays_export_answers_filter').submit();
        e.preventDefault();
    });

    $(document).find('#end-date-answers-filter').on('change', function(e) {
        $('#ays_export_answers_filter').submit();
        e.preventDefault();
    });

    $(document).find('#export_answers_guests').on('change', function(e) {
        $('#ays_export_answers_filter').submit();
        e.preventDefault();
    });

    $(document).find('#export_answers_only_guests').on('change', function(e) {
        if( $(this).prop('checked') === true ){
            $(this).parents('#ays_export_answers_filter').find('.filter-row-overlay').removeClass('display_none');
        }else{
            $(this).parents('#ays_export_answers_filter').find('.filter-row-overlay').addClass('display_none');
        }
        $('#ays_export_answers_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#user_id-answers-filter', function(e) {
        $('#ays_export_answers_filter').submit();
        e.preventDefault();
    });

    $(document).find('#ays_export_answers_filter').on('submit', function(e) {
        e.preventDefault();
        var $this = $('#export-answers-filters');
        var action = 'ays_results_export_filter';
        var user_id = $('#user_id-answers-filter').val();
        var quiz_id = $('#quiz_id-answers-filter').val();
        var date_from = $('#start-date-answers-filter').val();
        var date_to = $('#end-date-answers-filter').val();
        var export_guests = $('#export_answers_guests').prop('checked');
        var export_answers_only_guests = $('#export_answers_only_guests').prop('checked');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                user_id: user_id,
                quiz_id: quiz_id,
                date_from: date_from,
                date_to: date_to,
                flag: ! export_guests,
                only_guests: export_answers_only_guests
            },
            success: function(response) {
                $this.find('div.ays-quiz-preloader').css('display', 'none');
                $this.find(".export_results_count span").text(response.qanak);
            }
        });
    });

    // Export Answers filters
    $(document).find('.ays-export-answers-filters').on('click', function(e) {
        var $this = $('#export-answers-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $this.aysModal('show');
        e.preventDefault();
        var action  = 'ays_show_filters';
        var quiz_id = $this.find('#quiz_id-answers-filter').val();
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: {
                action: action,
                quiz_id: quiz_id,
                flag: true
            },
            success: function(res) {
                $this.find('div.ays-quiz-preloader').css('display', '');
                var newUserSelect = "";
                for (var u in res.users) {
                    newUserSelect += '<option value="'+ res.users[u].user_id +'">'+ res.users[u].display_name +'</option>';
                }

                $this.find('#user_id-answers-filter').html(newUserSelect);
                var userSel = $this.find('#user_id-answers-filter');
                userSel2 = userSel.select2({
                    dropdownParent: userSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });

                $(document).on('click', '.select2-selection__choice__remove', function(){
                    userSel2.select2("close");
                });

                $this.find(".export_results_count span").text(res.count);
                $this.find('.ays-modal-body').show();
            },
            error: function() {
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                }).then(function(res){
                    $(document).find('#export-answers-filters div.ays-quiz-preloader').css('display', 'none');
                    $this.aysModal('hide');
                });
            }
        });
    });

    $(document).on('click', '.ays_quizid_clear', function(){
        quizSel2.val(null).trigger('change');
        return false;
    });
    
    $(document).on('click', '.ays_userid_clear', function(){
        userSel2.val(null).trigger('change');
        return false;
    });

    $(document).find('#question-start-date-filter').on('change', function(e) {
        $('#ays_questions_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#question-end-date-filter').on('change', function(e) {
        $('#ays_questions_export_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#author_id-filter', function(e) {
        $('#ays_questions_export_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#category_id-filter', function(e) {
        $('#ays_questions_export_filter').submit();
        e.preventDefault();
    });

    $(document).on('change.select2', '#tag_id-filter', function(e) {
        $('#ays_questions_export_filter').submit();
        e.preventDefault();
    });

    $(document).find('#ays_questions_export_filter').on('submit', function(e) {
        e.preventDefault();
        var $this = $('#questions-export-filters');
        var action = 'ays_questions_export_filter';
        var author_id = $('#author_id-filter').val();
        var category_id = $('#category_id-filter').val();
        var tag_id = $('#tag_id-filter').val();
        var date_from = $('#question-start-date-filter').val() || $('#question-start-date-filter').attr('min');
        var date_to = $('#question-end-date-filter').val() || $('#question-end-date-filter').attr('max');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                author_id: author_id,
                category_id: category_id,
                tag_id: tag_id,
                date_from: date_from,
                date_to: date_to
            },
            success: function(response) {
                $this.find('div.ays-quiz-preloader').css('display', 'none');
                $this.find(".export_results_count span").text(response.count);
            }
        });
    });

    // Export Questions filters
    var authorSel2, catSel2, tagSel2;
    $(document).find('.ays-export-questions-filters').on('click', function(e) {
        var $this = $('#questions-export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $this.aysModal('show');
        e.preventDefault();
        var action = 'ays_show_questions_filters';
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: {
                action: action
            },
            success: function(res) {
                $this.find('div.ays-quiz-preloader').css('display', '');
                var newAuthorSelect = "";
                var newCategorySelect = "";
                var newTagSelect = "";
                for (var u in res.authors) {
                    newAuthorSelect += '<option value="'+ res.authors[u].author_id +'">'+ res.authors[u].display_name +'</option>';
                }
                for (var q in res.categories) {
                    newCategorySelect += '<option value="'+ res.categories[q].category_id +'">'+ res.categories[q].title +'</option>';
                }
                for (var q in res.tags) {
                    newTagSelect += '<option value="'+ res.tags[q].id +'">'+ res.tags[q].title +'</option>';
                }

                $this.find('#author_id-filter').html(newAuthorSelect);
                var authorSel = $this.find('#author_id-filter');
                authorSel2 = authorSel.select2({
                    dropdownParent: authorSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });

                $this.find('#category_id-filter').html(newCategorySelect);
                var catSel = $this.find('#category_id-filter');
                catSel2 = catSel.select2({
                    dropdownParent: catSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });

                $this.find('#tag_id-filter').html(newTagSelect);
                var tagSel = $this.find('#tag_id-filter');
                tagSel2 = tagSel.select2({
                    dropdownParent: tagSel.parent(),
                    closeOnSelect: true,
                    allowClear: false
                });
                // $this.find('input[type="date"]').attr({
                //     min: res.date_min,
                //     max: res.date_max
                // });

                $(document).on('click', '.select2-selection__choice__remove', function(){
                    authorSel2.select2("close");
                    catSel2.select2("close");
                    tagSel2.select2("close");
                });

                $this.find(".export_results_count span").text(res.count);
                $this.find('.ays-modal-body').show();
            },
            error: function() {
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h6>"+ quizLangObj.somethingWentWrong +"</h6>"
                }).then(function(res){
                    $(document).find('#questions-export-filters div.ays-quiz-preloader').css('display', 'none');
                    $this.aysModal('hide');
                });
            }
        });
    });

    $(document).on('click', '.ays_catid_clear', function(){
        catSel2.val(null).trigger('change');
        return false;
    });

    $(document).on('click', '.ays_authorid_clear', function(){
        authorSel2.val(null).trigger('change');
        return false;
    });

    $(document).find('.export-action').on('click', function(e) {
        e.preventDefault();
        var $this = $('#export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        var action = 'ays_results_export_file';
        var user_id = $('#user_id-filter').val();
        var quiz_id = $('#quiz_id-filter').val();
        var type = $(this).data('type');
        var date_from = $('#start-date-filter').val() || $('#start-date-filter').attr('min');
        var date_to = $('#end-date-filter').val() || $('#end-date-filter').attr('max');
        var export_guests = $('#export_results_guests').prop('checked');
        var export_answers_only_guests = $('#export_results_only_guests').prop('checked');
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: { 
                action: action, 
                type: type, 
                user_id: user_id, 
                quiz_id: quiz_id, 
                date_from: date_from, 
                date_to: date_to,
                with_guests: ! export_guests,
                only_guests: export_answers_only_guests
            },
            success: function(response) {
                if (response.status) {
                    switch (response.type) {
                        case 'xlsx':
                            var options = {
                                fileName: "quiz_results_export",
                                header: true
                            };
                            var tableData = [{
                                "sheetName": "Quiz results",
                                "data": response.data
                            }];
                            Jhxlsx.export(tableData, options);
                            break;
                        case 'csv':
                            var csvOptions = {
                                separator: ',',
                                fileName: 'quiz_results_export.csv'
                            };
                            var x = new CSVExport(response.data, response.fileFields, csvOptions);
                            break;
                        case 'json':
                            var text = JSON.stringify(response.data);
                            var data = new Blob([text], {type: "application/" + response.type});
                            var fileUrl = window.URL.createObjectURL(data);
                            $('#downloadFile').attr({
                                'href': fileUrl,
                                'download': "quiz_results_export." + response.type,
                            })[0].click();
                            window.URL.revokeObjectURL(fileUrl);
                            break;
                        default:
                            break;
                    }
                }
                $this.find('div.ays-quiz-preloader').css('display', 'none');
            }
        });
    });
    
    $(document).find('.ays-export-questions-statistics').on('click', function (e) {
        $(this).addClass('disabled');
        var type = $(this).attr('data-type');
        var id = $(this).attr('quiz-id');
        var action = 'ays_questions_statistics_export';
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                id: id,
                type: type
            },
            success: function (response) {
                if (response.status) {
                    var options = {
                        fileName: "quiz_questions_statistics_export",
                        header: true
                    };
                    var tableData = [{
                        "sheetName": "Quiz questions",
                        "data": response.data
                    }];
                    Jhxlsx.export(tableData, options);
                }
                $('.ays-export-questions-statistics').removeClass('disabled');
            }
        });
        e.preventDefault();
    });

    //Aro export single question results
    $(document).on('click','.ays-single-question-results-export', function (e) {
        var $this = $(this);
        $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'flex');
        $this.addClass('disabled');
        var type = $this.attr('data-type');
        var result_id = $this.attr('date-result');
        var action = 'ays_single_question_results_export';
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                result: result_id,
                type: type
            },
            success: function (response) {
                if (response.status) {
                    var options = {
                        fileName: "quiz_single_result_export",
                        header: true
                    };
                    var tableData = [{
                        "sheetName": "Quiz question",
                        "data": response.data
                    }];
                    Jhxlsx.export(tableData, options);
                }
                $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'none');
                $this.removeClass('disabled');
            }
        });
        e.preventDefault();
    });

    //Aro export result PDF
    $(document).on('click','.ays-export-result-pdf', function (e) {
        var $this = $(this);
        $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'flex');
        $this.attr('disabled');

        var action = 'ays_export_result_pdf';
        var result_id = $this.data('result');

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                result: result_id,
            },
            success: function (response) {
                if (response.status) {
                    $this.parent().find('#downloadFile').attr({
                        'href': response.fileUrl,
                        'download': response.fileName,
                    })[0].click();
                    window.URL.revokeObjectURL(response.fileUrl);
                }else{
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h4>"+ quizLangObj.dataDeleted +"</h4>"
                    })
                }
                $this.parents('.ays-modal').find('div.ays-quiz-preloader').css('display', 'none');
                $this.removeClass('disabled');
            }
        });
        e.preventDefault();
    });

    // Aro Export Answers XLSX
    $(document).find('.export-anwers-action').on('click', function (e) {
        e.preventDefault();
        var _this = $(this);
        _this.addClass('disabled');
        var $this      = $('#export-answers-filters');
        var action     = 'ays_answers_statistics_export';

        var type       = $(this).data('type');
        var quiz_id    = $(this).attr('quiz-id');
        var user_id    = $('#user_id-answers-filter').val();
        var date_from  = $('#start-date-answers-filter').val();
        var date_to    = $('#end-date-answers-filter').val();
        var export_guests = $('#export_answers_guests').prop('checked');
        var export_answers_only_guests = $('#export_answers_only_guests').prop('checked');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: {
                action: action,
                type: type,
                user_id: user_id,
                quiz_id: quiz_id,
                date_from: date_from,
                date_to: date_to,
                flag: ! export_guests,
                only_guests: export_answers_only_guests
            },
            success: function (response) {
                _this.removeClass('disabled');
                if (response.status) {
                    var options = {
                        fileName: "quiz_answers_export",
                        header: true
                    };
                    var tableData = [{
                        "sheetName": "Quiz questions",
                        "data": response.data
                    }];
                    Jhxlsx.export(tableData, options);
                    $this.find('div.ays-quiz-preloader').css('display', 'none');
                }else{
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h4>"+ quizLangObj.dataDeleted +"</h4>"
                    }).then(function(response) {
                        $this.find('div.ays-quiz-preloader').css('display', 'none');
                    });
                }
            }
        });
    });

    // Export questions filters
    $(document).find('.ays-questions-export').on('click', function (e) {
//        $('.ays-export-dropdown .ays-wp-loading').removeClass('d-none');
//        $('.export-download-progress-bar').removeClass('display_none');
//        $('.ays-export-dropdown>.dropdown-toggle').addClass('disabled');
        var $this = $('#questions-export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        var author_id = $('#author_id-filter').val();
        var category_id = $('#category_id-filter').val();
        var tag_id = $('#tag_id-filter').val();
        var date_from = $('#question-start-date-filter').val() || $('#question-start-date-filter').attr('min');
        var date_to = $('#question-end-date-filter').val() || $('#question-end-date-filter').attr('max');

        var type = $(this).attr('data-type');
        var action = 'ays_questions_export';
//        var time1;
//        var timeout = setTimeout(function(){
//            var width = 0;
//            time1 = setInterval(function(){
//                width++;
//                $('.ays-progress-bar').css({
//                    width: width + '%'
//                });
//                $('.ays-progress-value').text(width + '%');
//                if(width >= 64){
//                    clearInterval(time1);
//                }
//            }, 30);
//        }, 500);
//
//        var width = 0;
//        var time = setInterval(function(){
//            width++;
//            $('.ays-progress-bar').css({
//                width: width + '%'
//            });
//            $('.ays-progress-value').text(width + '%');
//            if(width >= 42){
//                clearInterval(time);
//            }
//        }, 30);
        
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                type: type,
                author_id: author_id,
                category_id: category_id,
                tag_id: tag_id,
                date_from: date_from,
                date_to: date_to
            },
            success: function (response) {
//                clearInterval(time);
//                clearInterval(time1);
//                clearTimeout(timeout);
                if (response.status) {
                    switch (response.type) {
                        case 'xlsx':
                            var options = {
                                fileName: "quiz_result_questions_export",
                                header: true
                            };
                            var tableData = [{
                                "sheetName": "Quiz questions",
                                "data": response.data
                            }];
                            Jhxlsx.export(tableData, options);
                            break;
                        case 'csv':
                            var csvOptions = {
                                separator: ',',
                                fileName: 'quiz_result_questions_export.csv'
                            };
                            var x = new CSVExport(response.data, response.fields, csvOptions);
                            break;
                        case 'json':
                            var text = JSON.stringify(response.data);
                            var data = new Blob([text], {type: "application/" + response.type});
                            var fileUrl = window.URL.createObjectURL(data);
                            $('#downloadFile').attr({
                                'href': fileUrl,
                                'download': "quiz_result_questions_export." + response.type,
                            })[0].click();
                            window.URL.revokeObjectURL(fileUrl);
                            break;
                        default:
                            break;
                    }
                }
//                $('.ays-progress-bar').css({
//                    width: 100 + '%',
//                    transition: '.2s ease'
//                });
//                $('.ays-progress-value').text(100 + '%');
//                $('.export-download-progress-bar').addClass('display_none');
//                $('.ays-export-dropdown .ays-wp-loading').addClass('d-none');
//                $('.ays-export-dropdown>.dropdown-toggle').removeClass('disabled');
                $this.find('div.ays-quiz-preloader').css('display', 'none');
            }
        });
        e.preventDefault();
    });

    // Open results more information popup window
    $(document).on('click', '.ays_quiz_read_result', function(e){
        var where = 'row';
        ays_show_results(e, $(this).find('.ays-show-results').eq(0), where);
    });

    function ays_show_results(e, this_element, where){
        if($(e.target).hasClass('ays_confirm_del') ||
           $(e.target).hasClass('ays_result_delete') ||
           $(e.target).hasClass('ays_result_certificate')){
            
        }else{
            e.preventDefault();
            $(document).find('div.ays-quiz-preloader').css('display', 'flex');
            $(document).find('#ays-results-modal').aysModal('show');
            var result_id = this_element.data('result');
            var action = 'ays_show_results';
            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: {
                    result: result_id,
                    action: action
                },
                success: function(response){
                    if(response.status === true){
                        $('div#ays-results-body').html(response.rows);
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        if($(this_element).parents('tr').hasClass('ays_read_result')){
                            $(this_element).parents('tr').removeClass('ays_read_result');
                            $(document).find('#ays_results_bage').text(
                                parseInt($(document).find('#ays_results_bage').text())-1
                            );
                        }
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h4>"+ quizLangObj.dataDeleted +"</h4>"

                        }).then(function(res) {
                            $(document).find('div.ays-quiz-preloader').css('display', 'none');
                            if($(this_element).parents('tr').hasClass('ays_read_result')){
                                $(this_element).parents('tr').removeClass('ays_read_result');
                                $(document).find('#ays_results_bage').text(
                                    parseInt($(document).find('#ays_results_bage').text())-1
                                );
                            }
                            $(document).find('.ays-modal').aysModal('hide');
                        });
                    }
                },
                error: function(){
                    swal.fire({
                        type: 'info',
                        html: "<h2>"+ quizLangObj.loadResource +"</h2><br><h4>"+ quizLangObj.dataDeleted +"</h4>"
                    }).then(function(res) {
                        $(document).find('div.ays-quiz-preloader').css('display', 'none');
                        if($(this_element).parents('tr').hasClass('ays_read_result')){
                            $(this_element).parents('tr').removeClass('ays_read_result');
                            $(document).find('#ays_results_bage').text(
                                parseInt($(document).find('#ays_results_bage').text())-1
                            );
                        }
                        $(document).find('.ays-modal').aysModal('hide');
                    });
                }
            });
        }
    }
        
    // Quick quiz submit function
    $(document).find('#ays_quick_submit_button').on('click',function (e) {
//        deactivate_questions();
        $(document).find('div.ays-quiz-preloader').css('display', 'flex');
        var questions =  $(document).find('.ays_modal_question');
        if($(e.target).parents('#ays-quick-modal-content').find('#ays-quiz-title').val() == ''){            
            swal.fire({
                type: 'error',
                text: quizLangObj.quizTitleNotEmpty
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        var qqanswers = $(e.target).parents('#ays-quick-modal-content').find('.ays_answer');
        var emptyAnswers = 0;
        for(var j = 0; j < qqanswers.length; j++){
            var parent =  qqanswers.eq(j).parents('.ays_modal_question');
            var questionType = parent.find('.ays_quick_question_type').val();

            if ( questionType == 'text' ) {
                var answerVal = parent.find('textarea.ays-correct-answer-value.ays-text-question-type-value').val();

                if(answerVal == ''){
                    emptyAnswers++;
                    break;
                }
            } else if( questionType == 'short_text' || questionType == 'number' || questionType == 'date' ) {
                var answerVal = parent.find('input.ays-correct-answer-value.ays-text-question-type-value').val();

                if(answerVal == ''){
                    emptyAnswers++;
                    break;
                }
            } else {
                if(qqanswers.eq(j).val() == ''){
                    emptyAnswers++;
                    break;
                }
            }
        }
        if(emptyAnswers > 0){
            swal.fire({
                type: 'error',
                text: quizLangObj.mustFillAllAnswers
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        
        for(var i=0;i<questions.length;i++){
            var question_text = aysEscapeHtml( questions.eq(i).find('.ays_question_input').val() );
            var question_type = questions.eq(i).find('.ays_quick_question_type').val();

            questions.eq(i).find('.ays_question_input').after('<input type="hidden" name="ays_quick_question[]" value="'+question_text+'">');

            if ( question_type == 'text' ) {
                var question_answers = questions.eq(i).find('.ays-correct-answer-value');

                question_answers.append('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+ aysEscapeHtml( question_answers.val() ) +'">');
                question_answers.append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
            } else if( question_type == 'short_text' || question_type == 'number' || question_type == 'date' ){
                var question_answers = questions.eq(i).find('input.ays-correct-answer-value.ays-text-question-type-value');

                question_answers.after('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+ aysEscapeHtml( question_answers.val() ) +'">');
                question_answers.after('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
            } else {
                var question_answers = questions.eq(i).find('.ays_answer');
                var question_answers_correct = questions.eq(i).find('input.ays_answer_unique_id');
                for(var a=0;a<question_answers.length;a++){
                    question_answers.eq(a).after('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+question_answers.eq(a).val()+'">');
                }
                for(var z=0;z<question_answers_correct.length;z++){
                    if(question_answers_correct.eq(z).prop('checked')){
                        question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
                    }else{
                        question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="false">');
                    }
                }
            }
        }

        var data = $('#ays_quick_popup').serializeFormJSON();
        data.action = 'ays_quick_start';
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                $(document).find('div.ays-quiz-preloader').css('display', 'none');
                if(response.status === true){
                    $(document).find('#ays_quick_popup')[0].reset();
                    $(document).find('#ays-quick-modal .ays-modal-content').addClass('animated bounceOutRight');
                    $(document).find('#ays-quick-modal').modal('hide');
                    swal({
                        title: '<strong>'+ quizLangObj.greatJob +'</strong>',
                        type: 'success',
                        html: '<p>'+ quizLangObj.useThisShortcode +'</p><input type="text" id="quick_quiz_shortcode" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_quiz id=&quot;' + response.quiz_id + '&quot;]" /><p style="margin-top:1rem;">'+ quizLangObj.advancedconfiguration +' <a href="admin.php?page=quiz-maker&action=edit&quiz=' + response.quiz_id + '">'+ quizLangObj.editQuizPage +'</a></p>',
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonText: '<i class="ays_fa ays_fa_thumbs_up"></i> '+ quizLangObj.great,
                        confirmButtonAriaLabel: quizLangObj.thumbsUpGreat,
                        onAfterClose: function() {
                            $(document).find('#ays-quick-modal').removeClass('animated bounceOutRight');
                            $(document).find('#ays-quick-modal').css('display', 'none');
                            window.location.href = "admin.php?page=quiz-maker";
                        }
                    });
                    var modalQuestion = $('.ays_modal_element.ays_modal_question');
                    modalQuestion.each(function(){
                        if($('.ays_modal_element.ays_modal_question').length !== 1){
                            $(this).remove();
                        }
                    });
                }
            }
        });
    });

    function deactivate_questions() {
        if ($('.active_question').length !== 0) {
            var question = $('.active_question').eq(0);
            if(!$(question).find('input[name^="ays_answer_radio"]:checked').length){
                $(question).find('input[name^="ays_answer_radio"]').eq(0).attr('checked',true)
            }
            $(question).find('.ays_add_answer').parents().eq(1).addClass('show_add_answer');
            $(question).find('.fa.fa-times').parent().removeClass('active_remove_answer').addClass('show_remove_answer');

            var question_text = $(question).find('.ays_question_input').val();
            $(question).find('.ays_question_input').remove();
            $(question).prepend('<p class="ays_question">' + question_text + '</p>');
            var answers_tr = $(question).find('.ays_answers_table tr');
            for (var i = 0; i < answers_tr.length; i++) {
                var answer_text = ($(answers_tr.eq(i)).find('.ays_answer').val()) ? $(answers_tr.eq(i)).find('.ays_answer').val() : '';
                $(answers_tr.eq(i)).find('.ays_answer_td').empty();
                var answer_html = '<p class="ays_answer">' + answer_text + '</p>'+((answer_text == '')?'<p>Answer</p>':'');
                $(answers_tr.eq(i)).find('.ays_answer_td').append(answer_html)
            }
            $('.active_question').find('.ays_question_overlay').removeClass('display_none');
            $('.active_question').removeClass('active_question');
        }
    }
    
    // Zapier test data send
    $("#testZapier").on('click', function () {
        var AysQuiz = {};
        var $this = $(this);
        $this.prop('disabled', true);
        $("#testZapierFields").find("input").each(function () {
            if ($(this).prop('checked')) {
                switch ($(this).val()) {
                    case "ays_user_name":
                        AysQuiz[$(this).data('name')] = "John Smith";
                        break;
                    case "ays_user_email":
                        AysQuiz[$(this).data('name')] = "john_smith@example.com";
                        break;
                    case "ays_user_phone":
                        AysQuiz[$(this).data('name')] = "+123123123";
                        break;
                    default:
                        AysQuiz[$(this).data('name')] = "Test data";
                        break;
                }
            }
        });
        $.post({
            url: $this.attr('data-url'),
            dataType: 'json',
            data: {
                "AysQuiz": JSON.stringify(AysQuiz)
            },
            success: function (response) {
                $this.prop('disabled', false);
                if (response.status) {
                    $this.removeClass('btn-outline-secondary').addClass('btn-success').text('Successfully sent')
                } else {
                    $this.removeClass('btn-outline-secondary').addClass('btn-danger').text('Failed')
                }
            },
            error: function () {
                $this.prop('disabled', false).removeClass('btn-outline-secondary').addClass('btn-danger').text('Failed')
            }
        });
    })

    //Slack Integration
    $(document).on('click', '#slackOAuthGetToken', function () {
        var clientId = $("#ays_slack_client").val(),
            clientSecret = $("#ays_slack_secret").val(),
            clientCode = $(this).attr('data-code'),
            successText = $(this).attr('data-success');
        if (clientId == '' || clientSecret == "" || clientCode == "") {
            return false;
        }
        $('#ays_submit').prop('disabled', true);
        $.post({
            url: "https://slack.com/api/oauth.access",
            data: {
                client_id: clientId,
                client_secret: clientSecret,
                code: clientCode
            },
            success: function (res) {
                $('#slackOAuthGetToken')
                    .text(successText)
                    .toggleClass('btn-secondary btn-success pointer-events-none');
                $('#ays_slack_token').val(res.access_token);
                $('#ays_submit').prop('disabled', false);
            }
        });
    });
    
    //Test Email send (AV)
    $(document).find('.ays_test_mail_btn').on('click', function(e) {
        var form = $(document).find('#ays-quiz-category-form');
        var del_message = $(document).find('#ays_test_delivered_message');
        var loader = '<img src="'+ del_message.data('src') +'">';
        del_message.html(loader);
        del_message.show();
        var data = form.serializeFormJSON();
        var action = 'ays_send_testing_mail';
        data.action = action;
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.status) {
                    if(response.mail){
                        del_message.css("color", "green");
                    }else{
                        del_message.css("color", "red");
                    }
                    del_message.html(response.message);
                }else{
                    del_message.html(response.message);
                    del_message.css("color", "red");
                }
                setTimeout(function(){
                    del_message.fadeOut(500);
                }, 1500);
            }
        });
    });

    window.onload = function(){
        $(document).find('a[href="#tab2"]').on('click', function(){
            aysQuizDescriptionLivePreview();
        });

        if($(document).find('.nav-tab.nav-tab-active').data('tab') == 'tab2'){
            aysQuizDescriptionLivePreview();
        }

        function aysQuizDescriptionLivePreview() {
            var emptySubtitle;
            if ($(document).find("#wp-ays-quiz-description-wrap").hasClass("tmce-active")){
                $(document).find("#wp-ays-quiz-description-wrap").addClass("html-active").removeClass("tmce-active");
                emptySubtitle = $(document).find('#ays-quiz-description').val();
                emptySubtitle = window.tinyMCE.get('ays-quiz-description').getContent();
                $(document).find("#wp-ays-quiz-description-wrap").addClass("tmce-active").removeClass("html-active");
            }else{
                emptySubtitle = $(document).find('#ays-quiz-description').val();
            }

            var action = 'ays_live_preivew_content';
            var data = {};
            data.action = action;
            data.content = emptySubtitle;
            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response) {
                    if (response.status) {
                        $(document).find('.ays-quiz-live-subtitle').html(response.content);
                    }
                }
            });
        }
    }

    //Access Only selected users (AV)
    $(document).find('#ays_quiz_users_sel').select2({
        allowClear: true,
        placeholder: 'Select users',
        minimumInputLength: 1,
        ajax: {
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: function (params) {
                var checkedUsers = $(document).find('#ays_quiz_users_sel').val();
                return {
                    action: 'ays_quiz_users_search',
                    q: params.term,
                    val: checkedUsers,
                    page: params.page
                };
            },
        }
    });

    //Access Only selected users (AV)
    $(document).find('select.interval_wproduct').select2({
        allowClear: false,
        placeholder: 'Select products',
        minimumInputLength: 1,
        ajax: {
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var checkedProducts = $(document).find('#ays_woo_selected_prods').val();
                var checkedArray = [];
                if(checkedProducts != ""){
                    checkedArray = checkedProducts.split(',');
                }
                return {
                    action: 'ays_get_woocommerce_products',
                    q: params.term,
                    prods: checkedArray,
                    page: params.page
                };
            },
        }
    });

    $(document).find('.ays-search-users-select').select2({
        placeholder: 'Select users',
        minimumInputLength: 1,
        allowClear: true,
        language: {
            // You can find all of the options in the language files provided in the
            // build. They all must be functions that return the string that should be
            // displayed.
            searching: function() {
                return quizLangObj.searching;
            },
            inputTooShort: function () {
                return quizLangObj.pleaseEnterMore;
            }
        },
        ajax: {
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: function (response) {
                var checkedUsers = $(document).find('.ays-search-users-select').val();
                return {
                    action: 'ays_quiz_reports_user_search',
                    search: response.term,
                    val: checkedUsers,
                };
            },
        }
    });

    $("#googleOAuth2").on('click', function (e) {
        e.preventDefault();

        var gRedirectUri = $(document).find("#ays_google_redirect").val();
        var gClientId = $(document).find("#ays_google_client").val();
        var gClientSecret = $(document).find("#ays_google_secret").val();

        if(gClientId && gClientSecret){
            $(this).parents('form').append('<input type="hidden" name="ays_googleOAuth2" value="ays_googleOAuth2">');
            $(this).parents('form').get(0).submit();
        }else{
            return false;
        }
    });

    //Google sheet Integration
    $(document).on('click', '#ays_quiz_get_token', function () {
        var $this = $(this);
        var gClientId = $("#ays_google_client").val(),
            gClientSecret = $("#ays_google_secret").val(),
            gCallBackUrl = $("#ays_google_redirect").val(),
            gCode = $("#ays_google_token").val();
        if (gClientId == '' || gClientSecret == "" || gCallBackUrl == "") {
            return false;
        }
        $.ajax({
            type: 'post',
            url: "https://accounts.google.com/o/oauth2/token",
            contentType: 'application/x-www-form-urlencoded; charset=utf-8',
            data: {
                grant_type: 'authorization_code',
                client_id: gClientId,
                code: gCode,
                client_secret: gClientSecret,
                redirect_uri: gCallBackUrl,
                scope: 'https://www.googleapis.com/auth/spreadsheets'
            },
            success: function (res) {
                $('#ays_get_token').val(res.access_token);
                $('#ays_get_refresh_token').val(res.refresh_token);
                $this.parent().append("<span style='margin-left: 15px;'>Token Given</span>");
                $this.prop("disabled" , true);
                $(document).find("#ays_quiz_get_token").prop("disabled" , true);
            }
        });
    });

    $(document).find('.ays_generate_cert_preview').on('click', function(e) {
        var $this = $(this);
        var form = $(document).find('#ays-quiz-category-form');
        var del_message = $(document).find('#ays_test_delivered_message');
        var previewWrap = $(document).find('.ays_generate_cert_preview_wrap');
        var openWrap = $(document).find('.ays_generate_cert_preview_open');
        var buttonWrap = $(document).find('.ays_generate_cert_preview_button_wrap');
        var loader = '<img src="'+ del_message.data('src') +'">';
        buttonWrap.append(loader);
        buttonWrap.show();
        $this.attr('disabled', 'disabled');
        var data = form.serializeFormJSON();
        var certTitle, certBody;
        if ($(document).find("#wp-ays_certificate_title-wrap").hasClass("tmce-active")){
            $(document).find("#wp-ays_certificate_title-wrap").addClass("html-active").removeClass("tmce-active");
            certTitle = $(document).find('#ays_certificate_title').val();
            certTitle = window.tinyMCE.get('ays_certificate_title').getContent();
            $(document).find("#wp-ays_certificate_title-wrap").addClass("tmce-active").removeClass("html-active");
        }else{
            certTitle = $(document).find('#ays_certificate_title').val();
        }
        if ($(document).find("#wp-ays_certificate_body-wrap").hasClass("tmce-active")){
            $(document).find("#wp-ays_certificate_body-wrap").addClass("html-active").removeClass("tmce-active");
            certBody = $(document).find('#ays_certificate_body').val();
            certBody = window.tinyMCE.get('ays_certificate_body').getContent();
            $(document).find("#wp-ays_certificate_body-wrap").addClass("tmce-active").removeClass("html-active");
        }else{
            certBody = $(document).find('#ays_certificate_body').val();
        }

        data.ays_certificate_title = certTitle;
        data.ays_certificate_body = certBody;

        var action = 'ays_generate_cert_preview';
        data.action = action;
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function(response) {
                buttonWrap.find('img').remove();
                if (response.status) {
                    openWrap.find('a').remove();
                    var a = "<a class='button-primary' href='"+ response.certUrl +"' target='_blank'>" + response.open + "</a>";
                    openWrap.append(a);
                    setTimeout(function(){
                        openWrap.find('a').remove();
                    }, 60 * 1000);
                }else{
                    var a = "<span style='color:red;display:none;'>"+ response.fail +"</span>";
                    openWrap.append(a);
                    openWrap.find('span').fadeIn(500);

                    setTimeout(function(){
                        openWrap.find('span').fadeOut(500);
                    }, 4000);

                    setTimeout(function(){
                        openWrap.find('span').remove();
                    }, 5000);
                }
                setTimeout(function(){
                    $this.removeAttr('disabled');
                }, 1500);
            }
        });
    });

    //Import Coupons
    $(document).on('click', '.ays-quiz-coupon-csv-import-action', function(e){
        e.preventDefault();

        var $_this = $(this);

        var formData = new FormData();
        var couponData = $('#ays_quiz_coupon_csv_import_file').prop('files')[0];
        var action = 'ays_generate_coupons';
        if(typeof(couponData) != "undefined"){
            formData.append('coupon_data', couponData);
            formData.append('action', action);
        }

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status) {
                    var content = '';
                    var couponsArray = response.coupons_ready_data;

                    for (var index = 1; index < couponsArray.length; index++) {
                        content += '<li class="ays-quiz-copy-active-coupon">';
                            content += couponsArray[index];
                            content += '<input type="hidden" value="'+couponsArray[index]+'" name="ays_quiz_coupons_active[]"/>';
                            content += '<a class="ays-quiz-copy-active-coupon">'
                            content +='</a>';
                        content += '</li>';
                    }

                    var coupon_active_list = $(document).find('#ays_quiz_coupons_active');
                        coupon_active_list.append(content);
                }
            }
        });
    });

    // Admin Notes
    $(document).on('click', '.ays-quiz-admin-note-save > button.ays-quiz-save-note', function(){
        var $this = $(this);

        var noteTextTexrarea = $this.parents('.ays-quiz-admin-note-textarea').find('div.ays-quiz-admin-note-text > textarea');
        var admiNoteTd = $this.parents('tr.ays_result_element').parent().find("td.ays_quiz_admin_note_td");
        var preloader = $this.parents('div.ays-quiz-admin-note').find('div.ays-quiz-preloader-note');

        var noteText = noteTextTexrarea.val();
        var resultID = noteTextTexrarea.attr('data-result') ;

        var action = 'get_admin_notes';
        var data = {};

        preloader.css( "display", "flex" );

        data.action = action;
        data.note_text = noteText;
        data.result_id = resultID;
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function(response) {
                if (response.status) {
                    preloader.css( "display", "none" );
                    $this.parents('div.ays-quiz-admin-note-textarea').hide(250);
                    if( response.result_id && response.result_id != '' ){
                        $(document).find('.ays-admin-note-text-list-table-' + response.result_id).html(response.note_text);
                    }
                    admiNoteTd.html(response.note_text);
                }else{
                    swal.fire({
                        type: 'info',
                        html: "<h6>"+ response.message +"</h6>"
                    }).then(function(res){
                        preloader.css('display', 'none');
                    });
                }
            }
        });

    });

    $(document).on('click', '.ays-quiz-update-database', function(e){
        e.preventDefault();

        var _this = $(this);
        var message = _this.data('message');
        var confirm = window.confirm(message);
        if(confirm === true){
            var action = 'ays_quiz_update_database_tables';
            var data = {};
            data.action = action;
            data.status = true;
            $.ajax({
                url: quiz_maker_ajax.ajax_url,
                method: 'post',
                dataType: 'json',
                data: data,
                success: function(response) {
                    if (response.status) {
                        window.location.reload();
                    }else{
                        swal.fire({
                            type: 'info',
                            html: "<h6>"+ response.message +"</h6>"
                        });
                    }
                }
            });
        }
    });

    $(document).find('#ays_quiz_create_author').select2({
        placeholder: quiz_maker_ajax.selectUser,
        minimumInputLength: 1,
        allowClear: true,
        language: {
            // You can find all of the options in the language files provided in the
            // build. They all must be functions that return the string that should be
            // displayed.
            searching: function() {
                return quiz_maker_ajax.searching;
            },
            inputTooShort: function () {
                return quiz_maker_ajax.pleaseEnterMore;
            }
        },
        ajax: {
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: function (response) {
                var checkedUsers = $(document).find('#ays_quiz_create_author').val();
                return {
                    action: 'ays_quiz_author_user_search',
                    search: response.term,
                    val: checkedUsers,
                };
            },
        }
    });

    //Allow selected users exporting quizzes
    $(document).find('#ays_quiz_users_to_export').select2({
        allowClear: true,
        placeholder: quiz_maker_ajax.selectUser,
        minimumInputLength: 1,
        language: {
            // You can find all of the options in the language files provided in the
            // build. They all must be functions that return the string that should be
            // displayed.
            searching: function() {
                return quiz_maker_ajax.searching;
            },
            inputTooShort: function () {
                return quiz_maker_ajax.pleaseEnterMore;
            }
        },
        ajax: {
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: function (params) {
                var checkedUsers = $(document).find('#ays_quiz_users_to_export').val();
                return {
                    action: 'ays_quiz_users_search',
                    q: params.term,
                    val: checkedUsers,
                    page: params.page
                };
            },
        }
    });

    $(document).on("click", ".ays-quiz-cards-block .ays-quiz-card__footer button.status-missing", function(e){
        var $this = $(this);
        var thisParent = $this.parents(".ays-quiz-cards-block");

        $this.prop('disabled', true);
        $this.addClass('disabled');

        var loader_html = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';

        $this.html(loader_html);

        var attr_plugin = $this.attr('data-plugin');
        var wp_nonce = thisParent.find('#ays_quiz_ajax_install_plugin_nonce').val();

        var data = {
            action: 'ays_quiz_install_plugin',
            _ajax_nonce: wp_nonce,
            plugin: attr_plugin,
            type: 'plugin'
        };

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                if (response.success) {
                    swal.fire({
                        type: 'success',
                        html: "<h4>"+ response['data']['msg'] +"</h4>"
                    }).then( function(res) {
                        if ( $this.hasClass('status-missing') ) {
                            $this.removeClass('status-missing');
                        }
                        $this.text(quiz_maker_ajax.activated);
                        $this.addClass('status-active');
                    });
                }
                else {
                    swal.fire({
                        type: 'info',
                        html: "<h4>"+ response['data'][0]['message'] +"</h4>"
                    }).then( function(res) {
                        $this.text(quiz_maker_ajax.errorMsg);
                    });
                }
            },
            error: function(){
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quiz_maker_ajax.loadResource +"</h2><br><h6>"+ quiz_maker_ajax.somethingWentWrong +"</h6>"
                }).then( function(res) {
                    $this.text(quiz_maker_ajax.errorMsg);
                });
                // $this.prop('disabled', false);
                // if ( $this.hasClass('disabled') ) {
                //     $this.removeClass('disabled');
                // }
            }
        });
    });

    $(document).on("click", ".ays-quiz-cards-block .ays-quiz-card__footer button.status-installed", function(e){
        var $this = $(this);
        var thisParent = $this.parents(".ays-quiz-cards-block");

        $this.prop('disabled', true);
        $this.addClass('disabled');

        var loader_html = '<i class="fa fa-spinner fa-spin" aria-hidden="true"></i>';

        $this.html(loader_html);

        var attr_plugin = $this.attr('data-plugin');
        var wp_nonce = thisParent.find('#ays_quiz_ajax_install_plugin_nonce').val();

        var data = {
            action: 'ays_quiz_activate_plugin',
            _ajax_nonce: wp_nonce,
            plugin: attr_plugin,
            type: 'plugin'
        };

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                if( response.success ){
                    swal.fire({
                        type: 'success',
                        html: "<h4>"+ response['data'] +"</h4>"
                    }).then( function(res) {
                        if ( $this.hasClass('status-installed') ) {
                            $this.removeClass('status-installed');
                        }
                        $this.text(quiz_maker_ajax.activated);
                        $this.addClass('status-active disabled');
                    });
                } else {
                    swal.fire({
                        type: 'info',
                        html: "<h4>"+ response['data'][0]['message'] +"</h4>"
                    });
                }
            },
            error: function(){
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quiz_maker_ajax.loadResource +"</h2><br><h6>"+ quiz_maker_ajax.somethingWentWrong +"</h6>"
                }).then( function(res) {
                    $this.text(quiz_maker_ajax.errorMsg);
                });;
                // $this.prop('disabled', false);
                // if ( $this.hasClass('disabled') ) {
                //     $this.removeClass('disabled');
                // }
            }
        });
    });

    //Import Passwords | CSV | TXT
    $(document).on('click', '.ays-quiz-password-csv-txt-import-action', function(e){
        e.preventDefault();

        var formData = new FormData();
        var passwordData = $(document).find('#ays_quiz_password_csv_txt_import_file').prop('files')[0];
        var errorMsgBox = $(document).find('.ays-quiz-password-csv-txt-import-error-message');
        var action = 'ays_generate_passwords_via_import';
        if(typeof(passwordData) != "undefined"){
            formData.append('password_data', passwordData);
            formData.append('action', action);
        }

        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.status) {
                    var content = '';
                    var passwordsArray = response.passwords_ready_data;

                    for (var index = 0; index < passwordsArray.length; index++) {
                        content += '<li>';
                            content += '<span class="created_psw">'+ passwordsArray[index] +'</span>';
                            content += '<a class="ays_gen_psw_move_to_used"><i class="fa fa-clipboard" aria-hidden="true"></i></a>';
                            content += '<input type="hidden" value="'+passwordsArray[index]+'" name="ays_active_gen_psw[]" class="ays_active_gen_psw"/>';
                        content += '</li>';
                    }

                    var coupon_active_list = $(document).find('#ays_generated_password ul.ays_active');
                        coupon_active_list.append(content);
                } else {
                    errorMsgBox.text(response.message);
                    setTimeout( function(){
                        errorMsgBox.text('');
                    }, 5000);
                }
            },
            error: function(){
                swal.fire({
                    type: 'info',
                    html: "<h2>"+ quizLangObj.somethingWentWrong +"</h2>"
                }).then(function(res) {
                    errorMsgBox.text('');
                });
            }
        });
    });

})( jQuery );


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

    if ( typeof text !== 'undefined' ) {
        return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
    }else{
        return '';
    }
}
