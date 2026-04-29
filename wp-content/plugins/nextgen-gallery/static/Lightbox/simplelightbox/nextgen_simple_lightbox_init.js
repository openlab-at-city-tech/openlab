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
    h3: [],
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

  var handleTikTokContent = function (element, imageContainer) {
    var playUrl = element.getAttribute("data-tiktok-play-url");
    var shareUrl = element.getAttribute("data-tiktok-share-url");

    if (!playUrl && !shareUrl) {
      imageContainer.classList.remove("sl-tiktok-mode");
      return false;
    }

    imageContainer.classList.add("sl-tiktok-mode");

    var existingContainer = imageContainer.querySelector(".ngg-tiktok-container");
    if (existingContainer) {
      existingContainer.remove();
    }
    var existingError = imageContainer.querySelector(".ngg-tiktok-error");
    if (existingError) {
      existingError.remove();
    }

    var $img = $(imageContainer).find("img");

    NextGEN_TikTok.handle_content({
      playUrl: playUrl,
      shareUrl: shareUrl,
      container: imageContainer,
      width: $img.width(),
      height: $img.height(),
      onBeforeAppend: function () {
        // img is already hidden via CSS .sl-tiktok-mode, but we can be explicit
        $img.hide();
      },
    });

    return true;
  };

  var cleanupTikTokContent = function (imageContainer) {
    imageContainer.classList.remove("sl-tiktok-mode");

    var tiktokContainer = imageContainer.querySelector(".ngg-tiktok-container");
    if (tiktokContainer) {
      tiktokContainer.remove();
    }
    var errorContainer = imageContainer.querySelector(".ngg-tiktok-error");
    if (errorContainer) {
      errorContainer.remove();
    }
  };

  // Get video settings for a gallery
  var getVideoSettings = function(galleryId) {
    if (!window.ngg_video_gallery_settings) {
      return {
        show_video_controls: true,
        show_play_pause_controls: true,
        autoplay_videos: false
      };
    }

    var settings = window.ngg_video_gallery_settings;
    var galleryIdStr = galleryId ? String(galleryId) : null;

    if (galleryIdStr && settings['gallery_' + galleryIdStr]) {
      return settings['gallery_' + galleryIdStr];
    }

    return settings.default || {
      show_video_controls: true,
      show_play_pause_controls: true,
      autoplay_videos: false
    };
  };

  var handleVideoContent = function (element, imageContainer) {
		const videoContainer = document.querySelector(".sl-wrapper")

    var videoUrl = element.getAttribute("data-video-url") || element.getAttribute("href");

    if (!videoUrl) {
      videoContainer.classList.remove("sl-video-mode");
      return false;
    }

    if (!window.NextGEN_Video || !window.NextGEN_Video.detect_platform(videoUrl)) {
      videoContainer.classList.remove("sl-video-mode");
      return false;
    }

    videoContainer.classList.add("sl-video-mode");

    var existingContainer = imageContainer.querySelector(".ngg-video-container");
    if (existingContainer) {
      existingContainer.remove();
    }
    var existingError = imageContainer.querySelector(".ngg-video-error");
    if (existingError) {
      existingError.remove();
    }

    var galleryId = null;
    var $galleryContainer = $(element).closest('[data-gallery-id]');
    if ($galleryContainer.length) {
      galleryId = $galleryContainer.attr('data-gallery-id') || $galleryContainer.data('gallery-id');
    }
    var videoSettings = getVideoSettings(galleryId);

    var $img = $(imageContainer).find("img");

    if (window.NextGEN_Video && window.NextGEN_Video.handle_content) {
      window.NextGEN_Video.handle_content({
        videoUrl: videoUrl,
        container: imageContainer,
        settings: videoSettings,
        containerClass: "ngg-video-container",
        videoClass: "ngg-video-player",
        errorClass: "ngg-video-error",
        onBeforeAppend: function () {
          // img is already hidden via CSS .sl-video-mode, but we can be explicit
          $img.hide();
        },
      });
    }

    return true;
  };

  var cleanupVideoContent = function (imageContainer) {
    imageContainer.classList.remove("sl-video-mode");

    var videoContainer = imageContainer.querySelector(".ngg-video-container");
    if (videoContainer) {
      videoContainer.remove();
    }
    var errorContainer = imageContainer.querySelector(".ngg-video-error");
    if (errorContainer) {
      errorContainer.remove();
    }
  };

  // Factory function to create lightbox content handlers
  // Reduces code duplication between TikTok and video handlers
  var createLightboxHandlers = function (options) {
    var handleContent = options.handleContent;
    var cleanupContent = options.cleanupContent;
    var eventName = options.eventName || "simplelightbox";

    return function (elements) {
      elements.each(function () {
        var el = this;

        // Handle shown event - content is displayed (fires after lightbox opens)
        // Small delay (150ms) ensures the image is fully rendered before processing.
        // This delay accounts for CSS transitions and DOM updates in SimpleLightbox.
        el.addEventListener("shown." + eventName, function () {
          setTimeout(function () {
            var imageContainer = document.querySelector(".sl-image");
            if (imageContainer) {
              handleContent(el, imageContainer);
            }
          }, 150);
        });

        // Handle changed event - navigated to new image
        // Same 150ms delay for consistency with shown event
        el.addEventListener("changed." + eventName, function () {
          setTimeout(function () {
            var imageContainer = document.querySelector(".sl-image");
            if (imageContainer) {
              cleanupContent(imageContainer);
              handleContent(el, imageContainer);
            }
          }, 150);
        });

        // Handle close event - clean up
        el.addEventListener("close." + eventName, function () {
          var imageContainer = document.querySelector(".sl-image");
          if (imageContainer) {
            cleanupContent(imageContainer);
          }
        });
      });
    };
  };

  // Note: SimpleLightbox uses native dispatchEvent with event names like 'shown.simplelightbox'
  // jQuery's .on() interprets dots as namespace separators, so we must use native addEventListener
  var attachTikTokHandlers = createLightboxHandlers({
    handleContent: handleTikTokContent,
    cleanupContent: cleanupTikTokContent,
  });

  // Attach video handlers to lightbox elements
  var attachVideoHandlers = createLightboxHandlers({
    handleContent: handleVideoContent,
    cleanupContent: cleanupVideoContent,
  });

  var nextgen_simplebox_options = {
    history: false,
    animationSlide: false,
    animationSpeed: 100,
    captionSelector: "self",
  };

  var nextgen_simplelightbox_init = function () {
    sanitizeCaptions();

    selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
    if (selector.length > 0) {
      lightbox = selector.simpleLightbox(nextgen_simplebox_options);
      attachTikTokHandlers(selector);
      // Attach video handlers
      attachVideoHandlers(selector);
    }
  };

  nextgen_simplelightbox_init();

  $(window).on("refreshed", function () {
    if (lightbox) {
      lightbox.destroy();
    }

    sanitizeCaptions();

    selector = nextgen_lightbox_filter_selector($, $(".ngg-simplelightbox"));
    if (selector.length > 0) {
      lightbox = selector.simpleLightbox(nextgen_simplebox_options);
      attachTikTokHandlers(selector);
      // Attach video handlers
      attachVideoHandlers(selector);
    }
  });
});
