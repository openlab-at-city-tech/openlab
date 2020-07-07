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
( function ( jQuery ) {
	jQuery( document ).ready(function() {
		var $j = jQuery;
		$j.ajaxcommenteditor = {
			init: function() { if ( jQuery( 'body.editor' ).length <= 0 ) { return; } initialize_events(); load_comment(); after_the_deadline(); expand_option();},
			width: 0,
			height:0,
			commentBoxHeight: 0
		};
		function after_the_deadline() {
			if (wpajaxeditcommentedit.AEC_AftertheDeadline == 'false') { return; }
			AtD.rpc_css_lang = wpajaxeditcommentedit.AEC_AftertheDeadline_lang;
			$j('#comment').addProofreader();
			$j("#submit").click(function() {
				$j(".AtD_edit_button").trigger("click");
			});
			$j("#AtD_0").click(function() {
				$j("div#comment").css("width", "98%");
				//Correct an annoying bug when someone clicks on ATD in the expanded comment box, unexpands, and unclicks ATD
				var buttonpos = $j("#message").position().top + $j("#message").height();
				if (parent.jQuery("#cboxWrapper").height() < buttonpos) {
					$j("#comment").css('height', $j.ajaxcommenteditor.commentBoxHeight);
				}
			});
			var spellcheck = $j("#AtD_0").clone(true);
			$j("#AtD_0").remove();
			$j("#edit_options").append(spellcheck);
		}
		function expand_option(obj) {
			if (wpajaxeditcommentedit.AEC_Expand == 'false') { return; }
			var $window = $j( window );
			$j.ajaxcommenteditor.width = parseInt($window.width());
			$j.ajaxcommenteditor.height = parseInt($window.height());

			$j("#edit_options").append("<span class='expand'></span>");

			$j(".expand").bind("click", function() {
				expand();
			});
			$j(".retract").bind("click", function() {
				retract();
			});
		}
		function expand() {
			$j(".expand").unbind("click");
			var winHeight = parent.jQuery.fn.colorbox.resize('100%', '100%',true);
			var timer =  setInterval(function() {
				clearTimeout(timer);
				$j(".expand").removeClass().addClass("retract");
				$j(".retract").bind("click", function() {retract()});
				$j.ajaxcommenteditor.commentBoxHeight = $j("#comment").height();
				//for ATD
				$j("#comment").css("width", "98%");
				$j("#comment").css('height', parent.jQuery("#cboxContent").height() - $j("#container").height() + $j.ajaxcommenteditor.commentBoxHeight-35);
			}, 1000);
		}
		function retract() {
			$j(".retract").unbind("click");
			parent.jQuery.fn.colorbox.resize($j.ajaxcommenteditor.width, $j.ajaxcommenteditor.height, false);
			$j("#comment").css('height', $j.ajaxcommenteditor.commentBoxHeight);
			var timer2 =  setInterval(function() {
				clearTimeout(timer2);
				$j(".retract").removeClass().addClass("expand");
				$j(".expand").bind("click", function() {expand()});
			}, 500);
		}
		function more_less_options() {
			if ($j("#comment-options").hasClass("closed")) {
				$j("#comment-options h3 span").text(wpajaxeditcommentedit.AEC_MoreOptions);
			} else {
				$j("#comment-options h3 span").text(wpajaxeditcommentedit.AEC_LessOptions);
			}
		}
		//Initializes the edit links
		function initialize_events() {
			//Read in cookie values and adjust the toggle box
			var cookieValue = readCookie('ajax-edit-comments-options');
			if (cookieValue) {
				$j("#comment-options").attr("class", cookieValue);
				more_less_options();
			}

			//The "more options" button
			$j("#comment-options h3").bind("click", function() {
				$j("#comment-options").toggleClass("closed");
				more_less_options();
				createCookie('ajax-edit-comments-options', $j("#comment-options").attr("class"), 365);
				return false;
			});
			//Cancel button
			$j("#cancel,#status a, #close a").bind("click", function() {  parent.jQuery.fn.colorbox.close();
				return false; });
			//Title for new window
			$j("#title a").bind("click", function() { window.open(this.href); return false; } );
			//Save button event
		}

		function load_comment() {

			//Change the edit text and events
			$j( '#status' ).show();
			$j( '#status' ).attr( 'class', 'success' );
			$j( '#message' ).html( wpajaxeditcommentedit.AEC_Loading );

			jQuery.post( ajaxurl, {
					action: $j( '#action' ).attr( 'value' ),
					cid: parseInt( $j( '#commentID' ).attr( 'value' ) ),
					pid: parseInt( $j( '#postID' ).attr( 'value' ) ),
					_ajax_nonce: $j( '#_wpnonce' ).val()
				},
				function ( data ) {
					//Add event for save button
					var error = false;
					$j( '#save' ).bind( 'click', function () {
						save_comment();
						return false;
					} );
					if ( typeof data.error != 'undefined' ) { //error
						error = true;
						$j( '#status' ).attr( 'class', 'error' );
						$j( '#message' ).html( data.error );
						$j( '#close-option' ).show();
						//remove event for save button
						$j( '#save' ).unbind( 'click' );
					} else { //success
						//Load content
						$j( '#comment' ).html( data.comment_content ); //For everyone else
						//$j( '#comment' ).attr( 'value', data.comment_content ); //For Opera
						$j( '#name' ).attr( 'value', data.comment_author );
						$j( '#e-mail' ).attr( 'value', data.comment_author_email );
						$j( '#URL' ).attr( 'value', data.comment_author_url );
					}
					if ( !error ) {
						//Enable the buttons
						$j( '#save, #cancel, #check_spelling' ).removeAttr( 'disabled' );
						//Update status message
						$j( '#status' ).attr( 'class', 'success' );
						$j( '#message' ).html( wpajaxeditcommentedit.AEC_LoadSuccessful );
					}
				}, 'json' );
		} //end load_comment
		function save_comment() {
			//After the deadline
			if ( wpajaxeditcommentedit.AEC_AftertheDeadline == 'true' ) {
				$j( '.AtD_edit_button' ).trigger( 'click' );
			}
			//Update status message
			$j( '#status' ).attr( 'class', 'success' );
			$j( '#message' ).html( wpajaxeditcommentedit.AEC_Saving );
			$j( '#save' ).attr( 'disabled', 'disabled' );
			var error = false;
			//Read in dom values
			var name = encodeURIComponent( $j( '#name' ).attr( 'value' ) );
			var email = encodeURIComponent( $j( '#e-mail' ).attr( 'value' ) );
			var url = encodeURIComponent( $j( '#URL' ).attr( 'value' ) );
			var comment = $j( '#comment' ).val();
			var nonce = $j( '#_wpnonce' ).attr( 'value' );
			var data = {
				action: 'savecomment',
				cid: parseInt( $j( '#commentID' ).attr( 'value' ) ),
				pid: parseInt( $j( '#postID' ).attr( 'value' ) ),
				_ajax_nonce: $j( '#_wpnonce' ).val(),
				comment_content: comment,
				comment_author: name,
				comment_author_email: email,
				comment_author_url: url
			};
			//Comment Status
			if ( $j( '#comment-status-radio' ).length > 0 ) {
				var comment_status = $j( '#comment-status-radio :checked' ).attr( 'value' );
				data = $j.extend( data, { comment_status: comment_status } );
			}
			//Timestamp
			if ( $j( '#timestamp' ).length > 0 ) {
				var month = encodeURIComponent( $j( '#mm' ).attr( 'value' ) );
				var day = encodeURIComponent( $j( '#jj' ).attr( 'value' ) );
				var year = encodeURIComponent( $j( '#aa' ).attr( 'value' ) );
				var hour = encodeURIComponent( $j( '#hh' ).attr( 'value' ) );
				var minute = encodeURIComponent( $j( '#mn' ).attr( 'value' ) );
				var ss = encodeURIComponent( $j( '#ss' ).attr( 'value' ) );
				data = $j.extend( data, { mm: month, jj: day, aa: year, hh: hour, mn: minute, ss: ss } );
			}
			jQuery.post( ajaxurl, data,
				function ( response ) {
					if ( typeof response.error != 'undefined' ) { //error
						error = true;
						$j( '#save' ).removeAttr( 'disabled' );
						$j( '#status' ).attr( 'class', 'error' );
						$j( '#message' ).html( response.error );
						$j( '#close-option' ).show();
					} else { //success
						var comment = response.content;
						var name = response.comment_author;
						var url = response.comment_author_url;
						var date = response.comment_date;
						var time = response.comment_time;
						var undo = response.undo;
						var comment_approved = response.comment_approved;
						var old_comment_approved = response.old_comment_approved;
						var spam_count = response.spam_count;
						var moderation_count = response.moderation_count;
						var comment_links = response.comment_links;
					}
					if ( !error ) {
						try {
							self.parent.jQuery.ajaxeditcomments.update_comment( 'edit-comment' + data.cid, comment );
							self.parent.jQuery.ajaxeditcomments.update_author( 'edit-author' + data.cid, name, url );
							self.parent.jQuery.ajaxeditcomments.update_date_or_time( 'aecdate' + data.cid, date );
							self.parent.jQuery.ajaxeditcomments.undo_message( data.cid, undo, true );
							self.parent.jQuery( '.spam-count' ).html( spam_count );
							self.parent.jQuery( '#edit-comment-admin-links' + data.cid ).html( comment_links );
							self.parent.jQuery( '.pending-count' ).html( moderation_count );
						} catch ( err ) {
						}
						$j( '#status' ).attr( 'class', 'success' );
						$j( '#message' ).html( wpajaxeditcommentedit.AEC_Saved );
						parent.jQuery.fn.colorbox.close();
					}
				}, 'json' );

		}
		//Cookie code conveniently stolen from http://www.quirksmode.org/js/cookies.html
		function createCookie(name,value,days) {
			if (days) {
				var date = new Date();
				date.setTime(date.getTime()+(days*24*60*60*1000));
				var expires = "; expires="+date.toGMTString();
			}
			else var expires = "";
			document.cookie = name+"="+value+expires+"; path=/";
		}
		function readCookie(name) {
			var nameEQ = name + "=";
			var ca = document.cookie.split(';');
			for(var i=0;i < ca.length;i++) {
				var c = ca[i];
				while (c.charAt(0)==' ') c = c.substring(1,c.length);
				if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
			return null;
		}
		$j("body").attr("style", "display: block;");
		$j.ajaxcommenteditor.init();

	});
} )( window.jQuery );