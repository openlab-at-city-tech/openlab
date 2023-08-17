<?php
header('Content-Type: application/json');
error_reporting(0);

include 'config.php';

if(!isset($_GET['glang']) or !isset($_POST['body']))
    exit;

// check if body is base64_encoded
if(!preg_match('/^[a-zA-Z0-9+\/]+={0,2}$/', $_POST['body']))
    exit;

$glang = $_GET['glang'];
$body = json_encode(array('email-body' => base64_encode(base64_decode($_POST['body'])), 'gt_translate_keys' => array(array('key' => 'email-body', 'format' => 'html-base64encoded'))));

$main_lang = isset($data['default_language']) ? $data['default_language'] : $main_lang;

if($glang == $main_lang) {
    exit;
}

$wp_config_dir = dirname(__FILE__, 5) . '/wp-config.php';
if(file_exists($wp_config_dir) and isset($_POST['access_key'])) {
    include $wp_config_dir;
    if(md5(substr(NONCE_SALT, 0, 10) . substr(NONCE_KEY, 0, 5)) != $_POST['access_key'])
        exit;
} else {
    exit;
}

if(!isset($_POST['subject'])) {
    die($body);
}

if(!function_exists('curl_init')) {
    if(function_exists('http_response_code'))
        http_response_code(500);

    echo 'PHP Curl library is required';
    exit;
}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://tdns.gtranslate.net/tdn-bin/email-translate?lang='.$glang);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
if(defined('CURL_IPRESOLVE_V4')) curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

// Debug
if($debug) {
    $fh = fopen(dirname(__FILE__).'/debug.txt', 'a');
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, $fh);
}

$response = curl_exec($ch);
$response_info = curl_getinfo($ch);
curl_close($ch);

echo $response;