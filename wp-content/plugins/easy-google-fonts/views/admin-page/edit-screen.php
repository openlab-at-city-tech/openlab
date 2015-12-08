<div id="edit-controls-wrap">
	<form id="update-control" action="" method="post">
		<div class="control-edit">
			<!-- Header -->
			<div id="edit-control-header">
				<div class="major-publishing-actions">
					<label for="menu-name" class="custom-control-label menu-name-label howto open-label">
						<span><?php _e( 'Control Name', $this->plugin_slug ); ?></span>
						<input autocomplete="off" type="text" value="<?php echo $control_name; ?>" title="<?php _e( 'Enter control name here', $this->plugin_slug ) ?>" class="custom-control-name regular-text menu-item-textbox input-with-default-title" id="custom-control-name" name="custom-control-name">
					</label>
					<div class="publishing-action">
						<span class="spinner"></span>
						<?php
							/**
							 * Build Save Redirect Link URL
							 * 
							 * Generate the first part of the URL and store it
							 * in a data attribute. This URL will have the rest
							 * of the query variables appended to it via AJAX.
							 *
							 * @since 1.0
							 * @version 1.1.1
							 * 
							 */
							$save_redirect_link = esc_url( 
													add_query_arg( 
														array( 
															'page'    => $this->plugin_slug,
															'action'  => 'edit',
															'dialog'  => 'updated',
															'control' => $this->control_selected_id
														), 
														admin_url( 'options-general.php' ) 
													) 
												); 

							submit_button( 
								__( 'Save Font Control', $this->plugin_slug ), 
								'primary', 
								'submit', 
								false, 
								array( 
									'id' => 'save_control_header', 
									'data-control-id'   => $this->control_selected_id,
									'data-redirect-url' => $save_redirect_link
								) 
							); 
						?>
					</div><!-- /.major-publishing-action -->
					<div class="clear"></div>
				</div><!-- /.major-publishing-actions -->
			</div>

			<!-- Body -->
			<div id="post-body">
				<div id="post-body-content">
						<h3><?php _e( 'Add CSS Selectors', $this->plugin_slug ); ?></h3>
						<div class="drag-instructions post-body-plain">
							<p><?php _e( 'Type each CSS selector that you would like this font control to manage in the box below. Use the tab key to separate each selector.', $this->plugin_slug ); ?></p>
						</div>
						<div>
							<ul id="tt-font-tags">								
								<?php $selectors = get_post_meta( $this->control_instance->ID, 'control_selectors', true ); ?>
								<?php if ( $selectors ) : ?>
									<?php foreach ( $selectors as $selector ) : ?>
										<li><?php echo $selector; ?></li>
									<?php endforeach; ?>
								<?php endif; ?>		
							</ul>
						</div>

						<h3><?php _e( 'Force Styles Override (Optional)', $this->plugin_slug ); ?></h3>
						<p><?php _e( "Please check the box below if you wish to override all of the styles for the selectors above that are forced in your theme's stylesheet.", $this->plugin_slug ); ?></p>
						<?php $force_styles = get_post_meta( $this->control_instance->ID, 'force_styles', true ); ?>
						<input autocomplete="off" id="control-force-styles" type="checkbox" <?php checked( $force_styles, true ); ?>>

				</div><!-- /#post-body-content -->
			</div><!-- /#post-body -->

			<!-- Footer -->
			<div id="edit-control-footer">
				<div class="major-publishing-actions">
					<?php
						/**
						 * Build Delete Link URL
						 * 
						 * Generate a unique edit URL for each custom
						 * font control.
						 * 
						 */
						$delete_link = '';
						$delete_link = esc_url( 
											add_query_arg( 
												array( 
													'page'    => $this->plugin_slug,
													'action'  => 'edit',
													'dialog'  => 'deleted',
													'name'    =>  str_replace ( ' ', '+', $control_name )
												), 
												admin_url( 'options-general.php' ) 
											) 
										);
					?>
					<span class="delete-action">
						<a data-redirect-url="<?php echo $delete_link; ?>" data-control-id="<?php echo $this->control_selected_id; ?>" id="delete-control" href="#" class="submitdelete deletion menu-delete"><?php _e( 'Delete Control', $this->plugin_slug ); ?></a>
					</span><!-- END .delete-action -->
					<div class="publishing-action">
						<span class="spinner"></span>
						<?php
							/**
							 * Build Save Redirect Link URL
							 * 
							 * Generate the first part of the URL and store it
							 * in a data attribute. This URL will have the rest
							 * of the query variables appended to it via AJAX.
							 *
							 * @since 1.0
							 * @version 1.1.1
							 * 
							 */
							$save_redirect_link = esc_url( 
													add_query_arg( 
														array( 
															'page'    => $this->plugin_slug,
															'action'  => 'edit',
															'dialog'  => 'updated',
															'control' => $this->control_selected_id
														), 
														admin_url( 'options-general.php' ) 
													) 
												); 

							submit_button( 
								__( 'Save Font Control', $this->plugin_slug ), 
								'primary', 
								'submit', 
								false, 
								array( 
									'id' => 'save_control_header', 
									'data-control-id' => $this->control_selected_id,
									'data-redirect-url' => $save_redirect_link
								) 
							); 
						?>
					</div><!-- END .publishing-action -->
					<div class="clear"></div>
				</div><!-- /.major-publishing-actions -->
			</div><!-- /#post-body -->

		</div><!-- /.control-edit -->
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
			wp_nonce_field( 'tt_font_edit_control_instance', 'tt_font_edit_control_instance_nonce' );
			wp_nonce_field( 'tt_font_delete_control_instance', 'tt_font_delete_control_instance_nonce' );
			wp_nonce_field( 'tt_font_create_control_instance', 'tt_font_create_control_instance_nonce' );
		?>	
	</form>
</div><!-- /#edit-controls-wrap -->