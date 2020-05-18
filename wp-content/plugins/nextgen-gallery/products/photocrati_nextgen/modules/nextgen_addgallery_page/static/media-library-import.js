(function($) {
    var ngg_importml = {

        ml_data: null,
        import_ids: [],

        selectors: {
            ml_btn_import:  $('#ngg-importML-selected-image-import'),
            ml_btn_select:  $('#ngg-importML-select-opener'),
            gallery_select: $('#ngg-importML-gallery-id'),
            gallery_name:   $('#ngg-importML-gallery-name')
        },

        initialize: function() {
            this.methods.initialize();
            this.methods.set_events();
        },

        methods: {

            initialize: function() {
                ngg_importml.ml_dialog = top.wp.media.frames.ngg_importml = top.wp.media({
                    multiple: true,
                    title: ngg_importml_i18n.title,
                    button: { text: ngg_importml_i18n.button_text }
                });
            },

            urlencode: function(str) {
                str = (str + '').toString();
                return encodeURIComponent(str)
                    .replace(/!/g,   '%21')
                    .replace(/'/g,   '%27')
                    .replace(/\(/g,  '%28')
                    .replace(/\)/g,  '%29')
                    .replace(/\*/g,  '%2A')
                    .replace(/%20/g, '+');
            },

            import: {

                import_count: 0,

                params: {
                    action: 'import_media_library'
                },

                start: function() {
                    // prevent the impatient from causing simultaneous ongoing posts
                    ngg_importml.selectors.ml_btn_import.attr('disabled', true);
                    ngg_importml.selectors.ml_btn_select.attr('disabled', true);

                    ngg_importml.methods.import.params.gallery_id   = ngg_importml.methods.urlencode(ngg_importml.selectors.gallery_select.val());
                    ngg_importml.methods.import.params.gallery_name = ngg_importml.methods.urlencode(ngg_importml.selectors.gallery_name.val());

                    ngg_importml.progress_bar = $.nggProgressBar({
                        title: ngg_importml_i18n.progress_title,
                        infinite: true,
                        starting_value: ngg_importml_i18n.in_progress
                    });

                    $(ngg_importml).trigger('send_ajax');
                },

                done: function() {
                    ngg_importml.progress_bar.close(100);
                    ngg_importml.selectors.ml_btn_import.attr('disabled', false);
                    ngg_importml.selectors.ml_btn_select.attr('disabled', false);

                    var msg = ngg_importml_i18n.imported_multiple;
                    if (ngg_importml.methods.import.import_count == 1) {
                        msg = ngg_importml_i18n.imported_singular;
                    }
                    msg = msg.replace('{gid}', ngg_importml.methods.import.params.gallery_id);
                    msg = msg.replace('{count}', ngg_importml.methods.import.import_count);

                    delete ngg_importml.methods.import.params.gallery_id;
                    delete ngg_importml.methods.import.params.gallery_name;

                    $.gritter.add({
                        title: ngg_importml_i18n.gritter_title,
                        text: msg,
                        sticky: true
                    });

                    ngg_importml.methods.import.import_count = 0;

                    // Empty the current selection & revert to the default state
                    ngg_importml.ml_dialog.trigger('reset');
                    ngg_importml.import_ids = [];
                    ngg_importml.selectors.ml_btn_import.fadeOut();
                },

                send_ajax: function() {
                    var params = ngg_importml.methods.import.params;
                    params.nonce = ngg_importml_i18n.nonce;
                    params.attachment_ids = [ngg_importml.import_ids.pop()];

                    $.post(photocrati_ajax.url, params, function(data) {
                        if (typeof data.error == 'undefined') {
                            ngg_importml.methods.import.import_count++;

                            // If we created a new gallery, ensure it's now in the drop-down list, and select it
                            if (ngg_importml.selectors.gallery_select.find('option[value="' + data.gallery_id + '"]').length == 0) {
                                ngg_importml.methods.import.params.gallery_id = data.gallery_id;
                                var option = $('<option/>').attr('value', data.gallery_id).html(data.gallery_name);
                                ngg_importml.selectors.gallery_select.append(option);
                                ngg_importml.selectors.gallery_select.val(data.gallery_id);
                                option.prop('selected', true);
                                ngg_importml.selectors.gallery_name.val('').fadeOut();
                            }
                        } else {
                            $.gritter.add({
                                title: ngg_importml_i18n.gritter_error,
                                text: data.error,
                                sticky: true
                            });
                        }
                        if (ngg_importml.import_ids.length == 0) {
                            ngg_importml.methods.import.done();
                        } else {
                            $(ngg_importml).trigger('send_ajax');
                        }
                    }, 'json');
                }
            },

            set_events: function() {

                $(ngg_importml).on('send_ajax', function() {
                    ngg_importml.methods.import.send_ajax();
                });

                // Captures selected images and records their ID
                ngg_importml.ml_dialog.on('select', function () {
                    ngg_importml.import_ids = [];
                    ngg_importml.ml_data = ngg_importml.ml_dialog.state().get('selection');
                    ngg_importml.ml_data.map(function(image) {
                        image = image.toJSON();
                        ngg_importml.import_ids.push(image.id);
                    });
                    var msg = ngg_importml_i18n.import_multiple.replace('%s', ngg_importml.import_ids.length);
                    if (ngg_importml.import_ids.length == 1) {
                        msg = ngg_importml_i18n.import_singular;
                    }
                    ngg_importml.selectors.ml_btn_import.html(msg);
                    ngg_importml.selectors.ml_btn_import.fadeIn();
                });

                // Opens Media Library dialog to select images for import
                ngg_importml.selectors.ml_btn_select.on('click', function(event) {
                    event.preventDefault();
                    ngg_importml.ml_dialog.open();
                });

                // Import selected images
                ngg_importml.selectors.ml_btn_import.on('click', function(event) {
                    event.preventDefault();
                    ngg_importml.methods.import.start();
                });

                // Show/hide MediaLibrary import buttons if a gallery is selected
                ngg_importml.selectors.gallery_select.on('change', function() {
                    if (parseInt(this.value) == 0) {
                        ngg_importml.selectors.gallery_name.fadeIn().focus();
                        if (ngg_importml.selectors.gallery_name.val().length == 0) {
                            ngg_importml.selectors.ml_btn_import.fadeOut();
                            ngg_importml.selectors.ml_btn_select.fadeOut();
                        }
                    } else {
                        ngg_importml.selectors.gallery_name.fadeOut(400, function() {
                            ngg_importml.selectors.gallery_select.focus();
                            ngg_importml.selectors.ml_btn_select.fadeIn();
                            if (ngg_importml.import_ids.length > 0) {
                                ngg_importml.selectors.ml_btn_import.fadeIn();
                            }
                        });
                    }
                });

                // Ensure the gallery name is filled in if "create new gallery" is selected
                ngg_importml.selectors.gallery_name.on('keyup', function() {
                    if (ngg_importml.selectors.gallery_name.val().length > 0) {
                        ngg_importml.selectors.gallery_name.removeClass('error');
                        ngg_importml.selectors.ml_btn_select.fadeIn();
                        if (ngg_importml.import_ids.length > 0) {
                            ngg_importml.selectors.ml_btn_import.fadeIn();
                        }
                    } else {
                        ngg_importml.selectors.ml_btn_import.fadeOut();
                        ngg_importml.selectors.ml_btn_select.fadeOut();
                    }
                });
            }
        }
    };

    $(document).ready(function() {
        window.ngg_importml = ngg_importml;
        ngg_importml.initialize();
        window.Frame_Event_Publisher.broadcast();
    });

})(jQuery);
