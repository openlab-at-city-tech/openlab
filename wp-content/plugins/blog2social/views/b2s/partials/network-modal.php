<?php

require_once (B2S_PLUGIN_DIR . '/includes/B2S/View/Modal/Network.php');      

$allowed_tags = array(
    'div' => array(
        'class' => true,
        'style' => true,
        'role'  => true,
    ),
    'button' => array(
        'type'         => true,
        'class'        => true,
        'data-dismiss' => true,
    ),
    'ul' => array(
        'class'            => true,
        'data-network-id'  => true,
        'data-network-id' => true,
    ),
    'li' => array(
        'class' => true,
    ),
    'p' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'strong' => array(),
    'span' => array(
        'class' => true,
        'style' => true,
    ),
    'h1' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'h2' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'h3' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'h4' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'a' => array(
        'href'  => true,
        'class' => true,
        'style' => true,
        'data-network-id' => true,
    ),
    'img' => array(
        'class' => true,
        'style' => true,
        'data-network-id' => true,
        'src' => true
    ),
);

$modal= new B2S_View_Modal_Network();
echo wp_kses($modal->getHtml(), $allowed_tags);