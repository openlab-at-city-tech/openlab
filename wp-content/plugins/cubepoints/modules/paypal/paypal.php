<?php

/** PayPal Top-up Module */

cp_module_register(__('PayPal Top-up', 'cp') , 'paypal' , '1.2', 'CubePoints', 'http://cubepoints.com', 'http://cubepoints.com' , __('Allow users to buy points using PayPal.', 'cp'), 1);

function cp_module_paypal_install(){
		add_option('cp_module_paypal_account', '');
		add_option('cp_module_paypal_sandbox', false);
		add_option('cp_module_paypal_currency', 'USD');
		add_option('cp_module_paypal_item', '%npoints% '.get_bloginfo('name').' '.__('Points', 'cp'));
		add_option('cp_module_paypal_cancel', get_bloginfo('url').'/?cp_module_paypal_return=0');
		add_option('cp_module_paypal_thankyou', get_bloginfo('url').'/?cp_module_paypal_return=1');
		add_option('cp_module_paypal_price', 0.05);
		add_option('cp_module_paypal_min', 1);
		add_option('cp_module_paypal_form',"<form method=\"post\">\n<input type=\"hidden\" name=\"cp_module_paypal_pay\" value=\"1\" />\nNumber of points to purchase:<br />\n<input type=\"text\" name=\"points\" /><br />\n<input type=\"submit\" value=\"Buy!\" />\n</form>");
}
add_action('cp_module_paypal_activate','cp_module_paypal_install');

if(cp_module_activated('paypal')){

function cp_module_paypal_shortcode( $atts ){
	$r = get_option('cp_module_paypal_form');
	$r = str_replace('%min%',get_option('cp_module_paypal_min'),$r);
	return $r;
}
add_shortcode('cp_paypal','cp_module_paypal_shortcode');

/** PayPal top-up logs hook */
add_action('cp_logs_description','cp_module_paypal_logs', 10, 4);
function cp_module_paypal_logs($type,$uid,$points,$data){
	if($type!='paypal') { return; }
	$data = unserialize($data);
	echo '<span title="'.__('Paid by', 'cp').': '.$data['payer_email'].'">'.__('PayPal Points Top-up', 'cp').' (ID: '.$data['txn_id'].')</span>';
}

function cp_module_paypal_round_up($value, $precision = 0) { 
    $sign = (0 <= $value) ? +1 : -1; 
    $amt = explode('.', $value); 
    $precision = (int) $precision; 
    
    if (strlen($amt[1]) > $precision) { 
        $next = (int) substr($amt[1], $precision); 
        $amt[1] = (float) (('.'.substr($amt[1], 0, $precision)) * $sign); 
        
        if (0 != $next) { 
            if (+1 == $sign) { 
                $amt[1] = $amt[1] + (float) (('.'.str_repeat('0', $precision - 1).'1') * $sign); 
            } 
        } 
    } 
    else { 
        $amt[1] = (float) (('.'.$amt[1]) * $sign); 
    } 
    
    return $amt[0] + $amt[1]; 
} 

function cp_module_paypal_add_admin_page(){
	add_submenu_page('cp_admin_manage', 'CubePoints - ' .__('PayPal Top-up','cp'), __('PayPal Top-up','cp'), 'manage_options', 'cp_modules_paypal_admin', 'cp_modules_paypal_admin');
}
add_action('cp_admin_pages','cp_module_paypal_add_admin_page');

function cp_modules_paypal_admin(){

// handles form submissions
if ($_POST['cp_module_paypal_form_submit'] == 'Y') {

	update_option('cp_module_paypal_account', trim($_POST['cp_module_paypal_account']));
	update_option('cp_module_paypal_sandbox', (bool)$_POST['cp_module_paypal_sandbox']);
	update_option('cp_module_paypal_currency', $_POST['cp_module_paypal_currency']);
	update_option('cp_module_paypal_item', trim($_POST['cp_module_paypal_item']));
		if(trim($_POST['cp_module_paypal_cancel'])==''){ $_POST['cp_module_paypal_cancel'] = get_bloginfo('url').'/?cp_module_paypal_return=0'; }
	update_option('cp_module_paypal_cancel', trim($_POST['cp_module_paypal_cancel']));
		if(trim($_POST['cp_module_paypal_thankyou'])==''){ $_POST['cp_module_paypal_thankyou'] = get_bloginfo('url').'/?cp_module_paypal_return=1'; }
	update_option('cp_module_paypal_thankyou', trim($_POST['cp_module_paypal_thankyou']));   
	update_option('cp_module_paypal_price', ((float)$_POST['cp_module_paypal_price']<=0)?1:(float)$_POST['cp_module_paypal_price']);
	update_option('cp_module_paypal_min', ((int)$_POST['cp_module_paypal_min']<=0)?1:(int)$_POST['cp_module_paypal_min']);
	update_option('cp_module_paypal_form', trim(stripslashes($_POST['cp_module_paypal_form'])));
	


	echo '<div class="updated"><p><strong>'.__('Settings Updated','cp').'</strong></p></div>';
}

function cp_module_paypal_currSel($curr){
	if($curr == get_option('cp_module_paypal_currency')) { echo 'selected'; }
}
if(get_option('cp_module_paypal_sandbox')){
	$cp_module_paypal_sandbox_checked = 'checked';
}
	
?>
<script type="text/javascript">
string1 = '<form method="post">'+"\n"+'<input type="hidden" name="cp_module_paypal_pay" value="1" />'+"\n"+'Number of points to purchase:<br />'+"\n"+'<input type="text" name="points" /><br />'+"\n"+'<input type="submit" value="Buy!" />'+"\n"+'</form>';
string2 = '<form method="post">'+"\n"+'<input type="hidden" name="cp_module_paypal_pay" value="1" />'+"\n"+'Number of points to purchase:<br />'+"\n"+'<select name="points">'+"\n"+'<option value="100">100 Points</option>'+"\n"+'<option value="200">200 Points</option>'+"\n"+'<option value="300">300 Points</option>'+"\n"+'<option value="400">400 Points</option>'+"\n"+'<option value="500">500 Points</option>'+"\n"+'</select>'+"\n"+'<br />'+"\n"+'<input type="submit" value="Buy!" />'+"\n"+'</form>';
string3 = '<form method="post">'+"\n"+'<input type="hidden" name="cp_module_paypal_pay" value="1" />'+"\n"+'<input type="hidden" name="points" value="100" />'+"\n"+'<input type="submit" value="Buy 100 Points" />'+"\n"+'</form>';
string4 = '<a href="<?php bloginfo('url'); ?>/?cp_module_paypal_pay=1&points=100">Buy 100 Points</a>';
</script>
<div class="wrap">
	<h2>CubePoints - <?php _e('PayPal Top-up', 'cp'); ?></h2>
	<?php _e('Configure the PayPal Top-up module.', 'cp'); ?><br /><br />

	<form name="cp_module_paypal_form" method="post">
		<input type="hidden" name="cp_module_paypal_form_submit" value="Y" />

	<h3><?php _e('PayPal Settings','cp'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_account"><?php _e('PayPal Username', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_account" name="cp_module_paypal_account" value="<?php echo get_option('cp_module_paypal_account'); ?>" size="40" /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_sandbox"><?php _e('Sandbox mode', 'cp'); ?>:</label></th>
			<td valign="middle"><input id="cp_module_paypal_sandbox" name="cp_module_paypal_sandbox" type="checkbox" value="1" <?php echo $cp_module_paypal_sandbox_checked; ?> /></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_currency"><?php _e('Currency', 'cp'); ?>:</label></th>
			<td valign="middle">
			<select id="cp_module_paypal_currency" name="cp_module_paypal_currency" class="widefat" width="230" style="width:270px;">
				<option value="AUD" <?php cp_module_paypal_currSel('AUD'); ?>>Australian Dollars</option>
				<option value="CAD" <?php cp_module_paypal_currSel('CAD'); ?>>Canadian Dollars</option>
				<option value="EUR" <?php cp_module_paypal_currSel('EUR'); ?>>Euros</option>
				<option value="GBP" <?php cp_module_paypal_currSel('GBP'); ?>>Pounds Sterling</option>
				<option value="JPY" <?php cp_module_paypal_currSel('JPY'); ?>>Yen</option>
				<option value="USD" <?php cp_module_paypal_currSel('USD'); ?>>U.S. Dollars</option>
				<option value="NZD" <?php cp_module_paypal_currSel('NZD'); ?>>New Zealand Dollar</option>
				<option value="CHF" <?php cp_module_paypal_currSel('CHF'); ?>>Swiss Franc</option>
				<option value="HKD" <?php cp_module_paypal_currSel('HKD'); ?>>Hong Kong Dollar</option>
				<option value="SGD" <?php cp_module_paypal_currSel('SGD'); ?>>Singapore Dollar</option>
				<option value="SEK" <?php cp_module_paypal_currSel('SEK'); ?>>Swedish Krona</option>
				<option value="DKK" <?php cp_module_paypal_currSel('DKK'); ?>>Danish Krone</option>
				<option value="PLN" <?php cp_module_paypal_currSel('PLN'); ?>>Polish Zloty</option>
				<option value="NOK" <?php cp_module_paypal_currSel('NOK'); ?>>Norwegian Krone</option>
				<option value="HUF" <?php cp_module_paypal_currSel('HUF'); ?>>Hungarian Forint</option>
				<option value="CZK" <?php cp_module_paypal_currSel('CZK'); ?>>Czech Koruna</option>
				<option value="ILS" <?php cp_module_paypal_currSel('ILS'); ?>>Israeli Shekel</option>
				<option value="MXN" <?php cp_module_paypal_currSel('MXN'); ?>>Mexican Peso</option>
				<option value="BRL" <?php cp_module_paypal_currSel('BRL'); ?>>Brazilian Real</option>
				<option value="MYR" <?php cp_module_paypal_currSel('MYR'); ?>>Malaysian Ringgits</option>
				<option value="PHP" <?php cp_module_paypal_currSel('PHP'); ?>>Philippine Pesos</option>
				<option value="TWD" <?php cp_module_paypal_currSel('TWD'); ?>>Taiwan New Dollars</option>
				<option value="THB" <?php cp_module_paypal_currSel('THB'); ?>>Thai Baht</option>
			</select>
			</td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_item"><?php _e('PayPal item name', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_item" name="cp_module_paypal_item" value="<?php echo get_option('cp_module_paypal_item'); ?>" size="40" /> <br /><small>Shortcode: %points%, %npoints%</small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_cancel"><?php _e('Cancel URL', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_cancel" name="cp_module_paypal_cancel" value="<?php echo get_option('cp_module_paypal_cancel'); ?>" size="40" /> <br /><small>URL to direct your users when they cancel the payment.</small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_thankyou"><?php _e('Thank You URL', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_thankyou" name="cp_module_paypal_thankyou" value="<?php echo get_option('cp_module_paypal_thankyou'); ?>" size="40" /> <br /><small>URL to direct your users when they complete the payment.</small></td>
		</tr>
	</table>
	<br />
	<h3><?php _e('Points Settings','cp'); ?></h3>
	<table class="form-table">
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_price"><?php _e('Price per point', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_price" name="cp_module_paypal_price" value="<?php echo get_option('cp_module_paypal_price'); ?>" size="40" /> <br /><small>Entering 0.05 would mean that $1 buys you 20 points.</small></td>
		</tr>
		<tr valign="top">
			<th scope="row"><label for="cp_module_paypal_min"><?php _e('Minimum points per purchase', 'cp'); ?>:</label></th>
			<td valign="middle"><input type="text" id="cp_module_paypal_min" name="cp_module_paypal_min" value="<?php echo get_option('cp_module_paypal_min'); ?>" size="40" /></td>
		</tr>
	</table>
	<br />
	<h3><?php _e('Form Settings','cp'); ?></h3>
	<label for="cp_module_paypal_form"><?php _e('Purchase Form HTML Code', 'cp'); ?>:</label><br />
	<textarea id="cp_module_paypal_form" name="cp_module_paypal_form" cols="90" rows="13" style="font-size:10px;" /><?php echo get_option('cp_module_paypal_form'); ?></textarea>
	<br />
	<small><?php _e('Choose a preset', 'cp'); ?>: 
		<a href="#" onclick="document.getElementById('cp_module_paypal_form').value=string1;return false;"><?php _e('Enter any amount', 'cp'); ?></a> |
		<a href="#" onclick="document.getElementById('cp_module_paypal_form').value=string2;return false;"><?php _e('Select from a list', 'cp'); ?></a> |
		<a href="#" onclick="document.getElementById('cp_module_paypal_form').value=string3;return false;"><?php _e('Single button with fixed points', 'cp'); ?></a> |
		<a href="#" onclick="document.getElementById('cp_module_paypal_form').value=string4;return false;"><?php _e('Link', 'cp'); ?></a>
	</small>
	<p>To insert the points purchase form into a page, use the following shortcode: <i>[cp_paypal]</i></p>
	<br />
	<h3><?php _e('PayPal IPN Configuration','cp'); ?></h3>
	<p><?php _e('To ensure that this module works correctly, Instant Payment Notifications (IPN) has to be turned on from your PayPal account.', 'cp'); ?></p>
	<p><?php _e('IPN Notification URL', 'cp'); ?>: <i><?php bloginfo('url'); ?>/?cp_module_paypal_ipn=1</i></p>
	<p><a href="https://www.paypal.com/cgi-bin/webscr?cmd=_profile-ipn-notify" target="_blank"><?php _e('Click here', 'cp'); ?></a> <?php _e('to configure PayPal IPN settings.', 'cp'); ?>

	<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Update Options','cp'); ?>" />
	</p>
</form>
</div>
<?php
}

function cp_module_paypal_pay(){
	if(isset($_REQUEST['cp_module_paypal_pay']) && $_REQUEST['cp_module_paypal_pay']!=''){
	header("Cache-Control: no-cache, must-revalidate");
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		
	if(get_option('cp_module_paypal_sandbox')){
		$loc = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
	}
	else{
		$loc = 'https://www.paypal.com/cgi-bin/webscr';
	}
	$points = (int) $_REQUEST['points'];
	if(!is_user_logged_in()){
		cp_module_paypal_showMessage(__('You must be logged in to purchase points!', 'cp'));
	}
	if($points<get_option('cp_module_paypal_min')){
		cp_module_paypal_showMessage(__('You must purchase a minimum of', 'cp').' '.get_option('cp_module_paypal_min').' points!');
	}
	$price =  cp_module_paypal_round_up(get_option('cp_module_paypal_price') * $points, 2);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title><?php _e('Processing payment...', 'cp'); ?></title> 
	<meta name="robots" content="noindex, nofollow" /> 
	<link rel='stylesheet' id='thickbox-css'  href='<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'style.css' ?>' type='text/css' media='all' /> 
</head>
<body>
	<form action="<?php echo $loc; ?>" method="post" name="paypal_form">
		<input type="hidden" name="cmd" value="_xclick">
		<input type="hidden" name="business" value="<?php echo get_option('cp_module_paypal_account'); ?>">
		<input type="hidden" name="item_name" value="<?php echo str_replace('%points%',cp_formatPoints($points),str_replace('%npoints%',$points,get_option('cp_module_paypal_item'))); ?>">
		<input type="hidden" name="on1" value="User">
		<input type="hidden" name="os1" value="<?php $user=get_userdata(cp_currentUser()); echo $user->user_login;?>">
		<input type="hidden" name="custom" value="<?php echo $points .'|'. cp_currentUser(); ?>">
		<input type="hidden" name="no_shipping" value="1">
		<input type="hidden" name="return" value="<?php echo get_option('cp_module_paypal_thankyou'); ?>">
		<input type="hidden" name="cbt" value="<?php _e('Return to', 'cp'); echo ' '; bloginfo('name'); ?>">
		<input type="hidden" name="cancel_return" value="<?php echo get_option('cp_module_paypal_cancel'); ?>">
		<input type="hidden" name="notify_url" value="<?php bloginfo('url'); ?>/?cp_module_paypal_ipn=1">
		<input type="hidden" name="rm" value="2">
		<input type="hidden" name="amount" value="<?php echo $price; ?>">
		<input type="hidden" name="currency_code" value="<?php echo get_option('cp_module_paypal_currency'); ?>">
	</form>
	<div id="container">
	<p id="load"><img src="<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'load.gif' ?>" alt="<?php _e('Processing payment...', 'cp'); ?>" /></p>
	<p id="text">Processing payment...</p> 
	<p><a href="#" onclick="document.paypal_form.submit();return false;">Click here to continue if you are not automatically redirected &raquo;</a></p> 
	</div> 
	<script type="text/javascript">
		setTimeout("document.paypal_form.submit()",2000);
	</script>
</body> 
</html> 
<?php
exit;
}
}

add_action('init','cp_module_paypal_pay');
	
function cp_module_paypal_message(){
		if(isset($_REQUEST['cp_module_paypal_return']) && $_REQUEST['cp_module_paypal_return']!=''){
		if($_REQUEST['cp_module_paypal_return']=='1'){
			cp_module_paypal_showMessage(__('Thank you for your purchase!', 'cp'));
		}
		if($_REQUEST['cp_module_paypal_return']=='0'){
			cp_module_paypal_showMessage(__('Your payment did not go through successfully!', 'cp'));
		}
		exit;
	}
}
	
function cp_module_paypal_showMessage($message){
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"> 
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US"> 
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /> 
	<title><?php bloginfo('name'); ?></title> 
	<meta name="robots" content="noindex, nofollow" /> 
	<link rel='stylesheet' id='thickbox-css'  href='<?php echo WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)). 'style.css' ?>' type='text/css' media='all' /> 
</head>
<body>
	<div id="container">
	<p id="text"><?php echo $message; ?></p> 
	<p><a href="<?php bloginfo('url'); ?>">Click here to return to <?php bloginfo('name'); ?> &raquo;</a></p> 
	</div>
</body> 
</html> 
<?php
exit();
}

add_action('init','cp_module_paypal_message');

function cp_module_paypal_ipn(){
	if(isset($_GET['cp_module_paypal_ipn']) && $_GET['cp_module_paypal_ipn']!=''){

		if(get_option('cp_module_paypal_sandbox')){
			$host = 'www.sandbox.paypal.com';
		}
		else{
			$host = 'www.paypal.com';
		}

		// read the post from PayPal system and add 'cmd'
		$req = 'cmd=' . urlencode('_notify-validate');
		 
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, 'https://' . $host . '/cgi-bin/webscr');
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $req);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Host: ' . $host));
		$res = curl_exec($ch);
		curl_close($ch);

		// assign posted variables to local variables
		$item_name = $_POST['item_name'];
		$item_number = $_POST['item_number'];
		$payment_status = $_POST['payment_status'];
		$payment_amount = $_POST['mc_gross'];
		$payment_currency = $_POST['mc_currency'];
		$txn_id = $_POST['txn_id'];
		$receiver_email = $_POST['receiver_email'];
		$payer_email = $_POST['payer_email'];
		$custom = $_POST['custom'];
		list($points,$uid)=explode('|',$custom);
		
		if (strcmp ($res, "VERIFIED") == 0) {
			// check the payment_status is Completed
			if($payment_status!='Completed'){
				die();
			}
			// check that txn_id has not been previously processed
			global $wpdb;
			$results = $wpdb->get_results('SELECT * FROM `'.CP_DB.'` WHERE `type`=\'paypal\'');
			foreach($results as $result){
				$data = unserialize($result->data);
				if($data['txn_id']==$txn_id){
					die();
				}
			}
			// check that receiver_email is your Primary PayPal email
			if($receiver_email!=trim(get_option('cp_module_paypal_account'))){
				die();
			}
			// check that payment_amount/payment_currency are correct
			if($payment_currency!=get_option('cp_module_paypal_currency')){
				die();
			}
			if((float)$payment_amount!=(float)cp_module_paypal_round_up(get_option('cp_module_paypal_price') * (int)$points, 2)){
				die();
			}
			// process payment
			cp_points('paypal', $uid, (int)$points, serialize(array('txn_id'=>$txn_id,'payer_email'=>$payer_email,'amt'=>$payment_amount)));
		}
		else if (strcmp ($res, "INVALID") == 0) {
			// invalid IPN
			die();
		}
		exit();

	}
}

add_action('init','cp_module_paypal_ipn');

}
?>