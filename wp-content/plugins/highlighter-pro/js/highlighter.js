/***************************************
BEGIN UTILITY FUNCTIONS
***************************************/

//utility for determining touch devices
function isTouchDevice() {
  return 'ontouchstart' in window // works on most browsers 
      || window.navigator.msMaxTouchPoints > 0; // works on ie10
}

//finds whether the bottom of the element is in the viewport
function bottomVisible(obj,offset){
    var a = obj.offset().top + offset,
        b = obj.outerHeight(true),
        c = jQuery(window).height(),
        d = jQuery(window).scrollTop();
    return ((c+d) >= (a+b));
}
//finds whether the top of the element is in the viewport
function topVisible(obj,offset){  
    var viewportHeight = jQuery(window).height(),
        documentScrollTop = jQuery(document).scrollTop(),
        minTop = documentScrollTop + offset,
        maxTop = documentScrollTop + viewportHeight,
        objOffset = obj.offset().top;
    return (objOffset >= minTop && objOffset <= maxTop);
}
//retrieves selected text
function getSelectedText() {
    t = (document.all) ? document.selection.createRange().text : document.getSelection();
    return t;
}

// check how much admin bar is showing
function getTopOffset() {
    topOffset = 0;
    var $win = jQuery(window).width();
    if(jQuery('#wpadminbar').length) {
        if($win < 601) {
            topOffset += 0;
        } else if($win < 783) {
            topOffset += 46;
        } else {
            topOffset += 32;
        }
    }
    return topOffset;
}

/***************************************
END UTILITY FUNCTIONS
***************************************/







/***************************************
BEGIN EVENTS
***************************************/

jQuery(window).scroll(function() {

    // remove highlighter popup if the user scrolls the page
    //removePopups(); // disabled this because medium theme in firefox was triggering it even when not scrolling

});

jQuery(window).load(function() {

    // detect and annotate your highlights on the page
    detectHighlights();

});

var resizeTimer;
jQuery(window).on('resize', function(e) {

  clearTimeout(resizeTimer);
  resizeTimer = setTimeout(function() {

    // label top positions and css classes could change on window resize
    detectHighlights();
            
  }, 250);

});

// keyboard shortcuts
jQuery(document).keyup(function(e) {

    // escape key pressed
    if (e.keyCode === 27) {
        removePopups();
        /*
        if(jQuery('.confirm-no').length) {
            jQuery('.confirm-no').trigger('click');
        }
        */
    }

});

// touchend can't find touch location, 
// so we have to store them in touchstart and touchmove events
var lastTouchX = 0;
var lastTouchY = 0;
jQuery('body').on('touchstart', function(e) {
    lastTouchX = e.originalEvent.touches[0].clientX;
    lastTouchY = e.originalEvent.touches[0].clientY;
});
jQuery('body').on('touchmove', function(e) {
    lastTouchX = e.originalEvent.touches[0].clientX;
    lastTouchY = e.originalEvent.touches[0].clientY;
});

jQuery(document).ready(function() {

    adjustDockedHeight();
    jQuery('.highlighted-text').removeClass('active');
    jQuery('body').addClass('highlights-ready');

    // add touch class to body
    if(isTouchDevice()) jQuery('body').addClass('touch');

    // add placeholder text to comment textarea for mobile
    //if(isTouchDevice()) jQuery('#highlighter-comment-textarea').attr('placeholder', 'Enter your comment...');
	
	// detect when the comment form gains focus (i.e. when the virtual keyboard is shown)
	jQuery('body').on('focus', '#highlighter-comment-textarea', function(e) {
		
		// here is where you could do something when the keyboard is shown
		
	});

    // detect state of mouse
    var isMouseDown = false
    jQuery('body').on('mousedown', function() {
        isMouseDown = true;
    })
    jQuery('body').on('mouseup', function() {
        isMouseDown = false;
    });

    // after user highlights text
    jQuery('body').on('mouseup touchend', function(e) {

        // sometimes the highlighter shield can glitch on and we
        // never want it by itself since there's no way to close out of it
        // doesn't apply to touch since those close out with other clicks
        if(!jQuery('.highlighter-docked-panel').hasClass('shown') 
            && !jQuery('.highlighter-confirm').length
            && !jQuery('.ajax-auth').hasClass('shown')
            && !isTouchDevice()) {
            removePopups();
        }

        getHighlightedText(e);

    });

    // shortcode purposes
    jQuery('.highlighter-shortcode .highlighted-text').removeClass('active');

    // remove highlighter popup if user clicks outside of it
    jQuery('body').on('click touch', '.highlighter-shield', function(e) {

        // if comment form is open and user clicks shield, remove the highlight that was just created
        if(!isTouchDevice() && jQuery('.highlighter-comments-wrapper').hasClass('shown')) {
            removeHighlight();
        }

        // popup is a child of shield, so don't remove popups if a button within is clicked
        if(!isTouchDevice() && !jQuery(e.target).closest('.highlighter-popup').length) {
            removePopups();
        }
        
    });
    // remove highlighter popup if user clicks close
    jQuery('body').on('click touch', '.login_close', function(e) {

        removePopups();
        return false;

    });

    // show forgot password form
    jQuery('#pop_forgot').click(function(){
        jQuery('form#login').removeClass('shown');
        jQuery('form#forgot_password').addClass('shown');
        return false;
    });

    // show form from inside popup
    jQuery('body').on('click touch', '#pop_login, #pop_signup', function (e) {
        if (jQuery(this).attr('id') == 'pop_signup') {
            jQuery('form#login').removeClass('shown');
            jQuery('form#register').addClass('shown');
        } else {
            jQuery('form#login').addClass('shown');
            jQuery('form#register').removeClass('shown');
        }
        return false;
    });

    // highlight the selected text
    jQuery('body').on('mousedown touch', '.btn-highlight-text', function() {

        highlightClicked();

    });

    // comment the selected text
    jQuery('body').on('mousedown touch', '.btn-comment:not(.btn-comment-link)', function() {
        
        commentClicked();

    });

    // user clicked comment button from non-single page
    jQuery('body').on('click touch', '.btn-comment-link', function() {

        highlightConfirm(this, 'redirectToPost', 'Continue');
        
    });

    // tweet the selected text
    jQuery('body').on('mousedown touchstart', '.btn-twitter', function() {

        twitterClicked(this);

    });

    // post to facebook the selected text
    jQuery('body').on('mousedown touchstart', '.btn-facebook', function() {

        facebookClicked(this);

    });

    // remove highlight
    jQuery('body').on('mousedown touchstart', '.btn-remove-highlight', function() {

        highlightConfirm(this, 'removeHighlight', 'Remove');

    });

    jQuery('body').on('mousedown touchstart', '.confirm-yes', function() {

        jQuery(this).addClass('mousedown');

    });

    jQuery('body').on('mousedown touchstart', '.confirm-no', function() {

        jQuery(this).addClass('mousedown');

    });

    jQuery('body').on('mouseup touchend', function() {

        jQuery('.confirm-yes, .confirm-no').removeClass('mousedown'); 

    });


    // highlightConfirm added events
    jQuery('body').on('mousedown touch', '.removeHighlight', function() {

        jQuery('.highlighter-confirm').remove();
        removeHighlight();
        removePopups();

    });

    jQuery('body').on('mouseup touch', '.addHighlight', function() {

        addToHighlight();

    });

    jQuery('body').on('mouseup touch', '.addComment', function() {

        addToComment();

    });

    jQuery('body').on('mouseup touch', '.addNewComment', function() {

        addNewComment();

    });

    jQuery('body').on('mouseup touch', '.addnewcomment-wrapper .confirm-no', function() {

        removeHighlight();

    });

    jQuery('body').on('mouseup touch', '.redirectToPost', function() {

        var linkspan = jQuery('.highlighter-confirm').data('span');
        var link = jQuery(linkspan).data('href');
        window.location.href = link;

    });

    // highlighter confirm no
    jQuery('body').on('mouseup touch', '.highlighter-confirm .confirm-no', function() {

        jQuery('.highlighter-confirm').remove();
        removePopups(); 

    });

    // comment specific confirm no
    jQuery('body').on('mouseup touch', '.highlighter-comment .confirm-no', function() {

        removeHighlight();
        removePopups();

    });

    // view highlight specific confirm no
    jQuery('body').on('mouseup touch', '.highlighter-view-notes-wrapper .confirm-no', function() {

        removePopups();

    });

    // submit the form if the outer button div was clicked
    jQuery('body').on('click touch', '#highlighter-comment-form .confirm-yes', function() {

        jQuery('#highlighter-comment-form').submit();

    });

    // when comment form is submitted call the ajax function
    jQuery('#highlighter-comment-form').submit(function(){

        ajaxSubmitComment();

        return false;
        
    });

    // view highlight button clicked
    jQuery('body').on('mouseup touch', '.btn-view-highlight', function() {

        var span = !jQuery(this).closest('.highlighter-popup').length ? jQuery(this).data('span') : jQuery(this).closest('.highlighter-popup').data('span');

        viewHighlight(this, jQuery(span));

    });

    var timer;
    // show popup buttons on existing highlight
    //jQuery('body').on('mouseenter touchstart', '.highlighted-text', function(e) {
        // used to be touchstart but was triggering addHighlightPopup twice...
    jQuery('body').on('mouseenter touch', '.highlighted-text', function(e) {
        var userid = jQuery('.highlighter-content').data('userid');
        // don't allow popups in shortcodes
        if(!jQuery(this).closest('.highlighter-shortcode').length) {
            // don't enable if this is not the top highlight on top-only mode
            if(jQuery('.highlighter-content.top-only').length && 
                !(jQuery(this).hasClass('top-highlight'))) return false;
            // don't enable if this is not user's highlight on yours-only mode
            if(jQuery('.highlighter-content.yours-only').length && 
                !(jQuery(this).is('[data-userid*="'+userid+'"]'))) return false;
            // don't show a new popup if the last one hasn't cleared (via timeout) yet
            if(!jQuery('.highlighter-popup').hasClass('shown') || isTouchDevice()) {
                clearTimeout(timer);
                removePopups();
                // don't want to popup if user is in the process of dragging the mouse (highlighting text)
                if(!isMouseDown) {
                    addActive(this);
                    var postid = jQuery(this).closest('.highlighter-content').data('postid');
                    addHighlightPopup(e, this, 'existing', postid);
                }
            }
        }

    });

    // shortcode purposes
    jQuery('body').on('mouseenter', '.highlighter-shortcode .highlighted-text', function(e) {
        addActive(this);
    });
    jQuery('body').on('mouseleave', '.highlighter-shortcode .highlighted-text', function(e) {
        jQuery(this).removeClass('active');
    });

    // hide popup buttons on existing highlight when exiting popover buttons
    jQuery('body').on('mouseleave', '.highlighter-popup, .highlighted-text', function() {

        clearTimeout(timer);

        if(!jQuery('.highlighter-shield').hasClass('shown') && !jQuery('.highligher-confirm .highlighter-docked-panel').length) {

            // add a slight delay
            timer = setTimeout(function(){
            
                var popupHovered = jQuery('.highlighter-popup:hover').length;
                var highlightHovered = jQuery('.highlighted-text:hover').length;
                if(!popupHovered && !highlightHovered) {
                    removePopups();
                }

            }, 100);

        }

    });

    // touch devices can toggle popups closed by clicking anywhere not within a popup
    jQuery(document).on('touchstart', function(e) {

        if (!jQuery(e.target).closest('.highlighter-popup, .highlighter-docked-panel, .ajax-auth, .highlighter-confirm').length) {

            clearTimeout(timer);
			// if the user is currently adding a comment, only let the cancel button
			// remove popups so they aren't confused by the keyboard still showing
            if(jQuery('.highlighter-shield, .highlighter-popup').hasClass('shown') 
			   && !jQuery('.highlighter-docked-panel').hasClass('shown')) {
                removePopups();
            }

        }

    });

    // ajax auth
    jQuery('form#login, form#register').on('submit', function (e) {
        
        ajaxLogin(this);
        e.preventDefault();
        return false;

    });

    jQuery('form#forgot_password').on('submit', function(e){
        
        ajaxForgotPassword(this);
        e.preventDefault();
        return false;

    });
    var commenttimer;
    // check content after user has stopped typing comment for 2 seconds
    jQuery('#highlighter-comment-textarea').on('keyup',function() {
        if (commenttimer) {
            clearTimeout(commenttimer);
        }
        commenttimer = setTimeout(function() {
            var $postid = jQuery('.highlighter-popup').data('postid')
            checkContent($postid);
        },2000);
    });

    // show highlighter stats
    jQuery('body').on('click touch', '.highlighter-stats-toggle, .highlighter-stats .confirm-no', function() {
        
        jQuery(this).closest('.highlighter-stats-wrapper').toggleClass('shown');
        
    });

});

/***************************************
END EVENTS
***************************************/










/***************************************
BEGIN FUNCTIONS
***************************************/

function addActive(_this) {
    jQuery(_this).addClass('active');
}

// detects and annotates all highlights on the page
function detectHighlights() {
    var userid = jQuery('.highlighter-content').data('userid');

    // setup vars
    var commentid = '',
        note = '',
        comment = '',
        commented = '',
        highlighticon = '',
        highlighted = '',
        divider = '',
        top = '',
        highlighted_count = 0,
        commented_count = 0,
        largest = 0,
        topspan = '',
        //spans = jQuery('.highlighter-content span[data-userid*="' + userid + '"]'), // if we want to only grab current user's highlights
        spans = jQuery('.highlighted-text').not('.highlighter-shortcode .highlighted-text'),
        msghighlighted = jQuery('.highlighter-content').data('msghighlighted'),
        msgcommented = jQuery('.highlighter-content').data('msgcommented'),
        labeldisplay = jQuery('.highlighter-content').data('labeldisplay'),
        highlightdisplay = jQuery('.highlighter-content').data('highlightdisplay');

    // remove existing notes and start fresh each time
    jQuery('.highlighter-note').remove();

    // exit the function if labels are turned off
    if(labeldisplay=='') return false;

    var showyours = jQuery.inArray('yours', labeldisplay) !== -1 ? true : false;
    var shownotes = jQuery.inArray('notes', labeldisplay) !== -1 ? true : false;
    var showtop = jQuery.inArray('top', labeldisplay) !== -1 ? true : false;

    // loop through each returned span
    spans.each(function() {
        var appendflag = false;
        commentid = jQuery(this).data('commentid');
        note = jQuery('<div>', {class: 'btn-view-highlight highlighter-note'});
        comment = jQuery('<span>', {class: 'highlighter-note-comment'});
        commented = jQuery(this).data('userid-comment');
        highlighted = jQuery(this).data('userid');
        
        // check and see if this span has current userid in comment data
        commented = (typeof commented != 'undefined') ? commented.toString() : '';
        highlighted = (typeof highlighted != 'undefined') ? highlighted.toString() : '';
        commented_count = commented === '' ? 0 : commented.split(',').length;
        highlighted_count = highlighted === '' ? 0 : highlighted.split(',').length;
        if(commented.split(',').indexOf(userid) !== -1 && showyours) {
            note.html('<span class="inner-text">' + msgcommented + '</span>');
            appendflag = true;
        } else if(highlighted.split(',').indexOf(userid) !== -1 && showyours) {
            note.html('<span class="inner-text">' + msghighlighted + '</span>');
            appendflag = true;
        } else if(commented_count > 0 && shownotes) {
            comment.html(commented_count);
            note.html(comment);
            appendflag = true;
        }
        // this is the top highlight so far
        if(highlighted_count > largest && showtop) {
            largest = highlighted_count;
            topspan = note;
            appendflag = true;
            // store this span in the dom
            jQuery('.highlighter-content').data('topspan', jQuery(this));
        }

        // setup note display
        if(appendflag) {        
            top = jQuery(this).offset().top;
            note.css('top', top + 'px');
            note.data('commentid', commentid);
            note.data('userid', userid);
            note.data('span', jQuery(this));
            // add this note to the dom
            jQuery('body').append(note);
        }
        
    }).promise().done(function() { 
        var highlighticon = jQuery('<span>', {class: 'highlighter-icon'});
        jQuery('.highlighter-content').removeClass('highlighter-content-loading');
        jQuery('.highlighter-note').addClass('shown').append(highlighticon);
        highlightNotePosition();

        // show top highlight span based on top highlight label
        if(highlightdisplay=='top') {
            jQuery('.highlighter-content').data('topspan').addClass('top-highlight');
        }
    });

    if(topspan.length > 0) {
        topspan.addClass('top-highlight');
        if(topspan.html().length) {
            if(topspan.has('.highlighter-note-comment').length) {
                divider = '';
            } else {
                divider = '<br />';
            }
            topspan.html('Top highlight' + divider + topspan.html());
        } else {
            topspan.html('Top highlight');
        }
    } 
    
}

// position the highlight notes after they are all added to the DOM
function highlightNotePosition() {
    var offset = jQuery('.highlighter-content').data('labeloffset');
    var placement = jQuery('.highlighter-content').data('labelplacement');

    if(jQuery('.highlighter-content').length) {
        if(placement==='right') {
            if(jQuery(window).width() > 991) {
                var left = jQuery('.highlighter-content').offset().left + jQuery('.highlighter-content').outerWidth() + offset;
                jQuery('.highlighter-note').removeClass('fixed-right').css('left', left + 'px');
            } else {
                jQuery('.highlighter-note').addClass('fixed-right');
                jQuery('body').addClass('label-compact');
            }
        } else {
            jQuery('.highlighter-note').addClass('placement-left');
            if(jQuery(window).width() > 991) {
                var right = jQuery(window).width() - jQuery('.highlighter-content').offset().left + offset;
                jQuery('.highlighter-note').removeClass('fixed-left').css('right', right + 'px');
            } else {
                jQuery('.highlighter-note').addClass('fixed-left');
                jQuery('body').addClass('label-compact');
            }
        }
    }
}

// twitter button clicked in popup
function twitterClicked(_this) {
    var msg = jQuery('.highlighter-content').data('twitterconfirm');
    
    if(confirm(msg) === false) {
        removePopups();
        return false;
    }

    // if user is logged in, try to add twitter span wrap to selected text,
    // otherwise just let them tweet the text with no DOM manipulation
    var $postid = jQuery('.highlighter-popup').data('postid'),
        $wrap = jQuery('.highlighter-content.post-' + $postid),
        $userid = $wrap.data('userid'),
        $span = jQuery('.highlighter-popup').data('span'),
        existing = false,
        $tweet = getSelectedText().toString()
        $twitterHighlights = jQuery('.highlighter-content').data('twitterhighlights');

    // check if comment button was clicked for existing highlight
    if(jQuery($span).hasClass('highlighted-text')) {
        existing = true;
    }

    // don't worry about highlighting if it's not enabled for Tweets
    if($userid && $twitterHighlights) {

        if(existing) {

            appendAttrId(jQuery($span), $userid, 'data-userid');
            $tweet = jQuery($span).html();

        } else {

            // find inner highlights
            findInnerHighlights($userid);

            // highlight the selection using rangy
            highlighter.highlightSelection("highlighted-text", {exclusive: false});

        }

        // update the post content with the highlight
        ajaxUpdateContent($postid);           

    } else {

        // remove the popup since the text will no longer be highlighted
        // this is automatically taken care of in ajaxUpdateContent() as well
        removePopups();

    }

    // open the tweet popup
    tweetHighlight($tweet);
}

// facebook button clicked in popup
function facebookClicked(_this) {
    var msg = jQuery('.highlighter-content').data('facebookconfirm');

    if(confirm(msg) === false) {
        removePopups();
        return false;
    }

    // if user is logged in, try to add facebook span wrap to selected text,
    // otherwise just let them post the text with no DOM manipulation
    var $postid = jQuery('.highlighter-popup').data('postid'),
        $wrap = jQuery('.highlighter-content.post-' + $postid),
        $userid = $wrap.data('userid'),
        $title = $wrap.data('posttitle'),
        $span = jQuery('.highlighter-popup').data('span'),
        existing = false,
        $fb = getSelectedText().toString(),
        $facebookHighlights = jQuery('.highlighter-content').data('facebookhighlights');

    // check if comment button was clicked for existing highlight
    if(jQuery($span).hasClass('highlighted-text')) {
        existing = true;
    }

    // don't worry about highlighting if it's not enabled for Facebook
    if($userid && $facebookHighlights) {

        if(existing) {

            appendAttrId(jQuery($span), $userid, 'data-userid');
            $fb = jQuery($span).html();

        } else {

            // find inner highlights
            findInnerHighlights($userid);

            // highlight the selection using rangy
            highlighter.highlightSelection("highlighted-text", {exclusive: false});

        }

        // update the post content with the highlight
        ajaxUpdateContent($postid);           

    } else {

        // remove the popup since the text will no longer be highlighted
        // this is automatically taken care of in ajaxUpdateContent() as well
        removePopups();

    }

    facebookHighlight('"' + $fb + '"', 'From: ' + $title);
}

// open the twitter window
function tweetHighlight (text) {
    if(typeof text != 'undefined') {
        var tweetUrl = 'https://twitter.com/share?text=' + encodeURIComponent(text) + '.' + '&url=' + window.location.href;
        window.open(tweetUrl,'twitter','width=500,height=270,status=0,toolbar=0');
    }
}

// open the facebook window
function facebookHighlight (text, title) {
    if(typeof text != 'undefined') {
        var facebookUrl = 'https://www.facebook.com/sharer/sharer.php?u=' + window.location.href + '&amp;src=sdkpreparse&title=' + encodeURIComponent(text) + '&description=' + encodeURIComponent(title);
        window.open(facebookUrl,'facebook','width=500,height=270,status=0,toolbar=0');
    }
}

// view an existing highlight
function viewHighlight(_this, span) {

    var commentsView = jQuery('.highlighter-content').data('commentsview');

    // figure out if we're coming from a highlighter note click
    if(jQuery(_this).hasClass('highlighter-note')) {
        var commentid = jQuery(_this).data('commentid');
    } else {
        var commentid = span.attr('data-commentid');
    }
    
    if(commentid) {

        if(commentsView=='dock') {

            // open the comment modal
            ajaxDockedHighlight(span, commentid);

        } else {

            // scroll down to the comment
            jQuery("html, body").animate({ scrollTop: jQuery('#div-comment-' + commentid).offset().top - 120 }, 800);
        
        }
        
    } else {

        // open the highlighers modal
        ajaxDockedHighlight(span, 0);
        
    }
}

// show the highlight in the docked panel
function ajaxDockedHighlight(span, commentid) {
    
    var userids = span.data('userid'),
        userids_comment = span.data('userid-comment'),
        highlight = span.html(),
        userid = jQuery('.highlighter-content').data('userid'),
        $action = 'ajax-get-comment';

    // if user has already highlighted this highlight, don't display highlight button
    if (userids.indexOf(userid) >= 0) {
        jQuery('.highlighter-view-notes-wrapper .confirm-yes').hide();
    } else {
        jQuery('.highlighter-view-notes-wrapper .confirm-yes').show();
    }

    // store the passed in span for later use
    jQuery('.highlighter-docked-panel').data('span', span);

    jQuery('.highlighter-comments-wrapper').removeClass('shown');
    jQuery('.highlighter-view-wrapper').addClass('shown');

    jQuery.post(highlighterAjax.ajaxurl, {
        action: $action,
        userids: userids,
        userids_comment: userids_comment,
        commentid: commentid,
        highlight: highlight
        
    }, function (response) {
        if(response.comment) {
            jQuery('.highlighter-view-loading').removeClass('shown');
            jQuery('.highlighter-view-note').html(response.comment);
            if(response.type === 'comment') {
                jQuery('.highlighter-view-note .highlighted-text-comment').hide();
                jQuery('.highlighter-view-note .highlight-comment-wrapper:first-child .highlighted-text-comment').show();
            }
            if(response.name) {
                jQuery('.highlighter-view-note-user').html('<span class="highlighter-comments-name">' + response.name + '</span>');
            }
            jQuery('html').addClass('no-scroll');
            jQuery('body').addClass('no-scroll');
            jQuery('.highlighter-shield').addClass('shown');
            adjustDockedHeight();
        }
    }); 
}

// make sure dock isn't too tall for the viewport
function adjustDockedHeight() {
    var dock = jQuery('.highlighter-view-wrapper');
    var height = jQuery(window).height();
    if(height < 450) {
        jQuery('.highlighter-docked-panel').addClass('full');

        // do some other adjustments here because of laziness
        jQuery('form.ajax-auth').addClass('full').css('height', height - 20 + 'px');
    }

    height = height - 300;
    if(dock.length > 0) {
        if(!topVisible(dock,0)) {
            jQuery('.highlighter-view-notes').css('height', height + 'px');
        }
    }
}

// see if text was highlighted on mouseup
function getHighlightedText(_e) {
    var sel = rangy.getSelection();
    if (sel.rangeCount > 0) {
        var range = sel.getRangeAt(0);
        var parentElement = range.commonAncestorContainer;
        if (parentElement.nodeType == 3) {
            parentElement = parentElement.parentNode;
        }
    }

    var _this = jQuery(parentElement).closest('.highlighter-content');

    if(_this.length && getSelectedText().toString()) {

        var postid = jQuery(_this).data('postid');

        addHighlightPopup(_e, _this, 'new', postid);

    }
}

// make sure content hasn't changed since last content load
function checkContent(postid) {

    var timestamp = jQuery('.highlighter-content.post-' + postid).data('timestamp');

    jQuery.post(highlighterAjax.ajaxurl, {
        action: 'ajax-check-content',
        postid: postid,
        timestamp: timestamp
        
    }, function (response) {
        if(response.modified) {
            removePopups();
            jQuery('.highlighter-content.post-' + postid).html(response.new_content);
            //jQuery('.highlighter-content').attr('data-timestamp', response.new_timestamp);
            jQuery('.highlighter-content.post-' + postid).data('timestamp', response.new_timestamp);
            detectHighlights();
        }
    }); 
}

// displays the highlighter popup controls
function addHighlightPopup(_e, _this, action, postid) {
    // defaults
    if (action === undefined) {
        action = 'new';
    }

    if(jQuery('.highlighter-popup').hasClass('shown')) return false;

    var userid = jQuery('.highlighter-content.post-' + postid).data('userid'),
        spanids = jQuery(_this).attr('data-userid'),
        spanids_comment = jQuery(_this).attr('data-userid-comment'),
        permalink = jQuery('.highlighter-content.post-' + postid).data('permalink'),
        userid = userid.toString(),
        sameuser = false,
        sameuser_comment = false,
        width = 0,
        wider = 50,
        divider = '',
        viewing_enabled = jQuery('.highlighter-content').data('viewingenabled'),
        $shield = jQuery('.highlighter-shield'),
        $popup = jQuery('.highlighter-popup'),
        $popup_nested = jQuery('.highlighter-shield .highlighter-popup'),
        $popup_solo = jQuery('.highlighter-popup').not('.highlighter-shield .highlighter-popup'),
        $triangle = jQuery('.highlighter-triangle'),
        $btn_highlight = jQuery('.btn-highlight-text'),
        $btn_comment = jQuery('.btn-comment').not('.btn-comment-link'),
        $btn_comment_link = jQuery('.btn-comment-link'),
        $btn_twitter = jQuery('.btn-twitter'),
        $btn_facebook = jQuery('.btn-facebook'),
        $btn_remove = jQuery('.btn-remove-highlight'),
        $btn_view = jQuery('.btn-view-highlight.btn-popup'),
        $lbl_count = jQuery('.lbl-count'),
        $lbl_you = jQuery('.lbl-you');

    // start off hiding all buttons and they will be added below
    $popup.find('div').removeClass('shown');

    // determine if this user is already contained in list of user ids
    if(typeof spanids_comment != 'undefined') {
        spanids_comment = spanids_comment.toString();
    } else {
        spanids_comment = '';
    }
    if(typeof spanids != 'undefined') {
        spanids = spanids.toString();
    } else {
        spanids = '';
    }
    if (spanids.split(",").indexOf(userid) !== -1) {
        sameuser = true;
    }
    if (spanids_comment.split(",").indexOf(userid) !== -1) {
        sameuser_comment = true;
    }
    // figure out position of popup
    if(isTouchDevice() && action === 'new') {
        var $popup_top = lastTouchY - 60;
        var $popup_left = lastTouchX - 25; 
        $popup.addClass('new-highlight');
    } else {
        var $popup_top = action == 'new' ? _e.clientY - 50 : jQuery(_this)[0].getBoundingClientRect().top - 50;
        var $popup_left = _e.clientX - 30;
        $popup.removeClass('new-highlight');
    }
    $popup.css('top', $popup_top);
    $popup.css('left', $popup_left);
    $popup.data('span', _this);
    $popup.data('postid', postid);
    $popup.addClass('postid-' + postid);

    // do some resets in case the popup was previous adjusted
    $popup.css('right', 'auto');
    $triangle.css('position', 'absolute');
    $triangle.css('bottom', '-10px');
    $triangle.css('top', 'auto');
    $triangle.css('left', '16px');

    // get comment and highlight counts for this span
    commented = (typeof spanids_comment != 'undefined') ? spanids_comment.toString() : '';
    highlighted = (typeof spanids != 'undefined') ? spanids.toString() : '';
    commented_count = commented === '' ? 0 : commented.split(',').length;
    highlighted_count = highlighted === '' ? 0 : highlighted.split(',').length;
    $cplural = commented_count === 1 ? '' : 's';
    $hplural = highlighted_count === 1 ? '' : 's';
    divider = highlighted_count > 0 ? '<br />' : '';
    if(highlighted_count > 0)
        $lbl_count.html(highlighted_count + ' highlight' + $hplural);
    if(commented_count > 0)
        $lbl_count.html($lbl_count.html() + divider + commented_count + ' comment' + $cplural);
    if(highlighted_count > 0 && commented_count > 0 && !sameuser) {
        $popup.addClass('highlight-and-comment');
    } else {
        $popup.removeClass('highlight-and-comment');
    }

    $lbl_you.html('You highlighted');
    if(sameuser_comment) $lbl_you.html($lbl_you.html() + ' and commented');

    $btn_comment_link.attr('data-href', permalink);

    // show the appropriate buttons
    if(action === 'new') {
        wider = 33;
        $btn_highlight.addClass('shown');
        // not on single page
        if(!jQuery('body').hasClass('single')) {
            $btn_comment_link.addClass('shown');
        } else if(jQuery('#highlighter-comment-form').length) {
            $btn_comment.addClass('shown'); 
        }
        $btn_twitter.addClass('shown');
        $btn_facebook.addClass('shown');

        // show the popup/shield and disable scrolling
        if(!isTouchDevice()) {

            jQuery('html').addClass('no-scroll');
            $shield.addClass('shown').find($popup).addClass('shown');

        } else {

            // don't add the shield for touch devices because the user
            // might need to get at the highlighted text handles and
            // expand/collapse the selection, which would be blocked by a shield
            $popup.addClass('shown');

        }

    } else if(action === 'existing') {
        if(sameuser) $btn_remove.addClass('shown');
        if(viewing_enabled) $btn_view.addClass('shown');
        if(!sameuser) {
            $btn_highlight.addClass('shown');
            // not on single page
            if(!jQuery('body').hasClass('single')) {
                $btn_comment_link.addClass('shown');
            } else if(jQuery('#highlighter-comment-form').length) {
                $btn_comment.addClass('shown'); 
            }
            $btn_twitter.addClass('shown');
            $btn_facebook.addClass('shown');
            if(highlighted_count > 0 || commented_count > 0) $lbl_count.addClass('shown');
        }
        if(sameuser) $lbl_you.addClass('shown');

        // show the popup/shield and disable scrolling
        if(isTouchDevice()) {

            jQuery('html').addClass('no-scroll');
            $shield.addClass('shown').find($popup).addClass('shown');

        } else {

            $popup.addClass('shown');

        }

    }
    // add width to outer popup element
    $whichPopup = isTouchDevice() ? $popup_nested : $popup_solo;
    $whichPopup.children('div').each(function() {
        if(jQuery(this).hasClass('shown')) {
            width += jQuery(this).outerWidth(true);
        }
    }).promise().done(function() { 

        $popup.css('width', width + wider);

        // adjust if popup hits right edge of viewport
        // if popup offset is 0 we need to grab the other popup value not inside the shield
        var $offset_left = $popup.offset().left;
        if($offset_left == 0) $offset_left = $popup_solo.offset().left;
        if($popup.width() + $offset_left + 30 >= jQuery(window).width()) {
            $popup.css('left', 'auto');
            $popup.css('right', 0);
            $triangle.css('position', 'fixed');
            $triangle.css('left', _e.clientX - 10);
            $triangle.css('bottom', 'auto');
            $triangle.css('top', $popup_top + jQuery('.highlighter-popup').outerHeight() - 1);
        }

    });

    // has content been changed since last load?
    checkContent(postid);

    jQuery('body').addClass('no-scroll');


    // we need the popup to get out of the way for a second otherwise
    // microsoft browsers can't maintain the correctly highlighted text
    // we do this by disabling pointer-events briefly
    var available;
    clearTimeout(available);
    available = setTimeout(function(){
    
        $popup.addClass('available');

    }, 100);

}

function isHighlightedText(el) {
    return jQuery(el).hasClass('highlighted-text');
}

// adds the highlight to the dom and store it
function highlightClicked() {

    // variables are either stored in the popup or in the dock
    $target = !jQuery('.highlighter-popup').hasClass('shown') ? jQuery('.highlighter-docked-panel') : jQuery('.highlighter-popup');

    // make sure user is logged in first
    var $postid = $target.data('postid'),
        $wrap = jQuery('.highlighter-content.post-' + $postid),
        $userid = jQuery('.highlighter-content').data('userid'),
        $span = $target.data('span'),
        existing = false,
        highlights = '';

    $span = jQuery($span);

    // check if highlighter button was clicked for existing highlight
    if($span.hasClass('highlighted-text')) {
        existing = true;
    }

    // we are logged in
    if($userid) { 
        // this is an existing highlight
        if(existing) {

            // let's confirm first that the user wants to add to this highlight
            highlightConfirm($span, 'addHighlight');

        // this is newly selected text
        } else {

            // find inner highlights
            findInnerHighlights($userid);

            var selection = rangy.getSelection();
            var range = selection.getRangeAt(0);

            // highlight the selection using rangy
            highlighter.highlightSelection("highlighted-text", {exclusive: false});

            // update the post content with the highlight
            ajaxUpdateContent($postid);

        }

    // we are not logged in
    } else {

        var $loginType = $wrap.data('logintype');
        var $loginURL = $wrap.data('loginurl');
        if($loginType=='redirect') {
            window.location.href = $loginURL;
        } else {
            jQuery('form#login').addClass('shown');
            jQuery('html').addClass('no-scroll');
            jQuery('body').addClass('no-scroll');
            jQuery('.highlighter-shield').addClass('shown');
        }
        
    }
}

// shows comment form
function commentClicked() {
    // make sure user is logged in first
    var $postid = jQuery('.highlighter-popup').data('postid'),
        $wrap = jQuery('.highlighter-content.post-' + $postid),
        $userid = $wrap.data('userid'),
        $span = jQuery('.highlighter-popup').data('span'),
        existing = false;

    // check if comment button was clicked for existing highlight
    if(jQuery($span).hasClass('highlighted-text')) {
        existing = true;
    }

    if($userid) {

        if(existing) {

            // let's confirm first that the user wants to add to this highlight
            highlightConfirm($span, 'addComment');

        } else {
            
            // find inner highlights
            findInnerHighlights($userid);

            // highlight the selection using rangy
            highlighter.highlightSelection("highlighted-text", {exclusive: false});

            var selection = rangy.getSelection();
            var range = selection.getRangeAt(0);
            var $span = range.endContainer.parentElement;

            // store the newly created highlight as the popup data
            jQuery('.highlighter-popup').data('span', $span);

            // let's confirm first that the user wants to add to this highlight
            highlightConfirm($span, 'addNewComment');

        }

    } else {

        var $loginType = $wrap.data('logintype');
        var $loginURL = $wrap.data('loginurl');
        if($loginType=='redirect') {
            window.location.href = $loginURL;
        } else {
            jQuery('form#login').addClass('shown');
            jQuery('html').addClass('no-scroll');
            jQuery('body').addClass('no-scroll');
            jQuery('.highlighter-shield').addClass('shown');
        }
        
    }
}

// find inner highlights and append userid to nodes
function findInnerHighlights($userid) {
    var sel = rangy.getSelection();
    var range = sel.getRangeAt(0);
    var highlights = range.getNodes([1], isHighlightedText);
    if(highlights.length > 0) {
        jQuery(highlights).each(function() {
            appendAttrId(jQuery(this), $userid, 'data-userid');
        });
    }
}

// user confirmed add to highlight
function addToHighlight() {

    $target = !jQuery('.highlighter-popup').hasClass('shown') ? jQuery('.highlighter-docked-panel') : jQuery('.highlighter-popup');
    var $userid = jQuery('.highlighter-content').data('userid'),
        $span = $target.data('span');

    $span = jQuery($span);

    // might need to get postid from content wrapper
    var $postid = !jQuery('.highlighter-popup').hasClass('shown') ? $span.closest('.highlighter-content').data('postid') : jQuery('.highlighter-popup').data('postid');

    // add the user to the existing userid list
    appendAttrId($span, $userid, 'data-userid');

    // update the post content with the highlight
    ajaxUpdateContent($postid);
}

// user confirmed add to comment
function addToComment() {

    var $postid = jQuery('.highlighter-popup').data('postid'),
        $wrap = jQuery('.highlighter-content.post-' + $postid),
        $userid = $wrap.data('userid'),
        $span = jQuery('.highlighter-popup').data('span');

    // add the user to the existing userid list
    appendAttrId(jQuery($span), $userid, 'data-userid');

    // store the newly created highlight as the popup data
    jQuery('.highlighter-popup').data('span', $span);

    // show the comment form
    showCommentForm($span, $postid);

    // manually remove the highlighter confirm box since ajaxUpdateContent hasn't run yet
    jQuery('.highlighter-confirm').remove();
}

// user confirmed add new comment
function addNewComment() {

    var $postid = jQuery('.highlighter-popup').data('postid'),
        $span = jQuery('.highlighter-popup').data('span');

    // show the comment form
    showCommentForm($span, $postid);

    // manually remove the highlighter confirm box since ajaxUpdateContent hasn't run yet
    jQuery('.highlighter-confirm').remove();
}

// removes existing popups
function removePopups() {
    if(jQuery('.highlighter-popup').hasClass('shown') || jQuery('.highlighter-view-wrapper').hasClass('shown')) {
        jQuery('body, html').removeClass('no-scroll');
        jQuery('form#login, form#register, form#forgot_password, .highlighter-docked-panel, .highlighter-shield, .highlighter-popup').removeClass('shown available');
        jQuery('.highlighter-confirm').remove(); 
        jQuery('.highlighted-text').removeClass('active');
        jQuery('.highlighter-view-loading').addClass('shown');
        jQuery('.highlighter-view-note').html('');
        jQuery('.highlighter-view-notes').css('height', 'auto');
        jQuery('.highlighter-view-note-user').html('');
    } else if(jQuery('.highlighter-shield').hasClass('shown')) {
        jQuery('body, html').removeClass('no-scroll');
        jQuery('.highlighter-shield, .highlighter-popup').removeClass('shown available');
        jQuery('.highlighter-confirm').remove(); 
    }
}

// removes the highlight span
function removeHighlight() {
    var postid = jQuery('.highlighter-popup').data('postid'),
        userid = jQuery('.highlighter-content.post-' + postid).data('userid').toString(),
        highlightedSpan = jQuery('.highlighter-popup').data('span'),
        spanids = jQuery(highlightedSpan).attr('data-userid');

    if(typeof spanids != 'undefined') {
        spanids = spanids.toString();
    } else {
        spanids = '';
    }

    // if this is the only user who has highlighted, remove highlight altogether
    if(userid === spanids) {
        jQuery(highlightedSpan).contents().unwrap();  
    } else {
        spanids = spanids.replace(userid + ',', ''); // removes userid from beginning or middle of string
        spanids = spanids.replace(userid, ''); // removes userid from end of string
        spanids = spanids.replace(/^,|,$/g, ''); // removes leftover start or trailing comma
        jQuery(highlightedSpan).attr('data-userid', spanids);
    }
    
    ajaxUpdateContent(postid);
}

// adds [id] to the [attr] of the [span]
function appendAttrId(span, id, attr) {
    var ids = span.attr(attr),
        id = id.toString();

    if(typeof ids != 'undefined') {
        ids = ids.toString();
    } else {
        ids = '';
    }

    // append id to list if not already present
    if (ids.split(",").indexOf(id) === -1) {
        if(ids !== '') {
            ids += ',' + id;
        } else {
            ids = id;
        }
    }
    span.attr(attr, ids);
}

// updates the content with the highlighted selection (span)
function ajaxUpdateContent(postid) {
    // get corresponding post id
    var wrap = jQuery('.highlighter-content.post-' + postid),
        timestamp = wrap.data('timestamp'),
        new_content = wrap.html();

    var timer;

    jQuery.post(highlighterAjax.ajaxurl, {
        action: 'ajax-update-content',
        postid: postid,
        timestamp: timestamp,
        new_content: new_content
        
    }, function (response) {
        jQuery('.highlighter-content.post-' + postid).html(response.new_content);
        //jQuery('.highlighter-content').attr('data-timestamp', response.new_timestamp);
        jQuery('.highlighter-content.post-' + postid).data('timestamp', response.new_timestamp);
        removePopups();

        // need the following timeout or else medium theme in firefox top offsets are all off
        // probably because of block level elements loading in new page content
        clearTimeout(timer);
        // add a slight delay
        timer = setTimeout(function(){
        
            detectHighlights();

        }, 100);
        
    }); 
}

// custom confirm overlay
function highlightConfirm(_this, trigger, yes, cancel) {

    // defaults
    if (yes === undefined) {
        yes = 'yes';
    }
    if (cancel === undefined) {
        cancel = 'cancel';
    }

    var msg = trigger.toLowerCase();
    message = jQuery('.highlighter-content').data(msg);

    // create the modal and elements
    var $confirm = jQuery('<div>', {class: 'highlighter-confirm ' + msg + '-wrapper'});
    var $btn_yes = jQuery('<div>', {class: 'btn-confirm confirm-yes ' + trigger});
    var $btn_no = jQuery('<div>', {class: 'btn-confirm confirm-no'});
    var $message = jQuery('<div>', {class: 'confirm-message'});

    // create the html
    $btn_yes.html(yes);
    $btn_no.html(cancel);
    $message.html(message);
    $confirm.data('span', _this);
    $confirm.append($message);
    $confirm.append($btn_yes);
    $confirm.append($btn_no);

    // add to the dom
    jQuery('html').addClass('no-scroll');
    jQuery('body').addClass('no-scroll');
    jQuery('.highlighter-shield').addClass('shown');

    /*var confirmtimer;
    // add a slight delay so the confirm dialog isn't accidentally
    // triggered by mousedown of triggering element
    // this value should be higher than the css transition duration
    // UPDATE - did away with this since it was causing issues with
    // other js event sequencing
    clearTimeout(confirmtimer);
    confirmtimer = setTimeout(function(){
    
        jQuery('body').append($confirm);

    }, 200);*/
    jQuery('body').append($confirm);

}

// show comment form
function showCommentForm(_this, postid) {
    jQuery('.highlighter-view-wrapper, .highlighter-comment .highlighter-view-loading').removeClass('shown');
    jQuery('.highlighter-comments-wrapper').addClass('shown');
    /*if(!isTouchDevice())*/ 
    jQuery('#highlighter-comment-textarea').val('').css('opacity', 1).focus();
    if(!jQuery('.highlighter-shield').hasClass('shown') && !isTouchDevice()) {
        jQuery('html').addClass('no-scroll');
        jQuery('body').addClass('no-scroll');
        jQuery('.highlighter-shield').addClass('shown');
    }
}

// submit the comment
function ajaxSubmitComment() {

    // define some vars
    var selectors = jQuery('.highlighter-content').data('selectors'),
        wrapper = jQuery('.highlighter-comments-wrapper'), // comment wrapper
        comment = jQuery('#highlighter-comment-textarea'), // comment textarea
        button = jQuery('#highlighter-comment-submit'), // submit button
        respond = jQuery(selectors['respond']), // comment form container
        commentlist = jQuery(selectors['comment-list']), // comment list container
        cancelreplylink = jQuery(selectors['cancel-reply']), // cancel reply link
        userid = jQuery('.highlighter-content').data('userid').toString(),
        loading = jQuery('.highlighter-comment .highlighter-view-loading');


    // validate comment
    if (comment.val().length < 3) {
        wrapper.addClass('error');
        return false;
    } else {
        wrapper.removeClass('error');
    }
    
    // if comment form isn't in process, submit it
    if ( !button.hasClass( 'loadingform' ) && !wrapper.hasClass( 'error' ) ){

        // clone the highlight and add it to the textarea
        comment.css('opacity', 0);
        loading.addClass('shown');
        highlightedSpan = jQuery('.highlighter-popup').data('span');
        var original = jQuery(highlightedSpan);
        var clone = original.clone(true);
        // add userid to original
        appendAttrId(original, userid, 'data-userid-comment');
        clone.removeClass('highlighted-text').addClass('highlighted-text-comment');
        clone = clone.prop('outerHTML');
        comment.val(clone + comment.val()); 

        // only get the form after span has been appended
        var _this = jQuery('#highlighter-comment-form');

        // ajax request
        jQuery.ajax({
            type : 'POST',
            url : highlighterAjax.ajaxurl, // admin-ajax.php URL
            data: jQuery(_this).serialize() + '&action=ajaxcomments', // send form data + action parameter
            beforeSend: function(xhr){
                // what to do just after the form has been submitted
                button.addClass('loadingform').val('Loading...');
            },
            error: function (request, status, error) {
                if( status == 500 ){
                    alert( 'Error while adding comment' );
                } else if( status == 'timeout' ){
                    alert('Error: The server did not respond (timeout)');
                } else {
                    // process WordPress errors
                    var wpErrorHtml = request.responseText.split("<p>"),
                        wpErrorStr = wpErrorHtml[1].split("</p>");

                    alert( wpErrorStr[0] );
                }
            },
            success: function ( response ) {

                var addedCommentHTML = response.comment_html;
                var commentID = response.comment_id;
                //original.attr('data-commentid', commentID);
                appendAttrId(original, commentID, 'data-commentid');

                // let's just use a comment indicator instead
                addedCommentHTML = '<div class="highlighter-new-comment">New comments added (refresh page to view)</div>';

                // if this post already has comments
                if( commentlist.length > 0 ){

                    // if in reply to another comment
                    if( respond.parent().hasClass( 'comment' ) ){

                        // if the other replies exist
                        if( respond.parent().children( '.children' ).length ){  
                            respond.parent().children( '.children' ).append( addedCommentHTML );
                        } else {
                            // if no replies, add <ol class="children">
                            addedCommentHTML = '<ol class="children">' + addedCommentHTML + '</ol>';
                            respond.parent().append( addedCommentHTML );
                        }
                        // close respond form
                        cancelreplylink.trigger("click");
                    } else {
                        // simple comment
                        commentlist.append( addedCommentHTML );
                    }
                }else{
                    // if no comments yet
                    addedCommentHTML = '<ol class="' + selectors['comment-list'] + '">' + addedCommentHTML + '</ol>';
                    respond.before( jQuery(addedCommentHTML) );
                }
                // clear textarea field
                comment.val('');
                comment.css('opacity', 1);
                loading.removeClass('shown');
            },
            complete: function(){
                // what to do after a comment has been added
                button.removeClass( 'loadingform' ).val( 'Respond' );
                var postid = jQuery('.highlighter-popup').data('postid');
                ajaxUpdateContent(postid);
            }
        });
    }
    return false;
}

function ajaxLogin(_this) {
    if (jQuery.isFunction(jQuery.fn.valid)) {
        if (!jQuery(_this).valid()) return false;
    }
    jQuery('p.status', _this).show().text(highlighterAjax.loadingmessage);
    action = 'ajaxlogin';
    username = jQuery('form#login #username').val();
    password = jQuery('form#login #password').val();
    email = '';
    security = jQuery('form#login #security').val();
    if (jQuery(_this).attr('id') == 'register') {
        action = 'ajaxregister';
        username = jQuery('#signonname').val();
        password = jQuery('#signonpassword').val();
        email = jQuery('#email').val();
        security = jQuery('#signonsecurity').val();  
    }  
    ctrl = jQuery(_this);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: highlighterAjax.ajaxurl,
        data: {
            'action': action,
            'username': username,
            'password': password,
            'email': email,
            'security': security
        },
        success: function (data) {
            jQuery('p.status', ctrl).text(data.message);
            if (data.loggedin == true) {
                document.location.href = highlighterAjax.redirecturl;
            }
        }
    });
}

function ajaxForgotPassword(_this) {

    if (jQuery.isFunction(jQuery.fn.valid)) {
        if (!jQuery(_this).valid()) return false;
    }
    jQuery('p.status', _this).show().text(highlighterAjax.loadingmessage);
    ctrl = jQuery(_this);
    jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: highlighterAjax.ajaxurl,
        data: { 
            'action': 'ajaxforgotpassword', 
            'user_login': jQuery('#user_login').val(), 
            'security': jQuery('#forgotsecurity').val(), 
        },
        success: function(data){                    
            jQuery('p.status',ctrl).text(data.message);              
        }
    });

}

if(jQuery.isFunction(jQuery.fn.validate))  {
    if (jQuery("#register").length) 
        jQuery("#register").validate(
            {rules:{
                password2:{ equalTo:'#signonpassword' 
                }   
            }}
        );
    else if (jQuery("#login").length) 
        jQuery("#login").validate();
    if(jQuery('#forgot_password').length)
        jQuery('#forgot_password').validate();
}


/***************************************
END FUNCTIONS
***************************************/








/***************************************
BEGIN RANGY 
***************************************/
var highlighter;

jQuery(window).load(function() {

    rangy.init();

    highlighter = rangy.createHighlighter();

    highlighter.addClassApplier(rangy.createClassApplier("highlighted-text", {
        ignoreWhiteSpace: true,
        elementTagName: "span",
        elementAttributes: {
            'data-userid': jQuery('.highlighter-content').attr('data-userid'),
        }
    }));

    /* 
    highlighter.addClassApplier(rangy.createClassApplier("tweeted-text", {
        ignoreWhiteSpace: true,
        elementTagName: "span",
        elementAttributes: {
            'data-userid': jQuery('.highlighter-content').attr('data-userid'),
        }
    }));

    highlighter.addClassApplier(rangy.createClassApplier("facebook-text", {
        ignoreWhiteSpace: true,
        elementTagName: "span",
        elementAttributes: {
            'data-userid': jQuery('.highlighter-content').attr('data-userid'),
        }
    }));
    */

});

/***************************************
END RANGY 
***************************************/


