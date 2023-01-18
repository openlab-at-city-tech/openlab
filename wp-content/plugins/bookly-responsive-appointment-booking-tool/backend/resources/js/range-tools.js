(function ($) {
    window.rangeTools = {
        setVal: function ($select, text) {
            $select.val($select.find('option').filter(function () {
                return $(this).html() == text;
            }).val());
        },

        ladda: function (elem) {
            var ladda = Ladda.create(elem);
            ladda.start();
            return ladda;
        },

        hideUp24Hours: function ($start, $end) {
            $start.on('change', function () {
                var $start = $(this),
                    start_time = $start.val();

                // Hide end time options to keep them within 24 hours after start time.
                var parts = start_time.split(':');
                parts[0] = parseInt(parts[0]) + 24;
                var end_time = parts.join(':');
                var frag = document.createDocumentFragment();
                var old_value = $end.val();
                var new_value = null;
                $('option', $end).each(function () {
                    if (this.value <= start_time || this.value > end_time) {
                        var span = document.createElement('span');
                        span.style.display = 'none';
                        span.appendChild(this.cloneNode(true));
                        frag.appendChild(span);
                    } else {
                        frag.appendChild(this.cloneNode(true));
                        if (new_value === null || old_value == this.value) {
                            new_value = this.value;
                        }
                    }
                });
                $end.empty().append(frag).val(new_value);

                // when the working day is disabled (working start time is set to 'OFF')
                // hide all the elements inside the row
                if (!$start.val()) {
                    $start.closest('.bookly-js-range-row').find('.bookly-js-hide-on-off').hide().end()
                        .find('.bookly-js-invisible-on-off').addClass('invisible');
                } else {
                    $start.closest('.bookly-js-range-row').find('.bookly-js-hide-on-off').show().end()
                        .find('.bookly-js-invisible-on-off').removeClass('invisible');
                }
            }).trigger('change');
        },

        hideInaccessibleEndTime: function( $start, $end, force_keep_values ) {
            var frag       = document.createDocumentFragment(),
                old_value  = $end.val(),
                new_value  = null,
                start_time = $start.val(),
                parts      = start_time.split(':');
            parts[0] = parseInt(parts[0]) + 24;
            var end_time = parts.join(':');

            $('option', $end).each(function () {
                if (((this.value <= start_time) && (!force_keep_values || this.value != old_value)) || this.value > end_time) {
                    var span = document.createElement('span');
                    span.style.display = 'none';
                    span.appendChild(this.cloneNode(true));
                    frag.appendChild(span);
                } else {
                    frag.appendChild(this.cloneNode(true));
                    if (new_value === null || old_value == this.value) {
                        new_value = this.value;
                    }
                }
            });
            $end.empty().append(frag).val(new_value);
        },

        // Hide unavailable time in range
        hideInaccessibleBreaks: function ($start, $end, $parent, force_keep_values) {
            var parent_range_start = $('.bookly-js-parent-range-start', $parent).val(),
                parent_range_end   = $('.bookly-js-parent-range-end', $parent).val(),
                frag1 = document.createDocumentFragment(),
                frag2 = document.createDocumentFragment(),
                old_value = $start.val(),
                new_value = null;
            $('option', $start).each(function () {
                if ((this.value < parent_range_start || this.value >= parent_range_end) && (!force_keep_values || this.value != old_value)) {
                    var span = document.createElement('span');
                    span.style.display = 'none';
                    span.appendChild(this.cloneNode(true));
                    frag1.appendChild(span);
                } else {
                    frag1.appendChild(this.cloneNode(true));
                    if (new_value === null || old_value == this.value) {
                        new_value = this.value;
                    }
                }
            });
            $start.empty().append(frag1).val(new_value);

            old_value = $end.val();
            new_value = null;
            $('option', $end).each(function () {
                if ((this.value <= $start.val() || this.value > parent_range_end) && (!force_keep_values || this.value != old_value)) {
                    var span = document.createElement('span');
                    span.style.display = 'none';
                    span.appendChild(this.cloneNode(true));
                    frag2.appendChild(span);
                } else {
                    frag2.appendChild(this.cloneNode(true));
                    if (new_value === null || old_value == this.value) {
                        new_value = this.value;
                    }
                }
            });
            $end.empty().append(frag2).val(new_value);
        },

        // range_start = parent_range_start + 1h, range_end = parent_range_start + 2h
        setPopoverRangeDefault: function ($start, $end, $row) {
            var parent_range_start = $('.bookly-js-parent-range-start',$row).val(),
                parts = parent_range_start.split(':'),
                hours = parseInt(parts[0], 10),
                start_hours = hours + 1;
            if (start_hours < 10) {
                start_hours = '0' + start_hours;
            }
            var end_hours = hours + 2;
            if (end_hours < 10) {
                end_hours = '0' + end_hours;
            }
            var end_str = end_hours + ':' + parts[1] + ':' + parts[2],
                start_str = start_hours + ':' + parts[1] + ':' + parts[2];

            $start.val(start_str);
            $end.val(end_str);
        }
    }
})(jQuery);