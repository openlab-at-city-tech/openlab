jQuery(function ($) {
    'use strict';
    let
        $appointmentsList = $('#bookly-appointments-datatables'),
        $idFilter = $('#bookly-filter-id'),
        $appointmentDateFilter = $('#bookly-filter-date'),
        $creationDateFilter = $('#bookly-filter-creation-date'),
        $staffFilter = $('#bookly-filter-staff'),
        $customerFilter = $('#bookly-filter-customer'),
        $serviceFilter = $('#bookly-filter-service'),
        $statusFilter = $('#bookly-filter-status'),
        $locationFilter = $('#bookly-filter-location'),
        $printDialog = $('#bookly-print-dialog'),
        $printSelectAll = $('#bookly-js-print-select-all', $printDialog),
        $printButton = $(':submit', $printDialog),
        $exportDialog = $('#bookly-export-dialog'),
        $exportSelectAll = $('#bookly-js-export-select-all', $exportDialog),
        $exportForm = $('form', $exportDialog),
        isMobile = false,
        urlParts = document.URL.split('#'),
        columns = [],
        pickers = {
            dateFormat: 'YYYY-MM-DD',
            appointmentDate: {
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            },
            creationDate: {
                startDate: moment().subtract(100, 'years'),
                endDate: moment().add(100, 'years'),
            },
        },
        status_filtered = false
    ;

    try {
        document.createEvent('TouchEvent');
        isMobile = true;
    } catch (e) {}

    function onChangeFilter() {
        bt.reload();
    }

    $statusFilter.booklyDropdown({onChange: onChangeFilter});

    $('.bookly-js-select').val(null);

    // Apply filter from anchor
    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function (part) {
            var params = part.split('=');
            if (params[0] === 'appointment-date') {
                if (params['1'] === 'any') {
                    $appointmentDateFilter
                        .data('date', 'any').find('span')
                        .html(BooklyL10n.dateRange.anyTime);
                } else {
                    pickers.appointmentDate.startDate = moment(params['1'].substring(0, 10));
                    pickers.appointmentDate.endDate = moment(params['1'].substring(11));
                    $appointmentDateFilter
                        .data('date', pickers.appointmentDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.appointmentDate.endDate.format(pickers.dateFormat))
                        .find('span')
                        .html(pickers.appointmentDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.appointmentDate.endDate.format(BooklyL10n.dateRange.format));
                }
            } else if (params[0] === 'tasks') {
                $appointmentDateFilter
                    .data('date', 'null').find('span')
                    .html(BooklyL10n.tasks.title);
            } else if (params[0] === 'created-date') {
                if (params['1'] === 'any') {
                    $creationDateFilter
                        .data('date', 'any').find('span')
                        .html(BooklyL10n.dateRange.createdAtAnyTime);
                } else {
                    pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                    pickers.creationDate.endDate = moment(params['1'].substring(11));
                    $creationDateFilter
                        .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                        .find('span')
                        .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
                }
            } else if (params[0] === 'status') {
                status_filtered = true;
                if (params[1] == 'any') {
                    $statusFilter.booklyDropdown('selectAll');
                } else {
                    $statusFilter.booklyDropdown('setSelected', params[1].split(','));
                }
            } else {
                $('#bookly-filter-' + params[0]).val(params[1]);
            }
        });
    } else {
        $.each(BooklyL10n.datatables.appointments.settings.filter, function (field, value) {
            if (field !== 'status') {
                if (value != '') {
                    $('#bookly-filter-' + field).val(value);
                }
                // check if select has correct values
                if ($('#bookly-filter-' + field).prop('type') == 'select-one') {
                    if ($('#bookly-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                        $('#bookly-filter-' + field).val(null);
                    }
                }
            }
        });
    }

    if (!status_filtered) {
        if (BooklyL10n.datatables.appointments.settings.filter.status) {
            $statusFilter.booklyDropdown('setSelected', BooklyL10n.datatables.appointments.settings.filter.status);
        } else {
            $statusFilter.booklyDropdown('selectAll');
        }
    }

    Ladda.bind($('button[type=submit]', $exportForm).get(0), {timeout: 2000});

    /**
     * Init table columns.
     */
    const table = 'appointments';

    $.each(BooklyL10n.datatables.appointments.settings.columns, function (column, show) {
        switch (column) {
            case 'customer_full_name':
                columns.push({data: 'customer.full_name', render: BooklyDatatables.escapeHtml()});
                break;
            case 'customer_phone':
                columns.push({
                    data: 'customer.phone',
                    render: function (data, type, row, meta) {
                        if (isMobile) {
                            return '<a href="tel:' + window.booklyIntlTelInput.utils.formatNumber(BooklyDatatables.escapeHtml(data), null, window.booklyIntlTelInput.utils.numberFormat.INTERNATIONAL) + '">' + BooklyDatatables.escapeHtml(data) + '</a>';
                        } else {
                            return data ? '<span style="white-space: nowrap;">' + window.booklyIntlTelInput.utils.formatNumber(BooklyDatatables.escapeHtml(data), null, window.booklyIntlTelInput.utils.numberFormat.INTERNATIONAL) + '</span>' : '';
                        }
                    }
                });
                break;
            case 'customer_email':
                columns.push({data: 'customer.email', render: BooklyDatatables.escapeHtml()});
                break;
            case 'customer_address':
                columns.push({data: 'customer.address', render: BooklyDatatables.escapeHtml(), orderable: false});
                break;
            case 'customer_birthday':
                columns.push({data: 'customer.birthday', render: BooklyDatatables.escapeHtml()});
                break;
            case 'staff_name':
                columns.push({data: 'staff.name', render: BooklyDatatables.escapeHtml()});
                break;
            case 'service_title':
                columns.push({
                    data: 'service.title',
                    render: function (data, type, row, meta) {
                        data = BooklyDatatables.escapeHtml(data);
                        if (row.service.extras.length) {
                            var extras = '<ul class="bookly-list list-dots bookly:m-0">';
                            $.each(row.service.extras, function (key, item) {
                                extras += '<li><nobr>' + item.title + '</nobr></li>';
                            });
                            extras += '</ul>';
                            return data + extras;
                        } else {
                            return data;
                        }
                    }
                });
                break;
            case 'payment':
                columns.push({
                    data: 'payment',
                    render: function (data, type, row, meta) {
                        if (row.payment_id) {
                            return '<a type="button" data-action="show-payment" class="text-primary" data-payment_id="' + row.payment_id + '">' + data + '</a>';
                        }
                        return '';
                    }
                });
                break;
            case 'service_duration':
                columns.push({data: 'service.duration'});
                break;
            case 'service_price':
                columns.push({data: 'service.price'});
                break;
            case 'attachments':
                columns.push({
                    data: 'attachment',
                    render: function (data, type, row, meta) {
                        if (data == '1') {
                            return '<button type="button" class="btn btn-link p-0" data-action="show-attachments" title="' + BooklyL10n.attachments + '"><i class="fas fa-fw fa-paperclip"></i></button>';
                        }
                        return '';
                    }
                });
                break;
            case 'rating':
                columns.push({
                    data: 'rating',
                    render: function (data, type, row, meta) {
                        if (row.rating_comment == null) {
                            return row.rating;
                        } else {
                            return '<a href="#" data-toggle="bookly-popover" data-trigger="hover" data-placement="bottom" data-content="' + BooklyDatatables.escapeHtml(row.rating_comment) + '" data-container="#bookly-appointments-datatables">' + BooklyDatatables.escapeHtml(row.rating) + '</a>';
                        }
                    },
                });
                break;
            case 'internal_note':
            case 'locations':
            case 'notes':
            case 'number_of_persons':
                columns.push({data: column, render: BooklyDatatables.escapeHtml()});
                break;
            case 'online_meeting':
                columns.push({
                    data: 'online_meeting_provider',
                    render: function (data, type, row, meta) {
                        switch (data) {
                            case 'zoom':
                                return '<a class="badge badge-primary" href="https://zoom.us/j/' + BooklyDatatables.escapeHtml(row.online_meeting_start_url) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Zoom <i class="fas fa-external-link-alt fa-fw"></i></a>';
                            case 'google_meet':
                                return '<a class="badge badge-primary" href="' + BooklyDatatables.escapeHtml(row.online_meeting_start_url) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Google Meet <i class="fas fa-external-link-alt fa-fw"></i></a>';
                            case 'jitsi':
                                return '<a class="badge badge-primary" href="' + BooklyDatatables.escapeHtml(row.online_meeting_start_url) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Jitsi Meet <i class="fas fa-external-link-alt fa-fw"></i></a>';
                            case 'bbb':
                                return '<a class="badge badge-primary" href="' + BooklyDatatables.escapeHtml(row.online_meeting_start_url) + '" target="_blank"><i class="fas fa-video fa-fw"></i> BigBlueButton <i class="fas fa-external-link-alt fa-fw"></i></a>';
                            case 'teams':
                                return '<a class="badge badge-primary" href="' + BooklyDatatables.escapeHtml(row.online_meeting_start_url) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Microsoft Teams <i class="fas fa-external-link-alt fa-fw"></i></a>';
                            default:
                                return '';
                        }
                    },
                });
                break;
            case 'id':
                columns.push({data: column, render: BooklyDatatables.escapeHtml()});
                break;
            default:
                if (column.startsWith('custom_fields_')) {
                    columns.push({
                        data: column.replace(/_([^_]*)$/, '.$1'),
                        render: BooklyDatatables.escapeHtml(),
                        orderable: false
                    });
                } else {
                    columns.push({data: column, render: BooklyDatatables.escapeHtml()});
                }
                break;
        }
        columns[columns.length - 1].title = BooklyL10n.datatables[table].titles[column] || column;
        columns[columns.length - 1].name = column;
        columns[columns.length - 1].show = show;
    });

    let options = {
        ajax: {
            url: ajaxurl,
            method: 'POST',
            data: function (d) {
                return $.extend({action: 'bookly_get_appointments', csrf_token: BooklyL10nGlobal.csrf_token}, {
                    filter: {
                        id: $idFilter.val(),
                        date: $appointmentDateFilter.data('date'),
                        created_date: $creationDateFilter.data('date'),
                        staff: $staffFilter.val(),
                        customer: $customerFilter.val(),
                        service: $serviceFilter.val(),
                        status: $statusFilter.booklyDropdown('getSelected'),
                        location: $locationFilter.val()
                    }
                }, d);
            }
        },
        columns: columns,
        rows: BooklyL10n.datatables[table].settings.page_length || 25,
        order: BooklyL10n.datatables[table].settings.order,
        l10n: {
            zeroRecords: BooklyL10n.zeroRecords,
            emptyTable: BooklyL10n.emptyTable,
            rowsPerPage: BooklyL10n.rowsPerPage,
        },
        edit: function (row) {
            BooklyAppointmentDialog.showDialog(
                row.id,
                null,
                null,
                function (event) {
                    bt.reload();
                }
            )
        },
        checked: function (rows) {
            return '<button type="button" title="' + BooklyL10n.delete + '" class="bookly:btn bookly:btn-xs bookly:btn-white" data-action="delete"><i class="far fa-fw fa-trash-alt mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.delete + '</span></button>';
        },
        getId(row) {
            return row.id + '-' + parseInt(row.ca_id);
        },
        saveSettings: function (settings) {
            $.post(
                ajaxurl,
                Object.assign(
                    {
                        action: 'bookly_update_table_settings',
                        table: table,
                        csrf_token: BooklyL10nGlobal.csrf_token
                    },
                    settings
                )
            );
        },
        filters: {
            'id': 'bookly-' + table + '-datatables-filters',
            'selected': function () {
                let filters = {};
                if ($idFilter.val() !== '') {
                    filters.id = {
                        title: BooklyL10n.filters.id,
                        value: $idFilter.val(),
                        reset: function () {
                            $idFilter.val('');
                            onChangeFilter();
                        }
                    };
                }
                let _date = $appointmentDateFilter.data('date');
                if (_date !== 'any') {
                    let parts = _date.split(' - ');
                    filters.date = {
                        title: BooklyL10n.filters.date,
                        value: _date === 'null' ? BooklyL10n.tasks.title : moment(parts[0].trim(), 'YYYY-MM-DD').format(BooklyL10n.dateRange.format) + ' - ' + moment(parts[1].trim(), 'YYYY-MM-DD').format(BooklyL10n.dateRange.format),
                        reset: function () {
                            $appointmentDateFilter
                                .data('date', 'any')
                                .find('span')
                                .html(BooklyL10n.dateRange.anyTime);
                            onChangeFilter();
                        }
                    };
                }
                let _created = $creationDateFilter.data('date');
                if (_created !== 'any') {
                    let parts = _created.split(' - ');
                    filters.created = {
                        title: BooklyL10n.filters.created,
                        value: moment(parts[0].trim(), 'YYYY-MM-DD').format(BooklyL10n.dateRange.format) + ' - ' + moment(parts[1].trim(), 'YYYY-MM-DD').format(BooklyL10n.dateRange.format),
                        reset: function () {
                            $creationDateFilter
                                .data('date', 'any')
                                .find('span')
                                .html(BooklyL10n.dateRange.anyTime);
                            onChangeFilter();
                        }
                    };
                }
                if ($customerFilter.length && $customerFilter.val() !== null) {
                    filters.customer = {
                        title: BooklyL10n.filters.customer,
                        value: $customerFilter.get(0).options[$customerFilter.get(0).selectedIndex].text,
                        reset: function () {
                            $customerFilter.val(null).trigger('change');
                            onChangeFilter();
                        }
                    };
                }
                if ($staffFilter.length && $staffFilter.val() !== null) {
                    filters.staff = {
                        title: BooklyL10n.filters.staff,
                        value: $staffFilter.get(0).options[$staffFilter.get(0).selectedIndex].text,
                        reset: function () {
                            $staffFilter.val(null).trigger('change');
                            onChangeFilter();
                        }
                    };
                }
                if ($serviceFilter.length && $serviceFilter.val() !== null) {
                    filters.service = {
                        title: BooklyL10n.filters.service,
                        value: $serviceFilter.get(0).options[$serviceFilter.get(0).selectedIndex].text,
                        reset: function () {
                            $serviceFilter.val(null).trigger('change');
                            onChangeFilter();
                        }
                    };
                }
                if ($locationFilter.length && $locationFilter.val() !== null) {
                    filters.location = {
                        title: BooklyL10n.filters.location,
                        value: $locationFilter.get(0).options[$locationFilter.get(0).selectedIndex].text,
                        reset: function () {
                            $locationFilter.val(null).trigger('change');
                            onChangeFilter();
                        }
                    };
                }
                let _selected = $statusFilter.booklyDropdown('getSelectedExt');
                if (_selected.length > 0 && _selected.length < $statusFilter.booklyDropdown('itemsCount')) {
                    let _value = [];
                    _selected.forEach(function (item) {
                        _value.push(item.name.trim());
                    })
                    filters.status = {
                        title: BooklyL10n.filters.status,
                        value: _value.join(', '),
                        reset: function () {
                            $statusFilter.booklyDropdown('selectAll');
                            onChangeFilter();
                        }
                    }
                }

                return filters;
            }
        },
        topToolbar: [
            {
                id: 'bookly-new-appointment',
                click: function () {
                    BooklyAppointmentDialog.showDialog(
                        null,
                        null,
                        moment(),
                        function (event) {
                            bt.reload();
                        }
                    )
                },
                content: '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus-lg" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 2a.5.5 0 0 1 .5.5v5h5a.5.5 0 0 1 0 1h-5v5a.5.5 0 0 1-1 0v-5h-5a.5.5 0 0 1 0-1h5v-5A.5.5 0 0 1 8 2"/></svg>' + BooklyL10n.new_appointment
            }
        ],
        drawCallback: function () {
            $('[data-toggle="bookly-popover"]', $appointmentsList).on('click', function (e) {
                e.preventDefault();
            }).booklyPopover();
        },
    }
    if (BooklyL10n.proEnabled) {
        options['settingsToolbar'] = function () {
            let buttons = '';
            buttons += '<button type="button" title="' + BooklyL10n.export + '" id="bookly-js-show-export-dialog" class="bookly:btn bookly:btn-sm bookly:btn-secondary" data-action="export"><i class="far fa-fw fa-share-square mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.export + '</span></button>';
            buttons += '<button type="button" title="' + BooklyL10n.print + '" id="bookly-js-show-print-dialog" class="bookly:btn bookly:btn-sm bookly:btn-secondary" data-action="print"><i class="fas fa-fw fa-print mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.print + '</span></button>';
            return buttons;
        }
    }
    let bt = BooklyDatatables.showForm('bookly-' + table + '-datatables', options);

    /**
     * Export.
     */
    $appointmentsList.on('click', '[data-action=export]', function () {
        let columnsHtml = '';
        bt.getColumns().forEach(function (column, index) {
            columnsHtml += '<div class="custom-control custom-checkbox"><input class="custom-control-input" id="bookly-ea-' + index + '" name="exp[' + column.name + ']" type="checkbox"' + (column.show ? 'checked' : '') + '><label class="custom-control-label" for="bookly-ea-' + index + '">' + column.title + '</label></div>';
        })
        $('.bookly-js-columns', $exportDialog).html(columnsHtml);
        $exportDialog.booklyModal('show');
    });
    $exportForm.on('submit', function () {
        $('[name="filter"]', $exportDialog).val(JSON.stringify({
            id: $idFilter.val(),
            date: $appointmentDateFilter.data('date'),
            created_date: $creationDateFilter.data('date'),
            staff: $staffFilter.val(),
            customer: $customerFilter.val(),
            service: $serviceFilter.val(),
            status: $statusFilter.booklyDropdown('getSelected'),
            location: $locationFilter.val(),
        }));
        $exportDialog.booklyModal('hide');

        return true;
    });

    $exportSelectAll
        .on('click', function () {
            let checked = this.checked;
            $('.bookly-js-columns input', $exportDialog).each(function () {
                $(this).prop('checked', checked);
            });
        });

    $exportDialog
        .on('change', '.bookly-js-columns input', function () {
            $exportSelectAll.prop('checked', $('.bookly-js-columns input:checked', $exportDialog).length == $('.bookly-js-columns input', $exportDialog).length);
        });

    /**
     * Print.
     */
    $appointmentsList.on('click', '[data-action=print]', function () {
        let columnsHtml = '';
        bt.getColumns().forEach(function (column, index) {
            columnsHtml += '<div class="custom-control custom-checkbox"><input class="custom-control-input" id="bookly-pa-' + index + '" value="' + index + '" type="checkbox"' + (column.show ? 'checked' : '') + '><label class="custom-control-label" for="bookly-pa-' + index + '">' + column.title + '</label></div>';
        })
        $('.bookly-js-columns', $printDialog).html(columnsHtml);
        $printDialog.booklyModal('show');
    });
    $printButton.on('click', function () {
        let columns = [];
        $('.bookly-js-columns input:checked', $printDialog).each(function () {
            columns.push(parseInt(this.value));
        });
        bt.print(columns);
    });

    $printSelectAll
        .on('click', function () {
            let checked = this.checked;
            $('.bookly-js-columns input', $printDialog).each(function () {
                $(this).prop('checked', checked);
            });
        });

    $printDialog
        .on('change', '.bookly-js-columns input', function () {
            $printSelectAll.prop('checked', $('.bookly-js-columns input:checked', $printDialog).length == $('.bookly-js-columns input', $printDialog).length);
        });

    $appointmentsList
        // Show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: bt.getRowData().payment_id,
                done: function (event) {
                    bt.reload();
                }
            });
        })
        .on('click', '[data-action=delete]', function (e) {
            let data = [],
                rows = bt.getCheckedRows();

            rows.forEach(function (row) {
                data.push({ca_id: row.ca_id ? row.ca_id : 'null', id: row.id});
            });

            new BooklyConfirmDeletingAppointment({
                    action: 'bookly_delete_customer_appointments',
                    data: data,
                    csrf_token: BooklyL10nGlobal.csrf_token,
                },
                function (response) {bt.reload();}
            );
        });

    /**
     * Init date range pickers.
     */

    let
        pickerRanges1 = {},
        pickerRanges2 = {}
    ;
    pickerRanges1[BooklyL10n.dateRange.anyTime] = [moment().subtract(100, 'years'), moment().add(100, 'years')];
    pickerRanges1[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    pickerRanges1[BooklyL10n.dateRange.today] = [moment(), moment()];
    pickerRanges1[BooklyL10n.dateRange.tomorrow] = [moment().add(1, 'days'), moment().add(1, 'days')];
    pickerRanges1[BooklyL10n.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    pickerRanges1[BooklyL10n.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    pickerRanges1[BooklyL10n.dateRange.next_7] = [moment(), moment().add(7, 'days')];
    pickerRanges1[BooklyL10n.dateRange.next_30] = [moment(), moment().add(30, 'days')];
    pickerRanges1[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    pickerRanges1[BooklyL10n.dateRange.nextMonth] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

    pickerRanges2[BooklyL10n.dateRange.anyTime] = [pickers.creationDate.startDate, pickers.creationDate.endDate];
    pickerRanges2[BooklyL10n.dateRange.yesterday] = pickerRanges1[BooklyL10n.dateRange.yesterday];
    pickerRanges2[BooklyL10n.dateRange.today] = pickerRanges1[BooklyL10n.dateRange.today];
    pickerRanges2[BooklyL10n.dateRange.last_7] = pickerRanges1[BooklyL10n.dateRange.last_7];
    pickerRanges2[BooklyL10n.dateRange.last_30] = pickerRanges1[BooklyL10n.dateRange.last_30];
    pickerRanges2[BooklyL10n.dateRange.thisMonth] = pickerRanges1[BooklyL10n.dateRange.thisMonth];

    if (BooklyL10n.tasks.enabled) {
        pickerRanges1[BooklyL10n.tasks.title] = [moment(), moment().add(1, 'days')];
    }
    $appointmentDateFilter.daterangepicker(
        {
            parentEl: $appointmentDateFilter.parent(),
            startDate: pickers.appointmentDate.startDate,
            endDate: pickers.appointmentDate.endDate,
            ranges: pickerRanges1,
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
                case BooklyL10n.dateRange.anyTime:
                    $appointmentDateFilter
                        .data('date', 'any')
                        .find('span')
                        .html(BooklyL10n.dateRange.anyTime);
                    break;
                default:
                    $appointmentDateFilter
                        .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                        .find('span')
                        .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
            }
        }
    );

    $creationDateFilter.daterangepicker(
        {
            parentEl: $creationDateFilter.parent(),
            startDate: pickers.creationDate.startDate,
            endDate: pickers.creationDate.endDate,
            ranges: pickerRanges2,
            showDropdowns: true,
            linkedCalendars: false,
            autoUpdateInput: false,
            locale: $.extend(BooklyL10n.dateRange, BooklyL10n.datePicker)
        },
        function (start, end, label) {
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
                        .html(BooklyL10n.dateRange.createdAtAnyTime);
                    break;
                default:
                    $creationDateFilter
                        .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                        .find('span')
                        .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
            }
        }
    );

    /**
     * On filters change.
     */
    $('.bookly-js-select')
        .booklySelect2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language: {
                noResults: function () {
                    return BooklyL10n.no_result_found;
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

    $idFilter.on('keyup', onChangeFilter);
    $appointmentDateFilter.on('apply.daterangepicker', onChangeFilter);
    $creationDateFilter.on('apply.daterangepicker', onChangeFilter);
    $staffFilter.on('change', onChangeFilter);
    $customerFilter.on('change', onChangeFilter);
    $serviceFilter.on('change', onChangeFilter);
    $locationFilter.on('change', onChangeFilter);
});