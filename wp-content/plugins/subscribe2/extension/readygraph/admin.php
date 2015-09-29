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
	/*if (!get_option('readygraph_application_id') || strlen(get_option('readygraph_application_id')) <= 0)s2_rg_connect();*/
	if(isset($_GET["tutorial"]) && $_GET["tutorial"] == "true"){update_option('readygraph_tutorial',"true");}
	else{update_option('readygraph_tutorial',"false");}
	if(isset($_GET["readygraph_upgrade_notice"]) && $_GET["readygraph_upgrade_notice"] == "dismiss") {update_option('readygraph_upgrade_notice', 'false');}
	if(isset($_GET["popup_position"]) && $_GET["popup_position"] == "bottom-right"){update_option('readygraph_enable_notification', 'true');update_option('readygraph_enable_popup', 'false');}
	if(isset($_GET["popup_position"]) && $_GET["popup_position"] == "center"){update_option('readygraph_enable_notification', 'true');update_option('readygraph_enable_popup', 'true');}
	if(isset($_GET["popup_position"]) && $_GET["popup_position"] == "disabled"){update_option('readygraph_enable_notification', 'false');update_option('readygraph_enable_popup', 'false');}
	if(isset($_GET["popup_delay"])){update_option('readygraph_delay', intval($_GET["popup_delay"]));}
	if (!get_option('readygraph_plan') || strlen(get_option('readygraph_plan')) <= 0)update_option('readygraph_tutorial',"true");
?>	

<form method="post" id="myForm">
<input type="hidden" name="readygraph_access_token" value="<?php echo get_option('readygraph_access_token', '') ?>">
<input type="hidden" name="readygraph_refresh_token" value="<?php echo get_option('readygraph_refresh_token', '') ?>">
<input type="hidden" name="readygraph_email" value="<?php echo get_option('readygraph_email', '') ?>">
<input type="hidden" name="readygraph_application_id" value="<?php echo get_option('readygraph_application_id', '') ?>">
<input type="hidden" name="readygraph_delay" value="<?php echo get_option('readygraph_delay', '5000') ?>">
<input type="hidden" name="readygraph_enable_notification" value="<?php echo get_option('readygraph_enable_notification', 'true') ?>">
<input type="hidden" name="readygraph_enable_popup" value="<?php echo get_option('readygraph_enable_popup', 'true') ?>">

<div>
<div class="authenticate" style="display: none;">
	<div class="wrap1" style="min-height: 600px;">
		<div id="icon-plugins" class="icon32"></div>
		<h2>We have enhanced <?php echo $s2_main_plugin_title ?> with ReadyGraph's Growth/Revenue Engine</h2>
		<p style="display:none;color:red;" id="error"></p>
		<div class="register-left">
	<div class="alert" style="margin: 0px auto; padding: 20px 15px; text-align: center;">
			<h3>Activate ReadyGraph to get more traffic and revenue</h3>
<!--		<h3 style="margin-top: 0px; font-weight: 300;"><?php //echo $main_plugin_title ?>, Now with ReadyGraph</h3> -->
		<p style="padding: 50px 0px 10px 0px;"><a class="btn btn-primary connect" href="javascript:void(0);" style="font-size: 18px; padding: 20px 25px;">Connect ReadyGraph >></a></p>
		<span><input type="checkbox" id="readygraph_monetize" name="readygraph_monetize" value="1" style="margin: 0 10px;" checked >Enable Monetization</span>
		
		<!--<p style="padding: 0px 0px;"><a class="btn btn-default skip" href="javascript:void(0);" style="font-size: 10px; line-height: 20px; padding: 0 30px;">Skip ReadyGraph</a></p>-->
		<p style="margin-top:50px">Readygraph maximizes your Growth and Revenue</p>
		<p style="text-align: left; padding: 0 20px;">
			- Collect site reviews<br> 
			- Monetize mobile and web traffic with optimized, non-intrusive ad units<br> 
			- Get more traffic<br>
			- Send automatic email digests of all your site posts<br>
			- Get better deliverablility<br>
			- Track performance and user activity<br>
			- Automatically synchs with your current subscriber list<br>
			- Your best content featured to the full UserBase community<br>
		</p>
	</div>
          
      </div>
		<div class="register-right">
			<div class="form-wrap alert" style="font-size:12px;">
			<p><h3>ReadyGraph grows your site</h3></p>
			<p>ReadyGraph delivers audience growth and motivates users to come back.</p><br /><p><span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_currency.png"></span><span style="width: 90%;"><b>Maximize Revenue –</b> Compensate yourself for your hardwork with standardized, non-intrusive ad units. Optimized for mobile and web to maximize revenue. Powered by our high quality partner AdsOptimal.</span></span><br /><br />
			<span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_fb.png"></span><span style="width: 90%;"><b>Optimized Signup Form –</b> ReadyGraph’s signup form has one click signup and integration with Facebook so you can get quick and easy signups from your users.</span></span><br /><br />
			<span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_heart.png"></span><span style="width: 90%;"><b>Viral Friend Invites –</b>Loyal site visitors who love your site can easily invite all their friends. Readygraph encourages your visitors' friends to come and signup for your site too.</span></span><br /><br />
			<span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_mail.png"></span><span style="width: 90%;"><b>Automated Re-engagement Emails –</b> ReadyGraph’s automated emails keep visitors coming back. Send a daily or weekly digest of all your new posts and keep them informed about site activity, events, etc.</span></span><br /><br />
			<span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_chart.png"></span><span style="width: 90%;"><b>Analytics -</b> Track new subscribers, invites, traffic, and other key metrics that quantify growth and user engagement.  ReadyGraph safely stores user data on the cloud so you can access from anywhere.</span></span><br /><br />
			<span style="min-height: 50px;"><span class="rg-signup-icon rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/ub-icon.png" style="padding: 0 10px;"></span><span style="width: 90%;"><b>Your Site Promoted in UserBase Rankings -</b> Users vote on your latest content and top ranked posts are promoted on UserBase.com to thousands of people.</span></span><br /><br />
			If you have questions or concerns contact us anytime at <a href="mailto:info@readygraph.com" target="_blank">info@readygraph.com</a> Feel free to check out our <a href="http://readygraph.com/faq/" target="_blank">FAQ</a> for a more comprehensive overview.  You can also completely <a class="delete" href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&action=<?php echo base64_encode("deleteaccount");?>">Delete ReadyGraph</a> if you don't want access to our amazing growth tools.  Either way, good luck building a massive userbase!<br/><br/>By connecting to ReadyGraph, you agree to our <a href="http://readygraph.com/tos/" target="_blank">Terms of Service</a> and <a href="http://readygraph.com/privacy/" target="_blank">Privacy Policy</a>.
			</p>
			</div>
		</div>
	</div>
</div>
<div class="authenticating" style="display: none;">
	<div style="color: #ffffff; width: 350px; margin: 100px auto 0px; padding: 15px; border: solid 1px #2a388f; text-align: center; background-color: #2961cb; -webkit-border-radius: 7px; -moz-border-radius: 7px; border-radius: 7px;">
		<h3 style="margin-top: 0px; font-weight: 300;"><?php echo $s2_main_plugin_title ?>, Now with ReadyGraph</h3>
		<h4 style="padding: 50px 0; line-height: 42px;">Retrieving Your Account..</h4>
		<p>Activate Readygraph features to optimize <?php echo $s2_main_plugin_title ?> functionality. Signup For These Benefits:</p>
		<p style="text-align: left; padding: 0 20px;">
			- For qualifying sites, monetize traffic with optimized, non-intrusive ad units<br>
			- Grow your subscribers faster<br>
			- Engage users with automated email updates<br>
			- Enhanced email deliverablility<br>
			- Track performace with user-activity analytics<br>
			- Automatically synchs with your current subscriber list<br>
			- Your best content featured to the full UserBase community<br>
		</p>
	</div>
</div>

<div class="authenticated" style="display: none;">

	<?php if(get_option('readygraph_tutorial') && get_option('readygraph_tutorial') == "true"){ ?>
	<div class="tutorial-true" style="margin: 5% auto;">
		<h3 style="font-weight: normal; text-align: center;"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/check.png"/>Congratulations! <?php echo $s2_main_plugin_title; ?>'s ReadyGraph growth engine is now active.</h3>
		
			<div style="width: 45%; margin: 1% 1% 0 10%; float: left">
			<h3 style="font-weight: normal;color: grey;">Step 1: Choose a plan for exposure to more new users!</h3>
			<div class="rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/></div>
			<h4 class="rg-h4">Cross promotion to thousands of users</h4><p class="rg-icon-content">Get promoted through our community emails and your own site SEO page on UserBase.com</p>
			<div class="rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/></div>
			<h4 class="rg-h4">Let users vote up your content</h4><p class="rg-icon-content">Add vote buttons in your site post emails.  Top voted posts featured on UserBase.com</p>
			<div class="rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/></div>
			<h4 class="rg-h4">Content recommendations</h4><p class="rg-icon-content">As a member of our cross promotion network, your users discover valuable content from related sites</p>
			<div class="rg-icon-thumb"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/round-check.png" class="rg-small-icon"/></div>
			<h4 class="rg-h4">Full set of growth tools</h4><p class="rg-icon-content">Optimized signup form, viral invites, site update emails, and more!</p>
			</div>
			<div style="width: 25%; margin: 1% 5% 0 0; float: left; background: #F0F0F0; border-radius: 15px;padding: 1% 2% 1% 1%"><h4 class="rg-h4">Select your plan</h4>
			<div style="margin: 10px;"><div class="rg-icon-thumb"><input type="radio" name="select-plan" value="promote_free" style="font-weight: bold; margin: 12px 0"></div><p class="rg-icon-content"><strong>Free - Stick with the Basic Plan</strong> </input><br><span style="margin-top: -12px">Basic tools, Promotion if content ranks highly</span></p></div>
			<div style="margin: 10px;"><div class="rg-icon-thumb"><input type="radio" name="select-plan" value="promote_39" style="font-weight: bold; margin: 12px 0" checked></div><p class="rg-icon-content"><strong>Get promoted to 2000 users monthly</strong></input><br><span style="margin-top: -12px">$39/month</span></p></div>
			<div style="margin: 10px;"><div class="rg-icon-thumb"><input type="radio" name="select-plan" value="promote_59" style="font-weight: bold; margin: 12px 0"></div><p class="rg-icon-content"><strong>Get promoted to 10,000 users monthly</strong></input><br><span style="margin-top: -12px">$59/month</span></p></div>
			<div style="margin: 10px;"><div class="rg-icon-thumb"><input type="radio" name="select-plan" value="promote_99" style="font-weight: bold; margin: 12px 0"></div><p class="rg-icon-content"><strong>Get promoted to 100,000 users monthly</strong></input><br><span style="margin-top: -12px">$99/month</span></p></div>
			<div style="margin: 10px;"><div class="rg-icon-thumb"><input type="radio" name="select-plan" value="promote_no" style="font-weight: bold; margin: 12px 0"></div><p class="rg-icon-content"><strong>Don't promote my site</strong></input><br><span style="margin-top: -12px">Opt out of cross promotion network</span></p></div>
			<div class="rg-icon-thumb" style="margin: 10px;width:100%"><input type="checkbox" id="plan-type" name="plan-type" value="annual" style="font-weight: bold">&nbsp;&nbsp; Save 20% with an annual plan</input></div>
			<div class="save-changes" style="font-weight: normal; text-align: center;"><a class="btn btn-large btn-warning save-next" href="#" style="margin: 15px" onclick="subscribe_readygraph()">Continue</a><br> 
			</div></div>
	</div>
	<?php } else { ?>
	
	<div class="tutorial-false" style="margin: 2% auto; width: 90%">
		<h3 style="font-weight: normal; text-align: center;">Settings - Make adjustments to grow and engage your userbase</h3>
			<div style="float: left;width: 75%;">
			<div style="display: block;min-height: 250px;">
				<div style="width: 45%; margin: 0 auto; float: left;"><h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/11.png" class="rg-big-icon"/>Email</h4>
				<button type="button" class="btn btn-large btn-warning save-next" onclick="window.open('http://readygraph.com/application/customize/settings/advance/');return false;" style="margin: 15px" formtarget="_blank">Automated Email Settings</button>
				<button type="button" class="btn btn-large btn-warning save-next" onclick="window.open('http://readygraph.com/application/insights/');return false;" style="margin: 15px"formtarget="_blank">Mass Email Users</button>
				<br>
				<a href="https://readygraph.com/application/customize/settings/email/welcome/" target="_blank" style="margin: 15px;color:#093e7d;">Welcome</a>
				<a href="https://readygraph.com/application/customize/settings/email/invitation/" target="_blank" style="margin: 15px;color:#093e7d;">Invite</a>
				<a href="https://readygraph.com/application/customize/settings/email/follow/" target="_blank" style="margin: 15px;color:#093e7d;">Follow</a>
				<a href="https://readygraph.com/application/customize/settings/email/base/" target="_blank" style="margin: 15px;color:#093e7d;">Content Update Digest</a>
				</div>
				<div style="width: 45%; margin: 0 auto; float: right;"><h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/6.png" class="rg-big-icon"/>Analytics</h4>
				<button type="button" class="btn btn-large btn-warning save-next" onclick="window.open('https://readygraph.com/application/insights/');return false;" style="margin: 15px">User Statistics</button>

				</div>
			</div>
			<div style="display: block;min-height: 250px;">
				<div style="width: 45%; margin: 0 auto; float: left;"><h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/7.png" class="rg-big-icon"/>Signup Overlay</h4>
				<p>Signup Popup Activated?
									<select class="signup-popup" name="signup-popup" class="form-control" onchange="return popup_position(this)">
										<option value="yes-center">Yes, in Center</option>
										<option value="yes-bottom-right">Yes, in Bottom Right</option>
										<option value="no">No</option>
									</select></p>
				<p>Signup Popup Delay?&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;    
									<select class="popup-delay" name="popup-delay" class="form-control" onchange="return popup_delay(this)">
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
									</select>
				</div>
				<div style="width: 45%; margin: 0 auto; float: right;"><h4 class="rg-h4"><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/8.png" class="rg-big-icon"/>Help</h4>
				<a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=faq" style="margin: 15px;color:#093e7d;">FAQ</a>
				<br>
				<a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=basic-settings&tutorial=true" style="margin: 15px;color:#093e7d;">Tutorial</a>
				<br>
				<a href="mailto:info@readygraph.com" style="margin: 15px;color:#093e7d;">Contact Us</a>
				<br>
				<a href="<?php $current_url = explode("&", $_SERVER['REQUEST_URI']); echo $current_url[0];?>&ac=deactivate-readygraph" style="margin: 15px;color:#093e7d;">Deactivate ReadyGraph</a>

				</div>
			</div>
			</div>
			<div style="width: 23%; display: block; min-height: 200px; float: right;">
				<div class="readygraph_upgrade_right_sidebar">
					<div style="background: #0B3E7F; padding: 5px; color: #fff; "><h4>ReadyGraph Premium</h4></div>
					<p class="centered-image">All the tools you need to grow your audience.<br><br><a href="https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', '') ?>" target="_blank" style="color: #b1c1ca" ><img src="<?php echo plugin_dir_url( __FILE__ );?>assets/go-premium.png" height="40px" style="margin:5px" /></a></p>
				</div>
				<div class="readygraph_upgrade_right_sidebar" style="margin-top: 10px;">
					<p class="centered-image">
					<em><strong>Top 3 benefits you can get!</strong></em><br>
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/7.png" width="50px" style="margin:5px" /><br>
					1. Promotion to 10,000+ new users/month<br>
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/11.png" width="50px" style="margin:5px" /><br>
					2. Unlimited post update emails<br>
					<img src="<?php echo plugin_dir_url( __FILE__ );?>assets/icon_fb.png" width="50px" style="margin:5px" /><br>
					3. Unlimited Facebook invite referrals<br>
					
					</p>
				</div>
			</div>
	</div>
	<?php } ?>
</div>
</div>
</form>

<script type="text/javascript" charset="utf-8">
var enable_monetize;


function subscribe_readygraph() {
    var radios = document.getElementsByName("select-plan");
	if (document.getElementById('plan-type').checked) {
            annual="true";
        } else {
            annual="false";
        }

    for (var i = 0; i < radios.length; i++) {       
        if (radios[i].checked) {
            plan = radios[i].value;
            break;
        }
    }
	var current_url = document.URL;
	var url_array = document.URL.split( '&' );
	url = 'https://readygraph.com/accounts/payment/?email=<?php echo get_option('readygraph_email', '') ?>&payment_plan='+plan+'&is_annual='+annual+'&redirect_uri='+encodeURIComponent(url_array[0]+'&ac=site-profile');
	current_url = url_array[0]+'&ac=site-profile&readygraph_plan='+plan;
	if (plan === "promote_free"){
	window.location.href = current_url;
	}
	else{
	var win=window.open(url, '_blank');
	window.open(current_url, '_self');
	window.location.href = current_url;
	win.focus();
	}
}
function popup_position(n){
	<?php 	$current_url = explode("&", $_SERVER['REQUEST_URI']); ?>
  if(n.selectedIndex === 0){
  // show a div (id)  // alert(n.value);
	
    window.location.replace("<?php echo $current_url[0].'&popup_position=center';?>");
   }else if(n.selectedIndex === 1){
     window.location.replace("<?php echo $current_url[0].'&popup_position=bottom-right';?>");
   }
    // this last one is not what you ask but for completeness 
    // hide the box div if the first option is selected again
    else if (n.selectedIndex == 2){ // alert(n[1].value);
    window.location.replace("<?php echo $current_url[0].'&popup_position=disabled';?>");
    }
  }
function popup_delay(n){
	<?php 	$current_url = explode("&", $_SERVER['REQUEST_URI']); ?>
    window.location.replace("<?php echo $current_url[0].'&popup_delay=';?>"+n.value);
  }
</script>

<?php include("footer.php"); ?>