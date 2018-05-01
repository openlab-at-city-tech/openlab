<?php 
/**
 * Create Sidebar Screen View
 *
 * This view displays the output for the create screen
 * for the custom sidebar setttings screen in the 
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
 * Build Edit Redirect Link URL
 * 
 * Generate the first part of the URL and store it
 * later in a data attribute. This URL will have the 
 * rest of the query variables appended to it via AJAX.
 *
 * @since 1.0.1
 * @version 1.0.9
 * 
 */
$edit_redirect_link = esc_url( 
				add_query_arg( 
					array( 
						'page'    => $this->plugin_slug,
						'action'  => 'edit'
					), 
					admin_url( 'admin.php' ) 
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
$delete_link = $this->admin_url;
?>
<div id="sidebars-frame">
	<!-- Sidebar -->
	<div id="sidebar-all-pages-column" class="metabox-holder metabox-holder-disabled">
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
								<input autocomplete="off" type="text" value="" title="<?php _e( 'Enter sidebar name here', 'easy-custom-sidebars' ); ?>" class="custom-sidebar-name regular-text menu-item-textbox input-with-default-title" id="custom-sidebar-name" name="custom-sidebar-name">
							</label>
							<div class="publishing-action">
								<span class="spinner"></span>
								<?php 
									submit_button(
										__( 'Create Sidebar', 'easy-custom-sidebars' ),
										'primary',
										'submit',
										false,
										array(
											'id'                => 'create_sidebar_header',
											'data-redirect-url' => $edit_redirect_link,
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
							<h3><?php _e( 'Create New Sidebar', 'easy-custom-sidebars' ); ?></h3>
							<div id="sidebar-instructions" class="post-body-plain" style="display:none;">
								<p class="post-body-plain"><?php _e( 'Give your sidebar a name above, then click Create Sidebar.', 'easy-custom-sidebars' ) ?></p>
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
										__( 'Create Sidebar', 'easy-custom-sidebars' ),
										'primary',
										'submit',
										false,
										array(
											'id'                => 'create_sidebar_footer',
											'data-sidebar-id'    => $this->sidebar_selected_id,
											'data-redirect-url' => $edit_redirect_link,
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
