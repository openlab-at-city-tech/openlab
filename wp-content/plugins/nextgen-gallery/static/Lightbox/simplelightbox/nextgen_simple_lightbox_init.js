jQuery(function ($) {
  var selector = null;
  var lightbox = null;

  // Whitelist of allowed HTML elements and their allowed attributes.
  var allowedTags = {
    a: ["href", "title", "target", "rel"],
    b: [],
    i: [],
    u: [],
    em: [],
    strong: [],
    p: [],
    br: [],
    span: ["class", "id", "style"],
    img: ["src", "alt", "title"],
    h1: [],
    h2: [],
    h4: [],
    h4: [],
    h5: [],
    h6: [],
    ul: [],
    ol: [],
    li: [],
  };

  // Function to sanitize HTML, allowing only certain tags and attributes.
  var sanitizeHTML = function (str) {
    // Create a temporary DOM element to parse the HTML string.
    var tempDiv = document.createElement("div");
    tempDiv.innerHTML = str;

    // Iterate through all elements.
    var elements = tempDiv.querySelectorAll("*");
    elements.forEach(function (el) {
      var tagName = el.tagName.toLowerCase();

      // If the tag is not allowed, replace the element with its content.
      if (!allowedTags.hasOwnProperty(tagName)) {
        el.replaceWith(el.innerHTML);
        return;
      }

      // If the tag is allowed, check attributes.
      var allowedAttributes = allowedTags[tagName];

      // Loop through each attribute of the element.
      for (var i = el.attributes.length - 1; i >= 0; i--) {
        var attrName = el.attributes[i].name;
        var attrValue = el.attributes[i].value;

        // Remove attributes that are not allowed for this tag.
        if (!allowedAttributes.includes(attrName)) {
          el.removeAttribute(attrName);
        }

        // Additional checks to sanitize certain attributes like href, src.
        if (
          ["href", "src"].includes(attrName) &&
          attrValue.startsWith("javascript:")
        ) {
          el.removeAttribute(attrName); // Remove dangerous URLs.
        }

        // Sanitize the title attribute (if allowed).
        if (attrName === "title") {
          el.setAttribute("title", sanitizeTitle(attrValue)); // Sanitize the title value.
        }
      }
    });

    // Return the sanitized HTML as a string.
    var sanitizedText = tempDiv.innerHTML;
    return sanitizedText.replace(/\\/g, "");
  };

  // Helper function to sanitize the content of the title attribute.
  var sanitizeTitle = function (title) {
    // Replace potential XSS characters in the title.
    return title
      .replace(/</g, "&lt;")
      .replace(/>/g, "&gt;")
      .replace(/"/g, "&quot;");
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
