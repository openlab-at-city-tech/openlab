/*WP Ajax Edit Move Comments Script
--Created by Ronald Huereca
--Created on: 09/14/2009
--Last modified on: 03/22/1011
--Relies on jQuery, wp-ajax-edit-comments, wp-ajax-response, thickbox
	
	Copyright 2007-2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)

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
$j.ajaxmovecomment = {
	init: function() { if ( jQuery( 'body.move' ).length <= 0 ) { return; } initialize_events(); load_posts(); }
};
	//Initializes the edit links
	function initialize_events() {
    //Cancel button
    $j("#cancel,#status a, #close a").bind("click", function() {  parent.jQuery.fn.colorbox.close();
    return false; });
    //Title for new window
    $j("#title a").bind("click", function() { window.open(this.href); return false; } );
    
		//Title Search button
		$j("#title_search").bind("click", function() {
			$j("#post_title_move").attr("disabled", "disabled");																				 
			load_title_ajax();
		});
		
		//ID Search button
		$j("#id_search").bind("click", function() {
			$j("#post_id_move").attr("disabled", "disabled");
			var data = pre_process();	
			data.post_id = $j("#post_id").val();
		
			//Show and hide certain elements
			$j("#post_id_buttons").addClass("hidden");
			$j("#post_id_loading").removeClass("hidden");
			$j("#id_search").attr("disabled", "disabled");
			$j("#post_id_radio").html("");
			jQuery.post(ajaxurl, data, 
			function ( response ) {
				var count = 0; 
				var radio = '';
				$j("#id_search").removeAttr("disabled");
				$j("#post_id_loading").addClass("hidden");
				if ( typeof response.posts != "undefined" ) {
					count += 1;
					radio += "<input type='radio' name='posts_id' id='posts_id_" + response.posts.post_id + "' value='" + response.posts.post_id + "' />&nbsp;&nbsp;<label for='posts_id_" + response.posts.post_id + "'>" + response.posts.post_title + "</label><br />";	
				}
				
				if (count >= 1) {
					$j("#post_id_buttons").removeClass("hidden");
				}
				//write to screen
				$j("#post_id_radio").html(radio);
				
				//Setup Events for ID
				$j("input[name='posts_id']").click(function() { 
					$j("#post_id_move").removeAttr("disabled");
					var new_id = $j(this).val();
					$j("#post_id_move").bind("click", function() {
						$j("#post_id_move").attr("disabled", "disabled");
						var data = pre_process();
						data.newid = new_id;			
						data = check_approve( data );
						jQuery.post(ajaxurl, data, 
						function ( response ) {
							//for the admin panel
							update_admin_panel( response );
							parent.jQuery.fn.colorbox.close();
						}, 'json' );
					}); //end #post_id_move click																 
				});
			}, 'json' );
		}); //end id_search click
  }
	//Checks to see if the approve button is available and only adds it if the value is one
	function check_approve(data) {
		if ($j("#approved:checked").length > 0) {
				data = $j.extend(data, { approve: "1"});
		}
		return data;
	}
	function load_title_ajax() {
		var data = pre_process();	
		data.post_title =  $j("#move_title").val();
		
		//Show and hide certain elements
		$j("#post_title_buttons").addClass("hidden");
		$j("#post_title_loading").removeClass("hidden");
		$j("#title_search").attr("disabled", "disabled");
		$j("#post_title_radio").html("");
		jQuery.post(ajaxurl, data, 
		function ( response ) {
			count = 0; radio = '';
			$j("#title_search").removeAttr("disabled");
			$j("#post_title_loading").addClass("hidden");
			$j.each( response.posts, function() {
					if (this.data != '') {
					count += 1;
						radio += "<input type='radio' name='posts_title' id='post_title_" + this.post_id + "' value='" + this.post_id + "' />&nbsp;&nbsp;<label for='post_title_" + this.post_id + "'>" + this.post_title + "</label><br />";
					}
			});
			if (count >= 1) {
				$j("#post_title_buttons").removeClass("hidden");
			}
			//write to screen
			$j("#post_title_radio").html(radio);
			
			//Setup events for title
			$j("input[name='posts_title']").click(function() { 
				$j("#post_title_move").removeAttr("disabled");
				var new_id = $j(this).val();
				$j("#post_title_move").bind("click", function() { 
					$j("#post_title_move").attr("disabled", "disabled");
					var data = pre_process();
					data.newid = new_id;			
					data = check_approve( data );
					jQuery.post(ajaxurl, data, 
					function ( response ) {
						//for the admin panel
						update_admin_panel( response );
						parent.jQuery.fn.colorbox.close();
					}, 'json' );
				});																			 
			});

		}, 'json' );
	}
	//Loads a group of posts in the Posts tab.
	//post_offset and direction (true, or false)
	function load_posts_ajax(post_offset, dir) {
		if (post_offset < 0) { 
			post_offset = 0;
		}
		var data = pre_process();
		data.post_offset = post_offset;
		
		jQuery.post(ajaxurl, data, 
		function( response ) {
			if ( typeof response.error != "undefined" ) { alert( response.error ); return; }
			var radio = "";
			var count = 0;
			//Display found posts
			$j.each( response.posts, function() {
				count += 1;
				if ( count < 6 ) {
					radio += "<input type='radio' name='posts' id='post_" + this.post_id + "' value='" + this.post_id + "' />&nbsp;&nbsp;<label for='post_" + this.post_id + "'>" + this.post_title + "</label><br />";		
				}
			});
			//Show and hide certain elements
			$j("#post_loading").addClass("hidden");
			$j("#post_buttons").removeClass("hidden");
			//write to screen
			$j("#post_radio").html(radio);
			
			//Setup events for posts
			$j("input[name='posts']").click(function() { 
				$j("#post_move").removeAttr("disabled");
				var new_id = $j(this).val();
				$j("#post_move").bind("click", function() { 
					$j("#post_move").attr("disabled", "disabled");
					var data = pre_process();
					data.newid = new_id;			
					data = check_approve( data );
					jQuery.post(ajaxurl, data, 
					function ( response ) {
						//for the admin panel
						update_admin_panel( response );
						parent.jQuery.fn.colorbox.close();
					}, 'json' );
				});																			 
			}); //end event for posts
				
			//Write the offset
			//$j("#post_offset").attr("value", count);
			if (count == 6 && dir == "true") {
				//Show next button
				$j("#post_next").removeClass("hidden");
				if (post_offset >= 5) {
					$j("#post_previous").removeClass("hidden");
				}
			} else if (count > 0 && count < 6 && post_offset != 0 && dir == "true") {
				$j("#post_previous").removeClass("hidden");
			}
			if (post_offset >= 5 && dir == "false") {
				$j("#post_offset").attr("value", post_offset);
				$j("#post_previous").removeClass("hidden");
				$j("#post_next").removeClass("hidden");
			} else if (post_offset == 0 && count == 6 ) {
					$j("#post_next").removeClass("hidden");
			}
		}, 'json' );
 
    	
	} //end load_posts_ajax
	function load_posts() {
		load_posts_ajax(parseInt($j("#post_offset").attr("value")), "true");
		$j("#post_next").bind("click", function() {
			$j("#post_loading").removeClass("hidden");
			$j("#post_radio").html("");
			$j("#post_previous").addClass("hidden");
			$j("#post_next").addClass("hidden");
			$j("#post_buttons").addClass("hidden");
			$j("#post_move").attr("disabled", "disabled");
			p = pre_process();
			var post_offset = parseInt($j("#post_offset").attr("value")) + 5;
			$j("#post_offset").attr("value", post_offset);
			load_posts_ajax(post_offset, "true");
			return false;
		});
		$j("#post_previous").bind("click", function() { 
			$j("#post_loading").removeClass("hidden");
			$j("#post_radio").html("");
			$j("#post_previous").addClass("hidden");
			$j("#post_next").addClass("hidden");
			$j("#post_buttons").addClass("hidden");
			$j("#post_move").attr("disabled", "disabled");
			p = pre_process();
			var post_offset = parseInt($j("#post_offset").attr("value")) - 5;
			$j("#post_offset").attr("value", post_offset);
			load_posts_ajax(post_offset, "false");
			return false;
		});
	}
	//Updates the admin panel when someone moves a comment
	function update_admin_panel( response ) {
		if ( typeof response.nochange != "undefined" ) { return; }
		
		var comment_id = response.comment_id;
		//Update New ID
		var newID = response.new_post.new_id;
		var oldID = response.new_post.old_id;
		var title = response.new_post.title;
		var comments = response.new_post.comments;
		var permalink =response.new_post.permalink;
		
		//Update the edit post link
		if (self.parent.jQuery("#comment-" + comment_id + " .post-com-count-wrapper a:first").length != 0) {
			var new_edit_url = self.parent.jQuery("#comment-" + comment_id + " .post-com-count-wrapper a:first").attr("href");
			//todo - need to update regex to work in admin panel
			new_edit_url = new_edit_url.replace(/[0-9]+$/,newID);
			self.parent.jQuery("#comment-" + comment_id + " .post-com-count-wrapper a:first").attr("href", new_edit_url);
			self.parent.jQuery("#comment-" + comment_id + " .post-com-count-wrapper a:first").html(title);
			
			//Update the edit comment link
			var new_comment_url = self.parent.jQuery("#comment-" + comment_id + " .post-com-count").attr("href");
			new_comment_url = new_comment_url.replace(/[0-9]+$/,newID);
			self.parent.jQuery("#comment-" + comment_id + " .post-com-count-wrapper a:last").attr("href",new_comment_url);
			
			//Update the permalink
			self.parent.jQuery("#comment-" + comment_id + " .response-links a:last").attr("href",permalink);
			
			
			//Update the comments count
			$j.each(self.parent.jQuery(".response:contains(" + title + ")"), function() {
				$j(this).find(".comment-count").html(comments);																																																																											
				}
			);
		}
		//Update Old ID
		var comments = response.old_post.comments;
		var title = response.old_post.title;
		//Update the comments count
		$j.each(self.parent.jQuery(".response:contains(" + title + ")"), function() {
			$j(this).find(".comment-count").html(comments);																																																																											
			}
		);
		
		if ( typeof response.approved != "undefined" ) {
			self.parent.jQuery(".spam-count").html(response.approved.spam_count);
			self.parent.jQuery(".pending-count").html(response.approved.moderation_count);
			self.parent.jQuery(".aec-approve-" + comment_id + ",#approve-comment-" + comment_id).hide();
			self.parent.jQuery(".aec-spam-" + comment_id + ",#spam-comment-" + response.approved.comment_id).show();
			self.parent.jQuery(".aec-moderate-" + comment_id + ",#moderate-comment-" + comment_id).show();
		} 
		
		if ( typeof response.status_message != "undefined" ) {
			self.parent.jQuery("#comment-undo-" + comment_id).html( response.status_message);
		}
	} //end update_admin_panel
	function pre_process(element) {
		var data = {};
		data._ajax_nonce = $j("#_wpnonce").val();
		data.cid = parseInt($j("#commentID").val());
		data.pid = parseInt($j("#postID").val());
		data.action = $j("#action").val();
		return data;
	};
	$j.ajaxmovecomment.init();
	$j('body').show();
	$j("body").attr("style", "display: block;");
});