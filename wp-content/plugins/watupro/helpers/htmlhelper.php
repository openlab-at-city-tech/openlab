<?php 
// contains little procedural functions to output various HTML strings

// Adapted code from the MIT licensed QuickDD class
// created also by us
if(!function_exists('WTPquickDD_date')) {
	function WTPquickDD_date($name, $date=NULL, $format=NULL, $markup=NULL, $start_year=1900, $end_year=2100)
	{
	   // normalize params
	   if(empty($date) or !preg_match("/\d\d\d\d\-\d\d-\d\d/",$date)) $date=date("Y-m-d");
	    if(empty($format)) $format="YYYY-MM-DD";
	    if(empty($markup)) $markup=array();
	
	    $parts=explode("-",$date);
	    $html="";
	
	    // read the format
	    $format_parts=explode("-",$format);
	
	    $errors=array();
	    
	    // let's output
	    foreach($format_parts as $cnt=>$f)
	    {
	        if(preg_match("/[^YMD]/",$f)) 
	        { 
	            $errors[]="Unrecognized format part: '$f'. Skipped.";
	            continue;
	        }
	
	        // year
	        if(strstr($f,"Y"))
	        {
	            $extra_html="";
	            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
	            $html.=" <select name=\"".$name."year\"".$extra_html.">\n";
	
	            for($i=$start_year;$i<=$end_year;$i++)
	            {
	                $selected="";
	                if(!empty($parts[0]) and $parts[0]==$i) $selected=" selected";
	                
	                $val=$i;
	                // in case only two digits are passed we have to strip $val for displaying
	                // it's either 4 or 2, everything else is ignored
	                if(strlen($f)<=2) $val=substr($val,2);        
	                
	                $html.="<option value='$i'".$selected.">$val</option>\n";
	            }
	
	            $html.="</select>";    
	        }
	
	        // month
	        if(strstr($f,"M"))
	        {
	            $extra_html="";
	            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
	            $html.=" <select name=\"".$name."month\"".$extra_html.">\n";
	
	            for($i=1;$i<=12;$i++)
	            {
	                $selected="";
	                if(!empty($parts[1]) and intval($parts[1])==$i) $selected=" selected";
	                
	                $val=sprintf("%02d",$i);
	                    
	                $html.="<option value='$val'".$selected.">$val</option>\n";
	            }
	
	            $html.="</select>";    
	        }
	
	        // day - we simply display 1-31 here, no extra intelligence depending on month
	        if(strstr($f,"D"))
	        {
	            $extra_html="";
	            if(isset($markup[$cnt]) and !empty($markup[$cnt])) $extra_html=" ".$markup[$cnt];
	            $html.=" <select name=\"".$name."day\"".$extra_html.">\n";
	
	            for($i=1;$i<=31;$i++)
	            {
	                $selected="";
	                if(!empty($parts[2]) and intval($parts[2])==$i) $selected=" selected";
	                
	                if(strlen($f)>1) $val=sprintf("%02d",$i);
	                else $val=$i;
	                    
	                $html.="<option value='$val'".$selected.">$val</option>\n";
	            }
	
	            $html.="</select>";    
	        }
	    }
	
	    // that's it, return dropdowns:
	    return $html;
	}
}

// safe redirect
function watupro_redirect($url)
{
	echo "<meta http-equiv='refresh' content='0;url=$url' />"; 
	exit;
}


// displays session flash, errors etc, and clears them if required
function watupro_display_alerts() {
	global $error, $success;
	
	if(!empty($_GET['flash'])) {
		echo "<div class='watupro-alert'><p>".$_GET['flash']."</p></div>";		
	}
	
	if(!empty($error)) {
		echo '<div class="watupro-error"><p>'.$error.'</p></div>';
	}
	
	if(!empty($success)){
		echo '<div class="watupro-success"><p>'.$success.'</p></div>';
	}
}

// program-specific serialization of questions with answers
// serializes like this: qID:ansID,ansID,ansID|qID:|qID:ansID,ansID etc
function watupro_serialize_questions($questions) {
	$str = "";
	foreach($questions as $ct=>$question) {
		if($ct) $str.=" | ";
		$str.=$question->ID.":";
		if(is_array($question->q_answers)) {
			foreach($question->q_answers as $cnt=>$answer) {
				if($cnt) $str.=",";
				$str .= $answer->ID;
			}
		}
	}
	
	return $str;
}

// unserialization from the format given above in watupro_serialize_questions
function watupro_unserialize_questions($str) {
	global $wpdb;
	
	$questions = explode(" | ", $str);
	
	// extract all IDs to save queries
	$qids = $aids = array(0);
	
	foreach($questions as $question) {
		 $parts = explode(":", $question);
		 if(!empty($parts[0]) and $parts[0] != 'undefined') $qids[] = $parts[0];
		 $answers = explode(",", @$parts[1]);
		 foreach($answers as $answer) {
		 	   if(empty($answer)) continue;
		 		$aids[] = $answer;
		 	}	
	}
	
	$qids = watupro_int_array($qids);
	$aids = watupro_int_array($aids);
		
	// now select all questions and answers
	$all_questions = $wpdb->get_results("SELECT tQ.*, tC.name as cat, tC.description as cat_description,
		  tC.parent_id as cat_parent_id
        FROM ".WATUPRO_QUESTIONS." tQ LEFT JOIN ".WATUPRO_QCATS." tC ON tQ.cat_id = tC.ID
        WHERE tQ.ID IN (".implode(",", $qids).") AND tQ.is_inactive=0");
  if(!sizeof($all_questions)) return null;      
	$all_answers = $wpdb->get_results("SELECT * FROM ".WATUPRO_ANSWERS." WHERE ID IN (".implode(",", $aids).")");
	
	// now re-match them in the stored way
	$final_questions = array();
	foreach($questions as $question) {
		list($qid, $aids) = explode(":", $question);
		$aids = explode(",", $aids);
		
		foreach($all_questions as $q) {
			 if($q->ID == $qid) {
			 		$answers = array();
			 		foreach($aids as $aid) {
			 			foreach($all_answers as $answer) {
			 				if($answer->ID == $aid) $answers[] = $answer;
			 			} 
			 		}
			 		// add newly found answers to the matching question
			 		$q->q_answers = $answers;
			 		$final_questions[] = $q;
			 }	
		}
	}
	
	return $final_questions;
}	

// unserialize answer from "in progress" taking
function watupro_unserialize_answer($answer) {
	global $wpdb;
	
	$answer_arr = unserialize(stripslashes($answer->answer));
	
	$question = $wpdb->get_row($wpdb->prepare("SELECT * FROM ".WATUPRO_QUESTIONS." WHERE ID=%d", $answer->question_id));
	
	$answer_value = $answer_arr[0];
	list($points, $is_correct) = WTPQuestion :: calc_answer($question, $answer_value);
	
	$answer->answer = $answer_value;
	$answer->is_correct = $is_correct;
	$answer->points  = $points;
	
	return $answer;
}

// this function outputs basic email field when "email user..." is selected and the user is not logged in
function watupro_ask_for_email($exam) {
	$advanced_settings = unserialize(stripslashes($exam->advanced_settings));	
	
	// do this only if the exam description does not contain the {{{email-field tag
	if(strstr($exam->description, "{{{email-field") 
		or (!empty($advanced_settings['ask_for_contact_details']) and !empty($advanced_settings['contact_fields']['email']))) return '';		
	
	echo "<p><label>".__('Enter email to receive results:','watupro') .
		"</label> <input type='text' size='30' name='watupro_taker_email' id='watuproTakerEmail".$exam->ID."' class='watupro-autogenerated'></p>";
}

function watupro_mktime($datetime) {
	list($date, $time) = explode(" ", $datetime);
	list($year, $month, $day) = explode("-", $date);
	list($h, $m, $s) = explode(":", $time);
	$unixtime = mktime($h,$m,$s,$month,$day, $year);
	return $unixtime;
}

// displays category header or pagination divs when exam is 
// paginated by category
// used both in initial exam display and in submit_exam
function watupro_cat_header($exam, $qct, $ques, $mode = 'show', $current_page = 1) {
	if(($mode == 'show' and $exam->single_page != WATUPRO_PAGINATE_PAGE_PER_CATEGORY) or !$exam->group_by_cat) return '';	
	global $cat_count, $question_catids;
	if(empty($cat_count)) $cat_count = 1;
	$output = '';
	
	 $advanced_settings = unserialize(stripslashes($exam->advanced_settings));
	 
	 $rtl_class = empty($advanced_settings['is_rtl']) ? '' : 'watupro-rtl';	

	if(!in_array($ques->cat_id, $question_catids)) {
		 if($qct and $mode == 'show') $output .= "</div>"; // close previous category div	   	 	
   	 if($mode=='show') $output .= "<div id='catDiv".$cat_count."' style='display:".(($cat_count == $current_page) ? 'block' : 'none')."' class='watupro_catpage $rtl_class'>";
   	 // adding or !isset here for compatibility with the previous default setting
   	 if((!empty($advanced_settings['show_xofy']) or !isset($advanced_settings['show_xofy'])) and $mode == 'show') $output .= "<p>".sprintf(__('Page %d of %d', 'watupro'), $cat_count, $exam->num_cats)."</p>";   	
   	 $output .= "<h3>".stripslashes(apply_filters('watupro_qtranslate', $ques->cat))."</h3>";
   	 if(!empty($ques->cat_description)) $output .= "<div>".apply_filters('watupro_content', stripslashes($ques->cat_description))."</div>";
   	 $cat_count++;   	 
   }
	 
   return $output;
}

// similar to cat_header but used when pagination is "X questions per page"
function watupro_paginate_header($exam, $qct, $num_pages, $mode = 'show') {
	if($exam->single_page != WATUPRO_PAGINATE_CUSTOM_NUMBER) return '';	

	//echo "page count: {$exam->page_count}, qct: $qct custom per pahe {$exam->custom_per_page}";
	if(empty($exam->page_count)) $exam->page_count = 1;
	$output = '';
	
   $advanced_settings = unserialize(stripslashes($exam->advanced_settings));
   
	if($exam->single_page == WATUPRO_PAGINATE_CUSTOM_NUMBER and (($qct % $exam->custom_per_page) == 0) ) {
		 if($qct and $mode == 'show') $output .= "</div>"; // close previous pagination div	   	 	
   	 if($mode=='show') $output .= "<div id='catDiv".$exam->page_count."' style='display:".($qct?'none':'block')."' class='watupro_catpage'>";
   	 
   	 // adding or !isset here for compatibility with the previous default setting
   	 if(!empty($advanced_settings['show_xofy']) or !isset($advanced_settings['show_xofy'])) $output .= "<p>".sprintf(__('Page %d of %d', 'watupro'), $exam->page_count, $num_pages)."</p>";   	 
   	 $exam->page_count++;   	 
   }
	 
   return $output;
}

// get admin email. This overwrites the global setting with the watupro's setting.
function watupro_admin_email() {
	$admin_email = get_option('watupro_admin_email');
	if(empty($admin_email)) $admin_email = get_option('admin_email');
	
	return $admin_email;
}

// load stored page position when returning with $in_progress
// this function actually outputs javascript
function watupro_load_page($in_progress) {
	global $wpdb;
	
	if(empty($in_progress->ID)) return false;
	
	$current_page = $wpdb->get_var($wpdb->prepare("SELECT current_page FROM ".	WATUPRO_TAKEN_EXAMS." WHERE ID=%d", $in_progress->ID));
	if(empty($current_page) or $current_page == 1) return true;
	
	echo "WatuPRO.current_question=$current_page;\nWatuPRO.pagePreLoaded=true;\n";
}

// escapes user input to be usable in preg_replace
// seems redundant. Let's call preg_quote instead but keep this function here for a while
function watupro_preg_escape($input) {
	// dollar sign causes ugly problems in fill the gaps
	$input = preg_quote($input, '/');
	return $input;
	/*return str_replace(array('^', '.', '|', '(', ')', '[', ']', '*', '+', '?', '{', '}', '$', '/' ), 
		array('\^', '\.', '\|', '\(', '\)', '\[', '\]', '\*', '\+', '\?', '\{', '\}', '\$', '\/' ), $input);*/
}

// thanks to http://stackoverflow.com/questions/20025030/convert-all-types-of-smart-quotes-with-php
// and http://stackoverflow.com/questions/9027472/weird-dash-character-in-php
function watupro_convert_smart_quotes($string) { 
	$endash = html_entity_decode('&#x2013;', ENT_COMPAT, 'UTF-8');	

   $chr_map = array(
   // Windows codepage 1252
   "\xC2\x82" => "'", // U+0082 -> U+201A single low-9 quotation mark
   "\xC2\x84" => '"', // U+0084->U+201E double low-9 quotation mark
   "\xC2\x8B" => "'", // U+008B->U+2039 single left-pointing angle quotation mark
   "\xC2\x91" => "'", // U+0091->U+2018 left single quotation mark
   "\xC2\x92" => "'", // U+0092->U+2019 right single quotation mark
   "\xC2\x93" => '"', // U+0093->U+201C left double quotation mark
   "\xC2\x94" => '"', // U+0094->U+201D right double quotation mark
   "\xC2\x9B" => "'", // U+009B->U+203A single right-pointing angle quotation mark
   "\xC2\x96" => "-", // U+2013 en dash
   "\xC2\x97" => "-", // U+2014 em dash   
   $endash => '-',
   
   // Regular Unicode     // U+0022 quotation mark 
                          // U+0027 apostrophe     
   "\xC2\xAB"     => '"', // U+00AB left-pointing double angle quotation mark
   "\xC2\xBB"     => '"', // U+00BB right-pointing double angle quotation mark
   "\xE2\x80\x98" => "'", // U+2018 left single quotation mark
   "\xE2\x80\x99" => "'", // U+2019 right single quotation mark
   "\xE2\x80\x9A" => "'", // U+201A single low-9 quotation mark
   "\xE2\x80\x9B" => "'", // U+201B single high-reversed-9 quotation mark
   "\xE2\x80\x9C" => '"', // U+201C left double quotation mark
   "\xE2\x80\x9D" => '"', // U+201D right double quotation mark
   "\xE2\x80\x9E" => '"', // U+201E double low-9 quotation mark
   "\xE2\x80\x9F" => '"', // U+201F double high-reversed-9 quotation mark
   "\xE2\x80\xB9" => "'", // U+2039 single left-pointing angle quotation mark
   "\xE2\x80\xBA" => "'", // U+203A single right-pointing angle quotation mark
	);
	$chr = array_keys  ($chr_map); // but: for efficiency you should
	$rpl = array_values($chr_map); // pre-calculate these two arrays
	$string = str_replace($chr, $rpl, html_entity_decode($string, ENT_QUOTES, "UTF-8"));
	
	return $string;
}

// returns clickable URL of a published quiz if found
function watupro_exam_url($exam_id, $target="_blank") {
	global $wpdb;
	
	$exam = $wpdb->get_row($wpdb->prepare("SELECT name, ID FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
	
	$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status='publish'
		AND post_content LIKE '%[watupro ".$exam->ID."]%' ORDER BY ID DESC LIMIT 1");
		
	if(empty($post_id)) return stripslashes($exam->name);
	else return '<a href="'.get_permalink($post_id).'" target="'.$target.'">'.stripslashes($exam->name).'</a>';	 
}

// returns non-clickable URL of a published quiz if found
// @param $exam - when provided we'll avoid running a query
function watupro_exam_url_raw($exam_id, $exam = null) {
	global $wpdb;
	
	if(!$exam) $exam = $wpdb->get_row($wpdb->prepare("SELECT name, ID FROM ".WATUPRO_EXAMS." WHERE ID=%d", $exam_id));
	
	$post_id = $wpdb->get_var("SELECT ID FROM {$wpdb->posts} WHERE post_status='publish'
		AND post_content LIKE '%[watupro ".$exam->ID."]%' ORDER BY ID DESC LIMIT 1");
		
	if(empty($post_id)) return '';
	else return get_permalink($post_id);	 
}

// enqueue the localized and themed datepicker
function watupro_enqueue_datepicker() {
	$locale_url = get_option('watupro_locale_url');	
	wp_enqueue_script('jquery-ui-datepicker');	
	if(!empty($locale_url)) {
		// extract the locale
		$parts = explode("datepicker-", $locale_url);
		$sparts = explode(".js", $parts[1]);
		$locale = $sparts[0];
		wp_enqueue_script('jquery-ui-i18n-'.$locale, $locale_url, array('jquery-ui-datepicker'));
	}
	$css_url = get_option('watupro_datepicker_css');
	if(empty($css_url)) $css_url = '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css';
	wp_enqueue_style('jquery-style', $css_url);
}

/*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Tristan Jahier
 * thanks to http://tristan-jahier.fr/blog/2013/08/convertir-un-format-de-date-php-en-format-de-date-jqueryui-datepicker
 */
if(!function_exists('dateformat_PHP_to_jQueryUI')) { 
	function dateformat_PHP_to_jQueryUI($php_format) {
	    $SYMBOLS_MATCHING = array(
	        // Day
	        'd' => 'dd',
	        'D' => 'D',
	        'j' => 'd',
	        'l' => 'DD',
	        'N' => '',
	        'S' => '',
	        'w' => '',
	        'z' => 'o',
	        // Week
	        'W' => '',
	        // Month
	        'F' => 'MM',
	        'm' => 'mm',
	        'M' => 'M',
	        'n' => 'm',
	        't' => '',
	        // Year
	        'L' => '',
	        'o' => '',
	        'Y' => 'yy',
	        'y' => 'y',
	        // Time
	        'a' => '',
	        'A' => '',
	        'B' => '',
	        'g' => '',
	        'G' => '',
	        'h' => '',
	        'H' => '',
	        'i' => '',
	        's' => '',
	        'u' => ''
	    );
	    $jqueryui_format = "";
	    $escaping = false;
	    for($i = 0; $i < strlen($php_format); $i++)
	    {
	        $char = $php_format[$i];
	        if($char === '\\') // PHP date format escaping character
	        {
	            $i++;
	            if($escaping) $jqueryui_format .= $php_format[$i];
	            else $jqueryui_format .= '\'' . $php_format[$i];
	            $escaping = true;
	        }
	        else
	        {
	            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
	            if(isset($SYMBOLS_MATCHING[$char]))
	                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
	            else
	                $jqueryui_format .= $char;
	        }
	    }
	    return $jqueryui_format;
	}
}

// makes sure all values in array are numbers. Typically used to sanitize POST data from multiple checkboxes
function watupro_int_array($value) {
   if(empty($value) or !is_array($value)) return array();
   $value = array_filter($value, 'is_numeric');
   return $value;
}

// strip tags when user is not allowed to use unfiltered HTML
// keep some safe tags on
function watupro_strip_tags($content) {
   if(!current_user_can('unfiltered_html') and WATUPRO_UNFILTERED_HTML != 1) {
		$content = strip_tags($content, '<b><i><em><u><a><p><br><div><span><hr><font><img><strong>');
	}
	
	return $content;
}

// fix base 64 decoded strings in the DB once into rawurlencoded to avoid fake security warnings on some servers
// we'll do this just once on init() and then set a variable that the given data is fixed
function watupro_fix64($what) {
	global $wpdb;

	// make sure we don't get "headers already sent" on first activation
	if(strtolower($wpdb->get_var("SHOW TABLES LIKE '".WATUPRO_EXAMS."'")) != strtolower(WATUPRO_EXAMS)) return false;	
	
	if($what == 'contact_fields') {
		$exams = $wpdb->get_results("SELECT ID, advanced_settings FROM ".WATUPRO_EXAMS." ORDER BY ID");
		
		foreach($exams as $exam) {
			$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
			if(!empty($advanced_settings['contact_fields']['labels_encoded'])) {
				$advanced_settings['contact_fields']['email_label'] = stripslashes(base64_decode($advanced_settings['contact_fields']['email_label']));
		  	 	$advanced_settings['contact_fields']['name_label'] = stripslashes(base64_decode($advanced_settings['contact_fields']['name_label']));
		  	 	$advanced_settings['contact_fields']['phone_label'] = stripslashes(base64_decode($advanced_settings['contact_fields']['phone_label']));
		  	 	$advanced_settings['contact_fields']['company_label'] = stripslashes(base64_decode($advanced_settings['contact_fields']['company_label']));
		  	 	$advanced_settings['contact_fields']['field1_label'] = stripslashes(base64_decode(@$advanced_settings['contact_fields']['field1_label']));
		  	 	$advanced_settings['contact_fields']['field2_label'] = stripslashes(base64_decode(@$advanced_settings['contact_fields']['field2_label']));
		  	 	$advanced_settings['contact_fields']['intro_text'] = stripslashes(base64_decode($advanced_settings['contact_fields']['intro_text']));
		  	 	
		  	 	// now rawurlencode
		  	 	$advanced_settings['contact_fields']['email_label'] = rawurlencode($advanced_settings['contact_fields']['email_label']);
		  	 	$advanced_settings['contact_fields']['name_label'] = rawurlencode($advanced_settings['contact_fields']['name_label']);
		  	 	$advanced_settings['contact_fields']['phone_label'] = rawurlencode($advanced_settings['contact_fields']['phone_label']);
		  	 	$advanced_settings['contact_fields']['company_label'] = rawurlencode($advanced_settings['contact_fields']['company_label']);
		  	 	$advanced_settings['contact_fields']['field1_label'] = rawurlencode($advanced_settings['contact_fields']['field1_label']);
		  	 	$advanced_settings['contact_fields']['field2_label'] = rawurlencode($advanced_settings['contact_fields']['field2_label']);
		  	 	$advanced_settings['contact_fields']['intro_text'] = rawurlencode($advanced_settings['contact_fields']['intro_text']);
		  	 	
		  	 	$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET advanced_settings=%s WHERE ID=%d", serialize($advanced_settings), $exam->ID));
			}
		} // end foreach exam
		
		update_option('watupro_fix64_contact_fields', 1);
	} // end fixing contact fields

   // other advanced settings	- admin comments, premature text, sorted cats. Edit only if not empty
	if($what == 'advanced_settings') {		
		$exams = $wpdb->get_results("SELECT ID, advanced_settings FROM ".WATUPRO_EXAMS." ORDER BY ID");
		
		foreach($exams as $exam) {
			$advanced_settings = unserialize(stripslashes($exam->advanced_settings));
			
			if($advanced_settings['sorted_categories_encoded']) {
				$sorted_cats = $advanced_settings['sorted_categories'];				
				foreach($sorted_cats as $key => $val) {
					$key = base64_decode($key);
					$key = rawurlencode($key);
					$advanced_settings['sorted_categories'][$key] = $val;
				}
			} // end fixing sorted categories
			
			if(!empty($advanced_settings['admin_comments'])) {
				$advanced_settings['admin_comments'] = base64_decode($advanced_settings['admin_comments']);
				$advanced_settings['admin_comments'] = rawurlencode($advanced_settings['admin_comments']);
			}
			
			if(!empty($advanced_settings['premature_text'])) {
				$advanced_settings['premature_text'] = base64_decode($advanced_settings['premature_text']);
				$advanced_settings['premature_text'] = rawurlencode($advanced_settings['premature_text']);
			}
			
			$wpdb->query($wpdb->prepare("UPDATE ".WATUPRO_EXAMS." SET advanced_settings=%s WHERE ID=%d", serialize($advanced_settings), $exam->ID));
		} // end foreach exam
		
		update_option('watupro_fix64_advanced_settings', 1);
	} // end other advanced settings
}

// output responsive table CSS in admin pages (and not only)
function watupro_resp_table_css($screen_width = 600, $print_out = true) {
	$output = '
/* Credits:
 This bit of code: Exis | exisweb.net/responsive-tables-in-wordpress
 Original idea: Dudley Storey | codepen.io/dudleystorey/pen/Geprd */
  
@media screen and (max-width: '.$screen_width.'px) {
    table.watupro-table {width:100%;}
    table.watupro-table thead {display: none;}
    table.watupro-table tr:nth-of-type(2n) {background-color: inherit;}
    table.watupro-table tr td:first-child {background: #f0f0f0; font-weight:bold;font-size:1.3em;}
    table.watupro-table tbody td {display: block;  text-align:center;}
    table.watupro-table tbody td:before { 
        content: attr(data-th); 
        display: block;
        text-align:center;  
    }
}';

	if($print_out) echo $output;	
	else return $output;
} // end bftpro_resp_table_css()

function watupro_resp_table_js($print_out = true) {
	$output = '
/* Credits:
This bit of code: Exis | exisweb.net/responsive-tables-in-wordpress
Original idea: Dudley Storey | codepen.io/dudleystorey/pen/Geprd */
  
var headertext = [];
var headers = document.querySelectorAll("thead");
var tablebody = document.querySelectorAll("tbody");
console.log(headers);
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
	    if(typeof headertext[h] !== "undefined") col.setAttribute("data-th", headertext[h][j]);
	  } 
	}
}';
	if($print_out) echo $output;
	else return $output;
} // end bftpro_resp_table_js

// adds hidden text for screen readers showing correct/wrong answer
// we'll figure it out based on the CSS class of the answer
// add class for open end questions
function watupro_screen_reader_text($class, $open_end = false) {
	// if answer should not be revealed OR it's both not the user's answer and not correct, don't display anything
	// correct answers will be displayed even when not selected by user, unless we should keep unrevealed
	if(strstr($class, 'user-answer-unrevealed') or 
		(!strstr($class, 'user-answer') and !strstr($class, 'correct-answer'))) return '';
		
	if(strstr($class, 'correct-answer')) $text = __('correct', 'watupro');
	else $text = __('wrong', 'watupro');

	$open_end_class = $open_end ? ' watupro-open-end' : '';
	return '<span class="watupro-screen-reader'.$open_end_class.'">' . $text . '</span>';
}

// used to trim values in arrays with array_walk
function watupro_trim_value(&$value) { 
    $value = trim($value); 
}

// check if user is running SSL
// thanks to http://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps/33873274
function watupro_is_secure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}

// function to eventually encrypt user's IP address if GDPR features are enabled
function watupro_user_ip() {
	if(get_option('watupro_gdpr') == 1) {
		$part1 = substr($_SERVER['REMOTE_ADDR'], 0, 8);
		$part2 = substr($_SERVER['REMOTE_ADDR'], 8, strlen($_SERVER['REMOTE_ADDR']));
		$part2 = md5($part2);
		return $part1.$part2;
	}
	
	// no GDPR features
	return $_SERVER['REMOTE_ADDR'];
}

// function to use configurable text for some of the most common texts when available
function _wtpt($phrase) {
	$texts = get_option('watupro_texts'); // array of configurable texts
	if(empty($texts)) return $phrase;
	
	foreach($texts as $text) {
		list($left, $right) = explode('===', $text);
		if($left == $phrase and !empty($right)) return $right; 
	}
	
	// if no config found, return the original phrase
	return $phrase;
}

// get the design themes
function watupro_get_design_themes() {
	global $wpdb;	
	
	$themes = scandir(WATUPRO_PATH."/css/themes/");
	if(@file_exists(get_stylesheet_directory().'/watupro/themes')) {
		$custom_themes = scandir(get_stylesheet_directory().'/watupro/themes');
		$themes = array_merge($themes, $custom_themes);
	}
	$design_themes = array();
	foreach($themes as $theme) {
		if($theme == '.' or $theme == '..') continue;
		$parts = explode('.', $theme);
		array_pop($parts);
		$theme = implode('.', $parts);
		$design_themes[] = $theme;
	}
	
	// merge with custom themes from DB
	$themes = $wpdb->get_results("SELECT * FROM ".WATUPRO_THEMES." ORDER BY name");
	foreach($themes as $theme) {
		$design_themes[] = '[custom] '.stripslashes($theme->name);	
	}
	 
	return $design_themes;
}

// load FB JS SDK
function watupro_load_fb_sdk() {
	$appid = get_option('watuproshare_facebook_appid');	

	if(!empty($appid)) {
			echo  "<script>
	  window.fbAsyncInit = function() {
	    FB.init({
	      appId            : '".$appid."',
	      autoLogAppEvents : true,
	      xfbml            : true,
	      version          : 'v3.1'
	    });
	  };
	
	  (function(d, s, id){
	     var js, fjs = d.getElementsByTagName(s)[0];
	     if (d.getElementById(id)) {return;}
	     js = d.createElement(s); js.id = id;
	     js.src = \"https://connect.facebook.net/en_US/sdk.js\";
	     fjs.parentNode.insertBefore(js, fjs);
	   }(document, 'script', 'facebook-jssdk'));
	</script>";
	}
}

// define the order direction on table headers. If the site is already ordered on this column, the direction should be reversed
function watupro_order_dir($ob, $dir, $column_ob) {
	if($ob == $column_ob and $dir == 'asc') return 'desc';
	return 'asc';
} // end watupro_order_dir()

// short question summary or title for charts, drop-downs etc
function watupro_question_summary($question) {
	if(!empty($question->title)) return stripslashes($question->title);	
	
	$words = preg_split('/\s/', strip_tags(stripslashes($question->question)));
	$words = array_slice($words, 0, 10);
	$summary = implode(" ", $words).'...';	
	return $summary;
}