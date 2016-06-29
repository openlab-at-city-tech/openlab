<?php
@include_once('JSON.php');
@include_once ('DEV.php');
class search_api
{
	function __construct() 
	{
		add_filter('rewrite_rules_array','search_api::insertRules');
		add_filter('query_vars','search_api::insertQueryVars');
		add_action('parse_query','search_api::insertParseQuery');	
	}
	
	static function get_result_search($dev,$s,$co,$pa,$con,$comm,$type)
	{
		global $wpdb;
		$status = 'ok';
		$check_err = true;
		$sql = 'SELECT DISTINCT * FROM '.$wpdb->posts.' AS POSTS WHERE POSTS.post_status="publish"';
        $obj = $wpdb->get_results($sql);
		/**
		* This file is an independent controller, used to query the WordPress database
		* and provide search results for Ajax requests.
		*
		* @return string Either return nothing (i.e. no results) or return some formatted results.
		*/
		if (!defined('WP_PLUGIN_URL')) 
		{
			require_once( realpath('../../../').'/wp-config.php' );
		}
		$WP_Query_object = new WP_Query();
		$WP_Query_object->query(array('s' => $_GET['keyword']));
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
				exit();
  			}
  			if ($dev != 1)
  			{
				print ($encode);
				exit();
  			}		
		}
		foreach($WP_Query_object->posts as $result)
		{
			if(empty($type))
			{
				foreach($obj as $key => $value)
				{
					$cat = get_the_category($value->ID);
					$tag = get_the_tags($value->ID);
					$author = get_posts::return_author($value->post_author);
					$com = get_posts::return_comment($value->ID);
					if($com == null)
					{
						$com = array();
					}
					if($tag == false)
					{
						$tag = array();
					}
					if($result->ID == $value->ID and (($comm == null and $con == null) or ($comm == 0 and $con == 0)))
					{
						$exp = explode("\n",$value->post_content);
						if(empty($value->post_excerpt))
						{
							if(count($exp) > 1)
							{
								@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
								@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
							}
							else
							{
								@$value->post_excerpt = $value->post_content;
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
							}	
						}
						else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
						$posts[] = $obj[$key];
					}
					 if($result->ID == $value->ID and ($comm == 1 and $con == 1))
					 {
						$exp = explode("\n",$value->post_content);
						if(empty($value->post_excerpt))
						{
							if(count($exp) > 1)
							{
								@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
								@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
							}
							else
							{
								@$value->post_excerpt = $value->post_content;
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
							}	
						}
						else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
						 
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
							$posts[] = $obj[$key];
					 }
					 if($result->ID == $value->ID and (($comm == 1 and $con == null) or ($comm == 1 and $con == 0)))
						{
							$exp = explode("\n",$value->post_content);
							if(empty($value->post_excerpt))
							{
								if(count($exp) > 1)
								{
									@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
									@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
								}
								else
								{
									@$value->post_excerpt = $value->post_content;
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
								}	
							}
							else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
							$posts[] = $obj[$key];
						}
						if($result->ID == $value->ID and (($con == 1 and $comm == null) or ($con == 1 and $comm == 0)))
						{
							$exp = explode("\n",$value->post_content);
							if(empty($value->post_excerpt))
							{
								if(count($exp) > 1)
								{
									@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
									@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
								}
								else
								{
									@$value->post_excerpt = $value->post_content;
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
								}	
							}
							else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
							$posts[] = $obj[$key];
						}
				}
			}
			else if(!empty($type) and !(int) $type)
			{
				foreach($obj as $key => $value)
				{
					if($result->ID == $value->ID and $value->post_type == $type)
					{
						$cat = get_the_category($value->ID);
						$tag = get_the_tags($value->ID);
						$author = get_posts::return_author($value->post_author);
						$com = get_posts::return_comment($value->ID);
						if($com == null)
					{
						$com = array();
					}
					if($tag == false)
					{
						$tag = array();
					}
					if($result->ID == $value->ID and (($comm == null and $con == null) or ($comm == 0 and $con == 0)))
					{
						$exp = explode("\n",$value->post_content);
						if(empty($value->post_excerpt))
						{
							if(count($exp) > 1)
							{
								@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
								@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
							}
							else
							{
								@$value->post_excerpt = $value->post_content;
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
							}	
						}
						else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
						$posts[] = $obj[$key];
					}
					 if($result->ID == $value->ID and ($comm == 1 and $con == 1))
					 {
						$exp = explode("\n",$value->post_content);
						if(empty($value->post_excerpt))
						{
							if(count($exp) > 1)
							{
								@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
								@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
							}
							else
							{
								@$value->post_excerpt = $value->post_content;
								$order   = array("\r\n", "\n", "\r");
								$replace = ' ';
								@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
							}	
						}
						else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
						 
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
							$posts[] = $obj[$key];
					 }
					 if($result->ID == $value->ID and (($comm == 1 and $con == null) or ($comm == 1 and $con == 0)))
						{
							$exp = explode("\n",$value->post_content);
							if(empty($value->post_excerpt))
							{
								if(count($exp) > 1)
								{
									@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
									@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
								}
								else
								{
									@$value->post_excerpt = $value->post_content;
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
								}	
							}
							else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
							$posts[] = $obj[$key];
						}
						if($result->ID == $value->ID and (($con == 1 and $comm == null) or ($con == 1 and $comm == 0)))
						{
							$exp = explode("\n",$value->post_content);
							if(empty($value->post_excerpt))
							{
								if(count($exp) > 1)
								{
									@$value->post_excerpt = explode(" ",strrev(substr(strip_tags($value->post_content), 0, 175)),2);
									@$value->post_excerpt = strrev($value->post_excerpt[1]).' '.'&hellip; <a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);
								}
								else
								{
									@$value->post_excerpt = $value->post_content;
									$order   = array("\r\n", "\n", "\r");
									$replace = ' ';
									@$value->post_excerpt = str_replace($order, $replace, $value->post_excerpt);	
								}	
							}
							else @$value->post_excerpt = $value->post_excerpt.' '.'<a href="'.$value->guid.'"> Continue reading <span class="meta-nav">&rarr;</span></a>';
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
							$posts[] = $obj[$key];
						}
					}
				}
			}
		}
		if(empty($posts))
		{
			$posts = array();	
		}
		@rsort($posts);
		$count_tot = count($posts);
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
		if(!(int) $co and !empty($co))
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
		if($co > $count_tot and $count_tot != 0)
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
		if($count_tot == 0 and !empty($co) and (int) $co)
		{
			$check_err = false;
			$err_msg = 'no result found';
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
		if($co != null)
		{
			$page = search_api::_numpage($count_tot, $co);
		}
		if ($co == null)
		{
			$page = search_api::_numpage($count_tot, $count_tot);
			$co = $count_tot;
		}
		if($pa == null)
		{
			$curr_page = 1;
		}
		if($pa != null)
		{
			$curr_page = $pa;
		}
		if($check_err) 
		{
		if(empty($pa) or $pa == 1 or $page == 1)
		{
			for($i=0;$i<$co;$i++)
			{
				$post[] = $posts[$i];
			}
			@rsort($post);
			if(empty($post))
			{
				$post = array();	
			}
			$info = array(
			'status' => $status,
			'count' => $co,
			'count_total' => $count_tot,
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
			'count_total' => $count_tot,
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

	static function search_info($dev)
	{
		$err_msg = "Include 'keyword' var in your request";
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
			@$page = $co_tot / $co;
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
		$newrules['redirect/url/(.+)$']='index.php?wpapi=search&dev&keyword&count&page&content&comment&type';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','keyword','dev','count','page','content','comment','type');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'search')
		{
			$dev = $_GET['dev'];
			$key = $_GET['keyword'];
			$count = $_GET['count'];
			$page = $_GET['page'];
			$con = $_GET['content'];
			$comm = $_GET['comment'];
			$type = $_GET['type'];
			if(!empty($query->query_vars['keyword']) and $query->query_vars['keyword'] == $key)
			{
				search_api::get_result_search($dev,$key,$count,$page,$con,$comm,$type);
				header('Content-type: text/plain');
				exit();
			}
			search_api::search_info($dev);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>