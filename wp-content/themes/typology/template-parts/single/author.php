<?php if( typology_get_option( 'single_author' ) ): ?>

	<?php typology_section_heading( array( 'title' => __typology('about_author') ) ); ?>
	
	<?php if( typology_is_co_authors_active() && $coauthors_meta = get_coauthors() ) : ?>
		
		<div class="section-content typology-author typology-co-author-section">
	
			<?php foreach ($coauthors_meta as $key ) :  ?>
					
				<div class="container">

					<div class="col-lg-2">
						<?php echo get_avatar( $key->ID, 100 ); ?>
					</div>

					<div class="col-lg-10">

						<?php echo '<h5 class="typology-author-box-title">'. esc_html( $key->display_name ).'</h5>'; ?>

						<div class="typology-author-desc">
							<?php echo wpautop(  $key->description ); ?>
						</div>

						<div class="typology-author-links">
							<?php echo typology_get_author_links( $key->ID ); ?>
						</div>

					</div>

				</div>

			<?php endforeach; ?>
		</div>
		
	<?php else: ?>

		<div class="section-content typology-author">
				
			<div class="container">

				<div class="col-lg-2">
					<?php echo get_avatar( get_the_author_meta('ID'), 100); ?>
				</div>

				<div class="col-lg-10">

					<?php echo '<h5 class="typology-author-box-title">'.get_the_author_meta('display_name').'</h5>'; ?>

					<div class="typology-author-desc">
						<?php echo wpautop( get_the_author_meta('description') ); ?>
					</div>

					<div class="typology-author-links">
						<?php echo typology_get_author_links( get_the_author_meta('ID') ); ?>
					</div>

				</div>

			</div>

		</div>

	<?php endif; ?>
		

<?php endif; ?>