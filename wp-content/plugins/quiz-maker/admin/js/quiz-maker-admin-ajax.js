(function( $ ) {
    'use strict';
    $.fn.serializeFormJSON = function () {
        let o = {},
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
        
        if(window.aysQuestNewSelected.length > 0){
            $(document).find('td.empty_quiz_td').parent().remove();
            let data = $(this).serializeFormJSON();
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
                        let table = $('table#ays-questions-table tbody'),
                            id_container = $(document).find('input#ays_already_added_questions'),
                            existing_ids = ( id_container.val().split(',')[0] === "" ) ? [] : id_container.val().split(','),
                            new_ids = [];
                        for(let i = 0; i < response.ids.length; i++) {
                            if( $.inArray( response.ids[i], existing_ids ) === -1 ) {
                                new_ids.push(response.ids[i]);
                                table.append(response.rows[i]);
                                let table_rows = $('table#ays-questions-table tbody tr'),
                                    table_rows_length = table_rows.length;
                                if( table_rows_length % 2 === 0 ) {
                                    table_rows.eq( ( table_rows_length - 1 ) ).addClass('even');
                                }
                            }else{
                                let position = $.inArray( response.ids[i], existing_ids );
                            }
                        }
                        let table_rows = $('table#ays-questions-table tbody tr');

                        for(var i = 0; i < new_ids.length; i++){
                            existing_ids.push(new_ids[i]);
                        }

                        table_rows.each(function(){
                            let id = $(this).data('id');
                            if($.inArray( id.toString(), existing_ids ) === -1){
                                $(this).remove();
                            }
                        });
                        id_container.val( existing_ids );
                    }
                    $(document).find('.ays-modal').aysModal('hide');
                    let questions_count = response.ids.length;

                    let table_rows = $('table#ays-questions-table tbody tr');
                    $(document).find('.questions_count_number').html(table_rows.length);

                    $(document).find('.ays_refresh_qbank_categories').removeClass('display_none');
                    window.aysQuestNewSelected = [];
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
        let quiz_id = $(document).find('#quiz_stat_select').val();
        let action = 'get_current_quiz_statistic';
        let $this = $(this);
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
        let $this = $('#export-filters');
        let action = 'ays_results_export_filter';
        let user_id = $('#user_id-filter').val();
        let quiz_id = $('#quiz_id-filter').val();
        let date_from = $('#start-date-filter').val() || $('#start-date-filter').attr('min');
        let date_to = $('#end-date-filter').val() || $('#end-date-filter').attr('max');
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
                date_to: date_to
            },
            success: function(response) {
                $this.find('div.ays-quiz-preloader').css('display', 'none');
                $this.find(".export_results_count span").text(response.qanak);
            }
        });
    });
        
    let userSel2, quizSel2;
    
    $(document).find('.ays-export-filters').on('click', function(e) {
        let $this = $('#export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        $this.aysModal('show');
        e.preventDefault();
        let action = 'ays_show_filters';
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: {
                action: action
            },
            success: function(res) {
                $this.find('div.ays-quiz-preloader').css('display', '');
                let newUserSelect = "";
                let newQuizSelect = "";
                for (let u in res.users) {
                    newUserSelect += '<option value="'+ res.users[u].user_id +'">'+ res.users[u].display_name +'</option>';
                }
                for (let q in res.quizzes) {
                    newQuizSelect += '<option value="'+ res.quizzes[q].quiz_id +'">'+ res.quizzes[q].title +'</option>';
                }
                let userSel = $this.find('#user_id-filter').html(newUserSelect);
                userSel2 = userSel.select2({
                    dropdownParent: $this,
                    closeOnSelect: true,
                    allowClear: false
                });
                let quizSel = $this.find('#quiz_id-filter').html(newQuizSelect);
                quizSel2 = quizSel.select2({
                    dropdownParent: $this,
                    closeOnSelect: true,
                    allowClear: false
                });
                $this.find('input[type="date"]').attr({
                    min: res.date_min,
                    max: res.date_max
                });
                
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
                    html: "<h2>Can't load resource.</h2><br><h6>Maybe something went wrong.</h6>"
                }).then(function(res){
                    $(document).find('#export-filters div.ays-quiz-preloader').css('display', 'none');                    
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

    $(document).find('.export-action').on('click', function(e) {
        e.preventDefault();
        let $this = $('#export-filters');
        $this.find('div.ays-quiz-preloader').css('display', 'flex');
        let action = 'ays_results_export_file';
        let user_id = $('#user_id-filter').val();
        let quiz_id = $('#quiz_id-filter').val();
        let type = $(this).data('type');
        let date_from = $('#start-date-filter').val() || $('#start-date-filter').attr('min');
        let date_to = $('#end-date-filter').val() || $('#end-date-filter').attr('max');
        $.post({
            url: quiz_maker_ajax.ajax_url,
            dataType: 'json',
            data: { 
                action: action, 
                type: type, 
                user_id: user_id, 
                quiz_id: quiz_id, 
                date_from: date_from, 
                date_to: date_to 
            },
            success: function(response) {
                if (response.status) {
                    switch (response.type) {
//                        case 'xls':
//                            xlsExporter(jsonToSsXml(response.data, response.fields), 'quiz_questions_export', 'xls');
//                            break;
                        case 'xlsx':
                            var options = {
                                fileName: "quiz_questions_export",
                                header: true
                            };
                            var tableData = [{
                                "sheetName": "Quiz results",
                                "data": response.data
                            }];
                            Jhxlsx.export(tableData, options);
                            break;
                        case 'csv':
                            let csvOptions = {
                                separator: ',',
                                fileName: 'quiz_results_export.csv'
                            };
                            let x = new CSVExport(response.data, response.fileFields, csvOptions);
                            break;
                        case 'json':
                            let text = JSON.stringify(response.data);
                            let data = new Blob([text], {type: "application/" + response.type});
                            let fileUrl = window.URL.createObjectURL(data);
                            $('#downloadFile').attr({
                                'href': fileUrl,
                                'download': "quiz_questions_export." + response.type,
                            })[0].click();
                            window.URL.revokeObjectURL(fileUrl);
                            break;
                        default:
                            break;
                    }
//                    objectExporter({
//                        exportable: response.data,
//                        type: 'csv',
//                        headers: response.fileFields,
//                        fileName: 'quiz_results_export'
//                    });
                }
                $this.find('div.ays-quiz-preloader').css('display', 'none');
            }
        });
    });
    
    // Export questions filters
    
    $(document).find('.ays-questions-export').on('click', function (e) {
        $('.ays-export-dropdown .ays-wp-loading').removeClass('d-none');
        $('.ays-export-dropdown>.dropdown-toggle').addClass('disabled');
        let type = $(this).attr('data-type');
        let action = 'ays_questions_export';
        
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action,
                type: type
            },
            success: function (response) {
                if (response.status) {
                    switch (response.type) {
//                        case 'xls':
//                            xlsExporter(jsonToSsXml(response.data, response.fields), 'quiz_questions_export', 'xls');
//                            break;
                        case 'xlsx':
                            var options = {
                                fileName: "quiz_questions_export",
                                header: true
                            };
                            var tableData = [{
                                "sheetName": "Quiz questions",
                                "data": response.data
                            }];
                            Jhxlsx.export(tableData, options);
                            break;
                        case 'csv':
                            let csvOptions = {
                                separator: ','
                            };
                            let x = new CSVExport(response.data, response.fields, csvOptions);
                            break;
                        case 'json':
                            let text = JSON.stringify(response.data);
                            let data = new Blob([text], {type: "application/" + response.type});
                            let fileUrl = window.URL.createObjectURL(data);
                            $('#downloadFile').attr({
                                'href': fileUrl,
                                'download': "quiz_questions_export." + response.type,
                            })[0].click();
                            window.URL.revokeObjectURL(fileUrl);
                            break;
                        default:
                            break;
                    }
                }
                $('.ays-export-dropdown .ays-wp-loading').addClass('d-none');
                $('.ays-export-dropdown>.dropdown-toggle').removeClass('disabled');
            }
        });
        e.preventDefault();
    });
    
    $(document).find('.ays_questions_export').on('click', function(e){
        let action = 'ays_questions_export_file';
        $.ajax({
            url: quiz_maker_ajax.ajax_url,
            method: 'post',
            dataType: 'json',
            data: {
                action: action
            },
            success: function(response){
                if(response){
                    objectExporter({
                        exportable: response.data,
                        type: 'csv',
                        fileName: 'quiz_questions_export',
                        headers: response.fields,
                    });
                }
            }
        });
        e.preventDefault();
    });
    
    // Open results more information popup window
    $(document).on('click', '.ays_quiz_read_result', function(e){
        var where = 'row';
        ays_show_results(e, $(this).find('.ays-show-results').eq(0), where);
    });
    
//    $(document).find('.ays-show-results').on('click', function(e){
//        var where = 'element';
//        ays_show_results(e, $(this), where)
//    });
    
    function ays_show_results(e, this_element, where){
        if($(e.target).hasClass('ays_confirm_del') || $(e.target).hasClass('ays_result_delete')){
            
        }else{
            e.preventDefault();
            $(document).find('div.ays-quiz-preloader').css('display', 'flex');
            $(document).find('#ays-results-modal').aysModal('show');
            let result_id = this_element.data('result');
            let action = 'ays_show_results';
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
                            html: "<h2>Can't load resource.</h2><br><h4>Maybe the data has been deleted.</h4>",

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
                        html: "<h2>Can't load resource.</h2><br><h6>Maybe the data has been deleted.</h46>"
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
        deactivate_questions();
        $(document).find('div.ays-quiz-preloader').css('display', 'flex');
        var questions =  $(document).find('.ays_modal_question');
        if($(e.target).parents('#ays-quick-modal-content').find('#ays-quiz-title').val() == ''){            
            swal.fire({
                type: 'error',
                text: "Quiz title can't be empty"
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        let qqanswers = $(e.target).parents('#ays-quick-modal-content').find('.ays_answer');
        let emptyAnswers = 0;
        for(let j = 0; j < qqanswers.length; j++){
            if(qqanswers.eq(j).text() == ''){
                emptyAnswers++;
                break;
            }
        }
        if(emptyAnswers > 0){
            swal.fire({
                type: 'error',
                text: "You must fill all answers"
            });
            $(document).find('div.ays-quiz-preloader').css('display', 'none');
            return false;
        }
        
        for(var i=0;i<questions.length;i++){
            var question_text = questions.eq(i).find('.ays_question').text();
            questions.eq(i).find('.ays_question').after('<input type="hidden" name="ays_quick_question[]" value="'+question_text+'">')
            var question_answers = questions.eq(i).find('.ays_answer');
            var question_answers_correct = questions.eq(i).find('input[type="radio"]');
            for(var a=0;a<question_answers.length;a++){
                question_answers.eq(a).after('<input type="hidden" name="ays_quick_answer['+i+'][]" value="'+question_answers.eq(a).text()+'">');
            }
            for(var z=0;z<question_answers_correct.length;z++){
                if(question_answers_correct.eq(z).prop('checked')){
                    question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="true">');
                }else{
                    question_answers_correct.eq(z).parents().eq(0).append('<input type="hidden" name="ays_quick_answer_correct['+i+'][]" value="false">');
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
                        title: '<strong>Great job</strong>',
                        type: 'success',
                        html: '<p>You can use this shortcode to show your quiz.</p><input type="text" id="quick_quiz_shortcode" onClick="this.setSelectionRange(0, this.value.length)" readonly value="[ays_quiz id=&quot;' + response.quiz_id + '&quot;]" /><p style="margin-top:1rem;">For more detailed configuration visit <a href="admin.php?page=quiz-maker&action=edit&quiz=' + response.quiz_id + '">edit quiz page</a>.</p>',
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonText:
                          '<i class="ays_fa ays_fa_thumbs_up"></i> Great!',
                        confirmButtonAriaLabel: 'Thumbs up, great!',
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
                let answer_html = '<p class="ays_answer">' + answer_text + '</p>'+((answer_text == '')?'<p>Answer</p>':'');
                $(answers_tr.eq(i)).find('.ays_answer_td').append(answer_html)
            }
            $('.active_question').find('.ays_question_overlay').removeClass('display_none');
            $('.active_question').removeClass('active_question');
        }
    }
    
    // Zapier test data send
    $("#testZapier").on('click', function () {
        let AysQuiz = {};
        let $this = $(this);
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
        let clientId = $("#ays_slack_client").val(),
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
    return text.replace(/[&<>\"']/g, function(m) { return map[m]; });
}
