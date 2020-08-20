jQuery(function ($) {

  if ($('input.outofthebox_private_folder_name').length > 0) {
    var gf_block_upload = $('<div>').attr('id', 'gf_block_upload').css({position: 'absolute', top: 0, bottom: 0, left: 0, right: 0, 'z-index': 1000, background: 'rgba(255, 255, 255, 0.49)'});
    $('#OutoftheBox').css('position', 'relative').append(gf_block_upload);
  }

  $('input.outofthebox_private_folder_name').keyup(function () {

    var name = '';
    $('input.outofthebox_private_folder_name').each(function () {
      name += $(this).val() + '|';
    });
    name = (name.length > 0) ? name.slice(0, -1) : name;

    if (name.length > 2) {
      gf_block_upload.fadeOut();
      document.cookie = 'OftB-CF7-NAME=' + name + '; path=/';
    } else {
      gf_block_upload.fadeIn();
    }

  });

  var prefill = getCookie('OftB-CF7-NAME');
  if (prefill.length > 0) {
    var input = prefill.split('|');

    $(input).each(function (i, value) {
      $('input.outofthebox_private_folder_name:eq(' + i + ')').val(value);
    });
  }

  $('input.outofthebox_private_folder_name').trigger('keyup');

  $('.OutoftheBox').on('outofthebox-addupload outofthebox-removeupload', ".fileuploadform", function () {

    if ($(this).closest('.wpcf7-validates-as-required').length === 0) {
      return;
    }

    if ($(this).closest('.OutoftheBox').data('cpOutoftheBox').number_of_uploaded_files.Counter > 0) {
      $(this).closest('.wpcf7').find('#wpcp_form_submit').prop("disabled", false).fadeTo(400, 1);
    } else {
      $(this).closest('.wpcf7').find('#wpcp_form_submit').prop("disabled", true).fadeTo(400, 0.3);
    }
  });
  $('.OutoftheBox .fileuploadform').trigger('outofthebox-addupload');

  function getCookie(cname) {
    var name = cname + '=';
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == ' ') {
        c = c.substring(1);
      }
      if (c.indexOf(name) == 0) {
        return c.substring(name.length, c.length);
      }
    }
    return '';
  }
});

jQuery(document).ready(function ($) {
  'use strict';

  $("body").on("change", ".outofthebox-shortcode-value", function () {
    var decoded_shortcode = decodeURIComponent(escape(window.atob($(this).val())));
    $('#outofthebox-shortcode-decoded-value').text(decoded_shortcode).css('display', 'block');
  });

  var default_value = '[outofthebox class="cf7_upload_box" mode="upload" upload="1" uploadrole="all" viewrole="all" userfolders="auto" viewuserfoldersrole="none"';
  var encoded_data = window.btoa(unescape(encodeURIComponent(default_value)));
  $(".outofthebox-shortcode-value", "body").val(encoded_data).trigger('change');

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
    var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox outofthebox-modal outofthebox-modal80 light"><div class="modal-dialog"><div class="modal-content"><div class="loading"><div class="loader-beat"></div></div></div></div></div>');

    $('body').append(modaldialog);

    var $iframe_template = $('#outofthebox-shortcode-iframe');
    var $iframe = $iframe_template.clone().appendTo(modalbody).show();

    $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

    var shortcode = $('#outofthebox-shortcode-decoded-value', 'body').text()
    var shortcode_attr = shortcode.replace('</p>', '').replace('<p>', '').replace('[outofthebox ', '').replace('"]', '');
    var query = encodeURIComponent(shortcode_attr).split('%3D%22').join('=').split('%22%20').join('&');

    $iframe.attr('src', $iframe_template.attr('data-src') + '&' + query);

    $iframe.load(function () {
      $('.outofthebox-modal-body').fadeIn();
      $('.outofthebox-modal-footer').fadeIn();
      $('.modal-content .loading:first').fadeOut();
    });

    /* Open the Dialog and load the images inside it */
    var modal_action = new RModal(document.getElementById('outofthebox-modal-action'), {
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
});