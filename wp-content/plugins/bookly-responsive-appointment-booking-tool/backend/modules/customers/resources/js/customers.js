jQuery(function ($) {
    'use strict';
    let
        $customersList = $('#bookly-customers-list'),
        $mergeListContainer = $('#bookly-merge-list'),
        $mergeList = $customersList.clone().prop('id', '').find('th:last').remove().end().appendTo($mergeListContainer),
        $filter = $('#bookly-filter'),
        $checkAllButton = $('#bookly-check-all'),
        $newCustomerBtn = $('#bookly-new-customer'),
        $selectForMergeButton = $('#bookly-select-for-merge'),
        $mergeWithButton = $('[data-target="#bookly-merge-dialog"]'),
        $mergeDialog = $('#bookly-merge-dialog'),
        $mergeButton = $('#bookly-merge', $mergeDialog),
        $exportDialog = $('#bookly-export-customers-dialog'),
        $exportSelectAll = $('#bookly-js-export-select-all', $exportDialog),
        columns = [],
        info_renders = {}
    ;

    for (var a in BooklyL10n.infoFields) {
        info_renders[BooklyL10n.infoFields[a].id] = $.fn.dataTable.render.text();
        if (BooklyL10n.infoFields[a].type === 'file') {
            info_renders[BooklyL10n.infoFields[a].id] = function (data, type, row, meta) {
                if (data != '') {
                    return '<button type="button" class="btn btn-link" data-download-file="' + data + '" title="' + BooklyL10n.download + '"><i class="fas fa-fw fa-paperclip"></i></button>';
                }
                return '';
            }
        }
    }

    /**
     * Init table columns.
     */
    $.each(BooklyL10n.datatables.customers.settings.columns, function (column, show) {
        if (show) {
            switch (column) {
                case 'id':
                case 'last_appointment':
                case 'total_appointments':
                case 'payments':
                case 'wp_user':
                    columns.push({data: column, render: $.fn.dataTable.render.text()});
                    break;
                case 'address':
                    columns.push({data: column, render: $.fn.dataTable.render.text(), orderable: false});
                    break;
                case 'facebook':
                    columns.push({
                        data: 'facebook_id',
                        render: function (data, type, row, meta) {
                            return data ? '<a href="https://www.facebook.com/app_scoped_user_id/' + data + '/" target="_blank"><span class="dashicons dashicons-facebook"></span></a>' : '';
                        }
                    });
                    break;
                case 'phone':
                    columns.push({
                        data: column, render: function (data, type, row, meta) {
                            return data ? '<span style="white-space: nowrap;">' + window.booklyIntlTelInput.utils.formatNumber($.fn.dataTable.render.text().display(data), null, window.booklyIntlTelInput.utils.numberFormat.INTERNATIONAL) + '</span>' : '';
                        }
                    });
                    break;
                case 'tags':
                    columns.push({
                        data: 'tags',
                        render: function (data, type, row, meta) {
                            if (data) {
                                let text = '';
                                JSON.parse(data).forEach(function (tag) {
                                    let color = '#000';
                                    if (BooklyL10n.tagsData !== undefined) {
                                        let _tag = BooklyL10n.tagsData.list.find(function (t) {
                                            return t.tag.toLowerCase() === tag.toLowerCase()
                                        });
                                        if (_tag) {
                                            color = BooklyL10n.tagsData.colors[_tag.color_id];
                                        }
                                    }
                                    text += '<span class="badge p-2 mb-1 text-white" style="background-color: ' + color + '">' + $.fn.dataTable.render.text().display(tag) + '</span> ';
                                });
                                return text;
                            }
                            return '';
                        }
                    });
                    break;
                case 'birthday': {
                    columns.push({
                        data: 'birthday',
                        render: function (data, type, row, meta) {
                            return row.birthday_formatted;
                        }
                    });
                    break;
                }
                default:
                    if (column.startsWith('info_fields_')) {
                        columns.push({
                            data: column.replace(/_([^_]*)$/, '.$1'),
                            render: info_renders[parseInt(column.split('_').pop())],
                            orderable: false
                        });
                    } else {
                        columns.push({data: column, render: $.fn.dataTable.render.text()});
                    }
                    break;
            }
        }
    });
    columns[0].responsivePriority = 0;

    let dt = booklyDataTables.init($customersList, BooklyL10n.datatables.customers.settings, {
        ajax: {
            url: ajaxurl,
            method: 'POST',
            data: function(d) {
                return $.extend({}, d, {
                    action: 'bookly_get_customers',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    filter: $filter.val()
                });
            }
        },
        columns: columns.concat([
            {
                data: null,
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                width: 120,
                render: function(data, type, row, meta) {
                    return '<button type="button" class="btn btn-default" data-action="edit"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></button>';
                }
            },
            {
                data: null,
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return '<div class="custom-control custom-checkbox">' +
                        '<input value="' + row.id + '" id="bookly-dt-' + row.id + '" type="checkbox" class="custom-control-input">' +
                        '<label for="bookly-dt-' + row.id + '" class="custom-control-label"></label>' +
                        '</div>';
                }
            }
        ]),
    });

    /**
     * Add customer.
     */
    $newCustomerBtn.on('click', function () {
        BooklyCustomerDialog.showDialog({
            action: 'create',
            onDone: function (customer, result) {
                if (result && result.new_tags.length) {
                    BooklyL10n.tagsData.list = BooklyL10n.tagsData.list.concat(result.new_tags);
                }
                dt.ajax.reload(null, false);
            }
        })
    });

    /**
     * Select all customers.
     */
    $checkAllButton.on('change', function () {
        $customersList.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $customersList
        // On customer select.
        .on('change', 'tbody input:checkbox', function () {
            $checkAllButton.prop('checked', $customersList.find('tbody input:not(:checked)').length == 0);
            $mergeWithButton.prop('disabled', $customersList.find('tbody input:checked').length != 1);
        })
        // Edit customer.
        .on('click', '[data-action=edit]', function () {
            BooklyCustomerDialog.showDialog({
                action: 'load',
                customerId: booklyDataTables.getRowData(this, dt).id,
                onDone: function (customer, result) {
                    dt.ajax.reload(null, false);
                    if (result && result.new_tags.length) {
                        BooklyL10n.tagsData.list = BooklyL10n.tagsData.list.concat(result.new_tags);
                    }
                }
            })
        })
        .on('click', '[data-download-file]', function (e) {
            e.preventDefault();
            window.open(ajaxurl + (ajaxurl.indexOf('?') > 0 ? '&' : '?') + 'action=bookly_files_download&slug=' + $(this).data('download-file') + '&csrf_token=' + BooklyL10nGlobal.csrf_token, '_blank');
        });

    /**
     * On filters change.
     */
    function onChangeFilter() {
        dt.ajax.reload();
    }

    $filter.on('keyup', onChangeFilter);

    /**
     * Merge list.
     */
    var dt_merge = booklyDataTables.init($mergeList, {order: [{column: 'id', order: 'asc'}]}, {
        paging: false,
        serverSide: false,
        columns: columns.concat([
            {
                data: null,
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                    return '<button type="button" class="btn btn-default"><i class="fas fa-fw fa-times"></i></button>';
                }
            }
        ]),
    });

    /**
     * Select for merge.
     */
    $selectForMergeButton.on('click', function () {
        var $checkboxes = $customersList.find('tbody input:checked');

        if ($checkboxes.length) {
            $checkboxes.each(function () {
                var data = booklyDataTables.getRowData(this, dt);
                if (dt_merge.rows().data().indexOf(data) < 0) {
                    dt_merge.row.add(data).draw();
                }
                this.checked = false;
            }).trigger('change');
            $mergeWithButton.show();
            $mergeListContainer.show();
            dt_merge.responsive.recalc();
        }
    });

    /**
     * Merge customers.
     */
    $mergeButton.on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            ids = [];
        ladda.start();
        dt_merge.rows().every(function () {
            ids.push(this.data().id);
        });
        $.ajax({
            url: ajaxurl,
            method: 'POST',
            data: {
                action: 'bookly_merge_customers',
                csrf_token: BooklyL10nGlobal.csrf_token,
                target_id: $customersList.find('tbody input:checked').val(),
                ids: ids
            },
            dataType: 'json',
            success: function (response) {
                ladda.stop();
                $mergeDialog.booklyModal('hide');
                if (response.success) {
                    dt.ajax.reload(null, false);
                    dt_merge.clear();
                    $mergeListContainer.hide();
                    $mergeWithButton.hide();
                } else {
                    alert(response.data.message);
                }
            }
        });
    });

    /**
     * Remove customer from merge list.
     */
    $mergeList.on('click', 'button', function () {
        dt_merge.row($(this).closest('td')).remove().draw();
        var any = dt_merge.rows().any();
        $mergeWithButton.toggle(any);
        $mergeListContainer.toggle(any);
    });

    $exportSelectAll
        .on('click', function () {
            let checked = this.checked;
            $('.bookly-js-columns input', $exportDialog).each(function () {
                $(this).prop('checked', checked);
            });
        });

    $('.bookly-js-columns input', $exportDialog)
        .on('change', function () {
            $exportSelectAll.prop('checked', $('.bookly-js-columns input:checked', $exportDialog).length == $('.bookly-js-columns input', $exportDialog).length);
        });

    /**
     * Import & export customers.
     */
    Ladda.bind('#bookly-import-customers-dialog button[type=submit]');
    Ladda.bind('#bookly-export-customers-dialog button[type=submit]', {timeout: 2000});
});