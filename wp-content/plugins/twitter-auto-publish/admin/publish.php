<?php 
if( !defined('ABSPATH') ){ exit();}
/*add_action('publish_post', 'xyz_twap_link_publish');
add_action('publish_page', 'xyz_twap_link_publish');
$xyz_twap_future_to_publish=get_option('xyz_twap_future_to_publish');

if($xyz_twap_future_to_publish==1)
	add_action('future_to_publish', 'xyz_link_twap_future_to_publish');

function xyz_link_twap_future_to_publish($post){
	$postid =$post->ID;
	xyz_twap_link_publish($postid);
}*/
///////////////////////////////////////////////////////////////
add_action(  'transition_post_status',  'xyz_link_twap_future_to_publish', 10, 3 );

function xyz_link_twap_future_to_publish($new_status, $old_status, $post){
	
	if (isset($_GET['_locale']) && empty($_POST))
		return ;
	if(!isset($GLOBALS['twap_dup_publish']))
		$GLOBALS['twap_dup_publish']=array();
	
	$postid =$post->ID;
	$get_post_meta=get_post_meta($postid,"xyz_twap",true);
	
	$post_twitter_permission=get_option('xyz_twap_twpost_permission');
	if(isset($_POST['xyz_twap_twpost_permission'])){
		$post_twitter_permission=$_POST['xyz_twap_twpost_permission'];
		if ( (isset($_POST['xyz_twap_twpost_permission']) && isset($_POST['xyz_twap_twpost_image_permission'])) )
		{
			$futToPubDataArray=array( 'xyz_twap_twpost_permission'	=>	$_POST['xyz_twap_twpost_permission'],
					'xyz_twap_twpost_image_permission'	=>	$_POST['xyz_twap_twpost_image_permission'],
					'xyz_twap_twmessage'	=>	$_POST['xyz_twap_twmessage']);
			update_post_meta($postid, "xyz_twap_future_to_publish", $futToPubDataArray);
		}
	}
	else
	{
		if ($post_twitter_permission == 1) {
			if($new_status == 'publish')
			{
				if ($get_post_meta == 1 ) {
					return;
				}
			}
			else return;
		}
	}
	if($post_twitter_permission == 1)
	{
		if($new_status == 'publish')
		{
			if(!in_array($postid,$GLOBALS['twap_dup_publish'])) {
				$GLOBALS['twap_dup_publish'][]=$postid;
				xyz_twap_link_publish($postid);
			}
		}
		
	}
	//
	 
	
}
////////////////////////////////////////////////////////////////////////////////////
/*$xyz_twap_include_customposttypes=get_option('xyz_twap_include_customposttypes');
$carr=explode(',', $xyz_twap_include_customposttypes);
foreach ($carr  as $cstyps ) {
	add_action('publish_'.$cstyps, 'xyz_twap_link_publish');

}
*/

function xyz_twap_link_publish($post_ID) {
	$_POST_CPY=$_POST;
	$_POST=stripslashes_deep($_POST);
	$post_twitter_image_permission=0;$messagetopost='';
	$get_post_meta_future_data=get_post_meta($post_ID,"xyz_twap_future_to_publish",true);
	$post_twitter_permission=get_option('xyz_twap_twpost_permission');
	if(isset($_POST['xyz_twap_twpost_permission']))
		$post_twitter_permission=$_POST['xyz_twap_twpost_permission'];
	elseif(!empty($get_post_meta_future_data) && get_option('xyz_twap_default_selection_edit')==2 )///select values from post meta
	{
		$post_twitter_permission=$get_post_meta_future_data['xyz_twap_twpost_permission'];
		$post_twitter_image_permission=$get_post_meta_future_data['xyz_twap_twpost_image_permission'];
		$messagetopost=$get_post_meta_future_data['xyz_twap_twmessage'];
	}
	if ($post_twitter_permission != 1) {
		$_POST=$_POST_CPY;
		return ;
	} else if(( (isset($_POST['_inline_edit'])) || (isset($_REQUEST['bulk_edit'])) )  && (get_option('xyz_twap_default_selection_edit') == 0) ) {
		$_POST=$_POST_CPY;
		return;
	}
	global $current_user;
	wp_get_current_user();
	//$af=get_option('xyz_twap_af');
	
	
/////////////twitter//////////
	$tappid=get_option('xyz_twap_twconsumer_id');
	$tappsecret=get_option('xyz_twap_twconsumer_secret');
	$twid=get_option('xyz_twap_tw_id');
	$taccess_token=get_option('xyz_twap_current_twappln_token');
	$taccess_token_secret=get_option('xyz_twap_twaccestok_secret');
	if ($messagetopost=='')
	$messagetopost=get_option('xyz_twap_twmessage');
	if(isset($_POST['xyz_twap_twmessage']))
		$messagetopost=$_POST['xyz_twap_twmessage'];

	if ($post_twitter_image_permission==0)
	$post_twitter_image_permission=get_option('xyz_twap_twpost_image_permission');
	if(isset($_POST['xyz_twap_twpost_image_permission']))
		$post_twitter_image_permission=$_POST['xyz_twap_twpost_image_permission'];

	
	$postpp= get_post($post_ID);global $wpdb;
	$reg_exUrl = "/(http|https)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
	$entries0 = $wpdb->get_results($wpdb->prepare( 'SELECT user_nicename,display_name FROM '.$wpdb->base_prefix.'users WHERE ID=%d',$postpp->post_author));
	foreach( $entries0 as $entry ) {			
		$user_nicename=$entry->user_nicename;
		$user_displayname=$entry->display_name;
	}
	
	if ($postpp->post_status == 'publish')
	{
		$posttype=$postpp->post_type;
		if ($posttype=="page")
		{
			$xyz_twap_include_pages=get_option('xyz_twap_include_pages');	
			if($xyz_twap_include_pages==0)
			{
				$_POST=$_POST_CPY;return;
			}
		}
			
		else if($posttype=="post")
		{
			$xyz_twap_include_posts=get_option('xyz_twap_include_posts');
			if($xyz_twap_include_posts==0)
			{
				$_POST=$_POST_CPY;return;
			}
			
			$xyz_twap_include_categories=get_option('xyz_twap_include_categories');
			if($xyz_twap_include_categories!="All")
			{
				$carr1=explode(',', $xyz_twap_include_categories);
				$defaults = array('fields' => 'ids');
				$carr2=wp_get_post_categories( $post_ID, $defaults );
				$retflag=1;
				foreach ($carr2 as $key=>$catg_ids)
				{
					if(in_array($catg_ids, $carr1))
						$retflag=0;
				}
					
					
				if($retflag==1)
				{$_POST=$_POST_CPY;return;}
			}
		}
		else
		{
			
			$xyz_twap_include_customposttypes=get_option('xyz_twap_include_customposttypes');
			if($xyz_twap_include_customposttypes!='')
			{
				
			$carr=explode(',', $xyz_twap_include_customposttypes);
			
				if(!in_array($posttype, $carr))
				{
					$_POST=$_POST_CPY;return;
				}	
			
			}
			else 
			{
			$_POST=$_POST_CPY;return;	
			}
			
		}

		$get_post_meta=get_post_meta($post_ID,"xyz_twap",true);
		if($get_post_meta!=1)
			add_post_meta($post_ID, "xyz_twap", "1");
		include_once ABSPATH.'wp-admin/includes/plugin.php';
		$pluginName = 'bitly/bitly.php';
		
		if (is_plugin_active($pluginName)) {
			remove_all_filters('post_link');
		}
		$link = get_permalink($postpp->ID);

		
		$xyz_twap_apply_filters=get_option('xyz_twap_apply_filters');
		$ar2=explode(",",$xyz_twap_apply_filters);
		$con_flag=$exc_flag=$tit_flag=0;
		if(isset($ar2))
		{
			if(in_array(1, $ar2)) $con_flag=1;
			if(in_array(2, $ar2)) $exc_flag=1;
			if(in_array(3, $ar2)) $tit_flag=1;
		}
		$content = $postpp->post_content;
		if($con_flag==1)
			$content = apply_filters('the_content', $content);
		
		$content = html_entity_decode($content, ENT_QUOTES, get_bloginfo('charset'));
		$excerpt = $postpp->post_excerpt;
		if($exc_flag==1)
			$excerpt = apply_filters('the_excerpt', $excerpt);
		
		$excerpt = html_entity_decode($excerpt, ENT_QUOTES, get_bloginfo('charset'));
		$content = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $content);
		$content=  preg_replace("/\\[caption.*?\\].*?\\[.caption\\]/is","", $content);
		$content = preg_replace('/\[.+?\]/', '', $content);
		$excerpt = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', "", $excerpt);
		
		if($excerpt=="")
		{
			if($content!="")
			{
				$content1=$content;
				$content1=strip_tags($content1);
				$content1=strip_shortcodes($content1);
				
				$excerpt=implode(' ', array_slice(explode(' ', $content1), 0, 50));
			}
		}
		else
		{
			$excerpt=strip_tags($excerpt);
			$excerpt=strip_shortcodes($excerpt);
		}
		$description = $content;
		
		$description_org=$description;
		$attachmenturl=xyz_twap_getimage($post_ID, $postpp->post_content);
		if($attachmenturl!="")
			$image_found=1;
		else
			$image_found=0;
		
		$name = $postpp->post_title;
		$caption = html_entity_decode(get_bloginfo('title'), ENT_QUOTES, get_bloginfo('charset'));
		
		if($tit_flag==1)
			$name = apply_filters('the_title', $name);
		
		$name = html_entity_decode($name, ENT_QUOTES, get_bloginfo('charset'));
		$name=strip_tags($name);
		$name=strip_shortcodes($name);
		
		$description=strip_tags($description);		
		$description=strip_shortcodes($description);

		$description=str_replace("&nbsp;","",$description);
	
		$excerpt=str_replace("&nbsp;","",$excerpt);


		if($taccess_token!="" && $taccess_token_secret!="" && $tappid!="" && $tappsecret!="" && $post_twitter_permission==1)
		{
			
			////image up start///

			$img_status="";
			if($post_twitter_image_permission==1)
			{
				
				
				$img=array();
				if($attachmenturl!="")
					$img = wp_remote_get($attachmenturl,array('sslverify'=> (get_option('xyz_twap_peer_verification')=='1') ? true : false));
					
				if(is_array($img))
				{
					if (isset($img['body'])&& trim($img['body'])!='')
					{
						$image_found = 1;
							if (($img['headers']['content-length']) && trim($img['headers']['content-length'])!='')
							{
								$img_size=$img['headers']['content-length']/(1024*1024);
								if($img_size>3){$image_found=0;$img_status="Image skipped(greater than 3MB)";}
							}
							
						$img = $img['body'];
					
					}
					else
						$image_found = 0;
				}
					
			}
			///Twitter upload image end/////
			$messagetopost=str_replace("&nbsp;","",$messagetopost);
			
			$substring="";$islink=0;$issubstr=0;
		
			$substring=str_replace('{POST_TITLE}', $name, $messagetopost);
			$substring=str_replace('{BLOG_TITLE}', $caption,$substring);
			$substring=str_replace('{PERMALINK}', $link, $substring);
			$substring=str_replace('{POST_EXCERPT}', $excerpt, $substring);
			$substring=str_replace('{POST_CONTENT}', $description, $substring);
			$substring=str_replace('{USER_NICENAME}', $user_nicename, $substring);
			$substring=str_replace('{USER_DISPLAY_NAME}', $user_displayname, $substring);
			$publish_time=get_the_time(get_option('date_format'),$post_ID );
			$substring=str_replace('{POST_PUBLISH_DATE}', $publish_time, $substring);
			$substring=str_replace('{POST_ID}', $post_ID, $substring);
			preg_match_all($reg_exUrl,$substring,$matches); // @ is same as /
			
			if(is_array($matches) && isset($matches[0]))
			{
				$matches=$matches[0];
				$final_str='';
				$len=0;
				$tw_max_len=get_option('xyz_twap_tw_char_limit');
				if (function_exists('mb_strlen')) {
				foreach ($matches as $key=>$val)
				{
					$url_max_len=23;//23 for https and 22 for http
					$messagepart=mb_substr($substring, 0, mb_strpos($substring, $val));
			
					if(mb_strlen($messagepart)>($tw_max_len-$len))
					{
						$final_str.=mb_substr($messagepart,0,$tw_max_len-$len-3)."...";
						$len+=($tw_max_len-$len);
						break;
					}
					else
					{
						$final_str.=$messagepart;
						$len+=mb_strlen($messagepart);
					}
			
					$cur_url_len=mb_strlen($val);
					if(mb_strlen($val)>$url_max_len)
						$cur_url_len=$url_max_len;
			
					$substring=mb_substr($substring, mb_strpos($substring, $val)+strlen($val));
					if($cur_url_len>($tw_max_len-$len))
					{
						$final_str.="...";
						$len+=3;
						break;
					}
					else
					{
						$final_str.=$val;
						$len+=$cur_url_len;
					}
				}
			
				if(mb_strlen($substring)>0 && $tw_max_len>$len)
				{
			
					if(mb_strlen($substring)>($tw_max_len-$len))
					{
						$final_str.=mb_substr($substring,0,$tw_max_len-$len-3)."...";
					}
					else
					{
						$final_str.=$substring;
					}
				}
				}
				else {
					foreach ($matches as $key=>$val)
					{
							
						$url_max_len=23;
							$messagepart=substr($substring, 0, strpos($substring, $val));
								
							if(strlen($messagepart)>($tw_max_len-$len))
							{
								$final_str.=substr($messagepart,0,$tw_max_len-$len-3)."...";
								$len+=($tw_max_len-$len);
								break;
							}
							else
							{
								$final_str.=$messagepart;
								$len+=strlen($messagepart);
							}
								
							$cur_url_len=strlen($val);
							if(strlen($val)>$url_max_len)
								$cur_url_len=$url_max_len;
									
								$substring=substr($substring, strpos($substring, $val)+strlen($val));
								if($cur_url_len>($tw_max_len-$len))
								{
									$final_str.="...";
									$len+=3;
									break;
								}
								else
								{
									$final_str.=$val;
									$len+=$cur_url_len;
								}
									
					}
						
					if(strlen($substring)>0 && $tw_max_len>$len)
					{
							
						if(strlen($substring)>($tw_max_len-$len))
						{
							$final_str.=substr($substring,0,$tw_max_len-$len-3)."...";
						}
						else
						{
							$final_str.=$substring;
						}
					}
				}
				
			
				$substring=$final_str;
			}
 		 /* if (strlen($substring)>$tw_max_len)
                	$substring=substr($substring, 0, $tw_max_len-3)."...";*/

			$twobj = new TWAPTwitterOAuth(array( 'consumer_key' => $tappid, 'consumer_secret' => $tappsecret, 'user_token' => $taccess_token, 'user_secret' => $taccess_token_secret,'curl_ssl_verifypeer'   => false));
			$tw_publish_status='';
			if($image_found==1 && $post_twitter_image_permission==1)
			{
				$url = 'https://upload.twitter.com/1.1/media/upload.json';
				$img_response = wp_remote_get($attachmenturl,array('sslverify'=> (get_option('xyz_twap_peer_verification')=='1') ? true : false) );
				if ( is_array( $img_response ) ) {
					$img_body = $img_response['body'];
					$params=array('media_data' =>base64_encode($img_body));
				$code = $twobj->request('POST', $url, $params, true,true);
				if ($code == 200)
				{
					$response = json_decode($twobj->response['response']);
					 $media_ids_str = $response->media_id_string;
					$resultfrtw = $twobj->request('POST', $twobj->url('1.1/statuses/update'), array( 'media_ids' => $media_ids_str, 'status' => $substring));
					if($resultfrtw==200)
					{
						if ( $media_ids_str !='')
							$tw_publish_status="<span style=\"color:green\">statuses/update_with_media : Success.</span>";
						else
							$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";
					}
					else 
						$tw_publish_status="<span style=\"color:red\">statuses/update : ".$twobj->response['response']."</span>";
					
				}
				else
				{
					$tw_publish_status="<span style=\"color:red\">statuses/update : ".$twobj->response['response']."</span>";
				}
				}
			}
			else
			{
			$resultfrtw = $twobj->request('POST', $twobj->url('1.1/statuses/update'), array('status' =>$substring));
			if($resultfrtw==200)
				{
					$tw_publish_status="<span style=\"color:green\">statuses/update : Success.</span>";
				}
				elseif($resultfrtw!=200){
					if($twobj->response['response']!="")
					$tw_publish_status="<span style=\"color:red\">".$twobj->response['response'].".</span>";
					else
						$tw_publish_status=$resultfrtw;
				}
				else if($img_status!="")
					$tw_publish_status="<span style=\"color:red\">".$img_status.".</span>";
					
			}
			$tweet_id_string='';
			$resp = json_decode($twobj->response['response']);
			if (isset($resp->id_str) && !empty($resp->id_str)){
				$tweet_link="https://twitter.com/".$twid."/status/".$resp->id_str;
				$tweet_id_string="<br/><span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=".$tweet_link.">View Tweet</a></span>";
					
			}
			elseif (isset($resp->id) && !empty($resp->id)){
				$tweet_link="https://twitter.com/".$twid."/status/".$resp->id;
				$tweet_id_string="<br/><span style=\"color:#21759B;text-decoration:underline;\"><a target=\"_blank\" href=".$tweet_link.">View Tweet</a></span>";
					
			}
			
			
			$tw_publish_status_insert=serialize($tw_publish_status.$tweet_id_string);
			
			$time=time();
			$post_tw_options=array(
					'postid'	=>	$post_ID,
					'acc_type'	=>	"Twitter",
					'publishtime'	=>	$time,
					'status'	=>	$tw_publish_status_insert
			);
			$update_opt_array=array();
			
			$arr_retrive=(get_option('xyz_twap_post_logs'));
			
			$update_opt_array[0]=isset($arr_retrive[0]) ? $arr_retrive[0] : '';
			$update_opt_array[1]=isset($arr_retrive[1]) ? $arr_retrive[1] : '';
			$update_opt_array[2]=isset($arr_retrive[2]) ? $arr_retrive[2] : '';
			$update_opt_array[3]=isset($arr_retrive[3]) ? $arr_retrive[3] : '';
			$update_opt_array[4]=isset($arr_retrive[4]) ? $arr_retrive[4] : '';
			$update_opt_array[5]=isset($arr_retrive[5]) ? $arr_retrive[5] : '';
			$update_opt_array[6]=isset($arr_retrive[6]) ? $arr_retrive[6] : '';
			$update_opt_array[7]=isset($arr_retrive[7]) ? $arr_retrive[7] : '';
			$update_opt_array[8]=isset($arr_retrive[8]) ? $arr_retrive[8] : '';
			$update_opt_array[9]=isset($arr_retrive[9]) ? $arr_retrive[9] : '';
			
			array_shift($update_opt_array);
			array_push($update_opt_array,$post_tw_options);
			update_option('xyz_twap_post_logs', $update_opt_array);
		}
	}
	
	$_POST=$_POST_CPY;

}

?>
