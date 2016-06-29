<?php
@include_once('JSON.php');
@include_once ('DEV.php');
class get_posts
{
	function __construct() 
	{
		add_filter('rewrite_rules_array','get_posts::insertRules');
		add_filter('query_vars','get_posts::insertQueryVars');
		add_action('parse_query','get_posts::insertParseQuery');	
	}
	static function posts_info($dev,$comm,$con,$type)
	{
		global $wpdb;
		$sql = 'SELECT DISTINCT * FROM '.$wpdb->posts.' AS POSTS WHERE POSTS.post_status="publish"';
        $obj = $wpdb->get_results($sql);
		$count_total = 0;
		foreach($obj as $num)
		{
			$count_total++;
		}
		$count = $count_total;
		$page =  get_posts::_numpage($count_total,$count);
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if((int) $type)
		{
			$check_err = false;
			$err_msg = 'type can not an integer number';
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
			if(empty($type))
			{
				foreach($obj as $key => $value)
				{
					$cat = get_the_category($value->ID);
					$tag = get_the_tags($value->ID);
					$author = get_posts::return_author($value->post_author);
					$exp = explode("\n",$value->post_content);
					if(empty($value->post_excerpt))
					{
						if(count($exp) > 1)
						{
							$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
							$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
						}
						else
						{
							$value->post_excerpt = $value->post_content;
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
						}	
					}
					else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';	 
					if($tag == false)
					{
						$tag = array();
					}
					if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);
					}
					if($con == 1 or ($con == 1 and $comm == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);					
					}
					if($comm == 1 or ($comm == 1 and $con == 0))
					{
						$com = get_posts::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}		
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);
					}
					if($comm == 1 and $con == 1)
					{
						$com = get_posts::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);					
					}
					$posts[] = $obj[$key];
				}
			}
			else if(!empty($type) and !(int) $type)
			{
				$check_type = '';
				$count_total_type = 0;
				$count_type;
				foreach($obj as $key => $value)
				{
					if($value->post_type == $type)
					{
						$count_total_type++;
						$check_type = true;
						$cat = get_the_category($value->ID);
						$tag = get_the_tags($value->ID);
						$author = get_posts::return_author($value->post_author);
						$exp = explode("\n",$value->post_content);
						if(empty($value->post_excerpt))
						{
							if(count($exp) > 1)
							{
								$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
								$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
							}
							else
							{
								$value->post_excerpt = $value->post_content;
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
							}	
						}
						else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
						if($tag == false)
						{
							$tag = array();
						}
						if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
						{
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status
							);
						}
						if($con == 1 or ($con == 1 and $comm == 0))
						{
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'content' => $value->post_content,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status
							);					
						}
						if($comm == 1 or ($comm == 1 and $con == 0))
						{
							$com = get_posts::return_comment($value->ID);
							if($com == null)
							{
								$com = array();
							}		
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status,
							'comments' => $com
							);
						}
						if($comm == 1 and $con == 1)
						{
							$com = get_posts::return_comment($value->ID);
							if($com == null)
							{
								$com = array();
							}
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'content' => $value->post_content,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status,
							'comments' => $com
							);					
						}
						$posts[] = $obj[$key];
					}
					else if($value->post_type != $type and $check_type != true)
					{
						$check_type = false;	
					}
				}
				if($check_type == false)
				{
					$check_err = false;
					$err_msg = 'this type not found';
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
						exit();
					}
					if ($dev != 1)
					{
						print ($encode);
						exit();
					}		
				}
				$count_type = $count_total_type;
				$page_type = $count_total_type / $count_type;
				if(empty($posts))
				{
					$posts = array();
				}
				@rsort($posts);
				$info = array(
				'status' => $status,
				'count' => $count_type,
				'count_total' => $count_total_type,
				'pages' => $page_type,
				'posts' => $posts
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
				 if($dev != 1)
				 {
					print ($encode);
					exit();
				 }
			}
			if(empty($posts))
			{
				$posts = array();
			}
			@rsort($posts);
			$info = array(
			'status' => $status,
			'count' => $count,
			'count_total' => $count_total,
			'pages' => $page,
			'posts' => $posts
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
	static function return_comment($id)
	{
		global $wpdb;
		$sqlc = 'SELECT DISTINCT * FROM '.$wpdb->comments.' AS COMM
                 WHERE COMM.comment_post_id="'.$id.'" AND COMM.comment_approved = 1';
		$obj = $wpdb->get_results($sqlc);
		foreach ($obj as $key => $comment)
		{
			$obj[$key] = array(
			'id' => $comment->comment_ID,
			'author' => $comment->comment_author,
			'author_url' => $comment->comment_author_url,
			'parent' => $comment->comment_parent,
			'date' => $comment->comment_date,
			'content' => $comment->comment_content,
			'gravatar' => get_posts::get_gravatar($comment->comment_author_email)
			);
			$comments[] = $obj[$key];
		}		
		return $comments;
	}                       	
	static function return_author($id)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->users;
		$obj = $wpdb->get_results($sql);
		foreach($obj as $key => $value)
			{
				if($id == $value->ID)
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
				'gravatar' => get_posts::get_gravatar($value->user_email)
				);
				$authors[] = $obj[$key];
				return $authors;
				}
			}
			
	}
	static function get_id_info($dev,$ID,$comm,$con) 
	{	
		global $wpdb;
		$sql = 'SELECT DISTINCT * FROM '.$wpdb->posts.' AS POSTS WHERE POSTS.post_status="publish"';
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
			$err_msg = 'the query can not connect to database ';
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
					$cat = get_the_category($value->ID);
					$tag = get_the_tags($value->ID);
					$author = get_posts::return_author($value->post_author);
					$exp = explode("\n",$value->post_content);
					if(empty($value->post_excerpt))
					{
						if(count($exp) > 1)
						{
							$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
							$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
						}
						else
						{
							$value->post_excerpt = $value->post_content;
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
						}	
					}
					else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
					if($tag == false)
					{
						$tag = array();
					}
					if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);
					}
					if($comm == 1 or ($comm == 1 and $con == 0))
					{
						$com = get_posts::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}		
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);
					}
				if($con == 1 or ($con == 1 and $comm == 0))
				{
					$obj[$key] = array(
					'id' => $value->ID,
					'type' => $value->post_type,
					'slug' => $value->post_name,
					'url' => $value->guid,
					'status' => $value->post_status,
					'title' => $value->post_title,
					'title_plain' => $value->post_title,
					'content' => $value->post_content,
					'date' => $value->post_date,
					'modified' => $value->post_modified,
					'excerpt' => $value->post_excerpt,
					'parent' => $value->post_parent,
					'category' => $cat,
					'tag' => $tag,
					'author' => $author,
					'comment_count' => $value->comment_count,
					'comment_status' => $value->comment_status
					);					
				}
				if($comm == 1 and $con == 1)
				{
					$com = get_posts::return_comment($value->ID);
					if($com == null)
					{
						$com = array();
					}
					$obj[$key] = array(
					'id' => $value->ID,
					'type' => $value->post_type,
					'slug' => $value->post_name,
					'url' => $value->guid,
					'status' => $value->post_status,
					'title' => $value->post_title,
					'title_plain' => $value->post_title,
					'content' => $value->post_content,
					'date' => $value->post_date,
					'modified' => $value->post_modified,
					'excerpt' => $value->post_excerpt,
					'parent' => $value->post_parent,
					'category' => $cat,
					'tag' => $tag,
					'author' => $author,
					'comment_count' => $value->comment_count,
					'comment_status' => $value->comment_status,
					'comments' => $com
					);					
				}
					$posts[] = $obj[$key];
					$info = array(
					'status' => $status,
					'posts' => $posts
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
	static function get_count_page($dev,$co,$pa,$comm,$con,$type) {
		global $wpdb;
		$sql = 'SELECT DISTINCT * FROM '.$wpdb->posts.' AS POSTS WHERE POSTS.post_status="publish"';
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
			$err_msg = 'count can not be lower than count_total';
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
			$err_msg = 'the query can not connect to database ';
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
		if((int) $type)
		{
			$check_err = false;
			$err_msg = 'type can not an integer number';
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
			if(empty($type))
			{
				$page = get_posts::_numpage($count_total,$co);
				foreach($obj as $key => $value)
				{
					$cat = get_the_category($value->ID);
					$tag = get_the_tags($value->ID);
					$author = get_posts::return_author($value->post_author);
					$exp = explode("\n",$value->post_content);
					if(empty($value->post_excerpt))
					{
						if(count($exp) > 1)
						{
							$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
							$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
						}
						else
						{
							$value->post_excerpt = $value->post_content;
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
						}	
					}
					else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
					if($tag == false)
					{
						$tag = array();
					}
					if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);
					}
					if($con == 1 or ($con == 1 and $comm == 0))
					{
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status
						);	
					}
					if($comm == 1 or ($comm == 1 and $con == 0))
					{
						$com = get_posts::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}		
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);
					}
					if($comm == 1 and $con == 1)
					{
						$com = get_posts::return_comment($value->ID);
						if($com == null)
						{
							$com = array();
						}
						$obj[$key] = array(
						'id' => $value->ID,
						'type' => $value->post_type,
						'slug' => $value->post_name,
						'url' => $value->guid,
						'status' => $value->post_status,
						'title' => $value->post_title,
						'title_plain' => $value->post_title,
						'content' => $value->post_content,
						'date' => $value->post_date,
						'modified' => $value->post_modified,
						'excerpt' => $value->post_excerpt,
						'parent' => $value->post_parent,
						'category' => $cat,
						'tag' => $tag,
						'author' => $author,
						'comment_count' => $value->comment_count,
						'comment_status' => $value->comment_status,
						'comments' => $com
						);
					}
					$posts[] = $obj[$key];
				}
			}
			else if(!empty($type) and !(int) $type)
			{
				$check_type = '';
				$count_total = 0;
				foreach($obj as $key => $value)
				{
					if($value->post_type == $type)
					{
						$count_total++;
						$check_type = true;
						$cat = get_the_category($value->ID);
						$tag = get_the_tags($value->ID);
						$author = get_posts::return_author($value->post_author);
						$exp = explode("\n",$value->post_content);
					if(empty($value->post_excerpt))
					{
						if(count($exp) > 1)
						{
							$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
							$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
						}
						else
						{
							$value->post_excerpt = $value->post_content;
							$order   = array("\r\n", "\n", "\r");
							$replace = ' ';
							$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
						}	
					}
					else $value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
						if($tag == false)
						{
							$tag = array();
						}
						if(($comm == null and $con == null) or ($comm == 0 and $con == 0))
						{
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status
							);
						}
						if($con == 1 or ($con == 1 and $comm == 0))
						{
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'content' => $value->post_content,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status
							);					
						}
						if($comm == 1 or ($comm == 1 and $con == 0))
						{
							$com = get_posts::return_comment($value->ID);
							if($com == null)
							{
								$com = array();
							}		
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status,
							'comments' => $com
							);
						}
						if($comm == 1 and $con == 1)
						{
							$com = get_posts::return_comment($value->ID);
							if($com == null)
							{
								$com = array();
							}
							$obj[$key] = array(
							'id' => $value->ID,
							'type' => $value->post_type,
							'slug' => $value->post_name,
							'url' => $value->guid,
							'status' => $value->post_status,
							'title' => $value->post_title,
							'title_plain' => $value->post_title,
							'content' => $value->post_content,
							'date' => $value->post_date,
							'modified' => $value->post_modified,
							'excerpt' => $value->post_excerpt,
							'parent' => $value->post_parent,
							'category' => $cat,
							'tag' => $tag,
							'author' => $author,
							'comment_count' => $value->comment_count,
							'comment_status' => $value->comment_status,
							'comments' => $com
							);					
						}
						$posts[] = $obj[$key];
					}
					else if($value->post_type != $type and $check_type != true)
					{
						$check_type = false;	
					}
				}
				if($check_type == false)
				{
					$check_err = false;
					$err_msg = 'this type not found';
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
						exit();
					}
					if ($dev != 1)
					{
						print ($encode);
						exit();
					}		
				}
			}
			$page = get_posts::_numpage($count_total,$co);
			if(empty($posts))
			{
				$posts = array();	
			}
			@rsort($posts);
			if(empty($pa) or $pa == 1 or $page == 1)
			{
				for($i=0;$i<$co;$i++)
				{
					$post[] = $posts[$i];
				}
				@rsort($post);
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => 1,
				'posts' => $post
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
						if($posts[$i] == null)
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
					if($posts[$i] == null)
					{
						break;
					}
					else 
					{
						$post[] = $posts[$i];
					}
				}		
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => $pa,
				'posts' => $post
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
	static  function insertRules($rules){
		$newrules = array();
		$newrules['redirect/url/(.+)$']='index.php?wpapi=get_posts&dev&id&count&page&comment&content&type';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','dev','id','count','page','comment','content','type');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'get_posts')
		{
			$dev = $_GET['dev'];
			$id = $_GET['id'];
			$page = $_GET['page'];
			$count = $_GET['count'];
			$comm = $_GET['comment'];
			$con = $_GET['content'];
			$type = $_GET['type'];
			if(!empty($query->query_vars['id']) and $query->query_vars['id'] == $id)
			{
				get_posts::get_id_info($dev,$id,$comm,$con);
				header('Content-type: text/plain');
				exit();
				
			} 
			else if(!empty($query->query_vars['count']) and $query->query_vars['count'] == $count and $query->query_vars['page'] == $page)
			{
				get_posts::get_count_page($dev,$count,$page,$comm,$con,$type);
				header('Content-type: text/plain');
				exit();
			} 
			get_posts::posts_info($dev,$comm,$con,$type);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>