/*WP Ajax Edit Comments Editor Interface Script
--Created by Ronald Huereca
--Created on: 05/04/2008
--Last modified on: 03/22/2011
--Relies on jQuery, wp-ajax-edit-comments, wp-ajax-response, thickbox
	
	Copyright 2007,2008  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/
jQuery(document).ready(function() {
var $j = jQuery;
$j.ajaxemail = {
	init: function() { if ( jQuery( 'body.email' ).length <= 0 ) { return; } initialize_events();after_the_deadline();}
};
	//Initializes the edit links
	function initialize_events() {
    //Cancel button
    $j("#cancel,#status a, #close a").bind("click", function() {  parent.jQuery.fn.colorbox.close();
    return false; });
		$j("#send").bind("click", function() {
			//Clear errors
			$j("#to, #from, #subject, #message, #status").removeClass("error");
			$j("#status_message").html('');
			//Perform validation
			 if (do_validation()) {
				email_comment(); 		
			 }
		});
  	} //end initialize_events
  	function after_the_deadline() {
		if (wpajaxeditcommentedit.AEC_AftertheDeadline == 'false') { return; }
		AtD.rpc_css_lang = wpajaxeditcommentedit.AEC_AftertheDeadline_lang;
		$j('#message').addProofreader();
		var spellcheck = $j("#AtD_0").clone(true);
		$j("#AtD_0").remove();
		$j("#edit_options").append(spellcheck);
	}
	//Checks all fields for validation
	function do_validation() {
		//After the deadline - Move this to function email_comment after performing AJAX validation instead
		 if (wpajaxeditcommentedit.AEC_AftertheDeadline == 'true') {
			 $j(".AtD_edit_button").trigger("click");
		 }
		//Check to see if fields are empty
		var to = $j.trim($j("input[name='to']").attr("value"));
		var from = $j.trim($j("select[name='from']").attr("value"));
		var subject = $j.trim($j("#subject").attr("value"));
		var message = $j.trim($j("#message").attr("value"));
		var error = false;
		//Check to see if fields are empty
		if (to == "") { $j("#to").addClass("error"); error = true;}
		if (from == "") { $j("#from").addClass("error"); error = true;}
		if (subject == "") { $j("#subject").addClass("error"); error = true;}
		if (message == "") { $j("#message").addClass("error"); error = true;}
		
		if (error) {
			$j("#status").removeClass();
			 $j("#status").addClass("error");
			 $j("#status_message").html(wpajaxeditcommentedit.AEC_fieldsrequired);
			 return false;
		}
		return true;
	}
	function email_comment() {
		//Pre-process data
		var nonce = $j("#_wpnonce").attr("value");
		var cid = parseInt($j("#commentID").attr("value"));
		var pid = parseInt($j("#postID").attr("value"));
		var action = $j("#action").attr("value");
		var to = encodeURIComponent($j.trim($j("#to").attr("value")));
		var from = encodeURIComponent($j.trim($j("#from").attr("value")));
		var subject = encodeURIComponent($j.trim($j("#subject").attr("value")));
		var message = encodeURIComponent($j.trim($j("#message").attr("value")));
		//Change the edit text and events
		$j("#status").show();
		$j("#status").attr("class", "success");
		$j("#status_message").html(wpajaxeditcommentedit.AEC_Sending);
		
		jQuery.post( ajaxurl, { _ajax_nonce:nonce,action:action, cid: cid,pid:pid,to:to,from:from,subject:subject,message:message },
function(response){
	if ( typeof response.error != "undefined" ) { //error
		$j("#status").removeClass();
		$j("#status").addClass("error");
		$j("#to").addClass("error");
		$j("#status_message").html(wpajaxeditcommentedit.AEC_emailaddresserror);
	} else {
		$j("#status_message").html(wpajaxeditcommentedit.AEC_Sent);
		parent.jQuery.fn.colorbox.close();
	}
}, 'json' );
  } //end email_comment
	$j.ajaxemail.init(); 
	$j("body").attr("style", "display: block;");
});
