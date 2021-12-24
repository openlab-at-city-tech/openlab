<textarea id="fl-{{data.name}}-textarea" name="{{data.name}}" rows="7">{{data.value}}</textarea>
<br/><br/>
<span class="fl-field-description"><?php esc_html_e('Edit this module via the Module Builder or manually via the raw code', 'wpcloudplugins'); ?></span>
<br/><br/>
<button id="fl-{{data.name}}-select" class="fl-builder-button fl-builder-button" href="javascript:void(0);" onclick="return false;"><?php esc_html_e('Edit via Shortcode Builder', 'wpcloudplugins'); ?></button>