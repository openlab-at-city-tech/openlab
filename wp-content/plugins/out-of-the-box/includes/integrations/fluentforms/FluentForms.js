(function ($) {
    /* Shortcode Builder Popup */
    $('#ff_form_editor_app').on('click', '.outofthebox.open-shortcode-builder', function () {
        var input_field = $(this).closest('.el-form-item').find('textarea');
        var shortcode = input_field.val().replace('[outofthebox ', '').replace('"]', '');
        var query = encodeURIComponent(shortcode).split('%3D%22').join('=').split('%22%20').join('&');
        tb_show("Build Shortcode for Form", ajaxurl + '?action=outofthebox-getpopup&' + query + '&type=shortcodebuilder&standalone=1&asuploadbox=1&callback=wpcp_oftb_fluentforms_add_content&TB_iframe=true&height=600&width=800');

        // Update Z-index to force the popup over the Builder
        $('#TB_window, #TB_overlay').css('z-index', 99999999);

        $('.thickbox_data').removeClass('thickbox_data');
        input_field.addClass('thickbox_data');
    });

    /* Callback function to add shortcode to WPForms input field*/
    if (typeof window.wpcp_oftb_fluentforms_add_content === 'undefined') {
        window.wpcp_oftb_fluentforms_add_content = function (data) {
            //Field cannot be automatically updated as Fluent Forms is using VUE without dynamic setters
            //$('.thickbox_data').val(data).trigger('keyup change');

            tb_remove();
        }
    }
})(jQuery);