jQuery.noConflict();
jQuery(window).on("load", function () {

    if (jQuery('#b2sUserLang').val() == 'de') {
        showMeridian = false;
    }

    jQuery(".b2s-import-auto-post-type").chosen();
    jQuery(".b2s-import-auto-post-categories").chosen();
    jQuery(".b2s-import-auto-post-taxonomies").chosen();
    jQuery(".b2s-auto-post-assign-user").chosen();

    jQuery('.b2s-network-item-auth-list[data-network-count="true"]').each(function () {
        jQuery('.b2s-network-auth-count-current[data-network-id="' + jQuery(this).attr("data-network-id") + '"]').text(jQuery(this).children('li').length);
    });

    var length = jQuery('.b2s-post-type-item-update').filter(':checked').length;
    if (length > 0) {
        jQuery('.b2s-auto-post-own-update-warning').show();
    }

    //TOS Twitter 032018 - none multiple Accounts - User select once
    checkNetworkTos(2);


    //Twitter Dropdown anpassen
    var mandantId = jQuery('#b2s-auto-post-profil-dropdown').val();
    jQuery('.b2s-auto-post-error[data-error-reason="no-auth-in-mandant"]').hide();
    var tos = false;
    if (jQuery('#b2s-auto-post-profil-data-' + mandantId).val() == "") {
        tos = true;
    } else {
        //TOS Twitter Check
        var len = jQuery('#b2s-auto-post-profil-dropdown-twitter').children('option[data-mandant-id="' + mandantId + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-auto-post-twitter-profile').show();
            jQuery('#b2s-auto-post-profil-dropdown-twitter').prop('disabled', false);
            jQuery('#b2s-auto-post-profil-dropdown-twitter').show();
            jQuery('#b2s-auto-post-profil-dropdown-twitter option').attr("disabled", "disabled");
            jQuery('#b2s-auto-post-profil-dropdown-twitter option[data-mandant-id="' + mandantId + '"]').attr("disabled", false);
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-auto-post-twitter-profile').hide();
        jQuery('#b2s-auto-post-profil-dropdown-twitter').prop('disabled', 'disabled');
        jQuery('#b2s-auto-post-profil-dropdown-twitter').hide();
    }

    if (jQuery("#b2s_user_version").val() == 0) {
        jQuery('#b2s-user-network-settings-auto-post-own :input').prop('disabled', 'disabled');
    }

});

//TOS Twitter 032018 - none multiple Accounts - User select once
jQuery(document).on('change', '.b2s-network-tos-check', function () {
    var networkId = jQuery(this).attr('data-network-id');
    if (networkId == 2) {
        checkNetworkTos(networkId, false);
    }
    return false;
});

//TOS Twitter 032018 - none multiple Accounts - User select once
function checkNetworkTos(networkId) {
    var len = jQuery('.b2s-network-tos-check[data-network-id="' + networkId + '"]:checked').length;
    if (len > 1) {
        jQuery('.b2s-network-tos-auto-post-import-warning').show();
        jQuery('#b2s-auto-post-settings-btn').attr('disabled', 'disabled');
        return false;
    } else {
        jQuery('.b2s-network-tos-auto-post-import-warning').hide();
        jQuery('#b2s-auto-post-settings-btn').attr('disabled', false);
        return true;
    }
}

jQuery(document).on('change', '.b2s-post-type-item-update', function () {
    var length = jQuery('.b2s-post-type-item-update').filter(':checked').length;
    if (length == 0) {
        jQuery('.b2s-auto-post-own-update-warning').hide();
    } else {
        jQuery('.b2s-auto-post-own-update-warning').show();
    }
    return false;
});

jQuery('#b2s-user-network-settings-auto-post-own').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-settings-user-success').hide();
        jQuery('.b2s-settings-user-error').hide();
        jQuery(".b2s-loading-area").show();
        jQuery(".b2s-autopost-area").hide();
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
                jQuery(".b2s-loading-area").hide();
                jQuery(".b2s-autopost-area").show();
                if (data.result == true) {
                    jQuery('.b2s-settings-user-success').show();
                } else {
                    if (data.error == 'nonce') {
                        jQuery('.b2s-nonce-check-fail').show();
                    }
                    jQuery('.b2s-settings-user-error').show();
                }
            }
        });
        return false;
    }
});

jQuery(document).on('click', '.b2s-post-type-select-btn', function () {
    var type = jQuery(this).attr('data-post-type');
    var tempCurText = jQuery(this).text();
    if (jQuery(this).attr('data-select-toogle-state') == "0") { //0=select
        jQuery('.b2s-post-type-item-' + type).prop('checked', true);
        jQuery(this).attr('data-select-toogle-state', '1');
        if (type == 'update') {
            jQuery('.b2s-auto-post-own-update-warning').show();
        }
    } else {
        jQuery('.b2s-post-type-item-' + type).prop('checked', false);
        jQuery(this).attr('data-select-toogle-state', '0');
        if (type == 'update') {
            jQuery('.b2s-auto-post-own-update-warning').hide();
        }
    }
    jQuery(this).text(jQuery(this).attr('data-select-toogle-name'));
    jQuery(this).attr('data-select-toogle-name', tempCurText);
    return false;
});

jQuery(document).on('click', '.b2sInfoAutoPosterMModalBtn', function () {
    jQuery('#b2sInfoAutoPosterMModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoAutoPosterAModalBtn', function () {
    jQuery('#b2sInfoAutoPosterAModal').modal('show');
});
jQuery(document).on('click', '.b2sTwitterInfoModalBtn', function () {
    jQuery('#b2sTwitterInfoModal').modal('show');
});
jQuery(document).on('click', '.b2sInfoAssignAutoPostBtn', function () {
    jQuery('#b2sInfoAssignAutoPost').modal('show');
});
jQuery(document).on('click', '.b2sAutoPostBestTimesInfoModalBtn', function () {
    jQuery('#b2sAutoPostBestTimesInfoModal').modal('show');
});

jQuery(document).on('change', '.b2s-auto-post-area-toggle', function () {
    if (jQuery(this).is(':checked')) {
        jQuery('.b2s-auto-post-area[data-area-type="' + jQuery(this).data('area-type') + '"]').show();
        if (jQuery(this).data('area-type') == 'manuell') {
            if (jQuery('.b2s-autopost-m-show-modal').val() == '1') {
                jQuery('#b2sAutoPostMInfoModal').modal('show');
                jQuery('.b2s-autopost-m-show-modal').val('0');
            }
        }
        if (jQuery(this).data('area-type') == 'import') {
            if (jQuery('.b2s-autopost-a-show-modal').val() == '1') {
                jQuery('#b2sAutoPostAInfoModal').modal('show');
                jQuery('.b2s-autopost-a-show-modal').val('0');
            }
        }
    } else {
        jQuery('.b2s-auto-post-area[data-area-type="' + jQuery(this).data('area-type') + '"]').hide();
    }
});

jQuery(document).on('change', '#b2s-auto-post-profil-dropdown', function () {
    jQuery('.b2s-auto-post-error[data-error-reason="no-auth-in-mandant"]').hide();
    var tos = false;
    if (jQuery('#b2s-auto-post-profil-data-' + jQuery(this).val()).val() == "") {
        tos = true;
    } else {
        //TOS Twitter Check
        var len = jQuery('#b2s-auto-post-profil-dropdown-twitter').children('option[data-mandant-id="' + jQuery(this).val() + '"]').length;
        if (len >= 1) {
            jQuery('.b2s-auto-post-twitter-profile').show();
            jQuery('#b2s-auto-post-profil-dropdown-twitter').prop('disabled', false);
            jQuery('#b2s-auto-post-profil-dropdown-twitter').show();
            jQuery('#b2s-auto-post-profil-dropdown-twitter option').attr("disabled", "disabled");
            jQuery('#b2s-auto-post-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]').attr("disabled", false);
            jQuery('#b2s-auto-post-profil-dropdown-twitter option[data-mandant-id="' + jQuery(this).val() + '"]:first').attr("selected", "selected");
        } else {
            tos = true;
        }

    }
    //TOS Twitter 032018
    if (tos) {
        jQuery('.b2s-auto-post-twitter-profile').hide();
        jQuery('#b2s-auto-post-profil-dropdown-twitter').prop('disabled', 'disabled');
        jQuery('#b2s-auto-post-profil-dropdown-twitter').hide();
    }
});

jQuery(document).on('click', '#b2s-auto-post-settings-btn', function () {
    var submit = true;
    if (jQuery('.b2s-auto-post-area-toggle[data-area-type="manuell"]').is(':checked')) {
        var publish = jQuery('.b2s-post-type-item-publish').is(':checked');
        var update = jQuery('.b2s-post-type-item-update').is(':checked');
        if (publish == false && update == false) {
            submit = false;
            jQuery('.b2s-auto-post-error[data-error-reason="no-post-type"]').show();
            jQuery('.b2s-post-type-item-publish').css('border-color', 'red');
            jQuery('.b2s-post-type-item-update').css('border-color', 'red');
        }
        if (jQuery('#b2s-auto-post-profil-data-' + jQuery('#b2s-auto-post-profil-dropdown').val()).val() == "") {
            submit = false;
            jQuery('.b2s-auto-post-error[data-error-reason="no-auth-in-mandant"]').show();
        }
    }

    if (jQuery('.b2s-auto-post-area-toggle[data-area-type="import"]').is(':checked')) {
        if (jQuery('.b2s-network-tos-check').is(':checked') == false) {
            submit = false;
            jQuery('.b2s-auto-post-error[data-error-reason="import-no-auth"]').show();
            jQuery('.b2s-network-tos-check').css('border-color', 'red');
        }
    }

    if (submit) {
        jQuery('#b2s-user-network-settings-auto-post-own').submit();
    }
});

jQuery(document).on('change', '.b2s-post-type-item-publish', function () {
    jQuery('.b2s-auto-post-error[data-error-reason="no-post-type"]').hide();
    jQuery('.b2s-post-type-item-publish').css('border-color', '');
    jQuery('.b2s-post-type-item-update').css('border-color', '');
});
jQuery(document).on('change', '.b2s-post-type-item-update', function () {
    jQuery('.b2s-auto-post-error[data-error-reason="no-post-type"]').hide();
    jQuery('.b2s-post-type-item-publish').css('border-color', '');
    jQuery('.b2s-post-type-item-update').css('border-color', '');
});

jQuery(document).on('change', '.b2s-network-tos-check', function () {
    jQuery('.b2s-auto-post-error[data-error-reason="import-no-auth"]').hide();
    jQuery('.b2s-network-tos-check').css('border-color', '');
});

jQuery(document).on('click', '#b2s-auto-post-assign-by-disconnect', function () {
    jQuery(".b2s-loading-area").show();
    jQuery(".b2s-autopost-area").hide();
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_auto_post_assign_by_disconnect',
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            jQuery(".b2s-loading-area").hide();
            jQuery(".b2s-autopost-area").show();
            if (data.result == true) {
                location.reload();
            } else {
                if (data.error == 'nonce') {
                    jQuery('.b2s-nonce-check-fail').show();
                }
                jQuery('.b2s-settings-user-error').show();
            }
        }
    });
    return false;
});