<div class="<?php mistify_print_archive_entry_class('wp-block wp-block-kubio-query-loop-item  position-relative wp-block-kubio-query-loop-item__container mistify-search__k__fx1L_l5Ny--container mistify-local-577-container d-flex   '); ?>"" data-kubio="kubio/query-loop-item">
	<div class="position-relative wp-block-kubio-query-loop-item__inner mistify-search__k__fx1L_l5Ny--inner mistify-local-577-inner d-flex h-flex-basis h-px-lg-0 v-inner-lg-0 h-px-md-0 v-inner-md-0 h-px-0 v-inner-0">
		<div class="position-relative wp-block-kubio-query-loop-item__align mistify-search__k__fx1L_l5Ny--align mistify-local-577-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-start align-self-md-start align-self-start">
			<figure class="wp-block wp-block-kubio-post-featured-image  position-relative wp-block-kubio-post-featured-image__container mistify-search__k__iE82N7AEu-container mistify-local-578-container h-aspect-ratio--4-3 <?php mistify_post_missing_featured_image_class(); ?>" data-kubio="kubio/post-featured-image" data-kubio-settings="{{kubio_settings_value}}">
				<?php if(has_post_thumbnail()): ?>
				<img class='position-relative wp-block-kubio-post-featured-image__image mistify-search__k__iE82N7AEu-image mistify-local--image' src='<?php echo esc_url(get_the_post_thumbnail_url());?>' />
				<?php endif; ?>
				<div class="position-relative wp-block-kubio-post-featured-image__inner mistify-search__k__iE82N7AEu-inner mistify-local-578-inner">
					<div class="position-relative wp-block-kubio-post-featured-image__align mistify-search__k__iE82N7AEu-align mistify-local-578-align h-y-container align-self-lg-center align-self-md-center align-self-center"></div>
				</div>
			</figure>
			<div class="wp-block wp-block-kubio-row  position-relative wp-block-kubio-row__container mistify-search__k__GGANxTVUZA-container mistify-local-579-container gutters-row-lg-2 gutters-row-v-lg-2 gutters-row-md-2 gutters-row-v-md-2 gutters-row-0 gutters-row-v-2" data-kubio="kubio/row">
				<div class="position-relative wp-block-kubio-row__inner mistify-search__k__GGANxTVUZA-inner mistify-local-579-inner h-row align-items-lg-stretch align-items-md-stretch align-items-stretch justify-content-lg-center justify-content-md-center justify-content-center gutters-col-lg-2 gutters-col-v-lg-2 gutters-col-md-2 gutters-col-v-md-2 gutters-col-0 gutters-col-v-2">
					<div class="wp-block wp-block-kubio-column  position-relative wp-block-kubio-column__container mistify-search__k__LhD5PdqFDy-container mistify-local-580-container d-flex h-col-lg-auto h-col-md-auto h-col-auto" data-kubio="kubio/column">
						<div class="position-relative wp-block-kubio-column__inner mistify-search__k__LhD5PdqFDy-inner mistify-local-580-inner d-flex h-flex-basis h-px-lg-2 v-inner-lg-2 h-px-md-2 v-inner-md-2 h-px-2 v-inner-2">
							<div class="position-relative wp-block-kubio-column__align mistify-search__k__LhD5PdqFDy-align mistify-local-580-align h-y-container h-column__content h-column__v-align flex-basis-100 align-self-lg-start align-self-md-start align-self-start">
								<a class="position-relative wp-block-kubio-post-title__link mistify-search__k__tstzQ_uACq-link mistify-local-581-link d-block" href="<?php echo esc_url(get_the_permalink()); ?>">
									<h4 class="wp-block wp-block-kubio-post-title  position-relative wp-block-kubio-post-title__container mistify-search__k__tstzQ_uACq-container mistify-local-581-container" data-kubio="kubio/post-title">
										<?php the_title(); ?>
									</h4>
								</a>
								<div class="wp-block wp-block-kubio-divider  position-relative wp-block-kubio-divider__outer mistify-search__k__UZYoAt1SO-outer mistify-local-582-outer" data-kubio="kubio/divider">
									<div class="position-relative wp-block-kubio-divider__width-container mistify-search__k__UZYoAt1SO-width-container mistify-local-582-width-container">
										<div class="position-relative wp-block-kubio-divider__line mistify-search__k__UZYoAt1SO-line mistify-local-582-line"></div>
									</div>
								</div>
								<div class="wp-block wp-block-kubio-post-meta  position-relative wp-block-kubio-post-meta__metaDataContainer mistify-search__k__7IT84Ddn9-metaDataContainer mistify-local-583-metaDataContainer h-blog-meta" data-kubio="kubio/post-meta">
									<span class="metadata-item">
										<a href="<?php echo esc_url(get_day_link(get_post_time( 'Y' ),get_post_time( 'm' ),get_post_time( 'j' ))); ?>">
											<?php echo esc_html(get_the_date('F j, Y')); ?>
										</a>
									</span>
									<span class="metadata-separator">
										|
									</span>
									<span class="metadata-item">
										<a href="">
											<?php echo esc_html(get_the_time()); ?>
										</a>
									</span>
								</div>
								<p class="wp-block wp-block-kubio-post-excerpt  position-relative wp-block-kubio-post-excerpt__text mistify-search__k__-hWWlFyCEF-text mistify-local-584-text" data-kubio="kubio/post-excerpt">
									<?php mistify_post_excerpt(array (
  'max_length' => 16,
)); ?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
