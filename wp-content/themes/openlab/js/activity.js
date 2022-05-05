/**
 * This is the JavaScript related to the activity stream.
 * 
 */

jQuery( document ).ready(
	function($) {
        
        $(document).on( 'change', '#activity-loop-filter-form select', function(e) {
            $(this).parent('form').submit();
        });
        
	},
	(jQuery)
);
