var zpmNewTaskDate = '';
var ZPM_Manager = new ZephyrProjects();

(function($){
	
	jQuery(document).ready(function(){
		jQuery('.zpm-shortcode-progress-bar').each( function() {
			var total_tasks = $(this).data('total_tasks');
			var completed_tasks = $(this).data('completed_tasks')
			var width = (total_tasks !== 0) ? ((completed_tasks / total_tasks) * 100) : 0;
			$(this).css('width', width + '%');
		});


		if (jQuery('body').find('.zpm-shortcode__calendar').length > 0) {
			initializeCalendar();
		}

		jQuery('body').on('click', '.zpm-shortcode__file-upload-btn', function(){
			var taskId = $(this).data('task');
			var projectId = $(this).data('project');
			var subject = 'task';
			
			if (taskId == '' && projectId == '') {
				subjectId = '-1';
			} else if (taskId == '') {
				subjectId = projectId;
				subject = 'project';
			} else if (projectId == '') {
				subjectId = taskId;
				subject = 'task';
			}

			var zpmUploader = null;
			ZephyrProjects.upload_file(zpmUploader, function(res){
				
				var attachments = res;
				var attachment_holder = $('body').find('.zpm-shortcode__file-holder');
	           	for (var i = 0; i < attachments.length; ++i) {
	           		var attachment = attachments[i];
	           		var atts = attachment.attributes;

	           		ZephyrProjects.upload_attachment(atts.id, subject, subjectId, function(res){
						});	
	           		var html = '<div class="zpm_file_item_container" data-project-id="142">\
						<div class="zpm_file_item" data-attachment-id="337" data-attachment-url="' + atts.url + '" data-attachment-name="ivan-torres-376149-unsplash.jpg" data-task-name="" data-attachment-date="30 Apr 2019 05:25">\
						<div class="zpm_file_preview" data-zpm-action="show_info">\
							<span class="zpm_file_image" style="background-image: url(' + atts.url + ');"></span>\
						</div>\
						<h4 class="zpm_file_name">\
							' + atts.filename + ' \
						</h4>\
				</div>\
			</div>';
					attachment_holder.append(html);
	            }
			}, true);
		});

		$('body').on('click', '[data-trigger="new-task-modal"], #zpm-add-task', function(){
			zpmOpenNewTaskModal();
		});
		
		$('body').on('click', '#zpm-shortcode-modal__new-task #zpm_save_task', function(){
			var modal = jQuery('body').find('#zpm-shortcode-modal__new-task');

			var name = modal.find('#zpm_new_task_name').val();
			var description = modal.find('#zpm_new_task_description').val();
			var project = modal.find('#zpm_new_task_project').val();
			var assignee = modal.find('#zpm_new_task_assignee').val();
			var due_date = modal.find('#zpm_new_task_due_date').val();
			var start_date = modal.find('#zpm_new_task_start_date').val();
			var priority = 'priority_none';
			var status = modal.find('#zpm-new-task__status').val();
			var recurrence_type = modal.find('#zpm-new-task__recurrence-select').val();
			var recurrence_data = {};
			recurrence_data.type = recurrence_type;

			switch(recurrence_type) {
				case 'daily':
					var days = modal.find('#zpm-new-task__recurrence-daily').val();
					var expiration = modal.find('#zpm-new-task__recurrence-expiration-date').val();

					recurrence_data.days = days;
					recurrence_data.expires = expiration;
					break;
				case 'weekly':
					var expiration = modal.find('#zpm-new-task__recurrence-expiration-date-weekly').val();
					recurrence_data.expires = expiration;
					break;
				case 'monthly':
					var expiration = modal.find('#zpm-new-task__recurrence-expiration-date-monthly').val();
					recurrence_data.expires = expiration;
					break;
				case 'annually':
					var expiration = modal.find('#zpm-new-task__recurrence-expiration-date-annual').val();
					recurrence_data.expires = expiration;
					break;
				default:
					break;
			}

			var shortcodeType = $('.zpm-shortcode-task-list').data('type');

			var data = {
	        	task_name: name,
				task_description: description,
				subtasks: [],
				task_project: project,
				task_assignee: assignee,
				task_due_date: due_date,
				task_start_date: start_date,
				task_custom_fields: [],
				frontend: true,
				kanban_col: 1,
				priority: priority,
				status: status,
				shortcode: true,
				recurrence: recurrence_data,
				shortcode_type: shortcodeType
	        }
			ZephyrProjects.create_task( data, function(response){
				jQuery('body').find('.zpm-shortcode-task-list').prepend(response.shortcode_html);
			});

			ZephyrProjects.close_modal();
		});

		$('body').on('click', '.zpm-shortcode-task__delete', function(){
			var parentTask = $(this).closest('.zpm-task-shortcode');
			var taskId = parentTask.data('task-id');
			if (confirm(zpm_localized.strings.delete_task_notice)) {
				ZephyrProjects.remove_task({
					task_id: taskId
				});
				parentTask.remove();
			}
		});

		// Execute file actions
		$('body').on('click', '.zpm_file_action, .zpm_file_preview', function(){
			var action = $(this).data('zpm-action');
			var target = $(this).closest('.zpm_file_item');
			var $container = $(this).closest('.zpm_file_item_container');
			var projectId = $container.data('project-id');
			var fileId = target.data('attachment-id');
			var subjectName = target.data('task-name');

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
				<p>' + subjectName + '</p>\
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

		$('body').on('click', '.zpm-shortcode-background', function(){
			ZephyrProjects.close_modal();
		});

		$('body').on('click', '.zpm-complete-shortcode-task', function(){
			var task = $(this).closest('.zpm-project-shortcode__task');
			if (task.length == 0) {
				task = $(this).closest('.zpm-task-shortcode');
			}
			var taskId = task.data('task-id');
			var checked = !task.hasClass('zpm-task-completed') ? '1' : '0';
			$(this).closest('.zpm-task-card').toggleClass('completed');
			task.toggleClass('zpm-task-completed');
			var data = {
				id: taskId,
				completed: checked
			};
			ZephyrProjects.complete_task(data, function(response){

			});
		});

		$('body').on('click', '[data-edit-task]', function(e){
			if (!$(this).data('edit-task')) {
				return;
			}
			var taskId = $(this).data('task-id');
			var target = $(e.target);

			if (target.hasClass('zpm-complete-shortcode-task') || target.hasClass('zpm-shortcode-task__delete') || target.hasClass('zpm-shortcode-task__comments')) {
				return false;
			}
			zpmNewModal( '', '', '', '<div class="zephyr-shortcode-loader"></div>', 'zpm-shortcode-modal__edit-task');
			ZephyrProjects.ajax({
				action: 'zpm_editTaskModal',
				id: taskId
			}, function(res){
				var modal = jQuery('body').find('#zpm-shortcode-modal__edit-task');
				modal.html(res.html);
				jQuery('body').find('#zpm_edit_task_assignee, #zpm_edit_task_project, #zpm-edit-task-team-selection, #zpm-edit-task__status, #zpm-edit-task__recurrence-select, #zpm-edit-task__recurrence-daily').chosen();
				$('#zpm_edit_task_start_date, #zpm_edit_task_due_date, .zpm-datepicker').datepicker({dateFormat: 'yy-mm-dd' });
			});
		});

		$('body').on('click', '[data-edit-project]', function(e){
			if (!$(this).data('edit-project')) {
				return;
			}
			var projectId = $(this).data('project-id');
			var target = $(e.target);

			if (target.hasClass('zpm-complete-shortcode-task') || target.hasClass('zpm-shortcode-task__delete') || target.hasClass('zpm-shortcode-task__comments')) {
				return false;
			}
			zpmNewModal( '', '', '', '<div class="zephyr-shortcode-loader"></div>', 'zpm-shortcode-modal__edit-project');
			ZephyrProjects.ajax({
				action: 'zpm_editProjectModal',
				id: projectId
			}, function(res){
				var modal = jQuery('body').find('#zpm-shortcode-modal__edit-project');
				modal.html(res.html);
				$('#zpm_edit_project_start_date, #zpm_edit_project_due_date, .zpm-datepicker').datepicker({dateFormat: 'yy-mm-dd' });
			});
		});

		// Show task comments
		$('body').on('click', '.zpm-shortcode-task__comments', function(){
			var taskId = $(this).closest('.zpm-task-shortcode').data('task-id');
			zpmNewModal( '', '', '', '<div class="zephyr-shortcode-loader"></div>', 'zpm-shortcode-modal__task-comments');
			ZephyrProjects.ajax({
				action: 'zpm_getTaskComments',
				id: taskId
			}, function(res){
				var modal = jQuery('body').find('#zpm-shortcode-modal__task-comments');
				modal.html(res.html);
				var html = '<div class="zpm-shortcode-task-comments__input-zone"><input type="text" id="zpm-shortcode-comment__textbox" placeholder="Type here..." class="zpm-task-comment__input" /><span id="zpm-send-comment" class="fa" data-task-id="' + res.data.id + '">Send</span></div>';
				modal.append(html);
			});
		});

		$('body').on('click', '#zpm-shortcode-modal__task-comments #zpm-send-comment', function() {
			var task_id = $(this).data('task-id');
			var text_box = $(this).closest('#zpm-shortcode-modal__task-comments').find('#zpm-shortcode-comment__textbox');
			var message = $.trim(text_box.val());
			message = message.replace(/\<div><br><\/div>/g,'');

			var data = {
			user_id: zpm_localized.user_id,
			subject: 'task',
			subject_id: task_id,
			message: message,
			type: 'message',
			attachments: []
		};

		ZephyrProjects.send_comment(data, function(response) {
			$('body').find('#zpm-shortcode-modal__task-comments .zpm_task_comments').append(response.html);
		});
			//send_message('task', task_id, message);
			text_box.val('');
		});

		// New Task Recurrence
		$('body').on('change', '#zpm-new-task__recurrence-select', function(){
			var val = $(this).val();
			$('body').find('.zpm-new-task__recurrence-section').hide();
			$('body').find('.zpm-new-task__recurrence-section[data-section="' + val + '"]').show();
		});

		// Edit Task Recurrence
		$('body').on('change', '#zpm-edit-task__recurrence-select', function(){
			var val = $(this).val();
			$('body').find('.zpm-edit-task__recurrence-section').hide();
			$('body').find('.zpm-edit-task__recurrence-section[data-section="' + val + '"]').show();
		});

		$('body').on('click', '#zpm-update-task__btn', function(){
			var modal = jQuery('body').find('#zpm-shortcode-modal__edit-task');

			var name = modal.find('#zpm_edit_task_name').val();
			var description = modal.find('#zpm_edit_task_description').val();
			var project = modal.find('#zpm_edit_task_project').val();
			var assignee = modal.find('#zpm_edit_task_assignee').val();
			var due_date = modal.find('#zpm_edit_task_due_date').val();
			var start_date = modal.find('#zpm_edit_task_start_date').val();
			var priority = 'priority_none';
			var status = modal.find('#zpm-edit-task__status').val();
			var taskId = modal.find('#zpm-edit-task__id').val();
			var recurrence_type = modal.find('#zpm-edit-task__recurrence-select').val();
			var recurrence_data = {};
			recurrence_data.type = recurrence_type;

			switch(recurrence_type) {
				case 'daily':
					var days = modal.find('#zpm-edit-task__recurrence-daily').val();
					var expiration = modal.find('#zpm-edit-task__recurrence-expiration-date').val();

					recurrence_data.days = days;
					recurrence_data.expires = expiration;
					break;
				case 'weekly':
					var expiration = modal.find('#zpm-edit-task__recurrence-expiration-date-weekly').val();
					recurrence_data.expires = expiration;
					break;
				case 'monthly':
					var expiration = modal.find('#zpm-edit-task__recurrence-expiration-date-monthly').val();
					recurrence_data.expires = expiration;
					break;
				case 'annually':
					var expiration = modal.find('#zpm-edit-task__recurrence-expiration-date-annual').val();
					recurrence_data.expires = expiration;
					break;
				default:
					break;
			}
			var shortcodeType = $('.zpm-shortcode-task-list').data('type');

			var data = {
				task_id: taskId,
	        	task_name: name,
				task_description: description,
				subtasks: [],
				task_project: project,
				task_assignee: assignee,
				task_due_date: due_date,
				task_start_date: start_date,
				task_custom_fields: [],
				frontend: true,
				kanban_col: 1,
				priority: priority,
				status: status,
				recurrence: recurrence_data,
				shortcode: true,
				shortcode_type: shortcodeType
	        }

			ZephyrProjects.update_task( data, function(response){
				jQuery('body').find('.zpm-task-shortcode[data-task-id="' + response.task_id + '"]').replaceWith(response.shortcode_html);
			});

			ZephyrProjects.close_modal();
		});


		$('body').on('click', '#zpm-update-project__btn', function(){
			var modal = jQuery('body').find('#zpm-shortcode-modal__edit-project');
			var projectId = modal.find('[data-ajax-name="project-id"]').val();
			var name = modal.find('[data-ajax-name="name"]').val();
			var description = modal.find('[data-ajax-name="description"]').val();
			var due_date = modal.find('[data-ajax-name="start-date"]').val();
			var start_date = modal.find('[data-ajax-name="due-date"]').val();

	        var data = {
				project_id: projectId,
				project_name: name,
				project_description: description,
				project_due_date: due_date,
				project_start_date: start_date,
				shortcode: true
			};

			ZephyrProjects.update_project( data, function(response){
				jQuery('body').find('.zpm-project-shortcode[data-project-id="' + projectId + '"]').replaceWith(response.shortcode_html);
			});

			ZephyrProjects.close_modal();
		});

		jQuery('#zpm-shortcode__action-button #zpm-add-new-btn').on('click', function(){
			jQuery(this).toggleClass('active');
			jQuery('#zpm-add-new-dropdown').toggleClass('active');
		});

		jQuery('#zpm-shortcode__action-button #zpm-add-new-dropdown li').on('click', function(){
			jQuery(this).closest('#zpm-add-new-dropdown').removeClass('active');
			jQuery('#zpm-add-new-btn').removeClass('active');
		});

		jQuery('#zpm-shortcode__action-button #zpm-add-project').on('click', function(){
			zpmOpenNewProjectModal();
		});

		jQuery('body').on('click', '#zpm-shortcode-modal__new-project #zpm_modal_add_project', function(){
			//var form 
			var form = jQuery(this).closest('#zpm-shortcode-modal__new-project');
			var name = form.find('.zpm_project_name_input').val();
			var description = form.find('#zpm-new-project-description').val();

			var data = {
				project_name: name,
				project_description: description,
				project_categories: '',
				project_due_date: '',
				type: 'list',
				priority: '',
				categories: []
			};

			ZPM_Manager.create_project( data, function(response){
				jQuery.event.trigger( { type: 'zephyr_project_created', ndata: response } );
			});
			ZephyrProjects.close_modal();
		});

		// Progress charts for projects
		jQuery('.zpm-project-progress__shortcode').each(function(){
			var doughnut_chart;
			var zpm_project_doughnut_data;
			var projectId = jQuery(this).data('project-id');
			var element = jQuery(this);

			var colorCompleted = element.data('color-completed');
			var colorPending = element.data('color-pending');
			var colorOverdue = element.data('color-overdue');

			ZephyrProjects.ajax({
				action: 'zpm_project_task_progress',
				project_id: projectId
			}, function(response){
				var zpm_progress_chart = element[0];
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
			            	colorCompleted,
			                colorPending,
			                colorOverdue,
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
		});
	});


	function initializeCalendar() {
		var tasks = [];
		var firstDay = typeof zpm_localized.settings.first_day !== "undefined" ? zpm_localized.settings.first_day : 1;
		var calendar = null;
		var assignee = $('.zpm-shortcode__calendar').data('user');
		var isCompleted = $('.zpm-shortcode__calendar').data('completed');

		ZephyrProjects.getCalendarItems(function(data){

			$.each( data, function( key, val ) {
				//var url = zpm_localized.is_admin ? zpm_localized.tasks_url + '&action=view_task&task_id=' + val.id : zpm_localized.manager_home + '?action=task&id=' + val.id;
				var completed = (val.completed !== '0') ? 'completed' : 'not-completed';

				if (isCompleted == '0' && val.completed == '1') {
					return;
				} else if (isCompleted == '1' && val.completed == '0') {
					return;
				}

				var project_name = zpm_localized.strings.none;

				var itemType = 'task';

				if (typeof val.itemType !== 'undefined') {
					itemType = val.itemType;
					switch(val.itemType) {
						case 'milestone':
							url = zpm_localized.is_admin ? '' : zpm_localized.manager_home + '?action=milestones';
							break;
						default:
							break;
					}
				}
				var otherData = [];

				if ( typeof val.project_data !== "undefined" && val.project_data !== null ) {
					project_name = val.project_data.name;
				}

				if (assignee !== 'all') {
					if (assignee == 'current') {
						var count = 0;
						val.assignees.forEach( function( member ) {
					    	
					    	if ( member.id == zpm_localized.user_id ) {
					    		count++;
					    	}
					    });

					    if (count <= 0) {
					    	return;
					    }
					}
				}

				if ( typeof val.data !== "undefined" && val.data !== null ) {
					otherData = val.data;
				}


				if (typeof val.otherDays !== 'undefined') {
					val.otherDays.forEach(function(date){
						tasks.push({
							id: val.id,
							title: val.name,
							description: val.description,
							start: date,
							end: date,
							className: completed,
							project: project_name,
							assignee: val.assignee,
							assignees: val.assignees,
							completed: val.completed,
							status: val.status,
							team: val.team,
							project_id: val.project,
							styles: val.styles,
							itemType: itemType,
							data: otherData
						});
					});
				} else {
					tasks.push({
						id: val.id,
						title: val.name,
						description: val.description,
						start: val.date_due,
						end: val.date_due,
						className: completed,
						project: project_name,
						assignee: val.assignee,
						assignees: val.assignees,
						completed: val.completed,
						status: val.status,
						team: val.team,
						project_id: val.project,
						styles: val.styles,
						itemType: itemType,
						data: otherData
					});
				}
			});

			var options = {
				header: {
				   right: 'month, agendaWeek, today prev,next'
				},
				monthNames: zpm_localized.strings.month_names,
				dayNames: zpm_localized.strings.day_names,
				dayNamesShort: zpm_localized.strings.day_names_short,
				firstDay: firstDay,
				buttonText: zpm_localized.strings.button_text,
				events: tasks,
				editable: true,
				eventRender: function(event, element) {
				
					if (event.completed == '1') {
						element.prepend('<span class="zpm-calendar__completed">' + zpm_localized.strings.completed + '</span>');
					}

					element.attr("status", event.status);
					element.attr("data-team", event.team);
					element.attr("data-project", event.project_id);
					element.attr("data-assignee", event.assignee);
					element.attr("data-completed", event.completed);
					// element.attr("data-edit-task", "true");
					element.attr("data-task-id", event.id);
					element.attr("style", event.styles);
				    element.find(".fc-title").remove();
				    element.find(".fc-event-time").remove();
				    var new_description =
				    '<strong>' + event.title + '</strong><br/>'
				    + '<strong>Description: </strong><p class="zpm-calendar__description">' + event.description + '</p><br/>'
				        + '<strong>Project: </strong>' + event.project + '<br/>'
				        + '<strong>Assignee: </strong>'
				    ;
				    var count = 0;
				    event.assignees.forEach( function( assignee ) {
				    	if ( count <= 0 ) {
				    		new_description = new_description + assignee.name
				    	} else {
				    		new_description = new_description + ', ' + assignee.name
				    	}
				    	count++;
				    });

				    if ( event.assignees.length <= 0 ) {
				    	new_description = new_description + zpm_localized.strings.none;
				    }

				    new_description = new_description + '<br/>';
				    element.append(new_description);
				    //ZephyrProjects.filter_event(element, event);
				},
				eventDrop: function(info) {
					ZephyrProjects.updateTaskDueDate(info.id, info.start.toString(), function(response){
					});
				},
				dayClick: function(date, jsEvent, view) {
					var thisDate = new Date(parseInt(date._i));
					var dateFormat = $.datepicker.formatDate('yy-mm-dd', thisDate);
					zpmNewTaskDate = dateFormat;
					zpmOpenNewTaskModal();
		            //alert('Clicked on: ' + date.getDate()+"/"+date.getMonth()+"/"+date.getFullYear());  
		        },
			}

			calendar = $('.zpm-shortcode__calendar').fullCalendar( options );

			$('body').find('.zpm_task_loader').remove();

			if (ZephyrProjects.isCalendarPage()) {
				jQuery('.fc-month-button').text(zpm_localized.strings.month);
				jQuery('.fc-agendaWeek-button').text(zpm_localized.strings.week);
				jQuery('.fc-today-button').text(zpm_localized.strings.today);
			}
		});
	}
})(jQuery)

function zpmNewModal(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {

		var modal_navigation = (typeof navigation !== 'undefined' && navigation !== '') ? navigation : '';
		var modal_settings = (typeof options !== 'undefined' && options !== '') ? '<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown"><i class="dashicons dashicons-menu"></i>' + options + '</span>' : '';
		var modal = '<div id="' + modal_id + '" class="zpm-modal zpm-shortcode-modal" data-modal-action="remove" data-task-id="' + task_id + '" data-project-id="' + project_id + '">' +
					'<div class="zpm_modal_body">' +
						'<h2>' + subject + '</h2>' +
						'<h3 class="zpm_modal_task_name">' + header + '</h3>' + modal_settings +
						modal_navigation +
						'<div class="zpm_modal_content">' + content + '</div>' +
						'<div class="zpm_modal_buttons">' + buttons + '</div>' + 
					'</div>' +
				'</div';
		if ($('body').find('#zpm_modal_background').length <= 0) {
			modal = '<div id="zpm_modal_background" class="zpm-shortcode-background"></div>' + modal;
		}
		$('body').append(modal);
		ZephyrProjects.open_modal(modal_id);
	}

	function zpmOpenNewTaskModal(){
		zpmNewModal( '', '', '', '<div class="zephyr-shortcode-loader"></div>', 'zpm-shortcode-modal__new-task');
		ZephyrProjects.ajax({
			action: 'zpm_newTaskModal'
		}, function(res){
			var modal = jQuery('body').find('#zpm-shortcode-modal__new-task');
			modal.html(res.html);
			jQuery('body').find('#zpm_new_task_assignee, #zpm_new_task_project, #zpm-new-task-team-selection, #zpm-new-task__status, #zpm-new-task__recurrence-select, #zpm-new-task__recurrence-daily').chosen();
			$('#zpm_new_task_start_date, #zpm_new_task_due_date, .zpm-datepicker').datepicker({dateFormat: 'yy-mm-dd' });

			if (zpmNewTaskDate !== '') {
				$('body').find('#zpm_new_task_start_date').val(zpmNewTaskDate);
				$('body').find('#zpm_new_task_due_date').val(zpmNewTaskDate);
			}
		});
	}


function zpmOpenNewProjectModal(){
		zpmNewModal( '', '', '', '<div class="zephyr-shortcode-loader"></div>', 'zpm-shortcode-modal__new-project');
		ZephyrProjects.ajax({
			action: 'zpm_newProjectModal'
		}, function(res){
			var modal = jQuery('body').find('#zpm-shortcode-modal__new-project');
			modal.html(res.html);

			$('#zpm_new_project_start_date, #zpm_new_project_due_date, .zpm-datepicker').datepicker({dateFormat: 'yy-mm-dd' });

			// if (zpmNewTaskDate !== '') {
			// 	$('body').find('#zpm_new_task_start_date').val(zpmNewTaskDate);
			// 	$('body').find('#zpm_new_task_due_date').val(zpmNewTaskDate);
			// }
		});
	}