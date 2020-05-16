<tr id='tr_<?php print esc_attr("{$display_type_name}_show_altview_link"); ?>' class='<?php print !empty($hidden) ? 'hidden' : ''; ?>'>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_show_altview_link' class='tooltip'>
            <?php esc_html_e($show_alt_view_link_label); ?>
			<span>
                <?php esc_html_e($tooltip); ?>
            </span>
        </label>
    </td>
    <td>
		<input type="radio"
			id='<?php echo esc_attr($display_type_name); ?>_show_altview_link'
			name='<?php echo esc_attr($display_type_name); ?>[show_alternative_view_link]'
			class='show_altview_link'
			value='1'
			<?php echo checked(1, intval($show_alternative_view_link)); ?>>
		<label for='<?php echo esc_attr($display_type_name); ?>_show_altview_link'>Yes</label>
		&nbsp;
		<input type="radio"
			id='<?php echo esc_attr($display_type_name); ?>_show_altview_link_no'
			name='<?php echo esc_attr($display_type_name); ?>[show_alternative_view_link]'
			class='show_altview_link'
			value='0'
			<?php echo checked(0, $show_alternative_view_link); ?>/>
		<label for='<?php echo esc_attr($display_type_name); ?>_show_altview_link_no'>No</label>
    </td>
</tr>
