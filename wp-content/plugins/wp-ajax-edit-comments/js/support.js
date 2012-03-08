/*WP Ajax Edit Comments Admin Panel Script
--Created by Ronald Huereca
--Created on: 05/23/2010
--Last modified on: 05/23/2010
--Relies on jQuery, 'jquery-ui-sortable
	
	Copyright 2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)
*/
jQuery(document).ready(function() {
var $j = jQuery;
$j.aecsupport = {
	init: function() { initialize_events();}
};
	
	//Initializes the edit links
	function initialize_events() {
		$j("#aecshow").toggle(function() {$j("#aecshow").html(aecsupport.hide); $j("#aecsupportinfo").slideDown(500);}, function() {$j("#aecshow").html(aecsupport.show);$j("#aecsupportinfo").slideUp(500);});
		
  	} //end initialize_events
	$j.aecsupport.init(); 
});