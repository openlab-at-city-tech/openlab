<?php

require_once (B2S_PLUGIN_DIR . '/includes/B2S/View/Modal/General.php');      
$allowed_tags = array(
    'div' => array(
        'id'    => true,
        'class' => true,
        'style' => true,
    ),
    'button' => array(
        'type'         => true,
        'class'        => true,
        'data-dismiss' => true,
    ),
    'a' => array(
        'href'  => true,
        'class' => true,
        'style' => true,
    ),
    'h3' => array(
        'class' => true,
        'style' => true,
    ),
    'h4' => array(
        'class' => true,
        'style' => true,
    ),
    'p' => array(
        'class' => true,
        'style' => true,
    ),
    'strong' => array(),
    'hr' => array(
        'class' => true,
    ),
    'ul' => array(
        'class' => true,
    ),
    'li' => array(
        'class' => true,
    ),
    'span' => array(
        'class' => true,
        'style' => true,
    )
);

$modal= new B2S_View_Modal_General();
echo wp_kses($modal->getModalsHtml($modalNames), $allowed_tags);
    
