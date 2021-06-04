<?php

namespace OpenLab\Favorites\Favorite;

use OpenLab\Favorites\Schema;

class Favorite {
	protected $data = array(
		'date_created' => '0000-00-00 00:00:00',
		'group_id'     => 0,
		'id'           => 0,
		'user_id'      => 0,
	);

	public function __construct( $id = null ) {
		if ( null !== $id ) {
			$this->populate( $id );
		}
	}

	public function exists() {
		return $this->get_id() > 0;
	}

	public function save() {
		global $wpdb;

		$is_new = ( $this->get_id() === 0 );

		$table_name = Schema::get_table_name();

		if ( $is_new ) {
			$wpdb->insert(
				$table_name,
				array(
					'user_id'      => $this->get_user_id(),
					'group_id'     => $this->get_group_id(),
					'date_created' => $this->get_date_created(),
				),
				array(
					'%d', // user_id
					'%d', // group_id
					'%s', // date_created
				)
			);

			$id = $wpdb->insert_id;
			$this->set_id( $id );
		} else {
			$wpdb->update(
				$table_name,
				array(
					'user_id'      => $this->get_user_id(),
					'group_id'     => $this->get_group_id(),
					'date_created' => $this->get_date_created(),
				),
				array(
					'id' => $this->get_id(),
				),
				array(
					'%d', // user_id
					'%d', // group_id
					'%s', // date_created
				),
				array(
					'%d',
				)
			);
		}

		return true;
	}

	public function delete() {
		global $wpdb;

		$table_name = Schema::get_table_name();

		$deleted = $wpdb->delete(
			$table_name,
			[
				'id' => $this->get_id(),
			],
			[
				'%d',
			],
		);

		return (bool) $deleted;
	}

	public function populate( $id ) {
		global $wpdb;

		$table_name = Schema::get_table_name();

		$row = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ) );
		if ( ! $row ) {
			return;
		}

		$this->set_id( $row->id );
		$this->set_user_id( $row->user_id );
		$this->set_group_id( $row->group_id );
		$this->set_date_created( $row->date_created );
	}

	/**
	 * Get invitation ID.
	 *
	 * @return int
	 */
	public function get_id() {
		return $this->data['id'];
	}

	/**
	 * Get user ID.
	 *
	 * @return int
	 */
	public function get_user_id() {
		return (int) $this->data['user_id'];
	}

	/**
	 * Get group ID.
	 *
	 * @return int
	 */
	public function get_group_id() {
		return (int) $this->data['group_id'];
	}

	/**
	 * Get date created (UTC).
	 *
	 * @return string In 'Y-m-d H:i:s' format.
	 */
	public function get_date_created() {
		return $this->data['date_created'];
	}

	/**
	 * Get group name.
	 *
	 * @return string
	 */
	public function get_group_name() {
		$group = groups_get_group( $this->get_group_id() );
		return $group->name;
	}

	/**
	 * Get group URL.
	 *
	 * @return string
	 */
	public function get_group_url() {
		$group = groups_get_group( $this->get_group_id() );
		return bp_get_group_permalink( $group );
	}

	/**
	 * Set invitation ID.
	 *
	 * @param int $id
	 *
	 * @return int
	 */
	public function set_id( $id ) {
		$this->data['id'] = (int) $id;
	}

	/**
	 * Set user ID.
	 *
	 * @param int
	 */
	public function set_user_id( $id ) {
		$this->data['user_id'] = intval( $id );
	}

	/**
	 * Set group ID.
	 *
	 * @param int
	 */
	public function set_group_id( $id ) {
		$this->data['group_id'] = intval( $id );
	}

	/**
	 * Set date created.
	 *
	 * Expects a UTC timestamp in 'Y-m-d H:i:s' format.
	 *
	 * @param string
	 */
	public function set_date_created( $date ) {
		$this->data['date_created'] = $date;
	}
}
