'use strict';

window.init_out_of_the_box_media_player = function (listtoken) {
  var container = document.querySelector('.media[data-token="' + listtoken + '"]');

  /* Load Playlist via Ajax */
  var data = {
    action: 'outofthebox-get-playlist',
    account_id: container.getAttribute('data-account-id'),
    lastFolder: container.getAttribute('data-id'),
    sort: container.getAttribute('data-sort'),
    listtoken: listtoken,
    _ajax_nonce: OutoftheBox_vars.getplaylist_nonce
  };

  jQuery.ajaxQueue({
    type: "POST",
    url: OutoftheBox_vars.ajax_url,
    data: data,
    success: function (data) {
      var playlist = create_playlistfrom_json(data);
      init_mediaelement(container, listtoken, playlist);
    },
    error: function () {
      container.querySelector('.loading.initialize').style.display = 'none';
      container.querySelector('.wpcp__main-container').classList.add('error');
    },
    dataType: 'json'
  });
}