(function($) {
  'use strict';

  $(document).ready(function() {
    var $modal = $("#bptk-report-modal");
    var $modalOverlay = $("#bptk-report-modal-overlay");
    var $closeButton = $("#bptk-report-close-button");
    var $openButton = $(".bptk-report-button");

    $(document).on('click', '.bptk-report-button', function(e) {
      e.preventDefault();

      if ($(this).hasClass("bptk-report-member-button")) {
        $("#bptk-activity-type").val('member');
      } else if ($(this).hasClass("bptk-report-activity-button")) {
        $("#bptk-activity-type").val('activity');
      } else if ($(this).hasClass("bptk-report-activity-comment-button")) {
        $("#bptk-activity-type").val('activity-comment');
      } else if ($(this).hasClass("bptk-report-group-button")) {
        $("#bptk-activity-type").val('group');
      } else if ($(this).hasClass("bptk-report-message-button")) {
        $("#bptk-activity-type").val('message');
      } else if ($(this).hasClass("bptk-report-forum-topic-button")) {
        $("#bptk-activity-type").val('forum-topic');
      } else if ($(this).hasClass("bptk-report-forum-reply-button")) {
        $("#bptk-activity-type").val('forum-reply');
      } else if ($(this).hasClass("bptk-report-rtmedia-button")) {
        $("#bptk-activity-type").val('rtmedia');
      }

      $("#bptk-reported-id").val($(this).data('reported'));
      $("#bptk-link").val($(this).data('link'));
      $("#bptk-meta").val($(this).data('meta'));

      // if rtMedia detected, close popup to prevent inability to type in report box.
      if ($('.mfp-wrap').length) {
        var magnificPopup = $.magnificPopup.instance;
        // save instance in magnificPopup variable
        magnificPopup.close();
        // Close popup that is currently opened
      }

      $modal.toggleClass("bptk-report-closed", "new");
      $modalOverlay.toggleClass("bptk-report-closed", "new");
    });

    $modalOverlay.click(function(e) {
      e.preventDefault();

      $modal.toggleClass("bptk-report-closed");
      $modalOverlay.toggleClass("bptk-report-closed");
    });

    $closeButton.click(function(e) {
      e.preventDefault();

      $modal.toggleClass("bptk-report-closed");
      $modalOverlay.toggleClass("bptk-report-closed");

      $("#bptk-reported-id").val('');
      $("#bptk-activity-type").val('');
      $("#bptk-report-type").val(-1).change();
      $("#bptk-desc").val('');
      $("#bptk-link").val('');
      $("#bptk-meta").val('');
      $('#bptk-report-modal-response').hide();
      $("#bptk-report-submit").show();
      $("#bptk-report-submit").text('Send');

    });

    $("#bptk-report-submit").click(function() {

      var $initial = $('#bptk-desc').css('border');

      if ($('#bptk-desc').val().length === 0) {
        $('#bptk-desc').css('border', '1px solid red');
        return false;
      } else {
        $('#bptk-desc').css('border', $initial);
        $("#bptk-report-submit").text('...');
      }

      var data = {
        'action': 'process_form',
        'reported': $("#bptk-reported-id").val(),
        'reporter': $("#bptk-reporter-id").val(),
        'nonce': $(this).data('nonce'),
        'activity_type': $("#bptk-activity-type").val(),
        'report_type': $("#bptk-report-type").val(),
        'details': $("#bptk-desc").val(),
        'link': $("#bptk-link").val(),
        'meta': $("#bptk-meta").val()
      };

      $.post(settings.ajaxurl, data, function(response) {
        console.log('processed');

        if (response.success == true) {

          $("#bptk-report-submit").hide();
          $('#bptk-report-modal-response').show();
          $('#bptk-report-modal-response').text(response.data);

        } else {

          $("#bptk-report-submit").hide();
          $('#bptk-report-modal-response').show();
          $('#bptk-report-modal-response').text(response.error);
        }

      });
    });
  });

  $(document).ready(function() {

    if ($('.yz-profile').length) {

      // No longer required since last update

      // $(".bptk-report - member - button ").appendTo(".yz - usermeta ul ");
      // $(".bptk-report-member-button").show();
      // $(".bptk-block-profile").appendTo(".yz-usermeta ul");
      // $(".bptk-block-profile").show();
      // $(".bptk-suspend-profile").appendTo(".yz-usermeta ul");
      // $(".bptk-suspend-profile").show();
    } else if ($('.yz-group').length) {

      $(".bptk-report-group-button").appendTo(".yz-usermeta ul");
      $(".bptk-report-group-button").show();
    } else if ($('.yz-forum').length) {

      $(".bptk-report-forum-topic-button").appendTo(".yz-bbp-topic-head-meta");
      $(".bptk-report-forum-topic-button").show();
    } else {
      return;
    }


  });
})(jQuery);