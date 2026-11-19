jQuery(document).ready(function ($) {
    // Make ids and label "for" attributes unique when more than one login block is on the page
    $('.advgb-lores-form-wrapper').each(function (index) {
        if (index === 0) {
            return;
        }
        var $wrapper = $(this);
        ['advgb-login-user', 'advgb-login-password', 'rememberme', 'advgb-register-username', 'advgb-register-email'].forEach(function (id) {
            var newId = id + '-' + (index + 1);
            $wrapper.find('[id="' + id + '"]').attr('id', newId);
            $wrapper.find('label[for="' + id + '"]').attr('for', newId);
        });
    });

    // Add link to form url
    var loginForm = $('.advgb-form-login');
    var registerForm = $('.advgb-form-register');
    loginForm.attr('action', advgbLoresForm.login_url);
    registerForm.attr('action', advgbLoresForm.register_url);
    $('.advgb-lost-password > a').attr('href', advgbLoresForm.lostpwd_url);

    // Make user, email and password fields required
    loginForm.find('input[name="log"]').attr({ required: true, autocomplete: 'username' });
    loginForm.find('input[name="pwd"]').attr({ required: true, autocomplete: 'current-password' });
    registerForm.find('input[name="user_login"]').attr({ required: true, autocomplete: 'username' });
    registerForm.find('input[name="user_email"]').attr({ required: true, autocomplete: 'email' });

    // Decorative input icons
    $('.advgb-lores-form .advgb-lores-input-icon svg').attr({ 'aria-hidden': 'true', focusable: 'false' });

    // Add value to redirect input
    var redirectInput = $('.advgb-lores-form .redirect_to');
    var redirectType = redirectInput.data('redirect');
    if (redirectType === 'home') {
        redirectInput.val(advgbLoresForm.home_url);
    } else if (redirectType === 'dashboard') {
        redirectInput.val(advgbLoresForm.admin_url);
    } else if (redirectType === 'currentpage') {
        redirectInput.val(window.location.href);
    }

    // Show notice on failed login
    var url = new URL(window.location.href);
    var isLoginFailed = url.searchParams.get("login") === 'failed';
    if (isLoginFailed) {
        var failedNotice = $('<div class="advgb-login-failed-notice" role="alert" tabindex="-1"></div>').text(advgbLoresForm.login_failed_notice);
        loginForm.find('.advgb-login-form').prepend(failedNotice);
        loginForm.find('input[name="log"], input[name="pwd"]').attr('aria-invalid', 'true');
        loginForm.find('.advgb-login-failed-notice').first().trigger('focus');
    }

    // Add class when focus to input
    $('.advgb-lores-field .advgb-lores-field-input .advgb-lores-input').focus(function () {
        $(this).closest('.advgb-lores-field-input').addClass('focused');
    }).blur(function () {
        $(this).closest('.advgb-lores-field-input').removeClass('focused');
    });

    // Move focus to the heading (or first field) of the newly shown form
    function focusShownForm($form) {
        var $heading = $form.find('.advgb-lores-form-welcome').first();
        if ($heading.length && $heading.text().trim() !== '') {
            $heading.attr('tabindex', '-1').trigger('focus');
        } else {
            $form.find('.advgb-lores-input').first().trigger('focus');
        }
    }

    // Keyboard support for the switch controls
    $('.advgb-lores-form .advgb-register-link').attr('role', 'button');
    $('.advgb-lores-form .advgb-back-to-login-link').attr({ role: 'button', tabindex: '0' })
        .on('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ' || e.key === 'Spacebar') {
                e.preventDefault();
                $(this).trigger('click');
            }
        });
    $('.advgb-lores-form .advgb-register-link').on('keydown', function (e) {
        if (e.key === ' ' || e.key === 'Spacebar') {
            e.preventDefault();
            $(this).trigger('click');
        }
    });

    // Animate when switch between login and register form
    $('.advgb-lores-form .advgb-register-link').click(function (e) {
        e.preventDefault();
        var wrapperForm = $(this).closest('.advgb-lores-form-wrapper');
        var loginForm = wrapperForm.find('.advgb-login-form-wrapper');
        var registerForm = wrapperForm.find('.advgb-register-form-wrapper');

        loginForm.hide(0);
        registerForm.show("slide", { direction: "right" }, 300, function () {
            focusShownForm(registerForm);
        });
    });

    $('.advgb-lores-form .advgb-back-to-login-link').click(function (e) {
        e.preventDefault();
        var wrapperForm = $(this).closest('.advgb-lores-form-wrapper');
        var loginForm = wrapperForm.find('.advgb-login-form-wrapper');
        var registerForm = wrapperForm.find('.advgb-register-form-wrapper');

        registerForm.hide(0);
        loginForm.show("slide", { direction: "left" }, 300, function () {
            focusShownForm(loginForm);
        });
    });

    // Hide register form if register is disabled
    var registerEnabled = parseInt(advgbLoresForm.register_enabled);
    if (!registerEnabled) {
        $('.advgb-header-navigation').remove();
        registerForm.hide();
        registerForm.prepend($('<p style="color: #b30000"></p>').text(advgbLoresForm.unregistrable_notice));
    }

    // Check captcha is enable before submitting
    $('.advgb-lores-form form').submit(function (e) {
        if (typeof grecaptcha !== "undefined") {
            var $thisForm = $(this).closest('.advgb-lores-form');
            var g_id = parseInt($thisForm.find('.advgb-grecaptcha').data('gid'));
            var captcha = grecaptcha.getResponse(g_id) || undefined;
            var nonce_val = $(this).find('[name="advgb_blockform_nonce_field"]').val();
            var validated = false;

            $thisForm.find('.advgb-lores-form-error').remove();

            if (!captcha) {
                showFormError($thisForm, advgbLoresForm.captcha_empty_warning);
                return false;
            }

            $.ajax( {
                url: advgbLoresForm.ajax_url,
                type: "POST",
                async: false,
                data: {
                    action: 'advgb_lores_validate',
                    captcha: captcha,
                    nonce: nonce_val
                },
                beforeSend: function () {
                    $thisForm.addClass('sending');
                    $thisForm.append('<div class="advgb-form-sending" />');
                },
                success: function () {
                    validated = true;
                },
                error: function ( jqxhr ) {
                    var message = jqxhr && typeof jqxhr.responseJSON === 'string' ? jqxhr.responseJSON.replace(/<[^>]*>/g, '').trim() : '';
                    showFormError($thisForm, message || advgbLoresForm.server_error);
                    $thisForm.removeClass('sending');
                    $thisForm.find('.advgb-form-sending').remove();
                }
            } );

            return validated;
        }
    });

    // Inline error message, announced by screen readers
    function showFormError($thisForm, message) {
        var $notice = $('<div class="advgb-login-failed-notice advgb-lores-form-error" role="alert"></div>').text(message);
        $thisForm.find('.advgb-grecaptcha').first().before($notice);
    }
});
