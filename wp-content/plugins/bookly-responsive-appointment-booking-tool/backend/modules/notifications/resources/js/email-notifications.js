jQuery(function ($) {

    const $tab_container = $('.bookly-js-notifications-wrap'),
          $footer        = $('.bookly-js-notifications-footer');

    $('.bookly-js-notifications-tabs a').off().on('click', function (e) {
        $footer.hide();
        $tab_container.html('<div class=\'bookly-loading\'></div>');
        let tab = $(this).data('tab');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            xhrFields: {withCredentials: true},
            data: {
                action: 'bookly_email_notifications_load_tab',
                csrf_token: BooklyL10nGlobal.csrf_token,
                tab: tab
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $tab_container.html(response.data.html);
                    switch (tab) {
                        case 'settings':
                            $footer.show();
                            $('.bookly-js-save', $footer)
                            .on('click', function (e) {
                                e.preventDefault();

                                let ladda = Ladda.create(this),
                                    data  = $('#bookly-email-settings-form').serializeArray()
                                ;
                                data.push({name: 'action', value: 'bookly_save_general_settings_for_notifications'});

                                ladda.start();
                                $.ajax({
                                    url: ajaxurl,
                                    type: 'POST',
                                    data: data,
                                    dataType: 'json',
                                    success: function (response) {
                                        if (response.success) {
                                            booklyAlert({success: [BooklyL10n.settingsSaved]});
                                        }
                                        ladda.stop();
                                    }
                                });
                            });
                            break;
                        case 'logs':
                            $(document.body).trigger('bookly.init_email_logs',);
                            break;
                        default:
                            BooklyNotificationsList();
                            BooklyNotificationDialog();
                            break;
                    }
                }
            }
        });
    });
    $('.bookly-js-notifications-tabs a[data-tab=' + BooklyL10n.tab + ']').trigger('click');
});