jQuery(document).ready(function() {
	// list page size
	jQuery('#tfk_page_size ul').hide().after('<select id="tfk_page_size_js" />');
	jQuery('#tfk_page_size a').each(function() {
		var selected = jQuery(this).parent().attr('class') && jQuery(this).parent().attr('class').match('tfk_selected_page_size');
		jQuery('#tfk_page_size_js').append('<option value="' + jQuery(this).attr('href') + '"' 
				+ ( selected ? ' selected="selected"' : '' ) + '>'
				+ jQuery(this).text()
				+ '</option>');
	});
	jQuery('#tfk_page_size_js').bind('change', function () {
		var target = jQuery(this).val();
		jQuery.ajax({
			url: tznfrontjs_vars.plugins_url + '/taskfreak/ajax-npage.php?target=' + encodeURIComponent(target),
		}).done(function(data) {
			window.location = target;
		});
	});
	// list sort criteria
	jQuery('#tfk_sort_criteria ul').hide().after('<select id="tfk_sort_criteria_js" />');
	jQuery('#tfk_sort_criteria a').each(function() {
		var selected = jQuery(this).parent().attr('class') && jQuery(this).parent().attr('class').match('tfk_selected_order');
		jQuery('#tfk_sort_criteria_js').append('<option value="' + jQuery(this).attr('href') + '"' 
				+ ( selected ? ' selected="selected"' : '' ) + '>'
				+ jQuery(this).text()
				+ '</option>');
	});
	jQuery('#tfk_sort_criteria_js').bind('change', function () {
		window.location = jQuery(this).val();
		return false;
	});
	// view task history toggle
    jQuery('#tfk_task_history_toggle').addClass('tfk_task_history_hidden').attr('title', tznfrontjs_vars.task_hist_show);
	jQuery('#tfk_task_history_toggle').click(function() {
		if (jQuery('#tfk_task_history_toggle').hasClass('tfk_task_history_hidden')) {
			var $toggle = jQuery(this);
			$toggle.css('cursor', 'wait');
			jQuery.ajax({
				url: this + '&t=' + (new Date().getTime()),
				}).done(function(data) {
					jQuery('#tfk_task_history').replaceWith(jQuery('#tfk_task_history', data));
                    jQuery('#tfk_task_history_toggle').removeClass('tfk_task_history_hidden').attr('title', tznfrontjs_vars.task_hist_hide);
			}).always(function() {
				$toggle.css('cursor', 'pointer');
				jQuery('#tfk_task_history').show();
			});
		} else {
			jQuery('#tfk_task_history').hide();
            jQuery('#tfk_task_history_toggle').addClass('tfk_task_history_hidden').attr('title', tznfrontjs_vars.task_hist_show);
		}
		return false;
	});
	// view comment file upload
	jQuery('.tfk_file_more').click(function() {
		jQuery(this).hide();
		jQuery(this).parent().next().show();
		return false;
	});
	jQuery('input[type=file]').change(tfk_file_input_reset);
	jQuery('a[rel*=external]').click(function(){
		window.open(jQuery(this).attr('href'));
		return false; 
	});
	// list status changer
	jQuery('#tfk_tasksheet .tfk_sts a').attr('href', function(i,h) { return h + '&js=1'; });
	jQuery('#tfk_tasksheet .tfk_sts a').click(function() {
		var $clicked_a = jQuery(this);
		var clicked_a = { 'id': $clicked_a.attr('id').substr(9), 'level': $clicked_a.attr('id').substr(7,1) };
		jQuery('#tfk_col4-' + clicked_a['id']+ ' a').css('cursor', 'wait');
		jQuery.ajax({
			url: this + '&t=' + (new Date().getTime()),
			}).done(function(data) {
                                var m = data.match("<!-- TF!WP_status_change_result : (.*?) -->");
                                if (m) {
					var extracted_message = m[1].substr(4);
					if (m[1].substr(0,2) == 'OK') {
						for (var i = 1; i <= clicked_a['level']; i++) {
							jQuery('#tfk_sts' + i + '-' + clicked_a['id']).addClass('tfk_sts1').removeClass('tfk_sts0');
						}
						for (var i = parseInt(clicked_a['level']) + 1; i <= 3; i++) {
							jQuery('#tfk_sts' + i + '-' + clicked_a['id']).addClass('tfk_sts0').removeClass('tfk_sts1');
						}
						jQuery('#tfk_col4-' + clicked_a['id']+ ' .tfk_sts_lbl').text(extracted_message);
					} else {
						alert(extracted_message);
					}
				} else {
					alert(tznfrontjs_vars.error_message); // unknown error
				}
		}).complete(function() {
			jQuery('#tfk_tasksheet .tfk_sts a').css('cursor', 'pointer');
		});
		return false; 
	});
	// view status changer
	jQuery('#tfk_task_details .tfk_sts a').attr('href', function(i,h) { return h + '&js=1'; });
	jQuery('#tfk_task_details .tfk_sts a').click(function() {
		var $clicked_a = jQuery(this);
		var clicked_a = { 'id': $clicked_a.attr('id').substr(9), 'level': $clicked_a.attr('id').substr(7,1) };
		jQuery('#tfk_task_details .tfk_sts a').css('cursor', 'wait');
		jQuery.ajax({
			url: this + '&t=' + (new Date().getTime()),
			}).done(function(data) {
                                var m = data.match("<!-- TF!WP_status_change_result : (.*?) -->");
                                if (m) {
					var extracted_message = m[1].substr(4);
					if (m[1].substr(0,2) == 'OK') {
						for (var i = 1; i <= clicked_a['level']; i++) {
							jQuery('#tfk_sts' + i + '-' + clicked_a['id']).addClass('tfk_sts1').removeClass('tfk_sts0');
						}
						for (var i = parseInt(clicked_a['level']) + 1; i <= 3; i++) {
							jQuery('#tfk_sts' + i + '-' + clicked_a['id']).addClass('tfk_sts0').removeClass('tfk_sts1');
						}
						jQuery('#tfk_task_details .tfk_sts_lbl').text(extracted_message);
						if (!jQuery('#tfk_task_history_toggle').hasClass('tfk_task_history_hidden')) {
                                                        jQuery('#tfk_task_history_toggle').addClass('tfk_task_history_hidden').attr('title', tznfrontjs_vars.task_hist_show);
							jQuery('#tfk_task_history').hide();
						}
					} else {
						alert(extracted_message);
					}
				} else {
					alert(tznfrontjs_vars.error_message); // unknown error
				}
		}).complete(function () {
			jQuery('#tfk_task_details .tfk_sts a').css('cursor', 'pointer');
		});
		return false; 
	});
	// task edit
	jQuery('#tfk_deadline_date').datepicker({ dateFormat: tznfrontjs_vars.datepicker_format });
	jQuery('#tfk_cal_btn').click(function() { 
		jQuery('#tfk_deadline_date').datepicker('show'); 
	});
	tfk_update_select_prio_color();
	jQuery('#tfk_select_prio').change(tfk_update_select_prio_color);
	jQuery('#tfk_project').change(tfk_project_change);
	if (jQuery('#tfk_project').length > 0) {
		tfk_project_change.apply(jQuery('#tfk_project'));
	}
});

function tfk_project_change() {
	selected_user = jQuery('#tfk_user_id option[selected]').val();
	jQuery.ajax({
		url: jQuery(this).data('ajax') + '&user=' + selected_user + '&proj=' + jQuery(this).val() + '&t=' + (new Date().getTime()),
	}).done(function(data) {
		// neither jQuery('#tfk_corresponding_users option', data) nor jQuery('option', data) work on Android 2
		if (jQuery('option', data).length > 0) {
			jQuery('#tfk_user_id').html(jQuery('#tfk_corresponding_users option', data));
		}
	});
}

function tfk_file_input_reset() {
	jQuery(this).after('<a href="#" class="tfk_file_reset">×</a>');
	jQuery('.tfk_file_reset').click(function() {
		jQuery(this).hide();
		$file_input = jQuery(this).prev();
		$file_input_2 = jQuery('<input type="file" name="' + $file_input.attr('name') + '">');
		$file_input_2.change(tfk_file_input_reset);
		$file_input.after($file_input_2).remove();
		return false;
	});
}

function tfk_update_select_prio_color() {
	jQuery('#tfk_select_prio_color').removeClass().addClass('tfk_pr' + jQuery('#tfk_select_prio option:selected').val());
}
