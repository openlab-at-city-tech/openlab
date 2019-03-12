<?php 
    $has_image = typology_get_option( 'layout_c_fimg' ) ? 'post-image-on' : 'post-image-off';
?>
<article <?php post_class( 'typology-post typology-layout-c col-lg-6 text-center '.esc_attr($has_image).'' ); ?>>

    <?php if( typology_get_option( 'layout_c_fimg' ) && has_post_thumbnail() ) : ?>
        <a href="<?php the_permalink(); ?>" class="typology-featured-image"><?php the_post_thumbnail('typology-c'); ?></a>
    <?php endif; ?>
        
    <header class="entry-header">
        <?php the_title( sprintf( '<h2 class="entry-title h4"><a href="%s">', esc_url( get_permalink() ) ), '</a></h2>' ); ?>
        <?php if( $meta = typology_meta_display('c') ) : ?> 
            <div class="entry-meta"><?php echo typology_get_meta_data( $meta ); ?></div>
        <?php endif; ?>
        <?php if( typology_get_option( 'layout_c_dropcap' ) ) : ?>
            <div class="post-letter"><?php echo typology_get_letter(); ?></div>
        <?php endif; ?>
    </header>

</article>