<div class="ekit-onboard-main-header">
	<?php
		$step5_title = __( 'Upgrade within the next </br> <strong>2 hours</strong> and get a <strong>20% Discount.</strong>', 'elementskit-lite' );
	?>
	<h1 class="ekit-onboard-main-header--title">
		<?php echo wp_kses( $step5_title, \ElementsKit_Lite\Utils::get_kses_array() ); ?>
	</h1>
	<ul>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/heart-icon.svg" alt="">
			<?php echo esc_html__( 'Trusted by 2,000,000+ Websites', 'elementskit-lite' ); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/rating-icon.svg" alt="">
			<?php echo esc_html__( 'Rated 4.9 by 2,000 People', 'elementskit-lite' ); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/support-icon.svg" alt="">
			<?php echo esc_html__( '24/7 Customer Support', 'elementskit-lite' ); ?>
		</li>
	</ul>
</div>

<div class="ekit-onboard-pro-features">
	<h2><?php echo esc_html__( 'ElementsKit', 'elementskit-lite' ); ?> <span><?php echo esc_html__( 'PRO', 'elementskit-lite' ); ?></span></h2>

	<ul class="ekit-onboard-modules">
		<li>
			<span class="module-icon icon-01"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-01.svg" alt=""></span>
			<span><?php echo esc_html__( 'Advanced Header & Footer', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-02"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-02.svg" alt=""></span>
			<span><?php echo esc_html__( 'Multi-level Mega Menu Builder', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-03"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-03.svg" alt=""></span>
			<span><?php echo esc_html__( 'Advanced Tab & Accordion', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-04"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-04.svg" alt=""></span>
			<span><?php echo esc_html__( 'Parallax & Particles Background', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-05"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-05.svg" alt=""></span>
			<span><?php echo esc_html__( 'Advanced Carousel & Sliders', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-06"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-06.svg" alt=""></span>
			<span><?php echo esc_html__( 'Sticky & Scrolling Effects', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-07"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-07.svg" alt=""></span>
			<span><?php echo esc_html__( 'Cross Domain Copy Paste', 'elementskit-lite' ); ?></span>
		</li>
		<li>
			<span class="module-icon icon-08"><img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/module-icon-08.svg" alt=""></span>
			<span><?php echo esc_html__( 'Conditional & Protected Content', 'elementskit-lite' ); ?></span>
		</li>
	</ul>

	<ul class="ekit-onboard-module-meta">
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/check-icon.svg" alt="">
			<?php echo sprintf('<strong>%s</strong> %s', '90+', esc_html__( 'Widgets', 'elementskit-lite' )); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/check-icon.svg" alt="">
			<?php echo sprintf('<strong>%s</strong> %s', '20+', esc_html__( 'Modules', 'elementskit-lite' )); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/check-icon.svg" alt="">
			<?php echo sprintf('<strong>%s</strong> %s', '900+', esc_html__( 'Block Sections', 'elementskit-lite' )); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/check-icon.svg" alt="">
			<?php echo sprintf('<strong>%s</strong> %s', '45+', esc_html__( 'Header Footer Templates', 'elementskit-lite' )); ?>
		</li>
		<li>
			<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/check-icon.svg" alt="">
			<?php echo sprintf('<strong>%s</strong> %s', '3000+', esc_html__( 'Custom Icons', 'elementskit-lite' )); ?>
		</li>
	</ul>
</div>

<div class="ekit-onboard-pagination">
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn prev" href="#">
		<i class="icon icon-left-arrow"></i>
		<?php echo esc_html__( 'Back', 'elementskit-lite' ); ?>
	</a>
	<a href="https://wpmet.com/plugin/elementskit/pricing/?promo=onboard-coupon" target="_blank" class="attr-btn attr-btn-primary ekit-pro-btn">
		<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-24.png" alt="" class="shape-24">
		<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-23.png" alt="" class="shape-23">
		<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/crown-icon.svg" alt="">
		<?php echo esc_html__( 'Explore PRO', 'elementskit-lite' ); ?>
	</a>
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn next" href="#">
		<?php echo esc_html__( 'Complete Setup', 'elementskit-lite' ); ?>
	</a>
</div>

<div class="ekit-onboard-shapes">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-18.png" alt="" class="shape-18">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-19.png" alt="" class="shape-19">
</div>
