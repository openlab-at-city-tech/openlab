jQuery.noConflict();

var curSource = new Array();
curSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=all&filter_status=0&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
var newSource = new Array();

jQuery(document).ready(function () {
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
                $header = jQuery("<a>").html("+ <span class=\"hidden-sm hidden-xs\">" + jQuery("#b2sJSTextAddPost").val() + "</span>").addClass("b2s-calendar-sched-new-post-btn").attr('href', '#');
                sel_element.append($header);
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
                } else {
                    if(calEvent.errorText != null && calEvent.errorText != "") {
                        jQuery('.b2s-error-text').html(calEvent.errorText);
                        jQuery('#b2s-show-error-modal').modal('show');
                    }
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
    jQuery(".b2s-loading-area").hide();
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
    }
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
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

jQuery(document).on('change', '.b2s-calendar-filter-network-btn', function () {
    var filter_status = jQuery('#b2s-calendar-filter-status').val();
    newSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=' + jQuery(this).val() + '&filter_status=' + filter_status + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery('#b2s_calendar').fullCalendar('removeEventSource', curSource[0]);
    jQuery('#b2s_calendar').fullCalendar('addEventSource', newSource[0]);
    curSource[0] = newSource[0];

    jQuery('.b2s-calendar-filter-network-account-list').html("");
    jQuery('.b2s-calendar-filter-network-account-list').hide();
    if (jQuery(this).val() != 'all') {
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_get_calendar_filter_network_auth',
                'network_id': jQuery(this).val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery(".b2s-calendar-filter-network-account-list").show();
                    jQuery(".b2s-calendar-filter-network-account-list").html(data.content);
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }
        });
    }
    return false;
});


jQuery(document).on('change', '#b2s-calendar-filter-network-auth-sel', function () {
    var filter_network_details_auth_id = jQuery(this).val();
    var filter_network_id = jQuery('.b2s-calendar-filter-network-btn:checked').val();
    var filter_status = jQuery('#b2s-calendar-filter-status').val();
    newSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=' + filter_network_details_auth_id + '&filter_network=' + filter_network_id + '&filter_status=' + filter_status + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery('#b2s_calendar').fullCalendar('removeEventSource', curSource[0]);
    jQuery('#b2s_calendar').fullCalendar('addEventSource', newSource[0]);
    curSource[0] = newSource[0];

    return false;

});

jQuery(document).on('change', '#b2s-calendar-filter-status', function () {
    var filter_network_id = jQuery('.b2s-calendar-filter-network-btn:checked').val();
    var filter_network_details_auth_id = jQuery('#b2s-calendar-filter-network-auth-sel').val();
    if (typeof filter_network_details_auth_id == 'undefined') {
        filter_network_details_auth_id = 'all';
    }
    var filter_status = jQuery('#b2s-calendar-filter-status').val();
    newSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=' + filter_network_details_auth_id + '&filter_network=' + filter_network_id + '&filter_status=' + filter_status + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery('#b2s_calendar').fullCalendar('removeEventSource', curSource[0]);
    jQuery('#b2s_calendar').fullCalendar('addEventSource', newSource[0]);
    curSource[0] = newSource[0];

    return false;

});


//Modal Edit Post close
jQuery(document).on('click', '.b2s-modal-close-edit-post', function (e) {
    jQuery(jQuery(this).attr('data-modal-name')).remove();
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

jQuery(document).on('change', '.b2s-select', function () {
    jQuery('#b2sPagination').val("1");
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-sort-reset-btn', function () {
    jQuery('#b2sPagination').val("1");
    jQuery('#b2sSortPostTitle').val("");
    jQuery('#b2sSortPostAuthor').prop('selectedIndex', 0);
    jQuery('#b2sSortPostCat').prop('selectedIndex', 0);
    jQuery('#b2sSortPostType').prop('selectedIndex', 0);
    jQuery('#b2sSortPostSchedDate').prop('selectedIndex', 0);
    jQuery('#b2sShowByDate').val("");
    jQuery('#b2sUserAuthId').val("");
    jQuery('#b2sSortPostStatus').prop('selectedIndex', 0);
    jQuery('#b2sSortPostPublishDate').prop('selectedIndex', 0);
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '.b2s-calendar-sched-new-post-btn', function () {
    if (jQuery('#user_version').val() == 0) {
        jQuery('#b2s-sched-post-modal').modal('show');
        return false;
    }
    jQuery('#b2s-show-post-type-modal').modal('show');
    var view = jQuery('#b2s_calendar').fullCalendar('getView');
    var selSchedDate;
    if (view.type == 'month') {
        selSchedDate = jQuery(this).parent('td').attr('data-date');
    } else {
        selSchedDate = jQuery(this).parent('th').attr('data-date');
    }
    jQuery('#b2sSelSchedDate').val(selSchedDate);
    return false;
});

jQuery(document).on('click', '#b2s-btn-select-blog-post', function () {
    jQuery('#b2s-show-post-type-modal').modal('hide');
    jQuery('#b2s-show-post-all-modal').modal('show');
    if (!jQuery('#b2sSelSchedDate').length > 0) {
        jQuery('.b2sSortForm input:first').after('<input value="' + jQuery('#b2sSelSchedDate').val() + '" id="b2sSelSchedDate" name="b2sSelSchedDate" type="hidden">');
    }
    b2sSortFormSubmit();
    return false;
});

jQuery(document).on('click', '#b2s-btn-select-content-curation', function () {
    window.location.href = jQuery('#b2sRedirectUrlContentCuration').val() + '&schedDate=' + jQuery('#b2sSelSchedDate').val();
    return false;
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

//b2sSortForm 
function b2sSortFormSubmit() {
    jQuery(".b2s-loading-area").show();
    jQuery('.b2s-sort-result-item-area').html('');
    jQuery('.b2s-sort-pagination-area').html('');

    var data = {
        'action': 'b2s_sort_data',
        'b2sSortPostTitle': jQuery('#b2sSortPostTitle').val(),
        'b2sSortPostAuthor': jQuery('#b2sSortPostAuthor').val(),
        'b2sSortPostSharedBy': jQuery('#b2sSortPostSharedBy').val(),
        'b2sSortPostCat': jQuery('#b2sSortPostCat').val(),
        'b2sSortPostType': jQuery('#b2sSortPostType').val(),
        'b2sSortPostSchedDate': jQuery('#b2sSortPostSchedDate').val(),
        'b2sUserAuthId': jQuery('#b2sUserAuthId').val(),
        'b2sType': jQuery('#b2sType').val(),
        'b2sShowByDate': jQuery('#b2sShowByDate').val(),
        'b2sPagination': jQuery('#b2sPagination').val(),
        'b2sShowPagination': jQuery('#b2sShowPagination').length > 0 ? jQuery('#b2sShowPagination').val() : 1,
        'b2sSortPostStatus': jQuery('#b2sSortPostStatus').val(),
        'b2sSortPostPublishDate': jQuery('#b2sSortPostPublishDate').val(),
        'b2sSortPostShareStatus': jQuery('#b2sSortPostShareStatus').val(),
        'b2sUserLang': jQuery('#b2sUserLang').val(),
        'b2sSchedDate': jQuery('#b2sSelSchedDate').val(),
        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
    };

    if (jQuery('#b2sPostsPerPage').length > 0) {
        data['b2sPostsPerPage'] = jQuery('#b2sPostsPerPage').val();
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
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
                jQuery('.b2s-sort-result-item-area').html(data.content).show();
                jQuery('.b2s-sort-pagination-area').html(data.pagination).show();
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
    return false;
}
//Overlay second modal
jQuery('#b2s-show-post-all-modal').on('shown.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

//Overlay second modal
jQuery('#b2s-network-select-image').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

//Overlay second modal
jQuery('#b2s-post-ship-item-post-format-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

//Overlay second modal
jQuery('#b2sImageZoomModal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

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
jQuery(document).on("click", ".b2s-edit-post-delete", function () {
    var id = jQuery(this).data("b2s-id");
    var post_id = jQuery(this).data("post-id");
    var post_for_relay = jQuery(this).data("data-post-for-relay");
    var post_for_approve = jQuery(this).data("data-post-for-approve");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_post',
            'b2s_id': id,
            'post_id': post_id,
            'post_for_relay': post_for_relay,
            'post_for_approve': post_for_approve,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            jQuery('#b2s-edit-event-modal-' + id).modal('hide');
            refreshCalender();
            wp.heartbeat.connectNow();
        }
    });
});

/*jQuery(document).on("click", ".b2s-calendar-save-all", function (e) {
 jQuery('#save_method').val("apply-all");
 e.preventDefault();
 var id = jQuery(this).data("b2s-id");
 jQuery.ajax({
 url: ajaxurl,
 type: "POST",
 dataType: "json",
 cache: false,
 data: jQuery(this).closest("form").serialize(),
 success: function (data) {
 jQuery('#b2s-edit-event-modal-' + id).modal('hide');
 refreshCalender();
 jQuery('#b2s-edit-event-modal-' + id).remove();
 wp.heartbeat.connectNow();
 }
 });
 });*/

jQuery(document).on("click", ".b2s-edit-post-save-this", function (e) {
    e.preventDefault();
    jQuery('#save_method').val("apply-this");
    var id = jQuery(this).data("b2s-id");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: jQuery(this).closest("form").serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
        success: function (data) {
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            jQuery('#b2s-edit-event-modal-' + id).modal('hide');
            refreshCalender();
            jQuery('#b2s-edit-event-modal-' + id).remove();
            jQuery('body').removeClass('modal-open');
            jQuery('body').removeAttr('style');
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
function refreshCalender() {
    jQuery('#b2s_calendar').fullCalendar('refetchEvents');
}

jQuery('#b2s-info-meta-tag-modal').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2s-network-select-image').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2s-post-ship-item-post-format-modal').on('hidden.bs.modal', function (e) {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2s-info-change-meta-tag-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
//jQuery(this).attr('data-network-auth-id')
function checkSchedDateTime(dataNetworkAuthId) {
    var dateElement = '.b2s-post-item-details-release-input-date[data-network-auth-id="' + dataNetworkAuthId + '"]';
    var timeElement = '.b2s-post-item-details-release-input-time[data-network-auth-id="' + dataNetworkAuthId + '"]';
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


function printDateFormat(dataNetworkAuthId) {
    var dateElement = '.b2s-post-item-details-release-input-date[data-network-auth-id="' + dataNetworkAuthId + '"]';
    var dateStr = jQuery(dateElement).val();
    dateStr = dateStr.substring(8, 10) + '.' + dateStr.substring(5, 7) + '.' + dateStr.substring(0, 4);
    jQuery(dateElement).val(dateStr);
}

jQuery(document).on('click', '.b2s-get-settings-sched-time-default', function () {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_settings_sched_time_default',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                var tomorrow = new Date();
                if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                    tomorrow.setTime(jQuery('#b2sBlogPostSchedDate').val());
                }
                tomorrow.setDate(tomorrow.getDate() + 1);
                var tomorrowMonth = ("0" + (tomorrow.getMonth() + 1)).slice(-2);
                var tomorrowDate = ("0" + tomorrow.getDate()).slice(-2);
                var dateTomorrow = tomorrow.getFullYear() + "-" + tomorrowMonth + "-" + tomorrowDate;
                var today = new Date();
                if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                    today.setTime(jQuery('#b2sBlogPostSchedDate').val());
                }

                var todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
                var todayDate = ("0" + today.getDate()).slice(-2);
                var dateToday = today.getFullYear() + "-" + todayMonth + "-" + todayDate;
                var lang = jQuery('#b2sUserLang').val();
                if (lang == "de") {
                    dateTomorrow = tomorrowDate + "." + tomorrowMonth + "." + tomorrow.getFullYear();
                    dateToday = todayDate + "." + todayMonth + "." + today.getFullYear();
                }

                jQuery.each(data.times, function (network_id, time) {
                    if (jQuery('.b2s-post-item[data-network-id="' + network_id + '"]').is(":visible")) {
                        time.forEach(function (network_type_time, count) {
                            if (network_type_time != "") {


                                var hours = network_type_time.substring(0, 2);
                                if (lang == "en") {
                                    var timeparts = network_type_time.split(' ');
                                    hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                                }
                                if (hours < today.getHours()) {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateTomorrow);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                } else {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateToday);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateToday);
                                }
                                jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').val(network_type_time);
                                jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-type="' + count + '"][data-network-count="0"]').timepicker('setTime', network_type_time);

                                count++;
                            }
                        });
                    }
                });
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
    return false;
});



jQuery(document).on('click', '.b2s-get-settings-sched-time-user', function () {
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_settings_sched_time_user',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            var tomorrow = new Date();
            if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                tomorrow.setTime(jQuery('#b2sBlogPostSchedDate').val());
            }

            tomorrow.setDate(tomorrow.getDate() + 1);
            var tomorrowMonth = ("0" + (tomorrow.getMonth() + 1)).slice(-2);
            var tomorrowDate = ("0" + tomorrow.getDate()).slice(-2);
            var dateTomorrow = tomorrow.getFullYear() + "-" + tomorrowMonth + "-" + tomorrowDate;
            var today = new Date();
            if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                today.setTime(jQuery('#b2sBlogPostSchedDate').val());
            }

            var todayMonth = ("0" + (today.getMonth() + 1)).slice(-2);
            var todayDate = ("0" + today.getDate()).slice(-2);
            var dateToday = today.getFullYear() + "-" + todayMonth + "-" + todayDate;
            var lang = jQuery('#b2sUserLang').val();
            if (lang == "de") {
                dateTomorrow = tomorrowDate + "." + tomorrowMonth + "." + tomorrow.getFullYear();
                dateToday = todayDate + "." + todayMonth + "." + today.getFullYear();
            }
            if (data.result == true) {
                //V5.1.0 seeding
                if (data.type == 'new') {
                    //new
                    jQuery.each(data.times, function (network_auth_id, time) {
                        if (jQuery('.b2s-post-item[data-network-auth-id="' + network_auth_id + '"]').is(":visible")) {
                            //is not set special dates
                            if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + network_auth_id + '"]').val() == '0') {
                                jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + network_auth_id + '"]').val('1').trigger("change");
                            }
                            var hours = time.substring(0, 2);
                            var timeparts = time.split(' ');
                            if (typeof timeparts[1] != 'undefined') {
                                hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                            }

                            var isDelay = false;
                            var delayDay = data.delay_day[network_auth_id];
                            if (delayDay != undefined) {
                                if (delayDay > 0) {
                                    var delay = new Date();
                                    if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                                        delay.setTime(jQuery('#b2sBlogPostSchedDate').val());
                                    }
                                    delay.setDate(delay.getDate() + parseInt(delayDay));
                                    var delayMonth = ("0" + (delay.getMonth() + 1)).slice(-2);
                                    var delayDate = ("0" + delay.getDate()).slice(-2);
                                    var dateDelay = delay.getFullYear() + "-" + delayMonth + "-" + delayDate;
                                    if (lang == 'de') {
                                        dateDelay = delayDate + '.' + delayMonth + "." + delay.getFullYear();
                                    }
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateDelay);
                                    isDelay = true;

                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateDelay);
                                }
                            }
                            if (!isDelay) {
                                if (hours < today.getHours()) {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateTomorrow);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                } else {
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(dateToday);
                                    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').datepicker('update', dateToday);
                                }
                            }
                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').val(time);
                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + network_auth_id + '"][data-network-count="0"]').timepicker('setTime', new Date(today.getFullYear(), today.getMonth(), today.getDate(), hours, time.slice(3, 5)));
                        }
                    });
                } else {
                    //old
                    jQuery.each(data.times, function (network_id, time) {
                        if (jQuery('.b2s-post-item[data-network-id="' + network_id + '"]').is(":visible")) {
                            time.forEach(function (network_type_time, count) {
                                if (network_type_time != "") {
                                    var networkAuthId = jQuery(this).attr('data-network-auth-id');
                                    //is not set special dates
                                    if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val() != '1') {
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val('1').trigger("change");
                                    }
                                    var hours = network_type_time.substring(0, 2);
                                    if (lang == "en") {
                                        var timeparts = network_type_time.split(' ');
                                        hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                                    }
                                    if (hours < today.getHours()) {
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateTomorrow);
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                    } else {
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateToday);
                                        jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateToday);
                                    }
                                    jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(network_type_time);
                                    jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').timepicker('setTime', network_type_time);
                                    count++;
                                }
                            });
                        }
                    });
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                //default load best Times
                //jQuery('.b2s-get-settings-sched-time-default').trigger('click');
                //set current time
                jQuery('.b2s-post-item:visible').each(function () {
                    var networkAuthId = jQuery(this).attr('data-network-auth-id');
                    if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').is(':not(:disabled)')) {
                        //is not set special dates
                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() != '1') {
                            jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val('1').trigger("change");
                        }
                    }
                });
            }
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-add-multi-image', function() {
    jQuery('.b2s-network-select-image-content').html("");
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_get_image_modal',
            'id': jQuery('#post_id').val(),
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
});