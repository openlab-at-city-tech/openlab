<div class="typology-sidebar">
	<div class="typology-sidebar-header">
		<div class="typology-sidebar-header-wrapper">
			<?php get_template_part('template-parts/header/elements/logo'); ?>
			<span class="typology-sidebar-close"><i class="fa fa-times" aria-hidden="true"></i></span>
		</div>
	</div>

	<div class="widget typology-responsive-menu">
		<?php get_template_part('template-parts/header/elements/main-menu'); ?>
	</div>

	<?php if( typology_header_display('sidebar-button') ): ?>
			<?php $sidebar = typology_sidebar(); ?>	
			<?php if ( is_active_sidebar( $sidebar ) ) : ?>
					<?php dynamic_sidebar( $sidebar ); ?>
			<?php else: ?>
				<div class="widget">
					<div class="textwidget">
						<p><?php echo wp_kses_post( sprintf( __( 'Your sidebar area is currently empty. Hurry up and <a href="%s" target="_blank">add some widgets</a>.', 'typology' ), admin_url( 'widgets.php' ) ) );?></p>
					</div>
				</div>
			<?php endif; ?>
	<?php endif; ?>

</div>

<div class="typology-sidebar-overlay"></div>