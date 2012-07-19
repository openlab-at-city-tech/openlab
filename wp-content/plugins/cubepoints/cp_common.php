<?php
/**
 * CubePoints Common Functions
 */
 
 
/** Prints HTML for donate link */
function cp_donate_html(){
	echo '<div style="text-align:center;"><a href="http://cubepoints.com/donate/" style="border:none;text-decoration:none;background:url(\''.CP_PATH.'assets/donate.png\');display:inline-block;width:94px;height:31px;line-height:30px;color:#555;">Donate</a></div><br />';
}

/** Get difference in time */
function cp_relativeTime($timestamp){
	$difference = time() - $timestamp;
	$periods = array(__('sec','cp'), __('min','cp'), __('hour','cp'), __('day','cp'), __('week','cp'), __('month','cp'), __('year','cp'), __('decade','cp'));
	$lengths = array("60","60","24","7","4.35","12","10");
	if ($difference >= 0) { // this was in the past
		$ending = __('ago','cp');
	} else { // this was in the future
		$difference = -$difference;
		$ending = __('to go','cp');
	}		
	for($j = 0; $difference >= $lengths[$j]; $j++)
		$difference /= $lengths[$j];
	$difference = round($difference);
	if($difference != 1) $periods[$j].= 's';
	$text = "$difference $periods[$j] $ending";
	return $text;
}

/** Class for cURL */
class CURL {
    var $callback = false;

function setCallback($func_name) {
    $this->callback = $func_name;
}

function doRequest($method, $url, $vars) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    if ($method == 'POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
    }
    $data = curl_exec($ch);
    curl_close($ch);
    if ($data) {
        if ($this->callback)
        {
            $callback = $this->callback;
            $this->callback = false;
            return call_user_func($callback, $data);
        } else {
            return $data;
        }
    } else {
        return false;
    }
}

function get($url) {
    return $this->doRequest('GET', $url, 'NULL');
}

function post($url, $vars) {
    return $this->doRequest('POST', $url, $vars);
}
}
 
 ?>