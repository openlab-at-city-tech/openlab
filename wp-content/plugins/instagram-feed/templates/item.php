<?php

/**
 * Smash Balloon Instagram Feed Item Template
 * Adds an image, link, and other data for each post in the feed
 *
 * @version 2.9 Instagram Feed by Smash Balloon
 */

// Don't load directly.
if (!defined('ABSPATH')) {
	die('-1');
}

$classes = SB_Instagram_Display_Elements::get_item_classes($settings, $post);
$post_id = SB_Instagram_Parse::get_post_id($post);
$timestamp = SB_Instagram_Parse::get_timestamp($post);
$media_type = SB_Instagram_Parse::get_media_type($post);
$permalink = SB_Instagram_Parse::get_permalink($post);
$maybe_carousel_icon = $media_type === 'carousel' ? SB_Instagram_Display_Elements::get_icon('carousel', $icon_type) : '';
$maybe_video_icon = $media_type === 'video' ? SB_Instagram_Display_Elements::get_icon('video', $icon_type) : '';
$media_url = SB_Instagram_Display_Elements::get_optimum_media_url($post, $settings, $resized_images);
$media_full_res = SB_Instagram_Parse::get_media_url($post);
$media_all_sizes_json = SB_Instagram_Parse::get_media_src_set($post, $resized_images);

/**
 * Text that appears in the "alt" attribute for this image
 *
 * @param string $img_alt full caption for post
 * @param array $post api data for the post
 *
 * @since 2.1.5
 */
$caption = SB_Instagram_Parse::get_caption($post, sprintf(__('Instagram post %s', 'instagram-feed'), $post_id));
$img_alt = apply_filters('sbi_img_alt', $caption, $post);

/**
 * Text that appears in the visually hidden screen reader element
 *
 * @param string $img_screenreader first 50 bytes in UTF-8.
 * @param array $post api data for the post
 *
 * @since 2.1.5
 */
$img_screenreader = mb_substr($caption, 0, 50, 'UTF-8');
$img_screenreader = apply_filters('sbi_img_screenreader', $img_screenreader, $post);

?>
<div class="sbi_item sbi_type_<?php echo esc_attr($media_type); ?><?php echo esc_attr($classes); ?>"
	id="sbi_<?php echo esc_html($post_id); ?>" data-date="<?php echo esc_html($timestamp); ?>">
	<div class="sbi_photo_wrap">
		<a class="sbi_photo" href="<?php echo esc_url($permalink); ?>" target="_blank" rel="noopener nofollow"
			data-full-res="<?php echo esc_url($media_full_res); ?>"
			data-img-src-set="<?php echo esc_attr(sbi_json_encode($media_all_sizes_json)); ?>">
			<span class="sbi-screenreader"><?php echo esc_html($img_screenreader); ?></span>
			<?php echo $maybe_carousel_icon; ?>
			<?php echo $maybe_video_icon; ?>
			<img src="<?php echo esc_url($media_url); ?>" alt="<?php echo esc_attr($img_alt); ?>" aria-hidden="true">
		</a>
	</div>
</div>