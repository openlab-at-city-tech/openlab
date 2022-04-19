<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

use Inc\Api\Emails;
use Inc\Core\Projects;

class Core {

	public function register( ) {

		if (zpmIsProjectsPage()) {
			add_filter( 'zpm_after_quickmenu', array( $this, 'projectsPageMenuOptions' ) );
		}

		if (zpmIsTasksPage()) {
			add_filter( 'zpm_after_quickmenu', array( $this, 'tasksPageMenuOptions' ) );
		}

		add_filter( 'zpm_category_projects', array( $this, 'filterCategoryProjects' ) );

		add_action( 'zpm_project_completed', array( $this, 'projectCompleted' ) );
		add_action( 'zpm_project_assigned', array( $this, 'projectAssigned' ), 10, 2 );

	}

	public function projectCompleted( $project ) {
		$managers = Members::getManagers();

		$header = __( 'Project Completed', 'zephyr-project-manager' );
		$subject = __( 'Project Completed', 'zephyr-project-manager' );
		$body = sprintf( __( 'Project "%s" has been completed.', 'zephyr-project-manager' ), $project->name );
		$footer = '';

		$html = Emails::email_template( $header, $body, $footer );

		foreach ($managers as $manager) {
			Emails::send_email( $manager['email'], $subject, $html );
		}
	}

	public function projectAssigned( $project, $assignees ) {
		$managers = Members::getManagers();

		$header = __( 'Project Assigned to You', 'zephyr-project-manager' );
		$subject = __( 'Project Assigned to You', 'zephyr-project-manager' );
		$body = sprintf( __( 'Project "%s" has been assigned to you.', 'zephyr-project-manager' ), $project->name );
		$footer = '';

		$html = Emails::email_template( $header, $body, $footer );

		foreach ($assignees as $assignee) {
			if (isset($assignee['email'])) {
					Emails::send_email( $assignee['email'], $subject, $html );
			}
		}
	}

	public function filterCategoryProjects( $projects ) {
		$results = [];

		foreach ($projects as $project) {
			if ( Projects::has_project_access( $project ) ) {
				$results[] = $project;
			}
		}
		return $results;
	}

	public function projectsPageMenuOptions( $content ) {
		$content .= "<li class='zpm_fancy_item zpm-export-projects__btn'>" . __( 'Export Projects', 'zephyr-project-manager' ) . "</li>";
		$content .= "<li class='zpm_fancy_item zpm-import-projects__btn'>" . __( 'Import Projects', 'zephyr-project-manager' ) . "</li>";
      	return $content;
	}

	public function tasksPageMenuOptions( $content ) {
		$content .= "<li class='zpm_fancy_item zpm-export-tasks__btn'>" . __( 'Export Tasks', 'zephyr-project-manager' ) . "</li>";
		$content .= "<li class='zpm_fancy_item zpm-import-tasks__btn'>" . __( 'Import Tasks', 'zephyr-project-manager' ) . "</li>";
      	return $content;
	}
}
