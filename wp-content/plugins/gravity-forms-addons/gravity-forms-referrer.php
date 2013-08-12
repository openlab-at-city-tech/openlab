<?php
if(!function_exists('gf_yst_get_query')) {
	function gf_yst_get_query($query) {
		if (strpos($query, "google.")) {
			$pattern = '/^.*\/search.*[\?&]q=(.*)$/';
		} else if (strpos($query, "bing.com")) {
			$pattern = '/^.*q=(.*)$/';
		} else if (strpos($query, "yahoo.")) {
			$pattern = '/^.*[\?&]p=(.*)$/';
		} else if (strpos($query, "ask.")) {
			$pattern = '/^.*[\?&]q=(.*)$/';
		} else {
			return false;
		}
		preg_match($pattern, $query, $matches);
		$querystr = substr($matches[1], 0, strpos($matches[1], '&'));
		return urldecode($querystr);
	}
}

if(!function_exists('gf_yst_referer_session')) {
	function gf_yst_referer_session() {
		$baseurl = get_bloginfo('url');
		if ( !isset($_SESSION) ) {
			session_start();
		}
		if ( !isset($_SESSION['gf_yst_pages']) || !is_array($_SESSION['gf_yst_pages']) ) {
			$_SESSION['gf_yst_pages'] = array();
		}
		if ( !isset($_SESSION['gf_yst_referer']) || !is_array($_SESSION['gf_yst_referer']) ) {
			$_SESSION['gf_yst_referer'] = array();
		}
		
		// With the ajax submission option, every time someone submitted a form, admin-ajax.php would show as a visited page.
		// This should prevent that.
		if(isset($_SERVER['HTTP_REFERER']) && preg_match('/admin\-ajax\.php/ism', $_SERVER['HTTP_REFERER'], $matches)) { return; }
		
		if (!isset($_SERVER['HTTP_REFERER']) || (strpos($_SERVER['HTTP_REFERER'], $baseurl) === false) && ! (in_array($_SERVER['HTTP_REFERER'], $_SESSION['gf_yst_referer'])) ) {
			if (! isset($_SERVER['HTTP_REFERER'])) {
				$_SESSION['gf_yst_referer'][] = "Type-in or bookmark";
			} else {
				$_SESSION['gf_yst_referer'][] = $_SERVER['HTTP_REFERER'];	
			}
		}
		if (end($_SESSION['gf_yst_pages']) != "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']) {
			$_SESSION['gf_yst_pages'][] = "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];	
		}
		return;
	}
}

if(!function_exists('gf_yst_store_referrer')) {
	function gf_yst_store_referrer($data) {
		$referrerinfo = '';
		$keywords = array();
		$i = 1;
		foreach ($_SESSION['gf_yst_referer'] as $referer) {
			$referrerinfo .= str_pad("Referer $i: ",20) . $referer. "\r\n";
			$keywords_used = gf_yst_get_query($referer);
			if ($keywords_used) {
				$keywords[] = $keywords_used;
			}
			$i++;
		}
		$referrerinfo .= "\r\n";
		
		$i = 1;
		foreach ($_SESSION['gf_yst_pages'] as $page) {
			$referrerinfo .= str_pad("Page visited $i: ",20) . $page. "\r\n";
			$i++;
		}
		$referrerinfo .= "\r\n";
		
		$i = 1;
		if (count($keywords) > 0) {
			foreach ($keywords as $keyword) {
				$referrerinfo .= str_pad("Keyword $i: ",20) . $keyword. "\r\n";
				$i++;
			}
		}
		$referrerinfo .= "\r\n";
		
		if (isset($data['notification']['message'])) {
			$data['notification']['message'] .= "\r\nReferrer Info:\r\n".$referrerinfo;
		}
		
		return $data;
	}
}

	add_filter('gform_pre_submission_filter','gf_yst_store_referrer');

	// Update the session data
	add_action('init', 'gf_yst_referer_session', 99);
?>