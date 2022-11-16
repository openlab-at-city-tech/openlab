jQuery(function($) {
    'use strict';

    const $date_range = $('#purchases_date_range');
    const $datatable = $('#bookly-purchases');

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

    $.each(BooklyL10n.datatables.cloud_purchases.settings.columns, function(column, show) {
        if (show) {
            if (column === 'amount') {
                columns.push({
                    data: column,
                    render: function(data, type, row, meta) {
                        const disabled = ['Pending', 'Rejected', 'Cancelled reversal'].includes(row.status);
                        return data >= 0
                            ? '<span class="text-' + (disabled ? 'muted' : 'success') + '">+ $' + data + '</span>'
                            : '<span class="text-' + (disabled ? 'muted' : 'danger') + '">- $' + data.substring(1) + '</span>';
                    }
                });
            } else {
                columns.push({data: column, render: $.fn.dataTable.render.text()});
            }
        }
    });
    columns.push({
        data: null,
        className: "text-right",
        render: function(data, type, row, meta) {
            if ((row.type === 'PayPal' || row.type === 'Card') && row.status === 'Paid') {
                return '<button type="button" class="btn btn-default" data-action="download-invoice"><i class="far fa-fw fa-file-pdf mr-1"></i> ' + BooklyL10n.invoice.button + '</button>';
            }
            return '';
        }
    });

    var dt = $datatable.DataTable({
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
                    action: 'bookly_get_purchases_list',
                    csrf_token: BooklyL10n.csrfToken,
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

    $datatable.on('click', '[data-action=download-invoice]', function() {
        if (BooklyL10n.invoice.valid) {
            const data = $('#bookly-purchases').DataTable().row($(this).closest('td')).data();
            window.location = BooklyL10n.invoice.link + '/' + data.id;
        } else {
            booklyAlert({error: [BooklyL10n.invoice.alert]});
        }
    });
});