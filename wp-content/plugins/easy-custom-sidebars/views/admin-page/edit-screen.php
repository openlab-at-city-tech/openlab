<?php 
/**
 * Edit Sidebar Screen View
 *
 * This view displays the output for the edit screen
 * for each custom sidebar setttings screen in the 
 * WordPress Administration Area.       
 *
 * @package     Easy_Custom_Sidebars
 * @author      Sunny Johal - Titanium Themes <support@titaniumthemes.com>
 * @license     GPL-2.0+
 * @copyright   Copyright (c) 2015, Titanium Themes
 * @version     1.0.9
 * 
 */

/**
 * Build Save Redirect Link URL
 * 
 * Generate the first part of the URL and store it
 * later in a data attribute. This URL will have the 
 * rest of the query variables appended to it via AJAX.
 *
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
$save_redirect_link = esc_url( 
				add_query_arg( 
					array( 
						'page'    => $this->plugin_slug,
						'action'  => 'edit',
						'sidebar' => $this->sidebar_selected_id,
						'dialog'  => 'updated'
					), 
					admin_url( 'themes.php' ) 
				) 
			);

/**
 * Build Delete Link URL
 * 
 * Generate a unique edit URL for each custom
 * sidebar.
 *
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
$delete_link = esc_url( 
				add_query_arg( 
					array( 
						'page'    => $this->plugin_slug,
						'dialog'  => 'deleted',
						'name'    => str_replace( ' ', '+', $this->sidebar_instance->post_title ),
					), 
					admin_url( 'themes.php' ) 
				) 
			);
?>
<div id="sidebars-frame">
	<!-- Sidebar -->
	<div id="sidebar-all-pages-column" class="metabox-holder">
		<form id="sidebar-meta" action="" class="sidebar-meta" method="post" enctype="multipart/form-data">
			<?php $this->do_accordion_sections(); ?>
		</form><!-- END #sidebar-meta -->
	</div><!-- END #sidebar-all-pages-column -->

	<!-- Management -->
	<div id="sidebar-management-liquid">
		<div id="sidebar-management">
			<form autocomplete="off" id="update-sidebar" enctype="multipart/form-data" method="post" action="">
				<div class="sidebar-edit">

					<!-- Header -->
					<div id="sidebar-header">
						<div class="major-publishing-actions">
							<!-- Sidebar Name Input -->
							<label for="custom-sidebar-name" class="custom-sidebar-name-label howto open-label">
								<span><?php _e( 'Sidebar Name', 'easy-custom-sidebars' ); ?></span>
								<input autocomplete="off" type="text" value="<?php echo $this->sidebar_instance->post_title; ?>" title="<?php _e( 'Enter sidebar name here', 'easy-custom-sidebars' ); ?>" class="custom-sidebar-name regular-text menu-item-textbox input-with-default-title" id="custom-sidebar-name" name="custom-sidebar-name">
							</label>
							<div class="publishing-action">
								<span class="spinner"></span>
								<?php 
									submit_button(
										__( 'Save Sidebar', 'easy-custom-sidebars' ),
										'primary',
										'submit',
										false,
										array(
											'id'                => 'save_sidebar_header',
											'data-sidebar-id'    => $this->sidebar_selected_id,
											'data-redirect-url' => $save_redirect_link,
										)
									);
								?>
							</div>
							<div class="clear"></div>
						</div><!-- END .major-publishing-actions -->
					</div><!-- END #sidebar-header -->
					
					<!-- Post Body -->
					<div id="post-body">
						<div id="post-body-content">
							<h3><?php _e( 'Sidebar Replacement Pages', 'easy-custom-sidebars' ); ?></h3>
							<div class="drag-instructions post-body-plain" style="display:none;">
								<p>
									<?php _e( "Drag each item into the order you prefer. Click the arrow on the right of the item to reveal additional configuration options. Please ensure that any items added to this sidebar contain the default 'Sidebar to Replace' widget area selected in the sidebar properties below.", 'easy-custom-sidebars' ); ?>
								</p>
							</div>
							<div id="sidebar-instructions" class="post-body-plain" style="display:none;">
								<p>
									<?php _e( "Add items from the column on the left. Please ensure that any items added to this sidebar contain the default 'Sidebar to Replace' widget area selected in the sidebar properties below.", 'easy-custom-sidebars' ); ?>
								</p>
							</div>	

							<!-- Sidebar Items -->
							<ul class="sidebar nav-menus-php" id="sidebar-to-edit">
								<?php
									/**
									 * Load Sidebar Attachment
									 * 
									 * Loads all of the attachments assigned to 
									 * this custom sidebar.
									 *
									 * @since 1.0.1
									 * @version 1.0.9
									 * 
									 */
									$controller = ECS_Admin::get_instance();
									echo $controller->get_sidebar_attachment_markup( $this->sidebar_selected_id );
								?>
							</ul>

							<div class="sidebar-settings menu-settings">
								<h3><?php _e( 'Sidebar Properties', 'easy-custom-sidebars' ) ?></h3>
								<div id="sidebar-properties-instructions">
									<p><?php _e( 'Edit information below.', 'easy-custom-sidebars' ); ?></p>
								</div>
								<dl>
									<dt class="howto"><?php _e('Sidebar to Replace', 'easy-custom-sidebars') ?></dt>
									<dd>
										<?php if( $default_widget_areas ) : ?>
										<select name="" id="sidebar_replacement_id">
											<?php foreach ( $default_widget_areas as $widget_area ) : ?>
												<option value="<?php echo $widget_area['id']; ?>" <?php if ( $widget_area['id'] == $replacement_id ) : ?>selected<?php endif; ?> ><?php echo $widget_area['name']; ?></option>
											<?php endforeach; ?>
												<option value="0" <?php if ( '0' == $replacement_id ) : ?>selected<?php endif; ?> >
													<?php if ( '0' == $replacement_id ) : ?>
														&mdash; <?php _e( 'Select a Sidebar', 'easy-custom-sidebars' ); ?> &mdash;
													<?php else : ?>
														&mdash; <?php _e( 'Deactivate Sidebar', 'easy-custom-sidebars' ); ?> &mdash;
													<?php endif; ?>
												</option>
										</select>
										<?php endif; ?>
									</dd>
									<div class="clear"></div>
								</dl>
								<dl>
									<dt class="howto"><?php _e('Sidebar Description', 'easy-custom-sidebars') ?></dt>
									<dd>
										<textarea id="sidebar_description" title="<?php _e( 'Enter sidebar description here', 'easy-custom-sidebars' ) ?>" class="custom-sidebar-name regular-text menu-item-textbox input-with-default-title"><?php echo $sidebar_description; ?></textarea>
									</dd>
									<div class="clear"></div>
								</dl>
							</div>

						</div><!-- END #post-body-content -->
					</div><!-- END #post-body -->
					
					<!-- Footer -->
					<div id="sidebar-footer">
						<div class="major-publishing-actions">
							<span class="delete-action">
								<a data-redirect-url="<?php echo $delete_link; ?>" data-sidebar-id="<?php echo $this->sidebar_selected_id; ?>" id="delete-sidebar" href="#" class="submitdelete deletion menu-delete"><?php _e( 'Delete Sidebar', 'easy-custom-sidebars' ); ?></a>
							</span>
							<div class="publishing-action">
								<span class="spinner"></span>
								<?php 
									submit_button(
										__( 'Save Sidebar', 'easy-custom-sidebars' ),
										'primary',
										'submit',
										false,
										array(
											'id'                => 'save_sidebar_footer',
											'data-sidebar-id'    => $this->sidebar_selected_id,
											'data-redirect-url' => $save_redirect_link,
										)
									);
								?>
							</div>
							<div class="clear"></div>	
						</div><!-- END .major-publishing-actions -->
					</div><!-- END #sidebar-footer -->
				</div><!-- END .sidebar-edit -->
			</form><!-- END #update-sidebar -->
		</div><!-- END #sidebar-management -->
	</div><!-- END #sidebar-management-liquid -->
</div>
