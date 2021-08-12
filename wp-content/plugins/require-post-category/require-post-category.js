jQuery(function ($) {
    function rpc_event_handler(e) {
        require_post_category.error = false;
        $.each(require_post_category.taxonomies, function (taxonomy, config) {
            if (config.type == 'hierarchical') {
                if ($('#taxonomy-' + taxonomy + ' input:checked').length == 0) {
                    alert(config.message);
                    require_post_category.error = true;
                }
            } else {
                if ($('#tagsdiv-' + taxonomy + ' .tagchecklist').is(':empty')) {
                    alert(config.message);
                    require_post_category.error = true;
                }
            }
        });
        if (require_post_category.error) {
            e.stopImmediatePropagation();
            return false;
        } else {
            return true;
        }
    }
    $('#publish, #save-post').on('click.require-post-category', rpc_event_handler);
    $('#post').on('submit.require-post-category', rpc_event_handler);
    if ($('#publish')[0] != null && $._data($('#publish')[0], "events") != null) {
        var publish_click_events = $._data($('#publish')[0], "events").click;
        if (publish_click_events) {
            if (publish_click_events.length > 1) {
                publish_click_events.unshift(publish_click_events.pop());
            }
        }
    }
    if ($('#save-post')[0] != null && $._data($('#save-post')[0], "events") != null) {
        var save_click_events = $._data($('#save-post')[0], "events").click;
        if (save_click_events) {
            if (save_click_events.length > 1) {
                save_click_events.unshift(save_click_events.pop());
            }
        }
    }
    if ($('#post')[0] != null && $._data($('#post')[0], "events") != null) {
        var post_submit_events = $._data($('#post')[0], "events").submit;
        if (post_submit_events) {
            if (post_submit_events.length > 1) {
                post_submit_events.unshift(post_submit_events.pop());
            }
        }
    }
});