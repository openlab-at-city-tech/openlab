(function($) {

    "use strict";

    $(document).ready(function() {

        /* Metabox switch - do not show every metabox for every template */

        var johannes_is_gutenberg = typeof wp.apiFetch !== 'undefined';
        var johannes_template_selector = johannes_is_gutenberg ? '.editor-page-attributes__template select' : '#page_template';

        if (johannes_is_gutenberg) {

            var post_id = $('#post_ID').val();
            wp.apiFetch({ path: '/wp/v2/pages/' + post_id, method: 'GET' }).then(function(obj) {
                johannes_template_metaboxes(false, obj.template);
            });

        } else {
            johannes_template_metaboxes(false);
        }

        $('body').on('change', johannes_template_selector, function(e) {
            johannes_template_metaboxes(true);
        });

        function johannes_template_metaboxes(scroll, t) {

            var template = t ? t : $(johannes_template_selector).val();

            if (template == 'template-blank.php') {
                $('#johannes_page_display').fadeOut(300);
            } else {
                $('#johannes_page_display').fadeIn(300);
            }

        }

    });
})(jQuery);