<tr>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_link'>
            <?php esc_html_e($link_label); ?>
        </label>
    </td>
    <td>
        <input type='text'
               id='<?php echo esc_attr($display_type_name); ?>_link'
               name='<?php echo esc_attr($display_type_name); ?>[link]'
               class='ngg_singlepic_link'
               placeholder='http://...'
               value='<?php echo esc_attr($link); ?>'>
    </td>
</tr>
