/*WP Ajax Check Upgrades Script
--Created by Ronald Huereca
--Created on: 05/23/2010
--Last modified on: 05/23/2010
--Relies on jQuery, wp-ajax-response, jquery
	Copyright 2010  Ronald Huereca  (email : ron alfy [a t ] g m ail DOT com)
*/
jQuery(document).ready(function() {
var $j = jQuery;
$j.aeccheckupgrades = {
	init: function() {  _init(); }
};
	
  //Returns a data object for ajax calls
	function _init() {
		$j("#aeccheckupdates").bind("click", function() {
			$j("#aeccheckupdates").html(aec_check_upgrades.checking);
			var url = unserialize( jQuery( this ).attr('href'));
			jQuery.post( ajaxurl, { _ajax_nonce:url._wpnonce,action:url.action },
				function(response){
					$j("#aeccheckupdates").html(aec_check_upgrades.checkupgrades);
					if ( typeof response.error != "undefined" ) { //error
						$j("#aeccheckupdatesmessage").html( response.error );
					} else {
						$j("#aeccheckupdatesmessage").html( response.success );
					}
				}
			, 'json' );
			return false;
		}); //checkupdates click
	}; //end init
	function unserialize( s ) {
		var r = {}, q, pp, i, p;
		if ( !s ) { return r; }
		q = s.split('?'); if ( q[1] ) { s = q[1]; }
		pp = s.split('&');
		for ( i in pp ) {
			if ( jQuery.isFunction(pp.hasOwnProperty) && !pp.hasOwnProperty(i) ) { continue; }
			p = pp[i].split('=');
			r[p[0]] = p[1];
		}
		return r;
	};
	$j.aeccheckupgrades.init();
});