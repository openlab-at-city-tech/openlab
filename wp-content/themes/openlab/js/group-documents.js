(function($){
	
    $(document).ready(function() {
		
        $(document).on( 'change', 'input.bp-group-documents-file-type', function(e) {
            let wrapper = $('.bp-group-documents-fields');

            if( $(this).val() === 'upload' ) {
                wrapper.removeClass('show-link').addClass('show-upload');
            } else {
                wrapper.removeClass('show-upload').addClass('show-link');
            }
        });

	});

}(jQuery));
