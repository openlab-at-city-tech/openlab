jQuery.noConflict();

var b2sTosXingGroupCount = 0;
var currentOGImage = '';
var changedOGImage = false;

jQuery(document).on('heartbeat-send', function (e, data) {
    data['b2s_heartbeat'] = 'b2s_listener';
});

jQuery.xhrPool = [];

jQuery(window).on("load", function () {
    
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
        jQuery('#b2s-sched-post-modal').modal('show');
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
            if ((networkId == "2" || networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
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
            }
        }
    });
    return false;
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
    if (text == "" && networkId == 2) {
        text = jQuery('#b2sTwitterOrginalPost').val();
    }
    jQuery('.b2s-post-item-details-item-message-input[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val(text);
    networkCount(networkAuthId);
    return false;
});
jQuery(document).on("click", ".b2s-network-select-btn", function () {
    var networkAuthId = jQuery(this).attr('data-network-auth-id');
    var networkId = jQuery(this).attr('data-network-id');
    var networkType = jQuery(this).attr('data-network-type');
    var metaType = jQuery(this).attr('data-meta-type');
    var schedulerDays = jQuery(this).attr('scheduler-days');

    //doppelklick Schutz
    if (!jQuery(this).hasClass('b2s-network-select-btn-deactivate')) {
        //active?
        if (!jQuery(this).children().hasClass('active')) {
            //TOS XING Groups
            if ((networkId == 8 || networkId == 19) && networkType == 2) {
                if ((b2sTosXingGroupCount == jQuery('#b2sTosXingGroupCrosspostingLimit').val()) || (networkId == 19 && jQuery('.b2s-network-select-btn[data-network-id="' + networkId + '"][data-network-type="' + networkType + '"][data-network-tos-group-id="' + jQuery(this).attr('data-network-tos-group-id') + '"]').children().hasClass('active'))) {
                    jQuery('#b2s-tos-xing-group-max-count-modal').modal('show');
                    return false;
                } else {
                    b2sTosXingGroupCount++;
                }
            }
            //schon vorhanden?
            if (jQuery('.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]').length > 0 && !jQuery('.b2s-post-item[data-network-auth-id="' + networkAuthId + '"]').hasClass('b2s-post-item-connection-fail-dummy')) {
                activatePortal(networkAuthId);
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
                        if ((networkId == "2" || networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
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
                            if ((networkId == "2" || networkId == "24")) {
                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
                            }
                        } else {
                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + networkAuthId + '"]').prop("readonly", true);
                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + networkAuthId + '"]').hide();
                            if ((networkId == "2" || networkId == "24")) {
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
                        jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + networkAuthId + '"]').attr("style", "display:none !important");
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
                if (networkId == 2) {
                    if (jQuery('.b2s-post-item[data-network-id="' + networkId + '"]:visible').length == 1) {
                        jQuery('.tw-textarea-input[data-network-auth-id="' + networkAuthId + '"]').text(jQuery('#b2sTwitterOrginalPost').val());
                    } else {
                        jQuery('.tw-textarea-input[data-network-auth-id="' + networkAuthId + '"]').text("");
                    }
                }
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
                        'isVideo': jQuery('#b2sIsVideo').val(),
                        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
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

                                if(schedulerDays == 30 || schedulerDays == 365){
                                    var end = "+"+schedulerDays+"d";
                                } else {
                                    var end = "";
                                }
                                jQuery(".b2s-post-item-details-release-input-date").datepicker({
                                    format: dateFormat,
                                    language: language,
                                    maxViewMode: 2,
                                    todayHighlight: true,
                                    startDate: today,
                                    calendarWeeks: true,
                                    autoclose: true,
                                    endDate: end
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
                                        if ((jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val() == 1 && ((data.networkId == 1 && (data.networkType == 1 || data.networkType == 2)) || (data.networkId == 3 && (data.networkType == 0 || data.networkType == 1)) || (data.networkId == 2))) || data.networkId == 12) {
                                            jQuery('.b2s-multi-image-area[data-network-auth-id="' + data.networkAuthId + '"]').show();
                                        }
                                        jQuery('.b2s-post-ship-item-post-format-text[data-network-auth-id="' + data.networkAuthId + '"]').html(postFormatText[postFormatType][jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val()]);
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"]').val(jQuery('.b2sNetworkSettingsPostFormatCurrent[data-network-type="' + data.networkType + '"][data-network-id="' + data.networkId + '"]').val());
                                        var isMetaChecked = false;
                                        var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
                                        if (typeof data.networkId != 'undefined' && jQuery.inArray(data.networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
                                            isMetaChecked = true;
                                        }
                                        if ((data.networkId == "2" || data.networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
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
                                            if ((networkId == "2" || networkId == "24")) {
                                                jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
                                            }
                                        } else {
                                            jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                            jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                            jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').hide();
                                            if ((networkId == "2" || networkId == "24")) {
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


                                    //Twitter TOS 032018 - protected multiple accounts with same content to same time
                                    //delete comment field one more
                                    if (data.networkId == 2) {

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

                                    //Content Curation
                                    if (jQuery('#b2sPostType').val() == 'ex') {
                                        jQuery('.b2s-post-item-details-preview-title[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                        jQuery('.b2s-post-item-details-preview-desc[data-network-auth-id="' + data.networkAuthId + '"]').prop("readonly", true);
                                        jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + data.networkAuthId + '"]').attr("style", "display:none !important");
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
                                        if (jQuery('#b2sExPostFormat').val() == 0 || jQuery('#b2sExPostFormat').val() == 1 || jQuery('#b2sExPostFormat').val() == 2) {
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

                                }

                                if (data.networkId == 4 && (jQuery('#b2sExPostFormat').val() == 0 || jQuery('#b2sExPostFormat').val() == 1 || jQuery('#b2sExPostFormat').val() == 2)) {
                                    if (jQuery('#user_version').val() >= 1) {
                                        var exPostFormat = jQuery('#b2sExPostFormat').val();
                                        if (exPostFormat == 0) {
                                            exPostFormat = 2;
                                        }
                                        if (exPostFormat == 1) {
                                            exPostFormat = 0;
                                        }
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 0 + '"]').removeAttr('selected');
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 1 + '"]').removeAttr('selected');
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + 2 + '"]').removeAttr('selected');
                                        jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + data.networkAuthId + '"] option[value="' + exPostFormat + '"]').attr('selected', 'selected').change();
                                    }
                                }

                                //Draft
                                if (data.draft == true) {
                                    if (data.draftActions.post_format == "0" || data.draftActions.post_format == "1") {
                                        jQuery('.b2s-post-ship-item-post-format[data-network-auth-id="' + data.networkAuthId + '"]').trigger('click');
                                        jQuery('.b2s-post-item-details-preview-url-reload[data-network-auth-id="' + data.networkAuthId + '"]').addClass('disabled');
                                        jQuery('.b2s-user-network-settings-post-format[value="' + data.draftActions.post_format + '"][data-network-auth-id="' + data.networkAuthId + '"][data-network-id="' + networkId + '"][data-network-type="' + networkType + '"]').trigger('click');
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
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').val(data.draftActions.releaseSelect);
                                        jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + data.networkAuthId + '"]').trigger('change');

                                        jQuery.each(data.draftActions.date, function (index, value) {
                                            if (index == "1" || index == "2") {
                                                jQuery('.b2s-post-item-details-release-input-add[data-network-auth-id="' + data.networkAuthId + '"][data-network-count="' + (index - 1) + '"]').trigger('click');
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

                                    if (data.networkId == 2) {
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
        if ((networkId == "2" || networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
            isMetaChecked = true;
        }

        if (postFormat == "0" && (networkId == "1" || networkId == "2")) { //isLinkPost for Faceboo or Twitter
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
jQuery(document).on('click', '.b2s-post-ship-item-post-format', function () {
    openPostFormat(jQuery(this).attr('data-network-id'), jQuery(this).attr('data-network-type'), jQuery(this).attr('data-network-auth-id'), jQuery(this).attr('data-post-wp-type'), true);
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
jQuery(document).on('change', '.b2s-post-item-details-release-input-date-select', function () {
    var dataNetworkCount = 0;
    if (jQuery(this).val() == 0) {
        //TOS Twitter 032018 - none multiple accounts post same content to same time
        if (jQuery(this).attr('data-network-id') == 2) {
            jQuery('.b2s-network-tos-sched-warning[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').hide();
        }
        if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == 1 || jQuery(this).attr('data-network-id') == 12) {
            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').show();
        }
    }
    if (jQuery(this).val() == 2) {
        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2s-sched-post-modal').modal('show');
            return false;
        } else {
            //TOS Twitter 032018 - none multiple accounts post same content to same time
            if (jQuery(this).attr('data-network-id') == 2) {
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
            if (jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').val() == 1 || jQuery(this).attr('data-network-id') == 12) {
                jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').show();
            }
        }
    }
    if (jQuery(this).val() == 1) {
        if (jQuery(this).attr('data-user-version') == 0) {
            jQuery('#b2s-sched-post-modal').modal('show');
            return false;
        } else {

            //TOS Twitter 032018 - none multiple accounts post same content to same time
            if (jQuery(this).attr('data-network-id') == 2) {
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

            jQuery('.b2s-multi-image-area[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"][data-network-count="-1"]').hide();
        }
    }
    releaseChoose(jQuery(this).val(), jQuery(this).attr('data-network-auth-id'), dataNetworkCount);
//    jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + jQuery(this).attr('data-network-auth-id') + '"]').focus();
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
        if (jQuery(this).children().hasClass('active')) {
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
jQuery(document).on('click', '.b2s-post-item-details-release-input-add', function () {
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
            url = "http://" + url;
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
    if ((networkId == "2" || networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
        isMetaChecked = true;
    }

    if (postFormat == "0" && (networkId == "1" || networkId == "2")) { //isLinkPost for Facebook or Twitter
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
                if (jQuery(this).find('.active').length > 0) {
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

    if (jQuery('#b2sInsertImageType').val() == '1') { //HTML-Network
        var sceditor = jQuery('.b2s-post-item-details-item-message-input-allow-html[data-network-auth-id="' + networkAuthId + '"]').sceditor('instance');
        sceditor.insert("<br /><img src='" + currentImage + "'/><br />");
        jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage); //Torial
    } else {
        //default
        if (networkCountId == -1) {
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').attr('src', currentImage);
            jQuery('.b2s-post-item-details-url-image[data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-img-required');
            jQuery('.b2s-image-url-hidden-field[data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
            jQuery('.b2s-image-remove-btn[data-network-auth-id="' + networkAuthId + '"]').show();
            jQuery('.cropper-open[data-network-auth-id="' + networkAuthId + '"]').show();

        } else {
            //customize sched content
            jQuery('.b2s-post-item-details-url-image[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').attr('src', currentImage);
            jQuery('.b2s-post-item-details-url-image[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').removeClass('b2s-img-required');
            jQuery('.b2s-image-url-hidden-field[data-network-count="' + networkCountId + '"][data-network-auth-id="' + networkAuthId + '"]').val(currentImage);
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
    jQuery('.b2s-post-item-details-url-image[data-network-image-change="1"]' + noGifs).attr('src', jQuery('input[name=image_url]:checked').val());
    jQuery('#b2s_blog_default_image').val(jQuery('input[name=image_url]:checked').val());
    jQuery('.b2s-post-item-details-url-image' + noGifs).removeClass('b2s-img-required');
    jQuery('.b2s-image-url-hidden-field' + noGifs).val(jQuery('input[name=image_url]:checked').val());
    jQuery('.b2s-image-remove-btn' + noGifs).show();
    jQuery('.cropper-open' + noGifs).show();
    jQuery('.b2s-post-item-details-release-input-date-select' + noGifs).each(function () {
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
                    '<img class="img-thumbnail networkImage" alt="blogImage" src="' + attachment.url + '">' +
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
jQuery("#b2sNetworkSent").validate({
    ignore: "",
    errorPlacement: function (error, element) {
        return false;
    },
    submitHandler: function (form) {
        if (checkNetworkSelected() == false) {
            return false;
        }
        if (checkPostSchedOnBlog() == false) {
            return false;
        }
        if (checkImageByImageNetworks() == false) {
            return false;
        }

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
                var content = data.content;
                jQuery(".b2s-loading-area").hide();
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
    jQuery('.b2s-network-list.active').each(function () {
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
                        if (jsonMandantIds.indexOf(mandantId) !== -1 && !jQuery(this).hasClass('active')) {
                            //remove
                            var newMandant = new Array();
                            jQuery(jsonMandantIds).each(function (index, item) {
                                if (item !== mandantId) {
                                    newMandant.push(item);
                                }
                            });
                            jQuery(this).parents('.b2s-sidbar-wrapper-nav-li').attr('data-mandant-id', JSON.stringify(newMandant));
                        } else if (jsonMandantIds.indexOf(mandantId) == -1 && jQuery(this).hasClass('active')) {
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
    jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').children().removeClass('active').find('.b2s-network-status-img').addClass('b2s-network-hide');
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
    jQuery('.b2s-network-select-btn[data-network-auth-id="' + networkAuthId + '"]').children().addClass('active').find('.b2s-network-hide').removeClass('b2s-network-hide');
    checkNetworkSelected();
    submitArea();
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
                        return false;
                    }
                    result = false;
                }
            });
        }
    });
    return result;
}


function releaseChoose(choose, dataNetworkAuthId, dataNetworkCount) {
    var selectorInput = '[data-network-auth-id="' + dataNetworkAuthId + '"]';
    jQuery('.b2s-post-item-details-release-area-details-row' + selectorInput).hide();
    if (choose == 0) {

        //since 4.8.0 customize content
        if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-customize-content') == "1") {
            jQuery('.b2s-post-item-details-item-message-input' + selectorInput + '[data-network-count="-1"]').removeAttr('disabled');
            jQuery('.b2s-post-item-details-item-message-area' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-post-item-details-url-image' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-select-image-modal-open' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-image-remove-btn' + selectorInput + '[data-network-count="-1"]').show();
            jQuery('.b2s-post-original-area' + selectorInput).addClass('col-sm-7').addClass('col-lg-9');
            jQuery('.b2s-post-tool-area' + selectorInput).show();
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

        //since 4.8.0 customize content
        if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-customize-content') == "1") {
            jQuery('.b2s-post-item-details-item-message-input' + selectorInput + '[data-network-count="-1"]').prop('disabled', true);
            jQuery('.b2s-post-item-details-item-message-area' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.b2s-post-item-details-url-image' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.b2s-select-image-modal-open' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.b2s-image-remove-btn' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.cropper-open' + selectorInput + '[data-network-count="-1"]').hide();
            jQuery('.b2s-post-original-area' + selectorInput).removeClass('col-sm-7').removeClass('col-lg-9');
            jQuery('.b2s-post-tool-area' + selectorInput).hide();
            //TOS Network Twitter
            if (jQuery('.b2s-post-item-details-release-input-date-select' + selectorInput).attr('data-network-id') == "2") {
                jQuery('.b2s-post-ship-item-copy-original-text' + selectorInput + '[data-network-count="0"]').trigger('click');
            }
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
                url = "http://" + url;
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
        if (networkId == "2") { //twitter
            if (url.length != "0") {
                limit = limit - 26;
            }
        }
        if (networkId == "3") { //linkedin
            if (url.length != "0") {
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
            if (url.length != "0") {
                limit = limit - url.length;
            }
        }
        if (networkId == "38") { //mastodon
            if (url.length != "0") {
                limit = limit - url.length;
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
            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength+ " + "+url.length);

        } else {
            jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength);
        }
    }
}

function networkCount(networkAuthId) {
    var twitterLimit = 280;
    var mastodonLimit = 500;
    var networkCountId = -1; //default;
    if (jQuery(':focus').length > 0) {
        var attr = jQuery(':focus').attr('data-network-count');
        if (typeof attr !== typeof undefined && attr !== false) {
            networkCountId = attr;
        }
    }
    var url = jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val();
    var text = jQuery(".b2s-post-item-details-item-message-input[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").val();
    jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").removeClass("error");
    if (typeof url !== typeof undefined && url !== false) {
        if (url.length != "0") {
            if (url.indexOf("http://") == -1 && url.indexOf("https://") == -1) {
                url = "http://" + url;
                jQuery(".b2s-post-item-details-item-url-input[data-network-auth-id='" + networkAuthId + "']").val(url);
            }
            if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "2") { //twitter
                twitterLimit = twitterLimit - 26;
            }
            if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "38") { //mastodon
                mastodonLimit = mastodonLimit - url.length;
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
        jQuery(".b2s-post-item-countChar[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(textLength);
        if (jQuery(".b2s-post-item-details-item-message-input[data-network-auth-id='" + networkAuthId + "']").attr('data-network-id') == "2") {
            var threadCount = Math.ceil(textLength / twitterLimit);
            jQuery(".b2s-post-item-count-threads[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").html(threadCount);
            if (threadCount >= 2) {
                jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").show();
            } else {
                jQuery(".b2s-post-item-show-thread-count[data-network-count='" + networkCountId + "'][data-network-auth-id='" + networkAuthId + "']").hide();
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
    jQuery('.b2s-network-select-btn').children().removeClass('active').find('.b2s-network-status-img').addClass('b2s-network-hide');
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

jQuery(document).on('click', '.b2sInfoPostRelayModalBtn', function () {
    jQuery('#b2sInfoPostRelayModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoSchedTimesModalBtn', function () {
    jQuery('#b2sInfoSchedTimesModal').modal('show');
});
jQuery(document).on('click', '.b2s-network-setting-save-btn', function () {
    jQuery('#b2s-network-setting-save').modal('show');
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
        var text = jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').val();
        var start = jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').attr('selectionStart');
        var end = jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').attr('selectionEnd');
        if (typeof start == 'undefined' || typeof end == 'undefined') {
            start = text.length;
            end = text.length;
        }
        var newText = text.slice(0, start) + emoji + text.slice(end);
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').val(newText);
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').focus();
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').prop("selectionStart", parseInt(start) + emoji.length);
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').prop("selectionEnd", parseInt(start) + emoji.length);
        jQuery('.b2s-post-item-details-item-message-input[data-network-auth-id="' + currentEmojiNetworkAuthId + '"][data-network-count="' + currentEmojiNetworkCount + '"]').trigger('keyup');
    }
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
        if (jQuery('#user_version').val() >= 2) {
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"]').removeClass('b2s-settings-checked');
            var currentPostFormat = jQuery('.b2s-post-item-details-post-format[data-network-auth-id="' + networkAuthId + '"]').val();
            jQuery('.b2s-user-network-settings-post-format[data-network-type="' + networkType + '"][data-network-id="' + networkId + '"][data-post-format="' + currentPostFormat + '"]').addClass('b2s-settings-checked');
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

    //Edit Meta Tags
    var isMetaChecked = false;
    var ogMetaNetworks = jQuery('#ogMetaNetworks').val().split(";");
    if (typeof networkId != 'undefined' && jQuery.inArray(networkId.toString(), ogMetaNetworks) != -1 && jQuery('#isOgMetaChecked').val() == "1") {
        isMetaChecked = true;
    }
    if ((networkId == "2" || networkId == "24") && jQuery('#isCardMetaChecked').val() == "1") {
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
            if (networkId == 1 || networkId == 2 && postFormat == 0) {
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
        jQuery('.b2s-load-info-meta-tag-modal[data-network-auth-id="' + networkAuthId + '"]').attr("style", "display:none !important");
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
    if (((postFormat == 1 && ((networkId == 1 && (networkType == 1 || networkType == 2)) || (networkId == 2) || (networkId == 3 && (networkType == 0 || networkType == 1)))) || networkId == 12) && jQuery('.b2s-post-item-details-release-input-date-select[data-network-auth-id="' + networkAuthId + '"]').val() != 1) {
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

    if (networkId == 2) {
        if (postFormat == 0) {
            jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').show();
        } else {
            jQuery('.b2s-alert-twitter-card[data-network-auth-id="' + networkAuthId + '"]').hide();
        }
    }
    return false;
}

//Network: Tumblr post format
jQuery(document).on('change', '.b2s-post-item-details-post-format[data-network-id="4"]', function () {
    var type = jQuery(this).val();
    var networkAuthId = jQuery(this).data('network-auth-id');
    if (type == 2) {
        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').show();
    }
    if (type == 1) {
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').show();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').hide();
    }
    if (type == 0) {
        jQuery('.b2s-format-area-tumblr-link[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-format-area-tumblr-image[data-network-auth-id="' + networkAuthId + '"]').hide();
        jQuery('.b2s-post-item-details-item-message-area[data-network-auth-id="' + networkAuthId + '"]').show();
    }
});


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

