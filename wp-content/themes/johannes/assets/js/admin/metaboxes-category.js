(function($) {

    "use strict";

    $(document).ready(function() {

        /* Enable/disable slider and sidebar basend on current layout */
        $('body').on('click', '.johannes-opt-layouts img.johannes-img-select', function(e) {
            e.preventDefault();
            var sidebar = parseInt($(this).data('sidebar'));

            if( sidebar ){
                $('.johannes-opt-sidebar').removeClass('johannes-opt-disabled');
            } else {
                $('.johannes-opt-sidebar').addClass('johannes-opt-disabled');
            }

        });


        /* Image upload */
        var meta_image_frame;

        $('body').on('click', '#johannes-image-upload', function(e) {

            e.preventDefault();

            if (meta_image_frame) {
                meta_image_frame.open();
                return;
            }

            meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
                title: 'Choose your image',
                button: {
                    text: 'Set Category image'
                },
                library: {
                    type: 'image'
                }
            });

            meta_image_frame.on('select', function() {

                var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
                $('#johannes-image-url').val(media_attachment.url);
                $('#johannes-image-preview').attr('src', media_attachment.url);
                $('#johannes-image-preview').show();
                $('#johannes-image-clear').show();

            });

            meta_image_frame.open();
        });


        $('body').on('click', '#johannes-image-clear', function(e) {
            $('#johannes-image-preview').hide();
            $('#johannes-image-url').val('');
            $(this).hide();
        });

    });

})(jQuery);