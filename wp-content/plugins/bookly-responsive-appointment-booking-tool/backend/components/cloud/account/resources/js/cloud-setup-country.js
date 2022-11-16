jQuery(function ($) {
    'use strict';
    let $modal   = $('#bookly-setup-country'),
        $country = $('#bookly-s-country', $modal),
        $setBtn  = $('#bookly-set-country', $modal),
        settings = {
            $country: $('#bookly-country'),
            $invoiceCountry: $('.bookly-js-invoice-country .bookly-js-label')
        }
    ;

    $country.booklySelectCountry({
        dropdownParent: $modal,
        language: {
            noResults: function() { return BooklyCloudPanelL10n.noResults; }
        }
    });
    $.get('https://ipinfo.io', function() {}, 'jsonp').always(function (resp) {
        const countryCode = (resp && resp.country) ? resp.country : '';
        $country.val(countryCode.toLowerCase()).trigger('change');
    });

    $modal.booklyModal('show');

    $setBtn.on('click', function () {
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
                    booklyAlert({success: [BooklyCloudPanelL10n.settingsSaved]});
                    if (BooklyRechargeDialogL10n) {
                        BooklyRechargeDialogL10n.country = country;
                    }
                    settings.$country.val(country).trigger('change');
                    settings.$invoiceCountry.html($country.booklySelect2('data')[0].text);
                    if (response.auto_recharge === 'disabled') {
                        $(document.body).trigger('bookly.auto-recharge.toggle', [false]);
                    }
                    $modal.booklyModal('hide');
                } else {
                    if (response.data && response.data.message) {
                        booklyAlert({error: [response.data.message]});
                    }
                }
            }
        }).always(ladda.stop);
    });
});