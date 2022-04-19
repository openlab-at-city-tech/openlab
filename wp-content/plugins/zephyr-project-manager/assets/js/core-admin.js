var zephyrSocket = false;

if (zpm_localized.settings.node_enabled) {
	zephyrSocket = io('https://zephyr-project-manager.eu-4.evennode.com/');
}

var ZPM_Manager = new ZephyrProjects();

if (zephyrSocket) {
	zephyrSocket.on('connect', function(data) {
		zephyrSocket.emit('join', zpm_localized.website);
	});
}

var zpmProjects = [];

jQuery(document).ready(function($) {

	if (zephyrSocket) {
		zephyrSocket.on('task-created', function(data) {
			//ZephyrProjects.sendDesktopNotification( 'New Task Created', 'A new task has been created called ' + data.name, 'icon.jpg' );
		});
	}

	// Initialization functions
	cct_initialize();
	zpmSetupRippleEffect();

	ZephyrProjects.get_projects(function(response){
		zpmProjects = response;
	});

	// Initialize
	function cct_initialize() {
		var task_loading_ajax = null;
		if (ZephyrProjects.isCalendarPage()) {
			ZephyrProjects.initialize_calendar();
		}
		var dateFormat = 'yy-mm-dd';
		var timeFormat = 'HH:mm';
		//dateFormat = 'yy-mm-dd H:i';
		$('#zpm_project_color').wpColorPicker();
		$('#zpm_category_color').wpColorPicker();
		$('.zpm-color-picker').wpColorPicker();
		$('#zpm_colorpicker_primary, #zpm_colorpicker_primary_dark, #zpm_colorpicker_primary_light').wpColorPicker();
		$('#zpm_edit_project_start_date').datepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_edit_project_due_date').datepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_new_task_start_date').datetimepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_new_task_due_date').datetimepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_edit_task_start_date').datetimepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_edit_task_due_date').datetimepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('#zpm_edit_project_end_date').datepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$("#zpm_project_due_date").datepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$("#zpm_task_due_date").datetimepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$(".zpm-datepicker").datepicker({dateFormat: dateFormat, timeFormat:  timeFormat });
		$('body').append('<div id="zpm_notifcation_holder"></div>');
		$('.zpm_comment_content').each(function(){
			$(this).html(ZephyrProjects.linkify($(this).html()));
		});
	}

	$('body').find('.zpm-multi-select').chosen({
	    disable_search_threshold: 10,
	    width: '100%'
	});

	$('body').find('.zpm-chosen-select').chosen({
	    disable_search_threshold: 10,
	    width: '100%'
	});

	// Dropdown menu
	$('body').on('click', function(e){
		target = $(e.target);
		if (target.hasClass('disabled')) {
			return;
		}
		if (target.find('.zpm_dropdown_menu').hasClass('active')) {
			target.find('.zpm_dropdown_menu').removeClass('active');
			return;
		}
		if (target.hasClass('active')) {
			return;
		}
		if (!$(e.target).data('zpm-pro-upsell')) {
			$('body').find('.zpm-pro-notice').removeClass('active');
		}
		$('.zpm_dropdown_menu').removeClass('active');
		if ( target.hasClass('zpm_taskbar_link') || target.hasClass('zpm_taskbar_list_item') ) {
			target.closest('.zpm_taskbar_list_item').find('.zpm_dropdown_menu').toggleClass('active');
		} else if ( target.hasClass('zpm_options_button') || target.hasClass('zpm_project_grid_options') || target.hasClass('zpm_project_grid_options_icon') || target.hasClass('zpm_category_option_icon') ) {
			target.find('.zpm_dropdown_menu').toggleClass('active');
		}
	});

	$('[zpm-open-modal]').on('click', function(){
		var modal_id = $(this).attr('zpm-open-modal');
		ZephyrProjects.open_modal( modal_id );
	});

	$('.zpm_tab').on('click', function(){
		var target = $(this).data('target');
		$('.zpm_tab').removeClass('active');
		$('.tab-pane').removeClass('active');
		$(this).addClass('active');
		$('.tab-pane[data-section="' + target + '"]').addClass('active');
	});

	$('body').on('click', '[data-zpm-tab-trigger]', function() {
		var tab = $(this).data('zpm-tab-trigger');
		$('body').find('[data-zpm-tab-trigger]').removeClass('zpm_tab_selected');
		$(this).addClass('zpm_tab_selected');
		$('body').find('[data-zpm-tab]').removeClass('zpm_tab_active');
		$('body').find('[data-zpm-tab="' + tab + '"]').addClass('zpm_tab_active');
	});

	/* Upload Profile Picture */
	var mediaUploader;

	$('.zpm_settings_profile_picture').on('click', function() {
		if (mediaUploader) {
			mediaUploader.open();
			return;
		}

		mediaUploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.choose_image,
			button: {
			text: zpm_localized.strings.choose_image
		}, multiple: false });

	    var image_holder = $('.zpm_settings_profile_image');
	    var image_input = $('#zpm_profile_picture_hidden');

		mediaUploader.on('select', function() {
			var attachment = mediaUploader.state().get('selection').first().toJSON();
			image_holder.css('background-image', 'url(' + attachment.url + ')');
			image_input.val(attachment.url);
		});
		// Open the uploader dialog
		mediaUploader.open();
	});

	// Import tasks via CSV
	var csvFileUploader;

	$('#zpm_import_tasks_from_csv').on('click', function() {
		if (csvFileUploader) {
			csvFileUploader.open();
			return;
		}

		csvFileUploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.select_csv,
			button: {
			text: zpm_localized.strings.choose_file
		}, multiple: false });

		csvFileUploader.on('select', function() {
			var attachment = csvFileUploader.state().get('selection').first().toJSON();
			zpm_import_tasks(attachment);
		});
		// Open the uploader dialog
		csvFileUploader.open();
	});

	// Import tasks via JSON
	var jsonFileUploader;
	$('#zpm_import_tasks_from_json').on('click', function() {
		if (jsonFileUploader) {
			jsonFileUploader.open();
			return;
		}

		jsonFileUploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.select_json,
			button: {
			text: zpm_localized.strings.choose_file
		}, multiple: false });

		jsonFileUploader.on('select', function() {
			var attachment = jsonFileUploader.state().get('selection').first().toJSON();
			zpm_import_tasks(attachment);
		});
		// Open the uploader dialog
		jsonFileUploader.open();
	});

	// Reset profile picture
	$('#zpm_reset_profile_picture').on('click', function() {
		default_image = $('#zpm_gravatar').val();
		var image_holder = $('.zpm_settings_profile_image');
	    var image_input = $('#zpm_profile_picture_hidden');

	    image_holder.css('background-image', 'url(' + default_image + ')');
		image_input.val(default_image);
	});

	/* Charts and Data Vizualization */
	var projects = [];
	var labels = [];

	$.getJSON( zpm_localized.rest_url + 'projects', function( data ) {
		$.each( data, function( key, val ) {
			projects.push({
				id: val.id,
				name: val.name,
				description: val.description,
				date_created: val.date_created,
				date_due: val.date_due,
				completed: val.completed
			});
			labels.push(val.name);
		});

		var canvas = $("#myChart");
		if (canvas.length) {
			var ctx = canvas;
			var chart = new Chart(ctx, {
			    type: 'bar',
			    data: {
			        labels: labels,
			        datasets: [{
			            label: zpm_localized.strings.tasks_completed,
			            data: [12, 19, 3, 5],
			            backgroundColor: [
			                'rgba(255, 99, 132, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)'
			            ],
			            borderColor: [
			                'rgba(255,99,132,1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)'
			            ],
			            borderWidth: 1
			        }, {
			            label: zpm_localized.strings.tasks_remaining,
			            data: [10, 5, 9, 17],
			            backgroundColor: [
			                'rgba(255, 99, 132, 0.2)',
			                'rgba(54, 162, 235, 0.2)',
			                'rgba(255, 206, 86, 0.2)',
			                'rgba(75, 192, 192, 0.2)'
			            ],
			            borderColor: [
			                'rgba(255,99,132,1)',
			                'rgba(54, 162, 235, 1)',
			                'rgba(255, 206, 86, 1)',
			                'rgba(75, 192, 192, 1)'
			            ],
			            borderWidth: 1
			        }]
			    },
			    options: {
			        scales: {
			            yAxes: [{
			                ticks: {
			                    beginAtZero:true
			                }
			            }]
			        }
			    }
			});
		}
	});

	// Display task reminders and notifications
	ZephyrProjects.task_reminders();

	jQuery('body').on('click', '.zpm_floating_notification_button', function(){
		var notification = jQuery(this).closest('.zpm_floating_notification');
		var task_id = notification.data('id');
		var item = notification.data('item');
		notification.addClass('dismissed');
		localStorage.setItem(item + task_id, true);
	});

	$('#zpm_add_new_btn').on('click', function() {
		$(this).toggleClass('active');
	});

	$('#zpm_create_quickproject, #zpm_first_project, #zpm_create_new_project').on('click', function() {
		zpm_close_quicktask_modal();
		ZephyrProjects.open_modal('zpm_project_modal');
		$('.zpm_project_name_input').focus();
	});

	$('.zpm_fancy_item').on('click', function(){
		zpm_close_quicktask_modal();
	});

	function zpm_close_quicktask_modal() {
		$('body').find('#zpm_add_new_btn').removeClass('active');
		$('body').find('#' + $('body').find('#zpm_add_new_btn').data('zpm-dropdown-toggle')).removeClass('active');
	}

	$('body').on('click', '.zpm_close_modal', function() {
		var modalId = $(this).closest('.zpm-modal').attr('id');
		ZephyrProjects.close_modal();
	});

	$('body').on('click', '[data-zpm-trigger="remove_modal"]', function(){
		var modalId = $(this).closest('.zpm-modal').attr('id');
		ZephyrProjects.remove_modal( modalId );
	});

	$('body').on('click', '.zpm-modal-background[data-zpm-trigger="remove_modal"]', function(){
		$('.zpm-modal').remove();
	});

	$('body').on('click', '[data-zpm-trigger="close_modal"]', function(){
		ZephyrProjects.close_modal();
	});

	// Close modals when clicking on modal background
	$('body').on('click', '.zpm_modal_background', function() {
		var backgroundId = $(this).attr('id');

		if (backgroundId == 'zpm_quickview_project_background') {
			var modalId = 'zpm_quickview_modal';
			ZephyrProjects.remove_modal(modalId);
		} else {
			ZephyrProjects.close_modal();
		}
	});

	// New Project Modal
	//var open_modal = $('#zpm_add_new_btn');
	//var close_modal = $('#zpm_project_modal .close');

	var modal = $('.zpm-modal');

	$(window).on('click', function(e){
		if ($(e.target)[0] == modal[0]) {
	        $('body').find('.zpm_modal_background').remove();
    		$('body').find('.zpm-modal').remove();
    	}
	});

	$('body').on('click', '.zpm_project_title', function(e) {
		e.preventDefault();
		if (e.target.className.indexOf('zpm_project_grid_options') > -1) { return; };
		var title = $(this).text();
		var content = '<input type="text" name="zpm_project_title"/>';
		content += '<input type="text" name="zpm_project_description">';
	});

	// Custom Select Fields
	$('body').on('click', '.zpm_select_option', function(e){
		var data = $(this).data();
		var optionValue = $(this).html();

		$(this).closest('.zpm_select_dropdown').find('.zpm_select_option').each(function(){
			$(this).removeClass('selected');
		});

		$(this).closest('.zpm_select_trigger').find('.zpm_selected_option').html(optionValue);
		$(this).closest('.zpm_select_trigger').removeClass('active');
		$(this).closest('.zpm_select_dropdown').removeClass('active');
		$(this).addClass('selected');
	});

	$('body').on('click', '.zpm_select_trigger', function(e){
		var data = $(this).data();
		$('.zpm_select_trigger').each(function(){
			var thisData = $(this).data();
		});

		if (e.target.className.indexOf('zpm_select_option') <= -1) {
			$(this).toggleClass('active');
			$(this).find('.zpm_select_dropdown').toggleClass('active');
		}
	});

	function zpmNewModal(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {
		var modal_navigation = (typeof navigation !== 'undefined' && navigation !== '') ? navigation : '';
		var modal_settings = (typeof options !== 'undefined' && options !== '') ? '<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown"><i class="dashicons dashicons-menu"></i>' + options + '</span>' : '';
		var modal = '<div id="' + modal_id + '" class="zpm-modal" data-modal-action="remove" data-task-id="' + task_id + '" data-project-id="' + project_id + '">' +
					'<div class="zpm_modal_body">' +
						'<h2>' + subject + '</h2>' +
						'<h3 class="zpm_modal_task_name">' + header + '</h3>' + modal_settings +
						modal_navigation +
						'<div class="zpm_modal_content">' + content + '</div>' +
						'<div class="zpm_modal_buttons">' + buttons + '</div>' +
					'</div>' +
				'</div';
		$('body').append(modal);
		ZephyrProjects.open_modal(modal_id);
	}

	$('body').on('click', '#zpm_quick_task_add', function(){
		$('body').find('.zpm_quicktask_container').toggleClass('active');
	});

	ZephyrProjects.refreshProjectsProgressBar();

	$('body').on('click', '.zpm_project_title', function(e){
		e.preventDefault();
		var menu_ids = [
			'zpm_add_project_to_dashboard',
			'zpm_delete_project',
			'zpm-project-action__archive',
			'zpm_copy_project',
			'zpm_export_project',
			'zpm_export_project_to_csv',
			'zpm_export_project_to_json',
			'zpm_print_project'
		];

		if (e.target.className.indexOf('zpm_project_grid_options') > -1 || $.inArray($(e.target).attr('id'), menu_ids) > -1 ) {
			return;
		};

		var title = $(this).find('.zpm_project_grid_name').text();
		var description = $(this).closest('.zpm_project_grid_cell').find('.zpm_project_description').text();
		var project_link = $(this).attr('href');
		var project_id = $(this).data('project_id');
		var buttons = '<a class="zpm_button" href="' + project_link + '">' + zpm_localized.strings.go_to_project + '</a>';
		var task_data = {
			project_id: project_id
		};

		ZephyrProjects.open_modal('zpm_quickview_modal');
		var project_view_modal = $('body').find('#zpm_quickview_modal');
		project_view_modal.html('<div class="zpm_task_loader"></div>');
		var data = {
			action: 'zpm_view_project',
			project_id: project_id
		}

		ZephyrProjects.ajax( data, function(response){
			project_view_modal.find('.zpm_task_loader').remove();
			project_view_modal.html(response.html);
			$('body').find('.zpm_quicktask_content #zpm_quicktask_date').datepicker({dateFormat: 'yy-mm-dd' });
			$('body').find('.zpm_quicktask_content #zpm_quicktask_select_assignee').chosen({
			    disable_search_threshold: 10,
			    no_results_text: zpm_localized.strings.no_users_found,
			    width: "100%"
			});

			$('body').find('#zpm_quickview_modal').attr('data-project-id', data.project_id);

			ZephyrProjects.get_tasks(task_data, function(response){
				//$('body').find('.zpm_task_loader').remove();

				//$('body').find('#zpm_quickview_modal .zpm_modal_content').html( response );
			});
		});
		// ZephyrProjects.get_project( data, function( response ) {
		// 	$('body').find('#zpm-project-modal-overview').html(response.overview_html);
		// 	$('body').find('#zpm-project-modal-discussion').html(response.comments_html);
		// 	$('body').find('#zpm-project-modal-due-date').html(response.date_due);
		// 	$('body').find('#zpm-project-preview__header').after(response.priority_html);
		// });
	});

	$('body').on('click', '.zpm_task_complete', function(){
		$(this).toggleClass('completed');
	});

	/* Open new task modal on Tasks page */
	$('body').on('click', '#zpm_task_add_new', function(){
		jQuery.event.trigger( { type: 'zephyr_new_task_modal_opened', data: {} } );
		$('body').find('#zpm_create_task').addClass('active');

		ZephyrProjects.open_modal();
		$('body').find('#zpm-new-task-template-select').trigger('change');
		$('body').find('select#zpm_new_task_project').chosen({
		    disable_search_threshold: 10,
		    no_results_text: zpm_localized.strings.no_projects_found,
		    width: "100%"
		});
	});

	/* Open new task modal for the QuickAdd menu */
	$('body').on('click', '#zpm_quickadd_task', function(){
		jQuery.event.trigger( { type: 'zephyr_new_task_modal_opened', data: {} } );
		ZephyrProjects.open_modal('zpm_create_task');
		var modal = jQuery('body').find('#zpm_create_task');
		modal.find('[data-ajax-name="parent-id"]').val('-1');
		$('body').find('#zpm-new-task-template-select').trigger('change');
		$('body').find('select#zpm_new_task_project').chosen({
		    disable_search_threshold: 10,
		    no_results_text: zpm_localized.strings.no_projects_found,
		    width: "100%"
		});
	});

	/* Open new task modal when there are no tasks */
	$('body').on('click', '#zpm_first_task', function(){
		$('body').find('#zpm_task_add_new').trigger('click');
	});

	/* Open new task modal on Project editor page */
	$('body').on('click', '#zpm_add_new_project_task', function() {
		ZephyrProjects.open_modal('zpm_create_task');
		$('body').find('#zpm-new-task-template-select').trigger('change');
	});

	var zpm_new_task_files = [];
	var zpm_new_task_uploader;

	$('body').on('click', '#zpm-new-task__new-file', function(){
		if (zpm_new_task_uploader) {
			zpm_new_task_uploader.open();
			return;
		}

		zpm_new_task_uploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.choose_file,
			button: {
			text: zpm_localized.strings.choose_file
		}, multiple: 'add' });

		zpm_new_task_uploader.on('select', function() {
			var attachments = zpm_new_task_uploader.state().get('selection').map(

                function( attachment ) {

                    attachment.toJSON();
                    return attachment;

            });

           for (var i = 0; i < attachments.length; ++i) {
				zpm_new_task_files.push({attachment_id: attachments[i].id});
				$("#zpm_create_task .zpm_modal_body").animate({ scrollTop: $('#zpm_create_task .zpm_modal_body').prop("scrollHeight")}, 1000);
				$('#zpm-new-task__attachments').append('<div class="zpm-new-task__attachment"><a href="' + attachments[i].attributes.url + '" target="_blank">' + attachments[i].attributes.url + '</a></div>');

            }
		});
		// Open the uploader dialog
		zpm_new_task_uploader.open();

	});

	/* Create a new task */
	$('#zpm_save_task').on('click', function() {
		// Close modal, save task and update task list
		var form = $(this).closest('.zpm-form');
		var name = $('#zpm_new_task_name').val();
		var description = $('#zpm_new_task_description').val();
		var project = $('#zpm_new_task_project').val();
		var assignee = $('#zpm_new_task_assignee').val();
		var due_date = $('#zpm_new_task_due_date').val();
		var start_date = $('#zpm_new_task_start_date').val();
		var team = $('#zpm-new-task-team-selection').val();
		var priority = $('body').find('#zpm-new-task-priority-value').val();
		var status = $('body').find('#zpm-new-task__status').val();
		var is_recurring = $('body').find('#zpm-new-task__type-daily').is(':checked');
		var type = is_recurring ? 'daily' : 'default';

		var recurrence_type = $('#zpm-new-task__recurrence-select').val();
		var recurrence_data = {};
		recurrence_data.type = recurrence_type;

		switch(recurrence_type) {
			case 'daily':
				var days = $('#zpm-new-task__recurrence-daily').val();
				var expiration = $('#zpm-new-task__recurrence-expiration-date').val();

				recurrence_data.days = days;
				recurrence_data.expires = expiration;
				break;
			case 'weekly':
				var expiration = $('#zpm-new-task__recurrence-expiration-date-weekly').val();
				recurrence_data.expires = expiration;
				break;
			case 'monthly':
				var expiration = $('#zpm-new-task__recurrence-expiration-date-monthly').val();
				recurrence_data.expires = expiration;
				break;
			case 'annually':
				var expiration = $('#zpm-new-task__recurrence-expiration-date-annual').val();
				recurrence_data.expires = expiration;
				break;
			default:
				break;
		}

		$('#zpm_new_task_project').val('').trigger("chosen:updated");
		$('#zpm_new_task_assignee').val('').trigger("chosen:updated");
		$('#zpm-new-task-team-selection').val('').trigger("chosen:updated");

		var custom_fields = [];
		$('body').find('#zpm_create_task .zpm_task_custom_field').each(function(){
			var id = $(this).data('zpm-cf-id');
			var type = $(this).data('zpm-cf-type');
			var value = $(this).val();

			if (type == "checkbox") {
				value = $(this).is(':checked');
			}

			if (type == 'rating') {
				value = $(this).find('.zpm-rating-field__value').val();
			}

			custom_fields.push({
				id: id,
				value: value
			});

		});

		if (name == '') {
			alert( zpm_localized.strings.enter_task_name );
			return;
		}


		if (due_date !== "" && start_date !== "") {

			dateStart = Date.parse(start_date);
			dateEnd = Date.parse(due_date);

			if (dateStart > dateEnd) {
				alert("Due Date should be greater than the Start Date.");
				return;
			}
		}

		var subtasks = [];

		$('.zpm_task_subtask_item').each(function(){
			if ($(this).val() !== '') {
				subtasks.push($(this).val());
			}
		})

		var data = {
        	task_name: name,
			task_description: description,
			subtasks: subtasks,
			task_project: project,
			task_assignee: assignee,
			task_due_date: due_date,
			task_start_date: start_date,
			task_custom_fields: custom_fields,
			team: team,
			priority: priority,
			status: status,
			type: type,
			recurrence: recurrence_data
        };

        form.find('[data-ajax-name]').each(function(){
			var name = jQuery(this).data('ajax-name');
			var value = jQuery(this).val();
			data[name] = value;
		});

        if ($('body').find('#zpm-new-task-kanban-id').length > 0 && $('body').find('#zpm-new-task-kanban-id').val() !== '') {
        	data.kanban_col = $('#zpm-new-task-kanban-id').val();
        } else if (jQuery('body').find('.zpm_kanban_heading').length > 0) {
        	data.kanban_col = jQuery('body').find('.zpm_kanban_heading').first().data('kanban-id');
        }

        ZPM_Manager.set_new_task_data(data);
        ZPM_Manager.create_task(function(response){
        	if ( zpm_new_task_files.length > 0 ) {
				ZephyrProjects.upload_new_task_attachments( response.id, zpm_new_task_files );
			}

			zpm_new_task_files = [];
			$('#zpm-new-task__attachments').html('');
			jQuery.event.trigger( { type: 'zephyr_task_created', ndata: response } );

        	var new_task = 	response.new_task_html;
			var taskList = $('body').find('.zpm_task_list');
			if (response.parent_id !== '-1') {
				taskList = $('body').find('#zpm_subtask_list');
			}

			taskList.prepend(new_task);
			$('body').find('.zpm_message_center').remove();
			$('body').find('#zpm_task_option_container').removeClass('zpm_hidden');
			$('body').find('#zpm_task_list_container').removeClass('zpm_hidden');
			$('body').find('.zpm_no_results_message').addClass('zpm_hidden');
			$('.zpm_no_results_message').hide();

			if ($('body').find('.zpm_kanban_container').length !== 0) {
				var container = $('body').find('.zpm-delete-kanban-row[data-kanban-id="' + response.kanban_col + '"]').closest('.zpm_kanban_row').find('.zpm_kanban_container');
				container.append(response.kanban_html);
				container.animate({ scrollTop: container.prop("scrollHeight")}, 1000);
			}
        });


		ZephyrProjects.close_modal();

		$('body').find('#zpm_create_task #zpm_new_task_name').val('');
		$('body').find('#zpm_create_task #zpm_new_task_description').val('');
		$('body').find('#zpm_create_task #zpm_new_task_due_date').val('');
		if ($('body').find('#zpm-project-id').length <= 0) {
			$('body').find('#zpm_create_task #zpm_new_task_project').val('-1');
		}
		$('body').find('#zpm_create_task #zpm_new_task_assignee').val('-1');
		$('body').find('#zpm_create_task #zpm_task_subtasks').html('');
	});

	$('#zpm_save_changes_task').on('click', function() {

		var form = jQuery(this).closest('#zpm_task_editor_settings');
		var taskId = $(this).data('task-id');
		var name = $('#zpm_edit_task_name').val();
		var description = $('#zpm_edit_task_description').val();
		var assignee = $('#zpm_edit_task_assignee').val();
		var due_date = $('#zpm_edit_task_due_date').val();
		var start_date = $('#zpm_edit_task_start_date').val();
		let project_id = $('body').find('#zpm_edit_task_project').val();
		var team = $('body').find('#zpm-edit-task-team-selection').val();
		var priority = $('body').find('#zpm-edit-task-priority-value').val();
		var status = $('body').find('#zpm-edit-task__status').val();
		var is_recurring = $('body').find('#zpm-edit-task__type-daily').is(':checked');
		var type = is_recurring ? 'daily' : 'default';
		var subtasks = [];
		var custom_fields = [];

		var recurrence_type = $('#zpm-edit-task__recurrence-select').val();
		var recurrence_data = {};
		recurrence_data.type = recurrence_type;
		var recurrenceParent = '';

		switch(recurrence_type) {
			case 'daily':
				var days = $('#zpm-edit-task__recurrence-daily').val();
				var expiration = $('#zpm-edit-task__recurrence-expiration-date').val();
				recurrenceParent = $('body').find('#zpm-edit-task__recurrence [data-section="daily"]');
				recurrence_data.days = days;
				recurrence_data.expires = expiration;
				break;
			case 'weekly':
				var expiration = $('#zpm-edit-task__recurrence-expiration-date-weekly').val();
				recurrence_data.expires = expiration;
				recurrenceParent = $('body').find('#zpm-edit-task__recurrence [data-section="weekly"]');
				break;
			case 'monthly':
				var expiration = $('#zpm-edit-task__recurrence-expiration-date-monthly').val();
				recurrence_data.expires = expiration;
				recurrenceParent = $('body').find('#zpm-edit-task__recurrence [data-section="monthly"]');
				break;
			case 'annually':
				var expiration = $('#zpm-edit-task__recurrence-expiration-date-annual').val();
				recurrence_data.expires = expiration;
				recurrenceParent = $('body').find('#zpm-edit-task__recurrence [data-section="annually"]');
				break;
			default:
				break;
		}

		if (recurrenceParent !== '') {
			recurrence_data.frequency = recurrenceParent.find('[data-ajax-name="frequency"]').val();
			recurrence_data.start = recurrenceParent.find('[data-ajax-name="recurrence-start"]').val();
		}

		if (name == "") {
			alert(zpm_localized.strings.enter_task_name);
			return;
		}

		if (start_date !== "" && due_date !== "") {
			var startDate = Date.parse(start_date);
			var endDate = Date.parse(due_date);
			if (endDate < startDate) {
				alert("Due date should greated than start date.");
				return;
			}
		}

		$('body').find('#zpm_task_edit_custom_fields .zpm_task_custom_field').each(function(){
			var id = $(this).data('zpm-cf-id');
			var type = $(this).data('zpm-cf-type');
			var value = $(this).val();

			if (type == "checkbox") {
				var value = $(this).is(':checked');
			}

			if (type == 'rating') {
				value = $(this).find('.zpm-rating-field__value').val();
			}

			custom_fields.push({
				id: id,
				value: value
			});
		});

		$(this).html( zpm_localized.strings.saving );

		var data = {
			task_id: taskId,
			task_name: name,
			task_description: description,
			task_assignee: assignee,
			task_subtasks: subtasks,
			task_due_date: due_date,
			task_start_date: start_date,
			task_custom_fields: custom_fields,
			task_project: project_id,
			team: team,
			priority: priority,
			status: status,
			type: type,
			recurrence: recurrence_data
		};

		form.find('[data-ajax-name]').each(function(){
			var name = jQuery(this).data('ajax-name');
			var value = jQuery(this).val();
			data[name] = value;
		});

		ZephyrProjects.update_task(data, function(response){
			var background = jQuery('#zpm-edit-task-priority').css('background-color');
			var text_color = "#fff";
			$('#zpm_save_changes_task').html( zpm_localized.strings.save_changes );
			if (priority == "" || priority == "priority_none") {
				$('#zpm-task-edit-priority-label').addClass('zpm-label-hidden');
				text_color = "#333";
			} else {
				$('#zpm-task-edit-priority-label').removeClass('zpm-label-hidden');
			}
			$('#zpm-task-edit-priority-label').removeClass('priority_high').removeClass('priority_low').removeClass('priority_medium').removeClass('priority_critical').addClass(priority).text($('.zpm-edit-task-priority[data-value="' + priority + '"]').text());


			$('#zpm-task-edit-priority-label').attr('style', 'background: ' + background + ' !important; color: ' + text_color + ' !important;');
			ZephyrProjects.notification(zpm_localized.strings.changes_saved);
		});
	});

	/* Update task completion status */
	$('body').on('click', '.zpm_task_mark_complete', function() {
		var task_id = $(this).data('task-id');
		if ($(this).is(':checked')) {
			var data = {
				id: task_id,
				completed: 1
			}

			ZephyrProjects.complete_task(data, function(response){});
			$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').addClass('zpm_task_complete');
			$('body').find('.zpm_task_mark_complete[data-task-id="' + task_id + '"]').attr('checked', 'checked');
		} else {
			$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').removeClass('zpm_task_complete');
			$('body').find('.zpm_task_mark_complete[data-task-id="' + task_id + '"]').removeAttr('checked');
			var data = {
				id: task_id,
				completed: 0
			}

			ZephyrProjects.complete_task(data, function(response){});
		}
	});

	/* Mark a task as complete */
	$('body').on('click', '.zpm_task_mark_complete', function(){
		var checked = $(this).is(':checked');
		var task_id = $(this).closest('li').data('task-id');

		if (checked == 'checked') {
			$(this).closest('li').addClass('zpm_task_completed');
		} else {
			$(this).closest('li').removeClass('zpm_task_completed');
		}
	});

	/* Delete a task from the list */
	$('body').on('click', '.zpm_delete_task', function() {
		var task_id = $(this).closest('li').data('task-id');
		$(this).closest('li').hide();
	});

	// Select project type
	$('body').on('click', '.zpm_modal_item', function() {
		var type = $(this).find('.image').data('project-type');
		$('#zpm-project-type').val(type);
		$('body').find('.zpm_modal_item .image').removeClass('zpm_project_selected');
		$(this).find('.image').addClass('zpm_project_selected');
	});

	// Add new project via modal
	$('body').on('click', '#zpm_modal_add_project', function() {
		var name = $(this).closest('#zpm_project_modal').find('.zpm_project_name_input').val();
		var project_type = $(this).closest('#zpm_project_modal').find('.zpm_project_selected').data('project-type');
		var description = $('body').find('#zpm-new-project-description').val();
		var type = $('body').find('#zpm-project-type').val();
		var priority = $('body').find('#zpm-new-project-priority-value').val();
		var categories = $('body').find('#zpm-new-project__categories').val();
		var modal = $(this).closest('#zpm_project_modal');
		if (name == '') {
			$(this).closest('#zpm_project_modal').find('.zpm_project_name_input').after('<span class="zpm_validation_error">' + zpm_localized.strings.enter_project_name + '</span>');
		} else {
			modal.find('.zpm_project_name_input').val('');
			ZephyrProjects.close_modal();

			var data = {
				project_name: name,
				project_description: description,
				project_categories: '',
				project_due_date: '',
				type: type,
				priority: priority,
				categories: categories
			};

			ZPM_Manager.create_project( data, function(response){
				jQuery.event.trigger( { type: 'zephyr_project_created', ndata: response } );
				$('body').find('#zpm_project_manager_display').removeClass('zpm_hide');
				$('body').find('.zpm_no_results_message').hide();
				$('body').find('.zpm_project_grid').prepend(response.html);
			});

			if (categories) {
				categories.forEach(function(cat){
					var val = $('.zpm-category__grid-cell[data-category-id="' + cat + '"]').find('.zpm-category-card__count-value');
					var count = parseInt(val.text());
					val.text(count + 1);
				});
			}
		}
	});

	var task_loading_ajax = null;

	// Selected task from list
	$('body').on('click', '.zpm_task_list_row', function(e) {
		//e.preventDefault();

		if (e.target.className.indexOf('zpm_task_mark_complete') > -1 || e.target.className.indexOf('zpm-material-checkbox-label') > -1) {

			//return;
		} else {
			e.preventDefault();
			var data = $(this).data();
			var task_name = data.taskName;
			var task_id = data.taskId;
			var task_view_modal = $('body').find('#zpm_task_view_container');
			task_view_modal.html('<div class="zpm_task_loader"></div>');

			var data = {
				task_id: task_id
			}

			if ($(this).closest('#zpm_quickview_modal').length > 0) {
				var url = zpm_localized.tasks_url + '&action=view_task&task_id=' + task_id;
				var win = window.open(url, '_blank');
	  			win.focus();
			} else {
				ZephyrProjects.view_task(data, function(response){
					task_view_modal.html(response);
				});

				ZephyrProjects.open_modal('zpm_task_view_container');
				$('body').find('#zpm_task_view_container').attr('data-task-id', task_id);
			}
		}
	});

	$('body').on('click', '#zpm_create_quicktask', function() {
		var quickTaskDataHolder = $(this).closest('.zpm_quicktask_container');
		var taskName = quickTaskDataHolder.find('#zpm_quicktask_name');
		var taskDescription = quickTaskDataHolder.find('#zpm_quicktask_description');
		var taskDueDate = quickTaskDataHolder.find('#zpm_quicktask_date');
		var taskProject = $(this).closest('.zpm-modal').data('project-id');
		var taskAssignee = quickTaskDataHolder.find('#zpm_quicktask_select_assignee');
		quickTaskDataHolder.removeClass('active');

		$('body').find('#zpm_quick_task_add').html( zpm_localized.strings.saving ).addClass('saving');

		var data = {
			task_project: taskProject,
			task_name: taskName.val(),
			task_description: taskDescription.val(),
			task_assignee: taskAssignee.val(),
			task_due_date: taskDueDate.val()
		}

		ZephyrProjects.create_task(data, function(response){
			$('body').find('.zpm_task_list').prepend(response.new_task_html);
			$('body').find('#zpm_quick_task_add').html( zpm_localized.strings.add_task ).removeClass('saving');
			taskName.val(''); taskDescription.val(''); taskDueDate.val(''); taskAssignee.val('');
			$('body').find('#zpm_tasks_tab .zpm_message_center').remove();
		});
	});

	// Execute file actions
	$('body').on('click', '.zpm_file_action, .zpm_file_preview', function(){
		var action = $(this).data('zpm-action');
		var target = $(this).closest('.zpm_file_item');
		var $container = $(this).closest('.zpm_file_item_container');
		var projectId = $container.data('project-id');
		var fileId = target.data('attachment-id');

		if (action == 'download_file') {
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = target.data('attachment-name');
			link.href = target.data('attachment-url');
			link.click();
		}
		if (action == 'show_info') {
			var file_data = target.data();
			var html = '<div><label class="zpm_label">' + zpm_localized.strings.file_link + '</label>\
			<p><a class="wppm_link" href="' + file_data.attachmentUrl + '">' + file_data.attachmentUrl + '</a></p>\
			<label class="zpm_label">' + zpm_localized.strings.project + '</label>\
			<input type="hidden" id="zpm-file-manager__last-file-id" /> \
			<p><select id="zpm-file-manager__project-change" class="zpm_input">';

			zpmProjects.forEach(function(project){
				html = html + '<option value="' + project.id + '" ';
				if (projectId == project.id) {
					html += 'selected="selected" ';
				}
				html = html + ' >' + project.name + '</option>';
			});
			html = html + '</select></p>\
			<label class="zpm_label">' + zpm_localized.strings.date_uploaded + '</label>\
			<p>' + file_data.attachmentDate + '</p>\
			</div>';

			zpmNewModal( zpm_localized.strings.file_info, file_data.attachmentName, html, '', 'zpm_file_info_modal');
			$('body').find('#zpm-file-manager__last-file-id').val(fileId);
		}
		if (action == 'remove_file') {
			if (confirm( zpm_localized.strings.delete_file_notice )) {
				ZephyrProjects.remove_comment({
					comment_id: target.data('attachment-id'),
				}, function(){
					ZephyrProjects.notification( zpm_localized.strings.file_removed );
				});
				$(this).closest('.zpm_file_item_container').remove();
			}
		}
	});

	$('body').on('change', '#zpm-file-manager__project-change', function(){
		var value = $(this).val();
		var fileId = $('body').find('#zpm-file-manager__last-file-id').val();
		ZephyrProjects.updateFileProject(fileId, value);
		$('body').find('.zpm_file_item[data-attachment-id="' + fileId + '"]').closest('.zpm_file_item_container').attr('data-project-id', value).data('project-id', value);
		ZephyrProjects.remove_modal('zpm_file_info_modal');
		ZephyrProjects.close_modal('zpm_file_info_modal');
	});

	// Like a task
	$('body').on('click', '#zpm_like_task_btn', function(e) {
		$(this).toggleClass('zpm_liked');
		var task_id = $(this).data('task-id');
		var data = {
			task_id: task_id
		}

		ZephyrProjects.like_task(data);
	});

	// Follow a task
	$('body').on('click', '#zpm_follow_task', function(){
		var task_id = $(this).closest('#zpm_task_view_container').data('task-id');
		var data = {
			task_id: task_id
		}

		$('body').find('#zpm_follow_task').toggleClass('zpm_following').removeClass('lnr-plus-circle').addClass('lnr-redo').addClass('zpm_spin');

		ZephyrProjects.follow_task(data, function(response){
			$('body').find('#zpm_follow_task').removeClass('lnr-redo').removeClass('zpm_spin').addClass('lnr-plus-circle');
			if (response.following) {
				$('body').find('.zpm_task_follower[data-user-id="' + response.user_id + '"]').remove();
			} else {
				$('body').find('#zpm_task_following').append(response.html);
			}
		});
	});

	// Custom Dropdown
	$('body').find('#zpm_new_task_assignee').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_users_found,
	    width: "50%"
	});

	$('body').find('#zpm-edit-task__status').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found,
	    width: "100%"
	});

	$('body').find('#zpm-edit-project__status').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found,
	    width: "100%"
	});

	$('body').find('#zpm-new-task__status').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found,
	    width: "100%"
	});

	$('body').find('#zpm_file_upload_project').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_users_found,
	    width: "50%"
	});

	$('body').find('#zpm-new-task-team-selection').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_teams_found,
	    width: "50%"
	});

	$('body').find('#zpm-new-task-template-select').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_templates_found,
	    width: "50%"
	});

	$('body').find('#zpm_edit_task_assignee').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_users_found,
	    width: "50%"
	});

	$('body').find('#zpm_edit_task_assignee').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_users_found,
	    width: "100%"
	});

	$('body').find('#zpm-edit-task-team-selection').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_teams_found,
	    width: "100%"
	});

	$('body').find('#zpm_edit_task_project').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_projects_found,
	    width: "100%"
	});

	$('body').find('#zpm-new-project__categories').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found,
	    width: "100%"
	});

	$('body').find('#zpm-calendar__filter-team, #zpm-calendar__filter-assignee, #zpm-calendar__filter-project, #zpm-calendar__filter-completed').addClass('visible').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found
	});

	$('body').find('#zpm-edit-task__recurrence-select').chosen({
	    disable_search_threshold: 10,
	    no_results_text: zpm_localized.strings.no_results_found,
	    width: "100%"
	});

	$('body').find('#zpm-calendar__filter-completed').val('0').trigger('chosen:updated');

	// Edit task team changed
	$('body').on('change', '#zpm-edit-task-team-selection', function(){
		let teamId = $(this).val();

		updateEditTaskTeamMembers(teamId);
	});

	// var zpmTeamValue = $('#zpm-edit-task-team-selection').val();

	// if (typeof zpmTeamValue !== 'undefined' && zpmTeamValue !== -1) {
	// 	//updateEditTaskTeamMembers(zpmTeamValue);
	// 	ZephyrProjects.getTeam({
	// 		id: zpmTeamValue
	// 	}, function(res) {

	// 		if (res != null) {
	// 			$('#zpm_edit_task_assignee > option').hide();
	// 			$('#zpm_edit_task_assignee > option[value="-1"]').show();
	// 			$.each(res.members, function(key, val) {
	// 				let userId = val.id;
	// 				$('#zpm_edit_task_assignee > option[value="' + userId + '"]').show();
	//         		$('#zpm_edit_task_assignee').trigger("chosen:updated");
	// 			});
	// 		}
	// 	});
	// }

	function updateEditTaskTeamMembers(teamId) {
		//$('#zpm_edit_task_assignee > option').show();
        //$('#zpm_edit_task_assignee').trigger("chosen:updated");

		ZephyrProjects.getTeam({
			id: teamId
		}, function(res) {
			$('#zpm_edit_task_assignee').val('');
			$('#zpm_edit_task_assignee').trigger("chosen:updated");
			if (res != null) {
				var values = [];
				$.each(res.members, function(key, val) {
					let userId = val.id;
					values.push(userId);
				});
				$('#zpm_edit_task_assignee').val(values);
	        	$('#zpm_edit_task_assignee').trigger("chosen:updated");
			}
		});
	}

    // Task Subtasks
    $('body').on('click', '#zpm_task_add_subtask', function(){
    	var newSubTask = '<span class="zpm_task_subtask"><input type="text" class="zpm_task_subtask_item" placeholder="' + zpm_localized.strings.new_subtask + '" value=""/><i class="zpm_delete_subtask_icon dashicons dashicons-no-alt"></i></span>';
    	$('body').find('#zpm_task_subtasks').append(newSubTask);
    	var lastSubTask = $('body').find('.zpm_task_subtask:last-of-type');
    	var scrollParent = $('body').find('.zpm_modal_content');

	    scrollParent.animate({
	        scrollTop: lastSubTask.offset().top
	    }, 0);
	});

	$('body').on('click', '.zpm_delete_subtask_icon', function(){
    	$(this).closest('.zpm_task_subtask').remove();
	});

	// Task Options
	$('body').on('click', '.zpm_modal_options_btn', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$(this).find('.zpm_modal_dropdown').toggleClass('active');
	});

	$('body').on('click', '[zpm-toggle-dropdown]', function(){
		let id = $(this).attr('zpm-toggle-dropdown');
		$('#' + id).toggleClass('zpm-open');
	});

	$('body').on('click', '.zpm-dropdown-item', function(e){
		$(this).closest('.zpm-dropdown').removeClass('zpm-open');
		e.stopPropagation();
	});

	// New Task Priority
	$('body').on('click', '.zpm-new-task-priority', function(e){
		let priority = $(this).data('value');
		let color = $(this).data('color');

		if (typeof color !== "undefined") {
			var text_color = "#fff";
			if (priority == "priority_none") {
				text_color = "#333";
			}
			$('body').find('#zpm-new-task-priority').attr('style', 'background: ' + color + ' !important; color: ' + text_color + ' !important')
		}

		$('body').find('#zpm-new-task-priority').attr('data-priority', priority);
		$('body').find('#zpm-new-task-priority-value').val(priority);
		$('body').find('#zpm-new-task-priority .zpm-priority-name').text(zpm_localized.strings.priority + ": " + $(this).text());
		e.stopPropagation();
	});

	// Edit Task Priority
	$('body').on('click', '.zpm-edit-task-priority', function(e){
		let priority = $(this).data('value');
		let color = $(this).data('color');

		if (typeof color !== "undefined") {
			var text_color = "#fff";
			if (priority == "priority_none") {
				text_color = "#333";
			}
			$('body').find('#zpm-edit-task-priority').attr('style', 'background: ' + color + ' !important; color: ' + text_color + ' !important')
		}

		$('body').find('#zpm-edit-task-priority').attr('data-priority', priority);
		$('body').find('#zpm-edit-task-priority-value').val(priority);
		$('body').find('#zpm-edit-task-priority .zpm-priority-name').text(zpm_localized.strings.priority + ": " + $(this).text());
		e.stopPropagation();
	});

	// General Priority Dropdown
	$('body').on('click', '.zpm-priority-selection .zpm-dropdown-item', function(e){
		let priority = $(this).data('value');
		let color = $(this).data('color');

		if (typeof color !== "undefined") {
			var text_color = "#fff";
			if (priority == "priority_none") {
				text_color = "#333";
			}
			$('body').find('#zpm-edit-task-priority').attr('style', 'background: ' + color + ' !important; color: ' + text_color + ' !important')
		}

		if (typeof color !== "undefined") {
			$(this).closest('.zpm-priority-selection').attr('style', 'background: ' + color + ' !important; color: ' + text_color + ' !important;');
		}

		$(this).closest('.zpm-priority-selection').attr('data-priority', priority);
		$(this).closest('.zpm-priority-selection').prev().val(priority);
		$(this).closest('.zpm-priority-selection').find('.zpm-priority-name').text(zpm_localized.strings.priority + ": " + $(this).text());
		e.stopPropagation();
	});

	// Copy a task
	$('body').on('click', '#zpm_copy_task', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var buttons = '<button id="zpm_copy_task_btn" class="zpm_button">' + zpm_localized.strings.create_new_task + '</button>';

		var optionsList = [
			{
				name: zpm_localized.strings.task_description,
				default: 'checked',
				value: 'description'
			},{
				name: zpm_localized.strings.assignee,
				default: 'checked',
				value: 'assignee'
			},{
				name: zpm_localized.strings.subtasks,
				default: 'checked',
				value: 'subtasks'
			},{
				name: zpm_localized.strings.attachments,
				default: '',
				value: 'attachments'
			},{
				name: zpm_localized.strings.start_date,
				default: 'checked',
				value: 'start_date'
			},{
				name: zpm_localized.strings.due_date,
				default: 'checked',
				value: 'due_date',
			}
		];

		var options = '<ul class="zpm_copy_task_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = options +
				'<li>' +
					'<label for="zpm_copy_task_option_' + i + '" class="zpm_checkbox_label">' +
						'<input type="checkbox" id="zpm_copy_task_option_' + i + '" name="zpm_copy_task_option_' + i + '" class="zpm_copy_task_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
							'<div class="zpm_main_checkbox">'+
								'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
								'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
								'<polyline points="4 11 8 15 16 6"></polyline>' +
							'</svg>' +
						'</div>' +
				    '</label>' +
					optionsList[i].name +
				'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_copy_task_body"><h5 class="zpm_copy_project_title">' + zpm_localized.strings.include + ': </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal( zpm_localized.strings.copy_task, '<input id="zpm_copy_task_name" value="' + zpm_localized.strings.copy_of + ' ' + $.trim(taskName) + '" placeholder="' + zpm_localized.strings.task_name + '" />', content, buttons, 'zpm_copy_task_modal', taskId);
    	ZephyrProjects.open_modal('zpm_copy_task_modal');
	});

	$('body').on('click', '#zpm_copy_task_btn', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var newName = $('body').find('#zpm_copy_task_name').val();
		var copySettings = [];

		$('.zpm_copy_task_options .zpm_copy_task_option').each(function(){
			var checked = ($(this).is(':checked')) ? true : false;
			var optionValue = $(this).data('option-value');

			if (checked) {
				copySettings.push(optionValue);
			}
		});

		var data = {
			task_id: taskId,
			task_name: newName,
			copy_options: copySettings
		}
		ZephyrProjects.copy_task(data, function(response){
			$('body').find('.zpm_task_list').prepend(response.html);
			$('body').find('.zpm_message_center').remove();
		});

		ZephyrProjects.close_modal();
	});

	// Copy a project
	$('body').on('click', '#zpm_copy_project', function(){
		var projectId = $(this).closest('.zpm_project_item').data('project-id');
		var projectName = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		var buttons = '<button id="zpm_copy_project_btn" class="zpm_button">' + zpm_localized.strings.create_new_project + '</button>';

		var optionsList = [
			{
				name: zpm_localized.strings.description,
				default: 'checked',
				value: 'description'
			},{
				name: zpm_localized.strings.tasks,
				default: 'checked',
				value: 'tasks'
			},{
				name: zpm_localized.strings.attachments,
				default: 'checked',
				value: 'attachments'
			},{
				name: zpm_localized.strings.start_date,
				default: 'checked',
				value: 'start_date'
			},{
				name: zpm_localized.strings.due_date,
				default: 'checked',
				value: 'due_date',
			}
		];

		var options = '<ul class="zpm_copy_project_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = 	options +
						'<li>' +
							'<label for="zpm_copy_project_option_' + i + '" class="zpm_checkbox_label">' +
								'<input type="checkbox" id="zpm_copy_project_option_' + i + '" name="zpm_copy_project_option_' + i + '" class="zpm_copy_project_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
									'<div class="zpm_main_checkbox">'+
										'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
										'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
										'<polyline points="4 11 8 15 16 6"></polyline>' +
									'</svg>' +
								'</div>' +
						    '</label>' +
							optionsList[i].name +
						'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_copy_project_body"><h5>' + zpm_localized.strings.include + ': </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal( zpm_localized.strings.copy_project, '<input id="zpm_copy_project_name" value="' + zpm_localized.strings.copy_of + ' ' + $.trim(projectName) + '" placeholder="' + zpm_localized.strings.project_name + '" />', content, buttons, 'zpm_copy_project_modal', projectId, '', projectId);
    	ZephyrProjects.open_modal('zpm_copy_project_modal');
	});

	$('body').on('click', '#zpm_copy_project_btn', function(){
		var project_id = $(this).closest('.zpm-modal').data('project-id');
		var new_name = $('body').find('#zpm_copy_project_name').val();
		var copy_options = [];

		$('.zpm_copy_project_options .zpm_copy_project_option').each(function(){
			var checked = ($(this).is(':checked')) ? true : false;
			var option_value = $(this).data('option-value');
			if (checked) {
				copy_options.push(option_value);
			}
		});

		var data = {
			project_id: project_id,
			project_name: new_name,
			copy_options: copy_options
		}

		ZephyrProjects.copy_project(data, function(response){
			$('body').find('.zpm_project_grid').prepend(response.html);
		});
		ZephyrProjects.close_modal();
	});


	// Convert task to Project
	$('body').on('click', '#zpm_convert_task', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var buttons = '<button id="zpm_convert_task_btn" class="zpm_button">' + zpm_localized.strings.convert_task + '</button>';

		var optionsList = [
			{
				name: zpm_localized.strings.task_description_as_description,
				default: 'checked',
				value: 'description'
			},{
				name: zpm_localized.strings.subtasks_as_tasks,
				default: 'checked',
				value: 'subtasks'
			},{
				name: zpm_localized.strings.assignee_as_creator,
				default: 'checked',
				value: 'assignee'
			}
		];

		var options = '<ul class="zpm_convert_task_options">';
		for (var i = 0; i < optionsList.length; i++) {
			options = 	options +
						'<li>' +
							'<label for="zpm_convert_task_option_' + i + '" class="zpm_checkbox_label">' +
								'<input type="checkbox" id="zpm_convert_task_option_' + i + '" name="zpm_convert_task_option_' + i + '" class="zpm_convert_task_option zpm_toggle invisible" value="1" ' + optionsList[i].default + ' data-option-value="' + optionsList[i].value + '">' +
									'<div class="zpm_main_checkbox">'+
										'<svg width="20px" height="20px" viewBox="0 0 20 20">' +
										'<path d="M3,1 L17,1 L17,1 C18.1045695,1 19,1.8954305 19,3 L19,17 L19,17 C19,18.1045695 18.1045695,19 17,19 L3,19 L3,19 C1.8954305,19 1,18.1045695 1,17 L1,3 L1,3 C1,1.8954305 1.8954305,1 3,1 Z"></path>' +
										'<polyline points="4 11 8 15 16 6"></polyline>' +
									'</svg>' +
								'</div>' +
						    '</label>' +
							optionsList[i].name +
						'</li>';
		}
		var options = options + '</ul>';
		var content = '<p id="zpm_convert_task_body"><h5>' + zpm_localized.strings.include + ': </h5>' + options + '</p>';

		ZephyrProjects.close_modal();
		zpmNewModal( zpm_localized.strings.convert_to_project, '<input id="zpm_convert_task_name" value="' + zpm_localized.strings.project + ': ' + $.trim(taskName) + '" placeholder="' + zpm_localized.strings.project_name + '" />', content, buttons, 'zpm_convert_task_modal', taskId);
    	ZephyrProjects.open_modal('zpm_convert_task_modal');
	});

	$('body').on('click', '#zpm_convert_task_btn', function(){
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var newName = $('body').find('#zpm_convert_task_name').val();

		var convertSettings = [];
		$('.zpm_convert_task_options .zpm_convert_task_option').each(function(){
			var checked = ($(this).is(':checked')) ? true : false;
			var optionValue = $(this).data('option-value');
			if (checked) {
				convertSettings.push(optionValue);
			}
		});

		var data = {
			task_id: taskId,
			project_name: newName,
			convert_options: convertSettings
		}

		ZephyrProjects.task_to_project(data, function(response){

		});

		ZephyrProjects.close_modal();
	});


	// Export a task to JSON
	$('body').on('click', '#zpm_export_task_to_json', function() {
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();

		var data = {
			task_id: taskId,
			export_to: 'json'
		}

		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});
	});

	// Export a task to CSV
	$('body').on('click', '#zpm_export_task_to_csv', function() {
		var taskId = $(this).closest('.zpm-modal').data('task-id');
		var baseModal = $(this).closest('.zpm-modal');
		var taskName = baseModal.find('.zpm_modal_task_name').html();
		var data = {
			task_id: taskId,
			export_to: 'csv'
		}

		ZephyrProjects.export_task(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});

	});

	// Export all tasks to JSON
	$('body').on('click', '#zpm_export_all_tasks_to_json', function() {

		var data = {
			export_to: 'json'
		}

		ZephyrProjects.export_tasks(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'All Tasks.json';
			link.href = response;
			link.click();
		});
	});

	// Export all tasks to CSV
	$('body').on('click', '#zpm_export_all_tasks_to_csv', function() {

		var data = {
			export_to: 'csv'
		}

		ZephyrProjects.export_tasks(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'All Tasks.csv';
			link.href = response;
			link.click();
		});
	});

	// Print a task
	$('body').on('click', '#zpm_print_task', function(){
		setTimeout(function(){
			var printContents = $('body').find('#zpm_task_view_container').html();
			var originalContents = document.body.innerHTML;
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
		}, 500);
	});

	$('body').on('click', '.zpm_custom_dropdown', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$(this).toggleClass('active');
    	$(this).find('#' + dropdown).toggleClass('active');
	});

	// Filter Tasks
	$('body').on('click', '#zpm_filter_tasks .zpm_selection_option, #zpm-tasks-filter-nav .zpm_selection_option', function() {
		var filter = $(this).data('zpm-filter');
		var option = $(this).html();
		var user_id = '-1';
		if (filter == '0') {
			user_id = $(this).data('user-id');
		}
		if (typeof filter == 'undefined') {
			return;
		}

		$(this).closest('.zpm_custom_dropdown').find('.zpm_selected_option').html(option);
		zpm_loader_modal( zpm_localized.strings.loading_tasks );

		var data = {
			zpm_filter: filter,
			zpm_user_id: user_id
		}

		ZephyrProjects.filter_tasks(data, function(response){
			$('body').find('.zpm_task_list').html(response.html);
			zpm_close_loader_modal();
		});
	});

	// Filter Projects
	$('body').on('click', '#zpm_filter_projects .zpm_selection_option', function() {
		var filter = $(this).data('zpm-filter');
		var option = $(this).html();
		var user_id = zpm_localized.user_id;
		var data = {
			zpm_filter: filter,
			zpm_user_id: user_id
		}

		if (typeof filter == "undefined") {
			// Filter projects by category
			filter = $(this).data('zpm-category-filter');
			data.filter_category = filter;
			option = zpm_localized.strings.category + ": " + option;
		} else {

		}

		$('#zpm-project-filter-title').html(option);

		ZephyrProjects.filter_projects( data, function(response){
			$('body').find('.zpm_project_grid').html(response.html);
			//zpm_close_loader_modal();
			$('.zpm_project_progress_bar').each( function() {
				var total_tasks = $(this).data('total_tasks');
				var completed_tasks = $(this).data('completed_tasks')
				var width = (total_tasks !== 0) ? ((completed_tasks / total_tasks) * 100) : 0;
				$(this).css('width', width + '%');
			});
		});
	});

	$('body').on('click', '#zpm_filter_tasks_icon', function(){
    	var dropdown = $(this).data('dropdown-id');
    	$('#zpm_filter_tasks').toggleClass('active');
    	$('body').find('#' + dropdown).toggleClass('active');
	});

	// Import tasks via CSV or JSON
	function zpm_import_tasks(attachment) {
		if (attachment.mime == 'text/csv') {
			ZephyrProjects.close_modal();
			zpmNewModal( zpm_localized.strings.import_tasks, zpm_localized.strings.importing_via_csv, '<div id="zpm_csv_task_import_data"></div>', '<button data-zpm-trigger="close_modal" class="zpm_button zpm_button_borderless" id="zpm_import_csv_data_btn">' + zpm_localized.strings.close + '</button>', 'zpm_import_csv_data_modal');
	    	ZephyrProjects.open_modal('zpm_import_csv_data_modal');

			var data = {
				zpm_file: attachment.url,
				zpm_import_via: 'csv'
			}

			ZephyrProjects.upload_tasks(data, function(response){
				var length = (response.tasks.length - 1);
				var output = '<h5>' + zpm_localized.strings.importing + ' ' + length + ' ' + zpm_localized.strings.tasks + ':</h5><ul id="zpm_csv_task_list">';
				for (var i = 1; i < response.tasks.length; i++) {
					var uploaded = (response.tasks[i].already_uploaded) ? 'zpm_task_exists' : '';
					var task_exists = (response.tasks[i].already_uploaded) ? zpm_localized.strings.task_exists + ': ' : '';
					output = output + '<li  class="zpm_imported_task ' + uploaded + '">' + task_exists + response.tasks[i].name + ' ' + response.tasks[i].description + ' (' + response.tasks[i].project + ')</li>';
				}
				output = output + '</ul>';
				$('body').find('#zpm_csv_task_import_data').html(output);
				$('.zpm_task_list').prepend(response.html);
			});

		} else if (attachment.mime == 'application/json') {
			ZephyrProjects.close_modal();
			zpmNewModal( zpm_localized.strings.import_tasks, zpm_localized.strings.importing_via_json, '<div id="zpm_json_task_import_data"></div>', '<button data-zpm-trigger="close_modal" class="zpm_button zpm_button_borderless" id="zpm_import_json_data_btn">' + zpm_localized.strings.close + '</button>', 'zpm_import_json_data_modal');
	    	ZephyrProjects.open_modal('zpm_import_json_data_modal');

	    	var data = {
				zpm_file: attachment.url,
				zpm_import_via: 'json'
			}

			ZephyrProjects.upload_tasks(data, function(response){
				var length = (response.tasks.length - 1);
				var output = '<h5>' + zpm_localized.strings.importing + ' ' + length + ' ' + zpm_localized.strings.tasks + ':</h5><ul id="zpm_json_task_list">';
				for (var i = 0; i < response.tasks.length; i++) {
					var uploaded = (response.tasks[i].already_uploaded) ? 'zpm_task_exists' : '';
					var task_exists = (response.tasks[i].already_uploaded) ? zpm_localized.strings.task_exists + ': ' : '';
					output = output + '<li class="zpm_imported_task ' + uploaded + '">' + task_exists + response.tasks[i].name + ' ' + response.tasks[i].description + ' (' + response.tasks[i].project + ')</li>';
				}
				output = output + '</ul>';
				$('body').find('#zpm_json_task_import_data').html(output);
				$('.zpm_task_list').prepend(response.html);
			});
		} else {
			alert( zpm_localized.strings.incorrect_import );
		}
	}

	// Hide admin sidebar
	$('body').on('click', '#zpm_hide_wp_adminbar', function(){
		$(document).find('body.wp-admin').toggleClass('folded');
		$(this).toggleClass('folded')
	});

	// Save project settings
	$('body').on('click', '#zpm_project_save_settings', function(){
		var form = $(this).closest('.zpm-form')
		var id = $(this).closest('#zpm_project_editor').data('project-id');
		var name = $('body').find('#zpm_edit_project_name').val();
		var description = $('body').find('#zpm_edit_project_description').val();
		var due_date = $('body').find('#zpm_edit_project_due_date').val();
		var start_date = $('body').find('#zpm_edit_project_start_date').val();
		var priority = $('body').find('#zpm-edit-project-priority-value').val();
		var status = $('body').find('#zpm-edit-project__status').val();
		var categories = $('body').find('#zpm-edit-project__categories').val();
		var custom_fields = [];

		$(this).html('Saving...');

		$('body').find('#zpm_task_edit_custom_fields .zpm_task_custom_field').each(function(){
			var id = $(this).data('zpm-cf-id');
			var type = $(this).data('zpm-cf-type');
			var value = $(this).val();

			if (type == "checkbox") {
				var value = $(this).is(':checked');
			}

			custom_fields.push({
				id: id,
				value: value
			});
		});

		$('#zpm_edit_project_description').mentionsInput('val', function(text) {
			var description = $.trim(text);
			var data = {
				project_id: id,
				project_name: name,
				project_description: description,
				project_due_date: due_date,
				project_start_date: start_date,
				project_categories: categories,
				priority: priority,
				custom_fields: custom_fields,
				status: status
			}

			form.find('[data-ajax-name]').each(function(){
				var name = jQuery(this).data('ajax-name');
				var value = jQuery(this).val();
				data[name] = value;
			});

			var assigneeSelect = $('body').find('#zpm-edit-project__assignee');
			if (assigneeSelect.length > 0) {
				data.assignees = assigneeSelect.val();
			}

			ZPM_Manager.update_project( data, function( response ){
				var background = jQuery('#zpm-edit-project-priority').css('background-color');
				var text_color = "#fff";

				if (priority == "" || priority == "priority_none") {
					$('#zpm-project-edit-priority-label').addClass('zpm-label-hidden');
					text_color = "#333";
				} else {
					$('#zpm-project-edit-priority-label').removeClass('zpm-label-hidden');
				}

				$('#zpm-project-edit-priority-label').removeClass('priority_high').removeClass('priority_low').removeClass('priority_medium').removeClass('priority_critical').addClass(priority).text($('.zpm-edit-project-priority[data-value="' + priority + '"]').text());
				$('#zpm-project-edit-priority-label').attr('style', 'background: ' + background + ' !important; color: ' + text_color + ' !important;');

				$('#zpm_project_name_title').html( name );
				$('#zpm_project_save_settings').html( zpm_localized.strings.save_changes );
			});
		});
		// ZephyrProjects.update_project(data, function(response){
		// 	var background = jQuery('#zpm-edit-project-priority').css('background-color');
		// 	var text_color = "#fff";

		// 	if (priority == "" || priority == "priority_none") {
		// 		$('#zpm-project-edit-priority-label').addClass('zpm-label-hidden');
		// 		text_color = "#333";
		// 	} else {
		// 		$('#zpm-project-edit-priority-label').removeClass('zpm-label-hidden');
		// 	}

		// 	$('#zpm-project-edit-priority-label').removeClass('priority_high').removeClass('priority_low').removeClass('priority_medium').removeClass('priority_critical').addClass(priority).text($('.zpm-edit-project-priority[data-value="' + priority + '"]').text());
		// 	$('#zpm-project-edit-priority-label').attr('style', 'background: ' + background + ' !important; color: ' + text_color + ' !important;');

		// 	$('#zpm_project_name_title').html( name );
		// 	$('#zpm_project_save_settings').html( zpm_localized.strings.save_changes );


		// });
	});

	// Delete project
	$('body').on('click', '#zpm_delete_project', function(){
		if (confirm( zpm_localized.strings.delete_project_notice )) {
			$(this).closest('.zpm_project_grid_cell').remove();
			var project_id = $(this).closest('.zpm_project_item').data('project-id');
			var project_name = $(this).closest('.zpm_project_title').find('.zpm_project_grid_name').text();

			ZephyrProjects.delete_project({
				project_id: project_id,
				project_name: project_name
			}, function(response){
				if (response.project_count == 0) {
					$('body').find('#zpm_project_manager_display').addClass('zpm_hide');
					$('body').find('#zpm_projects_holder').append('<div class="zpm_no_results_message">' + zpm_localized.strings.no_projects_created + '</div>');
				}
			});
		}
	});

	// Archive project
	$('body').on('click', '#zpm-project-action__archive', function(){
		$(this).closest('.zpm_project_grid_cell').remove();
		var item = $(this).closest('.zpm_project_item');
		var project_id = item.data('project-id');
		var archived = $(this).data('archived');

		ZephyrProjects.archiveProject({
			project_id: project_id,
			archived: archived
		}, function(response){
		});
	});

	// Like a project
	$('body').on('click', '#zpm_like_project_btn', function(e) {
		$(this).toggleClass('zpm_liked');
		var project_id = $(this).data('project-id');
		var data = {
			project_id: project_id
		}

		ZephyrProjects.like_project(data, function(response){

		});
	});

	// Export all projects
	$('body').on('click', '.zpm-export-projects__btn', function() {

		ZephyrProjects.ajax({
			action: 'zpm_exportProjectsToCSV'
		}, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'ZPM Projects.csv';
			link.href = response;
			link.click();
		});
	});

	// Export all tasks
	$('body').on('click', '.zpm-export-tasks__btn', function() {

		ZephyrProjects.ajax({
			action: 'zpm_exportTasksToCSV'
		}, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'ZPM Tasks.csv';
			link.href = response;
			link.click();
		});
	});

	// Export Project to CSV
	$('body').on('click', '#zpm_export_project_to_csv', function() {
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var project_name = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		project_name = jQuery.trim(project_name);

		var data = {
			project_id: project_id,
			project_name: project_name,
			export_to: 'csv'
		};

		ZephyrProjects.export_project(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = 'Project - ' + $.trim(project_name) + '.csv';
			link.href = response.project_csv;
			link.click();

			var link_tasks = document.createElement('a');
			document.body.appendChild(link_tasks);
			link_tasks.download = $.trim(project_name) + ' - Tasks.csv';
			link_tasks.href = response.project_tasks_csv;
			link_tasks.click();
		});
	});

	// Export Project to JSON
	$('body').on('click', '#zpm_export_project_to_json', function() {
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var project_name = $(this).closest('.zpm_project_item').find('.zpm_project_grid_name').html();
		var data = {
			project_id: project_id,
			project_name: project_name,
			export_to: 'json'
		}

		ZephyrProjects.export_project(data, function(response){
			var link = document.createElement('a');
			document.body.appendChild(link);
			link.download = response.file_name;
			link.href = response.file_url;
			link.click();
		});

	});

	// Print a Project
	$('body').on('click', '#zpm_print_project', function(){
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var data = {
			project_id: project_id
		}

		ZephyrProjects.print_project(data, function(response){

		});
	});

	// Custom Fancy Modal
	$('body').on('click', '[data-zpm-dropdown-toggle]', function(){
		var target = $(this).data('zpm-dropdown-toggle');
		$('body').find('#' + target).toggleClass('active');
	});

	/* Comments and Conversations */
	// Send task comment
	$('body').on('click', '#zpm_task_chat_comment', function() {
		var task_id = $(this).data('task-id');
		$('#zpm_chat_message').mentionsInput('val', function(text) {
			var message = $.trim(text);
			message = message.replace(/\<div><br><\/div>/g,'');
			send_message('task', task_id, message);
			$('#zpm_chat_message').mentionsInput('reset');
		});
	});

	// Send project comment
	$('body').on('click', '#zpm_project_chat_comment', function() {
		var project_id = $(this).data('project-id');
		$('#zpm_chat_message').mentionsInput('val', function(text) {
			var message = $.trim(text);
			message = message.replace(/\<div><br><\/div>/g,'');
			send_message('project', project_id, message);
			$('#zpm_chat_message').mentionsInput('reset');
		});
		// var message = $.trim($('body').find('#zpm_chat_message')[0].innerText);
		// message = message.replace(/\<div><br><\/div>/g,'');

	});

	function send_message( subject, subject_id, message ) {
		var attachments = [];
		$('body').find('.zpm_comment_attachment').each(function(){
			var attachment_id = $(this).data('attachment-id');
			attachments.push({
				attachment_id: attachment_id
			});
		});

		// if ($(this).text() == zpm_localized.strings.comment) {
		// 	$(this).html(zpm_localized.strings.sending);
		// }

		//$(this).addClass('zpm_message_sending');

		var data = {
			user_id: zpm_localized.user_id,
			subject: subject,
			subject_id: subject_id,
			message: message,
			type: 'message',
			attachments: attachments
		};

		ZephyrProjects.send_comment(data, function(response) {

			response.id = subject_id;
			response.user_id = zpm_localized.user_id;
			response.subject = subject;
			response.type = data.type;
			jQuery.event.trigger( { type: 'zephyr_new_message', ndata: response } );

			if (typeof response.files_html !== 'undefined') {
				jQuery('.zpm-files__container').html(response.files_html);
			}

			if ($('#zpm_task_chat_comment').text() == zpm_localized.strings.sending) {
				$('#zpm_task_chat_comment').html( zpm_localized.strings.comment );
			}
			$('#zpm_task_chat_comment').removeClass('zpm_message_sending');
			$('body').find('.zpm_task_comments').prepend(response.html);
			var newComment = $('body').find('.zpm_comment_content').first();
			newComment.html(ZephyrProjects.linkify(newComment.html()));
			$('body').find('#zpm_chat_message').html('');
			$('#zpm_chat_attachments').html('');
		});
	}

	/* Upload Task File */
	var zpm_file_uploader;

	$('body').on('click', '#zpm_task_chat_files, #zpm_project_chat_files', function() {

		ZephyrProjects.upload_file(zpm_file_uploader, function(res){
			var attachments = res;
           	for (var i = 0; i < attachments.length; ++i) {
           		var attachment_holder = $('body').find('#zpm_chat_attachments');
				attachment_holder.append('<span data-attachment-id="' + attachments[i].id + '" class="zpm_comment_attachment">' + attachments[i].attributes.url + '<span class="zpm_remove_attachment lnr lnr-cross"></span></span>');
            }
		}, true);
	});

	$('body').on('click', '#zpm-task-single__add-files-btn', function() {
		ZephyrProjects.upload_file(zpm_file_uploader, function(res){
			ZephyrProjects.notification( zpm_localized.strings.uploading_files );
			var attachments = res;
           	for (var i = 0; i < attachments.length; ++i) {
           		var attachment_holder = $('body').find('#zpm_chat_attachments');
				attachment_holder.append('<span data-attachment-id="' + attachments[i].id + '" class="zpm_comment_attachment">' + attachments[i].attributes.url + '<span class="zpm_remove_attachment lnr lnr-cross"></span></span>');
            }
            $('#zpm_task_chat_comment').click();

			$("html, body").animate({
			    scrollTop: $('#zpm_edit_task_comments').offset().top + 150
			}, 500);
		}, true);
	});

	/* Upload a general file from the Files page */
	var file_uploader;
	$('#zpm_upload_file_btn').on('click', function(){
		if (file_uploader) {
			file_uploader.open();
			return;
		}

		file_uploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.files,
			button: {
			text: zpm_localized.strings.upload_file
		}, multiple: false });

		file_uploader.on('select', function() {
			var project_id = $('#zpm-new-file-project-value').val();
			var attachment = file_uploader.state().get('selection').first().toJSON();
			var attachment_holder = $('body').find('#zpm_chat_attachments');
			upload_attachment(attachment.id, 'project', project_id);
			$(this).closest('.zpm_modal_footer').find('#zpm_submit_file').removeClass('inactive');
		});

		// Open the uploader dialog
		file_uploader.open();
	});

	/* Remove a selected attachment */
	$('body').on('click', '.zpm_remove_attachment', function(){
		$(this).closest('.zpm_comment_attachment').remove();
	});

	/* Delete a comment */
	$('body').on('click', '.zpm_delete_comment', function(){
		var comment = $(this).closest('.zpm_comment');
		var comment_id = comment.data('zpm-comment-id');
		comment.remove();

		var data = {
			comment_id: comment_id
		}

		ZephyrProjects.remove_comment(data, function(){
			ZephyrProjects.notification( zpm_localized.strings.message_removed );
		});
	});

	$('body').on('click', '.zpm-edit-message', function(){
		var comment = $(this).closest('.zpm_comment');
		var comment_id = comment.data('zpm-comment-id');
		var message = comment.find('.zpm_comment_content').text();
		var html = '<input type="hidden" data-ajax-name="message-id" value="' + comment_id + '" />\
		<textarea type="text" data-ajax-name="message">' + message + '</textarea>';
		ZephyrProjects.zephyrModal(zpm_localized.strings.edit_message, html, zpm_localized.strings.save_changes, function(modal){
			var id = modal.find('[data-ajax-name="message-id"]').val();
			var message = modal.find('[data-ajax-name="message"]').val();

			comment.find('.zpm_comment_content').text(message);
			ZephyrProjects.ajax({
				action: 'zpm_updateMessage',
				message_id: id,
				message: message
			}, function(response){
			});
		}, 'zpm-edit-message__modal');
	});

	function upload_attachment( attachment_id, attachment_type, subject_id ) {
		ZephyrProjects.notification( zpm_localized.strings.uploading_file );
		var attachment_type = (typeof attachment_type !== 'undefined') ? attachment_type : '';
		var subject_id = (typeof subject_id !== 'undefined') ? subject_id : '';
		var attachments = [{
			attachment_id: attachment_id,
			attachment_type: attachment_type,
			subject_id: subject_id
		}];
		var data = {
			attachments: attachments
		}

		ZephyrProjects.send_comment(data, function(response){
			$('body').find('.zpm_files_container').prepend(response.html);
			$('#zpm_no_files').hide();
			ZephyrProjects.notification( zpm_localized.strings.file_uploaded );
		});
	}

	/* Open the new subtask modal */
	$('body').on('click', '#zpm_add_new_subtask', function() {
		//ZephyrProjects.open_modal('zpm_new_subtask_modal');
		jQuery.event.trigger( { type: 'zephyr_new_task_modal_opened', data: {} } );
		ZephyrProjects.open_modal('zpm_create_task');
		var modal = jQuery('body').find('#zpm_create_task');
		modal.find('[data-ajax-name="parent-id"]').val(jQuery('body').find('#zpm-task-id').val());
		$('body').find('#zpm-new-task-template-select').trigger('change');
		$('body').find('select#zpm_new_task_project').chosen({
		    disable_search_threshold: 10,
		    no_results_text: zpm_localized.strings.no_projects_found,
		    width: "100%"
		});
	});

	$('body').on('click', '#zpm_save_new_subtask', function() {
		var task_id = $('body').find('#zpm_js_task_id').val();
		var subtask_name = $('body').find('#zpm_new_subtask_name').val();
		var description = $('body').find('#zpm-new-subtask__description').val();
		var start = $('body').find('#zpm-new-subtask__start').val();
		var due = $('body').find('#zpm-new-subtask__due').val();

		var data = {
			task_id: task_id,
			subtask_action: 'new_subtask',
			subtask_name: subtask_name,
			description: description,
			start: start,
			due: due
		}

		$('body').find('#zpm_new_subtask_name').val('');
		ZephyrProjects.close_modal();
		ZephyrProjects.notification( zpm_localized.strings.creating_subtask );

		ZephyrProjects.update_subtasks(data, function(response){
			ZephyrProjects.notification( zpm_localized.strings.subtask_saved );
			var subtask_list = $('body').find('#zpm_subtask_list');
			subtask_list.append(response.html);
			jQuery('#zpm-no-subtasks').hide();
		});
	});

	/* Delete a subtask from the database */
	$('body').on('click', '.zpm_delete_subtask', function() {
		$(this).closest('.zpm_subtask_item').remove();
		var task_id = $('body').find('#zpm_js_task_id').val();
		var subtask_id = $(this).data('zpm-subtask-id');
		var data = {
			subtask_action: 'delete_subtask',
			subtask_id: subtask_id
		}

		ZephyrProjects.update_subtasks(data, function(response){
			ZephyrProjects.notification( zpm_localized.strings.subtask_deleted );
		});
		if ($('body').find('.zpm_subtask_item').length <= 0) {
			jQuery('#zpm-no-subtasks').show();
		}
	});

	/* Edit a subtask */
	$('body').on('click', '.zpm_subtask_item', function(e) {
		// var taskId = $(this).data('zpm-subtask');
		// var target = $(e.target);
		// if (target.hasClass('zpm_subtask_is_done') || target.hasClass('zpm_delete_subtask') || target.hasClass('zpm-material-checkbox-label') || target.closest('.zpm-modal').length > 0) {

		// } else {
		// 	ZephyrProjects.zephyrAjaxModal({
		// 		action: 'zpm_subtaskEditModal',
		// 		id: taskId
		// 	}, function(response){
		// 		var data = {};
		// 		var modal = response.modal
		// 		data.new_subtask_name = modal.find('[data-ajax-name="name"]').val();
		// 		data.description = modal.find('[data-ajax-name="description"]').val();
		// 		data.start_date = modal.find('[data-ajax-name="start-date"]').val();
		// 		data.due_date = modal.find('[data-ajax-name="due-date"]').val();
		// 		data.subtask_action = 'update_subtask';
		// 		data.subtask_id = taskId;
		// 		data.task_id = modal.find('[data-ajax-name="parent-id"]').val();

		// 		ZephyrProjects.update_subtasks(data, function(response){
		// 			ZephyrProjects.notification( zpm_localized.strings.changes_saved );
		// 			$('body').find('[data-zpm-subtask="' + taskId + '"]').replaceWith(response.html);
		// 		});
		// 	});
		// }
	});

	/* Update subtask name in database */
	// $('body').on('click', '.zpm_update_subtask', function() {
	// 	var task_id = $('body').find('#zpm_js_task_id').val();
	// 	var subtask_id = $(this).data('zpm-subtask-id');
	// 	var subtask_parent = $(this).closest('.zpm_subtask_item');
	// 	var subtask = $(this).closest('.zpm_subtask_item').data('zpm-subtask');
	// 	var new_subtask = $(this).closest('.zpm_subtask_item').find('.zpm_subtask_name').html();
	// 	var data = {
	// 		task_id: task_id,
	// 		subtask_id: subtask_id,
	// 		subtask_action: 'update_subtask',
	// 		new_subtask_name: new_subtask
	// 	}
	// 	$(this).closest('.zpm_subtask_item').find('.zpm_subtask_name').removeAttr('contentEditable');
	// 	$(this).removeClass('is_active');

	// 	ZephyrProjects.update_subtasks(data, function(response){
	// 		ZephyrProjects.notification( zpm_localized.strings.changes_saved );
	// 	});
	// });

	/* Mark if subtask is done */
	$('body').on('click', '.zpm_subtask_is_done', function() {
		var task_id = $(this).data('task-id');

		if ($(this).is(':checked')) {
			var data = {
				id: task_id,
				completed: 1
			}

			ZephyrProjects.complete_task(data, function(response){});
			$(this).closest('.zpm_subtask_item').addClass('zpm_task_complete');
		} else {
			var data = {
				id: task_id,
				completed: 0
			}

			ZephyrProjects.complete_task(data, function(response){});
			$(this).closest('.zpm_subtask_item').removeClass('zpm_task_complete');
		}
	});

	/* Open the new status modal */
	$('body').on('click', '#zpm_new_status_btn', function() {
		$('#zpm-status-type__new').val( 'status' );
		ZephyrProjects.open_modal('zpm_new_status_modal');
	});

	$('body').on('click', '#zpm_new_priority_btn', function() {
		$('#zpm-status-type__new').val( 'priority' );
		ZephyrProjects.open_modal('zpm_new_status_modal');
	});

	$('body').on('click', '.zpm-status-list__item', function() {
		ZephyrProjects.open_modal('zpm_edit_status_modal');
		var name = $(this).find('.zpm-status-list__item-name').text();
		var color = $(this).find('.zpm-status-list__item-color').css('background-color');
		$('#zpm-edit-status-id').val($(this).data('status-slug'));
		$('#zpm_edit_status_name').val(name);
		$('#zpm_edit_status_color').val(color).trigger('change');
		$('#zpm-status-type__edit').val( 'status' );
	});

	$('body').on('click', '.zpm-priority-list__item', function() {
		ZephyrProjects.open_modal('zpm_edit_status_modal');
		var name = $(this).find('.zpm-priority-list__item-name').text();
		var color = $(this).find('.zpm-priority-list__item-color').css('background-color');
		$('#zpm-edit-status-id').val($(this).data('priority-slug'));
		$('#zpm_edit_status_name').val(name);
		$('#zpm_edit_status_color').val(color).trigger('change');
		$('#zpm-status-type__edit').val( 'priority' );
	});

	/* Open the new category modal */
	$('body').on('click', '#zpm_new_category_btn, #zpm_new_quick_category', function() {
		ZephyrProjects.open_modal('zpm_new_category_modal');
	});

	/* Create a new category */
	$('body').on('click', '#zpm_create_category', function(e){

		var name = $(this).closest('#zpm_new_category_modal').find('#zpm_category_name').val();
		var description = $(this).closest('#zpm_new_category_modal').find('#zpm_category_description').val();
		var color = $(this).closest('#zpm_new_category_modal').find('#zpm_category_color').val();
		if (name == '') { return; }

		ZephyrProjects.notification( zpm_localized.strings.creating_category );
		ZephyrProjects.close_modal();
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_name').val('');
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_description').val('');
		$(this).closest('#zpm_new_category_modal').find('#zpm_category_color').val('');
		var data = {
			category_name: name,
			category_description: description,
			category_color: color,
		}

		ZephyrProjects.create_category(data, function(response){
			$('.zpm_category_list').html(response);
		});
	});

	/* Delete a category */
	$('body').on('click', '.zpm_delete_category', function(){
		var category_id = $(this).data('category-id');

		if (confirm( zpm_localized.strings.delete_category_notice )) {
			ZephyrProjects.notification( zpm_localized.strings.deleting_category );
			ZephyrProjects.remove_category({
				id: category_id
			}, function(response){
				$('.zpm_category_list').html(response);
			});
		} else {

		}
	});

	/* Edit a category */
	$('body').on('click', '.zpm_category_row', function(e){
		if (e.target.className == 'zpm_delete_category' || e.target.className == 'zpm_delete_category_icon lnr lnr-cross') { return; }
		ZephyrProjects.open_modal('zpm_edit_category_modal');
		var category_id = $(this).data('category-id');
		var category_color = $(this).find('.zpm_category_color').data('zpm-color');
		var category_name = $(this).find('.zpm_category_name').html();
		var category_description = $(this).find('.zpm_category_description').html();

		$('body').find('#zpm-edit-category-id').val(category_id);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_name').val(category_name);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_description').val(category_description);
		$('body').find('#zpm_edit_category_modal #zpm_edit_category_color').val(category_color);
		$('#zpm_edit_category_color').wpColorPicker();
	});

	/* Update category */
	$('body').on('click', '#zpm_edit_category', function(e){
		var base = $(this).closest('#zpm_edit_category_modal');
		var category_id = $('#zpm-edit-category-id').val();
		var name = base.find('#zpm_edit_category_name').val();
		var description = base.find('#zpm_edit_category_description').val();
		var color = base.find('#zpm_edit_category_color').val();
		var data = {
			category_id: category_id,
			category_name: name,
			category_description: description,
			category_color: color,
		}

		if (name == '') {
			return;
		}

		ZephyrProjects.notification( zpm_localized.strings.saving_changes );
		ZephyrProjects.close_modal();
		ZephyrProjects.update_category(data, function(response){
			$('.zpm_category_list').html(response);
		});
	});

	/* Text Editor */
	$('.zpm_editor_toolbar a').click(function(e) {
		e.preventDefault();
		var command = $(this).data('command');

		if (command == 'h1' || command == 'h2' || command == 'p') {
			document.execCommand('formatBlock', false, command);
		}

		if (command == 'forecolor' || command == 'backcolor') {
			document.execCommand($(this).data('command'), false, $(this).data('value'));
		}

		if (command == 'createlink' || command == 'insertimage') {
			url = prompt('Enter the link here: ','http:\/\/');
			document.execCommand($(this).data('command'), false, url);
		}

		if (command == 'addCode') {
			document.execCommand("insertHTML", false, "<code class='cca_code_snippet' style='display: block;'>" + document.getSelection() + "</code>");
		} else {
			document.execCommand($(this).data('command'), false, null);
		}
    });

    /* Project quickview tabs */
    $('body').on('click', '.zpm_nav_item', function() {
    	tabId = $(this).data('zpm-tab');
    	var parentModal = $(this).closest('.zpm-modal');
    	if (parentModal.length > 0) {
    		parentModal.find('.zpm_nav_item').removeClass('zpm_nav_item_selected');
	    	parentModal.find('.zpm_tab_pane').removeClass('zpm_tab_active');
	    	parentModal.find('.zpm_tab_pane[data-zpm-tab="' + tabId + '"]').addClass('zpm_tab_active');
    	} else {
    		$('body').find('.zpm_nav_item').removeClass('zpm_nav_item_selected');
	    	$('body').find('.zpm_tab_pane').removeClass('zpm_tab_active');
	    	$('body').find('.zpm_tab_pane[data-zpm-tab="' + tabId + '"]').addClass('zpm_tab_active');
    	}
    	$(this).addClass('zpm_nav_item_selected');

		if (tabId == "project-tasks") {
			$('.project-type-board').addClass('no-background');
		} else {
			$('.project-type-board').removeClass('no-background');
		}
    });

    // Delete a task
	$('body').on('click', '#zpm_delete_task', function(){
		var task_id = $('body').find('#zpm_task_view_id').val();

		ZephyrProjects.remove_task({
			task_id: task_id
		});

		$('body').find('.zpm_task_list_row[data-task-id="' + task_id + '"]').remove();

		if ($('body').find('.zpm_task_list_row').length <= 0) {

			// If user is on the 'All Tab'
			if ( $('body').find('.zpm_selection_option[data-zpm-filter="-1"]').hasClass('zpm_nav_item_selected') ) {
				$('.zpm_no_results_message').show();
				$('body').find('#zpm_task_option_container').addClass('zpm_hidden');
				$('body').find('#zpm_task_list_container').addClass('zpm_hidden');
			} else {
				$('body').find('.zpm_task_list').html( '<p class="zpm_error_message">' + zpm_localized.strings.no_results_found + '</p>' );
			}
		}

    	ZephyrProjects.close_modal();
	});

	$('body').on('click', '.zpm_filter_file', function(){
		var project_id = $(this).data('project-id');
		$('.zpm_filter_file').removeClass('zpm_selected_link');
		$(this).addClass('zpm_selected_link');
		$('body').find('.zpm_file_item_container').hide();

		if (project_id == '-1') {
			$('body').find('.zpm_file_item_container').show();
			$('body').find('#zpm_no_files').hide();
		}

		$('#zpm-new-file-project-value').val(project_id);
		// If the project has files
		if ($('body').find('.zpm_file_item_container[data-project-id="' + project_id + '"]').length > 0) {
			if (project_id == '-1') {
				$('body').find('.zpm_file_item_container').show();
			} else {
				$('body').find('.zpm_file_item_container[data-project-id="' + project_id + '"]').show();
			}
			$('#zpm_no_files').hide();
		} else {
			if (project_id == '-1') {
				$('#zpm_no_files').hide();
			} else {
				if ( $('.zpm_file_item_container').length <= 0 ) {
					$('#zpm_no_files').show();
				} else {
					$('#zpm_no_files').hide();
				}
			}
		}
	});

	/* WP Dashboard Charts */
	var zpm_progress_chart = document.getElementById("zpm-dashboard-project-chart");
	var completed_projects = $('body').find('#zpm-dashboard-project-chart').data('project-completed');
	var pending_projects = $('body').find('#zpm-dashboard-project-chart').data('project-pending');

    var zpm_chart_data = {
	    labels: [
	        zpm_localized.strings.completed_projects,
	        zpm_localized.strings.pending_projects
	    ],
	    datasets: [{
            data: [ completed_projects, pending_projects],
            backgroundColor: [
            	'#ec1665',
                "#14aaf5",
            ],
            borderWidth: 0
        }]
	};

	var chart_options = {
	  cutoutPercentage: 80,
	  legend: {
	    position: 'bottom'
	  },
	  animation: {
	    animateRotate: false,
	    animateScale: true
	  }
	};

	if (zpm_progress_chart !== null) {
		var doughnut_chart = new Chart(zpm_progress_chart, {
		  type: 'doughnut',
		  data: zpm_chart_data,
		  options: chart_options
		});
	}

	function zpm_loader_modal( message ) {
		var html = '<div id="zpm_loader_modal" class="zpm-modal active"><div class="zpm_task_loader"></div>' + message + '</div>';
		if ($('body').find('#zpm_loader_modal').length > 0) {
			zpm_close_loader_modal();
		}
		$('body').append(html);
	}

	function zpm_close_loader_modal() {
		$('body').find('#zpm_loader_modal').remove();
	}

	$('body').on('click', '#zpm_update_project_progress', function(){
		zpm_update_project_progress();
	});

	function zpm_update_project_progress( project_id ) {
		// Display a project progress chart
		if (typeof project_id == 'undefined') {
			var project_id = $('body').find('#zpm_project_editor').data('project-id');
		}

		var data = {
			project_id: project_id
		}

		ZephyrProjects.project_progress(data, function(response){
			var data = [];
			$(response.chart_data).each(function(e, f){
				data.push({
					date: f.date,
					completed: f.completed_tasks,
					pending: f.pending_tasks,
					overdue: f.overdue_tasks
				});
			});

			// temp
			//ZephyrProjects.project_chart(data);
			zpm_close_loader_modal();
		});
	}

	$('#zpm_load_activities').on('click', function(){
		var button = $(this);
		var offset = button.data('offset');
		var data = {
			offset: offset
		}
		button.data('offset', offset+=1);
		zpm_loader_modal('Loading activity...');

		ZephyrProjects.display_activity(data, function(response){
			$('body').find('#zpm_loader_modal').remove();
			if (response !== false && response !== '') {
				$('#zpm_activity_body').append(response);
			} else {
				button.addClass('disabled').attr('disabled', 'disabled');
				zpm_close_loader_modal();
			}
		});
	});

	// Progress Page
	var project_selector = $('#zpm_project_progress_select');

	if (project_selector.length > 0) {
		var project_id = project_selector.val();
		zpm_update_project_progress( project_id );
		zpm_loader_modal('Loading progress...');
	}

	project_selector.on('change', function(){
		var project_id = $(this).val();
		zpm_update_project_progress( project_id );
		zpm_loader_modal(zpm_localized.strings.loading_progress);
	});

	// Quick Menu
	$('#zpm_new_quick_file').on('click', function(){
		ZephyrProjects.open_modal('zpm_new_file_upload');
	});

	$('#zpm_submit_file').on('click', function(){
		ZephyrProjects.close_modal();
		var attachment = [];
		attachment['attachment_id'] = $('#zpm_uploaded_file_name').val();
		attachment['attachment_type'] = 'project';
		attachment['subject_id'] = $('#zpm_file_upload_project').val();

		upload_attachment(attachment['attachment_id'], attachment['attachment_type'], attachment['subject_id']);
	});

	// File Uploader
	var quick_file_uploader;
	$('#zpm_upload_file').on('click', function() {
		if (quick_file_uploader) {
			quick_file_uploader.open();
			return;
		}

		quick_file_uploader = wp.media.frames.file_frame = wp.media({
			title: 'Select File',
			button: {
			text: 'Select File'
		}, multiple: false });

		quick_file_uploader.on('select', function() {
			var attachment = quick_file_uploader.state().get('selection').first().toJSON();
			$('#zpm_uploaded_file_name').val(attachment.id);
		});
		quick_file_uploader.open();
	});

	$('body').on('click', '#zpm_add_project_to_dashboard', function(){
		var project_id = $(this).closest('.zpm_project_item').data('project-id');
		var data = {
			project_id: project_id
		}
		ZephyrProjects.add_to_dashboard(data);
	});

	$('body').on('click', '.zpm_remove_project_from_dashboard', function(){
		var project_id = $(this).closest('.zpm_dashboard_project').data('project-id');
		var data = {
			project_id: project_id
		}
		$(this).parents('.zpm_dashboard_project_container').remove();
		ZephyrProjects.remove_from_dashboard(data);
	});

	$('#zpm_dismiss_review_notice').on('click', function(){
		$(this).closest('.zpm_admin_notice').remove();
		var data = {
			notice: 'review_notice'
		}
		ZephyrProjects.dismiss_notice(data);
	});

	$('#zpm_dismiss_welcome_notice').on('click', function(){
		$(this).closest('.zpm_admin_notice').remove();
		var data = {
			notice: 'welcome_notice'
		}
		ZephyrProjects.dismiss_notice(data);
	});

	// Update project status
	$('#zpm_update_project_status').on('click', function(){
		var project_id = $(this).data('project-id');
		var status = $('#zpm_project_status').html();
		var status_color = $('.zpm_project_status.active').data('status');
		var data = {
			project_id: project_id,
			status: status,
			status_color: status_color
		};

		ZephyrProjects.update_project_status(data, function(response){});
	});

	$('.zpm_project_status').on('click', function(){
		$('.zpm_project_status').removeClass('active');
		$(this).addClass('active');
		$('#zpm_project_status').text($(this).data('status-name'));
	});

	$('body').on('click', '#zpm_add_custom_field_pro', function(){
		$(this).find('.zpm-pro-notice').toggleClass('active');
	});

	$('body').on('click', '.zpm-close-pro-notice', function(){
		$(this).closest('.zpm-pro-notice').removeClass('active');
	});

	$('body').on('click', '#zpm-save-project-members', function(){
		var members = [];
		var project_id = $('#zpm-project-id').val();

		$('.zpm-project-member').each(function(){
			var checked = $(this).is(':checked');
			var member_id = $(this).data('member-id');
			if (checked) {
				members.push(member_id);
			}
		});

		var data = {
			project_id: project_id,
			members: members
		}

		ZephyrProjects.Project.update_members( data, function(response){
		});
	});

	$('body').on('click', '#zpm-select-all-project-members', function(){
		var action = $(this).data('zpm-action');
		if (action === "select_all") {
			$('.zpm-project-member').each(function(){
				var checked = $(this).is(':checked');
				if (!checked) {
					$(this).trigger('click');
				}
			});

			$('#zpm-select-all-project-members').data('zpm-action', 'deselect_all').text(zpm_localized.strings.deselect_all);
		} else {
			$('.zpm-project-member').each(function(){
				var checked = $(this).is(':checked');
				if (checked) {
					$(this).trigger('click');
				}
			});
			$('#zpm-select-all-project-members').data('zpm-action', 'select_all').text(zpm_localized.strings.select_all);
		}


	});

	$('body').on('change', '.zpm-can-zephyr', function(){
		var checked = $(this).is(':checked');
		var userID = $(this).data('user-id');
		ZephyrProjects.updateUserAccess({ user_id: userID, access: checked }, function(res){
			ZephyrProjects.notification(zpm_localized.strings.access_updated);
		});
	});

	$('.zpm-dismiss-whats-new').on('click', function(){
		let noticeContainer = $(this).closest('#zpm-whats-new');
		let notice_id = $(this).closest('#zpm-whats-new').data('notice');

		noticeContainer.addClass( 'zpm-hidden' );
		ZephyrProjects.dismiss_notice({
			notice: notice_id
		});
	});

	$('.zpm-dismiss-notice').on('click', function(){
		let notice_id = $(this).data('notice-id');
		$(this).addClass('dismissed');
		ZephyrProjects.dismiss_notice({
			notice: notice_id
		});
	});

	$('.zpm-dismiss-notice-button').on('click', function(){
		let noticeContainer = $(this).closest('#zpm-whats-new');
		let notice_id = $(this).data('notice-version')

		noticeContainer.addClass( 'zpm-hidden' );
		ZephyrProjects.dismiss_notice({
			notice: notice_id
		});
	});

	$('[data-zpm-modal-trigger]').on('click', function(){
		let id = $(this).data('zpm-modal-trigger');
		ZephyrProjects.open_modal(id);
	});

	$('[data-zpm-modal]').on('click', function(){
		let id = $(this).data('zpm-modal');
		ZephyrProjects.open_modal(id);
	});


	$('#zpm-new-team').on('click', function(){
		let teamName = $('#zpm-new-team-name');
		let teamDescription = $('#zpm-new-team-description');
		let teamMembers = [];

		$('.zpm-new-team-member').each(function(){
			if ($(this).is(':checked')) {
				teamMembers.push($(this).data('member-id'));
			}
			$(this).removeAttr('checked');
		});

		ZephyrProjects.addTeam({
			name: teamName.val(),
			description: teamDescription.val(),
			members: teamMembers
		}, function(res) {
			$('.zpm-teams-list').append(res.html);
			$('#zpm-new-task-team-selection').append('<option value="' + res.team.id + '">' + res.team.name + '</option>');
		});

		teamName.val('');
		teamDescription.val('');
		ZephyrProjects.close_modal('#zpm-new-team-modal');
		$('#zpm-no-teams-notice').hide();
	});

	// Check if new task - team selection has changed
	$('body').on('change', '#zpm-new-task-team-selection', function(){
		let teamId = $(this).val();
        // $('#zpm_new_task_assignee').val('');
        // $('#zpm_new_task_assignee').trigger("chosen:updated");

		ZephyrProjects.getTeam({
			id: teamId
		}, function(res) {
			if (res != null) {
				var values = [];
				$.each(res.members, function(key, val) {
					let userId = val.id;
					values.push(userId);

				});
				$('#zpm_new_task_assignee').val(values);
	        	$('#zpm_new_task_assignee').trigger("chosen:updated");
			}
		});
	});

	$('body').on('click', '.zpm-edit-team', function(){
		let id = $(this).data('team-id');
		let idHidden = $('#zpm-edit-team-id');
		let teamName = $('#zpm-edit-team-name');
		let teamDescription = $('#zpm-edit-team-description');

		$('body').find('.zpm-edit-team-member').removeAttr('checked');
		idHidden.val(id);
		teamName.val('');
		teamDescription.val('');

		ZephyrProjects.open_modal('zpm-edit-team-modal');

		$('body').find('#zpm-modal-loader-edit-team').show();
		ZephyrProjects.getTeam({
			id: id
		}, function(res) {
			$('body').find('#zpm-modal-loader-edit-team').hide();
			teamName.val(res.name);
			teamDescription.val(res.description);
			$.each(res.members, function(key, val) {
				let userId = val.id;
				$('body').find('.zpm-edit-team-member[data-member-id="' + userId + '"]').attr('checked', 'checked');
			});
		});

	});

	$('body').on('click', '.zpm-delete-team', function(){
		let id = $(this).data('team-id');
		let container = $(this).closest('.zpm_team_member');

		ZephyrProjects.confirm(zpm_localized.strings.delete_team_notice, function(){
			container.remove();
			ZephyrProjects.deleteTeam({
				id: id
			}, function(res) {
			});
		});
	});

	$('#zpm-edit-team').on('click', function(){
		let id = $('#zpm-edit-team-id');
		let teamName = $('#zpm-edit-team-name');
		let teamDescription = $('#zpm-edit-team-description');
		let teamMembers = [];

		$('.zpm-edit-team-member').each(function(){
			if ($(this).is(':checked')) {
				teamMembers.push($(this).data('member-id'));
			}
			$(this).removeAttr('checked');
		});

		ZephyrProjects.updateTeam({
			id: id.val(),
			name: teamName.val(),
			description: teamDescription.val(),
			members: teamMembers
		}, function(res) {
			$('body').find('.zpm_team_member[data-team-id="' + id.val() + '"]').replaceWith(res);
		});

		teamName.val('');
		teamDescription.val('');
		ZephyrProjects.close_modal('#zpm-edit-team-modal');
	});

	jQuery('body').on('click', function(e){
		var target = jQuery(e.target);
		var id = target.prop('id');

		if (jQuery(e.target).hasClass('.zpm_fancy_dropdown') || id == 'zpm_add_new_btn' || target.closest('#zpm_add_new_btn').length > 0) {

	    } else {
			jQuery('body').find('.zpm_fancy_dropdown').removeClass('active');
	    }
	});

	// Deactivation survey
	// $('body').on('click', '[data-slug="zephyr-project-manager"] .deactivate a', function(e){
	// 	e.preventDefault()
 //        var urlRedirect = document.querySelector('[data-slug="zephyr-project-manager"] .deactivate a').getAttribute('href');
 //        var html = `<div id="zpm_modal_background" class="zpm_modal_background zpm-modal-background active" data-zpm-trigger="remove_modal"></div><div id="zephyr-deactivation-modal" class="zpm-modal active">
 //        	<div class="zpm-modal-header">Please can you let me know why you are deactivating the plugin so that I can improve it?</div>
 //        	<div class="zpm-deactivation-form">
 //        		<div><input type="radio" name="zpm-deactivation-reason" id="zpm-deactivation-reason-needs" value="didnt_meet_needs"> <label for="zpm-deactivation-reason-needs">Plugin did not meet my needs</label></div>
	// 			<div><input type="radio" name="zpm-deactivation-reason" id="zpm-deactivation-reason-bugs" value="bugs"> <label for="zpm-deactivation-reason-bugs">There were bugs or errors on my site</label></div>
	// 			<div><input type="radio" name="zpm-deactivation-reason" id="zpm-deactivation-reason-features" value="features"> <label for="zpm-deactivation-reason-features">Features were lacking</label>
	// 			<textarea id="zpm-deactivation-features-suggestion" placeholder="Please let me know which features you felt were lacking I could add them ASAP and improve the plugin" class="zpm_input"></textarea></div>

	// 			<div>
	// 			<input type="radio" name="zpm-deactivation-reason" id="zpm-deactivation-reason-other" value="other"> <label for="zpm-deactivation-reason-other">Other</label>
	// 			<textarea id="zpm-deactivation-other-textarea" placeholder="Please specify so that I can improve the plugin in the future. Thank you." class="zpm_input"></textarea>
	// 			</div>
	// 			<p>Thank you for your feedback, it is greatly appreciated!</p>
 //        	</div>
 //        	<div class="zpm-deactivation-buttons">
 //        		<a data-zpm-trigger="remove_modal" class="zpm_button">Cancel</a>
 //        		<a id="zpm-send-deactivate-form" href="${urlRedirect}" class="zpm_button">Deactivate</a>
 //        	</div>
 //        </div>`;

 //        $('body').append(html);
 //    });

 //    $('body').on('click', '#zpm-send-deactivate-form', function(e){
 //    	e.preventDefault();
 //    	$(this).text('Deactivating...');
 //        let urlRedirect = $(this).attr('href');
 //        let val = $('body').find('input[name="zpm-deactivation-reason"]:checked').val();
 //        let suggestionText = $('body').find('#zpm-deactivation-features-suggestion').val();
 //        let otherText = $('body').find('#zpm-deactivation-other-textarea').val();
 //        ZephyrProjects.submit_deactivation_survey( { reason: val, suggestion: suggestionText, other: otherText }, function( res ) {
 //        	window.location.href = urlRedirect;
 //        } );
 //    });

    function zpmSetupRippleEffect() {
    	// Material ripple effect
		$('body').on('click', '.zpm_button,[data-ripple], [ripple], [zpm-ripple], .zpm_project_title.project_name, .zpm_button_outline', function(e) {
			var $self = $(this);

			if ($self.attr('disabled') || e.target.className.indexOf('zpm_task_mark_complete') > -1 || e.target.className.indexOf('zpm-material-checkbox-label') > -1 ) {
				return;
			}

			var initPos = $self.css('position'),
				offs = $self.offset(),
				x = e.pageX - offs.left,
				y = e.pageY - offs.top,
				dia = Math.min(this.offsetHeight, this.offsetWidth, 100), // start diameter
				$ripple = $('<div/>', {class : 'ripple',appendTo : $self });

			if (!initPos || initPos === 'static') {
				$self.css({position:'relative'});
			}

			$('<div/>', {
				class : 'rippleWave',
				css : {
					background: $self.data('ripple'),
					width: dia,
					height: dia,
					left: x - (dia/2),
					top: y - (dia/2),
				},
				appendTo: $ripple,
				one: {
				animationend : function(){
					$ripple.remove();
				}
			}
			});
		});
    }

    $('#zpm_edit_task_name').on('input', function(){
    	$('#zpm_task_name_title').text($(this).val());
    });

    jQuery(document).on( 'zephyr_task_created', function( e ){
    	if (zephyrSocket) {
	    	e.ndata.devices = zpm_localized.device_ids;
	    	e.ndata.user_id = zpm_localized.user_id;
	    	zephyrSocket.emit('task-created', zpm_localized.website, e.ndata );
	    }

    	let currentTotal = jQuery("#zpm_stat_tasks_total").text();
    	let newTotal = parseInt(currentTotal) + 1;
    	jQuery("#zpm_stat_tasks_total").text(newTotal);
    	let currentActive = jQuery("#zpm_stat_tasks_active").text();
    	let newActive = parseInt(currentActive) + 1;
    	jQuery("#zpm_stat_tasks_active").text(newActive);
    });

    jQuery(document).on( 'zephyr_task_deleted', function( e ){
    	if (zephyrSocket) {
	    	e.ndata.devices = zpm_localized.device_ids;
	    	e.ndata.user_id = zpm_localized.user_id;
	    	zephyrSocket.emit('task-deleted', zpm_localized.website, e.ndata );
	    }
    });

    jQuery(document).on( 'zephyr_new_message', function( e ){
    	if (zephyrSocket) {
	    	e.ndata.devices = zpm_localized.device_ids;
	    	zephyrSocket.emit( 'new-message', zpm_localized.website, e.ndata );
	    }
    });

    jQuery(document).on( 'zephyr_project_created', function( e ){
    	var data = e.ndata;
    	e.ndata.devices = zpm_localized.device_ids;
    	if (zephyrSocket) {
    		zephyrSocket.emit( 'new-project', zpm_localized.website, e.ndata );
    	}


    	jQuery('body').find('#zpm_new_task_project').append('<option value="' + data.project.id + '">' + data.project.name + '</option>');
		jQuery('body').find('#zpm_new_task_project').trigger('chosen:updated').trigger('change');

    	let currentCount = jQuery("#zpm_projects_created_count").text();
    	let newCount = parseInt(currentCount) + 1;
    	jQuery("#zpm_projects_created_count").text(newCount);
    	jQuery("#zpm_project_stats_total").html(newCount);

    	let currentActive = jQuery("#zpm_projects_active_count").text();
    	let newActive = parseInt(currentActive) + 1;
    	jQuery("#zpm_projects_active_count").text(newActive);
    });

    // Listen for new tasks created by other users
    if (zephyrSocket) {
	    zephyrSocket.on( 'task-created', function( data ) {
	    	if (typeof data.user_id !== zpm_localized.user_id) {
	    		if (data.assignee == zpm_localized.user_id) {
	    			ZephyrProjects.notification( data.username + " assigned a new task to you: <a href='" + zpm_localized.tasks_url + "&action=view_task&task_id=" + data.id + "' target='_blank'>" + data.name + "</a>", false, 5000 );
	    		} else {
	    			ZephyrProjects.notification( data.username + " created a new task: <a href='" + zpm_localized.tasks_url + "&action=view_task&task_id=" + data.id + "' target='_blank'>" + data.name + "</a>", false, 4000 );
	    		}

	    		var new_task = 	data.new_task_html;
				$('body').find('.zpm_task_list').prepend(new_task);
				$('body').find('.zpm_message_center').remove();
				$('body').find('#zpm_task_option_container').removeClass('zpm_hidden');
				$('body').find('#zpm_task_list_container').removeClass('zpm_hidden');
				$('body').find('.zpm_no_results_message').addClass('zpm_hidden');
				$('.zpm_no_results_message').hide();
	    	}
	    });
	}

    // Listen for new tasks created by other users
    if (zephyrSocket) {
	    zephyrSocket.on( 'new-project', function( data ) {
	    	if (typeof data.user_id !== zpm_localized.user_id) {
	    		$('body').find('#zpm_project_manager_display').removeClass('zpm_hide');
				$('body').find('.zpm_no_results_message').hide();
				$('body').find('.zpm_project_grid').prepend(data.html);
				ZephyrProjects.notification( data.username + " created a new project: <a href='" + zpm_localized.projects_url + "&action=edit_project&project=" + data.project.id + "' target='_blank'>" + data.project.name + "</a>", false, 4000 );
	    	}
	    });
	}

    // Listen for new message sent by other users
    if (zephyrSocket) {
	    zephyrSocket.on( 'new-message', function( data ) {
	    	if (typeof data.user_id !== zpm_localized.user_id) {
	    		if (data.subject == "task") {
	    			$('body').find('.zpm_task_comments[data-task-id="' + data.id + '"]').prepend(data.html);
	    			ZephyrProjects.notification( "New comment on the task: <a href='" + zpm_localized.tasks_url + "&action=view_task&task_id=" + data.id + "' target='_blank'>" + data.subject_object.name + "</a>", false, 4000 );
	    		} else {
	    			$('body').find('.zpm_task_comments[data-project-id="' + data.id + '"]').prepend(data.html);
	    			ZephyrProjects.notification( "New comment on the project: <a href='" + zpm_localized.projects_url + "&action=edit_project&project=" + data.id + "' target='_blank'>" + data.subject_object.name + "</a>", false, 4000 );
	    		}
	    	}
	    });
	}

	$('#zpm_edit_project_name').on('input', function(){
		$('#zpm_project_name_title').text($(this).val());
	});

   	$('.zpm_nav_item').on('click', function(){
		var tab = $(this).data('zpm-tab');
		if (typeof tab !== "undefined") {

		}
		location.hash = tab;
	});

   	var hash = window.location.hash.slice(1);
	if (typeof hash !== 'undefined' && hash !== "") {
		$('.zpm_nav_item[data-zpm-tab="' + hash + '"]').trigger('click');
	}

	$('body').on('click', '#zpm-copy-task-shortcode', function() {
		var shortcode = $(this).data('shortcode');
		ZephyrProjects.copy_to_clipboard( shortcode );
		ZephyrProjects.notification( zpm_localized.strings.shortcode_copied + ': ' + shortcode );
	});

	$('body').on('click', '#zpm-copy-project-shortcode', function() {
		var shortcode = $(this).data('shortcode');
		ZephyrProjects.copy_to_clipboard( shortcode );
		ZephyrProjects.notification( zpm_localized.strings.shortcode_copied + ': ' + shortcode );
	});

	jQuery('#zpm_create_task').keypress(function(e) {
		let keycode = e.which;
		if (keycode == 13) {
			jQuery('#zpm_save_task').click();
		}
	});

	jQuery('.zpm_project_name_input').keypress(function(e) {
		let keycode = e.which;
		if (keycode == 13) {
			jQuery('#zpm_modal_add_project').click();
		}
	});

	// document.onkeyup = function(e) {
	// 	if (e.which == 27) {
	// 		jQuery('#zpm_modal_background').click();
	// 	} else if (e.shiftKey && e.which == 84) {
	// 		jQuery('body').find('#zpm_quickadd_task').click();
	// 	} else if (e.shiftKey && e.which == 80) {
	// 		jQuery('body').find('#zpm_create_quickproject').click();
	// 	}
	// };

	$('.zpm-switch-project-type').on('click', function() {
		var id = $(this).data('project-id');
		var type = $(this).data('type');
		var pro = $(this).data('zpm-pro');

		if (pro && (pro == "1" || pro == "true")) {
			$(this).closest('#zpm_switch_project_type_button').find('.zpm-project-type__label').text(zpm_localized.strings.loading);
			ZephyrProjects.switch_project_type({
				project_id: id,
				type: type
			}, function(){
				location.reload();
			});
		}
	});

	$('button#zpm_profile_settings').on('click', function(e){
		var custom_fields = [];

		$('body').find('#zpm_profile_settings .zpm_task_custom_field[data-zpm-cf-id]').each(function(){
			var id = $(this).data('zpm-cf-id');
			var type = $(this).data('zpm-cf-type');
			var value = $(this).val();

			if (type == "checkbox") {
				value = $(this).is(':checked');
			}

			custom_fields.push({
				id: id,
				value: value
			});

		});

		var data = $(this).serializeArray(); // convert form to array
		data.push({name: "NonFormValue", value: custom_fields });
		$.ajax({
		    type: 'POST',
		    url: zpm_localized.ajaxurl,
		    data: $.param(data),
		});
	});

	// Create new status
	$('#zpm_create_status').on('click', function(){
		ZephyrProjects.close_modal('#zpm_new_status_modal');

		var name = $('#zpm_status_name').val();
		var color = $('#zpm_status_color').val();
		var type = $('#zpm-status-type__new').val();

		ZephyrProjects.create_status( {
			name: name,
			color: color,
			type: type
		}, function(res) {
			$('.zpm-' + type + '-list').append(res.html);
		} );
	});

	// Update status
	$('#zpm_edit_status').on('click', function(){
		ZephyrProjects.close_modal('#zpm_edit_status_modal');

		var name = $('#zpm_edit_status_name').val();
		var color = $('#zpm_edit_status_color').val();
		var slug = $('#zpm-edit-status-id').val();
		var type = $('#zpm-status-type__edit').val();

		var selector = '.zpm-' + type + '-list__item[data-' + type + '-slug="' + slug + '"]';

		ZephyrProjects.update_status( {
			name: name,
			color: color,
			slug: slug,
			type: type
		}, function(res) {
			$(selector).replaceWith(res.html);
		} );
	});

	// Delete Status
	$('body').on('click', '.zpm-delete-status', function(e){
		e.stopPropagation();
		var slug = $(this).data('id');
		if (confirm( zpm_localized.strings.delete_status_prompt )) {
			ZephyrProjects.delete_status({
				slug: slug,
				type: 'status'
			}, function(){
			});
			$(this).closest('.zpm-status-list__item').remove();
		}
	});

	$('body').on('click', '.zpm-delete-priority', function(e){
		e.stopPropagation();
		var slug = $(this).data('id');
		if (confirm( zpm_localized.strings.delete_status_prompt )) {
			ZephyrProjects.delete_status({
				slug: slug,
				type: 'priority'
			}, function(){
			});
			$(this).closest('.zpm-priority-list__item').remove();
		}
	});

	//$('#zpm-calendar__filter-assignee').val(zpm_localized.user_id).trigger('chosen:updated');

	$('body').on('change', '#zpm-calendar__filter-project, #zpm-calendar__filter-team, #zpm-calendar__filter-assignee, #zpm-calendar__filter-completed', function(){
		filterCalendarTasks()
	});

	ZephyrProjects.ajax({
		action: 'zpm_get_user_projects',
		user_id: zpm_localized.user_id
	}, function(response){
		$('.zpm-user-project-count-value').text(response.project_count);
	});

	// Deprecated since adding pagination
	// if (ZephyrProjects.isMembersPage()) {
	// 	ZephyrProjects.ajax({
	// 		action: 'zpm_team_members_list_html'
	// 	}, function(response){
	// 		$('#zpm_members').html(response.html);
	// 	});
	// }

	// Paginate member list
	var zpm_members_paged = 1;
	$('body').on('click', '#zpm-members-pagination__next', function(e){
		var pages = $(this).data('zpm-pages');

		$(this).data('zpm-page', zpm_members_paged);
		if ( zpm_members_paged < pages ) {
			zpm_members_paged++;
			ZephyrProjects.notification(zpm_localized.strings.loading);
			ZephyrProjects.ajax({
				action: 'zpm_get_members',
				page: zpm_members_paged,
				limit: 10
			}, function(response) {
				var html = '';
				response.forEach(function(member){
					html += member.list_html;
				});
				$('#zpm-member-list__table').html(html);
				ZephyrProjects.remove_notifications();
			});
			$('#zpm-members-pagination__previous').removeAttr('disabled');
			if (zpm_members_paged == pages) {
				$(this).attr('disabled', 'disabled');
			} else {
				$(this).removeAttr('disabled');
			}
		} else {
			$(this).attr('disabled', 'disabled');
		}
	});

	$('body').on('click', '#zpm-members-pagination__previous', function(e){
		var pages = $('#zpm-members-pagination__next').data('zpm-pages');

		$('#zpm-members-pagination__next').data('zpm-page', zpm_members_paged);
		if ( zpm_members_paged > 1 ) {
			zpm_members_paged--;
			ZephyrProjects.notification(zpm_localized.strings.loading);
			ZephyrProjects.ajax({
				action: 'zpm_get_members',
				page: zpm_members_paged,
				limit: 10
			}, function(response) {
				var html = '';
				response.forEach(function(member){
					html += member.list_html;
				});
				ZephyrProjects.remove_notifications();
				$('#zpm-member-list__table').html(html);
			});
			$('#zpm-members-pagination__next').removeAttr('disabled');
			if (zpm_members_paged == 1) {
				$(this).attr('disabled', 'disabled');
			} else {
				$(this).removeAttr('disabled');
			}
		} else {
			$(this).attr('disabled', 'disabled');
		}
	});

	$('body').on('click', '.zpm-members-pagination__page', function(e){
		var thisBtn = $(this);
		var page = $(this).data('page');
		zpm_members_paged = page;

		jQuery('.zpm-members-pagination__page').removeAttr('disabled');

		//$(this).data('zpm-page', zpm_members_paged);

		ZephyrProjects.notification(zpm_localized.strings.loading);
		ZephyrProjects.ajax({
			action: 'zpm_get_members',
			page: page,
			limit: 10
		}, function(response) {
			var html = '';
			response.forEach(function(member){
				html += member.list_html;
			});
			$('#zpm-member-list__table').html(html);
			ZephyrProjects.remove_notifications();
			thisBtn.attr('disabled', 'disabled');
		});

	});

	var doughnut_chart;
	var zpm_project_doughnut_data;

	// If we are viewing a single project
	if ( zpm_localized.current_project !== "-1" ) {
		ZephyrProjects.ajax({
			action: 'zpm_get_project_members',
			project_id: zpm_localized.current_project
		}, function(response){
			if ( typeof response !== "string" ) {
				response.forEach( function( user_id ) {
					ZephyrProjects.ajax({
						action: 'zpm_get_user_progress',
						user_id: user_id,
						project_id: zpm_localized.current_project
					}, function(response){
						var member = $('.zpm-project-progress__member[data-user-id="' + user_id + '"]');
						var percent = Math.round( parseInt( response.percent_complete ) );

						member.find('.zpm-progress-member__percent').text( percent + '%' );
						if ( percent >= 100 ) {
							member.addClass('zpm-green');
						}
						if ( percent < 100 ) {
							member.addClass('zpm-yellow-green');
						}
						if ( percent < 75 ) {
							member.addClass('zpm-yellow');
						}
						if ( percent < 50 ) {
							member.addClass('zpm-yellow-orange');
						}
						if ( percent < 25 ) {
							member.addClass('zpm-orange');
						}
						if ( percent <= 0 ) {
							member.addClass('zpm-red');
						}

						if ( parseInt( response.tasks_total ) <= 0 ) {
							member.addClass('zpm-no-tasks');
						}

						member.find('.zpm-project-progress__member-details').html(response.html);
					});
				});
			}
		});

		ZephyrProjects.ajax({
			action: 'zpm_project_task_progress',
			project_id: zpm_localized.current_project
		}, function(response){
			var zpm_progress_chart = document.getElementById("zpm-project-chart__doughnut");
			var completed_projects = response.completed;
			var pending_projects = response.pending;
			var overdue_tasks = response.overdue;

		    zpm_project_doughnut_data = {
			    labels: [
			        zpm_localized.strings.completed_tasks,
			        zpm_localized.strings.pending_tasks,
			        zpm_localized.strings.overdue_tasks
			    ],
			    datasets: [{
		            data: [ completed_projects, pending_projects, overdue_tasks ],
		            backgroundColor: [
		            	'#00bc8a',
		                "#6500d8",
		                "#e8005c",
		            ],
		            borderWidth: 0
		        }]
			};

			var chart_options = {
			  cutoutPercentage: 70,
			  legend: {
			    position: 'bottom'
			  },
			  animation: {
			    animateRotate: true,
			    animateScale: true
			  }
			};

			chart_options.tooltips = {
		        yAlign: 'bottom',
		        callbacks: {
		            labelColor: function(tooltipItem, chart) {
		                return {
		                    backgroundColor: 'rgba(20, 170, 245, .7)',
		                    color: 'rgb(255,255,255)'
		                }
		            },
		            labelTextColor: function(tooltipItem, chart) {
		                return '#fff';
		            },
		            labelChartColor: function(tooltipItem, chart) {
		                return {
		                    backgroundColor: 'rgba(20, 170, 245, .7)',
		                    color: 'rgb(255,255,255)'
		                }
		            },
		            labelTextChartColor: function(tooltipItem, chart) {
		                return "rgb(255,255,255)"
		            },
		        },
		    }
		    if (zpm_progress_chart !== null) {
		    	doughnut_chart = new Chart(zpm_progress_chart, {
				  type: 'doughnut',
				  data: zpm_project_doughnut_data,
				  options: chart_options
				});
		    }
		});
	}

	$('#zpm-project-chart__filter').on('change', function(){
		var user_id = $(this).val();

		ZephyrProjects.ajax({
			action: 'zpm_get_user_progress',
			project_id: zpm_localized.current_project,
			user_id: user_id
		}, function(response){
			zpm_project_doughnut_data = {
			    labels: [
			        zpm_localized.strings.completed_tasks,
			        zpm_localized.strings.pending_tasks,
			        zpm_localized.strings.overdue_tasks
			    ],
			    datasets: [{
		            data: [ response.completed, response.pending, response.overdue ],
		            backgroundColor: [
		            	'#00bc8a',
		                "#6500d8",
		                "#e8005c",
		            ],
		            borderWidth: 0
		        }]
			};
			doughnut_chart.data = zpm_project_doughnut_data;
			doughnut_chart.update();
		});
	});

	// Pagination of projects
	var projectsPage = ZephyrProjects.getUrlParam('projects_page');
	var zpmProjectsTotal = 100;
	var zpmProjectsPage = projectsPage ? projectsPage : 1;
	var zpmProjectsPerPage = zpm_localized.settings.projects_per_page;

	$('.zpm-projects-pagination__page').on('click', function(){
		var page = $(this).data('page');
		zpmProjectsPage = page;

		if (zpmProjectsPage <= zpmProjectsTotal) {
			$(this).addClass('zpm-pagination__current-page');
			$('.zpm-projects-pagination__page').not(this).removeClass('zpm-pagination__current-page');
			ZephyrProjects.paginateProjects( zpmProjectsPage, zpmProjectsPerPage, false );
		} else {
		}
	});

	// Deprecated next and prev
	// $('.zpm-projects-next').on('click', function(){

	// 	zpmProjectsPage++;

	// 	if (zpmProjectsPage <= zpmProjectsTotal) {
	// 		ZephyrProjects.paginateProjects( zpmProjectsPage, zpmProjectsPerPage, false );
	// 		$('.zpm-projects-previous').removeAttr('disabled');
	// 		if (zpmProjectsPage == zpmProjectsTotal) {
	// 			$(this).attr('disabled', 'disabled');
	// 		} else {
	// 			$(this).removeAttr('disabled');
	// 		}
	// 	} else {
	// 		alert('No more pages');
	// 	}

	// });

	// $('.zpm-projects-previous').on('click', function(){
	// 	zpmProjectsPage--;
	// 	if (zpmProjectsPage > 0) {
	// 		ZephyrProjects.paginateProjects( zpmProjectsPage, zpmProjectsPerPage, false );
	// 		$('.zpm-projects-next').removeAttr('disabled');
	// 		if (zpmProjectsPage == 1) {
	// 			$(this).attr('disabled', 'disabled');
	// 		} else {
	// 			$(this).removeAttr('disabled');
	// 		}
	// 	} else {
	// 		alert('No more pages');
	// 	}
	// });

	$('.zpm-project-view__option').on('click', function(){
		var view = $(this).data('view');
		$(this).addClass('zpm-state__active');
		$('.zpm-project-view__option').not(this).removeClass('zpm-state__active');

		ZephyrProjects.updateUserMeta( zpm_localized.user_id, 'project_view', view );

		switch(view) {
			case 'grid':
				$('#zpm_project_list').removeClass('zpm-project-view__list');
				break;
			case 'list':
				$('#zpm_project_list').addClass('zpm-project-view__list');
				break;
			default:
				break;
		}
	});

	$('.zpm-toggle-state').on('click', function(){
		$(this).toggleClass('zpm-state__active');
	});

	$('#zpm-project-view__archived').on('click', function(){
		var filter = '-1';
		if ($(this).hasClass('zpm-state__active')) {
			// Show all
			filter = 'archived';
		} else {
		}
		var title = $('#zpm-project-view__title');
		title.text(zpm_localized.strings.loading);

		ZephyrProjects.filter_projects( {
			zpm_filter: filter,
			zpm_user_id: zpm_localized.user_id
		}, function(response){
			if (filter == 'archived') {
				title.text(zpm_localized.strings.archived_projects);
			} else {
				title.text(zpm_localized.strings.all_projects);
			}
			$('body').find('.zpm_project_grid').html(response.html);
			//zpm_close_loader_modal();
			$('.zpm_project_progress_bar').each( function() {
				var total_tasks = $(this).data('total_tasks');
				var completed_tasks = $(this).data('completed_tasks')
				var width = (total_tasks !== 0) ? ((completed_tasks / total_tasks) * 100) : 0;
				$(this).css('width', width + '%');
			});
		});
	});

	$('body').on('click', '.zpm_project_item', function(e){

	});

	var category_id = ZephyrProjects.getUrlParam('category_id');
	ZephyrProjects.ajax({
		action: 'zpm_get_available_project_count',
		user_id: zpm_localized.user_id,
		category_id: category_id,
	}, function(response){
		zpmProjectsTotal = response.count;
	});

	// Recurrence selection changed
	$('#zpm-new-task__recurrence-select').on('change', function(){
		var val = $(this).val();
		$('.zpm-new-task__recurrence-section').hide();
		$('.zpm-new-task__recurrence-section[data-section="' + val + '"]').show();
	});

	// Recurrence selection changed
	$('#zpm-edit-task__recurrence-select').on('change', function(){
		var val = $(this).val();
		$('.zpm-edit-task__recurrence-section').hide();
		$('.zpm-edit-task__recurrence-section[data-section="' + val + '"]').show();
	});

	var currentProjectDescription = $('#zpm_edit_project_description').val();
	$('#zpm_chat_message, #zpm_edit_project_description').mentionsInput({
		elastic: false,
		onDataRequest:function (mode, query, callback) {
			ZephyrProjects.ajax({
				action: 'zpm_getUserData'
			}, function(responseData){
				responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 });
				callback.call(this, responseData);
			});
		}
	});
	$('#zpm_edit_project_description').val(currentProjectDescription);

	jQuery(document).on('zpm.task_window_loaded', function(res){
		$('#zpm_task_view_container #zpm_chat_message').mentionsInput({
			elastic: false,
			onDataRequest:function (mode, query, callback) {
				ZephyrProjects.ajax({
					action: 'zpm_getUserData'
				}, function(responseData){
					responseData = _.filter(responseData, function(item) { return item.name.toLowerCase().indexOf(query.toLowerCase()) !== -1 });
					callback.call(this, responseData);
				});
			}
		});
	});
	//$('#zpm_edit_project_description').val(currentProjectDescription);

	// Delete all Zephyr data
	jQuery('body').on('click', '#zpm-delete-data__button', function(){
		ZephyrProjects.confirm_modal('Delete All Zephyr Data','Are you sure that you would like to permanently delele ALL your Zephyr Project Manager data including Tasks, Projects, Categories, Files, Messages and settings? This action cannot be undone.', 'Delete', function(){
			jQuery('#zpm-delete-data__form button').click();
		});
	});

	jQuery('body').on('click', '.zpm-import-projects__btn', function(){
		ZephyrProjects.projectImporter();
	});

	jQuery('body').on('click', '.zpm-import-tasks__btn', function(){
		ZephyrProjects.taskImporter();
	});

	jQuery('body').on('click', '#zpm-members__bulk-access-btn', function(){
		ZephyrProjects.memberPicker({
			multiple: true,
			buttons: [
				{
					text: 'Remove Access',
					id: 'zpm-members-muli__remove',
					callback: function(members){
						if (typeof members !== 'undefined') {
							members.forEach(function(member){
								var userID = member.id;
								ZephyrProjects.updateUserAccess({ user_id: userID, access: false }, function(res){
									ZephyrProjects.notification(zpm_localized.strings.access_updated);
									window.location.reload();
								});
							});
						}
					}
				},
				{
					text: 'Allow Access',
					id: 'zpm-members-muli__add',
					callback: function(members){
						if (typeof members !== 'undefined') {
							ZephyrProjects.updateUserAccess({ user_id: members, access: true }, function(res){
								ZephyrProjects.notification(zpm_localized.strings.access_updated);
								window.location.reload();
							});
						}
					}
				}
			]
		}, function(data){
		});
	});
});

function filterCalendarTasks() {
	var project = jQuery('#zpm-calendar__filter-project').val();
	zpm_calendar.fullCalendar('rerenderEvents');
}
