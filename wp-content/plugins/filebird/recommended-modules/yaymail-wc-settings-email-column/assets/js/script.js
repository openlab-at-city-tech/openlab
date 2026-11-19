jQuery(document).ready(function () {
  // Add help tip icon to YayMail column header
  var $yaymailTh = jQuery("th").filter(function () {
    return jQuery(this).text().trim() === "YayMail";
  });
  if ($yaymailTh.length) {
    var $tip = jQuery('<span class="woocommerce-help-tip"></span>').attr(
      "data-tip",
      yaymailWCSettingsEmailColumn.helpText,
    );
    $yaymailTh.append($tip);
    if (jQuery.fn.tipTip) {
      $tip.tipTip({
        attribute: "data-tip",
        fadeIn: 50,
        fadeOut: 50,
        delay: 200,
        keepAlive: true,
      });
    }
  }

  var l10n = yaymailWCSettingsEmailColumn;
  var installing = false; // Blocks reopening the popup while a request is in flight.

  // Run the actual install/activate request (gated behind the confirm modal).
  function runInstall($btn, originalText) {
    installing = true;
    $btn
      .prop("disabled", true)
      .text("Installing…")
      .addClass("updating-message");

    function reset() {
      installing = false;
      $btn
        .prop("disabled", false)
        .text(originalText)
        .removeClass("updating-message");
    }

    jQuery.ajax({
      url: ajaxurl,
      method: "POST",
      data: {
        action: "yaymail_wc_settings_install_activate",
        nonce: l10n.nonce,
      },
      success: function (response) {
        if (response.success) {
          // Leave the button disabled — page navigates away to YayMail.
          window.location.href = l10n.yaymailUrl;
        } else {
          alert(
            response.data && response.data.message
              ? response.data.message
              : "Something went wrong.",
          );
          reset();
        }
      },
      error: function () {
        alert("Request failed. Please try again.");
        reset();
      },
    });
  }

  // Build the body paragraph, linking the first "YayMail" occurrence to the
  // plugin page. Uses text nodes + .attr — no markup injection.
  function buildConfirmBody(text, url) {
    var $p = jQuery("<p></p>");
    var marker = "YayMail";
    var idx = url ? text.indexOf(marker) : -1;
    if (idx === -1) {
      return $p.text(text);
    }
    $p.append(document.createTextNode(text.slice(0, idx)));
    $p.append(
      jQuery("<a></a>")
        .attr({ href: url, target: "_blank", rel: "noopener noreferrer" })
        .text(marker),
    );
    $p.append(document.createTextNode(text.slice(idx + marker.length)));
    return $p;
  }

  // Build & show the confirmation modal; only install after "Install Now".
  function openConfirmModal($btn, originalText) {
    jQuery("#yaymail-confirm-modal").remove(); // never stack modals
    jQuery(document).off("keydown.yaymailConfirm"); // drop any orphaned handler

    var $modal = jQuery(
      '<div id="yaymail-confirm-modal">' +
        '<div class="yaymail-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="yaymail-confirm-heading">' +
          '<button type="button" class="yaymail-confirm-x" aria-label="Close">&times;</button>' +
          '<h3 id="yaymail-confirm-heading"></h3>' +
          '<div class="yaymail-confirm-actions">' +
          '<button type="button" class="button button-primary yaymail-confirm-cancel"></button>' +
          '<button type="button" class="button button-primary yaymail-confirm-install"></button>' +
          "</div>" +
        "</div>" +
      "</div>",
    );

    // Inject localized, translatable strings via .text() (no markup injection).
    $modal.find("h3").text(l10n.confirmHeading);
    buildConfirmBody(l10n.confirmBody, l10n.yaymailPluginUrl).insertAfter(
      $modal.find("h3"),
    );
    $modal.find(".yaymail-confirm-install").text(l10n.confirmInstall);
    $modal.find(".yaymail-confirm-cancel").text(l10n.confirmCancel);

    function closeModal() {
      jQuery(document).off("keydown.yaymailConfirm");
      $modal.remove();
    }

    // Soft close — X / overlay / Esc just dismiss the dialog, nothing saved.
    $modal.find(".yaymail-confirm-x").on("click", closeModal);
    $modal.on("click", function (e) {
      if (!jQuery(e.target).closest(".yaymail-confirm-dialog").length) {
        closeModal();
      }
    });
    jQuery(document).on("keydown.yaymailConfirm", function (e) {
      if (e.key === "Escape" || e.keyCode === 27) {
        closeModal();
      }
    });

    // "No, thanks" — persist the dismissal and remove the column for good.
    $modal.find(".yaymail-confirm-cancel").on("click", function () {
      var $cancel = jQuery(this).prop("disabled", true);
      jQuery.ajax({
        url: ajaxurl,
        method: "POST",
        data: {
          action: "yaymail_wc_settings_dismiss_column",
          nonce: l10n.nonce,
        },
        success: function (response) {
          if (response && response.success) {
            // Remove the whole YayMail column. The header <th> carries the WC
            // column class; the body <td>s are output by our callback, so target
            // them via the install button's own cell.
            jQuery(".wc-email-settings-table-yaymail_cs").remove();
            jQuery(".yaymail-wc-settings-install-yaymail").closest("td").remove();
            closeModal();
          } else {
            $cancel.prop("disabled", false);
            alert(
              response && response.data && response.data.message
                ? response.data.message
                : "Something went wrong.",
            );
          }
        },
        error: function () {
          $cancel.prop("disabled", false);
          alert("Request failed. Please try again.");
        },
      });
    });

    $modal.find(".yaymail-confirm-install").on("click", function () {
      closeModal();
      runInstall($btn, originalText);
    });

    jQuery("body").append($modal);
    $modal.find(".yaymail-confirm-install").trigger("focus");
  }

  // Install / Activate YayMail — now opens a confirmation modal first.
  jQuery(".yaymail-wc-settings-install-yaymail").click(function (e) {
    e.preventDefault();
    if (installing) {
      return; // A request is already running — ignore further clicks.
    }
    var $btn = jQuery(this);
    openConfirmModal($btn, $btn.text().trim());
  });
});
