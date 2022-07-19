// Self-executing function to create and register the TinyMCE plugin
(function (siteurl, $) {
  window.id = "wordpress-post-page";

  tinyMCE.addI18n("en.ngg_attach_to_post", {
    title: "Attach NextGEN Gallery to Post",
  });

  // Create the plugin. We'll register it afterwards
  tinymce.create("tinymce.plugins.NextGEN_AttachToPost", {
    /**
     * The WordPress Site URL
     */
    siteurl: siteurl,

    /**
     * Returns metadata about this plugin
     */
    getInfo: function () {
      return {
        longname: "NextGEN Gallery",
        author: "Imagely",
        authorurl: "https://www.imagely.com",
        infourl:
          "https://www.imagely.com/wordpress-gallery-plugin/nextgen-gallery/",
        version: "0.1",
      };
    },

    /**
     * Initializes the plugin, this will be executed after the plugin has been created.
     */
    init: function (editor, plugin_url) {
      var self = this;

      // TinyMCE 4s events are a bit weird, but this lets us listen to the window-manager close event
      editor.windowManager.nggOldOpen = editor.windowManager.open;
      editor.windowManager.open = function (one, two) {
        var modal = editor.windowManager.nggOldOpen(one, two);
        modal.on("close", self.wm_close_event);
        return modal;
      };

      // Register a new TinyMCE command
      editor.addCommand(
        "ngg_attach_to_post",
        this.render_attach_to_post_interface,
        {
          editor: editor,
          plugin: editor.plugins.NextGEN_AttachToPost,
        }
      );

      // Add a button to trigger the above command
      editor.addButton("NextGEN_AttachToPost", {
        title: "ngg_attach_to_post.title",
        cmd: "ngg_attach_to_post",
        image: plugin_url + "/igw_button.png",
      });

      /**
       * Listen for click events to our placeholder
       */
      editor.on("mouseup touchend", function (e) {
        tinymce.extend(self, {
          editor: editor,
          plugin: editor.plugins.NextGEN_AttachToPost,
        });

        // Support for IGW placeholder images. NGG <= 2.1.50
        if (e.target.tagName === "IMG") {
          if (
            self.get_class_name(e.target).indexOf("ngg_displayed_gallery") >= 0
          ) {
            editor.dom.events.cancel(e);
            var id = e.target.src.match(/\d+$/);
            if (id) id = id.pop();
            self.render_attach_to_post_interface({
              key: "id",
              val: id,
            });
          }
        }

        // Support for IGW Visual Shortcodes. NGG >= 2.1.50.1
        else {
          var $target = $(e.target);

          if ($target.hasClass("nggPlaceholderButton")) {
            // Remove button
            if ($target.hasClass("nggIgwRemove")) {
              var $placeholder = $target.parents(".nggPlaceholder");
              var shortcode = $placeholder[0].getAttribute("data-shortcode");
              editor.fire("ngg-removed", { shortcode: shortcode });
              $placeholder.remove();
            }

            // Edit button
            else {
              // Do not use jQuery's .data() here: it will use cached data
              window.igw_shortcode = $(e.target)
                .parents(".nggPlaceholder")[0]
                .getAttribute("data-shortcode");

              self.render_attach_to_post_interface({
                key: "shortcode",
                val: Base64.encode(window.igw_shortcode),
                ref: $(e.target).parents(".nggPlaceholder").attr("id"),
              });
            }
          }
        }
      });

      /**
       * Find each shortcode and replace it with the placeholder, rendered using an underscore template
       * in templates/tinymce_placeholder.php
       */
      editor.on("BeforeSetContent", function (event) {
        handle_shortcode(event, "[ngg_images ");
        handle_shortcode(event, "[ngg ");
      });

      /**
       * Substitutes the IGW placeholders with the corresponding shortcode
       */
      editor.on("PostProcess", function (event) {
        var $content = $("<div/>").append(event.content);
        $content
          .find(".nggPlaceholder")
          .toArray()
          .forEach(function (placeholder) {
            var $placeholder = $(placeholder);
            var shortcode = $placeholder.data("shortcode");
            shortcode = "[" + _.unescape(shortcode) + "]";
            $placeholder.replaceWith(shortcode);
          });
        event.content = $content[0].innerHTML;
      });

      function handle_shortcode(event, shortcode_opening_tag) {
        while (event.content.indexOf(shortcode_opening_tag) >= 0) {
          var start_of_shortcode = event.content.indexOf(shortcode_opening_tag);
          var index = start_of_shortcode + shortcode_opening_tag.length;
          var found_attribute_assignment = false;
          var current_attribute_enclosure = null;
          var last_found_char = false;
          var content_length = event.content.length;
          while (true) {
            var char = event.content[index];
            if (char == '"' || (char == "'" && last_found_char == "=")) {
              // Is this the closing quote for an already found attribute assignment?
              if (
                found_attribute_assignment &&
                current_attribute_enclosure == char
              ) {
                found_attribute_assignment = false;
                current_attribute_enclosure = null;
              } else {
                found_attribute_assignment = true;
                current_attribute_enclosure = char;
              }
            } else if (char == "]") {
              // we've found a shortcode closing tag. But, we need to ensure
              // that this ] isn't within the value of a shortcode attribute
              if (!found_attribute_assignment) {
                break; //exit loop - we've found the shortcode
              }
            }

            last_found_char = char;

            if (index == content_length) {
              break;
            }

            index++;
          }

          // Replace the shortcode with a placeholder
          var match = event.content.substring(start_of_shortcode, ++index);
          var shortcode = match.substring(1, match.length - 1);
          shortcode = shortcode.replace("[", "&#91;");
          shortcode = shortcode.replace("]", "&#93;");

          var template = _.template($("#ngg-igw-placeholder").html());
          event.content = event.content.replace(
            match,
            template(
              $.extend(ngg_igw_i18n, {
                shortcode: shortcode,
                ref: _.now(),
              })
            )
          );
        }
      }
    },

    get_class_name: function (node) {
      var class_name = node.getAttribute("class")
        ? node.getAttribute("class")
        : node.className;
      if (class_name) {
        return class_name;
      } else {
        return "";
      }
    },

    wm_close_event: function (e) {
      if (
        e &&
        e.target &&
        e.target._id &&
        e.target._id == "ngg_attach_to_post_dialog"
      ) {
        // Restore scrolling for the main content window when the attach to post interface is closed
        $("html,body").css("overflow", "auto");
        tinyMCE.activeEditor.selection.select(
          tinyMCE.activeEditor.dom.select("p")[0]
        );
        tinyMCE.activeEditor.selection.collapse(0);
      }
    },

    /**
     * Renders the attach to post interface
     */
    render_attach_to_post_interface: function (params) {
      // Determine the attach to post url
      var attach_to_post_url = window.igw.url;
      if (typeof params != "undefined") {
        attach_to_post_url +=
          "&" + params.key + "=" + encodeURIComponent(params.val);
        if (typeof params["ref"] != "undefined") {
          attach_to_post_url += "&ref=" + encodeURIComponent(params.ref);
        }
      }
      attach_to_post_url += "&editor=" + this.editor.id;

      var win = window;
      while (win.parent != null && win.parent != win) {
        win = win.parent;
      }

      win = $(win);
      var winWidth = win.width();
      var winHeight = win.height();
      var popupWidth = 1600;
      var popupHeight = 1200;
      var minWidth = 800;
      var minHeight = 600;
      var maxWidth = winWidth - winWidth * 0.05;
      var maxHeight = winHeight - winHeight * 0.1;

      if (maxWidth < minWidth) {
        maxWidth = winWidth - 20;
      }
      if (maxHeight < minHeight) {
        maxHeight = winHeight - 40;
      }
      if (popupWidth > maxWidth) {
        popupWidth = maxWidth;
      }
      if (popupHeight > maxHeight) {
        popupHeight = maxHeight;
      }

      // for mobile devices: dismiss the keyboard by blurring any input with focus
      document.activeElement.blur();
      Array.prototype.forEach.call(
        document.querySelectorAll("input, textarea"),
        function (it) {
          it.blur();
        }
      );

      // Open a window, occupying 90% of the screen real estate
      this.editor.windowManager.open({
        url: attach_to_post_url,
        id: "ngg_attach_to_post_dialog",
        width: popupWidth,
        height: popupHeight,
        title: "NextGEN Gallery - Attach To Post",
      });

      // Ensure that the window cannot be scrolled - XXX actually allow scrolling in the main window and disable it for the inner-windows/frames/elements as to create a single scrollbar
      $("html,body").css("overflow", "hidden");
      $("#ngg_attach_to_post_dialog_ifr").css("overflow-y", "auto");
      $("#ngg_attach_to_post_dialog_ifr").css("overflow-x", "hidden");
    },
  });

  // Register plugin
  tinymce.PluginManager.add(
    "NextGEN_AttachToPost",
    tinymce.plugins.NextGEN_AttachToPost
  );
})(photocrati_ajax.wp_site_url, jQuery);
