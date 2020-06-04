<?php
	/* unserialize all saved option for  section 5 options */
	$icons 		= ($option1['sfsi_custom_files']) ? unserialize($option1['sfsi_custom_files']) : array() ;
	$option3	= unserialize(get_option('sfsi_section3_options',false));
	$option5	= unserialize(get_option('sfsi_section5_options',false));

	$custom_icons_order = unserialize($option5['sfsi_CustomIcons_order']);
	if(!isset($option5['sfsi_telegramIcon_order'])){                     
        $option5['sfsi_telegramIcon_order']    = '11';
    }
    if(!isset($option5['sfsi_vkIcon_order'])){                     
        $option5['sfsi_vkIcon_order']    = '12';
    }
    if(!isset($option5['sfsi_okIcon_order'])){                     
        $option5['sfsi_okIcon_order']    = '13';
    }
    if(!isset($option5['sfsi_weiboIcon_order'])){                     
        $option5['sfsi_weiboIcon_order']    = '14';
    }
    if(!isset($option5['sfsi_wechatIcon_order'])){                     
        $option5['sfsi_wechatIcon_order']    = '15';
    }
	$icons_order = array(
		$option5['sfsi_rssIcon_order']		=> 'rss',
		$option5['sfsi_emailIcon_order']	=> 'email',
		$option5['sfsi_facebookIcon_order']	=> 'facebook',
		$option5['sfsi_twitterIcon_order']	=> 'twitter',
		$option5['sfsi_youtubeIcon_order']	=> 'youtube',
		$option5['sfsi_pinterestIcon_order']=> 'pinterest',
		$option5['sfsi_linkedinIcon_order']	=> 'linkedin',
		$option5['sfsi_instagramIcon_order']=> 'instagram',
		$option5['sfsi_telegramIcon_order']=> 'telegram',
		$option5['sfsi_vkIcon_order']=> 'vk',
		$option5['sfsi_okIcon_order']=> 'ok',
		$option5['sfsi_weiboIcon_order']=> 'weibo',
		$option5['sfsi_wechatIcon_order']=> 'wechat',

	) ;
	
	/*
	 * Sanitize, escape and validate values
	 */
	$option5['sfsi_icons_size'] 				= 	(isset($option5['sfsi_icons_size']))
														? intval($option5['sfsi_icons_size'])
														: '';
	$option5['sfsi_icons_spacing'] 				= 	(isset($option5['sfsi_icons_spacing']))
														? intval($option5['sfsi_icons_spacing'])
														: '';
	$option5['sfsi_icons_Alignment'] 			= 	(isset($option5['sfsi_icons_Alignment']))
														? sanitize_text_field($option5['sfsi_icons_Alignment'])
														: '';
	$option5['sfsi_icons_Alignment_via_widget'] = 	(isset($option5['sfsi_icons_Alignment_via_widget']))
														? sanitize_text_field($option5['sfsi_icons_Alignment_via_widget'])
														: '';	
	$option5['sfsi_icons_Alignment_via_shortcode'] 	= 	(isset($option5['sfsi_icons_Alignment_via_shortcode']))
														? sanitize_text_field($option5['sfsi_icons_Alignment_via_shortcode'])
														: '';
	$option5['sfsi_icons_perRow'] 				= 	(isset($option5['sfsi_icons_perRow']))
														? intval($option5['sfsi_icons_perRow'])
														: '';
	$option5['sfsi_icons_ClickPageOpen']		= 	(isset($option5['sfsi_icons_ClickPageOpen']))
														? sanitize_text_field($option5['sfsi_icons_ClickPageOpen'])
														:'';	
	$option5['sfsi_icons_stick'] 				= 	(isset($option5['sfsi_icons_stick']))
														? sanitize_text_field($option5['sfsi_icons_stick'])
														: '';
	$option5['sfsi_rss_MouseOverText'] 			= 	(isset($option5['sfsi_rss_MouseOverText']))
														? sanitize_text_field($option5['sfsi_rss_MouseOverText'])
														: '';
	$option5['sfsi_email_MouseOverText'] 		= 	(isset($option5['sfsi_email_MouseOverText']))
														? sanitize_text_field($option5['sfsi_email_MouseOverText'])
														:'';
	$option5['sfsi_twitter_MouseOverText'] 		= 	(isset($option5['sfsi_twitter_MouseOverText']))
														? sanitize_text_field($option5['sfsi_twitter_MouseOverText'])
														: '';
	$option5['sfsi_facebook_MouseOverText'] 	= 	(isset($option5['sfsi_facebook_MouseOverText']))
														? sanitize_text_field($option5['sfsi_facebook_MouseOverText'])
														: '';
	$option5['sfsi_linkedIn_MouseOverText'] 	= 	(isset($option5['sfsi_linkedIn_MouseOverText']))
														? sanitize_text_field($option5['sfsi_linkedIn_MouseOverText'])
														: '';
	$option5['sfsi_pinterest_MouseOverText']	= 	(isset($option5['sfsi_pinterest_MouseOverText']))
														? sanitize_text_field($option5['sfsi_pinterest_MouseOverText'])
														: '';
	$option5['sfsi_youtube_MouseOverText'] 		= 	(isset($option5['sfsi_youtube_MouseOverText']))
														? sanitize_text_field($option5['sfsi_youtube_MouseOverText'])
														: '';
	$option5['sfsi_instagram_MouseOverText']	= 	(isset($option5['sfsi_instagram_MouseOverText']))
														? sanitize_text_field($option5['sfsi_instagram_MouseOverText'])
														: '';
	$option5['sfsi_telegram_MouseOverText']		= 	(isset($option5['sfsi_telegram_MouseOverText']))
														? sanitize_text_field($option5['sfsi_telegram_MouseOverText'])
														: '';
	$option5['sfsi_vk_MouseOverText']			= 	(isset($option5['sfsi_vk_MouseOverText']))
														? sanitize_text_field($option5['sfsi_vk_MouseOverText'])
														: '';
	$option5['sfsi_ok_MouseOverText']			= 	(isset($option5['sfsi_ok_MouseOverText']))
														? sanitize_text_field($option5['sfsi_ok_MouseOverText'])
														: '';
	$option5['sfsi_weibo_MouseOverText']		= 	(isset($option5['sfsi_weibo_MouseOverText']))
														? sanitize_text_field($option5['sfsi_weibo_MouseOverText'])
														: '';
	$option5['sfsi_wechat_MouseOverText']		= 	(isset($option5['sfsi_wechat_MouseOverText']))
														? sanitize_text_field($option5['sfsi_wechat_MouseOverText'])
														: '';
	$sfsi_icons_suppress_errors 				=   (isset($option5['sfsi_icons_suppress_errors']))
														? sanitize_text_field($option5['sfsi_icons_suppress_errors'])
														: 'no';
	if(is_array($custom_icons_order) ) 
	{
		foreach($custom_icons_order as $data)
		{
			$icons_order[$data['order']] = $data;
		}
	}
	ksort($icons_order);
?>

<!-- Section 5 "Any other wishes for your main icons?" main div Start -->
<div class="tab5">
	<h4>Order of your icons</h4>
    <!-- icon drag drop  section start here -->	
    <ul class="share_icon_order" >
        <?php 
	 	$ctn = 0;
	 	foreach($icons_order as $index=>$icn) :

		  switch ($icn) : 
          case 'rss' :?>
            	 <li class="rss_section" data-index="<?php echo $index; ?>" id="sfsi_rssIcon_order">
                	<a href="#" title="RSS"><img src="<?php echo SFSI_PLUGURL; ?>images/rss.png" alt="RSS" /></a>
                 </li>
          <?php break; ?><?php case 'email' :?>
          		<li class="email_section " data-index="<?php echo $index; ?>" id="sfsi_emailIcon_order">
                	<a href="#" title="Email"><img src="<?php echo SFSI_PLUGURL; ?>images/<?php echo $email_image; ?>" alt="Email" class="icon_img" /></a>
                </li>
          <?php break; ?><?php case 'facebook' :?>
          		<li class="facebook_section " data-index="<?php echo $index; ?>" id="sfsi_facebookIcon_order">
                	<a href="#" title="Facebook"><img src="<?php echo SFSI_PLUGURL; ?>images/facebook.png" alt="Facebook" /></a>
                </li>
          <?php break; ?><?php case 'twitter' :?>
          		<li class="twitter_section " data-index="<?php echo $index; ?>" id="sfsi_twitterIcon_order">
                	<a href="#" title="Twitter" ><img src="<?php echo SFSI_PLUGURL; ?>images/twitter.png" alt="Twitter" /></a>
                </li>
          <?php break; ?><?php case 'youtube' :?>
          		<li class="youtube_section " data-index="<?php echo $index; ?>" id="sfsi_youtubeIcon_order">
                	<a href="#" title="YouTube" ><img src="<?php echo SFSI_PLUGURL; ?>images/youtube.png" alt="YouTube" /></a>
                </li>
          <?php break; ?><?php case 'pinterest' :?>
          		<li class="pinterest_section " data-index="<?php echo $index; ?>" id="sfsi_pinterestIcon_order">
                	<a href="#" title="Pinterest" ><img src="<?php echo SFSI_PLUGURL; ?>images/pinterest.png" alt="Pinterest" /></a>
                </li>
          <?php break; ?><?php case 'linkedin' :?>
          		<li class="linkedin_section " data-index="<?php echo $index; ?>" id="sfsi_linkedinIcon_order">
                	<a href="#" title="Linked In" ><img src="<?php echo SFSI_PLUGURL; ?>images/linked_in.png" alt="Linked In" /></a>
                </li>
          <?php break; ?><?php case 'instagram' :?>
          		<li class="instagram_section " data-index="<?php echo $index; ?>" id="sfsi_instagramIcon_order">
                	<a href="#" title="Instagram" ><img src="<?php echo SFSI_PLUGURL; ?>images/instagram.png" alt="Instagram" /></a>
                </li>
		  <?php break; ?><?php case 'telegram' :?>
          		<li class="telegram_section " data-index="<?php echo $index; ?>" id="sfsi_telegramIcon_order">
                	<a href="#" title="telegram" ><img src="<?php echo SFSI_PLUGURL; ?>images/icons_theme/default/default_telegram.png" height="54px;" alt="telegram" /></a>
                </li>
		  <?php break; ?><?php case 'vk' :?>
          		<li class="vk_section " data-index="<?php echo $index; ?>" id="sfsi_vkIcon_order">
                	<a href="#" title="vk" ><img src="<?php echo SFSI_PLUGURL; ?>images/icons_theme/default/default_vk.png" height="54px;" alt="vk" /></a>
                </li>
		  <?php break; ?><?php case 'ok' :?>
          		<li class="ok_section " data-index="<?php echo $index; ?>" id="sfsi_okIcon_order">
                	<a href="#" title="ok" ><img src="<?php echo SFSI_PLUGURL; ?>images/icons_theme/default/default_ok.png" height="54px;" alt="ok" /></a>
                </li>
		  <?php break; ?><?php case 'weibo' :?>
          		<li class="weibo_section " data-index="<?php echo $index; ?>" id="sfsi_weiboIcon_order">
                	<a href="#" title="weibo" ><img src="<?php echo SFSI_PLUGURL; ?>images/icons_theme/default/default_weibo.png" height="54px;" alt="weibo" /></a>
                </li>
		  <?php break; ?><?php case 'wechat' :?>
          		<li class="wechat_section " data-index="<?php echo $index; ?>" id="sfsi_wechatIcon_order">
                	<a href="#" title="wechat" ><img src="<?php echo SFSI_PLUGURL; ?>images/icons_theme/default/default_wechat.png" height="54px;" alt="wechat" /></a>
                </li>
		  <?php break; ?><?php default   :?><?php if(isset($icons[$icn['ele']]) && !empty($icons[$icn['ele']]) && filter_var($icons[$icn['ele']], FILTER_VALIDATE_URL) ): ?>
          		<li class="custom_iconOrder sfsiICON_<?php echo $icn['ele']; ?>" data-index="<?php echo $index; ?>" element-id="<?php echo $icn['ele']; ?>" >
                	<a href="#" title="Custom Icon" ><img src="<?php echo $icons[$icn['ele']]; ?>" alt="Linked In" class="sfcm" /></a>
                </li> 
                <?php endif; ?><?php break; ?><?php  endswitch; ?><?php endforeach; ?> 
     
    </ul> <!-- END icon drag drop section start here -->
    
        <span class="drag_drp">(Drag &amp; Drop)</span>
     <!-- icon's size and spacing section start here -->	
    <div class="row">
	<h4>Size &amp; spacing of your icons</h4>
	<div class="icons_size"><span>Size:</span><input name="sfsi_icons_size" value="<?php echo ($option5['sfsi_icons_size']!='') ?  $option5['sfsi_icons_size'] : '' ;?>" type="text" /><ins>pixels wide &amp; tall</ins> <span class="last">Spacing between icons:</span><input name="sfsi_icons_spacing" type="text" value="<?php echo ($option5['sfsi_icons_spacing']!='') ?  $option5['sfsi_icons_spacing'] : '' ;?>" /><ins>Pixels</ins></div>

    <div class="icons_prem_disc">
        <p class="sfsi_prem_plu_desc"><b>New: </b>The Premium Plugin also allows you to define the vertical distance between the icons (and set this differently for mobile vs. desktop): <a  class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)"  style="cursor:pointer;border-bottom: 1px solid #12a252;color: #12a252 !important;font-weight:bold" class="sfisi_font_bold" target="_blank">Go premium now.<a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_spacings&utm_medium=banner" class="sfsi_font_inherit" style="color: #12a252 !important" target="_blank"> or learn more.</a>
    </div>
    
    </div>
    
    <div class="row">
	<h4>Alignments</h4>
	<div class="icons_size" style="width: max-content;display:flow-root">
		<span>Icons per row:</span>
		<input name="sfsi_icons_perRow" type="text" value="<?php echo ($option5['sfsi_icons_perRow']!='') ?  $option5['sfsi_icons_perRow'] : '' ;?>" />
		<ins class="leave_empty" style="margin-bottom: 34px;">Leave empty if you don't want to <br /> define this</ins>
	</div>
	<div class="icons_size" style="width: max-content;">
		<div style="width: 232px;float: left;position: relative;">
			<span style="line-height: 26px;margin-bottom: 22px;">Alignment of icons within a widget:</span>
			
		</div>
		<div class="field">
			<select name="sfsi_icons_Alignment_via_widget" id="sfsi_icons_Alignment_via_widget" class="styled">
				<option value="center" <?php echo ($option5['sfsi_icons_Alignment_via_widget']=='center') ?  'selected="selected"' : '' ;?>>Centered</option>
				<option value="right" <?php echo ($option5['sfsi_icons_Alignment_via_widget']=='right') ?  'selected="selected"' : '' ;?>>Right</option>
				<option value="left" <?php echo ($option5['sfsi_icons_Alignment_via_widget']=='left') ?  'selected="selected"' : '' ;?>>Left</option>
			</select>
		</div>
	</div>
	<div class="icons_size" style="width: max-content;">
		<div style="width: 232px;float: left;position: relative;">
			<span style="line-height: 26px;margin-bottom: 22px;">Alignment of icons if placed via shortcode:</span>
		</div>
		<div class="field">
			<select name="sfsi_icons_Alignment_via_shortcode" id="sfsi_icons_Alignment_via_shortcode" class="styled">
				<option value="center" <?php echo ($option5['sfsi_icons_Alignment_via_shortcode']=='center') ?  'selected="selected"' : '' ;?>>Centered</option>
				<option value="right" <?php echo ($option5['sfsi_icons_Alignment_via_shortcode']=='right') ?  'selected="selected"' : '' ;?>>Right</option>
				<option value="left" <?php echo ($option5['sfsi_icons_Alignment_via_shortcode']=='left') ?  'selected="selected"' : '' ;?>>Left</option>
			</select>
		</div>
	</div>
	<div class="icons_size" style="width: max-content;">
		<div style="width: 232px;float: left;position: relative;">
			<span style="line-height: 26px;margin-bottom: 10px;">Alignment of icons In the second row:</span>
			<ins class="sfsi_icons_other_allign" style="bottom: -22px;left: 0;width: 200px;color: rgb(128,136,145);">
				(with respect to icons in the first row; only relevant if your icons show in two or more rows)
			</ins>
		</div>
		<div class="field">
			<select name="sfsi_icons_Alignment" id="sfsi_icons_Alignment" class="styled">
				<option value="center" <?php echo ($option5['sfsi_icons_Alignment']=='center') ?  'selected="selected"' : '' ;?>>Centered</option>
				<option value="right" <?php echo ($option5['sfsi_icons_Alignment']=='right') ?  'selected="selected"' : '' ;?>>Right</option>
				<option value="left" <?php echo ($option5['sfsi_icons_Alignment']=='left') ?  'selected="selected"' : '' ;?>>Left</option>
			</select>
		</div>
	</div>

    <div class= "sfsi_new_prmium_follw" style="margin-top: 38px;">
		<p><b>New: </b>In the Premium Plugin you can show the icons vertically and give them different alignment options for icons placed on mobile <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=more_alignment_options&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> See all features.</a></p>
	</div>

    </div>
    
    <div class="row new_wind">
		<h4>New window</h4>
		<div class="row_onl"><p>If a user clicks on your icons, do you want to open the page in a new window?
		</p>
			<ul class="enough_waffling">
		    	<li>
		    		<input name="sfsi_icons_ClickPageOpen" <?php echo ($option5['sfsi_icons_ClickPageOpen']=='yes') ?  'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />
		    		<label>Yes</label>
		    	</li>
				<li>
					<input name="sfsi_icons_ClickPageOpen" <?php echo ($option5['sfsi_icons_ClickPageOpen']=='no') ?  'checked="true"' : '' ;?> type="radio" value="no" class="styled" />
					<label>No</label>
				</li>
	      	</ul>
      	</div>
    </div>

   
     <!-- icon's floating and stick section start here -->	
    <div class="row sticking">
	
	<h4>Sticky icons</h4>
	
    <div class="clear float_options" <?php if($option5['sfsi_icons_stick']=='yes') :?> style="display:none" <?php endif;?>>

  
  	</div> 
  
  <div class="space">
    
    <p class="list">Make icons stick?</p>

    <ul class="enough_waffling">
  		
  		<li>
  			<input name="sfsi_icons_stick" <?php echo ($option5['sfsi_icons_stick']=='yes') ?  'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />
  			<label>Yes</label>
  		</li>

		<li>
			<input name="sfsi_icons_stick" <?php echo ($option5['sfsi_icons_stick']=='no') ?  'checked="true"' : '' ;?>  type="radio" value="no" class="styled" />
			<label>No</label>
		</li>

  	</ul>

	<p>
		If you select «Yes» here, then the icons which you placed via <span style="text-decoration: underline;"><b>widget</b></span> or <span style="text-decoration: underline;"><b>shortcode</b></span> will still be visible on the screen as user scrolls down your page, i.e. they will stick at the top.</p> 

	<p>
		This is not to be confused with making the icons permanently placed in the same position, which is possible in the <a target="_blank" href="https://www.ultimatelysocial.com/usm-premium"><b>Premium Plugin</b></a>.
	</p> 

  </div>
  

</div><!-- END icon's floating and stick section -->

<!--*************  Sharing texts & pictures section STARTS *****************************-->

<div class="row sfsi_custom_social_data_setting" id="custom_social_data_setting">

		<h4>Sharing texts & pictures?</h4>
		<p>On the pages where you edit your posts/pages, you’ll see a (new) section where you can define which pictures & text should be shared. This extra section is displayed on the following:</p>		

			<?php 
				$checkedS   = (isset($option5['sfsi_custom_social_hide']) && $option5['sfsi_custom_social_hide']=="yes") ? 'checked="checked"': '';	
				$checked    = (isset($option5['sfsi_custom_social_hide']) && $option5['sfsi_custom_social_hide']=="yes") ? '': 'checked="checked"';
				$checkedVal = (isset($option5['sfsi_custom_social_hide'])) ? $option5['sfsi_custom_social_hide']: 'no';				
			?>
		<div class="social_data_post_types">
                <ul class="socialPostTypesUl">
                	<li>
						<div class="radio_section tb_4_ck">
							<input type="checkbox" <?php echo $checked; ?> value="page" class="styled"  />
							<label class="cstmdsplsub">Page</label>
						</div>
					</li>
                	<li>
						<div class="radio_section tb_4_ck">
							<input type="checkbox" <?php echo $checked; ?> value="post" class="styled"  />
							<label class="cstmdsplsub">Post</label>
						</div>
					</li>						
                </ul>

                <ul class="sfsi_show_hide_section">
               		<li>
						<div class="radio_section tb_4_ck">
							<input name="sfsi_custom_social_hide" type="checkbox" <?php echo $checkedS; ?> value="<?php echo $checkedVal; ?>" class="styled"  />
							<label class="cstmdsplsub">Hide section for all</label>
						</div>
					</li>
                </ul>
 		</div>

		<div class="sfsi_new_prmium_follw sfsi_social_sharing" style="margin-bottom: 15px;">
			<p>Note: This feature is currently only available in the Premium Plugin. <a style="cursor:pointer" class="pop-up" data-id="sfsi_quickpay-overlay" onclick="sfsi_open_quick_checkout(event)"  class="sfisi_font_bold" target="_blank">Go premium now</a><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=define_pic_and_text&utm_medium=banner" class="sfsi_font_inherit" target="_blank"> or learn more.</a>
			</p>
		</div> 		
</div>

<!--********************  Sharing texts & pictures section CLOSES ************************************************-->

 <!-- mouse over text section start here -->
 <div class="row mouse_txt">
    <h4>Mouseover text</h4>
	<p>
    	If you’ve given your icon only one function (i.e. no pop-up where user can perform different actions) then you can define 
here what text will be displayed if a user moves his mouse over the icon:
	</p>
	<div class="space">
		<div class="clear"></div>
		<div class="mouseover_field rss_section">
			<label>RSS:</label><input name="sfsi_rss_MouseOverText" value="<?php echo ($option5['sfsi_rss_MouseOverText']!='') ?  $option5['sfsi_rss_MouseOverText'] : '' ;?>" type="text" />
		</div>
		<div class="mouseover_field email_section">
			<label>Email:</label><input name="sfsi_email_MouseOverText" value="<?php echo ($option5['sfsi_email_MouseOverText']!='') ?  $option5['sfsi_email_MouseOverText'] : '' ;?>" type="text" />
		</div>
		
		<div class="clear">
		<div class="mouseover_field twitter_section">
			<label>Twitter:</label>
			<input name="sfsi_twitter_MouseOverText" value="<?php echo ($option5['sfsi_twitter_MouseOverText']!='') ?  $option5['sfsi_twitter_MouseOverText'] : '' ;?>" type="text" />
		</div>
		<div class="mouseover_field facebook_section">
			<label>Facebook:</label>
			<input name="sfsi_facebook_MouseOverText" value="<?php echo ($option5['sfsi_facebook_MouseOverText']!='') ?  $option5['sfsi_facebook_MouseOverText'] : '' ;?>" type="text" />
		</div>
		</div>
		<div class="clear">
		<div class="mouseover_field linkedin_section">
			<label>LinkedIn:</label>
			<input name="sfsi_linkedIn_MouseOverText" value="<?php echo ($option5['sfsi_linkedIn_MouseOverText']!='') ?  $option5['sfsi_linkedIn_MouseOverText'] : '' ;?>"  type="text" />
		</div>
		</div>
		<div class="clear">
		<div class="mouseover_field pinterest_section">
			<label>Pinterest:</label>
			<input name="sfsi_pinterest_MouseOverText" value="<?php echo ($option5['sfsi_pinterest_MouseOverText']!='') ?  $option5['sfsi_pinterest_MouseOverText'] : '' ;?>" type="text" />
		</div>
		<div class="mouseover_field youtube_section">
			<label>Youtube:</label>
			<input name="sfsi_youtube_MouseOverText" value="<?php echo ($option5['sfsi_youtube_MouseOverText']!='') ?  $option5['sfsi_youtube_MouseOverText'] : '' ;?>" type="text" />
		</div>
		</div>
		<div class="clear">
		    <div class="mouseover_field instagram_section">
				<label>Instagram:</label>
				<input name="sfsi_instagram_MouseOverText" value="<?php echo ($option5['sfsi_instagram_MouseOverText']!='') ?  $option5['sfsi_instagram_MouseOverText'] : '' ;?>" type="text" />
			</div>
			<div class="mouseover_field telegram_section">
				<label>Telegram:</label>
				<input name="sfsi_telegram_MouseOverText" value="<?php echo ($option5['sfsi_telegram_MouseOverText']!='') ?  $option5['sfsi_telegram_MouseOverText'] : '' ;?>" type="text" />
		    </div>
		</div>
		<div class="clear">
		    <div class="mouseover_field vk_section">
				<label>VK:</label>
				<input name="sfsi_vk_MouseOverText" value="<?php echo ($option5['sfsi_vk_MouseOverText']!='') ?  $option5['sfsi_vk_MouseOverText'] : '' ;?>" type="text" />
			</div>
			<div class="mouseover_field ok_section">
				<label>Ok:</label>
				<input name="sfsi_ok_MouseOverText" value="<?php echo ($option5['sfsi_ok_MouseOverText']!='') ?  $option5['sfsi_ok_MouseOverText'] : '' ;?>" type="text" />
		    </div>
		</div>
		<div class="clear">
		    <div class="mouseover_field weibo_section">
				<label>Weibo:</label>
				<input name="sfsi_weibo_MouseOverText" value="<?php echo ($option5['sfsi_weibo_MouseOverText']!='') ?  $option5['sfsi_weibo_MouseOverText'] : '' ;?>" type="text" />
			</div>
			<div class="mouseover_field wechat_section">
				<label>WeChat:</label>
				<input name="sfsi_wechat_MouseOverText" value="<?php echo ($option5['sfsi_wechat_MouseOverText']!='') ?  $option5['sfsi_wechat_MouseOverText'] : '' ;?>" type="text" />
		    </div>
		</div>
        <div class="clear"> </div>  
		<div class="custom_m">
        	<?php 
                $sfsiMouseOverTexts =  unserialize($option5['sfsi_custom_MouseOverTexts']);
                $count = 1; for($i=$first_key; $i <= $endkey; $i++) :
            ?><?php if(!empty( $icons[$i])) : ?>
                
                <div class="mouseover_field custom_section sfsiICON_<?php echo $i; ?>">
                    <label>Custom <?php echo $count; ?>:</label>
                    <input name="sfsi_custom_MouseOverTexts[]" value="<?php echo (isset($sfsiMouseOverTexts[$i]) && $sfsiMouseOverTexts[$i]!='') ?sanitize_text_field($sfsiMouseOverTexts[$i]) : '' ;?>" type="text" file-id="<?php echo $i; ?>" />
                </div>
                  
                <?php if($count%2==0): ?>
                
                <div class="clear"> </div>  
            <?php endif; ?><?php $count++; endif; endfor; ?>
		</div>
		
	</div>

	</div>
	<!-- END mouse over text section -->

    <div class="row new_wind">
		<h4>Error reporting</h4>
		<div class="row_onl"><p>Suppress error messages?</p>
			<ul class="enough_waffling">
		    	<li>
		    		<input name="sfsi_icons_suppress_errors" <?php echo ($sfsi_icons_suppress_errors=='yes') ?  'checked="true"' : '' ;?> type="radio" value="yes" class="styled"  />
		    		<label>Yes</label>
		    	</li>
				<li>
					<input name="sfsi_icons_suppress_errors" <?php echo ($sfsi_icons_suppress_errors=='no') ?  'checked="true"' : '' ;?> type="radio" value="no" class="styled" />
					<label>No</label>
				</li>
	      	</ul>
      	</div>
	</div>
	
	<!-- <div class="row new_wind">
		<h4>Tips</h4>
		<div class="row_onl"><p>Show useful tips for more sharing & traffic?</p>
			<ul class="enough_waffling">
		    	<li>
		    		<input name="sfsi_icons_hide_banners" checked="true"  type="radio" value="yes" class="styled"  />
		    		<label>Yes</label>
		    	</li>
				<li>
					<input name="sfsi_icons_hide_banners" type="radio" value="no" class="styled" />
					<label>No</label>
				</li>
	      	</ul>
      	</div>
    </div> -->

	<?php sfsi_ask_for_help(5); ?>
    <!-- SAVE BUTTON SECTION   --> 
    <div class="save_button">
         <img src="<?php echo SFSI_PLUGURL ?>images/ajax-loader.gif" class="loader-img" />
         <?php  $nonce = wp_create_nonce("update_step5"); ?>
         <a href="javascript:;" id="sfsi_save5" title="Save" data-nonce="<?php echo $nonce;?>">Save</a>
    </div>
    <!-- END SAVE BUTTON SECTION   -->
    
    <a class="sfsiColbtn closeSec" href="javascript:;" >Collapse area</a>
    <label class="closeSec"></label>
        
    <!-- ERROR AND SUCCESS MESSAGE AREA-->
    <p class="red_txt errorMsg" style="display:none"> </p>
    <p class="green_txt sucMsg" style="display:none"> </p>
    <div class="clear"></div>
    
</div>
<!-- END Section 5 "Any other wishes for your main icons?"-->
