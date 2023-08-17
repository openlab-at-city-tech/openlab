jQuery(function ($) {
    'use strict';
    let
        $appointmentsList = $('#bookly-appointments-list'),
        $checkAllButton = $('#bookly-check-all'),
        $idFilter = $('#bookly-filter-id'),
        $appointmentDateFilter = $('#bookly-filter-date'),
        $creationDateFilter = $('#bookly-filter-creation-date'),
        $staffFilter = $('#bookly-filter-staff'),
        $customerFilter = $('#bookly-filter-customer'),
        $serviceFilter = $('#bookly-filter-service'),
        $statusFilter = $('#bookly-filter-status'),
        $locationFilter = $('#bookly-filter-location'),
        $newAppointmentBtn = $('#bookly-new-appointment'),
        $printDialog = $('#bookly-print-dialog'),
        $printSelectAll = $('#bookly-js-print-select-all', $printDialog),
        $printButton = $(':submit', $printDialog),
        $exportDialog = $('#bookly-export-dialog'),
        $exportSelectAll = $('#bookly-js-export-select-all', $exportDialog),
        $exportForm = $('form', $exportDialog),
        $showDeleteConfirmation = $('#bookly-js-show-confirm-deletion'),
        isMobile = false,
        urlParts = document.URL.split('#'),
        columns = [],
        order = [],
        pickers = {
            dateFormat: 'YYYY-MM-DD',
            appointmentDate: {
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month'),
            },
            creationDate: {
                startDate: moment(),
                endDate: moment().add(100, 'years'),
            },
        },
        status_filtered = false
    ;

    try {
        document.createEvent('TouchEvent');
        isMobile = true;
    } catch (e) {}

    $statusFilter.booklyDropdown({onChange: function () { dt.ajax.reload(); }});

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
                pickers.creationDate.startDate = moment(params['1'].substring(0, 10));
                pickers.creationDate.endDate = moment(params['1'].substring(11));
                $creationDateFilter
                    .data('date', pickers.creationDate.startDate.format(pickers.dateFormat) + ' - ' + pickers.creationDate.endDate.format(pickers.dateFormat))
                    .find('span')
                    .html(pickers.creationDate.startDate.format(BooklyL10n.dateRange.format) + ' - ' + pickers.creationDate.endDate.format(BooklyL10n.dateRange.format));
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
    $.each(BooklyL10n.datatables.appointments.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'customer_full_name':
                    columns.push({data: 'customer.full_name', render: $.fn.dataTable.render.text()});
                    break;
                case 'customer_phone':
                    columns.push({
                        data: 'customer.phone',
                        render: function (data, type, row, meta) {
                            if (isMobile) {
                                return '<a href="tel:' + $.fn.dataTable.render.text().display(data) + '">' + $.fn.dataTable.render.text().display(data) + '</a>';
                            } else {
                                return $.fn.dataTable.render.text().display(data);
                            }
                        }
                    });
                    break;
                case 'customer_email':
                    columns.push({data: 'customer.email', render: $.fn.dataTable.render.text()});
                    break;
                case 'customer_address':
                    columns.push({data: 'customer.address', render: $.fn.dataTable.render.text(), orderable: false});
                    break;
                case 'customer_birthday':
                    columns.push({data: 'customer.birthday', render: $.fn.dataTable.render.text()});
                    break;
                case 'staff_name':
                    columns.push({data: 'staff.name', render: $.fn.dataTable.render.text()});
                    break;
                case 'service_title':
                    columns.push({
                        data: 'service.title',
                        render: function (data, type, row, meta) {
                            data = $.fn.dataTable.render.text().display(data);
                            if (row.service.extras.length) {
                                var extras = '<ul class="bookly-list list-dots">';
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
                case 'attachments':
                    columns.push({
                        data: 'attachment',
                        render: function (data, type, row, meta) {
                            if (data == '1') {
                                return '<button type="button" class="btn btn-link" data-action="show-attachments" title="' + BooklyL10n.attachments + '"><i class="fas fa-fw fa-paperclip"></i></button>';
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
                                return '<a href="#" data-toggle="bookly-popover" data-trigger="hover" data-placement="bottom" data-content="' + $.fn.dataTable.render.text().display(row.rating_comment) + '" data-container="#bookly-appointments-list">' + $.fn.dataTable.render.text().display(row.rating) + '</a>';
                            }
                        },
                    });
                    break;
                case 'internal_note':
                case 'locations':
                case 'notes':
                case 'number_of_persons':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'online_meeting':
                    columns.push({
                        data: 'online_meeting_provider',
                        render: function (data, type, row, meta) {
                            switch (data) {
                                case 'zoom':
                                    return '<a class="badge badge-primary" href="https://zoom.us/j/' + $.fn.dataTable.render.text().display(row.online_meeting_id) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Zoom <i class="fas fa-external-link-alt fa-fw"></i></a>';
                                case 'google_meet':
                                    return '<a class="badge badge-primary" href="' + $.fn.dataTable.render.text().display(row.online_meeting_id) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Google Meet <i class="fas fa-external-link-alt fa-fw"></i></a>';
                                case 'jitsi':
                                    return '<a class="badge badge-primary" href="' + $.fn.dataTable.render.text().display(row.online_meeting_id) + '" target="_blank"><i class="fas fa-video fa-fw"></i> Jitsi Meet <i class="fas fa-external-link-alt fa-fw"></i></a>';
                                case 'bbb':
                                    return '<a class="badge badge-primary" href="' + $.fn.dataTable.render.text().display(row.online_meeting_id) + '" target="_blank"><i class="fas fa-video fa-fw"></i> BigBlueButton <i class="fas fa-external-link-alt fa-fw"></i></a>';
                                default:
                                    return '';
                            }
                        },
                    });
                    break;
                default:
                    if (column.startsWith('custom_fields_')) {
                        columns.push({
                            data: column.replace(/_([^_]*)$/, '.$1'),
                            render: $.fn.dataTable.render.text(),
                            orderable: false
                        });
                    } else {
                        columns.push({data: column, render: $.fn.dataTable.render.text()});
                    }
                    break;
            }
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        width: 120,
        render: function (data, type, row, meta) {
            return '<button type="button" class="btn btn-default" data-action="edit"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></button>';
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        render: function (data, type, row, meta) {
            const cb_id = row.ca_id === null ? 'bookly-dt-a-' + row.id : 'bookly-dt-ca-' + row.ca_id;
            return '<div class="custom-control custom-checkbox">' +
                '<input value="' + row.ca_id + '" data-appointment="' + row.id + '" id="' + cb_id + '" type="checkbox" class="custom-control-input">' +
                '<label for="' + cb_id + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    $.each(BooklyL10n.datatables.appointments.settings.order, function (_, value) {
        const index = columns.findIndex(function (c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });
    /**
     * Init DataTables.
     */
    var dt = $appointmentsList.DataTable({
        order: order,
        info: false,
        searching: false,
        lengthChange: false,
        processing: true,
        responsive: true,
        pageLength: 25,
        pagingType: 'numbers',
        serverSide: true,
        drawCallback: function (settings) {
            $('[data-toggle="bookly-popover"]', $appointmentsList).on('click', function (e) {
                e.preventDefault();
            }).booklyPopover();
            dt.responsive.recalc();
        },
        ajax: {
            url: ajaxurl,
            type: 'POST',
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
        dom: "<'row'<'col-sm-12'tr>><'row float-left mt-3'<'col-sm-12'p>>",
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing: BooklyL10n.processing
        }
    });

    // Show ratings in expanded rows.
    dt.on('responsive-display', function (e, datatable, row, showHide, update) {
        if (showHide) {
            $('[data-toggle="bookly-popover"]', row.child()).on('click', function (e) {
                e.preventDefault();
            }).booklyPopover();
        }
    });

    /**
     * Add appointment.
     */
    $newAppointmentBtn.on('click', function () {
        BooklyAppointmentDialog.showDialog(
            null,
            null,
            moment(),
            function (event) {
                dt.ajax.reload();
            }
        )
    });

    /**
     * Export.
     */
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

    $('.bookly-js-columns input', $exportDialog)
        .on('change', function () {
            $exportSelectAll.prop('checked', $('.bookly-js-columns input:checked', $exportDialog).length == $('.bookly-js-columns input', $exportDialog).length);
        });

    /**
     * Print.
     */
    $printButton.on('click', function () {
        let columns = [];
        $('input:checked', $printDialog).each(function () {
            columns.push(this.value);
        });
        let config = {
            title: '&nbsp;',
            exportOptions: {
                columns: columns
            },
            customize: function (win) {
                win.document.firstChild.style.backgroundColor = '#fff';
                win.document.body.id = 'bookly-tbs';
                $(win.document.body).find('table').removeClass('bookly-collapsed');
                $(win.document.head).append('<style>@page{size: auto;}</style>');
            }
        };
        $.fn.dataTable.ext.buttons.print.action(null, dt, null, $.extend({}, $.fn.dataTable.ext.buttons.print, config));
    });

    $printSelectAll
        .on('click', function () {
            let checked = this.checked;
            $('.bookly-js-columns input', $printDialog).each(function () {
                $(this).prop('checked', checked);
            });
        });

    $('.bookly-js-columns input', $printDialog)
        .on('change', function () {
            $printSelectAll.prop('checked', $('.bookly-js-columns input:checked', $printDialog).length == $('.bookly-js-columns input', $printDialog).length);
        });

    /**
     * Select all appointments.
     */
    $checkAllButton.on('change', function () {
        $appointmentsList.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $appointmentsList
        // On appointment select.
        .on('change', 'tbody input:checkbox', function () {
            $checkAllButton.prop('checked', $appointmentsList.find('tbody input:not(:checked)').length == 0);
        })
        // Show payment details
        .on('click', '[data-action=show-payment]', function () {
            BooklyPaymentDetailsDialog.showDialog({
                payment_id: getDTRowData(this).payment_id,
                done: function (event) {
                    dt.ajax.reload();
                }
            });
        })
        // Edit appointment.
        .on('click', '[data-action=edit]', function (e) {
            e.preventDefault();
            BooklyAppointmentDialog.showDialog(
                getDTRowData(this).id,
                null,
                null,
                function (event) {
                    dt.ajax.reload();
                }
            )
        });

    $showDeleteConfirmation.on('click', function () {
        let data = [],
            $checkboxes = $appointmentsList.find('tbody input:checked');

        $checkboxes.each(function () {
            data.push({ca_id: this.value, id: $(this).data('appointment')});
        });

        new BooklyConfirmDeletingAppointment({
                action: 'bookly_delete_customer_appointments',
                data: data,
                csrf_token: BooklyL10nGlobal.csrf_token,
            },
            function (response) {dt.draw(false);}
        );
    });

    /**
     * Init date range pickers.
     */

    let
        pickerRanges1 = {},
        pickerRanges2 = {}
    ;
    pickerRanges1[BooklyL10n.dateRange.anyTime] = [moment(), moment().add(100, 'years')];
    pickerRanges1[BooklyL10n.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    pickerRanges1[BooklyL10n.dateRange.today] = [moment(), moment()];
    pickerRanges1[BooklyL10n.dateRange.tomorrow] = [moment().add(1, 'days'), moment().add(1, 'days')];
    pickerRanges1[BooklyL10n.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    pickerRanges1[BooklyL10n.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    pickerRanges1[BooklyL10n.dateRange.next_7] = [moment(), moment().add(7, 'days')];
    pickerRanges1[BooklyL10n.dateRange.next_30] = [moment(), moment().add(30, 'days')];
    pickerRanges1[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    pickerRanges1[BooklyL10n.dateRange.nextMonth] = [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')];

    pickerRanges2[BooklyL10n.dateRange.anyTime] = pickerRanges1[BooklyL10n.dateRange.anyTime];
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

    function getDTRowData(element) {
        let $el = $(element).closest('td');
        if ($el.hasClass('child')) {
            $el = $el.closest('tr').prev();
        }
        return dt.row($el).data();
    }

    $idFilter.on('keyup', function () { dt.ajax.reload(); });
    $appointmentDateFilter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $creationDateFilter.on('apply.daterangepicker', function () { dt.ajax.reload(); });
    $staffFilter.on('change', function () { dt.ajax.reload(); });
    $customerFilter.on('change', function () { dt.ajax.reload(); });
    $serviceFilter.on('change', function () { dt.ajax.reload(); });
    $locationFilter.on('change', function () { dt.ajax.reload(); });
});