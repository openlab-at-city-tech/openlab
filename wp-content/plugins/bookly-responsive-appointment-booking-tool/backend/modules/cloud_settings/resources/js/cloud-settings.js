jQuery(function ($) {
    'use strict';

    const $invoiceCountry = $('.bookly-js-invoice-country .bookly-js-label');
    const $country = $('#bookly-country');

    let invoiceDataValid = true;


    /**
     * Country tab
     */
    $country
        .booklySelectCountry()
        .val(BooklyL10n.country).trigger('change')
    ;

    $('#bookly-update-country').on('click', function () {
        const ladda = Ladda.create(this);
        ladda.start();
        const country = $country.val();
        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                action: 'bookly_change_country',
                csrf_token: BooklyL10nGlobal.csrf_token,
                country: country
            },
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    booklyAlert({success: [BooklyL10n.settingsSaved]});
                    BooklyL10n.country = country;
                    if (BooklyRechargeDialogL10n) {
                        BooklyRechargeDialogL10n.country = country;
                    }
                    $invoiceCountry.html($country.booklySelect2('data')[0].text);
                    if (response.auto_recharge === 'disabled') {
                        $(document.body).trigger('bookly.auto-recharge.toggle', [false]);
                    }
                } else {
                    if (response.data && response.data.message) {
                        booklyAlert({error: [response.data.message]});
                    }
                }
            }
        }).always(ladda.stop);
    });

    /**
     * Invoice tab
     */
    if ($country.length && $country.booklySelect2('data').length > 0) {
        $invoiceCountry.html($country.booklySelect2('data')[0].text);
    }

    $('.bookly-js-invoice-country a').on('click', function (e) {
        e.preventDefault();
        $('.nav-link[href="#bookly-country-tab"]').trigger('click');
    });

    $('#bookly-save-invoice').on('click', function (e) {
        e.preventDefault();
        const $form = $(this).closest('form');
        invoiceDataValid = true;
        $('input[required]', $form).each(function () {
            if ($(this).val() === '') {
                $(this).addClass('is-invalid');
                invoiceDataValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });

        if (invoiceDataValid) {
            const ladda = Ladda.create(this);
            ladda.start();
            const data = $form.serializeArray();
            data.push({name: 'action', value: 'bookly_save_invoice_data'});
            data.push({name: 'csrf_token', value: BooklyL10nGlobal.csrf_token});
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        booklyAlert({success: [BooklyL10n.settingsSaved]});
                    } else {
                        booklyAlert({error: [response.data.message]});
                    }
                }
            }).always(ladda.stop);
        } else {
            return false;
        }
    }).closest('form').find('input[required]').each(function () {
        if (this.value === '') {
            invoiceDataValid = false;
        }
    });
    $('input[name="invoice[send]"]').on('change', function () {
        $(this).closest('.form-group').next().toggle(this.checked);
    }).trigger('change');

    /**
     * Notifications tab
     */
    $('#bookly-account-notifications-tab :checkbox').on('change', function () {
        let $checkbox = $(this);
        $checkbox.prop('disabled', true).addClass('bookly-checkbox-loading');
        $.get(
            ajaxurl,
            {
                action: 'bookly_admin_notify',
                csrf_token: BooklyL10nGlobal.csrf_token,
                option_name: $checkbox.attr('name'),
                value: $checkbox.is(':checked') ? 1 : 0
            },
            function () {
            },
            'json'
        ).always(function () {
            $checkbox.prop('disabled', false).removeClass('bookly-checkbox-loading');
        });
    });

    /**
     * Change password tab
     */
    $('#bookly-change-password').on('click', function (e) {
        e.preventDefault();
        const $form = $(this).closest('form');
        const $oldPassword = $form.find('#old_password');
        const $newPassword = $form.find('#new_password');
        const $repeatPassword = $form.find('#new_password_repeat');
        $oldPassword.toggleClass('is-invalid', $oldPassword.val() === '');
        $newPassword.toggleClass('is-invalid', $newPassword.val() === '');
        if ($oldPassword.val() !== '' && $newPassword.val() !== '') {
            if ($newPassword.val() === $repeatPassword.val()) {
                $newPassword.removeClass('is-invalid');
                $repeatPassword.removeClass('is-invalid');
                const ladda = Ladda.create(this);
                ladda.start();
                const data = $form.serializeArray();
                data.push({name: 'action', value: 'bookly_change_password'});
                data.push({name: 'csrf_token', value: BooklyL10nGlobal.csrf_token});
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: data,
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            booklyAlert({success: [BooklyL10n.settingsSaved]});
                            $form.trigger('reset');
                        } else {
                            if (response.data && response.data.message) {
                                booklyAlert({error: [response.data.message]});
                            }
                        }
                    }
                }).always(ladda.stop);
            } else {
                booklyAlert({error: [BooklyL10n.passwords_no_match]});
                $newPassword.addClass('is-invalid');
                $repeatPassword.addClass('is-invalid');
            }
        }
    });
});