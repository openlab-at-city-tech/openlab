<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Pages;

use \DateTime;
use Inc\Zephyr;
use Inc\Core\Tasks;
use Inc\Core\Projects;
use Inc\Core\Utillities;
use Inc\Core\Activity;
use Inc\Core\Members;
use Inc\Api\SettingsApi;
use Inc\ZephyrProjectManager;
use Inc\Base\BaseController;
use Inc\Api\Callbacks\AdminCallbacks;
use Inc\Api\Callbacks\SanitizationCallbacks;

class Admin extends BaseController {
	public $settings;
	public $callbacks;
	public $callbacks_sanitization;
	public $pages = array();
	public $subpages = array();
	public $access_level;
	public $manager;
	
	public function register() {

		if (isset($_POST['zpm_first_time'])) {
			add_option( 'zpm_first_time', true );
		}

		add_action( 'wp_dashboard_setup', array($this, 'setup_dashboard_widget') );
		add_action( 'wp_login', array( $this, 'on_login' ) );
		add_action( 'wp_logout', array( $this, 'on_logout' ) );

		add_filter( 'zpm-project-tab-pages', array( $this, 'project_tabs' ), 10, 2 );

		$this->settings = new SettingsApi();
		$this->callbacks = new AdminCallbacks();
		$this->callbacks_sanitization = new SanitizationCallbacks();
		$generalSettings = Utillities::general_settings();
		//$this->set_pages();

		$project = new Projects();
		$zpm_used = get_option('zpm_used');

		if (get_option('zpm_first_time')) {
			//$this->set_sub_pages();
		} else {
			//add_action( 'admin_notices', array( $this, 'first_time_use' ) );
		}

		// Review notice
		if( empty( get_option( 'zpm_review_notice_dismissed' ) ) && $zpm_used > 5 ) {
			//add_action( 'admin_notices', array( $this, 'review_notice' ) );
		}
		
		$this->settings->add_pages( $this->pages )->with_sub_page( __( 'Dashboard', 'zephyr-project-manager' ) )->add_sub_pages( $this->subpages )->register();
		
		add_filter( 'upload_mimes', array($this, 'custom_mime_types'), 1, 1 );

		if (!Zephyr::isPro()) {
			add_filter( 'zpm_task_item_extra_details', array($this, 'task_list_custom_data'), 1, 2 );
		}

		add_action( 'admin_print_scripts', array( $this, 'hide_unrelated_notices' ) );

		add_action( 'admin_menu', array( $this, 'check_access_level' ) );
 
		//add_filter('parse_query', array($this, 'filter_media_files') );
		
		$this->addShortcodes();

		add_action( 'custom_menu_order', array( $this, 'custom_submenu_order' ) );
		add_filter( 'zpm_has_zephyr_access', array( $this, 'hasZephyrAccess' ) );

		if ($generalSettings['view_own_files']) {
			//add_filter( 'ajax_query_attachments_args', array( $this, 'showCurrentUserFiles' ) );
		}

		// Remove new user Zephyr access
		add_action( 'user_register', array( $this, 'newUserCreated' ), 10, 1 );
	}

	public function addShortcodes() {
		add_shortcode( 'zephyr_project', array( $this, 'project_shortcode' ) );
		add_shortcode( 'zephyr_task', array( $this, 'task_shortcode' ) );
		add_shortcode( 'zephyr_calendar', array( $this, 'calendarShortcode' ) );
		add_shortcode( 'zephyr_files', array( $this, 'filesShortcode' ) );
		add_shortcode( 'zephyr_file_upload', array( $this, 'fileUploadShortcode' ) );
		add_shortcode( 'zephyr_activity', array( $this, 'activityShortcode' ) );
		add_shortcode( 'zephyr_new_task_button', array( $this, 'newTaskModalShortcode' ) );
		add_shortcode( 'zpm_action_button', array( $this, 'actionButtonShortcode' ) );
		add_shortcode( 'zpm_project_progress', array( $this, 'projectProgressShortcode' ) );
		add_shortcode( 'zpm_dashboard_projects', array( $this, 'dashboardProjectsShortcode' ) );
		add_shortcode( 'zpm_user_overview', array( $this, 'userOverview' ) );
	}

	public function showCurrentUserFiles( $query ) {
		$user_id = get_current_user_id();

		if ( $user_id && !current_user_can( 'administrator' ) ) {
			$query['author'] = $user_id;
		}

		return $query;
	} 

	public function handleZephyrAccess() {
		if (!apply_filters( 'zpm_has_zephyr_access', true )) {
			wp_die(__("Sorry, you do not have access to this page", 'zephyr-project-manager'));
			exit();
		}
	}

	public function hasZephyrAccess() {
		$lowestAccess = get_option( 'zpm_access_settings', 'manage_options' );

		if ( current_user_can('zpm_all_zephyr_capabilities') || current_user_can('administrator') ) {
			return true;
		}

		if ( current_user_can( $lowestAccess ) || Utillities::user_has_role( get_current_user_id(), $lowestAccess ) ) {
			return true;
		}

		if (Utillities::hasZephyrRole() || current_user_can('manage_options')) {
			return true;
		}

		if (!Utillities::hasZephyrRole() && Utillities::can_zephyr(get_current_user_id())) {
			return true;
		}

		// if (Utillities::is_zephyr_role($userId)) {
		// 	if (current_user_can('zpm_access_zephyr')) {
		// 		return true;
		// 	}
		// }

		// if ( $access_level == 'zpm_user' || $access_level == 'zpm_manager' || $access_level == 'zpm_admin' || $access_level == 'zpm_frontend_user' || $access_level == 'read'  ) {
		// 	if ( zpm_user_has_role( $user_id, 'subscriber' ) ) {
		// 		return false;
		// 	}
		// }
		return false;
	}

	public function on_login( $user_login ) {
		$user = get_user_by('login', $user_login);
		$date = date('Y-m-d H:i:s');
		Activity::log_activity( $user->ID, '', '', '', '', __( 'logged in', 'zephyr-project-manager' ) );
	}

	public function on_logout() {
		$date = date('Y-m-d H:i:s');
		Activity::log_activity( get_current_user_id(), '', '', '', '', __( 'logged out', 'zephyr-project-manager' ) );
	}

	public function custom_submenu_order() {
	    global $submenu;
	    $newSubmenu = [];
	    foreach ($submenu as $menuName => $menuItems) {

	        if ('zephyr_project_manager' === $menuName) {

	            foreach ($menuItems as $index => $item) {
	            	$slug = $item[2];
	            	// Move help page to bottom
	            	if ( isset( $item[2] ) && $slug == 'zephyr_project_manager_extensions' ) {
	            		$newSubmenu[97] = $item;
	            	} else if ( isset( $slug ) && $slug == 'zephyr_project_manager_help' ) {
	            		$newSubmenu[98] = $item;
	            	} else if ( isset( $slug ) && $slug == 'zephyr_project_manager_settings' ) {
	            		$newSubmenu[99] = $item;
	            	} else {
	            		$newSubmenu[$index] = $item;
	            	}

	            	$newSubmenu = apply_filters( 'zpm_admin_menu_order', $newSubmenu, $index, $slug );
	            }

	            ksort($newSubmenu);

	            $submenu['zephyr_project_manager'] = $newSubmenu;
	            break;
	        }
	    }
	}

	function filesShortcode( $atts ) {
		$a = shortcode_atts( array(
			'project' => '',
			'task'    => '',
			'user'    => 'all'
		), $atts );

		$attachments = BaseController::get_attachments();
		$filetypes = array();
		$currentUserId = get_current_user_id();

		// Get an array of all filetypes that are used
		foreach ($attachments as $attachment) {
			$attachment_url = wp_get_attachment_url($attachment['message']);
			$attachment_type = wp_check_filetype($attachment_url)['ext'];
			if (!in_array($attachment_type, $filetypes)) {
				array_push($filetypes, $attachment_type);
			}
		}

		ob_start();
		?>
		<div class="zpm-shortcode__file-holder">

		<?php foreach($attachments as $attachment) : ?>
				<?php

					if (!Utillities::can_view_file( $attachment )) {
						continue;
					}

					$subject_name = __( 'None', 'zephyr-project-manager' );

					if ($attachment['subject'] == 'task') {
						if (!empty($a['project'])) {
							continue;
						}
						if (!empty($a['task'])) {
							if ($attachment['subject_id'] !== $a['task']) {
								continue;
							}
						}
						$task = (is_object(Tasks::get_task($attachment['subject_id']))) ? Tasks::get_task($attachment['subject_id']) : false;
						$subject_name = ($task) ? stripslashes($task->name) : __( 'No Task', 'zephyr-project-manager' );

						if ($a['user'] == 'current') {
							if (!Tasks::is_assignee($task, $currentUserId)) {
								continue;
							}
						}
					}
					if ($attachment['subject'] == 'project') {
						if (!empty($a['task'])) {
							continue;
						}
						if (!empty($a['project'])) {
							if ($attachment['subject_id'] !== $a['project']) {
								continue;
							}
						}
						$project = (is_object(Projects::get_project($attachment['subject_id']))) ? Projects::get_project($attachment['subject_id']) : false;
						$subject_name = ($project) ? stripslashes($project->name) : __( 'No Project', 'zephyr-project-manager' );
						if ($a['user'] == 'current') {
							if (!Projects::is_project_member($project, $currentUserId)) {
								continue;
							}
						}
					}

					$project_id = $attachment['subject_id'];
					$attachmentId = $attachment['message'];
					$attachmentDatetime = new DateTime( $attachment['date_created'] );
					$attachmentDate = $attachmentDatetime->format('d M Y H:i');
					$attachmentUrl = wp_get_attachment_url($attachmentId);
					$attachmentType = wp_check_filetype($attachment_url)['ext']; 
					$attachmentName = basename(get_attached_file($attachmentId));
				?>
				<div class="zpm_file_item_container" data-project-id="<?php echo $project_id; ?>">
					<div class="zpm_file_item" data-attachment-id="<?php echo $attachment['id']; ?>" data-attachment-url="<?php echo $attachmentUrl; ?>" data-attachment-name="<?php echo $attachmentName; ?>" data-task-name="<?php  echo $subject_name; ?>" data-attachment-date="<?php echo $attachmentDate; ?>">
						<?php if (wp_attachment_is_image($attachmentId)) : ?>
							<!-- If attachment is an image -->
							<div class="zpm_file_preview" data-zpm-action="show_info">
								<span class="zpm_file_image" style="background-image: url(<?php echo $attachmentUrl; ?>);"></span>
							</div>
						<?php else: ?>
							<div class="zpm_file_preview" data-zpm-action="show_info">
								<div class="zpm_file_type"><?php echo '.' . $attachmentType; ?></div>
							</div>
						<?php endif; ?>

						<h4 class="zpm_file_name">
							<?php echo $attachmentName; ?>
							<span class="zpm_file_actions zpm-colors__background-primary">
								<span class="zpm_file_action lnr lnr-download" data-zpm-action="download_file"></span>
								<span class="zpm_file_action lnr lnr-question-circle" data-zpm-action="show_info"></span>
								<span class="zpm_file_action lnr lnr-trash" data-zpm-action="remove_file"></span>
							</span>
						</h4>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
	}

	function calendarShortcode( $atts ) {
		$a = shortcode_atts( array(
			'user' => 'all',
			'completed' => '-1'
		), $atts );

		ob_start();

		?>
			<div id="zpm_calendar" class="zpm-shortcode__calendar" data-user="<?php echo $a['user']; ?>" data-completed="<?php echo $a['completed']; ?>"></div>
		<?php
	
		$html = ob_get_clean();
		return $html;
	}

	function fileUploadShortcode( $atts ) {
		$a = shortcode_atts( array(
			'project' => '',
			'task' => ''
		), $atts );

		ob_start();

		?>
			<div class="zpm-shortcode__file-uploader">
				<button class="zpm-shortcode__file-upload-btn" data-project="<?php echo $a['project']; ?>" data-task="<?php echo $a['task']; ?>"><?php _e( 'Upload File', 'zephyr-project-manager' ); ?></button>
			</div>
		<?php
	
		$html = ob_get_clean();
		return $html;
	}

	function project_shortcode( $atts ){
		$a = shortcode_atts( array(
		'id' => '-1',
		'link' => '',
		'link_to_project' => false,
		'assignee' => 'all',
		'edit' => false
		), $atts );

		ob_start();

		if ($a['assignee'] == 'current') {
			$a['assignee'] = get_current_user_id();
		}

		if ( $a['id'] == 'all' || $a['id'] == '-1' ) {
			$projects = Projects::get_projects();

			if ($a['assignee'] !== 'all') {
				$projects = Projects::get_member_projects( $a['assignee'] );
			}

			foreach ($projects as $project) {
				if (!is_object($project)) {
					continue;
				}
				$Tasks = new Tasks();
				$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_projects') );
				$other_data = unserialize( $project->other_data);
				$color = isset($other_data['color']) ? $other_data['color'] : '#eee';
				$complete = ( ($project->completed == '1') ? 'completed disabled' : '' );
				$categories = maybe_unserialize( $project->categories );
				$team = maybe_unserialize( $project->team );
				$total_tasks = $Tasks->get_project_task_count( $project->id );
				$completed_tasks = $Tasks->get_project_completed_tasks( $project->id );
				$active_tasks = (int) $total_tasks - (int) $completed_tasks;
				$message_count = sizeof( Projects::get_comments( $project->id ) );
				$tasks = Tasks::get_project_tasks($project->id);

				$can_link = $a['link'] !== '' || $a['link_to_project'] !== false ? true : false;
				$link = '';

				if ( $can_link ) {
				  if ($a['link'] !== '') {
				    $link = $a['link'];
				  } else if ($a['link_to_project'] !== false) {
				    $link = Utillities::get_project_url() . '/?action=project&id=' . $project->id;
				  }
				}

				$classes = $project->completed !== '0' ? 'completed' : '';

				?>

				<div class="zpm-project-shortcode" data-project-id="<?php echo $project->id; ?>" data-edit-project="<?php echo $a['edit'] ? 'true' : 'false'; ?>">
				    <div class="zpm-project-card <?php echo $classes; ?>">

				      <?php if ($can_link) : ?>
				        <a class="zpm-shortcode-link" href="<?php echo $link; ?>"></a>
				      <?php endif; ?>


				      <div class="zpm-project-card-header">
				        <small class="zpm-project-card-type"><?php _e( 'Project', 'zephyr-project-manager' ); ?></small>
				        <h3 class="zpm-project-card-title"><?php echo $project->name; ?></h3>
				      </div>
				      <div class="zpm-project-card-body">
				        
				        <span class="zpm-project-shortcode-description"><?php echo $project->description; ?></span>
					      <div id="zpm-project-shortcode-progress">
					        <span class="zpm-project-shortcode-stat">
					          <p class="zpm-shortcode-stat-number"><?php echo $completed_tasks; ?></p>
					          <p><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
					        </span>
					        <span class="zpm-project-shortcode-stat">
					          <p class="zpm-shortcode-stat-number"><?php echo $active_tasks; ?></p>
					          <p><?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?></p>
					        </span>
					        <span class="zpm-project-shortcode-stat">
					          <p class="zpm-shortcode-stat-number"><?php echo $message_count; ?></p>
					          <p><?php _e( 'Messages', 'zephyr-project-manager' ); ?></p>
					        </span>
					      </div>

					       <div class="zpm-project-shortcode__tasks">
								<?php foreach((array) $tasks as $task) : ?>
									<div class="zpm-project-shortcode__task <?php echo $task->completed == '1' ? 'zpm-task-completed' : ''; ?>" data-task-id="<?php echo $task->id; ?>">
										<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
										<?php echo $task->name; ?>
										<?php if (!empty($task->description)) : ?>
											<span class="zpm-project-shortcode__description">
												<?php echo ' - ' . $task->description; ?>
											</span>
										<?php endif; ?>
									</div>
								<?php endforeach; ?>
							</div>

					      <div class="zpm-shortcode-progress-background">
					        <div class="zpm-shortcode-progress-bar" data-total_tasks="<?php echo $total_tasks; ?>" data-completed_tasks="<?php echo $completed_tasks; ?>"></div>
					      </div>
				      </div>
				      <div class="zpm-project-card-footer">
				        
				      </div>
				    </div>
				</div>
				<?php
			}
		} else {
			$project = Projects::get_project( $a['id'] );
			if (!is_object($project)) {
				return '';
			}
			$Tasks = new Tasks();
			$base_url = esc_url( admin_url('/admin.php?page=zephyr_project_manager_projects') );
			$other_data = unserialize( $project->other_data);
			$color = isset($other_data['color']) ? $other_data['color'] : '#eee';
			$complete = ( ($project->completed == '1') ? 'completed disabled' : '' );
			$categories = maybe_unserialize( $project->categories );
			$team = maybe_unserialize( $project->team );
			$total_tasks = $Tasks->get_project_task_count( $project->id );
			$completed_tasks = $Tasks->get_project_completed_tasks( $project->id );
			$active_tasks = (int) $total_tasks - (int) $completed_tasks;
			$message_count = sizeof( Projects::get_comments( $project->id ) );
			$tasks = Tasks::get_project_tasks($project->id);

			$can_link = $a['link'] !== '' || $a['link_to_project'] !== false ? true : false;
			$link = '';

			if ( $can_link ) {
			  if ($a['link'] !== '') {
			    $link = $a['link'];
			  } else if ($a['link_to_project'] !== false) {
			    $link = Utillities::get_project_url() . '/?action=project&id=' . $project->id;
			  }
			}

			$classes = $project->completed !== '0' ? 'completed' : '';

			?>

			<div class="zpm-project-shortcode" data-project-id="<?php echo $project->id; ?>"  data-edit-project="<?php echo $a['edit'] ? 'true' : 'false'; ?>">
			    <div class="zpm-project-card <?php echo $classes; ?>">

			      <?php if ($can_link) : ?>
			        <a class="zpm-shortcode-link" href="<?php echo $link; ?>"></a>
			      <?php endif; ?>


			      <div class="zpm-project-card-header">
			        <small class="zpm-project-card-type"><?php _e( 'Project', 'zephyr-project-manager' ); ?></small>
			        <h3 class="zpm-project-card-title"><?php echo $project->name; ?></h3>
			      </div>
			      <div class="zpm-project-card-body">
			        
			        <span class="zpm-project-shortcode-description"><?php echo $project->description; ?></span>
				      <div id="zpm-project-shortcode-progress">
				        <span class="zpm-project-shortcode-stat">
				          <p class="zpm-shortcode-stat-number"><?php echo $completed_tasks; ?></p>
				          <p><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></p>
				        </span>
				        <span class="zpm-project-shortcode-stat">
				          <p class="zpm-shortcode-stat-number"><?php echo $active_tasks; ?></p>
				          <p><?php _e( 'Active Tasks', 'zephyr-project-manager' ); ?></p>
				        </span>
				        <span class="zpm-project-shortcode-stat">
				          <p class="zpm-shortcode-stat-number"><?php echo $message_count; ?></p>
				          <p><?php _e( 'Messages', 'zephyr-project-manager' ); ?></p>
				        </span>
				      </div>

				    <div class="zpm-project-shortcode__tasks">
						<?php foreach((array) $tasks as $task) : ?>
							<div class="zpm-project-shortcode__task <?php echo $task->completed == '1' ? 'zpm-task-completed' : ''; ?>" data-task-id="<?php echo $task->id; ?>">
								<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
								<?php echo $task->name; ?>
								<?php if (!empty($task->description)) : ?>
									<span class="zpm-project-shortcode__description">
										<?php echo ' - ' . $task->description; ?>
									</span>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>

				      <div class="zpm-shortcode-progress-background">
				        <div class="zpm-shortcode-progress-bar" data-total_tasks="<?php echo $total_tasks; ?>" data-completed_tasks="<?php echo $completed_tasks; ?>"></div>
				      </div>
			      </div>
			      <div class="zpm-project-card-footer">
			        
			      </div>
			    </div>
			</div>
			<?php
		}
		$html = ob_get_clean();
		return $html;
    }

    function task_shortcode( $atts ){
		$a = shortcode_atts( array(
			'id' => '-1',
			'assignee' => '-1',
			'project' => '-1',
			'limit' => '-1',
			'type' => 'cards',
			'comments' => '0',
			'hide_completed' => '0',
			'logged_in' => false
		), $atts );

		ob_start();
		$manager = ZephyrProjectManager();

		if ($a['logged_in'] == true) {
			if (!is_user_logged_in()) {
				return;
			}
		}

		if ($a['id'] == 'all' || $a['id'] == '-1') {

			$tasks = $manager::get_tasks();
			$i = 0;

			if ( $a['assignee'] !== '-1' ) {
				$a['assignee'] = $a['assignee'] !== 'current' ? $a['assignee'] : get_current_user_id();
				$tasks = Tasks::get_user_tasks( $a['assignee'] );
			}

			if ( $a['project'] !== '-1' ) {
				foreach ($tasks as $key => $task) {
					if ($task->project !== $a['project']) {
						unset($tasks[$key]);
					}
				}
			}

			?>
			<div class="zpm-shortcode-task-list" data-type="<?php echo $a['type']; ?>">

			<?php foreach ($tasks as $task) {

					if (!is_object($task) || ($a['limit'] !== '-1' && $i >= $a['limit'])) {
						continue;
					}

					if ($a['hide_completed'] == '1' && $task->completed == '1') {
						continue;
					}

					$canComplete = apply_filters( 'zpm_can_complete_task', true, $task );

					
					$classes = $task->completed !== '0' ? 'completed' : '';
					$today = new DateTime();
					$general_settings = Utillities::general_settings();
					$due_datetime = new DateTime($task->date_due);
					$users = get_users();
					$user_id = wp_get_current_user()->ID;
					$task_project = Projects::get_project($task->project);
					$project_name = is_object($task_project) ? $task_project->name : '';
			        $row_classes = (($task->completed == '1') ? 'zpm_task_complete' : '');
			        $assignees = Tasks::get_assignees( $task, true );
					$due_today = ($today->format('Y-m-d') == $due_datetime->format('Y-m-d')) ? true : false;
					$overdue = ($today > $due_datetime && !$due_today) ? true : false;
					$due_date = (!$due_today) ? $due_datetime->format($general_settings['date_format']) : 'Today';
					$due_date = ($task->date_due !== '0000-00-00 00:00:00') ? date_i18n($general_settings['date_format'], strtotime($task->date_due)) : '';
					$editTask = Utillities::canEditTask($task);

					?>
					<?php if ($a['type'] == 'cards') : ?>
						<div class="zpm-task-shortcode <?php echo $task->completed == '1' ? 'zpm-task-completed' : ''; ?>" data-task-id="<?php echo $task->id; ?>" data-edit-task="<?php echo $editTask; ?>">
							<div class="zpm-task-card <?php echo $classes; ?>">

								<div class="zpm-task-card-header">
									<small class="zpm-task-card-type"><?php _e( 'Task', 'zephyr-project-manager' ); ?></small>

									<div class="zpm-task-card__title">
										<?php if ($canComplete) : ?>
											<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
										<?php endif; ?>
										
										<h3 class="zpm-task-card-title"><?php echo $task->name; ?></h3>
									</div>
									

									<div class="zpm-task-card-dates"><?php echo $due_date; ?></div>
								</div>

								<div class="zpm-task-card-body">
									<div class="zpm-task-card__description"><?php echo $task->description; ?></div>
								</div>

								<div class="zpm-task-card-footer">
									<div class="zpm-task-card__assignee">
										<?php foreach ( $assignees as $assignee ) : ?>
											<span title="<?php echo $assignee['name']; ?>" class='zpm-task-assignee__avatar' style='background-image: url("<?php echo $assignee['avatar'] ?>"); <?php echo $assignee['avatar'] == '' ? 'display: none;' : ''; ?>' title="<?php echo $assignee['name'] ?>"></span>
										<?php endforeach; ?>
									</div>
								</div>
								<span class="zpm-shortcode-task__delete lnr lnr-cross-circle"></span>
								<?php if ($a['comments'] == '1') : ?>
									<span class="zpm-shortcode-task__comments lnr lnr-bubble"></span>
								<?php endif; ?>
							</div>
						</div>
					<?php else: ?>
						<div class="zpm-task-shortcode zpm-task-shortcode-list-item <?php echo $task->completed == '1' ? 'zpm-task-completed' : ''; ?>" data-task-id="<?php echo $task->id; ?>" data-edit-task="<?php echo $editTask; ?>" <?php echo $classes; ?>>
							
							<?php if ($canComplete) : ?>
								<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
							<?php endif; ?>
							<span class="zpm-task-list-title"><?php echo $task->name; ?></span>
							<span class="zpm-task-card__description"><?php echo !empty($task->description) ? ' - ' . $task->description : ''; ?></span>
							<span class="zpm-shortcode-task__delete lnr lnr-cross-circle"></span>
							<?php if ($a['comments'] == '1') : ?>
								<span class="zpm-shortcode-task__comments lnr lnr-bubble"></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
					<?php
					$i++;
				}
				?>
			</div>
			<?php
		} else {
			$task = $manager::get_task( $a['id'] );

			if (!is_object($task)) {
				return '';
			}
			$canComplete = apply_filters( 'zpm_can_complete_task', true, $task );
			
			$classes = $task->completed !== '0' ? 'completed' : '';

			$today = new DateTime();
			$general_settings = Utillities::general_settings();
			$due_datetime = new DateTime($task->date_due);
			$users = get_users();
			$user_id = wp_get_current_user()->ID;
			$task_project = Projects::get_project($task->project);
			$project_name = is_object($task_project) ? $task_project->name : '';
	        $row_classes = (($task->completed == '1') ? 'zpm_task_complete' : '');
	        $assignees = Tasks::get_assignees( $task, true );
			$due_today = ($today->format('Y-m-d') == $due_datetime->format('Y-m-d')) ? true : false;
			$overdue = ($today > $due_datetime && !$due_today) ? true : false;
			$due_date = (!$due_today) ? $due_datetime->format($general_settings['date_format']) : 'Today';
			$due_date = ($task->date_due !== '0000-00-00 00:00:00') ? date_i18n($general_settings['date_format'], strtotime($task->date_due)) : '';

			?>

			<?php if ($a['type'] == 'cards') : ?>
				<div class="zpm-task-shortcode" data-task-id="<?php echo $task->id; ?>" data-edit-task="true">
					<div class="zpm-task-card <?php echo $classes; ?>">

						<div class="zpm-task-card-header">
							<small class="zpm-task-card-type"><?php _e( 'Task', 'zephyr-project-manager' ); ?></small>

							<div class="zpm-task-card__title">
								<?php if ($canComplete) : ?>
									<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
								<?php endif; ?>
								
								<h3 class="zpm-task-card-title"><?php echo $task->name; ?></h3>
								<!-- <?php if ($task->completed == '1') : ?>
									<span class="zpm-task-card__completed_label"><?php _e( 'Completed', 'zephyr-project-manager' ); ?></span>
								<?php endif; ?> -->
							</div>
							

							<div class="zpm-task-card-dates"><?php echo $due_date; ?></div>
						</div>

						<div class="zpm-task-card-body">
							<div class="zpm-task-card__description"><?php echo $task->description; ?></div>
						</div>

						<div class="zpm-task-card-footer">
							<div class="zpm-task-card__assignee">
								<?php foreach ( $assignees as $assignee ) : ?>
									<span title="<?php echo $assignee['name']; ?>" class='zpm-task-assignee__avatar' style='background-image: url("<?php echo $assignee['avatar'] ?>"); <?php echo $assignee['avatar'] == '' ? 'display: none;' : ''; ?>' title="<?php echo $assignee['name'] ?>"></span>
								<?php endforeach; ?>
							</div>
						</div>
						<span class="zpm-shortcode-task__delete lnr lnr-cross-circle"></span>
						<?php if ($a['comments'] == '1') : ?>
							<span class="zpm-shortcode-task__comments lnr lnr-bubble"></span>
						<?php endif; ?>
					</div>
				</div>
				<?php else: ?>
					<div class="zpm-task-shortcode zpm-task-shortcode-list-item <?php echo $task->completed == '1' ? 'zpm-task-completed' : ''; ?>" data-task-id="<?php echo $task->id; ?>" data-edit-task="true" <?php echo $classes; ?>>
						<?php if ($canComplete) : ?>
							<span class="lnr lnr-checkmark-circle zpm-complete-shortcode-task"></span>
						<?php endif; ?>
						<span class="zpm-task-list-title"><?php echo $task->name; ?></span>
						<span class="zpm-task-card__description"><?php echo !empty($task->description) ? ' - ' . $task->description : ''; ?></span>
						<span class="zpm-shortcode-task__delete lnr lnr-cross-circle"></span>
						<?php if ($a['comments'] == '1') : ?>
							<span class="zpm-shortcode-task__comments lnr lnr-bubble"></span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			<?php
		}
		$html = ob_get_clean();
		return $html;
    }

    public function activityShortcode($atts) {
    	$a = shortcode_atts( array(
			'id' => '-1',
			'limit' => '100'
		), $atts );

    	$activities = Activity::get_activities( array( 'limit' => $a['limit'], 'offset' => 0 ) );
		$activities = Activity::display_activities($activities);
		ob_start();
		if (!$activities) : ?>
			<div class="zpm_no_results_message"><?php _e('There is no activity yet. Once there is, the activities will be displayed here.', 'zephyr-project-manager'); ?></div>
		<?php else: ?>
			<?php echo $activities; ?>
		<?php endif; ?>
		<?php
		$html = ob_get_clean();
		return $html;
    }

    public function actionButtonShortcode($atts) {
    	$a = shortcode_atts( array(
		), $atts );

		ob_start();
		$data = apply_filters( 'zpm_frontend_buttons', '' );
		
		?>
		<div id="zpm-shortcode__action-button">
			<?php echo $data; ?>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
    }

    public function projectProgressShortcode($atts) {
    	$a = shortcode_atts( array(
    		'id' => '-1',
    		'height' => 300,
    		'width' => 300,
    		'user_id' => '-1',
    		'color_completed' => '#00bc8a',
    		'color_pending' => '#6500d8',
    		'color_overdue' => '#e8005c'
		), $atts );

		ob_start();

		if ($a['user_id'] == 'current') {
			$projects = Projects::getUserProjects( get_current_user_id() );
			foreach ($projects as $project) {
				$this->projectProgressChartHtml( $project, $a );
			}
		} else {
			$project = Projects::get_project($a['id']);
			$this->projectProgressChartHtml( $project, $a );
		}

		?>
		
		<?php
		$html = ob_get_clean();
		return $html;
    }

    public function projectProgressChartHtml( $project, $a ) {
    	?>
    	<div class="zpm-project-progress__shortcode-wrap">
			<h5 class="zpm-project-progress__title"><?php echo $project->name; ?></h5>
			<canvas
			class="zpm-project-progress__shortcode"
			data-project-id="<?php echo $project->id; ?>"
			height="<?php echo $a['height']; ?>"
			width="<?php echo $a['width']; ?>"
			data-color-completed="<?php echo $a['color_completed']; ?>"
			data-color-pending="<?php echo $a['color_pending']; ?>"
			data-color-overdue="<?php echo $a['color_overdue']; ?>"></canvas>
		</div>
		<?php
    }

    public function dashboardProjectsShortcode( $atts ) {

    	$dashboard_projects = Projects::get_dashboard_projects();
    	?>
    	<div id="zpm-project-list">

    	<?php
		foreach ($dashboard_projects as $project) :
				if (!is_object($project)) {
					continue;
				} ?>
				<?php echo Projects::new_project_cell($project, array( 'is_dashboard_project' => true )); ?>
				<?php
			endforeach;
			?>
		</div>
		<?php
    }

    public function userOverview( $atts ) {
    	$a = shortcode_atts( array(
    		'id' => 'current'
		), $atts );

		$userId = get_current_user_id();
		$user_tasks = Tasks::get_user_tasks( $userId );
		$user_completed_tasks = Tasks::get_user_completed_tasks( $userId );
		$user_pending_tasks = Tasks::get_user_completed_tasks( $userId , '0');
		$userProjects = Projects::get_user_projects( $userId );

		ob_start();
		?>
		<div id="zpm-panel-user-overview" class="zpm-panel zpm-panel-12 zpm-user-overview__shortcode">

			<h4 class="zpm_panel_title"><?php _e( 'User Overview', 'zephyr-project-manager' ); ?></h4>

			<div id="zpm-project-stat-overview" class="zpm-user-overview-stats">
				<span class="zpm-project-stat">
					<span id="zpm_project_stats_total" class="zpm-project-stat-value zpm-user-project-count-value"><?php echo sizeof($userProjects); ?></span>
					<span class="zpm-project-stat-label"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
					<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo Utillities::get_frontend_url('action=projects&user=' . $userId); ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
				</span>
				<span class="zpm-project-stat">
					<span id="zpm_project_stats_total" class="zpm-project-stat-value"><?php echo sizeof( $user_tasks ); ?></span>
					<span class="zpm-project-stat-label"><?php _e( 'Tasks', 'zephyr-project-manager' ); ?></span>
					<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo Utillities::get_frontend_url('action=tasks&user=' . $userId); ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
				</span>
				<span class="zpm-project-stat">
					<span class="zpm-project-stat-value medium"><?php echo sizeof( $user_pending_tasks ); ?></span>
					<span class="zpm-project-stat-label"><?php _e( 'Pending Tasks', 'zephyr-project-manager' ); ?></span>
					<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo Utillities::get_frontend_url('action=tasks&user=' . $userId . '&status=pending'); ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
				</span>
				<span class="zpm-project-stat">
					<span class="zpm-project-stat-value good"><?php echo sizeof( $user_completed_tasks ); ?></span>
					<span class="zpm-project-stat-label"><?php _e( 'Completed Tasks', 'zephyr-project-manager' ); ?></span>
					<a class="zpm_button zpm_button_inverted zpm-dashboard__button" href="<?php echo Utillities::get_frontend_url('action=tasks&user=' . $userId . '&completed=true'); ?>"><?php _e( 'View All', 'zephyr-project-manager' ) ?></a>
				</span>
			</div>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
    }

    public function newTaskModalShortcode($atts) {
    	$a = shortcode_atts( array(
		), $atts );

    	
		ob_start();
		?>
		<div class="zpm-shortcode__new-task-btn-wrap">
			<button class="zpm-shortcode__new-task-btn" data-trigger="new-task-modal"><?php _e( 'New Task', 'zephyr-project-manager' ); ?></button>
		</div>
		<?php
		$html = ob_get_clean();
		return $html;
    }

	public function check_access_level() {
		$user_id = get_current_user_id();
		$user_settings = Utillities::get_user_settings( $user_id );
		if ( $user_settings['can_zephyr'] == "false" ) {
			remove_menu_page( 'zephyr_project_manager' );
		}

		$access_level = Utillities::get_access_level();
		if ( !apply_filters( 'zpm_has_zephyr_access', true ) ) {
			remove_menu_page( 'zephyr_project_manager' );
		}

		if (isZephyrPage()) {
			$this->handleZephyrAccess();
		}
	}

	/**
	* Remove all non-Zephyr related plugin notices from plugin pages
	*/
	public function hide_unrelated_notices() {

		$zpm_pages = zpm_get_pages();

		// Quit if it is not on our pages
		if ( empty( $_REQUEST['page'] ) || in_array($_REQUEST['page'], $zpm_pages) === false ) {
			return;
		}

		$zpm_used = get_option('zpm_used') ? get_option('zpm_used') : 0;

		update_option('zpm_used', ($zpm_used + 1) );

		global $wp_filter;

		if ( ! empty( $wp_filter['user_admin_notices']->callbacks ) && is_array( $wp_filter['user_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['user_admin_notices']->callbacks as $priority => $hooks ) {
				
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['user_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['admin_notices']->callbacks ) && is_array( $wp_filter['admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}

		if ( ! empty( $wp_filter['all_admin_notices']->callbacks ) && is_array( $wp_filter['all_admin_notices']->callbacks ) ) {
			foreach ( $wp_filter['all_admin_notices']->callbacks as $priority => $hooks ) {
				foreach ( $hooks as $name => $arr ) {
					if ( is_object( $arr['function'] ) && $arr['function'] instanceof \Closure ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
						continue;
					}
					if ( ! empty( $arr['function'][0] ) && is_object( $arr['function'][0] ) && strpos( strtolower( get_class( $arr['function'][0] ) ), 'zpm_admin_notice' ) !== false ) {
						continue;
					}
					if ( ! empty( $name ) && strpos( strtolower( $name ), 'zpm_admin_notice' ) === false ) {
						unset( $wp_filter['all_admin_notices']->callbacks[ $priority ][ $name ] );
					}
				}
			}
		}
	}

	/**
	* Sets all the main plugin pages
	*/
	public static function get_pages() {
		$callbacks = new AdminCallbacks();
      	$access_level = Utillities::get_access_level();
		$pages = array(
			array(
				'page_title' => sprintf( __('%s Project Manager', 'zephyr-project-manager'), zpm_get_company_name() ),
				'menu_title' => sprintf( __('%s Project Manager', 'zephyr-project-manager'), zpm_get_company_name() ),
				'capability' => $access_level, 
				'menu_slug'  => 'zephyr_project_manager', 
				'callback'   => array( $callbacks, 'adminDashboard' ), 
				'icon_url'   => ZPM_PLUGIN_URL . 'assets/img/logo.png', 
				'position'   => 110
			)
		);

		return $pages;
	}

	/**
	* Sets all the plugin subpages
	*/
	public static function get_sub_pages() {
		$callbacks = new AdminCallbacks();
		$access_level = Utillities::get_access_level();

		if ( apply_filters( 'zpm_hide_zephyr', false ) ) {
			return [];
		}

		$subpages = array(
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Dashboard', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Dashboard', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager', 
				'callback'    => array( $callbacks, 'adminDashboard' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Projects', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Projects', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_projects', 
				'callback'    => array( $callbacks, 'adminProjects' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Tasks', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Tasks', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_tasks', 
				'callback'    => array( $callbacks, 'adminTasks' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'File Manager', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Files', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_files', 
				'callback'    => array( $callbacks, 'adminFiles' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Activity', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Activity', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_activity', 
				'callback'    => array( $callbacks, 'adminActivity' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Calendar', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Calendar', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_calendar', 
				'callback'    => array( $callbacks, 'adminCalendar' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Categories, Priorities & Statuses', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Categories, Priorities & Statuses', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_categories', 
				'callback'    => array( $callbacks, 'adminCategories' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Devices', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Devices', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_devices', 
				'callback'    => array( $callbacks, 'devicesPage' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Settings', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Settings', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_settings', 
				'callback'    => array( $callbacks, 'adminSettings' )
			),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Teams & Members', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Teams & Members', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_teams_members', 
				'callback'    => array( $callbacks, 'adminTeamsMembers' )
			), 
			// TODO: Add in next version
			// array(
			// 	'parent_slug' => 'zephyr_project_manager',
			// 	'page_title'  => __( 'Extensions', 'zephyr-project-manager' ), 
			// 	'menu_title'  => __( 'Extensions', 'zephyr-project-manager' ), 
			// 	'capability'  => $access_level, 
			// 	'menu_slug'   => 'zephyr_project_manager_extensions', 
			// 	'callback'    => array( $callbacks, 'extensionPage' )
			// ),
			array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Help', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Help', 'zephyr-project-manager' ), 
				'capability'  => $access_level, 
				'menu_slug'   => 'zephyr_project_manager_help', 
				'callback'    => array( $callbacks, 'help_page' )
			)
		);
		if (!BaseController::is_pro()) {
			$subpages[] = array(
				'parent_slug' => 'zephyr_project_manager',
				'page_title'  => __( 'Zephyr Pro', 'zephyr-project-manager' ), 
				'menu_title'  => __( 'Get Premium', 'zephyr-project-manager' ), 
				'capability'  => 'manage_options', 
				'menu_slug'   => 'zephyr_project_manager_purchase_premium', 
				'callback'    => array( $callbacks, 'purchase_premium' )
			);
		}

		return $subpages;
	}

	/**
	* Adds the custom mime types
	*/
	function custom_mime_types( $mime_types ) {
		$mime_types['json'] = 'application/json';
		return $mime_types;
	}

	/**
	* Adds the Dashboard Widgets to the Dashboard
	*/
	public function setup_dashboard_widget() {
		$project_count = Projects::project_count();
		$user_settings = Utillities::get_user_settings( get_current_user_id() );

		if ( isset( $user_settings['hide_dashboard_widgets'] ) ) {
			if ($user_settings['hide_dashboard_widgets'] == true) {
				return;
			}
		} else {
			return;
		}

		if ($user_settings['can_zephyr'] == "false") {
			return;
		}

		if ($project_count > 0) {
			wp_add_dashboard_widget(
	            'zpm_dashboard_overview',
	            __( 'Zephyr Latest Projects', 'zephyr-project-manager' ),
	            array($this, 'render_dashboard_widget' )
	        );
		}
		
		// WP Dashboard Tasks
        wp_add_dashboard_widget(
            'zpm_dashboard_tasks_overview',
            __( 'Zephyr Tasks', 'zephyr-project-manager' ),
            array($this, 'render_dashboard_tasks_widget' )
        );
	}

	/**
	* Renders the dashboard widget to display a project progress overview and the progress for the three latest projects
	*/
	public function render_dashboard_widget() {
		$project_count = Projects::project_count();
		$completed_projects = Projects::completed_project_count();
		$pending_projects = $project_count - $completed_projects;
		$latest_projects = Projects::get_projects(3);
		
		$colors = array(
			'#448AFF',
			'#7B1FA2',
			'#E91E63',
		); ?>

		<div id="zpm_dashboard_chart">
			<canvas id="zpm-dashboard-project-chart" data-project-total="<?php echo $project_count; ?>" data-project-completed="<?php echo $completed_projects; ?>" data-project-pending="<?php echo $pending_projects; ?>" width="100" height="100"></canvas>
		</div>
		<div id="zpm_project_overview">
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $project_count; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Projects', 'zephyr-project-manager' ); ?></span>
			</span>
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $completed_projects; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Completed', 'zephyr-project-manager' ); ?></span>
			</span>
			<span class="zpm_project_stat_section">
				<span class="zpm_project_stat_status"><?php echo $pending_projects; ?></span>
				<span class="zpm_project_stat_title"><?php _e( 'Pending', 'zephyr-project-manager' ); ?></span>
			</span>
		</div>

		<div class="zpm_dashboard_projects">

			<?php if (sizeof( (array) $latest_projects ) > 0) : ?>
				<h3 id="zpm_dashboard_heading">Latest Projects</h3>
				<ul class="zpm_dashboard_project_list">
					<?php $i = 0; ?>
					<?php foreach ($latest_projects as $project) : ?>
						<?php $project_progress = Projects::percent_complete($project->id); ?>
						<li class="zpm_dashboard_project">
							<span class="zpm_dashboard_project_name"><?php echo $project->name; ?></span>
							<span class="zpm_dashboard_project_progress">
								<span class="zpm_dashboard_progress_bar">
									<span class="zpm_dashboard_progress_indicator zpm_color_<?php echo $i; ?>" style="width: <?php echo $project_progress . '%'; ?>; background-color: <?php echo $colors[$i] ?>"><?php echo $project_progress . '%' ?></span>
								</span>
							</span>
						</li>
						<?php $i++; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
			<div class="zpm-dashboard-widget-buttons">
				<a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_projects')); ?>" class="zpm_button"><?php _e( 'View All Projects', 'zephyr-project-manager' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	* Renders the dashboard widget to display a task overview of your tasks for the week and overdue tasks
	*/
	public function render_dashboard_tasks_widget() {
		$args = array(
			'limit' => 5,
			'assignee' => get_current_user_id()
		);
		$my_tasks = Tasks::get_tasks($args);
		$week_tasks = Tasks::get_week_tasks(get_current_user_id());
		$args = array( 'assignee' => get_current_user_id() );
		$overdue_tasks = Tasks::get_overdue_tasks($args); ?>

		<?php if (Tasks::get_task_count() <= 0) : ?>
			<p><?php _e( 'There are no tasks to view at the moment.', 'zephyr-project-manager' ); ?></p>
			<?php return; ?>
		<?php endif; ?>
		<h3 class="zpm_dashboard_heading"><?php _e( 'My Tasks Due This Weeks', 'zephyr-project-manager' ); ?>:</h3>
		<ul class="zpm_admin_list">
			<?php foreach($week_tasks as $task) : ?>
				<?php $due_date = date('D', strtotime($task->date_due)); ?>
				<li class="zpm-dashboard-list-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>"><?php echo stripslashes($task->name); ?><span class="zpm_widget_date zpm_date_pending"><?php echo $due_date; ?></span></a></li>
			<?php endforeach; ?>
		</ul>
		<?php if (empty($week_tasks)) : ?>
			<p><?php _e( 'You have no tasks due this week', 'zephyr-project-manager' ); ?>.</p>
		<?php endif; ?>

		<h3 class="zpm_dashboard_heading"><?php _e( 'My Overdue Tasks', 'zephyr-project-manager' ); ?>:</h3>
		<ul class="zpm_admin_list">
			<?php foreach($overdue_tasks as $task) : ?>
				<?php if ($task->date_due == '0000-00-00 00:00:00') { continue; } ?>
				<?php $due_date = date('d M', strtotime($task->date_due)); ?>
				<li class="zpm-dashboard-list-item"><a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')) . '&action=view_task&task_id=' . $task->id ?>" class=""><?php echo stripslashes($task->name); ?><span class="zpm_widget_date zpm_date_overdue"><?php echo $due_date; ?></span></a></li>
			<?php endforeach; ?>
		</ul>

		<?php if (empty($overdue_tasks)) : ?>
			<p><?php _e( 'You have no overdue tasks.', 'zephyr-project-manager' ); ?></p>
		<?php endif; ?>
		
		<div class="zpm-dashboard-widget-buttons">
			<a href="<?php echo esc_url(admin_url('/admin.php?page=zephyr_project_manager_tasks')); ?>" class="zpm_button"><?php _e( 'See All Tasks', 'zephyr-project-manager' ); ?></a>
		</div>

		<?php
	}

	public function render_dashboard_projects() {
		$dashboard_projects = Projects::get_dashboard_projects();
		?>
		<div class="zpm_panel_container">
		<?php
		foreach ($dashboard_projects as $project) :
			?>
			<div class="zpm_panel_12 zpm_dashboard_project_container">
				<div class="zpm_panel zpm_chart_panel zpm_dashboard_project" data-project-id="<?php echo $project->id; ?>">
					<?php $chart_data = get_option('zpm_chart_data', array()); ?>
					<h4 class="zpm_panel_heading"><?php echo $project->name; ?></h4>
					<span class="zpm_remove_project_from_dashboard lnr lnr-cross-circle"></span>
					<canvas id="zpm_line_chart" class="zpm-dashboard-project-chart" width="600" height="350" data-project-id="<?php echo $project->id; ?>" data-chart-data='<?php echo json_encode($chart_data[$project->id]); ?>'></canvas>

				</div>
			</div>
		<?php
		endforeach;
		?>
		</div>
		<?php
	}

	/**
	* Displays the admin notice for a user to view the Zephyr page when they have not used it before
	*/
	public function first_time_use() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	        <p><?php printf( __( 'Get started with Zephyr Project Manager now from %s here %s', 'zephyr-project-manager' ), '<a href="' . esc_url(admin_url('/admin.php?page=zephyr_project_manager')) . '" class="zpm_link">', '</a>' ); ?></p>
	    </div>
	    <?php
	}

	/**
	* Displays the review notice
	*/
	public function review_notice() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	    	<span id="zpm_dismiss_review_notice" class="lnr lnr-cross-circle"></span>
	        <p><?php _e( 'Thanks for using Zephyr Project Manager. If you enjoy it, could you please consider leaving a review? It would really mean the world to me!', 'zephyr-project-manager' ); ?></p>
	        <button class="zpm_button"><a href="https://wordpress.org/support/plugin/zephyr-project-manager/reviews/" target="_blank"><?php _e( 'Leave a Review', 'zephyr-project-manager' ); ?></a></button>
	    </div>
	    <?php
	}

	/**
	* Displays the welcome notice
	*/
	public function welcome_notice() {
		?>
	    <div class="zpm_update_notice zpm_admin_notice update notice">
	    	<span id="zpm_dismiss_welcome_notice" class="lnr lnr-cross-circle"></span>
	        <h4 class="zpm_notice_heading"><?php _e('Welcome to Zephyr Project Manager', 'zephyr-project-manager'); ?></h4>
			<p class="zpm_panel_description">
				<?php _e('Thanks for using Zephyr Project Manager. If you experience any problems or have any feature requests, I would be more than happy to add them. Please contact me at dylanjkotze@gmail.com for any queries.', 'zephyr-project-manager') ?>
			</p>
	    </div>
	    <?php
	}

	public function task_list_custom_data( $content, $task ) {
		return $content;
	}

	public function filter_media_files( $wp_query ) {
	    // if ( strpos( $_SERVER[ 'REQUEST_URI' ], '/wp-admin/upload.php' ) !== false ) {
	    //     global $current_user;
	    //         $wp_query->set( 'author', $current_user->id );
	    // }
	}

	public function project_tabs( $content, $project_id ) {
		return $content;
	}

	public function newUserCreated( $userId ) {
		// When a new user is created, remove their Zephyr Access by default
		if (!Utillities::is_zephyr_role($userId)) {
			Utillities::update_user_access($userId, false);
		}
	}
}