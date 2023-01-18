jQuery(function($) {
    'use strict';

    /**
     * Notifications Tab
     */
    BooklyNotificationsList();
    BooklyNotificationDialog();

    var $phone_input = $('#admin_phone');
    if (BooklyL10n.intlTelInput.enabled) {
        $phone_input.intlTelInput({
            preferredCountries: [BooklyL10n.intlTelInput.country],
            initialCountry: BooklyL10n.intlTelInput.country,
            geoIpLookup: function(callback) {
                $.get('https://ipinfo.io', function() {}, 'jsonp').always(function(resp) {
                    var countryCode = (resp && resp.country) ? resp.country : '';
                    callback(countryCode);
                });
            },
            utilsScript: BooklyL10n.intlTelInput.utils
        });
    }

    $('#send_test_sms').on('click', function(e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'bookly_send_test_sms',
                csrf_token: BooklyL10nGlobal.csrf_token,
                phone_number: getPhoneNumber()
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    booklyAlert({success: [response.message]});
                } else {
                    booklyAlert({error: [response.message]});
                }
            }
        });
    });

    $('[data-action=save-administrator-phone]')
        .on('click', function(e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_save_administrator_phone',
                    bookly_sms_administrator_phone: getPhoneNumber(),
                    csrf_token: BooklyL10nGlobal.csrf_token
                },
                success: function(response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    }
                }
            });
        });

    function getPhoneNumber() {
        var phone_number;
        try {
            phone_number = BooklyL10n.intlTelInput.enabled ? $phone_input.intlTelInput('getNumber') : $phone_input.val();
            if (phone_number == '') {
                phone_number = $phone_input.val();
            }
        } catch (error) {  // In case when intlTelInput can't return phone number.
            phone_number = $phone_input.val();
        }

        return phone_number;
    }

    /**
     * Campaigns Tab.
     */
    $("[href='#campaigns']").one('click', function() {
        let $container = $('#campaigns'),
            $add_campaign = $('#bookly-js-new-campaign', $container),
            $check_all_button = $('#bookly-cam-check-all', $container),
            $list = $('#bookly-campaigns', $container),
            $filter = $('#bookly-filter', $container),
            $delete_button = $('#bookly-delete', $container),
            columns = [],
            order = [],
            current_time,
            campaign_pending = $('<span/>', {class: 'badge badge-info', text: BooklyL10n.campaign.pending})[0].outerHTML,
            campaign_in_progress = $('<span/>', {class: 'badge badge-primary', text: BooklyL10n.campaign.in_progress})[0].outerHTML,
            campaign_completed = $('<span/>', {class: 'badge badge-success', text: BooklyL10n.campaign.completed})[0].outerHTML,
            campaign_canceled = $('<span/>', {class: 'badge badge-secondary', text: BooklyL10n.campaign.canceled})[0].outerHTML,
            dt;

        /**
         * Init table columns.
         */
        $.each(BooklyL10n.datatables.sms_mailing_campaigns.settings.columns, function(column, show) {
            if (show) {
                switch (column) {
                    case 'state':
                        columns.push({
                            data: column,
                            className: 'align-middle',
                            render: function(data, type, row, meta) {
                                switch (data) {
                                    case 'pending':
                                        return campaign_pending;
                                    case 'in-progress':
                                        return campaign_in_progress;
                                    case 'completed':
                                        return campaign_completed;
                                    case 'canceled':
                                        return campaign_canceled;
                                    default:
                                        return $.fn.dataTable.render.text().display(data);
                                }
                            }
                        });
                        break;
                    case 'send_at':
                        columns.push({
                            data: column,
                            className: 'align-middle',
                            render: function(data, type, row, meta) {
                                return moment(data).format(BooklyL10n.moment_format_date_time);
                            }
                        });
                        break;
                    default:
                        columns.push({data: column, render: $.fn.dataTable.render.text()});
                        break;
                }
            }
        });

        $.each(BooklyL10n.datatables.sms_mailing_campaigns.settings.order, function(_, value) {
            const index = columns.findIndex(function(c) { return c.data === value.column; });
            if (index !== -1) {
                order.push([index, value.order]);
            }
        });

        columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            className: "text-right",
            render: function(data, type, row, meta) {
                return '<button type="button" class="btn btn-default"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></button>';
            }
        });
        columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            render: function(data, type, row, meta) {
                return '<div class="custom-control custom-checkbox">' +
                    '<input value="' + row.id + '" id="bookly-dtcam-' + row.id + '" type="checkbox" class="custom-control-input">' +
                    '<label for="bookly-dtcam-' + row.id + '" class="custom-control-label"></label>' +
                    '</div>';
            }
        });

        dt = $list.DataTable({
            info: false,
            searching: false,
            lengthChange: false,
            pageLength: 25,
            pagingType: 'numbers',
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                type: 'POST',
                url: ajaxurl,
                data: function(d) {
                    return $.extend({
                        action: 'bookly_get_campaign_list',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                    }, {filter: {search: $filter.val()}}, d);
                },
            },
            order: order,
            columns: columns,
            language: {
                zeroRecords: BooklyL10n.noResults,
                processing: BooklyL10n.processing
            },
            dom: '<\'row\'<\'col-sm-12\'tr>><\'row float-left mt-3\'<\'col-sm-12\'p>>',
        });

        $add_campaign
            .on('click', function() {
                BooklyCampaignDialog.showDialog(null, function() {
                    dt.ajax.reload();
                });
            });

        /**
         * Edit campaign.
         */
        $list.on('click', 'button', function() {
            let data = dt.row($(this).closest('td')).data();
            BooklyCampaignDialog.showDialog(data.id, function() {
                dt.ajax.reload();
            })
        });

        /**
         * Select all mailing lists.
         */
        $check_all_button.on('change', function() {
            $list.find('tbody input:checkbox').prop('checked', this.checked);
        });

        /**
         * On campaign select.
         */
        $list.on('change', 'tbody input:checkbox', function() {
            $check_all_button.prop('checked', $list.find('tbody input:not(:checked)').length == 0);
        });

        /**
         * Delete campaign(s).
         */
        $delete_button.on('click', function() {
            if (confirm(BooklyL10n.areYouSure)) {
                let ladda = Ladda.create(this),
                    ids = [],
                    $checkboxes = $('tbody input:checked', $list);
                ladda.start();

                $checkboxes.each(function() {
                    ids.push(this.value);
                });

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_delete_campaigns',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        ids: ids
                    },
                    dataType: 'json',
                    success: function(response) {
                        ladda.stop();
                        if (response.success) {
                            dt.rows($checkboxes.closest('td')).remove().draw();
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            }
        });

        /**
         * On filters change.
         */
        $filter
            .on('keyup', function() {
                dt.ajax.reload();
            })
            .on('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            });
    });

    /**
     * Mailing list Tab.
     */
    $("[href='#mailing']").one('click', function() {
        let $ml_container = $('#mailing_lists'),
            $mr_container = $('#mailing_recipients'),
            ml = {
                $list: $('#bookly-mailing-lists', $ml_container),
                $delete_button: $('#bookly-delete', $ml_container),
                $check_all_button: $('#bookly-ml-check-all', $ml_container),
                $filter: $('#bookly-filter', $ml_container),
                columns: [],
                order: [],
                dt: null,
                list_id: null
            },
            mr = {
                $list: $('#bookly-recipients-list', $mr_container),
                $delete_button: $('#bookly-delete', $mr_container),
                $check_all_button: $('#bookly-mr-check-all', $mr_container),
                $filter: $('#bookly-filter', $mr_container),
                columns: [],
                order: [],
                $list_name: $('#bookly-js-mailing-list-name', $mr_container),
                dt: null,
                $back: $('#bookly-js-show-mailing-list', $mr_container),
                $add_recipients_button: $('#bookly-js-add-recipients', $mr_container)
            };

        mr.$add_recipients_button.on('click', function() {
            BooklyAddRecipientsDialog.showDialog(ml.list_id, function() {
                mr.dt.ajax.reload();
            });
        });

        $(document.body)
            .on('bookly.mailing-recipients.show', {},
                function(event, mailing_list) {
                    ml.list_id = mailing_list.id;
                    mr.$list_name.html(mailing_list.name);
                    switchView('mailing_recipients');
                });

        mr.$back.on('click', function() {
            switchView('mailing_lists');
        });

        /**
         * Init table columns.
         */
        $.each(BooklyL10n.datatables.sms_mailing_lists.settings.columns, function(column, show) {
            if (show) {
                ml.columns.push({data: column, render: $.fn.dataTable.render.text()});
            }
        });

        $.each(BooklyL10n.datatables.sms_mailing_lists.settings.order, function(_, value) {
            const index = ml.columns.findIndex(function(c) { return c.data === value.column; });
            if (index !== -1) {
                ml.order.push([index, value.order]);
            }
        });

        ml.columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            className: "text-right",
            render: function(data, type, row, meta) {
                return '<button type="button" class="btn btn-default"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></button>';
            }
        });
        ml.columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            render: function(data, type, row, meta) {
                return '<div class="custom-control custom-checkbox">' +
                    '<input value="' + row.id + '" id="bookly-dtml-' + row.id + '" type="checkbox" class="custom-control-input">' +
                    '<label for="bookly-dtml-' + row.id + '" class="custom-control-label"></label>' +
                    '</div>';
            }
        });

        /**
         * Init DataTables for mailing lists.
         */
        ml.dt = ml.$list.DataTable({
            info: false,
            searching: false,
            lengthChange: false,
            pageLength: 25,
            pagingType: 'numbers',
            processing: true,
            responsive: true,
            serverSide: true,
            ajax: {
                url: ajaxurl,
                type: 'POST',
                data: function(d) {
                    return $.extend({
                        action: 'bookly_get_mailing_list',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                    }, {filter: {search: ml.$filter.val()}}, d)
                }
            },
            order: ml.order,
            columns: ml.columns,
            language: {
                zeroRecords: BooklyL10n.noResults,
                processing: BooklyL10n.processing
            },
            dom: "<'row'<'col-sm-12'tr>><'row float-left mt-3'<'col-sm-12'p>>",
        });

        /**
         * Select all mailing lists.
         */
        ml.$check_all_button.on('change', function() {
            ml.$list.find('tbody input:checkbox').prop('checked', this.checked);
        });

        /**
         * On mailing list select.
         */
        ml.$list.on('change', 'tbody input:checkbox', function() {
            ml.$check_all_button.prop('checked', ml.$list.find('tbody input:not(:checked)').length == 0);
        });

        /**
         * Edit mailing list.
         */
        ml.$list.on('click', 'button', function() {
            $(document.body).trigger('bookly.mailing-recipients.show', [ml.dt.row($(this).closest('td')).data()]);
        });

        /**
         * Delete mailing lists.
         */
        ml.$delete_button.on('click', function() {
            if (confirm(BooklyL10n.areYouSure)) {
                let ladda = Ladda.create(this),
                    ids = [],
                    $checkboxes = $('tbody input:checked', ml.$list);
                ladda.start();

                $checkboxes.each(function() {
                    ids.push(this.value);
                });

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_delete_mailing_lists',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        ids: ids
                    },
                    dataType: 'json',
                    success: function(response) {
                        ladda.stop();
                        if (response.success) {
                            ml.dt.rows($checkboxes.closest('td')).remove().draw();
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            }
        });

        /**
         * Init table columns.
         */
        $.each(BooklyL10n.datatables.sms_mailing_recipients_list.settings.columns, function(column, show) {
            if (show) {
                mr.columns.push({data: column, render: $.fn.dataTable.render.text()});
            }
        });

        $.each(BooklyL10n.datatables.sms_mailing_recipients_list.settings.order, function(_, value) {
            const index = mr.columns.findIndex(function(c) { return c.data === value.column; });
            if (index !== -1) {
                mr.order.push([index, value.order]);
            }
        });

        mr.columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            render: function(data, type, row, meta) {
                return '<div class="custom-control custom-checkbox">' +
                    '<input value="' + row.id + '" id="bookly-dtmr-' + row.id + '" type="checkbox" class="custom-control-input">' +
                    '<label for="bookly-dtmr-' + row.id + '" class="custom-control-label"></label>' +
                    '</div>';
            }
        });

        /**
         * Select all recipients.
         */
        mr.$check_all_button.on('change', function() {
            mr.$list.find('tbody input:checkbox').prop('checked', this.checked);
        });

        /**
         * On recipient select.
         */
        mr.$list.on('change', 'tbody input:checkbox', function() {
            mr.$check_all_button.prop('checked', mr.$list.find('tbody input:not(:checked)').length == 0);
        });

        /**
         * Delete recipients.
         */
        mr.$delete_button.on('click', function() {
            if (confirm(BooklyL10n.areYouSure)) {
                let ladda = Ladda.create(this),
                    ids = [],
                    $checkboxes = $('tbody input:checked', mr.$list);
                ladda.start();

                $checkboxes.each(function() {
                    ids.push(this.value);
                });

                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_delete_mailing_recipients',
                        csrf_token: BooklyL10nGlobal.csrf_token,
                        ids: ids
                    },
                    dataType: 'json',
                    success: function(response) {
                        ladda.stop();
                        if (response.success) {
                            mr.dt.rows($checkboxes.closest('td')).remove().draw();
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            }
        });

        /**
         * On filters change.
         */
        ml.$filter
            .on('keyup', function() {
                ml.dt.ajax.reload();
            })
            .on('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            });
        mr.$filter
            .on('keyup', function() {
                mr.dt.ajax.reload();
            })
            .on('keydown', function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                    return false;
                }
            });

        function switchView(view) {
            if (view === 'mailing_lists') {
                $mr_container.hide();
                $ml_container.show();
                ml.dt.ajax.reload();
            } else {
                $ml_container.hide();
                if (mr.dt === null) {
                    mr.dt = mr.$list.DataTable({
                        info: false,
                        searching: false,
                        lengthChange: false,
                        pageLength: 25,
                        pagingType: 'numbers',
                        processing: true,
                        responsive: true,
                        serverSide: true,
                        ajax: {
                            type: 'POST',
                            url: ajaxurl,
                            data: function(d) {
                                return $.extend({
                                    action: 'bookly_get_mailing_recipients',
                                    csrf_token: BooklyL10nGlobal.csrf_token,
                                    mailing_list_id: ml.list_id,
                                }, {filter: {search: mr.$filter.val()}}, d);
                            }
                        },
                        order: mr.order,
                        columns: mr.columns,
                        language: {
                            zeroRecords: BooklyL10n.noResults,
                            processing: BooklyL10n.processing
                        },
                        dom: '<\'row\'<\'col-sm-12\'tr>><\'row float-left mt-3\'<\'col-sm-12\'p>>',
                    });
                } else {
                    mr.dt.ajax.reload();
                }
                $mr_container.show();
            }
        }
    });

    /**
     * Date range pickers options.
     */
    var picker_ranges = {};
    picker_ranges[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10n.dateRange.today] = [moment(), moment()];
    picker_ranges[BooklyL10n.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
    var locale = $.extend({}, BooklyL10n.dateRange, BooklyL10n.datePicker);

    /**
     * SMS Details Tab.
     */
    $('[href="#sms_details"]').one('click', function() {
        var $date_range = $('#sms_date_range');
        $date_range.daterangepicker(
            {
                parentEl: $date_range.parent(),
                startDate: moment().subtract(30, 'days'), // by default select "Last 30 days"
                ranges: picker_ranges,
                locale: locale,
                showDropdowns: true,
                linkedCalendars: false,
            },
            function(start, end) {
                var format = 'YYYY-MM-DD';
                $date_range
                    .data('date', start.format(format) + ' - ' + end.format(format))
                    .find('span')
                    .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
            }
        );

        /**
         * Init Columns.
         */
        let columns = [];

        $.each(BooklyL10n.datatables.sms_details.settings.columns, function(column, show) {
            if (show) {
                if (column === 'message') {
                    columns.push({
                        data: column,
                        render: function(data, type, row, meta) {
                            return $.fn.dataTable.render.text().display(data).replaceAll('&lt;br /&gt;', '<br/>');
                        }
                    })
                } else {
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                }
            }
        });
        if (columns.length) {
            let dt = $('#bookly-sms').DataTable({
                ordering: false,
                paging: false,
                info: false,
                searching: false,
                processing: true,
                responsive: true,
                ajax: {
                    url: ajaxurl,
                    data: function(d) {
                        return {
                            action: 'bookly_get_sms_list',
                            csrf_token: BooklyL10nGlobal.csrf_token,
                            range: $date_range.data('date')
                        };
                    },
                    dataSrc: 'list'
                },
                columns: columns,
                language: {
                    zeroRecords: BooklyL10n.zeroRecords,
                    processing: BooklyL10n.processing
                }
            });
            $date_range.on('apply.daterangepicker', function() {
                dt.ajax.reload();
            });
            $(this).on('click', function() {
                dt.ajax.reload();
            });
        }
    });

    /**
     * Prices Tab.
     */
    let columns = [];

    function formatPrice(number) {
        number = number.replace(/0+$/, '');
        if ((number + '').split('.')[1].length === 1) {
            return '$' + number + '0';
        }

        return '$' + number;
    }

    $.each(BooklyL10n.datatables.sms_prices.settings.columns, function(column, show) {
        if (show) {
            switch (column) {
                case 'country_iso_code':
                    columns.push({
                        data: column,
                        className: 'align-middle',
                        render: function(data, type, row, meta) {
                            return '<div class="iti-flag ' + data + '"></div>';
                        }
                    });
                    break;
                case 'price':
                    columns.push({
                        data: column,
                        className: "text-right",
                        render: function(data, type, row, meta) {
                            return formatPrice(data);
                        }
                    });
                    break;
                case 'price_alt':
                    columns.push({
                        data: column,
                        className: "text-right",
                        render: function(data, type, row, meta) {
                            if (row.price_alt === '') {
                                return BooklyL10n.na;
                            } else {
                                return formatPrice(data);
                            }
                        }
                    });
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
            }
        }
    });
    if (columns.length) {
        $('#bookly-prices').DataTable({
            ordering: false,
            paging: false,
            info: false,
            searching: false,
            processing: true,
            responsive: true,
            ajax: {
                url: ajaxurl,
                data: {action: 'bookly_get_price_list', csrf_token: BooklyL10nGlobal.csrf_token},
                dataSrc: 'list'
            },
            columns: columns,
            language: {
                zeroRecords: BooklyL10n.noResults,
                processing: BooklyL10n.processing
            }
        });
    }

    /**
     * Sender ID Tab.
     */
    $("[href='#sender_id']").one('click', function() {
        var $request_sender_id = $('#bookly-request-sender_id'),
            $reset_sender_id = $('#bookly-reset-sender_id'),
            $cancel_sender_id = $('#bookly-cancel-sender_id'),
            $sender_id = $('#bookly-sender-id-input');

        /**
         * Init Columns.
         */
        let columns = [];

        $.each(BooklyL10n.datatables.sms_sender.settings.columns, function(column, show) {
            if (show) {
                switch (column) {
                    case 'name':
                        columns.push({
                            data: column,
                            render: function(data, type, row, meta) {
                                if (data === null) {
                                    return '<i>' + BooklyL10n.default + '</i>';
                                } else {
                                    return $.fn.dataTable.render.text().display(data);
                                }
                            }
                        });
                        break;
                    default:
                        columns.push({data: column, render: $.fn.dataTable.render.text()});
                }
            }
        });
        if (columns.length) {
            var dt = $('#bookly-sender-ids').DataTable({
                ordering: false,
                paging: false,
                info: false,
                searching: false,
                processing: true,
                responsive: true,
                ajax: {
                    url: ajaxurl,
                    data: {action: 'bookly_get_sender_ids_list', csrf_token: BooklyL10nGlobal.csrf_token},
                    dataSrc: function(json) {
                        if (json.pending) {
                            $sender_id.val(json.pending);
                            $request_sender_id.hide();
                            $sender_id.prop('disabled', true);
                            $cancel_sender_id.show();
                        }

                        return json.list;
                    }
                },
                columns: columns,
                language: {
                    zeroRecords: BooklyL10n.zeroRecords2,
                    processing: BooklyL10n.processing
                }
            });
        }

        $request_sender_id.on('click', function() {
            let ladda = Ladda.create(this);
            ladda.start();
            $.ajax({
                url: ajaxurl,
                data: {action: 'bookly_request_sender_id', csrf_token: BooklyL10nGlobal.csrf_token, 'sender_id': $sender_id.val()},
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.sender_id.sent]});
                        $request_sender_id.hide();
                        $sender_id.prop('disabled', true);
                        $cancel_sender_id.show();
                        dt.ajax.reload();
                    } else {
                        booklyAlert({error: [response.data.message]});
                    }
                }
            }).always(function() {
                ladda.stop();
            });
        });

        $reset_sender_id.on('click', function(e) {
            e.preventDefault();
            if (confirm(BooklyL10n.areYouSure)) {
                $.ajax({
                    url: ajaxurl,
                    data: {action: 'bookly_reset_sender_id', csrf_token: BooklyL10nGlobal.csrf_token},
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            booklyAlert({success: [BooklyL10n.sender_id.set_default]});
                            $('.bookly-js-sender-id').html('Bookly');
                            $('.bookly-js-approval-date').remove();
                            $sender_id.prop('disabled', false).val('');
                            $request_sender_id.show();
                            $cancel_sender_id.hide();
                            dt.ajax.reload();
                        } else {
                            booklyAlert({error: [response.data.message]});
                        }
                    }
                });
            }
        });

        $cancel_sender_id.on('click', function() {
            if (confirm(BooklyL10n.areYouSure)) {
                var ladda = Ladda.create(this);
                ladda.start();
                $.ajax({
                    method: 'POST',
                    url: ajaxurl,
                    data: {action: 'bookly_cancel_sender_id', csrf_token: BooklyL10nGlobal.csrf_token},
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $sender_id.prop('disabled', false).val('');
                            $request_sender_id.show();
                            $cancel_sender_id.hide();
                            dt.ajax.reload();
                        } else {
                            if (response.data && response.data.message) {
                                booklyAlert({error: [response.data.message]});
                            }
                        }
                    }
                }).always(function() {
                    ladda.stop();
                });
            }
        });
        $(this).on('click', function() { dt.ajax.reload(); });
    });

    $('#bookly-open-tab-sender-id').on('click', function(e) {
        e.preventDefault();
        $('#sms_tabs li a[href="#sender_id"]').trigger('click');
    });

    $('[href="#' + BooklyL10n.current_tab + '"]').click();
});