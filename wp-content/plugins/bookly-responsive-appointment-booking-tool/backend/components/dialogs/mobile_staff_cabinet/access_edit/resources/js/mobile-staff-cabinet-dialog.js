jQuery(function ($) {
    'use strict';

    let $keysList = $('#bookly-keys-list'),
        $modal = $('#bookly-cloud-staff-cabinet-key-modal'),
        $title = $('.modal-title', $modal),
        $newToken = $('#bookly-js-new-key'),
        $save = $('#bookly-save', $modal),
        $staff = $('[name="staff_id"]',$modal),
        isNew,
        row
    ;

    $newToken
        .on('click', function () {
            isNew = true;
            $modal.booklyModal('show');
        });

    $modal
        .on('show.bs.modal', function () {
            $title.html(isNew ? BooklyL10nMobileStaffCabinet.new : BooklyL10nMobileStaffCabinet.edit);
            $('option',$staff).remove();
            $staff.append($('<option/>',{val: 0}));
            if (!isNew && row.id) {
                $staff.append($('<option/>', {html: row.full_name, val: row.id, selected: true}));
            }
            $.each(BooklyL10nMobileStaffCabinet.staff_members, function (index, option) {
                $staff.append($('<option/>',{html: option.full_name, val: option.id}));
            });
        });

    $save
        .on('click', function (e) {
            let staff_id = $staff.val();
            if (staff_id == '0') {
                e.preventDefault();
                booklyAlert({error: [BooklyL10nMobileStaffCabinet.staff_required]});
            } else {
                let ladda = Ladda.create(this),
                    data = {
                        action: 'bookly_mobile_staff_cabinet_grant_token',
                        token: isNew ? null : row.cloud_msc_token,
                        staff_id: staff_id,
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        send_notification: $('#bookly-send-notifications').is(':checked') ? 1 : 0
                    };
                ladda.start();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $keysList.DataTable().ajax.reload();
                            BooklyL10nMobileStaffCabinet.staff_members = response.data.staff_members;
                            $modal.booklyModal('hide');
                            if (data.send_notification && response.data.queue.all.length) {
                                BooklyNotificationsQueueDialog.showDialog({queue: response.data.queue});
                            }
                        } else {
                            booklyAlert({error: [response.data.message]});
                        }
                        ladda.stop();
                    }
                });
            }
        });

    $keysList
        // Edit token.
        .on('click', '[data-action=edit]', function () {
            row = $keysList.DataTable().row($(this).closest('td')).data();
            isNew = false;
            $modal.booklyModal('show');
        });
});