<?php
/**
 * Class representing the account data returned from the Dropbox API.
 *
 * @since 2.9
 *
 * @package Gravity_Forms\Gravity_Forms_Dropbox
 */

namespace Gravity_Forms\Gravity_Forms_Dropbox;

/**
 * Class Account
 *
 * @since 2.9
 *
 * @package Gravity_Forms\Gravity_Forms_Dropbox
 */
class Account {
	/**
	 * Account data from the Dropbox API.
	 *
	 * @since 2.9
	 * @var object
	 */
	private $data;

	/**
	 * Type of account.
	 *
	 * @var string
	 */
	private $type;

	/**
	 * Team data.
	 *
	 * @since 2.9
	 * @var object
	 */
	private $team_info;

	/**
	 * Team member ID of the account.
	 *
	 * @since 2.9
	 * @var string
	 */
	private $team_member_id = '';

	/**
	 * Account constructor.
	 *
	 * @param object $data Account data from the Dropbox API.
	 *
	 * @since 2.9
	 */
	public function __construct( $data ) {
		if ( ! is_object( $data ) ) {
			return;
		}

		$this->data = $data;
		$this->type = isset( $data->account_type->{'.tag'} ) ? $data->account_type->{'.tag'} : null;

		if ( $this->is_team_account() ) {
			$this->team_info      = $this->data->team;
			$this->team_member_id = $this->data->team_member_id;
		}
	}

	/**
	 * Check whether the account is a team account.
	 *
	 * @since 2.9
	 * @return bool
	 */
	public function is_team_account() {
		return property_exists( $this->data, 'team_member_id' );
	}

	/**
	 * Get the team member ID.
	 *
	 * @since 2.9
	 * @return string
	 */
	public function get_team_member_id() {
		return $this->team_member_id;
	}

	/**
	 * Get the information about the team.
	 *
	 * @since 2.9
	 * @return object
	 */
	public function get_team_info() {
		return $this->team_info;
	}
}
