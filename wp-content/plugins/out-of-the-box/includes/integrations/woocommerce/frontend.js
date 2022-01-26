jQuery(document).ready(function ($) {
    'use strict';
    $.widget("cp.OutoftheBoxWC", {
        options: {

        },

        _create: function () {

            /* Ignite! */
            this._initiate();

        },

        _destroy: function () {
            return this._super();
        },

        _setOption: function (key, value) {
            this._super(key, value);
        },

        _initiate: function () {
            var self = this;
            self._initButtons();
        },

        _initButtons: function () {
            var self = this;

            $('.wpcp-wc-open-box').on('click', function (e) {
                self.openUploadBox($(this));
            });
        },

        openUploadBox: function (button) {

            var self = this;

            var container = button.next('.woocommerce-order-upload-box');

            /* Close any open modal windows */
            $('#outofthebox-modal-action').remove();

            /* Build the Upload Dialog */
            var modalheader = $('<a tabindex="0" class="close-button" title="' + this.options.str_close_title + '" onclick="modal_action.close();"><i class="eva eva-close eva-lg" aria-hidden="true"></i></a></div>');
            var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" style="height: 100%; padding:0;"></div>');
            var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal ' + this.options.content_skin + '"><div class="modal-dialog" style="max-width: 80vw;"><div class="modal-content" style="max-height: 90%;"></div></div></div>');

            $('body').append(modaldialog);
            $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody);


            /* Fill Textarea */
            $('.outofthebox-modal-body').append(container);
            container.show();

            /* Set the button actions */
            $('#outofthebox-modal-action .outofthebox-modal-confirm-btn').on('click', function (e) {
                modal_action.close();
            });

            /* Open the dialog */
            var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
                bodyClass: 'rmodal-open',
                dialogOpenClass: 'animated slideInDown',
                dialogCloseClass: 'animated slideOutUp',
                escapeClose: true,
                afterClose() {
                    container.hide();
                    button.after(container);
                },
            });

            document.addEventListener('keydown', function (ev) {
                modal_action.keydown(ev);
            }, false);
            modal_action.open();
            window.modal_action = modal_action;
            return false;
        }

    });

})

// Initiate the Module!
jQuery(document).ready(function ($) {
    $(document).OutoftheBoxWC(OutoftheBox_vars);
});