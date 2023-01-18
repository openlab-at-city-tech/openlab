<div class="elementskit-testimonial-slider ekit_testimonial_style_5 slick-slider arrow_inside <?php echo !empty($settings['ekit_testimonial_show_dot']) ? 'slick-dotted' : '' ?>" <?php echo $this->get_render_attribute_string('wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
	<div class="swiper-container">
        <div class="slick-list swiper-wrapper">
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
					<div class="slick-slide">
						<<?php echo esc_attr( $wrapTag ); ?> class="elementskit-single-testimonial-slider elementskit-testimonial-slider-block-style elementskit-testimonial-slider-block-style-two <?php echo esc_attr(!empty($testimonial['ekit_testimonial_active']) ? 'testimonial-active' : ''); ?>  elementor-repeater-item-<?php echo esc_attr( $testimonial[ '_id' ] ); ?>" <?php echo $this->get_render_attribute_string( 'link-' . esc_attr($testimonial['_id'] )); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
							<div class="elementskit-commentor-header">
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

								<?php if(isset($ekit_testimonial_wartermark_enable) && ($ekit_testimonial_wartermark_enable == 'yes') && ($ekit_testimonial_wartermark_position == 'top')):?>
								<div class="elementskit-icon-content elementskit-watermark-icon <?php if ($ekit_testimonial_wartermark_custom_position == 'yes') : ?> ekit_watermark_icon_custom_position <?php endif; ?>">
									
									<?php
										// new icon
										$migrated = isset( $settings['__fa4_migrated']['ekit_testimonial_wartermarks'] );
										// Check if its a new widget without previously selected icon using the old Icon control
										$is_new = empty( $settings['ekit_testimonial_wartermark'] );
										if ( $is_new || $migrated ) {
											// new icon
											\Elementor\Icons_Manager::render_icon( $settings['ekit_testimonial_wartermarks'], [ 'aria-hidden' => 'true' ] );
										} else {
											?>
											<i class="<?php echo esc_attr($settings['ekit_testimonial_wartermark']); ?>" aria-hidden="true"></i>
											<?php
										}
									?>
								
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
								<?php if(isset($ekit_testimonial_wartermark_enable) && ($ekit_testimonial_wartermark_enable == 'yes') && ($ekit_testimonial_wartermark_position == 'bottom')):?>
								<div class="elementskit-icon-content elementskit-watermark-icon <?php if ($ekit_testimonial_wartermark_custom_position == 'yes') : ?> ekit_watermark_icon_custom_position <?php endif; ?>">
									<?php
										// new icon
										$migrated = isset( $settings['__fa4_migrated']['ekit_testimonial_wartermarks'] );
										// Check if its a new widget without previously selected icon using the old Icon control
										$is_new = empty( $settings['ekit_testimonial_wartermark'] );
										if ( $is_new || $migrated ) {
											// new icon
											\Elementor\Icons_Manager::render_icon( $settings['ekit_testimonial_wartermarks'], [ 'aria-hidden' => 'true' ] );
										} else {
											?>
											<i class="<?php echo esc_attr($settings['ekit_testimonial_wartermark']); ?>" aria-hidden="true"></i>
											<?php
										}
									?>
								</div>
								<?php endif;?>
							</div>
						</<?php echo esc_attr( $wrapTag ); ?>>
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