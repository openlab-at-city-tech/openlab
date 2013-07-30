/*WP Ajax Edit Comments Admin Panel Script
--Created by Ronald Huereca
--Created on: 02/16/2010
--Last modified on: 02/16/2010
--Relies on jQuery, 'jquery-ui-sortable
	
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
$j.ajaxadminpanel = {
	init: function() { initialize_events();},
	action: $j("#aecadminpanel").attr("action")
};
	
	//Initializes the edit links
	function initialize_events() {
		//For the icons preview
		$j("select[name='icon_set']").bind("change", function() {
			$j("#iconpreview img").attr("src", $j("input[name='iconpreviewurl']").attr("value") + $j(this).attr("value") + "/sprite.png");													  
		});
		//For the classic options
		$j("#sortclassicul").sortable({
			stop: function(event, ui) {
				var lis = $j("li.sortableclassic");
				$j.each(lis, function() {
					update_order_classic($j(this));								
				});
			}
		}).disableSelection();
		$j(".classic").toggle(function() {
			var span = $j(this);
			var li = span.parent();
			span.addClass("disabled");
			span.removeClass("enabled");
			update_order_classic(li);
		}, function() {
			var span = $j(this);
			var li = span.parent();
			span.addClass("enabled");
			span.removeClass("disabled");
			update_order_classic(li);
		});
		
		//For the dropdown menu options
		$j("#sort0ul, #sort1ul,#sort2ul").sortable({
			connectWith: '.connectedSortable',
			stop: function(event, ui) {
				var lis = $j("li.sortable");
				$j.each(lis, function() {
					update_order_dropdown($j(this));								
				});
			}
		}).disableSelection();
		$j(".dropdown").toggle(function() {
			var span = $j(this);
			var li = span.parent();
			span.addClass("disabled");
			span.removeClass("enabled");
			update_order_dropdown(li);
		}, function() {
			var span = $j(this);
			var li = span.parent();
			span.addClass("enabled");
			span.removeClass("disabled");
			update_order_dropdown(li);
		});
  	} //end initialize_events
	//Update the order/input boxes for the dropdown
	function update_order_dropdown(li) {
		var parent = li.parent();
		var list_order = $j("#" + parent.attr("id") + " >li").index(li);
		var ul_id = parent.attr("id");
		var column = ul_id.match(/(\d)+/);
		column = column[0];
		var enabled = "1";
		if (!($j("li#" + li.attr("id") + " span").hasClass('enabled'))) {
			enabled = "0";			
		}
		$j("li#" + li.attr("id") +" input").attr("value", column + "," + list_order + "," + enabled);	
	}
	//Update the order/input boxes for the classic
	function update_order_classic(li) {
		var parent = li.parent();
		var list_order = $j("#" + parent.attr("id") + " >li").index(li);
		var enabled = "1";
		if (!($j("li#" + li.attr("id") + " span").hasClass('enabled'))) {
			enabled = "0";			
		}
		$j("li#" + li.attr("id") +" input").attr("value", list_order + "," + enabled);	
	}
	$j.ajaxadminpanel.init(); 
});