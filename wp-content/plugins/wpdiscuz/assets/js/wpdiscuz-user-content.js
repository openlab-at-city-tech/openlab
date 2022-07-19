;
/* global jQuery */
/* global wpdiscuzAjaxObj */
/* global wpdiscuzUCObj */
/* global Cookies */
jQuery(document).ready(function ($) {
    var refreshAfterDeleting = 0;
    var isNativeAjaxEnabled = parseInt(wpdiscuzAjaxObj.isNativeAjaxEnabled);
    var additionalTab = parseInt(wpdiscuzUCObj.additionalTab);
    $('body').on('click', '.wpd-info,.wpd-page-link,.wpd-delete-content,.wpd-user-email-delete-links', function (e) {
        e.preventDefault();
    });

    $('body').on('click', '.wpd-info.wpd-not-clicked', function (e) {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var data = new FormData();
        data.append('action', 'wpdGetInfo');
        data.append('wpdiscuz_nonce', wpdiscuzAjaxObj.wpdiscuz_nonce);
        
        wpdFullInfo(btn, data);
        return false;
    });

    function wpdFullInfo(btn, data) {
        var icon = $('.fas', btn);
        var oldClass = icon.attr('class');
        icon.removeClass();
        icon.addClass('fas fa-pulse fa-spinner');
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    icon.removeClass();
                    icon.addClass(oldClass);
                    if (response) {
                        $('#wpdUserContentInfo').html(response);
                        $('#wpdUserContentInfo ul.wpd-list .wpd-list-item:first-child').addClass('wpd-active');
                        $('#wpdUserContentInfo div.wpd-content .wpd-content-item:first-child').addClass('wpd-active');

                        if (!($('#wpdUserContentInfo').is(':visible'))) {
                            $('#wpdUserContentInfoAnchor').trigger('click');
                        }
                    }
                });
    }

    $('body').on('click', '.wpd-list-item', function () {
        var relValue = $('input.wpd-rel', this).val();
        $('#wpdUserContentInfo .wpd-list-item').removeClass('wpd-active');
        $('#wpdUserContentInfo .wpd-content-item').removeClass('wpd-active');
        var $this = $(this);
        if (!$('#wpdUserContentInfo #' + relValue).text().length) {
            var data = new FormData();
            data.append('action', $this.attr('data-action'));
            data.append('wpdiscuz_nonce',wpdiscuzAjaxObj.wpdiscuz_nonce);
            data.append('page', 0);
            $('#wpdUserContentInfo #' + relValue).addClass('wpd-active');
            $('#wpdUserContentInfo #' + relValue).css('text-align', 'center');
            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, true, data)
                    .done(function (response) {
                        if (response) {
                            $('#wpdUserContentInfo #' + relValue).css('text-align', '');
                            $this.addClass('wpd-active');
                            $('#wpdUserContentInfo #' + relValue).html(response);
                        }
                        $('#wpdiscuz-loading-bar').hide();
                    });
        } else {
            $this.addClass('wpd-active');
            $('#wpdUserContentInfo #' + relValue).addClass('wpd-active');
        }
    });


    $('body').on('click', '.wpd-page-link.wpd-not-clicked', function (e) {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var goToPage = btn.data('wpd-page');
        var action = $('.wpd-active .wpd-pagination .wpd-action').val();
        var data = new FormData();
        data.append('action', action);
        data.append('page', goToPage);
        data.append('wpdiscuz_nonce',wpdiscuzAjaxObj.wpdiscuz_nonce);
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, true, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    if (response) {
                        $('.wpd-content-item.wpd-active').html(response);
                    }
                    $('#wpdiscuz-loading-bar').hide();
                });
    });

    $('body').on('click', '.wpd-delete-content.wpd-not-clicked', function () {
        var btn = $(this);
        var id = parseInt(btn.data('wpd-content-id'));
        if (!isNaN(id)) {
            var action = btn.data('wpd-delete-action');
            if (action === 'wpdDeleteComment' && !confirm(wpdiscuzAjaxObj.applyFilterOnPhrase(wpdiscuzUCObj.msgConfirmDeleteComment, 'wc_confirm_comment_delete', btn))) {
                return false;
            } else if (action === 'wpdCancelSubscription' && !confirm(wpdiscuzAjaxObj.applyFilterOnPhrase(wpdiscuzUCObj.msgConfirmCancelSubscription, 'wc_confirm_cancel_subscription', btn))) {
                return false;
            } else if (action === 'wpdCancelFollow' && !confirm(wpdiscuzAjaxObj.applyFilterOnPhrase(wpdiscuzUCObj.msgConfirmCancelFollow, 'wc_confirm_cancel_follow', btn))) {
                return false;
            }
            var icon = $('i', btn);
            var oldClass = icon.attr('class');
            var goToPage = $('.wpd-wrapper .wpd-page-number').val();
            var childCount = $('.wpd-content-item.wpd-active').children('.wpd-item').length;
            btn.removeClass('wpd-not-clicked');
            icon.removeClass().addClass('fas fa-pulse fa-spinner');
            if (childCount === 1 && goToPage > 0) {
                goToPage = goToPage - 1;
            }

            var data = new FormData();
            data.append('id', id);
            data.append('page', goToPage);
            data.append('action', action);
            data.append('wpdiscuz_nonce',wpdiscuzAjaxObj.wpdiscuz_nonce);

            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                    .done(function (response) {
                        btn.addClass('wpd-not-clicked');
                        icon.removeClass().addClass(oldClass);
                        $('.wpd-content-item.wpd-active').html(response);
                        refreshAfterDeleting = 1;
                    });

        }
    });

    $('body').on('click', '[data-lity-close]', function (e) {
        if ($(e.target).is('[data-lity-close]')) {
            if (refreshAfterDeleting) {
                window.location.reload(true);
            }
        }
    });

    $('body').on('click', '.wpd-user-email-delete-links.wpd-not-clicked', function () {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        $('.wpd-loading', btn).addClass('wpd-show');
        var data = new FormData();
        data.append('action', 'wpdEmailDeleteLinks');
        data.append('wpdiscuz_nonce',wpdiscuzAjaxObj.wpdiscuz_nonce);
        wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                .done(function (response) {
                    btn.addClass('wpd-not-clicked');
                    $('[data-lity-close]', window.parent.document).trigger('click');
                });
    });

    $('body').on('click', '.wpd-user-settings-button.wpd-not-clicked', function () {
        var btn = $(this);
        btn.removeClass('wpd-not-clicked');
        var guestAction = btn.data('wpd-delete-action');
        if (guestAction !== 'deleteCookies') {
            btn.find('.wpd-loading').addClass('wpd-show');
            var data = new FormData();
            data.append('action', 'wpdGuestAction');
            data.append('guestAction', guestAction);
            data.append('wpdiscuz_nonce',wpdiscuzAjaxObj.wpdiscuz_nonce);
            
            wpdiscuzAjaxObj.getAjaxObj(isNativeAjaxEnabled || additionalTab, false, data)
                    .done(function (response) {
                        btn.addClass('wpd-not-clicked');
                        btn.find('.wpd-loading').removeClass('wpd-show');
                        try {
                            var r = $.parseJSON(response);
                            btn.after(r.message);
                            var messageWrap = btn.next('.wpd-guest-action-message');
                            messageWrap.fadeIn(100).fadeOut(7000, function () {
                                messageWrap.remove();
                                if (parseInt(r.code) === 1) {
                                    btn.parent().remove();
                                    guestActionDeleteCookieClass();
                                }
                            });
                        } catch (e) {
                            console.log(e);
                        }
                    });
        } else {
            wpdDeleteAllCookies();
        }
    });

    function guestActionDeleteCookieClass() {
        if (!$('.wpd-delete-all-comments').length && !$('.wpd-delete-all-subscriptions').length) {
            $('.wpd-delete-all-cookies').parent().addClass('wpd-show');
        }
    }

    function wpdDeleteAllCookies() {
        var wpdCookies = document.cookie.split(";");
        for (var i = 0; i < wpdCookies.length; i++) {
            var wpdCookie = wpdCookies[i];
            var eqPos = wpdCookie.indexOf("=");
            var name = eqPos > -1 ? wpdCookie.substr(0, eqPos) : wpdCookie;
            Cookies.remove(name.trim());
        }
        location.reload(true);
    }

});