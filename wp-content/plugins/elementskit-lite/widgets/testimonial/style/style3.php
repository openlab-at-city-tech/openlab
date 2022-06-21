<div class="elementskit-testimonial-slider slick-slider arrow_inside <?php echo !empty($settings['ekit_testimonial_show_dot']) ? 'slick-dotted' : '' ?>" <?php echo $this->get_render_attribute_string('wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
	<div class="swiper-container">
		<div class="slick-list swiper-wrapper">
		<?php
			foreach ($testimonials as $testimonial):
				$clientPhoto = '';
				$wrapTag = 'div';
				$ratingTag = 'a';

				if ( !empty( $testimonial['link']['url'] ) ):
					$wrapTag = 'a';
					$ratingTag = 'span';
					
					$this->add_link_attributes( 'link-' . $testimonial['_id'], $testimonial['link'] );
				endif;
				
				if (isset($testimonial['client_photo']) && !empty($testimonial['client_photo']['url']) &&  sizeof($testimonial['client_photo']) > 0) {
					$clientPhoto = isset($testimonial['client_photo']['url']) ? $testimonial['client_photo']['url'] : '';  } ?>
					<div class="swiper-slide">
						<div class="slick-slide">
							<<?php echo esc_attr( $wrapTag ); ?> class="elementskit-testimonial_card" style="background-image: url(<?php esc_attr_e($clientPhoto, 'elementskit-lite' );?>);" <?php echo $this->get_render_attribute_string( 'link-' . esc_attr($testimonial['_id'] )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
								<?php if ($ekit_testimonial_rating_enable == 'yes') : ?>
								<ul class="elementskit-stars">
									<?php
									$reviewData = isset($testimonial['rating']) ? $testimonial['rating'] : 0;
									for($m = 1; $m <= 5; $m++){
										$iconStart = 'eicon-star-o';
										if($reviewData >= $m){
											$iconStart = 'eicon-star active';
										}
									?>
									<li><<?php echo esc_attr( $ratingTag ); ?>><i class="<?php esc_attr_e( $iconStart, 'elementskit-lite' );?>"></i></<?php echo esc_attr( $ratingTag ); ?>></li>

									<?php }?>
								</ul>
								<?php endif; ?>

								<?php if ( isset($testimonial['review']) && !empty($testimonial['review'])) : ?>
									<p class="elementskit-commentor-coment"><?php echo isset($testimonial['review']) ? wp_kses($testimonial['review'], \ElementsKit_Lite\Utils::get_kses_array()): ''; ?></p>
								<?php endif;  ?>

								<?php if ( isset($testimonial['review_youtube_link']) && !empty($testimonial['review_youtube_link'])) : ?>
									<div class="elementskit-video-popup-content">
										<a href="<?php esc_attr_e($review_youtube_link, 'elementskit-lite');?>" class="video-popup"><i class="icon icon-play"></i></a>
									</div><!-- .elementskit-video-popup-content END -->
								<?php endif;  ?>

								<span class="elementskit-profile-info">
									<strong class="elementskit-author-name"><?php echo isset($testimonial['client_name']) ? esc_html($testimonial['client_name']) : ''; ?></strong>
									<span class="elementskit-author-des"><?php echo isset($testimonial['designation']) ? wp_kses($testimonial['designation'], \ElementsKit_Lite\Utils::get_kses_array()) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- Already escaped by kspan method by elementskit ?></span>
								</span>
								<div class="xs-overlay elementor-repeater-item-<?php echo esc_attr( $testimonial[ '_id' ] ); ?>"></div>
							</<?php echo esc_attr( $wrapTag ); ?>><!-- .testimonial_card END -->
						</div>
					</div>
			<?php endforeach; ?>
		</div>
	</div>
	<ul class="slick-dots swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"></ul>
	<?php if(!empty($settings['ekit_testimonial_show_arrow'])) : ?>
		<button type="button" class="slick-prev slick-arrow"><i class="<?php echo esc_attr($prevArrowIcon); ?>"></i></button>
		<button type="button" class="slick-next slick-arrow"><i class="<?php echo esc_attr($nextArrowIcon); ?>"></i></button>
	<?php endif; ?>
</div>
