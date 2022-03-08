document.addEventListener("DOMContentLoaded", function(){
    (function($) {
        $('.masonry-view .advgb-recent-posts').isotope({
            itemSelector: '.advgb-recent-post',
            percentPosition: true
        });
        $(window).on('load resize', function(){
            $('.masonry-view .advgb-recent-posts').isotope();
        });
    })(jQuery);
});
