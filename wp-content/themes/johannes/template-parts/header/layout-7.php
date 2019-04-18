<div class="header-middle header-layout-7">
    <div class="container d-flex justify-content-center align-items-center">
        <div class="slot-c">
            <?php get_template_part('template-parts/header/elements/branding'); ?>
        </div>
    </div>
</div>

<div class="header-bottom">
    <div class="container">
        <div class="header-bottom-slots d-flex justify-content-center align-items-center">
            <div class="slot-l">
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
</div>