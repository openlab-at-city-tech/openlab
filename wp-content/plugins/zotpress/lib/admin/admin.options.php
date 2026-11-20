<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Restrict to Editors
if ( current_user_can('edit_others_posts') )
{

?>

		<div id="zp-Zotpress" class="wrap">

            <?php include( dirname(__FILE__) . '/admin.menu.php' ); ?>

			<div id="zp-Options-Wrapper">

				<h3><?php esc_html_e('Options','zotpress'); ?></h3>

				<?php include(__DIR__ . '/admin.options.form.php'); ?>

				<hr>

				<!-- START OF CACHETIME -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">

						<h4><?php esc_html_e('Set Cache Timer','zotpress'); ?></h4>

						<p class="note"><?php esc_html_e('Modify how often Zotpress pings Zotero for updates to the cache.','zotpress'); ?></p>

						<div id="zp-Zotpress-Options-CacheTimer" class="zp-Zotpress-Options">

							<div class="zp-CacheTimer-Container">
								<label for="zp-Zotpress-Options-CacheTimer"><?php esc_html_e('Choose Cache Timer','zotpress'); ?>:</label>
								<select id="zp-Zotpress-Options-CacheTimer"><?php

							// See if default exists
                            $zp_default_cachetimer = "10";
                            if (get_option("Zotpress_DefaultCacheTimer"))
                                $zp_default_cachetimer = get_option("Zotpress_DefaultCacheTimer");

							echo "<option value=\"10\" ";
							if ( $zp_default_cachetimer == "10" ) echo "selected=\"selected\" ";
							echo ">Every 10 minutes</option>\n";

							echo "<option value=\"30\" ";
							if ( $zp_default_cachetimer == "30" ) echo "selected=\"selected\" ";
							echo ">Every 30 minutes</option>\n";

							echo "<option value=\"60\" ";
							if ( $zp_default_cachetimer == "60" ) echo "selected=\"selected\" ";
							echo ">Every 60 minutes</option>\n";

							echo "<option value=\"1440\" ";
							if ( $zp_default_cachetimer == "1440" ) echo "selected=\"selected\" ";
							echo ">Every 24 hours</option>\n";

							?></select></div><!-- .zp-CacheTimer-Container -->

							<input type="button" id="zp-Zotpress-Options-CacheTimer-Button" class="button-secondary" value="<?php esc_html_e('Set Cache Timer','zotpress'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php esc_html_e('Success','zotpress'); ?>!</div>
							<div class="zp-Errors"><?php esc_html_e('Errors','zotpress'); ?>!</div>

						</div>
					</div>
				</div><!-- END OF CACHETIME -->


				<!-- START OF CPT -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">

						<h4><?php esc_html_e('Set Reference Widget','zotpress'); ?></h4>

						<p class="note"><?php esc_html_e('Add or remove the Zotpress Reference widget for specific post types.','zotpress'); ?></p>

						<div id="zp-Zotpress-Options-CPT" class="zp-Zotpress-Options">

							<div class="zp-CPT-Checkbox-Container"><?php

							// See if default exists
                            $zp_default_cpt = "post,page";
                            if (get_option("Zotpress_DefaultCPT"))
                                $zp_default_cpt = get_option("Zotpress_DefaultCPT");
							$zp_default_cpt = explode(",",$zp_default_cpt);

							$post_types = get_post_types( '', 'names' );

							foreach ( $post_types as $post_type )
							{
								echo "<div class='zp-CPT-Checkbox'>";
								echo "<input type=\"checkbox\" name=\"zp-CTP\" id=\"".esc_html($post_type)."\" value=\"".esc_html($post_type)."\" ";
								if ( in_array( $post_type, $zp_default_cpt ) ) echo "checked ";
								echo ">";
								echo "<label ";
								echo "for=\"".esc_html($post_type)."\">".esc_html($post_type)."</label>";
								echo "</div>\n";
							}

							?></div><!-- .zp-CPT-Checkbox-Container -->

							<input type="button" id="zp-Zotpress-Options-CPT-Button" class="button-secondary" value="<?php esc_html_e('Set Reference Widget','zotpress'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php esc_html_e('Success','zotpress'); ?>!</div>
							<div class="zp-Errors"><?php esc_html_e('Errors','zotpress'); ?>!</div>

						</div>
					</div>
				</div><!-- END OF CPT -->

				<hr>

				<!-- START OF RESET -->
				<div class="zp-Column-1">
					<div class="zp-Column-Inner">

						<h4><?php esc_html_e('Reset Zotpress','zotpress'); ?></h4>

						<p class="note"><?php esc_html_e('Note: This action will clear all database entries associated with Zotpress, including account information and citations&#8212;it cannot be undone. Proceed with caution.', 'zotpress'); ?></p>

						<div id="zp-Zotpress-Options-Reset" class="zp-Zotpress-Options">

							<input type="button" id="zp-Zotpress-Options-Reset-Button" class="button-secondary" value="<?php esc_html_e('Reset Zotpress','zotpress'); ?>">
							<div class="zp-Loading">loading</div>
							<div class="zp-Success"><?php esc_html_e('Success','zotpress'); ?>!</div>
							<div class="zp-Errors"><?php esc_html_e('Errors','zotpress'); ?>!</div>

						</div>
					</div>
				</div><!-- END OF RESET -->

			</div><!-- zp-Browse-Wrapper -->

		</div>

<?php

} // !current_user_can('edit_others_posts')

else
{
	echo "<p>".esc_html_e("Sorry, you don't have permission to access this page.","zotpress")."</p>";
}

?>
