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
	//redirect to main page
	$current_url = explode("&", $_SERVER['REQUEST_URI']); 
	echo '<script>window.location.replace("'.$current_url[0].'");</script>';
	}
	else {
	if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
	if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
	if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
	if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);
	if (isset($_POST["readygraph_settings"])) update_option('readygraph_settings', $_POST["readygraph_settings"]);
	if (isset($_POST["retentionemaileditor"])) update_option('readygraph_invite_email', $_POST["retentionemaileditor"]);
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

	<div style="margin: 0 5%">
		<h2>Deactivate ReadyGraph</h2>
			<h3> Uh oh! Are you sure you wish to deactivate ReadyGraph?</h3>
			<p> ReadyGraph is an extremely effective way to unlock the growth and engagement of your userbase. Here are a few alternatives you may want to consider:</p>
			<p><ol><li> Delay the popup longer to target more engaged users</li>
			<li>Use the smaller overlay in the bottom right instead of the center of your website</li>
			<li>Customize the look and feel of the popup</li>
			<li>Learn more in our FAQ or Tutorial</li>
			<li>Contact us with questions, clarifications or requests</li>			
			</ol></p>
			
			<h3>I'm sure. What do I do now?</h3>
			<p>If you are sure you want to deactivate Readygraph, you have two options: </p>
			<p>Deactivate. This turns ReadyGraph off, but you still have the abiity to reactivate Readygraph in the future should change your mind. <br><a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&action=<?php echo base64_encode("changeaccount");?>">Deactivate ReadyGraph</a></p>
			<p>Delete. This will permanently removes all code and files associated with ReadyGraph from your website.<br><a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&action=<?php echo base64_encode("deleteaccount");?>">Permanently Delete ReadyGraph</a></p>
			<p>&nbsp;</p>
			<p>Thanks a lot and good luck with your site!</p>
			<p>&nbsp;</p>
			<p><a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>">Nevermind, I want to keep growing my audience with ReadyGraph</a></p>
	</div>
</form>
<?php include("footer.php"); ?>