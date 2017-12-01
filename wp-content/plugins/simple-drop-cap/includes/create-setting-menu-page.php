<?php

// action for adding options page
add_action( 'admin_menu', 'wpsdc_create_menu' );

// funtion for wpsdc plugin menu
function wpsdc_create_menu()
{
	// add options page for simple drop cap plugin
	add_options_page( __( 'Simple Drop Cap Settings', 'simple-drop-cap' ), 'Simple Drop Cap', 'manage_options', 'wpsdc_settings_menu', 'wpsdc_settings_page' );

}

// function for creating wpsdc options field
function wpsdc_settings_page()
{
	ob_start();
	?>
		<div class="wrap">
			<h2><?php _e( 'Simple Drop Cap Settings', 'simple-drop-cap' ); ?></h2>

			<p><?php printf( __( 'If you find a bug or need a support request, please post your request %1$shere%2$s.', 'simple-drop-cap' ), '<a href="http://wordpress.org/support/plugin/simple-drop-cap" target="_blank">', '</a>' ); ?>

			<br>

			<?php printf( __( 'And if you find this plugin helpful, please leave a review and comment %1$shere%2$s. Thank you :)', 'simple-drop-cap' ), '<a href="http://wordpress.org/support/view/plugin-reviews/simple-drop-cap" target="_blank">', '</a>' ); ?></p>

			<p><strong><?php _e( 'Note', 'simple-drop-cap' ); ?>:</strong> <?php printf( __( 'Pro version is available now. More advanced features and better support. %1$sGet it now for the low price!%2$s', 'simple-drop-cap' ), '<a href="http://www.yudhistiramauris.com/products/simple-drop-cap-pro/" target="_blank">', '</a>' ); ?></p>

			<form method="post" action="options.php">

				<?php settings_fields( 'wpsdc-settings-group' ); // set plugin option group for the form ?>

				<?php $wpsdc_options = get_option( 'wpsdc_options' ); // get plugin options from the database ?>
		
				<h3><?php _e( 'Style Settings', 'simple-drop-cap' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e( 'Display Mode', 'simple-drop-cap' ); ?></th>
							<td>
								<input type="radio" id="option_display_mode_normal" name="wpsdc_options[option_display_mode]" value="normal" <?php checked( 'normal', $wpsdc_options['option_display_mode'] ); ?> ><?php _e( 'Normal Mode', 'simple-drop-cap' ); ?>

								<br>

								<small><?php printf( __( '%1$sClick here%2$s for the example.', 'simple-drop-cap' ), '<a href="http://wordpress.org/plugins/simple-drop-cap/screenshots/" target="_blank">', '</a>' ) ?></small>

								<br>
								<br>

								<input type="radio" id="option_display_mode_float" name="wpsdc_options[option_display_mode]" value="float" <?php checked( 'float', $wpsdc_options['option_display_mode'] ); ?> ><?php _e( 'Float Mode', 'simple-drop-cap' ); ?>

								<br>

								<small><?php printf( __( '%1$sClick here%2$s for the example.', 'simple-drop-cap' ), '<a href="http://wordpress.org/plugins/simple-drop-cap/screenshots/" target="_blank">', '</a>' ) ?></small>

								<br>
								<br>

								<input type="radio" id="option_display_mode_custom" name="wpsdc_options[option_display_mode]" value="custom" <?php checked( 'custom', $wpsdc_options['option_display_mode'] ); ?> ><?php _e( 'Custom Mode', 'simple-drop-cap' ); ?>							

								<br>

								<small><?php _e( 'Use your own specified CSS styles. This will override all styling options.', 'simple-drop-cap' ); ?></small>
							</td>
						</tr>						
						<tr valign="top">
							<th scope="row"><?php _e( 'Drop Cap Color', 'simple-drop-cap' ); ?></th>
							<td>
								<input type="text" class="font-color-field" name="wpsdc_options[option_font_color]" value="<?php echo esc_attr( $wpsdc_options['option_font_color'] ); ?>" >
							</td>
						</tr>
						<tr valign="top">
							<th scope="row"><?php _e( 'Custom CSS Style', 'simple-drop-cap' ); ?></th>
							<td>						
								<small><?php _e( 'Specify your custom CSS styles for custom mode in the box below.', 'simple-drop-cap' ); ?></small>
								<br>
								<textarea rows="7" cols="50" name="wpsdc_options[option_custom_css]" id="wpsdc_options[option_custom_css]"><?php echo esc_textarea( $wpsdc_options['option_custom_css'] ); ?></textarea>
							</td>
						</tr>
					</tbody>	
				</table>

				<h3><?php _e( 'Usage Settings', 'simple-drop-cap' ); ?></h3>
				<table class="form-table">
					<tbody>
						<tr valign="top">
							<th scope="row"><?php _e( 'Drop Cap Automation', 'simple-drop-cap' ); ?></th>
							<td>
								<input type="checkbox" name="wpsdc_options[option_enable_all_posts]" value="1" <?php checked( $wpsdc_options['option_enable_all_posts'], '1' ); ?>>
								<label for="wpsdc_options[option_enable_all_posts]"><?php _e( 'Automatically transform the first letter of all posts, pages, and custom post types into a drop cap.', 'simple-drop-cap' ); ?></label>
								<br>
								<small><?php _e( 'This also will disable [dropcap] shortcode on post, page, and custom post type.', 'simple-drop-cap' ); ?></small>
							</td>
						</tr>
					</tbody>	
				</table>

				<?php submit_button(); ?>

			</form>
		</div>
	<?php
	echo ob_get_clean();
}