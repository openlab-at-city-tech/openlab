<?php

/**
 * Enhanced Viewer
 */

// disable if adddressed. $_GET['gpid'] below has SQL injection vulnerability and should
// be replaced if this file is used again.
exit;
 
// access wp functions externally
require_once('libs/lib-bootstrap.php');

// no access if parent plugin is disabled
if ( ! function_exists('gde_activate') ) {
	wp_die('<p>'.__('You do not have sufficient permissions to access this page.').'</p>');
}

if ( isset( $_GET['a'] ) && $_GET['a'] == 'gt') {
	// proxy xml content - must be done to avoid XSS failures (contains embedded link data and enables text selection)
	
	$code = gde_get_contents("https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING']);
	header('Content-type: application/xml');
	echo $code;
	
} elseif ( isset( $_GET['a'] ) && $_GET['a'] == 'bi' ) {
	// proxy image content - prevents "too many redirects" on many-paged docs
	/*
	$code = gde_get_contents( "https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING'] );
	header('Content-type: image/png');
	echo $code;
	*/
	header( "Location: https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING'] );
	
} elseif ( isset( $_GET['jsfile'] ) ) {
	// proxy javascript content - not doing anything here but Google changes return 404 if not proxied (??)
	$code = gde_get_contents("https://docs.google.com/" . $_GET['jsfile']);  
	header('Content-type: text/javascript');  
	echo $code;
	
} else {
	
	// trap language
	if ( isset( $_GET['hl'] ) ) {
		$lang = $_GET['hl'];
	} else {
		$lang = "";
	}
	
	if ( ! $mode = gde_get_mode( $_GET ) ) {
		wp_die( __('Invalid URL format', 'gde') );
	}
	
	// get profile
	if ( isset( $_GET['gpid'] ) ) {
		$gpid = mysql_real_escape_string( $_GET['gpid'] );
		if ( $profile = gde_get_profile( $gpid ) ) {
			$tb = $profile['tb_flags'];
			$vw = $profile['vw_flags'];
			$bg = $profile['vw_bgcolor'];
			$css = $profile['vw_css'];
		}
	}
	
	// autodetect mobile
	if ( $mode !== "mobile" && $profile['tb_mobile'] == "default" ) {
		if ( gde_mobile_check() ) {
			$mode = "mobile";
			$_SERVER['QUERY_STRING'] = str_replace( $mode, "mobile", $_SERVER['QUERY_STRING'] );
		}
	}
	
	// get the default page content
	$code = gde_get_contents( "https://docs.google.com/viewer?" . $_SERVER['QUERY_STRING'] );
	
	if ( ! $code ) {
		// blank page was returned
		gde_load_failure();
		exit;
	} else {
		// fix js path
		$search[] = "gview/resources_gview/client/js";
		//$replace[] = "https://docs.google.com/gview/resources_gview/client/js"; // use this if js not proxied
		$replace[] = "?jsfile=gview/resources_gview/client/js";	// use this instead to proxy the js
		
		// mode-specific styles
		if ( $mode == "chrome" ) {
			// remove branding elements
			$newstyles[] = '
				#docs-header { display: none; }
				#gview-menu { display: none; }
				#searchElements { display: none; }
				#buttonElements { float: right; padding-right: 5px; }
				#thumb-pane { display: none; }
				#content-pane { top: 36px !important; left: 0 !important; width: 100% !important; }
				.view { background-color: inherit; }
			';
		} elseif ( $mode == "mobile" ) {
			$newstyles[] = "#page-footer { display: none !important; }";
		} elseif ( $mode == "embedded" ) {
			
			// new toolbar button style
			$newstyles[] = '
				#controlbarControls { padding-top: 3px; margin-left: 5px; right: 5px; }
				#controlbarPageNumber { padding: 4px 5px 0 5px; }
				.toolbar-button-icon { padding-top: 1px; }
				.goog-toolbar-button-outer-box { opacity: 0.60; padding: 1px; }
				.goog-toolbar-button-outer-box:hover { opacity: 1.0; padding: 0; }
				.goog-toolbar-button-outer-box:hover { 
					background-color: #F8F8F8;
					border: 1px solid #CCC;
					background-image: -moz-linear-gradient(center top , #F8F8F8, #F1F1F1);
					box-shadow: 0 1px 1px rgba(0, 0, 0, 0.1) inset;
				}
				.goog-custom-button-disabled, .goog-custom-button-disabled:hover { opacity: 0.3; border: 0; padding: 1px; }
				#openFullScreenButtonIcon {
					background-image: url("img/sprite-fullscr.png");
					background-position: center;
				}
				.view { background-color: inherit; }
			';
			
			// hide open in new window
			if ( strstr( $tb, 'n' ) !== false || ( $profile['tb_fulluser'] == "yes" && ! is_user_logged_in() ) ) {
				$newstyles[] = "#controlbarOpenInViewerButton, #gdeControlbarOpenInViewerButton { display: none !important; }";
			}
		}
		
		// toolbar flag key
		/*
			h	=>	hide toolbar (all)
			n	=>	hide new window button (embedded only)
			p	=>	hide page numbers (embedded & mobile)
			r	=>	hide next/prev page (all)
			z	=>	hide zoom in/out (all)
		*/
		
		// hide toolbar
		if (strstr($tb, 'h')) {
			$newstyles[] = "#controlbar { display: none; }";
		}
		
		// hide page numbers
		if (strstr($tb, 'p')) {
			$newstyles[] = "#controlbarPageNumber { display: none !important; }";
		}
		
		// hide prev/next buttons
		if (strstr($tb, 'r')) {
			$newstyles[] = "#controlbarBackButton { display: none !important; }";
			$newstyles[] = "#controlbarForwardButton { display: none !important; }";
		}
		
		# hide zoom in/out
		if (strstr($tb, 'z')) {
			if ( $mode == "mobile" ) {
				$newstyles[] = ".mobile-button-zoom-out { display: none !important; }";
				$newstyles[] = ".mobile-button-zoom-in { display: none !important; }";
			} else {
				$newstyles[] = "#controlbarZoomOutButton { display: none !important; }";
				$newstyles[] = "#controlbarZoomInButton { display: none !important; }";
			}
		}
		
		// the below viewer styles are applied to all modes
		/*
			b	=>	remove page borders
			f	=>	simulate "full bleed" (minimal margins) (currently not exposed)
			t	=>	transparent background
			v	=>	reduce vertical margins (currently not exposed)
			x	=>	hide selection of text
		*/
		
		// "full bleed" (experimental) - low res and breaks zooming
		if (strstr($vw, 'f')) {
			$newstyles[] = "#page-pane { margin: 0 -8px !important; }";
			$newstyles[] = "#gview { padding-bottom: -8px !important; }";
			$newstyles[] = ".page-element { margin: 0 !important; padding: 0 !important; width: 100% !important; }";
			$newstyles[] = ".page-image { width: 99% !important; }";
			$newstyles[] = "#content-pane { overflow-x: hidden; }";
		}
		
		// reduce vertical margins
		if (strstr($vw, 'v')) {
			//$newstyles[] = "#content-pane>div { margin: 0 !important; padding: 0 !important; }";
			$newstyles[] = "#content-pane { text-align: left; }";
			$newstyles[] = ".page-element { padding: 0; }";
			//$newstyles[] = "#content-pane { background-color: transparent; }";
		}
		
		// hide selection of text
		if ( strstr( $vw, 'x' ) ) {
			$search[] = 'class="page-image';
			$replace[] = 'class="page-image noselect';
			$newstyles[] = ".rubberband { display: none; }";
			$newstyles[] = ".highlight-pane { display: none; }";
			$newstyles[] = ".page-image { cursor: default; }";
		}
		
		// remove page borders or change color
		if ( strstr( $vw, 'b' ) && $mode !== "chrome" ) {
			$newstyles[] = ".page-image { border: none; }";
		} elseif ( $profile['vw_pbcolor'] ) {
			// set page border color
			$newstyles[] = ".page-image { border: 1px solid " . $profile['vw_pbcolor'] . "; }";
		}
		
		// set viewer background color
		if ( strstr( $vw, 't' )) {
			$newstyles[] = "#content-pane { background-color: transparent; }";
		} elseif ( ! empty( $bg ) ) {
			$newstyles[] = "#content-pane { background-color: $bg; }";
		}
		
		if (count($newstyles) > 0) {
			// build new stylesheet
			$styleblock = array();
			$styleblock[] = "\n<!-- GDE STYLE INSERT -->";
			
			$styleblock[] = '<style type="text/css">';
			foreach ($newstyles as $ln) {
				$styleblock[] = "\t $ln";
			}
			$styleblock[] = "</style>";
			
			// insert new styles
			$styleblock = implode("\n", $styleblock)."\n</head>";
			$search[] = "</head>";
			$replace[] = $styleblock;
		}
		
		// override stylesheet
		if ( ! empty( $css ) ) {
			$cssblock = '<link rel="stylesheet" type="text/css" href="'.$css."\">\n</head>";
			$search[] = "</head>";
			$replace[] = $cssblock;
		}
		
		// override right-click behavior (if link is private)
		if ( $profile['link_block'] == "yes" ) {
			$search[] = "<body";
			$replace[] = '<body oncontextmenu="return false;"';
			$search[] = "</body>";
			$replace[] = '
			<script type="text/javascript">
				if (document.addEventListener) {
					document.addEventListener(\'contextmenu\', function(e) {
						e.preventDefault();
					}, false);
				} else {
					document.attachEvent(\'oncontextmenu\', function() {
						window.event.returnValue = false;
					});
				}
			</script>
			'."\n</body>";
		}
		
		// override new window behavior
		$src = "js/tb-newwin.php";
		if ( $mode == "embedded" && $profile['tb_fullscr'] !== "default" ) {
			if ( $profile['tb_fullwin'] == "same" ) {
				$arg[] = "a=fs";
			}
			//if ( $profile['tb_print'] == "yes" ) {
			//	$arg[] = "p=1";
			//}
			if ( isset( $arg ) && is_array( $arg ) ) {
				$src .= "?hl=$lang&" . implode("&", $arg);
			}
			$scriptblock = '<script type="text/javascript" src="' . $src . '"></script>';
			$search[] = "</body>";
			$replace[] = $scriptblock."\n</body>";
		} elseif ( $profile['tb_fullscr'] == "default" && $profile['tb_fullwin'] == "same" ) {
			$src .= "?a=fs&url=" . $_GET['url'] . "&hl=" . $lang;
			$scriptblock = '<script type="text/javascript" src="' . $src . '"></script>';
			$search[] = "</body>";
			$replace[] = $scriptblock."\n</body>";
		}
		
		// perform string replacements
		$code = str_replace($search, $replace, $code);
	}
	
	// disable caching of viewer if link is blocked or document cache is off
	if ( $profile['link_block'] == "yes" || strstr( urldecode( $_GET['url'] ), "?" ) ) {
		gde_block_caching();
	}
	
	// output page
	header('Content-type: text/html; charset=utf-8');
	echo $code;
}


/**
 * Fetch remote file source
 *
 * @since   2.5.0.1
 * @return  string Contents of remote file
 */
function gde_get_contents( $url ) {
	
	$opts = array(
		'user-agent'	=> 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
		'timeout'		=>	20,
		'ssl_verify'	=>	false
	);
	
	$response = wp_remote_get( $url, $opts );
	
	if ( is_wp_error( $response ) || wp_remote_retrieve_response_code( $response ) != 200 ) {
		gde_dx_log("Error retrieving document");
		return false;
	} else {
		$result = trim( $response['body'] );
		if ( empty( $result ) && function_exists('curl_version') ) {
			// document returned OK but has no contents
			// (this is rare but seems to happen in some server configs)
			gde_dx_log("Retrieve body empty - using cURL fallback");
			$result = trim( gde_curl_get_contents( $url ) );
			if ( empty( $result ) ) {
				return false;
			}
		} elseif ( empty( $result ) ) {
			gde_dx_log("Retrieve body empty - cURL fallback not available");
			return false;
		}
	}
	
	return $result;
}

/**
 * Fetch remote file source (cURL fallback)
 *
 * @since   1.7.0.0
 * @return  string Contents of remote file
 */
function gde_curl_get_contents( $url ) {
	$ch = curl_init();
	curl_setopt_array( $ch, array(
	    CURLOPT_URL            => $url, 
	    CURLOPT_RETURNTRANSFER => true,
	    CURLOPT_CONNECTTIMEOUT => 20,	// HTTP API is 5, overridden in gde_get_contents to match this
	    CURLOPT_SSL_VERIFYPEER => false
	) );
	$file_contents = curl_exec( $ch );
	curl_close( $ch ); 
	
	return $file_contents;
}

/**
 * Check for mobile browser
 *
 * @since   2.5.0.1
 * @return  bool Browser is detected as mobile, or not
 */
function gde_mobile_check() {
	//return true;	// test
	include_once("libs/lib-mobilecheck.php");
	
	if ( gde_is_mobile_browser() ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Get profile data
 *
 * @since   2.5.0.1
 * @return  array Array of specific profile data (bool false on failure)
 */
function gde_get_profile( $id ) {
	global $wpdb;
	$table = $wpdb->prefix . 'gde_profiles';
	
	$profile = $wpdb->get_results( "SELECT * FROM $table WHERE profile_id = $id", ARRAY_A );
	$profile = unserialize($profile[0]['profile_data']);
	
	if ( is_array($profile) ) {
		return $profile;
	} else {
		return false;
	}
}

/**
 * Get viewer mode
 *
 * @since   2.5.0.1
 * @return  string Which mode to grab content source from
 */
function gde_get_mode( $get ) {
	if ( isset( $get['embedded'] ) ) {
		return "embedded";
	} elseif ( isset( $get['mobile'] ) ) {
		return "mobile";
	} elseif ( isset( $get['chrome'] ) ) {
		//return "chrome";
		return "embedded";
	} else {
		return false;
	}
}

/**
 * Block caching of entire viewer frame (not just doc)
 *
 * @since   2.5.0.4
 * @return  void
 */
function gde_block_caching() {
    header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
}

/**
 * Error page when enhanced viewer not supported (empty document body)
 *
 * @since   2.5.0.3
 * @return  string HTML source for error to display
 */
function gde_load_failure() {
	// test stuff
	global $profile;
	
	$title = "GDE " . __('Error', 'gde') . ": ";
	$error = $title . __('Unable to retrieve document contents. Please try again or switch to Standard Viewer.', 'gde');
	
	gde_block_caching();
	header('Content-type: text/html; charset=utf-8');
	
	echo '
<html>
<head>
	<title>' . $title . '</title>
</head>
<body>
	<p>' . $error . '</p>
</body>
</html>';

}

?>