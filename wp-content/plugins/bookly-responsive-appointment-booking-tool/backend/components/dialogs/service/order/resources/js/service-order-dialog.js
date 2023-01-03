jQuery(function($) {
    'use strict';

    $(document.body)
        .on('service.submitForm', {},
            // Bind submit handler for service saving.
            function(event, $panel, data) {
                BooklyServiceOrderDialogL10n.services
                    .find(function(service) { return service.id == data.id; }).title = $.fn.dataTable.render.text().display(data.title);
            })
        .on('service.deleted', {},
            function(event, services) {
                BooklyServiceOrderDialogL10n.services = BooklyServiceOrderDialogL10n.services.filter(function(el) {
                    return !services.includes(String(el.id));
                })
            });

    var $dialog = $('#bookly-service-order-modal'),
        $list = $('#bookly-list', $dialog),
        $template = $('#bookly-service-template'),
        $save = $('#bookly-save', $dialog)
    ;

    // Save categories
    $save.on('click', function(e) {
        e.preventDefault();
        var ladda = Ladda.create(this),
            services = [];
        ladda.start();
        $list.find('li').each(function(position, category) {
            services.push($(category).find('[name="id"]').val());
        });
        $.post(ajaxurl, booklySerialize.buildRequestData('bookly_update_service_positions', {services: services}),
            function(response) {
                if (response.success) {
                    BooklyServiceOrderDialogL10n.services = response.data;
                    $dialog.booklyModal('hide');
                }
                ladda.stop();
            });
    });

    $dialog.off().on('show.bs.modal', function() {
        $list.html('');
        BooklyServiceOrderDialogL10n.services.forEach(function(service) {
            $list.append(
                $template.clone().show().html()
                    .replace(/{{id}}/g, service.id)
                    .replace(/{{title}}/g, service.title||'')
            );
        });
    });

    Sortable.create($list[0], {
        handle: '.bookly-js-draghandle',
    });
    $('[data-target="#bookly-service-order-modal"]').prop('disabled', false);
});