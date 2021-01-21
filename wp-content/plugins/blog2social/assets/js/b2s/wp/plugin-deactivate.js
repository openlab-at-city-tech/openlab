jQuery(document).on('click', '.b2s-plugin-modal-btn-close', function () {
    jQuery('#' + jQuery(this).attr('data-modal-target')).hide();
});
jQuery(document).on('click', '#b2s-deactivate', function (e) {
    var redirect = jQuery(this).attr("href");
    jQuery('html, body').animate({scrollTop: jQuery("body").offset().top}, 1);
    jQuery('#b2s-plugin-deactivate-modal').show();
    jQuery('#b2s-plugin-deactivate-redirect-url').val(redirect);
    return false;
});
jQuery(document).on('click', '#b2s-plugin-deactivate-btn', function () {
    var isChecked = 1;
    if (!jQuery("#b2s-plugin-deactivate-checkbox-sched-post").is(':checked')) {
        isChecked = 0;
    }
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_plugin_deactivate_delete_sched_post',
            'delete_sched_post': isChecked,
            'b2s_deactivate_nonce': jQuery('#b2s_deactivate_nonce').val()
        },
        error: function () {
            window.location.reload();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                window.location.href = jQuery('#b2s-plugin-deactivate-redirect-url').val();
            } else {
                window.location.reload();
            }
        }
    });
    return false;
});