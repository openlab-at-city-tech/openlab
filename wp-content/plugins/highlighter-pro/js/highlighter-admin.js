(function( $ ) {
 
    // find and loop through all locked redux fields
	var $links = $('tr.sl-locked > td > fieldset');
	$links.each(function() { 
		jQuery(this).append('<div class="sl-locked-shield"><span>Pro Feature</span></div>');

	});
     
})( jQuery );