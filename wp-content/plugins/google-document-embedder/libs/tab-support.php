<?php

	/*
	 * Support tab content
	 */
	 
	if ( ! defined( 'ABSPATH' ) ) {
		exit;
	}
	
	global $pdata, $gde_ver, $wp_version, $gdeoptions, $current_user;
	
	get_currentuserinfo();
	if ( isset( $current_user->user_identity ) ) {
		$name = $current_user->user_identity;
	} else {
		$name = '';
	}
	if ( isset( $current_user->user_email ) ) {
		$email = $current_user->user_email;
	} else {
		$email = '';
	}
?>

<div class="gde-support-warn">
	<p><strong><?php _e('Most support questions have already been answered. Please review these pages before asking for support:', 'gde'); ?></strong></p>
	<ul style="list-style-type:square; padding-left:25px;line-height:1em;">
		<li><a href="<?php echo $pdata['PluginURI']; ?>/notes/">Google Doc Embedder <?php _e('Help', 'gde'); ?></a></li>
		<li><a href="<?php echo GDE_WP_URL; ?>faq/"><?php _e('Plugin FAQ', 'gde'); ?></a></li>
	</ul>
</div>
<br clear="both" />

<form action="<?php echo GDE_PLUGIN_URL;?>libs/lib-formsubmit.php" id="debugForm">

<h3><?php _e('Support Request', 'gde'); ?></h3>
<p><?php _e("Requests sent from this form are handled by an actual human, so please don't send test messages or other spam.", 'gde'); ?></p>

<table class="form-table">
<tr valign="top">
	<th scope="row"><label for="sender_name" id="name_label"><?php _e('Your Name', 'gde'); ?></label></th>
	<td><input size="25" name="name" id="sender_name" value="<?php echo $name; ?>" type="text"></td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sender" id="sender_label"><?php _e('Your E-mail', 'gde'); ?>*</label></th>
	<td>
		<input size="25" name="email" id="sender" value="<?php echo $email; ?>" type="text">
		<div id="err_email" class="err" style="color:red;font-weight:bold;display:none;"><?php _e('A valid email address is required.', 'gde'); ?></div>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="sc" id="sc_label"><?php _e('Shortcode', 'gde'); ?></label></th>
	<td>
		<input size="50" name="shortcode" id="sc" value="" type="text" placeholder="[gview file=&quot;...&quot;]"><br/>
		<em><?php _e("If you're having a problem getting a specific document to work, paste the shortcode you're trying to use here.", 'gde'); ?></em>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="url" id="url_label"><?php _e('URL', 'gde'); ?></label></th>
	<td>
		<input size="50" name="url" id="url" value="" type="text" placeholder="http://..."><br/>
		<em><?php _e("Paste the full web address of a page where I should be able to see the problem occurring.", 'gde'); ?></em>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><label for="msg" id="msg_label"><?php _e('Message', 'gde'); ?>*</label></th>
	<td>
		<textarea name="message" id="msg" style="width:75%;min-height:50px;"></textarea>
	</td>
</tr>
<tr valign="top">
	<th scope="row"><?php _e('Message Options', 'gde'); ?></th>
	<td>
		<input type="checkbox" name="senddb" id="senddb" checked="checked"> <label for="senddb" id="senddb_label"><?php _e('Send debug information', 'gde'); ?></label>
		<span id="dbinfo-show">(<a href="javascript:void(0);" id="ta_toggleon"><?php _e('View', 'gde'); ?></a>)<br/></span>
		<span id="dbinfo-hide" style="display: none;">(<a href="javascript:void(0);" id="ta_toggleoff"><?php _e('Hide', 'gde'); ?></a>)<br/></span>
		<input type="checkbox" name="cc" id="cc"> <label for="cc" id="cc_label"><?php _e('Send me a copy', 'gde'); ?></label>
	</td>
</tr>
<tr>
	<td colspan="2">
	<div id="debugblock" style="display:none;">
	<p><strong><?php _e('Debug Information', 'gde'); ?>:</strong><br/>
	<?php _e('Note: Profile and settings export and diagnostic log (if present) will be attached.', 'gde'); ?></p>
	<textarea name="debug" id="debugtxt" style="width:100%;min-height:200px;font-family:monospace;" readonly="readonly">
<?php

	echo "=== GDE Debug Information ===\n\n";
	
	echo "GDE Version: $gde_ver / GDE DB: " . get_site_option( 'gde_db_version', 0 ) . "\n";
	echo "Profiles: " . gde_debug_tables( 'gde_profiles', true ) . "\n";
	echo "Secure Docs: " . gde_debug_tables( 'gde_secure', true );
	
	echo "\n\n--- Env ---\n";
	echo "WordPress Version: $wp_version [".get_locale()."]\n";
	echo "Multisite: ";
	if ( is_multisite() ) {
		echo "Yes ";
		if ( is_plugin_active_for_network( plugin_basename( __FILE__ ) ) ) {
			echo "(network activated)\n";
		} else {
			echo "(not network activated)\n";
		}
	} else {
		echo "No\n";
	}
	echo "PHP Version: ".phpversion()."\n";
	echo "Plugin URL: ".GDE_PLUGIN_URL."\n";
	echo "Server Env: ".$_SERVER['SERVER_SOFTWARE']."\n";
	echo "Browser Env: ".$_SERVER['HTTP_USER_AGENT']."\n\n";

	echo "cURL: ";
	if (function_exists('curl_version')) {
		$curl = curl_version(); echo $curl['version']."\n";
	} else { echo "No\n"; }
	
	echo "allow_url_fopen: ";
	if (ini_get('allow_url_fopen') !== "1") {
		echo "No\n";
	} else { echo "Yes\n"; }
	
	echo "GD: ";
	if (extension_loaded('gd')) {
		$gd = gd_info(); echo $gd['GD Version']." ";
		if ($gd['PNG Support']) {
			echo "(PNG Supported)";
		} else { echo "(PNG Unsupported)"; }
		echo "\n";
	} else { echo "No\n"; }
	
	echo "Rich Editing: ";
	if (get_user_option('rich_editing')) {
		echo "Yes\n";
	} else { echo "No\n"; }
	
	if (version_compare($wp_version, "3.4", ">") ) {
		echo "\n-- Active Theme --\n";
		$theme = wp_get_theme();
		echo $theme->Name . " " . $theme->Version;
	}
	
	echo "\n\n--- Other Active Plugins ---\n";
	$plugins = get_plugins();
	foreach ( $plugins as $k => $v ) {
		$str = $v['Name'] . " " . $v['Version'];
		if ( ! is_plugin_active( $k ) || $v['Name'] == $pdata['Name'] ) {
			continue;
		}
		echo $str . "\n";
	}
	
	echo "</textarea>";
?>
	<br/><br/>
	</div>

	<div id="debugwarn" style="display:none;color:red;font-weight:bold;">
		<p><?php _e("I'm less likely to be able to help you if you do not include debug information.", 'gde'); ?></p>
	</div>
	<input id="debugsend" class="button-primary" type="submit" value="<?php _e('Send Request', 'gde'); ?>" name="submit">
	<span id="formstatus" style="padding-left:20px;display:none;">
		<img src="<?php echo GDE_PLUGIN_URL;?>img/in-proc.gif" alt="">
	</span>
	<span id="doneconfirm" style="display: none;"> <?php _e('Request Sent', 'gde'); ?></span>
	<span id="failconfirm" style="display: none;"> <?php _e('Delivery failed. You can manually send this information to wpp@tnw.org for help.', 'gde'); ?></span>
	</td>
</tr>
</table>
</form>