jQuery(document).ready(function ($) {
    $('body').on('click', '.wpdiscuz_addon_note .notice-dismiss', function () {
        $.ajax({url: ajaxurl, data: {action: 'dismiss_wpdiscuz_addon_note'}})
    })
    $('body').on('click', '.wpdiscuz_tip_note .notice-dismiss', function () {
        var tipid = $('#wpdiscuz_tip_note_value').val();
        $.ajax({url: ajaxurl, data: {action: 'dismiss_wpdiscuz_tip_note', tip: tipid}})
    })
});