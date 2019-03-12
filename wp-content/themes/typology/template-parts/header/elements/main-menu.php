<?php if( typology_header_display('main-menu') ) : ?>
	<?php if ( has_nav_menu( 'typology_main_menu' ) ) : ?>
		<?php wp_nav_menu( array( 'theme_location' => 'typology_main_menu', 'container'=> '', 'menu_class' => 'typology-nav typology-main-navigation',  ) ); ?>
	<?php else: ?>
		<?php if ( current_user_can( 'manage_options' ) ): ?>
			<ul class="typology-nav">
				<li><a href="<?php echo esc_url( admin_url( 'nav-menus.php' )); ?>"><?php esc_html_e( 'Click here to add main navigation', 'typology' ); ?></a></li>
			</ul>
		<?php endif; ?>
	<?php endif; ?>
<?php endif; ?>