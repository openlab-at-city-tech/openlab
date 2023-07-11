jQuery(function($) {
    let $servicesList = $('#services-list'),
        $checkAllButton = $('#bookly-check-all'),
        filters = {
            category: $('#bookly-filter-category'),
            search: $('#bookly-filter-search')
        },
        $deleteButton = $('#bookly-delete'),
        $deleteModal = $('.bookly-js-delete-cascade-confirm'),
        urlParts = document.URL.split('#'),
        columns = [],
        order = []
    ;

    $('.bookly-js-select').val(null);

    // Apply filter from anchor
    if (urlParts.length > 1) {
        urlParts[1].split('&').forEach(function(part) {
            var params = part.split('=');
            $('#bookly-filter-' + params[0]).val(params[1]);
        });
    } else {
        $.each(BooklyL10n.datatables.services.settings.filter, function(field, value) {
            if (value != '') {
                $('#bookly-filter-' + field).val(value);
            }
            // check if select has correct values
            if ($('#bookly-filter-' + field).prop('type') == 'select-one') {
                if ($('#bookly-filter-' + field + ' option[value="' + value + '"]').length == 0) {
                    $('#bookly-filter-' + field).val(null);
                }
            }
        });
    }

    /**
     * Init Columns.
     */
    if (BooklyL10n.show_type) {
        columns.push({
            data: null,
            responsivePriority: 1,
            orderable: false,
            render: function(data, type, row, meta) {
                return '<i class="' + row.type_icon + ' fa-fw" title="' + row.type + '"></i>';
            },
        });
    }
    columns.push({
        data: null,
        responsivePriority: 1,
        orderable: false,
        render: function(data, type, row, meta) {
            return '<i class="fas fa-fw fa-circle" style="color:' + row.color + ';">';
        }
    });

    $.each(BooklyL10n.datatables.services.settings.columns, function(column, show) {
        if (show) {
            switch (column) {
                case 'category_id':
                    columns.push({
                        data: column,
                        render: function(data, type, row, meta) {
                            if (row.category != null) {
                                return BooklyL10n.categories.find(function(category) {
                                    return category.id === row.category;
                                }).name;
                            } else {
                                return BooklyL10n.uncategorized;
                            }
                        }
                    });
                    break;
                case 'online_meetings':
                    columns.push({
                        data: column,
                        render: function(data, type, row, meta) {
                            switch (data) {
                                case 'zoom':
                                    return '<span class="badge badge-secondary"><i class="fas fa-video fa-fw"></i> Zoom</span>';
                                case 'google_meet':
                                    return '<span class="badge badge-secondary"><i class="fas fa-video fa-fw"></i> Meet</span>';
                                case 'jitsi':
                                    return '<span class="badge badge-secondary"><i class="fas fa-video fa-fw"></i> Jitsi Meet</span>';
                                case 'bbb':
                                    return '<span class="badge badge-secondary"><i class="fas fa-video fa-fw"></i> BigBlueButton</span>';
                                default:
                                    return '';
                            }
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
        responsivePriority: 2,
        orderable: false,
        searchable: false,
        render: function(data, type, row, meta) {
            return data.disabled ? '' : '<div class="d-inline-flex"><button type="button" class="btn btn-default mr-1" data-action="edit"><i class="far fa-fw fa-edit mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.edit + '…</span></button><button type="button" class="btn btn-default ladda-button" data-action="duplicate" data-spinner-size="40" data-style="zoom-in" data-spinner-color="#666666"><span class="ladda-label"><i class="far fa-fw fa-clone mr-lg-1"></i><span class="d-none d-lg-inline">' + BooklyL10n.duplicate + '…</span></span></button></div>';
        }
    });
    columns.push({
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
    });
    $.each(BooklyL10n.datatables.services.settings.order, function(_, value) {
        const index = columns.findIndex(function(c) { return c.data === value.column; });
        if (index !== -1) {
            order.push([index, value.order]);
        }
    });

    /**
     * Init DataTables.
     */
    var dt = $servicesList.DataTable({
        order: order,
        info: false,
        searching: false,
        lengthChange: false,
        processing: true,
        responsive: true,
        pageLength: 25,
        pagingType: 'numbers',
        serverSide: true,
        ajax: {
            url: ajaxurl,
            type: 'POST',
            data: function(d) {
                let data = $.extend({action: 'bookly_get_services', csrf_token: BooklyL10nGlobal.csrf_token, filter: {}}, d);
                Object.keys(filters).map(function(filter) {data.filter[filter] = filters[filter].val();});

                return data;
            }
        },
        columns: columns,
        rowCallback: function(row, data) {
            if (data.disabled) {
                $(row).addClass('text-muted');
            }
        },
        dom: "<'row'<'col-sm-12'tr>><'row float-left mt-3'<'col-sm-12'p>>",
        language: {
            zeroRecords: BooklyL10n.zeroRecords,
            processing: BooklyL10n.processing
        }
    });

    /**
     * On filter search change.
     */
    filters.search
        .on('keyup', function() {
            dt.ajax.reload();
        })
        .on('keydown', function(e) {
            if (e.keyCode == 13) {
                e.preventDefault();
                return false;
            }
        })
    ;
    filters.category
        .on('change', function() {
            dt.ajax.reload();
        });

    /**
     * Select all appointments.
     */
    $checkAllButton.on('change', function() {
        $servicesList.find('tbody input:checkbox').prop('checked', this.checked);
        $deleteButton.prop('disabled',$servicesList.find('tbody input:checked').length === 0);
    });

    /**
     * On appointment select.
     */
    $servicesList.on('change', 'tbody input:checkbox', function() {
        $checkAllButton.prop('checked', $servicesList.find('tbody input:not(:checked)').length === 0);
        $deleteButton.prop('disabled',$servicesList.find('tbody input:checked').length === 0);
    });

    $('.bookly-js-delete', $deleteModal).on('click', function(e) {
        e.preventDefault();
        let data = {
                action: 'bookly_remove_services',
                csrf_token: BooklyL10nGlobal.csrf_token,
            },
            ladda = rangeTools.ladda(this),
            service_ids = [],
            $checkboxes = $servicesList.find('tbody input:checked');

        $checkboxes.each(function() {
            service_ids.push(dt.row($(this).closest('td')).data().id);
        });
        data['service_ids[]'] = service_ids;

        $.post(ajaxurl, data, function() {
            dt.rows($checkboxes.closest('td')).remove().draw();
            $(document.body).trigger('service.deleted', [service_ids]);
            ladda.stop();
            $deleteModal.booklyModal('hide');
        });
    });

    $('.bookly-js-edit', $deleteModal).on('click', function() {
        rangeTools.ladda(this);
        window.location.href = BooklyL10n.appointmentsUrl + '#service=' + dt.row($servicesList.find('tbody input:checked')[0].closest('td')).data().id;
    });

    $deleteButton.on('click', function() {
        $deleteModal.booklyModal('show');
    });

    $servicesList.on('click', '[data-action="duplicate"]', function() {
        if (confirm(BooklyL10n.are_you_sure + "\n\n" + BooklyL10n.private_warning)) {
            let ladda = rangeTools.ladda(this),
                $tr = $(this).closest('tr'),
                data = $servicesList.DataTable().row($tr.hasClass('child') ? $tr.prev() : $tr).data();
            $.post(
                ajaxurl,
                {
                    action: 'bookly_duplicate_service',
                    service_id: data.id,
                    csrf_token: BooklyL10nGlobal.csrf_token,
                },
                function(response) {
                    if (response.success) {
                        dt.ajax.reload();
                        BooklyServiceOrderDialogL10n.services.push({id: response.data.id, title: response.data.title});
                    } else {
                        requiredBooklyPro();
                    }
                    ladda.stop();
                }
            );
        }
    });

    $('.bookly-js-select')
        .booklySelect2({
            width: '100%',
            theme: 'bootstrap4',
            dropdownParent: '#bookly-tbs',
            allowClear: true,
            placeholder: '',
            language: {
                noResults: function() {
                    return BooklyL10n.noResultFound;
                }
            }
        });
});