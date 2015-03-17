<?php
/* DEFAULT OPTIONS
*------------------------------------------------------------*/
add_option('wp_twitter_fdx_widget_title', 'Widget Title');
add_option('wp_twitter_fdx_username', ''); //Twitter Widget ID
add_option('wp_twitter_fdx_width', '250');
add_option('wp_twitter_fdx_height', '300');
add_option('wp_twitter_fdx_shell_bg', '0'); //Tweet limit
add_option('wp_twitter_fdx_shell_text', 'light');
add_option('wp_twitter_fdx_tweet_bg', 'noborders'); //Widget Layout
add_option('wp_twitter_fdx_tweet_text', 'FFE959');
add_option('wp_twitter_fdx_links', '4aed05');

add_option('wp_twitter_fdx_search_widget_sidebar_title', 'Sidebar Title');
add_option('wp_twitter_fdx_widget_search_caption', ''); //Widget Layout
add_option('wp_twitter_fdx_search_width', '250');
add_option('wp_twitter_fdx_search_height', '300');
add_option('wp_twitter_fdx_search_shell_bg', '0'); //Tweet limit
add_option('wp_twitter_fdx_search_tweet_bg', 'FFE959');
add_option('wp_twitter_fdx_search_tweet_text', 'dark'); //Theme
add_option('wp_twitter_fdx_search_links', '4aed05');
add_option('wp_twitter_fdx_widget_search_title', ''); // Twitter Widget ID

    if (isset($_POST['info_update']))
    {
		update_option('wp_twitter_fdx_widget_title', stripslashes_deep((string)$_POST["wp_twitter_fdx_widget_title"]));
        update_option('wp_twitter_fdx_username', (string)$_POST["wp_twitter_fdx_username"]);
        update_option('wp_twitter_fdx_height', (string)$_POST['wp_twitter_fdx_height']);
		update_option('wp_twitter_fdx_width', (string)$_POST['wp_twitter_fdx_width']);
		update_option('wp_twitter_fdx_shell_bg', (string)$_POST['wp_twitter_fdx_shell_bg']);
		update_option('wp_twitter_fdx_shell_text', (string)$_POST['wp_twitter_fdx_shell_text']);
		update_option('wp_twitter_fdx_tweet_bg', (string)$_POST['wp_twitter_fdx_tweet_bg']);
		update_option('wp_twitter_fdx_tweet_text', (string)$_POST['wp_twitter_fdx_tweet_text']);
		update_option('wp_twitter_fdx_links', (string)$_POST['wp_twitter_fdx_links']);
		update_option('wp_twitter_fdx_widget_search_title', stripslashes_deep((string)$_POST['wp_twitter_fdx_widget_search_title']));
		update_option('wp_twitter_fdx_widget_search_caption', stripslashes_deep((string)$_POST['wp_twitter_fdx_widget_search_caption']));
        update_option('wp_twitter_fdx_search_height', (string)$_POST['wp_twitter_fdx_search_height']);
		update_option('wp_twitter_fdx_search_width', (string)$_POST['wp_twitter_fdx_search_width']);
		update_option('wp_twitter_fdx_search_shell_bg', (string)$_POST['wp_twitter_fdx_search_shell_bg']);
		update_option('wp_twitter_fdx_search_tweet_bg', (string)$_POST['wp_twitter_fdx_search_tweet_bg']);
		update_option('wp_twitter_fdx_search_tweet_text', (string)$_POST['wp_twitter_fdx_search_tweet_text']);
		update_option('wp_twitter_fdx_search_links', (string)$_POST['wp_twitter_fdx_search_links']);
		update_option('wp_twitter_fdx_search_widget_sidebar_title', (string)$_POST['wp_twitter_fdx_search_widget_sidebar_title']);
        echo '<div class="updated fade"><p><strong>' . __( 'Settings updated', $this->hook) . '.</strong></p></div>';
        } else {};

/* wrap
*********************************************************************************/
echo '<div class="wrap">'. get_screen_icon('fdx-lock');
echo '<h2>'. $this->pluginname . ' : ' . __('Widgets Settings', $this->hook) . '</h2>';
?>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">

<?php include('inc-sidebar.php'); ?>

<div class="postbox-container">
<div class="meta-box-sortables">

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<input type="hidden" name="info_update" id="info_update" value="true" />

<div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon5">&nbsp;</span><?php _e('Twitter Profile Widget Options', $this->hook) ?></h3>
<div class="inside">
<!-- ############################################################################################################### -->

 <div class="fdx-left-content">
<h3><?php echo get_option('wp_twitter_fdx_widget_title'); ?></h3>
<br/>
<a class="twitter-timeline"
width="<?php echo get_option('wp_twitter_fdx_width'); ?>"
height="<?php echo get_option('wp_twitter_fdx_height'); ?>"
data-theme="<?php echo get_option('wp_twitter_fdx_shell_text'); ?>"
data-link-color="#<?php echo get_option('wp_twitter_fdx_links'); ?>"
data-border-color="#<?php echo get_option('wp_twitter_fdx_tweet_text'); ?>"
data-chrome="<?php echo get_option('wp_twitter_fdx_tweet_bg'); ?>"
data-tweet-limit="<?php echo get_option('wp_twitter_fdx_shell_bg'); ?>"
data-widget-id="<?php echo get_option('wp_twitter_fdx_username'); ?>"></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>


</div><!-- left content -->

<div class="fdx-right-content">

 <p><input name="wp_twitter_fdx_widget_title" type="text" size="20" value="<?php echo get_option('wp_twitter_fdx_widget_title'); ?>"> <strong><?php _e('Widget Title', $this->hook) ?></strong></p>

 <hr class="sep">
  <p><input name="wp_twitter_fdx_username" type="text" size="20" value="<?php echo get_option('wp_twitter_fdx_username'); ?>" /> <strong>Twitter Widget ID</strong> <em>(data-widget-id)</em><br/><small><?php _e('You need to go on', $this->hook) ?> <strong><a href="https://twitter.com/settings/widgets/" target="_blank">twitter.com/settings/widgets/</a></strong> <?php _e('create a new widget and get your Twitter widget ID. You get this Twitter Widget ID from the code provided by Twitter when you create this widget', $this->hook) ?>: data-widget-id="<strong>1234567890</strong>"</small></p>
    <p><input name="wp_twitter_fdx_width" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_width'); ?>" /> <strong><?php _e('Widget Width', $this->hook) ?></strong>  </p>
     <p><input name="wp_twitter_fdx_height" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_height'); ?>" /><strong> <?php _e('Widget Height', $this->hook) ?></strong></p>

      <p><input  id="sw-shell-background" name="wp_twitter_fdx_shell_bg" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_shell_bg'); ?>" /> <?php _e('<strong>Tweet limit</strong> <em>(value between 1 and 20, or 0 for all)</em>', $this->hook) ?></p>
        <p><input   id="sw-shell-color" name="wp_twitter_fdx_shell_text" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_shell_text'); ?>" /> <strong><?php _e('Theme', $this->hook) ?>:</strong> (<code>light</code> - <code>dark</code>)</p>
       <p>	<input class="color" id="sw-tweet-text" name="wp_twitter_fdx_tweet_text" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_tweet_text'); ?>" />  <strong><?php _e('Border color', $this->hook) ?></strong></p>
        <p><input class="color" id="sw-tweet-links" name="wp_twitter_fdx_links" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_links'); ?>" /> <strong><?php _e('Link Color', $this->hook) ?></strong></p>
        <p><input  rel="tweet-background" id="sw-tweet-background" name="wp_twitter_fdx_tweet_bg" type="text" size="40" value="<?php echo get_option('wp_twitter_fdx_tweet_bg'); ?>" /> <strong>Widget Layout / Chrome</strong><br/><em><?php _e('Use a space-separated set of the following options:', $this->hook) ?></em> [<a href="https://dev.twitter.com/docs/embedded-timelines" target="_blank">?</a>]<br/><code>noheader nofooter noborders noscrollbar transparent</code></p>




 </div><!-- right content -->
<div class="clear"></div>





<!-- ############################################################################################################### -->
</div>
</div>

<div class="postbox" >
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span class="icon5">&nbsp;</span><?php _e('Twitter Search Widget Options', $this->hook) ?></h3>
<div class="inside">
<!-- ############################################################################################################### -->

 <div class="fdx-left-content">
<h3><?php echo get_option('wp_twitter_fdx_search_widget_sidebar_title'); ?></h3>
<br/>
<a class="twitter-timeline"
width="<?php echo get_option('wp_twitter_fdx_search_width'); ?>"
height="<?php echo get_option('wp_twitter_fdx_search_height'); ?>"
data-theme="<?php echo get_option('wp_twitter_fdx_search_tweet_text'); ?>"
data-link-color="#<?php echo get_option('wp_twitter_fdx_search_links'); ?>"
data-border-color="#<?php echo get_option('wp_twitter_fdx_search_tweet_bg'); ?>"
data-chrome="<?php echo get_option('wp_twitter_fdx_widget_search_caption'); ?>"
data-tweet-limit="<?php echo get_option('wp_twitter_fdx_search_shell_bg'); ?>"
data-widget-id="<?php echo get_option('wp_twitter_fdx_widget_search_title'); ?>"></a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>
</div>

<div class="fdx-right-content">

<p><input name="wp_twitter_fdx_search_widget_sidebar_title" type="text" size="20" value="<?php echo get_option('wp_twitter_fdx_search_widget_sidebar_title'); ?>"> <strong><?php _e('Widget Title', $this->hook) ?></strong></p>
<hr class="sep">
<p><input name="wp_twitter_fdx_widget_search_title" type="text" size="20" value="<?php echo get_option('wp_twitter_fdx_widget_search_title'); ?>"> <strong>Twitter Widget ID</strong> <em>(data-widget-id)</em><br/><small><?php _e('You need to go on', $this->hook) ?> <strong><a href="https://twitter.com/settings/widgets/" target="_blank">twitter.com/settings/widgets/</a></strong> <?php _e('create a new widget and get your Twitter widget ID. You get this Twitter Widget ID from the code provided by Twitter when you create this widget', $this->hook) ?>: data-widget-id="<strong>1234567890</strong>"</small></p>
<p><input name="wp_twitter_fdx_search_width" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_width'); ?>" /> <strong><?php _e('Widget Width', $this->hook) ?></strong></p>
<p><input name="wp_twitter_fdx_search_height" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_height'); ?>" /> <strong><?php _e('Widget Height', $this->hook) ?></strong></p>
<p><input  id="sw-shell-background2" name="wp_twitter_fdx_search_shell_bg" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_shell_bg'); ?>" /> <?php _e('<strong>Tweet limit</strong> <em>(value between 1 and 20, or 0 for all)</em>', $this->hook) ?></p>
<p><input  id="sw-tweet-text2" name="wp_twitter_fdx_search_tweet_text" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_tweet_text'); ?>" /> <strong><?php _e('Theme', $this->hook) ?>:</strong> <em>(<code>light</code> - <code>dark</code>)</em> </p>
<p><input class="color" id="sw-tweet-background2" name="wp_twitter_fdx_search_tweet_bg" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_tweet_bg'); ?>" /> <strong><?php _e('Border Color', $this->hook) ?> </strong></p>
<p><input class="color" id="sw-tweet-links2" name="wp_twitter_fdx_search_links" type="text" size="7" value="<?php echo get_option('wp_twitter_fdx_search_links'); ?>" /> <strong><?php _e('Link Color', $this->hook) ?></strong> </p>
<p><input name="wp_twitter_fdx_widget_search_caption" type="text" size="40" value="<?php echo get_option('wp_twitter_fdx_widget_search_caption'); ?>"> <strong>Widget Layout / Chrome</strong><br/><em><?php _e('Use a space-separated set of the following options:', $this->hook) ?></em> [<a href="https://dev.twitter.com/docs/embedded-timelines" target="_blank">?</a>]<br/><code>noheader nofooter noborders noscrollbar transparent</code></p>
</div><div class="clear"></div><!-- right content -->


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






