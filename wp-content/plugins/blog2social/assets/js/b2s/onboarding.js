jQuery.noConflict();

jQuery(document).on('click', '.b2s-start-onboarding', function() {

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_user_onboarding',
            'onboarding': 1,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
           
        }
    });
});

jQuery(document).on('click', '.b2s-stop-onboarding', function() {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_save_user_onboarding',
            'onboarding': 2,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            location.reload();
        }
    });
});

jQuery(document).on('click', '#b2s-onboarding-btn-step-1', function() {
    jQuery("#b2s-onboarding-step-1-img-container").show();
    jQuery("#b2s-onboarding-step-1-img-container").removeClass('b2s-onboarding-step-opacity');
    jQuery("#b2s-onboarding-step-2-img-container").hide();

});

jQuery(document).on('click', '#b2s-onboarding-btn-step-2', function() {
    jQuery("#b2s-onboarding-step-1-img-container").show();
    jQuery("#b2s-onboarding-step-1-img-container").addClass('b2s-onboarding-step-opacity');
    jQuery("#b2s-onboarding-step-2-img-container").show();

});

jQuery(document).on('click', '#b2s-onboarding-btn-step-3', function() {
    jQuery("#b2s-onboarding-step-1-img-container").hide();
    jQuery("#b2s-onboarding-step-2-img-container").hide();
});




