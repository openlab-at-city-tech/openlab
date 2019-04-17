<?php $actions = johannes_get( 'header', 'top_actions' ); ?>

<?php if ( !empty( $actions ) ): ?>
	<ul class="johannes-menu-actions">
		<?php foreach ( $actions as $element ): ?>
			<li><?php get_template_part( 'template-parts/header/elements/' . $element ); ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
