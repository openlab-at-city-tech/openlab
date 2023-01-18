/**
 * jQuery booklyDropdown.
 */
(function($) {
    let id = 0;
    let methods = {
        init: function(options) {
            let opts = $.extend({}, $.fn.booklyDropdown.defaults, options);

            return this.filter('ul').each(function() {
                if ($(this).data('booklyDropdown')) {
                    return;
                }
                let obj = {
                    $container: $('<div class="bookly-dropdown"/>'),
                    $button: $('<button type="button" class="btn btn-default bookly-dropdown-toggle d-flex align-items-center w-100" data-toggle="bookly-dropdown"/>'),
                    $counter: $('<span class="flex-grow-1 text-left mr-1"/>'),
                    $ul: $(this),
                    $selectAll: $('<input type="checkbox" class="custom-control-input"/>').attr('id', 'bookly-js-dropdown-' + (++id)),
                    $groups: $(),
                    $options: [],
                    preselected: [],  // initially selected options
                    refresh: function() {
                        let $selected = obj.$options.filter(function(o) { return o.is(':checked') });
                        obj.$selectAll.prop('checked', false);
                        obj.$groups.prop('checked', false);
                        if ($selected.length === 0) {
                            obj.$counter.text(obj.txtNothingSelected);
                        } else if ($selected.length === obj.$options.length) {
                            obj.$counter.text(obj.txtAllSelected);
                            obj.$selectAll.prop('checked', true);
                            obj.$groups.prop('checked', true);
                        } else {
                            if ($selected.length === 1) {
                                obj.$counter.text($selected[0].next().text());
                            } else {
                                obj.$counter.text($selected.length + '/' + obj.$options.length);
                            }
                            obj.$groups.each(function() {
                                let $this = $(this);
                                $this.prop('checked', $this.data('group-checkboxes').filter(':not(:checked)').length === 0);
                            });
                        }
                    }
                };
                // Texts.
                obj.txtSelectAll = obj.$ul.data('txt-select-all') || opts.txtSelectAll;
                obj.txtAllSelected = obj.$ul.data('txt-all-selected') || opts.txtAllSelected;
                obj.txtNothingSelected = obj.$ul.data('txt-nothing-selected') || opts.txtNothingSelected;

                let $content = obj.$button;
                if (obj.$ul.data('hide-icon') === undefined) {
                    $content.append($('<i class="mr-1 fa-fw"/>').addClass(obj.$ul.data('icon-class') || opts.iconClass));
                }
                $content.append(obj.$counter);

                obj.$container
                    .addClass(obj.$ul.data('container-class') || opts.containerClass)
                    .append($content)
                    .append(
                        obj.$ul
                            .addClass('bookly-dropdown-menu bookly-dropdown-menu-' + (obj.$ul.data('align') || opts.align))
                            // Options (checkboxes).
                            .append($.map(opts.options, function(option) {
                                return $('<li/>')
                                    .data({
                                        'input-name': option.inputName || opts.inputsName,
                                        'value': option.value || '',
                                        'selected': option.selected || false
                                    })
                                    .text(option.name)
                                    ;
                            }))
                            .find('li')
                            .addClass('bookly-dropdown-item')
                            .wrapInner('<div class="custom-control custom-checkbox ml-4"><label class="custom-control-label"></label></div>')
                            .each(function() {
                                let $li = $(this),
                                    $checkbox = $('<input type="checkbox" class="custom-control-input"/>').attr('id', 'bookly-js-dropdown-' + (++id)),
                                    $ul = $li.find('ul:first')
                                ;
                                if ($li.is('[data-flatten-if-single]') && obj.$ul.children().length === 1) {
                                    $li.replaceWith($ul.children());
                                    return true;
                                }
                                if ($ul.length > 0) {
                                    $ul.appendTo($li);
                                    $ul.addClass('p-0');
                                    obj.$groups = obj.$groups.add($checkbox);
                                } else {
                                    $checkbox
                                        .attr('name', $li.data('input-name'))
                                        .val($li.data('value'))
                                        .prop('checked', !!$li.data('selected'))
                                    ;
                                    obj.$options.push($checkbox);
                                    if ($checkbox.prop('checked')) {
                                        obj.preselected.push($checkbox.val());
                                    }
                                }
                                $li.find('label:first').attr('for', $checkbox.attr('id')).before($checkbox);
                            })
                            .end()
                            // Select all.
                            .prepend(
                                $('<li class="bookly-dropdown-item"/>')
                                    .append(
                                        $('<div class="custom-control custom-checkbox"/>')
                                            .append(obj.$selectAll)
                                            .append(
                                                $('<label class="custom-control-label"/>')
                                                    .attr('for', obj.$selectAll.attr('id'))
                                                    .append(obj.txtSelectAll)
                                            )
                                    )
                            )
                            // Replace with container.
                            .replaceWith(obj.$container)
                            // Do not close on click.
                            .on('click', function(e) {
                                e.stopPropagation();
                            })
                    )
                    // Events.
                    .on('change', 'input:checkbox', function() {
                        let $this = $(this),
                            checked = this.checked;
                        if ($this.is(obj.$selectAll)) {
                            obj.$options.forEach(function(o) {o.prop('checked', checked);});
                            opts.onChange.call(obj.$ul, obj.$options.map(function(o) { return o.val(); }), checked, true);
                        } else if ($this.is(obj.$groups)) {
                            $this.data('group-checkboxes').prop('checked', this.checked);
                            opts.onChange.call(obj.$ul, $this.data('group-checkboxes').map(function() { return this.value; }).get(), checked, false);
                        } else {
                            opts.onChange.call(obj.$ul, [this.value], checked, false);
                        }
                        obj.refresh();
                    })
                ;

                // Attach a handler to an event for the container
                obj.$container.bind('bookly-dropdown.change', function() {
                    opts.onChange.call(obj.$ul, obj.$options.map(function(o) { return o.val(); }), this.checked, false);
                });

                // Link group checkboxes with sub-items.
                obj.$groups.each(function() {
                    let $this = $(this),
                        $checkboxes = $this.closest('li').find('ul input:checkbox')
                    ;
                    $this.data('group-checkboxes', $checkboxes);
                });

                obj.refresh();
                obj.$ul.data('booklyDropdown', obj);
            });
        },
        deselect: function(values) {
            if (!Array.isArray(values)) {
                values = [values];
            }
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {
                    if ($.inArray(o.val(), values) > -1) {
                        o.prop('checked', false);
                    }
                });
                obj.refresh();
            });
        },
        deselectAll: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {o.prop('checked', false)});
                obj.refresh();
            });
        },
        getSelected: function() {
            var obj = this.filter('ul').data('booklyDropdown'),
                res = []
            ;
            if (obj) {
                obj.$options.filter(function(o) { return o.is(':checked') }).forEach(function(o) {
                    res.push(o.val());
                });
            }

            return res;
        },
        getSelectedAllState: function() {
            var obj = this.filter('ul').data('booklyDropdown');
            return obj.$selectAll.prop('checked');
        },
        getSelectedExt: function() {
            var obj = this.filter('ul').data('booklyDropdown'),
                res = []
            ;
            if (obj) {
                obj.$options.filter(function(o) { return o.is(':checked') }).forEach(function(o) {
                    res.push({value: o.val(), name: o.next('label').text()});
                });
            }

            return res;
        },
        hide: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$container.hide();
            });
        },
        refresh: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.refresh();
            });
        },
        reset: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {
                    o.prop('checked', $.inArray(o.val(), obj.preselected) > -1);
                });
                obj.refresh();
            });
        },
        select: function(values) {
            if (!Array.isArray(values)) {
                values = [values];
            }
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {
                    if ($.inArray(o.val(), values) > -1) {
                        o.prop('checked', true);
                    }
                });
                obj.refresh();
            });
        },
        selectAll: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {o.prop('checked', true)});
                obj.refresh();
            });
        },
        setSelected: function(values) {
            if (!Array.isArray(values)) {
                values = [values];
            }
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$options.forEach(function(o) {
                    o.prop('checked', $.inArray(o.val(), values) > -1);
                });
                obj.refresh();
            });
        },
        show: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$container.css('display', '');
            });
        },
        toggle: function() {
            return this.filter('ul').each(function() {
                var obj = $(this).data('booklyDropdown');
                if (!obj) {
                    return;
                }
                obj.$button.booklyDropdown('toggle');
            });
        }
    };

    $.fn.booklyDropdown = function(method) {
        if (methods[method]) {
            return methods[method].apply(this, Array.prototype.slice.call(arguments, 1));
        } else if (typeof method === 'object' || !method) {
            return methods.init.apply(this, arguments);
        } else {
            $.error('No method ' + method + ' for jQuery.booklyDropdown');
        }
    };

    $.fn.booklyDropdown.defaults = {
        align: $('body').hasClass('rtl') ? 'right ' : 'left',
        containerClass: '',
        iconClass: 'far fa-user',
        txtSelectAll: 'All',
        txtAllSelected: 'All selected',
        txtNothingSelected: 'Nothing selected',
        inputsName: '',
        options: [],
        onChange: function(values, selected, all) {}
    };
})(jQuery);