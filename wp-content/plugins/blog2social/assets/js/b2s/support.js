jQuery.noConflict();


jQuery(document).ready(function () {
//    getWidgetFaq();
    if (window.location.href.match('b2s-support-check-system') != null) {
        jQuery('.b2s-support-check-sytem').trigger('click');
    }
    if (window.location.href.match('b2s-support-sharing-debugger') != null) {
        jQuery('.b2s-support-sharing-debugger').trigger('click');
    }
    jQuery(document).on('click', '.b2s-btn-sharing-debugger', function () {
        var networkId = jQuery(this).attr('data-network-id');
        if (networkId != 2) {
            var url = jQuery(this).attr('b2s-url-query') + encodeURIComponent(jQuery('#b2s-debug-url[data-network-id="' + networkId + '"').val());
        } else {
            var url = jQuery(this).attr('b2s-url-query');
        }
        window.open(url, '_blank');
        return false;
    });
});

//function getWidgetFaq() {
//    jQuery('.b2s-faq-area').show();
//    if (typeof wp.heartbeat == "undefined") {
//        jQuery('#b2s-heartbeat-fail').show();
//    }
//    jQuery.ajax({
//        url: ajaxurl,
//        type: "POST",
//        dataType: "json",
//        cache: false,
//        data: {
//            'action': 'b2s_get_faq_entries',
//            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
//        },
//        error: function () {
//            jQuery('.b2s-faq-area').hide();
//            return false;
//        },
//        success: function (data) {
//            if (data.result == true) {
//                jQuery('.b2s-loading-area-faq').hide();
//                jQuery('.b2s-faq-content').html(data.content);
//            } else {
//                if (data.error == 'nonce') {
//                    jQuery('.b2s-nonce-check-fail').show();
//                }
//                jQuery('.b2s-faq-area').hide();
//            }
//        }
//    });
//}


function base64EncodeUnicode(str) {
    var utf8Bytes = encodeURIComponent(str).replace(/%([0-9A-F]{2})/g, function (match, p1) {
        return String.fromCharCode('0x' + p1);
    });

    return btoa(utf8Bytes);
}

jQuery(document).on('click', '.b2s-support-check-sytem', function () {
    
    var heartbeat_status = true;
    if (typeof wp.heartbeat == "undefined") {
        heartbeat_status = false;
    }
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-support-fail').hide();
    jQuery('#b2s-main-debug').hide();
    jQuery('.b2s-loading-area').show();

    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        dataType: "json",
        cache: false,
        data: {'action': 'b2s_support_systemrequirements', 'heartbeat_status': heartbeat_status, 'b2s_security_nonce': jQuery('#b2s_security_nonce').val()},
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            jQuery('.b2s-loading-area').hide();
            jQuery('#b2s-main-debug').show();
            return false;
        },
        success: function (data) {
            if (typeof data === 'undefined' || data === null) {
                jQuery('.b2s-server-connection-fail').show();
                jQuery('.b2s-loading-area').hide();
                jQuery('#b2s-main-debug').show();
                return false;
            } else if (data.result != true) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.error == 'admin') {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('#b2s-support-no-admin').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                    jQuery('.b2s-loading-area').hide();
                    jQuery('#b2s-main-debug').show();
                    return false;
                }
            } else {
                if (typeof data.htmlData !== 'undefined') {
                    jQuery('#b2s-debug-htmlData').html(data.htmlData);
                    if (typeof data.blogData !== 'undefined') {
                        jQuery('#b2s-debug-export').removeClass('b2s-support-link-not-active');
                        jQuery('#b2s-debug-export').attr(
                                "href", "data:application/octet-stream;charset=utf-8;base64," +
                                base64EncodeUnicode(JSON.stringify(data.blogData, undefined, 2))
                                );
                    } else {
                        jQuery('#b2s-debug-export').addClass('b2s-support-link-not-active');
                    }
                    jQuery('.b2s-loading-area').hide();
                    jQuery('#b2s-main-debug').show();
                    return true;
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                    jQuery('.b2s-loading-area').hide();
                    jQuery('#b2s-main-debug').show();
                    return false;
                }
            }

        }
    });
});

jQuery(document).on('click', '#b2s-reload-debug-btn', function () {
    if (window.location.href.match('b2s-support-check-system') != null) {
        location.reload();
    } else {
        window.location.href = window.location.href + '#b2s-support-check-system';
        location.reload();
    }
});

jQuery(document).on('click', '#b2s-community-register', function() {
    jQuery('#b2s-community-username').removeClass('error');
    jQuery('#b2s-community-email').removeClass('error');
    jQuery('#b2s-community-password').removeClass('error');
    if(jQuery('#b2s-community-username').val().length < 1) {
        jQuery('#b2s-community-username').addClass('error');
        return false;
    }
    if(jQuery('#b2s-community-email').val().length < 1) {
        jQuery('#b2s-community-email').addClass('error');
        return false;
    }
    if(jQuery('#b2s-community-password').val().length < 1) {
        jQuery('#b2s-community-password').addClass('error');
        return false;
    }
    
    jQuery('.b2s-community-register-loading').show();
    jQuery('.b2s-community-register-form').hide();
    jQuery('.b2s-community-error').hide();
    var username = jQuery('#b2s-community-username').val();
    var email = jQuery('#b2s-community-email').val();
    var password = jQuery('#b2s-community-password').val();
    
    if(password.length < 8) {
        jQuery('.b2s-community-error[data-error-type="invalid_password"]').show();
        jQuery('#b2s-community-password').addClass('error');
        jQuery('.b2s-community-register-loading').hide();
        jQuery('.b2s-community-register-form').show();
        return false;
    }
    
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_community_register',
            'username': username,
            'email': email,
            'password': password,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-community-error[data-error-type="invalid_data"]').show();
            jQuery('.b2s-community-register-loading').hide();
            jQuery('.b2s-community-register-form').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-community-register-loading').hide();
                jQuery('.b2s-community-register-success').show();
                return true;
            } else {
                if(typeof data.error_reason == 'undefined' || data.error_reason.length < 1) {
                    jQuery('.b2s-community-error[data-error-type="invalid_data"]').show();
                } else if(data.error_reason == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-community-error[data-error-type="'+data.error_reason+'"]').show();
                }
            }
            jQuery('.b2s-community-register-loading').hide();
            jQuery('.b2s-community-register-form').show();
            return false;
        }
    });
    
});

jQuery(document).on('change', '#b2s-community-terms', function() {
    if(jQuery(this).is(':checked')) {
        jQuery('#b2s-community-register').prop("disabled", false);
    } else {
        jQuery('#b2s-community-register').prop("disabled", true);
    }
});