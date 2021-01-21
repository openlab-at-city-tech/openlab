jQuery(document).on('click', "#prgLogoutBtn", function () {
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            action: "b2s_prg_logout",
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        success: function (data) {
            if (data.result == true) {
                parent.window.location.href = parent.window.location.pathname + "?page=prg-post&prgLogout=true";
                return false;
            } else {
                if(data.error == 'nonce'){
                    jQuery('.b2s-nonce-check-fail').show();
                }
            }
        }
    });
});

jQuery(document).on('click', '.b2s-modal-close', function () {
    jQuery(jQuery(this).attr('data-modal-name')).modal('hide');
    jQuery(jQuery(this).attr('data-modal-name')).hide();
    jQuery('body').removeClass('modal-open');
    jQuery('body').removeAttr('style');
    return false;
});