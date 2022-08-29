<div class="ekit-onboard-main-header">
	<h1 class="ekit-onboard-main-header--title"><strong><?php echo esc_html__(
		'Watch how to use our top widgets.
Save 1 hour of your learning time.',
		'elementskit-lite'
	); ?></strong></h1>
</div>
<div class="ekit-onboard-tutorial">
	<div class="ekit-onboard-tutorial--btn">
		<a class="ekit-onboard-tutorial--link" data-video_id="VhBl3dHT5SY" href="#"><i class="icon icon-play1"></i></a>
	</div>
	
	<div class="ekti-admin-video-tutorial-popup">
			<div class="ekti-admin-video-tutorial-iframe"></div>
	</div>
</div>


<div class="ekit-onboard-tut-term">
	<label class="ekit-onboard-tut-term--label">
		<?php 
		$term = \ElementsKit_Lite\Libs\Framework\Attr::instance()->utils->get_option( 'settings', array() );
		?>
		<input 
		<?php 
		if ( empty( $term['tut_term'] ) || $term['tut_term'] !== 'user_agreed' ) :
			?>
			checked="checked"<?php endif; ?> class="ekit-onboard-tut-term--input" name="settings[tut_term]" type="checkbox" value="user_agreed">
		<?php echo esc_html__( 'Share non-sensitive diagnostic data and details about plugin usage.', 'elementskit-lite' ); ?>
	</label>

	<p class="ekit-onboard-tut-term--helptext"><?php echo esc_html__( "We gather non-sensitive diagnostic data as well as information about plugin use. Your site's URL, WordPress and PHP versions, plugins and themes, as well as your email address, will be used to give you a discount coupon. This information enables us to ensure that this plugin remains consistent with the most common plugins and themes at all times. We pledge not to give you any spam, for sure.", 'elementskit-lite' ); ?></p>
	<p class="ekit-onboard-tut-term--help"><?php echo esc_html__( 'What types of information do we gather?', 'elementskit-lite' ); ?></p>
</div>
<div class="ekit-onboard-pagination">
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn prev" href="#"><i class="icon icon-arrow-left"></i><?php echo esc_html__( 'Back', 'elementskit-lite' ); ?></a>
	<a class="ekit-onboard-btn ekit-onboard-pagi-btn next" href="#"><?php echo esc_html__( 'Next Step', 'elementskit-lite' ); ?></a>
</div>
<div class="ekit-onboard-shapes">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-07.png" alt="" class="shape-07">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-14.png" alt="" class="shape-14">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-15.png" alt="" class="shape-15">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-16.png" alt="" class="shape-16">
	<img src="<?php echo esc_url(self::get_url()); ?>assets/images/onboard/shape-17.png" alt="" class="shape-17">
</div>
