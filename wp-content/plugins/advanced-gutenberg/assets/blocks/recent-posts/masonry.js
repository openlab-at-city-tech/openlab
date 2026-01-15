document.addEventListener("DOMContentLoaded", function(){
    (function($) {
        // Function to apply isotope
        const applyIsotope = function() {
            $('.masonry-view .advgb-recent-posts').isotope({
                itemSelector: '.advgb-recent-post',
                percentPosition: true
            });
        };

        // Apply isotope on load and resize
        $(window).on('load resize', applyIsotope);

        // Apply isotope when the tab containing the recent posts is clicked
        $('.advgb-tab').on('click', function() {
            // You might need to adjust the selector above if it doesn't match your tabs
            setTimeout(applyIsotope, 0);
        });
    })(jQuery);
});