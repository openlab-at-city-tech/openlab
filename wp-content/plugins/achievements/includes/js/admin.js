jQuery(document).ready(function($) {

	$('.initially_hidden').addClass('hidden_initially');

	if ( $("#action_id option:selected").hasClass('group') )
		$('fieldset.mandatory-trigger div.groups').show();

	$('#name').change(function(event) {
		if ( !$('#slug').hasClass( 'tainted' ) )
			$('#slug').val( $(this).val().toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-') );
	});

	$('#slug').change(function(event) {
		if ( !$(this).hasClass('tainted') )
			$(this).addClass('tainted');
	});

	$('#is_hidden').change(function(event) {
		if ( !this.checked )
			return;

		document.getElementById('is_active').checked = true;
	});

	$('#is_active').change(function(event) {
		if ( this.checked )
			return;

		var is_hidden = document.getElementById('is_hidden');
		if ( is_hidden.checked )
			document.getElementById('is_hidden').checked = false;
	});

	$('input[name=achievement_type]').change(function(event) {
		var newVal = $(this).val();

		if ( 'badge' == newVal ) {
			$("#group_id").val("-1");
			$('fieldset.mandatory-trigger div.event').fadeOut('fast');
			$('fieldset.mandatory-trigger div.groups').fadeOut('fast');

		} else if ( 'event' == newVal ) {
			$('fieldset.mandatory-trigger div.event').fadeIn('fast');

			if ( $("#action_id option:selected").hasClass('group') )
				$('fieldset.mandatory-trigger div.groups').fadeIn('fast');
		}
	});

	$('#action_id').change(function(event) {
		if ( $("#action_id option:selected").hasClass('group') ) {
			$('fieldset.mandatory-trigger div.groups').fadeIn('fast');

		} else {
			$("#group_id").val("-1");
			$('fieldset.mandatory-trigger div.groups').fadeOut('fast');
		}
	});

	$('img.avatar-preview').click(function(event) {
		$('#item-header-avatar .avatar').attr('src', $(this).attr('src'));

		$(this).parent().children().each(function() {
			$(this).removeClass('avatar-preview-selected');
		});

		$(this).addClass('avatar-preview-selected');

		var picture_id = $(this).attr('id');
		$('#picture_id').attr('value', picture_id.substr(1, picture_id.length-1));
	});

	$("#grant-invite-list input").click(function(event) {
		var member_id = $(this).val();
		if ( $(this).attr('checked') == false ) {
			$('#grant-user-list li#uid-' + member_id).remove();
			return;
		}

		$('.ajax-loader').toggle();
		$('div.item-list-tabs li.selected').addClass('loading');

		$.post( ajaxurl, {
			action: 'dpa_grant_user_details',
			'cookie': encodeURIComponent(document.cookie),
			'member_id': member_id
		},
		function(response)
		{
			$('.ajax-loader').toggle();
			$('#grant-user-list').append(response);
			jQuery('div.item-list-tabs li.selected').removeClass('loading');
		});
	});

});