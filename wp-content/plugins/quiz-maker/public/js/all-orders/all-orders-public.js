(function ($) {
    'use strict';    
    $(document).ready(function () {

    	var dataTableData = {
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
		    }
		}

    	var endDateColumn = $(document).find('#ays-quiz-all-orders-page th.ays-quiz-all-orders-end-date-column');
        var endDateColumnIndex = endDateColumn.parents('thead').find('th').index(endDateColumn);

        if( endDateColumnIndex !== -1 ){
            dataTableData.order = [[ endDateColumnIndex, "desc" ]];
        }
        
        // for details
        $(document).find('#ays-quiz-all-orders-page').DataTable( dataTableData );


    });
    
})(jQuery);
