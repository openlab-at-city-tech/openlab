jQuery(function ($) {
    let $modal = $('#bookly-delete-dialog'),
        $delete = $('#bookly-delete', $modal),
        $addNotify = $("#bookly-delete-notify", $modal),
        $reason = $('#bookly-delete-reason', $modal),
        $cover = $('#bookly-delete-reason-cover', $modal)
    ;

    $addNotify
        .on('click', function () {
            $cover.toggle(this.checked);
        });

    function confirmDeleteAppointment(opt, onDone) {
        let self = this,
            options = $.extend({
                action: null,
                csrf_token: BooklyL10nGlobal.csrf_token,
            }, opt),
            deleteCustomerAppointments = this.deleteCustomerAppointments
        ;
        $modal.booklyModal('show');
        $delete
            .one('click', function () {
                let ladda = Ladda.create(this);
                ladda.start();
                deleteCustomerAppointments(options)
                    .then(function (response) {
                        if (typeof onDone == 'function') {
                            onDone(response);
                        }
                    })
                    .always(function () {ladda.stop();});
                }
            );
    }

    confirmDeleteAppointment.prototype = {
        deleteCustomerAppointments: function (options) {
            let data = $.extend({}, options, {
                notify: $addNotify.prop('checked') ? 1 : 0,
                reason: $addNotify.prop('checked') ? $reason.val() : ''
            });

            return $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $modal.booklyModal('hide');
                        if (response.data && response.data.queue && response.data.queue.all.length) {
                            BooklyNotificationsQueueDialog.showDialog({queue: response.data.queue});
                        }
                    }
                }
            });
        }
    }

    window.BooklyConfirmDeletingAppointment = confirmDeleteAppointment;
});