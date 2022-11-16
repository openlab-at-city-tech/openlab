/**
 * jQuery booklyNavScrollable.
 */
(function ($) {
    $.fn.booklyNavScrollable = function () {
        let $wrap = $('.nav', this),
            $tabs = $('.nav-link', $wrap),
            down = false,
            dragged = false,
            scrollLeft = 0,
            x = 0,
            el = $wrap.get(0);
        setTimeout(function () {
            el.scrollLeft = $tabs.filter('.active').position().left
        }, 0);
        $wrap.mousedown(function (e) {
            down = true;
            dragged = false;
            scrollLeft = this.scrollLeft;
            x = e.clientX;
        });
        $tabs.on('click', function (e) {
            if (dragged) {
                e.stopImmediatePropagation();
                e.preventDefault();
                dragged = false;
            }
        }).on('dragstart', function () {
            return false;
        });
        $('body').mousemove(function (e) {
            if (down) {
                el.scrollLeft = scrollLeft + x - e.clientX;
                if (Math.abs(scrollLeft - el.scrollLeft) > 2) {
                    dragged = true;
                }
            }
        }).mouseup(function () {
            down = false;
        });
    };
})(jQuery);