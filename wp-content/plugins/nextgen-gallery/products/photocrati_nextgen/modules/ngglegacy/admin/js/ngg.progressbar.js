/*
 * Progress bar Plugin for NextGEN gallery
 * Version:  2.0.3
 * Author : Alex Rabe
 *
 */
(function($) {
	nggProgressBar = {

		settings: {
				id:	'progressbar',
				maxStep: 100,
				wait: false,
				header: '',
                init:false
		},

		init: function( s ) {
			s = this.settings = $.extend( {}, this.settings, {}, s || {} );
			width = Math.round( ( 100 / s.maxStep ) * 100 ) /100;
			// add the initial progressbar
			if ( $( "#" + s.id + "_dialog" ).length == 0) {
				s.header = (s.header.length > 0) ? s.header : '' ;
				$("body").append('<div id="' + s.id + '_dialog"><div id="' + s.id + '" class="progressborder"><div class="' + s.id + '"><span>0%</span></div></div></div>');
                // we open the dialog
                $( "#" + s.id + "_dialog" ).dialog({
            		width: 640,
                    resizable : true,
            		modal: true,
                    title: s.header,
					position: {
						my:		'center',
						at:		'center',
						of:		this.find_parent(window)
					}
            	});
			}
            // get the pointer to the dialog
            this.div = $('#' + s.id + '_dialog');
            s.init = true;
		},

		/**
		* Finds the parent window for the current child window
		*/
	   find_parent: function(child){
		   var retval = child;
		   try {
		   	if (retval && retval.parent)
		   		retval = retval.parent;
		   }
		   catch (Exception){
		   }
		   return retval;
	   },

		addMessage: function( message ) {
			s = this.settings;
			if (!s.init) this.init();
			var div = this.div;
			if ( div.find("#" + s.id + "_message").length == 0)
				div.append('<div class="' + s.id + '_message"><span style="display:block" id="' + s.id + '_message">' + message + '</span></div>');
			else
				$("#" + s.id + "_message").html( message );
		},

		addNote: function( note, detail ) {
			s = this.settings;
			if (!s.init) this.init();
			var div = this.div;
			s.wait = true;
			if ( div.find("#" + s.id + "_note").length == 0)
				div.append('<ul id="' + s.id + '_note">&nbsp;</ul>');

			if (detail)
				$("#" + s.id + "_note").append("<li>" + note + "<div class='show_details'><span>[more]</span><br />" + detail + "</div></li>");
			else
				$("#" + s.id + "_note").append("<li>" + note + "</li>");
            // increase the height to show the note
            div.dialog("option", "height", 220);
		},

		increase: function( step ) {
			s = this.settings;
			var value = step * width + "%";
			var rvalue = Math.round (step * width) + "%" ;
			$("#" + s.id + " div").width( value );
			$("#" + s.id + " span").html( rvalue );

            // Try to restore ATP tabs
            $(this.find_parent(window).document).scrollTop(0);
            var tinymce_frame = $(this.find_parent(window).frameElement).parent();
            var css_top = tinymce_frame.css('top');
            setTimeout(function(){
                tinymce_frame.css('top', 0);
            }, 1);
            setTimeout(function(){
                tinymce_frame.css('top', css_top);
            }, 3);
		},

		finished: function() {
			s = this.settings;
			$("#" + s.id + " div").width( '100%' );
			$("#" + s.id + " span").html( '100%' );
			// in the case we add a note , we should wait for a click
			var div = this.div;
			var progressBar = this;
			if (s.wait) {
                $("#" + s.id).delay(1000).hide("slow");
                progressBar.addNote("Done!");
				div.click(function () {
					progressBar.remove_dialog(false, 0);
	    		});
	    	}
			else {
                window.setTimeout(function() {
					progressBar.remove_dialog(true, 1);
                }, 1000);
	    	}
		},

		remove_dialog: function(delay, value){
			// Destroy the dialog
			if (delay)
				$("#" + s.id + "_dialog" ).delay(4000).dialog("destroy");
			else
				$("#" + s.id + "_dialog").dialog("destroy");

			// Remove the dialog element
			$("#" + s.id + "_dialog").remove();

			// In the case it's the manage page, force a submit
			$('.nggform').prepend("<input type=\"hidden\" name=\"ajax_callback\" value=\""+value+"\">");
			if (delay)
				$('.nggform').delay(4000).submit();
			else
				$('.nggform').submit();
		}
	};
})(jQuery);
