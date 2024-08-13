<?php

namespace PDFEmbedder\Tasks;

use ActionScheduler_DataController;
use ActionScheduler_DBStore;
use PDFEmbedder\Tasks\UsageTracking\SendUsageTask;

/**
 * Class Tasks manages the tasks queue and provides API to work with it.
 *
 * @since 4.7.0
 */
class Tasks {

	/**
	 * Group that will be assigned to all actions.
	 *
	 * @since 4.7.0
	 */
	const GROUP = 'pdfembedder';

	/**
	 * PDFEmbedder pending or in-progress actions.
	 *
	 * @since 4.7.0
	 *
	 * @var array
	 */
	private static $active_actions;

	/**
	 * Perform certain things on class init.
	 *
	 * @since 4.7.0
	 */
	public function init() { // phpcs:ignore WPForms.PHP.HooksMethod.InvalidPlaceForAddingHooks

		// Hide the Action Scheduler admin menu item.
		add_action( 'admin_menu', [ $this, 'admin_hide_as_menu' ], PHP_INT_MAX - 1 );

		// Skip tasks registration if Action Scheduler is not usable yet.
		if ( ! self::is_usable() ) {
			return;
		}

		// Register tasks.
		foreach ( $this->get_tasks() as $task ) {
			if ( ! is_subclass_of( $task, Task::class ) ) {
				continue;
			}

			$new_task = new $task();

			// Run the init method, if a task has one defined.
			if ( method_exists( $new_task, 'init' ) ) {
				$new_task->init();
			}
		}
	}

	/**
	 * Get the list of default scheduled tasks.
	 * Tasks, that are fired under certain specific circumstances
	 * (like sending emails) are not listed here.
	 *
	 * @since 4.7.0
	 */
	public function get_tasks(): array {

		// A list of default tasks included with the plugin.
		$tasks = [
			SendUsageTask::ACTION => SendUsageTask::class,
		];

		/**
		 * Filters list of tasks classes.
		 *
		 * @since 4.7.0
		 *
		 * @param Task[] $tasks List of tasks classes.
		 */
		return apply_filters( 'pdfemb_tasks_get_tasks', $tasks );
	}

	/**
	 * Hide Action Scheduler admin area when not in debug mode.
	 *
	 * @since 4.7.0
	 */
	public function admin_hide_as_menu() {

		/**
		 * Filters whether to hide Action Scheduler admin area.
		 *
		 * @since 4.7.0
		 *
		 * @param bool $hide Whether to hide Action Scheduler admin area.
		 */
		if ( apply_filters( 'pdfemb_tasks_admin_hide_as_menu', true ) ) {
			remove_submenu_page( 'tools.php', 'action-scheduler' );
		}
	}

	/**
	 * Create a new task.
	 * Used for "inline" tasks, that require additional information
	 * from the plugin runtime before they can be scheduled.
	 *
	 * Example:
	 *     pdf_embedder()->get( 'tasks' )
	 *              ->create( 'i_am_the_dude' )
	 *              ->async()
	 *              ->params( 'The Big Lebowski', 1998 )
	 *              ->register();
	 *
	 * This `i_am_the_dude` action will be later processed as:
	 *     add_action( 'i_am_the_dude', 'thats_what_you_call_me' );
	 *
	 * @since 4.7.0
	 *
	 * @param string $action Action that will be used as a hook.
	 */
	public function create( string $action ): Task {

		return new Task( $action );
	}

	/**
	 * Cancel a specific action/task.
	 *
	 * @since 4.7.0
	 *
	 * @param string $task Task/action to cancel.
	 */
	public function cancel( string $task ) {

		if ( empty( $task ) ) {
			return;
		}

		if ( class_exists( 'ActionScheduler_DBStore' ) ) {
			ActionScheduler_DBStore::instance()->cancel_actions_by_hook( $task );
			self::$active_actions = self::get_active_actions();
		}
	}

	/**
	 * Cancel all the AS actions for a group.
	 *
	 * @since 4.7.0
	 *
	 * @param string $group Group to cancel all actions for.
	 */
	public function cancel_all( string $group = '' ) {

		if ( empty( $group ) ) {
			$group = self::GROUP;
		} else {
			$group = sanitize_key( $group );
		}

		if ( class_exists( 'ActionScheduler_DBStore' ) ) {
			ActionScheduler_DBStore::instance()->cancel_actions_by_group( $group );
			self::$active_actions = self::get_active_actions();
		}
	}

	/**
	 * Remove all the AS actions for a group and remove group.
	 *
	 * @since 4.7.0
	 *
	 * @param string $group Group to remove all actions for.
	 */
	public function remove_all( string $group = '' ) {

		global $wpdb;

		if ( empty( $group ) ) {
			$group = self::GROUP;
		} else {
			$group = sanitize_key( $group );
		}

		if (
			class_exists( 'ActionScheduler_DBStore' ) &&
			isset( $wpdb->actionscheduler_actions ) &&
			isset( $wpdb->actionscheduler_groups )
		) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$group_id = $wpdb->get_var(
				$wpdb->prepare( "SELECT group_id FROM {$wpdb->actionscheduler_groups} WHERE slug=%s", $group )
			);

			if ( ! empty( $group_id ) ) {
				// Delete actions.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->delete( $wpdb->actionscheduler_actions, [ 'group_id' => (int) $group_id ], [ '%d' ] );

				// Delete group.
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery
				$wpdb->delete( $wpdb->actionscheduler_groups, [ 'slug' => $group ], [ '%s' ] );
			}
		}
	}

	/**
	 * Whether ActionScheduler thinks that it has migrated or not.
	 *
	 * @since 4.7.0
	 *
	 * @return bool
	 */
	public static function is_usable(): bool {

		// No tasks if ActionScheduler wasn't loaded.
		if ( ! class_exists( 'ActionScheduler_DataController' ) ) {
			return false;
		}

		return ActionScheduler_DataController::is_migration_complete();
	}

	/**
	 * Whether task has been scheduled and is pending.
	 *
	 * @since 4.7.0
	 *
	 * @param string $hook Hook to check for.
	 */
	public static function is_scheduled( string $hook ): bool {

		// If ActionScheduler wasn't loaded, then no tasks are scheduled.
		if ( ! function_exists( 'as_next_scheduled_action' ) ) {
			return false;
		}

		if ( is_null( self::$active_actions ) ) {
			self::$active_actions = self::get_active_actions();
		}

		if ( in_array( $hook, self::$active_actions, true ) ) {
			return true;
		}

		// Action is not in the array, so it is not scheduled or belongs to another group.
		if ( function_exists( 'as_has_scheduled_action' ) ) {
			// This function more performant than `as_next_scheduled_action`, but it is available only since AS 3.3.0.
			return as_has_scheduled_action( $hook );
		}

		return as_next_scheduled_action( $hook ) !== false;
	}

	/**
	 * Get all PDFEmbedder pending or in-progress actions.
	 *
	 * @since 4.7.0
	 */
	private static function get_active_actions() {

		global $wpdb;

		$group = self::GROUP;
		$sql   = "SELECT a.hook FROM {$wpdb->prefix}actionscheduler_actions a
					JOIN {$wpdb->prefix}actionscheduler_groups g ON g.group_id = a.group_id
					WHERE g.slug = '$group' AND a.status IN ('in-progress', 'pending')";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery,WordPress.DB.PreparedSQL.NotPrepared
		$results = $wpdb->get_results( $sql, 'ARRAY_N' );

		return $results ? array_merge( ...$results ) : [];
	}
}
