<blockquote class="deprecated">
	<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
	<strong>Deprecated:</strong> For more information, please see our <a target='_blank' href='https://www.imagely.com/docs/styles-tab-deprecation'>documentation</a>
</blockquote>
<table class="full-width">
    <tr>
        <td class="column1">
            <label for="activateCSS">
                <?php esc_html_e($activateCSS_label); ?>
            </label>
        </td>
        <td colspan="2">
            <input id="activateCSS" type="radio" name="style_settings[activateCSS]" value="1" <?php checked(1, $activateCSS); ?>/>
            <label for="activateCSS"><?php _e('Yes', 'nggallery'); ?></label>
            &nbsp;
            <input id="activateCSS_no" type="radio" name="style_settings[activateCSS]" value="0" <?php checked(0, $activateCSS); ?>/>
            <label for="activateCSS_no"><?php _e('No', 'nggallery'); ?></label>
        </td>
    </tr>
	<tr id="tr_photocrati-nextgen_styles_activated_stylesheet" class="<?php echo ($activateCSS == 0 ? 'hidden' : ''); ?>">
		<td class="column1">
			<label for="activated_stylesheet">
				<?php esc_html_e($select_stylesheet_label) ?>
			</label>
		</td>
		<td>
			<select id="activated_stylesheet" name="style_settings[CSSfile]">
			<?php foreach ($stylesheets as $value => $p): ?>
				<option
					value="<?php echo esc_attr($value)?>"
					description="<?php echo esc_attr($p['description'])?>"
					author="<?php echo esc_attr($p['author'])?>"
					version="<?php echo esc_attr($p['version'])?>"
					<?php selected($value, $activated_stylesheet)?>
				><?php esc_html_e($p['name'])?></option>
			<?php endforeach ?>
			</select>
            <p class="description">
				<?php _e('Place any custom stylesheets in <strong>wp-content/ngg_styles</strong>', 'nggallery'); ?><br/>
                <?php
                    printf(
                        __("All stylesheets must contain a <a href='#' onclick='%s'>file header</a>", 'nggallery'),
                        'javascript:alert("/*\nCSS Name: Example\nDescription: This is an example stylesheet\nAuthor: John Smith\nVersion: 1.0\n*/");'
                    );
                ?>
            </p>
		</td>
	</tr>
	<tr id="tr_photocrati-nextgen_styles_show_more" class="<?php echo ($activateCSS == 0 ? 'hidden' : ''); ?>">
		<td colspan="2">
			<a
				href="#"
				id="advanced_stylesheet_options"
				class="nextgen_advanced_toggle_link"
				rel="advanced_stylesheet_form"
				hidden_label="<?php echo esc_attr($hidden_label)?>"
				active_label="<?php echo esc_attr($active_label)?>">
				<?php esc_html_e($hidden_label) ?>
			</a>
		</td>
	</tr>
	<tr class="hidden" id="advanced_stylesheet_form">
		<td colspan="2">
			<label for="cssfile_contents" class="align-to-top">
				<?php esc_html_e($cssfile_contents_label)?>
			</label>
			<p
				class="description"
				writable_label="<?php echo esc_attr($writable_label)?>"
				readonly_label="<?php echo esc_attr($readonly_label)?>"
				id="writable_identicator">
			</p>
			<textarea id="cssfile_contents" name="cssfile_contents" <?php echo ($activateCSS == 0 ? 'disabled' : ''); ?>></textarea>
		</td>
	</tr>
</table>