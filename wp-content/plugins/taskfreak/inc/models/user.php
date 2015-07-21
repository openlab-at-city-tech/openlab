<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

*** Capabilities used to indentify user level *****

read			Subscriber / Client / Volunteer
edit_posts 		Contributor / Freelance / Employee
publish_posts	Author / Staff / Official
edit_pages 		Editor / Manager / Moderator
manage_options 	Administrator

*/

class tfk_user extends tzn_model
{

	function __construct() {
		parent::__construct(
			'user',
			array(
				'ID'			 	=> 'UID',
				'user_login'		=> 'STR',
				'user_nicename'		=> 'STR',
				'user_email'		=> 'EML',
				'user_url'			=> 'STR',
				'display_name'		=> 'STR',
				'status'			=> 'NUM'
			),
			'ID'
		);
	}
	
	public function db_table($table = '') {
		return $this->db->base_prefix.'users';
	}
	
	// --- ACCESS RIGHTS -------
	
	public static function get_role($user=null) {
		global $wp_roles;
		if (empty($user)) {
			$user = wp_get_current_user();
		}
		$roles = $user->roles;
		return array_shift($roles);
	}
	
	public static function get_role_list($user=null) {

		$arr = array();
		
		if (is_user_logged_in()) {
			switch (self::get_role($user)) {
			case 'administrator':
				$arr[] = 'administrator';
			case 'editor':
				$arr[] = 'editor';
			case 'author':
				$arr[] = 'author';
			case 'contributor':	
				$arr[] = 'contributor';		
			case 'subscriber':
				$arr[] = 'subscriber';
			}
		}
		return $arr;
	}
	
	public static function get_roles_sql($p) {
		$arr = self::get_role_list();
		if (!count($arr)) {
			return "$p=''"; // no minimum role required
		} else {
			return "$p IN ('','".implode("','", $arr)."')";
		}
	}
	
	public static function check_role($r, $user=null) {
		return (in_array($r, self::get_role_list($user)));
	}
	
	// --- DISABLE INSERT, UPDATE and DELETE from DB ---
	
	public function update($args='') {
		return false;
	}
	
	public function insert($option='') {
		return false;
	}
	
	public function delete($args='') {
		return false;
	}
	
}

class tfk_author extends tfk_user
{

	function __construct() {
		parent::__construct();
	}
	
	public function db_alias() {
		return 'author';
	} 

}