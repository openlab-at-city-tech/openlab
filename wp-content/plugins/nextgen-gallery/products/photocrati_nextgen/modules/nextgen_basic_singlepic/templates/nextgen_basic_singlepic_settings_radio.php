<tr>
    <td>
        <label for="<?php print esc_attr("{$display_type_name}_{$name}"); ?>"
               <?php if (!empty($text)) { ?>title='<?php print esc_attr($text); ?>'<?php } ?>
               <?php if (!empty($text)) { ?>class='tooltip'<?php } ?>>
            <?php print $label; ?>
        </label>
    </td>
    <td>
        <input type="radio"
               id="<?php print $display_type_name . '_' . $name; ?>"
               name="<?php print $display_type_name . '[' . $name . ']'; ?>"
               class="<?php print $display_type_name . '_' . $name; ?>"
               value="1"
            <?php checked(1, $value); ?>/>
        <label for="<?php print $display_type_name . '_' . $name; ?>"><?php _e('Yes'); ?></label>
        &nbsp;
        <input type="radio"
               id="<?php print $display_type_name . '_' . $name; ?>_no"
               name="<?php print $display_type_name . '[' . $name . ']'; ?>"
               class="<?php print $display_type_name . '_' . $name; ?>"
               value="0"
            <?php checked(0, $value); ?>/>
        <label for="<?php print $display_type_name . '_' . $name; ?>_no"><?php _e('No'); ?></label>
    </td>
</tr>
