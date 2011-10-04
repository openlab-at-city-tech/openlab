jQuery(document).ready(function($) {

	/** controls character input/counter **/
	$('#genesis_title').keyup(function() {
		var charLength = $(this).val().length;
		// Displays count
		$('#genesis_title_chars').html(charLength);
	});
	$('#genesis_description').keyup(function() {
		var charLength = $(this).val().length;
		// Displays count
		$('#genesis_description_chars').html(charLength);
	});

	// Array: selector of toggle element, selector of element to show/hide, checkable value for select || null
	var genesis_toggles = [
		// Checkbox toggles
		['#genesis-settings\\[update\\]', '#genesis_update_notification_setting', null],
		['#genesis-settings\\[nav\\]', '#genesis_nav_settings', null],
		['#genesis-settings\\[subnav\\]', '#genesis_subnav_settings', null],
		['#genesis-settings\\[content_archive_thumbnail\\]', '#genesis_image_size', null],
		['#genesis-settings\\[nav_extras_enable\\]', '#genesis_nav_extras_settings', null],
		// Select toggles
		['#genesis-settings\\[nav_extras\\]', '#genesis_nav_extras_twitter', 'twitter'],
		['#genesis-settings\\[content_archive\\]', '#genesis_content_limit_setting', 'full']
	];

	$.each( genesis_toggles, function( k, v ) {
		$( v[0] ).live( 'change', function() {
			genesis_toggle_settings( v[0], v[1], v[2] );
		});
		genesis_toggle_settings( v[0], v[1], v[2] ); // Check when page loads too.
	});

	function genesis_toggle_settings( selector, show_selector, check_value ) {
		if (
			( check_value === null && $( selector ).is( ':checked' ) ) ||
			( check_value !== null && $( selector ).val() === check_value )
		) {
			$( show_selector ).slideDown( 'fast' );
		} else {
			$( show_selector ).slideUp( 'fast' );
		}
	}

	function genesis_category_checklist_toggle() {
		$category_checklist_toggle = $('<li class="check-column"><label><input type="checkbox" value="" /> <em>' + genesis.category_checklist_toggle + '</em></label></li>');

		$category_checklist_toggle.prependTo($('#categorydiv .categorychecklist'));

		$('.check-column :checkbox').click(function (e) {
			$(this).closest('#categorydiv .categorychecklist').find(':checkbox').attr('checked', $(this).attr('checked'));
		});
	}
	genesis_category_checklist_toggle();

});

function genesis_confirm( text ) {
	var answer = confirm( text );

	if( answer ) { return true; }
	else { return false; }
}