jQuery(window).on("load", function () {
    jQuery(".checkPRGButton").removeAttr("disabled");
    if (jQuery(".prgImage")[0] != undefined && jQuery(".prgImage")[0].checked) {
        jQuery(".prgImageRights").show();
    }
});

jQuery(document).on('change', '.prgImage', function () {
    jQuery(".prgImageRights").show();
});

jQuery(document).on('click', '.prg-ship-confirm', function () {
    jQuery('#confirm').val('1');
    jQuery('#prgShip').submit();
});

jQuery(document).on('click', '.publish', function () {
    jQuery('#publish').val('1');
});

jQuery("#prgShip").validate({
    ignore: "",
    onsubmit: true,
    invalidHandler: function (e, validator) {
        if (validator.errorList.length)
            jQuery('a[href="#' + jQuery(validator.errorList[0].element).closest(".tab-pane").attr("id") + '"]').tab("show")
    },
    rules: {
        title: "required",
        message: "required",
        kategorie_id: "required",
        name_presse: "required",
        anrede_presse: "required",
        vorname_presse: "required",
        nachname_presse: "required",
        strasse_presse: "required",
        nummer_presse: "required",
        plz_presse: "required",
        ort_presse: "required",
        land_presse: "required",
        telefon_presse: "required",
        email_presse: {
            required: true,
            email: true
        },
        url_presse: {
            required: true,
            url: true
        },
        name_mandant: "required",
        anrede_mandant: "required",
        vorname_mandant: "required",
        nachname_mandant: "required",
        strasse_mandant: "required",
        nummer_mandant: "required",
        plz_mandant: "required",
        ort_mandant: "required",
        land_mandant: "required",
        telefon_mandant: "required",
        email_mandant: {
            required: true,
            email: true
        },
        url_mandant: {
            required: true,
            url: true
        },
        info_mandant: {
            required: true,
            minlength: 20
        },
        bildtitel: {
            required: ".prgImage:checked"
        },
        bildcopyright: {
            required: ".prgImage:checked"
        }
    },
    errorPlacement: function (error, element) {
        return true;
    },
    submitHandler: function (form) {
        if (jQuery('#confirm').val() == '0' && jQuery('#publish').val() == '1') {
            jQuery('#prg-ship-modal').modal('show');
            return false;
        }
        jQuery('.prg-loading-area').show();
        jQuery('.prg-ship-form').hide();
        jQuery.ajax({
            url: ajaxurl,
            type: "POST",
            dataType: "json",
            cache: false,
            data: jQuery(form).serialize() + '&b2s_security_nonce=' + jQuery('#b2s_security_nonce').val(),
            success: function (data) {
                if (data.result == false) {
                    var errorCode = unescape(data.error);
                    var type = unescape(data.type);
                    jQuery('.prg-loading-area').hide();
                    jQuery('.prg-ship-form').show();
                    if (errorCode == "2") {
                        jQuery("#prgShipFail").show();
                    } else {
                        jQuery("#prgShipInvalidData").show();
                    }
                } else {
                    parent.window.location.href = parent.window.location.pathname + "?page=prg-post&prgShip=true&type=" + type;
                }
                return false;
            }
        })
    }
});
