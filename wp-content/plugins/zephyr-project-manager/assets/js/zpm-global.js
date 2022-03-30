jQuery(document).ready(function(){

	if (ZephyrProjects.isProjectsPage()) {
		$ = jQuery;
		var selector = 'zpm-task-list__project';

		if (!ZephyrProjects.isAdmin()) {
			selector = 'zpm-project-task-list';
		}

		var drake = dragula({
			copySortSource: true
		});

		var container =  document.getElementById(selector);
		drake.containers.push(container);
		var projectId = jQuery('#zpm-project-id').val();
		drake.on('dragend', function(one, two, three){
			var taskIdsOrder = [];
			
			var parentList = jQuery('#' + selector);
			parentList.find('.zpm_task_list_row').each(function(){
				console.log($(this));
				var taskId = $(this).data('task-id');
				taskIdsOrder.push(taskId);
			});

			ZephyrProjects.ajax({
				action: 'zpm_updateProjectSetting',
				project_id: projectId,
				key: 'task_order',
				value: taskIdsOrder
		    }, function(res){

		    });
		});
	}

	jQuery('body').on('click', '.zpm-remove-parent', function(){
		jQuery(this).parent().remove();
	});

	// Remove project from dashboard
	jQuery('body').on('click','#zpm-remove-from-dashboard', function(e){
		e.stopPropagation();
		e.preventDefault();
		var parent = jQuery(this).closest('.zpm_project_item');
		var projectId = parent.data('project-id');
		parent.closest('.zpm_project_grid_cell').remove();
		ZephyrProjects.ajax({
			action: 'zpm_removeProjectFromDashboard',
			id: projectId
		}, function(){})
	});

	jQuery('#zpm-task-preview__info').on('change', 'input, select, textarea', function(){
		ZephyrProjects.saveTaskPanel();
		ZephyrProjects.notification('Changes Saved');
	});

	jQuery('#zpm-project-preview__info').on('change', 'input, select, textarea', function(){
		if (!jQuery(this).hasClass('zpm-panel__no-update')) {
			ZephyrProjects.saveProjectPanel();
			ZephyrProjects.notification('Changes Saved');
		}
	});

	jQuery('body').on('click', function(e){
		var target = jQuery(e.target);
		if (target.closest('#zpm-project-preview__bar').length <= 0 && target.closest('.zpm_project_item').length <= 0){
			ZephyrProjects.closeProjectPanel();
		}

		if (target.closest('#zpm-task-preview__bar').length <= 0 && target.closest('.zpm_task_list_row').length <= 0){
			ZephyrProjects.closeTaskPanel();
		}
	});
});