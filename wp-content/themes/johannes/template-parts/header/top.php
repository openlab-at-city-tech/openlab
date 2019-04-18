<?php $top = johannes_get( 'header', 'top' ); ?>

<div class="header-top">
    <div class="container d-flex justify-content-between align-items-center ">

        <?php if ( !empty( $top['l'] ) ): ?>
            <div class="slot-l">
               <?php foreach( $top['l'] as $element ) : ?>
                    <?php get_template_part( 'template-parts/header/elements/'. $element  ); ?>
               <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( !empty( $top['c'] ) ): ?>
            <div class="slot-c">
                <?php foreach( $top['c'] as $element ) : ?>
                    <?php get_template_part( 'template-parts/header/elements/'. $element  ); ?>
               <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ( !empty( $top['r'] ) ): ?>
            <div class="slot-r">
               <?php foreach( $top['r'] as $element ) : ?>
                    <?php get_template_part( 'template-parts/header/elements/'. $element  ); ?>
               <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </div>
</div>