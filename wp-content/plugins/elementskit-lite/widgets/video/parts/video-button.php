<?php
if($ekit_video_popup_button_style == 'icon') {
	$this->add_render_attribute('button', ['class' => [ 'ekit_icon_button' ]]);
}

if($ekit_video_popup_video_glow == 'yes') {
	$this->add_render_attribute('button', ['class' => [ 'glow-btn' ]]);
}

if($ekit_video_popup_video_type == 'self') {
	$this->add_render_attribute('button', ['class' => ['ekit-video-popup'], 'href' => '#'.$generate_id]);
}else{
	$this->add_render_attribute('button', ['class' => ['ekit-video-popup'], 'href' => $ekit_video_popup_url]);
}

$this->add_render_attribute('button', ['class' => ['ekit-video-popup-btn'], 'aria-label' => "video-popup"]);
?>

<a <?php $this->print_render_attribute_string('button'); ?>>
	<?php if ($ekit_video_popup_button_style == 'text') : ?>
		<span><?php echo esc_html($ekit_video_popup_button_title); ?></span>
	<?php endif; ?>
	<?php if ($ekit_video_popup_button_style == 'icon' && $ekit_video_popup_button_icons != '') : ?>
		<?php echo wp_kses($this->video_icon(), \ElementsKit_Lite\Utils::get_kses_array()); ?>
	<?php endif; ?>
	<?php if ($ekit_video_popup_button_style == 'both') : ?>
		<?php if ($ekit_video_popup_icon_align == 'before' && $ekit_video_popup_button_icons != '') : ?>
			<?php echo wp_kses($this->video_icon(), \ElementsKit_Lite\Utils::get_kses_array()); ?>
		<?php endif; ?>
		<span><?php echo esc_html($ekit_video_popup_button_title); ?></span>
		<?php if ($ekit_video_popup_icon_align == 'after' && $ekit_video_popup_button_icons != '') : ?>
			<?php echo wp_kses($this->video_icon(), \ElementsKit_Lite\Utils::get_kses_array()); ?>
		<?php endif; ?>
	<?php endif; ?>
</a>