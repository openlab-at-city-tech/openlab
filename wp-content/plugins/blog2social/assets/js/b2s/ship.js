
jQuery.noConflict();

var b2sTosXingGroupCount = 0;
var currentOGImage = '';
var changedOGImage = false;

jQuery(document).on('heartbeat-send', function (e, data) {
    data['b2s_heartbeat'] = 'b2s_listener';
});

jQuery.xhrPool = [];

jQuery(window).on("load", function () {

    //Onboarding
    if (typeof jQuery('#b2s-toastee-paused').val() != "undefined") {
        jQuery('.b2s-container').css('margin-top', "40px");
    }

    // Assistini data
    initAssSidebar();

    init(true);
    imageSize();
    if (jQuery('.toggelbutton').is(':visible') && !jQuery("#b2s-wrapper").hasClass("toggled")) {
        jQuery('.btn-toggle-menu').trigger('click');
    }
    if (jQuery('#b2sOpenDraftIncompleteModal').val() == '1') {
        jQuery('#b2sDraftIncompleteModal').modal('show');
    }

});


//Stop duplicate posts by page refreshing during the post process
jQuery(document).on('keydown', '#b2sNetworkSent', function (event) {
    if (event.keyCode == 116) {
        event.preventDefault();
        return false;
    }
});

jQuery(document).on('click', '.btn-toggle-menu', function () {
    if (jQuery('.toggelbutton').is(':visible')) {
        jQuery("#b2s-wrapper").toggleClass("toggled");
        if (jQuery("#b2s-wrapper").hasClass("toggled")) {
            jQuery(".sidebar-brand").hide();
            jQuery(".btn-toggle-glyphicon").removeClass('glyphicon-chevron-right').addClass('glyphicon-chevron-left');
        } else {
            jQuery(".sidebar-brand").show();
            jQuery(".btn-toggle-glyphicon").removeClass('glyphicon-chevron-left').addClass('glyphicon-chevron-right');
        }
    }
});

jQuery.sceditor.formats.xhtml.allowedTags = ['h1', 'h2', 'p', 'br', 'i', 'em', 'b', 'a', 'img', 'span'];
jQuery.sceditor.command.set(
        "h1", {
            exec: function () {
                if (this.currentBlockNode() == undefined || this.currentBlockNode().nodeName != 'H1') {
                    this.wysiwygEditorInsertHtml('<h1>', '</h1>');
                } else {
                    jQuery(this.currentBlockNode()).replaceWith(this.currentBlockNode().innerText);
                }
            },
            txtExec: ["<h1>", "</h1>"],
            tooltip: "H1"
        });
jQuery.sceditor.command.set(
        "h2", {
            exec: function () {
                if (this.currentBlockNode() == undefined || this.currentBlockNode().nodeName != 'H2') {
                    this.wysiwygEditorInsertHtml('<h2>', '</h2>');
                } else {
                    jQuery(this.currentBlockNode()).replaceWith(this.currentBlockNode().innerText);
                }
            },
            txtExec: ["<h2>", "</h2>"], tooltip: "H2"});

jQuery.sceditor.command.set(
        "custom-image", {
            exec: function () {
                var me = this;
                if (typeof (b2s_is_calendar) != "undefined" && b2s_is_calendar)
                {
                    jQuery('.b2s-network-select-image-content').html("");
                    jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        cache: false,
                        async: false,
                        data: {
                            'action': 'b2s_get_image_modal',
                            'id': b2s_current_post_id,
                            'image_url': '',
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
                }
                var networkAuthId = jQuery(this.getContentAreaContainer()).parents('.b2s-post-item-details').find('.b2s-post-item-details-network-display-name').attr('data-network-auth-id');
                jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', networkAuthId);
                jQuery('.b2s-image-change-this-network').show();
                jQuery('.b2s-upload-image').attr('data-network-auth-id', networkAuthId);
                var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + networkAuthId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + networkAuthId + '"]').html();
                jQuery('.b2s-selected-network-for-image-info').html(content);
                jQuery('#b2s-network-select-image').modal('show');
                jQuery('.b2s-image-change-meta-network').hide();
                jQuery('#b2sInsertImageType').val("1");
                imageSize();

            },
            txtExec: function () {
                var networkAuthId = jQuery(this.getContentAreaContainer()).parents('.b2s-post-item-details').find('.b2s-post-item-details-network-display-name').attr('data-network-auth-id');
                jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', networkAuthId);
                jQuery('.b2s-upload-image').attr('data-network-auth-id', networkAuthId);
                var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + networkAuthId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + networkAuthId + '"]').html();
                jQuery('.b2s-selected-network-for-image-info').html(content);
                jQuery('#b2s-network-select-image').modal('show');
                jQuery('#b2sInsertImageType').val("1");
                imageSize();
            }, tooltip: "Image"});
jQuery.sceditor.command.set(
        "custom-emoji", {
            exec: function () {
                var me = this;
                if (pickerHTML.pickerVisible) {
                    pickerHTML.hidePicker();
                } else {
                    pickerHTML.showPicker(jQuery(this.getContentAreaContainer()).parent('.sceditor-container').find('.sceditor-toolbar').find('.sceditor-button-custom-emoji'));
                    currentPickerHTMLContent = this;
                }
            },
            txtExec: function () {
                var me = this;
                if (pickerHTML.pickerVisible) {
                    pickerHTML.hidePicker();
                } else {
                    pickerHTML.showPicker(jQuery(this.getContentAreaContainer()).parent('.sceditor-container').find('.sceditor-toolbar').find('.sceditor-button-custom-emoji'));
                    currentPickerHTMLContent = this;
                }
            }, tooltip: "Emoji"});


jQuery(document).on('click', '.b2s-toogle-calendar-btn', function () {

    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkId = jQuery(this).attr('data-network-id');
    var toogleBtnText = jQuery(this).attr('data-toogle-text-btn');
    var currentBtnText = jQuery(this).text();

    jQuery(this).text(toogleBtnText);
    jQuery(this).attr('data-toogle-text-btn', currentBtnText);

    //change to show
    var calendar = jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + networkAuthId + '"]');
    if (calendar.hasClass('hide')) {
        calendar.removeClass('hide');
        calendar.addClass('show');
        jQuery('.b2s-calendar-filter-area[data-network-auth-id="' + networkAuthId + '"]').removeClass('hide');

        if (calendar.is(':empty')) {
            b2s_cur_source_ship_calendar[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=' + networkId + '&filter_status=2' + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();

            jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + networkAuthId + '"]').fullCalendar({
                editable: false,
                locale: b2s_calendar_locale,
                eventLimit: 2,
                contentHeight: 530,
                timeFormat: 'H:mm',
                eventSources: [b2s_cur_source_ship_calendar[0]],
                eventRender: function (event, element) {
                    show = true;
                    $header = jQuery("<div>").addClass("b2s-calendar-header");
                    $isRelayPost = '';
                    $isCuratedPost = '';
                    if (event.post_type == 'b2s_ex_post') {
                        $isCuratedPost = ' (Curated Post)';
                    }
                    if (event.relay_primary_post_id > 0) {
                        $isRelayPost = ' (Retweet)';
                    }
                    $network_name = jQuery("<span>").text(event.author + $isRelayPost + $isCuratedPost).addClass("network-name").css("display", "block");
                    element.find(".fc-time").after($network_name);
                    element.html(element.html());
                    $parent = element.parent();
                    $header.append(element.find(".fc-content"));
                    element.append($header);
                    $body = jQuery("<div>").addClass("b2s-calendar-body");
                    $body.append(event.avatar);
                    $body.append(element.find(".fc-title"));
                    $body.append(jQuery("<br>"));
                    var $em = jQuery("<em>").css("padding-top", "5px").css("display", "block");
                    $em.append("<img src='" + b2s_plugin_url + "assets/images/portale/" + event.network_id + "_flat.png' style='height: 16px;width: 16px;display: inline-block;padding-right: 2px;padding-left: 2px;' />")
                    $em.append(event.network_name);
                    $em.append(jQuery("<span>").text(": " + event.profile));
                    $body.append($em);
                    element.append($body);
                },
                dayRender: function (date, element) {
                    if (!jQuery(element[0]).hasClass('fc-past')) {
                        var date = jQuery(element[0]).attr('data-date');
                        var sel_element = jQuery(element[0]).closest('div').next('div').find('td[data-date="' + date + '"]');
                        $header = jQuery("<a>").html("+ <span class=\"hidden-sm hidden-xs\">" + jQuery("#b2sJSTextAddSchedule").val() + "</span>").addClass("b2s-calendar-add-schedule-btn").attr('href', '#').attr('data-network-auth-id', networkAuthId);
                        sel_element.append($header);
                    }
                }
            });
        } else {
            jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + networkAuthId + '"]').fullCalendar('refetchEvents');
        }

    } else {
        calendar.removeClass('show');
        calendar.addClass('hide');
        jQuery('.b2s-calendar-filter-area[data-network-auth-id="' + networkAuthId + '"]').addClass('hide');

    }

    return false;

});

jQuery(document).on('click', '.b2s-calendar-add-schedule-btn', function () {
    var selSchedDate = jQuery(this).parent('td').attr('data-date');
    var networkAuthId = jQuery(this).attr('data-network-auth-id');

    if (jQuery('#user_version').val() == 0) {
        jQuery('.b2s-post-item-details-release-input-date-select-reset[data-network-auth-id="' + networkAuthId + '"]').val('0');
        jQuery('#b2sPreFeatureScheduleModal').modal('show');
        return false;
    }

    if (jQuery('#b2sUserLang').val() == 'de') {
        selSchedDate = selSchedDate.substring(8, 10) + '.' + selSchedDate.substring(5, 7) + '.' + selSchedDate.substring(0, 4);
    }

    //isfirst
    if (!jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"] option[value="1"]:selected').length > 0) {
        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val('1').trigger("change");
        jQuery('.b2s-post-item-details-release-input-date[data-network-count="0"][data-network-auth-id="' + networkAuthId + '"]').val(selSchedDate);
    } else {
        //add
        var curSel = jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + networkAuthId + '"]').filter(':visible');
        if (curSel.length > 0) {
            curSel.trigger('click');
            netCountNext = parseInt(curSel.attr('data-network-count')) + 1;
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + netCountNext + '"][data-network-auth-id="' + networkAuthId + '"]').val(selSchedDate);
        } else {
            //do not adding write to first
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="0"][data-network-auth-id="' + networkAuthId + '"]').val(selSchedDate);
        }
    }
    return false;
});

jQuery(document).on('change', '.b2s-calendar-filter-network-sel', function () {
    var newSource = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=' + jQuery(this).val() + '&filter_status=2' + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    var oldSource = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=' + jQuery(this).attr('data-last-sel') + '&filter_status=2' + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery(this).attr('data-last-sel', jQuery(this).val());
    jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').fullCalendar('removeEventSource', oldSource);
    jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').fullCalendar('addEventSource', newSource);
    return false;
});


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
                                jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-type="' + count + '"]').each(function () {
                                    if (jQuery(this).is(':not(:disabled)')) {
                                        var networkAuthId = jQuery(this).attr('data-network-auth-id');
                                        //is not set special dates
                                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val() == '0') {
                                            jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"]').val('1').trigger("change");
                                        }
                                        var hours = network_type_time.substring(0, 2);
                                        if (lang == "en") {
                                            var timeparts = network_type_time.split(' ');
                                            hours = (timeparts[1] == 'AM') ? hours : (parseInt(hours) + 12);
                                        }
                                        if (hours < today.getHours()) {
                                            if (jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val() < dateTomorrow) {
                                                jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateTomorrow);
                                                jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateTomorrow);
                                            }
                                        } else {
                                            if (jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val() < dateToday) {
                                                jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(dateToday);
                                                jQuery('.b2s-post-item-details-release-input-date[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').datepicker('update', dateToday);
                                            }
                                        }
                                        jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').val(network_type_time);
                                        jQuery('.b2s-post-item-details-release-input-time[data-network-id="' + network_id + '"][data-network-auth-id="' + networkAuthId + '"][data-network-type="' + count + '"][data-network-count="0"]').timepicker('setTime', network_type_time);
                                    }
                                });
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



jQuery(document).on('click', '.b2s-sidbar-network-auth-btn', function () {
    jQuery('#b2s-network-list-modal').modal('show');
    return false;
});
jQuery(document).on('click', '.change-meta-tag', function () {
    var attr = jQuery(this).attr('readonly');
    if (typeof attr !== typeof undefined && attr !== false) {
        var networkAuthId = jQuery(this).attr("data-network-auth-id");
        //Content cuation
        var postType = jQuery('.b2s-post-ship-item-post-format[data-network-auth-id=' + networkAuthId + ']').attr('data-post-wp-type');
        if (postType != "ex") {
            jQuery('.meta-text').hide();
            var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val();
            var networkId = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').attr("data-network-id");
            var isMetaChecked = false;
            var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
            if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
                isMetaChecked = true;
            }
            if ((networkId == "2" || networkId == "24" || networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
                isMetaChecked = true;
            }
            var showDefault = true;
            if (postFormat == "0" && !isMetaChecked) { //isLinkPost
                showDefault = false;
                if (networkId == "1") {
                    jQuery('.isOgMetaChecked').show();
                } else {
                    jQuery('.isCardMetaChecked').show();
                }
            }
            if (showDefault) {
                jQuery('.isLinkPost').show();
            }
            jQuery('#b2s-info-change-meta-tag-modal').modal('show');
        }
    }
    return false;
});
// Linkpost change Meta Tags title + desc
jQuery(document).on('keyup', '.change-meta-tag', function () {
    var currentText = jQuery(this).val();
    var metaTag = jQuery(this).attr('data-meta');
    jQuery('.change-meta-tag[data-meta=' + metaTag + ']').each(function () {
        //override this content with current content by keyup
        jQuery(this).val(currentText);
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
                            if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + network_auth_id + '"]').is(':not(:disabled)')) {
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
                        }
                    });
                } else {
                    //old
                    jQuery.each(data.times, function (network_id, time) {
                        if (jQuery('.b2s-post-item[data-network-id="' + network_id + '"]').is(":visible")) {
                            time.forEach(function (network_type_time, count) {
                                if (network_type_time != "") {
                                    jQuery('.b2s-post-item-details-release-input-date-select[data-network-id="' + network_id + '"][data-network-type="' + count + '"]').each(function () {
                                        if (jQuery(this).is(':not(:disabled)')) {
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
                                        }
                                    });
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
jQuery('#b2sPreFeatureModal').on('show.bs.modal', function () {
    jQuery('.b2s-post-item-details-release-input-date-select-reset').val('0');
});
jQuery(document).on('click', '.b2s-network-list-add-btn-profeature', function () {
    jQuery('#b2s-network-list-modal').modal('hide');
});
jQuery(document).on('click', '.b2s-post-item-details-release-area-sched-for-all', function () {
    var dataNetworkAuthId = jQuery(this).attr('data-network-auth-id');
    var dataNetworkCount = 0;
    var selMode = jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + dataNetworkAuthId + '"]').val();
    if (jQuery('.b2s-post-item-details-release-area-details-row[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="1"]').is(":visible")) {
        dataNetworkCount = 1;
    }
    if (jQuery('.b2s-post-item-details-release-area-details-row[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="2"]').is(":visible")) {
        dataNetworkCount = 2;
    }

    jQuery('.b2s-post-item-details-release-input-date-select').each(function () {

        if (jQuery(this).attr('data-network-auth-id') != dataNetworkAuthId && jQuery(this).has('option[value="' + jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + dataNetworkAuthId + '"]').val() + '"]').length > 0) {
            jQuery(this).val(jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + dataNetworkAuthId + '"]').val());
            //view elements
            releaseChoose(jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + dataNetworkAuthId + '"]').val(), jQuery(this).attr('data-network-auth-id'), dataNetworkCount);
            //view elements interval
            if (selMode == 2) {
                for (var i = 0; i <= dataNetworkCount; i++) {
                    var curInterval = jQuery('.b2s-post-item-details-release-input-interval-select[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val();
                    releaseChooseInterval(curInterval, '[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]', i);
                }
            }
        }
    });
    //set values
    for (var i = 0; i <= dataNetworkCount; i++) {
        jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
        jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
        if (selMode == 2) {
            var curInterval = jQuery('.b2s-post-item-details-release-input-interval-select[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val();
            jQuery('.b2s-post-item-details-release-input-interval-select[data-network-count="' + i + '"]').val(curInterval);
            if (curInterval == 0) {
                jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
                jQuery('.b2s-post-item-details-release-input-lable-day-mo[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-mo[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-di[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-di[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-mi[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-mi[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-do[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-do[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-fr[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-fr[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-sa[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-sa[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
                jQuery('.b2s-post-item-details-release-input-lable-day-so[data-network-count="' + i + '"]').prop('checked', jQuery('.b2s-post-item-details-release-input-lable-day-so[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"').prop('checked'));
            }
            if (curInterval == 1) {
                jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
                jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
            }
            if (curInterval == 2) {
                jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
                jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + i + '"]').val(jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + i + '"][data-network-auth-id="' + dataNetworkAuthId + '"]').val());
            }
        }
    }

    if (dataNetworkCount == 2) {
        jQuery('.b2s-post-item-details-release-input-add[data-network-count="0"]').hide();
        jQuery('.b2s-post-item-details-release-input-add[data-network-count="1"]').hide();
        jQuery('.b2s-post-item-details-release-input-hide[data-network-count="1"]').hide();
        jQuery('.b2s-post-item-details-release-input-hide[data-network-count="2"]').show();
    } else if (dataNetworkCount == 1) {
        jQuery('.b2s-post-item-details-release-input-add[data-network-count="0"]').hide();
        jQuery('.b2s-post-item-details-release-input-hide[data-network-count="1"]').show();
    }

    return false;
});
jQuery(document).on("click", ".b2s-user-network-settings-post-format", function () {
    changePostFormat(jQuery(this).attr("data-network-id"), jQuery(this).attr("data-network-type"), jQuery(this).val(), jQuery(this).attr("data-network-auth-id"), jQuery(this).attr("data-post-format-type"), jQuery(this).attr("data-post-wp-type"), true);
    return false;
});
jQuery(document).on("click", ".b2s-post-ship-item-full-text", function () {
    jQuery('.b2s-server-connection-fail').hide();
    var curSchedCount = jQuery(this).attr('data-network-count');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_ship_item_full_text',
            'postId': jQuery('#b2sPostId').val(),
            'userLang': jQuery('#b2sUserLang').val(),
            'networkAuthId': jQuery(this).attr('data-network-auth-id'),
            'networkId': jQuery(this).attr('data-network-id'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                messageInput = jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + curSchedCount + '"][data-network-auth-id="' + data.networkAuthId + '"]');
                messageInput.val(data.text);
                networkCount(data.networkAuthId);
                networkTextLimit = messageInput.attr('data-network-text-limit');
                if (typeof networkTextLimit != undefined) {
                    if (parseInt(networkTextLimit) > 0 && parseInt(data.networkId) > 0) {
                        networkLimitAll(data.networkAuthId, data.networkId, networkTextLimit);
                    }
                }
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.error == 'permission_author') {
                    jQuery('.b2s-no-permission-author').show();
                }
            }
        }
    });
    return false;
});

jQuery(document).on("click", ".b2s-post-item-option-share-type", function () {
    jQuery('.b2s-post-item-option-share-type[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').not(this).prop('checked', false);
    return true;
});

jQuery(document).on("click", ".b2s-post-item-share-as-reel", function () {

    if (jQuery(this).prop('checked')) {
        if (jQuery('#is_video').val() == 1 && jQuery(this).attr('data-network-id') == 1 || jQuery(this).attr('data-network-id') == 12) {

            if (jQuery(this).attr('data-network-count') >= 0) {

                jQuery('.b2s-post-item-option-share-as-story[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').prop('checked', false);
                jQuery('.b2s-post-item-sched-customize-text[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
                jQuery('.b2s-share-as-story-fields[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
                showAssButtons(jQuery(this).attr('data-network-auth-id'),jQuery(this).attr('data-network-count')  );
            } else
            {
                jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').not('.b2s-share-as-story-fields').show();
                jQuery('.b2s-post-item-option-share-as-story[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').prop('checked', false);
                showAssButtons(jQuery(this).attr('data-network-auth-id'),jQuery(this).attr('data-network-count')  );
            }

        }
    }

});


jQuery(document).on("click", ".b2s-post-item-option-share-as-story", function () {

    if (jQuery(this).prop('checked')) {

        if (jQuery('#is_video').val() == 1 && jQuery(this).attr('data-network-id') == 1) {

            jQuery('#b2s\\[' + jQuery(this).attr('data-network-auth-id') + '\\]\\[isReelCB\\]').prop('checked', false);

        }

        if (jQuery(this).attr('data-network-count') >= 0) {

            jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').children().not('.b2s-post-item-details-item-message-area-sched').hide();
            jQuery('.b2s-post-item-details-item-url-input[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').parent().hide();
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        } else {

            jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').first().hide();
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').parent().hide();
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
            jQuery('.b2s-post-original-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        }

        jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('required', false);
        // hide assistini buttons
        hideAssButtons(jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-count'));

        // Stories cannot be commented: hide and disable comment area
        var authId = jQuery(this).attr('data-network-auth-id');
        var networkId = jQuery(this).attr('data-network-id');
        var networkCount = jQuery(this).attr('data-network-count');
        
        // Only for Instagram (12) and Facebook (1) in video mode
        if (networkId == 12 || networkId == 1) {
            disableCommentByStory(authId, networkCount);
        }
    } else {

        if (jQuery(this).attr('data-network-count') >= 0) {

            jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
            jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').children().not('.b2s-post-item-textarea-loader').show();
            jQuery('.b2s-post-item-details-item-url-input[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').parent().show();
            jQuery('.b2s-multi-image-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
        } else {
            jQuery('.b2s-post-item-details-item-message-area[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').first().show();
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').parent().show();
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
            jQuery('.b2s-post-original-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
        }
        jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + jQuery(this).attr('data-network-count') + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('required', true);
        // show assistini buttons
        showAssButtons(jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-count'));

        // Re-enable comment area when story is unchecked
        var authId = jQuery(this).attr('data-network-auth-id');
        var networkId = jQuery(this).attr('data-network-id');
        var networkCount = jQuery(this).attr('data-network-count');
        
        if (networkId == 12 || networkId == 1) {
            enableCommentByStory(authId, networkCount);
        }
    }
    return true;
});

jQuery(document).on("click", ".b2s-post-ship-item-message-delete", function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkCountId = jQuery(this).attr('data-network-count');
    jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val("");
    initSceditor(networkAuthId);
    networkCount(networkAuthId);
    return false;
});
jQuery(document).on("click", ".b2s-post-ship-item-copy-original-text", function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkCountId = jQuery(this).attr('data-network-count');
    var networkId = jQuery(this).attr('data-network-id');
    var text = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + networkAuthId + '"]').val();
    if (text == "" && (networkId == 2 || networkId == 45)) {
        text = jQuery('#b2sTwitterOrginalPost').val();
    }
    jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val(text);
    networkCount(networkAuthId);
    return false;
});

jQuery(document).on("keyup", ".b2s-post-item-details-item-comment-input", function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkCount = jQuery(this).attr('data-network-count');
    commentLimitAll(networkAuthId, networkCount);
});

jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-edit-template-comment', function () {
    var tb = jQuery(this).get(0);
    jQuery('.b2s-edit-template-comment-selection-start[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionStart);
    jQuery('.b2s-edit-template-comment-selection-end[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionEnd);
    
    // Update comment preview with shortcode replacement
    var comment = generateExamplePost(jQuery(this).val(), jQuery('.b2s-edit-template-range-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
    comment = comment.replace(/\n/g, "<br>");
    jQuery('.b2s-edit-template-preview-comment[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(comment);
    
    // Show or hide comment wrapper based on whether there's text
    var wrapper = jQuery('.b2s-edit-template-preview-comment-wrapper[data-network-type="' + jQuery(this).attr('data-network-type') + '"]');
    if (jQuery(this).val().trim() !== '') {
        wrapper.show();
    } else {
        wrapper.hide();
    }
});

jQuery(document).on('click', '.b2s-edit-template-comment-post-item', function () {
    var networkType = jQuery(this).attr('data-network-type');
    var text = jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val();
    var start = jQuery('.b2s-edit-template-comment-selection-start[data-network-type="' + networkType + '"]').val();
    var end = jQuery('.b2s-edit-template-comment-selection-end[data-network-type="' + networkType + '"]').val();

    var reg = new RegExp("({.+?})", "g");
    var amatch = null;
    while ((amatch = reg.exec(text)) != null) {
        var thisMatchStart = amatch.index;
        var thisMatchEnd = amatch.index + amatch[0].length;
        //case: keydown in pattern
        if (start > thisMatchStart && end < thisMatchEnd) {
            event.preventDefault();
            return false;
        }
    }
    var newText = text.slice(0, start) + jQuery(this).html() + text.slice(end);
    jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val(newText);
    jQuery('.b2s-edit-template-comment').focus();
    jQuery('.b2s-edit-template-comment').trigger('keyup');
    event.preventDefault();
    return false;
});

jQuery(document).on('click', '.b2s-edit-template-comment-clear-btn', function () {
    var networkType = jQuery(this).attr('data-network-type');
    jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val("");
    jQuery('.b2s-edit-template-comment').focus();
    event.preventDefault();
    return false;
});

//Force Reload Used to update Ship Item when Network Template Settings changed
jQuery(document).on("click", ".b2s-network-select-btn", function (event, forceReloadFromTemplateChange, chosenPostFormat) {

    forceReloadFromTemplateChange = forceReloadFromTemplateChange == true;

    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkId = jQuery(this).attr('data-network-id');
    var networkType = jQuery(this).attr('data-network-type');
    var metaType = jQuery(this).attr('data-meta-type');
    var maxSchedDate = jQuery(this).attr('data-max-sched-date');

    // Remove old items, so force reload does not create duplicates
    if (forceReloadFromTemplateChange == true) {
        var allItems = jQuery('.b2s-post-list > .b2s-post-item[data-network-auth-id="' + networkAuthId + '"]');
        allItems = allItems.remove();
    }

    //doppelklick Schutz
    if (!jQuery(this).hasClass('b2s-network-select-btn-deactivate')) {
        //active?
        if (forceReloadFromTemplateChange || (!jQuery(this).children().hasClass('b2s-network-list-active'))) {
            //TOS XING Groups
            if ((networkId == 8 || networkId == 19) && networkType == 2) {
                if ((b2sTosXingGroupCount == jQuery('#b2sTosXingGroupCrosspostingLimit').val()) || (networkId == 19 && jQuery('.b2s-network-select-btn[data-network-id="' + networkId + '"][data-network-type="' + networkType + '"][data-network-tos-group-id="' + jQuery(this).attr('data-network-tos-group-id') + '"]').children().hasClass('b2s-network-list-active'))) {
                    jQuery('#b2s-tos-xing-group-max-count-modal').modal('show');
                    return false;
                } else {
                    b2sTosXingGroupCount++;
                }
            }
          
            //schon vorhanden?
            if (forceReloadFromTemplateChange == false && jQuery('.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]').length > 0 && !jQuery('.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]').hasClass('b2s-post-item-connection-fail-dummy')) {
            
                activatePortal(networkAuthId);

                //Hide OG Area when image format from v8.4 onwards
                if (jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val() == 1) {

                    jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').children(':not(div.input-group)').attr('style', 'display: none !important;');
                }

                //PostFormat
                if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').length > 0 || networkId == 15) {
                    var postFormatText = JSON.parse(jQuery('.b2sNetworkSettingsPostFormatText').val());
                    var postFormatType = jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').attr('data-post-format-type');
                    if (jQuery('#user_version').val() >= 2) {
                        
                        jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + networkAuthId + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val()]);
                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val());
                        //if linkpost then show btn meta tags
                        var isMetaChecked = false;
                        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
                        if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
                            isMetaChecked = true;
                        }
                        if ((networkId == "2" || networkId == "24" || networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
                            isMetaChecked = true;
                        }
                        if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val() == "0") {
                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", false);
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", false);
                            var dataMetaType = jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').attr("data-meta-type");
                            if (dataMetaType == "og") {
                                jQuery('#b2sChangeOgMeta').val("1");
                            } else {
                                jQuery('#b2sChangeCardMeta').val("1");
                            }

                            //Copy from further item meta tags by same network
                            jQuery('.change-meta-tag[data-meta-type="' + dataMetaType + '"]').each(function () {
                                if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == "0" && jQuery(this).attr('data-network-auth-id') != networkAuthId) { //other Linkpost by same network
                                    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                                    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src'));
                                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                                    if (jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').is(":visible")) {
                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == 1) {
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                        }
                                    } else {
                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').hide();
                                        jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').hide();

                                    }

                                    return true;
                                }
                            });
                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').show();
                            if (jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').data('meta-type') == 'og' && changedOGImage == true) {
                                if (currentOGImage != "") {
                                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentOGImage);
                                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentOGImage);
                                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                                    jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();
                                } else {
                                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', jQuery('#b2sDefaultNoImage').val());
                                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('#b2sDefaultNoImage').val());
                                }
                            } else {
                                jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').trigger("click");
                            }
                            if ((networkId == "2" || networkId == "24" || networkId == "45")) {
                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
                            }
                        } else {
                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').hide();
                            if ((networkId == "2" || networkId == "24" || networkId == "45")) {
                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').hide();
                            }
                        }

                    } else {
                        
                        jQuery('.b2s-post-ship-item-post-format-text[data-network-id="' + networkId + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val()]);
                        jQuery('.b2s-post-item-details-post-format[data-network-id="' + networkId + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val());
                    }

                    //Content Curation
                    if (jQuery('#b2sPostType').val() == 'ex') {

                        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                        jQuery('.b2sInfoMetaTagModal[data-network-auth-id="' + networkAuthId + '"]').attr("style", "display:none !important");
                        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() == 0) {

                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"]').hide();
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').hide();
                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').hide();
                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').show();

                        } else {

                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').hide();
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').hide();
                        }
                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {

                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                        }
                    }
                }

                //XING TOS Group
                jQuery('.b2s-content-info[data-network-auth-id="' + networkAuthId + '"]').show();

                //Twitter TOS 032018 - protected multiple accounts with same content to same time
                //delete comment field one more
                /* From Version >8.7.0 Twitter multiple texts allowed again
                if (networkId == 2 || networkId == 45) {
                    if (jQuery('.b2s-post-item[data-network-id="' + networkId + '"]:visible').length == 1) {
                        jQuery('.tw-textarea-input[data-network-auth-id="' + networkAuthId + '"]').text(jQuery('#b2sTwitterOrginalPost').val());
                    } else {
                        jQuery('.tw-textarea-input[data-network-auth-id="' + networkAuthId + '"]').text("");
                    }
                }
                */
                checkGifAnimation(networkAuthId, networkId);
            } else {
               
                jQuery(this).addClass('b2s-network-select-btn-deactivate');
                jQuery('.b2s-network-status-img-loading[data-network-auth-id="' + networkAuthId + '"]').show();
                jQuery('.b2s-empty-area').hide();
                loadingDummyShow(networkAuthId, jQuery(this).attr('data-network-id'));
                jQuery('.b2s-server-connection-fail').hide();
                var legacyMode = jQuery('#isLegacyMode').val();
                if (legacyMode == "1") {
                    legacyMode = false; // loading is sync (stack)
                } else {
                    legacyMode = true; // loading is async (parallel)
                }

                jQuery.ajax({
                    url: ajaxurl,
                    type: "POST",
                    dataType: "json",
                    async: legacyMode,
                    cache: false,
                    data: {
                        'action': 'b2s_ship_item',
                        'networkAuthId': networkAuthId,
                        'networkType': jQuery(this).attr('data-network-type'),
                        'networkKind': jQuery(this).attr('data-network-kind'),
                        'networkId': networkId,
                        'networkDisplayName': jQuery(this).attr('data-network-display-name'),
                        'instantSharing': jQuery(this).attr('data-instant-sharing'),
                        'networkTosGroupId': jQuery(this).attr('data-network-tos-group-id'),
                        'userLang': jQuery('#b2sUserLang').val(),
                        'postId': jQuery('#b2sPostId').val(),
                        'relayCount': jQuery('#b2sRelayCount').val(),
                        'selSchedDate': jQuery('#selSchedDate').val(),
                        'b2sPostType': jQuery('#b2sPostType').val(),
                        'b2sIsDraft': jQuery('#b2sIsDraft').val(),
                        'ignoreTemplate': (jQuery('#b2sIgnoreTemplate').length ? jQuery('#b2sIgnoreTemplate').val() : 0),
                        'isVideo': jQuery('#b2sIsVideo').val(),
                        'assConnected': jQuery('#b2s-ship-ass-connected').val(),
                        'b2s_security_nonce': jQuery('#b2s_security_nonce').val(),
                        'forceReloadFromTemplateChange': forceReloadFromTemplateChange?1:0
                    },
                    beforeSend: function (jqXHR) { // before jQuery send the request we will push it to our array
                        jQuery.xhrPool.push(jqXHR);
                    },
                    complete: function (jqXHR) { // when some of the requests completed it will splice from the array
                        var index = jQuery.xhrPool.indexOf(jqXHR);
                        if (index > -1) {
                            jQuery.xhrPool.splice(index, 1);
                        }
                    },
                    error: function (jqXHR) {
                        var index = jQuery.xhrPool.indexOf(jqXHR);
                        if (index > -1) {
                            jQuery.xhrPool.splice(index, 1);
                        }
                        loadingDummyConnectionFail(networkAuthId, networkId);
                        jQuery('.b2s-network-status-img-loading[data-network-auth-id="' + networkAuthId + '"]').hide();
                        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-network-select-btn-deactivate');
                        jQuery('.b2s-server-connection-fail').show();
                        return true;
                    },
                    success: function (data) {
                   
                        if (data != undefined) {
                            jQuery('.b2s-network-status-img-loading[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                            jQuery('.b2s-network-select-btn[data-network-auth-id="' + data.networkAuthId + '"]').removeClass('b2s-network-select-btn-deactivate');
                            if (data.result == true) {
                                jQuery('.b2s-post-item-loading-dummy[data-network-auth-id="' + data.networkAuthId + '"]').remove();
                                var order = jQuery.parseJSON(jQuery('.b2s-network-navbar-order').val());
                                var pos = order.indexOf(data.networkAuthId.toString());
                                var add = false;
                                for (var i = pos; i >= 0; i--) {
                                    if (jQuery('.b2s-post-list > .b2s-post-item[data-network-auth-id="' + order[i] + '"]').length > 0) {
                                        jQuery('.b2s-post-list > .b2s-post-item[data-network-auth-id="' + order[i] + '"]').after(data.content);
                                        i = -1;
                                        add = true;
                                    }
                                }
                                if (add == false) {
                                    jQuery('.b2s-post-list').prepend(data.content);
                                }
                              
                                if(forceReloadFromTemplateChange && chosenPostFormat!==null){

                                    try{
                                        var postFormatType= getPostFormatTypeByNetwork(networkId,networkType);
                                        changePostFormat(networkId, networkType, chosenPostFormat, networkAuthId, postFormatType, jQuery(this).attr("data-post-wp-type"), true);
                                          
                                    }catch(e){
                                      
                                    }
                                }
                                      
                                activatePortal(data.networkAuthId);
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
                                var today = new Date();
                                if (jQuery('#b2sBlogPostSchedDate').length > 0) {
                                    today.setTime(jQuery('#b2sBlogPostSchedDate').val());
                                }

                                //MaxSchedDate
                                var maxDate = new Date();
                                maxDate.setTime(maxSchedDate);

                                jQuery(".b2s-post-item-details-release-input-date").datepicker({
                                    format: dateFormat,
                                    language: language,
                                    maxViewMode: 2,
                                    todayHighlight: true,
                                    startDate: today,
                                    calendarWeeks: true,
                                    autoclose: true,
                                    endDate: maxDate
                                });


                                jQuery('.b2s-post-item-details-release-input-time').timepicker({
                                    minuteStep: 15,
                                    appendWidgetTo: 'body',
                                    showSeconds: false,
                                    showMeridian: showMeridian,
                                    defaultTime: today, //'current',
                                    snapToStep: true
                                });

                                jQuery(".b2s-post-item-details-release-input-date").datepicker().on('changeDate', function (e) {
                                    checkSchedDateTime(jQuery(this).attr('data-network-auth-id'));
                                    var dataNetworkAuthId = jQuery(this).attr('data-network-auth-id');
                                    var count = jQuery(this).attr('data-network-count');
                                    var maxValue = get3YearsMax('date', dataNetworkAuthId, count);

                                    if (maxValue) {
                                        jQuery('.b2s-network-tos-sched-max-values-alert[data-network-auth-id="' + dataNetworkAuthId + '"]').show();
                                        jQuery(this).datepicker('update', maxValue);
                                        return;
                                    }

                                    jQuery('.b2s-network-tos-sched-max-values-alert[data-network-auth-id="' + dataNetworkAuthId + '"]').hide();
                                });

                                jQuery('.b2s-post-item-details-release-input-time').timepicker().on('changeTime.timepicker', function (e) {
                                    checkSchedDateTime(jQuery(this).attr('data-network-auth-id'));
                                });
                                //Check Text Limit
                                var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + data.networkAuthId + '"]').attr('data-network-text-limit');
                                if (textLimit != "0") {
                                    networkLimitAll(data.networkAuthId, data.networkId, textLimit);
                                } else {
                                    networkCount(data.networkAuthId);
                                }
                                jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').trigger("change");
                                initSceditor(data.networkAuthId);
                                //Bild setzen
                                if (jQuery('#b2s_blog_default_image').val() != "") {
                                    if (jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').length > 0) {
                                        var networkNotAllowGif = jQuery('#b2sNotAllowGif').val().split(";");
                                        var attachmenUrl = jQuery('#b2s_blog_default_image').val();
                                        var attachmenUrlExt = attachmenUrl.substr(attachmenUrl.lastIndexOf('.') + 1);
                                        attachmenUrlExt = attachmenUrlExt.toLowerCase();
                                        if (attachmenUrlExt == 'gif' && jQuery.inArray(networkId, networkNotAllowGif) != -1) {
                                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', jQuery('.b2s-network-default-image').val());
                                        } else {
                                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', jQuery('#b2s_blog_default_image').val());
                                        }
                                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').val() == 1) {
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="0"]').show();
                                            jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="0"]').show();
                                        } else {
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                            jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                        }
                                    }
                                    jQuery('.b2s-image-url-hidden-field').val(jQuery('#b2s_blog_default_image').val());
                                }

                                //Time zone
                                jQuery('.b2s-settings-time-zone-text').html(jQuery('#user_timezone_text').val());
                                //PostFormat
                                if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').length > 0 || data.networkId == 15) {
                                    var postFormatText = JSON.parse(jQuery('.b2sNetworkSettingsPostFormatText').val());
                                    var postFormatType = jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').attr('data-post-format-type');
                                    if (jQuery('#user_version').val() >= 2) {
                                        //Multi Image
                                        if ((jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val() == 1 && ((data.networkId == 1 && (data.networkType == 1 || data.networkType == 2)) || (data.networkId == 3 && (data.networkType == 0 || data.networkType == 1)) || (data.networkId == 2))) || data.networkId == 12 || data.networkId == 45) {
                                            jQuery('.b2s-multi-image-area[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                        }
                                        
                                        jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + data.networkAuthId + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val()]);
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val());

                                        // check for add link (posting templates)
                                        if (jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val() == 1) {
                                            if (jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + data.networkAuthId + '][url]"]').attr('data-add-link') == 1) { // add link true
                                                url = jQuery("#b2sDefault_url").val();
                                                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
                                            } else { // add link false
                                                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val('');
                                            }
                                        }

                                        var isMetaChecked = false;
                                        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
                                        if (typeof data.networkId != 'undefined' && jQuery.inArray(data.networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
                                            isMetaChecked = true;
                                        }
                                        if ((data.networkId == "2" || data.networkId == "24" || data.networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
                                            isMetaChecked = true;
                                        }

                                        if (isMetaChecked && jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val() == "0") {
                                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", false);
                                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", false);
                                            var dataMetaType = jQuery('.b2s-network-select-btn[data-network-auth-id="' + data.networkAuthId + '"]').attr("data-meta-type");
                                            if (dataMetaType == "og") {
                                                jQuery('#b2sChangeOgMeta').val("1");
                                                //TODO change image to OG image
                                            } else {
                                                jQuery('#b2sChangeCardMeta').val("1");
                                            }

                                            //Copy from further item meta tags by same network
                                            jQuery('.change-meta-tag[data-meta-type="' + dataMetaType + '"]').each(function () {
                                                if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == "0" && jQuery(this).attr('data-network-auth-id') != data.networkAuthId) { //other Linkpost by same network
                                                    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                                                    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                                                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src'));
                                                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());

                                                    if (jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').is(":visible")) {
                                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                                        jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                                    } else {
                                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                                        jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                                    }
                                                    return true;
                                                }
                                            });
                                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                            if (data.draft == false) {
                                                if (jQuery('.b2s-network-select-btn[data-network-auth-id="' + data.networkAuthId + '"]').data('meta-type') == 'og' && changedOGImage == true) {
                                                    if (currentOGImage != "") {
                                                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', currentOGImage);
                                                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(currentOGImage);
                                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                                        jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                                    } else {
                                                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', jQuery('#b2sDefaultNoImage').val());
                                                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('#b2sDefaultNoImage').val());
                                                    }
                                                } else {
                                                    jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').trigger("click");
                                                }

                                            }
                                            if ((networkId == "2" || networkId == "24" || networkId == "45")) {
                                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
                                            }
                                        } else {
                                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                            if ((networkId == "2" || networkId == "24" || networkId == "45")) {
                                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').hide();
                                            }
                                        }

                                    } else {
                                        jQuery('.b2s-post-ship-item-post-format-text[data-network-id="' + data.networkId + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val()]);
                                        jQuery('.b2s-post-item-details-post-format[data-network-id="' + data.networkId + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val());
                                    }


                                    if (jQuery('#selSchedDate').val() != "") {
                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                        jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                    }
                                    
                                    var defaultPostFormat= jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val();
                             
                                    //Facebook Image Post: show share as story 
                                    if (networkId == 1 && defaultPostFormat == 1) {
                                        jQuery('.b2s-share-as-story-fields[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                    }

                                    //Twitter TOS 032018 - protected multiple accounts with same content to same time
                                    //delete comment field one more
                                    /*  From Version >8.7.0 Twitter multiple texts allowed again
                                    if (data.networkId == 2 || data.networkId == 45) {
                                       
                                        //set original post
                                        if (jQuery('#b2sTwitterOrginalPost').val() == "") {
                                            jQuery('#b2sTwitterOrginalPost').val(jQuery('.tw-textarea-input[data-network-auth-id="' + data.networkAuthId + '"]').val());
                                        }

                                        if (jQuery('.tw-textarea-input[data-network-id="' + data.networkId + '"]:visible').length >= 1) {
                                            var firstAuth = jQuery('.b2s-post-item[data-network-id="' + data.networkId + '"]:first').attr('data-network-auth-id');
                                            if (firstAuth != data.networkAuthId) {
                                                jQuery('.tw-textarea-input[data-network-auth-id="' + data.networkAuthId + '"]').text("");
                                            } else {
                                                if (jQuery('.tw-textarea-input[data-network-id="' + data.networkId + '"]:visible').length >= 2) {
                                                    jQuery('.tw-textarea-input[data-network-auth-id="' + data.networkAuthId + '"]').text("");
                                                }
                                            }
                                        }
                                    }
                                    */

                                    //Content Curation
                                    if (jQuery('#b2sPostType').val() == 'ex') {
                                        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                        jQuery('.b2sInfoMetaTagModal[data-network-auth-id="' + data.networkAuthId + '"]').attr("style", "display:none !important");
                                        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"]').val() == 0) {

                                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                            jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                        } else {

                                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                            jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                        }
                                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').val() == 1) {

                                            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                            jQuery('.cropper-open[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                        }

                                        //CC Imagepost V6.0.0
                                        if (!forceReloadFromTemplateChange && (jQuery('#b2sExPostFormat').val() == 0 || jQuery('#b2sExPostFormat').val() == 1 || jQuery('#b2sExPostFormat').val() == 2)) {
                                            if (jQuery('#user_version').val() >= 1) {
                                                var exPostFormat = jQuery('#b2sExPostFormat').val();
                                                if (exPostFormat == 2) {
                                                    exPostFormat = 1;
                                                }

                                                openPostFormat(data.networkId, data.networkType, data.networkAuthId, 'ex', false);
                                                changePostFormat(data.networkId, data.networkType, exPostFormat, data.networkAuthId, 'post', 'ex', false);
                                            }
                                        }
                                    }

                                    //Hide OG Area when changed to image from v8.4 onwards
                                    if (jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val() == 1) {

                                        jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').children(':not(div.input-group)').attr('style', 'display: none !important;');
                                    }
                                }

                                //Tumblr Set Post Format
                                if (data.networkId == 4 && (jQuery('#b2sExPostFormat').val() == 0 || jQuery('#b2sExPostFormat').val() == 1 || jQuery('#b2sExPostFormat').val() == 2) || jQuery('#b2sExPostFormat').val() == 3) {
                                    
                                    if (jQuery('#user_version').val() >= 1) {
                                       
                                        var postFormat =  jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-id="4"]').val();

                                        //If not from settings default to HTML/Text
                                        if(postFormat == undefined){
                                            postFormat = 0;
                                        }

                                        if (data.draft == true) {
                                            if(data.draftActions.post_format == "0" || data.draftActions.post_format == "1" || data.draftActions.post_format == "3") {
                                                postFormat = data.draftActions.post_format;
                                            }
                                        }
                                      
                                    }else
                                    {
                                        postFormat = 0;
                                    }

                                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 0 + '"]').removeAttr('selected');
                                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 1 + '"]').removeAttr('selected');
                                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 3 + '"]').removeAttr('selected');
                                    jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + postFormat + '"]').attr('selected', 'selected').change();
                                }

                                //Draft
                                if (data.draft == true) {
                            
                                    if (data.draftActions.post_format == "0" || data.draftActions.post_format == "1") {

                                        jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click', [true]);
                                        jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').addClass('disabled');
                                        jQuery('.b2s-user-network-settings-post-format-area-new[data-network-auth-id="' + data.networkAuthId + '"][data-post-format="' + data.draftActions.post_format + '"]').trigger('click');
                                        jQuery('.b2s-user-network-settings-post-format-apply[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click', [data.networkAuthId]);
                                    }

                                    jQuery.each(data.draftActions.sched_image_url, function (index, value) {
                                        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
                                        if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
                                            if (currentOGImage == '') {
                                                currentOGImage = value;
                                            } else {
                                                value = currentOGImage;
                                            }
                                        }
                                        if (value == "") {
                                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + index + '"]').hide();
                                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + index + '"]').hide();
                                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('src', jQuery('.b2s-network-default-image').val());
                                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val("");
                                        } else {
                                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('src', value);
                                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                        }
                                    });

                                    if (data.draftActions.releaseSelect == "1") {
                                        var selectedFromDraft = 0;
                                        const arr = data?.draftActions?.share_as_story;
                                        if (Array.isArray(arr) && arr.length > 0) {
                                            selectedFromDraft = arr[0];
                                        }
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.releaseSelect);
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').trigger('change',[selectedFromDraft]);

                                        jQuery.each(data.draftActions.date, function (index, value) {
                                            if (index == "1" || index == "2") {
                                                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + (index - 1) + '"]').trigger('click',[true]);
                                            }
                                            jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.time, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.sched_content, function (index, value) {
                                            jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                        });
                                    }
                                    if (data.draftActions.releaseSelect == "2") {

                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.releaseSelect);
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').trigger('change');

                                        jQuery.each(data.draftActions.intervalSelect, function (index, value) {
                                            if (index == "1" || index == "2") {
                                                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + (index - 1) + '"]').trigger('click');
                                            }
                                            jQuery('.b2s-post-item-details-release-input-interval-select[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-interval-select[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.weeks, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-weeks[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-weeks[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.duration_month, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-months[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-months[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.duration_time, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-times[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-times[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.select_day, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-select-day[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-select-day[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.select_timespan, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.mo, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-mo[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.di, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-di[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.mi, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-mi[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.do, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-do[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.fr, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-fr[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.sa, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-sa[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.so, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-lable-day-so[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').attr('checked', true);
                                        });
                                        jQuery.each(data.draftActions.date, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.time, function (index, value) {
                                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                    }

                                    if (data.draftActions.post_relay == "1") {
                                        jQuery('.b2s-post-item-details-relay[data-network-auth-id="' + data.networkAuthId + '"]').attr('checked', true);
                                        jQuery('.b2s-post-item-details-relay[data-network-auth-id="' + data.networkAuthId + '"]').trigger('change');

                                        jQuery.each(data.draftActions.post_relay_account, function (index, value) {
                                            if (index >= 1) {
                                                jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + (index - 1) + '"]').trigger('click');
                                            }
                                            jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                        jQuery.each(data.draftActions.post_relay_delay, function (index, value) {
                                            jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').val(value);
                                            jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + index + '"]').trigger('change');
                                        });
                                    }

                                    while (jQuery('.remove-tag-btn[data-network-auth-id="' + data.networkAuthId + '"]').is(':visible')) {
                                        jQuery('.remove-tag-btn[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click');
                                    }
                                    jQuery('.b2s-post-item-details-tag-input-elem[data-network-auth-id="' + data.networkAuthId + '"]').last().val('');
                                    jQuery.each(data.draftActions.tags, function (index, value) {
                                        if (index >= 1) {
                                            jQuery('.ad-tag-btn[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click');
                                        }
                                        jQuery('.b2s-post-item-details-tag-input-elem[data-network-auth-id="' + data.networkAuthId + '"]').last().val(value);
                                    });

                                    jQuery('.b2s-post-item-details-item-title-input[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.custom_title);

                                    jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.url);
                                    jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + data.networkAuthId + '"]').removeClass('error');


                                    if (data.networkId == 1) {
                                        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.og_title);
                                        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.og_desc);
                                    }

                                    if (data.networkId == 2 || data.networkId == 45) {
                                        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.card_title);
                                        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.card_desc);
                                    }

                                    if (data.draftActions.image_url == "") {
                                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').hide();
                                        jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').attr('src', jQuery('.b2s-network-default-image').val());
                                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').val("");
                                    } else {
                                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').attr('src', data.draftActions.image_url);
                                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="-1"]').val(data.draftActions.image_url);
                                    }
                                    checkSchedDateTime(data.networkAuthId);


                                    if (data.draftActions.multi_image_1 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').attr('src', data.draftActions.multi_image_1);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').val(data.draftActions.multi_image_1);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="1"]').show();
                                    }
                                    if (data.draftActions.multi_image_2 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').attr('src', data.draftActions.multi_image_2);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').val(data.draftActions.multi_image_2);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="2"]').show();
                                    }
                                    if (data.draftActions.multi_image_3 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').attr('src', data.draftActions.multi_image_3);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').val(data.draftActions.multi_image_3);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="3"]').show();
                                    }
                                    if (data.draftActions.multi_image_4 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').attr('src', data.draftActions.multi_image_4);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').val(data.draftActions.multi_image_4);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="4"]').show();
                                    }
                                    if (data.draftActions.multi_image_5 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').attr('src', data.draftActions.multi_image_5);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').val(data.draftActions.multi_image_5);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="5"]').show();
                                    }
                                    if (data.draftActions.multi_image_6 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').attr('src', data.draftActions.multi_image_6);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').val(data.draftActions.multi_image_6);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="6"]').show();
                                    }
                                    if (data.draftActions.multi_image_7 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').attr('src', data.draftActions.multi_image_7);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').val(data.draftActions.multi_image_7);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="7"]').show();
                                    }
                                    if (data.draftActions.multi_image_8 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').attr('src', data.draftActions.multi_image_8);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').val(data.draftActions.multi_image_8);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').hide();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').show();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="8"]').show();
                                    }
                                    if (data.draftActions.multi_image_9 != "") {
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').attr('src', data.draftActions.multi_image_9);
                                        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').val(data.draftActions.multi_image_9);
                                        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').show();
                                        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').show();
                                        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').show();
                                        jQuery('.b2s-add-multi-image[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').hide();
                                        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + data.networkAuthId + '"][data-image-count="9"]').show();
                                    }

                                }

                                //XING Groups
                                if (data.networkId == 19 && data.networkType == 2) {
                                    if (jQuery('.networkKind[data-network-auth-id="' + data.networkAuthId + '"]').val() == '3') {
                                        jQuery('.marketplace_area[data-network-auth-id="' + data.networkAuthId + '"][data-network-id="' + data.networkId + '"]').show();
                                    }
                                }
                                //XING TOS Group
                                if (data.networkId == 19) {
                                    jQuery('.b2s-content-info[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                }

                                if (metaType == 'og' && currentOGImage != '') {
                                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(currentOGImage);
                                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', currentOGImage);
                                }

                            } else {
                                if (data.error == 'nonce') {
                                    jQuery('.b2s-nonce-check-fail').show();
                                }
                                if (typeof data.reason !== "undefined") {
                                    //TOS XING Groups
                                    if (data.reason == 'tos_xing_group_exists') {
                                        b2sTosXingGroupCount--;
                                        deactivatePortal(data.networkAuthId);
                                        jQuery('.b2s-post-item-loading-dummy[data-network-auth-id="' + data.networkAuthId + '"]').remove();
                                        jQuery('#b2s-tos-xing-group-modal').modal('show');
                                        return false;
                                    }
                                    //Invalid Video
                                    if (data.reason == 'invalid_video') {
                                        deactivatePortal(data.networkAuthId, 'video');
                                        jQuery('.b2s-network-select-btn[data-network-id="' + data.networkId + '"]').addClass('b2s-network-select-btn-deactivate');
                                        jQuery('.b2s-network-status-invalid-video[data-network-id="' + data.networkId + '"]').show();
                                        infoNetworkPropertiesError(data.networkAuthId, data.networkId, data.content);
                                        jQuery('.b2s-post-item-loading-dummy[data-network-auth-id="' + data.networkAuthId + '"]').remove();
                                        return false;
                                    }

                                }
                            }
                            checkGifAnimation(data.networkAuthId, data.networkId);

                            if(data.networkId==36){
                                var toggleOn= jQuery("#b2s\\["+data.networkAuthId+"\\]\\[b2s-tiktok-toggle-on\\]").html();
                                
                                if(toggleOn == '1'){
                                    jQuery('.toggle[name="b2s['+data.networkAuthId+'][b2s-tiktok-disclose-toggle]"]').click();
                                    
                                }
                            }
                        }
                        
                        
                    }

                   
                });
            }
        } else {
            //TOS XING Groups
            if ((networkId == 8 || networkId == 19) && networkType == 2) {
                b2sTosXingGroupCount--;
            }
            deactivatePortal(networkAuthId);
        }
    }
    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-url-image', function () {
    var networkAuthId = jQuery(this).attr("data-network-auth-id");
    var networkCountId = jQuery(this).attr("data-network-count");
    if (jQuery('.b2s-select-image-modal-open[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').is(":visible")) {

        var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val();
        var networkId = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').attr("data-network-id");
        var isMetaChecked = false;
        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
        if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
            isMetaChecked = true;
        }
        if ((networkId == "2" || networkId == "24" || networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
            isMetaChecked = true;
        }

        if (postFormat == "0" && (networkId == "1" || networkId == "2" || networkId == "45")) { //isLinkPost for Faceboo or Twitter
            jQuery('.meta-text').hide();
            if (!isMetaChecked) {
                if (networkId == "1") {
                    jQuery('.isOgMetaChecked').show();
                } else {
                    jQuery('.isCardMetaChecked').show();
                }
                jQuery('#b2s-info-change-meta-tag-modal').modal('show');
                return false;
            }
        }

        jQuery('.b2s-select-image-modal-open[data-network-count="' + networkCountId + '"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').trigger('click');
    }
    return false;
});
jQuery(document).on('click', '.b2s-submit-btn-scroll', function () {
    jQuery('.b2s-submit-btn').trigger('click');
});
jQuery(document).on('click', '.b2s-post-ship-item-post-format', function (e, openedFromDraft) {
    
    var openModal= true;
    if(openedFromDraft){
        openModal = false;
    }
    openPostFormat(jQuery(this).attr('data-network-id'), jQuery(this).attr('data-network-type'), jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-post-wp-type'), openModal);
    return false;
});
jQuery(document).on('click', '.b2s-btn-trigger-post-ship-item-post-format', function () {
   
    jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').trigger('click');
    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-release-input-days', function () {
    jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').removeClass('error');
});
jQuery(document).on('change', '.b2s-post-item-details-release-input-time', function () {
    jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').removeClass('error');
});
jQuery(document).on('change', '.b2s-post-item-details-release-input-date', function () {
    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').removeClass('error');
});
jQuery('.b2s-network-details-mandant-select').change(function () {
    hideDuplicateAuths();
    chooseMandant();
});
jQuery(document).on('change', '.b2s-post-item-details-item-group-select', function () {
    if (jQuery(this).attr('data-change-network-display-name') == 'true') {
        var label = jQuery(this.options[this.selectedIndex]).closest('optgroup').prop('label');
        jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').html(label);
        jQuery('.b2s-post-ship-network-display-name[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val(label);
    }
    //Xing groups
    if (jQuery(this).attr('data-network-id') == '19') {
        var option = jQuery('option:selected', this).attr('data-network-kind');
        if (option == '3') { //Marketplace
            jQuery('.marketplace_area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-id="' + jQuery(this).attr('data-network-id') + '"]').show();
        } else {
            jQuery('.marketplace_area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-id="' + jQuery(this).attr('data-network-id') + '"]').hide();
        }
        jQuery('.networkKind[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val(option);
    }

    return false;
});
//select recurrent sched interval mode
jQuery(document).on('change', '.b2s-post-item-details-release-input-interval-select', function () {
    var interval = jQuery(this).val();
    var selectorInput = '[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]';
    var dataCount = jQuery(this).attr('data-network-count');
    releaseChooseInterval(interval, selectorInput, dataCount);
    return false;
});

//select shipping mode
jQuery(document).on('change', '.b2s-post-item-details-release-input-date-select', function (event, selectedFromDraft) {

    var dataNetworkCount = 0;
    var hideTextareaAfter = false;
 
    if (jQuery(this).val() == 0) {
       
        // start - enable assistini buttons for main textare of this network
        if (this.getAttribute('data-network-id') != 4) { // special case tumblr

            var networkAuthId = jQuery(this).data('network-auth-id');
            if ((jQuery(this).attr('data-network-id') == 12 || jQuery(this).attr('data-network-id') == 1) && jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').prop('checked') == true) {
                // hide textarea
                hideTextareaAfter = true;
                // hide all ass buttons
                hideAssButtons(networkAuthId);
            } else {
                // show ass buttons
                showAssButtons(networkAuthId);
            }
        }
        // end

        //TOS Twitter 032018 - none multiple accounts post same content to same time
        jQuery('.b2s-twitter-thread-container[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();

        if (jQuery(this).attr('data-network-id') == 2 || jQuery(this).attr('data-network-id') == 45) {
            jQuery('.b2s-network-tos-sched-warning[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        }
        
        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == 1 || jQuery(this).attr('data-network-id') == 12  || jQuery(this).attr('data-network-id') == 36) {
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').show();
        }
    }
    if (jQuery(this).val() == 2) {
     
        // start - enable assistini buttons for main textare of this network
        if (this.getAttribute('data-network-id') != 4) { // special case tumblr
            var networkAuthId = jQuery(this).data('network-auth-id');
            showAssButtons(networkAuthId);
        }
        // end

        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2sPreFeatureScheduleModal').modal('show');
            return false;
        } else {
            jQuery('.b2s-twitter-thread-container[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();

            //TOS Twitter 032018 - none multiple accounts post same content to same time
            if (jQuery(this).attr('data-network-id') == 2 || jQuery(this).attr('data-network-id') == 45) {
                jQuery('.b2s-network-tos-sched-warning[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
            }

            for (var i = 1; i <= 2; i++) {
                jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + i + '"]').each(function () {
                    if (jQuery(this).prop('checked')) {
                        dataNetworkCount = 1;
                    }
                });
            }
            if (dataNetworkCount == 2) {
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').hide();
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"]').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="2"').show();
            } else if (dataNetworkCount == 1) {
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"').show();
            }

            if ((jQuery(this).attr('data-network-id') == 12 || jQuery(this).attr('data-network-id') == 1) && jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').prop('checked') == true) {
                hideTextareaAfter = true;
            }


        }
    }
    if (jQuery(this).val() == 1) {

        // start - disable assistini buttons for main textarea of this network
        if (! ( this.getAttribute('data-network-id') == 4 || this.getAttribute('data-network-id') == 11 ||  this.getAttribute('data-network-id') == 27 || this.getAttribute('data-network-id') == 38 || this.getAttribute('data-network-id') == 39 ) ) { // special case tumblr
            var networkAuthId = jQuery(this).data('network-auth-id');
            hideAssButtons(networkAuthId);
        }
        // end
        
        //Add Reel and story settings to specific date for Instagram and Facebook
        if ((jQuery(this).attr('data-network-id') == 12 || jQuery(this).attr('data-network-id') == 1) && jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').prop('checked') == true && selectedFromDraft == 1) {
            
            jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').prop('checked', true);
            jQuery('.b2s-post-item-ass-auth-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').hide();
            jQuery('.b2s-post-item-ass-create-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').hide();
            jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').hide();

        }

        if ((jQuery(this).attr('data-network-id') == 12 || jQuery(this).attr('data-network-id') == 1) && jQuery('.b2s-post-item-share-as-reel[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').prop('checked') == true) {
            jQuery('.b2s-post-item-share-as-reel[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').prop('checked', true);
        }
        

        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2sPreFeatureScheduleModal').modal('show');
            return false;
        } else {
            jQuery('.b2s-twitter-thread-container[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();

            //TOS Twitter 032018 - none multiple accounts post same content to same time
            if (jQuery(this).attr('data-network-id') == 2 || jQuery(this).attr('data-network-id') == 45) {
                jQuery('.b2s-network-tos-sched-warning[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
            } else {
                //set orginal edit content for customize sched content
                var content = jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').val();
                jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val(content);

            }

            checkSchedDateTime(jQuery(this).attr('data-network-auth-id'));
            if (dataNetworkCount == 2) {
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').hide();
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"]').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="2"').show();
            } else if (dataNetworkCount == 1) {
                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').hide();
                jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="1"').show();
            }
         
            if (!(jQuery(this).attr('data-network-id')==36)){

                jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
            }
        }

        if (jQuery(this).attr('data-network-id') == 12 || jQuery(this).attr('data-network-id') == 1) {
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').parent().show();
        }
    }
  
    releaseChoose(jQuery(this).val(), jQuery(this).attr('data-network-auth-id'), dataNetworkCount,jQuery(this).attr('data-network-id'));

    if (hideTextareaAfter == true) { // in case of "share_as_story" hide textarea
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').first().hide();
        jQuery('.b2s-post-item-ass-auth-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').first().hide();
        jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
    }

    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-id'), textLimit);
    } else {
        networkCount(jQuery(this).attr('data-network-auth-id'));
    }
});
jQuery(document).on('click', '#b2s-network-sched-post-info-ignore', function () {
    jQuery('#b2sSchedPostInfoIgnore').val("1");
    jQuery('.b2s-submit-btn').trigger("click");
    return false;
}); 
jQuery(document).on('click', '.b2s-re-share-btn', function () {
    jQuery('.panel-group').removeClass('b2s-border-color-warning');
    jQuery(".b2s-settings-user-sched-time-area").show();
    jQuery('#b2s-sidebar-wrapper').show();
    jQuery('.b2s-post-item-info-area').show();
    jQuery('.b2s-post-item-details-message-info').show();
    jQuery('.b2s-post-item-details-edit-area').show();
    jQuery('.b2s-post-item-details-message-result').hide();
    jQuery('.b2s-post-item-details-message-result').html("");
    jQuery(".b2s-post-area").show();
    jQuery('.b2s-publish-area').show();
    jQuery('.b2s-footer-menu').show();
    window.scrollTo(0, 0);
    jQuery('.b2s-reporting-btn-area').hide();
    jQuery('#b2sSchedPostInfoIgnore').val("0");
    //Calendar close for resfresh
    jQuery('.b2s-toogle-calendar-btn').each(function () {
        if (!jQuery(this).hasClass('hide')) {
            var toogleBtnText = jQuery(this).attr('data-toogle-text-btn');
            var currentBtnText = jQuery(this).text();
            jQuery(this).text(toogleBtnText);
            jQuery(this).attr('data-toogle-text-btn', currentBtnText);
            var networkAuthId = jQuery(this).attr('data-network-auth-id');
            var calendar = jQuery('.b2s-post-item-calendar-area[data-network-auth-id="' + networkAuthId + '"]');
            calendar.removeClass('show');
            calendar.addClass('hide');
            jQuery('.b2s-calendar-filter-area[data-network-auth-id="' + networkAuthId + '"]').addClass('hide');
        }
    });

    //TOS XING Group
    jQuery('.b2s-network-select-btn').each(function () {
        if (jQuery(this).children().hasClass('b2s-network-list-active')) {
            if (jQuery(this).attr('data-network-id') == "19" && jQuery(this).attr('data-network-type') == "2") {
                b2sTosXingGroupCount--;
                deactivatePortal(jQuery(this).attr('data-network-auth-id'));
                jQuery('.b2s-network-select-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').addClass('b2s-network-select-btn-deactivate');
                jQuery('.b2s-post-item-loading-dummy[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').remove();
            }
        }
    });

    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-release-input-add', function (event, openedFromDraft) {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var netCount = jQuery(this).attr('data-network-count');
    var networkId = jQuery(this).attr('data-network-id');
    var networkType = jQuery(this).attr('data-network-type');
    var netCountNext = parseInt(netCount) + 1;
    var curMode = jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val();
    jQuery(this).hide();
    jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-release-area-details-row[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
    jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
    
    if (curMode == 1) {

        if(networkId==1 || networkId==12){
            if(openedFromDraft != true){
                if(jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount+ '"]').prop("checked") == true){
            
                    if(jQuery("#b2sIsVideo").val() == "1"){
                        jQuery('.b2s-post-item-sched-customize-text[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').hide();
                        jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext+ '"]').prop("checked", true);
                        hideAssButtons(networkAuthId, netCountNext);
                    }else
                    {
                        jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext+ '"]').prop("checked", true);
                        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').hide();
                        hideAssButtons(networkAuthId, netCountNext);
                    }
                }
            }else{
                if(jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext+ '"]').prop("checked") == true){
       
                    jQuery('.b2s-post-item-sched-customize-text[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').hide();
                    hideAssButtons(networkAuthId, netCountNext);
                    var toggleBtn = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]');
                    toggleBtn.prop('disabled', true);
                    toggleBtn.prop('checked', false);
                    updateToggleCommentValue(toggleBtn);
                    toggleBtn.attr('data-disabled-by-story', 'true');
                    jQuery('.b2s-post-item-details-item-comment-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').hide();
                            
                }
                
            }

            if(jQuery('.b2s-post-item-share-as-reel[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount+ '"]').prop("checked") == true){
                jQuery('.b2s-post-item-share-as-reel[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').prop("checked", true);
            }
            
            // Handle comment area disable if share_as_story is checked
            if(jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount+ '"]').prop("checked") == true){
                var $toggle = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount + '"]');
                var $commentArea = jQuery('.b2s-comment-area-' + networkAuthId + '[data-network-count="' + netCount + '"]');
                if($toggle.length) {
                    $toggle.prop('checked', false).val('0').prop('disabled', true).attr('data-disabled-by-story', 'true');
                    $commentArea.hide();
                }
            }

        }
       
        //since 4.9.0 custom content
        jQuery('.b2s-post-item-details-release-customize-sched-area-details-row[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
    }
    //recurrently
    if (curMode == 2) {
        jQuery('.b2s-post-item-details-release-input-interval-select[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-release-input-interval-select[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
        jQuery('.b2s-post-item-details-release-area-label-duration[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-release-area-div-duration[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').val('1');
        jQuery('.b2s-post-item-details-release-area-label-day[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').show();
        jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
        //since 4.9.0 custom content
        jQuery('.b2s-post-item-details-release-customize-sched-area-details-row[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').hide();
    }

    jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountNext + '"]').focus();
    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + networkAuthId + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(networkAuthId, networkId, textLimit);
    } else {
        networkCount(networkAuthId);
    }
    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-release-input-hide', function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var netCount = jQuery(this).attr('data-network-count');
    var netCountBevor = parseInt(netCount) - 1;
    var selectorInput = '[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCount + '"]'
    jQuery('.b2s-post-item-details-release-area-details-row' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-hide[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountBevor + '"]').show();
    jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + netCountBevor + '"]').show();
    //clean all fields
    jQuery('.b2s-post-item-details-release-input-date' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-time' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).val('');
    jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-days' + selectorInput).prop('checked', false);
    jQuery('.b2s-post-item-details-release-input-days' + selectorInput).prop('disabled', true);
    //since 4.9.0 custom content
    jQuery('.b2s-post-item-details-item-message-input' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-item-message-input' + selectorInput).removeClass('error');
    jQuery('.b2s-post-item-details-release-customize-sched-area-details-row' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).val("0");
    jQuery('.b2s-post-item-details-release-area-label-duration-month' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-area-div-duration-month' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-months' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-months' + selectorInput).val("1");
    jQuery('.b2s-post-item-details-release-area-label-select-day' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).val("1");
    jQuery('.b2s-post-item-details-release-area-label-duration-time' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-area-div-duration-time' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-times' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-times' + selectorInput).val("1");
    jQuery('.b2s-post-item-details-release-area-label-select-timespan' + selectorInput).hide();
    jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).prop('disabled', true);
    jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).val("1");
    return false;
});
jQuery(document).on("keyup", ".complete_network_url", function () {
    var url = jQuery(this).val();
    jQuery(this).removeClass("error");
    if (url.length != "0") {
        if (url.indexOf("http://") == -1 && url.indexOf("https://") == -1) {
            url = "https://" + url;
            jQuery(this).val(url);
        }
    } else if (jQuery(this).hasClass("required_network_url")) {
        if (!((jQuery(this).attr('data-network-id') == 1 || jQuery(this).attr('data-network-id') == 3 || jQuery(this).attr('data-network-id') == 19) && jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + jQuery(this).attr('data-network-auth-id') + ']').val() == 1)) { //Facebook & Linkedin Imagepost don't require Link
            url = jQuery("#b2sDefault_url").val();
            jQuery(this).val(url);
        }
    }
});
jQuery(document).on('click', '.scroll-to-top', function () {
    window.scrollTo(0, 0);
    return false;
});
jQuery(document).on('click', '.scroll-to-bottom', function () {
    window.scrollTo(0, document.body.scrollHeight);
    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-preview-url-reload', function () {
    var re = new RegExp(/^(https?:\/\/)+[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+(?:\.[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=%.ÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß]+$/);
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var url = jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').val();
    if (re.test(url)) {
        jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').removeClass('error');
        jQuery(this).addClass('glyphicon-refresh-animate');
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_ship_item_reload_url',
                'networkId': jQuery(this).attr('data-network-id'),
                'networkAuthId': networkAuthId,
                'postId': jQuery('#b2sPostId').val(),
                'defaultUrl': jQuery('#b2sDefault_url').val(),
                'url': url,
                'postType': jQuery('#b2sPostType').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').removeClass('glyphicon-refresh-animate');
                if (data.result == true) {
                    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').val(data.title);
                    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').val(data.description);

                    //Tumblr Link Field special
                    var networkId= jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"]').data("network-id");
                    if(networkId==4){
                       
                        var postFormat= jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val();
                        if(postFormat == 3){
                            jQuery('.b2s-post-item-details-item-title-input[data-network-auth-id="' + data.networkAuthId + '"]').val(data.title);
                            jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + data.networkAuthId + '"]').val(data.description);
                        }
                    }
                    
                    if (jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('data-network-image-change') == '0') {
                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', data.image);
                    }
                    if (jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('data-network-image-change') == '1') {
                        if (data.image != "") {
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', data.image);
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(data.image);
                        } else {
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + data.networkAuthId + '"]').attr('src', jQuery('#b2sDefaultNoImage').val());
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('#b2sDefaultNoImage').val());
                        }
                        checkGifAnimation(data.networkAuthId, data.networkId);
                    }
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }

        });
    } else {
        jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').addClass('error');
    }
});
jQuery(document).on('click', '.b2s-select-image-modal-open', function () {
    jQuery('.b2s-upload-image-invalid-extension').hide();
    jQuery('.b2s-image-change-meta-network').hide();
    jQuery('.b2s-image-add-this-network').hide();
    jQuery('.b2s-image-change-this-network').show();
    var metaType = jQuery(this).attr('data-meta-type');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + authId + ']').val();
    var networkId = jQuery('.b2s-network-select-btn[data-network-auth-id=' + authId + ']').attr("data-network-id");
    var isMetaChecked = false;
    var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
    if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
        isMetaChecked = true;
    }
    if ((networkId == "2" || networkId == "24" || networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
        isMetaChecked = true;
    }

    if (postFormat == "0" && (networkId == "1" || networkId == "2" || networkId == "45")) { //isLinkPost for Facebook or Twitter
        jQuery('.meta-text').hide();
        if (!isMetaChecked) {
            if (networkId == "1") {
                jQuery('.isOgMetaChecked').show();
            } else {
                jQuery('.isCardMetaChecked').show();
            }
            jQuery('#b2s-info-change-meta-tag-modal').modal('show');
            return false;
        }
    }

    jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', authId).attr('data-network-count', countId).attr('data-network-id', networkId);
    jQuery('.b2s-image-change-all-network').attr('data-network-count', countId).attr('data-network-id', networkId);
    jQuery('.b2s-upload-image').attr('data-network-auth-id', authId).attr('data-network-count', countId);
    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('#b2sInsertImageType').val("0");
    if (typeof metaType !== 'undefined') {
        jQuery('.b2s-image-change-this-network').attr('data-meta-type', metaType);
   
        if (postFormat != "1") {
            var activeMetaNetworks = {};
            var inactiveMetaNetworks = {};
            jQuery('.b2s-network-select-btn[data-meta-type="' + metaType + '"]').each(function () {
                if (jQuery(this).find('.b2s-network-list-active').length > 0) {
                    activeMetaNetworks[jQuery(this).attr('data-network-auth-id')] = jQuery(this).attr('data-network-id');
                } else {
                    inactiveMetaNetworks[jQuery(this).attr('data-network-auth-id')] = jQuery(this).attr('data-network-id');
                }
            });

            jQuery('.b2s-change-meta-image-networks').html('');
            jQuery.each(activeMetaNetworks, function (key, value) {
                jQuery('.b2s-change-meta-image-networks').append('<div style="display: inline-block;"><img class="b2s-meta-image-network-icon" src="' + jQuery('#b2sPortalImagePath').val() + value + '_flat.png' + '"> ' + jQuery('.b2s-network-select-btn[data-network-auth-id="' + key + '"]').attr('data-network-display-name').toUpperCase() + '</div>');
            });
            jQuery.each(inactiveMetaNetworks, function (key, value) {
                jQuery('.b2s-change-meta-image-networks').append('<div style="display: inline-block;"><img class="b2s-meta-image-network-icon b2s-btn-disabled" src="' + jQuery('#b2sPortalImagePath').val() + value + '_flat.png' + '"> ' + jQuery('.b2s-network-select-btn[data-network-auth-id="' + key + '"]').attr('data-network-display-name').toUpperCase() + '</div>');
            });
            jQuery('.b2s-change-meta-image-info').show();

            jQuery('.b2s-image-change-meta-network').show();
            jQuery('.b2s-image-change-this-network').hide();

            jQuery(document).on('click', '.b2s-image-change-meta-network', function () {
                currentOGImage = jQuery('input[name=image_url]:checked').val();
                changedOGImage = true;
                
                if (typeof jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + authId + '"]') != typeof undefined) {
                    jQuery.ajax({
                        url: ajaxurl,
                        type: "POST",
                        dataType: "json",
                        cache: false,
                        async: false,
                        data: {
                            'action': 'b2s_check_image_size_network',
                            'network_id': networkId,
                            'image_url': currentOGImage,
                            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                        },
                        error: function () {
                            jQuery('.b2s-server-connection-fail').show();
                            return false;
                        },
                        success: function (data) {
                            jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + authId + '"]').hide();
                            if (data.error != undefined) {
                                if (data.error == 'nonce') {
                                    jQuery('.b2s-nonce-check-fail').show();
                                } else {
                                    jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + authId + '"]').show();
                                }
                            }
                        }
                    });
                }

                if (jQuery('.b2s-input-hidden[name="action"][value="b2s_edit_save_post"]').length > 0) { //sched or calender view
                    jQuery('.b2s-image-change-this-network').trigger('click');
                } else { //ship view
                    jQuery.each(activeMetaNetworks, function (networkAuthId, networkId) {
                        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() != "1") {
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentOGImage);
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentOGImage);
                            checkGifAnimation(networkAuthId, networkId);
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();
                            if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {
                                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();

                            }
                        }
                    });
                    jQuery.each(inactiveMetaNetworks, function (networkAuthId, networkId) {
                        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() != "1") {
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentOGImage);
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentOGImage);
                            checkGifAnimation(networkAuthId, networkId);
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();
                            if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {
                                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                                jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();

                            }
                        }
                    });
                }
                jQuery('#b2s-network-select-image').modal('hide');
            });
        } else {
            jQuery('.b2s-change-meta-image-info').hide();
        }

    } else {
        jQuery('.b2s-image-change-this-network').attr('data-meta-type', "");
        jQuery('.b2s-change-meta-image-info').hide();
    }
    //set selected image 
    var selImageVal = jQuery('.b2s-post-item-details-url-image[data-network-count="' + countId + '"][data-network-auth-id="' + authId + '"]').attr('src');
    jQuery('#b2s-network-select-image').modal('show');
    jQuery('.checkNetworkImage[data-src="' + selImageVal + '"]').attr('checked', 'checked');
    imageSize();
    return false;
});
jQuery(document).on('click', '.b2s-image-remove-btn', function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkCountId = jQuery(this).attr('data-network-count');
    var defaultImage = jQuery('#b2sDefaultNoImage').val();
    //default
    if (networkCountId == -1) {
        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', defaultImage);
        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val("");
        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').hide();
    } else {
        //customize sched content
        jQuery('.b2s-post-item-details-url-image[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').attr('src', defaultImage);
        jQuery('.b2s-image-url-hidden-field[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val("");
        jQuery('.b2s-image-remove-btn[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.cropper-open[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').hide();
    }

    // remove image error
    jQuery('.b2s-content-info-image[data-network-auth-id="' + networkAuthId + '"]').hide();

    //add check linkpost change meta tag image for this network
    var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val();
    var networkId = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').attr('data-network-id');
    if (typeof postFormat !== typeof undefined && postFormat !== false) {
        if (postFormat == "0") {  //if linkpost
            jQuery('.b2s-post-item-details-post-format[data-network-id=' + networkId + ']').each(function () {
                if (jQuery(this).val() == "0" && jQuery('.b2s-post-ship-item-post-format[data-network-auth-id=' + jQuery(this).attr('data-network-auth-id') + ']').is(":visible") && jQuery(this).attr('data-network-auth-id') != networkAuthId) { //other Linkpost by same network
                    //override this image with current image
                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src', defaultImage);
                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val("");
                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                    jQuery('.cropper-open[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                }
            });
            if (jQuery('.b2s-select-image-modal-open[data-network-auth-id=' + networkAuthId + ']').attr('data-meta-type') == 'og') {
                jQuery('.b2s-select-image-modal-open[data-meta-type="og"]').each(function () {
                    if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + jQuery(this).attr('data-network-auth-id') + ']').val() == "0") {
                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src', defaultImage);
                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val("");
                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                        jQuery('.cropper-open[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                    }
                });
                currentOGImage = '';
                changedOGImage = true;
            }
            //customize sched content
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', defaultImage);
            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val("");
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').hide();
        }
    }
    return false;
});
jQuery(document).on('click', '.b2s-image-change-this-network', function () {
    
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkCountId = jQuery(this).attr('data-network-count');
    var networkId = jQuery(this).attr('data-network-id');
    var currentImage = jQuery('input[name=image_url]:checked').val();
    var label = jQuery("label[for='" + jQuery('input[name=image_url]:checked').attr('id') + "'] :first-child");
    var alt = label.attr('alt');
 
    if (typeof jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"]') != typeof undefined) {
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            async: false,
            data: {
                'action': 'b2s_check_image_size_network',
                'network_id': networkId,
                'image_url': currentImage,
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"]').hide();
                if (data.error != undefined) {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    } else {
                        jQuery('.b2s-content-info-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"]').show();
                    }
                }
            }
        });
    }
    
    if (jQuery('#b2sInsertImageType').val() == '1') { //HTML-Network
        var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
        sceditor.insert("<br /><img src='" + currentImage + "'/><br />");
        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage); //Torial
    } else {
        //default
        if (networkCountId == -1) {
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentImage);
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('alt', alt);
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-img-required');
            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
            jQuery('.b2s-image-alt-hidden-field[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val(alt);
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();

        } else {
            //customize sched content
            jQuery('.b2s-post-item-details-url-image[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').attr('src', currentImage);
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('alt', alt);
            jQuery('.b2s-post-item-details-url-image[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-img-required');
            jQuery('.b2s-image-url-hidden-field[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
            jQuery('.b2s-image-url-hidden-field[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').attr('alt', alt)
            jQuery('.b2s-image-remove-btn[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.cropper-open[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').show();

        }

        if (jQuery(this).attr('data-meta-type') == "og") {
            jQuery('#b2sChangeOgMeta').val("1");
        }
        if (jQuery(this).attr('data-meta-type') == "card") {
            jQuery('#b2sChangeCardMeta').val("1");
        }
       
        //add check linkpost change meta tag image for this network
        var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val();
        var networkId = jQuery('.b2s-network-select-btn[data-network-auth-id=' + networkAuthId + ']').attr('data-network-id');
        if (typeof postFormat !== typeof undefined && postFormat !== false) {
            if (networkId != 12) { // ignore for instagram
                if (postFormat == "0") {  //if linkpost
                    jQuery('.b2s-post-item-details-post-format[data-network-id=' + networkId + ']').each(function () {
                        if (jQuery(this).val() == "0" && jQuery('.b2s-post-ship-item-post-format[data-network-auth-id=' + jQuery(this).attr('data-network-auth-id') + ']').is(":visible") && jQuery(this).attr('data-network-auth-id') != networkAuthId) { //other Linkpost by same network
                            //override this image with current image
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src', currentImage);
                            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').removeClass('b2s-img-required');
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val(currentImage);
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
                            if (networkCountId >= 0) {
                                jQuery('.b2s-image-remove-btn[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                                jQuery('.cropper-open[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
                            }
                        }
                    });
                    //customize sched content
                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentImage);
                    jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-img-required');
                    jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                    if (networkCountId >= 0) {
                        jQuery('.b2s-image-remove-btn[data-network-count="-1"][data-network-auth-id="' + networkAuthId + '"]').hide();
                        jQuery('.cropper-open[data-network-count="-1"][data-network-auth-id="' + networkAuthId + '"]').hide();
                    }
                }
            }
        }
    }
    jQuery('.b2s-upload-image-invalid-extension').hide();
    jQuery('.b2s-upload-image-no-permission').hide();
    jQuery('.b2s-upload-image-free-version-info').hide();
    jQuery('#b2s-network-select-image').modal('hide');
    checkGifAnimation(networkAuthId, networkId);
    return false;
});
jQuery(document).on('change', '.b2s-post-item-details-relay', function () {
    if (jQuery(this).attr('data-user-version') == 0) {
        jQuery(this).prop("checked", false);
        jQuery('#b2sInfoPostRelayModal').modal('show');
        return false;
    }
    jQuery('.b2s-post-item-relay-area-details-row[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
    if (jQuery(this).is(":checked")) {

        if (jQuery('#b2sRelayAccountData').val() != "") {
            jQuery('.b2s-post-item-relay-area-details-ul[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
            jQuery('.b2s-post-item-relay-area-details-row[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            jQuery('.b2s-post-item-details-relay-area-label-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            jQuery('.b2s-post-item-details-relay-area-label-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').removeAttr('disabled');
            jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').removeAttr('disabled');
            jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').show();
            //Relay Html Data
            if (jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').has('option').length == 0) {
                var optionData = window.atob(jQuery('#b2sRelayAccountData').val());
                jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="0"]').append(optionData);
            }

        } else {
            jQuery(this).prop("checked", false);
        }

    } else {
        jQuery('.b2s-post-item-relay-area-details-ul[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-relay-area-details-row[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-details-relay-area-label-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-details-relay-area-label-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').prop('disabled', true);
        jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').prop('disabled', true);
        jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
    }
    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-relay-input-add', function () {
    var netCount = jQuery(this).attr('data-network-count');
    var netCountNext = parseInt(netCount) + 1;
    jQuery(this).hide();
    jQuery('.b2s-post-item-relay-area-details-row[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-area-label-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-area-label-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
    jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').removeAttr('disabled');
    jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    jQuery('.b2s-post-item-details-relay-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').show();
    //Relay Html Data
    if (jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').has('option').length == 0) {
        var optionData = window.atob(jQuery('#b2sRelayAccountData').val());
        jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCountNext + '"]').append(optionData);
    }

    return false;
});
jQuery(document).on('click', '.b2s-post-item-details-relay-input-hide', function () {
    var netCount = jQuery(this).attr('data-network-count');
    jQuery('.b2s-post-item-relay-area-details-row[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-relay-area-label-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-relay-area-label-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    jQuery('.b2s-post-item-details-relay-input-account[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').prop('disabled', true);
    jQuery('.b2s-post-item-details-relay-input-delay[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').prop('disabled', true);
    jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide()
    jQuery('.b2s-post-item-details-relay-input-hide[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + netCount + '"]').hide();
    if (netCount >= 1) {
        var before = netCount - 1;
        jQuery('.b2s-post-item-details-relay-input-add[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="' + before + '"]').show();
    }

    return false;
});
jQuery(document).on('click', '.b2s-image-change-all-network', function () {
    var label = jQuery("label[for='" + jQuery('input[name=image_url]:checked').attr('id') + "'] :first-child");
    var alt = label.attr('alt');

    jQuery('.b2s-post-item-details-item-message-input-allow-html').each(function () {
        var sce = jQuery(this).sceditor('instance');
        if (typeof sce !== 'undefined' && typeof sce.insert !== 'undefined') {
            if (jQuery(sce.getBody().innerHTML).find(".b2s-post-item-details-image-html-src").length > 0) {
                var innerHtml = sce.getBody().innerHTML;
                innerHtml = innerHtml.replace(/class="b2s-post-item-details-image-html-src" src=".*"/, 'class="b2s-post-item-details-image-html-src" src="' + jQuery('input[name=image_url]:checked').val() + '"');
                innerHtml = innerHtml.replace(/src=".*" class="b2s-post-item-details-image-html-src"/, 'class="b2s-post-item-details-image-html-src" src="' + jQuery('input[name=image_url]:checked').val() + '"');
                jQuery('.b2s-post-ship-item-message-delete[data-network-auth-id="' + jQuery(this).data('network-auth-id') + '"]').trigger('click')
                var sce = jQuery(this).sceditor('instance');
                sce.insert(innerHtml);
            } else {
                sce.insert("<br /><img class='b2s-post-item-details-image-html-src' src='" + jQuery('input[name=image_url]:checked').val() + "'/><br />");
            }
        }
    });

    jQuery('.b2s-image-alt-hidden-field').each(function () {
        jQuery(this).val(alt);
    });

    var noGifs = '';
    if (typeof jQuery('input[name=image_url]:checked').val() !== typeof undefined) {
        var attachmenUrlExt = jQuery('input[name=image_url]:checked').val().substr(jQuery('input[name=image_url]:checked').val().lastIndexOf('.') + 1).toLowerCase();
        if (attachmenUrlExt == 'gif') {
            var networkNotAllowGif = jQuery('#b2sNotAllowGif').val().split(";");
            jQuery.each(networkNotAllowGif, function (key, value) {
                noGifs += ':not([data-network-id="' + value + '"])';
            });
        }
    }

    var chosenImage = jQuery('input[name=image_url]:checked').val();
    let imageError = [];

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        async: false,
        data: {
            'action': 'b2s_check_image_size_network_all',
            'image_url': chosenImage,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-content-info-image').hide();
            if (data.error != undefined) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    imageError = data.error;
                }
            }
        }
    });

    jQuery('.b2s-post-item-details-url-image[data-network-image-change="1"]' + noGifs).attr('src', jQuery('input[name=image_url]:checked').val());
    jQuery('#b2s_blog_default_image').val(jQuery('input[name=image_url]:checked').val());
    jQuery('.b2s-post-item-details-url-image' + noGifs).removeClass('b2s-img-required');
    jQuery('.b2s-image-url-hidden-field' + noGifs).val(jQuery('input[name=image_url]:checked').val());
    jQuery('.b2s-image-remove-btn' + noGifs).show();
    jQuery('.cropper-open' + noGifs).show();
    jQuery('.b2s-post-item-details-release-input-date-select' + noGifs).each(function () {
        var itemNetworkId = jQuery(this).data('network-id');
        var itemNetworkAuthId = jQuery(this).data('network-auth-id');
        if (jQuery.inArray(itemNetworkId, imageError) != -1) {
            jQuery('.b2s-content-info-image[data-network-id="' + itemNetworkId + '"][data-network-auth-id="' + itemNetworkAuthId + '"]').show();
        }

        if (jQuery(this).val() == 1) {
            jQuery('.b2s-image-remove-btn[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]' + noGifs).hide();
            jQuery('.cropper-open[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]' + noGifs).hide();
        }
    });

    jQuery('.b2s-upload-image-invalid-extension').hide();
    jQuery('.b2s-upload-image-no-permission').hide();
    jQuery('.b2s-upload-image-free-version-info').hide();
    jQuery('.b2sChangeOgMeta').val("1");
    jQuery('.b2sChangeCardMeta').val("1");
    jQuery('#b2s-network-select-image').modal('hide');

    currentOGImage = jQuery('input[name=image_url]:checked').val();
    changedOGImage = true;
    if (typeof currentOGImage !== typeof undefined) {
        var attachmenUrlExt = currentOGImage.substr(currentOGImage.lastIndexOf('.') + 1);
        attachmenUrlExt = attachmenUrlExt.toLowerCase();
        if (attachmenUrlExt == 'gif') {
            jQuery('.b2s-image-url-hidden-field').each(function () {
                checkGifAnimation(jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-network-id'));
            });
        }
    }
    return false;
});
jQuery(document).on('click', '.b2s-upload-image', function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
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
            var validExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            var networkNotAllowGif = jQuery('#b2sNotAllowGif').val().split(";");
            var networkId = jQuery('input[name="b2s[' + networkAuthId + '][network_id]"]').val();
            var attachment = wpMedia.state().get('selection').first().toJSON();
            var attachmenUrl = attachment.url;
            var attachmenUrlExt = attachmenUrl.substr(attachmenUrl.lastIndexOf('.') + 1);
            attachmenUrlExt = attachmenUrlExt.toLowerCase();
            if (jQuery.inArray(attachmenUrlExt, validExtensions) == -1 || (attachmenUrlExt == 'gif' && jQuery.inArray(networkId, networkNotAllowGif) != -1)) {
                jQuery('#b2s-network-select-image').modal('show');
                jQuery('.b2s-upload-image-invalid-extension').show();
                jQuery('#b2s-upload-image-invalid-extension-file-name').html('<span class="glyphicon glyphicon-ban-circle"></span> ' + attachment.name + '.' + attachmenUrlExt + '<br>');
                jQuery('.b2s-choose-image-no-image-info-text').hide();
                jQuery('.b2s-choose-image-no-image-extra-btn').hide();
                return false;
            }
            var count = parseInt(jQuery('.b2s-choose-image-count').val());
            count = count + 1;
            jQuery('.b2s-choose-image-count').val(count);
            var content = '<div class="b2s-image-item">' +
                    '<div class="b2s-image-item-thumb">' +
                    '<label for="b2s-image-count-' + count + '">' +
                    '<img class="img-thumbnail networkImage" alt="' + attachment.alt + '" src="' + attachment.url + '">' +
                    '</label>' +
                    '</div>' +
                    '<div class="b2s-image-item-caption text-center">' +
                    '<div class="b2s-image-item-caption-resolution clearfix small"></div>' +
                    '<input type="radio" value="' + attachment.url + '" data-src="' + attachment.url + '" class="checkNetworkImage" name="image_url" id="b2s-image-count-' + count + '">' +
                    '</div>' +
                    '</div>';
            jQuery('.b2s-image-choose-area').html(jQuery('.b2s-image-choose-area').html() + content);
//            jQuery('.b2s-image-change-btn-area').show();
            jQuery('.b2s-choose-image-no-image-info-text').hide();
            jQuery('.b2s-choose-image-no-image-extra-btn').hide();
            jQuery('.b2s-upload-image-invalid-extension').hide();
            jQuery('input[name=image_url]:last').prop("checked", true);
            jQuery('#b2s-network-select-image').modal('show');
            imageSize();
        });
        wpMedia.on('close', function () {
            jQuery('#b2s-network-select-image').modal('show');
        });
    } else {
        jQuery('.b2s-upload-image-no-permission').show();
    }
    return false;
});

jQuery(document).on('change', '.checkNetworkImage', function () {
    var networkNotAllowGif = jQuery('#b2sNotAllowGif').val().split(";");
    var attachmenUrlExt = jQuery('input[name=image_url]:checked').val().substr(jQuery('input[name=image_url]:checked').val().lastIndexOf('.') + 1);
    attachmenUrlExt = attachmenUrlExt.toLowerCase();
    if (attachmenUrlExt == 'gif') {
        var networkAuthId = jQuery('.b2s-upload-image').attr('data-network-auth-id');
        var networkId = jQuery('input[name="b2s[' + networkAuthId + '][network_id]"]').val();
        if (jQuery.inArray(networkId, networkNotAllowGif) != -1) {
            jQuery('.b2s-image-change-this-network').attr('disabled', true);
            jQuery('.b2s-image-change-all-network').attr('disabled', true);
            jQuery('.b2s-upload-image-invalid-extension').show();
            jQuery('#b2s-upload-image-invalid-extension-file-name').html();
        } else {
            jQuery('.b2s-image-change-this-network').attr('disabled', false);
            jQuery('.b2s-image-change-all-network').attr('disabled', false);
        }
        jQuery('.b2s-gif-support-info').show();
    } else {
        jQuery('.b2s-image-change-this-network').attr('disabled', false);
        jQuery('.b2s-image-change-all-network').attr('disabled', false);
        jQuery('.b2s-gif-support-info').hide();
    }
});
jQuery('#b2s-network-select-image').on('shown.bs.modal', function () {
    jQuery('.checkNetworkImage').trigger('change');
});
jQuery(document).on('click', '.b2s-upload-image-free-version', function () {
    jQuery('.b2s-upload-image-free-version-info').show();
});

jQuery("#b2sNetworkSent").keypress(function (e) {
    if (e.keyCode == 13 && e.target.tagName == "INPUT")
        return false;
});
jQuery.validator.addMethod("checkUrl", function (value, element, regexp) {
    var re = new RegExp(regexp);
    return this.optional(element) || re.test(value);
}, "Invalid Url");
jQuery.validator.addClassRules("b2s-post-item-details-item-url-input", {
    checkUrl: /^(https?:\/\/)+[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+(?:\.[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=%.ÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß]+$/
});
//Twitter TOS 032018 - protected multiple accounts with same content to same time ( by all connections)
jQuery.validator.addMethod("unique", function (value, element, params) {
    var curNetworkAuthId = jQuery(element).attr('data-network-auth-id');
    var curNetworkId = jQuery(element).attr('data-network-id');
    var prefix = params;
    var selector = jQuery.validator.format("[name!='{0}'][unique='{1}'][data-network-id='" + curNetworkId + "']", element.name, prefix);
    var matches = new Array();

    jQuery('.b2s-unique-content[data-network-id="' + curNetworkId + '"]').hide();
    jQuery('.tw-textarea-input').removeClass('error');

    jQuery(selector).each(function (index, item) {
        //none disabled elements || ignore default content if curSchedMode=1
        if (!jQuery(item).is(':not(:disabled)') || !jQuery(item).is(':visible') || !jQuery('.b2s-post-item[data-network-auth-id="' + jQuery(item).attr('data-network-auth-id') + '"]').is(':visible') || (jQuery(item).attr('data-network-count') == -1 && jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + jQuery(item).attr('data-network-auth-id') + '"] option[value="1"]:selected').length > 0)) {
            return true;
        }
        if (jQuery.trim(value) == jQuery.trim(jQuery(item).val())) {
            jQuery('.b2s-unique-content[data-network-auth-id="' + jQuery(item).attr('data-network-auth-id') + '"]').show();
            matches.push(item);
        }
    });
    if (matches.length != 0) {
        jQuery('.b2s-unique-content[data-network-auth-id="' + curNetworkAuthId + '"]').show();
    }
    return true;
});


jQuery.validator.classRuleSettings.unique = {
    unique: true
};

jQuery.validator.addMethod("checkTags", function (value, element, test) {
    var allowed_tags = ['p', 'h1', 'h2', 'br', 'i', 'em', 'b', 'a', 'img', 'span'];
    var tags = value.match(/(<([^>]+)>)/ig);
    if (tags !== null && tags.length > 0) {
        if (jQuery(element).hasClass('b2s-post-item-details-item-message-input-allow-html')) {
            for (var i = 0; i < tags.length; i++) {
                var allowed_count = 0;
                for (var e = 0; e < allowed_tags.length; e++) {
                    var regex = new RegExp("<\s*(\/)?" + allowed_tags[e] + "(( [^>]*>)|[>])");
                    if (tags[i].match(regex) != null) {
                        allowed_count = 1;
                    }
                }
                if (allowed_count == 0) {
                    return false;
                }
            }
        } else {
            return false;
        }
    }
    return true;
});

jQuery.validator.addClassRules('b2s-post-item-details-item-message-input', {'checkTags': true});
jQuery.validator.addClassRules('b2s-post-item-details-release-input-date-select', {'checkSched': true});
jQuery.validator.addClassRules('b2s-post-item-details-item-title-input', {required: true});
jQuery.validator.addMethod('checkSched', function (value, element, rest) {
    if (jQuery(element).is(':not(:disabled)') && jQuery(element).val() != 0) {
        var networkAuthId = jQuery(element).attr('data-network-auth-id');
        if (jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + networkAuthId + '"]').val() == "") {
            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + networkAuthId + '"]').addClass('error');
            return false;
        } else {
            jQuery('.b2s-post-item-details-release-input-time[data-network-auth-id="' + networkAuthId + '"]').removeClass('error');
        }
        if (jQuery(element).val() == 1) {
            if (jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + networkAuthId + '"]').val() == "") {
                jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + networkAuthId + '"]').addClass('error');
                return false;
            } else {
                jQuery('.b2s-post-item-details-release-input-date[data-network-auth-id="' + networkAuthId + '"]').removeClass('error');
            }

        } else {
            var maxCount = jQuery('.b2s-post-item-details-release-input-daySelect[data-network-auth-id="' + networkAuthId + '"]').length;
            jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + networkAuthId + '"]').removeClass('error');
            var daySelect = false;
            var daySelectErrorCount = 0;
            for (var count = 0; count < maxCount; count++) {
                if (jQuery('.b2s-post-item-details-release-input-lable-day-mo[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + count + '"]').is(':not(:disabled)')) {
                    daySelect = false;
                    jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + count + '"]').each(function () {
                        if (jQuery(this).is(':checked')) {
                            daySelect = true;
                        }
                    });
                    if (daySelect === false) {
                        daySelectErrorCount += 1;
                        jQuery('.b2s-post-item-details-release-input-days[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + count + '"]').addClass('error');
                    }
                }
            }
            if (daySelectErrorCount != 0) {
                return false;
            }
        }
    }
    return true;
});

function checkMaxInputVarsLimit(form) {
    var maxInputVars = parseInt(jQuery('#max_input_vars').val(), 10);
    var formInputCount = jQuery(form).serializeArray().length;
    if (!isNaN(maxInputVars) && maxInputVars > 0 && formInputCount > maxInputVars) {
        jQuery('.b2s-max-input-vars-value').text(maxInputVars);
        jQuery('#b2sMaxInputVarsModal').modal('show');
        return false;
    }
    return true;
}

jQuery("#b2sNetworkSent").validate({
    ignore: "",
    errorPlacement: function (error, element) {
        return false;
    },
    submitHandler: function (form) {

        //Check for Max input vars
        if (checkMaxInputVarsLimit(form) == false) {
            return false;
        }

        //Licence Condition
        if (checkLicenceCondition() == false) {
            return false;
        }
        if (checkNetworkSelected() == false) {
            return false;
        }
        if (checkPostSchedOnBlog() == false) {
            return false;
        }
        if (checkImageByImageNetworks() == false) {
            return false;
        }
        var send= jQuery(form).serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();

        var userDate = new Date();
        var pubDate = userDate.getFullYear() + "-" + padDate(userDate.getMonth() + 1) + "-" + padDate(userDate.getDate()) + " " + padDate(userDate.getHours()) + ":" + padDate(userDate.getMinutes()) + ":" + padDate(userDate.getSeconds());
        jQuery('#publish_date').val(pubDate);
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-post-area").hide();
        jQuery(".b2s-settings-user-sched-time-area").hide();
        jQuery('#b2s-sidebar-wrapper').hide();

        jQuery('.b2s-post-item-info-area').hide();
        jQuery.xhrPool.abortAll();
        jQuery('.b2s-server-connection-fail').hide();
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
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                    return false;
                }
                if (data.error == 'permission') {
                    jQuery('.b2s-no-permission').show();
                    jQuery(".b2s-loading-area").hide();
                    jQuery(".b2s-post-area").show();
                    jQuery('#b2s-sidebar-wrapper').show();
                    return false;
                }
                jQuery(".b2s-loading-area").hide();

                //Onboarding
                if (typeof jQuery('#b2s-toastee-paused').val() != "undefined") {
                    jQuery('.b2s-onboarding-share-step-1').hide();
                    jQuery('.b2s-onboarding-share-step-2').show();
                }

                //Licence / Network Condition
                if (data.result == true) {
                    jQuery('#current_licence_open_sched_post_quota').html(data.currentOpenSchedLimit);
                    jQuery('#current_licence_open_daily_post_quota').val(data.currentOpenDailyLimit);

                    if (data.currenOpenDailyLimit <= 0) {
                        jQuery('.b2s-current-licence-open-daily-post-quota-sidebar-info').show();
                    }

                    jQuery('#current_network_open_sched_post_quota').html(data.currentNetwork45OpenSchedLimit);
                    jQuery('#current_network_open_daily_post_quota').val(data.currentNetwork45OpenDailyLimit);

                    if (data.currentNetwork45OpenDailyLimit <= 0) {
                        jQuery('.b2s-network-licence-open-daily-post-quota-sidebar-info').show();
                    }

                }


                var content = data.content;
                for (var i = 0; i < content.length; i++) {
                    jQuery('.b2s-post-item-details-message-info[data-network-auth-id="' + content[i]['networkAuthId'] + '"]').hide();
                    jQuery('.b2s-post-item-details-edit-area[data-network-auth-id="' + content[i]['networkAuthId'] + '"]').hide();
                    jQuery('.b2s-post-item-details-message-result[data-network-auth-id="' + content[i]['networkAuthId'] + '"]').show();
                    jQuery('.b2s-post-item-details-message-result[data-network-auth-id="' + content[i]['networkAuthId'] + '"]').html(content[i]['html']);
                    jQuery('.b2s-content-info').hide();
                    if (typeof content[i]['approve'] !== typeof undefined) {
                        jQuery('.panel-group[data-network-auth-id="' + content[i]['networkAuthId'] + '"]').addClass('b2s-border-color-warning');
                    }
                }
                jQuery(".b2s-post-area").show();
                jQuery('.b2s-publish-area').hide();
                jQuery('.b2s-footer-menu').hide();
                window.scrollTo(0, 0);
                jQuery('.b2s-empty-area').hide();
                jQuery('.b2s-reporting-btn-area').show();
                wp.heartbeat.connectNow();
            }
        });
        return false;
    }
});
jQuery('#b2s-network-list-modal').on('show.bs.modal', function (e) {
    jQuery('.b2s-network-list-modal-mandant').html(jQuery(".b2s-network-details-mandant-select option:selected").text());
});
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
});

jQuery(document).on('click', '.b2s-loading-area-save-profile-change', function () {
    var selectedAuth = new Array();
    jQuery('.b2s-network-list.b2s-network-list-active').each(function () {
        selectedAuth.push(jQuery(this).parents('.b2s-network-select-btn').attr('data-network-auth-id'));
    });
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_ship_navbar_save_settings',
            'mandantId': jQuery('.b2s-network-details-mandant-select').val(),
            'selectedAuth': selectedAuth,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-ship-settings-save').show();
                window.scrollTo(0, 0);
                var mandantId = jQuery('.b2s-network-details-mandant-select').val();
                jQuery('.b2s-network-list').each(function () {
                    var jsonMandantIds = jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id');
                    if (jsonMandantIds !== undefined) {
                        var jsonMandantIds = jQuery.parseJSON(jsonMandantIds);
                        if (jsonMandantIds.indexOf(mandantId) !== -1 && !jQuery(this).hasClass('b2s-network-list-active')) {
                            //remove
                            var newMandant = new Array();
                            jQuery(jsonMandantIds).each(function (index, item) {
                                if (item !== mandantId) {
                                    newMandant.push(item);
                                }
                            });
                            jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id', JSON.stringify(newMandant));
                        } else if (jsonMandantIds.indexOf(mandantId) == -1 && jQuery(this).hasClass('b2s-network-list-active')) {
                            //add
                            jsonMandantIds.push(mandantId);
                            jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id', JSON.stringify(jsonMandantIds));
                        }
                    }
                });
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
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
        } else if (typeof data.action !== typeof undefined && data.action == 'assAuth') {
            //ASS
            //TODO Store via ajax, API/POST.php access_token in user_tool
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                cache: false,
                async: false,
                data: {
                    'action': 'b2s_ass_auth_save',
                    'ass_access_token': data.ass_access_token,
                    'ass_words_open': data.ass_words_open,
                    'ass_words_total': data.ass_words_total,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (authData) {
                    authData = JSON.parse(authData);
                    if (authData.result == true) {
                        //Success
                        jQuery('.b2s-stepwizard-btn-circle').addClass('b2s-ass-color').removeClass('btn-default').addClass('btn-danger');
                        jQuery('.b2s-ass-auth-step-1-content').hide();
                        jQuery('.b2s-ass-auth-step-3-content').show();

                        jQuery('.b2s-post-item-ass-auth-btn').hide();
                        jQuery('.b2s-post-item-ass-create-btn').show();
                        jQuery('.b2s-post-item-ass-reset-btn').show();
                        jQuery('.b2s-post-item-ass-setting-btn').show();
                        jQuery('#b2s-ship-ass-connected').val(1);

                        //background
                        jQuery('#sidebar_ship_ass_words_open').text(data.ass_words_open);
                        jQuery('#sidebar_ship_ass_words_total').text(data.ass_words_total);
                        jQuery('#b2s-ship-ass-words-open').val(data.ass_words_open);
                        jQuery('#b2s-ship-ass-words-total').val(data.ass_words_total);
                        jQuery('.b2s-ass-sidebar-account').show();
                    }
                }
            });



        } else {
            loginSuccess(data.networkId, data.networkType, data.displayName, data.networkAuthId, data.mandandId, data.instant_sharing);
        }
    }
});


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



jQuery.xhrPool.abortAll = function () { // our abort function
    jQuery(this).each(function (idx, jqXHR) {
        jqXHR.abort();
    });
    jQuery.xhrPool.length = 0
};
function loadingDummyShow(networkAuthId, networkId) {
    jQuery('.b2s-post-item-connection-fail-dummy[data-network-auth-id="' + networkAuthId + '"]').remove();
    var html = '<div class="b2s-post-item b2s-post-item-loading-dummy" data-network-auth-id="' + networkAuthId + '">'
            + '<div class="panel panel-group">'
            + '<div class="panel-body">'
            + '<div class="b2s-post-item-area">'
            + '<div class="b2s-post-item-details">'
            + '<div class="b2s-loader-impulse b2s-loader-impulse-md b2s-post-item-loading-impulse-area">'
            + '<img class="img-responsive" src="' + jQuery('#b2sPortalImagePath').val() + networkId + '_flat.png" alt="">'
            + '</div>'
            + '<div class="clearfix"></div>'
            + '<div class="text-center"><small>'
            + jQuery('#b2sJsTextLoading').val()
            + '</small></div>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
    var order = jQuery.parseJSON(jQuery('.b2s-network-navbar-order').val());
    var pos = order.indexOf(networkAuthId.toString());
    var add = false;
    for (var i = pos; i >= 0; i--) {
        if (jQuery('.b2s-post-item[data-network-auth-id="' + order[i] + '"]').length > 0) {
            jQuery('.b2s-post-item[data-network-auth-id="' + order[i] + '"]').after(html);
            i = -1;
            add = true;
        }
    }
    if (add == false) {
        jQuery('.b2s-post-list').prepend(html);
    }
}


jQuery(document).on('click', '.b2s-post-item-info-network-properties-error-btn', function () {
    jQuery('.b2s-post-item-info-network-properties-error[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
    var visible = false;
    jQuery('.b2s-post-network-properties-error-list').find('.b2s-post-item').each(function () {
        if (jQuery(this).is(":visible")) {
            visible = true;
        }
    });

    if (!visible) {
        jQuery('.b2s-post-list').find('.b2s-post-item').each(function () {
            if (jQuery(this).is(":visible")) {
                visible = true;
            }
        });
    }

    if (!visible) {
        jQuery('.b2s-empty-area').show();
    }
});

function infoNetworkPropertiesError(networkAuthId, networkId, errorReason) {
    var html = '<div class="b2s-post-item b2s-post-item-info-network-properties-error" data-network-auth-id="' + networkAuthId + '">'
            + '<div class="panel panel-group">'
            + '<div class="panel-body">'
            + '<button type="button" class="b2s-post-item-info-network-properties-error-btn close" data-network-auth-id="' + networkAuthId + '">×</button>'
            + '<div class="b2s-post-item-area">'
            + '<div class="b2s-post-item-thumb hidden-xs">'
            + '<img class="img-responsive" src="' + jQuery('#b2sPortalImagePath').val() + networkId + '_flat.png" alt="">'
            + '</div>'
            + '<div class="b2s-post-item-details pull-left">'
            + jQuery('.b2s-network-details[data-network-auth-id="' + networkAuthId + '"]').html()
            + '<div class="alert alert-warning">'
            + errorReason
            + '</div>'
            + '</div>'
            + '<div class="clearfix"></div>'
            + '</div>'
            + '</div>'
            + '</div>';
    jQuery('.b2s-post-network-properties-error-list').append(html);
}

function loadingDummyConnectionFail(networkAuthId, networkId) {
    var html = '<div class="b2s-post-item b2s-post-item-connection-fail-dummy" data-network-auth-id="' + networkAuthId + '">'
            + '<div class="panel panel-group">'
            + '<div class="panel-body">'
            + '<div class="b2s-post-item-area">'
            + '<div class="b2s-post-item-details">'
            + '<div class="b2s-post-item-details-portal-img-area">'
            + '<img class="img-responsive" src="' + jQuery('#b2sPortalImagePath').val() + networkId + '_flat.png" alt="">'
            + '</div>'
            + '<div class="clearfix"></div>'
            + '<div class="text-center"><small>'
            + jQuery('#b2sJsTextConnectionFail').val()
            + '</small>'
            + '<br/>'
            + '<a class="btn btn-link btn-sm" target="_blank" href="' + jQuery('#b2sJsTextConnectionFailLink').val() + '">' + jQuery('#b2sJsTextConnectionFailLinkText').val() + '</a>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>'
            + '</div>';
    jQuery('.b2s-post-item-loading-dummy[data-network-auth-id="' + networkAuthId + '"]').replaceWith(html);
}

function init(firstrun) {
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
    var today = new Date();
    if (jQuery('#b2sBlogPostSchedDate').length > 0) {
        today.setTime(jQuery('#b2sBlogPostSchedDate').val());
    }

    jQuery(".b2s-post-item-details-release-input-date").datepicker({
        format: dateFormat,
        language: language,
        maxViewMode: 2,
        todayHighlight: true,
        startDate: today,
        endDate: today,
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
    checkNetworkSelected();
    //imageCheck();
    if (firstrun == true) {
        jQuery(window).scroll(function () {
            submitArea();
        });
        jQuery('.b2s-post-item-details-release-input-date-select').each(function () {
          
            releaseChoose(jQuery(this).val(), jQuery(this).attr('data-network-auth-id'), 0);
        });

        //V5.0.0 Content Curation set selected Profile
        if (jQuery(".b2s-network-details-mandant-select option[value='" + jQuery('#selProfile').val() + "']").length > 0) {
            jQuery('.b2s-network-details-mandant-select').val(jQuery('#selProfile').val());
        }
        hideDuplicateAuths();
        chooseMandant();
    }
}


function initSceditor(networkAuthId) {
    var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
    if (typeof sceditor !== 'undefined' && typeof sceditor.destroy == 'function') {
        sceditor.destroy();
    }
    if (jQuery('.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]').data('network-id') == 14) {// Torial is only HTML Network that dose not support Emojis
        var toolbar = "h1,h2,bold,italic,link,unlink,custom-image|source";
    } else {
        var toolbar = "h1,h2,bold,italic,link,unlink,custom-image,custom-emoji|source";
    }
    jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor({
        plugins: 'xhtml',
        toolbar: toolbar,
        autoUpdate: true,
        emoticonsEnabled: false
    });
    var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
    if (typeof sceditor !== 'undefined' && typeof sceditor.destroy == 'function') {
        sceditor.height(500);
        sceditor.width(window.getComputedStyle(document.querySelector('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]')).width);
        sceditor.keyUp(function () {
            jQuery('.b2s-post-item-countChar[data-network-auth-id="' + networkAuthId + '"]').html(jQuery(this).prev('.b2s-post-item-details-item-message-input').prevObject[0].getBody().textContent.length);
        });
        jQuery('.b2s-post-item-countChar[data-network-auth-id="' + networkAuthId + '"]').html(jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').prev('.b2s-post-item-details-item-message-input').prevObject[0]._sceditor.getBody().textContent.length);
    }

    sceditor.bind("valuechanged", function() {
       
        let content = sceditor.val();
        var contentBefore= content;
        content = content.replace(/<(?!\/?(h1|h2|p|br|i|em|b|a|img|span)\b)[^>]*>/gi, "");
        if(contentBefore != content){ 
            sceditor.val(content);
        }
        
    });

}

function submitArea() {
    if (jQuery('.b2s-publish-area').length > 0) {
        if (jQuery(window).scrollTop() + jQuery(window).height() >= jQuery('.b2s-publish-area').offset().top) {
            jQuery(".b2s-footer-menu").hide();
        } else {
            jQuery(".b2s-footer-menu").show();
        }
    }
}

function imageSize() {
    jQuery('.networkImage').each(function () {
        var width = this.naturalWidth;
        var height = this.naturalHeight;
        jQuery(this).parents('.b2s-image-item').find('.b2s-image-item-caption-resolution').html(width + 'x' + height);
        if (width == 0)
        {
            setTimeout(function () {
                imageSize();
            }, 50);
        }
    });
}

function navbarDeactivatePortal(reason) {
    if (reason == "image") {
        var portale = Array(6, 7, 12);
        for (var i = 0; i <= portale.length; i++) {
            jQuery('.b2s-network-select-btn[data-network-id="' + portale[i] + '"]').addClass('b2s-network-select-btn-deactivate');
            jQuery('.b2s-network-status-no-img[data-network-id="' + portale[i] + '"]').show();
        }
    }
}

function navbarActivatePortal(reason) {
    if (reason == "image") {
        var portale = Array(6, 7, 12);
        for (var i = 0; i <= portale.length; i++) {
            jQuery('.b2s-network-select-btn[data-network-id="' + portale[i] + '"]').removeClass('b2s-network-select-btn-deactivate');
            jQuery('.b2s-network-status-no-img[data-network-id="' + portale[i] + '"]').hide();
        }
    }
}

function deactivatePortal(networkAuthId, postType = 'text') {
    var selector = '.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]';
    jQuery(selector).hide();
    jQuery(selector).find('.form-control').each(function () {
        jQuery(this).attr("disabled", "disabled");
    });
    jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').children().removeClass('b2s-network-list-active').find('.b2s-network-status-img').addClass('b2s-network-hide');
    checkNetworkSelected(postType);
    submitArea();
    return true;
}

function activatePortal(networkAuthId, check) {
    var selector = '.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]';
    jQuery(selector).show();
    jQuery(selector).find('.form-control').each(function () {

        if (!jQuery(this).hasClass('b2s-post-item-details-item-message-input')) {
            jQuery(this).removeAttr("disabled", "disabled");
        }

        if ((jQuery(this).hasClass('b2s-post-item-details-release-input-weeks')) ||
                (jQuery(this).hasClass('b2s-post-item-details-release-input-date')) ||
                (jQuery(this).hasClass('b2s-post-item-details-release-input-time')) ||
                (jQuery(this).hasClass('b2s-post-item-details-release-input-days')) ||
                (jQuery(this).hasClass('b2s-post-item-details-relay-input-delay')) ||
                (jQuery(this).hasClass('b2s-post-item-details-relay-input-account'))) {
            if (!jQuery(this).is(':visible')) {
                jQuery(this).prop('disabled', true);
            }
        }
    });
    //Customize Content
    if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {
        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
        jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').prop('disabled', true);
        jQuery(selector).find('.b2s-post-item-details-item-message-input').each(function () {
            if (jQuery(this).is(':visible')) {
                jQuery(this).removeAttr("disabled", "disabled");
            }
        });
    } else {
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').removeAttr("disabled", "disabled");
    }
    jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').children().addClass('b2s-network-list-active').find('.b2s-network-hide').removeClass('b2s-network-hide');
    checkNetworkSelected();
    submitArea();
}


function checkLicenceCondition() {

    //Licence Condition 
    if (typeof jQuery('#current_licence_open_daily_post_quota') != "undefined" && typeof jQuery('#current_licence_open_sched_post_quota') != "undefined") {
        var dailyLimit = jQuery('#current_licence_open_daily_post_quota').val();
        var schedLimit = jQuery('#current_licence_open_sched_post_quota').html();
        jQuery('.licence-condition-daily-modal-title').hide();
        jQuery('.licence-condition-sched-modal-title').hide();

        var isDirectPost = 0;
        var isSchedPost = 0;

        if (jQuery('#user_version').val() > 0) {
            jQuery('.b2s-post-item-details-release-input-date-select').each(function () {
                if (jQuery(this).is(":visible")) {
                    if (jQuery(this).val() > 0) {
                        isSchedPost = 1;
                    } else {
                        isDirectPost = 1;
                    }
                }
            });

            if (isSchedPost == 1 && schedLimit <= 0) {
                jQuery('.licence-condition-sched-modal-title').show();
                jQuery('.b2s-licence-condition-modal').modal('show');
                return false;
            }
            //direct share
            if (isDirectPost == 1 && dailyLimit <= 0) {
                jQuery('.licence-condition-daily-modal-title').show();
                jQuery('.b2s-licence-condition-modal').modal('show');
                return false;
            }
        } else {
            //direct share
            if (dailyLimit <= 0) {
                jQuery('.licence-condition-daily-modal-title').show();
                jQuery('.b2s-licence-condition-modal').modal('show');
                return false;
            }
        }
    }
    return true;
}

function checkNetworkSelected(postType = 'text') {
//überprüfen ob mindestens ein PostItem vorhanden und sichtbar ist
    var visible = false;
    jQuery('.b2s-post-list').find('.b2s-post-item').each(function () {
        if (jQuery(this).is(":visible")) {
            visible = true;
        }
    });
    if (!visible) {
        jQuery('.b2s-post-network-properties-error-list').find('.b2s-post-item').each(function () {
            if (jQuery(this).is(":visible")) {
                visible = true;
            }
        });
    }
    if (!visible) {
        jQuery('.b2s-publish-area').hide();
        jQuery('.b2s-footer-menu').hide();
        jQuery('.b2s-empty-area').show();
        if (postType == 'video') {
            jQuery('.b2s-empty-area').hide();
        }
        return false;
    } else {
        jQuery('.b2s-publish-area').show();
        if (jQuery('.b2s-publish-area').length > 0) {
            if (jQuery(window).scrollTop() + jQuery(window).height() < jQuery('.b2s-publish-area').offset().top) {
                jQuery('.b2s-footer-menu').show();
            }
        }
        jQuery('.b2s-empty-area').hide();
        return true;
}
}

function checkPostSchedOnBlog() {
    if (jQuery('#b2sBlogPostSchedDate').length > 0 && jQuery('#b2sPostType').val() == "") {
        if (jQuery('#b2sSchedPostInfoIgnore').val() == "0") {
            if (jQuery('.b2s-post-item-details-release-input-date-select option[value="0"]:selected').length > 0) {
                jQuery('#b2s-network-sched-post-info').modal("show");
                return false;
            }
        }
    }
    return true;
}

function checkImageByImageNetworks() {
    var result = true;
    jQuery('.b2sOnlyWithImage').each(function () {
        if (jQuery(this).is(":visible")) {
            var networkAuthId = jQuery(this).attr('data-network-auth-id');
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]:visible').each(function () {
                if (jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + jQuery(this).attr('data-network-count') + '"]').val() == "") {
                    if (!jQuery('#b2s-network-select-image').hasClass('in')) {
                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + jQuery(this).attr('data-network-count') + '"]').addClass('b2s-img-required');
                        jQuery('.b2s-image-change-this-network').attr('data-network-auth-id', networkAuthId);
                        jQuery('.b2s-upload-image').attr('data-network-auth-id', networkAuthId);
                        jQuery('#b2s-network-select-image').modal('show');
                        imageSize();
                        window.scrollTo(0, (jQuery(this).offset().top - 45));
                        result = false;
                    }
                }
            });
        }
    });
    return result;
}


function releaseChoose(choose, dataNetworkAuthId, dataNetworkCount, networkId=0) {


    var selectorInput = '[data-network-auth-id="' + dataNetworkAuthId + '"]';
    jQuery('.b2s-post-item-details-release-area-details-row' + selectorInput).hide();
    if (choose == 0) {

        //since 4.8.0 customize content
        if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-customize-content') == "1") {
            jQuery('.b2s-post-item-details-item-message-input' + selectorInput + '[data-network-count="-1"]').removeAttr('disabled');
            jQuery('.b2s-post-item-details-item-message-area' + selectorInput + '[data-network-count="-1"]').show();

            //Facebook Stories hide Share as story when link selected
            if (jQuery('.b2s-post-item-details-post-format[name="b2s[' + dataNetworkAuthId + '][post_format]"]').val() == 0 && jQuery('.b2s-post-item-details-post-format[name="b2s[' + dataNetworkAuthId + '][post_format]"]').attr('data-network-id') == 1) {

                jQuery('.b2s-share-as-story-fields' + selectorInput + '[data-network-count="-1"]').hide();
            }

            jQuery('.b2s-post-item-details-url-image' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-select-image-modal-open' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-image-remove-btn' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-post-original-area' + selectorInput).addClass('col-sm-7').addClass('col-lg-9');
            jQuery('.b2s-post-tool-area' + selectorInput).show();
            
            // Show comment area for mode 0 only if toggle is checked (and not disabled by story) or there is a comment
            var $toggle = jQuery('.b2s-toggle-comment' + selectorInput + '[data-network-count="-1"]');
            var $commentArea = jQuery('.b2s-comment-area-' + dataNetworkAuthId + '[data-network-count="-1"]');
            var $commentInput = jQuery('.b2s-post-item-details-item-comment-input[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="-1"]');
            var $shareAsStory = jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="-1"]');
            jQuery('.b2s-toggle-comment-wrapper' + selectorInput + '[data-network-count="-1"]').show();
            
            // Check if share_as_story is checked - if so, disable and hide comment area
            if ($shareAsStory.length && $shareAsStory.prop('checked')) {
                $toggle.prop('checked', false).val('0').prop('disabled', true).attr('data-disabled-by-story', 'true');
                $commentArea.hide();
            } else if ($toggle.attr('data-disabled-by-story') !== 'true' && ($toggle.prop('checked') || ($commentInput.length && $commentInput.val() && $commentInput.val().trim() !== ''))) {
                $commentArea.show();
            } else {
                $commentArea.hide();
            }
        }

        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-customize-sched-area-details-row' + selectorInput).hide();
        jQuery('.b2s-post-item-sched-customize-text' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).val("0");
        //monthly- duration month
        jQuery('.b2s-post-item-details-release-area-div-duration-month' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-month' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months' + selectorInput).prop('disabled');
        //monthly- publish day (select-day)
        jQuery('.b2s-post-item-details-release-area-label-select-day' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).hide();
        //own period- duration times
        jQuery('.b2s-post-item-details-release-area-div-duration-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times' + selectorInput).prop('disabled');
        //own period- timespan
        jQuery('.b2s-post-item-details-release-area-label-select-timespan' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-days' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-daySelect' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-add' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-details-ul' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-save-settings' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-area-details-ul' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-save-settings-label' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-interval' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-day' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-duration' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-interval' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-day' + selectorInput).hide();
    } else if (choose == 1) {
       
        //Take text char count from default text area
        var $source = jQuery(".b2s-post-item-countChar[data-network-count=-1][data-network-auth-id='" + dataNetworkAuthId + "']");
        var $target = jQuery(".b2s-post-item-countChar[data-network-count=0][data-network-auth-id='" + dataNetworkAuthId + "']");

        if ($source.length && $target.length) {
            var sourceHtml = $source.html();
            if (typeof sourceHtml !== "undefined") {
                $target.html(sourceHtml);
            }
        }
         
        //since 4.8.0 customize content
        if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-customize-content') == "1") {
            jQuery('.b2s-post-item-details-item-message-input' + selectorInput + '[data-network-count="-1"]').prop('disabled', true);
            jQuery('.b2s-post-item-details-item-message-area' + selectorInput + '[data-network-count="-1"]').hide();
            
            if(networkId != 36){
                
                jQuery('.b2s-post-item-details-url-image' + selectorInput + '[data-network-count="-1"]').hide();
                jQuery('.b2s-select-image-modal-open' + selectorInput + '[data-network-count="-1"]').hide();
                jQuery('.b2s-image-remove-btn' + selectorInput + '[data-network-count="-1"]').hide();
                jQuery('.cropper-open' + selectorInput + '[data-network-count="-1"]').hide();

            }
            jQuery('.b2s-post-original-area' + selectorInput).removeClass('col-sm-7').removeClass('col-lg-9');
            jQuery('.b2s-post-tool-area' + selectorInput).hide();
            //TOS Network Twitter
            if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-id') == "2" || jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-id') == "45") {
                jQuery('.b2s-post-ship-item-copy-original-text' + selectorInput + '[data-network-count="0"]').trigger('click');
            }
            
            // Hide default comment area
            jQuery('.b2s-toggle-comment-wrapper' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.b2s-comment-area-' + dataNetworkAuthId + '[data-network-count="-1"]').hide();
        }
      
        jQuery('.b2s-post-item-details-release-area-details-row' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).prop('disabled', true);
        for (var i = 0; i <= dataNetworkCount; i++) {
            jQuery('.b2s-post-item-details-release-area-details-row[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            //since 4.8.0 customize content
            jQuery('.b2s-post-item-details-release-customize-sched-area-details-row[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            // Show scheduled comment areas only if toggle is checked (and not disabled by story) or there is a comment
            var $toggleSched = jQuery('.b2s-toggle-comment' + selectorInput + '[data-network-count="' + i + '"]');
            var $commentAreaSched = jQuery('.b2s-comment-area-' + dataNetworkAuthId + '[data-network-count="' + i + '"]');
            var $commentInputSched = jQuery('.b2s-post-item-details-item-comment-input[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + i + '"]');
            var $shareAsStorySched = jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + i + '"]');
            jQuery('.b2s-toggle-comment-wrapper' + selectorInput + '[data-network-count="' + i + '"]').show();
            
            // Check if share_as_story is checked - if so, disable and hide comment area
            if ($shareAsStorySched.length && $shareAsStorySched.prop('checked')) {
                $toggleSched.prop('checked', false).val('0').prop('disabled', true).attr('data-disabled-by-story', 'true');
                $commentAreaSched.hide();
                hideAssButtons(dataNetworkAuthId, i);
                jQuery('.b2s-post-item-details-item-message-emoji-btn[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + i + '"]').hide();
                jQuery('.b2s-post-item-details-item-comment-emoji-btn[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + i + '"]').hide();
            } else if ($toggleSched.attr('data-disabled-by-story') !== 'true' && ($toggleSched.prop('checked') || ($commentInputSched.length && $commentInputSched.val() && $commentInputSched.val().trim() !== ''))) {
                $commentAreaSched.show();
            } else {
                $commentAreaSched.hide();
            }
        }
        jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks' + selectorInput).prop('disabled');
        //monthly- duration month
        jQuery('.b2s-post-item-details-release-area-div-duration-month' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-month' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months' + selectorInput).prop('disabled');
        //monthly- publish day (select-day)
        jQuery('.b2s-post-item-details-release-area-label-select-day' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-select-day' + selectorInput).hide();
        //own period- duration times
        jQuery('.b2s-post-item-details-release-area-div-duration-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times' + selectorInput).prop('disabled');
        //own period- timespan
        jQuery('.b2s-post-item-details-release-area-label-select-timespan' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan' + selectorInput).prop('disabled');
        //new since v.4.5.0
        jQuery('.b2s-post-item-details-release-area-label-duration' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-interval-select' + selectorInput).val("0");
        jQuery('.b2s-post-item-details-release-input-daySelect' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-days' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-add' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-details-ul' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-save-settings' + selectorInput).prop('disabled', false);
        jQuery('.b2s-post-item-details-release-save-settings-label' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-interval' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-date' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-time' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-day' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-duration' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-interval' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-date' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-time' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-day' + selectorInput).hide();
    } else if (choose == 2) {

        //since 4.8.0 customize content
        if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-customize-content') == "1") {
            jQuery('.b2s-post-item-details-item-message-input' + selectorInput + '[data-network-count="-1"]').removeAttr('disabled');
            jQuery('.b2s-post-item-details-item-message-area' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-post-item-details-url-image' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-select-image-modal-open' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-image-remove-btn' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.cropper-open' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-post-original-area' + selectorInput).addClass('col-sm-7').addClass('col-lg-9');
            jQuery('.b2s-post-tool-area' + selectorInput).show();
            
            // Show comment area for mode 2 only if toggle is checked (and not disabled by story) or there is a comment
            var $toggle2 = jQuery('.b2s-toggle-comment' + selectorInput + '[data-network-count="-1"]');
            var $commentArea2 = jQuery('.b2s-comment-area-' + dataNetworkAuthId + '[data-network-count="-1"]');
            var $commentInput2 = $commentArea2.find('.b2s-post-item-details-item-comment-input');
            var $shareAsStory2 = jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="-1"]');
            jQuery('.b2s-toggle-comment-wrapper' + selectorInput + '[data-network-count="-1"]').show();
            
            // Check if share_as_story is checked - if so, disable and hide comment area
            if ($shareAsStory2.length && $shareAsStory2.prop('checked')) {
                $toggle2.prop('checked', false).val('0').prop('disabled', true).attr('data-disabled-by-story', 'true');
                $commentArea2.hide();
            } else if ($toggle2.attr('data-disabled-by-story') !== 'true' && ($toggle2.prop('checked') || ($commentInput2.val() && $commentInput2.val().trim() !== ''))) {
                $commentArea2.show();
            } else {
                $commentArea2.hide();
            }
        }

        jQuery('.b2s-post-item-details-release-area-details-row' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-customize-sched-area-details-row' + selectorInput).hide();
        jQuery('.b2s-post-item-sched-customize-text' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-date' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-time' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-input-add' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-daySelect' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-details-ul' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-save-settings' + selectorInput).prop('disabled', false);
        jQuery('.b2s-post-item-details-release-save-settings-label' + selectorInput).hide();
        for (var i = 0; i <= dataNetworkCount; i++) {
            jQuery('.b2s-post-item-details-release-area-details-row[data-network-count="' + i + '"]' + selectorInput).show();
            //new since v4.5.0
            jQuery('.b2s-post-item-details-release-input-interval-select[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-interval-select[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-date[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            jQuery('.b2s-post-item-details-release-input-daySelect[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-time[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + i + '"]' + selectorInput).show();
            jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
            jQuery('.b2s-post-item-details-release-input-days[data-network-count="' + i + '"]' + selectorInput).removeAttr('disabled');
        }
        jQuery('.b2s-post-item-details-release-area-label-duration' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-interval' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-date' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-time' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-day' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-duration' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-interval' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-date' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-div-time' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-div-day' + selectorInput).show();
    }

    var showMeridian = true;
    if (jQuery('#b2sUserTimeFormat').val() == 0) {
        showMeridian = false;
    }

    jQuery('.b2s-post-item-details-release-input-time').timepicker({
        minuteStep: 15,
        appendWidgetTo: 'body',
        showSeconds: false,
        showMeridian: showMeridian,
        defaultTime: 'current',
        snapToStep: true
    });
}


function releaseChooseInterval(interval, selectorInput, dataCount) {

    //change view
    if (interval == 0) { //weekly,default
        // show
        //select days
        jQuery('.b2s-post-item-details-release-input-days[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled');
        jQuery('.b2s-post-item-details-release-area-label-day[data-network-count="' + dataCount + '"]' + selectorInput).show();
        //duration weeks
        jQuery('.b2s-post-item-details-release-area-div-duration[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-duration[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled');
        //hide
        //monthly- duration month
        jQuery('.b2s-post-item-details-release-area-div-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //monthly- publish day (select-day)
        jQuery('.b2s-post-item-details-release-area-label-select-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        //own period- duration times
        jQuery('.b2s-post-item-details-release-area-div-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //own period- timespan
        jQuery('.b2s-post-item-details-release-area-label-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
    }

    if (interval == 1) { //monthly
        // show
        //duration month
        jQuery('.b2s-post-item-details-release-area-div-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled');
        //publish day (select-day)
        jQuery('.b2s-post-item-details-release-area-label-select-day[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled');
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).show();
        //hide
        //weekly - select days
        jQuery('.b2s-post-item-details-release-input-days[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-area-label-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        //weekly- duration weeks
        jQuery('.b2s-post-item-details-release-area-div-duration[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //own period- duration times
        jQuery('.b2s-post-item-details-release-area-div-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //own period- timespan
        jQuery('.b2s-post-item-details-release-area-label-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
    }

    if (interval == 2) { //own period
        // show
        //duration times
        jQuery('.b2s-post-item-details-release-area-div-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-area-label-duration-time[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).show(); //select
        jQuery('.b2s-post-item-details-release-input-times[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled'); //select
        //timespan
        jQuery('.b2s-post-item-details-release-area-label-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).show();
        jQuery('.b2s-post-item-details-release-input-select-timespan[data-network-count="' + dataCount + '"]' + selectorInput).removeAttr('disabled');
        //hide
        //weekly - select days
        jQuery('.b2s-post-item-details-release-input-days[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled', true);
        jQuery('.b2s-post-item-details-release-area-label-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        //weekly- duration weeks
        jQuery('.b2s-post-item-details-release-area-div-duration[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-weeks[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //monthly- duration month
        jQuery('.b2s-post-item-details-release-area-div-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-area-label-duration-month[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-months[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        //monthly- publish day (select-day)
        jQuery('.b2s-post-item-details-release-area-label-select-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).prop('disabled');
        jQuery('.b2s-post-item-details-release-input-select-day[data-network-count="' + dataCount + '"]' + selectorInput).hide();
    }
}
function removeAllTags(networkAuthId) {
    jQuery('.b2s-post-item-details-tag-input-elem[data-network-auth-id="' + networkAuthId + '"]').remove();
}

function addTagwithContent(networkAuthId, content) {
    var selector = ".b2s-post-item-details-tag-input-elem[data-network-auth-id='" + networkAuthId + "']";
    var container = jQuery(".b2s-post-item-details-tag-input[data-network-auth-id='" + networkAuthId + "']");
    
    if(jQuery(selector).length > 0) {     
        jQuery(selector).last().after('<input class="form-control b2s-post-item-details-tag-input-elem" data-network-auth-id="' + networkAuthId + '" value="'+content+'" name="b2s[' + networkAuthId + '][tags][]">');
    }else
    {
        container.append('<input class="form-control b2s-post-item-details-tag-input-elem" data-network-auth-id="' + networkAuthId + '" value="'+content+'" name="b2s[' + networkAuthId + '][tags][]">');
    }
 
    jQuery(".remove-tag-btn[data-network-auth-id='" + networkAuthId + "'").show();
    var limit = jQuery(".b2s-post-item-details-tag-limit[data-network-auth-id='" + networkAuthId + "']").val();

    if (typeof limit !== typeof undefined && limit !== false) {
        if (jQuery(selector).length >= limit) {
            jQuery(".ad-tag-btn[data-network-auth-id='" + networkAuthId + "'").hide();
        }
    }
}

function addTag(networkAuthId) {
    var selector = ".b2s-post-item-details-tag-input-elem[data-network-auth-id='" + networkAuthId + "']";
    jQuery(selector).last().after('<input class="form-control b2s-post-item-details-tag-input-elem" data-network-auth-id="' + networkAuthId + '" value="" name="b2s[' + networkAuthId + '][tags][]">');
    jQuery(".remove-tag-btn[data-network-auth-id='" + networkAuthId + "'").show();
    var limit = jQuery(".b2s-post-item-details-tag-limit[data-network-auth-id='" + networkAuthId + "']").val();
    if (typeof limit !== typeof undefined && limit !== false) {
        if (jQuery(selector).length >= limit) {
            jQuery(".ad-tag-btn[data-network-auth-id='" + networkAuthId + "'").hide();
        }
    }
}

function removeTag(networkAuthId) {
    var selector = ".b2s-post-item-details-tag-input-elem[data-network-auth-id='" + networkAuthId + "']";
    jQuery(selector).last().remove();
    if (jQuery(selector).length === 1) {
        jQuery(".remove-tag-btn[data-network-auth-id='" + networkAuthId + "'").hide();
    }
    var limit = jQuery(".b2s-post-item-details-tag-limit[data-network-auth-id='" + networkAuthId + "']").val();
    if (typeof limit !== typeof undefined && limit !== false) {
        if (jQuery(selector).length < limit) {
            jQuery(".ad-tag-btn[data-network-auth-id='" + networkAuthId + "'").show();
        }
    }
}

jQuery(document).on('change', '.b2s-twitter-thread', function () {
    var checkbox = jQuery(this);
    var networkAuthId = jQuery(this).attr("data-network-auth-id");
    var networkCountId = jQuery(this).attr('data-network-count');
    var networkId = jQuery(this).attr('data-network-id');
    var twitterLimit = 280;
    var textField = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']");
  
    if (checkbox.is(':checked')) {
        jQuery(".b2s-insert-tweet-break-button").attr("disabled", false);
        var networkCountText = "networkCount('" + networkAuthId + "');";
        textField.attr("onkeyup", networkCountText)
        disableCommentByThread(networkAuthId, "-1");

    } else {
        jQuery(".b2s-insert-tweet-break-button").attr("disabled", true);
        var networkId = jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id');
        var networkLimitAllText = "networkLimitAll('" + networkAuthId + "','" + networkId + "','" + twitterLimit + "');";
        textField.attr("onkeyup", networkLimitAllText);
        textField.val(textField.val().replaceAll("{new tweet}", ""));
        networkLimitAll(networkAuthId, networkId, twitterLimit);
        jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").hide();

        enableCommentByThread(networkAuthId, "-1");
        
    }
});

jQuery(document).on('click', '.b2s-insert-tweet-break-button', function () {

    var networkAuthId = jQuery(this).attr("data-network-auth-id");
    var networkCountId = jQuery(this).attr('data-network-count');
    var checkbox = jQuery(".b2s-twitter-thread[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']");
    if (checkbox.is(':checked')) {
        var input = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']");
        var cursorPosition = input.prop("selectionStart");
        var text = input.val();
        text = text.substring(0, cursorPosition)
                + "{new tweet}"
                + text.substring(cursorPosition + 1, text.length);
        input.val(text);
    }


    return false;
});

jQuery(document).on('change', '.b2s-twitter-thread-sched', function () {
    var checkbox = jQuery(this);
    var networkAuthId = jQuery(this).attr("data-network-auth-id");
    var networkCountId = jQuery(this).attr('data-network-count');
    var twitterLimit = 280;
    var textField = jQuery(".b2s-post-item-sched-customize-text[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']")


    if (checkbox.is(':checked')) {
        jQuery(".b2s-insert-tweet-break-button-sched").attr("disabled", false);
        var networkCountText = "networkCount('" + networkAuthId + "');";
        textField.attr("onkeyup", networkCountText)

       disableCommentByThread(networkAuthId, networkCountId);

    } else {
        jQuery(".b2s-insert-tweet-break-button-sched").attr("disabled", true);
        var networkId = jQuery(".b2s-post-item-sched-customize-text[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id');
        var networkLimitAllText = "networkLimitAll('" + networkAuthId + "','" + networkId + "','" + twitterLimit + "');";
        textField.attr("onkeyup", networkLimitAllText)
        textField.val(textField.val().replaceAll("{new tweet}", ""));
        networkLimitAll(networkAuthId, networkId, twitterLimit);
        jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").hide();

        // Re-enable comment area when thread is unchecked
        enableCommentByThread(networkAuthId, networkCountId);

    }
});

jQuery(document).on('click', '.b2s-insert-tweet-break-button-sched', function () {
    var networkAuthId = jQuery(this).attr("data-network-auth-id");
    var networkCountId = jQuery(this).attr('data-network-count');
    var checkbox = jQuery(".b2s-twitter-thread-sched[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']");

    if (checkbox.is(':checked')) {
        var input = jQuery(".b2s-post-item-sched-customize-text[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']");
        var cursorPosition = input.prop("selectionStart");
        var text = input.val();
        text = text.substring(0, cursorPosition)
                + "{new tweet}"
                + text.substring(cursorPosition + 1, text.length);
        input.val(input.val() + "{new tweet}");

    }
    return false;
});


function removeTweetBreaks(text, textField) {
    textField.val(textField.val().replaceAll("{new tweet}", ""));
}

function networkLimitAll(networkAuthId, networkId, limit) {

    var networkCountId = -1; //default;
    if (jQuery(':focus').length > 0) {
        var attr = jQuery(':focus').attr('data-network-count');
        if (typeof attr !== typeof undefined && attr !== false) {
            networkCountId = attr;
        }
    }

    var regX = /(<([^>]+)>)/ig;
    var url = jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val();
    var text = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").val();
    jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").removeClass("error");
    if (typeof url !== typeof undefined && url !== false) {
        if (url.length != "0") {
            if (url.indexOf("http://") == -1 && url.indexOf("https://") == -1) {
                url = "https://" + url;
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
            }
        } else if (jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").hasClass("required_network_url")) {
            if (!((jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 1 || jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 3 || jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 19) && jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val() == 1)) { //Facebook & Linkedin Imagepost don't require Link
                url = jQuery("#b2sDefault_url").val();
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
            }
        }
    }

    if (typeof text !== typeof undefined && text !== false) {
        var textLength = text.length;
        var newText = text;
        if (networkId == "2" || networkId == "45") { //twitter
            if (url != undefined && url.length != "0") {
                limit = limit - 26;
            }
            var textStripped = text.replaceAll("{new tweet}", "");
            textLength = textStripped.length;
        }
        if (networkId == "3" || networkId == "38" || networkId == "44") { //linkedin(3) - mastodon(38) - threads(44)
            if (url != undefined && url.length != "0") {
                limit = limit - url.length;
            }
        }
        if (networkId == "12") { //instagram
            var matches = text.match(/(#[^# ]{1,})/g);
            if (matches != null && matches.length > 30) {
                jQuery('.b2s-content-info[data-network-auth-id="' + networkAuthId + '"]').show();
                jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").addClass("warning");
            } else {
                jQuery('.b2s-content-info[data-network-auth-id="' + networkAuthId + '"]').hide();
                jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").removeClass("warning");
            }
        }
        if (networkId == "19" && jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').attr('data-network-type') == 0 && jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() == 1) { //xing
            if (url != undefined && url.length != "0") {
                limit = limit - url.length;
            }
        }
        // if (networkId == "38") { //mastodon
        //     if (url != undefined && url.length != "0") {
        //         limit = limit - url.length;
        //     }
        // }
        if (networkId == "43" && jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() == 1) { //bluesky
            if (url != undefined && url.length != "0") {
                limit = limit - getNetwork43UrlLength(url);
            }
        }


        if (textLength >= limit) {
            newText = text.substring(0, limit);
            var pos = getCaretPos(this);
            jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").val(newText.replace(regX, ""));
            setCaretPos(this, pos);
            var text = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").val();
            var textLength = text.length;
        }
        if (networkId == "38") { //mastodon
            var mastodonLength = textLength + (url != undefined && url.length != '0' ? url.length : 0);
            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(mastodonLength);
        } else if (networkId == "43" && jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() == 1) { //bluesky
            var blueskyLength = textLength + getNetwork43UrlLength(url);
            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(blueskyLength);
        } else {
            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength);
        }
    }
}

//BlueSky cuts urls after 16 chars into the path
function getNetwork43UrlLength(url = "") {
    if (url != "") {
        var hostLength = 0;
        var pathLength = 0;
        var parsedUrl = new URL(url);
        if (parsedUrl.hostname) {
            hostLength = parsedUrl.hostname.length;
        }
        if (parsedUrl.pathname) {
            // Exclude the trailing slash if it's the only character in the path
            if (parsedUrl.pathname === "/") {
                parsedUrl.pathname = "";
            }
            pathLength = parsedUrl.pathname.length;
            if (pathLength > 16) {
                pathLength = 16;
            }
        }
        return hostLength + pathLength + 1; // +1 /n
    }
    return 0;

}

function networkCount(networkAuthId) {
    
    var twitterLimit = 280;
    var networkCountId = -1; //default;
    if (jQuery(':focus').length > 0) {
        var attr = jQuery(':focus').attr('data-network-count');
        if (typeof attr !== typeof undefined && attr !== false) {
            networkCountId = attr;
        }
    }
    var url = jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val();
    var text = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").val();

    var tumblr= jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").prop("data-network-id");

    jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").removeClass("error");
    if (typeof url !== typeof undefined && url !== false) {
        if (url.length != "0") {
            if (url.indexOf("http://") == -1 && url.indexOf("https://") == -1) {
                url = "https://" + url;
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
            }
            if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "2" || jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "45") { //twitter
                twitterLimit = twitterLimit - 26;
            }
        } else if (jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").hasClass("required_network_url")) {
            if (!((jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 1 || jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 3 || jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == 19) && jQuery('.b2s-post-item-details-post-format[data-network-auth-id=' + networkAuthId + ']').val() == 1)) { //Facebook & Linkedin Imagepost don't require Link
                url = jQuery("#b2sDefault_url").val();
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
            }
        }
    }

    if (typeof text !== 'undefined' && jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').length == 0) {
        var textLength = text.length;
        if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "2" || jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "45") {
            var textStripped = text.replaceAll("{new tweet}", "");
            textLength = textStripped.length;
        }

        jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength);
        if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "2" || jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "45") {
            var threadCount = Math.ceil(textLength / twitterLimit);

            var splitText = text.split("{new tweet}");
            if (splitText.length > 1) {
                var baseCount = 0;
                for (var i = 0; i < splitText.length; i++) {

                    var tempCount = Math.ceil(splitText[i].length / twitterLimit);
                    baseCount += tempCount;

                }
                threadCount = baseCount;
            }
            jQuery(".b2s-post-item-count-threads[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(threadCount);

            if (threadCount >= 2) {
                jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").show();
                jQuery(".b2s-post-item-show-thread-count-sched[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").show();

            } else {
                jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").hide();
                jQuery(".b2s-post-item-show-thread-count-sched[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").hide();

            }
        }

    }
    if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "12") { //instagram
        var matches = text.match(/(#[^# ]{1,})/g);
        if (matches != null && matches.length > 30) {
            jQuery('.b2s-content-info[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").addClass("warning");
        } else {
            jQuery('.b2s-content-info[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").removeClass("warning");
        }
    }
}


function getCaretPos(domElem) {
    var pos;
    if (document.selection) {
        domElem.focus();
        var sel = document.selection.createRange();
        sel.moveStart("character", -domElem.value.length);
        pos = sel.text.length;
    } else if (domElem.selectionStart || domElem.selectionStart == "0")
        pos = domElem.selectionStart;
    return pos;
}

function commentLimitAll(networkAuthId, networkCount) {
    var commentInput = jQuery(".b2s-post-item-details-item-comment-input[data-network-auth-id='" + networkAuthId + "'][data-network-count='" + networkCount + "']");
    
    if (commentInput.length === 0) {
        return;
    }
    
    var limit = parseInt(commentInput.attr('data-network-comment-limit'));
    var text = commentInput.val();
    
    if (!limit || limit === 0) {
        // No limit, just update character count
        if (text) {
            jQuery(".b2s-post-item-comment-countChar[data-network-auth-id='" + networkAuthId + "'][data-network-count='" + networkCount + "']").html(text.length);
        }
        return;
    }
    
    if (typeof text !== typeof undefined && text !== false) {
        var textLength = text.length;
        var newText = text;
        
        // If text exceeds limit, cut it
        if (textLength >= limit) {
            newText = text.substring(0, limit);
            var pos = getCaretPos(commentInput[0]);
            commentInput.val(newText);
            setCaretPos(commentInput[0], pos);
            textLength = limit;
        }
        
        // Update character count display
        jQuery(".b2s-post-item-comment-countChar[data-network-auth-id='" + networkAuthId + "'][data-network-count='" + networkCount + "']").html(textLength);
    }
}

function setCaretPos(domElem, pos) {
    if (domElem.setSelectionRange) {
        domElem.focus();
        domElem.setSelectionRange(pos, pos);
    } else if (domElem.createTextRange) {
        var range = domElem.createTextRange();
        range.collapse(true);
        range.moveEnd("character", pos);
        range.moveStart("character", pos);
        range.select();
    }
}

function ucfirst(str) {
    str += '';
    return str.charAt(0).toUpperCase() + str.substr(1);
}


function hideDuplicateAuths() {
    jQuery(".b2s-sidbar-wrapper-nav-li").each(function () {
        jQuery(this).show();
    });
    var mandantId = jQuery('.b2s-network-details-mandant-select').val();
    jQuery(".b2s-sidbar-wrapper-nav-li").each(function () {
        if (jQuery(this).is(":visible")) {
            var dataNetworkDisplayName = jQuery(this).children('.b2s-network-select-btn').attr('data-network-display-name');
            var dataNetworkId = jQuery(this).children('.b2s-network-select-btn').attr('data-network-id');
            var dataNetworkType = jQuery(this).children('.b2s-network-select-btn').attr('data-network-type');
            var dataNetworkAuthId = jQuery(this).children('.b2s-network-select-btn').attr('data-network-auth-id');
            jQuery('.b2s-network-select-btn[data-network-display-name="' + dataNetworkDisplayName + '"][data-network-id="' + dataNetworkId + '"][data-network-type="' + dataNetworkType + '"][data-network-auth-id!="' + dataNetworkAuthId + '"]').each(function () {
                var selectedDataMandantId = jQuery.parseJSON(jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id'));
                if (jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-default-id') != mandantId && selectedDataMandantId.indexOf(mandantId) == -1) {
                    jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').hide();
                }
            });
        }
    });
}

function chooseMandant() {

//Laden abbrechen und anzeige zurück setzten
    jQuery.xhrPool.abortAll();
    jQuery('.b2s-post-item-loading-dummy').remove();
    jQuery('.b2s-network-status-img-loading').hide();
    jQuery('.b2s-network-select-btn-deactivate').removeClass('b2s-network-select-btn-deactivate');
    //imageCheck();
    //TOS XING Groups
    b2sTosXingGroupCount = 0;
    //expiredDate wieder setzten
    jQuery('.b2s-network-status-expiredDate').each(function () {
        if (jQuery(this).is(':visible')) {
            jQuery('.b2s-network-select-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').addClass('b2s-network-select-btn-deactivate');
        }
    });
    jQuery('.b2s-network-select-btn-deactivate')
    var mandantId = jQuery('.b2s-network-details-mandant-select').val();
    jQuery('.b2s-post-item').hide();
    jQuery('.b2s-post-item').find('.form-control').each(function () {
        jQuery(this).attr("disabled", "disabled");
        jQuery(this).removeClass('error');
    });
    jQuery('.b2s-network-select-btn').children().removeClass('b2s-network-list-active').find('.b2s-network-status-img').addClass('b2s-network-hide');
    //Check IS RE-PUBLISH
    var isMultiSelectNetwork = false;
    if (typeof jQuery('#b2sMultiSelectedNetworkAuthId') != 'undefined' && typeof jQuery('#b2sMultiSelectedNetworkAuthId').val() != 'undefined' && jQuery('#b2sMultiSelectedNetworkAuthId').val() != '') { //exisits?
        var selectedNetworks = jQuery('#b2sMultiSelectedNetworkAuthId').val().split(',');
        var preventMutliClick = [];
        selectedNetworks.forEach(function (selectedAuthId) {
            if (!isMultiSelectNetwork && jQuery(".b2s-network-select-btn[data-network-auth-id='" + selectedAuthId + "']").length > 0) {
                isMultiSelectNetwork = true;
                var mandantId = jQuery(".b2s-network-select-btn[data-network-auth-id='" + selectedAuthId + "']").parent('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id');
                jQuery('.b2s-network-details-mandant-select').val(mandantId);
                jQuery('#b2sSelectedMultiNetworkAuthId').val("0");
            }
            if (!preventMutliClick.includes(selectedAuthId)) {
                jQuery(".b2s-network-select-btn[data-network-auth-id='" + selectedAuthId + "']").trigger('click');
                preventMutliClick.push(selectedAuthId);
            }
        });
    }
    if (!isMultiSelectNetwork) {
        if (jQuery('#b2sSelectedNetworkAuthId').val() > 0 && jQuery(".b2s-network-select-btn[data-network-auth-id='" + jQuery('#b2sSelectedNetworkAuthId').val() + "']").length > 0) { //exisits?
            jQuery(".b2s-network-select-btn[data-network-auth-id='" + jQuery('#b2sSelectedNetworkAuthId').val() + "']").trigger('click');
            var mandantId = jQuery(".b2s-network-select-btn[data-network-auth-id='" + jQuery('#b2sSelectedNetworkAuthId').val() + "']").parent('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id');
            jQuery('.b2s-network-details-mandant-select').val(mandantId);
            jQuery('#b2sSelectedNetworkAuthId').val("0");
        } else {
            jQuery(".b2s-sidbar-wrapper-nav-li").each(function () {
                var mandantIds = jQuery.parseJSON(jQuery(this).attr('data-mandant-id'));
                if (mandantIds.indexOf(mandantId) != -1 && !jQuery(this).children('.b2s-network-select-btn').hasClass('b2s-network-select-btn-deactivate')) {
                    jQuery(this).children('.b2s-network-select-btn').trigger('click');
                }
            });
        }
    }

    checkNetworkSelected();
}

function padDate(n) {
    return ("0" + n).slice(-2);
}

function wop(url, name) {
    jQuery('.b2s-network-auth-success').hide();
    var location = window.location.protocol + '//' + window.location.hostname;
    url = encodeURI(url + '&mandant_id=' + jQuery('.b2s-network-details-mandant-select').val() + '&location=' + location);
    window.open(url, name, "width=650,height=900,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}


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


function loginSuccess(networkId, networkType, displayName, networkAuthId, mandandId, instant_sharing) {
    jQuery('.b2s-network-auth-success').show();
    jQuery('#b2s-network-list-modal').modal('hide');
    jQuery('#b2s-network-list-modal').hide();
    jQuery('body').removeClass('modal-open');
    jQuery('body').removeAttr('style');
    if (jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').length == 0) {
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_ship_navbar_item',
                'networkId': networkId,
                'networkType': networkType,
                'displayName': displayName,
                'networkAuthId': networkAuthId,
                'instant_sharing': instant_sharing,
                'mandandId': mandandId,
                'isVideo': jQuery('#is_video').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                if (data.result == true) {
                    jQuery(data.content).insertAfter('.b2s-sidbar-network-auth-btn');
                    jQuery('.b2s-network-select-btn[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click');
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            }
        });
    } else {
        jQuery('.b2s-network-status-expiredDate[data-network-auth-id="' + networkAuthId + '"]').remove();
        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-network-select-btn-deactivate');
        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').removeAttr('onclick');
        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').attr('data-network-display-name', displayName);
        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"] > .b2s-network-list > .b2s-network-details > h4').text(displayName);
        jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').trigger('click');
    }
    jQuery('.b2s-network-select-btn[data-network-id="' + networkId + '"][data-network-type="' + networkType + '"][data-network-display-name="' + displayName.toLowerCase() + '"]').each(function () {
        jQuery('.b2s-network-status-expiredDate[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').remove();
        jQuery(this).removeClass('b2s-network-select-btn-deactivate');
        jQuery(this).removeAttr('onclick');
    });
}


//ASS
jQuery(document).on('click', '#b2s-ass-auth-step1-btn', function () {
    var add = "";
    if (jQuery('#b2s-ass-auth-email-own').is(':checked')) {
        add = '&email=' + jQuery('#b2s-ass-auth-email-own').attr('data-auth-email');
    }
    wopAssAuth(jQuery(this).attr('data-url') + add, jQuery(this).attr('data-auth-title'));
    return true;
});

function wopAssAuth(url, name) {
    var location = window.location.protocol + '//' + window.location.hostname;
    url = encodeURI(url + '&location=' + location);
    window.open(url, name, "width=650,height=800,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}

jQuery(document).on('click', '#b2s-ass-auth-step3-btn', function () {
    jQuery('.b2sAssAuthModal').modal('hide');
    return false;
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

    //network deprecated
    if (jQuery(dateElement).attr('data-network-id') == '8') {
        var deprecatedDate = new Date('2019-03-30T23:59:59');
        var count = jQuery(dateElement).attr('data-network-count');
        if (enter.getTime() > deprecatedDate.getTime()) {
            jQuery('.network-tos-deprecated-warning[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + count + '"]').show();
            jQuery(dateElement).datepicker('update', now);
            jQuery(timeElement).timepicker('setTime', now);
        }
    }

    //network deprecated
    if (jQuery(dateElement).attr('data-network-id') == '10') {
        var deprecatedDate = new Date('2019-04-01T23:59:59');
        var count = jQuery(dateElement).attr('data-network-count');
        if (enter.getTime() > deprecatedDate.getTime()) {
            jQuery('.network-tos-deprecated-warning[data-network-auth-id="' + dataNetworkAuthId + '"][data-network-count="' + count + '"]').show();
            jQuery(dateElement).datepicker('update', now);
            jQuery(timeElement).timepicker('setTime', now);
        }
    }

}

jQuery(document).on("click", ".b2s-draft-btn", function (event) {
    event.preventDefault();
    jQuery('.b2s-loader-btn-ship').css('display', 'inline-block');
    jQuery('.b2s-submit-btn').prop('disabled', true);
    jQuery('.b2s-submit-btn-scroll').prop('disabled', true);
    jQuery('.b2s-post-draft-saved-success').hide();
    jQuery('.b2s-post-draft-saved-fail').hide();
    jQuery('.b2s-server-connection-fail').hide();

    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        cache: false,
        data: {
            action: 'b2s_check_draft_exists',
            postId: jQuery('#post_id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            saveDraft();
            return false;
        },
        success: function (data) {
            result = JSON.parse(data);
            if (result.result == true) {
                jQuery('#b2s-save-draft-modal').modal('show');
                return true;
            } else {
                if (result.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
            saveDraft();
            return false;
        }
    });

});

jQuery(document).on('click', '.b2s-draft-btn-scroll', function () {
    jQuery('.b2s-draft-btn').trigger('click');
});

function saveDraft() {

    if (checkMaxInputVarsLimit('#b2sNetworkSent') == false) {
        jQuery('.b2s-loader-btn-ship').css('display', 'none');
        jQuery('.b2s-submit-btn').removeAttr('disabled');
        jQuery('.b2s-submit-btn-scroll').removeAttr('disabled');
        return false;
    }

    jQuery('#action').val('b2s_save_draft_data');
    var data = jQuery('#b2sNetworkSent').serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery('#action').val('b2s_save_ship_data');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        data: data,
        error: function () {
            jQuery('.b2s-loader-btn-ship').css('display', 'none');
            jQuery('.b2s-submit-btn').removeAttr('disabled');
            jQuery('.b2s-submit-btn-scroll').removeAttr('disabled');
            jQuery('.b2s-server-connection-fail').show();
            window.scrollTo(0, 0);
            return false;
        },
        success: function (data) {
            
            jQuery('.b2s-loader-btn-ship').css('display', 'none');
            jQuery('.b2s-submit-btn').removeAttr('disabled');
            jQuery('.b2s-submit-btn-scroll').removeAttr('disabled');
            result = JSON.parse(data);
            if (result.result == true) {
                jQuery('.b2s-post-draft-saved-success').show();
                window.setTimeout(function () {
                    jQuery('.b2s-post-draft-saved-success').fadeOut();
                }, 5000);
            } else {

                if (result.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (result.error == 'permission') {
                    jQuery('.b2s-no-permission').show();
                }
                jQuery('.b2s-post-draft-saved-fail').show();
                window.setTimeout(function () {
                    jQuery('.b2s-post-draft-saved-fail').fadeOut();
                }, 5000);
            }
            window.scrollTo(0, 0);
            return true;
        }
    });
}

jQuery('#b2s-save-draft-modal').on('hidden.bs.modal', function () {
    jQuery('.b2s-loader-btn-ship').css('display', 'none');
    jQuery('.b2s-submit-btn').removeAttr('disabled');
    jQuery('.b2s-submit-btn-scroll').removeAttr('disabled');
});

jQuery(document).on('click', '.b2s-save-draft-confirm-btn', function () {
    saveDraft();
    jQuery('#b2s-save-draft-modal').modal('hide');
    return true;
});

jQuery('#b2sAuthNetwork6Modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery('#b2sInfoNoCache').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sInfoFormat').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2sInfoFormatBtn', function () {
    jQuery('#b2sInfoFormat').modal('show');
    var id = jQuery(this).attr('data-network-id');
    jQuery('.b2sInfoFormatText').hide();
    jQuery('.b2sInfoFormatText[data-network-id="' + id + '"]').show();
});

jQuery('#b2sInfoContent').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sInfoCharacterLimit').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});
jQuery('#b2sMaxInputVarsModal').on('hidden.bs.modal', function () {
    if (jQuery('.modal.in:visible, .modal.show:visible').length === 0) {
        jQuery('body').removeClass('modal-open');
        jQuery('.modal-backdrop').remove();
    }
});
jQuery('.b2s-info-share-as-story-modal').on('hidden.bs.modal', function () {
    jQuery('body').addClass('modal-open');
});

jQuery(document).on('click', '.b2sInfoNetwork18Btn', function () {
    jQuery('#b2sInfoNetwork18').modal('show');
});
jQuery(document).on('click', '.b2sInfoNoCacheBtn', function () {
    jQuery('#b2sInfoNoCache').modal('show');
});
jQuery(document).on('click', '.b2sInfoContentBtn', function () {
    jQuery('#b2sInfoContent').modal('show');
});
jQuery(document).on('click', '.b2sInfoCharacterLimitBtn', function () {
    jQuery('#b2sInfoCharacterLimit').modal('show');
});

jQuery(document).on('click', '.b2sInfoPostRelayModalBtn', function () {
    jQuery('#b2sInfoPostRelayModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoSchedTimesModalBtn', function () {
    jQuery('#b2sInfoSchedTimesModal').modal('show');
});
jQuery(document).on('click', '.b2s-info-share-as-story-modal-btn', function () {
    jQuery('.b2s-info-share-as-story-modal').modal('show');
});
jQuery(document).on('click', '.b2sNetworkSettingSaveModal', function () {
    jQuery('#b2sNetworkSettingSaveModal').modal('show');
});
jQuery(document).on('click', '.b2s-network-list-modal-btn', function () {
    jQuery('#b2s-network-list-modal').modal('show');
});
jQuery(document).on('click', '.b2s-re-share-info-btn', function () {
    jQuery('#b2s-re-share-info').modal('show');
});

function checkGifAnimation(networkAuthId, networkId) {
    if (jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').length >= 1 && jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val() != '') {
        var attachmenUrlExt = jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val().substr(jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val().lastIndexOf('.') + 1);
        attachmenUrlExt = attachmenUrlExt.toLowerCase();
        if (attachmenUrlExt == 'gif') {
            var postFormat = 0;
            if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').length > 0) {
                postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val();
            }
            var animatedGif = JSON.parse(jQuery('#b2sAnimateGif').val());
            if (typeof animatedGif[networkId] != "undefined" && animatedGif[networkId][postFormat] == true) {
                jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val());
            } else {
                jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').each(function () {
                    var imgItem = this;
                    window.setTimeout(function () {
                        freeze_gif(imgItem);
                    }, 1);
                });
            }
        } else {
            if (jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src') != jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val()) {
                jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val());
            }
        }
    }
    return false;
}

function freeze_gif(i) {
    var c = document.createElement('canvas');
    var w = c.width = i.width;
    var h = c.height = i.height;
    c.getContext('2d').drawImage(i, 0, 0, w, h);
    try {
        i.src = c.toDataURL("image/gif"); // if possible, retain all css aspects
    } catch (e) { // cross-domain -- mimic original with all its tag attributes
        for (var j = 0, a; a = i.attributes[j]; j++)
            c.setAttribute(a.name, a.value);
        i.parentNode.replaceChild(c, i);
    }
    return false;
}

var currentEmojiNetworkAuthId = 0;
var currentEmojiNetworkCount = -1;
var currentEmojiInputSelector = '.b2s-post-item-details-item-message-input';
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
    if (currentEmojiNetworkAuthId > 0) {
        var inputSelector = currentEmojiInputSelector + '[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]';
        var text = jQuery(inputSelector).val();
        var start = jQuery(inputSelector).attr('selectionStart');
        var end = jQuery(inputSelector).attr('selectionEnd');
        if (typeof text === 'undefined') {
            return;
        }
        if (typeof start == 'undefined' || typeof end == 'undefined') {
            start = text.length;
            end = text.length;
        }
        var newText = text.slice(0, start) + emoji + text.slice(end);
        jQuery(inputSelector).val(newText);
        jQuery(inputSelector).focus();
        jQuery(inputSelector).prop("selectionStart", parseInt(start) + emoji.length);
        jQuery(inputSelector).prop("selectionEnd", parseInt(start) + emoji.length);
        jQuery(inputSelector).trigger('keyup');
    }
});

jQuery(document).on('click', '.b2s-post-item-details-item-message-emoji-btn, .b2s-post-item-details-item-comment-emoji-btn', function () {
    if (picker.pickerVisible) {
        picker.hidePicker();
    } else {
        currentEmojiInputSelector = jQuery(this).hasClass('b2s-post-item-details-item-comment-emoji-btn') ? '.b2s-post-item-details-item-comment-input' : '.b2s-post-item-details-item-message-input';
        currentEmojiNetworkAuthId = jQuery(this).attr('data-network-auth-id');
        currentEmojiNetworkCount = jQuery(this).attr('data-network-count');
        picker.showPicker(jQuery(this));
    }
});

jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-post-item-details-item-message-input, .b2s-post-item-details-item-comment-input', function () {
    var tb = jQuery(this).get(0);
    jQuery(this).attr('selectionStart', tb.selectionStart);
    jQuery(this).attr('selectionEnd', tb.selectionEnd);
});

var pickerHTML = new EmojiButton({
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
var currentPickerHTMLContent;
pickerHTML.on('emoji', function (emoji) {
    currentPickerHTMLContent.insert(emoji);
});

jQuery(document).on('click', '.b2s-add-multi-image', function () {
    var imageCount = jQuery(this).attr('data-image-count');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    var networkId = jQuery('.b2s-network-select-btn[data-network-auth-id=' + authId + ']').attr("data-network-id");
    jQuery('.b2s-image-change-all-network').hide();
    jQuery('.b2s-image-change-meta-network').hide();
    jQuery('.b2s-image-change-this-network').hide();
    jQuery('.b2s-image-add-this-network').attr('data-network-auth-id', authId).attr('data-network-count', countId).attr('data-network-id', networkId).attr('data-image-count', imageCount);
    jQuery('.b2s-image-add-this-network').show();
    var content = "<img class='b2s-post-item-network-image-selected-account' height='22px' src='" + jQuery('.b2s-post-item-network-image[data-network-auth-id="' + authId + '"]').attr('src') + "' /> " + jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + authId + '"]').html();
    jQuery('.b2s-selected-network-for-image-info').html(content);
    jQuery('.b2s-multi-image-info-text').show();
    jQuery('.b2s-default-image-info-text').hide();
    jQuery('#b2s-network-select-image').modal('show');
    return false;
});

jQuery(document).on('hidden.bs.modal', '#b2s-network-select-image', function () {
    jQuery('.b2s-multi-image-info-text').hide();
    jQuery('.b2s-default-image-info-text').show();
    return false;
});

jQuery(document).on('click', '.b2s-image-add-this-network', function () {
    var currentImage = jQuery('input[name=image_url]:checked').val();
    var imageCount = jQuery(this).attr('data-image-count');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    if (countId == -1) {
        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').attr('src', currentImage);
        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').val(currentImage);
        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').show();
        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').show();
        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').show();
        jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').hide();
        jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + (parseInt(imageCount) + 1) + '"]').show();
        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"]').show();
    } else {
        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').attr('src', currentImage);
        jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').val(currentImage);
        jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').show();
        jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').show();
        jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').show();
        jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').hide();
        jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + (parseInt(imageCount) + 1) + '"][data-network-count="' + countId + '"]').show();
        jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').show();
    }
    jQuery('#b2s-network-select-image').modal('hide');
    return false;
});

jQuery(document).on('click', '.b2s-multi-image-remove-btn', function () {
    var imageCount = jQuery(this).attr('data-image-count');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').attr('src', '');
    jQuery('.b2s-add-multi-image-hidden-field[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').val('');
    jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').hide();
    jQuery('.b2s-multi-image-remove-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').hide();
    jQuery('.b2s-multi-image-zoom-btn[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').hide();
    jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').show();
    jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + (parseInt(imageCount) + 1) + '"][data-network-count="' + countId + '"]').hide();
    jQuery('.b2s-select-multi-image-modal-open[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').hide();
    return false;
});

jQuery(document).on('click', '.b2s-post-item-details-url-image-multi', function () {
    var imageCount = jQuery(this).attr('data-image-count');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    jQuery('.b2s-add-multi-image[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').trigger('click');
    return false;
});

jQuery(document).on('click', '.b2s-select-multi-image-modal-open', function () {
    var imageCount = jQuery(this).attr('data-image-count');
    var authId = jQuery(this).attr('data-network-auth-id');
    var countId = jQuery(this).attr('data-network-count');
    jQuery('.b2s-post-item-details-url-image-multi[data-network-auth-id="' + authId + '"][data-image-count="' + imageCount + '"][data-network-count="' + countId + '"]').trigger('click');
    return false;
});

jQuery(document).on('click', '.b2s-network-add-page-info-btn', function () {
    jQuery('#b2sNetworkAddPageInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddPageInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=page', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-group-info-btn', function () {
    jQuery('#b2sNetworkAddGroupInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddGroupInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=group', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-instagram-info-btn', function () {
    jQuery('#b2sNetworkAddInstagramInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddInstagramInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=profile', 'Blog2Social Network');
        return false;
    });
    return false;
});

jQuery(document).on('click', '.b2s-network-add-instagram-business-info-btn', function () {
    jQuery('#b2sNetworkAddInstagramBusinessInfoModal').modal('show');
    var b2sAuthUrl = jQuery(this).data('b2s-auth-url');
    jQuery(document).on('click', '.b2s-add-network-continue-btn', function () {
        jQuery('#b2sNetworkAddInstagramBusinessInfoModal').modal('hide');
        wop(b2sAuthUrl + '&choose=page', 'Blog2Social Network');
        return false;
    });
    return false;
});

function openPostFormat(networkId, networkType, networkAuthId, wpType, showModal) {

    if (jQuery('#user_version').val() >= 1) {

        jQuery('.b2s-user-network-settings-post-format-area').hide();
        jQuery('.b2s-user-network-settings-post-format-area[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').show();
        jQuery('#b2s-post-ship-item-post-format-network-title').html(jQuery('.b2s-user-network-settings-post-format-area[data-network-id="' + networkId + '"]').attr('data-network-title'));
       
        jQuery('.b2s-user-network-settings-post-format[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').removeClass('b2s-settings-checked');

        var currentPostFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val();
       
        jQuery('.b2s-user-network-settings-post-format-apply[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').attr("data-network-auth-id", networkAuthId);
        jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').attr("data-network-auth-id", networkAuthId);
        jQuery('.b2s-user-network-settings-post-format-area-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').attr("data-network-auth-id", networkAuthId);
        
        if (showModal) {
            jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="' + currentPostFormat + '"]').prop("checked", true);

            //remove old ui values 
            jQuery('.b2s-user-network-settings-post-format-area-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="0"]').removeClass('b2s-settings-checked-new');
            jQuery('.b2s-user-network-settings-post-format-area-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="1"]').removeClass('b2s-settings-checked-new');
            jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="0"]').prop("checked", false);
            jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="1"]').prop("checked", false);
            
            jQuery('.b2s-user-network-settings-post-format-area-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="' + currentPostFormat + '"]').addClass('b2s-settings-checked-new');
        } else
        {
            jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="' + currentPostFormat + '"]').prop("checked", false);
        }

        jQuery('.b2s-user-network-settings-post-format-new[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="' + currentPostFormat + '"][data-network-auth-id="' + networkAuthId + '"]').prop("checked", true);
        
        if (jQuery('#user_version').val() >= 2) {
            jQuery('#b2s-post-ship-item-post-format-network-display-name').html(jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + networkAuthId + '"]').text().toUpperCase());
        }
        
        jQuery('.b2s-post-format-settings-info').hide();
        jQuery('.b2s-post-format-settings-info[data-network-id="' + networkId + '"]').show();
        if (showModal) {
            jQuery('#b2s-post-ship-item-post-format-modal').modal('show');
        }
        jQuery('.b2s-user-network-settings-post-format').attr('data-network-auth-id', networkAuthId);
        jQuery('.b2s-user-network-settings-post-format').attr('data-post-wp-type', wpType);
        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val() == "1" && jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val() != currentOGImage && jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').attr('data-meta-type') == 'og') {
            jQuery('.b2s-select-link-chang-image').show();
        } else {
            jQuery('.b2s-select-link-chang-image').hide();
        }
    } else {
        if (showModal) {
            jQuery('#b2sInfoFormatModal').modal('show');
        }
    }
    return false;
}

function changePostFormat(networkId, networkType, postFormat, networkAuthId, postFormatType, postType, closeModal) {

    jQuery('.b2s-settings-user-success').hide();
    jQuery('.b2s-settings-user-error').hide();
    jQuery('.b2s-server-connection-fail').hide();
    jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').val(postFormat);
    //PostFormat
    if (jQuery('.b2s-post-ship-item-post-format-text[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').length > 0) {
        var postFormatText = JSON.parse(jQuery('.b2sNetworkSettingsPostFormatText').val());
        if (jQuery('#user_version').val() >= 2) {
            jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + networkAuthId + '"]').html(postFormatText[postFormatType][postFormat]);
            jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val(postFormat);
        } else {
            jQuery('.b2s-post-ship-item-post-format-text[data-network-id="' + networkId + '"]').html(postFormatText[postFormatType][postFormat]);
            jQuery('.b2s-post-item-details-post-format[data-network-id="' + networkId + '"]').val(postFormat);
        }
        if (jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').attr('data-meta-type') == 'og' && postType != "ex") {
            if (currentOGImage == '' && jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val() != '') {
                currentOGImage = jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val();
                if (postFormat == "0") {
                    jQuery('.b2s-network-select-btn[data-meta-type="og"]').each(function () {
                        jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').attr('src', currentOGImage);
                        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val(currentOGImage);
                        jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').show();
                        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == 1) {
                            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
                            jQuery('.cropper-open[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
                        }
                    });
                }
            }
            if (postFormat == "0" && jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val() != currentOGImage) {
                jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentOGImage);
                jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentOGImage);
                jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
                if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {
                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                    jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
                }
            }
        }
    }
    var textLimit = jQuery('.b2s-post-item-details-item-message-input[data-network-count="-1"][data-network-auth-id="' + networkAuthId + '"]').attr('data-network-text-limit');
    if (textLimit != "0") {
        networkLimitAll(networkAuthId, networkId, textLimit);
    } else {
        networkCount(networkAuthId);
    }

    // check for add Link (posting templates)
    if (networkId != 12) {
        if (postFormat == 0) {
            let addUrl = (jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + networkAuthId + '][url]"]').val().length > 0 ? jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + networkAuthId + '][url]"]').val() : jQuery("#b2sDefault_url").val());
            jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(addUrl);
        } else {
            if (jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + networkAuthId + '][url]"]').attr('data-add-link') == 1) {
                let addUrl = (jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + networkAuthId + '][url]"]').val().length > 0 ? jQuery('.b2s-post-item-details-item-url-input[name="b2s[' + networkAuthId + '][url]"]').val() : jQuery("#b2sDefault_url").val());
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(addUrl);
            } else {
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val('');
            }
        }
    }

    //Edit Meta Tags
    var isMetaChecked = false;
    var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
    if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
        isMetaChecked = true;
    }
    if ((networkId == "2" || networkId == "24" || networkId == "45") && jQuery('#isCardMetaChecked').val() == "1") {
        isMetaChecked = true;
    }
    if (isMetaChecked && postFormat == '0' && jQuery('#user_version').val() > 0) { //If linkpost
        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", false);
        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", false);
        var dataMetaType = jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').attr("data-meta-type");
        if (dataMetaType == "og") {
            jQuery('#b2sChangeOgMeta').val("1");
        } else {
            jQuery('#b2sChangeCardMeta').val("1");
        }

        //Copy from further item meta tags by same network
        jQuery('.b2s-post-item-details-post-format[data-network-id=' + networkId + ']').each(function () {
            if (jQuery(this).val() == "0" && jQuery('.b2s-post-ship-item-post-format[data-network-auth-id=' + jQuery(this).attr('data-network-auth-id') + ']').is(":visible") && jQuery(this).attr('data-network-auth-id') != networkAuthId) { //other Linkpost by same network
                jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val());
                if (jQuery('.b2s-image-remove-btn[data-network-count="-1"][data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]:visible').length == 1) {
                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').show();
                    jQuery('.cropper-open[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').show();

                } else {
                    jQuery('.b2s-image-remove-btn[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
                    jQuery('.cropper-open[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();

                }

                return true;
            }
        });

        //Set & Check Link
        if (typeof jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]') !== undefined) {
            //Facebook + Twitter && Linkpost
            if ((networkId == 1 || networkId == 2 || networkId == 45) && postFormat == 0) {
                if (jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').val() == "") {
                    jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').val(jQuery('#b2sDefault_url').val());
                }
            }
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').show();
            if (jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').hasClass('disabled')) {
                jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').removeClass('disabled');
            }
        }

    } else {
        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
        jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').hide();
    }

    //Content Curation V5.0.0
    if (postType == "ex") {
        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
        jQuery('.b2sInfoMetaTagModal[data-network-auth-id="' + networkAuthId + '"]').attr("style", "display:none !important");
        if (postFormat == '0') {

            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').trigger("click");
        } else {
            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').hide();
        }
        if (jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() == 1) {
            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();

        }
    }
    jQuery('.b2s-user-network-settings-post-format[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').removeClass('b2s-settings-checked');
    jQuery('.b2s-user-network-settings-post-format[data-network-auth-id="' + networkAuthId + '"][data-post-format="' + postFormat + '"]').addClass('b2s-settings-checked');
    if (closeModal) {
        jQuery('#b2s-post-ship-item-post-format-modal').modal('hide');
    }
    checkGifAnimation(networkAuthId, networkId);
    //Multi Image
    if (((postFormat == 1 && ((networkId == 1 && (networkType == 1 || networkType == 2)) || (networkId == 2) || (networkId == 45) || (networkId == 3 && (networkType == 0 || networkType == 1)))) || networkId == 12 )  && jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() != 1) {
        jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').show();
    } else {
        jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').hide();
    }
    jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="0"]').show();
    jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="1"]').show();
    jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="2"]').show();

    if (postFormat == 0 && networkId == 1) {
        jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').addClass('required_network_url');
    }

    if (networkId == 2 || networkId == 45) {
        if (postFormat == 0) {
            jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
        } else {
            jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').hide();
        }
    }

    if (networkId == 1) {

        if (postFormat == 0) {
            jQuery('.b2s-share-as-story-fields[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').parent().show();
            jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').not('.b2s-share-as-story-fields').show();

        } else {

            jQuery('.b2s-share-as-story-fields[data-network-auth-id="' + networkAuthId + '"]').show();

            if (jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"]').prop("checked")) {

                jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').parent().hide();
                jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"]').hide();
                jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').not('.b2s-share-as-story-fields').hide();
            }
        }
    }

    if (networkId == 12) {

        //Fix Bug IG Postformat = 0 is image cutout, buttons need to show
        if (postFormat == 0) {
            jQuery('.b2s-select-image-modal-open[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
        }

        if (jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + networkAuthId + '"]').prop("checked")) {
            jQuery('.b2s-share-as-story-fields[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').parent().hide();
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + networkAuthId + '"]').hide();
            jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').not('.b2s-share-as-story-fields').hide();
        }
    }

    //Hide OG Area when changed to image from v8.4 onwards
    if (postFormat == 1) {
        jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').children(':not(div.input-group)').attr('style', 'display: none !important;');
    } else
    {
        if (networkId != 12) {
            jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').children(':not(div.input-group)').show();
        }
    }

    return false;
}

//Network: Tumblr post format
jQuery(document).on('change', '.b2s-post-item-details-post-format[data-network-id="4"]', function () {

    var type = jQuery(this).val();
    var networkAuthId = jQuery(this).data('network-auth-id');
    var networkCountId = -1;
   
    if(type==0 || type==1){
      
        jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').prop("disabled", true);
    }else
    {
        jQuery('.b2s-post-item-details-item-url-input[data-network-auth-id="' + networkAuthId + '"]').prop("disabled", false);
    }


    if (type == 3) {

        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').show();
        
        //Change Linkpost View
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').children('.sceditor-container').hide();
        jQuery('.tumblr-textarea-input[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').prop('disabled', false);
        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop('disabled', true);
        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop('disabled', true);
        jQuery('.alert-warning[data-network-auth-id="' + networkAuthId + '"]').show();

        if(jQuery('.link-textarea-initialized[data-network-auth-id="' + networkAuthId + '"]').data("linktextarea-initialized")==0){
            //Handle Text shorten link strip tags etc.
            var text = jQuery('.tumblr-textarea-input[data-network-auth-id="' + networkAuthId + '"]').val();
            text= stripTags(text);
            text= cutText(text, 125);

            jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').val(text);
            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').val(text);
            //jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(text.length);
            jQuery('.link-textarea-initialized[data-network-auth-id="' + networkAuthId + '"]').data("linktextarea-initialized", 1);
        }
        var length=   jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').val().length;
        jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(length);
        jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').attr('style', 'min-height: 8em !important;');
        

        showAssButtons(networkAuthId);
    }
    if (type == 1) {
        jQuery('.alert-warning[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-item-countChar-from[data-network-auth-id="' + networkAuthId + '"]').hide();
       
        // hide assistini buttons
        hideAssButtons(networkAuthId);
    }
    if (type == 0) {
        jQuery('.alert-warning[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').children('.sceditor-container').show();
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-original-area[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-item-countChar-from[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').prop('disabled', true);
    
        // show assistini buttons
        showAssButtons(networkAuthId);

        var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
        if (sceditor) {
            var plainText = sceditor.getBody().innerHTML;   // Gets plain text (no HTML)
            var textLength = plainText.length;      // Character count
             jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength);
        }
    }
});

jQuery(document).on('input change', '.tumblr-link-textarea-input', function(){

    var currentText= jQuery(this).val();
    var networkAuthId = jQuery(this).data('network-auth-id');
    jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').val(currentText);
    //jQuery('.tumblr-textarea-input[data-network-auth-id="' + networkAuthId + '"]').val(currentText);
    jQuery('.b2s-post-item-countChar[data-network-auth-id="' + networkAuthId + '"]').html(currentText.length);
}   
);

jQuery(document).on('input change', '.b2s-post-item-details-item-title-input', function(){

    var currentText= jQuery(this).val();
    var networkAuthId = jQuery(this).data('network-auth-id');
    jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').val(currentText);
   
}
);

jQuery(document).on('click', '.b2s-multi-image-zoom-btn', function () {
    var img = jQuery(this).closest('.text-center').find('.b2s-post-item-details-url-image-multi').attr('src');
    if (img != '') {
        jQuery('#b2sImageZoomModal').modal('show');
        jQuery('#b2sImageZoom').attr('src', img);
    }
    return false;
});


//CROPPER
//Global Cropper Variables
var cropper = null;
var scaleX = 1;
var scaleY = 1;


String.prototype.filename = function (extension) {
    var s = this.replace(/\\/g, '/');
    s = s.substring(s.lastIndexOf('/') + 1);
    return extension ? s.replace(/[?#].+$/, '') : s.split('.')[0];
}


//Function needed to directly display cropped image back 
function blobToDataURL(blob, callback) {
    var a = new FileReader();
    a.onload = function (e) {
        callback(e.target.result);
    }
    a.readAsDataURL(blob);
}

//Start the cropper on top of an image
jQuery(document).on("click", ".cropper-open", function (e) {
    jQuery('#b2s-network-editor-error-not-save').hide();
    var networkCount = jQuery(this).attr('data-network-count');
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkId = jQuery(this).attr('data-network-id');

    if (typeof networkCount == 'undefined' || typeof networkAuthId == 'undefined' || typeof networkId == 'undefined') {
        jQuery('#b2s-network-editor-image-modal').modal('hide');
        return true;
    }

    jQuery('#b2s-network-editor-image-network-auth-id').val(networkAuthId);
    jQuery('#b2s-network-editor-image-network-count').val(networkCount);
    jQuery('#b2s-network-editor-image-network-id').val(networkId);
    jQuery('#b2s-network-editor-image-network-account').html(jQuery('.b2s-post-item-details-network-display-name[data-network-auth-id="' + networkAuthId + '"]').html());
    jQuery('#b2s-network-editor-image-modal').modal('show');

    var imageToCrop = jQuery('.b2s-post-item-details-url-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]')[0];


    var width = imageToCrop.naturalWidth;
    var height = imageToCrop.naturalHeight;
    var minsize = 250;
    jQuery("#b2s-network-editor-image-src").attr("src", imageToCrop.src);
    jQuery("#b2s-network-editor-image-name").val(imageToCrop.src.filename());
    const image = document.getElementById("b2s-network-editor-image-src");

    var imgratio = width / height;
    var minCropBoxvalue;
    if (imgratio > 6 / 4) {
        var ratio = width / minsize;
        minCropBoxvalue = 600 / ratio;
    } else {
        var ratio = height / minsize;
        minCropBoxvalue = 400 / ratio;
    }
    cropper = new Cropper(image, {
        //aspectRatio: 16 / 9,
        zoomable: false,
        minCropBoxWidth: minCropBoxvalue,
        minCropBoxHeight: minCropBoxvalue,
        crop(event) {
        },
    });
    return false;
});

//Execute Options defined in Optionsmenu
jQuery(document).on("click", ".b2s-network-editor-image-option", function (e) {
    var idofcaller = e.target.id;
    //Leftrotation
    if (idofcaller == "b2s-rot-left") {
        cropper.rotate(-5);
    }
    //Rightrotation
    if (idofcaller == "b2s-rot-right") {
        cropper.rotate(5);
    }
    //XMirror
    if (idofcaller == "b2s-x-mirror") {
        if (scaleX == 1) {
            scaleX = -1;
        } else {
            scaleX = 1;
        }
        cropper.scaleX(scaleX);
    }
    //Ymirror
    if (idofcaller == "b2s-y-mirror") {
        if (scaleY == 1) {
            scaleY = -1;
        } else {
            scaleY = 1;
        }
        cropper.scaleY(scaleY);
    }
    return false;
});

jQuery(document).on("click", ".b2s-network-editor-image-modal-close", function (e) {
    jQuery('#b2s-network-editor-image-modal').modal('hide');
});

jQuery(document).on("click", "#b2s-network-editor-image-btn-save", function (e) {
    jQuery('#b2s-network-cut-image').modal('hide');
    var networkAuthId = jQuery('#b2s-network-editor-image-network-auth-id').val();
    var networkCount = jQuery('#b2s-network-editor-image-network-count').val();
    var networkId = jQuery('#b2s-network-editor-image-network-id').val();
    var filename = jQuery('#b2s-network-editor-image-name').val();

    cropper.getCroppedCanvas('image/png').toBlob((blob) => {
        blobToDataURL(blob, function (dataurl) {
            jQuery('.b2s-post-item-details-url-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]').attr("src", dataurl);
            cropper.destroy();
        });
        jQuery.ajax({
            url: jQuery('#b2s-network-editor-image-rest-endpoint').val() + 'wp/v2/media',
            method: 'POST',
            beforeSend: function (xhr) {
                xhr.setRequestHeader('X-WP-Nonce', jQuery('#b2s-network-editor-image-rest-nonce').val());
                xhr.setRequestHeader('Content-Disposition', 'attachment;filename=' + filename + '_edit.png');
            },
            data: blob,
            cache: false,
            contentType: false,
            processData: false
        }).done(function (response) {
            if (typeof (response.source_url) !== "undefined" && response.source_url != "") {
                var path = response.source_url;
                jQuery('.b2s-post-item-details-url-image[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]').attr("src", path);
                jQuery('.b2s-image-url-hidden-field[data-network-id="' + networkId + '"][data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]').val(path);
                jQuery('#b2s-network-editor-image-modal').modal('hide');
            } else {
                jQuery('#b2s-network-editor-error-not-save').show();
            }

        });
    });
    return false;
});

jQuery(document).on('click', '.deleteDraftBtn', function () {
    jQuery('#b2s-delete-confirm-draft-id').val(jQuery(this).attr('data-b2s-draft-id'));
    jQuery('.b2s-delete-draft-modal').modal('show');
});

jQuery(document).on('click', '.b2s-draft-delete-confirm-btn', function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_draft',
            'draftId': jQuery('#b2s-delete-confirm-draft-id').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-delete-draft-modal').modal('hide');
            if (data.result == true) {
                jQuery('.b2s-draft-list-entry[data-b2s-draft-id="' + jQuery('#b2s-delete-confirm-draft-id').val() + '"]').remove();
                jQuery('.b2s-post-remove-success').show();
                location.reload();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.error == 'permission') {
                    jQuery('.b2s-no-permission').show();
                }
                jQuery('.b2s-post-remove-fail').show();
            }
            return true;
        }
    });
});


//Tool:Assistini / Auth
jQuery(document).on('click', '.b2s-post-item-ass-auth-btn', function () {
    jQuery('.b2sAssAuthModal').modal('show');
    return true;
});

//Tool:Assistini / AI Settings - opens Edit Post Template in AI mode
jQuery(document).on('click', '.b2s-post-item-ass-setting-btn', function () {
    var editBtn = jQuery(this).closest('.b2s-post-item').find('.b2s-edit-template-btn');
    if (editBtn.length) {
        window.b2sOpenTemplateInAiMode = true;
        editBtn.trigger('click');
    }
    return true;
});

jQuery(document).on('change', '.b2s-global-ai-settings-checkbox-4', function () {
    if (this.checked) {
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-original-content').hide();
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content').show();
        jQuery('.b2s-global-ai-settings-checkbox-4-original-content-checked').hide();
        jQuery('.b2s-global-ai-settings-checkbox-4-displayed-content-checked').show();
    } else {
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-displayed-content').hide();
        jQuery('.toggle-label-b2s-global-ai-settings-checkbox-4-original-content').show();
        jQuery('.b2s-global-ai-settings-checkbox-4-displayed-content-checked').hide();
        jQuery('.b2s-global-ai-settings-checkbox-4-original-content-checked').show();
    }
});

jQuery(document).on('change', '#b2s-global-ai-settings-checkbox-1', function () {
    var checked = jQuery(this).prop('checked');
    if (checked) {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', true).prop('checked', true);
        var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
        var additionText = jQuery('#b2s-global-ai-settings-checkbox-3-conditional-text').text();
        jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text + ' ' + additionText);
    } else {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', false);
        var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
        jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text);
    }
});


jQuery(document).on('click', '.b2s-tiktok-promotion-radio', function () {
    var options = jQuery('.b2s-tiktok-promotion-options[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]');
    if (jQuery(this).attr("value") == 1) {
        options.show();
    } else {
        options.hide();
    }
});


jQuery(document).on('change', '.b2s-toastee-toggle', function () {
    var onboardingPaused = jQuery("#b2s-toastee-paused").val()

    if (jQuery(this).is(':checked')) {
        if (onboardingPaused == 1) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 0,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(0)
                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').show();
    } else {
        if (onboardingPaused == 0) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 1,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(1)

                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').hide();

    }
});



jQuery(document).on('change', '.b2s-toastee-toggle', function () {
    var onboardingPaused = jQuery("#b2s-toastee-paused").val()

    if (jQuery(this).is(':checked')) {
        if (onboardingPaused == 1) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 0,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(0)
                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').show();
    } else {
        if (onboardingPaused == 0) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_save_user_onboarding_paused',
                    'onboarding_paused': 1,
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    jQuery("#b2s-toastee-paused").val(1)

                }
            });
        }
        jQuery('.b2s-onboarding-toastee-body').hide();

    }
}
);

function initAssSidebar() {
    if (jQuery('#b2s-ship-ass-connected').val() == 1) {
        jQuery('.b2s-ass-sidebar-account').show();
        jQuery('#sidebar_ship_ass_words_open').text(jQuery('#b2s-ship-ass-words-open').val());
        jQuery('#sidebar_ship_ass_words_total').text(jQuery('#b2s-ship-ass-words-total').val());
    }
}

jQuery(document).on('click', '#b2s-sidebar-ship-ass-logout-btn', function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: true,
        data: {
            'action': 'b2s_ass_logout',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            var data = JSON.parse(data);
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                if (data.result == true) {
                    window.location.reload();
                }
            }
        }
    });
});

function assGenerateText(networkAuthId, networkName, schedCount = false) {

    if (schedCount !== false) {
        var textareaElm = jQuery('.b2s-post-item-details-item-message-input[name="b2s[' + networkAuthId + '][sched_content][' + schedCount + ']"]');
        var loaderElm = jQuery('.b2s-post-item-textarea-loader[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]');
        var createBtnElm = jQuery('.b2s-post-item-ass-create-btn');
        var resetBtnElm = jQuery('.b2s-post-item-ass-reset-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]');
    } else {
        var textareaElm = jQuery('textarea[name="b2s[' + networkAuthId + '][content]"]');
        var loaderElm = jQuery('.b2s-post-item-textarea-loader[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]');
        var createBtnElm = jQuery('.b2s-post-item-ass-create-btn');
        var resetBtnElm = jQuery('.b2s-post-item-ass-reset-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]');
    }
    var postId = textareaElm.data('post-id');
    var postFormatElm = null;

    if (typeof jQuery('input[name="b2s[' + networkAuthId + '][post_format]"]').val() != undefined) {
        postFormatElm = jQuery('input[name="b2s[' + networkAuthId + '][post_format]"]').val();
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        cache: false,
        async: true,
        data: {
            'action': 'b2s_ass_generate_text_sm',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val(),
            'post_format': postFormatElm,
            'post_network_name': networkName,
            'post_lang': jQuery('#b2sUserLang').val(),
            'post_id': postId,
            'network_id': textareaElm.data('network-id'),
            'network_type': textareaElm.data('network-type'),
            'network_kind': textareaElm.data('network-kind'),
            'sel_sched_date': jQuery('#selSchedDate').val(),
            'b2s_post_type': jQuery('#b2sPostType').val(),
            'relay_count': jQuery('#b2sRelayCount').val(),
            'is_video_mode': jQuery('#b2sIsVideo').val(),
            'post_url': jQuery('#b2sDefault_url').val(),
            'input_text': textareaElm.val()
        },
        beforeSend: function () {
            textareaElm.prop('disabled', true);
            createBtnElm.prop('disabled', true);
            resetBtnElm.prop('disabled', true);
            if (schedCount == false) {
                var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
                var sceditorBody = jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').children('.sceditor-container');
                if (sceditor != undefined && typeof sceditor.readOnly === 'function') {
                    sceditor.readOnly(true);
                    sceditorBody[0].classList.add('b2s-post-item-sceditor-disabled');
                }
            }
            loaderElm.show();
        },
        success: function (data) {
            loaderElm.hide();
            textareaElm.prop('disabled', false);
            createBtnElm.prop('disabled', false);
            resetBtnElm.prop('disabled', false);
            if (schedCount == false) {
                var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
                var sceditorBody = jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"][data-network-count="-1"]').children('.sceditor-container');
                if (sceditor != undefined && typeof sceditor.readOnly === 'function') {
                    sceditor.readOnly(false);
                    sceditorBody[0].classList.remove('b2s-post-item-sceditor-disabled');
                }
            }
        
            var data = JSON.parse(data);
        
            if(data.error){
                showAssError(data.error);

            } else if (data.result == true && data.ass_text != '') {
                var tumblrLink=false;
                if (textareaElm[0].classList.contains('b2s-post-item-details-item-message-input-allow-html')) { // is html network
                
                    //Tumbr Linkpost
                    var networkId= jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').data("network-id");
                    if(networkId== 4 ){
                        
                        //Handle Tags from Assistini
                        removeAllTags(networkAuthId);
                        var ass_hashtags = [...data.ass_hashtags.matchAll(/#([\p{L}\p{N}_]+)/gu)].map(match => match[1]);
                        ass_hashtags.forEach(element => {
                           
                            addTagwithContent(networkAuthId, element);
                        });
                        
                        var postFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val();

                        if(postFormat == 3){
                           
                            loaderElm.hide();
                            tumblrLink= true;
                            var text= data.ass_text;
                            text= stripTags(text);
                            text= cutText(text, 125);
                            
                            jQuery('.tumblr-link-textarea-input[data-network-auth-id="' + networkAuthId + '"]').val(text);
                            var networkCountId = '-1';
                            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(text.length);
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').val(text);
                           
                        }
                    }

                    var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
                    if (sceditor != undefined && typeof sceditor.insert === 'function') {
                        
                        // match the first <img ...> and capture the src attribute
                        var htmlBefore = sceditor.val() || '';
                        var match = htmlBefore.match(/<img [^>]*src=["']([^"']+)["'][^>]*>/i);
                        var currentImage = match ? match[1] : null;

                        sceditor.val('');

                        if (currentImage) {
                            sceditor.insert("<img src='" + currentImage + "'/><br />");
                            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
                        }

                        sceditor.insert(data.ass_text);
                    }
                       loaderElm.hide();
                  
                } else { // no html network
                       
                    textareaElm.val(data.ass_text);
                    loaderElm.hide();
                }
                var networkCountId = '-1';
                if (schedCount !== false) {
                    networkCountId = schedCount;
                }
                if(!tumblrLink){
                    jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(data.ass_text.length);
                }
                jQuery('#sidebar_ship_ass_words_open').text(data.ass_words_open);
                jQuery('#sidebar_ship_ass_words_total').text(data.ass_words_total);
            } else {
                showAssError("default");    
            }
        },
         complete: function () {
         createBtnElm.prop('disabled', false);
        }
    });
}

function showAssError(code){

    if(code=="nonce"){
        jQuery('.b2s-nonce-check-fail').show();
        return;
    }

    if(code==3100){
        if (jQuery('#b2sIsVideo').val() == 1) {
            code = code +"-video";
        }
    }

    if(jQuery('.b2sAssErrorModal-'+code).length<1){
       code="default";
    }

    jQuery('.b2sAssErrorModal-body').hide();
    jQuery('.b2sAssErrorModal-'+code).show();
    jQuery('#b2sAssErrorModal').modal('show');

}
jQuery(document).on('click', '.b2s-post-item-ass-reset-btn', function () {
    var networkAuthId = jQuery(this).data('network-auth-id');
    var schedCount = jQuery(this).data('network-count');
    var originalMessage = atob(jQuery('.b2s-post-item-ass-original-message[data-network-auth-id="' + networkAuthId + '"]').val());
    var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
    if (sceditor != undefined && sceditor.length == undefined) {
        sceditor.val('');
        sceditor.insert(jQuery('<textarea />').html(originalMessage).text());
    } else {
        if (schedCount == -1) {
            var textareaElm = jQuery('.b2s-post-item-details-item-message-input[name="b2s[' + networkAuthId + '][content]"]');
        } else {
            var textareaElm = jQuery('.b2s-post-item-details-item-message-input[name="b2s[' + networkAuthId + '][sched_content][' + schedCount + ']"]');
        }
        textareaElm.val(jQuery('<textarea />').html(originalMessage).text());
    }
    jQuery(".b2s-post-item-countChar[data-network-count='" + schedCount + "'][data-network-auth-id='" + networkAuthId + "']").html(originalMessage.length);
});

function hideAssButtons(networkAuthId = 0, schedCount = - 1) {
    jQuery('.b2s-post-item-ass-auth-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
    jQuery('.b2s-post-item-ass-create-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
    jQuery('.b2s-post-item-ass-reset-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
    jQuery('.b2s-post-item-ass-setting-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
}

function showAssButtons(networkAuthId = 0, schedCount = - 1) {
    if (jQuery('#b2s-ship-ass-connected').val() == 1) {
        jQuery('.b2s-post-item-ass-auth-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
        jQuery('.b2s-post-item-ass-create-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').show();
        jQuery('.b2s-post-item-ass-reset-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').show();
        jQuery('.b2s-post-item-ass-setting-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').show();
    } else {
        jQuery('.b2s-post-item-ass-auth-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').show();
        jQuery('.b2s-post-item-ass-create-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
        jQuery('.b2s-post-item-ass-reset-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
        jQuery('.b2s-post-item-ass-setting-btn[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + schedCount + '"]').hide();
}
}

jQuery(document).on('click', '.b2s-user-network-settings-post-format-apply', function (e, dataNetworkAuthId) {

    if(dataNetworkAuthId != 'undefined'){
        var dataNetworkAuthId = jQuery(this).attr('data-network-auth-id');
    }

    if (jQuery('.b2s-user-network-settings-post-format-new[data-network-auth-id="' + dataNetworkAuthId + '"][data-post-format="1"]').prop('checked') == true) {

        changePostFormat(jQuery(this).attr("data-network-id"), jQuery(this).attr("data-network-type"), "1", jQuery(this).attr("data-network-auth-id"), jQuery(this).attr("data-post-format-type"), jQuery(this).attr("data-post-wp-type"), true);

    } else
    {
        changePostFormat(jQuery(this).attr("data-network-id"), jQuery(this).attr("data-network-type"), "0", jQuery(this).attr("data-network-auth-id"), jQuery(this).attr("data-post-format-type"), jQuery(this).attr("data-post-wp-type"), true);
    }

});

jQuery(document).on('click', '.b2s-user-network-settings-post-format-new, img.b2s-user-network-settings-post-format-area-new', function () {

    var dataNetworkAuthId = jQuery(this).attr('data-network-auth-id');
    var postFormat = jQuery(this).attr('data-post-format');

    jQuery('.b2s-user-network-settings-post-format-new[data-network-auth-id="' + dataNetworkAuthId + '"][data-post-format="' + postFormat + '"]').prop("checked", true);
    jQuery('.b2s-user-network-settings-post-format-new[data-network-auth-id="' + dataNetworkAuthId + '"][data-post-format!="' + postFormat + '"]').prop("checked", false);

    jQuery('img.b2s-user-network-settings-post-format-area-new[data-network-auth-id="' + dataNetworkAuthId + '"][data-post-format="' + postFormat + '"]').addClass('b2s-settings-checked-new');
    jQuery('img.b2s-user-network-settings-post-format-area-new[data-network-auth-id="' + dataNetworkAuthId + '"][data-post-format!="' + postFormat + '"]').removeClass('b2s-settings-checked-new');

});

jQuery(document).on('change', '.b2s-post-item-details-release-input-times, .b2s-post-item-details-release-input-select-timespan', function () {

    var dataNetworkAuthId = jQuery(this).attr('data-network-auth-id');
    var count = jQuery(this).attr('data-network-count');

    if (jQuery(this).hasClass('b2s-post-item-details-release-input-times')) {

        var maxValue = get3YearsMax('times', dataNetworkAuthId, count);
    }

    if (jQuery(this).hasClass('b2s-post-item-details-release-input-select-timespan')) {

        var maxValue = get3YearsMax('timespan', dataNetworkAuthId, count);
    }

    if (maxValue) {

        jQuery('.b2s-network-tos-sched-max-values-alert[data-network-auth-id="' + dataNetworkAuthId + '"]').show();
        jQuery(this).val(maxValue);
        return;
    }
    jQuery('.b2s-network-tos-sched-max-values-alert[data-network-auth-id="' + dataNetworkAuthId + '"]').hide();
});

function get3YearsMax(type, dataNetworkAuthId, count) {

    var times = jQuery('[name="b2s[' + dataNetworkAuthId + '][duration_time][' + count + ']"]').val();
    var timespan = jQuery('[name="b2s[' + dataNetworkAuthId + '][select_timespan][' + count + ']"]').val();
    var startDateStr = jQuery('[name="b2s[' + dataNetworkAuthId + '][date][' + count + ']"]').val();
    var startTimeStr = jQuery('[name="b2s[' + dataNetworkAuthId + '][time][' + count + ']"]').val();
    var startDateStr = startDateStr + " " + startTimeStr;

    const nowDate = new Date();
    const futureDate = new Date(nowDate);
    futureDate.setFullYear(futureDate.getFullYear() + 3);
    const [datePart, timePart] = startDateStr.split(' ');
    var day, month, year;
    var eng = true;

    //German Years
    if (datePart.split(".").length == 3)
    {
        eng = false;
        [day, month, year] = datePart.split('.');
    } else if (datePart.split('-').length == 3)//English Years
    {
        [year, month, day] = datePart.split('-');
    } else
    {
        return false;
    }

    const [hour, minute] = timePart.split(':');
    const startDate = new Date(year, month - 1, day, hour, minute);
    var startOffset = startDate - nowDate;
    //keep 1 day margin
    var daysTotal = (timespan * times) + 1;
    var totalRangeInMs = startOffset + (daysTotal * 24 * 60 * 60 * 1000);
    var maxRangeInMs = futureDate - nowDate;

    if (totalRangeInMs > maxRangeInMs) {
        var maxValue = 1;
        if (type == 'times') {
            return Math.floor(maxRangeInMs / ((24 * 60 * 60 * 1000) * timespan));
        }
        if (type == "timespan") {
            return Math.floor(maxRangeInMs / ((24 * 60 * 60 * 1000) * times));
        }

        if (type == "date") {
            var totalMilliseconds = daysTotal * 24 * 60 * 60 * 1000;
            futureDate.setTime(futureDate.getTime() - totalMilliseconds);
            const day = String(futureDate.getDate()).padStart(2, '0');
            const month = String(futureDate.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
            const year = futureDate.getFullYear();

            if (eng) {
                return `${year}-${month}-${day}`;
            } else
            {
                return `${day}.${month}.${year}`;
            }
        }
    }
    return false;
}

function cutText(str, maxChars) {
  if (str.length <= maxChars) return str;

  // Try cutting at last punctuation before maxChars
  const punctuationRegex = /[.,;!?]/g;
  let cutIndex = -1;
  let match;

  while ((match = punctuationRegex.exec(str)) !== null) {
    if (match.index <= maxChars) {
      cutIndex = match.index + 1; // include punctuation
    } else {
      break;
    }
  }

  if (cutIndex > 0) {
    return str.slice(0, cutIndex).trim();
  }

  // Fallback: cut after last full word before maxChars
  const words = str.split(' ');
  let result = '';
  let length = 0;

  for (let i = 0; i < words.length; i++) {
    const word = words[i];
    if (length + word.length + (i > 0 ? 1 : 0) > maxChars) {
      break;
    }
    if (result) result += ' ';
    result += word;
    length = result.length;
  }

  return result;
}

function cutAfterWord(str, maxChars) {
  if (str.length <= maxChars) return str;

  const words = str.split(' ');
  let result = '';
  let length = 0;

  for (let i = 0; i < words.length; i++) {
    const word = words[i];
    if (length + word.length + (i > 0 ? 1 : 0) > maxChars) {
      // Stop before adding this word if it would exceed maxChars,
      // but still add it once to finish the word
      if (result) result += ' ';
      result += word;
      break;
    }
    if (result) result += ' ';
    result += word;
    length = result.length;
  }
  return result;
}

function stripTags(input) {
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = input;
    return tempDiv.textContent || tempDiv.innerText || '';
}

// Mirrors B2S_Util::getExcerpt($text, 0, $limit):
// Normalises HTML first (converts <br>/<p> to newlines, strips remaining tags)
// so that sentence boundaries like "werden." are never masked by trailing HTML.
// Prefers ending at a sentence boundary (. ? !); falls back to last word boundary.
function b2sGetExcerpt(text, limit) {
    // Normalise HTML: block separators become newlines, other tags are stripped
    var clean = text
        .replace(/<br\s*\/?>/gi, '\n')
        .replace(/<\/p>/gi, '\n')
        .replace(/<[^>]+>/g, '')
        .replace(/\n{2,}/g, '\n\n')
        .replace(/[ \t]+/g, ' ')
        .trim();
    if (clean.length <= limit) {
        return clean;
    }
    var parts = clean.split(/([ \t\n\r]+)/);
    var length = 0;
    var lastTaken = 0;
    var partCount = 0;
    for (var i = 0; i < parts.length; i++) {
        length += parts[i].length;
        if (length > limit) {
            break;
        }
        partCount++;
        var lastChar = parts[i].slice(-1);
        if (lastChar === '.' || lastChar === '?' || lastChar === '!') {
            lastTaken = partCount;
        }
    }
    if (lastTaken > 0) {
        return parts.slice(0, lastTaken).join('').trim().replace(/\n/g, '<br>');
    }
    // Fallback: cut at last word boundary within limit
    var sub = clean.substring(0, limit);
    var lastSpace = sub.lastIndexOf(' ');
    return (lastSpace > 0 ? sub.substring(0, lastSpace) : sub).trim().replace(/\n/g, '<br>');
}

/////////New TikTok Direct Review///////////////////////////////

//Validation

jQuery.validator.addMethod('checkTiktokPrivacy', function (value, element, rest) {
    var select = jQuery('.b2s-tiktok-form-select[data-network-auth-id="'+jQuery(element).attr('data-network-auth-id')+'"]');
    if (select.val() == 1 &&jQuery(element).val() == "") {
        return false; 
    }
    return true;
});
jQuery.validator.addClassRules('b2s-tiktok-status_privacy', {'checkTiktokPrivacy': true});


jQuery(document).on('change', '.b2s-tiktok-promotion-option', function () {  
    var authId = jQuery(this).attr("data-network-auth-id");  

    var submitButton = jQuery('.b2s-submit-btn');
    var submitButtonScroll = jQuery('.b2s-submit-btn-scroll');
    submitButton.prop('disabled', false)
    submitButtonScroll.prop('disabled', false)
    submitButton.removeAttr('title');
    submitButtonScroll.removeAttr('title');
    submitButton.unbind('mouseenter mouseleave');
    submitButtonScroll.unbind('mouseenter mouseleave');

    if(jQuery(this).val() == 0){
        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();

    } else {
        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').hide();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').show();

    }
});


jQuery(document).on('change', '.b2s-tiktok-form-select', function () {
    var authId = jQuery(this).attr("data-network-auth-id");
    var mode = jQuery(this).val();

    if(mode == 0){
        //share as draft
     
        jQuery('.tiktok-share-settings[data-network-auth-id="'+authId+'"]').hide();
        jQuery('.b2s-tiktok-draft-note[data-network-auth-id="'+authId+'"]').show();

        var isVideo= jQuery('.tiktok-video-preview[data-network-auth-id="'+authId+'"]').data("is-video");
        if(isVideo){
            jQuery('.tiktok-video-preview[data-network-auth-id="'+authId+'"]').hide();
            jQuery('.tiktok-text-input-fields[data-network-auth-id="'+authId+'"]').hide();
        }

    
    } else if(mode == 1){

        var isVideo= jQuery('.tiktok-video-preview[data-network-auth-id="'+authId+'"]').data("is-video");
      
        if(isVideo){
            jQuery('.tiktok-video-preview[data-network-auth-id="'+authId+'"]').show();
            jQuery('.tiktok-text-input-fields[data-network-auth-id="'+authId+'"]').show();
        }
        //share directly
        jQuery('.tiktok-share-settings[data-network-auth-id="'+authId+'"]').show();
        jQuery('.b2s-tiktok-draft-note[data-network-auth-id="'+authId+'"]').hide();
      
       
    }
});

jQuery(document).on('change', '.b2s-tiktok-status_privacy', function () {
    var authId = jQuery(this).attr("data-network-auth-id");
    var mode = jQuery(this).val();

    if(mode == "SELF_ONLY"){
   
        jQuery('.b2s-tiktok-promotion-radio[data-network-auth-id="'+authId+'"]').attr("disabled", true);
        jQuery('.b2s-tiktok-promotion-radio[data-network-auth-id="'+authId+'"]').first().prop("checked", false);
        jQuery('.b2s-tiktok-promotion-radio[data-network-auth-id="'+authId+'"]').last().prop("checked", true);
        jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionThirdParty\\]').attr("disabled", true);

    } else {

        jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionThirdParty\\]').attr("disabled", false);
    }
});

jQuery(document).on('click', '.tiktok-promotional-toggle', function () {
   

    jQuery(this).toggleClass('off'); 

    var authId = jQuery(this).attr("data-network-auth-id");

    var options = jQuery('.b2s-tiktok-promotion-options[data-network-auth-id="'+jQuery(this).attr('data-network-auth-id')+'"]');
    var self_only_option = jQuery('.b2s-tiktok-status_privacy[data-network-auth-id="'+authId+'"] > option[value="SELF_ONLY"]');

    var submitButton = jQuery('.b2s-submit-btn');
    var submitButtonScroll = jQuery('.b2s-submit-btn-scroll');

    //Toggle on
    if(!jQuery(this).hasClass('off')){
        options.show();

        var promotionChecked= jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionOwnBrand\\]').is(':checked');
        var brandedChecked= jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionThirdParty\\]').is(':checked');

        if(brandedChecked){
            self_only_option.attr("disabled", true);
            self_only_option.text(jQuery(".b2s-tiktok-self-only-disabled-text").val());
        }

        if(promotionChecked && !brandedChecked){
            jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').show();
            jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').hide();
            jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
            jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();
            
        }

        if(!promotionChecked && !brandedChecked){
            jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').hide();
            jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').hide();
            jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
            jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();
        
            submitButton.prop('disabled', true)
            submitButtonScroll.prop('disabled', true)

            submitButtonScroll.hover(
                function () {
                    var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                    jQuery(this).attr('title', text);
                });

            submitButton.hover(
                function () {
                    var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                    jQuery(this).attr('title', text);
                });
        
        
        } else {
            
            submitButton.prop('disabled', false)
            submitButtonScroll.prop('disabled', false)
            submitButton.removeAttr('title');
            submitButtonScroll.removeAttr('title');
        }

    } else {

        jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').hide();
        jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').hide();

        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();
       
        options.hide();
        self_only_option.attr("disabled", false);
        self_only_option.text(jQuery(".b2s-tiktok-self-only-text").val());

        submitButton.prop('disabled', false);
        submitButtonScroll.prop('disabled', false);
        submitButton.removeAttr('title');
        submitButtonScroll.removeAttr('title');  
        submitButton.unbind('mouseenter mouseleave');
        submitButtonScroll.unbind('mouseenter mouseleave');
    }

   
});

jQuery(document).on('change', '.b2s-tiktok-promotion-option', function () {
  
    if (jQuery(this).is(':checked')) {
      jQuery(this).val("on");   // change value when checked
    } else {
      jQuery(this).val("off");  // change value when unchecked
    }

    var authId = jQuery(this).attr("data-network-auth-id");
  
    var promotionChecked= jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionOwnBrand\\]').is(':checked');
    var brandedChecked= jQuery('#b2s\\['+authId+'\\]\\[b2sTiktokPromotionThirdParty\\]').is(':checked');

    var submitButton = jQuery('.b2s-submit-btn');
    var submitButtonScroll = jQuery('.b2s-submit-btn-scroll');

    if(!promotionChecked && !brandedChecked){
            
        submitButton.prop('disabled', true)
        submitButtonScroll.prop('disabled', true)

        submitButtonScroll.hover(
            function () {
                var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                jQuery(this).attr('title', text);
            });

        submitButton.hover(
            function () {
                var text = jQuery('.b2s-tiktok-no-promotion-selected').val();
                jQuery(this).attr('title', text);
            });
    
    }else {
            submitButton.prop('disabled', false)
            submitButtonScroll.prop('disabled', false)
            submitButton.removeAttr('title');
            submitButtonScroll.removeAttr('title');
    }

    var self_only_option = jQuery('.b2s-tiktok-status_privacy[data-network-auth-id="'+authId+'"] > option[value="SELF_ONLY"]');

    if(brandedChecked){
        jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').show();
        jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').hide();

        self_only_option.attr("disabled", true);
        self_only_option.text(jQuery(".b2s-tiktok-self-only-disabled-text").val());

        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').hide();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').show();
  
    }else
    {
        self_only_option.attr("disabled", false);
        self_only_option.text(jQuery(".b2s-tiktok-self-only-text").val());
    }
    
    if(promotionChecked && !brandedChecked){
        
        jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').show();
        jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').hide();
        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();
        return;
    }
    if(!promotionChecked && !brandedChecked){
        jQuery('#b2s\\['+authId+'\\]\\[b2sPromotional\\]').hide();
        jQuery('#b2s\\['+authId+'\\]\\[b2sPaidPartnership\\]').hide();
        jQuery('.tiktok-music-confirmation[data-network-auth-id="'+authId+'"]').show();
        jQuery('.tiktok-music-brand-confirmation[data-network-auth-id="'+authId+'"]').hide();
        return;
    }

});

function toggleFbPageShareAsStory(networkId, networkType) {
        if (networkId !== 1 || networkType != 1) {
            return;
        }
        var wrapper = jQuery('.b2s-edit-template-share-as-story-wrapper[data-network-type=' + networkType + ']');
        if (!wrapper.length) {
            return;
        }
        var formatValue = jQuery('.b2s-edit-template-post-format[data-network-type=' + networkType + ']').val();
        if (formatValue== 1) {
            wrapper.show();
        } else {
            wrapper.hide();
        }
};

var b2sAiSettingsChanged = false;
var b2sStandardSettingsChanged = false;
var b2sAiSettingsPendingMode = null;
var b2sAiSettingsLoading = false;

function getCurrentEditTemplateMode() {
    var activeMode = jQuery('.b2s-edit-template-mode-btn.active').attr('data-mode');
    return (activeMode === 'ai') ? 'ai' : 'standard';
}

function setEditTemplateMode(mode) {
    var targetMode = (mode === 'ai') ? 'ai' : 'standard';
    jQuery('.b2s-edit-template-mode-btn').removeClass('active');
    jQuery('.b2s-edit-template-mode-btn[data-mode="' + targetMode + '"]').addClass('active');

    if (targetMode === 'ai') {
        jQuery('.b2s-edit-template-standard-content').hide();
        jQuery('.b2s-edit-template-ai-content').show();
        if (!jQuery('.b2s-ai-ass-connected-indicator').is(':visible')) {
            jQuery('.b2s-edit-template-ai-content-connect-gate').show();
        }
        jQuery('.b2s-edit-template-save-btn').hide();
        jQuery('.b2s-edit-template-no-cache-area').hide();
        jQuery('.b2s-edit-template-content').addClass('b2s-ai-mode-active');
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        if ($tabsNav.length) {
            var $config = jQuery('.tab-pane.active .b2s-ai-template-config');
            if ($config.length) {
                $config.before($tabsNav.detach());
            }
        }
        jQuery.ajax({
            url: ajaxurl,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                'action': 'b2s_get_ass_settings',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (data) {
                b2sAiSettingsLoading = true;
                if (data.result == true) {
                    if (data.settings.post_template != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-1').prop('checked', data.settings.post_template);
                        if (data.settings.post_template == true) {
                            jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', true).prop('checked', true);
                            var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
                            var additionText = jQuery('#b2s-global-ai-settings-checkbox-3-conditional-text').text();
                            jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text + ' ' + additionText);
                        }
                    }
                    if (data.settings.deactivate_emojis != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked', data.settings.deactivate_emojis);
                    }
                    if (data.settings.generate_hashtags != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked', data.settings.generate_hashtags);
                    }
                    if (data.settings.displayed_content != null) {
                        jQuery('#b2s-global-ai-settings-checkbox-4').prop('checked', data.settings.displayed_content).trigger('change');
                    }
                }
                b2sAiSettingsLoading = false;
            }
        });
    } else {
        jQuery('.b2s-edit-template-standard-content').show();
        jQuery('.b2s-edit-template-ai-content').hide();
        jQuery('.b2s-edit-template-ai-content-connect-gate').hide();
        jQuery('.b2s-edit-template-save-btn').show();
        jQuery('.b2s-edit-template-no-cache-area').show();
        jQuery('.b2s-edit-template-content').removeClass('b2s-ai-mode-active');
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        var $tabContent = jQuery('.tab-content.clearfix');
        if ($tabsNav.length && $tabContent.length) {
            $tabContent.before($tabsNav.detach());
        }
    }

    initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
}

jQuery(document).on('change input', '.ai-template-form-element', function () {
    if (!b2sAiSettingsLoading) {
        b2sAiSettingsChanged = true;
    }
});

jQuery(document).on('change input', '.standard-template-form-element', function () {
    b2sStandardSettingsChanged = true;
});

jQuery(document).on('click', '.b2s-edit-template-mode-btn', function () {
    var targetMode = jQuery(this).attr('data-mode');
    if (targetMode === 'standard' && b2sAiSettingsChanged) {
        b2sAiSettingsPendingMode = targetMode;
        jQuery('#b2sAiSettingsUnsavedModal').modal('show');
        return false;
    }
    if (targetMode === 'ai' && b2sStandardSettingsChanged) {
        b2sAiSettingsPendingMode = targetMode;
        jQuery('#b2sAiSettingsUnsavedModal').modal('show');
        return false;
    }
    setEditTemplateMode(targetMode);
    return false;
});

jQuery(document).on('click', '.b2s-ai-unsaved-save-btn', function () {
    jQuery('#b2sAiSettingsUnsavedModal').modal('hide');
    if (b2sAiSettingsPendingMode === 'standard') {
        jQuery('.b2s-edit-template-save-ai-btn').trigger('click');
    } else {
        jQuery('.b2s-edit-template-save-btn').trigger('click');
    }
    if (b2sAiSettingsPendingMode) {
        setEditTemplateMode(b2sAiSettingsPendingMode);
        b2sAiSettingsPendingMode = null;
    }
});

jQuery(document).on('click', '.b2s-ai-unsaved-skip-btn', function () {
    jQuery('#b2sAiSettingsUnsavedModal').modal('hide');
    b2sAiSettingsChanged = false;
    b2sStandardSettingsChanged = false;
    if (b2sAiSettingsPendingMode) {
        setEditTemplateMode(b2sAiSettingsPendingMode);
        b2sAiSettingsPendingMode = null;
    }
});


jQuery('#b2sAiSettingsUnsavedModal').on('hidden.bs.modal', function () {
    if (jQuery('.modal.in').length) {
        jQuery('body').addClass('modal-open');
    }
});

jQuery('#b2sProFeatureEditTemplateModal').on('hidden.bs.modal', function () {
    if (jQuery('.modal.in').length) {
        jQuery('body').addClass('modal-open');
    }
});

jQuery(document).on('shown.bs.tab', '.b2s-template-profile, .b2s-template-page, .b2s-template-group', function () {
    if (getCurrentEditTemplateMode() === 'ai') {
        var $tabsNav = jQuery('.b2s-tabs-nav-container');
        if ($tabsNav.length) {
            var $config = jQuery('.tab-pane.active .b2s-ai-template-config');
            if ($config.length) {
                $config.before($tabsNav.detach());
            }
        }
    }
});

// Load Global AI Settings on page load
jQuery(document).ready(function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_ass_settings',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            b2sAiSettingsLoading = true;
            if (data.result == true) {
                if (data.settings.post_template != null) {
                    jQuery('#b2s-global-ai-settings-checkbox-1').prop('checked', data.settings.post_template);
                    if (data.settings.post_template == true) {
                        jQuery('#b2s-global-ai-settings-checkbox-3').prop('disabled', true).prop('checked', true);
                        var text = jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text().replace(/\s?\(.*?\)/g, '');
                        var additionText = jQuery('#b2s-global-ai-settings-checkbox-3-conditional-text').text();
                        jQuery('label[for="b2s-global-ai-settings-checkbox-3"]').text(text + ' ' + additionText);
                    }
                }
                if (data.settings.deactivate_emojis != null) {
                    jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked', data.settings.deactivate_emojis);
                }
                if (data.settings.generate_hashtags != null) {
                    jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked', data.settings.generate_hashtags);
                }
                if (data.settings.displayed_content != null) {
                    jQuery('#b2s-global-ai-settings-checkbox-4').prop('checked', data.settings.displayed_content).trigger('change');
                }
            }
            b2sAiSettingsLoading = false;
        }
    });
});

jQuery(document).on('click', '.b2s-edit-template-save-ai-btn', function () {
    b2sAiSettingsChanged = false;
    // Save Global AI Settings (same action as b2s-ass-settings-save-btn)
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_ass_settings_save',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val(),
            'setting_post_template': jQuery('#b2s-global-ai-settings-checkbox-1').prop('checked'),
            'setting_deactivate_emojis': jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked'),
            'setting_generate_hashtags': jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked'),
            'setting_displayed_content': jQuery('#b2s-global-ai-settings-checkbox-4').prop('checked')
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
        }
    });

    // Free users can only save global AI settings above
    if (parseInt(jQuery('#b2sUserVersion').val(), 10) < 1) {
        jQuery('#b2s-edit-template').modal('hide');
        return false;
    }

    var networkId = parseInt(jQuery('#b2s-edit-template-network-id').val(), 10);
    var typeId = getActiveTemplateType();
    var aiInstructionField = jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + typeId + '"]');
    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + typeId + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + typeId + '"]');
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + typeId + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + typeId + '"]');
    var emojiField = jQuery('.b2s-ai-template-emojis[data-network-type="' + typeId + '"]');

    if (!aiInstructionField.length || !networkId) {
        return false;
    }

    var generateHashtags = generateHashtagsField.length ? generateHashtagsField.val() : 'none';

    var payload = {
        enabled: jQuery('.b2s-ai-template-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_goal_enabled: jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        tone_language_enabled: jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        hashtags_keywords_enabled: jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_length_output_enabled: jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        answer_in_language: jQuery('.b2s-ai-template-answer-language[data-network-type="' + typeId + '"]').val(),
        network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
        ai_instruction: aiInstructionField.val(),
        post_goal: jQuery('.b2s-ai-template-post-goal[data-network-type="' + typeId + '"]').val(),
        cta_type: jQuery('.b2s-ai-template-cta-type[data-network-type="' + typeId + '"]').val(),
        point_of_view: jQuery('.b2s-ai-template-point-of-view[data-network-type="' + typeId + '"]').val(),
        tone: jQuery('.b2s-ai-template-tone[data-network-type="' + typeId + '"]').val(),
        content_focus: jQuery('.b2s-ai-template-content-focus[data-network-type="' + typeId + '"]').val(),
        text_form: jQuery('.b2s-ai-template-text-form[data-network-type="' + typeId + '"]').val(),
        form_of_address: jQuery('.b2s-ai-template-form-of-address[data-network-type="' + typeId + '"]').val(),
        emojis: emojiField.length ? emojiField.val() : 'off',
        writing_style: jQuery('.b2s-ai-template-writing-style[data-network-type="' + typeId + '"]').val(),
        generate_hashtags: generateHashtags,
        hashtags_count: (generateHashtags === 'from_ai' && hashtagsCountField.length) ? hashtagsCountField.val() : 0,
        use_keywords: (enforceKeywordsField.length && enforceKeywordsField.is(':checked') && useKeywordsField.length) ? useKeywordsField.val() : '',
        keyword_strength: jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + typeId + '"]').val(),
        text_length: jQuery('.b2s-ai-template-text-length[data-network-type="' + typeId + '"]').val(),
        text_depth: jQuery('.b2s-ai-template-text-depth[data-network-type="' + typeId + '"]').val()
    };

    jQuery('.b2s-edit-template-save-success').hide();
    jQuery('.b2s-edit-template-save-failed').hide();

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        dataType: 'json',
        cache: false,
        data: {
            action: 'b2s_save_ai_post_template',
            networkId: networkId,
            typeId: typeId,
            payload: payload,
            b2s_security_nonce: jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-edit-template-save-failed').show();
            return false;
        },
        success: function (data) {
            if (data.result === true) {
                jQuery('.b2s-edit-template-save-success').show();
                setTimeout(function () {
                    jQuery('.b2s-edit-template-save-success').fadeOut();
                }, 3000);
                jQuery('#b2s-edit-template').modal('hide');
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                if (data.error == 'permission') {
                    jQuery('#b2s-edit-template').modal('hide');
                    jQuery('.b2s-no-permission-author').show();
                    return false;
                }
                jQuery('.b2s-edit-template-save-failed').show();
            }
        }
    });

    return false;
});

function getAiTemplatePreviewPostId() {
    var selectors = ['#post_ID', '#b2s_post_id', '#b2s-post-id', '#b2sPostId'];
    for (var i = 0; i < selectors.length; i++) {
        var value = parseInt(jQuery(selectors[i]).val(), 10);
        if (!isNaN(value) && value > 0) {
            return value;
        }
    }
    return 0;
}

function getAiPreviewText(typeId, key) {
    return jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]').data(key) || key;
}

function renderAiTemplatePreviewResult(typeId, data, isError) {
    var previewElm = jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]');
    if (!previewElm.length) {
        return;
    }

    previewElm.find('.b2s-ai-preview-loader').hide();
    previewElm.show();

    if (isError) {
        previewElm.find('.b2s-ai-preview-result').hide();
        previewElm.find('.b2s-ai-preview-message').text(data).show();
        return;
    }

    previewElm.find('.b2s-ai-preview-message').hide();
    previewElm.find('.b2s-ai-template-preview-title').text(previewElm.data('text-generated') || 'Generated text');
    var assText = (data.ass_text || '').replace(/\r\n|\r|\n/g, '<br>');
    previewElm.find('.b2s-ai-template-preview-text').html(assText);
    var wordsOpen = typeof data.ass_words_open !== 'undefined' ? data.ass_words_open : 0;
    var wordsTotal = typeof data.ass_words_total !== 'undefined' ? data.ass_words_total : 0;
    previewElm.find('.b2s-ai-template-preview-meta').text((previewElm.data('text-left') || 'Left') + ': ' + wordsOpen + ' / ' + wordsTotal);
    if (data.ass_hashtags) {
        previewElm.find('.b2s-ai-template-preview-hashtags').html('<strong>' + (previewElm.data('text-hashtags') || 'Hashtags') + ':</strong> ' + data.ass_hashtags).show();
    } else {
        previewElm.find('.b2s-ai-template-preview-hashtags').hide();
    }
    previewElm.find('.b2s-ai-preview-result').show();
}

jQuery(document).on('click', '.b2s-ai-template-preview-btn', function () {
    var previewBtn = jQuery(this);
    var networkId = parseInt(jQuery('#b2s-edit-template-network-id').val(), 10);
    var typeId = getActiveTemplateType();
    var postId = getAiTemplatePreviewPostId();
    var postTextElm = jQuery('.b2s-edit-template-preview-content[data-network-type="' + typeId + '"]').first();
    var postText = postTextElm.length ? jQuery.trim(postTextElm.text()) : '';
    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + typeId + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + typeId + '"]');
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + typeId + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + typeId + '"]');
    var emojiField = jQuery('.b2s-ai-template-emojis[data-network-type="' + typeId + '"]');
    var generateHashtags = generateHashtagsField.length ? generateHashtagsField.val() : 'none';

    var aiTemplatePayload = {
        enabled: jQuery('.b2s-ai-template-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_goal_enabled: jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        tone_language_enabled: jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        hashtags_keywords_enabled: jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        content_length_output_enabled: jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + typeId + '"]').is(':checked') ? 1 : 0,
        answer_in_language: jQuery('.b2s-ai-template-answer-language[data-network-type="' + typeId + '"]').val(),
        network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
        ai_instruction: jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + typeId + '"]').val(),
        post_goal: jQuery('.b2s-ai-template-post-goal[data-network-type="' + typeId + '"]').val(),
        cta_type: jQuery('.b2s-ai-template-cta-type[data-network-type="' + typeId + '"]').val(),
        point_of_view: jQuery('.b2s-ai-template-point-of-view[data-network-type="' + typeId + '"]').val(),
        tone: jQuery('.b2s-ai-template-tone[data-network-type="' + typeId + '"]').val(),
        content_focus: jQuery('.b2s-ai-template-content-focus[data-network-type="' + typeId + '"]').val(),
        text_form: jQuery('.b2s-ai-template-text-form[data-network-type="' + typeId + '"]').val(),
        form_of_address: jQuery('.b2s-ai-template-form-of-address[data-network-type="' + typeId + '"]').val(),
        emojis: emojiField.length ? emojiField.val() : 'off',
        writing_style: jQuery('.b2s-ai-template-writing-style[data-network-type="' + typeId + '"]').val(),
        generate_hashtags: generateHashtags,
        hashtags_count: (generateHashtags === 'from_ai' && hashtagsCountField.length) ? hashtagsCountField.val() : 0,
        use_keywords: (enforceKeywordsField.length && enforceKeywordsField.is(':checked') && useKeywordsField.length) ? useKeywordsField.val() : '',
        keyword_strength: jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + typeId + '"]').val(),
        text_length: jQuery('.b2s-ai-template-text-length[data-network-type="' + typeId + '"]').val(),
        text_depth: jQuery('.b2s-ai-template-text-depth[data-network-type="' + typeId + '"]').val()
    };

    if (!networkId) {
        renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-no-network'), true);
        return false;
    }

    if (!postId) {
        renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-no-context'), true);
        return false;
    }

    jQuery.ajax({
        url: ajaxurl,
        type: 'POST',
        cache: false,
        async: true,
        data: {
            action: 'b2s_ass_generate_text_sm',
            b2s_security_nonce: jQuery('#b2s_security_nonce').val(),
            post_format: jQuery('.b2s-edit-template-post-format[data-network-type="' + typeId + '"]').val(),
            post_network_name: jQuery('.b2s-ai-template-network-name[data-network-type="' + typeId + '"]').val(),
            post_lang: jQuery('#b2sUserLang').val() || 'en',
            post_id: postId,
            network_id: networkId,
            network_type: typeId,
            network_kind: typeId,
            sel_sched_date: jQuery('#selSchedDate').val() || '',
            b2s_post_type: jQuery('#b2sPostType').val() || '',
            relay_count: jQuery('#b2sRelayCount').val() || 0,
            is_video_mode: jQuery('#b2sIsVideo').val() || 0,
            post_url: jQuery('#b2sDefault_url').val() || jQuery('#b2s_post_url').val() || '',
            input_text: postText,
            ai_template_settings: aiTemplatePayload
        },
        beforeSend: function () {
            previewBtn.prop('disabled', true);
            var previewElm = jQuery('.b2s-ai-template-preview[data-network-type="' + typeId + '"]');
            previewElm.find('.b2s-ai-preview-result').hide();
            previewElm.find('.b2s-ai-preview-message').hide();
            previewElm.find('.b2s-ai-preview-loader-text').text(previewElm.data('text-generating') || 'Generating preview text...');
            previewElm.find('.b2s-ai-preview-loader').show();
            previewElm.show();
        },
        success: function (response) {
            previewBtn.prop('disabled', false);
            var data = response;
            if (typeof response === 'string') {
                try {
                    data = JSON.parse(response);
                } catch (e) {
                    renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-parse-error'), true);
                    return;
                }
            }

            if (data && data.error === 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
                renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-security-fail'), true);
                return;
            }

            if (data && data.result === true && data.ass_text) {
                renderAiTemplatePreviewResult(typeId, data, false);
                if (typeof data.ass_words_open !== 'undefined') {
                    jQuery('#sidebar_ship_ass_words_open').text(data.ass_words_open);
                    jQuery('#b2s-ship-ass-words-open').val(data.ass_words_open);
                }
                if (typeof data.ass_words_total !== 'undefined') {
                    jQuery('#sidebar_ship_ass_words_total').text(data.ass_words_total);
                    jQuery('#b2s-ship-ass-words-total').val(data.ass_words_total);
                }
                return;
            }

            renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-gen-fail'), true);
        },
        error: function () {
            previewBtn.prop('disabled', false);
            renderAiTemplatePreviewResult(typeId, getAiPreviewText(typeId, 'text-request-fail'), true);
        }
    });

    return false;
});

function updateAiTemplateState(networkType) {
    var enabled = jQuery('.b2s-ai-template-enabled[data-network-type="' + networkType + '"]').is(':checked');
    jQuery('.b2s-ai-template-settings[data-network-type="' + networkType + '"]').show().toggleClass('b2s-ai-template-settings-disabled', !enabled);
    jQuery('.b2s-ai-template-disabled-info[data-network-type="' + networkType + '"]').toggle(!enabled);

    var contentGoalEnabled = jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var contentGoalFields = jQuery('.b2s-ai-template-content-goal-fields[data-network-type="' + networkType + '"]');
    if (contentGoalFields.length) {
        contentGoalFields.toggleClass('b2s-ai-template-group-fields-disabled', !contentGoalEnabled);
        contentGoalFields.find('input, select, textarea, button').prop('disabled', !contentGoalEnabled);
    }

    var toneLanguageEnabled = jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var toneLanguageFields = jQuery('.b2s-ai-template-tone-language-fields[data-network-type="' + networkType + '"]');
    if (toneLanguageFields.length) {
        toneLanguageFields.toggleClass('b2s-ai-template-group-fields-disabled', !toneLanguageEnabled);
        toneLanguageFields.find('input, select, textarea, button').prop('disabled', !toneLanguageEnabled);
    }

    var hashtagsKeywordsEnabled = jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var hashtagsKeywordsFields = jQuery('.b2s-ai-template-hashtags-keywords-fields[data-network-type="' + networkType + '"]');
    if (hashtagsKeywordsFields.length) {
        hashtagsKeywordsFields.toggleClass('b2s-ai-template-group-fields-disabled', !hashtagsKeywordsEnabled);
        hashtagsKeywordsFields.find('input, select, textarea, button').prop('disabled', !hashtagsKeywordsEnabled);
    }

    var contentLengthOutputEnabled = jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + networkType + '"]').is(':checked');
    var contentLengthOutputFields = jQuery('.b2s-ai-template-content-length-output-fields[data-network-type="' + networkType + '"]');
    if (contentLengthOutputFields.length) {
        contentLengthOutputFields.toggleClass('b2s-ai-template-group-fields-disabled', !contentLengthOutputEnabled);
        contentLengthOutputFields.find('input, select, textarea, button').prop('disabled', !contentLengthOutputEnabled);
    }

    var generateHashtagsField = jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + networkType + '"]');
    var hashtagsCountField = jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + networkType + '"]');
    if (hashtagsCountField.length) {
        hashtagsCountField.prop('disabled', !(generateHashtagsField.length && generateHashtagsField.val() === 'from_ai' && hashtagsKeywordsEnabled));
        if (generateHashtagsField.length && generateHashtagsField.val() === 'none') {
            hashtagsCountField.val(0);
        }
    }

    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + networkType + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + networkType + '"]');
    if (useKeywordsField.length) {
        useKeywordsField.toggle(enforceKeywordsField.length && enforceKeywordsField.is(':checked'));
    }
}

function initAiTemplateSettings(context) {
    var scope = context && context.length ? context : jQuery(document);
    scope.find('.b2s-ai-template-enabled').each(function () {
        var networkType = jQuery(this).attr('data-network-type');
        updateAiTemplateState(networkType);
    });

    scope.find('.b2s-ai-template-connect-gate:visible').each(function () {
        resetAiInlineAssistiniAuth(jQuery(this));
    });
}

function loadDefaultAiTemplate(networkType) {
    var settingsWrapper = jQuery('.b2s-ai-template-settings[data-network-type="' + networkType + '"]');
    if (!settingsWrapper.length) {
        return;
    }

    var defaultConfig = settingsWrapper.attr('data-ai-defaults');
    if (!defaultConfig) {
        return;
    }

    var defaults = null;
    try {
        defaults = JSON.parse(defaultConfig);
    } catch (error) {
        return;
    }

    if (!defaults || typeof defaults !== 'object') {
        return;
    }

    jQuery('.b2s-ai-template-enabled[data-network-type="' + networkType + '"]').prop('checked', parseInt(defaults.enabled, 10) === 1);
    jQuery('.b2s-ai-template-content-goal-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.content_goal_enabled === 'undefined' ? true : parseInt(defaults.content_goal_enabled, 10) === 1);
    jQuery('.b2s-ai-template-tone-language-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.tone_language_enabled === 'undefined' ? true : parseInt(defaults.tone_language_enabled, 10) === 1);
    jQuery('.b2s-ai-template-hashtags-keywords-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.hashtags_keywords_enabled === 'undefined' ? true : parseInt(defaults.hashtags_keywords_enabled, 10) === 1);
    jQuery('.b2s-ai-template-content-length-output-enabled[data-network-type="' + networkType + '"]').prop('checked', typeof defaults.content_length_output_enabled === 'undefined' ? true : parseInt(defaults.content_length_output_enabled, 10) === 1);
    jQuery('.b2s-ai-template-answer-language[data-network-type="' + networkType + '"]').val(defaults.answer_in_language || 'en');
    jQuery('.b2s-ai-template-network-name[data-network-type="' + networkType + '"]').val(defaults.network_name || '');
    jQuery('.b2s-ai-template-ai-instruction[data-network-type="' + networkType + '"]').val(defaults.ai_instruction || '');
    jQuery('.b2s-ai-template-post-goal[data-network-type="' + networkType + '"]').val(defaults.post_goal || 'traffic');
    jQuery('.b2s-ai-template-cta-type[data-network-type="' + networkType + '"]').val(defaults.cta_type || 'none');
    jQuery('.b2s-ai-template-tone[data-network-type="' + networkType + '"]').val(defaults.tone || 'neutral');
    jQuery('.b2s-ai-template-content-focus[data-network-type="' + networkType + '"]').val(defaults.content_focus || 50);
    jQuery('.b2s-ai-template-point-of-view[data-network-type="' + networkType + '"]').val(defaults.point_of_view || 'neutral');
    jQuery('.b2s-ai-template-text-form[data-network-type="' + networkType + '"]').val(defaults.text_form || 'default');
    jQuery('.b2s-ai-template-form-of-address[data-network-type="' + networkType + '"]').val(defaults.form_of_address || 'neutral');
    jQuery('.b2s-ai-template-emojis[data-network-type="' + networkType + '"]').val(defaults.emojis || 'auto');
    jQuery('.b2s-ai-template-writing-style[data-network-type="' + networkType + '"]').val(defaults.writing_style || 'default');
    jQuery('.b2s-ai-template-generate-hashtags[data-network-type="' + networkType + '"]').val(defaults.generate_hashtags || 'from_ai');
    jQuery('.b2s-ai-template-hashtags-count[data-network-type="' + networkType + '"]').val(typeof defaults.hashtags_count !== 'undefined' ? defaults.hashtags_count : 1);
    jQuery('.b2s-ai-template-keyword-strength[data-network-type="' + networkType + '"]').val(defaults.keyword_strength || 50);
    jQuery('.b2s-ai-template-text-length[data-network-type="' + networkType + '"]').val(defaults.text_length || 'medium');
    jQuery('.b2s-ai-template-text-depth[data-network-type="' + networkType + '"]').val(defaults.text_depth || 50);

    var keywords = defaults.use_keywords || '';
    var enforceKeywordsField = jQuery('.b2s-ai-template-enforce-keywords[data-network-type="' + networkType + '"]');
    var useKeywordsField = jQuery('.b2s-ai-template-use-keywords[data-network-type="' + networkType + '"]');
    if (enforceKeywordsField.length) {
        enforceKeywordsField.prop('checked', keywords.length > 0);
    }
    if (useKeywordsField.length) {
        useKeywordsField.val(keywords);
    }

    updateAiTemplateState(networkType);
    jQuery('.b2s-ai-template-preview[data-network-type="' + networkType + '"]').hide().html('');
}

function resetAiInlineAssistiniAuth(gate) {
    if (!gate || !gate.length) {
        return;
    }
    gate.find('.b2s-ai-ass-auth-step-1-content').show();
    gate.find('.b2s-ai-ass-auth-step-3-content').hide();
    gate.find('.b2s-ai-ass-step-circle').removeClass('b2s-ass-color btn-danger').addClass('btn-default');
    gate.find('.b2s-ai-ass-step-circle[data-step="1"]').addClass('b2s-ass-color btn-danger').removeClass('btn-default');
}

function openInlineAssistiniAuth(url, title) {
    var location = window.location.protocol + '//' + window.location.hostname;
    var targetUrl = encodeURI(url + '&location=' + location);
    window.open(targetUrl, title, "width=650,height=800,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}

jQuery(document).on('click', '.b2s-ai-ass-auth-step1-btn', function () {
    var gate = jQuery(this).closest('.b2s-ai-template-connect-gate');
    var emailOwn = gate.find('.b2s-ai-ass-auth-email-option[value="0"]');
    var add = '';
    if (emailOwn.is(':checked')) {
        add = '&email=' + emailOwn.attr('data-auth-email');
    }

    gate.find('.b2s-ai-ass-step-circle[data-step="1"], .b2s-ai-ass-step-circle[data-step="2"]').addClass('b2s-ass-color btn-danger').removeClass('btn-default');
    openInlineAssistiniAuth(jQuery(this).attr('data-url') + add, jQuery(this).attr('data-auth-title'));
    return false;
});

jQuery(document).on('click', '.b2s-ai-ass-auth-step3-btn', function () {
    var gate = jQuery(this).closest('.b2s-ai-template-connect-gate');
    gate.hide();
    jQuery('.b2s-ai-template-config').show();
    jQuery('.b2s-ai-ass-connected-indicator').show();
    jQuery('#b2s-global-ai-settings-not-connected-overlay').remove();
    jQuery('.b2s-ai-template-not-connected-overlay').remove();
    return false;
});

jQuery(document).on('change', '.b2s-ai-template-enabled, .b2s-ai-template-content-goal-enabled, .b2s-ai-template-tone-language-enabled, .b2s-ai-template-hashtags-keywords-enabled, .b2s-ai-template-content-length-output-enabled, .b2s-ai-template-generate-hashtags, .b2s-ai-template-enforce-keywords', function () {
    var networkType = jQuery(this).attr('data-network-type');
    updateAiTemplateState(networkType);
});

jQuery(document).on('change', '.b2s-ai-template-emojis', function () {
    if (jQuery(this).val() !== 'none') {
        jQuery('#b2s-global-ai-settings-checkbox-2').prop('checked', false);
    }
});

jQuery(document).on('change', '.b2s-ai-template-hashtags-count', function () {
    if (parseInt(jQuery(this).val(), 10) > 0) {
        jQuery('#b2s-global-ai-settings-checkbox-3').prop('checked', true);
    }
});

jQuery(document).on('click', '.b2s-edit-template-load-default-ai', function () {
    loadDefaultAiTemplate(jQuery(this).attr('data-network-type'));
    return false;
});

jQuery('#b2s-edit-template').on('show.bs.modal', function () {
    b2sAiSettingsChanged = false;
    b2sStandardSettingsChanged = false;
}).on('shown.bs.modal', function () {
    jQuery(this).removeAttr('aria-hidden');
}).on('hidden.bs.modal', function () {
    jQuery(this).attr('aria-hidden', 'true');
});

jQuery(document).on('click', '.b2s-edit-template-btn', function () {

    jQuery('b2s-edit-template-user-upgrade-required').hide();
    jQuery('.b2s-edit-template-content').hide();
    jQuery('.b2s-edit-template-save-btn').hide();
    jQuery('.b2s-loading-area').show();
    jQuery('#b2s-edit-template').modal('show');
    jQuery('#b2s-edit-template-network-id').val(jQuery(this).attr('data-network-id'));
    var networkId = jQuery(this).attr('data-network-id');
    var networkType = jQuery(this).attr('data-network-type');

    jQuery('.b2s-edit-template-network-img').hide();
    jQuery('#b2s-edit-template-network-img-' + networkId).show();

    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_get_edit_template',
            'networkId': networkId,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {

                if(data.content == 'b2s_upgrade_required') {
                    jQuery('.b2s-loading-area').hide();
                    jQuery('.b2s-edit-template-user-upgrade-required').show();
                    return;
                }

                jQuery('#b2s-edit-template').modal('show');
                jQuery('.b2s-edit-template-content').html(data.content);
                jQuery('.b2s-loading-area').hide();
                jQuery('.b2s-edit-template-content').show();
                jQuery('.b2s-edit-template-save-btn').show();
                if (jQuery('#b2sUserVersion').val() < 1 && networkId != 1 && networkId != 3 && networkId != 19) {
                    jQuery('.b2s-edit-template-save-btn').addClass('b2s-btn-disabled');
                } else {
                    jQuery('.b2s-edit-template-save-btn').removeClass('b2s-btn-disabled');
                }
                jQuery('.b2s-edit-template-post-content').trigger('keyup');
                jQuery('.b2s-edit-template-comment').trigger('keyup');
                initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
                if (window.b2sOpenTemplateInAiMode) {
                    window.b2sOpenTemplateInAiMode = false;
                    setEditTemplateMode('ai');
                } else {
                    setEditTemplateMode('standard');
                }
                jQuery('.b2s-edit-template-share-as-story-wrapper').each(function () {
                    var wrapperType = jQuery(this).data('network-type');
                    toggleFbPageShareAsStory(networkId, wrapperType);
                });
                if (networkId == 12) {
                    Coloris({
                        el: '.b2s-edit-template-colorpicker',
                        theme: 'polaroid',
                        swatches: [
                            '#ffffff',
                            '#000000',
                            '#ff0000',
                            '#00ff00',
                            '#0000ff',
                            '#ffff00',
                            '#c3073f',
                            '#5cdb95',
                            '#659dbd',
                            '#f9db7a',
                            '#e46de0'
                        ]
                    });
                }

                //When opened click Type
                if(networkType){
                    triggerClickByNetworkType(networkType);
                }

            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
});

// Edit Template - Link Post
jQuery(document).on('click', '.b2s-edit-template-link-post', function () {
    var networkId = jQuery(this).data('network-id');
    jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
    if(networkId == 4){
        jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('3');
    }else {
        jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('0');
    }
    jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
    jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.tumblr-link-post-notice').show();
    toggleFbPageShareAsStory(networkId, jQuery(this).attr('data-network-type'));

    // Tumblr special Preview Post again
    if(networkId == 4) {
        var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
        jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
    }
});

// Edit Template - Image Post
jQuery(document).on('click', '.b2s-edit-template-image-post', function () {
    var networkId = jQuery(this).data('network-id');
    jQuery('.tumblr-link-post-notice').hide();
    jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
    jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('1');
    jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
    jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
    toggleFbPageShareAsStory(networkId, jQuery(this).attr('data-network-type'));

    // Tumblr special Preview Post again
    if(networkId == 4) {
        var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
        jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
    }
});

// Edit Template - Text Post
jQuery(document).on('click', '.b2s-edit-template-text-post', function () {
    var networkId = jQuery(this).data('network-id');
    jQuery('.tumblr-link-post-notice').hide();
    jQuery('.b2s-edit-template-link-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-image-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-primary').addClass('btn-light');
    jQuery('.b2s-edit-template-text-post[data-network-type=' + jQuery(this).attr('data-network-type') + ']').removeClass('btn-light').addClass('btn-primary');
    jQuery('.b2s-edit-template-post-format[data-network-type=' + jQuery(this).attr('data-network-type') + ']').val('0');
    jQuery('.b2s-edit-template-link-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-image-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').hide();
    jQuery('.b2s-edit-template-text-preview[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
    jQuery('.b2s-edit-template-enable-link-area[data-network-type=' + jQuery(this).attr('data-network-type') + ']').show();
    toggleFbPageShareAsStory(networkId, jQuery(this).attr('data-network-type'));

    // Tumblr special Preview Post again
    if(networkId == 4) {
        var post = generateExamplePost(jQuery('.b2s-edit-template-post-content').val().replace(/\n/g, "<br>"), jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(), jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val());
        jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
    }
});

// Edit Template - Content Post Item
jQuery(document).on('click', '.b2s-edit-template-content-post-item', function () {
    var networkType = jQuery(this).attr('data-network-type');
    var text = jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val();
    var start = jQuery('.b2s-edit-template-content-selection-start[data-network-type="' + networkType + '"]').val();
    var end = jQuery('.b2s-edit-template-content-selection-end[data-network-type="' + networkType + '"]').val();

    var reg = new RegExp("({.+?})", "g");
    var amatch = null;
    while ((amatch = reg.exec(text)) != null) {
        var thisMatchStart = amatch.index;
        var thisMatchEnd = amatch.index + amatch[0].length;
        // case: keydown in pattern
        if (start > thisMatchStart && end < thisMatchEnd) {
            event.preventDefault();
            return false;
        }
    }
    var newText = text.slice(0, start) + jQuery(this).html() + text.slice(end);
    jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val(newText);
    jQuery('.b2s-edit-template-post-content').focus();
    jQuery('.b2s-edit-template-post-content').trigger('keyup');
    event.preventDefault();
    return false;
});

// Edit Template - Content Clear Button
jQuery(document).on('click', '.b2s-edit-template-content-clear-btn', function () {
    var networkType = jQuery(this).attr('data-network-type');
    jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val("");
    jQuery('.b2s-edit-template-post-content').focus();
    jQuery('.b2s-edit-template-post-content').trigger('keyup');
    event.preventDefault();
    return false;
});

// Edit Template - Range Input Validation
jQuery(document).on('keyup', '.b2s-edit-template-range', function () {
    if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
        jQuery(this).val("1");
    }
    if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
        jQuery(this).val(jQuery(this).attr('max'));
    }
    event.preventDefault();
    return false;
});

// Edit Template - Excerpt Range Input Validation
jQuery(document).on('keyup', '.b2s-edit-template-excerpt-range', function () {
    if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
        jQuery(this).val("1");
    }
    if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
        jQuery(this).val(jQuery(this).attr('max'));
    }
    event.preventDefault();
    return false;
});

// Edit Template - Comment Range Input Validation
jQuery(document).on('keyup', '.b2s-edit-template-range-comment', function () {
    if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
        jQuery(this).val("1");
    }
    if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
        jQuery(this).val(jQuery(this).attr('max'));
    }
    event.preventDefault();
    return false;
});

// Edit Template - Comment Excerpt Range Input Validation
jQuery(document).on('keyup', '.b2s-edit-template-excerpt-range-comment', function () {
    if (isNaN(parseInt(jQuery(this).val())) || parseInt(jQuery(this).val()) < 1) {
        jQuery(this).val("1");
    }
    if (jQuery(this).attr('max') > 0 && parseInt(jQuery(this).val()) > jQuery(this).attr('max')) {
        jQuery(this).val(jQuery(this).attr('max'));
    }
    event.preventDefault();
    return false;
});

// Edit Template - Range Change Handler
jQuery(document).on('change', '.b2s-edit-template-range', function () {
    jQuery('.b2s-edit-template-post-content').trigger('keyup');
});

// Edit Template - Excerpt Range Change Handler
jQuery(document).on('change', '.b2s-edit-template-excerpt-range', function () {
    jQuery('.b2s-edit-template-post-content').trigger('keyup');
});

// Edit Template - Comment Range Change Handler
jQuery(document).on('change', '.b2s-edit-template-range-comment', function () {
    jQuery('.b2s-edit-template-comment').trigger('keyup');
});

// Edit Template - Comment Excerpt Range Change Handler
jQuery(document).on('change', '.b2s-edit-template-excerpt-range-comment', function () {
    jQuery('.b2s-edit-template-comment').trigger('keyup');
});

// Edit Template - Load Default
jQuery(document).on('click', '.b2s-edit-template-load-default', function () {
    jQuery('.b2s-edit-template-content').hide();
    jQuery('.b2s-edit-template-save-btn').hide();
    jQuery('.b2s-edit-template-save-success').hide();
    jQuery('.b2s-edit-template-save-failed').hide();
    jQuery('.b2s-loading-area').show();
    var networkType = jQuery(this).attr('data-network-type');

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_load_default_post_template',
            'networkId': jQuery('#b2s-edit-template-network-id').val(),
            'networkType': networkType,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            jQuery('.b2s-edit-template-load-default-failed').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            if (data.result == true) {
                jQuery('.b2s-template-tab-' + networkType).html(data.html);
                initAiTemplateSettings(jQuery('.b2s-template-tab-' + networkType));
                setEditTemplateMode(getCurrentEditTemplateMode());
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-edit-template-load-default-failed').show();
            }
        }
    });
});

// Edit Template - Save Button
jQuery(document).on('click', '.b2s-edit-template-save-btn', function () {
    b2sStandardSettingsChanged = false;

    //When reloading always reset ignore template to false to make sure any changes in template will be applied
    jQuery('#b2sIgnoreTemplate').val(0);

    if (jQuery('#b2sUserVersion').val() < 1 && jQuery('#b2s-edit-template-network-id').val() != 1 && jQuery('#b2s-edit-template-network-id').val() != 3 && jQuery('#b2s-edit-template-network-id').val() != 19) {
        return false;
    }

    if (jQuery('#b2s-edit-template-network-id').val() == 12) {
        var matches = jQuery('.b2s-edit-template-post-content').val().match(/#/g);
        if (matches != null && matches.length > 30) {
            jQuery('.b2s-edit-template-post-content').addClass('error');
            jQuery('.b2s-edit-template-hashtag-warning').show();
            return false;
        } else {
            jQuery('.b2s-edit-template-post-content').removeClass('error');
            jQuery('.b2s-edit-template-hashtag-warning').hide();
        }
    }

    jQuery('.b2s-edit-template-content').hide();
    jQuery('.b2s-edit-template-save-btn').hide();
    jQuery('.b2s-edit-template-save-success').hide();
    jQuery('.b2s-edit-template-save-failed').hide();
    jQuery('.b2s-loading-area').show();

    template_data = {};

    jQuery('.b2s-edit-template-post-content').each(function (i, obj) {
        var networkType = jQuery(obj).attr('data-network-type');
        template_data[networkType] = {};
        if (jQuery('.b2s-edit-template-multi-kind[data-network-type="' + networkType + '"]').val() == 1) {
            template_data[networkType]['multi_kind'] = 1;
            template_data[networkType]['type_kind'] = {};
            jQuery('.b2s-edit-template-range[data-network-type="' + networkType + '"]').each(function (index) {
                var type_kind = jQuery(this).data('network-type-kind');
                template_data[networkType]['type_kind'][type_kind] = {};
                template_data[networkType]['type_kind'][type_kind]['range_max'] = jQuery(this).val();
                template_data[networkType]['type_kind'][type_kind]['excerpt_range_max'] = jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + networkType + '"][data-network-type-kind="' + type_kind + '"]').val();
            });
        } else {
            template_data[networkType]['multi_kind'] = 0;
            template_data[networkType]['range_max'] = jQuery('.b2s-edit-template-range[data-network-type="' + networkType + '"]').val();
            template_data[networkType]['excerpt_range_max'] = jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + networkType + '"]').val();
        }

        template_data[networkType]['format'] = jQuery('.b2s-edit-template-post-format[data-network-type="' + networkType + '"]').val();
        template_data[networkType]['content'] = jQuery('.b2s-edit-template-post-content[data-network-type="' + networkType + '"]').val();
        // Save default comment for networks that support comments
        if (typeof jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]') != "undefined") {
            var defaultCommentValue = jQuery('.b2s-edit-template-comment[data-network-type="' + networkType + '"]').val();
            if (defaultCommentValue !== undefined) {
                template_data[networkType]['comment'] = defaultCommentValue;
            }
            
            // Save comment character limits
            var commentRangeMax = jQuery('.b2s-edit-template-range-comment[data-network-type="' + networkType + '"]').val();
            var commentExcerptRangeMax = jQuery('.b2s-edit-template-excerpt-range-comment[data-network-type="' + networkType + '"]').val();
            if (commentRangeMax !== undefined && commentExcerptRangeMax !== undefined) {
                template_data[networkType]['comment_range_max'] = commentRangeMax;
                template_data[networkType]['comment_excerpt_range_max'] = commentExcerptRangeMax;
            }
        }
        
        if (jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45){
            if (jQuery('.b2s-twitter-thread[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['twitterThreads'] = true;
            }else{
                template_data[networkType]['twitterThreads'] = false;
            }
        }

        if (typeof jQuery('.b2s-edit-template-enable-link') != "undefined") {
            if (jQuery('.b2s-edit-template-enable-link[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['addLink'] = true;
            } else {
                template_data[networkType]['addLink'] = false;
            }
        }
        if (jQuery('.b2s-edit-template-share-as-story[data-network-type="' + networkType + '"]').length) {
            if (jQuery('.b2s-edit-template-share-as-story[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['share_as_story'] = 1;
            } else {
                template_data[networkType]['share_as_story'] = 0;
            }
        }
        if (jQuery('#b2s-edit-template-network-id').val() == 12) {
            if (jQuery('.b2s-edit-template-shuffle-hashtags[data-network-type="' + networkType + '"]').is(':checked')) {
                template_data[networkType]['shuffleHashtags'] = true;
            } else {
                template_data[networkType]['shuffleHashtags'] = false;
            }
            template_data[networkType]['frameColor'] = jQuery('#b2s-edit-template-colorpicker').val();
        }
    });


    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_post_template',
            'template_data': template_data,
            'networkId': jQuery('#b2s-edit-template-network-id').val(),
            'link_no_cache': (jQuery("#link-no-cache").is(':checked') ? '1' : '0'),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            jQuery('.b2s-edit-template-save-failed').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();
            jQuery('.b2s-edit-template-content').show();
            jQuery('.b2s-edit-template-save-btn').show();
            if (data.result == true) {
            
            var networkId= jQuery('#b2s-edit-template-network-id').val();
            var activeTemplateType= getActiveTemplateType();
            if(networkId == 12){
                activeTemplateType = 1;
            }

            //Update the ship items with new template values
            var authIdsToReload= getAuthIdsFromNetwork(networkId, activeTemplateType);
                authIdsToReload.forEach(dataNetworkAuthId => {

                    var chosenPostFormat = null;
               
                    if (template_data &&template_data[activeTemplateType] &&template_data[activeTemplateType]['format']){
                        chosenPostFormat = template_data[activeTemplateType]['format'];
                    }
                    
                    triggerReloadShipItem(dataNetworkAuthId, chosenPostFormat);
    
                });

                jQuery('.b2s-edit-template-save-success').show();
                setTimeout(function () {
                    jQuery('.b2s-edit-template-save-success').fadeOut();
                }, 3000);
                jQuery('#b2s-edit-template').modal('hide');
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-edit-template-save-failed').show();
            }
        }
    });
});

function getPostFormatWpType(networkId, networkType){

       var el = jQuery(
        '.b2s-user-network-settings-post-format-area' +
        '[data-network-id="' + networkId + '"]' +
        '[data-network-type="' + networkType + '"]'
    );

    if (el.length > 0) {
        return el.attr('data-post-wp-type');
    }

    return null; 
   
}

function getPostFormatTypeByNetwork(networkId, networkType) {
    var el = jQuery(
        '.b2s-user-network-settings-post-format-area' +
        '[data-network-id="' + networkId + '"]' +
        '[data-network-type="' + networkType + '"]'
    );

    if (el.length > 0) {
        return el.attr('data-post-format-type');
    }

    return null; 
}

function getActiveTemplateType() {

    // Profile
    if (jQuery('.b2s-template-profile').closest('li.active').length > 0) {
        return 0;
    }
    // Page
    if (jQuery('.b2s-template-page').closest('li.active').length > 0) {
        return 1;
    }
    // Group
    if (jQuery('.b2s-template-group').closest('li.active').length > 0) {
        return 2;
    }

    return 0; 
}

function triggerClickByNetworkType(networkType) {

    if(networkType == 0){
        
        jQuery('.b2s-template-profile').tab('show');
    }

    if(networkType == 1){
    
        jQuery('.b2s-template-page').tab('show');
    }

    if(networkType == 2){
        
        jQuery('.b2s-template-group').tab('show');
    }
}

function triggerReloadShipItem(authId,chosenPostFormat) {

    var selector = 'div.b2s-network-select-btn'+
        '[data-network-auth-id="' + authId + '"]';

    var element = jQuery(selector);

    if (element.length > 0) {
        element.trigger('click',[true, chosenPostFormat]);
        return true;
    }

    return false;
}

function getAuthIdsFromNetwork(networkId, activeTemplateType) {
    var ids = [];

    jQuery('div.b2s-post-item[data-network-id="' + networkId + '"][data-network-type="' + activeTemplateType + '"]:visible').each(function () {
        var authId = jQuery(this).attr('data-network-auth-id');
        if (authId && ids.indexOf(authId) === -1) {
            ids.push(authId);
        }
    });

    return ids;
}

document.addEventListener('dragstart', function (event) {
    event.dataTransfer.setData('Text', event.target.innerHTML);
});

document.addEventListener('drop', function (event) {
    setTimeout(function () {
        jQuery('.b2s-edit-template-post-content').trigger('keyup');
    }, 0);
});

jQuery(document).on('mousedown mouseup keydown keyup', '.b2s-edit-template-post-content', function () {
    var tb = jQuery(this).get(0);
    jQuery('.b2s-edit-template-content-selection-start[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionStart);
    jQuery('.b2s-edit-template-content-selection-end[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(tb.selectionEnd);
});

jQuery(document).on('keyup', '.b2s-edit-template-post-content', function () {
    var post = generateExamplePost(
        jQuery(this).val().replace(/\n/g, "<br>"),
        jQuery('.b2s-edit-template-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val(),
        jQuery('.b2s-edit-template-excerpt-range[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').val()
    );
    jQuery('.b2s-edit-template-preview-content[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(post);
    if (typeof jQuery('#b2s_post_title').val() != 'undefined' && jQuery('#b2s_post_title').val() != '') {
        jQuery('.b2s-edit-template-preview-title[data-network-type="' + jQuery(this).attr('data-network-type') + '"]').html(jQuery('#b2s_post_title').val());
    }
});

function generateExamplePost(template, content_range, exerpt_range) {
    // Strip WP block delimiter comments
    template = template.replace(/<!--[\s\S]*?-->/g, '');
    // Escape user-supplied HTML to prevent XSS. The template arrives with \n already
    // replaced by <br> at the call site, so we preserve those with a placeholder.
    template = template
        .replace(/&/g, '&amp;')
        .replace(/<br>/gi, '\x01')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/\x01/g, '<br>');
    if (jQuery('#b2s_use_post').val() == 'true') {
        var content = '';
        var exerpt = '';
        var title = '';
        var url = '';
        var author = '';
        var keywords = '';
        if (typeof jQuery('#b2s_post_content').val() != 'undefined' && jQuery('#b2s_post_content').val() != '') {
            content = b2sGetExcerpt(jQuery('#b2s_post_content').val(), content_range);
        }
        if (typeof jQuery('#b2s_post_excerpt').val() != 'undefined' && jQuery('#b2s_post_excerpt').val() != '') {
            exerpt = b2sGetExcerpt(jQuery('#b2s_post_excerpt').val(), exerpt_range);
        }
        if (typeof jQuery('#b2s_post_title').val() != 'undefined' && jQuery('#b2s_post_title').val() != '') {
            title = jQuery('#b2s_post_title').val();
        }
        if (typeof jQuery('#b2s_post_url').val() != 'undefined' && jQuery('#b2s_post_url').val() != '') {
            url = jQuery('#b2s_post_url').val();
        }
        if (typeof jQuery('#b2s_post_author').val() != 'undefined' && jQuery('#b2s_post_author').val() != '') {
            author = jQuery('#b2s_post_author').val();
        }
        if (typeof jQuery('#b2s_post_keywords').val() != 'undefined' && jQuery('#b2s_post_keywords').val() != '') {
            keywords = jQuery('#b2s_post_keywords').val();
        }
  
        template = template.replace(/{CONTENT}/g, content);
        template = template.replace(/{EXCERPT}/g, exerpt);
        template = template.replace(/{TITLE}/g, title);
        template = template.replace(/{URL}/g, url);
        template = template.replace(/{AUTHOR}/g, author);
        template = template.replace(/{KEYWORDS}/g, keywords);

    }
    if (typeof jQuery('.b2s-edit-template-limit').val() != 'undefined' && jQuery('.b2s-edit-template-limit').val() > 0) {
        var limit = parseInt(jQuery('.b2s-edit-template-limit').val(), 10);
        if (template.length > limit || jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45) {
            if ((jQuery('#b2s-edit-template-network-id').val() == 2 || jQuery('#b2s-edit-template-network-id').val() == 45) && jQuery('.b2s-edit-template-post-format').val() == 0) {
                template = b2sGetExcerpt(template, limit - 24);
            } else {
                template = b2sGetExcerpt(template, limit);
            }
        }
    }

    //tumblr special preview case
    if(jQuery('#b2s-edit-template-network-id').val() == 4){
        var postFormat= jQuery('.b2s-edit-template-post-format').val();

        if(postFormat == 3){
            template= stripTags(template);
            template= template.replace(/(\r\n|\n|\r)/gm, '');
            template = template.substring(0, 125);
            template = template + "...";
        }

        if(postFormat ==1){
            template = title;
        }else
        {
            jQuery('.b2s-edit-template-text-preview-tumblr-title').html(title);
        }
        
        jQuery('.b2s-edit-template-text-preview-tumblr-hashtags').html(keywords);

    }
    return template;
}

function updateToggleCommentValue($toggle) {
    if (!$toggle || $toggle.length === 0) {
        return;
    }
    $toggle.each(function () {
        var $item = jQuery(this);
        $item.val($item.is(':checked') ? '1' : '0');
    });
}

// Toggle First Comment Field
jQuery(document).on('change', '.b2s-toggle-comment', function(e) {
    var authId = jQuery(this).data('network-auth-id');
    var networkCount = jQuery(this).data('network-count');
    var commentArea = jQuery('.b2s-comment-area-' + authId + '[data-network-count="' + networkCount + '"]');
    var isChecked = jQuery(this).is(':checked');
    
    // Check if toggle is disabled by story or thread
    if (jQuery(this).attr('data-disabled-by-story') == 'true' || jQuery(this).attr('data-disabled-by-thread') == 'true') {
        e.preventDefault();
        return false;
    }
    
    updateToggleCommentValue(jQuery(this));

    // Toggle the comment area visibility
    if (isChecked) {
        commentArea.slideDown(300);
        var commentTextarea = jQuery('.b2s-post-item-details-item-comment-input[data-network-auth-id="' + authId + '"][data-network-count="' + networkCount + '"]');
        
        // When comment is enabled, uncheck and grey out story button
        var networkId = commentArea.attr('data-network-id');
        if (jQuery('#is_video').val() == 1 && (networkId == 12 || networkId == 1)) {
            var storyCheckbox = jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + authId + '"][data-network-count="-1"]');
            
            if (storyCheckbox.prop('checked')) {
                storyCheckbox.prop('checked', false);
                // Trigger the story unchecked logic to show other fields
                storyCheckbox.trigger('click'); // This will uncheck it
                storyCheckbox.prop('checked', false); // Make sure it stays unchecked
            }
            
            // Grey out story option
            var storyContainer = storyCheckbox.closest('.b2s-share-as-story-fields');
            storyContainer.css({
                'opacity': '0.5',
                'pointer-events': 'none'
            });
            storyContainer.attr('data-disabled-by-comment', 'true');
            storyCheckbox.attr('data-disabled-by-comment', 'true');
        }
        
    } else {
        // Re-enable story button when comment is hidden (toggle unchecked)
        commentArea.slideUp(300);
        var commentTextarea = jQuery('.b2s-post-item-details-item-comment-input[data-network-auth-id="' + authId + '"][data-network-count="' + networkCount + '"]');
        var networkId = commentArea.attr('data-network-id');
        if (jQuery('#is_video').val() == 1 && (networkId == 12 || networkId == 1)) {
            var storyCheckbox = jQuery('.b2s-post-item-option-share-as-story[data-network-auth-id="' + authId + '"][data-network-count="-1"]');
            var storyContainer = storyCheckbox.closest('.b2s-share-as-story-fields');
            
            if (storyContainer.attr('data-disabled-by-comment') == 'true') {
                storyContainer.css({
                    'opacity': '1',
                    'pointer-events': 'auto'
                });
                storyContainer.removeAttr('data-disabled-by-comment');
                storyCheckbox.removeAttr('data-disabled-by-comment');
            }
        }
        
    }
});

function enableCommentByThread(networkAuthId, networkCount) {
    var commentArea = jQuery('.b2s-comment-area-' + networkAuthId + '[data-network-count="' + networkCount + '"]');
    var toggleBtn = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]');
    var commentInput = commentArea.find('.b2s-post-item-details-item-comment-input');
    
    // Re-enable if it was disabled by thread
    if (toggleBtn.attr('data-disabled-by-thread') == 'true') {
        toggleBtn.removeAttr('data-disabled-by-thread');
        
        // Re-enable toggle switch
        toggleBtn.prop('disabled', false);
        toggleBtn.removeAttr('data-disabled-by-thread');
        
        // If there's a comment value, check toggle and show comment area
        if (commentInput.val() && commentInput.val().trim() !== '') {
            if (!commentArea.is(':visible')) {
                commentArea.slideDown(300);
                toggleBtn.prop('checked', true);
                updateToggleCommentValue(toggleBtn);
            }
        }
    }

}

function disableCommentByThread(networkAuthId, networkCount) {

    var commentArea = jQuery('.b2s-comment-area-' + networkAuthId + '[data-network-count="' + networkCount + '"]');
    var toggleBtn = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]');
        
    // Hide comment area if visible
    if (commentArea.is(':visible')) {
        commentArea.slideUp(300);
        // Uncheck the toggle
        toggleBtn.prop('checked', false);
        updateToggleCommentValue(toggleBtn);
    }
    
    // Disable toggle switch
    toggleBtn.prop('disabled', true);
    toggleBtn.attr('data-disabled-by-thread', 'true');

}

function disableCommentByStory(networkAuthId, networkCount) {
    var commentArea = jQuery('.b2s-comment-area-' + networkAuthId + '[data-network-count="' + networkCount + '"]');
    var toggleBtn = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]');
    
    // Hide comment area if visible
    if (commentArea.is(':visible')) {
        commentArea.slideUp(300);
    }

    
    // Grey out and disable toggle switch
    toggleBtn.prop('disabled', true);
    toggleBtn.prop('checked', false);
    updateToggleCommentValue(toggleBtn);
    toggleBtn.attr('data-disabled-by-story', 'true');

}

function enableCommentByStory(networkAuthId, networkCount) {
    var commentArea = jQuery('.b2s-comment-area-' + networkAuthId + '[data-network-count="' + networkCount + '"]');
    var toggleBtn = jQuery('.b2s-toggle-comment[data-network-auth-id="' + networkAuthId + '"][data-network-count="' + networkCount + '"]');
    var commentInput = commentArea.find('.b2s-post-item-details-item-comment-input');
    
    // Re-enable if it was disabled by story
    if (toggleBtn.attr('data-disabled-by-story') == 'true') {
        toggleBtn.removeAttr('data-disabled-by-story');
        
        // Re-enable toggle switch
        toggleBtn.prop('disabled', false);
        
        // If there's a comment value, check the toggle and show comment area
        if (commentInput.val() && commentInput.val().trim() !== '') {
            if (!commentArea.is(':visible')) {
                commentArea.slideDown(300);
                toggleBtn.prop('checked', true);
                updateToggleCommentValue(toggleBtn);
            }
        }
    }
}