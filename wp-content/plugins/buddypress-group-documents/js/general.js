jQuery(document).ready( function($) {

	//Hide the sort form submit, we're gonna submit on change
	$('#bp-group-documents-sort-form input[type=submit]').hide();
	$('#bp-group-documents-sort-form select[name=order]').on( 'change', function(){
		$('form#bp-group-documents-sort-form').submit();
	});

	//Hide the category form submit, we're gonna submit on change
	$('#bp-group-documents-category-form input[type=submit]').hide();
	$('#bp-group-documents-category-form select[name=category]').on( 'change', function(){
		$('form#bp-group-documents-category-form').submit();
	});

	//Hide the upload form by default, expand as needed
	$('#bp-group-documents-upload-button').show();
	$('#bp-group-documents-upload-button').on( 'click', function(){
		$('#bp-group-documents').addClass('is-edit-mode');
		return false;
	});

	if ( $('#bp-group-documents').hasClass( 'is-edit-mode' ) ) {
		$('.submenu .group-count').hide();
	}

	//prefill the new category field
	$('input.bp-group-documents-new-category').val('New Category...').css('color','#999').on( 'focus', function(){
		$(this).val('').css('color','inherit');
	});

	//check for presence of a file before submitting form
	$('form#bp-group-documents-form').on( 'submit', function(){

		//check for pre-filled values, and remove before sumitting
		if( $('input.bp-group-documents-new-category').val() == 'New Category...' ) {
			$('input.bp-group-documents-new-category').val('');
		}
		if( $('input[name=bp_group_documents_operation]').val() == 'add' ) {
			if($('input.bp-group-documents-file').val()) {
				return true;
			}
			alert('You must select a file to upload!');
			return false;
		}
	});

	//validate group admin form before submitting
	$('form#group-settings-form').on( 'submit', function() {

		//check for pre-filled values, and remove before sumitting
		if( $('input.bp-group-documents-new-category').val() == 'New Category...' ) {
			$('input.bp-group-documents-new-category').val('');
		}
	});

	//Make the user confirm when deleting a document
	$('a#bp-group-documents-delete').on( 'click', function(){
		return confirm('Are you sure you wish to permanently delete this document?');
	});

	//Track when a user clicks a document via Ajax
	$('a.group-documents-title').add($('a.group-documents-icon')).on( 'click', function(){
		dash_position = $(this).attr('id').lastIndexOf('-');
		document_num = $(this).attr('id').substring(dash_position+1);

		$.post( ajaxurl ,{
			action:'bp_group_documents_increment_downloads',
			document_id:document_num
		});

	});

	//Make user confirm when deleting a category
	$('a.group-documents-category-delete').on( 'click', function(){
		return confirm('Are you sure you wish to permanently delete this category?');
	});

	//add new single categories in the group admin screen via ajax
	$('#group-documents-group-admin-categories input[value=Add]').on( 'click', function(){
		$.post(ajaxurl, {
			action:'group_documents_add_category',
			category:$('input[name=bp_group_documents_new_category]').val()
		}, function(response){
			$('#group-documents-group-admin-categories input[value=Add]').parent().before(response);
		});
		return false;
	});

	//delete single categories in the group admin screen via ajax
	$('#group-documents-group-admin-categories a.group-documents-category-delete').on( 'click', function(){
		cat_id_string = $(this).parent('li').attr('id');
		pos = cat_id_string.indexOf('-');
		cat_id = cat_id_string.substring(pos+1);
		$.post(ajaxurl, {
			action:'group_documents_delete_category',
			category_id:cat_id
		}, function(response){
			if( '1' == response ) {
				$('li#' + cat_id_string).slideUp();
			}
		});
		return false;
	});

});
