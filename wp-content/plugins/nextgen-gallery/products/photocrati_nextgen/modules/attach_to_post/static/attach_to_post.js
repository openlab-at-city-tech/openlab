// Give this window an ID
this.id = 'ngg-attach_to_post';

// Provides a function to close the TinyMCE popup window
function close_attach_to_post_window() {
	var src = jQuery(top.document).find("#TB_window iframe").attr('src');
	if (src && src.match('attach_to_post')) {
		top.tb_remove();
	} else {
		top.tinyMCE.activeEditor.windowManager.close(window);
	}
}

// This function is only necessary on iOS because iframe's scrollable='yes' attribute
// is ignored there. To work around this we give each iframe a height matching its contents
// height and set overflow-y:scroll on the wrapping parent div.
function adjust_height_for_frame(parent_window, current_window, callback) {
    if (!/crios|iP(hone|od|ad)/i.test(navigator.userAgent)) {
        if (callback !== undefined) {
            return callback(parent_window, current_window);
        } else {
            return true;
        }
    }

	// Adjust height of the frame
	var $frame			= jQuery(current_window.frameElement);
	var new_height		= $frame.contents().height()/3;
    var new_height_body = $frame.contents().find('#wpbody').height();
    var parent_height   = jQuery(parent_window.document).height();
	var current_height	= $frame.height();

	// because #wpbody may have zero height
	if (new_height_body === 0) {
		new_height_body = $frame.contents().height();
	}

    if (new_height < new_height_body) { new_height = new_height_body; }
    if (new_height < parent_height)   { new_height = parent_height; }

    if (current_height < new_height) {
        var frame_id = $frame.attr('id');
        if (frame_id && frame_id.indexOf('ngg-iframe-') === 0) {
            var tab_id = frame_id.substr(11);
            if (tab_id) {
                jQuery('#' + tab_id).height(new_height);
            }
        }
    }

	if (callback !== undefined) {
        return callback(parent_window, current_window, new_height);
    } else {
        return true;
    }
}

// This overrides certain parts of shutter.js' positioning & sizing code
function ngg_get_measures_for_frame(frame) {
	var $frame			= jQuery(frame);
	var frame_id = $frame.attr('id');
	var measures = {};

	if (frame_id && frame_id.indexOf('ngg-iframe-') === 0) {
		var tab_id = frame_id.substr(11);
		
		if (tab_id) {
			var jDoc = jQuery(document);

            // remove around 40px for tabs and padding
			measures.scrollTop = jDoc.scrollTop() - 40;

			if (window.parent) {
                // remove around 40px for tabs and padding
				measures.scrollHeight = jQuery(window.parent.document)
											.find('.ngg_attach_to_post_window')
											.height() - 40;
			} else {
				measures.scrollHeight = jDoc.height();
			}

            if (typeof(window.console) !== 'undefined') {
                console.log(measures);
            }
		}
	}
	
	return measures;
}

// Activates the attach to post screen elements
jQuery(function($) {

	// iOS does not support iframe's scrollable="yes" attribute which requires some workarounds
    if (/crios|iP(hone|od|ad)/i.test(navigator.userAgent)) {
        $('#attach_to_post_tabs').addClass('ngg_atp_ios_detected');
    }

	// Activate tabs
	$('#attach_to_post_tabs').ngg_tabs({
        onShowTab: function(tab)  {
            // Fix z-index problem with frames and non-frames on Chrome 69/70
            if (navigator.appVersion.match(/Chrome\/(69|7)/)) {
                tab.attr('id') != 'displayed_tab' ?
                    $('#attach_to_post_tabs').addClass('chrome_70_hack_frames').removeClass('chrome_70_hack_noframes') :
                    $('#attach_to_post_tabs').removeClass('chrome_70_hack_frames').addClass('chrome_70_hack_noframes')
            }
        }
    });

	// If the preview area is being displayed, emit an event for that
	$('.ngg_page_content_menu a').on('click', function(){
		if ($(this).attr('data-id') === 'preview_tab') {
			$('#preview_area').trigger('opened');
		}
	});

	// Activate accordion for display tab
	$('.accordion').accordion({
        clearStyle: true,
        autoHeight: false,
        heightStyle: 'content'
    });

	// Apply active class to first tab
	$('.ui-tabs-nav li:first-of-type a').addClass("active_tab");

	// If the active display tab is clicked, then we assume that the user
	// wants to display the original tab content
	$('.ui-tabs-nav a').click(function(e) {

		/* Add color to the active link */
        $('.ui-tabs-nav a').removeClass("active_tab");
        $(this).addClass("active_tab");

		var element = e.target ? e.target : e.srcElement;

		// If the accordion tab is used to display an iframe, ensure when
		// clicked that the original iframe content is always displayed
		if ($(element).parent().hasClass('ui-state-active')) {
			var iframe = $(element.hash + ' iframe');
			if (iframe.length > 0) {
				if (iframe[0].contentDocument.location != iframe.attr('src')) {
					iframe[0].contentDocument.location = iframe.attr('src');
				}
			}
		}
	});

	// Close the window when the escape key is pressed
	$(this).keydown(function(e) {
		if (e.keyCode === 27) {
			close_attach_to_post_window();
        }
		return;
	});

	// Fade in now that all GUI elements are intact
	$('body').css({
		position: 'static',
		visibility: 'visible'
	}).animate({
		opacity: 1.0
	});

});

/* Open and close IGW video tutorial */
jQuery(function($) {

	$('#displayed_tab .ngg_igw_video_open').click( function(e) {
        $('#displayed_tab .ngg_igw_video_inner').append('<iframe class="ngg_igw_video_iframe" width="1050" height="590" src="https://www.youtube.com/embed/mNEnY23i9DE?rel=0" frameborder="0" allowfullscreen></iframe>');
        $('#displayed_tab .ngg_igw_video_inner').css("display", "block");
        $('#displayed_tab .ngg_igw_video_open').css("display", "none");
    });

    $('#displayed_tab .ngg_igw_video_close').click( function(e) {
        $('#displayed_tab .ngg_igw_video_iframe').remove();
        $('#displayed_tab .ngg_igw_video_inner').css("display", "none");
        $('#displayed_tab .ngg_igw_video_open').css("display", "block");
    });
    
});

/* Show Pro gallery promo only on Choose Display tab */
jQuery(function($) {

	$('.ngg_page_content_menu a').click( function(e) {
        
        var id = $(this).attr('data-id');
        if (id == "choose_display") { 
            $("#displayed_tab .ngg_igw_video_open").css("display", "block");
        }
        else { 
            $("#displayed_tab .ngg_igw_video_open").css("display", "none");
            $("#displayed_tab .ngg_igw_video_inner").css("display", "none");
        }

    });

});

function is_visual_editor() {
	return jQuery(top.document).find('.html-active:visible').length === 0;
}

function insert_into_editor(snippet, ref_or_id) {
	if (is_visual_editor()) {
		var editor = top.tinyMCE.activeEditor;
		if (editor.selection.getNode().outerHTML.indexOf(ref_or_id) >= 0) {
			jQuery(editor.selection.getNode()).attr('data-shortcode', snippet.substring(1, snippet.length-1));
		} else {
			editor.execCommand('mceInsertContent', false, snippet);
		}
		editor.selection.collapse(false);

	} else {
		myField = top.document.getElementById('content');

		myValue = snippet;

		//IE support
		if (document.selection) {
			myField.focus();
			sel = document.selection.createRange();
			sel.text = myValue;
		} else if (myField.selectionStart || myField.selectionStart === '0') {
            //MOZILLA and others
			var startPos = myField.selectionStart;
			var endPos = myField.selectionEnd;
			myField.value = myField.value.substring(0, startPos)
				+ myValue
				+ myField.value.substring(endPos, myField.value.length);
		} else {
			myField.value += myValue;
		}
	}
}