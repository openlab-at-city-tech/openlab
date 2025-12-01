<?php
if($ekit_video_popup_button_style == 'icon' || $ekit_video_inline_button_style == 'icon') {
	$this->add_render_attribute('button', ['class' => [ 'ekit_icon_button' ]]);
}

$glow_animation_class = '';
if ($ekit_video_popup_video_glow == 'yes' && !empty($ekit_video_popup_glow_animation_type)) {
    $glow_animation_class = 'glow-' . esc_attr($ekit_video_popup_glow_animation_type);
}elseif ($ekit_video_inline_video_glow == 'yes' && !empty($ekit_video_inline_glow_animation_type)) {
    $glow_animation_class = 'glow-' . esc_attr($ekit_video_inline_glow_animation_type);

}

$this->add_render_attribute('button', [
    'class' => [$glow_animation_class]
]);

if (isset($ekit_video_popup_radio_wave_scale['size'])) {
	$this->add_render_attribute(
		'button',
		'glow-radio_wave',
		'--ekit-radio-wave-scale: '. $ekit_video_popup_radio_wave_scale['size'].';'
	);
}

if (isset($ekit_video_inline_radio_wave_scale['size'])) {
	$this->add_render_attribute(
		'button',
		'glow-radio_wave',
		'--ekit-radio-wave-scale: '. $ekit_video_inline_radio_wave_scale['size'].';'
	);
}

if($ekit_video_popup_video_type == 'self' && $ekit_video_style === 'popup') {
	$this->add_render_attribute('button', ['class' => ['ekit-video-popup'], 'href' => '#'.$generate_id]);
}elseif($ekit_video_style === 'popup'){
	$this->add_render_attribute('button', ['class' => ['ekit-video-popup'], 'href' => $ekit_video_popup_url]);
}

if ($ekit_video_style === 'popup') {
	$this->add_render_attribute('button', ['class' => ['ekit-video-popup-btn'], 'aria-label' => "video-popup"]);
}
if ($ekit_video_style === 'inline' && isset($ekit_video_inline_button_icons__switch_overlay) && $ekit_video_inline_button_icons__switch_overlay === 'yes') {
	$this->add_render_attribute('button', ['class' => ['ekit-video-inline-btn'], 'aria-label' => "video-inline"]);
}

?>

<a <?php $this->print_render_attribute_string('button'); ?>>
	<?php if ($ekit_video_popup_button_style == 'text') : ?>
		<span class="ekit-video-popup-title"><?php echo esc_html($ekit_video_popup_button_title); ?></span>
	<?php endif; ?>
	<?php if ($ekit_video_popup_button_style == 'icon' && $ekit_video_popup_button_icons != '') : ?>
		<?php $this->video_icon(); ?>
	<?php endif; ?>
	<?php if ($ekit_video_popup_button_style == 'both') : ?>
		<?php if ($ekit_video_popup_icon_align == 'before' && $ekit_video_popup_button_icons != '') : ?>
			<?php  $this->video_icon(); ?>
		<?php endif; ?>
		<span class="ekit-video-popup-title"><?php echo esc_html($ekit_video_popup_button_title); ?></span>
		<?php if ($ekit_video_popup_icon_align == 'after' && $ekit_video_popup_button_icons != '') : ?>
			<?php $this->video_icon(); ?>
		<?php endif; ?>
	<?php endif; ?>

	<?php if ($ekit_video_inline_button_style == 'text') : ?>
		<span><?php echo esc_html($ekit_video_inline_button_title); ?></span>
	<?php endif; ?>
	<?php if ($ekit_video_inline_button_style == 'icon' && $ekit_video_inline_button_icons != '') : ?>
		<?php $this->video_icon(); ?>
	<?php endif; ?>
	<?php if ($ekit_video_inline_button_style == 'both') : ?>
		<?php if ($ekit_video_inline_icon_align == 'before' && $ekit_video_inline_button_icons != '') : ?>
			<?php  $this->video_icon(); ?>
		<?php endif; ?>
		<span><?php echo esc_html($ekit_video_inline_button_title); ?></span>
		<?php if ($ekit_video_inline_icon_align == 'after' && $ekit_video_inline_button_icons != '') : ?>
			<?php $this->video_icon(); ?>
		<?php endif; ?>
	<?php endif; ?>
</a>
