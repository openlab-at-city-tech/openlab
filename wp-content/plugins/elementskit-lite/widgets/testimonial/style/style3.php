<div class="elementskit-testimonial-slider arrow_inside <?php echo !empty($settings['ekit_testimonial_show_dot']) ? 'slider-dotted' : '' ?>" <?php $this->print_render_attribute_string('wrapper'); ?>>
	<div <?php $this->print_render_attribute_string('swiper-container'); ?>>
		<div class="swiper-wrapper">
			<?php foreach ($testimonials as $testimonial):
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
						<div class="swiper-slide-inner">
							<<?php echo esc_attr( $wrapTag ); ?> class="elementskit-testimonial_card" style="background-image: url(<?php echo esc_url($clientPhoto);?>);" <?php echo $this->get_render_attribute_string( 'link-' . esc_attr($testimonial['_id'] )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
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
									<li><<?php echo esc_attr( $ratingTag ); ?>><i class="<?php echo esc_attr( $iconStart); ?>"></i></<?php echo esc_attr( $ratingTag ); ?>></li>

									<?php }?>
								</ul>
								<?php endif; ?>

								<?php if ( isset($testimonial['review']) && !empty($testimonial['review'])) : ?>
									<p class="elementskit-commentor-coment"><?php echo isset($testimonial['review']) ? wp_kses($testimonial['review'], \ElementsKit_Lite\Utils::get_kses_array()): ''; ?></p>
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

		<?php if($settings['ekit_testimonial_show_dot'] == 'yes') : ?>
			<div class="swiper-pagination"></div>
		<?php endif; ?>

		<?php if(!empty($settings['ekit_testimonial_show_arrow'])) : ?>
			<div class="swiper-navigation-button swiper-button-prev"><i class="<?php echo esc_attr($prevArrowIcon); ?>"></i></div>
			<div class="swiper-navigation-button swiper-button-next"><i class="<?php echo esc_attr($nextArrowIcon); ?>"></i></div>
		<?php endif; ?>
	</div>
</div>
