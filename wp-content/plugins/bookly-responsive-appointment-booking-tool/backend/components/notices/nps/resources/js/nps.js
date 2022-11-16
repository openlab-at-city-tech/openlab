jQuery(function ($) {
    let $alert  = $('#bookly-nps-notice'),
        $quiz   = $('#bookly-nps-quiz'),
        $stars  = $('#bookly-nps-stars'),
        $msg    = $('#bookly-nps-msg'),
        $email  = $('#bookly-nps-email'),
        $form   = $('#bookly-nps-form'),
        $rate   = $('#bookly-js-rate-bookly'),
        rating  = 0;

    // Init stars.
    $quiz.on('mouseenter', '.bookly-js-star', function () {
        rating = $(this).index();
        $('#bookly-nps-quiz i.bookly-js-star').each(function () {
            if ($(this).index() <= rating) {
                $(this).removeClass('text-muted far').addClass('text-warning fas');
            } else {
                $(this).removeClass('text-warning fas').addClass('text-muted far');
            }
        });
        rating += 1;
    }).on('click', '.bookly-js-star', function () {
        if (rating <= 6) {
            $form.show();
        } else {
            $.post(ajaxurl, {action: 'bookly_nps_send', csrf_token: BooklyL10nGlobal.csrf_token, rate: rating});
            $alert.remove();
            let $title = $('.bookly-js-alert-title', $rate),
                text = $title.html();
            $title.html(text.replace('{star}', rating));
            $rate.show();
        }
    });

    $('#bookly-nps-btn').on('click', function () {
        $alert.find('.form-group .form-control').removeClass('is-invalid');
        if ($msg.val() == '') {
            $msg.addClass('is-invalid');
        } else {
            let ladda = Ladda.create(this);
            ladda.start();
            $.post(
                ajaxurl,
                {
                    action: 'bookly_nps_send',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    rate: $stars.val(),
                    msg: $msg.val(),
                    email: $email.val()
                },
                function (response) {
                    ladda.stop();
                    if (response.success) {
                        $alert.alert('close');
                        booklyAlert({success : [response.data.message]});
                    }
                }
            );
        }
    });

    $alert.on('close.bs.alert', function () {
        $.post(ajaxurl, {action: 'bookly_dismiss_nps_notice', csrf_token : BooklyL10nGlobal.csrf_token}, function () {
            // Indicator for Selenium that request has completed.
            $('.bookly-js-nps-notice').remove();
        });
    });
});