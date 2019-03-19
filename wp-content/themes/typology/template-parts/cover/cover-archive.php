<?php if( typology_get_archive_option( 'cover' ) ) : ?>
	
	<?php 
		$cover_media = typology_cover_media(); 
		$cover_media_class = !empty($cover_media)  ? 'typology-cover-overlay' : '';
	?>
	<div class="typology-cover-item <?php echo  esc_attr( $cover_media_class ); ?>">

		<div class="cover-item-container">

			<?php $cover = typology_get_archive_heading(); ?>
			
			<header class="entry-header">
				<?php if(!empty($cover['pre']) ): ?>
					<span class="entry-pre-title"><?php echo wp_kses_post( $cover['pre'] ); ?></span>
				<?php endif; ?>
				
				<?php if(!empty($cover['title']) ): ?>
					<h1 class="entry-title"><?php echo wp_kses_post( $cover['title'] ); ?></h1>
				<?php endif; ?>

				<?php if(!empty($cover['avatar']) ): ?>
					<div class="cover-avatar"><?php echo wp_kses_post( $cover['avatar'] ); ?></div>
				<?php endif; ?>

				<?php if(!empty($cover['desc']) ): ?>
					<div class="section-content cover-archive-desc">
						<?php echo wp_kses_post( $cover['desc'] ); ?>				
					</div>
				<?php endif; ?>

			</header>

			<?php if( typology_get_option('archive_dropcap') ) : ?>
	    		<div class="cover-letter"><?php echo typology_get_letter( $cover['title'] ); ?></div>
	    	<?php endif; ?>
			
		
		</div>

		<?php if( !empty($cover_media) ) : ?>
			<div class="typology-cover-img">
				<?php typology_display_media( $cover_media ); ?>
			</div>
		<?php endif; ?>

	</div>
<?php endif; ?>