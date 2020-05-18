<?php if (!empty($image)): ?>
    <?php
    
    $this->start_element('nextgen_gallery.gallery_container', 'container', $displayed_gallery);
    
		$image_size = $storage->get_original_dimensions($image);

		if ($image_size == null) {
			$image_size['width'] = $image->meta_data['width'];
			$image_size['height'] = $image->meta_data['height'];
		}
		
		$image_ratio = $image_size['width'] / $image_size['height'];
    
    $width = isset($settings['width']) ? $settings['width'] : null;
    $height = isset($settings['height']) ? $settings['height'] : null;
    
    $width = intval($width);
    $height = intval($height);
		
		if ($width != null && $height != null)
		{
			// check image aspect ratio, avoid distortions
			$aspect_ratio = $width / $height;
			if ($image_ratio > $aspect_ratio) {
				if ($image_size['width'] > $width) {
					$height = (int) round($width / $image_ratio);
				}
			}
			else {
				if ($image_size['height'] > $height) {
					$width = (int) round($height * $image_ratio);
				}
			}
			
			// Ensure that height is always null, or else the image won't be responsive correctly
			$height = null;
		}
		else if ($height != null)
		{
			$width = (int) round($height * $image_ratio);
			// Ensure that height is always null, or else the image won't be responsive correctly
			$height = null;
		}
		
		$style = null;
		
		if ($width) {
			$style .= 'max-width: ' . $width . 'px';
		}
		
		if ($height) {
			$style .= 'max-height: ' . $height . 'px';
		}

    ?>
    <?php $this->start_element('nextgen_gallery.image_panel', 'item', $image); ?>
    
		<div class="ngg-gallery-singlepic-image <?php echo $settings['float']; ?>" style="<?php echo esc_attr($style); ?>">
			<?php

			$this->start_element('nextgen_gallery.image', 'item', $image);
			
			?>
    	<a href="<?php echo esc_url($settings['link']); ?>"
		     title="<?php echo esc_attr($image->description)?>"
             data-src="<?php echo esc_attr($storage->get_image_url($image)); ?>"
             data-thumbnail="<?php echo esc_attr($storage->get_image_url($image, 'thumb')); ?>"
             data-image-id="<?php echo esc_attr($image->{$image->id_field}); ?>"
             data-title="<?php echo esc_attr($image->alttext); ?>"
             data-description="<?php echo esc_attr(stripslashes($image->description)); ?>"
             target='<?php echo esc_attr($target); ?>'
             <?php echo $effect_code ?>>
            <img class="ngg-singlepic"
             src="<?php echo $thumbnail_url; ?>"
             alt="<?php echo esc_attr($image->alttext); ?>"
             title="<?php echo esc_attr($image->alttext); ?>"
             <?php if ($width) { ?> width="<?php echo esc_attr($width); ?>" <?php } ?>
             <?php if ($height) { ?> height="<?php echo esc_attr($height); ?>" <?php } ?> />
    	</a>
		  <?php
		  
		 		$this->end_element(); 
		 	?>
    </div>
    <?php if (!is_null($inner_content)) { ?><span><?php echo $inner_content; ?></span><?php } ?>
    <?php
    
   		$this->end_element(); 
   		
   		$this->end_element(); 
    ?>
<?php else: ?>
    <p>No image found</p>
<?php endif ?>
