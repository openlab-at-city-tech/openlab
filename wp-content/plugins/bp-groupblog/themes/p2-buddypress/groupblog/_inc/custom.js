jQuery(document).ready(function($) {
	$('#create_post').click(function() {
		$('form#new_post').slideToggle(300);
	return false;
	});

	jq("#whats-new-options").animate({height:'40px'});
	jq("form#whats-new-form textarea").animate({height:'50px'});

	function limitChars(textid, limit, infodiv) {
	  var text = $('#'+textid).val();
	  var textlength = text.length;
	  if(textlength > limit) {
	    $('#' + infodiv).html('<span>0</span>');
	    //$('#'+textid).val(text.substr(0,limit));
	    return false;
	    } else {
	    $('#' + infodiv).html(limit - textlength);
	    return true;
	  }
	}

	$('#post_cat').val($('#post-types a.selectted').attr('id'));
	$('#post-types a').click(function(e) {
		$('.post-input').hide();
		$('#post-types a').removeClass('selected');
		$(this).addClass('selected');
		var $id = $(this).attr('id');
		$('#whats-new-post').removeClass('status post photo video featured').addClass($id);
		$('#whats-new-textarea').removeClass('status post photo video featured').addClass($id);

		if($(this).attr('id') != 'status') {
			$('#whats-new-status').hide();
			$('#char-count').hide();
			$('.post-input').show();
			$('#media-buttons').show();
		} else {
			$('#whats-new-status').fadeIn(300);
			$('#char-count').show();
			$('#media-buttons').hide();
		}

		if ( $('#whats-new-post').hasClass('.status') ) {
			$('#submit').val('Post Update');
		} else if ( $('#whats-new-post').hasClass('.post') ) {
			$('#posttitle').val('Enter a title for your Post...').attr('title', 'Enter a title for your Post...');
			$('#submit').val('Create Post');
		}	else if ( $('#whats-new-post').hasClass('.photo') ) {
			$('#posttitle').val('Enter a title for your Photo...').attr('title', 'Enter a title for your Photo...');
			$('#submit').val('Post Photo');
		}	else if ( $('#whats-new-post').hasClass('.video') ) {
			$('#posttitle').val('Enter a title for your Video...').attr('title', 'Enter a title for your Video...');
			$('#submit').val('Post Video');
		}	else if ( $('#whats-new-post').hasClass('.featured') ) {
			$('#posttitle').val('Enter a title for your Featured Post...').attr('title', 'Enter a title for your Featured Post...');
			$('#submit').val('Feature This');
		}

		$('#post_cat').val($(this).attr('id'));
		return false;

	});

	$('#posttext').click(function() {
		$(this).focus().keyup(function() {
  		limitChars('posttext', 140, 'counter');
		});
	});

	$('#posttitle').focus(function () {
		if ($(this).val() === $(this).attr('title')) {
			$(this).val('');
		}
	}).blur(function () {
		if ($(this).val() === '') {
			$(this).val($(this).attr('title'));
		}
	});

	// This is an exact copy from inc/global.js.
	// We only change the content id and submit id to match the P2 post form.
	// We try to leave the P2 framework in tact while changes the BP javascript
	// in a custom file here. It seems to work BUT we need to send more variables.
	// For example Title and possibly a blog id, to extract group id?
	//
	// This function is important in regards to the function bp_p2_post_update() in
	// our functions.php. At least we think so.
	// AJAX Functions

	/**** Activity Posting ********************************************************/

	/* New posts */
	$("input#submit").click( function() {
		var button = $(this);
		var form = button.parent().parent().parent().parent();

		form.children().each( function() {
			if ( $.nodeName(this, "textarea") || $.nodeName(this, "input") )
				$(this).attr( 'disabled', 'disabled' );
		});

		$( 'form#' + form.attr('id') + ' span.ajax-loader' ).show();

		/* Remove any errors */
		$('div.error').remove();
		button.attr('disabled','disabled');

		/* Default POST values */
		var object = '';
		var item_id = $("#whats-new-post-in").val();
		var content = $("textarea#posttext").val();

		/* Set object for non-profile posts */
		if ( item_id > 0 ) {
			object = $("#whats-new-post-object").val();
		}

		$.post( ajaxurl, {
			action: 'p2_post_update',
			'cookie': encodeURIComponent(document.cookie),
			'_wpnonce_p2_post_update': $("input#_wpnonce_p2_post_update").val(),
			'content': content,
			'object': object,
			'item_id': item_id
		},
		function(response)
		{
			$( 'form#' + form.attr('id') + ' span.ajax-loader' ).hide();

			form.children().each( function() {
				if ( $.nodeName(this, "textarea") || $.nodeName(this, "input") )
					$(this).attr( 'disabled', '' );
			});

			/* Check for errors and append if found. */
			if ( response[0] + response[1] == '-1' ) {
				form.prepend( response.substr( 2, response.length ) );
				$( 'form#' + form.attr('id') + ' div.error').hide().fadeIn( 200 );
				button.attr("disabled", '');
			} else {
				if ( 0 == $("ul.activity-list").length ) {
					$("div.error").slideUp(100).remove();
					$("div#message").slideUp(100).remove();
					$("div.activity").append( '<ul id="activity-stream" class="activity-list item-list">' );
				}

				//$("ul.activity-list").prepend(response);
				//$("ul.activity-list li:first").addClass('new-update');
				//$("li.new-update").hide().slideDown( 300 );
				//$("li.new-update").removeClass( 'new-update' );
				//$("textarea#posttext").val('');

				/* Re-enable the submit button after 8 seconds. */
				setTimeout( function() { button.attr("disabled", ''); }, 8000 );
			}
		});

	});

});

// Expanding Textareas plugin - licensed under the MIT
// https://github.com/bgrins/ExpandingTextareas
(function(e){if(typeof define==="function"&&define.amd){define(["jquery"],e)}else{e(jQuery)}})(function(e){function s(){e(this).closest(".expandingText").find("div").text(this.value.replace(/\r\n/g,"\n")+" ");e(this).trigger("resize.expanding")}e.expandingTextarea=e.extend({autoInitialize:true,initialSelector:"textarea.expanding",opts:{resize:function(){}}},e.expandingTextarea||{});var t=["lineHeight","textDecoration","letterSpacing","fontSize","fontFamily","fontStyle","fontWeight","textTransform","textAlign","direction","wordSpacing","fontSizeAdjust","wordWrap","word-break","borderLeftWidth","borderRightWidth","borderTopWidth","borderBottomWidth","paddingLeft","paddingRight","paddingTop","paddingBottom","marginLeft","marginRight","marginTop","marginBottom","boxSizing","webkitBoxSizing","mozBoxSizing","msBoxSizing"];var n={position:"absolute",height:"100%",resize:"none"};var r={visibility:"hidden",border:"0 solid",whiteSpace:"pre-wrap"};var i={position:"relative"};e.fn.expandingTextarea=function(o){var u=e.extend({},e.expandingTextarea.opts,o);if(o==="resize"){return this.trigger("input.expanding")}if(o==="destroy"){this.filter(".expanding-init").each(function(){var t=e(this).removeClass("expanding-init");var n=t.closest(".expandingText");n.before(t).remove();t.attr("style",t.data("expanding-styles")||"").removeData("expanding-styles")});return this}this.filter("textarea").not(".expanding-init").addClass("expanding-init").each(function(){var o=e(this);o.wrap("<div class='expandingText'></div>");o.after("<pre class='textareaClone'><div></div></pre>");var a=o.parent().css(i);var f=a.find("pre").css(r);o.data("expanding-styles",o.attr("style"));o.css(n);e.each(t,function(e,t){var n=o.css(t);if(f.css(t)!==n){f.css(t,n)}});o.bind("input.expanding propertychange.expanding keyup.expanding",s);s.apply(this);if(u.resize){o.bind("resize.expanding",u.resize)}});return this};e(function(){if(e.expandingTextarea.autoInitialize){e(e.expandingTextarea.initialSelector).expandingTextarea()}})})

// initialize all expanding textareas
jQuery(document).ready(function($) {
	$("textarea[class*=expand]").expandingTextarea();
});