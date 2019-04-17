<div class="header-middle header-layout-3">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="slot-l">
            <?php get_template_part('template-parts/header/elements/branding'); ?>
            <?php if( johannes_get('header', 'nav') ): ?>
                <?php get_template_part('template-parts/header/elements/menu-primary'); ?>
            <?php endif; ?>
        </div>
        <div class="slot-r">
            <?php if( johannes_get('header', 'actions') ): ?>
            	<?php foreach( johannes_get('header', 'actions') as $element ): ?>
             		<?php get_template_part('template-parts/header/elements/' . $element ); ?>
             	<?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>