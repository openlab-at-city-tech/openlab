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

    return $modal.booklyModal('show');
}

(function ($) {
    window.booklySerialize = {
        form: function($form) {
            let data = {};

            $.map($form.serializeArray(), function(n) {
                const keys = n.name.match(/[a-zA-Z0-9_]+|(?=\[\])/g);
                if (keys.length > 1) {
                    let tmp = data, pop = keys.pop();
                    for (let i = 0; i < keys.length, j = keys[i]; i++) {
                        tmp[j] = (!tmp[j] ? (pop == '') ? [] : {} : tmp[j]); tmp = tmp[j];
                    }
                    if (pop == '') {
                        tmp = (!Array.isArray(tmp) ? [] : tmp);
                        tmp.push(n.value);
                    }
                    else tmp[pop] = n.value;
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
                data: JSON.stringify(data),
            }
        }
    }
})(jQuery);