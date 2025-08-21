<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Restrict to Editors
if ( current_user_can('edit_others_posts') ) {

?>
<!-- START OF ACCOUNT -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">

						<h4><?php esc_html_e('Set Default Account','zotpress'); ?></h4>

						<p class="note"><?php esc_html_e('Note: Only applicable if you have multiple synced Zotero accounts.','zotpress'); ?></p>

						<div id="zp-Zotpress-Options-Account" class="zp-Zotpress-Options">

							<label for="zp-Zotpress-Options-Account"><?php esc_html_e('Choose Account','zotpress'); ?>:</label>
							<select id="zp-Zotpress-Options-Account">
								<?php

								global $wpdb;
								$zp_accounts = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."zotpress ORDER BY account_type DESC");
								$zp_accounts_total = $wpdb->num_rows;

								// See if default exists
								$zp_default_account = "";
								if ( get_option("Zotpress_DefaultAccount") )
									$zp_default_account = get_option("Zotpress_DefaultAccount");

								foreach ( $zp_accounts as $zp_account )
									if ( $zp_account->api_user_id == $zp_default_account )
										echo "<option id=\"".esc_html($zp_account->api_user_id)."\" value=\"".esc_html($zp_account->api_user_id)."\" selected='selected'>".esc_html($zp_account->api_user_id)." (".esc_html($zp_account->nickname).") [".esc_html(substr(esc_html($zp_account->account_type), 0, strlen(esc_html($zp_account->account_type))-1))."]</option>\n";
									else
										echo "<option id=\"".esc_html($zp_account->api_user_id)."\" value=\"".esc_html($zp_account->api_user_id)."\">".esc_html($zp_account->api_user_id)." (".esc_html($zp_account->nickname).") [".esc_html(substr(esc_html($zp_account->account_type), 0, strlen(esc_html($zp_account->account_type))-1))."]</option>\n";
								?>
							</select>

							<input type="button" id="zp-Zotpress-Options-Account-Button" class="zp-Account-Default button-secondary" value="<?php esc_html_e('Set Default Account','zotpress'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php esc_html_e('Success','zotpress'); ?>!</div>
							<div class="zp-Errors"><?php esc_html_e('Errors','zotpress'); ?>!</div>

							<h4 class="clear">

						</div>
						<!-- END OF ACCOUNT -->

					</div>
				</div>

				<div class="zp-Column-2">
					<div class="zp-Column-Inner">

						<!-- START OF STYLE -->
						<h4><?php esc_html_e('Set Default Citation Style for Importing','zotpress'); ?></h4>

						<p class="note"><?php
							echo sprintf(
								wp_kses(
									/* translators: s: Zotero styles URL */
									__( 'Note: Styles must be listed <a title="Zotero Styles" href="%s">here</a>. Use the name found in the style\'s URL, e.g. modern-language-association.', 'zotpress' ),
									array(
										'a' => array(
											'href' => array()
										)
									)
								), esc_url( 'http://www.zotero.org/styles' )
							); ?>
						</p>

						<div id="zp-Zotpress-Options-Style-Container" class="zp-Zotpress-Options">

							<label for="zp-Zotpress-Options-Style"><?php esc_html_e('Choose Style','zotpress'); ?>:</label>
							<select id="zp-Zotpress-Options-Style">
								<?php

								if ( ! get_option("Zotpress_StyleList") )
									add_option( "Zotpress_StyleList", "apa, apsa, asa, chicago-author-date, chicago-fullnote-bibliography, harvard1, modern-language-association, nature, vancouver");

								$zp_styles = explode(", ", get_option("Zotpress_StyleList"));
								sort($zp_styles);

								// See if default exists
								$zp_default_style = "apa";
								if ( get_option("Zotpress_DefaultStyle") )
									$zp_default_style = get_option("Zotpress_DefaultStyle");

								foreach( $zp_styles as $zp_style )
									if ( $zp_style == $zp_default_style )
										echo "<option id=\"".esc_html($zp_style)."\" value=\"".esc_html($zp_style)."\" selected='selected'>".esc_html($zp_style)."</option>\n";
									else
										echo "<option id=\"".esc_html($zp_style)."\" value=\"".esc_html($zp_style)."\">".esc_html($zp_style)."</option>\n";

								?>
								<option id="new" value="new-style"><?php esc_html_e('Add another style','zotpress'); ?> ...</option>
							</select>

							<div id="zp-Zotpress-Options-Style-New-Container">
								<label for="zp-Zotpress-Options-Style-New"><?php esc_html_e('Add Style','zotpress'); ?>:</label>
								<input id="zp-Zotpress-Options-Style-New" type="text">
							</div>

							<input type="button" id="zp-Zotpress-Options-Style-Button" class="button-secondary" value="<?php esc_html_e('Set Default Style','zotpress'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php esc_html_e('Success','zotpress'); ?>!</div>
							<div class="zp-Errors"><?php esc_html_e('Errors','zotpress'); ?>!</div>

							<hr class="clear">

						</div>
						<!-- END OF STYLE -->

					</div>
				</div>
<?php

} // !current_user_can('edit_others_posts')

else {
	echo "<p>".esc_html_e("Sorry, you don't have permission to access this page.",'zotpress') ."</p>";
}

?>