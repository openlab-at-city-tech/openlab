/**
 * UF Health Require Image Alt Tags
 * https://ufhealth.org/
 *
 * Copyright (c) 2017 UF Health
 * Licensed under the GPLv2+ license.
 */
jQuery(document).ready(function($) {
  'use strict';

  /**
   * Checks the media form for proper ALT text
   *
   * @since 1.0
   *
   * @param showNotice
   *
   * @returns {boolean}
   */
  var checkForAlt = function(showNotice) {

    var notice = ('undefined' !== typeof showNotice) ? showNotice : false,
        $parent = $('.media-frame-toolbar .media-toolbar-primary'),
        selectedImages = $('.media-frame-content ul.attachments li[aria-checked="true"]'),
        canProceed = true,
        badImages = [];

    // Clear all the marked ones first.
    $('.ufh-needs-alt-text').each(function(idx, li) {
      $(li).removeClass('ufh-needs-alt-text');
    });

    if (0 === selectedImages.length) { // This is seen in some modals.

      var $image = $('.attachment-details').attr('data-id'),
          altText;

      // Handle image uploads if there is a multi-select box (normal image
      // insertion).
      if ('undefined' !== typeof $image) {

        var image = wp.media.model.Attachment.get($image);

        altText = image.get('alt');

      }
      else { // Handle featured image, replace image, etc.

        // Different forms have different markup so attempt to address
        // accordingly.
        var hasLabel = $(
            '.media-modal-content label[data-setting="alt"] input'),
            noLabel = $('.media-frame-content input[data-setting="alt"]');

        if (hasLabel.length && 0 < hasLabel.length) {

          altText = hasLabel.val();

        }
        else {

          altText = noLabel.val();

        }
      }

      // If we don't have an alt text field or don't even have a media form
      // we're OK.
      if (0 === $('.media-sidebar.visible').length ||
          (altText.length && 0 < altText.length)) {

        $parent.addClass('ufh-has-alt-text');

        return true;

      }

      // Remove the mask that allows the button to be pushed.
      $parent.removeClass('ufh-has-alt-text');

      if (notice) {
        /* jshint ignore:start */
        alert(ufhTagsCopy.editTxt);
        /* jshint ignore:end */
      }

      return false;

    }
    else { // We've selected one or more in a normal box.

      selectedImages.each(function(idx, li) {

        var $image = $(li),
            imageId = $image.attr('data-id'),
            image = wp.media.model.Attachment.get(imageId),
            altText = image.get('alt');

        if ('undefined' !== typeof imageId) { // It's not actually an image or even an uploaded item.

          if (altText.length || 'image' !== image.get('type')) { //looks like we're OK on this one.

            $parent.addClass('ufh-has-alt-text');
            $image.removeClass('ufh-needs-alt-text');

          }
          else { // Mark it 0 dude.

            $image.addClass('ufh-needs-alt-text');

            badImages.push(image.get('title'));

            canProceed = false;

          }
        }
      });

      if (false === canProceed) {

        $parent.removeClass('ufh-has-alt-text');

        if (notice) {

          var imageList = '\n\n';

          for (var i = 0, l = badImages.length; i < l; i++) {
            imageList = imageList + badImages[i] + '\n\n';
          }
          /* jshint ignore:start */
          alert(ufhTagsCopy.disclaimer + '\n\n' + ufhTagsCopy.txt + ':' + imageList);
          /* jshint ignore:end */

        }

        return false;

      }

      return true;

    }
  };

  var body = $('body');

  // Bind to keyup.
  body.on('keyup',
      '.media-modal-content label[data-setting="alt"] input, .media-frame-content input[data-setting="alt"]',
      function() {
        checkForAlt();
      });

  // Bind to the 'Insert into post' button.
  body.on('mouseenter mouseleave click',
      '.media-frame-toolbar .media-toolbar-primary', function(e) {
        checkForAlt(e.type === 'click');
      });

});