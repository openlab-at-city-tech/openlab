(function ($) {
    'use strict';
    $(document).on('ready', function () {

        if ($('#edittag').length) {
            $('#edittag').prop('action', BP_CustoCG_Admin.edit_action);
        }

        if ($('#addtag').length) {
            $('#addtag').prop('action', BP_CustoCG_Admin.edit_action);
            $('#addtag input[name="screen"]').prop('value', BP_CustoCG_Admin.ajax_screen);
        }
        
        if ($('#edittag').length) {
            $('#edittag').prop('action', BP_CustoCG_Admin.edit_action);
            $('#edittag input[name="screen"]').prop('value', BP_CustoCG_Admin.ajax_screen);
        }

        if ($('.search-form').length) {
            $('.search-form').append('<input type="hidden" name="page" value="' + BP_CustoCG_Admin.search_page + '"/>');
        }

        $('#addtag #submit').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
        });

    });


})(jQuery);
