<?php
/**
 *  File to display support form in the plugin.
 *
 * @package    password-policy-manager/views
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$support_logo = dirname( plugin_dir_url( __FILE__ ) ) . '/includes/images/support.png	';

echo '	
		<div class = "moppm_mo_page_divided_layout_2">
			
			<img src="' . esc_url_raw( $support_logo ) . '">
			<h1>' . esc_html__( 'Support', 'password-policy-manager' ) . '</h1>
			<p>' . esc_html__( 'Need any help? We are available any time, Just send us a query so we can help you.', 'password-policy-manager' ) . '</p>
				<form name="f" method="post" action="">
					<input type="hidden" name="option" value="moppm_send_query"/>
					<input type="hidden" name="nonce" value="' . esc_attr( wp_create_nonce( 'sendQueryNonce' ) ) . '"/>
					<table class="moppm_mo_settings_table">
						<tr><td>
							<input type="email" class="moppm_table_textbox" id="query_email" name="query_email" value="' . esc_attr( $email ) . '" placeholder="Enter your email" required />
							</td>
						</tr>
						<tr><td>
							<input type="tel" class="moppm_table_textbox" name="query_phone" id="query_phone" value="' . esc_attr( $phone ) . '" placeholder="Enter your phone"/>
							</td>
						</tr>
						<tr>
							<td>
								   <textarea id="query" name="query" class="moppm_mo_settings_textarea" cols="52" rows="7" placeholder="Write your query here"></textarea>
							</td>
						</tr>
					</table>
					
					<input type="submit" name="send_query" id="send_query" value="' . esc_attr__( 'Submit Query', 'password-policy-manager' ) . '" style="margin-bottom:3%;" class="button button-primary"/>
				</form>
				<br/>			
		</div>';
