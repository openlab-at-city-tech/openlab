jQuery(function ($) {
    'use strict';

    const $prices = $('.bookly-js-product-price-selector'),
        component = {
            items: '[data-product-price-id]',
            dropdown: '.bookly-js-product-price-dropdown',
            enable: '.bookly-js-product-enable',
            enable_pc: '.bookly-js-product-enable-pc',
            disable: '.bookly-js-product-disable',
        },
        $onOffButtons = $('.bookly-js-product-enable,.bookly-js-product-enable-pc,.bookly-js-product-disable'),
        $infoButtons = $('.bookly-js-product-info-button'),
        $revertCancelButtons = $('.bookly-js-product-revert-cancel'),
        $updateRequiredButtons = $('.bookly-js-bookly-update-required'),
        $requiredBooklyPro = $('.bookly-js-required-bookly-pro'),
        $infoModal = $('#bookly-product-info-modal'),
        infoModal = {
            $loading: $('.bookly-js-loading', $infoModal),
            $content: $('#bookly-info-content', $infoModal),
            $title: $('.modal-title', $infoModal)
        },
        $activationModal = $('#bookly-product-activation-modal'),
        activationModal = {
            $title: $('.modal-title', $activationModal),
            $success: $('.bookly-js-success', $activationModal),
            $fail: $('.bookly-js-fail', $activationModal),
            $content: $('.bookly-js-content', $activationModal),
            $button: $('.bookly-js-action-btn', $activationModal)
        },
        $unsubscribeModal = $('#bookly-product-unsubscribe-modal'),
        $cancelSubscriptionButton = $('#bookly-cancel-subscription'),
        $cancelSubscriptionMethod = $('#bookly_cancel_subscription_method'),
        hash = window.location.href.split('#');

    $prices
        .on('click', component.items, function () {
            const $selector = $(this).parents('.bookly-js-product-price-selector'),
                product = $selector.data('product'),
                productPriceId = $(this).data('product-price-id'),
                $card = $('[data-product="' + product + '"]')
            ;
            if ($(this).data('type') === 'purchase_code') {
                $(this).closest('.bookly-js-cloud-product').find('.bookly-js-product-purchase-code-wrap').toggle(!BooklyL10n.products[product].active);
                $(component.enable, $card).toggle(false);
                $(component.enable_pc, $card).toggle(true);
            } else {
                $(this).closest('.bookly-js-cloud-product').find('.bookly-js-product-purchase-code-wrap').hide();
                $(component.enable, $card).toggle(true);
                $(component.enable_pc, $card).toggle(false);
            }
            $selector.data('pp-id', productPriceId);
            $('.bookly-js-product-price', $selector).html($(this).html());
        });

    for (var product in BooklyL10n.products) {
        let productActive = BooklyL10n.products[product].active,
            $card = $('[data-product="' + product + '"]')
        ;
        if (productActive) {
            $card.removeClass('bg-light').addClass('bg-white');
            $(component.items, $card).each(function () {
                const productPriceId = $(this).data('product-price-id');
                let selected = false;
                if (BooklyL10n.products[product].activated_by_pc && productPriceId === 0) {
                    selected = true;
                } else {
                    BooklyL10n.subscriptions.forEach(function (item) {
                        if (selected === false && item.product_price_id == productPriceId) {
                            selected = true;
                        }
                    });
                }
                if (selected) {
                    $(this).trigger('click');
                }
            });
        }

        $(component.enable_pc, $card).toggle(!productActive);
        $(component.enable, $card).toggle(!productActive);
        $(component.disable, $card).toggle(productActive);
        $(component.dropdown, $card).prop('disabled', productActive).toggleClass('disabled', productActive);

        // Hide prices selector if only one price available
        if ($(component.dropdown, $card).length && $(component.dropdown, $card).closest('.dropdown').find('.dropdown-menu li.dropdown-item').length < 2) {
            $(component.dropdown, $card).closest('.dropdown').hide();
        }

        if (!productActive) {
            if ($('.bookly-js-best-offer', $card).length > 0) {
                $('.bookly-js-best-offer', $card).trigger('click');
            } else if ($('.bookly-js-users-choice', $card).length > 0) {
                $('.bookly-js-users-choice', $card).trigger('click')
            } else {
                $(component.items + ':first', $card).first().trigger('click');
            }
        }
    }

    $infoButtons.on('click', function () {
        const ladda = Ladda.create(this);
        const product = $(this).closest('.bookly-js-cloud-product').data('product');
        ladda.start();
        infoModal.$loading.show();
        infoModal.$title.html(BooklyL10n.products[product].info_title).show();
        infoModal.$content.hide();
        $infoModal.booklyModal('show');
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_cloud_get_product_info',
                product: product,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    infoModal.$loading.hide();
                    infoModal.$content.html(response.data.html).show();
                } else {
                    booklyAlert({error: [response.data.message]});
                }
            }
        }).always(ladda.stop);
    });

    $('.bookly-js-product-login-button').on('click', function (e) {
        e.preventDefault();
        $(document.body).trigger('bookly.cloud.auth.form', ['login']);
        $('#bookly-cloud-auth-modal').booklyModal('show');
    });

    $onOffButtons.on('click', function () {
        const $button = $(this);
        const product = $(this).closest('.bookly-js-cloud-product').data('product');
        const status = $button.hasClass('bookly-js-product-enable') || $button.hasClass('bookly-js-product-enable-pc') ? 1 : 0;
        let product_price, purchase_code;
        if (status) {
            product_price = $(this).closest('.bookly-js-product-price-selector').data('pp-id');
            purchase_code = $(this).closest('.bookly-js-product-price-selector').find('.bookly-js-product-purchase-code-wrap input').val();
        }
        if (!status && BooklyL10n.products[product].has_subscription && !BooklyL10n.products[product].activated_by_pc) {
            $unsubscribeModal.data('product', product);
            $unsubscribeModal.booklyModal('show');
        } else {
            changeProductStatus(product, status, product_price, purchase_code, $button)
        }
    });

    $revertCancelButtons.on('click', function () {
        const product = $(this).closest('.bookly-js-cloud-product').data('product');
        switch (product) {
            case 'sms':
            case 'stripe':
                return;
        }
        const $button = $(this);
        const ladda = Ladda.create($button.get(0));
        ladda.start();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_cloud_revert_cancel_subscription',
                product: product,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    booklyAlert({error: [response.data.message]});
                    ladda.stop();
                }
            }
        });
    });

    $cancelSubscriptionButton.on('click', function () {
        changeProductStatus($unsubscribeModal.data('product'), $cancelSubscriptionMethod.find("input:checked").val(), 0, '', $(this));
    });

    function changeProductStatus(product, status, product_price, purchase_code, $button) {
        const ladda = Ladda.create($button.get(0));
        let action;
        switch (product) {
            case 'stripe':
                action = 'bookly_cloud_stripe_change_status';
                break;
            case 'sms':
                action = 'bookly_cloud_sms_change_status';
                break;
            case 'zapier':
            case 'cron':
            case 'voice':
            case 'square':
            case 'mobile-staff-cabinet':
            default:
                action = 'bookly_cloud_change_product_status';
                break;
        }

        ladda.start();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: action,
                status: status,
                product_price: product_price,
                purchase_code: purchase_code,
                product: product,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    if (status == '1') {
                        window.location.href = response.data.redirect_url;
                        if (product !== 'stripe') {
                            window.location.reload();
                        }
                    } else {
                        window.location.reload();
                    }
                } else {
                    if (response.data.hasOwnProperty('offer_to_top_up_balance')) {
                        $('#bookly-cloud-panel .bookly-js-recharge-dialog-activator').trigger('click');
                    }
                    booklyAlert({error: [response.data.message]});
                    ladda.stop();
                }
            }
        });
    }

    function showProductActivationMessage(product, status) {
        $activationModal.booklyModal('show');
        activationModal.$title.html(BooklyL10n.products[product].title);
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_cloud_get_product_activation_message',
                product: product,
                status: status,
                csrf_token: BooklyL10nGlobal.csrf_token
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    activationModal.$success.show();
                    activationModal.$content.html(response.data.content);
                    if (response.data.button) {
                        activationModal.$button
                            .find('span').html(response.data.button.caption).end().off()
                            .on('click', function () {
                                window.location.href = response.data.button.url;
                            })
                            .show();
                    }
                } else {
                    activationModal.$fail.show();
                    activationModal.$content.html(response.data.content);
                }
            }
        });
    }

    $updateRequiredButtons.on('click', function (e) {
        $('#bookly-product-update-required-modal').booklyModal('show');
    });

    $activationModal
        .on('show.bs.modal', function () {
            activationModal.$success.hide();
            activationModal.$fail.hide();
            activationModal.$content.html('<div class="bookly-loading"></div>');
            activationModal.$button.hide();
        });

    if (hash.length > 1) {
        let hashObj = {};
        hash[1].split('&').forEach(function (part) {
            var params = part.split('=');
            hashObj[params[0]] = params[1];
        });

        if (hashObj.hasOwnProperty('cloud-product')) {
            if (hashObj.hasOwnProperty('status')) {
                showProductActivationMessage(hashObj['cloud-product'], hashObj['status']);
                if ('pushState' in history) {
                    history.pushState('', document.title, window.location.pathname + window.location.search);
                } else {
                    window.location.href = '#';
                }
            }
        }
    }

    $requiredBooklyPro.on('click', function () {
        requiredBooklyPro();
    });
});