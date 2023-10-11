(function ($) {
    'use strict';    
    $(document).ready(function () {
        
        // for details
        $(document).find('#ays-quiz-all-result-score-page, .ays-individual-quiz-all-result-score-page').DataTable({
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
		});
    });
    
})(jQuery);
