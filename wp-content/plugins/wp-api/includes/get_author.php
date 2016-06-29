<?php
include_once('JSON.php');
include_once('DEV.php');
class get_author
{
	function __construct()
	{
		add_filter('rewrite_rules_array','get_author::insertRules');
		add_filter('query_vars','get_author::insertQueryVars');
		add_action('parse_query','get_author::insertParseQuery');
	}
	static function author_info($dev)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users;
		$obj = $wpdb->get_results($sql);
		$count_total = 0;
		foreach($obj as $num)
		{
			$count_total++;
		}
		$count = $count_total;
		$page =  get_author::_numpage($count_total,$count);
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'query can not connect to database';
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
		if(empty($obj))
		{
			$check_err = false;
			$err_msg = 'the table is empty';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if($check_err)
		{
			foreach($obj as $key => $value)
			{
				$obj[$key] = array(
				'id' => $value->ID,
				'slug' => $value->user_nicename,
				'name' => $value->display_name,
				'first_name' => get_user_meta($value->ID, 'first_name', true),
				'last_name' => get_user_meta($value->ID, 'last_name', true),
				'nickname' => $value->user_nicename,
				'url' => $value->user_url,
				'description' => get_user_meta($value->ID, 'description', true),
				'gravatar' => get_author::get_gravatar($value->user_email)
				);
				$authors[] = $obj[$key];
			}
			if(empty($authors))
			{
				$authors = array();
			}
			@rsort($authors);
			$info = array(
			'status' => $status,
			'count' => $count,
			'count_total' => $count_total,
			'pages' => $page,
			'authors' => $authors
			);
 			 $json = new Services_JSON();
  			 $encode = $json->encode($info);
  			 if($dev == 1)
  			 {
  			 	$dev = new Dev();
  			 	$output = $dev-> json_format($encode);
  			 	print($output);
  			 }
  			 if($dev != 1)
  			 {
  			 	print ($encode);
  			 }
				
		}	
	}
	static function get_id_info($dev,$ID) 
	{	
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users;
		$obj = $wpdb->get_results($sql);
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
		if(!(int) $ID)
		{
			$check_err = false;
			$err_msg = 'id should be an integer number';
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
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'query can not connect to database';
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
		if(empty($obj))
		{
			$check_err = false;
			$err_msg = 'the table is empty';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if($check_err)
		{
			$check = '';
			foreach($obj as $key => $value)
			{
				if($ID == $value->ID)
				{
					$obj[$key] = array(
					'id' => $value->ID,
					'slug' => $value->user_nicename,
					'name' => $value->display_name,
					'first_name' => get_user_meta($value->ID, 'first_name', true),
					'last_name' => get_user_meta($value->ID, 'last_name', true),
					'nickname' => $value->user_nicename,
					'url' => $value->user_url,
					'description' => get_user_meta($value->ID, 'description', true),
					'gravatar' => get_author::get_gravatar($value->user_email)
					);
				$authors[] = $obj[$key];
				$info = array(
				'status' => $status,
				'authors' => $authors
				);
				$json = new Services_JSON();
  			 	$encode = $json->encode($info);
  			 	if($dev == 1)
  			 	{
  			 		$dev = new Dev();
  			 		$output = $dev-> json_format($encode);
  			 		print($output);
  			 		$check = true;
  			 	}
  			 	if($dev != 1)
  			 	{
  			 		print ($encode);
  			 		$check = true;
  			 	}
				}
				if($check == true)
				{
					exit();
				}
				if($ID !== $value->ID)
				{
					$check = false;
				}
			}
		}
			if ($check == false and (int) $ID)
			{
				$status = 'error';
				$err_msg = 'this id is not available';
				$info = array(
				'status' => $status,
				'msg' => $err_msg	
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
	static function _numpage($co_tot,$co)
	{
		if($co > $co_tot)
		{
			$co_tot = $co;
			$page = $co_tot / $co;
			return $page;
		}
		else 
		{
			$page = $co_tot / $co;
			if ($page > 0 and $page < 1)
			{
				$page = 2;
				return $page;
			}
			else if ($page > 1 and $page < 2)
			{
				$page = 2;
				return $page;
			}
			 return ceil($page);
		}
	}
	static function get_count_page($dev,$co,$pa) {
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users;
		$obj = $wpdb->get_results($sql);
		$count_total = 0;
		foreach($obj as $num)
		{
			$count_total++;
		}
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
		if(!(int) $co)
		{
			$check_err = false;
			$err_msg = 'the value of count should be an integer number';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if(!empty($pa) and !(int) $pa)
		{
			$check_err = false;
			$err_msg = 'the value of page should be an integer number';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if($co > $count_total)
		{
			$check_err = false;
			$err_msg = 'count should be lower than count_total';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'query can not connect to database';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if(empty($obj))
		{
			$check_err = false;
			$err_msg = 'the table is empty';
			$status = 'error';
			$info = array(
			'status' => $status,
			'msg' => $err_msg
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
		if($check_err)
		{
			$page = get_author::_numpage($count_total,$co);
			foreach($obj as $key => $value)
			{
				$obj[$key] = array(
				'id' => $value->ID,
				'slug' => $value->user_nicename,
				'name' => $value->display_name,
				'first_name' => get_user_meta($value->ID, 'first_name', true),
				'last_name' => get_user_meta($value->ID, 'last_name', true),
				'nickname' => $value->user_nicename,
				'url' => $value->user_url,
				'description' => get_user_meta($value->ID, 'description', true),
				'gravatar' => get_author::get_gravatar($value->user_email)
				);
				$authors[] = $obj[$key];
			}
			@rsort($authors);
			if(empty($pa) or $pa == 1 or $page == 1)
			{
				for($i=0;$i<$co;$i++)
				{
					$author[] = $authors[$i];
				}
				@rsort($author);
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => 1,
				'authors' => $author
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
			if(!empty($pa) and $pa != 1 and $page != 1)
			{
				if($pa >= $page and $page != 1)
				{
					$pa = $page;
					$count = 0;
					$begin = $co*($pa-1);
					$end = $begin + ($co - 1);
					for($i=$begin;$i<=$end;$i++)
					{
						if($authors[$i] == null)
						{
							break;
						}
						else
						{
							$count++;
							$co = $count;	
						}
					}
				}
				$begin = $co*($pa-1);
				$end = $begin + ($co - 1);
				for($i=$begin;$i<=$end;$i++)
				{
					if($authors[$i] == null)
					{
						break;
					}
					else 
					{
						$author[] = $authors[$i];
					}
				}		
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => $pa,
				'authors' => $author
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
	}
	static function get_gravatar( $email, $s = 100, $d = 'mm', $r = 'g', $img = false, $atts = array() ) 
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
		$newrules['redirect/url/(.+)$']='index.php?wpapi=get_author&dev&id&count&page';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','dev','id','count','page');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'get_author')
		{
			$dev = $_GET['dev'];
			$id = $_GET['id'];
			$page = $_GET['page'];
			$count = $_GET['count'];
			if(!empty($query->query_vars['id']) and $query->query_vars['id'] == $id)
			{
				get_author::get_id_info($dev,$id);
				header('Content-type: text/plain');
				exit();
				
			}
			else if(!empty($query->query_vars['count']) and $query->query_vars['count'] == $count and $query->query_vars['page'] == $page)
			{
				get_author::get_count_page($dev,$count,$page);
				header('Content-type: text/plain');
				exit();
			}
			get_author::author_info($dev);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>