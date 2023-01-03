import $ from 'jquery';
import {opt, laddaStart, scrollTo, booklyAjax} from './shared.js';
import stepService from './service_step.js';
import stepExtras from './extras_step.js';
import stepTime from './time_step.js';
import stepRepeat from './repeat_step.js';
import stepDetails from './details_step.js';

/**
 * Cart step.
 */
export default function stepCart(params, error) {
    if (opt[params.form_id].skip_steps.cart) {
        stepDetails(params);
    } else {
        if (params && params.from_step) {
            // Record previous step if it was given in params.
            opt[params.form_id].cart_prev_step = params.from_step;
        }
        let data = $.extend({action: 'bookly_render_cart',}, params),
            $container = opt[params.form_id].$container;
        booklyAjax({
            data
        }).then(response => {
            $container.html(response.html);
            if (error) {
                $('.bookly-label-error', $container).html(error.message);
                $('tr[data-cart-key="' + error.failed_key + '"]', $container).addClass('bookly-label-error');
            } else {
                $('.bookly-label-error', $container).hide();
            }
            scrollTo($container, params.form_id);

            const customJS = response.custom_js;
            $('.bookly-js-next-step', $container).on('click', function (e) {
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

                stepDetails({form_id: params.form_id});
            });
            $('.bookly-add-item', $container).on('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                laddaStart(this);
                stepService({form_id: params.form_id, new_chain: true});
            });
            // 'BACK' button.
            $('.bookly-js-back-step', $container).on('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                laddaStart(this);
                switch (opt[params.form_id].cart_prev_step) {
                    case 'service':
                        stepService({form_id: params.form_id});
                        break;
                    case 'extras':
                        stepExtras({form_id: params.form_id});
                        break;
                    case 'time':
                        stepTime({form_id: params.form_id});
                        break;
                    case 'repeat':
                        stepRepeat({form_id: params.form_id});
                        break;
                    default:
                        stepService({form_id: params.form_id});
                }
            });
            $('.bookly-js-actions button', $container).on('click', function () {
                laddaStart(this);
                let $this = $(this),
                    $cart_item = $this.closest('tr');
                switch ($this.data('action')) {
                    case 'drop':
                        booklyAjax({
                            data: {
                                action: 'bookly_cart_drop_item',
                                form_id: params.form_id,
                                cart_key: $cart_item.data('cart-key')
                            }
                        }).then(response => {
                            let remove_cart_key = $cart_item.data('cart-key'),
                                $trs_to_remove  = $('tr[data-cart-key="'+remove_cart_key+'"]', $container)
                            ;
                            $cart_item.delay(300).fadeOut(200, function () {
                                if (response.data.total_waiting_list) {
                                    $('.bookly-js-waiting-list-price', $container).html(response.data.waiting_list_price);
                                    $('.bookly-js-waiting-list-deposit', $container).html(response.data.waiting_list_deposit);
                                } else {
                                    $('.bookly-js-waiting-list-price', $container).closest('tr').remove();
                                }
                                $('.bookly-js-subtotal-price', $container).html(response.data.subtotal_price);
                                $('.bookly-js-subtotal-deposit', $container).html(response.data.subtotal_deposit);
                                $('.bookly-js-pay-now-deposit', $container).html(response.data.pay_now_deposit);
                                $('.bookly-js-pay-now-tax', $container).html(response.data.pay_now_tax);
                                $('.bookly-js-total-price', $container).html(response.data.total_price);
                                $('.bookly-js-total-tax', $container).html(response.data.total_tax);
                                $trs_to_remove.remove();
                                if ($('tr[data-cart-key]').length == 0) {
                                    $('.bookly-js-back-step', $container).hide();
                                    $('.bookly-js-next-step', $container).hide();
                                }
                            });
                        });
                        break;
                    case 'edit':
                        stepService({form_id: params.form_id, edit_cart_item : $cart_item.data('cart-key')});
                        break;
                }
            });
        });
    }
}