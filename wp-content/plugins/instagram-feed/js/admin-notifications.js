/**
 * SBI Admin Notifications.
 *
 * @since 2.18
 */

'use strict';

var SBIAdminNotifications = window.SBIAdminNotifications || (function (document, window, $) {

    /**
     * Elements holder.
     *
     * @since 2.18
     *
     * @type {object}
     */
    var el = {

        $notifications: $('#sbi-notifications'),
        $nextButton: $('#sbi-notifications .navigation .next'),
        $prevButton: $('#sbi-notifications .navigation .prev'),
        $adminBarCounter: $('#wp-admin-bar-wpforms-menu .sbi-menu-notification-counter'),
        $adminBarMenuItem: $('#wp-admin-bar-sbi-notifications'),
        $notificationsClass: $('.sbi-notifications-wrap'),
    };

    var $messages = el.$notificationsClass.find('.messages'),
        $messagesCount = $messages.length,
        $message = $messages.find('.message'),
        $navigation = el.$notificationsClass.find('.navigation');

    // if there is only one notification, remove the navigation
    if ($messagesCount === 1) {
        $navigation.remove();
    }

    // if only 2 review messages, remove the navigation
    if ($messagesCount === 2) {
        if ($message.hasClass('sbi_review_step1_notice') && $message.hasClass('rn_step_2')) {
            $navigation.remove();
        }
    }

    // if there are multiple notifications, merge them into one div
    el.$notificationsClass.not(':first').hide();
    var $first = $messages.first();
    $first.find('.message').addClass('current');

    $messages.not(':first').each(function () {
        // get the message div and append to the first one
        $first.append($(this).html());
    });

    // remove the rest except the first one
    el.$notificationsClass.not(':first').remove();

    /**
     * Public functions and properties.
     *
     * @since 2.18
     *
     * @type {object}
     */
    var app = {

        /**
         * Start the engine.
         *
         * @since 2.18
         */
        init: function () {
            el.$notifications.find('.messages a').each(function () {
                if ($(this).attr('href').indexOf('dismiss=') > -1) {
                    $(this).addClass('button-dismiss');
                }
            })

            $(app.ready);
        },

        /**
         * Document ready.
         *
         * @since 2.18
         */
        ready: function () {

            app.updateNavigation();
            app.events();
        },

        /**
         * Register JS events.
         *
         * @since 2.18
         */
        events: function () {

            el.$notifications
                .on('click', '.dismiss', app.dismiss)
                .on('click', '.button-dismiss', app.buttonDismiss)
                .on('click', '.next', app.navNext)
                .on('click', '.prev', app.navPrev);
        },

        /**
         * Click on a dismiss button.
         *
         * @since 2.18
         */
        buttonDismiss: function (event) {
            event.preventDefault();
            app.dismiss();
        },

        /**
         * Click on the Dismiss notification button.
         *
         * @since 2.18
         *
         * @param {object} event Event object.
         */
        dismiss: function (event) {

            if (el.$currentMessage.length === 0) {
                return;
            }

            // Update counter.
            var count = parseInt(el.$adminBarCounter.text(), 10);
            if (count > 1) {
                --count;
                el.$adminBarCounter.html('<span>' + count + '</span>');
            } else {
                el.$adminBarCounter.remove();
                el.$adminBarMenuItem.remove();
            }

            // Remove notification.
            var $nextMessage = el.$nextMessage.length < 1 ? el.$prevMessage : el.$nextMessage,
                messageId = el.$currentMessage.data('message-id');

            if ($nextMessage.length === 0) {
                el.$notifications.remove();
            } else {
                el.$currentMessage.remove();
                $nextMessage.addClass('current');
                app.updateNavigation();
            }

            // AJAX call - update option.
            var data = {
                action: 'sbi_dashboard_notification_dismiss',
                nonce: sbi_admin.nonce,
                id: messageId,
            };

            $.post(sbi_admin.ajax_url, data, function (res) {

                if (!res.success) {
                    //SBIAdmin.debug( res );
                }
            }).fail(function (xhr, textStatus, e) {

                //SBIAdmin.debug( xhr.responseText );
            });
        },

        /**
         * Click on the Next notification button.
         *
         * @since 2.18
         *
         * @param {object} event Event object.
         */
        navNext: function (event) {

            if (el.$nextButton.hasClass('disabled')) {
                return;
            }

            el.$currentMessage.removeClass('current');
            el.$nextMessage.addClass('current');

            app.updateNavigation();
        },

        /**
         * Click on the Previous notification button.
         *
         * @since 2.18
         *
         * @param {object} event Event object.
         */
        navPrev: function (event) {

            if (el.$prevButton.hasClass('disabled')) {
                return;
            }

            el.$currentMessage.removeClass('current');
            el.$prevMessage.addClass('current');

            app.updateNavigation();
        },

        /**
         * Update navigation buttons.
         *
         * @since 2.18
         */
        updateNavigation: function () {

            el.$currentMessage = el.$notifications.find('.message.current');
            el.$nextMessage = el.$currentMessage.next('.message');
            el.$prevMessage = el.$currentMessage.prev('.message');

            if (el.$notifications.find('.sbi_review_step1_notice').length > 0) {
                var isReviewStep1 = el.$currentMessage.hasClass('sbi_review_step1_notice');
                var isReviewStep2 = el.$currentMessage.prev('.message').hasClass('rn_step_2');

                el.$nextMessage = isReviewStep1 ? el.$currentMessage.next('.message').next('.message') : el.$nextMessage;
                el.$prevMessage = isReviewStep2 ? el.$currentMessage.prev('.message').prev('.message') : el.$prevMessage;
            }

            if (el.$nextMessage.length === 0) {
                el.$nextButton.addClass('disabled');
            } else {
                el.$nextButton.removeClass('disabled');
            }

            if (el.$prevMessage.length === 0) {
                el.$prevButton.addClass('disabled');
            } else {
                el.$prevButton.removeClass('disabled');
            }
        },
    };

    return app;

}(document, window, jQuery));

// Initialize.
SBIAdminNotifications.init();
