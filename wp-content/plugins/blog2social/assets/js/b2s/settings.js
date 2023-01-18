jQuery.noConflict();
jQuery(window).on("load", function () {

    var showMeridian = true;
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
        showMeridian = false;
    }
    jQuery('.b2s-settings-sched-item-input-time').timepicker({
        minuteStep: 30,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current'
    });
    var b2sShowSection = jQuery('#b2sShowSection').val();
    if (b2sShowSection != "") {
        jQuery("." + b2sShowSection).trigger("click");
    }
    jQuery(".b2s-import-auto-post-type").chosen();
    
});

jQuery('.b2sSaveSocialMetaTagsSettings').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-user-settings-area").hide();
        jQuery('.b2s-server-connection-fail').hide();
        jQuery('.b2s-meta-tags-success').hide();
        jQuery('.b2s-meta-tags-danger').hide();
        jQuery.ajax({
            processData: false,
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                    if (data.b2s == true) {
                        if (data.yoast == true) {
                            jQuery('.b2s-meta-tags-yoast').show();
                        }
                        if (data.aioseop) {
                            jQuery('.b2s-meta-tags-aioseop').show();
                        }
                        if (data.webdados) {
                            jQuery('.b2s-meta-tags-webdados').show();
                        }
                    }
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});

jQuery(document).on('click', '.b2sClearSocialMetaTags', function () {

    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery('.b2s-clear-meta-tags').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_reset_social_meta_tags',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-clear-meta-tags-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-clear-meta-tags-error').show();
            }
        }
    });
    return false;
});



jQuery(document).on('click', '.b2s-upload-image', function () {
    var targetId = jQuery(this).attr('data-id');
    if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
        wpMedia = wp.media({
            title: jQuery('#b2s_wp_media_headline').val(),
            button: {
                text: jQuery('#b2s_wp_media_btn').val(),
            },
            multiple: false,
            library: {type: 'image'}
        });
        wpMedia.open();

        wpMedia.on('select', function () {
            var validExtensions = ['jpg', 'jpeg', 'png'];
            var attachment = wpMedia.state().get('selection').first().toJSON();

            jQuery('#' + targetId).val(attachment.url);
        });
    } else {
        jQuery('.b2s-upload-image-no-permission').show();
    }
    return false;
});




jQuery(document).on('click', '.b2s-save-settings-pro-info', function () {
    return false;
});

jQuery(document).on('click', '.b2s-user-network-settings-short-url', function () {
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery('.b2s-server-connection-fail').hide();
    
    var provider_id = jQuery(this).data('provider-id');
    if (jQuery('.b2s-user-network-shortener-state[data-provider-id="'+provider_id+'"]').val() == "0") {
        jQuery('.b2s-shortener-account-connect-btn[data-provider-id="'+provider_id+'"]').trigger('click');
    } else {
        jQuery(".b2s-user-settings-area").hide();
        jQuery(".b2s-loading-area").show();

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_user_network_settings',
                'short_url': jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-user-settings-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                    if (jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').is(":checked")) {
                        jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').prop('checked', false);
                    } else {
                        jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').prop('checked', true);
                    }
                } else {
                    if(data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
    }
    return false;
});

jQuery(document).on('click', '.b2s-shortener-account-delete-btn', function () {

    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery(".b2s-user-settings-area").hide();
    jQuery(".b2s-loading-area").show();

    var provider_id = jQuery(this).attr('data-provider-id');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'shortener_account_auth_delete': provider_id,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-user-network-shortener-account-detail[data-provider-id="' + provider_id + '"]').hide();
                jQuery('.b2s-user-network-shortener-connect[data-provider-id="' + provider_id + '"]').css('display', 'inline-block');
                if(jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').prop('checked') == true) {
                    jQuery('.b2s-user-network-settings-short-url[data-provider-id="'+provider_id+'"]').prop('checked', false);
                    jQuery('.b2s-user-network-settings-short-url[data-provider-id="-1"]').prop('checked', true);
                }
                jQuery('.b2s-user-network-shortener-state[data-provider-id="'+provider_id+'"]').val("0");
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});

jQuery(document).on('change', '#b2s-user-time-zone', function () {
    var curUserTime = calcCurrentExternTimeByOffset(jQuery('option:selected', this).attr('data-offset'), jQuery('#b2sUserLang').val());
    jQuery('#b2s-user-time').text(curUserTime);

    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-nonce-check-fail').hide();

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'user_time_zone': jQuery(this).val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        }
    });
    return false;
});

jQuery(document).on('click', '#b2s-user-network-settings-allow-shortcode', function () {
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'allow_shortcode': jQuery('#b2s-user-network-settings-allow-shortcode').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
                jQuery('#b2s-user-network-settings-allow-shortcode').val(data.content);
                if (jQuery("#b2s-user-network-settings-allow-shortcode").is(":checked")) {
                    jQuery('#b2s-user-network-settings-allow-shortcode').prop('checked', false);
                } else {
                    jQuery('#b2s-user-network-settings-allow-shortcode').prop('checked', true);
                }
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});


jQuery(document).on('click', '#b2s-general-settings-legacy-mode', function () {
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'legacy_mode': jQuery('#b2s-general-settings-legacy-mode').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
                jQuery('#b2s-general-settings-legacy-mode').val(data.content);
                if (jQuery("#b2s-general-settings-legacy-mode").is(":checked")) {
                    jQuery('#b2s-general-settings-legacy-mode').prop('checked', false);
                } else {
                    jQuery('#b2s-general-settings-legacy-mode').prop('checked', true);
                    jQuery('#b2s_og_active').prop('checked', false);
                    jQuery('#b2s_card_active').prop('checked', false);
                    jQuery('#b2s_oembed_active').prop('checked', false);
                }
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});

function padDate(n) {
    return ("0" + n).slice(-2);
}

function calcCurrentExternTimeByOffset(offset, lang) {

    var UTCstring = (new Date()).getTime() / 1000;
    var neuerTimestamp = UTCstring + (offset * 3600);
    neuerTimestamp = parseInt(neuerTimestamp);
    var newDate = new Date(neuerTimestamp * 1000);
    var year = newDate.getUTCFullYear();
    var month = newDate.getUTCMonth() + 1;
    if (month < 10) {
        month = "0" + month;
    }

    var day = newDate.getUTCDate();
    if (day < 10) {
        day = "0" + day;
    }

    var mins = newDate.getUTCMinutes();
    if (mins < 10) {
        mins = "0" + mins;
    }

    var hours = newDate.getUTCHours();
    if (lang == "de") {
        if (hours < 10) {
            hours = "0" + hours;
        }
        return  day + "." + month + "." + year + " " + hours + ":" + mins;
    }
    var am_pm = "";
    if (hours >= 12) {
        am_pm = "PM";
    } else {
        am_pm = "AM";
    }

    if (hours == 0) {
        hours = 12;
    }

    if (hours > 12) {
        var newHour = hours - 12;
        if (newHour < 10) {
            newHour = "0" + newHour;
        }
    } else {
        var newHour = hours;
    }
    return year + "/" + month + "/" + day + " " + newHour + ":" + mins + " " + am_pm;
}


function wopShortener(url, name) {
    var location = encodeURI(window.location.protocol + '//' + window.location.hostname);
    window.open(url + '&location=' + location, name, "width=900,height=600,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}

window.addEventListener('message', function (e) {
    if (e.origin == jQuery('#b2sServerUrl').val()) {
        var data = JSON.parse(e.data);
        loginSuccessShortener(data.providerId, data.displayName);
    }
});

function loginSuccessShortener(providerId, displayName) {
    if(providerId == 2) {
        displayNameParts = displayName.split('#SNIP#');
        displayName = jQuery('#brandName').val() + ': ' + displayNameParts[0] + ' | ' + jQuery('#campaignName').val() + ': ' + displayNameParts[1];
    }
    jQuery('.b2s-user-network-shortener-account-detail[data-provider-id="' + providerId + '"]').css('display', 'inline-block');
    jQuery('.b2s-shortener-account-display-name[data-provider-id="' + providerId + '"]').html(displayName);
    jQuery('.b2s-user-network-shortener-connect[data-provider-id="' + providerId + '"]').hide();
    jQuery('.b2s-user-network-settings-short-url[data-provider-id="' + providerId + '"]').prop("checked", true);
    jQuery('.b2s-user-network-settings-short-url[data-provider-id="' + providerId + '"]').val("0");
    jQuery('.b2s-user-network-shortener-state[data-provider-id="'+providerId+'"]').val("1");
}

jQuery(document).on('click', '.b2sInfoTimeZoneModalBtn', function () {
    jQuery('#b2sInfoTimeZoneModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoAllowShortcodeModalBtn', function () {
    jQuery('#b2sInfoAllowShortcodeModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoLegacyModeBtn', function () {
    jQuery('#b2sInfoLegacyMode').modal('show');
});

jQuery(document).on('change', '.b2s-time-format-toggle', function() {
    var time_format = 1;
    if(jQuery(this).is(':checked')) {
        time_format = 1;
    } else {
        time_format = 0;
    }
    
    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-user-settings-area").hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_user_network_settings',
            'user_time_format': time_format,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-user-settings-area").show();
            if (data.result == true) {
                jQuery('.b2s-settings-user-success').show();
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});