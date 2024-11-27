jQuery.noConflict();

jQuery(document).on('click', '.b2s-network-add-user-app-btn', function () {

    var network_id = jQuery(this).attr("data-network-id");

    //X Violations 11/24
    if (network_id == "2") {
        jQuery("#b2sXViolationModal").modal('show');
    } else {
        if (jQuery('#b2s-user-app-count-current[data-network-id="' + network_id + '"]')[0].innerText < jQuery('#b2s-network-app-full-count[data-network-id="' + network_id + '"]')[0].innerText) {
            jQuery('#b2s-edit-user-app-id').val("");
            jQuery('#b2s-edit-user-app-name').val("");
            jQuery('#b2s-edit-user-app-key').val("");
            jQuery('#b2s-edit-user-app-secret').val("");

            if (network_id == "6") {
                jQuery('#b2s-add-user-app-key').attr("placeholder", "App-Id");
            } else {
                jQuery('#b2s-add-user-app-key').attr("placeholder", "App Key");
            }
            jQuery('.network-app-info').hide();
            jQuery('.network-app-info[data-network-id="' + network_id + '"]').show();
            jQuery("#b2sAddUserAppModal").modal('show');

        } else if (jQuery("#b2s-user-license").val() == 1) {
            jQuery("#b2sBuyAddonAppsModal").modal('show');

        } else {
            jQuery('#b2sPreFeatureModal').modal('show');

        }
        jQuery('#b2s-add-user-app-network-id').val(jQuery(this).attr("data-network-id"));
    }
});

function wop(url, name) {
    jQuery('.b2s-network-auth-info').hide();
    jQuery('.b2s-network-auth-success').hide();
    var location = encodeURI(window.location.protocol + '//' + window.location.hostname);
    window.open(url + '&location=' + location, name, "width=650,height=900,scrollbars=yes,toolbar=no,status=no,resizable=no,menubar=no,location=no,directories=no,top=20,left=20");
}
jQuery('#b2s-add-app-form').validate({
    ignore: "",
    errorPlacement: function () {
        return false;
    },
    submitHandler: function (form) {
        jQuery('.b2s-user-app-alert').hide();
        var name = jQuery('#b2s-add-user-app-name').val();
        var network = jQuery('#b2s-add-user-app-network-id').val();
        var key = jQuery('#b2s-add-user-app-key').val();
        var secret = jQuery('#b2s-add-user-app-secret').val();

        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: {
                'action': 'b2s_add_user_app',
                'app_name': name,
                'network_id': network,
                'app_key': key,
                'app_secret': secret,
                'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
            },
            error: function () {
                jQuery("#b2sAddUserAppModal").modal('hide');
                jQuery('.b2s-server-connection-fail').show();
                return false;
            },
            success: function (data) {

                jQuery('.b2s-user-apps-permission-premium').hide();
                jQuery('.b2s-user-apps-permission-free').hide();
                jQuery('.b2s-user-apps-generic-error').hide();
                jQuery('.b2s-user-apps-success').hide();

                if (data.result == true) {
                    html = '<li class="b2s-network-item-auth-list-li" data-network-id="' + data.network_id + '" data-app-id="' + data.app_id + '">';
                    html += '<div class="pull-left">';
                    html += '<span class="b2s-network-item-auth-user-name">' + data.app_name + '</span>';
                    html += '</div>';
                    html += '<div class="pull-right">';
                    html += '<a class="b2s-btn-delete-app-button b2s-add-padding-network-delete pull-right" data-network-type="0" data-app-id="' + data.app_id + '" data-app-name="' + data.app_name + '" data-network-id="' + data.network_id + '" href="#">';
                    html += '<span class="glyphicon glyphicon-trash glyphicon-grey"></span>';
                    html += '</a>';
                    html += '<a class="b2s-btn-edit-app-button b2s-add-padding-network-delete pull-right" data-network-type="0" data-app-id="' + data.app_id + '" data-app-name="' + data.app_name + '" data-network-id="' + data.network_id + '" href="#">';
                    html += '<span class="glyphicon glyphicon-pencil glyphicon-grey"></span>';
                    html += '</a>';
                    html += '</div>';
                    html += '<div class="clearfix"></div>';
                    html += '</li>';

                    var anchor = jQuery('.b2s-network-item-auth-list[data-network-id="' + network + '"]');
                    jQuery(anchor).last().append(html);
                    var counter = jQuery('.b2s-user-app-count-current[data-network-id="' + network + '"]').html();
                    jQuery('.b2s-user-app-count-current[data-network-id="' + network + '"]').html(Number(counter) + 1);
                    jQuery("#b2sAddUserAppModal").modal('hide');
                    jQuery('.b2s-user-apps-success').show();

                } else {
                    if (data.error_code == "APP_PAID") {
                        if (jQuery("#b2s-user-license").val() == 1) {
                            jQuery('.b2s-user-apps-permission-premium').show();
                        } else {
                            jQuery('.b2s-user-apps-permission-free').show();
                        }
                    } else {
                        jQuery('.b2s-user-apps-generic-error').show();
                    }

                    jQuery("#b2sAddUserAppModal").modal('hide');
                }
            }
        });
        return false;
    }
});

jQuery(document).on('click', '.b2s-add-app-submit-btn', function () {
    var submit = true;

    if (jQuery('#b2s-add-user-app-name').val() == "") {
        submit = false;
        jQuery('#b2s-add-user-app-name').css('border-color', 'red');
    } else {
        jQuery('#b2s-add-user-app-key').css('border-color', 'gray');
    }

    if (jQuery('#b2s-add-user-app-key').val() == "") {
        submit = false;
        jQuery('#b2s-add-user-app-key').css('border-color', 'red');
    } else {
        jQuery('#b2s-add-user-app-key').css('border-color', 'gray');
    }

    if (jQuery('#b2s-add-user-app-secret').val() == "") {
        submit = false;
        jQuery('#b2s-add-user-app-secret').css('border-color', 'red');
    } else {
        jQuery('#b2s-add-user-app-key').css('border-color', 'gray');
    }

    if (submit) {
        jQuery('#b2s-add-app-form').submit();
    }
});

jQuery(document).on('click', '.b2s-btn-edit-app-button', function () {
    var app_id = jQuery(this).attr("data-app-id");
    var app_name = jQuery(this).attr("data-app-name");
    var app_key = jQuery(this).attr("data-app-key");
    var app_secret = jQuery(this).attr("data-app-secret");
    if (jQuery(this).attr("disabled") == null) {
        jQuery('#b2s-edit-user-app-name').val(app_name);
        jQuery('#b2s-edit-user-app-id').val(app_id);
        jQuery('#b2s-edit-user-app-key').attr("placeholder", app_key);
        jQuery('#b2s-edit-user-app-secret').attr("placeholder", app_secret);

        if (jQuery(this).attr("data-network-id") == "6") {
            jQuery('#b2s-edit-user-app-key-name').html("App-Id");
        } else {
            jQuery('#b2s-edit-user-app-key-name').html("App Key");
        }

        jQuery('#b2sEditUserAppModal').modal('show');
    }

});


jQuery(document).on('click', '.b2s-edit-app-submit-btn', function () {
    jQuery('.b2s-user-app-alert').hide();
    var app_id = jQuery('#b2s-edit-user-app-id').val();
    var app_name = jQuery('#b2s-edit-user-app-name').val();
    var app_key = jQuery('#b2s-edit-user-app-key').val();
    var app_secret = jQuery('#b2s-edit-user-app-secret').val();

    var data = {
        'action': 'b2s_edit_user_app',
        'app_id': app_id,
        'app_name': app_name,
        'app_key': app_key,
        'app_secret': app_secret,
        'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
    };

    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: data,
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            return false;
        },
        success: function (data) {
            if (data.result == true) {
                jQuery('.b2s-user-app-name[data-app-id="' + data.app_id + '"]').html(data.app_name);
            }
            jQuery('.b2s-user-apps-edit-success').show();
            jQuery('#b2sEditUserAppModal').modal('hide');
        }
    });

});


jQuery(document).on('click', '.b2s-btn-delete-app-button', function () {
    jQuery('.b2s-user-app-alert').hide();
    var app_id = jQuery(this).attr("data-app-id");
    jQuery('#b2s-delete-user-app-id').val(app_id);
    jQuery('#b2sDeleteUserAppModal').modal('show');

});


jQuery(document).on('click', '.b2s-btn-network-delete-app-confirm-btn', function () {
    var app_id = jQuery('#b2s-delete-user-app-id').val();
    var network_id = jQuery('.b2s-btn-delete-app-button[data-app-id="' + app_id + '"]').attr('data-network-id');
    jQuery.ajax({
        url: ajaxurl,
        type: "POST",
        dataType: "json",
        cache: false,
        data: {
            'action': 'b2s_delete_user_app',
            'app_id': app_id,
            'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
        },
        error: function () {
            jQuery('.b2s-server-connection-fail').show();
            jQuery('#b2sDeleteUserAppModal').modal('hide');
            return false;
        },
        success: function (data) {
            var currentAppCount = Number(jQuery('#b2s-user-app-count-current[data-network-id="' + network_id + '"]')[0].innerText);
            jQuery('#b2s-user-app-count-current[data-network-id="' + network_id + '"]').text(currentAppCount - 1);
            jQuery('#b2sDeleteUserAppModal').modal('hide');
            jQuery('.b2s-user-apps-delete-success').show();
            var app = jQuery('.b2s-network-item-auth-list-li[data-app-id="' + app_id + '"]');
            app.remove();
        }
    });
});

