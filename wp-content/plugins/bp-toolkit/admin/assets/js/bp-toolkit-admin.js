(function ($) {
    'use strict';

    $(function () {

        $('.bp-toolkit-rating-link').on('click', function () {
            $(this).parent().text($(this).data("rated"));
        });

        $('.bptk-help-tip').tipso({
            content: $('.bptk-help-tip').data('tip'),
            background: '#cc3333',
        });

    });

    $(function () {

        if ($('.bptk-box .bptk-field-wrap select').length > 0) {
            $('.bptk-box select').select2({
                placeholder: "Select an option",
                width: 'element'
            });
        }

    });

    $(function () {

        if ($('input[name="report_section[bptk_report_blacklist]').length > 0) {

            var input = document.querySelector('input[name="report_section[bptk_report_blacklist]"]');
            var tagify = new Tagify(input, {
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
            });

            input.addEventListener('change', onChange);

            function onChange(e) {
                // outputs a String
                console.log(e.target.value)
            }

        }
    });

    $(function () {
        $('.bptk-report-row').click(function () {
            ToggleRead(this);
        });

        function ToggleRead(obj) {
            console.log('clicked');
            console.log(obj);
            var report_id = $(obj).data('report');
            var nonce = $(obj).data('nonce');
            console.log(report_id);
            var title = jQuery("#post-" + report_id);
            var unread = title.hasClass("report-unread");
            console.log(unread);

            $("#mark_read_" + report_id).css("display", unread ? "none" : "inline");
            $("#mark_unread_" + report_id).css("display", unread ? "inline" : "none");
            // // jQuery("#is_unread_" + entry_id).css("display", marking_read ? "inline" : "none");
            title.toggleClass("report-unread");

            var newStatus = title.hasClass("report-unread");
            console.log(newStatus);

            UpdateCount("unread_count", unread ? -1 : 1);
            UpdateMenuCount("bptk-unread-count", unread ? -1 : 1);
            jQuery.ajax({
                type: "post",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "toggle_read",
                    report_id: report_id,
                    status: newStatus,
                    nonce: nonce
                },
                success: function (msg) {
                    console.log(msg);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }

        function UpdateCount(element_id, change) {
            var element = jQuery("#" + element_id);
            var count = parseInt(element.html(), 10) + change;
            if (count < 0) {
                return;
            }
            element.html(count + "");
        }

        function UpdateMenuCount(element_id, change) {
            var element = jQuery("." + element_id);
            var count = parseInt(element.html(), 10) + change;
            if (count < 0) {
                return;
            }
            element.html(count + "");
        }

    });

    $(function () {

        if ($('#bptk-toggle-uphold ').hasClass("bptk-report-upheld")) {
            $('#bptk-toggle-uphold').text('Remove Upheld Status');
        } else {
            $('#bptk-toggle-uphold').text('Mark Report as Upheld');
        }

        $('#bptk-toggle-uphold').click(function (e) {
            e.preventDefault();
            ToggleUphold(this);
        });

        function ToggleUphold(obj) {
            var nonce = $('#bptk-moderation-metabox-nonce').val();
            var id = $(obj).data('id');
            $(obj).toggleClass("bptk-report-upheld");
            var upheld = $(obj).hasClass("bptk-report-upheld");

            if (upheld == true) {
                $(obj).text('Remove Upheld Status');
            } else {
                $(obj).text('Mark Report as Upheld');
            }

            jQuery.ajax({
                type: "post",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "toggle_uphold",
                    status: upheld,
                    id: id,
                    nonce: nonce
                },
                success: function (msg) {
                    console.log(msg);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }

    });

    $(function () {

        if ($('#bptk-toggle-suspension').hasClass("bptk-member-suspended")) {
            $('#bptk-toggle-suspension').text('Unsuspend Reported User Now');
        } else {
            $('#bptk-toggle-suspension').text('Suspend Reported User Now');
        }

        $('#bptk-toggle-suspension').click(function (e) {
            e.preventDefault();
            ToggleSuspension(this);
        });

        function ToggleSuspension(obj) {
            var nonce = $('#bptk-moderation-metabox-nonce').val();
            var id = $(obj).data('id');
            $(obj).toggleClass("bptk-member-suspended");
            var suspended = $(obj).hasClass("bptk-member-suspended");

            if (suspended == true) {
                $(obj).text('Unsuspend Reported User Now');
            } else {
                $(obj).text('Suspend Reported User Now');
            }

            jQuery.ajax({
                type: "post",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "toggle_suspension",
                    status: suspended,
                    id: id,
                    nonce: nonce
                },
                success: function (msg) {
                    console.log(msg);

                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }

    });

    $(function () {

        if ($('#bptk-toggle-moderation').hasClass("bptk-item-moderated")) {
            $('#bptk-toggle-moderation').text('Unmoderate');
        } else {
            $('#bptk-toggle-moderation').text('Immediately Moderate');
        }

        $('#bptk-toggle-moderation').click(function (e) {
            e.preventDefault();
            ToggleModeration(this);
        });

        function ToggleModeration(obj) {
            var nonce = $('#bptk-moderation-metabox-nonce').val();
            var id = $(obj).data('id');
            var post = $(obj).data('post');
            var activity = $(obj).data('activity');
            $(obj).toggleClass("bptk-item-moderated");
            var moderated = $(obj).hasClass("bptk-item-moderated");

            if (moderated == true) {
                $(obj).text('Unmoderate');
            } else {
                $(obj).text('Immediately Moderate');
            }

            jQuery.ajax({
                type: "post",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "toggle_moderation",
                    status: moderated,
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

    $(function () {

        $('#bptk-quick-moderate').submit(ajaxSubmit);

        function ajaxSubmit() {
            var item_id = $('input[name="item_id"]').val();
            var nonce = $('input[name="quick_moderate_nonce"]').val();
            var activity = $('select[name="activity_type"]').val();
            var QuickModerate = $(this).serialize();
            jQuery.ajax({
                type: "POST",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: 'quick_moderate',
                    item_id: item_id,
                    activity: activity,
                    nonce: nonce,
                },
                success: function (response) {
                    // You can craft something here to handle the message return
                    $('#bptk-quick-moderate-message').text(response);
                    $('#bptk-quick-moderate-message').css('color', 'green');
                },
                fail: function (err) {
                    // You can craft something here to handle an error if something goes wrong when doing the AJAX request.
                    console.log(err);
                }
            });
            return false;
        }
    });

    $(function () {

        $('.bptk-report-settings-ajax-button').click(function (e) {
            e.preventDefault();
        });
    });


    $(function () {

        $('#bptk-report-reset-button').click(function (e) {
            ResetModeration(this);
        });

        function ResetModeration(obj) {

            var nonce = $(obj).data('nonce');
            var originalText = $(obj).text();
            var originalColor = $(obj).css('background-color');

            jQuery.ajax({
                type: "POST",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "reset_moderated__premium_only",
                    nonce: nonce
                },
                success: function (response) {
                    $(obj).text(response);
                    $(obj).css('background-color', 'green');
                    setTimeout(function () {
                        $(obj).text(originalText);
                        $(obj).css('background-color', originalColor);
                    }, 5000);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }
    });

    $(function () {

        $('#bptk-rebuild-blocks').click(function (e) {
            e.preventDefault();
            RebuildBlocks(this);
        });

        function RebuildBlocks(obj) {

            var nonce = $(obj).data('nonce');
            var originalText = $(obj).text();
            var originalColor = $(obj).css('background-color');

            jQuery.ajax({
                type: "POST",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "rebuild_blocks",
                    nonce: nonce
                },
                success: function (response) {
                    $('#bptk-rebuild-blocks-debug').text(response);
                    // $(obj).text(response);
                    $(obj).css('background-color', 'green');
                    setTimeout(function () {
                        $(obj).text(originalText);
                        $(obj).css('background-color', originalColor);
                    }, 5000);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });
        }
    });

    $(function () {

        $('div.add_note p').on('click', '.add_note', function () {

            if (!$('textarea#report_note').val()) {
                return;
            }

            $('#report_notes_empty').remove();

            var nonce = $('#report_notes_metabox_nonce').val();
            var post_id = $(this).data('id');
            var note = $('textarea#report_note').val();
            var note_type = $('select#report_note_type').val();

            jQuery.ajax({
                type: "POST",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "add_report_note_via_ajax",
                    nonce: nonce,
                    post_id: post_id,
                    note: note,
                    note_type: note_type,
                },
                success: function (response) {
                    $('ul.report_notes').prepend(response);
                    $('textarea#report_note').val('');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });

            return false;

        });
    });

    $(function () {

        $('.report_notes').on('click', 'a.delete_note', function () {
            var nonce = $('#report_notes_metabox_nonce').val();
            var note = $(this).closest('li.note');


            jQuery.ajax({
                type: "POST",
                dataType: "html",
                url: ajaxurl,
                data: {
                    action: "delete_report_note_via_ajax",
                    nonce: nonce,
                    note_id: $(note).attr('rel'),
                },
                success: function (response) {
                    $(note).remove();
                    // If we just removed final note, clean up the box
                    if ($('ul.report_notes li').length === 0) {
                        $('ul.report_notes').hide();
                        $('div.add_note').css('border', '0px');
                        console.log('empty');
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(jqXHR, textStatus, errorThrown);
                }
            });

            return false;
        });
    });
})(jQuery);