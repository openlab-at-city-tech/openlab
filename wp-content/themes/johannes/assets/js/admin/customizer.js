(function($) {

    "use strict";

    wp.customize('johannes_settings[header_height]', function(value) {
        value.bind(function(newvalue) {
            $('.header-middle > .container').height(newvalue);
        });
    });

    wp.customize('johannes_settings[header_sticky_height]', function(value) {
        value.bind(function(newvalue) {
            $('.header-sticky-main > .container').height(newvalue);
        });
    });

    // wp.customize.section('johannes_single', function(section) {
    //     section.expanded.bind(function(isExpanded) {
    //         if (isExpanded) {
    //             wp.customize.previewer.previewUrl.set('http://localhost/johannes/?p=192');
    //         }
    //     });
    // });


})(jQuery);