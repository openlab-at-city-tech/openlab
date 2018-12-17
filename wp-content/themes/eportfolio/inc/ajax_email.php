<?php
require_once( dirname(__FILE__) . '../../../../../wp-config.php');
require_once( dirname(__FILE__) . '../../functions.php');
global $options; foreach ($options as $value) { if (get_settings( $value['id'] ) === FALSE) { ${$value['id']} = $value['std']; } else { ${$value['id']} = get_settings( $value['id'] ); } }

if (empty($_GET['name']) || empty($_GET['email']) || empty($_GET['comment'])) {
	echo 'ERROR';
} else {

	$to = $ahstheme_contactemail;
	$subject = 'Email from your City Tech website';
	$message = stripslashes($_GET['comment']);
	$headers = 'From: '.$_GET['name'].'<'.$_GET['email'].'>';

	mail($to,$subject,$message,$headers);

	echo '<p>Thank you for your email!</p>';
}

?>
