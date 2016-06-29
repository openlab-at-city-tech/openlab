<?php
@include_once('JSON.php');
@include_once ('DEV.php');
class get_gravatar
{
	function __construct() 
	{
		add_filter('rewrite_rules_array','get_gravatar::insertRules');
		add_filter('query_vars','get_gravatar::insertQueryVars');
		add_action('parse_query','get_gravatar::insertParseQuery');	
	}
	static function get_gravatar($dev,$email,$s) 
	{
		if(!(int) $s and !empty($s))
		{
			$err_msg = "the size must an be integer number";
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg,
			);
			$json = new Services_JSON();
  			$encode = $json->encode($info);
  			if($dev == 1)
  			{
  				$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			 	exit();
  			}
  			if ($dev != 1)
  			{
				print ($encode);
				exit();
  			}
		}
		$gravatar = get_gravatar::_get_gravatar($email,$s);
		if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
		{
			$err_msg = "please insert a valid email address in 'email' ";
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg,
			);
			$json = new Services_JSON();
  			$encode = $json->encode($info);
  			if($dev == 1)
  			{
  				$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			}
  			if ($dev != 1)
  			{
				print ($encode);
  			}
		}
		else 
		{
			$status = 'ok';
			$info = array(
			'status' => $status,
			'gravatar' => $gravatar
			);
			$json = new Services_JSON();
  			$encode = $json->encode($info);
  			if($dev == 1)
  			{
  				$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			}
  			if ($dev != 1)
  			{
				print ($encode);
  			}
		} 
	}
	static function _get_gravatar( $email, $s, $d = 'mm', $r = 'g', $img = false, $atts = array() ) 
	{
    	$url = 'http://www.gravatar.com/avatar/';
    	$url .= md5( strtolower( trim( $email ) ) );
    	$url .= "?s=$s&d=$d&r=$r";
    	if ( $img ) 
    	{
        	$url = '<img src="' . $url . '"';
        	foreach ( $atts as $key => $val )
            	$url .= ' ' . $key . '="' . $val . '"';
       			$url .= ' />';
    	}
    return $url;
	}
	static  function insertRules($rules){
		$newrules = array();
		$newrules['redirect/url/(.+)$']='index.php?wpapi=gravatar&dev&email&size';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','dev','email','size');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'gravatar')
		{
			$email = $_GET['email'];
			$dev = $_GET['dev'];
			$s = $_GET['size'];
			get_gravatar::get_gravatar($dev,$email,$s);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>