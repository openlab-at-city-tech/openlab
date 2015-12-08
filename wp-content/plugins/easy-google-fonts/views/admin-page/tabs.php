<!-- Screen Navigation -->
	<?php screen_icon(); ?>
	<h2 class="nav-tab-wrapper">
		<a href="<?php echo $this->admin_url; ?>" class="nav-tab <?php if ( $this->is_edit_screen() || $this->is_create_screen() ) { echo 'nav-tab-active'; } ?>"> 
			<?php esc_html_e( 'Edit Font Controls', 'theme-translate' ); ?>
		</a>
		<a href="<?php echo $this->manage_url; ?>" class="nav-tab <?php if ( $this->is_manage_screen() ) { echo 'nav-tab-active'; } ?>">
			<?php esc_html_e( 'Manage Font Controls', 'theme-translate' ); ?>
		</a>
		<a href="<?php echo $this->advanced_url; ?>" class="nav-tab <?php if ( $this->is_advanced_screen() ) { echo 'nav-tab-active'; } ?>">
			<?php esc_html_e( 'Advanced', 'theme-translate' ); ?>
		</a>
	</h2>