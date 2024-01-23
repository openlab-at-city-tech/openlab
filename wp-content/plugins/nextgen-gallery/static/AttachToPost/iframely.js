if (window.frameElement) {
	document.getElementsByTagName('html')[0].id = 'iframely';
	jQuery(function($) {
        var $content = $('#ngg_page_content');
        $('#wpbody-content').html($content);
        $('#wpbody').html($content);
        $('#wpwrap').html($content);
        // $('#wpwrap').html($('#wpbody').html($('#wpbody-content').html($('#ngg_page_content'))));
        

		// We need to ensure that any POST operation includes the "attach_to_post"
		// parameter, to display subsequent clicks in iframely.
		$('form').each(function() {
			$(this).append("<input type='hidden' name='attach_to_post' value='1'/>");
		});

		var parent = window.parent;
		
		if (parent == null || typeof(parent.adjust_height_for_frame) === "undefined") {
			if (window != null && typeof(window.adjust_height_for_frame) !== "undefined") {
				parent = window;
			}
		}

        // Adjust the height of the frame
        if (typeof(parent.adjust_height_for_frame) !== "undefined") {
            parent.adjust_height_for_frame(parent, window, iframely_callback);
        }

	});
}

function iframely_callback(parent_window, current_window)
{
    var $current_window = jQuery(current_window);

    if (typeof($current_window.data('iframely')) === 'undefined') {
        $current_window.data('iframely', { attempts: 1 });
    }

    var iframely = $current_window.data('iframely');

    // After we've attempted to resize the frame 3 times, give up
    if (iframely.attempts == 3) {
        jQuery('#iframely').css({
            position: 'static',
            visibility: 'visible'
        }).animate({ opacity: 1.0 });
    } else {
        iframely.attempts += 1;
        setTimeout(function() {
            parent_window.adjust_height_for_frame(parent_window, current_window, iframely_callback);
        }, 400);
    }
}
