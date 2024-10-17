jQuery(function ($) {
  var selector = null;
  var lightbox = null;

  // Function to sanitize the caption.
  var sanitizeHTML = function (str) {
    return str.replace(/[^\w. ]/gi, function (c) {
      return "&#" + c.charCodeAt(0) + ";";
    });
  };

  // Function to sanitize captions in the DOM elements.
  var sanitizeCaptions = function () {
    $(".ngg-simplelightbox").each(function () {
      var caption = $(this).attr("title");
      if (caption) {
        // Sanitize the caption and update the element attribute.
        var sanitizedCaption = sanitizeHTML(caption);
        $(this).attr("title", sanitizedCaption);
      }
    });
  };

  var nextgen_simplebox_options = {
    history: false,
    animationSlide: false,
    animationSpeed: 100,
    captionSelector: "self",
  };

  var nextgen_simplelightbox_init = function () {
    // Sanitize all captions before initializing the lightbox.
    sanitizeCaptions();

    // Initialize SimpleLightbox.
    selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
    if (selector.length > 0) {
      lightbox = selector.simpleLightbox(nextgen_simplebox_options);
    }
  };

  nextgen_simplelightbox_init();

  $(window).on("refreshed", function () {
    if (lightbox) {
      lightbox.destroy();
    }

    // Sanitize captions again after refresh.
    sanitizeCaptions();

    // Re-initialize SimpleLightbox.
    selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
    if (selector.length > 0) {
      lightbox = selector.simpleLightbox(nextgen_simplebox_options);
    }
  });
});
