AJAX MODULE
======================

 == Introduction ==
-------------------

This module provides a means for executing AJAX actions through the C_Ajax_Controller class.
This controller is registed as a route, trigged by "/photocrati_ajax" It's designed in mind 
with the intention that other modules will adapt this controller to provide custom AJAX
actions.

This module also adds some client-side variables to assist with executing your AJAX actions:
=> 	photocrati_ajax.url, the url used to post your AJAX requests to
=>  photacrati_ajax.wp_site_url, the url of the WordPress site

To call an AJAX method using jQuery, you'd do the following:

	jQuery.post(photocrati_ajax.url, {action: "get_gallery", id: 1}, function(response){
		if (typeof response != 'object) response = JSON.parse(response);
	});
	
The above AJAX request will execute C_Ajax_Controller->get_gallery_action(), which is 
expected to return valid JSON (even if there is an error)


== Caveats ==
-------------

This module does not currently have any built-in security mechanisms. Any actions you
mixin using an adapter need to perform their own authorization checks.