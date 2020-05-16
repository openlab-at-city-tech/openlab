<tr id='tr_<?php print esc_attr("{$display_type_name}_{$name}"); ?>' class='<?php print !empty($hidden) ? 'hidden' : ''; ?>'>
    <td>
        <label for="<?php print esc_attr("{$display_type_name}_{$name}"); ?>"
               <?php if (!empty($text)) { ?>title='<?php print esc_attr($text); ?>'<?php } ?>
               <?php if (!empty($text)) { ?>class='tooltip'<?php } ?>>
            <?php print $label; ?>
        </label>
    </td>
    <td>
        <input type='number'
               step='any'
               id='<?php print esc_attr("{$display_type_name}_{$name}"); ?>'
               name='<?php print esc_attr("{$display_type_name}[{$name}]"); ?>'
               class='<?php print esc_attr("{$display_type_name}[{$name}]"); ?>'
               <?php if (!empty($placeholder)) { ?>placeholder='<?php print esc_attr($placeholder); ?>'<?php } ?>
               <?php if (!empty($min) || $min === 0) { ?>min='<?php print esc_attr($min); ?>'<?php } ?>
               <?php if (!empty($max) || $max === 0) { ?>max='<?php print esc_attr($max); ?>'<?php } ?>
               value='<?php print esc_attr($value); ?>'/>
    </td>
</tr>