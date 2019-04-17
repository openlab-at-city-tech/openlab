<div class="slot-l">
    <?php if( johannes_get('header', 'actions_l') ): ?>
        <?php foreach( johannes_get('header', 'actions_l') as $element ): ?>
            <?php get_template_part('template-parts/header/elements/' . $element ); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
<div class="slot-c">
    <?php get_template_part('template-parts/header/elements/branding'); ?>
</div>
<div class="slot-r">
    <?php if( johannes_get('header', 'actions_r') ): ?>
        <?php foreach( johannes_get('header', 'actions_r') as $element ): ?>
            <?php get_template_part('template-parts/header/elements/' . $element ); ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>