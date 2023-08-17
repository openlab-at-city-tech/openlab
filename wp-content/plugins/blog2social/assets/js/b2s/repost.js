jQuery(document).on('heartbeat-send', function (e, data) {
    data['b2s_heartbeat'] = 'b2s_listener';
    data['b2s_heartbeat_action'] = 'b2s_repost';
});

jQuery.noConflict();
jQuery(window).on("load", function () {

    jQuery(".b2s-re-post-type").chosen();
    jQuery(".b2s-re-post-categories").chosen();
    jQuery(".b2s-re-post-author").chosen();

    var dateFormat = "yyyy-mm-dd";
    var language = "en";
    if (jQuery('#b2sUserLang').val() == "de") {
        dateFormat = "dd.mm.yyyy";
        language = "de";
    }
    jQuery(".b2s-re-post-date-start").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        calendarWeeks: true,
        autoclose: true
    });

    jQuery(".b2s-re-post-date-end").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        calendarWeeks: true,
        autoclose: true
    });

    var showMeridian = true;
    if (jQuery('#b2sUserLang').val() == "de") {
        dateFormat = "dd.mm.yyyy";
        language = "de";
    }
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
        showMeridian = false;
    }
    jQuery('.b2s-re-post-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        snapToStep: true
    });

    jQuery('.b2s-re-post-queue-count').html(jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length);
    if (jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length == 0) {
        jQuery('.b2s-re-post-queue-delete-area').hide();
    }
    jQuery('.b2s-re-post-settings-option').trigger('change');
    jQuery('#b2s-re-post-profil-dropdown').trigger('change');

    jQuery('.b2s-re-post-delete-checked').hide();

    if (jQuery("#b2sUserVersion").val() == 0) {
        jQuery('#b2s-re-post-settings :input').prop('disabled', 'disabled');
    }

});

var curSource = new Array();
curSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=all&filter_status=5&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
var newSource = new Array();

jQuery(document).ready(function () {
    renderCalender();
    jQuery(".b2s-loading-area").hide();
});

jQuery(document).on('click', '.b2s-re-post-settings-header', function () {
    if (jQuery('.b2s-re-post-settings-area').is(':visible')) {
        jQuery('.b2s-re-post-settings-area').hide();
        jQuery('.b2s-re-post-settings-toggle').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    } else {
        jQuery('.b2s-re-post-settings-area').show();
        jQuery('.b2s-re-post-settings-toggle').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    }
});

jQuery(document).on('change', '#b2s-re-post-best-times-active', function () {
    if (jQuery(this).is(':checked')) {
        jQuery('.b2s-re-post-input-time').prop('disabled', true);
    } else {
        jQuery('.b2s-re-post-input-time').prop('disabled', false);
    }
});

jQuery(document).on('change', '.b2s-re-post-settings-option', function () {
    if (jQuery('.b2s-re-post-settings-option:checked').val() == 1) {
        jQuery('.b2s-re-post-settings-customize-area input').prop('disabled', false);
        jQuery(".b2s-re-post-type").prop('disabled', false).trigger("chosen:updated");
        jQuery(".b2s-re-post-categories").prop('disabled', false).trigger("chosen:updated");
        jQuery(".b2s-re-post-author").prop('disabled', false).trigger("chosen:updated");
    } else {
        jQuery('.b2s-re-post-settings-customize-area input').prop('disabled', true);
        jQuery(".b2s-re-post-type").prop('disabled', true).trigger("chosen:updated");
        jQuery(".b2s-re-post-categories").prop('disabled', true).trigger("chosen:updated");
        jQuery(".b2s-re-post-author").prop('disabled', true).trigger("chosen:updated");
    }
});

jQuery(document).on('click', '.b2s-re-post-submit-btn', function () {
    if (jQuery('.b2s-re-post-date-active').is(':checked') && (jQuery('.b2s-re-post-date-start').val() == "" || jQuery('.b2s-re-post-date-end').val() == "") && !(jQuery('.b2s-re-post-date-start').val() == "" && jQuery('.b2s-re-post-date-end').val() == "")) {
        if (jQuery('.b2s-re-post-date-start').val() == "") {
            jQuery('.b2s-re-post-date-start').addClass('error');
        }
        if (jQuery('.b2s-re-post-date-end').val() == "") {
            jQuery('.b2s-re-post-date-end').addClass('error');
        }
        return false;
    }
    jQuery('.b2s-repost-options-area').hide();
    jQuery('.b2s-repost-queue-area').hide();
    jQuery('.b2s-re-post-no-content').hide();
    jQuery('.b2s-re-post-content-in-queue').hide();
    jQuery('.b2s-re-post-limit-error').hide();
    jQuery('.b2s-loading-area').show();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery('#b2s-re-post-settings').serialize() + '&b2s-re-post-queue-count=' + jQuery('.b2s-re-post-queue-count').html() + '&action=b2s_re_post_submit&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        error: function () {
            jQuery('.b2s-repost-options-area').show();
            jQuery('.b2s-repost-queue-area').show();
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-repost-options-area').show();
            jQuery('.b2s-repost-queue-area').show();
            jQuery('.b2s-loading-area').hide();
            if (data.result == true) {
                if (data.queue != "") {
                    jQuery('.b2s-repost-queue-area').html(data.queue);
                    jQuery('.b2s-re-post-queue-count').html(jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length);
                    if (jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length == 0) {
                        jQuery('.b2s-re-post-queue-delete-area').hide();
                    }
                }
                renderCalender();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                    return false;
                }
                if (data.error == 'no_content') {
                    jQuery('.b2s-re-post-no-content').show();
                    return false;
                }
                if (data.error == 'content_in_queue') {
                    jQuery('.b2s-re-post-content-in-queue').show();
                    return false;
                }
                if (data.error == 'limit') {
                    jQuery('.b2s-re-post-limit-error').show();
                    return false;
                }
                jQuery('.b2s-server-connection-fail').show();
            }
        }
    });
});

jQuery(document).on('click', '.b2s-re-post-select-all', function () {
    jQuery('.b2s-re-post-queue-checkbox').prop('checked', true);
    jQuery('.b2s-re-post-queue-checkbox').trigger('change');
});

jQuery(document).on('change', '.b2s-re-post-queue-checkbox', function () {
    if (jQuery('.b2s-re-post-queue-checkbox:checked').length) {
        jQuery('.b2s-re-post-delete-checked').show();
    } else {
        jQuery('.b2s-re-post-delete-checked').hide();
    }
});

jQuery(document).on('change', '.b2s-re-post-limit', function () {
    if (jQuery(this).children("option:selected").data('limit') == 0) {
        jQuery(this).children('option:selected').prop('selected', false);
        jQuery(this).children('option[data-limit="1"]:last').prop('selected', 'selected');
        jQuery('.b2s-re-post-limit-info').show();
    } else {
        jQuery('.b2s-re-post-limit-info').hide();
    }
});

jQuery(document).on('change', '#b2s-re-post-profil-dropdown', function () {
    jQuery('.b2s-re-post-error[data-error-reason="no-auth-in-mandant"]').hide();
    var tos = false;
    if (jQuery('#b2s-re-post-profil-data-' + jQuery(this).val()).val() == "") {
        tos = true;
    } else {
        //TOS Twitter Check
        var len = jQuery('#b2s-re-post-profil-dropdown-twitter').children('option[data-mandant-id="' + jQuery(this).val() + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-re-post-twitter-profile').show();
            jQuery('#b2s-re-post-profil-dropdown-twitter').prop('disabled', false);
            jQuery('#b2s-re-post-profil-dropdown-twitter').show();
            jQuery('#b2s-re-post-profil-dropdown-twitter option').attr("disabled", "disabled");
            jQuery('#b2s-re-post-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]').attr("disabled", false);
            jQuery('#b2s-re-post-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]:first').attr("selected", "selected");
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-re-post-twitter-profile').hide();
        jQuery('#b2s-re-post-profil-dropdown-twitter').prop('disabled', 'disabled');
        jQuery('#b2s-re-post-profil-dropdown-twitter').hide();
    }
});

jQuery(document).on('click', '.b2s-re-post-delete-checked', function () {
    var checkboxes = jQuery('.b2s-re-post-queue-checkbox:checked');
    if (checkboxes.length > 0) {
        var items = [];
        jQuery(checkboxes).each(function (i, selected) {
            items[i] = jQuery(selected).val();
        });
        jQuery('#b2s-delete-confirm-post-id').val(items.join());
        jQuery('#b2s-delete-confirm-post-count').html(items.length);
        jQuery('.b2s-delete-sched-modal').modal('show');
        jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', true);
        jQuery('.b2s-sched-delete-confirm-btn').hide();
        jQuery('.b2s-sched-delete-confirm-multi-btn').prop('disabeld', false);
        jQuery('.b2s-sched-delete-confirm-multi-btn').show();
    }
});

jQuery(document).on('click', '.b2sDetailsSchedPostTriggerLink', function () {
    jQuery('.b2sDetailsSchedPostBtn[data-post-id="' + jQuery(this).data('post-id') + '"]').trigger('click');
    return false;
});

jQuery(document).on('click', '.b2sDetailsSchedPostBtn', function () {
    var postId = jQuery(this).attr('data-post-id');
    if (!jQuery(this).find('i').hasClass('isload')) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_sched_post_data',
                'postId': postId,
                'type': 'repost',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery('.b2s-post-sched-area[data-post-id="' + data.postId + '"]').html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }
        });
        jQuery(this).find('i').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up').addClass('isload').addClass('isShow');
    } else {
        if (jQuery(this).find('i').hasClass('isShow')) {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').hide();
            jQuery(this).find('i').removeClass('isShow').addClass('isHide').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
        } else {
            jQuery('.b2s-post-sched-area[data-post-id="' + postId + '"]').show();
            jQuery(this).find('i').removeClass('isHide').addClass('isShow').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
        }
    }

});

jQuery(document).on('click', '.b2s-post-sched-area-drop-btn', function () {
    jQuery('#b2s-delete-confirm-post-id').val(jQuery(this).attr('data-post-id'));
    jQuery('#b2s-delete-confirm-post-count').html('1');
    jQuery('.b2s-delete-sched-modal').modal('show');
    jQuery('.b2s-sched-delete-confirm-multi-btn').prop('disabeld', true);
    jQuery('.b2s-sched-delete-confirm-multi-btn').hide();
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', false);
    jQuery('.b2s-sched-delete-confirm-btn').show();
    return false;
});

jQuery(document).on('click', '.b2s-sched-delete-confirm-multi-btn', function () {
    jQuery('.b2s-delete-sched-modal').modal('hide');
    jQuery('.b2s-repost-options-area').hide();
    jQuery('.b2s-repost-queue-area').hide();
    jQuery('.b2s-loading-area').show();

    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_re_post_sched',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-repost-options-area').show();
            jQuery('.b2s-repost-queue-area').show();
            jQuery('.b2s-loading-area').hide();
            if (data.result == true) {
                data.postIds.forEach(function (postId) {
                    jQuery('.b2s-re-post-queue-checkbox[data-blog-post-id="' + postId + '"]').closest('li').remove();
                });
                jQuery('.b2s-re-post-queue-count').html(jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length);
                if (jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length == 0) {
                    jQuery('.b2s-re-post-queue-delete-area').hide();
                }
                jQuery('.b2s-re-post-queue-checkbox').trigger('change');
                renderCalender();
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

jQuery(document).on('click', '.b2s-sched-delete-confirm-btn', function () {
    jQuery('.b2s-delete-sched-modal').modal('hide');
    jQuery('.b2s-repost-options-area').hide();
    jQuery('.b2s-repost-queue-area').hide();
    jQuery('.b2s-loading-area').show();

    jQuery('.b2s-post-remove-fail').hide();
    jQuery('.b2s-post-remove-success').hide();
    jQuery('.b2s-sched-delete-confirm-btn').prop('disabeld', true);
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_sched_post',
            'postId': jQuery('#b2s-delete-confirm-post-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-repost-options-area').show();
            jQuery('.b2s-repost-queue-area').show();
            jQuery('.b2s-loading-area').hide();
            if (data.result == true) {
                var count = parseInt(jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html());
                var newCount = count - data.postCount;
                jQuery('.b2s-sched-count[data-post-id="' + data.blogPostId + '"]').html(newCount);
                if (newCount >= 1) {
                    jQuery.each(data.postId, function (i, id) {
                        jQuery('.b2s-post-sched-area-li[data-post-id="' + id + '"]').remove();
                    });
                } else {
                    jQuery('.b2s-post-sched-area-li[data-post-id="' + data.postId[0] + '"]').closest('ul').closest('li').remove();
                    jQuery('.b2s-re-post-queue-count').html(jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length);
                    if (jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length == 0) {
                        jQuery('.b2s-re-post-queue-delete-area').hide();
                    }
                }
                jQuery('.b2s-post-remove-success').show();
                renderCalender();
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



jQuery(document).on('click', '.b2s-post-edit-sched-btn', function () {
    showEditSchedPost(jQuery(this).attr('data-b2s-id'), jQuery(this).attr('data-post-id'), jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-type'), jQuery(this).attr('data-network-id'), jQuery(this).attr('data-relay-primary-post-id'));

});

//Customize 
function showEditSchedPost(b2s_id, post_id, network_auth_id, network_type, network_id, relay_primary_post_id) {
    if (jQuery('#b2s-edit-event-modal-' + b2s_id).length == 1)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).remove();
    }
    jQuery("#b2sPostId").val(post_id);
    var $modal = jQuery("<div>");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_post_edit_modal',
            'id': b2s_id,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                $modal = $modal.html(data);
            }
        }
    });
    b2s_current_post_id = post_id;
    jQuery("body").append($modal);
    jQuery(".b2s-edit-post-delete").hide();
    jQuery('#b2sUserTimeZone').val(jQuery('#user_timezone').val());
    jQuery('#b2s-edit-event-modal-' + b2s_id).modal('show');
    var post_format = jQuery('#b2sCurrentPostFormat').val();
    activatePortal(network_auth_id);
    initSceditor(network_auth_id);
    if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').length > 0) {
        var postFormatText = b2s_post_formats;
        var isSetPostFormat = false;
        var postFormatType = jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').attr('data-post-format-type');
        //is set post format => override current condidtions by user settings for this post
        if (post_format !== null) {
            jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val(post_format);
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][post_format]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(post_format);
            //edit modal select post format
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').removeClass('b2s-settings-checked');
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"][data-post-format="' + post_format + '"]').addClass('b2s-settings-checked');
        } else {
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val()]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val());
        }

        //if linkpost then show btn meta tags
        var isMetaChecked = false;
        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
        if (typeof network_id != 'undefined' && jQuery.inArray(network_id.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if ((network_id == "2" || network_id == "24") && jQuery('#isCardMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val() == "0") {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').show();
            //jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').trigger("click");
            var dataMetaType = jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').attr("data-meta-type");
            if (dataMetaType == "og") {
                jQuery('.b2sChangeOgMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            } else {
                jQuery('.b2sChangeCardMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            }
        } else {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').hide();
        }

        //Content Curation
        if (jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + network_auth_id + '"]').attr('data-post-wp-type') == 'ex') {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + network_auth_id + '"]').attr("style", "display:none !important");
            if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val() == 0) {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').hide();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').hide();
            } else {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').show();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').show();
            }
        }
    }
    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + network_auth_id + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(network_auth_id, network_id, textLimit);
    } else {
        networkCount(network_auth_id);
    }
    var today = new Date();
    var dateFormat = "yyyy-mm-dd";
    var language = "en";
    var showMeridian = true;
    if (jQuery('#b2sUserLang').val() == "de") {
        dateFormat = "dd.mm.yyyy";
        language = "de";
        showMeridian = false;
    }

    jQuery(".b2s-post-item-details-release-input-date").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        startDate: today,
        calendarWeeks: true,
        autoclose: true
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current',
        snapToStep: true
    });
    jQuery(".b2s-post-item-details-release-input-date").datepicker().on('changeDate', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker().on('changeTime.timepicker', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    init();

    //is relay post?
    if (relay_primary_post_id > 0) {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-input-hidden') && !jQuery(this).hasClass('b2s-modal-close') && !jQuery(this).hasClass('b2s-post-item-details-relay-input-delay') && !jQuery(this).hasClass('b2s-edit-post-delete') && !jQuery(this).hasClass('b2s-edit-post-save-this')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }

    if (!b2s_has_premium)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-modal-close')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }
}

jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
    jQuery('.b2s-network-select-image-content').html("");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_image_modal',
            'id': jQuery(this).data('post-id'),
            'image_url': jQuery(this).data('image-url'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                jQuery(".b2s-network-select-image-content").html(data);
            }
        }
    });
    var authId = jQuery(this).data('network-auth-id');
    jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId);
    jQuery('.b2s-upload-image').attr('data-network-auth-id', authId);
    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('#b2sInsertImageType').val("0");
    jQuery('.networkImage').each(function () {
        var width = this.naturalWidth;
        var height = this.naturalHeight;
        jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
    });
    jQuery('#b2s-network-select-image').modal('show');
    return false;
});

jQuery(document).on("click", ".b2s-edit-post-save-this", function (e) {
    e.preventDefault();
    jQuery('#save_method').val("apply-this");
    var id = jQuery(this).data("b2s-id");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery(this).closest("form").serialize() + '&sched_type=5' + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            jQuery('#b2s-edit-event-modal-' + id).modal('hide');
            jQuery('#b2s-edit-event-modal-' + id).remove();
            jQuery('body').removeClass('modal-open');
            jQuery('body').removeAttr('style');
            if (data.date != "") {
                jQuery('.b2s-post-sched-area-sched-time[data-post-id="' + id + '"]').html(data.date);
            }
            jQuery('.b2s-post-edit-success').show();
            wp.heartbeat.connectNow();
        }
    });
});
jQuery(document).on("click", ".release_locks", function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_calendar_release_locks',
            'post_id': jQuery('#post_id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            wp.heartbeat.connectNow();
        }
    });
});

function showFilter(typ) {
    if (typ == 'show') {
        jQuery('.filterShow').hide();
        jQuery('.form-inline').show();
        jQuery('.filterHide').show();
    } else {
        jQuery('.filterShow').show();
        jQuery('.form-inline').hide();
        jQuery('.filterHide').hide();
    }
}

function padDate(n) {
    return ("0" + n).slice(-2);
}


function checkSchedDateTime() {
    var dateElement = '#b2s-change-date';
    var timeElement = '#b2s-change-time';
    var dateStr = jQuery(dateElement).val();
    var minStr = jQuery(timeElement).val();
    var timeZone = parseInt(jQuery('#user_timezone').val()) * (-1);

    if (jQuery('#b2sUserLang').val() == 'de') {
        dateStr = dateStr.substring(6, 10) + '-' + dateStr.substring(3, 5) + '-' + dateStr.substring(0, 2);
    } else {
        var minParts = minStr.split(' ');
        var minParts2 = minParts[0].split(':');
        if (minParts[1] == 'PM') {
            minParts2[0] = parseInt(minParts2[0]) + 12;
        }
        minStr = minParts2[0] + ':' + minParts2[1];
    }

    var minParts3 = minStr.split(':');
    if (minParts3[0] < 10) {
        minParts3[0] = '0' + minParts3[0];
    }
    var dateParts = dateStr.split('-');

    //utc current time
    var now = new Date();
    //offset between utc und user
    var offset = (parseInt(now.getTimezoneOffset() / 60)) * (-1);
    //enter hour to user time
    var hour = parseInt(minParts3[0]) + timeZone + offset;
    //calculate datetime in utc
    var enter = new Date(dateParts[0], dateParts[1] - 1, dateParts[2], hour, minParts3[1]);
    //compare enter date time with allowed user time
    if (enter.getTime() < now.getTime()) {
        //enter set on next 15 minutes and calculate on user timezone
        enter.setTime(now.getTime() + (900000 - (now.getTime() % 900000)) - (3600000 * (timeZone + offset)));
        jQuery(dateElement).datepicker('update', enter);
        jQuery(timeElement).timepicker('setTime', enter);
    }
}

jQuery(document).on('click', '.b2s-sched-calendar-btn', function () {
    if (jQuery('#b2s-sched-calendar-area').is(":visible")) {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-show-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').hide();
    } else {
        jQuery('#b2s-sched-calendar-btn-text').text(jQuery(this).attr('data-hide-calendar-btn-title'));
        jQuery('#b2s-sched-calendar-area').show();
    }
});

//Overlay second modal
jQuery('#b2s-network-select-image').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
//Overlay second modal
jQuery('#b2s-post-ship-item-post-format-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2s-info-change-meta-tag-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

//Modal Edit Post close
jQuery(document).on('click', '.b2s-modal-close-edit-post', function (e) {
    jQuery(jQuery(this).attr('data-modal-name')).remove();
    return false;
});

jQuery(document).on('click', '.b2sTwitterInfoModalBtn', function () {
    jQuery('#b2sTwitterInfoModal').modal('show');
});

jQuery(document).on('click', '.b2s-re-post-submit-premium', function () {
    jQuery('#b2sInfoRePosterModal').modal('show');
});

jQuery(document).on('change', '.b2s-re-post-type', function () {
    if (jQuery(this).val() == null) {
        jQuery('.b2s-re-post-type-active').prop('checked', false);
    } else {
        jQuery('.b2s-re-post-type-active').prop('checked', true);
    }
});

jQuery(document).on('change', '.b2s-re-post-categories', function () {
    if (jQuery(this).val() == null) {
        jQuery('.b2s-re-post-categories-active').prop('checked', false);
    } else {
        jQuery('.b2s-re-post-categories-active').prop('checked', true);
    }
});

jQuery(document).on('change', '.b2s-re-post-author', function () {
    if (jQuery(this).val() == null) {
        jQuery('.b2s-re-post-author-active').prop('checked', false);
    } else {
        jQuery('.b2s-re-post-author-active').prop('checked', true);
    }
});

jQuery(document).on('change', '.b2s-re-post-date-start', function () {
    if (jQuery('.b2s-re-post-date-start').val() == "" && jQuery('.b2s-re-post-date-end').val() == "") {
        jQuery('.b2s-re-post-date-active').prop('checked', false);
    } else {
        jQuery('.b2s-re-post-date-active').prop('checked', true);
    }
    jQuery('.b2s-re-post-date-start').removeClass('error');
});

jQuery(document).on('change', '.b2s-re-post-date-end', function () {
    if (jQuery('.b2s-re-post-date-start').val() == "" && jQuery('.b2s-re-post-date-end').val() == "") {
        jQuery('.b2s-re-post-date-active').prop('checked', false);
    } else {
        jQuery('.b2s-re-post-date-active').prop('checked', true);
    }
    jQuery('.b2s-re-post-date-end').removeClass('error');
});

jQuery(document).on('click', '.b2s-network-info-modal-btn', function () {
    jQuery('#b2sInfoNetworkModal').modal('show');
    return false;
});

jQuery(document).on('click', '.b2s-re-post-show-calender-btn', function () {
    jQuery('.b2s-re-post-queue-area').hide();
    jQuery('.b2s-re-post-calender-area').show();
    jQuery('.b2s-re-post-queue-delete-area').hide();
    jQuery(".fc-today-button").trigger('click');
    return false;
});

jQuery(document).on('click', '.b2s-re-post-show-list-btn', function () {
    jQuery('.b2s-re-post-queue-area').show();
    jQuery('.b2s-re-post-calender-area').hide();
    if (jQuery('.b2s-re-post-queue-area .list-group-item[data-type="post"]').length == 0) {
        jQuery('.b2s-re-post-queue-delete-area').hide();
    } else {
        jQuery('.b2s-re-post-queue-delete-area').show();
    }
    return false;
});


function showEditSchedCalendarPost(b2s_id, post_id, network_auth_id, network_type, network_id, post_format, relay_primary_post_id) {
    if (jQuery('#b2s-edit-event-modal-' + b2s_id).length == 1)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).remove();
    }
    b2s_current_post_id = post_id;
    jQuery("#b2sPostId").val(post_id);
    var $modal = jQuery("<div>");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_post_edit_modal',
            'id': b2s_id,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                $modal = $modal.html(data);
            }
        }
    });
    jQuery("body").append($modal);
    jQuery('#b2sUserTimeZone').val(jQuery('#user_timezone').val());
    jQuery('#b2s-edit-event-modal-' + b2s_id).modal('show');
    activatePortal(network_auth_id);
    initSceditor(network_auth_id);
    if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').length > 0) {
        var postFormatText = b2s_calendar_formats;
        var isSetPostFormat = false;
        var postFormatType = jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').attr('data-post-format-type');
        //is set post format => override current condidtions by user settings for this post
        if (post_format !== null) {
            jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val(post_format);
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][post_format]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(post_format);
            //edit modal select post format
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').removeClass('b2s-settings-checked');
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"][data-post-format="' + post_format + '"]').addClass('b2s-settings-checked');
        } else {
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + network_auth_id + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val()]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val());
        }

        //if linkpost then show btn meta tags
        var isMetaChecked = false;
        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
        if (typeof network_id != 'undefined' && jQuery.inArray(network_id.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if ((network_id == "2" || network_id == "24") && jQuery('#isCardMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + network_type + '"][data-network-id="' + network_id + '"]').val() == "0") {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", false);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').show();
            //jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').trigger("click");
            var dataMetaType = jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').attr("data-meta-type");
            if (dataMetaType == "og") {
                jQuery('.b2sChangeOgMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            } else {
                jQuery('.b2sChangeCardMeta[data-network-auth-id="' + network_auth_id + '"]').val("1");
            }
        } else {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-id="' + network_id + '"]').hide();
        }

        //Content Curation
        if (jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + network_auth_id + '"]').attr('data-post-wp-type') == 'ex') {
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + network_auth_id + '"]').prop("readonly", true);
            jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + network_auth_id + '"]').attr("style", "display:none !important");
            if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + network_auth_id + '"]').val() == 0) {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').hide();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').hide();
            } else {
                jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + network_auth_id + '"]').show();
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + network_auth_id + '"]').show();
            }
        }
    }
    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + network_auth_id + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(network_auth_id, network_id, textLimit);
    } else {
        networkCount(network_auth_id);
    }
    var today = new Date();
    var dateFormat = "yyyy-mm-dd";
    var language = "en";
    var showMeridian = true;
    if (jQuery('#b2sUserLang').val() == "de") {
        dateFormat = "dd.mm.yyyy";
        language = "de";
        showMeridian = false;
    }

    jQuery(".b2s-post-item-details-release-input-date").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        startDate: today,
        calendarWeeks: true,
        autoclose: true
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current',
        snapToStep: true
    });
    jQuery(".b2s-post-item-details-release-input-date").datepicker().on('changeDate', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    jQuery('.b2s-post-item-details-release-input-time').timepicker().on('changeTime.timepicker', function (e) {
        checkSchedDateTime(network_auth_id);
    });
    init();

    //is relay post?
    if (relay_primary_post_id > 0) {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-input-hidden') && !jQuery(this).hasClass('b2s-modal-close') && !jQuery(this).hasClass('b2s-post-item-details-relay-input-delay') && !jQuery(this).hasClass('b2s-edit-post-delete') && !jQuery(this).hasClass('b2s-edit-post-save-this')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }

    if (!b2s_has_premium)
    {
        jQuery('#b2s-edit-event-modal-' + b2s_id).find("input, textarea, button").each(function () {
            if (!jQuery(this).hasClass('b2s-modal-close')) {
                jQuery(this).prop("disabled", true);
            }
        });
    }
}

function refreshCalender() {
    jQuery('#b2s_calendar').fullCalendar('refetchEvents');
}

function renderCalender() {
    jQuery('#b2s_calendar').fullCalendar({
        header: {
            left: 'title',
            right: 'today month,basicWeek, prev,next'
        },
        views: {
            month: {
                eventLimit: 2
            },
            basicWeek: {
                eventLimit: false
            }
        },
        editable: b2s_has_premium,
        locale: b2s_calendar_locale,
        timeFormat: 'H:mm',
        eventSources: [curSource[0]],
        eventRender: function (event, element) {
            show = true;
            $header = jQuery("<div>").addClass("b2s-calendar-header").attr('data-b2s-id', event.b2s_id);
            $isRelayPost = '';
            $isCuratedPost = '';
            $isRePost = '';
            if (event.post_type == 'b2s_ex_post') {
                $isCuratedPost = ' (Curated Post)';
            }
            if (event.relay_primary_post_id > 0) {
                $isRelayPost = ' (Retweet)';
            }
            if (event.b2s_sched_type == 5) {
                $isRePost = ' (Re-Share)';
            }
            $network_name = jQuery("<span>").text(event.author + $isRelayPost + $isCuratedPost + $isRePost).addClass("network-name").css("display", "block");
            element.find(".fc-time").after($network_name);
            element.html(element.html());
            $parent = element.parent();
            $header.append(element.find(".fc-content"));
            element.append($header);
            $body = jQuery("<div>").addClass("b2s-calendar-body");
            $body.append(event.avatar);
            if (event.status == "error") {
                $body.append(jQuery('<i>').addClass('glyphicon glyphicon-warning-sign glyphicon-danger'));
            }
            $body.append(element.find(".fc-title"));
            $body.append(jQuery("<br>"));
            var $em = jQuery("<em>").css("padding-top", "5px").css("display", "block");
            $em.append("<img src='" + b2s_plugin_url + "assets/images/portale/" + event.network_id + "_flat.png' style='height: 16px;width: 16px;display: inline-block;padding-right: 2px;padding-left: 2px;' />")
            $em.append(event.network_name);
            $em.append(jQuery("<span>").text(": " + event.profile));
            $body.append($em);
            element.append($body);
            if (event.status != "scheduled") {
                event.editable = false;
            }
        },
        dayRender: function (date, element) {
            var view = jQuery('#b2s_calendar').fullCalendar('getView');
            if (!jQuery(element[0]).hasClass('fc-past')) {
                var date = jQuery(element[0]).attr('data-date');
                var sel_element = '';
                if (view.type == 'month') {
                    sel_element = jQuery(element[0]).closest('div').next('div').find('td[data-date="' + date + '"]');
                } else {
                    sel_element = jQuery('.fc-basicWeek-view').find('th[data-date="' + date + '"]');
                }
            }

        },
        eventDrop: function (event, delta, revertFunc) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_calendar_move_post',
                    'b2s_id': event.b2s_id,
                    'user_timezone': event.user_timezone,
                    'sched_date': event.start.format(),
                    'post_for_relay': event.post_for_relay,
                    'post_for_approve': event.post_for_approve,
                    'network_type': event.network_type,
                    'nework_id': event.network_id,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {
                    refreshCalender();
                    wp.heartbeat.connectNow();
                }
            });
        },
        eventAllow: function (dropLocation, draggedEvent) {
            return dropLocation.start.isAfter(b2s_calendar_date) && draggedEvent.start.isAfter(b2s_calendar_datetime);
        },
        eventClick: function (calEvent, jsEvent, view) {
            if (calEvent.status == "scheduled") {
                showEditSchedCalendarPost(calEvent.b2s_id, calEvent.post_id, calEvent.network_auth_id, calEvent.network_type, calEvent.network_id, calEvent.post_format, calEvent.relay_primary_post_id);
            } else {
                if (calEvent.publish_link != "") {
                    window.open(calEvent.publish_link, '_blank');
                }
            }
        },
        loading: function (bool) {
            if (!bool) {
                //Routing from Dashboard - loading edit post preview
                var rfd = jQuery('#b2s_rfd').val();
                if (rfd == 1) {
                    jQuery('#b2s_rfd').val("0");
                    jQuery('.b2s-calendar-header[data-b2s-id="' + jQuery('#b2s_rfd_b2s_id').val() + '"]').parent().trigger('click');
                }
            }
        }

    });
    refreshCalender();
}