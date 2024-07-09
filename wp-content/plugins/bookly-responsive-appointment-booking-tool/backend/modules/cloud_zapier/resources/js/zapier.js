jQuery(function($) {
    'use strict';

    $('#bookly-zapier-generate-new-api-key').on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_cloud_zapier_generate_new_api_key',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                },
                dataType: 'json',
                success: function (response) {
                    ladda.stop();
                    if (response.success) {
                        $('#bookly_cloud_zapier_api_key').html(response.data.api_key);
                    } else {
                        booklyAlert({error: [response.data.message]});
                    }
                }
            });
        }
    });
});