jQuery(function ($) {
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
                action: 'bookly_diagnostics_test_run',
                test: $test.data('class'),
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            error: function () {
                $loading.hide();
                $reload.show();
                $failed.show();
            },
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
                        });
                    } else {
                        $loading.hide();
                        $reload.show();
                        $failed.show();
                        $errors.html(response.data.errors.join('<br/>')).show();
                    }
                })
            }
        });
    });

    $('.bookly-js-tests .bookly-js-reload-test').each(function () {
        $(this).trigger('click');
    });

    // Tools
    // Data Management

    $('.bookly-js-tables-dropdown').on('click', function (e) {
        e.stopPropagation();
    });
    $('#bookly_import_file').change(function () {
        if ($(this).val()) {
            $('#bookly_import').submit();
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

    $('[data-tool]').on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            $button = $(this);
        ladda.start();
        $.ajax({
            url: ajaxurl,
            type: 'POST',
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