jQuery(function ($) {
    $('#publish, #save-post').on('click.require-post-category', function (e) {
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
    });
    if ($('#publish').data('events') != null) {
        var publish_click_events = $('#publish').data('events').click;
        if (publish_click_events) {
            if (publish_click_events.length > 1) {
                publish_click_events.unshift(publish_click_events.pop());
            }
        }
    }
    if ($('#save-post').data('events') != null) {
        var save_click_events = $('#save-post').data('events').click;
        if (save_click_events) {
            if (save_click_events.length > 1) {
                save_click_events.unshift(save_click_events.pop());
            }
        }
    }
});