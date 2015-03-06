<?php $settings = self::fdx1_get_settings();
/* wrap
*********************************************************************************/
echo '<div class="wrap">'. get_screen_icon('fdx-lock');
echo '<h2>'. $this->pluginname . ' : ' . __('Connect to Twitter', $this->hook) . '</h2>';

/* show a warning
*------------------------------------------------------------*/
if ( $settings['url_type'] == 'bitly' && ( empty( $settings['bitly-api-key'] ) || empty( $settings['bitly-user-name'] ) ) )
{
echo "<div class='error'><p><strong>Bit.ly is selected, but account information is missing.</strong></p></div>";
}
elseif ( $settings['url_type'] == 'yourls' && ( empty($settings['yourls-api-key']) || empty($settings['yourls-user-name'] ) ) )
{
echo "<div class='error'><p><strong>YOURLS is selected, but account information is missing.</strong></p></div>";
}
if ( isset($_POST['fdx1_update_settings']) ) {
echo '<div class="updated fade"><p><strong>' . __( 'Settings updated', $this->hook ) . '.</strong></p></div>';
}
?>
<div id="poststuff">
<div id="post-body" class="metabox-holder columns-2">
<?php include('inc-sidebar.php'); ?>
<div class="postbox-container">
<div class="meta-box-sortables">

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<input type="hidden" name="info_update" id="info_update" value="true" />

<div class="postbox" >
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span><?php _e('Connect to Twitter', $this->hook) ?></span></h3>
<div class="inside">
<!-- ############################################################################################################### -->


<?php if ( !$settings['oauth_access_token'] ) { ?>

<div class="fdx-left-content">
<p><?php _e('uses OAuth authentication to connect to Twitter. Follow the authentication process below to authorise this Plugin to access on your Twitter account. ', $this->hook) ?></p>
</div><!-- left content -->

<div class="fdx-right-content">
<?php $auth_url = self::fdx1_get_auth_url(); ?>
<?php if ( $auth_url ) { ?>
<p><a href="<?php echo $auth_url; ?>" title="<?php _e( 'Sign in with Twitter', $this->hook ); ?>"><img src="<?php echo plugins_url( 'images/sign-in-with-twitter-gray.png', dirname(__FILE__));?>" width="158" height="28" alt=""></a></p>

<?php } else { ?>
<h4><?php _e( 'Not able to validate access to account, Twitter is currently unavailable. Try checking again in a couple of minutes.', $this->hook ); ?></h4>
<?php } ?>


</div><!-- right content -->
<?php } else { ?>
<div class="fdx-left-content">


<h2>Twitter ID = <a href="https://twitter.com/intent/user?user_id=<?php echo $settings['user_id']; ?>" data-width="700" data-height="500" rel="1" id="tuwiterid" class="newWindow"> <?php echo $settings['user_id']; ?></a></h2>

</div><!-- left content -->

<div class="fdx-right-content">
<p><?php _e( 'Your account has  been authorized.', $this->hook ); ?> (<strong><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&fdx1=deauthorize" onclick="return confirm( '<?php _e( 'Are you sure you want to deauthorize your Twitter account?', $this->hook ); ?>');"><?php _e( 'Deauthorize', $this->hook ); ?></a></strong>)</p>
</div><!-- right content -->
<?php } ?>



<div class="clear"></div>
<!-- ############################################################################################################### -->
</div>
</div>

<div class="postbox" >
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span><?php _e('Basic Settings', $this->hook) ?></span></h3>
<div class="inside">
<!-- ############################################################################################################### -->

<div class="fdx-left-content">
<p><?php _e('Shortcodes', $this->hook);?>:</p>

<div style="width: 65px; float: left"><strong><code>[title]</code><br /><code>[link]</code><br /><code>[author]</code><br/><code>[cat]</code><br/><code>[tags]</code></strong></div>
<small><?php _e('The title of your blog post/page', $this->hook) ?>.</small><br/>
<small><?php _e('The post/page URL', $this->hook)?>. </small><br/>
<small><?php _e('Post/page author\'s', $this->hook)?>.</small> <br/>
<small><?php _e('The first category', $this->hook)?>.<sup>(2)</sup></small><br/>
<small><?php _e('Post tags', $this->hook)?>.<sup>(1) (2)</sup></small><br/>
<p style="margin-top: 10px"><small>(1) <?php _e('Modified into hashtags, show only 3 tags of less than 15 characters each, and space replaced by', $this->hook)?>:<code>_</code></small></p>
<p style="margin-top: 10px"><small>(2) <?php _e('Only for:', $this->hook)?> <?php _e( 'Update when a post is published/edited', $this->hook ); ?>.</small></p>


</div><!-- left content -->
<div class="fdx-right-content">
<p><input type="checkbox" class="check" id="tweet_run_1" name="tweet_run_1"<?php if ( $settings['tweet_run_1'] ) echo ' checked'; ?> /> <strong><?php _e( 'Update when a post is published/edited', $this->hook ); ?></strong></p>
<p><?php _e('Text for post updates', $this->hook)?>:<br />
<input type="text" name="message" value="<?php echo( htmlentities( $settings['message'], ENT_COMPAT, "UTF-8" ) ); ?>" class="long" /> </p>

<h3 style="margin-top: 20px; padding: 0"></h3>

<p><input type="checkbox" class="check" id="tweet_run_2" name="tweet_run_2"<?php if ( $settings['tweet_run_2'] ) echo ' checked'; ?> /> <strong><?php _e( 'Update when a page is published/edited', $this->hook ); ?></strong></p>
<p><?php _e('Text for page updates', $this->hook)?>:<br />
<input type="text" name="message2" value="<?php echo( htmlentities( $settings['message2'], ENT_COMPAT, "UTF-8" ) ); ?>" class="long" /> </p>



</div><!-- right content -->
			<div class="clear"></div>
<!-- ############################################################################################################### -->
</div>
</div>

<div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span><?php _e('URL Shortener Account Settings', $this->hook) ?></span></h3>
<div class="inside">
<!-- ############################################################################################################### -->
<div class="fdx-left-content">
<p><?php _e('Long URLs will automatically be shortened using the specified URL shortener.', $this->hook ); ?></p>
</div><!-- left content -->
<div class="fdx-right-content">
<select name="fdx1-url-type" class="long select" id="url_shortener">
<option value="post_id"<?php if ( $settings['url_type'] == 'post_id' ) echo " selected"; ?>><?php _e( "Post ID", "fdx1" ); ?> (<?php echo self::fdx1_post_id_url_base() . '10'; ?>)</option>
<option value="tinyurl"<?php if ( $settings['url_type'] == 'tinyurl' ) echo " selected"; ?>>Tinyurl</option>
<option value="isgd"<?php if ( $settings['url_type'] == 'isgd' ) echo " selected"; ?>>Is.gd</option>
<option value="bitly"<?php if ( $settings['url_type'] == 'bitly' ) echo " selected"; ?>>Bit.ly</option>
<option value="yourls"<?php if ( $settings['url_type'] == 'yourls' ) echo " selected"; ?>>YOURLS</option>
</select>


     			   <div id="select2">
     				<ul>
                    <li>
		     		<input type="text" name="bitly-user-name" id="bitly-user-name" value="<?php if ( isset( $settings['bitly-user-name'] ) ) echo $settings['bitly-user-name']; ?>" />
		     		<label for="bitly-user-name"><?php _e( 'User Name', $this->hook ); ?></label>
	     			</li>
	     			<li>
		     		<input type="text" name="bitly-api-key" id="bitly-api-key" value="<?php if ( isset( $settings['bitly-api-key'] ) ) echo $settings['bitly-api-key']; ?>" />
		     		<label for="bitly-api-key">API key</label>
	     			</li>
                    </ul>
	     			</div>
<div id="select1">
<ul>
<li>
		     		<input type="text" name="yourls-user-name" id="yourls-user-name" value="<?php if ( isset( $settings['yourls-user-name'] ) ) echo $settings['yourls-user-name']; ?>" />
		     		<label for="yourls-user-name">Signature Token</label>
	     			</li>
	     			<li>
		     		<input type="text" name="yourls-api-key" id="yourls-api-key" value="<?php if ( isset( $settings['yourls-api-key'] ) ) echo $settings['yourls-api-key']; ?>" />
		     		<label for="yourls-api-key"><?php _e( 'Full URL path to', $this->hook ); ?> yourls-api.php</label>
	     			</li>
                    </ul>
</div>

				</div><!-- right content -->

                 <div class="clear"></div>

<!-- ############################################################################################################### -->
</div>
</div>


<div class="postbox">
<div class="handlediv" title="<?php _e('Click to toggle', $this->hook) ?>"><br /></div><h3 class='hndle'><span><?php _e('Limit Updating', $this->hook) ?></span></h3>
<div class="inside">
<!-- ############################################################################################################### -->
   <div class="fdx-left-content">
						<p><?php _e( 'The default behaviour is to publish all new posts as Tweets to your Twitter stream', $this->hook ); ?>.</p>
						<p><?php _e( 'Can also be configured to include/exclude entries that have a specific tag, category or Associated Tag/Category', $this->hook ); ?>.</p>
				</div><!-- left content -->

				<div class="fdx-right-content">
                   <p><strong><?php _e('Tags, Categories, Tag/Category', $this->hook ); ?> </strong><small><?php _e('(comma separated)', $this->hook ); ?></small> </p>

							<input type="text" id="fdx1-tags" name="fdx1-tags" value="<?php echo implode( $settings['tags'], ', '); ?>" class="long"/>


						<p><input type="checkbox" class="check" id="fdx1-reverse" name="fdx1-reverse"<?php if ( $settings['reverse'] ) echo ' checked'; ?> />
						<label for="fdx1-reverse"><?php _e( 'Reverses default behavior', $this->hook ); ?> <small><?php _e( '(exclude tags/categories listed above)', $this->hook ); ?></small></label>
</p>
				</div><!-- right content -->
			<div class="clear"></div>


<!-- ############################################################################################################### -->
</div>
</div>

<?php
// buttons
echo '<div class="button_submit">';
echo submit_button( __('Save all options', $this->hook ), 'primary', 'fdx1_update_settings', false, array( 'id' => '' ) ) ;
echo '</div>';

echo '<div class="button_reset">';
echo submit_button( __('Restore Defaults', $this->hook ), 'secondary', 'reset' , false, array( 'id' => 'space', 'onclick' => 'return confirm(\'' . esc_js( __( 'Restore Default Settings?',  $this->hook ) ) . '\');' ) );
echo '</form>';//form 2
echo '</div>';

// meta-box-sortables | postbox-container | post-body | poststuff | wrap
echo '</div></div></div></div></div>';
//----------------------------------------- ?>

