<?php
// The Weaver WP_Filesystem interface to "duplicate" fopen, fwrite, etc.

// Route all file operations through weaver_f functions so might eventually use WP I/O instead of PHP directly

$weaver_wbuff = '';

function weaver_f_check_WP_Filesystem() {

    if (!weaver_f_file_access_available())
        return false;

    $save_dir = weaver_f_uploads_base_dir() . 'weaver-subthemes';
    if (!weaver_f_mkdir($save_dir)) {
	return false;
    }

    return true;
}

function weaver_f_file_access_fail($who = '') {
    static $weaver_f_file_access_fail_sent = false;
    if ($weaver_f_file_access_fail_sent) return;	// only show once...
    $weaver_f_file_access_fail_sent = true;

    $readme = get_template_directory_uri().'/help.html';
?>
	<div class="error">
        <strong style="color:#f00; line-height:150%;">*** Weaver File Access Error! ***</strong> <small style="padding-left:20px;">(But don't panic!)</small>
	<p>Weaver is unable to process a file access request. You may have incomplete or incorrect FTP credentials set in
	the Advanced Options:Admin page, or in your wp-config.php file. Otherwise, it is unusual to see this error. It often is generated
	after you move to a new host, or switch between the <em>Weaver File Access Plugin</em> and <em>FTP File Access</em>.</p>
	<p>You may have to change the directory permissions on your web hosting server. See <a href="<?php echo $readme; ?>#File_access_plugin" target="_blank">
Weaver File Access Plugin</a> Help for more information.</p>
	<?php echo "<p>Diagnostics: $who</p>\n"; ?>
	</div>
<?php
	return;
}


function weaver_f_file_access_available() {
    if (function_exists('weaver_fileio_plugin')) return true;
    if (!function_exists('WP_Filesystem')) return false;
    if ( ! weaver_f_WP_Filesystem() ) {			// can we make files?
	return false;
    }
    return true;
}

function weaver_f_open($fn, $how) {
    if (function_exists('weaver_fp_open')) return weaver_fp_open($fn, $how);
    global $weaver_wbuff;
    // 'php://output'
    if ($fn == 'php://output' || $fn == 'echo')
        return 'echo';

    if ($weaver_wbuff != '') {
	return weaver_f_fail('You can only open one file to write at a time!');
    }
    $weaver_wbuff = '';				// zap the buffer - we only allow one write open at a time.
    return $fn;
}

function weaver_pop_msg($msg) {
    echo "<script> alert('" . $msg . "'); </script>";
    // echo "<h1>*** $msg ***</h1>\n";
}

function weaver_log($msg, $data='') {
    // $file = weaver_f_uploads_base_dir() . 'weaver-subthemes/log-weaver.txt';
    // $f = fopen($file,'a');
    // fwrite($f, microtime() . ': ' . $msg .' : ' . $data . ":\n");
    // fclose($f);
}

function weaver_f_write($fn,$data) {
    if (function_exists('weaver_fp_write')) return weaver_fp_write($fn,$data);
    global $weaver_wbuff;
    if ($fn == 'echo') {
        echo $data;
	return true;
    }
    else {
	$weaver_wbuff .= $data;
	return true;
    }
}

function weaver_f_close($fn) {
    if (function_exists('weaver_fp_close')) return weaver_fp_close($fn);
    global $weaver_wbuff;
    if ($fn == 'php://output' || $fn == 'echo')
        return true;
    else {
	if ( ! weaver_f_WP_Filesystem() ) {			// can we make files?
	    return false;
	}
	global $wp_filesystem;
	if ( ! $wp_filesystem->put_contents( $fn, $weaver_wbuff, FS_CHMOD_FILE) ) {
	    weaver_f_wp_filesystem_error();
	    return false;
	}
	$weaver_wbuff = '';
	return true;				// Weaver is done with a file
    }
}

function weaver_f_delete($fn) {
    if (function_exists('weaver_filep_delete')) return weaver_filep_delete($fn);
    if ( ! weaver_f_WP_Filesystem() ) {
	return false;
    }
    global $wp_filesystem;
    $ret = $wp_filesystem->delete($fn);
    weaver_f_wp_filesystem_error();
    return $ret;
}

function weaver_f_is_writable($fn) {
    if (function_exists('weaver_pis_writable')) return weaver_pis_writable($fn);
    if ($fn == 'php://output' || $fn == 'echo')
        return true;
    if ( ! weaver_f_WP_Filesystem() ) {
	return false;
    }
    global $wp_filesystem;
    $ret = $wp_filesystem->is_writable($fn);
    weaver_f_wp_filesystem_error();
    return $ret;
   }

function weaver_f_touch($fn) {
    if (function_exists('weaver_fp_touch')) return weaver_fp_touch($fn);
    if ( ! weaver_f_WP_Filesystem() ) {			// can we make files?
	return false;
    }
    // WP_Filesystem doesn't have touch for ftp
    global $wp_filesystem;
    if ( ! $wp_filesystem->put_contents( $fn, ' ', FS_CHMOD_FILE) ) {
	    weaver_f_wp_filesystem_error();
    	    return false;
	}
    return true;

}

function weaver_f_mkdir($fn) {
    if (function_exists('weaver_fp_mkdir')) return weaver_fp_mkdir($fn);

    if ( ! weaver_f_WP_Filesystem() ) {			// can we make files?
	return false;
    }
    global $wp_filesystem;
    if ($wp_filesystem->is_dir($fn)) return true;
    $ret = $wp_filesystem->mkdir($fn);
    weaver_f_wp_filesystem_error();
    return $ret;
}

// functions for reading files

function weaver_f_exists($fn) {
    // this one must use native PHP version since it is used at theme runtime as well as admin
    return @file_exists($fn);
}

function weaver_f_get_contents($fn) {
    if (function_exists('weaver_filep_get_contents')) return weaver_filep_get_contents($fn);
    if ( ! weaver_f_WP_Filesystem() ) {
	return false;
    }
    global $wp_filesystem;
    $ret = $wp_filesystem->get_contents($fn);
    weaver_f_wp_filesystem_error();
    return $ret;

}

// =========================== encryption =================================

function weaver_encrypt($p_text) {
    require_once('wvr-b32.php');
    if (defined('AUTH_KEY')) {		// this should exist, and be secret and unique for each installation
	$key = AUTH_KEY;
    } else {
	$key = '2wsx3edc4rfv5tgb';	// need a fallback
    }
    if ($p_text == '') return '';
    $pos = strpos($p_text,'_wvr_:');
    if ($pos !== false && $pos == 0) {	// only encrypt once - get multiple calls to save value, so fix here
	return $p_text;
    }

    $len = strlen($p_text);		// encode length into string as well
    $text = $len . '|' . $p_text;

    /* Open module, and create IV */
    $td = mcrypt_module_open('des', '', 'ecb', '');
    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
    $iv_size = mcrypt_enc_get_iv_size($td);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    /* Initialize encryption handle */
    if (mcrypt_generic_init($td, $key, $iv) != -1) {
	$c_t = mcrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	return '_wvr_:' . weaver_base32_encode($c_t);	// need this so db save works
    } else {
	return '_wvr_:' . weaver_base32_encode($p_text);	// must do something!
    }
}

function weaver_decrypt($c_text) {
    require_once('wvr-b32.php');
        if (defined('AUTH_KEY')) {
	$key = AUTH_KEY;
    } else {
	$key = '2wsx3edc4rfv5tgb';
    }
    if ($c_text == '') return '';
    $pos = strpos($c_text,'_wvr_:');
    if ($pos !== false && $pos == 0) { // strip the leading code
	$text = weaver_base32_decode(substr($c_text,6));
    } else {
	return $c_text;	// who knows at this point...
    }
    /* Open module, and create IV */
    $td = mcrypt_module_open('des', '', 'ecb', '');
    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
    $iv_size = mcrypt_enc_get_iv_size($td);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

    /* Initialize encryption handle */
    if (mcrypt_generic_init($td, $key, $iv) != -1) {
	$p_t = mdecrypt_generic($td, $text);
        mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	list($len,$padded) = explode('|', $p_t,2);
	return substr($padded,0,$len);	// restore to original length
    } else {
	return $text;
    }
}

// =========================== helper functions ===========================

function weaver_f_content_dir() {
    // delivers appropraite path for using weaver_f_ functions. WP_CONTENT_DIR
    if (function_exists('weaver_fileio_plugin')) return trailingslashit(WP_CONTENT_DIR);
    global $wp_filesystem;
    return trailingslashit($wp_filesystem->wp_content_dir());
}

function weaver_f_plugins_dir() {
    // delivers appropraite path for using weaver_f_ functions. WP_PLUGIN_DIR
    if (function_exists('weaver_fileio_plugin')) return trailingslashit(WP_PLUGIN_DIR);
    global $wp_filesystem;
    return trailingslashit($wp_filesystem->wp_plugins_dir());
}

function weaver_f_themes_dir() {
    // delivers appropraite path for using weaver_f_ functions.
    return weaver_f_content_dir() . 'themes/';
}

function weaver_f_wp_lang_dir() {
    // delivers appropraite path for using weaver_f_ functions. WP_LANG_DIR
    if (function_exists('weaver_fileio_plugin')) return trailingslashit(WP_LANG_DIR);
    global $wp_filesystem;
    return trailingslashit($wp_filesystem->wp_lang_dir());
}

function weaver_f_uploads_base_dir() {
    // delivers appropraite path for using weaver_f_ functions.

    if (function_exists('weaver_fileio_plugin')) {
	$upload_dir = wp_upload_dir();
	return trailingslashit($upload_dir['basedir']);
    } else {
	// There is no $wp_filesystem->uploads_dir(), so we have to create it ourselves.
	// the 'upload_path' option will have the path relative to the abspath, when
	// it is not empty - this happens on multi-site sites, empty on normal sites.
	// We combine the two to create the real basedir path

	global $wp_filesystem;
	if (weaver_f_WP_Filesystem() && is_object($wp_filesystem)) {
	    $abspath = trailingslashit($wp_filesystem->abspath());
	    $upload_path = trim(get_option( 'upload_path' ));
	    if ( empty($upload_path) ) {
		return weaver_f_content_dir() . 'uploads/';
	    }
	    return trailingslashit($abspath . $upload_path);
	} else {	// try to fall back to something reasonable
	    $upload_dir = wp_upload_dir();
	    return trailingslashit($upload_dir['basedir']);
	}
    }
}

function weaver_f_uploads_base_url() {
    $wpdir = wp_upload_dir();		// get the upload directory
    return trailingslashit(trim($wpdir['baseurl']));
}

function weaver_f_WP_Filesystem() {
    // see if we can get some credentials

    static $creds = false;
    global $wp_filesystem;

    if ($creds != false && is_object($wp_filesystem)) {
	return true;
    }

    $method = '';	// change to ftpext to force ftp

    if ($method == '') {
	if (!function_exists('get_filesystem_method'))
	    return false;
	$type = get_filesystem_method(array());	// lets see if we have direct or ftpx
	if ($type != 'direct' && $type != 'ftpext') {
	    return false;
	}
	if ($type == 'direct') {
	    return WP_Filesystem() ? true : false;
	}
    } else
	$type = $method;

    if ($type == 'ftpext' && !(defined('FTP_HOST') && defined('FTP_USER') && defined('FTP_PASS'))) {
	// See if we can fill in the FTP credentials
	// Are they in wp-config.php?
	$host = weaver_getopt('ftp_hostname');		// we should have these filled in
	$user = weaver_getopt('ftp_username');
	$password = weaver_decrypt(weaver_getopt('ftp_password'));
	if (!($host && $user && $password)) {
	    return false;
	}
	$_POST['hostname'] = $host;
	$_POST['user'] = $user;
	$_POST['password'] = $password;
    }
    $url = "";
    ob_start(); /* use output buffering to hide the form */
    $creds = request_filesystem_credentials($url, $method);
    ob_end_clean();

    if (false === $creds) {
	// if we get here, then we don't have credentials yet,
	// This should only happen if we provided bad info
	return false;
    }

    // now we have some credentials, try to get the wp_filesystem running
    if ( ! WP_Filesystem($creds) ) {
	weaver_f_wp_filesystem_error();
	return false;
    }
    return true;	// we are good to go
}

function weaver_f_wp_filesystem_error() {
    global $wp_filesystem;
    if (is_object($wp_filesystem) && $wp_filesystem->errors->get_error_code() ) {
	foreach ( $wp_filesystem->errors->get_error_messages() as $message )
	    show_message($message);
	return;
    }
}

function weaver_f_fail($msg) {
    weaver_debug($msg);
    return false;
}
?>
