<?php

/**
 * Connection Invitation object.
 */

class OpenLab_Group_Connection_Invitation {
	/**
	 * Invitation ID.
	 *
	 * @var int
	 */
	protected $invitation_id;

	/**
	 * Inviter group ID.
	 *
	 * @var int
	 */
	protected $inviter_group_id;

	/**
	 * Invitee group ID.
	 *
	 * @var int
	 */
	protected $invitee_group_id;

	/**
	 * Inviter user ID.
	 *
	 * @var int
	 */
	protected $inviter_user_id;

	/**
	 * Connection user ID.
	 *
	 * @var int
	 */
	protected $connection_id;

	/**
	 * Date created.
	 *
	 * @var string
	 */
	protected $date_created = '0000-00-00 00:00:00';

	/**
	 * Date accepted.
	 *
	 * @var string
	 */
	protected $date_accepted = '0000-00-00 00:00:00';

	/**
	 * Sets the invitation ID for this invitation.
	 *
	 * @param int $invitation_id Invitation ID.
	 */
	public function set_invitation_id( $invitation_id ) {
		$this->invitation_id = (int) $invitation_id;
	}

	/**
	 * Sets the inviter group ID for this invitation.
	 *
	 * @param int $inviter_group_id Inviter group ID.
	 */
	public function set_inviter_group_id( $inviter_group_id ) {
		$this->inviter_group_id = (int) $inviter_group_id;
	}

	/**
	 * Sets the invitee group ID for this invitation.
	 *
	 * @param int $invitee_group_id Invitee group ID.
	 */
	public function set_invitee_group_id( $invitee_group_id ) {
		$this->invitee_group_id = (int) $invitee_group_id;
	}

	/**
	 * Sets the inviter user ID for this invitation.
	 *
	 * @param int $inviter_user_id Inviter user ID.
	 */
	public function set_inviter_user_id( $inviter_user_id ) {
		$this->inviter_user_id = (int) $inviter_user_id;
	}

	/**
	 * Sets the connection ID for this invitation.
	 *
	 * @param int $connection_id Connection ID.
	 */
	public function set_connection_id( $connection_id ) {
		$this->connection_id = (int) $connection_id;
	}

	/**
	 * Sets the date_created for this invitation.
	 *
	 * @param int $date_created Date created, in MySQL format.
	 */
	public function set_date_created( $date_created ) {
		$this->date_created = $date_created;
	}

	/**
	 * Sets the date_accepted for this invitation.
	 *
	 * @param int $date_accepted Date accepted, in MySQL format.
	 */
	public function set_date_accepted( $date_accepted ) {
		$this->date_accepted = $date_accepted;
	}

	/**
	 * Saves the invitation.
	 *
	 * @return bool
	 */
	public function save() {
		global $wpdb;

		$table_name = self::get_table_name();

		$retval = false;
		if ( $this->invitation_id ) {
			$updated = $wpdb->update(
				$table_name,
				[
					'inviter_group_id' => $this->inviter_group_id,
					'invitee_group_id' => $this->invitee_group_id,
					'inviter_user_id'  => $this->inviter_user_id,
					'connection_id'    => $this->connection_id,
					'date_created'     => $this->date_created,
					'date_accepted'    => $this->date_accepted,
				],
				[
					'invitation_id' => $this->invitation_id,
				],
				[
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
				],
				[
					'%d',
				]
			);

			$retval = (bool) $updated;
		} else {
			$inserted = $wpdb->insert(
				$table_name,
				[
					'inviter_group_id' => $this->inviter_group_id,
					'invitee_group_id' => $this->invitee_group_id,
					'inviter_user_id'  => $this->inviter_user_id,
					'connection_id'    => $this->connection_id,
					'date_created'     => $this->date_created,
					'date_accepted'    => $this->date_accepted,
				],
				[
					'%d',
					'%d',
					'%d',
					'%d',
					'%s',
					'%s',
				]
			);

			if ( $inserted ) {
				$retval = true;
				$this->set_invitation_id( $wpdb->insert_id );
			}
		}

		wp_cache_delete( $this->invitation_id, 'openlab_connection_invitations' );

		return $retval;
	}

	/**
	 * Deletes the invitation.
	 *
	 * @return bool
	 */
	public function delete() {
		global $wpdb;

		$table_name = self::get_table_name();

		// Delete the invitation from the database.
		$deleted = $wpdb->delete(
			$table_name,
			array(
				'invitation_id' => $this->invitation_id,
			),
			array(
				'%d',
			)
		);

		// Invalidate the cache for the deleted invitation.
		wp_cache_delete( $this->invitation_id, 'openlab_connection_invitations' );

		return (bool) $deleted;
	}

	/**
	 * Gets the table name for the invitations table.
	 *
	 * @return string
	 */
	protected static function get_table_name() {
		global $wpdb;

		$table_prefix = $wpdb->get_blog_prefix( get_main_site_id() );

		return "{$table_prefix}openlab_connection_invitations";
	}

	/**
	 * Checks whether an invitation exists for a given group pair.
	 *
	 * @param int $inviter_group_id Inviter group ID.
	 * @param int $invitee_group_id Invitee group ID.
	 * @return bool
	 */
	public static function invitation_exists( $inviter_group_id, $invitee_group_id ) {
		$found = self::get(
			[
				'inviter_group_id' => $inviter_group_id,
				'invitee_group_id' => $invitee_group_id,
			]
		);

		return ! empty( $found );
	}

	/**
	 * Returns an instance based on invitation_id.
	 *
	 * @param int $invitation_id ID of the invitation.
	 * @return null|OpenLab_Group_Connection_Invitation
	 */
	public static function get_instance( $invitation_id ) {
		global $wpdb;

		$cached = wp_cache_get( $invitation_id, 'openlab_connection_invitations' );
		if ( is_array( $cached ) ) {
			$row = $cached;
		} else {
			$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE invitation_id = %d", self::get_table_name(), $invitation_id ) );

			wp_cache_set( $invitation_id, $row, 'openlab_connection_invitations' );
		}

		if ( ! $row ) {
			return null;
		}

		$invitation = new self();
		$invitation->set_invitation_id( (int) $row->invitation_id );
		$invitation->set_inviter_group_id( (int) $row->inviter_group_id );
		$invitation->set_invitee_group_id( (int) $row->invitee_group_id );
		$invitation->set_inviter_user_id( (int) $row->inviter_user_id );
		$invitation->set_date_created( $row->date_created );
		$invitation->set_date_accepted( $row->date_accepted );

		return $invitation;
	}

	/**
	 * Fetches invitations based on parameters.
	 *
	 * @param array $args {
	 *   Array of optional query arguments.
	 *   @var int $inviter_group_id Inviter group ID.
	 *   @var int $invitee_group_id Invitee group ID.
	 * }
	 * @return array
	 */
	public static function get( $args = [] ) {
		global $wpdb;

		$table_name = self::get_table_name();

		$sql = [
			'select' => $wpdb->prepare( 'SELECT invitation_id FROM %i', $table_name ),
			'where'  => [],
		];

		$int_fields = [ 'invitation_id', 'inviter_group_id', 'invitee_group_id' ];
		foreach ( $int_fields as $int_field ) {
			if ( ! isset( $args[ $int_field ] ) || null === $args[ $int_field ] ) {
				continue;
			}

			$sql['where'][ $int_field ] = $wpdb->prepare( '%i = %d', $int_field, $args[ $int_field ] );
		}

		$sql_statement = "{$sql['select']} WHERE " . implode( ' AND ', $sql['where'] );

		$invitation_ids = $wpdb->get_col( $sql_statement );

		return array_map(
			function( $invitation_id ) {
				return self::get_instance( $invitation_id );
			},
			$invitation_ids
		);
	}
}
