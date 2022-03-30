<?php

/**
* @package ZephyrProjectManager
*/

namespace Inc\Core;

if ( !defined( 'ABSPATH' ) ) {
	die;
}

class DB {

	public function __construct( ) {
	}

	public function insert($table_name, $args, $defaults = []) {
		global $wpdb;

		if (!empty($defaults)) {
			$args = wp_parse_args( $args, $defaults );
		}

		$insert = $wpdb->insert($table_name, $args);
		return $wpdb->insert_id;
	}

	public function get($table_name, $args = []) {
		global $wpdb;
		$query = "SELECT * FROM $table_name";

		if (!empty($args)) {
			$query .= ' WHERE';
			$i = 0;

			foreach ($args as $key => $value) {
				$query .= " `$key` = %s";
				if (sizeof($args) > $i && $i > 0) {
					$query .= " AND";
				}

				$i++;
			}
		}

		$values = [];
		foreach ($args as $key => $value) {
			$values[] = $value;
		}


		$results = $wpdb->get_results(
			$wpdb->prepare(
				$query,
				$values 
			)
		);

		return $results;
	}

	public function getById($table_name, $id) {
		global $wpdb;
		$query = "SELECT * FROM $table_name WHERE id = '$id';";

		$row = $wpdb->get_row($query);
		return $row;
	}

	public function getByField($table_name, $field, $value) {
		global $wpdb;
		$query = "SELECT * FROM $table_name WHERE " . $field . " = '$value'";
		$row = $wpdb->get_row($query);
		return $row;
	}
}