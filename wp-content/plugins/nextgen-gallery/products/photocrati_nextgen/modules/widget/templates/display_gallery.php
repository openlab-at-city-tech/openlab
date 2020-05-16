<?php
$controller = C_Display_Type_Controller::get_instance();
$storage    = C_Gallery_Storage::get_instance();

$effect_code = $controller->get_effect_code($gallery->displayed_gallery);
$settings    = $gallery->displayed_gallery->get_entity()->display_settings;

echo $settings['widget_setting_before_widget']
     . $settings['widget_setting_before_title']
     . $settings['widget_setting_title']
     . $settings['widget_setting_after_title'];
?>
<?php // keep the following a/img on the same line ?>
<div class="ngg-widget entry-content">
    <?php foreach ($images as $image) { ?>
        <a href="<?php echo esc_attr($storage->get_image_url($image, 'full', TRUE))?>"
           title="<?php echo esc_attr($image->description)?>"
           data-image-id='<?php echo esc_attr($image->pid); ?>'
           <?php echo $effect_code ?>
           ><img title="<?php echo esc_attr($image->alttext)?>"
                 alt="<?php echo esc_attr($image->alttext)?>"
                 src="<?php echo esc_attr($storage->get_image_url($image, $settings['image_type'], TRUE)); ?>"
                 width="<?php echo esc_attr($settings['image_width']); ?>"
                 height="<?php echo esc_attr($settings['image_height']); ?>"
            /></a>
    <?php } ?>
</div>

<?php echo $settings['widget_setting_after_widget']; ?>
