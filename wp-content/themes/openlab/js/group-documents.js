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

        // Remove the original click handler on the "Add new file" button
        $('#bp-group-documents-upload-button').off();

        // Add custom click handler on the "Add new file" button
        $(document).on( 'click', '#bp-group-documents-upload-button', function(e) {
            e.preventDefault();
            $('.submenu-item').removeClass('current-menu-item');
            $('.submenu-item.item-add-new-file').addClass('current-menu-item');
            $('#bp-group-documents').addClass('is-edit-mode');
        });

        // Toggle current menu item when clicking 
        $(document).on( 'click', '.submenu-item.item-add-new-file a', function(e) {
            e.preventDefault();
            if( ! $(this).hasClass('current-menu-item') ) {
                $('.submenu-item').removeClass('current-menu-item');
                $(this).parent('li').addClass('current-menu-item');
                $('#bp-group-documents').addClass('is-edit-mode');
            }
        })

        // Show files on cancel on add new file form
        $(document).on( 'click', '#btn-group-documents-cancel', function(e) {
            e.preventDefault();
            $('#bp-group-documents').removeClass('is-edit-mode');
            $('.submenu-item').removeClass('current-menu-item');
            $('.submenu-item.item-all-files').addClass('current-menu-item');
        });

	});

}(jQuery));
