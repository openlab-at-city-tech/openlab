import $ from 'jquery';
import {opt, laddaStart, scrollTo, booklyAjax} from './shared.js';
import stepTime from './time_step.js';
import stepRepeat from './repeat_step.js';
import stepCart from './cart_step.js';
import stepPayment from './payment_step.js';
import stepComplete from './complete_step.js';
import stepService from "./service_step";
import stepExtras from "./extras_step";

/**
 * Details step.
 */
export default function stepDetails(params) {
    let data = $.extend({action: 'bookly_render_details',}, params),
        $container = opt[params.form_id].$container;
    booklyAjax({
        data
    }).then(response => {
        $container.html(response.html);
        scrollTo($container, params.form_id);

        let intlTelInput = response.intlTelInput,
            update_details_dialog = response.update_details_dialog,
            woocommerce = response.woocommerce,
            customJS = response.custom_js,
            custom_fields_conditions = response.custom_fields_conditions || [],
            terms_error = response.l10n.terms_error
        ;

        if (opt[params.form_id].hasOwnProperty('google_maps') && opt[params.form_id].google_maps.enabled) {
            booklyInitGooglePlacesAutocomplete($container);
        }

        $(document.body).trigger('bookly.render.step_detail', [$container]);
        // Init.
        let phone_number = '',
            $guest_info = $('.bookly-js-guest', $container),
            $phone_field = $('.bookly-js-user-phone-input', $container),
            $email_field = $('.bookly-js-user-email', $container),
            $email_confirm_field = $('.bookly-js-user-email-confirm', $container),
            $birthday_day_field = $('.bookly-js-select-birthday-day', $container),
            $birthday_month_field = $('.bookly-js-select-birthday-month', $container),
            $birthday_year_field = $('.bookly-js-select-birthday-year', $container),

            $address_country_field = $('.bookly-js-address-country', $container),
            $address_state_field = $('.bookly-js-address-state', $container),
            $address_postcode_field = $('.bookly-js-address-postcode', $container),
            $address_city_field = $('.bookly-js-address-city', $container),
            $address_street_field = $('.bookly-js-address-street', $container),
            $address_street_number_field = $('.bookly-js-address-street_number', $container),
            $address_additional_field = $('.bookly-js-address-additional_address', $container),

            $address_country_error = $('.bookly-js-address-country-error', $container),
            $address_state_error = $('.bookly-js-address-state-error', $container),
            $address_postcode_error = $('.bookly-js-address-postcode-error', $container),
            $address_city_error = $('.bookly-js-address-city-error', $container),
            $address_street_error = $('.bookly-js-address-street-error', $container),
            $address_street_number_error = $('.bookly-js-address-street_number-error', $container),
            $address_additional_error = $('.bookly-js-address-additional_address-error', $container),

            $birthday_day_error = $('.bookly-js-select-birthday-day-error', $container),
            $birthday_month_error = $('.bookly-js-select-birthday-month-error', $container),
            $birthday_year_error = $('.bookly-js-select-birthday-year-error', $container),
            $full_name_field = $('.bookly-js-full-name', $container),
            $first_name_field = $('.bookly-js-first-name', $container),
            $last_name_field = $('.bookly-js-last-name', $container),
            $notes_field = $('.bookly-js-user-notes', $container),
            $custom_field = $('.bookly-custom-field', $container),
            $info_field = $('.bookly-js-info-field', $container),
            $phone_error = $('.bookly-js-user-phone-error', $container),
            $email_error = $('.bookly-js-user-email-error', $container),
            $email_confirm_error = $('.bookly-js-user-email-confirm-error', $container),
            $name_error = $('.bookly-js-full-name-error', $container),
            $first_name_error = $('.bookly-js-first-name-error', $container),
            $last_name_error = $('.bookly-js-last-name-error', $container),
            $captcha = $('.bookly-js-captcha-img', $container),
            $custom_error = $('.bookly-custom-field-error', $container),
            $info_error = $('.bookly-js-info-field-error', $container),
            $modals = $('.bookly-js-modal', $container),
            $login_modal = $('.bookly-js-login', $container),
            $cst_modal = $('.bookly-js-cst-duplicate', $container),
            $verification_modal = $('.bookly-js-verification-code', $container),
            $verification_code = $('#bookly-verification-code', $container),
            $next_btn = $('.bookly-js-next-step', $container),

            $errors = $([
                $birthday_day_error,
                $birthday_month_error,
                $birthday_year_error,
                $address_country_error,
                $address_state_error,
                $address_postcode_error,
                $address_city_error,
                $address_street_error,
                $address_street_number_error,
                $address_additional_error,
                $name_error,
                $first_name_error,
                $last_name_error,
                $phone_error,
                $email_error,
                $email_confirm_error,
                $custom_error,
                $info_error
            ]).map($.fn.toArray),

            $fields = $([
                $birthday_day_field,
                $birthday_month_field,
                $birthday_year_field,
                $address_city_field,
                $address_country_field,
                $address_postcode_field,
                $address_state_field,
                $address_street_field,
                $address_street_number_field,
                $address_additional_field,
                $full_name_field,
                $first_name_field,
                $last_name_field,
                $phone_field,
                $email_field,
                $email_confirm_field,
                $custom_field,
                $info_field
            ]).map($.fn.toArray)
        ;

        // Populate form after login.
        var populateForm = function (response) {
            $full_name_field.val(response.data.full_name).removeClass('bookly-error');
            $first_name_field.val(response.data.first_name).removeClass('bookly-error');
            $last_name_field.val(response.data.last_name).removeClass('bookly-error');

            if (response.data.birthday) {

                var dateParts = response.data.birthday.split('-'),
                    year = parseInt(dateParts[0]),
                    month = parseInt(dateParts[1]),
                    day = parseInt(dateParts[2]);

                $birthday_day_field.val(day).removeClass('bookly-error');
                $birthday_month_field.val(month).removeClass('bookly-error');
                $birthday_year_field.val(year).removeClass('bookly-error');
            }

            if (response.data.phone) {
                $phone_field.removeClass('bookly-error');
                if (intlTelInput.enabled) {
                    $phone_field.intlTelInput('setNumber', response.data.phone);
                } else {
                    $phone_field.val(response.data.phone);
                }
            }

            if (response.data.country) {
                $address_country_field.val(response.data.country).removeClass('bookly-error');
            }
            if (response.data.state) {
                $address_state_field.val(response.data.state).removeClass('bookly-error');
            }
            if (response.data.postcode) {
                $address_postcode_field.val(response.data.postcode).removeClass('bookly-error');
            }
            if (response.data.city) {
                $address_city_field.val(response.data.city).removeClass('bookly-error');
            }
            if (response.data.street) {
                $address_street_field.val(response.data.street).removeClass('bookly-error');
            }
            if (response.data.street_number) {
                $address_street_number_field.val(response.data.street_number).removeClass('bookly-error');
            }
            if (response.data.additional_address) {
                $address_additional_field.val(response.data.additional_address).removeClass('bookly-error');
            }

            $email_field.val(response.data.email).removeClass('bookly-error');
            if (response.data.info_fields) {
                response.data.info_fields.forEach(function (field) {
                    var $info_field = $container.find('.bookly-js-info-field-row[data-id="' + field.id + '"]');
                    switch ($info_field.data('type')) {
                        case 'checkboxes':
                            field.value.forEach(function (value) {
                                $info_field.find('.bookly-js-info-field').filter(function () {
                                    return this.value == value;
                                }).prop('checked', true);
                            });
                            break;
                        case 'radio-buttons':
                            $info_field.find('.bookly-js-info-field').filter(function () {
                                return this.value == field.value;
                            }).prop('checked', true);
                            break;
                        default:
                            $info_field.find('.bookly-js-info-field').val(field.value);
                            break;
                    }
                });
            }
            $errors.filter(':not(.bookly-custom-field-error)').html('');
        };

        // Conditional custom fields
        $('.bookly-custom-field-row').on('change', 'select, input[type="checkbox"], input[type="radio"]', function () {
            let $row = $(this).closest('.bookly-custom-field-row'),
                id = $row.data('id'),
                $that = $(this)
            ;
            $.each(custom_fields_conditions, function (i, condition) {
                let $target = $('.bookly-custom-field-row[data-id="' + condition.target + '"]');
                if (parseInt(condition.source) === id) {
                    switch ($row.data('type')) {
                        case 'drop-down':
                        case 'radio-buttons':
                            if ((condition.value.includes($that.val()) && condition.equal === '1') || (!condition.value.includes($that.val()) && condition.equal !== '1')) {
                                $target.show();
                            } else {
                                $target.hide();
                            }
                            break;
                        case 'checkboxes':
                            let show = false;
                            $row.find('input').each(function () {
                                if ($(this).prop('checked') && ((condition.value.includes($(this).val()) && condition.equal === '1') || (!condition.value.includes($(this).val()) && condition.equal !== '1'))) {
                                    show = true;
                                }
                            });
                            $target.toggle(show);
                            break;
                    }
                }
            });
        });
        // Custom fields date fields
        $('.bookly-js-cf-date', $container).each(function () {
            let $cf_date = $(this);
            $cf_date.pickadate({
                formatSubmit: 'yyyy-mm-dd',
                format: opt[params.form_id].date_format,
                min: $(this).data('min') !== '' ? $(this).data('min').split('-').map(function (value, index) { if (index === 1) return value - 1; else return parseInt(value);}) : false,
                max: $(this).data('max') !== '' ? $(this).data('max').split('-').map(function (value, index) { if (index === 1) return value - 1; else return parseInt(value);}) : false,
                clear: false,
                close: false,
                today: BooklyL10n.today,
                monthsFull: BooklyL10n.months,
                weekdaysFull: BooklyL10n.days,
                weekdaysShort: BooklyL10n.daysShort,
                labelMonthNext: BooklyL10n.nextMonth,
                labelMonthPrev: BooklyL10n.prevMonth,
                firstDay: opt[params.form_id].firstDay,
                onClose: function () {
                    // Hide for skip tab navigations by days of the month when the calendar is closed
                    $('#' + $cf_date.attr('aria-owns')).hide();
                },
            }).focusin(function () {
                // Restore calendar visibility, changed on onClose
                $('#' + $cf_date.attr('aria-owns')).show();
            });
        });

        if (intlTelInput.enabled) {
            $phone_field.intlTelInput({
                preferredCountries: [intlTelInput.country],
                initialCountry: intlTelInput.country,
                geoIpLookup: function (callback) {
                    $.get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
                        var countryCode = (resp && resp.country) ? resp.country : '';
                        callback(countryCode);
                    });
                },
                utilsScript: intlTelInput.utils
            });
        }
        // Init modals.
        $container.find('.bookly-js-modal.' + params.form_id).remove();

        $modals
            .addClass(params.form_id).appendTo($container)
            .on('click', '.bookly-js-close', function (e) {
                e.preventDefault();
                $(e.delegateTarget).removeClass('bookly-in')
                    .find('form').trigger('reset').end()
                    .find('input').removeClass('bookly-error').end()
                    .find('.bookly-label-error').html('')
                ;
            })
        ;
        // Login modal.
        $('.bookly-js-login-show', $container).on('click', function (e) {
            e.preventDefault();
            $login_modal.addClass('bookly-in');
        });
        $('button:submit', $login_modal).on('click', function (e) {
            e.preventDefault();
            var ladda = Ladda.create(this);
            ladda.start();
            booklyAjax({
                type: 'POST',
                data: {
                    action: 'bookly_wp_user_login',
                    form_id: params.form_id,
                    log: $login_modal.find('[name="log"]').val(),
                    pwd: $login_modal.find('[name="pwd"]').val(),
                    rememberme: $login_modal.find('[name="rememberme"]').prop('checked') ? 1 : 0
                }
            }).then(response => {
                BooklyL10n.csrf_token = response.data.csrf_token;
                $guest_info.fadeOut('slow');
                populateForm(response);
                $login_modal.removeClass('bookly-in');
            }).catch(response => {
                if (response.error == 'incorrect_username_password') {
                    $login_modal.find('input').addClass('bookly-error');
                    $login_modal.find('.bookly-label-error').html(opt[params.form_id].errors[response.error]);
                }
            }).finally(() => { ladda.stop(); })
        });
        // Customer duplicate modal.
        $('button:submit', $cst_modal).on('click', function (e) {
            e.preventDefault();
            $cst_modal.removeClass('bookly-in');
            $next_btn.trigger('click', [1]);
        });
        // Verification code modal.
        $('button:submit', $verification_modal).on('click', function (e) {
            e.preventDefault();
            $verification_modal.removeClass('bookly-in');
            $next_btn.trigger('click');
        });
        // Facebook login button.
        if (opt[params.form_id].hasOwnProperty('facebook') && opt[params.form_id].facebook.enabled && typeof FB !== 'undefined') {
            FB.XFBML.parse($('.bookly-js-fb-login-button', $container).parent().get(0));
            opt[params.form_id].facebook.onStatusChange = function (response) {
                if (response.status === 'connected') {
                    opt[params.form_id].facebook.enabled = false;
                    opt[params.form_id].facebook.onStatusChange = undefined;
                    $guest_info.fadeOut('slow', function () {
                        // Hide buttons in all Bookly forms on the page.
                        $('.bookly-js-fb-login-button').hide();
                    });
                    FB.api('/me', {fields: 'id,name,first_name,last_name,email'}, function (userInfo) {
                        booklyAjax({
                            type: 'POST',
                            data: $.extend(userInfo, {
                                action: 'bookly_pro_facebook_login',
                                form_id: params.form_id
                            })
                        }).then(response => {
                            populateForm(response);
                        });
                    });
                }
            };
        }

        $next_btn.on('click', function (e, force_update_customer) {
            e.stopPropagation();
            e.preventDefault();

            // Terms and conditions checkbox
            let $terms = $('.bookly-js-terms', $container),
                $terms_error = $('.bookly-js-terms-error', $container);

            $terms_error.html('');
            if ($terms.length && !$terms.prop('checked')) {
                $terms_error.html(terms_error);
            } else {

                var info_fields = [],
                    custom_fields = {},
                    checkbox_values,
                    captcha_ids = [],
                    ladda = laddaStart(this)
                ;

                // Execute custom JavaScript
                if (customJS) {
                    try {
                        $.globalEval(customJS.next_button);
                    } catch (e) {
                        // Do nothing
                    }
                }

                // Customer information fields.
                $('div.bookly-js-info-field-row', $container).each(function () {
                    var $this = $(this);
                    switch ($this.data('type')) {
                        case 'text-field':
                            info_fields.push({
                                id: $this.data('id'),
                                value: $this.find('input.bookly-js-info-field').val()
                            });
                            break;
                        case 'textarea':
                            info_fields.push({
                                id: $this.data('id'),
                                value: $this.find('textarea.bookly-js-info-field').val()
                            });
                            break;
                        case 'checkboxes':
                            checkbox_values = [];
                            $this.find('input.bookly-js-info-field:checked').each(function () {
                                checkbox_values.push(this.value);
                            });
                            info_fields.push({
                                id: $this.data('id'),
                                value: checkbox_values
                            });
                            break;
                        case 'radio-buttons':
                            info_fields.push({
                                id: $this.data('id'),
                                value: $this.find('input.bookly-js-info-field:checked').val() || null
                            });
                            break;
                        case 'drop-down':
                            info_fields.push({
                                id: $this.data('id'),
                                value: $this.find('select.bookly-js-info-field').val()
                            });
                            break;
                    }
                });
                // Custom fields.
                $('.bookly-custom-fields-container', $container).each(function () {
                    let $cf_container = $(this),
                        key = $cf_container.data('key'),
                        custom_fields_data = [];
                    $('div.bookly-custom-field-row', $cf_container).each(function () {
                        var $this = $(this);
                        if ($this.css('display') !== 'none') {
                            switch ($this.data('type')) {
                                case 'text-field':
                                case 'file':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('input.bookly-custom-field').val()
                                    });
                                    break;
                                case 'textarea':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('textarea.bookly-custom-field').val()
                                    });
                                    break;
                                case 'checkboxes':
                                    checkbox_values = [];
                                    $this.find('input.bookly-custom-field:checked').each(function () {
                                        checkbox_values.push(this.value);
                                    });
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: checkbox_values
                                    });
                                    break;
                                case 'radio-buttons':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('input.bookly-custom-field:checked').val() || null
                                    });
                                    break;
                                case 'drop-down':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('select.bookly-custom-field').val()
                                    });
                                    break;
                                case 'number':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('input.bookly-custom-field').val()
                                    });
                                    break;
                                case 'time':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('select.bookly-custom-field').val()
                                    });
                                    break;
                                case 'date':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('input.bookly-custom-field').pickadate('picker').get('select', 'yyyy-mm-dd')
                                    });
                                    break;
                                case 'captcha':
                                    custom_fields_data.push({
                                        id: $this.data('id'),
                                        value: $this.find('input.bookly-custom-field').val()
                                    });
                                    captcha_ids.push($this.data('id'));
                                    break;
                            }
                        }
                    });
                    custom_fields[key] = {custom_fields: JSON.stringify(custom_fields_data)};
                });

                try {
                    phone_number = intlTelInput.enabled ? $phone_field.intlTelInput('getNumber') : $phone_field.val();
                    if (phone_number == '') {
                        phone_number = $phone_field.val();
                    }
                } catch (error) {  // In case when intlTelInput can't return phone number.
                    phone_number = $phone_field.val();
                }
                var data = {
                    action: 'bookly_session_save',
                    form_id: params.form_id,
                    full_name: $full_name_field.val(),
                    first_name: $first_name_field.val(),
                    last_name: $last_name_field.val(),
                    phone: phone_number,
                    email: $email_field.val().trim(),
                    email_confirm: $email_confirm_field.length === 1 ? $email_confirm_field.val().trim() : undefined,
                    birthday: {
                        day: $birthday_day_field.val(),
                        month: $birthday_month_field.val(),
                        year: $birthday_year_field.val()
                    },
                    country: $address_country_field.val(),
                    state: $address_state_field.val(),
                    postcode: $address_postcode_field.val(),
                    city: $address_city_field.val(),
                    street: $address_street_field.val(),
                    street_number: $address_street_number_field.val(),
                    additional_address: $address_additional_field.val(),
                    address_iso: {
                        country: $address_country_field.data('short'),
                        state: $address_state_field.data('short'),
                    },
                    info_fields: info_fields,
                    notes: $notes_field.val(),
                    cart: custom_fields,
                    captcha_ids: JSON.stringify(captcha_ids),
                    force_update_customer: !update_details_dialog || force_update_customer,
                    verification_code: $verification_code.val()
                };
                // Error messages
                $errors.empty();
                $fields.removeClass('bookly-error');
                booklyAjax({
                    type: 'POST',
                    data: data
                }).then(response => {
                    if (woocommerce.enabled) {
                        var data = {
                            action: 'bookly_pro_add_to_woocommerce_cart',
                            form_id: params.form_id
                        };
                        booklyAjax({
                            type: 'POST',
                            data: data
                        }).then(response => {
                            window.location.href = woocommerce.cart_url;
                        }).catch(response => {
                            ladda.stop();
                            stepTime({form_id: params.form_id}, opt[params.form_id].errors[response.error]);
                        });
                    } else {
                        stepPayment({form_id: params.form_id});
                    }
                }).catch(response => {
                    var $scroll_to = null;
                    if (response.appointments_limit_reached) {
                        stepComplete({form_id: params.form_id, error: 'appointments_limit_reached'});
                    } else if (response.hasOwnProperty('verify')) {
                        ladda.stop();
                        $verification_modal
                            .find('#bookly-verification-code-text').html(response.verify_text).end()
                            .addClass('bookly-in');
                    } else if (response.group_skip_payment) {
                        booklyAjax({
                            type: 'POST',
                            data: {
                                action: 'bookly_save_appointment',
                                form_id: params.form_id
                            }
                        }).then(response => {
                            stepComplete({form_id: params.form_id, error: 'group_skip_payment'});
                        });
                    } else {
                        ladda.stop();

                        var invalidClass = 'bookly-error',
                            validateFields = [
                                {
                                    name: 'full_name',
                                    errorElement: $name_error,
                                    formElement: $full_name_field
                                },
                                {
                                    name: 'first_name',
                                    errorElement: $first_name_error,
                                    formElement: $first_name_field
                                },
                                {
                                    name: 'last_name',
                                    errorElement: $last_name_error,
                                    formElement: $last_name_field
                                },
                                {
                                    name: 'phone',
                                    errorElement: $phone_error,
                                    formElement: $phone_field
                                },
                                {
                                    name: 'email',
                                    errorElement: $email_error,
                                    formElement: $email_field
                                },
                                {
                                    name: 'email_confirm',
                                    errorElement: $email_confirm_error,
                                    formElement: $email_confirm_field
                                },
                                {
                                    name: 'birthday_day',
                                    errorElement: $birthday_day_error,
                                    formElement: $birthday_day_field
                                },
                                {
                                    name: 'birthday_month',
                                    errorElement: $birthday_month_error,
                                    formElement: $birthday_month_field
                                },
                                {
                                    name: 'birthday_year',
                                    errorElement: $birthday_year_error,
                                    formElement: $birthday_year_field
                                },
                                {
                                    name: 'country',
                                    errorElement: $address_country_error,
                                    formElement: $address_country_field
                                },
                                {
                                    name: 'state',
                                    errorElement: $address_state_error,
                                    formElement: $address_state_field
                                },
                                {
                                    name: 'postcode',
                                    errorElement: $address_postcode_error,
                                    formElement: $address_postcode_field
                                },
                                {
                                    name: 'city',
                                    errorElement: $address_city_error,
                                    formElement: $address_city_field
                                },
                                {
                                    name: 'street',
                                    errorElement: $address_street_error,
                                    formElement: $address_street_field
                                },
                                {
                                    name: 'street_number',
                                    errorElement: $address_street_number_error,
                                    formElement: $address_street_number_field
                                },
                                {
                                    name: 'additional_address',
                                    errorElement: $address_additional_error,
                                    formElement: $address_additional_field
                                }
                            ];

                        validateFields.forEach(function (field) {
                            if (!response[field.name]) {
                                return;
                            }

                            field.errorElement.html(response[field.name]);
                            field.formElement.addClass(invalidClass);

                            if ($scroll_to === null) {
                                $scroll_to = field.formElement;
                            }
                        });

                        if (response.info_fields) {
                            $.each(response.info_fields, function (field_id, message) {
                                var $div = $('div.bookly-js-info-field-row[data-id="' + field_id + '"]', $container);
                                $div.find('.bookly-js-info-field-error').html(message);
                                $div.find('.bookly-js-info-field').addClass('bookly-error');
                                if ($scroll_to === null) {
                                    $scroll_to = $div.find('.bookly-js-info-field');
                                }
                            });
                        }
                        if (response.custom_fields) {
                            $.each(response.custom_fields, function (key, fields) {
                                $.each(fields, function (field_id, message) {
                                    var $custom_fields_collector = $('.bookly-custom-fields-container[data-key="' + key + '"]', $container);
                                    var $div = $('[data-id="' + field_id + '"]', $custom_fields_collector);
                                    $div.find('.bookly-custom-field-error').html(message);
                                    $div.find('.bookly-custom-field').addClass('bookly-error');
                                    if ($scroll_to === null) {
                                        $scroll_to = $div.find('.bookly-custom-field');
                                    }
                                });
                            });
                        }
                        if (response.customer) {
                            $cst_modal
                                .find('.bookly-js-modal-body').html(response.customer).end()
                                .addClass('bookly-in')
                            ;
                        }
                    }
                    if ($scroll_to !== null) {
                        scrollTo($scroll_to, params.form_id);
                    }
                });
            }
        });

        $('.bookly-js-back-step', $container).on('click', function (e) {
            e.stopPropagation();
            e.preventDefault();
            laddaStart(this);
            if (!opt[params.form_id].skip_steps.cart) {
                stepCart({form_id: params.form_id});
            } else if (opt[params.form_id].no_time || opt[params.form_id].skip_steps.time) {
                if (opt[params.form_id].no_extras || opt[params.form_id].skip_steps.extras) {
                    stepService({form_id: params.form_id});
                } else {
                    stepExtras({form_id: params.form_id});
                }
            } else if (!opt[params.form_id].skip_steps.repeat && opt[params.form_id].recurrence_enabled) {
                stepRepeat({form_id: params.form_id});
            } else if (!opt[params.form_id].skip_steps.extras && opt[params.form_id].step_extras == 'after_step_time' && !opt[params.form_id].no_extras) {
                stepExtras({form_id: params.form_id});
            } else {
                stepTime({form_id: params.form_id});
            }
        });

        $('.bookly-js-captcha-refresh', $container).on('click', function () {
            $captcha.css('opacity', '0.5');
            booklyAjax({
                type: 'POST',
                data: {
                    action: 'bookly_custom_fields_captcha_refresh',
                    form_id: params.form_id,
                }
            }).then(response => {
                $captcha.attr('src', response.data.captcha_url).on('load', function () {
                    $captcha.css('opacity', '1');
                });
            });
        });
    });

    /**
     * global function to init google places
     */
    function booklyInitGooglePlacesAutocomplete(bookly_forms) {
        bookly_forms = bookly_forms || $('.bookly-form .bookly-details-step');

        bookly_forms.each(function () {
            initGooglePlacesAutocomplete($(this));
        });
    }

    /**
     * Addon: Google Maps Address
     * @param {jQuery} [$container]
     * @returns {boolean}
     */
    function initGooglePlacesAutocomplete($container) {
        var autocompleteInput = $container.find('.bookly-js-cst-address-autocomplete');

        if (!autocompleteInput.length) {
            return false;
        }

        var autocomplete = new google.maps.places.Autocomplete(
                autocompleteInput[0], {
                    types: ['geocode']
                }
            ),
            autocompleteFields = [
                {
                    selector: '.bookly-js-address-country',
                    val: function () {
                        return getFieldValueByType('country');
                    },
                    short: function () {
                        return getFieldValueByType('country', true);
                    }
                },
                {
                    selector: '.bookly-js-address-postcode',
                    val: function () {
                        return getFieldValueByType('postal_code');
                    }
                },
                {
                    selector: '.bookly-js-address-city',
                    val: function () {
                        return getFieldValueByType('locality') || getFieldValueByType('administrative_area_level_3') || getFieldValueByType('postal_town');
                    }
                },
                {
                    selector: '.bookly-js-address-state',
                    val: function () {
                        return getFieldValueByType('administrative_area_level_1');
                    },
                    short: function () {
                        return getFieldValueByType('administrative_area_level_1', true);
                    }
                },
                {
                    selector: '.bookly-js-address-street',
                    val: function () {
                        return getFieldValueByType('route');
                    }
                },
                {
                    selector: '.bookly-js-address-street_number',
                    val: function () {
                        return getFieldValueByType('street_number');
                    }
                },
                {
                    selector: '.bookly-js-address-additional_address',
                    val: function () {
                        return getFieldValueByType('subpremise') || getFieldValueByType('neighborhood') || getFieldValueByType('sublocality');
                    }
                }
            ];

        var getFieldValueByType = function (type, useShortName) {
            var addressComponents = autocomplete.getPlace().address_components;

            for (var i = 0; i < addressComponents.length; i++) {
                var addressType = addressComponents[i].types[0];

                if (addressType === type) {
                    return useShortName ? addressComponents[i]['short_name'] : addressComponents[i]['long_name'];
                }
            }

            return '';
        };

        autocomplete.addListener('place_changed', function () {
            autocompleteFields.forEach(function (field) {
                var element = $container.find(field.selector);

                if (element.length === 0) {
                    return;
                }
                element.val(field.val());
                if (typeof field.short == 'function') {
                    element.data('short', field.short());
                }
            });
        });
    }
}