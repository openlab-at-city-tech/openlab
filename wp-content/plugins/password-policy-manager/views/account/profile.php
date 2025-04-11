<?php
/**
 * File to display miniorange user profile page.
 *
 * @package    password-policy-manager/views/account
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$back_button = admin_url() . 'admin.php?page=moppm';
echo '
            <div>
                <h4>Thank You for registering with miniOrange.</h4>
                <h3>Your Profile</h3>
                <table border="1" style="background-color:#FFFFFF; border:1px solid #CCCCCC; border-collapse: collapse; padding:0px 0px 0px 10px; margin:2px; width:85%">
                    <tr>
                        <td style="width:45%; padding: 10px;">Username/Email</td>
                        <td style="width:55%; padding: 10px;">' . esc_html( $email ) . '</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">User ID</td>
                        <td style="width:55%; padding: 10px;">' . esc_html( $key ) . '</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">API Key</td>
                        <td style="width:55%; padding: 10px;">' . esc_html( $api ) . '</td>
                    </tr>
                    <tr>
                        <td style="width:45%; padding: 10px;">Token Key</td>
                        <td style="width:55%; padding: 10px;">' . esc_html( $token ) . '</td>
                    </tr>
                </table>
                <br/>
                 <center>';
if ( isset( $back_button ) ) {

		echo '<a class="button button-secondary" href="' . esc_url( $back_button ) . '">Back</a> ';
}
				echo '
                <a id="moppm_log_out" class="button button-primary">Remove Account</a>
                </center>
                <p><a href="#mo_wpns_forgot_password_link">Click here</a> if you forgot your password to your miniOrange account.</p>
            </div>
	<form id="forgot_password_form" method="post" action="">
		<input type="hidden" name="option" value="moppm_reset_password" />
        <input type="hidden" name="nonce" value=' . esc_attr( wp_create_nonce( 'moppm-account-nonce' ) ) . ' >
	</form>
	
	<script>
		jQuery(document).ready(function(){
			jQuery(\'a[href="#mo_wpns_forgot_password_link"]\').click(function(){
				jQuery("#forgot_password_form").submit();
			});
		});
	</script>';
?>
<script type="text/javascript">
		jQuery(document).ready(function()
		{

			jQuery("#moppm_log_out").click(function()
			{
				var data =  
				{
					'action'                  : 'moppm_ajax',
					'option'                  : 'moppm_log_out_form',  
				};
				jQuery.post(ajaxurl, data, function(response) {
					window.location.reload(true);
				});
			});
	});
</script>
