jQuery.noConflict();

if (typeof wp.heartbeat !== "undefined") {
    jQuery(document).on('heartbeat-send', function (e, data) {
        data['b2s_heartbeat'] = 'b2s_listener';
        data['b2s_heartbeat_action'] = 'b2s_video_upload';
    });
    wp.heartbeat.connectNow();
}

jQuery(window).on("load", function () {
    jQuery('#b2sPagination').val("1");
    /*video upload list*/
    b2sSortFormSubmit();
    /*video url curation*/
    activateVideo();

    jQuery(".b2s-progress-bar").loading();
    if (jQuery('#b2sUserCanUseVideoAddon').val() == '0' || jQuery('#b2sUserCanUseVideoAddon').val() == '') {
        jQuery('.b2s-video-upload-file-container').css('opacity', '0.2');
        jQuery('.b2s-video-upload-file-container :input').prop('disabled', 'disabled');
        jQuery('.b2s-video-upload-file-container').find("a").attr('disabled', 'disabled');
    }
});


/*general*/
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


/*Video url curation*/
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


jQuery(document).on('click', '.b2s-video-upload-feedback-btn', function () {
    jQuery('.b2s-video-upload-feedback-modal').modal('show');
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
        //MaxSchedDate
        var maxDate = new Date(jQuery('#b2sMaxSchedDate').val());
        jQuery('#b2s-post-curation-ship-date').b2sdatepicker({'autoClose': true, 'toggleSelected': false, 'minutesStep': 15, 'minDate': new Date(), 'maxDate': maxDate, 'startDate': today, 'todayButton': new Date(), 'position': 'top left'});

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
                if (data.error == 'nonce') {
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
    if (jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if (jQuery('#b2s-curation-post-format').val() == '1') {
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

    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
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
                if (jQuery('#b2s-curation-post-format').val() == '0') {
                    jQuery('.b2s-curation-preview-area').show();
                } else if (jQuery('#b2s-curation-post-format').val() == '1') {
                    jQuery('.b2s-curation-image-area').show();
                } else {
                    jQuery('.b2s-curation-text-area').show();
                }

                if (data.error == 'NO_AUTH') {
                    jQuery('#b2s-curation-no-auth-info').show();
                } else if (data.error == 'nonce') {
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
    if (jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if (jQuery('#b2s-curation-post-format').val() == '1') {
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

    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                window.location.href = data.redirect;
                return false;
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-loading-area').hide();
                jQuery('#b2s-curation-no-data-info').show();
                jQuery('.b2s-curation-settings-area').show();
                jQuery('.b2s-curation-preview-area').show();
                if (jQuery('#b2s-curation-post-format').val() == '0') {
                    jQuery('.b2s-curation-link-area').show();
                } else if (jQuery('#b2s-curation-post-format').val() == '1') {
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

jQuery(document).on('click', '#b2s-btn-curation-draft', function () {
    jQuery('#b2s-curation-no-data-info').hide();
    jQuery('#b2s-curation-no-auth-info').hide();
    jQuery('#b2s-curation-saved-draft-info').hide();
    var noContent = false;
    if (jQuery('#b2s-curation-post-format').val() == '0') {
        if (jQuery('#b2s-post-curation-preview-title').val().length === 0) {
            jQuery('#b2s-post-curation-preview-title').addClass('error');
            noContent = true;
        }
        if (jQuery('#b2s-post-curation-comment').val().length === 0) {
            jQuery('#b2s-post-curation-comment').addClass('error');
            noContent = true;
        }
    } else if (jQuery('#b2s-curation-post-format').val() == '1') {
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
    jQuery.ajax({
        processData: false,
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery("#b2s-curation-post-form").serialize() + '&postFormat=' + jQuery('#b2s-curation-post-format').val() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
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
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-curation-settings-area').show();
            if (jQuery('#b2s-curation-post-format').val() == '0') {
                jQuery('.b2s-curation-preview-area').show();
            } else if (jQuery('#b2s-curation-post-format').val() == '1') {
                jQuery('.b2s-curation-image-area').show();
            } else {
                jQuery('.b2s-curation-image-area').show();
            }
        }
    });
    return false;
});

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
    if (jQuery('.b2s-curation-input-area').is(':visible')) {
        jQuery('.b2s-curation-settings-area').hide();
    } else {
        jQuery('.b2s-curation-settings-area').show();
    }
    jQuery('.b2s-curation-input-area-info-header-text').hide();
    jQuery('.b2s-curation-input-area-info-header-text-video').show();
}

jQuery(document).on('change', '#b2s-post-curation-comment', function () {
    jQuery('#b2s-post-curation-comment-dummy').val(jQuery('#b2s-post-curation-comment').val());
});
jQuery(document).on('change', '#b2s-post-curation-comment-image', function () {
    jQuery('#b2s-post-curation-comment-dummy').val(jQuery('#b2s-post-curation-comment-image').val());
});
jQuery(document).on('change', '#b2s-post-curation-comment-text', function () {
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
picker.on('emoji', function (emoji) {
    if (jQuery('#b2s-curation-post-format').val() == '0') {
        var text = jQuery('#b2s-post-curation-comment').val();
        var start = jQuery('#b2s-post-curation-comment').attr('selectionStart');
        var end = jQuery('#b2s-post-curation-comment').attr('selectionEnd');
    } else {
        var text = jQuery('#b2s-post-curation-comment-image').val();
        var start = jQuery('#b2s-post-curation-comment-image').attr('selectionStart');
        var end = jQuery('#b2s-post-curation-comment-image').attr('selectionEnd');
    }
    if (typeof start == 'undefined' || typeof end == 'undefined') {
        start = text.length;
        end = text.length;
    }
    var newText = text.slice(0, start) + emoji + text.slice(end);
    jQuery('.b2s-post-item-details-item-message-input').val(newText);
    jQuery('.b2s-post-item-details-item-message-input').focus();
    jQuery('.b2s-post-item-details-item-message-input').prop("selectionStart", parseInt(start) + emoji.length);
    jQuery('.b2s-post-item-details-item-message-input').prop("selectionEnd", parseInt(start) + emoji.length);
    jQuery('.b2s-post-item-details-item-message-input').trigger('keyup');
});

jQuery(document).on('click', '.b2s-post-item-details-item-message-emoji-btn', function () {
    if (picker.pickerVisible) {
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

jQuery(document).on('click', '.b2s-post-item-details-url-image', function () {
    jQuery('.b2s-select-image-modal-open').trigger('click');
});

jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
    jQuery('#b2s-network-select-image').modal('show');
    return false;
});

jQuery(document).on('click', '.b2s-network-info-modal-btn', function () {
    if (jQuery('#b2s-curation-post-format').val() == "2") {
        jQuery('#b2sTextPostInfoModal').modal('show');
    } else {
        jQuery('#b2sInfoNetworkModal').modal('show');
    }
    return false;
});

jQuery(document).on('keyup', '.b2s-post-item-details-item-message-input', function () {
    jQuery(this).removeClass('error');
});

jQuery(document).on('click', '.b2s-curation-info-premium-btn', function () {
    if (jQuery(this).data('type') == 'text') {
        jQuery('#b2s-modal-header-text').show();
        jQuery('#b2s-modal-header-image').hide();
    } else {
        jQuery('#b2s-modal-header-image').show();
        jQuery('#b2s-modal-header-text').hide();
    }
    jQuery('#b2sInfoCCModal').modal('show');
});

jQuery(document).on('click', '.b2s-re-share-btn', function () {
    jQuery('.b2s-curation-post-list-area').hide();
    jQuery('.b2s-curation-settings-area').show();
    if (jQuery('#b2s-curation-post-format').val() == '0') {
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

/*Video upload to media libray*/

jQuery(document).on('click', '.b2sDetailsPublishPostTriggerLink', function () {
    jQuery(this).parent().prev().find('button').trigger('click');
    return false;
});

function b2sSortFormSubmit() {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('.b2s-sort-result-area').hide();
    jQuery('.b2s-sort-result-item-area').html("").hide();
    jQuery('.b2s-sort-pagination-content').html("");
    jQuery('.b2s-sort-pagination-area').hide();

    var currentType = jQuery('#b2sType').val();
    if (currentType != "undefined") {
        jQuery('.b2s-post-btn').removeClass('btn-primary').addClass('btn-link');
        jQuery('.b2s-post-' + currentType).removeClass('btn-link').addClass('btn-primary');
    }

    var data = {
        'action': 'b2s_sort_data',
        'b2sSortPostTitle': jQuery('#b2sSortPostTitle').val(),
        'b2sSortPostAuthor': jQuery('#b2sSortPostAuthor').val(),
        'b2sType': jQuery('#b2sType').val(),
        'b2sPagination': jQuery('#b2sPagination').val(),
        'b2sShowPagination': jQuery('#b2sShowPagination').length > 0 ? jQuery('#b2sShowPagination').val() : 1,
        'b2sUserLang': jQuery('#b2sUserLang').val(),
        'b2sPostsPerPage': jQuery('#b2sPostsPerPage').val(),
        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
    };

    if (jQuery('#b2sPostsPerPage').length > 0) {
        data['b2sPostsPerPage'] = jQuery('#b2sPostsPerPage').val();
    }

    var legacyMode = true;
    if (jQuery('#isLegacyMode').val() !== undefined) {
        if (jQuery('#isLegacyMode').val() == "1") {
            legacyMode = false; // loading is sync (stack)
        }
    }
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        async: legacyMode,
        cache: false,
        data: data,
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (typeof data === 'undefined' || data === null) {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            }
            if (data.result == true) {
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-sort-result-area').show();
                jQuery('.b2s-sort-result-item-area').html(data.content).show();
                if (data.pagination != '') {
                    jQuery('.b2s-sort-pagination-content').html(data.pagination);
                    jQuery('.b2s-sort-pagination-area').show();
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
                return false;
            }
        }
    });
}

jQuery(document).on('click', '.b2s-post-per-page', function () {
    jQuery('#b2sPostsPerPage').val(jQuery(this).data('post-per-page'));
    jQuery('.b2s-post-per-page').addClass('btn-default').removeClass('btn-primary');
    jQuery(this).removeClass('btn-default').addClass('btn-primary');
    jQuery('#b2s-sort-submit-btn').trigger('click');
});

jQuery(document).on('click', '#b2s-sort-reset-btn', function () {
    jQuery('#b2sPagination').val("1");
    jQuery('#b2sSortPostTitle').val("");
    jQuery('#b2sSortPostAuthor').prop('selectedIndex', 0);
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-sort-submit-btn', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('keypress', '#b2sSortPostTitle', function (event) {
    if (event.keyCode == 13) {  //Hide Enter
        return false;
    }
});
jQuery(document).on('click', '.b2s-pagination-btn', function () {
    jQuery('#b2sPagination').val(jQuery(this).attr('data-page'));
    b2sSortFormSubmit();
    return false;
});

//Upload Area
// preventing page from redirecting
jQuery(document).on('dragover', 'html', function (e) {
    e.preventDefault();
    e.stopPropagation();
});

jQuery(document).on('drop', 'html', function (e) {
    e.preventDefault();
    e.stopPropagation();
});
// Drag enter
jQuery(document).on('dragenter', '.b2s-video-upload-file-area', function (e) {
    e.stopPropagation();
    e.preventDefault();
});
// Drag over
jQuery(document).on('dragover', '.b2s-video-upload-file-area', function (e) {
    e.stopPropagation();
    e.preventDefault();
});
// Drop
jQuery(document).on('drop', '.b2s-video-upload-file-area', function (e) {
    e.stopPropagation();
    e.preventDefault();
    var file = e.originalEvent.dataTransfer.files;
    jQuery('.b2s-video-upload-error').hide();
    jQuery('.b2s-video-upload-success').hide();
    var fd = new FormData();
    jQuery('.b2s-video-upload-file-name').html(file[0].name);
    jQuery('.b2s-video-upload-file-area').hide();
    jQuery('.b2s-video-upload-progress-area').show();
    fd.append('file', file[0]);
    fd.append('b2s_security_nonce', jQuery('#b2s_security_nonce').val());
    fd.append('action', 'b2s_upload_video');
    uploadVideo(fd);
});
// Open file selector on div click
jQuery(document).on('click', '#b2s-video-upload-file-area', function () {
    jQuery("#b2s-video-upload-file").click();
});
// file selected
jQuery(document).on('change', '#b2s-video-upload-file', function () {
    jQuery('.b2s-video-upload-error').hide();
    jQuery('.b2s-video-upload-success').hide();
    var fd = new FormData();
    var file = jQuery('#b2s-video-upload-file')[0].files[0];
    fd.append('action', 'b2s_upload_video');
    fd.append('file', file);
    fd.append('b2s_security_nonce', jQuery('#b2s_security_nonce').val());
    jQuery('.b2s-video-upload-file-name').html(file.name);
    jQuery('.b2s-video-upload-file-area').hide();
    jQuery('.b2s-video-upload-progress-area').show();
    uploadVideo(fd);
});


function uploadVideo(formdata) {
    jQuery.ajax({
        url: ajaxurl,
        type: 'post',
        data: formdata,
        contentType: false,
        processData: false,
        dataType: 'json',
        xhr: function () {
            var xhr = new window.XMLHttpRequest();
            xhr.upload.addEventListener("progress", function (evt) {
                if (evt.lengthComputable) {
                    var percentComplete = evt.loaded / evt.total;
                    percentComplete = parseInt(percentComplete * 100);
                    jQuery('.progress-bar').css('width', percentComplete + '%');
                }
            }, false);
            return xhr;
        },
        success: function (response) {
            jQuery('.b2s-video-upload-file-area').show();
            jQuery('.b2s-video-upload-progress-area').hide();
            if (response.result == false) {
                if (response.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('#b2s-video-upload-error-' + response.error.replace('_', '-')).show();
                }
            } else {
                jQuery('#b2s-video-upload-success').show();
                jQuery('#b2s-sort-result-item-area li.b2s-video-upload-list-last-trigger').removeClass('b2s-video-upload-list-last-trigger');
                jQuery('.b2s-video-upload-list-empty').hide();
                jQuery('.b2s-sort-result-item-area').prepend(response.videoItem);
            }
        }
    });
}

jQuery(document).on('click', '.b2s-show-video-uploads', function () {
    var attachment_id = jQuery(this).data('attachment-id');
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_get_video_upload_data',
                'attachment_id': attachment_id,
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-video-upload-area[data-attachment-id="' + data.attachment_id + '"]').html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
                wp.heartbeat.connectNow();
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-video-upload-area[data-attachment-id="' + attachment_id + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-video-upload-area[data-attachment-id="' + attachment_id + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }
});


jQuery(document).on('click', '.checkbox-all', function () {
    if (jQuery('.checkbox-all').is(":checked")) {
        jQuery('.checkboxes[data-attachment-id="' + jQuery(this).attr('data-attachment-id') + '"]').prop("checked", true);
    } else {
        jQuery('.checkboxes[data-attachment-id="' + jQuery('.checkbox-all').attr('data-attachment-id') + '"]').prop("checked", false);
    }
});

jQuery(document).on('click', '.checkbox-post-video-upload-all-btn', function () {
    var checkboxes = jQuery('.checkboxes[data-attachment-id="' + jQuery(this).attr('data-attachment-id') + '"]:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-attachment-id').val(items.join());
        jQuery('#b2s-delete-confirm-attachment-count').html(items.length);
        jQuery('.b2s-delete-video-upload-modal').modal('show');
        jQuery('.b2s-video-upload-delete-confirm-btn').prop('disabeld', false);
    }
});

jQuery(document).on('click', '.b2s-post-video-upload-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-attachment-id').val(jQuery(this).attr('data-attachment-id'));
    jQuery('#b2s-delete-confirm-attachment-count').html('1');
    jQuery('.b2s-delete-video-upload-modal').modal('show');
    jQuery('.b2s-video-upload-delete-confirm-btn').prop('disabeld', false);
});

jQuery(document).on('click', '.b2s-video-upload-delete-confirm-btn', function () {
    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-video-upload-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_publish_post',
            'postId': jQuery('#b2s-delete-confirm-attachment-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-video-upload-modal').modal('hide');
            if (data.result == true) {
                jQuery.each(data.postId, function (i, id) {
                    jQuery('.b2s-post-video-upload-area-li[data-attachment-id="' + id + '"]').remove();
                });
                jQuery('.b2s-post-remove-success').show();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            wp.heartbeat.connectNow();
            return true;
        }
    });
});


jQuery(document).on('click', '.b2s-video-upload-btn-trial', function () {
    jQuery('.b2s-video-upload-activate-trial-error').hide();
    var $btn = jQuery(this);
    $btn.button('loading');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_activate_addon_trial',
            'type': 'video',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                location.reload(true);
            } else {
                $btn.button('reset');
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    if (data.error == 'has-trial') {
                        $btn.prop('disabled', 'disabled');
                    }
                    jQuery('#b2s-video-upload-error-trial-' + data.error).show();
                }
            }
            return true;
        }
    });
});
