<?php
	if ( ! defined( 'ABSPATH' ) ) exit;
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, options, and other information that should provide
 * The User Interface to the end user.
 *
 * @package   ReadyGraph
 * @author    dan@readygraph.com
 * @license   GPL-2.0+
 * @link      http://www.readygraph.com
 * @copyright 2014 Your Name or Company Name
 */
include("header.php");

	if(isset($_GET["readygraph_upgrade_notice"]) && $_GET["readygraph_upgrade_notice"] == "dismiss") {update_option('readygraph_upgrade_notice', 'false');}
	if (!get_option('readygraph_access_token') || strlen(get_option('readygraph_access_token')) <= 0) {
	//redirect to main page
	$current_url = explode("&", $_SERVER['REQUEST_URI']); 
	echo '<script>window.location.replace("'.$current_url[0].'");</script>';
	}
	else {
		if(isset($_GET["source"]) && $_GET["source"] == "signup-popup"){
			if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
			if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
			if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
			if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);
			if (isset($_POST["readygraph_settings"])) update_option('readygraph_settings', $_POST["readygraph_settings"]);
			if (isset($_POST["readygraph_delay"])) {
			update_option('readygraph_delay', $_POST["delay"]);
			$app_id = get_option('readygraph_application_id');
			if ($_POST["delay"] >= 20000) wp_remote_get( "http://readygraph.com/api/v1/tracking?event=popup_delay&app_id=$app_id" ); 
			}
			if (isset($_POST["readygraph_enable_notification"])) update_option('readygraph_enable_notification', $_POST["notification"]);	
			if (isset($_POST["readygraph_auto_select_all"])) update_option('readygraph_auto_select_all', $_POST["selectAll"]);
			if (isset($_POST["readygraph_enable_branding"])) update_option('readygraph_enable_branding', $_POST["branding"]);
			if (isset($_POST["readygraph_send_blog_updates"])) update_option('readygraph_send_blog_updates', $_POST["blog_updates"]);
			if (isset($_POST["readygraph_send_real_time_post_updates"])) update_option('readygraph_send_real_time_post_updates', $_POST["real_time_post_update"]);
			if (isset($_POST["readygraph_popup_template"])) update_option('readygraph_popup_template', $_POST["popup_template"]);

		}
		else{
			if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
			if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
			if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
			if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);	
			if (isset($_POST["readygraph_enable_sidebar"])) update_option('readygraph_enable_sidebar', $_POST["sidebar"]);
		}
	}
 ?>	
<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">
<input type="hidden" name="readygraph_enable_sidebar" value="<?php echo get_option('readygraph_enable_sidebar', 'false') ?>">
	<div><a href="#">Engage Users</a> > Social Feed</div>
	<?php if(get_option('readygraph_upgrade_notice') && get_option('readygraph_upgrade_notice') == "true") { ?><div class="upgrade-notice"><div class="aa_close"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&readygraph_upgrade_notice=dismiss"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/dialog_close.png"></a></div>
	<div class="upgrade-notice-text">Want to grow your users even faster? Try <a href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', ''); ?>" target="_blank">ReadyGraph Premium</a> for free.</div>
	</div>
	<?php } ?>
	<div class="social-feed" style="margin: 2% auto; width: 80%">
	
		<h3 style="font-weight: normal; text-align: center;">Enable your social feed sidebar to build a community (Optional)</h3>
		<h4 style="font-weight: normal; text-align: center;">Allow users to discuss your content and message each other through the social sidebar</h4>
			<div style="width: 100%; display: block;min-height: 200px;">
				<div style="width: 65%; margin: 1% auto; float: left;"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/ready_sidebar.png" style="height: 500px;"/>
				</div>
				<div style="width: 30%; margin: 1% auto; float: right;">
				<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/7.png" class="rg-big-icon"/>Social Feed</h4>
				<p>Enable Sidebar: 
					<select class="sidebar" name="sidebar" class="form-control">
						<option value="true">YES</option>
						<option value="false">NO</option>
					</select><a href="#" class="help-tooltip"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/Help-icon.png" width="15px" style="margin-left:10px;"/><span><img class="callout" src="<?php echo plugin_dir_url( __FILE__ );?>assets/callout_black.gif" /><strong>ReadyGraph Social Feed Settings</strong><br />You can add an optional social sidebar to your site that allows users the ability to share and discuss the best content on your site.  For an example, click here.<br /></span></a>
				</p><br />
				<div class="save-changes"><?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-next" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=customize-emails&source=social-feed" style="float: right;margin: 15px">Save Changes & Next</button><?php } ?>
			<button type="submit" class="btn btn-large btn-warning save" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=social-feed" style="float: right;margin: 15px">Save Changes</button>
			<?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-previous" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=signup-popup" style="float: right;margin: 15px">Previous</button><?php } ?>
			</div>
				</div>
				
			</div>
	</div>
</form>
<?php include("footer.php"); ?>