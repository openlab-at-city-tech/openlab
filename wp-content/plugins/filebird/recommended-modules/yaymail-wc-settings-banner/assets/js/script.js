jQuery(document).ready(function () {
  var cfg = yaymailWcSettingsBanner;

  function buildBannerHtml() {
    var btnText = cfg.is_installed ? cfg.i18n.btnActivate : cfg.i18n.btnInstall;
    return (
      '<div id="yaymail-banner" class="notice notice-info is-dismissible yaymail-marketplace-banner">' +
      '<button type="button" class="notice-dismiss" id="yaymail-banner-noti-dismiss">' +
      '<span class="screen-reader-text">' +
      cfg.i18n.dismiss +
      "</span>" +
      "</button>" +
      '<div class="yaymail-banner-wrapper">' +
      '<div class="yaymail-banner-content">' +
      "<h3>" +
      cfg.i18n.title +
      "</h3>" +
      "<p>" +
      cfg.i18n.desc +
      "</p>" +
      '<p class="yaymail-banner-actions">' +
      '<button type="button" class="button button-primary yaymail-banner-install-yaymail">' +
      btnText +
      "</button>" +
      '<a href="javascript:;" id="yaymail-banner-dismiss" class="yaymail-banner-inline-dismiss">' +
      cfg.i18n.dismiss +
      "</a>" +
      "</p>" +
      "</div>" +
      '<div class="yaymail-banner-image">' +
      '<img src="' +
      cfg.imageUrl +
      '" alt="' +
      cfg.i18n.imgAlt +
      '" />' +
      "</div>" +
      "</div>" +
      "</div>" +
      "<style>" +
      ".yaymail-marketplace-banner { margin-bottom:24px; }" +
      "#yaymail-banner .yaymail-banner-wrapper { display:flex; flex-wrap:wrap; align-items:center; justify-content:space-between; gap:20px; padding:12px 0; }" +
      "#yaymail-banner .yaymail-banner-content { flex:1; }" +
      "#yaymail-banner .yaymail-banner-content h3 { margin:0 0 8px; font-size:14px; }" +
      "#yaymail-banner .yaymail-banner-content p { margin:0 0 12px; }" +
      "#yaymail-banner .yaymail-banner-actions { margin:0 !important; }" +
      "#yaymail-banner .yaymail-banner-inline-dismiss { margin-left:10px; text-decoration:none; }" +
      "#yaymail-banner .yaymail-banner-image { flex-shrink:0; margin-right:30px; }" +
      "#yaymail-banner .yaymail-banner-image img { display:block; width:auto; height:120px; }" +
      "</style>"
    );
  }

  function dismissBanner(days) {
    jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      data: {
        action: "yaymail_banner_dismiss",
        nonce: cfg.nonce,
        days: days,
        type: "noti",
      },
      complete: function () {
        jQuery("#yaymail-banner").hide("slow");
      },
    });
  }

  function bindEvents() {
    // Direct binding — banner is always in DOM when bindEvents() is called
    jQuery("#yaymail-banner .yaymail-banner-inline-dismiss").on(
      "click",
      function (e) {
        e.preventDefault();
        dismissBanner(30);
      },
    );

    jQuery("#yaymail-banner .notice-dismiss").on("click", function (e) {
      e.preventDefault();
      dismissBanner(7);
    });

    jQuery(".yaymail-banner-install-yaymail").on("click", function (e) {
      e.preventDefault();
      var $btn = jQuery(this);
      var originalText = $btn.text().trim();
      $btn
        .prop("disabled", true)
        .text("Installing…")
        .addClass("updating-message");

      jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        data: { action: "yaymail_banner_install_activate", nonce: cfg.nonce },
        success: function (response) {
          if (response.success) {
            window.location.href = cfg.yaymailUrl;
          } else {
            alert(
              response.data && response.data.message
                ? response.data.message
                : "Something went wrong.",
            );
            $btn
              .prop("disabled", false)
              .text(originalText)
              .removeClass("updating-message");
          }
        },
        error: function () {
          alert("Request failed. Please try again.");
          $btn
            .prop("disabled", false)
            .text(originalText)
            .removeClass("updating-message");
        },
      });
    });
  }

  if (cfg.is_marketplace) {
    // .woocommerce-marketplace__content is React-rendered; observe until it appears
    var observer = new MutationObserver(function (mutations, obs) {
      var $container = jQuery(".woocommerce-marketplace__content");
      if ($container.length && !jQuery("#yaymail-banner").length) {
        obs.disconnect();
        $container.prepend(buildBannerHtml());
        bindEvents();
      }
    });
    observer.observe(document.body, { childList: true, subtree: true });
  } else {
    // Admin-notice banner already in the DOM via PHP
    bindEvents();
  }
});
