(function ($) {
    let firstInit = true;

    $.fn.booklyEditable.types.popover = function (obj) {
        if (firstInit) {
            construct();
            firstInit = false;
        }

        const content = function () {
            const $content = $('<div class="mt-2">');
            const fieldType = obj.$el.data('fieldtype') || 'input';

            switch (fieldType) {
                case 'textarea':
                    $.each(obj.values, function (option, value) {
                        let $textarea = $('<textarea class="form-control bookly-js-editable-control" rows="5" cols="50"/>');
                        $textarea
                            .attr('placeholder', obj.opts.l10n.enter_a_content)
                            .attr('name', option)
                            .val(value);
                        $('<div class="form-group mb-2"/>').append($textarea).appendTo($content);
                    });
                    break;
                default:
                    $.each(obj.values, function (option, value) {
                        let $input = $('<input type="text" class="form-control bookly-js-editable-control"/>');
                        $input
                            .attr('placeholder', obj.opts.l10n.enter_a_content)
                            .attr('name', option)
                            .val(value);
                        $('<div class="form-group mb-2"/>').append($input).appendTo($content);
                    });
                    break;
            }
            $content.append('<hr/>');
            $content.append('<div class="text-right"><div class="btn-group btn-group-sm" role="group"><button type="button" class="btn btn-success bookly-js-editable-save"><i class="fas fa-fw fa-check"></i></button><button type="button" class="btn btn-default" data-dismiss="bookly-popover"><i class="fas fa-fw fa-times"></i></button></div></div>');

            // "Close" button
            $content.find('button[data-dismiss="bookly-popover"]').on('click', close);

            // "Save" button
            $content.find('button.bookly-js-editable-save').on('click', function () {
                $content.find('.bookly-js-editable-control').each(function () {
                    obj.values[this.name] = this.value;
                });
                obj.update();
                close();
            });

            // Process keypress
            $content.find('.bookly-js-editable-control').on('keyup', function (e) {
                if (e.keyCode === 27) {
                    close();
                }
            });

            function close() {
                obj.$el.booklyPopover('hide');
            }

            return $content;
        };

        // Init popover
        const opts = $.extend({}, {
            placement: 'auto',
            fallbackPlacement: ['bottom'],
            container: '#bookly-appearance',
        }, obj.opts);

        obj.$el.booklyPopover({
            html: true,
            placement: obj.$el.data('placement') || opts.placement,
            fallbackPlacement: obj.$el.data('fallbackPlacement') || opts.fallbackPlacement,
            container: opts.container,
            template: '<div class="bookly-popover bookly-editable-popover" role="tooltip"><div class="arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>',
            trigger: 'manual',
            title: obj.$el.data('title') || '',
            content: content
        });

        // Click on editable field
        obj.$el.on('click', function (e) {
            e.preventDefault();

            if (!obj.$el.attr('aria-describedby')) {
                obj.$el.booklyPopover('show');
                obj.$el.off('shown.bs.popover').on('shown.bs.popover', function () {
                    if (obj.$el.attr('aria-describedby') !== undefined) {
                        $(obj.$el.data('bs.popover').tip).find('.bookly-js-editable-control:first').focus();
                    }
                });
            } else {
                obj.$el.booklyPopover('hide');
            }
        });
    };

    function construct() {
        // Process click outside the popover to hide it
        $(document).on('click', function (e) {
            if ($(e.target).closest('.bookly-popover').length === 0) {
                let $activators = $('.bookly-js-editable[aria-describedby]');
                $activators.each(function () {
                    if (this !== e.target) {
                        $(this).booklyPopover('hide');
                    }
                });
            }
        });
    }
})(jQuery);