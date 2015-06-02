<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

** TASK STATUS ********

	0 => Draft
	20 => In Progress
	30 => Suspended
	60 => Closed

*/

class tfk_item extends tzn_model
{

	public function __construct() {
		parent::__construct(
			'item',
			array(
				'item_id'	 		=> 'UID',
				'project'			=> 'OBJ',
				'priority'			=> 'NUM',
				'title'				=> 'STR',
				'description'		=> 'HTM',
	            'deadline_date'     => 'DTE',
				'creation_date'		=> 'DTM',
				'user_id'			=> 'NUM',
	            'author_id'       	=> 'NUM'
			)
		);
		
		$this->errors['global_errors'] = '';
		$this->errors['status'] = '';
		$this->errors['file'] = '';
		foreach ($this->properties as $k => $v) {
			$this->errors[$k] = '';
		}
	}

	/**
	 * check before saving task
	 */
	public function check() {
		$msg = array();
		if (empty($this->data['title'])) {
			$msg['title'] = __('Title should not be blank', 'taskfreak');
		}
		if (!preg_match('/^[123]$/', $this->data['priority'])) {
			$msg['priority'] = __('Priority should be 1, 2, or 3', 'taskfreak');
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $this->data['deadline_date'])) {
			$msg['deadline_date'] = __('Invalid date format', 'taskfreak');
		}
		if (strlen($this->data['description']) > 65535) {
			$msg['description'] = __('Description is too big (maybe you pasted an image into it?)', 'taskfreak');
		}
		
		foreach ($msg as $k => $v) {
			$this->errors[$k] = '<p class="tfk_err">'.__($v, 'taskfreak').'</p>';
		}
		return empty($msg);
	}
	
	/**
	 * load current task status
	 * @return tfk_item_status
	 */
	public function get_status() {
		$obj = new tfk_item_status();
		if ($pid = $this->get_uid()) {
			if ($obj->load_list(array(
					'where'	=> 'item_id='.$pid.' AND action_code <> "" AND comment_id = 0',
					'order'	=> 'log_date DESC',
					'limit' => 1
			))) {
				return $obj->next(true);
			}
		}
	
		// no status (new item or not loaded)
		$obj->set('action_code', 0);
		$obj->set('log_date', 'NOW');
		return $obj;
	}
	
	/**
	 * set task status
	 * @param number $status status code
	 * @param number $uid (optional) id of the user setting the status
	 * @param string $info (optional) may contain "creation" for example 
	 * @return boolean
	 */
	public function set_status($status, $uid=null, $info=null) {
		if (is_null($uid)) {
			$uid = get_current_user_id();
		}
		$obj = new tfk_item_status();
		$obj->set('item_id', $this->get_uid());
		$obj->set('log_date','NOW');
		$obj->set('action_code', $status);
		$obj->set_object('user', $uid);
		if (!is_null($info)) {
			$obj->set('info', $info);
		}
		return $obj->insert('ignore');
	}
	
	/**
	 * get deadline proximity bar width in pixels
	 * @return number
	 */
	public function get_deadline_proximity_bar() {
		if ($this->data['deadline_date'] == '0000-00-00 00:00:00') {
			return 300;
		}
		$date_diff_in_sec = strtotime($this->data['deadline_date']) + 24*60*60 - time() - tzn_tools::get_user_timezone_offset(); 
		if ($date_diff_in_sec < 86400)
			return 300; // px
		elseif ($date_diff_in_sec > 365 * 86400)
			return 30;
		else
			return 300 - ($date_diff_in_sec / 86400 * 270 / 365);
	}
	
	/**
	 * get deadline proximity in words
	 * @return string
	 */
	public function get_deadline_proximity() {
		if ($this->data['deadline_date'] == '0000-00-00 00:00:00')
			return __('Undefined Deadline', 'taskfreak');

		$date_diff['sec'] = strtotime($this->data['deadline_date']) + 24*60*60 - time() - tzn_tools::get_user_timezone_offset();		
		$date_diff['min'] = round($date_diff['sec'] / 60);
		$date_diff['hour'] = round($date_diff['sec'] / 3600);
		$date_diff['day'] = round($date_diff['sec'] / 86400);
		$date_diff['month'] = round($date_diff['sec'] / 2592000);
		if ($date_diff['sec'] < 0) {
			return __("Past due !", 'taskfreak');
		}
		if ($date_diff['sec'] < 3600) {
			return sprintf(_n("%s minute remaining", "%s minutes remaining", $date_diff['min'], 'taskfreak'), $date_diff['min']);
		}
		if ($date_diff['sec'] < 86400) {
			return sprintf(_n("%s hour remaining", "%s hours remaining", $date_diff['hour'], 'taskfreak'), $date_diff['hour']);
		}
		if ($date_diff['sec'] < 365 * 86400) {
			return sprintf(_n("%s day remaining", "%s days remaining", $date_diff['day'], 'taskfreak'), $date_diff['day']);
		}
		return sprintf(_n("%s month remaining", "%s months remaining", $date_diff['month'], 'taskfreak'), $date_diff['month']);
	}
	
	/**
	 * get an extract from the description (body) of the task
	 * useful for the HTML title attribute
	 * @return string
	 */
	public function get_description_extract() {
		$description_text = preg_replace('/&nbsp;/', ' ', $this->data['description']);
		$description_text = html_entity_decode(strip_tags($description_text));
		if (strlen($description_text) > 140) {
			$description_text = substr($description_text, 0, 140);
			$description_text = preg_replace('/\w+$/', '', $description_text); // prevent cutting words
			$description_text .= '[…]';
		}
		return htmlspecialchars($description_text);
	}
}

class tfk_item_status extends tfk_log {

	public function __construct() {
		parent::__construct();
	}
	
	/**
	 * get a list of statuses in translated strings
	 * @param mixed $draft include draft status ?
	 * @param string $context by default, context for translation is "many tasks"
	 * @return array
	 */
	public static function get_status_list($draft, $context='many tasks') {
		$arr = array(
    		0 		=> _x('Draft', $context, 'taskfreak'),
    		20 		=> _x('In Progress', $context, 'taskfreak'),
    		30 		=> _x('Suspended', $context, 'taskfreak'),
    		60 		=> _x('Closed', $context, 'taskfreak')
		);
    	if (!$draft) {
	    	unset($arr[0]);
    	}
    	return $arr;
	}
	
	/**
	 * get task current status in string
	 * @return string
	 */
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
			default:
				return __('Unknown Status !', 'taskfreak');
		}
	}
	
	/**
	 * get an HTML select (dropdown list) of task statuses
	 * @param string $name name of the HTML select element
	 * @param number $selected (optional) current status, 0 by default
	 * @param string $xtra (optional) class for the select element
	 * @param string $draft (optional) include draft status ?
	 * @return string
	 */
	public static function list_select($name, $selected=0, $xtra='class="tzn_option_select"', $draft=true) {
		return tzn_tools::form_select($name, self::get_status_list($draft), $selected, $xtra);
    }
    
    /**
     * get a HTML li (list) of links to task statuses + "All tasks" for filtering status
     * @param unknown $url base URL
     * @param unknown $name name of the filter argument inside the query
     * @param unknown $current current filter
     * @return string
     */
    public static function list_links($url, $name, $current) {
	    return  tzn_tools::form_links($url, $name, array('all' => _x('All tasks', 'tasks', 'taskfreak')), $current) 
	    		.tzn_tools::form_links($url, $name, self::get_status_list(is_user_logged_in()), $current);
    }
	
}

class tfk_item_comment extends tzn_model
{
	public function __construct() {
		parent::__construct(
				'item_comment',
				array(
						'item_comment_id'	=> 'UID',
						'item_id'	 		=> 'NUM',
						'user_id'			=> 'NUM',
						'post_date'			=> 'DTM',
						'body'				=> 'HTM',
			            'last_change_date'	=> 'DTM'
				)
		);
		
		$this->errors['global_errors'] = '';
		$this->errors['file'] = '';
		foreach ($this->properties as $k => $v) {
			$this->errors[$k] = '';
		}
	}
	
	/**
	 * check task before saving
	 * @return boolean
	 */
	public function check() {
		if (empty($this->data['body'])) {
			$this->errors["body"] = '<p class="tfk_err">'.__('Empty comment not allowed.', 'taskfreak').'</p>';
			return false;
		}
		return true;
	}
}

class tfk_item_file extends tzn_model
{
	public function __construct() {
		$this->error = '';
		parent::__construct(
				'item_file',
				array(
						'item_file_id'		=> 'UID',
						'item_id'	 		=> 'NUM',
						'user_id'			=> 'NUM',
						'file_title'		=> 'STR',
						'file_name'			=> 'STR',
						'file_type'			=> 'STR',
						'file_size'			=> 'NUM',
						'post_date'			=> 'DTM',
						'last_change_date'	=> 'DTM',
						'file_tags'			=> 'STR'
				)
		);
	}
	
	/**
	 * save uploaded file to disk and metadata into database
	 * @param array $file_input file input information from PHP
	 * @return boolean
	 */
	public function upload($file_input) {
		$wp_upload_dir = wp_upload_dir();
		if ($_FILES[$file_input]['error'] == UPLOAD_ERR_OK) {
			if (preg_match('/(php|phtml|phps)/i', $_FILES[$file_input]['type'])
			|| preg_match('/\.(php|phtml|phps)$/i', $_FILES[$file_input]['name'])) {
				$this->error = __('Forbidden upload: ', 'taskfreak').$_FILES[$file_input]['name'];
				return false;
			} else {
				$file_title = $_FILES[$file_input]['name'];
				$file_name = time().'_'.preg_replace('/[^\w\d.-_,]/', '-', $file_title);
				if (!@move_uploaded_file($_FILES[$file_input]['tmp_name'], $wp_upload_dir['path'].'/'.$file_name)) {
					$this->error = __('Unable to write file to upload directory. Please tell an admin.', 'taskfreak');
					return false;
				} else {
					@chmod($wp_upload_dir['path'].'/'.$file_name, 0444);
					$this->set('file_title', $file_title);
					$this->set('file_name', $file_name);
					$this->set('file_size', $_FILES[$file_input]['size']);
					$this->set('file_type', $_FILES[$file_input]['type']);
					$this->set('post_date', 'NOW');
					return true;
				}
			}
		} else {
			$this->error = __('Upload of ', 'taskfreak').$_FILES[$file_input]['name'].__(' failed with error code ', 'taskfreak').$_FILES[$file_input]['error'];
			return false;
		}
	}
}

class tfk_item_info extends tfk_item
{
	public function __construct() {
		parent::__construct();
		$this->properties['comment_count'] = 'NUM';
		$this->properties['file_count'] = 'NUM'; 
		$this->properties['item_status'] = 'OBJ';
		$this->properties['item_status_info'] = 'STR';
		$this->properties['item_status_date'] = 'DTM';
		$this->properties['item_status_user_id'] = 'NUM';
		$this->properties['proximity'] = 'NUM';
	}
	
	/**
	 * (non-PHPdoc)
	 * @see tzn_model::load_list()
	 */
	public function load_list($args=null) { 
		$sql = array(
				'sql' 	=> 'SELECT '.$this->db_alias().'.*,
							IFNULL(DATEDIFF('.$this->db_alias().'.deadline_date, NOW()), 9999999999) AS proximity,
							project.project_id,
							project.name,
							project.description AS project_description,
							project.who_read,
							project.who_comment,
							project.who_post,
							project.who_manage,
							project.trashed,
							user.*,
							item_status.log_date AS item_status_date,
							item_status.info AS item_status_info,
							COUNT(DISTINCT item_comment.item_comment_id) AS comment_count,
							COUNT(DISTINCT item_file.item_file_id) AS file_count,
							(SELECT action_code
								FROM '.$this->db_table('log').'
								WHERE item_id = item.item_id
								AND comment_id = 0
								AND action_code <> ""
								ORDER BY log_date DESC
								LIMIT 1) AS item_status_action_code,
							item_status.user_id AS item_status_user_id
							FROM '.$this->db_table().' AS '.$this->db_alias().'
							INNER JOIN '.$this->db_table('project').' AS project ON item.project_id = project.project_id
							INNER JOIN '.$this->db_table('log').' AS item_status ON item.item_id = item_status.item_id
							LEFT JOIN '.$this->db->base_prefix.'users AS user ON user.ID = item.user_id
							LEFT JOIN '.$this->db_table('item_comment').' AS item_comment ON item.item_id = item_comment.item_id
							LEFT JOIN '.$this->db_table('item_file').' AS item_file ON item.item_id = item_file.item_id AND file_tags = "task"
							',
				'count'	=> true,
				'group' => $this->db_alias().'.item_id'
		);
	
		$where = 'item_status.log_date = (
											SELECT MAX(log_date) 
											FROM '.$this->db_table('log').' 
											WHERE item_id = item.item_id
											AND comment_id = 0 
										)';
	
		if ($args) {
			if (isset($args['where'])) {
				$where .= ' AND '.$args['where'];
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
	
	/**
	 * get number of attachments in string (for an HTML title attribute)
	 * @return string
	 */
	public function get_attachments() {
		return sprintf(_n('%d attachment', '%d attachments', $this->data['file_count'], 'taskfreak'), $this->data['file_count']);
	}
}

class tfk_item_status_info extends tfk_item_status {

	public function __construct() {
		parent::__construct();
		$this->properties['type'] = 'STR';
		$this->properties['creation_date'] = 'DTM';
		$this->properties['title_or_name'] = 'STR';
	}

	/**
	 * (non-PHPdoc)
	 * @see tzn_model::load_list()
	 */
	public function load_list($args=null) {
		$sql = array(
				'sql'=> 'SELECT log.*,
					user.display_name,
					IF (log.item_id = 0, "project", "task") AS type, 
					IFNULL(title, project.name) AS title_or_name,
					IFNULL(item.creation_date, project.creation_date) AS creation_date,
					IFNULL(who_read,
						(SELECT who_read FROM '.$this->db_table('project').' WHERE project_id = item.project_id LIMIT 1)
					) AS who_read,
					IF( log.project_id = 0,
						(SELECT action_code FROM '.$this->db_table().'
						 WHERE item_id = item.item_id
						 AND action_code <> ""
						 AND comment_id = 0
						 ORDER BY log_date DESC LIMIT 1),
						(SELECT action_code FROM '.$this->db_table().'
						 WHERE project_id = project.project_id
						 AND action_code <> ""
						 AND comment_id = 0
						 ORDER BY log_date DESC limit 1)
					) AS status
					FROM '.$this->db_table().' AS log
					LEFT JOIN '.$this->db_table('item').' AS item ON item.item_id = log.item_id
					LEFT JOIN '.$this->db_table('project').' AS project ON project.project_id = log.project_id
					LEFT JOIN '.$this->db->base_prefix.'users AS user ON user.ID = log.user_id',
				'where' => '(log.item_id <> 0 OR project.trashed = 0)',
				'having'=> tfk_user::get_roles_sql('who_read').' AND status > 0',
				'order' => 'log_date DESC'
		);

		if (isset($args['where']))
			$sql['where'] .= ' AND '.$args['where'];

		return parent::load_list($sql);
	}
}
