<article <?php post_class( 'typology-post typology-layout-a ' . esc_attr($ad_class) ); ?>>

    <header class="entry-header">
        <?php the_title( sprintf( '<h2 class="entry-title h1"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        <?php if( $meta = typology_meta_display('a') ) : ?> 
            <div class="entry-meta"><?php echo typology_get_meta_data( $meta ); ?></div>
        <?php endif; ?>
        <?php if( typology_get_option( 'layout_a_dropcap' ) ) : ?>
            <div class="post-letter"><?php echo typology_get_letter(); ?></div>
        <?php endif; ?>
    </header>

    <div class="entry-content">
        <?php if( typology_get_option( 'layout_a_fimg' ) && has_post_thumbnail() ) : ?>
            <a href="<?php the_permalink(); ?>" class="typology-featured-image"><?php the_post_thumbnail('typology-a'); ?></a>
        <?php endif; ?>

        <?php if( typology_get_option('layout_a_content') == 'excerpt' ) : ?>
            <?php echo typology_get_excerpt( typology_get_option( 'layout_a_excerpt_limit' ) ); ?>
        <?php else: ?>
            <?php the_content(); ?>
        <?php endif; ?>
    </div>
    
    <?php if( $buttons = typology_buttons_display('a') ) : ?>      
        <div class="entry-footer">
            <?php echo typology_get_buttons_data( $buttons ); ?>
        </div>
    <?php endif; ?>

</article>