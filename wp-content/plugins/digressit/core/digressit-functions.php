<?php
add_action('public_ajax_function', 'live_content_search_ajax');	
add_action('public_ajax_function', 'live_comment_search_ajax');	


function live_content_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;

	$excluded_words = array('the','and');
	//every three letters we give results
	if(strlen($request_params['value']) > 3 && !in_array($request_params['value'], $excluded_words)){


		$posts = null;
		$message = null;		


		$sql = "SELECT * FROM $wpdb->posts p  
				WHERE p.post_status = 'publish' 
				AND ( p.post_type  = 'post' OR  p.post_type  = 'page' ) 
				AND ( p.post_content LIKE \"%".esc_sql($request_params['value'])."%\"  OR p.post_content LIKE \"%".esc_sql($request_params['value'])."%\" ) 
				GROUP BY p.ID LIMIT 3";
	
	
		$posts = $wpdb->get_results($sql);			
		//var_dump($posts);

		$message = null;
		foreach($posts as $p){
			$message .= "<div class='search-result'>".
						"<div class='post-title'><a href='".get_permalink($p->ID)."'>".$p->post_title."</a></div>".
						"</div>";
		}
		

		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
	die(json_encode(array('status' => 0, "message" => '')));
	
	
}



function live_comment_search_ajax($request_params){
	extract($request_params);
	global $wpdb, $current_user;


	$excluded_words = array('the','and');
	//every three letters we give results
	if(strlen($request_params['value']) > 3 && !in_array($request_params['value'], $excluded_words)){


		//$blog_list = get_blog_list( 0, 'all' );

		$posts = null;
		$message = null;		
		//foreach ($blog_list AS $blog) {
		//	switch_to_blog($blog['blog_id']);
		//$sql = "SELECT * FROM $wpdb->posts p,  $wpdb->comments c  WHERE p.ID = c.comment_post_ID AND c.comment_approved = 1 AND p.post_status = 'publish' AND c.comment_content LIKE '%".esc_sql($request_params['value'])."%' GROUP BY comment_ID LIMIT 3";

		$sql = "SELECT *
		FROM $wpdb->comments c, $wpdb->posts p
		WHERE p.ID = c.comment_post_ID
		AND c.comment_approved =1
		AND p.post_status = 'publish'
		AND (c.comment_content LIKE '%".esc_sql($request_params['value'])."%'
              OR c.comment_content LIKE '".esc_sql($request_params['value'])."%' 
              OR c.comment_content LIKE '%".esc_sql($request_params['value'])."')
        GROUP BY comment_ID LIMIT 3";
		$posts = $wpdb->get_results($sql);			
		
		//var_dump($posts);

		foreach($posts as $post){
			$message .= "<div class='search-result'>".
						"<div class='post-title'><a href='".get_permalink($post->ID)."#comment-".$post->comment_ID."'>".substr($post->comment_content, 0, 75)." [...]</a></div>".
						"</div>";
		}
		//	restore_current_blog();
		//}
		die(json_encode(array('status' => count($posts), "message" => $message)));
	}
	die(json_encode(array('status' => 0, "message" => '')));
}

?>