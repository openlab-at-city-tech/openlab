<?php
/* DEFAULT OPTIONS
*------------------------------------------------------------*/
add_option('wp_twitter_fdx_tweet_button_display_single', '1');
add_option('wp_twitter_fdx_tweet_button_display_page', '1');
add_option('wp_twitter_fdx_tweet_button_display_home', '-1');
add_option('wp_twitter_fdx_tweet_button_display_arquive', '-1');

add_option('wp_twitter_copynshare', '-1');

add_option('wp_twitter_fdx_tweet_button_place', 'after');
add_option('wp_twitter_fdx_tweet_button_style', 'large_buton');
add_option('wp_twitter_fdx_tweet_button_style2', 'floatside_left');
add_option('wp_twitter_fdx_tweet_button_style3', '3');
add_option('wp_twitter_fdx_tweet_button_choose', 'direct_post');
add_option('wp_twitter_fdx_tweet_button_container', 'text-align: center');
add_option('wp_twitter_fdx_tweet_button_twitter_username', '');

add_option('wp_twitter_fdx_services', 'facebook,twitter,linkedin,email,sharethis');    //New
add_option('wp_twitter_fdx_logo_top', plugins_url( '/images/logo300x40.png', dirname(__FILE__)));


//#############################################################################################################################
   	$wp_twitter_fdx_tweet_button_place = @$_POST['wp_twitter_fdx_tweet_button_place'];
	$wp_twitter_fdx_tweet_button_style = @$_POST['wp_twitter_fdx_tweet_button_style'];
    $wp_twitter_fdx_tweet_button_style2 = @$_POST['wp_twitter_fdx_tweet_button_style2'];
    $wp_twitter_fdx_tweet_button_style3 = @$_POST['wp_twitter_fdx_tweet_button_style3'];
    $wp_twitter_fdx_tweet_button_choose  = @$_POST['wp_twitter_fdx_tweet_button_choose'];

    if (isset($_POST['info_update2']))
    {
        update_option('wp_twitter_fdx_tweet_button_display_single', (@$_POST['wp_twitter_fdx_tweet_button_display_single']=='1') ? '1':'-1' );
		update_option('wp_twitter_fdx_tweet_button_display_page', (@$_POST['wp_twitter_fdx_tweet_button_display_page']=='1') ? '1':'-1' );
		update_option('wp_twitter_fdx_tweet_button_display_home', (@$_POST['wp_twitter_fdx_tweet_button_display_home']=='1') ? '1':'-1' );
		update_option('wp_twitter_fdx_tweet_button_display_arquive', (@$_POST['wp_twitter_fdx_tweet_button_display_arquive']=='1') ? '1':'-1' );

        update_option('wp_twitter_copynshare', (@$_POST['wp_twitter_copynshare']=='1') ? '1':'-1' );

		update_option('wp_twitter_fdx_tweet_button_container', stripslashes_deep((string)$_POST['wp_twitter_fdx_tweet_button_container']));
		update_option('wp_twitter_fdx_tweet_button_twitter_username', stripslashes_deep((string)$_POST['wp_twitter_fdx_tweet_button_twitter_username']));

   		update_option('wp_twitter_fdx_services', stripslashes_deep((string)$_POST['wp_twitter_fdx_services']));   //New


		update_option('wp_twitter_fdx_logo_top', stripslashes_deep((string)$_POST['wp_twitter_fdx_logo_top']));
    	update_option('wp_twitter_fdx_tweet_button_place', stripslashes_deep((string)@$_POST['wp_twitter_fdx_tweet_button_place']));
		update_option('wp_twitter_fdx_tweet_button_style', stripslashes_deep((string)@$_POST['wp_twitter_fdx_tweet_button_style']));
        update_option('wp_twitter_fdx_tweet_button_style2', stripslashes_deep((string)@$_POST['wp_twitter_fdx_tweet_button_style2']));
        update_option('wp_twitter_fdx_tweet_button_style3', stripslashes_deep((string)@$_POST['wp_twitter_fdx_tweet_button_style3']));
 		update_option('wp_twitter_fdx_tweet_button_choose', stripslashes_deep((string)@$_POST['wp_twitter_fdx_tweet_button_choose']));
       echo '<div class="updated fade"><p><strong>' . __( 'Settings updated', $this->hook ) . '.</strong></p></div>';
        } else {
	$wp_twitter_fdx_tweet_button_place = get_option('wp_twitter_fdx_tweet_button_place');
	$wp_twitter_fdx_tweet_button_style = get_option('wp_twitter_fdx_tweet_button_style');
    $wp_twitter_fdx_tweet_button_style2 = get_option('wp_twitter_fdx_tweet_button_style2');
    $wp_twitter_fdx_tweet_button_style3 = get_option('wp_twitter_fdx_tweet_button_style3');
    $wp_twitter_fdx_tweet_button_choose  = get_option('wp_twitter_fdx_tweet_button_choose');
}
echo '<div class="wrap">'. get_screen_icon('fdx-lock');
echo '<h2>'. $this->pluginname . ' : ' . __('Sharethis Button Integration', $this->hook) . '</h2>';
?>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<?php include('inc-sidebar.php'); ?>

<div class="postbox-container">
<div class="meta-box-sortables">

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<input type="hidden" name="info_update2" id="info_update" value="true" />

<div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon3">&nbsp;</span><?php _e('Allow integration in:', $this->hook) ?></h3>
<div class="inside">
<ul>
<li><input name="wp_twitter_fdx_tweet_button_display_single" type="checkbox"<?php if(get_option('wp_twitter_fdx_tweet_button_display_single')!='-1') echo 'checked="checked"'; ?> value="1" /> <?php _e('Post', $this->hook) ?> </li>
<li><input name="wp_twitter_fdx_tweet_button_display_page" type="checkbox"<?php if(get_option('wp_twitter_fdx_tweet_button_display_page')!='-1') echo 'checked="checked"'; ?> value="1" /> <?php _e('Pages', $this->hook) ?> </li>
<li><input name="wp_twitter_fdx_tweet_button_display_home" type="checkbox"<?php if(get_option('wp_twitter_fdx_tweet_button_display_home')!='-1') echo 'checked="checked"'; ?> value="1" /> <?php _e('Front Page (Home)', $this->hook) ?> </li>
<li><input name="wp_twitter_fdx_tweet_button_display_arquive" type="checkbox"<?php if(get_option('wp_twitter_fdx_tweet_button_display_arquive')!='-1') echo 'checked="checked"'; ?> value="1" /> <?php _e('Archive', $this->hook) ?> <span class="description">  (<?php _e('category, tags, author, date, attachment', $this->hook) ?>)</span> </li>
</ul>
</div></div>

<div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon8">&nbsp;</span><?php _e('Sharethis Style', $this->hook) ?></h3>
<div class="inside">

<ul>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="before" <?php checked('before', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('Before Content', $this->hook) ?></li>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="after" <?php checked('after', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('After Content', $this->hook) ?> <span class="description">(<?php _e('default', $this->hook) ?>)</span></li>
</ul>
<h3 style="margin: 0; padding: 0"></h3>

<ul>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="floatside" <?php checked('floatside', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('Fixed on the sides', $this->hook) ?></li>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="fixedtop" <?php checked('fixedtop', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('Top of Page', $this->hook) ?></li>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="fixedbottom" <?php checked('fixedbottom', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('Fixed on Bottom', $this->hook) ?></li>
</ul>


<h3 style="margin: 0; padding: 0"></h3>

<ul>
 <li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="sharenow" <?php checked('sharenow', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('ShareNow', $this->hook) ?></li>
<li><input name="wp_twitter_fdx_tweet_button_place" type="radio" value="shareegg" <?php checked('shareegg', $wp_twitter_fdx_tweet_button_place); ?> /> <?php _e('Share Egg', $this->hook) ?></li>
</ul>

<!-- ***************************************************************************************** -->

 </div></div>

 <div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon7">&nbsp;</span><?php _e('Customization', $this->hook) ?></h3>
<div class="inside">
<br />
<div class="tabsCell">

<table style="width:100%;" class="widefat">
<thead><tr><th>

<ul class="tabs">
<li><a href="#tabid1" title=""><span><?php _e('Buttons', $this->hook) ?></span></a></li>
<li><a href="#tabid2" title=""><span><?php _e('Fixed on the sides', $this->hook) ?></span></a></li>
<li><a href="#tabid3" title=""><span><?php _e('Top of Page', $this->hook) ?></span></a></li>
<li><a href="#tabid4" title=""><span><?php _e('Fixed on Bottom', $this->hook) ?></span></a></li>
<li><a href="#tabid5" title=""><span><?php _e('ShareNow', $this->hook) ?></span></a></li>
</ul>

</th></tr></thead>
<tbody><tr class="alternate"><td>
<div class="tab_container">

<div id="tabid1" class="tab_content">
<!-- ******************************************tab1****************************************** -->
<p><strong><?php _e('Type of the Button', $this->hook) ?>   </strong> </p>
  <table style="width: 100%; text-align: center">
     <tr>
       <td valign="top"><input name="wp_twitter_fdx_tweet_button_style" type="radio" value="small_buton" <?php checked('small_buton', $wp_twitter_fdx_tweet_button_style); ?> /> <?php _e('Small Butons (16x16)', $this->hook) ?><br /><img src="<?php echo plugins_url( 'images/but1.png', dirname(__FILE__));?>" width="140" height="54" border="0" alt="" /></td>
       <td valign="top"><input name="wp_twitter_fdx_tweet_button_style" type="radio" value="large_buton" <?php checked('large_buton', $wp_twitter_fdx_tweet_button_style); ?> /> <?php _e('Large Buttons (32x32)', $this->hook) ?><br /><img src="<?php echo plugins_url( 'images/but2.png', dirname(__FILE__));?>" width="140" height="54" border="0" alt="" /></td>
      <td valign="top"><input name="wp_twitter_fdx_tweet_button_style" type="radio" value="h_count_buton" <?php checked('h_count_buton', $wp_twitter_fdx_tweet_button_style); ?> /> <?php _e('Horizontal Count', $this->hook) ?><br /><img src="<?php echo plugins_url( 'images/but3.png', dirname(__FILE__));?>" width="140" height="54" border="0" alt="" /></td>
       <td valign="top"><input name="wp_twitter_fdx_tweet_button_style" type="radio" value="v_count_buton" <?php checked('v_count_buton', $wp_twitter_fdx_tweet_button_style); ?> /> <?php _e('Vertical Count', $this->hook) ?><br /><img src="<?php echo plugins_url( 'images/but4.png', dirname(__FILE__));?>" width="140" height="54" border="0" alt="" /></td>
     </tr>
   </table>
<p><strong><?php _e('CSS Style Align', $this->hook) ?>:</strong> <input name="wp_twitter_fdx_tweet_button_container" type="text" size="50" value="<?php echo get_option('wp_twitter_fdx_tweet_button_container'); ?>" /></p>
<code>text-align: center</code>&nbsp;&nbsp;&nbsp;<code>float: left; margin-right: 10px;</code>&nbsp;&nbsp;&nbsp;<code>float: right</code>
 <!-- ******************************************tab1****************************************** -->
</div>

<div id="tabid2" class="tab_content">
<!-- ******************************************tab2****************************************** -->
<p><strong><?php _e('Docking Position', $this->hook) ?> </strong>  </p>

<table style="width: 100%">
  <tr>
    <td style="width: 50%; vertical-align: top">
<p><img src="<?php echo plugins_url( 'images/button_sidebar.png', dirname(__FILE__));?>" width="232" height="100" border="0" alt="" style="vertical-align: middle"  /> <input name="wp_twitter_fdx_tweet_button_style2" type="radio" value="floatside_left" <?php checked('floatside_left', $wp_twitter_fdx_tweet_button_style2); ?> /> <?php _e('Left', $this->hook) ?></p>
 </td>
 <td style="width: 50%; vertical-align: top">
<p><img src="<?php echo plugins_url( 'images/button_sidebar2.png', dirname(__FILE__));?>" width="232" height="100" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style2" type="radio" value="floatside_right" <?php checked('floatside_right', $wp_twitter_fdx_tweet_button_style2); ?> /> <?php _e('Right', $this->hook) ?></p>
</td>
  </tr>
</table>
<!-- ******************************************tab2****************************************** -->
</div>

<div id="tabid3" class="tab_content">
<!-- ******************************************tab3****************************************** -->
<p><strong><?php _e('Logo Area', $this->hook) ?></strong> <span class="description"> (300x40 pix)</span>  </p>
<p><img src="<?php echo plugins_url( 'images/button_top.png', dirname(__FILE__));?>" width="234" height="101" border="0" alt="" /><br /><strong>Logo Url:</strong> <input name="wp_twitter_fdx_logo_top" type="text" size="75" value="<?php echo get_option('wp_twitter_fdx_logo_top'); ?>" /></p>
<!-- ******************************************tab3****************************************** -->
</div>

<div id="tabid4" class="tab_content">
<!-- ******************************************tab4****************************************** -->
<p><strong>options of customization: coming soon  </strong>  </p>
<p><img src="<?php echo plugins_url( 'images/sahre_bar.png', dirname(__FILE__));?>" width="233" height="86" border="0" alt="" />   </p>
<!-- ******************************************tab4****************************************** -->
</div>

<div id="tabid5" class="tab_content">
<!-- ******************************************tab5****************************************** -->
<p><strong><?php _e('Choose a theme', $this->hook) ?>  </strong> </p>

<table style="width: 100%">
  <tr>
    <td style="width: 33%; vertical-align: top"><p><img src="<?php echo plugins_url( 'images/fbtheme_3.png', dirname(__FILE__));?>" width="140" height="112" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style3" type="radio" value="3" <?php checked('3', $wp_twitter_fdx_tweet_button_style3); ?> /></p>
<p><img src="<?php echo plugins_url( 'images/fbtheme_4.png', dirname(__FILE__));?>" width="140" height="112" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style3" type="radio" value="4" <?php checked('4', $wp_twitter_fdx_tweet_button_style3); ?> /></p>
</td>
    <td style="width: 33%; vertical-align: top"><p><img src="<?php echo plugins_url( 'images/fbtheme_5.png', dirname(__FILE__));?>" width="140" height="112" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style3" type="radio" value="5" <?php checked('5', $wp_twitter_fdx_tweet_button_style3); ?> /></p>
<p><img src="<?php echo plugins_url( 'images/fbtheme_6.png', dirname(__FILE__));?>" width="140" height="112" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style3" type="radio" value="6" <?php checked('6', $wp_twitter_fdx_tweet_button_style3); ?> /></p>
</td>
    <td style="width: 33%; vertical-align: top"><p><img src="<?php echo plugins_url( 'images/fbtheme_7.png', dirname(__FILE__));?>" width="140" height="112" border="0" alt="" style="vertical-align: middle" /> <input name="wp_twitter_fdx_tweet_button_style3" type="radio" value="7" <?php checked('7', $wp_twitter_fdx_tweet_button_style3); ?> /></p>
</td>
  </tr>
</table>



<!-- ******************************************tab5****************************************** -->
</div>


</div><!-- tab_container -->


</td>
 </tr>
 </tbody>
 </table>

</div><!-- tabsCell -->




<table style="width:100%;" class="widefat">
 <thead><tr><th><?php _e('Change order or modify list of buttons.', $this->hook) ?></th> </tr></thead>
<tbody><tr class="alternate"><td>
<p><strong><?php _e('Selected Services', $this->hook) ?>:</strong> <input name="wp_twitter_fdx_services" type="text" size="65" value="<?php echo get_option('wp_twitter_fdx_services'); ?>" /><small><em> (<?php _e('lowercase, separated by commas', $this->hook) ?>)</em></small></p>
<p><a href="http://sharethis.com/publishers/services-directory" data-width="980" data-height="680" rel="1" id="share1" class="newWindow" title="<?php _e('Sharing Services Directory', $this->hook) ?>"><?php _e('Service Codes', $this->hook) ?></a>: <code>sharethis</code>, <code>email</code>, <code>facebook</code>, <code>twitter</code>, <code>linkedin</code>, <code>pinterest</code>, <code>tumblr</code>, <code>googleplus</code>, <code>blogger</code>, <code>delicious</code>, <code>wordpress</code>, <code>technorati</code>, <code>stumbleupon</code>, <code>reddit</code>, <code>digg</code>, <code>plusone</code><small><em>(Google +1)</em></small>, <code>fblike</code><small><em>(<?php _e('Facebook Like', $this->hook) ?>)</em></small>, <code>fbrec</code><small><em>(<?php _e('Facebook Recommend', $this->hook) ?>)</em></small>, <code>fbsend</code><small><em>(<?php _e('Facebook Send', $this->hook) ?>)</em></small></p>
</td>
 </tr>
 </tbody>
 </table>



<!-- ***************************************************************************************** -->
 </div></div>

<div class="postbox closed">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon5">&nbsp;</span><?php _e('Choose which version of the widget you would like to use: ', $this->hook) ?></h3>
<div class="inside">
<p><input name="wp_twitter_fdx_tweet_button_choose" type="radio" value="multi_post" <?php checked('multi_post', $wp_twitter_fdx_tweet_button_choose); ?> /> <strong><?php _e('Multi Post', $this->hook) ?></strong> <br /><span class="description"><?php _e('Sharing takes place inside the widget, without taking users away from your site. Preferences are saved so your users can share to more than one service at the same time.', $this->hook) ?></span> </p>
<p><input name="wp_twitter_fdx_tweet_button_choose" type="radio" value="direct_post" <?php checked('direct_post', $wp_twitter_fdx_tweet_button_choose); ?> /> <strong><?php _e('Classic', $this->hook) ?></strong> <br /> <span class="description"><?php _e('Your users will be redirected to Facebook, Twitter, etc when clicking on the corresponding buttons. The widget is opened when users click on "Email" and "ShareThis".', $this->hook) ?></span></p>
 </div></div>

<!-- ***************************************************************************************** -->
<div class="postbox closed">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon6">&nbsp;</span><?php _e('Want Analytics?', $this->hook) ?></h3>
<div class="inside">


<p><a href="http://sharethis.com/external-login" data-width="660" data-height="400" rel="1" id="share2_1" class="newWindow"><strong><?php _e('CLICK HERE TO REGISTER', $this->hook) ?></strong></a> <span class="description">  <?php _e('At the end of the flow, you will be given a publisher key. Please paste it in the textbox below.', $this->hook) ?> </span> </p>
<p><strong>Publisher key: </strong><input name="wp_twitter_fdx_tweet_button_twitter_username" type="text" size="45" value="<?php echo get_option('wp_twitter_fdx_tweet_button_twitter_username'); ?>" />&nbsp;&nbsp;&nbsp;&nbsp;<span id="butpop"><a href="http://sharethis.com/publishers/metrics-dashboard" data-width="980" data-height="680" rel="1" id="share_3" class="newWindow"><code class="red"><?php _e('see their stats', $this->hook) ?></code></a></span> </p>
<h3></h3>
<p><input name="wp_twitter_copynshare" type="checkbox"<?php if(get_option('wp_twitter_copynshare')!='-1') echo 'checked="checked"'; ?> value="1" /> <strong>CopyNShare</strong> <br /><span class="description"> <?php _e('If activated, start tracking your users copy and paste shares by adding to your widget (Publisher key is required).', $this->hook) ?> <strong><a href="http://support.sharethis.com/customer/portal/articles/517332#copynshare" target="_blank">FAQs</a></strong></span></p>

<!-- ############################################################################################################### -->
</div>
</div>



<div style="text-align: center">
<?php
submit_button( __('Save All Options', $this->hook ), 'primary', 'submit', false, array( 'id' => '' ) );
?>
</div>


</form>
</div> <!-- /postbox-container -->
</div><!-- /meta-box-sortables -->



</div><!-- /post-body -->
</div><!-- /poststuff -->


</div><!-- /wrap -->
<div class="clear"></div>
