<div id="ngg-importML-gallery-selection">
    <label for="ngg-importML-gallery-id"><?php _e('Gallery', 'nggallery'); ?></label>
    <select id="ngg-importML-gallery-id">
        <option value="0"><?php _e('Create a new gallery', 'nggallery'); ?></option>
        <?php foreach ($galleries as $gallery): ?>
            <option value="<?php echo esc_attr($gallery->{$gallery->id_field}) ?>"><?php echo esc_attr($gallery->title) ?></option>
        <?php endforeach ?>
    </select>
    <input type="text" id="ngg-importML-gallery-name" name="gallery_name" placeholder="<?php _e('Gallery title', 'nggallery'); ?>"/>
</div>

<button id='ngg-importML-select-opener'
        class='button-primary'><?php echo $i18n['select-opener']; ?></button>
<button id='ngg-importML-selected-image-import'
        class='button-primary hidden'></button>