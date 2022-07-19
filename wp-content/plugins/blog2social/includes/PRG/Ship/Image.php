<?php

class PRG_Ship_Image {

    public function __construct() {
         
    }

   public function getItemHtml($postId,$postContent,$postUrl,$userLang='en') {
        $content = '';
        $hook_filter = new B2S_Hook_Filter();
        $imageData = $hook_filter->get_wp_post_image($postId, false, $postContent,$postUrl, true,$userLang);
        $isImage = (is_array($imageData) && !empty($imageData)) ? true : false;

        if ($isImage) {
            $tempCountImage = 0;
            foreach ($imageData as $key => $image) {
                $content .='<div class="prg-image-item">';
                $content .='<div class="prg-image-item-thumb">';
                $content .='<label for="prg-image-count-' . esc_attr($tempCountImage) . '">';
                $content .='<img class="img-thumbnail" alt="blogImage" src="' . esc_url($image[0]) . '">';
                $content .= '</label>';
                $content .='</div>';
                $content .='<div class="prg-image-item-caption text-center">';
                $content .= '<input  ' . (($tempCountImage == 0) ? 'checked="checked"' : '') . ' type="radio" value="' . esc_attr($image[0]) . '" name="bild" class="prgImage" id="prg-image-count-' . esc_attr($tempCountImage) . '">';
                $content .= '</div>';
                $content .= '</div>';
                $tempCountImage++;
            }
        }

        return $content;
    }

}
