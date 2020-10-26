jQuery(document).ready(function ($) {
  'use strict';

  /* Tabs*/
  $('ul.outofthebox-nav-tabs li:not(".disabled")').click(function () {
    if ($(this).hasClass('disabled')) {
      return false;
    }
    var tab_id = $(this).attr('data-tab');

    $('ul.outofthebox-nav-tabs  li').removeClass('current');
    $('.outofthebox-tab-panel').removeClass('current');

    $(this).addClass('current');
    $("#" + tab_id).addClass('current');
    var hash = location.hash.replace('#', '');
    location.hash = tab_id;
    window.scrollTo(0, 0);
    window.parent.scroll(0, 0);
  });
  if (location.hash && location.hash.indexOf('TB_inline') < 0) {
    jQuery("ul.outofthebox-nav-tabs " + location.hash + "_tab").trigger('click');
  }

  /* Accordions */
  $(".outofthebox-accordion").accordion({
    active: false,
    collapsible: true,
    header: ".outofthebox-accordion-title",
    heightStyle: "content",
    classes: {
      "ui-accordion-header": "outofthebox-accordion-top",
      "ui-accordion-header-collapsed": "outofthebox-accordion-collapsed",
      "ui-accordion-content": "outofthebox-accordion-content"
    },
    icons: {
      "header": "fas fa-angle-down",
      "activeHeader": "fas fa-angle-up"
    }
  });
  $('.outofthebox-accordion .ui-accordion-header span').removeClass('ui-icon ui-accordion-header-icon');

  /* Permissions Tagify Input fields */
  $('.outofthebox-tagify.outofthebox-permissions-placeholders').each(function () {
    var tagify = new Tagify(this, {
      enforceWhitelist: true,
      editTags: false,
      dropdown: {
        enabled: 0,
        highlightFirst: true,
        maxItems: 20
      },
      whitelist: whitelist,
      templates: {
        tag: function (v, tagData) {
          return "<tag title='" + tagData.text + "' contenteditable='false' spellcheck='false' class='tagify__tag'><x class='tagify__tag__removeBtn'></x><div><img onerror='this.style.visibility=\"hidden\"' src='" + tagData.img + "'><span class='tagify__tag-type'>" + tagData.type + "</span><span class='tagify__tag-text'>" + (typeof tagData.text !== 'undefined' ? tagData.text : v) + "</span></div></tag>"

        },
        dropdownItem: function (tagData) {
          return "<div class='tagify__dropdown__item'><img onerror='this.style.visibility=\"hidden\"' src='" + tagData.img + "'><span class='tagify__tag-type'>" + tagData.type + "</span><span>" + tagData.text + "</span></div>"

        }
      }
    });
  });

  /* Media Player selection Box */
  $('#OutoftheBox_mediaplayer_skin_selectionbox').ddslick({
    width: '598px',
    background: '#f4f4f4',
    onSelected: function (item) {
      $("#OutoftheBox_mediaplayer_skin").val($('#OutoftheBox_mediaplayer_skin_selectionbox').data('ddslick').selectedData.value);
    }
  });

  var mode = $('body').attr('data-mode');

  $("input[name=mode]:radio").change(function () {

    $('.forfilebrowser, .foruploadbox, .forgallery, .foraudio, .forvideo, .forsearch').hide();
    $("#OutoftheBox_linkedfolders").trigger('change');

    $('#settings_upload_tab, #settings_advanced_tab, #settings_manipulation_tab, #settings_notifications_tab, #settings_layout_tab, #settings_sorting_tab, #settings_exclusions_tab').removeClass('disabled');
    $('.download-options').show();

    mode = $(this).val();
    switch (mode) {
      case 'files':
        $('.forfilebrowser').not('.hidden').show();
        break;

      case 'upload':
        $('.foruploadbox').not('.hidden').show();
        $('#settings_upload_tab, #settings_notifications_tab').removeClass('disabled');
        $('#settings_sorting_tab, #settings_advanced_tab, #settings_exclusions_tab, #settings_manipulation_tab').addClass('disabled');
        $('.download-options').hide();
        $('#OutoftheBox_upload').prop("checked", true).trigger('change');
        $('#OutoftheBox_notificationdownload, #OutoftheBox_notificationdeletion').closest('.option').hide();
        $('#OutoftheBox_singleaccount').prop("checked", true).trigger('change');
        break;

      case 'gallery':
        $('.forgallery').show();
        $('#OutoftheBox_upload_ext, #OutoftheBox_include_ext').val('gif|jpg|jpeg|png|bmp');
        break;

      case 'search':
        $('.forsearch').not('.hidden').show();
        $('#settings_upload_tab').addClass('disabled');
        $('#OutoftheBox_search_field').prop("checked", true).trigger('change');
        $('#OutoftheBox_singleaccount').prop("checked", true).trigger('change');
        break;

      case 'audio':
        $('.foraudio').show();
        $('.root-folder').show();
        $('#settings_upload_tab, #settings_manipulation_tab, #settings_notifications_tab').addClass('disabled');
        $('#OutoftheBox_singleaccount').prop("checked", true).trigger('change');
        break;

      case 'video':
        $('.forvideo').show();
        $('.root-folder').show();
        $('#settings_upload_tab, #settings_manipulation_tab, #settings_notifications_tab').addClass('disabled');
        $('#OutoftheBox_singleaccount').prop("checked", true).trigger('change');
        break;
    }

    $("#OutoftheBox_breadcrumb, #OutoftheBox_mediapurchase, #OutoftheBox_search, #OutoftheBox_showfiles, #OutoftheBox_slideshow, #OutoftheBox_upload, #OutoftheBox_upload_convert, #OutoftheBox_rename, #OutoftheBox_move, #OutoftheBox_copy, #OutoftheBox_editdescription, #OutoftheBox_delete, #OutoftheBox_createdocument, #OutoftheBox_addfolder").trigger('change');
    $('input[name=OutoftheBox_file_layout]:radio:checked').trigger('change').prop('checked', true);
    $('#OutoftheBox_linkedfolders').trigger('change');
  });

  $("input[name=OutoftheBox_file_layout]:radio").change(function () {
    switch ($(this).val()) {
      case 'grid':
        $('.forlistonly').fadeOut();
        break;
      case 'list':
        $('.forlistonly').fadeIn();
        break;
    }
  });

  $('[data-div-toggle]').change(function () {
    var toggleelement = '.' + $(this).attr('data-div-toggle');

    if ($(this).is(":checkbox")) {
      if ($(this).is(":checked")) {
        $(toggleelement).fadeIn().removeClass('hidden');
      } else {
        $(toggleelement).fadeOut().addClass('hidden');
      }
    } else if ($(this).is("select")) {
      if ($(this).val() === $(this).attr('data-div-toggle-value')) {
        $(toggleelement).fadeIn().removeClass('hidden');
      } else {
        $(toggleelement).fadeOut().addClass('hidden');
      }
    }
  });

  $("#OutoftheBox_linkedfolders").change(function () {
    $('input[name=OutoftheBox_userfolders_method]:radio:checked').trigger('change').prop('checked', true);
  });

  $("input[name=OutoftheBox_userfolders_method]:radio").change(function () {
    var is_checked = $("#OutoftheBox_linkedfolders").is(":checked");

    $('.root-folder').show();
    switch ($(this).val()) {
      case 'manual':
        if (is_checked) {
          $('.root-folder').hide();
        }
        $('.option-userfolders_auto').hide().addClass('hidden');
        break;
      case 'auto':
        $('.root-folder').show();
        $('.option-userfolders_auto').show().removeClass('hidden');
        break;
    }
  });

  $("input[name=sort_field]:radio").change(function () {
    switch ($(this).val()) {
      case 'shuffle':
        $('.option-sort-field').hide();
        break;
      default:
        $('.option-sort-field').show();
        break;
    }
  });

  $('#get_raw_shortcode').click(showRawShortcode);
  $('#do_insert').click(function (event) {
    insertShortcode(event);
  });

  $('.outofthebox .list-container').on('click', '.entry_media_shortcode', function (event) {
    event.stopPropagation();
    insertShortcodeAsMedia($(this).closest('.entry'));
  });

  $(".OutoftheBox img.preloading").unveil(200, $(".OutoftheBox .ajax-filelist"), function () {
    $(this).load(function () {
      $(this).removeClass('preloading');
    });
  });

  /* Initialise from shortcode */
  $('input[name=mode]:radio:checked').trigger('change').prop('checked', true)

  function createShortcode() {

    var dir = $(".root-folder .current-folder-raw").text(),
      account_id = $(".root-folder .OutoftheBox.files").attr('data-account-id'),
      singleaccount = $('#OutoftheBox_singleaccount').prop("checked"),
      custom_class = $('#OutoftheBox_class').val(),
      linkedfolders = $('#OutoftheBox_linkedfolders').prop("checked"),
      show_files = $('#OutoftheBox_showfiles').prop("checked"),
      max_files = $('#OutoftheBox_maxfiles').val(),
      show_folders = $('#OutoftheBox_showfolders').prop("checked"),
      ext = $('#OutoftheBox_ext').val(),
      show_filesize = $('#OutoftheBox_filesize').prop("checked"),
      show_filedate = $('#OutoftheBox_filedate').prop("checked"),
      filelayout = $("input[name=OutoftheBox_file_layout]:radio:checked").val(),
      show_ext = $('#OutoftheBox_showext').prop("checked"),
      candownloadzip = $('#OutoftheBox_candownloadzip').prop("checked"),
      canpopout = $('#OutoftheBox_canpopout').prop("checked"),
      lightbox_navigation = $('#OutoftheBox_lightboxnavigation').prop("checked"),
      showsharelink = $('#OutoftheBox_showsharelink').prop("checked"),
      showrefreshbutton = $('#OutoftheBox_showrefreshbutton').prop("checked"),
      show_breadcrumb = $('#OutoftheBox_breadcrumb').prop("checked"),
      breadcrumb_roottext = $('#OutoftheBox_roottext').val(),
      show_root = $('#OutoftheBox_rootname').prop("checked"),
      search = $('#OutoftheBox_search').prop("checked"),
      search_field = $('#OutoftheBox_search_field').prop("checked"),
      search_from = $('#OutoftheBox_searchfrom').prop("checked"),
      search_term = $('#OutoftheBox_searchterm').val(),
      previewinline = $('#OutoftheBox_previewinline').prop("checked"),
      allow_preview = $('#OutoftheBox_allow_preview').prop("checked"),
      include = $('#OutoftheBox_include').val(),
      exclude = $('#OutoftheBox_exclude').val(),
      show_system_files = $('#OutoftheBox_showsystemfiles').prop("checked"),
      sort_field = $("input[name=sort_field]:radio:checked").val(),
      sort_order = $("input[name=sort_order]:radio:checked").val(),
      crop = $('#OutoftheBox_crop').prop("checked"),
      slideshow = $('#OutoftheBox_slideshow').prop("checked"),
      pausetime = $('#OutoftheBox_pausetime').val(),
      show_filenames = $('#OutoftheBox_showfilenames').prop("checked"),
      show_descriptions_on_top = $('#OutoftheBox_showdescriptionsontop').prop("checked"),
      maximages = $('#OutoftheBox_maximage').val(),
      target_height = $('#OutoftheBox_targetHeight').val(),
      folder_thumbs = $('#OutoftheBox_folderthumbs').prop("checked"),
      max_width = $('#OutoftheBox_max_width').val(),
      max_height = $('#OutoftheBox_max_height').val(),
      upload = $('#OutoftheBox_upload').prop("checked"),
      upload_folder = $('#OutoftheBox_upload_folder').prop("checked"),
      upload_auto_start = $('#OutoftheBox_upload_auto_start').prop("checked"),
      overwrite = $('#OutoftheBox_overwrite').prop("checked"),
      upload_ext = $('#OutoftheBox_upload_ext').val(),
      minfilesize = $('#OutoftheBox_minfilesize').val(),
      maxfilesize = $('#OutoftheBox_maxfilesize').val(),
      maxnumberofuploads = $('#OutoftheBox_maxnumberofuploads').val(),
      rename = $('#OutoftheBox_rename').prop("checked"),
      move = $('#OutoftheBox_move').prop("checked"),
      copy = $('#OutoftheBox_copy').prop("checked"),
      can_delete = $('#OutoftheBox_delete').prop("checked"),
      can_addfolder = $('#OutoftheBox_addfolder').prop("checked"),
      can_createdocument = $('#OutoftheBox_createdocument').prop("checked"),
      deeplink = $('#OutoftheBox_deeplink').prop("checked"),
      notification_download = $('#OutoftheBox_notificationdownload').prop("checked"),
      notification_upload = $('#OutoftheBox_notificationupload').prop("checked"),
      notification_deletion = $('#OutoftheBox_notificationdeletion').prop("checked"),
      notification_emailaddress = $('#OutoftheBox_notification_email').val(),
      notification_skip_email_currentuser = $('#OutoftheBox_notification_skip_email_currentuser').prop("checked"),
      use_template_dir = $('#OutoftheBox_userfolders_template').prop("checked"),
      template_dir = $(".template-folder .OutoftheBox.files .current-folder-raw").text(),
      maxuserfoldersize = $('#OutoftheBox_maxuserfoldersize').val(),
      view_role = readTagify("input[name='OutoftheBox_view_role']"),
      preview_role = readTagify("input[name='OutoftheBox_preview_role']"),
      download_role = readTagify("input[name='OutoftheBox_download_role']"),
      share_role = readTagify("input[name='OutoftheBox_share_role']"),
      upload_role = readTagify("input[name='OutoftheBox_upload_role']"),
      renamefiles_role = readTagify("input[name='OutoftheBox_renamefiles_role']"),
      renamefolders_role = readTagify("input[name='OutoftheBox_renamefolders_role']"),
      move_role = readTagify("input[name='OutoftheBox_move_role']"),
      copy_files_role = readTagify("input[name='OutoftheBox_copy_files_role']"),
      copy_folders_role = readTagify("input[name='OutoftheBox_copy_folders_role']"),
      deletefiles_role = readTagify("input[name='OutoftheBox_deletefiles_role']"),
      deletefolders_role = readTagify("input[name='OutoftheBox_deletefolders_role']"),
      addfolder_role = readTagify("input[name='OutoftheBox_addfolder_role']"),
      createdocument_role = readTagify("input[name='OutoftheBox_createdocument_role']"),
      deeplink_role = readTagify("input[name='OutoftheBox_deeplink_role']"),
      view_user_folders_role = readTagify("input[name='OutoftheBox_view_user_folders_role']"),
      mediaplayer_skin = $('#OutoftheBox_mediaplayer_skin').val(),
      mediaplayer_skin_default = $('#OutoftheBox_mediaplayer_default').val(),
      media_buttons = readCheckBoxes("input[name='OutoftheBox_media_buttons[]']"),
      autoplay = $('#OutoftheBox_autoplay').prop("checked"),
      showplaylist = $('#OutoftheBox_showplaylist').prop("checked"),
      showplaylistonstart = $('#OutoftheBox_showplaylistonstart').prop("checked"),
      playlistinline = $('#OutoftheBox_showplaylistinline').prop("checked"),
      mediafiledate = $('#OutoftheBox_media_filedate').prop("checked"),
      playlistthumbnails = $('#OutoftheBox_playlistthumbnails').prop("checked"),
      linktomedia = $('#OutoftheBox_linktomedia').prop("checked"),
      mediapurchase = $('#OutoftheBox_mediapurchase').prop("checked"),
      linktoshop = $('#OutoftheBox_linktoshop').val(),
      ads = $('#OutoftheBox_media_ads').prop("checked"),
      ads_tag_url = $('#OutoftheBox_media_adstagurl').val(),
      ads_skipable = $('#OutoftheBox_media_ads_skipable').prop("checked"),
      ads_skipable_after = $('#OutoftheBox_media_ads_skipable_after').val();



    var data = '';

    if (OutoftheBox_vars.shortcodeRaw === '1') {
      data += '[raw]';
    }

    data += '[outofthebox ';

    if (custom_class !== '') {
      data += 'class="' + custom_class + '" ';
    }

    if (singleaccount === false) {
      dir = '';
      account_id = '';
      linkedfolders = false;
      data += 'singleaccount="0" ';
    }

    if (typeof dir === 'undefined' && ($("input[name=OutoftheBox_userfolders_method]:radio:checked").val() !== 'manual')) {
      $('#settings_folder_tab a').trigger('click');
      return false;
    }

    if (dir !== '/' && dir !== '') {
      if (linkedfolders) {
        if ($("input[name=OutoftheBox_userfolders_method]:radio:checked").val() !== 'manual') {
          data += 'dir="' + dir + '" ';
        }
      } else {
        data += 'dir="' + dir + '" ';
      }
    }


    if (account_id !== '') {
      if (linkedfolders && $("input[name=OutoftheBox_userfolders_method]:radio:checked").val() === 'manual') {

      } else {
        data += 'account="' + account_id + '" ';
      }
    }

    if (max_width !== '') {
      if (max_width.indexOf("px") !== -1 || max_width.indexOf("%") !== -1) {
        data += 'maxwidth="' + max_width + '" ';
      } else {
        data += 'maxwidth="' + parseInt(max_width) + '" ';
      }
    }

    if (max_height !== '') {
      if (max_height.indexOf("px") !== -1 || max_height.indexOf("%") !== -1) {
        data += 'maxheight="' + max_height + '" ';
      } else {
        data += 'maxheight="' + parseInt(max_height) + '" ';
      }
    }

    data += 'mode="' + $("input[name=mode]:radio:checked").val() + '" ';

    if (ext !== '') {
      data += 'ext="' + ext + '" ';
    }

    if (include !== '') {
      data += 'include="' + include + '" ';
    }
    if (exclude !== '') {
      data += 'exclude="' + exclude + '" ';
    }

    if (show_system_files === true) {
      data += 'showsystemfiles="1" ';
    }

    if (view_role !== 'administrator|editor|author|contributor|subscriber|guest') {
      data += 'viewrole="' + view_role + '" ';
    }

    if (sort_field !== 'name') {
      data += 'sortfield="' + sort_field + '" ';
    }

    if (sort_field !== 'shuffle' && sort_order !== 'asc') {
      data += 'sortorder="' + sort_order + '" ';
    }

    if (linkedfolders === true) {
      var method = $("input[name=OutoftheBox_userfolders_method]:radio:checked").val();
      data += 'userfolders="' + method + '" ';

      if (method === 'auto' && use_template_dir === true && template_dir !== '') {
        data += 'usertemplatedir="' + template_dir + '" ';
      }

      if (view_user_folders_role !== 'administrator') {
        data += 'viewuserfoldersrole="' + view_user_folders_role + '" ';
      }
    }

    if (mode === 'upload') {
      data += 'downloadrole="none" ';
    } else if (download_role !== 'administrator|editor|author|contributor|subscriber|pending|guest') {
      data += 'downloadrole="' + download_role + '" ';
    }


    var mode = $("input[name=mode]:radio:checked").val();
    switch (mode) {
      case 'audio':
      case 'video':

        if (mediaplayer_skin !== mediaplayer_skin_default) {
          data += 'mediaskin="' + mediaplayer_skin + '" ';
        }

        if (media_buttons !== 'prevtrack|playpause|nexttrack|volume|current|duration|fullscreen') {
          if (media_buttons == 'all') {
            media_buttons = 'prevtrack|playpause|nexttrack|volume|current|duration|skipback|jumpforward|speed|shuffle|loop|fullscreen';
          }

          data += 'mediabuttons="' + media_buttons + '" ';
        }

        if (autoplay === true) {
          data += 'autoplay="1" ';
        }

        if (showplaylist === false) {
          data += 'hideplaylist="1" ';
        } else {

          if (showplaylistonstart === false) {
            data += 'showplaylistonstart="0" ';
          }

          if (mode === 'video' && playlistinline === true) {
            data += 'playlistinline="1" ';
          }

          if (mediafiledate === false) {
            data += 'filedate="0" ';
          }

          if (playlistthumbnails === false) {
            data += 'playlistthumbnails="0" ';
          }

          if (linktomedia === true) {
            data += 'linktomedia="1" ';
          }

          if (mediapurchase === true && linktoshop !== '') {
            data += 'linktoshop="' + linktoshop + '" ';
          }
        }

        if (mode === 'video' && ads === true) {
          data += 'ads="1" ';

          if (ads_tag_url !== '') {
            data += 'ads_tag_url="' + ads_tag_url + '" ';
          }

          if (ads_skipable) {
            data += 'ads_skipable="1" ';
          } else if (ads_skipable_after !== '') {
            data += 'ads_skipable_after="' + ads_skipable_after + '" ';
          }
        }

        if (max_files !== '-1' && max_files !== '') {
          data += 'maxfiles="' + max_files + '" ';
        }

        break;

      case 'files':
      case 'gallery':
      case 'upload':
      case 'search':
        if (mode === 'gallery') {

          if (show_filenames === true) {
            data += 'showfilenames="1" ';
          }

          if (show_descriptions_on_top === true) {
            data += 'showdescriptionsontop="1" ';
          }

          if (maximages !== '') {
            data += 'maximages="' + maximages + '" ';
          }

          if (folder_thumbs === false) {
            data += 'folderthumbs="0" ';
          }

          if (target_height !== '') {
            data += 'targetheight="' + target_height + '" ';
          }

          if (slideshow === true) {
            data += 'slideshow="1" ';
            if (pausetime !== '') {
              data += 'pausetime="' + pausetime + '" ';
            }
          }

          if (crop === true) {
            data += 'crop="1" ';
          }

        }

        if (mode === 'files' || mode === 'search') {
          if (show_files === false) {
            data += 'showfiles="0" ';
          }
          if (show_folders === false) {
            data += 'showfolders="0" ';
          }
          if (show_filesize === false) {
            data += 'filesize="0" ';
          }

          if (show_filedate === false) {
            data += 'filedate="0" ';
          }

          if (filelayout === 'grid') {
            data += 'filelayout="grid" ';
          }

          if (show_ext === false) {
            data += 'showext="0" ';
          }

          if (allow_preview === false) {
            data += 'forcedownload="1" ';
          } else if (preview_role !== 'all') {
            data += 'previewrole="' + preview_role + '" ';
          }

          if (canpopout === true) {
            data += 'canpopout="1" ';
          }
        }

        if (max_files !== '-1' && max_files !== '') {
          data += 'maxfiles="' + max_files + '" ';
        }

        if (previewinline === false) {
          data += 'previewinline="0" ';
        }

        if (lightbox_navigation === false) {
          data += 'lightboxnavigation="0" ';
        }

        if (candownloadzip === true) {
          data += 'candownloadzip="1" ';
        }

        if (showsharelink === true) {
          data += 'showsharelink="1" ';

          if (share_role !== 'all') {
            data += 'sharerole="' + share_role + '" ';
          }
        }

        if (deeplink === true) {
          data += 'deeplink="1" ';

          if (deeplink_role !== 'all') {
            data += 'deeplinkrole="' + deeplink_role + '" ';
          }
        }

        if (showrefreshbutton === false) {
          data += 'showrefreshbutton="0" ';
        }

        if (search === false && mode !== 'search') {
          data += 'search="0" ';
        } else {
          if (search_field === true) {
            data += 'searchcontents="1" ';
          }

          if (search_from === true) {
            data += 'searchfrom="selectedroot" ';
          }

          if (search_term !== '' && singleaccount === true) {
            data += 'searchterm="' + search_term + '" ';
          }
        }

        if (show_breadcrumb === true) {
          if (show_root === true) {
            data += 'showroot="1" ';
          }
          if (breadcrumb_roottext !== '') {
            data += 'roottext="' + breadcrumb_roottext + '" ';
          }
        } else {
          data += 'showbreadcrumb="0" ';
        }

        if (notification_download === true || notification_upload === true || notification_deletion === true) {
          if (notification_emailaddress !== '') {
            data += 'notificationemail="' + notification_emailaddress + '" ';
          }

          if (notification_skip_email_currentuser === true) {
            data += 'notification_skipemailcurrentuser="1" ';
          }
        }

        if (notification_download === true) {
          data += 'notificationdownload="1" ';
        }

        if (upload === true) {
          data += 'upload="1" ';

          if (upload_folder === false) {
            data += 'upload_folder="0" ';
          }

          if (upload_auto_start === false) {
            data += 'upload_auto_start="0" ';
          } else {
            data += 'upload_auto_start="1" ';
          }

          if (upload_role !== 'administrator|editor|author|contributor|subscriber') {
            data += 'uploadrole="' + upload_role + '" ';
          }

          if (minfilesize !== '') {
            data += 'minfilesize="' + minfilesize + '" ';
          }

          if (maxfilesize !== '') {
            data += 'maxfilesize="' + maxfilesize + '" ';
          }

          if (maxnumberofuploads !== '-1' && maxnumberofuploads !== '0' && maxnumberofuploads !== '') {
            data += 'maxnumberofuploads="' + maxnumberofuploads + '" ';
          }

          if (overwrite === true) {
            data += 'overwrite="1" ';
          }

          if (upload_ext !== '') {
            data += 'uploadext="' + upload_ext + '" ';
          }

          if (notification_upload === true) {
            data += 'notificationupload="1" ';
          }

          if (maxuserfoldersize !== '-1' && maxuserfoldersize !== '') {
            data += 'maxuserfoldersize="' + maxuserfoldersize + '" ';
          }
        }

        if (rename === true) {
          data += 'rename="1" ';

          if (renamefiles_role !== 'administrator|editor') {
            data += 'renamefilesrole="' + renamefiles_role + '" ';
          }
          if (renamefolders_role !== 'administrator|editor') {
            data += 'renamefoldersrole="' + renamefolders_role + '" ';
          }
        }

        if (move === true) {
          data += 'move="1" ';

          if (move_role !== 'administrator|editor') {
            data += 'moverole="' + move_role + '" ';
          }
        }

        if (copy === true) {
          data += 'copy="1" ';

          if (copy_files_role !== 'administrator|editor') {
            data += 'copyfilesrole="' + copy_files_role + '" ';
          }
          if (copy_folders_role !== 'administrator|editor') {
            data += 'copyfoldersrole="' + copy_folders_role + '" ';
          }
        }


        if (can_delete === true) {
          data += 'delete="1" ';

          if (deletefiles_role !== 'administrator|editor') {
            data += 'deletefilesrole="' + deletefiles_role + '" ';
          }
          if (deletefolders_role !== 'administrator|editor') {
            data += 'deletefoldersrole="' + deletefolders_role + '" ';
          }

          if (notification_deletion === true) {
            data += 'notificationdeletion="1" ';
          }
        }

        if (can_createdocument === true) {
          data += 'createdocument="1" ';

          if (createdocument_role !== 'administrator|editor') {
            data += 'createdocumentrole="' + createdocument_role + '" ';
          }
        }

        if (can_addfolder === true) {
          data += 'addfolder="1" ';

          if (addfolder_role !== 'administrator|editor') {
            data += 'addfolderrole="' + addfolder_role + '" ';
          }
        }



        break;
    }

    data += ']';

    if (OutoftheBox_vars.shortcodeRaw === '1') {
      data += '[/raw]';
    }

    return data;

  }

  function doCallback(value) {
    var callback = $('form').data('callback');
    window.parent[callback](value);
  }

  function insertShortcode(event) {
    var data = createShortcode();
    event.preventDefault();

    if (data !== false) {
      doCallback(data)
    }
  }

  function insertShortcodeAsMedia($entry_element) {

    $("#OutoftheBox_showplaylist").prop("checked", false);
    var file_name = $entry_element.find('.entry_link:first').data('filename');
    $("#OutoftheBox_include").val(file_name);

    var video_extensions = ['mp4', 'm4v', 'ogg', 'ogv', 'webmv'];
    var audio_extensions = ['mp3', 'm4a', 'oga', 'wav', 'webm'];

    video_extensions.forEach(function (extension) {
      if (file_name.indexOf('.' + extension) >= 0) {
        $("input#video").trigger('click');
        return false;
      }
    });

    audio_extensions.forEach(function (extension) {
      if (file_name.indexOf('.' + extension) >= 0) {
        $("input#audio").trigger('click');
        return false;
      }
    });

    var data = createShortcode();

    if (data !== false) {
      doCallback(data)
    }
  }

  function showRawShortcode() {
    /* Close any open modal windows */
    $('#outofthebox-modal-action').remove();
    var shortcode = createShortcode();

    if (shortcode === false) {
      return false;
    }

    /* Build the Shortcode Dialog */
    var modalbuttons = '';
    modalbuttons += '<button class="simple-button blue outofthebox-modal-copy-btn" type="button" title="' + OutoftheBox_vars.str_copy_to_clipboard_title + '" >' + OutoftheBox_vars.str_copy_to_clipboard_title + '</button>';
    var modalheader = $('<a tabindex="0" class="close-button" title="' + OutoftheBox_vars.str_close_title + '" onclick="modal_action.close();"><i class="fas fa-times fa-lg" aria-hidden="true"></i></a></div>');
    var modalbody = $('<div class="outofthebox-modal-body" tabindex="0" style="word-break: break-word;"><strong>' + shortcode + '</strong></div>');
    var modalfooter = $('<div class="outofthebox-modal-footer"><div class="outofthebox-modal-buttons">' + modalbuttons + '</div></div>');
    var modaldialog = $('<div id="outofthebox-modal-action" class="OutoftheBox outofthebox-modal light"><div class="modal-dialog"><div class="modal-content"></div></div></div>');
    $('body').append(modaldialog);
    $('#outofthebox-modal-action .modal-content').append(modalheader, modalbody, modalfooter);

    /* Set the button actions */
    $('#outofthebox-modal-action .outofthebox-modal-copy-btn').unbind('click');
    $('#outofthebox-modal-action .outofthebox-modal-copy-btn').click(function () {

      var $temp = $("<input>");
      $("body").append($temp);
      $temp.val(shortcode).select();
      document.execCommand("copy");
      $temp.remove();

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

    return false;
  }

  function readCheckBoxes(element) {
    var values = $(element + ":checked").map(function () {
      return this.value;
    }).get();


    if (values.length === 0) {
      return "none";
    }

    if (values.length === $(element).length) {
      return "all";
    }

    return values.join('|');
  }

  function readTagify(element) {
    var tags = $(element).val();

    if (tags.length === 0) {
      return "none";
    }

    var tags_id = [];

    $.each(JSON.parse(tags), function (idx, obj) {
      tags_id.push(obj.id)
    });

    if (tags_id.indexOf('none') > -1) {
      return 'none';
    }

    if (tags_id.indexOf('all') > -1) {
      return 'all';
    }

    return tags_id.join('|')
  }

});