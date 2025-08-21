jQuery(function ($) {
    'use strict';

    let
        $payments_list = $('#bookly-payments-list'),
        $check_all_button = $('#bookly-check-all'),
        $id_filter = $('#bookly-filter-id'),
        $creationDateFilter = $('#bookly-filter-date'),
        $appointmentDateFilter = $('#bookly-filter-appointment-date'),
        $type_filter = $('#bookly-filter-type'),
        $customer_filter = $('#bookly-filter-customer'),
        $staff_filter = $('#bookly-filter-staff'),
        $service_filter = $('#bookly-filter-service'),
        $status_filter = $('#bookly-filter-status'),
        $payment_total = $('#bookly-payment-total'),
        $delete_button = $('#bookly-delete'),
        $download_invoice = $('#bookly-download-invoices'),
        urlParts = document.URL.split('#'),
        columns = [],
        pickers = {
            dateFormat: 'YYYY-MM-DD',
            creationDate: {
                startDate: moment().subtract(30, 'days'),
                endDate: moment(),
            },
            appointmentDate: {
                startDate: moment().subtract(100, 'years'),
                endDate: moment().add(100, 'years'),
            },
        };

    $('.bookly-js-select').val(null);

    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function (part) {
            var params = part.split('=');
            if (params[0] == 'created-date') {
                if (params['1'] === 'any') {
                    $creationDateFilter
                        .data('date', 'any').find('span')
                        .html(BooklyL10n.dateRange.anyTime);
                } else {
                    pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                    pickers.creationDate.endDate = moment(params['1'].substring(11));
                    $creationDateFilter
                        .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                        .find('span')
                        .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
                }
            } else if (params[0] === 'appointment-date') {
                if (params['1'] === 'any') {
                    $appointmentDateFilter
                        .data('date', 'any').find('span')
                        .html(BooklyL10n.dateRange.appAtAnyTime);
                } else {
                    pickers.appointmentDate.startDate = moment(params['1'].substring(0, 10));
                    pickers.appointmentDate.endDate = moment(params['1'].substring(11));
                    $appointmentDateFilter
                        .data('date', pickers.appointmentDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.appointmentDate.endDate.format(pickers.dateFormat))
                        .find('span')
                        .html(pickers.appointmentDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.appointmentDate.endDate.format(BooklyL10n.dateRange.format));
                }
            } else {
                $('#bookly-filter-' + params[0]).val(params[1]);
            }
        });
    } else {
        $.each(BooklyL10n.datatables.payments.settings.filter, function (field, value) {
            if (value != '') {
                let $elem = $('#bookly-filter-' + field);
                if ($elem.is(':checkbox')) {
                    $elem.prop('checked', value == '1');
                } else {
                    $elem.val(value);
                }
            }
            // check if select has correct values
            if ($('#bookly-filter-' + field).prop('type') == 'select-one') {
                if ($('#bookly-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                    $('#bookly-filter-' + field).val(null);
                }
            }
        });
    }

    $('.bookly-js-select').booklySelect2({
        width: '100%',
        allowClear: true,
        placeholder: '',
        theme: 'bootstrap4',
        dropdownParent: '#bookly-tbs',
        language: {
            noResults: function () {
                return BooklyL10n.noResultFound;
            }
        },
        matcher: function (params, data) {
            const term = $.trim(params.term).toLowerCase();
            if (term === '' || data.text.toLowerCase().indexOf(term) !== -1) {
                return data;
            }

            let result = null;
            const search = $(data.element).data('search');
            search &&
            search.find(function (text) {
                if (result === null && text.toLowerCase().indexOf(term) !== -1) {
                    result = data;
                }
            });

            return result;
        }
    });

    $('.bookly-js-select-ajax')
        .booklySelect2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language: {
                noResults: function () {
                    return BooklyL10n.no_result_found;
                },
                searching: function () {
                    return BooklyL10n.searching;
                }
            },
            ajax: {
                url: ajaxurl,
                dataType: 'json',
                delay: 250,
                data: function (params) {
                    params.page = params.page || 1;
                    return {
                        action: this.action === undefined ? $(this).data('ajax--action') : this.action,
                        filter: params.term,
                        page: params.page,
                        csrf_token: BooklyL10nGlobal.csrf_token
                    };
                }
            },
        });

    /**
     * Init Columns.
     */
    $.each(BooklyL10n.datatables.payments.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'created_at':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.created_format;
                        }
                    });
                    break;
                case 'start_date':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return type === 'sort' ? data : row.start_date_format;
                        }
                    });
                    break;
                case 'subtotal':
                    columns.push({
                        data: column,
                        render: $.fn.dataTable.render.text(),
                        orderable: false,
                        searchable: false
                    });
                    break;
                case 'service':
                case 'provider':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return $.fn.dataTable.render.text().display(data) + (row.multiple ? '<i class="fas fa-shopping-cart ml-1" title="' + BooklyL10n.multiple + '"></i>' : '');
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
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        width: BooklyL10n.invoice.enabled ? 180 : 90,
        render: function (data, type, row, meta) {
            var buttons = '<div class="d-inline-flex">';
            if (BooklyL10n.invoice.enabled) {
                buttons += '<button type="button" class="btn btn-default mr-1" data-action="view-invoice" data-payment_id="' + row.id + '"><i class="far fa-fw fa-file-pdf mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.invoice.button + '</span></button>';
            }
            return buttons + '<button type="button" class="btn btn-default" data-action="show-payment" data-payment_id="' + row.id + '"><i class="fas fa-fw fa-list-alt mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.details + '…</span></button></div>';
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.id + '" id="bookly-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-dt-' + row.id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    function getFilter() {
        return {
            id: $id_filter.val(),
            created_at: $creationDateFilter.data('date'),
            start_date: $appointmentDateFilter.data('date'),
            type: $type_filter.val(),
            customer: $customer_filter.val(),
            staff: $staff_filter.val(),
            service: $service_filter.val(),
            status: $status_filter.val()
        }
    }

    /**
     * Init DataTables.
     */
    var dt = booklyDataTables.init($payments_list, BooklyL10n.datatables.payments.settings, {
        ajax: {
            url: ajaxurl,
            method: 'POST',
            data: function(d) {
                return $.extend({}, d, {
                    action: 'bookly_get_payments',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    filter: getFilter()
                });
            },
            dataSrc: function(json) {
                $payment_total.html(json.total);

                return json.data;
            }
        },
        columns: columns
    });

    dt.on('order', function () {
        let order = [];
        dt.order().forEach(function (data) {
            order.push({
                column: columns[data[0]].data,
                order: data[1]
            });
        });
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_update_table_order',
                table: 'payments',
                csrf_token: BooklyL10nGlobal.csrf_token,
                order: order
            },
            dataType: 'json'
        });
    });

    /**
     * Select all coupons.
     */
    $check_all_button.on('change', function () {
        $payments_list.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $payments_list
        // On coupon select.
        .on('change', 'tbody input:checkbox', function () {
            $check_all_button.prop('checked', $payments_list.find('tbody input:not(:checked)').length == 0);
        })
        // Show invoice
        .on('click', '[data-action=view-invoice]', function () {
            window.location = $download_invoice.data('action') + '&invoices=' + $(this).data('payment_id');
        })
        // show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: dt.row($(this).closest('td')).data().id,
                done: function (event) {
                    dt.ajax.reload(null, false);
                }
            });
        });

    /**
     * Init date range picker.
     */

    let picker_ranges1 = {},
        picker_ranges2 = {};
    picker_ranges1[BooklyL10n.dateRange.anyTime] = [moment().subtract(100, 'years'), moment().add(100, 'years')];
    picker_ranges1[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges1[BooklyL10n.dateRange.today] = [moment(), moment()];
    picker_ranges1[BooklyL10n.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges1[BooklyL10n.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges1[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges1[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    picker_ranges2[BooklyL10n.dateRange.appAtAnyTime] = [pickers.appointmentDate.startDate, pickers.appointmentDate.endDate];
    picker_ranges2[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges2[BooklyL10n.dateRange.today] = [moment(), moment()];
    picker_ranges2[BooklyL10n.dateRange.tomorrow] = [moment().add(1, 'days'), moment().add(1, 'days')];
    picker_ranges2[BooklyL10n.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges2[BooklyL10n.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges2[BooklyL10n.dateRange.next_7] = [moment(), moment().add(7, 'days')];
    picker_ranges2[BooklyL10n.dateRange.next_30] = [moment(), moment().add(30, 'days')];
    picker_ranges2[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges2[BooklyL10n.dateRange.nextMonth] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];
    if (BooklyL10n.tasks.enabled) {
        picker_ranges2[BooklyL10n.tasks.title] = [moment(), moment().add(1, 'days')];
    }

    $creationDateFilter.daterangepicker(
        {
            parentEl: $creationDateFilter.parent(),
            startDate: pickers.creationDate.startDate,
            endDate: pickers.creationDate.endDate,
            ranges: picker_ranges1,
            showDropdowns: true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend({}, BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function(start, end, label) {
            switch (label) {
                case BooklyL10n.tasks.title:
                    $creationDateFilter
                        .data('date', 'null')
                        .find('span')
                        .html(BooklyL10n.tasks.title);
                    break;
                case BooklyL10n.dateRange.anyTime:
                    $creationDateFilter
                        .data('date', 'any')
                        .find('span')
                        .html(BooklyL10n.dateRange.anyTime);
                    break;
                default:
                    $creationDateFilter
                        .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                        .find('span')
                        .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
            }
        }
    );

    $appointmentDateFilter.daterangepicker(
        {
            parentEl: $appointmentDateFilter.parent(),
            startDate: pickers.appointmentDate.startDate,
            endDate: pickers.appointmentDate.endDate,
            ranges: picker_ranges2,
            showDropdowns: true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend({}, BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function (start, end, label) {
            switch (label) {
                case BooklyL10n.tasks.title:
                    $appointmentDateFilter
                        .data('date', 'null')
                        .find('span')
                        .html(BooklyL10n.tasks.title);
                    break;
                case BooklyL10n.dateRange.appAtAnyTime:
                    $appointmentDateFilter
                        .data('date', 'any')
                        .find('span')
                        .html(BooklyL10n.dateRange.appAtAnyTime);
                    break;
                default:
                    $appointmentDateFilter
                        .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                        .find('span')
                        .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
            }
        }
    );


    function onChangeFilter() {
        dt.ajax.reload();
    }

    $id_filter.on('keyup', onChangeFilter);
    $creationDateFilter.on('apply.daterangepicker', onChangeFilter);
    $appointmentDateFilter.on('apply.daterangepicker', onChangeFilter);
    $type_filter.on('change', onChangeFilter);
    $customer_filter.on('change', onChangeFilter);
    $staff_filter.on('change', onChangeFilter);
    $service_filter.on('change', onChangeFilter);
    $status_filter.on('change', onChangeFilter);

    /**
     * Delete payments.
     */
    $delete_button.on('click', function () {
        if (confirm(BooklyL10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();

            var data = [];
            var $checkboxes = $payments_list.find('tbody input:checked');
            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_delete_payments',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    data: data
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        dt.ajax.reload(null, false);
                    } else {
                        alert(response.data.message);
                    }
                    ladda.stop();
                }
            });
        }
    });
    $('li', $download_invoice).on('click', function () {
        if ($(this).hasClass('bookly-js-download-all-invoices')) {
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_get_payment_ids',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    filter: getFilter()
                },
                dataType: 'json',
                success: function (response) {
                    if (response.data.ids.length) {
                        window.location = $download_invoice.data('action') + '&invoices=' + response.data.ids.join(',');
                    }
                }
            });
        } else {
            var invoices = [];
            $payments_list.find('tbody input:checked').each(function () {
                invoices.push(this.value);
            });
            if (invoices.length) {
                window.location = $download_invoice.data('action') + '&invoices=' + invoices.join(',');
            }
        }
    });
});