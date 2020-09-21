<?php
/**
 * @var string $lightbox_library_label
 * @var C_NGG_Lightbox[] $libs
 * @var string $selected
 * @var string[] $sub_fields
 * @var string $lightbox_global
 */
?>
<?php $this->start_element('admin_page.other_options_lightbox_libraries', 'container'); ?>
<table>
    <!-- Lightbox Library Name -->
	<tr>
		<td class="column1">
			<label for="lightbox_library"><?php esc_html_e($lightbox_library_label)?></label>
		</td>
		<td>
			<select name="lightbox_library_id" id="lightbox_library">
				<?php foreach ($libs as $lib) { ?>
                    <option value="<?php echo esc_attr($lib->name)?>"
                            <?php selected($lib->name, $selected, TRUE)?>
                            data-library-name='<?php echo $lib->name; ?>'>
                        <?php if (isset($lib->title) && $lib->title) { ?>
                            <?php esc_html_e($lib->title) ?>
                        <?php } else { ?>
                            <?php esc_html_e($lib->name) ?>
                        <?php } ?>
                    </option>
                <?php } ?>
            </select>
        </td>
    </tr>

    <tbody>
        <tr>
            <td class="column1">
                <label for="lightbox_global"><?php esc_html_e('What must the lightbox be applied to?', 'nggallery')?></label>
            </td>
            <td>
                <select name="thumbEffectContext" id="lightbox_global">
                    <option value="nextgen_images" <?php selected('nextgen_images', $lightbox_global, TRUE)?>><?php esc_html_e('Only apply to NextGEN images', 'nggallery'); ?></option>
                    <option value="nextgen_and_wp_images" <?php selected('nextgen_and_wp_images', $lightbox_global, TRUE)?>><?php esc_html_e('Only apply to NextGEN and WordPress images', 'nggallery'); ?></option>
                    <option value="all_images" <?php selected('all_images', $lightbox_global, TRUE)?>><?php esc_html_e('Try to apply to all images', 'nggallery'); ?></option>
                    <option value="all_images_direct" <?php selected('all_images_direct', $lightbox_global, TRUE)?>><?php esc_html_e('Try to apply to all images that link to image files', 'nggallery'); ?></option>
                </select>
            </td>
        </tr>
    </tbody>

    <?php foreach ($sub_fields as $name => $form) { ?>
        <tbody class="lightbox_library_settings hidden" id="lightbox_library_<?php echo esc_attr($name); ?>">
            <?php echo $form; ?>
        </tbody>
    <?php } ?>
</table>
<?php $this->end_element(); ?>