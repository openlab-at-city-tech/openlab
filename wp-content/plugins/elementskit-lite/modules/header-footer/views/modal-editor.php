<div class="attr-modal attr-fade" id="elementskit_headerfooter_modal" tabindex="-1" role="dialog" aria-labelledby="elementskit_headerfooter_modalLabel">
	<div class="attr-modal-dialog attr-modal-dialog-centered" role="document">
		<form action="" method="get" id="elementskit-template-modalinput-form" data-open-editor="0" data-editor-url="<?php echo esc_url(get_admin_url()); ?>" data-nonce="<?php echo esc_attr(wp_create_nonce( 'wp_rest' )); ?>">
			<!-- <input type="hidden" name="post_author" value ="<?php //echo get_current_user_id(); ?>"> -->
			<div class="attr-modal-content">
				<div class="attr-modal-header">
					<button type="button" class="attr-close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="attr-modal-title" id="elementskit_headerfooter_modalLabel"><?php esc_html_e( 'Template Settings', 'elementskit-lite' ); ?></h4>
				</div>
				<div class="attr-modal-body" id="elementskit_headerfooter_modal_body">
					<div class="ekit-input-group">
						<label class="attr-input-label"><?php esc_html_e( 'Title:', 'elementskit-lite' ); ?></label>
						<input required type="text" name="title" class="ekit-template-modalinput-title attr-form-control">
					</div>
					<br />
					<div class="ekit-input-group">
						<label class="attr-input-label"><?php esc_html_e( 'Type:', 'elementskit-lite' ); ?></label>
						<select name="type" class="ekit-template-modalinput-type attr-form-control">
							<option value="header"><?php esc_html_e( 'Header', 'elementskit-lite' ); ?></option>
							<option value="footer"><?php esc_html_e( 'Footer', 'elementskit-lite' ); ?></option>
						</select>
					</div>
					<br />

					<div class="ekit-template-headerfooter-option-container">
						<div class="ekit-input-group">
							<label class="attr-input-label"><?php esc_html_e( 'Conditions:', 'elementskit-lite' ); ?></label>
							<select name="condition_a" class="ekit-template-modalinput-condition_a attr-form-control">
								<option value="entire_site"><?php esc_html_e( 'Entire Site', 'elementskit-lite' ); ?></option>
								<option value="singular"><?php esc_html_e( 'Singular (Only Pro)', 'elementskit-lite' ); ?></option>
								<option value="archive"><?php esc_html_e( 'Archive (Only Pro)', 'elementskit-lite' ); ?></option>
							</select>
						</div>
						<br>

						<div class="ekit-template-modalinput-condition_singular-container">
							<div class="ekit-input-group">
								<label class="attr-input-label"></label>
								<select name="condition_singular" class="ekit-template-modalinput-condition_singular attr-form-control">
									<option value="all"><?php esc_html_e( 'All Singulars (Only Pro)', 'elementskit-lite' ); ?></option>
									<option value="front_page"><?php esc_html_e( 'Front Page (Only Pro)', 'elementskit-lite' ); ?></option>
									<option value="all_posts"><?php esc_html_e( 'All Posts (Only Pro)', 'elementskit-lite' ); ?></option>
									<option value="all_pages"><?php esc_html_e( 'All Pages (Only Pro)', 'elementskit-lite' ); ?></option>
									<option value="selective"><?php esc_html_e( 'Selective Singular (Only Pro)', 'elementskit-lite' ); ?></option>
									<option value="404page"><?php esc_html_e( '404 Page (Only Pro)', 'elementskit-lite' ); ?></option>
								</select>
							</div>
							<br>

							<div class="ekit-template-modalinput-condition_singular_id-container ekit_multipile_ajax_search_filed">
								<div class="ekit-input-group">
									<label class="attr-input-label"></label>
									<select multiple name="condition_singular_id" class="ekit-template-modalinput-condition_singular_id"></select>
								</div>
								<br />
							</div>
							<br>
						</div>

						<div class="ekit-switch-group">
							<label class="attr-input-label"><?php esc_html_e( 'Activate/Deactivate:', 'elementskit-lite' ); ?></label>
							<div class="ekit-admin-input-switch">
								<input checked="" type="checkbox" value="yes"
									class="ekit-admin-control-input ekit-template-modalinput-activition"
									name="activation" id="ekit_activation_modal_input">
								<label class="ekit-admin-control-label" for="ekit_activation_modal_input">
									<span class="ekit-admin-control-label-switch" data-active="ON"
										data-inactive="OFF"></span>
								</label>
							</div>
						</div>
					</div>
					<br>
				</div>
				<div class="attr-modal-footer">
					<button type="button" class="attr-btn attr-btn-default elementskit-template-save-btn-editor"><?php esc_html_e( 'Edit content', 'elementskit-lite' ); ?></button>
					<button type="submit" class="attr-btn attr-btn-primary elementskit-template-save-btn"><?php esc_html_e( 'Save changes', 'elementskit-lite' ); ?></button>
				</div>
				<div class="ekit-spinner"></div>
			</div>
		</form>
	</div>
</div>
