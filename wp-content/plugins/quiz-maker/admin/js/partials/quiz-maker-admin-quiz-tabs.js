(function($){
    $(document).ready(function(){        

        var emptyArr = new Array();
        var changeDataTableDefaultValue = new Array( 0, "desc");
        emptyArr.push(changeDataTableDefaultValue);     
        $.extend(true, $.fn.dataTable.defaults, {
            aaSorting: emptyArr,
        });

        var questCategoryFilter = $(document).find('#add_quest_category_filter').select2({
            placeholder: quizLangDataTableObj.selectCategory,
//            dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter'),
            multiple: true,
            matcher: searchForPage,
            dropdownParent: $(document).find('#quest_cat_container')
        });
        var questTagFilter = $(document).find('#add_quest_tag_filter').select2({
            placeholder: quizLangDataTableObj.selectTags,
            //dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter'),
            multiple: true,
            matcher: searchForPage,
            dropdownParent: $(document).find('#quest_tag_container')
        });
        
        $(document).find('.ays_filter_cat_clear').on('click', function(){
            questCategoryFilter.val(null).trigger('change');
            qatable.draw();
        });
        $(document).find('.ays_filter_tag_clear').on('click', function(){
            questTagFilter.val(null).trigger('change');
            qatable.draw();
        });

        window.aysQuestSelected = [];
        window.aysQuestNewSelected = [];
        // var selectedRows = $(document).find('#ays-questions-table tbody tr.ays-question-selected');
        // for(var i=0; i < selectedRows.length; i++){
        //     window.aysQuestSelected.push(selectedRows.eq(i).data('id'));
        // }

        var questionIdsVal = $(document).find('#ays_already_added_questions').val();
        if( typeof questionIdsVal != "undefined" && questionIdsVal != "" ){
            var questionIds = questionIdsVal.split(',');
            var questionIds_new = new Array();

            for (var i = 0; i < questionIds.length; i++) {
                questionIds_new.push( parseInt( questionIds[i] ) );
            }
            window.aysQuestSelected = questionIds_new;
        }

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                let questionCat = $(document).find('.cat_filter').val();
                if(questionCat == null || questionCat.length == 0){
                    return true;
                }
                for(var i=0; i<questionCat.length; i++){
                    if (data[4] == questionCat[i]){
                        return true;
                    }
                }
                return false;
            }
        );

        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                var questionTag = $(document).find('.tag_filter').val();

                if(questionTag == null || questionTag == 0){
                    return true;
                }

                var splitData = data[5].split(", ");
                for(var j=0; j<questionTag.length; j++){
                    if(splitData.includes(questionTag[j])){
                        return true;
                    }
                }

                return false;
            }
        );

        var qatable = $('#ays-question-table-add').DataTable({
            paging: 5,
            "processing": true, 
            "serverSide": true,
            "responsive": true,
            "bDestroy": true,
            "ajax": {
                "url": quiz_maker_ajax.ajax_url,
                "method": "POST",
                "data": function ( d ) {
                    d.action = 'get_published_questions_ajax';
                    var quizId = $(document).find('#ays-question-table-add').data('quizId');
                    d.quiz_id = quizId;
                    var catFilter = $(document).find('.cat_filter').val();
                    if(catFilter != null){
                        d.cats = catFilter;
                    }
                    var tagFilter = $(document).find('.tag_filter').val();
                    if(tagFilter != null){
                        d.tags = tagFilter;
                    }
                },
            },
            columns: [{ 
                data: "first_column"
            },{ 
                data: "question",
                className: "ays-modal-td-question"
            },{ 
                data: "type"
            },{ 
                data: "create_date"
            },{ 
                data: "title",
                className: "ays-modal-td-category" 
            },{ 
                data: "tag_data",
                className: "ays-modal-td-tag" 
            },{ 
                data: "used",
                className: "ays-modal-td-used" 
            },{ 
                data: "id"
            }],
            "lengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, quizLangDataTableObj.all]],
            "language": {
                "sEmptyTable":     quizLangDataTableObj.sEmptyTable,
                "sInfo":           quizLangDataTableObj.sInfo,
                "sInfoEmpty":      quizLangDataTableObj.sInfoEmpty,
                "sInfoFiltered":   quizLangDataTableObj.sInfoFiltered,
                "sInfoPostFix":    "",
                "sInfoThousands":  ",",
                "sLengthMenu":     quizLangDataTableObj.sLengthMenu,
                "sLoadingRecords": quizLangDataTableObj.sLoadingRecords,
                "sProcessing":     quizLangDataTableObj.sProcessing,
                "sSearch":         quizLangDataTableObj.sSearch,
                "sUrl":            "",
                "sZeroRecords":    quizLangDataTableObj.sZeroRecords,
                "oPaginate": {
                    "sFirst":    quizLangDataTableObj.sFirst,
                    "sLast":     quizLangDataTableObj.sLast,
                    "sNext":     quizLangDataTableObj.sNext,
                    "sPrevious": quizLangDataTableObj.sPrevious,
                },
                "oAria": {
                    "sSortAscending":  quizLangDataTableObj.sSortAscending,
                    "sSortDescending": quizLangDataTableObj.sSortDescending
                }
            },
            "infoCallback": function(){
                let qaTableSelectAll =  $(document).find('#ays-question-table-add tbody tr.ays_quest_row');
                let qaTableSelected =  0;
                qaTableSelectAll.each(function(){                    
                    if(!$(this).hasClass('selected')){
                        qaTableSelected++;
                    }
                });
                if(qaTableSelected > 0){
                    if($(document).find('.select_all').hasClass('deselect')){
                        $(document).find('.select_all').removeClass('deselect');
                        $(document).find('.select_all').text(quizLangObj.selectAll);
                    }
                }else{
                    $(document).find('.select_all').addClass('deselect');
                    $(document).find('.select_all').text(quizLangObj.deselectAll);
                }
            },
            "drawCallback": function( settings ) {
                var qaTableRows =  $(document).find('#ays-question-table-add tbody tr.ays_quest_row');
                qaTableRows.each(function(){
                    if($.inArray(parseInt($(this).attr('data-id')), window.aysQuestSelected) == -1){
                        $(this).removeClass('selected');
                        $(this).find('.ays-select-single')
                            .removeClass('ays_fa_check_square_o')
                            .addClass('ays_fa_square_o');
                    } else {
                        $(this).addClass('selected');
                        $(this).find('.ays-select-single')
                            .addClass('ays_fa_check_square_o')
                            .removeClass('ays_fa_square_o');
                    }
                });
            },
            "rowCallback": function( row, data ) {
                $(row).attr('data-id', data['id']);
                $(row).addClass('ays_quest_row');
                if(data['selected'] == 'selected'){
                    $(row).addClass(data['selected']);
                }
            },
            "initComplete": function( settings, json ) {
                let selectedRows = $(document).find('#ays-question-table-add tbody tr.selected');
                for(let i=0; i < selectedRows.length; i++){
                    if($.inArray(selectedRows.eq(i).data('id'), window.aysQuestSelected) == -1){
                        window.aysQuestSelected.push(selectedRows.eq(i).data('id'));
                    }
                }
                var proccessing = "<div class='dataTables_processing_loader'><span class='dtable_loader'><img src='"+json.loader+"'></span><span>"+json.loaderText+"</span></div>";
                $(document).find('.dataTables_processing').html(proccessing);
            }
        });
        
        $(document).find('.cat_filter').on('select2:select', function() {
            qatable.draw();
        });
        $(document).find('.cat_filter').on('select2:unselect', function() {
            qatable.draw();
            questCategoryFilter.select2("close");
        });
        $(document).find('.cat_filter').on('select2:unselecting', function() {
            questCategoryFilter.select2("close");
        });

        $(document).find('.tag_filter').on('select2:select', function() {
            qatable.draw();
        });
        $(document).find('.tag_filter').on('select2:unselect', function() {
            qatable.draw();
            questTagFilter.select2("close");
        });
        $(document).find('.tag_filter').on('select2:unselecting', function() {
            questTagFilter.select2("close");
        });

        $(document).find('#ays-question-table-add_info,#ays-question-table-add_length').append('<button class="button select_all" type="button" style="margin-left:10px;">'+ quizLangObj.selectAll +'</button>');
        $(document).on('click', '.select_all', function(e){
            var $this = $(document).find('.select_all');
            var qaTableSelectAll = $(document).find('#ays-question-table-add tbody tr.ays_quest_row');
            if($this.hasClass('deselect')){
                qaTableSelectAll.each(function(){
                    var id = $(this).data('id');
                    var index = $.inArray(id, window.aysQuestSelected);
                    var indexNew = $.inArray(id, window.aysQuestNewSelected);

                    if($(this).hasClass('selected')){
                        $(this).removeClass('selected');
                        if ( indexNew !== -1 ) {
                            window.aysQuestNewSelected.splice( indexNew, 1 );
                        }
                    }
                    if ( index !== -1 ) {
                        window.aysQuestSelected.splice( index, 1 );
                    }
                    $(this).find('.ays-select-single').removeClass('ays_fa_check_square_o').addClass('ays_fa_square_o');
                });
                $this.removeClass('deselect');
                $this.text(quizLangObj.selectAll);
            }else{
                qaTableSelectAll.each(function(){
                    var id = $(this).data('id');
                    var index = $.inArray(id, window.aysQuestSelected);
                    var indexNew = $.inArray(id, window.aysQuestNewSelected);

                    if(!$(this).hasClass('selected')){
                        $(this).addClass('selected');
                        if ( indexNew === -1 ) {
                            window.aysQuestNewSelected.push( id );
                        }
                    }
                    if ( index === -1 ) {
                        window.aysQuestSelected.push( id );
                    }
                    $(this).find('.ays-select-single').removeClass('ays_fa_square_o').addClass('ays_fa_check_square_o');
                });
                $this.addClass('deselect');
                $this.text(quizLangObj.deselectAll);
            }
        });
        
        $(document).on('click', '#ays-question-table-add tbody tr.ays_quest_row', function(){
            let id = $(this).data('id');
            let index = $.inArray(id, window.aysQuestSelected);
            let index2 = $.inArray(id, window.aysQuestNewSelected);

            if ( index === -1 ) {
                window.aysQuestSelected.push( id );
            } else {
                window.aysQuestSelected.splice( index, 1 );
            }
            if ( index2 === -1 && index === -1 ) {
                window.aysQuestNewSelected.push( id );
            } else {
                window.aysQuestNewSelected.splice( index2, 1 );
            }
            
            if($(this).hasClass('selected')){
                $(this).find('.ays-select-single').removeClass('ays_fa_check_square_o').addClass('ays_fa_square_o');
            }else{
                $(this).find('.ays-select-single').removeClass('ays_fa_square_o').addClass('ays_fa_check_square_o'); 
            }
            $(this).toggleClass('selected');
            
        });
        
        $(document).on('click', '.ays-add-question', function () {
//            let page = 1; // set page 1
//            $('ul.ays-question-nav-pages').removeAttr('style');//moves pagination to first
//            let pages = $('ul.ays-question-nav-pages li');
//            pages.each(function () {
//                $(this).removeClass('active'); //remove active pages
//            });
//            pages.eq(0).addClass('active'); // assigning to first page element active
//            show_hide_rows(page); // show count of rows
            $(document).find('#ays-questions-modal').aysModal('show');
            qatable.draw();
        });

        $(document).on('click', '.ays-delete-question', function () {
            let index = 1,
                id_container = $(document).find('input#ays_already_added_questions'),
                existing_ids = id_container.val().split(',');
            let q = $(this);

            q.parents("tr").css({
                'animation-name': 'slideOutLeft',
                'animation-duration': '.3s'
            });
            let indexOfAddTable = $.inArray($(this).data('id'), window.aysQuestSelected);
            if(indexOfAddTable !== -1){
                window.aysQuestSelected.splice( indexOfAddTable, 1 );
                qatable.draw();
            }

            if ($.inArray($(this).data('id').toString(), existing_ids) !== -1) {
                let position = $.inArray($(this).data('id').toString(), existing_ids);
                existing_ids.splice(position, 1);
                id_container.val(existing_ids.join(','));
            }

            $(document).find('input[type="checkbox"]#ays_select_' + $(this).data('id')).prop('checked', false);

            setTimeout(function(){
                q.parents('tr').remove();
                let accordion = $(document).find('table.ays-questions-table tbody');
                let questions_count = accordion.find('tr.ays-question-row').length;
                $(document).find('.questions_count_number').text(questions_count);

                if($(document).find('tr.ays-question-row').length == 0){
                    var colspan =  $(document).find('table.ays-questions-table thead th').length;
                   let quizEmptytd = '<tr class="ays-question-row ui-state-default">'+
                    '    <td colspan="'+colspan+'" class="empty_quiz_td">'+
                    '        <div>'+
                    '            <i class="ays_fa ays_fa_info" aria-hidden="true" style="margin-right:10px"></i>'+
                    '            <span style="font-size: 13px; font-style: italic;">'+
                    '               There are no questions yet.'+
                    '            </span>'+
                    '            <a class="create_question_link" href="admin.php?page=quiz-maker-questions&action=add" target="_blank">Create question</a>'+
                    '        </div>'+
                    '        <div class="ays_add_question_from_table">'+
                    '            <a href="javascript:void(0)" class="ays-add-question">'+
                    '                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                    '                Add questions'+
                    '            </a>'+
                    '        </div>'+
                    '    </td>'+
                    '</tr>';
                    $(document).find('#ays-questions-table tbody').append(quizEmptytd);
                }
                $(document).find('tr.ays-question-row').each(function () {
                    if ($(this).hasClass('even')) {
                        $(this).removeClass('even');
                    }
                    let className = ((index % 2) === 0) ? 'even' : '';
                    index++;
                    $(this).addClass(className);
                });
            }, 300);
        });
        
        // Bulk delete
        let accordion = $(document).find('table.ays-questions-table tbody');
		$(document).on('click', '.ays_select_all', function(e){
            if(accordion.find('.empty_quiz_td').length > 0){
                return false;
            }
            accordion.find('.ays_del_tr').prop("checked", true);
            $(document).find('.ays_bulk_del_questions').prop('disabled', false);

            $(this).addClass("ays_clear_select_all");
            $(this).removeClass("ays_select_all");
		});
		
		$(document).on('click', '.ays_clear_select_all', function(e){
            accordion.find('.ays_del_tr').prop("checked", false);
            $(document).find('.ays_bulk_del_questions').prop('disabled', true);

            $(this).addClass("ays_select_all");
            $(this).removeClass("ays_clear_select_all");
		});
		
		$(document).on('click', 'table.ays-questions-table tbody .ays_del_tr', function(e){
            $(document).find('.ays_bulk_del_questions').prop('disabled', false);
            if(accordion.find('.ays_del_tr:checked').length == 0){
                $(document).find('.ays_bulk_del_questions').prop('disabled', true);
            }
		});
        
		$(document).on('click', '.ays_bulk_del_questions', function(e){
            let accordion_el = accordion.find('tr .ays_del_tr:checked'),
				accordion_el_length = accordion_el.length;
            let id_container = $(document).find('input#ays_already_added_questions'),
                existing_ids = id_container.val().split(',');
            let questions_count = $(document).find('.questions_count_number');
            accordion_el.each(function(e, el){
                $(this).parents("tr").css({
                    'animation-name': 'slideOutLeft',
                    'animation-duration': '.3s'
                });
                let a = $(this);
                let index = 1;
                let questionId = parseInt(a.parents('tr').data('id'));
                let indexOfAddTable = $.inArray(questionId, window.aysQuestSelected);
                if(indexOfAddTable !== -1){
                    window.aysQuestSelected.splice( indexOfAddTable, 1 );
                }

                if ($.inArray(questionId.toString(), existing_ids) !== -1) {
                    let position = $.inArray(questionId.toString(), existing_ids);
                    existing_ids.splice(position, 1);
                    id_container.val(existing_ids.join(','));
                }
                setTimeout(function(){
                    a.parents('tr').remove();
                    questions_count.text(accordion.find('tr.ays-question-row').length);
                    if(accordion.find('tr.ays-question-row').length == 0){
                        var colspan =  $(document).find('table.ays-questions-table thead th').length;
                        let quizEmptytd = '<tr class="ays-question-row ui-state-default">'+
                        '    <td colspan="'+colspan+'" class="empty_quiz_td">'+
                        '        <div>'+
                        '            <i class="ays_fa ays_fa_info" aria-hidden="true" style="margin-right:10px"></i>'+
                        '            <span style="font-size: 13px; font-style: italic;">'+
                        '               There are no questions yet.'+
                        '            </span>'+
                        '            <a class="create_question_link" href="admin.php?page=quiz-maker-questions&action=add" target="_blank">Create question</a>'+
                        '        </div>'+
                        '        <div class="ays_add_question_from_table">'+
                        '            <a href="javascript:void(0)" class="ays-add-question">'+
                        '                <i class="ays_fa ays_fa_plus_square" aria-hidden="true"></i>'+
                        '                Add questions'+
                        '            </a>'+
                        '        </div>'+
                        '    </td>'+
                        '</tr>';
                        accordion.append(quizEmptytd);
                    }                        

                    accordion.find('tr.ays-question-row').each(function () {
                        if ($(this).hasClass('even')) {
                            $(this).removeClass('even');
                        }
                        let className = ((index % 2) === 0) ? 'even' : '';
                        index++;
                        $(this).addClass(className);
                    });
                }, 300);
            });
            setTimeout(function(){
                qatable.draw();                
            }, 500);
            $(document).find('.ays_bulk_del_questions').prop('disabled', true);
            $(document).find('.ays-quiz-select-all-button').addClass("ays_select_all");
            if ( $(document).find('.ays-quiz-select-all-button').hasClass("ays_clear_select_all") ) {
                $(document).find('.ays-quiz-select-all-button').removeClass("ays_clear_select_all")
            }
		});
        
        // Question bank by category
        $(document).find('.question_bank_by_category_div').sortable({
            cursor: 'move',
			opacity: 0.8,
			placeholder: 'clone',
        });
        
        $(document).on('click', '.ays_refresh_qbank_cats_button', function () {
            $(document).find('#ays_apply_top').trigger('click');
        });
        
        $('input[name="ays_question_bank_type"]').on('change', function () {
            if ($(this).val() == 'general') {
                $('.question_bank_general').show(250);
                $('.question_bank_by_category').hide(250);
            } else if ($(this).val() == 'by_category') {
                $('.question_bank_general').hide(250);
                $('.question_bank_by_category').show(250);
            }

        });

        $(document).find('input[name="ays_question_count_per_page_type"]').on('change', function () {
            if ($(this).val() == 'general') {
                $(document).find('.question_count_per_page_general').show(250);
                $(document).find('.question_count_per_page_custom').hide(250);
            } else if ($(this).val() == 'custom') {
                $(document).find('.question_count_per_page_general').hide(250);
                $(document).find('.question_count_per_page_custom').show(250);
            }

        });

        $('input[name="ays_quiz_timer_type"]').on('change', function () {
            if ($(this).val() == 'quiz_timer') {
                $('.hide-on-question-timer').show(250);
                $('.show-on-question-timer').hide(250);
            } else if ($(this).val() == 'question_timer') {
                $('.hide-on-question-timer').hide(250);
                $('.show-on-question-timer').show(250);
            }

        });
    });
})(jQuery);
