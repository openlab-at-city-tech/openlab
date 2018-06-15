<?php 
/**
 * Manage Screen View
 *
 * This view displays the output for the manage sidebars
 * screen in the WordPress Administration Area. This 
 * screen allows the user to:
 *     
 *     - Choose to edit a specific sidebar
 *     - Quickly delete a specific sidebar
 *     - Quickly delete all sidebars   
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */
?>
<form autocomplete="off" action="">
	<?php 
		/**
		 * Output New Sidebar Dialog Message
		 * 
		 * If there are no sidebars output a dialog message
		 * to prompt the user to create a new custom sidebar.
		 * 
		 */
		if ( $this->no_sidebars ) : ?>
			<div class="manage-sidebars manage-menus no-sidebars">
				<label><?php _e( 'Create a new sidebar for your theme:', 'easy-custom-sidebars' ); ?></label>
				<?php 
					submit_button(
						__( 'Create a New Sidebar', 'easy-custom-sidebars' ),
						'secondary',
						'create_new_sidebar',
						false,
						array( 'data-create-sidebar-url' => $this->create_url )
					);
				?>
			</div>
	<?php
		/**
		 * Output Custom Sidebar Table
		 * 
		 * If there are existing sidebars output a table that
		 * displays all custom sidebar instances.
		 * 
		 */	
		else : ?>
			<div class="manage-sidebars manage-menus sidebar-dialog no-sidebars">
				<label class="manage-label"><?php _e( 'Manage your sidebar replacements here or:', 'easy-custom-sidebars' ); ?></label>
				<label class="new-label"><?php _e( 'Create a new sidebar for your theme:', 'easy-custom-sidebars' ); ?></label>
				<?php 
					submit_button(
						__( 'Create a New Sidebar', 'easy-custom-sidebars' ),
						'secondary',
						'create_new_sidebar',
						false,
						array( 'data-create-sidebar-url' => $this->create_url )
					);
				?>
			</div>

			<!-- Sidebar Replacements Table -->
			<table id="sidebar-replacements-table" class="widefat fixed" cellspacing="0">
				<thead>
					<tr>
						<th class="manage-column column-sidebars"><?php _e( 'Sidebar Name', 'easy-custom-sidebars' ); ?></th>
						<th class="manage-column column-sidebars-widget-replacement"><?php _e( 'Default Sidebar To Replace', 'easy-custom-sidebars' ) ?></th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php $row_count = 0; ?>
					<?php while ( $this->sidebars->have_posts() ) : $this->sidebars->the_post(); ?>
						<?php 
							$row_class              = ( $row_count % 2 == 0 ) ? 'alternate' : '';
							$sidebar_id             = get_post_meta( get_the_ID(), 'sidebar_id', true );
							$sidebar_replacement_id = get_post_meta( get_the_ID(), 'sidebar_replacement_id', true );
							$selected_option        = false;
							$edit_link = esc_url(
								add_query_arg(
									array(
										'screen'  => 'edit',
										'action'  => 'edit',
										'sidebar' => $sidebar_id,
									),
									$this->admin_url
								)
							);
						?>
						<tr class="<?php echo $row_class; ?> sidebar-replacements-row">
							<td class="sidebar-replacement-title post-title page-title column-title">
								<div>
									<strong><a data-sidebar-reference="<?php echo $sidebar_id; ?>" class="sidebar-edit-link row-title" href="<?php echo $edit_link; ?>"><?php the_title(); ?></a></strong>
								</div>
								<div class="row-actions">
									<a data-sidebar-reference="<?php echo $sidebar_id; ?>" class="sidebar-edit-link" href="<?php echo $edit_link; ?>"><?php _e( 'Edit', 'easy-custom-sidebars' ); ?></a> | <a data-sidebar-reference="<?php echo $sidebar_id; ?>" class="sidebar-delete-link" href="#"><?php _e( 'Delete', 'easy-custom-sidebars' ); ?></a>
								</div>
							</td>

							<!-- Replacement Sidebar Select -->
							<?php 
								$controller = ECS_Widget_Areas::get_instance();
							?>
							<td class="default-widget-areas">
								<select data-sidebar-reference="<?php echo $sidebar_id; ?>" name="" id="">
									<?php foreach ( $controller->get_default_widget_areas() as $sidebar ) : ?>
											<option value="<?php echo $sidebar['id']; ?>" <?php if ( $sidebar_replacement_id == $sidebar['id'] ) : $selected_option = true; ?>selected<?php endif; ?>>
												<?php echo $sidebar['name'] ; ?>
											</option>
									<?php endforeach; ?>
									<?php if ( ! $selected_option ) : ?>
										<option value="0" selected>&mdash; <?php _e( 'Select a Sidebar', 'easy-custom-sidebars' ); ?> &mdash;</option>
									<?php else : ?>
										<option value="0">&mdash; <?php _e( 'Deactivate Sidebar', 'easy-custom-sidebars' ); ?> &mdash;</option>
									<?php endif; ?>
								</select>
							</td>
							<td><span class="spinner" style=""></span></td>	
						</tr>
						<?php $row_count++; ?>
					<?php endwhile; ?>	
				</tbody>
			</table>
			<?php 
				/**
				 * Create Delete All Sidebars Link
				 *
				 * Creates a button that will delete all custom
				 * sidebars that have been created by the user.
				 * 
				 */
			?>
			<a href="#" id="delete_all_sidebars"><?php _e( 'Delete All Sidebars', 'easy-custom-sidebars' ); ?></a>
	<?php endif; ?>
</form>
