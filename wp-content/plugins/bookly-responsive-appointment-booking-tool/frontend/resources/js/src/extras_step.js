import $ from 'jquery';
import {opt, laddaStart, scrollTo, booklyAjax, Format} from './shared.js';
import stepService from './service_step.js';
import stepTime from './time_step.js';
import stepRepeat from './repeat_step.js';
import stepCart from './cart_step.js';
import stepDetails from './details_step.js';

/**
 * Extras step.
 */
export default function stepExtras(params) {
    var data = {
            action: 'bookly_render_extras',
        },
        $container = opt[params.form_id].$container;
    if (opt[params.form_id].skip_steps.service && opt[params.form_id].use_client_time_zone) {
        // If Service step is skipped then we need to send time zone offset.
        data.time_zone = opt[params.form_id].timeZone;
        data.time_zone_offset = opt[params.form_id].timeZoneOffset;
    }
    $.extend(data, params);
    booklyAjax({
        data
    }).then(response => {
        BooklyL10n.csrf_token = response.csrf_token;
        $container.html(response.html);

        scrollTo($container, params.form_id);

        let $next_step = $('.bookly-js-next-step', $container),
            $back_step = $('.bookly-js-back-step', $container),
            $goto_cart = $('.bookly-js-go-to-cart', $container),
            $extras_items = $('.bookly-js-extras-item', $container),
            $extras_summary = $('.bookly-js-extras-summary span', $container),
            customJS = response.custom_js,
            $this,
            $input,
            format = new Format(response)
        ;

        var extrasChanged = function($extras_item, quantity) {
            var $input = $extras_item.find('input'),
                $total = $extras_item.find('.bookly-js-extras-total-price'),
                total_price = quantity * parseFloat($extras_item.data('price'));

            $total.text(format.price(total_price));
            $input.val(quantity);
            $extras_item.find('.bookly-js-extras-thumb').toggleClass('bookly-extras-selected', quantity > 0);

            // Updating summary
            var amount = 0;
            $extras_items.each(function(index, elem) {
                var $this = $(this),
                    multiplier = $this.closest('.bookly-js-extras-container').data('multiplier');
                amount += parseFloat($this.data('price')) * $this.find('input').val() * multiplier;
            });
            if (amount) {
                $extras_summary.html(' + ' + format.price(amount));
            } else {
                $extras_summary.html('');
            }
        };

        $extras_items.each(function(index, elem) {
            var $this = $(this);
            var $input = $this.find('input');
            $('.bookly-js-extras-thumb', $this)
                .on('click', function() {
                    extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : ($this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity')));
                })
                .keypress(function(e) {
                    e.preventDefault();
                    if (e.which == 13 || e.which == 32) {
                        extrasChanged($this, $input.val() > $this.data('min_quantity') ? $this.data('min_quantity') : ($this.data('min_quantity') == '0' ? 1 : $this.data('min_quantity')));
                    }
                });
            $this.find('.bookly-js-count-control').on('click', function() {
                var count = parseInt($input.val());
                count = $(this).hasClass('bookly-js-extras-increment')
                    ? Math.min($this.data('max_quantity'), count + 1)
                    : Math.max($this.data('min_quantity'), count - 1);
                extrasChanged($this, count);
            });

            setInputFilter($input.get(0), function(value) {
                let valid = /^\d*$/.test(value) && (value === '' || (parseInt(value) <= $this.data('max_quantity') && parseInt(value) >= $this.data('min_quantity')))
                if (valid) {
                    extrasChanged($this, value === '' ? $this.data('min_quantity') : parseInt(value));
                }
                return valid;
            });
            extrasChanged($this, $input.val());
        });

        $goto_cart.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);
            stepCart({form_id: params.form_id, from_step: 'extras'});
        });

        $next_step.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);

            // Execute custom JavaScript
            if (customJS) {
                try {
                    $.globalEval(customJS.next_button);
                } catch (e) {
                    // Do nothing
                }
            }

            var extras = {};
            $('.bookly-js-extras-container', $container).each(function() {
                var $extras_container = $(this);
                var chain_id = $extras_container.data('chain');
                var chain_extras = {};
                // Get checked extras for chain.
                $extras_container.find('.bookly-js-extras-item').each(function(index, elem) {
                    $this = $(this);
                    $input = $this.find('input');
                    if ($input.val() > 0) {
                        chain_extras[$this.data('id')] = $input.val();
                    }
                });
                extras[chain_id] = JSON.stringify(chain_extras);
            });
            booklyAjax({
                type: 'POST',
                data: {
                    action: 'bookly_session_save',
                    form_id: params.form_id,
                    extras: extras
                }
            }).then(response => {
                if (opt[params.form_id].step_extras == 'before_step_time' && !opt[params.form_id].skip_steps.time) {
                    stepTime({form_id: params.form_id, prev_step: 'extras'});
                } else if (!opt[params.form_id].skip_steps.repeat && opt[params.form_id].recurrence_enabled) {
                    stepRepeat({form_id: params.form_id});
                } else if (!opt[params.form_id].skip_steps.cart) {
                    stepCart({form_id: params.form_id, add_to_cart: true, from_step: 'time'});
                } else {
                    stepDetails({form_id: params.form_id, add_to_cart: true});
                }
            });
        });
        $back_step.on('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);
            if (opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_time) {
                stepTime({form_id: params.form_id, prev_step: 'extras'});
            } else {
                stepService({form_id: params.form_id});
            }
        });
    });
}

function setInputFilter(textbox, inputFilter) {
    ["input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop"].forEach(function(event) {
        textbox.addEventListener(event, function() {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty('oldValue')) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            } else {
                this.value = '';
            }
        });
    });
}