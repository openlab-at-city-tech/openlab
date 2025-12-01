jQuery(function($) {
    'use strict';

    let $keysList = $('#bookly-keys-list'),
        $newToken = $('#bookly-js-new-key'),
        btn = {
            edit: $('<button type="button" class="btn btn-default" data-action="edit">').append($('<i class="far fa-fw fa-edit mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span>').get(0).outerHTML
        },
        $revokeButton = $('#bookly-keys-list-delete-button'),
        columns = []
    ;

    $newToken
        .on('click', function () {
            BooklyGrantAuthDialog.showDialog({
                id: null,
                token: null,
                staff_id: null,
                wp_user_id: null
            }, function() {
                dt.ajax.reload(null, false);
            });
        });

    $.each(BooklyL10n.datatables.cloud_mobile_staff_cabinet.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'token':
                    columns.push({data: column, render: $.fn.dataTable.render.text(), class: 'text-monospace'});
                    break;
                case 'full_name':
                    columns.push({
                        data: column,
                        render: function(data, type, row, meta) {
                            if (row.wp_user_id) {
                                return data + ' <span class="text-muted">(' + BooklyL10n.wp_user + ')</span>';
                            }
                            if (row.staff_id) {
                                return data + ' <span class="text-muted">(' + BooklyL10n.staff + ')</span>';

                            }
                            return data;
                        }
                    });
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
                '<input value="' + row.token + '" id="bookly-dt-' + row.token + '" type="checkbox" class="custom-control-input">' +
                '<label for="bookly-dt-' + row.token + '" class="custom-control-label"></label>' +
                '</div>';
        }
    });

    /**
     * Init DataTables.
     */

    var dt = booklyDataTables.init($keysList, BooklyL10n.datatables.cloud_mobile_staff_cabinet.settings,
        {
            ajax: {
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_cloud_mobile_staff_cabinet_get_access_tokens',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                }
            },
            columns: columns
        }).on('change', function() {
            $keysList.find('tbody input:checkbox').prop('checked', this.checked);
        }).on('click', '[data-action=edit]', function() {
                let row = booklyDataTables.getRowData(this, dt);
                BooklyGrantAuthDialog.showDialog({
                    id: row.id,
                    token: row.token,
                    staff_id: row.staff_id || null,
                    wp_user_id: row.wp_user_id || null,
                    name: row.full_name,
                }, function() {
                    dt.ajax.reload(null, false);
                });
            });

    /**
     * Revoke keys.
     */
    $revokeButton.on('click', function() {
        booklyModal(BooklyL10n.areYouSure, BooklyL10n.revokeTokensMessage, BooklyL10n.cancel, BooklyL10n.revoke)
            .on('bs.click.main.button', function(event, modal, mainButton) {
                let ladda = Ladda.create(mainButton),
                    data = [],
                    $checkboxes = $('tbody input:checked', $keysList)
                ;
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
                    success: function(response) {
                        ladda.stop();
                        if (response.success) {
                            dt.rows($checkboxes.closest('td')).remove().draw();
                            BooklyGrantAuthDialog.setStaffMembers(response.data.staff_members);
                            modal.booklyModal('hide');
                        } else {
                            alert(response.data.message);
                        }
                    }
                });
            });
    });
});