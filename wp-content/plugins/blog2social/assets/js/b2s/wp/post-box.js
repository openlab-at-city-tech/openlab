jQuery(document).on('heartbeat-send', function (e, data) {
    data['b2s_heartbeat'] = 'b2s_listener';
    data['b2s_heartbeat_action'] = 'b2s_auto_posting';
});

jQuery(window).on("load", function () {

    //Editor Gutenberg
    //ref https://developer.wordpress.org/block-editor/data/data-core-editor/
    if (wp && wp.data && wp.data.select && wp.data.subscribe && wp.data.select('core/editor') != null) {
        var originalModifiedDate = new Date(wp.data.select('core/editor').getCurrentPostAttribute("modified"));
        wp.data.subscribe(function () {
            var isAutosavingPost = wp.data.select('core/editor').isAutosavingPost();
            var isSavingPost = wp.data.select('core/editor').isSavingPost();
            if (!isAutosavingPost && isSavingPost) {
                var currentModifiedDate = new Date(wp.data.select('core/editor').getCurrentPostAttribute("modified"));
                if ((originalModifiedDate.getTime() < currentModifiedDate.getTime())) {
                    originalModifiedDate = currentModifiedDate;

                    //update infobox
                    jQuery.ajax({
                        url: ajaxurl,
                        type: "GET",
                        dataType: "json",
                        cache: false,
                        data: {
                            'action': 'b2s_update_post_box',
                            'post_id': jQuery('#post_ID').val(),
                            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                        },
                        error: function () {
                            return false;
                        },
                        success: function (data) {
                            if (data.result == true) {
                                if (typeof data.shareCount != 'undefined') {
                                    jQuery('.b2s-meta-box-share-count').html(data.shareCount);
                                }
                                if (typeof data.lastPostDate != 'undefined') {
                                    jQuery('.b2s-meta-box-last-post-date').html(data.lastPostDate);
                                }
                                if (typeof data.active != 'undefined') {
                                    if (data.active == true) {
                                        jQuery('.b2s-enable-auto-post').prop('checked', true).trigger('change');
                                    } else {
                                        jQuery('.b2s-enable-auto-post').prop('checked', false).trigger('change');
                                    }
                                }
                            }
                        }
                    });
                }
            }
        });
    }

    //Gutenberg V5.0.0
    jQuery('#b2s-post-meta-box-auto').removeClass('hide-if-js');
    //--
    jQuery('#b2s-post-box-calendar-header').addClass('closed');
    jQuery('#b2s-post-box-calendar-header').hide();
    if (typeof wp.heartbeat == "undefined") {
        jQuery('#b2s-heartbeat-fail').show();
        jQuery('.b2s-loading-area').hide();
    } else {
        if (!b2sIsValidUrl(jQuery('#b2s-home-url').val())) {
            jQuery('#b2s-url-valid-warning').show();
        } else {
            jQuery('#b2s-url-valid-warning').hide();
        }
    }
    //TOS Twitter 032018
    jQuery('#b2s-network-tos-warning').show();

    if (jQuery('#b2s-enable-auto-post').is(':checked')) {
        jQuery('#b2s-post-box-calendar-header').show();
        if (jQuery('#b2s-post-meta-box-version').val() == "0" && jQuery(this).val() == "publish") {
            jQuery('#b2s-enable-auto-post').prop('checked', false);
        }
    }

    //update Twitter Dropdown
    var mandantId = jQuery('#b2s-post-meta-box-profil-dropdown').val();
    var tos = false;
    if (jQuery('#b2s-post-meta-box-profil-data-' + mandantId).val() == "") {
        jQuery('#b2s-post-meta-box-state-no-auth').show();
        tos = true;
    } else {
        jQuery('#b2s-post-meta-box-state-no-auth').hide();
        //TOS Twitter Check
        var len = jQuery('#b2s-post-meta-box-profil-dropdown-twitter').children('option[data-mandant-id="' + mandantId + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-meta-box-auto-post-twitter-profile').show();
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter').prop('disabled', false);
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter').show();
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter option').attr("disabled", "disabled");
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter option[data-mandant-id="' + mandantId + '"]').attr("disabled", false);
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-meta-box-auto-post-twitter-profile').hide();
        jQuery('#b2s-post-meta-box-profil-dropdown-twitter').prop('disabled', 'disabled');
        jQuery('#b2s-post-meta-box-profil-dropdown-twitter').hide();
    }

});


jQuery(document).on('click', '.postbox-container', function () {
    var id = jQuery(this).children().find('#b2s-post-box-calendar-header').attr('id');
    if (id == 'b2s-post-box-calendar-header') {
        if (!jQuery('#' + id).hasClass('closed')) {
            if (jQuery('.b2s-post-box-calendar-content').is(':empty')) {
                jQuery('#b2s-post-box-calendar-btn').trigger('click');
            }
        }
    }
    return true;
});


//V7.1.0
jQuery(document).on('click', '#b2s-meta-video-box-btn-customize', function () {
    var url = jQuery(this).attr('data-url');
    if (url != "") {
        window.location.href = url;
    }
    return false;
});

//V5.0.0 compability gutenberg editor
jQuery(document).on('click', '#b2s-meta-box-btn-customize', function () {
    var postStatus = jQuery('#b2s-post-status').val();
    if (postStatus != 'publish' && postStatus != 'future') {
        jQuery.ajax({
            url: ajaxurl,
            type: "GET",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_get_blog_post_status',
                'post_id': jQuery('#post_ID').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('#b2s-post-meta-box-state-no-publish-future-customize').show();
                return false;
            },
            success: function (data) {
                if (data != 'publish' && data != 'future') {
                    jQuery('#b2s-post-meta-box-state-no-publish-future-customize').show();
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                    jQuery('#b2s-post-meta-box-state-no-publish-future-customize').hide();
                    window.location.href = jQuery('#b2s-redirect-url-customize').val() + jQuery('#post_ID').val();
                }
            }
        });
    } else {
        jQuery('#b2s-post-meta-box-state-no-publish-future-customize').hide();
        window.location.href = jQuery('#b2s-redirect-url-customize').val() + jQuery('#post_ID').val();
    }
});



jQuery(document).on('click', '#b2s-post-box-calendar-btn', function () {
    jQuery('#b2s-post-box-calendar-header').show();
    jQuery('#b2s-post-box-calendar-header').removeClass('closed');

    if (jQuery('.b2s-post-box-calendar-content').is(':empty')) {
        //Load First
        jQuery('.b2s-post-box-calendar-content').fullCalendar({
            editable: false,
            locale: jQuery('#b2sUserLang').val(),
            eventLimit: 2,
            contentHeight: 530,
            timeFormat: 'H:mm',
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
                $em.append("<img src='" + jQuery('#b2sPluginUrl').val() + "assets/images/portale/" + event.network_id + "_flat.png' style='height: 16px;width: 16px;display: inline-block;padding-right: 2px;padding-left: 2px;' />")
                $em.append(event.network_name);
                $em.append(jQuery("<span>").text(": " + event.profile));
                $body.append($em);
                element.append($body);
            },
        });
    }

    var target = jQuery(this.hash);
    target = target.length ? target : jQuery('[name=' + this.hash.substr(1) + ']');
    if (target.length) {
        jQuery('html,body').animate({
            scrollTop: target.offset().top - 100
        }, 1000);
    }

    return false;


});




jQuery(document).on('click', '#b2s-enable-auto-post', function () {
    jQuery('#b2s-post-box-calendar-header').show();
    if (jQuery('#b2s-post-meta-box-version').val() == "0" && jQuery(this).val() == "publish") {
        jQuery('#b2s-enable-auto-post').prop('checked', false);
        jQuery('#b2s-post-meta-box-note-trial').show();
    } else {
        jQuery('#b2s-post-meta-box-note-trial').hide();
    }
});

jQuery(document).on('change', '.b2s-post-meta-box-sched-select', function () {
    if (jQuery(this).val() >= '1' && jQuery('#b2s-post-meta-box-version').val() <= 1) {
        jQuery(this).val('0');
        jQuery('#b2s-post-meta-box-note-premium').show();
    }
});

//Classic Editor WP < 5.0.0
jQuery(document).on('click', '#publish', function () {
    //Check is Auto-Post-Import active
    if (jQuery('#b2sAutoPostImportIsActive').length > 0) {
        if (jQuery('#b2sAutoPostImportIsActive').val() == "1") {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_lock_auto_post_import',
                    'userId': jQuery('#b2sBlogUserId').val(),
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            });
        }
    }
});

//Gutenberg WP > 5.0.1
jQuery(document).on('click', '.editor-post-publish-button', function () {
    //Check is Auto-Post-Import active
    if (jQuery('#b2sAutoPostImportIsActive').length > 0) {
        if (jQuery('#b2sAutoPostImportIsActive').val() == "1") {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_lock_auto_post_import',
                    'userId': jQuery('#b2sBlogUserId').val(),
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                }
            });
        }
    }
});

jQuery(document).on('click', '.b2s-btn-close-meta-box', function () {
    jQuery('#' + jQuery(this).attr('data-area-id')).hide();
    return false;
});

jQuery(document).on('click', '.b2s-info-btn', function () {
    jQuery('html, body').animate({scrollTop: jQuery("body").offset().top}, 1);
    jQuery('#' + jQuery(this).attr('data-modal-target')).show();
});
jQuery(document).on('click', '.b2s-meta-box-modal-btn-close', function () {
    jQuery('#' + jQuery(this).attr('data-modal-target')).hide();
});

jQuery(document).on('change', '#b2s-post-meta-box-profil-dropdown', function () {
    var tos = false;
    if (jQuery('#b2s-post-meta-box-profil-data-' + jQuery(this).val()).val() == "") {
        jQuery('#b2s-post-meta-box-state-no-auth').show();
        tos = true;
    } else {
        jQuery('#b2s-post-meta-box-state-no-auth').hide();
        //TOS Twitter Check
        var len = jQuery('#b2s-post-meta-box-profil-dropdown-twitter').children('option[data-mandant-id="' + jQuery(this).val() + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-meta-box-auto-post-twitter-profile').show();
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter').prop('disabled', false);
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter').show();
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter option').attr("disabled", "disabled");
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]').attr("disabled", false);
            jQuery('#b2s-post-meta-box-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]:first').attr("selected", "selected");
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-meta-box-auto-post-twitter-profile').hide();
        jQuery('#b2s-post-meta-box-profil-dropdown-twitter').prop('disabled', 'disabled');
        jQuery('#b2s-post-meta-box-profil-dropdown-twitter').hide();
    }


});

function b2sIsValidUrl(str) {
    var pattern = new RegExp(/^(https?:\/\/)+[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+(?:\.[a-zA-Z0-9\wÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß-]+)+[\w\-\._~:/?#[\]@!\$&'\(\)\*\+,;=%.ÄÖÜÑÁÉÍÓÚÂÃÀÇÊÔÕÆÈËÎÏŒÙÛŸØÅöäüñáéíóúâãàçêôõæèëîïœùûÿøåß]+$/);
    if (!pattern.test(str)) {
        return false;
    }
    return true;
}

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

jQuery(document).on('click', '.b2s-options-btn', function () {
    if (jQuery('.b2s-options').is(':visible')) {
        jQuery('.b2s-options').hide();
        jQuery('.b2s-options-btn > .glyphicon').removeClass('glyphicon-chevron-up').addClass('glyphicon-chevron-down');
    } else {
        jQuery('.b2s-options').show();
        jQuery('.b2s-options-btn > .glyphicon').removeClass('glyphicon-chevron-down').addClass('glyphicon-chevron-up');
    }
});

jQuery(document).on('change', '.b2s-enable-auto-post', function () {
    if (jQuery(this).is(':checked')) {
        jQuery('.b2s-post-meta-box-active').show();
        jQuery('.b2s-post-meta-box-inactive').hide();
    } else {
        jQuery('.b2s-post-meta-box-active').hide();
        jQuery('.b2s-post-meta-box-inactive').show();
    }
});
