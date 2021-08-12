<?php $this->start_element('nextgen_gallery.gallery_container', 'container', $displayed_gallery); ?>

<div class="ngg-galleryoverview carousel-view <?php if (!intval($ajax_pagination)) echo ' ngg-ajax-pagination-none'; ?>"
	id="ngg-gallery-<?php echo esc_attr($displayed_gallery_id)?>-<?php echo esc_attr($current_page)?>">

    <div class="ngg-basic-thumbnails-carousel">
        <?php
        $image_size = $storage->get_image_dimensions($current_image, 'full');
        ?>
        <a href="<?php echo esc_attr($storage->get_current_image_url($current_image, 'full', TRUE))?>"
           title="<?php echo esc_attr($current_image->description)?>"
           data-src="<?php echo esc_attr($storage->get_current_image_url($current_image)); ?>"
           data-thumbnail="<?php echo esc_attr($storage->get_current_image_url($current_image, 'thumb')); ?>"
           data-current_image-id="<?php echo esc_attr($current_image->{$current_image->id_field}); ?>"
           data-title="<?php echo esc_attr($current_image->alttext); ?>"
           data-description="<?php echo esc_attr(stripslashes($current_image->description)); ?>"
           data-current_image-slug="<?php echo esc_attr($current_image->current_image_slug); ?>"
            <?php echo $effect_code ?>>
            <img title="<?php echo esc_attr($current_image->alttext)?>"
                 alt="<?php echo esc_attr($current_image->alttext)?>"
                 src="<?php echo esc_attr($storage->get_current_image_url($current_image, 'full'))?>"
                 width="<?php echo esc_attr($image_size['width'])?>"
                 height="<?php echo esc_attr($image_size['height'])?>"
                 style="max-width: <?php print esc_attr($image_size['width']); ?>px;"/>
        </a>
    </div>

    <div class="ngg-basic-thumbnails-carousel-list">
        <?php $this->start_element('nextgen_gallery.image_list_container', 'container', $images); ?>
            <?php

            $application = C_Router::get_instance()->get_routed_app();
            $controller = C_Display_Type_Controller::get_instance();

            for ($i = 0; $i < count($images); $i++) {
                $image = $images[$i];
                $thumb_size = $storage->get_image_dimensions($image, $thumbnail_size_name);
                $style = isset($image->style) ? $image->style : null;

                if (isset($image->hidden) && $image->hidden) {
                    $style = 'style="display: none;"';
                } else {
                    $style = null;
                }

                $this->start_element('nextgen_gallery.image_panel', 'item', $image);
                ?>
                    <div id="<?php echo esc_attr('ngg-image-' . $i) ?>"
                         class="ngg-basic-thumbnails-carousel-thumbnail" <?php if ($style) echo $style; ?>>
                        <?php $this->start_element('nextgen_gallery.image', 'item', $image); ?>
                        <?php $href = $controller->set_param_for($application->get_routed_url(TRUE), 'pid', $image->image_slug); ?>
                            <div class="ngg-gallery-thumbnail">
                                <a href="<?php echo esc_attr($href); ?>"
                                   title="<?php echo esc_attr($image->description)?>">
                                    <img title="<?php echo esc_attr($image->alttext)?>"
                                         alt="<?php echo esc_attr($image->alttext)?>"
                                         src="<?php echo esc_attr($storage->get_image_url($image, $thumbnail_size_name))?>"
                                         width="<?php echo esc_attr($thumb_size['width'])?>"
                                         height="<?php echo esc_attr($thumb_size['height'])?>"/>
                                </a>
                            </div>
                        <?php $this->end_element(); ?>
                    </div>
                <?php $this->end_element(); ?>
            <?php } ?>
        <?php $this->end_element(); ?>
    </div>

    <?php if (!empty($slideshow_link)) { ?>
        <div class="slideshowlink">
            <a href='<?php echo esc_attr($slideshow_link) ?>'><?php echo esc_html($slideshow_link_text) ?></a>
        </div>
    <?php } ?>

    <?php if ($pagination) { ?>
        <?php echo $pagination ?>
    <?php } ?>
</div>

<?php $this->end_element(); ?>