jQuery(function($) {
    'use strict';

    let $keysList = $('#bookly-keys-list'),
        $newToken = $('#bookly-js-new-key'),
        $checkAllButton = $('#bookly-check-all'),
        btn = {
            copy_token: $('<button/>', {type: 'button',  class: 'btn btn-default', 'data-action': 'copy_token', title: BooklyL10n.copy_token }).append($('<i class="far fa-fw fa-copy mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.copy_token + '</span>').get(0).outerHTML,
            copy_link: $('<button/>', {type: 'button',  class: 'btn btn-default mr-2 ml-2', 'data-action': 'copy_link', title: BooklyL10n.copy_link }).append($('<i class="fas fa-fw fa-link mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.copy_link + '</span>').get(0).outerHTML,
            edit: $('<button/>', {type: 'button',  class: 'btn btn-default', 'data-action': 'edit', title: BooklyL10n.edit + '…' }).append($('<i class="far fa-fw fa-edit mr-lg-1" />'), '<span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span>').get(0).outerHTML
        },
        $revokeButton = $('#bookly-keys-list-delete-button'),
        columns = [],
        app_auth_url = 'https://app.bookly.pro/?token='
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
            return '<div class="d-flex">' + btn.copy_token + btn.copy_link + btn.edit + '</div>';
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
        }).on('click', '[data-action=copy_token]', function() {
            let row = booklyDataTables.getRowData(this, dt);
            booklyCopyTextToClipboard(row.token);
        }).on('click', '[data-action=copy_link]', function() {;
            let row = booklyDataTables.getRowData(this, dt);
            booklyCopyTextToClipboard(app_auth_url + row.token);
        }).on('change', 'tbody input:checkbox', function () {
            $checkAllButton.prop('checked', $keysList.find('tbody input:not(:checked)').length == 0);
        })

    function booklyCopyTextToClipboard(text) {
        let $temp = $('<input/>', {type: 'text', value: text});
        $('body').append($temp);
        $temp.select();
        document.execCommand('copy');
        $temp.remove();
        booklyAlert({success: [BooklyL10n.copied]});
    }

    $checkAllButton.on('change', function () {
        $keysList.find('tbody input:checkbox').prop('checked', this.checked);
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