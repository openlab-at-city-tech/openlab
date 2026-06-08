jQuery(function ($) {
    const L10N = window.SliNoticesL10n ? window.SliNoticesL10n : {};

    $('.sli-notice .notice-dismiss').on('click', function (e) {
        const notice = $(this).parent().attr('data-notice');
        const nonce = L10N.nonce;
        const action = L10N.action;

        $.ajax({
            url: L10N.ajaxUrl,
            method: 'POST',
            data: {action, nonce, notice}
        });

        e.preventDefault();
        e.stopPropagation();
    });
});
