<?php
    $cover_media =  typology_cover_media();
    $cover_media_class = !empty($cover_media) ? 'typology-cover-overlay' : '';
?>
<div class="typology-cover-item <?php echo esc_attr( $cover_media_class ); ?>">

    <div class="cover-item-container">
        <header class="entry-header">
            <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
        </header>
        <?php if( typology_get_option('page_dropcap') ) : ?>
            <div class="cover-letter"><?php echo typology_get_letter(); ?></div>
        <?php endif; ?>
    </div>

<?php if( $cover_media ) : ?>
    <div class="typology-cover-img">
        <?php typology_display_media( $cover_media ); ?>
    </div>
<?php endif; ?>

</div>