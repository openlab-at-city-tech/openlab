<?php
/**
 * @var array $watermark_fields
 * @var array $watermark_sources
 * @var int $offset_x
 * @var int $offset_y
 * @var string $active_label
 * @var string $hidden_label
 * @var string $offset_label
 * @var string $position_label
 * @var string $preview_label
 * @var string $refresh_url
 * @var string $refresh_label
 * @var string $thumbnail_url
 * @var string $watermark_automatically_at_upload_label
 * @var string $watermark_automatically_at_upload_label_no
 * @var string $watermark_automatically_at_upload_label_yes
 * @var string $watermark_automatically_at_upload_value
 * @var string $watermark_source
 * @var string $watermark_source_label
 */
?>
<table>
    <tr>
        <td class="column1">
            <label for="watermark_automatically_at_upload">
                <?php print esc_html($watermark_automatically_at_upload_label); ?>
            </label>
        </td>
        <td>
            <label for="watermark_automatically_at_upload">
                <?php print esc_html($watermark_automatically_at_upload_label_yes); ?>
            </label>
            <input id='watermark_automatically_at_upload'
                   type="radio"
                   name="watermark_options[watermark_automatically_at_upload]"
                   value="1"
                   <?php checked(TRUE, $watermark_automatically_at_upload_value ? TRUE : FALSE)?>/>
            <label for="watermark_automatically_at_upload_no">
                <?php print esc_html($watermark_automatically_at_upload_label_no); ?>
            </label>
            <input id='watermark_automatically_at_upload_no'
                   type="radio"
                   name="watermark_options[watermark_automatically_at_upload]"
                   value="0"
                   <?php checked(FALSE, $watermark_automatically_at_upload_value ? TRUE : FALSE)?>/>
        </td>
    </tr>
	<tr>
		<td class="column1">
			<label for="watermark_source">
				<?php esc_html_e($watermark_source_label)?>
			</label>
		</td>
		<td>
			<div class="column_wrapper">
				<select name="watermark_options[wmType]" id="watermark_source">
				<?php foreach ($watermark_sources as $label => $value): ?>
					<option value="<?php echo esc_attr($value)?>"
						    <?php selected($value, $watermark_source) ?>>
                        <?php esc_html_e($label)?>
                    </option>
				<?php endforeach ?>
				</select>
			</div>
		</td>
	</tr>

    <tr class="watermark_field hidden">
        <td>
            <?php echo $position_label; ?>
        </td>
        <td>
            <table class='nextgen_settings_position' border='1'>
                <tr>
                    <td><input type="radio" name="watermark_options[wmPos]" value="topLeft"   <?php checked('topLeft',   $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="topCenter" <?php checked('topCenter', $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="topRight"  <?php checked('topRight',  $position); ?>/></td>
                </tr>
                <tr>
                    <td><input type="radio" name="watermark_options[wmPos]" value="midLeft"   <?php checked('midLeft',   $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="midCenter" <?php checked('midCenter', $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="midRight"  <?php checked('midRight',  $position); ?>/></td>
                </tr>
                <tr>
                    <td><input type="radio" name="watermark_options[wmPos]" value="botLeft"   <?php checked('botLeft',   $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="botCenter" <?php checked('botCenter', $position); ?>/></td>
                    <td><input type="radio" name="watermark_options[wmPos]" value="botRight"  <?php checked('botRight',  $position); ?>/></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr class="watermark_field hidden">
        <td>
            <?php echo $offset_label; ?>
        </td>
        <td>
            <label for='nextgen_settings_wmXpos'>w</label>
            <input type='number'
                   id='nextgen_settings_wmXpos'
                   name='watermark_options[wmXpos]'
                   placeholder='0'
                   min='0'
                   value='<?php echo esc_attr($offset_x) ?>'/> /
            <input type='number'
                   id='nextgen_settings_wmYpos'
                   name='watermark_options[wmYpos]'
                   placeholder='0'
                   min='0'
                   value='<?php echo esc_attr($offset_y) ?>'/>
            <label for='nextgen_settings_wmYpos'>h</label>
        </td>
    </tr>

    <?php if (!is_null($thumbnail_url)) { ?>
        <tr class="watermark_field hidden">
            <td>
                <?php echo $preview_label; ?>
            </td>
            <td>
                <img src='<?php echo $thumbnail_url; ?>'/>
                <button id='nextgen_settings_preview_refresh' class="button-primary" data-refresh-url='<?php echo $refresh_url; ?>'><?php echo $refresh_label; ?></button>
            </td>
        </tr>
    <?php } ?>

    <tr class="watermark_field hidden">
		<td colspan="2">
			<a
				id="watermark_customization"
				href="#"
				class="nextgen_advanced_toggle_link"
				hidden_label="<?php echo esc_attr($hidden_label)?>"
				active_label="<?php echo esc_attr($active_label)?>"
			>
			<?php esc_html_e($hidden_label)?>
			</a>
		</td>
	</tr>
	<?php foreach ($watermark_fields as $source_name => $fields): ?>
	<tbody class="hidden" id="watermark_<?php echo esc_attr($source_name) ?>_source">
		<?php echo $fields ?>
	</tbody>
	<?php endforeach ?>
</table>