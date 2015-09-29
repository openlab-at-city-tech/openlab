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


	if (!get_option('readygraph_access_token') || strlen(get_option('readygraph_access_token')) <= 0) {
	}
	else {
	if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
	if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
	if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
	if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);
	}
	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		if (isset($_POST["readygraph_monetize"]) && $_POST["readygraph_monetize"] == "1") update_option('readygraph_enable_monetize', "true");
		else update_option('readygraph_enable_monetize', "false");
		if (isset($_POST["readygraph_monetize_email"])) update_option('readygraph_monetize_email', $_POST["readygraph_monetize_email"]);
		s2_readygraph_monetize_update();
	}
?>	

<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">
<input type="hidden" name="readygraph_settings" value="<?php echo htmlentities(str_replace("\\\"", "\"", get_option('readygraph_settings', '{}'))) ?>">
<input type="hidden" name="readygraph_delay" value="<?php echo get_option('readygraph_delay', '5000') ?>">
<input type="hidden" name="readygraph_enable_sidebar" value="<?php echo get_option('readygraph_enable_sidebar', 'false') ?>">
<input type="hidden" name="readygraph_enable_notification" value="<?php echo get_option('readygraph_enable_notification', 'true') ?>">
<input type="hidden" name="readygraph_auto_select_all" value="<?php echo get_option('readygraph_auto_select_all', 'true') ?>">
<input type="hidden" name="readygraph_enable_branding" value="<?php echo get_option('readygraph_enable_branding', 'false') ?>">
<input type="hidden" name="readygraph_send_blog_updates" value="<?php echo get_option('readygraph_send_blog_updates', 'true') ?>">
<input type="hidden" name="readygraph_send_real_time_post_updates" value="<?php echo get_option('readygraph_send_real_time_post_updates', 'false') ?>">
<input type="hidden" name="readygraph_popup_template" value="<?php echo get_option('readygraph_popup_template', 'default-template') ?>">

	<div style="margin: 3% 5%">
		<?php if(get_option('readygraph_enable_monetize') && get_option('readygraph_enable_monetize') == "true") { ?><h3 style="font-weight: normal; text-align: center;"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/check.png"/>Congratulations! <?php echo $s2_main_plugin_title; ?>'s ReadyGraph monetization engine is now active.</h3><?php } ?>
		<h3><strong>Adjust Revenue Settings</strong></h3>
			
			<div style="width: 60%;">
			<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" style="float: left; height: 20px; vertical-align: middle;"/><h5 class="rg-h4" style="margin-left: 30px;text-align:justify; line-height: 20px;">Note: To view your revenue stats, adjust ad placements, and request payment, please click the button below.  This will take you to your dashboard page powered by our monetization partner AdsOptimal. Please contact us <a href="mailto:info@readygraph.com">info@readygraph.com</a> anytime if you have questions.  If you no longer wish to monetize via our non-intrusive highly optimized ad units, you can turn off monetization below.  Remember to save your changes!</h5>
			
			<br>
			<div style="display: block; margin: 10px;"><label for="readygraph_monetize_email">Email:</label><input type="text" name="readygraph_monetize_email" id="readygraph_monetize_email" value="<?php echo get_option('readygraph_monetize_email');?>" style="display: inline; margin: 0 0 0 20px" /></div>
				<p><input type="checkbox" name="readygraph_monetize" value="1" style="margin: 0 10px;" <?php if(get_option('readygraph_enable_monetize') && get_option('readygraph_enable_monetize') == "true") echo "checked"; ?> >Enable Monetization</span>
					<a href="#" class="help-tooltip"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/Help-icon.png" width="15px" style="margin-left:10px;"/><span><img class="callout" src="<?php echo plugin_dir_url( __FILE__ );?>assets/callout_black.gif" /><strong>ReadyGraph Monetization Settings</strong><br />You can check/uncheck this box to enable/disable the monetization settings for ReadyGraph<br /></span></a>
				</p>
				<div class="save-changes">
			<a type="button" class="btn btn-large btn-warning" href="https://www.adsoptimal.com/api/v4/redirect/dashboard?adoid=<?php echo get_option('readygraph_adsoptimal_id', ''); ?>&secret=<?php echo get_option('readygraph_adsoptimal_secret', ''); ?>;" target="_blank" style="margin: 15px">View Revenue Dashboard</a><button type="submit" class="btn btn-large btn-warning save" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=monetization-settings" style="margin: 15px">Save Changes</button>
			</div>
			
			</div>
			
	</div>

</form>
<?php include("footer.php"); ?>