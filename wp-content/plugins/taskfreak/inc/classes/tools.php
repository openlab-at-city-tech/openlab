<?php

/*
@package TaskFreak
@since 0.1
@version 1.0

*/

class tzn_tools {

	/**
	 * sanitize any value
	 */
	public static function sanitize($value, $type) {
		$obj = new tzn_model();
		return $obj->sani($value, $type);
	}
	
	/**
	 * get clean baselink
	 */
	public static function baselink() {
		return remove_query_arg(array(
				'mode',
				'proj',
				'filter_project',
				'filter_task',
				'filter_recent',
				'view',
				'edit',
				'js',
				'tfknonce',
				'status',
				't',
				'pg',
				'npg',
				'sort',
				'ord',
				'noheader'
		 ));
	}
	
	
	/**
	 * add / replace parameter in URL
	 */
	public static function concat_url($url, $param) {
    	// hash
    	$hash = '';
		if ($pos = strpos($url,'#')) {
			$hash = substr($url,$pos);
			$url = substr($url,0,$pos);
		}
		if ($pos = strpos($param,'#')) {
			$hash = substr($param,$pos);
		}
		// params
		$url = str_replace('&amp;','&',$url);
		if ($pos = strpos($url,'?')) {
			$arrParam = explode('=',$param);
			if (strpos($url,$arrParam[0].'=')) {
				// parameter already in url
				$strQuery = substr($url,$pos+1);
				$arrQuery = explode('&',$strQuery);
				$arrResult = array();
				$found = false;
				foreach ($arrQuery as $value) {
					if (preg_match('/^'.$arrParam[0].'=/', $value)) {
                        if ($arrParam[1]) {
                            // add only if has a value
    						$arrResult[] = $param;
                        }
						$found = true;
					} else {
						$arrResult[] = $value;
					}
				}
				if ($found) {
					$url = substr($url,0,$pos).'?'.implode('&',$arrResult);
				} else {
					$url .= '&'.$param;
				}
			} else {
				$url .= '&'.$param;
			}
    	} else {
    		$url .= '?'.$param;
    	}
    	return str_replace('&','&amp;',$url).$hash;
	}
	
	/* ==== HTML FORM HELPERS ======================================== */
	
	/**
	 * generates a <select> from a list
	 */
	public static function form_select($name, $list, $selected, $xtra) {		
	    $str = '<select name="'.$name.'"';
	    if ($xtra) {
		    $str .= ' '.$xtra;
	    }
	    $str .= '>';
	    foreach ($list as $p => $label) {
	    	$str .= '<option';
		    if ($selected == $p) { $str .= ' selected="selected"'; }
			$str .= ' value="'.$p.'">'.$label.'</option>';
	    }
	    $str .= '</select>';
		return $str;
	}
	
	/**
	 * generates links from a list
	 */
	public static function form_links($url, $name, $list, $selected) {
		$str = '';
		foreach ($list as $p => $label) {
			if ("$selected" == "$p") {
				$str .= '<li class="tfk_selected_filter">'.$label.'</li>';
			} else {
				$str .= '<li><a href="'.esc_url(add_query_arg($name, $p, $url)).'">'.$label.'</a></li>';
			}
		}
		return $str;
	}
	
	/* ==== STRING HELPERS =========================================== */
	
	/**
	 * transforms a string from CamelCasing to flat_with_underscores
	 */
	public static function camel_to_flat($str, $sep='_') {
		$str = preg_replace('/(?<=\\w)(?=[A-Z])/',"$sep$1", trim($str));
		return strtolower($str);
	}
	
	/**
	 * transforms a string from flat_with_underscores to CamelCasing
	 */
	public static function flat_to_camel($str, $firstCap=false, $sep='_') {
		$arr = explode($sep,trim($str));
		$str = '';
		foreach($arr as $sep) {
			if ((!$str && $firstCap) || $str) {
				$str .= ucfirst($sep);
			} else {
				$str .= $sep;
			}
		}
		return $str;
	}
	
	/**
	 * gets any kind of arguments and returns an array
	 * (an array, a string containing values separated by commas, or many arguments)
	 */
	public static function mixed_to_array() {
		$arg = func_get_arg(0);
		if (empty($arg)) {
			return array();
		} else if (is_array($arg)) {
			return $arg;
		} else if (func_num_args() == 1) {
			$arr = explode(',',$arg);
			array_walk($arr, 'trim');
			return $arr;
		} else {
			return func_get_args();
		}
	}
	
	/**
	 * generate random string
	 */
	public static function get_random($len = 32, $strChars = 'abcdefghijklmnopqrstuvwxyz0123456789')
	{
		$strCode = "";
		$intLenChars = strlen($strChars);
		for ( $i = 0; $i < $len; $i++ )	{
			$n = mt_rand(1, $intLenChars);
			$strCode .= substr($strChars, ($n-1), 1);
		}
		return $strCode;
	}
	
	/**
	 * convert string to XML friendly string (no space, no special caracters)
	 */
	public static function clean_strip($str) {
		$str = trim($str);
		if ($str) {
			/*
			if (constant('APP_CHARSET') == 'UTF-8') {
				$str = utf8_decode($str);
			}
			*/
			$str = utf8_decode($str);
			$str = preg_replace(
				array('/[àáäâ]/','/[èéëê]/','/[ìíïî]/','/[òóöôø]/','/[ùúüû]/','/[çć]/','/[ \'\?\/\\&"]/'),
				array('a','e','i','o','u','c','-'),
				strtolower($str));
			$str = preg_replace('/[^a-z0-9\-]/','_',$str);
			$str = str_replace('---','-',$str);
			$str = trim($str,'_');
			$str = trim($str,'-');
		}
		return $str;
	}
	
	/* ==== DATE HELPERS ============================================ */
	
	/**
	 * get current user's timezone offset in seconds
	 * note: currently uses WP timezone's setting
	 * @todo support user's timezone
	 */
	public static function get_user_timezone_offset() {
		return get_option('gmt_offset')*3600;
	}
	
	/**
	 * tries to convert string to unix time using strtotime
	 */
	public static function str_to_unix($val) {
		if (!$val || preg_match('/^(0000|9999)/',$val)) {
			return 0;
		}
		$t = strtotime($val);
		if ($t === false) {
			return false;
		}
		return $t;
	}
	
	/**
	 * parse date from human readable to SQL format
	 */
	public static function parse_date($val) {
		if (empty($val)) {
			return '0000-00-00';
		}
		if (preg_match('/^[0|2|9][0|1|9][0-9]{2}\-[0-1][0-9]\-[0-3][0-9]$/', $val)) {
			// SQL format, return as it is (no timezone evaluation)
			return $val;
		}
		if (!get_option('datetime_us_format')) {
			// try to parse non US format (dd/mm/yy)
			if (preg_match('/^([0-3]?[0-9])\/([0-1][0-9])(\/(20)?[0-9]{2})?$/',$val, $arr)) {
				$year = date('Y');
				if (empty($arr[3])) {
					$arr[3] = $year;
				} else if (strlen($arr[3]) == 2) {
					$arr[3] = substr($year,0,2).substr($arr[3],1);
				} else {
					$arr[3] = substr($arr[3],1);
				}
				return $arr[3].'-'.$arr[2].'-'.$arr[1];
			}
		}
		// try human readable formats (english only)
		if ($t = self::str_to_unix($val)) {
			return strftime('%Y-%m-%d', $t);
		}
		$info['error'] = 'invalid_date';
		return false;
	}
	
	/**
	 * format date from SQL format to human readable
	 */
	public static function format_date($val, $format='') {
		if (empty($format)) {
			$options = get_option('tfk_options');
			$format = $options['format_date'];
		}
		$t = self::str_to_unix($val);
		if ($t === false) {
			return '/!\\';
		}
		if ($t) {
			return date($format, $t);
		} else {
			return '';
		}
	}
	
	/**
	 * parse duration from human readable to SQL format (seconds)
	 */
	public static function parse_duration($val) {
		if (empty($val)) {
			return 0;
		}
		if (preg_match('/^[0-9]+$/', $val)) {
			// just a number of seconds
			return intval($val);
		} else if (preg_match('/^([0-2]?[0-9])\:([0-5][0-9])(\:([0-5][0-9]))?$/', $val, $arr)) {
			$t = 0;
			switch(count($arr)) {
			case 5:
				// seconds
				$t += intval($arr[4]);
			case 3:
				// minutes
				$t += intval($arr[2])*60;
				$t += intval($arr[1])*3600;
			}
			return $t;
		} else {
			return false;
		}
	}
	
	/**
	 * format duration from SQL format to human readable
	 * eg. 12:00 (12 hours)
	 */
	public static function format_duration($val, $seconds=false) {
		$h = floor($val / 3600);
		$m = floor($val / 60) - ($h*60);
		$s = $val - ($h*3600 + $m*60);
			
		$str = str_pad($h, 2, '0',STR_PAD_LEFT)
			.':'.str_pad($m, 2, '0',STR_PAD_LEFT);
		if ($seconds) {
			$str .= ':'.str_pad($s, 2, '0',STR_PAD_LEFT);
		}
		return $str;
	}
	
	/**
	 * parse time from human readable to SQL format (seconds)
	 * also converts to GMT date to store in database
	 */
	public static function parse_time($val) {
		if ($val = self::parse_duration($val)) {
			$val -= self::get_user_timezone_offset();
		}
		return $val;
	}
	
	/**
	 * format duration from SQL format to human readable
	 * also converts to user's timezone
	 * eg. 12:00 (12 hours)
	 */
	public static function format_time($val) {
		if ($val = self::format_duration($val)) {
			$val += self::get_user_timezone_offset();
		}
		return $val;
	}
	
	/**
	 * format date from SQL format to human readable
	 * also converts to user's timezone
	 */
	public static function parse_datetime($val) {
		if (empty($val)) {
			return '0000-00-00 00:00:00';
		}
		if ($val == 'NOW') {
			return current_time('mysql', 1);
		}
		if (preg_match('/^[0|2|9][0|1|9][0-9]{2}\-[0-1][0-9]\-[0-3][0-9]( ([0-2][0-9]\:[0-5][0-9])(\:[0-5][0-9])?)?$/', $val)) {
			// SQL format, return as it is (no timezone evaluation)
			return $val;
		}
		$d = '';
		$t = 0;
		$arr = explode(' ',$val);
		foreach($arr as $v) {
			if ($tmp = self::parse_time($v)) {
				$t = $tmp;
			} else if ($tmp = self::parse_date($v)) {
				$d = $tmp;
			}
		}
		if ($d && is_integer($t)) {
			$dh = 3600*24;
			if  ($t < 0) {
				// got to go one day back
				$d = strftime('%Y-%m-%d',strtotime($d)-$dh);
				$t = $dh+$t; // + means - as $t is < 0
			} else if ($t > $dh) {
				// got to move on one day
				$d = strftime('%Y-%m-%d',strtotime($d)+$dh);
				$t = $t-$dh;
			}
			// return datetime in SQL format
			return $d.' '.self::format_duration($t, false); // timezone already processed
		}
		return false;
	}
	
	/**
	 * format date from SQL format to human readable
	 * also converts to user's timezone
	 */
	public static function format_datetime($val, $format='', $default=false) {
		if (empty($format)) {
			$options = get_option('tfk_options');
			$format = $options['format_date'].' '.$options['format_time'];
		}
		$t = self::str_to_unix($val);
		if ($t === false) {
			return '/!\\';
		}
		if ($t) {
			$t += self::get_user_timezone_offset();
			return date($format, $t);
		} else {
			return $default;
		}
	}
	
	/**
	 * convert PHP date formats to jQuery datepicker formats
	 */
	public static function date_format_convert_php_jquery($val) {
		return preg_replace(array(
				'/j/',
				'/d/',
				'/z/',
				'/D/',
				'/l/',
				'/n/',
				'/m/',
				'/M/',
				'/F/',
				'/y/',
				'/Y/',
				
		), array(
				'd',
				'dd',
				'o',
				'D',
				'DD',
				'm',
				'mm',
				'M',
				'MM',
				'y',
				'yy',
		), $val);
	}
}