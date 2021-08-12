(function ($) {
    'use strict';

    /* binding to the load field settings event to initialize */
    $(document).on("gform_load_field_settings", function (event, field, form) {
        jQuery("#field_wpcp_outofthebox").val(field.defaultValue);
        if (field["OutoftheBoxShortcode"] !== undefined && field["OutoftheBoxShortcode"] !== '') {
            jQuery("#field_wpcp_outofthebox").val(field["OutoftheBoxShortcode"]);
        }
    });

    /* Shortcode Generator Popup */
    $('.wpcp-shortcodegenerator.outofthebox').on('click', function (e) {
        var shortcode = jQuery("#field_wpcp_outofthebox").val();
        shortcode = shortcode.replace('[outofthebox ', '').replace('"]', '');
        var query = encodeURIComponent(shortcode).split('%3D%22').join('=').split('%22%20').join('&');
        tb_show("Build Shortcode for Form", ajaxurl + '?action=outofthebox-getpopup&' + query + '&type=shortcodebuilder&asuploadbox=1&callback=wpcp_oftb_gf_add_content&TB_iframe=true&height=600&width=800');
    });

    /* Callback function to add shortcode to GF field */
    if (typeof window.wpcp_oftb_gf_add_content === 'undefined') {
        window.wpcp_oftb_gf_add_content = function (data) {
            $('#field_wpcp_outofthebox').val(data);
            SetFieldProperty('OutoftheBoxShortcode', data);

            tb_remove();
        }
    }
})(jQuery);