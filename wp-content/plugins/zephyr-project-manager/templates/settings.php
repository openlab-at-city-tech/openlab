<?php
	/**
	* The Settings page
	*/
	if ( !defined( 'ABSPATH' ) ) {
		die;
	}

	use Inc\Zephyr;
	use Inc\Core\Members;
	use Inc\Core\Projects;
	use Inc\Core\Utillities;

	global $wpdb; 

	$current_user = wp_get_current_user();
	$user_id = $current_user->data->ID;
	$user_name = $current_user->data->display_name;
	$user_email = $current_user->data->user_email;
	$isAdmin = current_user_can( 'administrator' );

	// Delete all data if chosen
	if (isset($_POST['zpm-delete-all-data']) && $isAdmin) {
		$tables = Utillities::getTables();
		foreach($tables as $table) {
			Utillities::truncateTable($table);
		}
	}

	// Save Profile Settings
	if (isset($_POST['zpm_profile_settings'])) {
		check_admin_referer('zpm_save_project_settings');

		$name = (isset($_POST['zpm_settings_name']) && $_POST['zpm_settings_name'] !== '') ? sanitize_text_field($_POST['zpm_settings_name']) : $user_name;
		$profile_picture = isset($_POST['zpm_profile_picture']) ? sanitize_text_field($_POST['zpm_profile_picture']) : get_avatar_url($user_id);
		$description = isset($_POST['zpm_settings_description']) ? sanitize_textarea_field($_POST['zpm_settings_description']) : '';
		$email = (isset($_POST['zpm_settings_email']) && $_POST['zpm_settings_email'] !== '') ? sanitize_email($_POST['zpm_settings_email']) : $user_email;
		$notify_activity = isset($_POST['zpm_notify_activity']) ? 1 : '0';
		$notify_tasks = isset($_POST['zpm_notify_tasks']) ? 1 : '0';
		$notify_updates = isset($_POST['zpm_notify_updates'] ) ? 1 : '0';
		$notify_task_assigned = isset($_POST['zpm_notify_task_assigned'] ) ? 1 : '0';
		$hide_dashboard_widgets = isset($_POST['zpm-hide-dashboard-widgets'] ) ? true : false;
		$access_level = isset($_POST['zpm-access-level']) ? $_POST['zpm-access-level'] : 'edit_posts';
		$settings = array(
			'user_id' 		  		 => $user_id,
			'profile_picture' 		 => $profile_picture,
			'name' 			  		 => $name,
			'description' 	  		 => $description,
			'email' 		  		 => $email,
			'notify_activity' 		 => $notify_activity,
			'notify_tasks' 	  		 => $notify_tasks,
			'notify_updates'  		 => $notify_updates,
			'notify_task_assigned'   => $notify_task_assigned,
			'hide_dashboard_widgets' => $hide_dashboard_widgets,
		);
		update_option( 'zpm_user_' . $user_id . '_settings', $settings );
		update_option( 'zpm_access_settings', $access_level );
	}
	
	$user_settings_option = get_option('zpm_user_' . $user_id . '_settings');
	$general_settings = Utillities::general_settings();
	$access_settings = get_option('zpm_access_settings');
	$settings_profile_picture = (isset($user_settings_option['profile_picture'])) ? esc_url($user_settings_option['profile_picture']) : esc_url(get_avatar_url($user_id));
	$settings_name = (isset($user_settings_option['name'])) ? esc_html($user_settings_option['name']) : esc_html($user_name);
	$access_level = $access_settings ? $access_settings : 'edit_posts';
	$settings_description = isset($user_settings_option['description']) ? esc_textarea($user_settings_option['description']) : '';
	$settings_email = isset($user_settings_option['email']) ? esc_html($user_settings_option['email']) : esc_html($user_email);

	$settings_notify_activity = (isset($user_settings_option['notify_activity'])) ? $user_settings_option['notify_activity'] : '0';
	$settings_notify_tasks = (isset($user_settings_option['notify_tasks'])) ? $user_settings_option['notify_tasks'] : '0';
	$settings_notify_updates = (isset($user_settings_option['notify_updates'])) ? $user_settings_option['notify_updates'] : '0';
	$settings_notify_task_assigned = (isset($user_settings_option['notify_task_assigned'])) ? $user_settings_option['notify_task_assigned'] : '1';

	$settings_notifications['activity'] = $settings_notify_activity == '1' ? esc_attr('checked') : '';
	$settings_notifications['tasks'] = $settings_notify_tasks == '1' ? esc_attr('checked') : '';
	$settings_notifications['updates'] = $settings_notify_updates == '1' ? esc_attr('checked') : '';
	$settings_notifications['task_assigned'] = $settings_notify_task_assigned == '1' ? esc_attr('checked') : '';
	$action = isset($_GET['action']) ? $_GET['action'] : '';
	$selected = $action == 'profile' || $action == '' ? 'zpm_tab_selected' : '';
	
	$days_of_week = Utillities::getDaysOfWeek();
	$date_formats = Utillities::getDateFormats();

	$user_caps = Utillities::get_caps();
	$zpm_roles = Utillities::get_roles();
	$settingsPages = Zephyr::getSettingsPages();
	$users = Members::get_zephyr_members();
	$projects = Projects::get_projects();
?>

<main class="zpm_settings_wrap">
	<?php $this->get_header(); ?>
	<div id="zpm_container">
		<div class="zpm_body">
			<div class="tab-content">
				<div data-section="profile_settings" class="tab-pane active">
					<?php
						$tabs = '<h3 class="zpm_h3 zpm_tab_title ' . $selected . '" data-zpm-tab-trigger="1">' . __( 'Profile Settings', 'zephyr-project-manager' ) . '</h3><h3 class="zpm_h3 zpm_tab_title" data-zpm-tab-trigger="0">' . __( 'General Settings', 'zephyr-project-manager' ) . '</h3>';
						echo apply_filters( 'zpm_settings_tabs', $tabs);

						echo '<h3 class="zpm_h3 zpm_tab_title" data-zpm-tab-trigger="advanced">' . __( 'Advanced', 'zephyr-project-manager' ) . '</h3>';

						foreach ($settingsPages as $page) {
							?>
								<h3 class="zpm_h3 zpm_tab_title" data-zpm-tab-trigger="<?php echo $page['slug']; ?>"><?php echo $page['title']; ?></h3>
							<?php
						}
					?>

					<?php ob_start(); ?>

					<div class="zpm_tab_panel <?php echo $action == 'profile' || $action == '' ? 'zpm_tab_active' : ''; ?>" data-zpm-tab="1">
						<!-- Profile Settings -->
						<form id="zpm_profile_settings" method="post">
							<label class="zpm_label"><?php _e( 'Profile Picture', 'zephyr-project-manager' ); ?></label>
							<div class="zpm_settings_profile_picture">
								<span class="zpm_settings_profile_background"></span>
								<span class="zpm_settings_profile_image" style="background-image: url(<?php echo $settings_profile_picture; ?>);"></span>
							</div>

							<input type="hidden" id="zpm_profile_picture_hidden" name="zpm_profile_picture" value="<?php echo $settings_profile_picture; ?>" />
							<input type="hidden" id="zpm_gravatar" value="<?php echo get_avatar_url($user_id); ?>" />

							<div class="zpm-form__group">
								<input type="text" name="zpm_settings_name" id="zpm_settings_name" class="zpm-form__field" placeholder="<?php _e( 'Name', 'zephyr-project-manager' ); ?>" value="<?php echo $settings_name; ?>">
								<label for="zpm_settings_name" class="zpm-form__label"><?php _e( 'Name', 'zephyr-project-manager' ); ?></label>
							</div>

							<div class="zpm-form__group">
								<textarea type="text" name="zpm_settings_description" id="zpm_settings_description" class="zpm-form__field" placeholder="<?php _e( 'Description', 'zephyr-project-manager' ); ?>"><?php echo $settings_description; ?></textarea>
								<label for="zpm_settings_description" class="zpm-form__label"><?php _e( 'Description', 'zephyr-project-manager' ); ?></label>
							</div>

							<div class="zpm-form__group">
								<input type="text" name="zpm_settings_email" id="zpm_settings_email" class="zpm-form__field" placeholder="<?php _e( 'Email Address', 'zephyr-project-manager' ); ?>" value="<?php echo $settings_email; ?>">
								<label for="zpm_settings_email" class="zpm-form__label"><?php _e( 'Email Address', 'zephyr-project-manager' ); ?></label>
							</div>

							<?php do_action( 'zpm_settings_fields' ); ?>

							<label class="zpm_label"><?php _e( 'Hide WordPress Dashboard Widgets', 'zephyr-project-manager' ); ?></label>

							<label for="zpm-hide-dashboard-widgets" class="zpm-material-checkbox">
								<input type="checkbox" id="zpm-hide-dashboard-widgets" name="zpm-hide-dashboard-widgets" class="zpm_toggle invisible" value="1" <?php echo isset( $settings['hide_dashboard_widgets'] ) && $settings['hide_dashboard_widgets'] == true ? 'checked' : '';  ?>>
								<span class="zpm-material-checkbox-label"><?php _e( 'Hidden', 'zephyr-project-manager' ); ?></span>
							</label>

							<label class="zpm_label"><?php _e( 'Email Notifications', 'zephyr-project-manager' ); ?></label>

								<label for="zpm_notify_activity" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm_notify_activity" name="zpm_notify_activity" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['activity']; ?> >
									<span class="zpm-material-checkbox-label"><?php _e( 'All Activity', 'zephyr-project-manager' ); ?></span>
								</label>

								<label for="zpm_notify_tasks" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm_notify_tasks" name="zpm_notify_tasks" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['tasks']; ?> >
									<span class="zpm-material-checkbox-label"><?php _e( 'New Tasks', 'zephyr-project-manager' ); ?></span>
								</label>

								<label for="zpm_notify_task_assigned" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm_notify_task_assigned" name="zpm_notify_task_assigned" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['task_assigned']; ?> >
									<span class="zpm-material-checkbox-label"><?php _e( 'Task Assigned', 'zephyr-project-manager' ); ?></span>
								</label>

								<label for="zpm_notify_updates" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm_notify_updates" name="zpm_notify_updates" class="zpm_toggle invisible" value="1" <?php echo $settings_notifications['updates']; ?> >
									<span class="zpm-material-checkbox-label"><?php _e( 'Weekly Updates', 'zephyr-project-manager' ); ?></span>
								</label>
						
								<?php 
									if (current_user_can('administrator')) :
								?>
								<label class="zpm_label"><?php _e( 'Lowest Level of Access to Zephyr Project Manager', 'zephyr-project-manager' ); ?></label>
								<select id="zpm-access-level" class="zpm_input" name="zpm-access-level">
										<option value="manage_options" <?php echo $access_level == 'manage_options' ? 'selected' : ''; ?>><?php _e( 'Administrator', 'zephyr-project-manager' ); ?></option>
										<option value="edit_pages" <?php echo $access_level == 'edit_pages' ? 'selected' : ''; ?>><?php _e( 'Editor', 'zephyr-project-manager' ); ?></option>
										<option value="edit_published_posts" <?php echo $access_level == 'edit_published_posts' ? 'selected' : ''; ?>><?php _e( 'Author', 'zephyr-project-manager' ); ?></option>
										<option value="contributor" <?php echo $access_level == 'contributor' ? 'selected' : ''; ?>><?php _e( 'Contributor', 'zephyr-project-manager' ); ?></option>

										<option value="zpm_manager" <?php echo $access_level == 'zpm_admin' ? 'selected' : ''; ?>><?php _e( 'ZPM Administrator', 'zephyr-project-manager' ); ?></option>
										<option value="zpm_manager" <?php echo $access_level == 'zpm_manager' ? 'selected' : ''; ?>><?php _e( 'ZPM Manager', 'zephyr-project-manager' ); ?></option>

										<option value="zpm_user" <?php echo $access_level == 'zpm_view_project_manager' || $access_level == 'zpm_user'  ? 'selected' : ''; ?>><?php _e( 'ZPM User', 'zephyr-project-manager' ); ?></option>

										<option value="read" <?php echo $access_level == 'read' ? 'selected' : ''; ?>><?php _e( 'Subscriber', 'zephyr-project-manager' ); ?></option>
								</select>
								<?php
									endif;
								?>

								<input type="hidden" name="zpm_user_custom_fields" value="{name: 'jeff', surname: 'bob'}" />
								<?php wp_nonce_field('zpm_save_project_settings'); ?>

								<div id="zpm-profile-settings-buttons">
									<button type="submit" class="zpm_button" name="zpm_profile_settings" id="zpm_profile_settings"><?php _e( 'Save Settings', 'zephyr-project-manager' ); ?></button>

									<!-- <?php do_action( 'zpm_settings_buttons' ); ?> -->
								</div>
						</form>
					</div>

					<div class="zpm_tab_panel <?php echo $action == 'general' ? 'zpm_tab_active' : ''; ?>" data-zpm-tab="0">
						<!-- General Settings -->
						<form id="zpm_profile_settings" method="post">

							<label class="zpm_label zpm_divider_label"><?php _e( 'General Settings', 'zephyr-project-manager' ) ?></label>

							<div class="zpm-form__group">
								<input type="text" name="zpm-settings__projects-per-page" id="zpm-settings__projects-per-page" class="zpm-form__field" placeholder="<?php _e( 'Projects Per Page', 'zephyr-project-manager' ); ?>" value="<?php echo $general_settings['projects_per_page']; ?>">
								<label for="zpm-settings__projects-per-page" class="zpm-form__label"><?php _e( 'Projects Per Page', 'zephyr-project-manager' ); ?></label>
							</div>

							<div class="zpm-form__group">
								<input type="text" name="zpm-settings__tasks-per-page" id="zpm-settings__tasks-per-page" class="zpm-form__field" placeholder="<?php _e( 'Tasks Per Page', 'zephyr-project-manager' ); ?>" value="<?php echo $general_settings['tasks_per_page']; ?>">
								<label for="zpm-settings__tasks-per-page" class="zpm-form__label"><?php _e( 'Tasks Per Page', 'zephyr-project-manager' ); ?></label>
							</div>

							<label class="zpm_label"><?php _e( 'Group Projects by Category', 'zephyr-project-manager' ); ?></label>

							<label for="zpm-settings__enable-category-grouping" class="zpm-material-checkbox">
								<input type="checkbox" id="zpm-settings__enable-category-grouping" name="zpm-settings__enable-category-grouping" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['enable_category_grouping'] ) && $general_settings['enable_category_grouping'] ? 'checked' : '';  ?>>
								<span class="zpm-material-checkbox-label"><?php _e( 'Enable grouping of projects by category', 'zephyr-project-manager' ); ?></span>
							</label>

							<label class="zpm_label"><?php _e( 'Display Project ID', 'zephyr-project-manager' ); ?></label>

							<label for="zpm-settings-display-project-id" class="zpm-material-checkbox">
								<input type="checkbox" id="zpm-settings-display-project-id" name="zpm-settings-display-project-id" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['display_project_id'] ) && $general_settings['display_project_id'] !== "0" ? 'checked' : '';  ?>>
								<span class="zpm-material-checkbox-label"><?php _e( 'Display Unique Project ID', 'zephyr-project-manager' ); ?></span>
							</label>

							<label for="zpm-settings-display-database-project-id" class="zpm-material-checkbox">
								<input type="checkbox" id="zpm-settings-display-database-project-id" name="zpm-settings-display-database-project-id" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['display_database_project_id'] ) && $general_settings['display_database_project_id'] !== "0" ? 'checked' : '';  ?>>
								<span class="zpm-material-checkbox-label"><?php _e( 'Display Auto Increment Project ID', 'zephyr-project-manager' ); ?></span>
							</label>

							<div class="zpm-form-field-section">
								<label for="zpm-settings__display-task-id" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm-settings__display-task-id" name="zpm-settings__display-task-id" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['display_task_id'] ) && $general_settings['display_task_id'] !== "0" ? 'checked' : '';  ?>>
									<span class="zpm-material-checkbox-label"><?php _e( 'Display Task ID', 'zephyr-project-manager' ); ?></span>
								</label>
							</div>

							<label class="zpm_label zpm_divider_label"><?php _e( 'Email Settings', 'zephyr-project-manager' ) ?></label>

							<div class="zpm-form__group">
								<input type="text" name="zpm-settings-email-from-name" id="zpm-settings-email-from-name" class="zpm-form__field" placeholder="<?php _e( 'From Name', 'zephyr-project-manager' ); ?>" value="<?php echo $general_settings['email_from_name']; ?>">
								<label for="zpm-settings-email-from-name" class="zpm-form__label"><?php _e( 'From Name', 'zephyr-project-manager' ); ?></label>
							</div>

							<div class="zpm-form__group">
								<input type="text" name="zpm-settings-email-from-email" id="zpm-settings-email-from-email" class="zpm-form__field" placeholder="<?php _e( 'From Email', 'zephyr-project-manager' ); ?>" value="<?php echo $general_settings['email_from_email']; ?>">
								<label for="zpm-settings-email-from-email" class="zpm-form__label"><?php _e( 'From Email', 'zephyr-project-manager' ); ?></label>
							</div>

							<label class="zpm_label zpm_divider_label"><?php _e( 'Permissions & Capabilities', 'zephyr-project-manager' ) ?></label>

							<?php if (Utillities::is_admin()) : ?>
								<?php foreach ( $zpm_roles as $key => $role ) : ?>
									<label class="zpm_label"><?php echo $role['name']; ?></label>
									<select multiple="true" class="zpm-multi-select" name="zpm-settings-user-caps-<?php echo $key; ?>[]">
									<?php foreach ($user_caps as $cap) {
										$name = str_replace('zpm_', '', $cap);
										$name = str_replace('_', ' ', $name);
										$name = ucwords($name);
									?>

									<option <?php echo isset( $role['role']->capabilities[$cap]) && $role['role']->capabilities[$cap] == true ? 'selected' : ''; ?> value="<?php echo $cap; ?>"><?php echo $name; ?></option>
									<?php
									} ?>
									</select>
								<?php endforeach; ?>

								<!-- Who can complete tasks -->
								<label class="zpm_label"><?php _e( 'Who can complete tasks', 'zephyr-project-manager' ); ?></label>
								<select class="zpm_input" name="zpm-settings__can-complete-tasks">
									<option value="0" <?php echo isset($general_settings['can_complete_tasks']) && $general_settings['can_complete_tasks'] == '0' ? 'selected' : ''; ?>><?php _e( 'Everyone', 'zephyr-project-manager' ); ?></option>
									<option value="1" <?php echo isset($general_settings['can_complete_tasks']) && $general_settings['can_complete_tasks'] == '1' ? 'selected' : ''; ?>><?php _e( 'Only Assigned Users', 'zephyr-project-manager' ); ?></option>
									<option value="2" <?php echo isset($general_settings['can_complete_tasks']) && $general_settings['can_complete_tasks'] == '2' ? 'selected' : ''; ?>><?php _e( 'Only Administrators & Managers', 'zephyr-project-manager' ); ?></option>
									<option value="3" <?php echo isset($general_settings['can_complete_tasks']) && $general_settings['can_complete_tasks'] == '3' ? 'selected' : ''; ?>><?php _e( 'Nobody', 'zephyr-project-manager' ); ?></option>
								</select>
							<?php endif; ?>

							<div class="zpm-form-field-section">
								<label for="zpm_view_own_files" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm_view_own_files" name="zpm_view_own_files" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['view_own_files'] ) && $general_settings['view_own_files'] ? 'checked' : '';  ?>>
									<span class="zpm-material-checkbox-label"><?php _e( 'Allow users to only view own files', 'zephyr-project-manager' ); ?></span>
								</label>
							</div>

							<div class="zpm-form-field-section">
								<label for="zpm-settings__view-members" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm-settings__view-members" name="zpm-settings__view-members" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['view_members'] ) && $general_settings['view_members'] ? 'checked' : '';  ?>>
									<span class="zpm-material-checkbox-label"><?php _e( 'Allow users to view other members on the site', 'zephyr-project-manager' ); ?></span>
								</label>
							</div>

							<label class="zpm_label"><?php _e( 'Default Assignee', 'zephyr-project-manager' ); ?></label>
							<select class="zpm_input" name="zpm-settings__default-assignee">
								<option value="-1" <?php echo isset($general_settings['default_assignee']) && $general_settings['default_assignee'] == '-1' ? 'selected' : ''; ?>><?php _e( 'None', 'zephyr-project-manager' ); ?></option>
								<?php foreach ($users as $user) : ?>
									<option value="<?php echo $user['id']; ?>" <?php echo isset($general_settings['default_assignee']) && $general_settings['default_assignee'] == $user['id'] ? 'selected' : ''; ?>><?php echo $user['name']; ?></option>
								<?php endforeach; ?>
							</select>

							<label class="zpm_label"><?php _e( 'Default Project', 'zephyr-project-manager' ); ?></label>
							<select class="zpm_input" name="zpm-settings__default-project">
								<option value="-1" <?php echo isset($general_settings['default_project']) && $general_settings['default_project'] == '-1' ? 'selected' : ''; ?>><?php _e( 'None', 'zephyr-project-manager' ); ?></option>
								<?php foreach ($projects as $project) : ?>
									<option value="<?php echo $project->id; ?>" <?php echo isset($general_settings['default_project']) && $general_settings['default_project'] == $project->id ? 'selected' : ''; ?>><?php echo $project->name; ?></option>
								<?php endforeach; ?>
							</select>

							<?php do_action( 'zpm_permission_settings' ); ?>

							<label class="zpm_label zpm_divider_label"><?php _e( 'Customization', 'zephyr-project-manager' ) ?></label>

							<label class="zpm_label" for="zpm_colorpicker_primary"><?php _e( 'Primary Color', 'zephyr-project-manager' ); ?></label>
							<input type="text" name="zpm_backend_primary_color" id="zpm_colorpicker_primary" class="zpm_input" value="<?php echo $general_settings['primary_color']; ?>">

							<label class="zpm_label" for="zpm_colorpicker_primary_dark"><?php _e( 'Primary Dark Color', 'zephyr-project-manager' ); ?></label>
							<input type="text" name="zpm_backend_primary_color_dark" id="zpm_colorpicker_primary_dark" class="zpm_input" value="<?php echo $general_settings['primary_color_dark']; ?>">
							<label class="zpm_label" for="zpm_colorpicker_primary_light"><?php _e( 'Primary Light Color', 'zephyr-project-manager' ); ?></label>
							<input type="text" name="zpm_backend_primary_color_light" id="zpm_colorpicker_primary_light" class="zpm_input" value="<?php echo $general_settings['primary_color_light']; ?>">

							<label class="zpm_label zpm_divider_label"><?php _e( 'Dates & Calendar Settings', 'zephyr-project-manager' ) ?></label>

							<!-- First day of week -->
							<label class="zpm_label"><?php _e( 'Calendar First Day', 'zephyr-project-manager' ); ?></label>
							<select id="zpm-settings-first-day" class="zpm_input" name="zpm-settings-first-day">
								<?php foreach ($days_of_week as $val => $name) : ?>
									<option value="<?php echo $val; ?>" <?php echo $general_settings['first_day'] == $val ? 'selected' : ''; ?>><?php echo $name; ?></option>
								<?php endforeach; ?>
							</select>

							<!-- Date formats -->
							<label class="zpm_label"><?php _e( 'Date Format', 'zephyr-project-manager' ); ?></label>
							<select id="zpm-settings-date-format" class="zpm_input" name="zpm-settings-date-format">
								<?php foreach ($date_formats as $val => $date) : ?>
									<option value="<?php echo $val; ?>" <?php echo $general_settings['date_format'] == $val ? 'selected' : ''; ?>><?php echo $date; ?></option>
								<?php endforeach; ?>
							</select>

							<!-- Show Time -->
							<label for="zpm-setting__show-time" class="zpm-material-checkbox">
								<input type="checkbox" id="zpm-setting__show-time" name="zpm-setting__show-time" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['show_time'] ) && $general_settings['show_time'] == true ? 'checked' : '';  ?>>
								<span class="zpm-material-checkbox-label"><?php _e( 'Show Time', 'zephyr-project-manager' ); ?></span>
							</label>

							<!-- Emails -->
							<label class="zpm_label zpm_divider_label"><?php _e( 'Email Settings', 'zephyr-project-manager' ) ?></label>
							<div class="zpm-form__group">
								<textarea name="zpm-settings__email-mentions-content" id="zpm-settings__email-mentions-content" class="zpm-form__field" placeholder="<?php _e( 'Mentions Email', 'zephyr-project-manager' ); ?>" value="<?php echo $general_settings['email_from_email']; ?>"><?php echo $general_settings['email_mentions_content']; ?></textarea>
								<label for="zpm-settings__email-mentions-content" class="zpm-form__label"><?php _e( 'Mentions Email', 'zephyr-project-manager' ); ?></label>
							</div>

							
							<?php do_action( 'zpm_general_settings', '' ); ?>

							<div>
								<label for="zpm-settings__enable-node" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm-settings__enable-node" name="zpm-settings__enable-node" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['node_enabled'] ) && $general_settings['node_enabled'] == true ? 'checked' : '';  ?>>
									<span class="zpm-material-checkbox-label"><?php _e( 'Enable Node Server (For real-time updates)', 'zephyr-project-manager' ); ?></span>
								</label>
							</div>

							<div class="zpm-form-field-section">
								<label for="zpm-settings__override-default-emails" class="zpm-material-checkbox">
									<input type="checkbox" id="zpm-settings__override-default-emails" name="zpm-settings__override-default-emails" class="zpm_toggle invisible" value="1" <?php echo isset( $general_settings['override_default_emails'] ) && $general_settings['override_default_emails'] ? 'checked' : '';  ?>>
									<span class="zpm-material-checkbox-label"><?php _e( 'Override default email settings', 'zephyr-project-manager' ); ?></span>
								</label>
							</div>

							<?php wp_nonce_field('zpm_save_general_settings'); ?>
							<button type="submit" class="zpm_button" name="zpm_save_general_settings" id="zpm_save_general_settings"><?php _e( 'Save Settings', 'zephyr-project-manager' ); ?></button>
						</form>
					</div>

					<div class="zpm_tab_panel <?php echo $action == 'advanced' ? 'zpm_tab_active' : ''; ?>" data-zpm-tab="advanced">
						<!-- General Settings -->
						<form id="zpm_advanced_settings" method="post" enctype='multipart/form-data'>

							<?php do_action( 'zpm_advanced_settings_content' ); ?>

							<div class="zpm-form__group">
								<textarea type="text" name="zpm-settings__custom-css" id="zpm-settings__custom-css" class="zpm-form__field" placeholder="<?php _e( 'Custom CSS', 'zephyr-project-manager' ); ?>"><?php echo $general_settings['custom_css']; ?></textarea>
								<label for="zpm-settings__custom-css" class="zpm-form__label"><?php _e( 'Custom CSS', 'zephyr-project-manager' ); ?></label>
							</div>

							<?php if(current_user_can( 'administrator' )) : ?>
								<button type="button" class="zpm_button zpm-button__red" id="zpm-delete-data__button" data-zpm-modal="zpm-delete-data__modal"><?php _e( "DELETE all Zephyr Project Manager Data", 'zephyr-project-manager' ); ?></button>
							<?php endif; ?>
							
							<?php wp_nonce_field('zpm_save_general_settings'); ?>
							<button type="submit" class="zpm_button" name="zpm-settings__advanced-submit"><?php _e( 'Save Settings', 'zephyr-project-manager' ); ?></button>
						</form>

						<?php if(current_user_can( 'administrator' )) : ?>
							<form id="zpm-delete-data__form" style="display: none;" method="post" name="zpm-delete-all-data">
								<button type="submit" name="zpm-delete-all-data"></button>
							</form>
						<?php endif; ?>
						
					</div>

					<?php
						$pages = ob_get_clean();
						echo apply_filters( 'zpm_settings_pages', $pages);
					?>
					<?php
						foreach ($settingsPages as $page) {
							?>
								<div class="zpm_tab_panel <?php echo $action == $page['slug'] ? 'zpm_tab_active' : ''; ?>" data-zpm-tab="<?php echo $page['slug']; ?>">
									<?php echo $page['content']; ?>
								</div>
							<?php
						}
					?>

				</div>
			</div>
		</div>
	</div>
</main>

<?php $this->get_footer(); ?>