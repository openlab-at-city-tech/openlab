<?php if ( has_nav_menu( 'johannes_menu_social' ) ): ?>
    <?php
	wp_nav_menu(
		array(
			'theme_location' => 'johannes_menu_social',
			'container'      => '',
			'items_wrap' => '<ul id="%1$s" class="%2$s"><li class="header-el-label">'.__johannes( 'social_label' ).'</li>%3$s</ul>',
			'menu_class'     => 'johannes-menu johannes-menu-social',
			'link_before'    => '<span>',
			'link_after'     => '</span>'
		)
	);
?>
<?php else: ?>
	<?php get_template_part('template-parts/header/elements/menu-placeholder'); ?>
<?php endif; ?>
