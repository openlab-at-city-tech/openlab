<div class="wrap watupro-wrap">
	<h1><?php _e('WatuPRO Social Sharing', 'watupro')?></h1>
	
	<p><?php _e('You can use the shortcode [watuproshare-buttons] to display Facebook and Twitter share buttons on the "Final screen" on your quiz.', 'watupro')?></b></p>
	<p><?php printf(__('The social media buttons are provided by <a href="%s" target="_blank">Arbenting</a>. Feel free to replace them with other icons.', 'watupro'), 'http://arbent.net/blog/social-media-circles-icon-set')?></p>
	
	<form method="post">
		<h2><?php _e('Facebook, Google Plus and LinkedIn Sharing', 'watupro')?></h2>
		
		<p><label><?php _e('Your Facebook App ID:', 'watupro')?></label> <input type="text" name="facebook_appid" value="<?php echo $appid?>"> <a href="https://developers.facebook.com/apps" target="_blank"><?php _e('Get one here', 'watupro')?></a></p>
		<p><?php _e('If you leave it empty, no Facebook share button will be generated.', 'watupro')?></p>
		
		
		<p><input type="checkbox" name="google_plus" value="1" <?php if(!empty($google_plus['enabled'])) echo 'checked'?>> <?php _e('Enable Google Plus share button', 'watupro')?></p>
		
		<p><input type="checkbox" name="linkedin_enabled" value="1" <?php if($linkedin_options['enabled']) echo 'checked'?>> <?php _e('Show LinkedIn button', 'watupro')?></p>
		
		<div id="linkedinOptions">
			<p><?php _e('Title:', 'watupro')?> <input type="text" name="linkedin_title" value="<?php echo htmlentities(stripslashes(@$linkedin_options['title']))?>" size="40">
			<p><?php _e('Text:', 'watupro')?> <textarea name="linkedin_msg" rows="4" cols="60"><?php echo stripslashes(@$linkedin_options['msg'])?></textarea>
			<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{url}}}, {{{grade-title}}} and {{{grade-description}}}.', 'watupro')?>
			<br>
			<b><?php _e('Known issue: Google Plus ignores the texts and shows only title. Facebook will strip HTML.', 'watupro');?></b></p>		
			<p><?php _e('If you leave title and text empty, grade title and grade description will be used respectively.', 'watupro')?></p>	
		</div>	
			
			<p><b><?php _e('IMPORTANT: Facebook, Google, and LinkedIn need to be able to access your site to retrieve the social sharing data. If the site is on localhost or behind a htaccess login box sharing will not work properly.', 'watupro')?></b></p>
		
		<h2><?php _e('Share by Email Options', 'watupro')?> </h2>
		
		<p><input type="checkbox" name="share_by_email" value="1" <?php if(!empty($share_by_email['enabled'])) echo 'checked'?> onclick="this.checked ? jQuery('#shareByEmail').show() : jQuery('#shareByEmail').hide();"> <?php _e('Enable sharing by email.', 'watupro')?></p>
		
		<div id="shareByEmail" style='display:<?php echo !empty($share_by_email['enabled']) ? 'block' : 'none'?>;'>
			<p><?php _e('Subject', 'watupro')?> <input type="text" name="email_subject" value="<?php echo htmlentities(stripslashes(@$share_by_email['subject']))?>" size="60"></p>
			<p><?php _e('Message', 'watupro')?> <textarea name="email_message" rows="3" cols="60"><?php echo stripslashes(@$share_by_email['message'])?></textarea>
			<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{grade-title}}}, {{{grade-description}}} and {{{url}}}. <b>HTML code is not supported.</b>', 'watupro')?></p>
		</div>				
		
		<h2><?php _e('Twitter Sharing Options:', 'watupro')?></h2>	
		
		<p><?php _e('If you leave "Tweet text" empty the tweet text will be extracted from the grade description. If it is empty, the grade title will be used.', 'watupro')?></p>
		
		<p><input type="checkbox" name="use_twitter" value="1" <?php if($twitter_options['use_twitter']) echo 'checked'?> onclick="jQuery('#twitterOptions').toggle();"> <?php _e('Show Tweet button', 'watupro')?></p>
		
		<div id="twitterOptions" style="display:<?php echo empty($twitter_options['use_twitter']) ? 'none' : 'block'?>">
			<p><input type="checkbox" name="show_count" value="1" <?php if(!empty($twitter_options['show_count'])) echo 'checked'?>> <?php _e('Show count', 'watupro')?></p>
			<p><?php _e('Via @', 'watupro')?> <input type="text" name="via" value="<?php echo @$twitter_options['via']?>"></p>
			<p><?php _e('Hashtag #', 'watupro')?><input type="text" name="hashtag" value="<?php echo @$twitter_options['hashtag']?>"></p>
			<p><?php _e('Tweet text (No more than 140 chars):', 'watupro')?> <textarea name="tweet" maxlength="140" rows="3" cols="40"><?php echo stripslashes(@$twitter_options['tweet'])?></textarea>
			<br> <?php _e('You can use the variables {{{quiz-name}}}, {{{grade-title}}} and {{{grade-description}}}.', 'watupro')?></p>			
		</div>	
		
		<h2><?php _e('Custom Button Images:', 'watupro');?></h2>
		
		<p><?php printf(__('You can replace the default button images with yours by entering the URLs below. If you have the images on your computer you can upload them to your <a href="%s" target="_blank">Media Library</a> and then get the URLs of the uploaded images from it.', 'watupro'), 'upload.php');?></p>
		
		<p><label><?php _e('Custom Twitter Button:', 'watupro');?></label> <input type="text" name="twitter_button" value="<?php echo empty($twitter_options['twitter_button']) ? '' : $twitter_options['twitter_button'];?>" size="30"></p>		
		<p><label><?php _e('Custom Email Button:', 'watupro');?></label> <input type="text" name="email_button" value="<?php echo empty($share_by_email['email_button']) ? '' : $share_by_email['email_button'];?>" size="30"></p>
		<p><label><?php _e('Custom Facebook Button:', 'watupro');?></label> <input type="text" name="facebook_button" value="<?php echo empty($linkedin_options['facebook_button']) ? '' : $linkedin_options['facebook_button'];?>" size="30"></p>
		<p><label><?php _e('Custom Linkedin Button:', 'watupro');?></label> <input type="text" name="linkedin_button" value="<?php echo empty($linkedin_options['linkedin_button']) ? '' : $linkedin_options['linkedin_button'];?>" size="30"></p>
		
		<h2><?php _e('Activating the Sharing Options:', 'watupro');?></h2>
			<p><?php _e('You can place the shortcode', 'watupro')?> <input type="text" readonly="readonly" value="[watuproshare-buttons]" onclick="this.select();"> <?php _e('in the quiz "Final page" box.', 'watupro')?></p>
		
		<p><input type="submit" value="<?php _e('Save All Settings', 'watupro')?>" class="button-primary"></p>
		<input type="hidden" name="ok" value="1">
	</form>
</div>