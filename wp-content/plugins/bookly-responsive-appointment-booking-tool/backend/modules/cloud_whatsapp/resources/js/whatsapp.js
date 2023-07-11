jQuery(function($) {
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
     * WhatsApp Details Tab.
     */
    $('[href="#details"]').one('click', function() {
        var $date_range = $('#whatsapp_date_range');
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
                    .html(start.format(BooklyL10nGlobal.dateRange.format) + ' - ' + end.format(BooklyL10nGlobal.dateRange.format));
            }
        );

        /**
         * Init Columns.
         */
        let columns = [];

        $.each(BooklyL10n.datatables.whatsapp_details.settings.columns, function(column, show) {
            if (show) {
                switch (column) {
                    case 'status':
                        columns.push({
                            data: column,
                            render: function(data, type, row, meta) {
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
            let dt = $('#bookly-messages').DataTable({
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
                            action: 'bookly_get_messages_list',
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

    $('#bookly-save', tabs.$settings)
        .on('click', function(e) {
            let ladda = Ladda.create(this);
            ladda.start();

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: booklySerialize.buildRequestData('bookly_cloud_whatsapp_save_settings', {
                    access_token: $('[name=access_token]', tabs.$settings).val(),
                    phone_id: $('[name=phone_id]', tabs.$settings).val(),
                    business_account_id: $('[name=business_account_id]', tabs.$settings).val(),
                }),
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    } else {
                        booklyAlert({success: [response.data.message]});
                    }
                    ladda.stop();
                }
            });
        });

    if (hash.length > 1) {
        switch (hash[1]) {
            case 'settings':
            case 'details':
                $('[href="#' + hash[1] + '"]').click()
                window.location.href = '#';
                break;
        }
    }
});