<div class="attr-row ekit_tab_wraper_group">
	<div class="attr-col-lg-3 attr-col-md-4">
		<div class="ekit_logo">
			<img src="<?php echo esc_url(self::get_url() . 'assets/images/logo-ekit.png'); ?>" height="40" />
		</div>
		<div class="ekit-admin-nav" id="v-elementskit-tab" role="tablist" aria-orientation="vertical">
			<ul class="attr-nav attr-nav-tabs">
				<li><a href="#" class="ekit-admin-nav-link ekit-admin-nav-hidden top"></a></li>

				<!-- settings_sections nav begins -->
				<?php 
				$count = 0;
				foreach ( $settings_sections as $section_key => $section ) :
					reset( $settings_sections ); 
					$attr_section_key = ( $section_key !== key( $settings_sections ) ) ? '' : 'attr-active';
					?>
				<li role="presentation" class="<?php echo esc_attr($attr_section_key) ?>">
					<a class="ekit-admin-nav-link <?php echo $count == 1 ? 'bottom' : ''; ?>" id="v-elementskit-<?php echo esc_attr( $section_key ); ?>-tab" data-attr-toggle="pill" href="#v-elementskit-<?php echo esc_attr( $section_key ); ?>" role="tab"
						aria-controls="v-elementskit-<?php echo esc_attr( $section_key ); ?>" data-attr-toggle="tab" role="tab">
						<div class="ekit-admin-tab-content">
							<span class="ekit-admin-title"><?php echo esc_html( $section['title'] ); ?></span>
							<span class="ekit-admin-subtitle"><?php echo esc_html( $section['sub-title'] ); ?></span>
						</div>
						<div class="ekit-admin-tab-icon">
							<i class="<?php echo esc_attr( $section['icon'] ); ?>"></i>
						</div>
					</a>
				</li>
								<?php 
								$count++;
endforeach; 
				?>
				<!-- settings_sections nav ends -->

				<?php if ( \ElementsKit_Lite::package_type() == 'free' ) : ?>
				<li role="presentation" class="ekit-go-pro-nav-tab">
					<a class="ekit-admin-nav-link" id="v-elementskit-ekit-go-pro-nav-tab" href="https://wpmet.com/elementskit-pricing" role="tab" target="_blank">
						<div class="ekit-admin-tab-content">
							<span class="ekit-admin-title"><?php echo esc_html__( 'Go Premium', 'elementskit-lite' ); ?></span>
							<span class="ekit-admin-subtitle"><?php echo esc_html__( 'Get premium features', 'elementskit-lite' ); ?></span>
						</div>
						<div class="ekit-admin-tab-icon">
							<img src="<?php echo esc_url(self::get_url() . 'assets/images/loader-krasi.gif'); ?>" class="ekit-go-pro-gif" alt="elementskit go pro premium" />
						</div>
					</a>
				</li>
				<?php endif; ?>

			<li><a href="#" class="ekit-admin-nav-link ekit-admin-nav-hidden"></a></li>
			</ul>
		</div>
	</div>
	<div class="attr-col-lg-9 attr-col-md-8">
		<div class="attr-tab-content" id="v-elementskit-tabContent">

			<!-- settings_sections content begins -->
			<?php 
			foreach ( $settings_sections as $section_key => $section ) :
				reset( $settings_sections ); 
				$attr_section_key = ( $section_key !== key( $settings_sections ) ) ? '' : 'attr-active';
				?>
				<div class="attr-tab-pane <?php echo esc_attr($attr_section_key) ?>" id="v-elementskit-<?php echo esc_attr( $section_key ); ?>" role="tabpanel" aria-labelledby="v-elementskit-tab-<?php echo esc_attr( $section_key ); ?>">
					<div class="ekit-admin-section-header">
						<h2 class="ekit-admin-section-heaer-title"><i class="<?php echo esc_attr( $section['icon'] ); ?>"></i><?php echo esc_html( $section['title'] ); ?></h2>
								
						<?php if ( in_array( $section_key, array( 'widgets', 'modules' ) ) ) : ?>
							<div class="attr-input attr-input-switch ekit-content-type-free ekit-all-element-switch">
								<div class="ekit-admin-input-switch attr-card-body">
									<input checked="" type="checkbox" value="image-accordion" class="ekit-admin-control-input ekit-all-control-input" name="ekit_all_activation_input_widget_<?php echo esc_attr( $section_key ); ?>" id="ekit_all_activation_input_widget_<?php echo esc_attr( $section_key ); ?>">
									<label class="ekit-admin-control-label" for="ekit_all_activation_input_widget_<?php echo esc_attr( $section_key ); ?>">
										Disable All
										<span class="ekit-admin-control-label-switch" data-active="ON"
											data-inactive="OFF"></span>
										Enable All
									</label>                                        
								</div>
							</div>
						<?php endif; ?>
						<div class="ekit-admin-input-switch">
							<button class="attr-btn-primary attr-btn ekit-admin-settings-form-submit"><div class="ekit-spinner"></div><i class="ekit-admin-save-icon fa fa-check-circle"></i><?php esc_html_e( 'Save Changes', 'elementskit-lite' ); ?></button>
						</div>
					</div>
					<?php 
					include isset( $section['view_path'] )
						? $section['view_path']
						: self::get_dir() . 'views/settings-sections/' . $section_key . '.php'; 
					?>
				</div>
			<?php endforeach; ?>
			<!-- settings_sections content ends -->

		</div>
	</div>
</div>
