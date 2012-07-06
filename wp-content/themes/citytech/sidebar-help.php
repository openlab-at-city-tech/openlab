<?php
/**
 * The Help Sidebar
 *
 */?>

        <h2 class="sidebar-title">Help</h2>
    	<?php $args = array(
				'theme_location' => 'helpmenu',
				'container' => 'div',
                'container_id' => 'help-menu',
				'menu_class' => 'sidbar-nav'
			);
		
		wp_nav_menu( $args ); ?>