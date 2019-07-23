function advgbRecaptchaInit() {
    var advgbForms = document.querySelectorAll('.advgb-newsletter form .advgb-grecaptcha, .advgb-contact-form form .advgb-grecaptcha');

    advgbForms.forEach(function (form) {
        grecaptcha.render( form, {
            sitekey: advgbGRC.site_key,
            theme: advgbGRC.theme !== 'invisible' ? advgbGRC.theme : undefined,
            size: advgbGRC.theme === 'invisible' ? 'invisible' : undefined
        } );
    });

    if (advgbGRC.theme === 'invisible') {
        grecaptcha.execute();
    }
}