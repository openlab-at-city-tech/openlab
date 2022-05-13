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

        // Remove the original 'submit' event for the group documents form
        $('form#bp-group-documents-form').off('submit');

        // Attach new submit event on the group documents form
        $(document).on( 'submit', 'form#bp-group-documents-form', function() {
            
            //check for pre-filled values, and remove before sumitting
            if( $('input.bp-group-documents-new-category').val() == 'New Category...' ) {
                $('input.bp-group-documents-new-category').val('');
            }

            if( $('input[name=bp_group_documents_operation]').val() == 'add' && $('input[name=bp_group_documents_file_type]:checked').val() == 'upload' ) {
                if($('input.bp-group-documents-file').val()) {
                    return true;
                }

                alert('You must select a file to upload!');
                return false;
            }
        });

	});

}(jQuery));
