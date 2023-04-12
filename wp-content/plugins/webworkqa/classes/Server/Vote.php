<?php

namespace WeBWorK\Server;

/**
 * Vote CRUD.
 */
class Vote {
	protected $id;
	protected $user_id;
	protected $item_id;
	protected $item;
	protected $value = 0;

	/**
	 * Whether the vote exists in the database.
	 */
	public function exists() {
		return (bool) $this->id;
	}

	public function set_id( $id ) {
		$this->id = (int) $id;
	}

	public function set_user_id( $user_id ) {
		$this->user_id = (int) $user_id;
	}

	public function set_item( Util\Voteable $item ) {
		$this->item = $item;
	}

	public function set_value( $value ) {
		$this->value = (int) $value;
	}

	public function get_id() {
		return $this->id;
	}

	public function get_user_id() {
		return $this->user_id;
	}

	public function get_item_id() {
		return $this->item->get_id();
	}

	public function get_value() {
		return $this->value;
	}

	public function get_item_vote_count( $force_query = false ) {
		return $this->item->get_vote_count( $force_query );
	}

	public function save() {
		if ( $this->exists() ) {
			$saved = $this->update();
		} else {
			$saved = $this->insert();
		}

		// Cache busting.
		if ( $saved ) {
			$this->item->get_vote_count( true );
		}

		return $saved;
	}

	public function delete() {
		global $wpdb;

		if ( ! $this->exists() ) {
			return false;
		}

		$deleted = $wpdb->delete(
			$this->get_table_name(),
			array( 'id' => $this->id ),
			array( '%d' )
		);

		if ( $deleted ) {
			$this->id = null;

			// Cache busting.
			$this->item->get_vote_count( true );
		}

		return (bool) $deleted;
	}

	public function populate() {
		global $wpdb;

		$table_name = $this->get_table_name();
		$row        = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM $table_name WHERE user_id = %d AND item_id = %d",
				$this->get_user_id(),
				$this->get_item_id()
			)
		);

		if ( $row ) {
			$this->set_id( $row->id );
			$this->set_value( $row->value );
		}
	}

	protected function update() {
		global $wpdb;

		$updated = $wpdb->update(
			$this->get_table_name(),
			array(
				'user_id' => $this->get_user_id(),
				'item_id' => $this->get_item_id(),
				'value'   => $this->get_value(),
			),
			array(
				'id' => $this->get_id(),
			),
			array( '%d', '%d', '%d' ),
			array( '%d' )
		);

		return (bool) $updated;
	}

	protected function insert() {
		global $wpdb;

		$inserted = $wpdb->insert(
			$this->get_table_name(),
			array(
				'user_id' => $this->get_user_id(),
				'item_id' => $this->get_item_id(),
				'value'   => $this->get_value(),
			),
			array( '%d', '%d', '%d' )
		);

		if ( ! $inserted || ! $wpdb->insert_id ) {
			return false;
		}

		$this->id = (int) $wpdb->insert_id;

		return true;
	}

	protected function get_table_name() {
		global $wpdb;
		return $wpdb->get_blog_prefix() . 'webwork_votes';
	}
}
