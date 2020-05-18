<tr>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_quality'>
            <?php esc_html_e($quality_label); ?>
        </label>
    </td>
    <td>
        <input type='number'
               id='<?php echo esc_attr($display_type_name); ?>_quality'
               name='<?php echo esc_attr($display_type_name); ?>[quality]'
               class='ngg_singlepic_quality'
               placeholder='quality %'
               min='1'
               max='100'
               value='<?php echo esc_attr($quality); ?>'>
    </td>
</tr>
