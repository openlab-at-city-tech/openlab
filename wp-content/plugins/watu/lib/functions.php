<?php
// a bunch of helper functions
function watu_define_newline() {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$unewline = "\r\n";
	if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'win')) {
	   $unewline = "\r\n";
	} else if (strstr(strtolower($_SERVER["HTTP_USER_AGENT"]), 'mac')) {
	   $unewline = "\r";
	} else {
	   $unewline = "\n";
	}
	return $unewline;
}

function watu_get_mime_type()  {
	// credit to http://yoast.com/wordpress/users-to-csv/
	$USER_BROWSER_AGENT="";

			if (preg_match('/OPERA(\/| )([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OPERA';
			} else if (preg_match('/MSIE ([0-9].[0-9]{1,2})/',strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='IE';
			} else if (preg_match('/OMNIWEB\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='OMNIWEB';
			} else if (preg_match('/MOZILLA\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
				$USER_BROWSER_AGENT='MOZILLA';
			} else if (preg_match('/KONQUEROR\/([0-9].[0-9]{1,2})/', strtoupper($_SERVER["HTTP_USER_AGENT"]), $log_version)) {
		    	$USER_BROWSER_AGENT='KONQUEROR';
			} else {
		    	$USER_BROWSER_AGENT='OTHER';
			}

	$mime_type = ($USER_BROWSER_AGENT == 'IE' || $USER_BROWSER_AGENT == 'OPERA')
				? 'application/octetstream'
				: 'application/octet-stream';
	return $mime_type;
}

function watu_redirect($url) {
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}

// escapes user input to be usable in preg_replace & other preg functions
function watu_preg_escape($input) {
	return str_replace(array('^', '.', '|', '(', ')', '[', ']', '*', '+', '?', '{', '}', '$', '/'), 
		array('\^', '\.', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\$', '\/' ), $input);
}

// notify admin / user about taken quiz
function watu_notify($exam, $uid, $output, $who = 'admin') {
	global $user_email;
	
	// $exam->user_name is set in controllers/submit_exam.php. In case this function is called elsehwere, avoid php notice:
  if(empty($exam->user_name)) $exam->user_name = '';
	
	if(empty($uid) and !empty($_POST['watu_taker_email'])) {			
			$user_email = $_POST['watu_taker_email'];
	}
	
	$admin_email = watu_admin_email();
	if(strstr($admin_email, '<')) {
		$parts = explode('<', $admin_email);
		$admin_email = trim(str_replace('>', '', $parts[1]));
	}
	$admin_emails = array();
	
	if(!empty($exam->notify_email)) {
		$emails = explode(',', $exam->notify_email);
		foreach($emails as $email) $admin_emails[] = trim($email);
	}
	else $admin_emails[] = $admin_email;
	
	// different output for user / admin?
	if(strstr($output, '{{{split}}}')) {
		$parts = explode('{{{split}}}', $output);
		if($who == 'admin') $output = $parts[1];
		else $output = $parts[0];
	} 
	
	// replace styles in the snapshot with the images
	$correct_style=' style="padding-right:20px;background:url('.WATU_URL.'correct.png) no-repeat right top;" ';
	$wrong_style=' style="padding-right:20px;background:url('.WATU_URL.'wrong.png) no-repeat right top;" ';
	$user_answer_style = ' style="font-weight:bold;" ';	
	
	
	$output=str_replace('><!--WATUEMAILanswerWATUEMAIL--','',$output);
	$output=str_replace('><!--WATUEMAILanswer correct-answer user-answerWATUEMAIL--', $correct_style, $output);
	$output=str_replace('><!--WATUEMAILanswer correct-answeruser-answerWATUEMAIL--', $correct_style, $output);
	$output=str_replace('><!--WATUEMAILanswer correct-answerWATUEMAIL--',$correct_style,$output);
	$output=str_replace('><!--WATUEMAILanswer user-answerWATUEMAIL--', $wrong_style,$output);
	
	$output = str_replace("<li class='answer user-answer'>", "<li ".$user_answer_style.">", $output);
	$output = str_replace("<li class='answer user-answer correct-answer'>", "<li ".$user_answer_style.">", $output);	
	$output = str_replace("<li class='answer correct-answer user-answer'>", "<li ".$user_answer_style.">", $output);
	
	$headers  = 'MIME-Version: 1.0' . "\r\n";
	$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
	$headers .= 'From: '. watu_admin_email() . "\r\n";
	
	$admin_subject = sprintf(__('User results on "%s"', 'watu'), stripslashes($exam->name));	
	$user_subject = sprintf(__('Your results on "%s"', 'watu'), stripslashes($exam->name));	
	
	if(!empty($exam->email_subject)) {
				$email_subject = stripslashes($exam->email_subject);
		if(strstr($email_subject, '{{{split}}}')) {
			list($user_subject, $admin_subject) = explode('{{{split}}}', $email_subject);
		}
		else $user_subject = $admin_subject = $email_subject;
	}	
			
	if($who == 'admin') {
		
		$user_data = empty($user_email) ? __('Guest', 'watu') : $user_email;	
		
		$admin_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $admin_subject); 
		$admin_subject = str_replace('%%USER-NAME%%', $exam->user_name, $admin_subject);			
		
		$message = "Details of $user_data:<br><br>".$output;	
		// echo $message;
		//echo $admin_subject."<br>";
		foreach($admin_emails as $admin_email) {			
			wp_mail($admin_email, $admin_subject, $message, $headers);
		}
	}  
	else {
		// email user
		if(empty($user_email)) return true;
		
		$user_subject = str_replace('%%QUIZ_NAME%%', stripslashes($exam->name), $user_subject);	
		$user_subject = str_replace('%%USER-NAME%%', $exam->user_name, $user_subject);		
		
		$message = $output;	
		
		//echo $user_subject."<br>";
		wp_mail($user_email, $user_subject, $message, $headers);
	}
   // echo $message;   
} // end watu_notify

// replace user email in the quiz output / email output
// replace also %%USER-NAME%% var
function watu_replace_email($email, $output) {
	global $user_ID;
	if(strstr($output, '%%EMAIL%%')) {
		if(empty($email) and is_user_logged_in()) {
			$current_user = wp_get_current_user();
			$email = $current_user->user_email;
		}		
		
		$output = str_replace('%%EMAIL%%', $email, $output); 
	}
	
	if(strstr($output, '%%USER-NAME%%')) {
		if(!is_user_logged_in()) $user_name = __('Guest', 'watu');
		else {
			$user = get_userdata($user_ID);
			$user_name = $user->display_name;
		}
		
		$output = str_replace('%%USER-NAME%%', $user_name, $output); 
	}
	
	return $output;
}

// get admin email. This overwrites the global setting with the Watu setting.
function watu_admin_email() {
	$admin_email = get_option('watu_admin_email');
	if(empty($admin_email)) $admin_email = get_option('admin_email');
	
	return $admin_email;
}

// strip tags when user is not allowed to use unfiltered HTML
// keep some safe tags on
function watu_strip_tags($content) {
   if(!current_user_can('unfiltered_html')) {
		$content = strip_tags($content, '<b><i><em><u><a><p><br><div><span><hr><font><img>');
	}
	
	return $content;
}

// makes sure all values in array are ints. Typically used to sanitize POST data from multiple checkboxes
function watu_int_array($value) {
   if(empty($value) or !is_array($value)) return array();
   $value = array_filter($value, 'is_numeric');
   return $value;
}

// returns clickable URL of a published quiz if found
function watu_exam_url($exam_id, $target="_blank") {
	global $wpdb;
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT name, ID FROM ".WATU_EXAMS." WHERE ID=%d", $exam_id));
	
	$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status='publish'
		AND post_content LIKE '%[watu ".$exam->ID."]%' ORDER BY ID DESC LIMIT 1");
		
	if(empty($post_id)) return stripslashes($exam->name);
	else return '<a href="'.get_permalink($post_id).'" target="'.$target.'">'.stripslashes($exam->name).'</a>';	 
}

// returns non-clickable URL of a published quiz if found
function watu_exam_url_raw($exam_id) {
	global $wpdb;
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT name, ID FROM ".WATU_EXAMS." WHERE ID=%d", $exam_id));
	
	$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status='publish'
		AND post_content LIKE '%[watu ".$exam->ID."]%' ORDER BY ID DESC LIMIT 1");
		
	if(empty($post_id)) return '';
	else return get_permalink($post_id);	 
}

// output responsive table CSS in admin pages (and not only)
function watu_resp_table_css($screen_width = 600, $print_out = true) {
	$output = '
/* Credits:
 This bit of code: Exis | exisweb.net/responsive-tables-in-wordpress
 Original idea: Dudley Storey | codepen.io/dudleystorey/pen/Geprd */
  
@media screen and (max-width: '.$screen_width.'px) {
    table.watu-table {width:100%;}
    table.watu-table thead {display: none;}
    table.watu-table tr:nth-of-type(2n) {background-color: inherit;}
    table.watu-table tr td:first-child {background: #f0f0f0; font-weight:bold;font-size:1.3em;}
    table.watu-table tbody td {display: block;  text-align:center;}
    table.watu-table tbody td:before { 
        content: attr(data-th); 
        display: block;
        text-align:center;  
    }
}';

	if($print_out) echo $output;	
	else return $output;
} // end bftpro_resp_table_css()

function watu_resp_table_js($print_out = true) {
	$output = '
/* Credits:
This bit of code: Exis | exisweb.net/responsive-tables-in-wordpress
Original idea: Dudley Storey | codepen.io/dudleystorey/pen/Geprd */
  
var headertext = [];
var headers = document.querySelectorAll("thead");
var tablebody = document.querySelectorAll("tbody");

for (var i = 0; i < headers.length; i++) {
	headertext[i]=[];
	for (var j = 0, headrow; headrow = headers[i].rows[0].cells[j]; j++) {
	  var current = headrow;
	  headertext[i].push(current.textContent);
	  }
} 

for (var h = 0, tbody; tbody = tablebody[h]; h++) {
	for (var i = 0, row; row = tbody.rows[i]; i++) {
	  for (var j = 0, col; col = row.cells[j]; j++) {
	    col.setAttribute("data-th", headertext[h][j]);
	  } 
	}
}';
	if($print_out) echo $output;
	else return $output;
} // end bftpro_resp_table_js

// credit to normadize at http://php.net/manual/en/function.str-getcsv.php
function watu_parse_csv ($csv_string, $delimiter = ",", $skip_empty_lines = true, $trim_fields = true) {	
    $enc = preg_replace('/(?<!")""/', '!!Q!!', $csv_string);
    $enc = preg_replace_callback(
        '/"(.*?)"/s',
        'watu_parse_csv_field',
        $enc
    );
    $lines = preg_split($skip_empty_lines ? ($trim_fields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);
    return array_map(
        'watu_parse_csv_line',
        $lines
    );
}

function watu_parse_csv_field($field) {
   return urlencode(utf8_encode($field[1]));
}

function watu_parse_csv_line($line) {	
	$delimiter=$_POST['delimiter'];
	if($delimiter=="tab") $delimiter="\t";
	
	// convert encoding?
	if(!mb_detect_encoding($line, 'UTF-8', true)) $line = mb_convert_encoding($line, "UTF-8");
	
   $fields = true ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);
   return array_map(
       'watu_urlencode_csv_field',
       $fields
   );
}

function watu_urlencode_csv_field($field) {
 	 return str_replace('!!Q!!', '"', urldecode($field));
 }