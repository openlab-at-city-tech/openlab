jQuery(document).ready(function ($) {
  'use strict';

  $("body").on("change", ".outofthebox-shortcode-value", function () {
    var decoded_shortcode = decodeURIComponent(escape(window.atob($(this).val())));
    $('#outofthebox-shortcode-decoded-value').val(decoded_shortcode).css('display', 'block');
  });

  $("body").on("keyup", "#outofthebox-shortcode-decoded-value", function () {
    var encoded_data = window.btoa(unescape(encodeURIComponent($(this).val())));
    $(".outofthebox-shortcode-value", "body").val(encoded_data)
    $('.outofthebox-shortcode-value').trigger('change');
  });

  var default_value = '[outofthebox class="cf7_upload_box" mode="upload" upload="1" uploadrole="all" upload_auto_start="0" viewrole="all" userfolders="auto" viewuserfoldersrole="none"]';
  var encoded_data = window.btoa(unescape(encodeURIComponent(default_value)));
  $(".outofthebox-shortcode-value", "body").val(encoded_data).trigger('change');

  // Callback function to add shortcode to CF7 input field
  if (typeof window.wpcp_oftb_cf7_add_content === 'undefined') {
    window.wpcp_oftb_cf7_add_content = function (data) {
      var encoded_data = window.btoa(unescape(encodeURIComponent(data)));

      $('.outofthebox-shortcode-value').val(encoded_data);
      $('.outofthebox-shortcode-value').trigger('change');

      if (data.indexOf('userfolders="auto"') > -1) {
        $('.out-of-the-box-upload-folder').fadeIn();
      } else {
        $('.out-of-the-box-upload-folder').fadeOut();
      }

      window.modal_action.close();
    }
  }

  // Modal opening Shortcode Builder
  $("body").on("click", ".OutoftheBox-CF-shortcodegenerator", function () {

    if ($('#outofthebox-modal-action').length > 0) {
      window.modal_action.open();
      return true;
    }

    /* Build the Insert Dialog */
    var modalbuttons = '';
    var modalheader = $('<a tabindex="0" class="close-button" title="" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
    var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" style="display:none"></div>');
    var modalfooter = $('<div class="outofthebox-modal-footer" style="display:none"><div class="outofthebox-modal-buttons">' + '' + '</div></div>');
    var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal outofthebox-modal80 light"><div class="modal-dialog"><div class="modal-content"><div class="loading"><div class="loader-beat"></div></div></div></div></div>');

    $('body').append(modaldialog);

    var $iframe_template = $('#outofthebox-shortcode-iframe');
    var $iframe = $iframe_template.clone().appendTo(modalbody).show();

    $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

    var shortcode = $('#outofthebox-shortcode-decoded-value', 'body').val()
    var shortcode_attr = shortcode.replace('</p>', '').replace('<p>', '').replace('[outofthebox ', '').replace('"]', '');
    var query = encodeURIComponent(shortcode_attr).split('%3D%22').join('=').split('%22%20').join('&');

    $iframe.attr('src', $iframe_template.attr('data-src') + '&' + query);

    $iframe.on('load', function () {
      $('.outofthebox-modal-body').fadeIn();
      $('.outofthebox-modal-footer').fadeIn();
      $('.modal-content .loading:first').fadeOut();
    });

    /* Open the Dialog and load the images inside it */
    var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
      bodyClass: 'rmodal-open',
      dialogOpenClass: 'animated slideInDown',
      dialogCloseClass: 'animated slideOutUp',
      escapeClose: true
    });
    document.addEventListener('keydown', function (ev) {
      modal_action.keydown(ev);
    }, false);
    modal_action.open();
    window.modal_action = modal_action;

  });
})