jQuery( function() {
    function trigger_update_by_ajax( data ) {
        jQuery.ajax({
            url: trp_updb_localized['admin_ajax_url'],
            type: 'post',
            dataType: 'json',
            data: data,
            success: function (response) {
                jQuery('#trp-update-database-progress').append(response['progress_message'])
                if ( response['trp_update_completed'] == 'no' ) {
                    trigger_update_by_ajax(response);
                }
            },
            error: function (errorThrown) {
                jQuery('#trp-update-database-progress').append(errorThrown['responseText'])
                console.log('TranslatePress AJAX Request Error while triggering database update');
            }
        });
    };
    trigger_update_by_ajax( {
        action: 'trp_update_database',
        trp_updb_nonce: trp_updb_localized['nonce'],
        initiate_update: true,
    } );
});