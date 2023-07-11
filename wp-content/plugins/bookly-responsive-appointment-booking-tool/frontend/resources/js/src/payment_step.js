import $ from 'jquery';
import {opt, laddaStart, scrollTo, booklyAjax} from './shared.js';
import stepCart from './cart_step.js';
import stepTime from './time_step.js';
import stepDetails from './details_step.js';
import stepComplete from './complete_step.js';

/**
 * Payment step.
 */
export default function stepPayment(params) {
    var $container = opt[params.form_id].$container;
    booklyAjax({
        type: 'POST',
        data: {
            action: 'bookly_render_payment',
            form_id: params.form_id,
            page_url: document.URL.split('#')[0]
        }
    }).then(response => {
        // If payment step is disabled.
        if (response.disabled) {
            save(params.form_id);
            return;
        }

        $container.html(response.html);
        scrollTo($container, params.form_id);
        if (opt[params.form_id].status.booking == 'cancelled') {
            opt[params.form_id].status.booking = 'ok';
        }

        const customJS = response.custom_js;
        let $stripe_card_field = $('#bookly-stripe-card-field', $container);

        // Init stripe intents form
        if ($stripe_card_field.length) {
            if (response.stripe_publishable_key) {
                var stripe = Stripe(response.stripe_publishable_key, {
                    betas: ['payment_intent_beta_3']
                });
                var elements = stripe.elements();

                var stripe_card = elements.create('cardNumber');
                stripe_card.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-field");

                var stripe_expiry = elements.create('cardExpiry');
                stripe_expiry.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-expiry-field");

                var stripe_cvc = elements.create('cardCvc');
                stripe_cvc.mount("#bookly-form-" + params.form_id + " #bookly-stripe-card-cvc-field");
            } else {
                $('.pay-card .bookly-js-next-step', $container).prop('disabled', true);
                let $details = $stripe_card_field.closest('.bookly-js-details');
                $('.bookly-form-group', $details).hide();
                $('.bookly-js-card-error', $details).text('Please call Stripe() with your publishable key. You used an empty string.');
            }
        }

        var $payments = $('.bookly-payment', $container),
            $apply_coupon_button = $('.bookly-js-apply-coupon', $container),
            $coupon_input = $('input.bookly-user-coupon', $container),
            $coupon_error = $('.bookly-js-coupon-error', $container),
            $apply_gift_card_button = $('.bookly-js-apply-gift-card', $container),
            $gift_card_input = $('input.bookly-user-gift', $container),
            $gift_card_error = $('.bookly-js-gift-card-error', $container),
            $apply_tips_button = $('.bookly-js-apply-tips', $container),
            $applied_tips_button = $('.bookly-js-applied-tips', $container),
            $tips_input = $('input.bookly-user-tips', $container),
            $tips_error = $('.bookly-js-tips-error', $container),
            $deposit_mode = $('input[type=radio][name=bookly-full-payment]', $container),
            $coupon_info_text = $('.bookly-info-text-coupon', $container),
            $buttons = $('.bookly-gateway-buttons,.bookly-js-details', $container),
            $payment_details
        ;
        $payments.on('click', function () {
            $buttons.hide();
            $('.bookly-gateway-buttons.pay-' + $(this).val(), $container).show();
            if ($(this).data('with-details') == 1) {
                let $parent = $(this).closest('.bookly-list');
                $payment_details = $('.bookly-js-details', $parent);
                $('.bookly-js-details', $parent).show();
            } else {
                $payment_details = null;
            }
        });
        $payments.eq(0).trigger('click');

        $deposit_mode.on('change', function () {
            let data = {
                action: 'bookly_deposit_payments_apply_payment_method',
                form_id: params.form_id,
                deposit_full: $(this).val()
            };
            $(this).hide();
            $(this).prev().css('display', 'inline-block');
            booklyAjax({
                type: 'POST',
                data: data,
            }).then(response => {
                stepPayment({form_id: params.form_id});
            });
        });

        $apply_coupon_button.on('click', function (e) {
            var ladda = laddaStart(this);
            $coupon_error.text('');
            $coupon_input.removeClass('bookly-error');

            let data = {
                action: 'bookly_coupons_apply_coupon',
                form_id: params.form_id,
                coupon_code: $coupon_input.val()
            };

            booklyAjax({
                type: 'POST',
                data: data,
                error: function () {
                    ladda.stop();
                }
            }).then(response => {
                stepPayment({form_id: params.form_id});
            }).catch(response => {
                $coupon_error.html(response.error);
                $coupon_input.addClass('bookly-error');
                $coupon_info_text.html(response.text);
                scrollTo($coupon_error, params.form_id);
            }).finally(() => { ladda.stop(); });
        });

        $apply_gift_card_button.on('click', function (e) {
            var ladda = laddaStart(this);
            $gift_card_error.text('');
            $gift_card_input.removeClass('bookly-error');

            let data = {
                action: 'bookly_pro_apply_gift_card',
                form_id: params.form_id,
                gift_card: $gift_card_input.val()
            };

            booklyAjax({
                type: 'POST',
                data: data,
                error: function () {
                    ladda.stop();
                }
            }).then(response => {
                stepPayment({form_id: params.form_id});
            }).catch(response => {
                $gift_card_error.html(response.error);
                $gift_card_input.addClass('bookly-error');
                scrollTo($gift_card_error, params.form_id);
            }).finally(() => { ladda.stop(); });
        });

        $tips_input.on('keyup', function () {
            $applied_tips_button.hide();
            $apply_tips_button.css('display', 'inline-block');
        });

        $apply_tips_button.on('click', function (e) {
            var ladda = laddaStart(this);
            $tips_error.text('');
            $tips_input.removeClass('bookly-error');

            var data = {
                action: 'bookly_pro_apply_tips',
                form_id: params.form_id,
                tips: $tips_input.val()
            };

            booklyAjax({
                type: 'POST',
                data: data,
                error: function () {
                    ladda.stop();
                }
            }).then(response => {
                stepPayment({form_id: params.form_id});
            }).catch(response => {
                $tips_error.html(response.error);
                $tips_input.addClass('bookly-error');
                scrollTo($tips_error, params.form_id);
                ladda.stop();
            });
        });

        $('.bookly-js-next-step', $container).on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            var ladda = laddaStart(this),
                $form
            ;

            // Execute custom JavaScript
            if (customJS) {
                try {
                    $.globalEval(customJS.next_button);
                } catch (e) {
                    // Do nothing
                }
            }

            if ($('.bookly-payment[value=local]', $container).is(':checked') || $(this).hasClass('bookly-js-coupon-payment')) {
                // handle only if was selected local payment !
                e.preventDefault();
                save(params.form_id);
            } else if ($('.bookly-payment[value=card]', $container).is(':checked')) {
                if ($('.bookly-payment[data-form=stripe]', $container).is(':checked')) {
                    booklyAjax({
                        type: 'POST',
                        data: {
                            action: 'bookly_stripe_create_intent',
                            form_id: params.form_id
                        }
                    }).then(response => {
                        stripe.confirmCardPayment(
                            response.intent_secret,
                            {
                                payment_method: {
                                    card: stripe_card
                                }
                            }
                        ).then(function (result) {
                            if (result.error) {
                                booklyAjax({
                                    type: 'POST',
                                    data: {
                                        action: 'bookly_stripe_failed_payment',
                                        form_id: params.form_id,
                                        intent_id: response.intent_id
                                    }
                                }).then(response => {
                                    ladda.stop();
                                    $('.bookly-js-card-error', $payment_details).text(result.error.message);
                                });
                            } else {
                                stepComplete({form_id: params.form_id});
                            }
                        });
                    }).catch(response => {
                            if (response.error === 'cart_item_not_available') {
                                handleErrorCartItemNotAvailable(response, params.form_id);
                            }
                            ladda.stop();
                            $('.bookly-js-card-error', $payment_details).text(response.error_message);
                        }
                    )
                } else {
                    e.preventDefault();
                    let data = {
                            action: 'bookly_authorize_net_aim_payment',
                            card: {
                                number: $('input[name="card_number"]', $payment_details).val(),
                                cvc: $('input[name="card_cvc"]', $payment_details).val(),
                                exp_month: $('select[name="card_exp_month"]', $payment_details).val(),
                                exp_year: $('select[name="card_exp_year"]', $payment_details).val()
                            },
                            form_id: params.form_id
                        },
                        cardPayment = function (data) {
                            booklyAjax({
                                type: 'POST',
                                data: data
                            }).then(response => {
                                stepComplete({form_id: params.form_id});
                            }).catch(response => {
                                if (response.error == 'cart_item_not_available') {
                                    handleErrorCartItemNotAvailable(response, params.form_id);
                                } else if (response.error == 'payment_error') {
                                    ladda.stop();
                                    $('.bookly-js-card-error', $payment_details).text(response.error_message);
                                }
                            });
                        };
                    cardPayment(data);
                }
            } else if ($('.bookly-js-checkout', $container).is(':checked')) {
                e.preventDefault();
                $form = $(this).closest('form');
                let $payment_id = $('input.bookly-payment-id', $form),
                    $payment_token = $('input.bookly-js-add-payment-token', $form);
                if ($payment_id.length > 0 || $payment_token.length > 0) {
                    booklyAjax({
                        type: 'POST',
                        data: {
                            action: 'bookly_pro_save_pending_appointment',
                            form_id: params.form_id,
                            payment_type: $form.data('gateway')
                        }
                    }).then(response => {
                        $payment_id.val(response.payment.id);
                        if ($payment_token.length) {
                            let url = $payment_token.val();
                            url += (url.indexOf('?') ? '&' : '?') + 'bookly-token=' + response.payment.token;
                            $payment_token.val(url);
                        }
                        $form.submit();
                    }).catch(response => {
                        if (response.error == 'cart_item_not_available') {
                            handleErrorCartItemNotAvailable(response, params.form_id);
                        }
                    });
                } else {
                    booklyAjax({
                        type: 'POST',
                        data: {
                            action: 'bookly_check_cart',
                            form_id: params.form_id
                        }
                    }).then(response => {
                        $form.submit();
                    }).catch(response => {
                        if (response.error == 'cart_item_not_available') {
                            handleErrorCartItemNotAvailable(response, params.form_id);
                        }
                    });
                }
            }
        });

        $('.bookly-js-back-step', $container).on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);
            stepDetails({form_id: params.form_id});
        });
    });
}

/**
 * Save appointment.
 */
function save(form_id) {
    booklyAjax({
        type: 'POST',
        data: {
            action: 'bookly_save_appointment',
            form_id: form_id
        }
    }).then(response => {
        stepComplete({form_id: form_id});
    }).catch(response => {
        if (response.error == 'cart_item_not_available') {
            handleErrorCartItemNotAvailable(response, form_id);
        }
    });
}

/**
 * Handle error with code 3 which means one of the cart item is not available anymore.
 *
 * @param response
 * @param form_id
 */
function handleErrorCartItemNotAvailable(response, form_id) {
    if (!opt[form_id].skip_steps.cart) {
        stepCart({form_id: form_id}, {
            failed_key: response.failed_cart_key,
            message: opt[form_id].errors[response.error]
        });
    } else {
        stepTime({form_id: form_id}, opt[form_id].errors[response.error]);
    }
}