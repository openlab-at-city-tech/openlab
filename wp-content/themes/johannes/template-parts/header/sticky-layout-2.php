<div class="slot-l">
    <?php get_template_part('template-parts/header/elements/branding'); ?>
</div>
<div class="slot-c">
    <?php if( johannes_get('header', 'nav') ): ?>
        <div class="d-none d-lg-block">
            <?php get_template_part('template-parts/header/elements/menu-primary'); ?>
        </div>
    <?php endif; ?>
</div>
<div class="slot-r">
    <?php if( johannes_get('header', 'actions') ): ?>
        <?php foreach( johannes_get('header', 'actions') as $element ): ?>
            <?php get_template_part('template-parts/header/elements/' . $element ); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
