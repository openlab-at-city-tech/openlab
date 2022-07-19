<?php
$image = new B2S_Ship_Image($view = 'modal');
if (!empty($_POST['image_url'])) {
    $image->setImageData(array(array(sanitize_text_field($_POST['image_url']))));
}
echo wp_kses($image->getItemHtml($postData->ID, $postData->post_content, $postUrl, substr(B2S_LANGUAGE, 0, 2)), array(
    'div' => array(
        'class' => array(),
        'style' => array(),
    ),
    'span' => array(
        'id' => array(),
    ),
    'a' => array(
        'target' => array(),
        'href' => array(),
    ),
    'i' => array(
        'class' => array(),
    ),
    'label' => array(
        'for' => array(),
    ),
    'img' => array(
        'class' => array(),
        'alt' => array(),
        'src' => array(),
    ),
    'input' => array(
        'class' => array(),
        'type' => array(),
        'value' => array(),
        'id' => array(),
        'name' => array(),
        'checked' => array(),
    ),
    'br' => array(),
    'button' => array(
        'class' => array(),
        'data-network-id' => array(),
        'data-post-id' => array(),
        'data-network-auth-id' => array(),
        'data-meta-type' => array(),
        'data-image-count' => array(),
        'style' => array()
    )
));                 