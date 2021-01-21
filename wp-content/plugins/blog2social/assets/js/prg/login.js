jQuery(document).on('click', '#prgLoginBtn', function () {
    jQuery("#prgLoginInfoFail").hide();
    jQuery("#prgLoginInfoSSL").hide();
    var postId = jQuery('#postId').val();
    jQuery("#prgLogin").validate({
        ignore: "",
        rules: {
            username: {
                required: true
            },
            password: {
                required: true
            }
        },
        errorPlacement: function () {
            return false;
        },
        submitHandler: function (form) {
            jQuery.ajax({
                url: ajaxurl,
                type: "POST",
                dataType: "json",
                cache: false,
                data: {
                    'action': 'b2s_prg_login',
                    'postId': postId,
                    'username': jQuery('#username').val(),
                    'password': jQuery('#password').val(),
                    'b2s_security_nonce': jQuery('#b2s_security_nonce').val()
                },
                success: function (data) {
                    if (data.result == false) {
                        var errorCode = unescape(data.error);
                        if (errorCode == "2") {
                            jQuery("#prgLoginInfoSSL").show();
                        } else {
                            jQuery("#prgLoginInfoFail").show();
                        }
                        if(data.error == 'nonce'){
                            jQuery('.b2s-nonce-check-fail').show();
                        }
                    } else {
                        window.location.href = window.location.pathname + "?page=prg-ship&postId=" + postId;
                    }
                    return false;
                }
            });
        }
    });
});




