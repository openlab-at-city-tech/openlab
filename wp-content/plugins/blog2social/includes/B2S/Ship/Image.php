<?php

class B2S_Ship_Image {

    public $isImage;
    private $imageData = null;
    private $viewMode;

    public function __construct($view = 'normal') {
        $this->viewMode = (isset($view) && !empty($view)) ? $view : null;  //normal or modal(Kalendar)    
    }

    public function setImageData(array $data) {
        $this->imageData = $data;
    }

    public function getItemHtml($postId, $postContent, $postUrl, $userLang = 'en') {
        $firstImage = "";
        if ($this->imageData) {
            $imageData = $this->imageData;
        } else {
            $hook_filter = new B2S_Hook_Filter();
            $imageData = $hook_filter->get_wp_post_image($postId, false, $postContent, $postUrl, true, $userLang);
        }
        
        $isImage = (is_array($imageData) && !empty($imageData)) ? true : false;

        $content = '<div class="col-xs-12 del-padding-left del-padding-right"><div class="alert alert-danger b2s-upload-image-invalid-extension" style="display:none;"><span id="b2s-upload-image-invalid-extension-file-name"></span>' . esc_html__('The images file types .jpg and .png are allowed. Please try another.', 'blog2social') . '</div></div>';
        $content .= '<div class="col-xs-12 del-padding-left del-padding-right"><div class="alert alert-info b2s-upload-image-no-permission" style="display:none;">' . esc_html__('You need a higher user role to upload an image on this blog. Please contact your administrator.', 'blog2social') . '</div></div>';
        $content .= '<div class="col-xs-12 del-padding-left del-padding-right"><div class="alert alert-danger b2s-upload-image-free-version-info" style="display:none;">' . esc_html__('To select an individual image from your media library,', 'blog2social');
        $content .= ' <a target="_blank" href="' . esc_url(B2S_Tools::getSupportLink('affiliate')) . '">' . esc_html__('please upgrade', 'blog2social') . '</a>';
        $content .= '</div></div>';
        $content .= '<div class="col-xs-12 del-padding-left del-padding-right"><div class="alert alert-info b2s-gif-support-info" style="display:none;">' . esc_html__('The networks Diigo, Bloglovin’ and Google Business Profile do not support the GIF image format and will display your standard image instead.', 'blog2social') . '</div></div>';
        $content .= '<div class="col-xs-12 del-padding-left del-padding-right"><div class="alert alert-primary b2s-change-meta-image-info" style="display:none;">' . esc_html__('By changing your image in the link post format it will be changed for all networks listed below. This also applies for all scheduled posts in this post format. ', 'blog2social') . '<a href="'.esc_url(B2S_Tools::getSupportLink('open_graph_tags')).'" target="_blank">' . esc_html__('More Information', 'blog2social') . '</a><br><br><div class="b2s-change-meta-image-networks"></div></div></div>';

        $content .= '<div class="row b2s-image-size-info">';
        $content .='<div class="col-xs-12 hidden-xs hidden-sm">';
        $content .='<i class="b2s-multi-image-info-text">' . esc_html__('Sharing more than one image improves the visibility of your content. You can create image series, show sequences, and level up your storytelling. With Blog2Social you can share up to 10 images per post on Instagram, up to 4 images in one post on Facebook (page and group) and Twitter.', 'blog2social') . '</i>';
        $content .='<i class="b2s-multi-image-info-text">' . esc_html__('The best size for images in social media posts are between: 667-1000px X 523-1000px. Blog2Social will automatically resize your image according to the requirements of each network.', 'blog2social') . '</i>';
        $content .='<i class="b2s-default-image-info-text">' . esc_html__('The best size for images in social media posts are between: 667-1000px x 523-1000px. Blog2Social will automatically resize your image according to the network requirements. You can also share up to 4 images in one post on Facebook (page and group) and on Twitter.', 'blog2social') . '</i>';
        $content .='<i> ' . esc_html__('Changes to the image for link-posts will apply to the image for all networks with link-post settings for this post.', 'blog2social') . '</i>';
        $content .='</div>';
        $content .='</div>';
        $tempCountImage = 0;
        $content .= '<div class="b2s-image-choose-area">';

        if ($isImage) {
            foreach ($imageData as $key => $image) {
                $checked = (($tempCountImage == 0) ? "checked" : "");
                $firstImage = ($tempCountImage == 0) ? $image[0] : $firstImage;
                $content .= '<div class="b2s-image-item">';
                $content .= '<div class="b2s-image-item-thumb">';
                $content .= '<label for="b2s-image-count-' . esc_attr($tempCountImage) . '">';
                $content .= '<img class="img-thumbnail networkImage img-responsive" alt="blogImage" src="' . esc_url($image[0]) . '">';
                $content .= '</label>';
                $content .= '</div>';
                $content .= '<div class="b2s-image-item-caption text-center">';
                $content .= '<div class="b2s-image-item-caption-resolution clearfix small"></div>';
                $content .= '<input type="radio" value="' . esc_url($image[0]) . '" class="checkNetworkImage" name="image_url" id="b2s-image-count-' . $tempCountImage . '" ' . $checked . '>';
                $content .= '</div>';
                $content .= '</div>';
                $tempCountImage++;
            }
        } else {
            if($this->viewMode != 'curation') {
                $content .= '<div class="col-xs-12 del-padding-left del-padding-right b2s-choose-image-no-image-info-text"><br><div class="alert alert-info">' . esc_html__('No images are included in your post.', 'blog2social') . '</div></div>';
            } else {
                $content .= '<br>';
            }
        }

        $content .= '</div>';
        $content .= '<input type="hidden" class="b2s-choose-image-count" value="' . esc_attr($tempCountImage) . '">';
        $content .= '<div class="col-xs-12  del-padding-left del-padding-right">';

        if ($this->viewMode != 'modal') {
            if($this->viewMode != 'curation'){
                $content .= ' <button class="btn btn-primary b2s-image-change-all-network b2s-image-change-btn-area" data-network-id="" data-post-id="' . esc_attr($postId) . '">' . esc_html__('Apply image for all posts', 'blog2social') . '</button>';
            } else {
                $content .= ' <button class="btn btn-primary b2s-image-change b2s-image-change-btn-area">' . esc_html__('Apply image', 'blog2social') . '</button>';
            }
        }

        if (B2S_PLUGIN_USER_VERSION > 0) {
            $content .= '<button class="btn btn-primary b2s-image-change-this-network b2s-image-change-btn-area" ' . (!$isImage ? 'style="display:none"' : '') . ' data-network-auth-id="" data-network-id="" data-meta-type="" data-post-id="' . esc_attr($postId) . '">' . esc_html__('Apply image for this post', 'blog2social') . '</button>';
            $content .= '<button class="btn btn-primary b2s-image-change-meta-network b2s-image-change-btn-area" ' . (!$isImage ? 'style="display:none"' : '') . ' data-network-auth-id="" data-network-id="" data-meta-type="" data-post-id="' . esc_attr($postId) . '">' . esc_html__('Apply image for this post', 'blog2social') . '</button>';
            $content .= '<button class="btn btn-primary b2s-image-add-this-network b2s-image-change-btn-area" ' . (!$isImage ? 'style="display:none"' : '') . ' data-network-auth-id="" data-network-id="" data-image-count="" data-post-id="' . esc_attr($postId) . '">' . esc_html__('Apply image for this image gallery', 'blog2social') . '</button>';
        } else {
            $content .= '<button class="btn btn-primary b2s-btn-disabled b2s-upload-image-free-version b2s-image-change-btn-area" ' . (!$isImage ? 'style="display:none"' : '') . ' data-post-id="' . esc_attr($postId) . '">' . esc_html__('Apply image for this post', 'blog2social') . ' <span class="label label-success">' . esc_html__('SMART', 'blog2social') . ' </span></button>';
        }

        if (current_user_can('upload_files')) {
            if (B2S_PLUGIN_USER_VERSION > 1) {
                $content .= '<button class="btn btn-primary b2s-upload-image">' . esc_html__('Image upload / Media Gallery', 'blog2social') . '</button>';
            } else {
                $content .= '<button class="btn btn-primary b2s-btn-disabled b2s-upload-image-free-version">' . esc_html__('Image upload / Media Gallery', 'blog2social') . ' <span class="label label-success">' . esc_html__('PRO', 'blog2social') . ' </span></button>';
            }
        }
        $content .= '</div>';
        $content .= '<input type="hidden" id="b2s_wp_media_headline" value="' . esc_attr__('Select or upload an image from media gallery', 'blog2social') . '">';
        $content .= '<input type="hidden" id="b2s_wp_media_btn" value="' . esc_attr__('Use image', 'blog2social') . '">';
        $content .= '<input type="hidden" id="b2s_blog_default_image" value="' . esc_url($firstImage) . '">';
        $content .= "<input type='hidden' id='blog_image' name='blog_image' value='" . esc_attr(trim($isImage)) . "'>";
        return $content;
    }

}
