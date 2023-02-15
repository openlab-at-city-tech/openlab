<?php

namespace OpenLab;

/**
 * Async class.
 */
class Clone_Async_Process extends WP_Async_Request {
	protected $action = 'openlab_clone_async_process';

	protected function handle() {
		if ( empty( $_POST['group_id'] ) ) {
			return;
		}

		$group_id = (int) $_POST['group_id'];

		$running = groups_get_groupmeta( $group_id, 'clone_in_progress', true );
		if ( $running && ( ( time() - $running ) < ( 5 * MINUTES_IN_SECONDS ) ) ) {
			return;
		}

		$steps = groups_get_groupmeta( $group_id, 'clone_steps', true );

		// Nothing to do.
		if ( ! $steps ) {
			return;
		}

		groups_update_groupmeta( $group_id, 'clone_in_progress', time() );

		$the_step = reset( $steps );

		remove_action( 'bp_activity_after_save', 'ass_group_notification_activity', 50 );

		$source_group_id = groups_get_groupmeta( $group_id, 'clone_source_group_id', true );
		$group_cloner    = new \OpenLab_Clone_Course_Group( $group_id, $source_group_id );

		switch ( $the_step ) {
			case 'groupmeta' :
				$group_cloner->migrate_groupmeta();
			break;

			case 'avatar' :
				$group_cloner->migrate_avatar();
			break;

			case 'docs' :
				$group_cloner->migrate_docs();
			break;

			case 'files' :
				$group_cloner->migrate_files();
			break;

			case 'topics' :
				$group_cloner->migrate_topics();
			break;

			case 'site' :
				$source_site_id         = groups_get_groupmeta( $group_id, 'clone_source_blog_id', true );
				$clone_destination_path = groups_get_groupmeta( $group_id, 'clone_destination_path', true );
				openlab_clone_course_site( $group_id, $source_group_id, $source_site_id, $clone_destination_path );
			break;
		}

		add_action( 'bp_activity_after_save', 'ass_group_notification_activity', 50 );

		$new_steps = array_diff( $steps, [ $the_step ] );
		if ( $new_steps ) {
			groups_update_groupmeta( $group_id, 'clone_steps', $new_steps );
		} else {
			groups_delete_groupmeta( $group_id, 'clone_steps' );
		}

		groups_delete_groupmeta( $group_id, 'clone_in_progress' );

		if ( $new_steps ) {
			$this->data( [ 'group_id' => $group_id ] )->dispatch();
		}
	}
}
