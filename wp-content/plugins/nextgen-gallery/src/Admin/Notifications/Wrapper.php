<?php

namespace Imagely\NGG\Admin\Notifications;

/**
 * Notification Wrapper class.
 *
 * Wraps notification data for display in the admin interface.
 */
class Wrapper {

	/**
	 * Notification name.
	 *
	 * @var string
	 */
	public $_name;

	/**
	 * Notification data.
	 *
	 * @var array
	 */
	public $_data;

	/**
	 * Constructor.
	 *
	 * @param string $name Notification name.
	 * @param array  $data Notification data.
	 */
	public function __construct( $name, $data ) {
		$this->_name = $name;
		$this->_data = $data;
	}

	/**
	 * Checks if the notification is renderable.
	 *
	 * @return bool Always returns true.
	 */
	public function is_renderable() {
		return true;
	}

	/**
	 * Checks if the notification is dismissable.
	 *
	 * @return bool Always returns true.
	 */
	public function is_dismissable() {
		return true;
	}

	/**
	 * Renders the notification content.
	 *
	 * @return string The notification message.
	 */
	public function render() {
		return $this->_data['message'];
	}
}
