jQuery(function ($) {
    let $dateFilter = $('#bookly-filter-date'),
        $based_on = $('#bookly-based-on'),
        pickerRanges = [];

    /**
     * Init date range pickers.
     */

    pickerRanges[BooklyL10n.dateRange.last_7]    = [moment().subtract(7, 'days'), moment()];
    pickerRanges[BooklyL10n.dateRange.last_30]   = [moment().subtract(30, 'days'), moment()];
    pickerRanges[BooklyL10n.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    pickerRanges[BooklyL10n.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $dateFilter.daterangepicker({
        parentEl : $dateFilter.parent(),
        startDate: moment().subtract(7, 'days'),
        endDate  : moment(),
        ranges   : pickerRanges,
        showDropdowns  : true,
        linkedCalendars: false,
        autoUpdateInput: false,
        locale: $.extend({},BooklyL10n.dateRange, BooklyL10n.datePicker)
    },
    function(start, end, label) {
        switch (label) {
            default:
                var format = 'YYYY-MM-DD';
                $dateFilter
                    .data('date', start.format(format) + ' - ' + end.format(format))
                    .find('span')
                    .html(start.format(BooklyL10n.dateRange.format) + ' - ' + end.format(BooklyL10n.dateRange.format));
        }
    } );

    $based_on.val(BooklyL10n.based_on);

    $dateFilter.on('apply.daterangepicker', function () {
        $(document.body).trigger('bookly.dateRange.changed', [$dateFilter.data('date'), $based_on.val()]);
    }).trigger('apply.daterangepicker');

    $based_on.on('change', function() {
        $(document.body).trigger('bookly.dateRange.changed', [$dateFilter.data('date'), $based_on.val()]);
    });
});