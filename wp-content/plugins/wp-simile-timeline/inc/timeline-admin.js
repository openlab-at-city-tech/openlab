/*
 * timeline-admin.js
 * Description: JavaScript functions for option interface
 * Plugin URI: freshlabs.de
 * Author: freshlabs
 * 
	===========================================================================
	SIMILE Timeline for WordPress
	Copyright (C) 2006-2019 freshlabs
	
	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.
	
	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with this program.  If not, see <http://www.gnu.org/licenses/>.
	===========================================================================
*/
jQuery(document).ready( function($) {
	
	// init tabs
	var form_action = document.stl_options.getAttribute('action');
	// append tab-ID to form action attribute on select
	var $tabs = $("#stl-timeline-option-container").tabs({
		select: function(event, ui) {
			document.stl_options.setAttribute('action', form_action.concat('#'+ui.panel.id));
			// uncheck uninstall checkbox when leaving uninstall tab
			$('#stl-timeline-delete-confirm').attr('checked', false);
		}
	});
	// ...and onload
	var s = ""+window.location;
	var p = s.lastIndexOf("#");
	if(p != -1){
		document.stl_options.setAttribute('action', form_action.concat(s.substr(p)));
	}
	
	// init accordion
	$('.stl-collapsible-handle').addClass('active');
	$('.stl-collapsible-handle').click(function(){
		$(this).parent().parent().parent().parent().toggleClass('inactive');
		$(this).toggleClass('active');
	});
	
	// Start-stop date input activation
	toggleUseStartStopInputs($('#stl_timeline_usestartstop').attr('checked'));
	
	$('#stl_timeline_usestartstop').click(function(){
		toggleUseStartStopInputs(this.checked);
	});
	
	$('.stl-suboption-handle').addClass('active');
	$('.stl-suboption-handle').click(function(){
		$(this).next().next().toggleClass('inactive');
		$(this).toggleClass('active');
	});
	
	$('.stl-newentry').addClass('inactive');
	// add hotzone buttons
	$('.stl-addsubentry').click(function(){
		$(this).parent().parent().prev().toggleClass('inactive').addClass('nofoot');
		$(this).parent().hide();
	});
	
	// Type Picker > hide optional inputs
	$('.stl_timeline_decorator_type_picker').change(function(){
		// point decorator: hide fields
		if(this.value == 0){
			$(this).parent().parent().find('.stl_timeline_decorator_optional').hide();
		}
		else{
			$(this).parent().parent().find('.stl_timeline_decorator_optional').show();
		}
	});
	
	function toggleUseStartStopInputs(handle){
		if(!handle){
			$('#stl_timeline_usestartstop_inputs input').attr("disabled", true);
			$('#stl_timeline_usestartstop_inputs select').attr("disabled", true);
		}
		if(handle){
			$('#stl_timeline_usestartstop_inputs input').removeAttr("disabled");
			$('#stl_timeline_usestartstop_inputs select').removeAttr("disabled");
		}
	}
}); 

/* ColorPicker
 * TODO: find correct position on screen. somehow the position:top attribute is ignored
 * */
var cp = new ColorPicker('colorPickerDiv');

function pickColor(color) {
	ColorPicker_targetInput.value = color;
	ColorPicker_targetInput.previousSibling.previousSibling.style.backgroundColor = color;
}
function PopupWindow_populate(contents) {
	contents += '<br /><p style="text-align:center;margin-top:0px;"><input type="button" class="button-secondary" value="Close" onclick="cp.hidePopup(\'prettyplease\')"></input></p>';
	this.contents = contents;
	this.populated = false;
}

function getAnchorPosition(anchorname) {
	// This function will return an Object with x and y properties
	var useWindow=false;
	var coordinates=new Object();
	var x=0,y=0;
	// Browser capability sniffing
	var use_gebi=false, use_css=false, use_layers=false;
	if (document.getElementById) { use_gebi=true; }
	else if (document.all) { use_css=true; }
	else if (document.layers) { use_layers=true; }
	x = window.innerWidth/2;
	y = Math.ceil(window.innerHeight/2) + window.pageYOffset;
	coordinates.x=x;
	coordinates.y=y;
	return coordinates;
}
function PopupWindow_showPopup(anchorname) {
	this.getXYPosition(anchorname);
	this.x += this.offsetX;
	this.y += this.offsetY;
	if (!this.populated && (this.contents != "")) {
		this.populated = true;
		this.refresh();
		}
	if (this.divName != null) {
		
		// Show the DIV object
		if (this.use_gebi) {
			document.getElementById(this.divName).style.left = this.x + "px";
			document.getElementById(this.divName).style.top = this.y;
			document.getElementById(this.divName).style.visibility = "visible";
			}
		else if (this.use_css) {
			document.all[this.divName].style.left = this.x;
			document.all[this.divName].style.top = this.y;
			document.all[this.divName].style.visibility = "visible";
			}
		else if (this.use_layers) {
			document.layers[this.divName].left = this.x;
			document.layers[this.divName].top = this.y;
			document.layers[this.divName].visibility = "visible";
			}
			
		document.getElementById(this.divName).style.top = this.y + "px";
		}
	else {
		if (this.popupWindow == null || this.popupWindow.closed) {
			// If the popup window will go off-screen, move it so it doesn't
			if (this.x<0) { this.x=0; }
			if (this.y<0) { this.y=0; }
			if (screen && screen.availHeight) {
				if ((this.y + this.height) > screen.availHeight) {
					this.y = screen.availHeight - this.height;
					}
				}
			if (screen && screen.availWidth) {
				if ((this.x + this.width) > screen.availWidth) {
					this.x = screen.availWidth - this.width;
					}
				}
			var avoidAboutBlank = window.opera || ( document.layers && !navigator.mimeTypes['*'] ) || navigator.vendor == 'KDE' || ( document.childNodes && !document.all && !navigator.taintEnabled );
			this.popupWindow = window.open(avoidAboutBlank?"":"about:blank","window_"+anchorname,this.windowProperties+",width="+this.width+",height="+this.height+",screenX="+this.x+",left="+this.x+",screenY="+this.y+",top="+this.y+"");
			}
		this.refresh();
		}
}