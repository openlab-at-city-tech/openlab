jQuery(function($) {
    'use strict';

    let $keysList = $('#bookly-keys-list'),
        $checkAllButton = $('#bookly-check-all'),
        btn = {
            edit: $('<button type="button" class="btn btn-default" data-action="edit">').append($('<i class="far fa-fw fa-edit mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span>').get(0).outerHTML
        },
        $revokeButton = $('#bookly-js-revoke'),
        columns = []
    ;

    $.each(BooklyL10n.datatables.cloud_mobile_staff_cabinet.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'cloud_msc_token':
                    columns.push({data: column, render: $.fn.dataTable.render.text(), class: 'text-monospace'});
                    break;
                default:
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
            }
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        width: 90,
        render: function (data, type, row, meta) {
            return '<div class="d-flex flex-row-reverse">' + btn.edit + '</div>';
        }
    });
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        searchable: false,
        render: function (data, type, row, meta) {
            return '<div class="custom-control custom-checkbox mt-1">' +
                '<input value="' + row.cloud_msc_token + '" id="bookly-dt-' + row.cloud_msc_token + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-dt-' + row.cloud_msc_token + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    columns[0].responsivePriority = 0;

    let order = [];
    $.each(BooklyL10n.datatables.cloud_mobile_staff_cabinet.settings.order, function (key, value) {
        const index = columns.findIndex(function (c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $keysList.DataTable({
        order: order,
        info: false,
        searching: false,
        processing: true,
        responsive: true,
        paging: false,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_cloud_mobile_staff_cabinet_get_access_tokens',
                csrf_token: BooklyL10nGlobal.csrf_token,
            }
        },
        columns: columns,
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing : BooklyL10n.processing,
            emptyTable: BooklyL10n.emptyTable,
            loadingRecords: BooklyL10n.loadingRecords
        },
        layout: {
            bottomStart: 'paging',
            bottomEnd: null
        }
    });

    /**
     * Select all access keys.
     */
    $checkAllButton.on('change', function () {
        $keysList.find('tbody input:checkbox').prop('checked', this.checked);
    });

    /**
     * On access key select.
     */
    $keysList.on('change', 'tbody input:checkbox', function () {
        $checkAllButton.prop('checked', $keysList.find('tbody input:not(:checked)').length == 0);
    });

    /**
     * Revoke keys.
     */
    $revokeButton.on('click', function () {
        if (confirm(BooklyL10n.revokeTokensMessage)) {
            var ladda = Ladda.create(this),
                data  = [],
                $checkboxes = $('tbody input:checked',$keysList);
            ladda.start();

            $checkboxes.each(function () {
                data.push(this.value);
            });

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_cloud_mobile_staff_cabinet_revoke_access_tokens',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    keys: data
                },
                dataType: 'json',
                success: function (response) {
                    ladda.stop();
                    if (response.success) {
                        dt.rows($checkboxes.closest('td')).remove().draw();
                        if (typeof BooklyL10nMobileStaffCabinet === 'undefined') {
                            BooklyL10nMobileStaffCabinet.staff_members = response.data.staff_members;
                        }
                    } else {
                        alert(response.data.message);
                    }
                }
            });
        }
    });
});