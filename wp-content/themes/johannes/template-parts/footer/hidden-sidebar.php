<div class="johannes-sidebar johannes-sidebar-hidden">

	<div class="johannes-sidebar-branding">
	    <?php echo johannes_get_branding( true ); ?>
	    <span class="johannes-action-close"><i class="jf jf-close" aria-hidden="true"></i></span>
	</div>	

	<?php $display_class = johannes_get('header', 'nav') ? 'd-md-block d-lg-none' : ''; ?>
	<div class="johannes-menu-mobile widget <?php echo esc_attr( $display_class ); ?>">
		<div class="widget-inside johannes-bg-alt-1">
		<h4 class="widget-title"><?php echo __johannes('menu_label'); ?></h4>
			<?php get_template_part('template-parts/header/elements/menu-primary'); ?>
			<?php if( johannes_get('header', 'actions_responsive') ): ?>
                <?php foreach( johannes_get('header', 'actions_responsive') as $element ): ?>
                    <?php get_template_part('template-parts/header/elements/' . $element ); ?>
                <?php endforeach; ?>
            <?php endif; ?>
		</div>
	</div>

	<?php if ( is_active_sidebar('johannes_sidebar_hidden') ) : ?>
	    <?php dynamic_sidebar( 'johannes_sidebar_hidden' ); ?>
    <?php endif; ?>

</div>