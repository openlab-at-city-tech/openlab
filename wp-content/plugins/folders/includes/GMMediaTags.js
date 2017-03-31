jQuery(document).ready(function($) {
	var GMMediaTags = {};

	GMMediaTags.add_bulk_element = function(id, add, force_add) {
		if ( ! id ) return false;
		if ( $("select[name='action']").children("option:selected").val() == 'bulk_edit_media_tag' || $("select[name='action2']").children("option:selected").val() == 'bulk_edit_media_tag' ) {
			if ( $('#bulk-edit').length && ( $('#bulk-edit').is(':visible') || force_add ) ) {
				if (add && ! $('#ttle' + id).length ) {
					var a = '<a id="_' + id + '" class="ntdelbutton" title="' + gm_mediatags_vars.remove_from_edit + '">X</a>' + $('#post-' + id + ' td.title strong a').text();
					$('<div id="ttle' + id + '"></div>').html(a).appendTo('#bulk-titles');
				} else if ( ! add ) {
					$('#ttle' + id).remove();
				}
			}
		}
	}


	GMMediaTags.reset_bulk_edit = function() {
		$('#bulk-edit').hide();
		$('#bulk-titles').empty();
		$('#bulk-edit textarea').val('');
		$('#bulk-edit input[type="checkbox"]').attr('checked', false);
	}


	GMMediaTags.bulk_media_error = function () {
		GMMediaTags.reset_bulk_edit();
		$('div.gm_mediatags_error').remove();
		$('#icon-upload').siblings('h2').eq(0).after( '<div class="error gm_mediatags_error"><p>' + gm_mediatags_vars.update_error + '</p></div>' );
	}


	$(document).on('click', '#doaction, #doaction2', function(e) {
		if ( $("select[name='action']").children("option:selected").val() == 'bulk_edit_media_tag' || $("select[name='action2']").children("option:selected").val() == 'bulk_edit_media_tag' ) {
			e.preventDefault();
			var selected = new Array();
			$('.check-column input:checked').each(function(index, element) {
				var val = $(this).val();
				if ( val != 'on' ) selected.push(val);
			});
			if ( $('#bulk-edit').length && ! $('#bulk-edit').is(':visible') && selected.length > 0 ) {
				$.each(selected, function(index, value) {
					GMMediaTags.add_bulk_element(value, true, true);
				})
				$('#bulk-edit').show();
			} else if ( ! $('#bulk-edit').length && selected.length > 0 )  {
				$.ajax({
					type: "POST",
					url: ajaxurl,
					data: {
						action: "add_media_tag_bulk_tr",
						media_tag_ver: gm_mediatags_vars.ver_html,
						colspan: $('#the-list tr').eq(0).children(':visible').length,
						media: selected
					}
				}).done(function( markup ) {

					if ( markup ) {
						$('#the-list').prepend(markup);
						$('label.inline-edit-tags textarea').each(function() {
							$(this).suggest( ajaxurl + "?action=ajax-tag-search&tax=" + $(this).attr('class').replace('tax_input_', ''), { multiple:true, multipleSep: "," } );
						});
					}
				});
			}
		}
	});


	$(document).on('click', '#bulk-titles a.ntdelbutton', function() {
		$(this).parent().remove();
		$('#cb-select-' + $(this).attr('id').replace('_', '')).attr('checked', false);
		if ( $('#cb-select-all-1').is(':checked') ) {
			$('#cb-select-all-1').attr('checked', false);
			$('#cb-select-all-2').attr('checked', false);
		}
	});


	$(document).on('click', 'p.inline-assign_media_tag a.cancel', function() {
		GMMediaTags.reset_bulk_edit();
	});


	$(document).on('click', '#bulk_assign_media_tag', function(e) {
		e.preventDefault();
		if ( $('#bulk-titles div').length ) {
			var media = new Array();
			$('#bulk-titles div a').each( function() {
				media.push( $(this).attr('id').replace('_', '') );
			});
			$.ajax({
				type: "POST",
				dataType: "json",
				url: ajaxurl,
				data: {
					action: "save_media_tag_bulk",
					media_tag_ver: gm_mediatags_vars.ver_save,
					attachments: media,
					formData: $('#posts-filter').serialize(),
				}
			}).done(function(json) {
				if ( json.bulk_media_tag == 'updated' && json.location) {
					window.location = json.location;
				} else if ( json.bulk_media_tag == 'error' ) {
					GMMediaTags.bulk_media_error();
				}
			}).fail( function() { GMMediaTags.bulk_media_error(); } );
		}
	});



	var newoption = "<option value='bulk_edit_media_tag'>" + gm_mediatags_vars.assign_terms + "</option>";
	$("select[name='action']").children("option").eq(0).after( newoption );
	$("select[name='action2']").children("option").eq(0).after( newoption );


});
