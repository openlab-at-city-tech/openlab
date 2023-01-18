jQuery(function ($) {
    'use strict';
    var $modal       = $('#bookly-table-settings-modal'),
        $save_button = $('.bookly-js-table-settings-save', $modal),
        $columns     = $('.bookly-js-table-columns', $modal),
        $template    = $('#bookly-table-settings-template');

    // Save settings.
    $save_button.on('click', function (e) {
        e.preventDefault();
        let ladda   = Ladda.create(this),
            columns = {};
        ladda.start();
        $modal.find('input[type=checkbox]').each(function () {
            columns[this.name] = this.checked ? 1 : 0;
        });
        $.post(
            ajaxurl,
            {
                action    : 'bookly_update_table_settings',
                table     : $modal.find('[name="bookly-table-name"]').val(),
                columns   : columns,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            function (response) {
                if ($modal.data('location').length !== '') {
                    location = $modal.data('location');
                } else {
                    location.reload();
                }
            });
    });

    Sortable.create($columns[0], {
        handle: '.bookly-js-draghandle'
    });

    // Open table settings modal.
    $(document).off('click', '.bookly-js-table-settings').on('click', '.bookly-js-table-settings', function () {
        let table_settings = window[$(this).data('setting-name')].datatables[$(this).data('table-name')].settings,
            table_titles   = window[$(this).data('setting-name')].datatables[$(this).data('table-name')].titles,
            table_name     = $(this).data('table-name');

        $('[name="bookly-table-name"]', $modal).val(table_name);

        // Generate columns.
        $columns.html('');
        $.each(table_settings.columns, function (name, show) {
            $columns.append(
                $template.clone().show().html()
                    .replace(/{{name}}/g, name)
                    .replace(/{{title}}/g, table_titles[name])
                    .replace(/{{checked}}/g, show ? 'checked' : '')
                    .replace(/{{id}}/g, 'bookly-ts-' + table_name + '-' + name)

            );
        });
        $('#bookly-table-settings-modal').data('location', $(this).data('location')).booklyModal('show');
    });
});