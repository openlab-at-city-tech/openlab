jQuery(function ($) {
    'use strict';
    var $modal = $('#bookly-table-settings-modal'),
        $save_button = $('.bookly-js-table-settings-save', $modal),
        $columns = $('.bookly-js-table-columns', $modal),
        $template = $('#bookly-table-settings-template'),
        $paging_container = $('.bookly-table-paging', $modal),
        $page_length = $('#bookly_dt_page_length', $paging_container);

    // Save settings.
    $save_button.on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            columns = {};
        ladda.start();
        $modal.find('input[type=checkbox]').each(function () {
            columns[this.name] = this.checked ? 1 : 0;
        });
        $.post(
            ajaxurl,
            {
                action: 'bookly_update_table_settings',
                table: $('[name="bookly-table-name"]',$modal).val(),
                columns: columns,
                page_length: $page_length.val(),
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
        let table_name = $(this).data('table-name'),
            datatable = window[$(this).data('setting-name')].datatables[table_name],
            paging = $(this).data('table-paging') || false;

        $paging_container.toggle(paging);
        $('[name="bookly-table-name"]', $modal).val(table_name);

        // Generate columns.
        $columns.html('');
        $.each(datatable.settings.columns, function (name, show) {
            $columns.append(
                $template.clone().show().html()
                    .replace(/{{name}}/g, name)
                    .replace(/{{title}}/g, datatable.titles[name])
                    .replace(/{{checked}}/g, show ? 'checked' : '')
                    .replace(/{{id}}/g, 'bookly-ts-' + table_name + '-' + name)

            );
        });
        const page_length = datatable.settings.hasOwnProperty('page_length') ? datatable.settings.page_length : 25;
        if (!$page_length.find('option[value="' + page_length + '"]').length) {
            $page_length.append($('<option>', {value: page_length, text: page_length}));
        }

        $page_length.val(page_length);
        $('#bookly-table-settings-modal').data('location', $(this).data('location')).booklyModal('show');
    });
});