<?php
if( !defined('ABSPATH') ){ exit();}
global $current_user;
$auth_varble=0;
wp_get_current_user();
$imgpath= plugins_url()."/twitter-auto-publish/images/";
$heimg=$imgpath."support.png";


if(!$_POST && isset($_GET['twap_notice']) && $_GET['twap_notice'] == 'hide')
{
	if (! isset( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'],'twap-shw')){
		wp_nonce_ays( 'twap-shw');
		exit;
	}
	update_option('xyz_twap_dnt_shw_notice', "hide");
	?>
<style type='text/css'>
#tw_notice_td
{
display:none !important;
}
</style>
<div class="system_notice_area_style1" id="system_notice_area">
Thanks again for using the plugin. We will never show the message again.
 &nbsp;&nbsp;&nbsp;<span
		id="system_notice_area_dismiss">Dismiss</span>
</div>

<?php
}



$tms1="";
$tms2="";
$tms3="";
$tms4="";
$tms5="";
$tms6="";

$terf=0;
if(isset($_POST['twit']))
{
	if (! isset( $_REQUEST['_wpnonce'] )|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'xyz_smap_tw_settings_form_nonce' ))
	{
		wp_nonce_ays( 'xyz_smap_tw_settings_form_nonce' );
		exit();
	}
	$tappid=sanitize_text_field($_POST['xyz_twap_twconsumer_id']);
	$tappsecret=sanitize_text_field($_POST['xyz_twap_twconsumer_secret']);
	$twid=sanitize_text_field($_POST['xyz_twap_tw_id']);
	$taccess_token=sanitize_text_field($_POST['xyz_twap_current_twappln_token']);
	$taccess_token_secret=sanitize_text_field($_POST['xyz_twap_twaccestok_secret']);
	$tposting_permission=intval($_POST['xyz_twap_twpost_permission']);
	$tposting_image_permission=intval($_POST['xyz_twap_twpost_image_permission']);
	$tmessagetopost=$_POST['xyz_twap_twmessage'];
	$xyz_twap_tw_char_limit=$_POST['xyz_twap_tw_char_limit'];
	$xyz_twap_tw_char_limit=intval($xyz_twap_tw_char_limit);
	if ($xyz_twap_tw_char_limit<140)
		$xyz_twap_tw_char_limit=140;
	if($tappid=="" && $tposting_permission==1)
	{
		$terf=1;
		$tms1="Please fill api key.";

	}
	elseif($tappsecret=="" && $tposting_permission==1)
	{
		$tms2="Please fill api secret.";
		$terf=1;
	}
	elseif($twid=="" && $tposting_permission==1)
	{
		$tms3="Please fill twitter username.";
		$terf=1;
	}
	elseif($taccess_token=="" && $tposting_permission==1)
	{
		$tms4="Please fill twitter access token.";
		$terf=1;
	}
	elseif($taccess_token_secret=="" && $tposting_permission==1)
	{
		$tms5="Please fill twitter access token secret.";
		$terf=1;
	}
	elseif($tmessagetopost=="" && $tposting_permission==1)
	{
		$tms6="Please fill message format for posting.";
		$terf=1;
	}
	else
	{
		$terf=0;
		if($tmessagetopost=="")
		{
			$tmessagetopost="{POST_TITLE}-{PERMALINK}";
		}

		update_option('xyz_twap_twconsumer_id',$tappid);
		update_option('xyz_twap_twconsumer_secret',$tappsecret);
		update_option('xyz_twap_tw_id',$twid);
		update_option('xyz_twap_current_twappln_token',$taccess_token);
		update_option('xyz_twap_twaccestok_secret',$taccess_token_secret);
		update_option('xyz_twap_twmessage',$tmessagetopost);
		update_option('xyz_twap_twpost_permission',$tposting_permission);
		update_option('xyz_twap_twpost_image_permission',$tposting_image_permission);
		update_option('xyz_twap_tw_char_limit', $xyz_twap_tw_char_limit);
		
	}
}



if(isset($_POST['twit']) && $terf==0)
{
	?>

<div class="system_notice_area_style1" id="system_notice_area">
	Settings updated successfully. &nbsp;&nbsp;&nbsp;<span
		id="system_notice_area_dismiss">Dismiss</span>
</div>
<?php }
if(isset($_POST['twit']) && $terf==1)
{
	?>
<div class="system_notice_area_style0" id="system_notice_area">
	<?php 
	if(isset($_POST['twit']))
	{
		echo esc_html($tms1);echo esc_html($tms2);echo esc_html($tms3);echo esc_html($tms4);echo esc_html($tms5);echo esc_html($tms6);
	}
	?>
	&nbsp;&nbsp;&nbsp;<span id="system_notice_area_dismiss">Dismiss</span>
</div>
<?php } ?>
<script type="text/javascript">
function detdisplay_twap(id)
{
	document.getElementById(id).style.display='';
}
function dethide_twap(id)
{
	document.getElementById(id).style.display='none';
}


</script>

<div style="width: 100%">

<div class="xyz_twap_tab">
  <button class="xyz_twap_tablinks" onclick="xyz_twap_open_tab(event, 'xyz_twap_twitter_settings')" id="xyz_twap_default_tab_settings">Twitter Settings</button>
   <button class="xyz_twap_tablinks" onclick="xyz_twap_open_tab(event, 'xyz_twap_basic_settings')" id="xyz_twap_basic_tab_settings">General Settings</button>
</div>
<div id="xyz_twap_twitter_settings" class="xyz_twap_tabcontent">
<table class="widefat" style="width: 99%;background-color: #FFFBCC">
<tr>
<td id="bottomBorderNone" style="border: 1px solid #FCC328;">
	<div>
		<b>Note :</b> You have to create a Twitter application before filling in following fields. 	
		<br><b><a href="https://developer.twitter.com/en/apps/create" target="_blank">Click here</a></b> to create new application. Specify the website for the application as :	<span style="color: red;"><?php echo  (is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']; ?>		 </span> 
		 <br>In the twitter application, navigate to	<b>Settings > Application Type > Access</b>. Select <b>Read and Write</b> option. 
		 <br>After updating access, navigate to <b>Details > Your access token</b> in the application and	click <b>Create my access token</b> button.
		<br>For detailed step by step instructions <b><a href="http://help.xyzscripts.com/docs/social-media-auto-publish/faq/how-can-i-create-twitter-application/" target="_blank">Click here</a></b>.

	</div>
</td>
</tr>
</table>


	<form method="post">
	<?php wp_nonce_field( 'xyz_smap_tw_settings_form_nonce' );?>
		<input type="hidden" value="config">

			<div style="font-weight: bold;padding: 3px;">All fields given below are mandatory</div> 
			<table class="widefat xyz_twap_widefat_table" style="width: 99%">
						<tr valign="top">
					<td>Enable auto publish	posts to my twitter account
					</td>
			<td  class="switch-field">
				<label id="xyz_twap_twpost_permission_yes"><input type="radio" name="xyz_twap_twpost_permission" value="1" <?php  if(get_option('xyz_twap_twpost_permission')==1) echo 'checked';?>/>Yes</label>
				<label id="xyz_twap_twpost_permission_no"><input type="radio" name="xyz_twap_twpost_permission" value="0" <?php  if(get_option('xyz_twap_twpost_permission')==0) echo 'checked';?>/>No</label>
			</td>
				</tr>
				<tr valign="top">
					<td width="50%">API key
					</td>
					<td><input id="xyz_twap_twconsumer_id"
						name="xyz_twap_twconsumer_id" type="text"
						value="<?php if($tms1=="") {echo esc_html(get_option('xyz_twap_twconsumer_id'));}?>" />
						<a href="http://help.xyzscripts.com/docs/social-media-auto-publish/faq/how-can-i-create-twitter-application/" target="_blank">How can I create a Twitter Application?</a>
					</td>
				</tr>

				<tr valign="top">
					<td>API secret
					</td>
					<td><input id="xyz_twap_twconsumer_secret"
						name="xyz_twap_twconsumer_secret" type="text"
						value="<?php if($tms2=="") { echo esc_html(get_option('xyz_twap_twconsumer_secret')); }?>" />
					</td>
				</tr>
				<tr valign="top">
					<td>Twitter username
					</td>
					<td><input id="xyz_twap_tw_id" class="al2tw_text"
						name="xyz_twap_tw_id" type="text"
						value="<?php if($tms3=="") {echo esc_html(get_option('xyz_twap_tw_id'));}?>" />
					</td>
				</tr>
				<tr valign="top">
					<td>Access token
					</td>
					<td><input id="xyz_twap_current_twappln_token" class="al2tw_text"
						name="xyz_twap_current_twappln_token" type="text"
						value="<?php if($tms4=="") {echo esc_html(get_option('xyz_twap_current_twappln_token'));}?>" />
					</td>
				</tr>
				<tr valign="top">
					<td>Access	token secret
					</td>
					<td><input id="xyz_twap_twaccestok_secret" class="al2tw_text"
						name="xyz_twap_twaccestok_secret" type="text"
						value="<?php if($tms5=="") {echo esc_html(get_option('xyz_twap_twaccestok_secret'));}?>" />
					</td>
				</tr>
				<tr valign="top">
					<td>Message format for posting <img src="<?php echo $heimg?>"
						onmouseover="detdisplay_twap('xyz_tw')" onmouseout="dethide_twap('xyz_tw')" style="width:13px;height:auto;">
						<div id="xyz_tw" class="twap_informationdiv"
							style="display: none; font-weight: normal;">
							{POST_TITLE} - Insert the title of your post.<br />{PERMALINK} -
							Insert the URL where your post is displayed.<br />{POST_EXCERPT}
							- Insert the excerpt of your post.<br />{POST_CONTENT} - Insert
							the description of your post.<br />{BLOG_TITLE} - Insert the name
							of your blog.<br />{USER_NICENAME} - Insert the nicename
							of the author.<br />{POST_ID} - Insert the ID of your post.
							<br />{POST_PUBLISH_DATE} - Insert the publish date of your post.
							<br />{USER_DISPLAY_NAME} - Insert the display name of the author.
						</div></td>
	<td>
	<select name="xyz_twap_info" id="xyz_twap_info" onchange="xyz_twap_info_insert(this)">
		<option value ="0" selected="selected">--Select--</option>
		<option value ="1">{POST_TITLE}  </option>
		<option value ="2">{PERMALINK} </option>
		<option value ="3">{POST_EXCERPT}  </option>
		<option value ="4">{POST_CONTENT}   </option>
		<option value ="5">{BLOG_TITLE}   </option>
		<option value ="6">{USER_NICENAME}   </option>
		<option value ="7">{POST_ID}   </option>
		<option value ="8">{POST_PUBLISH_DATE}   </option>
		<option value ="9">{USER_DISPLAY_NAME}   </option>
		</select> </td></tr><tr><td>&nbsp;</td><td>
		<textarea id="xyz_twap_twmessage"  name="xyz_twap_twmessage" style="height:80px !important;" ><?php if($tms6=="") {
								echo esc_textarea(get_option('xyz_twap_twmessage'));}?></textarea>
	</td></tr>
						
				
				<tr valign="top">
					<td>Attach image to twitter post
					</td>
					<td  class="switch-field">
						<label id="xyz_twap_twpost_image_permission_yes"><input type="radio" name="xyz_twap_twpost_image_permission" value="1" <?php  if(get_option('xyz_twap_twpost_image_permission')==1) echo 'checked';?>/>Yes</label>
						<label id="xyz_twap_twpost_image_permission_no"><input type="radio" name="xyz_twap_twpost_image_permission" value="0" <?php  if(get_option('xyz_twap_twpost_image_permission')==0) echo 'checked';?>/>No</label>
					</td>
				</tr>
				
				<tr valign="top">
					<td>Twitter character limit  <img src="<?php echo $heimg?>"
							onmouseover="detdisplay_twap('xyz_twap_tw_char_limit')" onmouseout="dethide_twap('xyz_twap_tw_char_limit')" style="width:13px;height:auto;">
							<div id="xyz_twap_tw_char_limit" class="twap_informationdiv" style="display: none;">
							The character limit of tweets  is 280.<br/>
							Use 140 for languages like Chinese, Japanese and Korean <br/>which won't get the 280 character limit.<br />
							</div></td>
				<td>
					<input id="xyz_twap_tw_char_limit"  name="xyz_twap_tw_char_limit" type="text" value="<?php echo esc_html(get_option('xyz_twap_tw_char_limit'));?>" style="width: 155px">
				</td></tr>
				
				<tr>
			<td   id="bottomBorderNone"></td>
					<td   id="bottomBorderNone"><div style="height: 50px;">
							<input type="submit" class="submit_twap_new"
								style=" margin-top: 10px; "
								name="twit" value="Save" /></div>
					</td>
				</tr>
			</table>

	</form>
</div>
<?php 

	if(isset($_POST['bsettngs']))
	{
		if (! isset( $_REQUEST['_wpnonce'] )|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'xyz_smap_tw_basic_settings_form_nonce' ))
		{
			wp_nonce_ays( 'xyz_smap_tw_basic_settings_form_nonce' );
			exit();
		}

		$xyz_twap_include_pages=intval($_POST['xyz_twap_include_pages']);
		$xyz_twap_include_posts=intval($_POST['xyz_twap_include_posts']);

		if($_POST['xyz_twap_cat_all']=="All")
			$twap_category_ids=$_POST['xyz_twap_cat_all'];//redio btn name
		else
		{
			$twap_category_ids=$_POST['xyz_twap_catlist'];//dropdown
			$twap_category_ids=implode(',', $twap_category_ids);
		}
		$xyz_customtypes="";
		
        if(isset($_POST['post_types']))
		$xyz_customtypes=$_POST['post_types'];

        $xyz_twap_peer_verification=intval($_POST['xyz_twap_peer_verification']);
        $xyz_twap_premium_version_ads=intval($_POST['xyz_twap_premium_version_ads']);
        $xyz_twap_default_selection_edit=intval($_POST['xyz_twap_default_selection_edit']);
        
        //$xyz_twap_future_to_publish=$_POST['xyz_twap_future_to_publish'];
		$twap_customtype_ids="";

		$xyz_twap_applyfilters="";
		if(isset($_POST['xyz_twap_applyfilters']))
			$xyz_twap_applyfilters=$_POST['xyz_twap_applyfilters'];
		
		
		
		if($xyz_customtypes!="")
		{
			for($i=0;$i<count($xyz_customtypes);$i++)
			{
				$twap_customtype_ids.=$xyz_customtypes[$i].",";
			}

		}
		$twap_customtype_ids=rtrim($twap_customtype_ids,',');

		$xyz_twap_applyfilters_val="";
		if($xyz_twap_applyfilters!="")
		{
			for($i=0;$i<count($xyz_twap_applyfilters);$i++)
			{
			$xyz_twap_applyfilters_val.=$xyz_twap_applyfilters[$i].",";
		}
		}
		$xyz_twap_applyfilters_val=rtrim($xyz_twap_applyfilters_val,',');
		
		update_option('xyz_twap_apply_filters',$xyz_twap_applyfilters_val);
		update_option('xyz_twap_include_pages',$xyz_twap_include_pages);
		update_option('xyz_twap_include_posts',$xyz_twap_include_posts);
		if($xyz_twap_include_posts==0)
			update_option('xyz_twap_include_categories',"All");
		else
			update_option('xyz_twap_include_categories',$twap_category_ids);
		update_option('xyz_twap_include_customposttypes',$twap_customtype_ids);
		update_option('xyz_twap_peer_verification',$xyz_twap_peer_verification);
		update_option('xyz_twap_premium_version_ads',$xyz_twap_premium_version_ads);
		update_option('xyz_twap_default_selection_edit',$xyz_twap_default_selection_edit);
		//update_option('xyz_twap_future_to_publish',$xyz_twap_future_to_publish);
	}
	//$xyz_twap_future_to_publish=get_option('xyz_twap_future_to_publish');
	$xyz_credit_link=get_option('xyz_credit_link');
	$xyz_twap_include_pages=get_option('xyz_twap_include_pages');
	$xyz_twap_include_posts=get_option('xyz_twap_include_posts');
	$xyz_twap_include_categories=get_option('xyz_twap_include_categories');
	if ($xyz_twap_include_categories!='All')
	$xyz_twap_include_categories=explode(',', $xyz_twap_include_categories);
	$xyz_twap_include_customposttypes=get_option('xyz_twap_include_customposttypes');
	$xyz_twap_apply_filters=get_option('xyz_twap_apply_filters');
	$xyz_twap_peer_verification=esc_html(get_option('xyz_twap_peer_verification'));
	$xyz_twap_premium_version_ads=esc_html(get_option('xyz_twap_premium_version_ads'));
	$xyz_twap_default_selection_edit=esc_html(get_option('xyz_twap_default_selection_edit'));
	?>
	
		<div id="xyz_twap_basic_settings" class="xyz_twap_tabcontent">
		<form method="post">
<?php wp_nonce_field( 'xyz_smap_tw_basic_settings_form_nonce' );?>
			<table class="widefat xyz_twap_widefat_table" style="width: 99%">
			<tr><td><h2>Basic Settings</h2></td></tr>
				<tr valign="top">

					<td  colspan="1" width="50%">Publish wordpress `pages` to twitter
					</td>
			<td  class="switch-field">
				<label id="xyz_twap_include_pages_yes"><input type="radio" name="xyz_twap_include_pages" value="1" <?php  if($xyz_twap_include_pages==1) echo 'checked';?>/>Yes</label>
				<label id="xyz_twap_include_pages_no"><input type="radio" name="xyz_twap_include_pages" value="0" <?php  if($xyz_twap_include_pages==0) echo 'checked';?>/>No</label>
			</td>
				</tr>
				
				<tr valign="top">

					<td  colspan="1">Publish wordpress `posts` to twitter
					</td>
			<td  class="switch-field">
				<label id="xyz_twap_include_posts_yes"><input type="radio" name="xyz_twap_include_posts" value="1" <?php  if($xyz_twap_include_posts==1) echo 'checked';?>/>Yes</label>
				<label id="xyz_twap_include_posts_no"><input type="radio" name="xyz_twap_include_posts" value="0" <?php  if($xyz_twap_include_posts==0) echo 'checked';?>/>No</label>
			</td>
				</tr>
				<?php 
				$xyz_twap_hide_custompost_settings='';
					$args=array(
							'public'   => true,
							'_builtin' => false
					);
					$output = 'names'; // names or objects, note names is the default
					$operator = 'and'; // 'and' or 'or'
					$post_types=get_post_types($args,$output,$operator);

					$ar1=explode(",",$xyz_twap_include_customposttypes);
					$cnt=count($post_types);
					if($cnt==0)
					$xyz_twap_hide_custompost_settings = 'style="display: none;"';//echo 'NA';
					?>
				<tr valign="top" <?php echo $xyz_twap_hide_custompost_settings;?>>

					<td  colspan="1">Select wordpress custom post types for auto publish</td>
					<td><?php 
					foreach ($post_types  as $post_type ) {
					
						echo '<input type="checkbox" name="post_types[]" value="'.$post_type.'" ';
						if(in_array($post_type, $ar1))
						{
							echo 'checked="checked"/>';
						}
						else
							echo '/>';
					
							echo $post_type.'<br/>';
					
					}
					?>
					</td>
				</tr>
				
				
				<tr><td><h2>Advanced Settings</h2></td></tr>
				
				<tr valign="top" id="selPostCat">

					<td  colspan="1">Select post categories for auto publish
					</td>
					<td class="switch-field">
	                <input type="hidden" value="<?php echo esc_html($xyz_twap_include_categories);?>" name="xyz_twap_sel_cat" 
			id="xyz_twap_sel_cat"> 
					<label id="xyz_twap_include_categories_no">
					<input type="radio"	name="xyz_twap_cat_all" id="xyz_twap_cat_all" value="All" onchange="rd_cat_chn(1,-1)" <?php if($xyz_twap_include_categories=="All") echo "checked"?>>All<font style="padding-left: 10px;"></font></label>
					<label id="xyz_twap_include_categories_yes">
					<input type="radio"	name="xyz_twap_cat_all" id="xyz_twap_cat_all" value=""	onchange="rd_cat_chn(1,1)" <?php if($xyz_twap_include_categories!="All") echo "checked"?>>Specific</label>
					<br /> <br /> <div class="scroll_checkbox"  id="cat_dropdown_span">
					<?php 
					$args = array(
							'show_option_all'    => '',
							'show_option_none'   => '',
							'orderby'            => 'name',
							'order'              => 'ASC',
							'show_last_update'   => 0,
							'show_count'         => 0,
							'hide_empty'         => 0,
							'child_of'           => 0,
							'exclude'            => '',
							'echo'               => 0,
							'selected'           => '1 3',
							'hierarchical'       => 1,
							'id'                 => 'xyz_twap_catlist',
							'class'              => 'postform',
							'depth'              => 0,
							'tab_index'          => 0,
							'taxonomy'           => 'category',
							'hide_if_empty'      => false );

					if(count(get_categories($args))>0)
					{
						$twap_categories=get_categories();
						foreach ($twap_categories as $twap_cat)
						{
							$cat_id[]=$twap_cat->cat_ID;
							$cat_name[]=$twap_cat->cat_name;
							?>
							<input type="checkbox" name="xyz_twap_catlist[]"  value="<?php  echo $twap_cat->cat_ID;?>" <?php if(is_array($xyz_twap_include_categories)) if(in_array($twap_cat->cat_ID, $xyz_twap_include_categories)) echo "checked" ?>/><?php echo $twap_cat->cat_name; ?>
							<br/><?php }
					}
					else
						echo "NIL";
					?><br /> <br /> </div>
				</td>
				</tr>

				<tr valign="top">

					<td scope="row" colspan="1" width="50%">Auto publish on editing posts/pages/custom post types
					</td>
				<td>
					<input type="radio" name="xyz_twap_default_selection_edit" value="1" <?php  if($xyz_twap_default_selection_edit==1) echo 'checked';?>/>Enabled
					<br/><input type="radio" name="xyz_twap_default_selection_edit" value="0" <?php  if($xyz_twap_default_selection_edit==0) echo 'checked';?>/>Disabled
					<br/><input type="radio" name="xyz_twap_default_selection_edit" value="2" <?php  if($xyz_twap_default_selection_edit==2) echo 'checked';?>/>Use settings from post creation or post updation
				</td>
				</tr>

				<tr valign="top">
				
				<td scope="row" colspan="1" width="50%">Enable SSL peer verification in remote requests</td>
				<td  class="switch-field">
					<label id="xyz_twap_peer_verification_yes"><input type="radio" name="xyz_twap_peer_verification" value="1" <?php  if($xyz_twap_peer_verification==1) echo 'checked';?>/>Yes</label>
					<label id="xyz_twap_peer_verification_no"><input type="radio" name="xyz_twap_peer_verification" value="0" <?php  if($xyz_twap_peer_verification==0) echo 'checked';?>/>No</label>
				</td>
				</tr>
				
				<tr valign="top">
					<td scope="row" colspan="1">Apply filters during publishing	</td>
					<td>
					<?php 
					$ar2=explode(",",$xyz_twap_apply_filters);
					for ($i=0;$i<3;$i++ ) {
						$filVal=$i+1;
						
						if($filVal==1)
							$filName='the_content';
						else if($filVal==2)
							$filName='the_excerpt';
						else if($filVal==3)
							$filName='the_title';
						else $filName='';
						
						echo '<input type="checkbox" name="xyz_twap_applyfilters[]"  value="'.$filVal.'" ';
						if(in_array($filVal, $ar2))
						{
							echo 'checked="checked"/>';
						}
						else
							echo '/>';
					
						echo '<label>'.$filName.'</label><br/>';
					
					}
					
					?>
					</td>
				</tr>
<tr><td><h2>Other Settings</h2></td></tr>

				<tr valign="top">

					<td  colspan="1">Enable credit link to author
					</td>
					<td  class="switch-field">
						<label id="xyz_credit_link_yes"><input type="radio" name="xyz_credit_link" value="twap" <?php  if($xyz_credit_link=='twap') echo 'checked';?>/>Yes</label>
						<label id="xyz_credit_link_no"><input type="radio" name="xyz_credit_link" value="<?php echo $xyz_credit_link!='twap'?$xyz_credit_link:0;?>" <?php  if($xyz_credit_link!='twap') echo 'checked';?>/>No</label>
					</td>
				</tr>

				
				

				<tr valign="top">

					<td  colspan="1">Enable premium version ads
					</td>
					<td  class="switch-field">
						<label id="xyz_twap_premium_version_ads_yes"><input type="radio" name="xyz_twap_premium_version_ads" value="1" <?php  if($xyz_twap_premium_version_ads==1) echo 'checked';?>/>Yes</label>
						<label id="xyz_twap_premium_version_ads_no"><input type="radio" name="xyz_twap_premium_version_ads" value="0" <?php  if($xyz_twap_premium_version_ads==0) echo 'checked';?>/>No</label>
					</td>
				</tr>

				
				<tr>

					<td id="bottomBorderNone">
							

					</td>

					
<td id="bottomBorderNone"><div style="height: 50px;">
<input type="submit" class="submit_twap_new" style="margin-top: 10px;"	value=" Update Settings" name="bsettngs" /></div></td>
				</tr>


			</table>
		</form>
		</div>
		
</div>		
<?php if (is_array($xyz_twap_include_categories))
$xyz_twap_include_categories1=implode(',', $xyz_twap_include_categories);
else 
	$xyz_twap_include_categories1=$xyz_twap_include_categories;
	?>
	<script type="text/javascript">
	//drpdisplay();
var catval='<?php echo esc_html($xyz_twap_include_categories1); ?>';
var custtypeval='<?php echo esc_html($xyz_twap_include_customposttypes); ?>';
var get_opt_cats='<?php echo esc_html(get_option('xyz_twap_include_posts'));?>';
jQuery(document).ready(function() {
	<?php  if(isset($_POST['bsettngs'])) {?>
					document.getElementById("xyz_twap_basic_tab_settings").click();	
					<?php }
					else {?>
					document.getElementById("xyz_twap_default_tab_settings").click();
					<?php }?>

	
	  if(catval=="All")
		  jQuery("#cat_dropdown_span").hide();
	  else
		  jQuery("#cat_dropdown_span").show();

	  if(get_opt_cats==0)
		  jQuery('#selPostCat').hide();
	  else
		  jQuery('#selPostCat').show();
   var xyz_credit_link=jQuery("input[name='xyz_credit_link']:checked").val();
   if(xyz_credit_link=='twap')
	   xyz_credit_link=1;
   else
	   xyz_credit_link=0;
   XyzTwapToggleRadio(xyz_credit_link,'xyz_credit_link');
   
   var xyz_twap_cat_all=jQuery("input[name='xyz_twap_cat_all']:checked").val();
   if (xyz_twap_cat_all == 'All') 
	   xyz_twap_cat_all=0;
   else 
	   xyz_twap_cat_all=1;
   XyzTwapToggleRadio(xyz_twap_cat_all,'xyz_twap_include_categories'); 
  

   var toggle_element_ids=['xyz_twap_twpost_image_permission','xyz_twap_twpost_permission','xyz_twap_include_pages','xyz_twap_include_posts','xyz_twap_peer_verification','xyz_twap_premium_version_ads'];

   jQuery.each(toggle_element_ids, function( index, value ) {
		   checkedval= jQuery("input[name='"+value+"']:checked").val();
		   XyzTwapToggleRadio(checkedval,value); 
   	});
	}); 
	
function setcat(obj)
{
var sel_str="";
for(k=0;k<obj.options.length;k++)
{
if(obj.options[k].selected)
sel_str+=obj.options[k].value+",";
}


var l = sel_str.length; 
var lastChar = sel_str.substring(l-1, l); 
if (lastChar == ",") { 
	sel_str = sel_str.substring(0, l-1);
}

document.getElementById('xyz_twap_sel_cat').value=sel_str;

}

//var d1='<?php // echo esc_html($xyz_twap_include_categories1);?>';
// splitText = d1.split(",");
// jQuery.each(splitText, function(k,v) {
// jQuery("#xyz_twap_catlist").children("option[value="+v+"]").attr("selected","selected");
// });

function rd_cat_chn(val,act)
{
	if(val==1)
	{
		if(act==-1)
		  jQuery("#cat_dropdown_span").hide();
		else
		  jQuery("#cat_dropdown_span").show();
	}
	
}

function xyz_twap_info_insert(inf){
	
    var e = document.getElementById("xyz_twap_info");
    var ins_opt = e.options[e.selectedIndex].text;
    if(ins_opt=="0")
    	ins_opt="";
    var str=jQuery("textarea#xyz_twap_twmessage").val()+ins_opt;
    jQuery("textarea#xyz_twap_twmessage").val(str);
    jQuery('#xyz_twap_info :eq(0)').prop('selected', true);
    jQuery("textarea#xyz_twap_twmessage").focus();

}
function xyz_twap_show_postCategory(val)
{
	if(val==0)
		jQuery('#selPostCat').hide();
	else
		jQuery('#selPostCat').show();
}
var toggle_element_ids=['xyz_twap_twpost_image_permission','xyz_twap_twpost_permission','xyz_twap_include_pages','xyz_twap_include_posts','xyz_twap_peer_verification','xyz_credit_link','xyz_twap_premium_version_ads','xyz_twap_include_categories'];

jQuery.each(toggle_element_ids, function( index, value ) {
	jQuery("#"+value+"_no").click(function(){
		XyzTwapToggleRadio(0,value);
		if(value=='xyz_twap_include_posts')
			xyz_twap_show_postCategory(0);
	});
	jQuery("#"+value+"_yes").click(function(){
		XyzTwapToggleRadio(1,value);
		if(value=='xyz_twap_include_posts')
			xyz_twap_show_postCategory(1);
	});
	});
function xyz_twap_open_tab(evt, xyz_twap_form_div_id) {
    var i, xyz_twap_tabcontent, xyz_twap_tablinks;
    tabcontent = document.getElementsByClassName("xyz_twap_tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("xyz_twap_tablinks");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(xyz_twap_form_div_id).style.display = "block";
    evt.currentTarget.className += " active";
}
</script>
	<?php 
?>
