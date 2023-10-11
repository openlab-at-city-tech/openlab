(function ($) {
    'use strict';
    $(document).ready(function () {

        var dataTableData = {
            paging: 5,
            responsive: true,
            "bDestroy": true,
            "lengthMenu": [[5, 10, 25, 50, 100, -1], [5, 10, 25, 50, 100, quizLangLeaderboardDataTableObj.all]],
        	"language": {
				"sEmptyTable":     quizLangLeaderboardDataTableObj.sEmptyTable,
				"sInfo":           quizLangLeaderboardDataTableObj.sInfo,
				"sInfoEmpty":      quizLangLeaderboardDataTableObj.sInfoEmpty,
				"sInfoFiltered":   quizLangLeaderboardDataTableObj.sInfoFiltered,
				"sInfoPostFix":    "",
				"sInfoThousands":  ",",
				"sLengthMenu":     quizLangLeaderboardDataTableObj.sLengthMenu,
				"sLoadingRecords": quizLangLeaderboardDataTableObj.sLoadingRecords,
				"sProcessing":     quizLangLeaderboardDataTableObj.sProcessing,
				"sSearch":         quizLangLeaderboardDataTableObj.sSearch,
				"sUrl":            "",
				"sZeroRecords":    quizLangLeaderboardDataTableObj.sZeroRecords,
				"oPaginate": {
					"sFirst":    quizLangLeaderboardDataTableObj.sFirst,
					"sLast":     quizLangLeaderboardDataTableObj.sLast,
					"sNext":     quizLangLeaderboardDataTableObj.sNext,
					"sPrevious": quizLangLeaderboardDataTableObj.sPrevious,
				},
				"oAria": {
					"sSortAscending":  quizLangLeaderboardDataTableObj.sSortAscending,
					"sSortDescending": quizLangLeaderboardDataTableObj.sSortDescending
				}
		    }
		}

        // for details
        $(document).find('table.ays-quiz-individual-leaderboard-pagination, table.ays-quiz-global-leaderboard-pagination, table.ays-quiz-global-quiz-category-leaderboard-pagination').DataTable( dataTableData );
    });

})(jQuery);
