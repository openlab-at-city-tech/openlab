jQuery(function ($) {
    let $notice = $('#bookly-sms-promotion-notice'),
        type = $notice.data('type'),
        id = $notice.data('id');
    $notice.on('close.bs.alert', function () {
        $.post(ajaxurl, {
            action: 'bookly_dismiss_sms_promotion_notice',
            id: id,
            dismiss: $notice.data('dismiss'),
            csrf_token: BooklyL10nGlobal.csrf_token
        });
    });
    $notice.find('.bookly-js-remind-me-later').on('click', function () {
        $notice.data('dismiss', 'remind').alert('close');
    });
    $notice.find('.bookly-js-apply-action').on('click', function () {
        switch (type) {
            case 'registration':
                $(document.body).trigger('bookly.cloud.auth.form', ['register']);
                $('#bookly-cloud-auth-modal').booklyModal('show');
                break;
            default:
                let $modal = $('#bookly-js-recharge-modal');
                $modal.booklyModal();
                $('.bookly-js-back', $modal).trigger('click');
                break;
        }
        $notice.alert('close');
    });
});