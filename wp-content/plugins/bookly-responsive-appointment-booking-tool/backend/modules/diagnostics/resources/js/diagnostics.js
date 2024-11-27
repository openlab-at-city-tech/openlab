jQuery(function ($) {
    'use strict';

    let reloadTestButtons = [];
    // Common
    let url_params = {};
    try {
        let queryString = window.location.search[0] === '?'
            ? window.location.search.slice(1)
            : window.location.search;
        let queries = queryString.split('&');

        queries.forEach(function (query) {
            let pair = query.split('='),
                key = decodeURIComponent(pair[0]);

            url_params[key] = decodeURIComponent(pair[1] || '');
        });
    } catch (e) { }

    function setCookie(name, value) {
        let expires = new Date();
        expires.setFullYear(expires.getFullYear() + 3);
        document.cookie = name + '=' + (value || '') + ';expires=' + expires.toUTCString() + ';path=/';
    }

    function getCookie(name) {
        let keyValue = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
        return keyValue ? keyValue[2] : null;
    }

    // All tests
    function runAllTests() {
        reloadTestButtons = [];
        $('.bookly-js-tests .bookly-js-reload-test').each(function () {
            reloadTestButtons.push($(this));
        });
        runNextTest();
    }

    function runNextTest() {
        if (reloadTestButtons.length > 0) {
            reloadTestButtons[0].trigger('click');
            reloadTestButtons.shift();
        }
    }

    $('.bookly-js-reload-test').on('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        let $test = $(this).closest('.bookly-js-test'),
            $loading = $test.find('.bookly-js-loading-test'),
            $reload = $test.find('.bookly-js-reload-test'),
            $success = $test.find('.bookly-js-success-test'),
            $failed = $test.data('error-type') === 'warning' ? $test.find('.bookly-js-warning-test') : $test.find('.bookly-js-failed-test'),
            $errors = $test.find('.bookly-js-test-errors')
        ;
        $loading.show();
        $reload.hide();
        $success.hide();
        $failed.hide();
        $errors.hide();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_run_diagnostics_test',
                test: $test.data('class'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            error: function () {
                $loading.hide();
                $reload.show();
                $failed.show();
            }
        }).then(function (response) {
            if ($test.data('test') !== 'check-sessions') {
                $loading.hide();
                $reload.show();
                if (response.success) {
                    $success.show();
                } else {
                    $failed.show();
                    if (response.data.errors.length > 0) {
                        $errors.html(response.data.errors.join('<br/>')).show();
                    }
                }
                runNextTest();
            } else {
                // Sessions test ajax calls
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_diagnostics_ajax',
                        test: $test.data('class'),
                        ajax: 'ajax1',
                        csrf_token: BooklyL10nGlobal.csrf_token
                    }
                }).then(function (response) {
                    if (response.success) {
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {
                                action: 'bookly_diagnostics_ajax',
                                test: $test.data('class'),
                                ajax: 'ajax2',
                                csrf_token: BooklyL10nGlobal.csrf_token
                            }
                        }).then(function (response) {
                            $loading.hide();
                            $reload.show();
                            if (response.success) {
                                $success.show();
                            } else {
                                $failed.show();
                                $errors.html(response.data.errors.join('<br/>')).show();
                            }
                            runNextTest();
                        });
                    } else {
                        $loading.hide();
                        $reload.show();
                        $failed.show();
                        $errors.html(response.data.errors.join('<br/>')).show();
                        runNextTest();
                    }
                })
            }
        })
    });

    let $autorun_tests = $('.bookly-js-autorun-all-tests'),
        autorun = getCookie('bookly_diagnostic_autorun_tests');
    if (autorun === '0') {
        $autorun_tests.removeClass('bookly-js-active');
        $('i', $autorun_tests).addClass('fa-square');
        $('.bookly-js-tests').each(function () {
            $('.bookly-js-loading-test', $(this)).hide();
            $('.bookly-js-reload-test', $(this)).show();
        });
    } else {
        $autorun_tests.addClass('bookly-js-active');
        $('i', $autorun_tests).removeClass('fa-square');
        runAllTests();
    }

    $autorun_tests.on('click', function () {
        let active = $(this).hasClass('bookly-js-active');
        setCookie('bookly_diagnostic_autorun_tests', active ? '0' : '1');
        $(this).toggleClass('bookly-js-active');
        $('i', $autorun_tests).toggleClass('fa-square');
        if (!active) {
            runAllTests();
        }
    });

    // Tools
    // Data Management

    $('.bookly-js-tables-dropdown').on('click', function (e) {
        e.stopPropagation();
    });
    $('#bookly_import_file').change(function (e) {
        if ($(this).val()) {
            let $spinner = $(this).siblings('.bookly-js-spinner');
            $spinner.show();
            const formData = new FormData();
            formData.append('action', 'bookly_import_data');
            formData.append('csrf_token', BooklyL10nGlobal.csrf_token);
            formData.append('safe', $('[name=safe]', $(this).closest('.input-group')).prop('checked') ? '1' : '0');
            formData.append('import', e.target.files[0]);
            fetch(ajaxurl, {method: 'POST', body: formData})
                .then(function (response) {
                    if (response.status == 200) {
                        return response.json();
                    }
                    $spinner.hide();
                    throw new Error(response.statusText + ' (' + response.status + ')');
                })
                .then(function (result) {
                    if (result.success) {
                        booklyAlert({success: [result.data.message]});
                    } else {
                        booklyAlert({error: result.data.message});
                    }
                    $spinner.hide();
                    let $modal = booklyModal('Are you sure you want to reload this page?', null, 'No', 'Reload')
                        .on('bs.click.main.button', function (event, modal, mainButton) {
                            location.reload();
                        });
                    setTimeout(function () {
                        $('.modal-footer .btn-success', $modal).focus();
                    }, 500);
                }).catch(function (error) {
                booklyAlert({error: ['Request failed: ' + error]});
            });
        }
    });

    // Forms Data
    $('#forms-data button[data-action="copy"]').on('click', function () {
        let $button = $(this),
            form_id = $button.closest('.list-group-item-action').data('form_id'),
            data = $button.closest('.list-group-item-action').data('form_data'),
            $copied = $('<small>', {
                class: 'ml-2',
                text: 'copied'
            });
        $button.before($copied);
        $button.hide();
        const $temp = $('<input/>');
        $('body').append($temp);
        $temp.val(JSON.stringify(data)).select();
        document.execCommand('copy');
        $temp.remove();
        console.group(form_id);
        console.dir(data);
        console.groupEnd();
        setTimeout(function () {
            $copied.remove();
            $button.show();
        }, 1000);
    });
    $('#forms-data button[data-action="destroy"]').on('click', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        let $li = $(this).closest('.list-group-item-action');
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'FormsData',
                ajax: 'destroy',
                form_id: $li.data('form_id'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                $li.remove();
            }
        });
    });

    // External plugins
    $('#external-plugins button').on('click', function () {
        let $plugin = $(this).closest('.list-group-item-action'),
            action = $(this).data('action'),
            ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'ExternalPlugins',
                ajax: action,
                plugin: $plugin.data('plugin'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                if (response.success) {
                    switch (action) {
                        case 'install':
                        case 'activate':
                            $plugin.find('button').hide();
                            $plugin.find('button[data-action="delete"]').show();
                            break;
                        case 'delete':
                            $plugin.find('button').hide();
                            $plugin.find('button[data-action="install"]').show();
                            break;
                    }
                } else {
                    booklyAlert('Failed');
                }
            }
        });
    });

    // Shortcodes
    $('#bookly-find-shortcode-and-open').on('click', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'ShortCodes',
                ajax: 'find',
                shortcode: $('#bookly_shortcode').val(),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                if (response.success) {
                    window.open(response.data.url, '_blank').focus();
                }
            }
        });
    });

    // Advanced options
    $('#advanced-options').on('click', 'button[data-action="set-default-option"]', function () {
        let ladda = Ladda.create(this);
        let $that = $(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'AdvancedOptions',
                ajax: 'setDefault',
                option: $that.data('option'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function () {
                ladda.stop();
                $that.closest('.bookly-js-advanced-option-card').hide();
            }
        });
    }).on('click', '.bookly-js-advanced-options-option-name button', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'AdvancedOptions',
                ajax: 'getOption',
                option: $('#bookly-advanced-options-option-name').val(),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                if (response.success) {
                    $('#bookly-advanced-options-option-current-value').val(response.data.current);
                    $('#bookly-advanced-options-option-default-value').val(response.data.default);
                    $('#bookly-advanced-options-option-value').val(response.data.current);
                    $('.bookly-js-advanced-options-set-option').show();
                } else {
                    $('#bookly-advanced-options-option-current-value').val('');
                    $('#bookly-advanced-options-option-default-value').val('');
                    $('#bookly-advanced-options-option-value').val('');
                    $('.bookly-js-advanced-options-set-option').hide();
                    booklyAlert({error: ['Failed']});
                }
            }
        });
    }).on('click', '.bookly-js-advanced-options-set-option button', function () {
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'AdvancedOptions',
                ajax: 'setOption',
                option: $('#bookly-advanced-options-option-name').val(),
                value: $('#bookly-advanced-options-option-value').val(),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function () {
                ladda.stop();
                $('#bookly-advanced-options-option-current-value').val($('#bookly-advanced-options-option-value').val());
            }
        });
    });

    // Optional logs
    $('.bookly-js-enable-optional-logs').on('click', function () {
        let $that = $(this),
            ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: 'Logs',
                ajax: 'enableLogs',
                option: $that.closest('.bookly-js-optional-logs-entry').data('option'),
                period: $that.data('period'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                if (response.data.until) {
                    $that.closest('.bookly-js-optional-logs-entry').find('.bookly-js-optional-logs-until').text(response.data.until);
                    $that.closest('.bookly-js-optional-logs-entry').find('.bookly-js-optional-logs-active').show();
                } else {
                    $that.closest('.bookly-js-optional-logs-entry').find('.bookly-js-optional-logs-active').hide();
                }
            }
        });
    });

    // Logs
    $('#bookly_logs_expire').change(function (e) {
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_set_logs_expire',
                expire: $(this).val(),
                csrf_token: BooklyL10nGlobal.csrf_token,
            },
            dataType: 'json'
        });
    });
    let $container = $('#logs'),
        $logsDateFilter = $('#bookly-logs-date-filter', $container),
        $logsTable = $('#bookly-logs-table', $container),
        $logsSearch = $('#bookly-log-search', $container),
        $logsAction = $('#bookly-filter-logs-action', $container).booklyDropdown(),
        $logsTarget = $('#bookly-filter-logs-target-id', $container);

    if (url_params.hasOwnProperty('debug')) {
        $logsAction.booklyDropdown('setSelected', ['error', 'debug']);
    } else {
        $logsAction.booklyDropdown('selectAll');
    }

    $('#bookly-delete-logs').on('click', function () {
        if (confirm(BooklyL10nGlobal.l10n.areYouSure)) {
            var ladda = Ladda.create(this);
            ladda.start();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_delete_logs',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                },
                dataType: 'json',
                success: function () {
                    ladda.stop();
                    dt_logs.ajax.reload(null, false);
                }
            });
        }
    });

    let pickers = {
        dateFormat: 'YYYY-MM-DD',
        creationDate: {
            startDate: moment().subtract(30, 'days'),
            endDate: moment(),
        },
    };
    let picker_ranges = {};
    picker_ranges[BooklyL10nGlobal.dateRange.anyTime] = [moment().subtract(100, 'years'), moment().add(100, 'years')];
    picker_ranges[BooklyL10nGlobal.dateRange.yesterday] = [moment().subtract(1, 'days'), moment().subtract(1, 'days')];
    picker_ranges[BooklyL10nGlobal.dateRange.today] = [moment(), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.last_7] = [moment().subtract(7, 'days'), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.last_30] = [moment().subtract(30, 'days'), moment()];
    picker_ranges[BooklyL10nGlobal.dateRange.thisMonth] = [moment().startOf('month'), moment().endOf('month')];
    picker_ranges[BooklyL10nGlobal.dateRange.lastMonth] = [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')];

    $logsDateFilter.daterangepicker({
            timePicker: true,
            parentEl: $('.bookly-js-tests'),
            startDate: pickers.creationDate.startDate,
            endDate: pickers.creationDate.endDate,
            ranges: picker_ranges,
            showDropdowns: true,
            linkedCalendars: false,
            autoUpdateInput: false,
            timePicker24Hour: true,
            timePickerSeconds: true,
        },
        function (start, end, label) {
            switch (label) {
                case BooklyL10nGlobal.dateRange.anyTime:
                    dt_logs.page.len(booklyDataTables.getPageLength());
                    $logsDateFilter
                        .data('date', 'any')
                        .find('span')
                        .html(BooklyL10nGlobal.dateRange.anyTime);
                    break;
                case 'Custom Range':
                    dt_logs.page.len(1000);
                    $logsDateFilter
                        .data('date', start.format('YYYY-MM-DD HH:mm:ss') + ' - ' + end.format('YYYY-MM-DD HH:mm:ss'))
                        .find('span')
                        .html(start.format(BooklyL10nGlobal.dateRange.format + ' HH:mm:ss') + ' - ' + end.format(BooklyL10nGlobal.dateRange.format + ' HH:mm:ss'));
                    break;
                default:
                    dt_logs.page.len(booklyDataTables.getPageLength());
                    $logsDateFilter
                        .data('date', start.format(pickers.dateFormat) + ' - ' + end.format(pickers.dateFormat))
                        .find('span')
                        .html(start.format(BooklyL10nGlobal.dateRange.format) + ' - ' + end.format(BooklyL10nGlobal.dateRange.format));
            }
        }
    );

    let dt_logs;
    $('[href=#logs]').one('click', function () {

        let columns = [
            {data: 'id', width: 80, responsivePriority: 0},
            {data: 'created_at', responsivePriority: 0},
            {
                data: 'action', responsivePriority: 0,
                render: function (data, type, row, meta) {
                    return data === 'error' && row.target.indexOf('bookly-') !== -1
                        ? '<span class="text-danger">ERROR</span>'
                        : data;
                },
            },
            {
                data: 'target', responsivePriority: 2,
                render: function (data, type, row, meta) {
                    const isBooklyError = data && (data.indexOf('bookly-') !== -1);
                    return $('<div>', {
                        dir: 'rtl',
                        class: 'text-truncate',
                        text: isBooklyError ? data.slice(data.indexOf('bookly-')) : data
                    }).prop('outerHTML');
                },
            },
            {data: 'target_id', responsivePriority: 1},
            {data: 'author', responsivePriority: 1},
            {
                data: 'details',
                render: function (data, type, row, meta) {
                    try {
                        return JSON.stringify(JSON.parse(data), null, 2).replace(/\n/g, '<br/>');
                    } catch (e) {
                        return data;
                    }
                },
                className: 'none',
                responsivePriority: 2
            },
            {data: 'comment', responsivePriority: 2},
            {
                data: 'ref', className: 'none', responsivePriority: 1,
                render: function (data, type, row, meta) {
                    return data && data.replace(/\n/g, '<br>');
                }
            },
        ];

        if (url_params.hasOwnProperty('debug')) {
            columns.push({
                data: null,
                responsivePriority: 1,
                orderable: false,
                render: function (data, type, row, meta) {
                    return '<div class="custom-control custom-checkbox">' +
                        '<input value="' + row.id + '" id=bookly-logs-"' + row.id + '" type="checkbox" class="custom-control-input">' +
                        '<label for=bookly-logs-"' + row.id + '" class="custom-control-label"></label>' +
                        '</div>';
                }
            });
        }

        dt_logs = booklyDataTables.init($logsTable, {order: [{column: 'id', order: 'desc'}]},
            {
                ajax: {
                    url: ajaxurl,
                    method: 'POST',
                    data: function (d) {
                        return $.extend({action: 'bookly_get_logs', csrf_token: BooklyL10nGlobal.csrf_token}, {
                            filter: {
                                created_at: $logsDateFilter.data('date'),
                                search: $logsSearch.val(),
                                action: $logsAction.booklyDropdown('getSelected'),
                                target: $logsTarget.val()
                            }
                        }, d);
                    }
                },
                columns: columns,
            });
    });

    let $checkAllLogs = $('#bookly-check-all', $logsTable),
        $restoreButton = $('#bookly-logs-restore');

    function toggleRestoreButton() {
        let $_checkboxes = $('td input[type=checkbox]:checked');
        if ($_checkboxes.length > 0) {
            $_checkboxes.each(function () {
                let _row = booklyDataTables.getRowData(this, dt_logs);
                if (_row?.action === 'delete') {
                    $restoreButton.show();
                    return false;
                }
                $restoreButton.hide();
            })
        } else {
            $restoreButton.hide();
        }
    }

    toggleRestoreButton();

    $logsTable.on('change', 'td input[type=checkbox]', function () {
        toggleRestoreButton();
    });

    $checkAllLogs.on('change', function () {
        $logsTable.find('tbody input:checkbox').prop('checked', this.checked).trigger('change');
    });

    $restoreButton.on('click', function () {
        if (confirm(BooklyL10nGlobal.l10n.areYouSure)) {
            let ladda = Ladda.create(this),
                $button = $(this),
                ids = [];
            ladda.start();
            $('td input[type=checkbox]:checked', $logsTable).each(function () {
                let _row = booklyDataTables.getRowData(this, dt_logs);
                if (_row?.action === 'delete') {
                    ids.push(_row.id);
                }
            });

            $.ajax({
                url: ajaxurl,
                method: 'POST',
                data: {
                    action: 'bookly_diagnostics_ajax',
                    tool: 'Logs',
                    ajax: 'restore',
                    ids: ids,
                    csrf_token: BooklyL10nGlobal.csrf_token
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        booklyAlert({success: ['Success']});
                    } else {
                        booklyAlert({error: ['Failed']});
                    }
                    ladda.stop();
                },
                error: function () {
                    booklyAlert({error: ['Failed']});
                    ladda.stop();
                },
            }).always(function () {
                ladda.stop();
                dt_logs.ajax.reload();
            });
        }
    });

    function onChangeFilter() {
        dt_logs.ajax.reload();
    }

    $logsDateFilter.on('apply.daterangepicker', onChangeFilter);
    $logsTarget.on('keyup', onChangeFilter);
    $logsAction.on('change', function () {
        setTimeout(onChangeFilter, 0);
    });
    $logsSearch.on('keyup', onChangeFilter)
        .on('keydown', function (e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        });

    $('[data-tool]').on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            $button = $(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'bookly_diagnostics_ajax',
                tool: $button.data('tool'),
                ajax: $button.data('ajax'),
                params: $button.data('params'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if ($button.data('hide-on-success')) {
                        $button.closest($button.data('hide-on-success')).remove();
                    }
                    if ($button.data('hide-errors-on-success')) {
                        $button.closest('.card').find('.bookly-js-has-error').hide();
                    }
                }
                let message = response.hasOwnProperty('data') && response.data.hasOwnProperty('message') ? response.data.message : null;
                if (message) {
                    if (response.success) {
                        booklyAlert({success: [message]});
                    } else {
                        booklyAlert({error: [message]});
                    }
                }

                ladda.stop();
            },
            error: function () {
                booklyAlert({error: ['Error: in query execution.']});
                ladda.stop();
            },
        }).always(function () {
            ladda.stop();
        });
    })
})