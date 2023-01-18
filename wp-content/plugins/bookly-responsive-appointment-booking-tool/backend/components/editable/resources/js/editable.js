/**
 * jQuery booklyEditable.
 */
(function ($) {
    let methods = {
        init: function (options) {
            let opts = $.extend(true, {}, $.fn.booklyEditable.defaults, options);

            return this.each(function () {
                const $this = $(this);

                if ($this.data('booklyEditable')) {
                    return;
                }

                const type = $this.data('type') || opts.type;
                if (type in $.fn.booklyEditable.types) {
                    const obj = {
                        $el: $this,
                        opts: opts,
                        values: $.extend({}, $this.data('values')),
                        option: $this.data('option'),
                        update: function () {
                            // Update content in all related elements
                            $.each(obj.values, function (option, value) {
                                $('[data-option="' + option + '"]')
                                    .not('.bookly-js-editable')
                                    .text(value)
                                    .end()
                                    .filter('.bookly-js-editable:not(.bookly-js-permanent-title)')
                                    .text(value || obj.opts.l10n.empty)
                                    .end()
                                    .filter('.bookly-js-editable')
                                        .each(function () {
                                            const elObj = $(this).data('booklyEditable');
                                            $.extend(elObj.values, obj.values);
                                        });
                            });
                        }
                    }

                    // Init
                    $.fn.booklyEditable.types[type](obj);

                    // Set text for empty field.
                    if ($this.text() === '') {
                        $this.text(opts.l10n.empty);
                    }

                    $this.data('booklyEditable', obj);
                }
            });
        },
        reset: function () {
            const obj = this.data('booklyEditable');
            if (!obj) {
                return;
            }

            obj.values = $.extend({}, obj.$el.data('values'));
            obj.update();
        },
        getValue: function () {
            const obj = this.data('booklyEditable');
            if (!obj) {
                return;
            }

            return obj.values;
        }
    };

    $.fn.booklyEditable = function (method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('No method ' + method + ' for jQuery.booklyEditable');
        }
    };

    $.fn.booklyEditable.types = {};

    $.fn.booklyEditable.defaults = {
        type: 'popover',
        l10n: {
            edit: BooklyL10nEditable.edit,
            empty: BooklyL10nEditable.empty,
            enter_a_content: BooklyL10nEditable.enter_a_content
        }
    };
})(jQuery);