var bpeoGroupMsg = BpEventOrganiserSettings.group_privacy_message,
	bpeoToggleFlag = false,
	bpeoCurrStatus = '';

jQuery(function($){
	var select2obj;

	bpeoSelect = $('#bp_event_organiser_metabox select');
	bpeoSubmit = $('#submitdiv .inside');
	bpeoPublicFlag = bpeoSelect.find('[title]').length;
	bpeoIsPrivate  = !!bpeoPublicFlag; // convert to boolean
	bpeoIsPrivate  = !bpeoPublicFlag;  // flip the boolean

	bpeoToggle = function() {
		var notice = bpeoSubmit.find('.updated');

		if ( true === $.isEmptyObject( bpeoSelect.val() ) ) {
			$('.misc-pub-post-status, .misc-pub-visibility').show();
			$('.edit-post-status').show();
			$('#save-post').show();
			$('#submitdiv .inside .error').hide();
			notice.fadeOut();
		} else {
			$('.misc-pub-post-status, .misc-pub-visibility').hide();
			$('#save-post').hide();
			$('#submitdiv .inside .error').show();

			if ( bpeoPublicFlag > 0 ) {
				bpeoGroupMsg = BpEventOrganiserSettings.group_public_message;

				$("#visibility-radio-public" ).prop("checked", true);
				$('#post-status-display').fadeOut('fast').text( postL10n.published ).fadeIn('fast');
				$('#post-visibility-display').fadeOut('fast').text( postL10n.public ).fadeIn('fast');
			} else {
				bpeoGroupMsg = BpEventOrganiserSettings.group_privacy_message;

				$("#visibility-radio-private" ).prop("checked", true);
				$('#post-status-display').fadeOut('fast').text( postL10n.privatelyPublished ).fadeIn('fast');
				$('#post-visibility-display').fadeOut('fast').text( postL10n.private ).fadeIn('fast');
			}

			if ( notice.length ) {
				bpeoToggleFlag = false;

				notice.fadeOut('fast');
				notice.find('p').html( bpeoGroupMsg );
				notice.fadeIn();
			} else {
				bpeoSubmit.prepend('<div class="updated"><p>' + bpeoGroupMsg + '</p></div>');
			}

		}
	}

	$('#post-status-select, #post-visibility-select, #timestampdiv').hide();

	if ( ! $( 'body' ).hasClass( 'bp-user' ) ) {
		if ( ! bpeoSubmit.find('.updated').length && typeof adminpage === 'undefined' ) {
			bpeoSubmit.prepend('<div class="updated"><p>' + bpeoGroupMsg + '</p></div>');
		}
		bpeoToggle();
	}

	// do not show "Save Draft" button for public groups when clicking on the
	// "Cancel" button when toggled from the "Publish immediately" option
	//
	// overrides WP's updateText() function
	bpeoSubmit.on( "click", ".cancel-timestamp", function() {
		if ( bpeoSubmit.find('.updated').is(':visible') ) {
			$('#save-post').hide();
		}
	});

	bpeoFormatResponse = function(data) {
		return data.name || data.text;
	}

	bpeoFormatResult = function(data) {
		if (data.loading) return data.name;

		var markup = '<div style="clear:both;">' +
		'<div style="float:left;margin-right:8px;">' + data.avatar + '</div>' +
		'<div><span style="font-weight:600;">' + data.name + '</span> <em>(' + data.type + ')</em></div>';

		if (data.description) {
			markup += '<div style="font-size:.9em;line-height:1.9;">' + data.description + '</div>';
		}

		markup += '</div>';

		return markup;
	}

	select2obj = bpeoSelect.select2({
		ajax: {
			method: 'POST',
			url: ajaxurl,
			dataType: 'json',
			delay: 500,
			data: function (params) {
				return {
					s: params.term, // search term
					action: 'bpeo_get_groups',
					nonce: $('#bp_event_organiser_nonce_field').val(),
					page: params.page
				};
			},
			cache: true,
			processResults: function (data, page) {
				// parse the results into the format expected by Select2.
				// since we are using custom formatting functions we do not need to
				// alter the remote JSON data
				return {
					results: data
				};
			},
		},
		escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
		minimumInputLength: 3,
		templateResult: bpeoFormatResult,
		templateSelection: bpeoFormatResponse
	});

	bpeoSelect.on("select2:unselecting", function (e) {
		if ( 'Public' == e.params.args.data.title || true === e.params.args.data.public ) {
			bpeoPublicFlag--;
		}
	});

	bpeoSelect.on("select2:selecting", function (e) {
		if ( 'Public' == e.params.args.data.title || true === e.params.args.data.public ) {
			bpeoPublicFlag++;
		}
	});

	bpeoSelect.on("select2:unselect select2:select", function (e) {
		bpeoToggleFlag = true;
		bpeoToggle();
	});

	bpeoSelect.on( 'change', function() {
		checkGroupOrganizer();
	} );

	if ( select2obj ) {
		var $silent_wrapper = $( '#bpeo-silent-wrapper' );

		// Move the silent checkbox to where it belongs.
		$silent_wrapper.insertBefore( '#publishing-action' );

		function checkGroupOrganizer() {
			// If a group has been selected, and we haven't already added the silent-ness
			// checkbox, then add it
			var groups = select2obj.val();
			if ( groups && groups.length >= 1 ) {
				$silent_wrapper.show();
			} else {
				$silent_wrapper.hide();
			}
		}

		checkGroupOrganizer();
	}
});
