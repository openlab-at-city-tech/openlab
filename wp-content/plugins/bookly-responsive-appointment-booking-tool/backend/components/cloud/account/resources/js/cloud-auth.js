jQuery(function ($) {
    'use strict';
    let $modal                = $('#bookly-cloud-auth-modal'),
        $loginBtn             = $('#bookly-cloud-login-button'),
        $registerBtn          = $('#bookly-cloud-register-button'),
        $loginForm            = $('#bookly-form-login', $modal),
        $registerForm         = $('#bookly-form-register', $modal),
        $forgotForm           = $('#bookly-form-forgot', $modal),
        $recoveryCodeForm     = $('#bookly-form-recovery-code', $modal),
        $recoveryPasswordForm = $('#bookly-form-recovery-password', $modal),
        $country              = $('#bookly-r-country', $modal)
    ;

    $country.booklySelectCountry({
        dropdownParent: $modal,
        language      : {
            noResults: function () {
                return BooklyCloudAuthL10n.noResults;
            }
        }
    });
    $.get('https://ipinfo.io', function () {}, 'jsonp').always(function (resp) {
        const countryCode = (resp && resp.country) ? resp.country : '';
        $country.val(countryCode.toLowerCase()).trigger('change');
    });

    // Login button from panel
    $loginBtn.on('click', function () {
        $modal.booklyModal('show');
        showForm('login');
    });

    // Register button from panel
    $registerBtn.on('click', function () {
        $modal.booklyModal('show');
        showForm('register');
    });

    $modal.on('click', '.bookly-js-modal-form-switch', function (e) {
        e.preventDefault();
        showForm($(this).data('target'));
    });

    $('input[type=text], input[type=password]', $modal).on('keydown', function (e) {
        if (e.which == 13) {
            $modal.find('.modal-footer button.btn-success:visible').click();
        }
    });

    // Register.
    $modal.find('button[name="form-register"]').on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            method  : 'POST',
            url     : ajaxurl,
            data    : {
                action         : 'bookly_cloud_register',
                username       : $registerForm.find('input[name="username"]').val(),
                password       : $registerForm.find('input[name="password"]').val(),
                password_repeat: $registerForm.find('input[name="password_repeat"]').val(),
                country        : $registerForm.find('select[name="country"]').val(),
                accept_tos     : $registerForm.find('input[name="accept_tos"]').prop('checked') ? 1 : 0,
                csrf_token     : BooklyCloudAuthL10n.csrfToken,
            },
            dataType: 'json',
            success : function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    if (response.data && response.data.message) {
                        booklyAlert({error: [response.data.message]});
                    }
                    ladda.stop();
                }
            }
        });
    });

    // Login.
    $modal.find('button[name="form-login"]').on('click', function (e) {
        e.preventDefault();
        let ladda = Ladda.create(this);
        ladda.start();
        $.ajax({
            method  : 'POST',
            url     : ajaxurl,
            data    : {
                action    : 'bookly_cloud_login',
                username  : $loginForm.find('input[name="username"]').val(),
                password  : $loginForm.find('input[name="password"]').val(),
                csrf_token: BooklyCloudAuthL10n.csrfToken,
            },
            dataType: 'json',
            success : function (response) {
                if (response.success) {
                    window.location.reload();
                } else {
                    if (response.data && response.data.message) {
                        booklyAlert({error: [response.data.message]});
                    }
                    ladda.stop();
                }
            }
        });
    });

    // Forgot password.
    $modal.find('button[name="form-forgot"]').on('click', function (e) {
        e.preventDefault();
        const $button = $(this);
        let ladda = Ladda.create(this);
        ladda.start();
        const password = $recoveryPasswordForm.find('input[name="password"]').val();
        const step = $button.data('step');
        if (step === 2 && password !== $recoveryPasswordForm.find('input[name="password_repeat"]').val()) {
            booklyAlert({error: [BooklyCloudAuthL10n.passwords_not_match]});
            ladda.stop();
        } else {
            $.ajax({
                method  : 'POST',
                url     : ajaxurl,
                data    : {
                    action    : 'bookly_forgot_password',
                    username  : $forgotForm.find('input[name="username"]').val(),
                    code      : $recoveryCodeForm.find('input[name="code"]').val(),
                    password  : password,
                    step      : step,
                    csrf_token: BooklyCloudAuthL10n.csrfToken,
                },
                dataType: 'json',
                success : function (response) {
                    if (response.success) {
                        showForm($button.data('next'));
                    } else {
                        if (response.data && response.data.message) {
                            booklyAlert({error: [response.data.message]});
                        }
                        ladda.stop();
                    }
                }
            });
        }
    });

    $(document.body).on('bookly.cloud.auth.form', {},
        function (event, form) {
            showForm(form);
        }
    );

    // Login required.
    if ($('div#bookly-login-required').length) {
        $modal.booklyModal('show');
        showForm('login');
    }

    function showForm(target) {
        $modal.find('.bookly-js-modal-title').hide();
        $modal.find('.bookly-js-modal-form').hide();
        $modal.find('.bookly-js-modal-buttons').hide();
        $modal.find('.bookly-js-title-' + target).show();
        $modal.find('#bookly-form-' + target).show();
        $modal.find('.bookly-js-buttons-' + target).show();
    }
});