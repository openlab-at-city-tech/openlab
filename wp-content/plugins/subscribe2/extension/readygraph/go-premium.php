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

	<div style="margin: 3% 5%">
		<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/15.png" style="float: right;margin-left: 10%;" width="310px" height="350px">
		<h3><strong>Effortlessly Increase Your Site's Userbase</strong></h3>
			
			<h4> Scale your audience faster with our Premium Growth Features:</h4>
			<div style="width: 60%;">
			<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/>Your site promoted to 10,000 New Users Every Month in our Community Email Update</h4>
			<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/>Unlimited Viral Email/Facebook Invites</h4>
			<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/>Unlimited Blog Post Notifications</h4>
			<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/>Premium Phone/Email Support</h4>
			<h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/>More Premium Features Added All The Time!</h4>
			<br>
			<a href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', '') ?>" target="_blank"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/go-premium.png" height="40px" style="margin:5px"/></a>
			</div>
			<div style="margin: 65px 0; width: 100%; display: block;">
				<div class="rg-three-column" style="float: left">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/12.png" height="40px" style="margin:15px"/>Explosive Website Growth
				</div>
				<div class="rg-three-column" style="float: left">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/13.png" height="40px" style="margin:15px"/>Develop a Community of Users
				</div>
				<div class="rg-three-column" style="float: left">
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/14.png" height="40px" style="margin:15px"/>Track Growth with Powerful Analytics
				</div>
			</div>
			<p><h4 class="rg-h4">ReadyGraph Premium equips your Wordpress site with the industry's most powerful proven growth features. Websites have used our tools to increase their growth rate by upto 70X.</h4></p>
			<p><h4 class="rg-h4">Don't keep keep your website a secret! Set your growth to "full-blast" with ReadyGraph.</h4>	</p>
			<p><h4 class="rg-h4"><a target="_blank" href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', '') ?>">Start A Free Trial Today!</a> </h4></p>
			
	</div>

</form>
<?php include("footer.php"); ?>