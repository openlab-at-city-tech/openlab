jQuery(document).ready(function ($) {
  'use strict';

  $('#do_link').click(linkDoc);

  function doCallback(value) {
    var callback = $('form').data('callback');
    window.parent[callback](value);
  }

  function linkDoc() {
    var listtoken = $(".OutoftheBox.files").attr('data-token'),
      lastpath = $(".OutoftheBox[data-token='" + listtoken + "']").attr('data-path'),
      entries = readCheckBoxes(".OutoftheBox[data-token='" + listtoken + "'] input[name='selected-files[]']"),
      account_id = $(".OutoftheBox.files").attr('data-account-id');

    if (entries.length === 0) {
      doCallback('');
    }

    $.ajax({
      type: "POST",
      url: OutoftheBox_vars.ajax_url,
      data: {
        action: 'outofthebox-create-link',
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
              if (linkresult.link === false) {
                data += '<a class="OutoftheBox-directlink" href="#">Plugin does not have permission to create shared link for: ' + linkresult.name + '</a><br/>';
              } else {
                data += '<a class="OutoftheBox-directlink" href="' + linkresult.link + '" target="_blank">' + linkresult.name + '</a><br/>';
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