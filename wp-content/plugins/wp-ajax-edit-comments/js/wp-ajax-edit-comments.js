/*WP Ajax Edit Script
--Created by Ronald Huereca
--Created on: 03/28/2007
--Last modified on: 01/07/2010
--Relies on jQuery, wp-ajax-response, colorbox
	Copyright 2007-2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)
*/
jQuery(document).ready(function() {
var $j = jQuery;
$j.ajaxeditcomments = {
	init: function() { $j.extend($j.ajaxeditcomments.vars, { timers: {}, timerObjs: {}}); initialize_links();  },
	delink: function(obj) { $j(".aec-undo-span").html('');_delink($j(obj));},
	move: function(obj) { $j(".aec-undo-span").html('');return _thickbox($j(obj));},
	edit: function(obj) { $j(".aec-undo-span").html('');return _thickbox($j(obj));},
	request_deletion: function(obj) { $j(".aec-undo-span").html('');return _thickbox($j(obj));},
	request_delete: function(obj) { $j(".aec-undo-span").html(''); _request_delete_comment($j(obj)); return false;},
	email: function(obj) { $j(".aec-undo-span").html(''); return _thickbox($j(obj));},
	blacklist_comment: function(obj) { $j(".aec-undo-span").html(''); return _thickbox($j(obj));},
	approve: function(obj) { $j(".aec-undo-span").html('');_approve($j(obj));},
	spam: function(obj) { 
		$j(".aec-undo-span").html('');
		_spam($j(obj));},
	moderate: function(obj) { $j(".aec-undo-span").html('');_moderate($j(obj));},
	delete_comment: function(obj) { $j(".aec-undo-span").html('');_delete_comment($j(obj));},
	deleteperm_comment: function(obj) { $j(".aec-undo-span").html('');_deleteperm_comment($j(obj));},
	restore_comment: function(obj) { $j(".aec-undo-span").html('');_restore_comment($j(obj));},
	remove_comment: function(id) { 
		var li = $j("#" + "comment-" + id);
		if (li.is("li") || li.is("div") ) {
			li.addClass("ajax-delete");
			li.slideUp(1000, function() { li.remove(); });
		}
	},
	retrieve_element: function(id) {
		return $j("#" + id);
	},
	update_comment: function(id, content) {
  	$j("#" + id).html(content);
	},
	update_author: function(id, author, url) {
		delinkid = id.match(/\d+$/)[0];
		if ( url == '' || 'http://' == url ) {
			$j(".aec-delink-" + delinkid + ",#delink-comment-" + delinkid).hide();
			if (author == '') { $j("#" + id).html(wpajaxeditcomments.AEC_Anon); return; }
			$j("#" + id).html(author);
		} else if (author == '') {
			$j("#" + id).html(wpajaxeditcomments.AEC_Anon);
		} else {
			$j("#" + id).html("<a href='" + url + "'>" + author + "</a>");
			$j(".aec-delink-" + delinkid + ",#delink-comment-" + delinkid).show();
		}
	},
	update_date_or_time: function(id, date_or_time) {
		$j("#" + id).html(date_or_time);
	},
	remove_element: function(id) {
		var li = $j(id);
		if (li.is("li") || li.is("div") ) {
			li.addClass("ajax-unapprove");
			li.slideUp(1000, function() { li.remove(); });
		}
	},
	undo_message: function(comment_id,message, undolink) { /*undo is a boolean.  True if undo link, false if none*/
		$j("#comment-undo-" + comment_id).html(message);
		if (undolink == true) {
			$j(".aec-undo-link").unbind("click");
			$j(".aec-undo-link").bind("click", function() { 
				$j("#comment-undo-" + comment_id).html(wpajaxeditcomments.AEC_undoing); 
				undo($j(this)); 
				return false; });
		}
	},
	dropdown: function(obj) {
		obj = $j(obj);
		var url = unserialize(obj.attr('href'));
		add_dropdown(url.cid);
		return false;
	},
	vars: {},
	get_timer : function(obj) {
		get_time_left_timer(obj);
	}
};
	var vars = $j.ajaxeditcomments.vars;
	//Adds the drop down box
	function add_dropdown(cid) {
		//Move links relative to the document
		$j("body").append("<span class='aec-dropdown-container' id='aec-dropdown-container-" + cid + "'>" + $j("#aec-dropdown-" + cid).html() + "</span>");
		//Set up position for drop down menu
		$j("#aec-dropdown-container-" + cid).css("top",parseInt($j("#aec-dropdownlink-" + cid).offset().top) + parseInt($j("#aec-dropdownlink-" + cid).outerHeight())+ 5 + "px");
		
		$j("#aec-dropdown-container-" + cid).css("left",$j("#aec-dropdownlink-" + cid).offset().left);		
		//$j("#aec-dropdown-container-" + cid).css("width",$j("#aec-dropdownarrow-" + cid).width()*2);
		
		$j("#aec-dropdownlink-text-" + cid).text(wpajaxeditcomments.AEC_LessOptions);
		$j("#aec-dropdownlink-" + cid).removeAttr("onclick");
		if ($j.support.noCloneEvent != false) {
			//Show the drop down menu
			$j("#aec-dropdownlink-" + cid).removeClass("aec-dropdownlink");
			$j("#aec-dropdownlink-" + cid).addClass("aec-dropdownlink-less");
		}
		
		$j("#aec-dropdownlink-" + cid).bind("click", function() { return false; });
		$j("#aec-dropdown-container-" + cid).slideDown("", function() { 
			if ($j.support.noCloneEvent == false) { //for IE 7
				//Show the drop down menu
				$j("#aec-dropdownlink-" + cid).removeClass("aec-dropdownlink");
				$j("#aec-dropdownlink-" + cid).addClass("aec-dropdownlink-less");
			}
			//Assign events
			//Provide focus to the dropdown box
			$j("#aec-dropdown-container-" + cid).focus();
			
			$j("#aec-dropdownlink-" + cid).unbind("mouseup"); //mouseup is for IE
			$j("#aec-dropdownlink-" + cid).bind("mouseup", function() { 
				remove_dropdown(cid);
				return false;
			});
			
			
			//Provide focus to the dropdown box
			$j("#aec-dropdown-container-" + cid).focus();	
			
			
			
		});
		
		
	};
	//Removes the drop down box
	function remove_dropdown(cid) {
		if ($j(".aec-dropdown-container").length == 0) { return false; }
		$j("#aec-dropdownlink-text-" + cid).text(wpajaxeditcomments.AEC_MoreOptions);
			//Remove the wrapper container and place links in original spot
			$j("#aec-dropdown-" + cid).html($j("#aec-dropdown-container-" + cid).html());
			$j("#aec-dropdown-container-" + cid).slideUp("", function() { 
					$j("#aec-dropdown-container-" + cid).remove();
					$j("#aec-dropdownlink-" + cid).removeClass("aec-dropdownlink-less");
					$j("#aec-dropdownlink-" + cid).addClass("aec-dropdownlink");
				
					//Assign events
					$j("#aec-dropdownlink-" + cid).unbind("blur");
					$j("#aec-dropdown-container-" + cid).unbind("blur");
					$j("#aec-dropdown-container-" + cid).unbind("mouseleave");
					$j("#aec-dropdownlink-" + cid).unbind("mouseup");
					$j("#aec-dropdownlink-" + cid).bind("mouseup", function() { 																									
						add_dropdown(cid);
						return false;
					});																																																					
			});	
	};
	function undo(obj) {
		var data = pre_process($j(obj));
		$j(".undo" + data.cid).parent().html('');
		data.action = 'undo';

//post
		jQuery.post( ajaxurl, data, 
			function( response ) {
				if ( typeof response.error != "undefined" ) { alert( response.error ); return; }
				$j("#comment-undo-" + data.cid).html(wpajaxeditcomments.AEC_undosuccess);
				var comment = response.content;
				var name = response.comment_author;
				var url = response.comment_author_url;
				var date = response.comment_date;
				var time = response.comment_time;
				jQuery.ajaxeditcomments.update_comment("edit-comment" + data.cid,comment);
				jQuery.ajaxeditcomments.update_author("edit-author" + data.cid,name, url);
				jQuery.ajaxeditcomments.update_date_or_time("aecdate" + data.cid,date);
				$j("#edit-comment-admin-links" + data.cid).html(response.comment_links);
				//for the admin panel
				$j("#comment-" + data.cid + " .comment-count").html(response.approve_count);
				$j(".spam-count").html(response.spam_count);
				$j(".pending-count").html(response.moderation_count);
			}, 'json' );
		
	}; //end undo
	
	//Initializes the edit links
	function initialize_links() {
  	//Leave the style in for Safari
  	$j(".edit-comment-admin-links").css("display", "block");
    $j(".edit-comment-user-link").css("display", "block");
		$j(".hidden").hide();
    /* For Crappy IE */
    $j(".edit-comment-admin-links").show();
    $j(".edit-comment-user-link").show();
    if (wpajaxeditcomments.AEC_CanScroll == "1") {
      var location = "" + window.location;
      var pattern = /(#[^-]*\-[^&]*)/;
      if (pattern.test(location)) {
        location = $j("" + window.location.hash);
        var targetOffset = location.offset().top;
        $j('html,body').animate({scrollTop: targetOffset}, 1000);
      }
    }
   get_time_left();
  };
	//Finds an area (if applicable) and displays the time left to comment
  function get_time_left() {
	 var numTimers = $j("." + "ajax-edit-time-left").length;
	 var timers = new Array();
	 var i = 0;
  	$j("." + 'ajax-edit-time-left').each(function() { 
    	data = pre_process($j(this).prev());
    	data = $j.extend( data,{ action: 'getthetimeleft', cid: data.cid,pid:data.pid, _ajax_nonce: data.nonce });
    	data.action = 'getthetimeleft';
		jQuery.post( ajaxurl, data, 
function ( response ) { 
			i += 1;
			var cid = response.cid;
			if ( typeof response.error != "undefined" || typeof response.success != "undefined" ) { //error
				return;
			}
			var minutes = parseInt( response.minutes );
			var seconds = parseInt( response.seconds );

			var element = $j("#ajax-edit-time-left-" + response.cid);
			var timer = {minutes: minutes, seconds: seconds, cid: cid, element: element};
			timers[$j(timers).length] = timer;
			if (i == numTimers) {
				vars.timers = setInterval(function() {jQuery.ajaxeditcomments.get_timer(timers)}, 1000);
			}
}, 'json' ); //end ajax
    }); //end jquery each
  }; //end get_time_left
	//Updates the UI with the correct time left to edit
  //Parameters - timer (obj with timer data)
  function get_time_left_timer(timers) {
	  var timerArr = new Array();
	  if ($j(timers).length == 0) {
		clearTimeout(vars.timers);  
		return;
	  }
	 $j(timers).each(function() {
		seconds = this.seconds - 1;
		minutes = this.minutes;
		element = this.element; 
		//Check to see if the time has run out
		if (minutes <=0 && seconds <= 0) { 
			//timers = timers.slice(i,i+1);
			$j("#edit" + this.cid).unbind();
			element.remove();
			$j("#edit-comment-user-link-" + this.cid).remove();
			
			//Remove the colorbox if applicable
			try {
				//This try statement is for the iFrame
				//Iframe code from:  http://xkr.us/articles/dom/iframe-document/
				if (document.getElementById('cboxIframe') != undefined) {
					var oIframe = document.getElementById('cboxIframe');
					var oDoc = (oIframe.contentWindow || oIframe.contentDocument);
					if (oDoc.document) oDoc = oDoc.document;
					if ($j("#timer" + this.cid, oDoc).length > 0) {
						jQuery.fn.colorbox.close(); //for iframe
					}
				}
			} catch(err) { }
		} else {
			timerArr[$j(timerArr).length] = this;
			if (seconds < 0) { minutes -= 1; seconds = 59; }
				//Create timer text
				var text = "";
				if (minutes >= 1) {
				if (minutes >= 2) { text = minutes + " " + wpajaxeditcomments.AEC_Minutes; } else { text = minutes + " " + wpajaxeditcomments.AEC_Minute; }
				if (seconds > 0) { text += " " + wpajaxeditcomments.AEC_And + " "; }
				}
				if (seconds > 0) {
				if (seconds >= 2) { text += seconds + " " + wpajaxeditcomments.AEC_Seconds; } else { text += seconds + " " + wpajaxeditcomments.AEC_Second; }
			}
			//Output the timer to the user
			try {
				//This try statement is for the iFrame
				//Iframe code from:  http://xkr.us/articles/dom/iframe-document/
				if (document.getElementById('cboxIframe') != undefined) {
					var oIframe = document.getElementById('cboxIframe');
					var oDoc = (oIframe.contentWindow || oIframe.contentDocument);
					if (oDoc.document) oDoc = oDoc.document;
					$j("#timer" + this.cid, oDoc).html("&nbsp;(" + text + ")");
				}
			} catch(err) { }
			$j("#ajax-edit-time-left-" + this.cid).html("&nbsp;(" + text + ")");
			this.seconds = seconds;
			this.minutes = minutes;
		} //end if
	}); //end each
	clearTimeout(vars.timers);  
	vars.timers = setInterval(function() {jQuery.ajaxeditcomments.get_timer(timerArr)}, 1000);
  };
  //Returns a data object for ajax calls
  function pre_process(element) {
  	var data = {};
    var url = unserialize(element.attr('href'));
	data.aecurl = url;
    data._ajax_nonce = url._wpnonce;
    data.cid = url.cid;
    data.pid = url.pid;
    data.action = url.action;
    return data;
  };
	function _delink(obj) {
		var data = pre_process($j(obj));
		
		remove_dropdown(data.cid);
		//undo message
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_delinking, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			if ( typeof response.error != "undefined" ) { alert( response.error ); return; }
			//For in the admin panel
			var content = response.content;
			$j.ajaxeditcomments.update_comment("edit-comment" + data.cid,content);
			if ( response.comment_author_url == '') {
				$j("#the-comment-list #comment-" + data.cid + " A:first").html("");
				//For on a post
				$j(".aec-delink-" + data.cid + ",#delink-" + data.cid).hide();
				$j("#edit-author" + data.cid).html($j("#edit-author" + data.cid + " A").html()) //for on a post
				
				//undo message
				jQuery.ajaxeditcomments.undo_message(data.cid, response.undo, true);
			} 
		}, 'json' );
	};

	function _thickbox( obj ) {
		obj = $j( obj );

		//try to detect mobile browsers
		var is_mobile = false,
			uagent    = navigator.userAgent.toLowerCase();

		try {

			if ( uagent.search( 'iphone' ) > - 1 ) {
				is_mobile = true;
			}
			if ( uagent.search( 'ipod' ) > - 1 ) {
				is_mobile = true;
			}
			if ( uagent.search( 'webkit' ) > - 1 ) {
				if ( uagent.search( 'series60' ) > - 1 ) {
					if ( uagent.search( 'symbian' ) > - 1 ) {
						is_mobile = true;
					}
				}
			}
			if ( uagent.search( 'android' ) > - 1 ) {
				is_mobile = true;
			}
			if ( uagent.search( 'windows ce' ) > - 1 ) {
				is_mobile = true;
			}
			if ( uagent.search( 'blackberry' ) > - 1 ) {
				is_mobile = true;
			}
			if ( uagent.search( 'palm' ) > - 1 ) {
				is_mobile = true;
			}
		} catch ( err ) {
		}
		var data = pre_process( obj );

		remove_dropdown( data.cid );

		$j( 'a#' + obj.attr( 'id' ) ).colorbox( {
			iframe: true,
			scrolling: false,
			width: is_mobile ? '100%' : wpajaxeditcomments.AEC_colorbox_width,
			height: wpajaxeditcomments.AEC_colorbox_height,
			opacity: 0.6
		} );

		//$j("a#" + obj.attr("id") + ":first").trigger("click.colorbox"); //Getting rid of this stops the stack overflow issues

		return false;
	};
	function _update_comment_interface( response, comment_id ) {
		if ( typeof response.error != "undefined" ) { alert( response.error ); return; }
		$j("#edit-comment-admin-links" + comment_id).html( response.comment_links);					
		$j("#comment-" + comment_id + " .comment-count").html( response.approve_count);
		$j(".spam-count").html( response.spam_count);
		$j(".pending-count").html( response.moderation_count);	
		$j(".trash-count").html( response.trash_count);	
		//undo message
		jQuery.ajaxeditcomments.undo_message( comment_id, response.undo,true);
	}
	function _approve(obj) {
		var data = pre_process($j(obj)); 
		remove_dropdown(data.cid);
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_approving, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			_update_comment_interface( response, data.cid );
		}, 'json' );
		
	};
	function _spam(obj) {
		var data = pre_process($j(obj));
		remove_dropdown(data.cid);
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_spamming, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			_update_comment_interface( response, data.cid );
		}, 'json' );
	};
	function _moderate(obj) {
		var data = pre_process($j(obj));
		remove_dropdown(data.cid);
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_moderating, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			_update_comment_interface( response, data.cid );
		}, 'json' );
	};
	function _delete_comment(obj) {
		var data = pre_process($j(obj));
		remove_dropdown(data.cid);
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_deleting, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			$j(".aec-delete-" + data.cid).hide();
			$j("#delete-" + data.cid).hide();
			_update_comment_interface( response, data.cid );
			$j("#edit-comment-admin-links" + data.cid).html('');
		}, 'json' );
	};
	function _request_delete_comment(obj) {
		var data = pre_process($j(obj));
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_Sure+" <a href='#' id='aecconfirmyes"+data.cid+"'>"+wpajaxeditcomments.AEC_Yes+"</a> - <a href='#' id='aecconfirmno"+data.cid+"'>"+wpajaxeditcomments.AEC_No+"</a>", false);
		$j("#aecconfirmyes" + data.cid).bind("click", function() {
			jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_deleting, false);
			jQuery.post( ajaxurl, data, 
		function( response ) {
				if ( typeof response.error != "undefined" ) { $j("#comment-undo-" + response.cid).html(''); alert( response.error ); return; }
				$j("#comment-undo-" + response.cid).html(wpajaxeditcomments.AEC_permdelete);
				$j("#edit" + response.cid).unbind();
				$j("#edit-comment-user-link-" + response.cid).remove();
			 }, 'json' );
			return false;
		});	
		$j("#aecconfirmno" + data.cid).bind("click", function() {
			jQuery.ajaxeditcomments.undo_message(data.cid, '', false);
			return false;
		});
	}
	function _deleteperm_comment(obj) {
		var data = pre_process($j(obj));
		remove_dropdown(data.cid);
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_deleting, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			$j( "#comment-undo-" + data.cid ).remove();
			$j("#edit-comment-admin-links" + data.cid).html( wpajaxeditcomments.AEC_permdelete );
		}, 'json' );
	};
	function _restore_comment(obj) {
		var data = pre_process($j(obj));
		jQuery.ajaxeditcomments.undo_message(data.cid, wpajaxeditcomments.AEC_restoring, false);
		jQuery.post( ajaxurl, data, 
		function( response ) {
			$j( "#comment-undo-" + data.cid ).remove();
			$j("#edit-comment-admin-links" + data.cid).html( wpajaxeditcomments.AEC_restored );
		}, 'json' );
	};
	function unserialize( s ) {
		var r = {}, q, pp, i, p;
		if ( !s ) { return r; }
		q = s.split('?'); if ( q[1] ) { s = q[1]; }
		pp = s.split('&');
		for ( i in pp ) {
			if ( jQuery.isFunction(pp.hasOwnProperty) && !pp.hasOwnProperty(i) ) { continue; }
			p = pp[i].split('=');
			r[p[0]] = p[1];
		}
		return r;
	};
	$j.ajaxeditcomments.init();
});