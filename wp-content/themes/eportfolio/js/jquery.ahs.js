jQuery(document).ready(function($) {

	// remove border and margin-bottom from last widget in sidebar
	$('#sidebar .widget:last-child').css({border: 'none', marginBottom: '0px'});
	
	// remove 'search' from button
	$('#searchsubmit').val(' ');

});