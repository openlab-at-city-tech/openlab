<?php
/**
 * Group invitations class.
 *
 * @package BuddyPress
 * @subpackage Core
 * @since 5.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
 * Group invitations class.
 *
 * An extension of the core Invitations class that adapts the
 * core logic to accommodate group invitation behavior.
 *
 * @since 5.0.0
 */
class OpenLab_Connection_Invitation_Manager extends BP_Invitation_Manager {
	/**
	 * Processes the sending of an invitation.
	 *
	 * @param \BP_Invitation $invitation The ID of the invitation to mark as sent.
	 * @return bool True on success, false on failure.
	 */
	public function run_send_action( BP_Invitation $invitation ) {
		return true;
	}

	/**
	 * This is where custom actions are added to run when an invitation
	 * or request is accepted.
	 *
	 * @since 5.0.0
	 *
	 * @param string $type Are we accepting an invitation or request?
	 * @param array  $r    Parameters that describe the invitation being accepted.
	 * @return bool True on success, false on failure.
	 */
	public function run_acceptance_action( $type, $r ) {
		if ( ! $type || ! in_array( $type, array( 'request', 'invite' ), true ) ) {
			return false;
		}

		return true;
	}
}
