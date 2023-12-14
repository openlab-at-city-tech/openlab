jQuery(function ($) {
    let $modal = $('#bookly-contact-us-modal'),
        $btnContactUs = $('#bookly-contact-us-btn'),
        $btnFeedback = $('#bookly-feedback-btn'),
        $notices = $('#bookly-alert-notices'),
        popoverConfig = {
            html: true,
            sanitize: false,
            trigger: 'manual',
            container: '#bookly-tbs',
            template: '<div class="bookly-popover" role="tooltip"><div class="arrow"></div><div class="popover-body"></div></div>'
        }
    ;

    if ($btnContactUs.data('content')) {
        $btnContactUs
            .on('click', function () {
                $btnContactUs.booklyPopover('hide');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {action : 'bookly_contact_us_btn_clicked', csrf_token: BooklyL10nGlobal.csrf_token }
                });
            })
            .booklyPopover($.extend({
                placement: function (tip) {
                    $(tip)
                        .css({maxWidth:'none'})
                        .find('.popover-body button').on('click', function () {
                            $btnContactUs.booklyPopover('hide');
                            $.ajax({
                                url: ajaxurl,
                                type: 'POST',
                                data: {
                                    action: 'bookly_dismiss_contact_us_notice',
                                    csrf_token: BooklyL10nGlobal.csrf_token
                                },
                                success: function (response) {
                                    $btnContactUs.attr('data-processed', true);
                                }
                            });
                        });

                    return 'bottom';
                }
            }, popoverConfig))
            .booklyPopover('show')
        ;
    }

    if ($btnFeedback.data('content')) {
        $btnFeedback
            .on('click', function () {
                $btnFeedback.booklyPopover('hide');
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {action: 'bookly_dismiss_feedback_notice', csrf_token: BooklyL10nGlobal.csrf_token}
                });
            })
            .booklyPopover($.extend({
                placement: function (tip) {
                    $(tip)
                        .css({maxWidth:'none'})
                        .find('.popover-body').css({overflow:'hidden'})
                        .find('button').on('click', function () {
                        $btnFeedback.booklyPopover('hide');
                        $.ajax({
                            url: ajaxurl,
                            type: 'POST',
                            data: {action : 'bookly_dismiss_feedback_notice', csrf_token : BooklyL10nGlobal.csrf_token }
                        });
                    });

                    return 'bottom';
                }
            }, popoverConfig))
            .booklyPopover('show')
        ;
    }

    $('#bookly-support-send').on('click', function () {
        var $name = $('#bookly-support-name'),
            $email = $('#bookly-support-email'),
            $msg = $('#bookly-support-msg')
        ;

        // Validation.
        $email.toggleClass('is-invalid', $email.val() == '');
        $msg.toggleClass('is-invalid', $msg.val() == '');

        // Send request.
        if ($modal.find('.is-invalid').length == 0) {
            var ladda = Ladda.create(this);
            ladda.start();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_send_support_request',
                    csrf_token: BooklyL10nGlobal.csrf_token,
                    name: $name.val(),
                    email: $email.val(),
                    msg: $msg.val()
                },
                dataType : 'json',
                success  : function (response) {
                    if (response.success) {
                        $msg.val('');
                        $modal.booklyModal('hide');
                        booklyAlert({success : [response.data.message]});
                    } else {
                        booklyAlert({error : [response.data.message]});
                        if (response.data.invalid_email) {
                            $email.addClass('is-invalid');
                        }
                    }
                },
                complete : function () {
                    ladda.stop();
                }
            });
        }
    });

    $('#bookly-js-mark-read-all-messages')
        .on('click', function (e) {
            e.preventDefault();
            var $link = $(this),
                ladda = Ladda.create($('#bookly-bell').get(0)),
                $dropdown = $link.closest(".dropdown-menu");

            $dropdown.prev().dropdown('toggle');
            ladda.start();
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'bookly_mark_read_all_messages',
                    csrf_token: BooklyL10nGlobal.csrf_token
                },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        $('.bookly-js-new-messages-count').remove();
                        $link.closest('li').remove();
                        $('a', $dropdown).each(function () {
                            $(this).html($(this).text());
                        });
                    }
                },
                complete: function () {
                    ladda.stop();
                }
            });
        });

    $('.bookly-js-proceed-to-demo')
        .on('click', function () {
            var $modal = $('#bookly-demo-site-info-modal'),
                target = $(this).data('target');

            if ($('#bookly-js-dont-show-again-demo', $modal).prop('checked')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_dismiss_demo_site_description',
                        csrf_token: BooklyL10nGlobal.csrf_token
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('[data-action=show-demo]').data('target', target);
                        }
                    }
                });
            }
            $modal.booklyModal('hide');
            window.open(target, '_blank');
        });

    $('.bookly-js-proceed-requests')
        .on('click', function () {
            var $modal = $('#bookly-feature-requests-modal');
            if ($('#bookly-js-dont-show-again-feature', $modal).prop('checked')) {
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'bookly_dismiss_feature_requests_description',
                        csrf_token: BooklyL10nGlobal.csrf_token
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            $('[data-action=feature-request]').data('target', BooklySupportL10n.featuresRequestUrl);
                        }
                    }
                });
            }
            $modal.booklyModal('hide');
            window.open(BooklySupportL10n.featuresRequestUrl, '_blank');
        });

    $notices.booklyPopover({
        html: true,
        placement: function(tip) {
            $(tip).css({maxWidth: '500px'});

            return 'bottom';
        },
        container: '#bookly-tbs',
        template: '<div class="bookly-popover" role="tooltip"><div class="popover-body"></div></div>',
        content: function () {
            let $txt_l1 = $('<span></span>'),
                $txt_l2 = $('<span></span>'),
                $content = $('<div class="mt-2"><br/><hr style="border-top-color: rgba(0,0,0,.1)"/></div>');
            $txt_l1.text(BooklySupportL10n.capabilities);
            $txt_l2.text(BooklySupportL10n.contact_with_admin);

            $content.find('br:first').before($txt_l1);
            $content.find('hr').after($txt_l2);

            return $content.get(0);
        },
        trigger: 'hover'
    });
});