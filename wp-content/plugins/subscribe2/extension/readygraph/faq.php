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
	//echo '<script>window.location.replace("'.$current_url[0].'");</script>';
	}
	else {

	}
?>	

<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">

	<div class="tutorial-true" style="margin: 5% auto;">
		<h3 style="font-weight: normal; text-align: center;">Frequenty Asked Questions</h3>
		
<h4> GENERAL QUESTIONS: </h4>

<b>What is ReadyGraph?</b>

<p>ReadyGraph is a tool that makes it easy for websites to grow and manage their user-base, by allowing/utilizing: <b>user sign-up</b>, through an optional notification tab and an intelligent pop-up, with one-click sign-up and social login options; <b>user friend invitations</b>, through the sign-up pop-up, or a sidebar button; <b>automated emails</b>(optional) that keep visitors coming back, such as welcome messages to greet new users, recent site updates/posts to keep them informed, gentle reminders for inactive users, and a weekly digest of new content; <b>mass emailing</b>, for fast communication to all your subscribers; <b>user-interaction</b>, through an optional comment-feed sidebar; <b>analytic tools</b>, to track new subscribers, daily visits, and other key metrics that quantify your website’s growth and user engagement.</p>

<b>How do I install ReadyGraph?</b>

<p>After installing this plug-in, you can activate the ReadyGraph features by connecting/signing-up for your ReadyGraph account.</p>

<b>How do I uninstall ReadyGraph?</b>

<p>You can deactivate the ReadyGraph features by navigating to the upper-right corner of the “ReadyGraph App” page, clicking the drop-down menu with your email address, and disconnecting your ReadyGraph account.</p>

<b>Can I delay the sign-up pop-up?</b>

<p>Yes, you can delay the pop-up for up to 20 minutes; however, the most effective delay is only a few seconds. That ensures that users are engaged, before showing the pop-up to them. </p>

<b>How do I check my website’s stats?</b>

<p>You can check your website’s stats by clicking the “Insights” button at the upper-right corner of the “ReadyGraph App” page. There, you will find various metrics about your site growth.</p>

<b>Can I use both the pop-up and the form widget?</b>

<p>Yes, you can; they will not conflict with each other.</p>

<b>How do I contact someone for support, or to suggest a feature?</b>

<p>You can contact us at info@readygraph.com. We appreciate all feedback.</p>

<b>I’m having problems with the latest version of the plug-in; can I switch back to an older version?</b>

<p>Yes, just navigate to the “Developers” tab on the wordpress.org plug-in page, and select the version that works for you.</p>

<h4> ACCOUNT QUESTIONS: </h4>

<b>How do I change my account email address?</b>

<p>Contact us as info@readygraph.com.</p>

<b>How do I turn off email notifications from ReadyGraph?</b>

<p>You can turn them off via the account settings page on ReadyGraph.com.</p>

<b>How do I disconnect ReadyGraph from my site?</b>

<p>You can disconnect ReadyGraph from your site by navigating to the upper-right corner of the “ReadyGraph App” page in this plug-in, and clicking the drop-down menu with your email address on it; there will be an option there to disconnect ReadyGraph from your site.</p>

<h4> CUSTOMIZATION QUESTIONS: </h4>

<b>Can I customize the pop-up?</b>

<p>Yes, you can choose a template that matches your site design, from the various templates available.</p>

<b>Can I customize the friend-invite form?</b>

<p>You can customize the text on the friend invite form to something that suits your website.</p>

<b>Can I customize my emails?</b>

Yes, on the right side of the “ReadyGraph App” page, you will find a link to a page where you can Configure/Enable/Disable the various automated emails that you can send via ReadyGraph.</p>

<h4> QUESTIONS ABOUT YOUR SUBSCRIBERS: </h4>

<b>How do I view my subscribers?</b>

<p>Clicking the “Insights” button at the top of the “ReadyGraph App” page of this plug-in will take you to a page where you can view a list of your subscribers.</p>

<b>How do I mail my subscribers?</b>

<p>On the right side of the “ReadyGraph App” page, you will find a link to a page where you can send mass emails to your subscribers.</p>

<b>Can I import a list of existing subscribers?</b>

<p>This is a feature currently under development and is scheduled to be released in our next update.</p>

<b>Can I export a list of my subscribers?</b>

<p>This is a feature currently under development and is scheduled to be released in our next update.</p> 

<b>If I decide to stop using ReadyGraph, do I keep my subscribers?</b>

<p>Yes, contact us at info@readygraph.com for assistance.</p>

<b>Can I send automated emails/newsletters to my subscribers?</b>

<p>On the right side of the “ReadyGraph App” page, you will find a link to a page where you can Enable/Disable/Configure the various automated emails that you can send via ReadyGraph.</p>

<b>Is ReadyGraph necessary in order to use this plug-in?</b>

<p>No, it isn’t; you can use this plug-in without ReadyGraph features enabled, but you would be missing out on added growth opportunities.</p>

If you have questions or concerns, contact us anytime at [info@readygraph.com](mailto:info@readygraph.com)
	</div>
	
</form>
<?php include("footer.php"); ?>