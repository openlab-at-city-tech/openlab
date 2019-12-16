jQuery(document).ready(function ($) {
    // Add link to form url
    var loginForm = $('.advgb-form-login');
    var registerForm = $('.advgb-form-register');
    loginForm.attr('action', advgbLoresForm.login_url);
    registerForm.attr('action', advgbLoresForm.register_url);
    $('.advgb-lost-password .advgb-lost-password-link').attr('href', advgbLoresForm.lostpwd_url);

    // Add value to redirect input
    var redirectInput = $('.advgb-lores-form .redirect_to');
    var redirectType = redirectInput.data('redirect');
    if (redirectType === 'home') {
        redirectInput.val(advgbLoresForm.home_url);
    } else if (redirectType === 'dashboard') {
        redirectInput.val(advgbLoresForm.admin_url);
    }

    // Show notice on failed login
    var url = new URL(window.location.href);
    var isLoginFailed = url.searchParams.get("login") === 'failed';
    if (isLoginFailed) {
        var failedNotice = $('<div class="advgb-login-failed-notice">'+advgbLoresForm.login_failed_notice+'</div>');
        loginForm.find('.advgb-login-form').prepend(failedNotice);
    }

    // Add class when focus to input
    $('.advgb-lores-field .advgb-lores-field-input .advgb-lores-input').focus(function () {
        $(this).closest('.advgb-lores-field-input').addClass('focused');
    }).blur(function () {
        $(this).closest('.advgb-lores-field-input').removeClass('focused');
    });

    // Animate when switch between login and register form
    $('.advgb-lores-form .advgb-register-link').click(function (e) {
        e.preventDefault();
        var wrapperForm = $(this).closest('.advgb-lores-form-wrapper');
        var loginForm = wrapperForm.find('.advgb-login-form-wrapper');
        var registerForm = wrapperForm.find('.advgb-register-form-wrapper');

        loginForm.hide(0);
        registerForm.show("slide", { direction: "right" }, 300);
    });

    $('.advgb-lores-form .advgb-back-to-login-link').click(function (e) {
        e.preventDefault();
        var wrapperForm = $(this).closest('.advgb-lores-form-wrapper');
        var loginForm = wrapperForm.find('.advgb-login-form-wrapper');
        var registerForm = wrapperForm.find('.advgb-register-form-wrapper');

        registerForm.hide(0);
        loginForm.show("slide", { direction: "left" }, 300);
    });

    // Hide register form if register is disabled
    var registerEnabled = parseInt(advgbLoresForm.register_enabled);
    if (!registerEnabled) {
        $('.advgb-header-navigation').remove();
        registerForm.hide();
        registerForm.prepend('<p style="color: red">'+advgbLoresForm.unregistrable_notice+'</p>')
    }

    // Check captcha is enable before submitting
    $('.advgb-lores-form form').submit(function (e) {
        if (typeof grecaptcha !== "undefined") {
            var $thisForm = $(this).closest('.advgb-lores-form');
            var g_id = parseInt($thisForm.find('.advgb-grecaptcha').data('gid'));
            var captcha = grecaptcha.getResponse(g_id) || undefined;
            var validated = false;

            if (!captcha) {
                alert(advgbLoresForm.captcha_empty_warning);
                return false;
            }

            $.ajax( {
                url: advgbLoresForm.ajax_url,
                type: "POST",
                async: false,
                data: {
                    action: 'advgb_lores_validate',
                    captcha: captcha
                },
                beforeSend: function () {
                    $thisForm.addClass('sending');
                    $thisForm.append('<div class="advgb-form-sending" />');
                },
                success: function () {
                    validated = true;
                },
                error: function ( jqxhr, textStatus, error ) {
                    alert(textStatus + " : " + error + ' - ' + jqxhr.responseJSON);
                    $thisForm.removeClass('sending');
                    $thisForm.find('.advgb-form-sending').remove();
                }
            } );

            return validated;
        }
    });
});