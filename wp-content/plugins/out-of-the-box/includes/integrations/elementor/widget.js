(function ($) {
    'use strict';

    $(window).on("elementor/frontend/init", function () {

        elementor.channels.editor.on('wpcp:editor:edit_outofthebox_shortcode', openShortcodeBuilder);
        elementorFrontend.hooks.addAction('frontend/element_ready/wpcp-outofthebox.default', function () {
            $('.OutoftheBox').parent().trigger('inview');
        });
    });

    function openShortcodeBuilder(view) {

        window.wpcp_oftb_elementor_add_content = function (value) {
            view._parent.model.setSetting('shortcode', value)
            window.parent.jQuery('.elementor-control-shortcode textarea').trigger('input')
            window.modal_action.close();
            $('#outofthebox-modal-action').remove();
        }

        if ($('#outofthebox-modal-action').length > 0) {
            window.modal_action.close();
            $('#outofthebox-modal-action').remove();
        }

        /* Build the  Dialog */
        var modalbuttons = '';
        var modalheader = $('<a tabindex="0" class="close-button" title="" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
        var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" style="display:none"></div>');
        var modalfooter = $('<div class="outofthebox-modal-footer" style="display:none"><div class="outofthebox-modal-buttons">' + '' + '</div></div>');
        var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal outofthebox-modal80 light"><div class="modal-dialog"><div class="modal-content"><div class="loading"><div class="loader-beat"></div></div></div></div></div>');

        $('body').append(modaldialog);

        var shortcode = view._parent.model.getSetting('shortcode', 'true');
        var shortcode_attr = shortcode.replace('</p>', '').replace('<p>', '').replace('[outofthebox ', '').replace('"]', '');
        var query = encodeURIComponent(shortcode_attr).split('%3D%22').join('=').split('%22%20').join('&');

        var $iframe_template = $("<iframe src='" + OutoftheBox_vars.ajax_url + "?action=outofthebox-getpopup&type=shortcodebuilder&callback=wpcp_oftb_elementor_add_content&" + query + "' width='100%' height='500' tabindex='-1' frameborder='0'></iframe>");
        var $iframe = $iframe_template.appendTo(modalbody);

        $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

        $iframe.load(function () {
            $('.outofthebox-modal-body').fadeIn();
            $('.outofthebox-modal-footer').fadeIn();
            $('.modal-content .loading:first').fadeOut();
        });

        /* Open the Dialog */
        var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
            bodyClass: 'rmodal-open',
            dialogOpenClass: 'animated slideInDown',
            dialogCloseClass: 'animated slideOutUp',
            escapeClose: true
        });
        document.addEventListener('keydown', function (ev) {
            modal_action.keydown(ev);
        }, false);
        modal_action.open();
        window.modal_action = modal_action;
    }

})(jQuery);




