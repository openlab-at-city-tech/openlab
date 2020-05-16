<tr>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_template'
               class='tooltip'
               title="<?php esc_html_e($template_text); ?>">
            <?php esc_html_e($template_label); ?>
        </label>
    </td>
    <td>
        <div class='ngg_settings_template_wrapper'>
            <select name='<?php echo esc_attr($display_type_name); ?>[template]'
                    id='<?php echo esc_attr($display_type_name); ?>_template>'
                    class='ngg_thumbnail_template ngg_settings_template'>
                <option></option> 
                <?php foreach ($templates as $file => $label): ?>
                    <?php if ($file && $label): ?>
                    <option value="<?php echo esc_attr($file) ?>" <?php selected($chosen_file, $file, TRUE); ?>><?php esc_html_e($label); ?></option>
                    <?php endif ?>
                <?php endforeach ?>
            </select>
        </div>
    </td>
</tr>
