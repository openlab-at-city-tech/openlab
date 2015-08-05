<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

** PROJECT RIGHTS ********

	who_read : Level required to see tasks
	who_comment : Level required to comment
	who_post : Level required to post new task
	who_manage : Level required to manage and moderate (manage users, edit any tasks, delete comments)

** PROJECT STATUS ********

	0 => Draft
	20 => In Progress
	30 => Suspended
	60 => Closed

	+ Boolean trashed

** MEMBER POSITION *******

	10 => Client / Volunteer
	20 => Freelance / Employee
	30 => Staff / Official
	40 => Manager / Moderator
	50 => Leader / Coordinator

*/

class tfk_project extends tzn_model
{

	public function __construct() {
		parent::__construct(
			'project',
			array(
				'project_id'	 	=> 'UID',
				'name'				=> 'STR',
				'description'		=> 'HTM',
				'who_read'			=> 'STR',
				'who_comment'		=> 'STR',
				'who_post'			=> 'STR',
				'who_manage'		=> 'STR',
				'creation_date'		=> 'DTM',
				'trashed'			=> 'NUM'
			)
		);
	}
	
	/**
	 * sets default access rights
	 */
	public function init_rights($opts) {
		$this->set('who_read', $opts['access_read']);
		$this->set('who_comment', $opts['access_comment']);
		$this->set('who_post', $opts['access_post']);
		$this->set('who_manage', $opts['access_manage']);
	}
	
	/**
	 * check before creating new project
	 */
	public function check() {
		// TODO trim
		if (!empty($this->data['name'])) {
			return true;
		}
	}
	
	/**
	 * load current status (last one set)
	 * if new project, status is 0 (draft)
	 */
	public function get_status() {
		$obj = new tfk_project_status();
		if ($pid = $this->get_uid()) {
			if ($obj->load_list(array(
				'where'	=> 'project_id='.$pid,
				'order'	=> 'log_date DESC'
			))) {
				return $obj->next(true);
			}
		}
		
		// no status (new project or not loaded)
		$obj->set('action_code', 0);
		$obj->set('log_date', 'NOW');
		return $obj;
	}
	
	/**
	 * set new status (saves in database)
	 * @param status new status key to save
	 * @param uid user ID, if not provided uses current user's ID
	 */
	public function set_status($status, $uid=null, $info=null) {
		if (is_null($uid)) {
			$uid = get_current_user_id();
		}
		$obj = new tfk_project_status();
		$obj->set('project_id', $this->get_uid());
		$obj->set('log_date','NOW');
		$obj->set('action_code', $status);
        $obj->set_object('user', $uid);
        if (!is_null($info)) {
        	$obj->set('info', $info);
        }
		return $obj->insert('ignore');
	}
	
	/**
	 * get project visibility, i.e. who can access project (read)
	 */
	public function get_visibility() {
		if (empty($this->data['who_read'])) {
			return __('Any visitor','taskfreak');
		} else {
			$role = get_role( $this->data['who_read'] );
			return translate_user_role($role->name);
		}
		
	}
	
	/** 
	 * check user acces
	 */
	public function check_access($what='read', $user_id=null) {
		$what = 'who_'.$what;
		if (empty($user_id)) {
			$user = wp_get_current_user();
		} else {
			$user = get_user_by('id', $user_id);
		}
		
		if ($r = $this->get($what)) {
			if (tfk_user::check_role($r, $user)) {
				// all is good
				return true;
			} else {
				// current user does not have sufficient role
				return false;
			}
		} else {
			// no specific role required (public)
			return true;
		}
	}
	
	/**
	 * delete project
	 */
	public function delete($args = '') {
		// TODO delete files
		return $this->db->query('DELETE '.$this->db_table('project').', ' // .$this->db_table('project_user').', '
			.$this->db_table('log').', '.$this->db_table('item').', '.$this->db_table('item_comment')
			.', '.$this->db_table('item_file').', '.$this->db_table('item_status')
			.' FROM '.$this->db_table('project')
			// .' INNER JOIN '.$this->db_table('project_user').' ON '.$this->db_table('project_user').'.project_id = '.$this->db_table('project').'.project_id'
			.' LEFT JOIN '.$this->db_table('log').' ON '.$this->db_table('log').'.project_id = '.$this->db_table('project').'.project_id'
			.' LEFT JOIN '.$this->db_table('item').' ON '.$this->db_table('item').'.project_id = '.$this->db_table('project').'.project_id'
			.' LEFT JOIN '.$this->db_table('item_comment').' ON '.$this->db_table('item_comment').'.item_id = '.$this->db_table('item').'.item_id'
			.' LEFT JOIN '.$this->db_table('item_file').' ON '.$this->db_table('item_file').'.item_id = '.$this->db_table('item').'.item_id'
			.' LEFT JOIN '.$this->db_table('item_status').' ON '.$this->db_table('item_status').'.item_id = '.$this->db_table('item').'.item_id'
			.' WHERE '.$this->db_table('project').'.project_id = '.$this->get_uid());
	}

}

class tfk_project_status extends tfk_log
{
	
	public function __construct() {
		parent::__construct();
	}
	
	public static function get_status_list($draft) {
		$arr = array(
    		0 		=> _x('Draft', 'many projects', 'taskfreak'),
    		20 		=> _x('In Progress', 'many projects', 'taskfreak'),
    		30 		=> _x('Suspended', 'many projects', 'taskfreak'),
    		60 		=> _x('Closed', 'many projects', 'taskfreak')
    	);
    	if (!$draft) {
	    	unset($arr[0]);
    	}
    	return $arr;
	}
	
	public function get_status() {
		$key = $this->get('action_code',0);
		switch($key) {
			case 0:
				return __('Draft', 'taskfreak');
			case 20:
				return __('In Progress', 'taskfreak');
			case 30:
				return __('Suspended', 'taskfreak');
			case 60:
				return __('Closed', 'taskfreak');
		}
	}
	
	public static function list_select($name, $selected=0, $xtra='class="tzn_option_select"', $draft=true) {
    	return tzn_tools::form_select($name, self::get_status_list($draft), $selected, $xtra);
    }
    
    public static function list_links($url, $name, $current) {
	    return 	 tzn_tools::form_links($url, $name, array('all' => _x('All', 'projects', 'taskfreak')), $current)
	    		.tzn_tools::form_links($url, $name, self::get_status_list(current_user_can('manage_options')), $current);
    }
	
}

class tfk_project_user extends tzn_model 
{

	public function __construct() {
		parent::__construct(
			'project_user',
			array(
				'project' 			=> 'OBJ',
				'user'				=> 'OBJ',
				'position'			=> 'NUM'
			),
			array(
				'project_id', 'user_id'
			)
		);
	}
	
	public function get_user_id() {
		return $this->get('user')->get_uid();
	}
	
	public function get_user_name() {
		return $this->get('user')->get('display_name');
	}

	public function get_position($pos=null) {
		if (is_null($pos)) {
			$pos = $this->data['position'];
		}
		switch ($pos) {
			case 0:
				return __('Guest', 'taskfreak');
			case 1:
				return __('Client', 'taskfreak');
			case 2:
				return __('Staff', 'taskfreak');
			case 3:
				return __('Manager', 'taskfreak');
			case 4:
				return __('Project Leader', 'taskfreak');
		}
	}

    public function check_rights($level) {
        $level--;
        return ($GLOBALS['confProjectRights'][$this->position]{$level} == '1');
    }

    public function load_position($pid, $mid) {
        $table = $this->db_table();
        $where = $table.'.project_id='.$pid.' AND '.$table.'.user_id='.$mid;
        return $this->load(array(
        	'where' => $where
        ));
    }
    
    /**
     * get list of users already associated with project
     * returns array with users' IDs
     */
    public static function list_already($id) {
    	$arr = array();
	    $obj = new tfk_project_user();
	    $obj->load_list(array(
			'where' => 'project_user.project_id='.$id,
			'order'	=> 'position DESC'
		));
		while ($obj->next()) {
			$arr[] = $obj->get_user_id();
		}
		return $arr;
    }
    
    public static function list_select($id, $name, $selected=0, $max=4) {
    	$arr = array(
    		0 => __('Guest', 'taskfreak'),
    		1 => __('Client', 'taskfreak'),
    		2 => __('Staff', 'taskfreak'),
    		3 => __('Manager', 'taskfreak'),
    		4 => __('Project Leader', 'taskfreak')
    	);
		
	    $str = '<select name="'.$name.'" id="'.$id.'">';
	    foreach ($arr as $p => $label) {
	    	if ($p > $max) {
		    	break;
	    	}
	    	$str .= '<option';
		    if ($selected == $p) { $str .= ' selected="selected"'; }
			$str .= ' value="'.$p.'">'.$label.'</option>';
	    }
	    $str .= '</select>';
		return $str;
    }
    
    /**
     * add first user (current user logged in)
     */
    public static function list_insert($pid, $pos=4) {
	    $obj = new tfk_project_user();
		$obj->set_object('project', $pid);
		$obj->set_object('user', get_current_user_id());
		$obj->set('position', $pos);
		$obj->insert();
    }
    
    /** 
     * update list of users
     */
    public static function list_update($pid, $data, $list) {
	    if (empty($data) || empty($pid)) {
		    return false;
	    }
	    $i=0;
	    // update existing users
	    while ($obj = $list->next(true)) {
			$uid = $obj->get_user_id();
			if (isset($data[$uid]) && $data[$uid] != $obj->value('position')) {
				// position changed, update
				$obj->set('position', $data[$uid]);
				$obj->update();
				$i++;
			}
			unset($data[$uid]);
		}
		// add new users
		foreach ($data as $uid => $pos) {
			$obj = new tfk_project_user();
			$obj->set_object('project', $pid);
			$obj->set_object('user', $uid);
			$obj->set('position', $pos);
			$obj->insert();
		}
		return $i;
    }
    
    /**
     * remove users
     */
    public static function list_remove($pid, $data) {
	    if (empty($data) || empty($pid)) {
		    return false;
	    }
	    $sql = 'project_id='.$pid.' AND user_id';
	    if (preg_match('/,/', $data)) {
		    $sql .= ' IN ('.$data.')';
	    } else {
		    $sql .= '='.$data;
	    }
	    $obj = new tfk_project_user();
	    return $obj->delete(array('where'=>$sql));
    }

}

class tfk_project_info extends tfk_project
{
	
	public function __construct() {
		parent::__construct();
		$this->properties['project_status'] = 'OBJ';
	}
	
	public function load_list($args=null) {
		$sql = array(
			'sql' => 'SELECT 
						project.project_id, 
						project.name, 
						project.description, 
						project.who_read, 
						project.who_comment, 
						project.who_post, 
						project.who_manage, 
						project.trashed,
						project_status.log_date AS project_status_log_date, 
						project_status.action_code AS project_status_action_code,
						project_status.project_id AS project_status_project_id, 
						project_status.user_id AS project_status_user_id
						FROM '.$this->db_table().' AS '.$this->db_alias().'
						INNER JOIN '.$this->db_table('log').' AS project_status 
						ON project.project_id = project_status.project_id 
						AND project_status.item_id = 0
						',
			'count' => true,
			'group' => 'project_status.project_id'
		);
		
		$where = 'project_status.log_date = (
												SELECT MAX(log_date) 
												FROM '.$this->db_table('log').' 
												WHERE project.project_id = project_id 
												AND item_id = 0
											)';
		
		if ($args) {
			if (isset($args['where'])) {
				$where = $where .= ' AND '.$args['where'];
				unset($args['where']);
			}
			$args['where'] = $where;
			$args = array_merge($args, $sql);
		} else {
			$sql['where'] = $where;
			$args = $sql;
		}
		return parent::load_list($args);
	}
	
}