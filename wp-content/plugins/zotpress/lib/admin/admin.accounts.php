<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 


// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{
	// Determine if server supports OAuth
	$oauth_is_not_installed = ! in_array( 'oauth', get_loaded_extensions() );

	if ( isset( $_GET['oauth'] ) )
	{
		include(__DIR__ . "/admin.accounts.oauth.php");

	} else { ?>

		<div id="zp-Zotpress" class="wrap">

			<?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>



			<!-- ZOTPRESS MANAGE ACCOUNTS -->

			<div id="zp-ManageAccounts">

				<h3><?php esc_html_e('Synced Zotero Accounts','zotpress'); ?></h3>
				<?php
				if ( ! isset( $_GET['no_accounts'] ) 
						|| ( isset( $_GET['no_accounts'] ) && $_GET['no_accounts'] != 'true' ) )
				{ ?>
				<a title="<?php esc_html_e('Add Account','zotpress'); ?>" class="zp-AddAccountButton button button-secondary" href="<?php echo esc_url(admin_url("admin.php?page=Zotpress&setup=true")); ?>">
					<span class="dashicons dashicons-plus-alt"></span>
					<span><?php esc_html_e('Add Account','zotpress'); ?></span>
				</a><?php } ?>

				<table id="zp-Accounts" class="wp-list-table widefat fixed posts">

					<thead>
						<tr>
							<th class="default first manage-column" scope="col"><?php esc_html_e('Default','zotpress'); ?></th>
							<th class="account_type first manage-column" scope="col"><?php esc_html_e('Type','zotpress'); ?></th>
							<th class="api_user_id manage-column" scope="col"><?php esc_html_e('User ID','zotpress'); ?></th>
							<th class="private_key manage-column" scope="col"><?php esc_html_e('Private Key','zotpress'); ?></th>
							<th class="nickname manage-column" scope="col"><?php esc_html_e('Nickname','zotpress'); ?></th>
							<th class="cache last manage-column" scope="col"><?php esc_html_e('Cache','zotpress'); ?></th>
							<th class="remove last manage-column" scope="col"><?php esc_html_e('Remove','zotpress'); ?></th>
						</tr>
					</thead>

					<tbody id="zp-AccountsList">
						<?php

							global $wpdb;

							$accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress");
							$zebra = " alternate";

							foreach ($accounts as $num => $account)
							{
								$zebra = $num % 2 == 0 ? " alternate" : "";

								$code = "<tr id='zp-Account-" . $account->api_user_id . "' class='zp-Account".$zebra."' rel='" . $account->api_user_id . "'>\n";

								// DEFAULT
								$code .= "                          <td class='default first'>";
								// if ( get_option("Zotpress_DefaultAccount") && get_option("Zotpress_DefaultAccount") == $account->api_user_id ) $code .= " selected";
								$code .= "<a href='javascript:void(0);' rel='". $account->api_user_id ."' class='default zp-Account-Default dashicons dashicons-star-";
								if ( get_option("Zotpress_DefaultAccount") && get_option("Zotpress_DefaultAccount") == $account->api_user_id ) $code .= "filled"; else  $code .= "empty";
								$code .= "' title='".__('Set as Default','zotpress')."'><span>".__('Set as Default','zotpress')."</span></a></td>\n";

								// ACCOUNT TYPE
								$code .= "                          <td class='account_type'>" . substr($account->account_type, 0, -1) . "</td>\n";

								// API USER ID
								$code .= "                          <td class='api_user_id'>" . $account->api_user_id . "</td>\n";

								// PRIVATE KEY
								$code .= "                          <td class='private_key'>";
								if ($account->public_key)
								{
									$code .= $account->public_key;
								}
								else
								{
									$code .= 'No private key entered. <strong><a class="zp-OAuth-Button" href="'.get_bloginfo( 'url' ).'/wp-content/plugins/zotpress/lib/admin/admin.accounts.oauth.php?oauth_user='.$account->api_user_id.'">'.__('Authenticate via OAuth','zotpress').'?</a></strong> <strong>Note:</strong> '.__('Clicking this link will take you to the Zotero website. Once there, click the "Change Permissions" button. For User accounts, be sure "Allow library access" is checked. For Group accounts, be sure "Read Only" is selected.','zotpress');
								}
								$code .= "</td>\n";

								// NICKNAME
								$code .= "                          <td class='nickname'>";
								if ($account->nickname)
									$code .= $account->nickname;
								$code .= "</td>\n";

								// CACHE
								$code .= "                          <td class='cache last'>\n";
								$code .= "                              <a title='".__('Clear Cache','zotpress')."' class='cache dashicons dashicons-image-rotate' href='#" . $account->api_user_id . "'><span>".__('Clear Cache','zotpress')."</span></a>\n";
								$code .= "                          </td>\n";

								// REMOVE
								$code .= "                          <td class='remove last'>\n";
								$code .= "                              <a title='".__('Remove','zotpress')."' class='delete dashicons dashicons-trash' href='#" . $account->api_user_id . "'><span>".__('Remove','zotpress')."</span></a>\n";
								$code .= "                          </td>\n";

								$code .= "                         </tr>\n\n";

								echo wp_kses_post($code);
							}
						?>
					</tbody>

				</table>

			</div>

			<span id="ZOTPRESS_PLUGIN_URL" class="ZP_ATTR"><?php echo esc_url(ZOTPRESS_PLUGIN_URL); ?></span>

			<?php if ( ! $oauth_is_not_installed ) { ?>
				<h3><?php esc_html_e('What is OAuth?','zotpress'); ?></h3>

				<p>
					OAuth helps you create the necessary private key for allowing Zotpress to read your Zotero library and display
					it for all to see. You can do this manually through the Zotero website; using OAuth in Zotpress is just a quicker, more straightforward way of going about it.
					<strong>Note: You'll need to have OAuth installed on your server to use this option.</strong> If you don't have OAuth installed, you'll have to generate a private key manually through the <a href="http://www.zotero.org/">Zotero</a> website.
				</p>
			<?php } ?>


		</div>

	<?php } /* OAuth check */

} // ! current_user_can('edit_others_posts')

else
{
	echo "<p>";
	esc_html_e("Sorry, you don't have permission to access this page.", "zotpress");
	echo "</p>";
}

?>
