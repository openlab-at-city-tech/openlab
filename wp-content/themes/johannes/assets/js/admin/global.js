(function($) {

    "use strict";

    $(document).ready(function() {

        $("body").on('click', '#johannes_welcome_box_hide', function(e) {
            e.preventDefault();
            $(this).parent().fadeOut(300).remove();
            $.post(ajaxurl, { action: 'johannes_hide_welcome' }, function(response) {});
        });

        $("body").on('click', '#johannes_update_box_hide', function(e) {
            e.preventDefault();
            $(this).parent().remove();
            $.post(ajaxurl, { action: 'johannes_update_version' }, function(response) {});
        });

        $('body').on('click', '.mks-twitter-share-button', function(e) {
            e.preventDefault();
            var data = $(this).attr('data-url');
            johannes_social_share(data);
        });

        $('body').on('click', 'img.johannes-img-select', function(e) {
            e.preventDefault();
            $(this).closest('ul').find('img.johannes-img-select').removeClass('selected');
            $(this).addClass('selected');
            $(this).closest('ul').find('input').removeAttr('checked');
            $(this).closest('li').find('input').attr('checked', 'checked');
        });

        /* Display options custom/inherit switch */
        $('body').on('click', '.johannes-opt-display input', function(e) {
            var selection = $(this).val();
            //alert( selection );
            if (selection == 'custom') {
                $('.johannes-opt-display-custom').removeClass('johannes-hidden');
            } else {
                $('.johannes-opt-display-custom').addClass('johannes-hidden');
            }

        });


    });

    function johannes_social_share(data) {
        window.open(data, "Share", 'height=500,width=760,top=' + ($(window).height() / 2 - 250) + ', left=' + ($(window).width() / 2 - 380) + 'resizable=0,toolbar=0,menubar=0,status=0,location=0,scrollbars=0');
    }

})(jQuery);