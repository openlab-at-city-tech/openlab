(function ($) {

    if (window.OpenLab === undefined) {
        var OpenLab = {};
    }

    var resizeTimer;

    OpenLab.admin = {
        init: function () {

            OpenLab.admin.helpPostAutocomplete();

        },
        helpPostAutocomplete: function () {

            if (!$('.help-post-autocomplete').length) {
                return false;
            }

            $('.help-post-autocomplete').each(function () {

                var thisElem = $(this);

                thisElem.autocomplete({
                    source: function (request, response) {
                        $.ajax({
                            type: 'GET',
                            url: localVars.ajaxurl,
                            dataType: 'json',
                            data: {
                                action: 'openlab_ajax_help_post_autocomplete',
                                term: request.term,
                                nonce: localVars.nonce
                            },
                            success: function (data, textStatus, XMLHttpRequest)
                            {
                                if (data === 'exit') {
                                    console.log('early exit');
                                    response([]);
                                    OpenLab.admin.helpPostAutoCompleteReturnAction($(this), 0, '');
                                } else {
                                    console.log('data return', data.length);
                                    response(data.length === 1 && data[ 0 ].length === 0 ? [] : data);
                                }
                            },
                            error: function (MLHttpRequest, textStatus, errorThrown) {
                                console.log(errorThrown);
                                response([]);
                                OpenLab.admin.helpPostAutoCompleteReturnAction($(this), 0, '');
                            }
                        })
                    },
                    minLength: 3,
                    select: function (event, ui) {
                        
                        if (ui.item.id == 0) {
                            OpenLab.admin.helpPostAutoCompleteReturnAction($(this), 0, '');
                        } else {
                            OpenLab.admin.helpPostAutoCompleteReturnAction($(this), ui.item.id, ui.item.label);
                        }

                    }
                });

            })

        },
        helpPostAutoCompleteReturnAction: function (thisComplete, id, val) {
            thisComplete.val(val);
            var storage = thisComplete.data('target');
            $('#' + storage).val(id);
        }
    };

    $(document).ready(function () {
        console.log('go');
        OpenLab.admin.init();

    });
})(jQuery);