jQuery.noConflict();

var curSource = new Array();
curSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=all&filter_status=0&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
var newSource = new Array();

jQuery(document).ready(function () {
    jQuery('.b2s-widget-calendar').fullCalendar({
        editable: false,
        locale: b2s_calendar_locale,
        eventLimit: 2,
        contentHeight: 475,
        timeFormat: 'H:mm',
        customButtons: {
            showall: {
                text: jQuery('#showFullCalenderText').val(),
                click: function () {
                    window.open('admin.php?page=blog2social-calendar', "_self");
                }
            }
        },
        header: {
            left: 'title',
            center: '',
            right: 'showall today prev,next'
        },
        eventSources: curSource[0],
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
        eventClick: function (calEvent, jsEvent, view) {
            window.location.href = window.location.pathname + "?page=blog2social-calendar&rfd=true&b2s_id=" + calEvent.b2s_id;
        }
    });

    drawBasic();

    jQuery('#b2s-activity-date-picker').b2sdatepicker({
        'autoClose': true,
        'toggleSelected': true,
        'minutesStep': 15
    });
    jQuery('#b2s-activity-date-picker').on("selectDate", function () {
        setTimeout(drawBasic);
    });
    getWidgetContent();
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

/*Post-Widget Position**/
jQuery(document).on('click', '.b2s-post-btn', function () {
    var target = jQuery(".b2s-post");
    if (target.length) {
        jQuery('html,body').animate({
            scrollTop: target.offset().top - 50
        }, 1000);
    }
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

/* Aktivity-Chart*/
function drawBasic() {
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
            'action': 'b2s_get_stats',
            'from': jQuery('#b2s-activity-date-picker').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (content) {
            if (content.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else {
                var published = [];
                var scheduled = [];
                function dateToYMD(date) {
                    var d = date.getUTCDate();
                    var m = date.getUTCMonth() + 1;
                    var y = date.getUTCFullYear();
                    return '' + y + '-' + (m <= 9 ? '0' + m : m) + '-' + (d <= 9 ? '0' + d : d);
                }

                function dateToDMY(date) {
                    var d = date.getUTCDate();
                    var m = date.getUTCMonth() + 1;
                    var y = date.getUTCFullYear();
                    return '' + (d <= 9 ? '0' + d : d) + '.' + (m <= 9 ? '0' + m : m) + '.' + y;
                }

                jQuery(Object.keys(content)).each(function () {
                    if (published.length > 0) {
                        var diff = parseInt((new Date(published[published.length - 1].x).getTime() - new Date(this).getTime()) / (24 * 3600 * 1000));
                        while (diff < -1) {
                            var date = new Date(published[published.length - 1].x.toString());
                            var newDate = new Date(date.setTime(date.getTime() + 86400000));
                            published.push({x: dateToYMD(newDate), y: 0});
                            scheduled.push({x: dateToYMD(newDate), y: 0});
                            diff = parseInt((new Date(published[published.length - 1].x).getTime() - new Date(this).getTime()) / (24 * 3600 * 1000));
                        }
                    }

                    published.push({x: this.toString(), y: content[this][0]});
                    scheduled.push({x: this.toString(), y: content[this][1]});
                });
                var options = {
                    series: [],
                    chart: {
                        redrawOnParentResize: true,
                        type: 'bar',
                        height: 450,
                        animations: {
                            enabled: true,
                            easing: 'easeout',
                            speed: 500,
                            dynamicAnimation: {
                                enabled: true,
                                speed: 300
                            }
                        },
                        toolbar: {
                            show: true,
                            tools: {
                                download: true,
                                zoom: true,
                                zoomin: true,
                                zoomout: true,
                                pan: false,
                                reset: true
                            }
                        }
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '90%'
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    colors: ['#79b232CC', '#c0c0c0CC'],
                    
                    legend: {
                        position: 'top',
                        horizontalAlign: 'left'
                    },
                    
                    xaxis: {
                        type: 'datetime',
                        labels: {
                            tickAmount: 2,
                            range: 5,
                            datetimeFormatter: {
                                year: 'yyyy',
                                month: 'MMM yy',
                                day: 'dd MMM',
                                hour: 'dd MMM'
                            }
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        forceNiceScale: false,
                        labels: {
                            formatter: function (val) {
                                return val.toFixed(0);
                            }
                        }
                    },
                    noData: {
                        text: 'keine Daten vorhanden...'
                    }
                };
                var chart = new ApexCharts(document.querySelector("#chart_div"),options);
                chart.render();
                
                chart.updateSeries([{
                    name: 'Ver�ffentlicht',
                    data: published
                },{
                    name: 'Geplant',
                    data: scheduled
                }]);
            }
        }
    });
    document.getElementById('chart_div').style.marginTop = '15px';
}


function isMail(mail) {
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(mail);
}

jQuery(document).on('change', '.b2s-calendar-filter-network-btn', function () {
    var filter_status = jQuery('#b2s-calendar-filter-status').val();
    newSource[0] = ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=' + jQuery(this).val() + '&filter_status=' + filter_status + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val();
    jQuery('.b2s-widget-calendar').fullCalendar('removeEventSources');
    jQuery('.b2s-widget-calendar').fullCalendar('addEventSource', newSource[0]);
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
    jQuery('.b2s-widget-calendar').fullCalendar('removeEventSources');
    jQuery('.b2s-widget-calendar').fullCalendar('addEventSource', newSource[0]);
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
    jQuery('.b2s-widget-calendar').fullCalendar('removeEventSources');
    jQuery('.b2s-widget-calendar').fullCalendar('addEventSource', newSource[0]);
    curSource[0] = newSource[0];

    return false;

});