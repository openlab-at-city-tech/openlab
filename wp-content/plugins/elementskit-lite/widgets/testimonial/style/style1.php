<div class="elementskit-testimonial-slider slick-slider arrow_inside elementskit-default-testimonial <?php echo !empty($settings['ekit_testimonial_show_dot']) ? 'slick-dotted' : '' ?>" <?php echo $this->get_render_attribute_string('wrapper'); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
	<div class="swiper-container">
        <div class="slick-list swiper-wrapper">
		<?php
			// start foreach loop
			foreach ($testimonials as $testimonial):
				$wrapTag = 'div';

				if ( !empty( $testimonial['link']['url'] ) ):
					$wrapTag = 'a';
					$this->add_link_attributes( 'link-' . $testimonial['_id'], $testimonial['link'] );
				endif;
		?>
			<div class="swiper-slide">
				<div class="slick-slide">
					<<?php echo esc_attr( $wrapTag ); ?> class="elemntskit-testimonial-item" <?php echo $this->get_render_attribute_string( 'link-' . $testimonial['_id'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Already escaped by elementor ?>>
						<div class="elementskit-single-testimonial-slider <?php echo esc_attr(!empty($testimonial['ekit_testimonial_active']) ? 'testimonial-active' : ''); ?>">
							<div class="row">
								<div class="col-lg-6 elementkit-testimonial-col">
									<div class="elementskit-commentor-content">
										<?php if (isset($testimonial['client_logo']) && !empty($testimonial['client_logo']['url']) && sizeof($testimonial['client_logo']) > 0) { ?>
											<div class="elementskit-client_logo">
												<?php echo wp_kses( \Elementskit_Lite\Utils::get_attachment_image_html($testimonial, 'client_logo', 'full'), \ElementsKit_Lite\Utils::get_kses_array());?>
											</div>
										<?php
											} ?>
										<?php if ( isset($testimonial['review']) && !empty($testimonial['review'])) : ?>
											<p><?php echo isset($testimonial['review']) ? wp_kses($testimonial['review'], \ElementsKit_Lite\Utils::get_kses_array()) : ''; ?></p>
										<?php endif;  ?>
										<?php if ( 'yes' == $ekit_testimonial_title_separetor ): ?>
											<span class="elementskit-border-hr"></span>
										<?php endif; ?>
										<span class="elementskit-profile-info">
											<strong class="elementskit-author-name"><?php echo isset($testimonial['client_name']) ? esc_html($testimonial['client_name']) : ''; ?></strong>
											<span class="elementskit-author-des"><?php echo isset($testimonial['designation']) ? wp_kses(\ElementsKit_Lite\Utils::kspan($testimonial['designation']), \ElementsKit_Lite\Utils::get_kses_array()) : ''; // phpcs:ignore WordPress.Security.EscapeOutput -- Already escaped by kspan method by elementskit ?></span>
										</span>
									</div>
								</div>
								<div class="col-lg-6 elementkit-testimonial-col">
									<div class="elementskit-profile-image-card">
										<?php if (isset($testimonial['client_photo']) && !empty($testimonial['client_photo']['url']) &&  sizeof($testimonial['client_photo']) > 0) {
												echo wp_kses( \Elementskit_Lite\Utils::get_attachment_image_html($testimonial, 'client_photo', 'full'), \ElementsKit_Lite\Utils::get_kses_array());
												?>
										<?php } ?>
										<?php if( isset($ekit_testimonial_enable_social) && $ekit_testimonial_enable_social == 'yes'):?>
											<div class="elementskit-hover-area">
												<ul class="social-list medium circle text-colored">
													<?php if(isset($testimonial['facebook_url']) && strlen($testimonial['facebook_url']) > 5){?>
													<li><a href="<?php esc_attr_e($testimonial['facebook_url'], 'elementskit-lite');?>" class="facebook"><i class="fa fa-facebook"></i></a></li>
													<?php }?>
													<?php if(isset($testimonial['twitter_url']) && strlen($testimonial['twitter_url']) > 5){?>
													<li><a href="<?php esc_attr_e($testimonial['twitter_url'], 'elementskit-lite');?>" class="twitter"><i class="fa fa-twitter"></i></a></li>
													<?php }?>
													<?php if(isset($testimonial['linkedin_url']) && strlen($testimonial['linkedin_url']) > 5){?>
													<li><a href="<?php esc_attr_e($testimonial['linkedin_url'], 'elementskit-lite');?>" class="linkedin"><i class="fa fa-linkedin"></i></a></li>
													<?php }?>
													<?php if(isset($testimonial['youtube_url']) && strlen($testimonial['youtube_url']) > 5){?>
													<li><a href="<?php esc_attr_e($testimonial['youtube_url'], 'elementskit-lite');?>" class="youtube"><i class="fa fa-youtube"></i></a></li>
													<?php }?>
												</ul>
											</div>
										<?php endif;?>
									</div>
								</div>
							</div>
						</div>
					</<?php echo esc_attr( $wrapTag ); ?>>
				</div>
			</div>
		<?php endforeach; // end foreach loop ?>
		</div>
	</div>
	<ul aria-describedby="paginations" class="slick-dots swiper-pagination swiper-pagination-clickable swiper-pagination-bullets"></ul>
	<?php if(!empty($settings['ekit_testimonial_show_arrow'])) : ?>
		<button type="button" class="slick-prev slick-arrow"><i class="<?php echo esc_attr($prevArrowIcon); ?>"></i></button>
		<button type="button" class="slick-next slick-arrow"><i class="<?php echo esc_attr($nextArrowIcon); ?>"></i></button>
	<?php endif; ?>
</div>
