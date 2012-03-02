/*WP Ajax Edit Comments Editor Interface Script
--Created by Ronald Huereca
--Created on: 05/04/2008
--Last modified on: 01/07/2010
--Relies on jQuery, wp-ajax-edit-comments, wp-ajax-response, thickbox
	
	Copyright 2007-2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)

*/
jQuery(document).ready(function() {
var $j = jQuery;
$j.ajaxrequestdeletion = {
	init: function() { if ( jQuery( 'body.request-deletion' ).length <= 0 ) { return; } initialize(); after_the_deadline(); }
};
	function after_the_deadline() {
		if (wpajaxeditcommentedit.AEC_AftertheDeadline == 'false') { return; }
		if ( typeof AtD.rpc_css_lang != "undefined" ) {
			AtD.rpc_css_lang = wpajaxeditcommentedit.AEC_AftertheDeadline_lang;
			$j('#deletion-reason').addProofreader();
		}
	}
	//Initializes the edit links
	function initialize() {
  	//Read in cookie values and adjust the toggle box
    //Cancel button
    $j("#cancel,#status a, #close a").bind("click", function() {  parent.jQuery.fn.colorbox.close();
    return false; });
  	//Pre-process data
	var data = {};
  	data.cid = parseInt($j("#commentID").val());
    data.pid = parseInt($j("#postID").val());
    data.action = $j("#action").val();
  	data._ajax_nonce = $j("#_wpnonce").val();
    
  	//Change the edit text and events
    $j("#status").show();
    $j("#status").attr("class", "success");
  	$j("#message").html(wpajaxeditcommentedit.AEC_Ready);
		//Send button event
  	$j("#send-request").bind("click", function() { send_request( data ); return false; });
	}
  function send_request(data) {
	 //After the deadline - 
	 if (wpajaxeditcommentedit.AEC_AftertheDeadline == 'true') {
		 $j(".AtD_edit_button").trigger("click");
	 }
  	//Update status message
	data.message = encodeURIComponent($j("#deletion-reason").val());
	
    $j("#status").attr("class", "success");
    $j("#message").html(wpajaxeditcommentedit.AEC_Sending);
    $j("#send-request").attr("disabled", "disabled");
		
	jQuery.post( ajaxurl, data, 
	function( response ) {
		$j("#message").html(wpajaxeditcommentedit.AEC_RequestDeletionSuccess);
		//if response error
		if ( typeof response.error != "undefined" ) {
			$j("#message").html(wpajaxeditcommentedit.AEC_RequestError);
			$j("#status").attr("class", "error");
			return;
		}
		try {
			self.parent.jQuery("#comment-undo-" + response.cid).html(wpajaxeditcommentedit.AEC_RequestDeletionSuccess);
			self.parent.jQuery("#edit" + response.cid).unbind();
			self.parent.jQuery("#edit-comment-user-link-" + response.cid).remove();
			//close thickbox
		  parent.jQuery.fn.colorbox.close();
		} catch(err) {}
	}, 'json'); //end ajax
    
  } //end send_request
	$j.ajaxrequestdeletion.init();
});