jQuery(window).on("load", function () {
    if (typeof wp.heartbeat == "undefined") {
        jQuery('.b2s-heartbeat-fail').show();
    } else {
        jQuery('.b2s-heartbeat-fail').hide();
    }
    var b2sPolicy = jQuery('#b2sUserAcceptPrivacyPolicy').val();
    if (typeof b2sPolicy !== typeof undefined && b2sPolicy !== false) {
        if (b2sPolicy === 'true') {
            jQuery('#b2sModalPrivacyPolicy').modal('show');
        }
    }

    /* Temporarily closed since 8.4
     * if (jQuery('#b2s-metrics-banner-show').val() == '0' && jQuery('.b2s-metrics-starting-modal').length == 0) {
     jQuery('#b2s-metrics-banner-modal').modal('show');
     }*/

    if (jQuery('#b2s-trial-seven-day-modal').length > 0) {
        jQuery('#b2s-trial-seven-day-modal').modal('show');
    }
    if (jQuery('#b2s-final-trail-modal').length > 0) {
        jQuery('#b2s-final-trail-modal').modal('show');
    }
    if (jQuery('.b2s-changelog-body').length > 0) {
        jQuery('#b2s-changelog-modal').modal('show');
    }
});

jQuery(document).on('click', '.b2s-show-feedback-modal', function () {
    jQuery('#b2sTrailFeedbackModal').modal('show');
});

jQuery(document).on('click', '.b2s-send-trail-feedback', function () {
    jQuery('.b2s-network-auth-info').hide();
    if (jQuery('#b2s-trial_message').val() == "") {
        jQuery('.b2s-feedback-success').fail();
        return false;
    }
    jQuery('#b2sTrailFeedbackModal').modal('hide');
    jQuery('.b2s-server-connection-fail').hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_send_trail_feedback',
            'feedback': jQuery('#b2s-trial_message').val(),
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-feedback-success').show();
            }
        }
    });
});


jQuery(document).on('click', '.b2s-modal-privacy-policy-accept-btn', function () {
    jQuery('#b2sModalPrivacyPolicy').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_accept_privacy_policy',
            'accept': true,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
        }
    });
    return false;
});

jQuery(document).on('click', '.b2s-key-area-btn-submit', function () {
    jQuery('.b2s-key-area-success').hide();
    jQuery('.b2s-key-area-fail').hide();
    jQuery('.b2s-key-area-fail-max-use').hide();
    jQuery('.b2s-key-area-fail-no-token').hide();

    if (jQuery('.b2s-key-area-input').val() == "") {
        jQuery('.b2s-key-area-input').addClass('error');
    } else {
        jQuery('.b2s-key-area-btn-submit').prop('disabled', true);
        jQuery('.b2s-key-area-input').removeClass('error');
        jQuery('.b2s-server-connection-fail').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_update_user_version',
                'key': jQuery('.b2s-key-area-input').val(),
                'user_id': jQuery('#b2s-license-user').val(),
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {
                jQuery('#b2sInfoKeyModal').modal('show');
                jQuery('.b2s-key-area-btn-submit').prop('disabled', false);
                jQuery('.b2s-trail-premium-info-area').hide();
                if (data.result == true) {
                    jQuery('.b2s-key-area-success').show();
                    if (data.licenseName != false) {
                        jQuery('.b2s-key-area-key-name').html(data.licenseName);
                        jQuery('.b2s-key-name').html(data.licenseName);
                    }
                    jQuery('#b2s-license-user-select').empty();
                    jQuery('#b2s-license-user-select').append(jQuery('<option value="0"></option>'));
                    jQuery('#b2s-license-user-select').trigger("chosen:updated");
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                    if (data.reason != null && data.reason == 1) {
                        jQuery('.b2s-key-area-fail-max-use').show();
                    } else if (data.reason != null && data.reason == 2) {
                        jQuery('.b2s-key-area-fail-no-token').show();
                    } else {
                        jQuery('.b2s-key-area-fail').show();
                    }

                }
            }
        });
        return false;
    }
});

//ADDON
jQuery(document).on('click', '.b2sAddonFeatureModalBtn', function () {
    jQuery('#b2sAddonFeatureModal').modal('show');
    jQuery('#b2sAddonFeatureModal').find('.modal-title').html(jQuery(this).attr('data-title'));
    return false;
});

jQuery(document).on('click', '.b2sPreFeatureEditAndDeleteModal', function () {
       jQuery("#b2sPreFeatureEditAndDeleteModal").modal('show');
});

jQuery(document).on('click', '.b2sPreFeatureAutoPosterModal', function () {
       jQuery("#b2sPreFeatureAutoPosterModal").modal('show');
});

jQuery(document).on('click', '.b2sPreFeatureReshareModal', function () {
       jQuery("#b2sPreFeatureReshareModal").modal('show');
});

jQuery(document).on('click', '.b2sProFeatureMultiImageModal', function () {
    jQuery("#b2sProFeatureMultiImageModal").modal('show');
});

jQuery(document).on('click', '.b2sPreFeaturePostFormatModal', function () {

    jQuery("#b2sPreFeaturePostFormatModal").modal('show');
});

jQuery(document).on('click', '.b2sProFeatureAddCommentModal', function (e) {
   
    jQuery('#b2s-edit-template').modal('hide');//avoid double modal 
    jQuery("#b2sProFeatureAddCommentModal").modal('show');
    
});

jQuery(document).on('click', '.b2sPreFeatureNetworksModal', function () {

    //check for network specific Network Modals
    var networkId = jQuery(this).closest('.list-group-item').attr('data-network-id');//network View
    if(networkId == null){
        networkId=  jQuery(this).closest('li').attr('data-network-id'); //Portal View in Ship
        
    }
    if(networkId != null && networkId != undefined && networkId != 0 && (jQuery(this).hasClass('b2s-network-auth-btn') || jQuery(this).hasClass('b2s-network-list-add-btn'))){
       var modalHasNetworkContent= setNetworkAdModal(networkId);

        if(modalHasNetworkContent){
            jQuery('.modal-advertising-network-modal-network').modal('show');
            return false;
        }
    }
    //default Modal
    jQuery("#b2sPreFeatureNetworksModal").modal('show');
    return false;
});

jQuery(document).on('click', '.b2sProFeatureNetworksModal', function () {

    //check for network specific Network Modals
    var networkId = jQuery(this).closest('.list-group-item').attr('data-network-id');//network View
    if(networkId == null){
        networkId=  jQuery(this).closest('li').attr('data-network-id'); //Portal View in Ship
    }

    if(networkId != null && networkId != undefined && networkId != 0 && (jQuery(this).hasClass('b2s-network-auth-btn') || jQuery(this).hasClass('b2s-network-list-add-btn'))){
        
        var modalHasNetworkContent= setNetworkAdModal(networkId);

        if(modalHasNetworkContent){
            jQuery('.modal-advertising-network-modal-network').modal('show');
            return false;
        }
    }
    //default Modal
    jQuery("#b2sProFeatureNetworksModal").modal('show');
    return false;

});

jQuery(document).on('click', '.b2sBusinessFeatureNetworksModal', function () {

    //check for network specific Network Modals
    var networkId = jQuery(this).closest('.list-group-item').attr('data-network-id');//network View
    if(networkId == null){
        networkId=  jQuery(this).closest('li').attr('data-network-id'); //Portal View in Ship
    }

    if(networkId != null && networkId != undefined && networkId != 0 && (jQuery(this).hasClass('b2s-network-auth-btn') || jQuery(this).hasClass('b2s-network-list-add-btn'))){
        
        var modalHasNetworkContent= setNetworkAdModal(networkId);

        if(modalHasNetworkContent){
           
            jQuery('.modal-advertising-network-modal-network').modal('show'); 
            return false;

        }
    }
    
    //default Modal
    jQuery("#b2sBusinessFeatureNetworksModal").modal('show');
    return false;

});

function setNetworkAdModal(networkId){
    
    jQuery('.modal-advertising-network-title').hide();
    jQuery('.modal-advertising-network-subline').hide();
    jQuery('.modal-advertising-network-list').hide();
    jQuery('.modal-advertising-network-bottomtext').hide();
    jQuery('.modal-advertising-network-upgrade-btn').hide();
    jQuery('.modal-advertising-network-subline[data-network-id="' + networkId + '"]').show();
    jQuery('.modal-advertising-network-title[data-network-id="' + networkId + '"]').show();
    jQuery('.modal-advertising-network-list[data-network-id="' + networkId + '"]').show();
    jQuery('.modal-advertising-network-bottomtext[data-network-id="' + networkId + '"]').show();
    jQuery('.modal-advertising-network-upgrade-btn[data-network-id="' + networkId + '"]').show();
    
    if(jQuery('.modal-advertising-network-title[data-network-id="' + networkId + '"]').length > 0){
        return true;
    } 
    return false;
}

jQuery(document).on('click', '.b2sPreFeatureBestTimesModal', function () {
    jQuery("#b2sPreFeatureBestTimesModal").modal('show');
    return false;
});

jQuery(document).on('click', '.b2sProFeatureNetworkGroupsModal', function () {
    jQuery("#b2sProFeatureNetworkGroupsModal").modal('show');
    return false;
})


//PREMIUM-PRO
jQuery(document).on('click', '.b2sProFeatureModalBtn', function () {

    //check for network specific 
    var networkId = jQuery(this).closest('.list-group-item').attr('data-network-id');

    if(networkId != null && networkId != undefined && networkId != 0 && (jQuery(this).hasClass('b2s-network-auth-btn') || jQuery(this).hasClass('b2s-network-list-add-btn'))){
      
        if(jQuery('.modal-advertising-network-modal-network-'+networkId).length > 0){
            jQuery('.modal-advertising-network-modal-network-'+networkId).modal('show');
            return false;
        }
    }

    jQuery('#b2sProFeatureModal').modal('show');
    jQuery('#b2sProFeatureModal').find('.modal-title').html(jQuery(this).attr('data-title'));
    jQuery('#b2sProFeatureModal').find('.modal-body').hide();
    jQuery('#b2sProFeatureModal').find('.' + jQuery(this).attr('data-type')).show();
    return false;
});


jQuery(document).on('heartbeat-send', function (e, data) {
    data['client'] = 'b2s';
});

jQuery(document).on('click', '.b2s-modal-close', function () {
    if (jQuery(this).attr('data-modal-name') == ".b2s-metrics-feedback-modal") {
        if (jQuery("#b2s-metrics-dont-show-again").is(":checked")) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_metrics_feedback_close',
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                error: function () {
                    jQuery('.b2s-server-connection-fail').show();
                    return false;
                },
                success: function (data) {
                    if (data.result == false) {
                        if (data.error == 'nonce') {
                            jQuery('.b2s-nonce-check-fail').show();
                        } else {
                            jQuery('.b2s-server-connection-fail').show();
                        }
                    }
                    return true;
                }
            });
        }
    }
    jQuery(jQuery(this).attr('data-modal-name')).modal('hide');
    jQuery(jQuery(this).attr('data-modal-name')).hide();
    jQuery('body').removeClass('modal-open');
    jQuery('body').removeAttr('style');
    return false;
});

//Tool:Assistini / Auth
jQuery(document).on('click', '.b2s-ass-register-btn, .b2s-post-item-ass-auth-btn', function (e) {
    if (jQuery(this).hasClass('btn-success-assistini-connected')) {
        return false;
    }
    e.preventDefault();
    jQuery('.b2sAssAuthModal').modal('show');
    return false;
});

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

function updateAssConnectButtons(isConnected) {
 
    jQuery('.b2s-ass-register-btn').each(function () {
        var button = jQuery(this);
        var defaultLabel = button.attr('data-default-label');
        var connectedLabel = button.attr('data-connected-label');

        if (isConnected) {
            if (connectedLabel) {
                button.text(connectedLabel);
            }
            button.addClass('btn btn-success-assistini-connected is-connected b2s-btn-disabled b2s-ass-connected');
            button.attr('aria-disabled', 'true');
        } else {
            if (defaultLabel) {
                button.text(defaultLabel);
            }
            button.removeClass('btn btn-success-assistini-connected is-connected b2s-btn-disabled b2s-ass-connected');
            button.removeAttr('aria-disabled');
        }
    });

    if (isConnected) {
        jQuery('.b2s-ass-logout-btn').show();
    } else {
        jQuery('.b2s-ass-logout-btn').hide();
    }

    if (isConnected) {
        jQuery('.b2s-ai-template-connect-gate').hide();
        jQuery('.b2s-ai-template-config').show();
        jQuery('.b2s-ai-ass-connected-indicator').show();
        jQuery('#b2s-global-ai-settings-not-connected-overlay').remove();
        jQuery('.b2s-ai-template-not-connected-overlay').remove();
    } else {
        jQuery('.b2s-ai-template-connect-gate').show();
        jQuery('.b2s-ai-template-config').hide();
        jQuery('.b2s-ai-ass-connected-indicator').hide();
    }

    if (typeof initAiTemplateSettings === 'function') {
        initAiTemplateSettings(jQuery('.b2s-edit-template-content'));
    }
}

function resetAssAuthModal() {

    var stepButtons = jQuery('.b2s-stepwizard-btn-circle');
    var firstStep = stepButtons.first();
    var otherSteps = stepButtons.not(firstStep);

    firstStep.addClass('b2s-ass-color').removeClass('btn-default').addClass('btn-danger');
    otherSteps.removeClass('b2s-ass-color').removeClass('btn-danger').addClass('btn-default');

    jQuery('.b2s-ass-auth-step-3-content').hide();
    jQuery('.b2s-ass-auth-step-1-content').show();
}

window.addEventListener('message', function (e) {
    var serverUrlElm = jQuery('#b2sServerUrl');
    if (!serverUrlElm.length || e.origin != serverUrlElm.val()) {
        return;
    }
    var data = JSON.parse(e.data);
    if (typeof data.action !== typeof undefined && data.action == 'assAuth') {
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
                    jQuery('.b2s-stepwizard-btn-circle').addClass('b2s-ass-color').removeClass('btn-default').addClass('btn-danger');
                    jQuery('.b2s-ass-auth-step-1-content').hide();
                    jQuery('.b2s-ass-auth-step-3-content').show();

                    updateAssConnectButtons(true);

                    jQuery('.b2s-post-item-ass-auth-btn').hide();
                    jQuery('.b2s-post-item-ass-create-btn').show();
                    jQuery('.b2s-post-item-ass-reset-btn').show();
                    jQuery('.b2s-post-item-ass-setting-btn').show();
                    jQuery('#b2s-ship-ass-connected').val(1);

                    if (jQuery('#sidebar_ship_ass_words_open').length) {
                        jQuery('#sidebar_ship_ass_words_open').text(data.ass_words_open);
                        jQuery('#sidebar_ship_ass_words_total').text(data.ass_words_total);
                        jQuery('#b2s-ship-ass-words-open').val(data.ass_words_open);
                        jQuery('#b2s-ship-ass-words-total').val(data.ass_words_total);
                        jQuery('.b2s-ass-sidebar-account').show();
                    }
                }
            }
        });
    }
});

jQuery(document).on('click', '.b2s-ass-logout-btn', function () {
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
            var response = JSON.parse(data);
            if (response.error == 'nonce') {
                jQuery('.b2s-nonce-check-fail').show();
            } else if (response.result == true) {
                updateAssConnectButtons(false);
                resetAssAuthModal();
            }
        }
    });
    return false;
});


jQuery(document).on('click', '.b2s-load-info-twitter-thread-modal', function () {
    jQuery('#b2sInfoTwitterThreads').modal('show');
    return false;
});


jQuery(document).on('click', '.b2sInfoMetaTagModal', function () {
    
    jQuery('#b2sInfoMetaTagModal').css('z-index', 2000);
    jQuery('#b2sInfoMetaTagModal').modal('show');
    
    return false;
});

function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}

function hideRating(forever)
{
    var data = {
        'action': 'b2s_hide_rating',
        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
    };

    if (forever) {
        data.forever = true;
    }

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: data
    });
}

jQuery(document).on("click", ".b2s-hide-rating", function (e) {
    e.preventDefault();
    hideRating(false);
    jQuery(this).closest('.panel').remove();
});

jQuery(document).on("click", ".b2s-hide-rating-forever", function (e) {
    e.preventDefault();
    hideRating(true);
    jQuery(this).closest('.panel').remove();
});

jQuery(document).on("click", ".b2s-allow-rating", function (e) {
    hideRating(false);
    jQuery(this).closest('.panel').remove();
});

jQuery(document).on("click", ".b2s-hide-premium-message", function (e) {
    e.preventDefault();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            action: 'b2s_hide_premium_message',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        }
    });
    jQuery(this).closest('.panel').remove();
});

jQuery(document).on("click", ".b2s-hide-trail-message", function (e) {
    e.preventDefault();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            action: 'b2s_hide_trail_message',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        }
    });
    jQuery(this).closest('.panel').remove();
});

jQuery(document).on("click", ".b2s-hide-trail-ended-modal", function (e) {
    e.preventDefault();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            action: 'b2s_hide_trail_ended_message',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        }
    });
    jQuery(this).closest('.panel').remove();
});

jQuery('.b2s-modal-privacy-policy-scroll-content').on('scroll', function () {
    if (jQuery(this).scrollTop() + jQuery(this).innerHeight() >= jQuery(this)[0].scrollHeight) {
        jQuery('.b2s-scroll-modal-down').hide();
    }
});

jQuery(document).on("click", ".b2s-scroll-modal-down", function (e) {
    var total = jQuery('.b2s-modal-privacy-policy-scroll-content')[0].scrollHeight;
    var current = jQuery('.b2s-modal-privacy-policy-scroll-content').scrollTop() + jQuery('.b2s-modal-privacy-policy-scroll-content').innerHeight();
    if (current >= total) {
        jQuery('.b2s-scroll-modal-down').hide();
    } else {
        jQuery('.b2s-modal-privacy-policy-scroll-content').animate({scrollTop: current + 30}, 'slow');
    }
    return false;
});

jQuery(document).on('click', '.b2s-network-auth-info-close', function () {
    jQuery(this).closest('.b2s-network-auth-info').hide();
});

jQuery(document).on('click', '.b2s-warning-close', function () {
    jQuery(this).closest('.panel').hide();
});


(function () {
    var noticeTimers = {};

    function onNoticeVisible(el) {
        var index = jQuery('.b2s-header-notice').index(el);
        jQuery('html, body').animate({scrollTop: 0}, 'fast');
        clearTimeout(noticeTimers[index]);
        noticeTimers[index] = setTimeout(function () {
            jQuery(el).fadeOut();
        }, 8000);
    }

    var observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            var el = mutation.target;
            if (jQuery(el).is(':visible')) {
                onNoticeVisible(el);
            }
        });
    });

    jQuery(document).ready(function () {
        jQuery('.b2s-header-notice').each(function () {
            observer.observe(this, {attributes: true, attributeFilter: ['style', 'class']});
        });
    });
}());



jQuery(document).on('click', '.b2s-metrics-banner-close', function () {
    jQuery('#b2s-metrics-banner-modal').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_metrics_banner_close',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == false) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
            }
            return true;
        }
    });
});

jQuery(document).on('click', '.b2s-continue-trial-btn', function () {
    jQuery('#b2s-trial-seven-day-modal').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_continue_trial_option',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == false) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
            }
            return true;
        }
    });
});

jQuery(document).on('click', '.b2s-hide-final-trial-btn', function () {
    jQuery('#b2s-final-trail-modal').modal('hide');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_final_trial_option',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == false) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-server-connection-fail').show();
                }
            }
            return true;
        }
    });
});


jQuery(document).on('click', '#b2s-debug-connection-btn', function () {
    jQuery('.b2s-loading-area').show();
    jQuery(this).hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_debug_connection',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery('.b2s-loading-area').hide();

            if (data.result == false) {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                } else {
                    jQuery('.b2s-debug-connection-result-area').show();
                    jQuery('.b2s-debug-connection-result-code').hide();
                    jQuery('.b2s-debug-connection-result-code-info').hide();
                    jQuery('.b2s-debug-connection-result-error').show();
                }
            } else {
                jQuery('.b2s-debug-connection-result-area').show();
                jQuery('.b2s-debug-connection-result-code').html(data.output);
                jQuery('.b2s-debug-connection-result-error').hide();

            }
            return true;
        }
    });
});
