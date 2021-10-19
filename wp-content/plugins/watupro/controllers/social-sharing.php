<?php
class WatuPROSharing {
	static function options() {
		global $wpdb;
		
		if(!empty($_POST['ok'])) {
			update_option('watuproshare_facebook_appid', sanitize_text_field($_POST['facebook_appid']));	
			$use_twitter = empty($_POST['use_twitter']) ? 0 : 1;	
			$show_count = empty($_POST['show_count']) ? 0 : 1;		
			$large_button = empty($_POST['large_button']) ? 0 : 1;
			$share_by_email_chk = empty($_POST['share_by_email']) ? 0 : 1;
			$gplus_enabled = empty($_POST['google_plus']) ? 0 : 1;
			$linkedin_enabled = empty($_POST['linkedin_enabled']) ? 0 : 1;
			
			$twitter_options = array("use_twitter" => $use_twitter, "show_count" => $show_count,
			 "via"=>sanitize_text_field($_POST['via']), "hashtag" => sanitize_text_field($_POST['hashtag']), 'large_button' => $large_button,
			 "tweet"=>watupro_strip_tags($_POST['tweet']), "twitter_button" => esc_url_raw($_POST['twitter_button']));
			update_option('watuproshare_twitter', $twitter_options);
			
			$share_by_email = array("enabled" => $share_by_email_chk, 'subject' => sanitize_text_field($_POST['email_subject']),
				'message' => strip_tags($_POST['email_message']), 'email_button' => esc_url_raw($_POST['email_button']));
			update_option('watuproshare_email', $share_by_email);	
			
			$google_plus = array("enabled" => $gplus_enabled);
			update_option('watuproshare_gplus', $google_plus);
			
			$linkedin_options = array("enabled" => $linkedin_enabled,  "msg" => watupro_strip_tags($_POST['linkedin_msg']), 
				'title' => sanitize_text_field($_POST['linkedin_title']), "linkedin_button" => esc_url_raw($_POST['linkedin_button']), 
				'facebook_button' => esc_url_raw($_POST['facebook_button']));
			update_option('watuproshare_linkedin', $linkedin_options);
		}
		
		$appid = get_option('watuproshare_facebook_appid');
		$twitter_options = get_option('watuproshare_twitter');
		$share_by_email = get_option('watuproshare_email');
		$google_plus = get_option('watuproshare_gplus');
		$linkedin_options = get_option('watuproshare_linkedin');
		include(WATUPRO_PATH.'/views/sharing-options.html.php');
	}	
	
	// display the social sharing buttons
	static function display() {
		global $wpdb;
		$taking_id = intval(@$GLOBALS['watupro_taking_id']);	
		ob_start();
		// https://developers.facebook.com/docs/sharing/reference/feed-dialog
		$appid = get_option('watuproshare_facebook_appid');
		
		// get the grade title and description
		$grade_id = $wpdb->get_var($wpdb->prepare("SELECT grade_id FROM ".WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $taking_id));
		if(empty($grade_id)) $grade = (object)array("gtitle"=>'None', 'gdescription'=>'None');
		else $grade = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_GRADES." WHERE ID=%d", $grade_id));
		
		// select quiz name
		$quiz_name = $wpdb->get_var($wpdb->prepare("SELECT tE.name FROM ".WATUPRO_EXAMS." tE
			JOIN ".WATUPRO_TAKEN_EXAMS." tT ON tE.ID = tT.exam_id 
			WHERE tT.ID = %d", $taking_id));
		
		// any picture?
		$picture_str = '';
		if(strstr($grade->gdescription, '<img')) {
			// find all pictures in the grade descrption
			$html = stripslashes($grade->gdescription);
			$dom = new DOMDocument;
			$dom->loadHTML($html);
			$images = array();
			foreach ($dom->getElementsByTagName('img') as $image) {
			    $src =  $image->getAttribute('src');	
			    $class = $image->getAttribute('class');
			    $images[] = array('src'=>$src, 'class'=>$class);
			} // end foreach DOM element
			
			if(sizeof($images)) {
				$target_image = $images[0]['src'];
				
				// but check if we have any that are marked with the class
				foreach($images as $image) {
					if(strstr($image['class'], 'watupro-share')) {
						$target_image = $image['src'];
						break;
					}
				}
				
				$picture_str = "&picture=".urlencode($target_image);
			}
		}   // end searching for image
		
		$twitter_options = get_option('watuproshare_twitter');
		
		// prepare tweet text
		if(!empty($twitter_options['use_twitter'])) {
			$tweet = stripslashes($twitter_options['tweet']);
			
			if(empty($tweet)) {
				$tweet = stripslashes($grade->gdescription);
				if(empty($tweet)) $tweet = stripslashes($grade->gtitle);
			}
			else {
				$tweet = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $tweet);
				$tweet = str_replace('{{{grade-description}}}', stripslashes($grade->gdescription), $tweet);
				$tweet = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $tweet);
			}
			
			$tweet = substr($tweet, 0, 140);
		}
		
		// share by email
		$email_options = get_option('watuproshare_email');
		if(!empty($email_options['enabled'])) {
			$subject = stripslashes($email_options['subject']); 
			$subject = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $subject);
			$subject = str_replace('{{{grade-description}}}', stripslashes($grade->gdescription), $subject);
			$subject = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $subject);
			$subject = str_replace('{{{url}}}', get_permalink(@$_POST['post_id']), $subject);
			$subject = htmlentities($subject);
			
			$message = stripslashes($email_options['message']); 
			$message = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $message);
			$message = str_replace('{{{grade-description}}}', stripslashes($grade->gdescription), $message);
			$message = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $message);
			$message = str_replace('{{{url}}}', get_permalink(@$_POST['post_id']), $message);
			//$message = str_replace(array("\n", "\r"), "%0A", $message);
			$message = rawurlencode($message);
		}
		
		// google plus
		$gplus = get_option('watuproshare_gplus');
		
		// linkedin
		$linkedin = get_option('watuproshare_linkedin');
		
		// keep linkedin vars always because they are also used in Facebook
		$linkedin_msg = stripslashes($linkedin['msg']);
		$linkedin_title = stripslashes($linkedin['title']);		
				
		// title and description set up?
		if(!empty($linkedin_title)) {
			$linkedin_title = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $linkedin_title);				
			$linkedin_title = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $linkedin_title);
		}
		if(!empty($linkedin_msg)) {
			$linkedin_msg = str_replace('{{{grade-title}}}', stripslashes($grade->gtitle), $linkedin_msg);			
			$linkedin_msg = str_replace('{{{grade-description}}}', stripslashes($grade->gdescription), $linkedin_msg);	
			$linkedin_msg = str_replace('{{{quiz-name}}}', stripslashes($quiz_name), $linkedin_msg);
			$linkedin_msg = str_replace('{{{url}}}', get_permalink($_POST['post_id']), $linkedin_msg);
		}
		
		// if not, default to grade title and desc
		if(empty($linkedin_title)) $linkedin_title = $grade->gtitle;
		if(empty($linkedin_msg)) $linkedin_msg = $grade->gdescription;
		
		$linkedin_title = stripslashes($linkedin_title);
		$linkedin_title  = str_replace('"', '&quot;', $linkedin_title);
		$linkedin_title  = str_replace("'", '’', $linkedin_title);

		$linkedin_msg = stripslashes($linkedin_msg);
		$linkedin_msg  = str_replace('"', '&quot;', $linkedin_msg);
		$linkedin_msg  = str_replace("'", '’', $linkedin_msg);
		
		$shareable_url = site_url('?watupro_sssnippet=1&amp;tid='.$taking_id.'&amp;return_to='.$_POST['post_id']);
		
		// buttons
		$facebook_button = empty($linkedin['facebook_button']) ? WATUPRO_URL.'/img/share/facebook.png' : $linkedin['facebook_button'];
		$email_button = empty($email_options['email_button']) ? WATUPRO_URL.'/img/share/mail.png' : $email_options['email_button'];
		$twitter_button = empty($twitter_options['twitter_button']) ? 'Tweet' : '<img src="'.$twitter_options['twitter_button'].'" alt="Twitter share button">';
		?>	
		<div class="watupro-social-sharing"><?php if(!empty($appid)):?><a href="#" title="<?php _e('Share your results on Facebook', 'watupro');?>" onclick="WatuPRO.FBShare('<?php echo get_permalink($_POST['post_id'])?>',encodeURIComponent('<?php echo addslashes($linkedin_title)?>'), encodeURIComponent('<?php echo strip_tags(str_replace(array("\r", "\n"), " ", $linkedin_msg));?>'), '<?php echo $picture_str?>');return false;"><img src="<?php echo $facebook_button;?>"></a>&nbsp;
		<?php endif; // end if Facebook
		?><?php if(!empty($gplus['enabled'])):?><!-- Place this tag in your head or just before your close body tag. -->
<script src="https://apis.google.com/js/platform.js" async defer></script>
<div class="g-plus" data-action="share" data-annotation="none" data-href="<?php echo $shareable_url;?>"></div>
<?php endif; // end if G+
	   if(!empty($linkedin['enabled'])):
	   	if(empty($linkedin['linkedin_button'])):?>
	   	<script src="//platform.linkedin.com/in.js" type="text/javascript">
 			 lang: en_US
			</script>
		<script type="IN/Share" data-url="<?php echo $shareable_url;?>"></script>
		<?php else: // linkedin with custom button?>
		<a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo $shareable_url;?>&title=<?php echo addslashes($linkedin_title)?>'&summary=<?php echo strip_tags(str_replace(array("\r", "\n"), " ", $linkedin_msg));?>" target="_new"> <img src="<?php echo $linkedin['linkedin_button']?>" alt="linkedin share button" title="Share on Linked In" /> </a>	 
	   <?php endif;
	   endif; // endif linkedin
	   if(!empty($email_options['enabled'])):?><a href="mailto:?subject=<?php echo $subject?>&amp;body=<?php echo $message?>" title="<?php _e('Share by Email', 'watupro')?>"><img src="<?php echo $email_button?>"></a>&nbsp;<?php endif; // end if email
	   if(!empty($twitter_options['use_twitter'])):
	   	if(empty($twitter_options['twitter_button'])):?>
			 <a href="https://twitter.com/share" class="twitter-share-button watupro-twitter-share-button" data-url="<?php echo get_permalink($_POST['post_id'])?>" data-via="<?php echo $twitter_options['via']?>" data-hashtags="<?php echo $twitter_options['hashtag']?>" data-text="<?php echo htmlentities($tweet)?>" <?php if(empty($twitter_options['show_count'])):?>data-count="none"<?php endif;?>><?php echo $twitter_button?></a>
	<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
			<?php else: // custom twitter button ?>
			<a href="http://twitter.com/share?url=<?php echo get_permalink($_POST['post_id'])?>;text=<?php echo urlencode($tweet)?>;size=l&amp;count=none&hashtags=<?php echo $twitter_options['hashtag']?>&via=<?php echo $twitter_options['via']?>&url=<?php echo get_permalink($_POST['post_id'])?>" target="_blank">			   
			        <?php echo $twitter_button?>		        
			</a>
			<?php endif; // end twitter?>
	   <?php endif;?></div>
		<?php 
		$content = ob_get_clean();
		return $content;
	}
}