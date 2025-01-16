(function ($) {

    var Details = function ($container, options) {
        var obj = this;
        jQuery.extend(obj.options, options);
        let $form = $('.bookly-js-staff-details', $container),
            $staff_full_name = $('#bookly-full-name', $container),
            $staff_wp_user = $('#bookly-wp-user', $container),
            $staff_email = $('#bookly-email', $container),
            $staff_phone = $('#bookly-phone', $container),
            $staff_locations = $('#bookly-js-locations', $container),
            $staff_gateways_list = $('#bookly-js-gateways-list', $form),
            $staff_gateways = $('#bookly-gateways', $container),
            $staff_color = $('.bookly-js-color-picker', $container);

        if (obj.options.intlTelInput.enabled) {
            window.booklyIntlTelInput($staff_phone.get(0), {
                preferredCountries: [obj.options.intlTelInput.country],
                initialCountry: obj.options.intlTelInput.country,
                geoIpLookup: function (callback) {
                    $.get('https://ipinfo.io', function () {
                    }, 'jsonp').always(function (resp) {
                        var countryCode = (resp && resp.country) ? resp.country : '';
                        callback(countryCode);
                    });
                }
            });
        }

        $staff_wp_user.on('change', function () {
            if (this.value && this.value !== 'create') {
                $staff_full_name.val($staff_wp_user.find(':selected').text());
                $staff_email.val($staff_wp_user.find(':selected').data('email'));
            }
        });
        if ($staff_color.length) {
            $staff_color.wpColorPicker();
        }

        $staff_locations.booklyDropdown();
        $staff_gateways_list.booklyDropdown();

        $container
            .on('click', '.bookly-thumb label', function (e) {
                e.preventDefault();
                e.stopPropagation();
                var frame = wp.media({
                    library: {type: 'image'},
                    multiple: false
                });
                frame
                    .on('select', function () {
                        var selection = frame.state().get('selection').toJSON(),
                            img_src;
                        if (selection.length) {
                            if (selection[0].sizes['thumbnail'] !== undefined) {
                                img_src = selection[0].sizes['thumbnail'].url;
                            } else {
                                img_src = selection[0].url;
                            }
                            $('[name=attachment_id]', $form).val(selection[0].id).trigger('change');
                            $('#bookly-js-staff-avatar', $form).find('.bookly-js-image').css({
                                'background-image': 'url(' + img_src + ')',
                                'background-size': 'cover'
                            });
                            $('.bookly-thumb-delete', $form).show();
                            $('.bookly-thumb', $form).addClass('bookly-thumb-with-image');
                            $(this).hide();
                        }
                    });

                frame.open();
                $(document).off('focusin.modal');
            })
            // Delete staff avatar
            .on('click', '.bookly-thumb-delete', function () {
                var $thumb = $(this).parents('.bookly-js-image');
                $thumb.attr('style', '');
                $('[name=attachment_id]', $form).val('').trigger('change');
                $('.bookly-thumb', $form).removeClass('bookly-thumb-with-image');
                $('.bookly-thumb-delete', $form).hide();
            })
            // Save staff member details.
            .on('click', '#bookly-details-save', function (e) {
                e.preventDefault();
                let ladda = Ladda.create(this),
                    data = booklySerialize.form($form),
                    $staff_phone = $('#bookly-phone', $form),
                    phone;
                ladda.start();
                // for BooklyPro listener in archive.js
                // When button disabled, listeners don't process
                $(this).removeAttr('disabled');

                data.phone = obj.options.intlTelInput.enabled ? booklyGetPhoneNumber($staff_phone.get(0)) : $staff_phone.val();
                $.ajax({
                    type: 'POST',
                    url: ajaxurl,
                    data: booklySerialize.buildRequestData('bookly_update_staff', data),
                    dataType: 'json',
                    xhrFields: {withCredentials: true},
                    crossDomain: 'withCredentials' in new XMLHttpRequest(),
                    success: function (response) {
                        if (response.success) {
                            obj.options.saving({success: [obj.options.l10n.saved]}, response.data.staff);

                            $('.bookly-js-staff-name').text($('#bookly-full-name', $form).val());
                        } else {
                            obj.options.validation(true, response.data.error);
                        }
                        ladda.stop();
                    }
                });
            })
            .on('click', 'button:reset', function () {
                setTimeout(function () {
                    $staff_locations.booklyDropdown('reset');
                    $staff_gateways_list.booklyDropdown('reset');
                }, 0);
            })
            .on('input', '#bookly-email', function () {
                obj.options.validation(this.value == '', '');
            })
            .on('input', '#bookly-full-name', function () {
                obj.options.validation(this.value == '', '');
            })
            .on('change', '[name="gateways"]', function () {
                if (this.value == 'default') {
                    $staff_gateways_list.closest('.form-group').hide();
                } else {
                    $staff_gateways_list.closest('.form-group').show();
                }
            });
        $('[name="gateways"]:checked', $form).trigger('change');
    };

    Details.prototype.options = {
        intlTelInput: {},
        l10n: {},
        validation: function (has_error, info) {
            $(document.body).trigger('staff.validation', ['staff-details', has_error, info]);
        },
        saving: function (alerts, data) {
            $(document.body).trigger('staff.saving', [alerts]);
            if (alerts.hasOwnProperty('success')) {
                $(document.body).trigger('staff.saved', ['staff-details', data]);
            }
        }
    };

    window.BooklyStaffDetails = Details;
})(jQuery);