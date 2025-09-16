/**
 * Handles any Soliloquy WP_List_Table events:
 * - Quick / Bulk Edit
 */
jQuery(document).ready(function ($) {

	//Init the Clipboard
	new Clipboard('.soliloquy-clipboard');

	//Prevent Default
	$('.soliloquy-clipboard').on('click', function (e) {

		e.preventDefault();

	});
	/**
	 * Quick / Bulk Edit Support
	 */
	if (typeof inlineEditPost !== 'undefined') {
		// we create a copy of the WP inline edit post function
		var wp_inline_edit = inlineEditPost.edit;

		// and then we overwrite the function with our own code
		inlineEditPost.edit = function (id) {
			// "call" the original WP edit function
			// we don't want to leave WordPress hanging
			wp_inline_edit.apply(this, arguments);

			// get the post ID
			var post_id = 0;
			if (typeof (id) == 'object') {
				post_id = parseInt(this.getId(id));
			}

			if (post_id > 0) {
				// Get the Edit and Post Row Elements
				var edit_row = $('#edit-' + post_id),
					post_row = $('#post-' + post_id);
				// Get Soliloquy Settings
				// These are stored in hidden input fields created by includes/admin/posttype.php
				// We populate via JS because there's no $post object for us to access in includes/admin/common.php's quick edit functions
				slider_theme = $('input[name="_soliloquy_' + post_id + '[slider_theme]"]', $(post_row)).val(),
					slider_transition = $('input[name="_soliloquy_' + post_id + '[transition]"]', $(post_row)).val();

				// Populate Quick Edit Fields with data from the above hidden fields
				$('select[name="_soliloquy[slider_theme]"]', $(edit_row)).val(slider_theme);
				$('select[name="_soliloquy[transition]"]', $(edit_row)).val(slider_transition);

			}

		};

		// Remove all hidden inputs when a search is performed
		// This stops them from being included in the GET URL, otherwise we'd have a really long search URL
		// which breaks some nginx configurations
		$('form#posts-filter').on('submit', function (e) {
			$('input.soliloquy-quick-edit').remove();
		})
	}

});