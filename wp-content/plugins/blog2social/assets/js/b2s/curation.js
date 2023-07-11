jQuery.noConflict();

if (typeof wp.heartbeat !== "undefined") {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['b2s_heartbeat'] = 'b2s_listener';
    });
    wp.heartbeat.connectNow();
}
jQuery(window).on("load", function () {
    var url_string = window.location.href;
    var url_param = new URL(url_string);
    var type = url_param.searchParams.get("type");
    switch (type) {
        case "link":
            activateLink();
            break;
        case "image":
            activateImage();
            break;
        case "text":
            activateText();
            break;
        case "video":
            activateVideo();
            break;
    }
    
    var url = url_param.searchParams.get("url");
    var comment = url_param.searchParams.get("comment");
    var image_id = url_param.searchParams.get("image_id");
    var image_url = url_param.searchParams.get("image_url");
    var postId = url_param.searchParams.get("postId");
    if (typeof postId != "undefined" && postId != "" && postId != null) {
        jQuery('#b2s-draft-id').val(postId);
    }
    if (typeof url != "undefined" && url != "" && url != null) {
        jQuery('#b2s-curation-input-url').val(url);
        jQuery('.b2s-btn-curation-continue').trigger('click');
    } else if(typeof comment != "undefined" && comment != "" && comment != null) {
        if(typeof image_id != "undefined" && image_id != "" && image_id != null && typeof image_url != "undefined" && image_url != "" && image_url != null) {
            activateImage();
            jQuery('.b2s-post-item-details-url-image').attr('src', image_url);
            jQuery('.b2s-image-url-hidden-field').val(image_url);
            jQuery('.b2s-image-id-hidden-field').val(image_id);
            jQuery('.b2s-image-remove-btn').show();
            jQuery('.b2s-post-item-details-item-message-input').val(comment);
        } else {
            activateText();
            jQuery('.b2s-post-item-details-item-message-input').val(comment);
        }
    }    
});

jQuery(document).on('click', '.b2s-curation-link', function() {
    activateLink();
    return false;
});
jQuery(document).on('click', '.b2s-curation-text', function() {
    activateText();
    return false;
});
jQuery(document).on('click', '.b2s-curation-image', function() {
    activateImage();
    return false;
});
//jQuery(document).on('click', '.b2s-curation-video', function() {
//    activateVideo();
//    return false;
//});

jQuery(document).on('click', '.b2s-btn-curation-continue', function () {
    jQuery('#b2s-curation-input-url-help').hide();
    var re = new RegExp(/^(https?:\/\/)+[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+(?:\.[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=%.ÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß]+$/);
    var url = jQuery('#b2s-curation-input-url').val();
    if (re.test(url)) {
        jQuery('#b2s-curation-input-url').removeClass('error');
        jQuery('.b2s-loading-area').show();
        jQuery('.b2s-curation-result-area').show();
        scrapeDetails(url);
    } else {
        jQuery('#b2s-curation-input-url').addClass('error');
        jQuery('#b2s-curation-input-url-help').show();
    }
    return false;
});

jQuery(document).on("keyup", "#b2s-curation-input-url", function () {
    var url = jQuery(this).val();
    jQuery(this).removeClass("error");
    jQuery('#b2s-curation-input-url-help').hide();
    if (url.length != "0") {
        if (url.indexOf("http://") == -1 && url.indexOf("https://") == -1) {
            url = "https://" + url;
            jQuery(this).val(url);
        }
    }
    return false;
});

jQuery(document).on('click', '.b2s-btn-change-url-preview', function () {
    jQuery('.b2s-curation-input-area').show();
    jQuery('.b2s-btn-curation-continue').prop("disabled", false);
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-curation-preview-area').hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-no-review-info').hide();
    jQuery('#b2s-curation-no-data-info').hide();
    return false;
});

jQuery(document).on('change', '#b2s-post-curation-ship-type', function () {
    if (jQuery(this).val() == 1) {
        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2s-sched-post-modal').modal('show');
            jQuery(this).val('0');
            return false;
        }
    }

    if (jQuery(this).val() == 1) {
        jQuery('.b2s-post-curation-ship-date-area').show();
        jQuery('#b2s-post-curation-ship-date').prop("disabled", false);

        var today = new Date();

        if (jQuery('#b2sSelSchedDate').val() != "") {
            today.setTime(jQuery('#b2sSelSchedDate').val());
        }
        if (today.getMinutes() >= 30) {
            today.setHours(today.getHours() + 1);
            today.setMinutes(0);
        } else {
            today.setMinutes(30);
        }

        var setTodayDate = today.getFullYear() + '-' + (padDate(today.getMonth() + 1)) + '-' + padDate(today.getDate()) + ' ' + formatAMPM(today);
        if (jQuery('#b2s-post-curation-ship-date').attr('data-language') == 'de') {
            setTodayDate = padDate(today.getDate()) + '.' + (padDate(today.getMonth() + 1)) + '.' + today.getFullYear() + ' ' + padDate(today.getHours()) + ':' + padDate(today.getMinutes());
        }
        jQuery('#b2s-post-curation-ship-date').b2sdatepicker({'autoClose': true, 'toggleSelected': false, 'minutesStep': 15, 'minDate': new Date(), 'startDate': today, 'todayButton': new Date(), 'position': 'top left'});

        var curationPicker = jQuery('#b2s-post-curation-ship-date').b2sdatepicker().data('b2sdatepicker');
        curationPicker.selectDate(new Date(today.getFullYear(), today.getMonth(), today.getDate()));
        jQuery('#b2s-post-curation-ship-date').val(setTodayDate);

    } else {
        jQuery('.b2s-post-curation-ship-date-area').hide();
        jQuery('#b2s-post-curation-ship-date').prop("disabled", true);
    }
});

function scrapeDetails(url) {
    var loadSettings = true;
    if (!jQuery('.b2s-curation-settings-area').is(':empty')) {
        loadSettings = false;
    }
    jQuery('.b2s-curation-input-area').hide();
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-curation-preview-area').hide();
//    jQuery('.b2s-curation-select').hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-no-review-info').hide();
    jQuery('#b2s-curation-no-data-info').hide();


    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        async: true,
        cache: true,
        data: {
            'url': url,
            'action': 'b2s_scrape_url',
            'loadSettings': loadSettings,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-curation-settings-area').hide();
            jQuery('.b2s-curation-preview-area').hide();
            jQuery('.b2s-curation-preview-area').show();
            jQuery('#b2s-btn-curation-customize').prop("disabled", true);
            jQuery('#b2s-btn-curation-share').prop("disabled", true);
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            if (data.result == true) {
                if (loadSettings) {
                    jQuery('.b2s-curation-settings-area').html(data.settings);
                    jQuery('#b2s-post-curation-profile-select [value="0"]').prop('selected', true).trigger('change');
                }
                jQuery('.b2s-curation-settings-area').show();
                jQuery('.b2s-curation-preview-area').html(data.preview);
                jQuery('.b2s-curation-preview-area').show();
                jQuery('#b2s-btn-curation-customize').prop("disabled", false);
                jQuery('#b2s-btn-curation-share').prop("disabled", false);

                //set date + select schedulding
                if (jQuery('#b2sSelSchedDate').val() != "") {
                    jQuery('#b2s-post-curation-ship-type').val('1').trigger('change');
                }
                var url_string = window.location.href;
                var url_param = new URL(url_string);
                var postId = url_param.searchParams.get("postId");
                if (typeof postId != "undefined" && postId != "") {
                    jQuery('#b2s-draft-id').val(postId);
                }
                var title = url_param.searchParams.get("title");
                if (typeof title != "undefined" && title != "" && jQuery('#b2s-post-curation-preview-title').val() == "") {
                    jQuery('#b2s-post-curation-preview-title').val(title);
                }
                var comment = url_param.searchParams.get("comment");
                if (typeof comment != "undefined" && comment != "") {
                    jQuery('#b2s-post-curation-comment').val(comment);
                }
                loadDraftShipData();
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.preview != "") {
                    jQuery('.b2s-curation-preview-area').html(data.preview);
                    jQuery('.b2s-curation-preview-area').show();
                }
                if (data.error == "NO_PREVIEW") {
                    jQuery('.b2s-curation-input-area').show();
                    jQuery('.b2s-curation-settings-area').hide();
                    jQuery('.b2s-curation-preview-area').hide();
                    jQuery('#b2s-curation-no-review-info').show();
                    jQuery('#b2s-curation-no-auth-info').hide();
                    jQuery('#b2s-curation-no-data-info').hide();
                }
                if (data.error == "NO_AUTH") {
                    jQuery('.b2s-curation-input-area').show();
                    jQuery('.b2s-curation-settings-area').hide();
                    jQuery('.b2s-curation-preview-area').hide();
                    jQuery('#b2s-curation-no-auth-info').show();
                    jQuery('#b2s-curation-no-review-info').hide();
                    jQuery('#b2s-curation-no-data-info').hide();
                }
                jQuery('#b2s-btn-curation-customize').prop("disabled", true);
                jQuery('#b2s-btn-curation-share').prop("disabled", true);
            }
//            jQuery('.b2s-curation-select').show();
            if (data.scrapeError == true) {
                jQuery('#b2s-post-curation-preview-title').attr('type', 'text');
            }
        }
    });
    return false;

}

jQuery(document).on("keyup", "#b2s-post-curation-preview-title", function () {
    jQuery(this).removeClass('error');
    if (jQuery(this).val().length === 0) {
        jQuery(this).addClass('error');
    }
    return false;
});
jQuery(document).on("keyup", "#b2s-post-curation-comment", function () {
    jQuery(this).removeClass('error');
    if (jQuery(this).val().length === 0) {
        jQuery(this).addClass('error');
    }
    return false;
});

jQuery(document).on('click', '#b2s-btn-curation-share', function () {
    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    jQuery('.b2s-post-curation-action').val('b2s_curation_share');
    var noContent = false;
    if(jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if(jQuery('#b2s-curation-post-format').val() == '1') {
        if (jQuery('.b2s-image-url-hidden-field').val().length === 0) {
            jQuery('.b2s-post-item-details-url-image').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment-image').val().length === 0) {
            jQuery('#b2s-post-curation-comment-image').addClass('error');
            noContent = true;
        }
    } else {
        if (jQuery('#b2s-post-curation-comment-text').val().length === 0) {
            jQuery('#b2s-post-curation-comment-text').addClass('error');
            noContent = true;
        }
    }
    if (noContent) {
        return false;
    }
    
    jQuery('.b2s-curation-post-list').html("");
    jQuery('.b2s-curation-post-list-area').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-curation-preview-area').hide();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
//    jQuery('.b2s-curation-select').hide();

    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat='+jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-curation-post-list-area').show();
                jQuery('.b2s-curation-post-list').html(data.content);
            } else {
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-curation-post-list-area').hide();
                jQuery('.b2s-curation-settings-area').show();
                if(jQuery('#b2s-curation-post-format').val() == '0') {
                    jQuery('.b2s-curation-preview-area').show();
                } else if(jQuery('#b2s-curation-post-format').val() == '1') {
                    jQuery('.b2s-curation-image-area').show();
                } else {
                    jQuery('.b2s-curation-text-area').show();
                }

                if (data.error == 'NO_AUTH') {
                    jQuery('#b2s-curation-no-auth-info').show();
                } else if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('#b2s-curation-no-data-info').show();
                }
            }
            wp.heartbeat.connectNow();
        }
    });
    return false;
});

window.addEventListener('message', function (e) {
    if (e.origin == jQuery('#b2sServerUrl').val()) {
        var data = JSON.parse(e.data);
        if (typeof data.action !== typeof undefined && data.action == 'approve') {
            jQuery('.b2s-post-item-details-message-result[data-network-auth-id="' + data.networkAuthId + '"]').html("<br><span class=\"text-success\"><i class=\"glyphicon glyphicon-ok-circle\"></i> " + jQuery("#b2sJsTextPublish").val() + " </span>");
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                cache: false,
                async: false,
                data: {
                    'action': 'b2s_update_approve_post',
                    'post_id': data.post_id,
                    'publish_link': data.publish_link,
                    'publish_error_code': data.publish_error_code,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {
                }
            });
        }
    }
});

function wopApprove(networkAuthId, postId, url, name) {
    var location = encodeURI(window.location.protocol + '//' + window.location.hostname);
    var win = window.open(url + '&location=' + location, name, "width=650,height=900,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
    if (postId > 0) {
        function checkIfWinClosed(intervalID) {
            if (win.closed) {
                clearInterval(intervalID);
                //Show Modal
                jQuery('.b2s-publish-approve-modal').modal('show');
                jQuery('#b2s-approve-post-id').val(postId);
                jQuery('#b2s-approve-network-auth-id').val(networkAuthId);
            }
        }
        var interval = setInterval(function () {
            checkIfWinClosed(interval);
        }, 500);
    }
}

jQuery(document).on('click', '.b2s-approve-publish-confirm-btn', function () {
    var postId = jQuery('#b2s-approve-post-id').val();
    var networkAuthId = jQuery('#b2s-approve-network-auth-id').val();
    if (postId > 0) {
        jQuery('.b2s-post-item-details-message-result[data-network-auth-id="' + networkAuthId + '"]').html("<br><span class=\"text-success\"><i class=\"glyphicon glyphicon-ok-circle\"></i> " + jQuery("#b2sJsTextPublish").val() + " </span>");
        jQuery('.b2s-publish-approve-modal').modal('hide');
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_update_approve_post',
                'post_id': postId,
                'publish_link': "",
                'publish_error_code': "",
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (data) {
            }
        });
    }
});


jQuery(document).on('click', '#b2s-btn-curation-customize', function () {
    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    var noContent = false;
    if(jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if(jQuery('#b2s-curation-post-format').val() == '1') {
        if (jQuery('.b2s-image-url-hidden-field').val().length === 0) {
            jQuery('.b2s-post-item-details-url-image').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment-image').val().length === 0) {
            jQuery('#b2s-post-curation-comment-image').addClass('error');
            noContent = true;
        }
    } else {
        if (jQuery('#b2s-post-curation-comment-text').val().length === 0) {
            jQuery('#b2s-post-curation-comment-text').addClass('error');
            noContent = true;
        }
    }
    if (noContent) {
        return false;
    }
    jQuery('.b2s-post-curation-action').val('b2s_curation_customize');
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-curation-preview-area').hide();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
//    jQuery('.b2s-curation-select').hide();
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat='+jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                window.location.href = data.redirect;
                return false;
            } else {
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-loading-area').hide();
                jQuery('#b2s-curation-no-data-info').show();
                jQuery('.b2s-curation-settings-area').show();
                jQuery('.b2s-curation-preview-area').show();
//                jQuery('.b2s-curation-select').show();
                if(jQuery('#b2s-curation-post-format').val() == '0') {
                    jQuery('.b2s-curation-link-area').show();
                } else if(jQuery('#b2s-curation-post-format').val() == '1') {
                    jQuery('.b2s-curation-image-area').show();
                } else {
                    jQuery('.b2s-curation-text-area').show();
                }
            }

        }
    });
    return false;
});

jQuery(document).on('change', '#b2s-post-curation-profile-select', function () {
    var tos = false;
    if (jQuery('#b2s-post-curation-profile-data' + jQuery(this).val()).val() == "") {
        jQuery('#b2s-curation-no-auth-info').show();
        tos = true;
    } else {
        jQuery('#b2s-curation-no-auth-info').hide();
        //TOS Twitter Check
        var len = jQuery('#b2s-post-curation-twitter-select').children('option[data-mandant-id="' + jQuery(this).val() + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-curation-twitter-area').show();
            jQuery('#b2s-post-curation-twitter-select').prop('disabled', false);
            jQuery('#b2s-post-curation-twitter-select').show();
            jQuery('#b2s-post-curation-twitter-select option').attr("disabled", "disabled");
            jQuery('#b2s-post-curation-twitter-select option[data-mandant-id="' + jQuery(this).val() + '"]').attr("disabled", false);
            jQuery('#b2s-post-curation-twitter-select option[data-mandant-id="' + jQuery(this).val() + '"]:first').attr("selected", "selected");
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-curation-twitter-area').hide();
        jQuery('#b2s-post-curation-twitter-select').prop('disabled', 'disabled');
        jQuery('#b2s-post-curation-twitter-select').hide();
    }
});



function padDate(n) {
    return ("0" + n).slice(-2);
}


function formatAMPM(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0' + minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;
    return strTime;
}

jQuery(document).on('click', '#b2s-btn-curation-draft', function () {
    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    var noContent = false;
    if(jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if(jQuery('#b2s-curation-post-format').val() == '1') {
        if (jQuery('.b2s-image-url-hidden-field').val().length === 0) {
            jQuery('.b2s-post-item-details-url-image').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment-image').val().length === 0) {
            jQuery('#b2s-post-curation-comment-image').addClass('error');
            noContent = true;
        }
    } else {
        if (jQuery('#b2s-post-curation-comment-text').val().length === 0) {
            jQuery('#b2s-post-curation-comment-text').addClass('error');
            noContent = true;
        }
    }
    if (noContent) {
        return false;
    }
    jQuery('.b2s-post-curation-action').val('b2s_curation_draft');
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-curation-settings-area').hide();
    jQuery('.b2s-curation-preview-area').hide();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
//    jQuery('.b2s-curation-select').hide();
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat='+jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                if (typeof data.postId != undefined) {
                    jQuery('#b2s-draft-id').val(data.postId);
                }
                jQuery('#b2s-curation-saved-draft-info').show();
                setTimeout(function () {
                    jQuery('#b2s-curation-saved-draft-info').fadeOut("slow");
                }, 5000);
            } else {
                jQuery('#b2s-curation-no-data-info').show();
                if(data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-curation-settings-area').show();
            if(jQuery('#b2s-curation-post-format').val() == '0') {
                jQuery('.b2s-curation-preview-area').show();
            } else if(jQuery('#b2s-curation-post-format').val() == '1') {
                jQuery('.b2s-curation-image-area').show();
            } else {
                jQuery('.b2s-curation-image-area').show();
            }
//            jQuery('.b2s-curation-select').show();

        }
    });
    return false;
});

function activateLink() {
    jQuery('.b2s-curation-title').hide();
    jQuery('#b2s-curation-title-link').show();
    jQuery('.b2s-curation-subtitle').hide();
    jQuery('#b2s-curation-subtitle-link').show();
    jQuery('#b2s-post-curation-comment').val(jQuery('#b2s-post-curation-comment-dummy').val());
    jQuery('#b2s-curation-post-format').val('0');
    jQuery('.b2s-curation-link').removeClass('btn-outline-dark').addClass('btn-primary');
    jQuery('.b2s-curation-video').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-image').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-text').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-loading-area').hide();
    jQuery('.b2s-curation-link-area').show();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
    if(jQuery('.b2s-curation-input-area').is(':visible')){
        jQuery('.b2s-curation-settings-area').hide();
    } else {
        jQuery('.b2s-curation-settings-area').show();
    }
    jQuery('.b2s-curation-input-area-info-header-text').show();
    jQuery('.b2s-curation-input-area-info-header-text-video').hide();
};

function activateVideo() {
    jQuery('.b2s-curation-title').hide();
    jQuery('#b2s-curation-title-video').show();
    jQuery('.b2s-curation-subtitle').hide();
    jQuery('#b2s-curation-subtitle-video').show();
    jQuery('#b2s-post-curation-comment').val(jQuery('#b2s-post-curation-comment-dummy').val());
    jQuery('#b2s-curation-post-format').val('0');
    jQuery('.b2s-curation-video').removeClass('btn-outline-dark').addClass('btn-primary');
    jQuery('.b2s-curation-link').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-image').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-text').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-loading-area').hide();
    jQuery('.b2s-curation-link-area').show();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
    if(jQuery('.b2s-curation-input-area').is(':visible')){
        jQuery('.b2s-curation-settings-area').hide();
    } else {
        jQuery('.b2s-curation-settings-area').show();
    }
    jQuery('.b2s-curation-input-area-info-header-text').hide();
    jQuery('.b2s-curation-input-area-info-header-text-video').show();
};

function activateImage() {
    jQuery('.b2s-curation-title').hide();
    jQuery('#b2s-curation-title-image').show();
    jQuery('.b2s-curation-subtitle').hide();
    jQuery('#b2s-curation-subtitle-image').show();
    jQuery('#b2s-post-curation-comment-image').val(jQuery('#b2s-post-curation-comment-dummy').val());
    jQuery('#b2s-curation-post-format').val('1');
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-curation-link-area').hide();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
    jQuery('.b2s-curation-image').removeClass('btn-outline-dark').addClass('btn-primary');
    jQuery('.b2s-curation-link').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-video').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-text').removeClass('btn-primary').addClass('btn-outline-dark');
    if(jQuery('.b2s-curation-settings-area').html().length == 0) {
        jQuery.ajax({
            url: ajaxurl,
            type: "GET",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_get_curation_ship_details',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if(jQuery('.b2s-curation-image').hasClass('btn-primary')) {
                    if (data.result == true) {
                        jQuery('.b2s-curation-settings-area').html(data.settings);
                        jQuery('#b2s-post-curation-profile-select [value="0"]').prop('selected', true).trigger('change');
                        jQuery('.b2s-loading-area').hide();
                        jQuery('.b2s-curation-image-area').show();
                        jQuery('.b2s-curation-settings-area').show();
                        loadDraftShipData();
                        return false;
                    } else {
                        jQuery('.b2s-loading-area').hide();
                        if(data.error == 'nonce') {
                            jQuery('.b2s-nonce-check-fail').show();
                        }
                        if (data.error == "NO_AUTH") {
                            jQuery('.b2s-curation-image-area').show();
                            jQuery('.b2s-curation-settings-area').hide();
                            jQuery('#b2s-curation-no-auth-info').show();
                            jQuery('#b2s-curation-no-review-info').hide();
                            jQuery('#b2s-curation-no-data-info').hide();
                        }
                        jQuery('#b2s-btn-curation-customize').prop("disabled", true);
                        jQuery('#b2s-btn-curation-share').prop("disabled", true);
                    }
                }
            }
        });
    } else {
        jQuery('.b2s-loading-area').hide();
        jQuery('.b2s-curation-image-area').show();
        jQuery('.b2s-curation-settings-area').show();
    }
    return false;
};

function activateText() {
    jQuery('.b2s-curation-title').hide();
    jQuery('#b2s-curation-title-text').show();
    jQuery('.b2s-curation-subtitle').hide();
    jQuery('#b2s-curation-subtitle-text').show();
    jQuery('#b2s-post-curation-comment-text').val(jQuery('#b2s-post-curation-comment-dummy').val());
    jQuery('#b2s-curation-post-format').val('2');
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-curation-link-area').hide();
    jQuery('.b2s-curation-image-area').hide();
    jQuery('.b2s-curation-text-area').hide();
    jQuery('.b2s-curation-text').removeClass('btn-outline-dark').addClass('btn-primary');
    jQuery('.b2s-curation-link').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-video').removeClass('btn-primary').addClass('btn-outline-dark');
    jQuery('.b2s-curation-image').removeClass('btn-primary').addClass('btn-outline-dark');
    if(jQuery('.b2s-curation-settings-area').html().length == 0) {
        jQuery.ajax({
            url: ajaxurl,
            type: "GET",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_get_curation_ship_details',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if(jQuery('.b2s-curation-text').hasClass('btn-primary')) {
                    if (data.result == true) {
                        jQuery('.b2s-curation-settings-area').html(data.settings);
                        jQuery('#b2s-post-curation-profile-select [value="0"]').prop('selected', true).trigger('change');
                        jQuery('.b2s-loading-area').hide();
                        jQuery('.b2s-curation-text-area').show();
                        jQuery('.b2s-curation-settings-area').show();
                        loadDraftShipData();
                        return false;
                    } else {
                        jQuery('.b2s-loading-area').hide();
                        if(data.error == 'nonce') {
                            jQuery('.b2s-nonce-check-fail').show();
                        }
                        if (data.error == "NO_AUTH") {
                            jQuery('.b2s-curation-text-area').show();
                            jQuery('.b2s-curation-settings-area').hide();
                            jQuery('#b2s-curation-no-auth-info').show();
                            jQuery('#b2s-curation-no-review-info').hide();
                            jQuery('#b2s-curation-no-data-info').hide();
                        }
                        jQuery('#b2s-btn-curation-customize').prop("disabled", true);
                        jQuery('#b2s-btn-curation-share').prop("disabled", true);
                    }
                }
            }
        });
    } else {
        jQuery('.b2s-loading-area').hide();
        jQuery('.b2s-curation-text-area').show();
        jQuery('.b2s-curation-settings-area').show();
    }
    return false;
};

jQuery(document).on('change', '#b2s-post-curation-comment', function() {
    jQuery('#b2s-post-curation-comment-dummy').val(jQuery('#b2s-post-curation-comment').val());
});
jQuery(document).on('change', '#b2s-post-curation-comment-image', function() {
    jQuery('#b2s-post-curation-comment-dummy').val(jQuery('#b2s-post-curation-comment-image').val());
});
jQuery(document).on('change', '#b2s-post-curation-comment-text', function() {
    jQuery('#b2s-post-curation-comment-dummy').val(jQuery('#b2s-post-curation-comment-text').val());
});

var emojiTranslation = JSON.parse(jQuery('#b2sEmojiTranslation').val());
var picker = new EmojiButton({
    position: 'auto',
    autoHide: false,
    i18n: {
        search: emojiTranslation['search'],
        categories: {
            recents: emojiTranslation['recents'],
            smileys: emojiTranslation['smileys'],
            animals: emojiTranslation['animals'],
            food: emojiTranslation['food'],
            activities: emojiTranslation['activities'],
            travel: emojiTranslation['travel'],
            objects: emojiTranslation['objects'],
            symbols: emojiTranslation['symbols'],
            flags: emojiTranslation['flags']
        },
        notFound: emojiTranslation['notFound']
    }
});
picker.on('emoji', function(emoji){
    if(jQuery('#b2s-curation-post-format').val() == '0') {
        var text = jQuery('#b2s-post-curation-comment').val();
        var start = jQuery('#b2s-post-curation-comment').attr('selectionStart');
        var end = jQuery('#b2s-post-curation-comment').attr('selectionEnd');
    } else {
        var text = jQuery('#b2s-post-curation-comment-image').val();
        var start = jQuery('#b2s-post-curation-comment-image').attr('selectionStart');
        var end = jQuery('#b2s-post-curation-comment-image').attr('selectionEnd');
    }
    if(typeof start == 'undefined' || typeof end == 'undefined'){
        start = text.length;
        end = text.length;
    } 
    var newText = text.slice(0, start) + emoji + text.slice(end);
    jQuery('.b2s-post-item-details-item-message-input').val(newText);
    jQuery('.b2s-post-item-details-item-message-input').focus();
    jQuery('.b2s-post-item-details-item-message-input').prop("selectionStart", parseInt(start)+emoji.length);
    jQuery('.b2s-post-item-details-item-message-input').prop("selectionEnd", parseInt(start)+emoji.length);
    jQuery('.b2s-post-item-details-item-message-input').trigger('keyup');
});

jQuery(document).on('click', '.b2s-post-item-details-item-message-emoji-btn', function() {
    if(picker.pickerVisible) {
        picker.hidePicker();
    } else {
        currentEmojiNetworkAuthId = jQuery(this).attr('data-network-auth-id');
        currentEmojiNetworkCount = jQuery(this).attr('data-network-count');
        picker.showPicker(jQuery(this));
    }
});

jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-post-item-details-item-message-input', function () {
    var tb = jQuery(this).get(0);
    jQuery(this).attr('selectionStart', tb.selectionStart);
    jQuery(this).attr('selectionEnd', tb.selectionEnd);
});

jQuery(document).on('click', '.b2s-post-item-details-url-image', function() {
    jQuery('.b2s-select-image-modal-open').trigger('click');
});

jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
    jQuery('#b2s-network-select-image').modal('show');
    return false;
});

jQuery(document).on('click', '.b2s-network-info-modal-btn', function () {
    if(jQuery('#b2s-curation-post-format').val() == "2") {
        jQuery('#b2sTextPostInfoModal').modal('show');
    } else {
        jQuery('#b2sInfoNetworkModal').modal('show');
    }
    return false;
});

jQuery(document).on('click', '.b2s-upload-image', function () {
    if (typeof wp !== 'undefined' && wp.media && wp.media.editor) {
        jQuery('#b2s-network-select-image').modal('hide');
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
            var count = parseInt(jQuery('.b2s-choose-image-count').val());
            count = count + 1;
            jQuery('.b2s-choose-image-count').val(count);
            var attachment = wpMedia.state().get('selection').first().toJSON();
            var content = '<div class="b2s-image-item">' +
                    '<div class="b2s-image-item-thumb">' +
                    '<label for="b2s-image-count-' + count + '">' +
                    '<img class="img-thumbnail networkImage" alt="blogImage" src="' + attachment.url + '">' +
                    '</label>' +
                    '</div>' +
                    '<div class="b2s-image-item-caption text-center">' +
                    '<div class="b2s-image-item-caption-resolution clearfix small"></div>' +
                    '<input type="radio" value="' + attachment.url + '" data-src="' + attachment.url + '" data-id="' + attachment.id + '" class="checkNetworkImage" name="image_url" id="b2s-image-count-' + count + '">' +
                    '</div>' +
                    '</div>';
            jQuery('.b2s-image-choose-area').html(jQuery('.b2s-image-choose-area').html() + content);
            jQuery('.b2s-choose-image-no-image-info-text').hide();
            jQuery('.b2s-choose-image-no-image-extra-btn').hide();
            jQuery('.b2s-upload-image-invalid-extension').hide();
            jQuery('input[name=image_url]:last').prop("checked", true);
            jQuery('#b2s-network-select-image').modal('show');
        });
        wpMedia.on('close', function () {
            jQuery('#b2s-network-select-image').modal('show');
        });
    } else {
        jQuery('.b2s-upload-image-no-permission').show();
    }
    return false;
});

jQuery(document).on('click', '.b2s-image-change', function () {
    if (jQuery('input[name=image_url]:checked').length > 0) {
        jQuery('.b2s-post-item-details-url-image').attr('src', jQuery('input[name=image_url]:checked').val());
        jQuery('.b2s-image-url-hidden-field').val(jQuery('input[name=image_url]:checked').val());
        jQuery('.b2s-image-id-hidden-field').val(jQuery('input[name=image_url]:checked').data('id'));
        jQuery('.b2s-image-remove-btn').show();
        jQuery('.b2s-upload-image-invalid-extension').hide();
        jQuery('.b2s-upload-image-no-permission').hide();
        jQuery('.b2s-upload-image-free-version-info').hide();
        jQuery('.b2s-post-item-details-url-image').removeClass('error');
        if(jQuery('input[name=image_url]:checked').data('id') > 0) {
            jQuery.ajax({
                url: ajaxurl,
                type: "GET",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_get_image_caption',
                    'image_id': jQuery('input[name=image_url]:checked').data('id'),
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    if (data.result == true) {
                        if(data.caption != '' && jQuery('.b2s-post-item-details-item-message-input').val() == '') {
                            jQuery('.b2s-post-item-details-item-message-input').val(data.caption);
                        }
                        return false;
                    } else {
                        if(data.error == 'nonce') {
                            jQuery('.b2s-nonce-check-fail').show();
                        }
                    }
                }
            });
        }
    }
    jQuery('#b2s-network-select-image').modal('hide');
    return false;
});

jQuery(document).on('click', '.b2s-image-remove-btn', function () {
    var defaultImage = jQuery('#b2sDefaultNoImage').val();
    //default
    jQuery('.b2s-post-item-details-url-image').attr('src', defaultImage);
    jQuery('.b2s-image-url-hidden-field').val("");
    jQuery('.b2s-image-id-hidden-field').val("");
    jQuery('.b2s-image-remove-btn').hide();
    return false;
});

jQuery(document).on('keyup', '.b2s-post-item-details-item-message-input', function() {
    jQuery(this).removeClass('error');
});

jQuery(document).on('click', '.b2s-curation-info-premium-btn', function () {
    if(jQuery(this).data('type') == 'text') {
        jQuery('#b2s-modal-header-text').show();
        jQuery('#b2s-modal-header-image').hide();
    } else {
        jQuery('#b2s-modal-header-image').show();
        jQuery('#b2s-modal-header-text').hide();
    }
    jQuery('#b2sInfoCCModal').modal('show');
});

jQuery(document).on('click', '.b2s-re-share-btn', function() {
    jQuery('.b2s-curation-post-list-area').hide();
    jQuery('.b2s-curation-settings-area').show();
//    jQuery('.b2s-curation-select').show();
    if(jQuery('#b2s-curation-post-format').val() == '0') {
        jQuery('.b2s-curation-preview-area').show();
    } else {
        jQuery('.b2s-curation-image-area').show();
    }
});

function loadDraftShipData() {
    var url_string = window.location.href;
    var url_param = new URL(url_string);
    var ship_type = url_param.searchParams.get("ship_type");
    var ship_date = url_param.searchParams.get("ship_date");
    var profile_select = url_param.searchParams.get("profile_select");
    var twitter_select = url_param.searchParams.get("twitter_select");
    if (typeof ship_type != "undefined" && ship_type != "" && ship_type != null && ship_type > 0) {
        jQuery('#b2s-post-curation-ship-type').val(ship_type);
        jQuery('#b2s-post-curation-ship-type').trigger('change');
        if (typeof ship_date != "undefined" && ship_date != "" && ship_date != null) {
            jQuery('#b2s-post-curation-ship-date').val(ship_date);
            jQuery('#b2s-post-curation-ship-date').trigger('change');
        }
    }
    if (typeof profile_select != "undefined" && profile_select != "" && profile_select != null) {
        jQuery('#b2s-post-curation-profile-select').val(profile_select);
        jQuery('#b2s-post-curation-profile-select').trigger('change');
        if (typeof twitter_select != "undefined" && twitter_select != "" && twitter_select != null && twitter_select > 0) {
            jQuery('#b2s-post-curation-twitter-select').val(twitter_select);
            jQuery('#b2s-post-curation-twitter-select').trigger('change');
        }
    }
}

jQuery(document).on('click', '.b2sTextPostInfoModalBtn', function() {
    jQuery('#b2sTextPostInfoModal').modal('show');
});