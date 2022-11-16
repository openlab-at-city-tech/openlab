jQuery(function ($) {
    'use strict';

    let $dialog = $('#bookly-create-mailing-list-modal'),
        $name = $('#bookly-mailing-list-name', $dialog),
        $newList = $('#bookly-js-new-mailing-list'),
        $save = $('#bookly-save', $dialog),
        new_category_id
    ;

    // Save categories
    $save.on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this);
        ladda.start();
        $.post(ajaxurl,
            {
                action: 'bookly_create_mailing_list',
                name: $name.val(),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            function (response) {
                if (response.success) {
                    $dialog.booklyModal('hide');
                    $(document.body).trigger('bookly.mailing-recipients.show', [response.data]);
                }
                ladda.stop();
            });
    });

    $newList.on('click', function (e) {
        e.preventDefault();
        $dialog.booklyModal('show');
    });

    $dialog.off().on('show.bs.modal', function () {
        $name.val('');
    });
});