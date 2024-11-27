function booklyAlert(alert) {
    // Check if there are messages in alert.
    let not_empty = false;
    for (let type in alert) {
        if (['success', 'error'].includes(type) && alert[type].length) {
            not_empty = true;
            break;
        }
    }

    if (not_empty) {
        let $container = jQuery('#bookly-alert');
        if ($container.length === 0) {
            $container = jQuery('<div id="bookly-alert" class="bookly-alert" style="max-width:600px"></div>').appendTo('#bookly-tbs');
        }
        for (let type in alert) {
            alert[type].forEach(function (message) {
                const $alert = jQuery('<div class="alert"><button type="button" class="close" data-dismiss="alert">&times;</button></div>');

                switch (type) {
                    case 'success':
                        $alert
                            .addClass('alert-success')
                            .prepend('<i class="fas fa-check-circle fa-fw fa-lg text-success align-middle mr-1"></i>');
                        setTimeout(function() {
                            $alert.remove();
                        }, 10000);
                        break;
                    case 'error':
                        $alert
                            .addClass('alert-danger')
                            .prepend('<i class="fas fa-times-circle fa-fw fa-lg text-danger align-middle mr-1"></i>');
                        break;
                }

                $alert
                    .append('<b>' + message + '</b>')
                    .appendTo($container);
            });
        }
    }
}

function booklyModal(title, text, closeCaption, mainActionCaption) {
    let $mainButton = '',
        $modal = jQuery('<div>', {class: 'bookly-modal bookly-fade', tabindex: -1, role: 'dialog'});
    if (mainActionCaption) {
        $mainButton = jQuery('<button>', {class: 'btn ladda-button btn-success', type: 'button', title: mainActionCaption, 'data-spinner-size': 40, 'data-style': 'zoom-in'})
            .append('<span>', {class: 'ladda-label'}).text(mainActionCaption);
        $mainButton.on('click', function (e) {
            e.stopPropagation();
            $modal.trigger('bs.click.main.button', [$modal, $mainButton.get(0)]);
        });
    }

    $modal
        .append(
            jQuery('<div>', {class: 'modal-dialog'})
                .append(
                    jQuery('<div>', {class: 'modal-content'})
                        .append(
                            jQuery('<div>', {class: 'modal-header'})
                                .append(jQuery('<h5>', {class: 'modal-title', html: title}))
                                .append(
                                    jQuery('<button>', {class: 'close', 'data-dismiss': 'bookly-modal', type: 'button'})
                                        .append('<span>').text('×')
                                )
                        )
                        .append(
                            text ? jQuery('<div>', {class: 'modal-body', html: text}) : ''
                        )
                        .append(
                            jQuery('<div>', {class: 'modal-footer'})
                                .append($mainButton)
                                .append(
                                    jQuery('<button>', {class: 'btn ladda-button btn-default', 'data-dismiss': 'bookly-modal', type: 'button'})
                                        .append('<span>').text(closeCaption)
                                )
                        )
                )
        );
    jQuery('#bookly-tbs').append($modal);

    $modal.on('hide.bs.modal', function () {
        setTimeout(function () {$modal.remove();}, 2000)
    });
    setTimeout(function() {$modal.booklyModal('show')}, 0);

    return $modal;
}

function requiredBooklyPro() {
    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        data: {
            action: 'bookly_required_bookly_pro',
            csrf_token: BooklyL10nGlobal.csrf_token
        },
        success: function(response) {
            if (response.success) {
                let $features = jQuery('<div>', {class: 'col-12'}),
                    $content = jQuery('<div>')
                ;
                response.data.features.forEach(function(feature, f) {
                    $features.append(jQuery('<div>', {html: feature}).prepend(jQuery('<i>', {class: 'fa-fw mr-1 fas fa-check text-success'})));
                });

                $content
                    .append(jQuery('<div>', {class: 'm-n3'})
                        .append(
                            jQuery('<img/>', {src: response.data.image, alt: 'Bookly Pro', class: 'img-fluid'})
                        )
                    ).append(jQuery('<div>', {class: 'row'})
                        .append(jQuery('<div>', {class: 'mx-auto h4 mt-4 text-bookly', html: response.data.caption}))
                    ).append(jQuery('<div>', {class: 'row'})
                        .append(jQuery('<div>', {class: 'col text-center', html: response.data.body}))
                    ).append(jQuery('<div>', {class: 'form-row mt-3'})
                        .append($features)
                    );

                booklyModal('', $content, response.data.close, response.data.upgrade)
                    .on('bs.click.main.button', function(event, modal, mainButton) {
                        Ladda.create(mainButton).start();
                        window.location.href = 'https://codecanyon.net/item/bookly-booking-plugin-responsive-appointment-booking-and-scheduling/7226091?ref=ladela&utm_campaign=admin_menu&utm_medium=pro_not_active&utm_source=bookly_admin';
                        modal.booklyModal('hide');
                    })
                    .on('show.bs.modal', function() {
                        jQuery('.modal-header', jQuery(this)).remove();
                    });
            }
        },
    });
}

(function ($) {
    window.booklyDataTables = {
        settings: {},
        init: function($container, settings, initial_settings) {
            if (initial_settings.hasOwnProperty('row_with_checkbox') && initial_settings.row_with_checkbox) {
                initial_settings.columns.push({
                    data: null,
                    responsivePriority: 1,
                    orderable: false,
                    render: function(data, type, row, meta) {
                        let prefix = meta.settings.sInstance + '-';
                        return '<div class="custom-control custom-checkbox mt-1">' +
                            '<input value="' + row.id + '" id="' + prefix + row.id + '" type="checkbox" class="custom-control-input">' +
                            '<label for="' + prefix + row.id + '" class="custom-control-label"></label>' +
                            '</div>'
                    }
                });
                delete (initial_settings.row_with_checkbox);
            }
            this.settings = settings;
            const default_settings = {
                order: this.getOrder(initial_settings.columns),
                pageLength: this.getPageLength(),
                language: this.getLocalization(initial_settings),
                layout: {
                    bottomStart: {
                        paging: {
                            type: 'numbers'
                        }
                    },
                    bottomEnd: null,
                    topEnd: null,
                    topStart: null,
                },
                searching: false,
                info: false,
                processing: true,
                responsive: true,
                paging: true,
                serverSide: true,
            };

            return $container.DataTable($.extend({}, default_settings, initial_settings));
        },
        getOrder: function(columns) {
            let order = [];
            $.each(this.settings.order, function(_, value) {
                const index = columns.findIndex(function(c) {
                    return c.data === value.column;
                });
                if (index !== -1) {
                    order.push([index, value.order]);
                }
            });

            return order;
        },
        getPageLength: function() {
            return this.settings.hasOwnProperty('page_length') ? this.settings.page_length : 25;
        },
        getLocalization: function(initial_settings) {
            let language = initial_settings.hasOwnProperty('language')
                ? initial_settings.language
                : {};
            if (window.BooklyL10n !== undefined) {
                ['zeroRecords', 'processing', 'emptyTable'].forEach(function(value) {
                    if (!language.hasOwnProperty(value)) {
                        language[value] = BooklyL10n[value];
                    }
                });
            }

            return language;
        },
        getRowData: function(element, dt) {
            const $el = $(element).closest('td');
            const dataTable = dt || $(element).closest('table').DataTable();
            return $el.hasClass('child')
                ? dataTable.row($el.closest('tr').prev()).data()
                : dataTable.row($el).data();
        },
    };

    window.booklySerialize = {
        form: function($form) {
            let data = {},
                serialized = $form.serializeArray();
            $('input[type=radio]:not(:checked)', $form).each(function() {
                if (this.name) {
                    let find = false,
                        that = this;
                    serialized.forEach(function(item) {
                        if (!find && item.name === that.name) {
                            find = true;
                        }
                    });
                    if (!find) {
                        serialized.push({name: this.name, value: null});
                    }
                }
            });
            $('input[type=checkbox]:not(:checked)', $form).each(function() {
                if (this.name) {
                    serialized.push({name: this.name, value: null});
                }
            });
            $.map(serialized, function(n) {
                const keys = n.name.match(/[a-zA-Z0-9_-]+|(?=\[\])/g);
                if (keys.length > 1) {
                    let tmp = data, key = keys.pop();
                    for (let i = 0; i < keys.length, j = keys[i]; i++) {
                        tmp[j] = (!tmp[j]
                            ? (key == '' && i == keys.length - 1) ? [] : {}
                            : tmp[j]
                        );
                        tmp = tmp[j];
                    }
                    if (n.value !== null) {
                        if (Array.isArray(tmp)) {
                            tmp.push(n.value);
                        } else {
                            tmp[key] = n.value;
                        }
                    }
                } else data[keys.pop()] = n.value;
            });

            return data;
        },
        buildRequestDataFromForm: function(action, $form) {
            return this.buildRequestData(action, this.form($form));
        },
        buildRequestData: function(action, data) {
            return {
                action: action,
                csrf_token: BooklyL10nGlobal.csrf_token,
                json_data: JSON.stringify(data),
            }
        }
    }
})(jQuery);