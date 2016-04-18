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
	$current_url = explode("&", $_SERVER['REQUEST_URI']); 
	echo '<script>window.location.replace("'.$current_url[0].'");</script>';
	}
	else {
		if(isset($_GET["source"]) && $_GET["source"] == "basic-settings"){
		
		}
		else{
			if (isset($_POST["readygraph_access_token"])) update_option('readygraph_access_token', $_POST["readygraph_access_token"]);
			if (isset($_POST["readygraph_refresh_token"])) update_option('readygraph_refresh_token', $_POST["readygraph_refresh_token"]);
			if (isset($_POST["readygraph_email"])) update_option('readygraph_email', $_POST["readygraph_email"]);
			if (isset($_POST["readygraph_application_id"])) update_option('readygraph_application_id', $_POST["readygraph_application_id"]);
			
			if (isset($_POST["sitedesceditor"])) update_option('readygraph_site_description', $_POST["sitedesceditor"]);
			if (isset($_POST["site_profile_name"])) update_option('readygraph_site_name', $_POST["site_profile_name"]);
			if (isset($_POST["site_profile_url"])) update_option('readygraph_site_url', $_POST["site_profile_url"]);
			if (isset($_POST["site_category"])) update_option('readygraph_site_category', $_POST["site_category"]);
			if (isset($_POST["site_language"])) {update_option('readygraph_site_language', $_POST["site_language"]);s2_siteprofile_sync();}

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

	}
	if (get_option('readygraph_enable_branding', '') == 'false') {
	?>
<style>
/* FOR INLINE WIDGET */
.rgw-text {
    display: none !important;
}
</style>
<?php } ?>	

<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">
<input type="hidden" name="readygraph_settings" value="<?php echo htmlentities(str_replace("\\\"", "\"", get_option('readygraph_settings', '{}'))) ?>">
<input type="hidden" name="readygraph_delay" value="<?php echo get_option('readygraph_delay', '5000') ?>">
<input type="hidden" name="readygraph_enable_notification" value="<?php echo get_option('readygraph_enable_notification', 'true') ?>">
<input type="hidden" name="readygraph_auto_select_all" value="<?php echo get_option('readygraph_auto_select_all', 'true') ?>">
<input type="hidden" name="readygraph_enable_branding" value="<?php echo get_option('readygraph_enable_branding', 'false') ?>">
<input type="hidden" name="readygraph_send_blog_updates" value="<?php echo get_option('readygraph_send_blog_updates', 'true') ?>">
<input type="hidden" name="readygraph_send_real_time_post_updates" value="<?php echo get_option('readygraph_send_real_time_post_updates', 'false') ?>">
<input type="hidden" name="readygraph_popup_template" value="<?php echo get_option('readygraph_popup_template', 'default-template') ?>">
	<div><div><a href="#">Grow Users</a> > Signup Popup</div>
	<?php if(get_option('readygraph_upgrade_notice') && get_option('readygraph_upgrade_notice') == "true") { ?><div class="upgrade-notice"><div class="aa_close"><a href="<?php echo $_SERVER['REQUEST_URI']; ?>&readygraph_upgrade_notice=dismiss"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/dialog_close.png"></a></div>
	<div class="upgrade-notice-text">Want to grow your users even faster? Try <a href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', ''); ?>" target="_blank">ReadyGraph Premium</a> for free.</div>
	</div>
	<?php } ?>
	<h3 style="font-weight: normal; text-align: center;">Increase signups with the Intelligent Signup Popup</h3>
	<h4 style="font-weight: normal; text-align: center;">Users instantly added to your list - One Click Signup - Automatically targets engaged users</h4>
	<div style="width: 90%; margin: 0 auto;">
	<div class="rg-vertical-tab-body-container" style="width: 50%; text-align: center;float: left">
			<div class="btn-group" data-toggle="buttons" style="padding: 20px 0;">
				<label class="btn btn-primary active rg-vertical-tab" tab="LOGIN_REQUIRE">
					<input type="radio" name="options" id="option1"> Facebook Connect
				</label>
				<label class="btn btn-primary rg-vertical-tab" tab="LOGIN_WITH_EMAIL">
					<input type="radio" name="options" id="option2"> Email Sign In
				</label>
				<label class="btn btn-primary rg-vertical-tab" tab="IMPORT_WITH_EMAIL">
					<input type="radio" name="options" id="option3"> Contact Importer
				</label>
				<label class="btn btn-primary rg-vertical-tab" tab="DEFAULT">
					<input type="radio" name="options" id="option4"> Invitation Page
				</label>
				<a href="#" class="help-tooltip"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/Help-icon.png" width="15px" style="margin-left:10px;"/><span><img class="callout" src="<?php echo plugin_dir_url( __FILE__ );?>assets/callout_black.gif" /><strong>ReadyGraph Plugin Settings</strong><br />You can directly edit the text in the orange box below.<br /></span></a>
			</div>
			<div class="rg-preview-widget" style=""></div>
	</div>
	<div style="width:50%; border-left: solid 1px #cccccc; text-align: left;padding-left: 25px;float:right">
		<div style="padding: 20px 0;">
			<p>Popup Templates: 
			<select class="popup_template" name="popup_template" class="form-control">
				<option value="default-template">Default Template</option>
				<option value="red-template">Red Template</option>
				<option value="blue-template">Blue Template</option>
				<option value="black-template">Black Template</option>
				<option value="gray-template">Gray Template</option>
				<option value="green-template">Green Template</option>
				<option value="yellow-template">Yellow Template</option>
				<option value="custom-template">Custom Template</option>
			</select><a href="#" class="help-tooltip"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/Help-icon.png" width="15px" style="margin-left:10px;"/><span><img class="callout" src="<?php echo plugin_dir_url( __FILE__ );?>assets/callout_black.gif" /><strong>Templates</strong><br />For custom colors, select custom-template and change your colors in [plugin_name]/extension/readygraph/assets/css/custom-popup.css.<br />You can do a lot more with CSS.</span></a></p><br />
			<p>Signup Popup Delay: 
			<select class="delay" name="delay" class="form-control">
				<option value="0">0 seconds</option>
				<option value="5000">5 seconds</option>
				<option value="10000">10 seconds</option>
				<option value="15000">15 seconds</option>
				<option value="20000">20 seconds</option>
				<option value="30000">30 seconds</option>
				<option value="60000">1 minute</option>
				<option value="120000">2 minutes</option>
				<option value="180000">3 minutes</option>
				<option value="240000">4 minutes</option>
				<option value="300000">5 minutes</option>
				<option value="600000">10 minutes</option>
				<option value="900000">15 minutes</option>
				<option value="1200000">20 minutes</option>
			</select><a href="#" class="help-tooltip"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/Help-icon.png" width="15px" style="margin-left:10px;"/><span><img class="callout" src="<?php echo plugin_dir_url( __FILE__ );?>assets/callout_black.gif" /><strong>ReadyGraph Popup Settings</strong><br />ReadyGraph's intelligent registration popup maximizes signups to your list.  You can adjust it so that it displays to users after a preset time.  Shorter times will yield more signups. <br /></span></a></p><br />

			<p>Enable Lower Right Notification: 
			<select class="notification" name="notification" class="form-control">
				<option value="true">YES</option>
				<option value="false">NO</option>
			</select></p><br />
			<p>Pre-checked Invite Contact: 
			<select class="selectAll" name="selectAll" class="form-control">
				<option value="true">YES</option>
				<option value="false">NO</option>
			</select></p><br />
			<p>Show Powered by Readygraph on popup: 
			<select class="branding" name="branding" class="form-control">
				<option value="true">YES</option>
				<option value="false">NO</option>
			</select></p><br />
			<p>Include blog updates in daily/weekly email digest of Readygraph: 
			<select class="blog_updates" name="blog_updates" class="form-control">
				<option value="true">YES</option>
				<option value="false">NO</option>
			</select></p><br />
			<p>Send Real Time Post Updates to your subscribers: 
			<select class="real_time_post_update" name="real_time_post_update" class="form-control">
				<option value="true">YES</option>
				<option value="false">NO</option>
			</select></p><br />


			<p>If you have questions or concerns contact us anytime at <a href="mailto:info@readygraph.com" target="_blank">info@readygraph.com</a></p><br />
		</div>
		<div class="save-changes"><?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-next" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=social-feed&source=signup-popup" style="float: right;margin: 15px">Save Changes & Next</button> <?php } ?>
		<button type="submit" class="btn btn-large btn-warning save" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=signup-popup" style="float: right;margin: 15px">Save Changes</button>
		<?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?><button type="submit" class="btn btn-large btn-warning save-previous" formaction="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=site-profile" style="float: right;margin: 15px">Previous</button> <?php } ?>
		</div>
	</div>
	</div>
	</div>

</form>
<?php include("footer.php"); ?>