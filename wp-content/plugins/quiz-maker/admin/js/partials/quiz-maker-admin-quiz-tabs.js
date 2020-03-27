(function($){
    $(document).ready(function(){        
        let questCategoryFilter = $(document).find('#add_quest_category_filter').select2({
            placeholder: 'Select Category',
//            dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter'),
            multiple: true,
            matcher: searchForPage,
            dropdownParent: $(document).find('#quest_cat_container')
        });
        
        $(document).find('.ays_filter_cat_clear').on('click', function(){
            questCategoryFilter.val(null).trigger('change');
            qatable.draw();
        });
        window.aysQuestSelected = [];
        window.aysQuestNewSelected = [];
        let selectedRows = $(document).find('#ays-question-table-add tbody tr.selected');
        for(let i=0; i < selectedRows.length; i++){
            window.aysQuestSelected.push(selectedRows.eq(i).data('id'));
        }
        $.fn.dataTable.ext.search.push(
            function( settings, data, dataIndex ) {
                let questionCat = $(document).find('.cat_filter').val();
                if(questionCat == null){
                    return true;
                }
                for(let i=0; i<questionCat.length; i++){
                    if (data[4] == questionCat[i]){
                        return true;
                    }
                }
                return false;
            }
        );
        let qatable = $('#ays-question-table-add').DataTable({
            paging: 5,
            "lengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, "All"]],
            "infoCallback": function(){
                let qaTableSelectAll =  $(document).find('#ays-question-table-add tbody tr.ays_quest_row');
                let qaTableSelected =  0;
                qaTableSelectAll.each(function(){                    
                    if(!$(this).hasClass('selected')){
                        qaTableSelected++;
                    }
                });
                if(qaTableSelected > 0){
                    if($(document).find('#select_all').hasClass('deselect')){
                        $(document).find('#select_all').removeClass('deselect');
                        $(document).find('#select_all').text('Select All');
                    }
                }else{
                    $(document).find('#select_all').addClass('deselect');
                    $(document).find('#select_all').text('Deselect All');
                }
            },
            "drawCallback": function( settings ) {
                let qaTableRows =  $(document).find('#ays-question-table-add tbody tr.ays_quest_row');                
                qaTableRows.each(function(){                    
                    if($.inArray(parseInt($(this).data('id')), window.aysQuestSelected) == -1){
                        $(this).removeClass('selected');
                        $(this).find('.ays-select-single')
                            .removeClass('ays_fa_check_square_o')
                            .addClass('ays_fa_square_o');
                    }
                });
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
        $(document).find('#ays-question-table-add_info').append('<button id="select_all" class="button" type="button" style="margin-left:10px;">Select All</button>');
        $(document).on('click', '#select_all', function(e){
            window.aysQuestSelected = [];
			if(typeof window.aysQuestNewSelected == 'undefined'){
            	window.aysQuestNewSelected = [];
			}
            let qaTableSelectAll =  $(document).find('#ays-question-table-add tbody tr.ays_quest_row');
            if($(this).hasClass('deselect')){
                qaTableSelectAll.each(function(){
                    $(this).removeClass('selected');
                    $(this).find('.ays-select-single').removeClass('ays_fa_check_square_o').addClass('ays_fa_square_o');
                });
                $(this).removeClass('deselect');
                $(this).text('Select All');
            }else{
                qaTableSelectAll.each(function(){
                    if(! $(this).hasClass('selected')){
                        $(this).addClass('selected');
                        window.aysQuestSelected.unshift($(this).data('id'));
                        window.aysQuestNewSelected.unshift($(this).data('id'));
                        $(this).find('.ays-select-single').removeClass('ays_fa_square_o').addClass('ays_fa_check_square_o');
                    }
                });
                $(this).addClass('deselect');
                $(this).text('Deselect All');
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
        });


        // Bulk delete
        
        let accordion = $(document).find('table.ays-questions-table tbody');
		$(document).on('click', '.ays_select_all', function(e){
            if(accordion.find('.empty_quiz_td').length > 0){
                return false;
            }
            accordion.find('.ays_del_tr').prop("checked", "true");	
            if($(document).find('.ays_bulk_del_questions').prop('disabled')){
                $(document).find('.ays_bulk_del_questions').removeProp('disabled');
            }
            $(this).addClass("ays_clear_select_all");
            $(this).removeClass("ays_select_all");
		});
		
		$(document).on('click', '.ays_clear_select_all', function(e){
            accordion.find('.ays_del_tr').removeProp("checked");	
            if(! $(document).find('.ays_bulk_del_questions').prop('disabled')){
                $(document).find('.ays_bulk_del_questions').prop('disabled', "true");
            }
            $(this).addClass("ays_select_all");
            $(this).removeClass("ays_clear_select_all");
		});
		
		$(document).on('click', 'table.ays-questions-table tbody .ays_del_tr', function(e){
            if($(document).find('.ays_bulk_del_questions').prop('disabled')){
                $(document).find('.ays_bulk_del_questions').removeProp('disabled');
            }
            if(accordion.find('.ays_del_tr:checked').length == 0){
                $(document).find('.ays_bulk_del_questions').attr('disabled','disabled');
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
                       let quizEmptytd = '<tr class="ays-question-row ui-state-default">'+
                        '    <td colspan="5" class="empty_quiz_td">'+
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
            $(document).find('.ays_bulk_del_questions').attr('disabled','disabled');
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
    });
})(jQuery);