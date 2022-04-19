var ZephyrProjects;
var zpm_error_occurred;
var zpm_members = [];
var zpmProjects = [];

(function($){

	var task_loading_ajax = null;
	var project_loading_ajax = null;
	zpm_calendar = null;
	var projects_loading = null;

	var instance;
	ZephyrProjects = function() {

		if (instance) {
			return instance;
	    }

		instance = this;

		this.new_task_data = {};
		this.new_project_data = {};
		this.edit_project_data = {};

		this.set_new_task_data = function( data ) {
			this.new_task_data = data;
		}

		this.get_new_task_data = function() {
			return this.new_task_data;
		}

		this.set_new_project_data = function( data ) {
			this.new_project_data = data;
		}

		this.get_new_project_data = function() {
			return this.new_project_data;
		}

		this.set_edit_project_data = function( data ) {
			this.edit_project_data = data;
		}

		this.get_edit_project_data = function() {
			return this.edit_project_data;
		}

	    this.create_task = function( callback ) {
	    	var data = this.get_new_task_data();
			ZephyrProjects.notification( zpm_localized.strings.creating_task );

			data.action = 'zpm_new_task';
			data.zpm_nonce = zpm_localized.zpm_nonce;

			if ( task_loading_ajax != null ) {
	            task_loading_ajax.abort();
	            task_loading_ajax = null;
	        }

			task_loading_ajax = $.ajax({
				url: zpm_localized.ajaxurl,
				type: 'POST',
				dataType: 'json',
				data: data,
				error: function(response) {
					//ZephyrProjects.notification( zpm_localized.strings.error_creating_task );
					callback(response);
				},
				success: function(response) {
					ZephyrProjects.notification( zpm_localized.strings.task_created + ': ' + response.name);
					callback(response);
					jQuery.event.trigger( { type: 'zpm.task_created', zpm_data: response } );
				}
			});
		}

		this.create_project = function( data, callback ) {
			this.set_new_project_data(data);

			jQuery.event.trigger( { type: 'zpm_new_project_data_set' }, [ data ] );

			var data = this.get_new_project_data();
			data.action = 'zpm_new_project';
			data.zpm_nonce = zpm_localized.zpm_nonce;

			ZephyrProjects.notification( zpm_localized.strings.creating_project, true);

			$.ajax({
				url: zpm_localized.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: data,
				error: function(response) {
					ZephyrProjects.notification( zpm_localized.strings.error_creating_project );
					callback(response);
				},
				success: function(response) {
					ZephyrProjects.notification( zpm_localized.strings.project_created + ': "' + data.project_name + '"' );
					callback(response);
					jQuery.event.trigger( { type: 'zpm.project_created', zpm_data: response } );
				}
			});
		}

		this.update_project = function( data, callback ){
			this.set_edit_project_data(data);
			jQuery.event.trigger( { type: 'zpm_edit_project_data_set' }, [ data ] );
			var data = this.get_edit_project_data();
			data.action = 'zpm_save_project';
			data.zpm_nonce = zpm_localized.zpm_nonce;

			$.ajax({
				url: zpm_localized.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: data,
				error: function(response) {
					ZephyrProjects.notification( zpm_localized.strings.error_saving_task );
					callback(response);
				},
				success: function(response) {
					ZephyrProjects.notification( zpm_localized.strings.changes_saved );
					callback(response);
				}
			});	
		}

		return instance;
	}

	ZephyrProjects.Project = {};
	ZephyrProjects.Task = {};

	ZephyrProjects.notification = function( string, infinite, time, icon = '' ) {
		var infinite = (infinite) ? true : false;
		var current_notification = $(document).find('#zpm_system_notification');
		var time = (time) ? time : 2000;
		var icon_string = icon !== '' ? '<span class="zpm-notification-icon ' + icon + '"></span>' : '';
		var notification = '<div id="zpm_system_notification">' + icon_string + string + '</div>';

		if ( current_notification.length !== 0 ) {
			current_notification.html(string);
		} else {
			$('body').append(notification);
		}

		if ( !infinite ) {
			setTimeout( function() {
				$('body').find('#zpm_system_notification').addClass('zpm_hide_notification');
				setTimeout( function() {
					$('body').find('#zpm_system_notification').remove();
				}, 800 );
			}, time );
		}
	}

	ZephyrProjects.remove_notifications = function(){
		$('body').find('#zpm_system_notification').remove();
	}

	ZephyrProjects.modal = function(id, content, active) {
		var html = '<div id="' + id + '" class="zpm-modal">' + content + '</div>';
		$('body').append(html);
		if (active) {
			ZephyrProjects.open_modal(id);
		}
		var modal = jQuery('body').find('#' + id);
		return modal;
	}

	ZephyrProjects.open_modal = function(selector) {
		$('body').find('#zpm_modal_background').addClass('active');
		$('body').find('#' + selector).addClass('active');
	}

	ZephyrProjects.open_submodal = function(selector) {
		$('body').find('#' + selector).addClass('active').before('<div id="zpm-submodal-background"></div>');
	}

	ZephyrProjects.close_modal = function(selector) {
		if (selector) {
			$('body').find(selector).removeClass('active');
		} else {
			$('body').find('.zpm-modal').removeClass('active');
		}
		$('body').find('#zpm_modal_background, .zpm_modal_background').removeClass('active');
    	$('body').find('.zpm-modal[data-modal-action="remove"]').each(function(){
    		$(this).remove();
    	});
	}

	ZephyrProjects.close_submodal = function(selector) {
		if (selector) {
			$('body').find(selector).removeClass('active');
		} else {
			$('body').find('.zpm-modal').removeClass('active');
		}

		$('body').find('#zpm-submodal-background').remove();
	}

	ZephyrProjects.remove_modal = function(selector) {
		$('body').find('.zpm_modal_background, #zpm_modal_background').removeClass('active');
		$('body').find('').removeClass('active');

		if (ZephyrProjects.containsString(selector, '.') || ZephyrProjects.containsString(selector, '#')) {
			$('body').find(selector).remove();
		} else {
			$('body').find('#' + selector).remove();
		}
		
		$('body').find('#zpm-submodal-background').remove();
	}

	ZephyrProjects.scroll_to_bottom = function( selector ) {
		jQuery(selector).animate({
		    scrollTop: jQuery(selector).offset().top + jQuery(selector).height()
		}, 1000);
	}

	ZephyrProjects.get_task = function(data, callback) {
		data.action = 'zpm_get_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				alert( zpm_localized.strings.error_loading_tasks );
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.submit_deactivation_survey = function( data, callback ) {
		data.action = 'zpm_deactivation_survey';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.get_tasks = function(data, callback) {
		data.action = 'zpm_get_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				alert( zpm_localized.strings.error_loading_tasks );
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.get_all_tasks = function( callback) {
		var data = {};
		data.action = 'zpm_get_all_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		ZephyrProjects.ajax(data, function(response){
			callback(response);
		});
		// $.ajax({
		// 	url: zpm_localized.ajaxurl,
		// 	type: 'post',
		// 	dataType: 'json',
		// 	data: data,
		// 	error: function(response) {
		// 		callback(response);
		// 	},
		// 	success: function(response) {
		// 		callback(response);	
		// 	}
		// });
	}

	ZephyrProjects.getTasksDateRange = function( options = {}, callback ) {

		var data = {
			action: 'zpm_getTasksDateRange',
			nonce: zpm_localized.zpm_nonce,
			options: options
		};

		ZephyrProjects.ajax(data, function(response){
			callback(response);
		});
	}


	ZephyrProjects.getCalendarItems = function( callback) {
		var data = {};
		data.action = 'zpm_getCalendarItems';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.get_projects = function(callback) {
		var data = {};
		data.action = 'zpm_get_projects';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		if (zpmProjects.length > 0) {
			callback(zpmProjects);
		} else {
			$.ajax({
				url: zpm_localized.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: data,
				error: function(response) {
					callback([]);
				},
				success: function(response) {
					zpmProjects = response;
					callback(response);	
				}
			});
		}
	}

	ZephyrProjects.get_project_tasks = function( data, callback) {
		data.action = 'zpm_get_project_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);	
			}
		});
	}

	ZephyrProjects.create_task = function( data, callback ) {

		ZephyrProjects.notification( zpm_localized.strings.creating_task );

		data.action = 'zpm_new_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		if ( task_loading_ajax != null ) {
            task_loading_ajax.abort();
            task_loading_ajax = null;
        }

		task_loading_ajax = $.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_task );
				callback(response);
			},
			success: function(response) {
				jQuery.event.trigger( { type: 'zpm.task_created', zpm_data: response } );
				ZephyrProjects.notification( zpm_localized.strings.task_created + ': ' + response.name);
				callback(response);
			}
		});
	}

	/**
	* Updates a selected task
	*/

	ZephyrProjects.update_task = function(data, callback) {
		data.action = 'zpm_save_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				//ZephyrProjects.notification( zpm_localized.strings.task_updated );
				callback(response);
			},
			success: function(response) {
				//ZephyrProjects.notification( zpm_localized.strings.task_updated );
				callback(response);
			}
		});	
	}

	/**
	* Updates a selected task priority
	*/
	ZephyrProjects.update_task_priority = function(data, callback) {
		data.action = 'zpm_update_task_priority';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	ZephyrProjects.view_task = function(data, callback){
		data.action = 'zpm_view_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_viewing_task );
			},
			success: function(response) {
				callback(response);
				jQuery(document).trigger('zpm.task_window_loaded', response);
			}
		});
	}

	ZephyrProjects.updateFileProject = function(fileId, projectId){
		var data = {
			action: 'zpm_updateFileProject',
			zpm_nonce: zpm_localized.zpm_nonce,
			file_id: fileId,
			project_id: projectId
		};
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
			}
		});
	}

	/**
	* Copies a project into a new project
	*/
	ZephyrProjects.copy_project = function(data, callback) {
		data.action = 'zpm_copy_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		ZephyrProjects.notification( zpm_localized.strings.copying_project );

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_copying_project );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_copied + ': "' + data.project_name + '"');
				callback(response);
			}
		});	
	}

	/**
	* Initializes the calender and adds all tasks
	*/
	ZephyrProjects.initialize_calendar = function(){
		var tasks = [];
		var firstDay = typeof zpm_localized.settings.first_day !== "undefined" ? zpm_localized.settings.first_day : 1;

		ZephyrProjects.getCalendarItems(function(data){
			$.each( data, function( key, val ) {
				var url = zpm_localized.is_admin ? zpm_localized.tasks_url + '&action=view_task&task_id=' + val.id : zpm_localized.manager_home + '?action=task&id=' + val.id;
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

				var completed = (val.completed !== '0') ? 'completed' : 'not-completed';

				var project_name = zpm_localized.strings.none;

				if ( typeof val.project_data !== "undefined" && val.project_data !== null ) {
					project_name = val.project_data.name;
				}

				var otherData = [];

				if ( typeof val.data !== "undefined" && val.data !== null ) {
					otherData = val.data;
				}

				if (typeof val.otherDays !== 'undefined' && val.otherDays.length > 0) {
					val.otherDays.forEach(function(date){
						tasks.push({
							id: val.id,
							title: val.name,
							description: val.description,
							start: date,
							end: date,
							className: completed,
							url: url,
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
						url: url,
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
				defaultView: ZephyrProjects.isMobile() ? 'basicDay' : 'month',
				editable: true,
				eventRender: function(event, element) {
					var project = $('#zpm-calendar__filter-project').val();
					var team = $('#zpm-calendar__filter-team').val();
					var assignee = $('#zpm-calendar__filter-assignee').val();
					var completed = $('#zpm-calendar__filter-completed').val();

					if (project !== "all") {
						if (event.project_id !== project) {
							return false;
						}
					}

					if (completed !== "all") {
						if (completed == '1' || completed == '0') {
							if (event.completed !== completed) {
								return false;
							}
						} else {
							var valid = false;
							for (var field in event.data) {
								var custom_field = event.data[field];
								if (custom_field['id'] == '7') {
									if (custom_field['value'] == completed) {
										valid = true;
									} else {
										//valid = false;
									}
								}
							}

							if (!valid) {
								return false;
							}
						}
					}

					if (team !== "all") {
						if (event.team !== team) {
							return false;
						}
					}

					if (assignee !== "all") {
						
						var count = 0;
						event.assignees.forEach( function( member ) {
					    	
					    	if ( member.id == assignee ) {
					    		count++;
					    	}
					    });

					    if (count <= 0) {
					    	return false;
					    }
					}

					if (event.completed == '1') {
						element.prepend('<span class="zpm-calendar__completed">' + zpm_localized.strings.completed + '</span>');
					}

					element.attr("status", event.status);
					element.attr("data-team", event.team);
					element.attr("data-project", event.project_id);
					element.attr("data-assignee", event.assignee);
					element.attr("data-completed", event.completed);
					element.attr("style", event.styles);
				    element.find(".fc-title").remove();
				    element.find(".fc-event-time").remove();
				    element.addClass(event.itemType);
				    var new_description =
				    '<strong>' + event.title + '</strong><br/>'
				    + '<strong>Description: </strong><p class="zpm-calendar__description">' + event.description + '</p><br/>';

				    if (event.itemType !== 'project') {
				    	new_description += '<strong>Project: </strong>' + event.project + '<br/>'
				        + '<strong>Assignee: </strong>';
				         var count = 0;
				    
					    event.assignees.forEach( function( assignee ) {
					    	if ( count <= 0 ) {
					    		new_description = new_description + assignee.name
					    	} else {
					    		new_description = new_description + ', ' + assignee.name
					    	}
					    	count++;
					    });
				    }


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
				}
			}

			zpm_calendar = $('#zpm_calendar').fullCalendar( options );

			$('body').find('#zpm_calendar .zpm_task_loader').remove();

			if (ZephyrProjects.isCalendarPage()) {
				jQuery('.fc-month-button').text(zpm_localized.strings.month);
				jQuery('.fc-agendaWeek-button').text(zpm_localized.strings.week);
				jQuery('.fc-today-button').text(zpm_localized.strings.today);
			}
		});
		
		// $.getJSON( zpm_localized.rest_url + 'tasks', function( data ) {
		// 	$.each( data, function( key, val ) {
		// 		var url = zpm_localized.is_admin ? zpm_localized.tasks_url + '&action=view_task&task_id=' + val.id : zpm_localized.manager_home + '?action=task&id=' + val.id;
		// 		var completed = (val.completed !== '0') ? 'completed' : 'not-completed';
		// 		tasks.push({ 
		// 			title: val.name + '\n' + val.description,
		// 			start: val.date_start,
		// 			end: val.date_due,
		// 			className: completed,
		// 			url: url
		// 		});
		// 	});
		// }).success(function() { 
		// 	$('#zpm_calendar').fullCalendar({
		// 		header: {
		// 		    right: 'month, agendaWeek, today prev,next'
		// 		  },
		// 		events: tasks
		// 	}); 
		// 	$('body').find('.zpm_task_loader').remove();
		// }).error(function() {
		// 	$('#zpm_calendar').fullCalendar({
		// 		header: {
		// 		    right: 'month, agendaWeek, today prev,next'
		// 		  }
		// 	}); 
		// 	$('body').find('.zpm_task_loader').remove();
		// }).complete(function() {
		// 	$('body').find('.zpm_task_loader').remove();
		// });
	}

	ZephyrProjects.task_reminders = function(){
		var tasks = [];

		// $.getJSON( zpm_localized.rest_url + 'tasks', function(data) {
		// 	$.each( data, function( key, val ) {
		// 		var name = val.name;
		// 		var id = val.id;
		// 		var date_due = val.date_due;
		// 		const now = new Date();
		// 		var parts = date_due.split('-');
		// 		parts[2] = parts[2].split(' ');
		// 		parts[2] = parts[2][0];

		// 		if (val.completed == "1") {
		// 			return;
		// 		}
				
		// 		var mydate = new Date(parts[0], parts[1] - 1, parts[2]);

		// 		if (val.assignee == zpm_localized.user_id) {
		// 			if ((mydate.getFullYear() == now.getFullYear()) &&
		// 				(mydate.getMonth() == now.getMonth()) &&
		// 				(mydate.getDay() == now.getDay()) && !localStorage.getItem('task' + id)) {
		// 					ZephyrProjects.task_notification( zpm_localized.strings.task_due_today + ': ' + name, id, 'task');
		// 			} else if ((mydate.getFullYear() == now.getFullYear()) &&
		// 				(mydate.getMonth() == now.getMonth()) &&
		// 				(mydate.getDay() == now.getDay()+1) && !localStorage.getItem('taskReminder' + id)) {
		// 				ZephyrProjects.task_notification( zpm_localized.strings.task_due_tomorrow + ': "' + name + '"', id, 'taskReminder');
		// 			}
		// 		}
		// 	});

		// });

		ZephyrProjects.get_all_tasks( function( data ) {
			$.each( data, function( key, val ) {
				var name = val.name;
				var id = val.id;
				var date_due = val.date_due;
				const now = new Date();

				if (typeof date_due == "undefined") {
					return;
				}

				var parts = date_due.split('-');
				parts[2] = parts[2].split(' ');
				parts[2] = parts[2][0];

				if (val.completed == "1") {
					return;
				}
				
				var mydate = new Date(parts[0], parts[1] - 1, parts[2]);

				if (val.assignee == zpm_localized.user_id) {
					if ((mydate.getFullYear() == now.getFullYear()) &&
						(mydate.getMonth() == now.getMonth()) &&
						(mydate.getDay() == now.getDay()) && !localStorage.getItem('task' + id)) {
							ZephyrProjects.task_notification( zpm_localized.strings.task_due_today + ': ' + name, id, 'task');
					} else if ((mydate.getFullYear() == now.getFullYear()) &&
						(mydate.getMonth() == now.getMonth()) &&
						(mydate.getDay() == now.getDay()+1) && !localStorage.getItem('taskReminder' + id)) {
						ZephyrProjects.task_notification( zpm_localized.strings.task_due_tomorrow + ': "' + name + '"', id, 'taskReminder');
					}
				}
			});
		});
	}

	ZephyrProjects.task_notification = function(message, id, item){
		var count = $('body').find('.zpm_floating_notification').length;
		var last = $('body').find('.zpm_floating_notification').last();

		var notification = $('body').find('.zpm_floating_notification');
		var height = last.height();

		if (count > 0) {
			var position = last.offset();
			offset = $(window).height() - height;

		    $(window).scroll(function () {
		       var position = last.offset();
		       offset = position.top - height;
		    });

		}
		
		offset = count * 95;
		var notification_holder = $('body').find('#zpm_notifcation_holder');

		setTimeout(function(){
			notification_holder.prepend('<div class="zpm_floating_notification" style="margin-bottom: ' + offset + 'px;" data-id="' + id + '" data-item="' + item + '">Hi ' + zpm_localized.user_name + '<br/>' + message + '<button class="zpm_floating_notification_button">' + zpm_localized.strings.dismiss_notice + '</button></div>');
		},500);
	}

	/**
	* Updates the selected project
	*/
	ZephyrProjects.update_project = function(data, callback){
		data.action = 'zpm_save_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_saving_task );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.changes_saved );
				callback(response);
			}
		});	
	};

	/**
	* Returns the data for a project
	*/
	ZephyrProjects.get_project = function(data, callback){
		data.action = 'zpm_get_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		if ( project_loading_ajax != null ) {
            project_loading_ajax.abort();
            project_loading_ajax = null;
	    }

		project_loading_ajax = $.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				//callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	};

	ZephyrProjects.ajax_modal = function( id, title, classes ) {
		var html = '<div id="' + id + '" class="zpm-modal zephyr-modal ' + classes + '" data-modal-action="remove"><div class="zpm-modal__title"></div><div class="zpm-modal__content"><div class="zpm_task_loader"></div></div><div>';
		$('body').append( html );
		ZephyrProjects.open_modal( id );
		return jQuery('body').find('#' + id);
	}

	ZephyrProjects.create_project = function(data, callback) {
		data.action = 'zpm_new_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		ZephyrProjects.notification( zpm_localized.strings.creating_project, true);

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_project );
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_created + ': "' + data.project_name + '"' );
				callback(response);
				jQuery.event.trigger( { type: 'zpm.project_created', zpm_data: response } );
			}
		});
	}

	// Removes a message
	ZephyrProjects.remove_comment = function(data, callback) {
		data.action = 'zpm_remove_comment';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_removing_message );
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Marks a task as liked
	ZephyrProjects.like_task = function(data) {
		data.action = 'zpm_like_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
			}
		});
	}

	// Copies a task
	ZephyrProjects.copy_task = function(data, callback) {
		data.action = 'zpm_copy_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		if (typeof data.copy_options == "undefined") {
			var options_list = [
				"description", 
				"assignee",
				"subtasks",
				"attachments",
				"start_date",
				"due_date"
			];
			data.copy_options = options_list;
		}

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_copying_task );
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Converts a task to a project
	ZephyrProjects.task_to_project = function(data, callback) {
		data.action = 'zpm_convert_to_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		ZephyrProjects.notification( zpm_localized.strings.converting_to_project );

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_converting_task );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.new_project_created + ': ' + data.project_name );
				callback(response);
			}
		});	
	}

	// Marks a task as complete/incomplete
	ZephyrProjects.complete_task = function(data, callback) {
		data.action = 'zpm_update_task_completion';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
				if (typeof ZephyrProjects.Pro !== "undefined") {
					ZephyrProjects.Pro.milestones_updated( response );
				}
			},
			success: function(response) {
				callback(response);
				if (typeof ZephyrProjects.Pro !== "undefined") {
					ZephyrProjects.Pro.milestones_updated( response );
				}
			}
		});	
	}

	// Updates a tasks due date
	ZephyrProjects.updateTaskDueDate = function(taskId, dueDate, callback) {
		var data = {
			action: 'zpm_updateTaskDueDate',
			zpm_nonce: zpm_localized.zpm_nonce,
			task_id: taskId,
			date: dueDate
		};

		ZephyrProjects.notification(zpm_localized.strings.changes_saved);
		ZephyrProjects.ajax(data, function(response){
			callback(response);
		});
	}

	ZephyrProjects.complete_project = function( data, callback ) {
		data.action = 'zpm_complete_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Follow a task
	ZephyrProjects.follow_task = function(data, callback) {
		data.action = 'zpm_follow_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports a single task to CSV/JSON
	ZephyrProjects.export_task = function(data, callback) {
		data.action = 'zpm_export_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_task );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports all task to CSV/JSON
	ZephyrProjects.export_tasks = function(data, callback) {
		data.action = 'zpm_export_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_tasks );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Dismiss a notice
	ZephyrProjects.dismiss_notice = function(data) {
		data.action = 'zpm_dismiss_notice';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response){
			},
			success: function(response){
			}
		});
	}

	// Add project to dashboard
	ZephyrProjects.add_to_dashboard = function(data) {
		data.action = 'zpm_add_project_to_dashboard';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_adding_to_dashboard );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.added_to_dashboard );
			}
		});
	}

	// Remove project from dashboard
	ZephyrProjects.remove_from_dashboard = function(data) {
		ZephyrProjects.notification( zpm_localized.strings.adding_to_dashboard );
		data.action = 'zpm_remove_project_from_dashboard';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.problem_occurred );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.removed_from_dashboard );
			}
		});
	}

	// Display activity
	ZephyrProjects.display_activity = function(data, callback) {
		data.action = 'zpm_display_activities';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	// Get project progress
	ZephyrProjects.project_progress = function(data, callback) {
		data.action = 'zpm_project_progress';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				//ZephyrProjects.notification( zpm_localized.strings.error_loading_project_tasks );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Remove a task
	ZephyrProjects.remove_task = function(data, callback) {
		data.action = 'zpm_remove_task';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_task );
				callback();
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.task_deleted );
				jQuery.event.trigger( { type: 'zephyr_task_deleted', ndata: response } );
				callback();
			}
		});	
	}

	// Update a category
	ZephyrProjects.update_category = function(data, callback) {
		data.action = 'zpm_update_category';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_saving_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_saved );
				callback(response);
			}
		});
	}

	// Remove category
	ZephyrProjects.remove_category = function(data, callback) {
		data.action = 'zpm_remove_category';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_deleted );
				callback(response);
			}
		});	
	}

	// Uploads tasks
	ZephyrProjects.upload_tasks = function(data, callback) {
		data.action = 'zpm_upload_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_importing_file );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Deletes a project
	ZephyrProjects.delete_project = function(data, callback) {
		data.action = 'zpm_remove_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_deleting_project );
				jQuery.event.trigger( { type: 'zpm.project_deleted', zpm_data: response } );

			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project + ' <i><b>\'' + data.project_name + '\'</b></i> ' + zpm_localized.strings.deleted + '.' );
				jQuery.event.trigger( { type: 'zpm.project_deleted', zpm_data: response } );
				callback(response);
			}
		});
	}

	// Deletes a project
	ZephyrProjects.archiveProject = function(data, callback) {
		data.action = 'zpm_archiveProject';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				//ZephyrProjects.notification( zpm_localized.strings.error_deleting_project );
			},
			success: function(response) {
				if (data.archived) {
					ZephyrProjects.notification( zpm_localized.strings.project_archived );
				} else {
					ZephyrProjects.notification( zpm_localized.strings.project_unarchived );
				}
				
				callback(response);
			}
		});
	}

	// Filters a task
	ZephyrProjects.filter_tasks = function(data, callback) {
		data.action = 'zpm_filter_tasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_filtering );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Filters a project
	ZephyrProjects.filter_projects = function(data, callback) {
		data.action = 'zpm_filter_projects';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_filtering );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Create Status
	ZephyrProjects.create_status = function(data, callback) {
		data.action = 'zpm_create_status';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Update Status
	ZephyrProjects.update_status = function(data, callback) {
		data.action = 'zpm_update_status';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Delete Status
	ZephyrProjects.delete_status = function(data, callback) {
		data.action = 'zpm_delete_status';
		data.zpm_nonce = zpm_localized.zpm_nonce;
		
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Creates a category
	ZephyrProjects.create_category = function(data, callback) {
		data.action = 'zpm_create_category';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_creating_category );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.category_created );
				callback(response);
			}
		});
	}

	// Likes a project
	ZephyrProjects.like_project = function(data, callback) {
		data.action = 'zpm_like_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Exports a project to CSV/JSON
	ZephyrProjects.export_project = function(data, callback) {
		data.action = 'zpm_export_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_exporting_project_csv );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Sends a comment
	ZephyrProjects.send_comment = function(data, callback) {
		data.action = 'zpm_send_comment';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_sending_message );
				$('#zpm_task_chat_comment').removeClass('zpm_message_sending').html( zpm_localized.strings.comment );
			},
			success: function(response) {
				callback(response);
				jQuery.event.trigger( { type: 'zpm.message_sent', zpm_data: response } );
			}
		});
	}

	// Uploads a task file
	ZephyrProjects.uploadTaskFile = function(taskId, fileId, type = 'attachment', callback) {
		var data = {
			action: 'zpm_uploadTaskFile',
			zpm_nonce: zpm_localized.zpm_nonce,
			task_id: taskId,
			file_id: fileId,
			type: type
		}

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {

			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Updates a subtask
	ZephyrProjects.update_subtasks = function(data, callback) {
		data.action = 'zpm_update_subtasks';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.problem_occurred );
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	// Prints a project
	ZephyrProjects.print_project = function(data, callback) {
		data.action = 'zpm_print_project';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_printing_tasks );
			},
			success: function(response) {

				var projectPage = '<div id="zpm_print_project_page">' +
					'<h2 class="zpm_print_project_heading">' +  response['project'].name + '</h2>' +
					'<h3 class="zpm_print_subheading">' + zpm_localized.strings.printed_from_zephyr + '</h3>' +
					'<div class="zpm_print_project_tasks">' +
						'<ul class="zpm_print_project_task_list">';
								for (var i = 0; i < response['tasks'].length; i++) {
									var due_date = (response['tasks'][i].date_due !== '0000-00-00') ? 'Due: ' + response['tasks'][i].date_due : zpm_localized.strings.no_date_set
									var checked = (response['tasks'][i].completed == '1') ? 'checked' : '';
									projectPage = projectPage + '<li class="zpm_print_project_task">' +
										'<input type="checkbox" class="zpm_print_project_check" value="1" ' + checked + ' />' +
										'<span class="zpm_print_task_assignee">' + response['tasks'][i].username.display_name + ': </span>' +
										'<span class="zpm_print_task_name">' + response['tasks'][i].name + '</span>' +
										'<span class="zpm_print_task_due_date">' + due_date + '</span>' +
									'</li>';
								}
						projectPage = projectPage + '</ul>' +
					'</div>' +
				'</div>';

				setTimeout(function(){
					var printContents = projectPage;
					var originalContents = document.body.innerHTML;
					document.body.innerHTML = printContents;
					window.print();
					document.body.innerHTML = originalContents;
				}, 500);
				callback(response);
			}
		});
	}

	ZephyrProjects.project_chart = function(data) {
		var zpm_progress_chart = document.getElementById('zpm_project_progress_chart');
		var overdue_data = [];
		var pending_data = [];
		var completed_data = [];
		var x_labels = [];
		var labelColors = [];

		for (var i = 0; i < data.length; i++) {
			completed_data.push(data[i].completed);
			overdue_data.push(data[i].overdue);
			pending_data.push(data[i].pending);
			x_labels.push(data[i].date);
		}

		var complete_tasks = {
	    	label: zpm_localized.strings.completed_tasks,
	        data: completed_data,
	        borderColor: "rgba(20, 170, 245, .7)",
	        backgroundColor: "rgba(20, 170, 245, .4)",
	        lineTension: '1',
	        fill: false
	    };

		var pending_tasks = {
	    	label: zpm_localized.strings.pending_tasks,
	        data: pending_data,
	        borderColor: "rgba(100, 48, 204, .7)",
	        backgroundColor: "rgba(100, 48, 204, .4)",
	        lineTension: '1',
	        fill: false
	    };

		var due_tasks = {
	    	label: zpm_localized.strings.due_tasks,
	        data: overdue_data,
	        borderColor: "rgba(250, 145, 145, .8)",
	        backgroundColor: "rgba(250, 145, 145, .8)",
	        lineTension: '1',
	        fill: false
	    };

	    var zpm_chart_data = {
	    	labels: x_labels,
		    datasets: [due_tasks, complete_tasks, pending_tasks]
		};
		 
		var chart_options = {
		  legend: {
		    position: 'bottom',
		  },
		  animation: {
		    animateRotate: false,
		    animateScale: true
		  },
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
			var line_chart = new Chart( zpm_progress_chart, {
			  type: 'line',
			  data: zpm_chart_data,
			  options: chart_options
			});
		}
	}

	ZephyrProjects.update_project_status = function(data, callback){
		data.action = 'zpm_update_project_status';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.error_updating_status );
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.project_status_saved );
				callback(response);
			}
		});
	}

	ZephyrProjects.getMembers = function( callback, args = {} ){

		if (zpm_members.length <= 0) {
			var data = {};
			data.action = 'zpm_getMembers';
			data.args = args;
			$.ajax({
				url: zpm_localized.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: data,
				error: function(response) {
					callback(response);
				},
				success: function(members) {
					zpm_members = members;
					members.forEach(function(member){
						zpm_members[member.id] = member;
					});
					callback(zpm_members);
				}
			});
		} else {
			callback(zpm_members);
		}
		
	}

	ZephyrProjects.Project.update_members = function(data, callback){
		data.action = 'zpm_update_project_members';
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				ZephyrProjects.notification( zpm_localized.strings.members_saved );
				callback(response);
			}
		});
	}

	ZephyrProjects.zpm_modal = function(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {
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

	// Initializes the dashboard charts
	ZephyrProjects.dashboard_charts = function() {
		$('.zpm-dashboard-project-chart').each(function(){
			var projectId = $(this).data('project-id');
			var chart = $(this)[0];

			ZephyrProjects.ajax({
				action: 'zpm_project_task_progress',
				project_id: projectId
			}, function(response){
				var zpm_progress_chart = chart;
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
		});
	}

	ZephyrProjects.Task.filterBy = function( data, callback ) {
		data.action = 'zpm_filter_tasks_by';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.updateUserAccess = function( data, callback ) {
		data.action = 'zpm_update_user_access';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.addTeam = function( data, callback ) {
		data.action = 'zpm_add_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.updateTeam = function( data, callback ) {
		data.action = 'zpm_update_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.getTeam = function( data, callback ) {
		data.action = 'zpm_get_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.deleteTeam = function( data, callback ) {
		data.action = 'zpm_delete_team';

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.confirm = function( message, confirmCallback ) {
		if ( confirm( message ) ) {
			confirmCallback();
		} else {

		}
	}

	ZephyrProjects.sendDesktopNotification = function( title, body, icon ) {
		if (!("Notification" in window)) {
		}
		else if (Notification.permission === "granted") {
			var options = {
			    body: body,
			    icon: icon,
			    dir : "ltr"
			};
			var notification = new Notification( title, options );
		}
		else if (Notification.permission !== 'denied') {
			Notification.requestPermission(function (permission) {
				if (!('permission' in Notification)) {
					Notification.permission = permission;
				}

				if (permission === "granted") {
					var options = {
					    body: body,
					    icon: icon,
					    dir : "ltr"
					};

					var notification = new Notification( title, options );
				}
			});
		}
	}

	ZephyrProjects.getPage = function() {
		var page = ZephyrProjects.getUrlParam("page");

		if (!ZephyrProjects.isAdmin()) {
			page = ZephyrProjects.getUrlParam("action");
		}
		
		return page;
	}

	ZephyrProjects.isProjectsPage = function() {
		var page = ZephyrProjects.getPage();
		if (page == "zephyr_project_manager_projects") {
			return true;
		}

		if (page == 'project') {
			return true;
		}
		return false;
	}

	ZephyrProjects.isCalendarPage = function() {
		var page = ZephyrProjects.getPage();
		if (page == "zephyr_project_manager_calendar") {
			return true;
		}
		return false;
	}

	ZephyrProjects.isReportsPage = function() {
		var page = ZephyrProjects.getPage();

		if (!ZephyrProjects.isAdmin()) {
			var action = ZephyrProjects.getUrlParam('action');
			if (action == 'reports') {
				return true;
			} else {
				return false;
			}
		} else {
			if (page == "zephyr_project_manager_reports") {
				return true;
			}
		}
		return false;
	}

	ZephyrProjects.isMembersPage = function() {
		var page = ZephyrProjects.getPage();
		if (page == "zephyr_project_manager_teams_members") {
			return true;
		}
		return false;
	}

	// Changes a project type
	ZephyrProjects.switch_project_type = function( data, callback ) {
		data.action = 'zpm_switch_project_type';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	ZephyrProjects.update_task_start_date = function( data, callback ) {
		data.action = 'zpm_update_task_start_date';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	ZephyrProjects.update_task_end_date = function( data, callback ) {
		data.action = 'zpm_update_task_end_date';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	ZephyrProjects.get_paginated_projects = function( data, callback ) {
		data.action = 'zpm_get_paginated_projects';
		data.zpm_nonce = zpm_localized.zpm_nonce;

		if ( projects_loading != null ) {
            projects_loading.abort();
            projects_loading = null;
        }

		projects_loading = $.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});	
	}

	ZephyrProjects.quick_input_modal = function( title, accept_btn, decline_btn, accept_callback, decline_callback ) {
		var unique_id = Math.floor(Math.random()*90000) + 10000;
		var modal_id = 'zpm-modal-' + unique_id;
		var accept_btn_id = 'zpm-accept-btn-' + unique_id;
		var decline_btn_id = 'zpm-decline-btn-' + unique_id;
		var input_id = 'zpm-modal-input-' + unique_id;

		var input_field = '<div class="zpm-form__group"><input type="text" name="zpm_category_name" id="' + input_id + '" class="zpm-form__field" placeholder="' + title + '"><label for="' + input_id + '" class="zpm-form__label">' + title + '</label></div>';

		var html = '<div id="' + modal_id + '" class="zpm-quick-input-modal zpm-modal"><h3 class="zpm-modal-title">' + title + '</h3>' + input_field + '<div class="zpm-modal-buttons"><button class="zpm_button zpm_cancel_button" id="' + decline_btn_id + '">' + decline_btn + '</button><button id="' + accept_btn_id + '" class="zpm_button">' + accept_btn + '</button></div></div>';
		$('body').append( html );

		ZephyrProjects.open_modal( modal_id );

		jQuery('body').on('click', '#' + accept_btn_id, function(){
			accept_callback(jQuery('body').find('#' + input_id).val());
			ZephyrProjects.close_modal('#' + modal_id);
		});

		jQuery('body').on('click', '#' + decline_btn_id, function(){
			decline_callback();
			ZephyrProjects.close_modal('#' + modal_id);
		});
	}

	ZephyrProjects.copy_to_clipboard = function( str ) {
		const el = document.createElement('textarea');
		el.value = str;
		document.body.appendChild(el);
		el.select();
		document.execCommand('copy');
		document.body.removeChild(el);
	}

	// General AJAX request with callback
	ZephyrProjects.ajax = function( data, callback ) {
		data.zpm_nonce = zpm_localized.zpm_nonce;
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			data: data,
			error: function(response, status, error) {
				if (!zpm_error_occurred) {
					// show error
					var message = 'One or more errors have occured on your website. To debug and fix this, please visit <a href="https://zephyr-one.com/debugging-errors/">the debugging and troubleshooting page</a>.';
					zpm_error_occurred = true;
					callback(response);
				}
				
				console.log('An error occurred when performing the request: ' + response.responseText);
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.ajaxFileUpload = function( data, callback ) {
		//data.zpm_nonce = zpm_localized.zpm_nonce;
		//data.action = 'zpm_uploadAjaxFile';
		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'post',
			dataType: 'json',
			contentType: false,
    		processData: false,
			cache: false,
			data: data,
			beforeSend: function(xhr) { 
				//xhr.setRequestHeader("Content-type","multipart/form-data; charset=utf-8; boundary=" + Math.random().toString().substr(2));
			    
			},
			error: function(response) {
				callback(response);
			},
			success: function(response) {
				callback(response);
			}
		});
	}

	ZephyrProjects.get_user_by_unique_id = function( id, callback ) {
		data = {};
		data.id = id;
		data.action = 'zpm_get_user_by_unique_id';
		ZephyrProjects.ajax( data, function(res) {
			callback(res);
		} );
	}

	ZephyrProjects.show_error = function( title, text, button_text ) {
		if (typeof button_text == "undefined") {
			button_text = "OK";
		}
		var html = "<div id='zpm-error-modal' class='zpm-modal zephyr-modal zephyr-error-modal' data-modal-action='remove'><h5 class='zpm-modal-header'>" + title + "</h5><p class='zpm-modal-text'>" + text + "</p><div class='zpm-modal-buttons'><button class='zpm-modal-dismiss-error zpm_button' data-zpm-trigger='remove_modal'>" + button_text + "</button></div></div>";	
		jQuery('body').append(html);
		ZephyrProjects.open_modal('zpm-error-modal');
	}

	ZephyrProjects.confirm_modal = function( title, text, button_text, callback ) {

		if (typeof button_text == "undefined") {
			button_text = "OK";
		}
		
		var random = Math.floor(Math.random()*90000) + 10000;
		var id = "zpm-confirm-modal-" + random;
		var accept_btn_id = "zpm-accept-confirm-btn-" + id;

		var html = "<div id='" + id + "' class='zpm-modal zephyr-modal zephyr-confirm-modal' data-modal-action='remove'><h5 class='zpm-modal-header'>" + title + "</h5><p class='zpm-modal-text'>" + text + "</p><div class='zpm-modal-buttons'><button class='zpm-modal-dismiss-error zpm_button' data-zpm-trigger='remove_modal'>" + zpm_localized.strings.cancel + "</button><button id='" + accept_btn_id + "' class='zpm_button' data-zpm-trigger='remove_modal'>" + button_text + "</button></div></div>";	
		jQuery('body').append(html);

		jQuery('body').on('click', '#' + accept_btn_id, function() {
			callback(true);
		});

		ZephyrProjects.open_modal(id);
	}

	ZephyrProjects.zephyrModal = function( title, text, button_text, callback, classes = '' ) {

		if (typeof button_text == "undefined") {
			button_text = "OK";
		}

		var random = Math.floor(Math.random()*90000) + 10000;
		var id = "zpm-confirm-modal-" + random;
		var accept_btn_id = "zpm-accept-confirm-btn-" + id;

		var html = "<div id='" + id + "' class='zpm-modal zephyr-modal zephyr-confirm-modal " + classes + "'><h5 class='zpm-modal-header'>" + title + "</h5><div class='zpm-modal-text'>" + text + "</div><div class='zpm-modal-buttons'><button class='zpm-modal-dismiss-error zpm_button' data-zpm-trigger='remove_modal'>" + zpm_localized.strings.cancel + "</button><button id='" + accept_btn_id + "' class='zpm_button'>" + button_text + "</button></div></div>";	
		jQuery('body').append(html);
		ZephyrProjects.initDatePickers();

		jQuery('body').on('click', '#' + accept_btn_id, function() {
			var $modal = $('body').find('#' + id);
			callback($modal);
			ZephyrProjects.close_modal('#' + id);
			$modal.remove();
		});

		ZephyrProjects.open_modal(id);
		$modal = $('body').find('#' + id);
		return $modal;
	}

	ZephyrProjects.loaderHtml = function() {
		var html = `<div id='" + preloaderId + "' class='zpm-modal-preloader'>\
			<div class='zpm-loader-holder'><div class='zpm-loader'></div></div>\
		</div>`;
		return html;
	}

	ZephyrProjects.zephyrAjaxModal = function(data, callback, classes = '') {

		var random = Math.floor(Math.random()*90000) + 10000;
		var id = "zpm-ajax-modal-" + random;
		var accept_btn_id = "zpm-accept-confirm-btn-" + id;
		var preloaderId = "zpm-modal-preloader-" + id;

		var html = "<div id='" + id + "' class='zpm-modal zephyr-modal zephyr-confirm-modal zephyr-ajax-modal " + classes + "'>\
			<div id='" + preloaderId + "' class='zpm-modal-preloader'>\
				<div class='zpm-loader-holder'><div class='zpm-loader'></div></div>\
			</div>\
		</div>";	
		jQuery('body').append(html);
		$modal = $('body').find('#' + id);

		$.ajax({
			url: zpm_localized.ajaxurl,
			type: 'POST',
			data: data,
			error: function(response) {
				$modal.html(response).addClass('loaded');
			},
			success: function(response) {
				$modal.html(response).addClass('loaded');
				ZephyrProjects.initDatePickers();
				$('body').on('click', '#' + id + ' .zpm-modal-accept-btn', function(){

					data.response = response;
					data.modal = $modal;
					callback(data);
				});

				$('body').on('click', '#' + id + ' .zpm-modal-cancel-btn', function(){
				});
			}
		});

		// jQuery('body').on('click', '#' + accept_btn_id, function() {
		// 	callback(true);
		// 	ZephyrProjects.close_modal('#' + id);
		// 	$('body').find('#' + id).remove();
		// });

		ZephyrProjects.open_modal(id);
	}

	ZephyrProjects.is_image = function( url ) {
		if (/(jpg|gif|png|JPG|GIF|PNG|JPEG|jpeg)$/.test( url )){ 
			return true;
		} else {
			return false;
		}
	}

	ZephyrProjects.inputHtml = function(label, id, classes) {
		if (typeof classes == 'undefined') {
			classes = '';
		}
		var html = '<div class="zpm-form__group ' + id + '-field">\
				<input type="text" name="' + id + '" id="' + id + '" class="zpm-form__field ' + classes + '" placeholder="' + label + '" autocomplete="off">\
				<label for="' + id + '" class="zpm-form__label">' + label + '</label>\
			</div>';
		return html;
	}

	ZephyrProjects.textareaHtml = function(label, id) {
		var html = '<div class="zpm-form__group ' + id + '-field">\
				<textarea name="' + id + '" id="' + id + '" class="zpm-form__field" placeholder="' + label + '" autocomplete="off"></textarea>\
				<label for="' + id + '" class="zpm-form__label">' + label + '</label>\
			</div>';
		return html;
	}

	ZephyrProjects.multiSelectHtml = function(label, options, id) {
		var html = '<select id="' + id + '" multiple="true" data-placeholder="' + label + '">';
		for (var i = 0; i < options.length; i++) {
			var option = options[i];
			var selected = option.selected == true ? 'selected' : '';
			html = html + '<option ' + selected + ' value="' + option.value + '">' + option.text + '</option>';
		}
		html = html + '</select>';
		return html;
	}

	ZephyrProjects.is_pdf = function( url ) {
		if (/(pdf)$/.test( url )){ 
			return true;
		} else {
			return false;
		}
	}

	ZephyrProjects.is_pdf = function( url ) {
		if (/(pdf)$/.test( url )){ 
			return true;
		} else {
			return false;
		}
	}


	ZephyrProjects.upload_new_task_attachments = function( task_id, attachments ) {
		
		var data = {
			user_id: zpm_localized.user_id,
			subject: 'task',
			subject_id: task_id,
			message: '',
			type: 'message',
			attachments: attachments,
			send_email: false
		};

		ZephyrProjects.send_comment(data, function(response) {
			response.id = task_id;
			response.user_id = zpm_localized.user_id;
			response.subject = 'task';
			response.type = data.type;
		});
	}

	ZephyrProjects.upload_file = function( uploader, callback, multiple = false, mediaUploader = true ) {

		if (!ZephyrProjects.isAdmin() && zpm_localized.settings['view_own_files'] || !mediaUploader) {
			// show default uploads
			jQuery('body').append('<input type="file" id="zpm-file-upload__input" style="display: none;" />');
			var fileUploader = jQuery('body').find('#zpm-file-upload__input');
			fileUploader.click();
			fileUploader.on('change', function(evt){
				var files = evt.target.files;
				var file = files[0];
				var formData = new FormData();
				formData.append("file", file);
				formData.append('action', 'zpm_uploadAjaxFile');

				var attachment_holder = $('body').find('#zpm-chat-attachments');
				attachment_holder.append('<span data-attachment-id="' + file.name + '" class="zpm-comment-attachment">Uploading...<span class="zpm-remove-attachment lnr lnr-cross"></span></span>');
				ZephyrProjects.ajaxFileUpload(formData, function(res){
					callback(res);
					var filePreview = $('body').find('[data-attachment-id="' + file.name + '"]');
					filePreview.text(res.filename);
					filePreview.attr('data-attachment-id', res.url);
					fileUploader.remove();
				} );
			});
			
		} else {
			if ( typeof multiple == "undefined" ) {
				multiple = false;
			}

			if (uploader) {
				uploader.open();
				return;
			} else {
				var uploader = null;
			}

			uploader = wp.media.frames.file_frame = wp.media({
				title: 'Files',
				button: {
				text: 'Upload Files'
			}, multiple: multiple });

			uploader.on('select', function() {
				var attachment = uploader.state().get('selection').first().toJSON();
				if (multiple) {
					var attachments = uploader.state().get('selection').map( 
	                function( attachment ) {
		                    attachment.toJSON();
		                    return attachment;
		            });
		            callback(attachments);
				} else {
					callback(attachment);
				}
				
			});

			// Open the uploader dialog
			uploader.open();
		}	
	}

	ZephyrProjects.readFile = function( uploader, callback ) {
		// show default uploads
		jQuery('body').append('<input type="file" id="zpm-file-upload__input" style="display: none;" />');
		var fileUploader = jQuery('body').find('#zpm-file-upload__input');
		fileUploader.click();
		fileUploader.on('change', function(evt){
			var reader = new FileReader();
	       
			var files = evt.target.files;
			var file = files[0];

			if (file.type == "") {
				file.type = "text/csv";
			}

			console.log(file);

			reader.onload = function onReaderLoad(event){
				console.log('READ FILE');
				if (file.type == 'application/json') {
		        	var obj = JSON.parse(event.target.result);
		        	callback(obj);
				} else {
					var allText = event.target.result
			        var allTextLines = allText.split(/\r\n|\n/);
				    var headers = allTextLines[0].split(',');
				    var lines = [];
						headers = headers.map((item) => {
							return item.replace("\"", "");
						});

				    for (var i=1; i<allTextLines.length; i++) {
				        var data = allTextLines[i].split(',');
				        var tarr = {};
								for (var j=0; j<headers.length; j++) {
									var headerName = headers[j];
									headerName = headerName.toLowerCase();
									headerName = headerName.replace(/ /g,"_");
									var value = data[j];

									if (typeof value !== 'undefined') {
										value = value.replace(/['"]+/g, '');
										headerName = headerName.replace(/['"]+/g, '');
										tarr[headerName] = value;
									} else {
										tarr[headerName] = '';
									}
								}

								console.log(tarr);
								lines.push(tarr);
				    }
				    callback(lines);
				}
		        
		    }

			reader.readAsText(file);
		});
	}

	ZephyrProjects.upload_attachment = function ( attachment_id, attachment_type, subject_id, callback ) {
		ZephyrProjects.notification( 'Uploading file...' );
		var attachment_type = (typeof attachment_type !== 'undefined') ? attachment_type : '';
		var subject_id = (typeof subject_id !== 'undefined') ? subject_id : '';

		var attachments = [{
			attachment_id: attachment_id,
			attachment_type: attachment_type,
			subject_id: subject_id
		}];
		
		ZephyrProjects.send_comment({
			attachments: attachments
		}, function(response){
			callback(response);
			$('body').find('.zpm_files_container').prepend(response.html);
			ZephyrProjects.notification( 'File uploaded.' );
		});
	}

	ZephyrProjects.filter_event = function(element, event) {
		var project = $('#zpm-calendar__filter-project').val();
		var team = $('#zpm-calendar__filter-team').val();
		var assignee = $('#zpm-calendar__filter-assignee').val();
		element.css('display: none');
		if (project == "all" && team == "all" && assignee == "all") {
			element.css('display', 'block');
		}

		if (project == event.project_id) {
			element.css('display', 'block');
		}
		if (assignee == event.assignee) {
			element.css('display', 'block');
		}
		if (team == event.team) {
			element.css('display', 'block');
		}
	}

	ZephyrProjects.paginateProjects = function( page, limit, frontend = false ) {
		$('#zpm-project-count__current').text(page);
		ZephyrProjects.addParameterToURL('projects_page', page);
		category_id = ZephyrProjects.getUrlParam('category_id');
		var data = {
			page: page,
			limit: limit,
			category_id: category_id
		};

		if (frontend == true) {
			data.frontend = frontend;
		}
		ZephyrProjects.get_paginated_projects( data, function(response){
			$('#zpm-project-list').html(response.html);
			$('#zpm_project_list .zpm_project_grid').html(response.html);
			ZephyrProjects.refreshProjectsProgressBar();
		});
	}

	ZephyrProjects.addParameterToURL = function( param, value ){
	    var url = new URL(window.location.href);
		var query_string = url.search;
		var search_params = new URLSearchParams(query_string); 
		search_params.set(param, value);
		url.search = search_params.toString();
		var new_url = url.toString();
		var html = $('body').html();
		var title = zpm_localized.strings.projects + ' | ' + zpm_localized.strings.page + ' ' + value;
	    document.title = title;
	    window.history.pushState(title, title, new_url);
	}

	ZephyrProjects.getUrlParam = function( param ) {
		var url_string = window.location.href;
		var url = new URL(url_string);
		var param = url.searchParams.get(param);
		return param;
	}

	ZephyrProjects.refreshProjectsProgressBar = function() {
		$('.zpm_project_progress_bar').each( function() {
			var total_tasks = $(this).data('total_tasks');
			var completed_tasks = $(this).data('completed_tasks')
			var width = (total_tasks !== 0) ? ((completed_tasks / total_tasks) * 100) : 0;
			$(this).css('width', width + '%');
		});
	}

	ZephyrProjects.updateUserMeta = function( userId, key, value ) {
		var data = {
			action: 'zpm_update_user_meta',
			key: key,
			value: value,
			user_id: userId
		}
		ZephyrProjects.ajax(data, function(response){
		});
	}

	ZephyrProjects.newModal = function(subject, header, content, buttons, modal_id, task_id, options, project_id, navigation) {
		var modal_navigation = (typeof navigation !== 'undefined' && navigation !== '') ? navigation : '';
		var modal_settings = (typeof options !== 'undefined' && options !== '') ? '<span class="zpm_modal_options_btn" data-dropdown-id="zpm_view_task_dropdown"><i class="dashicons dashicons-menu"></i>' + options + '</span>' : '';
		var modal = '<div id="' + modal_id + '" class="zpm-modal" data-task-id="' + task_id + '" data-project-id="' + project_id + '">' +
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

	ZephyrProjects.initDatePickers = function() {
		$('body').find('.zpm-datepicker').datepicker({dateFormat: 'yy-mm-dd' });
	}

	ZephyrProjects.initChosen = function() {
		$('body').find('.zpm-chosen').chosen({
			disable_search_threshold: 10,
		    no_results_text: "No Results",
		});
	}

	ZephyrProjects.removeFromArray = function(arr, val) {
		for( var i = 0; i < arr.length; i++){ 
		   if ( arr[i] === val) {
		     arr.splice(i, 1); 
		   }
		}
		return arr;
	}

	ZephyrProjects.openTaskPanel = function(task_id, task_name, description, task_url, assignees){
		$('#zpm-task-preview__action-btn').attr('href', task_url);
		$('#zpm-task-preview__title').text(task_name);
		$('#zpm-task-preview__description').text(description);
		// $('.zpm-task-preview__name').text(task_name);
		// $('.zpm-task-preview__description').text(description);
		// $('.zpm-task-preview__assignee').html(assignees);
		// $('.zpm-task-preview__due-date').text('');
		$('#zpm-task-preview__bar').addClass('zpm-task-preview__active');
		// $('#zpm-task-preview__subtasks').html('').removeClass('zpm-no-subtasks');
		$('#zpm-task-preview__loader').show();
		//$('#zpm-task-preview__custom-fields').html('');
		$('#zpm-task-preview__info').animate({
	        scrollTop: 0
	    }, 200);
	    // $('#zpm-task-preview__priority').hide();
	    // $('.zpm-task-preview__priority').hide();
	    // $('#zpm-task-preview__recurrence').hide();
	    // $('#zpm-task-preview-section__files').hide();
	    // $('#zpm-task-preview__extra').hide();
	    // $('#zpm-task-preview__files').html('');
	    $('#zpm-task-preview__apply-task').addClass('zpm-element__hidden');

	    ZephyrProjects.getTaskPanelHTML( task_id, function(html){
	    	jQuery('body').find('#zpm-task-preview__info').html(html);
	    	$('#zpm-task-preview__loader').hide();
	    	ZephyrProjects.initDatePickers();
	    	ZephyrProjects.initChosen();
	    	ZephyrProjects.mentionInput('#zpm_chat_message');
	    });

		// ZephyrProjects.get_task({
		// 	task_id:task_id
		// }, function(res){
		// 	var description = $.trim(res.description) !== '' ? res.description : zpm_localized.strings.no_description;
		// 	$('#zpm-task-preview__description').text(description);
		// 	$('.zpm-task-preview__description').text(description);

		// 	$('#zpm-task-preview__apply-task').data('task-id', res.id);
		// 	$('.zpm-open-task-edit-modal').data('task-id', res.id);
		// 	var priority = res.formatted_priority;
		// 	if ( res.priority == 'priority_none' || res.priority == '' ) {
		// 		$('#zpm-task-preview__priority').hide();
		// 	} else {
		// 		$('.zpm-task-preview__priority').show();
		// 		$('#zpm-task-preview__priority').text(priority.name).css('background-color', priority.color).show();
		// 	}

		// 	$('.zpm-task-preview__start-date').text(res.formatted_start_date);
		// 	$('.zpm-task-preview__due-date').text(res.formatted_due_date);
		// 	$('#zpm-task-preview__loader').hide();
		// 	$('#zpm-task-preview__subtasks').html('');

		// 	res.subtasks.forEach( function(subtask){
		// 		var atts = subtask.completed == '1' ? 'checked' : '';
		// 		var checkbox = '<label for="zpm-subtask-id-' + subtask.id + '" class="zpm-material-checkbox">' +
		// 			'<input type="checkbox" id="zpm-subtask-id-' + subtask.id + '" name="zpm-subtask-id-' + subtask.id + '" class="zpm_subtask_is_done zpm_toggle invisible" value="1" ' + atts + ' data-task-id="' + subtask.id + '">' +
		// 			'<span class="zpm-material-checkbox-label"></span>' +
		// 		'</label>';
		// 		var html = '<div class="zpm-task-preview__subtask">' + checkbox + subtask.name + '</div>';
		// 		$('#zpm-task-preview__subtasks').append(html).addClass('zpm-active');
		// 	});

		// 	if (res.subtasks.length <= 0) {
		// 		$('#zpm-task-preview__subtasks').html(zpm_localized.strings.no_subtasks).addClass('zpm-no-subtasks');
		// 	}

		// 	res.custom_fields.forEach( function(custom_field) {
		// 		var html = '<label class="zpm-task-preview__label zpm-task-preview__section-content">' + custom_field.name + '</label>' +
		// 				'<p class="zpm-task-preview__label-value">' + ZephyrProjects.parseLinks(custom_field.field_value) + '</p>';
		// 		$('#zpm-task-preview__custom-fields').append(html);
		// 	});

		// 	if (res.recurrence !== '') {
		// 		$('#zpm-task-preview__recurrence').show();
		// 		$('#zpm-task-preview__recurrence-value').text(res.recurrence);
		// 	}

		// 	if (res.attachments.length > 0) {
		// 		$('#zpm-task-preview-section__files').show();
		// 		res.attachments.forEach( function(attachment) {
		// 			$('#zpm-task-preview__files').append(attachment.html);
		// 		});
		// 	}

		// 	if (typeof res.extra !== 'undefined' && res.extra !== '') {
		// 		$('#zpm-task-preview__extra').show();
		// 		$('#zpm-task-preview__extra').html(res.extra);
		// 	}

		// 	if (!res.hasApplied) {
		// 		$('#zpm-task-preview__apply-task').removeClass('zpm-element__hidden');
		// 	}
		// });
	}

	ZephyrProjects.saveTaskPanel = function() {
		var data = ZephyrProjects.getAjaxData(jQuery('#zpm-task-preview__info'));

		var custom_fields = [];
		var requiredCustomField = false;
		$('body').find('#zpm-task-preview__info .zpm_task_custom_field').each(function(){
		    var id = $(this).data('zpm-cf-id');
		    var type = $(this).data('zpm-cf-type');
		    var value = $(this).val();
		    var required = $(this).data('cf-required');

		    if (type == "checkbox") {
		        value = $(this).is(':checked');
		    } else {
		        if (required && value == '') {
		            requiredCustomField = true;
		            $(this).addClass('.zpm-cf-required');
		        }
		    }

		    custom_fields.push({
		        id: id,
		        value: value
		    });
		});

		if (requiredCustomField) {
		    ZephyrProjects.notification('Custom Field Required');
		}

		data.task_custom_fields = custom_fields;
		jQuery('#zpm-task-preview__title').text(data.task_name);
		ZephyrProjects.update_task(data, function(response){
		});

		var task = jQuery('body').find('.zpm_task_list_row[data-task-id="' + data.task_id + '"]');
		task.find('.zpm_task_description').text(' - ' + data.task_description);
	}

	ZephyrProjects.saveProjectPanel = function() {
		var data = ZephyrProjects.getAjaxData(jQuery('#zpm-project-preview__info'));

		var custom_fields = [];
		var requiredCustomField = false;
		$('body').find('#zpm-project-preview__info .zpm_task_custom_field').each(function(){
		    var id = $(this).data('zpm-cf-id');
		    var type = $(this).data('zpm-cf-type');
		    var value = $(this).val();
		    var required = $(this).data('cf-required');

		    if (type == "checkbox") {
		        value = $(this).is(':checked');
		    } else {
		        if (required && value == '') {
		            requiredCustomField = true;
		            $(this).addClass('.zpm-cf-required');
		        }
		    }

		    custom_fields.push({
		        id: id,
		        value: value
		    });
		});

		if (requiredCustomField) {
		    ZephyrProjects.notification('Custom Field Required');
		}

		data.custom_fields = custom_fields;

		jQuery('#zpm-project-preview__title').text(data.project_name);
		ZephyrProjects.update_project(data, function(response){
		});

		var project = jQuery('body').find('.zpm_project_item[data-project-id="' + data.project_id + '"]');
		project.find('.zpm_project_grid_name').text(data.project_name);
		project.find('.zpm_project_description').text(data.project_description);
	}

	ZephyrProjects.openProjectPanel = function(project_id, name, description, url, categories){
		$('#zpm-project-preview__action-btn').attr('href', url);
		$('#zpm-project-preview__title').text(name);
		$('#zpm-project-preview__bar').addClass('zpm-project-preview__active');
		$('#zpm-project-preview__loader').show();

		 ZephyrProjects.getProjectPanelHTML( project_id, function(html){
	    	jQuery('body').find('#zpm-project-preview__info').html(html);
	    	$('#zpm-project-preview__loader').hide();
	    	ZephyrProjects.initDatePickers();
	    	ZephyrProjects.initChosen();
	    	ZephyrProjects.mentionInput('#zpm_chat_message');
	    });
	}

	ZephyrProjects.closeProjectPanel = function() {
		jQuery('body').find('#zpm-project-preview__bar').removeClass('zpm-project-preview__active');
	}

	ZephyrProjects.closeTaskPanel = function() {
		jQuery('body').find('#zpm-task-preview__bar').removeClass('zpm-task-preview__active');
	}

	ZephyrProjects.getTaskPanelHTML = function(id, callback) {
		ZephyrProjects.ajax({
			action: 'zpm_getTaskPanelHTML',
			id: id
		}, function(data){
			callback(data.html);
		});
	}

	ZephyrProjects.getProjectPanelHTML = function(id, callback) {
		ZephyrProjects.ajax({
			action: 'zpm_getProjectPanelHTML',
			id: id
		}, function(data){
			callback(data.html);
		});
	}

	ZephyrProjects.parseLinks = function(string) {
		if (string.indexOf('https') !== '-1') {
			var string = string.replace(/(https:\/\/[^\s]+)/g, "<a href='$1'>$1</a>");
		} else {
			var string = string.replace(/(http:\/\/[^\s]+)/g, "<a href='$1'>$1</a>");
		}
		return string;
	}

	ZephyrProjects.isAdmin = function() {
		$body = $('body');
		if ($body.hasClass('wp-admin')) {
			return true;
		} else {
			return false;
		}
	}

	ZephyrProjects.updateTaskStatus = function(taskId, status, callback) {
		var data = {
			task_id: taskId,
			status: status,
			action: 'zpm_updateTaskStatus'
		};

		ZephyrProjects.ajax(data, function(response){
			callback(response);
		});
	}

	ZephyrProjects.showFullPageLoader = function() {
		var html = '<div class="zpm-full-page-loader">\
			<div class="zpm-full-page-loader__bg"></div>\
			<div class="sk-cube-grid">\
			  <div class="sk-cube sk-cube1"></div>\
			  <div class="sk-cube sk-cube2"></div>\
			  <div class="sk-cube sk-cube3"></div>\
			  <div class="sk-cube sk-cube4"></div>\
			  <div class="sk-cube sk-cube5"></div>\
			  <div class="sk-cube sk-cube6"></div>\
			  <div class="sk-cube sk-cube7"></div>\
			  <div class="sk-cube sk-cube8"></div>\
			  <div class="sk-cube sk-cube9"></div>\
			</div>\
		</div>';
		$('body').append(html);

		setTimeout(function(){
			ZephyrProjects.dismissFullPageLoader();
		}, 15000);
	}

	ZephyrProjects.dismissFullPageLoader = function() {
		$('body').find('.zpm-full-page-loader').remove();
	}

	ZephyrProjects.linkify = function(inputText) {
		var replacedText, replacePattern1, replacePattern2, replacePattern3;

	    replacePattern1 = /(\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/gim;
	    replacedText = inputText.replace(replacePattern1, '<a href="$1" target="_blank">$1</a>');

	    replacePattern2 = /(^|[^\/])(www\.[\S]+(\b|$))/gim;
	    replacedText = replacedText.replace(replacePattern2, '$1<a href="http://$2" target="_blank">$2</a>');

	    replacePattern3 = /(([a-zA-Z0-9\-\_\.])+@[a-zA-Z\_]+?(\.[a-zA-Z]{2,6})+)/gim;
	    replacedText = replacedText.replace(replacePattern3, '<a href="mailto:$1">$1</a>');

	    return inputText;
	}

	ZephyrProjects.isMobile = function() {
		var check = false;
		(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);
		return check;
	}

	ZephyrProjects.shouldShowCalendarEvent = function(event) {
		return true;
	}

	ZephyrProjects.sendEmail = function(userId, header, subject, body, footer) {
		ZephyrProjects.ajax({
			action: 'zpm_sendEmail',
			user_id: userId,
			header: header,
			subject: subject,
			body: body,
			footer: footer
		}, function(res){
		})
	}

	ZephyrProjects.getAjaxData = function(form) {
		var data = {};
		form.find('[data-ajax-name]').each(function(){
			var name = $(this).data('ajax-name');
			var value = $(this).val();
			var type = jQuery(this).attr('type');
			

			if (type == 'checkbox') {
				if (value == 'on') {
					value = 1;
				} else {
					value = 0;
				}
			}

			data[name] = value;
		});
		return data;
	}

	ZephyrProjects.memberPicker = function(data, callback) {

		var modal = ZephyrProjects.zephyrModal( 'Select Member', ZephyrProjects.loaderHtml(), 'Select User', function(){}, 'zpm-member-picker' );

		ZephyrProjects.getMembers(function(members){
			modal.html('<div class="zpm-member-picker__container zpm-list"></div>');
			var memberContainer = modal.find('.zpm-member-picker__container');

			members.forEach(function(member){

				if (typeof data.exlude !== 'undefined' && jQuery.inArray(member.id, data.exlude) !== -1) {
					return;
				}
				if (data.multiple) {
					memberContainer.append('<div class="zpm-list__item zpm-member-picker__item" data-member-id="' + member.id + '"><input type="checkbox" class="zpm-member-picker__member-checkbox" data-member="' + member.id + '" />' + member.name + ' (' + member.email + ')</div>');
				} else {
					memberContainer.append('<div class="zpm-list__item zpm-member-picker__item" data-member-id="' + member.id + '">' + member.name + ' (' + member.email + ')</div>');
				}
			});


			if (typeof data.buttons !== 'undefined') {
				var btnText = data.buttonText ? data.buttonText : 'Select';
				var buttons = `
				<div class="zpm-modal-buttons zpm-member-picker__buttons">`;
				data.buttons.forEach(function(button){
					buttons += `<button id="${button.id}" class="zpm_button">${button.text}</button>`;
					jQuery('body').on('click', '#' + button.id, function(){
						var members = [];

						jQuery(modal).find('.zpm-member-picker__member-checkbox').each(function(){
							if (jQuery(this).is(':checked')) {
								var memberId = jQuery(this).data('member');
								members.push(zpm_members[memberId]);
							}
						});
						button.callback(members);
						ZephyrProjects.remove_modal('.zpm-member-picker');

						
					});
				});
				buttons += `</div>`;
				modal.append(buttons);
			} else if (data.multiple) {
				var btnText = data.buttonText ? data.buttonText : 'Select';
				var buttons = `
				<div class="zpm-modal-buttons zpm-member-picker__buttons">
					<button id="zpm-member-picker__submit-btn" class="zpm_button">${btnText}</button>
				</div>`;
				modal.append(buttons);
			}
			
		}, data );

		if (!data.multiple) {
			jQuery(modal).on('click', '.zpm-list__item', function(){
				var id = jQuery(this).data('member-id');
				callback(zpm_members[id]);
				ZephyrProjects.remove_modal('.zpm-member-picker');
			});
		} else {
			var members = [];
			jQuery(modal).on('click', '#zpm-member-picker__submit-btn', function(){
				jQuery(modal).find('.zpm-member-picker__member-checkbox').each(function(){
					if (jQuery(this).is(':checked')) {
						var memberId = jQuery(this).data('member');
						members.push(zpm_members[memberId]);
					}
				});
				callback(members);
				ZephyrProjects.remove_modal('.zpm-member-picker');
			});
		}
	}

	ZephyrProjects.projectImporter = function( callback ) {

		var uploader = null;
		ZephyrProjects.readFile(uploader, function( projects, error = false ){

			var modal = ZephyrProjects.zephyrModal( 'Projects to Import', ZephyrProjects.loaderHtml(), 'Import Projects', function(){}, 'zpm-project-importer' );
			modal.html('<div class="zpm-project-importer__container zpm-list"></div>');

			if (error) {
				modal.html('<p class="zpm-modal-error">Unsupported file type. Please select a JSON or CSV file.</p>');
				return false;
			}

			var projectContainer = modal.find('.zpm-project-importer__container');
			var results = [];

			if (!Array.isArray(projects)) {
				results.push(projects);
			} else {
				results = projects;
			}

			results.forEach(function(project){
				var html = `<div class="zpm-list__item zpm-project-importer__item" data-id="${project.id}">${project.name} <span class="zpm-project-importer__description">${project.description}</span></div>`;
				projectContainer.append(html);
			});

			var buttons = `
			<div class="zpm-modal-buttons zpm-member-picker__buttons">
				<button id="zpm-project-importer__submit-btn" class="zpm_button">Import Projects</button>
			</div>`;
			modal.append(buttons);

			jQuery(modal).on('click', '#zpm-project-importer__submit-btn', function(){
				jQuery(this).text('Importing...');
				//ZephyrProjects.remove_modal('.zpm-project-importer');
				ZephyrProjects.ajax({
					action: 'zpm_saveProjects',
					projects: results
				}, function(res){
					window.location.reload();
				});
			});

		});

		
	}

	// Task Importer
	ZephyrProjects.taskImporter = function( callback ) {

		var uploader = null;
		ZephyrProjects.readFile(uploader, function( tasks, error = false ){

			var modal = ZephyrProjects.zephyrModal( 'Tasks to Import', ZephyrProjects.loaderHtml(), 'Import Tasks', function(){}, 'zpm-task-importer' );
			modal.html('<div class="zpm-task-importer__container zpm-list"></div>');

			if (error) {
				modal.html('<p class="zpm-modal-error">Unsupported file type. Please select a JSON or CSV file.</p>');
				return false;
			}

			var taskContainer = modal.find('.zpm-task-importer__container');
			var results = [];
			if (!Array.isArray(tasks)) {
				results.push(tasks);
			} else {
				results = tasks;
			}

			results.forEach(function(task){
				var html = `<div class="zpm-list__item zpm-task-importer__item" data-id="${task.id}">${task.name} <span class="zpm-project-importer__description">${task.description}</span></div>`;
				taskContainer.append(html);
			});

			var buttons = `
			<div class="zpm-modal-buttons zpm-task-importer__buttons">
				<button id="zpm-task-importer__submit-btn" class="zpm_button">Import Tasks</button>
			</div>`;
			modal.append(buttons);

			jQuery(modal).on('click', '#zpm-task-importer__submit-btn', function(){
				jQuery(this).text('Importing...');
				//ZephyrProjects.remove_modal('.zpm-project-importer');
				ZephyrProjects.ajax({
					action: 'zpm_saveTasks',
					tasks: results
				}, function(res){
					window.location.reload();
				});
			});
		});
	}

	ZephyrProjects.projectImporterAjax = function( callback ) {

		var csvFileUploader;
		if (csvFileUploader) {
			csvFileUploader.open();
			return;
		}

		csvFileUploader = wp.media.frames.file_frame = wp.media({
			title: zpm_localized.strings.choose_file,
			button: {
			text: zpm_localized.strings.choose_file
		}, multiple: false });
	  
		csvFileUploader.on('select', function() {
			var attachment = csvFileUploader.state().get('selection').first().toJSON();
			var modal = ZephyrProjects.zephyrModal( 'Projects to Import', ZephyrProjects.loaderHtml(), 'Import Projects', function(){}, 'zpm-project-importer' );

			if (attachment.mime == 'text/csv') {
				ZephyrProjects.ajax({
					action: 'zpm_loadProjectsFromCSV',
					file: attachment.url
				}, function(projects){
					modal.html('<div class="zpm-project-importer__container zpm-list"></div>');
					var projectContainer = modal.find('.zpm-project-importer__container');
					projects.forEach(function(project){
						var html = `<div class="zpm-list__item zpm-project-importer__item" data-id="${project.id}">${project.name} <span class="zpm-project-importer__description">${project.description}</span></div>`;
						projectContainer.append(html);
					});

					var buttons = `
					<div class="zpm-modal-buttons zpm-member-picker__buttons">
						<button id="zpm-project-importer__submit-btn" class="zpm_button">Import Projects</button>
					</div>`;
					modal.append(buttons);

					jQuery(modal).on('click', '#zpm-project-importer__submit-btn', function(){
						jQuery(this).text('Importing...');
						//ZephyrProjects.remove_modal('.zpm-project-importer');
						ZephyrProjects.ajax({
							action: 'zpm_saveProjects',
							projects: projects
						}, function(res){
							window.location.reload();
						});
					});
				});
			} else if (attachment.mime == 'text/json' || attachment.mime == 'application/json') {
				ZephyrProjects.ajax({
					action: 'zpm_loadProjectsFromJSON',
					file: attachment.url
				}, function(projects){
					modal.html('<div class="zpm-project-importer__container zpm-list"></div>');
					var projectContainer = modal.find('.zpm-project-importer__container');
					projects.forEach(function(project){
						var html = `<div class="zpm-list__item zpm-project-importer__item" data-id="${project.id}">${project.name} <span class="zpm-project-importer__description">${project.description}</span></div>`;
						projectContainer.append(html);
					});

					var buttons = `
					<div class="zpm-modal-buttons zpm-member-picker__buttons">
						<button id="zpm-project-importer__submit-btn" class="zpm_button">Import Projects</button>
					</div>`;
					modal.append(buttons);

					jQuery(modal).on('click', '#zpm-project-importer__submit-btn', function(){
						jQuery(this).text('Importing...');
						//ZephyrProjects.remove_modal('.zpm-project-importer');
						ZephyrProjects.ajax({
							action: 'zpm_saveProjects',
							projects: projects
						}, function(res){
							window.location.reload();
						});
					});
				});
			} else {
				modal.html('<p class="zpm-modal-error">Unsupported file type. Please select a JSON or CSV file.</p>');
			}
		});

		// Open the uploader dialog
		csvFileUploader.open();
	}


	ZephyrProjects.containsString = function(string, substring) {
		if (string.indexOf(substring) !== -1) {
			return true;
		} else {
			return false;
		}
	}

	ZephyrProjects.getDateFormat = function() {
		var format = 'yy-mm-dd';
		var selectedFormat = zpm_localized.settings.date_format;
		if (selectedFormat == 'F j, Y') {
			format = 'MM dd, yy';
		}

		if (selectedFormat == 'M/D/Y') {
			format = 'MM/DD/yy';
		}

		return format;
	}

	ZephyrProjects.createPDF = function( element, filename, done ) {

		var options = {
			margin: 0.5,
			filename: filename + '.pdf',
			image: { type: 'jpeg', quality: 0.98 },
			html2canvas: { dpi: 150, letterRendering: true },
			jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
		}

		setTimeout(function(){
			html2pdf(element, options);
			done();
		},500);
	}

	ZephyrProjects.mentionInput = function( selector ) {
		jQuery('body').find(selector).mentionsInput({
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
	}

	ZephyrProjects.getFormData = function( form, isFormData = false ) {
		var data = {};

		if (isFormData) {
			data = new FormData();
		}

		form.find('[name]').each(function(i, e){
			var element = jQuery(this);
			var key = jQuery(this).attr('name');
			var value = jQuery(this).val();
			var type = jQuery(this).attr('type');

			if (type == 'file') {
				value = element.prop('files')[0];
			}

			if (type == 'checkbox') {
				value = jQuery(this).is(':checked');
			}

			if (isFormData) {
				data.append(key, value);
			} else {
				data[key] = value;
			}
			
		});
		return data;
	}

	ZephyrProjects.resetForm = function(form) {
		form.find('[name]').each(function(){
			jQuery(this).val('');
		});
	}

})(jQuery)