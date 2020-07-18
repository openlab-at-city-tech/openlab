<?php

$state = (strtr($_GET['state'], '-_~', '+/='));

$csrfToken = $state;
$urlState = null;

$splitPos = strpos($state, '|');

if (false !== $splitPos) {
    $csrfToken = substr($state, 0, $splitPos);
    $urlState = substr($state, $splitPos + 1);
}

if (base64_encode(base64_decode($urlState)) === $urlState) {
    $redirectto = base64_decode($urlState);
} else {
    $redirectto = urldecode($_GET['state']);
}

$params = http_build_query($_GET);
$url = $redirectto.'&'.$params;

header('location: '.$url);
die();
