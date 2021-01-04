<?php
if( !defined('ABSPATH') ){ exit();}
global $wpdb;
$xyz_twap_message='';
if(isset($_GET['msg']))
	$xyz_twap_message = $_GET['msg'];
if($xyz_twap_message == 1){
	?>
	<div class="system_notice_area_style1" id="system_notice_area">
	Thank you for the suggestion.&nbsp;&nbsp;&nbsp;<span
	id="system_notice_area_dismiss">Dismiss</span>
	</div>
	<?php
	}
else if($xyz_twap_message == 2){
		?>
		<div class="system_notice_area_style0" id="system_notice_area">
		wp_mail not able to process the request.&nbsp;&nbsp;&nbsp;<span
		id="system_notice_area_dismiss">Dismiss</span>
		</div>
		<?php
	}
else if($xyz_twap_message == 3){
	?>
	<div class="system_notice_area_style0" id="system_notice_area">
	Please suggest a feature.&nbsp;&nbsp;&nbsp;<span
	id="system_notice_area_dismiss">Dismiss</span>
	</div>
	<?php
}
if (isset($_POST) && isset($_POST['xyz_send_mail']))
{
	if (! isset( $_REQUEST['_wpnonce'] )|| ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'xyz_twap_suggest_feature_form_nonce' ))
	{
		wp_nonce_ays( 'xyz_twap_suggest_feature_form_nonce' );
		exit();
	}
	if (isset($_POST['xyz_twap_suggested_feature']) && $_POST['xyz_twap_suggested_feature']!='')
	{
		$xyz_twap_feature_content=$_POST['xyz_twap_suggested_feature'];
		$xyz_twap_sender_email = get_option('admin_email');
		$entries0 = $wpdb->get_results( $wpdb->prepare( 'SELECT display_name FROM '.$wpdb->base_prefix.'users WHERE user_email=%s',array($xyz_twap_sender_email)));
		foreach( $entries0 as $entry ) {
			$xyz_twap_admin_username=$entry->display_name;
		}
		$xyz_twap_recv_email='support@xyzscripts.com';
		$xyz_twap_mail_subject="WP TWITTER AUTO PUBLISH - FEATURE SUGGESTION";
		$xyz_twap_headers = array('From: '.$xyz_twap_admin_username.' <'. $xyz_twap_sender_email .'>' ,'Content-Type: text/html; charset=UTF-8');
		$wp_mail_processed=wp_mail( $xyz_twap_recv_email, $xyz_twap_mail_subject, $xyz_twap_feature_content, $xyz_twap_headers );
		if ($wp_mail_processed==true){
		 header("Location:".admin_url('admin.php?page=twitter-auto-publish-suggest-features&msg=1'));exit();}
		else {
			header("Location:".admin_url('admin.php?page=twitter-auto-publish-suggest-features&msg=2'));exit();}
	}
	else {
		header("Location:".admin_url('admin.php?page=twitter-auto-publish-suggest-features&msg=3'));exit();}
}?>
<form method="post" >
<?php wp_nonce_field( 'xyz_twap_suggest_feature_form_nonce' );?>
<h3>Contribute And Get Rewarded</h3>
<span style="color: #1A87B9;font-size:13px;padding-left: 10px;" >* Suggest a feature for this plugin and stand a chance to get a free copy of premium version of this plugin.</span>
<table  class="widefat xyz_twap_widefat_table" style="width:98%;padding-top: 10px;">
<tr><td>
<textarea name="xyz_twap_suggested_feature" id="xyz_twap_suggested_feature" style="width:620px;height:250px !important;"></textarea>
</td></tr>
<tr>
<td><input name="xyz_send_mail" class="submit_twap_new" style="color:#FFFFFF;border-radius:4px; margin-bottom:10px;" type="submit" value="Send Mail To Us">
</td></tr>
</table>
</form>