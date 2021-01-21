jQuery.noConflict();

/* Calendar-Widget */
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
        eventSources: ajaxurl + '?action=b2s_get_calendar_events&filter_network_auth=all&filter_network=all' + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
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
    jQuery('#chart_div').html("<div class=\"b2s-loading-area\">\n" +
            "        <br>\n" +
            "        <div class=\"b2s-loader-impulse b2s-loader-impulse-md\"></div>\n" +
            "        <div class=\"clearfix\"></div>\n" +
            "    </div>");
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
                jQuery('#chart_div').html("<canvas id=\"b2s_activity_chart\" style=\"max-width:690px !important; max-height:320px !important;\"></canvas>");
                var ctx = document.getElementById("b2s_activity_chart").getContext('2d');
                var published = [];
                var published_colors = [];
                var scheduled = [];
                var scheduled_colors = [];
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
                            published_colors.push('rgba(121,178,50,0.8)');
                            scheduled_colors.push('rgba(192,192,192,0.8)');
                            scheduled.push({x: dateToYMD(newDate), y: 0});
                            diff = parseInt((new Date(published[published.length - 1].x).getTime() - new Date(this).getTime()) / (24 * 3600 * 1000));
                        }
                    }

                    published.push({x: this.toString(), y: content[this][0]});
                    published_colors.push('rgba(121,178,50,0.8)');
                    scheduled_colors.push('rgba(192,192,192,0.8)');
                    scheduled.push({x: this.toString(), y: content[this][1]});
                });
                var unit = "day";
                if (published.length > 100)
                {
                    unit = "month";
                }

                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        datasets: [{
                                label: jQuery("#chart_div").data('text-published'),
                                data: published,
                                backgroundColor: published_colors
                            }, {
                                label: jQuery("#chart_div").data('text-scheduled'),
                                data: scheduled,
                                backgroundColor: scheduled_colors
                            }]
                    },
                    options: {
                        tooltips: {
                            callbacks: {
                                title: function (tooltipItem) {
                                    if (jQuery("#chart_div").data('language') == "de") {
                                        var date = new Date(tooltipItem[0].xLabel);
                                        return dateToDMY(date);
                                    } else {
                                        return tooltipItem[0].xLabel
                                    }
                                }
                            }
                        },
                        scales: {
                            xAxes: [{
                                    type: "time",
                                    time: {
                                        unit: unit
                                    }
                                }
                            ],
                            yAxes: [{
                                    ticks: {
                                        beginAtZero: true
                                    }
                                }]
                        }
                    }
                });
            }
        }
    });
}


function isMail(mail) {
    var regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return regex.test(mail);
}

