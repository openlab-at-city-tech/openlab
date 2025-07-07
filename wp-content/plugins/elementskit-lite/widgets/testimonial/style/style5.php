<div class="elementskit-testimonial-slider ekit_testimonial_style_5 arrow_inside <?php echo !empty($settings['ekit_testimonial_show_dot']) ? 'slider-dotted' : '' ?>" <?php $this->print_render_attribute_string('wrapper'); ?>>
	<div <?php $this->print_render_attribute_string('swiper-container'); ?>>
		<div class="swiper-wrapper">
			<?php
			foreach ($testimonials as $testimonial):
				$wrapTag = 'div';
				$ratingTag = 'a';
	
				if ( !empty( $testimonial['link']['url'] ) ):
					$wrapTag = 'a';
					$ratingTag = 'span';
					
					$this->add_link_attributes( 'link-' . $testimonial['_id'], $testimonial['link'] );
				endif;
			?>
				<div class="swiper-slide">
					<div class="swiper-slide-inner">
						<<?php echo esc_attr( $wrapTag ); ?> class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two <?php echo esc_attr(!empty($testimonial['ekit_testimonial_active']) ? 'testimonial-active' : ''); ?>  elementor-repeater-item-<?php echo esc_attr( $testimonial[ '_id' ] ); ?>" <?php echo $this->get_render_attribute_string( 'link-' . esc_attr($testimonial['_id'] )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
							<div class="elementskit-commentor-header">
								<?php if ($ekit_testimonial_rating_enable == 'yes') : ?>
									<ul class="elementskit-stars">
										<?php
										$reviewData = isset($testimonial['rating']) ? $testimonial['rating'] : 0;
										for($m = 1; $m <= 5; $m++){
											$iconStart = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M287.9 0c9.2 0 17.6 5.2 21.6 13.5l68.6 141.3 153.2 22.6c9 1.3 16.5 7.6 19.3 16.3s.5 18.1-5.9 24.5L433.6 328.4l26.2 155.6c1.5 9-2.2 18.1-9.7 23.5s-17.3 6-25.3 1.7l-137-73.2L151 509.1c-8.1 4.3-17.9 3.7-25.3-1.7s-11.2-14.5-9.7-23.5l26.2-155.6L31.1 218.2c-6.5-6.4-8.7-15.9-5.9-24.5s10.3-14.9 19.3-16.3l153.2-22.6L266.3 13.5C270.4 5.2 278.7 0 287.9 0zm0 79L235.4 187.2c-3.5 7.1-10.2 12.1-18.1 13.3L99 217.9 184.9 303c5.5 5.5 8.1 13.3 6.8 21L171.4 443.7l105.2-56.2c7.1-3.8 15.6-3.8 22.6 0l105.2 56.2L384.2 324.1c-1.3-7.7 1.2-15.5 6.8-21l85.9-85.1L358.6 200.5c-7.8-1.2-14.6-6.1-18.1-13.3L287.9 79z"/></svg>';
											if($reviewData >= $m){
												$iconStart = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512"><path d="M316.9 18C311.6 7 300.4 0 288.1 0s-23.4 7-28.8 18L195 150.3 51.4 171.5c-12 1.8-22 10.2-25.7 21.7s-.7 24.2 7.9 32.7L137.8 329 113.2 474.7c-2 12 3 24.2 12.9 31.3s23 8 33.8 2.3l128.3-68.5 128.3 68.5c10.8 5.7 23.9 4.9 33.8-2.3s14.9-19.3 12.9-31.3L438.5 329 542.7 225.9c8.6-8.5 11.7-21.2 7.9-32.7s-13.7-19.9-25.7-21.7L381.2 150.3 316.9 18z"/></svg>';
											}
											?>
											<li><<?php echo esc_attr( $ratingTag ); ?>><?php echo wp_kses($iconStart, \ElementsKit_Lite\Utils::get_kses_array()); ?></<?php echo esc_attr( $ratingTag ); ?>></li>
										<?php }?>
									</ul>
								<?php endif; ?>

								<?php if(isset($ekit_testimonial_wartermark_enable) && ($ekit_testimonial_wartermark_enable == 'yes')):?>
								<div class="elementskit-icon-content elementskit-watermark-icon ">
									<?php \Elementor\Icons_Manager::render_icon( $settings['ekit_testimonial_wartermarks'], [ 'aria-hidden' => 'true' ] ); ?>
								</div>
								<?php endif;?>
							</div>
							
							<?php if ( isset($testimonial['review']) && !empty($testimonial['review'])) : ?>
								<div class="elementskit-commentor-content"><p><?php echo isset($testimonial['review']) ? wp_kses($testimonial['review'], \ElementsKit_Lite\Utils::get_kses_array()) : ''; ?></p></div>
							<?php endif;  ?>

							<div class="elementskit-commentor-bio">
								<div class="elementkit-commentor-details <?php echo esc_attr($ekit_testimonial_client_area_alignment); ?>">
									<?php
										if (isset($testimonial['client_photo']) && !empty($testimonial['client_photo']['url']) && sizeof($testimonial['client_photo']) > 0) {
									?>
										<div class="elementskit-commentor-image ekit-testimonial--avatar">
											<?php echo wp_kses( \Elementskit_Lite\Utils::get_attachment_image_html($testimonial, 'client_photo', 'full', [
												'height'	=> esc_attr($ekit_testimonial_client_image_size['size']),
												'width'	=> esc_attr($ekit_testimonial_client_image_size['size'])
											]), \ElementsKit_Lite\Utils::get_kses_array());?>
										</div>
									<?php
										}
									?>
									<div class="elementskit-profile-info">
										<strong class="elementskit-author-name"><?php echo isset($testimonial['client_name']) ? esc_html($testimonial['client_name']) : ''; ?></strong>
										<span class="elementskit-author-des"><?php echo isset($testimonial['designation']) ? wp_kses(\ElementsKit_Lite\Utils::kspan($testimonial['designation']), \ElementsKit_Lite\Utils::get_kses_array()) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- Already escaped by kspan method by elementskit ?></span>
									</div>
								</div>
							</div>
						</<?php echo esc_attr( $wrapTag ); ?>>
					</div>
				</div>
			<?php endforeach; ?>
		</div>

		<?php if($settings['ekit_testimonial_show_dot'] == 'yes') : ?>
			<div class="swiper-pagination"></div>
		<?php endif; ?>

		<?php if(!empty($settings['ekit_testimonial_show_arrow'])) : ?>
			<div class="swiper-navigation-button swiper-button-prev">
				<?php \Elementor\Icons_Manager::render_icon($ekit_testimonial_left_arrows, [ 'aria-hidden' => 'true' ]); ?>
			</div>
			<div class="swiper-navigation-button swiper-button-next">
				<?php \Elementor\Icons_Manager::render_icon($ekit_testimonial_right_arrows, [ 'aria-hidden' => 'true' ]); ?>
			</div>
		<?php endif; ?>
	</div>
</div>