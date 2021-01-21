jQuery(document).ready( function ($) {
	$('input#cac_ncs_groups').devbridgeAutocomplete({
		serviceUrl: ajaxurl,
		params: {
			action: "cac_ncs_groups_query",
		},
		deferRequestBy: 200,
		onSelect: function ( value ) {
			addGroup( value );

			// Add to the ID list.
			updateGroupIds( value.data );

			console.log( $(this) );

			// Reset input value.
			$('input#cac_ncs_groups').val('');
		},
	});

	function addGroup(group) {
		var groups = $('.cac-ncs-groups-results');
		var removeBtn = '<span class="cac-nsc-remove-group"><a href="#">x</a></span></li>';
		var element = '<li class="olsc-group" data-group-id="' + group.data + '">' + group.value + ' ' + removeBtn + '</li>';

		groups.append(element);
	}

	function updateGroupIds(groupId) {
		var groupIds = $('#cac_nsc_group_ids').val();
		var currentIds = groupIds ? groupIds.split(',') : [];

		// Add new group to the id list.
		currentIds.push(groupId);

		// Update hidden input field.
		$('#cac_nsc_group_ids').val( currentIds.join(',') );
	}

	$('.cac-ncs-groups-results').on( 'click', '.cac-nsc-remove-group', function( event ) {
		event.preventDefault();

		var groupId = $(this).closest('.olsc-group').data('group-id');
		var groupIds = $('#cac_nsc_group_ids').val();
		var currentIds = groupIds ? groupIds.split(',') : [];

		var updatedIds = currentIds.filter( function( id ) {
			return Number( id ) !== groupId;
		} );

		// Update hidden input field and remove group element.
		$('#cac_nsc_group_ids').val( updatedIds.join(',') );
		$(this).closest('.olsc-group').remove();
	} );
} );
