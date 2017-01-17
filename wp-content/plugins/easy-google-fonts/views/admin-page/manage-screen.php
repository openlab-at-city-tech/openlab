<?php 
/**
 * Manage Screen
 *
 * This file contains the view for the Manage Font Controls
 * Screen.
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
<form autocomplete="off" method="post" action="<?php echo esc_url( add_query_arg( array( 'screen' => 'edit_controls' ), $this->admin_url ) ); ?>">
	<?php 
		/**
		 * Output New Font Control Dialog Message
		 * 
		 * If there are no font control output a dialog message
		 * to prompt the user to create a new custom control.
		 * 
		 */
		if ( $this->no_controls ) : ?>
		<div class="manage-controls no-controls">
			<label><?php _e( 'Create a new font control for your theme:', $this->plugin_slug ); ?></label>
			<?php submit_button( __( 'Create a New Font Control', $this->plugin_slug ), 'secondary', 'create_new_control', false, array( 'data-create-control-url' => $this->create_url ) ); ?>	
		</div><!-- /.no-controls -->
	<?php
		/**
		 * Output Custom Font Controls Table
		 * 
		 * If there are existing font controls output a table that
		 * displays all custom font control instances.
		 * 
		 */		 
		else : ?>
		<div class="manage-controls control-dialog">
			<label class="manage-label"><?php _e( 'Manage your custom font controls here or:', $this->plugin_slug ); ?></label>
			<label class="new-label"><?php _e( 'Create a new font control for your theme:', $this->plugin_slug ); ?></label>
			<?php submit_button( __( 'Create a New Font Control', $this->plugin_slug ), 'secondary', 'create_new_control', false, array( 'data-create-control-url' => $this->create_url ) ); ?>
		</div><!-- /.control-dialog -->
		
		<table id="font-controls-table" class="widefat fixed" cellspacing="0">
			<thead>
				<tr>
					<th class="manage-column column-controls "><?php _e( 'Font Control Name', $this->plugin_slug ); ?></th>
					<th class="manage-column column-controls"><?php _e( 'CSS Selectors', $this->plugin_slug ) ?></th>
					<th class="manage-column column-controls"><?php _e( 'Force Styles', $this->plugin_slug ) ?></th>
					<th></th>
				</tr>
			</thead>
			<tbody>
				<?php $row_count = 0; ?>
				<?php while ( $this->font_controls->have_posts() ) : $this->font_controls->the_post(); ?>
					<?php 
						$row_class       = ( $row_count % 2 == 0 ) ? 'alternate' : '';
						$selectors       = get_post_meta( get_the_ID(), 'control_selectors', true );
						$selector_output = '';
						$control_id      = get_post_meta( get_the_ID(), 'control_id', true );
						$force_styles    = get_post_meta( get_the_ID(), 'force_styles', true );

						$edit_link = esc_url( 
							add_query_arg( 
								array( 
									'screen'  => 'edit_controls',
									'action'  => 'edit',
									'control' => $control_id
								), 
								$this->admin_url
							) 
						);
						
						if ( $selectors ) {
							foreach ( $selectors as $selector ) {
								$selector_output .= "{$selector}, ";
							}
						}
					?>
					<tr class="<?php echo $row_class; ?>">
						<td class="post-title page-title column-title">
							<div>
								<strong><a href="#" class="row-title"><?php the_title(); ?></a></strong>
							</div>
							<div class="row-actions">
								<a data-control-reference="<?php echo $control_id; ?>" class="control-edit-link" href="<?php echo $edit_link; ?>"><?php _e( 'Edit', $this->plugin_slug ); ?></a> | <a data-control-reference="<?php echo $control_id; ?>" class="control-delete-link" href="#"><?php _e( 'Delete', $this->plugin_slug ); ?></a>
							</div>
						</td>
						<td class=""><?php echo $selector_output; ?></td>
						<td class=""><input autocomplete="off" data-control-reference="<?php echo $control_id; ?>" class="tt-force-styles" type="checkbox" <?php checked( $force_styles, true ); ?>></td>
						<td><span class="spinner" style=""></span></td>	
					</tr>
					<?php $row_count++; ?>
				<?php endwhile; ?>
			</tbody>
		</table>
		<?php 
			/**
			 * Create Delete All Controls Link
			 *
			 * Creates a button that will delete all custom
			 * controls created by the user.
			 */
		?>
		<a href="#" id="delete_all_controls"><?php _e( 'Delete All Controls', $this->plugin_slug ); ?></a>
	<?php endif; ?>
	<?php 
		/**
		 * Create Font Control Nonce Fields for Security
		 * 
		 * This ensures that the request to modify controls 
		 * was an intentional request from the user. Used in
		 * the Ajax request for validation.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/wp_nonce_field 	wp_nonce_field()
		 * 
		 */
		wp_nonce_field( 'tt_font_delete_control_instance', 'tt_font_delete_control_instance_nonce' );
		wp_nonce_field( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );
	?>
</form>
