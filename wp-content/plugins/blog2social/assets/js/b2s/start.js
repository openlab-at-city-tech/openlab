jQuery.noConflict();

jQuery(document).ready(function () {
    getWidgetContent();
    getActivityPublishContent();
    getActivitySchedContent();
    getCalendarEvent();
});

jQuery(document).on('click', '.b2s-dashboard-trial-expired-btn', function () {
    if (jQuery('#b2s-trial-seven-day-modal').length > 0) {
        jQuery('#b2s-trial-seven-day-modal').modal('show');
    }
    if (jQuery('#b2s-final-trail-modal').length > 0) {
        jQuery('#b2s-final-trail-modal').modal('show');
    }
    return false;
});

jQuery(document).on('click', '.b2s-dashboard-premium-enterprise-version-btn', function () {
    if (jQuery('#b2s-dashboard-premium-enterprise-version-modal').length > 0) {
        jQuery('#b2s-dashboard-premium-enterprise-version-modal').modal('show');
    }
    return false;
});



jQuery(document).on('click', '.b2s-dashboard-addon-add-user-btn', function () {
    if (jQuery('#b2s-dashboard-premium-addon-add-user-modal').length > 0) {
        jQuery('#b2s-dashboard-premium-addon-add-user-modal').modal('show');
    }
    return false;
});

jQuery(document).on('click', '.b2s-dashboard-addon-add-social-account-btn', function () {
    if (jQuery('#b2s-dashboard-premium-addon-add-social-account-modal').length > 0) {
        jQuery('#b2s-dashboard-premium-addon-add-social-account-modal').modal('show');
    }
    return false;
});


/* EMail-Widget */
jQuery(document).on('click', '.b2s-mail-btn', function () {
    if (isMail(jQuery('#b2s-mail-update-input').val())) {
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_post_mail_update',
                'email': jQuery('#b2s-mail-update-input').val(),
                'lang': jQuery('#user_lang').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            }
        });
        jQuery('.b2s-mail-update-area').hide();
        jQuery('.b2s-mail-update-success').show();
    } else {
        jQuery('#b2s-mail-update-input').addClass('error');
    }
    return false;
});

/* Content-Widget */
function getWidgetContent() {
    if (jQuery('.b2s-dashboard-multi-widget').length > 0)
    {
        var data = [];
        var widget = jQuery('.b2s-dashboard-multi-widget');
        var legacyMode = jQuery('#isLegacyMode').val();
        if (legacyMode == "1") {
            legacyMode = false; // loading is sync (stack)
        } else {
            legacyMode = true; // loading is async (parallel)
        }
        jQuery.ajax({
            url: ajaxurl,
            type: "GET",
            dataType: "json",
            async: legacyMode,
            cache: false,
            data: {
                'action': 'b2s_get_multi_widget_content',
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            success: function (content) {
                data = content;
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    widget.data('position', 0); //random: new Date().getSeconds() % data.length;
                    show();
                    setInterval(function () {
                        jQuery('.b2s-dashboard-multi-widget .glyphicon-chevron-left').trigger("click");
                    }, 30000);
                }
            }
        });
        jQuery('.b2s-dashboard-multi-widget .glyphicon-chevron-right').on("click", function () {
            widget.data('position', widget.data('position') * 1 + 1);
            show(widget);
        });
        jQuery('.b2s-dashboard-multi-widget .glyphicon-chevron-left').on("click", function () {
            widget.data('position', widget.data('position') * 1 - 1);
            show(widget);
        });
        function show()
        {
            if (widget.data('position') < 0)
            {
                widget.data('position', data.length - 1);
            } else if (widget.data('position') > data.length - 1)
            {
                widget.data('position', 0);
            }
            var id = widget.data('position');
            widget.find('.b2s-dashboard-multi-widget-content').html(data[id]['content']);
            widget.find('.b2s-dashboard-h5').text(data[id]['title']);
        }
    }
}

/*Calendar Widget */
function getCalendarEvent() {

    const startDate = new Date();
    let highlightedDays = {};

    jQuery.ajax({
        url: ajaxurl,
        type: "GET",
        dataType: "json",
        async: (jQuery('#isLegacyMode').val() == "1") ? false : true,
        cache: false,
        data: {
            'action': 'b2s_get_calendar_widget_content',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            jQuery('.b2s-cal-sched-dashboard-loader').hide();
            if (data.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            }
            highlightedDays = data.result;
            jQuery('#b2s-cal-sched-dashboard').b2sdatepicker({
                'language': jQuery('#b2sLang').val(),
                'minDate': new Date(),
                'inline': true,
                /*onSelect : function(date,formattedDate, datepicker){
                 },*/
                onRenderCell: function (date, cellType) {
                    if (cellType === "day") {

                        date.setTime(date.getTime() - (date.getTimezoneOffset() * 60000));
                        const dateString = date.toISOString().slice(0, 10);
                        const colorArray = highlightedDays[dateString];

                        if (colorArray && colorArray.length === 1) {
                            const circles = colorArray
                                    .map(
                                            (color) =>
                                            `<div class="b2sdatepicker--color-circle" style="background-color: ${color};"></div>`
                                    )
                                    .join("");
                            return {
                                classes: "b2sdatepicker--highlight",
                                html: `<div class="b2sdatepicker--day-number">${date.getDate()}</div>${circles}`,
                            };
                        } else if (colorArray && colorArray.length > 1) {
                            const circles = colorArray
                                    .map(
                                            (color) =>
                                            `<div class="b2sdatepicker--color-circle" style="background-color: ${color};"></div>`
                                    )
                                    .join("");
                            return {
                                classes: "b2sdatepicker--highlight",
                                html: `<div class="b2sdatepicker--day-number">${date.getDate()}</div><div class="b2sdatepicker--color-container">${circles}</div>`,
                            };
                        }
                        return null;
                    }
                }
            });
        }
    });
}

jQuery(document).on('click', '#b2s-dashboard-activity-publish-btn', function () {
    getActivityPublishContent();
});
jQuery(document).on('click', '#b2s-dashboard-activity-sched', function () {
    getActivitySchedContent();
});

function getActivityPublishContent() {
    // var data = [];
    var publishElm = jQuery('.b2s-dashboard-activity-publish');
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
            'action': 'b2s_get_dashboard_activity',
            'b2sType': 'publish',
            'b2sUserLang': jQuery('#user_lang').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (typeof data.content != undefined) {
                if (data.content != '') {
                    publishElm.html(data.content);
                    publishElm.show();
                } else {
                    jQuery('.b2s-dashboard-activity-publish-case-1').show();
                }
                jQuery('.b2s-dashboard-activity-publish-loader').hide();
            } else {
                if (typeof data.error != undefined && data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
}
function getActivitySchedContent() {
    var data = [];
    var schedElm = jQuery('.b2s-dashboard-activity-sched');
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
            'action': 'b2s_get_dashboard_activity',
            'b2sType': 'sched',
            'b2sUserLang': jQuery('#user_lang').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (typeof data.content != undefined) {
                if (data.content != '') {
                    jQuery('.b2s-dashboard-activity-sched-case-4').show();
                    schedElm.html(data.content);
                } else {
                    if (data.user.user_version > 0) {
                        jQuery('.b2s-dashboard-activity-sched-case-1').show();
                    } else {
                        if (data.user.allow_trial == true) {
                            jQuery('.b2s-dashboard-activity-sched-case-2').show();
                        } else {
                            jQuery('.b2s-dashboard-activity-sched-case-3').show();
                        }
                    }
                }
                jQuery('.b2s-dashboard-activity-sched-loader').hide();
            } else {
                if (typeof data.error != undefined && data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
}
function isMail(mail) {
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(mail);
}