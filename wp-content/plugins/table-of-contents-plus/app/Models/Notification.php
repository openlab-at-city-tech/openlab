<?php
namespace AIOSEO\TableOfContents\Models;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The Notification DB model class.
 *
 * @since 1.0.0
 */
class Notification extends Model {
	/**
	 * The name of the table in the database, without the prefix.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $table = 'aioseo_table_of_contents_notifications';

	/**
	 * An array of fields to set to null if already empty when saving to the database.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $nullFields = [
		'start',
		'end',
		'notification_id',
		'notification_name',
		'button1_label',
		'button1_action',
		'button2_label',
		'button2_action'
	];

	/**
	 * Fields that should be json encoded on save and decoded on get.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $jsonFields = [ 'level' ];

	/**
	 * Fields that should be boolean values.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $booleanFields = [ 'dismissed' ];

	/**
	 * Fields that should be hidden when serialized.
	 *
	 * @var array
	 */
	protected $hidden = [ 'id' ];

	/**
	 * An array of fields attached to this resource.
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $columns = [
		'id',
		'slug',
		'notification_id',
		'notification_name',
		'title',
		'content',
		'type',
		'level',
		'start',
		'end',
		'button1_label',
		'button1_action',
		'button2_label',
		'button2_action',
		'dismissed',
		'new',
		'created',
		'updated'
	];

	/**
	 * Returns all notifications.
	 *
	 * @since 1.0.0
	 *
	 * @param  bool  $reset Whether or not to reset the new notifications.
	 * @return array        List of notifications.
	 */
	public static function getNotifications( $reset = true ) {
		return [
			'active'    => self::getAllActiveNotifications(),
			'new'       => self::getNewNotifications( $reset ),
			'dismissed' => self::getAllDismissedNotifications()
		];
	}

	/**
	 * Returns the active notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of active notifications.
	 */
	public static function getAllActiveNotifications() {
		$staticNotifications = self::getStaticNotifications();
		$notifications       = array_values( json_decode( wp_json_encode( self::getActiveNotifications() ), true ) );

		return ! empty( $staticNotifications ) ? array_merge( $staticNotifications, $notifications ) : $notifications;
	}

	/**
	 * Returns all static notifications.
	 *
	 * @since 1.2.0
	 *
	 * @return array List of static notifications.
	 */
	public static function getStaticNotifications() {
		$notifications       = [ 'review' ];
		$staticNotifications = [];
		foreach ( $notifications as $notification ) {
			switch ( $notification ) {
				case 'review':
					// If dismissed, don't show again.
					$originalDismissed = get_user_meta( get_current_user_id(), '_aioseo_table_of_contents_plugin_review_dismissed', true );
					if ( '4' !== $originalDismissed ) {
						break;
					}

					$dismissed = get_user_meta( get_current_user_id(), '_aioseo_table_of_contents_notification_plugin_review_dismissed', true );
					if ( '3' === $dismissed ) {
						break;
					}

					if ( ! empty( $dismissed ) && $dismissed > time() ) {
						break;
					}

					$activated = aioseoTableOfContents()->internalOptions->internal->firstActivated( time() );
					if ( $activated > strtotime( '-20 days' ) ) {
						break;
					}

					$staticNotifications[] = [
						'slug'      => 'notification-' . $notification,
						'component' => 'notifications-' . $notification . '2',
					];
					break;
				default:
					break;
			}
		}

		return $staticNotifications;
	}

	/**
	 * Retrieve active notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of active notifications or empty.
	 */
	public static function getActiveNotifications() {
		return self::filterNotifications(
			aioseoTableOfContents()->core->db
				->start( 'aioseo_table_of_contents_notifications' )
				->where( 'dismissed', 0 )
				->whereRaw( "(start <= '" . gmdate( 'Y-m-d H:i:s' ) . "' OR start IS NULL)" )
				->whereRaw( "(end >= '" . gmdate( 'Y-m-d H:i:s' ) . "' OR end IS NULL)" )
				->orderBy( 'start DESC' )
				->orderBy( 'created DESC' )
				->run()
				->models( 'AIOSEO\\TableOfContents\\Models\\Notification' )
		);
	}

	/**
	 * Returns all new notifications. After retrieving them, this will reset them.
	 * This means that calling this method twice will result in no results
	 * the second time. The only exception is to pass false as a reset variable to prevent it.
	 *
	 * @since 1.0.0
	 *
	 * @param  boolean $reset Whether or not to reset the new notifications.
	 * @return array          List of new notifications if any exist.
	 */
	public static function getNewNotifications( $reset = true ) {
		$notifications = self::filterNotifications(
			aioseoTableOfContents()->core->db
				->start( 'aioseo_table_of_contents_notifications' )
				->where( 'dismissed', 0 )
				->where( 'new', 1 )
				->whereRaw( "(start <= '" . gmdate( 'Y-m-d H:i:s' ) . "' OR start IS NULL)" )
				->whereRaw( "(end >= '" . gmdate( 'Y-m-d H:i:s' ) . "' OR end IS NULL)" )
				->orderBy( 'start DESC' )
				->orderBy( 'created DESC' )
				->run()
				->models( 'AIOSEO\\TableOfContents\\Models\\Notification' )
		);

		if ( $reset ) {
			self::resetNewNotifications();
		}

		return $notifications;
	}

	/**
	 * Resets all new notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function resetNewNotifications() {
		aioseoTableOfContents()->core->db
			->update( 'aioseo_table_of_contents_notifications' )
			->where( 'new', 1 )
			->set( 'new', 0 )
			->run();
	}

	/**
	 * Returns a list of dismissed notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of dismissed notifications.
	 */
	public static function getAllDismissedNotifications() {
		return array_values( json_decode( wp_json_encode( self::getDismissedNotifications() ), true ) );
	}

	/**
	 * Retrieve dismissed notifications.
	 *
	 * @since 1.0.0
	 *
	 * @return array List of dismissed notifications or empty.
	 */
	public static function getDismissedNotifications() {
		return self::filterNotifications(
			aioseoTableOfContents()->core->db
				->start( 'aioseo_table_of_contents_notifications' )
				->where( 'dismissed', 1 )
				->orderBy( 'updated DESC' )
				->run()
				->models( 'AIOSEO\\TableOfContents\\Models\\Notification' )
		);
	}

	/**
	 * Returns a notification by its name.
	 *
	 * @since 1.0.0
	 *
	 * @param  string       $name The notification name.
	 * @return Notification       The notification.
	 */
	public static function getNotificationByName( $name ) {
		return aioseoTableOfContents()->core->db
			->start( 'aioseo_table_of_contents_notifications' )
			->where( 'notification_name', $name )
			->run()
			->model( 'AIOSEO\\TableOfContents\\Models\\Notification' );
	}

	/**
	 * Stores a new notification in the DB.
	 *
	 * @since 1.0.0
	 *
	 * @param  array        $fields       The fields.
	 * @return Notification $notification The notification.
	 */
	public static function addNotification( $fields ) {
		// Set the dismissed status to false.
		$fields['dismissed'] = 0;

		$notification = new self();
		$notification->set( $fields );
		$notification->save();

		return $notification;
	}

	/**
	 * Deletes a notification by its name.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $name The notification name.
	 * @return void
	 */
	public static function deleteNotificationByName( $name ) {
		aioseoTableOfContents()->core->db
			->delete( 'aioseo_table_of_contents_notifications' )
			->where( 'notification_name', $name )
			->run();
	}

	/**
	 * Filters the notifications based on the targeted plan levels.
	 *
	 * @since 1.0.0
	 *
	 * @param  array $notifications          The notifications
	 * @return array $remainingNotifications The remaining notifications.
	 */
	public static function filterNotifications( $notifications ) {
		$remainingNotifications = [];
		foreach ( $notifications as $notification ) {
			$levels = $notification->level;
			if ( ! is_array( $levels ) ) {
				$levels = empty( $notification->level ) ? [ 'all' ] : [ $notification->level ];
			}

			foreach ( $levels as $level ) {
				if ( ! aioseoTableOfContents()->notifications->validateType( $level ) ) {
					continue 2;
				}
			}

			$remainingNotifications[] = $notification;
		}

		return $remainingNotifications;
	}
}