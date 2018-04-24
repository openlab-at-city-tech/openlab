<?php 
/**
 * Updated Dialog Message 
 *
 * Message to display to the user if this
 * custom sidebar has been updated.
 * 
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
?>
<?php if ( isset( $_GET['dialog'] ) ) : ?>
	<?php if ( $_GET['dialog'] == 'updated' ) : ?>
		<?php $updated_sidebar_name = isset( $_GET['name'] ) ? esc_attr( $_GET['name'] ) : __( 'Sidebar', 'easy-custom-sidebars' ); ?>
		<div class="updated below-h2" id="update_message">
			<p><?php printf( __( '%1$s has been updated.', 'easy-custom-sidebars' ), "<strong>{$updated_sidebar_name}</strong>" ) ?></p>
		</div>
	<?php endif; ?>
<?php endif; ?>
