<?php $this->start_element('nextgen_gallery.gallery_container', 'container', $displayed_gallery); ?>
<div class="ngg-albumoverview default-view">
    <?php foreach ($galleries as $gallery) {
        if ($open_gallery_in_lightbox && $gallery->entity_type == 'gallery') {
            $anchor = $gallery->displayed_gallery->effect_code . "
                      href='" . nextgen_esc_url($gallery->pagelink) . "'
                      data-src='" . esc_attr($gallery->previewpic_fullsized_url) . "'
                      data-fullsize='" . esc_attr($gallery->previewpic_fullsized_url) . "'
                      data-thumbnail='" . esc_attr($gallery->previewurl) . "'
                      data-title='" . esc_attr($gallery->previewpic_image->alttext) . "'
                      data-description='" . esc_attr(stripslashes($gallery->previewpic_image->description)) . "'
                      data-image-id='" . esc_attr($gallery->previewpic) . "'";
        } else {
            $anchor = "class='gallery_link' href='" . nextgen_esc_url($gallery->pagelink) . "'";
        } ?>
        <div class="ngg-album">
            <div class="ngg-albumcontent">
                <div class="ngg-thumbnail">
                    <a <?php echo $anchor; ?>>
                        <img class="Thumb"
                             alt="<?php echo esc_attr($gallery->title); ?>"
                             src="<?php echo nextgen_esc_url($gallery->previewurl); ?>"/>
                    </a>
                </div>
                <div class="ngg-albumtitle">
                  <a <?php echo $anchor; ?>><?php print wp_kses($gallery->title, M_I18N::get_kses_allowed_html()); ?></a>
                </div>
                <div class="ngg-description">
                    <p><?php print wp_kses($gallery->galdesc, M_I18N::get_kses_allowed_html()); ?></p>
                    <?php if (isset($gallery->counter) && $gallery->counter > 0) { ?>
                        <p class="ngg-album-gallery-image-counter"><strong><?php echo $gallery->counter; ?></strong>&nbsp;<?php _e('Photos', 'nggallery'); ?></p>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php echo $pagination; ?>
</div>
<?php $this->end_element(); ?>
