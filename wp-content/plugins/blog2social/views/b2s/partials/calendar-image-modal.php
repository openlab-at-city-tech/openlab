<?php
$image = new B2S_Ship_Image($view = 'modal');
if (!empty($_POST['image_url'])) {
    $image->setImageData(array(array($_POST['image_url'])));
}
echo $image->getItemHtml($postData->ID, $postData->post_content, $postUrl, substr(B2S_LANGUAGE, 0, 2));                 