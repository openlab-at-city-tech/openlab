<div class="attr-modal attr-fade" id="attr_menu_control_panel_modal" tabindex="-1" role="dialog">
	<div class="attr-modal-dialog attr-modal-dialog-centered" role="document">
		<div class="attr-modal-content ekit_menu_modal_content">
			<div class="attr-modal-header">
			<ul class="tb-nav tb-nav-tabs ekit_menu_control_nav" role="tablist">
					<li role="presentation" id="attr_content_nav" class="attr-active"><a class="attr-nav-link" href="#attr_content_tab" aria-controls="attr_content_tab"
							role="tab" data-attr-toggle="tab"><?php esc_html_e( 'Content', 'elementskit-lite' ); ?></a></li>
					<li role="presentation" id="attr_icon_nav"><a class="attr-nav-link ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal" href="#attr_icon_tab" aria-controls="attr_icon_tab" role="tab"
							data-attr-toggle="tab"><?php esc_html_e( 'Icon', 'elementskit-lite' ); ?></a></li>
					<li role="presentation" id="attr_badge_nav"><a class="attr-nav-link ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal" href="#attr_badge_tab" aria-controls="attr_badge_tab"
							role="tab" data-attr-toggle="tab"><?php esc_html_e( 'Badge', 'elementskit-lite' ); ?></a></li>
					<li role="presentation" id="attr_badge_nav"><a class="attr-nav-link" href="#attr_vertical_menu_setting_tab" aria-controls="attr_vertical_menu_setting_tab"
							role="tab" data-attr-toggle="tab"><?php esc_html_e( 'Settings', 'elementskit-lite' ); ?></a></li>
				</ul>
			</div>
			<div class="attr-modal-body ekit-wid-con">
				<div class="attr-tab-content">
					<div role="tabpanel" class="attr-tab-pane attr-active" id="attr_content_tab">
						<?php if ( defined( 'ELEMENTOR_VERSION' ) ) : ?>
						<div class="switch-wrapper">
							<input type="checkbox" value="1" id="elementskit-menu-item-enable" />
							<label for="elementskit-menu-item-enable"><span><em></em></span></label>
						</div>
						<div id="elementskit-menu-builder-warper">
							<small
								class="elementskit-menu-mega-submenu enabled_item"><?php esc_html_e( 'Megamenu enabled', 'elementskit-lite' ); ?></small>
							<small
								class="elementskit-menu-mega-submenu disabled_item"><?php esc_html_e( 'Megamenu disabled', 'elementskit-lite' ); ?></small>

							<button disabled type="button" id="elementskit-menu-builder-trigger"
								class="elementskit-menu-elementor-button button" data-attr-toggle="modal"
								data-target="#elementskit-menu-builder-modal">
								<img src="<?php echo esc_url( $this->url ); ?>/assets/images/elementor-icon.png"
									alt="elementskit megamenu" />
								<?php esc_html_e( 'Edit megamenu content', 'elementskit-lite' ); ?>
							</button>

							<div id="mobile_submenu_content_type" class="ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal-container">
								<strong><?php esc_html_e( 'Use mobile submenu as:', 'elementskit-lite' ); ?></strong>
								<span><input type="radio" name="content_type" checked value="builder_content"> <?php esc_html_e( 'builder content', 'elementskit-lite' ); ?></span>
								<span><input type="radio" name="content_type" value="submenu_list"> <?php esc_html_e( 'wp submenu list', 'elementskit-lite' ); ?></span>
							</div>
						</div>
						<?php else : ?>
						<p class="no-elementor-notice">
							<?php esc_html_e( 'This plugin requires Elementor page builder to edt megamenu items content', 'elementskit-lite' ); ?>
						</p>
						<?php endif; ?>
					</div>
					<div role="tabpanel" class="attr-tab-pane" id="attr_icon_tab">
						<table class="option-table ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal-container">
							<tbody>
								<tr>
									<td><strong><?php esc_html_e( 'Choose icon color', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="text" value="#bada55" class="elementskit-menu-wpcolor-picker"
											id="elementskit-menu-icon-color-field" />
									</td>
								</tr>
								<tr>
									<td><strong><?php esc_html_e( 'Select icon', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<select id="elementskit-menu-icon-field" class="elementskit-menu-icon-picker">
											<option value=""><?php esc_html_e( 'No icon', 'elementskit-lite' ); ?></option>
											<?php
											foreach ( self::get_icons() as $icon_class ) {
												echo "<option value='" . esc_attr( $icon_class ) . "'>'" . esc_html( $icon_class ) . "'</option>";
											}
											?>
										</select>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div role="tabpanel" class="attr-tab-pane" id="attr_badge_tab">
						<table class="option-table ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal ekit-<?php echo esc_attr(ElementsKit_Lite::package_type()); ?>-labal-container">
							<tbody>
								<tr>
									<td><strong><?php esc_html_e( 'Badge text', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="text"
											placeholder="<?php esc_html_e( 'Badge Text', 'elementskit-lite' ); ?>"
											id="elementskit-menu-badge-text-field" />
									</td>
								</tr>

								<tr>
									<td><strong><?php esc_html_e( 'Choose badge color', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="text" class="elementskit-menu-wpcolor-picker" value="#ffffff"
											id="elementskit-menu-badge-color-field" />
									</td>
								</tr>

								<tr>
									<td><strong><?php esc_html_e( 'Choose badge background', 'elementskit-lite' ); ?></strong>
									</td>
									<td class="alignright">
										<input type="text" class="elementskit-menu-wpcolor-picker" value="#bada55"
											id="elementskit-menu-badge-background-field" />
									</td>
								</tr>

							</tbody>
						</table>
					</div>
					<div role="tabpanel" class="attr-tab-pane" id="attr_vertical_menu_setting_tab">
						<table class="option-table">
							<tbody class="xs_menu_settings_panel">
								<tr id="xs_megamenu_width_type">
									<td><strong><?php esc_html_e( 'Mega Menu Width as:', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright ekit_width_lists">
										<input type="radio" name="width_type" id="width_type_default" value="default_width" checked>
										<label for="width_type_default"><?php esc_html_e( 'Default Width', 'elementskit-lite' ); ?></label>
										<input type="radio" id="width_type_full" name="width_type" value="full_width">
										<label for="width_type_full"><?php esc_html_e( 'Full Width', 'elementskit-lite' ); ?></label>
										<input type="radio" id="width_type_custom" name="width_type" value="custom_width">
										<label for="width_type_custom"><?php esc_html_e( 'Custom Width', 'elementskit-lite' ); ?></label>
									</td>
								</tr>
								<tr class="menu-width-container">
									<td><strong><?php esc_html_e( 'Menu Width', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="text" placeholder="<?php esc_html_e( '750px', 'elementskit-lite' ); ?>" id="elementskit-menu-vertical-menu-width-field" />
									</td>
								</tr>
								<tr id="vertical_megamenu_position_type">
									<td><strong><?php esc_html_e( 'Mega Menu Position as:', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="radio" id="position_type_top" name="position_type" value="top_position">
										<label for="position_type_top"><?php esc_html_e( 'Default', 'elementskit-lite' ); ?></label>
										<input type="radio" name="position_type" id="position_type_relative" checked value="relative_position">
										<label for="position_type_relative"><?php esc_html_e( 'Relative', 'elementskit-lite' ); ?></label>
									</td>
								</tr>
								<tr id="enable_ajax_load">
									<td><strong><?php esc_html_e( 'Enable Ajax Load:', 'elementskit-lite' ); ?></strong></td>
									<td class="alignright">
										<input type="radio" id="ajax_load_yes" name="megamenu_ajax_load" value="yes">
										<label for="ajax_load_yes"><?php esc_html_e( 'Yes', 'elementskit-lite' ); ?></label>
										<input type="radio" id="ajax_load_no" name="megamenu_ajax_load" checked value="no">
										<label for="ajax_load_no"><?php esc_html_e( 'No', 'elementskit-lite' ); ?></label>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="attr-modal-footer">
				<input type="hidden" id="elementskit-menu-modal-menu-id">
				<input type="hidden" id="elementskit-menu-modal-menu-has-child">
				<div class="clearfix ekit-modal-controls">
					<div class="left-content">
						<button class="btn-modal-close" type="button" data-dismiss="modal">
							<svg width="14" height="14" viewBox="0 0 14 14" xmlns="https://www.w3.org/2000/svg">
								<line fill="none" stroke="#000" stroke-width="1.1" x1="1" y1="1" x2="13" y2="13"></line>
								<line fill="none" stroke="#000" stroke-width="1.1" x1="13" y1="1" x2="1" y2="13"></line>
							</svg>
						</button>
						
					</div>
					<div class="right-content">
					<a class="button button-default alignleft" href="https://help.wpmet.com/docs/mega-menu-module/" target="_blank" >Megamenu Turorial</a>

						<span class='spinner'></span>
						<?php echo wp_kses( get_submit_button( esc_html__( 'Save', 'elementskit-lite' ), 'elementskit-menu-item-save button-primary alignright', '', false ), ['input'=>['class'=>[], 'type'=>[], 'value'=>[]]] ); ?>
					</div>
				</div>
			</div>
			<span id="elementskit-menu-modal-spinner" class='spinner'></span>
		</div>
	</div>
</div>

<div class="attr-modal attr-fade" id="elementskit-menu-builder-modal" tabindex="-1" role="dialog"
	aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
	<div class="attr-modal-dialog attr-modal-dialog-centered" role="document">
		<div class="attr-modal-content">
			<div class="attr-modal-body">
				<button class="ekit_close" type="button" data-dismiss="modal"><svg width="20" height="20"
						viewBox="0 0 20 20" xmlns="https://www.w3.org/2000/svg">
						<line fill="none" stroke="#fff" stroke-width="1.4" x1="1" y1="1" x2="19" y2="19"></line>
						<line fill="none" stroke="#fff" stroke-width="1.4" x1="19" y1="1" x2="1" y2="19"></line>
					</svg></button>
				<iframe id="elementskit-menu-builder-iframe" src="" frameborder="0"></iframe>
			</div>
		</div>
	</div>
</div>
