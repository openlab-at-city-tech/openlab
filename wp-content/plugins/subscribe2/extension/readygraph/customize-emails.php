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
		if(isset($_GET["source"]) && $_GET["source"] == "social-feed"){
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
	<div class="tutorial-true" style="margin: 5% auto;">
		<h3 style="font-weight: normal; text-align: center;">Next Step: Customize automated emails to engage your userbase</h3>
		<h4 style="font-weight: normal; text-align: center;">Head over to ReadyGraph.com to customize emails such as:</h4>
		
			<div style="width: 275px; margin: 0 auto;"><ol><li>Welcome Email</li>
			<li>Friend Invite Email</li>
			<li>Updates of new content you post</li>
			<li>Several More</li></ol>
			
			<div class="save-changes" style="font-weight: normal; text-align: center;"><button type="button" class="btn btn-large btn-warning save-next" onclick="window.open('https://readygraph.com/application/customize/settings/advance/');return false;" style="margin: 15px">Customize Email</button><br>
			<a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=basic-settings" style="margin: 15px">Skip, End Tutorial</a>
			</div></div>
	</div>

</form>
<?php include("footer.php"); ?>