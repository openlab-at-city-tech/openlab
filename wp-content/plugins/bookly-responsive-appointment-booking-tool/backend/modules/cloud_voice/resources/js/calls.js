jQuery(function ($) {
    'use strict';
    let tabs = {
            $settings: $('#settings')
        },
        hash = window.location.href.split('#');

    /**
     * Notifications Tab
     */
    BooklyNotificationsList();
    BooklyNotificationDialog();

    var $phone_input = $('#admin_phone');
    if (BooklyL10n.intlTelInput.enabled) {
        window.booklyIntlTelInput($phone_input.get(0), {
            preferredCountries: [BooklyL10n.intlTelInput.country],
            initialCountry: BooklyL10n.intlTelInput.country,
            geoIpLookup: function (callback) {
                $.get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
                    var countryCode = (resp && resp.country) ? resp.country : '';
                    callback(countryCode);
                });
            }
        });
    }

    $('#test_call').on('click', function (e) {
        e.preventDefault();
        $.ajax({
            url: ajaxurl,
            data: {
                action: 'bookly_make_test_call',
                csrf_token: BooklyL10nGlobal.csrf_token,
                phone_number: BooklyL10n.intlTelInput.enabled ? booklyGetPhoneNumber($phone_input.get(0)) : $phone_input.val()
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    booklyAlert({success: [response.data.message]});
                } else {
                    booklyAlert({error: [response.data.message]});
                }
            }
        });
    });

    $('[data-action=save-administrator-phone]')
        .on('click', function (e) {
            e.preventDefault();
            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_save_administrator_phone',
                    bookly_sms_administrator_phone: BooklyL10n.intlTelInput.enabled ? booklyGetPhoneNumber($phone_input.get(0)) : $phone_input.val(),
                    csrf_token: BooklyL10nGlobal.csrf_token
                },
                success: function (response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    }
                }
            });
        });

    /**
     * Date range pickers options.
     */
    var picker_ranges = {};
    picker_ranges[BooklyL10nGlobal.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10nGlobal.dateRange.today] = [moment(), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10n.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];
    var locale = $.extend({}, BooklyL10nGlobal.dateRange, BooklyL10nGlobal.datePicker);

    /**
     * Voice Details Tab.
     */
    $('[href="#details"]').one('click', function () {
        var $date_range = $('#voice_date_range');
        $date_range.daterangepicker(
            {
                parentEl: $date_range.parent(),
                startDate: moment().subtract(30, 'days'), // by default select "Last 30 days"
                ranges: picker_ranges,
                locale: locale,
                showDropdowns: true,
                linkedCalendars: false,
            },
            function (start, end) {
                var format = 'YYYY-MM-DD';
                $date_range
                    .data('date', start.format(format) + ' - ' + end.format(format))
                    .find('span')
                    .html(start.format(BooklyL10nGlobal.dateRange.format) + ' - ' + end.format(BooklyL10nGlobal.dateRange.format));
            }
        );

        /**
         * Init Columns.
         */
        let columns = [];

        $.each(BooklyL10n.datatables.voice_details.settings.columns, function (column, show) {
            if (show) {
                switch (column) {
                    case 'status':
                        columns.push({
                            data: column,
                            render: function (data, type, row, meta) {
                                return BooklyL10n.status.hasOwnProperty(data)
                                    ? BooklyL10n.status[data]
                                    : (data.charAt(0).toUpperCase() + data.slice(1)).replaceAll('-', ' ');
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
            let dt = $('#bookly-calls').DataTable({
                ordering: false,
                paging: false,
                info: false,
                searching: false,
                processing: true,
                responsive: true,
                ajax: {
                    url: ajaxurl,
                    data: function (d) {
                        return {
                            action: 'bookly_get_calls_list',
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
                },
                layout: {
                    bottomStart: 'paging',
                    bottomEnd: null
                }
            });

            function onChangeFilter() {
                dt.ajax.reload();
            }

            $date_range.on('apply.daterangepicker', onChangeFilter);
            $(this).on('click', function () {
                dt.ajax.reload(null, false);
            });
        }
    });

    $('#bookly-save', tabs.$settings)
        .on('click', function (e) {
            let ladda = Ladda.create(this);
            ladda.start();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_cloud_voice_save_settings',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    language: $('#bookly_cloud_voice_language', tabs.$settings).val(),
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    } else {
                        booklyAlert({success: [response.data.message]});
                    }
                    ladda.stop();
                }
            });
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

    $.each(BooklyL10n.datatables.voice_prices.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'country_iso_code':
                    columns.push({
                        data: column,
                        className: 'align-middle',
                        render: function (data, type, row, meta) {
                            return '<div class="iti__flag iti__' + data + '"></div>';
                        }
                    });
                    break;
                case 'call_price':
                    columns.push({
                        data: column,
                        className: "text-right",
                        render: function (data, type, row, meta) {
                            return formatPrice(data);
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
                data: {action: 'bookly_get_voice_price_list', csrf_token: BooklyL10nGlobal.csrf_token},
                dataSrc: 'list'
            },
            columns: columns,
            language: {
                zeroRecords: BooklyL10n.noResults,
                processing: BooklyL10n.processing
            },
            layout: {
                bottomStart: 'paging',
                bottomEnd: null
            }
        });
    }

    $('#bookly-js-test-voice-notifications').on('click', function () {
        BooklyTestingVoiceDialog.showDialog();
    });

    if (hash.length > 1) {
        switch (hash[1]) {
            case 'settings':
            case 'details':
            case 'price_list':
                $('[href="#' + hash[1] + '"]').click()
                window.location.href = '#';
                break;
        }
    }
});