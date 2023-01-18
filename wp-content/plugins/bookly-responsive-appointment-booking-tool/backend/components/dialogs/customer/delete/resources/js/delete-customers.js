jQuery(function ($) {
    'use strict';

    let
        $customersList        = $('#bookly-customers-list'),
        $initDeletingButton   = $('#bookly-delete'),
        $deleteDialog         = $('#bookly-delete-dialog'),
        $deleteButton         = $('.bookly-js-delete', $deleteDialog),
        $rememberCheckbox     = $('#bookly-js-remember-choice-checkbox', $deleteDialog),
        $deleteEventsCheckbox = $('#bookly-js-delete-with-events-checkbox', $deleteDialog),
        $deleteWPUserCheckbox = $('#bookly-js-delete-with-wp-user-checkbox', $deleteDialog)
    ;

    /**
     * Delete customers.
     */
    $initDeletingButton.on('click', function () {
        var ladda       = Ladda.create(this),
            customers   = [],
            $checkboxes = $customersList.find('tbody input:checked');

        ladda.start();
        $checkboxes.each(function () {
            customers.push(this.value);
        });

        if (customers.length > 0) {
            $.ajax({
                url     : ajaxurl,
                type    : 'POST',
                data    : {
                    action    : 'bookly_check_customers',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    customers : customers
                },
                dataType: 'json',
                success : function (response) {
                    ladda.stop();
                    $('.bookly-js-delete-with-events', $deleteDialog).toggle(response.data.exists_events);
                    $('.bookly-js-delete-without-events', $deleteDialog).toggle(!response.data.exists_events);

                    $deleteButton.prop('disabled', response.data.exists_events && !response.data.with_events);

                    $deleteEventsCheckbox.prop('checked', response.data.with_events);
                    $deleteWPUserCheckbox.prop('checked', response.data.with_wp_users);
                    $rememberCheckbox.prop('checked', response.data.remember);
                    $deleteDialog.booklyModal('show');
                }
            });
        } else {
            setTimeout(function () {
                ladda.stop();
            }, 200);
        }
    });

    $deleteEventsCheckbox.on('change', function () {
        $deleteButton.prop('disabled', !$deleteEventsCheckbox.prop('checked'));
    });

    $deleteButton.on('click', function () {
        var ladda       = Ladda.create(this),
            customers   = [],
            $checkboxes = $customersList.find('tbody input:checked')
        ;

        ladda.start();
        $checkboxes.each(function () {
            customers.push(this.value);
        });

        $.ajax({
            url     : ajaxurl,
            type    : 'POST',
            data: {
                action       : 'bookly_delete_customers',
                csrf_token   : BooklyL10nGlobal.csrf_token,
                customers    : customers,
                with_wp_users: $deleteWPUserCheckbox.prop('checked') ? 1 : 0,
                with_events  : $deleteEventsCheckbox.prop('checked') ? 1 : 0,
                remember     : $rememberCheckbox.prop('checked') ? 1 : 0
            },
            dataType: 'json',
            success : function (response) {
                ladda.stop();
                $deleteDialog.booklyModal('hide');
                if (response.success) {
                    $customersList.DataTable().ajax.reload(null, false);
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});