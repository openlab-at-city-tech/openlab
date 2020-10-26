jQuery(document).ready(function ($) {
  'use strict';

  $('#do_embed').click(insertDoc);

  function doCallback(value) {
    var callback = $('form').data('callback');
    window.parent[callback](value);
  }

  function insertDoc() {
    var listtoken = $(".OutoftheBox.files").attr('data-token'),
      lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
      entries = readCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']"),
      account_id = $(".OutoftheBox.files").attr('data-account-id');

    if (entries.length === 0) {
      doCallback('')
    }

    $.ajax({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: {
        action: 'outofthebox-embedded',
        account_id: account_id,
        listtoken: listtoken,
        lastpath: lastpath,
        entries: entries,
        _ajax_nonce: OutoftheBox_vars.createlink_nonce
      },
      beforeSend: function () {
        $(".OutoftheBox .loading").height($(".OutoftheBox .ajax-filelist").height());
        $(".OutoftheBox .loading").fadeTo(400, 0.8);
        $(".OutoftheBox .insert_links").attr('disabled', 'disabled');
      },
      complete: function () {
        $(".OutoftheBox .loading").fadeOut(400);
        $(".OutoftheBox .insert_links").removeAttr('disabled');
      },
      success: function (response) {
        if (response !== null) {
          if (response.links !== null && response.links.length > 0) {

            var data = '';

            $.each(response.links, function (key, linkresult) {
              if ($.inArray(linkresult.extension, ['jpg', 'jpeg', 'png', 'gif']) > -1) {
                data += '<img src="' + linkresult.embeddedlink + '" />';
              } else {
                data += '<iframe src="' + linkresult.embeddedlink + '" height="480" style="width:100%;" frameborder="0" scrolling="no" class="oftb-embedded" allowfullscreen></iframe>';
              }
            });

            doCallback(data)
          } else { }
        }
      },
      dataType: 'json'
    });
    return false;
  }

  function readCheckBoxes(element) {
    var values = $(element + ":checked").map(function () {
      return this.value;
    }).get();
    return values;
  }
});