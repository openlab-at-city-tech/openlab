<?php
if( !defined('ABSPATH') ){ exit();}
function wp_twap_admin_notice()
{
	add_thickbox();
	$sharelink_text_array_tw = array
						(
						"I Use WP Twitter Auto Publish  wordpress plugin from @xyzscripts and you should too",
						"WP Twitter Auto Publish  wordpress Plugin from @xyzscripts is awesome",
						"Thanks @xyzscripts for developing such a wonderful Twitter auto publishing wordpress plugin",
						"I was looking for a Twitter publishing plugin like this. Thanks @xyzscripts",
						"Its very easy to use WP Twitter Auto Publish  wordpress Plugin from @xyzscripts",
						"I installed WP Twitter Auto Publish from @xyzscripts, it works flawlessly",
						"The WP Twitter Auto Publish wordpress plugin that i use works terrific", 
						"I am using WP Twittter Auto Publish wordpress plugin from @xyzscripts and I like it",
						"The WP Twitter Auto Publish plugin from @xyzscripts is simple and works fine",
						"I've been using this Twitter plugin for a while now and it is really good",
						"WP Twitter Auto Publish wordpress plugin is a fantastic plugin",
						"WP Twitter Auto Publish wordpress plugin is easy to use and works great. Thank you!",
						"Good and flexible WP Twitter Auto publish plugin especially for beginners",
						"The best Twittter auto publish wordpress plugin I have used ! THANKS @xyzscripts",
						);
$sharelink_text_tw = array_rand($sharelink_text_array_tw, 1);
$sharelink_text_tw = $sharelink_text_array_tw[$sharelink_text_tw];
$xyz_twap_link = admin_url('admin.php?page=twitter-auto-publish-settings&twap_blink=en');
$xyz_twap_link = wp_nonce_url($xyz_twap_link,'twap-blk');
$xyz_twap_notice = admin_url('admin.php?page=twitter-auto-publish-settings&twap_notice=hide');
$xyz_twap_notice = wp_nonce_url($xyz_twap_notice,'twap-shw');
	echo '
	<script type="text/javascript">
			function xyz_twap_shareon_tckbox(){
			tb_show("Share on","#TB_inline?width=500&amp;height=75&amp;inlineId=show_share_icons_tw&class=thickbox");
		}
	</script>
	<div id="twap_notice_td" class="error" style="color: #666666;margin-left: 2px; padding: 5px;line-height:16px;">
	<p>Thank you for using <a href="https://wordpress.org/plugins/twitter-auto-publish/" target="_blank">WP Twitter Auto Publish </a> plugin from <a href="https://xyzscripts.com/" target="_blank">xyzscripts.com</a>. Would you consider supporting us with the continued development of the plugin using any of the below methods?</p>
	<p>
	<a href="https://wordpress.org/support/plugin/twitter-auto-publish/reviews" class="button xyz_rate_btn" target="_blank">Rate it 5★\'s on wordpress</a>';
	if(get_option('xyz_credit_link')=="0")
		echo '<a href="'.$xyz_twap_link.'" class="button xyz_backlink_btn xyz_blink">Enable Backlink</a>';
	
	echo '<a class="button xyz_share_btn" onclick=xyz_twap_shareon_tckbox();>Share on</a>
		<a href="https://xyzscripts.com/donate/5" class="button xyz_donate_btn" target="_blank">Donate</a>
	
	<a href="'.$xyz_twap_notice.'" class="button xyz_show_btn">Don\'t Show This Again</a>
	</p>
	
	<div id="show_share_icons_tw" style="display: none;">
	<a class="button" style="background-color:#3b5998;color:white;margin-right:4px;margin-left:100px;margin-top: 25px;" href="http://www.facebook.com/sharer/sharer.php?u=https://xyzscripts.com/wordpress-plugins/twitter-auto-publish/" target="_blank">Facebook</a>
	<a class="button" style="background-color:#00aced;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="http://twitter.com/share?url=https://xyzscripts.com/wordpress-plugins/twitter-auto-publish/&text='.$sharelink_text_tw.'" target="_blank">Twitter</a>
	<a class="button" style="background-color:#007bb6;color:white;margin-right:4px;margin-left:20px;margin-top: 25px;" href="http://www.linkedin.com/shareArticle?mini=true&url=https://xyzscripts.com/wordpress-plugins/twitter-auto-publish/" target="_blank">LinkedIn</a>
	</div>
	
	
	
	</div>';
	
	
}
$twap_installed_date = get_option('twap_installed_date');
if ($twap_installed_date=="") {
	$twap_installed_date = time();
}
if($twap_installed_date < ( time() - (30*24*60*60) ))
{
	if (get_option('xyz_twap_dnt_shw_notice') != "hide")
	{
		add_action('admin_notices', 'wp_twap_admin_notice');
	}
}
?>