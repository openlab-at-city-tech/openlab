(function ($) {

    "use strict";
    /*------------------------------------------
        = BACK TO TOP BTN SETTING
    -------------------------------------------*/

    $("body").append("<a href='#' class='back-to-top'>&#8593;</a>");

    function toggleBackToTopBtn() {
        var amountScrolled = 1000;
        if ($(window).scrollTop() > amountScrolled) {
            $("a.back-to-top").fadeIn("slow");
        } else {
            $("a.back-to-top").fadeOut("slow");
        }
    }

    $(".back-to-top").on("click", function () {
        $("html,body").animate({
            scrollTop: 0
        }, 700);
        return false;
    })


    /*==========================================================================
     WHEN WINDOW SCROLL
     ==========================================================================*/
    $(window).on("scroll", function () {

        if ($(".site-header").length) {
            stickyMenu($('.site-header .navigation'), "sticky-on");
        }

        toggleBackToTopBtn();
    });

    new WOW().init();
})(window.jQuery);
