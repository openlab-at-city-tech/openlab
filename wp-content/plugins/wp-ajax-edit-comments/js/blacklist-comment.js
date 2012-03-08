/*WP Ajax Edit Comments Editor Interface Script
--Created by Ronald Huereca
--Created on: 05/04/2008
--Last modified on: 10/25/2008
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
$j.ajaxblacklistcomment = {
	init: function() { if ( jQuery( '.blacklist' ).length <= 0 ) { return; } initialize(); }
};
	//Initializes the edit links
	function initialize() {
  	//Read in cookie values and adjust the toggle box
    //Cancel button
    $j("#cancel,#status a, #close a").bind("click", function() {  parent.jQuery.fn.colorbox.close();
    return false; });
  	//Pre-process data
	var data = {};
    data._ajax_nonce = $j("#_wpnonce").val();
    data.cid = parseInt($j("#commentID").val());
    data.pid = parseInt($j("#postID").val());
    data.action = $j("#action").val();
    
  	//Change the edit text and events
		//Send button event
  	$j("#send-request").bind("click", function() { submit_blacklist( data ); return false; });
		$j("#status").show();
		$j("#status").attr("class", "success");
		$j("#message").html(wpajaxeditcommentedit.AEC_Ready);
	}
  function submit_blacklist(data) {
  	//Update status message
    $j("#status").attr("class", "success");
    $j("#message").html(wpajaxeditcommentedit.AEC_Blacklisting);
    $j("#send-request").attr("disabled", "disabled");
	var parameters = '';
	var length = $j("input:checked").length;
    $j.each($j("input:checked"), function() {
				length -= 1;
				parameters += $j(this).val();
				if (length > 0) { parameters += ",";}
		});
		data = $j.extend( data, { parameters: parameters });
    	jQuery.post( ajaxurl, data, 
		function( response ) {
			$j("#send-request").removeAttr("disabled");
			if ( typeof response.error != "undefined" ) {
				$j("#status").attr("class", "error");
				$j("#message").html( response.error );
				return;
			}
			self.parent.jQuery("#edit-comment-admin-links" + data.cid).html(response.comment_links);
			self.parent.jQuery(".spam-count").html(response.spam_count);
			self.parent.jQuery(".pending-count").html(response.moderation_count);
			$j("#send-request,#cancel").remove();
			$j("#message").html(response.message);
		}, 'json' ); //end jquery post
  } //end submit_blacklist
	$j("body").attr("style", "display: block;");
	$j.ajaxblacklistcomment.init();
});