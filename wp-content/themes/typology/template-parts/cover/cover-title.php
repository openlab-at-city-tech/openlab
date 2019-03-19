<?php 
	$cover_media = typology_cover_media(); 
	$cover_media_class = !empty($cover_media) ? 'typology-cover-overlay' : '';
?>
<div class="typology-cover-item <?php echo esc_attr( $cover_media_class ); ?>">
	<div class="cover-item-container">
	    <header class="entry-header">
	        <?php the_title( '<h2 class="entry-title h1">', '</h2>' ); ?>
	    </header>
	    <?php if( typology_get_option('front_page_cover_dropcap') ) : ?>
	    	<div class="cover-letter"><?php echo typology_get_letter( get_the_title() ); ?></div>
	    <?php endif; ?>
	</div>

	<?php if( !empty($cover_media) ) : ?>
		<div class="typology-cover-img">
			<?php typology_display_media( $cover_media ); ?>
		</div>
	<?php endif; ?>

</div>