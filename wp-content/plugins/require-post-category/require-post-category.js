jQuery(function($){
    $('#publish, #save-post').click(function(e){
        if($('#taxonomy-category input:checked').length==0){
            alert(require_post_category.message);
            e.stopImmediatePropagation();
            return false;
        }else{
            return true;
        }
    });
    var publish_click_events = $('#publish').data('events').click;
    if(publish_click_events){
        if(publish_click_events.length>1){
            publish_click_events.unshift(publish_click_events.pop());
        }
    }
    if($('#save-post').data('events') != null){
        var save_click_events = $('#save-post').data('events').click;
        if(save_click_events){
            if(save_click_events.length>1){
                save_click_events.unshift(save_click_events.pop());
            }
        }
    }
});