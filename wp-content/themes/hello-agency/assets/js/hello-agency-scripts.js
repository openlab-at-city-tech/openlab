(function ($) {
  $("#hello-agency-content").click(function () {
    $("html, body").animate(
      {
        scrollTop: $($.attr(this, "href")).offset().top,
      },
      500
    );
    return false;
  });

  if ($("#hello-agency-tab-1").length > 0) {
    $(".wp-block-heading.hello-agency-tab-one").addClass("active");

    function isDivInViewport($div) {
      var viewportTop = $(window).scrollTop();
      var viewportBottom = viewportTop + $(window).height();
      var divTop = $div.offset().top;
      var divBottom = divTop + $div.outerHeight();
      return divBottom >= viewportTop && divTop <= viewportBottom;
    }

    // Check if the div with the id "myDiv" is in the viewport
    var $haCurrentTab = $("#hello-agency-tab-1");
    $(window).on("scroll", function () {
      if (isDivInViewport($haCurrentTab)) {
        $(".wp-block-heading.hello-agency-tab-one").addClass("active");
      } else {
        $(".wp-block-heading.hello-agency-tab-one").removeClass("active");
      }
    });
    var $haCurrentTab2 = $("#hello-agency-tab-2");
    $(window).on("scroll", function () {
      if (isDivInViewport($haCurrentTab2)) {
        $(".wp-block-heading.hello-agency-tab-two").addClass("active");
      } else {
        $(".wp-block-heading.hello-agency-tab-two").removeClass("active");
      }
    });
    var $haCurrentTab3 = $("#hello-agency-tab-3");
    $(window).on("scroll", function () {
      if (isDivInViewport($haCurrentTab3)) {
        $(".wp-block-heading.hello-agency-tab-three").addClass("active");
      } else {
        $(".wp-block-heading.hello-agency-tab-three").removeClass("active");
      }
    });
    var $haCurrentTab4 = $("#hello-agency-tab-4");
    $(window).on("scroll", function () {
      if (isDivInViewport($haCurrentTab4)) {
        $(".wp-block-heading.hello-agency-tab-four").addClass("active");
      } else {
        $(".wp-block-heading.hello-agency-tab-four").removeClass("active");
      }
    });
  }

  $(document).ready(function () {
    function isElementPartiallyInViewport(element) {
      var rect = element.getBoundingClientRect();
      var windowHeight = window.innerHeight || document.documentElement.clientHeight;
      return rect.bottom >= 0 && rect.top <= windowHeight;
    }
    function onScroll() {
      var elements = $(".hello-agency-animate");
      elements.each(function () {
        if (isElementPartiallyInViewport(this) && !$(this).hasClass("hello-agency-fadeinup")) {
          $(this).addClass("hello-agency-fadeinup");
        }
      });
    }
    // Bind the scroll event
    $(window).scroll(onScroll);
    onScroll();
  });
})(jQuery);
