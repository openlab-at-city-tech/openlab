jQuery(function($) {
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
        order = []
    ;

    /**
     * Init table columns.
     */
    $.each(BooklyL10n.datatables.customers.settings.columns, function(column, show) {
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
                        render: function(data, type, row, meta) {
                            return data ? '<a href="https://www.facebook.com/app_scoped_user_id/' + data + '/" target="_blank"><span class="dashicons dashicons-facebook"></span></a>' : '';
                        }
                    });
                    break;
                case 'birthday': {
                    columns.push({
                        data: 'birthday',
                        render: function(data, type, row, meta) {
                            return row.birthday_formatted;
                        }
                    });
                    break;
                }
                default:
                    if (column.startsWith('info_fields_')) {
                        const id = parseInt(column.split('_').pop());
                        const field = BooklyL10n.infoFields.find(function(i) { return i.id === id; });
                        columns.push({
                            data: 'info_fields.' + id + '.value' + (field.type === 'checkboxes' ? '[, ]' : ''),
                            render: $.fn.dataTable.render.text(),
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

    $.each(BooklyL10n.datatables.customers.settings.order, function(_, value) {
        const index = columns.findIndex(function(c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $customersList.DataTable({
        order: order,
        info: false,
        searching: false,
        lengthChange: false,
        pageLength: 25,
        pagingType: 'numbers',
        processing: true,
        responsive: true,
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
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
        dom: "<'row'<'col-sm-12'tr>><'row float-left mt-3'<'col-sm-12'p>>",
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing: BooklyL10n.processing
        }
    });

    /**
     * Add customer.
     */
    $newCustomerBtn.on('click', function() {
        BooklyCustomerDialog.showDialog({
            action: 'create',
            onDone: function(event) {
                dt.ajax.reload();
            }
        })
    });

    /**
     * Select all customers.
     */
    $checkAllButton.on('change', function() {
        $customersList.find('tbody input:checkbox').prop('checked', this.checked);
    });

    $customersList
        // On customer select.
        .on('change', 'tbody input:checkbox', function() {
            $checkAllButton.prop('checked', $customersList.find('tbody input:not(:checked)').length == 0);
            $mergeWithButton.prop('disabled', $customersList.find('tbody input:checked').length != 1);
        })
        // Edit customer.
        .on('click', '[data-action=edit]', function() {
            BooklyCustomerDialog.showDialog({
                action: 'load',
                customerId: getDTRowData(this).id,
                onDone: function(event) {
                    dt.ajax.reload();
                }
            })
        });

    /**
     * On filters change.
     */
    $filter.on('keyup', function() { dt.ajax.reload(); });

    /**
     * Merge list.
     */
    var mdt = $mergeList.DataTable({
        order: [[0, 'asc']],
        info: false,
        searching: false,
        paging: false,
        responsive: true,
        columns: columns.concat([
            {
                data: null,
                responsivePriority: 1,
                orderable: false,
                searchable: false,
                render: function(data, type, row, meta) {
                    return '<button type="button" class="btn btn-default"><i class="fas fa-fw fa-times"></i></button>';
                }
            }
        ]),
        language: {
            zeroRecords: BooklyL10n.zeroRecords
        }
    });

    /**
     * Select for merge.
     */
    $selectForMergeButton.on('click', function() {
        var $checkboxes = $customersList.find('tbody input:checked');

        if ($checkboxes.length) {
            $checkboxes.each(function() {
                var data = getDTRowData(this);
                if (mdt.rows().data().indexOf(data) < 0) {
                    mdt.row.add(data).draw();
                }
                this.checked = false;
            }).trigger('change');
            $mergeWithButton.show();
            $mergeListContainer.show();
            mdt.responsive.recalc();
        }
    });

    /**
     * Merge customers.
     */
    $mergeButton.on('click', function(e) {
        e.preventDefault();
        let ladda = Ladda.create(this),
            ids = [];
        ladda.start();
        mdt.rows().every(function() {
            ids.push(this.data().id);
        });
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'bookly_merge_customers',
                csrf_token: BooklyL10nGlobal.csrf_token,
                target_id: $customersList.find('tbody input:checked').val(),
                ids: ids
            },
            dataType: 'json',
            success: function(response) {
                ladda.stop();
                $mergeDialog.booklyModal('hide');
                if (response.success) {
                    dt.ajax.reload(null, false);
                    mdt.clear();
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
    $mergeList.on('click', 'button', function() {
        mdt.row($(this).closest('td')).remove().draw();
        var any = mdt.rows().any();
        $mergeWithButton.toggle(any);
        $mergeListContainer.toggle(any);
    });

    $exportSelectAll
        .on('click', function() {
            let checked = this.checked;
            $('.bookly-js-columns input', $exportDialog).each(function() {
                $(this).prop('checked', checked);
            });
        });

    $('.bookly-js-columns input', $exportDialog)
        .on('change', function() {
            $exportSelectAll.prop('checked', $('.bookly-js-columns input:checked', $exportDialog).length == $('.bookly-js-columns input', $exportDialog).length);
        });

    /**
     * Import & export customers.
     */
    Ladda.bind('#bookly-import-customers-dialog button[type=submit]');
    Ladda.bind('#bookly-export-customers-dialog button[type=submit]', {timeout: 2000});

    function getDTRowData(element) {
        let $el = $(element).closest('td');
        if ($el.hasClass('child')) {
            $el = $el.closest('tr').prev();
        }
        return dt.row($el).data();
    }
});