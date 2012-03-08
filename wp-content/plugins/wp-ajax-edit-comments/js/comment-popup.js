/*WP Ajax Edit Comments Pop-up Script
--Created by Ronald Huereca
--Created on: 02/18/2010
--Last modified on: 02/18/2010
--Relies on jQuery, wp-ajax-edit-comments, wp-ajax-response, thickbox
	
	Copyright 2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)

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
$j.ajaxcommentpopup = {
	init: function() { if ( jQuery( '#aec-popup' ).length <= 0 ) { return; } close_event();  after_the_deadline(); buttons(); commentbox_height(); fill_commentbox(); $j("#comment").focus(); },
	closed: function() {
		if (aec_popup.atd == 'true') { $j(".AtD_edit_button").trigger("click"); }
		parent.jQuery("#comment").attr("value", $j("#comment").attr("value"));
		parent.jQuery("#comment").focus();
	}
};
	function buttons() {
		$j("#aec_edit_options").append("<span class='aec_retract'></span>");
		$j("#close, .aec_retract").bind("click",function() {  
		 	parent.jQuery.fn.colorbox.close();
		 });	
		$j("#submit").bind("click",function() { 
			if (aec_popup.atd == 'true') { $j(".AtD_edit_button").trigger("click"); }								
		 	parent.jQuery("#comment").attr("value", $j("#comment").attr("value"));
			parent.jQuery("#commentform input[name='submit']").trigger("click");
		 });
	}
	function close_event() {
		parent.jQuery(".aec_expand").bind("cbox_cleanup", function() { 
			$j.ajaxcommentpopup.closed();
			parent.jQuery(".aec_expand").unbind("cbox_cleanup");
		});
	}
	function after_the_deadline() {
		AtD.rpc_css_lang = aec_popup.atdlang;
		if (aec_popup.atd == 'false') { return; }
		$j('#comment').addProofreader();
		var spellcheck = $j("#AtD_0").clone(true);
		$j("#AtD_0").remove();
		$j("#aec_edit_options").append(spellcheck);
	}
	function commentbox_height() {
		var height = $j("#comment").height();
		$j("#comment").css('height', parent.jQuery("#cboxContent").height() - $j("body").height() + height-35);
	}
	function fill_commentbox() {
		var text = parent.jQuery("#comment").attr("value");
		$j("#comment").attr("value", text);
	}
	$j.ajaxcommentpopup.init();
	
});