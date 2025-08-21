jQuery(function ($) {
    'use strict';
    window.BooklyNotificationsList = function () {
        let $notificationList = $('#bookly-js-notification-list'),
            $btnCheckAll = $('#bookly-check-all', $notificationList),
            $modalTestEmail = $('#bookly-test-email-notifications-modal'),
            $btnTestEmail = $('#bookly-js-test-email-notifications'),
            $testNotificationsList = $('#bookly-js-test-notifications-list', $modalTestEmail),
            $btnDeleteNotifications = $('#bookly-js-delete-notifications'),
            $filter = $('#bookly-filter'),
            columns = []
        ;

        /**
         * Init Columns.
         */
        $.each(BooklyL10n.datatables[BooklyL10n.gateway + '_notifications'].settings.columns, function (column, show) {
            if (show) {
                switch (column) {
                    case 'type':
                        columns.push({
                            data: 'order',
                            render: function (data, type, row, meta) {
                                return '<span class="hidden">' + data + '</span><i class="fa-fw ' + row.icon + '" title="' + row.title + '"></i>';
                            }
                        });
                        break;
                    case 'active':
                        columns.push({
                            data: column,
                            render: function (data, type, row, meta) {
                                return '<span class="badge ' + (row.active == 1 ? 'badge-success' : 'badge-info') + '">' + BooklyL10n.state[data] + '</span>' + ' (<a href="#" data-action="toggle-active">' + BooklyL10n.action[data] + '</a>)';
                            }
                        });
                        break;
                    default:
                        columns.push({data: column, render: $.fn.dataTable.render.text()});
                        break;
                }
            }
        });
        columns.push({
            data: null,
            className: 'text-right',
            orderable: false,
            responsivePriority: 1,
            render: function (data, type, row, meta) {
                return ' <button type="button" class="btn btn-default ladda-button" data-action="edit" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666"><span class="ladda-label"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></span></button>';
            }
        });

        columns[0].responsivePriority = 0;

        function toggleActive(row) {
            let data = row.data();
            data.active = data.active === '1' ? '0' : '1';
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_set_notification_state',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    id: data.id,
                    active: data.active
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        row.data(data).draw();
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    }
                }
            });
        }

        /**
         * Notification list
         */
        var dt = booklyDataTables.init($notificationList, BooklyL10n.datatables[BooklyL10n.gateway + '_notifications'].settings, {
            paging: false,
            searching: true,
            serverSide: false,
            ajax: {
                url: ajaxurl,
                data: {
                    action: 'bookly_get_notifications',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    gateway: BooklyL10n.gateway
                }
            },
            columns: columns,
            language: {
                zeroRecords: BooklyL10n.zeroRecordsAlt,
                emptyTable: BooklyL10n.emptyTable,
                processing: BooklyL10n.processing,
                loadingRecords: BooklyL10n.loadingRecords
            },
            row_with_checkbox: true
        }).on('click', '[data-action=toggle-active]', function (e) {
            e.preventDefault();
            let $tr = $(this).closest('tr');
            if ($tr.hasClass('child')) {
                toggleActive(dt.row($tr.prev().closest('tr')));
            } else {
                toggleActive(dt.row($tr));
            }
        }).on('order', function () {
            let order = [];
            dt.order().forEach(function (data) {
                order.push({
                    column: columns[data[0]].data,
                    order: data[1]
                });
            });
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_update_table_order',
                    table: BooklyL10n.gateway + '_notifications',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    order: order
                },
                dataType: 'json'
            });
        });

        /**
         * On filters change.
         */
        $filter
            .on('keyup', function () {
                dt.search(this.value).draw();
            })
            .on('keydown', function (e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            })
        ;

        /**
         * Select all notifications.
         */
        $btnCheckAll
            .on('change', function () {
                $('tbody input:checkbox', $notificationList).prop('checked', this.checked);
            });

        $notificationList
            .on('change', 'tbody input:checkbox', function () {
                $btnCheckAll.prop('checked', $notificationList.find('tbody input:not(:checked)').length === 0);
            });

        /**
         * Delete notifications.
         */
        $btnDeleteNotifications.on('click', function () {
            if (confirm(BooklyL10n.areYouSure)) {
                let ladda = Ladda.create(this),
                    data = [],
                    $checkboxes = $('input:checked', $notificationList);
                ladda.start();

                $checkboxes.each(function () {
                    data.push(this.value);
                });

                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: {
                        action: 'bookly_delete_notifications',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        notifications: data
                    },
                    dataType: 'json',
                    success: function (response) {
                        ladda.stop();
                        if (response.success) {
                            dt.rows($checkboxes.closest('td')).remove().draw();
                        }
                    }
                });
            }
        });

        $('[href="#bookly-js-auto"]').click(
            function () {
                if (this.classList.contains("toggle")) {
                    $(this).removeClass("border rounded mb-3 toggle");
                    $(this).addClass("border-light rounded-top bg-light");
                } else {
                    $(this).removeClass("border-light rounded-top bg-light")
                    $(this).addClass("border rounded mb-3 toggle");
                }
            });

        $btnTestEmail
            .on('click', function () {
                $modalTestEmail.booklyModal()
            });

        let $check = $('<div/>', {class: 'bookly-dropdown-item my-0 pl-3'}).append(
            $('<div>', {class: 'custom-control custom-checkbox'}).append(
                $('<input>', {class: 'custom-control-input', type: 'checkbox'}),
                $('<label>', {class: 'custom-control-label text-wrap w-100'})
            ));
        $modalTestEmail
            .on('change', '#bookly-check-all-entities', function () {
                $(':checkbox', $testNotificationsList).prop('checked', this.checked);
                $(':checkbox:first-child', $testNotificationsList).trigger('change');
            })
            .on('click', '[for=bookly-check-all-entities]', function (e) {
                e.stopPropagation();
            })
            .on('click', '.btn-success', function () {
                var ladda = Ladda.create(this),
                    data = $(this).closest('form').serializeArray();
                ladda.start();
                $(':checked', $testNotificationsList).each(function () {
                    data.push({name: 'notification_ids[]', value: $(this).data('notification-id')});
                });
                data.push({name: 'action', value: 'bookly_test_email_notifications'});
                $.ajax({
                    url: ajaxurl,
                    method: 'POST',
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        ladda.stop();
                        if (response.success) {
                            booklyAlert({success: [BooklyL10n.sentSuccessfully]});
                            $modalTestEmail.booklyModal('hide');
                        }
                    }
                });
            })
            .on('shown.bs.modal', function () {
                let $send = $(this).find('.btn-success'),
                    active = 0;
                $send.prop('disabled', true);
                $testNotificationsList.html('');
                (dt.rows().data()).each(function (notification) {
                    let $cloneCheck = $check.clone();

                    $('label', $cloneCheck).html(notification.name).attr('for', 'bookly-n-' + notification.id)
                        .on('click', function (e) {
                            e.stopPropagation();
                        })
                    ;
                    $(':checkbox', $cloneCheck)
                        .prop('checked', notification.active == '1')
                        .attr('id', 'bookly-n-' + notification.id)
                        .data('notification-id', notification.id)
                    ;

                    $testNotificationsList.append($cloneCheck);

                    if (notification.active == '1') {
                        active++;
                    }
                });
                $('.bookly-js-count', $modalTestEmail).html(active);
                $send.prop('disabled', false);
            });

        $testNotificationsList
            .on('change', ':checkbox', function () {
                $('.bookly-js-count', $modalTestEmail).html($(':checked', $testNotificationsList).length);
            });
    };
});