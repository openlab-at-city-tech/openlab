<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

Any model should inherit this class
- data sanitization
- mysql queries (through wpdb)
- html helpers

constructor should call parent constructor

class my_data extends tzn_model {

	function __construct() {
		parent::__construct(
			'my_data',	// table name without prefix
			array(
				'my_data_id'	=> 'UID',
				'title'			=> 'STR',
				'description'	=> 'BBS',
				'illustration'	=> 'IMG',
				'author'		=> 'OBJ,member'
			)
		);
	}
}

property types :
- UID : an integer or alphanumerical (no space and no special characters)
- INT : an integer
- NUM : a positive integer
- DEC : a decimal eg. DEC,3 = 3 decimals
- DTE : a date
- DTM : a date time
- DUR : a duration in seconds
- TIM : a time
- TMZ : a time zone
- EML : an email address
- URL : an web URL / URI
- BOL : a boolean
- LVL : a list of booleans (usefull for rights)
- STR : a string (no carriage returns)
- TXT : a string, all HTML tags removed
- BBS : some HTML accepted
- HTM : all HTML accepted (no javascript though)
- DOC : a file -TODO-
- IMG : an image -TODO-
- OBJ : a nested data object eg. OBJ,class

*/

class tzn_model {
	
	protected $_error;		// errors 
	protected $properties;	// properties definition
	
	protected $key;
	protected $table;
	
	protected $db;
	
	public $data;	// current object's data
	
	public $rows;	// multiple rows data
	protected $idx;		// index
	protected $total;	// total number of results
	
	protected $_page;		// pagination page number
	protected $_page_size;	// pagination : page size
	
	/**
	 * constructor method, sets database table and key
	 * @name table name (without prefix)
	 * @key row primary key. if multiple cols, give an array
	 */ 
	public function __construct($name, $properties, $key='') {
	
		global $wpdb;
		$this->db = $wpdb;
	
		$this->table = $name;
		
		$this->properties = $properties;
		
		if ($key) {
			$this->key = $key;
		} else {
			$arr = array();
			foreach ($properties as $k => $t) {
				if ($t == 'UID') {
					$arr[] = $k;
				}
			}
			if (!count($arr)) {
				$this->key = $this->table.'_id';
			} else if (count($arr) > 1) {
				$this->key = $arr;
			} else {
				$this->key = array_shift($arr);
			}
		}
	}
	
	/**
	 * returns UID (primary key)
	 */
	public function get_uid() {
		if (is_array($this->key)) {
			$arr = array();
			foreach ($this->key as $k) {
				if (!isset($this->data[$k])) {
					return false;
				}
				$arr[$k] = $this->data[$k];
			}
			return $arr;
		} else if (isset($this->data[$this->key])) {
			return $this->data[$this->key];
		} else {
			return false;
		}
	}
	
	/**
	 * set UID (primary key)
	 * @uid string, integer or array if multiple cols are used for primary key
	 * UID must be alphanumerical (spaces, dots and comma allowed)
	 */
	public function set_uid($uid) {
		if (is_array($uid)) {
			foreach ($uid as $k => $v) {
				if (!preg_match('/^[0-9a-z,\. \-:]+$/i', $v)) {
					return false;
				}
				$this->data[$k] = $v;
			}
			return true;
		} else if (preg_match('/^[0-9a-z,\. \-:]+$/i', $uid)) {
			$this->data[$this->key] = $uid;
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * returns table name (without prefix)
	 * usefull to create alias in SQL query
	 * eg. $sql = 'SELECT * FROM '.$this->db_table().' AS '.$this->db_alias();
	 */
	public function db_alias() {
		return $this->table;
	}
	
	/**
	 * returns table name (with WP database prefix)
	 */
	public function db_table($table='') {
		if (empty($table)) {
			$table = $this->table;
		}
		return $this->db->prefix.TZN_PROJECT_PREFIX.'_'.$table;
	}
	
	/**
	 * check if value is set and is not empty
	 */
	public function has($key) {
		if (!isset($this->data[$key])) {
			return false;
		}
		$value = $this->data[$key];
		return (!empty($value) && !preg_match('/00\-00/', $value));
	}
	
	/**
	 * returns value unescaped or formated
	 */
	public function get($key, $default='') {
		if (!isset($this->data[$key])) {
			return $default;
		} else {
			return $this->data[$key];
		}
	}
	
	/**
	 * returns value to display in HTML
	 */
	public function html($key, $default='') {
		$value = $this->get($key, $default);
		$type = substr($this->properties[$key], 0, 3);
		switch ($type) {
		case 'DTE':
			return tzn_tools::format_date($value);
		case 'DTM':
			return tzn_tools::format_datetime($value);
			break;
		case 'DUR':
			return tzn_tools::format_duration($value);
		case 'TIM':
			return tzn_tools::format_time($value);
		case 'TMZ': // timezone
			// TODO
		case 'BOL':
			return ($value)?1:0;
		case 'LVL':
			break;
		case 'STR':
		case 'TXT':
			$spe = array('&','<','>');
			$sfe = array('&amp;','&lt;','&gt;');
			return str_replace(array("\r\n", "\r", "\n"), "<br />", str_replace($spe, $sfe, $value));
		case 'BBS':
			$value = preg_replace("/(?<!\")((http|ftp)+(s)?"
				.":\/\/[^<>\s]+)/i", "<a href=\"\\0\" target=\"_blank\">\\0</a>", $val);
			return str_replace(array("\r\n", "\r", "\n"), "<br />", str_replace('"','&quot;',$value));
		case 'HTM':
			return preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is","", $value);
			break;
		case 'DOC':
			// TODO
		case 'IMG':
			// TODO
		case 'OBJ':
			if (is_object($value)) {
				return $value->get_uid();
			}
		case 'UID':
		case 'NUM': // positive number
		case 'INT': // any integer
		case 'DEC': // float (TODO check regional settings, look for decimal separator . or ,)
		default:
			return $value;
			break;
		}
	}
	
	/**
	 * returns value to print in form field
	 * eg. echo '<input type="text" name="title" value="'.$obj->value('title').'" />';
	 */
	public function value($key, $default='') {
	
		$value = $this->get($key, $default);
		$type = substr($this->properties[$key], 0, 3);
		switch ($type) {
		case 'BBS':
		case 'HTM':
			return str_replace('"','&quot;',$value);
		default:
			/*
			$spe = array('<','>');
			$sfe = array('&lt;','&gt;');
			$value = str_replace($spe, $sfe, $value);
			*/
			return esc_attr(htmlspecialchars($this->get($key, $default)));
		}
		
	}
	
	/**
	 * sanitize (check) value
	 * @value data
	 * @type or regexp
	 */
	public function sani($value, $type='STR') {
		switch ($type) {
		case 'UID':
			return preg_replace('/[^0-9a-z_\-\.]/i','', $value);
		case 'NUM': // positive number
			return abs(intval($value));
		case 'INT': // any integer
			return intval($value);
		case 'DEC':
			return floatval($value);
		case 'DTE':
			return tzn_tools::parse_date($value);
		case 'DTM':
			return tzn_tools::parse_datetime($value);
			break;
		case 'DUR':
			return tzn_tools::parse_duration($value);
		case 'TIM':
			return tzn_tools::parse_time($value);
		case 'TMZ': // timezone
			// TODO
			break;
		case 'EML': // email
			return is_email($value); // WP native function
		case 'URL':
			return esc_url($value, null, null); // WP native function
		case 'BOL':
			return ($value)?1:0;
		case 'LVL':
			// TODO
			break;
		case 'STR':
			return sanitize_text_field($value); // WP native function
		case 'TXT':
			return trim(strip_tags($value));
		case 'BBS':
			return wp_kses($value, array(
				'a' => array(
			        'href'	=> array(),
			        'title'	=> array(),
			        'target' => array(),
			        'class'	=>array()
			    ),
			    'big' 	=> array(),
			    'small' => array(),
			    'em' 	=> array(),
			    'strong' => array(),
			    'blockquote'	=> array(),
			    'address' => array(),
			    'var'	=> array(),
			    'code'	=> array(),
			    'abbr'	=> array('title'=>array()),
			    'hr'	=> array('class'=>array()),
			    'ul'	=> array('class'=>array()),
			    'ol'	=> array('class'=>array()),
			    'li'	=> array('class'=>array()),
			    'table'	=> array('class'=>array()),
			    'thead'	=> array('class'=>array()),
			    'tbody'	=> array('class'=>array()),
			    'tr'	=> array('class'=>array()),
			    'th'	=> array('class'=>array()),
			    'td'	=> array('class'=>array()),
			)); // WP native function
		case 'HTM':
			return preg_replace("/<script[^>]*>[^<]+<\/script[^>]*>/is","", $value);
		
		case 'OBJ':
			if (is_object($value)) {
				return $value;
			} else {
				return $this->init_object($key, $value);
			}
		case 'DOC':
		case 'IMG':
		default:
			return $value;
			break;
		}
	}
	
	/**
	 * sets value (data coming from http)
	 * @key field or property name
	 * @value property's value
	 * @sanitize sanitization if needed
	 */	
	public function set($key, $value, $sani='') {
		if ($sani) {
			$this->data[$key] = $this->sani($value, $sani);
			return true;
		} else if (isset($this->properties[$key])) {
			$this->data[$key] = $this->sani($value, substr($this->properties[$key],0,3));
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * instantiate nested object
	 */
	public function init_object($key, $value='', $type='') {
		if (empty($type)) {
			$type = $this->properties[$key];
		}
		$arr = explode(',', $type);
		$class = $key;
		if (isset($arr[1])) {
			$class = $arr[1];
		}
		$class = TZN_PROJECT_PREFIX.'_'.$class;
		$obj = new $class();
		if ($value) {
			if (is_array($value)) {
				$obj->set_auto($value);
			} elseif (is_object($value)) {
				$obj = $value;
			} else {
				$obj->set_uid($value);
			}
		}
		return $obj;
	}
	
	public function set_object($key, $value, $type='') {
		$this->data[$key] = $this->init_object($key, $value, $type);
	}
	
	/**
	 * set document file
	 */
	public function set_doc($key, $data) {
		// TODO - set file object
	}
	
	/**
	 * set image file
	 */
	public function set_img($key, $data) {
		// TODO - set file object
	}
	
	/**
	 * set data from HTTP / forms
	 */
	public function set_auto($data) {
		foreach ($this->properties as $key => $type) {
			$t = substr($type,0,3);
			if (!isset($data[$key]) && $t != 'UID' && $t != 'OBJ') {
				continue;
			}
			switch($t) {
			case 'UID':
				if (isset($data[$key])) {
					$this->set_uid($data[$key]);
				} else if (isset($data['id'])) {
					$this->set_uid($data['id']);
				}
				break;
			case 'OBJ':
				$this->set_object($key, $data, $type);
				break;
			case 'DOC':
				$this->set_doc($key, $data[$key]);
				break;
			case 'IMG':
				$this->set_img($key, $data[$key]);
				break;
			default:
				$this->set($key, $data[$key], $t);
				break;
			}
		}
	}
	
	/**
	 * set data from SQL
	 * WARNING : no sanitization, use with care
	 * TODO : method should be protected, not public
	 */
	public function set_data($data, $nested=false) {
		foreach ($this->properties as $key => $type) {
			$nkey = ($nested)?($nested.'_'.$key):$key;
			$t = substr($type,0,3);
			if (!isset($data[$nkey]) && !isset($data[$key]) && $t != 'UID' && $t != 'OBJ') {
				continue;
			}
			switch($t) {
			case 'UID':
				if (isset($data[$nkey])) {
					$this->set_uid($data[$nkey]);
				} else if (isset($data[$key])) {
					$this->set_uid($data[$key]);
				} else if (isset($data['id'])) {
					$this->set_uid($data['id']);
				}
				break;
			case 'OBJ':
				$obj = $this->init_object($key,null,$type);
				if ($nested) {
					if (isset($data[$nkey])) {
						$this->set_uid($data[$nkey]);
					} else if (isset($data[$key])) {
						$this->set_uid($data[$key]);
					}
				} else {
					$obj->set_data($data, $key);
				}
				$this->data[$key] = $obj;
				break;
			default:
				if (isset($data[$nkey])) {
					$this->data[$key] = $data[$nkey];
				} else {
					$this->data[$key] = $data[$key];
				}
				break;
			}
		}
	}
	
	/* ==== SQL PREPARATION METHODS =========================================== */
	
	/**
	 * checks method arguments, looking for a full sql statement
	 */
	protected function _sql_init($args) {
		$sql = '';	
		if ($args) {
			if (is_string($args)) {
				// arg is a SQL query
				$sql = $args;
			} else if (isset($args['sql'])) {
				$sql = $args['sql'];
			}
		}
		return $sql;
	}
	
	/**
	 * prepare select and from statements
	 */
	protected function _sql_select($args=null) {
		$args = tzn_tools::mixed_to_array($args);
		$fields = $join = array();
		$sel = 'SELECT ';
		if (isset($args['count']) && ($args['count'] == true)) {
			$sel .= 'SQL_CALC_FOUND_ROWS ';
		}
		$from = ' FROM `'.$this->db_table().'`';
		foreach ($this->properties as $key => $type) {
			$t = substr($type, 0, 3);
			if ($t == 'OBJ') {
				$obj = $this->init_object($key, '', $type);
				$fields[] = $obj->db_alias().'.*';
				$join[$key] = $obj;
			} else {
				$fields[] = $this->db_alias().'.'.$key;
			}
		}
		if (count($join)) {
			//$sel .= '`'.implode('`, `', $fields).'`';
			$sel .= implode(', ', $fields);
			$from .= ' AS '.$this->db_alias();
			foreach ($join as $key => $obj) {
				$from .= ' LEFT JOIN `'.$obj->db_table().'` AS '.$obj->db_alias()
					.' ON '.$this->db_alias().'.'.$key.'_id='.$obj->db_alias().'.'.$obj->key;
			}
		} else {
			$sel .= '*';
			
		}
		return $sel.$from;
	}
	
	/**
	 * generic method to create statement
	 */
	protected function _sql_statement($state, $res, $glue=' AND ') {
		if (is_string($res)) {
			return " $state $res";
		}
		if (is_array($res)) {
			$sql = $value = array();
			foreach ($res as $k => $v) {
				$str = '';
				if (is_string($k)) {
					$str = "`$k`=";
				}
				if (is_numeric($v) && intval($v) == $v) {
					$str .= '%d';
				} else {
					$str .= '%s';
				}
				$sql[] = $str;
				$values[] = $v;
			}
			return " $state ".$this->db->prepare(implode($glue, $sql), $values);
		}
		return '';
	}
	
	/**
	 * checks arguments, looking for an id or a where condition
	 * returns where condition as an array (automatic if needed)
	 */
	protected function _sql_where_array($args, $letitgo=false) {
	
		$kid = $this->key;

		$where = '';		
		if (isset($args['where'])) {
			$where = $args['where'];
		}
		
		if ($where == '1') {
			// forcing all
			return null;
		}
		if (isset($args['id'])) {
			// id is provided
			return array($kid => $args['id']);
		}
		if (is_array($where)) {
			// where condition is provided (as an array)
			return $where;
		} else if (strlen($where)) {
			// where condition is provided (as a string)
			// return as is: it doesn't need to be parsed again in _sql_statement()
			return $where; 
		}
		if (is_array($kid)) {
			// primary key is a multi column key
			$arr = array();
			foreach ($kid as $k) {
				if (empty($this->data[$k])) {
					break;
				}
				$arr[$k] = $this->data[$k];
			}
			if (count($kid) == count($arr)) {
				return $arr;
			}
			
		} else if (!empty($this->data[$kid])) {
			// use primary key
			return array($kid => $this->data[$kid]);
		}
		if ($letitgo) {
			return false;
		} else {
			echo '<pre>';
			print_r($args);
			echo '</pre>';
			die ('dangerous no where statement, giving up');
		}
	}
	
	/**
	 * returns where statement as a string, with WHERE keyword
	 */
	protected function _sql_where($args) {
		$res = $this->_sql_where_array($args);
		if (empty($res)) {
			return '';
		}
		return $this->_sql_statement('WHERE', $res,' AND ');
	}
	
	/*
	 * look from group by statement
	 */
	protected function _sql_group($args) {
		$res = '';
		if (isset($args['group'])) {
			$res = $args['group'];
		} else if (isset($args['group_by'])) {
			$res = $args['group_by'];
		} else {
			return '';
		}
		return $this->_sql_statement('GROUP BY', $res,', ');
	}
	
	/*
	 * look from having statement
	 */
	protected function _sql_having($args) {
		$res = '';
		if (isset($args['having'])) {
			$res = $args['having'];
		} else {
			return '';
		}
		return $this->_sql_statement('HAVING', $res,' AND ');
	}
	
	/*
	 * look from order statement
	 */
	protected function _sql_order($args) {
		$res = '';
		if (isset($args['order'])) {
			$res = $args['order'];
		} else if (isset($args['order_by'])) {
			$res = $args['order_by'];
		} else {
			return '';
		}
		return $this->_sql_statement('ORDER BY', $res,', ');
	}
	
	/**
	 * limit statement
	 */
	protected function _sql_limit($args) {
		if (!isset($args['page_size']) && !isset($args['page_size_default'])) {
			return false;
		}
		if (isset($args['page_size'])) {
			$this->_page_size = intval($args['page_size']);
		}
		if (!$this->_page_size && isset($args['page_size_default'])) {
			$this->_page_size = intval($args['page_size_default']);
		}
		if (isset($args['page'])) {
			$this->_page = intval($args['page']);
		}
		if (!$this->_page) {
			$this->_page = 1;
		}
		if ($args['count'] !== true) {
			// not using post count method, so total is set
			// check that page number is not over the top
			while ((($this->_page - 1) * $this->_page_size) > $this->total) {
				$this->_page--;
			}
		}
		return ' LIMIT '.($this->_page - 1) * $this->_page_size.', '
			.$this->_page_size;
	}
	
	/**
	* save file document
	*/
	protected function _save_doc($key) {
		// TODO - save file
		// replace object data with filepath
	}
	
	/**
	* save file image
	*/
	protected function _save_img($key) {
		// TODO - save file
		// create thumbnails if necessary
		// replace object data with filepath
	}
	
	/**
	 * guess format corresponding to object property's format
	 * to use in WP function prepare()
	 */
	protected function _prepare_sql_property($key, $type) {
		switch($type) {
		case 'UID':
			$value = $this->get($key);
			if (is_integer($value) || preg_match('/^[0-9]+$/', $value)) {
				$fmt =  '%d';
				break;
			} else {
				$fmt =  '%s';
				break;
			}
		case 'NUM': // positive number
		case 'INT': // any integer
		case 'BOL': // boolean
			$fmt =  '%d';
			break;
		case 'DEC': // float
			$fmt =  '%f';
			break;
		case 'DTE': // date
		case 'DTM': // date time
		case 'DUR': // duration in seconds
		case 'TIM': // time hh:mm
		case 'TMZ': // timezone
		case 'EML': // email
		case 'URL': // URL
			$fmt =  '%s';
			break;
		case 'STR':
		case 'TXT':
		case 'BBS':
		case 'HTM':
			$fmt =  '%s';
			break;
		case 'OBJ':
			$id = $this->data[$key]->get_uid();
			// remove object, replace by UID
			unset($this->data[$key]);
			$key = $key.'_id';
			$this->data[$key] = $id;
			// guess format
			if (is_integer($id) || preg_match('/^[0-9]+$/', $id)) {
				$fmt =  '%d';
				break;
			} else {
				$fmt =  '%s';
				break;
			} 
		case 'DOC':
			$this->_save_doc($key);
			$fmt =  '%s';
			break;
		case 'IMG':
			$this->_save_img($key);
			$fmt =  '%s';
			break;
		default:
			$fmt =  '%s';
		}
		return array($key, $fmt);
	}
	
	/**
	 * prepare SQL data for insert and update
	 * loop through properties to save files
	 * returns array of formats for prepare
	 */
	protected function _prepare_sql_properties() {
		$_formats = array();
		foreach ($this->properties as $key => $type) {
			if (!isset($this->data[$key])) {
				continue;
			}
			list($key, $fmt) = $this->_prepare_sql_property($key, $type);
			$_formats[$key] = $fmt;
		}
		$formats = array();
		foreach (array_keys($this->data) as $key) {
			$formats[] = $_formats[$key];
		}
		return $formats;
	}
	
	/**
	 * insert new row in database
	 * if args['ignore'] then adds ignore
	 */
	public function insert($option='') {
		// save files and flatten objects (replace object by its ID)
		$formats = $this->_prepare_sql_properties();
		
		// prepare SQL
		$sql = 'INSERT';
		if ($option == 'ignore') {
			$sql .= ' IGNORE';
		} else if ($option == 'replace') {
			$sql = 'REPLACE';
		}
		$sql .= ' INTO `'.$this->db_table().'`'
			.' (`'.implode( '`,`', array_keys($this->data) ).'`)'
			.' VALUES (' . implode( ",", $formats ) . ')';
		$insert_result = $this->db->query($this->db->prepare($sql, $this->data));
		if ( ($option == 'ignore' && $insert_result !== false) || $insert_result ) {
			if (is_string($this->key)) {
				$this->set_uid($this->db->insert_id);
				return $this->get_uid();
			}
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * replace (add or update)
	 */
	public function replace() {
		return $this->insert('replace');
	}
	
	/**
	 * update database
	 * TODO select fields
	 */
	public function update($args='') {
		// save files and flatten objects (replace object by its ID)
		$this->_prepare_sql_properties();
		
		// update
		$this->db->update($this->db_table(), $this->data, $this->_sql_where_array($args));
	}
	
	/**
	 * save (insert or update if UID is set)
	 */
	public function save() {
		if ($this->get_uid()) {
			return $this->update();
		} else {
			return $this->insert();
		}
	}
	
	/**
	 * delete entry from database
	 */
	public function delete($args='') {
		// TODO loop through properties to delete files
		$sql = "DELETE FROM `".$this->db_table()."`".$this->_sql_where($args);
		return $this->db->query($sql);
	}
	
	/**
	 * load one row
	 */
	public function load($args='') {
		$sql = $this->_sql_init($args); // check if sql is provided
		if (!$sql) {
			$sql = $this->_sql_select()
				.$this->_sql_where($args);
		}
		
		$data = $this->db->get_row($sql, ARRAY_A);
		if ($this->db->num_rows) {
			$this->idx = 0;
			$this->rows = array(0 => $data);
			$this->next();
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * count rows
	 */
	public function load_count($args='') {
		
		$sql = $this->_sql_init($args); // check if sql is provided
		
		if (!$sql) {
			$join = array();
			$sel = 'SELECT COUNT(*)';
			$from = ' FROM `'.$this->db_table().'`';
			foreach ($this->properties as $key => $type) {
				$t = substr($type, 0, 3);
				if ($t == 'OBJ') {
					$obj = $this->init_object($key, '', $type);
					$join[$key] = $obj;
				}
			}
			if (count($join)) {
				$from .= ' AS '.$this->db_alias();
				foreach ($join as $key => $obj) {
					$from .= ' LEFT JOIN `'.$obj->db_table().'` AS '.$obj->db_alias()
						.' ON '.$this->db_alias().'.'.$key.'='.$obj->db_alias().'.'.$this->get_key();
				}
			}
			$sql = $sel.$from;
		}
		
		$sql .= $this->_sql_where($args)
			.$this->_sql_group($args)
			.$this->_sql_having($args)
			.$this->_sql_order($args);
			
		return $this->db->get_var($sql);
		
	}
		
	/**
	 * load multiple rows
	 * @args array of arguments
	 * [sql] row selection sql query (select, from, join, but NO where, group, order, limit)
	 * [count] total count sql query (to get total number of rows, NO where, etc.)
	 * 		count found rows (true by default in no query, false by default if query provided)
	 * [where] where condition (array or string, array is safer but limited - this is compulsory)
	 * [group] group by statement, as a string
	 * [having] having statement, as a string
	 * [order] order by statement, as a string
	 */
	public function load_list($args='') {
	
		$this->idx = 0;
		$this->total = 0;
		
		$this->data = $this->rows = array();
		
		$sql = $this->_sql_init($args); // check if sql is provided
		
		// check count parameter, and set default counting method parameter
		$count = isset($args['count'])?$args['count']:($sql?false:(empty($args['page_size'])?false:true));
		$args['count'] = $count;
		
		// prepare conditions
		$sql_conditions = $this->_sql_where($args)
			.$this->_sql_group($args)
			.$this->_sql_having($args)
			.$this->_sql_order($args);
		
		if (!$sql) {
			// SQL not provided		
			// generate SQL query
			$sql = $this->_sql_select($args);
		}
		
		$sql .= $sql_conditions;
		
		$foundrows = false; // don't count total if not needed

		if (!empty($args['page_size'])) {
			// pagination is ON, total count is needed
			
			$foundrows = true; // retreive using FOUND_ROWS (yes by default)
					
			if ($count === true) {
				// use SQL_CALC_FOUND_ROWS to count total rows
				$foundrows = true;
				if (!preg_match('/^SELECT SQL_CALC_FOUND_ROWS/i', $sql)) {
					$sql = preg_replace('/SELECT/i','SELECT SQL_CALC_FOUND_ROWS', $sql, 1);
				}
			} else {
				// separate query to count rows, call before selecting rows
				if ($count) {
					// SQL is provided to count total rows
					$this->total = $this->db->get_var($count.$sql_conditions);
					if (!$this->total) {
						// no rows found, no need to continue
						return false;
					}
					$foundrows = false;
				} else {
					// generate separate SQL query to count total rows
					$this->total = $this->load_count($args);
				}
				
				if (!$this->total) {
					// no rows found, no need to continue
					return false;
				}
			}
		}
		
		// add limit statement to SQL
		$sql .= $this->_sql_limit($args);
		
		// now select rows (actual data)
		$data = $this->db->get_results($sql, ARRAY_A);
		
		if ($n = $this->db->num_rows) {
		
			$this->rows = $data;
			
			// auto count ?
			if ($foundrows) {
				// it's time to get total number of rows
				$this->total = $this->db->get_var('SELECT FOUND_ROWS()');
			}
			
			return $n;
			
		} else {
			return false;
		}
		
	}
	
	/**
	 * number of rows found
	 */
	public function count() {
		return count($this->rows);
	}
	
	/**
	 * how many rows is there in total
	 */
	public function total() {
		return $this->total;
	}
	
	/**
	 * check if there's more results
	 */
	public function more() {
		return ($this->idx < count($this->rows));
	}
	
	/**
	 * move to next result
	 */
	public function next($returnobject=false) {
		if ($this->more()) {
			if ($returnobject) {
				$c = get_class($this);
				$obj = new $c();
				$obj->set_data($this->rows[$this->idx]);
				$this->idx++;
				return $obj;
			} else {
				$this->set_data($this->rows[$this->idx]);
				$this->idx++;
				return true;
			}
		} else {
			$this->idx = 0;
			return false;
		}
	}
	
	/**
	 * reset index for more() and next()
	 */
	public function reset() {
		$this->idx = 0;
	}
	
	/**
	 * clear data
	 */
	public function free() {
		$this->rows = array();
		$this->data = array();
	}
	
	/* ==== PAGINATION HELPERS ================================================ */
	
	/* ----- Pagination ------ */
	
	/**
	 * returns links to pages
	 * @link base link
	 * @param parameter to change in link
	 */
	function pagination_links($link, $param, $class='page-numbers',$classon='current') {
		
		if (!$this->_page_size) return false;
		
		$max = 0;
		// loop determining number of pages total
		while (($max * $this->_page_size) < $this->total) {
			$max++;
		}
		
		$str = '';
		
		if ($max > 0) { // no page = no pagination

			$link = preg_replace('/([?|&amp;]'.$param.'\=[0-9a-zA-Z% ]*)/i','',$link);
			if (preg_match('/\?/',$link)) {
				$link .= '&amp;';
			} else {
				$link .= '?';
			}
			$link .= $param.'=';
			
			$first = true;
			$start = 1;
			$stop = $max;
			if ($max > 10) {
				$start = $this->_page - 7;
				if ($start < 1) {
					$start = 1;
				}
				$stop = $start + 14;
				if ($stop > $max) {
					$diff = $stop - $max;
					$stop = $max;
					$start -= $diff;
				}
				if ($start < 1) {
					$start = 1;
				}
			}
			
			// back to first link
			if ($start > 1) {
				$str = '<a href="'.tzn_tools::concat_url($link,$param.'=1').'">&lt;&lt;</a> ';
			}
			
			// previous link
			$str .= $this->pagination_previous($link, $param);

			for ($i=$start; $i<=$stop; $i++) {
			
				$str .= ' ';
				
				if ($this->_page == $i) {
					$str .= '<span class="'.$class.' '.$classon.'">'.$i.'</span>';
				} else {
					$str .= '<a href="'.$link.$i.'" class="'.$class.'">'.$i.'</a>';
				}
			}
			
			$str .= ' '.$this->pagination_next($link, $param);
			
			if ($stop < $max) {
				$str .= ' <a href="'.tzn_tools::concat_url($link,$param.'='.$max).'">&gt;&gt;</a>';
			}	
		}
		
		return $str;
	}

	function pagination_has_previous() {
		if (empty($this->_page) || ($this->_page <= 1)) {
			return false;
		} else {
			return true;
		}
	}

	function pagination_previous($link, $param, $text = "&lt;")
	{
		$str = '<span>'.$text.'</span>';
		if ($this->pagination_has_previous()) {
			$begin = '<a ';
			$begin .= 'href="'.$link;
			if (preg_match('/\?/',$link)) {
				$begin .= '&amp;';
			} else {
				$begin .= '?';
			}
			$begin.= $param.'=';
			$end = '">';
			$str = $begin.($this->_page-1).$end.$text.'</a>';
		}
		return $str;
	}

	function pagination_has_next() {
		if (!$this->_page_size) return false;
		if (empty($this->_page)) {
			$this->_page = 1;
		}
		if (($this->_page * $this->_page_size) >= ($this->total)) {
			return false;
		} else {
			return true;
		}
	}

	function pagination_next($link, $param, $text = "&gt;") 
	{
		$str = '<span>'.$text.'</span>';
		if ($this->pagination_has_next()) {
			$begin = '<a ';
			$begin .= 'href="'.$link;
			if (preg_match('/\?/',$link)) {
				$begin .= '&amp;';
			} else {
				$begin .= '?';
			}
			$begin.= $param.'=';
			$end = '">';
			$str = $begin.($this->_page+1).$end.$text.'</a>';
		}
		return $str;
	}
	
	function pagination_needed() {
		return ($this->pagination_has_previous() || $this->pagination_has_next());
	}

	/**
	 * display pagination sstatistics
	 * TODO : translate
	 */
	function pagination_stats() {
	   $tmp = (($this->_page - 1) * $this->_page_size)+1;
	   return 'Displaying '.$tmp.'-'.(($tmp-1) + $this->count()).' of '.$this->total;
	}
	
	/**
	 * get number of pages found
	 * pagination helper for WP paginate_links
	 */
	function pagination_wp_links($link, $param='pg') {
		$t = ceil($this->total / $this->_page_size);
		$format = '?'.$param;
		if (preg_match('/\?/', $link)) {
			$format = '&amp;'.$param;
		}
		$format .= '=%#%';
		$str = paginate_links(array(
			'base'		=> $link.'%_%',
			'format'	=> $format,
			'current'	=> $this->_page,
			'total'		=> $t
		));
		return '<div class="tablenav">'
			.'<div class="tablenav-pages">'
			.'<span class="displaying-num">'.$this->pagination_stats().'</span>'
			.$str
			.'</div>'
			.'</div>';
	}
		
}