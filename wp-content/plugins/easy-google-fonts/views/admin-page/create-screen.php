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
							 * Build Edit Redirect Link URL
							 * 
							 * Generate the first part of the URL and store it
							 * in a data attribute. This URL will have the rest
							 * of the query variables appended to it via AJAX.
							 *
							 * @since 1.0
							 * @version 1.1.1
							 * 
							 */
							$edit_redirect_link = esc_url( 
											add_query_arg( 
												array( 
													'page'    => $this->plugin_slug,
													'action'  => 'edit'
												), 
												admin_url( 'options-general.php' ) 
											) 
										);

							// Create submit button
							submit_button( 
								__( 'Create Font Control', $this->plugin_slug ), 
								'primary', 
								'submit', 
								false, 
								array( 
									'id'                => 'create_control_header',
									'data-redirect-url' => $edit_redirect_link
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
					<p class="post-body-plain"><?php _e( 'Give your font control a name above, then click Create Font Control.', $this->plugin_slug ); ?></p>
				</div><!-- /#post-body-content -->
			</div><!-- /#post-body -->

			<!-- Footer -->
			<div id="edit-control-footer">
				<div class="major-publishing-actions">
					<span class="delete-action">
						<?php $delete_link = $this->admin_url; ?>
						<a data-redirect-url="<?php echo $delete_link; ?>" data-control-id="<?php echo $this->control_selected_id; ?>" id="delete-control" href="#" class="submitdelete deletion menu-delete"><?php _e( 'Delete Control', $this->plugin_slug ); ?></a>
					</span><!-- END .delete-action -->
					<div class="publishing-action">
						<span class="spinner"></span>
							<?php 
								/**
								 * Build Edit Redirect Link URL
								 * 
								 * Generate the first part of the URL and store it
								 * in a data attribute. This URL will have the rest
								 * of the query variables appended to it via AJAX.
								 *
								 * @since 1.0
								 * @version 1.1.1
								 * 
								 */
								$edit_redirect_link = esc_url( 
												add_query_arg( 
													array( 
														'page'    => $this->plugin_slug,
														'action'  => 'edit'
													), 
													admin_url( 'options-general.php' ) 
												) 
											);

								// Create submit button
								submit_button( 
									__( 'Create Font Control', $this->plugin_slug ), 
									'primary', 
									'submit', 
									false, 
									array( 
										'id'                => 'create_control_header',
										'data-redirect-url' => $edit_redirect_link
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