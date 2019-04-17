<div class="johannes-header header-sticky">

    <div class="header-sticky-main <?php echo johannes_get('header', 'sticky_single') ? esc_attr('d-none d-md-block') : ''; ?>">
        <div class="container d-flex justify-content-between align-items-center">
            <?php get_template_part('template-parts/header/sticky-layout-'. johannes_get('header', 'sticky_layout') ); ?>
        </div>
    </div>

    <?php if( johannes_get('header', 'sticky_single') ) : ?>
        <div class="header-sticky-contextual">
            <div class="container d-flex justify-content-center align-items-center">
                <div class="slot-l d-none d-md-block">
                    <?php the_title(); ?>
                </div>
                <div class="slot-r">
                    <div class="d-none d-md-flex align-items-center">
                        <?php echo johannes_get_meta_data( array('comments') ); ?>
                    </div>
                        <?php get_template_part('template-parts/single/share' ); ?>
                    
                     <div class="d-block d-md-none">
                        <?php get_template_part('template-parts/header/elements/hamburger' ); ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

</div>