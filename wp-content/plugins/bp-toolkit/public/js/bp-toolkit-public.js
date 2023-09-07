jQuery(document).ready(function($) {
    'use strict';

    $(document).ready(function () {
        var $modal = $("#bptk-report-modal");
        var $modalOverlay = $("#bptk-report-modal-overlay");
        var $closeButton = $("#bptk-report-close-button");
        var $openButton = $(".bptk-report-button");

        $(document).on('click', '.bptk-report-button', function (e) {
            e.preventDefault();

            if ($(this).hasClass("bptk-report-member-button")) {
                $("#bptk-activity-type").val('member');
                $('#bptk-item-id').val($(this).data('reported'));
            } else if ($(this).hasClass("bptk-report-activity-button")) {
                $("#bptk-activity-type").val('activity');
                var li = $(this).closest('li');
                var item_id = li.attr('id').substr(9, li.attr('id').length);
                $('#bptk-item-id').val(item_id);
            } else if ($(this).hasClass("bptk-report-activity-comment-button")) {
                $("#bptk-activity-type").val('activity-comment');
                var li = $(this).closest('li');
                var item_id = li.attr('id').substr(9, li.attr('id').length);
                $('#bptk-item-id').val(item_id);
            } else if ($(this).hasClass("bptk-report-group-button")) {
                $("#bptk-activity-type").val('group');
                $('#bptk-item-id').val($(this).data('id'));
            } else if ($(this).hasClass("bptk-report-comment-button")) {
                $("#bptk-activity-type").val('comment');
                $('#bptk-item-id').val($(this).data('id'));
            } else if ($(this).hasClass("bptk-report-message-button")) {
                $("#bptk-activity-type").val('message');
                $('#bptk-item-id').val($(this).data('thread'));
            } else if ($(this).hasClass("bptk-report-forum-topic-button")) {
                $("#bptk-activity-type").val('forum-topic');
                // Try and get item ID from the closest article. If not, try and find it in the reply form
                if ($(this).closest('article').length) {
                    var article = $(this).closest('article');
                    var item_id = article.attr('id').substr(5, article.attr('id').length);
                } else if ($('.bbp-reply-form').length) {
                    var article = $('.bbp-reply-form');
                    var item_id = article.attr('id').substr(10, article.attr('id').length);
                }
                $('#bptk-item-id').val(item_id);
            } else if ($(this).hasClass("bptk-report-forum-reply-button")) {
                $("#bptk-activity-type").val('forum-reply');
                if ($(this).closest('.bbp-reply-header').length) {
                    var div = $(this).closest('.bbp-reply-header');
                } else if ($(this).closest('.bs-reply-list-item').length) {
                    var div = $(this).closest('.bs-reply-list-item');
                }
                var item_id = div.attr('id').substr(5, div.attr('id').length);
                $('#bptk-item-id').val(item_id);
            } else if ($(this).hasClass("bptk-report-rtmedia-button")) {
                $("#bptk-activity-type").val('rtmedia');
                $('#bptk-item-id').val($(this).data('media'));
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

        $modalOverlay.click(function (e) {

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

        $closeButton.click(function (e) {
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

        $("#bptk-report-submit").click(function () {

            var $initial = $('#bptk-desc').css('border');

            if ($('#bptk-desc').val().length === 0) {
                $('#bptk-desc').css('border', '1px solid red');
                return false;
            } else {
                $('#bptk-desc').css('border', $initial);
                $("#bptk-report-submit").text('...');
            }

            // Check what component button is for, and find an item ID

            var data = {
                'action': 'process_form',
                'reported': $("#bptk-reported-id").val(),
                'reporter': $("#bptk-reporter-id").val(),
                'nonce': $(this).data('nonce'),
                'activity_type': $("#bptk-activity-type").val(),
                'report_type': $("#bptk-report-type").val(),
                'details': $("#bptk-desc").val(),
                'link': $("#bptk-link").val(),
                'meta': $("#bptk-meta").val(),
                'item_id': $('#bptk-item-id').val()
            };

            $.post(bptk_ajax_settings.ajaxurl, data, function (response) {

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

    $(document).ready(function () {

        if ($('.youzify-group').length) {

            $(".bptk-report-group-button").appendTo(".youzify-usermeta ul");
            $(".bptk-report-group-button").css({
                'display': 'inline-block',
                'cursor': 'pointer'
            });
        } else if ($('.youzify-forum').length) {

            $(".bptk-report-forum-topic-button").appendTo(".youzify-bbp-topic-head-meta");
            $(".bptk-report-forum-topic-button").show();
        } else {

        }
    });

    $(document).ready(function () {
        $(document).on('click', '.bptk-report-button-disabled', function (e) {
            e.preventDefault();
        });
    });

    $(function () {

        $('#bptk-frontend-moderation-panel .button').click(function (e) {
            e.preventDefault();
            process_panel(this);
        });

        function process_panel(obj) {
            var nonce = $('#bptk-frontend-moderation-panel-nonce').val();
            var type = $(obj).attr('id');
            var id = $(obj).data('id');
            var post = $(obj).data('post');
            var activity = $(obj).data('activity');
            $(obj).toggleClass("bptk-button-true");
            var active = $(obj).hasClass("bptk-button-true");

            jQuery.ajax({
                type: "post",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "process_panel",
                    type: type,
                    status: active,
                    id: id,
                    post: post,
                    activity: activity,
                    nonce: nonce
                },
                success: function (msg) {

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }

    });

    $( 'body' ).on(
        'click',
        '.bptk-moderate-button',
        function () {
            unmoderate_activity( this );
            return false;
        }
    );

    function unmoderate_activity( el ) {
        let activity_id = $( el ).attr('data-id');
        let nonce = $( el ).attr('data-nonce');

        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: bptk_ajax_settings.ajaxurl,
            data: {
                activity_id: activity_id,
                nonce: nonce,
                action: 'unmoderate_activity'
            },
            success: function (response) {
                if ( ! response.success ) {
                    console.log( response );
                } else {
                    console.log( response );
                    let activity = $( '#activity-' + response.data.id );
                    $( activity ).removeClass( 'moderated' );
                    $( el ).parent().hide();
                }
            }
        });
    }

    if ( $('.activity-list').length > 0 ) {
        console.log('init');

    }

    $(document).ready(function(){
        if ( $('.activity-list').length > 0 ) {
            console.log('init');

        }

        $('li').each(function() {
            // console.log( $(this) );

        });
    })
});