<?php
@include_once('JSON.php');
@include_once ('DEV.php');
class comment_api
{
	function __construct() 
	{
		add_filter('rewrite_rules_array','comment_api::insertRules');
		add_filter('query_vars','comment_api::insertQueryVars');
		add_action('parse_query','comment_api::insertParseQuery');	
	}
	static function comment_send($dev,$name,$email,$website,$comment,$post_id,$parent)
	{
		global $wpdb;
		if(empty($name) or empty($email) or empty($comment) or empty($post_id))
		{
			$err_msg = "required arguments are 'name', 'email', 'content', 'post_id' and optional arguments are 'website', 'parent'";
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
			$check = true;
			if((int) $name)
			{
				$err_msg = 'name argument can not be a number';
				$status = 'error';
				$check = false;
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
			if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
			{
				$err_msg = 'please insert a valid email address';
				$status = 'error';
				$check = false;
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
			if(!(int) $post_id or is_float($post_id))
			{
				$err_msg = 'post id must be an integer number';
				$status = 'error';
				$check = false;
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
			if(!(int) $parent and !empty($parent))
			{
				$err_msg = 'parent argument must be an integer number';
				$status = 'error';
				$check = false;
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
			if($parent == null or empty($parent))
			{
				$parent = 0;
			}
			if($check)
			{
				$sql = 'SELECT DISTINCT * FROM '.$wpdb->posts.' AS POSTS WHERE POSTS.post_status="publish"';
        		$obj = $wpdb->get_results($sql);
        		$check_comment_status = true;
        		$is_post = false;
        		foreach ($obj as $key => $value)
        		{
        			if($value->ID == $post_id)
        			{
						$is_post = true;
        				if($value->comment_status == 'closed') $check_comment_status = false;
        				break;
        			}
        		}
        		$sql_c = 'SELECT * FROM '.$wpdb->comments;
        		$obj_c = $wpdb->get_results($sql_c);
        		$check_p = true;
        		foreach ($obj_c as $value_c)
        		{
        			if($parent == $value_c->comment_ID and $post_id == $value_c->comment_post_ID) 
        			{
        				$check_p = true;
						$parent = $value_c->comment_ID;
        				break;
        			}
        			if($parent != $value_c->comment_ID) 
					{
						$check_p = false;
					}
        		}
        		if($check_p == false)
        		{
        			$parent = 0;
        		}
        		if($is_post == false)
        		{
        			$err_msg = "post id not found";
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
				if($check_comment_status == false)
        		{
        			$check = false;
        			$err_msg = "comments are closed for this post id";
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
				$time = current_time('mysql');
				$user_ip = $_SERVER['REMOTE_ADDR'];
				$user_agent = $_SERVER['HTTP_USER_AGENT'];
				$comment_type = 'comment';
				$data = array(
    			'comment_post_ID' => $post_id,
    			'comment_author' => $name,
    			'comment_author_email' => $email,
    			'comment_author_url' => $website,
				'comment_author_IP' => $user_ip,
    			'comment_content' => $comment,
    			'comment_type' => $comment_type,
    			'comment_parent' => $parent,
    			'comment_date' => $time,
				'comment_approved' => 0,
				'comment_agent' => $user_agent
				);
				if(wp_insert_comment($data))
				{
					$err_msg = "the comment has been sent";
        			$status = 'pending';
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
			}
		}
		
	}	
	static  function insertRules($rules){
		$newrules = array();
		$newrules['redirect/url/(.+)$']='index.php?wpapi=comment&dev&name&email&website&content&post_id&parent';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','name','dev','email','website','content','post_id','parent');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'comment')
		{
			$dev = $_GET['dev'];
			$name = $_GET['name'];
			$email = $_GET['email'];
			$website = $_GET['website'];
			$comment = $_GET['content'];
			$post_id = $_GET['post_id'];
			$parent = $_GET['parent'];
			comment_api::comment_send($dev,$name,$email,$website,$comment,$post_id,$parent);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>