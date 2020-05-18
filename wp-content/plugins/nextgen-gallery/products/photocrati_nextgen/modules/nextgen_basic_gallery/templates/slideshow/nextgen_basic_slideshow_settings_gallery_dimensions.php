<tr>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_gallery_width'
               class="tooltip"
               title="<?php echo $gallery_dimensions_tooltip; ?>">
            <?php esc_html_e($gallery_dimensions_label); ?>
        </label>
    </td>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_gallery_width'>w</label>
        <input type='number'
               id='<?php echo esc_attr($display_type_name); ?>_gallery_width'
               name='<?php echo esc_attr($display_type_name); ?>[gallery_width]'
               class='nextgen_settings_field_width_and_height'
               placeholder='<?php _e('Width'); ?>'
               min='1'
               required='required'
               value='<?php echo esc_attr($gallery_width); ?>'/> /
        <input type='number'
               id='<?php echo esc_attr($display_type_name); ?>_gallery_height'
               name='<?php echo esc_attr($display_type_name); ?>[gallery_height]'
               class='nextgen_settings_field_width_and_height'
               placeholder='<?php _e('Height'); ?>'
               min='1'
               required='required'
               value='<?php echo esc_attr($gallery_height); ?>'/>
        <label for='<?php echo esc_attr($display_type_name); ?>_gallery_height'>h</label>
    </td>
</tr>
