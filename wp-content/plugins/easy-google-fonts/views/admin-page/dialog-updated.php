<?php 
/**
 * Updated Control Message 
 *
 * Message to display to the user if this
 * font control has been updated.
 * 
 * @package   Easy_Google_Fonts
 * @author    Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license   GPL-2.0+
 * @link      http://wordpress.org/plugins/easy-google-fonts/
 * @copyright Copyright (c) 2016, Titanium Themes
 * @version   1.4.2
 * 
 */
?>
<?php if ( isset( $_GET['dialog'] ) ) : ?>
	<?php if ( 'updated' == $_GET['dialog'] ) : ?>
		<?php $updated_control_name =  isset( $_GET['name'] ) ? esc_attr( $_GET['name'] ) : __( 'Font Control', $this->plugin_slug ); ?>
		<div class="updated below-h2" id="update_message">
			<p>
				<?php printf( __( '%1$s has been updated. Please visit the %2$s to manage this control.', $this->plugin_slug ), "<strong id='updated_control_name'>{$updated_control_name}</strong>", "<strong><a href='" . admin_url( 'customize.php' ) . "'>customizer</a></strong>" ); ?>
			</p>
		</div>
	<?php endif; ?>
<?php endif; ?>
