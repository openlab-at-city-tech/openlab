<?php if ( current_user_can( 'manage_options' ) ): ?>
	    <ul class="johannes-menu">
	        <li>
	            <a href="<?php echo esc_url( admin_url( 'nav-menus.php' )); ?>">
				   <?php esc_html_e( 'Click here to add menu', 'johannes' ); ?>
				</a>
	        </li>
	    </ul>
<?php endif; ?>