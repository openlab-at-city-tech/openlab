<?php 
/**
 * Deleted Control Dialog Message 
 * 
 * Checks if a font control has just been deleted and
 * outputs a feedback to the message if it has.
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
	<?php if ( $_GET['dialog'] == 'deleted' ) : ?>
		<?php $deleted_control_name = isset( $_GET['name'] ) ? esc_attr( $_GET['name'] ) : __( 'Font Control', $this->plugin_slug ); ?>
		<div class="updated below-h2" id="delete_message">
			<p><?php printf( __( '%1$s has been deleted.', $this->plugin_slug ), "<strong>{$deleted_control_name}</strong>" ) ?></p>
		</div>
	<?php endif; ?>
<?php endif; ?>
