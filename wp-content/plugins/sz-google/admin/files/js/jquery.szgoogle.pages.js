// Definition of code to handle the tab on the 
// pages of configuration in the admin panel

jQuery(document).ready(function()
{
	jQuery('#sz-google-tab').find('a').click(function() 
	{
		// I remove all tabs present the class identification 
		// active element and add this class only to the active

		jQuery('#sz-google-tab').find('a').removeClass('nav-tab-active');
		jQuery(this).addClass('nav-tab-active');

		// I take on all divisions of the content tab of the class 
		// impostazionne "active" and add only the selected

		var id = jQuery(this).attr('id').replace('sz-google-tab-','');

		jQuery('.sz-google-tab-div').removeClass('sz-google-active');
		jQuery('#sz-google-tab-div-'+ id).addClass('sz-google-active');

	});

	// Initial steps to execute immediately after the page 
	// loads with activation of the links that are "active"

	var active_tab = window.location.hash.replace('#','');

	// If not anything special and the tab is not found in the HTML 
	// code used as default tab that match the first div in the code

	if (active_tab == '' || !jQuery('#sz-google-tab-'+ active_tab).length) 
	{
		active_tab = jQuery('.sz-google-tab-div').attr('id');

		if (typeof active_tab != 'undefined') {
			active_tab = active_tab.replace('sz-google-tab-div-','');
 		}
	}

	// Activation by adding classes "active" link 
	// and division that are currently displayed

	jQuery('#sz-google-tab-'     + active_tab).addClass('nav-tab-active');
	jQuery('#sz-google-tab-div-' + active_tab).addClass('sz-google-active');
});
