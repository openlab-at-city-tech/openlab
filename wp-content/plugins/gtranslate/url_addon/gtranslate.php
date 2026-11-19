<?php
// direct file access is intentional here because this file acts as an entry point for the reverse proxy system
if(!defined('ABSPATH'))
    define('ABSPATH', dirname(__DIR__, 4) . '/');
if(!defined('ABSPATH'))
    exit;

error_reporting(0);

include 'config.php';

if(!isset($_GET['glang']) or !isset($_GET['gurl']))
    exit;

$glang = $_GET['glang'];

// pick a server based on hostname
$server_id = intval(substr(md5(preg_replace('/^www\./', '', $_SERVER['HTTP_HOST'])), 0, 5), 16) % count($servers);
$server = $servers[$server_id];

$page_url = '/'.$_GET['gurl'];

$page_url_segments = explode('/', $page_url);
foreach($page_url_segments as $i => $segment) {
    $page_url_segments[$i] = rawurlencode($segment);
}
$page_url = implode('/', $page_url_segments);

$get_params = $_GET;
if(isset($get_params['glang']))
    unset($get_params['glang']);
if(isset($get_params['gurl']))
    unset($get_params['gurl']);

if(count($get_params)) {
    $page_url .= '?' . rtrim(str_replace('=&', '&', http_build_query($get_params, '', '&', PHP_QUERY_RFC3986)), '=');
}

$main_lang = isset($data['default_language']) ? $data['default_language'] : $main_lang;

if($glang == $main_lang) {
    $page_url = preg_replace('/^[\/]+/', '/', $page_url);

    header('Location: ' . $page_url, true, 301);
    exit;
}

$page_url = $server.'.tdn.gtranslate.net' . $page_url;

$protocol = ((isset($_SERVER['HTTPS']) and ($_SERVER['HTTPS'] == 'on' or $_SERVER['HTTPS'] == 1)) or (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) and  $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https' : 'http';
$page_url = $protocol . '://' . $page_url;

if(!in_array(strtolower($glang), array('en','ar','bg','zh-cn','zh-tw','hr','cs','da','nl','fi','fr','de','el','hi','it','ja','ko','no','pl','pt','ro','ru','es','sv','ca','tl','iw','id','lv','lt','sr','sk','sl','uk','vi','sq','et','gl','hu','mt','th','tr','fa','af','ms','sw','ga','cy','be','is','mk','yi','hy','az','eu','ka','ht','ur','bn','bs','ceb','eo','gu','ha','hmn','ig','jw','kn','km','lo','la','mi','mr','mn','ne','pa','so','ta','te','yo','zu','my','ny','kk','mg','ml','si','st','su','tg','uz','am','co','haw','ku','ky','lb','ps','sm','gd','sn','sd','fy','xh')))
    exit;

if(!function_exists("getallheaders")) {
  //Adapted from http://www.php.net/manual/en/function.getallheaders.php#99814
  function getallheaders() {
    $result = array();
    foreach($_SERVER as $key => $value) {
      if (substr($key, 0, 5) == "HTTP_") {
        $key = str_replace(" ", "-", ucwords(strtolower(str_replace("_", " ", substr($key, 5)))));
        $result[$key] = $value;
      } else if ($key == "CONTENT_TYPE") {
        $result["Content-Type"] = $value;
      }
    }
    return $result;
  }
}

$request_headers = getallheaders();

if(isset($request_headers['X-GT-Lang']) or isset($request_headers['X-Gt-Lang']) or isset($request_headers['x-gt-lang'])) {
    echo 'Please remove DNS cname records for GTranslate!';
    exit;
}

$host = $glang . '.' . preg_replace('/^www\./', '', $_SERVER['HTTP_HOST']);
$request_headers['Host'] = $host;
if(isset($request_headers['HOST'])) unset($request_headers['HOST']);
if(isset($request_headers['host'])) unset($request_headers['host']);

if(!function_exists('zlib_decode'))
    $request_headers['Accept-Encoding'] = '';
else
    $request_headers['Accept-Encoding'] = 'gzip';

if(isset($request_headers['accept-encoding'])) unset($request_headers['accept-encoding']);

if(isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
    $request_headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    if(isset($request_headers['authorization'])) unset($request_headers['authorization']);
}
//print_r($request_headers);
//exit;

if(isset($request_headers['content-type'])) {
    $request_headers['Content-Type'] = $request_headers['content-type'];
    unset($request_headers['content-type']);
}

if(isset($request_headers['Content-Type']) and strpos($request_headers['Content-Type'], 'multipart/form-data;') !== false) {
    $is_multipart = true;
    $request_headers['Content-Length'] = '';

    if(isset($request_headers['content-length']))
        unset($request_headers['content-length']);
}

$headers = array();
foreach($request_headers as $key => $val) {
    // remove cloudflare CF headers: CF-IPCountry, CF-Ray, etc...
    if(preg_match('/^CF-/i', $key))
        continue;
    else
        $headers[] = $key . ': ' . $val;
}

// add real visitor IP header
if(isset($_SERVER['HTTP_CLIENT_IP']) and !empty($_SERVER['HTTP_CLIENT_IP']))
    $viewer_ip_address = $_SERVER['HTTP_CLIENT_IP'];
if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) and !empty($_SERVER['HTTP_CF_CONNECTING_IP']))
    $viewer_ip_address = $_SERVER['HTTP_CF_CONNECTING_IP'];
if(isset($_SERVER['HTTP_X_SUCURI_CLIENTIP']) and !empty($_SERVER['HTTP_X_SUCURI_CLIENTIP']))
    $viewer_ip_address = $_SERVER['HTTP_X_SUCURI_CLIENTIP'];
if(!isset($viewer_ip_address))
    $viewer_ip_address = $_SERVER['REMOTE_ADDR'];

$headers[] = 'X-GT-Viewer-IP: ' . $viewer_ip_address;

// add X-Forwarded-For
if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
    $headers[] = 'X-GT-Forwarded-For: ' . $_SERVER['HTTP_X_FORWARDED_FOR'];

//print_r($headers);
//exit;

if(!function_exists('curl_init')) {
    http_response_code(500);
    echo 'PHP Curl library is required';
    exit;
}

// phpcs:disable WordPress.WP.AlternativeFunctions.curl_curl_init, WordPress.WP.AlternativeFunctions.curl_curl_setopt, WordPress.WP.AlternativeFunctions.curl_curl_exec, WordPress.WP.AlternativeFunctions.curl_curl_getinfo, WordPress.WP.AlternativeFunctions.curl_curl_close, WordPress.WP.AlternativeFunctions.curl_curl_errno, WordPress.WP.AlternativeFunctions.file_system_operations_fopen -- Direct cURL usage is required here because we do not include WP Core
// proxy request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $page_url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
if(defined('CURL_IPRESOLVE_V4')) curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__).'/cacert.pem');
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);

switch($_SERVER['REQUEST_METHOD']) {
    case 'POST': {
        curl_setopt($ch, CURLOPT_POST, true);
        if(isset($is_multipart)) {
            $multipart_boundary = substr($request_headers['Content-Type'], strpos($request_headers['Content-Type'], 'boundary=') + 9);

            $new_post = array();
            http_build_query_for_curl_multipart($_POST, $new_post, null, $multipart_boundary);

            // add files
            if(count($_FILES) > 0)
                http_build_files_for_curl_multipart($_FILES, $new_post, null, $multipart_boundary);

            // add final boundary
            $new_post[] = "--$multipart_boundary--";
            $new_post[] = "";

            $new_post = implode("\r\n", $new_post);

            $headers[] = 'Content-Length: ' . strlen($new_post);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $new_post);
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
        }
    }; break;

    case 'PUT': {
        curl_setopt($ch, CURLOPT_PUT, true);
        curl_setopt($ch, CURLOPT_INFILE, fopen('php://input', 'r'));
    }; break;
}

// Debug
if($debug) {
    $fh = fopen(dirname(__FILE__).'/debug.txt', 'a');
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_STDERR, $fh);
}

$response = curl_exec($ch);
$curl_errno = curl_errno($ch);
$response_info = curl_getinfo($ch);
curl_close($ch);
// phpcs:enable WordPress.WP.AlternativeFunctions.curl_curl_init, WordPress.WP.AlternativeFunctions.curl_curl_setopt, WordPress.WP.AlternativeFunctions.curl_curl_exec, WordPress.WP.AlternativeFunctions.curl_curl_getinfo, WordPress.WP.AlternativeFunctions.curl_curl_close, WordPress.WP.AlternativeFunctions.curl_curl_errno, WordPress.WP.AlternativeFunctions.file_system_operations_fopen

//print_r($response_info);

if($response === false or $curl_errno !== 0) {
    if($curl_errno === CURLE_OPERATION_TIMEDOUT) {
        http_response_code(504);
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache, no-store');
        if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'HEAD')
            echo '<h1>Gateway Timeout:</h1><p>The translation server did not respond in time.</p>';
    } else {
        http_response_code(502);
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache, no-store');
        if(!isset($_SERVER['REQUEST_METHOD']) or $_SERVER['REQUEST_METHOD'] !== 'HEAD')
            echo '<h1>Bad Gateway:</h1><p>Unable to retrieve a complete response from the translation server.</p>';
    }

    exit;
}

$header_size = $response_info['header_size'];
$header = substr($response, 0, $header_size);
$html = substr($response, $header_size);

if(function_exists('zlib_decode')) {
    $return_gz = false;
    $html_gunzip = @zlib_decode($html);

    if($html_gunzip !== false) {
        $html = $html_gunzip;
        unset($html_gunzip);

        if(strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip') !== false) {
            $return_gz = true;
            header('Content-Encoding: gzip');
            header('Vary: Accept-Encoding');
        }
    }
}

$response_headers = explode(PHP_EOL, $header);
//print_r($response_headers);
$headers_sent = '';
foreach($response_headers as $header) {
    if(!empty(trim($header)) and !preg_match('/Content\-Length:|Transfer\-Encoding:|Content\-Encoding:|Link:/i', $header)) {

        if(preg_match('/^(Location|Refresh):/i', $header)) {
            $header = str_ireplace($host, $_SERVER['HTTP_HOST'] . '/' . $glang, $header);
            $header = str_ireplace('Location: /', 'Location: /' . $glang . '/', $header);
            $header = str_replace('/' . $glang . '/' . $glang . '/', '/' . $glang . '/', $header);
        }

        // woocommerce cookie path fix
        if(preg_match('/^Set-Cookie:/i', $header) and strpos($header, 'woocommerce') !== false) {
            $header = preg_replace('/path=\/.*\/;/', 'path=/;', $header);
        }

        $headers_sent .= $header;
        header($header, false);
    }
}
//echo $headers_sent;

// TODO: modify URLs
$html = str_ireplace($host, $_SERVER['HTTP_HOST'] . '/' . $glang, $html);
$html = str_ireplace('href="/', 'href="/' . $glang . '/', $html);
$html = preg_replace('/href=\"\/' . $glang . '\/(af|sq|am|ar|hy|az|eu|be|bn|bs|bg|ca|ceb|ny|zh-CN|zh-TW|co|hr|cs|da|nl|en|eo|et|tl|fi|fr|fy|gl|ka|de|el|gu|ht|ha|haw|iw|hi|hmn|hu|is|ig|id|ga|it|ja|jw|kn|kk|km|ko|ku|ky|lo|la|lv|lt|lb|mk|mg|ms|ml|mt|mi|mr|mn|my|ne|no|ps|fa|pl|pt|pa|ro|ru|sm|gd|sr|st|sn|sd|si|sk|sl|so|es|su|sw|sv|tg|ta|te|th|tr|uk|ur|uz|vi|cy|xh|yi|yo|zu)\//i', 'href="/$1/', $html); // fix double language code
$html = str_ireplace('href="/' . $glang . '//', 'href="//', $html);
$html = str_ireplace('action="/', 'action="/' . $glang . '/', $html);
$html = str_ireplace('action=\'/', 'action=\'/' . $glang . '/', $html);
$html = str_ireplace('action="/' . $glang . '//', 'action="//', $html);
$html = str_ireplace('action=\'/' . $glang . '//', 'action=\'//', $html);
$html = str_ireplace('action="//' . $_SERVER['HTTP_HOST'], 'action="//' . $_SERVER['HTTP_HOST'] . '/' . $glang, $html);
$html = str_ireplace('action=\'//' . $_SERVER['HTTP_HOST'], 'action=\'//' . $_SERVER['HTTP_HOST'] . '/' . $glang, $html);

// woocommerce specific changes
$html = str_ireplace(
    array('ajax_url":"\\/',              '"checkout_url":"\\/'              ),
    array('ajax_url":"\\/'.$glang.'\\/', '"checkout_url":"\\/'.$glang.'\\/' ),
    $html
);

if(isset($_GET['language_edit'])) {
    $html = str_replace('/tdn-static/', $protocol . '://tdns.gtranslate.net/tdn-static/', $html);
    $html = str_replace('/tdn-bin/', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/' . $glang . '/tdn-bin/', $html);
}

// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- Raw output is required here because this response is emitted as part of the reverse proxy system
if(function_exists('gzencode') and isset($return_gz) and $return_gz and zlib_get_coding_type() == false)
    echo gzencode($html);
else
    echo $html;
// phpcs:enable WordPress.Security.EscapeOutput.OutputNotEscaped

function http_build_query_for_curl_multipart($arrays, &$new, $prefix, $multipart_boundary) {
    if(is_object($arrays))
        $arrays = get_object_vars($arrays);

    foreach($arrays as $key => $value) {
        $k = isset($prefix) ? $prefix . '[' . str_replace(array("\0", "\"", "\r", "\n"), '_', $key) . ']' : str_replace(array("\0", "\"", "\r", "\n"), '_', $key);
        if(is_array($value) or is_object($value)) {
            http_build_query_for_curl_multipart($value, $new, $k, $multipart_boundary);
        } else {
            $new[$k] = implode("\r\n", array(
                "--$multipart_boundary",
                "Content-Disposition: form-data; name=\"$k\"",
                '',
                filter_var($value),
            ));
        }
    }
}

function http_build_files_for_curl_multipart($arrays, &$new, $prefix, $multipart_boundary) {
    if(is_object($arrays))
        $arrays = get_object_vars($arrays);

    foreach($arrays as $key => $value) {
        $k = isset($prefix) ? $prefix . '[' . str_replace(array("\0", "\"", "\r", "\n"), '_', $key) . ']' : str_replace(array("\0", "\"", "\r", "\n"), '_', $key);
        if(isset($value['name'], $value['tmp_name'], $value['error']) and (is_array($value['name']) or is_object($value['name']))) {
            // multiple files, one level array case, todo: multidimensional case e.g. documents[page][front] documents[page][back]
            foreach($value['name'] as $i => $name) {
                if(empty($value['tmp_name'][$i]) or $value['error'][$i] !== UPLOAD_ERR_OK or !is_uploaded_file($value['tmp_name'][$i]))
                    continue;

                $v = str_replace(array("\0", "\"", "\r", "\n"), '_', $name);
                $data = file_get_contents($value['tmp_name'][$i]);
                $kk = $k.'['.$i.']';
                $new[$kk] = implode("\r\n", array(
                    "--$multipart_boundary",
                    "Content-Disposition: form-data; name=\"{$kk}\"; filename=\"{$v}\"",
                    'Content-Type: application/octet-stream',
                    '',
                    $data,
                ));
            }
        } elseif(isset($value['name'], $value['tmp_name'], $value['error'])) {
            if(empty($value['tmp_name']) or $value['error'] !== UPLOAD_ERR_OK or !is_uploaded_file($value['tmp_name']))
                continue;

            $v = str_replace(array("\0", "\"", "\r", "\n"), '_', $value['name']);
            $data = file_get_contents($value['tmp_name']);
            $new[$k] = implode("\r\n", array(
                "--$multipart_boundary",
                "Content-Disposition: form-data; name=\"{$k}\"; filename=\"{$v}\"",
                'Content-Type: application/octet-stream',
                '',
                $data,
            ));
        }
    }
}