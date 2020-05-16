<tr id='tr_<?php print esc_attr("{$display_type_name}_alternative_view"); ?>' class='<?php print !empty($hidden) ? 'hidden' : ''; ?>'>
    <td>
        <label for='<?php echo esc_attr($display_type_name); ?>_alternative_view' class="tooltip">
            <?php esc_html_e($show_alt_view_link_label); ?>
			<span>
				<?php esc_html_e($tooltip) ?>
			</span>
        </label>
    </td>
    <td>
		<select class="ngg_alternative_view" name='<?php echo esc_attr($display_type_name); ?>[alternative_view]'>
			<option value="0" <?php echo selected(0, $alternative_view) ?>>None</option>
			<?php foreach ($altviews as $altview_name => $altview_properties): ?>
			<option value="<?php echo esc_attr($altview_name)?>" <?php echo selected($altview_name, $alternative_view)?>>
				<?php esc_html_e($altview_properties['title'])?>
			</option>
			<?php endforeach ?>
		</select>
    </td>
</tr>
