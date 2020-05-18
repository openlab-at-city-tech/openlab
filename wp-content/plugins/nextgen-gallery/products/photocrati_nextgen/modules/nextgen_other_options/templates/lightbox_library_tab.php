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

    <?php foreach ($sub_fields as $name => $form) { ?>
        <tbody class="lightbox_library_settings hidden" id="lightbox_library_<?php echo esc_attr($name); ?>">
            <?php echo $form; ?>
        </tbody>
    <?php } ?>
    <?php if ( !defined('NGG_PRO_PLUGIN_VERSION') && !defined('NGG_PLUS_PLUGIN_VERSION') && !is_multisite() ) { ?>
        <tr>
            <td colspan="2" class="ngg_options_promo">
                <?php esc_html_e('Want a stunning, full screen, lightbox with customization options?', 'nggallery'); ?><a href="https://www.imagely.com/wordpress-gallery-plugin/pro-lightbox-demo/?utm_source=ngg&utm_medium=ngguser&utm_campaign=pro_lightbox" target="_blank"><?php esc_html_e('Get the Pro Lightbox!', 'nggallery'); ?></a>
            </td>
        </tr>
    <?php } ?>
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
</table>
