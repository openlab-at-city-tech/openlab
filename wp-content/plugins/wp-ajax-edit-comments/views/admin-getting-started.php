<?php 
/* Admin Panel Support Code - Created on May 23, 2010 by Ronald Huereca 
Last modified on May 23, 2010
todo - internationalization
*/

global $wpdb,$aecomments, $user_email;
if ( !is_a( $aecomments, 'WPrapAjaxEditComments' ) && !current_user_can( 'administrator' ) ) 
	die('');
 //global settings
function pb_aec_get_feed( $feed, $limit, $append = '', $replace = '', $cache_time = 300 ) {
	require_once(ABSPATH.WPINC.'/feed.php');  
	$rss = fetch_feed( $feed );
	if (!is_wp_error( $rss ) ) {
		$maxitems = $rss->get_item_quantity( $limit ); // Limit 
		$rss_items = $rss->get_items(0, $maxitems); 
		
		echo '<ul class="pluginbuddy-nodecor">';

		$feed_html = get_transient( md5( $feed ) );
		if ( $feed_html == '' ) {
			foreach ( (array) $rss_items as $item ) {
				$feed_html .= '<li>- <a href="' . $item->get_permalink() . '">';
				$title =  $item->get_title(); //, ENT_NOQUOTES, 'UTF-8');
				if ( $replace != '' ) {
					$title = str_replace( $replace, '', $title );
				}
				if ( strlen( $title ) < 30 ) {
					$feed_html .= $title;
				} else {
					$feed_html .= substr( $title, 0, 32 ) . ' ...';
				}
				$feed_html .= '</a></li>';
			}
			set_transient( md5( $feed ), $feed_html, $cache_time ); // expires in 300secs aka 5min
		}
		echo $feed_html;
		
		echo $append;
		echo '</ul>';
	} else {
		echo 'Temporarily unable to load feed...';
	}
}			
?>
<?php

// Needed for fancy boxes...
wp_enqueue_style('dashboard');
wp_print_styles('dashboard');
wp_enqueue_script('dashboard');
wp_print_scripts('dashboard');

// If they clicked the button to reset plugin defaults...
if ( !empty( $_POST['reset_defaults'] ) ) {
	$aecomments->reset_admin_options();
	?>
	<div class='updated'><p><strong>Options have been reset.</strong></p></div>
	<?php
}
?>

<div class="wrap">
	<div class="postbox-container" style="width:70%;">
		<h2><?php printf( __( 'Getting Started with Ajax Edit Comments v%s', 'LION' ), $aecomments->get_version() ); ?></h2>
		<br />
		
		Ajax Edit Comments is a commercial WordPress plugin that allows users to edit their comments for a period of time. Administrators have a lot more features, such as the ability to edit comments directly on a post or page. 		
		
		
		<br /><br />
		
		<br />
		
		<center>
			<div style="float: left; background: #D4E4EE; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding: 15px; text-align: center; width: 300px; line-height: 1.6em;">
				<a title="Click to visit the PluginBuddy Support Forum" href="http://ithemes.com/support/ajax-edit-comments/" style="text-decoration: none; color: #000000;">
					For technical support visit our<br />
					<span style="font-size: 2.8em;">Support Forum</span>
					<br />Support &middot; Questions &middot;  Help
				</a>
			</div>
			
			<div style="float: right; background: #D4E4EE; -webkit-border-radius: 8px; -moz-border-radius: 8px; border-radius: 8px; padding: 15px; text-align: center; width: 395px; line-height: 1.6em;">
				<a title="Click to visit the PluginBuddy Knowledge Base" href="http://ithemes.com/codex/page/Ajax-Edit-Comments" style="text-decoration: none; color: #000000;">
					<img src="http://pluginbuddy.com/wp-content/uploads/2011/03/kb.png" alt="" width="70" height="70" style="float: right;" />For documentation &#038; help visit our<br />
					<span style="font-size: 2.8em;">Knowledge Base</span>
					<br />Walkthroughs &middot; Tutorials &middot;  Technical Details
				</a>
			</div>
		</center>
		
		<br style="clear: both;" />
		
		
		<br />
		<h3>Version History</h3>
		<textarea rows="7" cols="70"><?php readfile( $aecomments->get_plugin_dir( '/history.txt' ) ); ?></textarea>
		<br /><br />
		<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#pluginbuddy_debugtoggle").click(function() {
					jQuery("#pluginbuddy_debugtoggle_div").slideToggle();
				});
			});
		</script>
		
		<a id="pluginbuddy_debugtoggle" class="button secondary-button">Debugging Information</a>
		<div id="pluginbuddy_debugtoggle_div" style="display: none;">
			<h3>Debugging Information</h3>
			<?php
			echo '<textarea rows="7" cols="65">';
			echo 'Plugin Version = Ajax Edit Comments '. $aecomments->get_version() .' (ajax-edit-comments)'."\n";
			echo 'WordPress Version = '.get_bloginfo("version")."\n";
			echo 'PHP Version = '.phpversion()."\n";
			global $wpdb;
			echo 'DB Version = '.$wpdb->db_version()."\n";
			echo "\n". '<pre>' . print_r( $aecomments->get_all_admin_options(), true ) . '</pre>';
			echo '</textarea>';
			?>
			<p>
			<form method="post" action="<?php echo esc_attr( $_SERVER["REQUEST_URI"] ); ?>">
				<input type="hidden" name="reset_defaults" value="true" />
				<input type="submit" name="submit" value="Reset Plugin Settings & Defaults" id="reset_defaults" class="button secondary-button" onclick="if ( !confirm('WARNING: This will reset all settings associated with this plugin to their defaults. Are you sure you want to do this?') ) { return false; }" />
			</form>
			</p>
		</div>
		<br /><br /><br />
		<a href="http://pluginbuddy.com" style="text-decoration: none;"><img src="<?php echo $aecomments->get_plugin_url( '/images/pluginbuddy.png' ); ?>" style="vertical-align: -3px;" /> PluginBuddy.com</a><br /><br />
	</div>
	<div class="postbox-container" style="width:20%; margin-top: 35px; margin-left: 15px;">
		<div class="metabox-holder">	
			<div class="meta-box-sortables">
				
				<div id="breadcrumbslike" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Things to do...</span></h3>
					<div class="inside">
						<ul class="pluginbuddy-nodecor">
							<li>- <a href="http://twitter.com/home?status=<?php echo urlencode('Check out this awesome plugin, Ajax Edit Comments!  http://ithem.es/aec @pluginbuddy'); ?>" title="Share on Twitter" onClick="window.open(jQuery(this).attr('href'),'ithemes_popup','toolbar=0,status=0,width=820,height=500,scrollbars=1'); return false;">Tweet about this plugin.</a></li>
							<li>- <a href="http://pluginbuddy.com/purchase/">Check out PluginBuddy plugins.</a></li>
							<li>- <a href="http://pluginbuddy.com/purchase/">Check out iThemes themes.</a></li>
							<li>- <a href="http://secure.hostgator.com/cgi-bin/affiliates/clickthru.cgi?id=ithemes">Get HostGator web hosting.</a></li>
						</ul>
					</div>
				</div>

				<div id="breadcrumsnews" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Latest news from PluginBuddy</span></h3>
					<div class="inside">
						<p style="font-weight: bold;">PluginBuddy.com</p>
						<?php pb_aec_get_feed( 'http://pluginbuddy.com/feed/', 5 );  ?>
						<p style="font-weight: bold;">Twitter @pluginbuddy</p>
						<?php
						$twit_append = '<li>&nbsp;</li>';
						$twit_append .= '<li><img src="'. $aecomments->get_plugin_url( '/images/twitter.png' ) . '" style="vertical-align: -3px;" /> <a href="http://twitter.com/pluginbuddy/">Follow @pluginbuddy on Twitter.</a></li>';
						$twit_append .= '<li><img src="'. $aecomments->get_plugin_url( '/images/facebook.png' ) . '" style="vertical-align: -3px;" /> <a href="http://facebook.com/pluginbuddy/">Like PluginBuddy on Facebook.</a></li>';
						$twit_append .= '<li><img src="'. $aecomments->get_plugin_url( '/images/feed.png' ) . '" style="vertical-align: -3px;" /> <a href="http://pluginbuddy.com/feed/">Subscribe to RSS news feed.</a></li>';
						$twit_append .= '<li><img src="'. $aecomments->get_plugin_url( '/images/email.png' ) . '" style="vertical-align: -3px;" /> <a href="http://pluginbuddy.com/subscribe/">Subscribe to Email Newsletter.</a></li>';
						pb_aec_get_feed( 'http://twitter.com/statuses/user_timeline/108700480.rss', 5, $twit_append, 'pluginbuddy: ' );
						?>
					</div>
				</div>
				
				<div id="breadcrumbssupport" class="postbox">
					<div class="handlediv" title="Click to toggle"><br /></div>
					<h3 class="hndle"><span>Need support?</span></h3>
					<div class="inside">
						<p>See our <a href="http://pluginbuddy.com/purchase/ajax-edit-comments/">tutorials & videos</a> or visit our <a href="http://ithemes.com/support/ajax-edit-comments">support forum</a> for additional information and help.</p>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>