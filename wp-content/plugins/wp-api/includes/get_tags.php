<?php
include_once('JSON.php');
include_once ('DEV.php');
class get_tags 
{
	function __construct() 
	{
		add_filter('rewrite_rules_array','get_tags::insertRules');
		add_filter('query_vars','get_tags::insertQueryVars');
		add_action('parse_query','get_tags::insertParseQuery');	
	}
	static function tag_info($dev,$t,$c)
	{
		if(($t == 1 and $c == 0) or ($t == 1 and $c == null))
		{
			return get_tags::get_tag($dev,$t);
			exit();	
		}
		if(($c == 1 and $t == 0) or ($c == 1 and $t == null))
		{
			return get_tags::get_cat($dev,$c);
			exit();
		}
		if($c == 1 and $t == 1)
		{
			return get_tags::get_tag_cat($dev,$t,$c);
			exit();	
		}
		if($t == null and $c == null) $check_err = true;
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$status = '';
		$check_err = true;
		$err_msg = '';
		$count_total = 0;
		foreach($obj as $num)
		{
			$count_total++;
		}
		$count = $count_total;
		$pages = get_tags::_numpage($count_total, $count);
		if (!$obj)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if (empty($obj))
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
		if(($t == 0 and $c == 0) and ($t != null and $c != null))
		{
			return get_tags::get_not_tag_cat($dev,$t,$c);
			exit();	
		} 
		if($check_err)
		{
			$status = 'ok';
			foreach ($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						$obj[$key] = array(
						'id' => $tag->term_id,
						'slug' => $tag->slug,
						'title' => $tag->name,
						'taxonomy' => $tag_tax->taxonomy,
						'description' => $tag_tax->description,
						'post_count' => $tag_tax->count
						);
						$tags[] = $obj[$key];
					}
				}
			}
			if(empty($tags))
			{
				$tags = array();	
			}
			@rsort($tags);
			$info = array(
			'status' => $status,
			'count' => $count,
			'count_total' => $count_total,
			'pages' => $pages,
			'tags' => $tags
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
	static function get_id_info($dev,$ID)
	{
		global $wpdb;
		$sql = 'SELECT * FROM '.$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
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
			$err_msg = 'query can not connect to database ';
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
			$check = false;
			foreach($obj as $key => $tag)
			{
				if($ID == $tag->term_id)
				{
					foreach ($obj_tax as $key_tax => $tag_tax)
					{			
						if ($tag->term_id == $tag_tax->term_id)
						{
							$obj[$key] = array(
							'id' => $tag->term_id,
							'slug' => $tag->slug,
							'title' => $tag->name,
							'taxonomy' => $tag_tax->taxonomy,
							'description' => $tag_tax->description,
							'post_count' => $tag_tax->count
							);
							$tags[] = $obj[$key];
							$check = true;
						}
					}
				}
			}
			if($check == true)
			{
				$info = array(
				'status' => $status,
				'tags' => $tags
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
			else if($check == false)
			{
				$status = 'error';
				$err_msg = 'id is not available';
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
				$page = 1;
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
		$sql = 'SELECT * FROM '.$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$check_err = true;
		$err_msg = '';
		$status = 'ok';
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
			$err_msg = 'value of count should be an integer number';
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
			$err_msg = 'value of page should be an integer number';
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
			$err_msg = 'query can not connect to database ';
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
			$page = get_tags::_numpage($count_total,$co);
			foreach($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						$obj[$key] = array(
						'id' => $tag->term_id,
						'slug' => $tag->slug,
						'title' => $tag->name,
						'taxonomy' => $tag_tax->taxonomy,
						'description' => $tag_tax->description,
						'post_count' => $tag_tax->count
						);
						$tags[] = $obj[$key];
					}
				}
			}
			@rsort($tags);
			if(empty($pa) or $pa == 1 or $page == 1)
			{
				for($i=0;$i<$co;$i++)
				{
					$tages[] = $tags[$i];
				}
				@rsort($tages);
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => 1,
				'tags' => $tages
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
						if($tags[$i] == null)
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
					if($tags[$i] == null)
					{
						break;
					}
					else 
					{
						$tages[] = $tags[$i];
					}
				}		
				$info = array(
				'status' => $status,
				'count' => $co,
				'count_total' => $count_total,
				'pages' => $page,
				'currPage' => $pa,
				'tags' => $tages
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
	static  function get_tag($dev,$t)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$status = '';
		$check_err = true;
		$err_msg = '';
		$count_total = 0;
		foreach($obj_tax as $num)
		{
			if($num->taxonomy == 'post_tag')
			{
				$count_total++;
			}
		}
		if (!$obj or !$obj_tax)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if (empty($obj) or empty($obj_tax))
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
		if($check_err and $t == 1)
		{
			$status = 'ok';
			foreach ($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						if($tag_tax->taxonomy == 'post_tag')
						{
						$obj[$key] = array(
						'id' => $tag->term_id,
						'slug' => $tag->slug,
						'title' => $tag->name,
						'description' => $tag_tax->description,
						'post_count' => $tag_tax->count
						);
						$tags[] = $obj[$key];
						}
					}
				}
			}
			if(empty($tags))
			{
				$tags = array();
			}
			@rsort($tags);
			$info = array(
			'status' => $status,
			'count_total' => $count_total,
			'tags' => $tags
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
	static  function get_cat($dev,$c)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$status = '';
		$check_err = true;
		$err_msg = '';
		$count_total = 0;
		foreach($obj_tax as $num)
		{
			if($num->taxonomy == 'category' or $num->taxonomy == 'link_category')
			{
			$count_total++;
			}
		}
		if (!$obj or !$obj_tax)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if (empty($obj) or empty($obj_tax))
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
		if($check_err and $c == 1)
		{
			$status = 'ok';
			foreach ($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						if($tag_tax->taxonomy == 'category' or $tag_tax->taxonomy == 'link_category')
						{
							$obj[$key] = array(
							'id' => $tag->term_id,
							'slug' => $tag->slug,
							'title' => $tag->name,
							'taxonomy' => $tag_tax->taxonomy,
							'description' => $tag_tax->description,
							'post_count' => $tag_tax->count,
							'parent_id' => $tag_tax->parent
							);
							$tags[] = $obj[$key];
						}
					}
				}
			}
			if(empty($tags))
			{
				$tags = array();	
			}
			@rsort($tags);
			$info = array(
			'status' => $status,
			'count_total' => $count_total,
			'categories' => $tags
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
	static function get_tag_cat($dev,$t,$c)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$status = '';
		$check_err = true;
		$err_msg = '';
		$count_total_tag = 0;
		foreach($obj_tax as $num)
		{
			if($num->taxonomy == 'post_tag')
			{
				$count_total_tag++;
			}
		}
		$count_total_cat = 0;
		foreach($obj_tax as $num)
		{
			if($num->taxonomy == 'category' or $num->taxonomy == 'link_category')
			{
				$count_total_cat++;
			}
		}
		if (!$obj or !$obj_tax)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if (empty($obj) or empty($obj_tax))
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
			$status = 'ok';
			foreach ($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						if($tag_tax->taxonomy == 'post_tag')
						{
							$obj[$key] = array(
							'id' => $tag->term_id,
							'slug' => $tag->slug,
							'title' => $tag->name,
							'description' => $tag_tax->description,
							'post_count' => $tag_tax->count
							);
							$tags[] = $obj[$key];
						}
						if($tag_tax->taxonomy == 'category' or $tag_tax->taxonomy == 'link_category')
						{
							$obj[$key] = array(
							'id' => $tag->term_id,
							'slug' => $tag->slug,
							'title' => $tag->name,
							'taxonomy' => $tag_tax->taxonomy,
							'description' => $tag_tax->description,
							'post_count' => $tag_tax->count,
							'parent_id' => $tag_tax->parent
							);
							$categories[] = $obj[$key];
						}
					}
				}
			}
			if(empty($tags))
			{
				$tags = array();	
			}
			if(empty($categories))
			{
				$categories = array();	
			}
			@rsort($tags);
			@rsort($categories);
			$info = array(
			'status' => $status,
			'count_total_tag' => $count_total_tag,
			'count_total_category' => $count_total_cat,
			'tags' => $tags,
			'categories' => $categories
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
	static function get_not_tag_cat($dev,$t,$c)
	{
		global $wpdb;
		$sql = "SELECT * FROM ".$wpdb->terms;
		$sql_tax = "SELECT * FROM ".$wpdb->term_taxonomy;
		$obj = $wpdb->get_results($sql);
		$obj_tax = $wpdb->get_results($sql_tax);
		$status = '';
		$check_err = true;
		$err_msg = '';
		$count_total_not_tag = 0;
		foreach($obj_tax as $num)
		{
			if($num->taxonomy != 'post_tag' and $num->taxonomy != 'category' and $num->taxonomy != 'link_category')
			{
				$count_total_not_tag++;
			}
		}
		if (!$obj or !$obj_tax)
		{
			$check_err = false;
			$err_msg = 'the query can not connect to database';
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
		if (empty($obj) or empty($obj_tax))
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
		if($check_err and ($t == 0 and $c == 0))
		{
			$status = 'ok';
			foreach ($obj as $key => $tag)
			{
				foreach ($obj_tax as $key_tax => $tag_tax)
				{
					if ($tag->term_id == $tag_tax->term_id)
					{
						if($tag_tax->taxonomy != 'post_tag' and $tag_tax->taxonomy != 'category' and $tag_tax->taxonomy != 'link_category')
						{
							$obj[$key] = array(
							'id' => $tag->term_id,
							'slug' => $tag->slug,
							'title' => $tag->name,
							'taxonomy' => $tag_tax->taxonomy,
							'description' => $tag_tax->description,
							'post_count' => $tag_tax->count,
							'parent_id' => $tag_tax->parent
							);
							$tags[] = $obj[$key];
						}
					}
				}
			}
			if(empty($tags))
			{
				$tags = array();	
			}
			@rsort($tags);
			$info = array(
			'status' => $status,
			'count_total' => $count_total_not_tag,
			'tags' => $tags
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
	static  function insertRules($rules){
		$newrules = array();
		$newrules['redirect/url/(.+)$']='index.php?wpapi=get_tags&dev&id&count&page&tag&cat';
		return $newrules+$rules;
	}
	static function insertQueryVars($vars){
		array_push($vars, 'wpapi','dev','id','count','page','tag','cat');
		return $vars;
	}
	static function insertParseQuery($query)
	{
		if(!empty($query->query_vars['wpapi']) and $query->query_vars['wpapi'] == 'get_tags')
		{
			$dev = $_GET['dev'];
			$id = $_GET['id'];
			$page = $_GET['page'];
			$count = $_GET['count'];
			$tag = $_GET['tag'];
			$cat = $_GET['cat'];
			if(!empty($query->query_vars['id']) and $query->query_vars['id'] == $id)
			{
				get_tags::get_id_info($dev,$id);
				header('Content-type: text/plain');
				exit();
				
			}
			if(!empty($query->query_vars['count']) and $query->query_vars['count'] == $count and $query->query_vars['page'] == $page)
			{
				get_tags::get_count_page($dev,$count,$page);
				header('Content-type: text/plain');
				exit();
			}
			get_tags::tag_info($dev,$tag,$cat);
			header('Content-type: text/plain');
			exit();	
		}
	}
}
?>