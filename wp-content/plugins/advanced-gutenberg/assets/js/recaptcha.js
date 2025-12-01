function advgbRecaptchaInit() {
    var advgbForms = document.querySelectorAll('.advgb-newsletter form .advgb-grecaptcha, .advgb-contact-form form .advgb-grecaptcha, .advgb-lores-form .advgb-grecaptcha');

    advgbForms.forEach(function (form) {
        var g_id = grecaptcha.render( form, {
            sitekey: advgbGRC.site_key,
            theme: advgbGRC.theme !== 'invisible' ? advgbGRC.theme : undefined,
            size: advgbGRC.theme === 'invisible' ? 'invisible' : undefined
        } );

        form.setAttribute('data-gid', g_id);
    });

    if (advgbGRC.theme === 'invisible') {
        grecaptcha.execute();
    }
}